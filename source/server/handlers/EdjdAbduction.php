<?php
/**
 * Extra-Dimensional Jump Drive abductions — the Walkers of Sigma-957 hyperspace kidnap.
 * WALKERS_OF_SIGMA_PLAN.md section 3.18 (Stage 20), extended to TERRAIN by section 3.18b (2026-09-20).
 *
 * THE RULE, AS BUILT
 * An EDJD (JumpEngine::markExtraDimensional - Wanderer, Traveler, Waymarker, Guideship) declares an
 * abduction of an ENEMY unit in Initial Orders: a type-'ballistic' order, damageclass 'abduction',
 * whose firing mode is the power level. At the end of the Firing phase every declaration aimed at the
 * same target is resolved together:
 *
 *   - An EDJD TAKES HOLD (starts a chain) when the target ended its movement in an Energy Draining Field
 *     connected to the EDJD ship's own field, AND the ship's OEW on it is higher than the target's DEW
 *     including defensive ELINT support (SDEW, BDEW) - offensive ELINT is excluded. That first turn
 *     delivers NO power-turns (D64); power and supporting drives count from the next.
 *   - Once begun, the conditions are never checked again (D65): the chain continues while at least one
 *     EDJD that has held it on EVERY turn of it still has a working declaration ("as long as at least
 *     one has been affecting the target for the duration"). Otherwise the turn is a new attempt, judged
 *     on the conditions - a restart if an EDJD meets them, a collapse if none does.
 *   - Every WORKING declaration on a continuing turn contributes (D54): an EDJD its power level in
 *     power-turns, any other Walker hull drive half a power-turn for double power.
 *   - The cost is locked on the chain's first turn (Q12): ceil(ramming factor / 50), or / 10 against
 *     advanced armour, with ATTACHED units' ramming factors added - never what it carries inside it
 *     (user ruling 2026-09-13). At the cost, the target leaves through Movement::applyJumpOut - "as if it
 *     had left the game of its own accord".
 *   - A declaration whose drive is DESTROYED or DEACTIVATED by the time it resolves is CANCELLED (user
 *     ruling 2026-09-13): no power, a log line saying so, and no detonation roll.
 *
 * ⭐ TERRAIN CAN BE ABDUCTED TOO (§3.18b, user ruling 2026-09-20 - Stage 20's Q13 reopened). An asteroid,
 * a moon, a fixed jump gate or a shipyard is an object like any other and pays the same ceil(RF / 50);
 * a jump point is a hole in space and is refused (isAbductableTerrain); terrain belongs to nobody, so the
 * "enemy only" rule does not apply to it. The one rule that genuinely changes shape is the FIELD: a unit
 * standing in more than one hex - an irregular hexOffsets asteroid, or a moon with a Huge radius - needs
 * EVERY hex of its footprint inside the connected field, not just its centre (isInConnectedField).
 *
 * ⭐⭐ NO STORED STATE BUT ONE NOTE PER DRIVE PER TURN (D22). Each working declaration writes
 * `<targetId>:<halves>:<cost>:<since>:<anchor>` on its drive ('EDJD'). The chain is re-derived from
 * those notes on every load (getChains): the notes naming a target for the last resolved turn carry
 * the chain's start turn, and every turn from there must have notes naming it with that same start.
 * A gap needs no cleanup, a reload needs no sweep, and a replay reads what the dice-free resolution
 * wrote. An attempt that took no hold writes `<targetId>:0:0:0:0` - never part of a chain, and there
 * so a second run of the same turn's resolution finds the turn already done (idempotence).
 *
 * ⚠️ HALF power-turns are carried as INTEGERS ("halves") everywhere, server and client, so a Scribe's
 * half can never meet a float comparison. The cost is in whole power-turns; complete at halves >= 2 x cost.
 *
 * ⚠️ POWER IS NOT VALIDATED HERE, OR ANYWHERE ON THE SERVER (D46's precedent): each level costs the
 * drive's powerReq on the client's reactor balance (JumpEngine.getAbductionPowerDraw), which the
 * Initial Orders commit gate enforces. There is no server twin of the power balance to check against.
 */
class EdjdAbduction
{
    /* ================================================================ resolution ============ */

