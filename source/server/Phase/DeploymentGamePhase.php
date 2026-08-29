<?php

class DeploymentGamePhase implements Phase
{
    public function advance(TacGamedata $gameData, DBManager $dbManager)
    {
        $gameData->setPhase(1);

        $dbManager->setPlayersWaitingStatusInGame($gameData->id, false);
        $dbManager->updateGamedata($gameData);

        //Have to load new gamedata, because the old object does not have moves for ships that were just submitted
        //foreach ($dbManager->getTacGamedata($gameData->forPlayer, $gameData->id)->ships as $ship) {
        foreach ($gameData->ships as $ship) {            
            if ($ship->isDestroyed() || $ship->base || $ship->smallBase || $ship->isTerrain() || ($ship->getTurnDeployed($gameData) > $gameData->turn))  {
                continue;
            }
            if($ship->trueStealth) $ship->checkStealth($gameData); //Extra check needed at start of turn for Stealth ships like Torvalus.

            //Chameleon Sensor Suite: catches a turn-1 deployment that lands inside 5 hexes of an
            //enemy. The end-of-Movement sweep is the primary one (positions do not change between
            //Firing and the next Movement), but it would miss this case entirely.
            if (TacGamedata::$chameleonPresent) $ship->checkChameleonReveal($gameData, 'deployment');
        }

        //Checks for late-deploying slots to see if next phases skipped - DK
        foreach($gameData->slots as $slot){
            $minTurnDeploy = $gameData->getMinTurnDeployedSlot($slot->slot, $slot->depavailable);
            if($minTurnDeploy > $gameData->turn || ($slot->surrendered !== null && $slot->surrendered <= $gameData->turn)){ //Entire slot deploys after current turn or has Surrendered.
                //Set lastphase, and lastTurn to skip Initial Orders on this turn
                $dbManager->updatePlayerSlotPhase($gameData->id, $slot->playerid, $slot->slot, 1, $gameData->turn);
            }
        }

        /* Orieni HK Control Node shortfall at deployment: an HK forced onto the map (no
         * hangar space) is uncontrolled on its FIRST turn, but that turn has no preceding
         * crit phase, so the fire-phase resolver (which applies the penalty NEXT turn) would
         * miss it. Apply the Uncontrolled crit in effect THIS turn for map-deployed HKs that
         * exceed node capacity, then persist the new crits. */
        HkControlNodeOrieni::$deploymentShortfallResolved = false; //reset once-per-advance guard
        HkControlNodeOrieni::resolveDeploymentShortfall($gameData);
        $dbManager->submitCriticals($gameData->id, $gameData->getUpdatedCriticals(), $gameData->turn);
    }

