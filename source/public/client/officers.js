'use strict';

/* OFFICERS (OFFICERS_PLAN.md, repo root) - the client half: the labels and one-line effects, the post
   a lobby officer would take, and the ONE list every insignia, tooltip line and ship-level listing
   reads (§7, "two tracks drift").

   WHERE THE LIST COMES FROM:
     - in GAME, and for anything the server built: ship.officers, sent by Officers::addForJson to the
       owner's team only (and to everybody once the game is over). An enemy's ship simply has none, so
       nothing on this side ever compares userids - the isRevealedToCurrentViewer pattern.
     - in the LOBBY, before the server has seen the fleet: derived from the bought OFF_* rows, the way
       Officers::pickPosts derives it. Only the buyer's own browser holds those counts; server payloads
       never carry enhancementOptions, and a static blueprint's rows are all 0.
   Out of action is decided SERVER-SIDE from the full damage history and the kill notes (§4.4); the
   client only reads out/how. The one exception is the lobby's pre-battle damage preview: a post the
   player destroys there leaves its officer disabled from the start (§4.9), as the server will rule. */
window.officers = {

	ID_PREFIX: 'OFF_',

	/* ⚠️ MIRROR of Officers::$registry (PHP): ids, labels, posts and `needs`, in the SAME order - the
	   order every list here is shown in. `summary` is the effect line the System Info tooltip prints
	   (D12). `stats` is what the officer adds to ship fields while he serves (Officers::apply), which
	   the lobby adds at READ time (displayStat); his other effects have their own previews below
	   (reactorBonus, displayFireControl) or none the lobby can show (the Scanner's saved EW). */
	REGISTRY: [
		{ id: 'OFF_HELM', label: 'Expert Helmsman', post: 'cnc', stats: { iniativebonus: 5 },
			summary: '+5 initiative, wins ties, -1 on meteor chart' },
		{ id: 'OFF_SCAN', label: 'Expert Scanner', post: 'scanner',
			summary: 'may save 1 more EW point to spend in Firing Phase' },
		{ id: 'OFF_TECH', label: 'Expert Technician', post: 'reactor',
			summary: '+2 power, or a +33% power from largest destroyed system\'s power' },
		{ id: 'OFF_GENE', label: 'Expert Geneticist', post: 'cnc', stats: { forwardDefense: 2, sideDefense: 2, toHitBonus: -1 },
			summary: '-1 to hit with own weapons, +2 to be hit, enemies may ram this ship' },
		{ id: 'OFF_MWE', label: 'Matter Weapons Expert', post: 'cnc', needs: 'matterWeapon',
			summary: '+1 fire control and +1 damage for Matter weapons' }
	],

	isOfficerId: function isOfficerId(enhID) {
		return typeof enhID === 'string' && enhID.indexOf(this.ID_PREFIX) === 0;
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
	   ⚠️ D2: the server picks from BLUEPRINT values, before any enhancement moves an output, but a lobby
	   fleet ship has already had lobbyEnhancements.apply - Poor Crew has taken a point off its strongest
	   scanner. So outputs are read off the store's pristine copy of the hull when the page has one. */
	findPost: function findPost(ship, post) {
		if (!ship || !ship.systems) return null;
		var systems = this.systemsOf(ship);
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

	/* An officer's own test beyond having the post. ⚠️ MIRROR PAIR with Officers::isEligible's `needs`.
	     matterWeapon  the hull carries a weapon with a Matter mode (the Matter Weapons Expert) */
	passesNeeds: function passesNeeds(ship, officer) {
		if (!officer.needs) return true;
		if (officer.needs === 'matterWeapon') {
			var self = this;
			return this.systemsOf(ship).some(function (s) { return self.isMatterWeapon(s); });
		}
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
	   which of the two sources answers. Flights carry none before the fighter framework (Stage 4). */
	listFor: function listFor(ship) {
		if (!ship || ship.flight) return [];
		if (Array.isArray(ship.officers)) return ship.officers;
		if (!Array.isArray(ship.enhancementOptions)) return [];

		var list = [];
		for (var i = 0; i < this.REGISTRY.length; i++) {
			var officer = this.REGISTRY[i];
			if (!this.isBought(ship, officer.id)) continue;
			var post = this.findPost(ship, officer.post);
			if (!post || !this.passesNeeds(ship, officer)) continue;
			var entry = { id: officer.id, post: post.id };
			if (this.isPostDestroyed(ship, post)) {
				entry.out = 0; //pre-battle damage: he never serves (Officers::serves)
				entry.how = 'disabled';
			}
			list.push(entry);
		}
		return list;
	},

	//true when the list is the lobby's own derivation rather than something the server sent
	isDerived: function isDerived(ship) {
		return Boolean(ship) && !ship.flight && !Array.isArray(ship.officers);
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

	//"(disabled, turn 3)", "(killed, turn 3)", the pre-battle wording, or '' while he serves
	stateText: function stateText(entry) {
		if (entry.out === undefined || entry.out === null) return '';
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

	/* A ship field as a window should show it: iniativebonus, forwardDefense, sideDefense, toHitBonus.
	   ⚠️ MIRROR PAIR with Officers::apply's stat changes (PHP) - enhancementsDifferential.js compares the
	   two through this function. */
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
