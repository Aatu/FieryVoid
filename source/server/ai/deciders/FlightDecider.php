<?php
/**
 * CPU opponent - where a fighter flight goes (TACTICAL_AI_PLAN.md section 5.4; stage T4, "flight
 * tactics: escort, strike, intercept"; reworked 2026-11-19 on the user's notes on game 4750).
 *
 * The moves are ShipMover's: every plan it offers is one the page accepts (tests/ai/
 * shipMoverHarness.php replays them through movement.js, jinking included), and so is the jinking
 * added to the plan chosen. This class only CHOOSES among the places the flight can end this turn,
 * scoring each in named terms (principle P7), in "value removed" - expected damage times what a
 * point of the unit hit is worth (its value over the damage that finishes it, FireDecider's worth),
 * no target counted past the damage that finishes it (ShipDecider::removed / lost):
 *
 *   offence  what the flight's guns should do from there, in their arcs, at THIS turn's Fire phase
 *            (AIView::atFireTime: a unit yet to move flies straight on, and fire at it counts
 *            MOVE_UNMOVED_OFFENCE). EVERY enemy unit is a target; an enemy flight near the bot's ships
 *            is worth FLIGHT_INTERCEPT_WEIGHT more to a flight that can shoot fighters;
 *   danger   what the enemy's guns could do to the flight there (FLIGHT_DANGER_WEIGHT): an enemy that
 *            has moved from its arcs - a flight moving after it knows which guns bear -, one yet to move
 *            with every gun at MOVE_UNMOVED_DANGER (it may turn to bring them to bear). The enemy's EW is
 *            this turn's, in sight since Initial Orders closed - its CCEW included;
 *   jinking  each place is priced twice, as it is and with all the jinking the thrust left allows
 *            (5% a level off the flight's own fire and off the fire aimed at it), and the better kept;
 *   closing  with nothing in reach from anywhere this turn, each hex between the flight's guns' reach
 *            and its nearest target - the raiders near the bot's ships when there are any
 *            (FLIGHT_CLOSING_PER_HEX) - while that target is within two turns;
 *   escort   with the enemy further off than that, each hex beyond FLIGHT_ESCORT_RANGE of the nearest
 *            of the bot's ships (FLIGHT_TETHER_PER_HEX): a flight with nothing to do keeps with the
 *            fleet, rather than flying into the enemy alone;
 *   board    the hazards ShipDecider pays (Hazards, stage T5c): terrain, the map edge, drifting fields,
 *            meteor swarms, the mines the bot can see.
 *
 * Its MISSION is named for the log: INTERCEPT when an enemy flight is within FLIGHT_INTERCEPT_RANGE of
 * one of the bot's ships and the flight's guns can fire at fighters, STRIKE otherwise. Until 2026-11-19
 * an intercepting flight counted ONLY those raiders as targets: in game 4750 an Aurora flight moving
 * last, with the Frazi out of its reach, fled from two G'Quans it could have fired on from 2-5 hexes
 * (its danger priced every G'Quan gun whatever its arc).
 *
 * NOT YET: pivots and combat pivots, docking and launching (the bot's flights start on the map),
 * Fighter Missions, flights working together, levels (stage T8).
 */
class FlightDecider
{
    //What the last orders() did, for tac_ai_log: missions given, flights jinking.
    public static $last = array('strike' => 0, 'intercept' => 0, 'escort' => 0, 'jinking' => 0);

    public static function reset()
    {
        self::$last = array('strike' => 0, 'intercept' => 0, 'escort' => 0, 'jinking' => 0);
    }

    //For the harness and scratch checks only: set to an array and rows() appends its six best places, terms and all.
    public static $trace = null;

