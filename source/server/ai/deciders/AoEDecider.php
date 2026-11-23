<?php
/**
 * CPU opponent - AREA-EFFECT weapons, in Initial Orders (stage T9 - TACTICAL_AI_PLAN.md 10.10, the first family of enabler E5).
 *
 * The user, 2026-11-23: "AOE weapons like Narn energy mines can probably use the same movement predictions as we have already for CPU
 * players, e.g. choose a target (usually a fighter flight for Energy Mines) and take a guess where they will move, then target that area."
 *
 * An Energy Mine is launched at a HEX in Initial Orders, before anybody moves, and goes off in the Fire phase (AoE::fire): three times in
 * four it lands on that hex (15% it strays up to six hexes, 10% it fizzles) and does its full damage (maxDamage, 30) to every unit there -
 * to each craft of a flight separately - and minDamage (10) to every unit in the six hexes round it. The bot's own units included.
 *
 * THE GUESS starts from the one Initial Orders makes everywhere (EWDecider, BallisticDecider): every unit flies straight on
 * (AIView::straightOn). Where an enemy ENDS is spread round that hex (AIConfig::AOE_SPREAD): on it some of the time, on one of the six next
 * to it less, on one of the twelve two off less again, and further off the rest (where no mine aimed here reaches it).
 *
 * THE MINES (the user, 2026-11-23: "Better to space out the mines a little within 1 or 2 hexes of each other to cover more space. Even the
 * 10 damage from an indirect hit is lethal to fighters."): a mine at a hex does its full damage to an enemy ending there and its least to
 * one ending next to it. What a mine is worth is what it ADDS - over the mines already planned, an end the mines cover already counts once,
 * at the most damage they do there - summed over every end of every enemy by its odds, in value (the target's worth a box, as FireDecider
 * counts it), times AOE_LANDS. Greedy: the best (mine, hex) left - every hex within two of an enemy's guess a candidate, clear of the bot's
 * own units (AOE_OWN_MARGIN, where they stand or flying straight on), one the mine can be launched at - then the next, valued against the
 * first. A second mine at a flight therefore goes next to the first - covering the hexes it would end in if it turns - rather than on it,
 * and only while that is worth more than a hex of another enemy's.
 *
 * LEGALITY IS THE PAGE'S (weaponManager.targetHex): AIView::hexShot is the port; tests/ai/fireOracle.js clicks every planned hex through it.
 * NOT YET: the level forecast for ships (EnemyForecast is a Movement decision).
 */
class AoEDecider
{
    //What the last plan did, for tac_ai_log.
    public static $last = array('launches' => 0, 'targets' => 0);

    //Is $weapon an area-effect weapon the bot aims (AIConfig::AOE_WEAPONS, subclasses included)?
    public static function isAoe(Weapon $weapon)
    {
        foreach (AIConfig::AOE_WEAPONS as $class) {
            if ($weapon instanceof $class) return true;
        }
        return false;
    }

    /* The plan: a list of array('unit', 'weapon', 'hex', 'shot' (AIView::hexShot's answer), 'value'). $view: Initial Orders. */
    public static function plan(AIView $view)
    {
        self::$last = array('launches' => 0, 'targets' => 0);
        if (!$view->ballistics) return array();
        $turn = $view->turn;

        $mines = array();
        foreach ($view->myUnits() as $unit) {
            foreach ($view->mounts($unit) as $mount) {
                if ($mount['fighter'] === null && self::isAoe($mount['weapon'])) $mines[] = array('unit' => $unit, 'weapon' => $mount['weapon']);
            }
        }
        if (count($mines) == 0) return array();

        $guess = function ($unit) use ($turn) {
            $row = AIView::straightOn($unit, $turn);
            return ($row !== null) ? $row->position : $unit->getHexPos();
        };
        $own = array();
        foreach ($view->myUnits() as $unit) {
            $own[] = $unit->getHexPos();
            $own[] = $guess($unit);
        }
        //each enemy's ends: hex key => array(hex, odds) - its guessed hex and the two rings round it (AOE_SPREAD)
        $enemies = array();
        foreach ($view->enemies() as $enemy) {
            if (!empty($enemy->mine) || $enemy->isTerrain()) continue;
            $at = $guess($enemy);
            $spread = AIConfig::AOE_SPREAD[($enemy instanceof FighterFlight) ? 'flight' : 'ship'];
            $ends = array();
            foreach (self::within($at, 2) as $key => $hex) {
                $ring = $at->distanceTo($hex);
                $ends[$key] = array($hex, $spread[$ring] / (($ring == 0) ? 1 : 6 * $ring));
            }
            $enemies[(int)$enemy->id] = array('unit' => $enemy, 'at' => $at, 'ends' => $ends, 'covered' => array(), 'harm' => array());
        }

        //the candidates: every hex within two of an enemy's guess, clear of the bot's own units
        $candidates = array();
        foreach ($enemies as $e) {
            foreach ($e['ends'] as $key => $end) {
                if (isset($candidates[$key])) continue;
                $clear = true;
                foreach ($own as $at) {
                    if ($at->distanceTo($end[0]) <= AIConfig::AOE_OWN_MARGIN) $clear = false;
                }
                if ($clear) $candidates[$key] = $end[0];
            }
        }

        $plan = array();
        $legal = array(); //"mine index:hex key" => hexShot's answer
        while (count($mines) > 0) {
            $best = null;
            foreach ($mines as $index => $mine) {
                foreach ($candidates as $key => $hex) {
                    $value = self::adds($view, $enemies, $mine['weapon'], $hex);
                    if ($value <= 0 || ($best !== null && $value <= $best['value'])) continue;
                    if (!array_key_exists("$index:$key", $legal)) $legal["$index:$key"] = $view->hexShot($mine['unit'], $mine['weapon'], $hex);
                    if ($legal["$index:$key"] === null) continue;
                    $best = array('index' => $index, 'key' => $key, 'hex' => $hex, 'shot' => $legal["$index:$key"], 'value' => $value);
                }
            }
            if ($best === null) break;
            $mine = $mines[$best['index']];
            $plan[] = array('unit' => $mine['unit'], 'weapon' => $mine['weapon'], 'hex' => $best['hex'], 'shot' => $best['shot'], 'value' => $best['value']);
            unset($mines[$best['index']]);
            unset($candidates[$best['key']]); //no two mines at one hex
            foreach ($enemies as $id => $e) {
                foreach ($e['ends'] as $key => $end) {
                    $damage = self::damageAt($mine['weapon'], $best['hex'], $end[0]);
                    if ($damage > (isset($e['covered'][$key]) ? $e['covered'][$key] : 0)) $enemies[$id]['covered'][$key] = $damage;
                }
            }
        }

        $targets = 0;
        foreach ($enemies as $e) if (count($e['covered']) > 0) $targets++;
        self::$last = array('launches' => count($plan), 'targets' => $targets);
        return $plan;
    }

