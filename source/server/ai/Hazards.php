<?php
/**
 * CPU opponent - what the BOARD costs a unit for one plan (TACTICAL_AI_PLAN.md section 10.7, stage T5c).
 *
 * Built once per Movement decision from the bot's own MASKED view (principle P2: a mine the bot has not
 * detected is simply not on its board), then asked about every end state ShipDecider and FlightDecider score:
 *
 *   board    the fixed costs of stage T1b (MoveDecider::hazard, which this reproduces exactly on a board with
 *            nothing below on it): each blocked hex entered - terrain and Enormous units, MOVE_COST_BLOCKED -,
 *            ending off the map, and next turn's straight continuation into a blocked hex or off the map. A
 *            DRIFTING asteroid on the continuation counts by the chance that it will be standing there.
 *   drift    a drifting field (TerrainDrift: one-hex asteroids and meteor swarms, in a game whose rule says they
 *            drift) moves at the START of next turn - one hex straight on or 60 degrees to either side, or not at
 *            all, 25% each - and a unit standing in the hex it enters is struck in Pre-Firing on the side facing
 *            the drift (RammingAttack::getDriftOverruns). So ending in one of its three forward hexes costs 25% of
 *            that strike: an asteroid's d10 x speed, a swarm's meteors.
 *   meteors  a meteor swarm's hex ENTERED this turn: the meteors it rolls (the expectation of
 *            MeteorSwarm::rollMeteorChart, d10 + speed each); and next turn's continuation through one, by the
 *            chance it is there, weighted as next turn's terms are (the ship can still steer).
 *   mines    a mine the bot can see that would attack the unit (AoE.php: the first unit whose STARTING hex or
 *            any hex it enters lies within the mine's range for that kind of unit - halved against a Jammer -
 *            with a clear line; a Proximity or Captor mine attacks once and is gone, a DEW mine's guns go on
 *            firing). A plan that brings the unit within range pays the attack; when the unit already starts
 *            within range the mine fires whatever it does, so no plan is the worse for it.
 *
 * The priced terms are in VALUE - expected damage times what a point of the unit is worth - as the fire terms
 * are; the board's fixed costs outweigh everything, as they always have. Estimates for ranking, in the spirit of
 * BaseShip::setExpectedDamage: armour is not taken off, and a mine is not known to pick another unit first.
 *
 * NOT YET: dust clouds, gravity wells and the Triad's other spawned terrain are not priced (they never block a
 * line of fire, so blockedHexes does not list them either); the bot does not sweep for mines it cannot see.
 */
class Hazards
{
    //"q,r" => true for every hex never to be entered: terrain and Enormous units, as the game lists them (TacGamedata::setBlockedHexes).
    public $blocked = array();
    //array(halfWidth, halfHeight) of the map, or null for a map with no edge.
    public $bounds = null;

    /** @var AIView */
    private $view;
    private $blockedList = array();  //the same hexes as OffsetCoordinates, for the mines' line of sight
    private $fields = array();       //drifting fields: array('kind', 'unit', 'hex', 'now' => keys, 'overrun' => "q,r" => chance, 'possible' => "q,r" => chance)
    private $driftingNow = array();  //"q,r" => true: a drifting ASTEROID stands there now (blocked this turn, maybe not next)
    private $swarmsNow = array();    //"q,r" => true: a meteor swarm stands there now, drifting or not
    private $staticSwarms = array(); //"q,r" => true: ...one that will still be there next turn
    private $mines = array();        //the mines the bot can see that would attack its units

