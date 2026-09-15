<?php
/**
 * Energy Draining Field exposure resolver — the Walkers of Sigma-957 drain.
 * WALKERS_OF_SIGMA_PLAN.md section 2.2 (Stage 4b).
 *
 * WHAT IT DOES
 * Every unit standing in a hex of somebody else's Energy Draining Field at the Critical Hit
 * step has its thrust, energy, initiative and total EW drained for the FOLLOWING turn, and a
 * fighter flight additionally rolls for craft dropping out. The drain escalates the longer a
 * unit stays in the field.
 *
 * ⭐ WHY IT IS ONE SWEEP AND NOT A PER-FIELD EFFECT. The rules say "additional fields do not
 * provide cumulative modifiers". Resolving per unit, once, off the already-collapsed
 * TacGamedata::$edfHexes map makes that structural rather than a special case: three
 * overlapping Walker fields and one field are the same thing to this code. The $resolvedTurn
 * guard is the same shape GraviticMineHandler uses for cross-mine shearing.
 *
 * ⭐ WHERE IT RUNS, AND WHY NOT IN criticalPhaseEffects(). It is called from the TOP of
 * Criticals::setCriticals, before pass 1. Two reasons:
 *   - pass 1 is where Fighter::testCritical rolls dropouts, and the plan wanted the EDF to
 *     feed that roll. It cannot: testCritical only runs on a fighter that was DAMAGED this
 *     turn, so an undamaged flight sitting in a field would never roll at all. This resolver
 *     therefore rolls its own dropout, mirroring testCritical's comparison — but it still
 *     belongs before pass 1 so the two cannot interleave on one craft.
 *   - a global sweep hung off one system's criticalPhaseEffects() is the wrong shape anyway.
 *     HkJamming (called from the tail of the same method) is the precedent this follows.
 *
 * ⭐⭐ REPLAY DETERMINISM WITHOUT A ROLL NOTE. setCriticals re-runs on replay and Dice::d is
 * not deterministic, so HkJamming has to persist its d20 in an IndividualNote. This resolver
 * does not need to: the RESULT is the record. Every roll it makes is written straight into a
 * persisted critical's `param`, and the EdfExposed marker on the victim says "this unit has
 * already been resolved for turn N". A reload finds that marker and skips — see
 * alreadyResolvedThisTurn(). The $resolvedTurn static only saves the sweep from running twice
 * inside ONE request; the marker is what makes it safe across requests.
 *
 * ⚠️ Never branch on $gamedata->phase in here (plan trap 3): by the time a phase's advance()
 * reaches this, the next phase has already been set.
 */
class EdfExposure
{
    /* ================= EDF DRAIN STATS — the real rules, 2026-09-04 ===================
       Straight out of the rules text, no longer inferred:

         "The ship loses 1d10 of the following attributes on the next turn, increased by a
          further 1d10 for every additional turn ended in the EDF. The minimum any attribute
          (other than initiative) can be reduced to is zero."   -> Free Thrust, Energy, Initiative
         "The ship's total EW is reduced by 1d6 for the next turn, increased by a further 1d6
          for every additional turn ended in the EDF."          -> ⚠️ EW IS d6, NOT d10
         "Initiative penalties produced by the field cannot exceed a total of -100 (FV value)."
         "must immediately roll for drop-out on 2d10 instead of the usual 1d10, also increased
          by an additional 1d10 for every successive turn"      -> (turns + DROPOUT_EXTRA) dice
         "Enormous units ... The modifiers are limited to the first die (1d10 or 1d6) and do not
          increase with every additional round."                -> ENORMOUS_DICE, both dice

       ⚠️ THE TWO DIE SIZES ARE THE THING TO GET RIGHT. Three attributes roll d10 and EW rolls
       d6; the first build assumed one die for all four and was wrong about EW.
       ⚠️⚠️ "INCREASED BY A FURTHER 1d10" IS CUMULATIVE, NOT A RE-ROLL. Turn one's figure stands
       and each further consecutive turn adds ONE more die to the total already in force, so the
       drain only ever grows while a unit stays in a field - see accumulate(). Rolling N fresh
       dice each turn (the first build) let turn two come out lighter than turn one.
       ⚠️ INI_SCALE is not a stat - it is FV's d100 conversion (plan trap 5). The rules quote the
       cap in FV units (-100), so INI_CAP is that figure in TABLETOP points, x5 by the reader.
       ================================================================================== */
    const DIE             = 10; //thrust, energy and initiative
    const EW_DIE          = 6;  //⚠️ EW alone rolls d6
    const ENORMOUS_DICE   = 1;  //"limited to the first die and do not increase"
    const DROPOUT_EXTRA   = 1;  //dropout rolls (consecutive turns + this) dice: 2d10 on turn one
    const INI_CAP         = 20; //tabletop; x INI_SCALE is the rules' -100 FV floor
    const INI_SCALE       = 5;  //FV d100 initiative: one tabletop point is five
    /* ================================================================================== */

