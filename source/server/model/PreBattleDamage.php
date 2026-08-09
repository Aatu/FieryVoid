<?php

/**
 * Pre-battle damage & fleet damage persistence — the single source of rules.
 * See PREBATTLE_DAMAGE_PLAN.md (§3 wire format, §4.2 this class).
 *
 * One compact per-ship payload serves the lobby state, the buy POST, the
 * saved-fleet write, the saved-fleet read and the in-game "Save Current Fleet":
 *
 *   preBattleDamage: {
 *     sys: { "<systemid>": { d: <int>, k: 0|1, c: {<critClass>: <count>}, p: {<critClass>: <int>} }, ... },
 *     ftr: { "<ordinal>":  { d: <int>, k: 0|1, c: {<critClass>: <count>}, p: {<critClass>: <int>} }, ... },
 *     mne: { "<ordinal>":  { d: <int> }, ... }
 *   }
 *
 *   d = total damage points (1 .. maxhealth); k = 1 when destroyed;
 *   c = map of critical phpclass => count;
 *   p = map of critical phpclass => integer param, for the few classes that keep their
 *       MAGNITUDE in the crit's param rather than in the number of crits (see
 *       $paramCriticals). A sibling map rather than a richer `c` value on purpose: every
 *       consumer of `c` keeps counting, and `p` is simply absent for the other 40-odd
 *       classes. A key in `p` with no matching key in `c` is pruned at every boundary.
 *
 * ORDINARY SHIPS are keyed by systemid: BaseShip::add*System assigns ids in pure
 * constructor order and the same constructor runs for the lobby's static blueprint,
 * the buy-POST reconstruction and the game load. Enhancements never add systems.
 *
 * FLIGHTS are keyed by fighter ORDINAL 1..flightSize, because in the lobby a flight
 * is ONE sample fighter plus a number — the N fighters are minted server-side by
 * populate(), so per-fighter system ids do not exist client-side at all. Ordinals
 * are resolved by POSITION (array_values), never by array key: FighterFlight::addSystem
 * draws fighter ids from a running autoid that ALSO consumes every subsystem, so the
 * keys are 1, 1+k, 1+2k… and not 1..N.
 *
 * A FIGHTER IS ONLY EVER DAMAGED, NEVER DESTROYED (`k` is dropped for the ftr bucket and
 * damage caps at maxhealth − 1). A dead fighter has no representation in B5W other than
 * "not in the flight", and flightSize already says that: it is freely editable in the
 * lobby, and Part 2's in-game save records the SURVIVING craft. Destroyed craft therefore
 * shrink the flight rather than riding along as wrecks.
 *
 * Criticals reach a payload two ways: CAPTURED from a live game and carried through
 * (live game -> saved fleet -> lobby -> game DB), or AUTHORED in the lobby from the
 * per-class catalogue (plan §11, built 2026-08-08 - see offerableCriticalTypes and
 * $generalCriticals below, and Manager::getSystemCriticals). Both arrive in the same
 * `c`/`p` keys and go through the same validator, which is why authoring needed no
 * change to the wire format.
 */
class PreBattleDamage
{
    /* Pre-battle rows are written at turn 0. The lobby sits at turn 0 (createGame
       inserts turn 0/phase -2 and changeTurn bumps to 1 right after BuyingGamePhase
       ::advance), so turn 0 renders in the lobby, makes ShipSystem::isDestroyed's
       structure cascade (currentTurn - 1) fire from Deployment on, and never matches
       a `turn == gamedata.turn` combat-log/replay filter. */
    const TURN = 0;
    /* TRANSIENT criticals (one-turn / self-expiring) are stamped at turn 1, not turn 0.
       Stamped at 0 the base Critical constructor would set turnend = 1, and
       getCriticalsForShips' `turnend = 0 OR turnend >= fetchTurn` test would drop it the
       moment turn 1 began - i.e. a carried "output halved for one turn" would silently do
       nothing at all. At turn 1 its turnend is 2, so it is in effect for the FIRST turn of
       the new battle, which is what "save temporary critical effects" promises.
       (Plan §11.4 called this out as the fix if authoring them was ever wanted.) */
    const TRANSIENT_TURN = 1;
    const DAMAGECLASS = 'PreGame';
    const PUBNOTES = 'Pre-battle damage';

    const KIND_SYSTEM = 0;
    const KIND_FIGHTER = 1;
    /* MINES. A bulk mine purchase is ONE lobby ship carrying bulkBuy = N, minted into N
       separate tac_ship rows by BuyingGamePhase - the same "one object plus a number"
       shape a flight has, which is why it gets ordinals rather than system ids. A mine
       can carry only STRUCTURE damage: it has no criticals table worth the name and its
       other systems are all untargetable, so the bucket's entries are {d} and nothing
       else (user decision 2026-08-08). */
    const KIND_MINE = 2;

    /* bucket name => kind. THE list every loop walks, so adding a fourth kind is one
       line here rather than six greps. */
    const BUCKETS = array('sys' => self::KIND_SYSTEM, 'ftr' => self::KIND_FIGHTER, 'mne' => self::KIND_MINE);

    const MAX_CRIT_COUNT = 5;
    /* Ceiling on a stored crit param. Generous: the biggest real DamageReductionReduced
       total is the whole rating of a Thought Shield, and a value larger than the system's
       own output simply floors the effect at zero. It exists so a crafted POST cannot
       write a nonsense integer, not to express a rule. */
    const MAX_CRIT_PARAM = 300;

