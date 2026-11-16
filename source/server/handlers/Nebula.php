<?php

/* THE NEBULA - B5W Kitchen Sink 12.5.7, NEBULA_PLAN.md. The rule itself is NebulaRule; this is where its
   effects are worked out, so each site that applies one asks here rather than re-deriving it.

   ⭐ EVERY CALLER TESTS TacGamedata::$nebulaPresent FIRST, at the call site, and calls in here only when it
   is true. Most games have no nebula (user, 2026-11-06), and the sites below run per shot, per die, per
   hit-chance - one false static read and nothing else is all an ordinary game may pay. Nothing in this class
   checks the static again; a caller that skips the test applies the nebula to every game in the database.

   Where each effect lives (plan section 3):
     thrusters -1, gravitic shields -1 .... applyToShip(), from BaseShip::onConstructed
     fighter / shuttle thrust -1 .......... FighterFlight::getEffectiveFreeThrust (thrustPenalty)
     jinking 2 thrust a level ............. jinkCost(): AutomatedMovement; movement.js on the client
     no EW ................................ InitialOrdersGamePhase::process; ew.js on the client
     fire control, OB ..................... Weapon::calculateHitBase (fireControl, offensiveBonus);
                                            weaponManager.js mirrors them
     intercept -1 ......................... Weapon::getInterceptionMod and the meteor-defence rating
     -1 per die, pulses, plasma ........... Weapon::getFinalDamage / getDamageMod; Dice::$perDiePenalty
     missile ranges halved ................ Weapon::isInDistanceRange; Weapon::stripForJson publishes them
     jammers .............................. Jammer::getSpecialAbilityValue
     EM weapons ........................... isEmDisabled(): firing validation and interception
     jump arrival +3 ...................... JumpEngine::getExitDeviationModifier

   User rulings 2026-11-06: no EW means no lock-on, as written (D1); only the Jammer SYSTEM stops (D2);
   "missiles" are the missile racks (D3); halving fire control and offensive bonus rounds DOWN (D4 - first ruled
   up, revised after play-test game 4545). Only a negative fire control value doubles - not a called shot's penalty,
   not a Piercing shot (user, game 4545). */
class Nebula {

    /* "Advanced races ignore the effects on thrusters and jinking, but all other items apply as listed"
       (KS 12.5.14) - and only an advanced race's ship may use an EM weapon in one. Ancient and Primordial
       alike (user). ⚠️ ASK THE UNIT, NEVER THE WEAPON: a weapon's own factionAge is always 1, because only
       ship classes set it. */
    public static function isAdvanced($unit){
        return $unit !== null && (int)$unit->factionAge >= 3;
    }

    /* THE LOAD HOOK - BaseShip::onConstructed, right after the system enhancements and BEFORE the per-system
       onConstructed loop, the slot ShipClasses' Hardened Shields note reserves for output changes:
       Shield::onConstructed snapshots getOutput() into tohitPenalty / damagePenalty, so a shield cut after
       that loop would be half applied. effectCriticals() ADDS the crits' mods to outputMod (it never resets
       it), so the -1 put there first survives.

       outputMod reaches the client by itself - ShipSystem::stripForJson publishes it whenever it is not 0,
       and shipManager.systems.getOutput adds it - so the thrust panel and the shield's hit-chance term need
       no client code.

       Runs once per ship per gamedata load (each load builds its ships afresh). A FighterFlight's systems
       are its fighters - neither thrusters nor shields - so a flight passes straight through; its thrust is
       FighterFlight::getEffectiveFreeThrust's. */
    public static function applyToShip($ship){
        $advanced = self::isAdvanced($ship);
        foreach ($ship->systems as $system){
            //"All thruster ratings are reduced by 1" - every Thruster class (gravitic, mag-grav, Mindrider...).
            if ($system instanceof Thruster){
                if (!$advanced) $system->outputMod -= 1;
            } else if ($system instanceof GraviticShield){
                //KS 10.10: "shield ratings are reduced by 1 point" - an Ancient's too (only thrust and jinking are exempt).
                $system->outputMod -= 1;
            }
        }
    }

    //A fighter flight's or shuttle's thrust rating: 1 less, unless it is an advanced race's.
    public static function thrustPenalty($unit){
        return self::isAdvanced($unit) ? 0 : 1;
    }

    //"Jinking requires 2 thrust per level, not 1 thrust" - unless it is an advanced race's. A multiplier, so a
    //jinking SHIP (jinkinglimit > 0, which pays its pivot cost a level) pays double too.
    public static function jinkCostMultiplier($unit){
        return self::isAdvanced($unit) ? 1 : 2;
    }

