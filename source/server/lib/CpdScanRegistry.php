<?php
/**
 * CpdScanRegistry - fleet-wide shield adaptation earned by the Walkers' Chromatic Pulse Driver.
 *
 * WHAT IT HOLDS
 * A CPD firing in Scanning mode does no damage; a hit on a unit that carries any shield-type
 * defensive system teaches the scanning fleet something about that RACE's shields, and from the
 * NEXT turn every ship on the scanner's team shoots at shields that are that much weaker.
 *
 * The knowledge is therefore keyed by (team, race) and not by ship, weapon or hull:
 *
 *     self::$adaptation[<teamId>][<faction string>] = <points>
 *
 * "The same race" is the RAW $ship->faction string (WALKERS_OF_SIGMA_PLAN.md D7) - no faction
 * families, no normalising. Minbari Federation and Minbari Protectorate are two races and adapt
 * separately, and so does any custom or `whatif` hull carrying its own faction string.
 * ⚠️ Read faction off the TARGET HULL, never off its ships directory name - the two do not always
 * match, which is the whole reason ShipLoader::getFactionDirMap() exists.
 *
 * WHERE THE NUMBERS COME FROM AND WHERE THEY GO
 *   in  : ChromaticPulseDriver::onIndividualNotesLoaded(), once per gamedata load, replaying the
 *         "CPDSCAN" IndividualNotes of every CPD in the game (pulse.php).
 *   out : applyToShieldBucket() below, called from the four places that aggregate a unit's
 *         defensive systems - BaseShip::getHitChanceMod / getDamageMod (ShipClasses.php) and
 *         FighterFlight's two redefinitions - plus the client mirror in model/ship.js.
 *         AND applyToCapacity(), for the shields that hold a POOL instead of a modifier
 *         (Thirdspace Shield, Thought Shield, both Trek Shield Projections) - reached through
 *         Shield::getCapacityAgainstShooter() from their doesProtectFromDamage()/doProtect().
 *
 * ⚠️⚠️ THIS IS A PER-LOAD STATIC AND MUST BE RESET PER LOAD. One request loads gamedata more than
 * once (Manager::advanceGameState loads, then the phase's advance() loads again), and without a
 * reset the second load would double every fleet's adaptation. DBManager::getSystemDataForShips
 * calls resetPerLoadState() immediately before its onIndividualNotesLoaded sweep, in the same
 * place and for the same reason GraviticAugmenter::resetPerLoadState() is called - see
 * arch_augmenter_jink_hidden_phase2 for the bug that costs.
 *
 * ⭐ COST. The CPD is one weapon on one Ancient faction, and applyToShieldBucket() has to be
 * consulted by the four aggregators that every shot in every game passes through. So the whole
 * feature hangs off ONE static boolean, TacGamedata::$cpdAdaptationPresent, set by record() below:
 *   - the four call sites test the boolean, so an ordinary game makes no call and passes no args;
 *   - TacGamedata::onConstructed tests it rather than class_exists(), so it does not publish;
 *   - DBManager's reset uses class_exists($name, FALSE), autoloading off, so a game with no CPD
 *     scan never even READS this file. (If the class is not loaded, its table is empty anyway.)
 * The redundant empty() test at the top of applyToShieldBucket is a second line of defence, not
 * the gate - it costs nothing because it is unreachable in the common case.
 *
 * Design record: WALKERS_OF_SIGMA_PLAN.md section 3.4 (Stage 3).
 */
class CpdScanRegistry
{
    /* The getDefensiveType() string every shield-ish defensive system in the tree answers with:
       Shield, EMShield, FlareShielding, GraviticShield, ShadingField, AbbaiShieldProjector,
       FlareGenerator, PakmaraPlasmaWeb and NexusWaterCaster. Working on the aggregated BUCKET
       rather than on each of those nine classes is what makes one edit cover all of them - and
       the bucket is already "highest single source only", which is the rules' own
       "overlapping shields are not cumulative". */
    const SHIELD_TYPE = "Shield";

    /* [teamId][faction] => points of adaptation. Empty in every game that has no CPD in it, which
       is what makes applyToShieldBucket() free for everyone else. */
    private static $adaptation = array();

    /* Called once per gamedata load, BEFORE any note is replayed. See the class comment.
       ⚠️ DBManager calls this behind class_exists(..., false) - autoloading OFF - so in a game with
       no CPD scan this method is never reached and this file is never even read. It also clears the
       gate itself, but DBManager clears that unconditionally too, precisely because it cannot know
       whether this class is loaded. */
    public static function resetPerLoadState(){
        self::$adaptation = array();
        TacGamedata::$cpdAdaptationPresent = false;
    }

