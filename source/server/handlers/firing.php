<?php
/*old version of this file moved to .old file*/

class Firing
{
    public $gamedata;

    public static function validateFireOrders($fireOrders, $gamedata)
    {
        //Submit-time corruption guard. A stale client blueprint (out-of-date
        //staticShips for a phpclass) posts fire orders whose weaponid belongs to
        //an older system layout — system ids are positional (BaseShip::addSystem
        //assigns id = current system count), so any change to a ship's system
        //list shifts every later id and desynchronises older clients. Such orders
        //either reference a weaponid that no longer exists or one that now maps to
        //a NON-weapon system (e.g. a Thruster). If written to tac_fireorder they
        //crash the next game load in getFireOrdersForShips / TacGamedata (a
        //Thruster has no ->ballistic). DBManager::getFireOrdersForShips guards the
        //load path defensively; this is the DURABLE fix — reject the bad order
        //before it ever reaches the DB.
        //
        //An invalid order is detached from its owning system in place so the very
        //next $ship->getAllFireOrders() (used for the actual submit) no longer
        //includes it. We still return true: a corrupt order is dropped silently
        //rather than failing the whole ship's submission (some callers throw on
        //false, which would abort turn processing over one bad shot).
        foreach ($fireOrders as $fire) {
            //A selfIntercept order is a permission marker with no targeting of its own - leave it
            //alone. An 'intercept' order DOES carry an ordinary positional weaponid (the
            //interceptor), so it runs the same stale-blueprint weapon check as every other order
            //below; only the reading of ->targetid differs, and nothing here reads it. Before manual
            //interception was reintroduced no client emitted these, so skipping them cost nothing;
            //now it would leave the new path unguarded.
            if ($fire->type === 'selfIntercept')
                continue;

            $shooter = $gamedata->getShipById($fire->shooterid);

            /* ⭐ A HOMING MISSILE RE-ATTACK IS NEVER THE CLIENT'S TO SUBMIT
               (HOMING_MISSILE_PLAN.md). The server creates one at the end of the pass that missed
               (AmmoMissileRackS::persistNextPassOrder), so a row already exists for it before any
               player sees it - and since 2026-09-03 the order is VISIBLE during Initial Orders,
               which is the one phase where DBManager::submitFireorders inserts a ballistic order
               whatever its ->addToDB says. Without this, every commit would insert a second copy of
               every missile in the air, and a hand-edited POST could conjure 20 points of damage
               out of nothing.

               Same reject + detach convention as the corrupt-order path below: the order is dropped
               silently and the rest of the ship's submission goes through. Dropping it costs the
               player nothing - the authoritative copy is already in the database. */
            if ($fire->damageclass === AmmoMissileHM::REATTACK_CLASS) {
                $fire->rejected = true;
                Debug::log("validateFireOrders: rejecting POSTED homing-missile re-attack order "
                    . "(game {$gamedata->id}, shooter {$fire->shooterid}, weaponid {$fire->weaponid}, "
                    . "id {$fire->id}) - these are server-generated only.");
                if ($shooter) self::detachFireOrder($shooter, $fire);
                continue;
            }

            //Uncontrolled-flight guard. A remote-controlled Hunter-Killer flight whose
            //command link is severed (node shortfall or ELINT jamming) is driven entirely
            //by the server: AutomatedMovement moves it (drift/seek) and, if it ends co-located
            //with an enemy, createAutomatedRamOrders generates its ram. The player has no
            //control, so any fire order they managed to submit for it is spurious and would
            //DOUBLE the attack (player order + automated order) - exactly the 12-fire-order /
            //broken-return-damage bug on uncontrolled HK rams. Reject it before it reaches the
            //DB. Same detach + ->rejected convention as the corrupt-order path below.
            if ($shooter && self::isShooterUncontrolled($shooter, $gamedata)) {
                $fire->rejected = true;
                Debug::log("validateFireOrders: rejecting player fire order for UNCONTROLLED flight "
                    . "(game {$gamedata->id}, shooter {$fire->shooterid}, weaponid {$fire->weaponid}, "
                    . "type {$fire->type}) - server drives uncontrolled flights.");
                self::detachFireOrder($shooter, $fire);
                continue;
            }

            $weapon = $shooter ? $shooter->getSystemById($fire->weaponid) : null;

            //A Jump Engine order is not a shot, it is a VORTEX DECLARATION, and it has legality
            //rules of its own (JUMP_POINTS_PLAN.md section 2.1) that no other weapon has. It uses
            //the same ->rejected convention as the corrupt-order path below (see there for why the
            //flag alone is enough to keep an order out of the DB).
            if ($weapon instanceof JumpEngine) {
                self::validateVortexDeclaration($fire, $weapon, $shooter, $gamedata, $fireOrders);
                continue;
            }

            if ($weapon instanceof Weapon)
                continue; //valid — leave it attached

            //Invalid: shooter gone, weaponid missing, or it resolved to a
            //non-weapon system. Mark the order rejected AND detach it from its
            //owning system. The flag covers callers that submit the same array
            //they validated (e.g. the mine-ballistic path passes $newFireOrders to
            //both validate and submit); the detach covers callers that re-fetch
            //$ship->getAllFireOrders() for the submit. submitFireorders skips any
            //order with ->rejected set, so neither pattern can persist it.
            $fire->rejected = true;

            $sysType = $weapon ? get_class($weapon) : ($shooter ? 'missing weaponid' : 'missing shooter');
            Debug::log("validateFireOrders: rejecting corrupt fire order "
                . "(game {$gamedata->id}, shooter {$fire->shooterid}, weaponid {$fire->weaponid}, "
                . "type {$fire->type}) — resolved to: $sysType. Likely stale client blueprint.");

            if ($shooter)
                self::detachFireOrder($shooter, $fire);
        }

        return true;
    }

    /* JUMP_POINTS_PLAN.md STAGE 2 - server-side legality for a vortex declaration.
     *
     * The client already refuses every one of these (weaponManager.targetHex will not build the
     * order), so anything that reaches here is a stale blueprint or a tampered POST. Drop it the
     * same way a corrupt order is dropped - reject + detach, submission continues - rather than
     * returning false, which some callers turn into an exception that aborts the whole turn.
     *
     * ONLY Initial Orders declarations are judged. The persisted order is handed to
     * validateFireOrders again in Pre-Firing and Fire, by which point the ship has MOVED and the
     * 4-hex test would legitimately fail; re-judging a declaration that was legal when it was made
     * would silently detach it.
     *
     * ⚠️ $shooter is the ship from the REAL gamedata load, while $fire (and $fireOrders) come from
     * the POST-side rebuild - InitialOrdersGamePhase::process hands one of each. That is what makes
     * $shooter->getHexPos() trustworthy, since a POST-side ship carries no movement history. It is
     * also why the one-vortex test below reads $fireOrders and NOT $shooter->systems: the $gd
     * ship's fireOrders are the DB's, and this turn's declarations are not in the DB yet.
     *
     * detachFireOrder is deliberately not called here for the same reason - it matches by object
     * identity and the $gd ship holds different objects. ->rejected is what carries: submitFireorders
     * skips any order flagged with it, which is exactly the caller that matters. */
    private static function validateVortexDeclaration($fire, $weapon, $shooter, $gamedata, $fireOrders)
    {
        if (!$shooter) return;
        if ($gamedata->phase != 1) return;      //see above - only judge fresh declarations
        if ($fire->turn != $gamedata->turn) return;

        $reason = self::getVortexDeclarationBlock($fire, $weapon, $shooter, $gamedata, $fireOrders);
        if ($reason === null) return;           //legal - leave it alone

        $fire->rejected = true;
        //"mode" rather than "facing mode": on a FIXED GATE the firing mode is the programmed open
        //duration in turns, not a facing (JUMP_GATES_PLAN.md section 3.3), and both kinds of
        //declaration are rejected from here.
        Debug::log("validateFireOrders: rejecting vortex declaration "
            . "(game {$gamedata->id}, shooter {$fire->shooterid}, weaponid {$fire->weaponid}, "
            . "hex {$fire->x},{$fire->y}, mode {$fire->firingMode}) — $reason.");
    }

    /* The rules themselves (plan section 2.1). Returns null when the declaration is legal, or a
     * short reason for the log when it is not. Split out from the caller so the rule list reads as
     * a list. */
    private static function getVortexDeclarationBlock($fire, $weapon, $shooter, $gamedata, $fireOrders)
    {
        /* ⭐⭐ REINFORCEMENTS_PLAN.md STAGE 9 - THE ARRIVAL BRANCH IS NOW ABOVE THE LEGACY REFUSAL,
           AND THE ORDER OF THESE TWO LINES IS THE WHOLE OF "SHADOWS PHASE IN" (user ruling
           2026-08-29).

           A legacy drive has no vortex to OPEN - that is what markLegacy() turns off, and the
           refusal below is still exactly right for a ship on the board declaring a way out. But
           coming BACK is the other direction, and it was never a vortex gesture at all: an arrival
           declaration takes no range test, no line-of-sight test, no offline test and no charge
           test (see the entrance branch's own note below), which is precisely the list markLegacy()
           makes unanswerable. There is nothing left for the legacy flag to protect.

           ⚠️ SO THE REFUSAL MUST STAY, AND MUST STAY UNDERNEATH. A legacy hull that could declare
           an EXIT would spawn a yellow vortex on the board for a faction whose whole rule is that
           it leaves nothing behind. Widening isLegacyJump() rather than moving one line past it is
           the mistake this comment exists to stop.

           ⭐⭐ AND §3.4 - THE ARRIVAL BRANCH IS A DIFFERENT DECLARATION that happens to travel in
           the same fire-order shape, taken first and RETURNING, for the same reason the gate branch
           below is: almost none of the ship rules apply to it.

             - THERE IS NO RANGE TEST AND NO LINE-OF-SIGHT TEST, and their absence is the rule and
               not an omission (§2.2). The opener is in HYPERSPACE: it has no hex to measure either
               from. getHexPos() would answer with its slot's deployment-box centre - the 'start'
               movement row every ship is given - which is not a place it is, so a range test would
               be a real number computed from a fiction.
             - THERE IS NO OFFLINE TEST. A unit in hyperspace has no power allocation to be offline
               in; the engine's power rows do not exist.
             - THE CHARGE TEST DOES NOT APPLY either. A reinforcement has never been on the board,
               so it has never spent its drive.

           The discriminator is damageclass, mirroring 'jumppoint', and it is checked BEFORE the
           gate branch so a forged 'jumpexit' on a gate engine takes the arrival list (which refuses
           it, because a gate is not a hyperspace reinforcement) rather than the gate one. */
        if ($fire->damageclass === 'jumpexit')
            return self::getExitDeclarationBlock($fire, $weapon, $shooter, $gamedata, $fireOrders);

        //Plan section 9 - this hull is on the old one-click jump, so it has no vortex to declare.
        //The client cannot build the order either ($autoFireOnly / $hextarget false), so anything
        //arriving here is a stale blueprint from before the revert; drop it rather than let it
        //reach a spawn sweep that would refuse it silently.
        if ($weapon->isLegacyJump()) return "Jump Engine uses the legacy one-click jump";

        /* ⭐ REINFORCEMENTS_PLAN.md STAGE 8 - 'gateexit' IS THE SAME IDEA ONE STEP ALONG: a fixed
           GATE signal that asks for a doorway IN rather than out. It is judged by the GATE list
           below, which is right - every gate rule applies to it unchanged - so all this line has to
           do is refuse it on a SHIP's engine.

           ⚠️ AND IT MUST, because nothing further down would. The ship rules never read damageclass,
           so a forged 'gateexit' on a ship would be validated as an ordinary ENTRANCE declaration and
           then opened as one by getVortexDeclaration, which skips 'jumpexit' alone. A ship that
           wants a doorway in declares 'jumpexit' and is judged by the exit list above. */
        if ($fire->damageclass === 'gateexit' && !$weapon->isGateJump())
            return "gate arrival claim declared on a ship's Jump Engine";

        /* ⭐⭐ JUMP GATES (PHASE 2) - THE GATE BRANCH, AND IT IS TAKEN FIRST AND RETURNS.
           (JUMP_GATES_PLAN.md Stage 3, traps 1-4.)

           A fixed gate's claim is a DIFFERENT DECLARATION that happens to travel in the same
           fire-order shape, and almost none of the ship rules below apply to it:

             - THE RANGE TEST IS THE WRONG QUESTION. A ship projects a vortex up to 4 hexes away
               and the distance measured is the SHOOTER's; a gate opens one on its own hex, and the
               10 hexes markGate() puts in $range is how far away the CLAIMING PLAYER's nearest unit
               may be. Sharing one test would silently answer the other question.
             - THE OBSTRUCTION SWEEP ALONE WOULD REJECT EVERY CLAIM EVER MADE (plan trap 3). It
               refuses a hex holding any part of a Terrain unit - and the gate's own hex holds
               terrain: the gate. Same reason the Maintain branch below returns before it.
             - ONE-VORTEX-PER-SHIP is one-claim-per-PLAYER here, because a gate belongs to nobody in
               particular and several players may signal the same gate in the same turn (plan
               section 2.4 - that is the whole point of the contested-claim rule).

           So it is its own list, and it returns rather than falling through. */
        if ($weapon->isGateJump())
            return self::getGateSignalBlock($fire, $weapon, $shooter, $gamedata, $fireOrders);

        if ($weapon->isDestroyed($gamedata->turn)) return "Jump Engine is destroyed";
        if ($weapon->isOfflineOnTurn($gamedata->turn)) return "Jump Engine is offline";

        //ONE VORTEX PER SHIP - covers a second order on this engine and a second engine on the same
        //hull alike. Only orders BEFORE this one in the submission count, so the FIRST declaration
        //is the one that survives and every later one is dropped (scanning the whole array instead
        //would reject the first and keep the last, which is the wrong way round).
        foreach ($fireOrders as $other) {
            if ($other === $fire) break;
            if ($other->shooterid != $fire->shooterid) continue;
            if ($other->turn != $gamedata->turn) continue;
            if (!empty($other->rejected)) continue;
            if ($shooter->getSystemById($other->weaponid) instanceof JumpEngine)
                return "ship already has a vortex declaration this turn";
        }

        if ($fire->x === null || $fire->y === null || $fire->x === "null" || $fire->y === "null")
            return "no target hex";

        /* STAGE 2b - THE FACING IS NOW PLAYER-SETTABLE, SO IT IS NOW WORTH VALIDATING.
         * firingMode is the storage for the vortex facing (mode = facing + 1), written by the
         * on-map arrow control. Modes 1-6 are the six facings; mode 7 (STAGE 5) is the MAINTAIN
         * declaration, which is a different gesture judged by a different list - see below. Only a
         * tampered client can produce anything else - the arrow cannot - but an out-of-range mode
         * would reach the Stage 3 spawn sweep as a nonsense facing. */
        $mode = (int)$fire->firingMode;
        if ($mode < 1 || $mode > JumpEngine::MAINTAIN_MODE)
            return "illegal vortex facing (firing mode $mode)";

        $target = new OffsetCoordinate($fire->x, $fire->y);
        $distance = $shooter->getHexPos()->distanceTo($target);
        if ($distance > $weapon->range)
            return "target hex is $distance hexes away, limit is {$weapon->range}";

        /* STAGE 5 - MAINTAINING (plan section 2.4). The player keeps a vortex open by targeting its
         * OWN hex with the Jump Engine; that is the only thing that distinguishes the gesture, and
         * it is why the terrain test below has to be skipped here - the vortex IS terrain.
         *
         * The all-systems-offline requirement is deliberately NOT enforced at submit time. A hard
         * block in the submit path is a support burden ("why won't my turn commit?"), and the rule
         * has a natural consequence instead: JumpEngine::closeExpiredVortices sees the violation at
         * the end of the turn and closes the vortex with a reason in the log. The client warns
         * before the commit, which is where the player can still act on it.
         *
         * $weapon is the engine from the REAL gamedata load (see the caller), so its vortex state
         * has been rebuilt from the notes and these two questions can actually be answered. */
        if ($mode === JumpEngine::MAINTAIN_MODE){
            if (!$weapon->hasOpenVortex($gamedata->turn))
                return "no open vortex to maintain";

            //Not on the turn it was declared: it has not formed yet, and the opening declaration
            //is this turn's declaration.
            if ($weapon->vortexOpenTurn >= $gamedata->turn)
                return "vortex is still forming - it cannot be maintained until the turn after it was declared";

            $vortex = $gamedata->getShipById((int)$weapon->activeVortexId);
            if (!($vortex instanceof SpawnJumpPoint))
                return "this ship's vortex unit is gone";

            /* REINFORCEMENTS_PLAN.md §2.6 / §2.3 - AN EXIT HAS NO MAINTAIN. It is one-shot:
               it forms at the end of the turn it was declared, delivers its manifest on the next,
               and closes at the end of that one whatever anybody declares. The client never offers
               the control (isJumpVortex stays entrance-only, so JumpEngine.canMaintainVortex cannot see
               an exit), so only a tampered POST arrives here - but without this line a forged
               mode-7 order would hold an exit open indefinitely, and the ship that opened it
               could never open anything else (trap 5).
               Same shape and same reasoning as getMaintainDeclaration's gate refusal. */
            if ($vortex instanceof SpawnJumpPointExit)
                return "a jump point exit is one-shot and cannot be maintained";

            if (!$vortex->getHexPos()->equals($target))
                return "maintain must target this ship's own vortex hex";

            return null; //legal - and the obstruction sweep below must NOT run on a vortex hex
        }

        /* STAGE 5 - ONE VORTEX PER SHIP, ACROSS TURNS. The duplicate test above only sees a second
         * declaration inside the SAME submission; this is the one that stops a ship opening a
         * second jump point while it still holds one. It is deliberately after the maintain branch:
         * holding a vortex is precisely what makes a MAINTAIN legal and a second OPENING illegal.
         * (On the declaring turn itself this reads false - the unit is not spawned until
         * InitialOrdersGamePhase::advance - so a fresh declaration is never caught by it.) */
        if ($weapon->hasOpenVortex($gamedata->turn))
            return "ship already holds an open vortex";

        /* STAGE 6 - THE DRIVE HAS TO BE CHARGED. Opening a jump point spends the Jump Engine's
         * whole charge, and it recharges one per turn from the turn AFTER that jump point closed
         * (JumpEngine::getVortexRechargeLoad, and the ship file's 4th constructor argument is how
         * long that takes). The client keeps a recharging engine out of the weapon sweep by the
         * ordinary weaponManager.isLoaded test, so only a tampered POST reaches this. */
        $charge   = $weapon->getVortexRechargeLoad($gamedata->turn);
        $recharge = $weapon->getLoadingTime();
        if ($charge < $recharge)
            return "Jump Engine is still recharging ($charge/$recharge)";

        /* The hex must be EMPTY OF OBSTRUCTIONS - it may hold ships, friendly or enemy, but not any
         * part of a Terrain unit (which is also what a jump gate and, from Stage 3, a vortex are)
         * and not an Enormous unit. Terrain is tested across its WHOLE footprint, not just its
         * centre hex: an asteroid field's irregular hexOffsets shape and a moon's Huge radius both
         * count, which is what RammingAttack::getTerrainOccupiedHexes exists to answer. */
        foreach ($gamedata->ships as $unit) {
            if (!empty($unit->removed)) continue;
            if ($unit->isDestroyed($gamedata->turn)) continue;

            if ($unit->isTerrain()) {
                foreach (RammingAttack::getTerrainOccupiedHexes($unit) as $hex) {
                    if ($hex->q == $target->q && $hex->r == $target->r)
                        return "target hex holds terrain (unit {$unit->id})";
                }
                continue; //Terrain is Enormous too - do not also run the test below on it
            }

            if ($unit->Enormous && $unit->getHexPos()->equals($target))
                return "target hex holds an Enormous unit (unit {$unit->id})";
        }

        return null;
    }

