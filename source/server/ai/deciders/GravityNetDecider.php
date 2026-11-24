<?php
/**
 * CPU opponent - stage T9, Missing system M6: the GRAVITY NET, fired in Pre-Firing (TACTICAL_AI_PLAN.md 10.10, "M6 and M7 as built").
 *
 * The Minbari Rogata (2 nets), Shargoti (2) and Troligan (6) carry them. A net hits a unit no bigger than its ship (GravityNet::
 * beforeDamage) and moves it, before the Fire phase, to a hex its player picked within the net's Move Distance (a d6 the server rolls for
 * each net every Movement phase): speed, heading and facing kept. Only ONE net moves a unit a turn - of those that hit it, the one with
 * the largest Move Distance (S mode; GravityNetHandler), the first of equals in the game's unit order.
 *
 * The user's rulings (2026-11-23, plan M6): "Use OEW to judge this, e.g. bring an enemy ship into heavy weapon arcs of friendly ships that
 * have a lock, or move enemy ship so it no longer has heavy weapon arcs on ships it has OEW lock on"; and (2026-11-24) "get an angle on a
 * damage side of an enemy", or "if an enemy has OEW lock and lots of light weapons with high range penalty ... move them as far away as
 * possible to reduce their hit chances". All four are ONE measure here, the "best trade" ShipDecider moves by (the T5a ruling): the damage
 * the bot's units should deal this Fire phase, less the level's shipDanger weight times what the moved unit should deal them - each gun
 * priced by the engine's own hit formula (AIView::estimate), so a lock, the range penalty and the arcs are in it, and each target's
 * health counted from the side the shots arrive on (healthLeft - a damaged or destroyed section facing the guns makes each point worth
 * more). For each enemy a net can hit, every hex it could be put in is priced against where it stands; each net then goes, greedily, to
 * the unit and hex that add most - times its chance, and for a second net at the same unit only what it adds over the first - while that
 * is at least AIConfig::GRAVNET_MIN_GAIN.
 *
 * HARD also nets FRIENDLY units - its own and its teammates' (the user, 2026-11-24: "a HARD CPU player will consider whether moving allies
 * is advantageous and attempt to do so. Easy and Normal can skip this"; the skill 'netAllies'): a friend priced by the same trade - its own
 * fire, and the enemy's fire at the whole friendly side, with it put in the hex - out of a locked enemy's arcs, into its own guns' reach.
 * Every net needs a chance of at least AIConfig::GRAVNET_MIN_CHANCE (15 - the user, same day).
 *
 * Legality is the page's (AIView::netShot / netHex, judged by fireOracle.js replayNets). The mode is always S (P only matters against
 * another side's net on the same unit). Not priced: next turn (the unit keeps its speed and heading from where it is put), the bot's own
 * missiles already in flight at the unit; no OEW is put on a friend to make netting it likelier (Initial Orders knows nothing of nets).
 *
 * The decision is made on the bot's own MASKED view of the game (principle P2); in Pre-Firing every unit has moved.
 */
class GravityNetDecider
{
    //One line about the last plan, for tac_ai_log (null when no net fired).
    public static $lastSummary = null;
    //What the last plan() priced: units, hexes, whether the budget cut it short, and the CPU seconds it took.
    public static $last = array('targets' => 0, 'hexes' => 0, 'cut' => false, 'seconds' => 0.0);
    //CPU seconds the pricing may take (AIConfig::GRAVNET_DECISION_SECONDS); 0 = no limit - a harness sets it, for determinism.
    public static $budget = AIConfig::GRAVNET_DECISION_SECONDS;

    //The bot's Pre-Firing entries, one per unit it owns. $seat is its DBManager::getCpuSeats row.
    public static function orders(TacGamedata $gamedata, $botId, array $seat)
    {
        $gamedata->prepareForPlayer(); //the mask a human in this seat plays behind; also fills what the page reads
        $view = new AIView($gamedata, $botId, AIConfig::seatLevel($seat), AIConfig::seatSeed($seat));
        $plan = self::plan($view);
        self::$lastSummary = (count($plan) > 0) ? self::describe($plan) : null;
        return self::entries($view, $plan);
    }