    public function __construct(AIView $view)
    {
        $this->view = $view;
        $gamedata = $view->gamedata;
        $this->blocked = self::blockedKeys($gamedata);
        $this->bounds = self::mapBounds($gamedata);
        foreach ($gamedata->blockedHexes ?: array() as $hex) $this->blockedList[] = ($hex instanceof OffsetCoordinate) ? $hex : new OffsetCoordinate($hex);

        $rule = $gamedata->rules ? $gamedata->rules->getRuleByName('terrainDrift') : null;
        foreach ($gamedata->ships as $unit) {
            if ($unit->isDestroyed() || $unit->getTurnDeployed($gamedata) > $view->turn) continue;
            if ($unit->isTerrain()) {
                $last = $unit->getLastMovement();
                if (!$last || !$last->position) continue;
                $hex = Movement::toOffset($last->position);
                if (!$hex) continue;
                $kind = TerrainDrift::getKind($unit);
                $drifts = ($kind !== null && $rule && $rule->drifts($kind));
                $footprint = RammingAttack::getTerrainHexesAt($unit, $hex, (int)$last->facing);
                if (!empty($unit->isMeteoroid)) {
                    foreach ($footprint as $h) {
                        $this->swarmsNow[$h->q . ',' . $h->r] = true;
                        if (!$drifts) $this->staticSwarms[$h->q . ',' . $h->r] = true;
                    }
                }
                if ($drifts) $this->addField($unit, $kind, $hex, (int)$last->heading, (int)$last->facing, $footprint);
                continue;
            }
            if (!empty($unit->mine)) $this->addMine($unit);
        }
    }

    // ------------------------------------------------------------------ what a plan costs

    /* Every term the board charges $unit for one plan. The caller has stood $unit at the plan's end; $start is the
       hex it began the turn in, $plan as ShipMover gives it (path, position, heading, speed), $worth what a point of
       damage to the unit is worth. array('board', 'drift', 'meteors', 'mines', 'total'). */
    public function judge($unit, OffsetCoordinate $start, array $plan, $worth)
    {
        $path = $plan['path'];
        $end = $plan['position'];
        $speed = (int)$plan['speed'];
        $continuation = self::continuation($end, (int)$plan['heading'], $speed);

        $terms = array('board' => $this->board($path, $end, $continuation), 'drift' => 0.0, 'meteors' => 0.0, 'mines' => 0.0);

        if (count($this->fields) > 0) $terms['drift'] = $this->overrun($unit, $end, $speed) * $worth;
        if (count($this->swarmsNow) > 0) {
            $perStrike = self::meteorHits($unit, $speed) * (5.5 + $speed);
            $entered = 0;
            foreach ($path as $hex) {
                if (isset($this->swarmsNow[$hex->q . ',' . $hex->r])) $entered++;
            }
            $ahead = min(1, $this->chanceOn($continuation, 'meteors') + $this->staticOn($continuation, $this->staticSwarms));
            $terms['meteors'] = ($entered + AIConfig::MOVE_NEXT_TURN_WEIGHT * $ahead) * $perStrike * $worth;
        }
        if (count($this->mines) > 0) $terms['mines'] = $this->mineCost($unit, $start, $path, $end) * $worth;

        $terms['total'] = $terms['board'] + $terms['drift'] + $terms['meteors'] + $terms['mines'];
        return $terms;
    }

    /* The fixed costs (stage T1b's MoveDecider::hazard): a blocked hex entered, ending off the map, and the straight
       continuation - into a blocked hex (a drifting asteroid by the chance it is there) or off the map. 0 = clear. */
    public function board(array $path, OffsetCoordinate $end, array $continuation)
    {
        $score = 0;
        foreach ($path as $hex) {
            if (isset($this->blocked[$hex->q . ',' . $hex->r])) $score += AIConfig::MOVE_COST_BLOCKED;
        }
        $over = self::hexesOffMap($end, $this->bounds);
        if ($over > 0) $score += AIConfig::MOVE_COST_OFF_MAP + $over * AIConfig::MOVE_COST_PER_HEX_OVER;

        $static = false;
        foreach ($continuation as $hex) {
            $key = $hex->q . ',' . $hex->r;
            if (isset($this->blocked[$key]) && !isset($this->driftingNow[$key])) $static = true;
        }
        $chance = $static ? 1 : min(1, $this->chanceOn($continuation, 'asteroids'));
        $score += AIConfig::MOVE_COST_NEXT_TURN * $chance;

        $last = count($continuation) > 0 ? $continuation[count($continuation) - 1] : $end;
        $score += self::hexesOffMap($last, $this->bounds) * AIConfig::MOVE_COST_PER_HEX_OVER;
        return $score;
    }

