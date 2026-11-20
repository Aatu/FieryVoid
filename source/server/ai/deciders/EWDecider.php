<?php
/**
 * CPU opponent - electronic warfare, in Initial Orders (TACTICAL_AI_PLAN.md section 5.3, stage T3).
 *
 * ⚠️ EW LEGALITY IS THE CLIENT'S, as movement and firing legality are. EW::validateEW answers true
 * to anything, so "more points than the scanner gives", a Restricted EW critical, an LCV's sensors
 * and "who may be locked on to" are all asked by the page alone (ew.js, gamedata.js at commit, the
 * Initial Orders menu). points() and the limits in allocate() are a PORT of those, and
 * tests/ai/ordersOracle.js clicks every row the bot posts through the real ew.js.
 *
 * THE DECISION, per ship. Every point starts as defence (DEW - which is what an unspent point is
 * anyway). One point at a time moves to the offensive use that gains most, while the gain is
 * positive, in named terms (principle P7):
 *
 *   offence  what the ship's loaded guns should do: each gun counted against the enemy it does
 *            most to, with the OEW held on that enemy (CCEW too, on a flight);
 *   defence  what the enemy's guns should do to this ship at its DEW - each gun by the share of
 *            its fire this ship would draw among the bot's units (it fires at one of them).
 *
 * The user's rulings of 2026-11-19 (game 4750, where both Omegas went to 0 DEW at 1-4 hexes) shape
 * it further: within EW_CLOSE_RANGE a lock stays soft (EW_SOFT_LOCK) and, with an enemy SHIP that
 * close, at least EW_CLOSE_MIN_DEW of the points stay in DEW; a ship that can fire at fighters puts
 * up 1 point of CCEW as a deterrent whenever an enemy flight could come within its 10 hexes this
 * turn, and no more than EW_CCEW_CAP unless the enemy is fighter-heavy or the ship's guns are mostly
 * anti-fighter guns.
 *
 * Both are the REAL hit formula (Weapon::calculateHitBase, through AIView::estimate) asked with
 * the EW tried - never a formula of the bot's own. Two things are guessed, as a player guesses them:
 *   - where the units will be when the guns fire: the bot's ships are looked at where flying
 *     straight on takes them (which is what MoveDecider does unless something is in the way),
 *     the enemy where it stands, and arcs are not asked - facings change in Movement;
 *   - the enemy's EW, which is hidden until both sides have committed: each enemy is taken to
 *     repeat LAST turn's rows (remembered() - they were public then; the mask wipes them now).
 * And one thing is known and used as a player uses it: the turn's INITIATIVE order. Hard locks go
 * on enemies that move before the ship (beyond close range), soft ones (AIConfig::EW_SOFT_LOCK) on
 * those that move after.
 *
 * LEVELS (stage T5f): Easy splits its points half OEW on the nearest enemy, half DEW, and nothing else (the skill
 * 'simpleEW'); the CCEW deterrent is a skill of its own ('ccewDeterrent').
 *
 * NOT YET: ELINT functions (stage T7 - an ELINT ship just runs OEW / DEW), Detect Stealth / Detect
 * Mines, EW for flights.
 */
class EWDecider
{
    //What the last plan did, for tac_ai_log.
    public static $last = array('ships' => 0, 'points' => 0, 'offensive' => 0, 'targets' => 0);

    /* The enemy's EW rows of LAST turn: unit id => list of array(type, amount, targetid). Ask it
       BEFORE the load is masked. Only the turn before this one is read - rows that were on every
       player's screen - never this turn's, which an enemy who committed first already has. */
    public static function remembered(TacGamedata $gamedata, $botId)
    {
        $team = $gamedata->getPlayerTeam();
        $before = (int)$gamedata->turn - 1;
        $rows = array();

        foreach ($gamedata->ships as $unit) {
            if ((int)$unit->userid === (int)$botId || $unit->team == $team) continue;
            foreach ($unit->EW as $entry) {
                if ((int)$entry->turn !== $before) continue;
                $rows[(int)$unit->id][] = array($entry->type, (int)$entry->amount, (int)$entry->targetid);
            }
        }

        return $rows;
    }

