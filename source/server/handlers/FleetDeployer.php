<?php
/**
 * Fleet deployment in formation (TACTICAL_AI_PLAN.md section 5.2, stage T1a).
 *
 * A stand-alone service with two callers and no dependency on the CPU opponent:
 *   - the player's Auto-Deploy button (autoDeploy.php -> Manager::getAutoDeployPlan);
 *   - the CPU opponent's DeployDecider.
 *
 * plan() CHANGES NOTHING. It returns where each unit should stand; the caller places them - the
 * player's page through its own manual-placement functions, the bot as ordinary deploy rows.
 *
 * THE FORMATION. The slot's units are dealt into battlegroups, each built round an anchor (a
 * Capital or Enormous hull; with none, the largest hull size present). The groups are spread along
 * the deployment zone's LONG axis. Inside a group, seen from the enemy:
 *
 *      depth +1   fighter flights, a screen in front
 *      depth  0   the line: anchors in the centre, then HCVs, medium ships, LCVs outward
 *      depth -1   dedicated carriers and ELINT ships, behind the centre
 *      depth -2.. whatever did not fit on the line
 *
 * LEGALITY. Every hex returned satisfies DeploymentGamePhase::validateDeploymentArea (the rule
 * that judges the submission) AND the game page's slightly tighter box test, so a player's page
 * accepts the layout for commit. Terrain is never stood on; two ships never share a hex (the
 * page's rule - flights may share). A wanted hex that is not acceptable is replaced by the nearest
 * one that is; a unit with nowhere to go is left OUT of the plan for the caller to deal with.
 *
 * NOT PLACED BY IT: bases, OSATs and mines (mines have their own Deploy Minefield tool), terrain,
 * reinforcements arriving from hyperspace, and anything not picking its hex this turn.
 *
 * Deterministic: the same game state and options give the same layout. options.seed varies it -
 * the Auto-Deploy panel's Shuffle, and a bot's own seat seed: seed 0 is the standard layout;
 * any other mirrors the wings (odd seeds), changes which battlegroup stands where, and re-deals
 * the places among hulls of the SAME size class (and among the flights), so capitals still hold
 * the centre and the lighter hulls the wings.
 */
class FleetDeployer
{
    const LINE_SPACING = 2;          //hexes between neighbours on the line, when the zone has room
    const GROUP_GAP = 2;             //hexes kept clear between two battlegroups
    const CARRIER_MIN_FIGHTERS = 36; //hangar capacity from which a hull counts as a dedicated carrier
    const MIN_SPEED = 3;             //a group's hulls are never slowed below this - even a cumbersome ship starts at 3 or 4
    const FLIGHT_MIN_SPEED = 6;      //flights start faster than the line: two thirds of their thrust,
    const FLIGHT_MAX_SPEED = 9;      //kept within 6..9
    const SEARCH_RADIUS = 14;        //how far from its wanted hex a unit may end up

    /* The units of $playerid in slot $slotid that plan() would place. $unitIds (optional) narrows
       them to the listed ship ids - the page sends the ones it wants placed. */
    public static function eligibleUnits(TacGamedata $gamedata, $playerid, $slotid, $unitIds = null)
    {
        $wanted = null;
        if (is_array($unitIds)) {
            $wanted = array();
            foreach ($unitIds as $id) $wanted[(int)$id] = true;
        }

        $units = array();
        foreach ($gamedata->ships as $ship) {
            if ((int)$ship->userid !== (int)$playerid || (int)$ship->slot !== (int)$slotid) continue;
            if ($wanted !== null && !isset($wanted[(int)$ship->id])) continue;
            if ($ship->isDestroyed() || $ship->isTerrain() || !empty($ship->removed)) continue;
            if (!empty($ship->base) || !empty($ship->smallBase) || !empty($ship->osat) || !empty($ship->mine)) continue;
            if ($ship->getTurnPlaced($gamedata) != $gamedata->turn) continue;
            if (JumpEngine::isArrivingReinforcement($ship, $gamedata)) continue;
            if (self::committedDeployRow($ship, $gamedata->turn) !== null) continue; //placed and committed: no longer movable
            if (!$ship->getLastMovement()) continue;

            $units[] = $ship;
        }

        return $units;
    }