    /* ⭐⭐ THE JUMP POINT EXIT RULES (REINFORCEMENTS_PLAN.md §2.2 and §3.4). Returns null when
     * the declaration is legal, or a short reason for the log when it is not - the same contract
     * getVortexDeclarationBlock above has, and reached only from its exit branch.
     *
     * The list is SHORT and almost none of it overlaps the ship list, which is the whole reason it
     * is separate. What is here:
     *
     *   1. the opener is the submitting player's own unit
     *   2. it is a reinforcement STILL IN HYPERSPACE
     *   3. its engine is undestroyed and non-legacy (the caller has already refused a legacy one)
     *   4. it has not already declared an exit this turn
     *   5. the facing is one of the six
     *   6. the hex is on the map and free of obstructions
     *
     * ⚠️ $shooter IS THE SHIP FROM THE REAL GAMEDATA LOAD, not the POSTed one - the caller hands
     * one of each, which is exactly what makes test 2 answerable. A POST-side ship carries no
     * $reinforcement and no $arrivalTurn at all (plan trap 3), so asking the posted object would
     * read every unit in the game as front-line and let anyone declare an exit.
     *
     * ⚠️ NO RANGE, NO LINE OF SIGHT, NO OFFLINE TEST, NO CHARGE TEST. See the branch that calls
     * this for why each one is the wrong question rather than a missing rule.
     */
    private static function getExitDeclarationBlock($fire, $weapon, $shooter, $gamedata, $fireOrders)
    {
        //1. YOUR OWN UNIT. A gate is the one thing in this game a player may order without owning
        //   it, and an exit is not that - the opener's own drive holds the doorway open.
        if ($shooter->userid != $gamedata->forPlayer)
            return "jump point exit declared on a unit the player does not own";

        //2. STILL IN HYPERSPACE. isReinforcement() is `bought as a reinforcement AND no arrival
        //   turn yet`, which is precisely "has not been assigned an exit". A unit that already
        //   has one, or that was bought front-line, opens an ENTRANCE like anything else on the board.
        if (!$shooter->isReinforcement())
            return "only a reinforcement still in hyperspace may open a jump point exit";

        //3. A WORKING DRIVE. A gate engine cannot reach here - a gate is not a reinforcement and
        //   test 2 has just failed it. isDestroyed is asked anyway: pre-battle damage can destroy a
        //   system before turn 1.
        //   ⚠️ A LEGACY DRIVE IS DELIBERATELY NOT REFUSED (Stage 9). Up to Stage 8 the caller
        //   dropped one before this list was ever reached; a phasing hull now arrives here and is
        //   judged by exactly these rules, because there is not one test in this method that a
        //   legacy engine cannot answer. What it gets instead of a vortex is decided at SPAWN time
        //   (JumpEngine::openExitVortex picks SpawnJumpPointPhaseIn), which is the only place the
        //   difference lives.
        if ($weapon->isDestroyed($gamedata->turn))
            return "Jump Engine is destroyed";

        /* 4. ONE EXIT PER UNIT. Only orders BEFORE this one in the submission count, so the
              FIRST declaration survives and every later one is dropped - the same way round as the
              ship rule, and for the same reason (scanning the whole array would reject the first
              and keep the last).
              ⚠️ Any JumpEngine order counts, not only another 'jumpexit': a unit in hyperspace has
              no business declaring an entrance either, and letting the two coexist would hand the Stage
              6 sweep two declarations on one engine. */
        foreach ($fireOrders as $other) {
            if ($other === $fire) break;
            if ($other->shooterid != $fire->shooterid) continue;
            if ($other->turn != $gamedata->turn) continue;
            if (!empty($other->rejected)) continue;
            if ($shooter->getSystemById($other->weaponid) instanceof JumpEngine)
                return "unit already has a jump point declaration this turn";
        }

        if ($fire->x === null || $fire->y === null || $fire->x === "null" || $fire->y === "null")
            return "no target hex";

        /* 5. THE FACING, stored as firingMode = facing + 1, exactly as an entrance's is. Modes 1-6 only:
              7 is MAINTAIN, which an exit does not have (§2.3 - it is one-shot), so a mode-7
              'jumpexit' is refused here rather than falling through to the maintain branch it can
              never legally reach. */
        $mode = (int)$fire->firingMode;
        if ($mode < 1 || $mode > 6)
            return "illegal exit facing (firing mode $mode)";

        $target = new OffsetCoordinate($fire->x, $fire->y);

        /* 6. ON THE MAP AND FREE OF OBSTRUCTIONS - one shared test, and the sharing is the point.
              JumpEngine::getExitHexBlock is asked the identical question by the END-OF-TURN
              DEVIATION CLAMP (§2.5), which walks outward from the scattered hex looking for the
              nearest legal one. If the two ever disagreed, either a declaration would be accepted
              onto a hex the clamp then refuses to place a doorway on, or the clamp would put one
              somewhere the declaration rules say cannot hold it. The reasons it returns are this
              method's own log strings; see there for what each test is and why. */
        $hexBlock = JumpEngine::getExitHexBlock($gamedata, $target);
        if ($hexBlock !== null) return $hexBlock;

        return null;
    }