    /* The plan: a list of array('unit', 'net', 'target', 'hex' (OffsetCoordinate), 'chance' (percent), 'gain' (the trade the move buys,
       if it hits), 'added' (what the net adds to the plan, its chance and the nets before it counted)) - one row a net that fires. */
    public static function plan(AIView $view)
    {
        self::$last = array('targets' => 0, 'hexes' => 0, 'cut' => false, 'seconds' => 0.0);
        if (!$view->skill('gravityNets')) return array();
        $nets = self::nets($view);
        if (count($nets) == 0) return array();

        $started = AIConfig::cpuSeconds();
        $deadline = (self::$budget > 0) ? $started + self::$budget : null;
        $allies = (bool)$view->skill('netAllies');
        $board = self::board($view, $allies);
        $bounds = Hazards::mapBounds($view->gamedata);

        //each net's best hex at each unit it can hit: net index => unit id => array('target', 'hex', 'gain', 'chance')
        $options = array();
        foreach (self::targets($view, $nets, $allies) as $target) {
            $shooting = array();
            foreach ($nets as $n => $net) {
                if (!self::moves($net['unit'], $target)) continue; //the server would not move it (beforeDamage): nothing to buy
                $shot = $view->netShot($net['unit'], $net['net'], $target, $allies);
                if ($shot === null || $shot['chance'] < AIConfig::GRAVNET_MIN_CHANCE) continue; //(the user, 2026-11-24: 15% at least)
                /* From exactly between two of the unit's sections the page prices the shot on one profile, up to 15 off the engine's
                   mean (AIView::onEdge): a low one there may be a click the page would refuse - as FireDecider, not under FIRE_MIN_CHANCE_EDGE. */
                if ($view->onEdge($net['unit'], $target) && $shot['chance'] < AIConfig::FIRE_MIN_CHANCE_EDGE) continue;
                $shooting[$n] = (int)$shot['chance'];
            }
            if (count($shooting) == 0) continue;
            self::$last['targets']++;

            $reach = 0;
            foreach (array_keys($shooting) as $n) $reach = max($reach, $nets[$n]['reach']);
            $here = $target->getHexPos();
            $base = null;
            foreach (self::within($here, $reach) as $hex) {
                if (Hazards::hexesOffMap($hex, $bounds) > 0) continue; //the page would allow it; the bot keeps to the board
                $legalFor = array();
                foreach (array_keys($shooting) as $n) {
                    if ($view->netHex($nets[$n]['unit'], $nets[$n]['net'], $target, $hex)) $legalFor[] = $n;
                }
                if (count($legalFor) == 0) continue;
                if ($deadline !== null && AIConfig::cpuSeconds() > $deadline) {
                    self::$last['cut'] = true;
                    break 2;
                }
                if ($base === null) $base = self::trade($view, $board, $target);
                $gain = self::tradeAt($view, $board, $target, $hex) - $base;
                self::$last['hexes']++;
                foreach ($legalFor as $n) {
                    $id = (int)$target->id;
                    if (!isset($options[$n][$id]) || $gain > $options[$n][$id]['gain']) {
                        $options[$n][$id] = array('target' => $target, 'hex' => $hex, 'gain' => $gain, 'chance' => $shooting[$n]);
                    }
                }
            }
        }

        //greedy: the net and unit that add most, until nothing adds GRAVNET_MIN_GAIN
        $chosen = array(); //unit id => the rows aimed at it
        $plan = array();
        $used = array();
        while (true) {
            $best = null;
            foreach ($options as $n => $byTarget) {
                if (isset($used[$n])) continue;
                foreach ($byTarget as $id => $option) {
                    if ($option['gain'] <= 0) continue;
                    $now = isset($chosen[$id]) ? $chosen[$id] : array();
                    $row = array('n' => $n, 'rank' => $nets[$n]['rank'], 'p' => $option['chance'] / 100, 'gain' => $option['gain']);
                    $added = self::expected(array_merge($now, array($row))) - self::expected($now);
                    if ($best === null || $added > $best['added']) $best = array('n' => $n, 'id' => $id, 'row' => $row, 'option' => $option, 'added' => $added);
                }
            }
            if ($best === null || $best['added'] < AIConfig::GRAVNET_MIN_GAIN) break;
            $used[$best['n']] = true;
            $chosen[$best['id']][] = $best['row'];
            $net = $nets[$best['n']];
            $plan[] = array('unit' => $net['unit'], 'net' => $net['net'], 'target' => $best['option']['target'], 'hex' => $best['option']['hex'],
                'chance' => $best['option']['chance'], 'gain' => $best['option']['gain'], 'added' => $best['added']);
        }
        self::$last['seconds'] = AIConfig::cpuSeconds() - $started;
        return $plan;
    }

