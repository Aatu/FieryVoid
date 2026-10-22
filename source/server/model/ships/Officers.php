<?php
/* OFFICERS (OFFICERS_PLAN.md, repo root) - expert officers carried by a unit.

   An officer is an enhancement with a POST: the one system on the unit he lives in. While the post
   stands, his bonus applies. Two ways out of action, both permanent and both effective from the
   NEXT turn (D3):
     - DISABLED: the post was destroyed, by its own boxes or by its Structure block. Re-derived from
       the complete damage history at every load (the server loads every turn's damage rows), so
       nothing is stored. Self Repair reviving the post changes nothing: "undestroyed" is ignored.
     - KILLED: the post took damage through its armour and its critical roll came to 20 or more,
       modifiers included - the roll the post makes anyway, or one made for him when the post has
       just been destroyed. A dice result, so it IS stored: one OffKilled individual note per death,
       intercepted in DBManager's bulk note loop and never handed to any system (§4.4).

   THE ONE RUNTIME LIST. $ship->getOfficers() holds array('id', 'post', 'out', 'how') per officer:
   'post' is the post's system id, 'out' the turn he left action (null while he has not), 'how'
   'disabled' or 'killed' - or 'moved' for a fighter officer who left this flight row with his fighter
   (followFighter; stored as one OffMoved note). He serves on turn T while out === null or out >= T.
   Every effect, the payload and (Stage 2) the insignia read that list and nothing else (§7, "two
   tracks drift").

   ⚠️ POST-side ships (Manager::getShipsFromJSON) never run onConstructed, so their list is empty:
   nothing in a phase's process() may ask about officers ([[arch_post_side_ship_reconstruction]]).

   Track 1 (fixed-post officers, ship-level rows): the Helmsman (Stage 1); the Scanner, Technician,
   Geneticist and C&C Matter Weapons Expert (Stage 3); the fighter officers (Stage 4): Dogfighter,
   Missileer, Evader and the Expert Motivator, which was a fighter enhancement until then and keeps its
   old id; the Engineer and Scavenger (Stage 5: an end-of-turn repair pass, and the Scavenger's
   once-per-game save, stored as one OffSaved note); the Navigator (Stage 6: the ship's movement costs);
   and the Jump Officer (Stage 7: the jump drive's range, hold, failure chance, exit scatter and arrival
   initiative); and the fighter officer Expert Coordinator (Stage 9: cheaper combat pivots, no pivot penalty).
   Track 2 (Stage 8, PLACED officers): the Expert Gunner and the weapon-mounted Matter Weapons Expert,
   whose post is the weapon the player picks - bought as per-system rows (tac_sys_enhancements) through
   Enhancements::$systemEnhancementRegistry, and resolved into the same runtime list (pickPlacedPosts).

   Stage 10: a weapon's faster reload (the weapon-mounted Matter Weapons Expert always, the Gunner once),
   the Gunner's once-per-battle Piercing mode and called shot (abilities - tied to HIM, not to a weapon,
   and stored as OffUsed notes), and MOVING officers between posts in Initial Orders (resolveTransfers:
   OffTransfer notes, replayed at every load; he serves nowhere on the turn he moves). A moved officer's
   entry carries his `stays` (each post and the turns he held it) - so a kill note, a destroyed post or a
   spent ability is matched to the officer who was THERE at that turn.

   Stage 11: the Expert Laser Technician - a flat damage bonus on every laser, and lasers the player may
   BOOST with power for more (the generic boost buttons; capped at half the roll, one pool a turn).
   Stage 12: the Expert Plasma Scientist - +1 damage on plasma weapons bar the light ones (Fusers +2), the
   first two hexes of every plasma shot's range damage ignored, Plasma Batteries holding one point more (and
   tougher), Plasma Webs reaching a hex further.
   Stage 13: the Expert Security Officer - +5 initiative, -2 on his C&C's critical rolls, and every enemy boarding roll
   against his ship moved 2 in its favour (boardingShift).
   Stage 14: the Expert Religious Leader - +10 initiative and +1 damage on every hit; the first officer whose LOSS has an
   effect (`lost`: his crew's frenzy).
   Stage 15: the Religious Leader's placed twin (OFF_RELIP, any primary non-weapon system - the Matter Weapons Expert's
   pattern), so the lobby can post him anywhere; the lobby's own Move is client-side (officers.lobbyMove), writing rows.
   Stage 16: the Expert Surge Officer (Ipsha) - +1 damage on EM weapons, reactor power by size, his ship's EM hardening (the
   tabletop rules, rebuilt the same stage in WeaponEM) eased further, and Spark Fields 1 weaker against him.
   Stage 17: the Expert Ion Technician (Cascor) - +1 per die (or +1) on ion weapons, stronger Rad Cannons, and ion guns the player
   may BOOST with thrust for +1d10+1 a level (the generic boost, priced in thrust) - a boosted gun that does not fire discharges.
   Stage 18: the Expert Pilot - the first officer bought as a COUNT (`multi`): up to four on one flight, one in each of its first
   crafts, each +1 OB and a free jinking level beyond the limit (client, movement.js), and Spark Fields 1 weaker on his flight on a
   turn it neither jinks nor fires (pilotSparkRelief). Several pilots are still the flight's ONE officer (D9).
   Stage 19: the Expert Targeter (Raiders, Llort) - the first officer posted in a STRUCTURE block: +4 on called shots by the weapons
   he covers (targeterShotBonus) - his block's, or on a Medium ship one side's of its hit chart (sideReach) - and a side switch that is
   a move to his own post (`switchTo`). His kill roll counts only that turn's damage to the block (blockKillRoll).
   Stage 20: the Expert ELINT Officer - his array +1, -2 on sensor criticals, every ELINT range 25% longer (elintRange).
   Stage 21: the Expert Tailgunner - a fighter officer for flights with a TAIL GUN (Weapon::$tailGun): no penalty for firing it
   with the forward guns, or +5% with it where it has none (Weapon::getTailGunMod).
   Stage 22: the Expert Turret Officer (Vree) - placed in a turret-mounted weapon, aiding every weapon on that TURRET (Weapon::getTurretMates):
   +1 fire control, -1 on criticals, -2 on the turret jam roll, and a turret that fires no shot SHELTERED from all but called shots
   (openShelter). One to a turret (`perTurret`).
   Stage 23: the Expert Anticipator - initiative rolled twice, the higher kept (initiativeRoll); one turn in the Firing phase, as a
   fighter's combat pivot (Movement::ANTICIPATED_TURN); and a damage roll of two or more dice one die larger, its lowest dropped
   (Dice::$extraDie).
   Stage 24: the Breaching Expert - a fighter officer for BREACHING PODS (isBreachingPod), priced off his ONE pod whatever the
   flight's size (`unscaled`): the flight +1 thrust, his pod's Marines Elite (eliteMarines), and his pod's first capture party
   three attack rolls on its first turn aboard (spendCaptureAssault, captureRolls). Fleet-wide, at most 20% of a fleet's pods - the
   lobby's Fleet Correctness Report (gamelobby.js).
   Stage 25: the Expert Political Officer (Centauri capital ships and larger) - his Influence buys the ship an armed shuttle, one of
   four experts at no cost (freeExpertId) and five free Hardened Armour (pricePoliticalArmour), whatever the player leaves unchosen
   picked at random as the fleet is committed (placePoliticalPicks); and once per battle a stirring speech in the Firing phase, told in
   the combat log (speechLog), +30 initiative on the next turn.
   Stage 26: the Expert Software Engineer (Hyach) - in the Hyach Computer: one more bonus fire control point, kept until the Computer
   is destroyed and holdable to the Firing phase (HyachComputer::settleFiringBFCP); the first point of every hit on the Computer
   ignored (computerArmour); a stealth ship's signature masked (maskSignature); and one Defence, Sensor, Targeting or Weapon
   Specialist used a second time (specialistReuse).

   Which FACTIONS may buy an officer, and what some of them pay, is one table: $factionRules below. */
class Officers{

	const ID_PREFIX = 'OFF_';
	const KILL_NOTE = 'OffKilled'; //tac_individual_notes.notekey, varchar(40)
	const MOVE_NOTE = 'OffMoved';  //a fighter officer left this row with his fighter (followFighter)
	const SAVE_NOTE = 'OffSaved';  //the Expert Scavenger has spent his once-per-game save (scavengerSave)
	const KILL_ROLL = 20;          //"a '20' or greater (including any modifications)"
	const CNC_REPAIR_ROLL = 16;    //Engineer and Scavenger: "C&C criticals ... repaired only on a roll of 16 or greater on a single d20"
	const SCAVENGER_BOXES = 4;     //"Each turn, he can repair four destroyed boxes anywhere on the ship"
	const SAVE_POINTS = 6;         //"up to six points of damage can be ignored in any single volley"
	const TECH_POWER = 2;          //Technician: "provides an extra 2 points of power"
	const TECH_CRIT_MOD = -2;      //Technician: "a -2 bonus on any critical hit die roll involving the reactor"
	const NAV_CANNOT = 99;         //Navigator: a cost of 99 or more is a hull's "cannot" (a base's turn, a 999 pivot) - left alone
	const JUMP_RANGE = 1;          //Jump Officer: "open a jump gate up to 5 hexes away (not 4 hexes)"
	const JUMP_EXTRA_TURNS = 1;    //Jump Officer: "can hold it open for one turn longer than normal"
	const JUMP_FAILURE_CUT = 20;   //Jump Officer: "reduces the chance of a catastrophic explosion by 20%" (50% -> 30%)
	const JUMP_SCATTER_SHIFT = -1; //Jump Officer: "a -1 shift to the vortex scatter chart"
	const JUMP_INI_RELIEF = 20;    //Jump Officer: "any initiative penalty produced by scatter is reduced by 20"
	const COMBAT_PIVOT_FULL = 3;   //Coordinator: "a full 180" - in hex sides, 3 x 60 degrees
	const TRANSFER_NOTE = 'OffTransfer'; //an officer the player moved to another post (Stage 10): value "id:to", on his old post
	const USE_NOTE = 'OffUsed';          //an Expert Gunner spent a once-per-battle ability (Stage 10): value "id:ability", on his post
	const GUNNER_CALLED_BONUS = 3;       //Gunner: "a called shot at +3" (+15%)
	const PIERCE_PENALTY = 4;            //Piercing's usual -4 fire control: an added mode carries it, a native one is spared it once
	const PIERCE_PRIORITY = 2;           //an added Piercing mode resolves early, as the Battle Laser's own does
	const MAX_TRANSFER_CLAIMS = 8;       //a POST asking for more moves than any ship has movable officers is not read past this
	const LASER_BOOST_COST = 1;          //Laser Technician: "each point of power provides +1 extra damage"
	const LASER_BOOST_CAP = 2;           //...and "cannot be increased by more than 50%": at most the roll / 2, rounded down
	const PLASMA_RANGE_RELIEF = 2;       //Plasma Scientist: "ignore the first two hexes traveled when reducing damage"
	const PLASMA_BATTERY_BONUS = 1;      //...: "All plasma batteries can store an extra point of power"
	const PLASMA_BATTERY_CRIT_MOD = -3;  //...: "and receive a -3 bonus on any critical hit roll"
	const PLASMA_WEB_RANGE = 1;          //...: "Plasma webs receive a +1 bonus to range, even if suffering from a critical hit"
	const PLASMA_WEB_MODE = 2;           //the web's Anti-Fighter (offensive) mode - the one with a range; Defensive's 100 is "unlimited"
	const SECURITY_INI = 5;              //Security Officer: "a +1 bonus to the ship's initiative" - tabletop, so +5 in FV (QX1)
	const SECURITY_CNC_CRIT_MOD = -2;    //...: "and a -2 to any critical hit rolls against the C&C"
	const SECURITY_BOARDING_SHIFT = 2;   //...: "shift all die rolls by up to 2 in any direction desired by the defending player"
	const RELIGIOUS_INI = 10;            //Religious Leader: "an automatic +10 to the ship's initiative" - FV units (user ruling 2026-10-17)
	const RELIGIOUS_DAMAGE = 1;          //...: "each weapon the ship fires scores +1 damage per volley" - per hit (user ruling)
	const FRENZY_SENSORS = 1;            //...if he is wounded, captured or killed: "the ship receives +1 to its sensors"
	const FRENZY_PROFILE = 1;            //...: "and +5 to its defense profiles" - FV's displayed 5, one profile point (user ruling)
	const SURGE_DAMAGE = 1;              //Surge Officer: "All electromagnetic-category weapons add +1 damage" - per hit; a combined Surge Cannon shot +1 per cannon
	const SURGE_POWER_CAPITAL = 2;       //...: "Capital ships and larger receive +2 points of power"
	const SURGE_POWER_MEDIUM = 1;        //...: "while medium ships and HCVs get +1 extra power. LCVs and smaller units gain no extra benefits"
	const SURGE_HARDENING = 1;           //...: EM criticals "-3 penalty (not -2)", and a forced shutdown resisted on "a 1 or 2" (not 1) - WeaponEM
	const SURGE_SPARK_RELIEF = 1;        //...: "Any spark field affecting the ship reduces its damage by 1 point"
	const ION_DIE_BONUS = 1;             //Ion Technician: "All ion-based weapons ... score +1 damage per die, but no die can be increased above its maximum roll"
	const ION_FLAT_BONUS = 1;            //...: "Weapons scoring a flat amount of damage, like the dual ion bolter or ion torpedo, add +1 only"
	const ION_BOOST_THRUST = 3;          //...: "Three points of thrust produce an extra 1d10+1 damage"
	const ION_BOOST_MAX = 1;             //...one level per gun a turn (user ruling 2026-10-18, revised the same day) - officers.ION_BOOST_MAX (JS)
	const ION_BOOST_DIE = 10;            //...the boost's 1d10
	const ION_BOOST_FLAT = 1;            //...and its +1
	const RAD_STRUCTURE_BOXES = 12;      //...: "Rad cannons score 12 damage on structure instead of 10"
	const RAD_CNC_CRIT_MOD = 4;          //...: "If they hit C&C, the critical roll is at +4 on the die instead of the listed +2"
	const PILOT_OB = 1;                  //Expert Pilot: "+1 to the flight's offensive bonus" (+5% in FV)
	const PILOT_SPARK_RELIEF = 1;        //...: "will also reduce [spark field] damage by 1 point if he forgoes all his other benefits"
	const TARGETER_CALLED_BONUS = 4;     //Expert Targeter: "called shots have a +4 bonus to hit" - the -8 stays ("It does not cancel ...")
	const ELINT_SENSORS = 1;             //Expert ELINT Officer: "The sensor rating of his ship is increased by + 1"
	const ELINT_CRIT_MOD = -2;           //...: "All critical hit rolls against the sensors are reduced by 2"
	const ELINT_RANGE_PCT = 25;          //...: "The range of all ELINT functions is increased by 25% (round fractions of 0.5 or more up)"
	const ELINT_BLANKET_BALLISTIC = 3;   //...: blanket protection's total "divided by 3 (not 4) for purposes of ballistic weapons only"
	const TAILGUNNER_BONUS = 1;          //Expert Tailgunner: on a tail gun with no penalty, "+5% to hit with that gun only"
	const TURRET_CRIT_MOD = -1;          //Expert Turret Officer: "When rolling for critical hits against turret-mounted weapons, subtract 1"
	const TURRET_JAM_RELIEF = 2;         //...: "When rolling for the special turret jam critical, subtract 2 from the die roll"
	const BREACH_THRUST = 1;             //Breaching Expert: "The pod receives +1 thrust" - the flight's, as a flight moves as one
	const BREACH_CAPTURE_ROLLS = 3;      //...: "the pod's contingent gets three attack rolls on the first turn of combat"
	const BREACH_PARTY_KEY = 'expert';   //...the capture party he sent: a key in its CaptureShip critical's param (never sent - ShipSystem::stripForJson)
	const SPEECH_INITIATIVE = 30;        //Expert Political Officer: "This provides the ship with +30 to its initiative on that turn" - FV units, as the Quartermaster's -30 (QX1)
	const POLITICAL_ARMOUR_SYSTEMS = 5;  //...: "+1 to their armor values. The owner must select 5 different systems ... and cannot select structure blocks"
	const SPEECH_DAMAGE_CLASS = 'PoliticalSpeech'; //...his speech's combat log line (speechLog) - combatLog.js heads it "SPEECH:"
	const SOFTWARE_BFCP = 1;             //Expert Software Engineer: "The Hyach computer provides one extra bonus fire control point (BFCP)"
	const SOFTWARE_ARMOUR = 1;           //...: "ignores the first point of damage in any volley" - an armour point nothing pierces or wears
	const SIGNATURE_MASK_HEXES = 5;      //...: a stealth ship "appear[s] as though it were five hexes further away for detection purposes"
	/* ...: "permits a specialist to use his abilities a second time ... The specialist must be of the software category" - the Hyach
	   Specialists the user named (2026-10-22). ⚠️ MIRROR PAIR with officers.SOFTWARE_SPECIALISTS (JS). */
	private static $softwareSpecialists = array('Defence', 'Sensor', 'Targeting', 'Weapon');
	/* ...: "Select either an expert navigator, expert helmsman, expert engineer, expert scanner and add him to the crew at no cost" - in
	   REGISTRY order, which is the order a tie for the dearest goes by (freeExpertId). ⚠️ MIRROR PAIR with officers.POLITICAL_EXPERTS (JS). */
	private static $politicalExperts = array('OFF_HELM', 'OFF_SCAN', 'OFF_ENG', 'OFF_NAV');
	/* ...: "a stirring speech to his ship's crew, quoting the past glories of the Republic, the honor of serving the Emperor, and other
	   political mumbo-jumbo" - the combat log names one of these at random (speechLog). */
	private static $speechTopics = array(
		'the past glories of the Republic', 'the honour of serving the Emperor', 'the ancient splendour of Centauri Prime',
		'the duty owed to the Great Houses', 'the boundless destiny of the Centauri people', 'the wisdom of the Imperial Court',
		'the conquest of the Xon', 'the ingratitude of the Narn', 'the glory of the Imperial Fleet', 'the favour of the Great Maker',
		'the treachery of rival Houses', 'the burden of Empire', 'the Emperor\'s latest decree', 'the sacrifices of the Orieni War',
		'the necessity of unquestioning loyalty', 'the proper height of a hair crest', 'the virtues of a well-aged brivari',
		'the splendour of the Imperial Palace gardens', 'his own distinguished family', 'the colour purple',
		'the price of spoo', 'the importance of good tailoring' 
	);
	//...a Medium ship's side, looked at from its own bearing (a "TAG:" hit chart entry is arc-filtered): Front, Aft, Left, Right
	private static $sideBearing = array(1 => 0, 2 => 180, 3 => 270, 4 => 90);
	//the fire orders that are NOT the flight firing, for his Spark Field relief: interception uses neither OB nor jinking
	private static $notFiring = array('intercept', 'selfIntercept', 'meteorDefence');

	/* ⭐ FACTION RULES (user request 2026-10-15) - which factions an officer is offered to, and what some
	   of them pay. ONE table, so a faction's officers change here and nowhere else: README.md, "Officers:
	   faction availability and prices", is the how-to for a custom faction. An officer with no entry is
	   offered to every faction at his registry price. D9's exclusions (Ancients, OSATs, mines, terrain,
	   remotely controlled craft) and the hull's own tests (the post, `needs`) apply whatever this says.
	     only   offered ONLY to these factions
	     never  never offered to these factions
	     pct    his price in percent for these factions, in place of the registry's pct (of the hull, or
	            of one craft for a fighter officer); the registry's rounding still applies
	     max    (an officer bought as a count - `multi`) how many one unit of these factions may carry, in place
	            of the registry's `multi`
	   A faction is named by its hulls' $faction string, exactly as the ship files write it - the name the
	   faction has in Fleet Selection. A name that matches no faction simply never applies, so
	   officersHarness checks every name here against the factions that exist.
	   ⚠️ Changing a rule changes the static blueprints' offers: run fvbuild.ps1 -Statics. A saved fleet
	   holding an officer its faction may no longer buy is refunded on reload, and one whose price moved is
	   re-priced (Manager::loadSavedFleet); a running game simply loses the officer at its next load. */
	private static $factionRules = array(
		'OFF_GENE' => array('only' => array('Dilgar Imperium')),
		'OFF_MWE'  => array('only' => array('Grome Autocracy')),
		'OFF_SCAV' => array('pct' => array('Descari Committees' => 15, 'Balosian Underdwellers' => 20, 'Raiders' => 20, 'Llort' => 20)),
		'OFF_JUMP' => array('never' => array('Raiders', 'Belt Alliance')),
		//"5% of the ship's base cost. On non-Yolu ships, the cost is doubled" - the registry's 10, and 5 here
		'OFF_GUN'  => array('pct' => array('Yolu Confederation' => 5)),
		//(the weapon-mounted Matter Weapons Expert is OFF_MWE at another post - `sameAs` - and reads OFF_MWE's line)
		//"10% of the ship's base cost ... Races other than the Hyach pay 50% more" - the registry's 15, and 10 here
		'OFF_LASR' => array('pct' => array('Hyach Gerontocracy' => 10)),
		//"10% of the ship's base cost for Pak'ma'ra ... Any race using plasma weapons can use these officers, but the cost is doubled"
		'OFF_PLAS' => array('pct' => array("Pak'ma'ra Confederacy" => 10)),
		//"5% of the ship's basic value for Earth Alliance (EA) factions ... Non-EA factions increase the cost by 50%" - the registry's 7.5
		'OFF_SECU' => array('pct' => array('Earth Alliance' => 5, 'Earth Alliance (Early)' => 5, 'Earth Alliance (defenses)' => 5)),
		//"Availability: Minbari, Markab, Corillani and Yolu factions" - the Minbari Protectorate not included (user ruling 2026-10-17)
		'OFF_RELI' => array('only' => array('Minbari Federation', 'Markab Theocracy', 'Corillani Theocracy', 'Yolu Confederation')),
		//"Availability: Ipsha only" - not the Civilians' Ipsha-built Cargo Hauler
		'OFF_SURG' => array('only' => array('Ipsha Baronies')),
		//"Availability: Cascor only" - not the Civilians' Cascor-built Calacca and Caracti
		'OFF_ION'  => array('only' => array('Cascor Commonwealth')),
		//"Up to four expert pilots in the same flight are cumulative, except for Raider flights, which can use no more than two"
		'OFF_PILOT' => array('max' => array('Raiders' => 2)),
		//"Cost: 10% of the ship's base cost for Raiders. Availability: Raiders. Llort can also take at double the cost."
		'OFF_TARG' => array('only' => array('Raiders', 'Llort'), 'pct' => array('Llort' => 20)),
		/* "15% of the ship's base value for Abbai factions ... Double the cost for non-Abbai vessels. Not available for races whose
		   current mainline ships do not have at least an 8 sensor rating" - the races are the user's list (2026-10-20): not the
		   Centauri WotCR, the Early Earth Alliance or the Drazi WotCR, and no custom faction for now; the Abbai WotCR are Abbai. */
		'OFF_ELINT' => array('only' => array('Abbai Matriarchate', 'Abbai Matriarchate (WotCR)', 'Centauri Republic', 'Earth Alliance',
			'Narn Regime', 'Minbari Federation', 'Yolu Confederation', 'Torata Regency', 'Gaim Intelligence', 'Drazi Freehold',
			'Vree Conglomerate', 'Dilgar Imperium', 'Brakiri Syndicracy', 'Hyach Gerontocracy', 'Markab Theocracy', 'Corillani Theocracy',
			'Llort', 'Deneth Tribes', 'Minbari Protectorate'),
			'pct' => array('Abbai Matriarchate' => 15, 'Abbai Matriarchate (WotCR)' => 15)),
		//"Availability: Vree only" - not the Custom Ships' Shadow-built Xonn, whose turrets are Vree-style too
		'OFF_TURR' => array('only' => array('Vree Conglomerate')),
		//"10% of the ship's base value for Torata ... Races other than the Torata pay 50% more for this officer" - the registry's 15
		'OFF_ANTI' => array('pct' => array('Torata Regency' => 10)),
		//"25% of the cost of the breaching pod ... Any race may purchase one, but races other than Balosian, Yolu, Narn or Gaim double the
		//cost" - the registry's 50, and 25 here
		'OFF_BRCH' => array('pct' => array('Balosian Underdwellers' => 25, 'Yolu Confederation' => 25, 'Narn Regime' => 25, 'Gaim Intelligence' => 25)),
		//"Availability: ... Centauri Only" - the Wars of the Centauri Republic too (user ruling 2026-10-21)
		'OFF_POL'  => array('only' => array('Centauri Republic', 'Centauri Republic (WotCR)')),
		//"Availability: Hyach only" - and only a Hyach hull has his post, the Computer
		'OFF_SOFT' => array('only' => array('Hyach Gerontocracy')),
	);