    /* ─────────────────────────────────────────────────────────────────────────────────
       PARAM-CARRYING CRITICALS — the allow-list.
       ─────────────────────────────────────────────────────────────────────────────────
       A handful of criticals keep their MAGNITUDE in $param rather than in the number of
       crits: one DamageReductionReduced crit is a whole 1d10 of Phased Gravitic Torpedo
       phasing, and every consumer SUMS the params (ShipSystem::sumCriticalParam) instead
       of counting the crits. Carrying one of those with param NULL would store the wound
       and throw away how bad it was, which is why they used to be refused outright.

       A class named here may be saved WITH its param. Because the effect is a sum, the
       N crits on a system are collapsed into ONE row carrying the TOTAL - identical for
       every consumer (sumCriticalParam, the client's paramTotal, SystemInfo's
       PARAM_SUM_CRIT_DESCRIPTIONS line) and it gives the lobby a single number to edit.

       ⚠️ Only add a class here when its param is a PLAIN INTEGER and multiple crits of
       the class SUM. The marine/boarding criticals also carry a param, but theirs is an
       ARRAY of boarding-pod info describing a moment in that battle - they stay on the
       deny list below, and tac_saved_crit.param is an INT column so one could not be
       stored even if it got this far. */
    private static $paramCriticals = array(
        'DamageReductionReduced',
    );

    /* ─────────────────────────────────────────────────────────────────────────────────
       ⭐ PER-CLASS CRITICAL LIMITS — how many of one effect a unit may carry.
       ─────────────────────────────────────────────────────────────────────────────────
       Anything NOT named here may be carried up to MAX_CRIT_COUNT. A class listed here
       is capped at the given number, in the lobby editor AND at every write boundary.

       MIRRORS battleDamage.CRIT_LIMITS in source/public/client/battleDamage.js - edit
       BOTH, exactly like the deny list. The server is the boundary that enforces; the
       client copy stops the editor offering a count the server would silently clamp.

       ⚠️ HOW THE FIRST PASS WAS DERIVED (2026-08-08), so a future edit can argue with it:
       every entry below is a critical the game reads as a FLAG - the code asks
       `if ($system->hasCritical('X'))` and never multiplies by the count, and the class
       carries no outputMod - so a second instance changes nothing that can be observed.
       Everything left off the list either multiplies by its count (ReducedIniative,
       GunLost, ArmorReduced, PenaltyToHit, HalfEfficiency's `round($used/($crits+1))` …)
       or stacks through effectCriticals' outputMod sum (the OutputReduced family,
       FieldFluctuations), so capping those would change the rules rather than tidy them.

       The two burnout results and OutputHalved are the judgement calls: they DO stack
       arithmetically, but "efficiency halved" twice and "system non-functional" twice are
       not things a B5W sheet can express - a second one is just a destroyed system. */
    private static $critLimits = array(
        //pure flags: asked as `if (hasCritical(...))`, never counted
        'DamageReductionRemoved'     => 1,
        'ReducedArcs'                => 1,   //turret jam; testArcRestriction already refuses to double up
        'ArmorReduced'               => 10,
        'ContainmentBreach'          => 1,
        'ChargeHalve'                => 1,
        'ChargeEmpty'                => 1,
        'ShipDisabled'               => 1,
        'ShipDisabledOneTurn'        => 1,
        'ForcedOfflineOneTurn'       => 1,
        'OSATThrusterCrit'           => 1,
        //'GravThrusterCritIgnored'    => 1,
        //'AmmoExplosion'              => 1,   //a magazine only blows up once
        //'EngineShorted'              => 1,
        //'ControlsStuck'              => 1,
        'TendrilDestroyed'           => 1,
        //escalating results rather than stacking modifiers - see the note above
        'PartialBurnout'             => 1,
        'SevereBurnout'              => 1,
        'OutputHalved'               => 1,
        'OutputHalvedOneTurn'        => 1,
        //param-carrying: the magnitude lives in `p`, and sanitiseEntry forces the COUNT to
        //1 for these classes whatever this table says. Listed at 1 so the number the
        //catalogue serves the client agrees with what is actually stored - the client's
        //critLimit() prefers the catalogue's meta over its own mirror, so a larger figure
        //here would be a ceiling nothing honours.
        'DamageReductionReduced'     => 1,
    );

