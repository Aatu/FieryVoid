<?php
/**
 * CPU opponent - power, in Initial Orders (TACTICAL_AI_PLAN.md section 5.3, stage T3).
 *
 * ⚠️ THE POWER BALANCE IS THE CLIENT'S. The server has no twin of shipManager.power.getReactorPower:
 * it stores whatever power rows it is sent, and "you need to turn off systems before you can commit
 * the turn" is said by the page alone (gamedata.js, shipManager.power.getShipsNegativePower). A bot
 * that commits a ship in deficit is therefore a bot CHEATING, and nothing downstream would stop it.
 * balance() is a PORT of getReactorPower and tests/ai/ordersOracle.js replays every decision
 * through the real power.js. Change the balance in the client and it must be ported here.
 *
 * What this does, and all it does:
 *   - a system the game has forced off (a ForcedOffline critical) gets the "off" row the page
 *     writes for it at the start of the turn (power.js applyForcedOfflineEntry). The server reads
 *     the critical by itself (ShipSystem::isOfflineOnTurn), so this is for agreement, not for rule;
 *   - a ship in deficit switches systems off, least useful first, until it is not:
 *       the jump engine (the bot does not jump before stage T6), weapons still loading, other
 *       systems, loaded weapons, and only then the engine and the scanner.
 * It never overloads. A bot posts only this turn's rows, and carries nothing over: the same deficit
 * switches the same systems off again next turn, and a ship that has its power back gets everything back.
 *
 * HARD boosts as well (boost(), stage T5f-4 - the skill 'powerBoost'): one-turn guns that can reach
 * nothing this turn, and that no enemy shot needs for interception, are switched off and their power -
 * with any the reactor has spare - buys boost levels on the scanner or the engine, through the page's
 * own prices (power.js countBoostPowerUsed) and buttons (canBoost, clickPlus). ordersOracle.js clicks
 * every boost in through the real power.js. Every other level never boosts.
 *
 * LEFT ALONE (principle P4) - no rows at all, as before this stage: bases (a reactor per section),
 * Mag-Grav reactors (every system is a draw), ships with a Power Capacitor or a Plasma Battery,
 * and a ship that somehow carries a boost or an overload this turn.
 */
class PowerDecider
{
    /* What the last plan did, for tac_ai_log: 'off' systems switched off for a deficit, 'short' ships still in deficit; what
       boost() did: 'idle' guns switched off to pay for boosts, 'scanner' / 'engine' boost levels bought; and what interceptors()
       did: 'interceptors' Interceptors boosted to fire at fighters (stage T7). */
    public static $last = array('off' => 0, 'short' => 0, 'idle' => 0, 'scanner' => 0, 'engine' => 0, 'interceptors' => 0);

    //Systems the bot never switches off: the page ties other rules to them.
    const KEEP_ON = array('reactor', 'cnC', 'shieldGenerator');

    /* The bot's power rows for this turn: ship id => list of AIOrderBuilder::power rows. Every row
       is also written onto the loaded system, so that what is decided next (the scanner's output,
       which guns count) sees the ship as the server will. */
    public static function plan(TacGamedata $gamedata, $botId)
    {
        $turn = (int)$gamedata->turn;
        $plan = array();
        self::$last = array('off' => 0, 'short' => 0, 'idle' => 0, 'scanner' => 0, 'engine' => 0, 'interceptors' => 0);

        foreach (AIFallback::ownUnits($gamedata, $botId) as $ship) {
            if ($ship instanceof FighterFlight || $ship->isTerrain() || $ship->isDestroyed()) continue;
            if ($ship->getTurnDeployed($gamedata) > $turn) continue;

            $rows = array();
            foreach ($ship->systems as $system) {
                if ($system->isDestroyed() || !self::forcedOff($system, $turn) || self::hasRow($system, $turn, 1)) continue;
                $rows[] = self::switchOff($ship, $system, $turn);
            }

            //(a mine is asked for no balance by the page - only for the rows above)
            if (empty($ship->mine) && self::understands($ship, $turn)) {
                $balance = self::balance($ship, $turn);
                if ($balance < 0) {
                    foreach (self::sheddable($ship, $turn) as $system) {
                        if ($balance >= 0) break;
                        $rows[] = self::switchOff($ship, $system, $turn);
                        $balance += $system->powerReq;
                        self::$last['off']++;
                    }
                    if ($balance < 0) self::$last['short']++; //nothing left to switch off: the page lets that through too
                }
            }

            if (count($rows) > 0) $plan[(int)$ship->id] = $rows;
        }

        return $plan;
    }

