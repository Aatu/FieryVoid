<?php
/**
 * CPU opponent - the legal move generator for SHIPS (TACTICAL_AI_PLAN.md section 5.4, stage T1b).
 *
 * Movement legality lives in the client (source/public/client/movement.js). The server checks a
 * submitted move only for "was the stated cost paid through thrusters that still exist"
 * (Movement::validateThrustPayment); what a turn costs, when it is allowed, how much the engine
 * has and what a thruster may carry are all decided by the page. A bot has no page, so this class
 * is a PORT of those rules onto the server's own ship objects: it plots one ship's Movement phase
 * order by order, exactly as a player clicks it, and never produces a row the page would refuse.
 *
 *   $mover = ShipMover::forShip($ship, $gamedata);   // null = not a hull this class handles
 *   $mover->options();                               // what may be ordered next
 *   $mover->apply('turnleft');                       // order it (false = refused, nothing changed)
 *   $mover->finish();                                // straight on for the hexes that are left
 *   $mover->rows();                                  // this turn's rows, as AIOrderBuilder posts them
 *
 * ⚠️ MIRROR OF movement.js. Each private method names the client function it ports. Change a
 * movement rule in the client and this must follow - tests/ai/shipMoverHarness.php replays every
 * plan through the real movement.js under node and fails on any difference, in BOTH directions
 * (a row the page would refuse, and an order the page offers that this class does not).
 *
 * WHAT IT HANDLES (stage T1b): ordinary hulls - speed changes, straight moves, turns (with extra
 * thrust to shorten the turn delay), sideslips, and paying for each through the right thrusters.
 * Thruster criticals (Half Efficiency, First Thrust Ignored), lost thrusters, engine damage and
 * boost, agile snap turns, a rolled or reversed ship, Elite / Poor Crew, a Quartermaster and
 * docked LCVs are all costed as the page costs them.
 *
 * FIGHTER FLIGHTS (stage T4) are plotted too, by the page's flight rules: one pool of thrust (the
 * flight's freethrust) and no thrusters, every manoeuvre paid in full on its own row and committed
 * at once (speed changes at their accel cost, turns at ceil(speed x turn cost) and never less than
 * 1, sideslips at 1), facing kept on the heading, and JINKING - a level at a time up to the
 * flight's jinking limit, 1 thrust a level (2 in a nebula). A jink changes no hex, so enumerate()
 * leaves it out and the caller adds levels to the plan it picks (jinkLevels()).
 *
 * GRAVITIC DRIVES (stage T5d) are plotted by the same rules with the page's exceptions: a gravitic
 * hull may turn and change speed while pivoting, rolling or out of alignment, slip while rolling, and
 * out of alignment its speed changes are paid through the thrusters the pivot has turned to the job
 * (directionRequired). Bow-first or stern-first and not pivoting or rolling, it is costed exactly as
 * any other hull. Its gravitic flights are ordinary flights here (the page's "gravitic turn", a
 * flight's turn that keeps its facing, is an order this class does not give).
 *
 * PIVOTS AND ROLLS (stage T5e) as the page gives them: the PIVOT ARROWS (pivotleft / pivotright) start
 * a pivot, or - while pivoting - stop it (the page then writes the opposite pivot, facing unchanged; a
 * non-agile hull starts and stops one a turn; an agile hull's every pivot simply turns its facing), paid
 * any + one of two rear/side thruster pairs, never auto-paid by the page; the ROLL starts one, or stops
 * one carried over from last turn (a roll started this turn cannot be stopped this turn). A pivot left
 * running goes on a step every turn - the page adds that forced step when the ship becomes active
 * (doForcedPivot), and so does this class, at construction (forcedPivot). A roll left running swaps the
 * ship's sides at every turn change. The search tries them only when asked (allowPivots / allowRolls).
 *
 * EXTENDED TURNS (stage T5f-5, EXTENDED_TURNS_PLAN.md) as the page gives them: the BEGIN (extendTurnLeft / extendTurnRight -
 * the map icon when the engine cannot pay a whole turn, else the Make Extended Turn box on a turn's panel: qualifiesExtended),
 * paid as far as it can be and a point short of the whole (D2), at least a quarter (D3), the rest owed BY ROLE in the row's
 * value; after it no turn, pivot, roll or second begin this turn (Movement::$extendedTurnBlockedTypes). The NEXT turn the
 * COMPLETION (completeExtendedTurn) is the only order until it is made - a turn row with value 'extendedTurn', what was owed
 * mapped onto the thrusters doing each job now - or, when it can no longer be paid, the page cancels it by itself at
 * activation (extendTurnCancel, forced - written here at construction, after the forced pivot); either way no speed change
 * all that turn. The search tries a begin only when asked (allowExtendedTurns); an owed completion always comes first.
 *
 * WHAT IT DOES NOT: a ship's jinking, turning into a pivot, an emergency roll, jump-out,
 * half-phasing, contraction, an Expert Graviton Controller's rotation. It never orders one. Units it
 * refuses outright are listed in unsupportedReason(): remote-controlled flights (Hunter-Killers -
 * AutomatedMovement may be driving them), bases, OSATs, mines, terrain, Mindrider drives, and a unit
 * carrying or riding another.
 *
 * ONE POLICY OF ITS OWN: by default it never overthrusts. The page lets a thruster carry up to twice
 * its rating at the risk of a critical (Criticals: one test per thruster over its rating, the excess
 * added to the roll); a bot's thrusters carry their rating and no more UNLESS allowOverthrust() says
 * how many points over it a thruster may go (stage T5b - the user's ruling of 2026-11-13: players
 * overthrust to make a turn they need, to stay on the map above all). Even then a point goes over a
 * rating only when no thruster facing the right way can take it within its own, and click() keeps
 * the page's ceiling of twice the rating. A plan says what it overthrew (overthrust()), so the
 * decider can weigh the critical it invites.
 */
class ShipMover
{
    const ACCEL = 'accel';
    const DECEL = 'decel';
    const TURN_LEFT = 'turnleft';
    const TURN_RIGHT = 'turnright';
    const SLIP_LEFT = 'slipleft';
    const SLIP_RIGHT = 'slipright';
    const MOVE = 'move';
    const JINK = 'jink'; //a flight's: one more level
    /* Stage T5e: the page's PIVOT ARROWS - start a pivot, or, while pivoting, stop it (the page then writes the opposite
       pivot, facing unchanged) - and the ROLL - start one, or stop one carried over from last turn. */
    const PIVOT_LEFT = 'pivotleft';
    const PIVOT_RIGHT = 'pivotright';
    const ROLL = 'roll';
    /* Stage T5f-5: begin an extended turn (the row's own type), and complete the one owed from last turn (a turn row with
       value 'extendedTurn' - named by this action, as a pivot arrow is). */
    const EXTEND_LEFT = 'extendTurnLeft';
    const EXTEND_RIGHT = 'extendTurnRight';
    const COMPLETE_EXTENDED = 'completeExtendedTurn';

    //Thruster directions, as Thruster::$direction and a row's requiredThrust index them.
    const DIR_ANY = 0;
    const DIR_RETRO = 1;
    const DIR_MAIN = 2;
    const DIR_PORT = 3;
    const DIR_STBD = 4;

    private $ship;
    private $turn;
    private $atInitiative;

    /* Every movement row the ship was loaded with, then the ones plotted here - plain arrays in
       the shape the page holds them (type, position, speed, heading, facing, preturn, turn,
       value, requiredThrust, assignedThrust, forced, commit). The page's rules read the whole
       list, so this does too. */
    private $rows = array();
    private $loadedCount = 0;

    //id => array('id', 'direction', 'output', 'half', 'fti', 'dead')
    private $thrusters = array();

    private $engineThrust = 0;      //the turn's thrust before any of this turn's rows are paid
    private $engineBoost = 0;       //...of which this turn's engine boost (a Hard bot's power, stage T5f-4): gone next turn
    private $adrift = false;
    private $engineDestroyed = false;
    private $agile = false;
    private $gravitic = false;      //a gravitic drive (stage T5d): turns and speed changes in any attitude (see directionRequired)
    private $turnCostRate = 0;
    private $turnDelayRate = 0;
    private $accelCost = 0;
    private $submarine = false;
    private $lcvSurcharge = 0;
    private $crewMod = 0;
    private $flight = false;       //a fighter flight: one pool of thrust, no thrusters
    private $jinkLimit = 0;
    private $jinkCost = 1;
    private $overthrust = 0;       //points a thruster may carry over its rating (allowOverthrust); 0 = never
    private $shorten = false;      //enumerate() also tries each turn with all the extra thrust it can take (allowShortening)
    private $pivots = false;       //enumerate() also tries the pivot arrows (allowPivots, stage T5e)
    private $rolls = false;        //enumerate() also tries a roll (allowRolls, stage T5e)
    private $extendedTurns = false; //enumerate() also tries beginning an extended turn (allowExtendedTurns, stage T5f-5)
    private $pivotCost = 0;
    private $rollCost = 0;
    private $carried = array();    //thruster id => thrust it carried before any row plotted here (Engine Shorted's push)

    //apply()'s extra thrust meaning "as much as can be placed" - a turn delay is never bought below 1 (click()).
    const SHORTEN_MAX = 99;

    /* Why this unit is not one ShipMover plots for, or null when it is. Such a unit keeps the
       fallback: straight on (AIFallback::movement). */
    public static function unsupportedReason($ship, $turn)
    {
        if ($ship instanceof FighterFlight && !empty($ship->remoteControl)) return 'a remote-controlled flight';
        if ($ship->isTerrain()) return 'terrain';
        if (!empty($ship->base) || !empty($ship->smallBase)) return 'a base';
        if (!empty($ship->osat)) return 'an OSAT';
        if (!empty($ship->mine)) return 'a mine';
        if (!empty($ship->mindrider)) return 'a Mindrider drive';
        if (!empty($ship->attached)) return 'riding another unit';
        if (!empty($ship->hasAttached)) return 'carrying another unit';
        if ($ship->isDestroyed()) return 'destroyed';
        if (!$ship->getLastMovement()) return 'not on the board';
        //(an extended turn owed from last turn is no reason since stage T5f-5: it is completed first thing, or cancelled)

        foreach ($ship->movement as $move) {
            if ($move->turn != $turn) continue;
            //Rows only a player (or a special system) writes: this ship has already been given orders this turn.
            if (!$move->preturn && empty($move->forced) && $move->type != 'start' && $move->type != 'deploy') return 'already has orders this turn';
        }

        return null;
    }

    //The mover for one of the bot's ships in the game as loaded, or null (see unsupportedReason).
    public static function forShip($ship, TacGamedata $gamedata)
    {
        if (self::unsupportedReason($ship, $gamedata->turn) !== null) return null;
        return new ShipMover($ship, $gamedata->turn, AIOrderBuilder::initiativeOrder($ship, $gamedata));
    }

    /* Direct construction is for the harness, which has no game: $ship is a server ship object
       with its movement rows loaded, $turn the turn being plotted. */
    public function __construct($ship, $turn, $atInitiative = 0)
    {
        $this->ship = $ship;
        $this->turn = (int)$turn;
        $this->atInitiative = $atInitiative;

        foreach ($ship->movement as $move) $this->rows[] = self::rowFromOrder($move);
        $this->loadedCount = count($this->rows);

        $this->agile = !empty($ship->agile);
        $this->gravitic = !empty($ship->gravitic);
        $this->turnCostRate = round((float)$ship->turncost * 100) / 100;            //getTurnCost
        $this->turnDelayRate = round((float)$ship->turndelaycost * 100) / 100;      //getTurnDelayCost
        $this->accelCost = (float)$ship->accelcost;
        $this->submarine = !empty($ship->submarine);
        $this->flight = ($ship instanceof FighterFlight);
        $this->lcvSurcharge = (!$this->flight && !empty($ship->LCVCarrier)) ? (int)HangarOps::dockedLCVCount($ship) : 0;
        $this->crewMod = (int)$ship->getCrewTurnDelayModifier();
        if ($this->flight) {
            $this->jinkLimit = (int)$ship->jinkinglimit;
            $this->jinkCost = TacGamedata::$nebulaPresent ? (int)Nebula::jinkCostMultiplier($ship) : 1; //movement.js jinkLevelCost
        }

        $this->pivotCost = (int)$ship->pivotcost;
        $this->rollCost = (int)$ship->rollcost;

        $this->readSystems();
        foreach ($this->thrusters as $id => $thruster) $this->carried[$id] = $this->channeledEffective($id);
        $this->forcedPivot();
        $this->forcedExtendedCancel(); //after the forced pivot (EXTENDED_TURNS_PLAN.md T19): its payability sees that facing
    }

