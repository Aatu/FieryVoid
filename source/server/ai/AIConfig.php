<?php
/**
 * CPU opponent - every number and list the AI is tuned by, in one place (TACTICAL_AI_PLAN.md
 * section 5). Stage T0 needs only the driver's limits; weights and thresholds join as the
 * deciders are built.
 */
class AIConfig
{
    //The feature flag (Features.php). Off = none of source/server/ai/ is reachable.
    const FLAG = 'cpuOpponent';

    //Seconds one request may spend driving bots. A longer CPU turn carries on at the next poll.
    const BUDGET_SECONDS = 4.0;

    //Hard stop on submissions in one run, whatever the clock says (the self-play harness has no clock).
    const MAX_SUBMISSIONS_PER_RUN = 200;

    //A bot refused this many times at the same point of a game gives the game up (plan section 4).
    const STALL_LIMIT = 3;

    //Rows of tac_ai_log kept per game (DBManager::aiLog).
    const LOG_CAP = 2000;

    //How long "this game still has a bot waiting to play" is remembered between polls (seconds).
    const PENDING_TTL = 120;

    /* Movement, stage T1b (MoveDecider): a ship holds its course unless that is bad, and "bad" is
       scored with these. A blocked hex entered this turn, ending the turn off the map, and either
       of them on next turn's straight continuation. */
    const MOVE_PLANS = 300;             //candidate plans ShipMover is asked for when the course must change
    const MOVE_COST_BLOCKED = 10000;
    const MOVE_COST_OFF_MAP = 5000;
    const MOVE_COST_NEXT_TURN = 1000;       //a blocked hex on next turn's straight continuation
    const MOVE_COST_PER_HEX_OVER = 100;     //each hex past the map's edge, at this turn's end and next turn's
    const MOVE_COST_SPEED_LOST = 150;       //each point of speed given up to avoid trouble

    /* Ships, stage T5 (ShipDecider): where a ship goes, among EVERY end state ShipMover finds for it.
       The fire terms are in "value removed" - expected damage times what a point of the unit hit is
       worth (its value over the damage that finishes it), as FireDecider's worth. The shaping terms
       (closing, formation, speed, manoeuvres) are in HEXES, each hex worth MOVE_HEX_VALUE of the ship's
       own value: enough to decide where fire is slight or even, too little to override a real trade.
       User rulings 2026-11-16: range by best trade, loose formation, damaged sections protected
       moderately and destroyed ones far more. */
    const SHIP_PLANS = 1500;                //end states asked of ShipMover (big hulls have well under 200)
    const MOVE_HEX_VALUE = 0.005;           //one hex of the shaping terms, as a share of the ship's own value
    const MOVE_DANGER_WEIGHT = 0.75;        //the enemy's fire at the ship, against its own at the enemy: under 1 so a balanced bot leans into a fight
    const MOVE_UNMOVED_OFFENCE = 0.5;       //fire at an enemy that has yet to move this turn: it may turn away
    const MOVE_UNMOVED_DANGER = 0.5;        //an enemy yet to move: EVERY gun of it counts (it may turn to bring them to bear), at this weight
    const MOVE_LAUNCH_WEIGHT = 0.8;         //next turn's launches from the end hex (the end state decides which launchers bear)
    const MOVE_NEXT_TURN_WEIGHT = 0.5;      //the trade a turn on, everybody flying straight on: keeps a ship from overshooting
    const MOVE_SECTION_DAMAGED = 0.5;       //a hit on a section counts 1 + this x the share of the section already lost
    const MOVE_SECTION_DESTROYED = 3.0;     //...and a hit through a destroyed section - into the primary - this much
    const MOVE_CLOSING_PER_HEX = 1.0;       //each hex to the nearest enemy where the Fire phase should find it (always counted)...
    const MOVE_CLOSING_NEXT_TURN = 0.5;     //...and this much a hex of it a turn on, everybody flying straight on: a ship closing points at its enemy
    const MOVE_FORMATION_RANGE = 4;         //a loose formation: within this many hexes of the nearest of the bot's other ships...
    const MOVE_FORMATION_PER_HEX = 2.5;     //...at this much a hex beyond it (more than closing gains a hex, so a leader waits for the fleet)
    const MOVE_MIN_SPEED = 3;               //each point of speed below this costs MOVE_COST_SLOW (the deployer's floor too)
    const MOVE_COST_SLOW = 1.0;
    const MOVE_COST_FAST = 1.5;             //each point above the comfortable speed of the ship's size (live data: capital 4.7, heavy 5.2, medium 6.1)
    const MOVE_COMFORT_SPEED = array(3 => 5, 2 => 5, 1 => 6, 0 => 7); //shipSizeClass => speed; a bigger class uses the last
    const MOVE_COST_UNTURNABLE = 5.0;       //each point of speed above the fastest at which a whole turn's thrust still pays for a turn (ShipMover::fastestTurnable)
    /* Overthrust (stage T5b; user ruling 2026-11-13 - players overthrust to make a turn they need, to stay on the
       map above all). Plans within the thrusters' ratings are found first; overthrust only adds end states they
       cannot reach, each priced by the critical it invites. */
    const MOVE_OVERTHRUST = 99;             //points over a rating a thruster may carry (the page's own ceiling, twice the rating, still holds); 0 = never
    const MOVE_COST_THRUSTER_CRIT = 10.0;   //hexes per expected thruster critical (1 point over a sound thruster: 35% + 10% = 4.5 hexes)
    const MOVE_COST_TURN = 0.2;             //each turn: the plainest plan wins among equals
    const MOVE_COST_SLIP = 0.5;             //each sideslip - dearer, or slips become a cheaper way than braking to give up ground
    const MOVE_COST_SPEED_CHANGE = 0.3;     //each speed change

