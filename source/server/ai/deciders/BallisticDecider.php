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
 *   - any other ballistic weapon with nothing special about it (an Ion Torpedo; an Antimatter Torpedo since stage T9's M3-M5);
 *   - the BALLISTIC TORPEDO, whose shots split by default (stage T9, M8): its own pass after the salvos - torpedoes().
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
       $view must be a view of Initial Orders (AIView::$ballistics).
       (2026-11-26 - the user's tester on live game 7857: "full Apollo salvoes at the weakest fighter flight despite a more valuable
       ship") A flight moves after the launches - and sees them - and a ballistic hit is taken off by its jinking (Weapon::
       calculateHitBase: "ballistics always affected by jinking"). Every launch at a flight is priced with the flight jinking
       BALLISTIC_FLIGHT_JINK of what it can (its jinking limit, within its free thrust): a 'jink' row stood on it for the plan. Until
       then a launch at a flight was priced as if it would not jink at all. */
    public static function plan(AIView $view)
    {
        $jinked = array();
        if ($view->ballistics) {
            foreach ($view->enemies() as $enemy) {
                if (!($enemy instanceof FighterFlight) || !empty($enemy->mine)) continue;
                $most = min((int)$enemy->jinkinglimit, (int)$enemy->freethrust);
                $level = (int)ceil($most * AIConfig::BALLISTIC_FLIGHT_JINK);
                $last = $enemy->getLastMovement();
                if ($level <= 0 || !$last || Movement::getJinking($enemy, $view->turn) > 0) continue;
                $enemy->movement[] = new MovementOrder(-1, 'jink', new OffsetCoordinate($last->position), 0, 0, (int)$last->speed, (int)$last->heading, (int)$last->facing, false, $view->turn, $level, 0);
                $jinked[] = $enemy;
            }
        }
        try {
            return self::planJinked($view);
        } finally {
            foreach ($jinked as $enemy) array_pop($enemy->movement);
        }
    }

    private static function planJinked(AIView $view)
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
        $torpedoLaunchers = array(); //stage T9 (M8): Ballistic Torpedo launchers - torpedoes(), after the salvos
        foreach ($view->myUnits() as $unit) {
            foreach ($view->weapons($unit) as $mount) {
                $weapon = $mount['weapon'];
                if (AIView::isTorpedo($weapon)) {
                    $torpedoLaunchers[] = array('unit' => $unit, 'weapon' => $weapon, 'fighter' => $mount['fighter']);
                    continue;
                }
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

        $plan = array_merge($plan, self::torpedoes($view, $model, $torpedoLaunchers, $enemies, $volleys, $state, $maxThreat));

        //each launcher in the mode its order is in: the salvos were valued with every candidate in turn
        foreach ($plan as $shot) {
            if ((int)$shot['weapon']->firingMode !== (int)$shot['mode']) $shot['weapon']->changeFiringMode((int)$shot['mode']);
        }

        $targets = array();
        foreach ($plan as $shot) $targets[$shot['target']->id] = true;
        self::$last = array('launches' => count($plan), 'targets' => count($targets));

        return $plan;
    }

    /* THE BALLISTIC TORPEDO (stage T9, M8 - the user's ruling of 2026-11-23: "2-3 torpedoes at one or two ships, or 3-6 at a fighter
       flight (it targets separate fighters), backed by 1 or 2 OEW. Easy and Normal may simply fire 1-3 a turn at one target, Hard can
       consider splitting a bit more"; the OEW is EWDecider's). A torpedo is worth what it ADDS to its target's volley, interception
       counted (InterceptModel - a launcher's volley at a ship is one order), and is launched only while that keeps the launch floor
       (floor()) of what it would do unintercepted. A torpedo added to a volley at a ship rides the same roll and lands only when the roll
       beats the need by 20 more (AIView::isTorpedo): at 80% the first lands 0.8 of a torpedo, the second 0.6, the third 0.4 - at 60% the
       third would add 0.2 and stays in the tube. At a flight each is its own roll at a fighter of its own.
       One launcher at a time - the one whose launches are worth most first (FireDecider::score), then the next with those counted:
         - Easy and Normal (torpedoTargets 1): the unit whose whole volley is worth most - each such volley built a torpedo at a time
           while the one added keeps the floor;
         - Hard (2): a torpedo at a time at whichever unit it adds most to, up to two units - so a second unit is opened only where a
           torpedo there is worth more than one more at the first.
       Limits: what the launcher holds; AIConfig::TORPEDO_SHIP_VOLLEY at ships, the level's torpedoFlightVolley at a flight - and no launcher more
       than the flight's live craft (AIView::torpedoCap; the user, 2026-11-23: each launcher on its own - launchers do not work together). $volleys / $state: plan()'s - the salvos' entries and booked damage, brought up to date.
       Returns plan rows, one per launcher and unit ('orders' = its torpedoes there, one shot each, as the page's clicks make them). */
    private static function torpedoes(AIView $view, InterceptModel $model, array $launchers, array $enemies, array &$volleys, array &$state, $maxThreat)
    {
        $rows = array();
        $own = array(); //target id => the volley entries of the launchers already planned
        $open = $launchers;
        while (count($open) > 0) {
            $pick = null;
            foreach ($open as $index => $launcher) {
                $option = self::torpedoPlan($view, $model, $launcher, $enemies, $volleys, $state, $own, $maxThreat);
                if ($option !== null && ($pick === null || $option['score'] > $pick['score'])) $pick = $option + array('index' => $index);
            }
            if ($pick === null) break;

            $launcher = $open[$pick['index']];
            unset($open[$pick['index']]);
            foreach ($pick['targets'] as $targetId => $launch) {
                $own[$targetId][] = $launch['entry'];
                $volleys[$targetId]['value'] += $launch['gain'];
                $state[$targetId]['booked'] += $launch['gain'];
                $shot = $launch['entry']['shot'];
                $rows[] = array(
                    'unit' => $launcher['unit'], 'weapon' => $launcher['weapon'], 'fighter' => $launcher['fighter'],
                    'target' => $view->gamedata->getShipById($targetId),
                    'mode' => $shot['mode'], 'round' => null, 'chance' => $shot['chance'], 'orders' => $launch['count'], 'shots' => 1,
                    'damageclass' => $shot['damageclass'], 'damage' => $launch['gain'], 'score' => $launch['score'], 'torpedo' => true
                );
            }
        }
        foreach ($own as $targetId => $entries) $volleys[$targetId]['entries'] = array_merge($volleys[$targetId]['entries'], $entries);
        return $rows;
    }

    /* One torpedo launcher's launches, those already planned counted (torpedoes()): array('score', 'targets' => target id =>
       array('count', 'entry', 'gain', 'score')), or null when it launches nothing. */
    private static function torpedoPlan(AIView $view, InterceptModel $model, array $launcher, array $enemies, array $volleys, array $state, array $own, $maxThreat)
    {
        $held = $view->torpedoes($launcher['weapon']);
        if ($held < 1) return null;
        $plan = self::torpedoChoice($view, $model, $launcher, $enemies, $volleys, $state, $own, $maxThreat, $held);
        if ($plan !== null || $held < (int)$launcher['weapon']->getNormalLoad()) return $plan;

        /* A FULL launcher's reload (implementer's call, 2026-11-23 - self-play 6146: two Elutarians' launchers sat at 6 of 6 for 23 turns). The
           InterceptModel gives a target's every interceptor to the volley, as if nothing else came at it - an Omega's six take a 90% torpedo to
           nothing, a Hyperion's five 85% to 25% - so against EA no torpedo cleared the floor. But a launcher that holds its normal load loses
           next turn's torpedo if it keeps them all, and a torpedo that draws the interceptors is one fewer on the bot's guns (FireDecider counts
           its own missiles in the volleys it prices). So it sends that ONE torpedo at the unit it is worth most at unintercepted, where its
           chance alone meets the launch floor. */
        $best = null;
        foreach ($enemies as $enemy) {
            $shot = $view->torpedoShot($launcher['unit'], $launcher['weapon'], $enemy, 1);
            if ($shot === null || $shot['chance'] < self::floor($view, $enemy)) continue;
            $score = FireDecider::score(array('damage' => $shot['damage']), $state[(int)$enemy->id], $maxThreat);
            if ($score <= 0 || ($best !== null && $score <= $best['score'])) continue;
            $entry = array('unit' => $launcher['unit'], 'weapon' => $launcher['weapon'], 'shot' => $shot, 'ballistic' => true);
            $entries = array_merge($volleys[(int)$enemy->id]['entries'], isset($own[(int)$enemy->id]) ? $own[(int)$enemy->id] : array(), array($entry));
            $gain = max(0, $model->value($enemy, $entries) - $volleys[(int)$enemy->id]['value']);
            $best = array('score' => $score, 'targets' => array((int)$enemy->id => array('count' => 1, 'entry' => $entry, 'gain' => $gain, 'score' => $score)));
        }
        return $best;
    }

    //torpedoPlan's choice by what each torpedo ADDS, interception counted - Easy and Normal one unit, Hard a torpedo at a time over two.
    private static function torpedoChoice(AIView $view, InterceptModel $model, array $launcher, array $enemies, array $volleys, array $state, array $own, $maxThreat, $held)
    {
        if ((int)$view->skill('torpedoTargets') <= 1) {
            //Easy and Normal: the one unit whose whole volley is worth most
            $best = null;
            foreach ($enemies as $enemy) {
                $launch = null;
                while (true) {
                    $has = ($launch === null) ? 0 : $launch['count'];
                    if ($has >= ($enemy instanceof FighterFlight ? $view->torpedoCap($enemy, $held) : min($held, AIConfig::TORPEDO_SHIP_VOLLEY))) break;
                    $step = self::torpedoStep($view, $model, $launcher, $enemy, $has, ($launch === null) ? 0 : $launch['gain'], $volleys, $own);
                    if ($step === null) break;
                    $launch = array('count' => $has + 1, 'entry' => $step['entry'], 'gain' => $step['gain']);
                }
                if ($launch === null) continue;
                $launch['score'] = FireDecider::score(array('damage' => $launch['gain']), $state[(int)$enemy->id], $maxThreat);
                if ($launch['score'] > 0 && ($best === null || $launch['score'] > $best['score'])) $best = array('score' => $launch['score'], 'targets' => array((int)$enemy->id => $launch));
            }
            return $best;
        }

        //Hard: a torpedo at a time, wherever it adds most, up to torpedoTargets units
        $targets = array();
        $sent = 0;
        $atShips = 0;
        while ($sent < $held) {
            $step = null;
            foreach ($enemies as $enemy) {
                $targetId = (int)$enemy->id;
                $launch = isset($targets[$targetId]) ? $targets[$targetId] : null;
                $has = ($launch === null) ? 0 : $launch['count'];
                if ($launch === null && count($targets) >= (int)$view->skill('torpedoTargets')) continue;
                if ($enemy instanceof FighterFlight ? $has + 1 > $view->torpedoCap($enemy, $held) : $atShips + 1 > AIConfig::TORPEDO_SHIP_VOLLEY) continue;
                $try = self::torpedoStep($view, $model, $launcher, $enemy, $has, ($launch === null) ? 0 : $launch['gain'], $volleys, $own);
                if ($try === null) continue;
                $score = FireDecider::score(array('damage' => $try['gain']), $state[$targetId], $maxThreat);
                $added = $score - (($launch === null) ? 0 : $launch['score']);
                if ($added > 0 && ($step === null || $added > $step['added'])) $step = $try + array('target' => $targetId, 'count' => $has + 1, 'score' => $score, 'added' => $added, 'ship' => !($enemy instanceof FighterFlight));
            }
            if ($step === null) break;
            $targets[$step['target']] = array('count' => $step['count'], 'entry' => $step['entry'], 'gain' => $step['gain'], 'score' => $step['score']);
            $sent++;
            if ($step['ship']) $atShips++;
        }
        if (count($targets) == 0) return null;
        return array('score' => array_sum(array_column($targets, 'score')), 'targets' => $targets);
    }

    /* One more torpedo of $launcher at $enemy, where it has $has already (worth $had): array('entry' => the launcher's volley there with
       it, 'gain' => what that volley adds to the unit's, interception counted) - or null when the page would not launch it, it adds
       nothing, or what it adds is under the launch floor of what one torpedo would do unintercepted. */
    private static function torpedoStep(AIView $view, InterceptModel $model, array $launcher, $enemy, $has, $had, array $volleys, array $own)
    {
        $shot = $view->torpedoShot($launcher['unit'], $launcher['weapon'], $enemy, $has + 1);
        if ($shot === null) return null;
        $targetId = (int)$enemy->id;
        $entry = array('unit' => $launcher['unit'], 'weapon' => $launcher['weapon'], 'shot' => $shot, 'ballistic' => true);
        $entries = array_merge($volleys[$targetId]['entries'], isset($own[$targetId]) ? $own[$targetId] : array(), array($entry));
        $gain = $model->value($enemy, $entries) - $volleys[$targetId]['value'];
        if ($gain - $had <= 0) return null;
        if (100 * ($gain - $had) / max(0.5, (float)$shot['perHit']) < self::floor($view, $enemy)) return null;
        return array('entry' => $entry, 'gain' => $gain);
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
                //(stage T9, M8: a torpedo's order is a click of the page's doMultipleFireOrders, which notes it 'Split')
                $rows[] = AIOrderBuilder::fire($unitId, $shot['target']->id, $weaponId, $turn, $shot['mode'], $shot['shots'], $shot['damageclass'], 'ballistic',
                    !empty($shot['torpedo']) ? 'Split' : null);
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
                //a torpedo launcher may have a row at two units (stage T9, M8 - Hard): its orders go on the one system row together
                if (isset($out[$unitId][$weaponId])) $out[$unitId][$weaponId]['fireOrders'] = array_merge($out[$unitId][$weaponId]['fireOrders'], $rows);
                else $out[$unitId][$weaponId] = array('id' => $weaponId, 'fireOrders' => $rows);
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
