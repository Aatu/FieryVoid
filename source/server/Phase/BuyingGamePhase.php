<?php

class BuyingGamePhase implements Phase
{

    public function advance(TacGamedata $gameData, DBManager $dbManager)
    {
        $servergamedata = $dbManager->getTacGamedata($gameData->forPlayer, $gameData->id);

        $t1 = 0;
        $t2 = 0;
        $usedPositions = array(); // To store assigned (x, y) pairs 
        $moonPositions = array(); // To store assigned (x, y) pairs 

        foreach ($servergamedata->ships as $ship){

            if($ship->userid != -5){ //No point doing any of this for Asteroids, they will place separately in next part.
                $h = 3;
                if ($ship->team == 1){
                    $t1++;
                    $t = $t1;
                    $h = 0;
                }else{
                    $t2++;
                    $t = $t2;
                }

                if ($t % 2 == 0){
                    $y = $t/2;
                }else{
                    $y = (($t-1)/2)*-1;
                }

                $x = -30;

                if ($ship->team == 2){
                    $x=30;
                }
                
                $move = new MovementOrder(-1, "start", new OffsetCoordinate($x, $y), 0, 0, 5, $h, $h, true, 1, 0, 0);
                $ship->movement = array($move);
            } 
    /*               
            // Now let's see if we have to add any terrain.
            if (($gameData->rules->hasRuleName("asteroids") || $gameData->rules->hasRuleName("moons")) && $ship->userid == -5){
                // It's aterrain, so assign a unique random position.
                $deploymentZone = $this->getGamespace($gameData);
                if($ship instanceof moonSmall || $ship instanceof moon){
                    $maxX = ($deploymentZone['width'] / 2) - 6;
                    $maxY = ($deploymentZone['height'] / 2) - 4;                    
                }else{
                    $maxX = ($deploymentZone['width'] / 2) - 3;
                    $maxY = ($deploymentZone['height'] / 2) - 2;
                }    


                // Generate a unique random position
                while (true) {
                    $x = rand(-$maxX, $maxX);
                    $y = rand(-$maxY, $maxY);
                    
                    if (!isset($usedPositions["$x,$y"])) {
                        $usedPositions["$x,$y"] = true;
                        break;
                    }
                }

                $usedPositions["$x,$y"] = true; // Mark position as used
                $h = rand(0, 5); // Random heading/facing

                $move = new MovementOrder(-1, "start", new OffsetCoordinate($x, $y), 0, 0, 0, $h, $h, true, 1, 0, 0);
                $ship->movement = array($move);
            }
*/

        // Now let's see if we have to add any terrain.
        if (($gameData->rules->hasRuleName("asteroids") || $gameData->rules->hasRuleName("moons")) && $ship->userid == -5) {
            // It's an asteroid or moon, so assign a unique random position.
            $deploymentZone = $this->getGamespace($gameData);
            
            if ($ship instanceof moonSmall || $ship instanceof moon) {
                $maxX = ($deploymentZone['width'] / 2) - 7;
                $maxY = ($deploymentZone['height'] / 2) - 5;  
            } else {
                $maxX = ($deploymentZone['width'] / 2) - 3;
                $maxY = ($deploymentZone['height'] / 2) - 2;
            }    

            // Generate a unique random position
            while (true) {
                $x = rand(-$maxX, $maxX);
                $y = rand(-$maxY, $maxY);
                
                // Check for occupied hexes (applies to all)
                if (isset($usedPositions["$x,$y"])) {
                    continue; // Position is already taken
                }

                // If it's a moon, ensure it's not within 4 hexes of another moon
                if ($ship instanceof moonSmall || $ship instanceof moon) {
                    $tooClose = false;
                    foreach ($moonPositions as [$mx, $my]) {
                        $dx = $x - $mx;
                        $dy = $y - $my;
                        $distance = sqrt($dx * $dx + $dy * $dy); // Euclidean distance
                        
                        if ($distance < 5) { // Ensure moons are at least 5 hexes apart
                            $tooClose = true;
                            break;
                        }
                    }
                    if ($tooClose) {
                        continue; // Try another position
                    }
                }

                // If it's an asteroid, ensure it's not within 2 hexes of any moon
                if ($ship instanceof asteroidS || $ship instanceof asteroidM || $ship instanceof asteroidL) {
                    $tooCloseToMoon = false;
                    foreach ($moonPositions as [$mx, $my]) {
                        $dx = $x - $mx;
                        $dy = $y - $my;
                        $distance = sqrt($dx * $dx + $dy * $dy);

                        if ($distance < 3) { // Asteroids must be at least 3 hexes away from any moon
                            $tooCloseToMoon = true;
                            break;
                        }
                    }
                    if ($tooCloseToMoon) {
                        continue; // Try another position
                    }
                }                

                // Valid position found
                $usedPositions["$x,$y"] = true;
                
                // If it's a moon, store its position
                if ($ship instanceof moonSmall || $ship instanceof moon) {
                    $moonPositions[] = [$x, $y];
                }

                break;
            }

            $h = rand(0, 5); // Random heading/facing
            $move = new MovementOrder(-1, "start", new OffsetCoordinate($x, $y), 0, 0, 0, $h, $h, true, 1, 0, 0);
            $ship->movement = array($move);
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

        //Create asteroid as units in database.
        while ($counter > 0) {
            $size = Dice::d(3, 1);  //Use a dice to decide a random size of asteroid!
            if($size == 1){
                $currAsteroid = new asteroidS($gameData->id, -5, "Asteroid #" . $counter . "", $slot);
                $dbManager->submitShip($gameData->id, $currAsteroid, -5); //Save them with a nominal userid of -5, only terrain should use that!                   
            }else if($size == 2){
                $currAsteroid = new asteroidM($gameData->id, -5, "Asteroid #" . $counter . "", $slot);
                $dbManager->submitShip($gameData->id, $currAsteroid, -5); //Save them with a nominal userid of -5, only terrain should use that!                  
            }else{
                $currAsteroid = new asteroidL($gameData->id, -5, "Asteroid #" . $counter . "", $slot);
                $dbManager->submitShip($gameData->id, $currAsteroid, -5); //Save them with a nominal userid of -5, nonly terrain should use that!                    
            }
            $counter--; //Reduce counter   
        }
    }        

    public function addMoons($gameData, $dbManager, $moonValue, $slot)
    {
        $counter = $moonValue; // Should always be at least 1 to get here.
        $moonIndex = 1; // For naming
    
        if ($counter == 1) {    
            // Add a single large Moon
            $currMoon = new moon($gameData->id, -5, "Moon #$moonIndex", $slot);
            $dbManager->submitShip($gameData->id, $currMoon, -5);
        } else if ($counter > 1 && $counter < 4) {  
            // Add a big Moon first
            $currMoon = new moon($gameData->id, -5, "Moon #$moonIndex", $slot);
            $dbManager->submitShip($gameData->id, $currMoon, -5);
            $counter--; 
            $moonIndex++; 
    
            // Create any remaining small moons (1 or 2)
            while ($counter > 0) {
                $currMoon = new moonSmall($gameData->id, -5, "Moon #$moonIndex", $slot);
                $dbManager->submitShip($gameData->id, $currMoon, -5);
                $counter--; 
                $moonIndex++; 
            }
        } else if ($counter == 4) { 
            // Exactly three small moons
            while ($counter > 0) {
                $currMoon = new moonSmall($gameData->id, -5, "Moon #$moonIndex", $slot);
                $dbManager->submitShip($gameData->id, $currMoon, -5);
                $counter--; 
                $moonIndex++; 
            }
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

    public function process(TacGamedata $gameData, DBManager $dbManager, Array $ships)
    {

        $seenSlots = array();
        foreach($gameData->slots as $slot)
        {
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
                    $id = $dbManager->submitShip($gameData->id, $ship, $gameData->forPlayer);

                    
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
                }
            }

            /* Marcin Sawicki - this check needs to be removed as it prevents taking negative-value enhancements
            if ($points > $slot->points)
                throw new Exception("Fleet too expensive.");
            */
                
            $dbManager->setPlayerWaitingStatus($gameData->forPlayer, $gameData->id, true);

            // Now let's see if we have to add any terrain.
            if ($gameData->rules->hasRuleName("asteroids") && $slot->slot == 1) { // Generate all the asteroids from Slot/Player 1 
                $numberOfAsteroids = 0; //Initialise
                $asteroidsRule = $gameData->rules->getRuleByName('asteroids');
                
                if ($asteroidsRule && method_exists($asteroidsRule, 'jsonSerialize')) {
                    $numberOfAsteroids = $asteroidsRule->jsonSerialize();
                }                 

                $this->addAsteroids($gameData, $dbManager, $numberOfAsteroids, $slot->slot);
            }

            if ($gameData->rules->hasRuleName("moons") && $slot->slot == 1) { // Generate all moons from Slot/Player 1 
                $moonValue = 0; //Initialise
                $moonsRule = $gameData->rules->getRuleByName('moons');
                
                if ($moonsRule && method_exists($moonsRule, 'jsonSerialize')) {
                    $moonValue = $moonsRule->jsonSerialize();
                }                 

                $this->addMoons($gameData, $dbManager, $moonValue, $slot->slot);
            }

        }

        $dbManager->updatePlayerStatus($gameData->id, $gameData->forPlayer, $gameData->phase, $gameData->turn, $seenSlots);
    }
}