    /* Called from the end of Firing::fireWeapons, before the boost-jump sweep, behind the
       TacGamedata::$abductionCapable gate. */
    public static function resolve($gamedata)
    {
        $turn = (int)$gamedata->turn;

        //Every live declaration this turn, grouped by target.
        $byTarget = array();
        foreach ($gamedata->ships as $ship){
            if ($ship instanceof FighterFlight) continue;
            foreach ($ship->systems as $system){
                if (!($system instanceof JumpEngine) || !$system->canJoinAbduction()) continue;
                $fire = $system->getAbductionDeclaration($turn);
                if (!$fire) continue;

                //⚠️ IDEMPOTENT: a note for this turn means this turn's resolution has already run
                //and been persisted (every working declaration writes one - see the class comment).
                $notes = $system->getAbductionNotes();
                if (isset($notes[$turn])) return;

                $byTarget[(int)$fire->targetid][] = array('ship' => $ship, 'engine' => $system, 'fire' => $fire);
            }
        }
        if (empty($byTarget)) return;

        $priorChains = self::getChains($gamedata, $turn - 1);

        foreach ($byTarget as $targetId => $declarations){
            $target = $gamedata->getShipById($targetId);
            if (!self::isOnBoard($target, $gamedata)){
                /* Destroyed this turn (or never a legal target). D63: the attempt still happened, so every
                   declaring drive records it - a no-hold note - and starts its recharge from next turn. */
                foreach ($declarations as $d) self::writeNote($d, $targetId, 0, 0, 0, false, $gamedata);
                continue;
            }
            $prior = isset($priorChains[$targetId]) ? $priorChains[$targetId] : null;
            self::resolveTarget($target, $declarations, $prior, $gamedata);
        }
    }

    private static function resolveTarget($target, $declarations, $prior, $gamedata)
    {
        $turn = (int)$gamedata->turn;

        $working = array();
        $anchors = array();   //"shipId:systemId" => true, for every EDJD that holds the chain THIS turn

        foreach ($declarations as $d){
            /* ⭐ CANCELLED, NOT MERELY IDLE (user ruling 2026-09-13): a drive destroyed during this
               attempt - or deactivated, which the client already withdraws the order for, so only a
               forced shutdown or a stale POST reaches here - delivers nothing, and the player is told.
               A no-hold note is written so a second run of this turn's resolution stays a no-op. */
            $cancelled = self::getCancellationReason($d['ship'], $d['engine'], $gamedata);
            if ($cancelled !== null){
                self::writeNote($d, $target, 0, 0, 0, false, $gamedata);
                self::log($d['ship'], $target, "'s abduction of " . self::link($target) . " is cancelled - " . $cancelled . ".", $gamedata);
                continue;
            }
            if (!self::isDriveWorking($d['ship'], $d['engine'], $gamedata)) continue;

            $d['key'] = $d['ship']->id . ':' . $d['engine']->id;
            $d['halves'] = $d['engine']->getAbductionHalves($d['fire']);
            $working[] = $d;
        }
        if (empty($working)) return;

        /* ⭐⭐ D65 (user ruling 2026-09-13) - THE CONDITIONS ARE FOR TAKING HOLD ONLY. "The Energy Draining
           Field and more OEW than DEW conditions are only relevant for the initial targeting ... They should
           not be checked in subsequent turns after the abduction has begun." So a chain that stood last turn
           CONTINUES while at least one EDJD that has held it since it began is still applying power - a
           working declaration is all it takes, wherever the target moved and whatever the EW. Only when no
           such EDJD is left does the turn become a new attempt, judged on the conditions (a restart). */
        if ($prior){
            foreach ($working as $d){
                if ($d['engine']->isExtraDimensional() && isset($prior['anchors'][$d['key']])) $anchors[$d['key']] = true;
            }
        }
        $continuing = !empty($anchors);

        $refusals = array();
        if (!$continuing){
            foreach ($working as $d){
                if (!$d['engine']->isExtraDimensional()) continue;
                $why = self::getConditionBlock($d['ship'], $target, $gamedata);
                if ($why === null) $anchors[$d['key']] = true;
                else $refusals[] = $why;
            }
        }

        $logShip = $working[0]['ship'];
        foreach ($working as $d){
            if (isset($anchors[$d['key']])){ $logShip = $d['ship']; break; }
        }

        //NO HOLD - nothing continued it and no EDJD met both conditions. Recorded (idempotence) and explained.
        if (empty($anchors)){
            foreach ($working as $d) self::writeNote($d, $target, 0, 0, 0, false, $gamedata);

            if (!empty($refusals)) $reason = $refusals[0];
            elseif ($prior) $reason = "no Extra-Dimensional Jump Drive that began the abduction is still applying power";
            else $reason = "only an Extra-Dimensional Jump Drive can begin an abduction";
            $text = "fails to take hold of " . self::link($target) . " - " . $reason . ".";
            if ($prior) $text .= " The abduction collapses (" . self::formatHalves($prior['total']) . " of " . $prior['cost'] . " power-turns lost).";
            self::log($logShip, $target, $text, $gamedata);
            return;
        }

        if ($continuing){
            $since = $prior['since'];
            $cost  = $prior['cost'];
            $total = $prior['total'];
        }else{
            $since = $turn;
            $cost  = self::getCost($target, $gamedata);   //LOCKED here, for the life of the chain (Q12)
            $total = 0;
        }

        /* ⭐ D64 (user ruling 2026-09-13, play test) - THE FIRST TURN ONLY TAKES HOLD. "Power Turns should not
           start accumulating until the turn after the initial targeting has been successful": a turn that
           STARTS a chain (a fresh one, or a restart after the old one lapsed) establishes it - cost locked,
           anchors recorded - and delivers nothing, whatever power was declared. Power counts from the next
           turn, which is also the first turn a supporting drive may join (getDeclarationBlock). So an
           abduction can never complete on the turn it began. */
        foreach ($working as $d){
            $halves = $continuing ? $d['halves'] : 0;
            $total += $halves;
            self::writeNote($d, $target, $halves, $cost, $since, isset($anchors[$d['key']]), $gamedata);
        }

        if (!$continuing){
            $text = "takes hold of " . self::link($target) . " - the abduction begins: power can be applied from next turn, and "
                . $cost . " power-turns will remove it to hyperspace.";
            if ($prior) $text .= " The earlier abduction had lapsed (" . self::formatHalves($prior['total']) . " of " . $prior['cost'] . " power-turns lost).";
            self::log($logShip, $target, $text, $gamedata);
            return;
        }

        $done = ($total >= 2 * $cost);
        $text = "drags " . self::link($target) . " into hyperspace: " . self::formatHalves(min($total, 2 * $cost))
            . " of " . $cost . " power-turns (Turn " . ($turn - $since + 1) . " of the abduction)";
            //. ($done ? " - the abduction is complete." : "."); //Unnecesary info
        self::log($logShip, $target, $text, $gamedata);

        if ($done) self::abduct($target, $logShip, $gamedata);
    }

