<?php
/**
 * CPU opponent - Firing (TACTICAL_AI_PLAN.md section 5.6, stage T2).
 *
 * Every loaded direct-fire weapon the bot understands is offered every enemy it can legally hit
 * (AIView - the engagement table), and the shots are handed out GREEDILY: the best shot on the
 * board is taken, the damage it should do is booked against its target, and the rest are judged
 * again. What makes a shot good, in named terms (principle P7):
 *
 *   worth   the share of the target this volley should remove, times what the target is worth -
 *           so a cheap kill on a dear ship beats the same damage soaked by a fresh one;
 *   focus   a target already booked for damage draws the next gun (AIConfig::FIRE_FOCUS), until
 *           the plan would kill it FIRE_OVERKILL times over - then further fire counts for nothing
 *           there and the remaining guns look elsewhere. A HEAVY gun is drawn far harder
 *           (FIRE_FOCUS_HEAVY; user, 2026-11-19: concentrate the fleet's heavy firepower to take a
 *           ship down rather than spread it), light guns stay free for fighters and the rest;
 *   threat  the more an enemy can do to this fleet, the sooner it is shot (FIRE_THREAT_WEIGHT).
 *
 * SLOW WEAPONS (the user's feedback, 2026-11-15). A shot costs a weapon its reload, so a slow one
 * fires only at FIRE_MIN_CHANCE_BY_LOAD or better (minimumChance) - a slow PULSE weapon
 * FIRE_PULSE_PREMIUM higher still (2026-11-19: its damage grows much faster than its chance) - and
 * waits when a turn closer is clearly better (holdSlow: the real formula again where both units
 * will be, range penalty and plasma range damage included). A target drawing away lowers the floor
 * to FIRE_MIN_CHANCE_PARTING - by loading time too since stage T6a (game 4774: never under 34% for a
 * 3- or 4-turn gun).
 *
 * THE ENEMY'S INTERCEPTION (InterceptModel). A shot is worth what it adds to its target's volley
 * once the target's interceptors have been given out as the server gives them - so the bot neither
 * pretends there is none nor assumes every shot is intercepted, and a slow gun's floor holds on the
 * chance it keeps after interception.
 *
 * THE INTERCEPTION RESERVE - its own. A gun with an intercept rating that is given no order is used
 * by the server to intercept (Firing::automateIntercept), so holding one back is simply not ordering it.
 * Since stage T6a (the user's notes on game 4774, holdBack): a gun with an offensive shot of
 * FIRE_INTERCEPTOR_MIN_CHANCE (15%) or better always fires; one under it stays on defence only while
 * interceptable enemy fire can reach its unit (a laser cannot be intercepted) - one for each such order
 * it could stop from its arc, the unit's interceptors with no offensive shot at all covering first.
 *
 * Ballistics are BallisticDecider's (Initial Orders). NOT YET: called shots, and every weapon
 * AIView::weaponBlock turns away.
 *
 * The decision is made on the bot's own MASKED view of the game (principle P2).
 */
class FireDecider
{
    //One line about the last plan made, for tac_ai_log.
    public static $lastSummary = '';

    //The bot's Firing entries, one per unit it owns. $seat is its DBManager::getCpuSeats row.
    public static function orders(TacGamedata $gamedata, $botId, array $seat)
    {
        $gamedata->prepareForPlayer(); //the mask a human in this seat plays behind; also fills what the page reads

        $view = new AIView($gamedata, $botId, AIConfig::seatLevel($seat), AIConfig::seatSeed($seat));
        $entries = self::entries($view, self::plan($view));

        /* Stage T9, M2: the flights that started the battle in a hangar (the Orieni Hunter-Killers) are launched, as far as the bays'
           launch rates allow (HangarDecider) - a Fire-phase order, resolved at the end of the turn. */
        $launches = HangarDecider::launches($view);
        if (count($launches) > 0) {
            foreach ($entries as $index => $entry) {
                if (!isset($launches[(int)$entry['id']])) continue;
                foreach ($launches[(int)$entry['id']] as $hangarId => $system) $entries[$index]['systems'][$hangarId] = $system;
            }
        }
        $line = HangarDecider::describeLaunches();
        if ($line !== '') self::$lastSummary .= '; ' . $line;
        return $entries;
    }

    //A plan as the entries AIOrderBuilder describes: every unit of the bot, with its fire orders.
    public static function entries(AIView $view, array $plan)
    {
        $gamedata = $view->gamedata;
        $entries = array();
        foreach (AIFallback::ownUnits($gamedata, $view->botId) as $unit) {
            $entry = AIOrderBuilder::entry($unit);

            foreach ($plan as $shot) {
                if ($shot['unit']->id !== $unit->id) continue;

                $rows = array();
                for ($gun = 0; $gun < $shot['orders']; $gun++) {
                    $rows[] = AIOrderBuilder::fire($unit->id, $shot['target']->id, $shot['weapon']->id, $gamedata->turn, $shot['mode'], $shot['shots'], $shot['damageclass'],
                        'normal', !empty($shot['split']) ? 'Split' : null); //(stage T9) a split order carries the page's note
                }

                //(stage T9) a weapon Hard splits has a row per target: its orders go in together, after one another
                if ($shot['fighter'] !== null) {
                    $fighterId = $shot['fighter']->id;
                    if (!isset($entry['systems'][$fighterId])) $entry['systems'][$fighterId] = array('id' => $fighterId, 'systems' => array());
                    $at = null;
                    foreach ($entry['systems'][$fighterId]['systems'] as $i => $sub) {
                        if ($sub['id'] === $shot['weapon']->id) $at = $i;
                    }
                    if ($at === null) $entry['systems'][$fighterId]['systems'][] = array('id' => $shot['weapon']->id, 'fireOrders' => $rows);
                    else $entry['systems'][$fighterId]['systems'][$at]['fireOrders'] = array_merge($entry['systems'][$fighterId]['systems'][$at]['fireOrders'], $rows);
                } else if (isset($entry['systems'][$shot['weapon']->id])) {
                    $entry['systems'][$shot['weapon']->id]['fireOrders'] = array_merge($entry['systems'][$shot['weapon']->id]['fireOrders'], $rows);
                } else {
                    $entry['systems'][$shot['weapon']->id] = array('id' => $shot['weapon']->id, 'fireOrders' => $rows);
                }
            }

            $entries[] = $entry;
        }

        return $entries;
    }