    /* The bot's EW rows for this turn: ship id => list of AIOrderBuilder::ew rows, DEW last, as
       the page posts them. $view is the bot's MASKED load. */
    public static function plan(AIView $view, array $remembered = array())
    {
        $gamedata = $view->gamedata;
        $turn = $view->turn;
        $plan = array();
        self::$last = array('ships' => 0, 'points' => 0, 'offensive' => 0, 'targets' => 0);

        //"No ships can use EW of any kind": the page writes no row at all in a nebula
        if (TacGamedata::$nebulaPresent) return $plan;

        $ships = array();
        foreach (AIFallback::ownUnits($gamedata, $view->botId) as $ship) {
            if ($ship instanceof FighterFlight || $ship->isDestroyed() || $ship->isTerrain()) continue;
            if ($ship->getTurnDeployed($gamedata) > $turn) continue;
            $ships[] = $ship;
        }
        if (count($ships) == 0) return $plan;

        //Whom the page lets a ship lock on to: a targetable enemy that is not riding a docking bay.
        $enemies = array();
        foreach ($view->enemies() as $enemy) {
            if (HangarOps::bayCarrierAttachedTo($enemy, $gamedata) !== null) continue;
            $enemies[] = $enemy;
        }

        //The enemy's EW as it was last turn, stood in for this turn's.
        foreach ($enemies as $enemy) {
            if (!isset($remembered[(int)$enemy->id])) continue;
            foreach ($remembered[(int)$enemy->id] as $row) {
                $enemy->EW[] = new EWentry(-1, $enemy->id, $turn, $row[0], $row[1], $row[2]);
            }
        }

        /* Is the enemy weighted towards fighters (EW_FIGHTER_HEAVY of its visible value in flights)? Then CCEW may go past
           EW_CCEW_CAP on any ship (the user's ruling, 2026-11-19). */
        $flightValue = 0;
        $enemyValue = 0;
        foreach ($enemies as $enemy) {
            $value = $view->value($enemy);
            $enemyValue += $value;
            if ($enemy instanceof FighterFlight) $flightValue += $value;
        }
        $fighterHeavy = $enemyValue > 0 && $flightValue >= AIConfig::EW_FIGHTER_HEAVY * $enemyValue;

        //The bot's own units where flying straight on takes them - and where they stand now. Taken back below.
        $moved = array();
        $mine = $view->myUnits();
        $now = array();
        foreach ($mine as $unit) {
            $now[(int)$unit->id] = $unit->getHexPos();
            $row = self::ahead($unit, $turn);
            if ($row === null) continue;
            $unit->movement[] = $row;
            $moved[] = $unit;
        }

        $kept = array();
        foreach ($ships as $ship) $kept[(int)$ship->id] = $ship->EW;

        try {
            $locked = array();
            foreach ($ships as $ship) {
                $here = isset($now[(int)$ship->id]) ? $now[(int)$ship->id] : $ship->getHexPos();
                $alloc = self::allocate($view, $ship, $enemies, $mine, $here, $fighterHeavy);
                $rows = array();
                foreach ($alloc['OEW'] as $targetId => $amount) {
                    if ($amount <= 0) continue;
                    $rows[] = AIOrderBuilder::ew($ship->id, $turn, 'OEW', $amount, $targetId);
                    $locked[$targetId] = true;
                    self::$last['offensive'] += $amount;
                }
                if ($alloc['CCEW'] > 0) {
                    $rows[] = AIOrderBuilder::ew($ship->id, $turn, 'CCEW', $alloc['CCEW']);
                    self::$last['offensive'] += $alloc['CCEW'];
                }
                $rows[] = AIOrderBuilder::ew($ship->id, $turn, 'DEW', $alloc['DEW']); //the page writes this row at commit, 0 included
                $plan[(int)$ship->id] = $rows;
                self::$last['ships']++;
                self::$last['points'] += $alloc['points'];
            }
            self::$last['targets'] = count($locked);
        } finally {
            foreach ($moved as $unit) array_pop($unit->movement);
            foreach ($ships as $ship) $ship->EW = $kept[(int)$ship->id];
        }

        return $plan;
    }