    /* Flights, stage T4 (FlightDecider): where a fighter flight goes, among the legal plans ShipMover
       finds for it. Scores are in "value removed" - expected damage times what a point of the unit
       hit is worth (its value over the damage that finishes it), as FireDecider's worth. */
    const FLIGHT_PLANS = 250;               //plans ShipMover is asked for, per flight
    const FLIGHT_DANGER_WEIGHT = 1.0;       //what the enemy's guns could do to the flight where it ends, against what it does
    const FLIGHT_CLOSING_PER_HEX = 2.0;     //each hex between the flight's guns' reach and its target, when nothing is in reach
    const FLIGHT_INTERCEPT_RANGE = 12;      //an enemy flight this close to one of the bot's ships is what the flight goes after first
    const FLIGHT_ESCORT_RANGE = 6;          //with nothing in reach, a flight keeps within this many hexes of the fleet...
    const FLIGHT_TETHER_PER_HEX = 1.0;      //...at this much a hex beyond it

    /* Firing, stage T2 (FireDecider). Chances are percentages, as Weapon::calculateHitBase gives
       them, before interception. */
    const FIRE_MIN_CHANCE = 5;              //no order below this: the page itself refuses below 1, and this keeps clear of rounding
    /* A shot costs a weapon its whole reload, so the longer that is, the better the shot has to be
       (user, 2026-11-15: "1-20% with a weapon that recharges in 1 turn anyway is fine; 1-33% for a
       3 or 4 turn recharge would be unwise"). Loading time => the least chance it fires at; a
       longer loading time than the last entry uses the last. FireDecider::minimumChance. */
    const FIRE_MIN_CHANCE_BY_LOAD = array(2 => 25, 3 => 34, 4 => 40);
    const FIRE_MIN_CHANCE_PARTING = 15;     //...but a slow weapon whose target is drawing away takes what it can get, down to this
    /* Holding a slow weapon for next turn (FireDecider::holdSlow). The same target is priced again
       where both units will be if they fly straight on; the weapon waits when
       now < next x FIRE_HOLD_CONFIDENCE x (1 - 1 / loading time) - a turn of waiting costs a
       1-turn gun everything and a 4-turn gun a quarter of a shot. */
    const FIRE_HOLD_CONFIDENCE = 0.8;       //what next turn's shot is taken to be worth against this turn's certain one (the target may turn away, the gun may be lost)
    const FIRE_MIN_CHANCE_EDGE = 20;        //...for a shot from exactly between two of the target's sections, which the page may price up to 15 lower (AIView::onEdge)
    const FIRE_FOCUS = 0.5;                 //how much a target already being shot at draws the next gun (0 = spread freely)
    const FIRE_OVERKILL = 1.5;              //planned damage on one target stops counting past this multiple of what it has left
    const FIRE_THREAT_WEIGHT = 0.5;         //how much the most dangerous enemy is preferred over a harmless one of equal worth
    /* The interception reserve. The server intercepts with every loaded gun that has an intercept
       rating, was given no order and loads in one turn (Firing::automateIntercept). So a gun is
       "held back" simply by not being given an order. */
    const FIRE_RESERVE_PER_BALLISTIC = 1.0; //interceptors a unit holds back for each ballistic shot coming at it
    const FIRE_INTERCEPTOR_MIN_CHANCE = 30; //an interceptor with no shot at least this good stays on defence