    /* doForcedExtendedTurnCancel (stage T5f-5) - what the page does when the ship becomes active, after doForcedPivot: an
       extended turn owed from last turn that can no longer be completed is cancelled, with no pop-up (D5) - a FORCED, committed
       extendTurnCancel row whose value says why ('engineShorted', 'thrust', 'thrusters'). Not an order: actions() leaves it out. */
    private function forcedExtendedCancel()
    {
        if (!$this->isExtendedOutstanding()) return;
        $reason = $this->extendedCancelReason();
        if ($reason === null) return;
        $last = $this->rows[count($this->rows) - 1];
        $row = $this->newRow('extendTurnCancel', $last['q'], $last['r'], $last['speed'], $last['heading'], $last['facing'], array(null, null, null, null, null), $reason);
        $row['forced'] = true;
        $row['commit'] = true;
        $this->rows[] = $row;
    }

    /* Stage T5f-5: enumerate() also tries BEGINNING an extended turn (Hard - plan 5.8: "extended turns, where possible"). Off by
       default; an extended turn owed from last turn is completed whatever this says - the page offers nothing else first. */
    public function allowExtendedTurns($on = true)
    {
        $this->extendedTurns = (bool)$on;
        return $this;
    }

    /* doForcedPivot - what the page does when the ship becomes active (MovementPhaseStrategy): a pivot carried over from
       last turn goes on a step, facing turned, no thrust, a FORCED row. It is posted with the rest (the server keeps a forced
       pivot - only a forced jink is dropped) and is not an order: actions() leaves it out. */
    private function forcedPivot()
    {
        $pivoting = $this->isPivoting();
        if ($pivoting === 'no') return;
        $name = ($pivoting === 'left') ? self::PIVOT_LEFT : self::PIVOT_RIGHT;
        foreach ($this->rows as $row) {
            if ($row['turn'] == $this->turn && $row['type'] === $name && $row['forced']) return; //alreadyDone
        }
        $last = $this->rows[count($this->rows) - 1];
        $row = $this->newRow($name, $last['q'], $last['r'], $last['speed'], $last['heading'],
            Mathlib::addToHexFacing($last['facing'], ($pivoting === 'left') ? -1 : 1), array(null, null, null, null, null), 0);
        $row['forced'] = true;
        $row['commit'] = true;
        $this->rows[] = $row;
    }

    /* Stage T5e: enumerate() also tries the pivot arrows / a roll from now on. Off by default - the search is otherwise
       unchanged. ShipDecider asks for them by level (the user's ruling: rare, advanced moves). */
    public function allowPivots($on = true)
    {
        $this->pivots = (bool)$on;
        return $this;
    }

    public function allowRolls($on = true)
    {
        $this->rolls = (bool)$on;
        return $this;
    }

    /* Let a thruster carry up to $points over its rating from now on (0 = never, the default), within
       the page's own ceiling of twice the rating. Returns the mover. */
    public function allowOverthrust($points)
    {
        $this->overthrust = max(0, (int)$points);
        return $this;
    }

    /* TURN SHORTENING (user, 2026-11-19: "an important ability used by most players to increase their ability to
       manoeuvre"). From now on enumerate() also tries every turn of a SHIP with all the extra thrust it can take
       (SHORTEN_MAX - the page's own rule stops it at a delay of 1), so the search reaches the double turns only a
       shortened delay allows. Off by default: the T1b search is unchanged. A flight pays no extra for a turn. */
    public function allowShortening($on = true)
    {
        $this->shorten = (bool)$on;
        return $this;
    }

    /* What the plotted orders have put over the thrusters' ratings: thruster id => points over, for each
       thruster the orders took over its rating (or further over it - Engine Shorted's push may already
       be). Each such thruster rolls for a critical at the end of the turn, the excess added to the roll. */
    public function overthrust()
    {
        $over = array();
        foreach ($this->thrusters as $id => $thruster) {
            $now = $this->channeledEffective($id) - $thruster['output'];
            $before = max(0, $this->carried[$id] - $thruster['output']);
            if ($now > $before) $over[$id] = $now;
        }
        return $over;
    }

    /* The engine's thrust for the turn, the thrusters and what ails them, and whether the ship
       can manoeuvre at all - getRemainingEngineThrust (before the rows are taken off), isAdrift
       and isEngineDestroyed. */
    private function readSystems()
    {
        $ship = $this->ship;
        $turn = $this->turn;

        //A flight (getRemainingEngineThrust): its free thrust, nothing to destroy or set adrift.
        if ($this->flight) {
            $this->engineThrust = (int)$ship->freethrust;
            $this->engineDestroyed = false;
            $this->adrift = false;
            return;
        }
        $thrust = 0;
        $engines = 0;
        $enginesAlive = 0;
        $disabled = false;

        foreach ($ship->systems as $system) {
            if ($system->hasCritical('ShipDisabledOneTurn', $turn) || $system->hasCritical('ShipDisabled', $turn)) $disabled = true;

            if ($system instanceof Thruster) {
                $dead = $system->isDestroyed();
                $this->thrusters[(int)$system->id] = array(
                    'id' => (int)$system->id,
                    'direction' => (int)$system->direction,
                    'output' => $dead ? 0 : (int)$system->getOutput(),
                    'half' => (int)$system->hasCritical('HalfEfficiency', $turn),
                    'fti' => $system->hasCritical('FirstThrustIgnored', $turn) > 0,
                    'dead' => $dead
                );
            }

            if ($system instanceof Engine) $engines++;
            if ($system->isDestroyed()) continue;

            if ($system instanceof Engine) {
                $enginesAlive++;
                if (!$system->isOfflineOnTurn($turn)) {
                    //systems.js getOutput: rating + criticals + boost, less a one-turn drain
                    $own = (int)$system->getOutput();
                    /* A Trek impulse drive's and a Shadow BioDrive's getOutput() is exactly the figure the page is SENT (its rating
                       plus its nacelles' / its bio-thrusters' sum) - the page then adds the drive's OWN criticals and takes the
                       one-turn drain off itself (Engine::stripForJson publishes it). Without this a damaged drive read high. */
                    if ($system instanceof TrekImpulseDrive || $system instanceof BioDrive) $own += (int)$system->outputMod - (int)$system->sumCriticalParam('EdfThrustDrain');
                    if ($own > 0 || self::boostOf($system, $turn) > 0) {
                        $thrust += max(0, $own + self::boostOf($system, $turn));
                        $this->engineBoost += max(0, min(self::boostOf($system, $turn), $own + self::boostOf($system, $turn)));
                    }
                }
            }

            $thrust -= (int)$system->hasCritical('swtargetheld', $turn); //held by a tractor beam

            //A weapon boosted with thrust takes it from the engine.
            if (!empty($system->thrustBoosted) && !$system->isOfflineOnTurn($turn)) {
                $boost = self::boostOf($system, $turn);
                if ($boost > 0) $thrust -= $boost * (int)self::readProtected($system, 'thrustPerBoost');
            }
        }

        $this->engineThrust = $thrust;
        $this->engineDestroyed = $engines > 0 ? ($enginesAlive == 0) : true;

        $cnc = $ship->getSystemByName('CnC');
        $this->adrift = $disabled || ($cnc && $cnc->isDestroyed());
    }

    private static function boostOf($system, $turn)
    {
        $boost = 0;
        foreach ($system->power as $power) {
            if ($power->turn == $turn && $power->type == 2) $boost += (int)$power->amount;
        }
        return $boost;
    }

    private static function readProtected($object, $property)
    {
        if (!property_exists($object, $property)) return 0;
        $reader = function () use ($property) { return $this->$property; };
        return $reader->call($object);
    }

    private static function rowFromOrder($move)
    {
        $position = Movement::toOffset($move->position);
        return array(
            'type' => $move->type,
            'q' => $position ? $position->q : 0,
            'r' => $position ? $position->r : 0,
            'speed' => (int)$move->speed,
            'heading' => (int)$move->heading,
            'facing' => (int)$move->facing,
            'preturn' => (bool)$move->preturn,
            'turn' => (int)$move->turn,
            'value' => $move->value,
            'requiredThrust' => is_array($move->requiredThrust) ? $move->requiredThrust : array(),
            'assignedThrust' => is_array($move->assignedThrust) ? $move->assignedThrust : array(),
            'forced' => !empty($move->forced),
            'commit' => true
        );
    }


    // =============================================================== what the caller asks

    //Every order that may be given next, and that this class can pay for within its own policy.
    public function options()
    {
        $options = array();
        foreach (array(self::ACCEL, self::DECEL, self::TURN_LEFT, self::TURN_RIGHT, self::SLIP_LEFT, self::SLIP_RIGHT, self::MOVE, self::JINK,
            self::PIVOT_LEFT, self::PIVOT_RIGHT, self::ROLL, self::EXTEND_LEFT, self::EXTEND_RIGHT, self::COMPLETE_EXTENDED) as $action) {
            if ($this->can($action)) $options[] = $action;
        }
        return $options;
    }

    /* options() less the orders a search would throw away untried - so it never asks for them (stage T5f-5: each extended-turn
       begin it asked for was a whole payment worked out on every mover of every search, at every level). The same filter the
       searches applied after asking: no jink; pivots, rolls and begins only when allowed - and none of them in an aimed search. */
    private function searchOptions($aimed = false)
    {
        $options = array();
        foreach (array(self::ACCEL, self::DECEL, self::TURN_LEFT, self::TURN_RIGHT, self::SLIP_LEFT, self::SLIP_RIGHT, self::MOVE,
            self::PIVOT_LEFT, self::PIVOT_RIGHT, self::ROLL, self::EXTEND_LEFT, self::EXTEND_RIGHT, self::COMPLETE_EXTENDED) as $action) {
            if (($action === self::PIVOT_LEFT || $action === self::PIVOT_RIGHT) && ($aimed || !$this->pivots)) continue;
            if ($action === self::ROLL && ($aimed || !$this->rolls)) continue;
            if (($action === self::EXTEND_LEFT || $action === self::EXTEND_RIGHT) && ($aimed || !$this->extendedTurns)) continue;
            if ($this->can($action)) $options[] = $action;
        }
        return $options;
    }

    public function isFlight()
    {
        return $this->flight;
    }

    //A flight's jinking levels this turn (getJinking), and how many more it may add (canJink's limit) and pay for.
    public function jinking()
    {
        $levels = 0;
        foreach ($this->rows as $row) {
            if ($row['turn'] == $this->turn && $row['type'] === 'jink') $levels += (int)$row['value'];
        }
        return $levels;
    }

    public function jinkRoom()
    {
        if (!$this->flight || $this->jinkCost <= 0) return 0;
        return max(0, min($this->jinkLimit - $this->jinking(), intdiv(max(0, $this->remainingEngineThrust()), $this->jinkCost)));
    }

    //Add $levels jinking levels (as many as can be had). Returns how many were added.
    public function jinkLevels($levels)
    {
        $added = 0;
        while ($added < $levels && $this->jink()) $added++;
        return $added;
    }

    //Can this order be given now, and paid for?
    public function can($action)
    {
        $trial = clone $this;
        return $trial->apply($action);
    }

