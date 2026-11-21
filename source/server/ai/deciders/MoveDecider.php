<?php
/**
 * CPU opponent - Movement (TACTICAL_AI_PLAN.md sections 3.3 and 5.4).
 *
 * SHIPS (stage T5) go where ShipDecider sends them: every end state ShipMover finds is scored by
 * the fire it brings to bear this turn and next, the fire it takes, the launchers it brings to bear
 * for next turn, and the board's hazards.
 *
 * FIGHTER FLIGHTS (stage T4) go where FlightDecider sends them - strike, intercept or escort -
 * among the plans ShipMover finds for them.
 *
 * Units ShipMover does not plot for (Hunter-Killers, bases, Mindrider drives, a ship carrying
 * another...) keep the fallback's rows: straight on. Gravitic hulls are plotted since stage T5d.
 *
 * choose() below is the T1b rule - hold the course unless that flies into terrain or off the map -
 * and is what a decider falls back on when it has no plan to choose from.
 *
 * The decision is made on the bot's own MASKED view of the game (principle P2).
 */
class MoveDecider
{
    //One line about the last orders(), for tac_ai_log.
    public static $lastSummary = null;

    /* Bots (player ids) whose SHIPS keep the stage T1b rule - hold course, steer clear of terrain and
       the map edge (choose()) - instead of ShipDecider. For the self-play harness only (a ladder
       against "the bot before stage T5"): nothing in the game sets it. */
    public static $plainFor = array();

    //The bot's Movement entries, one per unit it owns. $seat is its DBManager::getCpuSeats row.
    public static function orders(TacGamedata $gamedata, $botId, array $seat)
    {
        $gamedata->prepareForPlayer(); //the mask a human in this seat plays behind
        self::$lastSummary = null;
        FlightDecider::reset();
        ShipDecider::reset();
        JumpDecider::reset();
        //The seat's level and seed (stage T5f): the view answers what the bot is able to do (AIView::skill) for every decider.
        $view = new AIView($gamedata, $botId, AIConfig::seatLevel($seat), AIConfig::seatSeed($seat));

        $active = array();
        foreach ($gamedata->getMyActiveShips() as $ship) $active[$ship->id] = true;

        //The board (stage T5c): terrain, drifting fields, meteor swarms, the mines this bot can see.
        $hazards = new Hazards($view);
        //Stage T5f-7: where each enemy ship yet to move will end (the level's skill 'forecast') - every unit decided below sees it there
        $view->forecast = EnemyForecast::forView($view, $hazards);
        //Hard (stage T5f-3): the one enemy ship the fleet's heavy guns go for together.
        if ((float)$view->skill('fleetFocus') > 0) $view->focus = self::focusTarget($view);

        $entries = array();
        $flights = 0;
        foreach (AIFallback::ownUnits($gamedata, $botId) as $ship) {
            $entry = AIOrderBuilder::entry($ship);

            if (isset($active[$ship->id])) {
                $mover = ShipMover::forShip($ship, $gamedata);
                if ($mover !== null && $mover->isFlight()) {
                    $entry['movement'] = FlightDecider::rows($view, $mover, $ship, $hazards);
                    $flights++;
                } else if ($mover !== null && in_array((int)$botId, self::$plainFor, true)) {
                    $entry['movement'] = self::choose($mover, $hazards->blocked, $hazards->bounds);
                } else if ($mover !== null) {
                    /* Stage T6c - a ship leaving the battle through its own jump point: on the turn it forms, into its hex through
                       its mouth; the turn after, standing in it, out (JumpDecider). Anything else, or a jump that no longer works: as usual. */
                    $rows = null;
                    $jump = JumpDecider::pending($view, $ship);
                    if ($jump !== null && $jump['open']) $rows = JumpDecider::jumpOutRows($mover, $ship, $jump);
                    else if ($jump !== null) $rows = JumpDecider::approachRows($view, $mover, $ship, $hazards, $jump);
                    else $rows = JumpDecider::brakeRows($view, $mover, $ship, $hazards); //too fast to reach a jump point this turn
                    $entry['movement'] = ($rows !== null) ? $rows : ShipDecider::rows($view, $mover, $ship, $hazards);
                } else {
                    $entry['movement'] = AIFallback::straightRows($ship, $gamedata);
                }
                /* Units of the bot that move in the same activation (simultaneous movement) are
                   decided one after another: each later one sees this one where it is going. */
                self::standAtEnd($ship, $entry['movement'], $gamedata->turn);
            }

            $entries[] = $entry;
        }

        $parts = array_merge(ShipDecider::$last, JumpDecider::$last);
        if ($flights > 0) {
            $f = FlightDecider::$last;
            $parts[] = $flights . ' flights: ' . $f['strike'] . ' striking, ' . $f['intercept'] . ' intercepting, ' . $f['escort'] . ' escorting; ' . $f['jinking'] . ' jinking'
                . ($f['searched'] > 0 ? '; ' . $f['found'] . ' of ' . $f['searched'] . ' less covered places reached' : '')
                . ($f['forced'] > 0 ? '; ' . $f['forced'] . ' MUST FIRE this turn (no shot for ' . (AIConfig::FLIGHT_MAX_IDLE_TURNS - 1) . ' turns, the side down to its fighters)' : '');
        }
        if (count($parts) > 0 && $hazards->describe() !== '') $parts[] = 'board: ' . $hazards->describe();
        if (count($parts) > 0 && count(EnemyForecast::$last) > 0) {
            $turning = 0;
            $failed = array();
            foreach (EnemyForecast::$last as $f) {
                if (isset($f['error'])) $failed[] = $f['error'];
                else if (count(array_diff($f['actions'], array(ShipMover::MOVE))) > 0) $turning++;
            }
            $parts[] = 'forecast: ' . count(EnemyForecast::$last) . ' enemy ships yet to move, ' . $turning . ' of them manoeuvring'
                . (count($failed) ? '; NOT FORECAST (straight on): ' . implode('; ', $failed) : '');
        }
        if (count($parts) > 0) self::$lastSummary = implode(' | ', $parts);

        return $entries;
    }

