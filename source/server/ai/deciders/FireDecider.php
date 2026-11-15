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
 *           there and the remaining guns look elsewhere;
 *   threat  the more an enemy can do to this fleet, the sooner it is shot (FIRE_THREAT_WEIGHT).
 *
 * THE INTERCEPTION RESERVE. A gun with an intercept rating that is given no order is used by the
 * server to intercept (Firing::automateIntercept), so holding one back is simply not ordering it:
 *   - a unit with ballistics coming at it holds back its best interceptors, FIRE_RESERVE_PER_BALLISTIC
 *     for each incoming shot;
 *   - any interceptor with no offensive shot of FIRE_INTERCEPTOR_MIN_CHANCE or better stays on defence.
 *
 * NOT YET: called shots, ballistics (stage T4), holding a slow weapon for next turn's better shot
 * beyond one threshold (T5 brings look-ahead), and every weapon AIView::weaponBlock turns away.
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

        $view = new AIView($gamedata, $botId);
        return self::entries($view, self::plan($view));
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
                    $rows[] = AIOrderBuilder::fire($unit->id, $shot['target']->id, $shot['weapon']->id, $gamedata->turn, $shot['mode'], $shot['shots'], $shot['damageclass']);
                }

                if ($shot['fighter'] !== null) {
                    $fighterId = $shot['fighter']->id;
                    if (!isset($entry['systems'][$fighterId])) $entry['systems'][$fighterId] = array('id' => $fighterId, 'systems' => array());
                    $entry['systems'][$fighterId]['systems'][] = array('id' => $shot['weapon']->id, 'fireOrders' => $rows);
                } else {
                    $entry['systems'][$shot['weapon']->id] = array('id' => $shot['weapon']->id, 'fireOrders' => $rows);
                }
            }

            $entries[] = $entry;
        }

        return $entries;
    }

    /* The plan: a list of array('unit', 'weapon', 'fighter', 'target', 'mode', 'chance', 'orders',
       'shots', 'damageclass', 'damage', 'score'). One row per weapon that fires. */
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
        foreach ($view->myUnits() as $unit) {
            $unitGuns = array();
            foreach ($view->weapons($unit) as $mount) {
                $weapon = $mount['weapon'];
                $options = array();
                $minimum = ($weapon->getLoadingTime() > 1) ? AIConfig::FIRE_MIN_CHANCE_SLOW : AIConfig::FIRE_MIN_CHANCE;
                $modes = $view->modes($weapon);

                foreach ($enemies as $enemy) {
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
                $unitGuns[] = array('unit' => $unit, 'weapon' => $weapon, 'fighter' => $mount['fighter'], 'options' => $options, 'intercept' => $intercept);
            }

            $held += self::holdBack($view, $unit, $unitGuns);
            foreach ($unitGuns as $gun) {
                if (count($gun['options']) > 0) $guns[] = $gun;
            }
        }

        //Greedy: take the best shot left, book it, judge the rest again.
        while (count($guns) > 0) {
            $bestScore = 0;
            $bestGun = null;
            $bestTarget = null;

            foreach ($guns as $index => $gun) {
                foreach ($gun['options'] as $targetId => $shot) {
                    $score = self::score($shot, $state[$targetId], $maxThreat);
                    if ($score > $bestScore) {
                        $bestScore = $score;
                        $bestGun = $index;
                        $bestTarget = $targetId;
                    }
                }
            }

            if ($bestGun === null) break; //nothing left is worth an order: everything in reach is already dead on paper

            $gun = $guns[$bestGun];
            $shot = $gun['options'][$bestTarget];
            $state[$bestTarget]['booked'] += $shot['damage'];
            $plan[] = array(
                'unit' => $gun['unit'], 'weapon' => $gun['weapon'], 'fighter' => $gun['fighter'],
                'target' => $view->gamedata->getShipById($bestTarget),
                'mode' => $shot['mode'], 'chance' => $shot['chance'], 'orders' => $shot['orders'], 'shots' => $shot['shots'],
                'damageclass' => $shot['damageclass'], 'damage' => $shot['damage'], 'score' => $bestScore
            );
            unset($guns[$bestGun]);
        }

        $targets = array();
        foreach ($plan as $shot) $targets[$shot['target']->id] = true;
        self::$lastSummary = count($plan) . ' weapons at ' . count($targets) . ' of ' . count($enemies) . ' enemies; ' . $held . ' held to intercept';

        return $plan;
    }

    /* What a shot is worth, given what is already booked on its target. Damage beyond
       FIRE_OVERKILL times the target's remaining health counts for nothing. */
    private static function score(array $shot, array $target, $maxThreat)
    {
        $room = $target['health'] * AIConfig::FIRE_OVERKILL - $target['booked'];
        $useful = min($shot['damage'], max(0, $room));
        if ($useful <= 0) return 0;

        $worth = $useful / $target['health'] * $target['value'];
        $focus = 1 + AIConfig::FIRE_FOCUS * min(1, $target['booked'] / $target['health']);
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
       back (by reference). Returns how many were held. */
    private static function holdBack(AIView $view, $unit, array &$unitGuns)
    {
        $held = 0;

        //no offensive shot good enough: stay on defence
        foreach ($unitGuns as $index => $gun) {
            if ($gun['intercept'] <= 0 || count($gun['options']) == 0) continue;
            $bestChance = 0;
            foreach ($gun['options'] as $shot) $bestChance = max($bestChance, $shot['chance']);
            if ($bestChance < AIConfig::FIRE_INTERCEPTOR_MIN_CHANCE) {
                $unitGuns[$index]['options'] = array();
                $held++;
            }
        }

        //ballistics on their way to this unit: its best interceptors wait for them
        $incoming = self::incomingBallistics($view, $unit);
        $want = (int)ceil($incoming * AIConfig::FIRE_RESERVE_PER_BALLISTIC);
        if ($want <= 0) return $held;

        $interceptors = array();
        foreach ($unitGuns as $index => $gun) {
            if ($gun['intercept'] > 0) $interceptors[] = $index;
        }
        usort($interceptors, function ($a, $b) use ($unitGuns) {
            if ($unitGuns[$a]['intercept'] !== $unitGuns[$b]['intercept']) return $unitGuns[$b]['intercept'] - $unitGuns[$a]['intercept'];
            return $a - $b;
        });

        foreach ($interceptors as $index) {
            if ($want <= 0) break;
            $want--;
            if (count($unitGuns[$index]['options']) == 0) continue; //already on defence, and counted
            $unitGuns[$index]['options'] = array();
            $held++;
        }

        return $held;
    }

    //Ballistic shots launched this turn at $unit, as far as this viewer is shown them.
    private static function incomingBallistics(AIView $view, $unit)
    {
        $count = 0;
        foreach ($view->gamedata->ships as $shooter) {
            if ($shooter->team == $unit->team) continue;
            foreach ($shooter->getAllFireOrders($view->turn) as $fire) {
                if ($fire->type === 'ballistic' && (int)$fire->targetid === (int)$unit->id) $count += max(1, (int)$fire->shots);
            }
        }
        return $count;
    }
}