    //How many battlegroups this slot's fleet can form: its anchors (see the header). At least 1.
    public static function maxGroups(TacGamedata $gamedata, $playerid, $slotid, $unitIds = null)
    {
        return max(1, count(self::anchors(self::eligibleUnits($gamedata, $playerid, $slotid, $unitIds))));
    }

    /* The plan: a list of array('shipid', 'x', 'y', 'facing', 'speed', 'group') - x/y are the hex's
       q/r. Options:
         groups    how many battlegroups (clamped to 1..maxGroups); default 1
         unitIds   only these ship ids (default: every eligible unit of the slot)
         occupied  hexes the caller knows to be in use and this cannot see - a page's placements
                   not yet committed: list of array('q', 'r', 'flight' => bool)
         seed      varies the layout; default 0 */
    public static function plan(TacGamedata $gamedata, $playerid, $slotid, array $options = array())
    {
        $slot = $gamedata->getSlotById($slotid);
        if (!$slot) return array();

        $units = self::eligibleUnits($gamedata, $playerid, $slotid, isset($options['unitIds']) ? $options['unitIds'] : null);
        if (count($units) == 0) return array();

        $seed = isset($options['seed']) ? abs((int)$options['seed']) : 0;
        $anchors = self::anchors($units);
        $groupCount = max(1, min(count($anchors), isset($options['groups']) ? (int)$options['groups'] : 1));

        $groups = self::dealIntoGroups($units, $anchors, $groupCount);
        if ($seed > 0) {
            for ($i = 0; $i < $seed % $groupCount; $i++) array_push($groups, array_shift($groups));
        }
        $mirror = ($seed % 2 == 1) ? -1 : 1;

        $frame = self::frame($gamedata, $slot, $units[0]);
        $board = self::board($gamedata, $slot, isset($options['occupied']) ? $options['occupied'] : array());

        $span = max(1, (int)floor($frame['length'] / $groupCount));
        $roomy = max(1, $span - self::GROUP_GAP);

        $plan = array();
        foreach ($groups as $index => $group) {
            $centre = (int)round(-$frame['length'] / 2 + $span * ($index + 0.5));
            list($hullSpeed, $flightSpeed) = self::commonSpeeds($group['units']);

            foreach (self::formation($group, $roomy, $mirror, $seed) as $place) {
                list($ship, $lateral, $depth) = $place;
                $hex = self::hexAt($frame, $centre + $lateral, $depth);
                $hex = self::nearestAcceptable($gamedata, $ship, $hex, $slot, $board);
                if ($hex === null) continue;

                $key = $hex->q . ',' . $hex->r;
                $board['any'][$key] = true;
                if (!($ship instanceof FighterFlight)) $board['ship'][$key] = true;

                $plan[] = array(
                    'shipid' => (int)$ship->id,
                    'x' => (int)$hex->q,
                    'y' => (int)$hex->r,
                    'facing' => $frame['facing'] === null ? (int)$ship->getLastMovement()->facing : $frame['facing'],
                    'speed' => ($ship instanceof FighterFlight) ? $flightSpeed : $hullSpeed,
                    'group' => $index + 1
                );
            }
        }

        return $plan;
    }

    // ------------------------------------------------------------------ groups

    private static function isFlight($ship)
    {
        return $ship instanceof FighterFlight;
    }

    //What a unit is worth, for balancing the groups: its cost with enhancements (a flight: per craft).
    private static function value($ship)
    {
        $cost = (float)$ship->pointCost;
        if (self::isFlight($ship)) $cost = $cost / 6 * max(1, (int)$ship->flightSize);
        return $cost + (float)$ship->pointCostEnh;
    }

    private static function byValue(array $units)
    {
        usort($units, function ($a, $b) {
            $difference = self::value($b) - self::value($a);
            if (abs($difference) > 0.001) return $difference > 0 ? 1 : -1;
            return $a->id - $b->id;
        });
        return $units;
    }