    /* D7: criticals that describe a MOMENT in a battle rather than a lasting wound are
       never stored. This list adds the Hangar-Ops / transient state markers on top of the
       generic tests in isValidCriticalType.
       ⚠️ This is the STORABILITY test only. Do NOT narrow it to a system's
       possibleCriticals table: genuine combat crits (AmmoExplosion, OSATThrusterCrit,
       LimpetBore …) are applied by bespoke code and are absent from those tables, so
       narrowing would silently eat carried crits on every reload. "What may a player be
       OFFERED" is a separate, narrower question — offerableCriticalTypes below. */
    /* ─────────────────────────────────────────────────────────────────────────────────
       ⭐ THE DENY LIST — the one place a critical is banned from saved fleets by NAME.
       ─────────────────────────────────────────────────────────────────────────────────
       TO BAN a critical from ever being carried between battles: add its class name to
       this array, AND to battleDamage.EXCLUDED_CRITICALS in
       source/public/client/battleDamage.js (the client twin - the server is the boundary
       that actually enforces it, the client list only stops us OFFERING to save something
       the server will drop).
       TO ALLOW one again: remove it from BOTH lists. It still has to pass the generic
       tests in isValidCriticalType below - a `forInfo` marker is refused whatever this
       list says.
       Nothing else needs touching: every write path (buy POST, fleet save, fleet load)
       goes through isValidCriticalType.
       ───────────────────────────────────────────────────────────────────────────────── */
    private static $denyList = array(
        /* NOTE 2026-08-08: ReducedArcs and DamageReductionReduced used to be banned here
           as "param-carrying". Both were wrong for different reasons and both are now
           carried between battles:
             - ReducedArcs (turret jam) carries NO param at all. The restricted arc lives
               on the weapon INSTANCE in the ship blueprint (Weapon::$reducedArcStart /
               setArcRestriction), and the crit is only the flag that says "locked to it"
               (weapon.php::effectCriticals). It rebuilds perfectly on the new ship.
             - DamageReductionReduced genuinely carries an integer param, which is now
               stored - see $paramCriticals above. */
        //weapon-cooldown marker; always carries a real turnend, which the probe below
        //cannot detect because its turnend is a constructor argument with no default
        'AmmoExplosion',
        'GravThrusterCritIgnored',
        'ControlsStuck',
        'EngineShorted', 
        'ForcedOfflineForTurns',
        //Hangar Ops / state markers
        'DockedFighter',
        'SplitLaunchedFighter',
        'LaunchedThisTurn',
        'LCVLaunchedThisTurn',
        'HangarOperations',
        'OrbitalRepairing',
        'DisengagedFighter',
        'ShadowFighterCutOff',
        'LimpetBoreTravelling',
        'Uncontrolled',
        /* MARINE / BOARDING ACTIONS. A boarding action is a thing happening in THAT
           battle - there are no marines aboard when the next one starts, so carrying the
           marker would show "Marines are sabotaging this system" on a ship nobody has
           boarded. Every one of these also passes forInfo = true from its constructor and
           so would be refused anyway; they are named here because a rule you can read
           beats a rule you have to derive, and because a future boarding critical that
           forgets forInfo would otherwise leak. */
        'Sabotage',
        'SabotageElite',
        'CaptureShip',
        'CaptureShipElite',
        'RescueMission',
        'RescueMissionElite',
        'DefenderLost',
        /* Momentary combat effects that are neither forInfo nor self-expiring, so nothing
           generic catches them. tmphitreduction is "immediate reduction in target's chance
           to hit for all fire" - it belongs to one exchange of fire, not to a campaign.
           (This was the one real leak: it tested storable=YES before 2026-08-08.) */
        'tmphitreduction',
    );

    private static $validCache = array();
    private static $probeCache = array();

    /* ------------------------------------------------------------------ *
     *  Validation
     * ------------------------------------------------------------------ */

    /**
     * Is $type a real, STORABLE Critical subclass?
     *
     * Not cosmetic: DBManager::getCriticalsForShips does `new $type(...)` on the stored
     * string, so an unvalidated type would be arbitrary class instantiation on every
     * subsequent load of that game. Called at every write boundary.
     */
    public static function isValidCriticalType($type)
    {
        if (!is_string($type) || $type === '') return false;
        if (isset(self::$validCache[$type])) return self::$validCache[$type];

        $valid = false;
        if (!in_array($type, self::$denyList, true)
            && preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $type)
            && class_exists($type)
            && is_subclass_of($type, 'Critical')
        ) {
            $probe = self::critProbe($type);
            /* `forInfo` classes are refused outright: they are display markers for
               something that happened during a battle (a boarding attempt, "may
               overheat", "damage a system" resolving this turn), never a lasting wound.
               A class whose own constructor forces forInfo = true - as all seven marine
               criticals do - is caught here even if it is missing from the deny list.

               TRANSIENT classes (one-turn / self-expiring) are NOT refused here any more.
               They are storable; what changes is the TURN they are stamped at - see
               isTransientCriticalType and TRANSIENT_TURN. Keeping this function the
               storability test and nothing more is the seam plan §4.2 asked for. */
            $valid = ($probe !== null && !$probe->forInfo);
        }

