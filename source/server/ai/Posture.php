<?php
/**
 * CPU opponent - can the bot's side still fight, and does it give up (TACTICAL_AI_PLAN.md section 10.8, stage T6b).
 *
 * The user's rulings of 2026-11-21: no fleet-wide withdrawal - "most ships fight until they are destroyed or the game is won" - and
 * a side SURRENDERS only when it can't fight: "when no CPU unit can damage any enemy (no usable weapon, or nothing can get within
 * reach) for 2 turns in a row, while it is behind on fleet value. Never on turns 1-2." (Self-play kept running to the turn cap with
 * one side down to units that could hurt nobody - plan 10.7, T5a and T5c records.)
 *
 * "Can damage" is asked as Hard's power management asks "can this gun fire this turn" (PowerDecider::reaches): each unit and each
 * visible enemy as close as both could come this turn - speed plus all the acceleration thrust buys, both ways, and a little slack
 * for what cannot be seen - and the best case for the shooter: every EW point of its ship locked on, no DEW against it. A gun counts
 * whatever its loading (a reloading gun fights again soon), but not one destroyed, forced off, on a destroyed craft, or out of
 * ammunition; a launcher counts with a round it can fire and the target in its launch range. Jump engines and rams are not guns.
 *
 * Decided in Initial Orders (TacticalAI::initialOrders sets $last, TacticalAI::play keeps the count of turns in tac_ai_state and
 * submits the surrender through the ordinary path). This class reads no database: the harnesses ask it directly.
 */
class Posture
{
    /* The last assessment (assess()), for TacticalAI::play: array('fight' => can any unit damage an enemy, 'able' => how many can,
       'units' => how many units the side has on the board, 'seen' => visible enemies, 'own' / 'enemy' => fleet value). */
    public static $last = null;

    /* The side's assessment, on the bot's masked view. Mines are not counted either way: they fire by themselves and decide
       nothing. */
    public static function assess(AIView $view)
    {
        $enemies = array();
        $enemyValue = 0;
        foreach ($view->enemies() as $enemy) {
            if (!empty($enemy->mine)) continue;
            $enemies[] = $enemy;
            $enemyValue += $view->value($enemy);
        }

        $own = 0;
        $able = 0;
        $units = 0;
        foreach ($view->myUnits() as $unit) {
            if (!empty($unit->mine)) continue;
            $units++;
            $own += $view->value($unit);
            if (count($enemies) > 0 && self::canFight($view, $unit, $enemies)) $able++;
        }

        return self::$last = array('fight' => $able > 0, 'able' => $able, 'units' => $units, 'seen' => count($enemies), 'own' => $own, 'enemy' => $enemyValue);
    }

    /* Should the side surrender now? $assessment from assess(); $run = how many turns in a row, ending LAST turn, it could not fight.
       The ruling: this is the second such turn (or later), it is behind on value, and it is turn 3 or later. A turn with no enemy in
       sight says nothing either way (a cloaked enemy is not a beaten one) - see run(). */
    public static function surrender(array $assessment, $turn, $run)
    {
        if ((int)$turn < AIConfig::SURRENDER_EARLIEST_TURN) return false;
        if ($assessment['seen'] == 0 || $assessment['fight']) return false;
        if ($assessment['own'] >= $assessment['enemy']) return false;
        return $run + 1 >= AIConfig::SURRENDER_TURNS_UNABLE;
    }

    /* The count after this turn: one more turn the side could not fight, or none - unchanged by a turn with no enemy in sight. */
    public static function run(array $assessment, $run)
    {
        if ($assessment['seen'] == 0) return (int)$run;
        return $assessment['fight'] ? 0 : (int)$run + 1;
    }

    //One line for tac_ai_log.
    public static function describe(array $assessment)
    {
        return sprintf('%d of %d units can damage an enemy; fleet value %.0f against %.0f', $assessment['able'], $assessment['units'], $assessment['own'], $assessment['enemy']);
    }