    /* Once per request. ⚠️ NOT sufficient on its own across requests — see the class comment
       and alreadyResolvedThisTurn(). Criticals::setCriticals resets it, as it does for
       HkJamming::$alreadyResolved. */
    public static $resolvedTurn = -1;

    public static function resolve($gamedata)
    {
        if (self::$resolvedTurn === (int)$gamedata->turn) return;
        self::$resolvedTurn = (int)$gamedata->turn;

        if (empty($gamedata->edfHexes)) return; //no field on the board: the whole feature is free

        foreach ($gamedata->ships as $unit) {
            if ($unit->isDestroyed()) continue;
            if ($unit->isReinforcement()) continue; //still in hyperspace, and not on the map

            $pos = $unit->getHexPos();
            if (!$pos) continue;

            $entry = isset($gamedata->edfHexes[$pos->q . ',' . $pos->r])
                   ? $gamedata->edfHexes[$pos->q . ',' . $pos->r] : null;
            if ($entry === null) continue;

            /* Own-fleet immunity, exactly as the targeting penalty resolves it: a hex covered
               ONLY by this unit's own team is free. A hex its own field AND an enemy's cover
               still drains it — the exemption is "nobody else reaches me here", not "my side
               put a field here". */
            $teams = $entry['teams'];
            if (isset($unit->team) && count($teams) === 1 && isset($teams[(int)$unit->team])) continue;

            /* WHO is doing the draining. $edfHexes has collapsed its sources into a team set,
               so the answer comes from the parallel server-side $edfSources map. It is only
               needed for the combat-log attribution below - the drain itself does not care -
               so a null here is survivable and logResult() falls back to a self-targeted row. */
            self::drainUnit($unit, $gamedata, $gamedata->getEdfSourceShip($pos, $unit));
        }
    }

    /* ---------------------------------------------------------------- one unit ------- */