    /* Take the target - and whatever is attached to it - out of the battle. Movement::applyJumpOut writes
       the three records a departure through a jump point writes (log order, 'jumped' CV note, structure
       destroyed as a HyperspaceJump), which is the rule's "as if it had left the game of its own accord".
       Units stowed INSIDE it go with their carrier by the existing carrier-jump handling; attached ones
       have to be taken explicitly, exactly as Movement::resolveJumpOuts takes them. */
    private static function abduct($target, $shooter, $gamedata)
    {
        Movement::applyJumpOut($target, $gamedata,
            " is dragged into hyperspace by " . self::link($shooter) . "'s Extra-Dimensional Jump Drive.");

        if (!empty($target->hasAttached)){
            foreach (array_keys($target->hasAttached) as $attachedId){
                $attached = $gamedata->getShipById((int)$attachedId);
                if (!$attached || $attached->isDestroyed()) continue;
                Movement::applyJumpOut($attached, $gamedata, " is carried into hyperspace with " . self::link($target) . ".");
            }
        }
    }

    private static function writeNote($declaration, $target, $halves, $cost, $since, $anchor, $gamedata)
    {
        $engine = $declaration['engine'];
        $engine->addIndividualNote(new IndividualNote(
            -1, $gamedata->id, $gamedata->turn, $gamedata->phase,
            $declaration['ship']->id, $engine->id,
            JumpEngine::ABDUCTION_NOTE, JumpEngine::ABDUCTION_NOTE,
            (int)(is_object($target) ? $target->id : $target) . ':' . (int)$halves . ':' . (int)$cost . ':' . (int)$since . ':' . ($anchor ? 1 : 0)
        ));
    }