    /* HARD's power management (stage T5f-4, the skill 'powerBoost' - the user's note 10 on game 4750: "switch off light
       weapons out of arc/range on early turns to boost Scanner or Engine; back on as the enemy nears"). Adds to plan()'s rows
       and returns them; writes every row onto its system, as plan() does, so the EW decided next sees a boosted scanner.

       Run on the MASKED view (it reads where the enemy is - principle P2) and before EW. Per ship whose balance this class
       answers for (understands()) and that is not in deficit:
         - which guns REACH an enemy this turn (reaches()): the units as close as both could come, the real hit formula with
           every EW point locked on - the best case, so a gun goes off only when it surely cannot fire;
         - how many enemy SHOTS could reach the ship: an interceptor stops one shot, so that many of the ship's one-turn
           interceptors stay on, the best rating first;
         - every other one-turn gun the page's Off button allows is IDLE: it pays for boosts, least useful first, and only
           as many as a level needs (a gun is never switched off for nothing);
         - a level of the SCANNER first while there is a fight this turn (a loaded gun of either side reaches), else the
           ENGINE; whichever cannot be bought, the other. "Back on as the enemy nears" needs nothing more: a one-turn gun
           switched off is loaded again the turn it is switched back on (Weapon::calculateLoading), and the bot carries no
           row over - every turn starts with every gun on.
       Only an ordinary Scanner / ELINT Scanner (every EW system of the ship one of those) and an ordinary Engine are boosted:
       their price and limit are the page's (boostPrice, boostLimit); anything else is left alone (P4). Not modelled: arcs
       (a facing changes in Movement - the same guess EWDecider makes), so a gun out of arc but in range stays on. */
    public static function boost(AIView $view, array $plan)
    {
        if (!$view->skill('powerBoost')) return $plan;
        $turn = $view->turn;

        $enemies = array();
        foreach ($view->enemies() as $enemy) {
            if (empty($enemy->mine)) $enemies[] = $enemy;
        }

        foreach (AIFallback::ownUnits($view->gamedata, $view->botId) as $ship) {
            if ($ship instanceof FighterFlight || $ship->isTerrain() || $ship->isDestroyed() || !empty($ship->mine)) continue;
            if ($ship->getTurnDeployed($view->gamedata) > $turn || $view->unitBlock($ship) !== null) continue;
            if (!self::understands($ship, $turn)) continue;
            $budget = self::balance($ship, $turn);
            if ($budget < 0) continue;

            $reach = self::reaches($view, $ship, $enemies);
            $idle = self::idle($view, $ship, $reach);
            $scanner = self::boostable($ship, $turn, 'scanner');
            $engine = self::boostable($ship, $turn, 'engine');
            $order = $reach['fight'] ? array('scanner', 'engine') : array('engine');
            $levels = array('scanner' => 0, 'engine' => 0);
            $systems = array('scanner' => $scanner, 'engine' => $engine);
            $rows = array();

            while (true) {
                $bought = false;
                foreach ($order as $kind) {
                    $system = $systems[$kind];
                    if ($system === null || $levels[$kind] >= self::boostLimit($system)) continue;
                    $price = self::boostPrice($system, $levels[$kind]);
                    if ($price === null) continue;
                    //the idle guns this level needs, least useful first - none unless the budget falls short
                    $freed = 0;
                    $take = 0;
                    while ($budget + $freed < $price && $take < count($idle)) $freed += $idle[$take++]->powerReq;
                    if ($budget + $freed < $price) continue;
                    foreach (array_splice($idle, 0, $take) as $gun) {
                        $rows[] = self::switchOff($ship, $gun, $turn);
                        self::$last['idle']++;
                    }
                    $budget += $freed - $price;
                    $levels[$kind]++;
                    $bought = true;
                    break;
                }
                if (!$bought) break;
            }

            foreach ($levels as $kind => $count) {
                if ($count <= 0) continue;
                $system = $systems[$kind];
                $system->power[] = new PowerManagementEntry(-1, $ship->id, $system->id, 2, $turn, $count);
                $rows[] = AIOrderBuilder::power($ship->id, $system->id, $turn, 2, $count);
                self::$last[$kind] += $count;
            }
            if (count($rows) > 0) $plan[(int)$ship->id] = array_merge(isset($plan[(int)$ship->id]) ? $plan[(int)$ship->id] : array(), $rows);
        }

        return $plan;
    }

