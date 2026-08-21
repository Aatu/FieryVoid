<?php

class InitialOrdersGamePhase implements Phase
{

public function advance(TacGamedata $gameData, DBManager $dbManager)
{
    $dbManager->setPlayersWaitingStatusInGame($gameData->id, true);
    $gameData->setPhase(2);

    if ($gameData->rules->hasRule("getNewActiveShip")) {
        $activeShipIds = $gameData->rules->callRule("getNewActiveShip", [$gameData, null]);

        // Check if there are any active ships to assign
        if (empty($activeShipIds)) {
            // No ships available to activate in Movement Phase, move to Firing Phase
            $gameData->setPhase(3);
            $gameData->setActiveship(-1);
            $dbManager->updateGamedata($gameData);
            $dbManager->setPlayersWaitingStatusInGame($gameData->id, false);  
            return;
        }

        $gameData->setActiveship($activeShipIds);
        $gameData->rules->callRule("setActiveShipPlayersNotWaiting", [$gameData, $dbManager]);
    } else {
        $ship = $gameData->getFirstShip();
        if ($ship === null) {
            // No ships found in getFirstShip move to Firing Phase
            $gameData->setPhase(3);
            $gameData->setActiveship(-1);
            $dbManager->updateGamedata($gameData);
            $dbManager->setPlayersWaitingStatusInGame($gameData->id, false);  
            return;
        }
        $dbManager->setPlayerWaitingStatus($ship->userid, $gameData->id, false);
        $gameData->setActiveship($ship->id);
    }

    // Chameleon Sensor Suite: the ELINT/EW plausibility checkpoint (D6c). Runs here rather than in
    // process() because process() rebuilds ships from the POST without applying enhancements or
    // loading notes - the disguise class and the reveal history are both absent on those objects.
    // Note $gameData->phase already reads 2 by now, hence the explicit checkpoint name.
    if (TacGamedata::$chameleonPresent) {
        foreach ($gameData->ships as $ship) {
            $ship->checkChameleonReveal($gameData, 'initial');
        }
    }

    // JUMP_POINTS_PLAN.md Stage 3: every vortex declared in the Initial Orders that just closed
    // FORMS here - a SpawnJumpPoint terrain unit goes onto the board at the declared hex, facing
    // the declared way, visible to everyone from Movement onward. Same reason as the Chameleon
    // checkpoint above for living in advance() rather than process(): process() rebuilds ships from
    // the POST without enhancements or loaded notes, so the engine's own vortex state is absent
    // there and a ship would re-open a jump point it already holds.
    //
    // LAST, deliberately, and after the active-ship selection: the vortex joins $gameData->ships
    // the instant it is inserted, and SimultaneousMovementRule::getNewActiveShip's ship filter -
    // unlike hasShipsAtIniative next to it - does not exclude terrain. The early returns above
    // cannot strand a declaration; a ship that just declared one is on the board, alive and not
    // terrain, which is exactly what makes hasShipsAtIniative find it.
    //
    // Note $gameData->phase already reads 2 by now - never branch on it in the sweep.
    JumpEngine::spawnDeclaredVortices($gameData);

    $dbManager->updateGamedata($gameData);
}

