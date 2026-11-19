<?php
/**
 * CPU opponent - where a SHIP goes (TACTICAL_AI_PLAN.md sections 5.4 and 10.7, stages T5a-T5c).
 *
 * The moves are ShipMover's: every plan it offers is one the page accepts (tests/ai/
 * shipMoverHarness.php replays them through movement.js). ShipMover finds every place the ship can
 * end this turn - a few dozen for a capital ship, a few hundred for a nimble one - and this class
 * scores each END STATE (hex, facing, heading, speed) in named terms (principle P7). The fire terms
 * are in "value removed" - expected damage times what a point of the unit hit is worth (its value
 * over the damage that finishes it), as FireDecider and FlightDecider count it - and come from the
 * real hit formula (AIView::estimate), never a formula of the bot's own:
 *
 *   offence    this turn's direct fire from the end hex and facing: each loaded gun at the enemy it
 *              does most to, arcs and line of sight asked, every unit where it will stand at the Fire
 *              phase (AIView::atFireTime - a unit yet to move flies straight on, and fire at it counts
 *              MOVE_UNMOVED_OFFENCE: it may yet turn away). No target counts past the damage that
 *              finishes it (removed()), and the ship's own loss stops at its whole value (lost());
 *   launchers  next turn's ballistic launches. A launch is made in Initial Orders from where Movement
 *              left the ship, so the end state decides which launchers bear (the user's note on game
 *              4701: a missile cruiser held course with three racks out of arc) - MOVE_LAUNCH_WEIGHT;
 *   danger     the enemy's fire at the ship there, MOVE_DANGER_WEIGHT against its own - "best trade",
 *              the user's ruling of 2026-11-16 - from the guns loaded this turn (next turn's terms: by
 *              then), as the bot's own are counted. A unit that has moved fires with its arcs. One yet to
 *              move is MORE dangerous (user, 2026-11-19): it sees where the ship ended, so every gun of
 *              it counts at MOVE_UNMOVED_DANGER and the section it hits leans to the ship's worst
 *              (AIView::exposureUnmoved). Each enemy's fire is weighed by the section it meets
 *              (AIView::exposure): a damaged section counts more, a destroyed one - the hit then goes into
 *              the primary structure - far more. A mine is not an enemy here: Hazards prices it;
 *   next turn  the same trade a turn on with everybody flying straight on (MOVE_NEXT_TURN_WEIGHT; both
 *              sides at the same weight - next turn's initiative is unknown): it keeps a ship from flying
 *              past its enemy for one good volley, and brings it in when this turn's guns reach nothing. A
 *              slow gun that fires this turn is not counted again;
 * and the SHAPING terms, in hexes, each worth MOVE_HEX_VALUE of the ship's own value - enough to
 * decide where the fire is slight or even, too little to buy a bad trade:
 *   closing    each hex to the nearest enemy (MOVE_CLOSING_PER_HEX) - the pursuit question of stage
 *              T2, ruled into T5. Always counted: as an on/off term (only with nothing in reach) one
 *              end state with a token shot turned it off for all, and ships drifted away;
 *   formation  each hex beyond MOVE_FORMATION_RANGE from the nearest of the bot's other ships - a loose
 *              formation, the user's ruling;
 *   speed      each point above the comfortable speed of the ship's size, or below MOVE_MIN_SPEED; a
 *              great deal for each point above the SAFE speed - the fastest at which one turn costs at
 *              most MOVE_SAFE_TURN_SHARE of the ship's thrust (user, 2026-11-19: Omegas at 7 and 8 were "too
 *              fast to safely manoeuvre"; only a kill or escaping heavy damage pays for it); more again above
 *              the speed at which a whole turn's thrust no longer pays for a turn (ShipMover::fastestTurnable);
 *   plainness  a little for each manoeuvre, so the plainest plan wins among equals;
 * and the BOARD (Hazards, stage T5c): a blocked hex entered, ending off the map or heading off it, which
 * outweigh everything; and, priced in value, a drifting field's overrun at the end hex, meteor swarms
 * entered, and the mines the bot can see.
 *
 * TURN SHORTENING (user, 2026-11-19): ShipMover also offers each turn with all the extra thrust it can
 * take (the double turns a shortened delay allows); the plan chosen then has its LAST turn shortened with
 * thrust that would otherwise go unused, within the ratings (ShipMover::shortened).
 *
 * The decision is made on the bot's own MASKED view of the game (principle P2).
 *
 * GRAVITIC hulls (stage T5d) are scored like any other: ShipMover plots them by the page's gravitic
 * rules, and as the bot never pivots them (T5e) they fly bow- or stern-first, where those rules are a
 * plain hull's.
 *
 * NOT YET (stage T5's later parts, section 10.7): pivots and rolls (T5e), levels (T5f). Interception is
 * not counted here: it changes how much a volley does, hardly where it is best fired from.
 */