    /* The rows for one flight: a ShipMover plan, with jinking levels after it. $view is the bot's
       masked view of the Movement phase; $hazards the board, built once for the decision. */
    public static function rows(AIView $view, ShipMover $mover, $flight, Hazards $hazards)
    {
        $plans = $mover->enumerate(AIConfig::FLIGHT_PLANS);
        if (count($plans) == 0) return MoveDecider::choose($mover, $hazards->blocked, $hazards->bounds);

        $context = self::context($view, $flight);
        $start = $flight->getHexPos();

        /* One estimate per place and facing: that is all fire and danger depend on - and per jinking level. The most a
           place allows is what the plan to it that leaves most thrust can buy. */
        $groups = array();
        foreach ($plans as $index => $plan) $groups[$plan['position']->q . ',' . $plan['position']->r . ',' . $plan['facing']][] = $index;
        $jinkable = array();
        foreach ($groups as $key => $indexes) {
            $pick = $indexes[0];
            foreach ($indexes as $index) {
                if ($plans[$index]['thrustLeft'] > $plans[$pick]['thrustLeft']) $pick = $index;
            }
            $jinkable[$key] = array($pick, self::jinkRoom($mover, $plans[$pick]));
        }

        $judged = $view->atFireTime(function () use ($view, $flight, $plans, $groups, $jinkable, $context, $hazards, $start) {
            $out = array();
            foreach ($groups as $key => $indexes) {
                $out[$key] = array(self::judge($view, $flight, $plans[$indexes[0]], 0, $context, $hazards, $start));
                if ($jinkable[$key][1] > 0) $out[$key][] = self::judge($view, $flight, $plans[$jinkable[$key][0]], $jinkable[$key][1], $context, $hazards, $start);
            }
            return $out;
        }, array((int)$flight->id));

        //Nothing in reach from anywhere: close on the target, or keep with the fleet when it is further than two turns off.
        $inReach = false;
        foreach ($judged as $variants) {
            foreach ($variants as $j) if ($j['offence'] > 0) $inReach = true;
        }
        $farOff = ($context['nearest'] > 2 * self::reach($plans, $context['guns']));
        self::$last[!$inReach && $farOff ? 'escort' : $context['mission']]++;

        $best = null;
        $bestScore = null;
        $scores = array();
        foreach ($judged as $key => $variants) {
            foreach ($variants as $v => $j) {
                $score = $j['offence'] - AIConfig::FLIGHT_DANGER_WEIGHT * $j['danger'] - $j['hazard'];
                if (!$inReach) $score -= $farOff ? AIConfig::FLIGHT_TETHER_PER_HEX * $j['beyondFleet'] : AIConfig::FLIGHT_CLOSING_PER_HEX * $j['short'];
                $scores[$key . ($v > 0 ? ' jink ' . $jinkable[$key][1] : '')] = array($score, $j);
                if ($bestScore === null || $score > $bestScore) {
                    $bestScore = $score;
                    $best = array($key, $v);
                }
            }
        }
        if (is_array(self::$trace)) {
            uasort($scores, function ($a, $b) { return $b[0] <=> $a[0]; });
            foreach (array_slice($scores, 0, 6, true) as $place => $t) {
                self::$trace[] = sprintf('  %8.1f  %-16s fire %.0f danger %.0f hazard %.0f short %d beyond %d', $t[0], $place, $t[1]['offence'], $t[1]['danger'], $t[1]['hazard'], $t[1]['short'], $t[1]['beyondFleet']);
            }
        }

        //The plan to that place: the plainest, or - when it jinks - the one leaving most thrust.
        list($key, $v) = $best;
        $jink = ($v > 0);
        $pick = $jink ? $jinkable[$key][0] : $groups[$key][0];

        $plotted = self::replay($mover, $plans[$pick]);
        if ($plotted === null) return MoveDecider::choose($mover, $hazards->blocked, $hazards->bounds); //cannot happen: the plan was found this way
        if ($jink && $plotted->jinkLevels($jinkable[$key][1]) > 0) self::$last['jinking']++;

        return $plotted->rows();
    }

    //The flight with one of its plans plotted again, or null.
    private static function replay(ShipMover $mover, array $plan)
    {
        $plotted = clone $mover;
        foreach ($plan['actions'] as $action) {
            if (!$plotted->apply($action)) return null;
        }
        return $plotted->isComplete() ? $plotted : null;
    }

    //The jinking levels a plan leaves room for (ShipMover::jinkRoom once it is plotted).
    private static function jinkRoom(ShipMover $mover, array $plan)
    {
        $plotted = self::replay($mover, $plan);
        return $plotted === null ? 0 : $plotted->jinkRoom();
    }