    /* HARD's INTERCEPTORS IN OFFENSIVE MODE (stage T7, the skill 'interceptorBoost'). The user, 2026-11-21: "To make sure that EA
       interceptors systems can only be fired offensively by CPU players when boosted (their fire control against everything is null
       until then, preventing players from targeting a shot in the client). It's important that the AI follows this rule too. We can
       make it so only a Hard opponent boosts some Interceptors when facing a lot of enemy fighters, Easy and Normal just skip this
       mechanic."
       THE RULE needs nothing here: an Interceptor (InterceptorMkI and its family) has fire control null against everything until it
       is boosted, so AIView::shot refuses it ('fc') exactly as the page refuses the click. Boosted in Initial Orders - free, one
       level - its own setSystemDataWindow gives it fire control against fighters for the turn (6, 8 or 10, what the page is then
       sent) and takes its intercept rating away (its Energy Web stays).
       WHEN AND HOW MANY (the user's rulings of 2026-11-21, asked):
         - the enemy is FIGHTER-HEAVY - EW_FIGHTER_HEAVY of its visible value in flights, the CCEW rule's measure
           (EWDecider::fighterHeavy) - and an enemy flight can reach the ship this turn: a boosted Interceptor of it would have a
           chance at the flight with both as close as they could come (boost()'s reach: maxMove both ways and POWER_REACH_SLACK,
           every EW point locked on);
         - one Interceptor stays on interception for each interceptable shot the enemy SHIPS could put on the ship this turn (a
           loaded gun in reach that can be intercepted - a laser cannot - or a loaded launcher in range: one a gun), the best
           intercept ratings first; the rest are boosted, but never more than half of the ship's Interceptors.
       WHICH ONES (stage T7b-2 - the user, 2026-11-21, after self-play 5824's 79 boosted Interceptor-turns gave 11 shots: "It is
       actually quite rare in practice to fire interceptors at fighters, but if we can prioritise Interceptors likely to have arcs for
       boost, that would be good"): only an Interceptor whose arc covers one of the flights in reach, the ship at its facing of the start
       of the turn (the likeliest it will have - arcs are not known before Movement) and the flight where it stands; those covering the
       most flights first, then the lowest ratings (the best stay on interception), then the highest ids. One with no flight in its arc
       is never boosted: that boost could only cost its interception.
       Only an Interceptor the page's plus button takes (boostable, not switched off, under its maxBoostLevel - power.js clickPlus;
       tests/ai/ordersOracle.js clicks every one in), loaded, with fire control null against everything now and some once boosted.
       Each is written onto its system with the fire control and rating the server will give it, so what is decided next sees the
       ship's anti-fighter guns as they will be. Only on a ship whose power this class answers for (understands(), principle P4 -
       the boost costs no power, so no budget). Runs after boost(): before it, an Interceptor's boost row would make boost()'s
       understands() refuse the ship; here the scanner and engine boosts boost() just bought are the bot's own and do not count. */
    public static function interceptors(AIView $view, array $plan)
    {
        self::$last['interceptors'] = 0;
        if (!$view->skill('interceptorBoost')) return $plan;
        $turn = $view->turn;

        $enemies = EWDecider::lockable($view);
        if (!EWDecider::fighterHeavy($view, $enemies)) return $plan;
        $flights = array();
        $ships = array();
        foreach ($enemies as $enemy) {
            if (!empty($enemy->mine)) continue;
            if ($enemy instanceof FighterFlight) $flights[] = $enemy;
            else $ships[] = $enemy;
        }
        if (count($flights) == 0) return $plan;

        foreach (AIFallback::ownUnits($view->gamedata, $view->botId) as $ship) {
            if ($ship instanceof FighterFlight || $ship->isTerrain() || $ship->isDestroyed() || !empty($ship->mine)) continue;
            if ($ship->getTurnDeployed($view->gamedata) > $turn || $view->unitBlock($ship) !== null) continue;
            //principle P4, as for every power row: a ship whose power the port does not answer for gets none (powerless, a capacitor...)
            if (!self::understands($ship, $turn, true)) continue;

            $interceptors = self::offensiveInterceptors($view, $ship, $turn);
            $most = intdiv(count($interceptors), 2);
            if ($most < 1) continue;

            //the flights a boosted Interceptor of this ship could reach this turn (its boosted fire control: one family, one figure a class)
            $reached = self::flightsInReach($view, $ship, $flights, $interceptors[0]['gun'], $interceptors[0]['boosted']['fireControl']);
            if (count($reached) == 0) continue;
            $count = min($most, count($interceptors) - self::shipShots($view, $ship, $ships));
            if ($count <= 0) continue;

            //only those with a flight in reach in their arc as the units stand now: the most flights covered, then the lowest ratings, first
            $arced = array();
            foreach ($interceptors as $entry) {
                $entry['covers'] = 0;
                foreach ($reached as $flight) if ($view->onArc($ship, $entry['gun'], $flight)) $entry['covers']++;
                if ($entry['covers'] > 0) $arced[] = $entry;
            }
            usort($arced, function ($a, $b) {
                if ($a['covers'] !== $b['covers']) return $b['covers'] - $a['covers'];
                if ((int)$a['gun']->intercept !== (int)$b['gun']->intercept) return (int)$a['gun']->intercept - (int)$b['gun']->intercept;
                return (int)$b['gun']->id - (int)$a['gun']->id;
            });

            $rows = array();
            foreach (array_slice($arced, 0, $count) as $entry) {
                $gun = $entry['gun'];
                $gun->power[] = new PowerManagementEntry(-1, $ship->id, $gun->id, 2, $turn, 1);
                $gun->fireControl = $entry['boosted']['fireControl'];
                $gun->intercept = $entry['boosted']['intercept'];
                $rows[] = AIOrderBuilder::power($ship->id, $gun->id, $turn, 2, 1);
                self::$last['interceptors']++;
            }
            $plan[(int)$ship->id] = array_merge(isset($plan[(int)$ship->id]) ? $plan[(int)$ship->id] : array(), $rows);
        }

        return $plan;
    }