    public function process(TacGamedata $gameData, DBManager $dbManager, Array $ships)
    {

        foreach ($ships as $ship){
            if ($ship->userid != $gameData->forPlayer)
                continue;
            /*
            $powers = array();

            foreach ($ship->systems as $system){
                if ($system->boostOtherPhases) {
                    if (!empty($system->power)) {
                        // Peel off the last entry so we can save it later
                        $lastPower = array_pop($system->power);

                        // Remove any power entries saved during Initial Orders
                        $system->removePowerEntriesForTurn($gameData);

                        // Put the last entry back if you still want it in $system->power
                        $system->power[] = $lastPower;
                    }
                }                    
                $powers = array_merge($powers, $system->power);
            }
		
            $dbManager->submitPower($gameData->id, $gameData->turn, $powers);
            */            
            $powers = array();

            // Can now boost Fighter Systems, so look for this.
            if ($ship instanceof FighterFlight) {                
                foreach ($ship->systems as $ftr) {
                    foreach ($ftr->systems as $ftrsys) {                   
                        /*if (is_array($ftrsys->boostOtherPhases) && in_array($gameData->phase, $ftrsys->boostOtherPhases)) {  //Attempted segment when boosting in other phases was allowed                
                                if (!empty($ftrsys->power)) {                            
                                    // Peel off the last entry so we can save it later
                                    $lastPower = array_pop($ftrsys->power);

                                    // Remove any power entries saved during Initial Orders
                                    $ftrsys->removePowerEntriesForTurn($gameData);

                                    // Put the last entry back in $ftrsys->power                                
                                    $ftrsys->power[] = $lastPower;
                                }                         
                        }*/
                    $powers = array_merge($powers, $ftrsys->power);                        
                    }
                }
            } else {
                foreach ($ship->systems as $system){
                    /*if (is_array($system->boostOtherPhases) && in_array($gameData->phase, $system->boostOtherPhases)) {  //Attempted segment when boosting in other phases was allowed
                        if (!empty($system->power)) {
                            // Peel off the last entry so we can save it later
                            $lastPower = array_pop($system->power);

                            // Remove any power entries saved during Initial Orders
                            $system->removePowerEntriesForTurn($gameData);

                            // Put the last entry back in $system->power
                            $system->power[] = $lastPower;
                        }
                    }*/                    
                $powers = array_merge($powers, $system->power);
                }
            }

            $dbManager->submitPower($gameData->id, $gameData->turn, $powers);
        }



		foreach ($ships as $currShip){ //generate system-specific information if necessary
            if ($currShip->mine) continue;
			$currShip->generateIndividualNotes($gameData, $dbManager);
		}		
		foreach ($ships as $currShip){ //save system-specific information if necessary (separate loop - generate for all, THEN save for all!
            if ($currShip->mine) continue;
			$currShip->saveIndividualNotes($dbManager);
		}


        $gd = $dbManager->getTacGamedata($gameData->forPlayer, $gameData->id);


        foreach ($ships as $ship){
            if ($ship->userid != $gameData->forPlayer)
                continue;

            if (EW::validateEW($ship, $gd)){
                $dbManager->submitEW($gameData->id, $ship->id, $ship->EW, $gameData->turn);
            }else{
                throw new Exception("Failed to validate EW");
            }	
        }


        $gd = $dbManager->getTacGamedata($gameData->forPlayer, $gameData->id); 
		
        if($gd->areMinesPresent){ //There are enemy mines in the game, check if any have been detected.        
            foreach ($gd->ships as $ship) {
                if ($ship->mine) {
                    $ship->generateIndividualNotes($gd, $dbManager);
                    $ship->saveIndividualNotes($dbManager);
					
                    //Have they fired any ballistic at player
					if ($ship->mineType == 'DEW') {
						$mineController = $ship->getSystemByName("MineControllerDEW");
						if ($mineController && $mineController->ballisticWeapon) {
							$newFireOrders = array();
							$fireOrders = $ship->getAllFireOrders();
							foreach($fireOrders as $fo) {
								if($fo->addToDB) {
									$newFireOrders[] = $fo;
								}
							}
							if(count($newFireOrders) > 0) {
								if (Firing::validateFireOrders($newFireOrders, $gd)){
									$dbManager->submitFireorders($gameData->id, $newFireOrders, $gameData->turn, $gameData->phase);
								}else{
									throw new Exception("Failed to validate Mine firing orders");
								}
							}
						}
					}					
                }
            }
        } 

        foreach ($ships as $ship){
            if ($ship->userid != $gameData->forPlayer) continue;

            if (Firing::validateFireOrders($ship->getAllFireOrders(), $gd)){
                $dbManager->submitFireorders($gameData->id, $ship->getAllFireOrders(), $gameData->turn, $gameData->phase);
            }else{
                throw new Exception("Failed to validate Ballistic firing orders");
            }
        }
				

        $dbManager->updatePlayerStatus($gameData->id, $gameData->forPlayer, $gameData->phase, $gameData->turn);
        $dbManager->setPlayerWaitingStatus($gameData->forPlayer, $gameData->id, true);
    }
}

?>