    /* The hulls battlegroups form round: Capital and Enormous ones, or - a fleet without any - the
       hulls of the largest size class it has. A fleet of flights alone is one group. */
    private static function anchors(array $units)
    {
        $hulls = array_values(array_filter($units, function ($ship) { return !self::isFlight($ship); }));
        if (count($hulls) == 0) return array_slice(self::byValue($units), 0, 1);

        $largest = max(array_map(function ($ship) { return (int)$ship->shipSizeClass; }, $hulls));
        $size = min(3, $largest);

        return self::byValue(array_values(array_filter($hulls, function ($ship) use ($size) {
            return (int)$ship->shipSizeClass >= $size;
        })));
    }

    /* Anchors first, most valuable first, then everything else the same way - each to the group
       worth least so far. The first $count anchors therefore open one group each. */
    private static function dealIntoGroups(array $units, array $anchors, $count)
    {
        $groups = array();
        for ($i = 0; $i < $count; $i++) $groups[] = array('units' => array(), 'value' => 0.0, 'lead' => null);

        $anchorIds = array();
        foreach ($anchors as $ship) $anchorIds[$ship->id] = true;
        $rest = self::byValue(array_values(array_filter($units, function ($ship) use ($anchorIds) { return !isset($anchorIds[$ship->id]); })));

        foreach (array_merge($anchors, $rest) as $ship) {
            $target = 0;
            for ($i = 1; $i < $count; $i++) {
                if ($groups[$i]['value'] < $groups[$target]['value'] - 0.001) $target = $i;
            }
            if ($groups[$target]['lead'] === null) $groups[$target]['lead'] = $ship->id;
            $groups[$target]['units'][] = $ship;
            $groups[$target]['value'] += self::value($ship);
        }

        return $groups;
    }

    // ------------------------------------------------------------------ formation

    private static function isSupport($ship)
    {
        foreach ($ship->systems as $system) {
            if ($system instanceof ElintScanner) return true;
        }

        $capacity = 0;
        if (isset($ship->fighters) && is_array($ship->fighters)) {
            foreach ($ship->fighters as $boxes) $capacity += (int)$boxes;
        }
        return $capacity >= self::CARRIER_MIN_FIGHTERS;
    }

    /* One group -> list of array(ship, lateral, depth), lateral counted from the group's centre.
       $room is how many hexes of the long axis the group has to itself. */
    private static function formation(array $group, $room, $mirror, $seed = 0)
    {
        $line = array();
        $support = array();
        $screen = array();

        foreach ($group['units'] as $ship) {
            if (self::isFlight($ship)) $screen[] = $ship;
            else if ($ship->id !== $group['lead'] && self::isSupport($ship)) $support[] = $ship;
            else $line[] = $ship;
        }

        /* Centre outward: biggest hulls first, the group's lead before its equals, then by value.
           A seeded (shuffled) layout orders hulls of one size class by a number made from the seed
           and the unit's id instead - a different deal for every seed, the same one every time
           for the same seed. */
        $lead = $group['lead'];
        $lot = function ($ship) use ($seed) { return crc32($seed . ':' . $ship->id); };
        $bySize = function ($a, $b) use ($lead, $seed, $lot) {
            if ((int)$a->shipSizeClass !== (int)$b->shipSizeClass) return (int)$b->shipSizeClass - (int)$a->shipSizeClass;
            if ($seed > 0 && $lot($a) !== $lot($b)) return $lot($a) < $lot($b) ? -1 : 1;
            if ($a->id === $lead) return -1;
            if ($b->id === $lead) return 1;
            $difference = self::value($b) - self::value($a);
            if (abs($difference) > 0.001) return $difference > 0 ? 1 : -1;
            return $a->id - $b->id;
        };
        usort($line, $bySize);
        usort($support, $bySize);
        $screen = self::byValue($screen);
        if ($seed > 0) usort($screen, function ($a, $b) use ($lot) { return $lot($a) < $lot($b) ? -1 : ($lot($a) > $lot($b) ? 1 : $a->id - $b->id); });

        $spacing = (self::rankCapacity($room, self::LINE_SPACING) >= count($line)) ? self::LINE_SPACING : 1;
        $capacity = self::rankCapacity($room, $spacing);

        $places = array();

        //The line, and behind it (past the support row) whatever it could not hold.
        $overflow = array_splice($line, $capacity);
        foreach ($line as $k => $ship) $places[] = array($ship, self::outward($k) * $spacing * $mirror, 0);
        foreach ($support as $k => $ship) $places[] = array($ship, self::outward($k % $capacity) * $spacing * $mirror, -1 - (int)floor($k / $capacity));
        $behind = -2 - (int)floor(max(0, count($support) - 1) / $capacity);
        foreach ($overflow as $k => $ship) $places[] = array($ship, self::outward($k % $capacity) * $spacing * $mirror, $behind - (int)floor($k / $capacity));

        //The screen: one flight a hex, in front; a second rank in front of that if it must.
        $screenCapacity = self::rankCapacity($room, 1);
        foreach ($screen as $k => $ship) $places[] = array($ship, self::outward($k % $screenCapacity) * $mirror, 1 + (int)floor($k / $screenCapacity));

        return $places;
    }