    /* What a set of nets at one unit should buy: the server moves it by the hitting net with the largest Move Distance (the first of
       equals in its order - 'rank'), so the i-th by rank counts when it hits and every one before it misses. */
    private static function expected(array $rows)
    {
        usort($rows, function ($a, $b) { return $a['rank'] <=> $b['rank']; });
        $missed = 1.0;
        $total = 0.0;
        foreach ($rows as $row) {
            $total += $missed * $row['p'] * $row['gain'];
            $missed *= 1 - $row['p'];
        }
        return $total;
    }

    /* The bot's nets that can fire this Pre-Firing phase: array('unit', 'net', 'reach' (its Move Distance), 'rank'). 'rank' is the order
       the server prefers them in at one unit (GravityNetHandler::getLargestMovement - the largest Move Distance, then the first in the
       game's unit order and the ship's systems: the order netsOf walks). */
    private static function nets(AIView $view)
    {
        $nets = array();
        $place = 0;
        foreach ($view->gamedata->ships as $unit) {
            foreach ($unit->systems as $system) {
                if (!($system instanceof GravityNet)) continue;
                $place++;
                if ((int)$unit->userid !== $view->botId || !$view->onBoard($unit) || $unit instanceof FighterFlight) continue;
                $reach = $view->netReach($system);
                if ($reach < 1) continue;
                $nets[] = array('unit' => $unit, 'net' => $system, 'reach' => $reach, 'rank' => array(-$reach, $place));
            }
        }
        usort($nets, function ($a, $b) { return $a['rank'] <=> $b['rank']; });
        return $nets;
    }

    /* The units a net might move: the enemies - and with $allies (Hard's 'netAllies') the friendly ones (friends()) - not mines, not
       Enormous; nearest to the nets first (a budget cut leaves the farthest unpriced). */
    private static function targets(AIView $view, array $nets, $allies = false)
    {
        $list = array();
        foreach (array_merge($view->enemies(), $allies ? self::friends($view) : array()) as $unit) {
            if (!empty($unit->mine) || (!($unit instanceof FighterFlight) && (int)$unit->shipSizeClass > 3)) continue;
            $near = PHP_INT_MAX;
            foreach ($nets as $net) $near = min($near, $net['unit']->getHexPos()->distanceTo($unit->getHexPos()));
            $list[] = array($near, (int)$unit->id, $unit);
        }
        usort($list, function ($a, $b) { return ($a[0] !== $b[0]) ? $a[0] - $b[0] : $a[1] - $b[1]; });
        return array_map(function ($row) { return $row[2]; }, $list);
    }

    /* The friendly units on the board, as AIView::enemies() takes the hostile ones: the bot's own (myUnits) and its teammates' - units a
       player may click (no Huge terrain, isTargetableBy). */
    private static function friends(AIView $view)
    {
        $units = $view->myUnits();
        foreach ($view->gamedata->ships as $unit) {
            if ($view->team === null || $unit->team != $view->team || (int)$unit->userid === $view->botId) continue;
            if (!$view->onBoard($unit)) continue;
            $units[] = $unit;
        }
        return array_values(array_filter($units, function ($unit) { return !($unit->Huge > 0) && $unit->isTargetableBy(); }));
    }

