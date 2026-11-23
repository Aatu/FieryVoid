<?php
/**
 * CPU opponent - what the enemy's interceptors will do to a volley (the user's feedback on stage T2,
 * 2026-11-15; used by FireDecider, and for missiles by BallisticDecider - stage T4).
 *
 * "There are only so many interceptor shots available for target, so it's possible to saturate the
 * enemy with fire." Interception is not a penalty on every shot. Each interceptor gun stops ONE
 * shot (Firing::automateIntercept), the one where it stops the most damage
 * (Firing::getBestInterception); a direct shot loses 1 (5%) more of every further interceptor put
 * on it (Weapon::getInterceptionMod), a ballistic one does not - so six interceptors can pile on a
 * lone missile and take it from 90% to nothing (self-play game 4700, 2026-11-15), while six missiles
 * at the same ship each lose one interceptor's worth and mostly get through.
 *
 * So a volley at one unit is valued as the server will leave it: the shots the bot plans at it are
 * made into throwaway fire orders and given that unit's interceptors exactly as automateIntercept
 * gives them - interceptors in Firing::compareInterceptAbility order, each gun to the shot where it
 * stops the most damage, by the server's own legality test (Firing::isLegalIntercept), its own
 * interception figure (each weapon's real getInterceptionMod) and its own estimate of what a shot
 * would do (getBestInterception's, ported in weight()). The selection loop is ported rather than
 * called because the server's re-tests the geometry on every call and a decision asks it thousands
 * of times; legality and weights are cached here. ⚠️ MIRROR of Firing::automateIntercept /
 * getBestInterception: tests/ai/fireHarness.php runs the real automateIntercept on every planned
 * volley and fails on any difference in a shot's total interception.
 *
 * The interceptors are gathered by the bot itself (pool(), a copy of
 * Firing::getUnassignedInterceptors for one unit), because the server's version would tell the bot
 * what it may not know (principle P2): it switches a missile rack into Interceptor mode when its
 * magazine holds Interceptor rounds - an enemy's rounds are hidden, so racks are not counted - and it
 * reads how much ammunition a fighter gun has left, which is the enemy's business too. Everything
 * else is what the server uses without being asked: loaded, switched on, a gun that loads in a turn
 * (a slower one needs a self-intercept order, which would be hidden), in arc of the shot. What the
 * enemy fires offensively this turn is hidden, so every such gun is taken to intercept - the worst
 * case, and what an idle gun does. Interception is for the unit fired at: the third-party cases (a
 * flight stopping missiles aimed at a ship in its hex, "free intercept" weapons) are not counted.
 */
class InterceptModel
{
    /** @var AIView */
    private $view;
    private $pools = array();    //unit id => its interceptors
    private $legal = array();    //"interceptor:shooter:weapon:mode:type:target" => bool
    private $weights = array();  //"shooter:weapon:mode:location:target" => getBestInterception's estimate
    private $listed = array();   //unit id => its place in the game's list of units

    public function __construct(AIView $view)
    {
        $this->view = $view;
        foreach (array_values($view->gamedata->ships) as $index => $unit) $this->listed[(int)$unit->id] = $index;
    }