    /* ⭐⭐ THE FIXED-GATE CLAIM RULES (JUMP_GATES_PLAN.md Stage 3 and section 2.1). Returns null
     * when the claim is legal, or a short reason for the log when it is not - same contract as
     * getVortexDeclarationBlock above, and reached only from its gate branch.
     *
     * $shooter is THE GATE, from the real gamedata load, and $gamedata->forPlayer is the CLAIMING
     * PLAYER - who is not the gate's owner and usually is not on its team. That asymmetry is the
     * whole feature: a gate is contested terrain with no owner priority (plan section 2.4).
     *
     * ⭐ IT HAS ONE SIDE EFFECT, AND IT IS DELIBERATE: on a legal claim it OVERWRITES $fire->targetid
     * with the server's own re-derived nearest qualifying unit (plan section 3.3, trap 4).
     * tac_fireorder has no player column and the gate belongs to nobody in particular, so targetid
     * is the only field that can record WHO claimed - and the client's value is a hint, never an
     * authority. A tampered POST cannot claim on someone else's behalf or fake a distance, because
     * both are recomputed here and again at resolution time.
     *
     * ⚠️ NO LINE-OF-SIGHT TEST APPEARS ANYWHERE IN THIS LIST, and that is the ruling, not an
     * omission (user ruling 2026-08-23). Signalling is a transmission; a ship projecting its own
     * vortex is an aimed effect and keeps its LoS rules.
     *
     * ⚠️ NOTHING HERE REVEALS THE SIGNALLER. See JumpEngine::hasVortexDeclaration - a gate claim
     * sits on the GATE's engine, so a hidden unit that signals stays hidden. */
    private static function getGateSignalBlock($fire, $weapon, $gate, $gamedata, $fireOrders)
    {
        if ($gate->isDestroyed($gamedata->turn)) return "the jump gate is destroyed";
        if ($weapon->isDestroyed($gamedata->turn)) return "the gate's Jump Engine is destroyed";
        if ($weapon->isOfflineOnTurn($gamedata->turn)) return "the gate's Jump Engine is offline";

        if ($fire->x === null || $fire->y === null || $fire->x === "null" || $fire->y === "null")
            return "no target hex";

        /* THE HEX IS ALWAYS THE GATE'S OWN, and there is nothing for the player to aim (plan
         * section 2.2): the vortex forms in the gate's mouth, facing the way the gate was placed.
         * The client never sends anything else, so a mismatch is a tampered POST trying to
         * relocate a jump point it does not own. */
        $target = new OffsetCoordinate($fire->x, $fire->y);
        if (!$gate->getHexPos()->equals($target))
            return "a gate opens its vortex on its OWN hex only";

        /* THE FIRING MODE IS THE PROGRAMMED OPEN DURATION, 1-4 turns (markGate). Modes 5-7 have no
         * meaning on a gate at all - and mode 7 is MAINTAIN, which a gate does not have, so a
         * forged one must never survive to reach getMaintainDeclaration (which refuses a gate
         * engine outright for exactly this reason). */
        $mode = (int)$fire->firingMode;
        if ($mode < 1 || $mode > JumpEngine::MAX_VORTEX_TURNS)
            return "illegal programmed duration (firing mode $mode)";

        /* ⭐ A WOUNDED GATE'S CAP IS A CLAMP, NOT A REFUSAL (plan section 2.5 and test 18). The
         * client's stepper already caps at this, so the only way to exceed it is a stale blueprint
         * or a tampered POST - and in both cases losing the player's whole turn over one number
         * they cannot see is the wrong answer, while a shorter jump point is exactly the rule.
         * Rewriting the mode HERE rather than only clamping at resolution keeps the invariant
         * "every persisted claim is within the gate's cap" true in the DB as well as in memory;
         * the min() in the resolution sweep stays as belt and braces. */
        $maxHold = JumpEngine::getGateMaxHold($gate);
        if ($mode > $maxHold){
            Debug::log("Jump gate signal: clamping claim on gate {$gate->id} (game {$gamedata->id}, "
                . "player {$gamedata->forPlayer}) from $mode turns to $maxHold - reactor damage.");
            $fire->firingMode = $maxHold;
        }

        //A gate holds ONE jump point at a time, exactly as a ship does. (On the claiming turn this
        //reads false - the vortex is not spawned until InitialOrdersGamePhase::advance.)
        if ($weapon->hasOpenVortex($gamedata->turn))
            return "the gate already holds an open vortex";

        /* THE 20-TURN RECHARGE (plan section 2.5). It is the engine's 4th constructor argument and
         * getVortexRechargeLoad derives the whole state off the vortex note, so this is the same
         * test a ship engine takes a few lines up - the gate-specific part is that reactor damage
         * lengthens the target, which is what getVortexRechargeTime() adds.
         *
         * ⚠️ getVortexRechargeTime(), NOT getLoadingTime(): the latter reads $loadingtime, which
         * Weapon::setLoading overwrites from the stored tac_systemdata row, and in a game recorded
         * before Phase 1 Stage 6 that row still says 1. See the method for the whole note. (The ship
         * branch above still asks getLoadingTime and so is lenient on such a game; that is
         * pre-existing Phase 1 behaviour and is deliberately left alone here.) */
        $charge   = $weapon->getVortexRechargeLoad($gamedata->turn);
        $recharge = $weapon->getVortexRechargeTime();
        if ($charge < $recharge)
            return "the gate's Jump Engine is still recharging ($charge/$recharge)";

        /* ⭐ THE ONE RULE THAT IS ENTIRELY NEW: the CLAIMING PLAYER must have a live, deployed,
         * non-terrain unit within the gate's signal range. WHICH unit is never chosen and never
         * matters (plan section 2.1) - only that one exists, and how far away the nearest is,
         * because that is what settles a contested claim. */
        $signaller = $weapon->getNearestGateSignaller($gate, $gamedata, $gamedata->forPlayer);
        if (!$signaller)
            return "player {$gamedata->forPlayer} has no live unit within {$weapon->range} hexes of the gate";

        /* ONE CLAIM PER PLAYER PER GATE PER TURN, and the FIRST is the one that survives - the same
         * rule and the same reason as the ship one-vortex test (scanning the whole array instead
         * would reject the first and keep the last, which is the wrong way round).
         *
         * Only orders BEFORE this one in the submission count. A player commits Initial Orders once
         * per turn, and InitialOrdersGamePhase hands this branch only that gate engine's orders, so
         * the submission IS the population. Other players' claims are separate submissions and are
         * settled at resolution time, not here. */
        foreach ($fireOrders as $other) {
            if ($other === $fire) break;
            if ($other->shooterid != $fire->shooterid) continue;
            if ($other->turn != $gamedata->turn) continue;
            if (!empty($other->rejected)) continue;
            if ($gate->getSystemById($other->weaponid) instanceof JumpEngine)
                return "this player already has a signal on this gate this turn";
        }

        /* ⭐⭐ REINFORCEMENTS_PLAN.md STAGE 8 - THE ARRIVAL FLAVOUR, AND IT IS THE LAST RULE ON
         * PURPOSE. A claim carrying damageclass 'gateexit' asks the gate for a doorway IN: it
         * spawns a SpawnJumpPointExit instead of a SpawnJumpPoint, and the units waiting in
         * hyperspace ride it - a fresh wave on each turn of the programmed hold (plan section 0).
         *
         * EVERY RULE ABOVE APPLIES TO IT UNCHANGED, which is the whole reason this is one branch at
         * the bottom rather than a fourth list: the gate, the hex, the duration, the recharge, the
         * signal range and the one-claim-per-player test are all properties of SIGNALLING a gate and
         * say nothing about which way the door then faces. This is the only extra question.
         *
         * ⚠️ AND IT IS A CORRECTNESS TEST, NOT AN EFFICIENCY ONE. A game without allowReinforcements
         * cannot contain a unit in hyperspace, so an arrival claim in one can only be a tampered POST
         * - and letting it through would open a ONE-WAY doorway that nobody can arrive through and no
         * ship can jump out of (plan section 2.6), which is strictly worse for the claimant than the
         * entrance they would otherwise have had. Refuse it rather than granting a useless jump
         * point.
         *
         * ⭐⭐ THE SECOND RULE IS GONE (user ruling 2026-09-02). Until now this also refused a claim
         * from a player with nothing of their OWN left in hyperspace, on the reasoning above applied
         * one step further - a door with nobody behind it is a door nobody uses. That was too narrow
         * in two directions at once. A gate exit stands for the whole of its programmed hold and ANY
         * unit of ANY side may ride it (JUMP_GATES_PLAN.md section 2.6), so the test barred a player
         * from opening a doorway their TEAMMATE's reinforcements would come through, and barred
         * opening one this turn for a wave only ready to ride it on a later one.
         * InitialOrdersGamePhase::collectGateOpeners has always accepted ANYBODY's standing gate exit
         * as an opener a manifest may name, so that half of the feature already worked; this test was
         * the only thing stopping the door being opened in the first place. A claim with nobody
         * behind it now costs exactly what it costs - the gate's charge, spent on a door - and that
         * is the player's call to make. */
        if ($fire->damageclass === 'gateexit'){
            if (!$gamedata->rules || !$gamedata->rules->hasRuleName('allowReinforcements'))
                return "this game has no reinforcements rule";
        }

        //Legal. Record WHO claimed, from the server's own reckoning - see the header note.
        $fire->targetid = $signaller->id;

        return null;
    }

    /* True when $shooter is a remote-controlled flight that is Uncontrolled THIS turn
     * (command link severed - node shortfall or ELINT jamming), so the server drives it
     * and any player fire order for it is spurious. Mirrors AutomatedMovement::isUncontrolled:
     * Uncontrolled is a oneturn crit on the sample fighter placed on turn T (effect T+1),
     * and hasCritical's oneturn handling matches it on the effect turn. Cheap-guarded on
     * remoteControl so ordinary ships short-circuit immediately. */
    private static function isShooterUncontrolled($shooter, $gamedata)
    {
        if (empty($shooter->remoteControl)) return false;
        if (!($shooter instanceof FighterFlight)) return false;
        $sample = $shooter->getSampleFighter();
        if (!$sample) return false;
        return $sample->hasCritical("Uncontrolled", $gamedata->turn) > 0;
    }

    /* Remove a specific FireOrder object from any system (or fighter subsystem)
     * on $ship that currently holds it. Used by validateFireOrders to drop a
     * corrupt order before submit. Matches by object identity, not weaponid,
     * because the whole point is that the order's weaponid is untrustworthy. */
    private static function detachFireOrder($ship, $badFire)
    {
        foreach ($ship->systems as $system) {
            self::spliceFireOrder($system, $badFire);
            if (!empty($system->systems) && is_array($system->systems)) {
                foreach ($system->systems as $sub) {
                    self::spliceFireOrder($sub, $badFire);
                }
            }
        }
    }

    private static function spliceFireOrder($system, $badFire)
    {
        if (empty($system->fireOrders) || !is_array($system->fireOrders))
            return;
        foreach ($system->fireOrders as $i => $existing) {
            if ($existing === $badFire) {
                unset($system->fireOrders[$i]);
            }
        }
        //Reindex so downstream count()/[0] access stays well-behaved.
        $system->fireOrders = array_values($system->fireOrders);
    }


    //compares weapons' capability as interceptor
    //if intercept rating is the same, faster-firing weapon would go first
    public static function compareInterceptAbility($weaponA, $weaponB)
    {
        if ($weaponA->intercept > $weaponB->intercept) {
            return -1;
        } else if ($weaponA->intercept < $weaponB->intercept) {
            return 1;
        } else if ( max($weaponA->loadingtime, $weaponA->normalload) < max($weaponB->loadingtime, $weaponB->normalload) ) {
            return -1;
        } else if ( max($weaponA->loadingtime, $weaponA->normalload) > max($weaponB->loadingtime, $weaponB->normalload) ) {
            return 1;
        } else {
            return 0;
        }
    } //endof function compareInterceptAbility


    /*gets all ready intercept-capable weapons that aren't otherwise assigned*/
    public static function getUnassignedInterceptors($gamedata, $ship)
    {
        $currTurn = $gamedata->turn;
        $toReturn = array();
        if ($ship instanceof FighterFlight) { //separate procedure for fighters
            $exclusiveWasFired = false;
            foreach ($ship->systems as $fighter) {
                if ($fighter->isDestroyed()) continue;
                foreach ($fighter->systems as $weapon) {
//                    if (($weapon instanceof Weapon) && ($weapon->ballistic != true)) {
                    if (($weapon instanceof Weapon)) {  //Changed line to allow ballistics to intercept  //GTS 07 Aug 2022
                        if (($weapon->exclusive) && $weapon->firedOnTurn($currTurn)) {
                            $exclusiveWasFired = true;
                            continue;
                        } else if ((!$weapon->firedOnTurn($currTurn)) && ($weapon->intercept > 0) && (self::isValidInterceptor($gamedata, $weapon))) {//not fired this turn, intercept-capable, and valid interceptor
                            if ((!isset($weapon->ammunition)) || ($weapon->ammunition > 0)) {//unlimited ammo or still has ammo available
                                $toReturn[] = $weapon;
                            }
                        }
                    }
                }
            }
            if ($exclusiveWasFired) $toReturn = array(); //exclusive weapon was fired, nothing can intercept!
        } else { //proper ship
           
            if (!(($ship->unavailable === true) || $ship->isDisabled())) { //ship itself can fight this turn
                foreach ($ship->systems as $weapon) {               	
                    if ((!($weapon instanceof Weapon))) continue; //not a weapon, or a ballistic weapon         
                    /* ===== AUTO-INTERCEPTOR-MISSILES (1 of 3) - THE SWITCH ITSELF =============
                       THIS is where the server decides, on the player's behalf, that a missile
                       launcher will spend an Interceptor round this turn. Nothing else opts it in.

                       $canModesIntercept is true for the generic MissileLauncher (missile.php),
                       false for A-Racks, Bomb Racks and Fighter Racks. switchModeForIntercept()
                       walks $interceptArray, picks the firing mode with the highest intercept
                       rating - i.e. Interceptor - CHANGES THE WEAPON INTO THAT MODE and sets
                       $weapon->intercept, which is what puts the launcher into $toReturn below and
                       so into the automation pool. A launcher that has already fired this turn is
                       skipped, but an idle loaded one is enrolled without being asked.

                       The player is never consulted because the consent gate lives in
                       isValidInterceptor (site 2 of 3) and only bites when the weapon's effective
                       loading time is > 1. A rack declared with $loadingtime = 1 - which is what
                       the Class-D Missile Rack, the Interceptor carrier, is - therefore never has
                       the selfIntercept marker demanded of it. That is exactly the behaviour
                       reported on 2026-08-20.

                       TO MAKE IT OPT-IN: this single line is the kill switch. Guarding it with the
                       same selfIntercept test isValidInterceptor uses (or with a new per-weapon
                       flag) leaves the launcher in its offensive mode, $weapon->intercept stays 0,
                       and it never reaches the pool. Manual interception is unaffected: the manual
                       path sets the order's firing mode itself, in automateIntercept, and never
                       comes through here.
                       Ammo is NOT consulted here - that is site 3 of 3. ==================== */
					if ($weapon->canModesIntercept && (!($weapon->firedOnTurn($currTurn)))) $weapon->switchModeForIntercept(); //To check for intercept values in non-default modes if weapon has appropriate marker and hasn't fired e.g. Intercept Missile etc                    
                    
                    /* //Old method before the additiona of split shot weapons, keep for now
                    if ((!$weapon->firedOnTurn($currTurn) || $weapon->canSplitShots) && ($weapon->intercept > 0)) {
                        if (self::isValidInterceptor($gamedata, $weapon)) {//not fired this turn, intercept-capable, and valid interceptor                 	
                            $toReturn[] = $weapon;
                        }
                    }
                    */

                        if (
                            (!$weapon->firedOnTurn($currTurn) || $weapon->canSplitShots)
                            && ($weapon->intercept > 0)
                        ) {
                            if (self::isValidInterceptor($gamedata, $weapon)) {
                                $toReturn[] = $weapon;
                            }
                        }                        
                }
            }
        }

        return $toReturn;
    } //endof getUnassignedInterceptors