    /* ================================================================ the chain ============= */

    /* Every chain still standing after $asOfTurn's resolution, keyed by target id:
       array(since, total (halves), cost, anchors => array("shipId:systemId" => true)).
       Gated on TacGamedata::$abductionPresent - no note restored this load, no chain anywhere. */
    public static function getChains($gamedata, $asOfTurn)
    {
        $chains = array();
        if (!TacGamedata::$abductionPresent) return $chains;
        $asOfTurn = (int)$asOfTurn;

        $byTarget = array();   //targetId => turn => list of notes
        foreach ($gamedata->ships as $ship){
            if ($ship instanceof FighterFlight) continue;
            foreach ($ship->systems as $system){
                if (!($system instanceof JumpEngine)) continue;
                foreach ($system->getAbductionNotes() as $noteTurn => $note){
                    if ($note['since'] <= 0) continue;   //an attempt that took no hold
                    $note['key'] = $ship->id . ':' . $system->id;
                    $byTarget[$note['target']][(int)$noteTurn][] = $note;
                }
            }
        }

        foreach ($byTarget as $targetId => $turns){
            if (!isset($turns[$asOfTurn])) continue;   //nothing delivered on the last turn: no chain
            $since = $turns[$asOfTurn][0]['since'];
            $cost  = $turns[$asOfTurn][0]['cost'];

            $total = 0;
            $anchors = null;
            $whole = true;
            for ($t = $since; $t <= $asOfTurn; $t++){
                $turnAnchors = array();
                $found = false;
                foreach (isset($turns[$t]) ? $turns[$t] : array() as $note){
                    if ($note['since'] !== $since) continue;
                    $found = true;
                    $total += $note['halves'];
                    if ($note['anchor']) $turnAnchors[$note['key']] = true;
                }
                if (!$found){ $whole = false; break; }   //cannot happen if the notes were written by resolve()
                $anchors = ($anchors === null) ? $turnAnchors : array_intersect_key($anchors, $turnAnchors);
            }
            if (!$whole || empty($anchors)) continue;

            $chains[$targetId] = array('since' => $since, 'total' => $total, 'cost' => $cost, 'anchors' => $anchors);
        }
        return $chains;
    }

    /* ================================================================ the conditions ======== */

    /* Is this declaration's drive delivering power this turn? The ship is on the board, the drive is
       intact (its own boxes and its section, this turn) and powered, and the unit is not jumping out -
       a Walker drive's boost IS its jump, and it cannot pour the same power into two things. Shared with
       JumpEngine::rollAbductionJumpFailure, which is what defines "active" for the detonation roll. */
    public static function isDriveWorking($ship, $engine, $gamedata)
    {
        $turn = (int)$gamedata->turn;
        if (!$ship || $ship instanceof FighterFlight) return false;
        if ($ship->isDestroyed()) return false;                          //also a DOCKED ship (removed)
        if (!$engine->canJoinAbduction()) return false;
        if ($engine->getRemainingHealth() <= 0) return false;
        $host = $ship->getStructureSystem($engine->location);
        if ($host && $host->isDestroyed($turn)) return false;
        if ($engine->isOfflineOnTurn($turn)) return false;
        if (JumpEngine::getUnitJumpingEngine($ship, $turn) !== null) return false;
        return true;
    }

    /* Why a declaration is CANCELLED, or null: its drive was destroyed (its own boxes, or its section this
       turn) or is offline. A whole ship destroyed is not reported here - its wreck says enough - and a unit
       jumping out is simply not working (isDriveWorking), the client having withdrawn the order already. */
    private static function getCancellationReason($ship, $engine, $gamedata)
    {
        $turn = (int)$gamedata->turn;
        if (!$ship || $ship->isDestroyed()) return null;
        if ($engine->getRemainingHealth() <= 0) return "its jump drive was destroyed";
        $host = $ship->getStructureSystem($engine->location);
        if ($host && $host->isDestroyed($turn)) return "its jump drive was destroyed";
        if ($engine->isOfflineOnTurn($turn)) return "its jump drive is deactivated";
        return null;
    }

