<?php

class BuyingGamePhase implements Phase
{

    public function advance(TacGamedata $gameData, DBManager $dbManager)
    {
        $servergamedata = $dbManager->getTacGamedata($gameData->forPlayer, $gameData->id);

        if (($gameData->rules->hasRuleName("asteroids") || $gameData->rules->hasRuleName("moons"))){        
            // Sort ships to prioritise placing larger terrain first 
            usort($servergamedata->ships, function($a, $b) {
                $getWeight = function($ship) {
                    if ($ship instanceof moonLarge) return 90;
                    if ($ship instanceof moonNew) return 80;
                    if ($ship instanceof moonSmallNew) return 70;
                    if ($ship instanceof asteroidThreeHex) return 60;
                    if ($ship instanceof asteroidTwoHex) return 50;
                    if ($ship instanceof asteroidLNew) return 40;
                    if ($ship instanceof asteroidMNew) return 30;
                    if ($ship instanceof asteroidSNew) return 20;
                    return 0;
                };
                
                return $getWeight($b) - $getWeight($a);
            });
        }

        // Identify participating teams and count ships per team
        $teamShips = [];
        foreach ($servergamedata->ships as $ship) {
            if ($ship->userid != -5) {
                $teamShips[$ship->team][] = $ship;
            }
        }
        
        $teams = array_keys($teamShips);
        sort($teams);
        $totalTeams = count($teams);
        
        $gamespace = $this->getGamespace($gameData);
        $width = $gamespace['width'];
        $height = $gamespace['height'];
        
        // Organize teams by Odd (Left) and Even (Right)
        $leftTeams = [];
        $rightTeams = [];

        foreach ($teams as $teamId) {
            if ($teamId % 2 != 0) {
                $leftTeams[] = $teamId;
            } else {
                $rightTeams[] = $teamId;
            }
        }

        // Calculate Team Centers
        $teamCenters = [];
        $teamHeadings = [];
        // Spacing between teams on the same side
        $teamVerticalSpacing = 20; 

        // LEFT TEAMS (Odd)
        $numLeft = count($leftTeams);
        foreach ($leftTeams as $i => $teamId) {
             // Center them vertically
             if ($numLeft > 1) {
                 $y = (($i) - ($numLeft - 1) / 2) * $teamVerticalSpacing;
             } else {
                 $y = 0;
             }
             $teamCenters[$teamId] = new OffsetCoordinate(-($width/2) - 5, $y);
             $teamHeadings[$teamId] = 0; // Face Right
        }

        // RIGHT TEAMS (Even)
        $numRight = count($rightTeams);
        foreach ($rightTeams as $i => $teamId) {
             if ($numRight > 1) {
                 $y = (($i) - ($numRight - 1) / 2) * $teamVerticalSpacing;
             } else {
                 $y = 0;
             }
             $teamCenters[$teamId] = new OffsetCoordinate(($width/2) + 5, $y);
             $teamHeadings[$teamId] = 3; // Face Left
        }

        foreach ($teams as $teamId) {
            $ships = $teamShips[$teamId];
            $center = $teamCenters[$teamId] ?? new OffsetCoordinate(0,0);
            $facing = $teamHeadings[$teamId] ?? 0;
            
            $shipCount = 0;
            $mineCount = 0;
            foreach ($ships as $ship) {
                $isMine = ($ship instanceof Mine);
                
                if ($isMine) {
                    $mineCount++;
                    $count = $mineCount;
                } else {
                    $shipCount++;
                    $count = $shipCount;
                }
                
                // Formations: Vertical Line relative to team center
                // Simple pattern: 0, 1, -1, 2, -2... along the vertical axis
                if ($count % 2 == 0) {
                    $offsetStep = $count / 2;
                } else {
                    $offsetStep = (($count - 1) / 2) * -1;
                }
                
                // Apply offset to Y axis (q, r+offset) for standard vertical stack
                // Mines are placed 2 hexes further behind on the x-axis
                $mineOffset = $isMine ? (($facing === 0) ? -2 : 2) : 0;
                $deployPos = new OffsetCoordinate($center->q + $mineOffset, $center->r + $offsetStep);

                $move = new MovementOrder(-1, "start", $deployPos, 0, 0, 5, $facing, $facing, true, 1, 0, 0);
                $ship->movement = array($move);
            }
        } 

        // Now let's see if we have to add any terrain.
        $moonPositions = [];
        $terrainOccupiedHexes = [];
        foreach ($servergamedata->ships as $ship) {
            if (($gameData->rules->hasRuleName("asteroids") || $gameData->rules->hasRuleName("moons")) && $ship->userid == -5) {
                // It's an asteroid or moon, so assign a unique random position.
                $deploymentZone = $this->getGamespace($gameData);
                
                if ($ship instanceof moonSmallNew || $ship instanceof moonNew || $ship instanceof moonLarge) {
                    $maxX = (int)(($deploymentZone['width'] / 2) - 6);
                    $maxY = (int)(($deploymentZone['height'] / 2) - 4);  
                } else {
                    $maxX = (int)(($deploymentZone['width'] / 2) - 2);
                    $maxY = (int)(($deploymentZone['height'] / 2) - 1);
                }    

                // Generate a unique random position
                $attempts = 0;
                $maxAttempts = 500;
                
                while (true) {
                    $attempts++;
                    // Safety break to prevent infinite loops (ajax timeout)
                    if ($attempts > $maxAttempts) {
                        // If we can't place it safely after many tries, just place it at a random spot 
                        // and ignore the "distance to other asteroids" rule, but still try to respect Moon distance if possible.
                        // Or just allow overlap as a fallback.
                        //$usedPositions["$x,$y"] = true; // Variable not previously used? kept commented out if wasn't there or local
                         // Register hexes anyway to prevent complete overlap if possible, but we stop checking collisions rigidly
                        if ($ship instanceof moonSmallNew || $ship instanceof moonNew || $ship instanceof moonLarge) {
                            $moonPositions[] = [$x, $y];
                        }
                        foreach ($currentUnitHexes as $hex) {
                            $terrainOccupiedHexes[] = $hex->q . "," . $hex->r;
                        }
                        $move = new MovementOrder(-1, "start", $center, 0, 0, 0, $h, $h, true, 1, 0, 0);
                        $ship->movement = array($move);
                        break;
                    }

                    $x = rand(-$maxX, $maxX);
                    $y = rand(-$maxY, $maxY);
                    $h = rand(0, 5); // Generate facing inside loop to validate specific footprint

                    $valid = true;
                    $currentUnitHexes = []; // To store hexes occupied by this unit
                    $center = new OffsetCoordinate($x, $y);
                    $currentUnitHexes[] = $center;

                    // 1. Calculate the hexes this unit would occupy
                    if (property_exists($ship, 'hexOffsets') && is_array($ship->hexOffsets) && count($ship->hexOffsets) > 0) {
                        foreach ($ship->hexOffsets as $offset) {
                            $currentUnitHexes[] = Mathlib::getRotatedHex($center, $offset, $h);
                        }
                    } elseif ($ship->Huge > 0) {
                        // Fallback for circular Huge terrain
                        $neighbours = Mathlib::getNeighbouringHexes($center, $ship->Huge);
                        foreach ($neighbours as $n) {
                            $currentUnitHexes[] = new OffsetCoordinate($n['q'], $n['r']);
                        }
                    }

                    // 2. Check collision with Moons
                    foreach ($moonPositions as $mPos) {
                        $moonCenter = new OffsetCoordinate($mPos[0], $mPos[1]);
                        // Moons need 7 hex buffer from other moons, Asteroids need 4 from moons
                        $limit = ($ship instanceof moonSmallNew || $ship instanceof moonNew || $ship instanceof moonLarge) ? 7 : 4;
                        
                        foreach ($currentUnitHexes as $myHex) {
                            if (Mathlib::getDistanceHex($myHex, $moonCenter) < $limit) {
                                $valid = false; 
                                break 2; // Break both loops
                            }
                        }
                    }
                    if (!$valid) continue; // Try next position

                    // 3. Check collision with other Terrain (Asteroids/Irregular)
                    // If we are over 50% of max attempts, relax the rule: allow 0 distance (adjacency), just no direct overlap
                    $minDist = ($attempts > ($maxAttempts / 2)) ? 0 : 1; 

                    if (!empty($terrainOccupiedHexes)) {
                        foreach ($currentUnitHexes as $myHex) {
                            foreach ($terrainOccupiedHexes as $occupiedHexStr) {
                                list($oq, $or) = explode(',', $occupiedHexStr);
                                $occupiedHex = new OffsetCoordinate((int)$oq, (int)$or);
                                
                                $dist = Mathlib::getDistanceHex($myHex, $occupiedHex);
                                if ($dist <= $minDist) { 
                                    $valid = false;
                                    break 2;
                                }
                            }
                        }
                    }
                    if (!$valid) continue;

                    // 4. Success! Register position and hexes
                    $usedPositions["$x,$y"] = true;
                    if ($ship instanceof moonSmallNew || $ship instanceof moonNew || $ship instanceof moonLarge) {
                        $moonPositions[] = [$x, $y];
                    }
                    
                    foreach ($currentUnitHexes as $hex) {
                        $terrainOccupiedHexes[] = $hex->q . "," . $hex->r;
                    }

                    // Assign movement
                    $move = new MovementOrder(-1, "start", $center, 0, 0, 0, $h, $h, true, 1, 0, 0);
                    $ship->movement = array($move);
                    break; // Break while(true)
                }
            }

            foreach ($ship->systems as $system) {
                $system->setInitialSystemData($ship);
            }
        }

        $dbManager->setPlayersWaitingStatusInGame($servergamedata->id, false);
        $dbManager->insertShips($servergamedata->id, $servergamedata->ships);
        $dbManager->insertSystemData(SystemData::getAndPurgeAllSystemData());

    }