        self::$validCache[$type] = $valid;
        return $valid;
    }

    /**
     * Is this critical a TEMPORARY one — in effect for a single turn and then gone?
     *
     * True for both `oneturn` classes (the base Critical constructor sets
     * turnend = turn + 1 for them) and classes like OutputReducedOneTurn that do the same
     * in their own constructor without setting the flag — which is why this is measured
     * off a probe rather than read off a property.
     *
     * Two consumers: toEntries/applyToShip stamp these at TRANSIENT_TURN so they actually
     * apply during turn 1 rather than expiring before it, and the client's summariser uses
     * the same rule to decide what the "save temporary critical effects" checkbox covers.
     */
    public static function isTransientCriticalType($type)
    {
        $probe = self::critProbe($type);
        if ($probe === null) return false;

        return (bool)$probe->oneturn || (int)$probe->turnend !== 0;
    }

    /** The turn a critical of this class should be stamped at. */
    public static function criticalTurn($type)
    {
        return self::isTransientCriticalType($type) ? self::TRANSIENT_TURN : self::TURN;
    }

    /**
     * Does this critical keep its MAGNITUDE in $param (so the param must travel with it,
     * and the crits of that class on one system collapse to a single summed row)?
     * See $paramCriticals.
     */
    public static function isParamCriticalType($type)
    {
        return is_string($type) && in_array($type, self::$paramCriticals, true);
    }

    /** {critClass => true} for the param-carrying classes, for the client's editor. */
    public static function paramCriticalTypes()
    {
        return array_fill_keys(self::$paramCriticals, true);
    }

    /**
     * How many of this critical one damage target may carry. See $critLimits.
     * MAX_CRIT_COUNT for anything not named there.
     */
    public static function criticalLimit($type)
    {
        if (is_string($type) && isset(self::$critLimits[$type])) {
            return (int)self::$critLimits[$type];
        }

        return self::MAX_CRIT_COUNT;
    }

    /** The whole table, for the client's editor. */
    public static function criticalLimits()
    {
        return self::$critLimits;
    }

    /** A stored param: a positive integer within MAX_CRIT_PARAM, or null. */
    private static function sanitiseParam($value)
    {
        if (is_array($value) || is_object($value)) return null;   //never an array param
        if (!is_numeric($value)) return null;

        $param = (int)$value;
        if ($param < 1) return null;
        if ($param > self::MAX_CRIT_PARAM) $param = self::MAX_CRIT_PARAM;

        return $param;
    }

    /**
     * One throwaway instance per class, used to read $description / $oneturn.
     * Every Critical subclass takes the same first six constructor arguments.
     */
    private static function critProbe($type)
    {
        if (array_key_exists($type, self::$probeCache)) return self::$probeCache[$type];

        $probe = null;
        try {
            $probe = new $type(-1, -1, -1, $type, self::TURN, 0);
            if (!($probe instanceof Critical)) $probe = null;
        } catch (Throwable $e) {
            $probe = null;
        }

        self::$probeCache[$type] = $probe;
        return $probe;
    }

    /* ------------------------------------------------------------------ *
     *  Sanitising / normalising
     * ------------------------------------------------------------------ */

    /**
     * Validate + normalise a raw client payload against THIS ship.
     * Drops anything invalid; never throws on bad client data.
     *
     * Depends only on getSystemById / maxhealth / flightSize, none of which need notes
     * or enhancements — POST-side reconstructed ships carry neither.
     */
    public static function sanitise($ship, $raw)
    {
        $clean = array();
        if (!is_array($raw) || !$ship) return $clean;

        $isFlight = ($ship instanceof FighterFlight);

        //--- ordinary ships: keyed by systemid -------------------------------
        if (!$isFlight && isset($raw['sys']) && is_array($raw['sys'])) {
            $sys = array();
            foreach ($raw['sys'] as $key => $entry) {
                if (!self::isIntish($key)) continue;
                $systemid = (int)$key;
                $system = $ship->getSystemById($systemid);
                if (!$system) continue;
                $e = self::sanitiseEntry($entry, (int)$system->maxhealth);
                if ($e) $sys[$systemid] = $e;
            }
            if ($sys) $clean['sys'] = $sys;
        }

        //--- flights: keyed by fighter ordinal 1..flightSize ------------------
        if ($isFlight && isset($raw['ftr']) && is_array($raw['ftr'])) {
            $flightSize = (int)$ship->flightSize;
            //A flight loaded from tac_saved_ship is deliberately left at ONE fighter
            //(the server populates from the stored flightSize at buy/load time), so
            //ordinals are validated against flightSize, never count($ship->systems).
            $sample = self::fighterByOrdinal($ship, 1);
            $maxhealth = $sample ? (int)$sample->maxhealth : 0;
            $ftr = array();
            foreach ($raw['ftr'] as $key => $entry) {
                if (!self::isIntish($key)) continue;
                $ordinal = (int)$key;
                if ($ordinal < 1 || $ordinal > $flightSize) continue;
                //FIGHTERS ARE DAMAGED, NEVER DESTROYED. A dead fighter has no
                //representation in B5W other than "not in the flight", and flightSize is
                //the natural way to say that - freely editable in the lobby, and what
                //Part 2 saves after a battle (surviving craft only). So a fighter's damage
                //is capped one point short of its structure and `k` is dropped: a flight
                //carries wounded craft, not wrecks.
                $e = self::sanitiseEntry($entry, $maxhealth, true);
                if ($e) $ftr[$ordinal] = $e;
            }
            if ($ftr) $clean['ftr'] = $ftr;
        }

        //--- mines: keyed by copy ordinal 1..bulkBuy --------------------------
        //A bulk mine purchase is one lobby object plus a number, exactly like a flight,
        //so its per-copy damage is keyed by ordinal too. STRUCTURE ONLY: everything else
        //on a mine is untargetable, and a mine has no criticals worth carrying - so the
        //entry is {d} and a mine, like a fighter, is damaged but never destroyed (a mine
        //you lost is one fewer in the bulk).
        if (!$isFlight && !empty($ship->mine) && isset($raw['mne']) && is_array($raw['mne'])) {
            $count = self::mineCount($ship);
            $structure = self::mineStructure($ship);
            $maxhealth = $structure ? (int)$structure->maxhealth : 0;
            $mne = array();
            foreach ($raw['mne'] as $key => $entry) {
                if (!self::isIntish($key)) continue;
                $ordinal = (int)$key;
                if ($ordinal < 1 || $ordinal > $count) continue;
                $e = self::sanitiseEntry($entry, $maxhealth, true, true);
                if ($e) $mne[$ordinal] = $e;
            }
            if ($mne) $clean['mne'] = $mne;
        }

        return $clean;
    }

    /** How many mines this one lobby object stands for (bulkBuy, floored at 1). */
    public static function mineCount($ship)
    {
        if (!$ship || empty($ship->mine)) return 0;
        $count = isset($ship->bulkBuy) ? (int)$ship->bulkBuy : 1;

        return $count > 0 ? $count : 1;
    }

    /**
     * The one system on a mine that can take damage: its Structure. Resolved by class
     * rather than by id so it survives a blueprint edit, and it is the only target the
     * mine bucket ever writes to.
     */
    public static function mineStructure($ship)
    {
        if (!$ship || empty($ship->systems)) return null;
        foreach ($ship->systems as $system) {
            if ($system instanceof Structure) return $system;
        }

        return null;
    }

    /**
     * Build a wire-format payload out of raw tac_saved_damage / tac_saved_crit rows,
     * validated against THIS ship (so a blueprint that changed since the fleet was
     * saved drops stale references rather than misapplying them).
     *
     * @param array $damageRows [[kind, ref, damage, destroyed], …]
     * @param array $critRows   [[kind, ref, type, amount, param], …] (param may be absent
     *                          on rows written before tac_saved_crit.param existed)
     */
    public static function sanitiseSavedRows($ship, $damageRows, $critRows)
    {
        $raw = array('sys' => array(), 'ftr' => array(), 'mne' => array());

        foreach ((array)$damageRows as $row) {
            list($kind, $ref, $damage, $destroyed) = array_pad(array_values((array)$row), 4, 0);
            $bucket = self::bucketForKind($kind);
            $ref = (int)$ref;
            if (!isset($raw[$bucket][$ref])) $raw[$bucket][$ref] = array();
            if ((int)$damage > 0) $raw[$bucket][$ref]['d'] = (int)$damage;
            if ((int)$destroyed) $raw[$bucket][$ref]['k'] = 1;
        }

        foreach ((array)$critRows as $row) {
            list($kind, $ref, $type, $amount, $param) = array_pad(array_values((array)$row), 5, null);
            $bucket = self::bucketForKind($kind);
            $ref = (int)$ref;
            if (!isset($raw[$bucket][$ref])) $raw[$bucket][$ref] = array();
            if (!isset($raw[$bucket][$ref]['c'])) $raw[$bucket][$ref]['c'] = array();
            $raw[$bucket][$ref]['c'][(string)$type] = (int)$amount;
            if ($param !== null && $param !== '') {
                if (!isset($raw[$bucket][$ref]['p'])) $raw[$bucket][$ref]['p'] = array();
                $raw[$bucket][$ref]['p'][(string)$type] = $param;
            }
        }

        return self::sanitise($ship, $raw);
    }

    /**
     * One {d,k,c} entry. Returns null when nothing survives.
     * $damageOnly caps the damage one point short of destruction and drops `k` - see the
     * fighter branch of sanitise().
     */
    private static function sanitiseEntry($entry, $maxhealth, $damageOnly = false, $noCriticals = false)
    {
        if (!is_array($entry)) return null;
        if ($maxhealth < 1) return null;

        $out = array();

        $k = !empty($entry['k']) ? 1 : 0;
        $cap = $damageOnly ? $maxhealth - 1 : $maxhealth;

        $d = isset($entry['d']) ? (int)$entry['d'] : 0;
        if ($d > $cap) $d = $cap;
        //A system with no structure left IS destroyed. Making that an invariant of the
        //payload (rather than only a UI convention) keeps the lobby preview and the game
        //in step: client isDestroyed reads a boolean, ShipSystem::isDestroyed reads the
        //destroyed FLAG on a damage row - neither re-derives destruction from the total.
        if ($d >= $maxhealth) $k = 1;
        if ($damageOnly) $k = 0;
        if ($d > 0) $out['d'] = $d;
        if ($k) $out['k'] = 1;

        if (!$noCriticals && isset($entry['c']) && is_array($entry['c'])) {
            $rawParams = (isset($entry['p']) && is_array($entry['p'])) ? $entry['p'] : array();
            $c = array();
            $p = array();
            foreach ($entry['c'] as $type => $count) {
                if (!self::isValidCriticalType($type)) continue;
                $count = (int)$count;
                if ($count < 1) continue;
                //Per-class ceiling (default MAX_CRIT_COUNT) - some effects are flags the
                //game reads once, so a second instance would be a wound that does nothing.
                $limit = self::criticalLimit($type);
                if ($count > $limit) $count = $limit;

                if (self::isParamCriticalType($type)) {
                    //A param crit's magnitude is the SUM of its params, so N crits are
                    //stored as ONE row carrying the total: identical for every consumer
                    //and it gives the lobby a single number to edit. A param class that
                    //arrives with no usable param has lost the only thing that made it
                    //worth carrying, so it is dropped rather than stored as a no-op.
                    $param = self::sanitiseParam(isset($rawParams[$type]) ? $rawParams[$type] : null);
                    if ($param === null) continue;
                    $c[$type] = 1;
                    $p[$type] = $param;
                } else {
                    $c[$type] = $count;
                }
            }
            if ($c) $out['c'] = $c;
            //`p` never outlives `c` - a param with no crit to hang on is meaningless.
            if ($p) $out['p'] = $p;
        }

        return $out ? $out : null;
    }

    private static function isIntish($key)
    {
        return is_int($key) || (is_string($key) && preg_match('/^\d+$/', $key));
    }

    /** The bucket a stored `kind` column belongs to; unknown kinds fall back to sys. */
    private static function bucketForKind($kind)
    {
        $bucket = array_search((int)$kind, self::BUCKETS, true);

        return $bucket === false ? 'sys' : $bucket;
    }

    /* ------------------------------------------------------------------ *
     *  Splitting damage from criticals (D3)
     * ------------------------------------------------------------------ */

    /**
     * THE single place damage and criticals are separated. $withDamage = false drops
     * d/k, $withCriticals = false drops c, and entries left empty are pruned.
     *
     * ⚠️ Callers must filter the PAYLOAD, not the render: $ship->preBattleDamage is what
     * the client re-POSTs at buy time, so a declined kind left in it would still be
     * written to tac_damage / tac_critical and the toggle would be a lie.
     */
    public static function filter($payload, $withDamage, $withCriticals)
    {
        $out = array();
        if (!is_array($payload)) return $out;

        foreach (array_keys(self::BUCKETS) as $bucket) {
            if (empty($payload[$bucket]) || !is_array($payload[$bucket])) continue;
            $kept = array();
            foreach ($payload[$bucket] as $ref => $entry) {
                if (!is_array($entry)) continue;
                $e = array();
                if ($withDamage) {
                    if (!empty($entry['d'])) $e['d'] = (int)$entry['d'];
                    if (!empty($entry['k'])) $e['k'] = 1;
                }
                if ($withCriticals && !empty($entry['c']) && is_array($entry['c'])) {
                    $e['c'] = $entry['c'];
                    //`p` travels with `c` or not at all, keyed the same way.
                    if (!empty($entry['p']) && is_array($entry['p'])) {
                        $p = array_intersect_key($entry['p'], $entry['c']);
                        if ($p) $e['p'] = $p;
                    }
                }
                if ($e) $kept[$ref] = $e;
            }
            if ($kept) $out[$bucket] = $kept;
        }

        return $out;
    }

    /** Does this payload contain each kind? Drives the two load checkboxes + messaging. */
    public static function contents($payload)
    {
        $has = array('damage' => false, 'criticals' => false);
        if (!is_array($payload)) return $has;

        foreach (array_keys(self::BUCKETS) as $bucket) {
            if (empty($payload[$bucket]) || !is_array($payload[$bucket])) continue;
            foreach ($payload[$bucket] as $entry) {
                if (!is_array($entry)) continue;
                if (!empty($entry['d']) || !empty($entry['k'])) $has['damage'] = true;
                if (!empty($entry['c'])) $has['criticals'] = true;
            }
        }

        return $has;
    }

    public static function isEmpty($payload)
    {
        if (!is_array($payload)) return true;
        foreach (array_keys(self::BUCKETS) as $bucket) {
            if (!empty($payload[$bucket])) return false;
        }

        return true;
    }

    /* ------------------------------------------------------------------ *
     *  Resolution
     * ------------------------------------------------------------------ */

    /**
     * Resolve a flight ordinal to the populated fighter, or null.
     *
     * POSITION-based on purpose: $ship->systems is keyed by $fighter->id, which
     * FighterFlight::addSystem draws from a running autoid that also consumes each
     * subsystem — so the keys are 1, 1+k, 1+2k…, NOT 1..N.
     */
    public static function fighterByOrdinal($ship, $ordinal)
    {
        if (!($ship instanceof FighterFlight)) return null;
        $ordinal = (int)$ordinal;
        if ($ordinal < 1) return null;

        $fighters = array_values($ship->systems);
        $idx = $ordinal - 1;

        return isset($fighters[$idx]) ? $fighters[$idx] : null;
    }

    /**
     * Expand a sanitised payload into DamageEntry[] + Critical[] for a KNOWN tac_ship id.
     * Returns array('damage' => [...], 'criticals' => [...]).
     *
     * One row per system rather than per point: the smallest thing that reproduces both
     * the display and the rules (getTotalDamage sums, isDestroyed reads the flag), and it
     * is the shape stripForJson would collapse a long history into anyway.
     */
    /**
     * $mineOrdinal — which copy of a bulk-bought mine these rows are for. A mine payload
     * is keyed by copy (1..bulkBuy) and BuyingGamePhase mints one tac_ship per copy, so
     * the caller says which one it is writing and only that ordinal's entry is expanded.
     * Null (every other unit) skips the `mne` bucket entirely.
     */
    public static function toEntries($ship, $clean, $gameid, $shipid, $mineOrdinal = null)
    {
        $parts = array('damage' => array(), 'criticals' => array());
        if (!is_array($clean) || !$ship) return $parts;

        foreach (self::BUCKETS as $bucket => $kind) {
            if (empty($clean[$bucket])) continue;
            if ($kind === self::KIND_MINE && $mineOrdinal === null) continue;

            foreach ($clean[$bucket] as $ref => $entry) {
                if ($kind === self::KIND_MINE && (int)$ref !== (int)$mineOrdinal) continue;

                if ($kind === self::KIND_FIGHTER) {
                    $system = self::fighterByOrdinal($ship, $ref);
                } else if ($kind === self::KIND_MINE) {
                    //every mine copy is the same blueprint, so the target is its Structure
                    $system = self::mineStructure($ship);
                } else {
                    $system = $ship->getSystemById((int)$ref);
                }
                if (!$system) continue;

                $destroyed = !empty($entry['k']) ? 1 : 0;
                $damage = isset($entry['d']) ? (int)$entry['d'] : 0;
                if ($destroyed && $damage < 1) $damage = (int)$system->maxhealth;

                if ($damage > 0 || $destroyed) {
                    //fireorderid 0, NOT -1: submitDamages runs a tac_fireorder back-fill
                    //lookup whenever fireorderid < 0, and there is no fire order here.
                    //pubnotes/damageclass are interpolated unescaped by submitDamages -
                    //only these two constants are ever passed, so nothing user-supplied
                    //reaches SQL. Keep it that way, or escape at the writer.
                    $parts['damage'][] = new DamageEntry(
                        -1, $shipid, $gameid, self::TURN, $system->id,
                        $damage, 0, 0, 0, $destroyed, 0,
                        self::PUBNOTES, self::DAMAGECLASS
                    );
                }

                if (!empty($entry['c'])) {
                    $params = (!empty($entry['p']) && is_array($entry['p'])) ? $entry['p'] : array();
                    foreach ($entry['c'] as $type => $count) {
                        if (!self::isValidCriticalType($type)) continue;
                        //Lasting crits at turn 0, temporary ones at turn 1 so they are in
                        //effect for the first turn instead of expiring as it begins.
                        $critTurn = self::criticalTurn($type);
                        //NULL for all but the param-carrying classes. submitCriticals runs
                        //it through DBEscape, and sanitiseParam has already forced it to a
                        //bounded integer, so nothing user-shaped reaches SQL.
                        $param = self::isParamCriticalType($type)
                            ? self::sanitiseParam(isset($params[$type]) ? $params[$type] : null)
                            : null;
                        for ($i = 0; $i < (int)$count; $i++) {
                            //id -1: submitCriticals only INSERTs when $critical->id < 1.
                            $crit = new $type(-1, $shipid, $system->id, $type, $critTurn, 0);
                            $crit->param = $param;
                            $parts['criticals'][] = $crit;
                        }
                    }
                }
            }
        }

        return $parts;
    }

    /**
     * Apply an ALREADY-FILTERED payload to a live ship OBJECT, so loadSavedFleet can
     * return a correct ship with no extra DB round trip. It applies whatever it is given
     * and needs no knowledge of the D3 toggles.
     *
     * The `ftr` bucket is deliberately skipped: a flight read from tac_saved_ship holds
     * one sample fighter, so ordinals beyond 1 have nothing to land on. The client
     * renders flights from the payload itself against its synthetic fighter rows.
     */
    public static function applyToShip($ship, $clean)
    {
        if (!$ship || empty($clean['sys'])) return;

        foreach ($clean['sys'] as $ref => $entry) {
            $system = $ship->getSystemById((int)$ref);
            if (!$system) continue;

            $destroyed = !empty($entry['k']) ? 1 : 0;
            $damage = isset($entry['d']) ? (int)$entry['d'] : 0;
            if ($destroyed && $damage < 1) $damage = (int)$system->maxhealth;

            if ($damage > 0 || $destroyed) {
                $system->setDamage(new DamageEntry(
                    -1, $ship->id, 0, self::TURN, $system->id,
                    $damage, 0, 0, 0, $destroyed, 0,
                    self::PUBNOTES, self::DAMAGECLASS
                ));
            }

            if (!empty($entry['c'])) {
                $params = (!empty($entry['p']) && is_array($entry['p'])) ? $entry['p'] : array();
                foreach ($entry['c'] as $type => $count) {
                    if (!self::isValidCriticalType($type)) continue;
                    $param = self::isParamCriticalType($type)
                        ? self::sanitiseParam(isset($params[$type]) ? $params[$type] : null)
                        : null;
                    $last = null;
                    for ($i = 0; $i < (int)$count; $i++) {
                        //Deliberately TURN (0) even for transient classes, unlike toEntries:
                        //this is the lobby PREVIEW of a loaded fleet and the lobby sits at
                        //turn 0, so a turn-1 stamp would render as "not there yet". The
                        //authoritative write - the one that decides when the effect actually
                        //bites - is toEntries at buy time.
                        $crit = new $type(-1, $ship->id, $system->id, $type, self::TURN, 0);
                        $crit->param = $param;
                        $system->setCritical($crit, self::TURN);
                        $last = $crit;
                    }
                    //ShipSystem::$critData[$class] = description is how crit text already
                    //reaches the client; SystemInfo.getCriticals reads it and otherwise
                    //falls back to the raw class name. Read it off the crit we just built,
                    //not off the probe: a param crit's getDescription() reads $param, so a
                    //probe would say "Damage reduction reduced by 0".
                    if ($last !== null) {
                        $system->critData[$type] = $last->getDescription();
                    } else {
                        $probe = self::critProbe($type);
                        if ($probe) $system->critData[$type] = $probe->getDescription();
                    }
                }
            }
        }
    }

    /**
     * {critClass => description} for just the classes present in $clean, so the lobby's
     * SystemInfo popup can name a carried critical.
     *
     * ⚠️ Keyed by CLASS, so it cannot express a PARAM-carrying critical: the same class
     * may sit on two systems with different params, and its getDescription() reads the
     * param ("Damage reduction reduced by 8"). Those classes are omitted here and the
     * client builds their label from the payload's `p` value instead
     * (battleDamage.PARAM_CRITICALS, the mirror of $paramCriticals).
     */
    public static function describeCriticals($clean)
    {
        $out = array();
        foreach (self::criticalTypesIn($clean) as $type) {
            if (self::isParamCriticalType($type)) continue;
            $probe = self::critProbe($type);
            $out[$type] = $probe ? $probe->getDescription() : $type;
        }

        return $out;
    }

    /**
     * {critClass => true} for the TEMPORARY classes present in $clean, so the lobby's
     * critical-effects list can label them "turn 1 only" rather than showing a one-turn
     * effect as though it were a lasting wound. Same probe isTransientCriticalType uses,
     * so the label and the turn the row is written at can never disagree.
     */
    public static function transientCriticals($clean)
    {
        $out = array();
        foreach (self::criticalTypesIn($clean) as $type) {
            if (self::isTransientCriticalType($type)) $out[$type] = true;
        }

        return $out;
    }

    /* ------------------------------------------------------------------ *
     *  The catalogue — what a player may be OFFERED (plan §11.2)
     * ------------------------------------------------------------------ */

    /**
     * The criticals this system may be offered in the lobby editor.
     *
     * ⚠️ A DIFFERENT QUESTION from isValidCriticalType, and deliberately so (plan §4.2's
     * seam): that one asks "may this be STORED", and narrowing it to possibleCriticals
     * would silently eat carried combat crits (AmmoExplosion, OSATThrusterCrit and
     * friends are applied by bespoke code and appear in no possibleCriticals table). This
     * one asks "what may a player INVENT here", which is the system's own hit-chart table
     * PLUS its $preBattleCriticals extras, minus anything that would be refused on the way
     * in anyway. The editor's "All" switch WIDENS this with $generalCriticals; it never
     * replaces it.
     */
    public static function offerableCriticalTypes($system)
    {
        if (!$system) return array();

        //getPreBattleCriticalTypes() = the hit chart PLUS the system's own
        //$preBattleCriticals extras (see the note on ShipSystem::$preBattleCriticals).
        //The method_exists guard keeps this working against anything that is not a real
        //ShipSystem.
        if (method_exists($system, 'getPreBattleCriticalTypes')) {
            $offered = $system->getPreBattleCriticalTypes();
        } else if (method_exists($system, 'getPossibleCriticalTypes')) {
            $offered = $system->getPossibleCriticalTypes();
        } else {
            return array();
        }

        $out = array();
        foreach ($offered as $type) {
            if (self::isValidCriticalType($type)) $out[] = $type;
        }

        return $out;
    }

    /**
     * ⭐ THE GENERAL LIST — what the editor's "All" switch offers, on any system.
     *
     * ⚠️ EDIT THIS ARRAY. It is the whole point of it: it is a hand-curated list, not a
     * derived one, and it is meant to be argued with.
     *
     * It used to be "every storable Critical class in the game", scraped out of
     * cricialClasses.php — 58 entries, most of which mean nothing on most systems
     * (a Reactor cannot have a turret jammed, an Engine has no ammunition to explode),
     * so the dropdown was unusable as a picker (user request 2026-08-08). A curated 12
     * replaced it, and that in turn was cut to the ONE below.
     *
     * THE TEST FOR MEMBERSHIP is that the effect is read GENERICALLY, by code that knows
     * nothing about which system it is looking at. ArmorReduced qualifies: weapon.php's
     * getSystemArmourBase asks it of whatever system is being shot at. Almost nothing else
     * does - the OutputReduced ladder and the burnouts work through effectCriticals' output
     * sum, which is generic, but they read as noise on most systems; everything else is
     * scoped to a weapon (GunLost, ReducedArcs, AmmoExplosion…), the C&C (ReducedIniative,
     * PenaltyToHit, CommunicationsDisrupted), a thruster (HalfEfficiency,
     * FirstThrustIgnored), a shield (DamageReduction*) or one faction's hull. Those belong
     * on the SYSTEM, in its own $preBattleCriticals.
     *
     * ⚠️ `OutputReduced` and `OutputReducedOneTurn` must never be added: they carry no
     * outputMod of their own (they expect a setParam), so authoring one stores a wound that
     * does nothing.
     *
     * ⚠️ THIS IS NOT THE STORABILITY TEST. Narrowing isValidCriticalType would eat
     * carried combat crits on reload (plan §4.2's seam). This list only decides what the
     * picker OFFERS with "All" ticked; a scenario author who wants a system-specific
     * effect adds it to THAT SYSTEM's $preBattleCriticals, which is the mechanism that
     * replaced "offer everything, everywhere".
     */
    public static $generalCriticals = array(
        // //ArmourRedcued added to shipSystem->$preBattleCrits instead of here since it was the only example for now.  
        // And 'All' checkbox removed from crit section.  Leave in for now, in case useful later.        
        //'ArmorReduced',      

    );

    /**
     * The editor's "all effects" list: $generalCriticals, re-validated.
     *
     * Re-validated rather than trusted, so a typo or a name that later joins the deny
     * list drops out here instead of being offered and then silently refused on write.
     */
    public static function allCriticalTypes()
    {
        static $cache = null;
        if ($cache !== null) return $cache;

        $cache = array();
        foreach (self::$generalCriticals as $type) {
            if (self::isValidCriticalType($type)) $cache[] = $type;
        }

        return $cache;
    }

    /**
     * {critClass => label} for a list of classes, for the editor's picker. Param-carrying
     * classes are omitted from describeCriticals for good reason (their text reads the
     * param), but a picker still has to name them - so their label here is the wording
     * WITHOUT the number, which is exactly what the client shows beside the param ticker.
     */
    public static function criticalCatalogueMeta($types)
    {
        $out = array();
        foreach ((array)$types as $type) {
            if (isset($out[$type])) continue;
            $probe = self::critProbe($type);
            $out[$type] = array(
                'label'     => $probe ? $probe->getDescription() : $type,
                'limit'     => self::criticalLimit($type),
                'param'     => self::isParamCriticalType($type),
                'transient' => self::isTransientCriticalType($type),
            );
        }

        return $out;
    }

    /** Every distinct critical class named anywhere in a payload. */
    private static function criticalTypesIn($clean)
    {
        $types = array();
        if (!is_array($clean)) return $types;

        foreach (array_keys(self::BUCKETS) as $bucket) {
            if (empty($clean[$bucket]) || !is_array($clean[$bucket])) continue;
            foreach ($clean[$bucket] as $entry) {
                if (empty($entry['c']) || !is_array($entry['c'])) continue;
                foreach ($entry['c'] as $type => $count) {
                    $types[$type] = true;
                }
            }
        }

        return array_keys($types);
    }
}
