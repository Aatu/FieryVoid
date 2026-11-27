<?php
/**
 * CPU opponent - ADAPTIVE ARMOUR, in Initial Orders (stage T9 - TACTICAL_AI_PLAN.md 10.10 "Missing systems", M13).
 *
 * The user's ruling (2026-11-23): "Pre-allocated points generally go to the most numerous weapon class in the enemy fleet (Ramming
 * Attacks ignored); as the ship is hit, the rest go where they can, up to the maximum."
 *
 * HOW A PLAYER SETS IT - the page is the judge (baseSystems.js AdaptiveArmorController; the server's Initial Orders handler,
 * AdaptiveArmorController::generateIndividualNotes, only clamps). The controller knows a list of damage classes (availableAA): the
 * enemy's weapon classes, marked in Deployment, and any class that has since hurt the unit - each hit that gets through unlocks one more
 * point of its class, up to the per-class maximum (AApertype). A click on a class adds a point (doIncrease) while the total is under
 * AAtotal, the class under AApertype, and either a PRE-ALLOCATED point is left - which unlocks the point as it sets it - or the class has
 * an unlocked point not yet set (canIncrease). Points set stay set. The page posts one class name per point added
 * (doIndividualNotesTransfer) on the controller.
 *
 * WHAT THE BOT DOES, for every controller of its own (a ship's, or each craft's):
 *   1. turn 1 only - the one turn the server unlocks a pre-allocated point as it is set (it adds availability on `turn == 1`; the page
 *      offers the points on a later-deploying unit's arrival turn too, which the server's clamp would then take away) - each
 *      pre-allocated point to the class with the most live enemy weapon mounts that can still take one;
 *   2. every turn, every unlocked point not yet set, the classes with the most live enemy weapon mounts first, while the total allows.
 * A class no live enemy weapon carries takes nothing (the page leaves it off the panel too - getRelevantArmorTypes); nor does Ramming
 * (the ruling) or Boarding (the controller never lists it). A mount counts once for each class it can fire as (weaponClass and
 * weaponClassArray - what markWpnDmgClass marks); a flight's mounts craft by craft.
 * The White Star (the Minbari's two hulls) has no pre-allocated points: only step 2 applies to it.
 */
class AdaptiveArmourDecider
{
    //What the last plan did, for tac_ai_log: controllers given points, and the points.
    public static $last = array('controllers' => 0, 'points' => 0);

    /* The bot's points this Initial Orders: unit id => the entry's 'systems' rows to add, shaped as BallisticDecider::systems shapes
       them - a ship's controller array('id', 'individualNotesTransfer' => class names, one per point), a craft's nested under the craft:
       craft id => array('id' => craft id, 'systems' => array(array('id', 'individualNotesTransfer'))). */
    public static function plan(AIView $view)
    {
        self::$last = array('controllers' => 0, 'points' => 0);
        $out = array();
        $ranking = null; //the enemy's classes, most mounts first - counted once, on the first controller
        foreach ($view->myUnits() as $unit) {
            foreach (self::controllers($unit) as $pair) {
                list($controller, $craft) = $pair;
                if ($ranking === null) $ranking = self::ranking($view);
                $points = self::points($controller, $ranking, (int)$view->turn);
                if (count($points) == 0) continue;
                self::$last['controllers']++;
                self::$last['points'] += count($points);
                $row = array('id' => (int)$controller->id, 'individualNotesTransfer' => $points);
                $unitId = (int)$unit->id;
                if ($craft === null) {
                    $out[$unitId][(int)$controller->id] = $row;
                } else {
                    $craftId = (int)$craft->id;
                    if (!isset($out[$unitId][$craftId])) $out[$unitId][$craftId] = array('id' => $craftId, 'systems' => array());
                    $out[$unitId][$craftId]['systems'][] = $row;
                }
            }
        }
        return $out;
    }

