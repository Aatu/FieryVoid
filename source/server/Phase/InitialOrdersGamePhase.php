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
            /* ⚠️⚠️ THE EXIT SWEEP MUST RUN ON THIS PATH TOO, and it is the ONLY one of the three
               vortex sweeps that must. The note on the trio at the foot of this method says the
               early returns cannot strand a declaration, because a ship that just declared one is
               on the board, alive and not terrain - which is precisely what makes it an active
               ship. THAT ARGUMENT DOES NOT HOLD FOR AN EXIT: its opener is in HYPERSPACE and is on
               nobody's initiative list, so a turn with nothing to activate would leave the
               declaration unresolved and the whole wave stranded, silently and for good.

               SAFE HERE AND NOT A LINE EARLIER: the selection above has already concluded there is
               nothing to activate, so the vortex this inserts cannot be handed to the Movement
               phase as an active unit - which is the hazard the ⚠️ on the trio below describes.

               A no-op in every ordinary game: the sweep opens on the reinforcements rule gate. */
            JumpEngine::spawnExitVortices($gameData, $dbManager);

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
            //THE SAME STRANDING GUARD as the getNewActiveShip branch above - see the ⚠️⚠️ there
            //for why the exit sweep alone needs it and why this is the safe place for it.
            JumpEngine::spawnExitVortices($gameData, $dbManager);

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

    // REINFORCEMENTS_PLAN.md Stage 6: the third vortex sweep, and the one that ROLLS. Every jump
    // point EXIT declared in the Initial Orders that just closed forms here - a SpawnJumpPointExit
    // goes onto the board at the hex the DEVIATION ROLL decides, and the declaration itself is
    // moved to that hex so the blue marker both players see is the real one.
    //
    // ⭐ HERE AND NOT AT THE END OF THE FIRING PHASE (user ruling 2026-08-29, plan section 2.3).
    // Both players have committed by now, so rolling here is what lets them SEE where the doorway
    // actually landed for the whole of the turn it forms on, and react to it - instead of finding
    // out at the start of the arrival turn with nothing left to do about it. The vortex UNIT still
    // appears on the board on the arrival turn and not before ($spawned is openTurn + 1).
    //
    // ⚠️ ONLY THE SPAWN HALF MOVED. JumpEngine::stampExitManifests - which turns a manifest into
    // arrival turns - still runs at the end of FireGamePhase::advance and must: it asks whether a
    // doorway will still be open NEXT turn, and closeExpiredVortices is what decides that.
    //
    // LAST of the three, for the reasons the two above give: advance() has already set the next
    // phase (never branch on it), the sweep needs a real getTacGamedata load, and a freshly
    // inserted vortex must not be visible to the active-ship selection higher up. Running third
    // also means the clamp that keeps the rolled hex legal already sees this turn's entrances and
    // gate vortices, so an exit cannot land on top of one.
    //
    // $dbManager is threaded through so the sweep can persist its own combat-log order and its own
    // moved declaration: advance() has no submitFireorders and no updateFireOrders of its own.
    JumpEngine::spawnExitVortices($gameData, $dbManager);

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
				

        /* REINFORCEMENTS_PLAN.md §3.5 / Stage 5 - THE MANIFEST. Which of this player's hyperspace
           units ride through which jump point exit.

           LAST, and after the fire orders have been validated and written, because that is what
           decides which exits actually exist: a declaration Firing rejected never reached the
           DB, so a manifest naming its opener must not be believed either. $gd is re-read for the
           same reason - it now holds this turn's surviving declarations. */
        self::persistManifest($gameData, $dbManager, $ships);

        $dbManager->updatePlayerStatus($gameData->id, $gameData->forPlayer, $gameData->phase, $gameData->turn);
        $dbManager->setPlayerWaitingStatus($gameData->forPlayer, $gameData->id, true);
    }

    /* REINFORCEMENTS_PLAN.md §3.5 - VALIDATE AND PERSIST tac_ship.arrivalvia.
     *
     * The client sets ship.arrivalVia locally on each unit it wants to bring through; this is where
     * that claim is checked and written. THE RULES, all three of them:
     *
     *   1. the unit must be the submitting player's own, and still in hyperspace
     *   2. arrivalVia must name a unit of theirs that is ALSO still in hyperspace
     *   3. that named unit must hold a legal 'jumpexit' declaration for THIS turn
     *
     * ⭐ OR (STAGE 8) arrivalVia names a FIXED GATE that is a doorway in - see collectGateOpeners.
     * A gate is the one opener a player does not own, does not have in hyperspace and may not even
     * have signalled themselves, so it cannot be found by the ship sweep below and is collected
     * separately. Everything downstream is identical: the id goes in the same $openers map.
     *
     * Anything else is written as NULL. Not rejected, not thrown on - NULL is the correct answer
     * for "no berth", and a manifest is a preference rather than an order: the units simply stay in
     * hyperspace and wait for another exit.
     *
     * ⚠️ EVERY QUESTION IS ASKED OF THE SERVER-SIDE SHIP (plan trap 3). A POST-side ship carries no
     * $reinforcement and no $arrivalTurn, so isReinforcement() on the posted object would answer
     * false for everything and the whole manifest would be discarded; and the posted object's
     * fireOrders are the ones just submitted, which is not the same thing as the ones that
     * survived validation.
     *
     * ⚠️ WRITTEN EVERY SUBMISSION, INCLUDING THE UN-SETTING. A player who un-ticks a unit sends no
     * arrivalVia for it, and that has to clear the column - so the sweep walks every hyperspace
     * unit the player owns rather than only the ones the POST mentioned. Skipping the write when
     * nothing changed keeps an ordinary turn's DB traffic at zero.
     */
    private static function persistManifest(TacGamedata $gameData, DBManager $dbManager, Array $ships)
    {
        /* ⚠️ THE RULE GATE IS AN EFFICIENCY GUARD AS WELL AS A CORRECTNESS ONE, and it must stay
           first. This method needs a fresh gamedata load to answer anything, and that would be a
           THIRD full load on every Initial Orders commit of every game in the system - including
           the overwhelming majority that will never have a reinforcement in them. Off ⇒ there are
           no reinforcements to have a manifest, so there is nothing to write and nothing to read. */
        if (!$gameData->rules->hasRuleName('allowReinforcements')) return;

        $gd = $dbManager->getTacGamedata($gameData->forPlayer, $gameData->id);

        //What each unit CLAIMED, keyed by ship id. Absent means "no berth".
        $claims = array();
        foreach ($ships as $ship){
            if ($ship->userid != $gameData->forPlayer) continue;
            if ($ship->arrivalVia === null) continue;
            $claims[(int)$ship->id] = (int)$ship->arrivalVia;
        }

        //Which of this player's hyperspace units hold a live exit declaration this turn. Read
        //off the freshly reloaded gamedata, so a rejected declaration is simply not here.
        $openers = array();
        foreach ($gd->ships as $unit){
            if ($unit->userid != $gameData->forPlayer) continue;
            if (!$unit->isReinforcement()) continue;

            foreach ($unit->systems as $system){
                if (!($system instanceof JumpEngine)) continue;

                foreach ($system->fireOrders as $fire){
                    if ($fire->damageclass !== 'jumpexit') continue;
                    if ((int)$fire->turn !== (int)$gameData->turn) continue;
                    if (!empty($fire->rejected)) continue;

                    $openers[(int)$unit->id] = true;
                    break 2;
                }
            }
        }

        self::collectGateOpeners($gd, $gameData, $openers);

        foreach ($gd->ships as $unit){
            if ($unit->userid != $gameData->forPlayer) continue;
            if (!$unit->isReinforcement()) continue;

            $wanted = isset($claims[(int)$unit->id]) ? $claims[(int)$unit->id] : null;
            if ($wanted !== null && !isset($openers[$wanted])) $wanted = null; //no such exit

            $current = ($unit->arrivalVia === null) ? null : (int)$unit->arrivalVia;
            if ($current === $wanted) continue; //nothing to write

            $dbManager->setShipArrivalVia($unit->id, $wanted);

            Debug::log("Jump point manifest: ship {$unit->id} arrivalvia "
                . ($current === null ? 'NULL' : $current) . " -> "
                . ($wanted === null ? 'NULL' : $wanted)
                . " (game {$gameData->id}, turn {$gameData->turn}, player {$gameData->forPlayer}).");
        }
    }

    /* ⭐⭐ REINFORCEMENTS_PLAN.md STAGE 8 - EVERY FIXED GATE THAT COULD BE THIS PLAYER'S DOORWAY IN,
     * added to the $openers map persistManifest validates against. TWO WAYS A GATE QUALIFIES, and
     * they are the two halves of a gate's life:
     *
     *   1. IT ALREADY HOLDS ONE. A SpawnJumpPointExit stands on the gate. This is the "wave on
     *      each turn of the hold" case (§0) and the one the client's Manage Reinforcements menu
     *      offers as "Select Reinforcements".
     *
     *      ⚠️ AND IT IS DELIBERATELY LENIENT ABOUT THE LAST TURN OF THE HOLD. holdsExitOpenOn
     *      asks hasOpenVortex, which reads $vortexCloseTurn - and closure is not recorded until the
     *      END of the Firing phase, so during Initial Orders a doorway that expires tonight still
     *      answers "open next turn" here. The client knows better (it has the age/hold pair off the
     *      system icon payload and greys the row with "jump point closes this turn"), and the
     *      END-OF-TURN SWEEP IS THE SINGLE AUTHORITY: JumpEngine::collectGateExits asks the same
     *      question at the moment the answer is settled, and refunds a berth that did not make it.
     *      A berth written here and cleared four phases later costs the player nothing - a strict
     *      test here that got the arithmetic wrong would silently drop a legitimate wave, which is
     *      the failure mode worth avoiding.
     *
     *   2. THIS PLAYER HAS JUST CLAIMED IT for arrival, with a live 'gateexit' order for this turn.
     *      The vortex does not exist yet - openSignalledGates spawns it at the end of THIS phase -
     *      so there is nothing to look at but the claim, which is exactly why arrivalVia names the
     *      OPENER and never the vortex (§3.1). The claim may still LOSE the contest to a nearer
     *      enemy entrance claim; that is fine and is why nothing is stamped here. The berth is written,
     *      no exit forms, and JumpEngine::stampArrivingReinforcements refunds it at the end of
     *      the turn.
     *
     * ⚠️ THE CLAIM IS READ THROUGH targetid, WHICH IS THE SERVER'S OWN ANSWER, not the client's.
     * tac_fireorder has no player column and a gate belongs to nobody, so Firing::getGateSignalBlock
     * re-derives the claiming player's nearest unit and OVERWRITES targetid before the row is
     * written (JUMP_GATES_PLAN.md §3.3, trap 4). Matching on it here is therefore asking "did the
     * SERVER record a claim by me?", which is the only trustworthy form of the question.
     *
     * ⚠️ ANOTHER PLAYER'S standing exit on a gate qualifies too, and that is correct rather than
     * a leak: a gate is contested ground with no owner priority, ANY unit of ANY side may use an
     * open vortex (JUMP_GATES_PLAN.md §2.6), and the vortex is a public blue unit on the board by
     * the time this can matter. What stays concealed is who rides it, which is §3.6's job. */
    private static function collectGateOpeners(TacGamedata $gd, TacGamedata $gameData, Array &$openers)
    {
        $turn = (int)$gameData->turn;

        foreach ($gd->ships as $gate){
            if (!$gate->isTerrain()) continue;

            foreach ($gate->systems as $system){
                if (!($system instanceof JumpEngine)) continue;
                if (!$system->isGateJump()) continue;

                //1. a doorway that is already standing and still covers next turn.
                if ($system->holdsExitOpenOn($gd, $turn + 1)){
                    $openers[(int)$gate->id] = true;
                    break;
                }

                //2. this player's own arrival claim, made a moment ago in this same submission.
                foreach ($system->fireOrders as $fire){
                    if ($fire->damageclass !== 'gateexit') continue;
                    if ((int)$fire->turn !== $turn) continue;
                    if (!empty($fire->rejected)) continue;

                    $claimant = $gd->getShipById((int)$fire->targetid);
                    if (!$claimant || $claimant->userid != $gameData->forPlayer) continue;

                    $openers[(int)$gate->id] = true;
                    break 2;
                }
            }
        }
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