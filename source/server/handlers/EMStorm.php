<?php
/**
 * THE ELECTROMAGNETIC STORM - EM_STORM_PLAN.md. The rule itself is EMStormRule; this rolls its effects.
 *
 * "At the start of each turn, before Initial Orders, each unit suffers the following effects ... temporary and
 * end at the conclusion of the current turn." Every roll is written as a `oneturn` critical carrying its amount in
 * `param`, stamped with the turn BEFORE the one it hurts, so hasCritical()/sumCriticalParam() report it on exactly
 * that turn and it lapses by itself. The RESULT is the record: replay and reloads read the criticals, nothing is
 * re-rolled. The Energy Draining Field (EdfExposure) is the same machine and the template for this one.
 *
 * TWO SWEEPS (user ruling R1):
 *   resolveEndOfTurn()  - from the top of Criticals::setCriticals, before Pass 1: stamped turn N, in force turn N+1.
 *                         Before Pass 1 so an attack's forced critical roll (Burst Beam's "+4") is rolled by Pass 1.
 *   resolveDeployment() - from DeploymentGamePhase::advance: every unit whose first turn is this one (turn 1:
 *                         everybody), stamped turn T-1, in force turn T. It rolls its own critical rolls.
 *
 * ⭐ EVERY CALLER TESTS TacGamedata::$emStormPresent FIRST. Nothing in here checks it again.
 * ⚠️ Never branch on $gamedata->phase in here: an advance() has already set the next phase.
 */
class EMStorm
{
    const INI_SCALE = 5;           //FV initiative is d100: one tabletop point is five
    const NOTE_KEY = 'stormDefence'; //an EM shield's Storm Defence declaration (EMShield::generateIndividualNotes)
    const LOG_CLASS = 'EMStorm';   //the combat-log line's damageclass (Firing::isHyperspaceLogOrder keeps it out of the firing)

    /* Once per request for the end-of-turn sweep; idempotency ACROSS requests is the EMStormInitiative crit
       (alreadyResolved). Criticals::setCriticals resets it, as it resets EdfExposure::$resolvedTurn. */
    public static $resolvedTurn = -1;

    /* The forced critical rolls (Burst Beam's "+4") whose result the log line is still waiting for: each entry
       array('system', 'before' => its crit count before the roll, 'token', 'order' => the unit's log line). At the
       end of a turn Pass 1 makes the roll AFTER the sweep, so Criticals::setCriticals calls reportForcedRolls() once
       Pass 1 is done; at Deployment the sweep rolls them itself and reports at once. Reset by each sweep. */
    private static $pendingRolls = array();

    /* "Advanced races (factionAge 3 or more) subtract 1 from all effect rolls. They do not temporarily lose power
       and are immune to the attacks." Ask the UNIT - a weapon's factionAge is always 1. */
    public static function isAdvanced($unit){
        return $unit !== null && (int)$unit->factionAge >= 3;
    }

    /* ================================================================ the two sweeps ======= */

    //End of turn N (Criticals::setCriticals): every unit on the map, for turn N+1.
    public static function resolveEndOfTurn($gamedata){
        $stamp = (int)$gamedata->turn;
        if (self::$resolvedTurn === $stamp) return;
        self::$resolvedTurn = $stamp;
        self::$pendingRolls = array();

        foreach ($gamedata->ships as $unit){
            if (!self::isStormable($unit)) continue;
            if ($unit->getTurnDeployed($gamedata) > $stamp) continue; //arrives through a later Deployment - rolled there
            self::strikeUnit($unit, $gamedata, $gamedata, $stamp, 1, false);
        }
    }

    /* Deployment of turn T (DeploymentGamePhase::advance): the units placed this turn, for turn T. $gamedata must be
       a FRESH load - the advance's own object predates the last submitter's Storm Defence notes. The criticals are
       made through a copy of it whose turn is T-1, so every crit the storm causes - its own, a blackout's, and those
       a forced critical roll produces - is stamped exactly as the end-of-turn sweep would have stamped it.
       Saves what it made: its criticals, and its log lines (phase 2: submitFireorders keeps only ballistics in 1). */
    public static function resolveDeployment($gamedata, $dbManager){
        $turn = (int)$gamedata->turn;
        $stamp = $turn - 1;
        $shifted = clone $gamedata; //shallow: the same ships, only `turn` differs
        $shifted->turn = $stamp;
        self::$pendingRolls = array();

        $any = false;
        foreach ($gamedata->ships as $unit){
            if (!self::isStormable($unit)) continue;
            if ((int)$unit->getTurnDeployed($gamedata) !== $turn) continue; //already on the map: rolled at the end of last turn
            if (self::strikeUnit($unit, $gamedata, $shifted, $stamp, -1, true)) $any = true;
        }
        if (!$any) return;

        $dbManager->submitCriticals($gamedata->id, $gamedata->getUpdatedCriticals(), $turn);
        $dbManager->submitFireorders($gamedata->id, $gamedata->getNewFireOrders(), $turn, 2);
    }