    /* One ship's split: array('points', 'DEW', 'CCEW', 'OEW' => target id => amount). $units: the bot's units on the
       board (where flying straight on takes them); $here: where this ship stands now; $fighterHeavy: see plan(). */
    private static function allocate(AIView $view, $ship, array $enemies, array $units, OffsetCoordinate $here, $fighterHeavy)
    {
        $turn = $view->turn;
        $points = self::points($ship, $turn);
        $alloc = array('points' => $points, 'DEW' => $points, 'CCEW' => 0, 'OEW' => array());
        if ($points <= 0 || count($enemies) == 0) return $alloc;

        //Units that keep everything on defence: the page gives them no offensive EW, or the bot has no handler for their state (P4).
        if ($view->unitBlock($ship) !== null || !empty($ship->getElintModules())) return $alloc;
        if (HangarOps::bayCarrierAttachedTo($ship, $view->gamedata) !== null) return $alloc;

        //ew.checkRestrictedEW: with that critical on the C&C, at least half the output stays defensive.
        $cnc = self::firstNamed($ship, 'cnC');
        $minDew = ($cnc && empty($ship->osat) && PowerDecider::crits($cnc, 'RestrictedEW', $turn) > 0) ? (int)ceil($points / 2) : 0;
        //ew.checkLCVSensors: all but 2 points go to OEW - CCEW does not count as OEW.
        $lcv = $ship->hasSpecialAbility('LCVSensors') && $points > 2;

        /* How near each enemy is: the nearer of where the ship stands and where flying straight on takes it (the bot has
           not moved yet), to where the enemy stands. */
        $ahead = $ship->getHexPos();
        $near = array();
        foreach ($enemies as $enemy) {
            $at = $enemy->getHexPos();
            $near[(int)$enemy->id] = min($here->distanceTo($at), $ahead->distanceTo($at));
        }

        //nearest first, so that equal gains go to the nearest
        $from = $ship->getHexPos();
        usort($enemies, function ($a, $b) use ($from) {
            $da = $from->distanceTo($a->getHexPos());
            $db = $from->distanceTo($b->getHexPos());
            return ($da == $db) ? $a->id - $b->id : (($da < $db) ? -1 : 1);
        });

        /* EASY (stage T5f, plan 5.8): half the points OEW on the nearest enemy, half DEW - nothing weighed, no CCEW. The page's
           own limits still hold: a Restricted EW critical's half stays DEW; an LCV keeps its rule below. */
        if ($view->skill('simpleEW') && !$lcv) {
            $oew = max(0, min(intdiv($points, 2), $points - $minDew));
            if ($oew > 0) {
                $alloc['OEW'][(int)$enemies[0]->id] = $oew;
                $alloc['DEW'] -= $oew;
            }
            return $alloc;
        }

        $guns = array();
        foreach ($view->mounts($ship) as $mount) {
            $weapon = $mount['weapon'];
            if (!$view->plainGun($weapon) || $weapon->isOfflineOnTurn($turn) || !$view->loaded($weapon)) continue;
            $guns[] = $weapon;
        }

        /* CCEW (user ruling, 2026-11-19). The deterrent: a ship with a gun that can fire at fighters puts up 1 point when an
           enemy flight could be within CCEW's reach at this turn's Fire phase (EW_CCEW_REACH + the flight's speed). More only
           while it gains, and never past EW_CCEW_CAP - unless the enemy is fighter-heavy or most of this ship's guns are
           anti-fighter guns (fire control against fighters at least as good as against capital ships). */
        $flights = false;
        $deterrent = false;
        foreach ($enemies as $enemy) {
            if (!($enemy instanceof FighterFlight)) continue;
            $flights = true;
            $last = $enemy->getLastMovement();
            if ($near[(int)$enemy->id] <= AIConfig::EW_CCEW_REACH + ($last ? max(0, (int)$last->speed) : 0)) $deterrent = true;
        }
        $antiFighter = 0;
        $plain = 0;
        foreach ($view->mounts($ship) as $mount) {
            $weapon = $mount['weapon'];
            if (!$view->plainGun($weapon) || $weapon->isOfflineOnTurn($turn)) continue;
            $fc = $weapon->fireControl;
            if (!isset($fc[0]) && !isset($fc[1]) && !isset($fc[2])) continue; //no offensive fire at all (an Interceptor)
            $plain++;
            //an anti-fighter gun aims better at fighters than at capital ships: a Twin Array (6/5/4) is one, a Standard Particle Beam (4/4/4) is not
            if (isset($fc[0]) && (!isset($fc[2]) || $fc[0] > $fc[2])) $antiFighter++;
        }
        $canShootFighters = false;
        foreach ($guns as $weapon) {
            if (isset($weapon->fireControl[0])) $canShootFighters = true;
        }
        $ccewCap = ($fighterHeavy || ($plain > 0 && 2 * $antiFighter >= $plain)) ? PHP_INT_MAX : AIConfig::EW_CCEW_CAP;

        /* CLOSE ENGAGEMENT (user ruling, 2026-11-19): an enemy within EW_CLOSE_RANGE gets a soft lock at most, and with an
           enemy SHIP that close at least EW_CLOSE_MIN_DEW of the points stay in DEW. (An LCV has the page's own rule.) */
        $close = array();
        $engaged = false;
        foreach ($enemies as $enemy) {
            if ($near[(int)$enemy->id] > AIConfig::EW_CLOSE_RANGE) continue;
            $close[(int)$enemy->id] = true;
            if (!($enemy instanceof FighterFlight) && empty($enemy->mine)) $engaged = true;
        }
        if ($engaged && !$lcv) $minDew = max($minDew, (int)ceil($points * AIConfig::EW_CLOSE_MIN_DEW));

        $base = array();
        foreach ($ship->EW as $entry) {
            if ((int)$entry->turn !== $turn) $base[] = $entry;
        }
        /* INITIATIVE (user, 2026-11-15). The initiative order is on the board before EW is set. An
           enemy that moves BEFORE this ship will be where the ship can see it when the ship
           moves, so its guns can be brought to bear: a hard lock is worth having. One that moves
           AFTER it (or with it) may leave the arcs again: its fire counts for less
           (EW_MOVES_AFTER_WEIGHT) and the lock stays soft (EW_SOFT_LOCK points). An enemy that
           cannot move at all counts as having moved. */
        $soft = array();
        foreach ($enemies as $enemy) {
            if (!empty($enemy->base) || !empty($enemy->smallBase) || !empty($enemy->osat) || !empty($enemy->mine)) continue;
            if ($enemy->iniative >= $ship->iniative) $soft[(int)$enemy->id] = true;
        }
        $capped = $soft + $close; //a soft lock at most: moving after the ship, or close

        $state = array('view' => $view, 'ship' => $ship, 'base' => $base, 'guns' => $guns, 'enemies' => $enemies,
            'units' => $units, 'threats' => null, 'offence' => array(), 'defence' => array(), 'soft' => $soft);

        //the CCEW deterrent first: a point of DEW the greedy below does not have to find a gain for (a level's skill, stage T5f)
        if ($deterrent && $canShootFighters && !$lcv && $alloc['DEW'] > $minDew && $view->skill('ccewDeterrent')) {
            $alloc['DEW']--;
            $alloc['CCEW']++;
        }

        $current = self::score($state, $alloc);
        while ($alloc['DEW'] > $minDew) {
            $best = null;
            $bestScore = $current + AIConfig::EW_MIN_GAIN;
            foreach (self::moves($alloc, $enemies, $flights && !$lcv && $alloc['CCEW'] < $ccewCap, $capped) as $try) {
                $score = self::score($state, $try);
                if ($score > $bestScore) {
                    $bestScore = $score;
                    $best = $try;
                }
            }
            if ($best === null) break;
            $alloc = $best;
            $current = $bestScore;
        }

        //An LCV has no choice: what is left above 2 goes to OEW, wherever it does most (the nearest, when nothing does anything).
        while ($lcv && $alloc['DEW'] > 2) {
            $best = null;
            $bestScore = null;
            foreach (self::moves($alloc, $enemies, false) as $try) {
                $score = self::score($state, $try);
                if ($bestScore === null || $score > $bestScore) {
                    $bestScore = $score;
                    $best = $try;
                }
            }
            if ($best === null) break;
            $alloc = $best;
        }

        return $alloc;
    }

