<?php
/**
 * CPU opponent - what a bot can see and what its guns can do about it: the ENGAGEMENT TABLE
 * (TACTICAL_AI_PLAN.md section 5.1). Built for one decision and thrown away with it.
 *
 * ⚠️ FIRING LEGALITY IS THE CLIENT'S, exactly as movement legality is. The server writes any fire
 * order whose weapon id names a weapon (Firing::validateFireOrders) and resolves it: arc, range,
 * line of sight, "is it loaded", "is it switched on" are all asked by weaponManager.js alone. So an
 * order built here that the page would have refused is a bot CHEATING, and nothing downstream
 * would stop it. shot() is therefore a PORT of weaponManager.selectWeapon + targetShip, and
 * tests/ai/fireOracle.js replays its answers through the real weaponManager.js. Change a targeting
 * rule in the client and it must be ported here; change anything here and run both harness halves.
 *
 * The port is deliberately NARROWER than the page (principle P4): only direct-fire weapons with
 * nothing special about them, only uncalled shots, only at an enemy. Whatever it does not
 * understand it leaves alone - weaponBlock() and unitBlock() say why.
 *
 * Hit chance is never re-derived: it is Weapon::calculateHitBase on a tentative FireOrder, the
 * formula the Fire phase itself will use. ⚠️ That method is not free of side effects - an Expert
 * Gunner's once-per-battle bonus is SPENT (written to the database) by a Piercing or a called
 * shot - so with officers in the game Piercing modes are not looked at, and nothing here calls a shot.
 *
 * The gamedata handed in must be the bot's own load AFTER prepareForPlayer(): the per-viewer mask
 * is what keeps this honest (principle P2), and its beforeTurn() sweep is what fills the same
 * weapon fields the page reads.
 */
class AIView
{
    /** @var TacGamedata */
    public $gamedata;
    public $botId;
    public $team;
    public $turn;
    /* Which weapons are being aimed: ballistic ones in Initial Orders (stage T4 - the page takes
       a ballistic launch in that phase and no other), direct fire in every other phase. */
    public $ballistics = false;

    private $blocked;             //blockedHexes, or an empty array
    private $shots = array();     //cache: "shooter:weapon:mode:target" => shot or false
    private $threat = array();    //cache: enemy id => expected damage it can deal
    private $why = array();       //cache: the same key as $shots => why there is no shot ('arc', 'range', ...)
    private $refusal = null;
    private $sent = array();      //cache: weapon object id => array(its public properties, its payload or null)
    private $mountOrder = array(); //cache: unit id => weapon object id => place in mounts()
    private $shieldedUnits = array(); //cache: unit id => does it carry a Shield-type defence (shielded() - stage T9)
    private $worst = array();     //cache: unit id => worstExposure()
    private $overlaps = array();  //cache (stage T8): unit id => whole bearing => do 2+ entries of its hit table hold it ('locations': the table)

    //Where TacGamedata::hideStealthShipMovement puts a unit this viewer may not see.
    const HIDDEN_Q = -10000;

    /* The seat's level and seed (stage T5f): what the bot is able to do (skill()) and what its random choices are drawn
       from (noise()). A view made without them plays Normal. */
    public $level = AIConfig::DEFAULT_LEVEL;
    private $seed = 0;
    //The enemy unit id the fleet's heavy guns go for this Movement phase (stage T5f-3, the skill 'fleetFocus'), or null.
    public $focus = null;

    public function __construct(TacGamedata $gamedata, $botId, $level = AIConfig::DEFAULT_LEVEL, $seed = 0)
    {
        $this->gamedata = $gamedata;
        $this->botId = (int)$botId;
        $this->turn = (int)$gamedata->turn;
        $this->ballistics = ((int)$gamedata->phase === 1);
        $this->team = $gamedata->getPlayerTeam();
        $this->blocked = is_array($gamedata->blockedHexes) ? $gamedata->blockedHexes : array();
        $this->level = isset(AIConfig::SKILLS[$level]) ? $level : AIConfig::DEFAULT_LEVEL;
        $this->seed = (int)$seed;
    }

    /* For the ladder harness only (selfPlayHarness --skill=hard:manoeuvres=swap): level => skill => value, read before
       AIConfig::SKILLS - a tuning question asked without editing the levels. Nothing in a game sets it. */
    public static $overrides = array();

    //One of the level's player skills (AIConfig::SKILLS).
    public function skill($name)
    {
        if (isset(self::$overrides[$this->level]) && array_key_exists($name, self::$overrides[$this->level])) return self::$overrides[$this->level][$name];
        return AIConfig::SKILLS[$this->level][$name];
    }

    /* A draw from the standard normal distribution, fixed by the seat's seed, the turn and $key - the same question gets
       the same answer, so a game replays exactly (plan 5.8). The global generators are left alone. */
    public function noise($key)
    {
        $words = unpack('N2', hash('sha256', $this->seed . '|' . $this->turn . '|' . $key, true));
        $u1 = ($words[1] + 1) / 4294967297;
        $u2 = ($words[2] + 1) / 4294967297;
        return sqrt(-2 * log($u1)) * cos(2 * M_PI * $u2);
    }

    // ------------------------------------------------------------------ units

    //Is this unit on the board and alive, as far as this viewer can tell?
    public function onBoard($unit)
    {
        if ($unit->isDestroyed() || $unit->isTerrain()) return false;
        if ($unit->getTurnDeployed($this->gamedata) > $this->turn) return false;
        if (!empty($unit->removed)) return false; //docked in a hangar, or gone through a jump point

        $last = $unit->getLastMovement();
        if (!$last || !$last->position) return false;
        if ((int)$last->position->q <= self::HIDDEN_Q) return false; //hidden from this viewer

        return true;
    }

    //The bot's own units that are on the board.
    public function myUnits()
    {
        $units = array();
        foreach ($this->gamedata->ships as $unit) {
            if ((int)$unit->userid === $this->botId && $this->onBoard($unit)) $units[] = $unit;
        }
        return $units;
    }

    //Every unit the bot may aim at: hostile, visible, and a thing the page lets a player click.
    public function enemies()
    {
        $units = array();
        foreach ($this->gamedata->ships as $unit) {
            if ($this->team === null || $unit->team == $this->team) continue;
            if ((int)$unit->userid === $this->botId) continue;
            if (!$this->onBoard($unit)) continue;
            if ($unit->Huge > 0 || !$unit->isTargetableBy()) continue;
            $units[] = $unit;
        }
        return $units;
    }

    /* Why NONE of this unit's weapons may be given an order by the bot, or null when they may.
       The first group is the page's own refusals (weaponManager.targetShip's opening lines and
       selectWeapon); the second is P4 - states the bot has no handler for. */
    public function unitBlock($unit)
    {
        if (!$this->onBoard($unit)) return 'not on the board';

        $flight = ($unit instanceof FighterFlight);
        if (!$flight && ($unit->isDisabled() || $this->pageDisabled($unit))) return 'disabled';
        if (!empty($unit->remoteControl)) return 'remote-controlled flight'; //Hunter-Killers: the server may be driving it
        if (HangarOps::bayCarrierAttachedTo($unit, $this->gamedata) !== null) return 'riding a docking bay';

        if (!empty($unit->trueStealth)) return 'stealth unit';        //firing reveals it: a decision the bot cannot make yet
        if (!empty($unit->mine)) return 'mine';                       //fires by itself
        if (!empty($unit->attached) || !empty($unit->hasAttached)) return 'attached to another unit';
        if (!empty($unit->skinDancing)) return 'skin dancing';
        if (JumpEngine::getUnitJumpingEngine($unit, $this->turn) !== null) return 'jumping out';

        return null;
    }

    /* shipManager.isDisabled, as the page asks it: a C&C carrying a "ship disabled" critical from
       before this turn. The server's own isDisabled() is asked as well (above) and the two do not
       always agree - an OSAT is never disabled to the server - so the bot fires only when BOTH
       let it. A base is the page's exception: it all but never calls one disabled. */
    private function pageDisabled($unit)
    {
        if (!empty($unit->base)) return false;

        $before = $this->turn - 1;
        foreach ($unit->systems as $system) {
            if ($system->displayName !== 'C&C') continue;
            foreach ($system->criticals as $critical) {
                if ($critical->phpclass !== 'ShipDisabledOneTurn' && $critical->phpclass !== 'ShipDisabled') continue;
                if ($critical->turn <= $before && ($critical->turnend == 0 || $critical->turnend >= $before)) return true;
            }
        }
        return false;
    }

    // ------------------------------------------------------------------ weapons

    /* The weapons of $unit the bot may order to fire now, as array('weapon' => Weapon,
       'fighter' => the Fighter carrying it, or null on a ship). */
    public function weapons($unit)
    {
        $list = array();
        if ($this->unitBlock($unit) !== null) return $list;

        foreach ($this->mounts($unit) as $mount) {
            if ($this->weaponBlock($unit, $mount['weapon'], $mount['fighter']) === null) $list[] = $mount;
        }
        return $list;
    }