    public function process(TacGamedata $gameData, DBManager $dbManager, Array $ships)
    {
        //Stage 7: flights queued for hangar deploy-start dock skip the
        //"must have a deploy movement" check below — they don't get placed on
        //the board. The transfer field is set on each Hangar in Manager.php
        //during ship parsing, BEFORE this method runs.
        $dockedFlightIds = HangarOps::collectQueuedDeployStartFlightIds($ships);

        $moves = $this->validateDeployment($gameData, $ships, $dockedFlightIds);
		
		foreach ($ships as $currShip){ //generate system-specific information if necessary
			$currShip->generateIndividualNotes($gameData, $dbManager);
		}
		foreach ($ships as $currShip){ //save system-specific information if necessary (separate loop - generate for all, THEN save for all!
			$currShip->saveIndividualNotes($dbManager);
        }

        //Hangar Ops: a deployment-phase dock that SPLITS a flight across rails/bays
        //(performDeployStartDock -> dockFighters) marks the docked fighters in the
        //SOURCE flight with a DockedFighter critical so they don't relaunch with the
        //flight. Those crits are created during generateIndividualNotes above but —
        //unlike the Fire Phase — are never persisted here, so on reload the source
        //flight regained its full size (a 9-flight split 3+3+3 relaunched 9+3+3 = 15).
        //Persist them now (mirrors FireGamePhase::advance). New crits carry id < 1
        //and updated = true; loaded crits are id >= 1 / updated = false, so this
        //inserts only the fresh dock crits with no duplication.
        $dbManager->submitCriticals($gameData->id, $gameData->getUpdatedCriticals(), $gameData->turn);

        /*//Attempted segment when boosting in other phases was allowed
        foreach ($ships as $ship){
            if ($ship->userid != $gameData->forPlayer)
                continue;

            $powers = array();
            // Can now boost Fighter Systems, so look for this.
            if ($ship instanceof FighterFlight) {                
                foreach ($ship->systems as $ftr) {
                    foreach ($ftr->systems as $ftrsys) {                   
                    if (is_array($ftrsys->boostOtherPhases) && in_array($gameData->phase, $ftrsys->boostOtherPhases)) {  //Prevent duplication                                                                                   
                            $powers = array_merge($powers, $ftrsys->power); 
                        }
                       
                    }
                }
            }else{
                foreach ($ship->systems as $system){
                    if (is_array($system->boostOtherPhases) && in_array($gameData->phase, $system->boostOtherPhases)) {                                  
                        $powers = array_merge($powers, $system->power);
                    }    
                }
            }    
		
            $dbManager->submitPower($gameData->id, $gameData->turn, $powers);
            
        }*/
        
		
        foreach ($moves as $shipid=>$move)
        {
            $dbManager->insertMovement($gameData->id, $shipid, $move);
        }

        //REINFORCEMENTS_PLAN.md Stage 7: whatever the player chose NOT to bring through goes back
        //to hyperspace. AFTER the movement writes and after the note loops above - see the method.
        self::releaseUnplacedReinforcements($gameData, $dbManager, $dockedFlightIds);

        $dbManager->updatePlayerStatus($gameData->id, $gameData->forPlayer, $gameData->phase, $gameData->turn);
        $dbManager->setPlayerWaitingStatus($gameData->forPlayer, $gameData->id, true);
    }