	/* One literal per officer (D6), walked in THIS order by apply() (D5).
	     label    the name in the buy dialog, the stored enhname and the kill note's notekey_human
	     post     a key into findPost()
	     pct      price in percent of the hull's blueprint pointCost, rounded up (D8) - of ONE CRAFT for
	              a fighter officer. At most one decimal place (the Security Officer's 7.5 - price)
	     roundDown  (optional) true: pct is rounded DOWN instead, where the officer's own text says so
	     apply    the static method that applies his stat changes while he serves, or null when every
	              rule he carries is asked for elsewhere (isActive)
	     publish  (optional) ship fields his apply moves that every viewer is sent while he serves: a
	              displayed stat is public, only the officer is hidden (user ruling 2026-10-10)
	     needs    (optional) a further test the hull must pass to be offered him, beyond having the post
	   Fighter officers (Stage 4) also carry:
	     flight      true: offered to flights only (and every other officer to ships only). His pct is
	                 of ONE craft, for a standard flight; a flight of another size pays it scaled by its
	                 craft count (flightPrice - user ruling 2026-10-15)
	     perMissile  (optional) points added PER CRAFT for every missile one craft can carry, on top of
	                 that (the Missileer)
	     multi       (optional, Stage 18) bought as a COUNT, up to this many (or $factionRules' `max`), and never
	                 more than the flight has craft: one officer in each of its first crafts, every one of them
	                 applying his effects (the Expert Pilot). Together they are still the flight's one officer.
	     unscaled    (optional, Stage 24) true: his pct of one craft is his price whatever the flight's size - he serves in a single
	                 craft, so a flight of two pays what a flight of one does (the Breaching Expert: "25% of the cost of the breaching pod")
	   Placed officers (Stage 8) carry, instead of a post key:
	     placed      true: the player picks his post, a weapon, in that system's own menu - a per-system row
	                 (Enhancements::$systemEnhancementRegistry), never an enhancementOptions offer
	     hosts       (optional) a further test the weapon must pass to host him (a method taking the system, then the ship)
	     nonWeapon   (optional) true: his posts are systems that are NOT weapons (`hosts` says which) - Stage 15
	     shipLimit   (optional) the method answering how many one ship may carry (default 1, "one of each")
	     sameAs      (optional) the ship-level officer he IS, at another post: his faction rules are that
	                 officer's, and a ship carries one or the other, never both (user ruling 2026-10-16)
	     anyDamage   (optional) true: DISABLED by any damage through armour to his post, KILLED when it is
	                 destroyed - on top of the ordinary kill roll (user ruling 2026-10-16)
	   Stage 10:
	     movable     true: the player may move him to another post in Initial Orders (resolveTransfers). Every
	                 officer sharing his rulesId moves as ONE man: the Matter Weapons Expert at the C&C is OFF_MWE
	                 and in a weapon OFF_MWEW (user ruling 2026-10-16), whichever his new post makes him.
	     reload      true: the weapon he serves in reloads a turn faster after every shot (fasterReload)
	     abilities   true: the Expert Gunner's once-per-battle faster reload, Piercing and called shot
	   Stage 14:
	     lost        (optional) the static method applying what his LOSS does, on every turn after he was disabled
	                 or killed in battle (isLost) - in place of `apply`, which only runs while he serves
	     lostPublish (optional) `publish`'s list for the turns `lost` runs
	   Stage 19:
	     side        (optional) which of a single-block hull's two sides he covers (1 = its first, 2 = its second - hullSides)
	     switchTo    (optional) the officer he becomes when ordered to his OWN post (mayMoveTo): the Expert Targeter's side switch
	   Stage 22:
	     perTurret   (optional) true: one of him to a TURRET (turretKey - a weapon's linked firing group, or the weapon alone), not only
	                 one to a system - at purchase and load (keepPlacedRows) and when he moves (mayMoveTo) */
	private static $registry = array(
		/* Expert Helmsman: +5 initiative (FV units, QX1), wins initiative ties, and -1 on the die he
		   rolls on a meteor swarm's impact chart (user, 2026-10-10). The tie-break is
		   TacGamedata::sortShips; the chart is RammingAttack::resolveMeteors. */
		'OFF_HELM' => array('label' => 'Expert Helmsman', 'post' => 'cnc', 'pct' => 5, 'apply' => 'applyHelmsman',
			'publish' => array('iniativebonus')),
		/* Expert Scanner: "can save an EW point for after Initial Orders, but before firing - the same
		   mechanically as an EW Detector". One more point on top of the detector ladder (CUMULATIVE,
		   user ruling 2026-10-10), anywhere on the map - EW::getDetectorAllowance and its JS twin. */
		'OFF_SCAN' => array('label' => 'Expert Scanner', 'post' => 'scanner', 'pct' => 7, 'apply' => null),
		/* Expert Technician - ALL THREE of his skills, cumulative (the full rules text, user 2026-10-15;
		   Stage 3 had built the best of three from a summary):
		     Improved Power      +2 reactor power, and -2 on every critical roll the reactor makes;
		     Efficient Transfers +1 power while the player has switched off at least one system with a
		                         power requirement, once a turn however many - CLIENT ONLY, power.js
		                         (officers.efficientTransferBonus): the power balance has no server twin;
		     Circuit Breakers    a third, rounded down, of the powerReq of EVERY system destroyed (user
		                         ruling 2026-10-15: added together, not only the largest). */
		'OFF_TECH' => array('label' => 'Expert Technician', 'post' => 'reactor', 'pct' => 9, 'apply' => 'applyTechnician'),
		/* Expert Geneticist: -1 to hit with this ship's own weapons, +2 to be hit (both profiles, the
		   Poor Crew and ProfileIncreased convention), and "enemies can ram this ship" - a tooltip line
		   only, as FV leaves ramming to the players. Offered on every hull with a C&C (user ruling
		   2026-10-10: "scenario only" is by agreement, not a gate). */
		'OFF_GENE' => array('label' => 'Expert Geneticist', 'post' => 'cnc', 'pct' => 5, 'apply' => 'applyGeneticist',
			'publish' => array('forwardDefense', 'sideDefense', 'toHitBonus')),
		/* Matter Weapons Expert (the C&C variant; the weapon-mounted one is Stage 8): +1 fire control and
		   +1 damage on every Matter-class weapon, per firing mode. Only for hulls carrying one. He may move
		   into a Matter weapon - becoming OFF_MWEW there - and back (Stage 10, `movable`). */
		'OFF_MWE'  => array('label' => 'Matter Weapons Expert', 'post' => 'cnc', 'pct' => 10, 'apply' => 'applyMatterExpert',
			'needs' => 'hasMatterWeapon', 'movable' => true),
		/* Expert Engineer (Stage 5): repairs one critical at the end of each turn - runRepairs, after Self
		   Repair. Never one the ship has not yet suffered for a full turn; a C&C critical only on a d20 of
		   16+, and a failed roll ends his repairs for the turn. */
		'OFF_ENG'  => array('label' => 'Expert Engineer', 'post' => 'engine', 'pct' => 5, 'apply' => null),
		/* Expert Scavenger (Stage 5): at the end of each turn erases one critical as the Engineer does, or,
		   with none left to erase, repairs 4 boxes (runRepairs); and ONCE per game ignores up to 6 points
		   of a hit that would destroy the C&C, a reactor, a scanner, an engine or a Structure block
		   (scavengerSave). User rulings 2026-10-15; scanners and engines added the same day. */
		'OFF_SCAV' => array('label' => 'Expert Scavenger', 'post' => 'engine', 'pct' => 25, 'apply' => null),
		/* Expert Navigator (Stage 6; rules text and rulings, user 2026-10-15): turn cost one level down
		   the rulebook's ladder; pivot and roll costs -1 (never below 1, and a cost of 0 stays 0); accel/
		   decel cost x0.75, its fractions kept until a turn's speed changes are added up and then rounded
		   up - the client's shipManager.movement.getSpeedChangeCost (Movement::doStuckEngine's
		   floor(thrust / cost) is already exact for it). Turn DELAY is unchanged (ruled). Priced at 7%
		   "dropping fractions", the one officer whose text rounds down. */
		'OFF_NAV'  => array('label' => 'Expert Navigator', 'post' => 'cnc', 'pct' => 7, 'roundDown' => true,
			'apply' => 'applyNavigator', 'publish' => array('turncost', 'pivotcost', 'rollcost', 'accelcost')),
		/* Expert Jump Officer (Stage 7; rules text, user 2026-10-15), in the jump drive, 5%. His drive opens a
		   jump point 1 hex further and holds it 1 turn longer (JumpEngine::applyJumpOfficer, which publishes
		   the drive's range itself); a damaged drive's failure chance is 20 lower (getJumpFailureChance). An
		   exit his ship opens rolls 1 lower on the scatter chart and loses 60 degrees of any facing shift, and
		   his own unit's arrival initiative penalty is 20 smaller, never a bonus (exitScatterShift,
		   cancelsFacingShift, arrivalPenaltyRelief). Not for Raiders or the Belt Alliance ($factionRules). */
		'OFF_JUMP' => array('label' => 'Expert Jump Officer', 'post' => 'jump', 'pct' => 5, 'apply' => 'applyJumpOfficer'),
		/* Expert Laser Technician (Stage 11; rules text, user 2026-10-17), in the reactor. Every shot in a Laser mode
		   scores +3 from a Spinal Laser, +2 from a Blast Laser, +1 from any other laser (laserDamageBonus); and the
		   player may put power into any laser in Initial Orders, 1 point = +1 damage with no maximum, but never more
		   than half the roll (laserBoostDamage). Only the laser part of a multi-mode weapon gains. Rulings (user
		   2026-10-17): a laser's boost is ONE pool for its turn, shared by all its hits; a serving Expert Gunner's
		   +1 per die replaces the flat bonus on his laser, while the boost still works there; an opponent sees no
		   boost on these lasers until the game is over (laserForJson); the owner's tooltip shows both. 10% for the
		   Hyach, 15% for everyone else ($factionRules); only for a hull carrying a laser. No stat moves at load:
		   every rule is asked at damage time, and the boost buttons come from the payload. */
		'OFF_LASR' => array('label' => 'Expert Laser Technician', 'post' => 'reactor', 'pct' => 15, 'apply' => null,
			'needs' => 'hasLaserWeapon'),
		/* Expert Plasma Scientist (Stage 12; rules text, user 2026-10-17), in the Engine. Every plasma weapon but the
		   light ones scores +1, a Fuser +2 (plasmaDamageBonus); every plasma shot ignores the first two hexes of its range
		   damage penalty (Weapon::getRangeDamageDistance); every Plasma Battery holds one point more while it stands, and
		   rolls 3 lower for criticals; every Plasma Web's Anti-Fighter range is 1 longer, its range critical included
		   (applyPlasmaScientist). Rulings (user 2026-10-17): "medium or heavy" = every plasma weapon but the light ones
		   ($plasmaClassBonus); a battery still holding its deployment charge starts full at its new capacity; the web's
		   range and the battery's capacity are shown to every viewer (a displayed stat is public). 10% for the
		   Pak'ma'ra, 20% for everyone else ($factionRules); only for a hull carrying a plasma weapon. */
		'OFF_PLAS' => array('label' => 'Expert Plasma Scientist', 'post' => 'engine', 'pct' => 20, 'apply' => 'applyPlasmaScientist',
			'needs' => 'hasPlasmaWeapon'),
		/* Expert Security Officer (Stage 13; rules text, user 2026-10-17), in the C&C. +5 initiative (tabletop +1, QX1) and -2
		   on his C&C's critical rolls (applySecurityOfficer); and "whenever a breaching pod attacks, shift all die rolls by up
		   to 2 in any direction desired by the defending player" - built as a fixed 2 in his ship's favour (the user's note:
		   higher is worse for the boarders on every table), on every boarding roll against his ship (boardingShift). Rulings
		   (user 2026-10-17): the defenders' own Capture rolls get -2 too; every boarding party - breaching pod, Grappling
		   Claw or transporter; the -2 on his own C&C only (a Pak'ma'ra hull has two). 5% for the Earth Alliance, 7.5% for
		   everyone else ($factionRules) - the one price with a decimal place (price). */
		'OFF_SECU' => array('label' => 'Expert Security Officer', 'post' => 'cnc', 'pct' => 7.5, 'apply' => 'applySecurityOfficer',
			'publish' => array('iniativebonus')),
		/* Expert Religious Leader (Stage 14; rules text, user 2026-10-17), at the C&C when bought, and movable to any other
		   primary non-weapon system - where he is OFF_RELIP, his placed twin below (Stage 15). +10 initiative (applyReligiousLeader) and +1 damage on every hit his
		   ship's weapons score (religiousDamageBonus). "If he is wounded, captured or killed" - disabled or killed in battle -
		   his crew's frenzy from the next turn on: initiative +20 instead, +1 sensors, and both profiles +1 (the rules' "+5",
		   in FV's displayed units) - applyReligiousFrenzy. Rulings (user 2026-10-17): FV display units; +1 per HIT (each pulse,
		   each gun's shot, a raking shot once, never per die); the frenzy keeps the +1 damage; Minbari Federation, Markab,
		   Corillani and Yolu only ($factionRules). 15%. */
		'OFF_RELI' => array('label' => 'Expert Religious Leader', 'post' => 'cnc', 'pct' => 15, 'apply' => 'applyReligiousLeader',
			'publish' => array('iniativebonus'), 'lost' => 'applyReligiousFrenzy',
			'lostPublish' => array('iniativebonus', 'forwardDefense', 'sideDefense'), 'movable' => true),
		/* Expert Surge Officer (Stage 16; rules text, user 2026-10-18), in the Mag-Gravitic Reactor. Every hit his ship's
		   Electromagnetic weapons score +1 - a combined Surge Cannon shot +1 per cannon in it, an EM Pulsar +1 per pulse, his own
		   Spark Field too (surgeDamageBonus); the reactor +2 power on a capital ship or larger, +1 on a medium ship or HCV, none
		   smaller (applySurgeOfficer); his ship's EM hardening eased further - EM critical rolls -3 instead of -2, a forced shutdown
		   resisted on 1-2 instead of 1 (surgeHardening, asked by WeaponEM); and any Spark Field's damage on his ship -1
		   (sparkFieldRelief). Rulings (user 2026-10-18): EM hardening rebuilt to the tabletop rules first (WeaponEM); the -3 is once
		   per system per turn, as the -2; his own Spark Field gains the +1. 10%, Ipsha only ($factionRules). */
		'OFF_SURG' => array('label' => 'Expert Surge Officer', 'post' => 'maggrav', 'pct' => 10, 'apply' => 'applySurgeOfficer'),
		/* Expert Ion Technician (Stage 17; rules text, user 2026-10-18), in the Engine. Every ion weapon (the Ion Torpedo included)
		   scores +1 per die, each die capped at its face, or +1 on a hit whose damage is one number (ionDieBonus, ionFlatBonus); his
		   ship's Rad Cannons mark 12 Structure boxes instead of 10, force a critical roll on a Scanner, an Engine or a reactor, and
		   roll a C&C's at +4 instead of +2 (improvesRadCannon); and the player may put 3 thrust into an ion gun whose damage is
		   rolled for +1d10+1 if it hits - the generic boost, priced in thrust (ionForJson, ionBoostDamage) - but a boosted gun that
		   does not fire that turn discharges as if it had (ionDischarged). Rulings (user 2026-10-18): ONE boost level per gun a turn
		   (revised the same day from repeatable); the discharge is built; a sub-reactor counts as the reactor. 10%, Cascor only ($factionRules);
		   only for a hull carrying an ion weapon. No stat moves at load: every rule is asked at damage time. */
		'OFF_ION'  => array('label' => 'Expert Ion Technician', 'post' => 'engine', 'pct' => 10, 'apply' => null,
			'needs' => 'hasIonWeapon'),
		/* Expert ELINT Officer (Stage 20; rules text, user 2026-10-20), in his ship's ELINT array - "ELINT vessel only", so a hull
		   without one is never offered him. His array +1 output, and -2 on the critical rolls of every sensor array on his ship
		   (applyElintOfficer); every ELINT function's range 25% longer, rounded half up (elintRange: 30 -> 38, 50 -> 63, 20 -> 25); and his
		   blanket protection counts a third, not a quarter, against ballistic weapons (ballisticBlanket). Rulings (user 2026-10-20):
		   the ranged functions only - SOEW, SDEW, Blanket, Disruption, Jamming (Detect Stealth and the stealth detection range
		   unchanged); the -2 on every array; the blanket's fractions kept, as FV's quarter keeps them. Both range and blanket reach
		   every viewer (addForJson). 30%, the Abbai 15%, for the races of the user's list only ($factionRules). */
		'OFF_ELINT' => array('label' => 'Expert ELINT Officer', 'post' => 'elint', 'pct' => 30, 'apply' => 'applyElintOfficer'),
		/* Expert Anticipator (Stage 23; rules text, user 2026-10-21), in the C&C. "When rolling for initiative, this officer's ship
		   rolls the die twice. The player then selects the higher one" (initiativeRoll). After moving, with the ship's turn delay
		   complete and the thrust for it unspent, ONE turn in the Firing phase - "not a roll, pivot, snap turn, or other maneuver" -
		   paid through the thrusters as any turn (the user's note: a fighter's combat pivot, with no firing penalty): a turnleft /
		   turnright row marked Movement::ANTICIPATED_TURN, kept by Movement::validateMovement only for his ship (mayAnticipate),
		   hidden from enemies until firing resolves (TacGamedata::hideEnemyCombatPivots). And "when rolling for damage, roll an
		   extra die and drop the lowest ... only on weapons that roll at least two dice" (extraDamageDie - Dice::$extraDie and
		   Dice::keepBest). Rulings (user 2026-10-21): the turn is a combat pivot's - given before the ship declares direct fire, and
		   this turn's fire, his and at him, meets the new facing; on a Gunner's weapon the extra die AND the Gunner's +1 per die.
		   15%, the Torata 10% ($factionRules). No stat moves at load. */
		'OFF_ANTI' => array('label' => 'Expert Anticipator', 'post' => 'cnc', 'pct' => 15, 'apply' => null),
		/* Expert Political Officer (Stage 25; rules text, user 2026-10-21), in the C&C - "Capital ships or larger units only. Centauri
		   Only" (`needs`, $factionRules). "Expert political officers have Influence with a capital 'I'": the ship possesses one armed
		   shuttle - Elite Crew's own retype of a default shuttle as the hangars fill (politicalArmedShuttles, asked by HangarOps); one
		   Expert Navigator, Helmsman, Engineer or Scanner comes at no cost - the dearest of those bought (freeExpertId, the user's note);
		   and +1 armour on 5 different systems, never Structure - five free Hardened Armour (pricePoliticalArmour). What the player
		   leaves unchosen, the server picks at random once, as the fleet is committed (placePoliticalPicks). None of it is lost with
		   him. "Once during each scenario ... a stirring speech ... +30 to its initiative on that turn ... announced before initiative
		   rolls are made": ordered in the Firing phase from his C&C's menu (saveSpeech), told in that turn's combat log (speechLog), and
		   +30 on the next turn's initiative (applyPoliticalOfficer). Rulings (user 2026-10-21): both Centauri factions; the random picks
		   the server's, at the commit; his armed shuttle on top of Elite Crew's; no +30 unless he still serves on the turn it lands.
		   40%. */
		'OFF_POL'  => array('label' => 'Expert Political Officer', 'post' => 'cnc', 'pct' => 40, 'apply' => 'applyPoliticalOfficer',
			'publish' => array('iniativebonus'), 'needs' => 'isCapitalOrLarger'),
		/* Expert Software Engineer (Stage 26; rules text, user 2026-10-22), in the Hyach Computer - "unique to the Hyach", so only a hull
		   with one is offered him. "One extra bonus fire control point (BFCP). This is lost only if the computer is completely destroyed"
		   (applySoftwareEngineer: the Computer's damage takes none of it), and "the player can leave this extra BFCP point unallocated
		   until the Weapons Fire Step" - one point held back from Initial Orders may be placed in the Firing phase
		   (HyachComputer::settleFiringBFCP). "The Hyach Computer ignores the first point of damage in any volley ... an extra point of
		   armor that cannot be damaged, destroyed or bypassed" (computerArmour). "If aboard a stealth unit ... five hexes further away for
		   detection purposes ..., except for fighters, which halve their detection range (round fractions up)" (maskSignature). "Permits
		   a specialist to use his abilities a second time during the scenario ... of the software category ... This only works for one
		   specialist per scenario" (specialistReuse). Rulings (user 2026-10-22): the hold built, and the Computer Specialist rebuilt to
		   its own rules on the same Firing-phase allocation; the per-category cap stays 2; a specialist used on turn N may be used again
		   from turn N+1. 15%, Hyach only ($factionRules). */
		'OFF_SOFT' => array('label' => 'Expert Software Engineer', 'post' => 'computer', 'pct' => 15, 'apply' => 'applySoftwareEngineer'),

		/* PLACED OFFICERS (Stage 8), posted in a weapon the player picks. AFTER every fixed-post officer, so
		   apply() has already raised the C&C Matter Weapons Expert's guns when it reaches them, and before
		   the fighter officers (a flight is never offered one: per-system rows are ships only).
		   One system hosts ONE placed officer (keepPlacedRows). */
		/* Expert Gunner (rules text, user 2026-10-16), on any weapon Gunsights may be bought for (user
		   ruling): +1 to hit - fire control, so not EW - and +1 damage per die, each die capped at its face
		   (Weapon::getFinalDamage, beside Elite Crew's, with which it stacks). "Does not combine any to-hit or
		   damage bonuses with those of other elite officers": on his weapon his own replace the Matter
		   Weapons Expert's (user ruling 2026-10-16). 10%, 5% for the Yolu ($factionRules); at most 1 on a
		   medium ship or smaller, 2 on a heavy, 3 on a capital, 4 on an enormous unit (gunnerLimit).
		   Stage 10 (user rulings 2026-10-16) - ONCE PER BATTLE each, automatic, never two on one shot, and HIS
		   (they go with him to another weapon): a Piercing shot (an added Piercing mode on a single-mode
		   Standard/Raking direct-fire gun, at Piercing's usual -4; on a gun that may already pierce, +4 on its
		   first Piercing shot - addPiercingMode / gunnerShotBonus); +3 on his weapon's first called shot that is
		   not Piercing; and a turn off the reload after the first firing that used neither (fasterReload). He
		   may move to another weapon he could be bought for (movable). */
		'OFF_GUN'  => array('label' => 'Expert Gunner', 'placed' => true, 'pct' => 10, 'apply' => 'applyGunner',
			'shipLimit' => 'gunnerLimit', 'movable' => true, 'abilities' => true),
		/* Matter Weapons Expert, moved "into one specific weapon instead of his normal station": +1 fire
		   control and +1 damage on THAT weapon's Matter modes only, the C&C variant's rule on one gun. The
		   same officer as OFF_MWE (sameAs): Grome only, 10%, and never both (user ruling 2026-10-16). "Disabled
		   if the weapon takes any damage at all (after armor), and automatically killed if the weapon is
		   destroyed" (anyDamage), and the ordinary kill roll besides (user ruling). "Plus its rate of fire +1
		   (never better than once a turn)": a turn off every reload (Stage 10, `reload`). He may move to another
		   Matter weapon or back to the C&C (movable). */
		'OFF_MWEW' => array('label' => 'Matter Weapons Expert', 'placed' => true, 'pct' => 10, 'sameAs' => 'OFF_MWE',
			'hosts' => 'isMatterWeapon', 'apply' => 'applyWeaponMatterExpert', 'anyDamage' => true,
			'movable' => true, 'reload' => true),
		/* The Expert Religious Leader away from his C&C (Stage 15, user request 2026-10-17: "freely place him in any viable
		   system" in Fleet Selection, where moving costs nothing): the same officer as OFF_RELI (sameAs - one or the other,
		   his faction rules and price), posted in any primary non-weapon system but that C&C (isReligiousPost). Bought, or
		   moved to, from that system's own menu; in game he moves between the two ids as the Matter Weapons Expert does
		   (postAs). Every effect is OFF_RELI's. */
		'OFF_RELIP' => array('label' => 'Expert Religious Leader', 'placed' => true, 'pct' => 15, 'sameAs' => 'OFF_RELI',
			'hosts' => 'isReligiousPost', 'nonWeapon' => true, 'apply' => 'applyReligiousLeader', 'publish' => array('iniativebonus'),
			'lost' => 'applyReligiousFrenzy', 'lostPublish' => array('iniativebonus', 'forwardDefense', 'sideDefense'), 'movable' => true),
		/* Expert Targeter (Stage 19; rules text, user 2026-10-20), in a STRUCTURE block: "called shots have a +4 bonus to hit ... only
		   called shots. It does not cancel the usual -8" (targeterShotBonus). "All weapons attached to this block receive the
		   benefit" - the weapons in its location; "on a medium ship, he must choose either forward or aft weapons, and will aid only
		   those weapons which can be hit from the chosen direction (on the Hit Location Chart)" - a hull with no outer block posts him
		   in its Primary Structure, covering one side's reach on its hit chart (sideReach). OFF_TARG is a block, or such a hull's
		   first side (Front, or Left); OFF_TARG2 its second (Aft, or Right) - the same officer (sameAs), offered only where the two
		   sides differ. Rulings (user 2026-10-20): the side's reach includes its Primary result; his kill roll counts only that
		   turn's damage to the block (blockKillRoll); on a Gunner's gun his +4 replaces the Gunner's called-shot +3. One per block
		   he could serve in, like a system enhancement (user ruling 2026-10-20 - targeterLimit; a block hosts one officer). Moves to
		   another outer block no other officer holds (movable); on a single-block hull switches side as a move to his own post
		   (switchTo). 10%, Llort 20%, Raiders and Llort only ($factionRules). No stat moves at load. */
		'OFF_TARG' => array('label' => 'Expert Targeter', 'placed' => true, 'pct' => 10, 'hosts' => 'isTargeterPost', 'nonWeapon' => true,
			'apply' => null, 'movable' => true, 'side' => 1, 'switchTo' => 'OFF_TARG2', 'shipLimit' => 'targeterLimit'),
		'OFF_TARG2' => array('label' => 'Expert Targeter', 'placed' => true, 'pct' => 10, 'sameAs' => 'OFF_TARG', 'hosts' => 'isTargeterSecondSide',
			'nonWeapon' => true, 'apply' => null, 'movable' => true, 'side' => 2, 'switchTo' => 'OFF_TARG'),
		/* Expert Turret Officer (Stage 22; rules text, user 2026-10-21), in a TURRET-MOUNTED weapon (Weapon::isTurretMount - the Vree saucers'
		   jammable mounts), aiding every weapon on that turret (Weapon::getTurretMates: his gun's linked firing group, or his gun alone) and
		   "not any other turrets on the same ship": +1 to hit - fire control, the Gunner's way - and -1 on their critical rolls
		   (applyTurretOfficer); -2 on the turret jam roll (turretJamRelief, asked by Weapon::testArcRestriction); and on a turn none of
		   them fires, the turret is SHELTERED - "no incoming fire can hit its weapons unless they use called shots" (openShelter,
		   isSheltered) - automatically, in place of the player's declaration (the user's note). Rulings (user 2026-10-21): only a SHOT
		   breaks the shelter (an interception does not); on a gun a serving Expert Gunner holds, the Gunner's +1 replaces his ("does not
		   combine"), his other effects still apply; a turret-mounted weapon linked to no other is a turret of its own; movable. One to a
		   turret (perTurret), so a ship may carry one per turret (turretLimit). 5% of the ship's base cost, Vree only ($factionRules). */
		'OFF_TURR' => array('label' => 'Expert Turret Officer', 'placed' => true, 'pct' => 5, 'hosts' => 'isTurretPost',
			'apply' => 'applyTurretOfficer', 'movable' => true, 'perTurret' => true, 'shipLimit' => 'turretLimit'),

		/* FIGHTER OFFICERS (Stage 4). A flight carries ONE officer in total (D9), posted on its first
		   craft. He is lost with that craft - destroyed, or dropped out - and never by a dock, a partial
		   dock or a split launch: he stays on the flight row he was bought on, and a "- Split" row never
		   carries him. No kill roll: a craft has no d20 critical roll, only the d10 dropout test (user
		   rulings 2026-10-11). Priced off one craft, scaled by the flight's size (flightPrice). ⚠️ Saved
		   fleets bought under the Stage 4 prices are re-priced on reload (legacyPrice). */
		/* Expert Dogfighter: +5 initiative (FV units, QX1), and wins initiative ties as the Helmsman does (user request
		   2026-10-20, winsInitiativeTies). 50% of a craft. */
		'OFF_DOG'  => array('label' => 'Expert Dogfighter', 'post' => 'craft', 'flight' => true, 'pct' => 50,
			'apply' => 'applyDogfighter', 'publish' => array('iniativebonus')),
		/* Expert Missileer: "+5 OB for missiles" = +1 OB (the +5 is FV's displayed %) on every ballistic
		   launch that gets the flight's OB - Weapon::calculateHitBase's fighter-ballistic branch and its
		   weaponManager twins. "50% of a fighter + 1 per missile the flight CAN take": half a craft, plus
		   1 point per craft for each missile one craft carries (user ruling 2026-10-11). Only for flights
		   that carry missiles. */
		'OFF_MSL'  => array('label' => 'Expert Missileer', 'post' => 'craft', 'flight' => true, 'pct' => 50,
			'perMissile' => 1, 'apply' => null, 'needs' => 'hasMissiles'),
		/* Expert Evader: "-1 to hit with the flight's own weapons, -1 damage on all incoming fire",
		   only on a turn the flight EVADES (user request 2026-10-20, replacing "always on"): an 'evade'
		   movement row ordered in the Firing phase like a combat pivot (Movement::hasEvaded; client
		   movement.js doEvade). -1 OB, which is what every fighter to-hit path reads on
		   both ends (the client never reads a flight's toHitBonus), and +1 in FighterFlight::getDamageMod.
		   100% of a craft - NOT per craft (user ruling 2026-10-15). */
		'OFF_EVAD' => array('label' => 'Expert Evader', 'post' => 'craft', 'flight' => true, 'pct' => 100,
			'apply' => 'applyEvader', 'publish' => array('offensivebonus')),
		/* Expert Motivator: -2 on the flight's dropout rolls. A fighter ENHANCEMENT until Stage 4, when the
		   user ruled it an officer (2026-10-11). ⚠️ It keeps its OLD id, EXP_MOTIV - live games and saved
		   fleets carry rows under it, and a withdrawn id drops a saved row silently while its cost stays
		   in pointCostEnh ([[arch_retiring_ship_enhancement]]). 50% of a craft (user, 2026-10-15) - no longer
		   per craft, as it was at 10% when it was an enhancement. Withheld, as before, by the nonstandard
		   fighter sets (blockStandardEnhancements). */
		'EXP_MOTIV' => array('label' => 'Expert Motivator', 'post' => 'craft', 'flight' => true, 'pct' => 50,
			'apply' => 'applyMotivator'),
		/* Expert Coordinator (Stage 9; rules text, user 2026-10-16): "may make a combat pivot for half the normal
		   thrust cost (i.e., 1 point instead of 2) and with no weapons fire penalties as a result, unless the combat
		   pivot was a full 180 degrees, in which case a penalty of -1 is applied". The thrust half is CLIENT ONLY
		   (shipManager.movement.getCombatPivotCost - movement.php recomputes no thrust cost); the to-hit half is
		   waivesCombatPivotPenalty, in Weapon::calculateHitBase and its client twin (hasCombatPivotPenalty). "One-half the base cost
		   of the fighter": 50% of a craft. Last in the list, so a doctored second officer row never displaces one
		   bought before he existed. */
		'OFF_COOR' => array('label' => 'Expert Coordinator', 'post' => 'craft', 'flight' => true, 'pct' => 50,
			'apply' => null),
		/* Expert Pilot (Stage 18; rules text, user 2026-10-19): "+1 to the flight's offensive bonus" (applyPilot); "one level of
		   jinking without paying any thrust cost for it ... The bonus level does not count against jinking limits" - CLIENT ONLY
		   (officers.freeJinks in movement.js canJink / doJink: movement.php validates no jink), and never through `jinkinglimit`,
		   which the lobby and the hangars read as the fighter's weight class; and "an Expert Pilot will also reduce [spark field]
		   damage by 1 point if he forgoes all his other benefits for the entire turn" - automatic on a turn his flight neither
		   jinks nor fires (user ruling 2026-10-19; pilotSparkRelief). "Up to four expert pilots in the same flight are
		   cumulative, except for Raider flights, which can use no more than two" (`multi`, $factionRules' `max`), never more than
		   the flight has craft, each in his own craft from the first (user notes): one lost with his craft takes his share away.
		   Rulings (user 2026-10-19): pilots ARE the flight's one officer (D9 - never beside another); "100% cost of a fighter" each,
		   for a flight of six and scaled by its size (§9.9, the Evader's rule). Last in the list, as the newest. */
		'OFF_PILOT' => array('label' => 'Expert Pilot', 'post' => 'craft', 'flight' => true, 'pct' => 100, 'multi' => 4,
			'apply' => 'applyPilot', 'publish' => array('offensivebonus')),
		/* Expert Tailgunner (Stage 21; rules text, user 2026-10-21): "adept at operating his fighter's tailgun in concert with the
		   forward-mounted weapons, and can train the rest of his flight in this special skill. The flight receives no penalties when
		   using the tailgun in the same turn as the other guns ... If the tailgun has no penalty associated with it, as in the Badger,
		   the officer provides a +5% to hit with that gun only" - Weapon::getTailGunMod asks hasTailgunner. "Half the cost of a
		   fighter": 50% of a craft. "Races can use this officer if they have fighters that meet the requirements e.g. an aft mounted
		   weapon": a flight whose craft carries a weapon flagged as its tail gun (hasTailGun) - every faction (user ruling: all 15). */
		'OFF_TAIL' => array('label' => 'Expert Tailgunner', 'post' => 'craft', 'flight' => true, 'pct' => 50,
			'apply' => null, 'needs' => 'hasTailGun'),
		/* Breaching Expert (Stage 24; rules text, user 2026-10-21): "This highly trained breaching specialist accompanies a breaching pod on
		   its mission ... The pod receives +1 thrust and ignores any loss of thrust due to damage" - the FLIGHT's thrust (applyBreachingExpert;
		   a flight moves as one), and FV takes no thrust from a flight for damage, so the second half has nothing to ignore. "The pod's Marines
		   receive benefits as if they were Elite" - his own pod's only (user ruling: "single pod"), asked by Marines::isElite (eliteMarines);
		   on a pod already Elite - the Narn and Gaim pods, or Elite Marines bought - that half adds nothing (user ruling), the rest still
		   applies. "If attempting to capture a ship, the pod's contingent gets three attack rolls on the first turn of combat. It is still
		   destroyed by a single hit ... only once per scenario": spendCaptureAssault marks the party at delivery, captureRolls rolls three.
		   "25% of the cost of the breaching pod" - of his ONE pod, not scaled by the flight's size (`unscaled`, user ruling); 50% for races
		   other than the Balosians, Yolu, Narn and Gaim ($factionRules). Breaching pods only (`needs`). "No more than 20% of a fleets
		   breaching pods" is the lobby's Fleet Correctness Report (user ruling), as every fleet-composition rule is. Last in the list, as
		   the newest. */
		'OFF_BRCH' => array('label' => 'Breaching Expert', 'post' => 'craft', 'flight' => true, 'pct' => 50, 'unscaled' => true,
			'apply' => 'applyBreachingExpert', 'publish' => array('freethrust'), 'needs' => 'isBreachingPod'),
	);

