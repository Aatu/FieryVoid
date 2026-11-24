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
 * LEVELS (stage T5f; the user's notes on game 4774): what the flight's own losses count for against the damage it deals
 * (the skill 'flightDanger' - Easy bold, Normal warier, Hard warier still), Hard pricing every jinking level on its best
 * places ('jink' = 'any': jinking takes the same 5% off both sides, so the trade turns where one side's chances run out
 * first), Easy's score noise, and each target's health from the flight's side (ShipDecider::targetsFrom).
 *
 * NOT YET: pivots and combat pivots, docking and launching (the bot's flights start on the map),
 * Fighter Missions, the search for the hexes the enemy covers least (T5f-2), flights working together (T5f-3).
 */
class FlightDecider
{
    //What the last orders() did, for tac_ai_log: missions given, flights jinking, flights that had to fire (stage T6g), Hunter-Killers (M2).
    public static $last = array('strike' => 0, 'intercept' => 0, 'escort' => 0, 'jinking' => 0, 'searched' => 0, 'found' => 0, 'forced' => 0, 'hunt' => 0, 'ramming' => 0);

    //Stage T6g: what a place where a flight that must fire cannot is marked down by - more than any other term can make up.
    const MUST = 1.0e6;

    public static function reset()
    {
        self::$last = array('strike' => 0, 'intercept' => 0, 'escort' => 0, 'jinking' => 0, 'searched' => 0, 'found' => 0, 'forced' => 0, 'hunt' => 0, 'ramming' => 0);
    }

    //For the harness and scratch checks only: set to an array and rows() appends its six best places, terms and all.
    public static $trace = null;

    /* Stage T8 (decision time): the flight's guns' expected damage at each enemy from one hex at one jinking level, arcs left out
       (AIView::estimate, $needArc false) - per flight decision. As for a ship (ShipDecider::$arcFree): what a gun does from a hex
       does not depend on which way the flight points or how fast it goes (checked over 4,752 gun / target pairs, flights jinking
       included), only whether the arc holds the target - and a flight's places are judged once per facing. */
    private static $arcFree = array();

    //Stage T8: this flight's share of its submission's CPU time (set by MoveDecider around each unit, then cleared); null = no budget.
    public static $share = null;
    public static $lastShare = null; //...the share the last rows() had (for the harnesses)

    /* The rows for one flight: a ShipMover plan, with jinking levels after it. $view is the bot's
       masked view of the Movement phase; $hazards the board, built once for the decision. */
    public static function rows(AIView $view, ShipMover $mover, $flight, Hazards $hazards)
    {
        self::$arcFree = array();
        /* Stage T8: the flight's share of its submission's CPU time, when MoveDecider gives one (AIConfig::MOVE_SUBMISSION_SECONDS) -
           the search stops at MOVE_SEARCH_SHARE of it (plainest plans first), the coverage search is not begun past MOVE_JUDGE_SHARE's
           start, and the judging stops at the end with at least one place judged. No share: as before, unbounded. */
        $started = AIConfig::cpuSeconds();
        $deadline = (self::$share !== null) ? $started + max(0.001, (float)self::$share) : null;
        $searchEnd = ($deadline === null) ? null : $started + (float)self::$share * AIConfig::MOVE_SEARCH_SHARE;
        self::$lastShare = self::$share;
        $plans = $mover->enumerate(AIConfig::FLIGHT_PLANS, $searchEnd);
        if (count($plans) == 0) return MoveDecider::choose($mover, $hazards->blocked, $hazards->bounds);

        $context = self::context($view, $flight);
        $start = $flight->getHexPos();

        //Stage T5f-2: the places around the targets the enemy's guns cover least, which the plainest-first search never reaches.
        if ($searchEnd === null || AIConfig::cpuSeconds() < $searchEnd) {
            foreach (self::coverageSearch($view, $mover, $flight, $plans, $context, $hazards, $start, $searchEnd) as $plan) $plans[] = $plan;
        }
        //Stage T9, M2: a flight designed to ram also asks for plans that END where its quarry will be at the Fire phase
        if ($context['ram'] !== null && ($searchEnd === null || AIConfig::cpuSeconds() < $searchEnd)) {
            foreach (self::ramSearch($view, $mover, $flight, $plans, $context) as $plan) $plans[] = $plan;
        }

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

        //Each place, as it is and jinking all the thrust allows: array of jinking levels => its terms. (Stage T8: until the share is spent.)
        $judged = $view->atFireTime(function () use ($view, $flight, $plans, $groups, $jinkable, $context, $hazards, $start, $deadline) {
            $out = array();
            foreach ($groups as $key => $indexes) {
                if ($deadline !== null && count($out) > 0 && AIConfig::cpuSeconds() > $deadline) break;
                $out[$key] = array(0 => self::judge($view, $flight, $plans[$indexes[0]], 0, $context, $hazards, $start));
                $room = $jinkable[$key][1];
                if ($room > 0) $out[$key][$room] = self::judge($view, $flight, $plans[$jinkable[$key][0]], $room, $context, $hazards, $start);
            }
            return $out;
        }, array((int)$flight->id));

        //Nothing in reach from anywhere: close on the target, or keep with the fleet when it is further than two turns off.
        $inReach = false;
        foreach ($judged as $variants) {
            foreach ($variants as $j) if ($j['offence'] > 0) $inReach = true;
        }
        //(a flight designed to ram never falls back to escorting: it hunts - see $score below)
        $farOff = ($context['ram'] === null) && ($context['nearest'] > 2 * self::reach($plans, $context['guns']));
        self::$last[!$inReach && $farOff && !$context['lastStand'] ? 'escort' : $context['mission']]++;

        /* A SIDE DOWN TO ITS FIGHTERS (stage T6g, AIConfig::FLIGHT_MAX_IDLE_TURNS): with no fleet to keep with, each hex short of
           the target costs at every distance - the flight does not fly further off than it must; and a flight that MUST fire this
           turn goes where a gun of it has a solid shot (FLIGHT_MUST_CHANCE, its jinking counted: an enemy moving after it must not
           take the shot away - the first self-play batch's forced flights jinked their chances down to the 5% floor and lost them),
           else where it has any shot FireDecider takes, else as near as it can get - the best such place by the usual terms. */
        $must = $context['mustShoot'];
        $tier = 0; //2: a solid shot can be had this turn; 1: only a shot over the floor; 0: none
        $leashed = false; //a place within FLIGHT_LEASH of the target can be reached this turn
        foreach ($judged as $variants) {
            foreach ($variants as $j) {
                if ($must) $tier = max($tier, ($j['shoots'] >= AIConfig::FLIGHT_MUST_CHANCE) ? 2 : (($j['shoots'] > 0) ? 1 : 0));
                if ($j['leash'] == 0) $leashed = true;
            }
        }
        if ($must) self::$last['forced']++;

        /* Stage T9, M2 - A FLIGHT DESIGNED TO RAM (the Orieni Hunter-Killers; the user, 2026-11-23: units designed to ram "can and should
           always Ram"). Where some place ends in a quarry's hex at the Fire phase, only those places are chosen from, by what the ram
           should do there (judge()'s 'ram' - the flight's own speed and jinking lower its chance, so the slower, unjinked way in wins) and
           the board's hazards: rams resolve before the other fire, so the fire the flight would take there counts for nothing. Else it
           HUNTS: each hex short of its nearest quarry costs RAM_PURSUIT times a flight's closing, speed beyond the chase speed as a hex
           short (it must still be able to stop on its quarry), and the fire it would take only RAM_DANGER_SHARE as much. */
        $ramming = false;
        if ($context['ram'] !== null) {
            foreach ($judged as $variants) {
                foreach ($variants as $j) if ($j['ram'] > 0) $ramming = true;
            }
            if ($ramming) self::$last['ramming']++;
        }
        $score = function (array $j) use ($context, $inReach, $farOff, $must, $tier, $leashed, $ramming) {
            if ($context['ram'] !== null) {
                //(never jinking into a ram: it lowers the ram's chance and buys nothing - and where a ram already finishes the target
                //either way the two would tie)
                if ($ramming) return ($j['ram'] > 0 && $j['jink'] == 0) ? $j['ram'] - $j['hazard'] : -self::MUST;
                return -$context['ramDanger'] * $j['danger'] - $j['hazard']
                    - AIConfig::FLIGHT_CLOSING_PER_HEX * (AIConfig::RAM_PURSUIT * $j['short'] + $j['fast']);
            }
            $s = $j['offence'] - $context['dangerWeight'] * $j['danger'] - $j['hazard'] - $context['support'] * $j['apart'];
            //down to its fighters: within FLIGHT_LEASH of the target where it can be, else as near as it gets - before anything else
            if ($context['lastStand']) $s -= $leashed ? (($j['leash'] > 0) ? self::MUST : 0) : self::MUST * $j['leash'];
            if ($context['lastStand']) $s -= AIConfig::FLIGHT_CLOSING_PER_HEX * ($j['short'] + $j['fast']);
            else if (!$inReach) $s -= $farOff ? AIConfig::FLIGHT_TETHER_PER_HEX * $j['beyondFleet'] : AIConfig::FLIGHT_CLOSING_PER_HEX * $j['short'];
            if ($must && $tier == 2) $s -= ($j['shoots'] >= AIConfig::FLIGHT_MUST_CHANCE) ? 0 : self::MUST;
            else if ($must && $tier == 1) $s -= ($j['shoots'] > 0) ? 0 : self::MUST;
            else if ($must) $s -= self::MUST * ($j['short'] + $j['fast']); //as near as it can get - without outrunning the chase
            return $s;
        };

        /* HARD jinks at whatever level pays (stage T5f, the user's ruling on game 4774: "balance having reasonable hit chances
           vs reducing chances of being hit to near 0" - there, 3 hexes off a CCEW ship, jinking 4-6). Jinking takes the same
           5% off both sides' chances, so the trade turns only where one side's chances run out first: every level between
           none and the most the thrust allows is priced, on the places that already score best (FLIGHT_JINK_PLACES). */
        if ($view->skill('jink') === 'any' && ($deadline === null || AIConfig::cpuSeconds() <= $deadline)) {
            $top = array();
            foreach ($judged as $key => $variants) {
                $top[$key] = max(array_map($score, $variants));
            }
            arsort($top);
            $view->atFireTime(function () use ($view, $flight, $plans, $jinkable, $context, $hazards, $start, &$judged, $top) {
                foreach (array_slice(array_keys($top), 0, AIConfig::FLIGHT_JINK_PLACES) as $key) {
                    for ($level = 1; $level < $jinkable[$key][1]; $level++) $judged[$key][$level] = self::judge($view, $flight, $plans[$jinkable[$key][0]], $level, $context, $hazards, $start);
                }
            }, array((int)$flight->id));
        }

        /* Easy's score noise (stage T5f, plan 5.8): each place judged a little better or worse than it is, a draw fixed by the
           seat's seed - in hexes of the flight's own value, as a ship's. */
        $noise = (float)$view->skill('noise') * AIConfig::MOVE_HEX_VALUE * $view->value($flight);

        $best = null;
        $bestScore = null;
        $scores = array();
        foreach ($judged as $key => $variants) {
            foreach ($variants as $levels => $j) {
                $s = $score($j);
                $scores[$key . ($levels > 0 ? ' jink ' . $levels : '')] = array($s, $j);
                if ($noise > 0) $s += $noise * $view->noise('flight|' . $flight->id . '|' . $key . '|' . $levels);
                if ($bestScore === null || $s > $bestScore) {
                    $bestScore = $s;
                    $best = array($key, $levels);
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
        list($key, $levels) = $best;
        $jink = ($levels > 0);
        $pick = $jink ? $jinkable[$key][0] : $groups[$key][0];

        $plotted = self::replay($mover, $plans[$pick]);
        if ($plotted === null) return MoveDecider::choose($mover, $hazards->blocked, $hazards->bounds); //cannot happen: the plan was found this way
        if ($jink && $plotted->jinkLevels($levels) > 0) self::$last['jinking']++;

        return $plotted->rows();
    }

    /* THE COVERAGE SEARCH (stage T5f-2; the user's note on game 4774: "Hard fighters could also find hexes where there is little
       or no light weapon coverage from the enemy and try to position themselves there. Normal mode could do a simpler search
       for this positioning too"). The plainest-first search reaches only the near-straight cone of a fast flight's places (133
       of the ~550 hexes a speed-9 Aurora can end in). So every hex within FLIGHT_COVERAGE_RANGE of a target the flight could
       reach, facing that target (a flight's guns bear forward), is judged as if the flight stood there - the real fire both
       ways, the enemy's arcs where it has moved - and the best of those the search did not reach (the level's skill
       'coverageGoals': Normal a few, Hard more, Easy none) are asked of ShipMover::enumerateToward. Returns the plans found;
       rows() judges them again with their real paths, among all the others.
       Each place gets an aimed search of AIConfig::FLIGHT_COVERAGE_PLANS, and up to FLIGHT_COVERAGE_TRIES x 'coverageGoals' places
       are asked (the user's notes on game 5826: with 2 and 3x an Aurora moving last, heading away from all three enemies, reached
       none of its 12 places - the loop back needed a longer search, and the best-scored places can all lie beyond the enemy, out of
       any plan's reach - and ended 9-11 hexes off with no shot). $until (CPU seconds, null = no budget): no place is asked once the
       flight's search time is spent. */
    private static function coverageSearch(AIView $view, ShipMover $mover, $flight, array $plans, array $context, Hazards $hazards, OffsetCoordinate $start, $until = null)
    {
        $wanted = (int)$view->skill('coverageGoals');
        if ($wanted <= 0 || count($context['guns']) == 0) return array();

        $reached = array();
        $speed = 0;
        foreach ($plans as $plan) {
            $reached[$plan['position']->q . ',' . $plan['position']->r . ',' . $plan['facing']] = true;
            $speed = max($speed, (int)$plan['speed']);
        }
        $last = $flight->getLastMovement();
        $now = $last ? (int)$last->speed : $speed;

        //Each hex near a target in reach, facing it: "q,r,facing" => the place as a plan (no path: the board is asked again later)
        $candidates = array();
        foreach ($context['enemies'] as $enemy) {
            $at = $enemy['unit']->getHexPos();
            if ($start->distanceTo($at) > $speed + AIConfig::FLIGHT_COVERAGE_RANGE) continue;
            $toward = Mathlib::hexCoToPixel($at);
            foreach (self::around($at, AIConfig::FLIGHT_COVERAGE_RANGE) as $hex) {
                if ($start->distanceTo($hex) > $speed - 1) continue; //further, a plan rarely has the thrust left to turn and face it
                $facing = ($hex->equals($at)) ? (int)$last->facing
                    : ((int)round(Mathlib::getCompassHeadingOfPoint(Mathlib::hexCoToPixel($hex), $toward) / 60)) % 6;
                $key = $hex->q . ',' . $hex->r . ',' . $facing;
                if (isset($reached[$key]) || isset($candidates[$key])) continue;
                $candidates[$key] = array('position' => $hex, 'heading' => $facing, 'facing' => $facing, 'speed' => $now, 'path' => array());
            }
        }
        if (count($candidates) == 0) return array();

        $scored = $view->atFireTime(function () use ($view, $flight, $candidates, $context, $hazards, $start) {
            $out = array();
            foreach ($candidates as $key => $place) {
                $j = self::judge($view, $flight, $place, 0, $context, $hazards, $start);
                $out[$key] = $j['offence'] - $context['dangerWeight'] * $j['danger'] - $j['hazard'];
            }
            return $out;
        }, array((int)$flight->id));
        arsort($scored);

        //the best places first, until $wanted of them are reached (a place may be out of the thrust's reach: then the next)
        $found = array();
        $asked = 0;
        $reachedGoals = 0;
        foreach (array_keys($scored) as $key) {
            if ($reachedGoals >= $wanted || $asked >= AIConfig::FLIGHT_COVERAGE_TRIES * $wanted || $scored[$key] <= 0) break;
            if ($until !== null && AIConfig::cpuSeconds() > $until) break;
            $asked++;
            $place = $candidates[$key];
            $got = $mover->enumerateToward(array($place['position']->q . ',' . $place['position']->r => $place['facing']), AIConfig::FLIGHT_COVERAGE_PLANS);
            foreach ($got as $plan) $found[] = $plan;
            if (count($got) > 0) $reachedGoals++;
            if (is_array(self::$trace)) self::$trace[] = sprintf('  coverage goal %-10s %8.1f  %d plans', $key, $scored[$key], count($got));
        }
        self::$last['searched'] += $asked;
        self::$last['found'] += $reachedGoals;
        return $found;
    }

    //Every hex within $radius of $center, $center included.
    private static function around(OffsetCoordinate $center, $radius)
    {
        $seen = array($center->q . ',' . $center->r => $center);
        $frontier = array($center);
        for ($step = 0; $step < $radius; $step++) {
            $next = array();
            foreach ($frontier as $hex) {
                for ($direction = 0; $direction < 6; $direction++) {
                    $n = $hex->moveToDirection($direction);
                    $key = $n->q . ',' . $n->r;
                    if (isset($seen[$key])) continue;
                    $seen[$key] = $n;
                    $next[] = $n;
                }
            }
            $frontier = $next;
        }
        return array_values($seen);
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
        $pileOn = (float)$view->skill('pileOn');
        $enemyFlights = false;
        $enemyFlightsUnmoved = false;
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
            /* HARD piles onto a damaged target (stage T5f-3, the skill 'pileOn' - user, 2026-11-19): worth more by the share of
               its value already lost, and the fleet's focus target (MoveDecider) by half the skill more. */
            $pile = 1.0;
            if ($pileOn > 0) {
                $full = max(1, (int)$enemy->pointCost + (int)$enemy->pointCostEnh);
                $pile += $pileOn * max(0, 1 - $view->value($enemy) / $full);
                if ($view->focus !== null && (int)$enemy->id === (int)$view->focus) $pile += $pileOn / 2;
            }
            $enemies[] = array('unit' => $enemy, 'moved' => $moved, 'arced' => array_values($arced), 'any' => array_values($any),
                'health' => $health, 'worth' => $view->value($enemy) / $health * ($raider ? AIConfig::FLIGHT_INTERCEPT_WEIGHT : 1) * $pile);
            if ($raider) $raiders[] = $enemy;
            if ($enemy instanceof FighterFlight) {
                $enemyFlights = true;
                if (!$moved) $enemyFlightsUnmoved = true;
            }
        }

        $closing = count($raiders) > 0 ? $raiders : array_map(function ($e) { return $e['unit']; }, $enemies);

        /* Stage T9, M2: a flight DESIGNED to ram (the Hunter-Killers - Posture::flightRams: its craft that can ram) closes on its QUARRY
           alone - the largest enemy ships within reach first (Posture::ramQuarry) - and rams in judge(). */
        $ramCraft = Posture::flightRams($view, $flight);
        $quarry = array();
        if (count($ramCraft) > 0) {
            $quarry = Posture::ramQuarry($flight, array_map(function ($e) { return $e['unit']; }, $enemies));
            if (count($quarry) > 0) $closing = $quarry;
        }
        $quarryIds = array();
        foreach ($quarry as $unit) $quarryIds[(int)$unit->id] = true;

        $nearest = PHP_INT_MAX;
        $chase = PHP_INT_MAX; //stage T6g: the speed closing in may build up to - the nearest target's, FLIGHT_CHASE_MARGIN over
        foreach ($closing as $target) {
            $d = $flight->getHexPos()->distanceTo($target->getHexPos());
            if ($d >= $nearest) continue;
            $nearest = $d;
            $last = $target->getLastMovement();
            $chase = ($last ? max(0, (int)$last->speed) : 0) + AIConfig::FLIGHT_CHASE_MARGIN;
        }

        /* HARD's mutual support (stage T5f-3, the skill 'support'): a cost in hexes of the flight's own value for each hex beyond
           FLIGHT_SUPPORT_RANGE of the nearest other flight of the bot, while the enemy has flights - in full while they have yet
           to move (they could fall on a flight caught alone), a quarter once they have: a flight moving after the enemy's
           fighters knows where they are, and is the likelier to go for them (the user, 2026-11-19). */
        $support = 0.0;
        if ((float)$view->skill('support') > 0 && $enemyFlights) {
            $support = (float)$view->skill('support') * AIConfig::MOVE_HEX_VALUE * $view->value($flight) * ($enemyFlightsUnmoved ? 1.0 : 0.25);
        }

        $health = $view->healthLeft($flight);
        $lastStand = Posture::lastStand($view); //stage T6g: the side down to its fighters
        $hunting = count($ramCraft) > 0 && count($quarry) > 0;
        return array('guns' => $guns, 'enemies' => $enemies, 'closing' => $closing, 'nearest' => $nearest, 'chase' => $chase,
            'lastStand' => $lastStand, 'mustShoot' => $lastStand && isset(Posture::$mustShoot[(int)$flight->id]),
            //stage T9, M2: the ram (one craft's - they are all alike), how many craft can ram, and the quarry's ids
            'ram' => $hunting ? $ramCraft[0]['ram'] : null, 'ramCount' => count($ramCraft), 'quarry' => $quarryIds,
            //...and what the fire it takes hunting counts for: a designed rammer's (a Delegor's) - not the level's fighter caution
            'ramDanger' => (float)$view->skill('shipDanger') * AIConfig::RAM_DANGER_SHARE,
            'mission' => $hunting ? 'hunt' : (count($raiders) > 0 ? 'intercept' : 'strike'), 'health' => $health, 'worth' => $view->value($flight) / $health,
            //the level's skills (stage T5f): what the flight's own losses count for, an enemy yet to move, staying together
            'dangerWeight' => (float)$view->skill('flightDanger'), 'unmovedDanger' => (float)$view->skill('unmovedDanger'), 'support' => $support);
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
            //(each pair priced once a hex and jinking level - $arcFree - and this facing asks only the arcs: estimate() with the arc is
            //exactly onArc ? the arc-free answer : 0; its range test asked first, the cheaper)
            $hexKey = $plan['position']->q . ',' . $plan['position']->r . '|' . (int)$jink;
            $from = $flight->getHexPos();
            foreach ($context['guns'] as $g => $gun) {
                foreach ($context['enemies'] as $e => $enemy) {
                    if (!$gun['weapon']->ignoresLoS && !$outward[$e]) continue;
                    if ($gun['weapon']->range != 0 && $from->distanceTo($enemy['unit']->getHexPos()) > $gun['weapon']->range) continue;
                    if (!$view->onArc($flight, $gun['weapon'], $enemy['unit'])) continue;
                    if (!isset(self::$arcFree[$hexKey][$g][$e])) self::$arcFree[$hexKey][$g][$e] = (float)$view->estimate($flight, $gun['weapon'], $enemy['unit'], false);
                    $d = self::$arcFree[$hexKey][$g][$e] * $gun['count'] * ($enemy['moved'] ? 1 : AIConfig::MOVE_UNMOVED_OFFENCE);
                    if ($d > 0) $damage[$g][$e] = $d;
                }
                foreach ($context['closing'] as $target) $short = min($short, max(0, $flight->getHexPos()->distanceTo($target->getHexPos()) - (float)$gun['weapon']->range));
            }
            if (count($context['guns']) == 0) {
                foreach ($context['closing'] as $target) $short = min($short, $flight->getHexPos()->distanceTo($target->getHexPos()));
            }

            /* Stage T6g: a flight that must fire this turn needs a place where a gun of it would really be fired - at or above
               FireDecider's floor (an expected damage above 0 may come from a 1% chance FireDecider never takes), and its higher
               floor for a shot from exactly between two of the target's sections (AIView::onEdge, FIRE_MIN_CHANCE_EDGE). */
            //stage T6g: how many hexes beyond FLIGHT_LEASH of the nearest target it closes on (where they will be at the Fire phase)
            $leash = 0;
            if ($context['lastStand']) {
                $apart = PHP_INT_MAX;
                foreach ($context['closing'] as $target) $apart = min($apart, $flight->getHexPos()->distanceTo($target->getHexPos()));
                $leash = ($apart === PHP_INT_MAX) ? 0 : max(0, $apart - AIConfig::FLIGHT_LEASH);
            }

            $shoots = 0; //the best chance at the Fire phase of a shot FireDecider would take from here (its jinking counted), or 0
            if ($context['mustShoot']) {
                foreach ($context['guns'] as $gun) {
                    $floor = FireDecider::minimumChance($gun['weapon'], true, $view->skill('pulsePremium'));
                    foreach ($context['enemies'] as $e => $enemy) {
                        if (!$gun['weapon']->ignoresLoS && !$outward[$e]) continue;
                        $need = $view->onEdge($flight, $enemy['unit']) ? max($floor, AIConfig::FIRE_MIN_CHANCE_EDGE) : $floor;
                        $chance = $view->chanceOf($flight, $gun['weapon'], $enemy['unit'], true);
                        if ($chance >= $need) $shoots = max($shoots, $chance);
                    }
                }
            }

            //danger: an enemy that has moved from its arcs; one yet to move with every gun
            $incoming = array();
            foreach ($context['enemies'] as $e => $enemy) {
                $d = 0;
                foreach ($enemy['moved'] ? $enemy['arced'] : $enemy['any'] as $kind) {
                    if (!$inward[$e] && !$kind['weapon']->ignoresLoS) continue;
                    $d += $view->estimate($enemy['unit'], $kind['weapon'], $flight, $enemy['moved']) * $kind['count'];
                }
                if ($d > 0) $incoming[] = array($d * ($enemy['moved'] ? 1 : $context['unmovedDanger']), 1.0);
            }

            //escort: how far beyond the fleet; mutual support (Hard): how far from the nearest other flight of the bot
            $fleet = PHP_INT_MAX;
            $wing = PHP_INT_MAX;
            foreach ($view->myUnits() as $mine) {
                if ((int)$mine->id === (int)$flight->id) continue;
                if ($mine instanceof FighterFlight) $wing = min($wing, $flight->getHexPos()->distanceTo($mine->getHexPos()));
                else $fleet = min($fleet, $flight->getHexPos()->distanceTo($mine->getHexPos()));
            }

            $board = $hazards->judge($flight, $start, $plan, $context['worth']);
            //each target's health from where the flight stands: the section its hits go through (stage T5f - Normal and Hard)
            $targets = ShipDecider::targetsFrom($view, $flight, $context['enemies']);

            /* Stage T9, M2: the best ram from here - a quarry standing in this hex at the Fire phase (judge() is asked inside
               atFireTime: an enemy yet to move stands where the level forecasts it, or straight on), the flight's speed and jinking
               as this place has them. */
            $ram = 0.0;
            if ($context['ram'] !== null) {
                foreach ($context['enemies'] as $enemy) {
                    if (!isset($context['quarry'][(int)$enemy['unit']->id])) continue;
                    if ($flight->getHexPos()->distanceTo($enemy['unit']->getHexPos()) != 0) continue;
                    $ram = max($ram, Posture::flightRamValue($view, $flight, $context['ram'], $enemy['unit'], $context['ramCount'], $enemy['health'], $enemy['worth']));
                }
            }
        } finally {
            if ($jink > 0) array_pop($flight->movement);
            array_pop($flight->movement);
        }

        return array('offence' => ShipDecider::removed($damage, $targets), 'danger' => ShipDecider::lost($incoming, $context['health'], $context['worth']),
            'short' => ($short === PHP_INT_MAX) ? 0 : $short, 'beyondFleet' => ($fleet === PHP_INT_MAX) ? 0 : max(0, $fleet - AIConfig::FLIGHT_ESCORT_RANGE),
            'apart' => ($wing === PHP_INT_MAX) ? 0 : max(0, $wing - AIConfig::FLIGHT_SUPPORT_RANGE),
            'hazard' => $board['total'], 'shoots' => $shoots, 'fast' => max(0, (int)$plan['speed'] - $context['chase']), 'leash' => $leash,
            'ram' => $ram, 'jink' => (int)$jink);
    }

    /* Stage T9, M2: the plans the plainest-first search does not offer a flight designed to ram - it lists every plan of few orders
       before any of many, and running a flight up to its top speed is many orders (self-play 6158: three Hunter-Killer flights crept
       up at speed 4-6, none in reach of a ram, and were shot down). For each of its nearest quarry (RAM_AIMED_QUARRY), where the
       quarry should stand at the Fire phase (where it has moved to, or the level's forecast / straight on): plans that END there
       when the flight can get there this turn (its speed and all its thrust - PowerDecider::maxMove), else plans that end as far
       along the way to it as that allows. ShipMover::enumerateToward, RAM_AIMED_PLANS a goal, any facing (a ram has no arc). */
    private static function ramSearch(AIView $view, ShipMover $mover, $flight, array $plans, array $context)
    {
        $reached = array();
        foreach ($plans as $plan) $reached[$plan['position']->q . ',' . $plan['position']->r] = true;
        $start = $flight->getHexPos();
        $reach = PowerDecider::maxMove($flight);
        $goals = $view->atFireTime(function () use ($context, $start, $reach, $reached) {
            $near = array();
            foreach ($context['enemies'] as $enemy) {
                if (!isset($context['quarry'][(int)$enemy['unit']->id])) continue;
                $at = $enemy['unit']->getHexPos();
                $near[] = array($start->distanceTo($at), $at);
            }
            usort($near, function ($a, $b) { return $a[0] - $b[0]; });
            $out = array();
            foreach (array_slice($near, 0, AIConfig::RAM_AIMED_QUARRY) as $one) {
                list($distance, $at) = $one;
                //in reach: its hex; else the hex as far along the way to it as the flight can go
                $goal = ($distance <= $reach) ? $at : self::along($start, $at, $reach);
                $key = $goal->q . ',' . $goal->r;
                if (!isset($reached[$key])) $out[$key] = true;
            }
            return $out;
        }, array((int)$flight->id));

        $found = array();
        foreach (array_keys($goals) as $key) {
            foreach ($mover->enumerateToward(array($key => true), AIConfig::RAM_AIMED_PLANS) as $plan) $found[] = $plan;
        }
        return $found;
    }

    //The hex $steps hexes from $from on the way to $to (each step to the neighbour nearest it).
    private static function along(OffsetCoordinate $from, OffsetCoordinate $to, $steps)
    {
        $at = $from;
        for ($i = 0; $i < $steps && $at->distanceTo($to) > 0; $i++) {
            $best = null;
            for ($direction = 0; $direction < 6; $direction++) {
                $next = $at->moveToDirection($direction);
                if ($best === null || $next->distanceTo($to) < $best->distanceTo($to)) $best = $next;
            }
            $at = $best;
        }
        return $at;
    }
}