    /* Null when both conditions hold for this EDJD ship, otherwise the reason for the log.
       ⭐ §3.18b - BOTH CONDITIONS ARE ASKED OF TERRAIN TOO (user ruling 2026-09-20). Terrain carries no
       EW, so "more OEW than DEW" costs the Walker exactly one OEW point allocated to the rock - which
       the client has always allowed (shipTooltipInitialOrdersMenu's isTargetable + isEnemyEW both pass
       on terrain) and which is a real price, since a Walker's EW is a real budget. The field condition
       is where terrain actually differs, and the difference lives in isInConnectedField: a multi-hex
       unit needs its WHOLE footprint covered, not its centre hex. */
    private static function getConditionBlock($ship, $target, $gamedata)
    {
        if (!self::isInConnectedField($ship, $target, $gamedata)){
            //"end its movement" is meaningless for a rock, and a moon fails this on ONE uncovered hex.
            if ($target->isTerrain())
                return "not every hex it occupies is inside an Energy Draining Field connected to " . self::link($ship) . "'s own";
            return "it did not end its movement in an Energy Draining Field connected to " . self::link($ship) . "'s own";
        }

        $turn = (int)$gamedata->turn;
        $oew = $ship->getOEW($target, $turn);
        //"including defensive but not offensive ELINT support" - SDEW and BDEW in, SOEW out.
        $dew = $target->getDEW($turn) + EW::getSupportedDEW($gamedata, $target) + EW::getBlanketDEW($gamedata, $target);
        if (!($oew > $dew))
            return self::link($ship) . "'s OEW on it (" . $oew . ") is not higher than its DEW (" . $dew . ")";

        return null;
    }

    /* ⭐ "THE TARGET MUST END ITS MOVEMENT IN AN ENERGY DRAINING FIELD CONNECTED TO THE SHIP'S".
       The published map collapses overlapping fields into one hex entry deliberately (that IS the
       overlap rule), so it cannot say which source covers a hex - but the server-side $edfSources twin
       can, and that is all the SEED needs: every hex this ship projects into. From there a flood fill
       over the hexes this ship's TEAM fields reaches everything "extended through ED Mines or other
       ships" (plan §3.18 option (a) - no new publication, no widened payload).
       ⚠️ Seeded from the ship's OWN source hexes, never from its position: a Walker whose field is
       down, sitting in an ally's field, is not connected to a field of its own.

       ⭐⭐ §3.18b - EVERY HEX OF THE TARGET, NOT ITS CENTRE (user ruling 2026-09-20). A ship stands in one
       hex and the question was "did the fill reach it"; a moon stands in nineteen, and "the target ended
       its movement in an Energy Draining Field" means the field covers ALL of them. So the fill no longer
       stops at the first goal - it crosses each one off and succeeds only when the set is empty, which is
       the same answer as before for every single-hex target (the old early exit was an optimisation, not
       a rule) and the whole of the multi-hex rule for terrain. A footprint hex inside a DISCONNECTED field
       of the same team is still a miss: only popped hexes count, and only reachable hexes are popped. */
    public static function isInConnectedField($ship, $target, $gamedata)
    {
        $hexes   = $gamedata->edfHexes;
        $sources = $gamedata->edfSources;
        if (!is_array($hexes) || !is_array($sources)) return false;

        $goals = self::getFootprintKeys($target);
        if (empty($goals)) return false;
        $team = (int)$ship->team;

        $queue = array();
        $seen  = array();
        foreach ($sources as $key => $owners){
            if (!isset($owners[$ship->id])) continue;
            if (!isset($hexes[$key]['teams'][$team])) continue;
            $queue[] = $key;
            $seen[$key] = true;
        }

        while (!empty($queue)){
            $key = array_pop($queue);
            unset($goals[$key]);
            if (empty($goals)) return true;
            list($q, $r) = explode(',', $key);
            foreach (Mathlib::getNeighbouringHexes(new OffsetCoordinate((int)$q, (int)$r), 1) as $n){
                $nKey = $n['q'] . ',' . $n['r'];
                if (isset($seen[$nKey])) continue;
                if (!isset($hexes[$nKey]['teams'][$team])) continue;
                $seen[$nKey] = true;
                $queue[] = $nKey;
            }
        }
        return false;
    }