    /* What the PAGE would offer at this point, whatever this class can pay for: the answers of
       canChangeSpeed (one answer, it does not look at the direction), canTurn, canSlip and
       canMove. The harness compares these with the real client. */
    public function clientOffers()
    {
        $speed = $this->canChangeSpeed();
        $offers = array(
            self::ACCEL => $speed,
            self::DECEL => $speed,
            self::TURN_LEFT => $this->canTurn(),
            self::TURN_RIGHT => $this->canTurn(),
            self::SLIP_LEFT => $this->canSlip(false),
            self::SLIP_RIGHT => $this->canSlip(true),
            self::MOVE => $this->canMove()
        );
        if ($this->flight) $offers[self::JINK] = $this->canJink(); //a ship's jinking is not plotted, so not compared
        if (!$this->flight) { //stage T5e: a flight's pivots are the Fire phase's (combat pivots) - not plotted, not compared
            $offers[self::PIVOT_LEFT] = $this->canPivot(false);
            $offers[self::PIVOT_RIGHT] = $this->canPivot(true);
            $offers[self::ROLL] = $this->canRoll();
            //stage T5f-5: qualifiesForExtendedTurn (the map icon, or the box on a turn's panel) and canCompleteExtendedTurn
            $offers[self::EXTEND_LEFT] = $this->qualifiesExtended(false);
            $offers[self::EXTEND_RIGHT] = $this->qualifiesExtended(true);
            $offers[self::COMPLETE_EXTENDED] = $this->canCompleteExtended();
        }
        return $offers;
    }

    /* Give one order. $extraThrust (turns only): thrust paid over the turn's cost to shorten its
       turn delay, one hex per point, never below a delay of 1; a point that cannot be placed is
       left out. Returns false, with nothing changed, when the order is not legal or not payable. */
    public function apply($action, $extraThrust = 0)
    {
        switch ($action) {
            case self::ACCEL: return $this->changeSpeed(true);
            case self::DECEL: return $this->changeSpeed(false);
            case self::TURN_LEFT: return $this->turn(false, (int)$extraThrust);
            case self::TURN_RIGHT: return $this->turn(true, (int)$extraThrust);
            case self::SLIP_LEFT: return $this->slip(false);
            case self::SLIP_RIGHT: return $this->slip(true);
            case self::MOVE: return $this->move();
            case self::JINK: return $this->jink();
            case self::PIVOT_LEFT: return $this->pivot(false);
            case self::PIVOT_RIGHT: return $this->pivot(true);
            case self::ROLL: return $this->roll();
            case self::EXTEND_LEFT: return $this->beginExtended(false);
            case self::EXTEND_RIGHT: return $this->beginExtended(true);
            case self::COMPLETE_EXTENDED: return $this->completeExtended((int)$extraThrust);
        }
        return false;
    }

    //Has the ship used all its movement? Only then may the turn's rows be submitted.
    public function isComplete()
    {
        return $this->remainingMovement() == 0;
    }

    //Straight on for every hex that is left.
    public function finish()
    {
        $guard = 0;
        while (!$this->isComplete() && $guard++ < 200) {
            if (!$this->move()) return false;
        }
        return $this->isComplete();
    }

    public function hexesLeft()
    {
        return max(0, $this->remainingMovement());
    }

    public function thrustLeft()
    {
        return $this->remainingEngineThrust();
    }

    /* The engine's thrust for a turn as next turn should have it: this turn's, without this turn's engine boost (stage T5f-5 - what an
       extended turn begun now must leave owed at most, or the page cancels it next turn for want of thrust). A judgement aid. */
    public function thrustNextTurn()
    {
        return max(0, $this->engineThrust - $this->engineBoost);
    }

    //How many more hexes the ship must travel before it may turn again (0 = it may turn now).
    public function turnDelay()
    {
        return $this->currentTurnDelay();
    }

    /* The fastest speed at which the ship could still pay for one turn out of a whole turn's thrust
       (canTurn's price: ceil(speed x turn cost), never under 1, plus the docked-LCV surcharge) - the
       speed beyond which it can only fly straight, brake or slip. A judgement aid for the deciders
       (stage T5: a ship that ends faster than this cannot come about at a map edge next turn), not a
       rule: the thrusters' own ratings are not asked. */
    public function fastestTurnable($boosted = true)
    {
        return $this->safeSpeed(1.0, $boosted);
    }

    /* The fastest speed at which one turn (canTurn's price, docked LCVs included) costs no more than $share of the
       ship's thrust for a turn - the user's "safe to manoeuvre" (2026-11-19: an Omega, thrust 8, at speed 7-8 is "a bit
       too fast"; at $share 0.75 its safe speed is 6). A judgement aid like fastestTurnable (= $share 1), not a rule.
       $boosted false: on the thrust without this turn's engine boost (stage T5f-4) - a speed built up on a boost has to be
       handled next turn without it. */
    public function safeSpeed($share, $boosted = true)
    {
        $budget = max(0, $this->engineThrust - ($boosted ? 0 : $this->engineBoost)) * $share;
        for ($speed = 30; $speed > 0; $speed--) {
            if (max(1, (int)ceil($speed * $this->turnCostRate)) + $this->lcvSurcharge <= $budget + 1e-9) return $speed;
        }
        return 0;
    }

    /* Where the plotted orders leave the ship - and (stage T5e) whether it is still pivoting ('no', 'left', 'right') or
       rolling at the end of its move: a pivot or roll left running goes on next turn by itself, so it is part of the end.
       Stage T5f-5: 'extending' - an extended turn begun this turn ('no', 'left', 'right'), completed first thing next turn. */
    public function state()
    {
        $last = $this->rows[count($this->rows) - 1];
        $begin = $this->extendedStart();
        return array(
            'position' => new OffsetCoordinate($last['q'], $last['r']),
            'heading' => $last['heading'],
            'facing' => $last['facing'],
            'speed' => $last['speed'],
            'pivoting' => $this->isPivoting(),
            'rolling' => $this->isRolling(),
            'extending' => ($begin === null) ? 'no' : (($begin['type'] === self::EXTEND_RIGHT) ? 'right' : 'left')
        );
    }

    //The hexes entered by the plotted orders, in order (the starting hex is not listed).
    public function path()
    {
        $hexes = array();
        for ($i = $this->loadedCount; $i < count($this->rows); $i++) {
            $type = $this->rows[$i]['type'];
            if ($type === 'move' || $type === 'slipleft' || $type === 'slipright') $hexes[] = new OffsetCoordinate($this->rows[$i]['q'], $this->rows[$i]['r']);
        }
        return $hexes;
    }

    /* The orders given so far, in the words apply() takes. The page's forced pivot is not an order (forcedPivot) - and a
       pivot ARROW is named by the arrow clicked, which is the opposite of the row's type when it stopped a pivot. */
    public function actions()
    {
        $actions = array();
        for ($i = $this->loadedCount; $i < count($this->rows); $i++) {
            $row = $this->rows[$i];
            if ($row['forced']) continue;
            if ($row['type'] === 'speedchange') $actions[] = $row['accel'] ? self::ACCEL : self::DECEL;
            else if (isset($row['arrow'])) $actions[] = $row['arrow'];
            else $actions[] = $row['type'];
        }
        return $actions;
    }

    /* The extra thrust each of those orders was given - apply()'s second argument, as the points actually placed: a
       turn's shortening, 0 for anything else. apply($actions[$i], $extras[$i]) in turn plots the same rows again. */
    public function extras()
    {
        $extras = array();
        for ($i = $this->loadedCount; $i < count($this->rows); $i++) {
            if ($this->rows[$i]['forced']) continue; //not an order (actions())
            $extras[] = isset($this->rows[$i]['extra']) ? (int)$this->rows[$i]['extra'] : 0;
        }
        return $extras;
    }

    /* The plan again with its LAST turn shortened by whatever thrust is spare within the thrusters' ratings (the user's
       ruling, 2026-11-19: "shortening turn delay is always useful (since any delay carries over to next turn as well) if
       you have the spare thrust on that turn, and you won't exceed thruster ratings"). All the thrust the turn can take
       first; when an order after it (a sideslip) can then no longer be paid, a point less, and so on - the spare thrust
       goes into the delay and nothing the plan needs is taken from it. Asked of the mover the plan was found from.
       Returns the mover with the plan plotted that way, or null when there is nothing to gain (no turn, no delay left at
       the end, no thrust to spare). The end state is the plan's own. */
    public function shortened(array $plan)
    {
        if ($this->flight || empty($plan['actions']) || (int)$plan['turnDelay'] <= 0) return null;
        $last = null;
        foreach ($plan['actions'] as $i => $action) {
            //(an owed extended turn's completion is a turn too, and may be shortened - EXTENDED_TURNS_PLAN.md R5)
            if ($action === self::TURN_LEFT || $action === self::TURN_RIGHT || $action === self::COMPLETE_EXTENDED) $last = $i;
        }
        if ($last === null) return null;

        $extras = isset($plan['extras']) ? $plan['extras'] : array_fill(0, count($plan['actions']), 0);
        $already = (int)$extras[$last];
        for ($extra = self::SHORTEN_MAX; $extra > $already; ) {
            $try = $extras;
            $try[$last] = $extra;
            $mover = clone $this;
            $mover->overthrust = 0;
            $placed = null;
            $paid = true;
            foreach ($plan['actions'] as $i => $action) {
                if (!$mover->apply($action, $try[$i])) {
                    $paid = false;
                    break;
                }
                if ($i == $last) $placed = $mover->lastExtra();
            }
            if ($paid) {
                if (!$mover->isComplete() || $mover->turnDelay() >= (int)$plan['turnDelay']) return null;
                $state = $mover->state();
                if (!$state['position']->equals($plan['position']) || $state['heading'] != $plan['heading'] || $state['facing'] != $plan['facing'] || $state['speed'] != $plan['speed']) return null;
                return $mover;
            }
            if ($placed === null) return null; //an order before the turn failed: cannot happen, the plan was found this way
            $extra = min($extra, $placed) - 1;
        }
        return null;
    }

    /* For the harness: the plotted rows as this class holds them, each paid row with the thrusters
       it was paid through in the order the points were placed ('clicks'). */
    public function plotted()
    {
        return array_slice($this->rows, $this->loadedCount);
    }

    /* This turn's rows as a browser posts them (AIOrderBuilder::move), thrust included. A paid
       row's assignedThrust is a list indexed by thruster id with nulls between, which is what a
       JavaScript array keyed by id becomes on the way to the server. */
    public function rows()
    {
        $out = array();
        for ($i = $this->loadedCount; $i < count($this->rows); $i++) {
            $row = $this->rows[$i];
            $entry = AIOrderBuilder::move($row['type'], new OffsetCoordinate($row['q'], $row['r']), $row['speed'], $row['heading'], $row['facing'],
                $this->turn, $this->atInitiative, $row['value']);
            $entry['requiredThrust'] = $row['requiredThrust'];
            $entry['forced'] = !empty($row['forced']); //the page's forced pivot (forcedPivot) is posted as the page posts it

            $assigned = array();
            if (count($row['assignedThrust']) > 0) {
                $top = max(array_keys($row['assignedThrust']));
                for ($id = 0; $id <= $top; $id++) $assigned[] = isset($row['assignedThrust'][$id]) ? (int)$row['assignedThrust'][$id] : null;
            }
            $entry['assignedThrust'] = $assigned;
            $out[] = $entry;
        }
        return $out;
    }

    /* Every distinct place the ship can end this turn, each with one way of getting there: the
       candidates MoveDecider scores (stage T5). A plan is array('actions', 'extras' - see extras(),
       'rows', 'path', 'position', 'heading', 'facing', 'speed', 'thrustLeft', 'turnDelay' - left at
       the end, 'overthrust' - see overthrust(); always empty unless allowOverthrust() was given).

       Bounded: at most $limit plans, PLAINEST FIRST. "Straight on" is always the first; then every
       plan with one manoeuvre (a turn, a slip or a speed change, at each point of the path), then
       two, and so on - and among movers with the same number of manoeuvres, the one nearest the
       end of its move, so plans are finished rather than widened. (Working on the fewest hexes
       left alone filled the quota with slowed-down ships before a single turn at cruising speed
       was tried.) With allowShortening(), each turn is tried a second time with all the extra
       thrust it can take (stage T5c): a plan records it ('extras'). Two plans that end alike
       keep the one with less turn delay left among the plainest.

       $deadline (stage T5f-8): an AIConfig::cpuSeconds() time the search stops at, with what it has
       found - the plainest plans, straight on always among them - and $searchCut set. */
    public static function plans($ship, TacGamedata $gamedata, $limit = 400)
    {
        $start = self::forShip($ship, $gamedata);
        return $start === null ? array() : $start->enumerate($limit);
    }