    private static function drainUnit($unit, $gamedata, $source = null)
    {
        $turn   = (int)$gamedata->turn;
        $marker = self::markerSystem($unit);
        if (!$marker) return; //no CnC and no sample fighter: nothing to hang the record on

        //Idempotency across reloads. See the class comment: the marker IS the record.
        if (self::alreadyResolvedThisTurn($marker, $turn)) return;

        /* Escalation. sumCriticalParam on a oneturn crit reports LAST turn's marker (its
           turn + 1 == this turn), so this reads "how many consecutive turns had I been in a
           field as of last turn" and adds one. 0 for a unit that has just flown in. */
        $consecutive = (int)$marker->sumCriticalParam("EdfExposed", $turn) + 1;

        $isFlight = ($unit instanceof FighterFlight);

        /* ⭐⭐ THE DRAIN IS CUMULATIVE AND LOCKED IN - IT IS NOT RE-ROLLED FROM SCRATCH
           (user ruling, 2026-09-04, correcting the first build). "The ship loses 1d10 ...
           increased by a further 1d10 for every additional turn ended in the EDF" means turn
           one's roll STANDS, and each further consecutive turn adds ONE more die on top of the
           total already in force. So the drain can only ever grow while a unit stays in a
           field. The first build rolled N fresh dice each turn, which let a second turn in the
           field come out LIGHTER than the first - the opposite of what the rule is for.

           Leaving the field for even one turn resets it: $consecutive comes back as 1, $carry
           is false, and the next entry starts again at a single die.

           ⚠️ LAST TURN'S TOTAL IS READ OFF THE CRIT THAT IS IN EFFECT THIS TURN. These are
           `oneturn` criticals, so the one rolled on turn N-1 is precisely what sumCriticalParam
           reports for turn N - which is the running total to add to. The read has to happen on
           the system the crit RIDES, and that is a different system for each of the four. */
        $carry    = ($consecutive > 1);
        $enormous = !empty($unit->Enormous);

        /* Where each drain lives. ⚠️ A FLIGHT "loses initiative and free thrust in the same
           manner as a ship" and nothing else: it has no Engine (the thrust crit rides its
           sample fighter and FighterFlight::getEffectiveFreeThrust takes it off freethrust), no
           Reactor and no Scanner, so energy and EW are not rolled at all. accumulate() returns
           0 for a missing system, which is also how a hull with no Scanner loses no EW. */
        $engine  = $isFlight ? $marker : $unit->getSystemByName("engine");
        $reactor = $isFlight ? null    : $unit->getSystemByName("reactor");
        $scanner = $isFlight ? null    : $unit->getSystemByName("Scanner");

        /* "The ship loses 1d10 of ... Free Thrust, Energy, Initiative" but "the ship's total EW is
           reduced by 1d6" - two different dice, and the EW one is easy to miss. */
        $thrust = self::accumulate($engine,  "EdfThrustDrain", $turn, $carry, $enormous, self::DIE);
        $ini    = self::accumulate($marker,  "EdfIniDrain",    $turn, $carry, $enormous, self::DIE);
        $power  = self::accumulate($reactor, "EdfPowerDrain",  $turn, $carry, $enormous, self::DIE);
        $ew     = self::accumulate($scanner, "EdfEwDrain",     $turn, $carry, $enormous, self::EW_DIE);

        /* "Initiative penalties produced by the field cannot exceed a total of -100 (FV value)."
           Clamped HERE as well as in the reader now that the figure accumulates: without it the
           stored total would climb for ever and the combat log would advertise a penalty the
           ship never actually takes. */
        if ($ini > self::INI_CAP) $ini = self::INI_CAP;

        /* THRUST. Ships lose Engine output; a flight's crit rides its sample fighter (plan
           decision D1: thrusters themselves are untouched either way).
           "If thrust is reduced to zero, the ship will be unable to maneuver" falls out of the
           clamp at 0 in the readers - there is nothing extra to enforce. */
        if ($thrust > 0) self::addParamCrit($engine, $unit, "EdfThrustDrain", $thrust, $gamedata);

        $blackout = false;
        if ($power > 0) {
            self::addParamCrit($reactor, $unit, "EdfPowerDrain", $power, $gamedata);
            $blackout = self::applyPowerBlackout($unit, $reactor, $gamedata);
        }

        if ($ini > 0) self::addParamCrit($marker, $unit, "EdfIniDrain", $ini, $gamedata);

        /* EW. ⭐ ON THE SCANNER, NOT THE CnC (user, 2026-09-04) - the same shape as the thrust
           drain on the Engine and the power drain on the Reactor: the drain rides the system it
           takes from, Scanner::getOutput() subtracts it and Scanner::stripForJson() publishes it
           as edfDrain, which the CLIENT already knows how to subtract. On the CnC it was applied
           only inside EW::getScannerOutput(), where no client code could see it. */
        if ($ew > 0) self::addParamCrit($scanner, $unit, "EdfEwDrain", $ew, $gamedata);

        $dropouts = 0;
        if ($isFlight) {
            /* "Even if the fighter/shuttle does not drop out, it will not be able to shoot the
               next turn" - unconditional, so no flag. */
            $crit = new EdfFighterGrounded(-1, $unit->id, $marker->id, "EdfFighterGrounded", $turn);
            $crit->updated = true;
            $crit->newCrit = true; //force save: it takes effect NEXT turn
            $marker->criticals[] = $crit;

            $dropouts = self::rollDropouts($unit, $consecutive, $gamedata);
        }

        //The marker last, so alreadyResolvedThisTurn() cannot short-circuit our own work above.
        $exposed = new EdfExposed(-1, $unit->id, $marker->id, "EdfExposed", $turn, 0, true, $consecutive);
        $exposed->updated = true;
        $exposed->newCrit = true;
        $marker->criticals[] = $exposed;

        self::logResult($unit, $gamedata, $consecutive, $thrust, $power, $ini, $ew, $dropouts, $blackout, $source);
    }