class ShipDecider
{
    //One line per ship moved by the last orders(), for tac_ai_log.
    public static $last = array();

    public static function reset()
    {
        self::$last = array();
    }

    /* The rows for one ship: the ShipMover plan whose end state scores best. $view is the bot's
       masked view of the Movement phase; $hazards the board, built once for the decision. */
    public static function rows(AIView $view, ShipMover $mover, $ship, Hazards $hazards)
    {
        $plans = self::candidates($mover);
        if (count($plans) == 0) return MoveDecider::choose($mover, $hazards->blocked, $hazards->bounds);

        $context = self::context($view, $ship, $mover);
        $start = $ship->getHexPos();

        //The fire terms depend only on where the ship ends and which way it points; the look-ahead on its heading and speed too.
        $judged = $view->atFireTime(function () use ($view, $ship, $plans, $context, $hazards, $start) {
            $out = array();
            $fire = array();
            foreach ($plans as $index => $plan) $out[$index] = self::judge($view, $ship, $plan, $context, $fire, $hazards, $start);
            return $out;
        }, array((int)$ship->id));

        $best = null;
        $bestScore = null;
        foreach ($judged as $index => $j) {
            $score = self::score($j, $context['hexValue']);
            $judged[$index]['score'] = $score;
            if ($bestScore === null || $score > $bestScore) {
                $bestScore = $score;
                $best = $index;
            }
        }

        $plan = $plans[$best];
        $j = $judged[$best];
        $rows = $plan['rows'];

        //Thrust left over shortens the last turn's delay (turn shortening): next turn starts free to turn.
        $shortened = empty($plan['overthrust']) ? $mover->shortened($plan) : null;
        if ($shortened !== null) $rows = $shortened->rows();

        $hz = $j['board'];
        self::$last[] = sprintf('%s: %s; fire %.0f, launch %.0f, danger %.0f, next %.0f/%.0f, %d hexes off%s%s%s',
            $ship->name, count($plan['actions']) ? self::describe($plan['actions'], $plan['extras']) : 'straight on',
            $j['offence'], $j['launch'], $j['danger'], $j['nextOffence'], $j['nextDanger'], $j['nearest'],
            empty($plan['overthrust']) ? '' : '; OVERTHRUST ' . implode(', ', array_map(function ($id, $x) { return "#$id +$x"; }, array_keys($plan['overthrust']), $plan['overthrust'])),
            $shortened !== null ? '; last turn shortened' : '',
            ($hz['drift'] + $hz['meteors'] + $hz['mines'] > 0) ? sprintf('; hazards: drift %.0f, meteors %.0f, mines %.0f', $hz['drift'], $hz['meteors'], $hz['mines']) : '');

        if (is_array(self::$trace)) {
            uasort($judged, function ($a, $b) { return $b['score'] <=> $a['score']; });
            foreach (array_slice($judged, 0, 5, true) as $index => $t) {
                self::$trace[] = sprintf('  %8.1f  %-40s fire %.0f launch %.0f danger %.0f next %.0f/%.0f near %d shaping %.1f hazard %.0f',
                    $t['score'], substr(self::describe($plans[$index]['actions'], $plans[$index]['extras']), 0, 40), $t['offence'], $t['launch'], $t['danger'],
                    $t['nextOffence'], $t['nextDanger'], $t['nearest'], $t['shaping'], $t['hazard']);
            }
        }

        return $rows;
    }