    //Set by the last enumerate(): its $deadline stopped it before the search was done.
    public $searchCut = false;

    public function enumerate($limit = 400, $deadline = null)
    {
        $plans = array();
        $seen = array();
        $expanded = 0;
        $serial = 0;
        $this->searchCut = false;

        $straight = clone $this;
        if ($straight->finish()) self::keepPlan($plans, $straight);

        $queue = new SplPriorityQueue();
        $queue->insert($this, array(0, -$this->hexesLeft(), 0));

        while (!$queue->isEmpty() && count($plans) < $limit && $expanded < $limit * 12) {
            if ($deadline !== null && ($expanded % 16) == 0 && AIConfig::cpuSeconds() > $deadline) {
                $this->searchCut = true;
                break;
            }
            $mover = $queue->extract();
            $expanded++;

            //A turn may still be ordered once the last hex is entered, so a complete mover is a plan AND is expanded.
            if ($mover->isComplete()) self::keepPlan($plans, $mover);

            //(no jink - no hex changes with it: the caller adds levels to the plan it picks; pivots, rolls, begins only when allowed)
            foreach ($mover->searchOptions() as $action) {
                $shortenable = $mover->shorten && !$mover->flight && ($action === self::TURN_LEFT || $action === self::TURN_RIGHT || $action === self::COMPLETE_EXTENDED);
                foreach ($shortenable ? array(0, self::SHORTEN_MAX) : array(0) as $extra) {
                    $next = clone $mover;
                    if (!$next->apply($action, $extra)) continue;
                    if ($extra > 0 && $next->lastExtra() == 0) continue; //no point could be placed: the plain turn is this one

                    $key = $next->searchKey();
                    if (isset($seen[$key])) continue;
                    $seen[$key] = true;
                    $queue->insert($next, array(-$next->manoeuvres(), -$next->hexesLeft(), -(++$serial)));
                }
            }
        }

        return array_values($plans);
    }

    /* Stage T5f-2 - plans that END on chosen hexes (the user's note on game 4774: fighters should "find hexes where there is
       little or no light weapon coverage from the enemy and try to position themselves there"). enumerate() finds the plainest
       plans first, which for a fast flight is a cone: 133 of the ~550 hexes a speed-9 Aurora can end in. This is the same
       search AIMED - best first by how well the hexes left fit the nearest goal (none wasted, none short), then by how far the
       heading is off the goal, then by manoeuvres. Every step is still apply()'s, so every plan is one the page accepts
       (tests/ai/shipMoverHarness.php replays them through movement.js). No jinking, pivot or roll is tried. $goals: "q,r" =>
       the facing the plan must end with, or true for any (a flight's guns bear where it points, and a slip or a speed change
       is cheaper than a turn, so left to itself the search keeps the heading it starts with). Returns up to $limit plans
       ending on a goal - one per end state, as enumerate()'s - from at most $limit x GOAL_EXPANSIONS movers looked at. */
    const GOAL_EXPANSIONS = 40;

    public function enumerateToward(array $goals, $limit = 4)
    {
        $plans = array();
        $targets = array();
        foreach ($goals as $key => $facing) {
            $parts = explode(',', (string)$key);
            if (count($parts) == 2) $targets[] = array(new OffsetCoordinate((int)$parts[0], (int)$parts[1]), ($facing === true) ? null : (int)$facing);
        }
        if (count($targets) == 0 || $limit <= 0) return $plans;

        $seen = array();
        $expanded = 0;
        $serial = 0;
        $queue = new SplPriorityQueue();
        $queue->insert($this, array(-$this->goalGap($targets), 0, 0));

        while (!$queue->isEmpty() && count($plans) < $limit && $expanded < $limit * self::GOAL_EXPANSIONS) {
            $mover = $queue->extract();
            $expanded++;

            if ($mover->isComplete()) {
                $state = $mover->state();
                $key = $state['position']->q . ',' . $state['position']->r;
                if (isset($goals[$key]) && ($goals[$key] === true || (int)$goals[$key] === (int)$state['facing'])) self::keepPlan($plans, $mover);
            }

            //(no jink, pivot, roll or begin in an aimed search; an owed completion is still made - nothing else is offered first)
            foreach ($mover->searchOptions(true) as $action) {
                $next = clone $mover;
                if (!$next->apply($action)) continue;
                $key = $next->searchKey();
                if (isset($seen[$key])) continue;
                $seen[$key] = true;
                $gap = $next->goalGap($targets);
                if ($gap === null) continue; //no goal within what the hexes left, and the speed the thrust could add, can cover
                $queue->insert($next, array(-$gap, -$next->manoeuvres(), -(++$serial)));
            }
        }

        return array_values($plans);
    }

    /* How badly a mover fits the nearest of $targets (array(hex, facing or null)), for enumerateToward(): 10 for each hex it
       would have to waste (its hexes left beyond the distance) or add (short of it - only as far as its thrust could buy the
       speed), 1 for each 60 degrees its heading is off the goal, and 1 for each 60 degrees between its heading and the facing
       the goal asks for. Null when no goal is within reach. */
    private function goalGap(array $targets)
    {
        $last = $this->rows[count($this->rows) - 1];
        $here = new OffsetCoordinate($last['q'], $last['r']);
        $left = $this->remainingMovement();
        $more = ($this->accelCost > 0) ? (int)floor(max(0, $this->remainingEngineThrust()) / $this->accelCost) : 0;
        $from = Mathlib::hexCoToPixel($here);

        $best = null;
        foreach ($targets as $target) {
            list($hex, $facing) = $target;
            $distance = $here->distanceTo($hex);
            if ($distance > $left + $more) continue;
            $fit = 10 * abs($left - $distance);
            if ($distance > 0) {
                $bearing = Mathlib::addToDirection(Mathlib::getCompassHeadingOfPoint($from, Mathlib::hexCoToPixel($hex)), -60 * (int)$last['heading']);
                $fit += (int)round(min($bearing, 360 - $bearing) / 60);
            }
            if ($facing !== null) {
                $steps = (($facing - (int)$last['heading']) % 6 + 6) % 6;
                $fit += min($steps, 6 - $steps);
            }
            if ($best === null || $fit < $best) $best = $fit;
        }
        return $best;
    }

    //Orders plotted so far that are not a plain step forward (the page's forced pivot is no order).
    private function manoeuvres()
    {
        $count = 0;
        for ($i = $this->loadedCount; $i < count($this->rows); $i++) {
            if ($this->rows[$i]['type'] !== 'move' && !$this->rows[$i]['forced']) $count++;
        }
        return $count;
    }

    /* One plan per end state: the first found, which is the one with fewest orders among those tried - or, among
       as few orders, the one that leaves less turn delay to start next turn with. */
    private static function keepPlan(array &$plans, ShipMover $mover)
    {
        $state = $mover->state();
        $key = $state['position']->q . ',' . $state['position']->r . ',' . $state['heading'] . ',' . $state['facing'] . ',' . $state['speed']
            . ',' . $state['pivoting'] . ',' . (int)$state['rolling'] . ',' . $state['extending'];
        $actions = $mover->actions();
        if (isset($plans[$key])) {
            $kept = $plans[$key];
            if (count($kept['actions']) != count($actions) || $kept['turnDelay'] <= $mover->turnDelay()) return;
        }

        $plans[$key] = $mover->plan();
    }

    //The plotted orders as a plan, in the shape enumerate() returns them (a complete mover's).
    public function plan()
    {
        return array_merge($this->state(), array(
            'actions' => $this->actions(), 'extras' => $this->extras(), 'rows' => $this->rows(), 'path' => $this->path(),
            'thrustLeft' => $this->thrustLeft(), 'turnDelay' => $this->turnDelay(), 'overthrust' => $this->overthrust()
        ));
    }

    /* Could the ship pay for a turn to that side NEXT turn, at $speed, from the attitude the plotted orders leave it in -
       its thrusters fresh, within their ratings, the engine's thrust as this turn's (stage T5e: the side thrusters a roll
       swaps). Next turn the ship is rolled if it is now and is not rolling at the end of this turn, or the other way round;
       a roll left running must be stopped first, which takes its cost. A judgement aid, not a rule. */
    public function turnPayableNextTurn($right, $speed)
    {
        if ($this->flight) return true;
        $rolled = ($this->isRolled() !== $this->isRolling());
        $last = $this->lastCommitted();
        $backwards = !($last['facing'] == $last['heading'] || Mathlib::addToHexFacing($last['facing'], 1) == $last['heading']
            || Mathlib::addToHexFacing($last['facing'], -1) == $last['heading']);

        $cost = max(1, (int)ceil($speed * $this->turnCostRate)) + $this->lcvSurcharge;
        $stop = $this->isRolling() ? $this->rollCost : 0;
        if ($this->engineThrust < $cost + $stop) return false;
        if ($speed == 0) return true; //one point, through any thruster

        $side = $right ? self::DIR_PORT : self::DIR_STBD; //a turn to starboard is pushed by the port thrusters
        if ($rolled) $side = ($side == self::DIR_PORT) ? self::DIR_STBD : self::DIR_PORT;
        $rear = self::DIR_MAIN;
        if ($backwards) {
            $side = ($side == self::DIR_PORT) ? self::DIR_STBD : self::DIR_PORT;
            $rear = self::DIR_RETRO;
        }
        $capacity = array(0, 0, 0, 0, 0);
        foreach ($this->thrusters as $thruster) {
            if (!$thruster['dead']) $capacity[$thruster['direction']] += $thruster['output'];
        }
        return $capacity[$side] >= intdiv($cost, 2) && $capacity[$rear] >= intdiv($cost, 2);
    }

    //The extra thrust the last order was given (see extras()).
    private function lastExtra()
    {
        $row = $this->rows[count($this->rows) - 1];
        return isset($row['extra']) ? (int)$row['extra'] : 0;
    }

    //Two movers with the same key have the same future.
    private function searchKey()
    {
        $last = $this->rows[count($this->rows) - 1];
        $slip = '';
        $speedChanges = 0;
        for ($i = $this->loadedCount; $i < count($this->rows); $i++) {
            $type = $this->rows[$i]['type'];
            if ($type === 'slipleft' || $type === 'slipright') $slip = $type . ($i == count($this->rows) - 1 ? '!' : '');
            if ($type === 'speedchange') $speedChanges++;
        }
        $channeled = array();
        foreach ($this->thrusters as $id => $thruster) $channeled[] = $this->channeledReal($id, false);

        //(the thrust left too: a flight has no thrusters to tell two movers' spending apart; and - stage T5e - the pivots and rolls made or running)
        $pivoted = $this->hasPivoted();
        $begin = $this->extendedStart(); //stage T5f-5: a begin made, an owed turn still outstanding
        return implode('|', array($last['q'], $last['r'], $last['heading'], $last['facing'], $last['speed'], $this->remainingMovement(),
            $this->currentTurnDelay(), $slip, $speedChanges, $this->isLastRowTurn() ? 't' : '', implode(',', $channeled), $this->remainingEngineThrust(),
            $this->isPivoting(), (int)$pivoted['left'] . (int)$pivoted['right'], (int)$this->isRolling(), (int)$this->hasRolled(), (int)$this->isRolled(),
            $begin === null ? '' : $begin['type'], (int)$this->isExtendedOutstanding()));
    }

    private function isLastRowTurn()
    {
        return self::isTurn($this->rows[count($this->rows) - 1]);
    }


    // =============================================================== the orders (do*)