    /* §3.18b - every hex $target occupies, as a set of 'q,r' keys. One hex for anything with a crew;
       for terrain the WHOLE footprint, which is an irregular hexOffsets shape rotated to its facing or
       a circular Huge radius. RammingAttack::getTerrainOccupiedHexes is the one place in the game that
       knows that shape (the collision sweep, the PlanetCrackerBeam and the client's getVortexHexBlock
       all read it or mirror it), so this only keys what it returns.
       ⚠️ The centre is checked FIRST: getTerrainOccupiedHexes hands its centre hex to Mathlib on the
       irregular path, and a unit with no position at all would take it a null. */
    private static function getFootprintKeys($target)
    {
        $keys = array();
        $pos = $target->getHexPos();
        if (!$pos) return $keys;

        if ($target->isTerrain()){
            foreach (RammingAttack::getTerrainOccupiedHexes($target) as $hex){
                if (!$hex) continue;
                $keys[$hex->q . ',' . $hex->r] = true;
            }
            return $keys;
        }

        $keys[$pos->q . ',' . $pos->r] = true;
        return $keys;
    }

    /* On the board and a unit at all - shared by submit-time validation and resolution.
       ⭐ TERRAIN IS NOT REFUSED HERE ANY MORE (§3.18b, user request 2026-09-20). This asks only "is it a
       unit standing on the map"; WHAT may be dragged off it is isAbductableTerrain's question, and the
       two are asked separately so a vortex is refused with its own reason rather than as an absence. */
    private static function isOnBoard($unit, $gamedata)
    {
        if (!$unit) return false;
        if ($unit->isDestroyed()) return false;
        if ($unit->isReinforcement()) return false;
        try {
            if ($unit->getTurnDeployed($gamedata) > $gamedata->turn) return false;
        } catch (Exception $e) { /* no slot - treat as on the board */ }
        return true;
    }

    /* ⭐⭐ §3.18b - MAY THIS TERRAIN UNIT BE DRAGGED INTO HYPERSPACE? (user ruling 2026-09-20 - Q13, which
       Stage 20 answered "out of scope for now", reopened and answered yes.)

       An asteroid, a moon, a fixed jump gate and a shipyard are all OBJECTS: the drive that takes a
       battlecruiser out can take a rock out, and the cost needs no new formula - the user chose the
       ordinary ceil(RF / 50) over the rules table's "Asteroid, Moon, Planetoid: 10 x radius cubed",
       which FV cannot express (terrain has no radius property, only a Huge hex reach that is 0 on the
       three round asteroids). A JUMP POINT is not an object but a hole in space, so SpawnJumpPoint and
       its two subclasses - the exit and the phase-in doorway - are refused by name.

       ⚠️ The Energy Draining Mine's orb gets no line of its own: it is unTargetable, and that is the
       test. Asked with no arguments, which is all $unTargetable amounts to today (ShipClasses.php), so
       the orb stays out of the published cost list as well as out of a declaration. */
    public static function isAbductableTerrain($unit)
    {
        if (!$unit) return false;
        if ($unit instanceof SpawnJumpPoint) return false;   //covers SpawnJumpPointExit and ...PhaseIn
        return $unit->isTargetableBy();
    }

    /* ================================================================ the cost ============== */

    /* Power-turns to abduct $target. "Round all fractions up ... if units are docked or otherwise
       connected, add their ramming factors together. If the target vessel is equipped with advanced
       armor (or better), divide the ramming factor by 10 instead of 50."
       BaseShip::getRammingFactor sums the MAX structure of sections still standing as of last turn, so
       the figure moves only when a whole section is lost - and the chain locks it on its first turn anyway.
       ⭐ WHAT A UNIT CARRIES INSIDE IT NEVER COUNTS (user ruling 2026-09-13): no hangar flight, no Docking
       Bay ship, no rail LCV - only units ATTACHED to its hull. That is also what lets the published preview
       and the locked cost be ONE figure: bay contents are private logistics, and a cost that summed them
       would disclose a load the moment an abduction began. */
    public static function getCost($target, $gamedata)
    {
        $rf = self::unitRammingFactor($target);
        foreach (self::getAttachedUnits($target, $gamedata) as $unit){
            $rf += self::unitRammingFactor($unit);
        }
        $divisor = !empty($target->advancedArmor) ? 10 : 50;
        return max(1, (int)ceil($rf / $divisor));
    }