    //Set while plan() prices Hard's plan WITHOUT its split (stage T9 - see the end of plan()).
    private static $wholeOnly = false;

    /* The plan: a list of array('unit', 'weapon', 'fighter', 'target', 'mode', 'chance', 'orders',
       'shots', 'damageclass', 'damage', 'score'). One row per weapon that fires - or, for a weapon Hard splits (stage T9), one per
       target it sends guns to, each marked 'split'. */
    public static function plan(AIView $view)
    {
        $enemies = $view->enemies();
        $plan = array();
        self::$lastSummary = 'no target';
        if (count($enemies) == 0) return $plan;

        $maxThreat = 0;
        $state = array(); //per target: what a kill takes, what the target is worth, what is booked
        foreach ($enemies as $enemy) {
            $state[$enemy->id] = array('health' => $view->healthLeft($enemy), 'value' => $view->value($enemy), 'threat' => $view->threat($enemy), 'booked' => 0);
            $maxThreat = max($maxThreat, $state[$enemy->id]['threat']);
        }

        //Every gun's legal shots, best mode against each target.
        $guns = array();
        $held = 0;
        $lastStand = Posture::lastStand($view); //stage T6g: a flight that must fire this turn keeps no gun back to intercept
        foreach ($view->myUnits() as $unit) {
            $unitGuns = array();
            $mounts = $view->weapons($unit);
            foreach ($mounts as $mount) {
                $weapon = $mount['weapon'];
                $options = array();
                $minimum = self::minimumChance($weapon, true, $view->skill('pulsePremium')); //the least it is ever fired at; holdSlow() asks for more
                $modes = $view->modes($weapon);
                $shipsOnly = self::shipsOnly($mount, $mounts);

                foreach ($enemies as $enemy) {
                    if ($shipsOnly && $enemy instanceof FighterFlight) continue;
                    $best = null;
                    foreach ($modes as $mode) {
                        $shot = $view->shot($unit, $weapon, $enemy, $mode);
                        if ($shot === null || $shot['chance'] < $minimum) continue;
                        if ($shot['edge'] && $shot['chance'] < AIConfig::FIRE_MIN_CHANCE_EDGE) continue;
                        if ($best === null || $shot['damage'] > $best['damage']) $best = $shot;
                    }
                    if ($best !== null) $options[$enemy->id] = $best;
                }

                $intercept = self::interceptRating($weapon);
                $unitGuns[] = array('unit' => $unit, 'weapon' => $weapon, 'fighter' => $mount['fighter'], 'options' => $options, 'intercept' => $intercept,
                    'heavy' => self::isHeavy($weapon) && $view->skill('heavyFocus')); //Easy draws its heavy guns as light ones (stage T5f)
            }

            if (!($lastStand && isset(Posture::$mustShoot[(int)$unit->id]))) $held += self::holdBack($view, $unit, $unitGuns);
            foreach ($unitGuns as $gun) {
                if (count($gun['options']) > 0) $guns[] = $gun;
            }
        }

        $kept = self::holdSlow($view, $guns);
        //stage T9 (E1): Hard offers each gun of a split-shot weapon to the greedy pick on its own (the skill 'splitShots')
        $splitWeapons = ($view->skill('splitShots') && !self::$wholeOnly) ? self::splitGuns($view, $guns) : 0;

        /* INTERCEPTION (InterceptModel; the user's feedback, 2026-11-15). Each target's volley is
           valued as the server will leave it once that target's interceptors have taken their pick:
           a shot is worth what it ADDS to its target's volley - which is less than its own damage
           while the target has interceptors to spare, and can be more, when it draws them off a
           shot already planned. The bot's own missiles of this turn are in the volleys from the
           start: they arrive now, and the interceptors that go to them do not go to the guns. */
        $model = new InterceptModel($view);
        $volleys = array();
        foreach ($enemies as $enemy) $volleys[$enemy->id] = array('entries' => array(), 'value' => 0.0);
        foreach (self::ownMissiles($view, $state) as $targetId => $entries) {
            $volleys[$targetId]['entries'] = $entries;
            $volleys[$targetId]['value'] = $model->value($view->gamedata->getShipById($targetId), $entries);
            $state[$targetId]['booked'] += $volleys[$targetId]['value'];
        }

        //Greedy: take the best shot left, book it, judge the rest again.
        $added = array(); //"gun:target" => what the shot adds to its target's volley; forgotten when that volley changes
        $swallowed = array(); //slow guns turned away at some point because the interceptors would have taken too much
        while (count($guns) > 0) {
            $bestScore = 0;
            $bestGun = null;
            $bestTarget = null;
            $bestAdds = 0;

            foreach ($guns as $index => $gun) {
                foreach ($gun['options'] as $targetId => $shot) {
                    $key = $index . ':' . $targetId;
                    if (!isset($added[$key])) $added[$key] = self::adds($model, $view, $volleys[$targetId], $targetId, $gun, $shot);
                    $adds = $added[$key];
                    if ($shot['damage'] <= 0) continue;
                    /* A slow weapon's floor holds on the chance the shot keeps once the interceptors have
                       had it, not before: a reload spent on a shot they will swallow is a reload wasted.
                       A one-turn gun loses nothing by firing, and fires for whatever it adds. */
                    if (isset($shot['floor']) && $shot['chance'] * $adds / $shot['damage'] < $shot['floor']) {
                        $swallowed[$index] = true;
                        continue;
                    }

                    $score = self::score(array('damage' => $adds) + $shot, $state[$targetId], $maxThreat, $gun['heavy'], (float)$view->skill('focus'));
                    if ($score > $bestScore) {
                        $bestScore = $score;
                        $bestGun = $index;
                        $bestTarget = $targetId;
                        $bestAdds = $adds;
                    }
                }
            }

            if ($bestGun === null) break; //nothing left is worth an order: everything in reach is already dead on paper

            $gun = $guns[$bestGun];
            $shot = $gun['options'][$bestTarget];
            $volleys[$bestTarget]['entries'][] = array('unit' => $gun['unit'], 'weapon' => $gun['weapon'], 'shot' => $shot, 'ballistic' => false);
            $volleys[$bestTarget]['value'] += $bestAdds;
            $state[$bestTarget]['booked'] += $bestAdds;
            foreach (array_keys($added) as $key) {
                if (substr($key, strpos($key, ':') + 1) == $bestTarget) unset($added[$key]);
            }
            $plan[] = array(
                'unit' => $gun['unit'], 'weapon' => $gun['weapon'], 'fighter' => $gun['fighter'],
                'target' => $view->gamedata->getShipById($bestTarget),
                'mode' => $shot['mode'], 'chance' => $shot['chance'], 'orders' => $shot['orders'], 'shots' => $shot['shots'],
                'damageclass' => $shot['damageclass'], 'damage' => $bestAdds, 'score' => $bestScore
            ) + (isset($gun['split']) ? array('split' => $gun['split']) : array());
            unset($guns[$bestGun]);
            self::dropConflicts($guns, $gun);
        }

        /* Stage T6g: a flight that must fire this turn (the side down to its fighters, Posture::$mustShoot) fires every gun it has a
           shot for, at the target it would do most to - into the interceptors too, which the greedy pick above declines (a shot they
           swallow adds nothing): the turn must not go by without a shot. Its guns were kept back for none above either. */
        $forced = 0;
        if ($lastStand && count(Posture::$mustShoot) > 0) {
            foreach (array_keys($guns) as $index) {
                if (!isset($guns[$index])) continue; //an exclusive weapon of its craft took its place (dropConflicts)
                $gun = $guns[$index];
                if (!isset(Posture::$mustShoot[(int)$gun['unit']->id])) continue;
                $pick = null;
                foreach ($gun['options'] as $targetId => $shot) {
                    if ($pick === null || $shot['damage'] > $gun['options'][$pick]['damage']) $pick = $targetId;
                }
                if ($pick === null) continue;
                $shot = $gun['options'][$pick];
                $plan[] = array(
                    'unit' => $gun['unit'], 'weapon' => $gun['weapon'], 'fighter' => $gun['fighter'],
                    'target' => $view->gamedata->getShipById($pick),
                    'mode' => $shot['mode'], 'chance' => $shot['chance'], 'orders' => $shot['orders'], 'shots' => $shot['shots'],
                    'damageclass' => $shot['damageclass'], 'damage' => 0, 'score' => 0
                ) + (isset($gun['split']) ? array('split' => $gun['split']) : array());
                unset($guns[$index]);
                self::dropConflicts($guns, $gun);
                $forced++;
            }
        }
        if ($splitWeapons > 0) $plan = self::mergeSplits($plan);

        /* Stage T6d (built at T8): LAST-RESORT RAMMING - a ship Posture::rammer lets ram (the Desperate rule; no usable weapon, or its
           primary down to RAM_PRIMARY_SHARE) rams the enemy SHIP in its own hex it does most to (Posture::ramValue: the ram's damage
           against what comes back), when that is worth anything. Its guns have fired above as any ship's do. */
        $rams = 0;
        $designedRams = 0; //stage T9: rams by units designed to ram (Posture::rammer)
        foreach ($view->myUnits() as $unit) {
            $ram = Posture::rammer($view, $unit);
            if ($ram === null) continue;
            $worth = $view->value($unit) / $view->healthLeft($unit);
            $best = null;
            foreach ($enemies as $enemy) {
                if ($enemy instanceof FighterFlight || !empty($enemy->mine) || $enemy->isTerrain()) continue;
                $value = Posture::ramValue($view, $unit, $ram, $enemy, array($state[$enemy->id]['health'], $state[$enemy->id]['value'] / $state[$enemy->id]['health'],
                    $view->healthLeft($unit), $worth, (float)$view->skill('shipDanger')));
                if ($value > 0 && ($best === null || $value > $best[1])) $best = array($enemy, $value);
            }
            if ($best === null) continue;
            $plan[] = array(
                'unit' => $unit, 'weapon' => $ram, 'fighter' => null, 'target' => $best[0],
                'mode' => 1, 'chance' => (int)round($view->chanceOf($unit, $ram, $best[0], false)), 'orders' => 1, 'shots' => 1,
                'damageclass' => strtolower((string)$ram->weaponClass), 'damage' => $best[1], 'score' => $best[1]
            );
            $rams++;
            if ($ram->designedToRam) $designedRams++;
        }

        /* Stage T9, M2: a flight DESIGNED to ram (the Orieni Hunter-Killers) rams whatever of its quarry shares its hex - every craft with
           its own Ramming Attack, an order each, all at one unit (as AutomatedMovement::createAutomatedRamOrders rams for an Uncontrolled
           one): the largest enemy ship there (Posture::ramClass), the most it does breaking a tie; a fighter flight only once the enemy has
           no ship left (the user, 2026-11-24). An Uncontrolled flight is the server's (Posture::flightRams gives it no craft). */
        $enemyShips = false;
        foreach ($enemies as $enemy) {
            if (!($enemy instanceof FighterFlight) && empty($enemy->mine) && !$enemy->isTerrain()) $enemyShips = true;
        }
        foreach ($view->myUnits() as $unit) {
            $craft = Posture::flightRams($view, $unit);
            if (count($craft) == 0) continue;
            $ram = $craft[0]['ram'];
            $best = null;
            foreach ($enemies as $enemy) {
                if (!empty($enemy->mine) || $enemy->isTerrain()) continue;
                if ($enemy instanceof FighterFlight && $enemyShips) continue;
                $value = Posture::flightRamValue($view, $unit, $ram, $enemy, count($craft), $state[$enemy->id]['health'], $state[$enemy->id]['value'] / $state[$enemy->id]['health']);
                if ($value <= 0) continue;
                $key = array(($enemy instanceof FighterFlight) ? -1 : Posture::ramClass($enemy), $value);
                if ($best === null || $key > $best[1]) $best = array($enemy, $key);
            }
            if ($best === null) continue;
            $target = $best[0];
            foreach ($craft as $one) {
                $plan[] = array(
                    'unit' => $unit, 'weapon' => $one['ram'], 'fighter' => $one['fighter'], 'target' => $target,
                    'mode' => 1, 'chance' => (int)round($view->chanceOf($unit, $one['ram'], $target, false)), 'orders' => 1, 'shots' => 1,
                    'damageclass' => strtolower((string)$one['ram']->weaponClass), 'damage' => $best[1][1] / count($craft), 'score' => $best[1][1] / count($craft)
                );
            }
            $rams++;
            $designedRams++;
        }

        //each weapon back in the mode its order is in: the volleys were valued with every candidate in turn
        foreach ($plan as $shot) {
            if ((int)$shot['weapon']->firingMode !== (int)$shot['mode']) $shot['weapon']->changeFiringMode((int)$shot['mode']);
        }

        $targets = array();
        $splitting = array(); //stage T9: the weapons ordered in their split mode (Hard)
        foreach ($plan as $shot) {
            $targets[$shot['target']->id] = true;
            if (!empty($shot['split'])) $splitting[spl_object_id($shot['weapon'])] = true;
        }
        $intercepted = 0; //slow guns left unfired because the interceptors would have swallowed their shot
        foreach (array_keys($swallowed) as $index) {
            if (isset($guns[$index])) $intercepted++;
        }
        self::$lastSummary = count($plan) . ' weapons at ' . count($targets) . ' of ' . count($enemies) . ' enemies; ' . $held . ' held to intercept'
            . ($kept > 0 ? '; ' . $kept . ' slow weapons kept for a better shot' : '')
            . ($intercepted > 0 ? '; ' . $intercepted . ' slow weapons not fired into the interceptors' : '')
            . ($forced > 0 ? '; ' . $forced . ' fired by flights that must fire (the side down to its fighters)' : '')
            . ($rams > 0 ? '; ' . $rams . ' RAMMING (' . ($designedRams == $rams ? 'designed to ram' : 'last resort, Desperate rule') . ')' : '')
            . (count($splitting) > 0 ? '; ' . count($splitting) . ' weapons splitting their shots' : '');

        /* Hard's split is kept only where it pays (the user: "whether there's any advantage in damage potential by splitting"): the
           same decision is made again with every weapon whole, and the split plan stands only when it is worth more (planWorth). The
           greedy pick is no optimiser - on one harness board in eight its finer pieces came out worse. */
        if ($splitWeapons > 0) {
            $summary = self::$lastSummary;
            self::$wholeOnly = true;
            try {
                $whole = self::plan($view);
            } finally {
                self::$wholeOnly = false;
            }
            if (self::planWorth($view, $whole) >= self::planWorth($view, $plan)) return $whole; //its weapons already in its modes
            foreach ($plan as $shot) {
                if ((int)$shot['weapon']->firingMode !== (int)$shot['mode']) $shot['weapon']->changeFiringMode((int)$shot['mode']);
            }
            self::$lastSummary = $summary;
        }

        return $plan;
    }