    /* returns best possible shot to intercept (or null if none is available)
    */
    public static function getBestInterception($gamedata, $currInterceptor, $incomingShots)
    {
        $bestInterception = null;
        $bestInterceptionVal = 0;
        foreach ($incomingShots as $firingOrder) {
            $isLegal = self::isLegalIntercept($gamedata, $currInterceptor, $firingOrder);
            if (!$isLegal) continue; //not a legal interception at all for this weapon
            $currInterceptionMod = $currInterceptor->getInterceptionMod($gamedata, $firingOrder);
            if ($currInterceptionMod <= 0) continue; //can't effectively intercept

            $shooter = $gamedata->getShipById($firingOrder->shooterid);
            $target = $gamedata->getShipById($firingOrder->targetid);
            $firingWeapon = $shooter->getSystemById($firingOrder->weaponid);

            $chosenLoc = $firingOrder->chosenLocation;
            if (!($chosenLoc > 0)) $chosenLoc = 0; //just in case it's not set/not a number!
            if ($target instanceof FighterFlight) {
                $exampleFighter = $target->getSampleFighter(); //not necessarily correct for adaptive armor, but have to base on something...
                $armour = $exampleFighter->getArmourComplete($target, $shooter, $firingWeapon->weaponClass);
                //$armour = 0; //let's simplify here...
            } else {
                $structureSystem = $target->getStructureSystem($chosenLoc);
                $armour = $structureSystem->getArmourComplete($target, $shooter, $firingWeapon->weaponClass); //shooter relevant only for fighters - and they don't care about calculating ambiguous damage!
            }
            $expectedDamageMax = $firingWeapon->maxDamage;
            $expectedDamageMin = $firingWeapon->minDamage;
            $expectedDamage = (($expectedDamageMin + $expectedDamageMax) / 2) - $armour;
            $expectedDamage = max(0.5, $expectedDamage); //assume some damage is always possible!
            //reduce damage for non-Standard modes...
            switch ($firingWeapon->damageType) {
                case 'Flash': //increase expected damage on account of collateral! 
                    $expectedDamage = $expectedDamage * 1.25;
                    break;
                case 'Raking': //Raking damage gets reduced multiple times, account for that a bit! - another armour down!
                    if ($expectedDamage > 10) { ///simplified, assuming Raking will be in 10-strong rakes
                        $expectedDamage = $expectedDamage - $armour; //from second rake - let's simplify that two full weights of armor will be deduced from damage
                        $expectedDamage = min(10, $expectedDamage);
                    }
                    break;
                case 'Piercing': //Piercing does little damage to actual outer section... but it does PRIMARY damage! very dangerous!
                    $expectedDamage = $expectedDamage * 1.1;
                    break;
                case 'Pulse': //multiple hits - assume half of max pulses hit !
                    $expectedDamage = 0.5 * $expectedDamage * max(2, $firingWeapon->maxpulses);
                    break;
                case 'Standard': //default damage!
                    $expectedDamage = $expectedDamage ;
                    break;
                default: //something else: can't be as good as Standard!
                    $expectedDamage = $expectedDamage * 0.9;
                    break;
            }
            //if weapon does no damage by itself, assume it has other, very relvant effect - comparable to 10 damage!
            if ($firingWeapon->maxDamage == 0) $expectedDamage = 10;
            $expectedDamage = max(0.1, $expectedDamage);//estimate _some_ damage always...
            //multiply by Shots...
            $expectedDamage = $expectedDamage * max(1, $firingWeapon->shots);

            //called shots are more important...
            if ($firingOrder->calledid != -1) {
                $expectedDamage = $expectedDamage * 1.1;
            }

            //how much is actually reduced?
            $hitChanceBefore = $firingOrder->needed - $firingOrder->totalIntercept;
            $hitChanceAfter = $hitChanceBefore - $currInterceptionMod;
            $hitChanceAfter = max(0, $hitChanceAfter);//negative numbers are irrelevant, effectively You can interept to 0
            $modifier = min(100, $hitChanceBefore) - $hitChanceAfter;
            if ($modifier <= 0) { //after interception hit chance is still over 100%... let's count as something, but much less - say, multiply by 0.1!
                $modifier = 0.1 * ($hitChanceBefore - $hitChanceAfter);
            }

            //...how much damage is actually stopped?
            $stoppedDamage = $modifier * $expectedDamage;//to get actual damage statistically stopped, You need to multiply this by 0.01 - but it's completely irrelevant for higher/lower comparision

            if ($stoppedDamage > $bestInterceptionVal) { //this is best interception candidate found so far!
                $bestInterception = $firingOrder;
                $bestInterceptionVal = $stoppedDamage;
            }
        }
        return $bestInterception;
    }//endof getBestInterception


    /*adds indicated weapon's capabilities to total interception variables
    	may create intercept order itself if needed
    */
    public static function addToInterceptionTotal($gamedata, $intercepted, $interceptor, $prepareOrder = false)
    {
        //update numbers appropriately
        $intercepted->totalIntercept += $interceptor->getInterceptionMod($gamedata, $intercepted);
        $intercepted->numInterceptors++;

        if ($prepareOrder) { //new firing order (intercept) should be prepared?
            $interceptFire = new FireOrder(-1, "intercept", $interceptor->getUnit()->id, $intercepted->id, $interceptor->id, -1,
                $gamedata->turn, $interceptor->firingMode, 0, 0, $interceptor->defaultShots, 0, 0, null, null
            );
            $interceptFire->addToDB = true;
			checkForSelfInterceptFire::setFired($interceptor->getUnit()->id, $interceptor->id, $gamedata->turn);
            $interceptor->fireOrders[] = $interceptFire;
        }
	    
		//fireDefensivaly call is needed for weapons that suffer some side effect when firing defensively
		$interceptor->fireDefensively($gamedata, $intercepted);
    } //endof function addToInterceptionTotal


    /*Marcin Sawicki, October 2017: change approach: allocate interception fire before ANY fire is actually resolved!
        this allows for auto-intercepting ballistics, too.
    */
    public static function automateIntercept($gamedata)
    { //automate allocation of intercept weapons
        //prepare list of all potential intercepts and all incoming fire
        $allInterceptWeapons = array();
        $allIncomingShots = array();
        foreach ($gamedata->ships as $ship) {
            if($ship->getTurnDeployed($gamedata) > $gamedata->turn)	continue; //Ship not deployed yet. Remove to avoid problems.            
            $interceptWeapons = self::getUnassignedInterceptors($gamedata, $ship);
            $allInterceptWeapons = array_merge($allInterceptWeapons, $interceptWeapons);
            $incomingShots = $ship->getAllFireOrders($gamedata->turn);
            $allIncomingShots = array_merge($allIncomingShots, $incomingShots);
        }

        //update intercepion totals!
        //Index this turn's orders by id so a manual intercept order can resolve the shot it names
        //directly. Resolving BY ID rather than scanning for a match is what lets validation tell
        //"no such order" apart from "found it", which the old scan silently conflated.
        $ordersById = array();
        foreach ($allIncomingShots as $anyOrder) {
            /*⚠️ ONLY ORDERS THAT HAVE A REAL ROW. A server-generated order that has not been
            inserted yet carries id -1, and several can carry it at once - so indexing on it would
            let one arbitrary shot answer to "-1" and let a stale client's intercept order be
            credited against a missile nobody aimed at. An order with no id has no identity a client
            could legitimately have named, so it simply cannot be manually intercepted.*/
            if ((int)$anyOrder->id <= 0) continue;
            $ordersById[$anyOrder->id] = $anyOrder;
        }
        //Per-weapon tally of manual intercept orders ACCEPTED so far, keyed shipid_weaponid, so the
        //gun cap is enforced against survivors rather than against everything that was submitted.
        $manualInterceptsAccepted = array();
        foreach ($allIncomingShots as $fireOrder) {
            /* 'intercept' ONLY - deliberately NOT 'selfIntercept' (fixed 2026-08-18).
            The two types live in different id spaces: an 'intercept' order's targetid is the id of
            the FIRE ORDER being intercepted, but a 'selfIntercept' order's targetid is the SHIP's
            own id (every one of the five client creation sites sets `targetid: ship.id` - see
            weaponManager.setSelfIntercept / onDeclareSelfInterceptSingle and the doMultipleSelfIntercept
            overrides in molecular.js, pulse.js, special.js). Matching one against the other below
            was only ever a coincidence test, and when a tac_fireorder.id happened to equal a
            tac_ship.id in the same game it fired twice over: the marker was credited as a real
            interceptor against an unrelated shot (bumping totalIntercept/numInterceptors AND
            running fireDefensively, so backlash triggered and an interceptor missile was drawn),
            and the same weapon was then ALSO given a genuine intercept order by the automation
            below - a double count.

            A selfIntercept order is a PERMISSION MARKER, not an assignment: it says "this
            long-recharge weapon consents to be auto-assigned". Its real consumer is
            isValidInterceptor(), which requires it for loadingTimeActual > 1, plus the
            split-weapon gun refund at the head of the assignment loop. Both are untouched by this
            guard, so the weapon still enters the pool and still gets a real order - it just no
            longer credits a shot nobody assigned it to. */
            if ($fireOrder->type != "intercept") continue; //manually assigned interception - no others exist at this point

            /* Player-declared intercept orders are validated HERE, per order, immediately before
            they are credited. Until manual interception was reintroduced nothing emitted such an
            order, so this path had no legality checking at all: isLegalIntercept is otherwise only
            reached from getBestInterception, i.e. the AUTOMATED side. A hand-crafted or stale-client
            order used to be honoured with no arc, uninterceptable, skindancing, ammo, readiness or
            ownership test whatsoever.

            Per order rather than as a pre-pass, because an interceptor missile's magazine is read by
            canInterceptAtAll and DEBITED by fireDefensively inside addToInterceptionTotal: N orders
            from one rack must each be checked against the RUNNING ammo state, exactly as the
            automation's per-gun loop does. A pre-pass would approve all N against an undrawn
            magazine and the surplus would then draw rounds that are not there. */
            $shooter = $gamedata->getShipById($fireOrder->shooterid);
            $interceptor = $shooter ? $shooter->getSystemById($fireOrder->weaponid) : null;

            /* The intercept rating that counts is the one for the ORDER's firing mode, not whatever
            mode this weapon object happens to be in when the loop reaches it - several weapons carry
            a per-mode interceptArray. The mode also selects which round MissileLauncher checks in
            canInterceptAtAll and draws in fireDefensively, so it has to stay set across
            addToInterceptionTotal and be put back afterwards: these objects are read again later in
            the same request, and changeFiringMode rewrites arcs, guns and damage along with it. */
            $originalMode = null;
            if (($interceptor instanceof Weapon)
                && isset($interceptor->firingModes[$fireOrder->firingMode])
                && $interceptor->firingMode != $fireOrder->firingMode
            ) {
                $originalMode = $interceptor->firingMode;
                $interceptor->changeFiringMode($fireOrder->firingMode);
            }

            $intercepted = self::validateManualIntercept(
                $gamedata, $fireOrder, $shooter, $interceptor, $ordersById, $manualInterceptsAccepted
            );

            if ($intercepted !== null) {
                $tallyKey = $fireOrder->shooterid . '_' . $fireOrder->weaponid;
                $manualInterceptsAccepted[$tallyKey] = (isset($manualInterceptsAccepted[$tallyKey])
                    ? $manualInterceptsAccepted[$tallyKey] : 0) + 1;
                self::addToInterceptionTotal($gamedata, $intercepted, $interceptor);
            }

            if ($originalMode !== null) $interceptor->changeFiringMode($originalMode);
        }


        //delete fire orders that are intercept orders or are hex-targeted or have no chance of hitting
        $shotsStillComing = array();
        foreach ($allIncomingShots as $fireOrder) {
            if (($fireOrder->needed - $fireOrder->totalIntercept) <= 0) continue;//no chance of hitting
            if (($fireOrder->type == "selfIntercept") || ($fireOrder->type == "intercept")) continue; //interception shot
            $shooter = $gamedata->getShipById($fireOrder->shooterid);
            $firingWeapon = $shooter->getSystemById($fireOrder->weaponid);            
            $firingWeapon->notActuallyHexTargeted($fireOrder);//Some weapons start hex targeted, but become normal e.g. BM Launcher - 4.3.24 DK
            if ($firingWeapon->hextarget) continue;//hex-targeted
            $shotsStillComing[] = $fireOrder;
        }
        $allIncomingShots = $shotsStillComing;
        $shotsStillComing = null; //just free memory

        //sort list of all potential intercepts - most effective first
        usort($allInterceptWeapons, [self::class, 'compareInterceptAbility']);

        //assign interception
        while ((count($allInterceptWeapons) > 0)) {//weapons can still intercept!
            $currInterceptor = array_shift($allInterceptWeapons); //most capable interceptor available

            //A split shot weapon may have fired some shots offensively, deduct these from intercept guns.                
            if ($currInterceptor->canSplitShots) {
                $currGuns = $currInterceptor->guns - count($currInterceptor->fireOrders); //Normally we just deduct fireOrders from total guns.
                foreach($currInterceptor->fireOrders as $fired){ //However, accelerator weapons like Discharge Gun have to be manually set, and this fireOrder shouldn't count against total.
                    if($fired->type == "selfIntercept") $currGuns++; //So if selfIntercept, add shot back on.
                }    
                $currInterceptor->guns = $currGuns; //Deduct fireOrders from intercept.                          
            }

            for ($i = 0; $i < $currInterceptor->guns; $i++) { //a single weapon can intercept multiple times...
                //find shot it would be most profitable to intercept with this weapon, and intercept it!
                $shotToIntercept = self::getBestInterception($gamedata, $currInterceptor, $allIncomingShots);
                if ($shotToIntercept != null) {
                    self::addToInterceptionTotal($gamedata, $shotToIntercept, $currInterceptor, true); //add numbers AND create order
                }
            }
        }

        //all possible interceptions have been made!
    } //endof function automateIntercept