    //Where a weapon comes in its unit's mounts() - the order the server lists a unit's fire orders in.
    public function mountIndex($unit, Weapon $weapon)
    {
        $key = (int)$unit->id;
        if (!isset($this->mountOrder[$key])) {
            $this->mountOrder[$key] = array();
            foreach ($this->mounts($unit) as $index => $mount) $this->mountOrder[$key][spl_object_id($mount['weapon'])] = $index;
        }
        $id = spl_object_id($weapon);
        return isset($this->mountOrder[$key][$id]) ? $this->mountOrder[$key][$id] : 0;
    }

    //Every weapon mount of a unit, usable or not.
    public function mounts($unit)
    {
        $list = array();
        if ($unit instanceof FighterFlight) {
            foreach ($unit->systems as $fighter) {
                if (!($fighter instanceof Fighter)) continue;
                foreach ($fighter->systems as $system) {
                    if ($system instanceof Weapon) $list[] = array('weapon' => $system, 'fighter' => $fighter);
                }
            }
        } else {
            foreach ($unit->systems as $system) {
                if ($system instanceof Weapon) $list[] = array('weapon' => $system, 'fighter' => null);
            }
        }
        return $list;
    }

    /* Why the bot leaves this weapon alone this Fire phase, or null. "Not a plain gun" first
       (P4), then the page's own tests in the weapon's CURRENT mode; shot() asks loaded() again
       for the mode it tries. */
    public function weaponBlock($unit, Weapon $weapon, $fighter = null)
    {
        if ($weapon instanceof DualWeapon || $weapon instanceof DuoWeapon) return 'compound weapon';
        if ($weapon instanceof JumpEngine) return 'jump engine';
        if ($weapon->isRammingAttack) return 'ramming';
        if ((bool)$weapon->ballistic !== $this->ballistics) return $weapon->ballistic ? 'ballistic' : 'direct fire';
        if ($weapon instanceof AmmoMissileRackS) return $this->rackBlock($unit, $weapon, $fighter);
        if ($weapon->preFires) return 'pre-firing weapon';
        if ($weapon->hextarget || !empty($weapon->hextargetArray)) return 'hex-targeted';
        if ($weapon->autoFireOnly || !empty($weapon->autoFireOnlyArray)) return 'fires by itself';
        if (!$this->plainMode($weapon)) return 'split shots'; //stage T9 (E1): only a weapon that splits in EVERY mode - see splitsIn()
        //from here on some flags are read from the weapon's payload (flag()): only for a weapon that got this far
        if ($this->flag($weapon, 'duoWeapon') || $this->flag($weapon, 'dualWeapon')) return 'compound weapon';
        if ($this->flag($weapon, 'isBoardingAction')) return 'boarding';
        if ($weapon->splitArcs || $this->flag($weapon, 'specialArcs')) return 'special arcs';
        if ($this->flag($weapon, 'hasSpecialTargeting') || $this->flag($weapon, 'canOnlyCalledShot') || $this->flag($weapon, 'reinforceAmount')) return 'special targeting';
        if ($weapon->linkedFiringGroup !== null || $this->flag($weapon, 'flightExclusiveGroup')) return 'linked firing';
        /* Stage T9 (E9): a DIRECT-FIRE exclusive weapon (the Sitara's Ion Bolt, the Phalan's Plasma Gun) is offered like any gun -
           FireDecider::conflicts keeps a craft to it or to its other guns, as the page does (the test at the end of this function, and
           weaponManager.checkConflictingFireOrder). A ballistic one stays left alone: BallisticDecider has no such rule. */
        if ($weapon->exclusive && $weapon->ballistic) return 'exclusive weapon';
        if ($weapon->weaponClass === 'Support' || $this->flag($weapon, 'canTargetAllies') || $this->flag($weapon, 'canTargetAlliesArray')) return 'support weapon';
        //Sustained fire is something a player switches on (an overload, in Initial Orders); the bot never does,
        //and a Heavy Laser that is not overloading is a plain gun.
        if ($weapon->alwaysoverloading || $weapon->isOverloadingOnTurn($this->turn)) return 'overload / sustained fire';
        /* Stage T9 (E2 - the user, 2026-11-23: limited-ammo weapons "Just fire freely until empty for now"), as the page's
           checkOutOfAmmo asks: a weapon with an `ammunition` count (the gatlings) fires while it is above 0; an old missile rack
           (MissileLauncher, its rounds in `missileArray`) fires in a mode with a round left (modes() offers only those) on a flight,
           and freely on a ship - the page counts no ship rack's rounds, and the server's ship racks keep none. A weapon fed from a
           magazine (AmmoDirectWeapon) is still left alone. */
        $ammunition = $this->flag($weapon, 'ammunition');
        if ($ammunition !== null && (int)$ammunition <= 0) return 'out of ammunition';
        if ($weapon->checkAmmoMagazine) return 'limited ammunition';
        if ($this->flag($weapon, 'missileArray')) {
            if (!($weapon instanceof MissileLauncher)) return 'limited ammunition';
            if (count($this->modes($weapon)) == 0) return 'out of ammunition';
        }
        if ($this->flag($weapon, 'specialHitChanceCalculation') || $this->flag($weapon, 'specialRangeCalculation') || $this->flag($weapon, 'specialHitChanceCalculationArray') || $this->flag($weapon, 'specialRangeCalculationArray')) return 'special hit calculation';
        if (count($weapon->getFixedFireControlModes()) > 0) return 'fixed fire control';

        if ($weapon->isDestroyed()) return 'destroyed';
        if ($fighter !== null && $fighter->isDestroyed()) return 'fighter destroyed';
        if ($weapon->isOfflineOnTurn($this->turn)) return 'offline';
        if ($weapon->stowed) return 'stowed';
        if ($weapon->firedOnTurn($this->turn) || count($this->ordersThisTurn($weapon)) > 0) return 'already has an order this turn';
        if (!$this->loaded($weapon)) return 'not loaded';

        //weaponManager.checkConflictingFireOrder: on the same craft (or ship) a weapon of the same kind - direct or ballistic - has an
        //order, and one of the two is exclusive (stage T9: both ways, as the page asks it; it was only "the other is exclusive")
        foreach (($fighter !== null ? $fighter->systems : $unit->systems) as $other) {
            if ($other === $weapon || !($other instanceof Weapon) || (bool)$other->ballistic !== (bool)$weapon->ballistic) continue;
            if (!$other->exclusive && !$weapon->exclusive) continue;
            if (count($this->ordersThisTurn($other)) > 0) return 'an exclusive weapon is firing';
        }

        return null;
    }

    /* Stage T9: weaponBlock's reasons that mean the bot never fires that KIND of weapon (principle P4) - not this turn's state of it
       (lost, off, reloading, empty, already firing). */
    const NEVER_FIRED = array('compound weapon', 'pre-firing weapon', 'hex-targeted', 'split shots', 'boarding', 'special arcs',
        'special targeting', 'linked firing', 'exclusive weapon', 'support weapon', 'overload / sustained fire', 'limited ammunition',
        'special hit calculation', 'fixed fire control');

    /* Does the bot fire this kind of weapon at all, in the phase that is its own (direct fire or ballistic)? Posture::usableGuns asks it
       (stage T9): a unit whose guns are all of kinds it never fires - Antimatter Converters, Gravity Nets, Point Pulsars - cannot fight,
       for surrender, jumping out and the last stand. */
    public function firesKind($unit, Weapon $weapon, $fighter = null)
    {
        $kept = $this->ballistics;
        $this->ballistics = (bool)$weapon->ballistic;
        try {
            $why = $this->weaponBlock($unit, $weapon, $fighter);
        } finally {
            $this->ballistics = $kept;
        }
        return !in_array($why, self::NEVER_FIRED, true);
    }