    /* Mines and terrain are immune (user). A destroyed unit, a docked flight (its craft read as destroyed) and a
       reinforcement still in hyperspace are not on the map. */
    private static function isStormable($unit){
        if ($unit->isDestroyed()) return false;
        if ($unit->isTerrain()) return false;
        if ($unit instanceof Mine) return false;
        if ($unit->isReinforcement()) return false;
        return true;
    }

    /* ================================================================ one unit ============= */

    /* $gamedata is the real game; $critData the object the criticals are made through (the real one at the end of a
       turn, the shifted copy at Deployment - see resolveDeployment). $declPhase is the phase whose Storm Defence
       declarations count: Initial Orders (1) for the end-of-turn storm, Deployment (-1) for the Deployment one.
       Returns true when it rolled the unit. */
    private static function strikeUnit($unit, $gamedata, $critData, $stamp, $declPhase, $rollCriticalsNow){
        $marker = self::markerSystem($unit);
        if (!$marker) return false;
        if (self::alreadyResolved($marker, $stamp)) return false;

        $advanced = self::isAdvanced($unit);
        $less = $advanced ? 1 : 0; //"subtract 1 from all effect rolls"
        $isFlight = ($unit instanceof FighterFlight);
        $parts = array();

        if ($isFlight){
            /* "Fighters and shuttles lose 1d3 from their offensive bonuses, but never more than one-half (drop
               fractions) of their offensive rating" - the rating as the nebula has already left it (user). Capped
               here, once, so the readers only ever subtract the stored figure. */
            $ob = (int)$unit->offensivebonus;
            if (TacGamedata::$nebulaPresent) $ob = Nebula::offensiveBonus($ob);
            $obLoss = min(max(0, Dice::d(3) - $less), (int)floor(max(0, $ob) / 2));
            if ($obLoss > 0){
                self::addParamCrit($marker, $unit, 'EMStormOffensive', $obLoss, $stamp);
                $parts[] = "-" . ($obLoss * 5) . " offensive bonus"; //as FV shows a bonus: x5
            }
        } else {
            //"Ships suffer a penalty of 1d6 from their sensor ratings" - not rolled in a nebula, where there is no EW at all (user).
            if (!TacGamedata::$nebulaPresent){
                $scanner = self::liveSystem($unit, 'Scanner');
                $sensors = max(0, Dice::d(6) - $less);
                if ($scanner && $sensors > 0){
                    self::addParamCrit($scanner, $unit, 'EMStormSensors', $sensors, $stamp);
                    $parts[] = "-$sensors sensors";
                }
            }

            if (!$advanced){
                //"Roll 1d6, and if a 4 or less is rolled (5 or less for enormous units, 3 or less for medium ships), reduce
                //the ship's power by the number of points shown on the die." EM hardening takes a point off (tabletop).
                $roll = Dice::d(6);
                if ($roll <= self::powerThreshold($unit)){
                    $reactor = self::liveSystem($unit, 'Reactor');
                    $power = WeaponEM::powerLoss($unit, $roll);
                    if ($reactor && $power > 0){
                        self::addParamCrit($reactor, $unit, 'EMStormPower', $power, $stamp);
                        $parts[] = "-$power power";
                        //a deficit the ship cannot shed blacks the reactor out, as a drained EDF reactor does (D14)
                        if (EdfExposure::applyPowerBlackout($unit, $reactor, $critData)) $parts[] = "REACTOR DRAINED - every powered system offline";
                    }
                }

                $strikes = self::resolveAttacks($unit, $gamedata, $critData, $stamp, $declPhase, $rollCriticalsNow);
                if (!empty($strikes)) $parts[] = implode('; ', $strikes);
            }
        }

        //"All units suffer a penalty of 2d6 from their initiative ratings." Last: it is also the "done" marker.
        $ini = max(0, Dice::d(6, 2) - $less);
        self::addParamCrit($marker, $unit, 'EMStormInitiative', max(1, $ini), $stamp);
        array_unshift($parts, "-" . (max(1, $ini) * self::INI_SCALE) . " initiative");

        $order = self::logResult($unit, $gamedata, $stamp, $parts);
        //this unit's forced critical rolls report into this line - at once if they have been rolled already (Deployment)
        foreach (self::$pendingRolls as $k => $entry){
            if ($entry['unit'] === $unit->id && $entry['order'] === null) self::$pendingRolls[$k]['order'] = $order;
        }
        if ($rollCriticalsNow) self::reportForcedRolls();
        return true;
    }