    /* HARD's fleet focus (stage T5f-3; the user's note 5 on game 4750: "it is more effective to concentrate a fleet's heavy
       firepower to try and take down an enemy ship rather than spread damage"). The enemy SHIP the fleet's heavy guns could most
       nearly finish where the units stand - every heavy gun's expected damage at it, arcs not asked (facings change in
       Movement), over what finishing it takes, no more than all of it, times what it is worth. As damage goes in, the same
       target scores higher, so the choice holds from turn to turn. Null when no heavy gun reaches an enemy ship. */
    private static function focusTarget(AIView $view)
    {
        $heavy = array();
        foreach ($view->myUnits() as $unit) {
            if ($unit instanceof FighterFlight) continue;
            foreach ($view->mounts($unit) as $mount) {
                if ($view->plainGun($mount['weapon']) && FireDecider::isHeavy($mount['weapon'])) $heavy[] = array($unit, $mount['weapon']);
            }
        }
        $best = null;
        $bestScore = 0;
        foreach ($view->enemies() as $enemy) {
            if ($enemy instanceof FighterFlight || !empty($enemy->mine)) continue;
            $damage = 0;
            foreach ($heavy as $gun) $damage += $view->estimate($gun[0], $gun[1], $enemy, false);
            if ($damage <= 0) continue;
            $score = min(1, $damage / $view->healthLeft($enemy)) * $view->value($enemy);
            if ($score > $bestScore) {
                $bestScore = $score;
                $best = (int)$enemy->id;
            }
        }
        return $best;
    }

