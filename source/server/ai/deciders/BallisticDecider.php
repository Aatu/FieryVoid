<?php
/**
 * CPU opponent - ballistic weapons, in Initial Orders (TACTICAL_AI_PLAN.md section 5.3, stage T4).
 *
 * A missile or torpedo is LAUNCHED in Initial Orders - before anybody has moved, before the
 * enemy's EW for the turn is known - and arrives in the Fire phase. The page takes a ballistic
 * launch in that phase and no other, and builds it with the same two functions as a direct shot
 * (weaponManager.selectWeapon + targetShip), so the legality here is AIView's port again, asked
 * in its ballistic form (AIView::$ballistics): the launch range, the arc at launch (none for a
 * unit in the same hex), line of sight, fire control. tests/ai/fireOracle.js replays every answer
 * through the real weaponManager.js in phase 1, and commits the magazines through the page's own
 * count (AmmoMagazine.doVerifyAmmoUsage).
 *
 * WHAT IT LAUNCHES (principle P4):
 *   - missile racks fed from an Ammunition Magazine (AmmoMissileRackS and everything built on it,
 *     a fighter's rack included), with the plain damaging rounds only (AIConfig::BALLISTIC_ROUNDS) -
 *     twin racks bound by a same-target firing link (a Thunderbolt's two missiles go at one unit,
 *     weaponManager.getLinkedFiringBlock) since stage T6a: linkKey keeps every launch of a link on one target;
 *   - any other ballistic weapon with nothing special about it (an Ion Torpedo).
 *   Left alone: hex-targeted ballistics (Energy Mines), the old missile launchers that carry their
 *   own rounds (FighterMissileRack), jump engines, and every special round.
 *
 * AMMUNITION is the one thing a direct shot does not have. A round is drawn from the unit's
 * magazine at commit: the page sends the magazine one mode name per launch
 * (AmmoMagazine.doIndividualNotesTransfer) and so does this - the server writes an 'AmmoUsed' note
 * for each and asks no questions, so launching a round the magazine does not hold would be the
 * bot cheating. A ledger per magazine keeps the plan inside both of the page's limits: the
 * rounds of each kind, and the magazine's total.
 *
 * THE DECISION is FireDecider's (worth x focus x threat - FireDecider::score), taken by SALVOS
 * because of INTERCEPTION (InterceptModel): a ship's interceptors pile on a missile with no loss
 * for each extra one, so one or two missiles at a ship with idle interceptors are simply eaten
 * (self-play, 2026-11-15: 206 of 321 single launches would have been), while a salvo bigger than
 * the interceptors can swallow mostly gets through. So for every target the best 1, 2, 3...
 * launches still to be had are valued together, as the server will leave them, and the salvo worth
 * most per launch is taken - if its launches keep, on average, BALLISTIC_MIN_CHANCE of what each
 * would do unintercepted (a round is spent for good). A missile's hit chance does not depend on
 * the range, so there is nothing to wait for. The enemy's interceptors are all taken to go to the
 * missiles: the bot's direct fire, which would draw some of them, is not decided until the Fire
 * phase (where FireDecider counts these missiles in turn).
 *
 * Two guesses, the ones EWDecider makes: the enemy is taken to repeat last turn's defensive EW
 * (its rows are still on the enemy units when this runs), and the bot's own EW for the turn is
 * the split EWDecider has just made (TacticalAI stands it on the ships first) - an Ion Torpedo
 * uses a lock, a missile does not.
 *
 * NOT YET: holding rounds back by level (BALLISTIC_RESERVE, stage T8), interceptor missiles as a
 * decision (the server uses them by itself), special rounds (stage T7).
 */
class BallisticDecider
{
    //One line about the last plan made, for tac_ai_log.
    public static $last = array('launches' => 0, 'targets' => 0);