    public function addAsteroids($gameData, $dbManager, $numberOfAsteroids, $slot)
    {
        $counter = $numberOfAsteroids;
        $irregulars = floor($numberOfAsteroids/6); //one sixth of random asteroid are irregular 2 or 3 hex asteroids.

        //Generate irregular Terrain first
        while ($irregulars > 0) {
            $size = Dice::d(2, 1);  //Use a dice to decide a random size of asteroid!                    
            if($size == 1){
                $currAsteroid = new asteroidTwoHex($gameData->id, -5, "Asteroid #" . $counter . "", $slot);
                $dbManager->submitShip($gameData->id, $currAsteroid, -5); //Save them with a nominal userid of -5, only terrain should use that!               
            }else{
                $currAsteroid = new asteroidThreeHex($gameData->id, -5, "Asteroid #" . $counter . "", $slot);
                $dbManager->submitShip($gameData->id, $currAsteroid, -5); //Save them with a nominal userid of -5, nonly terrain should use that!                            
            }
            $counter--;
            $irregulars--;               
        }

        //Create asteroid as units in database.
        while ($counter > 0) {
            $size = Dice::d(3, 1);  //Use a dice to decide a random size of asteroid!
            if($size == 1){
                $currAsteroid = new asteroidSNew($gameData->id, -5, "Asteroid #" . $counter . "", $slot);
                $dbManager->submitShip($gameData->id, $currAsteroid, -5); //Save them with a nominal userid of -5, only terrain should use that!                   
            }else if($size == 2){
                $currAsteroid = new asteroidMNew($gameData->id, -5, "Asteroid #" . $counter . "", $slot);
                $dbManager->submitShip($gameData->id, $currAsteroid, -5); //Save them with a nominal userid of -5, only terrain should use that!                  
            }else{
                $currAsteroid = new asteroidLNew($gameData->id, -5, "Asteroid #" . $counter . "", $slot);
                $dbManager->submitShip($gameData->id, $currAsteroid, -5); //Save them with a nominal userid of -5, nonly terrain should use that!                    
            }
            $counter--; //Reduce counter   
        }
    }        

public function addMoons($gameData, $dbManager, $smallCount, $mediumCount, $largeCount, $slot)
{
    $moonIndex = 1;

    // Small
    for ($i = 0; $i < $smallCount; $i++) {
        $currMoon = new moonSmallNew($gameData->id, -5, "Moon #$moonIndex", $slot);
        $dbManager->submitShip($gameData->id, $currMoon, -5);
        $moonIndex++;
    }

    // Medium
    for ($i = 0; $i < $mediumCount; $i++) {
        $currMoon = new moonNew($gameData->id, -5, "Moon #$moonIndex", $slot);
        $dbManager->submitShip($gameData->id, $currMoon, -5);
        $moonIndex++;
    }

    // Large
    for ($i = 0; $i < $largeCount; $i++) {
        $currMoon = new moonLarge($gameData->id, -5, "Moon #$moonIndex", $slot);
        $dbManager->submitShip($gameData->id, $currMoon, -5);
        $moonIndex++;
    }
}

