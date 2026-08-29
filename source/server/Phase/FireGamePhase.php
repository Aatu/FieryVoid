<?php

class FireGamePhase implements Phase
{
    public function advance(TacGamedata $gameData, DBManager $dbManager)
    {
        //print("start end");

        $gameData->setPhase(4);
        $gameData->setActiveship(-1);

        $dbManager->updateGamedata($gameData);

        $servergamedata = $dbManager->getTacGamedata($gameData->forPlayer, $gameData->id);
        Firing::prepareFiring($servergamedata, $dbManager); //Marcin Sawicki, October 2017: new approach: calculate base hit chance first!
        Firing::automateIntercept($servergamedata);
        //$dbManager is threaded through so createFailedAttachRamOrders can persist each
        //auto-ram FireOrder immediately (real DB id) before it resolves - see that method.
        Firing::fireWeapons($servergamedata, $dbManager);
        Criticals::setCriticals($servergamedata);

        // JUMP_POINTS_PLAN.md Stage 5: end of turn, after Firing, is when a jump vortex closes -
        // which is what makes one declared closed still usable for the whole of the turn it closes
        // on (plan section 2.3). AFTER setCriticals, deliberately: the jump-failure roll lives in
        // JumpEngine::criticalPhaseEffects and a ship it has just destroyed must read as destroyed
        // here, so its vortex closes with it. The sweep writes its closure notes straight to the
        // DB (Manager::insertIndividualNote) rather than through the generate/save loops below,
        // because it is recording something that has happened, not generating per-ship state.
        JumpEngine::closeExpiredVortices($servergamedata);

        // REINFORCEMENTS_PLAN.md Stage 6: every jump point EXIT declared in this turn's Initial
        // Orders forms here - a SpawnJumpPointExit goes onto the board at the hex the DEVIATION
        // ROLL decides, and every unit riding it is stamped with an arrival turn of turn+1.
        //
        // THE FIRING PHASE AND NOT THE END OF INITIAL ORDERS, where an ENTRANCE's vortex forms, and
        // that is a concealment rule (plan section 2.3): a vortex unit created a phase earlier
        // would publish its DEVIATED hex in every viewer's payload for the whole of the turn, while
        // the only thing an opponent is meant to have is the blue marker at the DECLARED hex.
        // Rolling here means the true hex does not exist during the turn it is secret in.
        //
        // AFTER closeExpiredVortices, so this turn's arrivals cannot be closed by the same pass
        // that ends last turn's exit; and BEFORE the slot loop at the bottom, which asks
        // getTurnDeployed to decide who gets a Deployment phase next turn - the arrival turns
        // stamped here are exactly what that question needs to see (Stage 7).
        //
        // Its combat-log orders need no submit of their own: submitFireorders below picks up every
        // new fire order, which is what the closure sweep above relies on too.
        JumpEngine::spawnExitVortices($servergamedata, $dbManager);


		foreach ($servergamedata->ships as $currShip){ //generate system-specific information if necessary
			$currShip->generateIndividualNotes($servergamedata, $dbManager);
		}		
		foreach ($servergamedata->ships as $currShip){ //save system-specific information if necessary (separate loop - generate for all, THEN save for all!
			$currShip->saveIndividualNotes($dbManager);
		}

        // Chameleon Sensor Suite: firing is where the array gets shot out, and a destroyed array
        // breaks the deception PERMANENTLY. Catching it here rather than at the next turn's
        // checkpoint matters because the owner may self-repair it in between - without this the
        // disguise would quietly come back.
        if (TacGamedata::$chameleonPresent) {
            foreach ($servergamedata->ships as $currShip) {
                $currShip->checkChameleonReveal($servergamedata, 'firing');
            }
        }



        $newFireOrders = $servergamedata->getNewFireOrders();
        $dbManager->submitFireorders($servergamedata->id, $newFireOrders, $servergamedata->turn, 3);
        $dbManager->updateFireOrders($servergamedata->getUpdatedFireOrders());

        // Copy new fire orders to the local $gameData object so subsequent loading calculations can access them
        // Restricted to Mine units as automatic interceptions on normal ships do not have multi-turn reloads.
        foreach ($newFireOrders as $fireOrder) {
            $ship = $gameData->getShipById($fireOrder->shooterid);
            if ($ship instanceof Mine && $ship->mineType == 'DEW') {
                $weapon = $ship->getSystemById($fireOrder->weaponid);
                if ($weapon) {
                    $weapon->setFireOrder($fireOrder);
                }
            }
        }

        $dbManager->submitDamages($servergamedata->id, $servergamedata->turn, $servergamedata->getNewDamages());

        // submit criticals
        $dbManager->submitCriticals($servergamedata->id,  $servergamedata->getUpdatedCriticals(), $servergamedata->turn);
		
        $dbManager->setPlayersWaitingStatusInGame($servergamedata->id, false);

       $playersSkipped = [];

       //Checks for late-deploying slots to see if next phases skipped - DK 
       foreach($gameData->slots as $slot){   
            if (!isset($playersSkipped[$slot->playerid])) {
                $playersSkipped[$slot->playerid] = true; // Assume skipped initially
            }

            $doDeployment = $servergamedata->checkDeploymentPhaseForPlayer($slot->playerid);

            /* Reinforcements pick their ENTRY HEX the turn before they arrive, so the Deployment
               phase is granted on the slot's placement turn (depavailable-1), not its arrival
               turn. The committed hexes then show to everyone as blue "Jump Point" markers for
               the whole of that turn - see BaseShip::getTurnPlaced.

               Raw depavailable deliberately, NOT getMinTurnPlacedSlot: that clamps to turn 1 when
               the slot also holds a base/OSAT/Terrain, which is right for forcing their manual
               turn-1 placement but would swallow the SAME slot's late arrivals and strand them
               off-board for good. The old code used raw depavailable here for the same reason. */
            $placeTurn = ($slot->depavailable > 1) ? $slot->depavailable-1 : $slot->depavailable;
            $needsPhase = ($placeTurn == $gameData->turn+1);

            /* Legacy games mid-flight: a slot whose placement turn already rolled past under the
               OLD arrival-turn rule would never be granted a Deployment phase again and its ships
               would be stranded off-board for good. Fall back to the old arrival-turn phase when
               nothing in the slot has been placed yet. */
            if (!$needsPhase && $slot->depavailable == $gameData->turn+1
                && !$servergamedata->slotHasPlacedShips($slot->slot)) $needsPhase = true;

            /* REINFORCEMENTS_PLAN.md STAGE 7 - the wave that just got an arrival turn needs a
               Deployment phase to walk through its doorway in. Nothing above can produce it: a
               reinforcement's arrival turn is decided in play by the exit it rides, and
               depavailable (which is what every clause above is built on) knows nothing about it.

               ⚠️ ASKED OF $servergamedata, NEVER $gameData. spawnExitVortices ran a few lines
               above this loop and stamped its arrival turns onto that object and the database; the
               outer $gameData was loaded before the Firing phase resolved and still believes every
               reinforcement is in hyperspace. Ask the wrong one and the whole wave silently misses
               the only turn its exit is open on. */
            if (!$needsPhase
                && $servergamedata->hasReinforcementsArriving($slot->playerid, $gameData->turn+1)) {
                $needsPhase = true;
            }

            if ($needsPhase || $doDeployment){
                //Slot places (or pre-orders) next turn, ensure that database know it completed this Firing Phase
                $dbManager->updatePlayerSlotPhase($gameData->id, $slot->playerid, $slot->slot, 3, $gameData->turn);   
                $playersSkipped[$slot->playerid] = false; // Mark player as NOT skipped             
            } else {
                //If not deploying next turn, set slot to skip that phase.  Manager::changeTurn always tries to create new Deplyment Phase.
                $dbManager->updatePlayerSlotPhase($gameData->id, $slot->playerid, $slot->slot, -1, $gameData->turn+1);                
            }        
        } 

        // Update waiting status for fully skipped players
        foreach ($playersSkipped as $playerid => $isSkipped) {
            if ($isSkipped) {
                 $dbManager->setPlayerWaitingStatus($playerid, $gameData->id, true);
            }
        } 
    }