    /* Can $unit damage any of $enemies this turn - "no usable weapon, or nothing can get within reach"? See the header.
       $turns (stage T9, M2 - the user's ruling of 2026-11-24): the reach over that many turns, both flying their hardest each turn - JumpDecider's
       "cannot get back into the fight" asks AIConfig::JUMP_REACH_TURNS; surrender, the last stand and the rest ask this turn's. */
    public static function canFight(AIView $view, $unit, array $enemies, $turns = 1)
    {
        if ($unit->isDestroyed()) return false;
        if (!($unit instanceof FighterFlight) && $unit->isDisabled()) return false;

        $guns = self::usableGuns($view, $unit);
        if (count($guns) == 0) return self::canRam($unit, $enemies, $turns);

        $turn = $view->turn;
        $points = ($unit instanceof FighterFlight) ? 0 : EWDecider::points($unit, $turn);
        foreach ($enemies as $enemy) {
            $steps = max(1, (int)$turns) * (PowerDecider::maxMove($unit) + PowerDecider::maxMove($enemy)) + AIConfig::POWER_REACH_SLACK;
            $found = PowerDecider::nearer($unit, $enemy, $steps, $turn, function () use ($view, $unit, $enemy, $guns, $points, $turn) {
                $kept = $unit->EW;
                if ($points > 0) $unit->EW[] = new EWentry(-1, $unit->id, $turn, 'OEW', $points, $enemy->id);
                try {
                    $distance = $unit->getHexPos()->distanceTo($enemy->getHexPos());
                    foreach ($guns as $gun) {
                        if ($gun->ballistic) {
                            if (self::launchable($gun, $enemy, $distance)) return true;
                            continue;
                        }
                        if ($view->estimate($unit, $gun, $enemy, false) > 0) return true;
                    }
                    return false;
                } finally {
                    $unit->EW = $kept;
                }
            });
            if ($found) return true;
        }
        return false;
    }

    /* Stage T9, M2: a flight DESIGNED to ram (the Hunter-Killers - its rams are its only weapons) can fight while a live craft still
       carries its Ramming Attack and an enemy could be in its hex this turn, both closing as hard as they can (the reach above). An
       Uncontrolled one counts too: the server flies it at the enemy and rams for it. */
    private static function canRam($unit, array $enemies, $turns = 1)
    {
        if (!($unit instanceof FighterFlight)) return false;
        $rams = false;
        foreach ($unit->systems as $fighter) {
            if (!($fighter instanceof Fighter) || $fighter->isDestroyed()) continue;
            foreach ($fighter->systems as $system) {
                if ($system instanceof RammingAttack && $system->designedToRam && !$system->isDestroyed()) $rams = true;
            }
        }
        if (!$rams) return false;
        foreach ($enemies as $enemy) {
            $steps = max(1, (int)$turns) * (PowerDecider::maxMove($unit) + PowerDecider::maxMove($enemy)) + AIConfig::POWER_REACH_SLACK;
            if ($unit->getHexPos()->distanceTo($enemy->getHexPos()) <= $steps) return true;
        }
        return false;
    }

    /* The weapons of $unit that could still do damage some turn: not destroyed (nor their craft), not forced off by the game
       (a gun the bot itself switches off for a power deficit is still a gun), with ammunition left; no jump engine, ram, support
       or effect-only weapon, nor one the bot never fires (a hex-targeted one). */
    public static function usableGuns(AIView $view, $unit)
    {
        $turn = $view->turn;
        $guns = array();
        foreach ($view->mounts($unit) as $mount) {
            $gun = $mount['weapon'];
            if ($gun->isDestroyed() || ($mount['fighter'] !== null && $mount['fighter']->isDestroyed())) continue;
            if ($gun instanceof JumpEngine || $gun->isRammingAttack || $gun->hextarget || $gun->weaponClass === 'Support') continue;
            if (PowerDecider::forcedOff($gun, $turn)) continue;
            //stage T9: a kind the bot never fires (AIView::NEVER_FIRED) does no damage for it - a unit left with only such guns cannot fight
            if (!$view->firesKind($unit, $gun, $mount['fighter'])) continue;
            if ($gun instanceof AmmoMissileRackS || $gun instanceof MissileLauncher) {
                //no plain round left in its magazine; an old rack of a flight with no round left (stage T9, E2 - AIView::modes)
                if (count($view->modes($gun)) == 0) continue;
            } else {
                if ($gun->maxDamage <= 0) continue;
                //no fire control for any size of unit: a pure interceptor (an Interceptor Mk II) never damages anything
                $fc = is_array($gun->fireControl) ? $gun->fireControl : array();
                if (!isset($fc[0]) && !isset($fc[1]) && !isset($fc[2])) continue;
                $vars = get_object_vars($gun);
                if (array_key_exists('ammunition', $vars) && $vars['ammunition'] !== null && (int)$vars['ammunition'] <= 0) continue;
            }
            $guns[] = $gun;
        }
        return $guns;
    }