    /* One unit's interceptors, in the order the server tries them (Firing::compareInterceptAbility,
       a stable sort over the unit's systems as listed). */
    public function pool($unit)
    {
        $id = (int)$unit->id;
        if (isset($this->pools[$id])) return $this->pools[$id];

        $gamedata = $this->view->gamedata;
        $turn = (int)$gamedata->turn;
        $valid = Closure::bind(function ($gd, $weapon) { return self::isValidInterceptor($gd, $weapon); }, null, Firing::class);
        $list = array();

        if ($unit instanceof FighterFlight) {
            foreach ($unit->systems as $fighter) {
                if (!($fighter instanceof Fighter) || $fighter->isDestroyed()) continue;
                $exclusiveFired = false;
                $own = array();
                foreach ($fighter->systems as $weapon) {
                    if (!($weapon instanceof Weapon)) continue;
                    if ($weapon->exclusive && $weapon->firedOnTurn($turn)) {
                        $exclusiveFired = true;
                        continue;
                    }
                    if (isset($weapon->ammunition)) continue; //how much is left is the enemy's business
                    if (!$weapon->firedOnTurn($turn) && $weapon->intercept > 0 && $valid($gamedata, $weapon)) $own[] = $weapon;
                }
                if (!$exclusiveFired) $list = array_merge($list, $own);
            }
        } else if (!(($unit->unavailable === true) || $unit->isDisabled())) {
            foreach ($unit->systems as $weapon) {
                if (!($weapon instanceof Weapon)) continue;
                if ($weapon->getMeteorDefenceOrder($turn) !== null) continue;
                if ($weapon->canModesIntercept) continue; //its rounds are hidden
                if ((!$weapon->firedOnTurn($turn) || $weapon->canSplitShots) && $weapon->intercept > 0 && $valid($gamedata, $weapon)) $list[] = $weapon;
            }
        }

        usort($list, array('Firing', 'compareInterceptAbility'));
        return $this->pools[$id] = $list;
    }

    /* What a volley at one unit should do once that unit's interceptors have had their say.
       $entries: list of array('unit' => the shooter, 'weapon', 'shot' => an AIView shot,
       'ballistic' => bool); one weapon at most once. Each weapon is left in its shot's mode. */
    public function value($target, array $entries)
    {
        $orders = $this->orders($target, $entries);
        $this->allocate($this->pool($target), array_column($orders, 'order'));

        $value = 0;
        foreach ($orders as $row) $value += $this->view->damageAfter($row['entry']['shot'], $row['entry']['weapon'], $row['order']->totalIntercept);
        return $value;
    }

    /* The throwaway orders of a volley - one per gun - in the order the server lists them: unit by
       unit as the game lists its units, within a unit system by system (getAllFireOrders). */
    public function orders($target, array $entries)
    {
        usort($entries, function ($a, $b) {
            $ua = $this->listed[(int)$a['unit']->id];
            $ub = $this->listed[(int)$b['unit']->id];
            if ($ua !== $ub) return $ua - $ub;
            return $this->view->mountIndex($a['unit'], $a['weapon']) - $this->view->mountIndex($b['unit'], $b['weapon']);
        });

        $turn = $this->view->turn;
        $orders = array();
        foreach ($entries as $entry) {
            $weapon = $entry['weapon'];
            $shot = $entry['shot'];
            if ((int)$weapon->firingMode !== (int)$shot['mode']) $weapon->changeFiringMode((int)$shot['mode']);
            /* Stage T9 (M8): a Ballistic Torpedo volley at a SHIP ('merged') is ONE order by the time interception is allocated - the server
               merges a launcher's torpedoes at one ship first (BallisticTorpedo::beforeFiringOrderResolution, in Firing::prepareFiring) -
               carrying all of them as its shots. */
            $merged = !empty($shot['merged']);
            $count = $merged ? 1 : max(1, (int)$shot['orders']);
            $shots = $merged ? max(1, (int)$shot['orders']) * max(1, (int)$shot['shots']) : $shot['shots'];
            for ($gun = 0; $gun < $count; $gun++) {
                $order = new FireOrder(-1, !empty($entry['ballistic']) ? 'ballistic' : 'normal', $entry['unit']->id, $target->id, $weapon->id, -1, $turn,
                    (int)$shot['mode'], (int)$shot['chance'], 0, $shots, 0, 0, null, null, $shot['damageclass']);
                $order->needed = (int)$shot['chance'];
                $order->chosenLocation = ($shot['hitLoc'] !== null) ? (int)$shot['hitLoc'] : 0;
                $order->totalIntercept = 0;
                $order->numInterceptors = 0;
                $orders[] = array('order' => $order, 'entry' => $entry);
            }
        }
        return $orders;
    }