    public function getGamespace($gameData)
    {
        $gamespace = $gameData->gamespace; // Example value, could also be "-1x-1"
        $defaultWidth = 60;
        $defaultHeight = 40;
        
        // Extract width and height
        sscanf($gamespace, "%dx%d", $width, $height);
        
        // Check if it's the 'Unlimited' case
        if ($width == -1 && $height == -1) {
            $width = $defaultWidth;
            $height = $defaultHeight;
        }
    
        return ['width' => $width, 'height' => $height];
    }

    public function process(TacGamedata $gameData, DBManager $dbManager, Array $ships, $slotid = 0)
    {

        // Optional sanity check (good for debugging)
        if ($slotid > 0 && !in_array($slotid, array_map(fn($s) => $s->slot, $gameData->slots))) {
            throw new Exception("Invalid slotid");
        }

        $seenSlots = array();
        foreach($gameData->slots as $slot)
        {
            // ✅ Skip slots that are not the requested one (if $slotid > 0)
            if ($slotid > 0 && $slot->slot != $slotid) continue;

            if ($gameData->hasAlreadySubmitted($gameData->forPlayer, $slot->slot))
                continue;

            $points = 0;
            foreach ($ships as $ship){

                if ($ship->slot != $slot->slot)
                    continue;

                $seenSlots[$slot->slot] = true;

                if (!$ship instanceof FighterFlight){
                    $points += $ship->pointCost;
                }


                else {
                    $points += ($ship->pointCost / 6) * $ship->flightSize;
                }

                if ($ship->userid == $gameData->forPlayer){
//Debug::log("bulkBuy " . $ship->bulkBuy);
                    $bulkBuy = isset($ship->bulkBuy) ? $ship->bulkBuy : 1;
//Debug::log("bulkBuy2 " . $bulkBuy);                    
                    for ($m = 0; $m < $bulkBuy; $m++) {
                        
                        // For mines, clone the ship object to save it individually without random movement
                        if (isset($ship->mine) && $ship->mine) {
                            $mineToSave = clone $ship;
                            $mineToSave->userid = $gameData->forPlayer; // Ensure ownership
                            
                            // Deep clone systems so they don't share references and get overwritten
                            $clonedSystems = array();
                            foreach($ship->systems as $sys) {
                                $clonedSys = clone $sys;
                                $clonedSystems[] = $clonedSys;
                            }
                            $mineToSave->systems = $clonedSystems;
                            
                            $id = $dbManager->submitShip($gameData->id, $mineToSave, $gameData->forPlayer);
                            $mineToSave->id = $id; // Set the newly generated DB ID so it doesn't collide
                            $savedShip = $mineToSave;
                        } else {
                            $id = $dbManager->submitShip($gameData->id, $ship, $gameData->forPlayer);
                            $ship->id = $id;
                            $savedShip = $ship;
                        }

                        //unit enhancements
                        foreach($ship->enhancementOptions as $enhancementEntry){ //ID,readableName,numberTaken,limit,price,priceStep
                            $enhID = $enhancementEntry[0];
                            $enhName = $enhancementEntry[1];
                            $enhNo = $enhancementEntry[2];
                            if ($enhNo > 0){ //actually taken
                                $dbManager->submitEnhancement($gameData->id, $id, $enhID, $enhNo, $enhName);
                            }
                        }
                        
                        // Check if ship uses variable flight size
                        if($ship instanceof FighterFlight){
                            $dbManager->submitFlightSize($gameData->id, $id, $ship->flightSize);

                            $firstFighter = $ship->systems[1];
                            $ammo = false;

                            foreach ($firstFighter->systems as $weapon){
                                if(isset($weapon->missileArray)){
                                    $ammo = $weapon->missileArray[1]->amount;
                                    break;
                                }
                            }

                            if ($ammo){
                                foreach($ship->systems as $fighter){
                                    foreach ($fighter->systems as $weapon){
                                        if(isset($weapon->missileArray)){
                                            $weapon->missileArray[1]->amount = $ammo;
                                            $dbManager->submitAmmo($id, $weapon->id, $gameData->id, $weapon->firingMode, $ammo, 0);
                                        }
                                    }
                                }
                            }
                            else{//Marcin Sawicki: generalized version of gun ammo initialization for fighters (not for missile launchers!)
                                foreach($ship->systems as $fighter){
                                    foreach($fighter->systems as $weapon){
                                        if(isset($weapon->ammunition) && (!isset($weapon->missileArray)) && ($weapon->ammunition > 0) ){
                                            $dbManager->submitAmmo($id, $weapon->id, $gameData->id, $weapon->firingMode, $weapon->ammunition, 0);
                                        }
                                    }
                                }
                            }
                        }
                        else{
                            foreach($ship->systems as $systemIndex=>$system){
                                if(isset($system->missileArray)){
                                    // this system has a missileArray. It uses ammo
                                    foreach($system->missileArray as $firingMode=>$ammo){
                                        $dbManager->submitAmmo($id, $system->id, $gameData->id, $firingMode, $ammo->amount, 0);
                                    }
                                }
                                else if($system instanceof Weapon) { //count ammo for other weapons as well!
                                    if(isset($system->ammunition) && ($system->ammunition > 0)){
                                        $dbManager->submitAmmo($id, $system->id, $gameData->id, $system->firingMode, $system->ammunition, 0);
                                    }
                                }
                            }
                        }
                    } // End of bulkBuy loop
                }
            }

            /* Marcin Sawicki - this check needs to be removed as it prevents taking negative-value enhancements
            if ($points > $slot->points)
                throw new Exception("Fleet too expensive.");
            */
                
            $dbManager->setPlayerWaitingStatus($gameData->forPlayer, $gameData->id, true);

            if ($gameData->rules->hasRuleName("moons") && $slot->slot == 1) {
                $moonData = $gameData->rules->getRuleByName('moons')->jsonSerialize();

                $small  = (int)($moonData['small']  ?? 0);
                $medium = (int)($moonData['medium'] ?? 0);
                $large  = (int)($moonData['large']  ?? 0);

                $this->addMoons($gameData, $dbManager, $small, $medium, $large, $slot->slot);
            }

            // Now let's see if we have to add any terrain.
            if ($gameData->rules->hasRuleName("asteroids") && $slot->slot == 1) { // Generate all the asteroids from Slot/Player 1 
                $numberOfAsteroids = 0; //Initialise
                $asteroidsRule = $gameData->rules->getRuleByName('asteroids');
                
                if ($asteroidsRule && method_exists($asteroidsRule, 'jsonSerialize')) {
                    $numberOfAsteroids = $asteroidsRule->jsonSerialize();
                }                 

                $this->addAsteroids($gameData, $dbManager, $numberOfAsteroids, $slot->slot);
            }

        }

        $dbManager->updatePlayerStatus($gameData->id, $gameData->forPlayer, $gameData->phase, $gameData->turn, $seenSlots);
    }
}