    //GravityNet::beforeDamage's size rule: the net's ship Enormous, or the unit no bigger than it (a flight always).
    private static function moves($shooter, $target)
    {
        if (!empty($shooter->Enormous)) return true;
        if (!empty($target->Enormous)) return false;
        return (int)$shooter->shipSizeClass >= (int)$target->shipSizeClass;
    }

    //Every hex within $radius of $centre, nearest first (the order the budget cuts in), the centre itself left out.
    private static function within(OffsetCoordinate $centre, $radius)
    {
        $hexes = array();
        for ($q = $centre->q - $radius; $q <= $centre->q + $radius; $q++) {
            for ($r = $centre->r - $radius; $r <= $centre->r + $radius; $r++) {
                $hex = new OffsetCoordinate($q, $r);
                $d = $centre->distanceTo($hex);
                if ($d >= 1 && $d <= $radius) $hexes[] = array($d, $q, $r, $hex);
            }
        }
        usort($hexes, function ($a, $b) { return ($a[0] !== $b[0]) ? $a[0] - $b[0] : (($a[1] !== $b[1]) ? $a[1] - $b[1] : $a[2] - $b[2]); });
        return array_map(function ($row) { return $row[3]; }, $hexes);
    }

    /* The board, priced once: the bot's units and their guns that fire this Fire phase (AIView::weapons - the bot's own rules) - with
       $allies (Hard) its teammates' units too, the whole friendly side - the enemies with their guns loaded now (as ShipDecider counts an
       enemy that has moved: from its arcs), and every friendly gun at every enemy where all stand now ('damage': own index => gun index =>
       enemy index => expected damage; a line of fire blocked = 0). With $allies also every enemy gun at every friendly unit ('incoming':
       enemy index => gun index => own index) and each friendly unit's health from each enemy's side ('health': enemy index => own index) -
       what netting a friend changes. */
    private static function board(AIView $view, $allies = false)
    {
        $own = array();
        foreach ($allies ? self::friends($view) : $view->myUnits() as $unit) {
            $guns = array();
            foreach ($view->weapons($unit) as $mount) {
                $weapon = $mount['weapon'];
                if ($weapon->ballistic || !$view->plainGun($weapon, $mount['fighter'])) continue;
                self::addKind($guns, $weapon);
            }
            $own[] = array('unit' => $unit, 'guns' => array_values($guns), 'value' => max(1, $view->value($unit)));
        }

        $enemies = array();
        foreach ($view->enemies() as $enemy) {
            $guns = array();
            if (empty($enemy->mine)) { //a mine fires by its own rules (Hazards prices them), as in ShipDecider
                foreach ($view->mounts($enemy) as $mount) {
                    $weapon = $mount['weapon'];
                    if (!$view->plainGun($weapon, $mount['fighter']) || !$view->loaded($weapon) || $weapon->isOfflineOnTurn($view->turn)) continue;
                    self::addKind($guns, $weapon);
                }
            }
            $health = $view->healthLeft($enemy);
            $enemies[] = array('unit' => $enemy, 'id' => (int)$enemy->id, 'guns' => array_values($guns), 'health' => $health, 'worth' => $view->value($enemy) / $health);
        }

        $damage = array();
        foreach ($own as $o => $mine) {
            foreach ($mine['guns'] as $g => $gun) {
                foreach ($enemies as $e => $enemy) $damage[$o][$g][$e] = self::fire($view, $mine['unit'], $gun, $enemy['unit']);
            }
        }
        $incoming = array();
        $health = array();
        if ($allies) {
            foreach ($enemies as $e => $enemy) {
                foreach ($own as $o => $mine) {
                    $health[$e][$o] = $view->healthLeft($mine['unit'], $enemy['unit']);
                    foreach ($enemy['guns'] as $g => $gun) $incoming[$e][$g][$o] = self::fire($view, $enemy['unit'], $gun, $mine['unit']);
                }
            }
        }
        return array('own' => $own, 'enemies' => $enemies, 'damage' => $damage, 'incoming' => $incoming, 'health' => $health);
    }