    //Next turn's straight continuation: the hexes $speed steps on along $heading.
    public static function continuation(OffsetCoordinate $end, $heading, $speed)
    {
        $hexes = array();
        $ahead = $end;
        for ($step = 0; $step < $speed; $step++) {
            $ahead = $ahead->moveToDirection($heading);
            $hexes[] = $ahead;
        }
        return $hexes;
    }

    // ------------------------------------------------------------------ drift

    /* A drifting field: where it stands, the hexes its drift at the start of next turn would newly cover (each
       direction 25%), and every hex it may stand in next turn with the chance of it. */
    private function addField($unit, $kind, OffsetCoordinate $hex, $heading, $facing, array $footprint)
    {
        $now = array();
        foreach ($footprint as $h) $now[$h->q . ',' . $h->r] = true;

        $possible = array();
        foreach (array_keys($now) as $key) $possible[$key] = AIConfig::HAZARD_DRIFT_CHANCE; //it stays put
        $overrun = array();
        foreach (array(-1, 0, 1) as $veer) { //TerrainDrift::resolve: 2 veers left, 3 straight on, 4 veers right - the heading becomes the direction moved
            $centre = $hex->moveToDirection(Mathlib::addToHexFacing($heading, $veer));
            foreach (RammingAttack::getTerrainHexesAt($unit, $centre, $facing) as $h) { //a drift never turns the field
                $key = $h->q . ',' . $h->r;
                $possible[$key] = (isset($possible[$key]) ? $possible[$key] : 0) + AIConfig::HAZARD_DRIFT_CHANCE;
                if (!isset($now[$key])) $overrun[$key] = (isset($overrun[$key]) ? $overrun[$key] : 0) + AIConfig::HAZARD_DRIFT_CHANCE;
            }
        }

        if ($kind === 'asteroids') {
            foreach (array_keys($now) as $key) $this->driftingNow[$key] = true;
        }
        $this->fields[] = array('kind' => $kind, 'unit' => $unit, 'hex' => $hex, 'overrun' => $overrun, 'possible' => $possible);
    }

    /* The damage expected from drifting fields moving onto $unit where it ends: per field the chance it drifts into
       that hex, times what it does to a unit there - an asteroid d10 x speed (an Ancient's speed counts 2 less), a
       swarm its meteors - on the side facing where it comes from. */
    private function overrun($unit, OffsetCoordinate $end, $speed)
    {
        $key = $end->q . ',' . $end->r;
        $damage = 0;
        foreach ($this->fields as $field) {
            if (!isset($field['overrun'][$key])) continue;
            if ($field['kind'] === 'asteroids') {
                $strike = 5.5 * max(0, $speed - ($unit->factionAge >= 3 ? 2 : 0));
            } else {
                $strike = self::meteorHits($unit, $speed) * (5.5 + $speed);
            }
            if ($strike <= 0) continue;
            $exposure = ($unit instanceof FighterFlight) ? 1.0 : $this->view->exposureAt($unit, $unit->getBearingOnPos(Mathlib::hexCoToPixel($field['hex'])));
            $damage += $field['overrun'][$key] * $strike * $exposure;
        }
        return $damage;
    }

    //The chance that a drifting field of $kind stands on one of $hexes next turn: per field, the chances of the hexes it may take there.
    private function chanceOn(array $hexes, $kind)
    {
        if (count($this->fields) == 0) return 0;
        $keys = array();
        foreach ($hexes as $hex) $keys[$hex->q . ',' . $hex->r] = true;

        $chance = 0;
        foreach ($this->fields as $field) {
            if ($field['kind'] !== $kind) continue;
            $p = 0;
            foreach ($field['possible'] as $key => $q) {
                if (isset($keys[$key])) $p += $q;
            }
            $chance += min(1, $p);
        }
        return $chance;
    }

    //1 when any of $hexes is in $set, else 0.
    private function staticOn(array $hexes, array $set)
    {
        foreach ($hexes as $hex) {
            if (isset($set[$hex->q . ',' . $hex->r])) return 1;
        }
        return 0;
    }