    /* For the harness and scratch checks only: set to an array and rows() appends its five best
       plans, terms and all. Null (the default) in a game. */
    public static $trace = null;

    /* One plan's worth. Fire both ways now and a turn on, next turn's launches; then the shaping
       terms, in hexes of the ship's own value: closing on the nearest enemy (always - enough to bring
       a ship in where the fire is slight, too little to buy a bad trade), formation, speed and
       manoeuvres; then the board. */
    private static function score(array $j, $hexValue)
    {
        return $j['offence'] + AIConfig::MOVE_LAUNCH_WEIGHT * $j['launch'] - AIConfig::MOVE_DANGER_WEIGHT * $j['danger']
            + AIConfig::MOVE_NEXT_TURN_WEIGHT * ($j['nextOffence'] - AIConfig::MOVE_DANGER_WEIGHT * $j['nextDanger'])
            - $hexValue * (AIConfig::MOVE_CLOSING_PER_HEX * $j['closing'] + $j['shaping'])
            - $j['hazard'];
    }

    /* Every end state the ship can reach this turn: first within its thrusters' ratings, then - with up
       to MOVE_OVERTHRUST points over a rating - the end states only an overthrust reaches (stage T5b; the
       user's ruling of 2026-11-13: within ratings first, over them only where that is worth the critical
       it invites, which judge() prices). Turns are tried shortened too (allowShortening). */
    private static function candidates(ShipMover $mover)
    {
        $plans = array();
        $search = clone $mover;
        foreach ($search->allowShortening()->enumerate(AIConfig::SHIP_PLANS) as $plan) $plans[self::endKey($plan)] = $plan;
        if (AIConfig::MOVE_OVERTHRUST > 0) {
            $over = clone $mover;
            foreach ($over->allowShortening()->allowOverthrust(AIConfig::MOVE_OVERTHRUST)->enumerate(AIConfig::SHIP_PLANS) as $plan) {
                $key = self::endKey($plan);
                if (!isset($plans[$key])) $plans[$key] = $plan;
            }
        }
        return array_values($plans);
    }

    private static function endKey(array $plan)
    {
        return $plan['position']->q . ',' . $plan['position']->r . ',' . $plan['heading'] . ',' . $plan['facing'] . ',' . $plan['speed'];
    }

    /* What a plan's overthrust risks, in hexes: for each thruster it takes over its rating, the end-of-turn
       critical roll (Criticals: d20 + the thruster's damage + the excess; 15 or more First Thrust Ignored,
       20 or more Half Efficiency as well), each expected critical MOVE_COST_THRUSTER_CRIT - the worse one
       counted twice. */
    private static function overthrustRisk($ship, array $overthrust)
    {
        $risk = 0;
        foreach ($overthrust as $id => $excess) {
            $thruster = $ship->getSystemById($id);
            $add = (int)$excess + ($thruster ? (int)floor($thruster->getTotalDamage()) : 0);
            $risk += AIConfig::MOVE_COST_THRUSTER_CRIT * (max(0, min(1, (6 + $add) / 20)) + max(0, min(1, (1 + $add) / 20)));
        }
        return $risk;
    }

    //The plan's orders in a few words: "accel, turnright x2 (shortened), move x5".
    private static function describe(array $actions, array $extras = array())
    {
        $out = array();
        foreach ($actions as $i => $action) {
            $word = $action . (!empty($extras[$i]) ? ' (shortened)' : '');
            $last = count($out) - 1;
            if ($last >= 0 && $out[$last][0] === $word) $out[$last][1]++;
            else $out[] = array($word, 1);
        }
        return implode(', ', array_map(function ($a) { return $a[0] . ($a[1] > 1 ? ' x' . $a[1] : ''); }, $out));
    }