    /* Stage T9 (E9, exclusive weapons - the Sitara's Ion Bolt, the Phalan's Plasma Gun): do two guns exclude each other? On one craft (or
       one ship) a weapon marked exclusive cannot fire beside another weapon of the same kind - direct fire or ballistic - with an order
       (weaponManager.checkConflictingFireOrder). Two pieces of one split weapon never do. */
    public static function conflicts(array $a, array $b)
    {
        return $a['unit'] === $b['unit'] && self::excludes($a['weapon'], $a['fighter'], $b['weapon'], $b['fighter']);
    }

    //The same, for two weapons of one unit: each with the craft carrying it (null on a ship).
    private static function excludes(Weapon $a, $fighterA, Weapon $b, $fighterB)
    {
        if ($a === $b || $fighterA !== $fighterB) return false;
        if (!$a->exclusive && !$b->exclusive) return false;
        return (bool)$a->ballistic === (bool)$b->ballistic;
    }

    //The guns $picked now rules out (conflicts): off the list.
    private static function dropConflicts(array &$guns, array $picked)
    {
        foreach ($guns as $index => $gun) {
            if (self::conflicts($picked, $gun)) unset($guns[$index]);
        }
    }

    /* The user, 2026-11-23: "these more powerful guns will be fired at ships, and the normal paired guns will be fired at fighters or when
       the larger gun is recharging". An exclusive weapon is therefore offered no FLIGHT while its craft has another gun it could fire this
       turn instead ($mounts: the unit's usable weapons, AIView::weapons); with none (the Sitara carries the Ion Bolt alone, or the paired
       guns are lost) it takes a flight as any gun does. While it reloads it is not in $mounts, and the paired guns take every target. */
    private static function shipsOnly(array $mount, array $mounts)
    {
        if (!$mount['weapon']->exclusive) return false;
        foreach ($mounts as $other) {
            if (self::excludes($mount['weapon'], $mount['fighter'], $other['weapon'], $other['fighter'])) return true;
        }
        return false;
    }