    //A flight's ramming factor is per craft (FighterFlight::getRammingFactor), so a flight is its live craft.
    private static function unitRammingFactor($unit)
    {
        if ($unit instanceof FighterFlight){
            $craft = 0;
            foreach ($unit->systems as $fighter){
                if (!$fighter->isDestroyed()) $craft++;
            }
            return $craft * (int)$unit->getRammingFactor();
        }
        return (int)$unit->getRammingFactor();
    }

    //Units attached to $target's hull (hasAttached) - the only "connected" units the cost counts.
    private static function getAttachedUnits($target, $gamedata)
    {
        $ids = array();
        if (!empty($target->hasAttached)){
            foreach (array_keys($target->hasAttached) as $id) $ids[(int)$id] = true;
        }
        unset($ids[(int)$target->id]);

        $units = array();
        foreach (array_keys($ids) as $id){
            $unit = $gamedata->getShipById($id);
            if (!$unit || $unit->isDestroyedByDamage()) continue;
            $units[] = $unit;
        }
        return $units;
    }

    /* ================================================================ submit-time =========== */

    /* The declaration's legality, from Firing::getVortexDeclarationBlock (Initial Orders only). Null when
       legal, otherwise a reason for the log - the order is then rejected and the rest of the submission
       stands. The two CONDITIONS are deliberately not judged here: they are about where the target ENDS
       its movement and the EW standing then, neither of which exists yet. */
    public static function getDeclarationBlock($fire, $weapon, $shooter, $gamedata, $fireOrders)
    {
        if (!$weapon->canJoinAbduction()) return "only a Walker ship's jump drive can take part in an abduction";
        if ($fire->type !== 'ballistic') return "an abduction is declared in Initial Orders";
        if ($shooter->isDestroyed()) return "the declaring unit is not on the board";
        if ($weapon->isDestroyed($gamedata->turn)) return "Jump Engine is destroyed";
        if ($weapon->isOfflineOnTurn($gamedata->turn)) return "Jump Engine is offline";
        //D63: a drive recharging after an abduction may still CONTINUE the one it delivered power to last turn.
        $charge = $weapon->getVortexRechargeLoad($gamedata->turn);
        $recharge = $weapon->getVortexRechargeTime();
        if ($charge < $recharge && !$weapon->isContinuingAbduction($gamedata->turn, $fire->targetid))
            return "Jump Engine is still recharging ($charge/$recharge)";

        $target = $gamedata->getShipById((int)$fire->targetid);
        if (!self::isOnBoard($target, $gamedata)) return "no unit on the board to abduct";
        /* ⭐ §3.18b - TERRAIN BELONGS TO NOBODY (user ruling 2026-09-20), which is the convention the whole
           client already runs on: gamedata.isMyorMyTeamShip answers false for EVERY terrain unit outside
           deployment, whoever bought it. So the team test is for units with a crew, and a Walker may drag
           its own fleet's asteroid out of the way as readily as the enemy's. Without this exception the
           client would offer the declaration and the server would silently reject it. */
        if (!$target->isTerrain() && $target->team == $shooter->team) return "only an enemy unit can be abducted";
        if ($target instanceof FighterFlight) return "a fighter flight cannot be abducted";
        if (!$target->isTargetableBy($shooter, $gamedata->turn)) return "that unit cannot be targeted";
        //§3.18b: terrain may be abducted, but a jump point is a hole in space - there is nothing to take hold of.
        if ($target->isTerrain() && !self::isAbductableTerrain($target)) return "a jump point cannot be abducted";

        $mode = (int)$fire->firingMode;
        if ($weapon->isExtraDimensional()){
            if ($mode < 1 || $mode > JumpEngine::ABDUCTION_MAX_POWER) return "illegal abduction power level $mode";
        }else{
            if ($mode !== 2) return "a supporting Walker drive applies double power and nothing else (power level $mode)";
            //D64: a supporting drive may only join an abduction a FRIENDLY EDJD has already taken hold of.
            if (!self::isHeldByTeam($target->id, $shooter->team, $gamedata))
                return "a supporting Walker drive can only join an abduction an Extra-Dimensional Jump Drive has already taken hold of";
        }

        //ONE ABDUCTION PER UNIT PER TURN - the first survives, as with the one-vortex rule.
        foreach ($fireOrders as $other){
            if ($other === $fire) break;
            if ($other->shooterid != $fire->shooterid) continue;
            if ((int)$other->turn !== (int)$gamedata->turn) continue;
            if (!empty($other->rejected)) continue;
            if ($other->damageclass === JumpEngine::ABDUCTION_CLASS) return "unit already declares an abduction this turn";
        }

        $fire->calledid = -1;   //the whole unit - an abduction has no called shot
        return null;
    }