    private static function rankCapacity($room, $spacing)
    {
        return max(1, (int)floor(($room - 1) / $spacing) + 1);
    }

    //0, 1, -1, 2, -2, ...
    private static function outward($k)
    {
        return ($k % 2 == 1) ? (int)(($k + 1) / 2) : -(int)($k / 2);
    }

    /* The group's two starting speeds: array(hulls, flights).

       HULLS share one speed, so the line starts in formation: the speed they stand at (the game's
       starting speed), lowered while any of them would need its whole engine output (or more) for
       one turn at it - but never below MIN_SPEED (user ruling 2026-11-13: even cumbersome ships,
       Hurr and Grome hulls for instance, should start at 3 or 4). Never raised.

       FLIGHTS share one speed too, and a faster one (same ruling: fighters often start at 6-9):
       two thirds of the slowest flight's thrust, kept within FLIGHT_MIN_SPEED..FLIGHT_MAX_SPEED. */
    private static function commonSpeeds(array $units)
    {
        $hullSpeed = null;
        $flightThrust = null;

        foreach ($units as $ship) {
            if (self::isFlight($ship)) {
                $thrust = (int)$ship->freethrust;
                if ($thrust > 0) $flightThrust = ($flightThrust === null) ? $thrust : min($flightThrust, $thrust);
                continue;
            }
            $start = (int)$ship->getLastMovement()->speed;
            $hullSpeed = ($hullSpeed === null) ? $start : min($hullSpeed, $start);
        }

        $hullSpeed = (int)$hullSpeed;
        foreach ($units as $ship) {
            if (self::isFlight($ship)) continue;

            $thrust = 0;
            foreach ($ship->systems as $system) {
                if ($system instanceof Engine && !$system->isDestroyed()) $thrust += (int)$system->output;
            }
            $turncost = (float)$ship->turncost;
            if ($thrust <= 0 || $turncost <= 0) continue;

            while ($hullSpeed > self::MIN_SPEED && ceil($turncost * $hullSpeed) >= $thrust) $hullSpeed--;
        }

        $flightSpeed = ($flightThrust === null) ? self::FLIGHT_MIN_SPEED
            : max(self::FLIGHT_MIN_SPEED, min(self::FLIGHT_MAX_SPEED, (int)round($flightThrust * 2 / 3)));

        return array($hullSpeed, $flightSpeed);
    }

    // ------------------------------------------------------------------ the zone