    //A gun of a kind (class, mode, arc, criticals) - counted, as ShipDecider counts them: alike guns are priced once.
    private static function addKind(array &$guns, Weapon $weapon)
    {
        $key = get_class($weapon) . '|' . $weapon->firingMode . '|' . AIView::arcKey($weapon) . '|' . count($weapon->criticals);
        if (!isset($guns[$key])) $guns[$key] = array('weapon' => $weapon, 'count' => 0);
        $guns[$key]['count']++;
    }

    //What guns of a kind should do to $target where both stand now: the arc, the line of fire, the engine's hit formula (estimate).
    private static function fire(AIView $view, $shooter, array $gun, $target)
    {
        if (!$gun['weapon']->ignoresLoS && !$view->clearLine($shooter, $target)) return 0.0;
        return (float)$view->estimate($shooter, $gun['weapon'], $target, true) * $gun['count'];
    }

    //The trade with $target put in $hex, as the server's move leaves it (GravityNet::doGravityNetMove: speed, heading and facing kept).
    private static function tradeAt(AIView $view, array $board, $target, OffsetCoordinate $hex)
    {
        $last = $target->getLastMovement();
        $target->movement[] = new MovementOrder(-1, 'prefire', $hex, 0, 0, (int)$last->speed, (int)$last->heading, (int)$last->facing, false, $view->turn, 0, 0);
        try {
            return self::trade($view, $board, $target);
        } finally {
            array_pop($target->movement);
        }
    }

    /* The part of this Fire phase's trade that $target's place changes, as it stands: every one of the bot's units' fire - its guns
       shared out over the enemies, none counted past what finishes a unit (ShipDecider::removed), each enemy's health and worth from the
       unit's side (targetsFrom) - less the level's shipDanger times $target's own fire at the bot's units, shared out the same way, each
       unit's health and worth from $target's side. The other enemies' fire at the bot does not change with it, and is left out. */
    private static function trade(AIView $view, array $board, $target)
    {
        $id = (int)$target->id;
        $at = null;
        foreach ($board['enemies'] as $e => $enemy) if ($enemy['id'] === $id) $at = $e;
        if ($at === null) {
            foreach ($board['own'] as $o => $mine) if ((int)$mine['unit']->id === $id) return self::friendTrade($view, $board, $o, $target);
            return 0.0;
        }

        $offence = 0.0;
        foreach ($board['own'] as $o => $mine) {
            if (count($mine['guns']) == 0) continue;
            $damage = array();
            foreach ($mine['guns'] as $g => $gun) {
                foreach ($board['enemies'] as $e => $enemy) {
                    $d = ($e === $at) ? self::fire($view, $mine['unit'], $gun, $target) : $board['damage'][$o][$g][$e];
                    if ($d > 0) $damage[$g][$e] = $d;
                }
            }
            if (count($damage) > 0) $offence += ShipDecider::removed($damage, ShipDecider::targetsFrom($view, $mine['unit'], $board['enemies']));
        }

        $danger = 0.0;
        $guns = $board['enemies'][$at]['guns'];
        if (count($guns) > 0) {
            $damage = array();
            $targets = array();
            foreach ($board['own'] as $o => $mine) {
                $health = $view->healthLeft($mine['unit'], $target);
                $targets[$o] = array('unit' => $mine['unit'], 'health' => $health, 'worth' => $mine['value'] / $health);
                foreach ($guns as $g => $gun) {
                    $d = self::fire($view, $target, $gun, $mine['unit']);
                    if ($d > 0) $damage[$g][$o] = $d;
                }
            }
            if (count($damage) > 0) $danger = ShipDecider::removed($damage, $targets);
        }

        return $offence - (float)$view->skill('shipDanger') * $danger;
    }

