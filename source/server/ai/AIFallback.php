<?php
/**
 * CPU opponent - the fallback orders (TACTICAL_AI_PLAN.md section 3.3, principle P3).
 *
 * For every phase, the safest legal "do nothing": the orders a bot submits when it has no decider
 * for the phase yet, and the ones it falls back to when a decider's orders are refused. A game
 * with a bot in it can therefore always move on.
 *
 *   Deployment (-1)     first free legal hex of the slot's deployment zone, as the unit stands
 *   Initial Orders (1)  all EW to defence; no power changes, no ballistics
 *   Movement (2)        straight on at the current speed
 *   Pre-Firing (5)      nothing
 *   Firing (3)          nothing (the server still intercepts by itself)
 *
 * Until the deciders exist (stages T1-T5) these ARE the bot's play.
 *
 * NOT YET, and each waits for the stage that owns it:
 *   - Initial Orders here does not clear negative power: that is PowerDecider's job (stage T3).
 *     The server does not refuse a commit for it - the client does - so this stays the plain
 *     retry for when the deciders' orders are refused.
 *   - Movement here never turns: a unit flying straight into terrain collides, as a human's
 *     would. Steering clear is MoveDecider's job (stage T1b, on ShipMover); this stays the plain
 *     retry for when its orders are refused.
 *
 * Every method returns the plain entries AIOrderBuilder describes - one per unit the bot owns.
 */
class AIFallback
{
    //Entries for the game's current phase.
    public static function orders(TacGamedata $gamedata, $botId)
    {
        switch ((int)$gamedata->phase) {
            case -1: return self::deployment($gamedata, $botId);
            case 1:  return self::initialOrders($gamedata, $botId);
            case 2:  return self::movement($gamedata, $botId);
            default: return self::nothing($gamedata, $botId); //Pre-Firing, Firing
        }
    }

    //The bot's own units, in the game's order.
    public static function ownUnits(TacGamedata $gamedata, $botId)
    {
        $units = array();
        foreach ($gamedata->ships as $ship) {
            if ((int)$ship->userid === (int)$botId) $units[] = $ship;
        }
        return $units;
    }

    private static function nothing(TacGamedata $gamedata, $botId)
    {
        $entries = array();
        foreach (self::ownUnits($gamedata, $botId) as $ship) $entries[] = AIOrderBuilder::entry($ship);
        return $entries;
    }

    /* DEPLOYMENT. A unit placing this turn gets one 'deploy' row; every other unit gets none
       (DeploymentGamePhase::validateDeployment asks for exactly that). The hex is the first one,
       working outward from the centre of the slot's zone, that the server's own rule accepts and
       that neither terrain nor another unit of this fleet already stands on.

       Facing, heading and speed are the 'start' row's, which is what the client's deploy copies
       (movement.js deploy) - except that a unit which cannot move is placed at speed 0. */
    /* $planned (DeployDecider, stage T1a): ship id => array('x', 'y', 'facing', 'speed') for the
       units a formation has a place for. Those stand where it says; every other unit that places
       now - a base, an OSAT, a mine, one the formation had no room for - gets the fallback hex,
       clear of the planned ones. */
    public static function deployment(TacGamedata $gamedata, $botId, array $planned = array())
    {
        $entries = array();
        $taken = array();
        foreach ($gamedata->blockedHexes ?: array() as $hex) {
            $blocked = new OffsetCoordinate($hex);
            $taken[$blocked->q . ',' . $blocked->r] = true;
        }
        foreach ($planned as $place) $taken[(int)$place['x'] . ',' . (int)$place['y']] = true;

        foreach (self::ownUnits($gamedata, $botId) as $ship) {
            $entry = AIOrderBuilder::entry($ship);

            if (self::placesNow($ship, $gamedata)) {
                $start = $ship->getLastMovement();

                if ($start && isset($planned[(int)$ship->id])) {
                    $place = $planned[(int)$ship->id];
                    $entry['movement'][] = AIOrderBuilder::move('deploy', new OffsetCoordinate((int)$place['x'], (int)$place['y']),
                        $place['speed'], $place['facing'], $place['facing'], $gamedata->turn, 0);
                    $entries[] = $entry;
                    continue;
                }

                $hex = self::findDeploymentHex($ship, $gamedata, $start, $taken);
                if ($hex !== null && $start) {
                    $taken[$hex->q . ',' . $hex->r] = true;
                    $speed = self::isImmobile($ship) ? 0 : $start->speed;
                    $entry['movement'][] = AIOrderBuilder::move('deploy', $hex, $speed, $start->heading, $start->facing, $gamedata->turn, 0);
                }
            }

            $entries[] = $entry;
        }

        return $entries;
    }

    //Must this unit supply a deploy row in this Deployment phase?
    private static function placesNow($ship, TacGamedata $gamedata)
    {
        if ($ship->isDestroyed() || $ship->isTerrain()) return false;
        if ($ship->getTurnPlaced($gamedata) != $gamedata->turn) return false;

        //An arrival from hyperspace is placed by the server (JumpEngine::placeArrivingReinforcements);
        //where it was not, leaving it in hyperspace is the legal "do nothing".
        if (JumpEngine::isArrivingReinforcement($ship, $gamedata)) return false;

        foreach ($ship->movement as $move) {
            if ($move->type == 'deploy' && $move->turn == $gamedata->turn) return false; //already placed
        }

        return true;
    }