    // ------------------------------------------------------------------ stage T6d: last-resort ramming

    /* The ship's RammingAttack when it may and should ram this turn, else null - the user's ruling of 2026-11-21 (built at stage T8):
       "RAM (Desperate rule teams, Normal and Hard; Easy never) when it has no usable weapon OR its primary structure is at or below
       25% - and only at a target it can reach this turn" (the target is ShipDecider's and FireDecider's to find). The Desperate rule is
       honour-based - nothing in the engine stops a ram - so this is the bot keeping it. A SHIP only (a ship cannot ram a fighter, and a
       flight's ram is not built); not a base, OSAT, mine or terrain, whose ram is there "for technical purposes" (autoFireOnly). */
    public static function rammer(AIView $view, $unit)
    {
        if ($unit instanceof FighterFlight || !empty($unit->base) || !empty($unit->smallBase) || !empty($unit->osat) || !empty($unit->mine)) return null;
        if ($unit->isTerrain() || $unit->isDestroyed() || $unit->isDisabled()) return null;

        $ram = null;
        foreach ($unit->systems as $system) {
            if ($system instanceof RammingAttack && !$system->isDestroyed() && !$system->autoFireOnly) {
                $ram = $system;
                break;
            }
        }
        if ($ram === null) return null;
        //the page's own tests before it takes an order (weaponManager.targetShip): loaded, not switched off (stage T9 - found by the fire pair)
        if (!$view->loaded($ram) || $ram->isOfflineOnTurn($view->turn)) return null;
        /* Stage T9 (the user, 2026-11-23): a unit DESIGNED to ram (RammingAttack::$designedToRam - the Dilgar Delegor; the Orieni
           Hunter-Killers, a flight, when the bot flies them) "can and should always Ram, and can ignore the normal conditions for making
           a ramming attack" - at every level, the Desperate rule or not, armed and whole. */
        if ($ram->designedToRam) return $ram;

        if (!$view->skill('ram')) return null;
        if (!self::desperate($view->gamedata, $unit->team)) return null;

        if (count(self::usableGuns($view, $unit)) == 0) return $ram;
        $primary = $unit->getStructureSystem(0);
        if ($primary && $primary->getRemainingHealth() <= AIConfig::RAM_PRIMARY_SHARE * (int)$primary->maxhealth) return $ram;
        return null;
    }

    //Does the game's Desperate rule cover $team? Its value is one team, or -1 for every team (Create Game's Desperate Rules box).
    public static function desperate(TacGamedata $gamedata, $team)
    {
        $rule = $gamedata->rules->getRuleByName('desperate');
        if ($rule === null) return false;
        $teams = (int)$rule->jsonSerialize();
        return $teams === -1 || $teams === (int)$team;
    }

    /* What ramming $target from where $unit now stands should be worth, in value (stage T6d): the ram's own expected damage (AIView -
       the engine's ram hit chance, the weapon's damage range) no further than what finishes the target, times what a point of it is
       worth, less what the ram does back to $unit (the target's ramming factor, on a hit) times $unit's own worth and the danger weight.
       0 when there is no ram to be had (a fighter, another hex, no chance). $worth: array(target health, target worth, own health,
       own worth, danger weight). */
    public static function ramValue(AIView $view, $unit, RammingAttack $ram, $target, array $worth)
    {
        if ($target instanceof FighterFlight) return 0;
        if ($unit->getHexPos()->distanceTo($target->getHexPos()) != 0) return 0;
        $chance = $view->chanceOf($unit, $ram, $target, false) / 100;
        if ($chance <= 0) return 0;
        $dealt = min($view->estimate($unit, $ram, $target, false), $worth[0]) * $worth[1];
        $back = $target->getSystemByName('RammingAttack');
        $factor = $back ? $back->getBaseRammingFactor() : $target->getRammingFactor();
        $taken = min($chance * 0.625 * $factor, $worth[2]) * $worth[3] * $worth[4]; //(0.625: the mean of a ram's quarter-to-whole factor, as its damage range)
        return $dealt - $taken;
    }