    /* What a plan is worth (stage T9 - Hard's split against the whole weapons): each target's planned damage (its volley, interception
       counted: the rows' 'damage') up to FIRE_OVERKILL times what it has left, as score() counts it, times what a point of it is worth
       and the target's threat weight. Rams are left out (their 'damage' is a value, and both plans hold the same rams). */
    public static function planWorth(AIView $view, array $plan)
    {
        $dealt = array();
        foreach ($plan as $shot) {
            if ($shot['weapon']->isRammingAttack) continue;
            $id = (int)$shot['target']->id;
            $dealt[$id] = (isset($dealt[$id]) ? $dealt[$id] : 0) + $shot['damage'];
        }
        $maxThreat = 0;
        foreach ($view->enemies() as $enemy) $maxThreat = max($maxThreat, $view->threat($enemy));
        $worth = 0;
        foreach ($dealt as $id => $damage) {
            $target = $view->gamedata->getShipById($id);
            $health = $view->healthLeft($target);
            if ($health <= 0) continue;
            $threat = 1 + ($maxThreat > 0 ? AIConfig::FIRE_THREAT_WEIGHT * $view->threat($target) / $maxThreat : 0);
            $worth += min($damage, $health * AIConfig::FIRE_OVERKILL) / $health * $view->value($target) * $threat;
        }
        return $worth;
    }