    /* $ship's Interceptors that a boost would turn to offence, each as array('gun' => the weapon, 'boosted' => array('fireControl',
       'intercept') as the server will set them): fire control null against everything now and some once boosted, an intercept rating
       now and none then; loaded; and a boost the page's plus button takes (power.js clickPlus: boostable, not switched off -
       canBoost - and under the maxBoostLevel hasMaxBoost() holds it to). */
    private static function offensiveInterceptors(AIView $view, $ship, $turn)
    {
        $list = array();
        foreach ($ship->systems as $system) {
            if (!($system instanceof InterceptorMkI) || $system->isDestroyed() || empty($system->boostable)) continue;
            if (self::isOff($system, $turn) || self::hasRow($system, $turn, 2) || (int)$system->maxBoostLevel < 1) continue;
            if ((int)$system->intercept <= 0 || !$view->loaded($system)) continue;
            $fc = $system->fireControl;
            if (isset($fc[0]) || isset($fc[1]) || isset($fc[2])) continue;
            $boosted = self::boostedState($system, $turn);
            if (!isset($boosted['fireControl'][0]) || $boosted['intercept'] > 0) continue;
            $list[] = array('gun' => $system, 'boosted' => $boosted);
        }
        return $list;
    }

    /* The fire control and intercept rating the server gives $gun boosted this turn - its OWN setSystemDataWindow (what
       prepareForPlayer's beforeTurn sweep runs, and so what the page is sent), on a copy: that method also rebuilds the damage
       arrays and firing mode, which the live weapon must not have done twice. */
    private static function boostedState(Weapon $gun, $turn)
    {
        $copy = clone $gun;
        $copy->power[] = new PowerManagementEntry(-1, 0, $gun->id, 2, $turn, 1);
        $copy->setSystemDataWindow($turn);
        return array('fireControl' => $copy->fireControl, 'intercept' => (int)$copy->intercept);
    }

    /* Which of these enemy flights $gun, given fire control $fireControl (its boosted one), could hit this turn - both as close as they
       could come, every EW point of the ship locked on (boost()'s reach test). The arcs are asked separately (interceptors()). */
    private static function flightsInReach(AIView $view, $ship, array $flights, Weapon $gun, $fireControl)
    {
        $turn = $view->turn;
        $points = EWDecider::points($ship, $turn);
        $own = self::maxMove($ship);
        $reached = array();
        foreach ($flights as $flight) {
            $steps = $own + self::maxMove($flight) + AIConfig::POWER_REACH_SLACK;
            $inReach = self::nearer($ship, $flight, $steps, $turn, function () use ($view, $ship, $flight, $gun, $fireControl, $points, $turn) {
                $keptEW = $ship->EW;
                $keptFc = $gun->fireControl;
                if ($points > 0) $ship->EW[] = new EWentry(-1, $ship->id, $turn, 'OEW', $points, $flight->id);
                $gun->fireControl = $fireControl;
                try {
                    return $view->estimate($ship, $gun, $flight, false) > 0;
                } finally {
                    $ship->EW = $keptEW;
                    $gun->fireControl = $keptFc;
                }
            });
            if ($inReach) $reached[] = $flight;
        }
        return $reached;
    }

    /* How many interceptable shots these enemy SHIPS could put on $ship this turn: one per loaded gun that can be intercepted
       (FireDecider::interceptable - a laser cannot) and would have a chance at the ship, and per loaded launcher in range - each with
       both as close as they could come and every EW point of the enemy locked on (boost()'s reach test). An interceptor stops one
       ORDER, so a gun counts once whatever its shots or pulses. */
    private static function shipShots(AIView $view, $ship, array $ships)
    {
        $turn = $view->turn;
        $own = self::maxMove($ship);
        $shots = 0;
        foreach ($ships as $enemy) {
            $steps = $own + self::maxMove($enemy) + AIConfig::POWER_REACH_SLACK;
            $enemyPoints = EWDecider::points($enemy, $turn);
            $shots += self::nearer($ship, $enemy, $steps, $turn, function () use ($view, $ship, $enemy, $enemyPoints, $turn) {
                $kept = $enemy->EW;
                if ($enemyPoints > 0) $enemy->EW[] = new EWentry(-1, $enemy->id, $turn, 'OEW', $enemyPoints, $ship->id);
                try {
                    $distance = $ship->getHexPos()->distanceTo($enemy->getHexPos());
                    $count = 0;
                    foreach ($view->mounts($enemy) as $mount) {
                        $gun = $mount['weapon'];
                        if ($gun->isDestroyed() || !$view->loaded($gun) || !FireDecider::interceptable($gun)) continue;
                        if ($gun->ballistic) {
                            if ($gun instanceof JumpEngine || ((int)$gun->range > 0 && $distance > (int)$gun->range)) continue;
                        } else if (!$view->plainGun($gun) || $view->estimate($enemy, $gun, $ship, false) <= 0) {
                            continue;
                        }
                        $count += max(1, (int)$gun->guns);
                    }
                    return $count;
                } finally {
                    $enemy->EW = $kept;
                }
            });
        }
        return $shots;
    }