    /* trade() for a FRIENDLY unit (Hard's 'netAllies'), as it stands - board['own'][$f]: its own fire at the enemies (shared out as above,
       each enemy's health from its side), less shipDanger times EVERY enemy's fire at the friendly side, each enemy's guns shared out over
       the friendly units with this one where it now stands (each unit's health from that enemy's side). The other friends' fire does not
       change with it, and is left out. */
    private static function friendTrade(AIView $view, array $board, $f, $unit)
    {
        $offence = 0.0;
        $guns = $board['own'][$f]['guns'];
        if (count($guns) > 0) {
            $damage = array();
            foreach ($guns as $g => $gun) {
                foreach ($board['enemies'] as $e => $enemy) {
                    $d = self::fire($view, $unit, $gun, $enemy['unit']);
                    if ($d > 0) $damage[$g][$e] = $d;
                }
            }
            if (count($damage) > 0) $offence = ShipDecider::removed($damage, ShipDecider::targetsFrom($view, $unit, $board['enemies']));
        }

        $danger = 0.0;
        foreach ($board['enemies'] as $e => $enemy) {
            if (count($enemy['guns']) == 0) continue;
            $damage = array();
            $targets = array();
            foreach ($board['own'] as $o => $mine) {
                $health = ($o === $f) ? $view->healthLeft($unit, $enemy['unit']) : $board['health'][$e][$o];
                $targets[$o] = array('unit' => $mine['unit'], 'health' => $health, 'worth' => $mine['value'] / $health);
                foreach ($enemy['guns'] as $g => $gun) {
                    $d = ($o === $f) ? self::fire($view, $enemy['unit'], $gun, $unit) : $board['incoming'][$e][$g][$o];
                    if ($d > 0) $damage[$g][$o] = $d;
                }
            }
            if (count($damage) > 0) $danger += ShipDecider::removed($damage, $targets);
        }

        return $offence - (float)$view->skill('shipDanger') * $danger;
    }

    //A plan as the entries AIOrderBuilder describes: every unit of the bot, the nets that fire with their two orders each.
    public static function entries(AIView $view, array $plan)
    {
        $systems = self::systems($plan, $view->turn);
        $entries = array();
        foreach (AIFallback::ownUnits($view->gamedata, $view->botId) as $unit) {
            $entry = AIOrderBuilder::entry($unit);
            if (isset($systems[(int)$unit->id])) {
                foreach ($systems[(int)$unit->id] as $systemId => $system) $entry['systems'][$systemId] = $system;
            }
            $entries[] = $entry;
        }
        return $entries;
    }

    /* Unit id => net id => its system row: the two orders the page's two clicks make - GravityNet.doMultipleFireOrders (at the unit, one
       shot, 'gravitic', the note 'Split') and doMultipleHexFireOrders (at no unit, the hex in x / y, the net's shots, 'gravNetMoveHex', the
       note 'split'), both 'prefiring', in the net's S mode (1). */
    public static function systems(array $plan, $turn)
    {
        $out = array();
        foreach ($plan as $row) {
            $unitId = (int)$row['unit']->id;
            $netId = (int)$row['net']->id;
            $first = AIOrderBuilder::fire($unitId, (int)$row['target']->id, $netId, $turn, 1, 1, 'gravitic', 'prefiring', 'Split');
            $second = AIOrderBuilder::fire($unitId, -1, $netId, $turn, 1, (int)$row['net']->defaultShots, 'gravNetMoveHex', 'prefiring', 'split');
            $second['x'] = (int)$row['hex']->q;
            $second['y'] = (int)$row['hex']->r;
            $out[$unitId][$netId] = array('id' => $netId, 'fireOrders' => array($first, $second));
        }
        return $out;
    }

    private static function describe(array $plan)
    {
        $parts = array();
        foreach ($plan as $row) {
            $parts[] = $row['unit']->name . ' net #' . $row['net']->id . ' (move ' . (int)$row['net']->moveDistance . ') at ' . $row['target']->name
                . ' -> ' . $row['hex']->q . ',' . $row['hex']->r . ' (' . $row['chance'] . '%, trade +' . round($row['gain']) . ')';
        }
        return 'Gravity Nets: ' . implode('; ', $parts) . ' (' . self::$last['targets'] . ' units, ' . self::$last['hexes'] . ' hexes priced, '
            . number_format(self::$last['seconds'], 2) . ' s' . (self::$last['cut'] ? ', cut by the budget' : '') . ')';
    }
}