	/* An OFF_ id, or an officer kept under the id he had before officers existed (EXP_MOTIV) - so the
	   OFF_ test alone would let the Motivator back into the public tooltip. ⚠️ MIRROR PAIR with
	   officers.isOfficerId (JS). */
	public static function isOfficerId($enhID){
		$enhID = (string)$enhID;
		return strpos($enhID, self::ID_PREFIX) === 0 || isset(self::$registry[$enhID]);
	}

	public static function isFighterOfficer($id){
		return !empty(self::$registry[$id]['flight']);
	}

	//a PLACED officer (Stage 8): bought as a per-system row, posted in that row's system
	public static function isPlacedOfficer($id){
		return !empty(self::$registry[$id]['placed']);
	}

	//the officer whose $factionRules line applies: his own, or the one he IS at another post (sameAs)
	private static function rulesId($id){
		return isset(self::$registry[$id]['sameAs']) ? self::$registry[$id]['sameAs'] : $id;
	}

	public static function getRegistry(){
		return self::$registry;
	}

	public static function getLabel($id){
		return isset(self::$registry[$id]) ? self::$registry[$id]['label'] : (string)$id;
	}

	public static function getFactionRules(){
		return self::$factionRules;
	}

	//Does $factionRules let this unit's faction be offered him?
	private static function factionAllows($ship, $id){
		$id = self::rulesId($id);
		if (!isset(self::$factionRules[$id])) return true;
		$rule = self::$factionRules[$id];
		$faction = (string)$ship->faction;
		if (isset($rule['only']) && !in_array($faction, $rule['only'], true)) return false;
		if (isset($rule['never']) && in_array($faction, $rule['never'], true)) return false;
		return true;
	}

	//His price in percent for this unit's faction: $factionRules' when it names one, else the registry's
	public static function pctFor($ship, $id){
		$faction = (string)$ship->faction;
		$rules = self::rulesId($id);
		if (isset(self::$factionRules[$rules]['pct'][$faction])) return self::$factionRules[$rules]['pct'][$faction];
		return self::$registry[$id]['pct'];
	}

	/* D9: never on Ancient hulls, OSATs (mines extend OSAT) or terrain - one shared test in front of
	   every officer, not something each entry repeats. Bases, LCVs and the rest only where the post
	   exists, which findPost() decides. Flights too (Stage 4), except the flight-shaped mines
	   (MineClass, $mine), MicroSATs ($osat), the OSATs of the fighter world, and anything remotely
	   controlled ($remoteControl - the Orieni Hunter-Killers): "they have no pilots" (user ruling
	   2026-10-11). The Hunter-Killers were offered the Expert Motivator while it was an enhancement,
	   so a saved fleet can still hold one - Manager::loadSavedFleet refunds what it cost (legacyPrice). */
	public static function mayCarryOfficers($ship){
		if ((int)$ship->factionAge >= 3) return false;
		if ($ship instanceof OSAT || !empty($ship->osat)) return false;
		if (!empty($ship->mine)) return false;
		if (!empty($ship->remoteControl)) return false;
		if ($ship->isTerrain()) return false;
		return true;
	}

	/* ------------------------------------------------------------------ offers */

	/* Called at the end of Enhancements::setEnhancementOptionsShip and ::setEnhancementOptionsFighter,
	   so the offers land in the static blueprints (fvbuild -Statics follows any change here). Limit 1:
	   "one of each type" (D9) - or an officer bought as a count, maxCount; a flight's "one officer in
	   total" is the buy dialog's to enforce, and pickPosts' to defend. */
	public static function addOffers($ship){
		if (!self::mayCarryOfficers($ship)) return;
		foreach (self::$registry as $id => $officer){
			if (in_array($id, $ship->enhancementOptionsDisabled)) continue;
			if (!self::isEligible($ship, $id)) continue;
			$ship->enhancementOptions[] = array($id, $officer['label'], 0, self::maxCount($ship, $id), self::offerPrice($ship, $id), 0, false);
		}
	}

	/* How many of this officer one unit may carry: 1 ("one of each type", D9) - or, for an officer bought as a count (`multi`,
	   Stage 18), his registry number, or the faction's `max` ($factionRules), and never more than the flight may fly craft (a
	   positive maxFlightSize; the buy dialog also holds the count to the size bought - officers.rowMax). The offer's limit,
	   and pickPosts caps a bought row at it and at the craft the flight has. */
	public static function maxCount($ship, $id){
		if (empty(self::$registry[$id]['multi'])) return 1;
		$max = (int)self::$registry[$id]['multi'];
		$rules = self::rulesId($id);
		$faction = (string)$ship->faction;
		if (isset(self::$factionRules[$rules]['max'][$faction])) $max = (int)self::$factionRules[$rules]['max'][$faction];
		if (isset($ship->maxFlightSize) && (int)$ship->maxFlightSize > 0) $max = min($max, (int)$ship->maxFlightSize);
		return max(1, $max);
	}

	/* The offer's price. A ship officer: pct% of the hull. A fighter officer: pct% of ONE craft - his
	   price for a standard flight, which the lobby scales by the size bought (officers.flightCost, the
	   mirror of flightPrice). The Missileer's per-missile part is not in it: it grows with the flight,
	   so the lobby adds it at buy time (perCraftExtra). pct is the faction's (pctFor). */
	private static function offerPrice($ship, $id){
		if (empty(self::$registry[$id]['flight'])) return self::shipPrice($ship, $id);
		return self::craftPrice($ship, self::pctFor($ship, $id));
	}

	/* A ship officer's price: pct% of the hull, rounded up - or down for an officer marked roundDown. The
	   faction's pct (pctFor), unless $byFaction is false: legacyPrice, for rows saved before there were
	   faction prices. */
	private static function shipPrice($ship, $id, $byFaction = true){
		$pct = $byFaction ? self::pctFor($ship, $id) : self::$registry[$id]['pct'];
		return self::price($ship->pointCost, $pct, !empty(self::$registry[$id]['roundDown']));
	}

	/* ceil(pct% of the blueprint cost), in whole numbers: 0.07 x 100 is 7.000000000000001 in floats,
	   and ceil() would charge 8 for it (SHIP_ENHANCEMENTS_PLAN.md Stage 5 hit the same trap). $down:
	   floor instead ("dropping fractions" - the Navigator), in the same whole numbers. The pct is counted
	   in TENTHS (pctTenths), so 7.5% is exact; a whole percent prices exactly as it did before. */
	public static function price($pointCost, $pct, $down = false){
		$hundredths = max(0, (int)round($pointCost * 100));
		return intdiv($hundredths * self::pctTenths($pct) + ($down ? 0 : 99999), 100000);
	}

	/* ceil(pct% of ONE craft), in whole numbers: pointCost is for a standard flight (standardFlight). */
	public static function craftPrice($flight, $pct){
		$hundredths = max(0, (int)round($flight->pointCost * 100));
		$den = 100000 * self::standardFlight($flight);
		return intdiv($hundredths * self::pctTenths($pct) + $den - 1, $den);
	}

	//a percent in tenths of a percent, as a whole number (7.5 -> 75); a finer fraction is rounded to a tenth
	private static function pctTenths($pct){
		return (int)round((float)$pct * 10);
	}

	/* How many craft a flight's pointCost is for, so the flight its officer's listed price is for: ONE
	   on a single superheavy (maxFlightSize 1, its own pointCost), otherwise six whatever size it is
	   bought at - the lobby's own convention (confirm.getVariableSize, showShipBuy). NOT `instanceof
	   SuperHeavyFighter`, which every fighter ENHANCEMENT still asks: five superheavies (Sky / Fanged
	   Serpent AM, the Rogolon SHFs) fly up to 3 with a pointCost for six, and that test priced their
	   officers off all six craft. ⚠️ MIRROR PAIR with officers.standardFlight. */
	public static function standardFlight($flight){
		return ((int)$flight->maxFlightSize === 1) ? 1 : 6;
	}

	/* ⭐ What a fighter officer costs a flight of $crafts craft - THE rule (user ruling 2026-10-15): his
	   listed price (craftPrice, for a standard flight) scaled by $crafts / standardFlight and rounded up,
	   plus the Missileer's 1 point per missile per craft. So a 3-craft flight pays half the six-craft
	   price and a 12-craft flight twice it. $count (Stage 18): an officer bought as a count is $count
	   times the listed price, scaled and rounded up ONCE for the row - the lobby's own sum (enhCost is
	   price x count): 2 Expert Pilots on a 3-craft Aurora are ceil(114 x 3/6) = 57.
	   Stage 24: an `unscaled` officer is his listed price, whatever the size (a Breaching Expert in his one pod).
	   ⚠️ MIRROR PAIR with officers.flightCost. */
	public static function flightPrice($flight, $id, $crafts, $count = 1){
		$officer = self::$registry[$id];
		if (!empty($officer['unscaled'])) return self::craftPrice($flight, self::pctFor($flight, $id)) * max(1, (int)$count);
		$crafts = max(1, (int)$crafts);
		$standard = self::standardFlight($flight);
		$price = intdiv(self::craftPrice($flight, self::pctFor($flight, $id)) * max(1, (int)$count) * $crafts + $standard - 1, $standard);
		if (!empty($officer['perMissile'])) $price += (int)$officer['perMissile'] * self::missilesPerCraft($flight) * $crafts;
		return $price;
	}

	/* Missiles one craft can carry: its magazine's capacity (rounds, not salvoes) plus every legacy
	   launcher's own load (FighterMissileRack, and FighterTorpedoLauncher, which extends it). The
	   Missileer's per-missile price and his `needs`. ⚠️ MIRROR PAIR with officers.missilesPerCraft. */
	public static function missilesPerCraft($flight){
		if (!($flight instanceof FighterFlight)) return 0;
		$craft = self::firstCraft($flight);
		if ($craft === null) return 0;
		$total = 0;
		foreach ($craft->systems as $system){
			if ($system instanceof AmmoMagazine) $total += (int)$system->capacity;
			else if ($system instanceof FighterMissileRack) $total += (int)$system->maxAmount;
		}
		return $total;
	}

	/* ------------------------------------------------------------------ posts */

	/* The system an officer lives in, or null when the hull has none (so he is not offered).
	   ⚠️ MIRROR PAIR with officers.js findPost.
	     cnc      the first C&C that is not a Flag Bridge; failing that, the first C&C of any kind.
	              SecondaryCnC is not a CnC subclass, so it is never picked.
	     scanner  the strongest Scanner by output (ELINT scanners included) - the one Elite Crew,
	              Poor Crew and Improved Sensors move, so they agree on a hull with two.
	     elint    the strongest ELINT array by output (Stage 20): an ElintScanner, the Chameleon suite included - never a
	              Kirishiac ELINT module, which is no Scanner. No officer-eligible hull mixes a plain Scanner with an array.
	     reactor  the biggest Reactor by maxhealth - Elite Crew's rule (a malus would sit on the main
	              reactor too); Mag-Grav reactors included.
	     engine   the strongest Engine by output - the one Poor Crew and ADV_ENG move (Stage 5)
	     maggrav  the first Mag-Gravitic Reactor that can be hit (Stage 16): never a MagGravReactorTechnical, which can
	              never be damaged - so its officer could never be disabled or killed. Every Ipsha hull has exactly one.
	     jump     the first jump drive that opens jump points (Stage 7): not a legacy drive (the one-click
	              Hyperdrive / FTL Drive / Phasing Drive / Trek Nacelle, and every Ancient's), not a fixed
	              gate's. Through getUnitJumpEngines, never a sweep of $ship->systems ([[project_jump_points]]).
	              No officer-eligible hull carries two (measured 2026-10-15), so "his ship's drive" and "the
	              drive he sits in" cannot disagree.
	     computer the Hyach Computer (Stage 26): no hull carries two.
	     craft    a flight's first craft (Stage 4): the lobby's one fighter card, and the sample fighter
	              getSampleFighter() answers - id 1, whether or not it is still flying. An officer bought as a
	              count (Stage 18) takes the first craft, the next the second, and so on (nthCraft).
	   ⚠️ Asked BEFORE any enhancement moves an output (D2): Poor Crew takes a point off the strongest
	   scanner, so on a hull with two equal ones asking afterwards would pick the other. */
	private static function findPost($ship, $post){
		switch ($post){
			case 'craft':
				return ($ship instanceof FighterFlight) ? self::firstCraft($ship) : null;
			case 'cnc':
				$any = null;
				foreach ($ship->systems as $system){
					if (!($system instanceof CnC)) continue;
					if (!($system instanceof FlagBridge)) return $system;
					if ($any === null) $any = $system;
				}
				return $any;
			case 'scanner':
				return self::strongest($ship, 'Scanner', 'output', -1);
			case 'elint':
				return self::strongest($ship, 'ElintScanner', 'output', -1);
			case 'reactor':
				return self::strongest($ship, 'Reactor', 'maxhealth', -1000);
			case 'maggrav':
				foreach ($ship->systems as $system){
					if ($system instanceof MagGravReactor && $system->isTargetable) return $system;
				}
				return null;
			case 'engine':
				return self::strongest($ship, 'Engine', 'output', -1);
			case 'jump':
				foreach (JumpEngine::getUnitJumpEngines($ship) as $engine){
					if (!$engine->isLegacyJump() && !$engine->isGateJump()) return $engine;
				}
				return null;
			case 'computer':
				foreach ($ship->systems as $system){
					if ($system instanceof HyachComputer) return $system;
				}
				return null;
		}
		return null;
	}

	//Enhancements::strongestSystem's semantics: the first in construction order wins a tie
	private static function strongest($ship, $class, $field, $floor){
		$best = null;
		$bestValue = $floor;
		foreach ($ship->systems as $system){
			if ($system instanceof $class && $system->$field > $bestValue){
				$bestValue = $system->$field;
				$best = $system;
			}
		}
		return $best;
	}

	//the first craft by construction order: systems are keyed by id, and the first craft is id 1
	private static function firstCraft($flight){
		return self::nthCraft($flight, 1);
	}

	//the $n-th craft by construction order (1 = the first), or null when the flight has fewer
	private static function nthCraft($flight, $n){
		$i = 0;
		foreach ($flight->systems as $craft){
			if ($craft instanceof Fighter && ++$i === (int)$n) return $craft;
		}
		return null;
	}

	private static function craftCount($flight){
		$n = 0;
		foreach ($flight->systems as $craft) if ($craft instanceof Fighter) $n++;
		return $n;
	}

	//a fighter officer for a flight and every other officer for a ship; the faction may have him
	//($factionRules), the hull has the post, and passes the officer's own test if he has one
	private static function isEligible($ship, $id){
		$officer = self::$registry[$id];
		/* Track 2 has no ship-level row - offered per weapon by setSystemEnhancementOptions, posted by
		   pickPlacedPosts, asked per system by mayHost - so neither addOffers nor a doctored row reaches it. */
		if (!empty($officer['placed'])) return false;
		if (!empty($officer['flight']) !== ($ship instanceof FighterFlight)) return false;
		if (!self::factionAllows($ship, $id)) return false;
		if (self::findPost($ship, $officer['post']) === null) return false;
		if (empty($officer['needs'])) return true;
		$needs = $officer['needs'];
		return (bool)self::$needs($ship);
	}

	/* D2: posts are picked at the top of BaseShip::onConstructed, BEFORE any enhancement moves an
	   output, from the bought rows. A count above 1 still means one officer (D9), and a row the hull
	   would not have been offered (no post, the officer's own test fails, or his faction rules refuse
	   it) buys nothing - the server trusts the client's rows, so a doctored payload is answered here. So
	   is a flight's second officer row (D9: one officer in total): only the first in registry order serves.
	   An officer bought as a count (`multi`, Stage 18) is that many entries, one in each of the flight's
	   first crafts, held to maxCount and to the craft it has. */
	public static function pickPosts($ship){
		$list = array();
		if (self::hasOfficerRow($ship) && self::mayCarryOfficers($ship)){
			foreach (self::$registry as $id => $officer){
				if (!self::isBought($ship, $id)) continue;
				if (!self::isEligible($ship, $id)) continue;
				if (!empty($officer['multi']) && $ship instanceof FighterFlight){
					$n = min(self::boughtCount($ship, $id), self::maxCount($ship, $id), self::craftCount($ship));
					for ($k = 1; $k <= $n; $k++){
						$list[] = array('id' => $id, 'post' => (int)self::nthCraft($ship, $k)->id, 'out' => null, 'how' => null);
					}
					break;
				}
				$post = self::findPost($ship, $officer['post']);
				$list[] = array('id' => $id, 'post' => (int)$post->id, 'out' => null, 'how' => null);
				if ($ship instanceof FighterFlight) break;
			}
			if (!($ship instanceof FighterFlight)) self::pickPlacedPosts($ship, $list);
		}
		$ship->setOfficers($list);
	}

	/* Track 2 (Stage 8): one officer per bought per-system row, posted in that row's system. A row buys
	   nothing unless its system resolves under the name it was bought on (D13), it is still offered that
	   officer (Enhancements::systemEnhancementOffered - the per-system registry's own test), keepPlacedRows
	   lets it stand, and - for a sameAs officer - his ship-level self is not already aboard (the C&C
	   Matter Weapons Expert wins: the server trusts the client's rows, so a doctored list is answered
	   here). Posts are system ids, which pre-battle damage and the D11 refund already reckon with. */
	private static function pickPlacedPosts($ship, array &$list){
		$aboard = array();
		foreach ($list as $entry) $aboard[$entry['id']] = true;
		$pairs = array();
		foreach (self::placedRows($ship) as $row){
			$officer = self::$registry[$row[0]];
			if (!empty($officer['sameAs']) && isset($aboard[$officer['sameAs']])) continue;
			$system = $ship->getSystemById((int)$row[6]);
			if (!$system) continue;
			if (isset($row[7]) && $row[7] !== '' && (string)$system->name !== (string)$row[7]) continue;
			if (!Enhancements::systemEnhancementOffered($ship, $system, $row[0])) continue;
			$pairs[] = array($row[0], (int)$system->id);
		}
		foreach (self::keepPlacedRows($ship, $pairs) as $i) {
			$list[] = array('id' => $pairs[$i][0], 'post' => $pairs[$i][1], 'out' => null, 'how' => null);
		}
	}

	//the bought per-system rows that hold a placed officer (purchase tuples, systemid at [6], sysname at [7])
	private static function placedRows($ship){
		$rows = array();
		if (empty($ship->systemEnhancements) || !is_array($ship->systemEnhancements)) return $rows;
		foreach ($ship->systemEnhancements as $row){
			if (!is_array($row) || !isset($row[0], $row[2], $row[6])) continue;
			if ((int)$row[2] < 1 || !self::isPlacedOfficer((string)$row[0])) continue;
			$rows[] = $row;
		}
		return $rows;
	}

	/* ⭐ WHICH PLACED OFFICERS A SHIP MAY KEEP - THE rule, for the sanitiser (Enhancements::
	   sanitiseSystemEnhancements, at buy, save and reload, which names what it drops) and for the load
	   (pickPlacedPosts). $pairs: array(array(id, systemid), ...). Walked in registry order, then system id:
	     - a system hosts ONE placed officer (so a doctored Gunner + Matter expert on one gun keeps the Gunner);
	     - no officer passes his ship cap (placedLimit);
	     - a `perTurret` officer (Stage 22, the Expert Turret Officer): one to a turret (turretKey).
	   Returns the keys of $pairs that stand, in that order. ⚠️ MIRROR PAIR with officers.placedEntries (JS). */
	public static function keepPlacedRows($ship, array $pairs){
		$order = array_flip(array_keys(self::$registry));
		$keys = array_keys($pairs);
		usort($keys, function ($a, $b) use ($pairs, $order){
			$ra = isset($order[$pairs[$a][0]]) ? $order[$pairs[$a][0]] : PHP_INT_MAX;
			$rb = isset($order[$pairs[$b][0]]) ? $order[$pairs[$b][0]] : PHP_INT_MAX;
			if ($ra !== $rb) return $ra - $rb;
			if ((int)$pairs[$a][1] !== (int)$pairs[$b][1]) return (int)$pairs[$a][1] - (int)$pairs[$b][1];
			return $a - $b;
		});
		$hosted = array(); $count = array(); $turrets = array(); $kept = array();
		foreach ($keys as $key){
			list($id, $systemid) = $pairs[$key];
			if (!self::isPlacedOfficer($id) || isset($hosted[(int)$systemid])) continue;
			$n = isset($count[$id]) ? $count[$id] : 0;
			if ($n >= self::placedLimit($ship, $id)) continue;
			$turret = self::isPerTurret($id) ? self::turretKey($ship->getSystemById((int)$systemid)) : null;
			if ($turret !== null && isset($turrets[$id][$turret])) continue;
			$hosted[(int)$systemid] = true;
			$count[$id] = $n + 1;
			if ($turret !== null) $turrets[$id][$turret] = true;
			$kept[] = $key;
		}
		return $kept;
	}

	//a placed officer who is one to a TURRET, not only one to a system (Stage 22) - keepPlacedRows, mayMoveTo, the sanitiser's notice
	public static function isPerTurret($id){
		return !empty(self::$registry[$id]['perTurret']);
	}

	/* How many of this placed officer one ship may carry: his `shipLimit` method, or 1 ("one of each type").
	   Sent to the lobby as the 6th slot of his per-system offer (Enhancements::setSystemEnhancementOptions),
	   so systemEnhancements.set() refuses past it without a client copy of the rule. */
	public static function placedLimit($ship, $id){
		if (empty(self::$registry[$id]['shipLimit'])) return 1;
		$method = self::$registry[$id]['shipLimit'];
		return (int)self::$method($ship);
	}

	/* The Expert Gunner: "at most one such officer on a medium ship or smaller, two on a heavy combat vessel,
	   three on a capital ship and four on an enormous unit" - shipSizeClass 0 (LCV) and 1 give 1, 2 gives 2,
	   3 gives 3, and $Enormous (FV's enormous units; size class 4 is not implemented) gives 4. */
	private static function gunnerLimit($ship){
		if (!empty($ship->Enormous)) return 4;
		return max(1, min(3, (int)$ship->shipSizeClass));
	}

	/* May this system host this placed officer? D9's unit test (no Ancients, OSATs - which
	   Enhancements::systemMayBeEnhanced lets through - terrain, flights or remotely controlled craft), the
	   hull's enhancementOptionsDisabled, his faction rules, and his own weapon test (`hosts`: the Matter
	   expert needs a weapon with a Matter mode). Asked by the per-system registry's `eligible` wrappers,
	   AFTER Gunsights' own weapon test ("any weapon Gunsights may be bought for", user ruling 2026-10-16) -
	   so every host is a gun that rolls to hit. Stage 15: a `nonWeapon` officer (the Religious Leader's twin)
	   is posted in systems that are NOT weapons, which his `hosts` names. The per-hull opt-out of the
	   ship-level officer he IS (sameAs) covers him too. */
	public static function mayHost($ship, $system, $id){
		if (!self::isPlacedOfficer($id)) return false;
		if ($ship instanceof FighterFlight || !self::mayCarryOfficers($ship)) return false;
		if (in_array($id, $ship->enhancementOptionsDisabled) || in_array(self::rulesId($id), $ship->enhancementOptionsDisabled)) return false; //the per-hull opt-out, as addOffers honours it
		if (!self::factionAllows($ship, $id)) return false;
		if (empty(self::$registry[$id]['nonWeapon']) && !($system instanceof Weapon)) return false;
		if (empty(self::$registry[$id]['hosts'])) return true;
		$hosts = self::$registry[$id]['hosts'];
		return (bool)self::$hosts($system, $ship);
	}

	//the cheap test that keeps every ordinary ship (a single 'NONE' row) out of the resolver
	private static function hasOfficerRow($ship){
		foreach ($ship->enhancementOptions as $entry){
			if ((int)$entry[2] > 0 && self::isOfficerId($entry[0])) return true;
		}
		return !empty(self::placedRows($ship));
	}

	private static function isBought($ship, $id){
		return self::boughtCount($ship, $id) > 0;
	}

	//the count on his bought row (0 when none)
	private static function boughtCount($ship, $id){
		foreach ($ship->enhancementOptions as $entry){
			if ($entry[0] === $id && (int)$entry[2] > 0) return (int)$entry[2];
		}
		return 0;
	}

	/* ------------------------------------------------------------------ out of action */

	/* After the per-system onConstructed loop (structureSystem is filled there) and before
	   iniativeadded is computed (D5). Replays the officers' moves (Stage 10), derives each officer's
	   state, then applies the stat changes of every officer serving on $turn, in registry order. */
	public static function apply($ship, $turn){
		$list = $ship->getOfficers();
		if (empty($list)) return;
		$turn = (int)$turn;
		self::resolveTransfers($ship, $list, $turn);
		foreach ($list as $i => $entry){
			list($list[$i]['out'], $list[$i]['how']) = self::outState($ship, $entry);
		}
		self::resolveUses($ship, $list);
		$ship->setOfficers($list);

		foreach ($list as $i => $entry){
			$officer = self::$registry[$entry['id']];
			if (self::serves($entry, $turn)) $method = $officer['apply'];
			else if (isset($officer['lost']) && self::isLost($entry, $turn)) $method = $officer['lost']; //Stage 14: what his loss does
			else continue;
			if ($method !== null) self::$method($ship, $list[$i], $turn); //by reference for applyGunner, which notes his added mode
		}
		self::settleFiringModes($ship, $list);
		$ship->setOfficers($list);
	}

	/* WHEN AND HOW HE LEFT ACTION: array(out, how), or array(null, null) while he serves. The earliest
	   wins; on a tie the first offered (a kill before a move before a disabling: both in one turn reads
	   as killed, §4.4). Each is matched to the post he held at that turn ($entry['stays'], Stage 10), so
	   a Gunner who moved off a gun is not disabled when it falls later, and a kill note on one Gunner's
	   gun kills him and not his ship's other Gunners. */
	private static function outState($ship, array $entry){
		$best = null; $how = null;
		$take = function ($turn, $state) use (&$best, &$how){
			if ($turn === null) return;
			if ($best === null || (int)$turn < $best){ $best = (int)$turn; $how = $state; }
		};
		$stays = self::staysOf($entry);
		//KILLED: his kill notes, each on the post he held that turn (a note naming no post: wherever he was)
		foreach ($ship->getOfficerKills() as $kill){
			foreach ($stays as $stay){
				if ($kill['id'] !== $stay['id']) continue;
				if ($kill['post'] === null || ((int)$kill['post'] === (int)$stay['post'] && self::within($kill['turn'], $stay))) $take($kill['turn'], 'killed');
			}
		}
		/* ...and the weapon-mounted Matter Weapons Expert (anyDamage): "disabled if the weapon takes any damage
		   at all (after armor), and automatically killed if the weapon is destroyed" - so his post's fall while
		   he holds it (its own boxes or its Structure block) KILLS him. One hit that damages and destroys it is
		   a kill (the tie rule above). */
		foreach ($stays as $stay){
			if (empty(self::$registry[$stay['id']]['anyDamage'])) continue;
			$post = $ship->getSystemById($stay['post']);
			$fell = ($post !== null) ? self::disabledTurn($ship, $post) : null;
			if ($fell !== null && self::within($fell, $stay)) $take($fell, 'killed');
		}
		/* MOVED: a fighter officer who left this row with his fighter (followFighter) - a move beats the
		   DisengagedFighter bookkeeping that retires the row he left on the same turn. Matched on the post the
		   note sits on (Stage 18): of several Expert Pilots only the one whose craft left has gone. */
		foreach ($ship->getOfficerMoves() as $move){
			if ($move['id'] !== $entry['id']) continue;
			if ($move['post'] === null || (int)$move['post'] === (int)$entry['post']) $take((int)$move['turn'], 'moved');
		}
		//DISABLED: a post destroyed while he held it, or by the end of the turn he was moving to it (he arrives to a wreck)
		foreach ($stays as $stay){
			$post = $ship->getSystemById($stay['post']);
			if ($post === null){ $take(0, 'disabled'); continue; } //a vanished post never served
			$anyDamage = !empty(self::$registry[$stay['id']]['anyDamage']);
			$fell = self::disabledTurn($ship, $post);
			if ($fell !== null && ($stay['to'] === null || $fell <= $stay['to'])){
				if ($stay['from'] === null || $fell >= $stay['from']){
					if (!$anyDamage) $take($fell, 'disabled');
				} else if ($fell === $stay['from'] - 1){
					$take($fell, 'disabled');
				}
			}
			if ($anyDamage) $take(self::firstDamagedTurn($post, $stay), 'disabled');
		}
		return array($best, $how);
	}

