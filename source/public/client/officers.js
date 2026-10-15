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

	/* ⚠️ MIRROR of Officers::$registry (PHP): ids, labels, posts, `needs`, `flight` and `perMissile`, in
	   the SAME order - the order every list here is shown in. `summary` is the effect line the System
	   Info tooltip prints (D12). `stats` is what the officer adds to ship fields while he serves
	   (Officers::apply), which the lobby adds at READ time (displayStat); his other effects have their own
	   previews below (reactorBonus, displayFireControl) or none the lobby can show (the Scanner's saved EW).
	   Fighter officers (Stage 4) carry `flight: true`: priced for a standard flight and scaled by the
	   size bought (flightCost; user ruling 2026-10-15). */
	REGISTRY: [
		{ id: 'OFF_HELM', label: 'Expert Helmsman', post: 'cnc', stats: { iniativebonus: 5 },
			summary: '+5 initiative, wins ties, -1 on meteor chart' },
		{ id: 'OFF_SCAN', label: 'Expert Scanner', post: 'scanner',
			summary: 'May save 1 more EW point to spend in Firing Phase' },
		{ id: 'OFF_TECH', label: 'Expert Technician', post: 'reactor',
			summary: '+2 power, or +33% power from a destroyed system' },
		{ id: 'OFF_GENE', label: 'Expert Geneticist', post: 'cnc', stats: { forwardDefense: 2, sideDefense: 2, toHitBonus: -1 },
			summary: '-1 to hit with own weapons, +2 to be hit, enemies may ram this ship' },
		{ id: 'OFF_MWE', label: 'Matter Weapons Expert', post: 'cnc', needs: 'matterWeapon',
			summary: '+1 fire control and +1 damage for Matter weapons' },
		{ id: 'OFF_ENG', label: 'Expert Engineer', post: 'engine',
			summary: 'Repairs 1 critical per turn (a C&C critical on a d20 of 16+)' },
		{ id: 'OFF_SCAV', label: 'Expert Scavenger', post: 'engine',
			summary: 'Repairs 1 critical or 4 boxes per turn; once per game blocks up to 6 damage that would destroy the C&C, a reactor or a structure block' },
		{ id: 'OFF_DOG', label: 'Expert Dogfighter', post: 'craft', flight: true, stats: { iniativebonus: 5 },
			summary: '+5 initiative' },
		{ id: 'OFF_MSL', label: 'Expert Missileer', post: 'craft', flight: true, perMissile: 1, needs: 'missiles',
			summary: '+5 offensive bonus for missiles' },
		{ id: 'OFF_EVAD', label: 'Expert Evader', post: 'craft', flight: true, stats: { offensivebonus: -1 },
			summary: '-5 offensive bonus, -1 damage to hits on this flight' },
		{ id: 'EXP_MOTIV', label: 'Expert Motivator', post: 'craft', flight: true, stats: { critRollMod: -2 },
			summary: '-2 on dropout rolls' }
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
	     reactor  the biggest reactor by maxhealth - likewise
	     engine   the strongest engine by output - the pick Poor Crew's and ADV_ENG's lobby mirrors use
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
		if (post === 'reactor') {
			return this.strongest(ship, systems.filter(function (s) { return s && s.name === 'reactor'; }), 'maxhealth', -1000);
		}
		if (post === 'engine') {
			return this.strongest(ship, systems.filter(function (s) { return s && s.name === 'engine'; }), 'output', -1);
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
	     missiles      the flight carries missiles (the Expert Missileer) */
	passesNeeds: function passesNeeds(ship, officer) {
		if (!officer.needs) return true;
		if (officer.needs === 'matterWeapon') {
			var self = this;
			return this.systemsOf(ship).some(function (s) { return self.isMatterWeapon(s); });
		}
		if (officer.needs === 'missiles') return this.missilesPerCraft(ship) > 0;
		return false;
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
	   first bought in registry order, as Officers::pickPosts serves. */
	listFor: function listFor(ship) {
		if (!ship) return [];
		if (Array.isArray(ship.officers)) return ship.officers;
		if (!Array.isArray(ship.enhancementOptions)) return [];

		var list = [];
		for (var i = 0; i < this.REGISTRY.length; i++) {
			var officer = this.REGISTRY[i];
			if (Boolean(officer.flight) !== Boolean(ship.flight)) continue;
			if (!this.isBought(ship, officer.id)) continue;
			var post = this.findPost(ship, officer.post);
			if (!post || !this.passesNeeds(ship, officer)) continue;
			var entry = { id: officer.id, post: post.id };
			if (this.isPostDestroyed(ship, post)) {
				entry.out = 0; //pre-battle damage: he never serves (Officers::serves)
				entry.how = 'disabled';
			}
			list.push(entry);
			if (ship.flight) break;
		}
		return list;
	},

	//true when the list is the lobby's own derivation rather than something the server sent
	isDerived: function isDerived(ship) {
		return Boolean(ship) && !Array.isArray(ship.officers);
	},

	isBought: function isBought(ship, id) {
		for (var i = 0; i < ship.enhancementOptions.length; i++) {
			var row = ship.enhancementOptions[i];
			if (row && row[0] === id && (parseInt(row[2], 10) || 0) > 0) return true;
		}
		return false;
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

	/* Out on turn N = serves N, gone from N + 1; out at turn 0 (pre-battle damage) never serves.
	   ⚠️ MIRROR PAIR with Officers::serves (PHP). */
	serves: function serves(entry, turn) {
		if (entry.out === undefined || entry.out === null) return true;
		var out = parseInt(entry.out, 10);
		return out >= 1 && out >= (parseInt(turn, 10) || 0);
	},

	currentTurn: function currentTurn() {
		return (window.gamedata && gamedata.turn !== undefined) ? gamedata.turn : 0;
	},

	/* 'serving' | 'disabled' | 'killed' - what the insignia shows for one post: the BEST state of the
	   officers posted there, so any serving officer keeps it gold (D11). null with nobody posted. */
	postState: function postState(ship, systemId) {
		var entries = this.onPost(ship, systemId);
		if (entries.length === 0) return null;
		var turn = this.currentTurn();
		var state = 'killed';
		for (var i = 0; i < entries.length; i++) {
			if (this.serves(entries[i], turn)) return 'serving';
			if (entries[i].how !== 'killed') state = 'disabled';
		}
		return state;
	},

	/* "(disabled, turn 3)", "(killed, turn 3)", the pre-battle wording, or '' while he serves - unless a
	   serving Expert Scavenger has spent his once-per-game save: "(save used, turn 3)" (`used`, own team only). */
	stateText: function stateText(entry) {
		if (entry.out === undefined || entry.out === null) return entry.used ? '(save used, turn ' + entry.used + ')' : '';
		if (parseInt(entry.out, 10) < 1) return '(post destroyed before battle - he will not serve)';
		return '(' + (entry.how === 'killed' ? 'killed' : 'disabled') + ', turn ' + entry.out + ')';
	},

	//"Expert Helmsman (disabled, turn 3)" - the insignia's title, one officer per line
	describePost: function describePost(ship, systemId) {
		var self = this;
		return this.onPost(ship, systemId).map(function (entry) {
			var state = self.stateText(entry);
			return self.label(entry.id) + (state ? ' ' + state : '');
		}).join('\n');
	},

	//Is this officer aboard and serving now? ⚠️ MIRROR PAIR with Officers::isActive (PHP).
	isActive: function isActive(ship, id) {
		var list = this.listFor(ship);
		for (var i = 0; i < list.length; i++) {
			if (list[i].id === id) return this.serves(list[i], this.currentTurn());
		}
		return false;
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

	/* "Officers (2)" - the one line the ship window's gold Enhancements block shows for them (user request
	   2026-10-11: a count, like "System Enhancements (n)", the detail living on each post's tooltip). Own
	   team only, through listFor; null with none aboard.
	   A FLIGHT in the LOBBY names him instead ("Expert Dogfighter"; user request 2026-10-11): a flight
	   carries one officer at most (D9), so a count says less than his name. In game it stays a count. */
	summaryLine: function summaryLine(ship) {
		var list = this.listFor(ship);
		if (list.length === 0) return null;
		if (ship.flight && window.gamedata && gamedata.gamephase === -2) return this.label(list[0].id);
		return 'Officers (' + list.length + ')';
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
	   count); `extra` is perCraftExtra(ship, enhID), worked out once by the caller.
	   ⚠️ MIRROR PAIR with Officers::flightPrice (PHP). */
	flightCost: function flightCost(enhID, count, enhCost, size, extra, standard) {
		var cost = Number(enhCost) || 0;
		size = Number(size) || 1;
		if (!this.isFighterOfficer(enhID)) return cost * size;
		if ((parseInt(count, 10) || 0) < 1) return 0;
		standard = Number(standard) || 6;
		return Math.ceil(cost * size / standard) + (Number(extra) || 0) * size;
	},

	/* The buy dialog's price note for a fighter officer's row: "29pts per 6 craft", "40pts per 6 craft + 6pts
	   per craft"; a single superheavy's is just its price. null for anything else. */
	priceNote: function priceNote(ship, enhancement) {
		if (!this.isFighterOfficer(enhancement[0])) return null;
		var standard = this.standardFlight(ship);
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

	/* A ship field as a window should show it: iniativebonus, forwardDefense, sideDefense, toHitBonus, and a
	   flight's offensivebonus. ⚠️ MIRROR PAIR with Officers::apply's stat changes (PHP) -
	   enhancementsDifferential.js compares the two through this function. */
	displayStat: function displayStat(ship, field) {
		var value = Number(ship && ship[field]) || 0;
		var serving = this.servingDerived(ship);
		for (var i = 0; i < serving.length; i++) {
			var officer = this.get(serving[i].id);
			if (officer && officer.stats && officer.stats[field]) value += officer.stats[field];
		}
		return value;
	},

	displayIniativeBonus: function displayIniativeBonus(ship) {
		return this.displayStat(ship, 'iniativebonus');
	},

	/* Expert Technician, in the lobby: what he adds to THIS reactor's output - 0 unless it is his post.
	   The best of +2 and a third (rounded down) of the largest powerReq among the destroyed systems, which
	   in the lobby can only be pre-battle damage. power.js getReactorPower adds it to the balance.
	   ⚠️ MIRROR PAIR with Officers::technicianBonus (PHP). */
	reactorBonus: function reactorBonus(ship, system) {
		if (!system) return 0;
		var serving = this.servingDerived(ship);
		for (var i = 0; i < serving.length; i++) {
			if (serving[i].id !== 'OFF_TECH' || String(serving[i].post) !== String(system.id)) continue;
			var largest = 0;
			var self = this;
			this.systemsOf(ship).forEach(function (s) {
				if (!s || s === system || String(s.id) === String(system.id)) return;
				var req = parseInt(s.powerReq, 10) || 0;
				if (req > largest && self.isPostDestroyed(ship, s)) largest = req;
			});
			return Math.max(2, Math.floor(largest / 3));
		}
		return 0;
	},

	/* Matter Weapons Expert, in the lobby: this weapon's fire control in its current mode, +1 on every
	   non-null entry when it is a Matter mode and he serves (null = cannot target that size, and stays so).
	   ⚠️ MIRROR PAIR with Officers::applyMatterExpert (PHP). */
	displayFireControl: function displayFireControl(ship, system) {
		var fc = system ? system.fireControl : null;
		if (!Array.isArray(fc) || !this.isMatterWeapon(system) || !this.isMatterMode(system, system.firingMode)) return fc;
		var serving = this.servingDerived(ship);
		for (var i = 0; i < serving.length; i++) {
			if (serving[i].id !== 'OFF_MWE') continue;
			return fc.map(function (v) { return (v === null || v === undefined) ? v : (parseInt(v, 10) || 0) + 1; });
		}
		return fc;
	},

	/* SystemInfo's one hook into a system's data rows. The weapon tooltip quotes fire control only through
	   data["Fire control (fighter/med/cap)"], baked into the static blueprint, so the lobby rewrites that
	   row as it is shown. In game the server's isModified fire control already rebuilt it. */
	DATA_KEY_FIRECONTROL: 'Fire control (fighter/med/cap)',
	adjustDataDisplay: function adjustDataDisplay(ship, system, key, value) {
		if (key !== this.DATA_KEY_FIRECONTROL || !system) return value;
		var fc = this.displayFireControl(ship, system);
		if (fc === system.fireControl) return value;
		if (typeof system.translateFCtoD100txt === 'function') return system.translateFCtoD100txt(fc);
		return fc.map(function (v) { return (v === null || v === undefined) ? '-' : String(v * 5); }).join('/');
	}
};