    /* ---------------------------------------------------------------- escalation ----- */

    /**
     * One attribute's drain for the coming turn: last turn's total plus one more die.
     *
     * ⭐ THE RUNNING TOTAL NEEDS NO STORAGE OF ITS OWN. The drain already in force this turn IS
     * last turn's `oneturn` critical, and sumCriticalParam($type, $turn) is exactly the read that
     * reports it (crit->turn + 1 == $turn). So the accumulator is the previous critical, and the
     * new one carries the new total. Nothing has to be swept up or reconciled.
     *
     * ⚠️ $system is the system the crit RIDES - Engine, Reactor, Scanner, or the marker for
     * initiative - because that is where the previous total was written. A null means the unit
     * has nothing to drain for this attribute (a flight has no Reactor and no Scanner, and some
     * hulls have no Scanner at all), and the answer is 0 rather than a roll, so the combat log
     * cannot claim a drain that was never applied.
     *
     * ⚠️ ENORMOUS UNITS KEEP THE FIRST DIE AND NEVER ADD TO IT: "the modifiers are limited to
     * the first die (1d10 or 1d6) and do not increase with every additional round" - which is a
     * statement about the TOTAL, so the roll is not repeated either. On the first turn of an
     * exposure they roll one die like everybody else.
     */
    private static function accumulate($system, $phpclass, $turn, $carry, $enormous, $die)
    {
        if (!$system) return 0;

        $previous = $carry ? (int)$system->sumCriticalParam($phpclass, $turn) : 0;
        if ($enormous && $previous > 0) return $previous;   //locked at the first die

        return $previous + self::roll(1, $die);
    }

    /* ---------------------------------------------------------------- blackout ------- */

