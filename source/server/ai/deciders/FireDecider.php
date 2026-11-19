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
 * will be, range penalty and plasma range damage included).
 *
 * THE ENEMY'S INTERCEPTION (InterceptModel). A shot is worth what it adds to its target's volley
 * once the target's interceptors have been given out as the server gives them - so the bot neither
 * pretends there is none nor assumes every shot is intercepted, and a slow gun's floor holds on the
 * chance it keeps after interception.
 *
 * THE INTERCEPTION RESERVE - its own. A gun with an intercept rating that is given no order is used
 * by the server to intercept (Firing::automateIntercept), so holding one back is simply not ordering it:
 *   - a unit with ballistics coming at it holds back its best interceptors, FIRE_RESERVE_PER_BALLISTIC
 *     for each incoming shot;
 *   - any interceptor with no offensive shot of FIRE_INTERCEPTOR_MIN_CHANCE or better stays on defence.
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
                $minimum = self::minimumChance($weapon, true); //the least it is ever fired at; holdSlow() asks for more
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
                $unitGuns[] = array('unit' => $unit, 'weapon' => $weapon, 'fighter' => $mount['fighter'], 'options' => $options, 'intercept' => $intercept,
                    'heavy' => self::isHeavy($weapon));
            }

            $held += self::holdBack($view, $unit, $unitGuns);
            foreach ($unitGuns as $gun) {
                if (count($gun['options']) > 0) $guns[] = $gun;
            }
        }

        $kept = self::holdSlow($view, $guns);

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

                    $score = self::score(array('damage' => $adds) + $shot, $state[$targetId], $maxThreat, $gun['heavy']);
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
            );
            unset($guns[$bestGun]);
        }

        //each weapon back in the mode its order is in: the volleys were valued with every candidate in turn
        foreach ($plan as $shot) {
            if ((int)$shot['weapon']->firingMode !== (int)$shot['mode']) $shot['weapon']->changeFiringMode((int)$shot['mode']);
        }

        $targets = array();
        foreach ($plan as $shot) $targets[$shot['target']->id] = true;
        $intercepted = 0; //slow guns left unfired because the interceptors would have swallowed their shot
        foreach (array_keys($swallowed) as $index) {
            if (isset($guns[$index])) $intercepted++;
        }
        self::$lastSummary = count($plan) . ' weapons at ' . count($targets) . ' of ' . count($enemies) . ' enemies; ' . $held . ' held to intercept'
            . ($kept > 0 ? '; ' . $kept . ' slow weapons kept for a better shot' : '')
            . ($intercepted > 0 ? '; ' . $intercepted . ' slow weapons not fired into the interceptors' : '');

        return $plan;
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
                    $shot = $view->launched($unit, $weapon, $view->gamedata->getShipById($targetId), $launch[0]);
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
       slow weapon whose target is drawing away - nothing better is coming. */
    public static function minimumChance(Weapon $weapon, $parting = false)
    {
        $load = (int)$weapon->getLoadingTime();
        if ($load <= 1) return AIConfig::FIRE_MIN_CHANCE;
        //a slow PULSE weapon's damage grows much faster than its chance: both its floors are higher (user ruling, 2026-11-19)
        $premium = ($weapon->damageType === 'Pulse') ? AIConfig::FIRE_PULSE_PREMIUM : 0;
        if ($parting) return AIConfig::FIRE_MIN_CHANCE_PARTING + $premium;

        $floor = AIConfig::FIRE_MIN_CHANCE;
        foreach (AIConfig::FIRE_MIN_CHANCE_BY_LOAD as $turns => $chance) {
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
            $floor = self::minimumChance($weapon);

            foreach ($guns[$index]['options'] as $targetId => $shot) {
                $a = $now[$index][$targetId];
                $b = $next[$index][$targetId];
                if ($a <= 0 && $b <= 0) $hold = ($shot['chance'] < $floor);     //nothing to compare (a shot the estimate will not price): the floor alone
                else if ($a < $b * $wait) $hold = true;                          //a turn closer is worth the wait
                else $hold = ($shot['chance'] < $floor && $b >= $a);            //a poor shot that is not getting worse
                if ($hold) unset($guns[$index]['options'][$targetId]);
                //the floor the shot was let through on, which plan() holds a slow gun to again once interception is counted
                else $guns[$index]['options'][$targetId]['floor'] = ($shot['chance'] < $floor) ? AIConfig::FIRE_MIN_CHANCE_PARTING : $floor;
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
       in together, to take a ship down. */
    public static function score(array $shot, array $target, $maxThreat, $heavy = false)
    {
        $room = $target['health'] * AIConfig::FIRE_OVERKILL - $target['booked'];
        $useful = min($shot['damage'], max(0, $room));
        if ($useful <= 0) return 0;

        $worth = $useful / $target['health'] * $target['value'];
        $focus = 1 + ($heavy ? AIConfig::FIRE_FOCUS_HEAVY : AIConfig::FIRE_FOCUS) * min(1, $target['booked'] / $target['health']);
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
