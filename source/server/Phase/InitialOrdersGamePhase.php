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
    // $dbManager is threaded through so the sweep can persist the "a jump point opens" combat-log
    // order it writes (Stage 6): advance() has no submitFireorders of its own.
    JumpEngine::spawnDeclaredVortices($gameData, $dbManager);

    // JUMP_GATES_PLAN.md Stage 4: the same thing for FIXED JUMP GATES, and it has to be its own
    // sweep because the one above skips terrain - correctly (plan trap 2). The two resolve
    // different rules: a ship projects a vortex up to four hexes away, facing where it chose, and
    // may hold only one; a gate opens one on its OWN hex with its OWN fixed facing, and several
    // players may have signalled the same gate this turn, so exactly one claim wins on distance,
    // with a bounded roll-off for a tie (plan section 2.4).
    //
    // IMMEDIATELY AFTER, for the same three reasons the ship sweep is here at all: advance() is
    // handed a real getTacGamedata load (so the engines' vortex state and the units' positions are
    // real), $gameData->phase already reads 2 so nothing may branch on it, and the freshly inserted
    // vortex must not be visible to the active-ship selection above.
    //
    // $dbManager is threaded through so the sweep can persist its own combat-log orders - the
    // winner's line and each loser's - which advance() has no submitFireorders of its own for.
    JumpEngine::openSignalledGates($gameData, $dbManager);

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
            /* ⭐ JUMP_GATES_PLAN.md section 3.1 fact 2 / Stage 2 - THE SERVER HALF OF THE ONE
               EXEMPTION TO "you may only order what you own".

               A FIXED JUMP GATE belongs to whoever bought it - often the enemy - and ANY player may
               signal it (plan section 2.4: gates are contested terrain, with no owner priority).
               The userid guard below is what stops that, and it is widened here and NOWHERE ELSE:
               the power loop, the EW loop and the movement path all keep theirs untouched.

               ⚠️ ONLY THE JUMP ENGINE'S ORDERS ARE PASSED THROUGH. getAllFireOrders() on a gate
               would hand Firing every order on every system of a unit this player does not own; the
               narrow list below is the whole of what a signal is. Anything else forged onto a POSTed
               gate is simply not read - it never reaches validateFireOrders and so can never reach
               the DB.

               ⚠️ Validity is still Firing's call, not this loop's. getVortexDeclarationBlock's gate
               branch is what tests the 10-hex signal range, the charge, the mode and the one-claim
               rule, and it is what overwrites the client's targetid with the re-derived nearest
               unit. A claim from a player with nothing in range is rejected THERE. */
            $gateEngineOrders = null;
            if ($ship->userid != $gameData->forPlayer){
                $gateEngineOrders = self::getGateSignalOrders($ship, $gameData);
                if ($gateEngineOrders === null) continue;
            }

            $orders = ($gateEngineOrders !== null) ? $gateEngineOrders : $ship->getAllFireOrders();

            if (Firing::validateFireOrders($orders, $gd)){
                $dbManager->submitFireorders($gameData->id, $orders, $gameData->turn, $gameData->phase);
            }else{
                throw new Exception("Failed to validate Ballistic firing orders");
            }
        }
				

        $dbManager->updatePlayerStatus($gameData->id, $gameData->forPlayer, $gameData->phase, $gameData->turn);
        $dbManager->setPlayerWaitingStatus($gameData->forPlayer, $gameData->id, true);
    }

    /* JUMP_GATES_PLAN.md Stage 2 - THIS TURN'S GATE SIGNAL ORDERS ON A POSTED UNIT THE PLAYER DOES
     * NOT OWN, or null. Null means "this is an ordinary unowned unit" and the caller skips it
     * exactly as it does today, so the exemption cannot widen by accident.
     *
     * ⚠️ KEYED ON isGateJump(), NEVER ON A HULL NAME (plan trap 12). jumpgateNew and the civilian
     * Jumpgate both mount a JumpEngine and are obsolete and out of scope; neither is marked as a
     * gate, so neither can reach this path.
     *
     * The mirror of ajaxInterface.getGateSignalOrders on the client, and deliberately no laxer:
     * Initial Orders only, a gate engine only, this turn only, firing modes 1-4 only (the
     * programmed open duration - 5, 6 and 7 have no meaning on a gate, and mode 7 in particular is
     * MAINTAIN, which a gate does not have).
     *
     * This is a FILTER, not a validator. Whether the claim is legal - the claiming player has a
     * live unit within 10 hexes, the gate is charged and holds no open vortex, the hex is the
     * gate's own, the duration is within the reactor-damaged cap, no earlier claim this turn - is
     * Firing::getVortexDeclarationBlock's gate branch, which also overwrites the client's targetid
     * with the server's own re-derived nearest unit (plan section 3.3).
     *
     * ⚠️ $gameData->turn is a STRING out of mysqli (Phase 1 trap 10) and a fire order's turn comes
     * off the POST, so both sides are cast. */
    private static function getGateSignalOrders($ship, TacGamedata $gameData)
    {
        if ((int)$gameData->phase !== 1) return null;   //a signal is declared in Initial Orders and nowhere else
        if (!$ship || !is_array($ship->systems)) return null;

        $turn = (int)$gameData->turn;

        foreach ($ship->systems as $system){
            if (!($system instanceof JumpEngine)) continue;
            if (!$system->isGateJump()) continue;

            $orders = array();
            foreach ($system->fireOrders as $fire){
                if ((int)$fire->turn !== $turn) continue;

                $mode = (int)$fire->firingMode;
                if ($mode < 1 || $mode > 4) continue;

                $orders[] = $fire;
            }

            if (empty($orders)) return null;

            Debug::log("Jump gate signal: player " . $gameData->forPlayer . " posted " . count($orders)
                . " claim(s) on gate " . $ship->id . " (game " . $gameData->id . ", turn " . $turn . ").");

            return $orders;
        }

        return null;
    }
}

?>