    /* Stage T9, enabler E1 - HARD'S SPLIT (the skill 'splitShots'; the user, 2026-11-23: "Hard could consider whether there's any
       advantage in damage potential by splitting"). Every gun of a split-shot weapon the bot has a port of (AIView::splitShot) that
       has a shot this turn is replaced, in $guns (by reference), by one candidate per gun: the weapon's split mode, one order, at any
       target the whole weapon had a shot at. The greedy pick then gives each gun the target it does most for - a second gun goes
       elsewhere when its first target is dead on paper, or is not fired at all when it would add nothing. mergeSplits() puts the
       weapon back together. Returns how many weapons were split up. */
    private static function splitGuns(AIView $view, array &$guns)
    {
        $count = 0;
        foreach (array_keys($guns) as $index) {
            $gun = $guns[$index];
            $weapon = $gun['weapon'];
            if (!in_array(get_class($weapon), AIView::SPLIT_FAMILIES, true) || count($gun['options']) == 0) continue;
            $mode = null;
            foreach (array_keys($weapon->firingModes) as $m) {
                if ($view->splitsIn($weapon, (int)$m)) { $mode = (int)$m; break; }
            }
            if ($mode === null) continue;

            $options = array();
            $parts = 0;
            foreach ($gun['options'] as $targetId => $whole) {
                $shot = $view->splitShot($gun['unit'], $weapon, $view->gamedata->getShipById($targetId), $mode);
                if ($shot === null) continue;
                if (isset($whole['floor'])) $shot['floor'] = $whole['floor'];
                $options[$targetId] = $shot;
                $parts = max($parts, (int)$weapon->guns); //splitShot left the weapon in the split mode: its gun count there
            }
            if (count($options) == 0 || $parts < 2) continue;

            unset($guns[$index]);
            for ($g = 0; $g < $parts; $g++) {
                $guns[] = array('options' => $options, 'split' => array('key' => $index, 'guns' => $parts, 'whole' => $gun['options'])) + $gun;
            }
            $count++;
        }
        return $count;
    }