    /* Is this player-declared 'intercept' order legal? Returns the FireOrder it intercepts, or null -
    in which case the order has already been rejected, detached and logged.

    $weapon must ALREADY be in $fire->firingMode when this is called: several weapons carry a per-mode
    interceptArray, and MissileLauncher::canInterceptAtAll reads the magazine for the CURRENT mode's
    round. The caller owns setting and restoring the mode.

    Deliberately NOT isValidInterceptor(): that helper's loadingTimeActual > 1 branch demands a
    selfIntercept marker on the weapon, and for a hand-picked interceptor the declaration itself IS
    that consent - a selfIntercept marker only ever meant "I permit the automation to use me". Every
    other test it makes is repeated below.

    Drops are logged and never surfaced to the player: the client-side predicate is meant to be strict
    enough that reaching a drop means a stale client blueprint or a hand-edited payload, not a
    legitimate order the player expected to work. */
    private static function validateManualIntercept($gamedata, $fire, $shooter, $weapon, $ordersById, $acceptedPerWeapon)
    {
        $reason = null;
        $intercepted = null;

        //1. the interceptor itself must exist, be a weapon, and be able to shoot right now.
        if (!$shooter) {
            $reason = "shooterid does not resolve to a unit";
        } else if (!($weapon instanceof Weapon)) {
            $reason = "weaponid resolves to " . ($weapon ? get_class($weapon) : "nothing") . ", not a Weapon";
        } else if (!$weapon->getWeaponForIntercept()) {
            $reason = "weapon class cannot act as an interceptor";
        } else if (!isset($weapon->firingModes[$fire->firingMode])) {
            $reason = "unknown firing mode " . $fire->firingMode;
        } else if ($weapon->isDestroyed()) {
            $reason = "weapon is destroyed";
        } else if ($weapon->isOfflineOnTurn($gamedata->turn)) {
            $reason = "weapon is offline this turn";
        } else if ($weapon->stowed) {
            $reason = "weapon is stowed"; //e.g. Antigravity Beam on a docked Kirishiac Orbital
        } else if ($weapon->getTurnsloaded() < $weapon->getLoadingTime()) {
            $reason = "weapon is not loaded (" . $weapon->getTurnsloaded() . "/" . $weapon->getLoadingTime() . ")";
        } else if ($weapon->intercept <= 0) {
            $reason = "weapon has no intercept rating in mode " . $fire->firingMode;
        }

        //2. the shot being intercepted must exist, this turn, and be a real shot.
        if ($reason === null) {
            $intercepted = isset($ordersById[$fire->targetid]) ? $ordersById[$fire->targetid] : null;
            if (!$intercepted) {
                $reason = "targetid " . $fire->targetid . " matches no fire order this turn";
            } else if ($intercepted->type == "intercept" || $intercepted->type == "selfIntercept") {
                $reason = "targetid " . $fire->targetid . " is itself an " . $intercepted->type . " order";
            } else {
                //isLegalIntercept dereferences both of these unguarded, as does the automated path.
                //Here we are already validating, so resolve them first rather than fatal on a
                //stale-blueprint order that slipped past submit-time validation.
                $victimShooter = $gamedata->getShipById($intercepted->shooterid);
                $victimWeapon = $victimShooter ? $victimShooter->getSystemById($intercepted->weaponid) : null;
                if (!($victimWeapon instanceof Weapon)) {
                    $reason = "intercepted order " . $intercepted->id . " has no resolvable weapon";
                } else if (!$gamedata->getShipById($intercepted->targetid)) {
                    $reason = "intercepted order " . $intercepted->id . " has no resolvable target unit";
                } else {
                    //Same call the assignment loop below makes, and idempotent: it only ever clears
                    //hextarget on an order that does name a unit (BM Launcher and friends start
                    //hex-targeted and become normal). Without it a shot that is about to stop being
                    //hex-targeted would be refused here.
                    $victimWeapon->notActuallyHexTargeted($intercepted);
                    if ($victimWeapon->hextarget) {
                        //getInterceptionMod returns 0 for these, so crediting one would buy nothing
                        //while still bumping numInterceptors and drawing an interceptor missile.
                        $reason = "intercepted order " . $intercepted->id . " is hex-targeted";
                    }
                }
            }
        }

        //3. gun accounting (R2/R3/R7). A weapon that fired offensively cannot also intercept, and one
        //that manually intercepted cannot also fire. A canSplitShots weapon spends ONE gun per order
        //and may legitimately mix the two; a non-split weapon may not. Both are capped at ->guns,
        //counted against orders ACCEPTED so far so a surplus drops the extras, not the whole set.
        if ($reason === null) {
            $offensiveOrders = 0;
            foreach ($weapon->fireOrders as $order) {
                if ($order->turn != $gamedata->turn) continue;
                if ($order->type == "selfIntercept") continue; //a permission marker, not a shot
                if ($order->type == "intercept") continue; //counted via $acceptedPerWeapon instead
                $offensiveOrders++;
            }
            $tallyKey = $fire->shooterid . '_' . $fire->weaponid;
            $alreadyAccepted = isset($acceptedPerWeapon[$tallyKey]) ? $acceptedPerWeapon[$tallyKey] : 0;

            if (!$weapon->canSplitShots && $offensiveOrders > 0) {
                $reason = "non-split weapon already has " . $offensiveOrders . " offensive order(s) this turn";
            } else if (($offensiveOrders + $alreadyAccepted) >= $weapon->guns) {
                $reason = "all " . $weapon->guns . " gun(s) already spent ("
                    . $offensiveOrders . " offensive + " . $alreadyAccepted . " intercept)";
            }
        }

        //4. the same legality test the automation applies - arc (including split arcs and turret
        //jam), uninterceptable, doNotIntercept, ballisticIntercept, mines, skindancing, freeintercept
        //and fighter-escort geometry, and canInterceptAtAll (where a missile rack checks its magazine).
        if ($reason === null && !self::isLegalIntercept($gamedata, $weapon, $intercepted)) {
            $reason = "isLegalIntercept refused it";
        }

        if ($reason === null) return $intercepted;

        //Same rejected + detach convention as validateFireOrders. The DB row was written at commit
        //time and is left alone: detaching removes the order from resolution and from the totals,
        //and validation is deterministic given the same gamedata, so a re-read reaches this verdict
        //again. Deleting the row would buy nothing and cost a write.
        $fire->rejected = true;
        Debug::log("automateIntercept: dropping manual intercept order (game " . $gamedata->id
            . ", ship " . $fire->shooterid . ", weaponid " . $fire->weaponid
            . ", order " . $fire->id . ", targetid " . $fire->targetid . ") - " . $reason . ".");
        if ($shooter) self::detachFireOrder($shooter, $fire);

        return null;
    } //endof function validateManualIntercept


    private static function isValidInterceptor($gd, $weapon)
    {
        if (!($weapon instanceof Weapon)) return false;
        $weapon = $weapon->getWeaponForIntercept();

        if (!$weapon) {
            return false;
        }

        if ($weapon->intercept == 0) {
            return false;
        }
        if ($weapon->isDestroyed()) {
            //print($weapon->displayName . " is destroyed and cannot intercept " . $weapon->id);
            return false;
        }
        if ($weapon->isOfflineOnTurn($gd->turn)) {
            return false;
        }
        if ($weapon->stowed) { //Antigravity Beam on a docked Kirishiac Orbital - non-operational
            return false;
        }

        // not loaded yet
        if ($weapon->getTurnsloaded() < $weapon->getLoadingTime()) {
            return false;
        }
	
	$loadingTimeActual = max($weapon->getLoadingTime(),$weapon->normalload);//Accelerator (or multi-mode) weapons may have loading time of 1, yet reach full potential only after longer charging 

        /* ===== AUTO-INTERCEPTOR-MISSILES (2 of 3) - WHY NOBODY IS ASKED ======================
           The block below is the ONLY consent gate in the automated path: a weapon whose effective
           loading time is > 1 must carry a player-placed 'selfIntercept' marker or it is refused.
           That is the "don't waste my slow gun on interception without being told" rule.

           Loading time is the RACK's own $loadingtime (there is no per-firing-mode loading time -
           MissileLauncher's constructor copies range, damage, intercept and the rest out of the
           ammo class, but not loadingtime), and $normalload is 0 on every rack. So a rack declared
           with $loadingtime = 1 - the Class-D Missile Rack at AmmoMissileRackD is exactly that, and
           it carries Interceptor as its DEFAULT round - gives $loadingTimeActual == 1, this gate
           NEVER RUNS FOR IT, and the launcher is used automatically. That is the reported
           behaviour, and this is why: the rule keys on loading time, not on whether the weapon
           consumes ammo. A rack with $loadingtime = 2 does fall in here and does demand the marker.

           Note this is a DESCRIPTION of the current behaviour, not the place to change it: raising
           the threshold here would also start demanding markers from every ordinary 1-turn gun.
           The narrow lever is site 1 of 3. ================================================= */
        /*  //Old method  
	    if ($loadingTimeActual > 1) { 
            if (isset($weapon->fireOrders[0])) {
                if ($weapon->fireOrders[0]->type != "selfIntercept") {
                    return false;
                }
            } else {
                return false;
            }
        }                     
        */   
        //New Method taking itno account Split Shot weapons.
        if ($loadingTimeActual > 1) {
                $hasSelfIntercept = false;
        
                // If the weapon can split shots, check all fireOrders
                if ($weapon->canSplitShots) {
                    foreach ($weapon->fireOrders as $order) {
                        if ($order->type == "selfIntercept") {
                            $hasSelfIntercept = true;
                            break;
                        }
                    }
                } else {
                    // Fallback for non-splitting weapons: check only the first
                    if (isset($weapon->fireOrders[0]) && $weapon->fireOrders[0]->type == "selfIntercept") {
                        $hasSelfIntercept = true;
                    }
                }
        
                if (!$hasSelfIntercept) {
                    return false;
                }
        }

        //Added new checks for split shots weapons so they don't get ruled out at this moment.   
        if ($loadingTimeActual == 1 
            && $weapon->firedOnTurn($gd->turn) 
            && !$weapon->canSplitShots) { //Retain normal check for weapon that can't split, but leave room for split shot exceptions.
            return false;
        }

        //Now check if split shot weapons have any spare guns to intercept with.
        if ($weapon->canSplitShots) { //Weapon that might have fired some shots, but still have some remaining to intercept with.
            $count = count($weapon->fireOrders); //How many fireOrders were made?
            foreach ($weapon->fireOrders as $order1) {
                if ($order1->type == "selfIntercept") {
                    $count--;
                }
            }
            if($count >= $weapon->guns){ //If fireOrders have used up all shots, cannot intercept.
                return false;
            }
        }        

        return true;
    } //endof function isValidInterceptor


    public static function doIntercept($gd, $ship, $intercepts)
    {
        //returns all valid interceptors as $intercepts
        if (sizeof($intercepts) == 0) {
            //    debug::log($ship->phpclass." has nothing to intercept.");
            return;
        };
        usort($intercepts, [self::class, 'compareIntercepts']);
        foreach ($intercepts as $intercept) {
            $intercept->chooseTarget($gd);
        }
    }


    public static function compareIntercepts($a, $b)
    {
        if (sizeof($a->intercepts) > sizeof($b->intercepts)) {
            return -1;
        } else if (sizeof($b->intercepts) > sizeof($a->intercepts)) {
            return 1;
        } else {
            return 0;
        }
    }


    /*would this be a legal interception?...*/
    public static function isLegalIntercept($gd, $weapon, $fire)
    {
        if ($fire->type == "intercept") {
            //Debug::log("Fire is intercept\n");
            return false;
        }
        if ($fire->type == "selfIntercept") {
            //Debug::log("Fire is intercept\n");
            return false;
        }

        if ($weapon->intercept == 0) {
            //Debug::log("Weapon has intercept of zero\n");
            return false;
        }

        $shooter = $gd->getShipById($fire->shooterid);
        $target = $gd->getShipById($fire->targetid);
        $interceptingShip = $weapon->getUnit();
        $firingweapon = $shooter->getSystemById($fire->weaponid);	
        
        if($interceptingShip instanceof Mine){
            if(!$interceptingShip->getCommandControl()){
                return false; //Mines generally can't intercept using their weapons, unless they have command controller upgrade.        
            }
        }

        if ($firingweapon->doNotIntercept){ //some attacks simply aren't subject to interception - like being in a field, or ramming attacks
            //Debug::log("Target weapon cannot be intercepted\n");
            return false;
        }

        if (($firingweapon->uninterceptable) && (!($weapon->canInterceptUninterceptable))) { //some weapons can intercept normally unintereptable shots
            //Debug::log("Target weapon is uninterceptable\n");
            return false;
        }
        
        /* //Removed to allow shooting at own team.
        if ($shooter->team == $interceptingShip->team) {
            //Debug::log("Fire is friendly\n");
            return false;
        }
        */

        if ((!($firingweapon->ballistic)) && $weapon->ballisticIntercept) {
            //Debug::log("Can only intercept ballistics, and this is not ballistic\n");
            return false;
        }



        $relativeBearing = $firingweapon->getIncomingBearing($interceptingShip, $fire, $gd);

        //New arc check that checks split arcs like Heavy Slicer as well = DK Dec 2025
        //
        //ONLY a split-arc mount gets its arc arrays handed to isInAnyArc, because only there do the
        //arrays mean "arcs held at once" (see the invariant on Weapon::$startArcArray). On every
        //other weapon they are PER FIRING MODE, and passing those made a mount intercept across the
        //union of all its modes' arcs - a BSGLtKineticEnergyWeaponVA in its narrow mode went on
        //intercepting through the whole wide arc it does not currently have.
        //
        //TURRET JAM (ReducedArcs critical, Vree saucer turrets) drops the arrays too: a jammed mount
        //is locked to its reduced arc, and that arc lives ONLY in startArc/endArc, so the arrays
        //still hold the arcs the mount had before it jammed. Without this a jammed split mount would
        //go on intercepting right around the hull.
        $useSplitArcs = ($weapon instanceof Weapon) && $weapon->splitArcs && !$weapon->isArcRestricted();

        $interceptStartArcs = $useSplitArcs ? ($weapon->startArcArray ?? []) : [];
        $interceptEndArcs = $useSplitArcs ? ($weapon->endArcArray ?? []) : [];

        if (!mathlib::isInAnyArc(
            $relativeBearing,
            $weapon->startArc,
            $weapon->endArc,
            $interceptStartArcs,
            $interceptEndArcs
        )) {
            return false;
        }

        /* //Old check for just main arcs
        if (!mathlib::isInArc($relativeBearing, $weapon->startArc, $weapon->endArc)) {
            //Debug::log("Fire is not on weapon arc\n");
            return false;
        }
        */
	
        if (!$firingweapon->ballistic && isset($shooter->skinDancing[$target->id]) && $shooter->skinDancing[$target->id] === true) {          
            return false; // Can't intercept for ships skindancing on you.
        }

        // Check if the Intercepting Ship is a failed Skindancer
        if (!empty($interceptingShip->skinDancing)) {
            foreach ($interceptingShip->skinDancing as $status) {
                if ($status === 'Failed') {
                    return false; // Failed skindancers cannot intercept
                }
            }
        }

		//added for Vorlon weapons, also used for Interceptor missile.
		if(!$weapon->canInterceptAtAll($gd, $fire, $shooter, $target, $interceptingShip, $firingweapon)) return false; //some weapons do have exotic rules whether they can intercept at all

        if ($interceptingShip->id == $target->id) { //ship intercepting fire directed at it - usual case
            return true;
        } else { //fire directed at third party - only particular weapons are able to do so
            //Debug::log("Target is this another ship\n");
            if ($interceptingShip instanceof FighterFlight) { //can intercept ballistics IF together with target ship form start of turn
            	
				//if($weapon->freeinterceptspecial){ //weapon has own routine that handles whether it's capable of intercepting the shot
				if($weapon->freeinterceptspecial && $target->team == $interceptingShip->team && $shooter->id != $interceptingShip->id){ //Special freeintercept and target from same team.                
					return $weapon->canFreeInterceptShot($gd, $fire, $shooter, $target, $interceptingShip, $firingweapon);					
				}            	
            	
                if ($firingweapon->ballistic) { //only ballistic weapons can be intercepted this way
                    if ($target instanceof FighterFlight) {
                        return false; //cannot intercept fire at other fighters
                    } else {//target is ship
                        $selfPosNow = $interceptingShip->getCoPos();
                        $targetPosNow = $target->getCoPos();
                        //if ($fire->turn == 1) { //first turn - assume starting positions did match (technical reasons - units cannot start on same hex!)|| Now they can - DK 27.3.26
                        //    $selfPosPrevious = $selfPosNow;
                        //    $targetPosPrevious = $targetPosNow;
                        //} else {//standard - check actual position at the end of previous turn
                            //Null-guard: getLastTurnMovement can return null when a unit
                            //has no eligible movement entry for the start-of-turn lookup
                            //(immobile terrain with only a "start" move on turn-1 firing;
                            //or any future call site where the filter excludes every entry).
                            //An immobile unit's previous-turn position equals its current
                            //position, so falling back to getCoPos() preserves the same-hex
                            //check semantics without changing established movement rules.
                            $movement = $interceptingShip->getLastTurnMovement($fire->turn);
                            $selfPosPrevious = $movement ? mathlib::hexCoToPixel($movement->position) : $selfPosNow;
                            $movement = $target->getLastTurnMovement($fire->turn);
                            $targetPosPrevious = $movement ? mathlib::hexCoToPixel($movement->position) : $targetPosNow;
                        //}

                        if (($selfPosNow == $targetPosNow) && ($selfPosPrevious == $targetPosPrevious)) {
                            return true;
                        } else {
                            return false;
                        }
                    }
                } else { //incoming weapon is not ballistic
                    return false;
                }
            } else { //ship
                if (!$weapon->freeintercept) {
                    //Debug::log("Target is another ship, and this weapon is not freeintercept \n");
                    return false;
                }

				//if($weapon->freeinterceptspecial){ //weapon has own routine that handles whether it's capable of intercepting the shot
				if($weapon->freeinterceptspecial && $target->team == $interceptingShip->team && $shooter->id != $interceptingShip->id){ //Special freeintercept and target from same team.
					return $weapon->canFreeInterceptShot($gd, $fire, $shooter, $target, $interceptingShip, $firingweapon);					
				}else if ($target->team == $interceptingShip->team && $shooter->id != $interceptingShip->id){ //standard $freeintercept - must be between firing unit and target
					//new approach: bearing to target is opposite to bearing shooter, +/- 60 degrees
					//$oppositeBearing = mathlib::addToDirection($relativeBearing,180);//bearing exactly opposite to incoming shot
					$oppositeBearingFrom = mathlib::addToDirection($relativeBearing, 120);//bearing exactly opposite to incoming shot, minus 60 degrees
					$oppositeBearingTo = mathlib::addToDirection($oppositeBearingFrom, 120);//bearing exactly opposite to incoming shot, plus 60 degrees
					$targetBearing = $interceptingShip->getBearingOnUnit($target);
					if (mathlib::isInArc($targetBearing, $oppositeBearingFrom, $oppositeBearingTo)) {
						//Debug::log("VALID INTERCEPT\n");
						return true;
					}
				}
            }
        }

        //Debug::log("INVALID INTERCEPT\n"); //should not reach here!
        return false;
    } //endof function isLegalIntercept