    /* A MISSILE RACK fed from an Ammunition Magazine (AmmoMissileRackS and everything built on it,
       a fighter's rack included). Its firing modes are the kinds of round in the magazine, and
       every per-mode array it carries is filled for every round, special or not - so the general
       tests above ("has a hex-targeted mode", "has a special range calculation") would turn
       every rack away. A rack is judged by the ROUND instead: modes() offers only the plain
       damaging ones (AIConfig::BALLISTIC_ROUNDS) that the magazine still holds. */
    private function rackBlock($unit, AmmoMissileRackS $weapon, $fighter)
    {
        if ($weapon->isDestroyed()) return 'destroyed';
        if ($fighter !== null && $fighter->isDestroyed()) return 'fighter destroyed';
        if ($weapon->isOfflineOnTurn($this->turn)) return 'offline';
        if ($weapon->stowed) return 'stowed';
        if ($weapon->hasRamProw()) return 'ramming prow';
        if ($weapon->exclusive) return 'exclusive weapon';
        /* A firing link (weaponManager.getLinkedFiringBlock). The SAME-TARGET kind (no spread: the Thunderbolt's and the Armed
           Missile Shuttle's twin racks - both missiles of one craft at one unit) is kept by BallisticDecider (linkKey); before
           stage T6a it was refused here, and the Thunderbolts of game 4774 never launched a missile. An angular spread (the Vree
           turrets) is not modelled. */
        if ($weapon->linkedFiringGroup !== null && $weapon->linkedFiringSpread !== null) return 'linked firing';
        if ($weapon->firedOnTurn($this->turn) || count($this->ordersThisTurn($weapon)) > 0) return 'already has an order this turn';
        if (!$this->loaded($weapon)) return 'not loaded';
        if (count($this->modes($weapon)) == 0) return 'no round the bot uses';

        foreach (($fighter !== null ? $fighter->systems : $unit->systems) as $other) {
            if ($other === $weapon || !($other instanceof Weapon) || !$other->exclusive || !$other->ballistic) continue;
            if (count($this->ordersThisTurn($other)) > 0) return 'an exclusive weapon is firing';
        }

        return null;
    }

    //The rounds of one kind a rack's magazine still holds, as the page counts them (AmmoMagazine.ammoCountArray).
    public function rounds(AmmoMissileRackS $weapon, $mode)
    {
        $magazine = $weapon->getAmmoMagazine();
        $name = isset($weapon->firingModes[$mode]) ? $weapon->firingModes[$mode] : null;
        if (!$magazine || $name === null || !isset($magazine->ammoCountArray[$name])) return 0;
        return (int)$magazine->ammoCountArray[$name];
    }

    /* A weapon flag AS THE PAGE HAS IT, or null. Many of the flags that steer the page's targeting
       are protected on the server and reach the page only where a class sends them from its own
       stripForJson - and some have a default here that means nothing (Weapon::$specialArcs is
       true on every weapon). So: a public property is read as it is; anything else counts only
       if the weapon's payload carries it. */
    private function flag(Weapon $weapon, $name)
    {
        $key = spl_object_id($weapon);
        if (!isset($this->sent[$key])) $this->sent[$key] = array(get_object_vars($weapon), null);

        if (array_key_exists($name, $this->sent[$key][0])) return $this->sent[$key][0][$name];

        if ($this->sent[$key][1] === null) {
            try {
                $this->sent[$key][1] = (array)$weapon->stripForJson();
            } catch (Throwable $e) {
                $this->sent[$key][1] = array();
            }
        }
        return isset($this->sent[$key][1][$name]) ? $this->sent[$key][1][$name] : null;
    }

    //weaponManager.isLoaded, on the fields the page is sent.
    public function loaded(Weapon $weapon)
    {
        return $weapon->loadingtime <= $weapon->turnsloaded || $weapon->loadingtime <= $weapon->overloadturns;
    }

    private function ordersThisTurn(Weapon $weapon)
    {
        $orders = array();
        foreach ($weapon->fireOrders as $fire) {
            if ((int)$fire->turn === $this->turn) $orders[] = $fire;
        }
        return $orders;
    }

    /* Stage T9, enabler E1 (TACTICAL_AI_PLAN.md 10.10): does $weapon split its shots in $mode? The page sends a splitting mode
       through the weapon's own doMultipleFireOrders (weaponManager.targetShip) - an order a click, each at a unit of its own: the
       Twin Array family's 'Split' - and every other mode through its plain loop, an order a gun, all at the unit clicked. A weapon
       with a per-mode list splits as the list says (the page's updateFiringModeData: a mode the list leaves out does not split);
       one without it splits in every mode or in none.
       The user's ruling of 2026-11-23: "Split Weapons like Twin Arrays ... almost always fire all their shots at the same target in
       practice using Firing Mode 1" - so the bot orders a weapon in its plain modes only (modes()), and a weapon with none (the
       Ballistic Torpedo, the slicers) stays left alone (weaponBlock). Hard's own split: splitShot() and FireDecider::splitGuns. */
    public function splitsIn(Weapon $weapon, $mode)
    {
        if (!empty($weapon->canSplitShotsArray)) return !empty($weapon->canSplitShotsArray[(int)$mode]);
        return (bool)$weapon->canSplitShots;
    }

    //Has $weapon a firing mode that does not split its shots (splitsIn)?
    public function plainMode(Weapon $weapon)
    {
        foreach (array_keys($weapon->firingModes) as $mode) {
            if (!$this->splitsIn($weapon, (int)$mode)) return true;
        }
        return false;
    }

    //The firing modes of a weapon the bot will consider.
    public function modes(Weapon $weapon)
    {
        $modes = array();
        if ($weapon instanceof AmmoMissileRackS) {
            foreach ($weapon->firingModes as $mode => $name) {
                if (in_array($name, AIConfig::BALLISTIC_ROUNDS, true) && $this->rounds($weapon, (int)$mode) > 0) $modes[] = (int)$mode;
            }
            return $modes;
        }
        /* Stage T9 (E2): an OLD missile rack (MissileLauncher - its rounds in missileArray, a mode each): on a flight only a mode with a
           round left (the page's checkOutOfAmmo; a fighter rack's fire() spends one or fizzles - so a fighter rack on anything else is
           counted too); on a ship every mode (the page asks no count, the server keeps none). */
        if ($weapon instanceof MissileLauncher) {
            $counted = ($weapon instanceof FighterMissileRack) || ($weapon->getUnit() instanceof FighterFlight);
            foreach (array_keys($weapon->firingModes) as $mode) {
                $missile = isset($weapon->missileArray[$mode]) ? $weapon->missileArray[$mode] : null;
                if ($missile === null || ($counted && (int)$missile->amount <= 0)) continue;
                $modes[] = (int)$mode;
            }
            return $modes;
        }
        foreach (array_keys($weapon->firingModes) as $mode) {
            $mode = (int)$mode;
            if ($this->splitsIn($weapon, $mode)) continue; //stage T9 (E1): never a splitting mode - see splitsIn()
            $type = isset($weapon->damageTypeArray[$mode]) ? $weapon->damageTypeArray[$mode] : $weapon->damageType;
            //⚠️ see the header: with officers aboard, pricing a Piercing shot may spend an Expert Gunner's bonus
            if (TacGamedata::$officersPresent && $type === 'Piercing') continue;
            if (TacGamedata::$nebulaPresent && Nebula::isEmDisabled($weapon, $mode)) continue;
            $modes[] = $mode;
        }
        return $modes;
    }

    // ------------------------------------------------------------------ one shot

    /* Can $weapon of $unit, in $mode, legally fire at $target now - and what would it do?
       Null when the page would create no order (or the bot does not understand the case).
       Otherwise:
         'mode', 'chance' (percent, before interception), 'orders' (one fire order per gun),
         'shots' (the shots each order carries), 'damageclass' (as the page stamps it),
         'damage' (expected damage of the whole volley, after armour), 'hitLoc', 'distance',
         'edge' (see onEdge: the page may price this shot differently).
       The weapon is LEFT IN $mode. Answers are remembered for the life of the view. */
    public function shot($unit, Weapon $weapon, $target, $mode)
    {
        $key = $unit->id . ':' . $weapon->id . ':' . $mode . ':' . $target->id;
        if (!array_key_exists($key, $this->shots)) {
            $this->refusal = null;
            $this->shots[$key] = $this->work($unit, $weapon, $target, (int)$mode);
            $this->why[$key] = $this->refusal;
        }
        if ($this->shots[$key] !== null && (int)$weapon->firingMode !== (int)$mode) $weapon->changeFiringMode((int)$mode);
        return $this->shots[$key];
    }

    /* Why shot() answered null for this pairing: 'mode', 'unloaded', 'target', 'fc' (no fire control
       for that size of unit), 'los', 'arc', 'range', 'chance', or 'edge' (a launch from exactly between
       two of the target's sections on a table the bot cannot price without a die - hitBase). Null when
       there is a shot, or it was never asked. */
    public function why($unit, Weapon $weapon, $target, $mode)
    {
        $key = $unit->id . ':' . $weapon->id . ':' . $mode . ':' . $target->id;
        return isset($this->why[$key]) ? $this->why[$key] : null;
    }

    private function refuse($reason)
    {
        $this->refusal = $reason;
        return null;
    }