    /* REINFORCEMENTS_PLAN.md STAGE 7 - WHAT THE PLAYER DID NOT BRING THROUGH GOES BACK (plan §2.4).
     *
     * Placement is optional, so a wave of four can arrive as a wave of two. The two left behind are
     * not stranded and nothing about them is spent: both arrival fields are cleared, which puts them
     * straight back where they were before the exit formed - `reinforcement` with a NULL
     * arrivalturn, which is exactly what BaseShip::isReinforcement() means by "in hyperspace". They
     * are concealed from the enemy again by hideHyperspaceReinforcements, they answer 999 to both
     * turn accessors, and they can be named on the manifest of the next exit anybody opens.
     *
     * ⚠️ arrivalTurn MUST BE CLEARED, not just arrivalVia. Left set, the unit reads as an ordinary
     * ship that deployed on a turn now in the past - on the board, shootable, EW-relevant, and
     * standing at the off-map 'start' marker its slot gave it, for the rest of the game. §2.4 says
     * "goes back to unassigned"; this is the whole of what that has to mean.
     *
     * ⚠️ RUN AFTER generateIndividualNotes, NOT BEFORE. A reinforcement carrier's fighters dock at
     * deploy start rather than being placed, and that block resolves during the note loops above by
     * asking the SERVER-side flight for its turns (HangarOps::validateDeployBayOrders). Released
     * first, the flight would answer 999 and its own dock would refuse it. The $dockedFlightIds
     * exemption below covers the same case from the other side - a docked unit is aboard, which is
     * an arrival, and it deliberately writes no movement of its own.
     *
     * ⭐⭐ STAGE 8 - A GATE BERTH IS KEPT, A SHIP BERTH IS NOT, and §2.4 says exactly that: "an
     * unplaced unit keeps its berth if the exit will still be open next turn (a gate), and
     * otherwise goes back to unassigned". A ship's doorway closes at the end of the turn it is used
     * on, so there is never anything to keep; a gate's may have three turns left on its programmed
     * hold, and making the player re-name the same manifest every turn would be busywork with a
     * silent failure mode attached.
     *
     * ⭐ AND THE TEST IS "IS THE OPENER A GATE?", NOT "HOW MUCH HOLD IS LEFT?" - deliberately, and
     * this is the one design decision in the method. There are two moments that could decide this:
     * here, in the Deployment phase, and the end-of-turn sweep (JumpEngine::collectGateExits /
     * stampArrivingReinforcements) which already has to answer the identical question for every
     * berth in the game. Two answers means two chances to disagree, and a disagreement here is
     * invisible - a unit either quietly loses a berth it should have kept, or keeps a dead one. So
     * this keeps the berth OPTIMISTICALLY and the end-of-turn sweep is the single authority: it
     * re-stamps a doorway that is still open next turn and refunds one that is not.
     *
     * ⚠️ arrivalTurn IS STILL CLEARED IN BOTH CASES. Keeping a berth is not the same as staying an
     * arrival: the unit has to go back to isReinforcement() (concealed, 999 to both turn accessors,
     * re-stampable) or the ⚠️ above comes true.
     *
     * $dbManager is deliberately UNTYPED, matching JumpEngine::spawnExitVortices - it is what
     * lets the Stage 7 harness drive this with a write-capturing stub and prove the release against
     * real recorded games with zero database writes. */
    private static function releaseUnplacedReinforcements(TacGamedata $gamedata, $dbManager,
                                                          array $dockedFlightIds = array())
    {
        foreach ($gamedata->ships as $unit)
        {
            if ($unit->userid != $gamedata->forPlayer) continue;
            if (!JumpEngine::isArrivingReinforcement($unit, $gamedata)) continue;
            if (isset($dockedFlightIds[(int)$unit->id])) continue;   //arrived into a hangar - see above
            if ($unit->removed) continue;                            //already docked/absorbed this pass
            if ($unit->isDestroyed()) continue;                      //a wreck has nowhere to go back to

            //validateDeployment pushes each accepted move onto the SERVER-side ship, so this reads
            //what was actually committed a moment ago rather than what the client claimed.
            $placed = false;
            foreach ($unit->movement as $move){
                if ($move->type == "deploy" && $move->turn == $gamedata->turn) { $placed = true; break; }
            }
            if ($placed) continue;

            $unit->arrivalTurn = null;
            $dbManager->setShipArrivalTurn($unit->id, null);

            //A gate keeps its berth - see the ⭐⭐ above. getShipById, never the posted object:
            //isTerrain() is a blueprint property and a POST-side unit is not what this list holds
            //anyway ($gamedata->ships is the real load).
            $opener = ($unit->arrivalVia === null) ? null : $gamedata->getShipById((int)$unit->arrivalVia);
            $keepsBerth = ($opener !== null && $opener->isTerrain());

            if (!$keepsBerth){
                $unit->arrivalVia = null;
                $dbManager->setShipArrivalVia($unit->id, null);
            }

            Debug::log("Jump point exit: ship {$unit->id} was not placed and returns to hyperspace"
                . ($keepsBerth ? ", keeping its berth on gate {$unit->arrivalVia}" : "")
                . " (game {$gamedata->id}, turn {$gamedata->turn}).");
        }
    }

    private static function validateDeploymentArea($gamedata, $ship, $move){
        if($ship->isTerrain()) return true; //When manually placing Terrain, they can go anywhere.

        /* REINFORCEMENTS_PLAN.md STAGE 7 - a unit coming out of hyperspace ignores its slot's
           deployment box entirely and may stand in exactly one hex: the jump point exit it is
           riding (plan §2.4). Taken BEFORE the box/distance branches and returning outright, because
           the box would say yes to a hex nowhere near the doorway - a reinforcement's slot is an
           ordinary slot and its box is wherever the fleet started.
           ⚠️ $ship here is already the SERVER-side ship: validateDeployment resolves it through
           getShipById before calling this, which is what makes the arrival test answerable at all
           (a POST-side unit carries neither $reinforcement nor $arrivalTurn - plan trap 3). */
        if (JumpEngine::isArrivingReinforcement($ship, $gamedata)) {
            return self::validateReinforcementArrival($gamedata, $ship, $move);
        }

        $slot = $gamedata->slots[$ship->slot];
        $hexpos = Mathlib::hexCoToPixel($move->position);

        $deppos = Mathlib::hexCoToPixel(new OffsetCoordinate($slot->depx, $slot->depy));

        $hexpos = [
            "x" => round($hexpos["x"]),
            "y" => round($hexpos["y"])
        ];

        $hexWidth = cos(30/180*pi()) * 2;
        $hexHeight = sin(30/180*pi()) + 1;

        if (isset($ship->mine) && $ship->mine) {
            return self::validateMineDeploymentArea($gamedata, $ship, $move, $hexpos, $hexWidth, $hexHeight);
        }

        if ($slot->deptype == "box"){
            $depw = $slot->depwidth*$hexWidth;
            $deph = $slot->depheight*$hexHeight;


            $leftBottom = [
                "x" => floor($deppos["x"]-($depw/2)),
                "y" => floor($deppos["y"]-($deph/2))
            ];

            $rightTop = [
                "x" => ceil($deppos["x"]+($depw/2)),
                "y" => ceil($deppos["y"]+($deph/2))
            ];

            if ($hexpos["x"] <= $rightTop["x"] && $hexpos["x"] >= $leftBottom["x"]){
                if ($hexpos["y"] <= $rightTop["y"] && $hexpos["y"] >= $leftBottom["y"]){
                    return true;
                }
            }
        }else if ($slot->deptype=="distance"){
            if (Mathlib::getDistance($deppos, $hexpos) <= $slot->depheight){
                if (Mathlib::getDistance($deppos, $hexpos) > $slot->depwidth){
                    return true;
                }
            }
        }else{
            if (Mathlib::getDistance($deppos, $hexpos) <= $slot->depwidth){
                return true;
            }
        }


        return false;

    }