    /* ⚠️ THE ONLY PLACE THE FEATURE TURNS ON. Everything downstream - the four defensive-mod
       aggregators, the client publication - is gated on TacGamedata::$cpdAdaptationPresent, so a
       recorded point of adaptation is what makes all of it live. Deliberately not keyed on "is
       there a CPD on a hull": an unfired driver changes nothing and should cost nothing. */
    public static function record($teamId, $faction, $points){
        if ($teamId === null || $faction === null || $faction === '') return;
        $points = (int)$points;
        if ($points <= 0) return;

        $teamId = (int)$teamId;
        if (!isset(self::$adaptation[$teamId])) self::$adaptation[$teamId] = array();
        if (!isset(self::$adaptation[$teamId][$faction])) self::$adaptation[$teamId][$faction] = 0;
        self::$adaptation[$teamId][$faction] += $points;
        TacGamedata::$cpdAdaptationPresent = true;
    }

    /* Points team $teamId has accumulated against race $faction. 0 for everything unscanned. */
    public static function get($teamId, $faction){
        if ($teamId === null || $faction === null) return 0;
        $teamId = (int)$teamId;
        if (!isset(self::$adaptation[$teamId][$faction])) return 0;
        return (int)self::$adaptation[$teamId][$faction];
    }

    public static function isEmpty(){
        return empty(self::$adaptation);
    }

    /* The whole table, for TacGamedata::stripForJson to hand to the client so its hit-chance
       preview agrees with the dice. Adaptation is PUBLIC knowledge - it is earned by a scanning
       shot that resolves and is logged like any other - so every team's entry is published, which
       is also what lets a defender see why their shields are reading low.
       ⚠️ Returns null rather than array() when there is nothing: an empty PHP array encodes as
       JSON `[]`, not `{}`, and the client indexes this by team id (plan trap 9). */
    public static function publishAll(){
        if (empty(self::$adaptation)) return null;
        $out = new stdClass();
        foreach (self::$adaptation as $teamId => $byFaction){
            $out->{$teamId} = (object)$byFaction;
        }
        return $out;
    }

    /**
     * Reduce the "Shield" entry of an already-aggregated defensive-systems map by whatever the
     * SHOOTER's team has learned about the TARGET's race.
     *
     * Applied to the bucket rather than inside each shield class on purpose:
     *   - the bucket is per defensive TYPE and already holds only the strongest single source, so
     *     the reduction lands exactly once no matter how many shields the target mounts;
     *   - one edit covers every shield-type system in the game, including the four that are
     *     Weapon subclasses (Shield Projector, Flare Generator, Plasma Web, Water Caster);
     *   - a Torvalus Shading Field's STEALTH function is untouched, which is the plan's
     *     "adaptation is spent on its shield-type properties first". Its EM-shield contribution
     *     is in this bucket and is reduced; its detection profile is not in it at all and is
     *     deliberately left alone (arch_stealth_toggle_forecast - the detection forecast is a
     *     separate, own-team-only path and nothing here may reach it).
     *
     * Never turns a shield into a bonus: clamped at 0, and a bucket that is already <= 0
     * (a defensive system that makes its owner EASIER to hit) is left exactly as it was.
     *
     * $target is the unit being shot at; $shooter is whoever is shooting. Both may be null or
     * team-less (terrain, mines, unslotted units), in which case nothing happens.
     */
    public static function applyToShieldBucket(array $affectingSystems, $target, $shooter){
        if (empty(self::$adaptation)) return $affectingSystems;             //free for every ordinary game
        if (!isset($affectingSystems[self::SHIELD_TYPE])) return $affectingSystems;
        if ($affectingSystems[self::SHIELD_TYPE] <= 0) return $affectingSystems;

        $team    = ($shooter !== null && isset($shooter->team))    ? $shooter->team    : null;
        $faction = ($target  !== null && isset($target->faction))  ? $target->faction  : null;

        $points = self::get($team, $faction);
        if ($points <= 0) return $affectingSystems;

        $affectingSystems[self::SHIELD_TYPE] = max(0, $affectingSystems[self::SHIELD_TYPE] - $points);
        return $affectingSystems;
    }

    /**
     * The SAME rule, in the currency of a CAPACITY-POOL shield.
     *
     * Thirdspace Shields, Thought Shields and Trek Shield Projections are all `getDefensiveType()
     * === "Shield"` - so a scanning hit banks a point off them and they carry the marker - but
     * their getDefensiveHitChangeMod / getDefensiveDamageMod are hard 0 (a Thought Shield's is
     * non-zero only while EM-reinforced). They do not reduce a shot's profile or its damage; they
     * hold a POOL and absorb out of it. So applyToShieldBucket() above lands on nothing for them
     * and the scan had no effect at all - which is the gap this closes (user, 2026-09-04):
     * against an adapted fleet the pool's last $points are unspendable.
     *
     * ⚠️ COMPUTED PER SHOT, NEVER STORED. This is not damage: it writes no DamageEntry, so the
     * pool regenerates from its real remaining health as usual and reads at FULL strength against
     * every fleet that has not analysed the race. Two fleets with different adaptation shooting
     * the same shield in the same turn each see their own number, which is the point.
     *
     * $target is the unit being shot at; $shooter is whoever is shooting. Either may be null or
     * team-less (terrain, mines, unslotted units), in which case nothing happens.
     */
    public static function applyToCapacity($capacity, $target, $shooter){
        if (empty(self::$adaptation)) return $capacity;                     //free for every ordinary game
        if ($capacity <= 0) return $capacity;

        $team    = ($shooter !== null && isset($shooter->team))    ? $shooter->team    : null;
        $faction = ($target  !== null && isset($target->faction))  ? $target->faction  : null;

        $points = self::get($team, $faction);
        if ($points <= 0) return $capacity;

        return max(0, $capacity - $points);
    }