    //"5 or less for enormous units, 3 or less for medium ships" - and 4 for everything between (D6).
    private static function powerThreshold($unit){
        if (!empty($unit->Enormous)) return 5;
        if ((int)$unit->shipSizeClass <= 1) return 3;
        return 4;
    }

    //"Medium ships are attacked once, heavy combat vessels 1d2 times, capital ships 1d3 times, and enormous units 1d6 times."
    private static function attackCount($unit){
        if (!empty($unit->Enormous)) return Dice::d(6);
        $size = (int)$unit->shipSizeClass;
        if ($size <= 1) return 1;
        if ($size == 2) return Dice::d(2);
        return Dice::d(3);
    }

    /* ================================================================ the attacks ========== */

    /* "Roll each attack against a random side (but do not count a destroyed side as a valid target). The attack always
       hits, so roll for location against the affected side. If structure is hit, there is no effect. However, any other
       system will be involuntarily deactivated exactly as if it had been hit by an electro-burst beam. Multiple hits on
       the same location in the same turn are not cumulative." Returns the log's account of each strike. */
    private static function resolveAttacks($unit, $gamedata, $critData, $stamp, $declPhase, $rollCriticalsNow){
        $sides = self::liveSides($unit);
        $count = self::attackCount($unit);
        if (empty($sides)) return array();

        $shields = self::declaredShields($unit, $stamp + 1, $declPhase);
        $struck = array();   //system id => true: "not cumulative"
        $usedShields = array();
        $lines = array();

        //Burst Beam's hit is a plain Standard shot: no Flash, no Piercing, never ballistic (getHitSystemByTable/ByDice read only these)
        $pseudoWeapon = (object)array('ballistic' => false, 'damageType' => 'Standard', 'noPrimaryHits' => false);
        $pseudoFire = new FireOrder(-1, 'normal', $unit->id, $unit->id, -1, -1, (int)$gamedata->turn, 1,
            0, 0, 1, 0, 0, 0, 0, self::LOG_CLASS, 0);

        for ($i = 0; $i < $count; $i++){
            $side = $sides[Dice::d(count($sides)) - 1];

            /* "Ships with EM shields may elect to accept this effect using a facing shield, but this must be declared before
               the roll for location is made ... it cannot use the same shield against more than one attack." Declared
               (Storm Defence), unused, alive, and its arc covering the side's direction; several - one at random, as
               Burst Beam chooses among interposed EM shields. */
            $facing = array();
            foreach ($shields as $shield){
                if (isset($usedShields[$shield->id])) continue;
                if (self::arcCovers($shield, $side['bearing'])) $facing[] = $shield;
            }
            if (!empty($facing)){
                $shield = $facing[Dice::d(count($facing)) - 1];
                $usedShields[$shield->id] = true;
                $struck[$shield->id] = true;
                self::forceOffline($unit, $shield, $stamp);
                $lines[] = $shield->displayName . " takes a strike (offline)";
                continue;
            }

            $source = self::pointOnBearing($unit, $side['bearing']);
            $system = $unit->getHitSystem($unit, $pseudoFire, $pseudoWeapon, $gamedata, $side['loc'], $source);
            if (!$system || $system instanceof Structure){
                $lines[] = "strike on Structure (no effect)";
                continue;
            }
            if (isset($struck[$system->id])){
                $lines[] = $system->displayName . " struck again (no further effect)";
                continue;
            }
            $struck[$system->id] = true;
            $lines[] = self::burstEffect($unit, $system, $critData, $stamp, $rollCriticalsNow);
        }
        return $lines;
    }