    /* Which of $ship's guns could fire at an enemy this turn, and what the enemy could fire at it: array('guns' => weapon id
       => true for a gun of the ship that reaches some enemy, 'shots' => how many enemy orders could reach the ship (one per
       loaded gun or launcher - an interceptor stops one order), 'fight' => a LOADED gun of either side reaches - EW has a use
       this turn). Each pair
       is asked with the ship as close to the enemy as both could come (maxMove, both ways) and the best case for the shooter:
       every EW point of its ship locked on to the other, no DEW against it (this turn's is not set yet). (Public since the user's
       ruling on game 5826: EWDecider asks it whether any return fire could reach a ship at all.) */
    public static function reaches(AIView $view, $ship, array $enemies)
    {
        $turn = $view->turn;
        $result = array('guns' => array(), 'shots' => 0, 'fight' => false);
        $points = EWDecider::points($ship, $turn);
        $own = self::maxMove($ship);

        foreach ($enemies as $enemy) {
            $steps = $own + self::maxMove($enemy) + AIConfig::POWER_REACH_SLACK;
            $enemyPoints = ($enemy instanceof FighterFlight) ? 0 : EWDecider::points($enemy, $turn);
            self::nearer($ship, $enemy, $steps, $turn, function () use ($view, $ship, $enemy, $points, $enemyPoints, $turn, &$result) {
                $keptOwn = $ship->EW;
                $keptEnemy = $enemy->EW;
                if ($points > 0) $ship->EW[] = new EWentry(-1, $ship->id, $turn, 'OEW', $points, $enemy->id);
                if ($enemyPoints > 0) $enemy->EW[] = new EWentry(-1, $enemy->id, $turn, 'OEW', $enemyPoints, $ship->id);
                try {
                    $distance = $ship->getHexPos()->distanceTo($enemy->getHexPos());
                    foreach ($view->mounts($ship) as $mount) {
                        $gun = $mount['weapon'];
                        if ($gun->isDestroyed() || $gun->isOfflineOnTurn($turn) || isset($result['guns'][(int)$gun->id])) continue;
                        if ($gun->ballistic) {
                            //a launcher of its own launches this turn with this turn's EW: a fight, if it is loaded and in range
                            if (!($gun instanceof JumpEngine) && !$gun->hextarget && $view->loaded($gun) && ((int)$gun->range == 0 || $distance <= (int)$gun->range)) $result['fight'] = true;
                            continue;
                        }
                        if (!$view->plainGun($gun) || $view->estimate($ship, $gun, $enemy, false) <= 0) continue;
                        $result['guns'][(int)$gun->id] = true;
                        if ($view->loaded($gun)) $result['fight'] = true;
                    }
                    foreach ($view->mounts($enemy) as $mount) {
                        $gun = $mount['weapon'];
                        if ($gun->isDestroyed() || ($mount['fighter'] !== null && $mount['fighter']->isDestroyed()) || !$view->loaded($gun)) continue;
                        if ($gun->ballistic) {
                            //a launcher: its launch range (none = any), every round it may send interceptable
                            if ($gun instanceof JumpEngine || $gun->hextarget || ((int)$gun->range > 0 && $distance > (int)$gun->range)) continue;
                        } else {
                            if (!$view->plainGun($gun, $mount['fighter']) || $view->estimate($enemy, $gun, $ship, false) <= 0) continue;
                        }
                        //an interceptor stops one ORDER (InterceptModel) - one per gun, whatever its shots or pulses
                        $result['shots'] += max(1, (int)$gun->guns);
                        $result['fight'] = true;
                    }
                } finally {
                    $ship->EW = $keptOwn;
                    $enemy->EW = $keptEnemy;
                }
            });
        }

        return $result;
    }