    /* D64 - does a chain against $targetId stand as of LAST turn, anchored by a drive on $team? That is
       "the target has been successfully targeted by a friendly EDJD", which is what lets a supporting
       drive join. The anchors are "shipId:systemId" keys, and only an EDJD can be one. */
    public static function isHeldByTeam($targetId, $team, $gamedata)
    {
        $chains = self::getChains($gamedata, (int)$gamedata->turn - 1);
        if (!isset($chains[(int)$targetId])) return false;
        foreach (array_keys($chains[(int)$targetId]['anchors']) as $key){
            $anchorShip = $gamedata->getShipById((int)explode(':', $key)[0]);
            if ($anchorShip && (int)$anchorShip->team === (int)$team) return true;
        }
        return false;
    }

    /* ================================================================ publication =========== */

    /* TacGamedata::$abductions - the client's cost preview and every standing chain. Public to every
       viewer: a declaration is announced (it is a ballistic marker from Movement on), and what a chain
       has delivered is the resolution's own record. `costs` is exactly the figure a chain would lock this
       turn - getCost reads nothing a viewer may not see. */
    public static function publish($gamedata)
    {
        $costs = array();
        foreach ($gamedata->ships as $unit){
            if (!self::isOnBoard($unit, $gamedata)) continue;
            /* §3.18b: terrain now carries a cost like anything else, EXCEPT the two kinds that can never
               be a target - a jump point and the unTargetable orb. Terrain was excluded wholesale until
               2026-09-20 (isOnBoard's line), so this is an ADDITIVE change to the published payload:
               every key that was there before is still there and unchanged. */
            if ($unit->isTerrain() && !self::isAbductableTerrain($unit)) continue;
            $costs[(string)$unit->id] = self::getCost($unit, $gamedata);
        }

        $chains = array();
        foreach (self::getChains($gamedata, (int)$gamedata->turn - 1) as $targetId => $chain){
            if (!self::isOnBoard($gamedata->getShipById($targetId), $gamedata)) continue;
            $chains[(string)$targetId] = array('total' => $chain['total'], 'cost' => $chain['cost'], 'since' => $chain['since']);
        }

        //Objects, never arrays: keyed by unit id, and an empty PHP array encodes as JSON [] (plan trap 9).
        return array('costs' => (object)$costs, 'chains' => (object)$chains);
    }

    /* ================================================================ the log =============== */

    /* One combat-log row per target per turn, in EdfExposure's shape: Walker -> target, hosted on the
       Walker's RammingAttack, rolled 1 so the log prints it, shots 0 so it can never steal a real shot's
       damage, and damageclass 'Abduction', which weaponManager.doShortLogText prints as its sentence alone. */
    private static function log($shooter, $target, $text, $gamedata)
    {
        $host = $shooter->getSystemByName("RammingAttack");
        if (!$host){
            foreach ($shooter->systems as $system){
                if ($system instanceof Weapon){ $host = $system; break; }
            }
        }
        if (!$host) return;

        $fireOrder = new FireOrder(
            -1, "normal", $shooter->id, $target->id,
            $host->id, -1, $gamedata->turn, 1,
            0, 1, 0, 0, 0,
            0, 0, 'Abduction', 10000
        );
        //A possessive continues the name with no space ("Traveler#1's abduction ...").
        $fireOrder->pubnotes = "<br>EXTRA-DIMENSIONAL JUMP DRIVE: " . self::link($shooter) . ($text[0] === "'" ? "" : " ") . $text;
        $fireOrder->addToDB = true;
        $host->fireOrders[] = $fireOrder;
    }

    //A bare shiplink span - combatLog.colourShipLinksInNotes paints it in the READER's colours.
    private static function link($unit)
    {
        return '<span class="shiplink" data-id="' . (int)$unit->id . '">' . $unit->name . '</span>';
    }

    private static function formatHalves($halves)
    {
        $halves = (int)$halves;
        return ($halves % 2 === 0) ? (string)($halves / 2) : (intdiv($halves, 2) . '.5');
    }
}