    /* The plan: a list of array('unit', 'weapon', 'fighter', 'target', 'mode', 'round', 'chance',
       'orders', 'shots', 'damageclass', 'damage', 'score'). One row per weapon that launches;
       'round' is the name of the round a magazine-fed rack draws, null for any other weapon.
       $view must be a view of Initial Orders (AIView::$ballistics). */
    public static function plan(AIView $view)
    {
        self::$last = array('launches' => 0, 'targets' => 0);
        $plan = array();
        if (!$view->ballistics) return $plan;

        $enemies = $view->enemies();
        if (count($enemies) == 0) return $plan;

        $maxThreat = 0;
        $state = array();
        foreach ($enemies as $enemy) {
            $state[$enemy->id] = array('health' => $view->healthLeft($enemy), 'value' => $view->value($enemy), 'threat' => $view->threat($enemy), 'booked' => 0);
            $maxThreat = max($maxThreat, $state[$enemy->id]['threat']);
        }

        //Every launcher's legal launches: per target, one per usable round, best first.
        $ledger = array();
        $guns = array();
        $linked = array(); //firing link (linkKey) => the target its group is committed to this turn
        foreach ($view->myUnits() as $unit) {
            foreach ($view->weapons($unit) as $mount) {
                $weapon = $mount['weapon'];
                $rack = ($weapon instanceof AmmoMissileRackS);
                $magazine = $rack ? $weapon->getAmmoMagazine() : null;
                if ($rack && !$magazine) continue;
                if ($magazine) self::open($ledger, $magazine);

                $options = array();
                foreach ($enemies as $enemy) {
                    $minimum = self::floor($view, $enemy);
                    $shots = array();
                    foreach ($view->modes($weapon) as $mode) {
                        $shot = $view->shot($unit, $weapon, $enemy, $mode);
                        if ($shot === null || $shot['chance'] < $minimum) continue;
                        $shot['round'] = $rack ? $weapon->firingModes[$mode] : null;
                        $shots[] = $shot;
                    }
                    if (count($shots) == 0) continue;
                    usort($shots, function ($a, $b) {
                        if ($a['damage'] != $b['damage']) return ($a['damage'] > $b['damage']) ? -1 : 1;
                        return $a['mode'] - $b['mode'];
                    });
                    $options[$enemy->id] = $shots;
                }

                if (count($options) == 0) continue;
                /* No order of this turn stands on a twin to bind it: TacGamedata::hideSystemFireOrders strips every launch of
                   the current turn from Initial Orders, the owner's own included - the links start free, as on the page. */
                $guns[] = array('unit' => $unit, 'weapon' => $weapon, 'fighter' => $mount['fighter'], 'magazine' => $magazine, 'options' => $options,
                    'link' => self::linkKey($unit, $weapon, $mount['fighter']));
            }
        }

        /* Greedy, by SALVOS. Missiles are not intercepted at a discount for each extra interceptor
           (InterceptModel), so a ship's interceptors swallow one or two missiles whole and the next
           ones get through: a launch is only worth what the salvo it belongs to gets past them.
           Each step therefore looks, for every target, at salvos of the best 1, 2, 3... launches
           still to be had there (each launcher with the best round its magazine can still give),
           valued by what they add to that target's volley once its interceptors are counted; it
           takes the salvo worth most per launch whose launches keep, on average, the chance a
           launch needs (BALLISTIC_MIN_CHANCE, of what each would do unintercepted), books it, and
           looks again - so a second salvo at the same ship is valued against the first. */
        $model = new InterceptModel($view);
        $volleys = array();
        foreach ($enemies as $enemy) $volleys[$enemy->id] = array('entries' => array(), 'value' => 0.0);

        while (count($guns) > 0) {
            $best = null;
            foreach ($enemies as $enemy) {
                $salvo = self::bestSalvo($model, $view, $enemy, $guns, $ledger, $volleys[$enemy->id], $state[$enemy->id], $maxThreat, $linked);
                if ($salvo !== null && ($best === null || $salvo['perLaunch'] > $best['perLaunch'])) $best = $salvo;
            }
            if ($best === null) break;

            $targetId = (int)$best['target']->id;
            $volleys[$targetId]['value'] += $best['gain'];
            $state[$targetId]['booked'] += $best['gain'];
            foreach ($best['launches'] as $launch) {
                list($index, $shot) = $launch;
                $gun = $guns[$index];
                if ($gun['link'] !== null) $linked[$gun['link']] = $targetId; //its twin may now launch at this unit only
                self::draw($ledger, $gun['magazine'], $shot);
                $volleys[$targetId]['entries'][] = array('unit' => $gun['unit'], 'weapon' => $gun['weapon'], 'shot' => $shot, 'ballistic' => true);
                $plan[] = array(
                    'unit' => $gun['unit'], 'weapon' => $gun['weapon'], 'fighter' => $gun['fighter'],
                    'target' => $best['target'],
                    'mode' => $shot['mode'], 'round' => $shot['round'], 'chance' => $shot['chance'], 'orders' => $shot['orders'], 'shots' => $shot['shots'],
                    'damageclass' => $shot['damageclass'], 'damage' => $best['gain'] / count($best['launches']), 'score' => $best['perLaunch']
                );
                unset($guns[$index]);
            }
        }

        //each launcher in the mode its order is in: the salvos were valued with every candidate in turn
        foreach ($plan as $shot) {
            if ((int)$shot['weapon']->firingMode !== (int)$shot['mode']) $shot['weapon']->changeFiringMode((int)$shot['mode']);
        }

        $targets = array();
        foreach ($plan as $shot) $targets[$shot['target']->id] = true;
        self::$last = array('launches' => count($plan), 'targets' => count($targets));

        return $plan;
    }