    /* The one-turn guns of $ship that may pay for a boost, least useful first: on, loaded in a turn, ones the page's Off button
       allows (canOffline), reaching no enemy this turn - and not among the interceptors kept for the enemy's shots (the best
       intercept ratings, one per shot). A gun with ammunition, a compound gun or one with a firing order is left alone. */
    private static function idle(AIView $view, $ship, array $reach)
    {
        $turn = $view->turn;
        $guns = array();
        $interceptors = array();
        $drives = array();
        foreach ($ship->systems as $system) {
            /* Stage T6e (the user, 2026-11-21): a jump engine with more than half its boxes lost is not used to jump (JumpDecider - its
               failure chance is the share lost) and "can be deactivated for extra power by Hard CPU opponents": it pays first. */
            if ($system instanceof JumpEngine) {
                if (!$system->isDestroyed() && $system->powerReq > 0 && JumpDecider::driveDamage($system) > AIConfig::JUMP_MAX_DRIVE_DAMAGE
                    && !self::isOff($system, $turn) && !self::hasRow($system, $turn, 2) && empty($system->stripForJson()->powerLocked)) {
                    $ordered = false;
                    foreach ($system->fireOrders as $fire) if ((int)$fire->turn === (int)$turn) $ordered = true;
                    if (!$ordered) $drives[] = $system;
                }
                continue;
            }
            if (!($system instanceof Weapon) || $system instanceof DualWeapon || $system instanceof DuoWeapon) continue;
            if ($system->powerReq <= 0 || $system->isDestroyed() || self::isOff($system, $turn) || self::hasRow($system, $turn, 2)) continue;
            if ($system->ballistic || (int)$system->loadingtime != 1 || (int)$system->getNormalLoad() > 1 || !$view->loaded($system)) continue;
            if ($system->alwaysoverloading || $system->isOverloadingOnTurn($turn) || !empty($system->checkAmmoMagazine)) continue;
            $sent = $system->stripForJson();
            $vars = get_object_vars($system); //public properties only (AIView::flag reads a weapon's flags the same way)
            if (!empty($sent->powerLocked) || isset($vars['ammunition']) || isset($sent->ammunition)) continue;
            foreach ($system->fireOrders as $fire) {
                if ((int)$fire->turn === (int)$turn) continue 2;
            }
            if ((int)$system->intercept > 0) $interceptors[] = $system;
            if (isset($reach['guns'][(int)$system->id])) continue;
            $guns[] = $system;
        }

        //the interceptors the enemy's shots could need stay on: one per shot, the best ratings first
        usort($interceptors, function ($a, $b) {
            return ((int)$a->intercept !== (int)$b->intercept) ? (int)$b->intercept - (int)$a->intercept : (int)$a->id - (int)$b->id;
        });
        $kept = array();
        foreach (array_slice($interceptors, 0, $reach['shots']) as $gun) $kept[(int)$gun->id] = true;

        $list = array();
        foreach ($guns as $gun) {
            if (isset($kept[(int)$gun->id])) continue;
            $worth = (($gun->minDamage + $gun->maxDamage) / 2) * max(1, (int)$gun->defaultShots) * max(1, (int)$gun->guns) / $gun->powerReq;
            $list[] = array($worth, (int)$gun->id, $gun);
        }
        usort($list, function ($a, $b) {
            return ($a[0] != $b[0]) ? (($a[0] < $b[0]) ? -1 : 1) : $a[1] - $b[1];
        });
        return array_merge($drives, array_map(function ($row) { return $row[2]; }, $list));
    }

    /* The system a boost of $kind would go on, or null: 'scanner' - the one the page lets a player boost, its highest-rated
       sensors (power.js getHighestSensorsId: the first of the best by output among those NOT SWITCHED OFF - a destroyed one is
       in the comparison, at 0, and when it comes first the page offers no boost at all), when every EW system of the ship is
       an ordinary Scanner or ELINT Scanner (their price and limit are ported - any other is left alone); 'engine' - an ordinary
       Engine, switched on, the cheapest a level. A destroyed or switched-off system is never the one boosted. */
    private static function boostable($ship, $turn, $kind)
    {
        $best = null;
        $bestKey = null;
        foreach ($ship->systems as $system) {
            if ($kind === 'scanner') {
                if (!($system instanceof Scanner) && $system->outputType !== 'EW') continue;
                $class = get_class($system);
                if ($class !== 'Scanner' && $class !== 'ElintScanner') return null;
                if (self::isOff($system, $turn)) continue;
                $key = $system->isDestroyed() ? 0 : -(int)$system->getOutput();
            } else {
                if (get_class($system) !== 'Engine' || $system->isDestroyed() || self::isOff($system, $turn)) continue;
                $key = self::boostPrice($system, 0);
                if ($key === null) continue;
            }
            if ($bestKey === null || $key < $bestKey || ($key == $bestKey && (int)$system->id < (int)$best->id)) {
                $best = $system;
                $bestKey = $key;
            }
        }
        if ($best === null || $best->isDestroyed() || empty($best->boostable) || self::hasRow($best, $turn, 2)) return null;
        return $best;
    }

    /* What ONE more boost level costs when $levels are bought - power.js countBoostPowerUsed: 'output+1' costs the rating plus
       the level (output+1, output+2, ...), a number that much a level - or null for a price the port does not know. The
       efficiency and rating are the page's (the payload's, which an Advanced Engine Module or an officer may have changed). */
    public static function boostPrice($system, $levels)
    {
        $sent = $system->stripForJson();
        $efficiency = isset($sent->boostEfficiency) ? $sent->boostEfficiency : $system->boostEfficiency;
        $output = isset($sent->output) ? (int)$sent->output : (int)$system->output;
        if ($efficiency === 'output+1') return $output + (int)$levels + 1;
        if (is_int($efficiency) || (is_string($efficiency) && ctype_digit($efficiency))) return max(0, (int)$efficiency) > 0 ? (int)$efficiency : null;
        return null;
    }