    /* Stage T9, enabler E1 - Hard's split (the skill 'splitShots'): ONE gun of $weapon in its splitting $mode at $target - the order
       one click makes on the page (the Twin Array family's doMultipleFireOrders: one order, one shot, damage class 'Sweeping'), priced
       as shot() prices a volley: 'orders' 1, 'damage' one gun's share. Null where the page would make no such order, or for a weapon
       whose page split the bot has not ported (SPLIT_FAMILIES). The weapon is LEFT IN $mode; answers are remembered as shot()'s. */
    public function splitShot($unit, Weapon $weapon, $target, $mode)
    {
        $key = 'split:' . $unit->id . ':' . $weapon->id . ':' . $mode . ':' . $target->id;
        if (!array_key_exists($key, $this->shots)) {
            $this->refusal = null;
            $shot = null;
            if (!in_array(get_class($weapon), self::SPLIT_FAMILIES, true) || !$this->splitsIn($weapon, (int)$mode)) $this->refuse('mode');
            else $shot = $this->work($unit, $weapon, $target, (int)$mode, true);
            if ($shot !== null) {
                $guns = max(1, $shot['orders']);
                $shot['damage'] = $shot['damage'] / $guns;
                $shot['orders'] = 1;
                $shot['shots'] = 1;
                $shot['damageclass'] = 'Sweeping';
            }
            $this->shots[$key] = $shot;
            $this->why[$key] = $this->refusal;
        }
        if ($this->shots[$key] !== null && (int)$weapon->firingMode !== (int)$mode) $weapon->changeFiringMode((int)$mode);
        return $this->shots[$key];
    }

    /* Stage T9 (the user's note 4 - AREA-EFFECT weapons): can $weapon of $unit be launched at $hex now, as weaponManager.targetHex takes
       it, and what would the page make of it? Only the weapons of AIConfig::AOE_WEAPONS (an Energy Mine: ballistic, hex-targeted - the
       page's plain loop, one order a gun, no unit, the hex in x / y). The page's tests: Initial Orders (a ballistic), the unit able to
       fire, the weapon whole, loaded and switched on, no order on it this turn (the bot never re-aims one), the hex inside its arc, no
       blocked hex in the line, within its range (0 = any). Null when it cannot; else array('mode', 'orders', 'shots', 'damageclass').
       ⚠️ The page's targetHex ENDS the whole click on a blocked line - for the bot that is simply no launch at that hex. */
    public function hexShot($unit, Weapon $weapon, OffsetCoordinate $hex)
    {
        if (!$this->ballistics || !$weapon->ballistic || !$weapon->hextarget || $weapon->autoFireOnly) return null;
        if (!AoEDecider::isAoe($weapon) || $this->unitBlock($unit) !== null) return null;
        if ($weapon->isDestroyed() || $weapon->isOfflineOnTurn($this->turn) || !$this->loaded($weapon)) return null;
        if ($weapon->firedOnTurn($this->turn) || count($this->ordersThisTurn($weapon)) > 0) return null;
        if ($weapon->splitArcs || $this->flag($weapon, 'specialArcs')) return null; //the page asks such a weapon itself (isPosOnSpecialArc)
        if (!$this->onArcHex($unit, $weapon, $hex)) return null;
        $from = $unit->getHexPos();
        if (!$weapon->ignoresLoS && count($this->blocked) > 0 && Mathlib::isLoSBlocked($from, $hex, $this->blocked)) return null;
        if ($weapon->range != 0 && $from->distanceTo($hex) > $weapon->range) return null;
        $type = isset($weapon->data['Weapon type']) ? (string)$weapon->data['Weapon type'] : (string)$weapon->weaponClass;
        return array('mode' => (int)$weapon->firingMode, 'orders' => max(1, (int)$weapon->guns), 'shots' => (int)$weapon->defaultShots, 'damageclass' => strtolower($type));
    }

    /* The weapons whose page split splitShot() is a port of - by EXACT class, as the page picks a system's prototype by its class:
       the Twin Array, the Heavy Array, the Quad Array (mode 5) and the Quad Particle Beam share one doMultipleFireOrders shape (one
       order a click, one shot, 'Sweeping', until every gun has one). Not the Unreliable Twin Array: its page prototype is a plain
       Particle's, so a click in its Split mode makes no order at all. */
    const SPLIT_FAMILIES = array('TwinArray', 'HeavyArray', 'QuadArray', 'QuadParticleBeam');

    private function work($unit, Weapon $weapon, $target, $mode, $split = false)
    {
        if (!isset($weapon->firingModes[$mode])) return $this->refuse('mode');
        if ((int)$weapon->firingMode !== $mode) $weapon->changeFiringMode($mode);

        //what a mode may turn the weapon into (a splitting mode only for splitShot)
        if ($weapon->hextarget || $weapon->autoFireOnly || (bool)$weapon->canSplitShots !== $split || (bool)$weapon->ballistic !== $this->ballistics) return $this->refuse('mode');
        if (!$this->loaded($weapon)) return $this->refuse('unloaded');
        if ($weapon->guns < 1) return $this->refuse('mode');

        if ($target->id === $unit->id || $target->team == $unit->team) return $this->refuse('target');
        if (!empty($target->mine) && !empty($unit->mine)) return $this->refuse('target');
        if (!empty($target->skinDancing)) return $this->refuse('target'); //only a unit dancing on the same hull may shoot at it

        //fire control: a null entry is "cannot fire at this size of unit"
        $flightTarget = ($target instanceof FighterFlight);
        $fc = $weapon->fireControl;
        if ($flightTarget) {
            if (!isset($fc[0])) return $this->refuse('fc');
        } else if ($target->shipSizeClass < 2) {
            if (!isset($fc[1])) return $this->refuse('fc');
        } else if (!isset($fc[2])) {
            return $this->refuse('fc');
        }

        $from = $unit->getHexPos();
        $to = $target->getHexPos();
        if (!$weapon->ignoresLoS && count($this->blocked) > 0 && Mathlib::isLoSBlocked($from, $to, $this->blocked)) return $this->refuse('los');

        $distance = $from->distanceTo($to);
        //a ballistic may be launched at a unit in the same hex whatever the arc (weaponManager.isOnWeaponArc)
        if (!($this->ballistics && $distance == 0) && !$this->onArc($unit, $weapon, $target)) return $this->refuse('arc');

        if (!$this->inRange($unit, $weapon, $target, $distance)) return $this->refuse('range');
        //checkIsInRange: a young race's ballistic cannot be launched at a stealth flight more than 5 hexes off (here: nobody's)
        if ($this->ballistics && $flightTarget && $distance > 5 && $target->getSystemByName('Stealth')) return $this->refuse('range');

        $shot = $this->price($unit, $weapon, $target, $mode, $distance, $this->ballistics);
        if ($shot === null) return $this->refuse('edge'); //a launch the engine would price on a die (hitBase)
        //the page makes no order for a direct shot that cannot hit; it would launch such a ballistic, the bot never does
        if ($shot['chance'] < 1) return $this->refuse('chance');
        return $shot;
    }

    /* The real formula, on an order that exists only here - the shot shot() describes, legality
       aside. Null when it cannot be priced without a die (hitBase). 'perHit' is what one hit of the
       likeliest section does, for re-pricing the shot at a lower chance (damageAfter). */
    private function price($unit, Weapon $weapon, $target, $mode, $distance, $ballistic)
    {
        $order = new FireOrder(-1, $ballistic ? 'ballistic' : 'normal', $unit->id, $target->id, $weapon->id, -1, $this->turn, $mode, 0, 0, $weapon->defaultShots, 0, 0, null, null, null);
        $answers = $this->hitBase($weapon, $unit, $target, $order);
        if (count($answers) == 0) return null;
        $chance = 0;
        $expected = 0;
        $hitLoc = null;
        $perHit = 0;
        $likeliest = -1;
        foreach ($answers as $answer) {
            $hit = $this->damagePerHit($weapon, $unit, $target, $answer['loc'], $distance);
            $chance += $answer['weight'] * $answer['needed'];
            $expected += $answer['weight'] * $this->atChance($weapon, $hit, $answer['needed']);
            if ($answer['weight'] > $likeliest) {
                $likeliest = $answer['weight'];
                $hitLoc = $answer['loc'];
                $perHit = $hit;
            }
        }

        return array(
            'mode' => $mode,
            'chance' => (int)round($chance),
            'orders' => (int)$weapon->guns,
            'shots' => (int)$weapon->defaultShots,
            'damageclass' => strtolower((string)$weapon->weaponClass),
            'damage' => $expected,
            'perHit' => $perHit,
            'hitLoc' => $hitLoc,
            'distance' => $distance,
            'edge' => $this->onEdge($unit, $target)
        );
    }

    /* A missile of the bot's own already on its way this turn (a ballistic order of Initial Orders,
       read in the Fire phase): what it should do where its target now stands, priced as shot()
       prices a launch - from its launch hex. Null when it cannot be priced without a die. */
    public function launched($unit, Weapon $weapon, $target, $mode)
    {
        if ((int)$weapon->firingMode !== (int)$mode) $weapon->changeFiringMode((int)$mode);
        $distance = $unit->getHexPos()->distanceTo($target->getHexPos());
        return $this->price($unit, $weapon, $target, (int)$mode, $distance, true);
    }

