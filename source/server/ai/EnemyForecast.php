<?php
/**
 * CPU opponent - where an enemy SHIP yet to move this turn will END (TACTICAL_AI_PLAN.md section 10.7, stage T5f-7).
 *
 * The user, 2026-11-20: "I would actually love for both Hard and Normal to have some level of movement predictions (to a simpler
 * extent e.g. Normal might only consider that a ship might turn once) to anticipate Enemy movement in the way players would to an
 * extent. Also ofc, we only have to anticipate enemy ships that come after the ship in Initiative Order (or in same bracket if Sim.
 * Move rule is in place) since we already know where ships that moved before us will be."
 *
 * Until this stage an enemy yet to move was taken to fly straight on (AIView::atFireTime). Now, by the level's skill 'forecast':
 *   'turn' (Normal) - straight on, or ONE turn either way before any hex of its move or after the last, and nothing else: whichever
 *                     brings most of its loaded guns' fire on the bot's units - a player's "it will come about onto us" (it may
 *                     forecast an enemy into the bot's own hex for point-blank fire);
 *   'full' (Hard)   - wider: Normal's plans and the first FULL_PLANS end states the enemy's own ShipMover finds, plainest first
 *                     (speed changes, slips, two manoeuvres) - by the same measure, its fire on the bot's units. A first build
 *                     weighed the enemy's TRADE here (its fire less MOVE_DANGER_WEIGHT of the bot's at it): it forecast stand-offs,
 *                     and an audit of its predictions against the enemy's real decisions (TACTICAL_AI_PLAN.md 10.7, T5f-9) found it
 *                     the worst forecast of all - on EA worse than straight on - while the enemy's fire over the same wider set did
 *                     as well as Normal's or better (mean error 2.7-2.8 hexes against 2.8-3.0 on Brakiri, even on EA);
 *   'none' (Easy)   - nothing: straight on, as before.
 * Never into a blocked hex or off the map while a plan avoids it (MoveDecider::hazard, the T1b board); on a tie the plainest plan,
 * straight on first. Fire both ways is the real formula (AIView::estimate, arcs asked; line of sight is not), the bot's units where
 * the Fire phase should find them; each end hex and facing is priced once.
 *
 * Only enemy SHIPS that have yet to move - one before the bot's ships in initiative has moved and stands where it ended; under
 * simultaneous movement one in the same bracket has not - and that ShipMover plots (a base, an OSAT, a Mindrider drive... stays
 * where it is). Flights keep straight on. P2: nothing read is hidden from a player - positions, the enemy's systems and their damage,
 * its weapons' loading.
 *
 * Built once per Movement decision (MoveDecider::orders) for every unit the bot moves in it: AIView::atFireTime stands each forecast
 * ship at its end instead of straight on, which every Movement term then sees - this turn's fire both ways, and next turn's
 * straight continuation (AIView::ahead goes on from the forecast end). An enemy yet to move still fires with EVERY gun at the bot's
 * ship (the user's ruling on game 4750, note 9) - the forecast says from WHERE.
 */
class EnemyForecast
{
    const FULL_PLANS = 80;  //Hard: the enemy's end states weighed, plainest first

    /* What the last forView() found: unit id => array('actions' => the plan's orders, 'candidates' => plans weighed, 'value' => its
       score, 'mode' => the forecast it got - Hard's 'full', or 'turn' once Hard's time ran out). */
    public static $last = array();
    /* Stage T5f-8: a harness override of AIConfig::MOVE_FORECAST_SECONDS - null = AIConfig's, 0 = none (the levels harness). And
       every forView() of the process: level => list of array('seconds' (CPU), 'enemies' forecast) - the self-play harness reads it. */
    public static $budget = null;
    public static $timings = array();