    /* The most boost levels the page's plus button allows (power.js clickPlus: a limit only where hasMaxBoost() says so - a
       Scanner when its maxBoostLevel is above 0; an Engine never). */
    private static function boostLimit($system)
    {
        if ($system instanceof Scanner) {
            $sent = $system->stripForJson();
            $limit = isset($sent->maxBoostLevel) ? (int)$sent->maxBoostLevel : (int)$system->maxBoostLevel;
            return $limit > 0 ? $limit : PHP_INT_MAX;
        }
        return PHP_INT_MAX;
    }

    /* The most hexes a unit could cover this turn: its speed and all the acceleration its thrust buys (an Engine's output, a
       flight's free thrust, over the unit's acceleration cost). A unit that does not move by thrust covers none.
       (Public since stage T6b: Posture's "can it still fight" asks the same reach.) */
    public static function maxMove($unit)
    {
        if (!empty($unit->base) || !empty($unit->smallBase) || !empty($unit->osat) || !empty($unit->mine) || $unit->isTerrain()) return 0;
        $last = $unit->getLastMovement();
        $speed = $last ? max(0, (int)$last->speed) : 0;
        if ($unit instanceof FighterFlight) $thrust = (int)$unit->freethrust;
        else {
            $thrust = 0;
            foreach ($unit->systems as $system) {
                if ($system instanceof Engine && !$system->isDestroyed()) $thrust += max(0, (int)$system->getOutput());
            }
        }
        $accel = (float)$unit->accelcost;
        return $speed + ($accel > 0 ? (int)floor($thrust / $accel) : 0);
    }

    /* Run $work with $ship standing $steps hexes nearer $target (never past it) - a movement row of this turn on top of its
       list, taken off again whatever happens. (Public since stage T6b, for Posture.) */
    public static function nearer($ship, $target, $steps, $turn, callable $work)
    {
        $last = $ship->getLastMovement();
        $position = $ship->getHexPos();
        $goal = $target->getHexPos();
        for ($i = 0; $i < $steps && $position->distanceTo($goal) > 0; $i++) {
            $best = null;
            for ($direction = 0; $direction < 6; $direction++) {
                $next = $position->moveToDirection($direction);
                if ($best === null || $next->distanceTo($goal) < $best->distanceTo($goal)) $best = $next;
            }
            $position = $best;
        }
        $ship->movement[] = new MovementOrder(-1, 'move', $position, 0, 0, (int)$last->speed, (int)$last->heading, (int)$last->facing, false, $turn, 0, 0);
        try {
            return $work();
        } finally {
            array_pop($ship->movement);
        }
    }

    /* Is this a ship whose balance the port below answers for? $ownBoosts (stage T7, interceptors()): this turn's boost rows are the
       bot's own, just bought by boost() - not "a ship that somehow carries a boost", so they do not count against it (a bot's load
       carries no power row of the turn it is deciding). */
    public static function understands($ship, $turn, $ownBoosts = false)
    {
        if (!empty($ship->base) || !empty($ship->smallBase)) return false;

        $reactor = self::reactor($ship);
        if (!$reactor || $reactor->isDestroyed() || $reactor->fixedPower) return false;
        if (self::crits($reactor, 'ForcedOfflineOneTurn', $turn) > 0) return false; //powerless: the page asks nothing of it

        foreach ($ship->systems as $system) {
            if ($system->name === 'powerCapacitor' || $system->name === 'PlasmaBattery') return false;
            if ((!$ownBoosts && self::hasRow($system, $turn, 2)) || self::hasRow($system, $turn, 3)) return false;
        }
        return true;
    }

    /* shipManager.power.getReactorPower for a ship with one ordinary reactor and nothing boosted:
       what the reactor gives beyond the ship's needs, plus the need of every system that is off.
       Negative = the deficit the page will not let a player commit. */
    public static function balance($ship, $turn)
    {
        $reactor = self::reactor($ship);
        $sent = $reactor->stripForJson(); //the one-turn drains (Energy Draining Field, EM storm) reach the page as edfDrain
        $output = $reactor->output + $reactor->outputMod - (isset($sent->edfDrain) ? (int)$sent->edfDrain : 0);

        foreach ($ship->systems as $system) {
            if ($system->displayName === 'C&C') $output -= self::crits($system, 'tmppowerdown', $turn);
            if ($system->isDestroyed()) continue;
            if (self::isOff($system, $turn)) $output += $system->powerReq;
        }

        return $output;
    }