    /**
     * "If the ship's reactor is completely drained of power, this will force the deactivation of
     * everything on the ship that requires energy. This includes any weapon or system with a
     * power diamond, even if that icon contains a zero (such as missile racks)."
     *
     * ⭐ That cascade ALREADY EXISTS: Reactor::addCritical propagates a ForcedOfflineOneTurn to
     * every system with `powerReq > 0` OR `instanceof Weapon` - which is exactly the rules'
     * "power diamond, even if that icon contains a zero", because a missile rack is a Weapon with
     * powerReq 0. It is the same mechanism a reactor knockout already uses, and it handles the
     * StarBase per-section case for free. Do not write a second one.
     *
     * The timing lines up too: ForcedOfflineOneTurn is `oneturn`, so isOfflineOnTurn() reports it
     * exactly on turn+1 - the same turn the drain itself lands.
     *
     * ⚠️⚠️ "COMPLETELY DRAINED" IS NOT "output - drain <= 0" (user report, game 4334, 2026-09-04).
     * A Fiery Void blueprint's Reactor `output` is the ship's SPARE power with every system
     * powered up, not the reactor's total generation - which is why virtually every hull in the
     * database is built `new Reactor($armour, $maxhealth, 0, 0)`. Measuring the drain against that
     * figure blacked out ANY ship the field touched, on the first point of drain, and took its
     * owner's chance to trade systems for power away with it. The reactor is only truly drained
     * when the deficit outlasts everything the player could switch off, which is what
     * getMaxAvailablePower() below computes - the same question the client's
     * shipManager.power.getRemainingFreeablePower answers for the Initial Orders commit gate.
     * Anything less than that is an ORDINARY DEFICIT: the player powers systems down in Initial
     * Orders and the commit is blocked until they do (gamedata.doCommit -> getShipsNegativePower).
     *
     * ⚠️ Measured against the power available NEXT turn, which is what the rule is about: the whole
     * EdfPowerDrain that will be in effect then, including the crit added moments ago. getOutput()
     * cannot answer this - it reads the CURRENT turn, where a oneturn crit is invisible by design.
     *
     * ⚠️ Skipped for an already-destroyed reactor: the ship has bigger problems, and blacking out
     * a wreck would write a crit per system for nothing.
     */
    private static function applyPowerBlackout($unit, $reactor, $gamedata)
    {
        if ($reactor->isDestroyed()) return false;

        $nextDrain = (int)$reactor->sumCriticalParam("EdfPowerDrain", (int)$gamedata->turn + 1);
        if ($nextDrain <= 0) return false;

        //"as much negative power as the total amount of power it provides to offline-able systems"
        //(user's ruling): a drain the player could still shed is not a drained reactor.
        if (self::getMaxAvailablePower($unit, $reactor, $gamedata) >= $nextDrain) return false;

        //Already blacked out this turn (a re-entrant sweep) - do not stack a second cascade.
        foreach ($reactor->criticals as $crit) {
            if ($crit->phpclass === "ForcedOfflineOneTurn" && (int)$crit->turn === (int)$gamedata->turn) return true;
        }

        $reactor->addCritical($unit->id, "ForcedOfflineOneTurn", $gamedata);
        return true;
    }

    /**
     * The most power this unit could have spare next turn if its owner switched off everything
     * they are allowed to switch off. The server-side mirror of the client's
     * shipManager.power.getReactorPower at maximum shed (power.js) - keep the two in step.
     *
     * STANDARD REACTOR: `output` is already the spare power with everything running, so every
     * system the player may power down ADDS its powerReq back.
     *
     * ⚠️ MAG-GRAV REACTOR ($fixedPower, Ipsha and the Vorlon technical mount) IS THE OTHER WAY
     * ROUND: its `output` is the reactor's TOTAL generation and every powered system is subtracted
     * from it, which is exactly what "provides fixed total power, regardless of destroyed systems"
     * means. So the ceiling is the total MINUS only the draws that cannot be shed; adding the
     * freeable ones would count the same power twice and make such a ship look unblackout-able.
     *
     * ⚠️ POWER CAPACITORS AND PLASMA BATTERIES (Vorlon, Pak'ma'ra) hold a charge that the client
     * feeds into the reactor display as a NEGATIVE powerReq (PowerCapacitor.initializationUpdate).
     * The server object never sees that rewritten figure, so their stored charge is added here by
     * name - it is real power the ship can spend against a deficit, and without it a Vorlon hull
     * blacks out while its capacitor is still full.
     *
     * ⚠️ A DESTROYED system frees nothing: a standard reactor's spare-power figure never gets its
     * draw back (that is the whole point of the Mag-Grav's "regardless of destroyed systems"), and
     * the client skips destroyed systems for the same reason.
     *
     * ⚠️ POWER-LOCKED systems (a DEPLOYED Kirishiac orbital's beam / augmenter / launcher) draw
     * power and cannot be switched off, so they are not freeable. The test is the pairing plus the
     * deployed state, NOT `stowed` alone: `Weapon::$stowed` defaults to false on every weapon in
     * the game, so a bare !stowed test would lock the entire armament of every hull.
     *
     * A STARBASE has one reactor per section and Reactor::addCritical blacks out only that
     * section, so the sweep is restricted to the section the drained reactor sits in.
     */
    private static function getMaxAvailablePower($unit, $reactor, $gamedata)
    {
        $turn      = (int)$gamedata->turn;
        $fixed     = !empty($reactor->fixedPower);
        $perSection = ($unit instanceof StarBase);
        $available = (int)$reactor->output + (int)$reactor->outputMod;

        foreach ($unit->systems as $system) {
            if ($system === $reactor) continue;
            if ($system instanceof Reactor) continue;               //another section's, or a spare
            if ($perSection && $system->location != $reactor->location) continue;
            if ($system->isDestroyed($turn)) continue;               //draws nothing, frees nothing

            if ($system instanceof PowerCapacitor || $system instanceof PlasmaBattery) {
                $available += max(0, (int)$system->powerCurr);       //stored charge is spendable power
                continue;
            }

            if ((int)$system->powerReq <= 0) continue;               //nothing to free by switching it off

            if (self::isPowerLocked($system)) {
                if ($fixed) $available -= (int)$system->powerReq;    //unsheddable draw off a fixed total
                continue;
            }

            if (!$fixed) $available += (int)$system->powerReq;       //spare-power model: shedding gives it back
        }

        return max(0, $available);
    }