    //changeSpeed
    private function changeSpeed($accel)
    {
        if (!$this->canChangeSpeed()) return false;
        if ($this->speedClickWouldUndo($accel)) return false; //the page would take the last row back instead of adding one

        $last = $this->lastCommitted();
        $heading = $last['heading'];
        $speed = $last['speed'] + ($accel ? 1 : -1);
        $value = $accel ? 1 : 0;
        if ($speed < 0) { //through a standstill: the ship now backs away
            $heading = Mathlib::addToHexFacing($heading, 3);
            $speed = -$speed;
            $value = 1;
        }

        $required = array(null, null, null, null, null);
        //(the page asks with its "accel" argument true for BOTH arrows - only a pivot asks with it false)
        $required[$this->flight ? self::DIR_ANY : $this->directionRequired($accel ? self::DIR_MAIN : self::DIR_RETRO, true, false)] = $this->speedChangeCost();

        $this->rows[] = $this->newRow('speedchange', $last['q'], $last['r'], $speed, $heading, $last['facing'], $required, $value) + array('accel' => $accel);
        return $this->payLastRow(0);
    }

    //doTurn / doNormalTurn
    private function turn($right, $extraThrust)
    {
        if (!$this->canTurn()) return false;

        $required = $this->turnRequirement($right);
        $last = $this->rows[count($this->rows) - 1];
        $step = $right ? 1 : -1;
        $heading = Mathlib::addToHexFacing($last['heading'], $step);

        //a flight's facing follows its heading (doNormalTurn); a flight pays no extra to shorten a turn's delay
        $this->rows[] = $this->newRow($right ? 'turnright' : 'turnleft', $last['q'], $last['r'], $last['speed'],
            $heading, $this->flight ? $heading : Mathlib::addToHexFacing($last['facing'], $step), $required, 0);
        return $this->payLastRow($this->flight ? 0 : $extraThrust);
    }

    //doSlip
    private function slip($right)
    {
        if (!$this->canSlip($right)) return false;

        $last = $this->rows[count($this->rows) - 1];
        $towards = Mathlib::addToHexFacing($last['heading'], $right ? 1 : -1);
        $position = (new OffsetCoordinate($last['q'], $last['r']))->moveToDirection($towards);

        $required = array(null, null, null, null, null);
        //slipping to starboard is pushed by the port thrusters; a flight's slip is 1 thrust from its pool
        if ($this->flight) $required[self::DIR_ANY] = 1;
        else $required[$this->directionRequired($right ? self::DIR_PORT : self::DIR_STBD, false, true)] = (int)ceil($this->speed() / 5);

        $this->rows[] = $this->newRow($right ? 'slipright' : 'slipleft', $position->q, $position->r, $last['speed'], $last['heading'], $last['facing'], $required, 0);
        return $this->payLastRow(0);
    }

    //doMove
    private function move()
    {
        if (!$this->canMove()) return false;

        $last = $this->rows[count($this->rows) - 1];
        $position = (new OffsetCoordinate($last['q'], $last['r']))->moveToDirection($last['heading']);

        $row = $this->newRow('move', $position->q, $position->r, $last['speed'], $last['heading'], $last['facing'], array(null, null, null, null, null), 0);
        $row['commit'] = true;
        $this->rows[] = $row;
        return true;
    }

    //doJink, a flight adding a level: its whole price on the row, committed at once
    private function jink()
    {
        if (!$this->canJink()) return false;

        $last = $this->lastCommitted();
        $row = $this->newRow('jink', $last['q'], $last['r'], $last['speed'], $last['heading'], $last['facing'], array(self::DIR_ANY => $this->jinkCost), 1);
        $row['assignedThrust'] = array(self::DIR_ANY => $this->jinkCost);
        $row['commit'] = true;
        $this->rows[] = $row;
        return true;
    }

    /* doPivot - a pivot ARROW (stage T5e). Not pivoting: a pivot starts that way, the facing turns a step at once and goes on
       turning a step every turn (the page's forced pivot at activation) until stopped. Pivoting: the arrow on that side STOPS it -
       the row is then the opposite pivot and the facing stays. An agile hull's every pivot just turns the facing (isPivoting is
       always "no" for it). Paid any + one rear + one side thruster pair (payPivot); never auto-paid by the page. */
    private function pivot($right)
    {
        if (!$this->canPivot($right)) return false;

        $last = $this->rows[count($this->rows) - 1];
        $pivoting = $this->isPivoting();
        $arrow = $right ? self::PIVOT_RIGHT : self::PIVOT_LEFT;
        if ($pivoting !== 'no') $right = !$right;
        $facing = ($pivoting === 'no') ? Mathlib::addToHexFacing($last['facing'], $right ? 1 : -1) : $last['facing'];

        $half = intdiv($this->pivotCost, 2);
        $required = array($this->pivotCost % 2, $half, $half, $half, $half); //Array(any, rear, rear, side, side)
        $this->rows[] = $this->newRow($right ? 'pivotright' : 'pivotleft', $last['q'], $last['r'], $last['speed'], $last['heading'], $facing, $required, 0)
            + array('arrow' => $arrow);
        return $this->payLastRow(0);
    }

    /* doRoll (stage T5e): a roll starts - the ship is ROLLING until the turn changes, when its sides swap (the server flips
       "rolled"), and goes on rolling, its sides swapping every turn, until stopped - or a roll carried over is STOPPED. The
       whole cost through any thrusters. */
    private function roll()
    {
        if (!$this->canRoll()) return false;

        $last = $this->rows[count($this->rows) - 1];
        $this->rows[] = $this->newRow('roll', $last['q'], $last['r'], $last['speed'], $last['heading'], $last['facing'],
            array($this->rollCost, 0, 0, 0, 0), 'thisTurn');
        return $this->payLastRow(0);
    }

    /* doBeginExtendedTurn / setExtendedTurn (stage T5f-5): the begin row - facing and heading unchanged, the whole turn's
       requirement (calculateRequiredThrust) - paid as far as the thrusters and the engine allow but a point short of the whole
       (D2: assignThrust refuses the last point; the page's default pays as much as it can), at least a quarter (D3), then
       committed with what is still owed, BY ROLE, as its value (doneAssignThrust: JSON {"any","rear","side"}). */
    private function beginExtended($right)
    {
        if (!$this->qualifiesExtended($right)) return false;

        $last = $this->rows[count($this->rows) - 1];
        $required = $this->turnRequirement($right);
        $this->rows[] = $this->newRow($right ? self::EXTEND_RIGHT : self::EXTEND_LEFT, $last['q'], $last['r'], $last['speed'], $last['heading'], $last['facing'], $required, 0);
        $index = count($this->rows) - 1;

        for ($guard = 0; $guard < 400; $guard++) {
            $still = $this->stillRequired($index, false);
            if (self::owedTotal($still) <= 1) break;
            $paid = false;
            for ($d = 1; $d <= 4 && !$paid; $d++) {
                if ($still[$d] > 0) $paid = $this->clickBest($index, $d);
            }
            if (!$paid && $still[self::DIR_ANY] > 0) $paid = $this->clickBest($index, null);
            if (!$paid) break;
        }

        $cost = self::owedTotal($required);
        $owed = $this->extendedRoles($this->stillRequired($index, false));
        $paid = $cost - ($owed['any'] + $owed['rear'] + $owed['side']);
        if ($paid < (int)ceil($cost / 4) || $paid > $cost - 1) {
            array_pop($this->rows);
            return false;
        }
        $this->rows[$index]['value'] = json_encode($owed);
        $this->rows[$index]['commit'] = true;
        return true;
    }

    /* doCompleteExtendedTurn (stage T5f-5): the turn owed from last turn, to the side it was begun to, in the hex the ship stands
       in - a turn row with value 'extendedTurn' asking for what was owed, mapped onto the thrusters doing each job now
       (completionRequirement). It cannot be put off or cancelled by choice (R6), so it is paid within the thrusters' ratings if
       it can be and over them (up to the page's own ceiling) if it cannot; overthrust() then reports it. Extra thrust shortens
       its delay (R5), as any turn's. */
    private function completeExtended($extraThrust)
    {
        if (!$this->canCompleteExtended()) return false;

        $owed = $this->owedExtended();
        $right = ($owed['type'] === self::EXTEND_RIGHT);
        $step = $right ? 1 : -1;
        $last = $this->rows[count($this->rows) - 1];
        $row = $this->newRow($right ? 'turnright' : 'turnleft', $last['q'], $last['r'], $last['speed'], Mathlib::addToHexFacing($last['heading'], $step),
            Mathlib::addToHexFacing($last['facing'], $step), $this->completionRequirement($owed), 'extendedTurn');
        $row['arrow'] = self::COMPLETE_EXTENDED;

        $this->rows[] = $row;
        if ($this->payLastRow($extraThrust)) return true;
        $allowance = $this->overthrust;
        $this->overthrust = self::SHORTEN_MAX; //clickBest's limit: the page's own ceiling (twice a rating) still holds in click()
        $this->rows[] = $row;
        $paid = $this->payLastRow(0); //no shortening bought over a rating (the user's ruling: shorten within the ratings)
        $this->overthrust = $allowance;
        return $paid;
    }

    private function newRow($type, $q, $r, $speed, $heading, $facing, array $required, $value)
    {
        return array(
            'type' => $type, 'q' => (int)$q, 'r' => (int)$r, 'speed' => (int)$speed, 'heading' => (int)$heading, 'facing' => (int)$facing,
            'preturn' => false, 'turn' => $this->turn, 'value' => $value,
            'requiredThrust' => $required, 'assignedThrust' => array(), 'forced' => false, 'commit' => false
        );
    }


    // =============================================================== the gates (can*)

    //canChangeSpeed
    private function canChangeSpeed()
    {
        //EXTENDED TURNS (R4, D6): no speed change at all the turn after a begin - completed, cancelled or not yet either
        if ($this->owedExtended() !== null) return false;
        if ($this->adrift || $this->engineDestroyed) return false;
        if ($this->speedChangeCost() > $this->remainingEngineThrust()) return false;

        //acceleration must be the first thing in a turn
        foreach ($this->rows as $row) {
            if ($row['turn'] != $this->turn) continue;
            if (!$row['preturn'] && !$row['forced'] && !in_array($row['type'], array('speedchange', 'redline', 'deploy', 'sync', 'attached', 'detach'), true)) return false;
        }

        if ($this->gravitic) return true; //a gravitic drive with the thrust for it changes speed whatever its attitude
        if ($this->isOutOfAlignment() || $this->isPivoting() !== 'no') return false;
        return true;
    }

    //deleteSpeedChange: would a click on this arrow take the last row back instead of adding one?
    private function speedClickWouldUndo($accel)
    {
        $heading = $this->lastCommitted()['heading'];
        foreach ($this->rows as $row) {
            if ($row['turn'] != $this->turn || $row['type'] !== 'speedchange') continue;
            if ($row['preturn'] || $row['forced']) continue; //Engine Shorted's push is not the player's to take back
            $matches = ((int)$row['value'] == ($accel ? 1 : 0));
            if ((!$matches && $row['heading'] == $heading) || ($matches && $row['heading'] != $heading)) return true;
        }
        return false;
    }

    //getSpeedChangeCost: a fractional cost is summed over the turn's changes, then rounded up
    private function speedChangeCost()
    {
        $cost = $this->accelCost;
        if ($cost == floor($cost)) return (int)$cost;

        $done = 0;
        foreach ($this->rows as $row) {
            if ($row['turn'] == $this->turn && $row['type'] === 'speedchange') $done++;
        }
        return (int)(ceil(($done + 1) * $cost - 1e-9) - ceil($done * $cost - 1e-9));
    }

