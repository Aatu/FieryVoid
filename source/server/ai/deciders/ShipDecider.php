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
 * rules.
 *
 * PIVOTS AND ROLLS (stage T5e), by the seat's level - the user's notes of 2026-11-19: rare, advanced moves.
 * Every level STOPS a pivot or roll carried over from last turn (the search tries the stop; leaving one
 * running costs MOVE_COST_LEFT_RUNNING). HARD tries, on the end of its best plans, a pivot started and
 * stopped the same turn either way (an agile hull's single and double pivots) and a roll; NORMAL only the
 * roll that frees the turn it wants next turn (the thruster swap); EASY neither (variants()). Each is priced
 * by the engine's -3 on this turn's fire, its cost, ending out of alignment without a gravitic drive (by the
 * share of the ship expected to survive - never below the share of its combat value it has left: "desperate"
 * needs damage), next turn with the sides a roll swaps, and the turn it frees. A PIVOT pays for itself this
 * turn: its next-turn terms are taken from its seed's facing (self-play showed fresh ships pivoting on turn 1
 * for a straight-on projection's sake).
 *
 * LEVELS (stage T5f): the view's skills (AIView::skill, AIConfig::SKILLS) decide what the ship tries and how it weighs it -
 * turn shortening, the overspeed cost, what an enemy yet to move counts for, the pivots and rolls above, Easy's score noise,
 * and whether a target's health is its primary alone (Easy) or its primary plus the section the ship's hits go through
 * (targetsFrom - the user's ruling on game 4774). Since stage T5f-7 an enemy SHIP yet to move stands where the level forecasts it
 * (EnemyForecast, set on the view by MoveDecider) instead of straight on - for every term, this turn's and next turn's.
 *
 * DECISION TIME (stage T5f-8): next turn's terms are asked only of the MOVE_NEXT_TURN_PLANS best end states by this turn's score
 * (judgeAll's two passes), and each ship has MOVE_DECISION_SECONDS of CPU time - the search, then each pass, stops when its share
 * is spent, with the plainest plans found and at least one judged whole.
 *
 * HARD's fleet focus and the one-ply reply (stage T5f-3; the reply rebuilt both-sided at T5f-6 and off at every level): focusFactor
 * and the bestReply branch of fireTerms. Interception is not counted here: it changes how much a volley does, hardly where it is
 * best fired from.
 */
class ShipDecider
{
    //One line per ship moved by the last orders(), for tac_ai_log.
    public static $last = array();

    public static function reset()
    {
        self::$last = array();
    }

    /* Stage T5f-8 (brought forward from T8 - the user, 2026-11-20: "move forward the decision time-improvements currently in Stage
       T8 to compensate for the increased work"): harness overrides of AIConfig::MOVE_NEXT_TURN_PLANS and MOVE_DECISION_SECONDS -
       null = AIConfig's; a budget of 0 = none (the levels harness: a plan must not depend on the machine). Nothing in a game sets
       them. */
    public static $nextTurnPlans = null;
    public static $budget = null;
    /* Stage T8: this decision's share of its SUBMISSION's CPU time (AIConfig::MOVE_SUBMISSION_SECONDS), set by MoveDecider around
       each unit and cleared again - the budget is then the lesser of the two. Null = no share; ignored where the budget is off. */
    public static $share = null;
    /* Stage T6c: null, or callable(array $plan): bool - the only plans rows() may choose from (a jump: JumpDecider). rows() returns
       null when the filter leaves none. Set around one rows() call and cleared again. */
    public static $planFilter = null;
    /* What the last rows() did: 'plans' found, 'judgedNow' (this turn's terms), 'judgedNext' (next turn's too - judged whole),
       'variants' judged whole, 'cut' (the budget stopped the search or the judging), 'choice' (the end state chosen, endKey()),
       'seconds' (CPU). And every rows() of the process by level - the self-play harness reads and empties it. */
    public static $lastTiming = array();
    public static $timings = array();

    /* Stage T8 (decision time, plan section 12 item 4): the ship's guns' expected damage at each enemy from ONE HEX, arcs left out
       (AIView::estimate with $needArc false) - per ship decision. What a gun does from a hex does not depend on which way the ship
       points there, only whether its arc holds the target (checked: 3,888 gun / target pairs, the shooter turned through all six
       facings, none differed), and the end states of a search share their hexes several facings apiece - so each hex is priced
       once and each facing only asks the arcs. Keyed by the board ('now' = the Fire phase, 'next' = a turn on), the hex and what
       else stands on the shooter's own rows (this turn's pivots and rolls; a turn on, its speed, heading and swapped sides). */
    private static $arcFree = array();

    /* The rows for one ship: the ShipMover plan whose end state scores best. $view is the bot's masked view of the Movement
       phase - its level's skills (stage T5f: AIView::skill) decide what the ship may try and how it weighs it; $hazards the
       board, built once for the decision. */
    public static function rows(AIView $view, ShipMover $mover, $ship, Hazards $hazards)
    {
        //Stage T5f-8: the ship's CPU time - the search stops at its MOVE_SEARCH_SHARE, this turn's terms at MOVE_JUDGE_SHARE.
        $started = AIConfig::cpuSeconds();
        $budget = self::budgetSeconds();
        $clock = ($budget > 0) ? array($started + $budget * AIConfig::MOVE_JUDGE_SHARE, $started + $budget) : null;
        self::$arcFree = array();
        $timing = array('plans' => 0, 'judgedNow' => 0, 'judgedNext' => 0, 'variants' => 0, 'cut' => false, 'choice' => null, 'seconds' => 0.0, 'budget' => $budget);

        $plans = self::candidates($mover, $view->skill('shortening'), (bool)$view->skill('extendedTurns'),
            ($budget > 0) ? $started + $budget * AIConfig::MOVE_SEARCH_SHARE : null, $timing['cut']);
        /* Stage T6c: only the plans a caller allows (JumpDecider - the end states where a jump vortex can be declared, then the
           one that ends in it through its mouth). None left: null, and the caller decides without the filter. */
        if (self::$planFilter !== null) {
            $plans = array_values(array_filter($plans, self::$planFilter));
            if (count($plans) == 0) {
                self::timed($view, $timing, $started);
                return null;
            }
        }
        $timing['plans'] = count($plans);
        if (count($plans) == 0) {
            self::timed($view, $timing, $started);
            return MoveDecider::choose($mover, $hazards->blocked, $hazards->bounds);
        }

        $context = self::context($view, $ship, $mover);
        $start = $ship->getHexPos();

        //The fire terms depend only on where the ship ends, which way it points, and whether it pivots or rolls on the way.
        $fire = array();
        $judged = self::judgeAll($view, $ship, $plans, $context, $fire, $hazards, $start, $clock, $timing);
        $timing['judgedNext'] = count($judged);

        //Stage T5e: a pivot or a roll on the end of the best of those plans, as the level allows - while there is time.
        $variants = ($clock === null || AIConfig::cpuSeconds() <= $clock[1]) ? self::variants($mover, $plans, $judged, $context) : array();
        if (count($variants) > 0) {
            $first = count($plans);
            $counts = array('judgedNow' => 0, 'cut' => false);
            foreach (self::judgeAll($view, $ship, $variants, $context, $fire, $hazards, $start, $clock, $counts) as $index => $j) {
                $judged[$first + $index] = $j;
                $timing['variants']++;
            }
            if ($counts['cut']) $timing['cut'] = true;
            foreach ($variants as $variant) $plans[] = $variant;
        }

        /* Easy's score noise (stage T5f, plan 5.8): each end state judged a little better or worse than it is - a draw fixed by
           the seat's seed, the ship and the end state, in hexes of the ship's own value. The board's hazards are thousands:
           noise never takes a ship off the map or into a rock. */
        $noise = (float)$view->skill('noise') * $context['hexValue'];
        /* Stage T6d: a LAST RESORT IS TAKEN when it can be (the user's ruling: a rammer rams "a target it can reach this turn") - when
           some plan ends on a ram worth making (Posture::ramValue > 0), only those plans are chosen from, by the usual score: this
           turn's danger, which a weaponless or dying ship takes wherever it goes, does not talk it out of the ram. */
        $ramming = false;
        foreach ($judged as $j) {
            if (!empty($j['ram']) && $j['ram'] > 0) $ramming = true;
        }
        $best = null;
        $bestScore = null;
        foreach ($judged as $index => $j) {
            if ($ramming && !(!empty($j['ram']) && $j['ram'] > 0)) continue;
            $score = $j['score'] + (($noise > 0) ? $noise * $view->noise('move|' . $ship->id . '|' . self::endKey($plans[$index]) . '|' . count($plans[$index]['actions'])) : 0);
            if ($bestScore === null || $score > $bestScore) {
                $bestScore = $score;
                $best = $index;
            }
        }

        if (is_array(self::$trace)) { //harness only: every plan judged, a variant with its seed (stage T5e)
            self::$traceJudged = array();
            foreach ($judged as $index => $j) {
                $m = self::manoeuvresOf($plans[$index], $context);
                self::$traceJudged[] = array('key' => self::endKey($plans[$index]), 'seed' => isset($plans[$index]['seed']) ? $plans[$index]['seed'] : null,
                    'pivots' => $m['pivotStarts'], 'rolls' => $m['rollStarts'], 'plan' => self::describe($plans[$index]['actions'], $plans[$index]['extras']),
                    'nextOffence' => $j['nextOffence'], 'nextDanger' => $j['nextDanger'], 'shaping' => $j['shaping'], 'score' => $j['score']);
            }
        }

        $plan = $plans[$best];
        $j = $judged[$best];
        $rows = $plan['rows'];
        $timing['choice'] = self::endKey($plan);

        //Thrust left over shortens the last turn's delay (turn shortening): next turn starts free to turn. Not on Easy.
        $shortened = (empty($plan['overthrust']) && $view->skill('shortening')) ? $mover->shortened($plan) : null;
        if ($shortened !== null) $rows = $shortened->rows();
        self::timed($view, $timing, $started);

        $hz = $j['board'];
        self::$last[] = sprintf('%s: %s; fire %.0f, launch %.0f, danger %.0f, next %.0f/%.0f, %d hexes off%s%s%s%s',
            $ship->name, count($plan['actions']) ? self::describe($plan['actions'], $plan['extras']) : 'straight on',
            $j['offence'], $j['launch'], $j['danger'], $j['nextOffence'], $j['nextDanger'], $j['nearest'],
            empty($plan['overthrust']) ? '' : '; OVERTHRUST ' . implode(', ', array_map(function ($id, $x) { return "#$id +$x"; }, array_keys($plan['overthrust']), $plan['overthrust'])),
            $shortened !== null ? '; last turn shortened' : '',
            ($hz['drift'] + $hz['meteors'] + $hz['mines'] > 0) ? sprintf('; hazards: drift %.0f, meteors %.0f, mines %.0f', $hz['drift'], $hz['meteors'], $hz['mines']) : '',
            $timing['cut'] ? sprintf('; OUT OF TIME (%.1f s): %d end states found, %d judged this turn, %d whole', $timing['seconds'], $timing['plans'], $timing['judgedNow'], $timing['judgedNext']) : '')
            . (!empty($j['ram']) && $j['ram'] > 0 ? sprintf('; RAMMING (%s): worth %.0f', ($context['ram'] !== null && $context['ram']->designedToRam) ? 'designed to ram' : 'last resort, Desperate rule', $j['ram']) : '');

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
       plans, terms and all - and fills $traceJudged with every plan it judged. Null (the default) in a game. */
    public static $trace = null;
    public static $traceJudged = array();

    /* One plan's worth. Fire both ways now and a turn on, next turn's launches; then the shaping
       terms, in hexes of the ship's own value: closing on the nearest enemy (always - enough to bring
       a ship in where the fire is slight, too little to buy a bad trade), formation, speed and
       manoeuvres; then the board. */
    private static function score(array $j, $hexValue, $dangerWeight = AIConfig::MOVE_DANGER_WEIGHT, $closingWeight = 1.0)
    {
        return $j['offence'] + AIConfig::MOVE_LAUNCH_WEIGHT * $j['launch'] - $dangerWeight * $j['danger']
            + AIConfig::MOVE_NEXT_TURN_WEIGHT * ($j['nextOffence'] - $dangerWeight * $j['nextDanger'])
            - $hexValue * (AIConfig::MOVE_CLOSING_PER_HEX * $closingWeight * $j['closing'] + $j['shaping'])
            - $j['hazard']
            + (isset($j['ram']) ? $j['ram'] : 0); //stage T6d: a last-resort ram (0 for every ship that does not ram)
    }

    /* Every end state the ship can reach this turn: first within its thrusters' ratings, then - with up
       to MOVE_OVERTHRUST points over a rating - the end states only an overthrust reaches (stage T5b; the
       user's ruling of 2026-11-13: within ratings first, over them only where that is worth the critical
       it invites, which judge() prices). Turns are tried shortened too (allowShortening) - at a level with the skill.
       Stage T5f-5 ($extended, Hard's skill 'extendedTurns'): plans that BEGIN an extended turn too - but never one leaving owed
       more than next turn's engine can pay (thrustNextTurn): the page would cancel it for want of thrust, the thrust paid lost, and
       the server's tripwire log it as a cancellation nothing forced (EXTENDED_TURNS_PLAN.md D12).
       Stage T5f-8: the search stops at $deadline (AIConfig::cpuSeconds) with the plainest plans found so far, and sets $cut. */
    private static function candidates(ShipMover $mover, $shorten = true, $extended = false, $deadline = null, &$cut = false)
    {
        /* Stage T5e: a roll or pivot carried over from last turn may be STOPPED anywhere in the move (a non-gravitic hull
           can neither turn nor slip while it rolls) - the search tries the stop. It starts none: variants() does that. */
        $now = $mover->state();
        $carried = function (ShipMover $m) use ($now, $extended) {
            return $m->allowRolls($now['rolling'])->allowPivots($now['pivoting'] !== 'no')->allowExtendedTurns($extended);
        };
        $payable = function (array $plan) use ($mover) {
            if ($plan['extending'] === 'no') return true;
            foreach ($plan['rows'] as $row) {
                if ($row['type'] !== ShipMover::EXTEND_LEFT && $row['type'] !== ShipMover::EXTEND_RIGHT) continue;
                $owed = json_decode((string)$row['value'], true);
                return is_array($owed) && (int)$owed['any'] + (int)$owed['rear'] + (int)$owed['side'] <= $mover->thrustNextTurn();
            }
            return false;
        };

        $plans = array();
        $seeds = array(); //stage T8: the orders the search within the ratings could not pay - where the overthrust search starts
        $search = $carried(clone $mover);
        foreach ($search->allowShortening($shorten)->enumerate(AIConfig::SHIP_PLANS, $deadline, $seeds) as $plan) {
            if ($payable($plan)) $plans[self::endKey($plan)] = $plan;
        }
        if ($search->searchCut) {
            $cut = true;
            return array_values($plans);
        }
        if (AIConfig::MOVE_OVERTHRUST > 0 && count($seeds) > 0) {
            foreach ($search->enumerateOverthrust($seeds, AIConfig::MOVE_OVERTHRUST, AIConfig::SHIP_PLANS, $deadline) as $plan) {
                $key = self::endKey($plan);
                if (!isset($plans[$key]) && $payable($plan)) $plans[$key] = $plan;
            }
            if ($search->searchCut) $cut = true;
        }
        return array_values($plans);
    }

    //Stage T5f-8: a ship's decision budget in CPU seconds (0 = none) and how many plans next turn's terms are asked of.
    private static function budgetSeconds()
    {
        $budget = self::budgetOn() ? ((self::$budget === null) ? AIConfig::MOVE_DECISION_SECONDS : (float)self::$budget) : 0;
        //stage T8: no more than the submission's share - and never 0, which would mean "none"
        if ($budget > 0 && self::$share !== null) $budget = min($budget, max(0.001, (float)self::$share));
        return $budget;
    }

    //Is there a decision budget at all (stage T8: MoveDecider shares a submission's out only then)? A harness's 0 = none.
    public static function budgetOn()
    {
        return self::$budget === null || (float)self::$budget > 0;
    }

    private static function nextTurnCap()
    {
        return (self::$nextTurnPlans === null) ? AIConfig::MOVE_NEXT_TURN_PLANS : max(1, (int)self::$nextTurnPlans);
    }

    //Close a decision's $timing: its CPU seconds, kept as $lastTiming and under the level in $timings.
    private static function timed(AIView $view, array &$timing, $started)
    {
        $timing['seconds'] = AIConfig::cpuSeconds() - $started;
        self::$lastTiming = $timing;
        self::$timings[$view->level][] = $timing;
    }

    private static function endKey(array $plan)
    {
        return $plan['position']->q . ',' . $plan['position']->r . ',' . $plan['heading'] . ',' . $plan['facing'] . ',' . $plan['speed']
            . ',' . $plan['pivoting'] . ',' . (int)$plan['rolling'] . ',' . (isset($plan['extending']) ? $plan['extending'] : 'no');
    }

    /* Judge the plans, with everybody else where the Fire phase should find them, and score them - in TWO PASSES since stage T5f-8
       (plan section 12: "the next-turn terms - a whole second board evaluation per end state - asked only of the end states whose
       this-turn score leaves them within reach of the best"): this turn's terms and the board for every plan, in the search's order
       (plainest first), then next turn's only for the MOVE_NEXT_TURN_PLANS best by this turn's score. A plan not judged whole is no
       candidate: what comes back is the plans judged whole, by index, in the search's order (so the plainest wins a tie).
       $clock: null, or array(this turn's deadline, next turn's) in AIConfig::cpuSeconds() - each pass judges at least one plan;
       $counts gets 'judgedNow' and 'cut'. */
    private static function judgeAll(AIView $view, $ship, array $plans, array $context, array &$fire, Hazards $hazards, OffsetCoordinate $start, $clock = null, array &$counts = array())
    {
        $cap = self::nextTurnCap();
        $cut = false;
        $judgedNow = 0;
        $judged = $view->atFireTime(function () use ($view, $ship, $plans, $context, &$fire, $hazards, $start, $clock, $cap, &$cut, &$judgedNow) {
            $now = array();
            foreach ($plans as $index => $plan) {
                if ($clock !== null && count($now) > 0 && AIConfig::cpuSeconds() > $clock[0]) {
                    $cut = true;
                    break;
                }
                $now[$index] = self::judgeNow($view, $ship, $plan, $context, $fire, $hazards, $start);
                $now[$index]['score'] = self::score($now[$index], $context['hexValue'], $context['dangerWeight'], $context['closingWeight']);
            }
            $judgedNow = count($now);

            uasort($now, function ($a, $b) { return $b['score'] <=> $a['score']; }); //stable: the plainest first among equals
            //stage T6d: a plan that rams (rows() takes one when there is one) is always judged whole - first, whatever its score
            $ramFirst = array();
            foreach ($now as $index => $j) if (!empty($j['ram']) && $j['ram'] > 0) $ramFirst[$index] = $j;
            if (count($ramFirst) > 0) $now = $ramFirst + $now;
            $out = array();
            foreach ($now as $index => $j) {
                if (count($out) >= $cap) break;
                if ($clock !== null && count($out) > 0 && AIConfig::cpuSeconds() > $clock[1]) {
                    $cut = true;
                    break;
                }
                $out[$index] = self::judgeNext($view, $ship, $plans[$index], $context, $j);
            }
            ksort($out);
            return $out;
        }, array((int)$ship->id));
        foreach ($judged as $index => $j) $judged[$index]['score'] = self::score($j, $context['hexValue'], $context['dangerWeight'], $context['closingWeight']);
        $counts['judgedNow'] = (isset($counts['judgedNow']) ? $counts['judgedNow'] : 0) + $judgedNow;
        if ($cut) $counts['cut'] = true;
        return $judged;
    }

    /* Stage T5e - the user's notes of 2026-11-19: pivots and rolls are rare, advanced moves. HARD tries, on the end of each of
       its best plans (MOVE_MANOEUVRE_SEEDS, one per end hex, leaving nothing running): a pivot started and stopped the same turn
       either way - the facing turns a step and the course holds (bringing heavy guns round, hiding a damaged side; a gravitic
       hull then turns freely while pivoted) - an agile hull's single and double pivots, and a roll (its sides swap at the turn
       change, it is stopped next turn). NORMAL tries only the roll, and keeps it only where it frees the turn it wants next turn
       (turnFreed - the thruster swap). EASY tries neither. judge() prices each: the engine's -3 on this turn's fire, the thrust,
       MOVE_COST_PIVOT / MOVE_COST_ROLL, ending out of alignment without a gravitic drive, and next turn with the sides swapped.
       Since stage T5f the level's skill 'manoeuvres' says which: 'all' (Hard), 'swap' (Normal), 'none' (Easy). */
    private static function variants(ShipMover $mover, array $plans, array $judged, array $context)
    {
        if ($context['manoeuvres'] === 'none' || (!empty($context['flight']))) return array();
        $hard = ($context['manoeuvres'] === 'all');

        uasort($judged, function ($a, $b) { return $b['score'] <=> $a['score']; });
        $seeds = array();
        $seedNext = array(); //each seed's next-turn offence (a roll leaves it as it is)
        foreach (array_keys($judged) as $index) {
            $plan = $plans[$index];
            if ($plan['pivoting'] !== 'no' || $plan['rolling']) continue;
            $hex = $plan['position']->q . ',' . $plan['position']->r;
            if (isset($seeds[$hex])) continue;
            $seeds[$hex] = $plan;
            $seedNext[$hex] = $judged[$index]['nextOffence'];
            if (count($seeds) >= AIConfig::MOVE_MANOEUVRE_SEEDS) break;
        }

        $tails = array();
        if ($hard) {
            $tails = $context['agile']
                ? array(array(ShipMover::PIVOT_LEFT), array(ShipMover::PIVOT_RIGHT), array(ShipMover::PIVOT_LEFT, ShipMover::PIVOT_LEFT), array(ShipMover::PIVOT_RIGHT, ShipMover::PIVOT_RIGHT))
                : array(array(ShipMover::PIVOT_LEFT, ShipMover::PIVOT_LEFT), array(ShipMover::PIVOT_RIGHT, ShipMover::PIVOT_RIGHT)); //start, then the same arrow stops it
        }
        $tails[] = array(ShipMover::ROLL);

        $out = array();
        foreach ($seeds as $hex => $seed) {
            $base = self::replay($mover, $seed, array());
            if ($base === null) continue;
            foreach ($tails as $tail) {
                $grown = self::replay($mover, $seed, $tail);
                if ($grown === null) continue;
                $plan = $grown->plan();
                if ($plan['pivoting'] !== 'no') continue; //never leave a pivot running (a non-agile start the same arrow could not stop)
                $plan['seed'] = self::endKey($seed);
                /* A pivot pays for itself THIS turn: next turn's terms - everybody flying straight on - over-credit a facing turned
                   while the course holds (an aligned ship would simply turn next turn instead). In self-play a fresh Tethys and an
                   agile Halik pivoted on turn 1 with nothing in reach for them (games 4788, 4795). So next turn is judged with the
                   seed's facing. A roll's next turn is its whole point (the sides swap then) and keeps its own. */
                if ($tail !== array(ShipMover::ROLL)) $plan['nextFacing'] = (int)$seed['facing'];
                /* A roll is judged on PROTECTION (its next-turn danger, the sides swapped) and the thruster swap - never on next
                   turn's fire from mirrored arcs, which the straight-on projection over-credits as it did a pivot's turned facing
                   (self-play: fresh Hyperions rolled on turns 1-2). Next turn's fire stays its seed's. */
                else $plan['nextOffence'] = $seedNext[$hex];
                $plan['freed'] = ($tail === array(ShipMover::ROLL)) ? self::turnFreed($base, $grown, $plan, $context) : 0;
                if (!$hard && $plan['freed'] <= 0) continue; //Normal rolls only to swap its side thrusters
                $out[] = $plan;
            }
        }
        return $out;
    }

    //$plan's orders again on a copy of $mover, then $tail: the copy, complete - or null when an order cannot be given.
    private static function replay(ShipMover $mover, array $plan, array $tail)
    {
        $copy = clone $mover;
        foreach ($plan['actions'] as $i => $action) {
            if (!$copy->apply($action, isset($plan['extras'][$i]) ? $plan['extras'][$i] : 0)) return null;
        }
        foreach ($tail as $action) {
            if (!$copy->apply($action)) return null;
        }
        return $copy->isComplete() ? $copy : null;
    }

    /* The thruster swap (stage T5e, the user's "most common use after hiding damage"): the side the ship wants to turn to next
       turn - its quarry's side of its heading at the end of the move, more than 30 degrees off - cannot be paid for by its side
       thrusters as they are, and can once the roll has swapped them (the roll completes at the turn change; it is stopped first
       thing). MOVE_VALUE_TURN_FREED, or 0. */
    private static function turnFreed(ShipMover $without, ShipMover $with, array $plan, array $context)
    {
        $right = self::wantedSide($plan, $context['quarry']);
        if ($right === null) return 0;
        if ($without->turnPayableNextTurn($right, (int)$plan['speed'])) return 0;
        return $with->turnPayableNextTurn($right, (int)$plan['speed']) ? AIConfig::MOVE_VALUE_TURN_FREED : 0;
    }

    //true = starboard, false = port, null = no turn wanted: the nearest quarry within 30 degrees of the heading, dead astern, or none.
    private static function wantedSide(array $plan, array $quarry)
    {
        $from = Mathlib::hexCoToPixel($plan['position']);
        $nearest = null;
        foreach ($quarry as $unit) {
            $d = $plan['position']->distanceTo($unit->getHexPos());
            if ($nearest === null || $d < $nearest[0]) $nearest = array($d, $unit);
        }
        if ($nearest === null || $nearest[0] == 0) return null;
        $bearing = Mathlib::addToDirection(Mathlib::getCompassHeadingOfPoint($from, $nearest[1]->getCoPos()), -60 * (int)$plan['heading']);
        if ($bearing <= 30 || $bearing >= 330 || abs($bearing - 180) < 1e-6) return null;
        return $bearing < 180;
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
            if (!isset($guns[$key])) $guns[$key] = array('weapon' => $weapon, 'count' => 0, 'now' => $now, 'next' => $next, 'slow' => $slow,
                'heavy' => FireDecider::isHeavy($weapon)); //stage T5f-3: Hard's fleet focus draws the heavy guns
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
            $last = $enemy->getLastMovement();
            $enemies[] = array('unit' => $enemy, 'moved' => $moved, 'arced' => array_values($arced), 'any' => array_values($any),
                'health' => $view->healthLeft($enemy), 'worth' => $view->value($enemy) / $view->healthLeft($enemy),
                //where it stands before it moves, and how far it could go towards the ship (Hard's one-ply reply, stage T5f-3)
                'start' => $enemy->getHexPos(), 'reach' => $last ? max(0, (int)$last->speed) + 1 : 1);
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

        /* Stage T6d (built at T8): a ship that rams as a last resort (Posture::rammer - the Desperate rule, no usable weapon or its
           primary at RAM_PRIMARY_SHARE) and the enemy SHIPS it could ram this turn - "only at a target it can reach this turn": those
           whose hex at the Fire phase is known now (they have moved, or never move), enemy index => that hex. Never a flight. */
        $ram = Posture::rammer($view, $ship);
        $ramTargets = array();
        if ($ram !== null) {
            foreach ($enemies as $e => $enemy) {
                $unit = $enemy['unit'];
                if ($unit instanceof FighterFlight || !empty($unit->mine) || $unit->isTerrain()) continue;
                if ($enemy['moved']) {
                    $ramTargets[$e] = $unit->getHexPos()->q . ',' . $unit->getHexPos()->r;
                    continue;
                }
                /* Stage T9: a unit DESIGNED to ram ("should always Ram") goes for an enemy yet to move as well - at the hex the level
                   forecasts it to end in (EnemyForecast - or flying straight on), where atFireTime() stands it for the judging. */
                if (!$ram->designedToRam) continue;
                $row = isset($view->forecast[(int)$unit->id]) ? $view->forecast[(int)$unit->id] : AIView::straightOn($unit, $turn);
                $at = ($row !== null) ? $row->position : $unit->getHexPos();
                $ramTargets[$e] = $at->q . ',' . $at->r;
            }
        }

        $now = $mover->state(); //before any order: a pivot or roll carried over from last turn (stage T5e)
        return array('guns' => array_values($guns), 'launchers' => array_values($launchers), 'enemies' => $enemies, 'friends' => $friends, 'quarry' => $quarry,
            'ram' => $ram, 'ramTargets' => $ramTargets,
            'health' => $view->healthLeft($ship), 'worth' => $view->value($ship) / $view->healthLeft($ship), 'comfort' => $comfort,
            //the level's skills (stage T5f, AIConfig::SKILLS) - and Hard's focus target for the fleet's heavy guns (MoveDecider)
            'manoeuvres' => $view->skill('manoeuvres'), 'unmovedDanger' => (float)$view->skill('unmovedDanger'),
            'focus' => $view->focus, 'focusBonus' => (float)$view->skill('fleetFocus'), 'bestReply' => (bool)$view->skill('bestReply'),
            //stage T9: a unit designed to ram PURSUES - its closing counts RAM_PURSUIT times, the enemy's fire at it RAM_DANGER_SHARE as much
            'dangerWeight' => (float)$view->skill('shipDanger') * (($ram !== null && $ram->designedToRam) ? AIConfig::RAM_DANGER_SHARE : 1.0),
            'closingWeight' => ($ram !== null && $ram->designedToRam) ? AIConfig::RAM_PURSUIT : 1.0,
            'overspeed' => AIConfig::MOVE_COST_OVERSPEED * (float)$view->skill('overspeed'),
            //judged without this turn's engine boost (Hard's power, stage T5f-4): next turn may not have it
            'turnable' => $mover->fastestTurnable(false), 'safe' => $mover->safeSpeed(AIConfig::MOVE_SAFE_TURN_SHARE, false),
            'hexValue' => max(1, $view->value($ship)) * AIConfig::MOVE_HEX_VALUE, 'value' => max(1, $view->value($ship)),
            'fullValue' => max(1, (int)$ship->pointCost + (int)$ship->pointCostEnh), //AIView::value with all its combat value left
            'agile' => !empty($ship->agile), 'gravitic' => !empty($ship->gravitic),
            'pivotingAtStart' => $now['pivoting'], 'rollingAtStart' => $now['rolling'],
            'alignedAtStart' => ($now['facing'] == $now['heading'] || $now['facing'] == Mathlib::addToHexFacing($now['heading'], 3)));
    }

    /* A plan's pivots and rolls this turn (stage T5e), read off its rows: how many pivot / roll rows (the page's forced pivot is
       none - it is not an order), and how many of them START one rather than stop one carried over - a non-agile hull starts
       at most one of each; an agile hull's every pivot and roll is a whole one. */
    private static function manoeuvresOf(array $plan, array $context)
    {
        $pivots = 0;
        $rolls = 0;
        foreach ($plan['rows'] as $row) {
            if (!empty($row['forced'])) continue;
            if ($row['type'] === 'pivotleft' || $row['type'] === 'pivotright') $pivots++;
            if ($row['type'] === 'roll') $rolls++;
        }
        $pivotStarts = $context['agile'] ? $pivots : (($context['pivotingAtStart'] === 'no') ? min(1, $pivots) : 0);
        $rollStarts = $context['agile'] ? $rolls : ($context['rollingAtStart'] ? 0 : min(1, $rolls));
        return array('pivots' => $pivots, 'rolls' => $rolls, 'pivotStarts' => $pivotStarts, 'rollStarts' => $rollStarts);
    }

    //A movement row at the plan's end, standing in for one of its manoeuvres while the engine is asked about its fire.
    private static function marker($type, $last, $turn, $preturn = false)
    {
        return new MovementOrder(-1, $type, new OffsetCoordinate($last->position), 0, 0, (int)$last->speed, (int)$last->heading, (int)$last->facing, $preturn, $turn, 0, 0);
    }

    /* For next turn's terms after a roll was started (stage T5e): the ship as it will be once the roll completed at the turn
       change - its sides swapped (Movement::isRolled: an isRolled row in its latest turn; a row of a later turn ends being
       rolled; an agile hull's every roll row swaps them). Stood at the end of whatever the ship's last row is. */
    private static function sidesSwapped($ship, $turn, $agile)
    {
        $last = $ship->getLastMovement();
        if ($agile) return self::marker('roll', $last, $turn + 1);
        if (Movement::isRolled($ship)) return self::marker('move', $last, $turn + 1);
        return self::marker('isRolled', $last, $turn, true);
    }

    /* Stage T5f-5: an extended turn begun this turn is completed first thing next turn, in the hex it ends in - next turn starts
       turned, heading and facing (and so does its straight continuation on the board). The step it turns by, or 0. */
    private static function extendedStep(array $plan)
    {
        $extending = isset($plan['extending']) ? $plan['extending'] : 'no';
        if ($extending === 'no') return 0;
        return ($extending === 'right') ? 1 : -1;
    }

    /* Everything one plan is scored by THIS turn (the first pass of judgeAll): its fire both ways, the board, and the shaping terms
       - next turn's terms 0 and the closing this turn's distance alone, until judgeNext() adds them. The ship is put at the plan's
       end for the asking, and taken back. $fire caches the terms that depend only on the end hex and facing. */
    private static function judgeNow(AIView $view, $ship, array $plan, array $context, array &$fire, Hazards $hazards, OffsetCoordinate $start)
    {
        $m = self::manoeuvresOf($plan, $context);
        $end = new MovementOrder(-1, 'move', $plan['position'], 0, 0, $plan['speed'], $plan['heading'], $plan['facing'], false, $view->turn, 0, 0);
        $ship->movement[] = $end;
        $markers = 0;
        try {
            /* This turn's fire with the plan's pivots and rolls on the shot (stage T5e): a pivot this turn, or a roll at fire
               time, takes 3 off every shot of the ship - the engine's own rule (Weapon::calculateHitBase), its rows stood in. */
            for ($i = 0; $i < $m['pivots']; $i++, $markers++) $ship->movement[] = self::marker('pivotright', $ship->getLastMovement(), $view->turn);
            for ($i = 0; $i < $m['rolls']; $i++, $markers++) $ship->movement[] = self::marker('roll', $ship->getLastMovement(), $view->turn);
            $key = $plan['position']->q . ',' . $plan['position']->r . ',' . $plan['facing'] . '|' . $m['pivots'] . '|' . $m['rolls'];
            if (!isset($fire[$key])) $fire[$key] = self::fireTerms($view, $ship, $context, 'now|' . $plan['position']->q . ',' . $plan['position']->r . '|' . $m['pivots'] . '|' . $m['rolls']);
            $terms = $fire[$key];
            for (; $markers > 0; $markers--) array_pop($ship->movement);

            //the board, next turn's straight continuation included - turned by an extended turn begun now (stage T5f-5)
            $nextPlan = $plan;
            $step = self::extendedStep($plan);
            if ($step != 0) $nextPlan['heading'] = Mathlib::addToHexFacing((int)$plan['heading'], $step);
            $board = $hazards->judge($ship, $start, $nextPlan, $context['worth']);

            //stage T6d: a last-resort ram from here - the best of the ram targets standing in this hex at the Fire phase (Posture::ramValue)
            $ram = 0.0;
            if ($context['ram'] !== null) {
                $hex = $plan['position']->q . ',' . $plan['position']->r;
                foreach ($context['ramTargets'] as $e => $at) {
                    if ($at !== $hex) continue;
                    $enemy = $context['enemies'][$e];
                    $ram = max($ram, Posture::ramValue($view, $ship, $context['ram'], $enemy['unit'],
                        array($enemy['health'], $enemy['worth'], $context['health'], $context['worth'], $context['dangerWeight'])));
                }
            }
        } finally {
            for (; $markers > 0; $markers--) array_pop($ship->movement);
            array_pop($ship->movement);
        }

        //shaping, in hexes: formation, speed, manoeuvres
        $shaping = AIConfig::MOVE_FORMATION_PER_HEX * max(0, $terms['friend'] - AIConfig::MOVE_FORMATION_RANGE);
        $speed = (int)$plan['speed'];
        if ($speed < AIConfig::MOVE_MIN_SPEED) $shaping += AIConfig::MOVE_COST_SLOW * (AIConfig::MOVE_MIN_SPEED - $speed);
        if ($speed > $context['comfort']) $shaping += AIConfig::MOVE_COST_FAST * ($speed - $context['comfort']);
        if ($speed > $context['safe']) $shaping += $context['overspeed'] * ($speed - $context['safe']);
        if ($speed > $context['turnable']) $shaping += AIConfig::MOVE_COST_UNTURNABLE * ($speed - $context['turnable']);
        foreach ($plan['actions'] as $action) {
            if ($action === ShipMover::ACCEL || $action === ShipMover::DECEL) $shaping += AIConfig::MOVE_COST_SPEED_CHANGE;
            else if ($action === ShipMover::SLIP_LEFT || $action === ShipMover::SLIP_RIGHT) $shaping += AIConfig::MOVE_COST_SLIP;
            else if ($action === ShipMover::EXTEND_LEFT || $action === ShipMover::EXTEND_RIGHT) $shaping += AIConfig::MOVE_COST_TURN + AIConfig::MOVE_COST_EXTENDED_TURN;
            else if ($action !== ShipMover::MOVE) $shaping += AIConfig::MOVE_COST_TURN;
        }
        if (!empty($plan['overthrust'])) $shaping += self::overthrustRisk($ship, $plan['overthrust']);

        /* Pivots and rolls (stage T5e): each started (a non-agile pivot's start and its stop), a pivot or roll carried over and
           left running, ending out of alignment without a gravitic drive - a ship expected to die this turn hardly minds (the
           "desperate" swing of the guns) - and the thruster swap a roll buys (variants()). */
        $shaping += AIConfig::MOVE_COST_PIVOT * ($context['agile'] ? $m['pivotStarts'] : 2 * $m['pivotStarts']);
        $shaping += AIConfig::MOVE_COST_ROLL * $m['rollStarts'];
        if (($context['pivotingAtStart'] !== 'no' && $plan['pivoting'] !== 'no') || ($context['rollingAtStart'] && $plan['rolling'])) $shaping += AIConfig::MOVE_COST_LEFT_RUNNING;
        if (!$context['gravitic'] && $plan['facing'] != $plan['heading'] && $plan['facing'] != Mathlib::addToHexFacing($plan['heading'], 3)) {
            /* A pivot started this turn (the ship began its move aligned): times its survival - the share of the ship expected
               to outlast this turn's fire, but never below the share of its combat value it still has (the danger term runs high,
               section 10.7; in self-play a fresh Tethys read "doomed" on turn 1 at 13 hexes: "desperate" needs real damage). A
               ship that began its move misaligned pays it whole: realigning comes first. */
            $survival = $context['alignedAtStart'] ? max(1 - min(1, $terms['danger'] / $context['value']), $context['value'] / $context['fullValue']) : 1;
            $shaping += AIConfig::MOVE_COST_MISALIGNED * $survival;
        }
        if (!empty($plan['freed'])) $shaping -= $plan['freed'];

        return array('offence' => $terms['offence'], 'launch' => $terms['launch'], 'danger' => $terms['danger'], 'ram' => $ram,
            'nextOffence' => 0, 'nextDanger' => 0, 'shaping' => $shaping,
            'nearest' => $terms['nearest'], 'closing' => $terms['nearest'],
            'hazard' => $board['total'], 'board' => $board,
            //for judgeNext(): the slow guns this turn's fire used, and whether a roll begun now swaps the sides by next turn
            'firedSlow' => $terms['firedSlow'], 'swap' => ($m['rollStarts'] % 2) == 1);
    }

    /* The second pass of judgeAll: next turn's terms for a plan judgeNow() judged - everybody a turn on, flying straight on
       (AIView::ahead): no pivot or roll on the shot (the bot stops a roll first thing), the sides as a roll begun now leaves them,
       a pivot variant's facing as its seed's (variants(): a pivot pays for itself this turn), an extended turn begun now completed
       (stage T5f-5). Returns $j whole. */
    private static function judgeNext(AIView $view, $ship, array $plan, array $context, array $j)
    {
        $end = new MovementOrder(-1, 'move', $plan['position'], 0, 0, $plan['speed'], $plan['heading'], $plan['facing'], false, $view->turn, 0, 0);
        if (isset($plan['nextFacing'])) $end->facing = (int)$plan['nextFacing'];
        $step = self::extendedStep($plan);
        if ($step != 0) {
            $end->heading = Mathlib::addToHexFacing((int)$plan['heading'], $step);
            $end->facing = Mathlib::addToHexFacing((int)$plan['facing'], $step);
        }
        $ship->movement[] = $end;
        try {
            $next = $view->ahead(function () use ($view, $ship, $context, $j) {
                if ($j['swap']) $ship->movement[] = self::sidesSwapped($ship, $view->turn, $context['agile']);
                try {
                    return self::nextTerms($view, $ship, $context, $j['firedSlow'], $j['swap']);
                } finally {
                    if ($j['swap']) array_pop($ship->movement);
                }
            });
        } finally {
            array_pop($ship->movement);
        }
        if (isset($plan['nextOffence'])) $next['offence'] = $plan['nextOffence']; //a roll: next turn's fire is its seed's (variants())

        $j['nextOffence'] = $next['offence'];
        $j['nextDanger'] = $next['danger'];
        $j['closing'] = $j['nearest'] + AIConfig::MOVE_CLOSING_NEXT_TURN * $next['nearest'];
        return $j;
    }

    /* This turn's fire both ways, next turn's launches, and the distances, with the ship at the end
       state judged and everybody else where the Fire phase should find them. */
    private static function fireTerms(AIView $view, $ship, array $context, $hexKey)
    {
        $clear = self::lines($view, $ship, $context['enemies']);
        $damage = array();
        $firedSlow = array();
        //each gun at each enemy in its range that its arc holds from this facing - priced once a hex (gunFire), whatever the facing
        $distance = self::distances($ship, $context['enemies']);
        foreach ($context['guns'] as $index => $gun) {
            if (!$gun['now']) continue;
            foreach ($context['enemies'] as $e => $enemy) {
                if (!$gun['weapon']->ignoresLoS && !$clear[$e]) continue;
                if ($gun['weapon']->range != 0 && $distance[$e] > $gun['weapon']->range) continue; //(estimate()'s own range test, asked first: it is cheaper than the arc)
                if (!$view->onArc($ship, $gun['weapon'], $enemy['unit'])) continue;
                $d = self::gunFire($view, $ship, $gun['weapon'], $enemy['unit'], $hexKey, $index, $e) * $gun['count']
                    * ($enemy['moved'] ? 1 : AIConfig::MOVE_UNMOVED_OFFENCE) * self::focusFactor($gun, $enemy, $context);
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

        //each target's health from where the ship stands: the section its hits go through (stage T5f - Normal and Hard)
        $targets = self::targetsFrom($view, $ship, $context['enemies']);

        //The enemy's fire: from its arcs once it has moved; every gun, and towards the ship's worst side, while it has yet to.
        $inward = self::lines($view, $ship, $context['enemies'], true);
        $incoming = array();
        foreach ($context['enemies'] as $e => $enemy) {
            $d = self::incoming($view, $ship, $enemy, $inward[$e]);
            /* The one-ply reply, BOTH SIDES (plan 5.8, Hard: "assume each enemy's best reply"; the skill 'bestReply'): an enemy yet
               to move sees where the ship ended and either flies on or closes as near as its speed and a point allow (replyHex).
               It takes the one worse for the ship - the ship's fire at it against its fire at the ship, both priced where it would
               end (worseReply) - and the ship's offence at it and danger from it are then that one's. Stage T5f-3 priced only the
               enemy's fire from the closer hex, so Hard stood off and lost; this version (T5f-6) prices what closing costs it too. */
            if (!$enemy['moved'] && $context['bestReply'] && isset($enemy['start'])) {
                $closer = self::replyHex($enemy, $ship->getHexPos());
                if ($closer !== null) {
                    $last = $enemy['unit']->getLastMovement();
                    $enemy['unit']->movement[] = new MovementOrder(-1, 'move', $closer, 0, 0, (int)$last->speed, (int)$last->heading, (int)$last->facing, false, $view->turn, 0, 0);
                    $atReply = array();
                    try {
                        $dReply = self::incoming($view, $ship, $enemy, $view->clearLine($enemy['unit'], $ship));
                        $clear = $view->clearLine($ship, $enemy['unit']);
                        foreach ($context['guns'] as $index => $gun) {
                            if (!$gun['now'] || (!$gun['weapon']->ignoresLoS && !$clear)) continue;
                            $x = $view->estimate($ship, $gun['weapon'], $enemy['unit'], true) * $gun['count'] * AIConfig::MOVE_UNMOVED_OFFENCE * self::focusFactor($gun, $enemy, $context);
                            if ($x > 0) $atReply[$index] = $x;
                        }
                    } finally {
                        array_pop($enemy['unit']->movement);
                    }
                    $offStraight = 0;
                    foreach ($damage as $row) $offStraight += isset($row[$e]) ? $row[$e] : 0;
                    $exposure = $view->exposureUnmoved($ship, $enemy['unit']) * $context['unmovedDanger'];
                    if (self::worseReply(array('offence' => $offStraight, 'danger' => $d * $exposure), array('offence' => array_sum($atReply), 'danger' => $dReply * $exposure),
                        $targets[$e]['worth'], $context['worth'], $context['dangerWeight'])) {
                        foreach (array_keys($damage) as $index) unset($damage[$index][$e]);
                        foreach ($atReply as $index => $x) $damage[$index][$e] = $x;
                        $d = $dReply;
                    }
                }
            }
            if ($d <= 0) continue;
            if ($enemy['moved']) $incoming[] = array($d, $view->exposure($ship, $enemy['unit']));
            else $incoming[] = array($d * $context['unmovedDanger'], $view->exposureUnmoved($ship, $enemy['unit']));
        }

        return array('offence' => self::removed($damage, $targets), 'launch' => self::removed($launches, $targets),
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

    //Enemy index => hexes from the ship to it, where both stand now.
    private static function distances($ship, array $enemies)
    {
        $from = $ship->getHexPos();
        $out = array();
        foreach ($enemies as $e => $enemy) $out[$e] = $from->distanceTo($enemy['unit']->getHexPos());
        return $out;
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
    private static function nextTerms(AIView $view, $ship, array $context, array $firedSlow, $swap)
    {
        $clear = self::lines($view, $ship, $context['enemies']);
        $damage = array();
        //each gun at each enemy its arc holds a turn on - priced once a hex (gunFire): the speed, heading and swapped sides in the key
        $at = $ship->getLastMovement();
        $hexKey = 'next|' . $at->position->q . ',' . $at->position->r . '|' . (int)$at->speed . '|' . (int)$at->heading . '|' . (int)$swap;
        $distance = self::distances($ship, $context['enemies']);
        foreach ($context['guns'] as $index => $gun) {
            if (!$gun['next'] || isset($firedSlow[$index])) continue;
            foreach ($context['enemies'] as $e => $enemy) {
                if (!$gun['weapon']->ignoresLoS && !$clear[$e]) continue;
                if ($gun['weapon']->range != 0 && $distance[$e] > $gun['weapon']->range) continue;
                if (!$view->onArc($ship, $gun['weapon'], $enemy['unit'])) continue;
                $d = self::gunFire($view, $ship, $gun['weapon'], $enemy['unit'], $hexKey, $index, $e) * $gun['count'] * AIConfig::MOVE_UNMOVED_OFFENCE
                    * self::focusFactor($gun, $enemy, $context);
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

        return array('offence' => self::removed($damage, self::targetsFrom($view, $ship, $context['enemies'])), 'danger' => self::lost($incoming, $context['health'], $context['worth']),
            'nearest' => self::nearest($ship, $context['quarry']));
    }

    /* What gun $index ($weapon) should do to enemy $e ($target) from the hex the ship stands in, ARC LEFT OUT (AIView::estimate,
       $needArc false) - asked by a caller that has found the arc holds the target for the facing it judges (estimate() with the
       arc is exactly onArc ? this : 0), worked out once per $hexKey (see $arcFree) and pair, only for pairs some facing needs. */
    private static function gunFire(AIView $view, $ship, Weapon $weapon, $target, $hexKey, $index, $e)
    {
        if (!isset(self::$arcFree[$hexKey][$index][$e])) self::$arcFree[$hexKey][$index][$e] = (float)$view->estimate($ship, $weapon, $target, false);
        return self::$arcFree[$hexKey][$index][$e];
    }

    //What one enemy's guns loaded now should do to the ship this turn: from their arcs once it has moved, every gun while it has yet to.
    private static function incoming(AIView $view, $ship, array $enemy, $clear)
    {
        $d = 0;
        foreach ($enemy['moved'] ? $enemy['arced'] : $enemy['any'] as $kind) {
            if (!$kind['now'] || (!$clear && !$kind['weapon']->ignoresLoS)) continue;
            $d += $view->estimate($enemy['unit'], $kind['weapon'], $ship, $enemy['moved']) * $kind['count'];
        }
        return $d;
    }

    /* The one-ply reply's choice (stage T5f-6): is $reply worse for the ship than $straight? Each array('offence' => the ship's
       expected damage at the enemy, 'danger' => the enemy's at the ship, its exposure and weight in) - their net for the ship in
       value: the ship's fire times what a point of the enemy is worth, less its losses times its own worth and the danger weight.
       The enemy flies on unless closing is strictly worse for the ship. */
    private static function worseReply(array $straight, array $reply, $worthEnemy, $worthShip, $dangerWeight)
    {
        $net = function (array $option) use ($worthEnemy, $worthShip, $dangerWeight) {
            return $option['offence'] * $worthEnemy - $dangerWeight * $option['danger'] * $worthShip;
        };
        return $net($reply) < $net($straight);
    }

    /* Where an enemy yet to move would end to shoot at $end best, for the one-ply reply: from where it starts, $enemy['reach']
       hexes towards $end - its speed and a point it could add - stopping a hex short. Null when that is no nearer than flying
       straight on takes it. */
    private static function replyHex(array $enemy, OffsetCoordinate $end)
    {
        $at = $enemy['start'];
        for ($step = 0; $step < $enemy['reach'] && $at->distanceTo($end) > 1; $step++) {
            $best = null;
            for ($direction = 0; $direction < 6; $direction++) {
                $n = $at->moveToDirection($direction);
                if ($best === null || $n->distanceTo($end) < $best->distanceTo($end)) $best = $n;
            }
            $at = $best;
        }
        return ($at->distanceTo($end) < $enemy['unit']->getHexPos()->distanceTo($end)) ? $at : null;
    }

    /* HARD's fleet focus (stage T5f-3, the skill 'fleetFocus'): a heavy gun's fire at the target MoveDecider chose for the
       fleet counts 1 + the skill - so the fleet's ships each move to bring their heavy guns on the same ship. 1 otherwise. */
    private static function focusFactor(array $gun, array $enemy, array $context)
    {
        if ($context['focusBonus'] <= 0 || $context['focus'] === null || empty($gun['heavy'])) return 1.0;
        return ((int)$enemy['unit']->id === (int)$context['focus']) ? 1 + $context['focusBonus'] : 1.0;
    }

    /* The enemies as targets of fire from where $shooter stands now (stage T5f, the user's ruling on game 4774): each one's
       health and worth from that side - AIView::healthLeft with the shooter, the section its hits go through - at a level
       with the skill 'sectionHealth'. Any weight already in an enemy's worth (FlightDecider's interception weight) is kept.
       Without the skill, as they are. FlightDecider asks it too. */
    public static function targetsFrom(AIView $view, $shooter, array $enemies)
    {
        if (!$view->skill('sectionHealth')) return $enemies;
        foreach ($enemies as $e => $enemy) {
            $health = $view->healthLeft($enemy['unit'], $shooter);
            $enemies[$e]['worth'] = $enemy['worth'] * $enemy['health'] / $health;
            $enemies[$e]['health'] = $health;
        }
        return $enemies;
    }
}