    //Put a unit at the end of the rows just decided for it, for the decisions that follow (the load is thrown away after).
    private static function standAtEnd($ship, array $rows, $turn)
    {
        if (count($rows) == 0) return;
        $last = $rows[count($rows) - 1];
        $position = new OffsetCoordinate((int)$last['position']['q'], (int)$last['position']['r']);
        $ship->movement[] = new MovementOrder(-1, 'move', $position, 0, 0, (int)$last['speed'], (int)$last['heading'], (int)$last['facing'], false, (int)$turn, 0, 0);
    }

    /* The part of score() that is about the board, not the plan's manoeuvres: a blocked hex entered,
       ending off the map, and the same on next turn's straight continuation. 0 = nothing wrong. The T1b
       rule's own (choose()); the T5 deciders ask Hazards, which reproduces this and prices what else is
       on the board. */
    public static function hazard(array $path, OffsetCoordinate $end, $heading, $speed, array $blocked, $bounds)
    {
        $score = 0;
        foreach ($path as $hex) {
            if (isset($blocked[$hex->q . ',' . $hex->r])) $score += AIConfig::MOVE_COST_BLOCKED;
        }
        $over = Hazards::hexesOffMap($end, $bounds);
        if ($over > 0) $score += AIConfig::MOVE_COST_OFF_MAP + $over * AIConfig::MOVE_COST_PER_HEX_OVER;

        $ahead = $end;
        $aheadBlocked = false;
        for ($step = 0; $step < $speed; $step++) {
            $ahead = $ahead->moveToDirection($heading);
            if (isset($blocked[$ahead->q . ',' . $ahead->r])) $aheadBlocked = true;
        }
        if ($aheadBlocked) $score += AIConfig::MOVE_COST_NEXT_TURN;
        $score += Hazards::hexesOffMap($ahead, $bounds) * AIConfig::MOVE_COST_PER_HEX_OVER;
        return $score;
    }

    /* The rows for one ship. $blocked: "q,r" => true for every hex not to be entered. $bounds:
       array(halfWidth, halfHeight) of the map, or null when it has no edge. */
    public static function choose(ShipMover $mover, array $blocked, $bounds, $limit = AIConfig::MOVE_PLANS)
    {
        $straight = clone $mover;
        if (!$straight->finish()) return array(); //cannot happen: a straight move needs nothing

        $state = $straight->state();
        $cruise = $state['speed'];
        $best = self::score($straight->path(), $state['position'], $state['heading'], $cruise, $cruise, array(), $blocked, $bounds);
        $rows = $straight->rows();
        if ($best == 0) return $rows;

        foreach ($mover->enumerate($limit) as $plan) {
            $score = self::score($plan['path'], $plan['position'], $plan['heading'], $plan['speed'], $cruise, $plan['actions'], $blocked, $bounds);
            if ($score < $best) {
                $best = $score;
                $rows = $plan['rows'];
            }
        }

        return $rows;
    }

    /* How bad a plan is; 0 = nothing wrong with it. Entering a blocked hex or ending off the map
       this turn outweighs everything. Next turn's straight continuation counts too, by how far
       past the edge it would carry the ship - so a turn that only points the ship less at the
       edge already scores better, and the ship comes round over two turns instead of flying out.
       Losing speed costs: a ship that can turn away keeps its way on rather than braking to a
       halt at the edge. Each order costs a little, so the plainest plan among equals wins, and a
       ship in open space never manoeuvres at all. */
    private static function score(array $path, OffsetCoordinate $end, $heading, $speed, $cruise, array $actions, array $blocked, $bounds)
    {
        $score = self::hazard($path, $end, $heading, $speed, $blocked, $bounds);
        if ($score == 0) return 0;

        $score += max(0, $cruise - $speed) * AIConfig::MOVE_COST_SPEED_LOST;
        foreach ($actions as $action) {
            if ($action === ShipMover::ACCEL || $action === ShipMover::DECEL) $score += 3;
            else if ($action !== ShipMover::MOVE) $score += 1;
        }

        return $score;
    }

}