    /* The meteors a unit entering a swarm can expect: MeteorSwarm::rollMeteorChart's chart, averaged over the d20 with
       the same speed modifier (resolveMeteors: speed 4 or less -1, over 12 +1). ⚠️ A COPY of that chart - change one,
       change both. The Triad's spawned swarms roll their own chart; this one stands in for it. */
    const METEOR_CHART = array(
        12 => array(0, 0, 0, 0, 0),
        14 => array(0, 0, 0, 0, 1),
        15 => array(0, 0, 0, 1, 1),
        16 => array(0, 0, 1, 1, 1),
        17 => array(0, 1, 1, 1, 2),
        18 => array(0, 1, 1, 2, 2),
        19 => array(1, 1, 2, 2, 2),
        20 => array(1, 2, 2, 2, 3),
    );

    public static function meteorHits($unit, $speed)
    {
        if ($unit instanceof FighterFlight) $column = 0;
        else if ((int)$unit->shipSizeClass == 2) $column = 2;
        else if ((int)$unit->shipSizeClass == 3) $column = 3;
        else $column = ((int)$unit->shipSizeClass >= 4) ? 4 : 1;

        $modifier = 0;
        if ($speed <= 4) $modifier = -1;
        if ($speed > 12) $modifier = 1;

        $hits = 0;
        for ($die = 1; $die <= 20; $die++) {
            $roll = max(1, min(20, $die + $modifier));
            foreach (self::METEOR_CHART as $threshold => $row) {
                if ($roll <= $threshold) {
                    $hits += $row[$column];
                    break;
                }
            }
        }
        return $hits / 20;
    }

    // ------------------------------------------------------------------ mines

    /* A mine this viewer can see, if it would attack the bot's units: an enemy's, or its own side's without IFF. What
       it reads per kind of unit (AoE.php): a Proximity mine its range and the kinds it was set to attack, a Captor mine
       the ranges set per kind (its full range where none was set), a DEW mine its controller's ranges and its guns. */
    private function addMine($mine)
    {
        $view = $this->view;
        if (!$view->onBoard($mine)) return; //not detected: not on this viewer's board
        if ($mine->team == $view->team && $mine->getIFFSystem()) return;

        $hex = null;
        foreach ($mine->movement as $move) {
            if ($move->type === 'deploy') {
                $hex = Movement::toOffset($move->position);
                break;
            }
        }
        if (!$hex) return;

        $entry = array('unit' => $mine, 'hex' => $hex, 'ranges' => array(), 'damage' => 0.0, 'hit' => AIConfig::HAZARD_MINE_HIT, 'lasting' => false);
        $guns = 0.0;
        foreach ($mine->systems as $system) {
            if ($system->isDestroyed()) continue;
            if ($system instanceof ProximityMine) {
                if ($system->isOfflineOnTurn($view->turn)) continue;
                foreach (array('Fighters' => 0, 'LCVs-MCVs' => 1, 'Capitals-HCVs' => 2) as $type => $index) {
                    if (!isset($system->allocatedShipTypes[$type]) || $system->allocatedShipTypes[$type]) $entry['ranges'][$index] = (int)$system->range;
                }
                $entry['damage'] = ($system->minDamage + $system->maxDamage) / 2;
                $entry['hit'] = 1.0; //it always hits
            } else if ($system instanceof CaptorMine) {
                if ($system->isOfflineOnTurn($view->turn)) continue;
                foreach (array('Fighters' => 0, 'LCVs-MCVs' => 1, 'Capitals-HCVs' => 2) as $type => $index) {
                    $set = isset($system->allocatedRanges[$type]) ? $system->allocatedRanges[$type] : null;
                    $entry['ranges'][$index] = ($set !== null && !is_array($set)) ? (int)$set : (int)$system->range;
                }
                $entry['damage'] = ($system->minDamage + $system->maxDamage) / 2;
            } else if ($system instanceof MineControllerDEW) {
                foreach (array('Fighters' => 0, 'LCVs-MCVs' => 1, 'Capitals-HCVs' => 2) as $type => $index) {
                    $set = isset($system->allocatedRanges[$type]) ? $system->allocatedRanges[$type] : null;
                    $entry['ranges'][$index] = ($set !== null && !is_array($set)) ? min((int)$set, (int)$system->rangeSetting) : (int)$system->rangeSetting;
                }
                $entry['lasting'] = true;
            } else if ($system instanceof Weapon && !$system->isRammingAttack) {
                $guns += ($system->minDamage + $system->maxDamage) / 2 * max(1, (int)$system->guns);
            }
        }
        if ($entry['lasting']) $entry['damage'] = $guns;
        if ($entry['damage'] <= 0 || count($entry['ranges']) == 0) return;
        $this->mines[] = $entry;
    }

