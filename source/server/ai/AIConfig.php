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

    /* Seconds (wall time) one request may spend driving bots. A longer CPU turn carries on at the next poll - since stage T8 a
       Movement submission too: its units are decided one at a time, and once this is spent the ones decided are kept (tac_ai_state)
       and the rest decided on the next request, always at least one a request (MoveDecider::orders, TacticalAI 'pending'). */
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
    /* DECISION TIME (stage T5f-8, brought forward from T8 - the user, 2026-11-20: "move forward the decision time-improvements
       currently in Stage T8 to compensate for the increased work" of the enemy forecast). Measured on the Docker stack, one ship's
       Movement decision on a battle board: capital hulls 0.2-0.5 s, an Olympus 0.6-0.9 s, a Tethys 2.1 s (Normal, 492 end states)
       and 3.4 s (Hard, 840) - the SEARCH 1.2 / 2.1 s of it, this turn's terms 0.4 s, next turn's (a second board) 0.9 s.
       Next turn's terms are asked only of the MOVE_NEXT_TURN_PLANS end states best by this turn's score; and each ship has
       MOVE_DECISION_SECONDS of CPU time (not wall time - a process waiting for a CPU is not charged; but SMT siblings and all-core
       clocks still slow its work: 16 self-play processes on an 8-core / 16-thread machine took ~3x the CPU time a decision takes
       alone, so a ladder runs with selfPlayHarness --budget=0): the search stops at MOVE_SEARCH_SHARE of it (the plainest plans
       are found first, so what it has is a sound partial answer), this turn's terms at MOVE_JUDGE_SHARE (in the search's order,
       plainest first), next turn's at the end - always at least one plan judged whole. */
    const MOVE_NEXT_TURN_PLANS = 100;
    const MOVE_DECISION_SECONDS = 2.5;
    const MOVE_SEARCH_SHARE = 0.6;
    const MOVE_JUDGE_SHARE = 0.9;
    /* THE WHOLE SUBMISSION (stage T8, plan section 12 item 2 - what a player waits for is a SUBMISSION, and under simultaneous
       movement one carries every unit of the bot's activation): its units share this much CPU time, each given what is left over
       the units still to decide - never more than MOVE_DECISION_SECONDS, never less than MOVE_UNIT_FLOOR_SECONDS (every unit still
       finds its plainest plans and judges at least one whole). A lone unit's activation is unchanged. The forecast is inside it. */
    const MOVE_SUBMISSION_SECONDS = 8.0;
    const MOVE_UNIT_FLOOR_SECONDS = 0.3;
    /* The enemy forecast (EnemyForecast, stage T5f-7) - once per Movement decision, ~0.01 s an enemy ship yet to move at Normal,
       ~0.1 s at Hard (its own plans too, each end priced). Nearest enemy first; once Hard has spent this much CPU time the rest
       get Normal's forecast. */
    const MOVE_FORECAST_SECONDS = 1.0;
    const MOVE_HEX_VALUE = 0.005;           //one hex of the shaping terms, as a share of the ship's own value
    const MOVE_DANGER_WEIGHT = 0.75;        //the enemy's fire at the ship, against its own at the enemy: under 1 so a balanced bot leans into a fight
    const MOVE_UNMOVED_OFFENCE = 0.5;       //fire at an enemy that has yet to move this turn: it may turn away
    /* An enemy yet to move this turn is MORE dangerous than one that has moved (user, 2026-11-19, game 4750): it sees
       where the ship ended and lines up its best shot - its guns, and the ship's weakest side. So EVERY gun of it counts at
       full weight, and the section it hits leans towards the ship's worst one the closer it is (AIView::exposureUnmoved).
       Next turn's terms keep MOVE_UNMOVED_OFFENCE for both sides: next turn's initiative is not known yet. */
    const MOVE_UNMOVED_DANGER = 1.0;
    const MOVE_UNMOVED_DANGER_NEXT = 0.5;   //...a turn on, everybody moves again in an order nobody knows
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
    /* SAFE SPEED (user ruling 2026-11-19, game 4750: Omegas, thrust 8, at speed 7 and 8 were "a bit too fast to safely
       manoeuvre ... a rare move where you might accelerate to that speed to get a great shot in and finish off an opponent,
       or to escape certain death"). Above the fastest speed at which ONE turn costs no more than this share of the ship's
       thrust (ShipMover::safeSpeed - Omega 6, G'Quan 13), every point of speed costs MOVE_COST_OVERSPEED hexes: only a kill
       or escaping heavy damage outweighs it. */
    const MOVE_SAFE_TURN_SHARE = 0.75;
    const MOVE_COST_OVERSPEED = 25.0;       //hexes (12.5% of the ship's own value) per point of speed above the safe speed (12 still let game 4750's Omega #6 run at 7)
    /* TURN SHORTENING (user, 2026-11-19: "an important ability used by most players ... rarely overthrusting to do so";
       and "always useful (since any delay carries over to next turn as well) if you have the spare thrust on that turn,
       and you won't exceed thruster ratings" - by default for Normal and Hard, Easy may skip it) has no weight of its own.
       ShipMover offers each turn a second time with all the extra thrust it can take (allowShortening), so the search
       finds the double turns only a shortened delay allows - an overthrust for it is priced like any other; and the plan
       chosen has its LAST turn shortened with the thrust it has to spare within the ratings - all of it, or what an order
       after the turn leaves (ShipMover::shortened) - so next turn starts free to turn. */
    /* Overthrust (stage T5b; user ruling 2026-11-13 - players overthrust to make a turn they need, to stay on the
       map above all). Plans within the thrusters' ratings are found first; overthrust only adds end states they
       cannot reach, each priced by the critical it invites. */
    const MOVE_OVERTHRUST = 99;             //points over a rating a thruster may carry (the page's own ceiling, twice the rating, still holds); 0 = never
    const MOVE_COST_THRUSTER_CRIT = 10.0;   //hexes per expected thruster critical (1 point over a sound thruster: 35% + 10% = 4.5 hexes)
    /* THE BOARD, stage T5c (Hazards): terrain that DRIFTS, meteor swarms, and mines the bot can see. Priced in value -
       expected damage times what a point of the unit is worth, as the fire terms are - except a blocked hex entered and
       the map edge, which keep the fixed MOVE_COST_* above (they outweigh everything).
       DRIFT (TerrainDrift, user rulings 2026-11-08): at the START of every turn from turn 2 a one-hex asteroid or a meteor
       swarm stays put (25%) or moves one hex - straight on, or 60 degrees to either side (25% each) - and a unit standing in
       the hex it enters is struck in Pre-Firing. So ending in one of a field's three forward hexes is a 25% overrun, and
       next turn's straight continuation is priced against all four hexes the field may stand in. */
    const HAZARD_DRIFT_CHANCE = 0.25;
    /* MINES (AoE.php, read 2026-11-19): a Proximity or Captor mine attacks the first unit, in initiative order, whose
       starting hex or any hex it ENTERS lies within the mine's range (per kind of unit, halved against a Jammer) with a
       clear line - once, then it is gone; a DEW mine's guns keep firing at what comes within its range. Only mines the bot
       can SEE count (P2), and its own side's only when they have no IFF. A Proximity mine always hits; the others roll. */
    const HAZARD_MINE_HIT = 0.75;
    const MOVE_COST_TURN = 0.2;             //each turn: the plainest plan wins among equals
    const MOVE_COST_SLIP = 0.5;             //each sideslip - dearer, or slips become a cheaper way than braking to give up ground
    const MOVE_COST_SPEED_CHANGE = 0.3;     //each speed change

    /* Pivots and rolls, stage T5e - the user's notes of 2026-11-19: "pivots and rolls are still very rare, advanced types of
       movement ... I would gate them accordingly and probably only set Hard opponents to use Pivots or Rolls. Perhaps Normal
       could in the case of rolling to swap thrusters to turn in a direction it wanted next turn too, but otherwise no." Both
       run on until stopped by hand ("where new players often screw up"): every level stops one carried over from last turn.
       In hexes of the ship's own value, as the other shaping terms; the -3 to hit a pivot or roll puts on this turn's fire is
       the engine's own (Weapon::calculateHitBase), counted where the fire is. */
    const MOVE_MANOEUVRE_SEEDS = 10;        //the best plans (one per end hex) a pivot or roll is tried on the end of
    const MOVE_COST_PIVOT = 4.0;            //each pivot row - a start and its stop, the same turn, 8 (the bot never leaves one running)
    const MOVE_COST_ROLL = 8.0;             //a roll: started now, stopped first thing next turn (it completes at the turn change)
    const MOVE_COST_LEFT_RUNNING = 40.0;    //a pivot or roll carried over from last turn and not stopped when it could be
    const MOVE_COST_MISALIGNED = 100.0;     //ending out of alignment without a gravitic drive: no turn or speed change at all until it
                                            //pivots back - half the ship's value, so a ship that starts its move misaligned realigns at
                                            //the first chance (self-play, game 4796: at 12 an Omega weaved 17 turns off the map, a
                                            //realigned plan ending a hex further out). A pivot started THIS turn pays it times the share
                                            //of the ship expected to survive (never below the share of combat value it has left): the
                                            //user's "desperate" swing of the guns, by a damaged ship that expects to die
    const MOVE_VALUE_TURN_FREED = 14.0;     //a roll that lets the ship turn the way it wants next turn (towards its quarry) when its
                                            //side thrusters could not: the roll swaps which side's thrusters push that turn
    /* An extended turn begun (stage T5f-5, Hard): the turn is made first thing next turn, which is how its next-turn terms are judged;
       this much more (in hexes, as the other manoeuvres) for what it binds next turn to - the thrust still owed, and no speed change. */
    const MOVE_COST_EXTENDED_TURN = 1.0;

    /* Flights, stage T4 (FlightDecider): where a fighter flight goes, among the legal plans ShipMover
       finds for it. Scores are in "value removed" - expected damage times what a point of the unit
       hit is worth (its value over the damage that finishes it), as FireDecider's worth. */
    const FLIGHT_PLANS = 250;               //plans ShipMover is asked for, per flight
    /* What the enemy's guns could do to the flight where it ends, against what it does. 0.75 as for ships (user, 2026-11-19,
       game 4750: "fighter flights appear to be behaving far too cautiously" - an Aurora flight moving LAST retreated instead of
       parking 3-5 hexes off a G'Quan; it priced every gun of every enemy whatever its arc, and only the out-of-reach Frazi
       counted as a target). Enemies that have moved now fire from their arcs only; the flight is priced at the jinking it
       would really use. */
    const FLIGHT_DANGER_WEIGHT = 0.75;      //stage T5f: by level now - AIConfig::SKILLS 'flightDanger' (this is Easy's and legacy's)
    const FLIGHT_JINK_PLACES = 12;          //Hard prices every jinking level (skill 'jink' = 'any') on this many of its best places
    const FLIGHT_COVERAGE_RANGE = 4;        //the coverage search (stage T5f-2) looks at the hexes within this many of each target in reach
    /* ...asks ShipMover::enumerateToward for up to FLIGHT_COVERAGE_PLANS plans to each such place (so it looks at that many x
       ShipMover::GOAL_EXPANSIONS movers, 160), the best-scored places first, and gives up after FLIGHT_COVERAGE_TRIES x the level's
       'coverageGoals' places. It was 2 plans and 3x until the user's notes on game 5826 (2026-11-22): an Aurora moving LAST at speed
       9, heading away from all three enemies, needed a wide loop to come back to them; it asked 12 places and reached none (80 movers
       were too few for the loop; and on a board like it with the enemy facing the flight, the 12 best-scored places all lay beyond
       the enemy, facing back - a second turn-about no plan has) and flew on to 9-11 hexes off with no shot (score 17; places scoring
       150-194 were within reach). Measured (scratch flightsim.php, 5826 and four boards like it): 20 plans x 3 still failed one
       board and cost 0.95-1.0 s a Normal decision; 4 x 6 reached a good place on all of them at 0.59-0.60 s (HEAD 0.42). The search
       also stops asking once the flight's search time is spent (FlightDecider::coverageSearch). */
    const FLIGHT_COVERAGE_PLANS = 4;
    const FLIGHT_COVERAGE_TRIES = 6;
    //Stage T9, M11: the aimed search's plans for a flight going home to reload (to its carrier's hex - FlightDecider::dockRows)
    const FLIGHT_HOME_PLANS = 8;
    const FLIGHT_SUPPORT_RANGE = 4;         //Hard's mutual support (skill 'support', T5f-3): within this many hexes of another flight is together
    const FLIGHT_INTERCEPT_WEIGHT = 1.5;    //an enemy flight near the bot's ships is worth this much more to a flight that can shoot fighters - a priority, no longer the only target
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
    /* ...but a slow weapon whose target is drawing away (FireDecider::holdSlow: a turn on, the same shot is worse) settles for
       less - never below the user's line for a slow gun, though. Same layout as above. Stage T6a, the user's notes on game 4774
       (2026-11-21): a flat 15 here let two Heavy Lasers fire at 20% and 24% - "lower hit chance odds than a 4-turn weapon should
       normally fire" - against the ruling of 2026-11-15 ("1-33% for a 3 or 4 turn recharge would be unwise"). */
    const FIRE_MIN_CHANCE_PARTING = array(2 => 20, 3 => 34);
    /* PULSE weapons (user ruling 2026-11-19): one roll, one more pulse for every `grouping` it beats the need by, so their
       damage grows much faster than the hit chance (a Heavy Pulse expects ~0.5 pulses at 34%, ~1.0 at 54%). A SLOW pulse
       weapon's floors are this many points higher: Medium Pulse (2 turns) 35%, Heavy Pulse (3 turns) 44%. A pulse gun that
       loads in one turn loses nothing by firing and keeps FIRE_MIN_CHANCE. */
    const FIRE_PULSE_PREMIUM = 10;
    /* Holding a slow weapon for next turn (FireDecider::holdSlow). The same target is priced again
       where both units will be if they fly straight on; the weapon waits when
       now < next x FIRE_HOLD_CONFIDENCE x (1 - 1 / loading time) - a turn of waiting costs a
       1-turn gun everything and a 4-turn gun a quarter of a shot. */
    const FIRE_HOLD_CONFIDENCE = 0.8;       //what next turn's shot is taken to be worth against this turn's certain one (the target may turn away, the gun may be lost)
    const FIRE_MIN_CHANCE_EDGE = 20;        //...for a shot from exactly between two of the target's sections, which the page may price up to 15 lower (AIView::onEdge)
    const FIRE_FOCUS = 0.5;                 //how much a target already being shot at draws the next gun (0 = spread freely)
    /* CONCENTRATION (user, 2026-11-19: "it is more effective to concentrate a fleet's heavy firepower to try and take down an
       enemy ship rather than spread damage"; game 4750 turn 4 put 30 weapons on all 3 enemies). A HEAVY gun - one that takes
       more than a turn to load, or does FIRE_HEAVY_DAMAGE a shot - is drawn to the target already booked by this much
       instead; light guns keep FIRE_FOCUS, free to pick off fighters or add to the kill. */
    const FIRE_FOCUS_HEAVY = 2.5;
    const FIRE_HEAVY_DAMAGE = 15;
    const FIRE_OVERKILL = 1.5;              //planned damage on one target stops counting past this multiple of what it has left
    const FIRE_THREAT_WEIGHT = 0.5;         //how much the most dangerous enemy is preferred over a harmless one of equal worth
    /* The interception reserve. The server intercepts with every loaded gun that has an intercept
       rating, was given no order and loads in one turn (Firing::automateIntercept). So a gun is
       "held back" simply by not being given an order.
       Stage T6a, the user's notes on game 4774 (2026-11-21): "Human players almost always opt to fire offensively with 1 turn
       weapon instead of saving them for intercept if the odds are 15% or greater" - and only fire that CAN be intercepted is a
       reason to hold one at all ("Lasers are uninterceptable, and these types of weapons should not factor into what's likely
       incoming fire"); "where Defensive and Offensive priorities are relatively balanced, choose offence". Turn 3 had held six
       Particle Beams and two Light Pulses with 25% shots at a Va'Kar under the old flat 30%. So a gun whose best shot is under
       FIRE_INTERCEPTOR_MIN_CHANCE stays on defence only while interceptable enemy fire can reach its unit this Fire phase (loaded
       guns in arc and range, ballistics launched at it): one interceptor for each such order it could really stop (a shot from
       inside its arc), the unit's interceptors with no offensive shot at all covering what they can first (they are on defence
       anyway); every other gun fires (FireDecider::holdBack). */
    const FIRE_INTERCEPTOR_MIN_CHANCE = 5; //an interceptor with a shot at least this good always fires it

    /* Ballistics, stage T4 (BallisticDecider): launched in Initial Orders, before anybody has
       moved and before the enemy's EW is known. */
    //The rounds a magazine-fed missile rack is given orders with: plain missiles that do damage to the unit they are aimed at.
    //Every other round (Interceptor, Chaff, Jammer, Homing, Stealth, Starburst, Multiwarhead, Kinetic, HARM, Antimine, Piercing,
    //Dropout, Dummy) has rules of its own and is left in the magazine - an Interceptor is still used by the server on defence.
    const BALLISTIC_ROUNDS = array('Basic', 'LongRange', 'Heavy', 'Flash', 'Antifighter', 'D-Light', 'Dogfight');
    const BALLISTIC_MIN_CHANCE = 35;        //no launch below this: a round is spent for good, a torpedo's tube for its whole reload
    const BALLISTIC_MIN_CHANCE_FLIGHT = 50; //...at a flight: one fighter is a poor return on a missile
    const BALLISTIC_RESERVE = 0.0;          //share of a magazine never launched (0 = fire it dry; levels may raise it, stage T8)
    /* The BALLISTIC TORPEDO (stage T9, M8 - the user, 2026-11-23: "2-3 torpedoes at one or two ships, or 3-6 at a fighter flight (it
       targets separate fighters), backed by 1 or 2 OEW. Easy and Normal may simply fire 1-3 a turn at one target, Hard can consider
       splitting a bit more"). A launcher holds up to six and gains one a turn. At a ship its torpedoes are ONE volley - one roll, one
       interception, and 1 + one more for every 20% the roll beats the need by land (AIView::isTorpedo) - so each torpedo added to a
       volley adds less; BallisticDecider::torpedoes adds them one at a time while the one added keeps the launch floor
       (BALLISTIC_MIN_CHANCE(_FLIGHT) - of what it would do unintercepted). The most one launcher sends at ships in a turn: */
    const TORPEDO_SHIP_VOLLEY = 3;

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
    /* CLOSE ENGAGEMENT (user ruling 2026-11-19; game 4750 turn 5 put 16 of 16 points into OEW, 0 DEW, at 1-4 hexes): "once
       ships are in closer engagement range, it's common for them to mainly use soft locks and retain a decent amount of EW
       for DEW to protect from return fire". An enemy within EW_CLOSE_RANGE gets a soft lock at most (EW_SOFT_LOCK), and a
       ship with an enemy SHIP that close keeps at least EW_CLOSE_MIN_DEW of its points in DEW. And (the user's notes on game
       5826, 2026-11-22: "both Omegas should have soft-locked the Gquan at these ranges") every enemy SHIP that close which a
       loaded gun of the ship could reach gets at least one point (the skill 'closeLocks'). */
    const EW_CLOSE_RANGE = 10;
    const EW_CLOSE_MIN_DEW = 0.5;
    /* MEDIUM RANGE (the user's ruling on game 5826, 2026-11-22: "At range 10-20 hexes I think we should not go full EW with Normal or
       Hard CPU players, unless the danger of return fire is very low. 50% EW max lock would be more appropriate"). An enemy beyond
       EW_CLOSE_RANGE and within EW_MEDIUM_RANGE gets a lock of at most EW_MEDIUM_LOCK_SHARE of the ship's points - unless no enemy gun
       or launcher could reach the ship this turn at all, both closing as fast as they can (PowerDecider::reaches). Skill 'mediumLocks'. */
    const EW_MEDIUM_RANGE = 20;
    const EW_MEDIUM_LOCK_SHARE = 0.5;
    /* CCEW (user ruling 2026-11-19): "one point of CCEW is often used as a deterrent from even some distance away to prevent
       fighters from getting too close (or to give the option to punish them if they do). Greater amounts than 2 or 3 are
       generally only seen when the opponent's force is weighted towards fighters, or by vessels that have a lot of light
       anti-fighter weaponry." A ship with a gun that can fire at fighters puts up 1 point whenever an enemy flight could be
       within CCEW's 10 hexes at this turn's Fire phase (10 + the flight's speed); more only while it gains, and never past
       EW_CCEW_CAP unless the enemy's visible value is EW_FIGHTER_HEAVY or more in flights, or at least half of the ship's
       guns are anti-fighter guns (fire control better against fighters than against capital ships: a Twin Array 6/5/4
       is one, a Standard Particle Beam 4/4/4 is not). */
    const EW_CCEW_REACH = 10;
    const EW_CCEW_CAP = 2;
    const EW_FIGHTER_HEAVY = 0.33;

    //What a seat may be set to. Levels and personalities do nothing before stage T5 / T8; the
    //seat stores them from the start so that games created now keep their setting.
    const LEVELS = array('easy', 'normal', 'hard');
    const DEFAULT_LEVEL = 'normal';
    const PERSONALITIES = array('balanced');
    const DEFAULT_PERSONALITY = 'balanced';

    /* THE READINESS LIST (plan section 7, stage T7 - the user, 2026-11-21: "Hide Factions not cleared for CPU use in gamelobby
       Faction Picker ... just show EA and Raiders atm and add more as we tackle Stage T7 in time"). The factions a CPU player may
       field, spelt as their ship classes spell $faction. The lobby offers only these for a CPU slot (its `cpuFactions` key,
       Manager::addCpuSlots), leaves any other unit out of a saved fleet loaded into one, and the server refuses any other unit when
       the slot is readied (Manager::submitFleetForCpuSlot - the user's ruling of 2026-11-21). A faction joins when it meets section
       7: every hull clean in the ShipMover pair, no blocking system without a handler, self-play against EA with no AI defect.
       Both were proven by directory (EA, raiders) - the other factions those directories hold (EA's defences and early ships,
       Civilians, Custom Ships) are not on it.
       Stage T9 (the user, 2026-11-23: "Once a faction is available (either because all it's ships are playable by ai or because we've
       hidden the ones that aren't) add it to READY_FACTIONS"): the Major factions through the plan 10.10 gate, their unusable units hidden
       by CPU_MISSING_SYSTEMS. */
    const READY_FACTIONS = array('Earth Alliance', 'Raiders', 'Dilgar Imperium', 'Minbari Federation', 'Narn Regime', 'Centauri Republic',
        'Centauri Republic (WotCR)', 'Earth Alliance (Early)', 'Orieni Imperium');

    /* Why a CPU player cannot field $unit (a ship or a blueprint), or null when it can: its faction is not on READY_FACTIONS, or it is
       a kind of unit ShipMover never plots for (ShipMover::unsupportedHullReason - "Hide any ships that are currently listed in
       unsupportedReason()", the user, 2026-11-21). ⚠️ MIRROR: gamelobby.js gamedata.cpuUnitBlock asks the same of the lobby's
       blueprints; a drift shows as a refusal when the slot is readied, never as a CPU fleet the bot cannot fly. */
    public static function cpuUnitBlock($unit)
    {
        if (!in_array($unit->faction, self::READY_FACTIONS, true)) return $unit->faction . ' is not a CPU faction yet';
        $hull = ShipMover::unsupportedHullReason($unit);
        return ($hull !== null) ? $hull : self::missingSystemBlock($unit);
    }

    /* MISSING SYSTEMS (stage T9 - the user, 2026-11-23, on breaching pods: "Hide for now, we will add Marines later. Start a list of
       missing systems for AI in Tactical AI Plan so we can tackle them one-by-one in future and unhide these units for ai use" - the
       list is plan 10.10 "Missing systems"). Systems, by their `name`, that the AI does not use yet: name => array(why, what it hides).
       A CPU player may not field
         - with 'flight': a flight whose craft carry it - a boarding craft (breaching pods, assault shuttles, armed or not);
         - with 'unit': ANY unit carrying it, whatever else it carries (stage T9, M9 - the Molecular Slicer, the user, 2026-11-24: "a
           very complex Ancient weapon, add it to CPU_MISSING_SYSTEMS to hide the EA ships with it for now" - the Omega Epsilon, whose
           other guns would otherwise keep it in);
         - and with either, a unit that carries weapons and none the AI uses: every one of them on this list (the Ramming Attack and
           the jump engine are not counted - a unit with no other weapon is not hidden by this, a designed rammer included).
       Take a line out when the AI learns its system: the units come back by themselves.
       ⚠️ MIRROR PAIR with gamelobby.js CPU_MISSING_SYSTEMS / cpuMissingSystemBlock (the Store marks its rows once, when it builds them,
       so the page keeps its own copy of the list): change one, change both. A drift shows as a refusal when the slot is readied. */
    const CPU_MISSING_SYSTEMS = array(
        'Marines' => array('Marines', 'flight'),                    //boarding - the user, 2026-11-23: "we will add Marines later"
        //(the Antiproton Defender's line came out at stage T9's M3, 2026-11-23: AIView::PORTED_SPECIAL - the Raiders' Jia is back)
        //M9 - every Molecular Slicer (they split by default over a whole hit chart; the Shadows' and customs' too)
        'MolecularSlicerBeamL' => array('Molecular Slicer', 'unit'),
        'MolecularSlicerBeamM' => array('Molecular Slicer', 'unit'),
        'MolecularSlicerBeamH' => array('Molecular Slicer', 'unit'),
        'MolecularSlicerBeamA' => array('Molecular Slicer', 'unit'),
    );

    //Why a CPU player cannot field $unit for a system the AI does not use yet (CPU_MISSING_SYSTEMS), or null.
    public static function missingSystemBlock($unit)
    {
        $weapons = 0;   //weapons counted (not the Ramming Attack or the jump engine)
        $unused = 0;    //of them, on the list
        $why = array();
        foreach ($unit->systems as $system) {
            foreach (($system instanceof Fighter) ? $system->systems : array($system) as $mount) {
                $name = (string)$mount->name;
                $entry = isset(self::CPU_MISSING_SYSTEMS[$name]) ? self::CPU_MISSING_SYSTEMS[$name] : null;
                if ($entry !== null && $entry[1] === 'flight' && $unit instanceof FighterFlight) return 'a boarding craft: the AI does not use ' . $entry[0] . ' yet';
                if ($entry !== null && $entry[1] === 'unit') return 'carries a ' . $entry[0] . ': the AI does not use it yet';
                if (!($mount instanceof Weapon) || $name === 'RammingAttack' || $name === 'jumpEngine') continue;
                $weapons++;
                if ($entry === null) continue;
                $unused++;
                $why[$entry[0]] = true;
            }
        }
        return ($weapons > 0 && $unused == $weapons) ? 'no weapon the AI uses yet (' . implode(', ', array_keys($why)) . ')' : null;
    }

    /* LEVELS (stage T5f - plan 5.8, built from the user's notes on games 4750 and 4774; the split was accepted 2026-11-19).
       A level is a set of player SKILLS, and a decider asks AIView::skill() for the one it needs - never the level's name.
       'legacy' is not a seat's choice (AISeats takes only LEVELS): it is the Normal bot as it stood at stage T5e, which the
       ladder harness plays each change against (plan section 11, "a frozen previous AI version"). Every change of stage T5f
       goes behind a skill here, or that measure is lost.
         sectionHealth  a ship's health is its primary structure plus the section a hit goes through, not the primary alone
                        (user ruling 2026-11-19, game 4774: the primary alone priced a flight's shots at a ship twice too high
                        and left five Gatlings unfired at a Va'Kar "dead on paper"; Easy keeps that miscount)
         ccewDeterrent  1 point of CCEW whenever an enemy flight may come within 10 hexes (4750 note 2) - Easy's too since the
                        user's ruling on game 5826 ("Easy should just follow the same CCEW rule as Normal")
         closeLocks     a soft lock (1 point, before the search adds any more) on every enemy SHIP within EW_CLOSE_RANGE that a
                        loaded gun could reach, nearest first, while DEW keeps its close-range floor (the user's notes on game
                        5826: the split is priced with every gun at the one enemy it does most to, arcs unknown before Movement,
                        so the second ship up close never seemed to need a lock - 13 Omega guns then fired at the G'Quan with
                        none). Easy's simpleEW never gets this far; legacy stays as it was
         mediumLocks    at 10-20 hexes no lock over half the points unless no return fire could reach the ship (the user's ruling
                        on game 5826 - see EW_MEDIUM_RANGE). Easy and legacy as they were
         noFlightOEW    no OEW on a fighter flight - CCEW covers them - unless the enemy has no ship left and the flight is more
                        than 10 hexes off, out of CCEW's reach (the user's ruling on game 5826: "As a rule OEW should never be used
                        on fighter flights, CCEW is generally enough by itself"); Easy as well ("and not put OEW on fighters ever").
                        Legacy as it was
         simpleEW       half the points offensive, half DEW - nothing weighed (5.8, Easy): the CCEW deterrent first, the rest OEW on
                        the nearest enemy that may take a lock (noFlightOEW), or CCEW when there is none - since 5826 (it was the
                        whole half as OEW on the nearest enemy, a flight included)
         focus          the share of FIRE_FOCUS / FIRE_FOCUS_HEAVY used (Easy: "no focus-fire bonus")
         heavyFocus     heavy guns drawn to a booked target harder than light ones (4750 note 5)
         pulsePremium   a slow pulse weapon's floors FIRE_PULSE_PREMIUM higher (4750 note 7)
         shortening     turn shortening (4750 note 6: "Normal and Hard ... by default really (easy opponents can skip)")
         unmovedDanger  what every gun of an enemy yet to move counts for (4750 note 9; Easy keeps the T5a half)
         overspeed      the share of MOVE_COST_OVERSPEED paid (4750 note 4; Easy half)
         ballisticFloor launches held to BALLISTIC_MIN_CHANCE(_FLIGHT); without it any chance an order is worth (5.8, Easy)
         flightDanger   what a flight's own expected losses count against the damage it deals (4774 - see the rulings)
         jink           'max': a place is priced with no jinking and with all the thrust allows; 'any': every level between
         manoeuvres     pivots and rolls it may START (T5e): 'none', 'swap' (only the roll that frees next turn's turn), 'all'
         noise          score noise for movement, the standard deviation in hexes of the unit's own value (5.8: Easy "score
                        noise high"), drawn from the seat's seed - a game stays reproducible
         coverageGoals  how many of the places near its targets the enemy covers least a flight goes looking for beyond the
                        plainest-first search (T5f-2, 4774: "Hard fighters could also find hexes where there is little or no
                        light weapon coverage ... Normal mode could do a simpler search for this positioning too")
         pileOn         flights work together (T5f-3, user ruling 2026-11-19: "a mix of pile onto a damaged target and mutual
                        support depending on their initiative"): a target's worth to a flight x (1 + this x the share of its
                        value already lost), and the fleet's focus target (below) x (1 + this / 2)
         support        ...and the mutual support: hexes of the flight's own value for each hex beyond FLIGHT_SUPPORT_RANGE of
                        the nearest other flight of the bot, while the enemy has flights - full while they have yet to move
                        (they could fall on a flight caught alone), a quarter once they have (a flight moving after them
                        knows where they are: "higher rolling ini makes more likely to move after enemy fighters, and therefore
                        attack them")
         fleetFocus     the fleet's heavy guns put on one chosen target in Movement (4750 note 5): a heavy gun's fire at the
                        target MoveDecider chooses counts x (1 + this)
         bestReply      the one-ply look-ahead (5.8, Hard: "assume each enemy's best reply"): an enemy yet to move flies on or
                        closes as near as its speed allows, whichever is worse for the ship - since T5f-6 priced on BOTH sides,
                        its fire at the ship and the ship's at it where it would end (ShipDecider::worseReply). OFF at every level:
                        the T5f-3 version (its fire only) made Hard stand off (6-2 fell to 4-4 on EA, 2-5 on Brakiri); the
                        both-sided one lost too in the T5f-6 ladder (41% on EA, 50% on Brakiri, against 53% / 59% without)
         shipDanger     what a ship's own expected losses count against the damage it deals (MOVE_DANGER_WEIGHT, the T5a "best
                        trade" ruling) - 0.75 at every level. The T5f-6 ladder (mirror fleets, 32-game rounds, plan 10.7): Hard at
                        1.0 loses clearly (EA 41%, Brakiri 29%); 0.45, 0.6 and 0.75 cannot be told apart (54%, 56%, 55% over
                        64-128 games each - one 0.45 round's 66% on EA did not repeat), so the T5a value stands
         powerBoost     power management (T5f-4, the user's 4750 note 10: "switch off light weapons out of arc/range on early
                        turns to boost Scanner or Engine; back on as the enemy nears" - Hard only, plan 5.8): one-turn guns that
                        can reach nothing this turn, and that no enemy shot needs for interception, are switched off and the power
                        (with any the reactor has spare) buys boosts - the scanner first when there is a fight this turn, else the
                        engine (PowerDecider::boost)
         extendedTurns  extended turns (T5f-5, plan 5.8: "Hard - yes, where possible"; user 2026-11-19): the movement search
                        may BEGIN one (ShipMover::allowExtendedTurns) - never one whose remainder next turn's engine could not
                        pay; the turn owed is completed first thing next turn at every level (the page offers nothing else)
         forecast       where an enemy SHIP yet to move this turn will end (T5f-7; the user, 2026-11-20: "both Hard and Normal [to]
                        have some level of movement predictions (to a simpler extent e.g. Normal might only consider that a ship
                        might turn once) to anticipate Enemy movement in the way players would" - only the ships that move after
                        the bot's, or with it under simultaneous movement: the others are where they ended). 'none': straight on, as
                        before; 'turn': straight on or ONE turn, whichever brings most of its fire on the bot's units; 'full': the
                        same measure over a wider set - those plans and EnemyForecast::FULL_PLANS of the enemy's own (speed changes,
                        slips, two manoeuvres). AIView::atFireTime then stands it there (EnemyForecast). Not the enemy's TRADE: the
                        T5f-9 audit found that the worst predictor of all (plan 10.7)
         interceptorBoost  Interceptors switched to offensive mode (T7; the user, 2026-11-21: "only a Hard opponent boosts some
                        Interceptors when facing a lot of enemy fighters, Easy and Normal just skip this mechanic"). A boosted
                        Interceptor (free) gets fire control against fighters for the turn and loses its intercept rating; without the
                        boost its fire control is null against everything, so neither a player nor the bot can aim it
                        (PowerDecider::interceptors has the rule). Only Interceptors whose arc faces a flight are boosted (T7b-2,
                        the user: "prioritise Interceptors likely to have arcs")
         ram            last-resort ramming under the Desperate rule (T6d, built at T8 - the user's ruling of 2026-11-21: Normal and
                        Hard, Easy never): see RAM_PRIMARY_SHARE and Posture::rammer
         splitShots     a split-shot weapon (the Twin Array family) may divide its guns between targets (stage T9, enabler E1 - the
                        user, 2026-11-23: "Easy and Normal CPUs should just ignore the split mode and use their default firing mode.
                        Hard could consider whether there's any advantage in damage potential by splitting"): each gun is offered to
                        the greedy pick on its own, and a weapon whose guns all go to one target is ordered in its plain mode, as
                        Normal orders it (FireDecider::splitGuns / mergeSplits)
         aoeSpread      deployment spread out against an enemy carrying an area-effect weapon (stage T9 - the user, 2026-11-23: "Normal and
                        Hard CPU should recognise when energy mines ... are present and deploy in a more spread out way"): units AOE_SPACING
                        hexes apart where the zone has room (DeployDecider, FleetDeployer's 'spread'); Easy and legacy as they were
         torpedoTargets      how many units one Ballistic Torpedo launcher may divide its torpedoes between in a turn (stage T9, M8 - the user,
                        2026-11-23: "Easy and Normal may simply fire 1-3 a turn at one target, Hard can consider splitting a bit more"):
                        BallisticDecider::torpedoes, which sends the second unit torpedoes only where they are worth more than more at the first
         torpedoFlightVolley the most torpedoes one launcher sends at a FLIGHT in a turn ("3-6 at a fighter flight"; Easy and Normal "1-3") -
                        never more than the flight has craft. At ships TORPEDO_SHIP_VOLLEY, every level
         gravityNets    Gravity Nets fired in Pre-Firing (stage T9, M6 - the user, 2026-11-23/24: "Use OEW to judge this, e.g. bring an
                        enemy ship into heavy weapon arcs of friendly ships that have a lock, or move enemy ship so it no longer has heavy
                        weapon arcs on ships it has OEW lock on"; also to turn a damaged side to the fleet's guns, or push a ship of many
                        light, range-hungry guns as far off as the net allows): GravityNetDecider. No level split was ruled - on at every
                        level; legacy (= the T5e Normal) off, as it was
         pulsarCharge   a ship with Pulsar Mines (the Narn G'Karith) values ending its move within their 2 hexes and arc of enemy flights
                        that move AFTER it - they start their own move in range and are attacked (stage T9, M7 - the user: "charging into
                        enemy fighter units when the G'Karith moves first, since it will then trigger when the fighters take their movement
                        as they start in its range"): ShipDecider::pulsarFire. On at every level; legacy off
         netAllies      Gravity Nets at FRIENDLY units too - the bot's own and its teammates' (the user, 2026-11-24: "a HARD CPU player will
                        consider whether moving allies is advantageous and attempt to do so. Easy and Normal can skip this"): a friend put
                        where its fire is more, or the enemy's fire at the side less, by the same trade (GravityNetDecider). Hard only */
    const SKILLS = array(
        'easy' => array('sectionHealth' => false, 'ccewDeterrent' => true, 'closeLocks' => false, 'mediumLocks' => false, 'noFlightOEW' => true, 'simpleEW' => true, 'focus' => 0.0, 'heavyFocus' => false,
            'pulsePremium' => false, 'shortening' => false, 'unmovedDanger' => 0.5, 'overspeed' => 0.5, 'ballisticFloor' => false,
            'flightDanger' => 0.75, 'jink' => 'max', 'manoeuvres' => 'none', 'noise' => 10.0, 'coverageGoals' => 0,
            'pileOn' => 0.0, 'support' => 0.0, 'fleetFocus' => 0.0, 'bestReply' => false, 'shipDanger' => 0.75, 'powerBoost' => false, 'extendedTurns' => false, 'forecast' => 'none',
            'interceptorBoost' => false, 'ram' => false, 'splitShots' => false, 'aoeSpread' => false, 'torpedoTargets' => 1, 'torpedoFlightVolley' => 3, 'gravityNets' => true, 'pulsarCharge' => true, 'netAllies' => false),
        'normal' => array('sectionHealth' => true, 'ccewDeterrent' => true, 'closeLocks' => true, 'mediumLocks' => true, 'noFlightOEW' => true, 'simpleEW' => false, 'focus' => 1.0, 'heavyFocus' => true,
            'pulsePremium' => true, 'shortening' => true, 'unmovedDanger' => 1.0, 'overspeed' => 1.0, 'ballisticFloor' => true,
            'flightDanger' => 1.5, 'jink' => 'max', 'manoeuvres' => 'swap', 'noise' => 0.0, 'coverageGoals' => 4,
            'pileOn' => 0.0, 'support' => 0.0, 'fleetFocus' => 0.0, 'bestReply' => false, 'shipDanger' => 0.75, 'powerBoost' => false, 'extendedTurns' => false, 'forecast' => 'turn',
            'interceptorBoost' => false, 'ram' => true, 'splitShots' => false, 'aoeSpread' => true, 'torpedoTargets' => 1, 'torpedoFlightVolley' => 3, 'gravityNets' => true, 'pulsarCharge' => true, 'netAllies' => false),
        'hard' => array('sectionHealth' => true, 'ccewDeterrent' => true, 'closeLocks' => true, 'mediumLocks' => true, 'noFlightOEW' => true, 'simpleEW' => false, 'focus' => 1.0, 'heavyFocus' => true,
            'pulsePremium' => true, 'shortening' => true, 'unmovedDanger' => 1.0, 'overspeed' => 1.0, 'ballisticFloor' => true,
            'flightDanger' => 3.0, 'jink' => 'any', 'manoeuvres' => 'all', 'noise' => 0.0, 'coverageGoals' => 12,
            'pileOn' => 1.0, 'support' => 2.0, 'fleetFocus' => 0.5, 'bestReply' => false, 'shipDanger' => 0.75, 'powerBoost' => true, 'extendedTurns' => true, 'forecast' => 'full',
            'interceptorBoost' => true, 'ram' => true, 'splitShots' => true, 'aoeSpread' => true, 'torpedoTargets' => 2, 'torpedoFlightVolley' => 6, 'gravityNets' => true, 'pulsarCharge' => true, 'netAllies' => true),
        'legacy' => array('sectionHealth' => false, 'ccewDeterrent' => true, 'closeLocks' => false, 'mediumLocks' => false, 'noFlightOEW' => false, 'simpleEW' => false, 'focus' => 1.0, 'heavyFocus' => true,
            'pulsePremium' => true, 'shortening' => true, 'unmovedDanger' => 1.0, 'overspeed' => 1.0, 'ballisticFloor' => true,
            'flightDanger' => 0.75, 'jink' => 'max', 'manoeuvres' => 'swap', 'noise' => 0.0, 'coverageGoals' => 0,
            'pileOn' => 0.0, 'support' => 0.0, 'fleetFocus' => 0.0, 'bestReply' => false, 'shipDanger' => 0.75, 'powerBoost' => false, 'extendedTurns' => false, 'forecast' => 'none',
            'interceptorBoost' => false, 'ram' => false, 'splitShots' => false, 'aoeSpread' => false, 'torpedoTargets' => 1, 'torpedoFlightVolley' => 3, 'gravityNets' => false, 'pulsarCharge' => false, 'netAllies' => false)
    );

    /* POWER MANAGEMENT (skill 'powerBoost', stage T5f-4). A gun "reaches" an enemy when, with the units as close as both could
       come this turn - each at its speed plus all the acceleration its thrust buys, and this many hexes more for what cannot be
       seen (an engine boost) - the real hit formula still gives it a chance with every EW point of its ship locked on (the
       best case, so a gun goes off only when it surely cannot fire). A one-turn gun switched off at turn N is loaded again at
       N+1 when switched back on (Weapon::calculateLoading), so "back on as the enemy nears" is simply this turn's test. */
    const POWER_REACH_SLACK = 2;

    /* SURRENDER (stage T6b, the user's ruling of 2026-11-21 - plan 10.8): a side gives up only when none of its units can damage
       any enemy - "no usable weapon, or nothing can get within reach" (Posture::canFight, the reach above) - for this many turns
       in a row, while it is behind on fleet value, and never before this turn. Otherwise it fights on to the last unit. */
    const SURRENDER_TURNS_UNABLE = 2;
    const SURRENDER_EARLIEST_TURN = 5;

    /* JUMPING OUT (stage T6c, the user 2026-11-21: "A damaged Jump Engine trying to open a jump point can also destroy the ship, so if
       more than 50% damage is taken the Jump should not be attempted"). The share of a drive's boxes lost is also its failure chance
       (JumpEngine::getJumpFailureChance). JumpDecider has the rest of the ruling. */
    const JUMP_MAX_DRIVE_DAMAGE = 0.5;
    /* "EXPECTED TO BE DESTROYED" ONLY FOR A SHIP ALREADY SO DAMAGED - the user: ships jump out when "so damaged they can easily be
       destroyed ... this is however quite rare and most ships fight until they are destroyed". Its PRIMARY structure (the boxes whose
       loss destroys it) must be down to this share. The T6 self-play batches (2026-11-21): without it ships left on turn 3 at 88% of
       their value (the enemy fleet's whole fire, every gun at that one ship, is more than most ships' primary and one section); with
       the share asked of primary + the section facing the enemy instead, ships left with a whole primary and one section lost - a
       ship that can turn a fresh side. 0.5 was the implementer's pick; the user set 0.15 (2026-11-21, after committing T6). */
    const JUMP_DAMAGED_SHARE = 0.15;
    /* "CANNOT GET BACK INTO THE FIGHT" (the second case) - nothing of the ship could come within reach of an enemy within this many turns,
       both flying their hardest each turn (Posture::canFight). Stage T9, M2 (the user's ruling, 2026-11-24 - found by the Hunter-Killer
       self-play): it was this turn alone, and on turn 1 of a 42x30 game slow short-ranged carriers 34 hexes from the enemy (Orieni
       Prophet, Vigilant) jumped out at once. A ship stranded far off the map still leaves. */
    const JUMP_REACH_TURNS = 3;

    /* LAST-RESORT RAMMING (stage T6d, built at T8 - the user's ruling of 2026-11-21): a ship of a team the Desperate rule covers rams
       when it has no usable weapon, or its PRIMARY structure is down to this share of its boxes - Normal and Hard (skill 'ram'), only
       at an enemy ship it can reach this turn (Posture::rammer / ramValue; ShipDecider plans into its hex, FireDecider orders the ram). */
    const RAM_PRIMARY_SHARE = 0.25;

    /* Stage T9 (the user, 2026-11-23: units DESIGNED to ram "can and should always Ram"): such a unit - the Dilgar Delegor (Posture::
       rammer: RammingAttack::$designedToRam) - PURSUES. Its closing term (MOVE_CLOSING_PER_HEX) counts RAM_PURSUIT times, and the enemy's
       fire at it RAM_DANGER_SHARE of the level's shipDanger: out of reach of a ram, a Delegor 10-18 hexes from an Omega turned away and
       ran 12-20 hexes off - the "best trade" of its two Scatter Pulsars - and a rammer that turns away never rams. */
    const RAM_PURSUIT = 10.0;
    const RAM_DANGER_SHARE = 0.25;
    /* Stage T9, M2 - the Hunter-Killers (the user, 2026-11-23: "launch them immediately and they will usually target larger ships in enemy
       fleet due to their high damage potential, working their way down to HCV and MCV if no larger ships are available"; 2026-11-24: on
       down to LCVs, and fighters once no ship is left). A size class is "available" while one of its units is within this many turns of the
       flight, both flying their hardest (Posture::ramQuarry); a larger class further off than that gives way to a nearer smaller one. The
       turns are the implementer's reading. A Hunter-Killer flight pursues as a designed rammer ship does (RAM_PURSUIT, RAM_DANGER_SHARE). */
    const RAM_QUARRY_TURNS = 2;
    /* ...and the plans it asks ShipMover for beyond the plainest-first search (FlightDecider::ramSearch - enumerateToward): for each of its
       RAM_AIMED_QUARRY nearest quarry, RAM_AIMED_PLANS that end in its hex at the Fire phase, or as far along the way as it can go */
    const RAM_AIMED_PLANS = 6;
    const RAM_AIMED_QUARRY = 2;

    /* Stage T9 (the user, 2026-11-23: "Normal and Hard CPU should recognise when energy mines (in a way that can be extended later to cover
       other AoE weapons) are present and deploy in a more spread out way to minimise potential damage from this AoE weapon"). AOE_WEAPONS:
       the weapon classes (and their subclasses) that hit every unit in and round the hex they land in - an Energy Mine does its full damage
       to each unit in that hex (each craft of a flight) and a third of it to each unit in the six round it. Against an enemy carrying one,
       the skill 'aoeSpread' deploys units AOE_SPACING hexes apart where the zone has room (FleetDeployer's 'spread'): two units three
       hexes apart can never share a mine. Add a class here to make another weapon count. */
    const AOE_WEAPONS = array('EnergyMine', 'LightEnergyMine');
    const AOE_SPACING = 3;
    /* ...and aiming them (AoEDecider, the user's same note: "choose a target (usually a fighter flight for Energy Mines) and take a guess
       where they will move, then target that area"): a mine lands on its hex AOE_LANDS of the time (AoE::fire: 75 of 100 - 15 more stray
       up to six hexes, 10 fizzle). AOE_SPREAD: where an enemy ENDS against its straight-on guess - on the guessed hex, on one of the six
       next to it (all alike), on one of the twelve two off (all alike); the rest of the time further off. A flight turns on any hex, a ship
       less. Spacing (the user, 2026-11-23: "space out the mines a little within 1 or 2 hexes of each other to cover more space"): a second
       mine is valued by what it adds over the first, so it goes next to it, not on it. No mine where one of the bot's own units, where it
       stands or flying straight on, is within AOE_OWN_MARGIN hexes of the blast (it hits the hex and the six round it, friend or foe).
       The odds are guesses of the implementer's. */
    const AOE_LANDS = 0.75;
    const AOE_SPREAD = array('flight' => array(0.35, 0.3, 0.2), 'ship' => array(0.5, 0.25, 0.1));
    const AOE_OWN_MARGIN = 2;

    /* Stage T9, M6 - GRAVITY NETS (GravityNetDecider, the skill 'gravityNets'). A net is fired only where the move it buys is worth at
       least GRAVNET_MIN_GAIN of fleet value this Fire phase - the trade (damage the bot's units should deal less the level's shipDanger
       weight times what the moved unit should deal them, as ShipDecider weighs a trade) with the target where the net puts it, against where it stands, times the
       net's chance to hit (and for a second net at the same unit, only what it adds: the server moves a unit by ONE net, the hitting one
       with the largest Move Distance). A net reloads in two turns, so a trivial move now can cost a good one next turn; the figure is the
       implementer's. GRAVNET_DECISION_SECONDS: the CPU time pricing the hexes may take (GravityNetDecider::$budget; 0 = none, the
       harnesses) - the targets nearest the nets first, so a cut leaves the farthest unpriced. */
    const GRAVNET_MIN_GAIN = 10.0;
    //...and never at a chance under this (the user, 2026-11-24: "The minimum hit chance I think should be 15%") - a net spent on a long shot is not there next turn
    const GRAVNET_MIN_CHANCE = 15;
    const GRAVNET_DECISION_SECONDS = 2.0;

    /* A SIDE DOWN TO ITS FIGHTERS (stage T6g, the user 2026-11-21: "tone down AI fighters that stay too far away when they are the
       only unit left for the AI player, as this will frustrate players. E.g. can't go three turns without firing a shot, and maybe
       don't run as far away from the enemy" - self-play game 5749: two Thunderbolts jinked 10 hexes off the winner for 20 turns).
       While the bot's only units that can still fight are flights (Posture::lastStand), a flight that has fired nothing for this many
       turns less one must get a shot off this turn (Posture::mustShoot; FlightDecider flies it where a gun of it reaches FireDecider's
       floor, jinking counted, or as near as it can get; FireDecider keeps none of its guns back to intercept and fires every one it
       has a shot for, into the interceptors too), and no flight flies further from the enemy than it must (the closing cost always). */
    const FLIGHT_MAX_IDLE_TURNS = 4;
    /* ...and where it can, it goes for a shot of at least this chance at the Fire phase as the bot foresees it, its jinking counted:
       enemies that move after it end elsewhere than foreseen, and a shot planned at the 5% floor was then lost - the first T6g
       self-play batch's forced flights jinked their chances down to the floor and went 3-7 turns without a shot. */
    const FLIGHT_MUST_CHANCE = 20;
    /* ...and closing in it does not build up more speed than the target's this much over: each point beyond costs as a hex short of
       it. Without it the second T6g batch's flights, always ending as near as they could, ran up to speed 16-17 after a Hyperion
       doing 2, overshot it turn after turn far off the map, and the game reached the turn cap with them 7-47 hexes off. */
    const FLIGHT_CHASE_MARGIN = 6;
    /* "Don't run as far away from the enemy": while the side is down to its fighters, a flight ends no further than this many hexes
       from the nearest enemy it closes on whenever it can (before any other term - as a flight that must fire goes for its shot).
       A cost of a few points a hex was not enough: in the T6g batches the flights, on the turns they were not made to fire, flew
       28-42 hexes off a lone Hyperion to be safe from its guns, and every forced turn had that gap to make up. */
    const FLIGHT_LEASH = 11;

    //A seat's level (DBManager::getCpuSeats row): one SKILLS knows - a harness may seat 'legacy' - else the default.
    public static function seatLevel(array $seat)
    {
        $level = isset($seat['cpu']['level']) ? $seat['cpu']['level'] : null;
        return (is_string($level) && isset(self::SKILLS[$level])) ? $level : self::DEFAULT_LEVEL;
    }

    //A seat's seed, which every random choice of its bot is drawn from (plan 5.8: a game is reproducible).
    public static function seatSeed(array $seat)
    {
        return isset($seat['cpu']['seed']) ? (int)$seat['cpu']['seed'] : 0;
    }

    /* The CPU seconds this process has used (user + system) - the clock of a decision's budget (MOVE_DECISION_SECONDS, stage
       T5f-8): the same work however busy the machine is. Wall time where the platform has no getrusage(). */
    public static function cpuSeconds()
    {
        $usage = function_exists('getrusage') ? getrusage() : false;
        if (!is_array($usage) || !isset($usage['ru_utime.tv_sec'])) return microtime(true);
        return $usage['ru_utime.tv_sec'] + $usage['ru_utime.tv_usec'] / 1e6 + $usage['ru_stime.tv_sec'] + $usage['ru_stime.tv_usec'] / 1e6;
    }
}