    /* unit id => MovementOrder (the predicted end, a row of this turn) for every enemy ship of the bot's MASKED view that has yet to
       move this turn and that ShipMover plots. Empty at a level without the skill. Nearest to the bot's units first: once Hard has
       spent MOVE_FORECAST_SECONDS the rest get Normal's forecast (the first always gets the level's own). */
    public static function forView(AIView $view, Hazards $hazards)
    {
        self::$last = array();
        $mode = $view->skill('forecast');
        if ($mode !== 'turn' && $mode !== 'full') return array();
        $turn = $view->turn;
        $started = AIConfig::cpuSeconds();
        $budget = self::budgetSeconds();

        $pending = array();
        $enemyIds = array();
        foreach ($view->enemies() as $enemy) {
            $enemyIds[] = (int)$enemy->id;
            if ($enemy instanceof FighterFlight || !empty($enemy->mine)) continue;
            if ($enemy->getLastTurnMoved() >= $turn) continue; //it has moved: it stands where it ended
            if (ShipMover::unsupportedReason($enemy, $turn) !== null) continue;
            $pending[] = array($enemy, self::rangeTo($enemy, $view->myUnits()));
        }
        if (count($pending) == 0) return array();
        usort($pending, function ($a, $b) { return $a[1] <=> $b[1]; }); //stable: the view's order among equals

        //the bot's units where the Fire phase should find them; every enemy where it stands, but the one being forecast
        $out = $view->atFireTime(function () use ($view, $pending, $mode, $hazards, $started, $budget) {
            $out = array();
            foreach ($pending as $index => $entry) {
                $rushed = ($mode === 'full' && $budget > 0 && $index > 0 && AIConfig::cpuSeconds() - $started > $budget);
                $given = $rushed ? 'turn' : $mode;
                /* An enemy is any hull a player brings, of any faction, and ShipMover was proven on the bot's own: one it throws on
                   keeps straight on - the forecast is advice, never a reason to lose the whole Movement decision to the fallback.
                   The failure is kept for the decision line (MoveDecider). */
                try {
                    $row = self::predict($view, $entry[0], $given, $hazards);
                } catch (Throwable $e) {
                    $row = null;
                    self::$last[(int)$entry[0]->id] = array('actions' => array(), 'candidates' => 0, 'value' => null, 'mode' => $given,
                        'error' => get_class($entry[0]) . ': ' . get_class($e) . ': ' . $e->getMessage() . ' (' . basename($e->getFile()) . ':' . $e->getLine() . ')');
                }
                if ($row !== null) $out[(int)$entry[0]->id] = $row;
            }
            return $out;
        }, $enemyIds);
        self::$timings[$view->level][] = array('seconds' => AIConfig::cpuSeconds() - $started, 'enemies' => count($pending));
        return $out;
    }

    private static function budgetSeconds()
    {
        return (self::$budget === null) ? AIConfig::MOVE_FORECAST_SECONDS : max(0, (float)self::$budget);
    }

    //Hexes from $unit to the nearest of $units (PHP_INT_MAX when there are none).
    private static function rangeTo($unit, array $units)
    {
        $nearest = PHP_INT_MAX;
        foreach ($units as $other) $nearest = min($nearest, $unit->getHexPos()->distanceTo($other->getHexPos()));
        return $nearest;
    }