    /* REINFORCEMENTS_PLAN.md STAGE 7 - ONE HEX AND ONE FACING (plan §2.4).
     *
     * A reinforcement arrives THROUGH a doorway, so the doorway is the whole of its legal placement:
     * the exit's hex, on the exit's facing, or nowhere. There is no box, no distance and no
     * enemy-proximity rule to apply - the deviation roll already decided where the hex is, and a
     * player who dislikes where it landed may leave the unit in hyperspace instead (which is what
     * the partial-commit exemption in validateDeployment is for).
     *
     * ⭐ THE FACING IS CHECKED AND NOT MERELY FORCED. The client sets it when the unit is placed
     * (shipManager.movement.deploy) and refuses to turn it (canTurn), but the client is not the
     * authority: a submitted move carries whatever facing it carries, and arriving on a heading of
     * one's choosing is a real advantage. HEADING as well as facing, because they are set together
     * on arrival and the Movement phase reads heading to decide which way the unit is travelling.
     *
     * ⚠️ NO STACKING TEST HERE, deliberately. A whole wave comes through one hex (§2.4) and the
     * server has never had a one-ship-per-hex deployment rule anyway - that block lives entirely in
     * the client's onHexClicked, which Stage 7 exempts. Do not "restore" it here.
     *
     * ⚠️ COMPARE THE ORDINATES, never a distance. OffsetCoordinate::distanceTo returns a FLOAT
     * (CubeCoordinate rounds, and round() returns float in PHP), so `$d === 0` is false for a unit
     * standing exactly on the spot - the trap Stage 6 recorded and the shape of rule this is. */
    private static function validateReinforcementArrival($gamedata, $ship, $move) {
        $vortex = JumpEngine::getArrivalVortex($ship, $gamedata);

        //No doorway: the exit closed, was never formed, or the berth names something that is
        //not one. Nothing on the board is a legal hex for this unit, so refuse rather than fall
        //through to a slot box it has no business standing in.
        if ($vortex === null) return false;

        $hex = $vortex->getHexPos();
        if ((int)$move->position->q !== (int)$hex->q) return false;
        if ((int)$move->position->r !== (int)$hex->r) return false;

        $facing = (int)$vortex->getLastMovement()->facing;
        if ((int)$move->facing !== $facing) return false;
        if ((int)$move->heading !== $facing) return false;

        return true;
    }