    /* canTurn (either side - the page's test does not depend on it). A gravitic drive may turn while rolling, pivoting or out of
       alignment. $ignoreThrust (stage T5f-5): every condition but "enough engine thrust for the whole turn" - the page's third
       argument, which qualifiesForExtendedTurn asks with. */
    private function canTurn($ignoreThrust = false)
    {
        if ($this->extendedForbids('turnleft')) return false; //(both sides are on the list: the answer is the same for either)
        if ($this->adrift || $this->engineDestroyed) return false;
        if (!$this->gravitic && $this->isRolling()) return false;

        if ($this->currentTurnDelay() > 0) {
            //an agile ship's snap turn may follow straight on from a turn
            $previous = $this->lastCommitted();
            if (!($this->agile && $previous['turn'] == $this->turn && self::isTurn($previous) && $previous['value'] !== 'extendedTurn')) return false;
        }

        if (!$ignoreThrust && $this->remainingEngineThrust() < max(1, $this->turnCost()) + $this->lcvSurcharge) return false;
        if (!$this->gravitic && $this->isPivoting() !== 'no') return false;
        if (!$this->gravitic && !$this->flight && $this->isOutOfAlignment()) return false;

        return true;
    }

    /* qualifiesForExtendedTurn (stage T5f-5) - when the page lets a ship BEGIN an extended turn, by its map icon (canBeginExtendedTurn:
       the same, and a whole turn unaffordable) or by ticking Make Extended Turn on a turn's panel (this, the turn affordable):
       ships only; canTurn's every condition but the thrust; not after a begin this turn (the list); the delay met outright - a
       single 60 degree turn, no agile snap (D7); a cost of at least 2 (D3) and a quarter of it in engine thrust. */
    private function qualifiesExtended($right)
    {
        if ($this->flight) return false;
        if (!$this->canTurn(true)) return false;
        if ($this->extendedForbids($right ? self::EXTEND_RIGHT : self::EXTEND_LEFT)) return false;
        if ($this->currentTurnDelay() > 0) return false;
        $cost = self::owedTotal($this->turnRequirement($right));
        if ($cost < 2) return false;
        return $this->remainingEngineThrust() >= (int)ceil($cost / 4);
    }

    //canCompleteExtendedTurn (stage T5f-5): a turn owed and outstanding, and payable (no reason to cancel it).
    private function canCompleteExtended()
    {
        return $this->isExtendedOutstanding() && $this->extendedCancelReason() === null;
    }

    //canJink, a flight adding a level: the thrust for it, and the jinking limit
    private function canJink()
    {
        if ($this->extendedForbids('jink')) return false;
        if (!$this->flight || $this->adrift) return false;
        if ($this->remainingEngineThrust() < $this->jinkCost) return false;
        return $this->jinking() + 1 <= $this->jinkLimit;
    }

    private function turnCost()
    {
        $rate = $this->turnCostRate;
        if ($this->submarine && $this->isGoingBackwards()) $rate = $rate * 1.33;
        return (int)ceil($this->speed() * $rate);
    }

    //calculateRequiredThrust: half through the side, half through the rear, an odd point anywhere
    private function turnRequirement($right)
    {
        $required = array(null, null, null, null, null);

        //a flight: the whole cost from its pool, never less than 1 (asked before the speed-0 case, as the page asks it)
        if ($this->flight) return array(max(1, $this->turnCost()), null, null, null, null);

        if ($this->speed() === 0) return array(1 + $this->lcvSurcharge, 0, 0, 0, 0); //one point, through any thruster

        $cost = $this->turnCost() + $this->lcvSurcharge;
        $required[self::DIR_ANY] = $cost % 2;
        $required[$this->directionRequired(self::DIR_MAIN, false, true)] = intdiv($cost, 2);
        //a turn to starboard is pushed by the port thrusters
        $required[$this->directionRequired($right ? self::DIR_PORT : self::DIR_STBD, false, true)] = intdiv($cost, 2);

        return $required;
    }

    //canSlip (a gravitic drive may slip while rolling)
    private function canSlip($right)
    {
        if ($this->extendedForbids($right ? 'slipright' : 'slipleft')) return false;
        if ($this->adrift || $this->engineDestroyed) return false;
        if (!$this->gravitic && $this->isRolling()) return false;

        $name = $right ? 'slipright' : 'slipleft';
        $other = $right ? 'slipleft' : 'slipright';
        $moveBetween = true;
        foreach ($this->rows as $row) {
            if ($row['turn'] != $this->turn) continue;
            if ($row['type'] === $other || $row['type'] === $name) $moveBetween = false;
            if ($row['type'] === 'move') $moveBetween = true;
            if ($row['type'] === $other) return false; //one side only in a turn
        }
        if (!$moveBetween) return false;

        $thrust = $this->remainingEngineThrust();
        if (!$this->flight && ceil($this->speed() / 5) > $thrust) return false;
        if ($thrust == 0) return false;
        if ($this->remainingMovement() < 1) return false;

        return true;
    }

    //canMove ('move' is never on the list: only an owed turn not yet completed stops it)
    private function canMove()
    {
        if ($this->extendedForbids('move')) return false;
        return $this->remainingMovement() > 0;
    }

    /* canPivot (stage T5e). A non-agile hull: one pivot started and one stopped a turn, never against the way it pivots, not
       after turning into a pivot; not while rolling unless gravitic; the pivot's cost in thrust left. */
    private function canPivot($right)
    {
        if ($this->flight) return false; //a flight's pivots are the Fire phase's (combat pivots): not plotted here
        if ($this->extendedForbids($right ? 'pivotright' : 'pivotleft')) return false;
        if ($this->adrift || $this->engineDestroyed) return false;
        if (!$this->gravitic && $this->isRolling()) return false;

        $pivoted = $this->hasPivoted();
        $pivoting = $this->isPivoting();
        if ($this->hasTurnedIntoPivot() && !$this->agile) return false;
        if ($pivoted['right'] && $pivoting !== 'right' && $right && !$this->agile) return false;
        if ($pivoted['left'] && $pivoting !== 'left' && !$right && !$this->agile) return false;
        if (($right && $pivoting === 'left') || (!$right && $pivoting === 'right' && !$this->agile)) return false;
        if ($this->pivotCost > $this->remainingEngineThrust()) return false;
        return true;
    }

    /* canRoll (stage T5e): the cost in thrust left; a roll started THIS turn cannot be stopped this turn (the page takes it
       back instead), one carried over always can; else one roll a turn (an agile hull: any number), and not while pivoting
       unless gravitic. */
    private function canRoll()
    {
        if ($this->flight) return false;
        if ($this->extendedForbids('roll')) return false;
        if ($this->adrift || $this->engineDestroyed) return false;
        if ($this->rollCost > $this->remainingEngineThrust()) return false;

        $rolling = $this->isRolling();
        if (!$this->isRollingForIcon() && $rolling) return false;
        if ($rolling) return true;
        if (!$this->agile && $this->hasRolled()) return false;
        if ($this->isPivoting() !== 'no' && !$this->gravitic) return false;
        return true;
    }


    // =============================================================== paying (assignThrust)

    /* Pay for the row just added, one point at a time, each point through assignThrust's own
       rules; then up to $extra more on a turn. Commits the row and returns true, or removes it
       and returns false when the requirement cannot be met within the thrusters' ratings. */
    private function payLastRow($extra)
    {
        $index = count($this->rows) - 1;

        //A flight has no thrusters: the row is paid from its pool in full, and committed (changeSpeed / doNormalTurn / doSlip)
        if ($this->flight) {
            $cost = (int)$this->rows[$index]['requiredThrust'][self::DIR_ANY];
            if ($this->remainingEngineThrust() < $cost) {
                array_pop($this->rows);
                return false;
            }
            $this->rows[$index]['assignedThrust'] = array(self::DIR_ANY => $cost);
            $this->rows[$index]['commit'] = true;
            return true;
        }

        //A pivot is paid through one of two thruster pairs (stage T5e - see stillRequired): the first pair that can pay.
        if (self::isPivotRow($this->rows[$index])) {
            if (!$this->payPivot($index)) {
                array_pop($this->rows);
                return false;
            }
            $this->rows[$index]['commit'] = true;
            return true;
        }

        for ($guard = 0; $guard < 400; $guard++) {
            $still = $this->stillRequired($index, false);

            $direction = null;
            for ($d = 1; $d <= 4; $d++) {
                if ($still[$d] > 0) { $direction = $d; break; }
            }
            if ($direction === null && $still[self::DIR_ANY] <= 0) break; //paid

            if (!$this->clickBest($index, $direction)) {
                array_pop($this->rows);
                return false;
            }
        }

        if (self::isTurn($this->rows[$index])) {
            $placed = 0;
            for ($point = 0; $point < $extra; $point++) {
                if (!$this->clickBest($index, null)) break;
                $placed++;
            }
            if ($placed > 0) $this->rows[$index]['extra'] = $placed; //extras(): the replay gives exactly these clicks again
        }

        $this->rows[$index]['commit'] = true;
        return true;
    }

    /* A pivot row's payment (stage T5e). The page asks any + rear + rear + side + side and, once a point goes through one
       PAIR's directions, drops the other pair (calculateThrustStillReq): clockwise the retro and port jobs or the main and
       starboard jobs, anticlockwise retro and starboard or main and port - "jobs" as thrusterDirectionRequired names them
       for the ship's attitude. Each pair is tried in turn, its odd point through it too; the row is left as it was between
       tries. The page never pays a pivot by itself (autoAssignThrust skips pivots): every point here is a click. */
    private function payPivot($index)
    {
        $right = ($this->rows[$index]['type'] === 'pivotright');
        $pairs = array(
            array($this->directionRequired(self::DIR_RETRO), $this->directionRequired($right ? self::DIR_PORT : self::DIR_STBD)),
            array($this->directionRequired(self::DIR_MAIN), $this->directionRequired($right ? self::DIR_STBD : self::DIR_PORT))
        );
        foreach ($pairs as $pair) {
            $before = $this->rows[$index];
            if ($this->payThrough($index, $pair)) return true;
            $this->rows[$index] = $before;
        }
        return false;
    }

    private function payThrough($index, array $pair)
    {
        for ($guard = 0; $guard < 400; $guard++) {
            $still = $this->stillRequired($index, false);
            $direction = null;
            foreach ($pair as $d) {
                if ($still[$d] > 0) { $direction = $d; break; }
            }
            if ($direction === null) {
                for ($d = 1; $d <= 4; $d++) {
                    if ($still[$d] > 0) { $direction = $d; break; }
                }
            }
            if ($direction !== null) {
                if (!$this->clickBest($index, $direction)) return false;
                continue;
            }
            if ($still[self::DIR_ANY] <= 0) return true; //paid
            if (!$this->clickBest($index, $pair[0]) && !$this->clickBest($index, $pair[1]) && !$this->clickBest($index, null)) return false;
        }
        return false;
    }

    /* One more point on row $index: through a thruster facing $direction, or through any when
       $direction is null. Within ratings before over one (allowOverthrust), sound thrusters before
       damaged ones, then the one with most to spare; for "any", the thrusters along the line of
       travel first, as the page's own auto-assign does. */
    private function clickBest($index, $direction)
    {
        $along = $this->isGoingBackwards() ? self::DIR_RETRO : self::DIR_MAIN;

        $candidates = array();
        foreach ($this->thrusters as $id => $thruster) {
            if ($thruster['dead']) continue;
            if ($direction !== null && $thruster['direction'] != $direction) continue;

            $used = $this->channeledEffective($id);
            $spare = $thruster['output'] + $this->overthrust - $used;
            if ($spare < 1) continue; //never beyond its rating, or beyond the overthrust allowed

            $over = ($used >= $thruster['output']) ? 1 : 0;
            $damaged = ($thruster['half'] > 0 ? 2 : 0) + ($thruster['fti'] ? 1 : 0);
            $candidates[] = array($over, $damaged, $thruster['direction'] == $along ? 0 : 1, -$spare, $id);
        }
        sort($candidates);

        foreach ($candidates as $candidate) {
            if ($this->click($index, $candidate[4])) return true; //the thruster id: the last of the sort keys
        }
        return false;
    }