    /* What ONE gun of a planned shot should do once interception has taken $intercept points of
       chance off it (InterceptModel). The weapon is put in the shot's mode. */
    public function damageAfter(array $shot, Weapon $weapon, $intercept)
    {
        if ((int)$weapon->firingMode !== (int)$shot['mode']) $weapon->changeFiringMode((int)$shot['mode']);
        $chance = $shot['chance'] - $intercept;
        if ($chance <= 0) return 0;
        return $this->atChance($weapon, $shot['perHit'], $chance) / max(1, (int)$weapon->guns);
    }

    /* Does the shooter sit exactly on a line between two of the target's sections or profiles
       (every 30 degrees round it)? There the server ROLLS which of the sections is hit, weighted
       by profile (BaseShip::doGetHitSectionBearing), and takes that section's profile, while the
       page prices the shot on the forward / aft profile. The two can differ by the gap between a
       hull's profiles (10-15 points of hit chance), in either direction. The bot prices such a
       shot at the roll's expectation (hitBase); a decider only has to keep a wider margin on it. */
    public function onEdge($unit, $target)
    {
        return fmod((float)$target->getBearingOnUnit($unit), 30) == 0;
    }

    // ------------------------------------------------------------------ the engine's formula, without its dice

    /* Weapon::calculateHitBase, asked so that it leaves nothing behind and rolls no die.

       Two things in that method are not pure, and both matter to a bot that asks it hundreds of
       times a decision about shots it will mostly never fire:
         - THE SECTION. A shot from exactly between two of the target's sections hits one of them
           at RANDOM, weighted by profile (BaseShip::doGetHitSectionBearing, since 2026-01-12, on
           Dice::d - random_int, which cannot be seeded). The section's profile is the shot's
           defence, so the answer was itself a die roll: the same decision differed run to run.
         - BOOKKEEPING. The section found for a shooter is cached on the target for the rest of
           the request (activeHitLocations), and every shot adds to the target's expected damage
           (setExpectedDamage), which later picks read. So each answer depended on the questions
           asked before it - and a look-ahead to next turn's positions (FireDecider::holdSlow,
           AIView::ahead) would have been priced on THIS turn's section.
       So where the engine would roll, each ENTRY of the target's section table it would roll
       between is asked in turn, forced, and the answers are weighted as the engine weights its
       roll (profile over the sum of the profiles). An entry, not a section: a heavy combat
       vessel's table holds its front section twice, at the bow profile and at the side profile,
       and on the line between them the roll decides only the profile.
         - Direct fire: the entry is put in the very cache the engine reads first (getHitSection,
           getHitSectionProfile), which carries its profile with it - exact, for any table.
         - A ballistic is priced from its launch hex, which no cache serves. Its entry is forced
           by making every other section look destroyed by the damage expected this turn, and its
           profile by setting the hull's two profile figures to it for the call - if the table
           then shows that profile on every entry the bearing holds (forwardDefense / sideDefense
           are what every EA and raider table reads); otherwise no answer, and the bot does not
           launch from that line (a missile is priced on where the target stands NOW and meets it
           wherever it has gone, so the exact line was never worth much).
       Around every call the target's cache entry for this shooter, its expected damage and its
       profiles are put back as they were. The formula is the engine's own throughout; only its
       choice of entry is replaced by that choice's expectation.
       Returns a list of array('weight', 'needed', 'loc'): one entry when the section is certain,
       none when it cannot be priced without a die. */
    private function hitBase(Weapon $weapon, $shooter, $target, FireOrder $order)
    {
        $saved = self::bookkeeping($target, $shooter->id);
        $clean = array(false, null, $saved[2]);
        $profiles = array($target->forwardDefense, $target->sideDefense);
        try {
            self::bookkeeping($target, $shooter->id, $clean);
            $candidates = $this->rolledSections($weapon, $shooter, $target, $order, $saved[2]);
            if (count($candidates) < 2) {
                $weapon->calculateHitBase($this->gamedata, $order);
                return array(array('weight' => 1.0, 'needed' => (int)$order->needed, 'loc' => $order->chosenLocation));
            }

            $total = 0;
            foreach ($candidates as $candidate) $total += max(0, (float)$candidate['profile']);
            $answers = array();
            foreach ($candidates as $candidate) {
                self::bookkeeping($target, $shooter->id, $clean);
                if ($weapon->ballistic) {
                    if (!$this->forceLaunchEntry($target, $candidates, $candidate, $weapon, $order)) return array();
                } else {
                    self::bookkeeping($target, $shooter->id, array(true, $candidate, $saved[2]));
                }
                $try = clone $order;
                $weapon->calculateHitBase($this->gamedata, $try);
                $target->forwardDefense = $profiles[0];
                $target->sideDefense = $profiles[1];
                $weight = ($total > 0) ? max(0, (float)$candidate['profile']) / $total : 1 / count($candidates);
                $answers[] = array('weight' => $weight, 'needed' => (int)$try->needed, 'loc' => $try->chosenLocation);
            }
            return $answers;
        } finally {
            $target->forwardDefense = $profiles[0];
            $target->sideDefense = $profiles[1];
            self::bookkeeping($target, $shooter->id, $saved);
        }
    }

    /* The entries of the target's section table the engine would ROLL between for this shot: those
       whose arc holds the bearing (from the shooter, or from a ballistic's launch hex) and whose
       section still stands after the damage already expected this turn - doGetHitSectionBearing's
       own test, on the same filled entries. Fewer than two: no roll. A flight has no sections of
       its own and is never rolled for. */
    private function rolledSections(Weapon $weapon, $shooter, $target, FireOrder $order, array $expectedDamage)
    {
        if ($target instanceof FighterFlight) return array();

        $valid = array();
        $bearing = $this->launchBearing($weapon, $shooter, $target, $order);
        /* Stage T8: a whole-degree bearing that only one entry of the table holds is never rolled for - known without filling the
           table (asked for every hit chance the bot prices; the arcs of a unit's table do not change in a decision). */
        //(the bearing as it is - 360 included, which the engine gives for dead ahead and which isInArc need not read as 0)
        if ((float)$bearing == floor((float)$bearing) && $bearing >= 0 && $bearing <= 360 && !$this->overlapAt($target, (int)$bearing)) return array();
        foreach ($target->getLocations() as $loc) {
            if (Mathlib::isInArc($bearing, $loc['min'], $loc['max'])) $valid[] = $loc;
        }
        $filled = $target->fillLocations($valid);
        if (!is_array($filled)) return array();

        $live = array();
        foreach ($filled as $loc) {
            $structure = $target->getStructureSystem($loc['loc']);
            if (!$structure) continue;
            $expected = isset($expectedDamage[$loc['loc']]) ? $expectedDamage[$loc['loc']] : 0;
            if ($structure->getRemainingHealth() - $expected > 0) $live[] = $loc;
        }
        return $live;
    }

    /* Do two or more entries of $target's hit table hold this whole bearing (Mathlib::isInArc, the test rolledSections makes)? Only
       around a line between two sections. Remembered per unit and bearing for the view (the arcs of a table do not change in it). */
    private function overlapAt($target, $bearing)
    {
        $id = (int)$target->id;
        if (!isset($this->overlaps[$id][$bearing])) {
            if (!isset($this->overlaps[$id])) $this->overlaps[$id] = array('locations' => $target->getLocations());
            $holding = 0;
            foreach ($this->overlaps[$id]['locations'] as $loc) {
                if (Mathlib::isInArc($bearing, $loc['min'], $loc['max'])) $holding++;
            }
            $this->overlaps[$id][$bearing] = ($holding >= 2);
        }
        return $this->overlaps[$id][$bearing];
    }

    //The bearing the engine picks a section by: the shooter's, or a ballistic's launch hex's.
    private function launchBearing(Weapon $weapon, $shooter, $target, FireOrder $order)
    {
        return $weapon->ballistic
            ? $target->getBearingOnPos(Mathlib::hexCoToPixel($weapon->getFiringHex($this->gamedata, $order)))
            : $target->getBearingOnUnit($shooter);
    }

    /* A ballistic's entry, forced for one call (see hitBase). False when the table does not take
       the profile from the hull's two figures, so the entry cannot be forced; the caller has put
       everything back by then or will. */
    private function forceLaunchEntry($target, array $candidates, array $entry, Weapon $weapon, FireOrder $order)
    {
        $target->forwardDefense = $entry['profile'];
        $target->sideDefense = $entry['profile'];
        $bearing = $this->launchBearing($weapon, null, $target, $order);
        foreach ($target->getLocations() as $loc) {
            if (Mathlib::isInArc($bearing, $loc['min'], $loc['max']) && $loc['profile'] != $entry['profile']) return false;
        }
        self::force($target, $candidates, $entry['loc']);
        return true;
    }

