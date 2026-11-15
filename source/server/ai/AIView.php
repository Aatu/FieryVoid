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

    private $blocked;             //blockedHexes, or an empty array
    private $shots = array();     //cache: "shooter:weapon:mode:target" => shot or false
    private $threat = array();    //cache: enemy id => expected damage it can deal
    private $why = array();       //cache: the same key as $shots => why there is no shot ('arc', 'range', ...)
    private $refusal = null;
    private $sent = array();      //cache: weapon object id => array(its public properties, its payload or null)

    //Where TacGamedata::hideStealthShipMovement puts a unit this viewer may not see.
    const HIDDEN_Q = -10000;

    public function __construct(TacGamedata $gamedata, $botId)
    {
        $this->gamedata = $gamedata;
        $this->botId = (int)$botId;
        $this->turn = (int)$gamedata->turn;
        $this->team = $gamedata->getPlayerTeam();
        $this->blocked = is_array($gamedata->blockedHexes) ? $gamedata->blockedHexes : array();
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
        if ($weapon->ballistic) return 'ballistic';
        if ($weapon->preFires) return 'pre-firing weapon';
        if ($weapon->hextarget || !empty($weapon->hextargetArray)) return 'hex-targeted';
        if ($weapon->autoFireOnly || !empty($weapon->autoFireOnlyArray)) return 'fires by itself';
        if ($weapon->canSplitShots || !empty($weapon->canSplitShotsArray)) return 'split shots';
        //from here on some flags are read from the weapon's payload (flag()): only for a weapon that got this far
        if ($this->flag($weapon, 'duoWeapon') || $this->flag($weapon, 'dualWeapon')) return 'compound weapon';
        if ($this->flag($weapon, 'isBoardingAction')) return 'boarding';
        if ($weapon->splitArcs || $this->flag($weapon, 'specialArcs')) return 'special arcs';
        if ($this->flag($weapon, 'hasSpecialTargeting') || $this->flag($weapon, 'canOnlyCalledShot') || $this->flag($weapon, 'reinforceAmount')) return 'special targeting';
        if ($weapon->linkedFiringGroup !== null || $this->flag($weapon, 'flightExclusiveGroup')) return 'linked firing';
        if ($weapon->exclusive) return 'exclusive weapon';
        if ($weapon->weaponClass === 'Support' || $this->flag($weapon, 'canTargetAllies') || $this->flag($weapon, 'canTargetAlliesArray')) return 'support weapon';
        //Sustained fire is something a player switches on (an overload, in Initial Orders); the bot never does,
        //and a Heavy Laser that is not overloading is a plain gun.
        if ($weapon->alwaysoverloading || $weapon->isOverloadingOnTurn($this->turn)) return 'overload / sustained fire';
        if ($this->flag($weapon, 'ammunition') !== null || $weapon->checkAmmoMagazine || $this->flag($weapon, 'missileArray')) return 'limited ammunition';
        if ($this->flag($weapon, 'specialHitChanceCalculation') || $this->flag($weapon, 'specialRangeCalculation') || $this->flag($weapon, 'specialHitChanceCalculationArray') || $this->flag($weapon, 'specialRangeCalculationArray')) return 'special hit calculation';
        if (count($weapon->getFixedFireControlModes()) > 0) return 'fixed fire control';

        if ($weapon->isDestroyed()) return 'destroyed';
        if ($fighter !== null && $fighter->isDestroyed()) return 'fighter destroyed';
        if ($weapon->isOfflineOnTurn($this->turn)) return 'offline';
        if ($weapon->stowed) return 'stowed';
        if ($weapon->firedOnTurn($this->turn) || count($this->ordersThisTurn($weapon)) > 0) return 'already has an order this turn';
        if (!$this->loaded($weapon)) return 'not loaded';

        //weaponManager.checkConflictingFireOrder: an exclusive weapon on the same craft has an order
        foreach (($fighter !== null ? $fighter->systems : $unit->systems) as $other) {
            if ($other === $weapon || !($other instanceof Weapon) || !$other->exclusive || $other->ballistic) continue;
            if (count($this->ordersThisTurn($other)) > 0) return 'an exclusive weapon is firing';
        }

        return null;
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

    //The firing modes of a weapon the bot will consider.
    public function modes(Weapon $weapon)
    {
        $modes = array();
        foreach (array_keys($weapon->firingModes) as $mode) {
            $mode = (int)$mode;
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
       for that size of unit), 'los', 'arc', 'range', 'chance'. Null when there is a shot, or it was never asked. */
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

    private function work($unit, Weapon $weapon, $target, $mode)
    {
        if (!isset($weapon->firingModes[$mode])) return $this->refuse('mode');
        if ((int)$weapon->firingMode !== $mode) $weapon->changeFiringMode($mode);

        //what a mode may turn the weapon into
        if ($weapon->hextarget || $weapon->autoFireOnly || $weapon->canSplitShots || $weapon->ballistic) return $this->refuse('mode');
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

        if (!$this->onArc($unit, $weapon, $target)) return $this->refuse('arc');

        $distance = $from->distanceTo($to);
        if (!$this->inRange($unit, $weapon, $target, $distance)) return $this->refuse('range');

        //The real formula, on an order that exists only here.
        $order = new FireOrder(-1, 'normal', $unit->id, $target->id, $weapon->id, -1, $this->turn, $mode, 0, 0, $weapon->defaultShots, 0, 0, null, null, null);
        $weapon->calculateHitBase($this->gamedata, $order);
        $chance = (int)$order->needed;
        if ($chance < 1) return $this->refuse('chance'); //the page makes no order for a shot that cannot hit

        $perShot = $this->damagePerHit($weapon, $unit, $target, $order->chosenLocation);
        $shots = max(1, (int)$weapon->defaultShots);
        $expected = 0;
        if ($weapon->damageType === 'Pulse') {
            //one roll; each further pulse needs the roll to be `grouping` lower
            $expected = $perShot * $this->expectedPulses($weapon, $chance);
        } else {
            for ($i = 0; $i < $shots; $i++) {
                $expected += $perShot * max(0, min(100, $chance - $weapon->getShotHitChanceMod($i))) / 100;
            }
        }
        $expected *= $weapon->guns;

        return array(
            'mode' => $mode,
            'chance' => $chance,
            'orders' => (int)$weapon->guns,
            'shots' => (int)$weapon->defaultShots,
            'damageclass' => strtolower((string)$weapon->weaponClass),
            'damage' => $expected,
            'hitLoc' => $order->chosenLocation,
            'distance' => $distance,
            'edge' => $this->onEdge($unit, $target)
        );
    }

    /* Does the shooter sit exactly on a line between two of the target's sections or profiles
       (every 30 degrees round it)? There the DEFENDER chooses which side is hit - the server
       picks the section it would rather lose, and takes that section's profile - while the page
       prices the shot on the forward / aft profile. The two can differ by the gap between a
       hull's profiles (10-15 points of hit chance), in either direction. Nothing is wrong with
       either; a decider only has to keep a wider margin on such a shot. */
    public function onEdge($unit, $target)
    {
        return fmod((float)$target->getBearingOnUnit($unit), 30) == 0;
    }

    /* weaponManager.isOnWeaponArc: the compass heading of the target from the shooter, against
       the weapon's arc turned by the shooter's facing (and mirrored on a rolled ship). Written
       out rather than asked of BaseShip::getBearingOnUnit, which answers 360 for a unit dead
       ahead and then fails an arc that starts at 0. */
    public function onArc($unit, Weapon $weapon, $target)
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

        $heading = round(Mathlib::getCompassHeadingOfShip($unit, $target));

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

    /* What one hit is worth: the mode's average damage less the armour it meets. An estimate
       for ranking, in the spirit of BaseShip::setExpectedDamage - not a simulation. */
    private function damagePerHit(Weapon $weapon, $unit, $target, $hitLoc)
    {
        $average = ($weapon->minDamage + $weapon->maxDamage) / 2;
        if ($weapon->maxDamage <= 0) return 1; //no damage of its own: an effect weapon, counted as a token

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

    /* The damage that would finish a unit. A flight: every surviving craft. A ship: what is left
       of its primary structure - the box whose loss destroys it. */
    public function healthLeft($unit)
    {
        $left = 0;
        if ($unit instanceof FighterFlight) {
            foreach ($unit->systems as $fighter) {
                if ($fighter instanceof Fighter && !$fighter->isDestroyed()) $left += $fighter->getRemainingHealth();
            }
        } else {
            $primary = $unit->getStructureSystem(0);
            if ($primary) $left = $primary->getRemainingHealth();
        }
        return max(1, $left);
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
            if ($weapon->isDestroyed() || ($mount['fighter'] !== null && $mount['fighter']->isDestroyed())) continue;
            if ($weapon->ballistic || $weapon->hextarget || $weapon->isRammingAttack || $weapon instanceof JumpEngine) continue;
            if ($weapon->maxDamage <= 0 || $weapon->weaponClass === 'Support') continue;

            $best = 0;
            foreach ($mine as $unit) {
                $best = max($best, $this->estimate($enemy, $weapon, $unit));
            }
            $total += $best;
        }

        return $this->threat[$enemy->id] = $total;
    }

    //A cheap two-way estimate with no order behind it: arc, range, and the real hit formula in the weapon's current mode.
    private function estimate($shooter, Weapon $weapon, $target)
    {
        $fc = $weapon->fireControl;
        $index = ($target instanceof FighterFlight) ? 0 : (($target->shipSizeClass < 2) ? 1 : 2);
        if (!isset($fc[$index])) return 0;

        $distance = $shooter->getHexPos()->distanceTo($target->getHexPos());
        if ($weapon->range != 0 && $distance > $weapon->range) return 0;
        if (!$this->onArc($shooter, $weapon, $target)) return 0;

        $order = new FireOrder(-1, 'normal', $shooter->id, $target->id, $weapon->id, -1, $this->turn, $weapon->firingMode, 0, 0, $weapon->defaultShots, 0, 0, null, null, null);
        try {
            $weapon->calculateHitBase($this->gamedata, $order);
        } catch (Throwable $e) {
            return 0;
        }
        $chance = max(0, min(100, (int)$order->needed));
        if ($chance <= 0) return 0;

        return $this->damagePerHit($weapon, $shooter, $target, $order->chosenLocation) * $chance / 100
            * max(1, (int)$weapon->defaultShots) * max(1, (int)$weapon->guns);
    }
}
