'use strict';

/* OFFICERS (OFFICERS_PLAN.md, repo root) - the client half: the labels and one-line effects, the post
   a lobby officer would take, and the ONE list every insignia, tooltip line and ship-level listing
   reads (§7, "two tracks drift").

   WHERE THE LIST COMES FROM:
     - in GAME, and for anything the server built: ship.officers, sent by Officers::addForJson to the
       owner's team only (and to everybody once the game is over). An enemy's ship simply has none, so
       nothing on this side ever compares userids - the isRevealedToCurrentViewer pattern.
     - in the LOBBY, before the server has seen the fleet: derived from the bought officer rows, the way
       Officers::pickPosts derives it. Only the buyer's own browser holds those counts; server payloads
       never carry enhancementOptions, and a static blueprint's rows are all 0.
   Out of action is decided SERVER-SIDE from the full damage history and the kill notes (§4.4); the
   client only reads out/how. The one exception is the lobby's pre-battle damage preview: a post the
   player destroys there leaves its officer disabled from the start (§4.9), as the server will rule. */
window.officers = {

	ID_PREFIX: 'OFF_',

	/* ⚠️ MIRROR of Officers::$registry (PHP): ids, labels, posts, `needs`, `flight`, `perMissile`, `multi`, `mixesWith` and the placed
	   officers' `placed`, `sameAs` and `anyDamage`, in the SAME order - the order every list here is shown in. `summary` is the effect line the System
	   Info tooltip prints (D12). `stats` is what the officer adds to ship fields while he serves
	   (Officers::apply), which the lobby adds at READ time (displayStat); `adjust` names a method that
	   rewrites a field the same way when the change is not an addition (the Navigator's costs); his other
	   effects have their own previews below (reactorBonus, displayFireControl) or none the lobby can show
	   (the Scanner's saved EW).
	   Fighter officers (Stage 4) carry `flight: true`: priced for a standard flight and scaled by the
	   size bought (flightCost; user ruling 2026-10-15).
	   ⭐ FACTION RULES have no mirror here, on purpose: Officers::$factionRules (PHP) decides which offers
	   a hull's static blueprint carries and at what price, and a lobby row exists only for an offer - so
	   a faction's availability and prices are edited in that one table (README.md has the how-to). */
	REGISTRY: [
		{ id: 'OFF_HELM', label: 'Expert Helmsman', post: 'cnc', stats: { iniativebonus: 5 },
			summary: '+5 initiative, wins ties, -1 on meteor chart' },
		{ id: 'OFF_SCAN', label: 'Expert Scanner', post: 'scanner',
			summary: 'May save 1 more EW point to spend in Firing Phase' },
		{ id: 'OFF_TECH', label: 'Expert Technician', post: 'reactor',
			summary: '+2 power, +1 when a system is switched off, +1/3 of each destroyed system\'s power; -2 on reactor criticals' },
		{ id: 'OFF_GENE', label: 'Expert Geneticist', post: 'cnc', stats: { forwardDefense: 2, sideDefense: 2, toHitBonus: -1 },
			summary: '-1 to hit with own weapons, +2 to be hit, enemies may ram this ship' },
		{ id: 'OFF_MWE', label: 'Matter Weapons Expert', post: 'cnc', needs: 'matterWeapon', movable: true,
			summary: '+1 fire control and +1 damage for Matter weapons' },
		{ id: 'OFF_ENG', label: 'Expert Engineer', post: 'engine',
			summary: 'Repairs 1 critical per turn (a C&C critical on a d20 of 16+)' },
		{ id: 'OFF_SCAV', label: 'Expert Scavenger', post: 'engine',
			summary: 'Repairs 1 critical or 4 boxes per turn; once per game blocks up to 6 damage that would destroy a crticial system' },
		{ id: 'OFF_NAV', label: 'Expert Navigator', post: 'cnc', adjust: 'navigatorStat',
			summary: 'Turn cost one level lower, pivot and roll cost -1, accel/decel cost -25%' },
		{ id: 'OFF_JUMP', label: 'Expert Jump Officer', post: 'jump',
			summary: 'Open Jump points +1 range and maintain +1 turn; jump failure chance -20%; incoming jump points scatter less' },
		/* Stage 11: no stat moves - his flat bonus and the boost are the server's at damage time; the owner's tooltip
		   shows both (adjustDataDisplay, laserBoostText), and the boost buttons come with the payload (boostable). */
		{ id: 'OFF_LASR', label: 'Expert Laser Technician', post: 'reactor', needs: 'laserWeapon',
			summary: 'Lasers +1 damage (Blast Lasers +2, Spinal Lasers +3). Power may be put into any laser in Initial Orders: +1 damage per point' },
		/* Stage 12: his damage and range-damage rules are the server's at damage time (the owner's Damage line shows the
		   +1/+2 - plasmaDamageLine); a battery's extra point is charged at the commit (batteryCapacityBonus); a web's
		   range arrives in the payload. */
		{ id: 'OFF_PLAS', label: 'Expert Plasma Scientist', post: 'engine', needs: 'plasmaWeapon',
			summary: 'Plasma weapons +1 damage and ignore 2 hexes of range damage penalty; Plasma Batteries have +1 power and -3 on crits; Plasma Webs +1 range' },
		/* Stage 13: the initiative is previewed (stats); his -2 on the C&C's criticals and the boarding shift are the server's
		   alone (no client shows a critical modifier or a delivery or mission roll). */
		{ id: 'OFF_SECU', label: 'Expert Security Officer', post: 'cnc', stats: { iniativebonus: 5 },
			summary: '+5 initiative, -2 on C&C criticals; enemy boarding rolls against this ship 2 worse' },
		/* Stage 14: the initiative is previewed (stats); his +1 per hit raises the owner's Damage lines (religiousDamageLine);
		   his crew's frenzy once he is lost moves fields the server publishes, and a scanner's output, which it always sends.
		   `movable`: to any primary non-weapon system - the server's `targets` say which. */
		{ id: 'OFF_RELI', label: 'Expert Religious Leader', post: 'cnc', movable: true, stats: { iniativebonus: 10 },
			summary: '+10 initiative, +1 damage on every hit; once he is lost: +20 initiative, +1 sensors, +5% to be hit' },
		/* Stage 16: the reactor's extra power is previewed in the lobby (reactorBonus - in game the server raised `output`); his +1 per
		   EM hit raises the owner's Damage lines (surgeDamageLine); his hardening and the Spark Field relief are the server's alone. */
		{ id: 'OFF_SURG', label: 'Expert Surge Officer', post: 'maggrav',
			summary: 'EM weapons +1 damage; Extra power from Reactor; EM criticals -3, Shutdowns resisted on 1-2; Spark Fields deal 1 less damage to this ship' },
		/* Stage 17: no stat moves - his bonuses are the server's at damage time. The owner's Damage line shows a fixed-damage ion
		   weapon's +1, the Rad Cannon's 12 and this turn's thrust boost (ionDamageLine); his +1 per die, like Elite Crew's and a
		   Gunner's, is in the summary only. The boost buttons come with the payload (boostable, priced in thrust). */
		{ id: 'OFF_ION', label: 'Expert Ion Technician', post: 'engine', needs: 'ionWeapon',
			summary: 'Ion weapons have increased damage; Rad Cannons stronger; Boost ion weapons with thrust' },
		/* Stage 20: in the ELINT array. Its +1 output is previewed in the lobby (sensorBonus - in game the server raised `output`); the
		   -2 on sensor criticals is the server's alone. His longer ranges and his blanket against ballistics are read in game off the
		   numbers the server publishes to every viewer (elintRange, ballisticBlanket); the array's tooltip shows both (elintSpecial). */
		{ id: 'OFF_ELINT', label: 'Expert ELINT Officer', post: 'elint',
			summary: '+1 sensors, -2 on sensor criticals; ELINT ranges +25%; Blanket Protection gives +1 DEW per 3 points against ballistic weapons' },
		/* Stage 23: no stat moves. His initiative roll and his extra damage die are the server's; his Firing-phase turn is
		   movement.js's (canAnticipate - the turn arrows in the Firing phase). */
		{ id: 'OFF_ANTI', label: 'Expert Anticipator', post: 'cnc',
			summary: 'Initiative rolled twice, best kept; one turn in the Firing Phase; damage rolls of 2+ dice roll one extra die and drop the lowest' },
		/* Stage 25: Centauri capital ships and larger (`needs`; the faction is the offers'). No stat moves in the lobby: his free expert and
		   free Hardened Armour are PRICES (freeExpertId - the buy dialog and the fleet list; freeArmourRows - systemEnhancements.totalCost),
		   his armed shuttle is the server's hangars', and his speech is his C&C's Activate box in the Firing phase (canSpeak). */
		{ id: 'OFF_POL', label: 'Expert Political Officer', post: 'cnc', needs: 'capital',
			summary: 'An armed shuttle, one free Expert Navigator, Helmsman, Engineer or Scanner, and 5 free Hardened Armour (not on Structure); once per battle a speech in the Firing Phase: +30 initiative next turn' },
		/* Stage 26: Hyach only (the offers' - his post, the Hyach Computer, is Hyach-only too). His +1 BFCP is previewed in the lobby (computerBonus -
			in game the server raised `output`); the point he lets the player hold to the Firing phase is the Computer's own menu (HyachComputer
			.firingMode); his armour point and signature masking are the server's; his Specialist reuse comes in his payload (`reuse` -
			specialistReusable). */
		{ id: 'OFF_SOFT', label: 'Expert Software Engineer', post: 'computer',
			summary: '+1 BFCP (lost only with the Computer), which may be held back to the Firing Phase; the Computer ignores the first point of every hit; a stealth ship is detected as if 5 hexes further away (fighters at half range); one Defence, Sensor, Targeting or Weapon Specialist may be used a second time' },
		/* Stage 27: Gaim only (the offers'), in the largest Hangar. His boarding shift is the server's. The pods he aids - as many of his player's
		   pods, in fleet order, as his ship's Pod Allowance - are named in their Marines' "Elite" line to his team (marinesEliteLine, through
		   troopAidedCraft). The Bulkheads' extra box is previewed in the lobby (troopBulkheads, from lobbyEnhancements - in game the server
		   sends the boxes). */
		{ id: 'OFF_TROOP', label: 'Expert Troop Leader', post: 'hangar',
			summary: '-2 to enemy boarding rolls, +2 to defenders; +1 to fleet Marine rolls; every Bulkhead +1 box' },
		/* Stage 28: Drazi only (the offers'), in the C&C. The lobby previews his +10 initiative (stats - in game the server applied and published it,
		   and gives it to the flights his ship launches) and his +1 fire control on every weapon (displayFireControl - in game the server raised it).
		   The +2 at a unit his charge holds against and his precision strike are the hit-chance preview's (chargeHitBonus, precisionStrike); his +1
		   per die and both critical modifiers are the server's. The Charge is movement.js's (its icon - canCharge - and the reticle on the unit
		   charged, getChargeMarked), his Supreme Anticipation his C&C's Activate box in the Firing Phase (canAnticipateSupreme). */
		{ id: 'OFF_WARR', label: 'Expert Warrior', post: 'cnc', stats: { iniativebonus: 10, critRollMod: -1 },
			summary: '+1 fire control and +1 damage on all weapons; +10 initiative, also for launched flights; -1 on crit rolls, +1 on crits caused. Once per battle each, one a turn: Charge, precision strike, Supreme Anticipation' },
		/* Stage 29: in the C&C, on hulls with counted ballistic ammunition (`needs`; the offers' too). No stat moves. His +5 distance range is the
		   hit-chance preview's and the Range line's (distanceRangeBonus - in game the number the server publishes to every viewer); his magazine
		   slots and free missiles are the buy dialog's and the fleet list's (getMaxAmmoFit, ballisticFreeRounds - off the static blueprint's
		   ballisticPicks / ballisticSlots), and the server's at the commit, which also gives each counted weapon its extra round. */
		{ id: 'OFF_BALL', label: 'Expert Ballistic Officer', post: 'cnc', needs: 'countedBallistic',
			summary: '+5 distance range for missiles; +1 round per launcher; free missile per launcher' },
		/* Stage 30: in the C&C, on every hull with one (10% Hurr, 20% elsewhere - the offers'). The lobby previews his -30 initiative (stats - in
		   game the server applied and published it, and keeps it once he is lost). His free refit and free missiles are PRICES (quartermasterRefit
		   - systemEnhancements.totalCost, the buy dialog and the fleet list; quartermasterFreeRounds - off the static blueprint's
		   quartermasterPicks); the server rolls what is left at the commit. His turn delay is movement.js's (getQuartermasterTurnDelay, off the
		   published turnDelayPenalty); the patch, the C&C's +2 and the Improved Reactor's tripled breach are the server's. `stock`: everything he
		   brings stays when he is lost (user ruling 2026-10-24), so a patch used after it still shows. */
		{ id: 'OFF_QM', label: 'Expert Quartermaster', post: 'cnc', stats: { iniativebonus: -30 }, stock: true,
			summary: 'One free refit; Free missiles; first crit ignored. -30 initiative, +10% turn delay, +2 C&C criticals' },
		/* PLACED officers (Stage 8): posted in the weapon the player buys him on, from that system's own menu
		   (a per-system row - systemEnhancements.js), so they have no `post`. `sameAs`: the ship-level officer he
		   IS at another post (one or the other); `anyDamage`: disabled by any damage to his weapon.
		   Stage 10: `movable` - may be moved to another post in Initial Orders (the Matter Weapons Expert between
		   the C&C and his Matter weapons, as one man); `abilities` - the Gunner's once-per-battle three;
		   `reload` - his weapon reloads a turn faster. */
		{ id: 'OFF_GUN', label: 'Expert Gunner', placed: true, movable: true, abilities: true,
			summary: '+1 fire control and +1 damage per die. Once per battle each:  +1 turn towards reload, Piercing shot (or ignore Piercing FC penalty), +15% on called shot' },
		{ id: 'OFF_MWEW', label: 'Matter Weapons Expert', placed: true, sameAs: 'OFF_MWE', anyDamage: true, movable: true, reload: true,
			summary: '+1 fire control and +1 damage for this Matter weapon, which reloads a turn faster;' },
		/* Stage 15: the Expert Religious Leader away from his C&C - in any other primary non-weapon system (the server's
		   offers say which). The same officer as OFF_RELI (sameAs), with the same effects. */
		{ id: 'OFF_RELIP', label: 'Expert Religious Leader', placed: true, sameAs: 'OFF_RELI', movable: true, stats: { iniativebonus: 10 },
			summary: '+10 initiative, +1 damage on every hit; once he is lost: +20 initiative, +1 sensors, +5% to be hit' },
		/* Stage 19: the Expert Targeter, in a STRUCTURE block (ShipSection's bar carries his insignia, his tooltip and, in Initial
		   Orders, his menu). His +4 is the hit-chance preview's (targeterShotBonus), over the weapons the server says he covers
		   (`covers`). On a hull with one block (a Medium ship) he covers one side of its hit chart: OFF_TARG the first (Front, or
		   Left), OFF_TARG2 the second (sameAs) - `side` - and `switchTo` is the side selector's other id (a move to his own post). */
		{ id: 'OFF_TARG', label: 'Expert Targeter', placed: true, movable: true, side: 1, switchTo: 'OFF_TARG2',
			summary: '+20% on called shots (the called shot penalty still applies)' },
		{ id: 'OFF_TARG2', label: 'Expert Targeter', placed: true, sameAs: 'OFF_TARG', movable: true, side: 2, switchTo: 'OFF_TARG',
			summary: '+20% on called shots (the called shot penalty still applies)' },
		/* Stage 22: the Expert Turret Officer, in a turret-mounted weapon, aiding every weapon on that turret (turretKey: its linked firing
		   group, or the gun alone). One to a turret (`perTurret`). His +1 fire control is previewed in the lobby (displayFireControl - in game
		   the server raised it); his -1 on criticals, the -2 on the jam roll (the Turret Mount line says it - turretMountLine) and the shelter
		   are the server's. */
		{ id: 'OFF_TURR', label: 'Expert Turret Officer', placed: true, movable: true, perTurret: true,
			summary: 'Turret weapons: +1 fire control, -1 on criticals, -2 on the jam roll; if turret does not fire, can only be hit by called shots' },
		{ id: 'OFF_DOG', label: 'Expert Dogfighter', post: 'craft', flight: true, stats: { iniativebonus: 5 },
			summary: '+5 initiative, wins ties' },
		{ id: 'OFF_MSL', label: 'Expert Missileer', post: 'craft', flight: true, perMissile: 1, needs: 'missiles',
			summary: '+5 offensive bonus for missiles' },
		/* No `stats`: his -1 OB is only on a turn the flight Evades (user request 2026-10-20) - the server's once the row is
		   sent, pendingEvadeOb's until then. */
		{ id: 'OFF_EVAD', label: 'Expert Evader', post: 'craft', flight: true,
			summary: 'Evade in the Firing phase: that turn -5 offensive bonus, -1 damage to hits on this flight' },
		{ id: 'EXP_MOTIV', label: 'Expert Motivator', post: 'craft', flight: true, stats: { critRollMod: -2 },
			summary: '-2 on dropout rolls' },
		{ id: 'OFF_COOR', label: 'Expert Coordinator', post: 'craft', flight: true,
			summary: 'Halves combat pivot costs and gives no to-hit penalty, unless flight turns a full 180 degrees' },
		/* Stage 18: bought as a COUNT (`multi`) - one in each of the flight's first crafts, every one adding his OB (stats, per
		   entry) and a free jinking level beyond the limit (freeJinks, read by movement.js). His Spark Field relief is the
		   server's alone. The count's limit arrives in the offer (Officers::maxCount: 4, Raiders 2), and rowMax holds it to the
		   flight size in the buy dialog. */
		{ id: 'OFF_PILOT', label: 'Expert Pilot', post: 'craft', flight: true, multi: true, stats: { offensivebonus: 1 },
			summary: 'Each: +5 offensive bonus and one free jinking level beyond the limit' },
		/* Stage 21: only for a flight with a tail gun (`needs`). No stat moves - his effect is the hit-chance preview's
		   (weaponManager.getTailGunMod) and the tail gun's tooltip line (tailGunLine). */
		{ id: 'OFF_TAIL', label: 'Expert Tailgunner', post: 'craft', flight: true, needs: 'tailGun',
			summary: 'No penalty for firing the tail gun with the other guns; +5% with a tail gun that has no penalty' },
		/* Stage 24: only for a breaching pod (`needs`), in its first pod. `unscaled`: his price is his ONE pod's, whatever the flight's
		   size (flightCost). The flight's +1 thrust is previewed (stats - in game the server raised and published it); his pod's Elite
		   Marines are the server's at delivery, and its Marines' "Elite" line says so to his own team (marinesEliteLine); his capture
		   assault is the server's alone. The fleet's 20% is the Fleet Correctness Report's (breachingFleetCheck).
		   Stage 27: one to a pod, as many as the flight has (`multi`), beside Expert Troop Leaders (`mixesWith`: one officer to a pod, the
		   Breaching Experts first); the flight's thrust +1 however many (`statsOnce`). */
		{ id: 'OFF_BRCH', label: 'Breaching Expert', post: 'craft', flight: true, unscaled: true, multi: true, mixesWith: ['OFF_TRPOD'], statsOnce: true,
			needs: 'breachingPod', stats: { freethrust: 1 },
			summary: '+1 thrust (once per flight); his pod\'s Marines count as Elite; once per battle, his pod\'s first capture party makes three attack rolls on its first turn aboard' },
		/* Stage 27: the Expert Troop Leader in a Gaim breaching pod (the offers' - `needs` the pod), one to a pod (`multi`, `mixesWith`), priced
		   at his own pod (`unscaled`). His -3 and his escape roll are the server's; his pod's Marines' "Elite" line names him to his team
		   (marinesEliteLine). */
		{ id: 'OFF_TRPOD', label: 'Expert Troop Leader', post: 'craft', flight: true, unscaled: true, multi: true, mixesWith: ['OFF_BRCH'],
			needs: 'breachingPod',
			summary: '+3 on every roll of his pod\'s marine; when eliminated escapes on a d6 of 1-4' }
	],

	/* An OFF_ id, or an officer kept under the id he had before officers existed (EXP_MOTIV, the Expert
	   Motivator). ⚠️ MIRROR PAIR with Officers::isOfficerId (PHP). */
	isOfficerId: function isOfficerId(enhID) {
		return typeof enhID === 'string' && (enhID.indexOf(this.ID_PREFIX) === 0 || this.get(enhID) !== null);
	},

	isFighterOfficer: function isFighterOfficer(enhID) {
		var officer = this.get(enhID);
		return Boolean(officer && officer.flight);
	},

	get: function get(id) {
		for (var i = 0; i < this.REGISTRY.length; i++) {
			if (this.REGISTRY[i].id === id) return this.REGISTRY[i];
		}
		return null;
	},

	label: function label(id) {
		var officer = this.get(id);
		return officer ? officer.label : String(id);
	},

	/* The system an officer of this post would live in, or null.
	   ⚠️ MIRROR PAIR with Officers::findPost (PHP). Duck-typed, never instanceof: a bought lobby ship is a
	   jQuery.extend clone with no prototype chain ([[arch_lobby_ship_objects]]). A Flag Bridge is a
	   "cnC" carrying the FlagBridge special ability - the class property every FlagBridge declares.
	     cnc      the first C&C that is not a Flag Bridge; failing that, the first one of any kind
	     scanner  the strongest scanner by output - the list and the pick Elite Crew's lobby mirror uses
	     elint    the strongest ELINT array by output (Stage 20): a scanner named elintScanner, or the Chameleon suite
	     reactor  the biggest reactor by maxhealth - likewise
	     engine   the strongest engine by output - the pick Poor Crew's and ADV_ENG's lobby mirrors use
	     maggrav  the first Mag-Gravitic Reactor that can be hit: a reactor with fixedPower (the Thirdspace drive has it
	              too, but it is Ancient - no officers) that is targetable (never a MagGravReactorTechnical)
	     jump     the first jump drive that opens jump points: a legacy drive's range is 0 (markLegacy), the
	              lobby JUMP_ACC case's own test. The one gate drive (JumpgateCapital) is on terrain, which
	              carries no officers.
	     computer the Hyach Computer (Stage 26)
	     hangar   the largest plain hangar bay by boxes (Stage 27), the first on a tie: a "hangar" that is not a docking bay - catapults,
	              fighter rails and docking collars have names of their own
	     craft    a flight's first craft - the lobby's one fighter card, id 1
	   ⚠️ D2: the server picks from BLUEPRINT values, before any enhancement moves an output, but a lobby
	   fleet ship has already had lobbyEnhancements.apply - Poor Crew has taken a point off its strongest
	   scanner. So outputs are read off the store's pristine copy of the hull when the page has one. */
	findPost: function findPost(ship, post) {
		if (!ship || !ship.systems) return null;
		var systems = this.systemsOf(ship);
		if (post === 'craft') {
			return ship.flight ? this.firstCraft(ship) : null;
		}
		if (post === 'cnc') {
			var any = null;
			for (var i = 0; i < systems.length; i++) {
				var system = systems[i];
				if (!system || system.name !== 'cnC') continue;
				var isBridge = Array.isArray(system.specialAbilities) && system.specialAbilities.indexOf('FlagBridge') !== -1;
				if (!isBridge) return system;
				if (any === null) any = system;
			}
			return any;
		}
		if (post === 'scanner') {
			var scanners = (window.shipManager && shipManager.systems && typeof shipManager.systems.getScannerList === 'function')
				? shipManager.systems.getScannerList(ship) : [];
			return this.strongest(ship, scanners, 'output', -1);
		}
		if (post === 'elint') {
			var arrays = (window.shipManager && shipManager.systems && typeof shipManager.systems.getScannerList === 'function')
				? shipManager.systems.getScannerList(ship).filter(function (s) { return s && (s.name === 'elintScanner' || s.name === 'chameleonSensors'); }) : [];
			return this.strongest(ship, arrays, 'output', -1);
		}
		if (post === 'reactor') {
			return this.strongest(ship, systems.filter(function (s) { return s && s.name === 'reactor'; }), 'maxhealth', -1000);
		}
		if (post === 'engine') {
			return this.strongest(ship, systems.filter(function (s) { return s && s.name === 'engine'; }), 'output', -1);
		}
		if (post === 'maggrav') {
			for (var m = 0; m < systems.length; m++) {
				var r = systems[m];
				if (r && r.name === 'reactor' && r.fixedPower && r.isTargetable !== false) return r;
			}
			return null;
		}
		if (post === 'jump') {
			for (var j = 0; j < systems.length; j++) {
				if (systems[j] && systems[j].name === 'jumpEngine' && Number(systems[j].range) > 0) return systems[j];
			}
			return null;
		}
		if (post === 'computer') {
			for (var c = 0; c < systems.length; c++) {
				if (systems[c] && systems[c].name === 'hyachComputer') return systems[c];
			}
			return null;
		}
		if (post === 'hangar') {
			return this.strongest(ship, systems.filter(function (s) { return s && s.name === 'hangar' && !s.isDockingBay; }), 'maxhealth', -1);
		}
		return null;
	},

	//lobbyEnhancements.strongestOf's semantics (the first wins a tie), on the BLUEPRINT value of `field`
	strongest: function strongest(ship, systems, field, floor) {
		var pristine = this.pristineSystems(ship);
		var best = null;
		var bestValue = floor;
		for (var i = 0; i < systems.length; i++) {
			var system = systems[i];
			var original = pristine ? pristine[String(system.id)] : null;
			var value = Number(original && original[field] !== undefined ? original[field] : system[field]);
			if (value > bestValue) {
				bestValue = value;
				best = system;
			}
		}
		return best;
	},

	/* The hull's systems as the store holds them, by id - gamedata.allShips is never given enhancements
	   (gamelobby.js says so where it builds the fleet window). null outside the lobby, and in a harness. */
	pristineSystems: function pristineSystems(ship) {
		if (!window.gamedata || !gamedata.allShips || !ship.phpclass) return null;
		var list = gamedata.allShips[ship.faction];
		if (!list) return null;
		var store = Array.isArray(list) ? list : Object.values(list);
		for (var i = 0; i < store.length; i++) {
			if (!store[i] || store[i].phpclass !== ship.phpclass || store[i] === ship) continue;
			var byId = {};
			this.systemsOf(store[i]).forEach(function (s) { if (s) byId[String(s.id)] = s; });
			return byId;
		}
		return null;
	},

	//ship.systems as an array: an array in game, keyed by id on a lobby blueprint
	systemsOf: function systemsOf(ship) {
		if (!ship || !ship.systems) return [];
		return Array.isArray(ship.systems) ? ship.systems : Object.values(ship.systems);
	},

	//a flight's first craft: its systems are keyed by id (holes allowed - doLoadFleet leaves them), id 1 first
	firstCraft: function firstCraft(ship) {
		var systems = this.systemsOf(ship);
		for (var i = 0; i < systems.length; i++) {
			if (systems[i]) return systems[i];
		}
		return null;
	},

	/* Missiles one craft can carry: its magazine's capacity (rounds, not salvoes) plus each legacy
	   launcher's own load - duck-typed as the FighterMissileRack family, the only systems with both a
	   missileArray and a maxAmount. Read off the first craft's blueprint fields, which no enhancement
	   moves. ⚠️ MIRROR PAIR with Officers::missilesPerCraft (PHP). */
	missilesPerCraft: function missilesPerCraft(ship) {
		if (!ship || !ship.flight) return 0;
		var craft = this.firstCraft(ship);
		if (!craft || !craft.systems) return 0;
		var total = 0;
		this.systemsOf(craft).forEach(function (s) {
			if (!s) return;
			if (s.name === 'ammoMagazine') total += parseInt(s.capacity, 10) || 0;
			else if (s.missileArray && s.maxAmount !== undefined) total += parseInt(s.maxAmount, 10) || 0;
		});
		return total;
	},

	/* An officer's own test beyond having the post. ⚠️ MIRROR PAIR with Officers::isEligible's `needs`.
	     matterWeapon  the hull carries a weapon with a Matter mode (the Matter Weapons Expert)
	     laserWeapon   the hull carries a weapon with a Laser mode (the Expert Laser Technician)
	     plasmaWeapon  the hull carries a weapon with a Plasma mode (the Expert Plasma Scientist)
	     ionWeapon     the hull carries a weapon with an Ion mode, or an Ion Torpedo (the Expert Ion Technician)
	     missiles      the flight carries missiles (the Expert Missileer)
	     tailGun       the flight's craft has a tail gun - a weapon the blueprint flags `tailGun` (the Expert Tailgunner)
	     breachingPod  the flight is a breaching pod (isBreachingPod - the Breaching Expert)
	     capital       a capital ship or larger, size class 3 (the Expert Political Officer)
	     countedBallistic  a magazine-fed ballistic launcher, or a ballistic weapon keeping a count of rounds (the Expert Ballistic Officer) */
	passesNeeds: function passesNeeds(ship, officer) {
		if (!officer.needs) return true;
		var self = this;
		if (officer.needs === 'capital') return (parseInt(ship.shipSizeClass, 10) || 0) >= 3;
		if (officer.needs === 'matterWeapon') {
			return this.systemsOf(ship).some(function (s) { return self.isMatterWeapon(s); });
		}
		if (officer.needs === 'laserWeapon') {
			return this.systemsOf(ship).some(function (s) { return self.isLaserWeapon(s); });
		}
		if (officer.needs === 'plasmaWeapon') {
			return this.systemsOf(ship).some(function (s) { return self.isPlasmaWeapon(s); });
		}
		if (officer.needs === 'ionWeapon') {
			return this.systemsOf(ship).some(function (s) { return self.isIonWeapon(s); });
		}
		if (officer.needs === 'missiles') return this.missilesPerCraft(ship) > 0;
		if (officer.needs === 'tailGun') {
			var craft = ship.flight ? this.firstCraft(ship) : null;
			return !!craft && this.systemsOf(craft).some(function (s) { return s && s.tailGun === true; });
		}
		if (officer.needs === 'breachingPod') return this.isBreachingPod(ship);
		if (officer.needs === 'countedBallistic') {
			var systems = this.systemsOf(ship);
			if (systems.some(function (s) { return self.isCountedBallistic(s) && s.checkAmmoMagazine !== true; })) return true;
			return systems.some(function (s) { return self.isCountedBallistic(s); }) && this.hasMagazineOrdnance(ship);
		}
		return false;
	},

	/* A BREACHING POD: a flight whose craft carries Marines and that FV names a breaching pod - the 'Breaching Pods' hangar class, or
	   "Breaching Pod" in its class name (the Minbari Protectorate's and the Nausicaan pods ride shuttle hangars). Never a superheavy
	   carrying Marines. ⚠️ MIRROR PAIR with Officers::isBreachingPod (PHP). */
	isBreachingPod: function isBreachingPod(ship) {
		if (!ship || !ship.flight) return false;
		if (ship.hangarRequired !== 'Breaching Pods' && String(ship.shipClass || '').toLowerCase().indexOf('breaching pod') === -1) return false;
		var craft = this.firstCraft(ship);
		return !!craft && this.systemsOf(craft).some(function (s) { return s && (s.name === 'Marines' || s.name === 'ExtraMarines'); });
	},

	//⚠️ MIRROR PAIR with Officers::isLaserWeapon (PHP)
	isLaserWeapon: function isLaserWeapon(system) {
		if (!system || !system.weapon) return false;
		if (system.weaponClass === 'Laser') return true;
		var classes = system.weaponClassArray;
		if (!classes || typeof classes !== 'object') return false;
		return Object.keys(classes).some(function (mode) { return classes[mode] === 'Laser'; });
	},

	//a mode missing from weaponClassArray keeps the weapon's own class (as the server's changeFiringMode leaves it)
	isLaserMode: function isLaserMode(system, mode) {
		var classes = system.weaponClassArray;
		var cls = (classes && typeof classes === 'object' && classes[mode] !== undefined) ? classes[mode] : system.weaponClass;
		return cls === 'Laser';
	},

	//⚠️ MIRROR PAIR with Officers::isMatterWeapon / ::isMatterMode
	isMatterWeapon: function isMatterWeapon(system) {
		if (!system || !system.weapon) return false;
		if (system.weaponClass === 'Matter') return true;
		var classes = system.weaponClassArray;
		if (!classes || typeof classes !== 'object') return false;
		return Object.keys(classes).some(function (mode) { return classes[mode] === 'Matter'; });
	},

	isMatterMode: function isMatterMode(system, mode) {
		var classes = system.weaponClassArray;
		var cls = (classes && typeof classes === 'object' && classes[mode] !== undefined) ? classes[mode] : system.weaponClass;
		return cls === 'Matter';
	},

	/* Every officer aboard, as array({id, post, out?, how?}) in registry order. See the header for
	   which of the two sources answers. A flight's derived list holds ONE officer at most (D9), the
	   first bought in registry order, as Officers::pickPosts serves - or, for an officer bought as a
	   count (`multi`, Stage 18), that many entries: one in each of its first crafts, held to the row's
	   limit and the flight's size; then (Stage 27) an officer he `mixesWith` in the crafts after his, and
	   nobody else. A lobby flight holds a single craft card, so there they share it. */
	listFor: function listFor(ship) {
		if (!ship) return [];
		if (Array.isArray(ship.officers)) return ship.officers;
		if (!Array.isArray(ship.enhancementOptions)) return [];

		var list = [];
		var first = null; //a flight's first officer bought as a count, whose `mixesWith` may follow him
		var crafts = 0;   //...and the crafts taken so far
		for (var i = 0; i < this.REGISTRY.length; i++) {
			var officer = this.REGISTRY[i];
			if (officer.placed) continue; //track 2: placedEntries below
			if (Boolean(officer.flight) !== Boolean(ship.flight)) continue;
			if (!this.isBought(ship, officer.id)) continue;
			var post = this.findPost(ship, officer.post);
			if (!post || !this.passesNeeds(ship, officer)) continue;
			if (first !== null && first.mixesWith.indexOf(officer.id) === -1) continue;
			if (officer.multi && ship.flight) {
				var n = Math.min(this.boughtCount(ship, officer.id), this.rowLimit(ship, officer.id), (parseInt(ship.flightSize, 10) || 1) - crafts);
				for (var k = 1; k <= n; k++) list.push(this.derivedEntry(ship, officer.id, this.nthCraft(ship, ++crafts) || post));
				if (first !== null || !officer.mixesWith) break;
				first = officer;
				continue;
			}
			list.push(this.derivedEntry(ship, officer.id, post));
			if (ship.flight) break;
		}
		if (!ship.flight) list = list.concat(this.placedEntries(ship, list));
		return list;
	},

	derivedEntry: function derivedEntry(ship, id, post) {
		var entry = { id: id, post: post.id };
		if (this.isPostDestroyed(ship, post)) {
			entry.out = 0; //pre-battle damage: he never serves (Officers::serves)
			entry.how = 'disabled';
		}
		return entry;
	},

	//a flight's k-th craft (1 = the first), or null when it holds fewer - a lobby flight holds one
	nthCraft: function nthCraft(ship, k) {
		var crafts = this.systemsOf(ship).filter(function (s) { return Boolean(s); });
		return crafts[k - 1] || null;
	},

	//the limit his bought row carries (the offer's: Officers::maxCount), 1 when it says none
	rowLimit: function rowLimit(ship, id) {
		for (var i = 0; i < ship.enhancementOptions.length; i++) {
			var row = ship.enhancementOptions[i];
			if (row && row[0] === id) return Math.max(1, parseInt(row[3], 10) || 1);
		}
		return 1;
	},

	/* The most of this row the buy dialog lets a flight of `size` take: its limit, and for an officer bought as a count
	   (Stage 18) never more than the flight's craft ("not more than the flightSize" - user notes 2026-10-19) - less the
	   craft `taken` by the officers he shares a flight with (Stage 27, `mixesWith`: one officer to a pod). */
	rowMax: function rowMax(enhID, limit, size, taken) {
		var officer = this.get(enhID);
		var max = parseInt(limit, 10) || 0;
		if (officer && officer.multi) max = Math.min(max, Math.max(1, parseInt(size, 10) || 1) - (parseInt(taken, 10) || 0));
		return Math.max(0, max);
	},

	//Stage 27: may these two officers share one flight, one to a craft (`mixesWith`)?
	mixes: function mixes(enhID, otherID) {
		var officer = this.get(enhID);
		return Boolean(officer && officer.mixesWith && officer.mixesWith.indexOf(otherID) !== -1);
	},

	/* ------------------------------------------------------------------ placed officers (Stage 8) */

	isPlacedOfficer: function isPlacedOfficer(enhID) {
		var officer = this.get(enhID);
		return Boolean(officer && officer.placed);
	},

	//the bought per-system rows holding a placed officer: [enhID, label, count, limit, total, step, systemid, sysname]
	placedRows: function placedRows(ship) {
		var self = this;
		var rows = (ship && Array.isArray(ship.systemEnhancements)) ? ship.systemEnhancements : [];
		return rows.filter(function (row) {
			return Array.isArray(row) && (parseInt(row[2], 10) || 0) > 0 && self.isPlacedOfficer(row[0]);
		});
	},

	//⚠️ own lookup, not shipManager.systems.getSystem: a lobby flight's systems have holes (systemEnhancements.systemById)
	systemById: function systemById(ship, systemid) {
		var systems = this.systemsOf(ship);
		var id = parseInt(systemid, 10);
		for (var i = 0; i < systems.length; i++) {
			if (systems[i] && parseInt(systems[i].id, 10) === id) return systems[i];
		}
		return null;
	},

	/* How many of this placed officer one ship may carry: the 6th slot of his per-system offer, which the
	   server fills from Officers::placedLimit - so the size caps have no client copy. 1 without an offer. */
	placedLimit: function placedLimit(ship, enhID) {
		var offers = (ship && Array.isArray(ship.systemEnhancementOffers)) ? ship.systemEnhancementOffers : [];
		for (var i = 0; i < offers.length; i++) {
			if (offers[i][0] === enhID && offers[i][5] !== undefined) return parseInt(offers[i][5], 10) || 1;
		}
		return 1;
	},

	/* The lobby's derived track 2: one officer per bought row, posted in its system - after D13's name check,
	   with his ship-level self winning (sameAs), a system hosting ONE, and no officer past his ship cap
	   (registry order, then system id), a `perTurret` officer one to a turret (Stage 22). Pre-battle damage that
	   destroys his weapon refunds him (D11, the row is gone); damage that does not still disables the Matter
	   expert from the start (anyDamage).
	   ⚠️ MIRROR PAIR with Officers::pickPlacedPosts / ::keepPlacedRows (PHP). */
	placedEntries: function placedEntries(ship, shipLevel) {
		var rows = this.placedRows(ship);
		if (!rows.length) return [];
		var self = this;
		var aboard = {};
		(shipLevel || []).forEach(function (entry) { aboard[entry.id] = true; });
		var hosted = {}, count = {}, turrets = {}, out = [];
		this.REGISTRY.forEach(function (officer) {
			if (!officer.placed || (officer.sameAs && aboard[officer.sameAs])) return;
			rows.filter(function (row) { return row[0] === officer.id; })
				.map(function (row) { return { row: row, system: self.systemById(ship, row[6]) }; })
				.filter(function (c) { return c.system && (!c.row[7] || String(c.system.name) === String(c.row[7])); })
				.sort(function (a, b) { return parseInt(a.system.id, 10) - parseInt(b.system.id, 10); })
				.forEach(function (c) {
					var id = parseInt(c.system.id, 10);
					if (hosted[id] || (count[officer.id] || 0) >= self.placedLimit(ship, officer.id)) return;
					var turret = officer.perTurret ? officer.id + '|' + self.turretKey(c.system) : null;
					if (turret !== null && turrets[turret]) return;
					hosted[id] = true;
					count[officer.id] = (count[officer.id] || 0) + 1;
					if (turret !== null) turrets[turret] = true;
					var entry = { id: officer.id, post: c.system.id };
					if (officer.anyDamage ? self.isPostDamaged(ship, c.system) : self.isPostDestroyed(ship, c.system)) {
						entry.out = 0;
						entry.how = 'disabled';
					}
					out.push(entry);
				});
		});
		return out;
	},

	//any pre-battle damage on this system - the lobby's only damage (battleDamage's payload, else the system's rows)
	isPostDamaged: function isPostDamaged(ship, system) {
		if (this.isPostDestroyed(ship, system)) return true;
		if (window.battleDamage && typeof battleDamage.getEntry === 'function') {
			var entry = battleDamage.getEntry(ship, battleDamage.KIND_SYSTEM, system.id);
			if (entry && ((parseInt(entry.d, 10) || 0) > 0 || entry.k)) return true;
		}
		return Array.isArray(system.damage) && system.damage.some(function (d) {
			return d && (Number(d.damage) || 0) > (Number(d.armour) || 0);
		});
	},

	/* Why this placed officer may not be bought on this system right now - a sentence for the menu row's
	   hover - or null. systemEnhancements.set() refuses while there is one; the server does not trust that
	   (Officers::pickPlacedPosts, Enhancements::sanitisePlacedOfficers). */
	placedBlock: function placedBlock(ship, systemid, enhID) {
		var officer = this.get(enhID);
		if (!ship || !officer || !officer.placed) return null;
		if (officer.sameAs && Array.isArray(ship.enhancementOptions) && this.isBought(ship, officer.sameAs)) {
			return 'The ' + this.label(officer.sameAs) + ' is posted at the C&C (bought in the Officers section). A ship carries him in one place: use Move in the C&C\'s menu to post him here.';
		}
		var self = this;
		var id = parseInt(systemid, 10);
		var elsewhere = [];
		var reason = null;
		this.placedRows(ship).forEach(function (row) {
			var rowSystem = parseInt(row[6], 10);
			if (rowSystem === id && row[0] !== enhID) reason = 'This weapon already hosts the ' + self.label(row[0]) + ': a weapon hosts one officer.';
			if (rowSystem !== id && row[0] === enhID) elsewhere.push(row);
		});
		if (reason) return reason;
		//Stage 22: one Expert Turret Officer to a turret - a turret mate of this weapon may already hold him
		var system = officer.perTurret ? this.systemById(ship, id) : null;
		var mate = system ? elsewhere.filter(function (row) { return self.turretKey(self.systemById(ship, row[6])) === self.turretKey(system); })[0] : null;
		if (mate) {
			var held = this.systemById(ship, mate[6]);
			return 'This turret already has the ' + this.label(enhID) + ' (on ' + (held ? (held.displayName || held.name) : mate[7]) + ' #' + mate[6]
				+ '): he aids every weapon on his turret, one to a turret.';
		}
		var cap = this.placedLimit(ship, enhID);
		if (elsewhere.length >= cap) {
			var where = elsewhere.map(function (row) {
				var s = self.systemById(ship, row[6]);
				return (s ? (s.displayName || s.name) : row[7]) + ' #' + row[6];
			}).join(', ');
			return 'This ship may carry ' + cap + ' ' + this.label(enhID) + (cap === 1 ? '' : 's') + ' (already on ' + where + ')'
				+ (enhID === 'OFF_GUN' ? ' - 1 on a medium ship or smaller, 2 on a heavy, 3 on a capital, 4 on an enormous unit.' : '.');
		}
		return null;
	},

	/* The buy dialog's half: why a ship-level officer may not be bought because he is already placed in a
	   weapon (sameAs) - or null. confirm.js refuses the row and shows this in the Officers section. */
	shipLevelBlock: function shipLevelBlock(ship, enhID) {
		var self = this;
		var placed = this.REGISTRY.filter(function (o) { return o.placed && o.sameAs === enhID; });
		for (var i = 0; i < placed.length; i++) {
			var rows = this.placedRows(ship).filter(function (row) { return row[0] === placed[i].id; });
			if (!rows.length) continue;
			var s = this.systemById(ship, rows[0][6]);
			return 'The ' + self.label(enhID) + ' is placed in ' + (s ? (s.displayName || s.name) : rows[0][7]) + ' #' + rows[0][6]
				+ '. A ship carries him in one place: use Move in that system\'s menu (in the ship window) to post him back at the C&C.';
		}
		return null;
	},

	//true when the list is the lobby's own derivation rather than something the server sent
	isDerived: function isDerived(ship) {
		return Boolean(ship) && !Array.isArray(ship.officers);
	},

	isBought: function isBought(ship, id) {
		return this.boughtCount(ship, id) > 0;
	},

	//the count on his bought row (0 when none)
	boughtCount: function boughtCount(ship, id) {
		for (var i = 0; i < ship.enhancementOptions.length; i++) {
			var row = ship.enhancementOptions[i];
			var n = row ? (parseInt(row[2], 10) || 0) : 0;
			if (row && row[0] === id && n > 0) return n;
		}
		return 0;
	},

	isPostDestroyed: function isPostDestroyed(ship, post) {
		if (window.shipManager && shipManager.systems && typeof shipManager.systems.isDestroyed === 'function') {
			return Boolean(shipManager.systems.isDestroyed(ship, post));
		}
		return Boolean(post.destroyed);
	},

	//the officers posted on one system
	onPost: function onPost(ship, systemId) {
		return this.listFor(ship).filter(function (entry) { return String(entry.post) === String(systemId); });
	},

	/* Out on turn N = serves N, gone from N + 1; out at turn 0 (pre-battle damage) never serves. Nor on the turn
	   he moves to another post (`moving`, sent for the current turn only - Stage 10).
	   ⚠️ MIRROR PAIR with Officers::serves (PHP). */
	serves: function serves(entry, turn) {
		if (entry.moving !== undefined && entry.moving !== null) return false;
		if (entry.out === undefined || entry.out === null) return true;
		var out = parseInt(entry.out, 10);
		return out >= 1 && out >= (parseInt(turn, 10) || 0);
	},

	/* Out of action IN BATTLE before `turn` - disabled or killed (not pre-battle damage, not a fighter officer gone with his
	   fighter): an Expert Religious Leader's frenzy (Stage 14). ⚠️ MIRROR PAIR with Officers::isLost (PHP). */
	isLost: function isLost(entry, turn) {
		if (entry.out === undefined || entry.out === null) return false;
		var out = parseInt(entry.out, 10);
		return out >= 1 && out < (parseInt(turn, 10) || 0) && (entry.how === 'disabled' || entry.how === 'killed');
	},

	currentTurn: function currentTurn() {
		return (window.gamedata && gamedata.turn !== undefined) ? gamedata.turn : 0;
	},

	/* 'serving' | 'moving' | 'disabled' | 'killed' - what the insignia shows for one post: the BEST state of
	   the officers posted there, so any serving officer keeps it gold (D11). 'moving' (Stage 10): an officer
	   leaving this post this turn - ordered now, or committed - and the post one is moving TO. null with
	   nobody posted or arriving. */
	postState: function postState(ship, systemId) {
		var entries = this.onPost(ship, systemId);
		var turn = this.currentTurn();
		var state = null;
		for (var i = 0; i < entries.length; i++) {
			if (this.movingTo(ship, entries[i]) !== null) { state = 'moving'; continue; }
			if (this.serves(entries[i], turn)) return 'serving';
			if (state === null || state === 'killed') state = (entries[i].how === 'killed') ? 'killed' : 'disabled';
		}
		if (state !== 'moving' && this.arrivingAt(ship, systemId).length > 0) return 'moving';
		return state;
	},

	/* "(disabled, turn 3)", "(killed, turn 3)", the pre-battle wording, or '' while he serves - unless a
	   serving Expert Scavenger has spent his once-per-game save: "(save used, turn 3)" (`used`, own team only),
	   or an Expert Gunner has spent any of his three (`abilities`, Stage 10). */
	stateText: function stateText(entry) {
		if (entry.out === undefined || entry.out === null) {
			if (entry.used) return '(save used, turn ' + entry.used + ')';
			return this.abilitiesText(entry);
		}
		if (parseInt(entry.out, 10) < 1) {
			var officer = this.get(entry.id);
			//the weapon-mounted Matter Weapons Expert is out on ANY damage (anyDamage), not only a destroyed post
			return officer && officer.anyDamage ? '(his weapon is damaged before battle - he will not serve)'
				: '(post destroyed before battle - he will not serve)';
		}
		var lost = '(' + (entry.how === 'killed' ? 'killed' : 'disabled') + ', turn ' + entry.out + ')';
		//Stage 30: an officer whose abilities are the ship's stock (`stock`) may have one spent after he is lost
		var stock = this.get(entry.id) && this.get(entry.id).stock ? this.abilitiesText(entry) : '';
		return stock ? lost + ' ' + stock : lost;
	},

	//"(used: called shot, turn 3; Piercing shot, turn 4)" - the Gunner's spent once-per-battle abilities, or ''
	//capture: the Breaching Expert's (Stage 24); speech: the Expert Political Officer's (Stage 25); reuse: the Expert Software Engineer's (Stage 26)
	//charge, precision, anticipation: the Expert Warrior's (Stage 28); patch: the Expert Quartermaster's (Stage 30)
	ABILITY_NAMES: { reload: 'faster reload', pierce: 'Piercing shot', called: 'called shot', capture: 'capture assault', speech: 'speech',
		reuse: 'Specialist reuse', charge: 'charge', precision: 'precision strike', anticipation: 'Supreme Anticipation', patch: 'critical patch' },
	abilitiesText: function abilitiesText(entry) {
		var spent = entry && entry.abilities ? entry.abilities : null;
		if (!spent) return '';
		var self = this;
		var parts = Object.keys(this.ABILITY_NAMES).filter(function (key) { return spent[key] !== undefined && spent[key] !== null; })
			.map(function (key) { return self.ABILITY_NAMES[key] + ', turn ' + spent[key]; });
		return parts.length ? '(used: ' + parts.join('; ') + ')' : '';
	},

	/* "Expert Helmsman (disabled, turn 3)" - the insignia's title, one officer per line; Stage 10 adds an
	   officer moving off this post and one moving onto it */
	describePost: function describePost(ship, systemId) {
		var self = this;
		var lines = this.onPost(ship, systemId).map(function (entry) {
			var move = self.moveText(ship, entry);
			var state = move || self.stateText(entry);
			return self.label(entry.id) + (state ? ' ' + state : '');
		});
		this.arrivingAt(ship, systemId).forEach(function (arrival) {
			lines.push(self.label(arrival.entry.id) + ' ' + self.arrivalText(ship, arrival.entry));
		});
		return this.countLines(lines).join('\n');
	},

	/* Identical lines once each, "Expert Pilot (3)" for three (Stage 18: a lobby flight's pilots share its one card), in the
	   order they first appear - the fleet list's own "(n)" for placed officers. */
	countLines: function countLines(lines) {
		var order = [];
		var counts = {};
		lines.forEach(function (line) {
			if (!counts[line]) { counts[line] = 0; order.push(line); }
			counts[line]++;
		});
		return order.map(function (line) { return counts[line] > 1 ? line + ' (' + counts[line] + ')' : line; });
	},

	/* ------------------------------------------------------------------ the Expert Gunner's shot (Stage 10) */

	/* What his once-per-battle shot adds to THIS shot's hit chance, in d20 units: a Piercing shot while he still
	   has his Piercing - +4 on a weapon that may already pierce ("no penalty where it is"), nothing in the mode
	   added for him (`mode`, which carries the penalty); otherwise +3 on a called shot that is not Piercing, while
	   he still has it. Spent ON this turn still counts (the turn's own shot spent it). Never while he has been ordered
	   to move this turn (pendingMoveFireControl's reason). Asked for the SHOOTER, so always an own-team ship with its
	   list. ⚠️ MIRROR PAIR with Officers::gunnerShotBonus (PHP). */
	GUNNER_CALLED_BONUS: 3,
	PIERCE_PENALTY: 4,
	gunnerShotBonus: function gunnerShotBonus(ship, weapon, called) {
		if (!ship || !weapon || ship.flight) return 0;
		var turn = this.currentTurn();
		var self = this;
		var entry = this.listFor(ship).filter(function (e) {
			return e.id === 'OFF_GUN' && String(e.post) === String(weapon.id) && self.serves(e, turn) && self.transferOf(ship, e) === null;
		})[0];
		if (!entry) return 0;
		var open = function (ability) {
			var spent = entry.abilities ? entry.abilities[ability] : undefined;
			return spent === undefined || spent === null || (parseInt(spent, 10) || 0) >= turn;
		};
		if (weapon.damageType === 'Piercing') {
			if (!open('pierce')) return 0;
			return (entry.mode !== undefined && parseInt(weapon.firingMode, 10) === parseInt(entry.mode, 10)) ? 0 : this.PIERCE_PENALTY;
		}
		//Stage 19: on a gun a serving Expert Targeter covers, his +4 is the called shot's - the Gunner's +3 neither used nor spent
		//Stage 28: likewise on the gun making an Expert Warrior's precision strike
		return (this.bonusCall(weapon, called) && open('called') && !this.targeterCovering(ship, weapon) && !this.precisionWeapon(ship, weapon, called))
			? this.GUNNER_CALLED_BONUS : 0;
	},

	/* Is this a called shot an officer's called-shot bonus reaches (the Gunner's +3, the Targeter's +4)? Never in a mode whose hit calculation
	   is its own (hasFixedFireControl - the Antimatter Shredder's Shredder mode, whose attacks at fighters are called shots).
	   ⚠️ MIRROR PAIR with Officers::bonusCall (PHP). */
	bonusCall: function bonusCall(weapon, called) {
		return Boolean(called) && !this.hasFixedFireControl(weapon, weapon ? weapon.firingMode : null);
	},

	/* ------------------------------------------------------------------ moving officers (Stage 10) */

	/* In Initial Orders the player may move an officer marked `movable` to another post. The server sends, with each
	   such officer of the owner's, `targets` - every post he may move to this turn (Officers::mayMoveTo; occupied
	   posts, wrecks and posts he could not be bought for are already out) - and `moving` once a move is committed.
	   An order made here is kept on the ship (ship.officerTransfers, {id, from, to}) until the commit carries it
	   (ajaxInterface.construcGamedata -> transferClaims); the server checks it again before writing it. */

	//the movable officers posted here who may be ordered to move now (serving, not already moving, somewhere to go)
	movableOn: function movableOn(ship, systemId) {
		if (!ship || this.isDerived(ship)) return [];
		var self = this;
		var turn = this.currentTurn();
		return this.onPost(ship, systemId).filter(function (entry) {
			var officer = self.get(entry.id);
			return officer && officer.movable && self.serves(entry, turn) && Array.isArray(entry.targets) && entry.targets.length > 0;
		});
	},

	/* The Officers menu (SystemInfoButtons): Initial Orders, the player's own ship, and an officer here who may move
	   - or one already ordered to (so the order can be cancelled). */
	canManage: function canManage(ship, system) {
		if (!ship || !system || !window.gamedata || gamedata.gamephase !== 1 || gamedata.waiting || gamedata.replay) return false;
		if (typeof gamedata.isMyShip === 'function' && !gamedata.isMyShip(ship)) return false;
		return this.movableOn(ship, system.id).length > 0;
	},

	//his pending (uncommitted) order, or null
	transferOf: function transferOf(ship, entry) {
		var orders = (ship && Array.isArray(ship.officerTransfers)) ? ship.officerTransfers : [];
		for (var i = 0; i < orders.length; i++) {
			if (orders[i].id === entry.id && String(orders[i].from) === String(entry.post)) return orders[i];
		}
		return null;
	},

	//the post he is moving to this turn - committed (the server's `moving`) or ordered now - or null
	movingTo: function movingTo(ship, entry) {
		if (entry.moving !== undefined && entry.moving !== null) return parseInt(entry.moving, 10);
		var order = this.transferOf(ship, entry);
		return order ? parseInt(order.to, 10) : null;
	},

	//the officers moving onto this post this turn, as [{entry}]
	arrivingAt: function arrivingAt(ship, systemId) {
		var self = this;
		return this.listFor(ship).filter(function (entry) {
			return String(self.movingTo(ship, entry)) === String(systemId) && String(entry.post) !== String(systemId);
		}).map(function (entry) { return { entry: entry }; });
	},

	/* where he may be moved: the server's `targets`, less posts another officer has been ordered to this turn. In Fleet
	   Selection (Stage 15) the lobby's own rule (lobbyDestinations), worked out once when the pick starts. */
	destinations: function destinations(ship, entry) {
		if (this.isDerived(ship)) {
			var pick = this.picking;
			if (pick && pick.ship === ship && pick.id === entry.id && String(pick.from) === String(entry.post) && pick.dests) return pick.dests;
			return this.lobbyDestinations(ship, entry);
		}
		if (!Array.isArray(entry.targets)) return [];
		var self = this;
		var taken = this.listFor(ship).filter(function (other) { return other !== entry; })
			.map(function (other) { return self.movingTo(ship, other); })
			.filter(function (to) { return to !== null; }).map(String);
		return entry.targets.filter(function (id) { return taken.indexOf(String(id)) === -1; });
	},

	systemName: function systemName(ship, systemId) {
		var s = this.systemById(ship, systemId);
		return (s ? this.postName(s) : 'system') + ' #' + systemId;
	},

	/* A system's name for an officer line: its display name - or, for a Structure block (Stage 19, the Expert Targeter's post),
	   its section's, "Aft Structure", the ship window's own section names (ShipSection SECTION_NAMES). */
	SECTION_NAMES: { 0: 'Primary', 1: 'Forward', 2: 'Aft', 3: 'Port', 4: 'Starboard', 31: 'Port Fwd', 32: 'Port Aft', 41: 'Stbd Fwd', 42: 'Stbd Aft' },
	postName: function postName(system) {
		if (system && system.name === 'structure' && this.SECTION_NAMES[system.location] !== undefined) {
			return this.SECTION_NAMES[system.location] + ' Structure';
		}
		return system ? (system.displayName || system.name) : 'system';
	},

	//"(moving to Heavy Laser #12 - serves there from turn 5)", or '' while he is not moving
	moveText: function moveText(ship, entry) {
		var to = this.movingTo(ship, entry);
		if (to === null) return '';
		var pending = (entry.moving === undefined || entry.moving === null);
		var from = ' from turn ' + (this.currentTurn() + 1) + ')';
		if (String(to) === String(entry.post)) { //Stage 19: a side switch is a move to his own post
			return '(' + (pending ? 'ordered to switch to ' : 'switching to ') + this.sideName(ship, this.get(entry.id).switchTo) + ' - covers it' + from;
		}
		return '(' + (pending ? 'ordered to ' : 'moving to ') + this.systemName(ship, to) + ' - serves there' + from;
	},

	/* ------------------------------------------------------------------ the Expert Targeter (Stage 19) */

	/* His effect line, with the weapons he covers: "... - Aft weapons" (his block's section), or a Medium ship's side - "Front
	   weapons". What those weapons are is the server's (`covers`). Stage 25: an Expert Political Officer's in the lobby says what he has
	   brought so far (politicalStatus). Every other officer: his registry summary. */
	summaryOf: function summaryOf(ship, entry) {
		var officer = this.get(entry.id);
		if (!officer) return '';
		if (officer.id === 'OFF_POL' && this.isDerived(ship)) return officer.summary + '. ' + this.politicalStatus(ship);
		if (officer.id === 'OFF_QM' && this.isDerived(ship)) return officer.summary + '. ' + this.quartermasterStatus(ship); //Stage 30
		if ((officer.sameAs || officer.id) !== 'OFF_TARG') return officer.summary;
		return officer.summary + ' - ' + this.coverName(ship, entry) + ' weapons';
	},

	coverName: function coverName(ship, entry) {
		if (!this.hasOuterBlock(ship)) return this.sideName(ship, entry.id);
		var post = this.systemById(ship, entry.post);
		return (post && this.SECTION_NAMES[post.location] !== undefined) ? this.SECTION_NAMES[post.location] : 'his block\'s';
	},

	//a Structure block outside the Primary section - a hull with none (a Medium ship) posts him in its Primary Structure, by side
	hasOuterBlock: function hasOuterBlock(ship) {
		return this.systemsOf(ship).some(function (s) { return s && s.name === 'structure' && (parseInt(s.location, 10) || 0) !== 0; });
	},

	/* "Front" / "Aft", or "Left" / "Right" on a LeftRight hull: the side `id` covers - the hull's hit chart's non-Primary locations in
	   order, his `side` picking the first or the second (Officers::hullSides, PHP). */
	SIDE_NAMES: { 1: 'Front', 2: 'Aft', 3: 'Left', 4: 'Right' },
	sideName: function sideName(ship, id) {
		var officer = this.get(id);
		var sides = Object.keys((ship && ship.hitChart) || {}).map(function (k) { return parseInt(k, 10); })
			.filter(function (k) { return k !== 0 && !isNaN(k); }).sort(function (a, b) { return a - b; });
		var side = sides[((officer && officer.side) || 1) - 1];
		return this.SIDE_NAMES[side] || 'his side\'s';
	},

	/* The serving Targeter covering this weapon - not one ordered to move or switch this turn (he serves nowhere then, as the server
	   will rule once the order is sent) - or null. In game only: `covers` comes with the server's list. */
	targeterCovering: function targeterCovering(ship, weapon) {
		var turn = this.currentTurn();
		var self = this;
		return this.listFor(ship).filter(function (e) {
			var officer = self.get(e.id);
			return officer && (officer.sameAs || officer.id) === 'OFF_TARG' && self.serves(e, turn) && self.transferOf(ship, e) === null
				&& Array.isArray(e.covers) && e.covers.map(String).indexOf(String(weapon.id)) !== -1;
		})[0] || null;
	},

	/* What he adds to THIS shot's hit chance, in d20 units: +4 on a standard called shot (one that pays the -8) by a weapon he covers;
	   nothing on any other shot. Asked for the SHOOTER, so always an own-team ship with its list.
	   ⚠️ MIRROR PAIR with Officers::targeterShotBonus (PHP). */
	TARGETER_CALLED_BONUS: 4,
	targeterShotBonus: function targeterShotBonus(ship, weapon, called) {
		if (!this.bonusCall(weapon, called) || !ship || !weapon || ship.flight) return 0;
		return this.targeterCovering(ship, weapon) ? this.TARGETER_CALLED_BONUS : 0;
	},

	/* The side selector, in place of Move, for a Targeter who may switch sides - in game, one whose `targets` hold his own post
	   (Officers::mayMoveTo); in Fleet Selection, one whose other side this block offers. [{id, name, current}], or null. */
	sideChoices: function sideChoices(ship, entry) {
		var officer = entry ? this.get(entry.id) : null;
		if (!ship || !officer || !officer.switchTo) return null;
		var may = this.isDerived(ship)
			? Boolean(window.systemEnhancements) && Boolean(systemEnhancements.offerFor(ship, entry.post, officer.switchTo))
			: Array.isArray(entry.targets) && entry.targets.map(String).indexOf(String(entry.post)) !== -1;
		if (!may) return null;
		var mine = { id: entry.id, name: this.sideName(ship, entry.id), current: true };
		var other = { id: officer.switchTo, name: this.sideName(ship, officer.switchTo), current: false };
		return (officer.side || 1) === 1 ? [mine, other] : [other, mine];
	},

	/* Fleet Selection: switch him to his other side now, free - his row traded for the other side's on the same block, both at his
	   price (Officers::rowPrice), and put back if that is refused. true when he switched. */
	lobbySwitchSide: function lobbySwitchSide(ship, entry) {
		var officer = entry ? this.get(entry.id) : null;
		if (!officer || !officer.switchTo || !window.systemEnhancements || !this.sideChoices(ship, entry)) return false;
		var sysBefore = parseFloat(ship.pointCostSysEnh) || 0;
		systemEnhancements.set(ship, entry.post, entry.id, 0);
		var landed = systemEnhancements.set(ship, entry.post, officer.switchTo, 1) === 1;
		if (!landed) systemEnhancements.set(ship, entry.post, entry.id, 1);
		ship.pointCost = (parseFloat(ship.pointCost) || 0) + ((parseFloat(ship.pointCostSysEnh) || 0) - sysBefore);
		systemEnhancements.apply(ship);
		this.afterLobbyChange(ship);
		return landed;
	},

	/* Has the viewer an officer to read about on this system (posted, or arriving this turn)? The Structure bar's officers-only
	   tooltip (ShipSection) shows only then - a block has nothing else to say. */
	hasPostTooltip: function hasPostTooltip(ship, system) {
		return Boolean(ship && system) && (this.onPost(ship, system.id).length > 0 || this.arrivingAt(ship, system.id).length > 0);
	},

	//"(arriving from Medium Laser #8 - serves here from turn 5)"
	arrivalText: function arrivalText(ship, entry) {
		return '(arriving from ' + this.systemName(ship, entry.post) + ' - serves here from turn ' + (this.currentTurn() + 1) + ')';
	},

	/* The pick: "Move Officer" arms it, and the next click on one of that ship's systems ends it - on one of his
	   destinations it orders the move, anywhere else it cancels (SystemIcon.clickSystem asks pickTarget first).
	   While armed, his destinations are outlined (isPickTarget). */
	picking: null,

	startPick: function startPick(ship, entry) {
		this.picking = { ship: ship, id: entry.id, from: entry.post };
		if (this.isDerived(ship)) this.picking.dests = this.lobbyDestinations(ship, entry); //Fleet Selection: nothing changes while he is picked
		this.notice('Click a highlighted system to move the ' + this.label(entry.id) + ' there - anything else cancels.');
		this.repaint(ship);
	},

	pickingEntry: function pickingEntry(ship) {
		var pick = this.picking;
		if (!pick || pick.ship !== ship) return null;
		return this.onPost(ship, pick.from).filter(function (entry) { return entry.id === pick.id; })[0] || null;
	},

	isPickTarget: function isPickTarget(ship, system) {
		var entry = system ? this.pickingEntry(ship) : null;
		return Boolean(entry) && this.destinations(ship, entry).map(String).indexOf(String(system.id)) !== -1;
	},

	//true when the click was the pick's (and so nothing else should happen)
	pickTarget: function pickTarget(ship, system) {
		if (!this.picking) return false;
		var entry = this.pickingEntry(ship);
		if (!entry) { this.cancelPick(); return false; } //another ship's system: the pick ends, the click goes on
		if (this.isPickTarget(ship, system)) {
			this.picking = null;
			if (this.isDerived(ship)) {
				this.lobbyMove(ship, entry, system.id); //Fleet Selection (Stage 15): moved at once, free
			} else {
				this.setTransfer(ship, entry, system.id);
				this.notice(this.label(entry.id) + ' ordered to ' + this.systemName(ship, system.id) + ': he serves nowhere for the rest of this turn.');
			}
		} else {
			this.notice('Move cancelled - ' + this.systemName(ship, system.id) + ' is not a post the ' + this.label(entry.id) + ' can move to.');
		}
		this.picking = null;
		this.repaint(ship, system);
		return true;
	},

	cancelPick: function cancelPick() {
		var ship = this.picking ? this.picking.ship : null;
		this.picking = null;
		if (ship) this.repaint(ship);
	},

	setTransfer: function setTransfer(ship, entry, to) {
		var others = (Array.isArray(ship.officerTransfers) ? ship.officerTransfers : []).filter(function (order) {
			return !(order.id === entry.id && String(order.from) === String(entry.post));
		});
		others.push({ id: entry.id, from: parseInt(entry.post, 10), to: parseInt(to, 10) });
		ship.officerTransfers = others;
		this.repaint(ship);
	},

	cancelTransfer: function cancelTransfer(ship, entry) {
		if (!Array.isArray(ship.officerTransfers)) return;
		ship.officerTransfers = ship.officerTransfers.filter(function (order) {
			return !(order.id === entry.id && String(order.from) === String(entry.post));
		});
		this.repaint(ship);
	},

	//what the Initial Orders commit sends (construcGamedata): the orders still standing - each to one of his destinations
	transferClaims: function transferClaims(ship) {
		if (!ship || !Array.isArray(ship.officerTransfers) || !window.gamedata || gamedata.gamephase !== 1) return [];
		var self = this;
		return ship.officerTransfers.filter(function (order) {
			var entry = self.onPost(ship, order.from).filter(function (e) { return e.id === order.id; })[0];
			return entry && Array.isArray(entry.targets) && entry.targets.map(String).indexOf(String(order.to)) !== -1;
		}).map(function (order) { return { id: order.id, from: order.from, to: order.to }; });
	},

	repaint: function repaint(ship, system) {
		if (window.webglScene && typeof webglScene.customEvent === 'function') webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
		if (window.shipWindowManagerReact && typeof shipWindowManagerReact.update === 'function') shipWindowManagerReact.update();
	},

	//a passive notice (feedback_passive_notices): game.php's own, when it is there
	notice: function notice(text) {
		if (window.savedOrders && typeof savedOrders.showNotice === 'function') savedOrders.showNotice(text);
	},

	/* ------------------------------------------------------------------ moving officers in Fleet Selection (Stage 15) */

	/* User request 2026-10-17: in Fleet Selection a movable officer may be moved to any post he could be bought for, free
	   (user ruling) - nothing is lost there, unlike a move in game. The same pick as in game (startPick; SystemIcon outlines
	   and hands over the click), started by the Move button of the system's menu (ApplyDamageMenu), but the move is made at
	   once, by rewriting the purchase rows: a placed officer's per-system row moves to the new system; the C&C officer
	   (OFF_RELI, OFF_MWE) and his placed twin (sameAs) trade his ship-level row for a per-system row, and back. The server
	   needs nothing new: it re-checks every row at buy, save and reload (Enhancements::sanitiseSystemEnhancements,
	   Officers::pickPosts / pickPlacedPosts). ⚠️ Mirrors Officers::postAs from the lobby's own data: a post he could be
	   BOUGHT on. */

	//the officers he is, at one post or another (himself and any sameAs twin) - movable ones only
	moveFamily: function moveFamily(id) {
		var officer = this.get(id);
		var kind = officer ? (officer.sameAs || officer.id) : null;
		return this.REGISTRY.filter(function (o) { return o.movable && (o.sameAs || o.id) === kind; });
	},

	//a ship-level purchase row [id, label, count, limit, price, step, option], or null when the hull does not offer him
	shipLevelRow: function shipLevelRow(ship, id) {
		var rows = (ship && Array.isArray(ship.enhancementOptions)) ? ship.enhancementOptions : [];
		for (var i = 0; i < rows.length; i++) {
			if (rows[i] && rows[i][0] === id) return rows[i];
		}
		return null;
	},

	/* The officer he would be at `system` in Fleet Selection, or null. Never his own post or a destroyed system, nor one
	   another officer who holds a post (a placed or movable one) is on - one who never moves, such as a Helmsman, simply
	   shares it. A placed officer where that system offers him and his ship cap allows (himself not counted); a ship-level
	   one at his own post, on a hull offering him. ⚠️ MIRROR of Officers::postAs + mayMoveTo's occupancy rule (PHP). */
	lobbyPostAs: function lobbyPostAs(ship, entry, system) {
		if (!ship || !entry || !system || String(system.id) === String(entry.post) || this.isPostDestroyed(ship, system)) return null;
		var self = this;
		var held = this.listFor(ship).some(function (other) {
			if (String(other.post) !== String(system.id)) return false;
			var o = self.get(other.id);
			return Boolean(o && (o.placed || o.movable));
		});
		if (held) return null;
		var family = this.moveFamily(entry.id);
		for (var i = 0; i < family.length; i++) {
			var candidate = family[i];
			if (candidate.placed) {
				if (!window.systemEnhancements || !systemEnhancements.offerFor(ship, system.id, candidate.id)) continue;
				var others = this.placedRows(ship).filter(function (row) {
					return row[0] === candidate.id && !(row[0] === entry.id && String(row[6]) === String(entry.post));
				});
				if (others.length >= this.placedLimit(ship, candidate.id)) continue;
				//Stage 22: nor onto a turret another Expert Turret Officer holds (between the guns of his own turret he may move)
				if (candidate.perTurret && others.some(function (row) { return self.turretKey(self.systemById(ship, row[6])) === self.turretKey(system); })) continue;
				return candidate.id;
			}
			var post = this.findPost(ship, candidate.post);
			if (post && String(post.id) === String(system.id) && this.passesNeeds(ship, candidate) && this.shipLevelRow(ship, candidate.id)) return candidate.id;
		}
		return null;
	},

	//every system he could be moved to in Fleet Selection (lobbyPostAs), as ids - [] for an officer who does not move
	lobbyDestinations: function lobbyDestinations(ship, entry) {
		var officer = entry ? this.get(entry.id) : null;
		if (!ship || !officer || !officer.movable || !this.isDerived(ship)) return [];
		var self = this;
		return this.systemsOf(ship).filter(function (s) { return s && self.lobbyPostAs(ship, entry, s) !== null; })
			.map(function (s) { return parseInt(s.id, 10); });
	},

	//the officer posted on this system under this id in the lobby's own list, or null
	lobbyEntryAt: function lobbyEntryAt(ship, systemId, id) {
		return this.onPost(ship, systemId).filter(function (entry) { return entry.id === id; })[0] || null;
	},

	/* Buy (n 1) or un-buy (n 0) a ship-level officer's row from outside the buy dialog, moving pointCostEnh and pointCost by
	   the same amount - the lobby's invariant pointCost = hull + pointCostEnh + pointCostEnh2 + pointCostSysEnh. An Edit
	   later rebuilds pointCostEnh from the rows' counts (gamelobby doEditShip), so it agrees with what is written here.
	   Stage 25: the expert an Expert Political Officer brings costs nothing (politicalDiscount, before and after), and buying or
	   un-buying the Political Officer himself re-prices his free Hardened Armour - the refit bucket, pointCost with it.
	   Stage 29: buying or un-buying an Expert Ballistic Officer re-prices the ammunition rounds he makes free (ballisticDiscount).
	   Stage 30: an Expert Quartermaster's - his free reactor or accelerator and rounds (quartermasterDiscount), and his free refit.
	   false when the hull does not offer him. */
	setShipLevelRow: function setShipLevelRow(ship, id, n) {
		var row = this.shipLevelRow(ship, id);
		if (!row) return false;
		var discount = this.politicalDiscount(ship) + this.ballisticDiscount(ship) + this.quartermasterDiscount(ship);
		var delta = (n - (parseInt(row[2], 10) || 0)) * (parseFloat(row[4]) || 0);
		row[2] = n;
		delta -= this.politicalDiscount(ship) + this.ballisticDiscount(ship) + this.quartermasterDiscount(ship) - discount;
		ship.pointCostEnh = (parseFloat(ship.pointCostEnh) || 0) + delta;
		ship.pointCost = (parseFloat(ship.pointCost) || 0) + delta;
		//Stage 30: an Expert Quartermaster coming or going re-prices his free refit, as the Political Officer does his armour
		if ((id === 'OFF_POL' || id === 'OFF_QM') && window.systemEnhancements) {
			var refitsBefore = parseFloat(ship.pointCostSysEnh) || 0;
			systemEnhancements.totalCost(ship);
			ship.pointCost += (parseFloat(ship.pointCostSysEnh) || 0) - refitsBefore;
		}
		return true;
	},

	/* Move him to `toId` now (Fleet Selection): off his post first - a placed twin is refused while his ship-level self is
	   still bought (placedBlock) - then onto the new one; if that is refused he is put back exactly as he was. Free: both
	   rows cost his own price (Officers::rowPrice), and pointCost follows the refit bucket's change as
	   ApplyDamageMenu.settleRefitCost does. true when he moved. */
	lobbyMove: function lobbyMove(ship, entry, toId) {
		var as = this.lobbyPostAs(ship, entry, this.systemById(ship, toId));
		if (!as || !window.systemEnhancements) return false;
		var self = this;
		var sysBefore = parseFloat(ship.pointCostSysEnh) || 0;
		var restore;
		if (this.get(entry.id).placed) {
			systemEnhancements.set(ship, entry.post, entry.id, 0);
			restore = function () { systemEnhancements.set(ship, entry.post, entry.id, 1); };
		} else {
			this.setShipLevelRow(ship, entry.id, 0);
			restore = function () { self.setShipLevelRow(ship, entry.id, 1); };
		}
		var landed = this.get(as).placed ? systemEnhancements.set(ship, toId, as, 1) === 1 : this.setShipLevelRow(ship, as, 1);
		if (!landed) restore();
		ship.pointCost = (parseFloat(ship.pointCost) || 0) + ((parseFloat(ship.pointCostSysEnh) || 0) - sysBefore);
		systemEnhancements.apply(ship);
		this.afterLobbyChange(ship);
		return landed;
	},

	//the fleet list, the points panel and every open ship window, after a lobby write
	afterLobbyChange: function afterLobbyChange(ship) {
		if (window.gamedata && typeof gamedata.refreshFleetRow === 'function') gamedata.refreshFleetRow(ship);
		if (window.gamedata && typeof gamedata.calculateFleet === 'function') gamedata.calculateFleet();
		this.repaint(ship);
	},

	/* ⭐ USER RULING 2026-10-17 (Stage 15): any officer whose post pre-battle damage destroys is REMOVED AND REFUNDED - one
	   rule for both tracks, "for simplicity" (it was disabled-with-a-warning for a ship-level officer, QX5). A placed
	   officer's row goes with every refit on a destroyed system (systemEnhancements.dropDestroyed, D11); this is the
	   ship-level half: each bought ship officer whose post (findPost) is destroyed loses his row, and pointCostEnh and
	   pointCost give back what he cost. One-way, as D11: repairing the post does not bring him back. Flights are not swept:
	   pre-battle damage never destroys a fighter (a lost fighter is a smaller flight). Returns dropDestroyed's shape, for
	   systemEnhancements.describeRemoved. Stage 25: what each one gave back is what the unit's points moved by - an Expert Political
	   Officer's free expert and free armour priced again (setShipLevelRow). */
	dropDestroyedPosts: function dropDestroyedPosts(ship) {
		if (!ship || ship.flight || !ship.systems || !Array.isArray(ship.enhancementOptions)) return [];
		var self = this;
		var removed = [];
		this.REGISTRY.forEach(function (officer) {
			if (officer.placed || officer.flight) return;
			var row = self.shipLevelRow(ship, officer.id);
			var count = row ? (parseInt(row[2], 10) || 0) : 0;
			if (count < 1) return;
			var post = self.findPost(ship, officer.post);
			if (!post || !self.isPostDestroyed(ship, post)) return;
			var pointsBefore = parseFloat(ship.pointCost) || 0;
			self.setShipLevelRow(ship, officer.id, 0);
			//Stage 27: an Expert Troop Leader refunded takes the Bulkheads' box lobbyEnhancements gave them
			if (officer.id === 'OFF_TROOP' && ship.troopEnh) { self.troopBulkheads(ship, -1); ship.troopEnh = false; }
			var points = pointsBefore - (parseFloat(ship.pointCost) || 0);
			removed.push({ enhID: officer.id, label: officer.label, points: points, systemid: parseInt(post.id, 10),
				systemName: post.displayName || post.name });
		});
		return removed;
	},

	/* The lobby menu's Officers rows (systemEnhancements.menuRowsFor builds them, SystemEnhancementsSection renders them): a
	   bought movable officer's row carries `moveTo`, the posts he could take (the footer's Move button); and a ship-level
	   movable officer posted HERE - the C&C's Religious Leader or Matter Weapons Expert, bought in the Officers section - gets
	   a row of his own, `fixed`: the same shape, its ticker inert (he is bought and removed in the buy dialog), so the Move
	   button has someone to move. Fleet Selection only. */
	decorateMenuRows: function decorateMenuRows(ship, system, rows) {
		if (!ship || !system || ship.flight || !this.isDerived(ship)) return rows;
		var self = this;
		var entries = this.onPost(ship, system.id);
		rows.forEach(function (row) { self.decorateArmourRow(ship, system, row); }); //Stage 25: an Expert Political Officer's free armour
		rows.forEach(function (row) { self.decorateQuartermasterRow(ship, system, row); }); //Stage 30: an Expert Quartermaster's free refit (after: his prices are the whole sum)
		/* Stage 19: a Medium ship's block is offered the Expert Targeter twice, once per side (OFF_TARG, OFF_TARG2) - one officer,
		   so one row: the side bought, or his first side while neither is. The footer's side selector trades one for the other. */
		rows = rows.filter(function (row) {
			var officer = row.officer ? self.get(row.enhID) : null;
			if (!officer || !officer.side || row.count > 0) return true;
			var twinBought = rows.some(function (other) { return other.enhID === officer.switchTo && other.count > 0; });
			return !twinBought && officer.side === 1;
		});
		rows.forEach(function (row) {
			var officer = row.officer && row.count > 0 ? self.get(row.enhID) : null;
			var entry = officer && officer.movable ? entries.filter(function (e) { return e.id === row.enhID; })[0] : null;
			if (!entry) return;
			//a Targeter on a single-block hull has no other post: his side, if this block offers him the other one, is the choice
			if (officer.side && !self.hasOuterBlock(ship)) row.sides = self.sideChoices(ship, entry);
			else row.moveTo = self.lobbyDestinations(ship, entry);
		});
		entries.forEach(function (entry) {
			var officer = self.get(entry.id);
			if (!officer || officer.placed || !officer.movable) return;
			var shipRow = self.shipLevelRow(ship, entry.id);
			var price = shipRow ? (parseFloat(shipRow[4]) || 0) : 0;
			rows.push({ enhID: entry.id, label: officer.label, count: 1, max: 1, systemid: parseInt(system.id, 10), price: price,
				nextPrice: price, note: '', officer: true, fixed: true, blocked: null, moveTo: self.lobbyDestinations(ship, entry) });
		});
		return rows;
	},

	//Is this officer aboard and serving now? ⚠️ MIRROR PAIR with Officers::isActive (PHP).
	isActive: function isActive(ship, id) {
		var list = this.listFor(ship);
		for (var i = 0; i < list.length; i++) {
			if (list[i].id === id) return this.serves(list[i], this.currentTurn());
		}
		return false;
	},

	/* How many of this officer serve now - an officer bought as a count (Stage 18) is one entry per craft, and isActive answers
	   for the first only. ⚠️ MIRROR PAIR with Officers::servingCount (PHP). */
	servingCount: function servingCount(ship, id) {
		var turn = this.currentTurn();
		var self = this;
		return this.listFor(ship).filter(function (entry) { return entry.id === id && self.serves(entry, turn); }).length;
	},

	/* Expert Pilot: the jinking levels this flight may take at no thrust and beyond its jinking limit - one per serving pilot
	   ("The bonus level does not count against jinking limits for that fighter class"). CLIENT ONLY: movement.php validates no
	   jink, so shipManager.movement.canJink / doJink are the rule; never through ship.jinkinglimit, which the lobby and the
	   hangars read as the fighter's weight class. Asked for the moving flight, always an own-team one with its list. */
	PILOT_FREE_JINK: 1,
	freeJinks: function freeJinks(ship) {
		return (ship && ship.flight) ? this.servingCount(ship, 'OFF_PILOT') * this.PILOT_FREE_JINK : 0;
	},

	/* Expert Scanner: the EW points he adds to the late-EW allowance, on top of the EW Detector ladder
	   (ew.getDetectorAllowance). In game the list is the server's, so an enemy ship answers 0.
	   ⚠️ MIRROR PAIR with Officers::savedEwBonus (PHP). */
	savedEwBonus: function savedEwBonus(ship) {
		return this.isActive(ship, 'OFF_SCAN') ? 1 : 0;
	},

	/* Expert Missileer: the OB a fighter's ballistic launch gets, when it gets the flight's OB at all, is 1
	   higher. Asked for the SHOOTER, so it is always an own-team flight with its list.
	   ⚠️ MIRROR PAIR with Officers::missileObBonus (PHP), read in weapon.php's fighter-ballistic branch. */
	missileObBonus: function missileObBonus(ship) {
		return (ship && ship.flight && this.isActive(ship, 'OFF_MSL')) ? 1 : 0;
	},

	/* Expert Coordinator, thrust: a combat pivot costs half its normal thrust, rounded up - 1 instead of 2 for a
	   fighter, 2 instead of 3 for a shuttle. CLIENT ONLY: movement.php recomputes no thrust cost, so the cost the
	   row carries (shipManager.movement.getCombatPivotCost) is the cost. Asked for the moving flight, always an
	   own-team one with its list. */
	combatPivotCost: function combatPivotCost(ship, cost) {
		return (ship && ship.flight && this.isActive(ship, 'OFF_COOR')) ? Math.ceil(cost / 2) : cost;
	},

	/* Expert Coordinator, to hit: no combat pivot penalty unless this turn's pivots turned the flight a full 180
	   degrees (shipManager.movement.getCombatPivotRotation, in hex sides). Asked for the SHOOTER.
	   ⚠️ MIRROR PAIR with Officers::waivesCombatPivotPenalty (PHP), read in weapon.php's fighter branch. */
	COMBAT_PIVOT_FULL: 3,
	waivesCombatPivotPenalty: function waivesCombatPivotPenalty(ship) {
		if (!(ship && ship.flight && this.isActive(ship, 'OFF_COOR'))) return false;
		return shipManager.movement.getCombatPivotRotation(ship) < this.COMBAT_PIVOT_FULL;
	},

	/* Expert Tailgunner: does he serve this flight - read by weaponManager.getTailGunMod (the hit chance) and tailGunLine. Asked for
	   the SHOOTER, always an own-team flight with its list. ⚠️ MIRROR PAIR with Officers::hasTailgunner (PHP). */
	TAILGUNNER_BONUS: 1,
	hasTailgunner: function hasTailgunner(ship) {
		return !!(ship && ship.flight && this.isActive(ship, 'OFF_TAIL'));
	},

	/* A tail gun's "Tail Gun" tooltip line, as getTailGunMod would price it: the server writes the blueprint's (penalty, or a
	   navigator's "No penalty"), and this keeps it right for a Navigator bought on the flight and for an Expert Tailgunner -
	   whose part only his own team sees, as his list is theirs alone. */
	tailGunLine: function tailGunLine(ship, value) {
		if (!ship || !ship.flight) return value;
		var penalised = !ship.hasNavigator;
		if (this.hasTailgunner(ship)) return penalised ? 'No penalty (Expert Tailgunner)' : '+' + (this.TAILGUNNER_BONUS * 5) + '% (Expert Tailgunner)';
		return penalised ? value : 'No penalty (navigator)';
	},

	/* ------------------------------------------------------------------ Breaching Expert (Stage 24) */

	/* A pod's Marines' "Elite" tooltip line: the server writes the hull's ("Yes" for the Narn's and Gaim's own Elite Marines, or bought ones -
	   lobbyEnhancements writes those in the lobby), and this adds a serving Breaching Expert in the craft that carries them - for his own team
	   only, as his list is theirs alone. Marines Elite already keep their "Yes": he adds nothing there (user ruling 2026-10-21).
	   ⚠️ MIRROR PAIR with Officers::eliteMarines (PHP). */
	marinesEliteLine: function marinesEliteLine(ship, system, value) {
		if (!ship || !ship.flight) return value;
		var craft = this.craftHolding(ship, system);
		if (!craft) return value;
		var turn = this.currentTurn();
		var self = this;
		var line = value;
		if (value !== 'Yes' && this.onPost(ship, craft.id).some(function (entry) { return entry.id === 'OFF_BRCH' && self.serves(entry, turn); })) {
			line = 'Yes (Breaching Expert)';
		}
		//Stage 27: an Expert Troop Leader's -3 in this pod, or his ship's -1 (troopBonus) - to his team only, as his list is theirs alone
		var troop = this.troopBonus(ship, craft, turn);
		if (troop === -this.TROOP_POD_BONUS) return line + '; Expert Troop Leader: ' + troop + ' on its rolls';
		if (troop) return line + '; a Troop Leader\'s ship: ' + troop + ' on its rolls';
		return line;
	},

	//the craft of a flight whose systems include `system` (by id), or null
	craftHolding: function craftHolding(ship, system) {
		var self = this;
		return this.systemsOf(ship).filter(function (craft) {
			return craft && self.systemsOf(craft).some(function (s) { return s && String(s.id) === String(system.id); });
		})[0] || null;
	},

	/* "No more than 20% of a fleets breaching pods (round fractions of 0.5 or more up) can use these experts" - the Fleet Correctness
	   Report's line (gamelobby.js checkChoices; user ruling 2026-10-21: reported, as every fleet-composition rule is, not enforced). Pods
	   are counted CRAFT by craft (a flight's size) over every breaching pod in `ships`; experts are the Breaching Experts that would serve
	   (listFor - since Stage 27 one to a pod). With pods in flights of two at most, a fleet needs three pods for its first expert. */
	BREACH_FLEET_PCT: 20,
	breachingFleetCheck: function breachingFleetCheck(ships) {
		var pods = 0;
		var experts = 0;
		var self = this;
		(ships || []).forEach(function (ship) {
			if (!self.isBreachingPod(ship)) return;
			pods += parseInt(ship.flightSize, 10) || 1;
			experts += self.listFor(ship).filter(function (entry) { return entry.id === 'OFF_BRCH'; }).length;
		});
		//pods x 20% rounded half up, in whole numbers: floor((pods x 20 + 50) / 100)
		return { pods: pods, experts: experts, allowed: Math.floor((pods * this.BREACH_FLEET_PCT + 50) / 100) };
	},

	/* ------------------------------------------------------------------ Expert Troop Leader (Stage 27) */

	TROOP_POD_BONUS: 3,      //in his pod: -3 on every roll of its parties - Officers::TROOP_POD_BONUS (PHP)
	TROOP_SHIP_POD_BONUS: 1, //aboard a ship: -1 on every roll of "his own ship's" pods - Officers::TROOP_SHIP_POD_BONUS (PHP)
	TROOP_BULKHEAD_BOXES: 1, //every Bulkhead of his ship - Officers::TROOP_BULKHEAD_BOXES (PHP)

	/* A ship's POD ALLOWANCE - the breaching pods it may carry as the Fleet Correctness Report counts them (gamelobby.js checkChoices, its
	   per-ship profile): its "Breaching Pods" racks and HANG_BP's conversions, plus 1 - 2 on a capital, 4 on a base or an enormous unit,
	   doubled on an Assault hull - held to its assault shuttle, heavy (or "normal") and medium slots. Off the hull's own slots: the fleet
	   check rewrites `fighters` with the racks (and keeps the original as _originalFighters). In game the server sends his entry's
	   (`pods`). ⚠️ MIRROR PAIR with Officers::podAllowance (PHP). */
	podAllowance: function podAllowance(ship) {
		if (!ship || ship.flight) return 0;
		var fighters = ship._originalFighters || ship.fighters || {};
		var racks = (parseInt(fighters['Breaching Pods'], 10) || 0) + (Array.isArray(ship.enhancementOptions) ? this.boughtCount(ship, 'HANG_BP') : 0);
		var slots = (parseInt(fighters.heavy || fighters.normal, 10) || 0) + (parseInt(fighters.medium, 10) || 0) + (parseInt(fighters['assault shuttles'], 10) || 0);
		var cap = 1;
		if (ship.Enormous || ship.base || ship.smallBase) cap = 4;
		else if ((parseInt(ship.shipSizeClass, 10) || 0) >= 3) cap = 2;
		if (String(ship.shipClass || '').toLowerCase().indexOf('assault') !== -1) cap *= 2;
		return racks + Math.min(cap, slots);
	},

	/* A unit's player's units in FLEET order - the order the fleet was bought in: in game by unit id (the server inserts them in order), in
	   the lobby as Fleet Selection lists them (gamedata.ships' own order; its ids are strings). */
	fleetOf: function fleetOf(unit) {
		var all = (window.gamedata && gamedata.ships) ? Object.keys(gamedata.ships).map(function (k) { return gamedata.ships[k]; }) : [];
		var mine = all.filter(function (u) {
			return u && String(u.userid) === String(unit.userid) && (u.slot === undefined || unit.slot === undefined || String(u.slot) === String(unit.slot));
		});
		if (mine.every(function (u) { return /^\d+$/.test(String(u.id)); })) mine.sort(function (a, b) { return Number(a.id) - Number(b.id); });
		return mine;
	},

	//a flight's crafts: every craft in game, the one card of a lobby flight standing for flightSize
	podCraftCount: function podCraftCount(flight) {
		return Math.max(this.systemsOf(flight).filter(Boolean).length, parseInt(flight.flightSize, 10) || 1);
	},

	/* "If any of his own ship's breaching pods attack an opponent, they gain a +1 bonus on any attack table": as many of his player's
	   breaching pods as his ship's Pod Allowance, in fleet order, craft by craft - fixed, a lost pod's share passing to nobody (user rulings
	   2026-10-23); several Troop Leader ships of one player add up. Own team only (his list is theirs alone). ⚠️ MIRROR PAIR with
	   Officers::troopAidedCraft (PHP). */
	troopAidedCraft: function troopAidedCraft(flight, craft, turn) {
		var self = this;
		var fleet = this.fleetOf(flight);
		var allowance = 0;
		fleet.forEach(function (unit) {
			if (unit.flight) return;
			self.listFor(unit).forEach(function (entry) {
				if (entry.id !== 'OFF_TROOP' || !self.serves(entry, turn)) return;
				allowance += (entry.pods !== undefined) ? (parseInt(entry.pods, 10) || 0) : self.podAllowance(unit);
			});
		});
		if (allowance < 1) return false;
		var index = this.systemsOf(flight).filter(Boolean).indexOf(craft) + 1;
		var n = 0;
		var pods = fleet.filter(function (unit) { return self.isBreachingPod(unit); });
		for (var i = 0; i < pods.length; i++) {
			for (var k = 1; k <= this.podCraftCount(pods[i]); k++) {
				if (++n > allowance) return false;
				if (String(pods[i].id) === String(flight.id) && k === index) return true; //by id: a lobby window may hold a copy
			}
		}
		return false;
	},

	/* What his officers give a party from this pod's craft on its d10s: -3 while an Expert Troop Leader serves in it (his ship's 1 among
	   it - user ruling), else -1 while a Troop Leader ship counts it among its pods, else 0. ⚠️ MIRROR PAIR with Officers::troopBonus (PHP). */
	troopBonus: function troopBonus(flight, craft, turn) {
		var self = this;
		if (this.onPost(flight, craft.id).some(function (entry) { return entry.id === 'OFF_TRPOD' && self.serves(entry, turn); })) return -this.TROOP_POD_BONUS;
		return this.troopAidedCraft(flight, craft, turn) ? -this.TROOP_SHIP_POD_BONUS : 0;
	},

	/* The lobby's preview of "all bulkheads on the ship are considered to have 1 extra box": lobbyEnhancements.apply adds it once per build
	   (its maxhealth is read in too many places for a read-time preview), and dropDestroyedPosts takes it back with a refunded officer. In
	   game the server sends each Bulkhead's boxes. `sign` +1 or -1. */
	troopBulkheads: function troopBulkheads(ship, sign) {
		var boxes = sign * this.TROOP_BULKHEAD_BOXES;
		this.systemsOf(ship).forEach(function (s) {
			if (s && s.name === 'Bulkhead') s.maxhealth = (parseInt(s.maxhealth, 10) || 0) + boxes;
		});
	},

	/* Expert Evader (user request 2026-10-20): may this flight order an Evade - the Firing-phase movement order
	   (movement.js canEvade / doEvade) without which he does nothing. Own team only, through his list.
	   ⚠️ MIRROR PAIR with Officers::mayEvade (PHP). */
	canEvade: function canEvade(ship) {
		return !!(ship && ship.flight && this.isActive(ship, 'OFF_EVAD'));
	},

	/* ...and his -1 OB while the Evade row is still the client's own (id -1): once it has been sent, the server's
	   load applies it (Officers::applyEvader) and the row comes back with its real id. Read by displayStat and the
	   hit chance (weaponManager.computeOEW). A row that Cancel takes away takes the -1 with it. */
	pendingEvadeOb: function pendingEvadeOb(ship) {
		if (!this.canEvade(ship) || !window.gamedata) return 0;
		for (var i in ship.movement) {
			var move = ship.movement[i];
			if (move && move.type === 'evade' && move.turn == gamedata.turn && move.id == -1) return 1;
		}
		return 0;
	},

	/* Expert Anticipator (Stage 23): may this SHIP make its one Firing-phase turn (movement.js canAnticipate / canTurn) - never with
	   a unit attached to it, which only the Movement phase moves with its host. Own team only, through his list.
	   ⚠️ MIRROR PAIR with Officers::mayAnticipate (PHP). */
	canAnticipate: function canAnticipate(ship) {
		if (!ship || ship.flight) return false;
		if (ship.hasAttached && Object.keys(ship.hasAttached).length > 0) return false;
		return this.isActive(ship, 'OFF_ANTI');
	},

	/* ------------------------------------------------------------------ Expert Political Officer (Stage 25) */

	/* The four experts one of whom he brings at no cost, in registry order - which a tie for the dearest goes by.
	   ⚠️ MIRROR PAIR with Officers::$politicalExperts (PHP). */
	POLITICAL_EXPERTS: ['OFF_HELM', 'OFF_SCAN', 'OFF_ENG', 'OFF_NAV'],
	POLITICAL_ARMOUR_SYSTEMS: 5, //⚠️ MIRROR of Officers::POLITICAL_ARMOUR_SYSTEMS (PHP)

	/* Which of the four costs nothing: with a Political Officer bought, the DEAREST of them bought - the first on a tie; null with no
	   Political Officer or none of them. `rows`: [{id, count, price}] - a ship's own rows (shipRows), or a buy dialog's spinners
	   (confirm.isFreeExpert). ⚠️ MIRROR PAIR with Officers::freeExpertId (PHP). */
	freeExpertId: function freeExpertId(rows) {
		var bought = function (id) {
			return rows.filter(function (row) { return row.id === id && row.count > 0; })[0] || null;
		};
		if (!bought('OFF_POL')) return null;
		var best = null;
		var bestPrice = -1;
		this.POLITICAL_EXPERTS.forEach(function (id) {
			var row = bought(id);
			if (row && row.price > bestPrice) {
				best = id;
				bestPrice = row.price;
			}
		});
		return best;
	},

	//a ship's rows as freeExpertId and (Stage 29, with each level's price step) ballisticFreeRounds read them
	shipRows: function shipRows(ship) {
		var rows = (ship && Array.isArray(ship.enhancementOptions)) ? ship.enhancementOptions : [];
		return rows.filter(function (row) { return row && typeof row[0] === 'string'; }).map(function (row) {
			return { id: row[0], count: parseInt(row[2], 10) || 0, price: parseFloat(row[4]) || 0, step: parseFloat(row[5]) || 0 };
		});
	},

	//what the free expert would have cost this ship - the points it is spared (0 without one)
	politicalDiscount: function politicalDiscount(ship) {
		var rows = this.shipRows(ship);
		var free = this.freeExpertId(rows);
		return free ? rows.filter(function (row) { return row.id === free; })[0].price : 0;
	},

	/* One bought ship-level row's points for the fleet list (gamelobby enhancementLines): 0 for the free expert, else `cost` - less, on an
	   ammunition row, the rounds an Expert Ballistic Officer makes free (Stage 29, ballisticFreeRounds). Stage 30: 0 for an Expert
	   Quartermaster's free Improved Reactor or Jump Accelerator, and less his free rounds on an ammunition row (quartermasterFreeRounds). */
	shipRowCost: function shipRowCost(ship, entry, cost) {
		var rows = this.shipRows(ship);
		if (entry && entry[0] === this.freeExpertId(rows)) return 0;
		if (entry && this.QM_SHIP_REFITS.indexOf(entry[0]) !== -1) {
			var refit = this.quartermasterRefit(ship, rows);
			if (refit && refit.option === entry[0]) return 0;
		}
		var free = entry ? (this.ballisticFreeRounds(rows, ship && ship.ballisticPicks).saved[entry[0]] || 0)
			+ (this.quartermasterFreeRounds(rows, ship && ship.quartermasterPicks, ship && ship.ballisticPicks).saved[entry[0]] || 0) : 0;
		return free ? cost - free : cost;
	},

	/* ⭐ Which Hardened Armour is free, as the indexes of ship.systemEnhancements: with a Political Officer bought and his C&C standing,
	   the POLITICAL_ARMOUR_SYSTEMS dearest SYS_HARM rows that are not on Structure, a tie to the lower system id. Priced by the offer
	   (the blueprint, D10). ⚠️ MIRROR PAIR with Officers::freeArmourRows (PHP). */
	freeArmourRows: function freeArmourRows(ship) {
		var free = {};
		if (!this.armourFreeOpen(ship)) return free;
		this.armourCandidates(ship).slice(0, this.POLITICAL_ARMOUR_SYSTEMS).forEach(function (c) { free[c.index] = true; });
		return free;
	},

	/* his armour's candidates - every bought Hardened Armour not on Structure, dearest first ({index, price, id}) - but one an Expert
	   Quartermaster's free refit already spares (Stage 30: his comes first, as on the server - Officers::priceFreeRefits) */
	armourCandidates: function armourCandidates(ship) {
		var list = [];
		var spared = this.quartermasterSpared(ship);
		systemEnhancements.rows(ship).forEach(function (row, i) {
			if (!row || row[0] !== 'SYS_HARM' || (parseInt(row[2], 10) || 0) < 1 || spared[i] !== undefined) return;
			var system = systemEnhancements.systemById(ship, row[6]);
			if (!system || system.name === 'structure') return;
			var offer = systemEnhancements.offerFor(ship, row[6], 'SYS_HARM');
			list.push({ index: i, price: systemEnhancements.priceFor(offer, row[2]), id: parseInt(row[6], 10) });
		});
		return list.sort(function (a, b) { return (b.price - a.price) || (a.id - b.id); });
	},

	/* The free refits priced again - an Expert Quartermaster's free refit (its levels spared taken off), then a Political Officer's free
	   Hardened Armour among the rest (0), every other row of those kinds its offer's price - so a row that WAS free and is no longer (a dearer
	   reactor bought since) is paid again. systemEnhancements.totalCost runs it before it sums the bucket, so every write prices them as the
	   server's sanitiser will (Officers::priceFreeRefits). A row no longer offered keeps what it was priced at. */
	priceFreeRefits: function priceFreeRefits(ship) {
		if (!ship || !window.systemEnhancements || !Array.isArray(ship.systemEnhancements)) return;
		var self = this;
		var spared = this.quartermasterSpared(ship);
		var free = this.freeArmourRows(ship);
		ship.systemEnhancements.forEach(function (row, i) {
			if (!row || self.QM_REFITS.indexOf(row[0]) === -1) return; //his four per-system kinds - Hardened Armour among them
			var offer = systemEnhancements.offerFor(ship, row[6], row[0]);
			if (offer) row[4] = free[i] ? 0 : systemEnhancements.priceFor(offer, row[2]) - (spared[i] || 0);
		});
	},

	/* What buying a Hardened Armour on `system` would add to the ship (the system menu's price): its offer's price, less what his five
	   free ones absorb - free while fewer than five are placed, and past that the cheapest of them goes back to full price. */
	armourNextCost: function armourNextCost(ship, system, price) {
		if (!this.armourFreeOpen(ship) || !system || system.name === 'structure') return price;
		var max = this.POLITICAL_ARMOUR_SYSTEMS;
		var top = function (prices) {
			return prices.slice().sort(function (a, b) { return b - a; }).slice(0, max)
				.reduce(function (sum, p) { return sum + p; }, 0);
		};
		var prices = this.armourCandidates(ship).map(function (c) { return c.price; });
		return price - (top(prices.concat([price])) - top(prices));
	},

	//does this ship have a Political Officer whose free armour applies (bought, his C&C standing)?
	armourFreeOpen: function armourFreeOpen(ship) {
		if (!ship || ship.flight || !window.systemEnhancements || !Array.isArray(ship.enhancementOptions) || !this.isBought(ship, 'OFF_POL')) return false;
		var cnc = this.findPost(ship, 'cnc');
		return Boolean(cnc) && !this.isPostDestroyed(ship, cnc);
	},

	/* The lobby's line on what he has brought so far - his SystemInfo summary (summaryOf): "Free expert: Expert Navigator. Free
	   Hardened Armour: 3 of 5 placed - the rest go on random systems when the fleet is committed." */
	politicalStatus: function politicalStatus(ship) {
		var free = this.freeExpertId(this.shipRows(ship));
		var placed = this.armourFreeOpen(ship) ? Object.keys(this.freeArmourRows(ship)).length : 0;
		var text = 'Free expert: ' + (free ? this.label(free) : 'none bought - one is picked at random when the fleet is committed') + '. ';
		text += 'Free Hardened Armour: ' + placed + ' of ' + this.POLITICAL_ARMOUR_SYSTEMS + ' placed';
		return text + (placed < this.POLITICAL_ARMOUR_SYSTEMS ? ' - the rest go on random systems when the fleet is committed.' : '.');
	},

	/* The system menu's Hardened Armour row on his ship (decorateMenuRows): what it costs now (the price it is charged - 0 when free),
	   what buying it would add (armourNextCost), and a note saying so. */
	decorateArmourRow: function decorateArmourRow(ship, system, row) {
		if (row.enhID !== 'SYS_HARM' || !this.armourFreeOpen(ship)) return;
		if (system.name === 'structure') {
			row.note = 'The Expert Political Officer\'s free Hardened Armour cannot go on Structure.';
			return;
		}
		var bought = systemEnhancements.rowFor(ship, system.id, 'SYS_HARM');
		if (bought) row.price = parseFloat(bought[4]) || 0;
		else row.nextPrice = this.armourNextCost(ship, system, row.nextPrice);
		var placed = Object.keys(this.freeArmourRows(ship)).length;
		row.note = 'Expert Political Officer: ' + this.POLITICAL_ARMOUR_SYSTEMS + ' Hardened Armour free (the dearest, not on Structure) - '
			+ placed + ' placed; any left are placed at random when the fleet is committed.';
	},

	/* May this ship's Expert Political Officer give his speech now, from `system` - the Speech! button of the C&C he is posted in
	   (CnC.canActivate; a hull with two C&Cs shows it on his alone): the Firing phase, his own team's ship, he serves and has not spoken
	   yet (`abilities`, the server's). Through his list, so never an enemy's. ⚠️ MIRROR PAIR with Officers::speakerIndex (PHP). */
	canSpeak: function canSpeak(ship, system) {
		if (!ship || ship.flight || !system || !window.gamedata || gamedata.gamephase != 3) return false;
		if (typeof gamedata.isMyShip === 'function' && !gamedata.isMyShip(ship)) return false;
		var turn = this.currentTurn();
		var self = this;
		return this.listFor(ship).some(function (entry) {
			return entry.id === 'OFF_POL' && String(entry.post) === String(system.id) && self.serves(entry, turn)
				&& !(entry.abilities && entry.abilities.speech);
		});
	},

	/* ------------------------------------------------------------------ Expert Warrior (Stage 28) */

	CHARGE_HIT: 2,  //⚠️ MIRROR of Officers::CHARGE_HIT (PHP)
	CHARGE_MIN_HEXES: 4,  //⚠️ MIRROR of Officers::CHARGE_MIN_HEXES (PHP) - "at least 4 hexes towards the enemy"

	//this ship's Expert Warrior entry if he serves now, or null - through his list, so never an enemy's
	warriorEntry: function warriorEntry(ship) {
		if (!ship || ship.flight) return null;
		var turn = this.currentTurn();
		var self = this;
		return this.listFor(ship).filter(function (entry) { return entry.id === 'OFF_WARR' && self.serves(entry, turn); })[0] || null;
	},

	//Is this C&C an Expert Warrior's post? Its Activate box is then his Supreme Anticipation's - blue, with his labels (CnC, model/system/baseSystems.js)
	isWarriorPost: function isWarriorPost(ship, system) {
		if (!ship || !system) return false;
		return this.listFor(ship).some(function (entry) { return entry.id === 'OFF_WARR' && String(entry.post) === String(system.id); });
	},

	/* What his charge adds to the hit chance of one of his ship's shots, in d20 units: +2 at the unit it holds against this turn (`charged`,
	   judged by the server), with every weapon but the ram. His standing +1 is fire control (fireControlRaise; in game the server's).
	   ⚠️ MIRROR PAIR with Officers::chargeHitBonus (PHP). */
	chargeHitBonus: function chargeHitBonus(ship, weapon, target) {
		var entry = this.warriorEntry(ship);
		if (!entry || !target || !weapon || weapon.isRammingAttack) return 0;
		return (entry.charged !== undefined && entry.charged !== null && String(entry.charged) === String(target.id)) ? this.CHARGE_HIT : 0;
	},

	/* May his precision strike still be made this turn: not spent on an earlier turn, and no other of his abilities this turn - not a charge
	   (its movement row), not his Supreme Anticipation (spent, or ordered now on his C&C's Activate box). */
	precisionOpen: function precisionOpen(ship) {
		var entry = this.warriorEntry(ship);
		if (!entry) return false;
		var turn = this.currentTurn();
		var spent = entry.abilities || {};
		var on = function (key) { return spent[key] !== undefined && spent[key] !== null ? (parseInt(spent[key], 10) || 0) : null; };
		if (on('precision') !== null && on('precision') < turn) return false;
		if (on('anticipation') === turn) return false;
		var cnc = this.systemById(ship, entry.post);
		if (cnc && cnc.active && window.gamedata && gamedata.gamephase == 3) return false;
		return !(window.shipManager && shipManager.movement && typeof shipManager.movement.getChargeRow === 'function'
			&& shipManager.movement.getChargeRow(ship));
	},

	/* Is this weapon's called shot his precision strike - "a single 'called shot', with only one weapon": his ship's first called shot, so of this
	   turn's the one by his lowest-numbered weapon (the server prices them in that order). `called`: the shot asked about is a standard called
	   shot. ⚠️ MIRROR PAIR with Officers::precisionShot (PHP). */
	precisionWeapon: function precisionWeapon(ship, weapon, called) {
		if (!ship || !weapon || ship.flight || weapon.isRammingAttack || !this.bonusCall(weapon, called) || !this.precisionOpen(ship)) return false;
		var mine = parseInt(weapon.id, 10);
		var turn = this.currentTurn();
		var self = this;
		return !this.systemsOf(ship).some(function (other) {
			if (!other || !other.weapon || other.isRammingAttack || parseInt(other.id, 10) >= mine) return false;
			return (other.fireOrders || []).some(function (order) {
				if (!order || order.turn != turn || !(parseInt(order.calledid, 10) > 0) || String(order.type).indexOf('ntercept') !== -1) return false;
				if (self.hasFixedFireControl(other, order.firingMode)) return false;
				var target = window.gamedata && typeof gamedata.getShip === 'function' ? gamedata.getShip(order.targetid) : null;
				var calledSystem = (target && window.shipManager) ? shipManager.systems.getSystem(target, order.calledid) : null;
				return Boolean(calledSystem) && calledSystem.targetProfile == null;
			});
		});
	},

	//...and what it adds to that shot: the called shot penalty taken off again, doubled against a base as the penalty is (Officers::precisionStrike)
	precisionStrike: function precisionStrike(ship, weapon, target, called) {
		if (!this.precisionWeapon(ship, weapon, called)) return 0;
		var waived = -(Number(weapon.calledShotMod) || 0);
		return (target && target.base) ? 2 * waived : waived;
	},

	/* May this ship declare a charge now (movement.js canCharge, its Charge icon): his own team's, he serves, and he has not charged on an earlier
	   turn. Through his list, so never an enemy's. ⚠️ MIRROR PAIR with Officers::validateCharge's test (PHP). */
	canCharge: function canCharge(ship) {
		var entry = this.warriorEntry(ship);
		if (!entry || (window.gamedata && typeof gamedata.isMyShip === 'function' && !gamedata.isMyShip(ship))) return false;
		var spent = entry.abilities ? entry.abilities.charge : undefined;
		return spent === undefined || spent === null || (parseInt(spent, 10) || 0) >= this.currentTurn();
	},

	/* May he order his Supreme Anticipation now, from `system` - the Activate box of the C&C he is posted in: the Firing Phase, his own team's ship,
	   he serves, it is unspent, and his ship did not charge this turn ("cannot do more than one of them in a given turn").
	   ⚠️ MIRROR PAIR with Officers::anticipatorIndex (PHP). */
	canAnticipateSupreme: function canAnticipateSupreme(ship, system) {
		if (!system || !window.gamedata || gamedata.gamephase != 3) return false;
		if (typeof gamedata.isMyShip === 'function' && !gamedata.isMyShip(ship)) return false;
		var entry = this.warriorEntry(ship);
		if (!entry || String(entry.post) !== String(system.id)) return false;
		if (entry.abilities && entry.abilities.anticipation !== undefined && entry.abilities.anticipation !== null) return false;
		return !(window.shipManager && shipManager.movement && typeof shipManager.movement.getChargeRow === 'function'
			&& shipManager.movement.getChargeRow(ship));
	},

	/* ------------------------------------------------------------------ Expert Software Engineer (Stage 26) */

	//"The specialist must be of the software category" - the Hyach Specialists the user named. ⚠️ MIRROR PAIR with Officers::$softwareSpecialists (PHP).
	SOFTWARE_SPECIALISTS: ['Defence', 'Sensor', 'Targeting', 'Weapon'],
	SOFTWARE_BFCP: 1, //Officers::SOFTWARE_BFCP

	/* May the player hold one of `computer`'s points back to the Firing phase - its Expert Software Engineer serving in it? The Computer's
	   Firing-phase menu (HyachComputer.firingMode) and the Initial Orders commit's unassigned-BFCP warning (bfcpHeldBack) ask it. Through his
	   list, so never an enemy's. ⚠️ MIRROR PAIR with Officers::holdsFiringBFCP (PHP). */
	holdsFiringBFCP: function holdsFiringBFCP(ship, computer) {
		if (!ship || !computer) return false;
		var turn = this.currentTurn();
		var self = this;
		return this.listFor(ship).some(function (entry) {
			return entry.id === 'OFF_SOFT' && String(entry.post) === String(computer.id) && self.serves(entry, turn);
		});
	},

	//Is what Initial Orders leaves unassigned no more than the point he may hold to the Firing phase? Then the commit does not warn about it.
	bfcpHeldBack: function bfcpHeldBack(ship, computer) {
		if (!this.holdsFiringBFCP(ship, computer)) return false;
		return (Number(computer.output) || 0) - (Number(computer.BFCPtotal_used) || 0) <= this.SOFTWARE_BFCP;
	},

	/* In the lobby: his +1 BFCP on his Computer, 0 anywhere else - in game the server raised `output`. The tooltip's BFCP line shows it
	   (bfcpLine). ⚠️ MIRROR PAIR with Officers::applySoftwareEngineer (PHP). */
	computerBonus: function computerBonus(ship, system) {
		if (!system) return 0;
		var serving = this.servingDerived(ship);
		for (var i = 0; i < serving.length; i++) {
			if (serving[i].id === 'OFF_SOFT' && String(serving[i].post) === String(system.id)) return this.SOFTWARE_BFCP;
		}
		return 0;
	},

	//the Computer's "Bonus Fire Control Points (BFCP)" line, "used/total", with the lobby's preview of his point
	bfcpLine: function bfcpLine(ship, system, value) {
		var bonus = this.computerBonus(ship, system);
		var parts = String(value).split('/');
		if (!bonus || parts.length !== 2) return value;
		return parts[0] + '/' + ((parseFloat(parts[1]) || 0) + bonus);
	},

	/* The software Specialists his ship may use a second time now: the server's list (Officers::specialistReuse - each used once, on an
	   earlier turn; none once one has been used twice), sent to his own team while he serves. [] without him. */
	specialistReuseList: function specialistReuseList(ship) {
		var turn = this.currentTurn();
		var self = this;
		var entry = this.listFor(ship).filter(function (e) { return e.id === 'OFF_SOFT' && self.serves(e, turn); })[0];
		return (entry && Array.isArray(entry.reuse)) ? entry.reuse : [];
	},

	/* May `type` be used again this turn, through him: on the server's list, and no OTHER Specialist of that list already being used again
	   this turn ("This only works for one specialist per scenario")? HyachSpecialists canUse / canDecrease and the Specialists menu ask it. */
	specialistReusable: function specialistReusable(ship, specialists, type) {
		var list = this.specialistReuseList(ship);
		if (list.indexOf(type) === -1) return false;
		var used = (specialists && specialists.specAllocatedCount) ? specialists.specAllocatedCount : {};
		return !list.some(function (other) { return other !== type && used[other] > 0; });
	},

	/* "Officers (2)" - the one line the ship window's gold Enhancements block shows for them (user request
	   2026-10-11: a count, like "System Enhancements (n)", the detail living on each post's tooltip). Own
	   team only, through listFor; null with none aboard.
	   A FLIGHT in the LOBBY names him instead ("Expert Dogfighter"; user request 2026-10-11): a flight
	   carries one officer at most (D9), so a count says less than his name - or "Expert Pilot (3)" for
	   pilots bought as a count (Stage 18). In game it stays a count. */
	summaryLine: function summaryLine(ship) {
		var list = this.listFor(ship);
		if (list.length === 0) return null;
		if (ship.flight && window.gamedata && gamedata.gamephase === -2) {
			var same = list.filter(function (entry) { return entry.id === list[0].id; }).length;
			return this.label(list[0].id) + (same > 1 ? ' (' + same + ')' : '');
		}
		return 'Officers (' + list.length + ')';
	},

	/* ------------------------------------------------------------------ Expert Ballistic Officer (Stage 29) */

	BALLISTIC_RANGE: 5, //⚠️ MIRROR of Officers::BALLISTIC_RANGE (PHP) - the lobby's own figure; in game the server publishes it (distanceRangeBonus)
	BALLISTIC_EXTRA_ROUND: 1, //...BALLISTIC_EXTRA_ROUND - the lobby's preview of what the commit writes
	/* The magazine rounds that take two slots, never a free pick ("single-space (1 slot, e.g. not ammo like Starburst missiles which take 2
	   slots)"). ⚠️ MIRROR PAIR with the server's round sizes (Officers::paidPickTypes reads AmmoMissileK / AmmoMissileM's `size`). */
	BALLISTIC_TWO_SLOT: ['AMMO_K', 'AMMO_M'],

	/* A system his `needs` counts: a magazine-fed ballistic launcher (a missile rack, a Bomb Rack, a mine launcher - checkAmmoMagazine), or a
	   ballistic weapon keeping its own count of rounds (a Proximity Laser, a Limpet Bore). Never the jump drive. A launcher counts only where its
	   magazine has ordnance to buy (hasMagazineOrdnance - user ruling after play test 4512). ⚠️ MIRROR PAIR with Officers::hasCountedBallisticAmmo (PHP). */
	isCountedBallistic: function isCountedBallistic(system) {
		if (!system || !system.weapon || !system.ballistic || system.name === 'jumpEngine') return false;
		return system.checkAmmoMagazine === true || (parseInt(system.ammunition, 10) || 0) > 0;
	},

	//has this hull ordnance for its magazine - an ammunition offer in its blueprint? ⚠️ MIRROR PAIR with Officers::hasMagazineOrdnance (PHP)
	hasMagazineOrdnance: function hasMagazineOrdnance(ship) {
		return (ship && Array.isArray(ship.enhancementOptions) ? ship.enhancementOptions : []).some(function (row) {
			return row && typeof row[0] === 'string' && /^(AMMO_|MINE_|SHELL_)/.test(row[0]);
		});
	},

	/* An ordnance row's limit with him bought, in the buy dialog: the offer's own rule on the magazine his slots enlarge - "the per-type limit is
	   simply derived from the magazine total" (user ruling after play test 4512): as many rounds of its size as fit (Starburst and Multiwarhead
	   two slots), a tenth of them for Stealth and Homing missiles. A Solyrn's Heavy 280 -> 290, its Stealth 28 -> 29. `max` (the offer's) for
	   anything else. ⚠️ MIRROR PAIR with Enhancements::setEnhancementOptionsShip's ammunition limits - officersHarness writes
	   ballisticLimitsFixture.json from the server's own offers on an enlarged magazine, and the client harness compares every row. */
	BALLISTIC_TENTH: ['AMMO_S', 'AMMO_HM'],
	ballisticRowLimit: function ballisticRowLimit(ship, id, max) {
		var slots = ship ? (parseInt(ship.ballisticSlots, 10) || 0) : 0;
		if (!slots || typeof id !== 'string' || !/^(AMMO_|MINE_)/.test(id)) return max;
		var magazine = this.systemsOf(ship).filter(function (s) { return s && s.name === 'ammoMagazine'; })[0];
		if (!magazine) return max;
		var fit = Math.floor(((parseInt(magazine.capacity, 10) || 0) + slots) / (this.BALLISTIC_TWO_SLOT.indexOf(id) !== -1 ? 2 : 1));
		return this.BALLISTIC_TENTH.indexOf(id) !== -1 ? fit / 10 : fit;
	},

	/* A row whose rounds may be his free picks: an AMMO_ row with a price on this hull, never Basic, never a two-slot round (user ruling
	   2026-10-24). ⚠️ MIRROR PAIR with Officers::paidPickTypes (PHP), which the commit's fill uses. */
	isBallisticPick: function isBallisticPick(id, price) {
		return typeof id === 'string' && id.indexOf('AMMO_') === 0 && id !== 'AMMO_B' && this.BALLISTIC_TWO_SLOT.indexOf(id) === -1 && (Number(price) || 0) > 0;
	},

	/* ⭐ HIS FREE MISSILES - "any missiles the player buys are free up to the number of missile racks on the ship ... the most expensive
	   missile ammos are free" (the user's note): with him bought, the `picks` DEAREST rounds of the pickable rows (isBallisticPick) cost
	   nothing - each round at its own level's price (price + level x step, as the buy dialog charges it), a tie to the lower id (the dialog
	   and the fleet list hold their rows in different orders; only the split of a tie between rows depends on it, never the total).
	   `rows`: [{id, count, price, step}] - a ship's own (shipRows) or a buy dialog's spinners (confirm.dialogRows);
	   `picks`: the static blueprint's ballisticPicks. Returns {free: {id: rounds}, saved: {id: points}, discount: points}. The commit rolls
	   whatever is left unpicked (Officers::placeBallisticPicks), so a player who buys none still gets them. */
	ballisticFreeRounds: function ballisticFreeRounds(rows, picks) {
		var out = { free: {}, saved: {}, discount: 0 };
		picks = parseInt(picks, 10) || 0;
		if (picks <= 0 || !rows.some(function (row) { return row.id === 'OFF_BALL' && row.count > 0; })) return out;
		var self = this;
		var rounds = [];
		rows.forEach(function (row) {
			if (!self.isBallisticPick(row.id, row.price)) return;
			for (var level = 0; level < row.count; level++) rounds.push({ id: row.id, price: row.price + level * (row.step || 0) });
		});
		rounds.sort(function (a, b) { return (b.price - a.price) || (a.id < b.id ? -1 : (a.id > b.id ? 1 : 0)); });
		rounds.slice(0, picks).forEach(function (round) {
			out.free[round.id] = (out.free[round.id] || 0) + 1;
			out.saved[round.id] = (out.saved[round.id] || 0) + round.price;
			out.discount += round.price;
		});
		return out;
	},

	//what his free rounds spare this ship, in points (0 without him) - setShipLevelRow moves pointCost by it when he comes or goes
	ballisticDiscount: function ballisticDiscount(ship) {
		return ship ? this.ballisticFreeRounds(this.shipRows(ship), ship.ballisticPicks).discount : 0;
	},

	//is he bought on this LOBBY ship (its rows, as the server's raiseMagazines reads them)? false in game, where the server applied him
	ballisticAboard: function ballisticAboard(ship) {
		return this.isDerived(ship) && this.listFor(ship).some(function (entry) { return entry.id === 'OFF_BALL'; });
	},

	//the magazine slots he adds (the static blueprint's ballisticSlots) - in the lobby, while he is bought; in game the payload's capacity has them
	ballisticSlots: function ballisticSlots(ship) {
		return this.ballisticAboard(ship) ? (parseInt(ship.ballisticSlots, 10) || 0) : 0;
	},

	/* ⭐ HIS +5 - what a ballistic weapon of this ship adds to its distance range (never its launch range), never a Proximity Laser: in game
	   the number the server published to every viewer while he serves (Officers::addForJson), in the lobby his own while he would serve.
	   ⚠️ MIRROR PAIR with Officers::distanceRangeBonus (PHP). */
	distanceRangeBonus: function distanceRangeBonus(ship, weapon) {
		if (!ship || !weapon || !weapon.ballistic || weapon.name === 'ProximityLaserNew' || weapon.name === 'jumpEngine') return 0;
		if (ship.distanceRangeBonus !== undefined) return parseInt(ship.distanceRangeBonus, 10) || 0;
		return this.servingDerived(ship).some(function (entry) { return entry.id === 'OFF_BALL'; }) ? this.BALLISTIC_RANGE : 0;
	},

	/* A ballistic weapon's Range line ("30/70", or "30" where the distance range is the launch range) with his +5 on the distance part:
	   "30/75", "30/35". Anything not in that shape, or a range of 0 (unlimited), is left as it is. */
	ballisticRangeLine: function ballisticRangeLine(ship, system, value) {
		var bonus = this.distanceRangeBonus(ship, system);
		var parts = bonus ? /^\s*(\d+)\s*(?:\/\s*(\d+))?\s*$/.exec(String(value)) : null;
		if (!parts) return value;
		var launch = parseInt(parts[1], 10);
		var distance = Math.max(launch, parts[2] !== undefined ? parseInt(parts[2], 10) : 0);
		if (distance <= 0) return value;
		return launch + '/' + (distance + bonus);
	},

	//in the lobby: a counted weapon's Ammunition line with the round the commit will give it (in game the server's count has it)
	ballisticAmmunitionLine: function ballisticAmmunitionLine(ship, system, value) {
		if (!this.ballisticAboard(ship) || system.checkAmmoMagazine === true || !this.isCountedBallistic(system)) return value;
		var n = parseInt(value, 10);
		return isNaN(n) ? value : n + this.BALLISTIC_EXTRA_ROUND;
	},

	//in the lobby: the magazine's "Total rounds: 280/280" with his slots - 280/290 (in game the server's capacity has them)
	ballisticMagazineSpecial: function ballisticMagazineSpecial(ship, value) {
		var slots = this.ballisticSlots(ship);
		if (!slots) return value;
		return String(value).replace(/Total rounds: (\d+)\/(\d+)/, function (all, rounds, capacity) {
			return 'Total rounds: ' + rounds + '/' + ((parseInt(capacity, 10) || 0) + slots);
		});
	},

	/* ------------------------------------------------------------------ Expert Quartermaster (Stage 30) */

	/* The six refits one of which he brings at no cost, in the rules' order - which a tie for the dearest goes by - and the two of them that are
	   ship-level rows (the rest are bought per system). ⚠️ MIRROR PAIR with Officers::$quartermasterRefits (PHP). */
	QM_REFITS: ['SYS_GSGT', 'SYS_ADT', 'IMPR_REA', 'SYS_THR', 'SYS_HARM', 'JUMP_ACC'],
	QM_SHIP_REFITS: ['IMPR_REA', 'JUMP_ACC'],
	QM_REFIT_LABELS: { SYS_GSGT: 'Gunsights', SYS_ADT: 'Advanced Defensive Targeting', IMPR_REA: 'Improved Reactor', SYS_THR: 'Improved Thrust Rating',
		SYS_HARM: 'Hardened Armour', JUMP_ACC: 'Jump Accelerator' },
	QM_REFIT_SYSTEMS: 2, //⚠️ MIRROR of Officers::QM_REFIT_SYSTEMS (PHP) - "any two weapons", "any two thrusters", two armoured systems

	/* ⭐ HIS FREE REFIT - of the six, the DEAREST bought, as the server rules it: Gunsights, Advanced Defensive Targeting, Improved Thrust - the
	   two dearest systems bought it, each at its dearest level; Hardened Armour - the dearest two on one side (a tie to the lower location),
	   never Structure or a Primary system; the Improved Reactor or the Jump Accelerator - its row. A tie to the first in QM_REFITS, a tie between
	   systems to the lower id. `rows`: the ship-level rows [{id, count, price}] - the ship's own (shipRows) or a buy dialog's (confirm.dialogRows);
	   the refits are ship.systemEnhancements, priced by their offers (the blueprint, D10). null without him bought, with his C&C destroyed before
	   battle or with none of the six; else {option, free: {row index: points spared}, placed, location, points}. `anyPost`: never mind his
	   C&C (the ship-level saving - quartermasterShipSaving: the refund of an officer on a destroyed post is dropDestroyedPosts' business,
	   which reads the saving AFTER the editor has marked the post destroyed).
	   ⚠️ MIRROR PAIR with Officers::quartermasterRefit (PHP). */
	quartermasterRefit: function quartermasterRefit(ship, rows, anyPost) {
		if (!ship || ship.flight || !rows.some(function (row) { return row.id === 'OFF_QM' && row.count > 0; })) return null;
		var cnc = this.findPost(ship, 'cnc');
		if (!cnc || (!anyPost && this.isPostDestroyed(ship, cnc))) return null;
		var self = this;
		var candidates = {};
		//read, never created: a static blueprint in a buy dialog has no refits, and must not be given an array
		(window.systemEnhancements && Array.isArray(ship.systemEnhancements) ? ship.systemEnhancements : []).forEach(function (row, i) {
			var count = row ? (parseInt(row[2], 10) || 0) : 0;
			if (count < 1 || self.QM_REFITS.indexOf(row[0]) === -1) return;
			var system = systemEnhancements.systemById(ship, row[6]);
			var offer = systemEnhancements.offerFor(ship, row[6], row[0]);
			if (!system || !offer || self.isPostDestroyed(ship, system)) return;
			if (row[0] === 'SYS_HARM' && !self.isQuartermasterArmour(system)) return;
			(candidates[row[0]] = candidates[row[0]] || []).push({ index: i, points: systemEnhancements.priceForLevel(offer, count - 1),
				id: parseInt(system.id, 10), location: parseInt(system.location, 10) || 0 });
		});
		var best = null;
		this.QM_REFITS.forEach(function (id) {
			var choice = null;
			if (self.QM_SHIP_REFITS.indexOf(id) !== -1) {
				var row = rows.filter(function (r) { return r.id === id && r.count > 0; })[0];
				if (row && row.price > 0) choice = { option: id, free: {}, placed: 0, location: null, points: row.price };
			} else {
				choice = self.quartermasterPair(id, candidates[id] || []);
			}
			if (choice && (!best || choice.points > best.points)) best = choice;
		});
		return best;
	},

	//the systems that make one per-system refit's share of his worth most - Hardened Armour's on one side. ⚠️ MIRROR PAIR with Officers::quartermasterPair (PHP)
	quartermasterPair: function quartermasterPair(id, list) {
		var self = this;
		var sides = {};
		list.forEach(function (candidate) {
			var key = (id === 'SYS_HARM') ? candidate.location : 0;
			(sides[key] = sides[key] || []).push(candidate);
		});
		var best = null;
		Object.keys(sides).map(Number).sort(function (a, b) { return a - b; }).forEach(function (location) {
			var pair = sides[location].slice().sort(function (a, b) { return (b.points - a.points) || (a.id - b.id); }).slice(0, self.QM_REFIT_SYSTEMS);
			var free = {};
			var points = 0;
			pair.forEach(function (candidate) { free[candidate.index] = candidate.points; points += candidate.points; });
			if (!best || points > best.points) {
				best = { option: id, free: free, placed: pair.length, location: (id === 'SYS_HARM') ? location : null, points: points };
			}
		});
		return best;
	},

	//may his free Hardened Armour go on this system? "Non-structure and non-primary" - ⚠️ MIRROR PAIR with Officers::isQuartermasterArmour (PHP)
	isQuartermasterArmour: function isQuartermasterArmour(system) {
		return Boolean(system) && system.name !== 'structure' && (parseInt(system.location, 10) || 0) !== 0;
	},

	//the rows his free refit spares on this ship's refits ({row index: points}) - what priceFreeRefits takes off, and the Political Officer's armour never counts
	quartermasterSpared: function quartermasterSpared(ship) {
		var refit = this.quartermasterRefit(ship, this.shipRows(ship));
		return refit ? refit.free : {};
	},

	/* what his free Improved Reactor or Jump Accelerator spares the SHIP-LEVEL bucket (pointCostEnh): 0 when his refit is a system's, or none.
	   Whatever his C&C's state, as the Political Officer's free expert (politicalDiscount) - see quartermasterRefit's `anyPost`. */
	quartermasterShipSaving: function quartermasterShipSaving(ship, rows) {
		var refit = ship ? this.quartermasterRefit(ship, rows || this.shipRows(ship), true) : null;
		return (refit && this.QM_SHIP_REFITS.indexOf(refit.option) !== -1) ? refit.points : 0;
	},

	/* After a refit write (systemEnhancements.set, dropDestroyed): his free refit may have moved between a system and a ship-level row - two
	   Gunsights that outbid his free Improved Reactor make the reactor paid again - so pointCostEnh and pointCost move by what the ship-level
	   saving changed by. `before`: quartermasterShipSaving read before the write. The refit bucket the caller settles itself. */
	settleQuartermaster: function settleQuartermaster(ship, before) {
		var delta = before - this.quartermasterShipSaving(ship);
		if (!delta) return;
		ship.pointCostEnh = (parseFloat(ship.pointCostEnh) || 0) + delta;
		ship.pointCost = (parseFloat(ship.pointCost) || 0) + delta;
	},

	/* A row whose rounds may be his free missiles: an AMMO_ row with a price on this hull, never Basic - any size (user ruling 2026-10-24: a
	   two-slot round counts as one missile). ⚠️ MIRROR PAIR with Officers::quartermasterPickTypes (PHP). */
	isQuartermasterPick: function isQuartermasterPick(id, price) {
		return typeof id === 'string' && id.indexOf('AMMO_') === 0 && id !== 'AMMO_B' && (Number(price) || 0) > 0;
	},

	/* ⭐ HIS FREE MISSILES - "two special missiles of any type" for each rack, one for a Class-SO: with him bought, the `picks` DEAREST rounds of the
	   pickable rows (isQuartermasterPick) cost nothing, each at its own level's price, a tie to the lower id - after an Expert Ballistic Officer
	   bought in the same rows has taken his (ballisticFreeRounds): those rounds are not his. `picks` / `ballisticPicks`: the static blueprint's
	   quartermasterPicks / ballisticPicks. Returns ballisticFreeRounds' shape. The commit rolls whatever is left unpicked
	   (Officers::placeQuartermasterMissiles), which counts the rounds past the Ballistic Officer's as these do. */
	quartermasterFreeRounds: function quartermasterFreeRounds(rows, picks, ballisticPicks) {
		var out = { free: {}, saved: {}, discount: 0 };
		picks = parseInt(picks, 10) || 0;
		if (picks <= 0 || !rows.some(function (row) { return row.id === 'OFF_QM' && row.count > 0; })) return out;
		var self = this;
		var taken = this.ballisticFreeRounds(rows, ballisticPicks).free;
		var rounds = [];
		rows.forEach(function (row) {
			if (!self.isQuartermasterPick(row.id, row.price)) return;
			for (var level = 0; level < row.count; level++) rounds.push({ id: row.id, price: row.price + level * (row.step || 0) });
		});
		rounds.sort(function (a, b) { return (b.price - a.price) || (a.id < b.id ? -1 : (a.id > b.id ? 1 : 0)); });
		var passed = {}; //the Ballistic Officer's are each row's dearest rounds - the first of that id in this order
		rounds.filter(function (round) {
			if ((passed[round.id] || 0) >= (taken[round.id] || 0)) return true;
			passed[round.id] = (passed[round.id] || 0) + 1;
			return false;
		}).slice(0, picks).forEach(function (round) {
			out.free[round.id] = (out.free[round.id] || 0) + 1;
			out.saved[round.id] = (out.saved[round.id] || 0) + round.price;
			out.discount += round.price;
		});
		return out;
	},

	//what he spares this ship's ship-level bucket in points - his free reactor or accelerator, and his free rounds - setShipLevelRow moves pointCost by it
	quartermasterDiscount: function quartermasterDiscount(ship) {
		if (!ship) return 0;
		var rows = this.shipRows(ship);
		return this.quartermasterShipSaving(ship, rows) + this.quartermasterFreeRounds(rows, ship.quartermasterPicks, ship.ballisticPicks).discount;
	},

	//does this LOBBY ship have a Quartermaster whose free refit applies (bought, his C&C standing)?
	quartermasterOpen: function quartermasterOpen(ship) {
		if (!ship || ship.flight || !window.systemEnhancements || !this.isDerived(ship) || !this.isBought(ship, 'OFF_QM')) return false;
		var cnc = this.findPost(ship, 'cnc');
		return Boolean(cnc) && !this.isPostDestroyed(ship, cnc);
	},

	/* What setting a refit on `system` to `n` levels would move this ship's points by, his free refit (and a Political Officer's armour) included
	   - tried on the ship through the real write (systemEnhancements.set, which re-prices both buckets) and put back exactly as it was. */
	refitTryCost: function refitTryCost(ship, system, enhID, n) {
		var saved = { rows: systemEnhancements.clone(systemEnhancements.rows(ship)), sysEnh: ship.pointCostSysEnh, enh: ship.pointCostEnh, cost: ship.pointCost };
		var before = (parseFloat(ship.pointCostSysEnh) || 0) + (parseFloat(ship.pointCostEnh) || 0);
		systemEnhancements.set(ship, system.id, enhID, n);
		var after = (parseFloat(ship.pointCostSysEnh) || 0) + (parseFloat(ship.pointCostEnh) || 0);
		ship.systemEnhancements = saved.rows;
		ship.pointCostSysEnh = saved.sysEnh;
		ship.pointCostEnh = saved.enh;
		ship.pointCost = saved.cost;
		systemEnhancements.syncTooltip(ship);
		return after - before;
	},

	/* The system menu's row of one of his refits (decorateMenuRows): what it costs now (the price it is charged, his part taken off), what one
	   more level would add (refitTryCost - it may outbid his free reactor, or take a free Gunsight's place), and a note saying which is free. */
	decorateQuartermasterRow: function decorateQuartermasterRow(ship, system, row) {
		if (this.QM_REFITS.indexOf(row.enhID) === -1 || !this.quartermasterOpen(ship)) return;
		var bought = systemEnhancements.rowFor(ship, system.id, row.enhID);
		if (bought) row.price = parseFloat(bought[4]) || 0;
		if (row.count < row.max) row.nextPrice = this.refitTryCost(ship, system, row.enhID, row.count + 1);
		row.note = (row.note ? row.note + ' ' : '') + 'Expert Quartermaster: the dearest of his six refits is free. ' + this.quartermasterRefitText(ship) + '.';
	},

	//"Free refit: Gunsights (2 of 2)" - or what the commit will do with what is missing
	quartermasterRefitText: function quartermasterRefitText(ship) {
		var refit = this.quartermasterRefit(ship, this.shipRows(ship));
		if (!refit) return 'Free refit: none bought - one is picked at random when the fleet is committed';
		var text = 'Free refit: ' + this.QM_REFIT_LABELS[refit.option];
		if (this.QM_SHIP_REFITS.indexOf(refit.option) !== -1) return text;
		text += ' (' + refit.placed + ' of ' + this.QM_REFIT_SYSTEMS + ')';
		return refit.placed < this.QM_REFIT_SYSTEMS ? text + ' - the other is placed at random when the fleet is committed' : text;
	},

	/* The lobby's line on what he has brought so far - his SystemInfo summary (summaryOf): the free refit, and his free missiles "3 of 4 bought -
	   the rest are picked at random when the fleet is committed". */
	quartermasterStatus: function quartermasterStatus(ship) {
		var text = this.quartermasterRefitText(ship) + '.';
		var picks = parseInt(ship.quartermasterPicks, 10) || 0;
		if (!picks) return text;
		var free = this.quartermasterFreeRounds(this.shipRows(ship), picks, ship.ballisticPicks).free;
		var used = Object.keys(free).reduce(function (sum, id) { return sum + free[id]; }, 0);
		text += ' Free missiles: ' + used + ' of ' + picks + ' bought';
		return text + (used < picks ? ' - the rest are picked at random when the fleet is committed.' : '.');
	},

	/* ------------------------------------------------------------------ what a flight's officer costs */

	/* How many craft a flight's pointCost is for, so the flight a fighter officer's listed price is for:
	   1 on a single superheavy (maxFlightSize 1), otherwise 6 - the lobby's own convention
	   (confirm.getVariableSize). ⚠️ MIRROR PAIR with Officers::standardFlight (PHP). */
	standardFlight: function standardFlight(ship) {
		return (ship && Number(ship.maxFlightSize) === 1) ? 1 : 6;
	},

	/* The per-craft points a fighter officer adds on top of his scaled price: the Missileer's 1 per
	   missile each craft can carry ("+1 per missile the flight can take", user ruling 2026-10-11). The
	   offer tuple carries only the scaled part, since this one grows with the flight. */
	perCraftExtra: function perCraftExtra(ship, enhID) {
		var officer = this.get(enhID);
		if (!officer || !officer.perMissile || !officer.flight) return 0;
		return officer.perMissile * this.missilesPerCraft(ship);
	},

	/* ⭐ What one bought enhancement row costs a FLIGHT of `size` craft - THE rule every lobby site that
	   turns a fighter's rows into points goes through (confirm.getTotalCost and paintBuySummary, through
	   confirm.enhRowCost; gamelobby doBuyShip, doCopyShip, doEditShip and enhancementLines). A fighter
	   enhancement is priced per craft and multiplied by the flight size (D8). A fighter officer's listed
	   price is for a `standard` flight (standardFlight), so it is scaled by size / standard and rounded
	   up, plus his perCraftExtra per craft (user ruling 2026-10-15). enhCost is the row's own sum (price x
	   count) - so officers bought as a count (Stage 18, Expert Pilots) are scaled and rounded up ONCE for
	   the row, as Officers::flightPrice's $count; `extra` is perCraftExtra(ship, enhID), worked out once by the caller.
	   Stage 24: an `unscaled` officer costs his listed price at any size (a Breaching Expert serves in his one pod).
	   ⚠️ MIRROR PAIR with Officers::flightPrice (PHP). */
	flightCost: function flightCost(enhID, count, enhCost, size, extra, standard) {
		var cost = Number(enhCost) || 0;
		size = Number(size) || 1;
		if (!this.isFighterOfficer(enhID)) return cost * size;
		if ((parseInt(count, 10) || 0) < 1) return 0;
		if (this.get(enhID).unscaled) return cost;
		standard = Number(standard) || 6;
		return Math.ceil(cost * size / standard) + (Number(extra) || 0) * size;
	},

	/* The buy dialog's price note for a fighter officer's row: "29pts per 6 craft", "40pts per 6 craft + 6pts
	   per craft"; a single superheavy's is just its price; "57pts each per 6 craft, one per craft" for an
	   officer bought as a count (Stage 18). null for anything else. */
	priceNote: function priceNote(ship, enhancement) {
		if (!this.isFighterOfficer(enhancement[0])) return null;
		var standard = this.standardFlight(ship);
		var officer = this.get(enhancement[0]);
		if (officer && officer.multi && officer.unscaled) return enhancement[4] + 'pts each, one per pod'; //Stage 27: each his own pod's price
		if (officer && officer.multi) {
			return enhancement[4] + 'pts each' + (standard > 1 ? ' per ' + standard + ' craft' : '') + ', one per craft';
		}
		if (officer && officer.unscaled) return enhancement[4] + 'pts, in one craft'; //Stage 24: the same at any flight size
		var note = enhancement[4] + 'pts' + (standard > 1 ? ' per ' + standard + ' craft' : '');
		var extra = this.perCraftExtra(ship, enhancement[0]);
		if (extra > 0) note += ' + ' + extra + 'pts per craft';
		return note;
	},

	/* ------------------------------------------------------------------ what a window shows */

	/* The officers whose stat changes a window has to add itself: the lobby's own derivation. In game the
	   server has already applied them and sends the moved fields to every viewer (user ruling 2026-10-10),
	   and an enemy's derived list is empty - so this is empty everywhere but the lobby. Added at READ time,
	   never written into the ship, so a post destroyed in the pre-battle editor takes the bonus away on the
	   next render and an edit cannot compound it. */
	servingDerived: function servingDerived(ship) {
		if (!this.isDerived(ship)) return [];
		var turn = this.currentTurn();
		var self = this;
		return this.listFor(ship).filter(function (entry) { return self.serves(entry, turn); });
	},

	/* A ship field as a window should show it: iniativebonus, forwardDefense, sideDefense, toHitBonus, a
	   flight's offensivebonus and (Stage 24) freethrust, and the Navigator's turncost, pivotcost, rollcost and accelcost. ⚠️ MIRROR
	   PAIR with Officers::apply's stat changes (PHP) - enhancementsDifferential.js compares the two through
	   this function. */
	displayStat: function displayStat(ship, field) {
		var value = Number(ship && ship[field]) || 0;
		var serving = this.servingDerived(ship);
		var counted = {}; //Stage 27: an officer whose stats count once however many serve (`statsOnce` - the Breaching Experts' thrust)
		for (var i = 0; i < serving.length; i++) {
			var officer = this.get(serving[i].id);
			if (!officer) continue;
			if (officer.statsOnce && counted[officer.id]) continue;
			counted[officer.id] = true;
			if (officer.stats && officer.stats[field]) value += officer.stats[field];
			if (officer.adjust) value = this[officer.adjust](field, value);
		}
		//in game: an Expert Evader's Evade not yet sent (the server applied a sent one)
		if (field === 'offensivebonus') value -= this.pendingEvadeOb(ship);
		return value;
	},

	/* ------------------------------------------------------------------ Expert Navigator (Stage 6) */

	//The rulebook's turn cost levels, descending. ⚠️ MIRROR PAIR with Officers::$turnCostLadder (PHP).
	TURN_COST_LADDER: [2, 1.75, 1.66, 1.5, 1.33, 1.25, 1, 0.83, 0.75, 0.66, 0.5, 0.33, 0.25, 0.16, 0.1],
	NAV_CANNOT: 99, //a cost of 99 or more is a hull's "cannot" - left alone

	/* One movement cost with the Navigator's change - turn cost one level down the ladder (compared in
	   hundredths; 0.1 and 0 stay), pivot and roll -1 (minimum 1, a 0 stays 0), accel/decel x0.75 with its
	   fraction kept (movement.js getSpeedChangeCost rounds a turn's total up). Any other field unchanged.
	   ⚠️ MIRROR PAIR with Officers::turnCostLevelDown / navigatorThrustCost / navigatorAccelCost (PHP). */
	navigatorStat: function navigatorStat(field, value) {
		if (!(value > 0) || value >= this.NAV_CANNOT) return value;
		if (field === 'turncost') return this.turnCostLevelDown(value);
		if (field === 'pivotcost' || field === 'rollcost') return Math.max(1, value - 1);
		if (field === 'accelcost') return value * 3 / 4;
		return value;
	},

	/* ⭐ THE turn cost step, one level down the ladder (compared in hundredths; 0.1, 0 and a "cannot" stay):
	   the Expert Navigator's, and the Hyach Maneuvering Specialist's too (user ruling 2026-10-15) - its
	   preview in HyachSpecialists.doUse (model/system/baseSystems.js), applied after the Navigator's, which
	   the server has already put into ship.turncost. ⚠️ MIRROR PAIR with Officers::turnCostLevelDown (PHP). */
	turnCostLevelDown: function turnCostLevelDown(value) {
		if (!(value > 0) || value >= this.NAV_CANNOT) return value;
		var hundredths = Math.round(value * 100);
		for (var i = 0; i < this.TURN_COST_LADDER.length; i++) {
			if (Math.round(this.TURN_COST_LADDER[i] * 100) < hundredths) return this.TURN_COST_LADDER[i];
		}
		return value;
	},

	/* The other way: the lowest level above value - the Hyach Specialist's undo (doDecrease), as its old
	   table undid its halving. Exact for everything the Specialist meets: every Specialist hull's turn cost
	   (0.5, 0.66, 1, 1.5) and the level a Navigator puts each at are on the ladder above 0.1. */
	turnCostLevelUp: function turnCostLevelUp(value) {
		if (!(value > 0) || value >= this.NAV_CANNOT) return value;
		var hundredths = Math.round(value * 100);
		for (var i = this.TURN_COST_LADDER.length - 1; i >= 0; i--) {
			if (Math.round(this.TURN_COST_LADDER[i] * 100) > hundredths) return this.TURN_COST_LADDER[i];
		}
		return value;
	},

	displayIniativeBonus: function displayIniativeBonus(ship) {
		return this.displayStat(ship, 'iniativebonus');
	},

	/* Expert Technician, in the lobby: what he adds to THIS reactor's output - 0 unless it is his post.
	   +2, plus a third (rounded down, per system) of the powerReq of every destroyed system, which in the
	   lobby can only be pre-battle damage. power.js getReactorPower adds it to the balance.
	   ⚠️ MIRROR PAIR with Officers::technicianBonus (PHP).
	   Stage 16: an Expert Surge Officer's power on his Mag-Gravitic Reactor too (surgePower) - one may share it with a
	   Technician, so the two add up. */
	reactorBonus: function reactorBonus(ship, system) {
		if (!system) return 0;
		var serving = this.servingDerived(ship);
		var total = 0;
		for (var i = 0; i < serving.length; i++) {
			if (String(serving[i].post) !== String(system.id)) continue;
			if (serving[i].id === 'OFF_SURG') total += this.surgePower(ship);
			if (serving[i].id !== 'OFF_TECH') continue;
			var bonus = 2;
			var self = this;
			this.systemsOf(ship).forEach(function (s) {
				if (!s || s === system || String(s.id) === String(system.id)) return;
				var req = parseInt(s.powerReq, 10) || 0;
				if (req > 0 && self.isPostDestroyed(ship, s)) bonus += Math.floor(req / 3);
			});
			total += bonus;
		}
		return total;
	},

	/* Expert Surge Officer: +2 power on a capital ship or larger, +1 on a medium ship or HCV, none on an LCV.
	   ⚠️ MIRROR PAIR with Officers::surgePower (PHP). */
	surgePower: function surgePower(ship) {
		if (!ship) return 0;
		if (ship.Enormous || Number(ship.shipSizeClass) >= 3) return 2;
		if (Number(ship.shipSizeClass) >= 1) return 1;
		return 0;
	},

	/* Expert Technician, Efficient Transfers: +1 power while the player has switched off at least one
	   undestroyed system with a power requirement this turn - "only one system in any turn", so never
	   more than 1. A system forced offline by a critical (a cooldown, Forced Offline) returns its usual
	   power but not this point (user ruling 2026-10-15). In GAME only, from the server's list - so own
	   team only, as an enemy's list is empty; the lobby switches nothing off. No PHP pair: the power
	   balance lives only in power.js (getReactorPower adds this), the server has no twin of it. */
	efficientTransferBonus: function efficientTransferBonus(ship) {
		if (!ship || ship.flight || this.isDerived(ship) || !this.isActive(ship, 'OFF_TECH')) return 0;
		if (!window.shipManager || !shipManager.power) return 0;
		var systems = this.systemsOf(ship);
		for (var i = 0; i < systems.length; i++) {
			var s = systems[i];
			if (!s || s.destroyed || !(Number(s.powerReq) > 0)) continue;
			if (!shipManager.power.isOfflineOnTurn(ship, s, this.currentTurn())) continue;
			if (shipManager.power.isForcedOffline(ship, s)) continue;
			return 1;
		}
		return 0;
	},

	/* This weapon's fire control in its current mode as the lobby shows it, +1 on every non-null entry (null =
	   cannot target that size, and stays so) when:
	     - an Expert Gunner serves IN it - every mode;
	     - otherwise, in a Matter mode, the C&C Matter Weapons Expert serves, or the weapon-mounted one serves in
	       it; and, every mode, an Expert Turret Officer serves on its turret (Stage 22); and, every mode of every
	       weapon but the ram and a jump drive, an Expert Warrior serves in the C&C (Stage 28). A Gunner's +1 replaces
	       the Matter expert's, the Turret Officer's and the Warrior's on his own weapon (user rulings 2026-10-16,
	       2026-10-21, 2026-10-23), never adds.
	   Gunsights is already in system.fireControl (systemEnhancements.apply), so the two show stacked.
	   ⚠️ MIRROR PAIR with Officers::applyGunner / applyMatterExpert / applyWeaponMatterExpert / applyTurretOfficer / applyWarrior (PHP). */
	displayFireControl: function displayFireControl(ship, system) {
		var fc = system ? system.fireControl : null;
		if (!Array.isArray(fc)) return fc;
		var raise = this.fireControlRaise(ship, this.servingDerived(ship), system);
		if (!raise) return fc;
		return fc.map(function (v) { return (v === null || v === undefined) ? v : (parseInt(v, 10) || 0) + raise; });
	},

	/* how much the officers in `serving` raise this weapon's fire control in its current mode (displayFireControl's rule): 0, 1 or more -
	   0 in a mode whose fire control is fixed (the Antimatter Shredder's Shredder mode), as the server never raises one */
	fireControlRaise: function fireControlRaise(ship, serving, system) {
		if (this.hasFixedFireControl(system, system.firingMode)) return 0;
		var self = this;
		var on = function (id) {
			return serving.some(function (entry) { return entry.id === id && String(entry.post) === String(system.id); });
		};
		if (on('OFF_GUN')) return 1;
		var turret = serving.some(function (entry) {
			return entry.id === 'OFF_TURR' && self.turretKey(self.systemById(ship, entry.post)) === self.turretKey(system);
		}) ? 1 : 0;
		var warrior = (!system.isRammingAttack && system.name !== 'jumpEngine'
			&& serving.some(function (entry) { return entry.id === 'OFF_WARR'; })) ? 1 : 0;
		return turret + warrior + ((this.isMatterWeapon(system) && this.isMatterMode(system, system.firingMode)
			&& (on('OFF_MWEW') || serving.some(function (entry) { return entry.id === 'OFF_MWE'; }))) ? 1 : 0);
	},

	/* May nothing raise this firing mode's fire control? The static blueprint's `fixedFireControlModes` (the Antimatter Shredder's
	   Shredder mode). ⚠️ MIRROR PAIR with Weapon::hasFixedFireControl (PHP); systemEnhancements.hasFixedFireControl reads the same key. */
	hasFixedFireControl: function hasFixedFireControl(system, mode) {
		var fixed = system ? system.fixedFireControlModes : null;
		return Array.isArray(fixed) && fixed.map(Number).indexOf(parseInt(mode, 10)) !== -1;
	},

	/* Which turret a weapon is on (Stage 22): its linked firing group, or the weapon alone - "a turret of one". The static blueprint carries
	   linkedFiringGroup wherever it is set. null for no system. ⚠️ MIRROR PAIR with Officers::turretKey (PHP). */
	turretKey: function turretKey(system) {
		if (!system) return null;
		var group = system.linkedFiringGroup;
		return (group !== undefined && group !== null) ? 'group:' + group : 'mount:' + parseInt(system.id, 10);
	},

	/* The Expert Turret Officer serving on this weapon's turret - not one ordered to move this turn (he serves nowhere then, as the server
	   reads a committed order) - or null. The lobby's derived list and the owner's server list both answer; an enemy's is empty. */
	turretOfficerOn: function turretOfficerOn(ship, system) {
		if (!ship || !system || ship.flight) return null;
		var self = this;
		var turn = this.currentTurn();
		var turret = this.turretKey(system);
		return this.listFor(ship).filter(function (entry) {
			return entry.id === 'OFF_TURR' && self.serves(entry, turn) && self.transferOf(ship, entry) === null
				&& self.turretKey(self.systemById(ship, entry.post)) === turret;
		})[0] || null;
	},

	/* A turret-mounted weapon's "Turret Mount" tooltip line under an Expert Turret Officer: the jam roll's threshold 2 higher ("subtract 2
	   from the die roll" - 17+ reads 19+), and his name. The server writes the blueprint's line; only his own team sees this one. A turret
	   already jammed ("JAMMED - locked to ...") has no roll left to show. */
	TURRET_JAM_RELIEF: 2,
	turretMountLine: function turretMountLine(ship, system, value) {
		var text = String(value);
		var roll = /on a (\d+)\+ \(d20\)/.exec(text);
		if (!roll || !this.turretOfficerOn(ship, system)) return value;
		return text.replace(roll[0], 'on a ' + ((parseInt(roll[1], 10) || 0) + this.TURRET_JAM_RELIEF) + '+ (d20)') + ' (Expert Turret Officer)';
	},

	/* Stage 10 (user request 2026-10-16): the fire control an officer ordered to move THIS turn - not yet committed - still
	   gives this weapon in the numbers the server sent. The server raised them at load and drops them once the move is
	   committed (he serves nowhere that turn), so the owner's hit-chance preview drops them at once
	   (weaponManager.computeFireControl) - a ballistic launched in the same Initial Orders would otherwise show his +5%.
	   displayFireControl's rule asked twice: with every serving officer, and without those with an order pending - so a
	   Gunner leaving a Matter gun the C&C expert also covers costs it nothing, as on the server (the expert's +1 takes over). */
	pendingMoveFireControl: function pendingMoveFireControl(ship, weapon) {
		if (!ship || !weapon || ship.flight || this.isDerived(ship) || !Array.isArray(ship.officerTransfers) || !ship.officerTransfers.length) return 0;
		var self = this;
		var turn = this.currentTurn();
		var serving = this.listFor(ship).filter(function (entry) { return self.serves(entry, turn); });
		var staying = serving.filter(function (entry) { return self.transferOf(ship, entry) === null; });
		return this.fireControlRaise(ship, serving, weapon) - this.fireControlRaise(ship, staying, weapon);
	},

	/* Expert Jump Officer, in the lobby: the hexes he adds to THIS drive's range - 0 unless it is his post.
	   In game the server raised the range and sent it with the rebuilt tooltip (JumpEngine::stripForJson),
	   and the derived list is empty there. ⚠️ MIRROR PAIR with Officers::JUMP_RANGE /
	   JumpEngine::applyJumpOfficer (PHP). */
	JUMP_RANGE: 1,
	jumpRangeBonus: function jumpRangeBonus(ship, system) {
		if (!system) return 0;
		var serving = this.servingDerived(ship);
		for (var i = 0; i < serving.length; i++) {
			if (serving[i].id === 'OFF_JUMP' && String(serving[i].post) === String(system.id)) return this.JUMP_RANGE;
		}
		return 0;
	},

	/* ------------------------------------------------------------------ Expert Laser Technician (Stage 11) */

	/* His flat bonus on each laser, by the class's `name`. ⚠️ MIRROR PAIR with Officers::$laserClassBonus (PHP), which
	   is keyed by class - officersHarness compares the two over every laser in the statics. Any other laser: +1. */
	LASER_BONUS: { SpinalLaser: 3, BlastLaser: 2, improvedBlastLaser: 2, LtBlastLaser: 2 },

	/* What he adds to a hit from this laser: 0 unless he serves, and 0 on a laser a serving Expert Gunner holds - the
	   Gunner's +1 per die replaces it (user ruling 2026-10-17). A Gunner ordered off it this turn serves nowhere once
	   the order is committed, so the laser has it back at once, as on the server (pendingMoveFireControl's reading).
	   The lobby's derived list and the owner's server list both answer; an enemy's list is empty.
	   ⚠️ MIRROR PAIR with Officers::laserDamageBonus (PHP). */
	laserDamageBonus: function laserDamageBonus(ship, system) {
		if (!ship || ship.flight || !this.isLaserWeapon(system) || !this.isActive(ship, 'OFF_LASR')) return 0;
		if (this.gunnerServesOn(ship, system)) return 0;
		return this.LASER_BONUS[system.name] || 1;
	},

	//a serving Expert Gunner holds this weapon, and is not ordered off it this turn - his +1 per die replaces another officer's flat bonus there
	gunnerServesOn: function gunnerServesOn(ship, system) {
		var self = this;
		var turn = this.currentTurn();
		return this.listFor(ship).some(function (entry) {
			return entry.id === 'OFF_GUN' && String(entry.post) === String(system.id) && self.serves(entry, turn) && self.transferOf(ship, entry) === null;
		});
	},

	/* The points of power the player has put into this laser this turn while he serves - in game only (the lobby boosts
	   nothing), and only on the owner's side, the one the server tells the laser is boostable. 1 point = +1 damage,
	   never more than half a hit's roll, from one pool for the turn (Officers::laserBoostDamage). */
	laserBoost: function laserBoost(ship, system) {
		if (!ship || ship.flight || this.isDerived(ship) || !this.isLaserWeapon(system) || !this.isActive(ship, 'OFF_LASR')) return 0;
		if (!window.shipManager || !shipManager.power || typeof shipManager.power.getBoost !== 'function') return 0;
		return parseInt(shipManager.power.getBoost(system), 10) || 0;
	},

	//SystemInfo's "Laser boost" line, or null with no boost
	laserBoostText: function laserBoostText(ship, system) {
		var boost = this.laserBoost(ship, system);
		if (!boost) return null;
		return '+' + boost + ' damage this turn, never more than half a hit\'s roll';
	},

	//a laser's Damage line with his flat bonus and this turn's boost (raiseDamageLine); a line describing a non-laser mode 1 is left alone
	laserDamageLine: function laserDamageLine(ship, system, value) {
		if (!this.isLaserMode(system, 1)) return value;
		return this.raiseDamageLine(system, value, this.laserDamageBonus(ship, system), this.laserBoost(ship, system));
	},

	/* A weapon's Damage line ("min-max", or one number - Weapon::setSystemDataWindow's, which describes mode 1) with an
	   officer's flat bonus and a boost on each end: a roll gains min(boost, half of it). The roll is read off the weapon's
	   own min/max, so a number already raised by the line (a Hyach Weapon Specialist's +3) is carried, not halved. A line
	   in any other shape is left alone. */
	raiseDamageLine: function raiseDamageLine(system, value, flat, boost) {
		if (!flat && !boost) return value;
		var m = /^\s*(\d+)\s*(?:-\s*(\d+))?\s*$/.exec(String(value));
		if (!m) return value;
		var lo = parseInt(m[1], 10), hi = (m[2] !== undefined) ? parseInt(m[2], 10) : lo;
		var first = function (arr, fallback) { return (arr && typeof arr === 'object' && arr[1] !== undefined) ? arr[1] : fallback; };
		var rollLo = parseInt(first(system.minDamageArray, system.minDamage), 10);
		var rollHi = parseInt(first(system.maxDamageArray, system.maxDamage), 10);
		if (!(rollLo >= 0)) rollLo = lo;
		if (!(rollHi >= 0)) rollHi = hi;
		lo += flat + Math.min(boost, Math.floor(rollLo / 2));
		hi += flat + Math.min(boost, Math.floor(rollHi / 2));
		return (lo === hi) ? String(hi) : lo + '-' + hi;
	},

	/* ------------------------------------------------------------------ Expert Plasma Scientist (Stage 12) */

	/* His flat bonus by the class's `name`: +2 a Fuser, 0 the light plasma weapons (user ruling 2026-10-17) and the Plasma
	   Web, +1 any other plasma weapon. ⚠️ MIRROR PAIR with Officers::$plasmaClassBonus (PHP), keyed by class -
	   officersHarness compares the two over every plasma weapon in the statics. */
	PLASMA_BONUS: { Fuser: 2, RangedFuser: 2, FuserArray: 2, lightPlasma: 0, lightPlasmaBolter: 0, LtPlasmaProjector: 0,
		NexusLightEnhPlasma: 0, NexusLightMagGun: 0, EWPointPlasmaGun: 0, EWHeavyPointPlasmaGun: 0, PlasmaTorch: 0,
		TrekPlasmaBurst: 0, PakmaraPlasmaWeb: 0 },

	//⚠️ MIRROR PAIR with Officers::isPlasmaWeapon (PHP)
	isPlasmaWeapon: function isPlasmaWeapon(system) {
		if (!system || !system.weapon) return false;
		if (system.weaponClass === 'Plasma') return true;
		var classes = system.weaponClassArray;
		if (!classes || typeof classes !== 'object') return false;
		return Object.keys(classes).some(function (mode) { return classes[mode] === 'Plasma'; });
	},

	isPlasmaMode: function isPlasmaMode(system, mode) {
		var classes = system.weaponClassArray;
		var cls = (classes && typeof classes === 'object' && classes[mode] !== undefined) ? classes[mode] : system.weaponClass;
		return cls === 'Plasma';
	},

	/* What he adds to a hit from this weapon while he serves: the lobby's derived list and the owner's server list both
	   answer; an enemy's list is empty. ⚠️ MIRROR PAIR with Officers::plasmaDamageBonus / ::plasmaFlatBonus (PHP). */
	plasmaDamageBonus: function plasmaDamageBonus(ship, system) {
		if (!ship || ship.flight || !this.isPlasmaWeapon(system) || !this.isActive(ship, 'OFF_PLAS')) return 0;
		var bonus = this.PLASMA_BONUS[system.name];
		return (bonus === undefined) ? 1 : bonus;
	},

	//a plasma weapon's Damage line with his flat bonus (raiseDamageLine); a line describing a non-plasma mode 1 is left alone
	plasmaDamageLine: function plasmaDamageLine(ship, system, value) {
		if (!this.isPlasmaMode(system, 1)) return value;
		return this.raiseDamageLine(system, value, this.plasmaDamageBonus(ship, system), 0);
	},

	/* The extra point a Plasma Battery holds while he serves - and while it stands ("They cannot operate if completely
	   destroyed"). PlasmaBattery.doIndividualNotesTransfer charges it up to this at the Initial Orders commit; the server
	   takes the charge as sent. In game only, so own team. ⚠️ MIRROR PAIR with Officers::PLASMA_BATTERY_BONUS /
	   PlasmaBattery::getMaxCapacity (PHP). */
	PLASMA_BATTERY_BONUS: 1,
	batteryCapacityBonus: function batteryCapacityBonus(ship, battery) {
		if (!ship || ship.flight || this.isDerived(ship) || !this.isActive(ship, 'OFF_PLAS')) return 0;
		if (!battery || battery.destroyed) return 0;
		return this.PLASMA_BATTERY_BONUS;
	},

	/* ------------------------------------------------------------------ Expert Religious Leader (Stage 14) */

	/* What he adds to a hit from this weapon: +1, while he serves and after he is lost (his crew's frenzy keeps it) - the
	   lobby's derived list and the owner's server list both answer; an enemy's list is empty. A weapon whose mode-1 roll
	   cannot pass 0 (an Electro-Pulse Gun, a Stun Beam: their effect is not damage) and a ram gain nothing.
	   ⚠️ MIRROR PAIR with Officers::religiousDamageBonus (PHP). */
	RELIGIOUS_DAMAGE: 1,
	religiousDamageBonus: function religiousDamageBonus(ship, system) {
		if (!ship || ship.flight || !system || !system.weapon || system.name === 'RammingAttack') return 0;
		if (!(parseInt(this.modeOneDamage(system.maxDamageArray, system.maxDamage), 10) > 0)) return 0;
		var list = this.listFor(ship), turn = this.currentTurn();
		for (var i = 0; i < list.length; i++) {
			//at his C&C, or anywhere else as OFF_RELIP (Stage 15)
			if (list[i].id === 'OFF_RELI' || list[i].id === 'OFF_RELIP') return (this.serves(list[i], turn) || this.isLost(list[i], turn)) ? this.RELIGIOUS_DAMAGE : 0;
		}
		return 0;
	},

	//a weapon's mode-1 damage figure (the one its Damage line describes): the per-mode array's, else the plain value
	modeOneDamage: function modeOneDamage(arr, fallback) {
		return (arr && typeof arr === 'object' && arr[1] !== undefined) ? arr[1] : fallback;
	},

	/* A Damage line ("min-max", or one number) with his +1 - on the low end only when the lowest roll is above 0, since a
	   hit rolling 0 gains nothing (the server's rule). Applied after a Laser Technician's or Plasma Scientist's raise, so
	   the bonuses add up. A line in any other shape is left alone. */
	religiousDamageLine: function religiousDamageLine(ship, system, value) {
		return this.hitBonusLine(system, value, this.religiousDamageBonus(ship, system));
	},

	//a Damage line with a flat bonus on every hit - religiousDamageLine's rule, shared with surgeDamageLine (Stage 16)
	hitBonusLine: function hitBonusLine(system, value, bonus) {
		if (!bonus) return value;
		var m = /^\s*(\d+)\s*(?:-\s*(\d+))?\s*$/.exec(String(value));
		if (!m) return value;
		var lo = parseInt(m[1], 10), hi = (m[2] !== undefined) ? parseInt(m[2], 10) : lo;
		if (parseInt(this.modeOneDamage(system.minDamageArray, system.minDamage), 10) !== 0) lo += bonus;
		hi += bonus;
		return (lo === hi) ? String(hi) : lo + '-' + hi;
	},

	/* ------------------------------------------------------------------ Expert Surge Officer (Stage 16) */

	/* What he adds to a hit from this weapon in mode 1 (the mode its Damage line describes): +1 for an Electromagnetic mode 1
	   while he serves - a Surge Cannon's mode 1 is one cannon, so +1 too (a combined shot's +1 per cannon is the server's,
	   and the officer's summary says it). A weapon whose mode-1 roll cannot pass 0 (a Burst Beam) gains nothing. The lobby's
	   derived list and the owner's server list both answer; an enemy's list is empty.
	   ⚠️ MIRROR PAIR with Officers::surgeDamageBonus (PHP). */
	SURGE_DAMAGE: 1,
	surgeDamageBonus: function surgeDamageBonus(ship, system) {
		if (!ship || ship.flight || !system || !system.weapon) return 0;
		var classes = system.weaponClassArray;
		var cls = (classes && typeof classes === 'object' && classes[1] !== undefined) ? classes[1] : system.weaponClass;
		if (cls !== 'Electromagnetic') return 0;
		if (!(parseInt(this.modeOneDamage(system.maxDamageArray, system.maxDamage), 10) > 0)) return 0;
		return this.isActive(ship, 'OFF_SURG') ? this.SURGE_DAMAGE : 0;
	},

	surgeDamageLine: function surgeDamageLine(ship, system, value) {
		return this.hitBonusLine(system, value, this.surgeDamageBonus(ship, system));
	},

	/* ------------------------------------------------------------------ Expert Ion Technician (Stage 17) */

	//⚠️ MIRROR PAIR with Officers::isIonWeapon (PHP): an Ion mode, or the Ion Torpedo (a Ballistic weapon in FV)
	isIonWeapon: function isIonWeapon(system) {
		if (!system || !system.weapon) return false;
		if (system.name === 'ionTorpedo' || system.weaponClass === 'Ion') return true;
		var classes = system.weaponClassArray;
		if (!classes || typeof classes !== 'object') return false;
		return Object.keys(classes).some(function (mode) { return classes[mode] === 'Ion'; });
	},

	isIonMode: function isIonMode(system, mode) {
		if (system.name === 'ionTorpedo') return true;
		var classes = system.weaponClassArray;
		var cls = (classes && typeof classes === 'object' && classes[mode] !== undefined) ? classes[mode] : system.weaponClass;
		return cls === 'Ion';
	},

	//its mode-1 damage is one number. ⚠️ MIRROR PAIR with Officers::scoresFlat (PHP)
	ionScoresFlat: function ionScoresFlat(system) {
		return parseInt(this.modeOneDamage(system.minDamageArray, system.minDamage), 10) === parseInt(this.modeOneDamage(system.maxDamageArray, system.maxDamage), 10);
	},

	/* What he adds to a hit from a fixed-damage ion weapon (a Dual Ion Bolter, an Ion Torpedo): +1 while he serves, but not on a weapon a
	   serving Expert Gunner holds. A weapon whose mode-1 damage is 0 gains nothing, and the Rad Cannon has its own rules (its 10 is a
	   number of Structure boxes; its roll is 0). His +1 per die is not shown, as Elite Crew's and a Gunner's are not. The lobby's derived
	   list and the owner's server list both answer; an enemy's list is empty. ⚠️ MIRROR PAIR with Officers::ionFlatBonus (PHP). */
	ION_FLAT_BONUS: 1,
	ionDamageBonus: function ionDamageBonus(ship, system) {
		if (!ship || ship.flight || !this.isIonWeapon(system) || !this.isIonMode(system, 1) || system.name === 'RadCannon') return 0;
		if (!this.ionScoresFlat(system) || !(parseInt(this.modeOneDamage(system.maxDamageArray, system.maxDamage), 10) > 0)) return 0;
		if (!this.isActive(ship, 'OFF_ION') || this.gunnerServesOn(ship, system)) return 0;
		return this.ION_FLAT_BONUS;
	},

	/* The level of thrust boost the player has put into this ion gun this turn while he serves - in game only, on the owner's side,
	   the one the server tells the gun is boostable (ION_BOOST_THRUST thrust, +1d10+1 if it hits - Officers::ionBoostDamage). At most
	   ION_BOOST_MAX, as the server counts it (user ruling 2026-10-18, revised: one level per gun). */
	ION_BOOST_THRUST: 3,
	ION_BOOST_MAX: 1,
	ionBoost: function ionBoost(ship, system) {
		if (!ship || ship.flight || this.isDerived(ship) || !this.isIonWeapon(system) || this.ionScoresFlat(system) || !this.isActive(ship, 'OFF_ION')) return 0;
		if (!window.shipManager || !shipManager.power || typeof shipManager.power.getBoost !== 'function') return 0;
		return Math.min(this.ION_BOOST_MAX, parseInt(shipManager.power.getBoost(system), 10) || 0);
	},

	//power.js clickPlus asks it: this ion gun already holds his one boost level this turn. ⚠️ MIRROR PAIR with Officers::ION_BOOST_MAX (PHP)
	ionBoostFull: function ionBoostFull(ship, system) {
		return this.ionBoost(ship, system) >= this.ION_BOOST_MAX;
	},

	//SystemInfo's "Thrust boost" line, or null with no boost - with the rule a boosted gun lives by (Officers::ionDischarged)
	ionBoostText: function ionBoostText(ship, system) {
		var n = this.ionBoost(ship, system);
		if (!n) return null;
		return '+' + n + 'd10+' + n + ' damage if it hits (' + (n * this.ION_BOOST_THRUST) + ' thrust). It must fire this turn, or it discharges as if it had';
	},

	/* An ion weapon's Damage line: a fixed-damage weapon's +1 (hitBonusLine), and this turn's boost on each end (n x 1d10+1: +2n low,
	   +11n high); the Rad Cannon's 10 Structure boxes read 12. A line describing a non-ion mode 1, or in any other shape, is left alone. */
	RAD_STRUCTURE_BOXES: 12,
	ionDamageLine: function ionDamageLine(ship, system, value) {
		if (system.name === 'RadCannon') {
			return (!ship.flight && this.isActive(ship, 'OFF_ION') && String(value).trim() === '10') ? String(this.RAD_STRUCTURE_BOXES) : value;
		}
		if (!this.isIonMode(system, 1)) return value;
		var line = this.hitBonusLine(system, value, this.ionDamageBonus(ship, system));
		var n = this.ionBoost(ship, system);
		var m = n ? /^\s*(\d+)\s*-\s*(\d+)\s*$/.exec(String(line)) : null;
		if (!m) return line;
		return (parseInt(m[1], 10) + 2 * n) + '-' + (parseInt(m[2], 10) + 11 * n);
	},

	/* The Rad Cannon's Special text as the owner's tooltip shows it while he serves (Officers::improvesRadCannon): 12 Structure boxes,
	   the C&C's roll at +4, and a critical roll forced on a Scanner, an Engine and a reactor (a Sub Reactor too). A sentence worded
	   otherwise is left as it is. */
	radCannonSpecial: function radCannonSpecial(ship, value) {
		if (!ship || ship.flight || !this.isActive(ship, 'OFF_ION')) return value;
		return String(value)
			.replace('Structure: 10 boxes', 'Structure: ' + this.RAD_STRUCTURE_BOXES + ' boxes')
			.replace('critical roll forced (at +2)', 'critical roll forced (at +4)')
			.replace('Scanner: output reduced by 1.', 'Scanner: output reduced by 1, and critical roll forced.')
			.replace('Engine: output reduced by 2.', 'Engine: output reduced by 2, and critical roll forced.<br> - Reactor or Sub Reactor: critical roll forced.');
	},

	/* ------------------------------------------------------------------ Expert ELINT Officer (Stage 20) */

	ELINT_SENSORS: 1,           //⚠️ MIRROR of Officers::ELINT_SENSORS (PHP)
	ELINT_RANGE_PCT: 25,        //...ELINT_RANGE_PCT - the lobby's own figure; in game the server publishes it (elintRangeBonus)
	ELINT_BLANKET_BALLISTIC: 3, //...ELINT_BLANKET_BALLISTIC - likewise (blanketBallistic)

	/* In the lobby: what he adds to THIS array's output - +1 on his own post, 0 anywhere else. shipManager.systems.getOutput adds it,
	   so the icon and the EW pool show it. In game the server raised `output` (servingDerived is empty there).
	   ⚠️ MIRROR PAIR with Officers::applyElintOfficer (PHP). */
	sensorBonus: function sensorBonus(ship, system) {
		if (!system) return 0;
		var serving = this.servingDerived(ship);
		for (var i = 0; i < serving.length; i++) {
			if (serving[i].id === 'OFF_ELINT' && String(serving[i].post) === String(system.id)) return this.ELINT_SENSORS;
		}
		return 0;
	},

	/* How much longer his ship's ELINT ranges are, in percent: in game the number the server published to every viewer while he serves
	   (Officers::addForJson - an opponent's previews need it too), in the lobby his own while he would serve. 0 without him. */
	elintRangeBonus: function elintRangeBonus(ship) {
		if (!ship) return 0;
		if (ship.elintRangeBonus !== undefined) return parseInt(ship.elintRangeBonus, 10) || 0;
		return this.servingDerived(ship).some(function (entry) { return entry.id === 'OFF_ELINT'; }) ? this.ELINT_RANGE_PCT : 0;
	},

	/* THE range of an ELINT function from `elint`: `base` hexes, his percent longer, fractions of 0.5 or more up - 50 -> 63, 30 -> 38,
	   20 -> 25.
	   ⚠️ MIRROR PAIR with Officers::elintRange (PHP). */
	elintRange: function elintRange(elint, base) {
		var pct = this.elintRangeBonus(elint);
		if (!pct) return base;
		return Math.floor((base * (100 + pct) + 50) / 100);
	},

	/* His ELINT's blanket points against a ballistic weapon, scaled for the quarter ew.getSupportedBDEW takes: x4/3 while the server
	   says his blanket counts a third (blanketBallistic). ⚠️ MIRROR PAIR with Officers::ballisticBlanket (PHP). */
	ballisticBlanket: function ballisticBlanket(elint, blanket) {
		var divisor = elint ? parseInt(elint.blanketBallistic, 10) || 0 : 0;
		if (!divisor) return blanket;
		return blanket * 4 / divisor;
	},

	/* The ELINT array's Special text as the tooltip shows it while he serves - to every viewer in game, as the numbers are public: the
	   ranges 25% longer and the blanket's third against ballistics. A sentence worded otherwise is left as it is. */
	elintSpecial: function elintSpecial(ship, value) {
		if (!ship || ship.flight || !this.elintRangeBonus(ship)) return value;
		var self = this;
		var hexes = function (text, base) { return text.replace(new RegExp('\\b' + base + ' hexes', 'g'), self.elintRange(ship, base) + ' hexes'); };
		var divisor = ship.blanketBallistic !== undefined ? parseInt(ship.blanketBallistic, 10) || 0 : this.ELINT_BLANKET_BALLISTIC;
		var text = hexes(hexes(hexes(String(value), 50), 30), 20); //SDEW 50, SOEW and Disruption 30, Blanket 20
		if (divisor) text = text.replace('per 4 points allocated.', 'per 4 points allocated (per ' + divisor + ' against ballistic weapons).');
		return text;
	},

	/* SystemInfo's one hook into a system's data rows (and its Special text). The weapon tooltip quotes fire
	   control only through data["Fire control (fighter/med/cap)"] - in game too, less an officer just ordered to
	   move (pendingMoveFireControl, user request 2026-10-16) - and a jump drive its range through
	   data["Range"] and the Special sentence "target a hex within N hexes" - all baked into the static
	   blueprint, so the lobby rewrites them as they are shown. N is the drive's range as the lobby has it
	   (after a Jump Accelerator's +2, which lobbyEnhancements writes into both). In game the server's
	   isModified fire control already rebuilt the one, and its payload the other.
	   Stage 11: a laser's Damage, with an Expert Laser Technician's bonus and boost (laserDamageLine) - in game too,
	   where the server applies both only at damage time. Stage 12: a plasma weapon's, with an Expert Plasma Scientist's
	   +1 or +2 (plasmaDamageLine), the same way. Stage 14: every weapon's, with an Expert Religious Leader's +1 on top
	   (religiousDamageLine). Stage 16: an EM weapon's, with an Expert Surge Officer's +1 (surgeDamageLine). Stage 17: an ion
	   weapon's, with an Expert Ion Technician's +1 and thrust boost (ionDamageLine), and the Rad Cannon's Special text. Stage 20: an
	   ELINT array's Special text, with an Expert ELINT Officer's ranges and blanket (elintSpecial) - to every viewer, in game too.
	   Stage 21: a fighter's tail gun's "Tail Gun" line (tailGunLine). Stage 22: a turret-mounted weapon's "Turret Mount" line, under an
	   Expert Turret Officer (turretMountLine), and his +1 in its fire control (displayFireControl). Stage 24: a pod's Marines' "Elite"
	   line, under a Breaching Expert (marinesEliteLine). Stage 26: the Hyach Computer's BFCP line in the lobby, with an Expert Software
	   Engineer's point (bfcpLine). */
	DATA_KEY_FIRECONTROL: 'Fire control (fighter/med/cap)',
	adjustDataDisplay: function adjustDataDisplay(ship, system, key, value) {
		if (!system) return value;
		if (key === 'Damage') {
			var line = value;
			if (this.isLaserWeapon(system)) line = this.laserDamageLine(ship, system, line);
			else if (this.isPlasmaWeapon(system)) line = this.plasmaDamageLine(ship, system, line);
			else if (this.isIonWeapon(system)) line = this.ionDamageLine(ship, system, line);
			return this.surgeDamageLine(ship, system, this.religiousDamageLine(ship, system, line));
		}
		if (system.name === 'RadCannon' && key === 'Special') return this.radCannonSpecial(ship, value);
		if ((system.name === 'elintScanner' || system.name === 'chameleonSensors') && key === 'Special') return this.elintSpecial(ship, value);
		if (system.tailGun && key === 'Tail Gun') return this.tailGunLine(ship, value);
		if (key === 'Elite' && (system.name === 'Marines' || system.name === 'ExtraMarines')) return this.marinesEliteLine(ship, system, value);
		if (key === 'Turret Mount') return this.turretMountLine(ship, system, value);
		if (system.name === 'hyachComputer' && key === 'Bonus Fire Control Points (BFCP)') return this.bfcpLine(ship, system, value);
		//Stage 29: an Expert Ballistic Officer's distance range (every viewer); in the lobby his magazine slots and a counted weapon's round
		if (key === 'Range' && system.ballistic && system.name !== 'jumpEngine') return this.ballisticRangeLine(ship, system, value);
		if (key === 'Ammunition') return this.ballisticAmmunitionLine(ship, system, value);
		if (system.name === 'ammoMagazine' && key === 'Special') return this.ballisticMagazineSpecial(ship, value);
		if (system.name === 'jumpEngine' && (key === 'Range' || key === 'Special')) {
			var bonus = this.jumpRangeBonus(ship, system);
			if (!bonus) return value;
			if (key === 'Range') return (parseInt(value, 10) || 0) + bonus;
			var range = parseInt(system.range, 10) || 0;
			return String(value).replace('within ' + range + ' hexes', 'within ' + (range + bonus) + ' hexes');
		}
		if (key !== this.DATA_KEY_FIRECONTROL) return value;
		var fc = this.displayFireControl(ship, system);
		//Stage 10: an officer ordered off this turn gives the weapon nothing this turn - the hit-chance preview's rule
		var loss = this.pendingMoveFireControl(ship, system);
		if (loss && Array.isArray(fc)) fc = fc.map(function (v) { return (v === null || v === undefined) ? v : (parseInt(v, 10) || 0) - loss; });
		if (fc === system.fireControl) return value;
		if (typeof system.translateFCtoD100txt === 'function') return system.translateFCtoD100txt(fc);
		return fc.map(function (v) { return (v === null || v === undefined) ? '-' : String(v * 5); }).join('/');
	}
};