    // ------------------------------------------------------------------ stage T9, M2: flights designed to ram (the Orieni Hunter-Killers)

    /* The craft of a flight DESIGNED to ram that can ram this turn: list of array('fighter' => Fighter, 'ram' => RammingAttack) - every
       live craft whose Ramming Attack is designedToRam, loaded and switched on (weaponManager.targetShip's own tests). Empty for any other
       unit, and for a flight the server flies this turn (Uncontrolled - AutomatedMovement plots its seek and declares its rams, and
       Firing::validateFireOrders refuses a player's). Each craft rams with its own Ramming Attack, one order each, as the page orders a
       flight's ram. */
    public static function flightRams(AIView $view, $unit)
    {
        if (!($unit instanceof FighterFlight) || $unit->isDestroyed()) return array();
        if (!empty($unit->remoteControl) && AutomatedMovement::isUnderAutomatedControl($unit, $view->gamedata)) return array();
        $out = array();
        foreach ($unit->systems as $fighter) {
            if (!($fighter instanceof Fighter) || $fighter->isDestroyed()) continue;
            foreach ($fighter->systems as $system) {
                if (!($system instanceof RammingAttack) || !$system->designedToRam || $system->autoFireOnly || $system->isDestroyed()) continue;
                if (!$view->loaded($system) || $system->isOfflineOnTurn($view->turn)) continue;
                $out[] = array('fighter' => $fighter, 'ram' => $system);
                break;
            }
        }
        return $out;
    }

    /* What a flight designed to ram goes for, among $enemies: the user's rulings - "they will usually target larger ships in enemy fleet
       due to their high damage potential, working their way down to HCV and MCV if no larger ships are available" (2026-11-23), on down
       to LCVs and, once the enemy has no ship left, its fighter flights ("Anything, fighters too", 2026-11-24). "Available" is read as
       within reach: the largest size class (Enormous, capital, HCV, MCV, LCV) with a unit the flight could reach within
       AIConfig::RAM_QUARRY_TURNS turns (both flying their hardest - PowerDecider::maxMove), or the largest there is when none is that
       near. The units of that class; mines and terrain are never quarry. */
    public static function ramQuarry($flight, array $enemies)
    {
        $ships = array();
        $flights = array();
        foreach ($enemies as $enemy) {
            if (!empty($enemy->mine) || $enemy->isTerrain()) continue;
            if ($enemy instanceof FighterFlight) $flights[] = $enemy;
            else $ships[] = $enemy;
        }
        if (count($ships) == 0) return $flights;

        $own = PowerDecider::maxMove($flight);
        $classes = array();
        foreach ($ships as $ship) {
            $class = self::ramClass($ship);
            $near = $flight->getHexPos()->distanceTo($ship->getHexPos()) <= AIConfig::RAM_QUARRY_TURNS * $own + PowerDecider::maxMove($ship);
            if (!isset($classes[$class])) $classes[$class] = array('near' => false, 'units' => array());
            $classes[$class]['units'][] = $ship;
            if ($near) $classes[$class]['near'] = true;
        }
        krsort($classes);
        foreach ($classes as $row) {
            if ($row['near']) return $row['units'];
        }
        return reset($classes)['units'];
    }

    /* A ship's size class for ramQuarry: an Enormous unit above a capital ship, then shipSizeClass (capital 3, HCV 2, MCV 1) - and an LCV
       below an MCV: FV's LCVs are medium ships to the engine (class LCV extends MediumShip, shipSizeClass 1; hangarRequired 'LCVs'). */
    public static function ramClass($ship)
    {
        if (!empty($ship->Enormous)) return 4;
        if ($ship instanceof LCV || (string)($ship->hangarRequired ?? '') === 'LCVs') return 0;
        return (int)$ship->shipSizeClass;
    }

