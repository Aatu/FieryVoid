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
 * The user's notes on game 5826 (2026-11-22) - two Omegas with heavy guns ready locked no Narn ship on turn 3 and only the
 * Va'Kar on turn 4 - shape it further:
 *   - a split is priced as the engine will see it, every OEW and CCEW row of it on the ship at once: an enemy ELINT's
 *     disruption is shared over all of them (EW::getDistruptionEW), so a lone point on a ship can buy no lock where the
 *     same point beside a CCEW row buys half of one (score());
 *   - where an enemy ELINT disrupts the ship, a step may move several points onto one enemy at once - enough to get past
 *     the disruption - so the lock behind a first point that buys nothing is still found (lockStep());
 *   - within EW_CLOSE_RANGE every enemy SHIP a loaded gun could reach gets a soft lock (the level's skill 'closeLocks');
 *   - and the user's rulings on those notes (same day): at 10-20 hexes no lock over half the points unless no return fire could
 *     reach the ship at all (skill 'mediumLocks'); no OEW on a fighter flight - CCEW covers them - unless the enemy has no ship left
 *     and the flight is beyond CCEW's reach (skill 'noFlightOEW').
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
 * LEVELS (stage T5f): Easy splits its points half offensive, half DEW, and weighs nothing (the skill 'simpleEW') - since game
 * 5826 its offensive half is the CCEW deterrent and OEW on the nearest SHIP, as Normal would lock fighters and ships; the CCEW
 * deterrent is a skill of its own ('ccewDeterrent').
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

    //Whom the page lets a ship lock on to: a targetable enemy that is not riding a docking bay.
    public static function lockable(AIView $view)
    {
        $enemies = array();
        foreach ($view->enemies() as $enemy) {
            if (HangarOps::bayCarrierAttachedTo($enemy, $view->gamedata) !== null) continue;
            $enemies[] = $enemy;
        }
        return $enemies;
    }

    /* Is the enemy weighted towards fighters - EW_FIGHTER_HEAVY or more of its visible value in flights? $enemies: the enemy units to
       weigh (plan(): every one the page lets a ship lock on to). The CCEW rule's measure (the user, 2026-11-19), and since stage T7 the
       trigger of Hard's Interceptor boost too (PowerDecider::interceptors - the user's ruling of 2026-11-21: "the measure the CCEW
       rule uses"). */
    public static function fighterHeavy(AIView $view, array $enemies)
    {
        $flightValue = 0;
        $enemyValue = 0;
        foreach ($enemies as $enemy) {
            $value = $view->value($enemy);
            $enemyValue += $value;
            if ($enemy instanceof FighterFlight) $flightValue += $value;
        }
        return $enemyValue > 0 && $flightValue >= AIConfig::EW_FIGHTER_HEAVY * $enemyValue;
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

        $enemies = self::lockable($view);

        //The enemy's EW as it was last turn, stood in for this turn's.
        foreach ($enemies as $enemy) {
            if (!isset($remembered[(int)$enemy->id])) continue;
            foreach ($remembered[(int)$enemy->id] as $row) {
                $enemy->EW[] = new EWentry(-1, $enemy->id, $turn, $row[0], $row[1], $row[2]);
            }
        }

        /* Is the enemy weighted towards fighters (EW_FIGHTER_HEAVY of its visible value in flights)? Then CCEW may go past
           EW_CCEW_CAP on any ship (the user's ruling, 2026-11-19). */
        $fighterHeavy = self::fighterHeavy($view, $enemies);

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
        /* (2026-11-26 - the user's tester on live game 7857: "Apollo full CCEW vs fighters instead of 1-2 points as Hard should"; the
           Omega too, once the Sentri closed) A fighter-heavy enemy lifts the cap only on a ship with an anti-fighter gun to use it -
           the Apollo and the Omega have none (their Standard Particle Beams aim no better at a fighter than at a capital ship) - or
           when the enemy has no ship left at all (CCEW is then the only offensive EW there is: no OEW on fighters, game 5826). */
        $noShips = true;
        foreach ($enemies as $enemy) {
            if (!($enemy instanceof FighterFlight) && empty($enemy->mine)) $noShips = false;
        }
        $ccewCap = (($fighterHeavy && ($antiFighter > 0 || $noShips)) || ($plain > 0 && 2 * $antiFighter >= $plain)) ? PHP_INT_MAX : AIConfig::EW_CCEW_CAP;

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
        //enemy id => the most OEW it may get: a soft lock at most when it moves after the ship, or is close
        $caps = array();
        foreach ($soft + $close as $id => $yes) $caps[$id] = AIConfig::EW_SOFT_LOCK;

        /* MEDIUM RANGE (the user's ruling on game 5826, 2026-11-22: "At range 10-20 hexes I think we should not go full EW with
           Normal or Hard CPU players, unless the danger of return fire is very low. 50% EW max lock would be more appropriate").
           Beyond EW_CLOSE_RANGE and within EW_MEDIUM_RANGE, a lock holds at most EW_MEDIUM_LOCK_SHARE of the points - unless no
           enemy gun or launcher could reach this ship this turn at all, both closing as fast as they can (PowerDecider::reaches,
           Hard's power test). On 5826's turn 3 Omega #1 had put all 7 points on the G'Quan, 17 hexes off: the threats DEW is priced
           against were empty (the Heavy Lasers reloading, the pulse guns short of reach where the Narn ships stood), yet once both
           closed the Va'Kar's Medium Pulses and Ion Torpedo reached it. (A level's skill.) */
        $mediumCaps = array(); //(stage T9) the medium-range caps alone, for when the soft ones are lifted below
        if ($view->skill('mediumLocks') && $points > 1) {
            $medium = array();
            foreach ($enemies as $enemy) {
                $d = $near[(int)$enemy->id];
                if ($d > AIConfig::EW_CLOSE_RANGE && $d <= AIConfig::EW_MEDIUM_RANGE) $medium[] = (int)$enemy->id;
            }
            if (count($medium) > 0 && !self::unthreatened($view, $ship, $enemies)) {
                $half = max(1, (int)floor($points * AIConfig::EW_MEDIUM_LOCK_SHARE));
                foreach ($medium as $id) {
                    $caps[$id] = isset($caps[$id]) ? min($caps[$id], $half) : $half;
                    $mediumCaps[$id] = $half;
                }
            }
        }

        /* NO OEW ON FIGHTERS (the user's ruling on game 5826: "As a rule OEW should never be used on fighter flights, CCEW is generally
           enough by itself. The only caveat would be if there were only enemy fighters left and they are likely to be more than 10
           hexes away"). A flight may take OEW only when the enemy has no ship left and it is further than CCEW's reach from where the
           ship stands and from where it flies straight on (EW_CCEW_REACH). On 5826's turn 3 Omega #2 had put 2 points on the Frazi.
           (A level's skill - Easy's too, since the user's ruling below.) */
        $lockable = $enemies;
        if ($view->skill('noFlightOEW')) {
            $shipsLeft = false;
            foreach ($enemies as $enemy) {
                if (!($enemy instanceof FighterFlight) && empty($enemy->mine)) $shipsLeft = true;
            }
            $lockable = array();
            foreach ($enemies as $enemy) {
                if ($enemy instanceof FighterFlight && ($shipsLeft || $near[(int)$enemy->id] <= AIConfig::EW_CCEW_REACH)) continue;
                $lockable[] = $enemy;
            }
        }

        /* EASY (stage T5f, plan 5.8): half the points offensive, half DEW - nothing weighed. Since the user's ruling on game 5826
           (2026-11-22: "Easy should just follow the same CCEW rule as Normal and not put OEW on fighters ever") the offensive half
           is the CCEW deterrent first, where Normal would put it up, and the rest OEW on the nearest enemy that may take a lock -
           a ship, as for Normal (noFlightOEW); with nothing to lock (only fighters left, within CCEW's reach) the rest goes to CCEW
           as well, no further than Normal's cap. Until then it put its whole half on the nearest enemy, a flight included, and
           never put up CCEW. The page's own limits still hold: a Restricted EW critical's half stays DEW; an LCV keeps its rule below. */
        if ($view->skill('simpleEW') && !$lcv) {
            $half = max(0, min(intdiv($points, 2), $points - $minDew));
            $easy = array('points' => $points, 'DEW' => $points, 'CCEW' => 0, 'OEW' => array());
            $ccewOk = $deterrent && $canShootFighters && $view->skill('ccewDeterrent');
            if ($half > 0 && $ccewOk) $easy['CCEW'] = 1;
            $rest = $half - $easy['CCEW'];
            if ($rest > 0 && count($lockable) > 0) $easy['OEW'][(int)$lockable[0]->id] = $rest;
            else if ($rest > 0 && $ccewOk) $easy['CCEW'] += min($rest, $ccewCap - $easy['CCEW']);
            $easy['DEW'] = $points - $easy['CCEW'] - array_sum($easy['OEW']);
            return $easy;
        }

        //(game 5826) how many points one step of the search may move onto an enemy: 1 unless an enemy ELINT disrupts this ship
        $step = self::lockStep($view, $ship, $base);
        //stage T9 (M8): the ship's Ballistic Torpedo launchers that launch this turn - their volley counts in a split's worth (score())
        $launchers = array();
        foreach ($view->mounts($ship) as $mount) {
            if ($mount['fighter'] === null && AIView::isTorpedo($mount['weapon']) && $view->launcherNow($ship, $mount['weapon'])) $launchers[] = $mount['weapon'];
        }
        $state = array('view' => $view, 'ship' => $ship, 'base' => $base, 'guns' => $guns, 'enemies' => $enemies,
            'units' => $units, 'threats' => null, 'offence' => array(), 'defence' => array(), 'soft' => $soft, 'disrupted' => ($step > 1),
            'launchers' => $launchers);

        //the CCEW deterrent first: a point of DEW the greedy below does not have to find a gain for (a level's skill, stage T5f)
        if ($deterrent && $canShootFighters && !$lcv && $alloc['DEW'] > $minDew && $view->skill('ccewDeterrent')) {
            $alloc['DEW']--;
            $alloc['CCEW']++;
        }

        /* CLOSE ENGAGEMENT, A SOFT LOCK ON EVERY SHIP (the user's notes on game 5826, 2026-11-22: "both Omegas should have
           soft-locked the Gquan at these ranges"). The search below prices every gun at the one enemy it does most to, arcs
           not asked - a facing changes in Movement - so with the Va'Kar locked no Omega gun was ever expected to fire at the
           G'Quan beside it, and a point there gained nothing; up close, though, a ship's guns end up on different targets
           (turn 4's Fire phase: 13 Omega guns at the G'Quan, all without a lock). So before the search, one point on every enemy
           SHIP within EW_CLOSE_RANGE that a loaded gun of this ship could reach, nearest first, while DEW stays above its floor;
           the search then adds the second point where it pays. (A level's skill: Easy's simpleEW never gets here.) */
        if ($engaged && !$lcv && $view->skill('closeLocks')) {
            foreach ($enemies as $enemy) {
                if ($alloc['DEW'] <= $minDew) break;
                if (!isset($close[(int)$enemy->id]) || $enemy instanceof FighterFlight || !empty($enemy->mine)) continue;
                if (isset($alloc['OEW'][(int)$enemy->id]) || !self::reaches($view, $ship, $guns, $enemy, $base)) continue;
                $alloc['DEW']--;
                $alloc['OEW'][(int)$enemy->id] = 1;
            }
        }

        /* THE TORPEDO'S LOCK (stage T9, M8 - the user, 2026-11-23: Ballistic Torpedoes "backed by 1 or 2 OEW"). A ship whose torpedoes launch
           this turn puts a point, before the search, on the enemy SHIP they would do most to with it (AIView::torpedoEstimate - the volley the
           bot would send) - where the point adds something (a jammer denies it). The search may add the second: the volley counts in
           score(). A flight is CCEW's (noFlightOEW). Every level the search runs at (Easy's simpleEW puts its OEW on the nearest ship). */
        if (count($launchers) > 0 && !$lcv && $alloc['DEW'] > $minDew) {
            $pick = null;
            $pickWorth = 0;
            foreach ($lockable as $enemy) {
                if ($enemy instanceof FighterFlight || !empty($enemy->mine) || isset($alloc['OEW'][(int)$enemy->id])) continue;
                if (isset($caps[(int)$enemy->id]) && $caps[(int)$enemy->id] < 1) continue;
                $without = self::torpedoWorth($view, $ship, $launchers, $enemy, $base, 0);
                $with = self::torpedoWorth($view, $ship, $launchers, $enemy, $base, 1);
                if ($with > $without && $with > $pickWorth) {
                    $pick = $enemy;
                    $pickWorth = $with;
                }
            }
            if ($pick !== null) {
                $alloc['DEW']--;
                $alloc['OEW'][(int)$pick->id] = 1;
            }
        }

        /* The search: a step at a time, the one that gains most for each point it moves, while that is at least EW_MIN_GAIN a
           point. Every step is one point unless an enemy ELINT disrupts the ship (lockStep) - then a step may also move up to
           $step points onto one enemy at once, its gain counted per point, so it is taken only when it pays better than the
           single points beside it. */
        $current = self::score($state, $alloc);

        /* A JAMMER (stage T9 - the user's note on the Minbari, 2026-11-23: "The Minbari's unique system is the Jammer, which prevents
           OEW locks on their ships when active. This generally means they use more OEW, especially at the start of games, as the
           enemies ability to retaliate is vastly reduced due to low hit chances from having double range penalty"). The pricing
           already knows the jammer - DEW is priced through the real hit formula, the doubled range penalty included - so a jammed ship
           goes all-in on OEW at long range against enemies moving first. Against enemies moving AFTER it the soft lock capped every
           lock at EW_SOFT_LOCK and the rest stayed on DEW (a Sharlin 30 hexes from an Omega and a Hyperion moving after it: 2 OEW,
           10 DEW - with every enemy gun at a few percent). So a ship whose jammer denies EVERY enemy a lock (jammed()) has no soft
           locks beyond EW_CLOSE_RANGE: the split is the pricing's (an enemy moving after still counts for EW_MOVES_AFTER_WEIGHT of
           its fire); the close-range and medium-range caps stay. */
        if (self::jammed($ship, $enemies)) {
            foreach (array_keys($soft) as $id) {
                if (isset($close[$id])) continue;
                if (isset($mediumCaps[$id])) $caps[$id] = $mediumCaps[$id];
                else unset($caps[$id]);
            }
        }

        /* MISSILE SHIPS TURTLE (the user's tester, 2026-11-26, live game 7857 - the Apollo: "missile ships should 'turtle' with high DEW
           at Normal and Hard - missiles don't benefit from OEW"; Weapon::$useOEW is false on every missile). A ship most of whose fire
           is in weapons that use no OEW (missileShip) locks no enemy past EW_TURTLE_LOCK and keeps CCEW to the deterrent's point: the
           rest is DEW. (The skill 'missileTurtle'; after the jammer's lifts, which it overrules.) */
        if ($view->skill('missileTurtle') && self::missileShip($view, $ship)) {
            foreach ($enemies as $enemy) {
                $id = (int)$enemy->id;
                $caps[$id] = isset($caps[$id]) ? min($caps[$id], AIConfig::EW_TURTLE_LOCK) : AIConfig::EW_TURTLE_LOCK;
            }
            $ccewCap = min($ccewCap, 1);
        }

        while ($alloc['DEW'] > $minDew) {
            $best = null;
            $bestTotal = null;
            $bestScore = $current + AIConfig::EW_MIN_GAIN; //what a 1-point step must beat - exactly the rule before game 5826
            $bestRate = AIConfig::EW_MIN_GAIN;            //...and a step of more points, a point at a time
            $ccewRoom = ($flights && !$lcv) ? $ccewCap - $alloc['CCEW'] : 0;
            foreach (self::moves($alloc, $lockable, $ccewRoom, $caps, min($step, $alloc['DEW'] - $minDew)) as $try) {
                $score = self::score($state, $try);
                $spent = $alloc['DEW'] - $try['DEW'];
                $rate = ($score - $current) / $spent;
                if ($spent == 1 ? $score > $bestScore : $rate > $bestRate) {
                    $best = $try;
                    $bestTotal = $score;
                    $bestRate = $rate;
                    $bestScore = ($spent == 1) ? $score : $current + $rate;
                }
            }
            if ($best === null) break;
            $alloc = $best;
            $current = $bestTotal;
        }

        /* An LCV has no choice: what is left above 2 goes to OEW, wherever it does most (the nearest, when nothing does anything) -
           on a flight only when nothing else may take it (the page's LCV rule comes before the fighter rule). */
        while ($lcv && $alloc['DEW'] > 2) {
            $best = null;
            $bestScore = null;
            foreach (self::moves($alloc, count($lockable) > 0 ? $lockable : $enemies, 0) as $try) {
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

    /* Every split one point of DEW away from $alloc - and, with $step over 1, every split moving 2..$step points of it onto one
       enemy (or into CCEW) at once, listed after the single points so that a tie goes to the single point. $enemies: those that
       may take OEW. $ccewRoom: how many more CCEW points may go up (0: none). $caps: enemy id => the most OEW it may hold (a soft
       lock, EW_SOFT_LOCK; half the points at medium range); empty where the ship has no choice (an LCV). */
    private static function moves(array $alloc, array $enemies, $ccewRoom, array $caps = array(), $step = 1)
    {
        $list = array();
        for ($points = 1; $points <= max(1, (int)$step); $points++) {
            foreach ($enemies as $enemy) {
                $has = isset($alloc['OEW'][(int)$enemy->id]) ? $alloc['OEW'][(int)$enemy->id] : 0;
                if (isset($caps[(int)$enemy->id]) && $has + $points > $caps[(int)$enemy->id]) continue;
                $try = $alloc;
                $try['DEW'] -= $points;
                $try['OEW'][(int)$enemy->id] = $has + $points;
                $list[] = $try;
            }
            if ($points <= $ccewRoom) {
                $try = $alloc;
                $try['DEW'] -= $points;
                $try['CCEW'] += $points;
                $list[] = $try;
            }
        }
        return $list;
    }

    /* (2026-11-26) Is most of $ship's fire - EW_MISSILE_SHARE or more, each weapon by its average damage, shots and guns a turn of
       its loading - in weapons that use no OEW (Weapon::$useOEW false: every missile)? */
    public static function missileShip(AIView $view, $ship)
    {
        $all = 0.0;
        $missiles = 0.0;
        foreach ($view->mounts($ship) as $mount) {
            $weapon = $mount['weapon'];
            if ($weapon->isDestroyed() || $weapon instanceof JumpEngine || !empty($weapon->isRammingAttack) || $weapon->maxDamage <= 0) continue;
            if ($weapon->weaponClass === 'Support' || $weapon->isOfflineOnTurn($view->turn)) continue;
            $fc = $weapon->fireControl;
            if (!isset($fc[0]) && !isset($fc[1]) && !isset($fc[2])) continue; //no offensive fire at all (an Interceptor)
            $worth = (($weapon->minDamage + $weapon->maxDamage) / 2) * max(1, (int)$weapon->defaultShots) * max(1, (int)$weapon->guns)
                / max(1, (int)$weapon->loadingtime);
            $all += $worth;
            if (empty($weapon->useOEW)) $missiles += $worth;
        }
        return $all > 0 && $missiles >= AIConfig::EW_MISSILE_SHARE * $all;
    }

    /* How many points one step of the search may move onto one enemy (the user's notes on game 5826): 1 - the search as it always
       was - unless an enemy ELINT disrupts this ship; then 1 more than the disruption a lone lock would meet, by the engine's own
       sum (EW::getDistruptionEW: each ELINT in range and in sight, DIST / 3, shared over the ship's lock rows). On turn 3 the
       Va'Kar's remembered DIST 3 on each Omega ate a lone first point whole, so that point gained nothing and the point-at-a-time
       search never reached the lock behind it - both Omegas, heavy guns ready, stayed on DEW. */
    private static function lockStep(AIView $view, $ship, array $base)
    {
        $ship->EW = array_merge($base, array(new EWentry(-1, $ship->id, $view->turn, 'OEW', 1, -1)));
        $disruption = EW::getDistruptionEW($view->gamedata, $ship);
        $ship->EW = $base;
        return ($disruption > 0) ? 1 + (int)ceil($disruption) : 1;
    }

    /* (Stage T9) Does $ship's jammer deny every enemy that could shoot at it a lock - the engine's own test (Jammer::
       getSpecialAbilityValue: on, not in a nebula, the shooter of another faction, no Advanced Sensors)? Mines left to Hazards. */
    private static function jammed($ship, array $enemies)
    {
        $any = false;
        foreach ($enemies as $enemy) {
            if (!empty($enemy->mine)) continue;
            if ($ship->getSpecialAbilityValue('Jammer', array('shooter' => $enemy, 'target' => $ship)) <= 0) return false;
            $any = true;
        }
        return $any;
    }

    /* Is the danger of return fire "very low" (the user's ruling on game 5826)? Only when no loaded gun or launcher of any enemy
       (mines left to Hazards) could reach $ship this turn at all, with both closing as fast as they can and the enemy's every EW
       point locked on - the reach test of Hard's power management (PowerDecider::reaches), not the threats DEW is priced against,
       which stand the enemy still and leave ballistics out. */
    private static function unthreatened(AIView $view, $ship, array $enemies)
    {
        $armed = array();
        foreach ($enemies as $enemy) {
            if (empty($enemy->mine)) $armed[] = $enemy;
        }
        $reach = PowerDecider::reaches($view, $ship, $armed);
        return $reach['shots'] == 0;
    }

    //(Stage T9, M8) What $ship's torpedo launchers would do to $enemy with $points of OEW on it (AIView::torpedoEstimate) - the one lock row alone.
    private static function torpedoWorth(AIView $view, $ship, array $launchers, $enemy, array $base, $points)
    {
        $ship->EW = ($points > 0) ? array_merge($base, array(new EWentry(-1, $ship->id, $view->turn, 'OEW', $points, $enemy->id))) : $base;
        $worth = 0;
        foreach ($launchers as $weapon) $worth += $view->torpedoEstimate($ship, $weapon, $enemy);
        $ship->EW = $base;
        return $worth;
    }

    //Could a loaded gun of $ship do anything to $enemy with a point locked on it? Arcs not asked, as score() does not.
    private static function reaches(AIView $view, $ship, array $guns, $enemy, array $base)
    {
        $ship->EW = array_merge($base, array(new EWentry(-1, $ship->id, $view->turn, 'OEW', 1, $enemy->id)));
        $reach = false;
        foreach ($guns as $weapon) {
            if ($view->estimate($ship, $weapon, $enemy, false) > 0) {
                $reach = true;
                break;
            }
        }
        $ship->EW = $base;
        return $reach;
    }

    //What a split is worth to the ship: expected damage dealt less expected damage taken.
    private static function score(array &$state, array $alloc)
    {
        $view = $state['view'];
        $ship = $state['ship'];
        $turn = $view->turn;

        /* The split as the engine will see it (the user's notes on game 5826): every OEW and CCEW row of it on the ship at once.
           An enemy ELINT's disruption is shared over all of them (EW::getDistruptionEW divides it by getOEWTargetNum), so what a
           lock is worth depends on how many the ship holds. Until then each enemy was priced with its own row alone: a lone
           point on the G'Quan met the Va'Kar's whole disruption, where the CCEW row beside it would have halved it, while a
           point on the Frazi was priced beside that CCEW row - so the Omegas locked the fighters and no ship. Nothing else the
           hit formula asks of the shooter's EW depends on its other rows: where nothing disrupts the ship ($state['disrupted']
           false) every answer, and its cache, is what it always was. */
        $rows = $state['base'];
        $locks = 0;
        foreach ($alloc['OEW'] as $targetId => $amount) {
            if ($amount <= 0) continue;
            $rows[] = new EWentry(-1, $ship->id, $turn, 'OEW', $amount, $targetId);
            $locks++;
        }
        if ($alloc['CCEW'] > 0) {
            $rows[] = new EWentry(-1, $ship->id, $turn, 'CCEW', $alloc['CCEW'], -1);
            $locks++;
        }
        $standing = false;
        $offence = 0;
        foreach ($state['guns'] as $weapon) {
            $best = 0;
            foreach ($state['enemies'] as $enemy) {
                $oew = isset($alloc['OEW'][(int)$enemy->id]) ? $alloc['OEW'][(int)$enemy->id] : 0;
                $ccew = ($enemy instanceof FighterFlight) ? $alloc['CCEW'] : 0;
                $key = $weapon->id . ':' . $enemy->id . ':' . $oew . ':' . $ccew . ($state['disrupted'] ? ':' . $locks : '');
                if (!isset($state['offence'][$key])) {
                    if (!$standing) {
                        $ship->EW = $rows;
                        $standing = true;
                    }
                    $state['offence'][$key] = $view->estimate($ship, $weapon, $enemy, false)
                        * (isset($state['soft'][(int)$enemy->id]) ? AIConfig::EW_MOVES_AFTER_WEIGHT : 1);
                }
                $best = max($best, $state['offence'][$key]);
            }
            $offence += $best;
        }
        /* (Stage T9, M8) the ship's torpedo launchers: each its volley at the one enemy it does most to, launched now - the arc as the ship
           stands, the launch is before Movement (AIView::torpedoEstimate). Not weighted by who moves first: a torpedo follows its target. */
        foreach (isset($state['launchers']) ? $state['launchers'] : array() as $weapon) {
            $best = 0;
            foreach ($state['enemies'] as $enemy) {
                $oew = isset($alloc['OEW'][(int)$enemy->id]) ? $alloc['OEW'][(int)$enemy->id] : 0;
                $ccew = ($enemy instanceof FighterFlight) ? $alloc['CCEW'] : 0;
                $key = 'torpedo:' . $weapon->id . ':' . $enemy->id . ':' . $oew . ':' . $ccew . ($state['disrupted'] ? ':' . $locks : '');
                if (!isset($state['offence'][$key])) {
                    if (!$standing) {
                        $ship->EW = $rows;
                        $standing = true;
                    }
                    $state['offence'][$key] = $view->torpedoEstimate($ship, $weapon, $enemy);
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