    /* Burst Beam's table (BurstBeam::onDamagedSystem, specialWeapons.php) less its Structure line, which the storm
       replaces with "no effect" (user ruling R2: exactly as Burst Beam). Advanced Armor shrugs it off; EM hardening
       gets its tabletop saves (WeaponEM - arch_em_hardening_tabletop). */
    private static function burstEffect($unit, $system, $critData, $stamp, $rollCriticalsNow){
        $name = $system->displayName;
        if (WeaponEM::isTargetEMResistant($unit, $system)) return "$name unaffected (Advanced Armor)";

        if ($system->powerReq > 0 || $system->canOffLine){
            if (WeaponEM::resistsShutdown($unit)) return "$name resists (EM hardening)";
            self::forceOffline($unit, $system, $stamp);
            return "$name shut down";
        }

        //"Other system: critical roll forced (at +4)" - Pass 1 of setCriticals rolls it at the end of a turn; at
        //Deployment there is no Pass 1, so it is rolled here, through the shifted copy (its crits stamped T-1).
        //Either way the log line carries a token until the roll is known - reportForcedRolls() fills it in.
        $system->forceCriticalRoll = true;
        $system->critRollMod += 4;
        WeaponEM::hardenedCriticalRoll($unit, $system, $critData->turn); //EM hardening: -2 on the roll
        $token = '{emscrit:' . count(self::$pendingRolls) . '}';
        self::$pendingRolls[] = array('system' => $system, 'before' => count($system->criticals), 'token' => $token,
            'unit' => $unit->id, 'order' => null);
        if ($rollCriticalsNow){
            $system->testCritical($unit, $critData, array());
            $system->forceCriticalRoll = false;
        }
        return "$name critical roll +4" . $token;
    }

    /* The result of each forced critical roll, into the log line that announced it: " - critical: <what>" or
       " - no critical". Criticals::setCriticals calls it straight after Pass 1 (which made the end-of-turn rolls), and
       before FireGamePhase saves the new fire orders; the Deployment sweep calls it per unit, having rolled already.
       The crits a roll added are the ones past the count recorded when it was forced. */
    public static function reportForcedRolls(){
        foreach (self::$pendingRolls as $entry){
            if (!$entry['order']) continue; //a unit with no log host
            $names = array();
            foreach (array_slice($entry['system']->criticals, $entry['before']) as $crit){
                $names[] = ($crit->description !== '') ? $crit->description : $crit->phpclass;
            }
            $text = empty($names) ? ' - no critical' : ' - critical: ' . implode(', ', array_unique($names));
            $entry['order']->pubnotes = str_replace($entry['token'], $text, $entry['order']->pubnotes);
        }
        self::$pendingRolls = array();
    }

    /* ForcedOfflineOneTurn stamped $stamp: isOfflineOnTurn() reports it on $stamp+1 only. Built directly rather than
       through addCritical(), which stamps the gamedata's turn and, on a Reactor, cascades a blackout. */
    private static function forceOffline($unit, $system, $stamp){
        $crit = new ForcedOfflineOneTurn(-1, $unit->id, $system->id, "ForcedOfflineOneTurn", $stamp);
        $crit->updated = true;
        $crit->newCrit = true;
        $system->criticals[] = $crit;
    }

    /* The unit's outer sections with Structure still standing, each with its direction (D16): the width-weighted
       circular mean of the arcs getLocations() gives it - so an MCV's fore is 0 and its aft 180, a capital's four
       sides 0 / 90 / 180 / 270. Primary is never a side. */
    private static function liveSides($unit){
        $byLoc = array();
        foreach ($unit->getLocations() as $arc){
            $loc = (int)$arc['loc'];
            if ($loc === 0) continue;
            $width = ((int)$arc['max'] - (int)$arc['min'] + 360) % 360;
            if ($width === 0) $width = 360;
            $mid = deg2rad(((int)$arc['min'] + $width / 2) % 360);
            if (!isset($byLoc[$loc])) $byLoc[$loc] = array('x' => 0.0, 'y' => 0.0);
            $byLoc[$loc]['x'] += cos($mid) * $width;
            $byLoc[$loc]['y'] += sin($mid) * $width;
        }

        $sides = array();
        foreach ($byLoc as $loc => $vector){
            //"do not count a destroyed side". An MCV's fore and aft have no Structure of their own - getStructureSystem
            //answers with Primary's, which is also where a hit on them goes.
            $structure = $unit->getStructureSystem($loc);
            if (!$structure || $structure->isDestroyed()) continue;
            $bearing = (int)round(rad2deg(atan2($vector['y'], $vector['x'])));
            $sides[] = array('loc' => $loc, 'bearing' => (($bearing % 360) + 360) % 360);
        }
        return $sides;
    }