    /* Stage T6c: keep a system on that plan() switched off for a deficit - a ship leaving the battle needs its jump engine, which
       plan() sheds first - by switching off the next systems in sheddable()'s order instead. $plan is plan()'s (by reference). Returns
       a function that puts everything back, or null (nothing changed) when the system was not switched off by plan() this turn, or
       the rest of the ship cannot pay for it. */
    public static function keepOn($ship, $system, array &$plan, $turn)
    {
        $id = (int)$ship->id;
        if (self::forcedOff($system, $turn) || !self::hasRow($system, $turn, 1)) return null;

        $savedPlan = isset($plan[$id]) ? $plan[$id] : null;
        $savedPower = array();
        foreach ($ship->systems as $other) $savedPower[spl_object_id($other)] = array($other, $other->power);
        $undo = function () use (&$plan, $id, $savedPlan, $savedPower) {
            foreach ($savedPower as $saved) $saved[0]->power = $saved[1];
            if ($savedPlan === null) unset($plan[$id]);
            else $plan[$id] = $savedPlan;
        };

        $system->power = array_values(array_filter($system->power, function ($power) use ($turn) { return !((int)$power->turn === (int)$turn && (int)$power->type === 1); }));
        $rows = array();
        foreach (($savedPlan === null ? array() : $savedPlan) as $row) {
            if ((int)$row['systemid'] === (int)$system->id && (int)$row['type'] === 1) continue;
            $rows[] = $row;
        }
        $balance = self::balance($ship, $turn);
        foreach (self::sheddable($ship, $turn) as $other) {
            if ($balance >= 0) break;
            if ($other === $system) continue;
            $rows[] = self::switchOff($ship, $other, $turn);
            $balance += $other->powerReq;
        }
        if ($balance < 0) {
            $undo();
            return null;
        }
        if (count($rows) > 0) $plan[$id] = $rows;
        else unset($plan[$id]);
        return $undo;
    }

    /* The systems a deficit may switch off, in the order they go. What the page's Off button
       allows (SystemInfoButtons canOffline): a system that draws power, is on, and is not locked on. */
    public static function sheddable($ship, $turn)
    {
        $list = array();
        foreach ($ship->systems as $system) {
            if ($system->powerReq <= 0 || in_array($system->name, self::KEEP_ON, true)) continue;
            if ($system->isDestroyed() || self::isOff($system, $turn)) continue;
            $sent = $system->stripForJson();
            if (!empty($sent->powerLocked)) continue;

            if ($system instanceof JumpEngine) $rank = 0;
            else if ($system instanceof Weapon) $rank = ($system->loadingtime > $system->turnsloaded && $system->loadingtime > $system->overloadturns) ? 1 : 3;
            else if ($system instanceof Engine) $rank = 4;
            else if ($system instanceof Scanner) $rank = 5;
            else $rank = 2;

            //within a rank: the gun that gives least fire for its power first; anything else in its own order
            $worth = 0;
            if ($system instanceof Weapon && !($system instanceof JumpEngine)) {
                $worth = (($system->minDamage + $system->maxDamage) / 2) * max(1, (int)$system->defaultShots) * max(1, (int)$system->guns)
                    / max(1, (int)$system->loadingtime) / $system->powerReq;
            }
            $list[] = array($rank, $worth, (int)$system->id, $system);
        }

        usort($list, function ($a, $b) {
            if ($a[0] !== $b[0]) return $a[0] - $b[0];
            if ($a[1] != $b[1]) return ($a[1] < $b[1]) ? -1 : 1;
            return $a[2] - $b[2];
        });

        return array_map(function ($row) { return $row[3]; }, $list);
    }

    //shipManager.power.isOfflineOnTurn: forced off by a critical, or carrying an "off" row this turn.
    public static function isOff($system, $turn)
    {
        return self::forcedOff($system, $turn) || self::hasRow($system, $turn, 1);
    }

    //Forced off by the game (a ForcedOffline critical) - not by a player's own row. Public since stage T6b (Posture).
    public static function forcedOff($system, $turn)
    {
        return self::crits($system, 'ForcedOfflineOneTurn', $turn) > 0 || self::crits($system, 'ForcedOfflineForTurns', $turn) > 0;
    }

    //shipManager.criticals.countCriticalOnTurn, on the criticals the page is sent.
    public static function crits($system, $name, $turn)
    {
        $count = 0;
        foreach ($system->criticals as $critical) {
            if ($critical->phpclass === $name && $critical->turn <= $turn && ($critical->turnend == 0 || $critical->turnend >= $turn)) $count++;
        }
        return $count;
    }

    private static function hasRow($system, $turn, $type)
    {
        foreach ($system->power as $power) {
            if ((int)$power->turn === (int)$turn && (int)$power->type === $type) return true;
        }
        return false;
    }

    private static function switchOff($ship, $system, $turn)
    {
        $system->power[] = new PowerManagementEntry(-1, $ship->id, $system->id, 1, $turn, 0);
        return AIOrderBuilder::power($ship->id, $system->id, $turn, 1);
    }

    //shipManager.systems.getSystemByName(ship, "reactor"): the first one.
    private static function reactor($ship)
    {
        foreach ($ship->systems as $system) {
            if ($system->name === 'reactor') return $system;
        }
        return null;
    }
}