	/* Out on turn N = serves N, gone from N + 1 (D3: the support-system convention, and it keeps
	   simultaneous fire simultaneous). Out at turn 0 is pre-battle damage: he never serves (§4.9).
	   Stage 10: and not on a turn he spent moving to another post ("lose their abilities for the rest of
	   that turn"). ⚠️ MIRROR PAIR with officers.serves (JS). */
	private static function serves($entry, $turn){
		if (!empty($entry['transit']) && in_array((int)$turn, $entry['transit'], true)) return false;
		if ($entry['out'] === null) return true;
		return ($entry['out'] >= 1) && ($entry['out'] >= (int)$turn);
	}

	/* Out of action IN BATTLE before $turn - disabled or killed: what a `lost` method answers to (Stage 14, the Religious
	   Leader's frenzy), from the turn after it happened, as his bonuses end then. Not pre-battle damage (out 0: he never came
	   aboard for this battle), and not a fighter officer who went with his fighter ('moved' - he serves on, elsewhere).
	   ⚠️ MIRROR PAIR with officers.isLost (JS). */
	private static function isLost($entry, $turn){
		if ($entry['out'] === null || $entry['out'] < 1 || $entry['out'] >= (int)$turn) return false;
		return $entry['how'] === 'disabled' || $entry['how'] === 'killed';
	}

	/* ------------------------------------------------------------------ moving officers (Stage 10) */

	/* ⭐ MOVING OFFICERS (user request 2026-10-16). In Initial Orders the player may order a movable officer
	   (`movable` - the Expert Gunner, and the Matter Weapons Expert, at the C&C or in a weapon) to another post
	   he could hold; the order is one OffTransfer note on his old post (saveTransfers), and every load replays
	   them here in turn order. He serves nowhere on the turn he moves (serves), still counts at his OLD post
	   for damage and his kill roll that turn, and holds the new one from the next turn.
	   Each entry gets its `stays` - array('id', 'post', 'from', 'to') per post he has held, from/to the turns
	   (null: open), `transit` (the turns he moved) when he has moved, and `moving` (the post he is moving to)
	   on the turn of a move. Its id and post become those of the stay holding $turn - which is how the Matter
	   Weapons Expert is OFF_MWE at the C&C and OFF_MWEW in a weapon. An order the rules refuse (mayMoveTo)
	   moves nobody: the client offers only what they allow, and the server checks again before writing. */
	private static function resolveTransfers($ship, array &$list, $turn){
		foreach ($list as $i => $entry){
			$list[$i]['stays'] = array(array('id' => $entry['id'], 'post' => (int)$entry['post'], 'from' => null, 'to' => null));
		}
		$orders = $ship->getOfficerTransfers();
		if (empty($orders)) return;
		usort($orders, function ($a, $b){
			if ($a['turn'] !== $b['turn']) return $a['turn'] - $b['turn'];
			return $a['seq'] - $b['seq'];
		});
		foreach ($orders as $order){
			$m = (int)$order['turn'];
			if ($m > $turn) continue;
			$i = self::officerAt($list, $order['id'], $order['from'], $m);
			if ($i === null) continue;
			$target = $ship->getSystemById($order['to']);
			$as = ($target === null) ? null : self::mayMoveTo($ship, $list, $i, $target, $m);
			if ($as === null) continue;
			$last = count($list[$i]['stays']) - 1;
			$list[$i]['stays'][$last]['to'] = $m;
			$list[$i]['stays'][] = array('id' => $as, 'post' => (int)$target->id, 'from' => $m + 1, 'to' => null);
			$list[$i]['transit'][] = $m;
		}
		foreach ($list as $i => $entry){
			$stay = self::stayOn($entry, $turn);
			$list[$i]['id'] = $stay['id'];
			$list[$i]['post'] = (int)$stay['post'];
			if (!empty($entry['transit']) && in_array($turn, $entry['transit'], true)){
				$next = self::stayOn($entry, $turn + 1);
				$list[$i]['moving'] = (int)$next['post'];
			}
		}
	}

	/* May the officer at $list[$i] be moved to $target on turn $m? The id he would serve as there, or null.
	   ⭐ THE move rule - the load (resolveTransfers), the write (saveTransfers) and the payload's `targets`
	   (the client offers only those) all ask it:
	     - a movable officer, serving (not disabled or killed before $m), moving at most once a turn;
	     - a post he could hold (postAs: a weapon he could be bought for; the Matter Weapons Expert's C&C), not
	       his own, and not destroyed - his own only for an officer who becomes another there (`switchTo`, Stage 19: the
	       Expert Targeter's switch to a Medium ship's other side), costing the turn as any move does;
	     - not the post of another placed officer still in action that turn or the next - one officer to a
	       weapon (Stage 8), and no swaps: the post must be free when he sets out. A disabled or killed
	       officer holds nobody's post;
	     - a `perTurret` officer (Stage 22): nor a turret another of him holds, on the same terms - between the guns of
	       his own turret he may move. */
	private static function mayMoveTo($ship, array $list, $i, $target, $m){
		$entry = $list[$i];
		$stay = self::stayOn($entry, $m);
		if ($stay === null || empty(self::$registry[$stay['id']]['movable'])) return null;
		if (!empty($entry['transit']) && in_array((int)$m, $entry['transit'], true)) return null;
		$own = ((int)$target->id === (int)$stay['post']);
		if ($own && empty(self::$registry[$stay['id']]['switchTo'])) return null;
		list($out) = self::outState($ship, $entry);
		if ($out !== null && $out < $m) return null; //"Disabled or Killed Officers cannot be moved"
		$as = $own ? self::switchAs($ship, $stay['id'], $target) : self::postAs($ship, $stay['id'], $target);
		if ($as === null) return null;
		$fell = self::disabledTurn($ship, $target);
		if ($fell !== null && $fell < $m) return null;
		$turret = self::isPerTurret($as) ? self::turretKey($target) : null;
		foreach ($list as $j => $other){
			if ($j === $i) continue;
			foreach (array($m, $m + 1) as $t){
				$held = self::stayOn($other, $t);
				if ($held === null || !self::holdsAPost($held['id'])) continue;
				if ((int)$held['post'] !== (int)$target->id){
					if ($turret === null || $held['id'] !== $as || self::turretKey($ship->getSystemById($held['post'])) !== $turret) continue;
				}
				list($otherOut) = self::outState($ship, $other);
				if ($otherOut === null || $otherOut >= $m) return null;
			}
		}
		return $as;
	}

	/* The officer he would be at $system - his own id, or another sharing his rulesId (the Matter Weapons Expert:
	   OFF_MWE at the C&C, OFF_MWEW in a weapon) - or null when $system cannot host him. A placed officer needs
	   the per-system offer he could be BOUGHT on (the lobby's own test, so a move goes where a purchase could);
	   a ship-level one his own post. (The Religious Leader moves from his C&C as OFF_RELIP, his placed twin - Stage 15.) */
	private static function postAs($ship, $id, $system){
		$kind = self::rulesId($id);
		foreach (self::$registry as $candidate => $officer){
			if (empty($officer['movable']) || self::rulesId($candidate) !== $kind) continue;
			if (!empty($officer['placed'])){
				if (Enhancements::systemEnhancementOffered($ship, $system, $candidate)) return $candidate;
			} else if (self::isEligible($ship, $candidate) && self::findPost($ship, $officer['post']) === $system){
				return $candidate;
			}
		}
		return null;
	}

	//Stage 19: the officer he becomes on his own post ($registry `switchTo`), where the hull offers him there - or null
	private static function switchAs($ship, $id, $system){
		$to = isset(self::$registry[$id]['switchTo']) ? self::$registry[$id]['switchTo'] : null;
		return ($to !== null && Enhancements::systemEnhancementOffered($ship, $system, $to)) ? $to : null;
	}

	/* "Any primary non-weapon system" (the Religious Leader) - a system of the PRIMARY section that is not a weapon and not
	   Structure. Not one that can never be targeted either (a technical system - the Hyach Specialists, an ammo magazine, a
	   Mag-Grav "technical" reactor): an officer there could never be disabled or killed. ⚠️ A jump drive IS a Weapon in FV
	   (it "fires" to open a jump point), and a drive, not a weapon, in the rules: it counts. */
	private static function isPrimaryNonWeapon($ship, $system){
		if ((int)$system->location !== 0 || empty($system->isTargetable)) return false;
		if ($system instanceof Weapon && !($system instanceof JumpEngine)) return false;
		return !($system instanceof Structure) && !($system instanceof Fighter);
	}

	/* OFF_RELIP's posts (`hosts`, Stage 15): any primary non-weapon system but the C&C his ship-level self is posted in -
	   there he is OFF_RELI, bought in the Officers section, so one post never has two ways to buy him. */
	private static function isReligiousPost($system, $ship){
		return self::isPrimaryNonWeapon($ship, $system) && $system !== self::findPost($ship, 'cnc');
	}

	/* ------------------------------------------------------------------ the Expert Targeter (Stage 19) */

	/* How many Expert Targeters one ship may carry (`shipLimit`): one per block he could serve in - "1 per eligible (structure)
	   system, like other System Enhancements" (user ruling 2026-10-20). keepPlacedRows' one officer to a system does the rest, and
	   mayMoveTo's occupancy test keeps a moving Targeter off a block another one holds. A single-block hull: 1. */
	private static function targeterLimit($ship){
		$n = 0;
		foreach ($ship->systems as $system) if (self::isTargeterPost($system, $ship)) $n++;
		return max(1, $n);
	}

	/* OFF_TARG's posts (`hosts`): "One structure block" - any outer block (the user's note: the non-Primary blocks); a hull with
	   none, a Medium ship or an LCV, has one block, its Primary Structure, and he covers one side of its hit chart there. */
	private static function isTargeterPost($system, $ship){
		if (!($system instanceof Structure)) return false;
		if (self::hasOuterBlock($ship)) return (int)$system->location !== 0;
		return $system === $ship->getStructureSystem(0) && count(self::hullSides($ship)) > 0;
	}

	/* OFF_TARG2's (`hosts`): that one block again, covering its SECOND side - offered only where the two sides reach different
	   weapons, so an LCV (whose three charts are one) has no choice to make. */
	private static function isTargeterSecondSide($system, $ship){
		if (!($system instanceof Structure) || self::hasOuterBlock($ship) || $system !== $ship->getStructureSystem(0)) return false;
		$sides = self::hullSides($ship);
		if (count($sides) !== 2) return false;
		$first = array_keys(self::sideReach($ship, $sides[0]));
		$second = array_keys(self::sideReach($ship, $sides[1]));
		sort($first); sort($second);
		return $first !== $second;
	}

	//a Structure block outside the Primary section (Structure::createAsOuter's included)
	private static function hasOuterBlock($ship){
		foreach ($ship->systems as $system){
			if ($system instanceof Structure && (int)$system->location !== 0) return true;
		}
		return false;
	}

	//a single-block hull's sides: its hit chart's non-Primary locations, in order - 1 and 2 (Front, Aft), or 3 and 4 (Left, Right)
	private static function hullSides($ship){
		$sides = array();
		foreach (array_keys((array)$ship->hitChart) as $loc) if ((int)$loc !== 0) $sides[] = (int)$loc;
		sort($sides);
		return $sides;
	}

	/* ⭐ "Those weapons which can be hit from the chosen direction (on the Hit Location Chart)": every weapon (by id) the side's hit
	   chart names - "N:" redirects and "TAG:" entries resolved as a hit would resolve them (getSystemsByNameLoc), from the side's own
	   bearing - and, through its "Primary" result, every weapon the Primary chart names (user ruling 2026-10-20). Structure and the
	   jump drive (a Weapon in FV) are never his. */
	private static function sideReach($ship, $side, $viaPrimary = true){
		$out = array();
		if (!isset($ship->hitChart[$side])) return $out;
		$bearing = isset(self::$sideBearing[$side]) ? self::$sideBearing[$side] : 0;
		foreach ($ship->hitChart[$side] as $name){
			if (strcasecmp($name, 'Structure') === 0) continue;
			if (strcasecmp($name, 'Primary') === 0){
				if ($viaPrimary && (int)$side !== 0) $out += self::sideReach($ship, 0, false);
				continue;
			}
			foreach ($ship->getSystemsByNameLoc($name, $side, $bearing, true) as $system){
				if ($system instanceof Weapon && !($system instanceof JumpEngine)) $out[(int)$system->id] = $system;
			}
		}
		return $out;
	}

	/* Does this Targeter's entry cover $weapon? "All weapons attached to this block": the weapons in his block's location; on a
	   single-block hull, his side's hit chart reach (sideReach) - his `side`, the first or the second. */
	private static function targeterCovers($ship, array $entry, $weapon){
		if (!($weapon instanceof Weapon) || $weapon instanceof JumpEngine) return false;
		$post = $ship->getSystemById($entry['post']);
		if ($post === null) return false;
		if (self::hasOuterBlock($ship)) return (int)$weapon->location === (int)$post->location;
		$sides = self::hullSides($ship);
		$k = (isset(self::$registry[$entry['id']]['side']) ? (int)self::$registry[$entry['id']]['side'] : 1) - 1;
		return isset($sides[$k]) && isset(self::sideReach($ship, $sides[$k])[(int)$weapon->id]);
	}

	//...every weapon he covers, by id: the payload's `covers` (own team only), so the client's preview copies no hit-chart rule
	private static function coveredWeapons($ship, array $entry){
		$ids = array();
		foreach ($ship->systems as $system){
			if (self::targeterCovers($ship, $entry, $system)) $ids[] = (int)$system->id;
		}
		return $ids;
	}

	//the Expert Targeter serving on $turn who covers $weapon, or null
	private static function targeterFor($ship, $weapon, $turn){
		foreach ($ship->getOfficers() as $entry){
			if (self::rulesId($entry['id']) !== 'OFF_TARG' || !self::serves($entry, $turn)) continue;
			if (self::targeterCovers($ship, $entry, $weapon)) return $entry;
		}
		return null;
	}

	/* ⭐ HIS +4 - Weapon::calculateHitBase, in d20 units, behind $officersPresent, beside the Gunner's. A STANDARD called shot only
	   ($called: a call that pays the -8 - not a Kirishiac flat-profile one): "This does not provide any benefit to normal attacks,
	   only called shots. It does not cancel the usual -8 called shot penalty, either." Nothing to look up on any other shot, nor on an
	   Antimatter Shredder's attack at a fighter (bonusCall).
	   ⚠️ MIRROR PAIR with officers.targeterShotBonus (JS), in weaponManager.computeShotModifiers. */
	public static function targeterShotBonus($ship, $weapon, $called, $gamedata){
		if (!self::bonusCall($weapon, $called)) return 0;
		return (self::targeterFor($ship, $weapon, (int)$gamedata->turn) !== null) ? self::TARGETER_CALLED_BONUS : 0;
	}

	//an officer who occupies his post as far as another's move is concerned: a placed or movable one
	private static function holdsAPost($id){
		return !empty(self::$registry[$id]['placed']) || !empty(self::$registry[$id]['movable']);
	}

	//the officer serving as $id at post $from on turn $m, or null
	private static function officerAt(array $list, $id, $from, $m){
		foreach ($list as $i => $entry){
			$stay = self::stayOn($entry, $m);
			if ($stay !== null && $stay['id'] === (string)$id && (int)$stay['post'] === (int)$from) return $i;
		}
		return null;
	}

	//his posts so far: the stays resolveTransfers built, or the one post an entry built elsewhere names
	private static function staysOf(array $entry){
		if (!empty($entry['stays'])) return $entry['stays'];
		return array(array('id' => $entry['id'], 'post' => (int)$entry['post'], 'from' => null, 'to' => null));
	}

	private static function stayOn(array $entry, $turn){
		foreach (self::staysOf($entry) as $stay){
			if (self::within($turn, $stay)) return $stay;
		}
		return null;
	}

	private static function within($turn, array $stay){
		return ($stay['from'] === null || (int)$turn >= $stay['from']) && ($stay['to'] === null || (int)$turn <= $stay['to']);
	}

	/* InitialOrdersGamePhase::process: the moves a POST asked for ($claims - array(id, from, to) each, raw), checked
	   against the LOADED ship (the POST-side copy has no officers) with the load's own rule, and each one that
	   passes written as an OffTransfer note on his old post - read back at every later load (takeNote). */
	public static function saveTransfers($ship, $claims, $gamedata){
		if (!($ship instanceof BaseShip) || !is_array($claims)) return;
		$list = $ship->getOfficers();
		if (empty($list)) return;
		$turn = (int)$gamedata->turn;
		foreach (array_slice(array_values($claims), 0, self::MAX_TRANSFER_CLAIMS) as $claim){
			if (!is_array($claim) || !isset($claim['id'], $claim['from'], $claim['to'])) continue;
			$i = self::officerAt($list, (string)$claim['id'], (int)$claim['from'], $turn);
			$target = ($i === null) ? null : $ship->getSystemById((int)$claim['to']);
			$as = ($target === null) ? null : self::mayMoveTo($ship, $list, $i, $target, $turn);
			if ($as === null) continue;
			//this POST's later claims see him gone, as the load will
			$list[$i]['stays'] = self::staysOf($list[$i]);
			$list[$i]['stays'][count($list[$i]['stays']) - 1]['to'] = $turn;
			$list[$i]['stays'][] = array('id' => $as, 'post' => (int)$target->id, 'from' => $turn + 1, 'to' => null);
			$list[$i]['transit'][] = $turn;
			Manager::insertIndividualNote(new IndividualNote(-1, $gamedata->id, $turn, $gamedata->phase, $ship->id, (int)$claim['from'],
				self::TRANSFER_NOTE, substr(self::getLabel($list[$i]['id']) . ' moves to #' . (int)$target->id, 0, 40),
				$list[$i]['id'] . ':' . (int)$target->id));
		}
	}

	/* Every post he could be moved to this turn (the payload's `targets`, own team only): mayMoveTo over the
	   ship's systems. Empty while he cannot move at all. */
	private static function moveTargets($ship, array $list, $i, $turn){
		$out = array();
		if (empty(self::$registry[$list[$i]['id']]['movable']) || !self::serves($list[$i], $turn)) return $out;
		foreach ($ship->systems as $system){
			if (self::mayMoveTo($ship, $list, $i, $system, $turn) !== null) $out[] = (int)$system->id;
		}
		return $out;
	}

	/* DBManager's bulk note loop hands every note here first: an OffTransfer or OffUsed note is recorded on the
	   ship and never handed to its system (§4.4's reason - a Jump Engine misreads a note it does not know).
	   true = taken. Their values are "id:to" and "id:ability". */
	public static function takeNote($ship, $note){
		if ($note->notekey !== self::TRANSFER_NOTE && $note->notekey !== self::USE_NOTE) return false;
		$parts = explode(':', (string)$note->notevalue, 2);
		if (count($parts) === 2){
			if ($note->notekey === self::TRANSFER_NOTE) $ship->recordOfficerTransfer($parts[0], $note->systemid, (int)$parts[1], $note->turn, (int)$note->id);
			else $ship->recordOfficerUse($parts[0], $note->systemid, $parts[1], $note->turn);
		}
		return true;
	}

	/* Is this officer aboard and serving on $turn (default: the turn this gamedata was loaded at)?
	   On a hot path, ask behind TacGamedata::$officersPresent. */
	public static function isActive($ship, $id, $turn = null){
		if ($turn === null) $turn = TacGamedata::$currentTurn;
		foreach ($ship->getOfficers() as $entry){
			if ($entry['id'] === $id) return self::serves($entry, $turn);
		}
		return false;
	}

	/* The earliest turn the post was destroyed: by a damage row of its own, or by its Structure block
	   (for a post spanning several locations, the turn the LAST of its blocks fell). null = never.
	   ⚠️ Mirrors ShipSystem::isDestroyed's cascade, but without "undestroyed": a revival by Self Repair
	   does not bring an officer back. The block's fall turn is the officer's out turn, which is right:
	   isDestroyed() lets a system fall off one turn AFTER its block, so he serves the turn it falls. */
	private static function disabledTurn($ship, $post){
		if ($post instanceof Fighter) return self::craftLostTurn($post);
		$turn = self::firstDestroyedTurn($post);
		if (!($post instanceof Structure) && !$post->getSurvivesStructureDestruction()){
			$blocks = $post->getStructureSystem();
			$cascade = null;
			if (is_array($blocks)){
				$all = true;
				foreach ($blocks as $block){
					if (!$block) continue; //isDestroyed() treats a missing block as fallen too
					$fell = self::structureFallTurn($ship, $block);
					if ($fell === null){ $all = false; break; }
					$cascade = ($cascade === null) ? $fell : max($cascade, $fell);
				}
				if (!$all) $cascade = null;
			} else if ($blocks){
				$cascade = self::structureFallTurn($ship, $blocks);
			}
			$turn = self::earlier($turn, $cascade);
		}
		return $turn;
	}

	//a section's block also falls with the PRIMARY Structure, as in ShipSystem::isDestroyed
	private static function structureFallTurn($ship, $block){
		$turn = self::firstDestroyedTurn($block);
		if ((int)$block->location !== 0){
			$primary = $ship->getStructureSystem(0);
			if ($primary && $primary !== $block) $turn = self::earlier($turn, self::firstDestroyedTurn($primary));
		}
		return $turn;
	}

	/* A fighter officer's craft (§4.4 rule 3; user ruling 2026-10-11): lost when it is destroyed - the
	   earliest damage row that destroyed it - or drops out - its DisengagedFighter critical, which is
	   permanent and so loads every turn. ⚠️ NOT DockedFighter or SplitLaunchedFighter, although
	   Fighter::isDestroyed() reports both as destroyed: a dock or a split launch never loses him. */
	private static function craftLostTurn($craft){
		$turn = self::firstDestroyedTurn($craft);
		foreach ($craft->criticals as $critical){
			if ($critical instanceof DisengagedFighter) $turn = self::earlier($turn, (int)$critical->turn);
		}
		return $turn;
	}

	private static function firstDestroyedTurn($system){
		$first = null;
		foreach ($system->damage as $damage){
			if ($damage->destroyed) $first = self::earlier($first, (int)$damage->turn);
		}
		return $first;
	}

	/* The earliest turn the system took damage through its armour (isDamagedOnTurn's test) - pre-battle
	   damage included (turn 0, armour 0: he never serves). A repair row is negative and never counts.
	   $stay (Stage 10): only while he held it - a gun damaged before he moved into it does not disable him. */
	private static function firstDamagedTurn($system, $stay = null){
		$first = null;
		foreach ($system->damage as $damage){
			if ($damage->damage <= $damage->armour) continue;
			if ($stay !== null && !self::within($damage->turn, $stay)) continue;
			$first = self::earlier($first, (int)$damage->turn);
		}
		return $first;
	}

	private static function earlier($a, $b){
		if ($a === null) return $b;
		if ($b === null) return $a;
		return min($a, $b);
	}

	/* ------------------------------------------------------------------ the kill roll */

	/* Criticals::setCriticals, right after Pass 1 (which made the posts' own rolls) and before Pass 2's
	   Self Repair, behind TacGamedata::$officersPresent. Every post holding an officer still in action
	   - or put out of it THIS turn, since "make a roll even if the system is totally destroyed" - that
	   took damage through its armour this turn (isDamagedOnTurn, Pass 1's own trigger) is tested once,
	   and 20 or more kills every officer posted there.
	   The roll is the post's own from Pass 1 when it made one this turn; otherwise (Pass 1 skips a
	   destroyed system, and a missile launcher whose magazine exploded never reached the parent) the
	   same formula is rolled here. A Structure post (Stage 19, the Expert Targeter) rolls on this turn's damage alone (blockKillRoll).
	   ⚠️ Writes straight through Manager::insertIndividualNote - a sweep inside advance()
	   ([[arch_individual_notes_and_phase_hooks]]) - and updates the in-memory list, so anything later
	   in this request reads him as killed.
	   A fighter officer never rolls (user ruling 2026-10-11): his craft has no d20 critical roll, and
	   rolling the ship formula for it here would kill him on any hit worth 20. */
	public static function rollKills($ships, $gamedata){
		$turn = (int)$gamedata->turn;
		foreach ($ships as $ship){
			$list = $ship->getOfficers();
			if (empty($list)) continue;
			$byPost = array();
			foreach ($list as $i => $entry){
				if (self::isFighterOfficer($entry['id'])) continue;
				if ($entry['how'] === 'killed') continue;
				if ($entry['out'] !== null && $entry['out'] < $turn) continue; //out on an earlier turn
				$byPost[$entry['post']][] = $i;
			}
			$changed = false;
			foreach ($byPost as $postId => $indexes){
				$post = $ship->getSystemById($postId);
				if ($post === null || !$post->isDamagedOnTurn($turn)) continue;
				if ($post instanceof Structure){
					$roll = self::blockKillRoll($ship, $post, $turn); //Stage 19: only an Expert Targeter lives in a block
				} else {
					$roll = $post->getCritRollOnTurn($turn);
					if ($roll === null) $roll = self::rollFor($ship, $post);
				}
				if ($roll < self::KILL_ROLL) continue;
				foreach ($indexes as $i){
					$id = $list[$i]['id'];
					$list[$i]['out'] = $turn;
					$list[$i]['how'] = 'killed';
					$changed = true;
					//notekey_human is varchar(40) and an overflow aborts the whole advance: the longest
					//planned label, "Expert Software Engineer killed", is 31
					Manager::insertIndividualNote(new IndividualNote(-1, $gamedata->id, $turn, $gamedata->phase,
						$ship->id, $post->id, self::KILL_NOTE, substr(self::getLabel($id) . ' killed', 0, 40), $id));
				}
			}
			if ($changed) $ship->setOfficers($list);
		}
	}

	/* ShipSystem::testCritical's formula for a post that made no roll this turn: d20 + floor(total
	   damage) + the system's and the ship's critRollMod - plus a Hyach scanner's "damage halved for
	   critical rolls", which its own testCritical adds to critRollMod around the parent's roll. Since
	   Stage 3 an Expert Scanner can be posted there, so a destroyed one must roll as a captured one
	   does. ⚠️ MIRROR of Scanner::testCritical and ElintScanner::testCritical (the ELINT one wraps the
	   Scanner one, so a hull with both abilities halves twice there, and here). */
	private static function rollFor($ship, $post){
		$mod = $post->critRollMod;
		if ($post instanceof Scanner){
			$half = round($post->getTotalDamage() / 2);
			if ($post instanceof ElintScanner && $ship->getSpecialAbilityValue("HyachELINTSensors")) $mod -= $half;
			if ($ship->hasSpecialAbility("HyachSensors")) $mod -= $half;
		}
		return Dice::d(20) + floor($post->getTotalDamage()) + $mod + $ship->critRollMod;
	}

	/* An officer in a STRUCTURE block (the Expert Targeter, Stage 19): d20 + the damage the block took THIS turn through its armour
	   + the block's and the ship's critRollMod (user ruling 2026-10-20). The block's own Pass 1 roll counts all its damage, as every
	   system's does, which on a block that has been fought over kills him at once. Reached only from rollKills, for a block holding
	   an officer that was damaged this turn - "heavily gated since this will be a rare occurrence". */
	private static function blockKillRoll($ship, $block, $turn){
		return Dice::d(20) + floor($block->damageReceivedOnTurn($turn)) + $block->critRollMod + $ship->critRollMod;
	}

	//DBManager's bulk note loop hands every OffKilled note here instead of to its system (§4.4)
	public static function isKillNote($note){
		return $note->notekey === self::KILL_NOTE;
	}

	//...and every OffMoved note (followFighter), for the same reason
	public static function isMoveNote($note){
		return $note->notekey === self::MOVE_NOTE;
	}

	/* ------------------------------------------------------------------ effects */

	/* Every apply method takes ($ship, $entry, $turn): the officer's runtime entry (his post) and the
	   turn the load is for. ⚠️ Each stat change is a MIRROR PAIR with the lobby's read-time preview in
	   officers.js (`stats`, reactorBonus, displayFireControl) - enhancementsDifferential.js compares them. */

	private static function applyHelmsman($ship, $entry, $turn){
		$ship->iniativebonus += 5;
	}

	/* Expert Helmsman and Expert Dogfighter (user request 2026-10-20): "wins ties" - TacGamedata::sortShips, behind
	   $officersPresent. A Helmsman's ship and a Dogfighter's flight tied with each other both win, so the usual
	   tie-break settles it. */
	public static function winsInitiativeTies($ship){
		return self::isActive($ship, 'OFF_HELM') || self::isActive($ship, 'OFF_DOG');
	}

	//Expert Helmsman: -1 on the meteor impact chart's die - RammingAttack::resolveMeteors
	public static function meteorChartModifier($ship){
		return self::isActive($ship, 'OFF_HELM') ? -1 : 0;
	}

	/* Expert Scanner: the EW points he lets the ship hold back for the late window, ON TOP of the EW
	   Detector ladder (user ruling 2026-10-10) - EW::getDetectorAllowance. The pool clamp, the window
	   and the write are the detectors' own and need nothing. ⚠️ MIRROR PAIR with officers.savedEwBonus. */
	public static function savedEwBonus($ship, $turn = null){
		return self::isActive($ship, 'OFF_SCAN', $turn) ? 1 : 0;
	}

