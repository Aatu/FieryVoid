<?php
/**
 * CPU opponent - Movement (TACTICAL_AI_PLAN.md sections 3.3 and 5.4).
 *
 * STAGE T1b: there is no tactics here yet. A ship HOLDS ITS COURSE - straight on at its speed -
 * unless that would fly it into terrain or off the map, this turn or the next; then it takes the
 * plainest legal plan ShipMover has that does not. That closes the gap the T0 fallback left
 * ("movement never turns away from terrain or the map edge"). Choosing where to go - targets,
 * range bands, arcs - is stage T5, and replaces score() below.
 *
 * FIGHTER FLIGHTS (stage T4) go where FlightDecider sends them - strike, intercept or escort -
 * among the plans ShipMover finds for them.
 *
 * Units ShipMover does not plot for (Hunter-Killers, gravitic hulls, a ship carrying another...)
 * keep the fallback's rows: straight on.
 *
 * The decision is made on the bot's own MASKED view of the game (principle P2).
 */
class MoveDecider
{
    //One line about the last orders(), for tac_ai_log (only when a flight was moved).
    public static $lastSummary = null;

    //The bot's Movement entries, one per unit it owns. $seat is its DBManager::getCpuSeats row.
    public static function orders(TacGamedata $gamedata, $botId, array $seat)
    {
        $gamedata->prepareForPlayer(); //the mask a human in this seat plays behind
        self::$lastSummary = null;
        FlightDecider::reset();
        $view = null;

        $active = array();
        foreach ($gamedata->getMyActiveShips() as $ship) $active[$ship->id] = true;

        $blocked = self::blockedKeys($gamedata);
        $bounds = self::mapBounds($gamedata);

        $entries = array();
        $flights = 0;
        foreach (AIFallback::ownUnits($gamedata, $botId) as $ship) {
            $entry = AIOrderBuilder::entry($ship);

            if (isset($active[$ship->id])) {
                $mover = ShipMover::forShip($ship, $gamedata);
                if ($mover !== null && $mover->isFlight()) {
                    if ($view === null) $view = new AIView($gamedata, $botId);
                    $entry['movement'] = FlightDecider::rows($view, $mover, $ship, $blocked, $bounds);
                    $flights++;
                } else {
                    $entry['movement'] = ($mover !== null) ? self::choose($mover, $blocked, $bounds) : AIFallback::straightRows($ship, $gamedata);
                }
            }

            $entries[] = $entry;
        }

        if ($flights > 0) {
            $f = FlightDecider::$last;
            self::$lastSummary = $flights . ' flights: ' . $f['strike'] . ' striking, ' . $f['intercept'] . ' intercepting, ' . $f['escort'] . ' escorting; ' . $f['jinking'] . ' jinking';
        }

        return $entries;
    }

    /* The part of score() that is about the board, not the plan's manoeuvres: a blocked hex entered,
       ending off the map, and the same on next turn's straight continuation. 0 = nothing wrong. */
    public static function hazard(array $path, OffsetCoordinate $end, $heading, $speed, array $blocked, $bounds)
    {
        $score = 0;
        foreach ($path as $hex) {
            if (isset($blocked[$hex->q . ',' . $hex->r])) $score += AIConfig::MOVE_COST_BLOCKED;
        }
        $over = self::hexesOffMap($end, $bounds);
        if ($over > 0) $score += AIConfig::MOVE_COST_OFF_MAP + $over * AIConfig::MOVE_COST_PER_HEX_OVER;

        $ahead = $end;
        $aheadBlocked = false;
        for ($step = 0; $step < $speed; $step++) {
            $ahead = $ahead->moveToDirection($heading);
            if (isset($blocked[$ahead->q . ',' . $ahead->r])) $aheadBlocked = true;
        }
        if ($aheadBlocked) $score += AIConfig::MOVE_COST_NEXT_TURN;
        $score += self::hexesOffMap($ahead, $bounds) * AIConfig::MOVE_COST_PER_HEX_OVER;
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

    //How far past the map's edge a hex lies (0 = on the map).
    private static function hexesOffMap(OffsetCoordinate $hex, $bounds)
    {
        if ($bounds === null) return 0;
        return max(0, abs($hex->q) - $bounds[0]) + max(0, abs($hex->r) - $bounds[1]);
    }

    //Terrain and Enormous units, as the game itself lists them (TacGamedata::setBlockedHexes).
    private static function blockedKeys(TacGamedata $gamedata)
    {
        $keys = array();
        foreach ($gamedata->blockedHexes ?: array() as $hex) {
            $offset = new OffsetCoordinate($hex);
            $keys[$offset->q . ',' . $offset->r] = true;
        }
        return $keys;
    }

    //Half the map's width and height in hexes (the bounds terrain is laid out within), or null for an unlimited map.
    private static function mapBounds(TacGamedata $gamedata)
    {
        $width = 0;
        $height = 0;
        if (sscanf((string)$gamedata->gamespace, '%dx%d', $width, $height) !== 2 || $width <= 0 || $height <= 0) return null;
        return array(intdiv($width, 2), intdiv($height, 2));
    }
}