    /* What does not depend on the plan: the ship's guns and launchers by kind, the enemies with what
       they carry, and the bot's other ships. Asked BEFORE atFireTime() moves anybody, so "has this
       enemy moved this turn" is read off its own rows. */
    private static function context(AIView $view, $ship, ShipMover $mover)
    {
        $turn = $view->turn;

        //My direct-fire guns: loaded now (they fire this turn), or loaded by next turn.
        $guns = array();
        foreach ($view->mounts($ship) as $mount) {
            $weapon = $mount['weapon'];
            if ($weapon->ballistic || !$view->plainGun($weapon)) continue;
            $why = $view->weaponBlock($ship, $weapon, null);
            $now = ($why === null);
            $next = $now || ($why === 'not loaded' && (int)$weapon->getLoadingTime() <= (int)$weapon->turnsloaded + 1);
            if (!$now && !$next) continue;
            $slow = (int)$weapon->getLoadingTime() > 1;
            $key = get_class($weapon) . '|' . $weapon->firingMode . '|' . $weapon->startArc . '|' . $weapon->endArc . '|' . (int)$now . (int)$next . '|' . count($weapon->criticals);
            if (!isset($guns[$key])) $guns[$key] = array('weapon' => $weapon, 'count' => 0, 'now' => $now, 'next' => $next, 'slow' => $slow);
            $guns[$key]['count']++;
        }

        //My launchers that will launch next turn, each in a round the bot uses.
        $launchers = array();
        foreach ($view->mounts($ship) as $mount) {
            $weapon = $mount['weapon'];
            if (!$view->launcherNextTurn($ship, $weapon)) continue;
            if ($weapon instanceof AmmoMissileRackS) {
                $modes = $view->modes($weapon);
                if (count($modes) == 0) continue;
                if (!in_array((int)$weapon->firingMode, $modes, true)) $weapon->changeFiringMode($modes[0]);
            }
            $key = get_class($weapon) . '|' . $weapon->firingMode . '|' . $weapon->startArc . '|' . $weapon->endArc . '|' . count($weapon->criticals);
            if (!isset($launchers[$key])) $launchers[$key] = array('weapon' => $weapon, 'count' => 0);
            $launchers[$key]['count']++;
        }

        /* The enemies: moved this turn or not, and their guns - by kind with arcs (fire now from a unit that has moved), and
           without; each loaded now (it fires this turn) or by next turn, as the bot counts its own - every player sees an
           enemy weapon's loading on its ship window (until 2026-11-19 a gun still reloading counted as danger too, and in game
           4750 the Omegas ran from G'Quans whose heavy guns had just fired). A mine fires by its own rules, which Hazards
           prices: none of its guns are counted here. */
        $enemies = array();
        foreach ($view->enemies() as $enemy) {
            $moved = !empty($enemy->base) || !empty($enemy->smallBase) || !empty($enemy->osat) || !empty($enemy->mine)
                || $enemy->getLastTurnMoved() >= $turn;
            $arced = array();
            $any = array();
            if (empty($enemy->mine)) {
                foreach ($view->mounts($enemy) as $mount) {
                    $weapon = $mount['weapon'];
                    if (!$view->plainGun($weapon, $mount['fighter'])) continue;
                    $now = $view->loaded($weapon);
                    $next = $now || (int)$weapon->getLoadingTime() <= (int)$weapon->turnsloaded + 1;
                    if (!$now && !$next) continue;
                    $base = get_class($weapon) . '|' . $weapon->firingMode . '|' . (int)$now . (int)$next;
                    $withArc = $base . '|' . $weapon->startArc . '|' . $weapon->endArc;
                    if (!isset($arced[$withArc])) $arced[$withArc] = array('weapon' => $weapon, 'count' => 0, 'now' => $now, 'next' => $next);
                    $arced[$withArc]['count']++;
                    if (!isset($any[$base])) $any[$base] = array('weapon' => $weapon, 'count' => 0, 'now' => $now, 'next' => $next);
                    $any[$base]['count']++;
                }
            }
            $enemies[] = array('unit' => $enemy, 'moved' => $moved, 'arced' => array_values($arced), 'any' => array_values($any),
                'health' => $view->healthLeft($enemy), 'worth' => $view->value($enemy) / $view->healthLeft($enemy));
        }

        //What the ship closes on: the enemy's ships - a capital ship does not chase fighters - or its flights when that is all there is.
        $quarry = array();
        foreach ($enemies as $e) {
            if (!($e['unit'] instanceof FighterFlight) && empty($e['unit']->mine)) $quarry[] = $e['unit'];
        }
        if (count($quarry) == 0) {
            foreach ($enemies as $e) {
                if (empty($e['unit']->mine)) $quarry[] = $e['unit'];
            }
        }

        $friends = array();
        foreach ($view->myUnits() as $unit) {
            if ((int)$unit->id === (int)$ship->id || $unit instanceof FighterFlight) continue;
            $friends[] = $unit;
        }

        $size = max(0, (int)$ship->shipSizeClass);
        $comfort = AIConfig::MOVE_COMFORT_SPEED[0];
        foreach (AIConfig::MOVE_COMFORT_SPEED as $class => $speed) {
            if ($size >= $class) {
                $comfort = $speed;
                break;
            }
        }

        return array('guns' => array_values($guns), 'launchers' => array_values($launchers), 'enemies' => $enemies, 'friends' => $friends, 'quarry' => $quarry,
            'health' => $view->healthLeft($ship), 'worth' => $view->value($ship) / $view->healthLeft($ship), 'comfort' => $comfort,
            'turnable' => $mover->fastestTurnable(), 'safe' => $mover->safeSpeed(AIConfig::MOVE_SAFE_TURN_SHARE),
            'hexValue' => max(1, $view->value($ship)) * AIConfig::MOVE_HEX_VALUE);
    }

