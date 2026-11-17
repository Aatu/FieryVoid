<?php
/**
 * CPU opponent - where a fighter flight goes (TACTICAL_AI_PLAN.md section 5.4; stage T4, "flight
 * tactics: escort, strike, intercept").
 *
 * The moves are ShipMover's: every plan it offers is one the page accepts (tests/ai/
 * shipMoverHarness.php replays them through movement.js, jinking included), and so is the jinking
 * added to the plan chosen. This class only CHOOSES among the places the flight can end this turn,
 * scoring each in named terms (principle P7), in "value removed" - expected damage times what a
 * point of the unit hit is worth (its value over the damage that finishes it, FireDecider's worth):
 *
 *   offence  what the flight's guns should do from there, in their arcs, to the units it is after,
 *            at THIS turn's Fire phase (AIView::atFireTime: a unit yet to move flies straight on);
 *   danger   what every enemy gun within range could do to the flight there - arcs not asked, the
 *            enemy may still turn (FLIGHT_DANGER_WEIGHT);
 *   closing  with nothing in reach from anywhere this turn, each hex between the flight's guns' reach
 *            and its nearest target (FLIGHT_CLOSING_PER_HEX) - while that target is within two turns;
 *   escort   with the enemy further off than that, each hex beyond FLIGHT_ESCORT_RANGE of the nearest
 *            of the bot's ships (FLIGHT_TETHER_PER_HEX): a flight with nothing to do keeps with the
 *            fleet, rather than flying into the enemy alone;
 *   terrain  a blocked hex entered, ending off the map or heading off it, as MoveDecider scores them.
 *
 * Its MISSION decides whom it is after. INTERCEPT, when an enemy flight is within
 * FLIGHT_INTERCEPT_RANGE of one of the bot's ships and the flight's guns can fire at fighters: those
 * flights. Otherwise STRIKE: every enemy unit, each as much as it is worth.
 *
 * JINKING, with the thrust the chosen plan leaves: each level is 5% off the flight's own fire and
 * off the fire of the ships aimed at it, so it jinks all it can where it stands to take more than it
 * deals, and not at all otherwise.
 *
 * NOT YET: pivots and combat pivots, docking and launching (the bot's flights start on the map),
 * Fighter Missions, flights working together, levels (stage T8).
 */
class FlightDecider
{
    //What the last orders() did, for tac_ai_log: missions given, flights jinking.
    public static $last = array('strike' => 0, 'intercept' => 0, 'escort' => 0, 'jinking' => 0);

    public static function reset()
    {
        self::$last = array('strike' => 0, 'intercept' => 0, 'escort' => 0, 'jinking' => 0);
    }

    /* The rows for one flight: a ShipMover plan, with jinking levels after it. $view is the bot's
       masked view of the Movement phase; $blocked and $bounds as MoveDecider has them. */
    public static function rows(AIView $view, ShipMover $mover, $flight, array $blocked, $bounds)
    {
        $plans = $mover->enumerate(AIConfig::FLIGHT_PLANS);
        if (count($plans) == 0) return MoveDecider::choose($mover, $blocked, $bounds);

        $mission = self::mission($view, $flight);
        $guns = self::gunGroups($view, $flight);

        //One estimate per place and facing: that is all fire and danger depend on.
        $groups = array();
        foreach ($plans as $index => $plan) $groups[$plan['position']->q . ',' . $plan['position']->r . ',' . $plan['facing']][] = $index;

        $judged = $view->atFireTime(function () use ($view, $flight, $plans, $groups, $mission, $guns, $blocked, $bounds) {
            $out = array();
            foreach ($groups as $key => $indexes) $out[$key] = self::judge($view, $flight, $plans[$indexes[0]], $mission, $guns, $blocked, $bounds);
            return $out;
        }, array((int)$flight->id));

        //Nothing in reach from anywhere: close on the target, or keep with the fleet when it is further than two turns off.
        $inReach = false;
        foreach ($judged as $j) if ($j['offence'] > 0) $inReach = true;
        $farOff = ($mission['nearest'] > 2 * self::reach($plans, $guns));
        if (!$inReach) self::$last[$farOff ? 'escort' : $mission['name']]++;
        else self::$last[$mission['name']]++;

        $best = null;
        $bestScore = null;
        foreach ($judged as $key => $j) {
            $score = $j['offence'] - AIConfig::FLIGHT_DANGER_WEIGHT * $j['danger'] - $j['hazard'];
            if (!$inReach) $score -= $farOff ? AIConfig::FLIGHT_TETHER_PER_HEX * $j['beyondFleet'] : AIConfig::FLIGHT_CLOSING_PER_HEX * $j['short'];
            if ($bestScore === null || $score > $bestScore) {
                $bestScore = $score;
                $best = $key;
            }
        }

        //The plan to that place: the plainest, or - when it will jink - the one leaving most thrust.
        $j = $judged[$best];
        $jink = $j['danger'] > 0 && $j['dangerDamage'] > $j['offenceDamage'];
        $pick = $groups[$best][0];
        if ($jink) {
            foreach ($groups[$best] as $index) {
                if ($plans[$index]['thrustLeft'] > $plans[$pick]['thrustLeft']) $pick = $index;
            }
        }

        $plotted = clone $mover;
        foreach ($plans[$pick]['actions'] as $action) {
            if (!$plotted->apply($action)) return MoveDecider::choose($mover, $blocked, $bounds); //cannot happen: the plan was found this way
        }
        if (!$plotted->isComplete()) return MoveDecider::choose($mover, $blocked, $bounds);
        if ($jink && $plotted->jinkLevels($plotted->jinkRoom()) > 0) self::$last['jinking']++;

        return $plotted->rows();
    }

