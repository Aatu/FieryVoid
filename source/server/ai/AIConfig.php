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
    const FIRE_MIN_CHANCE_PARTING = 15;     //...but a slow weapon whose target is drawing away takes what it can get, down to this
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
    /* CLOSE ENGAGEMENT (user ruling 2026-11-19; game 4750 turn 5 put 16 of 16 points into OEW, 0 DEW, at 1-4 hexes): "once
       ships are in closer engagement range, it's common for them to mainly use soft locks and retain a decent amount of EW
       for DEW to protect from return fire". An enemy within EW_CLOSE_RANGE gets a soft lock at most (EW_SOFT_LOCK), and a
       ship with an enemy SHIP that close keeps at least EW_CLOSE_MIN_DEW of its points in DEW. */
    const EW_CLOSE_RANGE = 10;
    const EW_CLOSE_MIN_DEW = 0.5;
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

    /* LEVELS (stage T5f - plan 5.8, built from the user's notes on games 4750 and 4774; the split was accepted 2026-11-19).
       A level is a set of player SKILLS, and a decider asks AIView::skill() for the one it needs - never the level's name.
       'legacy' is not a seat's choice (AISeats takes only LEVELS): it is the Normal bot as it stood at stage T5e, which the
       ladder harness plays each change against (plan section 11, "a frozen previous AI version"). Every change of stage T5f
       goes behind a skill here, or that measure is lost.
         sectionHealth  a ship's health is its primary structure plus the section a hit goes through, not the primary alone
                        (user ruling 2026-11-19, game 4774: the primary alone priced a flight's shots at a ship twice too high
                        and left five Gatlings unfired at a Va'Kar "dead on paper"; Easy keeps that miscount)
         ccewDeterrent  1 point of CCEW whenever an enemy flight may come within 10 hexes (4750 note 2)
         simpleEW       half the points OEW on the nearest enemy, half DEW - nothing else (5.8, Easy)
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
                        T5f-9 audit found that the worst predictor of all (plan 10.7) */
    const SKILLS = array(
        'easy' => array('sectionHealth' => false, 'ccewDeterrent' => false, 'simpleEW' => true, 'focus' => 0.0, 'heavyFocus' => false,
            'pulsePremium' => false, 'shortening' => false, 'unmovedDanger' => 0.5, 'overspeed' => 0.5, 'ballisticFloor' => false,
            'flightDanger' => 0.75, 'jink' => 'max', 'manoeuvres' => 'none', 'noise' => 10.0, 'coverageGoals' => 0,
            'pileOn' => 0.0, 'support' => 0.0, 'fleetFocus' => 0.0, 'bestReply' => false, 'shipDanger' => 0.75, 'powerBoost' => false, 'extendedTurns' => false, 'forecast' => 'none'),
        'normal' => array('sectionHealth' => true, 'ccewDeterrent' => true, 'simpleEW' => false, 'focus' => 1.0, 'heavyFocus' => true,
            'pulsePremium' => true, 'shortening' => true, 'unmovedDanger' => 1.0, 'overspeed' => 1.0, 'ballisticFloor' => true,
            'flightDanger' => 1.5, 'jink' => 'max', 'manoeuvres' => 'swap', 'noise' => 0.0, 'coverageGoals' => 4,
            'pileOn' => 0.0, 'support' => 0.0, 'fleetFocus' => 0.0, 'bestReply' => false, 'shipDanger' => 0.75, 'powerBoost' => false, 'extendedTurns' => false, 'forecast' => 'turn'),
        'hard' => array('sectionHealth' => true, 'ccewDeterrent' => true, 'simpleEW' => false, 'focus' => 1.0, 'heavyFocus' => true,
            'pulsePremium' => true, 'shortening' => true, 'unmovedDanger' => 1.0, 'overspeed' => 1.0, 'ballisticFloor' => true,
            'flightDanger' => 3.0, 'jink' => 'any', 'manoeuvres' => 'all', 'noise' => 0.0, 'coverageGoals' => 12,
            'pileOn' => 1.0, 'support' => 2.0, 'fleetFocus' => 0.5, 'bestReply' => false, 'shipDanger' => 0.75, 'powerBoost' => true, 'extendedTurns' => true, 'forecast' => 'full'),
        'legacy' => array('sectionHealth' => false, 'ccewDeterrent' => true, 'simpleEW' => false, 'focus' => 1.0, 'heavyFocus' => true,
            'pulsePremium' => true, 'shortening' => true, 'unmovedDanger' => 1.0, 'overspeed' => 1.0, 'ballisticFloor' => true,
            'flightDanger' => 0.75, 'jink' => 'max', 'manoeuvres' => 'swap', 'noise' => 0.0, 'coverageGoals' => 0,
            'pileOn' => 0.0, 'support' => 0.0, 'fleetFocus' => 0.0, 'bestReply' => false, 'shipDanger' => 0.75, 'powerBoost' => false, 'extendedTurns' => false, 'forecast' => 'none')
    );

    /* POWER MANAGEMENT (skill 'powerBoost', stage T5f-4). A gun "reaches" an enemy when, with the units as close as both could
       come this turn - each at its speed plus all the acceleration its thrust buys, and this many hexes more for what cannot be
       seen (an engine boost) - the real hit formula still gives it a chance with every EW point of its ship locked on (the
       best case, so a gun goes off only when it surely cannot fire). A one-turn gun switched off at turn N is loaded again at
       N+1 when switched back on (Weapon::calculateLoading), so "back on as the enemy nears" is simply this turn's test. */
    const POWER_REACH_SLACK = 2;

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