    //assignThrust: one click on a thruster while row $index waits for thrust
    private function click($index, $id)
    {
        $thruster = $this->thrusters[$id];
        if ($this->adrift || $this->engineDestroyed || $thruster['dead']) return false;

        $row = $this->rows[$index];
        $already = $this->channeledEffective($id);
        $still = $this->stillRequired($index, false);
        $isTurn = self::isTurn($row);
        $nothingOwed = ($still[$thruster['direction']] <= 0 && $still[self::DIR_ANY] <= 0);

        if ($nothingOwed && !$isTurn) return false;
        //EXTENDED TURNS (D2, stage T5f-5): a begin is never paid in full - the point that would leave nothing owed is refused
        if (self::isExtendedStartRow($row) && self::owedTotal($still) <= 1) return false;
        if ($nothingOwed && $isTurn && $this->turnDelayOfRow($index) - 1 < 1) return false; //a turn delay is never bought below 1
        if ($thruster['output'] * 2 < $already + 1) return false;

        $step = 1 + $thruster['half'];
        if ($thruster['fti'] && $already <= 0) $step++;

        $assigned = $this->assignedByDirection($index, false);
        $largestRequired = 0;
        $largestElsewhere = 0;
        for ($d = 0; $d <= 4; $d++) {
            if (isset($row['requiredThrust'][$d]) && $row['requiredThrust'][$d] > $largestRequired) $largestRequired = $row['requiredThrust'][$d];
            if ($d != $thruster['direction'] && $assigned[$d] > $largestElsewhere) $largestElsewhere = $assigned[$d];
        }
        if ($assigned[$thruster['direction']] > $largestRequired && $assigned[$thruster['direction']] > $largestElsewhere) return false;

        if ($this->remainingEngineThrust() < $step) return false;

        $this->rows[$index]['assignedThrust'][$id] = (isset($row['assignedThrust'][$id]) ? (int)$row['assignedThrust'][$id] : 0) + $step;
        $this->rows[$index]['clicks'][] = $id;
        return true;
    }

    //getAmountChanneledReal: raw thrust sent through a thruster this turn
    private function channeledReal($id, $committedOnly)
    {
        $used = 0;
        foreach ($this->rows as $row) {
            if ($row['turn'] != $this->turn) continue;
            if ($committedOnly && !$row['commit']) continue;
            if (isset($row['assignedThrust'][$id])) $used += (int)$row['assignedThrust'][$id];
        }
        return $used;
    }

    //countAmountChanneled: what that thrust was worth, after the thruster's criticals
    private function channeledEffective($id)
    {
        $used = $this->channeledReal($id, false);
        if ($this->thrusters[$id]['fti']) $used--;
        return (int)ceil($used / ($this->thrusters[$id]['half'] + 1));
    }

    //calculateAssignedThrust: a row's thrust by thruster direction, after criticals
    private function assignedByDirection($index, $overthrustCheck)
    {
        $row = $this->rows[$index];
        $byDirection = array(0, 0, 0, 0, 0);

        foreach ($row['assignedThrust'] as $id => $amount) {
            if ($amount === null || !isset($this->thrusters[$id])) continue;
            $thruster = $this->thrusters[$id];

            $sub = 0;
            if ($thruster['fti']) {
                if ($overthrustCheck) {
                    if ($this->channeledReal($id, false) == $amount) $sub = 1; //the thruster's whole turn went into this row
                } else if ($this->channeledReal($id, true) === 0) {
                    $sub = 1; //the first point through it this turn
                }
            }

            $byDirection[$thruster['direction']] += (int)ceil($amount / ($thruster['half'] + 1)) - $sub;
            if ($byDirection[$thruster['direction']] < 0) $byDirection[$thruster['direction']] = 0;
        }

        return $byDirection;
    }

    //calculateThrustStillReq: what a row still needs; thrust over a direction's need pays the "any" share
    private function stillRequired($index, $overthrustCheck)
    {
        $assigned = $this->assignedByDirection($index, $overthrustCheck);
        $required = $this->rows[$index]['requiredThrust'];
        $still = array(0, 0, 0, 0, 0);
        $surplus = 0;

        for ($d = 0; $d <= 4; $d++) {
            $need = isset($required[$d]) ? (int)$required[$d] : 0;
            if ($assigned[$d] > $need) {
                $surplus += $assigned[$d] - $need;
            } else {
                $still[$d] = $need - $assigned[$d];
            }
        }
        $still[self::DIR_ANY] -= $surplus;

        /* A pivot (stage T5e): once one pair's jobs have a point, the other pair is no longer asked (the page nulls it - a null
           and a 0 answer every test alike). Directions as thrusterDirectionRequired names the jobs for this attitude. */
        $row = $this->rows[$index];
        if (self::isPivotRow($row)) {
            $port = $this->directionRequired(self::DIR_PORT);
            $stbd = $this->directionRequired(self::DIR_STBD);
            $main = $this->directionRequired(self::DIR_MAIN);
            $retro = $this->directionRequired(self::DIR_RETRO);
            if ($row['type'] === 'pivotright') { //clockwise
                if ($assigned[$retro] > 0 || $assigned[$port] > 0) { $still[$main] = 0; $still[$stbd] = 0; }
                else if ($assigned[$main] > 0 || $assigned[$stbd] > 0) { $still[$retro] = 0; $still[$port] = 0; }
            } else {
                if ($assigned[$retro] > 0 || $assigned[$stbd] > 0) { $still[$main] = 0; $still[$port] = 0; }
                else if ($assigned[$main] > 0 || $assigned[$port] > 0) { $still[$retro] = 0; $still[$stbd] = 0; }
            }
        }

        return $still;
    }


    // =============================================================== turn delay

    //calculateCurrentTurndelay
    private function currentTurnDelay()
    {
        return $this->turnDelayAtRow(count($this->rows) - 1);
    }

    //calculateTurndelayAtMove: counted backwards from row $index to the last turn made
    private function turnDelayAtRow($index)
    {
        if ($index < 0) return 0;

        $delay = (int)ceil($this->rows[$index]['speed'] * $this->turnDelayRate);
        if (!$this->flight) $delay += Officers::turnDelayPenalty($this->ship, $delay); //getQuartermasterTurnDelay: not a flight's
        $delay = $this->applyCrew($delay);
        $delay += $this->lcvSurcharge;

        $didTurn = false;
        $moved = 0;
        for ($i = $index; $i >= 0; $i--) {
            $row = $this->rows[$i];
            if ($row['type'] === 'move' || $row['type'] === 'slipleft' || $row['type'] === 'slipright') $moved++;
            if ($row['type'] === 'speedchange' && $this->turnDelayAtRow($i - 1) == 0) return 0; //satisfied before the speed changed
            if ($row['speed'] == 0) return 0;
            if (self::isTurn($row)) {
                $didTurn = true;
                $moved += $this->turnSequenceExtraThrust($i);
                break;
            }
            if ($moved >= $delay) break;
        }

        return $didTurn ? max(0, $delay - $moved) : 0;
    }

    //calculateTurndelay: the delay row $index's turn costs, with the extra thrust already paid on it
    private function turnDelayOfRow($index)
    {
        $row = $this->rows[$index];
        if ($row['speed'] == 0 || $this->turnDelayRate == 0) return 0;

        $delay = (int)ceil($row['speed'] * $this->turnDelayRate);
        $delay += Officers::turnDelayPenalty($this->ship, $delay);
        $delay -= self::isTurn($row) ? $this->turnSequenceExtraThrust($index) : $this->extraThrustSpent($index);
        if ($delay < 0) $delay = 0;
        $delay = $this->applyCrew($delay);

        return $delay + $this->lcvSurcharge;
    }

    //applyCrewTurnDelay: Poor Crew always adds; Elite Crew shortens a delay that exists, never below 1
    private function applyCrew($delay)
    {
        if ($this->crewMod == 0) return $delay;
        if ($this->crewMod > 0) return $delay + $this->crewMod;
        if ($delay <= 0) return $delay;
        return max(1, $delay + $this->crewMod);
    }

    //calculateExtraThrustSpent
    private function extraThrustSpent($index)
    {
        $still = $this->stillRequired($index, true);
        return max(0, -$still[self::DIR_ANY]);
    }

    //calculateTurnSequenceExtraThrust: a run of turns shares one delay
    private function turnSequenceExtraThrust($index)
    {
        $extra = 0;
        while ($index >= 0 && self::isTurn($this->rows[$index])) {
            $extra += $this->extraThrustSpent($index);
            $index--;
        }
        return $extra;
    }


    // =============================================================== the ship's attitude

    private static function isTurn(array $row)
    {
        return $row['type'] === 'turnright' || $row['type'] === 'turnleft';
    }

    //getLastCommitedMove
    private function lastCommitted()
    {
        $found = null;
        foreach ($this->rows as $row) {
            if ($row['commit']) $found = $row;
        }
        return $found !== null ? $found : $this->rows[0];
    }

    //getSpeed
    private function speed()
    {
        return (int)$this->lastCommitted()['speed'];
    }

    //getRemainingMovement
    private function remainingMovement()
    {
        $used = 0;
        foreach ($this->rows as $row) {
            if ($row['turn'] != $this->turn || !$row['commit']) continue;
            if ($row['type'] === 'move' || $row['type'] === 'slipleft' || $row['type'] === 'slipright') $used++;
        }
        return $this->speed() - $used;
    }

    //getRemainingEngineThrust
    private function remainingEngineThrust()
    {
        $left = $this->engineThrust;
        foreach ($this->rows as $row) {
            if ($row['turn'] != $this->turn) continue;
            if ($row['preturn'] && $row['type'] === 'speedchange') continue; //Engine Shorted's push is the shorted engine's own
            foreach ($row['assignedThrust'] as $amount) $left -= (int)$amount;
        }
        return $left;
    }

    //isGoingBackwards: the heading lies in the aft half
    private function isGoingBackwards()
    {
        $last = $this->lastCommitted();
        $heading = $last['heading'];
        $facing = $last['facing'];
        return !($facing == $heading || Mathlib::addToHexFacing($facing, 1) == $heading || Mathlib::addToHexFacing($facing, -1) == $heading);
    }

    //isOutOfAlignment: flying neither bow-first nor stern-first
    private function isOutOfAlignment()
    {
        $last = $this->lastCommitted();
        return !($last['facing'] == $last['heading'] || Mathlib::addToHexFacing($last['facing'], 3) == $last['heading']);
    }

    //isRolling
    private function isRolling()
    {
        if ($this->agile) return false;
        $rolling = false;
        foreach ($this->rows as $row) {
            if ($row['turn'] != $this->turn) continue;
            if ($row['type'] === 'isRolling') $rolling = true;
            if ($row['type'] === 'roll' && $row['commit']) $rolling = !$rolling;
        }
        return $rolling;
    }

    //isRolled
    private function isRolled()
    {
        if ($this->agile) {
            $rolled = false;
            foreach ($this->rows as $row) {
                if ($row['type'] === 'isRolled') $rolled = true;
                if ($row['type'] === 'roll') $rolled = !$rolled;
            }
            return $rolled;
        }

        foreach ($this->rows as $row) {
            if ($row['turn'] == $this->turn && $row['type'] === 'isRolled') return true;
        }
        return false;
    }

    //isPivoting
    private function isPivoting()
    {
        if ($this->agile) return 'no';
        $pivoting = 'no';
        foreach ($this->rows as $row) {
            if ($row['turn'] != $this->turn || !$row['commit']) continue;
            if ($row['type'] === 'isPivotingLeft') $pivoting = 'left';
            if ($row['type'] === 'isPivotingRight') $pivoting = 'right';
            if (!$row['preturn']) {
                if ($row['type'] === 'pivotright' && $pivoting === 'no') $pivoting = 'right';
                else if ($row['type'] === 'pivotleft' && $pivoting === 'no') $pivoting = 'left';
                else if ($row['type'] === 'pivotright' && $pivoting === 'left') $pivoting = 'no';
                else if ($row['type'] === 'pivotleft' && $pivoting === 'right') $pivoting = 'no';
            }
            if ($row['value'] === 'turnIntoPivot') $pivoting = 'no';
        }
        return $pivoting;
    }