    /* Whom the flight is after: array('name', 'targets' => enemy units, 'nearest' => hexes to the
       nearest of them now). */
    private static function mission(AIView $view, $flight)
    {
        $enemies = $view->enemies();
        $antiFighter = false;
        foreach ($view->mounts($flight) as $mount) {
            if ($view->plainGun($mount['weapon'], $mount['fighter']) && isset($mount['weapon']->fireControl[0])) $antiFighter = true;
        }

        $raiders = array();
        if ($antiFighter) {
            foreach ($enemies as $enemy) {
                if (!($enemy instanceof FighterFlight)) continue;
                foreach ($view->myUnits() as $mine) {
                    if ($mine instanceof FighterFlight) continue;
                    if ($enemy->getHexPos()->distanceTo($mine->getHexPos()) <= AIConfig::FLIGHT_INTERCEPT_RANGE) {
                        $raiders[] = $enemy;
                        break;
                    }
                }
            }
        }

        $name = count($raiders) > 0 ? 'intercept' : 'strike';
        $targets = count($raiders) > 0 ? $raiders : $enemies;
        $nearest = PHP_INT_MAX;
        foreach ($targets as $target) $nearest = min($nearest, $flight->getHexPos()->distanceTo($target->getHexPos()));
        return array('name' => $name, 'targets' => $targets, 'nearest' => $nearest);
    }

    /* The flight's guns that will fire this turn, by kind: a flight's fighters carry the same guns,
       so one of each is asked and counted for all. array(array('weapon', 'count')). */
    private static function gunGroups(AIView $view, $flight)
    {
        $groups = array();
        foreach ($view->mounts($flight) as $mount) {
            $weapon = $mount['weapon'];
            if (!$view->plainGun($weapon, $mount['fighter']) || $weapon->ballistic) continue;
            if ($weapon->isOfflineOnTurn($view->turn) || !$view->loaded($weapon)) continue;
            $key = get_class($weapon) . ':' . $weapon->firingMode;
            if (!isset($groups[$key])) $groups[$key] = array('weapon' => $weapon, 'count' => 0);
            $groups[$key]['count']++;
        }
        return array_values($groups);
    }

    //How far the flight can bring its guns this turn: its fastest plan's hexes and its longest gun.
    private static function reach(array $plans, array $guns)
    {
        $speed = 0;
        foreach ($plans as $plan) $speed = max($speed, (int)$plan['speed']);
        $range = 0;
        foreach ($guns as $gun) $range = max($range, (float)$gun['weapon']->range);
        return $speed + $range;
    }

    //Everything one end state is scored by. The flight is put there for the asking, and taken back.
    private static function judge(AIView $view, $flight, array $plan, array $mission, array $guns, array $blocked, $bounds)
    {
        $flight->movement[] = new MovementOrder(-1, 'move', $plan['position'], 0, 0, $plan['speed'], $plan['heading'], $plan['facing'], false, $view->turn, 0, 0);
        try {
            //offence: each kind of gun at the target it does most to
            $offence = 0;
            $offenceDamage = 0;
            $short = PHP_INT_MAX;
            foreach ($guns as $gun) {
                $best = 0;
                $bestDamage = 0;
                foreach ($mission['targets'] as $target) {
                    $distance = $flight->getHexPos()->distanceTo($target->getHexPos());
                    $short = min($short, max(0, $distance - (float)$gun['weapon']->range));
                    $damage = $view->estimate($flight, $gun['weapon'], $target, true) * $gun['count'];
                    $worth = $damage * $view->value($target) / $view->healthLeft($target);
                    if ($worth > $best) {
                        $best = $worth;
                        $bestDamage = $damage;
                    }
                }
                $offence += $best;
                $offenceDamage += $bestDamage;
            }
            if (count($guns) == 0) {
                foreach ($mission['targets'] as $target) $short = min($short, $flight->getHexPos()->distanceTo($target->getHexPos()));
            }

            //danger: every enemy gun, by kind, that can reach the flight there
            $dangerDamage = 0;
            foreach ($view->enemies() as $enemy) {
                $kinds = array();
                foreach ($view->mounts($enemy) as $mount) {
                    if (!$view->plainGun($mount['weapon'], $mount['fighter'])) continue;
                    $key = get_class($mount['weapon']) . ':' . $mount['weapon']->firingMode;
                    if (!isset($kinds[$key])) $kinds[$key] = array($mount['weapon'], 0);
                    $kinds[$key][1]++;
                }
                foreach ($kinds as $kind) $dangerDamage += $view->estimate($enemy, $kind[0], $flight, false) * $kind[1];
            }
            $danger = $dangerDamage * $view->value($flight) / $view->healthLeft($flight);

            //escort: how far beyond the fleet
            $fleet = PHP_INT_MAX;
            foreach ($view->myUnits() as $mine) {
                if ($mine instanceof FighterFlight || (int)$mine->id === (int)$flight->id) continue;
                $fleet = min($fleet, $flight->getHexPos()->distanceTo($mine->getHexPos()));
            }
            $beyondFleet = ($fleet === PHP_INT_MAX) ? 0 : max(0, $fleet - AIConfig::FLIGHT_ESCORT_RANGE);
        } finally {
            array_pop($flight->movement);
        }

        return array('offence' => $offence, 'offenceDamage' => $offenceDamage, 'danger' => $danger, 'dangerDamage' => $dangerDamage,
            'short' => ($short === PHP_INT_MAX) ? 0 : $short, 'beyondFleet' => $beyondFleet,
            'hazard' => MoveDecider::hazard($plan['path'], $plan['position'], $plan['heading'], $plan['speed'], $blocked, $bounds));
    }
}