    /* Hard's split weapons put back together (splitGuns): a weapon whose every gun went to ONE target is ordered in its plain mode,
       exactly as Normal orders it (the page's Normal mode: one order a gun, all at that unit); any other is ordered in its split
       mode - for each target, one order for each gun sent there (the page: one click each). */
    private static function mergeSplits(array $plan)
    {
        $out = array();
        $groups = array();
        foreach ($plan as $row) {
            if (!isset($row['split'])) { $out[] = $row; continue; }
            $groups[$row['split']['key']][] = $row;
        }
        foreach ($groups as $rows) {
            $byTarget = array();
            foreach ($rows as $row) $byTarget[(int)$row['target']->id][] = $row;
            $first = $rows[0];
            if (count($byTarget) == 1 && count($rows) == $first['split']['guns']) {
                $whole = $first['split']['whole'][(int)$first['target']->id];
                $out[] = array(
                    'unit' => $first['unit'], 'weapon' => $first['weapon'], 'fighter' => $first['fighter'], 'target' => $first['target'],
                    'mode' => $whole['mode'], 'chance' => $whole['chance'], 'orders' => $whole['orders'], 'shots' => $whole['shots'],
                    'damageclass' => $whole['damageclass'], 'damage' => array_sum(array_column($rows, 'damage')), 'score' => array_sum(array_column($rows, 'score'))
                );
                continue;
            }
            foreach ($byTarget as $list) {
                $row = $list[0];
                $row['orders'] = count($list);
                $row['damage'] = array_sum(array_column($list, 'damage'));
                $row['score'] = array_sum(array_column($list, 'score'));
                $row['split'] = true;
                $out[] = $row;
            }
        }
        return $out;
    }

    //What a shot adds to its target's volley, interception counted.
    private static function adds(InterceptModel $model, AIView $view, array $volley, $targetId, array $gun, array $shot)
    {
        $entries = $volley['entries'];
        $entries[] = array('unit' => $gun['unit'], 'weapon' => $gun['weapon'], 'shot' => $shot, 'ballistic' => false);
        return $model->value($view->gamedata->getShipById($targetId), $entries) - $volley['value'];
    }

    /* The bot's own missiles arriving this turn - its ballistic orders of Initial Orders, at a unit
       it may shoot at - as volley entries by target: what each should do where its target now
       stands, priced from its launch hex (AIView::launched). */
    private static function ownMissiles(AIView $view, array $state)
    {
        $volleys = array();
        foreach ($view->myUnits() as $unit) {
            foreach ($view->mounts($unit) as $mount) {
                $weapon = $mount['weapon'];
                if (!$weapon->ballistic) continue;
                $byTarget = array(); //target id => array(mode, orders)
                foreach ($weapon->fireOrders as $fire) {
                    if ((int)$fire->turn !== $view->turn || $fire->type !== 'ballistic' || !isset($state[(int)$fire->targetid])) continue;
                    $targetId = (int)$fire->targetid;
                    if (!isset($byTarget[$targetId])) $byTarget[$targetId] = array((int)$fire->firingMode, 0);
                    $byTarget[$targetId][1]++;
                }
                foreach ($byTarget as $targetId => $launch) {
                    //(stage T9, M8: a Ballistic Torpedo launcher's orders at one unit are one volley - AIView::launched prices them together)
                    $shot = $view->launched($unit, $weapon, $view->gamedata->getShipById($targetId), $launch[0], $launch[1]);
                    if ($shot === null || $shot['chance'] < 1) continue;
                    $shot['orders'] = $launch[1]; //one order is one missile
                    $volleys[$targetId][] = array('unit' => $unit, 'weapon' => $weapon, 'shot' => $shot, 'ballistic' => true);
                }
            }
        }
        return $volleys;
    }

    /* The least hit chance (percent) a weapon is fired at. A shot costs the weapon its reload, so
       a slow weapon asks for more (AIConfig::FIRE_MIN_CHANCE_BY_LOAD). $parting: the floor for a
       slow weapon whose target is drawing away - nothing better is coming. $pulsePremium: the level's skill (stage T5f - Easy
       fires slow pulse weapons at the plain floors). */
    public static function minimumChance(Weapon $weapon, $parting = false, $pulsePremium = true)
    {
        $load = (int)$weapon->getLoadingTime();
        if ($load <= 1) return AIConfig::FIRE_MIN_CHANCE;
        /* a slow PULSE weapon's damage grows much faster than its chance: both its floors are higher (user ruling, 2026-11-19) - and so
           does a slow weapon whose every hit grows with the roll (stage T9, M4 - the user, 2026-11-23, on the Antimatter Converter: "Its
           damage does benefit from better hit chances though, so just add a hit chance premium like we do with Pulse weapons") */
        $premium = ($pulsePremium && ($weapon->damageType === 'Pulse' || AIView::rollScaled($weapon))) ? AIConfig::FIRE_PULSE_PREMIUM : 0;

        //the parting floors by loading time too (stage T6a, game 4774: never below the user's line for a slow gun)
        $floor = AIConfig::FIRE_MIN_CHANCE;
        foreach (($parting ? AIConfig::FIRE_MIN_CHANCE_PARTING : AIConfig::FIRE_MIN_CHANCE_BY_LOAD) as $turns => $chance) {
            if ($load >= $turns) $floor = $chance;
        }
        return $floor + $premium;
    }