	/* Expert Technician: the reactor's output, raised at load, and its critical rolls eased. `output` is
	   sent to every client unconditionally and power.js reads it, so the client's power balance needs
	   nothing for these two skills (§4.6: it reaches the enemy too, as the refit star's numbers do).
	   The -2 is the reactor's own critRollMod, which ShipSystem::testCritical reads - so it also eases
	   his kill roll, which is that same roll "including any modifications" (D3). Efficient Transfers
	   is the client's (officers.efficientTransferBonus). */
	private static function applyTechnician($ship, $entry, $turn){
		$reactor = $ship->getSystemById($entry['post']);
		if ($reactor === null) return;
		$reactor->output += self::technicianBonus($ship, $reactor, $turn);
		$reactor->critRollMod += self::TECH_CRIT_MOD;
	}

	/* Improved Power's +2, plus Improved Circuit Breakers: a third (rounded down, per system) of the
	   powerReq of EVERY system destroyed BEFORE this turn. isDestroyed($turn - 1) is the state at the
	   end of the previous turn, Self Repair revivals included (a revived system draws power again),
	   and pre-battle damage counts on turn 1. The reactor itself is never counted, and a system with no
	   power requirement gives nothing. A destroyed Battle Laser (6) gives 2.
	   ⚠️ MIRROR PAIR with officers.reactorBonus (the lobby, where only pre-battle damage exists). */
	public static function technicianBonus($ship, $post, $turn){
		$bonus = self::TECH_POWER;
		foreach ($ship->systems as $system){
			if ($system === $post || (int)$system->powerReq <= 0) continue;
			if (!$system->isDestroyed((int)$turn - 1)) continue;
			$bonus += intdiv((int)$system->powerReq, 3);
		}
		return $bonus;
	}

	/* Expert Geneticist: +2 to be hit is +2 on both profiles, the way Poor Crew's +1 and the C&C's
	   ProfileIncreased critical are written - so every to-hit path, server and client, already has it. */
	private static function applyGeneticist($ship, $entry, $turn){
		$ship->forwardDefense += 2;
		$ship->sideDefense += 2;
		$ship->toHitBonus -= 1;
	}

	/* Matter Weapons Expert: +1 to every non-null fire control of every Matter MODE, in fireControlArray
	   AND in the live fireControl when the current mode is Matter - Weapon::changeFiringMode re-reads the
	   array straight over fireControl, so a bump to the scalar alone is gone at the first mode switch
	   (the Gunsights trap, §3.1). isModified makes weapon::stripForJson send both, and the client
	   rebuilds the tooltip's fire control line from them (shipManager.systems.initializeSystem). */
	private static function applyMatterExpert($ship, $entry, $turn){
		foreach ($ship->systems as $system){
			if (!($system instanceof Weapon) || !self::isMatterWeapon($system)) continue;
			if (self::servesOn($ship, 'OFF_GUN', $system, $turn)) continue; //the Gunner's replace his (user ruling 2026-10-16)
			self::raiseMatterModes($system);
		}
	}

	//+1 on every non-null fire control of a weapon's Matter modes, live and per mode (applyMatterExpert's rule) - never a fixed one
	private static function raiseMatterModes($system){
		if (!empty($system->fireControlArray) && is_array($system->fireControlArray)){
			foreach ($system->fireControlArray as $mode => $fc){
				if (is_array($fc) && self::isMatterMode($system, $mode) && !$system->hasFixedFireControl($mode)) $system->fireControlArray[$mode] = self::bumpFireControl($fc);
			}
		}
		if (self::isMatterMode($system, $system->firingMode) && !$system->hasFixedFireControl($system->firingMode)) $system->fireControl = self::bumpFireControl($system->fireControl);
		$system->isModified = true;
	}

	/* Matter Weapons Expert: +1 damage, read by Weapon::getBonusDamage for a shot in a Matter mode - from
	   the C&C on every Matter weapon but one a Gunner serves on (his own damage bonus replaces it, user
	   ruling 2026-10-16), or from inside THIS weapon (OFF_MWEW). */
	public static function matterDamageBonus($ship, $weapon){
		$turn = TacGamedata::$currentTurn;
		if (self::servesOn($ship, 'OFF_MWEW', $weapon, $turn)) return 1;
		if (!self::isActive($ship, 'OFF_MWE', $turn)) return 0;
		return self::servesOn($ship, 'OFF_GUN', $weapon, $turn) ? 0 : 1;
	}

	//Is this officer posted on THIS system, and serving on $turn?
	private static function servesOn($ship, $id, $system, $turn){
		foreach ($ship->getOfficers() as $entry){
			if ($entry['id'] === $id && (int)$entry['post'] === (int)$system->id) return self::serves($entry, $turn);
		}
		return false;
	}

	/* ------------------------------------------------------------------ placed officers (Stage 8) */

	/* Expert Gunner, to hit: +1 on every non-null fire control of his weapon, every mode - in fireControlArray
	   AND the live fireControl, for the same mode-switch reason as the Matter expert's. Gunsights does the same
	   earlier in the load, so the two stack (+2). isModified sends both to every viewer, exactly as for
	   Gunsights (§4.6): the shooter's own hit chance needs them. */
	private static function applyGunner($ship, &$entry, $turn){
		$weapon = $ship->getSystemById($entry['post']);
		if (!($weapon instanceof Weapon)) return;
		//his Piercing shot, while he still has it, on a gun with no Piercing mode of its own: a mode to fire it in
		//(BEFORE the +1 below, so the added mode carries it too)
		if (self::abilityOpen($entry, 'pierce', $turn) && self::mayAddPiercing($weapon)) $entry['mode'] = self::addPiercingMode($weapon, self::spentOn($entry, 'pierce', $turn));
		self::raiseEveryMode($weapon);
	}

	/* +1 on every non-null fire control of every mode, live and per mode (applyGunner's rule; the Expert Turret Officer's too, Stage 22) -
	   never on a mode whose fire control is fixed (Weapon::hasFixedFireControl: the Antimatter Shredder's Shredder mode) */
	private static function raiseEveryMode($weapon){
		if (!empty($weapon->fireControlArray) && is_array($weapon->fireControlArray)){
			foreach ($weapon->fireControlArray as $mode => $fc){
				if (is_array($fc) && !$weapon->hasFixedFireControl($mode)) $weapon->fireControlArray[$mode] = self::bumpFireControl($fc);
			}
		}
		if (!$weapon->hasFixedFireControl($weapon->firingMode)) $weapon->fireControl = self::bumpFireControl($weapon->fireControl);
		$weapon->isModified = true;
	}

	/* Expert Gunner, damage: +1 per die his weapon rolls, each die capped at its face ("a roll of 10 on 1d10
	   would not be increased") - Weapon::getFinalDamage adds it to Elite Crew's Dice::$perDieBonus, behind
	   $officersPresent. A weapon whose damage is not rolled gains nothing, which is the rule. */
	public static function gunnerDieBonus($ship, $weapon){
		return self::servesOn($ship, 'OFF_GUN', $weapon, TacGamedata::$currentTurn) ? 1 : 0;
	}

	/* ------------------------------------------------------------------ once per battle, and faster reloads (Stage 10) */

	//what a spent ability is called in its note and the tooltip
	private static $abilityNames = array('reload' => 'faster reload', 'pierce' => 'Piercing shot', 'called' => 'called shot',
		'capture' => 'capture assault', //Stage 24: the Breaching Expert's (spendCaptureAssault)
		'speech' => 'speech');          //Stage 25: the Expert Political Officer's (saveSpeech)

	/* Is this ability still his on $turn? Spent ON $turn still counts: that turn's shot is the one that spent it
	   (so every order his gun fires that turn, and a replay of it, reads the same). */
	private static function abilityOpen(array $entry, $ability, $turn){
		return !isset($entry['abilities'][$ability]) || (int)$entry['abilities'][$ability] >= (int)$turn;
	}

	/* The OffUsed notes (takeNote), each matched to the officer who held that post that turn - so a spent ability
	   goes with him to another weapon ("tied to the Officer, not the weapon", user 2026-10-16). */
	private static function resolveUses($ship, array &$list){
		$uses = $ship->getOfficerUses();
		if (empty($uses)) return;
		foreach ($list as $i => $entry){
			foreach ($uses as $use){
				if ($entry['out'] !== null && $entry['out'] < $use['turn']) continue; //a fallen officer left on that gun spent nothing
				foreach (self::staysOf($entry) as $stay){
					if ($stay['id'] !== $use['id'] || (int)$stay['post'] !== (int)$use['post'] || !self::within($use['turn'], $stay)) continue;
					$had = isset($list[$i]['abilities'][$use['ability']]) ? $list[$i]['abilities'][$use['ability']] : null;
					$list[$i]['abilities'][$use['ability']] = ($had === null) ? (int)$use['turn'] : min($had, (int)$use['turn']);
				}
			}
		}
	}

	/* Spend officer $i's $ability on $turn: in memory at once (a second order this request sees it) and as one OffUsed
	   note on his post, written straight through - firing and the turn change run inside advance(). Once only. */
	private static function spend($ship, $i, $ability, $turn, $gamedata){
		$list = $ship->getOfficers();
		if (isset($list[$i]['abilities'][$ability]) && (int)$list[$i]['abilities'][$ability] <= (int)$turn) return;
		$list[$i]['abilities'][$ability] = (int)$turn;
		$ship->setOfficers($list);
		$ship->recordOfficerUse($list[$i]['id'], $list[$i]['post'], $ability, $turn);
		Manager::insertIndividualNote(new IndividualNote(-1, $gamedata->id, (int)$turn, $gamedata->phase, $ship->id, (int)$list[$i]['post'],
			self::USE_NOTE, substr(self::getLabel($list[$i]['id']) . ': ' . self::$abilityNames[$ability] . ' used', 0, 40),
			$list[$i]['id'] . ':' . $ability));
	}

	//the index of the officer serving as $id IN this system on $turn, or null
	private static function servingIndex($ship, $id, $system, $turn){
		foreach ($ship->getOfficers() as $i => $entry){
			if ($entry['id'] === $id && (int)$entry['post'] === (int)$system->id && self::serves($entry, $turn)) return $i;
		}
		return null;
	}

	/* ⭐ THE GUNNER'S SHOT BONUS - Weapon::calculateHitBase, in d20 units, behind $officersPresent, once the order's
	   mode is set. One ability per shot (user rulings 2026-10-16):
	     a Piercing shot      his Piercing ability, if he still has it: on a gun that may already pierce, +4 - the
	                          usual -4 waived ("no penalty where it is"); in the mode added for him, nothing more
	                          (that mode carries the penalty) - either way it is spent;
	     a called shot        otherwise, +3 ("a called shot at +3"), spent - never on a Piercing shot, nor (Stage 19) on a gun
	                          a serving Expert Targeter covers, whose +4 is that shot's instead (targeterShotBonus), nor on an
	                          Antimatter Shredder's attack at a fighter (bonusCall);
	   The faster reload is the turn change's (fasterReload). ⚠️ MIRROR PAIR with officers.gunnerShotBonus (JS). */
	public static function gunnerShotBonus($ship, $weapon, $called, $gamedata){
		$turn = (int)$gamedata->turn;
		$i = self::servingIndex($ship, 'OFF_GUN', $weapon, $turn);
		if ($i === null) return 0;
		$entry = $ship->getOfficers()[$i];
		if ($weapon->damageType === 'Piercing'){
			if (!self::abilityOpen($entry, 'pierce', $turn)) return 0;
			self::spend($ship, $i, 'pierce', $turn, $gamedata);
			return (isset($entry['mode']) && (int)$weapon->firingMode === (int)$entry['mode']) ? 0 : self::PIERCE_PENALTY;
		}
		if (!self::bonusCall($weapon, $called) || !self::abilityOpen($entry, 'called', $turn)) return 0;
		//Stage 19 (user ruling 2026-10-20): on a gun a serving Expert Targeter covers, his +4 is the called shot's bonus - the
		//Gunner's +3 is neither used nor spent, so it is still his if he moves or the Targeter falls
		if (self::targeterFor($ship, $weapon, $turn) !== null) return 0;
		self::spend($ship, $i, 'called', $turn, $gamedata);
		return self::GUNNER_CALLED_BONUS;
	}

	/* Is $called a called shot an officer's called-shot bonus reaches (the Gunner's +3, the Targeter's +4)? Never one fired in a mode with a
	   hit calculation of its own (Weapon::hasFixedFireControl): the Antimatter Shredder's attacks at fighters are called shots, one at each
	   craft, but its Shredder mode gains no officer's bonus and spends none (user 2026-10-21). ⚠️ MIRROR PAIR with officers.bonusCall (JS). */
	private static function bonusCall($weapon, $called, $mode = null){
		return $called && !(($weapon instanceof Weapon) && $weapon->hasFixedFireControl($mode ?? $weapon->firingMode));
	}

	/* ⭐ A TURN OFF THE RELOAD - Weapon::onAdvancingGamedata, behind $officersPresent, on the loading the new turn
	   starts with (phase -1; the gamedata is the one the last turn was fought on, so the officers are that
	   turn's). A weapon that fired last turn and is reloading starts one turn further on, never past a full
	   load ("never better than once a turn" - a one-turn gun gains nothing, and spends nothing):
	     the weapon-mounted Matter Weapons Expert (`reload`)  after every firing while he served in it;
	     the Expert Gunner (`abilities`)                      ONCE: after the first firing that spent neither his
	                                                          Piercing nor his called shot (a firing that did
	                                                          reloads normally, and it waits for the next one).
	   Granted at the start of the reload: losing him half way through does not take it back. Any firing
	   counts - an interception starts a reload too. The client reads the loading the server writes, so
	   isLoaded agrees with no mirror. */
	public static function fasterReload($ship, $weapon, $data, $gamedata){
		if ((int)$gamedata->phase !== -1) return;
		$fired = (int)$gamedata->turn - 1;
		if ($fired < 1 || !$weapon->firedOnTurn($fired)) return;
		if ((int)$data->loading >= (int)$weapon->getNormalLoad()) return;
		foreach ($ship->getOfficers() as $i => $entry){
			if ((int)$entry['post'] !== (int)$weapon->id || !self::serves($entry, $fired)) continue;
			$officer = self::$registry[$entry['id']];
			if (!empty($officer['reload'])){
				$data->loading += 1;
			} else if (!empty($officer['abilities']) && self::abilityOpen($entry, 'reload', $fired)
				&& !self::spentOn($entry, 'pierce', $fired) && !self::spentOn($entry, 'called', $fired)
				&& !self::firingSpentAbility($weapon, $entry, $fired, $gamedata)){
				self::spend($ship, $i, 'reload', $fired, $gamedata);
				$data->loading += 1;
			}
			return;
		}
	}

	private static function spentOn(array $entry, $ability, $turn){
		return isset($entry['abilities'][$ability]) && (int)$entry['abilities'][$ability] === (int)$turn;
	}

	/* Did this weapon's firing on $fired spend his Piercing or his called shot? Read off its own fire orders, by
	   gunnerShotBonus's rule (a Piercing mode while his Piercing was his; else a standard called shot while his
	   called shot was), because the spend itself is not in this gamedata: Manager::advanceGameState runs the turn
	   change on the gamedata it loaded BEFORE the firing, and FireGamePhase::advance resolved the shots on a
	   gamedata of its own - so its OffUsed note and its in-memory spend are both invisible here (game 4484: the
	   faster reload went with a Piercing shot on turns 1 and 6). Interceptions spend neither. */
	private static function firingSpentAbility($weapon, array $entry, $fired, $gamedata){
		foreach ($weapon->fireOrders as $order){
			if ((int)$order->turn !== (int)$fired || (int)$order->weaponid !== (int)$weapon->id) continue;
			if (strpos((string)$order->type, 'ntercept') !== false) continue;
			$mode = (int)$order->firingMode;
			$type = isset($weapon->damageTypeArray[$mode]) ? $weapon->damageTypeArray[$mode] : $weapon->damageType;
			if ($type === 'Piercing'){
				if (self::abilityOpen($entry, 'pierce', $fired)) return true;
				continue;
			}
			if ((int)$order->calledid < 1 || !self::bonusCall($weapon, true, $mode) || !self::abilityOpen($entry, 'called', $fired)) continue;
			$target = $gamedata->getShipById($order->targetid);
			$called = $target ? $target->getCalledSystemAsAimed($order) : null;
			if ($called !== null && $called->getTargetProfileOverride() === null) return true;
		}
		return false;
	}

	/* ⭐ A REPLAY'S FIRED PIERCING MODE. Manager::getReplayGameData (the replay, and the printed combat log) builds the
	   past turn's data on a gamedata constructed at the game's CURRENT turn and only then sets it back to $turn - so
	   apply() judged every Gunner's Piercing by the current turn, and one spent ON $turn already read as gone: the
	   mode it was fired in was never added, and the log and replay named the shot by the weapon's own mode (game
	   4484, turn 6: "Raking"). This adds that mode back to the weapon he held on $turn, for every viewer - it was
	   fired. Nothing else about the officers is re-judged. */
	public static function restoreFiredModes($gamedata, $turn){
		if (!TacGamedata::$officersPresent) return;
		foreach ($gamedata->ships as $ship){
			if (!($ship instanceof BaseShip) || !$ship->hasOfficers()) continue;
			foreach ($ship->getOfficers() as $entry){
				if (!self::spentOn($entry, 'pierce', $turn)) continue;
				$stay = self::stayOn($entry, $turn);
				$weapon = ($stay !== null && $stay['id'] === 'OFF_GUN') ? $ship->getSystemById($stay['post']) : null;
				if ($weapon instanceof Weapon && self::mayAddPiercing($weapon)) self::addPiercingMode($weapon, true);
			}
		}
	}

	/* A gun his Piercing ability adds a mode to (user ruling 2026-10-16): direct fire, ONE firing mode, Standard or
	   Raking damage - 65% of the guns he can be bought for. Nothing in such a weapon's damage code branches on a
	   mode number. A gun with Piercing already uses its own mode (gunnerShotBonus); Pulse, Flash, ballistic,
	   hex-targeted and other multi-mode weapons offer no Piercing shot - he can still use it from another gun.
	   ⚠️ Nor a magazine-fed gun (checkAmmoMagazine - the Grome railguns): its modes ARE its shells, built from the
	   magazine, so one shell type reads as one mode; nor a dual / duo mount, whose modes pick a sub-weapon. */
	private static function mayAddPiercing($weapon){
		if ($weapon->ballistic || $weapon->hextarget || count($weapon->firingModes) !== 1) return false;
		if ($weapon->checkAmmoMagazine || $weapon instanceof DualWeapon || $weapon instanceof DuoWeapon) return false;
		if (!empty($weapon->damageTypeArray) && in_array('Piercing', $weapon->damageTypeArray, true)) return false;
		return in_array($weapon->damageType, array('Standard', 'Raking'), true);
	}

	/* ONE MORE FIRING MODE: a copy of the weapon's only mode, as Piercing, at the usual Piercing fire control (-4,
	   no shots at fighters - the Battle Laser's own Piercing mode) and an early resolution priority. Every per-mode
	   array the weapon fills gets the new key, since changeFiringMode reads them by mode on both ends; the weapon
	   sends them (Weapon::markAddedMode) because the static blueprint has never heard of the mode - to his own team,
	   and to anyone else only when $fired (spent on the turn this load is for: a replay of the shot, the printed log;
	   user request 2026-10-16 - it would point at him). Rebuilt at every load while the ability is his; gone the turn
	   after he spends it (settleFiringModes). Returns its number. */
	private static function addPiercingMode($weapon, $fired){
		$source = (int)array_key_first($weapon->firingModes);
		$mode = max(array_keys($weapon->firingModes)) + 1;
		if (empty($weapon->damageTypeArray)) $weapon->damageTypeArray = array($source => $weapon->damageType);
		if (empty($weapon->fireControlArray)) $weapon->fireControlArray = array($source => $weapon->fireControl);
		if (empty($weapon->priorityArray)) $weapon->priorityArray = array($source => $weapon->priority);
		$fields = array();
		foreach (get_object_vars($weapon) as $name => $value){ //public fields only, from here: what the client merges
			if (substr($name, -5) !== 'Array' || !is_array($value) || !array_key_exists($source, $value)) continue;
			if ($weapon->splitArcs && ($name === 'startArcArray' || $name === 'endArcArray')) continue; //a list of arcs, not modes
			$weapon->{$name}[$mode] = $value[$source];
			$fields[] = $name;
		}
		$weapon->firingModes[$mode] = 'Piercing';
		$weapon->damageTypeArray[$mode] = 'Piercing';
		$fc = $weapon->fireControlArray[$source];
		$pierced = array();
		foreach ($fc as $k => $value) $pierced[$k] = ((int)$k === 0 || $value === null) ? null : (int)$value - self::PIERCE_PENALTY;
		$weapon->fireControlArray[$mode] = $pierced;
		$weapon->priorityArray[$mode] = self::PIERCE_PRIORITY;
		$fields[] = 'firingModes';
		$weapon->markAddedMode($mode, array_unique($fields), $fired);
		if ((int)$weapon->firingMode === $mode) $weapon->changeFiringMode($mode); //a loading row may already name it
		return $mode;
	}

	/* A gun left in a mode it no longer has - the added Piercing mode, gone the turn after it was spent, still named
	   by the gun's loading row - goes back to its first mode. Every gun a Gunner has held. */
	private static function settleFiringModes($ship, array $list){
		foreach ($list as $entry){
			foreach (self::staysOf($entry) as $stay){
				if ($stay['id'] !== 'OFF_GUN') continue;
				$weapon = $ship->getSystemById($stay['post']);
				if ($weapon instanceof Weapon && !isset($weapon->firingModes[$weapon->firingMode])) $weapon->changeFiringMode((int)array_key_first($weapon->firingModes));
			}
		}
	}

	//Matter Weapons Expert in a weapon: the C&C variant's +1 fire control, on his own weapon's Matter modes only
	private static function applyWeaponMatterExpert($ship, $entry, $turn){
		$weapon = $ship->getSystemById($entry['post']);
		if ($weapon instanceof Weapon && self::isMatterWeapon($weapon)) self::raiseMatterModes($weapon);
	}

	/* "Only for factions with matter weapons" - asked of the hull, since a hull without one gains nothing
	   from him. A weapon counts when any of its modes is Matter. */
	private static function hasMatterWeapon($ship){
		foreach ($ship->systems as $system){
			if ($system instanceof Weapon && self::isMatterWeapon($system)) return true;
		}
		return false;
	}

	private static function isMatterWeapon($weapon){
		if ($weapon->weaponClass === 'Matter') return true;
		return is_array($weapon->weaponClassArray) && in_array('Matter', $weapon->weaponClassArray, true);
	}

	//a mode missing from weaponClassArray keeps the weapon's own class, as changeFiringMode leaves it
	private static function isMatterMode($weapon, $mode){
		$class = isset($weapon->weaponClassArray[$mode]) ? $weapon->weaponClassArray[$mode] : $weapon->weaponClass;
		return $class === 'Matter';
	}

	/* +1 to every NON-NULL entry. null means "cannot target this size class" and must stay null:
	   null + 1 is 1 in PHP (Enhancements::sysEnhBumpFireControl's warning). */
	private static function bumpFireControl($fireControl){
		if (!is_array($fireControl)) return $fireControl;
		foreach ($fireControl as $i => $fc){
			if ($fc !== null) $fireControl[$i] = (int)$fc + 1;
		}
		return $fireControl;
	}

	/* ------------------------------------------------------------------ Expert Navigator (Stage 6) */

	/* The rulebook's turn cost levels, in descending order: 0.83 is five sixths and 0.16 one sixth, written
	   to two places as the hulls write 0.33 and 0.66. Shared with the Hyach Maneuvering Specialist
	   (turnCostLevelDown). ⚠️ MIRROR PAIR with officers.TURN_COST_LADDER. */
	private static $turnCostLadder = array(2, 1.75, 1.66, 1.5, 1.33, 1.25, 1, 0.83, 0.75, 0.66, 0.5, 0.33, 0.25, 0.16, 0.1);

	/* Every number is a ship field movement.js reads as it is - published to every viewer (`publish`: a
	   displayed stat is public, user ruling 2026-10-10). ⚠️ MIRROR PAIR with officers.navigatorStat. */
	private static function applyNavigator($ship, $entry, $turn){
		$ship->turncost = self::turnCostLevelDown($ship->turncost);
		$ship->pivotcost = self::navigatorThrustCost($ship->pivotcost);
		$ship->rollcost = self::navigatorThrustCost($ship->rollcost);
		$ship->accelcost = self::navigatorAccelCost($ship->accelcost);
	}

	/* One level down the ladder: the highest level below the hull's cost, compared in hundredths (so 1.0
	   and 1 are one level, and 0.8 - Gemini AM - goes to 0.75). 0.1 has no level below it and stays; so
	   does 0 (cannot be lower) and a "cannot" sentinel.
	   ⭐ THE turn cost step: the Expert Navigator's, and the Hyach Maneuvering Specialist's (user ruling
	   2026-10-15: "mirror the Expert Navigator steps"). With both, the Navigator's passive step comes first
	   (Officers::apply, in onConstructed) and the Specialist's active one after it (onIndividualNotesLoaded),
	   so the ship drops two levels. ⚠️ MIRROR PAIR with officers.turnCostLevelDown. */
	public static function turnCostLevelDown($cost){
		if ($cost <= 0 || $cost >= self::NAV_CANNOT) return $cost;
		$hundredths = (int)round($cost * 100);
		foreach (self::$turnCostLadder as $level){
			if ((int)round($level * 100) < $hundredths) return $level;
		}
		return $cost;
	}

	//pivot and roll: "reduced by 1 point (minimum 1)"; "ships that already have a cost of 0 gain no advantage"
	public static function navigatorThrustCost($cost){
		if ($cost <= 0 || $cost >= self::NAV_CANNOT) return $cost;
		return max(1, $cost - 1);
	}

	/* "Reduced by 25%. Retain all fractions until the full acceleration/deceleration cost has been determined,
	   then round any remainder up": so the FIELD keeps the fraction (a whole cost x0.75 is always a quarter,
	   exact in floats) and the client rounds a turn's total (getSpeedChangeCost). The hulls write whole
	   numbers, and PHP's / gives a whole number back when it divides exactly, so a 4 reads 3, not 3.0. */
	public static function navigatorAccelCost($cost){
		if ($cost <= 0 || $cost >= self::NAV_CANNOT) return $cost;
		return $cost * 3 / 4;
	}

	/* ------------------------------------------------------------------ Expert Jump Officer (Stage 7) */

	/* The drive's own rules, on the drive: range +1 (and with it the "holder drifted too far" distance, as
	   the Jump Accelerator's +2 does), one turn more before the hold cap, and the failure cut - all read off
	   the flag this sets, which exists only while he serves on this load's turn. JumpEngine::stripForJson
	   publishes the range and the tooltip to every viewer (a displayed stat is public, user ruling
	   2026-10-10), and the cap goes out as vortexMaxTurns while a jump point stands. */
	private static function applyJumpOfficer($ship, $entry, $turn){
		$engine = $ship->getSystemById($entry['post']);
		if ($engine instanceof JumpEngine) $engine->applyJumpOfficer(self::JUMP_RANGE);
	}

	/* "A -1 shift to the vortex scatter chart (as listed in the modifiers) if his ship is the one opening the
	   vortex" - JumpEngine::getExitDeviationModifier, behind $officersPresent. */
	public static function exitScatterShift($opener, $turn){
		return self::isActive($opener, 'OFF_JUMP', $turn) ? self::JUMP_SCATTER_SHIFT : 0;
	}

	/* "He can cancel 60 degrees of unintended facing change caused by a vortex shift (this does not allow him
	   to alter the facing from its original position)" - JumpEngine::openExitVortex turns the doorway one
	   step back towards the declared facing, never past it. The doorway, so every unit riding it comes out
	   facing the way it does. */
	public static function cancelsFacingShift($opener, $turn){
		return self::isActive($opener, 'OFF_JUMP', $turn);
	}

	/* "Any initiative penalty produced by scatter is reduced by 20 for his unit only ... not an initiative
	   bonus" - BaseShip::getReinforcementArrivalIniModifier caps the sum at 0. His own unit: a rider through
	   his doorway keeps its full penalty, and a rider with an officer of its own has it reduced. */
	public static function arrivalPenaltyRelief($unit, $turn){
		return self::isActive($unit, 'OFF_JUMP', $turn) ? self::JUMP_INI_RELIEF : 0;
	}

	/* ------------------------------------------------------------------ Expert Laser Technician (Stage 11) */

	/* "Spinal lasers score +3 damage, blast lasers score +2 damage, and all other laser-category weapons score +1".
	   By class, so a subclass inherits its parent's (none exists today). ⚠️ MIRROR PAIR with officers.LASER_BONUS
	   (JS), which is keyed by each class's $name - officersHarness compares the two over every laser in the statics. */
	private static $laserClassBonus = array('SpinalLaser' => 3, 'BlastLaser' => 2, 'ImprovedBlastLaser' => 2, 'LtBlastLaser' => 2);

	public static function laserFlatBonus($weapon){
		foreach (self::$laserClassBonus as $class => $bonus){
			if ($weapon instanceof $class) return $bonus;
		}
		return 1;
	}