    /* How the formation lies in the slot's zone: its centre, which hex axis is the long one, which
       way is "towards the enemy" on the short one, how many hexes long the zone is, and the facing
       that looks at the enemy (null = no enemy zone to look at: keep each unit's own). */
    private static function frame(TacGamedata $gamedata, $slot, $anyUnit)
    {
        $centre = new OffsetCoordinate((int)$slot->depx, (int)$slot->depy);
        $centrePx = Mathlib::hexCoToPixel($centre);

        $width = max(1, (int)$slot->depwidth);
        $height = max(1, (int)$slot->depheight);
        if ($slot->deptype !== 'box') $width = $height = 2 * max($width, $height); //a radius, not a side
        $vertical = ($height * 1.5 >= $width * sqrt(3));

        //Where the enemy starts: the middle of the other teams' zones.
        $enemyX = 0; $enemyY = 0; $enemies = 0;
        foreach ($gamedata->slots as $other) {
            if ($other->team == $slot->team) continue;
            $px = Mathlib::hexCoToPixel(new OffsetCoordinate((int)$other->depx, (int)$other->depy));
            $enemyX += $px['x']; $enemyY += $px['y']; $enemies++;
        }

        $facing = null;
        $dx = 0; $dy = 0;
        if ($enemies > 0) {
            $dx = $enemyX / $enemies - $centrePx['x'];
            $dy = $enemyY / $enemies - $centrePx['y'];
        }
        if (abs($dx) > 0.01 || abs($dy) > 0.01) {
            $best = null;
            for ($direction = 0; $direction < 6; $direction++) {
                $stepPx = Mathlib::hexCoToPixel((new OffsetCoordinate(0, 0))->moveToDirection($direction));
                $dot = $stepPx['x'] * $dx + $stepPx['y'] * $dy;
                if ($best === null || $dot > $best + 0.0001) { $best = $dot; $facing = $direction; }
            }
        } else {
            //No enemy zone, or it is centred on this one: "forward" is the way the units already look.
            $own = (int)$anyUnit->getLastMovement()->facing;
            $stepPx = Mathlib::hexCoToPixel((new OffsetCoordinate(0, 0))->moveToDirection($own));
            $dx = $stepPx['x']; $dy = $stepPx['y'];
        }

        $forward = $vertical ? (($dx >= 0) ? 1 : -1) : (($dy >= 0) ? 1 : -1);

        return array('q' => $centre->q, 'r' => $centre->r, 'vertical' => $vertical, 'forward' => $forward,
            'length' => $vertical ? $height : $width, 'facing' => $facing);
    }

    private static function hexAt(array $frame, $lateral, $depth)
    {
        if ($frame['vertical']) return new OffsetCoordinate($frame['q'] + $depth * $frame['forward'], $frame['r'] + $lateral);
        return new OffsetCoordinate($frame['q'] + $lateral, $frame['r'] + $depth * $frame['forward']);
    }

    /* What already stands where: 'hard' = terrain (nothing may go there), 'ship' = a hull (no second
       hull), 'any' = anything at all (avoided while there is room). Built from terrain, this
       team's units already on the board or committed this phase, and the caller's own list. */
    private static function board(TacGamedata $gamedata, $slot, $occupied)
    {
        $board = array('hard' => array(), 'ship' => array(), 'any' => array());

        foreach ($gamedata->blockedHexes ?: array() as $hex) {
            $blocked = new OffsetCoordinate($hex);
            $board['hard'][$blocked->q . ',' . $blocked->r] = true;
        }

        foreach ($gamedata->ships as $ship) {
            if ($ship->isDestroyed() || !empty($ship->removed)) continue;

            if ($ship->isTerrain()) {
                if ($ship->isReinforcement() || !$ship->getLastMovement()) continue;
                foreach (self::terrainHexes($ship) as $hex) $board['hard'][$hex->q . ',' . $hex->r] = true;
                continue;
            }

            if ($ship->team != $slot->team) continue; //never reads where an opponent stands

            $row = self::committedDeployRow($ship, $gamedata->turn);
            if ($row !== null) $position = new OffsetCoordinate($row->position);
            else if ($ship->getTurnPlaced($gamedata) < $gamedata->turn && $ship->getTurnDeployed($gamedata) <= $gamedata->turn) $position = $ship->getHexPos();
            else continue;

            $key = $position->q . ',' . $position->r;
            $board['any'][$key] = true;
            if (!($ship instanceof FighterFlight) && empty($ship->mine)) $board['ship'][$key] = true;
        }

        if (is_array($occupied)) {
            foreach ($occupied as $entry) {
                if (!is_array($entry) || !isset($entry['q'], $entry['r']) || !is_numeric($entry['q']) || !is_numeric($entry['r'])) continue;
                $key = (int)$entry['q'] . ',' . (int)$entry['r'];
                $board['any'][$key] = true;
                if (empty($entry['flight'])) $board['ship'][$key] = true;
            }
        }

        return $board;
    }