    /* Is this system one of the shield-type defensive systems the reduction lands on?
       The ONE definition of "shield-ish", shared with ChromaticPulseDriver::isShieldSystem()
       (which decides whether a scanning hit banks a point at all) and with the bucket key
       above, so the marker, the scan and the arithmetic can never disagree about which
       systems are in scope. instanceof DefensiveSystem is the right first test: it is what
       BaseShip::checkIsValidAffectingSystem gates the whole bucket on, and it is what lets
       the four Weapon subclasses in the list (Shield Projector, Flare Generator, Plasma Web,
       Water Caster) answer for themselves. */
    public static function isShieldSystem($system){
        if (!($system instanceof DefensiveSystem)) return false;
        return ($system->getDefensiveType() === self::SHIELD_TYPE);
    }

    /**
     * Hang a display-only "Scanned by Walkers" marker on every shield-type system of every
     * unit whose RACE some fleet has analysed, so the reduction is visible on the ship sheet
     * and not only inside a hit-chance tooltip. Called once per payload-building load, from
     * TacGamedata::prepareForPlayer(), behind the $cpdAdaptationPresent gate (D10).
     *
     * WHY prepareForPlayer AND WHY THERE IN PARTICULAR:
     *   - it must run BEFORE setPreTurnTasks(), because that is the sweep whose
     *     beforeTurn() -> setSystemDataWindow() rebuilds ShipSystem::$critData out of
     *     $criticals; a marker added after it would ride the payload with no description;
     *   - it must run BEFORE applyChameleonDisguise(), so it lands on the REAL ship and never
     *     on a phantom sheet. A disguised hull therefore shows no marker, which under-reports
     *     rather than leaking - the same direction the client-side mirror already errs in,
     *     because a disguised ship publishes its DISGUISED faction;
     *   - prepareForPlayer runs only on the two READ paths (Manager::getGamedata and
     *     getReplayGameData), never on the submit path, so nothing in turn processing ever
     *     sees these objects.
     *
     * ⚠⚠ NOTHING HERE MAY BE SAVED. The criticals are left with $updated = false and
     * $newCrit = false, which is what keeps TacGamedata::getUpdatedCriticals() from listing
     * them; the CPDSCAN notes stay the single source of truth. Do not set either flag, and do
     * not give the marker a real id.
     *
     * Shown to EVERY viewer, like the adaptation itself: a scanning shot resolves and is
     * logged in public, and the defender needs to see why their shields read low.
     * The magnitude is the largest any single team holds against that race - with one Walker
     * fleet in play, which is the only case the rules produce, that is simply its total.
     */
    public static function applyScanMarkers($gamedata){
        if (empty(self::$adaptation)) return;

        //Largest holding per race, collapsed once rather than per ship.
        $byFaction = array();
        foreach (self::$adaptation as $points){
            foreach ($points as $faction => $value){
                if (!isset($byFaction[$faction]) || $byFaction[$faction] < $value){
                    $byFaction[$faction] = (int)$value;
                }
            }
        }

        $turn = (int)$gamedata->turn;
        foreach ($gamedata->ships as $ship){
            if (!isset($ship->faction) || !isset($byFaction[$ship->faction])) continue;
            $points = $byFaction[$ship->faction];
            if ($points < 1) continue;

            foreach ($ship->systems as $system){
                self::markSystem($ship, $system, $points, $turn);
                //A flight carries its shields on the individual craft, one level down. Fighter
                //::setSystemDataWindow recurses into these, so their critData is built too.
                if ($system instanceof Fighter){
                    foreach ($system->systems as $subsystem){
                        self::markSystem($ship, $subsystem, $points, $turn);
                    }
                }
            }
        }
    }

    /* One system. Idempotent: a second call in the same request must not stack a second
       marker, because ShipSystem::setSystemDataWindow() counts criticals per class and a
       duplicate would read as "(2 x) Scanned by Walkers". */
    private static function markSystem($ship, $system, $points, $turn){
        if (!self::isShieldSystem($system)) return;
        foreach ($system->criticals as $existing){
            if ($existing->phpclass === "ScannedByWalkers") return;
        }
        $crit = new ScannedByWalkers(-1, $ship->id, $system->id, "ScannedByWalkers", $turn);
        $crit->description = "Scanned (-" . $points . " effectiveness)";
        $system->criticals[] = $crit; //⚠ $updated stays false - see applyScanMarkers
    }
}