    /* Read ($restore null) or put back a target's section bookkeeping for one shooter:
       array(had a cached section, that section, the expected-damage table). Both live on BaseShip,
       one protected and one private, hence the bound closure. */
    private static function bookkeeping($target, $shooterId, ?array $restore = null)
    {
        $access = function ($shooterId, $restore) {
            if ($restore === null) {
                return array(array_key_exists($shooterId, $this->activeHitLocations),
                    isset($this->activeHitLocations[$shooterId]) ? $this->activeHitLocations[$shooterId] : null, $this->expectedDamage);
            }
            if ($restore[0]) $this->activeHitLocations[$shooterId] = $restore[1];
            else unset($this->activeHitLocations[$shooterId]);
            $this->expectedDamage = $restore[2];
            return $restore;
        };
        return Closure::bind($access, $target, BaseShip::class)($shooterId, $restore);
    }

    /* For one call: every candidate but $loc looks destroyed by the damage expected this turn, so
       the engine has nothing to roll between, and $loc is beyond reproach in the toughness
       comparison that then decides (pickLocationForHit). Undone by bookkeeping(). */
    private static function force($target, array $candidates, $loc)
    {
        $access = function ($candidates, $loc) {
            foreach ($candidates as $candidate) $this->expectedDamage[$candidate['loc']] = ($candidate['loc'] == $loc) ? -1000000 : 1000000;
        };
        Closure::bind($access, $target, BaseShip::class)($candidates, $loc);
    }

    /* weaponManager.isOnWeaponArc: the compass heading of the target from the shooter, against
       the weapon's arc turned by the shooter's facing (and mirrored on a rolled ship). Written
       out rather than asked of BaseShip::getBearingOnUnit, which answers 360 for a unit dead
       ahead and then fails an arc that starts at 0. */
    public function onArc($unit, Weapon $weapon, $target)
    {
        return $this->inArcAt($unit, $weapon, round(Mathlib::getCompassHeadingOfShip($unit, $target)));
    }

    /* Stage T9 (area-effect weapons): weaponManager.isPosOnWeaponArc - the same arc, at the compass heading of a HEX's centre from
       the unit (mathlib.getCompassHeadingOfPoint; the page rounds it in isInArc as onArc does). */
    public function onArcHex($unit, Weapon $weapon, OffsetCoordinate $hex)
    {
        return $this->inArcAt($unit, $weapon, round(Mathlib::getCompassHeadingOfPoint(Mathlib::hexCoToPixel($unit->getHexPos()), Mathlib::hexCoToPixel($hex))));
    }

    //Is the (rounded) compass $heading inside $weapon's arc, turned by $unit's facing - shipManager.systems.getArcs + mathlib.isInArc.
    private function inArcAt($unit, Weapon $weapon, $heading)
    {
        $start = $weapon->startArc;
        $end = $weapon->endArc;

        $length = ($start > $end) ? 360 - $start + $end : $end - $start;
        $fullCircle = ($start === $end || $start == $end || $length >= 360);
        if (!$fullCircle && Movement::isRolled($unit)) {
            $mirroredStart = self::addToDirection($end, $end * -2);
            $end = self::addToDirection($start, $start * -2);
            $start = $mirroredStart;
        }

        $facing = $unit->getFacingAngle();
        $start = self::addToDirection($start, $facing);
        $end = self::addToDirection($end, $facing);

        if ($start == $end) return true;
        if ($heading == 0 && ($start == 360 || $end == 360)) return true;
        if ($start > $end) return ($heading >= $start || $heading <= $end);
        return ($heading >= $start && $heading <= $end);
    }

    //mathlib.addToDirection, as the page has it.
    private static function addToDirection($current, $add)
    {
        $add = fmod($add, 360);
        if ($current + $add > 360) return $add - (360 - $current);
        if ($current + $add < 0) return 360 + ($current + $add);
        return $current + $add;
    }

    /* weaponManager.checkIsInRange. A range of 0 is unlimited. A jammer or a stealth hull
       shortens the range; where the page looks closely (was the jammer on last turn, how far
       off is the stealth hull) this simply takes the worse case, which can only cost the bot a
       shot, never give it one the page would refuse. */
    public function inRange($unit, Weapon $weapon, $target, $distance)
    {
        $range = $weapon->range;
        if ($range == 0) return true;

        $jam = 0;
        foreach (array('Jammer', 'Stealth') as $name) {
            $system = $target->getSystemByName($name);
            if ($system) $jam = max($jam, (float)$system->getOutputWhenOffline());
        }
        if ($jam > 0) {
            $advanced = $unit->hasSpecialAbility('AdvancedSensors') || $unit->getSystemByName('Fighteradvsensors');
            if ($advanced) $jam = 0;
            $range = $range / (1 + $jam); //Improved Sensors would halve $jam: not claimed
        }

        return $distance <= $range;
    }

    //What a volley at $chance percent should do: every gun, every shot (or pulse) of it, after armour and range.
    private function expected(Weapon $weapon, $unit, $target, $chance, $hitLoc, $distance)
    {
        return $this->atChance($weapon, $this->damagePerHit($weapon, $unit, $target, $hitLoc, $distance), $chance);
    }

    //The same, given what one hit does: every gun, every shot (or pulse) at $chance percent. The weapon's mode is the shot's.
    private function atChance(Weapon $weapon, $perShot, $chance)
    {
        $expected = 0;
        if ($weapon->damageType === 'Pulse') {
            //one roll; each further pulse needs the roll to be `grouping` lower
            $expected = $perShot * $this->expectedPulses($weapon, $chance);
        } else {
            $shots = max(1, (int)$weapon->defaultShots);
            for ($i = 0; $i < $shots; $i++) {
                $expected += $perShot * max(0, min(100, $chance - $weapon->getShotHitChanceMod($i))) / 100;
            }
        }
        return $expected * max(1, (int)$weapon->guns);
    }

    /* What one hit is worth: the mode's average damage, less what the range takes off it (a
       plasma weapon's rangeDamagePenalty - Weapon::getDamageMod), less the armour it meets. An
       estimate for ranking, in the spirit of BaseShip::setExpectedDamage - not a simulation. */
    private function damagePerHit(Weapon $weapon, $unit, $target, $hitLoc, $distance = 0)
    {
        $average = ($weapon->minDamage + $weapon->maxDamage) / 2;
        if ($weapon->maxDamage <= 0) return 1; //no damage of its own: an effect weapon, counted as a token

        if ($weapon->rangeDamagePenalty > 0 && $distance > 0) {
            $factor = (TacGamedata::$nebulaPresent && $weapon->weaponClass === 'Plasma') ? 2 : 1; //NEBULA_PLAN.md #11
            $average = max(0, $average - round($distance * $weapon->rangeDamagePenalty * $factor));
        }

        /* Stage T9 (the user, 2026-11-23: an EM Shield "reduces potential hit chances and damage so possibly needs considered for gauging
           incoming damage from enemies"): the hit chance has always counted a target's shields - it is the server's own formula - and now
           the damage they take off each hit does too, as Weapon's damage step takes it (getDamageMod: the strongest defence of each type,
           on arc from where the shooter stands). Asked only of a target carrying a Shield-type defence, so a decision pays nothing for the
           rest (an EA ship's Interceptors are defences too, of another type, and take nothing off). */
        if ($this->shielded($target)) {
            try {
                $average = max(0, $average - $target->getDamageMod($unit, null, $this->turn, $weapon));
            } catch (Throwable $e) {
                //as before stage T9: no reduction
            }
        }

        $armour = 0;
        try {
            if ($target instanceof FighterFlight) {
                $sample = $target->getSampleFighter();
                if ($sample) $armour = $sample->getArmourComplete($target, $unit, $weapon->weaponClass);
            } else {
                $structure = $target->getStructureSystem((int)$hitLoc);
                if ($structure) $armour = $structure->getArmourComplete($target, $unit, $weapon->weaponClass);
            }
        } catch (Throwable $e) {
            $armour = 0;
        }

        if ($weapon->damageType === 'Raking') {
            //armour comes off every rake
            $rake = max(1, (int)$weapon->getRakeSize());
            $rakes = max(1, (int)ceil($average / $rake));
            return max(0.5, $average - $armour * $rakes);
        }

        return max(0.5, $average - $armour);
    }