	/* His flat bonus on a shot - Weapon::getBonusDamage, for a shot in a Laser mode (weaponClass is the fired mode's
	   by then, so the pulse half of a Laser/Pulse Array gains nothing), behind $officersPresent. Added with the
	   weapon's other bonus damage, after its own reductions. A serving Expert Gunner's +1 per die REPLACES it on his
	   laser (user ruling 2026-10-17, the Gunner's "does not combine ... with other elite officers"). */
	public static function laserDamageBonus($ship, $weapon){
		$turn = TacGamedata::$currentTurn;
		if (!self::isActive($ship, 'OFF_LASR', $turn)) return 0;
		if (self::servesOn($ship, 'OFF_GUN', $weapon, $turn)) return 0;
		return self::laserFlatBonus($weapon);
	}

	/* What each laser has used of its boost this turn: weapon => array(turn => points). In memory only - every hit a
	   weapon makes in a turn is resolved in one request (FireGamePhase::advance) on one weapon object, and the entry
	   goes with the object. */
	private static $laserBoostSpent = null;

	/* ⭐ THE POWER BOOST - Weapon::getFinalDamage, behind $officersPresent, after the weapon's own reductions (as his
	   flat bonus): "each point of power provides +1 extra damage ... A weapon's total damage cannot be increased by
	   more than 50% through the use of this ability (e.g., if the base roll was 12 damage, it could be raised to 18
	   with 6 points of power, but any additional energy is lost)". $rolled is the roll as the weapon made it
	   (getDamage, so Elite Crew's and a Gunner's per-die bonuses are part of it); half of it, rounded down, is the
	   most one hit can gain. The points bought are the laser's boost levels on the order's turn - the generic type-2
	   power entries (no maximum: a laser is never boostable without him, and the client's boost has no ceiling),
	   each costing LASER_BOOST_COST power in the client's balance.
	   ONE POOL A TURN (user ruling 2026-10-17): the points are the laser's extra damage for the turn, handed to its
	   hits in the order they resolve - each pulse of a pulse laser, each gun's shot - until spent; what no hit can
	   take is lost. A hit on a Chameleon simulacrum alone (Weapon::fireChameleonPhantomOnly) is shown what a real
	   hit would get but spends nothing: the real hull was not hit, and a phantom sheet must read like a real one. */
	public static function laserBoostDamage($ship, $weapon, $rolled, $target, $fireOrder){
		if ($weapon->weaponClass !== 'Laser') return 0;
		$turn = (int)$fireOrder->turn;
		if (!self::isActive($ship, 'OFF_LASR', $turn)) return 0;
		$bought = self::boostOn($weapon, $turn); //boost LEVELS - a level is +1 damage, and costs LASER_BOOST_COST power
		if ($bought < 1) return 0;
		if (self::$laserBoostSpent === null) self::$laserBoostSpent = new WeakMap();
		$pool = isset(self::$laserBoostSpent[$weapon]) ? self::$laserBoostSpent[$weapon] : array();
		$spent = isset($pool[$turn]) ? $pool[$turn] : 0;
		$add = max(0, min($bought - $spent, (int)floor(max(0, $rolled) / self::LASER_BOOST_CAP)));
		if ($add > 0 && empty($target->chameleonIsPhantom)){
			$pool[$turn] = $spent + $add;
			self::$laserBoostSpent[$weapon] = $pool;
		}
		return $add;
	}

	//the power a system was boosted by on $turn: its type-2 power entries, as every boostable system counts them
	private static function boostOn($system, $turn){
		$boost = 0;
		foreach ($system->power as $power){
			if ((int)$power->turn === (int)$turn && (int)$power->type === 2) $boost += (int)$power->amount;
		}
		return $boost;
	}

	/* Weapon::stripForJson (through weaponForJson), for each laser of a ship he is aboard (serving or not), behind $officersPresent:
	   - his own team (and everybody once the game is over) is told the laser is BOOSTABLE while he serves - the
	     client then offers the generic boost buttons, with no maximum (no laser class sets one) - and what a point
	     costs, whenever its power entries are sent (power.js prices any boost it finds by boostEfficiency);
	   - anyone else gets the laser's power entries WITHOUT its boosts (user ruling 2026-10-17): no laser is boostable
	     without him, so a boosted one would name him. The extra damage still shows once the shots land. */
	private static function laserForJson($ship, $weapon, $strippedSystem){
		if (!self::isLaserWeapon($weapon) || !self::isAboard($ship, 'OFF_LASR')) return;
		if (TacGamedata::$currentGameFinished || $ship->isRevealedToCurrentViewer()){
			$strippedSystem->boostEfficiency = self::LASER_BOOST_COST;
			if (self::isActive($ship, 'OFF_LASR')) $strippedSystem->boostable = true;
			return;
		}
		self::stripBoosts($strippedSystem);
	}

	//a system's payload power entries without its boosts (type 2) - no power key at all when nothing else is left
	private static function stripBoosts($strippedSystem){
		if (empty($strippedSystem->power)) return;
		$kept = array();
		foreach ($strippedSystem->power as $power){
			if ((int)$power->type !== 2) $kept[] = $power;
		}
		if (empty($kept)) unset($strippedSystem->power);
		else $strippedSystem->power = $kept;
	}

	//Is this officer on the unit's list at all - serving, or out of action?
	private static function isAboard($ship, $id){
		foreach ($ship->getOfficers() as $entry){
			if ($entry['id'] === $id) return true;
		}
		return false;
	}

	//"Not available on ships without laser weapon(s)": a weapon with any Laser mode
	private static function hasLaserWeapon($ship){
		foreach ($ship->systems as $system){
			if ($system instanceof Weapon && self::isLaserWeapon($system)) return true;
		}
		return false;
	}

	//⚠️ MIRROR PAIR with officers.isLaserWeapon (JS)
	private static function isLaserWeapon($weapon){
		if ($weapon->weaponClass === 'Laser') return true;
		return is_array($weapon->weaponClassArray) && in_array('Laser', $weapon->weaponClassArray, true);
	}

	/* Weapon::stripForJson's one officer hook, behind $officersPresent: the Laser Technician's boost (laserForJson), the
	   Plasma Scientist's web range (webForJson) and the Ion Technician's thrust boost (ionForJson). */
	public static function weaponForJson($ship, $weapon, $strippedSystem){
		self::laserForJson($ship, $weapon, $strippedSystem);
		self::webForJson($ship, $weapon, $strippedSystem);
		self::ionForJson($ship, $weapon, $strippedSystem);
	}

	/* ------------------------------------------------------------------ Expert Plasma Scientist (Stage 12) */

	/* His flat bonus by class: "All medium or heavy plasma weapons on the ship score +1 damage (this would include medium
	   plasma cannons and larger, as well as plasma streams, plasma accelerators, etc.). Fuser torpedoes score +2 damage,
	   not +1." RULED (user 2026-10-17): every plasma weapon but the LIGHT ones listed here at 0 - so a new or custom
	   plasma weapon is +1 until it is listed. The Plasma Web's bonus is its range, not damage. By class (instanceof), so a
	   subclass inherits its parent's. ⚠️ MIRROR PAIR with officers.PLASMA_BONUS (JS), keyed by each class's $name -
	   officersHarness compares the two over every plasma weapon in the statics. */
	private static $plasmaClassBonus = array(
		'Fuser' => 2, 'RangedFuser' => 2, 'FuserArray' => 2,
		'LightPlasma' => 0, 'LightPlasmaBolter' => 0, 'LtPlasmaProjector' => 0, 'NexusLightEnhPlasma' => 0, 'NexusLightMagGun' => 0,
		'EWPointPlasmaGun' => 0, 'EWHeavyPointPlasmaGun' => 0, 'PlasmaTorch' => 0, 'TrekPlasmaBurst' => 0, 'PakmaraPlasmaWeb' => 0,
	);

	public static function plasmaFlatBonus($weapon){
		foreach (self::$plasmaClassBonus as $class => $bonus){
			if ($weapon instanceof $class) return $bonus;
		}
		return 1;
	}

	/* His flat bonus on a hit - Weapon::getFinalDamage, behind $officersPresent, for a shot in a Plasma mode, AFTER the
	   weapon's getDamageMod: the Plasma Bolters and the Nexus Enhanced Plasmas override that method and throw away the
	   parent's result, so a bonus added through getBonusDamage would never reach them. */
	public static function plasmaDamageBonus($ship, $weapon){
		if ($weapon->weaponClass !== 'Plasma') return 0;
		if (!self::isActive($ship, 'OFF_PLAS')) return 0;
		return self::plasmaFlatBonus($weapon);
	}

	/* The hexes of a plasma shot's range that cost it no damage: "ignore the first two hexes traveled when reducing damage
	   scored against an opponent ... a weapon which suffered -1 point of damage per 2 hexes which struck an enemy 10 hexes
	   away would lose only 4 points of damage, not 5". Weapon::getRangeDamageDistance takes them off the distance every
	   range damage rule reads - so a Plasma Bolter's free 15 hexes become 17. Every plasma weapon, light ones included. */
	public static function plasmaRangeRelief($ship){
		return self::isActive($ship, 'OFF_PLAS') ? self::PLASMA_RANGE_RELIEF : 0;
	}

	/* The systems he improves, at load:
	   - every Plasma Battery holds PLASMA_BATTERY_BONUS more while it stands ("They cannot operate if completely
	     destroyed (despite the +1 power bonus)" - PlasmaBattery::getMaxCapacity), and rolls 3 lower for its criticals;
	     one still holding its deployment charge starts FULL at the new capacity (user ruling 2026-10-17). Reactor::
	     effectCriticals has already folded every battery's charge into each reactor's output for Initial Orders - in the
	     per-system loop, BEFORE this pass - so what he changed is added there too, the same way;
	   - every Plasma Web's Anti-Fighter range is 1 longer (webForJson publishes it; the client takes 2 off it under the
	     web's range critical, so a crippled web reaches 2, not 1 - "even if suffering from a critical hit"). */
	private static function applyPlasmaScientist($ship, $entry, $turn){
		$gained = 0;
		foreach ($ship->systems as $system){
			if ($system instanceof PlasmaBattery){
				$before = $system->getOutput();
				$system->setCapacityBonus(self::PLASMA_BATTERY_BONUS);
				if ($system->holdsDeploymentCharge()) $system->powerCurr = $system->getMaxCapacity();
				$system->critRollMod += self::PLASMA_BATTERY_CRIT_MOD;
				$gained += $system->getOutput() - $before;
			} else if ($system instanceof PakmaraPlasmaWeb){
				if (isset($system->rangeArray[self::PLASMA_WEB_MODE])) $system->rangeArray[self::PLASMA_WEB_MODE] += self::PLASMA_WEB_RANGE;
				if ((int)$system->firingMode === self::PLASMA_WEB_MODE) $system->range += self::PLASMA_WEB_RANGE;
			}
		}
		if ($gained !== 0 && TacGamedata::$currentPhase <= 1){
			foreach ($ship->systems as $system){
				if ($system instanceof Reactor) $system->outputMod += $gained;
			}
		}
	}

	/* A Plasma Web's range, to EVERY viewer while he serves (user ruling 2026-10-17: a displayed stat is public, only the
	   officer is hidden) - Weapon::stripForJson sends range only when the weapon has a critical, and the client merges
	   the payload over the static blueprint, which reads 3. */
	private static function webForJson($ship, $weapon, $strippedSystem){
		if (!($weapon instanceof PakmaraPlasmaWeb) || !self::isActive($ship, 'OFF_PLAS')) return;
		$strippedSystem->range = $weapon->range;
		$strippedSystem->rangeArray = $weapon->rangeArray;
	}

	//"Not available on ships without plasma weapon(s)": a weapon with any Plasma mode (a Plasma Web counts)
	private static function hasPlasmaWeapon($ship){
		foreach ($ship->systems as $system){
			if ($system instanceof Weapon && self::isPlasmaWeapon($system)) return true;
		}
		return false;
	}

	//⚠️ MIRROR PAIR with officers.isPlasmaWeapon (JS)
	private static function isPlasmaWeapon($weapon){
		if ($weapon->weaponClass === 'Plasma') return true;
		return is_array($weapon->weaponClassArray) && in_array('Plasma', $weapon->weaponClassArray, true);
	}

	/* ------------------------------------------------------------------ Expert Security Officer (Stage 13) */

	/* +5 initiative (published to every viewer: `publish`), and -2 on the critical rolls of the C&C he sits in - its own
	   critRollMod, which ShipSystem::testCritical reads, so it also eases the kill roll of every officer posted there ("including
	   any modifications", D3), as the Technician's -2 does on his reactor. Another C&C on the hull is not his (user ruling
	   2026-10-17). The lobby previews the initiative (officers.js `stats`); nothing displays a critical modifier. */
	private static function applySecurityOfficer($ship, $entry, $turn){
		$ship->iniativebonus += self::SECURITY_INI;
		$cnc = $ship->getSystemById($entry['post']);
		if ($cnc !== null) $cnc->critRollMod += self::SECURITY_CNC_CRIT_MOD;
	}

	/* ⭐ THE boarding shift: how far every boarding roll against $ship moves in its favour while he serves. "Shift all die
	   rolls by up to 2 in any direction desired by the defending player" is built as a fixed 2 (user's note: higher is
	   worse for the boarders on every table), summed with the roll's other modifiers ("the attacker ... must add these in
	   before the security officer makes his alterations" - in a sum the order cannot matter). Every d10 a
	   boarding party rolls against his ship - its delivery (Marines, GrapplingClaw and the Trek transporter's
	   getDeliveryRollMod) and its missions (ShipSystem::getMarineRollMod: Wreak Havoc, Sabotage, Rescue and its Capture
	   combat roll) - adds it; his ship's own defending marines, who need 5 or less in Capture combat, subtract it (user
	   rulings 2026-10-17: every boarding party, the defenders' rolls too). Server only: the client previews only the
	   attach chance, which no crew affects. */
	public static function boardingShift($ship){
		return self::isActive($ship, 'OFF_SECU') ? self::SECURITY_BOARDING_SHIFT : 0;
	}

	/* ------------------------------------------------------------------ Expert Religious Leader (Stage 14) */

	//+10 initiative while he serves - published to every viewer (`publish`), as the Helmsman's is
	private static function applyReligiousLeader($ship, $entry, $turn){
		$ship->iniativebonus += self::RELIGIOUS_INI;
	}

	/* His crew's frenzy, on every turn after he was disabled or killed in battle (`lost`, isLost): "the initiative bonus is
	   doubled, and the ship receives +1 to its sensors and +5 to its defense profiles thereafter" - +20 initiative, +1 output
	   on the strongest Scanner still standing at the start of this turn (Improved Sensors' scanner, among the living - a
	   destroyed one would swallow the point), and +1 on both profiles: FV shows a profile x5, so the rules' "+5" is one
	   point (user ruling 2026-10-17), Poor Crew's and the Geneticist's way. The fields reach every viewer (`lostPublish`);
	   a scanner's output always does. */
	private static function applyReligiousFrenzy($ship, $entry, $turn){
		$ship->iniativebonus += 2 * self::RELIGIOUS_INI;
		$ship->forwardDefense += self::FRENZY_PROFILE;
		$ship->sideDefense += self::FRENZY_PROFILE;
		$best = null;
		foreach ($ship->systems as $system){
			if (!($system instanceof Scanner) || $system->output <= 0 || $system->isDestroyed((int)$turn - 1)) continue;
			if ($best === null || $system->output > $best->output) $best = $system;
		}
		if ($best !== null) $best->output += self::FRENZY_SENSORS;
	}

	/* ⭐ His +1 damage: Weapon::getFinalDamage, which runs once per HIT - each pulse of a pulse weapon, each shot of a weapon
	   with several guns, a raking shot once however many rakes it is split into - so "+1 per volley (not per sub-volley or
	   per die)" is one call there (user ruling 2026-10-17: per hit). While he serves AND after he is lost: the frenzy keeps
	   it (user ruling). Never on a hit whose roll is 0 - the Electro-Pulse Gun, Stun Beam, Gravity Net and Molecular Flayer
	   roll no damage, their effect is something else - nor on a ram, which is no weapon fired. Behind $officersPresent.
	   ⚠️ MIRROR PAIR with officers.religiousDamageBonus (JS), which raises the owner's Damage lines. */
	public static function religiousDamageBonus($ship, $weapon, $rolled){
		if ($rolled <= 0 || $weapon instanceof RammingAttack) return 0;
		foreach ($ship->getOfficers() as $entry){
			if (self::rulesId($entry['id']) !== 'OFF_RELI') continue; //at his C&C, or anywhere else as OFF_RELIP (Stage 15)
			$turn = TacGamedata::$currentTurn;
			return (self::serves($entry, $turn) || self::isLost($entry, $turn)) ? self::RELIGIOUS_DAMAGE : 0;
		}
		return 0;
	}

	/* ------------------------------------------------------------------ Expert Surge Officer (Stage 16) */

	/* +2 power on a capital ship or larger, +1 on a medium ship or HCV, none on an LCV - the Mag-Gravitic Reactor's output, raised
	   at load as the Technician's is: `output` reaches every client and power.js reads it, so the game's power balance needs
	   nothing more (the lobby previews it - officers.reactorBonus). */
	private static function applySurgeOfficer($ship, $entry, $turn){
		$reactor = $ship->getSystemById($entry['post']);
		if ($reactor !== null) $reactor->output += self::surgePower($ship);
	}

	/* "Capital ships and larger" - size class 3, and FV's enormous units ($Enormous); "medium ships and HCVs" - 1 and 2;
	   "LCVs and smaller" - 0. ⚠️ MIRROR PAIR with officers.surgePower (JS). */
	public static function surgePower($ship){
		if (!empty($ship->Enormous) || (int)$ship->shipSizeClass >= 3) return self::SURGE_POWER_CAPITAL;
		if ((int)$ship->shipSizeClass >= 1) return self::SURGE_POWER_MEDIUM;
		return 0;
	}

	/* ⭐ His +1 damage: Weapon::getFinalDamage, which runs once per HIT, for a shot in an Electromagnetic mode - so an EM Pulsar
	   gains it on every pulse ("+1 damage per pulse"), a Resonance Generator on every section it strikes, and a raking Surge
	   Cannon once per shot: +1 for each cannon in it ("a five-weapon blast would score +5 damage") - its firingMode is the
	   number of cannons combined, and the shot it falls back to when too few join is mode 1. His own ship's Spark Field gains
	   it too (user ruling 2026-10-18). Never on a hit whose roll is 0 (the Burst Beams and Electro-Pulse Gun roll none - their
	   effect is something else; a Spark Field boosted down to 0 stays 0), the Religious Leader's rule. Behind $officersPresent.
	   ⚠️ MIRROR PAIR with officers.surgeDamageBonus (JS), which raises the owner's Damage lines. */
	public static function surgeDamageBonus($ship, $weapon, $rolled){
		if ($rolled <= 0 || $weapon->weaponClass !== 'Electromagnetic') return 0;
		if (!self::isActive($ship, 'OFF_SURG')) return 0;
		if ($weapon instanceof SurgeCannon) return max(1, (int)$weapon->firingMode) * self::SURGE_DAMAGE;
		return self::SURGE_DAMAGE;
	}

	/* How much further he eases his ship's EM hardening: 1 while he serves - "a -3 penalty (not -2)" on an EM weapon's critical
	   roll, and a forced shutdown resisted on "a 1 or 2" instead of a 1. WeaponEM asks it only for a hardened unit. */
	public static function surgeHardening($ship){
		if (!TacGamedata::$officersPresent) return 0;
		return self::isActive($ship, 'OFF_SURG') ? self::SURGE_HARDENING : 0;
	}

	/* "Any spark field affecting the ship reduces its damage by 1 point" - SparkField::beforeDamage, on each hit a field (his
	   fleet's, an enemy's or a mine's) scores on his ship; never below 0. */
	public static function sparkFieldRelief($ship){
		return self::isActive($ship, 'OFF_SURG') ? self::SURGE_SPARK_RELIEF : 0;
	}

	/* ------------------------------------------------------------------ Expert Ion Technician (Stage 17) */

	/* "All ion-based weapons (including the ioniclaser)": a weapon with an Ion mode - the Ionic Laser is Ion-class in FV - and the
	   Ion Torpedo, which FV files as a Ballistic weapon (a Torpedo) although the rules name it. ⚠️ MIRROR PAIR with
	   officers.isIonWeapon (JS). */
	private static function isIonWeapon($weapon){
		if ($weapon instanceof IonTorpedo || $weapon->weaponClass === 'Ion') return true;
		return is_array($weapon->weaponClassArray) && in_array('Ion', $weapon->weaponClassArray, true);
	}

	//a shot in an Ion mode - weaponClass is the fired mode's by then - or the Ion Torpedo's
	private static function isIonShot($weapon){
		return $weapon instanceof IonTorpedo || $weapon->weaponClass === 'Ion';
	}

	/* "Weapons scoring a flat amount of damage ... add +1 only", and the boost "will not function ... with those that score a fixed
	   amount of damage": a weapon whose damage in this mode is one number - its Damage line's, from minDamageArray / maxDamageArray
	   (filled per mode at construction; a ReducedDamage critical scales both ends alike). The Rad Cannon (10) and the Ion Field
	   Generator (0) are flat too. ⚠️ MIRROR PAIR with officers.ionScoresFlat (JS), which reads mode 1. */
	private static function scoresFlat($weapon, $mode = null){
		if ($mode === null) $mode = $weapon->firingMode;
		$min = isset($weapon->minDamageArray[$mode]) ? $weapon->minDamageArray[$mode] : $weapon->minDamage;
		$max = isset($weapon->maxDamageArray[$mode]) ? $weapon->maxDamageArray[$mode] : $weapon->maxDamage;
		return (int)$min === (int)$max;
	}

	//an ion weapon he can boost: its damage is rolled (mode 1 - every Cascor weapon has one mode)
	private static function boostableIon($weapon){
		return self::isIonWeapon($weapon) && !self::scoresFlat($weapon, 1);
	}

	//he serves, and not on a weapon a serving Expert Gunner holds - the Gunner's +1 per die replaces his +1 there (the Laser
	//Technician's ruling, user 2026-10-17: only the flat bonus is the Gunner's to replace; the boost still works)
	private static function ionServesFor($ship, $weapon){
		$turn = TacGamedata::$currentTurn;
		return self::isActive($ship, 'OFF_ION', $turn) && !self::servesOn($ship, 'OFF_GUN', $weapon, $turn);
	}

	/* His +1 per die: Weapon::getFinalDamage adds it to Elite Crew's (and a Gunner's) Dice::$perDieBonus for an ion shot whose damage
	   is rolled, behind $officersPresent - each die capped at its face ("a d10 would score 10 damage on a roll of either 9 or 10"). */
	public static function ionDieBonus($ship, $weapon){
		if (!self::isIonShot($weapon) || self::scoresFlat($weapon)) return 0;
		return self::ionServesFor($ship, $weapon) ? self::ION_DIE_BONUS : 0;
	}

	/* "Weapons scoring a flat amount of damage, like the dual ion bolter or ion torpedo, add +1 only" - per hit, so on each of a Dual
	   Ion Bolter's two guns. Never on a roll of 0: the Rad Cannon's own rules "supersede the above benefit" (improvesRadCannon), and
	   the Ion Field Generator deals none - their effect is something else. Behind $officersPresent.
	   ⚠️ MIRROR PAIR with officers.ionDamageBonus (JS), which raises the owner's Damage lines. */
	public static function ionFlatBonus($ship, $weapon, $rolled){
		if ($rolled <= 0 || !self::isIonShot($weapon) || !self::scoresFlat($weapon)) return 0;
		return self::ionServesFor($ship, $weapon) ? self::ION_FLAT_BONUS : 0;
	}

	/* What each ion gun has used of its boost: weapon => array(turn => true). In memory only, as the Laser Technician's pool. */
	private static $ionBoostSpent = null;

	/* ⭐ THE THRUST BOOST - Weapon::getFinalDamage, behind $officersPresent: "Three points of thrust produce an extra 1d10+1 damage,
	   should the weapon score a hit." Each level of the generic boost (type-2 power entries) costs ION_BOOST_THRUST thrust - the
	   client takes it off the engine (ionForJson) - and adds 1d10+1; ONE level per gun a turn (user ruling 2026-10-18, revised the
	   same day from "as many as the thrust allows"): further levels a doctored entry carries add nothing. Only an ion shot whose damage is rolled ("will not function on non-ionic weapons or with those that score a fixed amount of
	   damage"), and only while he serves on the order's turn: the server takes boost entries as sent, so a doctored one is answered
	   here. Rolled as written - his +1 per die, and Elite Crew's, are for the weapon's own dice. ONCE a turn, on the gun's first
	   hit: every hit a weapon makes in a turn is resolved in one request on one weapon object (and no Cascor gun hits twice). A hit
	   on a Chameleon simulacrum alone is rolled but spends nothing (the Laser Technician's rule). */
	public static function ionBoostDamage($ship, $weapon, $target, $fireOrder){
		if (!self::isIonShot($weapon) || self::scoresFlat($weapon)) return 0;
		$turn = (int)$fireOrder->turn;
		if (!self::isActive($ship, 'OFF_ION', $turn)) return 0;
		$levels = min(self::ION_BOOST_MAX, self::boostOn($weapon, $turn));
		if ($levels < 1) return 0;
		if (self::$ionBoostSpent === null) self::$ionBoostSpent = new WeakMap();
		$spent = isset(self::$ionBoostSpent[$weapon]) ? self::$ionBoostSpent[$weapon] : array();
		if (!empty($spent[$turn])) return 0;
		$add = Dice::d(self::ION_BOOST_DIE, $levels) + $levels * self::ION_BOOST_FLAT;
		if (empty($target->chameleonIsPhantom)){
			$spent[$turn] = true;
			self::$ionBoostSpent[$weapon] = $spent;
		}
		return $add;
	}

	/* "If applied, the weapon must fire on that turn (if it does not, it discharges and is treated as having fired anyway)" -
	   Weapon::calculateLoadingFromLastTurn asks it beside firedOnTurn at the turn change, behind $officersPresent: a gun he boosted
	   on $turn that made no fire order then loses its loading as if it had fired. The turn change runs on the gamedata loaded BEFORE
	   the firing resolved (§9.16), which holds every order declared and every power entry of Initial Orders - all this reads. The
	   client follows the loading row the turn change writes, so it needs no mirror. An Expert Gunner's faster reload is for a
	   firing (fasterReload), so a discharge never spends it.
	   Only a gun that was LOADED on $turn discharges: one still recharging could not have fired, so a boost on it was never
	   "applied on the turn of firing" (user rule, 2026-10-18 - game 4491: a boost the client carried onto two recharging guns
	   would otherwise have reset their reload every turn). calculateLoadingFromLastTurn asks before setLoading, so the weapon
	   still holds $turn's loading. */
	public static function ionDischarged($weapon, $turn){
		$ship = $weapon->getUnit();
		if (!($ship instanceof BaseShip) || !self::boostableIon($weapon)) return false;
		if ($weapon->getTurnsloaded() < $weapon->getLoadingTime()) return false;
		if (self::boostOn($weapon, $turn) < 1) return false;
		return self::isActive($ship, 'OFF_ION', $turn);
	}

	/* Weapon::stripForJson (through weaponForJson), for each ion gun he can boost, on a ship he is aboard (serving or not) - the
	   Laser Technician's pattern (laserForJson), with THRUST for power, the Hypergraviton Blaster's path:
	   - his own team (and everybody once the game is over) is told what a boost level costs - no power (boostEfficiency 0) and
	     ION_BOOST_THRUST thrust (thrustBoosted / thrustPerBoost: movement.js getRemainingEngineThrust takes it off the engine, and
	     the Initial Orders commit already refuses a ship whose thrust goes negative) - whenever its power entries are sent; and
	     that the gun is BOOSTABLE while he serves and it is ready to fire this turn ("This power must be applied on the turn of
	     firing": a boosted gun that does not fire discharges, so one still charging is never offered the buttons);
	   - anyone else gets its power entries WITHOUT the boosts: "Weapons being charged in this manner will be detected (and their
	     strength known) by anything that detects special weapon arming modes, such as the Expert Analyst" - and by nothing else. */
	private static function ionForJson($ship, $weapon, $strippedSystem){
		if (!self::boostableIon($weapon) || !self::isAboard($ship, 'OFF_ION')) return;
		if (TacGamedata::$currentGameFinished || $ship->isRevealedToCurrentViewer()){
			$strippedSystem->boostEfficiency = 0;
			$strippedSystem->thrustBoosted = true;
			$strippedSystem->thrustPerBoost = self::ION_BOOST_THRUST;
			if (self::isActive($ship, 'OFF_ION') && $weapon->getLoadingTime() <= $weapon->getTurnsloaded()) $strippedSystem->boostable = true;
			return;
		}
		self::stripBoosts($strippedSystem);
	}

	/* The Rad Cannon, fired by HIS ship: "Rad cannons score 12 damage on structure instead of 10 (this supersedes the above benefit),
	   and force a critical roll if they hit the sensors, engine or reactor. If they hit C&C, the critical roll is at +4 on the die
	   instead of the listed +2." RadCannon::onDamagedSystem asks it for the shooter. A sub-reactor counts as the reactor (user ruling
	   2026-10-18). Kept on a Gunner's Rad Cannon: these are the weapon's own rules improved, not a bonus he could replace. */
	public static function improvesRadCannon($shooter){
		if (!TacGamedata::$officersPresent || !($shooter instanceof BaseShip)) return false;
		return self::isActive($shooter, 'OFF_ION');
	}