    //How far $mine reaches for $unit: its range for that kind of unit, halved (down) against a Jammer. Null: it never attacks one.
    private function mineRange(array $mine, $unit)
    {
        $index = ($unit instanceof FighterFlight) ? 0 : (int)$unit->getFireControlIndex();
        if (!isset($mine['ranges'][$index])) return null;
        $range = $mine['ranges'][$index];
        try {
            if ($unit->getSpecialAbilityValue('Jammer', array('shooter' => $mine['unit'], 'target' => $unit)) > 0) $range = (int)floor($range / 2);
        } catch (Throwable $e) {
        }
        return $range;
    }

    private function inMineRange(array $mine, OffsetCoordinate $hex, $range)
    {
        if ($mine['hex']->distanceTo($hex) > $range) return false;
        return count($this->blockedList) == 0 || !Mathlib::isLoSBlocked($mine['hex'], $hex, $this->blockedList);
    }

    /* The damage expected from the mines this plan brings $unit within range of. A mine the unit STARTS within range of
       attacks whatever it does - unless it is one that keeps firing, which a plan that ends out of its range leaves behind
       next turn. */
    private function mineCost($unit, OffsetCoordinate $start, array $path, OffsetCoordinate $end)
    {
        $damage = 0;
        foreach ($this->mines as $mine) {
            $range = $this->mineRange($mine, $unit);
            if ($range === null) continue;
            $attack = $mine['damage'] * $mine['hit'];
            $startsIn = $this->inMineRange($mine, $start, $range);
            if (!$startsIn) {
                foreach ($path as $hex) {
                    if ($this->inMineRange($mine, $hex, $range)) {
                        $damage += $attack;
                        break;
                    }
                }
            }
            if ($mine['lasting'] && $this->inMineRange($mine, $end, $range)) $damage += AIConfig::MOVE_NEXT_TURN_WEIGHT * $attack;
        }
        return $damage;
    }

    // ------------------------------------------------------------------ the map

    //Terrain and Enormous units, as the game itself lists them (TacGamedata::setBlockedHexes).
    public static function blockedKeys(TacGamedata $gamedata)
    {
        $keys = array();
        foreach ($gamedata->blockedHexes ?: array() as $hex) {
            $offset = new OffsetCoordinate($hex);
            $keys[$offset->q . ',' . $offset->r] = true;
        }
        return $keys;
    }

    //Half the map's width and height in hexes (the bounds terrain is laid out within), or null for an unlimited map.
    public static function mapBounds(TacGamedata $gamedata)
    {
        $width = 0;
        $height = 0;
        if (sscanf((string)$gamedata->gamespace, '%dx%d', $width, $height) !== 2 || $width <= 0 || $height <= 0) return null;
        return array(intdiv($width, 2), intdiv($height, 2));
    }

    //How far past the map's edge a hex lies (0 = on the map).
    public static function hexesOffMap(OffsetCoordinate $hex, $bounds)
    {
        if ($bounds === null) return 0;
        return max(0, abs($hex->q) - $bounds[0]) + max(0, abs($hex->r) - $bounds[1]);
    }

    //For the decision line: what is on the board that the bot steers around.
    public function describe()
    {
        $parts = array();
        if (count($this->fields) > 0) $parts[] = count($this->fields) . ' drifting fields';
        if (count($this->swarmsNow) > 0) $parts[] = count($this->swarmsNow) . ' meteor hexes';
        if (count($this->mines) > 0) $parts[] = count($this->mines) . ' mines in sight';
        return implode(', ', $parts);
    }
}