    //hasPivoted: which ways a pivot row of this turn (not a preturn one) went - a forced one and a stop included
    private function hasPivoted()
    {
        $has = array('left' => false, 'right' => false);
        foreach ($this->rows as $row) {
            if ($row['turn'] != $this->turn || $row['preturn']) continue;
            if ($row['type'] === 'pivotleft') $has['left'] = true;
            if ($row['type'] === 'pivotright') $has['right'] = true;
        }
        return $has;
    }

    //hasTurnedIntoPivot: a turn into the pivot this turn (an order this class never gives; a loaded row may carry it)
    private function hasTurnedIntoPivot()
    {
        foreach ($this->rows as $row) {
            if ($row['turn'] == $this->turn && $row['value'] === 'turnIntoPivot') return true;
        }
        return false;
    }

    //isRollingForIcon: rolling as the turn began - a roll row of this turn ('thisTurn') does not count
    private function isRollingForIcon()
    {
        if ($this->agile) return false;
        $rolling = false;
        foreach ($this->rows as $row) {
            if ($row['turn'] != $this->turn) continue;
            if ($row['value'] === 'thisTurn' || $row['value'] === 'emergencyRoll') continue;
            if ($row['type'] === 'isRolling') $rolling = true;
            if ($row['type'] === 'roll' && $row['commit']) $rolling = !$rolling;
        }
        return $rolling;
    }

    //hasRolled: a roll row of this turn, or rolling as it began
    private function hasRolled()
    {
        foreach ($this->rows as $row) {
            if ($row['turn'] == $this->turn && ($row['type'] === 'roll' || $row['type'] === 'isRolling')) return true;
        }
        return false;
    }

    private static function isPivotRow(array $row)
    {
        return $row['type'] === 'pivotleft' || $row['type'] === 'pivotright';
    }


    // =============================================================== extended turns (stage T5f-5, movement.js section "EXTENDED TURNS")

    //isExtendedTurnStart
    private static function isExtendedStartRow(array $row)
    {
        return $row['type'] === self::EXTEND_LEFT || $row['type'] === self::EXTEND_RIGHT;
    }

    //getExtendedTurnStart: this turn's begin row, or null
    private function extendedStart()
    {
        foreach ($this->rows as $row) {
            if ($row['turn'] == $this->turn && self::isExtendedStartRow($row)) return $row;
        }
        return null;
    }

    //getOwedExtendedTurn: LAST turn's begin row (the last of them), or null - the loaded rows hold turn N-1's (Movement's load window)
    private function owedExtended()
    {
        $owed = null;
        foreach ($this->rows as $row) {
            if ($row['turn'] == $this->turn - 1 && self::isExtendedStartRow($row)) $owed = $row;
        }
        return $owed;
    }

    //isExtendedTurnOutstanding: a turn owed, and this turn has neither a cancellation nor a COMMITTED completion
    private function isExtendedOutstanding()
    {
        if ($this->owedExtended() === null) return false;
        foreach ($this->rows as $row) {
            if ($row['turn'] != $this->turn) continue;
            if ($row['type'] === 'extendTurnCancel') return false;
            if ($row['commit'] && self::isTurn($row) && $row['value'] === 'extendedTurn') return false;
        }
        return true;
    }

    /* extendedTurnForbids - THE one predicate every manoeuvre gate asks with the row type it would create: true while an owed
       turn is outstanding (nothing comes before the completion), or after a begin this turn for a type on the server's list
       (Movement::$extendedTurnBlockedTypes - the MIRROR PAIR of the page's own; "a manoeuvre is a turn, a pivot or a roll"). */
    private function extendedForbids($type)
    {
        if ($this->isExtendedOutstanding()) return true;
        return $this->extendedStart() !== null && in_array($type, Movement::$extendedTurnBlockedTypes, true);
    }

    //getExtendedTurnCost / getExtendedTurnOwedTotal: the positive slots of a requirement, summed
    private static function owedTotal(array $slots)
    {
        $total = 0;
        foreach ($slots as $amount) {
            if ($amount > 0) $total += (int)$amount;
        }
        return $total;
    }

    //getExtendedTurnRoles: what is still owed BY ROLE - the "either" slot, the aft pair (1 + 2), the side pair (3 + 4)
    private static function extendedRoles(array $still)
    {
        $owed = function ($slot) use ($still) { return max(0, isset($still[$slot]) ? (int)$still[$slot] : 0); };
        return array('any' => $owed(0), 'rear' => $owed(1) + $owed(2), 'side' => $owed(3) + $owed(4));
    }

    /* getExtendedTurnRemainder: what a begin row left owed, by role - the snapshot in its value (written when it was committed;
       recomputing it would read this turn's criticals), or, only when that does not read, the row's own arrays. */
    private function extendedRemainder(array $begin)
    {
        $snapshot = is_string($begin['value']) ? json_decode($begin['value'], true) : null;
        if (is_array($snapshot) && isset($snapshot['any'], $snapshot['rear'], $snapshot['side'])) {
            return array('any' => max(0, (int)$snapshot['any']), 'rear' => max(0, (int)$snapshot['rear']), 'side' => max(0, (int)$snapshot['side']));
        }
        foreach ($this->rows as $index => $row) {
            if ($row === $begin) return self::extendedRoles($this->stillRequired($index, false));
        }
        return array('any' => 0, 'rear' => 0, 'side' => 0);
    }

    /* getExtendedTurnCompletionRequirement: the roles mapped onto the thrusters doing each job NOW (the same two
       thrusterDirectionRequired calls a turn's requirement makes) - after a roll the side role lands on the other side's. */
    private function completionRequirement(array $begin)
    {
        $roles = $this->extendedRemainder($begin);
        $right = ($begin['type'] === self::EXTEND_RIGHT);
        $required = array($roles['any'], null, null, null, null);
        $required[$this->directionRequired(self::DIR_MAIN, false, true)] = $roles['rear'];
        $required[$this->directionRequired($right ? self::DIR_PORT : self::DIR_STBD, false, true)] = $roles['side'];
        return $required;
    }

    /* getExtendedTurnCancelReason: why the owed turn cannot be completed, or null - the engine shorted since the begin (D9)
       first, then the payability dry run, then which it was: too little engine thrust for what is owed, or the thrusters. */
    private function extendedCancelReason()
    {
        $owed = $this->owedExtended();
        if ($owed === null) return null;
        if ($this->engineShortedSince((int)$owed['turn'])) return 'engineShorted';

        $required = $this->completionRequirement($owed);
        if ($this->canPayRequirement($required)) return null;
        $roles = $this->extendedRemainder($owed);
        return ($this->remainingEngineThrust() < $roles['any'] + $roles['rear'] + $roles['side']) ? 'thrust' : 'thrusters';
    }

    /* hasEngineShortedSince: a preturn speed change this turn (Movement::doStuckEngine's push), or an Engine Shorted critical on an
       engine from the begin's turn on - it always takes the engine offline, the push only on a high roll. */
    private function engineShortedSince($beginTurn)
    {
        foreach ($this->rows as $row) {
            if ($row['turn'] == $this->turn && $row['preturn'] && $row['type'] === 'speedchange') return true;
        }
        foreach ($this->ship->systems as $system) {
            if ($system->name !== 'engine') continue;
            foreach ($system->criticals as $critical) {
                if ($critical->phpclass === 'EngineShorted' && (int)$critical->turn >= $beginTurn) return true;
            }
        }
        return false;
    }

    /* canPayRequirement - THE PAYABILITY TEST, a dry run of the page's assignThrust (click() here), not a capacity formula: a
       scratch row paid greedily, the thrusters in ascending id cheapest first (First Thrust Ignored 1, Half Efficiency 2), the
       directions first, then the "either" slot - with the page's ceiling of twice a rating, not this class's own policy. */
    private function canPayRequirement(array $required)
    {
        $trial = clone $this;
        $last = $trial->rows[count($trial->rows) - 1];
        $trial->rows[] = $trial->newRow('extendTurnCheck', $last['q'], $last['r'], $last['speed'], $last['heading'], $last['facing'], $required, 0);
        $index = count($trial->rows) - 1;

        $order = array();
        $position = 0;
        foreach ($trial->thrusters as $id => $thruster) {
            if ($thruster['dead']) continue;
            $order[] = array(($thruster['fti'] ? 1 : 0) + ($thruster['half'] > 0 ? 2 : 0), $position++, $id);
        }
        usort($order, function ($a, $b) { return ($a[0] != $b[0]) ? $a[0] - $b[0] : $a[1] - $b[1]; });

        for ($guard = 0; $guard < 500; $guard++) {
            $still = $trial->stillRequired($index, false);
            if (self::owedTotal($still) <= 0) return true;
            $paid = false;
            foreach ($order as $entry) {
                if ($paid) break;
                if ($still[$trial->thrusters[$entry[2]]['direction']] > 0) $paid = $trial->click($index, $entry[2]);
            }
            foreach ($order as $entry) {
                if ($paid || $still[self::DIR_ANY] <= 0) break;
                $paid = $trial->click($index, $entry[2]);
            }
            if (!$paid) return false;
        }
        return false;
    }

    /* thrusterDirectionRequired: the thrusters that do a job on a ship flying bow-first do the opposite job on one
       flying stern-first (its heading in the aft half), and a rolled ship's sides are swapped.
       A GRAVITIC drive (stage T5d) differs only out of alignment - pivoted, flying neither bow- nor stern-first: there a
       SPEED CHANGE ($turnOrSlip false) is not reversed for the aft half but turned with the pivot (pivoted to port = going
       starboard-forward: the port thrusters push along the heading, the starboard ones against it); a turn or slip keeps
       the plain rule. $accel is the page's third argument: true for BOTH speed arrows (changeSpeed), false only for a
       pivot - which this class never orders. */
    private function directionRequired($direction, $accel = false, $turnOrSlip = false)
    {
        if ($direction > 2 && $this->isRolled()) $direction = ($direction == self::DIR_PORT) ? self::DIR_STBD : self::DIR_PORT;

        $outOfAlignment = $this->gravitic && $this->isOutOfAlignment();
        if ($this->isGoingBackwards() && (!$this->gravitic || $turnOrSlip || !$outOfAlignment)) {
            switch ($direction) {
                case self::DIR_RETRO: $direction = self::DIR_MAIN; break;
                case self::DIR_MAIN: $direction = self::DIR_RETRO; break;
                case self::DIR_PORT: $direction = self::DIR_STBD; break;
                case self::DIR_STBD: $direction = self::DIR_PORT; break;
            }
        }

        if ($outOfAlignment && !$turnOrSlip) {
            if ($this->isPivotedPort()) { //the port thrusters do the main ones' job, the starboard ones the retros'
                switch ($direction) {
                    case self::DIR_RETRO: return $accel ? self::DIR_STBD : self::DIR_PORT;
                    case self::DIR_MAIN: return $accel ? self::DIR_PORT : self::DIR_STBD;
                    case self::DIR_PORT: return self::DIR_RETRO;
                    case self::DIR_STBD: return self::DIR_MAIN;
                }
            } else { //pivoted to starboard: the starboard thrusters do the main ones' job, the port ones the retros'
                switch ($direction) {
                    case self::DIR_RETRO: return $accel ? self::DIR_PORT : self::DIR_STBD;
                    case self::DIR_MAIN: return $accel ? self::DIR_STBD : self::DIR_PORT;
                    case self::DIR_PORT: return self::DIR_MAIN;
                    case self::DIR_STBD: return self::DIR_RETRO;
                }
            }
        }

        return $direction;
    }

    //isPivotedPort: out of alignment with the heading one or two hex sides to starboard of the bow - the bow turned to port of the course
    private function isPivotedPort()
    {
        if (!$this->isOutOfAlignment()) return false;
        $last = $this->lastCommitted();
        return Mathlib::addToHexFacing($last['facing'], 1) == $last['heading'] || Mathlib::addToHexFacing($last['facing'], 2) == $last['heading'];
    }
}