    public function process(TacGamedata $gameData, DBManager $dbManager, Array $ships)
    {
        foreach ($ships as $ship){
            if ($ship->userid != $gameData->forPlayer)
                continue;

            if ($ship->isDestroyed())
                continue;

            if (Movement::validateMovement($gameData, $ship)){
                if (count($ship->movement)>0)
                    $dbManager->submitMovement($gameData->id, $ship->id, $gameData->turn, $ship->movement);
            }

            if (Firing::validateFireOrders($ship->getAllFireOrders(), $gameData)){
                $dbManager->submitFireorders($gameData->id, $ship->getAllFireOrders(), $gameData->turn, $gameData->phase);
            }
            /*//Attempted segment when boosting in other phases was allowed
            $powers = array();
            //Can now bosot Fighter Systems, so look for this.
            if($ship instanceof FighterFlight){                
                foreach($ship->systems as $ftr) foreach($ftr->systems as $ftrsys){                   
                    if (is_array($ftrsys->boostOtherPhases) && in_array($gameData->phase, $ftrsys->boostOtherPhases)) {  //Prevent duplication                     
                        if (!empty($ftrsys->power)) {                            
                            // Peel off the last entry so we can save it later
                            $lastPower = array_pop($ftrsys->power);

                            // Remove any power entries saved during Initial Orders
                            $ftrsys->removePowerEntriesForTurn($gameData);

                            // Put the last entry back if you still want it in $system->power
                            $ftrsys->power[] = $lastPower;
                        }
                        $powers = array_merge($powers, $ftrsys->power);
                        $ftrsys->doIndividualNotesTransferGD($gameData);                           
                    }                 
                }
            }else{
                foreach ($ship->systems as $system){
                    if (is_array($system->boostOtherPhases) && in_array($gameData->phase, $system->boostOtherPhases)) {  //Prevent duplication
                        if (!empty($system->power)) {
                            // Peel off the last entry so we can save it later
                            $lastPower = array_pop($system->power);

                            // Remove any power entries saved during Initial Orders
                            $system->removePowerEntriesForTurn($gameData);

                            // Put the last entry back if you still want it in $system->power
                            $system->power[] = $lastPower;
                        }
                        $powers = array_merge($powers, $system->power);
                        $system->doIndividualNotesTransferGD($gameData);                      
                    }                   
                }
            }

            $dbManager->submitPower($gameData->id, $gameData->turn, $powers);
            */
            
            //Sadly we have to add a quick process here to catch things like ships that have used their Specialists in Phase 3, otherwise notes aren't created before firing.
            $ship->generateAdditionalNotes($gameData, $dbManager);            
        }		

        $dbManager->updatePlayerStatus($gameData->id, $gameData->forPlayer, $gameData->phase, $gameData->turn);
        $dbManager->setPlayerWaitingStatus($gameData->forPlayer, $gameData->id, true);
        
        return true;
    }
}