    public static function preparePreFiring($gamedata){
	//additional call for weapons needing extra preparation
        foreach ($gamedata->ships as $ship){
            if($ship instanceof FighterFlight){
                foreach ($ship->systems as $ftr){
                    foreach ($ftr->systems as $system){                    
                        $system->beforePreFiringOrderResolution($gamedata);
                    }    
                }
            }else{
                foreach ($ship->systems as $system){
                    $system->beforePreFiringOrderResolution($gamedata);
                }
            }
        }

        //Chameleon Sensor Suite (D9): this phase runs its OWN calculateHitBase pass and
        //firePreFiringWeapons() allocates real damage from it, so the withdrawal has to happen here
        //too - otherwise a prefiring called shot at a disguised ship still reaches the real hull
        //carrying a simulacrum system id. Idempotent: a second call sees calledid already -1 and
        //skips the order, leaving $chameleonCalledId intact.
        self::withdrawChameleonCalledShots($gamedata);

        //Pre-Firing resolves a phase EARLIER than the Fire Phase, so a fleet that conceded during
        //Initial Orders or Movement would otherwise still get its pre-firing shots away here.
        self::withdrawSurrenderedFireOrders($gamedata);

        //A flight that spent last turn inside an Energy Draining Field is disrupted and cannot
        //fire this turn.
        //⚠️ NOT gated on TacGamedata::$edfPresent: the field that grounded the flight may have
        //been destroyed since, and the crit outlives it by a turn. The method self-gates on the
        //flight having any criticals at all, which is free for every ordinary flight.
        self::withdrawGroundedFighterFireOrders($gamedata);

        $ambiguousFireOrders  = array();
        foreach ($gamedata->ships as $ship){
            foreach($ship->getAllFireOrders($gamedata->turn) as $fire){
                //Remove ballistic and potential intercepts from firing pool
                if ($fire->type === "intercept" || $fire->type === "selfIntercept" || $fire->type === "ballistic"){
                    continue;
                }

                $weapon = $ship->getSystemById($fire->weaponid);
                if (!($weapon instanceof Weapon)){ //this isn't a weapon after all...
                    continue;
                }		
                if (self::isHyperspaceLogOrder($fire)) continue; //not a shot - see the method
               
		        $weapon->changeFiringMode($fire->firingMode); //For Chaff Missile
		
		    
                $fire->priority = $weapon->priority;
				//take different AF priority into account!
				if($fire->targetid !== -1){ //actually directed at an unit!
					$target = $gamedata->getShipById($fire->targetid); 
					if ($target instanceof FighterFlight){
						$fire->priority = $weapon->priorityAF;
					}
				}	
				
                if($weapon->isTargetAmbiguous($gamedata, $fire)){
                    $ambiguousFireOrders[] = $fire;
                }else{
                    $weapon->calculateHitBase($gamedata, $fire);
                }
            }
                     
        }

        //calculate hit chances for ambiguous firing!
        foreach($ambiguousFireOrders as $fireOrder){
            $ship = $gamedata->getShipById($fireOrder->shooterid);
            $weapon = $ship->getSystemById($fireOrder->weaponid);
            $weapon->calculateHitBase($gamedata, $fireOrder);
        }

    }//endof function preparePreFiring	    


public static function firePreFiringWeapons($gamedata){	
        $rammingOrders  = array();

        //Ramming Orders first
        foreach ($gamedata->ships as $ship){		           
            
            //Now fire Ramming Orders before other weapons while we're looking through ships in this section.    
            foreach($ship->getAllFireOrders($gamedata->turn) as $fire){
                if ($fire->turn != $gamedata->turn){
                    continue;
                }

                $weapon = $ship->getSystemById($fire->weaponid);
                if (!$weapon->isRammingAttack) continue; //Only interested in Ramming Attacks!
                if (self::isHyperspaceLogOrder($fire)) continue; //not a ram - see the method

                $rammingOrders[] = $fire;
            }
            
        }    
        
        usort($rammingOrders, [self::class, 'compareFiringOrders']);

        foreach ($rammingOrders as $ramming){
            $ship = $gamedata->getShipById($ramming->shooterid);
            self::fire($ship, $ramming, $gamedata);
        }        
        
        $fireOrders  = array();  //Array for non-ramming ship fire.      
        //Now fire ship weapons.
        foreach ($gamedata->ships as $ship){	

            if ($ship instanceof FighterFlight) continue; //No fighter attacks handled here now that Ramming Attacks are handled above - DK 03.25      
            if($ship->isDestroyed()) continue; //Ship could be destroyed by ramming now.

            foreach($ship->getAllFireOrders($gamedata->turn) as $fire){
                //Remove ballisitic and potential intercepts from firing pool, normal types don#t exist yet.
                if ($fire->type === "intercept" || $fire->type === "selfIntercept"  || $fire->type === "ballistic"){
                    continue;
                }

                $weapon = $ship->getSystemById($fire->weaponid);
                if (!($weapon instanceof Weapon)) continue; //...just in case...               
                if($weapon->isDestroyed($gamedata->turn) && !$weapon->ballistic) continue; //now individual weapons can be destroyed by Ramming before firing, but not ballistics.                
                if ($weapon->isRammingAttack) continue; //Ramming Attacks have already been resolved.

                //$fire->priority = $weapon->priority; //fire order priority already set, and may differ from basic weapon priority!
                $fireOrders[] = $fire;
            }
            
        }
        usort($fireOrders, [self::class, 'compareFiringOrders']);

        //Now fire ship weapons.
        foreach ($fireOrders as $fire){
            $ship = $gamedata->getShipById($fire->shooterid);
            //$wpn = $ship->getSystemById($fire->weaponid);
            //$p = $wpn->priority;
            // debug::log("resolve --- Ship: ".$ship->shipClass.", id: ".$fire->shooterid." wpn: ".$wpn->displayName.", priority: ".$p." versus: ".$fire->targetid);
            self::fire($ship, $fire, $gamedata);
        }

        // From here on, only fighter units are left.

	    //FIRE fighters at fighters
        $chosenfires = array();
        foreach ($gamedata->ships as $ship) {
            // Remember: ballistics that have been fired must still be
            // resolved! So don't continue on destroyed units/fighters.
            if (!($ship instanceof FighterFlight)) {
                continue;
            }

            foreach($ship->getAllFireOrders($gamedata->turn) as $fire){
                if ($fire->turn != $gamedata->turn){
                    continue;
                }

                //Remove ballisitic and potential intercepts from firing pool
                if ($fire->type === "intercept" || $fire->type === "selfIntercept"  || $fire->type === "ballistic"){
                    continue;
                }

                $weapon = $ship->getSystemById($fire->weaponid);

                //ramming attacks are already allocated!
                if ($weapon->isRammingAttack) continue;
                    
                //ballistic weapons will still reach their targets, but direct fire from fighters previously destroyed will not happen
                if ( (!$weapon->ballistic) && ($ship->getFighterBySystem($weapon->id)->isDestroyed()) ) continue;

                $chosenfires[] = $fire;
            }
        }
        usort($chosenfires, [self::class, 'compareFiringOrders']);

        foreach ($chosenfires as $fire){
            $shooter = $gamedata->getShipById($fire->shooterid);
            $target = $gamedata->getShipById($fire->targetid);
            if ( ($target == null) || ($target instanceof FighterFlight) ) {
                self::fire($shooter, $fire, $gamedata);
            }
        }

        //FIRE fighters at ships
        $chosenfires = array();
        foreach ($gamedata->ships as $ship) {
                // Remember: ballistics that have been fired must still be
                // resolved! So don't continue on destroyed units/fighters.
                if (!($ship instanceof FighterFlight)) {
                    continue;
                }

            foreach($ship->getAllFireOrders($gamedata->turn) as $fire){
                if ($fire->turn != $gamedata->turn){
                    continue;
                }

                //Remove ballisitic and potential intercepts from firing pool
                if ($fire->type === "intercept" || $fire->type === "selfIntercept"  || $fire->type === "ballistic"){
                    continue;
                }

                $weapon = $ship->getSystemById($fire->weaponid);

                //ramming attacks are already allocated!
                if ($weapon->isRammingAttack) continue;

                //ballistic weapons will still reach their targets, but direct fire from fighters previously destroyed will not happen
                if ( (!$weapon->ballistic) && ($ship->getFighterBySystem($weapon->id)->isDestroyed()) ) continue;

                $chosenfires[] = $fire;
            }
        }
        usort($chosenfires, [self::class, 'compareFiringOrders']);

        //FIRE rest of fighters
        foreach ($chosenfires as $fire){
            $shooter = $gamedata->getShipById($fire->shooterid);
            $target = $gamedata->getShipById($fire->targetid);
            if  ( ($target != null) && (!($target instanceof FighterFlight)) ) {
                self::fire($shooter, $fire, $gamedata);
            }
        }

    } //endof method firePreFiringWeapons





    /*Marcin Sawicki: count hit chances for starting fire phase fire*/
    /* A HYPERSPACE DEPARTURE IS NOT A SHOT (JUMP_POINTS_PLAN.md Stage 4).
     *
     * Leaving the battle writes a RammingAttack fire order at 100/100 against the departing unit
     * itself, purely so the combat log has a line to render - the damage entry beside it has
     * already destroyed the primary structure. The BOOST path never needed guarding because
     * JumpEngine::doHyperspaceJump runs at the very END of fireWeapons, after every gather has
     * happened. A vortex jump-out resolves a whole phase earlier, in Movement, so by the time
     * Pre-Firing and Fire load their orders this one is sitting in tac_fireorder looking exactly
     * like a ram - and would be re-resolved as one, ramming the departed unit into itself.
     *
     * Four gathers consult this: preparePreFiring / firePreFiringWeapons and prepareFiring /
     * fireWeapons. Matching on damageclass rather than on type keeps it independent of how the
     * order was submitted. */
    public static function isHyperspaceLogOrder($fire){
        return $fire->damageclass === 'HyperspaceJump'
            || $fire->damageclass === 'JumpFailure'
            || $fire->damageclass === 'JumpVortex';   //STAGE 6 - a jump point opening or closing
    }