    //What a mine at $hex adds over the mines already planned ('covered'), every enemy's ends by their odds, x AOE_LANDS.
    private static function adds(AIView $view, array &$enemies, Weapon $weapon, OffsetCoordinate $hex)
    {
        $value = 0;
        foreach ($enemies as $id => $e) {
            if ($e['at']->distanceTo($hex) > 3) continue; //no end of it within one of the blast
            foreach ($e['ends'] as $key => $end) {
                $damage = self::damageAt($weapon, $hex, $end[0]);
                $already = isset($e['covered'][$key]) ? $e['covered'][$key] : 0;
                if ($damage <= $already) continue;
                $value += $end[1] * (self::harmOf($view, $enemies[$id], $damage) - self::harmOf($view, $enemies[$id], $already));
            }
        }
        return AIConfig::AOE_LANDS * $value;
    }

    //The damage a mine at $blast does to a unit ending at $hex: its full on the hex, its least next to it (AoE::fire), else none.
    private static function damageAt(Weapon $weapon, OffsetCoordinate $blast, OffsetCoordinate $hex)
    {
        $d = $blast->distanceTo($hex);
        return ($d == 0) ? (int)$weapon->maxDamage : (($d == 1) ? (int)$weapon->minDamage : 0);
    }

    //harm(), remembered per enemy and damage.
    private static function harmOf(AIView $view, array &$e, $damage)
    {
        if ($damage <= 0) return 0;
        if (!isset($e['harm'][$damage])) $e['harm'][$damage] = self::harm($view, $e['unit'], $damage);
        return $e['harm'][$damage];
    }

    //Every hex within $radius of $centre: "q,r" => OffsetCoordinate, the centre first.
    private static function within(OffsetCoordinate $centre, $radius)
    {
        $hexes = array($centre->q . ',' . $centre->r => $centre);
        $frontier = array($centre);
        for ($ring = 1; $ring <= $radius; $ring++) {
            $next = array();
            foreach ($frontier as $hex) {
                for ($direction = 0; $direction < 6; $direction++) {
                    $step = $hex->moveToDirection($direction);
                    $key = $step->q . ',' . $step->r;
                    if (isset($hexes[$key])) continue;
                    $hexes[$key] = $step;
                    $next[] = $step;
                }
            }
            $frontier = $next;
        }
        return $hexes;
    }

    /* What $damage landing on $unit should be worth (the bot's measure: boxes x the unit's worth a box, as FireDecider counts it): a flight
       takes it on EVERY craft, each to what armour leaves of it and no more than the craft has left; a ship once, to what it has left. */
    private static function harm(AIView $view, $unit, $damage)
    {
        $health = $view->healthLeft($unit);
        if ($health <= 0) return 0;
        $worth = $view->value($unit) / $health;
        if (!($unit instanceof FighterFlight)) return min($damage, $health) * $worth;

        $dealt = 0;
        foreach ($unit->systems as $craft) {
            if (!($craft instanceof Fighter) || $craft->isDestroyed()) continue;
            $armour = is_array($craft->armour) && count($craft->armour) > 0 ? array_sum($craft->armour) / count($craft->armour) : 0;
            $dealt += min(max(0, $damage - $armour), $craft->getRemainingHealth());
        }
        return min($dealt, $health) * $worth;
    }

    /* The plan as the entries' systems: unit id => weapon id => array('id', 'fireOrders') - one order a gun, as weaponManager.targetHex
       builds it (ballistic, no unit, the hex in x / y). */
    public static function systems(array $plan, $turn)
    {
        $out = array();
        foreach ($plan as $p) {
            $order = AIOrderBuilder::fire($p['unit']->id, -1, $p['weapon']->id, $turn, $p['shot']['mode'], $p['shot']['shots'], $p['shot']['damageclass'], 'ballistic');
            $order['x'] = (int)$p['hex']->q;
            $order['y'] = (int)$p['hex']->r;
            $out[(int)$p['unit']->id][(int)$p['weapon']->id] = array('id' => (int)$p['weapon']->id, 'fireOrders' => array_fill(0, $p['shot']['orders'], $order));
        }
        return $out;
    }
}