    //Does $target carry a Shield-type defence (a Shield, an EM Shield, a fighter's shield...)? Remembered per unit for the view's life.
    private function shielded($target)
    {
        $key = (int)$target->id;
        if (!isset($this->shieldedUnits[$key])) {
            $systems = $target->systems;
            if ($target instanceof FighterFlight) {
                $sample = $target->getSampleFighter();
                $systems = $sample ? $sample->systems : array();
            }
            $this->shieldedUnits[$key] = false;
            foreach ($systems as $system) {
                if ($system instanceof DefensiveSystem && $system->getDefensiveType() === 'Shield') {
                    $this->shieldedUnits[$key] = true;
                    break;
                }
            }
        }
        return $this->shieldedUnits[$key];
    }

    //Pulses expected from one pulse shot: 1 on a hit, +1 for each `grouping` the roll beats the need by.
    private function expectedPulses(Weapon $weapon, $chance)
    {
        $chance = max(0, min(100, $chance));
        $max = max(1, (int)$weapon->maxpulses);
        $grouping = (float)$weapon->grouping;
        if ($grouping <= 0) return $chance / 100 * max(1, min($max, (int)$weapon->defaultShots));

        $expected = 0;
        for ($n = 1; $n <= $max; $n++) {
            $needed = $chance - ($n - 1) * $grouping; //at least n pulses
            if ($needed <= 0) break;
            $expected += $needed / 100;
        }
        return $expected;
    }

    // ------------------------------------------------------------------ worth and danger

    //What a unit is worth right now: its price, scaled by what is left of it.
    public function value($unit)
    {
        $cost = max(1, (int)$unit->pointCost + (int)$unit->pointCostEnh);
        return $cost * max(0, min(100, (float)$unit->calculateCombatValue())) / 100;
    }

    /* The damage that would finish a unit. A flight: every surviving craft. A ship: what is left of its primary structure -
       the box whose loss destroys it - and, at a level with the skill 'sectionHealth' (stage T5f; the user's ruling on game
       4774), the structure of the section a hit goes through first. From $shooter's side when that is given (the sections
       its bearing hits, shared out by profile as the engine picks among them), else the mean of the outer sections. A
       destroyed section adds nothing: a hit there goes into the primary. Without the skill - Easy - the primary alone, which
       made a flight value its shots at a ship about twice too high and left guns unfired at a ship "dead on paper". */
    public function healthLeft($unit, $shooter = null)
    {
        $left = 0;
        if ($unit instanceof FighterFlight) {
            foreach ($unit->systems as $fighter) {
                if ($fighter instanceof Fighter && !$fighter->isDestroyed()) $left += $fighter->getRemainingHealth();
            }
        } else {
            $primary = $unit->getStructureSystem(0);
            if ($primary) $left = $primary->getRemainingHealth();
            if ($this->skill('sectionHealth')) {
                $left += ($shooter === null) ? $this->outerStructure($unit) : $this->sectionStructureAt($unit, $unit->getBearingOnUnit($shooter));
            }
        }
        return max(1, $left);
    }

    //The mean of what is left of a ship's outer sections' structure, a destroyed one counting 0.
    private function outerStructure($unit)
    {
        $sum = 0;
        $count = 0;
        foreach ($unit->systems as $system) {
            if (!($system instanceof Structure) || (int)$system->location === 0) continue;
            $count++;
            if (!$system->isDestroyed()) $sum += $system->getRemainingHealth();
        }
        return ($count > 0) ? $sum / $count : 0;
    }

    /* The outer structure a hit from $bearing (degrees from the ship's facing) goes through: the entries of its hit table
       whose arc holds the bearing, shared out by profile (exposureAt's choice), each its section's structure left - 0 for a
       destroyed section, or for an entry the primary takes itself. */
    private function sectionStructureAt($unit, $bearing)
    {
        $weights = 0;
        $total = 0;
        foreach ($unit->getLocations() as $loc) {
            if (!Mathlib::isInArc($bearing, $loc['min'], $loc['max'])) continue;
            $profile = max(1, (float)$loc['profile']);
            $weights += $profile;
            if ((int)$loc['loc'] === 0) continue;
            $structure = $unit->getStructureSystem($loc['loc']);
            if ($structure && !$structure->isDestroyed()) $total += $profile * $structure->getRemainingHealth();
        }
        return ($weights > 0) ? $total / $weights : $this->outerStructure($unit);
    }

    /* How dangerous an enemy is to this fleet where it stands: the most each of its plain guns
       could do to any one of the bot's units, by the same estimate - loaded or not, since this
       is about the coming turns. Only what the mask shows goes in. */
    public function threat($enemy)
    {
        if (isset($this->threat[$enemy->id])) return $this->threat[$enemy->id];

        $mine = $this->myUnits();
        $total = 0;
        foreach ($this->mounts($enemy) as $mount) {
            $weapon = $mount['weapon'];
            if (!$this->plainGun($weapon, $mount['fighter'])) continue;

            $best = 0;
            foreach ($mine as $unit) {
                $best = max($best, $this->estimate($enemy, $weapon, $unit));
            }
            $total += $best;
        }

        return $this->threat[$enemy->id] = $total;
    }

    /* How much a hit from $shooter's side costs $unit, against an ordinary hit (stage T5 - the user's
       ruling of 2026-11-16: protect a damaged section moderately, a DESTROYED one far more). The
       section is picked as the engine picks it (doGetHitSectionBearing): the entries of the hit table
       whose arc holds the shooter's bearing, the live ones shared out by profile. A live section counts
       1 + MOVE_SECTION_DAMAGED x the share of its structure already lost. When every section facing
       the shooter is gone the hit goes into the primary structure (getHitSection answers 0), and counts
       MOVE_SECTION_DESTROYED. A hull with no section there (its primary takes such hits by design), and
       a flight, count 1. Positions and facings are the units' current ones. */
    public function exposure($unit, $shooter)
    {
        if ($unit instanceof FighterFlight) return 1.0;
        return $this->exposureAt($unit, $unit->getBearingOnUnit($shooter));
    }

    /* For an enemy that has yet to move this turn (user, 2026-11-19: an enemy moving after the ship is MORE dangerous - it
       can "line up better shots / hit a damaged section"). The section it meets leans from the one facing it now towards
       the ship's worst, by how much of the gap its speed can close: fully when it is within its own speed. */
    public function exposureUnmoved($unit, $shooter)
    {
        if ($unit instanceof FighterFlight) return 1.0;
        $now = $this->exposure($unit, $shooter);
        $last = $shooter->getLastMovement();
        $speed = $last ? max(0, (int)$last->speed) : 0;
        $distance = $unit->getHexPos()->distanceTo($shooter->getHexPos());
        $reach = ($distance <= 0) ? 1.0 : min(1.0, $speed / $distance);
        return $now + $reach * max(0, $this->worstExposure($unit) - $now);
    }

    //The worst exposure any bearing gives the unit (sampled every 15 degrees - each section, each line between two). Its damage, not its facing, decides it.
    public function worstExposure($unit)
    {
        if ($unit instanceof FighterFlight) return 1.0;
        $key = (int)$unit->id;
        if (!isset($this->worst[$key])) {
            $worst = 1.0;
            for ($bearing = 0; $bearing < 360; $bearing += 15) $worst = max($worst, $this->exposureAt($unit, $bearing));
            $this->worst[$key] = $worst;
        }
        return $this->worst[$key];
    }

    //exposure(), for a hit from $bearing - degrees relative to the unit's facing (a drifting field's, say: Hazards).
    public function exposureAt($unit, $bearing)
    {
        if ($unit instanceof FighterFlight) return 1.0;

        $weights = 0;
        $total = 0;
        $dead = false;
        foreach ($unit->getLocations() as $loc) {
            if (!Mathlib::isInArc($bearing, $loc['min'], $loc['max'])) continue;
            $structure = $unit->getStructureSystem($loc['loc']);
            if (!$structure) continue;
            $left = $structure->getRemainingHealth();
            if ($left <= 0) {
                $dead = true;
                continue;
            }
            $profile = max(1, (float)$loc['profile']);
            $lost = 1 - $left / max(1, (int)$structure->maxhealth);
            $weights += $profile;
            $total += $profile * (1 + AIConfig::MOVE_SECTION_DAMAGED * max(0, $lost));
        }

        if ($weights > 0) return $total / $weights;
        return $dead ? AIConfig::MOVE_SECTION_DESTROYED : 1.0;
    }

    //Is the line between two units clear of terrain and Enormous units (the page's test in shot())?
    public function clearLine($from, $to)
    {
        if (count($this->blocked) == 0) return true;
        return !Mathlib::isLoSBlocked($from->getHexPos(), $to->getHexPos(), $this->blocked);
    }