    /* Mirrors the `powerLocked` flag the deployable systems publish in their own stripForJson
       (gravitic.php, supportWeapons.php, torpedo.php): a weapon paired with a Kirishiac orbital
       draws power and cannot be powered down once the orbital is DEPLOYED. `linkedOrbital` is
       null on every standard mount, which is what keeps this free for the rest of the game. */
    private static function isPowerLocked($system)
    {
        return (!empty($system->linkedOrbital) && empty($system->stowed));
    }

    /* ---------------------------------------------------------------- dropouts ------- */

    /* Mirrors Fighter::testCritical's comparison — roll versus the craft's remaining health,
       with the flight's dropout bonus and any critRollMod — but with (turns + DROPOUT_EXTRA)
       dice instead of one, which is the plan's "2d10, +1d10 per successive turn".
       ⚠️ It cannot be left to testCritical: that only runs on a craft DAMAGED this turn
       (criticals.php pass 1 gates on isDamagedOnTurn), so an untouched flight parked in a
       field would never roll at all. */
    private static function rollDropouts($flight, $consecutive, $gamedata)
    {
        /* Unreachable today - a flight cannot be Enormous - but kept faithful: "limited to the
           first die and do not increase" leaves the BASE 2d10 standing and stops the escalation,
           it does not cut the base roll in half. */
        $dice = (!empty($flight->Enormous))
              ? (self::ENORMOUS_DICE + self::DROPOUT_EXTRA)
              : ($consecutive + self::DROPOUT_EXTRA);
        $bonus = self::dropoutBonus($flight, $gamedata);
        $dropped = 0;

        foreach ($flight->systems as $craft) {
            if (!($craft instanceof Fighter)) continue;
            if ($craft->isDestroyed($gamedata->turn)) continue;

            $roll = self::roll($dice, self::DIE) + $bonus + (int)$craft->critRollMod + (int)$flight->critRollMod;
            if ($roll > $craft->getRemainingHealth()) {
                $crit = new DisengagedFighter(-1, $flight->id, $craft->id, "DisengagedFighter", $gamedata->turn);
                $crit->updated = true;
                $crit->newCrit = true;
                $craft->criticals[] = $crit;
                $dropped++;
            }
        }
        return $dropped;
    }