    private static function validateMineDeploymentArea($gamedata, $ship, $move, $hexpos, $hexWidth, $hexHeight) {
        $myTeam = $gamedata->slots[$ship->slot]->team;

        // 9.5 hex buffer required around enemy deployment zones
        $bufferX = $hexWidth * 9.5;
        $bufferY = $hexHeight * 9.5;

        foreach ($gamedata->slots as $slot) {
            // Only consider enemy areas
            if ($slot->team == $myTeam) continue;

            $deppos = Mathlib::hexCoToPixel(new OffsetCoordinate($slot->depx, $slot->depy));
            
            // Expected boundaries matching the client UI
            $depw = $slot->depwidth * $hexWidth;
            $deph = $slot->depheight * $hexHeight;

            $offsetPosition = [
                "x" => $deppos["x"] - $hexpos["x"],
                "y" => $deppos["y"] - $hexpos["y"]
            ];

            // Expanded bounding box with the buffer
            $isWithinX = abs($offsetPosition["x"]) <= floor($depw / 2) + $bufferX;
            $isWithinY = abs($offsetPosition["y"]) <= floor($deph / 2) + $bufferY;

            if ($isWithinX && $isWithinY) {
                return false; // Found inside a restricted enemy zone
            }
        }

        return true;
    }

    private static function validateDeployment(TacGamedata $gamedata, $ships, array $dockedFlightIds = array())
    {
        $shipIdMoves = array();
        foreach ($ships as $ship)
        {
            if ($ship->userid !== $gamedata->forPlayer)
                continue;


            //PLACEMENT turn, not arrival turn: a late-slot unit commits its entry hex during the
            //Deployment phase of the turn BEFORE it arrives, so that is the turn it must supply
            //a "deploy" move on. The unit itself stays off-board until getTurnDeployed - see
            //BaseShip::getTurnPlaced.
            //⚠️ ASKED OF THE SERVER-SIDE SHIP, NOT THE POSTED ONE (REINFORCEMENTS_PLAN.md trap 3).
            //getTurnPlaced used to read nothing but $gamedata->getSlotById(), which made a POST-side
            //clone safe; it now also reads $reinforcement/$arrivalTurn, and a POST-side ship carries
            //neither. Left on $ship, a unit sitting in hyperspace would answer with its slot's
            //placement turn, be required to supply a deploy move it cannot have, and take the whole
            //submission down with "Entry not found".
            $servership = $gamedata->getShipById($ship->id);
            $placeTurn = ($servership !== null) ? $servership->getTurnPlaced($gamedata) : $ship->getTurnPlaced($gamedata);

            //Stage 7: a flight queued for hangar deploy-start dock has no
            //movement of its own — it goes straight into the carrier's hangar.
            //Skip the "must have a deploy entry" requirement, write no moves.
            if (isset($dockedFlightIds[(int)$ship->id])) {
                $shipIdMoves[$ship->id] = array();
                continue;
            }

            $moves = array();
            $found = false;

            if($placeTurn == $gamedata->turn){ //Is ship picking its entry hex this turn?
                foreach ($ship->movement as $move)
                {
                    if ($found)
                        throw new Exception("Deployment validation failed: Found more than one deployment entry for ship $ship->name.");

                    if ($move->type == "deploy")
                    {
                        $found = true;
                        //$servership was resolved above, for the placement-turn question
                        if (self::validateDeploymentArea($gamedata, $servership, $move))
                        {
                            $moves[] = $move;
                            $servership->movement[] = $move;
                        }else{
                            throw new Exception("Deployment validation failed: Illegal placement. Ship: " . $ship->name . "(".$move->position->q .",".$move->position->r.")");
                        }
                    }
                }
            }

            /* REINFORCEMENTS_PLAN.md STAGE 7 - PLACEMENT IS OPTIONAL FOR AN ARRIVAL (plan §2.4).
               A player may bring some of a wave through and leave the rest in hyperspace - the
               deviation may have put the doorway somewhere they would rather not stand, or they may
               simply want to hold a unit back - so a missing deploy entry is a legal answer here,
               where for every other unit it is a broken submission. What the unit loses by staying
               is its berth, and releaseUnplacedReinforcements is what takes it.
               ⚠️ ASKED OF $servership. The POST-side $ship carries no $arrivalTurn (trap 3), so the
               exemption would never fire and the first player to leave a unit behind would have
               their whole submission rejected. */
            $arriving = ($servership !== null && JumpEngine::isArrivingReinforcement($servership, $gamedata));

            if (!$found && $placeTurn == $gamedata->turn && !$arriving) //Throw if not found and slot is placing this turn.
                throw new Exception("Deployment validation failed: Entry not found for ship $ship->name.");

            $shipIdMoves[$ship->id] = $moves;
        }

        return $shipIdMoves;
    }
}
