<?php

class Criticals{


    public static function setCriticals($gamedata){

        $crits = array();

        HkJamming::$alreadyResolved = false; //reset the once-per-advance guard for this resolution
        HkControlNodeOrieni::$shortfallResolved = false; //reset the Orieni HK Control Node shortfall once-per-advance guard

        /* Hangar Ops Stage 10.1: two-pass split.
         *
         * The legacy single-pass loop interleaved testCritical (which on
         * Fighter subsystems rolls the dropout) and criticalPhaseEffects
         * (which on Hangar processes end-of-turn dock orders) per system,
         * per ship. If a carrier appeared before its source FighterFlight
         * in $gamedata->ships, the Hangar's processDockOrders saw a
         * pre-dropout snapshot of the flight and could dock a fighter
         * that was about to drop out the same turn.
         *
         * Pass 1 runs every testCritical block on every ship → every
         * fighter dropout, weapon-overload force-shutdown, and engine-
         * shorted roll resolves before any criticalPhaseEffects fires.
         * Pass 2 runs criticalPhaseEffects + the EngineShorted post-check
         * on the same iteration shape so Hangar Ops (and every other
         * cross-ship-dependent criticalPhaseEffects) sees the final
         * post-roll state.
         *
         * The EngineShorted post-check stays inside Pass 2's per-system
         * loop body so it can react to crits added by either pass — Pass 1
         * testCritical OR Pass 2 criticalPhaseEffects.
         *
         * Ship-list snapshot rationale: ConnectionStrut::testCritical (and
         * potentially other testCriticals) can cascade damage onto primary
         * structure and destroy a ship mid-execution. The legacy single
         * pass continued processing that ship's remaining systems anyway
         * because the ship-level isDestroyed() check was only evaluated
         * at the top of the ship loop. Snapshot the alive-at-start list
         * once and use it for both passes so behaviour matches: a ship
         * that was alive when setCriticals started gets fully processed.
         */
        $activeShips = array();
        foreach ($gamedata->ships as $ship){
            if (!$ship->isDestroyed()) $activeShips[] = $ship;
        }

        /* Jump-sequencing fix: Firing::fireWeapons applies HyperspaceJump /
         * JumpFailure damage to a jumping carrier's primary structure BEFORE
         * setCriticals runs, so a carrier mid-jump reads isDestroyed() here and
         * is absent from $activeShips. Pass 2 therefore never invokes
         * Hangar::criticalPhaseEffects on its hangars, and any queued
         * hangarDockOrder would be silently dropped. This pre-pass lands pending
         * docks on jumping/JumpFailed carriers so fighters that ordered a dock
         * this turn correctly count as being in the hangar at the moment of
         * jump (or destruction, for JumpFailure). Pass 3
         * (processCarrierDestructionEscapes) then sees the post-dock state. */
        HangarOps::processJumpingCarrierDockOrders($gamedata);
        /* WALKERS OF SIGMA-957 (WALKERS_OF_SIGMA_PLAN.md 2.2): the Energy Draining Field drain.
           BEFORE pass 1 on purpose - it rolls its own fighter dropouts and must not interleave
           with testCritical's.
           ⚠️ BOTH lines are inside the gate deliberately: even touching EdfExposure::$resolvedTurn
           would autoload the resolver, so a game with no field on the board must not reach either.
           The reset is the once-per-advance guard, exactly like HkJamming::$alreadyResolved above;
           idempotency ACROSS requests is the EdfExposed marker, not this static. */
        /* WALKERS OF SIGMA-957 (Stage 6): an Energy Draining Mine's probe landed during the Firing
           step that just ran, so its seven hexes are not in the map setEdfHexes() built at load.
           Fold them in now, so a unit caught in one is drained on the turn it lands (user ruling
           2026-09-05) - EnergyDrainingMine::commitPendingFields carries the reasoning.
           ⚠️ BEFORE the gate, not inside it: registerEdfField is what SETS $edfPresent, so a game
           whose only field is a probe that has just landed would otherwise skip the resolver.
           class_exists(..., FALSE) does not autoload, so a game with no AoE weapon in it pays one
           hash lookup and nothing else. */
        if (class_exists('EnergyDrainingMine', FALSE)) EnergyDrainingMine::commitPendingFields($gamedata);

        if (TacGamedata::$edfPresent) {
            EdfExposure::$resolvedTurn = -1;
            EdfExposure::resolve($gamedata);
        }


        // ---- Pass 1: testCritical block --------------------------------
        foreach ($activeShips as $ship){
            foreach ($ship->systems as $system){
                if ( ($system->isDestroyed() && (!($system instanceof MissileLauncher))) ) continue;

                if ($system instanceof Thruster){
                    $chan = Movement::getAmountChanneled($system, $ship, $gamedata->turn);
                    $overthrust = $chan - ($system->output + $system->outputMod );
                    if ($overthrust > 0){
                        $crits = $system->testCritical($ship, $gamedata, $crits, $overthrust);
                    }
                }

                if ($system->isDamagedOnTurn($gamedata->turn)){
                    $crits = $system->testCritical($ship, $gamedata, $crits);
                }

                if($system instanceof Weapon){
                    //for last segment of Sustained shot - force shutdown!
                    if(!$system->isOfflineOnTurn()){
                        $newExtraShots = $system->overloadshots - 1;
                        if( $newExtraShots == 0 ) {
                            $crit = new ForcedOfflineOneTurn(-1, $ship->id, $system->id, "ForcedOfflineOneTurn", $gamedata->turn);
                            $crit->updated = true;
                            $crit->newCrit = true; //force save even if crit is not for current turn
                            $system->criticals[] =  $crit;
                        }
                    }
                }
            }
        }

        // ---- Pass 2: criticalPhaseEffects + EngineShorted post-check ---
        foreach ($activeShips as $ship){
            foreach ($ship->systems as $system){
                $system->criticalPhaseEffects($ship, $gamedata); //hook for Critical phase effects

                //Now check for any EngineShorted Crits added by testCritical or criticalPhaseEffects()
                if ($system instanceof Engine) {
                    foreach ($system->criticals as $critical) {
                        if ($critical->phpclass === "EngineShorted" && $critical->inEffect) {
                            // Check if it matches the current turn
                            if ($critical->turn == $gamedata->turn) {
                                // Found a matching "Engine Shorted" critical for this turn
                                $system->doEngineShorted($ship, $gamedata);
                                break;
                            }
                        }
                    }
                }
            }
        }

        /* Hangar Ops Stage 18: scan ALL ships (not just the activeShips
         * snapshot) for destroyed-this-game carriers whose docked craft
         * haven't yet had an escape roll. Rolls d20, spawns escapees as
         * live FighterFlights at the carrier's last hex/heading/(facing +
         * hangar->direction)/speed, and disengages the non-escapees so
         * they properly fold to combat value 0. Jumped carriers are
         * excluded; the existing fleetList.getJumpedDockedFlightIds path
         * preserves their docked-flight CV verbatim. One-shot per carrier:
         * the roll persists via a hangarEscapeRoll note on the primary
         * hangar, gating subsequent passes via Hangar->escapeRolled. */
        HangarOps::processCarrierDestructionEscapes($gamedata);

        /* LCV Rails: a destroyed carrier's docked LCVs are full ships, not
         * FighterFlights, so they are not handled by the escape pass above.
         * Each docked LCV is treated as launched (escapes) and takes the rail's
         * sustained damage + 2d10 fragments — same as a destroyed rail. */
        HangarOps::processLCVCarrierDestruction($gamedata);

        /* HK Jamming: roll the ELINT-jamming disruption table for every jammed
         * remote-controlled fighter flight (Orieni Hunter-Killers). Runs last so it
         * sees final post-firing state (impact exemption reads this turn's ram) and
         * post-dropout flight composition. Adds ReducedIniativeOneTurn / Uncontrolled /
         * DisengagedFighter crits, which the caller persists via getUpdatedCriticals. */
        HkJamming::resolveJamming($gamedata);

        /* Orieni HK Control Node shortfall: if an Orieni player has more active HK flights
         * than their Control Nodes can command, the excess flights go Uncontrolled next turn.
         * Runs AFTER HkJamming so a flight already made Uncontrolled by Jamming this turn is
         * excluded from the count (it neither needs nor occupies a node). Adds Uncontrolled
         * crits, which the caller persists via getUpdatedCriticals. (NexusMakar nodes keep the
         * legacy proportional getIniMod penalty in the base HkControlNode class.) */
        HkControlNodeOrieni::resolveNodeShortfall($gamedata);

        return $crits;
    }


}
    

?>