    /* The flight's own dropout modifier, taking the conditional per-faction variant into
       account exactly as Fighter::testCritical does (Torvalus and friends). */
    private static function dropoutBonus($flight, $gamedata)
    {
        if (!method_exists($flight, 'getSpecialDropout')) return 0;
        if ($flight->getSpecialDropout()) return (int)$flight->getDropOutBonusSpecial($gamedata);
        return (int)$flight->getDropOutBonus();
    }

    /* ---------------------------------------------------------------- helpers -------- */

    /* Where a unit's exposure record lives: its CnC, or — for a flight, which has none — its
       sample fighter, which is already where getCommonIniModifiers reads a flight's
       initiative criticals from. */
    private static function markerSystem($unit)
    {
        if ($unit instanceof FighterFlight) {
            return $unit->getSampleFighter();
        }
        return $unit->getSystemByName("CnC");
    }

    /* ⚠️ A DIRECT SCAN, NOT hasCritical(). EdfExposed is a `oneturn` critical, so hasCritical()
       reports it only on turn+1 — asking it "is there one for THIS turn" always answers no,
       and the sweep would re-roll the whole drain on every reload of the same turn. */
    private static function alreadyResolvedThisTurn($marker, $turn)
    {
        foreach ($marker->criticals as $crit) {
            if ($crit->phpclass === "EdfExposed" && (int)$crit->turn === $turn) return true;
        }
        return false;
    }

    private static function addParamCrit($system, $unit, $phpclass, $amount, $gamedata)
    {
        $crit = new $phpclass(-1, $unit->id, $system->id, $phpclass, $gamedata->turn, 0, false, (int)$amount);
        $crit->updated = true;
        $crit->newCrit = true; //force save: a oneturn crit is not "for the current turn"
        $system->criticals[] = $crit;
    }

    /* The combat-log row has to hang off a WEAPON ON THE SHOOTER: several places downstream
       resolve a fire order's weaponid against the shooter and expect one (Firing::prepareFiring
       skips a non-Weapon outright, and the client's getAllFireOrdersLog only ever looks at
       gamedata.getShip(fire.shooterid)), so a row parked on a CnC - or on the victim - would
       simply never be seen. RammingAttack is the safe universal choice - BaseShip::onConstructed
       gives every non-flight hull one and FighterFlight gives every craft one - and it is the
       same system HkJamming logs through.
       ⚠️ NOT the EnergyDrainingField itself: it is a ShipSystem, not a Weapon, and the client's
       log loop calls weapon.changeFiringMode() on whatever it resolves. */
    private static function logHost($unit)
    {
        $ram = $unit->getSystemByName("RammingAttack");
        if ($ram) return $ram;
        foreach ($unit->systems as $system) if ($system instanceof Weapon) return $system;
        return null;
    }

    /* $die is explicit because the EW drain rolls d6 while the other three roll d10 -
       defaulting it would make the one exception the easy thing to forget. */
    private static function roll($dice, $die = self::DIE)
    {
        $total = 0;
        for ($i = 0; $i < $dice; $i++) $total += Dice::d($die);
        return $total;
    }