    public static function prepareFiring($gamedata, $dbManager = null){
	//additional call for weapons needing extra preparation
        foreach ($gamedata->ships as $ship){
            foreach ($ship->systems as $system){
                $system->beforeFiringOrderResolution($gamedata);
            }
        }

        //Chameleon Sensor Suite (D9): a called shot AT a disguised ship names a system on the
        //simulacrum. Both hulls number their systems 0..N, so the raw id resolves on the real ship
        //too and lands the call on an arbitrary real system (finding #16). Withdraw it ONCE, here,
        //before any hit-chance maths - four separate places downstream do
        //$target->getSystemById($fire->calledid) and none of them can tell a foreign id from a
        //real one.
        self::withdrawChameleonCalledShots($gamedata);

        //A fleet that conceded part-way through the turn must not still be shooting when the
        //phase resolves - see the method for why nothing downstream would have stopped it.
        self::withdrawSurrenderedFireOrders($gamedata);

        //A flight that spent last turn inside an Energy Draining Field is disrupted and cannot
        //fire this turn (WALKERS_OF_SIGMA_PLAN.md 2.2).
        //⚠️ NOT gated on TacGamedata::$edfPresent: the field that grounded the flight may have
        //been destroyed since, and the crit outlives it by a turn. The method self-gates on the
        //flight having any criticals at all, which is free for every ordinary flight.
        self::withdrawGroundedFighterFireOrders($gamedata);

        //Uncontrolled Hunter-Killers that ended movement co-located with an enemy ram it
        //(no player to submit the ram order). Done before ram orders are gathered below.
        //$dbManager is threaded through so each automated ram FireOrder is persisted
        //IMMEDIATELY (real DB id) before fireWeapons resolves it - otherwise its return-damage
        //DamageEntry captures the in-memory id (-1) and the firing fighter's destruction never
        //links to a combat-log row (the controlled/player-submitted ram already has a real id).
        AutomatedMovement::createAutomatedRamOrders($gamedata, $dbManager);

        $ambiguousFireOrders  = array();
        foreach ($gamedata->ships as $ship){
            foreach($ship->getAllFireOrders($gamedata->turn) as $fire){
                if ($fire->type === "intercept" || $fire->type === "selfIntercept" || $fire->type === "prefiring"){
                    continue;
                }

                $weapon = $ship->getSystemById($fire->weaponid);
                if (!($weapon instanceof Weapon)){ //this isn't a weapon after all...
                    continue;
                }		
                if (self::isHyperspaceLogOrder($fire)) continue; //not a shot - see the method
               
		        $weapon->changeFiringMode($fire->firingMode); //For Chaff Missile
		
		    
                $fire->priority = $weapon->priority;
				//take different AF priority into account!
				if($fire->targetid !== -1){ //actually directed at an unit!
					$target = $gamedata->getShipById($fire->targetid); 
					if ($target instanceof FighterFlight){
						$fire->priority = $weapon->priorityAF;
					}
				}	
				
                if($weapon->isTargetAmbiguous($gamedata, $fire)){
                    $ambiguousFireOrders[] = $fire;
                }else{
                    $weapon->calculateHitBase($gamedata, $fire);
                }
            }
                     
        }

        //calculate hit chances for ambiguous firing!
        foreach($ambiguousFireOrders as $fireOrder){
            $ship = $gamedata->getShipById($fireOrder->shooterid);
            $weapon = $ship->getSystemById($fireOrder->weaponid);
            $weapon->calculateHitBase($gamedata, $fireOrder);
        }

    }//endof function prepareFiring

    /*Chameleon Sensor Suite (D9). Withdraws every called shot aimed at a ship the SHOOTER still
      sees as somebody else, so that a simulacrum system id never reaches getSystemById() on the
      real hull. Runs once per resolution, before hit chances are calculated.

      WITHDRAWN, NOT TRANSLATED (user's ruling, 2026-08-02). The shooter named a mount on a sheet
      that is not this ship, and no mapping onto the real hull can be right. The class-match this
      used to do took the first gun of that class in CONSTRUCTION ORDER: in game 4272 a shot called
      at the simulacrum's forward Matter Cannon landed on the Dargan's forward Matter Cannon while
      the Gorith fighters were firing from port. The call therefore simply FAILS on the real ship -
      the hit rolls on the ordinary chart, for the section the shot's own bearing gives it, exactly
      like any uncalled shot. That is also the cheap answer: no new allocation machinery, and the
      real hull cannot be made a better target by being shot at through a mask.

      The declared call is not thrown away, it moves to $chameleonCalledId:
        - the mirrored allocation (D3) aims pass 2 with it, so the enemy still watches their called
          shot land where they called it, on the sheet they can see;
        - calculateHitBase() still resolves it - on the SIMULACRUM, via getCalledSystemAsAimed() -
          for the called-shot penalty, the profile override and the fire-control category. The
          shooter pays for the call they declared and the server's number matches the preview their
          own client computed off that same false sheet, which is the Stage 7 decision-5 identity.
          Skipping the penalty instead would make a called shot at a disguised ship strictly better
          than a called shot at anything else.

      Behind the per-load gate, so an ordinary game does not walk its fire orders for this at all.*/
    private static function withdrawChameleonCalledShots($gamedata)
    {
        if (!TacGamedata::$chameleonPresent) return;

        foreach ($gamedata->ships as $ship){
            foreach ($ship->getAllFireOrders($gamedata->turn) as $fire){
                if ($fire->calledid == -1) continue;
                if ($fire->targetid == -1) continue;

                $target = $gamedata->getShipById($fire->targetid);
                if ($target === null) continue;
                if (empty($target->chameleonDisguiseClass)) continue;

                $shooter = $gamedata->getShipById($fire->shooterid);
                if ($shooter === null) continue;
                //Only a shooter who still believes it aimed at the simulacrum. A team that has seen
                //through the deception is looking at the real hull and called a real system.
                if (!$target->isChameleonDisguisedFrom($shooter->team)) continue;

                //A ramming attack cannot be called (calculateHitBaseRam returns 0 for calledid !=
                //-1, and the client refuses to offer it). Withdrawing one would turn a doctored
                //illegal order into a legal ram, so rams keep whatever they arrived with and are
                //rejected downstream exactly as they are today.
                $weapon = $ship->getSystemById($fire->weaponid);
                if ($weapon instanceof Weapon && $weapon->isRammingAttack) continue;

                $fire->chameleonCalledId = $fire->calledid;
                $fire->calledid = -1;
            }
        }
    }
	
	

    /* sorts firing orders*/
    public static function compareFiringOrders($a, $b){
		/*
        if ($a->targetid !== $b->targetid){
            return $a->targetid - $b->targetid;
        }else if($a->calledid!==$b->calledid){ //called shots first!
            return $a->calledid - $b->calledid;
        }else if ($a->priority !== $b->priority){
                return $a->priority - $b->priority;
        }
		*/
		//let's sort by calledid then priority first, display may be by  target but not actual firing!
		if($a->calledid!==$b->calledid){ //called shots first!
            return $a->calledid - $b->calledid;
        }else if ($a->priority !== $b->priority){
            return $a->priority - $b->priority;
        }else if ($a->targetid !== $b->targetid){
            return $a->targetid - $b->targetid;
        }
        else {
            $val = $a->shooterid - $b->shooterid; //by shooter
            if ($val == 0) $val = $a->id - $b->id; //let's use database ID as final sorting element!
            return $val;
        }
    } //endof function compareFiringOrders

    /* Withdraw every still-unresolved fire order belonging to a fleet that has surrendered.

       Surrender is now possible in any phase, so a player can launch ballistics in Initial Orders
       and concede during Movement or Firing with shots already in the air. Those orders sit in
       tac_fireorder stamped with this turn and resolve normally at the end of the phase, so a
       fleet that has left the game could still kill ships. Nothing downstream was going to stop
       them either: fireWeapons() resolves orders from DESTROYED shooters on purpose ("ballistics
       that have been fired must still be resolved"), and the getTurnDeployed guard that lifts a
       surrendered fleet out of activation lists is not applied to the firing loops.

       DETACHED, not deleted from the DB, using the same ->rejected + detachFireOrder convention as
       the corrupt-order guard in validateFireOrders. Everything that resolves fire reads
       $ship->getAllFireOrders(), so one detach removes the order from the priority sort, from
       automateIntercept's incoming-shot list (defenders must not spend interceptors on a shot that
       can no longer land) and from every fireWeapons loop. Keeping the row means the replay and
       combat log of the final turn still show what was launched before the player left, which is
       the point of keeping a surrendered fleet visible for that turn at all.

       Their INTERCEPT orders need no handling here: interceptors are gathered by
       getUnassignedInterceptors, which already skips ships whose getTurnDeployed exceeds the
       current turn - and a surrendered slot reads as 999. Withdrawing whatever is attached is
       still the right blanket rule: a fleet that has conceded does nothing at all.

       Called from both prepareFiring and preparePreFiring, exactly like the chameleon withdrawal,
       because Pre-Firing runs its own resolution pass a phase earlier. Idempotent - a second pass
       finds the orders already detached. */
    private static function withdrawSurrenderedFireOrders($gamedata)
    {
        foreach ($gamedata->ships as $ship) {
            $slot = $gamedata->getSlotById($ship->slot);
            if ($slot === null) continue;
            if ($slot->surrendered === null) continue;
            //Surrendered on a LATER turn than the one being resolved: this is a past turn being
            //re-resolved, and the fleet was still playing at the time.
            if ($slot->surrendered > $gamedata->turn) continue;

            //getAllFireOrders builds a fresh array, so detaching inside the loop is safe.
            foreach ($ship->getAllFireOrders($gamedata->turn) as $fire) {
                $fire->rejected = true;
                self::detachFireOrder($ship, $fire);
            }
        }
    }

    /* WALKERS OF SIGMA-957 (WALKERS_OF_SIGMA_PLAN.md 2.2) - a flight caught in an Energy
       Draining Field last turn cannot shoot this turn. Same withdraw-and-detach convention as
       the surrender sweep above: the orders are rejected rather than failing the whole
       submission.
       ⚠️ IT HAS TO BE HERE, ON THE ADVANCE PATH, AND NOT IN validateFireOrders. A POST-side
       ship is reconstructed without criticals (arch_post_side_ship_reconstruction), so the same
       test there would read "no crit" for every flight in the game and silently do nothing,
       for ever. This runs from prepareFiring, where gamedata has been loaded in full.
       ⚠️ EdfFighterGrounded is a `oneturn` critical, so hasCritical() reports it exactly on the
       turn AFTER it was rolled - which is the rules' "will not be able to shoot the next turn"
       with no date arithmetic here. */
    private static function withdrawGroundedFighterFireOrders($gamedata)
    {
        foreach ($gamedata->ships as $ship) {
            if (!($ship instanceof FighterFlight)) continue;

            $sample = $ship->getSampleFighter();
            if (!$sample || empty($sample->criticals)) continue;
            if (!$sample->hasCritical("EdfFighterGrounded", $gamedata->turn)) continue;

            foreach ($ship->getAllFireOrders($gamedata->turn) as $fire) {
                //A selfIntercept marker is consent, not a shot - leave it, as every other
                //withdrawal path in this file does.
                if ($fire->type === 'selfIntercept') continue;
                $fire->rejected = true;
                self::detachFireOrder($ship, $fire);
            }
        }
    }

	/*actual firing of weapons in normal Firing Phase
	Marcin Sawicki, October 2017: at this stage, assume all necessary calculations (hit chance, target section), and only raw rolling remains!
	*/
    public static function fireWeapons($gamedata, $dbManager = null){
        $rammingOrders  = array();

        //Reactor explosions and Ramming Orders first    
        foreach ($gamedata->ships as $ship){		
            /*account for possible reactor overload!*/
            $reactorList = $ship->getSystemsByName('Reactor');
            foreach($reactorList as $reactorCurr){
                //is it overloading?...
                if( $reactorCurr->isOverloading($gamedata->turn) ){ //primed for self destruct!
                    $remaining =  $reactorCurr->getRemainingHealth();
                    //$armour =  $reactorCurr->armour; //just mark 0 armour
                    $toDo = $remaining;// + $armour;
                    
                    //try to make actual attack to show in log - use Ramming Attack system!				
                    $rammingSystem = $ship->getSystemByName("RammingAttack");
                    if($rammingSystem){ //actually exists! - it should on every ship!				
                        $newFireOrder = new FireOrder(
                            -1, "normal", $ship->id, $ship->id,
                            $rammingSystem->id, -1, $gamedata->turn, 1, 
                            100, 100, 1, 1, 0,
                            0,0,'SelfDestruct',10000
                        );
                        /*new FireOrder(
                            $id, $type, $shooterid, $targetid,
                            $weaponid, $calledid, $turn, $firingMode, $needed,
                            $rolled, $shots, $shotshit, $intercepted, $x, $y, $damageclass, $resolutionOrder
                        );*/
                        $newFireOrder->pubnotes = " self-destructs.";
                        $newFireOrder->addToDB = true;
                        $rammingSystem->fireOrders[] = $newFireOrder;
                    }
                    $damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $reactorCurr->id, $toDo, 0, 0, -1, true, false, "", "SelfDestruct");
                    $damageEntry->updated = true;
                    if($rammingSystem){ //add extra data to damage entry - so firing order can be identified!
                            $damageEntry->shooterid = $ship->id; //additional field
                            $damageEntry->weaponid = $rammingSystem->id; //additional field
                    }
                    $reactorCurr->damage[] = $damageEntry;
                }
            }

            //Now fire Ramming Orders before other weapons while we're looking through ships in this section.    
            foreach($ship->getAllFireOrders($gamedata->turn) as $fire){
                if ($fire->turn != $gamedata->turn){
                    continue;
                }

                $weapon = $ship->getSystemById($fire->weaponid);
                if (!$weapon->isRammingAttack) continue; //Only interested in Ramming Attacks!
                if (self::isHyperspaceLogOrder($fire)) continue; //not a ram - see the method

                $rammingOrders[] = $fire;
            }
         
        }    

        usort($rammingOrders, [self::class, 'compareFiringOrders']);

        foreach ($rammingOrders as $ramming){
            $ship = $gamedata->getShipById($ramming->shooterid);
            self::fire($ship, $ramming, $gamedata);
        }        

        $fireOrders  = array();  //Array for non-ramming ship fire.      
        //Now fire ship weapons.
        foreach ($gamedata->ships as $ship){	

            if ($ship instanceof FighterFlight) continue; //No fighter attacks handled here now that Ramming Attacks are handled above - DK 03.25      
            if($ship->isDestroyed()) continue; //Ship could be destroyed by ramming now.

            foreach($ship->getAllFireOrders($gamedata->turn) as $fire){
                if ($fire->type === "intercept" || $fire->type === "selfIntercept" || $fire->type === "prefiring"){
                    continue;
                }

                $weapon = $ship->getSystemById($fire->weaponid);
                if (!($weapon instanceof Weapon)) continue; //...just in case...
                if($weapon->isDestroyed($gamedata->turn) && !$weapon->ballistic) continue; //now individual weapons can be destroyed by Ramming before firing, but not ballistics.
                if ($weapon->isRammingAttack) continue; //Ramming Attacks have already been resolved.
                /*A Jump Engine order is a VORTEX DECLARATION, not a shot (JUMP_POINTS_PLAN.md).
                  Note this loop, unlike the interception passes above, deliberately does NOT skip
                  type 'ballistic' - launched ballistics are meant to reach fire(). Weapon::fire
                  would early-return on the null target, but only AFTER rolling a d100 and stamping
                  ->rolled and ->notes on a declaration that never rolls for anything. Skip it here
                  so the declaration reaches Movement exactly as the player made it. */
                if ($weapon instanceof JumpEngine) continue;

                //$fire->priority = $weapon->priority; //fire order priority already set, and may differ from basic weapon priority!
                $fireOrders[] = $fire;
            }
            
        }
        usort($fireOrders, [self::class, 'compareFiringOrders']);