    /* What does not depend on the place: the flight's guns, the enemies (moved or not, their guns with and without arcs,
       what each is worth to this flight), whom it closes on, and its mission's name. A mine is not a target or a gun
       here: Hazards prices it. */
    private static function context(AIView $view, $flight)
    {
        $turn = $view->turn;
        $guns = self::gunGroups($view, $flight);

        $antiFighter = false;
        foreach ($view->mounts($flight) as $mount) {
            if ($view->plainGun($mount['weapon'], $mount['fighter']) && isset($mount['weapon']->fireControl[0])) $antiFighter = true;
        }
        $ships = array();
        foreach ($view->myUnits() as $mine) {
            if (!($mine instanceof FighterFlight)) $ships[] = $mine;
        }

        $enemies = array();
        $raiders = array();
        foreach ($view->enemies() as $enemy) {
            if (!empty($enemy->mine)) continue;
            $moved = !empty($enemy->base) || !empty($enemy->smallBase) || !empty($enemy->osat) || $enemy->getLastTurnMoved() >= $turn;

            $raider = false;
            if ($antiFighter && $enemy instanceof FighterFlight) {
                foreach ($ships as $mine) {
                    if ($enemy->getHexPos()->distanceTo($mine->getHexPos()) <= AIConfig::FLIGHT_INTERCEPT_RANGE) {
                        $raider = true;
                        break;
                    }
                }
            }

            //its guns that fire this turn: loaded (every player sees an enemy weapon's loading)
            $arced = array();
            $any = array();
            foreach ($view->mounts($enemy) as $mount) {
                $weapon = $mount['weapon'];
                if (!$view->plainGun($weapon, $mount['fighter']) || !$view->loaded($weapon)) continue;
                $base = get_class($weapon) . '|' . $weapon->firingMode;
                $withArc = $base . '|' . $weapon->startArc . '|' . $weapon->endArc;
                if (!isset($arced[$withArc])) $arced[$withArc] = array('weapon' => $weapon, 'count' => 0);
                $arced[$withArc]['count']++;
                if (!isset($any[$base])) $any[$base] = array('weapon' => $weapon, 'count' => 0);
                $any[$base]['count']++;
            }

            $health = $view->healthLeft($enemy);
            $enemies[] = array('unit' => $enemy, 'moved' => $moved, 'arced' => array_values($arced), 'any' => array_values($any),
                'health' => $health, 'worth' => $view->value($enemy) / $health * ($raider ? AIConfig::FLIGHT_INTERCEPT_WEIGHT : 1));
            if ($raider) $raiders[] = $enemy;
        }

        $closing = count($raiders) > 0 ? $raiders : array_map(function ($e) { return $e['unit']; }, $enemies);
        $nearest = PHP_INT_MAX;
        foreach ($closing as $target) $nearest = min($nearest, $flight->getHexPos()->distanceTo($target->getHexPos()));

        $health = $view->healthLeft($flight);
        return array('guns' => $guns, 'enemies' => $enemies, 'closing' => $closing, 'nearest' => $nearest,
            'mission' => count($raiders) > 0 ? 'intercept' : 'strike', 'health' => $health, 'worth' => $view->value($flight) / $health);
    }

    /* The flight's guns that will fire this turn, by kind: a flight's fighters carry the same guns,
       so one of each is asked and counted for all. array(array('weapon', 'count')). */
    private static function gunGroups(AIView $view, $flight)
    {
        $groups = array();
        foreach ($view->mounts($flight) as $mount) {
            $weapon = $mount['weapon'];
            if (!$view->plainGun($weapon, $mount['fighter']) || $weapon->ballistic) continue;
            if ($weapon->isOfflineOnTurn($view->turn) || !$view->loaded($weapon)) continue;
            $key = get_class($weapon) . ':' . $weapon->firingMode;
            if (!isset($groups[$key])) $groups[$key] = array('weapon' => $weapon, 'count' => 0);
            $groups[$key]['count']++;
        }
        return array_values($groups);
    }