	//only for a hull carrying an ion weapon: the rules do not say so, but he does nothing without one (the Laser Technician's test)
	private static function hasIonWeapon($ship){
		foreach ($ship->systems as $system){
			if ($system instanceof Weapon && self::isIonWeapon($system)) return true;
		}
		return false;
	}

	/* ------------------------------------------------------------------ Expert ELINT Officer (Stage 20) */

	/* +1 output on the array he sits in - `output` reaches every client and ew.js sums it into the EW pool, so the game needs
	   nothing more (the lobby previews it - officers.sensorBonus) - and -2 on the critical rolls of EVERY sensor array on his ship
	   (user ruling 2026-10-20), each array's own critRollMod, which ShipSystem::testCritical reads: so it also eases his kill roll
	   on his array, as the Technician's -2 does on his reactor. Nothing displays a critical modifier. */
	private static function applyElintOfficer($ship, $entry, $turn){
		$array = $ship->getSystemById($entry['post']);
		if ($array !== null) $array->output += self::ELINT_SENSORS;
		foreach ($ship->systems as $system){
			if ($system instanceof Scanner) $system->critRollMod += self::ELINT_CRIT_MOD;
		}
	}

	/* ⭐ THE range of an ELINT function from $elint (a unit): $base hexes, 25% longer while his officer serves, fractions of 0.5 or more
	   rounded up - 30 -> 38, 20 -> 25, 50 -> 63. Every ELINT range test asks it: EW::getSupportedOEW (both legs), getSupportedDEW,
	   getBlanketDEW and getDistruptionEW, and HkJamming's. The ranged functions only (user ruling 2026-10-20): Detect Stealth has no
	   range of its own, and an ELINT's stealth detection range is not one of its functions. One static read in a game without officers.
	   ⚠️ MIRROR PAIR with officers.elintRange (JS), which reads the `elintRangeBonus` addForJson publishes. */
	public static function elintRange($elint, $base){
		if (!TacGamedata::$officersPresent || !self::isActive($elint, 'OFF_ELINT')) return $base;
		return intdiv((int)$base * (100 + self::ELINT_RANGE_PCT) + 50, 100);
	}

	/* His blanket against a BALLISTIC weapon: "the total EW value is divided by 3 (not 4) for purposes of ballistic weapons only" - the
	   ELINT's blanket points, scaled for the quarter EW::getBlanketDEW takes of each ELINT's: x4/3, so 12 points give 4 against a
	   ballistic weapon and 3 against anything else, and against each the best ELINT covering the unit wins. Fractions are kept, as the
	   quarter keeps them (user ruling 2026-10-20: 10 points give 3.33). Asked for a ballistic shot only, behind $officersPresent.
	   ⚠️ MIRROR PAIR with officers.ballisticBlanket (JS), which reads the `blanketBallistic` addForJson publishes. */
	public static function ballisticBlanket($elint, $blanket){
		if (!self::isActive($elint, 'OFF_ELINT')) return $blanket;
		return $blanket * 4 / self::ELINT_BLANKET_BALLISTIC;
	}

	/* ------------------------------------------------------------------ the Expert Turret Officer (Stage 22) */

	//OFF_TURR's posts (`hosts`): "One turret-mounted weapon" - a mount that can jam (Weapon::isTurretMount), any of its turret's guns
	private static function isTurretPost($system, $ship){
		return $system instanceof Weapon && $system->isTurretMount();
	}

	/* Which turret a weapon is on, as a key on its ship: its linked firing group - Weapon::getTurretMates' own test - or, for a mount linked
	   to no other, the weapon alone ("a turret of one", user ruling 2026-10-21). null for anything that is not a weapon.
	   ⚠️ MIRROR PAIR with officers.turretKey (JS). */
	private static function turretKey($system){
		if (!($system instanceof Weapon)) return null;
		return ($system->linkedFiringGroup !== null) ? 'group:' . $system->linkedFiringGroup : 'mount:' . (int)$system->id;
	}

	//How many Expert Turret Officers one ship may carry (`shipLimit`): one per turret ("1 Officer per turret", the user's note)
	private static function turretLimit($ship){
		$turrets = array();
		foreach ($ship->systems as $system){
			if (self::isTurretPost($system, $ship)) $turrets[self::turretKey($system)] = true;
		}
		return max(1, count($turrets));
	}

	/* Every weapon on his turret: +1 to hit - +1 on every non-null fire control of every mode, the Gunner's rule (raiseEveryMode), which
	   isModified sends to every viewer as Gunsights' is - except on a gun a serving Expert Gunner holds: the Gunner "does not combine any
	   to-hit or damage bonuses with those of other elite officers" (user ruling 2026-10-21, the Matter expert's precedent). And -1 on
	   every one's critical rolls (their critRollMod, which ShipSystem::testCritical reads) - so his own kill roll, and a Gunner's on that
	   turret, are eased too, as the Technician's -2 eases his. */
	private static function applyTurretOfficer($ship, $entry, $turn){
		$post = $ship->getSystemById($entry['post']);
		if (!($post instanceof Weapon)) return;
		foreach ($post->getTurretMates($ship) as $weapon){
			$weapon->critRollMod += self::TURRET_CRIT_MOD;
			if (!self::servesOn($ship, 'OFF_GUN', $weapon, $turn)) self::raiseEveryMode($weapon);
		}
	}

	//the Expert Turret Officer serving on $turn on $weapon's turret, or null
	private static function turretOfficerFor($ship, $weapon, $turn){
		$turret = self::turretKey($weapon);
		if ($turret === null) return null;
		foreach ($ship->getOfficers() as $entry){
			if ($entry['id'] !== 'OFF_TURR' || !self::serves($entry, $turn)) continue;
			if (self::turretKey($ship->getSystemById($entry['post'])) === $turret) return $entry;
		}
		return null;
	}

	/* -2 on the turret jam roll of a weapon on his turret - Weapon::testArcRestriction, behind $officersPresent. The jam is its own d20,
	   not a critical roll, so his -1 on criticals is not added to it: "When rolling for the special turret jam critical, subtract 2". */
	public static function turretJamRelief($ship, $weapon, $turn){
		return (self::turretOfficerFor($ship, $weapon, (int)$turn) !== null) ? self::TURRET_JAM_RELIEF : 0;
	}

	/* ⭐ SHELTERED TURRETS: "If no weapon on the turret fires in a given turn, the owning player can declare it to be 'sheltered.' If this
	   is done, no incoming fire can hit its weapons unless they use called shots." Built AUTOMATIC - no declaration (the user's note): a
	   turret a serving Expert Turret Officer holds, none of whose weapons fires a SHOT this turn - an interception, a self-intercept or a
	   Meteor Defence declaration is not one (user ruling 2026-10-21: Weapon::firedOffensivelyOnTurn). Decided ONCE, when every order of
	   the turn is known: Firing::fireWeapons opens it before the ship and fighter fire and closes it after (the rams before it are
	   collisions, and Pre-Firing resolves before the turn's own orders exist). Read by the hit-chart pools (BaseShip::getSystemsByNameLoc
	   and ::getSystemsByTag), where a sheltered weapon reads as DESTROYED: a hit that rolls it takes the chart's usual fallback - another
	   system of that name or tag, else the section's Structure. A called shot at one is resolved before any pool is asked
	   (getHitSystemByTable), so it hits as ever. Server only: an opponent sees where the hits land, nothing more. */
	private static $sheltered = array(); //ship id => array(system id => true), while Firing::fireWeapons resolves the turn's fire

	public static function openShelter($gamedata){
		self::$sheltered = array();
		if (!TacGamedata::$officersPresent) return;
		$turn = (int)$gamedata->turn;
		foreach ($gamedata->ships as $ship){
			foreach ($ship->getOfficers() as $entry){
				if ($entry['id'] !== 'OFF_TURR' || !self::serves($entry, $turn)) continue;
				$post = $ship->getSystemById($entry['post']);
				if (!($post instanceof Weapon)) continue;
				$mates = $post->getTurretMates($ship);
				foreach ($mates as $weapon){
					if ($weapon->firedOffensivelyOnTurn($turn)) continue 2;
				}
				foreach ($mates as $weapon) self::$sheltered[(int)$ship->id][(int)$weapon->id] = true;
			}
		}
	}

	public static function closeShelter(){
		self::$sheltered = array();
	}

	//Is $system a sheltered turret's weapon in the fire being resolved now (openShelter)? One empty() in every other game.
	public static function isSheltered($ship, $system){
		if (empty(self::$sheltered)) return false;
		return isset(self::$sheltered[(int)$ship->id][(int)$system->id]);
	}

	/* ------------------------------------------------------------------ Expert Anticipator (Stage 23) */

	/* "When rolling for initiative, this officer's ship rolls the die twice. The player then selects the higher one" - the d100 of
	   both initiative rolls (Manager::generateIniative and SimultaneousMovementRule::generateIniative), which run on the load of the
	   turn being rolled for. The second die is thrown only for his ship, so every other ship's roll is the die it always was. */
	public static function initiativeRoll($ship){
		$roll = Dice::d(100);
		if (TacGamedata::$officersPresent && self::isActive($ship, 'OFF_ANTI')) $roll = max($roll, Dice::d(100));
		return $roll;
	}

	/* "Roll an extra die and drop the lowest die from the calculation" - 1 for a shot of his ship's weapons, which
	   Weapon::getFinalDamage hands to Dice::$extraDie; the "at least two dice" half is the Dice rule. Never a ram: ramming is not one
	   of the ship's weapons (the Religious Leader's exclusion). On a serving Gunner's weapon as well (user ruling 2026-10-21). */
	public static function extraDamageDie($ship, $weapon){
		if ($weapon instanceof RammingAttack) return 0;
		return self::isActive($ship, 'OFF_ANTI') ? 1 : 0;
	}

	/* Who may give the Firing-phase turn - Movement::validateMovement, on the authoritative load (a POST-side ship has no officers).
	   Never a ship something is attached to (a boarding pod, a grappling claw, a docked unit): attached units are moved with their
	   host only in the Movement phase (MovementGamePhase::process), so they would be left facing the old way. */
	public static function mayAnticipate($ship){
		return !($ship instanceof FighterFlight) && empty($ship->hasAttached) && self::isActive($ship, 'OFF_ANTI');
	}

	/* ------------------------------------------------------------------ Expert Political Officer (Stage 25) */

	//"Availability: Capital ships or larger units only" - size class 3, bases and enormous units included
	private static function isCapitalOrLarger($ship){
		return (int)$ship->shipSizeClass >= 3;
	}

	/* Is a Political Officer BOUGHT on this unit, as its rows say? For the rules that run before the officer list exists, or with
	   none: the free expert's and the free armour's prices (at buy on a POST-side ship, and on a saved fleet), the picks made at the
	   commit, and the hangar's armed shuttle (HangarOps runs from onIndividualNotesLoaded, before onConstructed). A row the hull
	   would not be offered buys nothing, as in pickPosts. */
	public static function politicalAboard($ship){
		if (!is_object($ship) || empty($ship->enhancementOptions) || !is_array($ship->enhancementOptions)) return false;
		if (!self::isBought($ship, 'OFF_POL') || !self::mayCarryOfficers($ship)) return false;
		return self::isEligible($ship, 'OFF_POL');
	}

	/* "The ship possesses one armed shuttle. If this shuttle can use missiles, it comes loaded with one set of standard missiles at no
	   cost": one more default shuttle retyped into the faction's armed shuttle, as Elite Crew's are (user note) and on top of them
	   (user ruling 2026-10-21) - HangarOps::crewArmedShuttleUpgrades. None on a Poor Crew hull, which "cannot accommodate" armed
	   shuttles (HangarOps::crewBlocksArmedShuttles - the caller's rule). Not lost with him: it is the ship's. */
	public static function politicalArmedShuttles($ship){
		return self::politicalAboard($ship) ? 1 : 0;
	}

	/* "Select either an expert navigator, expert helmsman, expert engineer, expert scanner and add him to the crew at no cost": of
	   those four bought on a ship with a Political Officer, the DEAREST costs nothing (the user's note) - the first in registry order
	   on a tie. null with no Political Officer, or none of the four. rowPrice prices a saved fleet's row by it; the lobby prices the
	   buy dialog and the fleet list the same way. ⚠️ MIRROR PAIR with officers.freeExpertId (JS). */
	public static function freeExpertId($ship){
		if (!self::politicalAboard($ship)) return null;
		$best = null;
		$bestPrice = -1;
		foreach (self::$politicalExperts as $id){
			if (!self::isBought($ship, $id) || !self::isEligible($ship, $id)) continue;
			$price = self::shipPrice($ship, $id);
			if ($price > $bestPrice){
				$best = $id;
				$bestPrice = $price;
			}
		}
		return $best;
	}

	/* ⭐ WHICH HARDENED ARMOUR IS FREE - "ship systems (selected by the owner) receive +1 to their armor values. The owner must select 5
	   different systems ... and cannot select structure blocks": with a Political Officer aboard, the POLITICAL_ARMOUR_SYSTEMS dearest
	   SYS_HARM rows (a refit of +1 armour, one to a system) that are not on Structure, a tie to the lower system id. Any beyond them is
	   paid as ever. Nothing while his C&C is in $destroyed (pre-battle damage refunds him - Stage 15). Returns $rows' keys.
	   ⚠️ MIRROR PAIR with officers.freeArmourRows (JS). */
	private static function freeArmourRows($ship, array $rows, array $destroyed = array()){
		if (!self::politicalAboard($ship)) return array();
		$cnc = self::findPost($ship, 'cnc');
		if ($cnc === null || isset($destroyed[(int)$cnc->id])) return array();
		$candidates = array();
		foreach ($rows as $i => $row){
			if (!is_array($row) || !isset($row[0], $row[2], $row[6]) || (string)$row[0] !== 'SYS_HARM' || (int)$row[2] < 1) continue;
			$system = $ship->getSystemById((int)$row[6]);
			if (!$system || $system instanceof Structure) continue;
			$candidates[] = array($i, Enhancements::systemEnhancementTotalPrice($ship, $system, 'SYS_HARM', (int)$row[2]), (int)$row[6]);
		}
		usort($candidates, function ($a, $b){
			if ($a[1] !== $b[1]) return $b[1] - $a[1];
			return $a[2] - $b[2];
		});
		$free = array();
		foreach (array_slice($candidates, 0, self::POLITICAL_ARMOUR_SYSTEMS) as $candidate) $free[$candidate[0]] = true;
		return $free;
	}

	/* The last step of Enhancements::sanitiseSystemEnhancements - at buy, save and reload alike: the free Hardened Armour priced 0,
	   every other SYS_HARM row at its full price, and the total summed again. $out is that method's result. */
	public static function pricePoliticalArmour($ship, array $out, array $destroyed = array()){
		$free = self::freeArmourRows($ship, $out['rows'], $destroyed);
		$out['total'] = 0;
		foreach ($out['rows'] as $i => $row){
			if ((string)$row[0] === 'SYS_HARM'){
				$system = $ship->getSystemById((int)$row[6]);
				$out['rows'][$i][4] = (isset($free[$i]) || !$system) ? 0 : Enhancements::systemEnhancementTotalPrice($ship, $system, 'SYS_HARM', (int)$row[2]);
			}
			$out['total'] += $out['rows'][$i][4];
		}
		return $out;
	}

	/* As the fleet is COMMITTED (BuyingGamePhase, once per unit, right after its system enhancements are sanitised), whatever the player
	   left unchosen is chosen at random, ONCE, and stored as ordinary rows (user ruling 2026-10-21: the server's pick) - so it is fixed
	   for the whole game, no opponent can work it out, and every load reads it as it reads a purchase:
	     - none of the four experts bought: one of those this hull is offered, his row bought (free - freeExpertId);
	     - fewer than POLITICAL_ARMOUR_SYSTEMS free Hardened Armour: one on each of as many random systems as are missing - a system
	       offered it, not Structure, not destroyed by pre-battle damage and not refitted with it already.
	   Never on a saved fleet: only a game's commit rolls. Dice::d, which a harness can force. $out: sanitiseSystemEnhancements'
	   result, returned with the rows added and every Hardened Armour priced again. */
	public static function placePoliticalPicks($ship, array $out, $cleanPreBattle = null){
		if (!self::politicalAboard($ship)) return $out;
		$destroyed = Enhancements::sysEnhDestroyedSystemIds($ship, $cleanPreBattle);
		$cnc = self::findPost($ship, 'cnc');
		if ($cnc === null || isset($destroyed[(int)$cnc->id])) return $out;

		if (self::freeExpertId($ship) === null){
			$choices = array();
			foreach (self::$politicalExperts as $id) if (self::isEligible($ship, $id)) $choices[] = $id;
			if ($choices) self::buyShipLevelRow($ship, $choices[Dice::d(count($choices)) - 1]);
		}

		$missing = self::POLITICAL_ARMOUR_SYSTEMS - count(self::freeArmourRows($ship, $out['rows'], $destroyed));
		if ($missing > 0){
			$refitted = array();
			foreach ($out['rows'] as $row) if ((string)$row[0] === 'SYS_HARM') $refitted[(int)$row[6]] = true;
			$candidates = array();
			foreach ($ship->systems as $system){
				$id = (int)$system->id;
				if ($system instanceof Structure || isset($destroyed[$id]) || isset($refitted[$id])) continue;
				if (Enhancements::systemEnhancementOffered($ship, $system, 'SYS_HARM')) $candidates[] = $system;
			}
			for (; $missing > 0 && $candidates; $missing--){
				$k = Dice::d(count($candidates)) - 1;
				$system = $candidates[$k];
				array_splice($candidates, $k, 1);
				$out['rows'][] = array('SYS_HARM', Enhancements::systemEnhancementLabel('SYS_HARM'), 1,
					Enhancements::systemEnhancementLimit($ship, $system, 'SYS_HARM'), 0,
					Enhancements::systemEnhancementPriceStep($ship, $system, 'SYS_HARM'), (int)$system->id, (string)$system->name);
			}
		}
		return self::pricePoliticalArmour($ship, $out, $destroyed);
	}

	//buy a ship-level officer row the hull offers: its count 1, as the buy dialog writes it (or the offer appended, on a list without it)
	private static function buyShipLevelRow($ship, $id){
		foreach ($ship->enhancementOptions as $i => $entry){
			if (isset($entry[0]) && $entry[0] === $id){
				$ship->enhancementOptions[$i][2] = 1;
				return;
			}
		}
		$ship->enhancementOptions[] = array($id, self::getLabel($id), 1, 1, self::shipPrice($ship, $id), 0, false);
	}

	/* "Once during each scenario, the political officer can give a stirring speech ... This provides the ship with +30 to its
	   initiative on that turn. The speech must be announced before initiative rolls are made on that turn": ordered in the Firing
	   phase (the C&C's Speech! button), so it is announced before the NEXT turn's roll, which is where the +30 lands. That turn he must
	   still serve (user ruling 2026-10-21) - this runs only while he does. Published: iniativebonus is public. */
	private static function applyPoliticalOfficer($ship, $entry, $turn){
		if (isset($entry['abilities']['speech']) && (int)$entry['abilities']['speech'] === (int)$turn - 1) $ship->iniativebonus += self::SPEECH_INITIATIVE;
	}

	/* The index of this ship's Political Officer if he may give his speech on $turn from the C&C $systemid - posted there, serving, and
	   with it still his - or null. ⚠️ MIRROR PAIR with officers.canSpeak (JS). */
	private static function speakerIndex($ship, $systemid, $turn){
		foreach ($ship->getOfficers() as $i => $entry){
			if ($entry['id'] !== 'OFF_POL' || (int)$entry['post'] !== (int)$systemid || !self::serves($entry, $turn)) continue;
			return isset($entry['abilities']['speech']) ? null : $i;
		}
		return null;
	}

	/* FireGamePhase::process, through CnC::saveFirePhaseDeclaration: the speech a POST asked for on the C&C $cnc of a POST-side ship,
	   checked on the LOADED ship (the POST-side copy has no officers) and spent - one OffUsed note on his post, read back at every
	   later load (takeNote, resolveUses). A second order, from a re-commit or a doctored POST, finds it spent and writes nothing. */
	public static function saveSpeech($cnc, $gamedata){
		if (!TacGamedata::$officersPresent || !is_object($cnc)) return;
		$postShip = $cnc->getUnit();
		if (!$postShip) return;
		$ship = $gamedata->getShipById((int)$postShip->id);
		if (!$ship || $ship instanceof FighterFlight || (int)$ship->userid !== (int)$gamedata->forPlayer) return;
		$turn = (int)$gamedata->turn;
		$i = self::speakerIndex($ship, (int)$cnc->id, $turn);
		if ($i !== null) self::spend($ship, $i, 'speech', $turn, $gamedata);
	}

	/* FireGamePhase::advance, after firing: every speech given this turn told in the combat log, to every player - "The Political
	   Officer on-board <ship> begins a stirring speech about <a topic of the rules' mumbo-jumbo>". A log line wearing a fire order's
	   clothes ([[howto_create_fire_orders]]): on the ship's own RammingAttack, at itself, rolled 1 so the printed log keeps it, no shot.
	   The ship's name is a bare shiplink, coloured for each reader by the client. Behind $officersPresent. */
	public static function speechLog($gamedata){
		if (!TacGamedata::$officersPresent) return;
		$turn = (int)$gamedata->turn;
		foreach ($gamedata->ships as $ship){
			foreach ($ship->getOfficers() as $entry){
				if ($entry['id'] !== 'OFF_POL' || !isset($entry['abilities']['speech']) || (int)$entry['abilities']['speech'] !== $turn) continue;
				$ram = $ship->getSystemByName('RammingAttack');
				if (!$ram) continue;
				$order = new FireOrder(-1, 'normal', $ship->id, $ship->id, $ram->id, -1, $turn, 1, 0, 1, 0, 0, 0, 0, 0, self::SPEECH_DAMAGE_CLASS, 10000);
				$order->pubnotes = 'The Political Officer on-board <span class="shiplink" data-id="' . (int)$ship->id . '">' . $ship->name
					. '</span> begins a stirring speech about ' . self::$speechTopics[Dice::d(count(self::$speechTopics)) - 1] . '.';
				$order->addToDB = true;
				$ram->fireOrders[] = $order;
			}
		}
	}

	/* ------------------------------------------------------------------ Expert Software Engineer (Stage 26) */

	/* "The Hyach computer provides one extra bonus fire control point (BFCP). This is lost only if the computer is completely destroyed":
	   one more point on his Computer, which its damage loss (1 per 5 boxes - HyachComputer::getBFCPAvailable) never takes; he is out of
	   action with the Computer anyway (D3). Public: the Computer's output reaches every viewer, as a Technician's reactor does. */
	private static function applySoftwareEngineer($ship, $entry, $turn){
		$computer = $ship->getSystemById((int)$entry['post']);
		if ($computer instanceof HyachComputer) $computer->addOfficerBFCP(self::SOFTWARE_BFCP);
	}

	/* May the player hold one of $computer's points back to the Firing phase on $turn - its Expert Software Engineer serving in it ("The
	   player can leave this extra BFCP point unallocated until the Weapons Fire Step ..., recording it along with his weapons fire
	   orders")? HyachComputer::settleFiringBFCP asks it. ⚠️ MIRROR PAIR with officers.holdsFiringBFCP (JS). */
	public static function holdsFiringBFCP($ship, $computer, $turn){
		if (!TacGamedata::$officersPresent || !is_object($ship) || !is_object($computer)) return false;
		return self::servesOn($ship, 'OFF_SOFT', $computer, (int)$turn);
	}

	/* "The Hyach Computer ignores the first point of damage in any volley (essentially, it can be treated as having an extra point of armor
	   that cannot be damaged, destroyed or bypassed by weapons that affect or ignore armor)": SOFTWARE_ARMOUR on top of the armour every hit
	   on his Computer meets, added in ShipSystem::assignDamageReturnOverkill AFTER the weapon has worked out that armour - so a weapon that
	   ignores armour, or wears it down, still meets this point. Per hit, as armour is: each rake and each pulse. Behind $officersPresent. */
	public static function computerArmour($ship, $system, $gamedata){
		if (!($system instanceof HyachComputer)) return 0;
		return self::servesOn($ship, 'OFF_SOFT', $system, (int)$gamedata->turn) ? self::SOFTWARE_ARMOUR : 0;
	}

	/* "If aboard a stealth unit, advanced electronic signature masking software makes the ship appear as though it were five hexes further
	   away for detection purposes (but not for weapons fire or other reasons), except for fighters, which halve their detection range
	   (round fractions up)": Stealth::isDetectedMovement - FV's one range test for detecting a stealth ship - hands over a detector's range
	   and its distance and gets them back: a ship's distance SIGNATURE_MASK_HEXES longer, a flight's range halved, rounded up. Server only
	   (the client's own range forecast is commented out). Behind $officersPresent. */
	public static function maskSignature($ship, $detector, $detection, $distance, $turn){
		if (!self::isActive($ship, 'OFF_SOFT', $turn)) return array($detection, $distance);
		if ($detector instanceof FighterFlight) return array((int)ceil($detection / 2), $distance);
		return array($detection, $distance + self::SIGNATURE_MASK_HEXES);
	}

	/* "Working as a team, the Expert Software Engineer permits a specialist to use his abilities a second time during the scenario. The
	   specialist must be of the software category, and must wait at least a full turn after using his special function before he can use it
	   again. This only works for one specialist per scenario": the software Specialists ($softwareSpecialists) this ship may use again on
	   $turn - used once, on an EARLIER turn (user ruling 2026-10-22: from the next turn on), while he serves and before any software
	   Specialist has been used twice. Every use is one of the Specialists' own 'allocated' notes, read back at each load
	   (HyachSpecialists::getUseTurns), so nothing new is stored. His team's payload carries the list (addForJson), which the client offers
	   (officers.specialistReusable). ⚠️ The Specialists write their notes as the client sends them, unchecked - every use always has. */
	public static function specialistReuse($ship, $turn){
		if (!self::isActive($ship, 'OFF_SOFT', $turn)) return array();
		$uses = self::softwareUses($ship);
		if (self::reuseSpentTurn($uses) !== null) return array();
		$open = array();
		foreach ($uses as $type => $turns){
			if (count($turns) === 1 && $turns[0] < (int)$turn) $open[] = $type;
		}
		return $open;
	}

	//the turn the one reuse was spent: the second use of a software Specialist used twice (the earliest, were there two), or null
	private static function reuseSpentTurn(array $uses){
		$spent = null;
		foreach ($uses as $turns){
			if (count($turns) >= 2) $spent = ($spent === null) ? $turns[1] : min($spent, $turns[1]);
		}
		return $spent;
	}

	//each software Specialist this ship has used: type => its use turns, distinct and ascending
	private static function softwareUses($ship){
		$uses = array();
		foreach ($ship->systems as $system){
			if (!($system instanceof HyachSpecialists)) continue;
			foreach ($system->getUseTurns() as $type => $turns){
				if (in_array($type, self::$softwareSpecialists, true)) $uses[$type] = $turns;
			}
		}
		return $uses;
	}

	/* ------------------------------------------------------------------ fighter officers (Stage 4) */

	//Expert Dogfighter: +5 initiative, ELITE_SW's line
	private static function applyDogfighter($ship, $entry, $turn){
		$ship->iniativebonus += 5;
	}

	/* Expert Missileer: +1 OB on a ballistic launch that gets the flight's OB at all (navigator, or the
	   target in the launcher's arc - Weapon::calculateHitBase). Behind $officersPresent there. */
	public static function missileObBonus($flight){
		return self::isActive($flight, 'OFF_MSL') ? 1 : 0;
	}

	//"Only for flights with missiles"
	private static function hasMissiles($ship){
		return self::missilesPerCraft($ship) > 0;
	}

	/* Expert Evader, to hit: -1 OB on a turn the flight evades, which every fighter to-hit path on both
	   ends reads (and the published stat the shooter's own preview needs). The movement rows are loaded
	   before apply runs; the client takes the -1 off itself while its Evade row is still unsent
	   (officers.pendingEvadeOb), and an enemy does not see it before the Firing phase resolves
	   (hideEvadeOb). */
	const EVADE_OB = 1;
	private static function applyEvader($ship, $entry, $turn){
		if (Movement::hasEvaded($ship, $turn)) $ship->offensivebonus -= self::EVADE_OB;
	}

	/* TacGamedata::hideEnemyCombatPivots has just taken an enemy flight's Evade row out of the payload: its OB
	   goes back to what it was without one, so the published figure does not give the order away. */
	public static function hideEvadeOb($flight){
		if (self::isActive($flight, 'OFF_EVAD')) $flight->offensivebonus += self::EVADE_OB;
	}

	/* Expert Evader, damage: 1 point off every hit on the flight on a turn it evades -
	   FighterFlight::getDamageMod, the flight's defensive choke point (Weapon::getFinalDamage subtracts
	   it), behind $officersPresent. No client mirror: no client damage preview exists. */
	public static function evaderDamageReduction($flight, $turn){
		return (self::isActive($flight, 'OFF_EVAD', $turn) && Movement::hasEvaded($flight, $turn)) ? 1 : 0;
	}

	//Who may order an Evade - Movement::validateMovement, on the authoritative load (a POST-side ship has no officers)
	public static function mayEvade($flight){
		return ($flight instanceof FighterFlight) && self::isActive($flight, 'OFF_EVAD');
	}

	//Expert Motivator: -2 on dropout rolls, Fighter::testCritical's flight critRollMod (as EXP_MOTIV always was)
	private static function applyMotivator($ship, $entry, $turn){
		$ship->critRollMod -= 2;
	}