    /* Everything one plan is scored by. The ship is put at the plan's end for the asking, and taken
       back. $fire caches the terms that depend only on the end hex and facing. */
    private static function judge(AIView $view, $ship, array $plan, array $context, array &$fire, Hazards $hazards, OffsetCoordinate $start)
    {
        $ship->movement[] = new MovementOrder(-1, 'move', $plan['position'], 0, 0, $plan['speed'], $plan['heading'], $plan['facing'], false, $view->turn, 0, 0);
        try {
            $key = $plan['position']->q . ',' . $plan['position']->r . ',' . $plan['facing'];
            if (!isset($fire[$key])) $fire[$key] = self::fireTerms($view, $ship, $context);
            $terms = $fire[$key];

            $next = $view->ahead(function () use ($view, $ship, $context, $terms) {
                return self::nextTerms($view, $ship, $context, $terms['firedSlow']);
            });

            $board = $hazards->judge($ship, $start, $plan, $context['worth']);
        } finally {
            array_pop($ship->movement);
        }

        //shaping, in hexes: formation, speed, manoeuvres
        $shaping = AIConfig::MOVE_FORMATION_PER_HEX * max(0, $terms['friend'] - AIConfig::MOVE_FORMATION_RANGE);
        $speed = (int)$plan['speed'];
        if ($speed < AIConfig::MOVE_MIN_SPEED) $shaping += AIConfig::MOVE_COST_SLOW * (AIConfig::MOVE_MIN_SPEED - $speed);
        if ($speed > $context['comfort']) $shaping += AIConfig::MOVE_COST_FAST * ($speed - $context['comfort']);
        if ($speed > $context['safe']) $shaping += AIConfig::MOVE_COST_OVERSPEED * ($speed - $context['safe']);
        if ($speed > $context['turnable']) $shaping += AIConfig::MOVE_COST_UNTURNABLE * ($speed - $context['turnable']);
        foreach ($plan['actions'] as $action) {
            if ($action === ShipMover::ACCEL || $action === ShipMover::DECEL) $shaping += AIConfig::MOVE_COST_SPEED_CHANGE;
            else if ($action === ShipMover::SLIP_LEFT || $action === ShipMover::SLIP_RIGHT) $shaping += AIConfig::MOVE_COST_SLIP;
            else if ($action !== ShipMover::MOVE) $shaping += AIConfig::MOVE_COST_TURN;
        }
        if (!empty($plan['overthrust'])) $shaping += self::overthrustRisk($ship, $plan['overthrust']);

        return array('offence' => $terms['offence'], 'launch' => $terms['launch'], 'danger' => $terms['danger'],
            'nextOffence' => $next['offence'], 'nextDanger' => $next['danger'], 'shaping' => $shaping,
            'nearest' => $terms['nearest'], 'closing' => $terms['nearest'] + AIConfig::MOVE_CLOSING_NEXT_TURN * $next['nearest'],
            'hazard' => $board['total'], 'board' => $board);
    }

