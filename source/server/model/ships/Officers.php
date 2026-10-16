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

	/* ⭐ FACTION RULES (user request 2026-10-15) - which factions an officer is offered to, and what some
	   of them pay. ONE table, so a faction's officers change here and nowhere else: README.md, "Officers:
	   faction availability and prices", is the how-to for a custom faction. An officer with no entry is
	   offered to every faction at his registry price. D9's exclusions (Ancients, OSATs, mines, terrain,
	   remotely controlled craft) and the hull's own tests (the post, `needs`) apply whatever this says.
	     only   offered ONLY to these factions
	     never  never offered to these factions
	     pct    his price in percent for these factions, in place of the registry's pct (of the hull, or
	            of one craft for a fighter officer); the registry's rounding still applies
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
	);

	/* One literal per officer (D6), walked in THIS order by apply() (D5).
	     label    the name in the buy dialog, the stored enhname and the kill note's notekey_human
	     post     a key into findPost()
	     pct      price in percent of the hull's blueprint pointCost, rounded up (D8) - of ONE CRAFT for
	              a fighter officer
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
	   Placed officers (Stage 8) carry, instead of a post key:
	     placed      true: the player picks his post, a weapon, in that system's own menu - a per-system row
	                 (Enhancements::$systemEnhancementRegistry), never an enhancementOptions offer
	     hosts       (optional) a further test the weapon must pass to host him (a method taking the system)
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
	     abilities   true: the Expert Gunner's once-per-battle faster reload, Piercing and called shot */
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

		/* FIGHTER OFFICERS (Stage 4). A flight carries ONE officer in total (D9), posted on its first
		   craft. He is lost with that craft - destroyed, or dropped out - and never by a dock, a partial
		   dock or a split launch: he stays on the flight row he was bought on, and a "- Split" row never
		   carries him. No kill roll: a craft has no d20 critical roll, only the d10 dropout test (user
		   rulings 2026-10-11). Priced off one craft, scaled by the flight's size (flightPrice). ⚠️ Saved
		   fleets bought under the Stage 4 prices are re-priced on reload (legacyPrice). */
		/* Expert Dogfighter: +5 initiative (FV units, QX1). 50% of a craft. */
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
		   always on (user ruling 2026-10-11). -1 OB, which is what every fighter to-hit path reads on
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
		if (isset(self::$factionRules[$rules]['pct'][$faction])) return (int)self::$factionRules[$rules]['pct'][$faction];
		return (int)self::$registry[$id]['pct'];
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
	   "one of each type" (D9); a flight's "one officer in total" is the buy dialog's to enforce, and
	   pickPosts' to defend. */
	public static function addOffers($ship){
		if (!self::mayCarryOfficers($ship)) return;
		foreach (self::$registry as $id => $officer){
			if (in_array($id, $ship->enhancementOptionsDisabled)) continue;
			if (!self::isEligible($ship, $id)) continue;
			$ship->enhancementOptions[] = array($id, $officer['label'], 0, 1, self::offerPrice($ship, $id), 0, false);
		}
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
		$pct = $byFaction ? self::pctFor($ship, $id) : (int)self::$registry[$id]['pct'];
		return self::price($ship->pointCost, $pct, !empty(self::$registry[$id]['roundDown']));
	}

	/* ceil(pct% of the blueprint cost), in whole numbers: 0.07 x 100 is 7.000000000000001 in floats,
	   and ceil() would charge 8 for it (SHIP_ENHANCEMENTS_PLAN.md Stage 5 hit the same trap). $down:
	   floor instead ("dropping fractions" - the Navigator), in the same whole numbers. */
	public static function price($pointCost, $pct, $down = false){
		$hundredths = max(0, (int)round($pointCost * 100));
		return intdiv($hundredths * (int)$pct + ($down ? 0 : 9999), 10000);
	}

	/* ceil(pct% of ONE craft), in whole numbers: pointCost is for a standard flight (standardFlight). */
	public static function craftPrice($flight, $pct){
		$hundredths = max(0, (int)round($flight->pointCost * 100));
		$den = 10000 * self::standardFlight($flight);
		return intdiv($hundredths * (int)$pct + $den - 1, $den);
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
	   price and a 12-craft flight twice it. ⚠️ MIRROR PAIR with officers.flightCost. */
	public static function flightPrice($flight, $id, $crafts){
		$officer = self::$registry[$id];
		$crafts = max(1, (int)$crafts);
		$standard = self::standardFlight($flight);
		$price = intdiv(self::craftPrice($flight, self::pctFor($flight, $id)) * $crafts + $standard - 1, $standard);
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
	     reactor  the biggest Reactor by maxhealth - Elite Crew's rule (a malus would sit on the main
	              reactor too); Mag-Grav reactors included.
	     engine   the strongest Engine by output - the one Poor Crew and ADV_ENG move (Stage 5)
	     jump     the first jump drive that opens jump points (Stage 7): not a legacy drive (the one-click
	              Hyperdrive / FTL Drive / Phasing Drive / Trek Nacelle, and every Ancient's), not a fixed
	              gate's. Through getUnitJumpEngines, never a sweep of $ship->systems ([[project_jump_points]]).
	              No officer-eligible hull carries two (measured 2026-10-15), so "his ship's drive" and "the
	              drive he sits in" cannot disagree.
	     craft    a flight's first craft (Stage 4): the lobby's one fighter card, and the sample fighter
	              getSampleFighter() answers - id 1, whether or not it is still flying.
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
			case 'reactor':
				return self::strongest($ship, 'Reactor', 'maxhealth', -1000);
			case 'engine':
				return self::strongest($ship, 'Engine', 'output', -1);
			case 'jump':
				foreach (JumpEngine::getUnitJumpEngines($ship) as $engine){
					if (!$engine->isLegacyJump() && !$engine->isGateJump()) return $engine;
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
		foreach ($flight->systems as $craft){
			if ($craft instanceof Fighter) return $craft;
		}
		return null;
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
	   is a flight's second officer row (D9: one officer in total): only the first in registry order serves. */
	public static function pickPosts($ship){
		$list = array();
		if (self::hasOfficerRow($ship) && self::mayCarryOfficers($ship)){
			foreach (self::$registry as $id => $officer){
				if (!self::isBought($ship, $id)) continue;
				if (!self::isEligible($ship, $id)) continue;
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
	     - no officer passes his ship cap (placedLimit).
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
		$hosted = array(); $count = array(); $kept = array();
		foreach ($keys as $key){
			list($id, $systemid) = $pairs[$key];
			if (!self::isPlacedOfficer($id) || isset($hosted[(int)$systemid])) continue;
			$n = isset($count[$id]) ? $count[$id] : 0;
			if ($n >= self::placedLimit($ship, $id)) continue;
			$hosted[(int)$systemid] = true;
			$count[$id] = $n + 1;
			$kept[] = $key;
		}
		return $kept;
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
	   so every host is a gun that rolls to hit. */
	public static function mayHost($ship, $system, $id){
		if (!self::isPlacedOfficer($id)) return false;
		if ($ship instanceof FighterFlight || !self::mayCarryOfficers($ship)) return false;
		if (in_array($id, $ship->enhancementOptionsDisabled)) return false; //the per-hull opt-out, as addOffers honours it
		if (!self::factionAllows($ship, $id)) return false;
		if (!($system instanceof Weapon)) return false;
		if (empty(self::$registry[$id]['hosts'])) return true;
		$hosts = self::$registry[$id]['hosts'];
		return (bool)self::$hosts($system);
	}

	//the cheap test that keeps every ordinary ship (a single 'NONE' row) out of the resolver
	private static function hasOfficerRow($ship){
		foreach ($ship->enhancementOptions as $entry){
			if ((int)$entry[2] > 0 && self::isOfficerId($entry[0])) return true;
		}
		return !empty(self::placedRows($ship));
	}

	private static function isBought($ship, $id){
		foreach ($ship->enhancementOptions as $entry){
			if ($entry[0] === $id && (int)$entry[2] > 0) return true;
		}
		return false;
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
			if (!self::serves($entry, $turn)) continue;
			$method = self::$registry[$entry['id']]['apply'];
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
		   DisengagedFighter bookkeeping that retires the row he left on the same turn. */
		$moves = $ship->getOfficerMoves();
		if (isset($moves[$entry['id']])) $take((int)$moves[$entry['id']], 'moved');
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
	       his own, and not destroyed;
	     - not the post of another placed officer still in action that turn or the next - one officer to a
	       weapon (Stage 8), and no swaps: the post must be free when he sets out. A disabled or killed
	       officer holds nobody's post. */
	private static function mayMoveTo($ship, array $list, $i, $target, $m){
		$entry = $list[$i];
		$stay = self::stayOn($entry, $m);
		if ($stay === null || empty(self::$registry[$stay['id']]['movable'])) return null;
		if (!empty($entry['transit']) && in_array((int)$m, $entry['transit'], true)) return null;
		if ((int)$target->id === (int)$stay['post']) return null;
		list($out) = self::outState($ship, $entry);
		if ($out !== null && $out < $m) return null; //"Disabled or Killed Officers cannot be moved"
		$as = self::postAs($ship, $stay['id'], $target);
		if ($as === null) return null;
		$fell = self::disabledTurn($ship, $target);
		if ($fell !== null && $fell < $m) return null;
		foreach ($list as $j => $other){
			if ($j === $i) continue;
			foreach (array($m, $m + 1) as $t){
				$held = self::stayOn($other, $t);
				if ($held === null || (int)$held['post'] !== (int)$target->id || !self::holdsAPost($held['id'])) continue;
				list($otherOut) = self::outState($ship, $other);
				if ($otherOut === null || $otherOut >= $m) return null;
			}
		}
		return $as;
	}

	/* The officer he would be at $system - his own id, or another sharing his rulesId (the Matter Weapons Expert:
	   OFF_MWE at the C&C, OFF_MWEW in a weapon) - or null when $system cannot host him. A placed officer needs
	   the per-system offer he could be BOUGHT on (the lobby's own test, so a move goes where a purchase could);
	   a ship-level one his own post. */
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
	   same formula is rolled here.
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
				$roll = $post->getCritRollOnTurn($turn);
				if ($roll === null) $roll = self::rollFor($ship, $post);
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

	//Expert Helmsman: "wins ties" - TacGamedata::sortShips, behind $officersPresent
	public static function winsInitiativeTies($ship){
		return self::isActive($ship, 'OFF_HELM');
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

	//+1 on every non-null fire control of a weapon's Matter modes, live and per mode (applyMatterExpert's rule)
	private static function raiseMatterModes($system){
		if (!empty($system->fireControlArray) && is_array($system->fireControlArray)){
			foreach ($system->fireControlArray as $mode => $fc){
				if (is_array($fc) && self::isMatterMode($system, $mode)) $system->fireControlArray[$mode] = self::bumpFireControl($fc);
			}
		}
		if (self::isMatterMode($system, $system->firingMode)) $system->fireControl = self::bumpFireControl($system->fireControl);
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
		if (!empty($weapon->fireControlArray) && is_array($weapon->fireControlArray)){
			foreach ($weapon->fireControlArray as $mode => $fc){
				if (is_array($fc)) $weapon->fireControlArray[$mode] = self::bumpFireControl($fc);
			}
		}
		$weapon->fireControl = self::bumpFireControl($weapon->fireControl);
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
	private static $abilityNames = array('reload' => 'faster reload', 'pierce' => 'Piercing shot', 'called' => 'called shot');

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
	     a called shot        otherwise, +3 ("a called shot at +3"), spent - never on a Piercing shot;
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
		if (!$called || !self::abilityOpen($entry, 'called', $turn)) return 0;
		self::spend($ship, $i, 'called', $turn, $gamedata);
		return self::GUNNER_CALLED_BONUS;
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
			if ((int)$order->calledid < 1 || !self::abilityOpen($entry, 'called', $fired)) continue;
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

	/* Weapon::stripForJson, for each laser of a ship he is aboard (serving or not), behind $officersPresent:
	   - his own team (and everybody once the game is over) is told the laser is BOOSTABLE while he serves - the
	     client then offers the generic boost buttons, with no maximum (no laser class sets one) - and what a point
	     costs, whenever its power entries are sent (power.js prices any boost it finds by boostEfficiency);
	   - anyone else gets the laser's power entries WITHOUT its boosts (user ruling 2026-10-17): no laser is boostable
	     without him, so a boosted one would name him. The extra damage still shows once the shots land. */
	public static function laserForJson($ship, $weapon, $strippedSystem){
		if (!self::isLaserWeapon($weapon) || !self::isAboard($ship, 'OFF_LASR')) return;
		if (TacGamedata::$currentGameFinished || $ship->isRevealedToCurrentViewer()){
			$strippedSystem->boostEfficiency = self::LASER_BOOST_COST;
			if (self::isActive($ship, 'OFF_LASR')) $strippedSystem->boostable = true;
			return;
		}
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

	/* Expert Evader, to hit: -1 OB, which every fighter to-hit path on both ends reads (and the
	   published stat the shooter's own preview needs). */
	private static function applyEvader($ship, $entry, $turn){
		$ship->offensivebonus -= 1;
	}

	/* Expert Evader, damage: 1 point off every hit on the flight - FighterFlight::getDamageMod, the
	   flight's defensive choke point (Weapon::getFinalDamage subtracts it), behind $officersPresent.
	   No client mirror: no client damage preview exists. */
	public static function evaderDamageReduction($flight){
		return self::isActive($flight, 'OFF_EVAD') ? 1 : 0;
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
	   with him when HangarOps splits a flight. */
	public static function rowPrice($ship, $id){
		if (!isset(self::$registry[$id])) return 0;
		if (empty(self::$registry[$id]['flight'])) return self::shipPrice($ship, $id);
		return self::flightPrice($ship, $id, $ship->flightSize);
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

	/* The fighter officer this flight row holds (not one who has already left it with his fighter), or null. */
	private static function fighterOfficerAboard($flight){
		if (!($flight instanceof FighterFlight)) return null;
		foreach ($flight->getOfficers() as $entry){
			if (self::isFighterOfficer($entry['id']) && $entry['how'] !== 'moved') return $entry;
		}
		return null;
	}

	/* The share of a flight's enhancement value HangarOps gives $k of its $n craft when it splits the
	   row in two - proportional to the craft, as it always was, EXCEPT a fighter officer's own price,
	   which goes whole to the side that has him (user ruling 2026-10-11: his cost moves with him).
	   $withOfficer: does this side have him? With no officer aboard this is exactly round($total * $k / $n),
	   the split every caller made before. */
	public static function shareWithCraft($flight, $total, $k, $n, $withOfficer){
		if ($n <= 0) return 0;
		$entry = self::fighterOfficerAboard($flight);
		$cost = $entry ? max(0, min((float)$total, (float)self::rowPrice($flight, $entry['id']))) : 0;
		$share = (int)round(((float)$total - $cost) * $k / $n);
		return $withOfficer ? $share + (int)round($cost) : $share;
	}

	/* The serving fighter officer whose post (craft) is among $crafts - the craft leaving the row - or null. */
	private static function travellingOfficer($flight, array $crafts, $turn){
		$entry = self::fighterOfficerAboard($flight);
		if ($entry === null || !self::serves($entry, $turn)) return null;
		foreach ($crafts as $craft){
			if ($craft instanceof Fighter && (int)$craft->id === (int)$entry['post']) return $entry;
		}
		return null;
	}

	//Is this flight's fighter officer among the craft leaving it? (HangarOps, for shareWithCraft)
	public static function officerAmong($flight, array $crafts, $turn){
		return self::travellingOfficer($flight, $crafts, $turn) !== null;
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
	   Returns whether he moved. His cost follows him through shareWithCraft, which the callers apply. */
	public static function followFighter($source, $target, array &$crafts, $gamedata){
		if (!($source instanceof FighterFlight) || !($target instanceof FighterFlight)) return false;
		$entry = self::travellingOfficer($source, $crafts, $gamedata->turn);
		if ($entry === null) return false;

		$ordered = array();
		foreach ($crafts as $craft) if ((int)$craft->id === (int)$entry['post']) $ordered[] = $craft;
		foreach ($crafts as $craft) if ((int)$craft->id !== (int)$entry['post']) $ordered[] = $craft;
		$crafts = $ordered;

		$label = self::getLabel($entry['id']);
		Manager::insertSingleEnhancement($gamedata, $target->id, $entry['id'], 1, $label);
		$target->enhancementOptions[] = array($entry['id'], $label, 1, 0, 0, 0);
		self::pickPosts($target);

		/* Read at the NEXT load. The old row's in-memory list is left alone: he serves it to the end of
		   this turn, and shareWithCraft, called after this, still has to see him there to price the split. */
		Manager::insertIndividualNote(new IndividualNote(-1, $gamedata->id, $gamedata->turn, $gamedata->phase,
			$source->id, (int)$entry['post'], self::MOVE_NOTE, substr($label . ' left with his fighter', 0, 40), $entry['id']));
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
			if (!self::serves($entry, TacGamedata::$currentTurn)) continue;
			if (empty(self::$registry[$entry['id']]['publish'])) continue;
			foreach (self::$registry[$entry['id']]['publish'] as $field) $strippedShip->$field = $ship->$field;
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