    //Every hex a terrain unit covers (BuyingGamePhase places them by the same footprint).
    private static function terrainHexes($ship)
    {
        $centre = $ship->getHexPos();
        $hexes = array($centre);

        if (property_exists($ship, 'hexOffsets') && is_array($ship->hexOffsets) && count($ship->hexOffsets) > 0) {
            $facing = $ship->getLastMovement()->facing;
            foreach ($ship->hexOffsets as $offset) $hexes[] = Mathlib::getRotatedHex($centre, $offset, $facing);
        } else if ($ship->Huge > 0) {
            foreach (Mathlib::getNeighbouringHexes($centre, $ship->Huge) as $n) $hexes[] = new OffsetCoordinate($n['q'], $n['r']);
        }

        return $hexes;
    }

    private static function committedDeployRow($ship, $turn)
    {
        foreach ($ship->movement as $move) {
            if ($move->type == 'deploy' && $move->turn == $turn) return $move;
        }
        return null;
    }

    // ------------------------------------------------------------------ legality

    /* May $ship stand on $hex as far as its zone goes? The server's rule, and for a box zone the
       game page's own test as well (DeploymentPhaseStrategy getValidDeploymentCallback: strictly
       inside half the box each way) - it is a little tighter at the edges, and a layout the page
       refuses could not be committed. */
    public static function inZone(TacGamedata $gamedata, $ship, OffsetCoordinate $hex, $slot)
    {
        $start = $ship->getLastMovement();
        $probe = new MovementOrder(-1, 'deploy', $hex, 0, 0, $start->speed, $start->heading, $start->facing, false, $gamedata->turn, 0, 0);
        if (!DeploymentGamePhase::validateDeploymentArea($gamedata, $ship, $probe)) return false;

        if ($slot->deptype === 'box') {
            $at = Mathlib::hexCoToPixel($hex);
            $centre = Mathlib::hexCoToPixel(new OffsetCoordinate((int)$slot->depx, (int)$slot->depy));
            if (abs($centre['x'] - $at['x']) >= $slot->depwidth * sqrt(3) / 2 - 0.05) return false;
            if (abs($centre['y'] - $at['y']) >= $slot->depheight * 1.5 / 2 - 0.05) return false;
        }

        return true;
    }

    /* $wanted if the unit may stand there, else the nearest hex where it may - first looking for
       an empty one, then (a hull) one holding only flights, or (a flight) any without terrain. */
    private static function nearestAcceptable(TacGamedata $gamedata, $ship, OffsetCoordinate $wanted, $slot, array $board)
    {
        $flight = self::isFlight($ship);
        $candidates = self::hexesAround($wanted, self::SEARCH_RADIUS);

        foreach (array(true, false) as $wantEmpty) {
            foreach ($candidates as $hex) {
                $key = $hex->q . ',' . $hex->r;
                if (isset($board['hard'][$key])) continue;
                if ($wantEmpty && isset($board['any'][$key])) continue;
                if (!$wantEmpty && !$flight && isset($board['ship'][$key])) continue;
                if (self::inZone($gamedata, $ship, $hex, $slot)) return $hex;
            }
        }

        return null;
    }

    //Every hex within $radius of $centre, nearest first; ties in a fixed order (row, then column).
    private static function hexesAround(OffsetCoordinate $centre, $radius)
    {
        $hexes = array();
        for ($dr = -$radius; $dr <= $radius; $dr++) {
            for ($dq = -$radius - 1; $dq <= $radius + 1; $dq++) {
                $hex = new OffsetCoordinate($centre->q + $dq, $centre->r + $dr);
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
}