    private static function isImmobile($ship)
    {
        return !empty($ship->base) || !empty($ship->smallBase) || !empty($ship->osat) || !empty($ship->mine);
    }

    /* The first acceptable hex for $ship, nearest the zone's centre first, or null. A second pass
       allows a hex this fleet already uses: the server has no one-unit-per-hex rule for deployment,
       and a stacked fleet is better than a fleet that cannot be placed. */
    private static function findDeploymentHex($ship, TacGamedata $gamedata, $start, array $taken)
    {
        if (!$start) return null;
        $slot = $gamedata->getSlotById($ship->slot);
        if (!$slot) return null;

        $candidates = self::hexesAround((int)$slot->depx, (int)$slot->depy, max((int)$slot->depwidth, (int)$slot->depheight) + 1);

        foreach (array(false, true) as $allowStacking) {
            foreach ($candidates as $hex) {
                if (!$allowStacking && isset($taken[$hex->q . ',' . $hex->r])) continue;

                $probe = new MovementOrder(-1, 'deploy', $hex, 0, 0, $start->speed, $start->heading, $start->facing, false, $gamedata->turn, 0, 0);
                if (DeploymentGamePhase::validateDeploymentArea($gamedata, $ship, $probe)) return $hex;
            }
        }

        return null;
    }

    //Every hex within $radius of ($q, $r), nearest first; ties in a fixed order (row, then column).
    private static function hexesAround($q, $r, $radius)
    {
        $radius = max(1, min(40, (int)$radius));
        $centre = new OffsetCoordinate($q, $r);

        $hexes = array();
        for ($dr = -$radius; $dr <= $radius; $dr++) {
            for ($dq = -$radius; $dq <= $radius; $dq++) {
                $hex = new OffsetCoordinate($q + $dq, $r + $dr);
                $distance = (int)$centre->distanceTo($hex);
                if ($distance > $radius) continue;
                $hexes[] = array($distance, $hex->r, $hex->q, $hex);
            }
        }

        usort($hexes, function ($a, $b) {
            if ($a[0] !== $b[0]) return $a[0] - $b[0];
            if ($a[1] !== $b[1]) return $a[1] - $b[1];
            return $a[2] - $b[2];
        });

        return array_map(function ($row) { return $row[3]; }, $hexes);
    }

    /* INITIAL ORDERS. Every ship puts its whole sensor output into defensive EW; the client turns
       unspent EW into DEW at commit in the same way. Flights have no EW of their own to set. */
    private static function initialOrders(TacGamedata $gamedata, $botId)
    {
        $entries = array();

        foreach (self::ownUnits($gamedata, $botId) as $ship) {
            $entry = AIOrderBuilder::entry($ship);

            if (!($ship instanceof FighterFlight) && !$ship->isDestroyed() && !$ship->isTerrain()
                && $ship->getTurnDeployed($gamedata) <= $gamedata->turn) {
                $output = (int)EW::getScannerOutput($ship, $gamedata->turn);
                if ($output > 0) $entry['EW'][] = AIOrderBuilder::ew($ship->id, $gamedata->turn, 'DEW', $output);
            }

            $entries[] = $entry;
        }

        return $entries;
    }

    /* MOVEMENT. Each of the bot's ACTIVE units flies straight on: one 'move' row per hex of its
       speed, along its heading. No thrust is needed for that, so there is nothing to pay.
       A unit that cannot move, or is riding another unit, sends no rows - the same empty list the
       client sends for it. */
    private static function movement(TacGamedata $gamedata, $botId)
    {
        $active = array();
        foreach ($gamedata->getMyActiveShips() as $ship) $active[$ship->id] = true;

        $entries = array();

        foreach (self::ownUnits($gamedata, $botId) as $ship) {
            $entry = AIOrderBuilder::entry($ship);

            if (isset($active[$ship->id])) $entry['movement'] = self::straightRows($ship, $gamedata);

            $entries[] = $entry;
        }

        return $entries;
    }

    //One unit's "straight on" rows; none for a unit that cannot move or is riding another.
    public static function straightRows($ship, TacGamedata $gamedata)
    {
        $rows = array();
        if ($ship->isDestroyed() || self::isImmobile($ship) || !empty($ship->attached)) return $rows;

        $last = $ship->getLastMovement();
        if (!$last) return $rows;

        /* Stage T5f-5: a ship owing an extended turn (a Hard bot begins them) completes it before anything else - or the page
           has cancelled it at activation, which ShipMover writes the same way - and only then flies on. */
        if (Movement::getOwedExtendedTurn($ship->movement, $gamedata->turn)) {
            try {
                $mover = ShipMover::forShip($ship, $gamedata);
                if ($mover !== null) {
                    $mover->apply(ShipMover::COMPLETE_EXTENDED); //refused when the page has cancelled it (the forced row is already there)
                    if ($mover->finish()) return $mover->rows();
                }
            } catch (Throwable $e) {
                //the plain rows below
            }
        }

        $order = AIOrderBuilder::initiativeOrder($ship, $gamedata);
        $position = new OffsetCoordinate($last->position);
        for ($step = 0; $step < (int)$last->speed; $step++) {
            $position = $position->moveToDirection((int)$last->heading);
            $rows[] = AIOrderBuilder::move('move', $position, $last->speed, $last->heading, $last->facing, $gamedata->turn, $order);
        }

        return $rows;
    }
}