    /* This turn's fire both ways, next turn's launches, and the distances, with the ship at the end
       state judged and everybody else where the Fire phase should find them. */
    private static function fireTerms(AIView $view, $ship, array $context)
    {
        $clear = self::lines($view, $ship, $context['enemies']);
        $damage = array();
        $firedSlow = array();
        foreach ($context['guns'] as $index => $gun) {
            if (!$gun['now']) continue;
            foreach ($context['enemies'] as $e => $enemy) {
                if (!$gun['weapon']->ignoresLoS && !$clear[$e]) continue;
                $d = $view->estimate($ship, $gun['weapon'], $enemy['unit'], true) * $gun['count'] * ($enemy['moved'] ? 1 : AIConfig::MOVE_UNMOVED_OFFENCE);
                if ($d > 0) $damage[$index][$e] = $d;
            }
            if (isset($damage[$index]) && $gun['slow']) $firedSlow[$index] = true;
        }

        $launches = array();
        foreach ($context['launchers'] as $index => $launcher) {
            foreach ($context['enemies'] as $e => $enemy) {
                $d = $view->launchEstimate($ship, $launcher['weapon'], $enemy['unit']) * $launcher['count'] * ($enemy['moved'] ? 1 : AIConfig::MOVE_UNMOVED_OFFENCE);
                if ($d > 0) $launches[$index][$e] = $d;
            }
        }

        //The enemy's fire: from its arcs once it has moved; every gun, and towards the ship's worst side, while it has yet to.
        $inward = self::lines($view, $ship, $context['enemies'], true);
        $incoming = array();
        foreach ($context['enemies'] as $e => $enemy) {
            $d = 0;
            foreach ($enemy['moved'] ? $enemy['arced'] : $enemy['any'] as $kind) {
                if (!$kind['now'] || (!$inward[$e] && !$kind['weapon']->ignoresLoS)) continue;
                $d += $view->estimate($enemy['unit'], $kind['weapon'], $ship, $enemy['moved']) * $kind['count'];
            }
            if ($d <= 0) continue;
            if ($enemy['moved']) $incoming[] = array($d, $view->exposure($ship, $enemy['unit']));
            else $incoming[] = array($d * AIConfig::MOVE_UNMOVED_DANGER, $view->exposureUnmoved($ship, $enemy['unit']));
        }

        return array('offence' => self::removed($damage, $context['enemies']), 'launch' => self::removed($launches, $context['enemies']),
            'danger' => self::lost($incoming, $context['health'], $context['worth']), 'firedSlow' => $firedSlow,
            'nearest' => self::nearest($ship, $context['quarry']), 'friend' => self::nearest($ship, $context['friends']));
    }

    /* The value a set of guns should remove: each gun, best first, goes to the target where it adds most,
       and no target counts past the damage that finishes it - a crippled ship is worth its value, no more.
       (Uncapped, worth = value / health made one volley at a near-dead ship score in the tens of thousands,
       and in the first self-play game a ship fled off the map from such "danger" and another chased it
       there for the kill.) $damage: gun index => enemy index => expected damage; $enemies: enemy index =>
       array('health', 'worth'). FlightDecider prices its fire the same way. */
    public static function removed(array $damage, array $enemies)
    {
        $order = array();
        foreach ($damage as $gun => $row) {
            $order[$gun] = 0;
            foreach ($row as $e => $d) $order[$gun] = max($order[$gun], $d * $enemies[$e]['worth']);
        }
        arsort($order);

        $booked = array();
        $total = 0;
        foreach (array_keys($order) as $gun) {
            $bestAdd = 0;
            $bestTarget = null;
            foreach ($damage[$gun] as $e => $d) {
                $room = $enemies[$e]['health'] - (isset($booked[$e]) ? $booked[$e] : 0);
                $add = max(0, min($d, $room)) * $enemies[$e]['worth'];
                if ($add > $bestAdd) {
                    $bestAdd = $add;
                    $bestTarget = $e;
                }
            }
            if ($bestTarget === null) continue;
            $booked[$bestTarget] = (isset($booked[$bestTarget]) ? $booked[$bestTarget] : 0) + $damage[$gun][$bestTarget];
            $total += $bestAdd;
        }
        return $total;
    }