    /* Firing::automateIntercept's allocation, for the orders of one unit's volley and that unit's
       interceptors: each gun of each interceptor, in turn, to the shot where it stops the most
       damage (getBestInterception, ported), its figure added as addToInterceptionTotal adds it.
       addToInterceptionTotal itself is not called: its fireDefensively could spend an interceptor
       missile or charge a weapon, in a game that is only being imagined. Fills each order's
       totalIntercept and numInterceptors. */
    public function allocate(array $pool, array $orders)
    {
        $gamedata = $this->view->gamedata;
        $incoming = array();
        foreach ($orders as $order) {
            $order->totalIntercept = 0;
            $order->numInterceptors = 0;
            if ($order->needed > 0) $incoming[] = $order; //automateIntercept: a shot with no chance is not in the list
        }
        if (count($incoming) == 0) return;

        foreach ($pool as $interceptor) {
            for ($gun = 0; $gun < $interceptor->guns; $gun++) {
                $best = null;
                $bestStopped = 0;
                foreach ($incoming as $order) {
                    if (!$this->legal($interceptor, $order)) continue;
                    $mod = $interceptor->getInterceptionMod($gamedata, $order);
                    if ($mod <= 0) continue;

                    $before = $order->needed - $order->totalIntercept;
                    $after = max(0, $before - $mod);
                    $modifier = min(100, $before) - $after;
                    if ($modifier <= 0) $modifier = 0.1 * ($before - $after);
                    $stopped = $modifier * $this->weight($order);
                    if ($stopped > $bestStopped) {
                        $best = $order;
                        $bestStopped = $stopped;
                    }
                }
                if ($best === null) continue;
                $best->totalIntercept += $interceptor->getInterceptionMod($gamedata, $best);
                $best->numInterceptors++;
            }
        }
    }

    //Firing::isLegalIntercept, asked once per interceptor and kind of shot.
    private function legal(Weapon $interceptor, FireOrder $order)
    {
        $key = spl_object_id($interceptor) . ':' . $order->shooterid . ':' . $order->weaponid . ':' . $order->firingMode . ':' . $order->type . ':' . $order->targetid;
        if (!isset($this->legal[$key])) $this->legal[$key] = (bool)Firing::isLegalIntercept($this->view->gamedata, $interceptor, $order);
        return $this->legal[$key];
    }

    /* getBestInterception's estimate of what a shot would do - the figure an interceptor is given to
       the shot with the most of (ported: a shot's real damage is not the point here, the server's
       estimate of it is). The firing weapon is in the order's mode. */
    private function weight(FireOrder $order)
    {
        $key = $order->shooterid . ':' . $order->weaponid . ':' . $order->firingMode . ':' . $order->chosenLocation . ':' . $order->targetid;
        if (isset($this->weights[$key])) return $this->weights[$key];

        $gamedata = $this->view->gamedata;
        $shooter = $gamedata->getShipById($order->shooterid);
        $target = $gamedata->getShipById($order->targetid);
        $weapon = $shooter->getSystemById($order->weaponid);

        $location = ($order->chosenLocation > 0) ? $order->chosenLocation : 0;
        if ($target instanceof FighterFlight) {
            $armour = $target->getSampleFighter()->getArmourComplete($target, $shooter, $weapon->weaponClass);
        } else {
            $armour = $target->getStructureSystem($location)->getArmourComplete($target, $shooter, $weapon->weaponClass);
        }
        $expected = max(0.5, (($weapon->minDamage + $weapon->maxDamage) / 2) - $armour);
        switch ($weapon->damageType) {
            case 'Flash':
                $expected = $expected * 1.25;
                break;
            case 'Raking':
                if ($expected > 10) $expected = min(10, $expected - $armour);
                break;
            case 'Piercing':
                $expected = $expected * 1.1;
                break;
            case 'Pulse':
                $expected = 0.5 * $expected * max(2, $weapon->maxpulses);
                break;
            case 'Standard':
                break;
            default:
                $expected = $expected * 0.9;
                break;
        }
        if ($weapon->maxDamage == 0) $expected = 10;
        $expected = max(0.1, $expected) * max(1, $weapon->shots);
        if ($order->calledid != -1) $expected = $expected * 1.1;

        return $this->weights[$key] = $expected;
    }
}