    /* What $count craft of a flight designed to ram, ramming $target from where the flight stands now, should be worth: each craft's
       expected ram damage (AIView - the engine's ram chance, which counts the flight's own speed and jinking as the Hunter-Killers'
       "HK Targeting" penalty, and the target's speed), no more in all than finishes it ($health), times what a point of it is worth
       ($worth). Nothing for what comes back: a Hunter-Killer is spent by its ram, which is what it is for. 0 from another hex. */
    public static function flightRamValue(AIView $view, $flight, RammingAttack $ram, $target, $count, $health, $worth)
    {
        if ($flight->getHexPos()->distanceTo($target->getHexPos()) != 0 || $count <= 0) return 0;
        $each = $view->estimate($flight, $ram, $target, false);
        if ($each <= 0) return 0;
        return min($each * $count, (float)$health) * (float)$worth;
    }

    // ------------------------------------------------------------------ stage T6g: a side down to its fighters

    /* The flights that must get a shot off this turn (AIConfig::FLIGHT_MAX_IDLE_TURNS): unit id => true. TacticalAI::play sets it
       from tac_ai_state before the deciders run (mustShoot()); FlightDecider and FireDecider act on it only while the side is down
       to its fighters (lastStand()). */
    public static $mustShoot = array();

    /* Is the bot down to its fighters - "the only unit left": every unit of it that can still damage an enemy (canFight) a fighter
       flight, and one at least? A ship that can no longer fight does not count against it - in game 5749 the losing side's Omega,
       its guns useless 190 hexes off the map, was still on the board while its Thunderbolts hid. Asked by FlightDecider for each
       flight and by FireDecider: once a view (a WeakMap - a view's object id is reused once it is gone). */
    private static $stands = null;

    public static function lastStand(AIView $view)
    {
        if (self::$stands === null) self::$stands = new WeakMap();
        if (isset(self::$stands[$view])) return self::$stands[$view];

        $enemies = array();
        foreach ($view->enemies() as $enemy) {
            if (empty($enemy->mine)) $enemies[] = $enemy;
        }
        $flights = false;
        $ships = false;
        foreach ($view->myUnits() as $unit) {
            if (!empty($unit->mine)) continue;
            if ($unit instanceof FighterFlight) $flights = true;
            else if (!$ships && self::canFight($view, $unit, $enemies)) $ships = true;
        }
        return self::$stands[$view] = ($flights && !$ships);
    }

    //The ids of the units with an order to fire in $entries - a gun's or a launcher's, on a ship or on one of a flight's fighters.
    public static function firing(array $entries)
    {
        $ids = array();
        foreach ($entries as $entry) {
            if (self::hasFireOrder(isset($entry['systems']) ? $entry['systems'] : array())) $ids[] = (int)$entry['id'];
        }
        return $ids;
    }

    private static function hasFireOrder(array $systems)
    {
        foreach ($systems as $system) {
            if (!empty($system['fireOrders'])) return true;
            if (!empty($system['systems']) && self::hasFireOrder($system['systems'])) return true;
        }
        return false;
    }

    /* The turn each of the bot's flights last fired (tac_ai_state 'shots'), brought up to date: $flights the ids of its flights on
       the board now (one gone is forgotten), $firing the units firing in this phase's orders. A flight not seen before counts from
       the turn before this one - its turns without a shot start now. */
    public static function shots(array $shots, array $flights, array $firing, $turn)
    {
        $out = array();
        foreach ($flights as $id) $out[(int)$id] = isset($shots[$id]) ? (int)$shots[$id] : (int)$turn - 1;
        foreach ($firing as $id) {
            if (isset($out[(int)$id])) $out[(int)$id] = (int)$turn;
        }
        return $out;
    }

    /* The flights that have fired nothing for FLIGHT_MAX_IDLE_TURNS - 1 turns: this turn must not be one more ("can't go three
       turns without firing a shot"). */
    public static function mustShoot(array $shots, $turn)
    {
        $out = array();
        foreach ($shots as $id => $last) {
            if ((int)$turn - (int)$last >= AIConfig::FLIGHT_MAX_IDLE_TURNS) $out[(int)$id] = true;
        }
        return $out;
    }

    //Could $launcher launch at $target from $distance (fire control for its size, the launch range - none is any)?
    private static function launchable(Weapon $launcher, $target, $distance)
    {
        $fc = $launcher->fireControl;
        $index = ($target instanceof FighterFlight) ? 0 : (($target->shipSizeClass < 2) ? 1 : 2);
        if (!isset($fc[$index])) return false;
        return (int)$launcher->range == 0 || $distance <= (int)$launcher->range;
    }
}