    /* The value a unit should lose to $incoming - a list of (expected damage, exposure) per enemy -
       counted no further than the damage that finishes it ($health): each enemy's share weighed by the
       section it meets, the whole scaled down when it is more than the unit has left. */
    public static function lost(array $incoming, $health, $worth)
    {
        $raw = 0;
        $weighted = 0;
        foreach ($incoming as $one) {
            $raw += $one[0];
            $weighted += $one[0] * $one[1];
        }
        if ($raw > $health) $weighted *= $health / $raw;
        return $weighted * $worth;
    }

    /* Enemy index => is the line of fire clear - from the ship to the enemy, or ($inward) from the enemy to
       the ship, as the engine asks it from the shooter: asked once a position, not once a gun. */
    private static function lines(AIView $view, $ship, array $enemies, $inward = false)
    {
        $clear = array();
        foreach ($enemies as $e => $enemy) $clear[$e] = $inward ? $view->clearLine($enemy['unit'], $ship) : $view->clearLine($ship, $enemy['unit']);
        return $clear;
    }

    //Hexes from the ship to the nearest of $units (0 when there are none).
    private static function nearest($ship, array $units)
    {
        $nearest = PHP_INT_MAX;
        foreach ($units as $unit) $nearest = min($nearest, $ship->getHexPos()->distanceTo($unit->getHexPos()));
        return ($nearest === PHP_INT_MAX) ? 0 : $nearest;
    }

    /* The trade a turn on, every unit having flown straight on (AIView::ahead): the guns that will be
       loaded then - less a slow one that fires this turn - against every enemy, all of whom move again
       in an order nobody knows yet: fire at them counts MOVE_UNMOVED_OFFENCE, theirs bears from any arc
       at MOVE_UNMOVED_DANGER_NEXT. */
    private static function nextTerms(AIView $view, $ship, array $context, array $firedSlow)
    {
        $clear = self::lines($view, $ship, $context['enemies']);
        $damage = array();
        foreach ($context['guns'] as $index => $gun) {
            if (!$gun['next'] || isset($firedSlow[$index])) continue;
            foreach ($context['enemies'] as $e => $enemy) {
                if (!$gun['weapon']->ignoresLoS && !$clear[$e]) continue;
                $d = $view->estimate($ship, $gun['weapon'], $enemy['unit'], true) * $gun['count'] * AIConfig::MOVE_UNMOVED_OFFENCE;
                if ($d > 0) $damage[$index][$e] = $d;
            }
        }

        $inward = self::lines($view, $ship, $context['enemies'], true);
        $incoming = array();
        foreach ($context['enemies'] as $e => $enemy) {
            $d = 0;
            foreach ($enemy['any'] as $kind) {
                if (!$kind['next'] || (!$inward[$e] && !$kind['weapon']->ignoresLoS)) continue;
                $d += $view->estimate($enemy['unit'], $kind['weapon'], $ship, false) * $kind['count'];
            }
            if ($d > 0) $incoming[] = array($d * AIConfig::MOVE_UNMOVED_DANGER_NEXT, $view->exposure($ship, $enemy['unit']));
        }

        return array('offence' => self::removed($damage, $context['enemies']), 'danger' => self::lost($incoming, $context['health'], $context['worth']),
            'nearest' => self::nearest($ship, $context['quarry']));
    }
}
