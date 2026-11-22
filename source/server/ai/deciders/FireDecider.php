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
        $lastStand = Posture::lastStand($view); //stage T6g: a flight that must fire this turn keeps no gun back to intercept
        foreach ($view->myUnits() as $unit) {
            $unitGuns = array();
            foreach ($view->weapons($unit) as $mount) {
                $weapon = $mount['weapon'];
                $options = array();
                $minimum = self::minimumChance($weapon, true, $view->skill('pulsePremium')); //the least it is ever fired at; holdSlow() asks for more
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
                    'heavy' => self::isHeavy($weapon) && $view->skill('heavyFocus')); //Easy draws its heavy guns as light ones (stage T5f)
            }

            if (!($lastStand && isset(Posture::$mustShoot[(int)$unit->id]))) $held += self::holdBack($view, $unit, $unitGuns);
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
            );
            unset($guns[$bestGun]);
        }

        /* Stage T6g: a flight that must fire this turn (the side down to its fighters, Posture::$mustShoot) fires every gun it has a
           shot for, at the target it would do most to - into the interceptors too, which the greedy pick above declines (a shot they
           swallow adds nothing): the turn must not go by without a shot. Its guns were kept back for none above either. */
        $forced = 0;
        if ($lastStand && count(Posture::$mustShoot) > 0) {
            foreach ($guns as $index => $gun) {
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
                );
                unset($guns[$index]);
                $forced++;
            }
        }

        /* Stage T6d (built at T8): LAST-RESORT RAMMING - a ship Posture::rammer lets ram (the Desperate rule; no usable weapon, or its
           primary down to RAM_PRIMARY_SHARE) rams the enemy SHIP in its own hex it does most to (Posture::ramValue: the ram's damage
           against what comes back), when that is worth anything. Its guns have fired above as any ship's do. */
        $rams = 0;
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
            . ($intercepted > 0 ? '; ' . $intercepted . ' slow weapons not fired into the interceptors' : '')
            . ($forced > 0 ? '; ' . $forced . ' fired by flights that must fire (the side down to its fighters)' : '')
            . ($rams > 0 ? '; ' . $rams . ' RAMMING (last resort, Desperate rule)' : '');

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
       slow weapon whose target is drawing away - nothing better is coming. $pulsePremium: the level's skill (stage T5f - Easy
       fires slow pulse weapons at the plain floors). */
    public static function minimumChance(Weapon $weapon, $parting = false, $pulsePremium = true)
    {
        $load = (int)$weapon->getLoadingTime();
        if ($load <= 1) return AIConfig::FIRE_MIN_CHANCE;
        //a slow PULSE weapon's damage grows much faster than its chance: both its floors are higher (user ruling, 2026-11-19)
        $premium = ($pulsePremium && $weapon->damageType === 'Pulse') ? AIConfig::FIRE_PULSE_PREMIUM : 0;

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