    /* Every split one point of DEW away from $alloc. $capped: enemy id => true for those a lock stays
       soft on (no more than EW_SOFT_LOCK points); empty where the ship has no choice (an LCV). */
    private static function moves(array $alloc, array $enemies, $ccew, array $capped = array())
    {
        $list = array();
        foreach ($enemies as $enemy) {
            $has = isset($alloc['OEW'][(int)$enemy->id]) ? $alloc['OEW'][(int)$enemy->id] : 0;
            if (isset($capped[(int)$enemy->id]) && $has >= AIConfig::EW_SOFT_LOCK) continue;
            $try = $alloc;
            $try['DEW']--;
            $try['OEW'][(int)$enemy->id] = $has + 1;
            $list[] = $try;
        }
        if ($ccew) {
            $try = $alloc;
            $try['DEW']--;
            $try['CCEW']++;
            $list[] = $try;
        }
        return $list;
    }

    //What a split is worth to the ship: expected damage dealt less expected damage taken.
    private static function score(array &$state, array $alloc)
    {
        $view = $state['view'];
        $ship = $state['ship'];
        $turn = $view->turn;

        $offence = 0;
        foreach ($state['guns'] as $weapon) {
            $best = 0;
            foreach ($state['enemies'] as $enemy) {
                $oew = isset($alloc['OEW'][(int)$enemy->id]) ? $alloc['OEW'][(int)$enemy->id] : 0;
                $ccew = ($enemy instanceof FighterFlight) ? $alloc['CCEW'] : 0;
                $key = $weapon->id . ':' . $enemy->id . ':' . $oew . ':' . $ccew;
                if (!isset($state['offence'][$key])) {
                    $rows = $state['base'];
                    if ($oew > 0) $rows[] = new EWentry(-1, $ship->id, $turn, 'OEW', $oew, $enemy->id);
                    if ($ccew > 0) $rows[] = new EWentry(-1, $ship->id, $turn, 'CCEW', $ccew, -1);
                    $ship->EW = $rows;
                    $state['offence'][$key] = $view->estimate($ship, $weapon, $enemy, false)
                        * (isset($state['soft'][(int)$enemy->id]) ? AIConfig::EW_MOVES_AFTER_WEIGHT : 1);
                }
                $best = max($best, $state['offence'][$key]);
            }
            $offence += $best;
        }

        /* The enemy guns that could reach this ship at all (asked once, with no DEW up), then at the DEW tried. Each gun
           fires at one target: it is counted for this ship by the share of what it could do to the bot's units that this
           ship would take (until 2026-11-19 every gun counted 1 / the number of the bot's units - its fighter flights
           included - which priced a point of DEW at a quarter in game 4750 and left both Omegas on 0 DEW). Only the guns
           loaded this turn count, as on the offence side. A mine's guns are not counted (Hazards prices a mine). */
        if ($state['threats'] === null) {
            $state['threats'] = array();
            $ship->EW = $state['base'];
            foreach ($state['enemies'] as $enemy) {
                if (!empty($enemy->mine)) continue;
                foreach ($view->mounts($enemy) as $mount) {
                    //a gun that fires this turn: loaded, as the ship's own are counted (an enemy's loading is on every player's screen)
                    if (!$view->plainGun($mount['weapon'], $mount['fighter']) || !$view->loaded($mount['weapon'])) continue;
                    $here = $view->estimate($enemy, $mount['weapon'], $ship, false);
                    if ($here <= 0) continue;
                    $all = 0;
                    foreach ($state['units'] as $unit) $all += ((int)$unit->id === (int)$ship->id) ? $here : $view->estimate($enemy, $mount['weapon'], $unit, false);
                    $state['threats'][] = array($enemy, $mount['weapon'], $all > 0 ? $here / $all : 1);
                }
            }
        }
        $dew = $alloc['DEW'];
        if (!isset($state['defence'][$dew])) {
            $rows = $state['base'];
            $rows[] = new EWentry(-1, $ship->id, $turn, 'DEW', $dew, -1);
            $ship->EW = $rows;
            $taken = 0;
            foreach ($state['threats'] as $threat) $taken += $view->estimate($threat[0], $threat[1], $ship, false) * $threat[2];
            $state['defence'][$dew] = $taken;
        }

        return AIConfig::EW_OFFENCE * $offence - AIConfig::EW_DEFENCE * $state['defence'][$dew];
    }