    //A unit's controllers: array(controller, craft or null) - a ship's among its systems, a flight's one per live craft.
    public static function controllers($unit)
    {
        $found = array();
        if ($unit instanceof FighterFlight) {
            foreach ($unit->systems as $craft) {
                if (!($craft instanceof Fighter) || $craft->isDestroyed()) continue;
                foreach ($craft->systems as $system) {
                    if ($system instanceof AdaptiveArmorController) $found[] = array($system, $craft);
                }
            }
        } else {
            foreach ($unit->systems as $system) {
                if ($system instanceof AdaptiveArmorController) $found[] = array($system, null);
            }
        }
        return $found;
    }

    /* The enemy's damage classes, most live weapon mounts first (a tie by name, so a decision never depends on the order units load):
       class => mounts. Ramming and Boarding left out; a jump engine is no weapon here. */
    public static function ranking(AIView $view)
    {
        $counts = array();
        foreach ($view->enemies() as $enemy) {
            foreach ($view->mounts($enemy) as $mount) {
                $weapon = $mount['weapon'];
                if ($weapon->isDestroyed() || ($mount['fighter'] !== null && $mount['fighter']->isDestroyed())) continue;
                if ($weapon->isRammingAttack || $weapon instanceof JumpEngine) continue;
                foreach (self::classesOf($weapon) as $class) $counts[$class] = isset($counts[$class]) ? $counts[$class] + 1 : 1;
            }
        }
        uksort($counts, function ($a, $b) use ($counts) { return ($counts[$b] <=> $counts[$a]) ?: strcmp($a, $b); });
        return $counts;
    }

    //The classes a mount can do damage as (AdaptiveArmorController::markWpnDmgClass: weaponClassArray and weaponClass), each once.
    public static function classesOf(Weapon $weapon)
    {
        $classes = array();
        foreach (array_merge(array_values((array)$weapon->weaponClassArray), array($weapon->weaponClass)) as $class) {
            $class = (string)$class;
            if ($class === '' || $class === 'Boarding' || $class === 'Ramming') continue;
            $classes[$class] = true;
        }
        return array_keys($classes);
    }

    /* The points one controller takes this turn, in the order a player would click them: the class names, one per point. The page's
       canIncrease before each, on the controller's own state (availableAA, allocatedAA, AAtotal_used) - it is not changed here. */
    public static function points(AdaptiveArmorController $controller, array $ranking, $turn)
    {
        $allocated = (array)$controller->allocatedAA;
        $available = (array)$controller->availableAA;
        $used = 0;
        foreach ($allocated as $n) $used += (int)$n;
        $total = (int)$controller->AAtotal;
        $perType = (int)$controller->AApertype;
        /* The page's pre-allocated points left: on the turn the unit arrives it counts every point set so far against them
           (initializationUpdate: AApreallocated_used = AAtotal_used); the bot spends them on turn 1 alone (above). */
        $preLeft = ($turn == 1) ? max(0, (int)$controller->AApreallocated - $used) : 0;

        //only the classes the controller lists (the page selects among availableAA's keys) that a live enemy weapon carries
        $classes = array();
        foreach ($ranking as $class => $mounts) {
            if ($mounts > 0 && array_key_exists($class, $available)) $classes[] = $class;
        }

        $points = array();
        //1. the pre-allocated points: the most numerous class first, up to its maximum, then the next
        foreach ($classes as $class) {
            while ($preLeft > 0 && $used < $total && (int)($allocated[$class] ?? 0) < $perType) {
                $allocated[$class] = (int)($allocated[$class] ?? 0) + 1;
                $available[$class] = (int)$available[$class] + 1; //doIncrease: a pre-allocated point unlocks the point it sets
                $preLeft--;
                $used++;
                $points[] = $class;
            }
        }
        //2. the points the hits have unlocked, the most numerous class first
        foreach ($classes as $class) {
            while ($used < $total && (int)($allocated[$class] ?? 0) < min($perType, (int)$available[$class])) {
                $allocated[$class] = (int)($allocated[$class] ?? 0) + 1;
                $used++;
                $points[] = $class;
            }
        }
        return $points;
    }
}
