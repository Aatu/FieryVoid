<?php

class Criticals{


    public static function setCriticals($gamedata){

        $crits = array();

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

        return $crits;
    }


}
    

?>