	/* Expert Coordinator, to hit: the flight's combat pivots cost it no -1 "unless the combat pivot was a full 180
	   degrees" - its pivots this turn, rights against lefts, turned it COMBAT_PIVOT_FULL hex sides or more from where
	   the Firing phase found it (Movement::getCombatPivotRotation). Right-left-right is 60 degrees, no penalty; four
	   to one side passed through 180, penalty. Weapon::calculateHitBase, behind $officersPresent.
	   ⚠️ MIRROR PAIR with officers.waivesCombatPivotPenalty (JS). */
	public static function waivesCombatPivotPenalty($flight, $turn){
		if (!self::isActive($flight, 'OFF_COOR', $turn)) return false;
		return Movement::getCombatPivotRotation($flight, $turn) < self::COMBAT_PIVOT_FULL;
	}

	/* ------------------------------------------------------------------ Expert Pilot (Stage 18) */

	/* Expert Pilot: +1 OB each - apply() runs once per serving pilot, so a craft lost takes his share away from the next turn.
	   Published (the shooter's preview needs the real OB). His free jinking level is the client's (officers.freeJinks). */
	private static function applyPilot($ship, $entry, $turn){
		$ship->offensivebonus += self::PILOT_OB;
	}

	/* How many of this officer serve on $turn - an officer bought as a count (`multi`) is one entry per craft, and isActive
	   answers for the first only. ⚠️ MIRROR PAIR with officers.servingCount (JS). */
	public static function servingCount($ship, $id, $turn = null){
		if ($turn === null) $turn = TacGamedata::$currentTurn;
		$n = 0;
		foreach ($ship->getOfficers() as $entry){
			if ($entry['id'] === $id && self::serves($entry, $turn)) $n++;
		}
		return $n;
	}

	/* "An Expert Pilot will also reduce [spark field] damage by 1 point if he forgoes all his other benefits for the entire
	   turn" - AUTOMATIC (user ruling 2026-10-19): on a turn his flight has not jinked (his free level unused) and has not fired
	   (his OB unused), each serving pilot takes 1 point off every hit a Spark Field scores on each of its fighters
	   (SparkField::beforeDamage, behind $officersPresent). Firing = any of the flight's fire orders this turn but an
	   interception ($notFiring), which spends neither benefit. Both are known when a field resolves: the movement was
	   committed in phase 2, and the orders - ballistic launches from Initial Orders included - are in the firing gamedata. */
	public static function pilotSparkRelief($flight, $turn){
		if (!($flight instanceof FighterFlight)) return 0;
		$pilots = self::servingCount($flight, 'OFF_PILOT', $turn);
		if ($pilots < 1) return 0;
		if (Movement::getJinking($flight, $turn) > 0) return 0;
		foreach ($flight->getAllFireOrders($turn) as $order){
			if (!in_array($order->type, self::$notFiring, true)) return 0;
		}
		return $pilots * self::PILOT_SPARK_RELIEF;
	}

	/* ------------------------------------------------------------------ Expert Tailgunner (Stage 21) */

	//"if they have fighters that meet the requirements e.g. an aft mounted weapon" - a weapon its fighter's file flags as the tail gun
	private static function hasTailGun($ship){
		$craft = self::firstCraft($ship);
		if ($craft === null) return false;
		foreach ($craft->systems as $system){
			if ($system instanceof Weapon && $system->isTailGun()) return true;
		}
		return false;
	}

	//Does an Expert Tailgunner serve this flight on $turn - Weapon::getTailGunMod, behind $officersPresent. ⚠️ MIRROR PAIR with officers.hasTailgunner (JS).
	public static function hasTailgunner($flight, $turn){
		return self::isActive($flight, 'OFF_TAIL', $turn);
	}

	/* ------------------------------------------------------------------ Breaching Expert (Stage 24) */

	/* A BREACHING POD, the units he may serve in: a flight whose craft carries Marines and that FV names a breaching pod - in the
	   'Breaching Pods' hangar class (40 flights, the Chariot Breaching Sleds among them) or by its class name (the Minbari Protectorate's
	   Ronati and the Nausicaan pods, which ride shuttle hangars). Never a superheavy that carries Marines (the Dalithorn Assault Pinnace and
	   Customs Vessel, the Nausicaan Gliders). Also what the Fleet Correctness Report counts for his 20%.
	   ⚠️ MIRROR PAIR with officers.isBreachingPod (JS). */
	public static function isBreachingPod($ship){
		if (!($ship instanceof FighterFlight)) return false;
		if ($ship->hangarRequired !== 'Breaching Pods' && stripos((string)$ship->shipClass, 'Breaching Pod') === false) return false;
		$craft = self::firstCraft($ship);
		if ($craft === null) return false;
		foreach ($craft->systems as $system){
			if ($system instanceof Marines) return true;
		}
		return false;
	}

	/* "The pod receives +1 thrust" - the flight's, as it moves as one. Published: the boarding hit chance reads the shooter's thrust on
	   both ends (Marines::calculateHitBase, weaponManager.calculateBoardingAction), and so does the flight's movement. "...and ignores any
	   loss of thrust due to damage": FV takes no thrust from a flight for damage, so there is nothing to ignore - an Energy Draining
	   Field's drain (FighterFlight::getEffectiveFreeThrust) is a field's effect, not damage, and still applies. */
	private static function applyBreachingExpert($ship, $entry, $turn){
		$ship->freethrust += self::BREACH_THRUST;
	}

	/* "The pod's Marines receive benefits as if they were Elite" - the Marines of HIS pod (user ruling 2026-10-21: "single pod"), on
	   $turn: the delivery roll's -1 and the Elite mission classes, whose rolls are -1 (Marines::isElite, behind $officersPresent). Asked
	   at delivery and never written into Marines::$eliteMarines, whose "Elite" tooltip line is sent to every viewer - so it stays the
	   hull's own, and only his team's client adds him to it (officers.marinesEliteLine). Marines Elite already (the Narn and Gaim pods,
	   or Elite Marines bought) gain nothing more (user ruling) - the caller asks eliteMarines first. */
	public static function eliteMarines($flight, $weapon, $turn){
		return self::breachingExpertIndex($flight, $weapon, $turn) !== null;
	}

	//the index of the Breaching Expert serving on $turn in the craft that carries $weapon, or null
	private static function breachingExpertIndex($flight, $weapon, $turn){
		if (!($flight instanceof FighterFlight)) return null;
		$craft = $flight->getFighterBySystem($weapon->id);
		if (!$craft) return null;
		return self::servingIndex($flight, 'OFF_BRCH', $craft, $turn);
	}

	/* "If attempting to capture a ship, the pod's contingent gets three attack rolls on the first turn of combat. It is still destroyed
	   by a single hit from the enemy ... This benefit can be received only once per scenario." Marines::onDamagedSystem, as a capture
	   party from his pod is DELIVERED (its CaptureShip critical made) while he serves and has sent none before: spent then - one OffUsed
	   note - and true, so the caller marks the party (BREACH_PARTY_KEY in its critical's param) and it rolls three on its first turn
	   aboard (captureRolls), whatever becomes of him meanwhile. A delivery that fails spends nothing; nor does a Sabotage or a Rescue
	   party. Behind $officersPresent. */
	public static function spendCaptureAssault($flight, $weapon, $gamedata){
		$turn = (int)$gamedata->turn;
		$i = self::breachingExpertIndex($flight, $weapon, $turn);
		if ($i === null) return false;
		$entry = $flight->getOfficers()[$i];
		if (isset($entry['abilities']['capture'])) return false;
		self::spend($flight, $i, 'capture', $turn, $gamedata);
		return true;
	}

	/* How many attack rolls a capture party makes this turn (ShipSystem::doCaptureMission): BREACH_CAPTURE_ROLLS on its first turn
	   aboard - the turn its critical starts - when his (spendCaptureAssault), else one. The attack only: to the defenders it is still
	   one attacker, eliminated by a single hit. */
	public static function captureRolls($critical, $gamedata){
		if (!is_array($critical->param) || empty($critical->param[self::BREACH_PARTY_KEY])) return 1;
		return ((int)$critical->turn === (int)$gamedata->turn) ? self::BREACH_CAPTURE_ROLLS : 1;
	}

	/* ------------------------------------------------------------------ repairs (Stage 5) */

	/* Criticals::setCriticals, after Pass 2 - so after every Self Repair has spent its points - behind
	   TacGamedata::$officersPresent. Per ship, its serving Expert Engineer and then its Expert Scavenger
	   (user rulings 2026-10-15):
	     Engineer   one critical, the first in Self Repair's default order (criticalQueue).
	     Scavenger  one critical the same way while one is left to erase; with none, SCAVENGER_BOXES boxes.
	   A C&C critical is repaired only on a d20 of CNC_REPAIR_ROLL or more, and a failed roll ends THAT
	   officer's repairs for the turn - so a Scavenger who fails one repairs no boxes either; and he picks
	   for himself, so he may try the C&C critical the Engineer has just failed on.
	   He still repairs at the end of the turn he falls (out = this turn: the next-turn rule of D3).
	   Saved like Self Repair's own work: a critical's turnend (repairCritical) and a negative DamageEntry
	   go out with the ordinary end-of-firing save, so nothing is written from here. */
	public static function runRepairs($ships, $gamedata){
		$turn = (int)$gamedata->turn;
		foreach ($ships as $ship){
			if (!$ship->hasOfficers()) continue;
			if (self::isActive($ship, 'OFF_ENG', $turn)) self::repairOneCritical($ship, $turn);
			if (self::isActive($ship, 'OFF_SCAV', $turn) && self::repairOneCritical($ship, $turn) === null){
				self::repairBoxes($ship, $turn, self::SCAVENGER_BOXES);
			}
		}
	}

	//The first critical of criticalQueue: null when there was none, true once repaired, false on a failed C&C roll
	private static function repairOneCritical($ship, $turn){
		$queue = self::criticalQueue($ship, $turn);
		if (empty($queue)) return null;
		$job = $queue[0];
		if ($job['system'] instanceof CnC && Dice::d(20) < self::CNC_REPAIR_ROLL) return false;
		$job['system']->repairCritical($job['critical'], $turn);
		return true;
	}

	/* Every critical an officer may erase, in Self Repair's DEFAULT order (user ruling 2026-10-15 - the
	   player's Repair Queue overrides belong to a Self Repair system and are not read): the critical's
	   repairPriority, plus its system's while the critical's own is under 10; then the lowest system id,
	   then the lowest critical id. The filters are Self Repair's: a repairable critical on a repairable,
	   undestroyed system (C&C criticals are Self Repair's "cost 4" ones - SecondaryCnC is not a CnC), not
	   a temporary one or one already ended, and not one rolled this turn. That last is the Engineer's "has
	   suffered its effect for at least one full turn": rolled at the end of turn N, a critical acts on
	   N + 1 and may be repaired at the end of N + 1. ⚠️ MIRROR of SelfRepair::criticalPhaseEffects' step 2. */
	private static function criticalQueue($ship, $turn){
		$queue = array();
		foreach ($ship->systems as $system){
			if ($system->repairPriority < 1 || $system->isDestroyed($turn)) continue;
			foreach ($system->criticals as $critical){
				if ($critical->repairPriority < 1) continue;
				if ($critical->turn >= $turn) continue;
				if ($critical->oneturn || $critical->turnend > 0) continue;
				$priority = $critical->repairPriority;
				if ($priority < 10) $priority += $system->repairPriority;
				$queue[] = array('system' => $system, 'critical' => $critical, 'priority' => $priority);
			}
		}
		usort($queue, function ($a, $b){
			if ($a['priority'] !== $b['priority']) return $b['priority'] - $a['priority'];
			if ((int)$a['system']->id !== (int)$b['system']->id) return (int)$a['system']->id - (int)$b['system']->id;
			return (int)$a['critical']->id - (int)$b['critical']->id;
		});
		return $queue;
	}

	/* The Scavenger's boxes, spilling from system to system: the damaged systems in Self Repair's default
	   order (repairPriority, then the lowest id), Structure blocks included (user ruling 2026-10-15).
	   "Only un-destroyed systems ... other than on a destroyed section", and never damage taken this turn
	   (damageReceivedOnTurn - which also counts pre-battle damage, as it does for Self Repair). A negative
	   DamageEntry, as Self Repair writes, that never "undestroys". ⚠️ MIRROR of SelfRepair::
	   criticalPhaseEffects' step 1, without its +10 for a destroyed system, which he may not touch. */
	private static function repairBoxes($ship, $turn, $boxes){
		$queue = array();
		foreach ($ship->systems as $system){
			if ($system->repairPriority < 1 || $system->isDestroyed($turn)) continue;
			if (!($system instanceof Structure)){
				$block = $ship->getStructureSystem($system->getStructureLocation());
				if ($block && $block->isDestroyed($turn)) continue;
			}
			$repairable = ($system->maxhealth - $system->getRemainingHealth()) - $system->damageReceivedOnTurn($turn);
			if ($repairable > 0) $queue[] = array('system' => $system, 'boxes' => $repairable);
		}
		usort($queue, function ($a, $b){
			if ($a['system']->repairPriority !== $b['system']->repairPriority) return $b['system']->repairPriority - $a['system']->repairPriority;
			return (int)$a['system']->id - (int)$b['system']->id;
		});
		foreach ($queue as $job){
			if ($boxes < 1) break;
			$fixed = min($boxes, $job['boxes']);
			$entry = new DamageEntry(-1, $ship->id, -1, $turn, $job['system']->id, -$fixed, 0, 0, -1, false, false, '', 'Repair');
			$entry->updated = true;
			$job['system']->damage[] = $entry;
			$boxes -= $fixed;
		}
	}

	/* ⭐ THE SCAVENGER'S SAVE: "once per scenario ... up to six points of damage can be ignored in any single
	   volley, anywhere on the ship". Automatic (user ruling 2026-10-15): spent on the first hit that would
	   DESTROY the C&C, a reactor, a scanner (ELINT included), an engine or a Structure block when ignoring
	   SAVE_POINTS of it keeps that system standing - and then it ignores all of them, or the whole hit if
	   that is less. A hit 6 points cannot stop does not spend it. A volley's other rakes and pulses are hits
	   of their own and land in full.
	   Called from ShipSystem::assignDamageReturnOverkill, behind $officersPresent, AFTER any protecting system
	   (a Gaim Bulkhead, a shield pool) has taken its share: the once-per-game save is the last resort, judged
	   on the damage still coming, so it never spends itself on a hit a Bulkhead has already stopped.
	   Spent at once in memory, so a second hit this request sees it gone, and stored as one OffSaved note
	   (written straight through: firing runs inside advance()). Returns the hit's damage to deal. */
	public static function scavengerSave($ship, $system, $gamedata, $damage, $armour){
		if (!self::isSavedSystem($system)) return $damage;
		$dealt = $damage - $armour;
		$health = $system->getRemainingHealth();
		if ($dealt < $health || $dealt - self::SAVE_POINTS >= $health) return $damage;
		$turn = (int)$gamedata->turn;
		if (!self::isActive($ship, 'OFF_SCAV', $turn) || self::saveSpent($ship)) return $damage;

		$ship->recordOfficerSave('OFF_SCAV', $turn);
		$post = null;
		foreach ($ship->getOfficers() as $entry) if ($entry['id'] === 'OFF_SCAV') $post = (int)$entry['post'];
		Manager::insertIndividualNote(new IndividualNote(-1, $gamedata->id, $turn, $gamedata->phase,
			$ship->id, $post, self::SAVE_NOTE, substr(self::getLabel('OFF_SCAV') . ' save used', 0, 40), 'OFF_SCAV'));
		return $damage - min(self::SAVE_POINTS, $dealt);
	}

	//the systems his save protects (user rulings 2026-10-15)
	private static function isSavedSystem($system){
		return ($system instanceof CnC) || ($system instanceof Reactor) || ($system instanceof Scanner)
			|| ($system instanceof Engine) || ($system instanceof Structure);
	}

	private static function saveSpent($ship){
		$saves = $ship->getOfficerSaves();
		return isset($saves['OFF_SCAV']);
	}

	//DBManager's bulk note loop hands every OffSaved note here, as it does OffKilled and OffMoved
	public static function isSaveNote($note){
		return $note->notekey === self::SAVE_NOTE;
	}

	/* ------------------------------------------------------------------ a fighter officer follows his fighter */

	/* What one bought officer row costs the unit, by the lobby's rule: a ship officer pct% of the hull, a
	   fighter officer flightPrice at the row's own flightSize (the size it was bought or spawned at).
	   Stored with a saved fleet's row (Manager::submitSavedFleet), and used to keep an officer's cost
	   with him when HangarOps splits a flight. $count (Stage 18): an officer bought as a count costs the
	   row's count - read off the unit's bought row when null, or the count about to be written back
	   (Manager::loadSavedFleet re-prices before it restores the counts). Stage 25: the expert a Political Officer brings costs
	   nothing (freeExpertId - so the unit's other officer rows must already be bought on it: loadSavedFleet writes their counts first). */
	public static function rowPrice($ship, $id, $count = null){
		if (!isset(self::$registry[$id])) return 0;
		if (empty(self::$registry[$id]['flight'])) return ($id === self::freeExpertId($ship)) ? 0 : self::shipPrice($ship, $id);
		if (empty(self::$registry[$id]['multi'])) $count = 1;
		else if ($count === null) $count = self::boughtCount($ship, $id);
		return self::flightPrice($ship, $id, $ship->flightSize, max(1, (int)$count));
	}

	/* The fighter officers' Stage 4 prices, as live ran them until 2026-10-15 (ab0025aba): pct of one
	   craft - a craft being pointCost / 6 unless the hull is a SuperHeavyFighter - and charged once per
	   flight or per craft. EXP_MOTIV's 10% per craft was also his price as an enhancement before that. */
	private static $legacyFighterPrices = array(
		'OFF_DOG' => array(50, 'flight'), 'OFF_MSL' => array(50, 'flight'),
		'OFF_EVAD' => array(100, 'craft'), 'EXP_MOTIV' => array(10, 'craft'),
	);

	/* What a saved officer row cost when it was saved with no price of its own (tac_saved_enh.enhvalue
	   NULL, every row saved before 2026-10-15): the Stage 4 prices for a fighter officer, plus the
	   Missileer's missiles per craft (unchanged); for a ship officer, his registry pct - every such row
	   predates the faction prices ($factionRules, 2026-10-15), so a Descari fleet's Scavenger was paid at 25%. */
	public static function legacyPrice($ship, $id){
		if (!isset(self::$registry[$id])) return 0;
		if (!isset(self::$legacyFighterPrices[$id])){
			return empty(self::$registry[$id]['flight']) ? self::shipPrice($ship, $id, false) : self::rowPrice($ship, $id);
		}
		list($pct, $per) = self::$legacyFighterPrices[$id];
		$crafts = max(1, (int)$ship->flightSize);
		$den = 10000 * (($ship instanceof SuperHeavyFighter) ? 1 : 6);
		$price = intdiv(max(0, (int)round($ship->pointCost * 100)) * $pct + $den - 1, $den);
		if ($per === 'craft') return $price * $crafts;
		if (!empty(self::$registry[$id]['perMissile'])) $price += (int)self::$registry[$id]['perMissile'] * self::missilesPerCraft($ship) * $crafts;
		return $price;
	}

	/* The fighter officers this flight row holds (not one who has already left it with his fighter): one, or
	   for an officer bought as a count (Stage 18) one entry per craft holding one. */
	private static function fighterOfficersAboard($flight){
		if (!($flight instanceof FighterFlight)) return array();
		$aboard = array();
		foreach ($flight->getOfficers() as $entry){
			if (self::isFighterOfficer($entry['id']) && $entry['how'] !== 'moved') $aboard[] = $entry;
		}
		return $aboard;
	}

	//How many fighter officers this row holds - HangarOps gives the side that stays these, less the ones leaving (officerAmong)
	public static function officersAboard($flight){
		return count(self::fighterOfficersAboard($flight));
	}

	/* What the fighter officers this row holds cost it: his rowPrice - or, for officers bought as a count, the row's price
	   shared out by the number bought, for those still aboard. */
	private static function officersCost($flight, array $aboard){
		if (empty($aboard)) return 0.0;
		$id = $aboard[0]['id'];
		$price = (float)self::rowPrice($flight, $id);
		if (empty(self::$registry[$id]['multi'])) return $price;
		$bought = max(1, self::boughtCount($flight, $id));
		return $price * min(count($aboard), $bought) / $bought;
	}

	/* The share of a flight's enhancement value HangarOps gives $k of its $n craft when it splits the
	   row in two - proportional to the craft, as it always was, EXCEPT a fighter officer's own price,
	   which goes whole to the side that has him (user ruling 2026-10-11: his cost moves with him).
	   $withOfficer: does this side have him? true / false, or (Stage 18) how many of the row's officers
	   it has - each Expert Pilot's share of the row's price goes with his fighter. With no officer aboard
	   this is exactly round($total * $k / $n), the split every caller made before. */
	public static function shareWithCraft($flight, $total, $k, $n, $withOfficer){
		if ($n <= 0) return 0;
		$aboard = self::fighterOfficersAboard($flight);
		$cost = max(0, min((float)$total, self::officersCost($flight, $aboard)));
		$share = (int)round(((float)$total - $cost) * $k / $n);
		$count = count($aboard);
		if ($count === 0) return $share;
		$with = is_bool($withOfficer) ? ($withOfficer ? $count : 0) : max(0, min($count, (int)$withOfficer));
		return $share + (int)round($cost * $with / $count);
	}

	/* The serving fighter officers whose posts (crafts) are among $crafts - the craft leaving the row. */
	private static function travellingOfficers($flight, array $crafts, $turn){
		$leaving = array();
		foreach ($crafts as $craft) if ($craft instanceof Fighter) $leaving[(int)$craft->id] = true;
		$travelling = array();
		foreach (self::fighterOfficersAboard($flight) as $entry){
			if (self::serves($entry, $turn) && isset($leaving[(int)$entry['post']])) $travelling[] = $entry;
		}
		return $travelling;
	}

	/* How many of this flight's fighter officers are among the craft leaving it (HangarOps, for shareWithCraft): 0 or 1, or
	   up to four Expert Pilots (Stage 18) - so it is truthy exactly when one is. */
	public static function officerAmong($flight, array $crafts, $turn){
		return count(self::travellingOfficers($flight, $crafts, $turn));
	}

	/* ⭐ A FIGHTER OFFICER FOLLOWS HIS FIGHTER (user ruling 2026-10-11). Called by HangarOps wherever craft
	   leave one flight row for a new one, just before their state is copied across in the order of
	   $crafts (a partial launch, a partial dock's fragment, a rail escape, a carrier-death escape, a
	   catapult's stash extraction). If the officer's craft is among them:
	     - it is moved to the FRONT of $crafts, so its state lands in the new row's first craft - his post
	       there, by the same rule as anywhere (findPost 'craft');
	     - the new row buys his row (tac_enhancements), mirrored in memory as copyFlightAmmoEnhancements
	       does for ammunition;
	     - the old row gets one OffMoved note on his old post, intercepted at load like OffKilled, so from
	       the next turn he is gone from it ('moved') - he still serves this turn, the next-turn rule.
	   Several Expert Pilots (Stage 18): every one whose craft leaves goes - their crafts first, so they land
	   in the new row's first crafts, its row bought at their count, and one OffMoved note on each one's craft.
	   Returns whether anyone moved. Their cost follows them through shareWithCraft, which the callers apply. */
	public static function followFighter($source, $target, array &$crafts, $gamedata){
		if (!($source instanceof FighterFlight) || !($target instanceof FighterFlight)) return false;
		$travelling = self::travellingOfficers($source, $crafts, $gamedata->turn);
		if (empty($travelling)) return false;

		$posts = array();
		foreach ($travelling as $entry) $posts[(int)$entry['post']] = true;
		$ordered = array();
		foreach ($crafts as $craft) if (isset($posts[(int)$craft->id])) $ordered[] = $craft;
		foreach ($crafts as $craft) if (!isset($posts[(int)$craft->id])) $ordered[] = $craft;
		$crafts = $ordered;

		$id = $travelling[0]['id']; //a flight's one officer, or several of one kind bought as a count (D9)
		$label = self::getLabel($id);
		$count = count($travelling);
		Manager::insertSingleEnhancement($gamedata, $target->id, $id, $count, $label);
		$target->enhancementOptions[] = array($id, $label, $count, 0, 0, 0);
		self::pickPosts($target);

		/* Read at the NEXT load. The old row's in-memory list is left alone: he serves it to the end of
		   this turn, and shareWithCraft, called after this, still has to see him there to price the split. */
		foreach ($travelling as $entry){
			Manager::insertIndividualNote(new IndividualNote(-1, $gamedata->id, $gamedata->turn, $gamedata->phase,
				$source->id, (int)$entry['post'], self::MOVE_NOTE, substr($label . ' left with his fighter', 0, 40), $entry['id']));
		}
		return true;
	}

	/* ------------------------------------------------------------------ payload */

	/* BaseShip::stripForJson, after the enhancement fields.
	   - The LIST goes to the owner's team, and to everybody once the game is over (D4), with out/how
	     only once he has left action.
	   - The STATS a serving officer moved (his registry `publish` list) go to EVERY viewer (user ruling
	     2026-10-10): the ship tooltip's "base" initiative, the profiles and the to-hit bonus have to be
	     the ship's real ones - a hidden +5 misleads more than it hides, and the shooter's own hit-chance
	     preview needs a Geneticist's profiles. So an enemy reads the numbers but not who is behind them.
	     A Technician's reactor output and a Matter expert's fire control need no line here: output and
	     an isModified weapon's fire control are sent by the systems themselves.
	   ⚠️ Cosmetic secrecy, like the refit star - §4.6 lists the numbers that reach the enemy. */
	public static function addForJson($ship, $strippedShip){
		$list = $ship->getOfficers();
		if (empty($list)) return;
		foreach ($list as $entry){
			$officer = self::$registry[$entry['id']];
			if (self::serves($entry, TacGamedata::$currentTurn)) $key = 'publish';
			else if (isset($officer['lost']) && self::isLost($entry, TacGamedata::$currentTurn)) $key = 'lostPublish'; //Stage 14: what his loss moved
			else continue;
			if (empty($officer[$key])) continue;
			foreach ($officer[$key] as $field) $strippedShip->$field = $ship->$field;
		}
		/* Stage 20: an Expert ELINT Officer's longer ranges and his blanket's ballistic divisor, to every viewer - an opponent's own OEW
		   under his Disruption, and its hit chance against a unit under his blanket, are previewed with them (elintRange,
		   ballisticBlanket). Numbers, not a name: the officer list below stays own-team. A disguised Chameleon ship's payload is its
		   simulacrum's, which carries neither. */
		if (self::isActive($ship, 'OFF_ELINT')){
			$strippedShip->elintRangeBonus = self::ELINT_RANGE_PCT;
			$strippedShip->blanketBallistic = self::ELINT_BLANKET_BALLISTIC;
		}
		if (!TacGamedata::$currentGameFinished && !$ship->isRevealedToCurrentViewer()) return;
		$out = array();
		$saves = $ship->getOfficerSaves();
		$turn = (int)TacGamedata::$currentTurn;
		foreach ($list as $i => $entry){
			$row = array('id' => $entry['id'], 'post' => $entry['post']);
			//the Scavenger's once-per-game save: the turn it was spent (officers.stateText words it)
			if (isset($saves[$entry['id']])) $row['used'] = $saves[$entry['id']];
			/* Stage 10: the post he is moving to this turn; the Gunner's spent abilities (ability => turn) and the
			   number of the Piercing mode added for him; and, in Initial Orders (the only phase a move is ordered
			   in), every post he may move to - the client offers those and no others (the server checks again:
			   saveTransfers). */
			if (isset($entry['moving'])) $row['moving'] = $entry['moving'];
			if (!empty($entry['abilities'])) $row['abilities'] = $entry['abilities'];
			if (isset($entry['mode'])) $row['mode'] = $entry['mode'];
			//Stage 19: the weapons an Expert Targeter covers, by id - the client's hit-chance preview reads them (officers.targeterShotBonus)
			if (self::rulesId($entry['id']) === 'OFF_TARG') $row['covers'] = self::coveredWeapons($ship, $entry);
			/* Stage 26: the software Specialists an Expert Software Engineer lets this ship use again now (officers.specialistReusable),
			   and the turn the one reuse was spent (officers.stateText words it, as a Gunner's spent abilities) */
			if ($entry['id'] === 'OFF_SOFT'){
				$reuse = self::specialistReuse($ship, $turn);
				if (!empty($reuse)) $row['reuse'] = $reuse;
				$spent = self::reuseSpentTurn(self::softwareUses($ship));
				if ($spent !== null) $row['abilities']['reuse'] = $spent;
			}
			if (!empty(self::$registry[$entry['id']]['movable']) && (int)TacGamedata::$currentPhase === 1 && !TacGamedata::$currentGameFinished){
				$targets = self::moveTargets($ship, $list, $i, $turn);
				if (!empty($targets)) $row['targets'] = $targets;
			}
			/* An officer who moved with his fighter is not out of action - he is on another row now:
			   listed here as serving up to the turn he left, and not at all afterwards. */
			if ($entry['how'] === 'moved'){
				if (self::serves($entry, TacGamedata::$currentTurn)) $out[] = $row;
				continue;
			}
			if ($entry['out'] !== null){
				$row['out'] = $entry['out'];
				$row['how'] = $entry['how'];
			}
			$out[] = $row;
		}
		if (empty($out)) return;
		$strippedShip->officers = $out;
	}
}
?>