    //One enemy ship's forecast end, or null when it has no plan.
    private static function predict(AIView $view, $enemy, $mode, Hazards $hazards)
    {
        $mover = ShipMover::forShip($enemy, $view->gamedata);
        if ($mover === null) return null;
        /* Straight on and a turn either way after each hex, then straight on - built directly: the plainest-first search does not
           list them all before plans of two manoeuvres. Hard adds its plainest-first FULL_PLANS (one end state each). */
        $plans = self::oneTurnPlans($mover);
        if ($mode === 'full') {
            $ends = array();
            foreach ($plans as $plan) $ends[self::endKey($plan)] = true;
            foreach ($mover->enumerate(self::FULL_PLANS) as $plan) {
                if (!isset($ends[self::endKey($plan)])) $plans[] = $plan;
            }
        }
        if (count($plans) == 0) return null;

        $targets = $view->myUnits();
        $theirGuns = self::guns($view, $enemy);

        $priced = array();
        $best = null;
        $bestValue = null;
        $weighed = 0;
        foreach ($plans as $plan) {
            $weighed++;
            $key = $plan['position']->q . ',' . $plan['position']->r . ',' . $plan['facing'];
            if (!isset($priced[$key])) {
                $enemy->movement[] = new MovementOrder(-1, 'move', $plan['position'], 0, 0, (int)$plan['speed'], (int)$plan['heading'], (int)$plan['facing'], false, $view->turn, 0, 0);
                try {
                    $their = 0;
                    foreach ($theirGuns as $gun) {
                        $most = 0;
                        foreach ($targets as $unit) $most = max($most, $view->estimate($enemy, $gun['weapon'], $unit, true));
                        $their += $most * $gun['count'];
                    }
                } finally {
                    array_pop($enemy->movement);
                }
                $priced[$key] = $their;
            }
            //the enemy's fire on the bot's units, at both levels (not its trade - see the class comment)
            $value = $priced[$key] - MoveDecider::hazard($plan['path'], $plan['position'], (int)$plan['heading'], (int)$plan['speed'], $hazards->blocked, $hazards->bounds);
            if ($bestValue === null || $value > $bestValue + 1e-9) {
                $best = $plan;
                $bestValue = $value;
            }
        }
        if ($best === null) return null;

        self::$last[(int)$enemy->id] = array('actions' => $best['actions'], 'candidates' => $weighed, 'value' => $bestValue, 'mode' => $mode);
        return new MovementOrder(-1, 'move', $best['position'], 0, 0, (int)$best['speed'], (int)$best['heading'], (int)$best['facing'], false, $view->turn, 0, 0);
    }

    //A unit's direct-fire guns loaded this turn, by kind (class, mode, arc): array('weapon', 'count').
    private static function guns(AIView $view, $unit)
    {
        $kinds = array();
        foreach ($view->mounts($unit) as $mount) {
            $weapon = $mount['weapon'];
            if (!$view->plainGun($weapon, $mount['fighter']) || !$view->loaded($weapon) || $weapon->isOfflineOnTurn($view->turn)) continue;
            $key = get_class($weapon) . '|' . $weapon->firingMode . '|' . AIView::arcKey($weapon);
            if (!isset($kinds[$key])) $kinds[$key] = array('weapon' => $weapon, 'count' => 0);
            $kinds[$key]['count']++;
        }
        return array_values($kinds);
    }

    /* Straight on, and one turn to either side after each hex of the move (none, one, ... all of them) and straight on after it -
       every plan the page allows of them; an extended turn the enemy owes from last turn is made first in each (it can do nothing
       before it). */
    private static function oneTurnPlans(ShipMover $mover)
    {
        $start = clone $mover;
        if (in_array(ShipMover::COMPLETE_EXTENDED, $start->options(), true)) $start->apply(ShipMover::COMPLETE_EXTENDED);
        $plans = array();
        $straight = clone $start;
        if ($straight->finish()) $plans[] = $straight->plan();
        $walker = clone $start;
        for ($guard = 0; $guard < 60; $guard++) {
            foreach (array(ShipMover::TURN_LEFT, ShipMover::TURN_RIGHT) as $turn) {
                $turned = clone $walker;
                if ($turned->apply($turn) && $turned->finish()) $plans[] = $turned->plan();
            }
            if ($walker->isComplete() || !$walker->apply(ShipMover::MOVE)) break;
        }
        return $plans;
    }

    private static function endKey(array $plan)
    {
        return $plan['position']->q . ',' . $plan['position']->r . ',' . $plan['heading'] . ',' . $plan['facing'] . ',' . $plan['speed'];
    }
}