    //⚠️ 0..359 only: isInArc(360, 0, 180) is false (arch_server_bearing_360).
    private static function arcCovers($system, $bearing){
        return mathlib::isInArc($bearing, $system->startArc, $system->endArc);
    }

    /* A point ten hexes out on the unit's RELATIVE bearing $bearing - the synthetic source getHitSystem takes to
       prefer the systems that face the struck side, as a real shot from there would. getBearingOnPos's inverse:
       compass headings run clockwise from the facing (getCompassHeadingOfPoint negates atan2), and a rolled ship
       mirrors its relative bearings. */
    private static function pointOnBearing($unit, $bearing){
        $relative = Movement::isRolled($unit) ? (360 - $bearing) % 360 : $bearing;
        $heading = ($unit->getFacingAngle() + $relative) % 360;
        $pos = $unit->getCoPos();
        return mathlib::getPointInDirection(10, -$heading, $pos["x"], $pos["y"]);
    }

    /* The EM shields whose standing Storm Defence order is in force for the storm hitting $forTurn - as it stood when
       the last phase that could change it closed: Deployment of turn T for the Deployment storm, Initial Orders of
       turn N for the storm at the end of N. */
    private static function declaredShields($unit, $forTurn, $declPhase){
        $declTurn = ($declPhase === -1) ? $forTurn : $forTurn - 1;
        $list = array();
        foreach ($unit->systems as $system){
            if (!($system instanceof EMShield)) continue;
            if ($system->isDestroyed()) continue;
            if ($system->isStormDefenceActive($declTurn, $declPhase)) $list[] = $system;
        }
        return $list;
    }

    /* ================================================================ helpers ============= */

    /* Where the unit's record lives: its CnC, or a flight's sample fighter - where getCommonIniModifiers reads
       initiative crits. A hull with neither keeps it on its first system (the initiative is then not read, but the
       sweep still knows the unit is done). */
    private static function markerSystem($unit){
        if ($unit instanceof FighterFlight) return $unit->getSampleFighter();
        $cnc = $unit->getSystemByName("CnC");
        if ($cnc) return $cnc;
        foreach ($unit->systems as $system) return $system;
        return null;
    }

    //A DIRECT SCAN, not hasCritical(): a oneturn crit is reported only on turn+1 (EdfExposure::alreadyResolvedThisTurn).
    private static function alreadyResolved($marker, $stamp){
        foreach ($marker->criticals as $crit){
            if ($crit->phpclass === 'EMStormInitiative' && (int)$crit->turn === (int)$stamp) return true;
        }
        return false;
    }

    //The first system of a class (instanceof - Scanner takes ElintScanner too) that is still standing.
    private static function liveSystem($unit, $class){
        foreach ($unit->systems as $system){
            if ($system instanceof $class && !$system->isDestroyed()) return $system;
        }
        return null;
    }

    private static function addParamCrit($system, $unit, $phpclass, $amount, $stamp){
        $crit = new $phpclass(-1, $unit->id, $system->id, $phpclass, $stamp, 0, false, (int)$amount);
        $crit->updated = true;
        $crit->newCrit = true; //force save: a oneturn crit is not "for the current turn"
        $system->criticals[] = $crit;
    }

    /* One combat-log line per unit (D15): a technical fire order on the unit's own Ramming Attack, at itself, rolled 1
       (the printed log drops rolled 0), 0 shots - the Lucky Captain's and the EDF's shape (howto_create_fire_orders).
       Firing::isHyperspaceLogOrder keeps it out of the firing: one written at Deployment carries the current turn.
       Returns the order (null with no host), so the unit's forced critical rolls can report into it. */
    private static function logResult($unit, $gamedata, $stamp, $parts){
        $host = $unit->getSystemByName('RammingAttack');
        if (!$host) foreach ($unit->systems as $system) if ($system instanceof Weapon){ $host = $system; break; }
        if (!$host) return null;

        $order = new FireOrder(-1, 'normal', $unit->id, $unit->id, $host->id, -1, (int)$gamedata->turn, 1,
            0, 1, 0, 0, 0, 0, 0, self::LOG_CLASS, 10000);
        $order->pubnotes = '<span class="shiplink" data-id="' . (int)$unit->id . '">' . $unit->name . '</span>'
            . ' - turn ' . ($stamp + 1) . ': ' . implode(', ', $parts) . '.';
        $order->addToDB = true;
        $host->fireOrders[] = $order;
        return $order;
    }
}