    /* The least chance a launch at $target needs: BALLISTIC_MIN_CHANCE, or _FLIGHT at a flight - or, at a level without the
       skill 'ballisticFloor' (Easy, stage T5f: "fires ballistics whenever legal"), only the least any order is given at. */
    private static function floor(AIView $view, $target)
    {
        if (!$view->skill('ballisticFloor')) return AIConfig::FIRE_MIN_CHANCE;
        return ($target instanceof FighterFlight) ? AIConfig::BALLISTIC_MIN_CHANCE_FLIGHT : AIConfig::BALLISTIC_MIN_CHANCE;
    }

    /* The best salvo at one target, or null: array('target', 'launches' => list of array(gun index,
       shot), 'gain', 'perLaunch'). Candidates are the launchers that can reach the target, each
       with the best round its magazine can still give, best first; a salvo is the first k of them
       that the magazines can all supply together. */
    private static function bestSalvo(InterceptModel $model, AIView $view, $target, array $guns, array $ledger, array $volley, array $state, $maxThreat, array $linked = array())
    {
        $targetId = (int)$target->id;
        $candidates = array();
        foreach ($guns as $index => $gun) {
            if (!isset($gun['options'][$targetId])) continue;
            //a launcher whose firing link is committed to another unit (its twin launched there) may not launch here
            if ($gun['link'] !== null && isset($linked[$gun['link']]) && $linked[$gun['link']] !== $targetId) continue;
            $candidates[] = array($index, $gun['options'][$targetId]);
        }
        if (count($candidates) == 0) return null;
        usort($candidates, function ($a, $b) {
            $da = $a[1][0]['damage'];
            $db = $b[1][0]['damage'];
            if ($da != $db) return ($da > $db) ? -1 : 1;
            return $a[0] - $b[0];
        });

        $floor = self::floor($view, $target);
        $best = null;
        $launches = array();
        $entries = $volley['entries'];
        $raw = 0;
        $chances = 0;
        foreach ($candidates as $candidate) {
            list($index, $shots) = $candidate;
            $gun = $guns[$index];
            $pick = null;
            foreach ($shots as $shot) {
                if (!self::canDraw($ledger, $gun['magazine'], $shot)) continue;
                $pick = $shot;
                break; //the best round this launcher can still draw for that target; a lesser one is not a second choice
            }
            if ($pick === null) continue;
            self::draw($ledger, $gun['magazine'], $pick);

            $launches[] = array($index, $pick);
            $entries[] = array('unit' => $gun['unit'], 'weapon' => $gun['weapon'], 'shot' => $pick, 'ballistic' => true);
            $raw += $pick['damage'];
            $chances += $pick['chance'];

            $gain = $model->value($target, $entries) - $volley['value'];
            if ($raw <= 0 || $gain <= 0) continue;
            //the launches keep, on average, at least the chance a launch needs - of what each would do unintercepted
            if ($chances / count($launches) * $gain / $raw < $floor) continue;
            $score = FireDecider::score(array('damage' => $gain), $state, $maxThreat);
            $perLaunch = $score / count($launches);
            if ($perLaunch > 0 && ($best === null || $perLaunch > $best['perLaunch'])) {
                $best = array('target' => $target, 'launches' => $launches, 'gain' => $gain, 'perLaunch' => $perLaunch);
            }
        }
        return $best;
    }