    /* Will this ballistic weapon of $unit be one the bot launches NEXT turn (stage T5)? The P4 tests
       BallisticDecider's view applies (weaponBlock in ballistic mode), with "loaded" asked of next
       turn: a launcher still loading counts once a turn more completes it, and one that has launched
       this turn only if it reloads in a turn. */
    public function launcherNextTurn($unit, Weapon $weapon)
    {
        if (!$weapon->ballistic) return false;
        $was = $this->ballistics;
        $this->ballistics = true;
        try {
            $why = $this->weaponBlock($unit, $weapon, null);
        } finally {
            $this->ballistics = $was;
        }
        if ($why === null) return true;
        if ($why === 'not loaded') return (int)$weapon->getLoadingTime() <= (int)$weapon->turnsloaded + 1;
        if ($why === 'already has an order this turn') return (int)$weapon->getLoadingTime() <= 1;
        return false;
    }

    /* What a ballistic weapon of $shooter should do to $target if launched NEXT turn from where
       $shooter now stands (stage T5: "launchers to bear for next turn's launch", the user's note on
       game 4701). A launch is made in Initial Orders from the hex the last Movement phase ended in,
       so the tentative order is dated next turn - Weapon::getFiringHex then takes this turn's last
       row as the launch hex. Arc and launch range are the page's tests as shot() makes them (a unit
       in the same hex whatever the arc, a stealth flight not beyond 5 hexes); the hit formula is the
       engine's, with this turn's EW standing in for next turn's. The weapon is asked in its current
       mode. 0 when it could not be launched at $target from there. */
    public function launchEstimate($shooter, Weapon $weapon, $target)
    {
        if (!$weapon->ballistic) return 0;
        if (TacGamedata::$officersPresent && $weapon->damageType === 'Piercing') return 0;

        $fc = $weapon->fireControl;
        $index = ($target instanceof FighterFlight) ? 0 : (($target->shipSizeClass < 2) ? 1 : 2);
        if (!isset($fc[$index])) return 0;

        $distance = $shooter->getHexPos()->distanceTo($target->getHexPos());
        if ($distance > 0 && !$this->onArc($shooter, $weapon, $target)) return 0;
        if (!$this->inRange($shooter, $weapon, $target, $distance)) return 0;
        if ($target instanceof FighterFlight && $distance > 5 && $target->getSystemByName('Stealth')) return 0;

        $order = new FireOrder(-1, 'ballistic', $shooter->id, $target->id, $weapon->id, -1, $this->turn + 1, $weapon->firingMode, 0, 0, $weapon->defaultShots, 0, 0, null, null, null);
        try {
            $answers = $this->hitBase($weapon, $shooter, $target, $order);
        } catch (Throwable $e) {
            return 0;
        }

        $total = 0;
        foreach ($answers as $answer) {
            $chance = max(0, min(100, $answer['needed']));
            if ($chance > 0) $total += $answer['weight'] * $this->expected($weapon, $shooter, $target, $chance, $answer['loc'], $distance);
        }
        return $total;
    }

    //A direct-fire gun that does damage of its own and still exists - anybody's, loaded or not.
    public function plainGun(Weapon $weapon, $fighter = null)
    {
        if ($weapon->isDestroyed() || ($fighter !== null && $fighter->isDestroyed())) return false;
        if ($weapon->ballistic || $weapon->hextarget || $weapon->isRammingAttack || $weapon instanceof JumpEngine) return false;
        if ($weapon->maxDamage <= 0 || $weapon->weaponClass === 'Support') return false;
        return true;
    }

    /* A cheap two-way estimate with no order behind it: range, the real hit formula in the weapon's
       current mode, and - unless $needArc is false - the weapon's arc as the units stand. Without
       the arc it answers "what could this gun do to that unit once both have moved", which is the
       question Initial Orders asks (EWDecider): a facing changes in Movement, a distance much less. */
    public function estimate($shooter, Weapon $weapon, $target, $needArc = true)
    {
        $found = $this->hitAnswers($shooter, $weapon, $target, $needArc);
        if ($found === null) return 0;
        list($answers, $distance) = $found;

        $total = 0;
        foreach ($answers as $answer) {
            $chance = max(0, min(100, $answer['needed']));
            if ($chance > 0) $total += $answer['weight'] * $this->expected($weapon, $shooter, $target, $chance, $answer['loc'], $distance);
        }
        return $total;
    }

    /* The chance to hit $target with $weapon from where $shooter stands now - the hit table's entries weighed as estimate() weighs
       them; 0 where estimate() finds no shot at all. Stage T6g: a flight that must fire this turn (FlightDecider) needs a place
       where a gun of it reaches FireDecider's floor, which an expected damage above 0 does not say. (shot() cannot answer this:
       it is cached by unit, gun and target, not by where the unit stands.) */
    public function chanceOf($shooter, Weapon $weapon, $target, $needArc = true)
    {
        $found = $this->hitAnswers($shooter, $weapon, $target, $needArc);
        if ($found === null) return 0;
        $chance = 0;
        $weights = 0;
        foreach ($found[0] as $answer) {
            $chance += $answer['weight'] * max(0, min(100, $answer['needed']));
            $weights += $answer['weight'];
        }
        return ($weights > 0) ? $chance / $weights : 0;
    }

    //estimate()'s and chanceOf()'s tests and the server's hit table for the pairing: array(answers, distance), or null for no shot.
    private function hitAnswers($shooter, Weapon $weapon, $target, $needArc)
    {
        //⚠️ see the header: with officers aboard, pricing a Piercing shot may spend an Expert Gunner's bonus
        if (TacGamedata::$officersPresent && $weapon->damageType === 'Piercing') return null;

        $fc = $weapon->fireControl;
        $index = ($target instanceof FighterFlight) ? 0 : (($target->shipSizeClass < 2) ? 1 : 2);
        if (!isset($fc[$index])) return null;

        $distance = $shooter->getHexPos()->distanceTo($target->getHexPos());
        if ($weapon->range != 0 && $distance > $weapon->range) return null;
        if ($needArc && !$this->onArc($shooter, $weapon, $target)) return null;

        $order = new FireOrder(-1, 'normal', $shooter->id, $target->id, $weapon->id, -1, $this->turn, $weapon->firingMode, 0, 0, $weapon->defaultShots, 0, 0, null, null, null);
        try {
            return array($this->hitBase($weapon, $shooter, $target, $order), $distance);
        } catch (Throwable $e) {
            return null;
        }
    }

    // ------------------------------------------------------------------ next turn

    /* A unit one turn straight on from where it stands, as a movement row - or null for one that
       will not move. The guess a player makes about a board he cannot see yet, and what a bot's
       own ships do unless something is in the way (MoveDecider). */
    public static function straightOn($unit, $turn)
    {
        $last = $unit->getLastMovement();
        if (!$last || (int)$last->speed <= 0) return null;
        if (!empty($unit->base) || !empty($unit->smallBase) || !empty($unit->osat) || !empty($unit->mine) || !empty($unit->attached)) return null;

        $position = new OffsetCoordinate($last->position);
        for ($step = 0; $step < (int)$last->speed; $step++) $position = $position->moveToDirection((int)$last->heading);

        return new MovementOrder(-1, 'move', $position, 0, 0, $last->speed, $last->heading, $last->facing, false, $turn, 0, 0);
    }

    /* Stage T5f-7: unit id => MovementOrder - where an enemy ship yet to move is FORECAST to end this turn (EnemyForecast, the
       level's skill 'forecast'), set by MoveDecider for its decision. atFireTime() stands it there instead of straight on. */
    public $forecast = array();

    /* Run $work with the board as it should stand at THIS turn's Fire phase, as seen from the
       Movement phase: a unit that has moved this turn where it ended, any other (but $except) where
       flying straight on takes it - or, an enemy ship with a forecast, where it is forecast to end
       (stage T5f-7). Put back afterwards, whatever happens; only estimate() may be asked inside. */
    public function atFireTime(callable $work, array $except = array())
    {
        $moved = array();
        foreach (array_merge($this->myUnits(), $this->enemies()) as $unit) {
            if (in_array((int)$unit->id, $except, true) || $unit->getLastTurnMoved() >= $this->turn) continue;
            $row = isset($this->forecast[(int)$unit->id]) ? $this->forecast[(int)$unit->id] : self::straightOn($unit, $this->turn);
            if ($row === null) continue;
            $unit->movement[] = $row;
            $moved[] = $unit;
        }

        try {
            return $work();
        } finally {
            foreach ($moved as $unit) array_pop($unit->movement);
        }
    }

    /* Run $work with every unit on the board (the bot's and the enemy's) a turn straight on, and
       give back what it returns. The board is put back as it was, whatever happens. Nothing the
       view remembers (shot()) may be asked inside: only estimate(). */
    public function ahead(callable $work)
    {
        $moved = array();
        foreach (array_merge($this->myUnits(), $this->enemies()) as $unit) {
            $row = self::straightOn($unit, $this->turn);
            if ($row === null) continue;
            $unit->movement[] = $row;
            $moved[] = $unit;
        }

        try {
            return $work();
        } finally {
            foreach ($moved as $unit) array_pop($unit->movement);
        }
    }
}