    /* Surface the drain in the combat log the way HkJamming does — a technical fire order on a
       system the shooter certainly has. ⚠️ shotshit and shots stay 0: submitDamages links damage
       with an unknown fireorderid by (shooterid, weaponid, turn, shotshit>0), and a non-zero
       value here would let this informational row steal a real shot's damage.

       ⭐ THE SHOOTER IS THE WALKER, NOT THE VICTIM (user, 2026-09-04). This used to be a
       SELF-targeted order on the drained unit, so the log read "FIRE: <your ship>" in your own
       team's colour and every log filter - by shooter, by team, by ship - filed the drain under
       the ship suffering it. It is the Walker's field doing this, so the order now runs
       Walker -> victim like any other attack and the filters agree. $source comes from
       TacGamedata::getEdfSourceShip(); if it is ever null (no source map, or a hex covered only
       by the victim's own fleet, which resolve() already skips) this falls back to the old
       self-targeted row rather than losing the entry.
       ⚠️ The victim's NAME goes in the pubnotes text because "EdfExposure" is in
       weaponManager.doShortLogText - the short form prints the pubnotes ALONE, so the "at
       <target>" clause the long form would have shown is not rendered at all.

       ⭐ AND IT IS WRAPPED IN A BARE `shiplink` SPAN SO THE CLIENT CAN COLOUR IT (user, 2026-09-04).
       A log ship name is drawn in the READER's team colours (gamedata.getShipLogColorCss: mine
       green / ally blue / enemy red for a 2-team participant, the absolute palette for an observer),
       and the server cannot know who is reading. So it emits the name with no colour of its own and
       combatLog.colourShipLinksInNotes fills the style in per viewer, so the drained unit reads in
       the same colour its name has everywhere else in the log. ⚠️ Any future pubnotes that names a
       unit should use the same span rather than inventing a colour here.

       ⚠️⚠️ `rolled` MUST BE NON-ZERO OR THE PRINTED COMBAT LOG NEVER SHOWS THE ROW (user report,
       2026-09-04). weaponManager.getAllFireOrdersForLogPrint filters every order through
       isResolvedFireOrder(), which is nothing but `Number(fire.rolled) > 0` - so an order with
       rolled 0 is silently dropped before combatLog.logFireOrders ever sees it, pubnotes and all.
       HkJamming passes its real d20 there and so has always printed; this resolver's rolls are all
       spent on the criticals, so it passes a 1 purely as the "this order resolved" marker.
       Nothing else reads it: the log prints pubnotes, and shots/shotshit stay 0. */
    private static function logResult($unit, $gamedata, $consecutive, $thrust, $power, $ini, $ew, $dropouts, $blackout = false, $source = null)
    {
        //The row hangs off the SHOOTER's weapon: the client resolves fire.weaponid on the
        //shooter (combatLog/getAllFireOrdersLog), so a weapon on the victim would never resolve.
        $shooter = ($source && !$source->isDestroyed()) ? $source : $unit;
        $host    = self::logHost($shooter);
        if (!$host && $shooter !== $unit) {          //a Walker with no weapon at all: self-target
            $shooter = $unit;
            $host    = self::logHost($unit);
        }
        if (!$host) return;

        $parts = array();
        if ($thrust > 0) $parts[] = "-$thrust thrust";
        if ($power  > 0) $parts[] = "-$power power";
        if ($ini    > 0) $parts[] = "-" . ($ini * self::INI_SCALE) . " initiative";
        if ($ew     > 0) $parts[] = "-$ew EW";
        if ($dropouts > 0) $parts[] = $dropouts . ($dropouts === 1 ? " craft drops out" : " craft drop out");
        if ($unit instanceof FighterFlight) $parts[] = "cannot fire";
        if ($blackout) $parts[] = "REACTOR DRAINED - every powered system offline";
        if (empty($parts)) return;

        $fireOrder = new FireOrder(
            -1, "normal", $shooter->id, $unit->id,
            $host->id, -1, $gamedata->turn, 1,
            0, 1, 0, 0, 0,      //rolled = 1: the log's "this order resolved" marker, see above
            0, 0, 'EdfExposure', 10000
        );
        //The bare shiplink span is deliberate - combatLog.colourShipLinksInNotes paints it in the
        //READER's team colour and makes it clickable. See the note above.
        $fireOrder->pubnotes = "<br>ENERGY DRAINING FIELD affects "
            . '<span class="shiplink" data-id="' . (int)$unit->id . '">' . $unit->name . '</span>'
            . " - Turn $consecutive in the field. Penalties next turn: " . implode(", ", $parts) . ".";
        $fireOrder->addToDB = true;
        $host->fireOrders[] = $fireOrder;
    }
}