    //How far the flight can bring its guns this turn: its fastest plan's hexes and its longest gun.
    private static function reach(array $plans, array $guns)
    {
        $speed = 0;
        foreach ($plans as $plan) $speed = max($speed, (int)$plan['speed']);
        $range = 0;
        foreach ($guns as $gun) $range = max($range, (float)$gun['weapon']->range);
        return $speed + $range;
    }

    /* Everything one end state is scored by, jinking $jink levels. The flight is put there for the asking - its jinking
       as a 'jink' row, which the hit formula reads both ways (Movement::getJinking) - and taken back. */
    private static function judge(AIView $view, $flight, array $plan, $jink, array $context, Hazards $hazards, OffsetCoordinate $start)
    {
        $flight->movement[] = new MovementOrder(-1, 'move', $plan['position'], 0, 0, $plan['speed'], $plan['heading'], $plan['facing'], false, $view->turn, 0, 0);
        if ($jink > 0) $flight->movement[] = new MovementOrder(-1, 'jink', $plan['position'], 0, 0, $plan['speed'], $plan['heading'], $plan['facing'], false, $view->turn, $jink, 0);
        try {
            //lines of fire, each way, once a place (line of sight is the expensive question on an asteroid map)
            $outward = array();
            $inward = array();
            foreach ($context['enemies'] as $e => $enemy) {
                $outward[$e] = $view->clearLine($flight, $enemy['unit']);
                $inward[$e] = $view->clearLine($enemy['unit'], $flight);
            }

            //offence: each kind of gun at every enemy, then given out as ShipDecider gives a ship's guns
            $damage = array();
            $short = PHP_INT_MAX;
            foreach ($context['guns'] as $g => $gun) {
                foreach ($context['enemies'] as $e => $enemy) {
                    if (!$gun['weapon']->ignoresLoS && !$outward[$e]) continue;
                    $d = $view->estimate($flight, $gun['weapon'], $enemy['unit'], true) * $gun['count'] * ($enemy['moved'] ? 1 : AIConfig::MOVE_UNMOVED_OFFENCE);
                    if ($d > 0) $damage[$g][$e] = $d;
                }
                foreach ($context['closing'] as $target) $short = min($short, max(0, $flight->getHexPos()->distanceTo($target->getHexPos()) - (float)$gun['weapon']->range));
            }
            if (count($context['guns']) == 0) {
                foreach ($context['closing'] as $target) $short = min($short, $flight->getHexPos()->distanceTo($target->getHexPos()));
            }

            //danger: an enemy that has moved from its arcs; one yet to move with every gun
            $incoming = array();
            foreach ($context['enemies'] as $e => $enemy) {
                $d = 0;
                foreach ($enemy['moved'] ? $enemy['arced'] : $enemy['any'] as $kind) {
                    if (!$inward[$e] && !$kind['weapon']->ignoresLoS) continue;
                    $d += $view->estimate($enemy['unit'], $kind['weapon'], $flight, $enemy['moved']) * $kind['count'];
                }
                if ($d > 0) $incoming[] = array($d * ($enemy['moved'] ? 1 : AIConfig::MOVE_UNMOVED_DANGER), 1.0);
            }

            //escort: how far beyond the fleet
            $fleet = PHP_INT_MAX;
            foreach ($view->myUnits() as $mine) {
                if ($mine instanceof FighterFlight || (int)$mine->id === (int)$flight->id) continue;
                $fleet = min($fleet, $flight->getHexPos()->distanceTo($mine->getHexPos()));
            }

            $board = $hazards->judge($flight, $start, $plan, $context['worth']);
        } finally {
            if ($jink > 0) array_pop($flight->movement);
            array_pop($flight->movement);
        }

        return array('offence' => ShipDecider::removed($damage, $context['enemies']), 'danger' => ShipDecider::lost($incoming, $context['health'], $context['worth']),
            'short' => ($short === PHP_INT_MAX) ? 0 : $short, 'beyondFleet' => ($fleet === PHP_INT_MAX) ? 0 : max(0, $fleet - AIConfig::FLIGHT_ESCORT_RANGE),
            'hazard' => $board['total']);
    }
}