    /* "The fire control bonuses of all weapons ... are cut in half, and fire control penalties are doubled."
       A bonus halves rounding DOWN (user ruling D4, revised after play-test game 4545: +3 -> +1, +1 -> 0); a
       penalty doubles; 0 stays 0, and null - "cannot fire at this size of target" - stays null.
       ⚠️ MIRROR PAIR with nebula.fireControl (JS). */
    public static function fireControl($fc){
        if ($fc === null) return null;
        if ($fc > 0) return (int)floor($fc / 2);
        return $fc * 2;
    }

    //"...offensive bonuses for shuttles/fighters are cut in half": rounding DOWN (D4, revised). ⚠️ MIRROR PAIR with nebula.offensiveBonus (JS).
    public static function offensiveBonus($ob){
        if ($ob <= 0) return $ob;
        return (int)floor($ob / 2);
    }

    /* ⚠️ "Penalties are doubled" means a NEGATIVE FIRE CONTROL VALUE (fireControl above) and nothing else: a called
       shot's penalty and a Piercing shot are NOT doubled (user, after play-test game 4545 - both were built, and
       rolled back the same day). */

    /* "Weapons firing in defensive mode have their intercept ratings reduced by 1" - taken off
       getInterceptRating() where the rating is SPENT, never off the stored $intercept: fifteen classes
       override getInterceptRating with a figure of their own (boost levels, a Trek phaser's flat 2, a
       Particle Impeder's 3 + boost) and never read the property. Never below 0. */
    public static function interceptRating($rating){
        return max(0, $rating - 1);
    }

    /* "The range of all missiles is halved (this includes both the launch and distance ranges)." Missiles =
       the missile racks (user ruling D3): the MissileLauncher family, the multi-launchers and every ammo rack
       (fighter and bomb racks included) - but not torpedoes, and not BallisticMineLauncher, which is an ammo
       rack that lays mines. FighterTorpedoLauncher is a FighterMissileRack by class and a torpedo by rule. */
    public static function isMissile($weapon){
        if ($weapon instanceof BallisticMineLauncher || $weapon instanceof FighterTorpedoLauncher) return false;
        return $weapon instanceof MissileLauncher
            || $weapon instanceof MultiMissileLauncher
            || $weapon instanceof MultiBombRack
            || $weapon instanceof AmmoMissileRackS;
    }

    /* Halved, dropping fractions - the Kitchen Sink's own worked example of a halved missile range (5.12, a
       jammer halving launch range): "a missile with a normal launch range of 15 hexes could not be launched
       ... until the ship reached a range of 7 hexes". 0 means unlimited and stays 0. */
    public static function missileRange($range){
        if ($range <= 0) return $range;
        return (int)floor($range / 2);
    }

    /* Weapon::stripForJson, for a missile in a nebula: its ranges as the client must see them - the current mode's
       and every mode's - and the tooltip's "Range" line to match. The weapon's own arrays are never touched (an
       ammo rack rebuilds them from its ammo on every magazine change); Weapon::isInDistanceRange halves at the
       one server read instead. */
    public static function missileRangesForJson($weapon, $stripped){
        $halve = array(self::class, 'missileRange');
        $stripped->range = self::missileRange($weapon->range);
        $stripped->distanceRange = self::missileRange($weapon->distanceRange);
        if (!empty($weapon->rangeArray)) $stripped->rangeArray = array_map($halve, $weapon->rangeArray);
        if (!empty($weapon->distanceRangeArray)) $stripped->distanceRangeArray = array_map($halve, $weapon->distanceRangeArray);

        $data = isset($stripped->data) ? $stripped->data : $weapon->data;
        if (is_array($data) && isset($data["Range"]) && $stripped->range > 0) {
            $data["Range"] = $stripped->range . (($stripped->distanceRange > $stripped->range) ? '/' . $stripped->distanceRange : '');
            $stripped->data = $data;
        }
    }

    /* "Electromagnetic weapons do not function unless mounted on advanced race ships." Asked of the MODE a
       shot is fired in - three BSG guided missiles carry EM and non-EM modes side by side, and only their EM
       ones are switched off. $mode null = the weapon's current mode. */
    public static function isEmDisabled($weapon, $mode = null){
        $class = $weapon->weaponClass;
        if ($mode !== null && isset($weapon->weaponClassArray[$mode])) $class = $weapon->weaponClassArray[$mode];
        if ($class !== 'Electromagnetic') return false;
        //getUnit() is the hull - for a fighter's weapon, its FLIGHT (FighterFlight::addSystem wires it so).
        return !self::isAdvanced($weapon->getUnit());
    }
}