    /* The plan as what goes on each unit's entry: unit id => the entry's 'systems' rows to add.
       A ship: its launchers' fire orders, and one mode name per launch on its magazine. A flight:
       the same, nested under each fighter (the shape FireDecider::entries posts). */
    public static function systems(array $plan, $turn)
    {
        $out = array();
        foreach ($plan as $shot) {
            $unitId = (int)$shot['unit']->id;
            $weaponId = (int)$shot['weapon']->id;
            if (!isset($out[$unitId])) $out[$unitId] = array();

            $rows = array();
            for ($gun = 0; $gun < $shot['orders']; $gun++) {
                $rows[] = AIOrderBuilder::fire($unitId, $shot['target']->id, $weaponId, $turn, $shot['mode'], $shot['shots'], $shot['damageclass'], 'ballistic');
            }
            $magazineId = ($shot['round'] !== null) ? (int)$shot['weapon']->getAmmoMagazine()->id : null;
            $drawn = ($shot['round'] !== null) ? array_fill(0, count($rows), $shot['round']) : array();

            if ($shot['fighter'] !== null) {
                $fighterId = (int)$shot['fighter']->id;
                if (!isset($out[$unitId][$fighterId])) $out[$unitId][$fighterId] = array('id' => $fighterId, 'systems' => array());
                $out[$unitId][$fighterId]['systems'][] = array('id' => $weaponId, 'fireOrders' => $rows);
                if ($magazineId !== null) {
                    $found = false;
                    foreach ($out[$unitId][$fighterId]['systems'] as $n => $sub) {
                        if ($sub['id'] !== $magazineId) continue;
                        $out[$unitId][$fighterId]['systems'][$n]['individualNotesTransfer'] = array_merge($sub['individualNotesTransfer'], $drawn);
                        $found = true;
                    }
                    if (!$found) $out[$unitId][$fighterId]['systems'][] = array('id' => $magazineId, 'individualNotesTransfer' => $drawn);
                }
            } else {
                $out[$unitId][$weaponId] = array('id' => $weaponId, 'fireOrders' => $rows);
                if ($magazineId !== null) {
                    if (!isset($out[$unitId][$magazineId])) $out[$unitId][$magazineId] = array('id' => $magazineId, 'individualNotesTransfer' => array());
                    $out[$unitId][$magazineId]['individualNotesTransfer'] = array_merge($out[$unitId][$magazineId]['individualNotesTransfer'], $drawn);
                }
            }
        }
        return $out;
    }

    // ------------------------------------------------------------------ firing links (stage T6a)

    /* The firing link a launcher shares with its twins, or null: the craft (a flight's - each fighter's group is its own) or
       the ship, and the group's name - weaponManager.getLinkedFiringBlock's scope. Only the same-target kind gets this far
       (AIView::rackBlock): every launch of one link goes at one unit. */
    private static function linkKey($unit, Weapon $weapon, $fighter)
    {
        if ($weapon->linkedFiringGroup === null) return null;
        return ($fighter !== null ? 'craft ' . spl_object_id($fighter) : 'unit ' . (int)$unit->id) . ':' . $weapon->linkedFiringGroup;
    }

    // ------------------------------------------------------------------ the magazine ledger

    /* A magazine as the page counts it at commit (AmmoMagazine.doVerifyAmmoUsage): the rounds of
       each kind, their sizes, and the room left in total - less whatever share is kept back. */
    private static function open(array &$ledger, AmmoMagazine $magazine)
    {
        $key = spl_object_id($magazine);
        if (isset($ledger[$key])) return;

        $keep = max(0, min(1, (float)AIConfig::BALLISTIC_RESERVE));
        $rounds = array();
        foreach ($magazine->ammoCountArray as $name => $count) $rounds[$name] = max(0, (int)$count - (int)ceil((int)$count * $keep));
        $ledger[$key] = array('rounds' => $rounds, 'sizes' => $magazine->ammoSizeArray, 'room' => (int)$magazine->remainingAmmo);
    }

    private static function need(array $entry, array $shot)
    {
        $size = isset($entry['sizes'][$shot['round']]) ? (int)$entry['sizes'][$shot['round']] : 1;
        return array((int)$shot['orders'], (int)$shot['orders'] * $size);
    }

    private static function canDraw(array $ledger, $magazine, array $shot)
    {
        if ($magazine === null) return true;
        $entry = $ledger[spl_object_id($magazine)];
        if (!isset($entry['rounds'][$shot['round']])) return false;
        list($rounds, $room) = self::need($entry, $shot);
        return $entry['rounds'][$shot['round']] >= $rounds && $entry['room'] >= $room;
    }

    private static function draw(array &$ledger, $magazine, array $shot)
    {
        if ($magazine === null) return;
        $key = spl_object_id($magazine);
        list($rounds, $room) = self::need($ledger[$key], $shot);
        $ledger[$key]['rounds'][$shot['round']] -= $rounds;
        $ledger[$key]['room'] -= $room;
    }
}