        //Now fire ship weapons.
        foreach ($fireOrders as $fire){
            $ship = $gamedata->getShipById($fire->shooterid);
            //$wpn = $ship->getSystemById($fire->weaponid);
            //$p = $wpn->priority;
            // debug::log("resolve --- Ship: ".$ship->shipClass.", id: ".$fire->shooterid." wpn: ".$wpn->displayName.", priority: ".$p." versus: ".$fire->targetid);
            self::fire($ship, $fire, $gamedata);
        }

        // From here on, only fighter units are left.
	    //FIRE fighters at fighters
        $chosenfires = array();
        foreach ($gamedata->ships as $ship) {
            // Remember: ballistics that have been fired must still be
            // resolved! So don't continue on destroyed units/fighters.
            if (!($ship instanceof FighterFlight)) {
                continue;
            }

            foreach($ship->getAllFireOrders($gamedata->turn) as $fire){
                if ($fire->turn != $gamedata->turn){
                    continue;
                }

                $weapon = $ship->getSystemById($fire->weaponid);

                //ramming attacks are already allocated!
                if ($weapon->isRammingAttack) continue;
                    
                //ballistic weapons will still reach their targets, but direct fire from fighters previously destroyed will not happen
                if ( (!$weapon->ballistic) && ($ship->getFighterBySystem($weapon->id)->isDestroyed()) ) continue;
		        /* simplified above
                if (($ship->getFighterBySystem($weapon->id)->isDestroyed() || $ship->isDestroyed() )
                        && !$weapon->ballistic){
                    continue;
                }*/
		    
                //$fire->priority = $weapon->priority; priority already set!
                $chosenfires[] = $fire;
            }
        }
        usort($chosenfires, [self::class, 'compareFiringOrders']);

        foreach ($chosenfires as $fire){
            $shooter = $gamedata->getShipById($fire->shooterid);
            $target = $gamedata->getShipById($fire->targetid);
            if ( ($target == null) || ($target instanceof FighterFlight) ) {
                self::fire($shooter, $fire, $gamedata);
            }
        }

        //FIRE fighters at ships
        $chosenfires = array();
        foreach ($gamedata->ships as $ship) {
                // Remember: ballistics that have been fired must still be
                // resolved! So don't continue on destroyed units/fighters.
                if (!($ship instanceof FighterFlight)) {
                    continue;
                }

            foreach($ship->getAllFireOrders($gamedata->turn) as $fire){
                if ($fire->turn != $gamedata->turn){
                    continue;
                }

                $weapon = $ship->getSystemById($fire->weaponid);

                //ramming attacks are already allocated!
                if ($weapon->isRammingAttack) continue;

                //ballistic weapons will still reach their targets, but direct fire from fighters previously destroyed will not happen
                if ( (!$weapon->ballistic) && ($ship->getFighterBySystem($weapon->id)->isDestroyed()) ) continue;
                /*simplified above	
                $weapon = $ship->getSystemById($fire->weaponid);
                if (($ship->getFighterBySystem($weapon->id)->isDestroyed() || $ship->isDestroyed())
                    && !$weapon->ballistic) {
                    continue;
                }
                */
                //$fire->priority = $weapon->priority; //fire order priority already declared!
                $chosenfires[] = $fire;
            }
        }
        usort($chosenfires, [self::class, 'compareFiringOrders']);
        //FIRE rest of fighters
        foreach ($chosenfires as $fire){
            $shooter = $gamedata->getShipById($fire->shooterid);
            $target = $gamedata->getShipById($fire->targetid);
            if  ( ($target != null) && (!($target instanceof FighterFlight)) ) {
                self::fire($shooter, $fire, $gamedata);
            }
        }

        //Attachment outcomes are only known now, so units that failed to attach to an
        //Enormous unit ram it here - see createFailedAttachRamOrders.
        self::createFailedAttachRamOrders($gamedata, $dbManager);

        /* Check if any ships have activated jump engines, and do this after all other fire (in case
           they or their jump engine got destroyed).

           ⭐ SELECTED BY CLASS, NOT BY DISPLAY NAME (plan section 9 trap 2, fixed 2026-08-22).
           This sweep used to ask getSystemsByName('Jump Engine') plus a Shadow-Association special
           case for 'Phasing Drive', and getSystemsByNameLoc matches displayName (or hitChartName).
           Measured across all 776 jump engines in the static tree, that found 558 + 23 and MISSED
           195: Nacelle 132, Hyperdrive 50, FTL Drive 13 - all three with hitChartName null, so
           nothing rescued them. Boost-to-jump had never worked on a Trek Nacelle, a BSG FTL Drive
           or a Star Wars Hyperdrive. That was a pre-existing bug, and it is precisely those three
           families that markLegacy() now puts back on the boost path, so the two had to be fixed
           together. Any future "find the jump engines" sweep should test instanceof, never a name.

           NOT narrowed to isLegacyJump(): a boost committed on a NON-legacy engine before the
           Stage 2 deploy must still resolve (that is the whole reason Stage 2 left this code in
           place - see JumpEngine::$boostable). isOverloading() is the real gate, and a non-legacy
           engine can no longer be given a new boost, so it never fires for one by accident.

           isDestroyed() restates the filter getSystemsByName applied for free. doHyperspaceJump
           re-checks the engine's health and its host section itself, but a destroyed engine should
           not reach it at all. */
        foreach ($gamedata->ships as $ship) {

            if (!is_array($ship->systems)) continue;
            foreach($ship->systems as $jumpEngine){
                if (!($jumpEngine instanceof JumpEngine)) continue;
                if ($jumpEngine->isDestroyed()) continue;
                //is it overloading?...
                if( $jumpEngine->isOverloading($gamedata->turn) ){ //primed for entering hyperspace!
                    $jumpEngine->doHyperspaceJump($ship, $gamedata); //Actually create damage entry to destroy ship.
                }
            }
        }

    } //endof method fireWeapons


    /* AUTO-RAM ON FAILED ATTACHMENT.
     *
     * RammingAttack::beforePreFiringOrderResolution skips every unit with the "Attaches"
     * ability, because Pre-Firing runs a whole phase BEFORE the attach roll and has to
     * assume the attachment will succeed. This is the other half of that skip: by the end
     * of fireWeapons the roll has happened, so anything still unattached and sharing an
     * Enormous unit's hex rams it, exactly as any other ship would. The Pre-Firing skip
     * stays exactly as it is - that is what keeps the two paths from double-ramming.
     *
     * It has to be here rather than anywhere earlier or later. The attach outcome is only
     * written (onDamagedSystem -> $shooter->attached) inside fireWeapons, and ram orders
     * were gathered and resolved at the TOP of fireWeapons, so a ram created after the
     * boarding roll cannot join that batch. criticalPhaseEffects is too late: an order
     * created there would be persisted but never resolved, and would surface next turn.
     *
     * Consequences worth knowing:
     *  - Ram damage lands AFTER normal weapons fire rather than before, and a pod shot down
     *    earlier in the phase never rams (the isDestroyed guard makes that explicit).
     *  - Bases are speed 0, so the speed difference is 0, so the attach auto-succeeds and
     *    $attached is set - only a BLOCKED attach (section full, advanced armour, Ancient)
     *    ever rams one.
     *  - This ships more rams than before, because a unit that never declared an attach
     *    (out of marines, drifted in) now rams a co-located Enormous unit too. That is the
     *    general auto-ram rule the blanket skip was hiding.
     */
    public static function createFailedAttachRamOrders($gamedata, $dbManager = null)
    {
        foreach ($gamedata->ships as $shooter){
            if (!$shooter->hasSpecialAbility("Attaches")) continue;
            if ($shooter instanceof Terrain) continue;   //terrain collisions have their own path
            if ($shooter->isDestroyed()) continue;       //shot down earlier this phase - no ram
            if ($shooter->getTurnDeployed($gamedata) > $gamedata->turn) continue;

            $ram = self::getRammingAttackSystem($shooter);
            if (!$ram) continue;
            if ($ram->autoFireOnly) continue; //immobile units carry one for technical purposes only

            //A flight files ONE order naming its last live fighter, matching the Pre-Firing
            //skin-dance path exactly, so a failed attach and a failed skin dance produce
            //identical output.
            $calledid = -1;
            if ($shooter instanceof FighterFlight){
                foreach ($shooter->systems as $fighter){
                    if (!$fighter->isDestroyed()) $calledid = $fighter->id;
                }
            }

            $movementThisTurn = $shooter->getLastTurnMovement($gamedata->turn + 1);
            if (!$movementThisTurn) continue;

            foreach ($gamedata->getShipsInDistance($shooter, 0) as $targetID => $target){
                if ($targetID == $shooter->id) continue;              //do not ram self
                if (!$target->Enormous) continue;                     //only Enormous units auto-ram
                if ($target instanceof Terrain) continue;             //handled as a collision
                if ($target->isDestroyed()) continue;
                if ($target->getTurnDeployed($gamedata) > $gamedata->turn) continue;
                if (isset($shooter->attached[$targetID])) continue;   //attached units never ram their host
                if (isset($shooter->skinDancing[$targetID])) continue;
                if ($ram->checkAlreadyRammed($targetID)) continue;

                //don't duplicate a ram already declared this turn, manual or from Pre-Firing
                $alreadyDeclared = false;
                foreach ($ram->getFireOrders($gamedata->turn) as $existing){
                    if ($existing->targetid == $targetID) $alreadyDeclared = true;
                }
                if ($alreadyDeclared) continue;

                $fire = new FireOrder(
                    -1, "normal", $shooter->id, $targetID,
                    $ram->id, $calledid, $gamedata->turn, 1,
                    0, 0, 1, 0, 0,
                    $movementThisTurn->position->q, $movementThisTurn->position->r, 'TerrainCrash', 10000
                );
                $fire->pubnotes = "<br>COLLISION! Unit failed to attach and collided with the target!";
                $fire->addToDB = true;

                //Persist NOW so the order carries a real DB id before it resolves. The ram's
                //return damage lands on the FIRING unit and its DamageEntry captures
                //$fire->id; without a real id that is -1 and the pod's destruction never links
                //to a combat-log row. Clearing addToDB stops FireGamePhase's later
                //submitFireorders inserting a duplicate. Cast to int - insert ids come back as
                //strings (see the spawned-ship string-id trap).
                if ($dbManager) {
                    $fire->id = (int)$dbManager->submitSingleFireorder($gamedata->id, $fire);
                    $fire->addToDB = false;
                }

                //File it on the SHOOTER's own RammingAttack - Transverse Drive files its
                //collision under the jumping ship instead, which persists but is misfiled.
                $ram->fireOrders[] = $fire;

                //Resolve immediately: the ram batch at the top of fireWeapons is long gone.
                //calculateHitBase makes a TerrainCrash an automatic hit without touching
                //chosenLocation, which is what the Pre-Firing auto-ram relies on too.
                $ram->calculateHitBase($gamedata, $fire);
                self::fire($shooter, $fire, $gamedata);
            }
        }
    } //endof method createFailedAttachRamOrders


    /* The RammingAttack a unit would ram with. A flight mounts one per FIGHTER (they are
       subsystems), so take the first live fighter's - the flight files a single order. */
    private static function getRammingAttackSystem($shooter)
    {
        if ($shooter instanceof FighterFlight){
            foreach ($shooter->systems as $fighter){
                if ($fighter->isDestroyed()) continue;
                foreach ($fighter->systems as $sub){
                    if (!empty($sub->isRammingAttack)) return $sub;
                }
            }
            return null;
        }

        return $shooter->getSystemByName("RammingAttack");
    }


    private static function fire($ship, $fire, $gamedata)
    {
        if ($fire->turn != $gamedata->turn)
            return;

        if ($fire->type == "intercept" || $fire->type == "selfIntercept")
            return;

        if ($fire->rolled > 0)
            return;

        $weapon = $ship->getSystemById($fire->weaponid);
        $target = $gamedata->getShipById($fire->targetid);

        // If the target is an attached pod, weapon fires against it normally, but we also spawn a duplicate automatic hit against the host ship
        //
        // Two exclusions:
        // - doesSkipAttachedHostHit(): weapons that must never damage a ship, plus every
        //   hex-targeted weapon. An attached pod mirrors its host's movement so it is in the
        //   hex whenever the host is, which turned Plasma Web's cloud - a hex-targeted
        //   anti-fighter weapon - into a full-damage automatic hit on a capital ship.
        // - Terrain collisions: RammingAttack::beforePreFiringOrderResolution already creates
        //   a SEPARATE collision order for the host itself, so spilling the pod's order onto
        //   it as well made a host dragging a pod through an asteroid field take collision
        //   damage twice.
        $spillsToHost = $target
            && !empty($target->attached)
            && $target instanceof FighterFlight
            && !$weapon->doesSkipAttachedHostHit()
            && $fire->damageclass !== 'TerrainCollision'
            && $fire->damageclass !== 'TerrainCrash';

        if ($spillsToHost) {
            $hostShipId = key($target->attached);
            $hostShip = $gamedata->getShipById($hostShipId);
            if ($hostShip && !$hostShip->isDestroyed() && $hostShip->userid !== -5) {
                $savedAmmo = null;
                if (property_exists($weapon, 'ammunition')) $savedAmmo = $weapon->ammunition;
                
                $hostFire = new FireOrder(-1, $fire->type, $fire->shooterid, $hostShipId, $fire->weaponid, -1, $fire->turn, $fire->firingMode, 100, 1, $fire->shots, $fire->shotshit, $fire->intercepted, $fire->x, $fire->y, $fire->damageclass);
                $hostFire->needed = 100;
                $hostFire->rolled = 1;
                $hostFire->pubnotes = " Automatically on ship from shooting at an attached pod.";
                $hostFire->targetid = $hostShipId;
                $hostFire->id = -1; // New order
                $hostFire->addToDB = true;
                $hostFire->shotshit = 0;
                $hostFire->intercepted = 0;
                
                $weapon->fire($gamedata, $hostFire);
                $weapon->fireOrders[] = $hostFire;
                
                if ($savedAmmo !== null) $weapon->ammunition = $savedAmmo;
            }
        }

        $weapon->fire($gamedata, $fire);

    } //endof method fire


} //endof class Firing

?>