    /* A HEAVY gun (user, 2026-11-19 - "concentrate a fleet's heavy firepower to try and take down an enemy ship"): one
       that takes more than a turn to load, or does FIRE_HEAVY_DAMAGE a shot in its current mode. */
    public static function isHeavy(Weapon $weapon)
    {
        if ((int)$weapon->getLoadingTime() > 1) return true;
        return ($weapon->minDamage + $weapon->maxDamage) / 2 >= AIConfig::FIRE_HEAVY_DAMAGE;
    }

    /* FIRE NOW, OR A TURN CLOSER? (user, 2026-11-15.) A weapon that loads in one turn loses
       nothing by firing, so this is only about the slow ones. Each of a slow gun's shots is
       priced a second time where shooter and target will be if both fly straight on - the real
       hit formula again, so the range penalty comes into it, and for a plasma weapon the damage
       the range takes off as well. The arc is not asked next turn: a player expects to keep his
       guns on the target he is closing with. Then, per shot:

         - next turn is enough better (AIConfig::FIRE_HOLD_CONFIDENCE and the weapon's loading
           time - a turn's wait is half a 2-turn gun's fire and a quarter of a 4-turn gun's): wait;
         - not worth waiting for, but under the weapon's floor (minimumChance): wait - unless the
           target is drawing away, when the parting floor is all that is asked.

       Only the SAME target a turn on is looked at, so a gun never waits for an enemy it cannot
       bear on. Empties the options of the guns that wait (by reference); returns how many. */
    private static function holdSlow(AIView $view, array &$guns)
    {
        $slow = array();
        foreach ($guns as $index => $gun) {
            if ((int)$gun['weapon']->getLoadingTime() > 1) $slow[] = $index;
        }
        if (count($slow) == 0) return 0;

        $gamedata = $view->gamedata;
        $price = function () use ($view, $gamedata, &$guns, $slow) {
            $worth = array();
            foreach ($slow as $index) {
                $gun = $guns[$index];
                foreach ($gun['options'] as $targetId => $shot) {
                    if ((int)$gun['weapon']->firingMode !== (int)$shot['mode']) $gun['weapon']->changeFiringMode((int)$shot['mode']);
                    $worth[$index][$targetId] = $view->estimate($gun['unit'], $gun['weapon'], $gamedata->getShipById($targetId), false);
                }
            }
            return $worth;
        };
        $now = $price();
        $next = $view->ahead($price);

        $kept = 0;
        foreach ($slow as $index) {
            $weapon = $guns[$index]['weapon'];
            $wait = AIConfig::FIRE_HOLD_CONFIDENCE * (1 - 1 / max(1, (int)$weapon->getLoadingTime()));
            $floor = self::minimumChance($weapon, false, $view->skill('pulsePremium'));

            foreach ($guns[$index]['options'] as $targetId => $shot) {
                $a = $now[$index][$targetId];
                $b = $next[$index][$targetId];
                if ($a <= 0 && $b <= 0) $hold = ($shot['chance'] < $floor);     //nothing to compare (a shot the estimate will not price): the floor alone
                else if ($a < $b * $wait) $hold = true;                          //a turn closer is worth the wait
                else $hold = ($shot['chance'] < $floor && $b >= $a);            //a poor shot that is not getting worse
                if ($hold) unset($guns[$index]['options'][$targetId]);
                //the floor the shot was let through on, which plan() holds a slow gun to again once interception is counted
                else $guns[$index]['options'][$targetId]['floor'] = ($shot['chance'] < $floor) ? self::minimumChance($weapon, true, $view->skill('pulsePremium')) : $floor;
            }

            if (count($guns[$index]['options']) == 0) {
                unset($guns[$index]);
                $kept++;
            }
        }

        return $kept;
    }

    /* What a shot is worth, given what is already booked on its target. Damage beyond
       FIRE_OVERKILL times the target's remaining health counts for nothing. A heavy gun is drawn to a
       target already booked far more than a light one (FIRE_FOCUS_HEAVY): the fleet's heavy fire goes
       in together, to take a ship down. $focusShare: the level's (stage T5f - Easy has no focus-fire bonus at all). */
    public static function score(array $shot, array $target, $maxThreat, $heavy = false, $focusShare = 1.0)
    {
        $room = $target['health'] * AIConfig::FIRE_OVERKILL - $target['booked'];
        $useful = min($shot['damage'], max(0, $room));
        if ($useful <= 0) return 0;

        $worth = $useful / $target['health'] * $target['value'];
        $focus = 1 + $focusShare * ($heavy ? AIConfig::FIRE_FOCUS_HEAVY : AIConfig::FIRE_FOCUS) * min(1, $target['booked'] / $target['health']);
        $threat = 1 + ($maxThreat > 0 ? AIConfig::FIRE_THREAT_WEIGHT * $target['threat'] / $maxThreat : 0);

        return $worth * $focus * $threat;
    }

    /* The intercept rating the server would use this gun with if it is left alone - 0 when it
       would not: no rating, or a weapon that takes more than a turn to load (that needs a
       selfIntercept order, which the bot does not give). */
    private static function interceptRating(Weapon $weapon)
    {
        if ($weapon->intercept <= 0) return 0;
        if (max($weapon->getLoadingTime(), $weapon->normalload) > 1) return 0;
        return (int)$weapon->intercept;
    }