    /* The EW points the page gives this ship (ew.getScannerOutput): 0 for a ship adrift, else
       what its sensors put out now, less 2 for each Restricted EW and 1 for each "sensors down" on
       its primary C&C. ⚠️ Not EW::getScannerOutput: the two differ (the server calls a base or an
       OSAT with a disabled C&C sensorless, the page does not; "sensors down" the server counts too since
       2026-11-16), nothing on the server holds a commit to its own figure, and the row a
       player's page writes is the page's. */
    public static function points($ship, $turn)
    {
        if (empty($ship->osat) && empty($ship->base)) {
            foreach ($ship->systems as $system) {
                if (PowerDecider::crits($system, 'ShipDisabledOneTurn', $turn) > 0 || PowerDecider::crits($system, 'ShipDisabled', $turn) > 0) return 0;
            }
            $cnc = self::firstNamed($ship, 'cnC');
            if ($cnc && $cnc->isDestroyed()) return 0;
        }

        $output = 0;
        $primary = null;
        $bsgHybrid = null;
        foreach ($ship->systems as $system) {
            if ($system->outputType === 'EW') $output += max(0, (int)$system->getOutput($turn));
            //shipManager.getPrimaryCnC: the C&C with least left of it
            if ($system->displayName === 'C&C' && ($primary === null || $system->getRemainingHealth() < $primary->getRemainingHealth())) $primary = $system;
            if ($bsgHybrid === null && $system->name === 'BSGHybrid') $bsgHybrid = $system; //the page's getSystemByName: the first by name
        }
        if ($primary) $output -= 2 * PowerDecider::crits($primary, 'RestrictedEW', $turn) + PowerDecider::crits($primary, 'tmpsensordown', $turn);
        //a BSG hybrid's Sensor Loss, -3 each - the page subtracts it since 2026-11-16 (ew.js; it never did before), as the server does
        if ($bsgHybrid) $output -= 3 * PowerDecider::crits($bsgHybrid, 'SensorLoss', $turn);

        return max(0, $output);
    }

    //A unit one turn straight on from where it stands, as a movement row - or null for one that will not move.
    private static function ahead($unit, $turn)
    {
        return AIView::straightOn($unit, $turn);
    }

    //shipManager.systems.getSystemByName: the first system of that name.
    private static function firstNamed($ship, $name)
    {
        foreach ($ship->systems as $system) {
            if ($system->name === $name) return $system;
        }
        return null;
    }
}