    /* Ballistics, stage T4 (BallisticDecider): launched in Initial Orders, before anybody has
       moved and before the enemy's EW is known. */
    //The rounds a magazine-fed missile rack is given orders with: plain missiles that do damage to the unit they are aimed at.
    //Every other round (Interceptor, Chaff, Jammer, Homing, Stealth, Starburst, Multiwarhead, Kinetic, HARM, Antimine, Piercing,
    //Dropout, Dummy) has rules of its own and is left in the magazine - an Interceptor is still used by the server on defence.
    const BALLISTIC_ROUNDS = array('Basic', 'LongRange', 'Heavy', 'Flash', 'Antifighter', 'D-Light', 'Dogfight');
    const BALLISTIC_MIN_CHANCE = 35;        //no launch below this: a round is spent for good, a torpedo's tube for its whole reload
    const BALLISTIC_MIN_CHANCE_FLIGHT = 50; //...at a flight: one fighter is a poor return on a missile
    const BALLISTIC_RESERVE = 0.0;          //share of a magazine never launched (0 = fire it dry; levels may raise it, stage T8)

    /* Electronic warfare, stage T3 (EWDecider). Every point starts as defence; a point moves to
       offence while that is worth more in expected damage - dealt, weighed against taken. */
    const EW_OFFENCE = 1.0;                 //weight of the damage a point of OEW / CCEW should add to this ship's fire
    const EW_DEFENCE = 1.0;                 //weight of the damage a point of DEW should spare this ship
    const EW_MIN_GAIN = 0.05;               //a point moves only for at least this much (expected damage): keeps ties on defence
    /* Initiative (user, 2026-11-15): a ship locks hard on to an enemy that moves BEFORE it - it
       will see where that enemy ends up and can bring its guns to bear - and only softly (1-2
       points) on to one that moves after it and may fly out of arc. */
    const EW_SOFT_LOCK = 2;                 //the most OEW a ship puts on an enemy that moves after it
    const EW_MOVES_AFTER_WEIGHT = 0.5;      //how much the fire it hopes to bring on such an enemy counts, against 1 for one that moves first

    //What a seat may be set to. Levels and personalities do nothing before stage T5 / T8; the
    //seat stores them from the start so that games created now keep their setting.
    const LEVELS = array('easy', 'normal', 'hard');
    const DEFAULT_LEVEL = 'normal';
    const PERSONALITIES = array('balanced');
    const DEFAULT_PERSONALITY = 'balanced';
}