    /* The interception reserve for one unit: empties the offensive options of the guns it holds
       back (by reference). Returns how many were held.

       Stage T6a (the user's notes on game 4774, AIConfig::FIRE_INTERCEPTOR_MIN_CHANCE): a gun with an offensive shot of
       FIRE_INTERCEPTOR_MIN_CHANCE or better always fires it, against missiles too - "where Defensive and Offensive priorities are
       relatively balanced, choose offence". A gun under that stays on defence only while there is something for it to stop:
       one interceptor for each interceptable enemy order that can reach the unit (interceptableIncoming) - the unit's
       interceptors with no offensive shot at all cover what they can first (they are on defence anyway: an Omega's
       Interceptors before any of its Particle Beams), then the guns under the bar, best intercept rating first, each one only
       for an order it could really stop: a shot from inside its arc (the server's own test, Firing::isLegalIntercept - a port
       battery cannot help against fire from starboard). */
    private static function holdBack(AIView $view, $unit, array &$unitGuns)
    {
        $idle = array();       //interceptors with no offensive shot: they intercept whatever is decided here
        $candidates = array();
        foreach ($unitGuns as $index => $gun) {
            if ($gun['intercept'] <= 0) continue;
            if (count($gun['options']) == 0) {
                $idle[] = $gun['weapon'];
                continue;
            }
            $bestChance = 0;
            foreach ($gun['options'] as $shot) $bestChance = max($bestChance, $shot['chance']);
            if ($bestChance < AIConfig::FIRE_INTERCEPTOR_MIN_CHANCE) $candidates[] = $index;
        }
        if (count($candidates) == 0) return 0;

        $open = self::interceptableIncoming($view, $unit); //the orders nothing covers yet
        if (count($open) == 0) return 0;
        foreach ($idle as $weapon) {
            for ($gun = 0; $gun < max(1, (int)$weapon->guns) && count($open) > 0; $gun++) self::cover($view, $weapon, $open);
        }
        if (count($open) == 0) return 0;

        usort($candidates, function ($a, $b) use ($unitGuns) {
            if ($unitGuns[$a]['intercept'] !== $unitGuns[$b]['intercept']) return $unitGuns[$b]['intercept'] - $unitGuns[$a]['intercept'];
            return $a - $b;
        });
        $held = 0;
        foreach ($candidates as $index) {
            if (count($open) == 0) break;
            $weapon = $unitGuns[$index]['weapon'];
            if (!self::cover($view, $weapon, $open)) continue; //nothing it could stop: it fires
            for ($gun = 1; $gun < max(1, (int)$weapon->guns) && count($open) > 0; $gun++) self::cover($view, $weapon, $open);
            $unitGuns[$index]['options'] = array();
            $held++;
        }
        return $held;
    }

    //Take the first order of $open that $weapon could intercept off the list (by reference); false when there is none.
    private static function cover(AIView $view, Weapon $weapon, array &$open)
    {
        foreach ($open as $key => $order) {
            if (!Firing::isLegalIntercept($view->gamedata, $weapon, $order)) continue;
            unset($open[$key]);
            return true;
        }
        return false;
    }

    /* The enemy ORDERS this Fire phase that $unit's interceptors could stop - what a player looks at before keeping a gun
       back: every gun of a visible enemy that is loaded, can be intercepted (a laser cannot: Weapon::$uninterceptable, nor a
       ram or a field: $doNotIntercept), and has a shot at the unit as both stand (arc, range and the real formula -
       AIView::estimate), as a throwaway order a gun; and every ballistic launched at the unit this turn that can be
       intercepted, the order itself. Fire is simultaneous, so whom each enemy gun will choose is not known: any gun that could
       is counted. A list of FireOrders. */
    public static function interceptableIncoming(AIView $view, $unit)
    {
        $orders = array();
        foreach ($view->enemies() as $enemy) {
            foreach ($view->mounts($enemy) as $mount) {
                $weapon = $mount['weapon'];
                if (!$view->plainGun($weapon, $mount['fighter']) || !$view->loaded($weapon)) continue;
                if (!self::interceptable($weapon)) continue;
                if ($view->estimate($enemy, $weapon, $unit, true) <= 0) continue;
                for ($gun = 0; $gun < max(1, (int)$weapon->guns); $gun++) {
                    $orders[] = new FireOrder(-1, 'normal', $enemy->id, $unit->id, $weapon->id, -1, $view->turn, $weapon->firingMode, 0, 0, $weapon->defaultShots, 0, 0, null, null, null);
                }
            }
        }
        foreach ($view->gamedata->ships as $shooter) {
            if ($shooter->team == $unit->team) continue;
            foreach ($shooter->getAllFireOrders($view->turn) as $fire) {
                if ($fire->type !== 'ballistic' || (int)$fire->targetid !== (int)$unit->id) continue;
                $weapon = $shooter->getSystemById($fire->weaponid);
                if ($weapon instanceof Weapon && !self::interceptable($weapon)) continue;
                for ($shot = 0; $shot < max(1, (int)$fire->shots); $shot++) $orders[] = $fire;
            }
        }
        return $orders;
    }

    /* Can a shot of this weapon, in its current mode, be intercepted at all (Firing::isLegalIntercept's first tests)? Public since
       stage T7: Hard's Interceptor boost keeps Interceptors for the enemy ships' interceptable fire (PowerDecider::interceptors). */
    public static function interceptable(Weapon $weapon)
    {
        return !$weapon->doNotIntercept && !$weapon->uninterceptable && !$weapon->hextarget;
    }
}
