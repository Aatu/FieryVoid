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

	/* ⚠️ MIRROR of Officers::$registry (PHP): ids, labels, posts, `needs`, `flight`, `perMissile` and the placed
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
			summary: 'Repairs 1 critical or 4 boxes per turn; once per game blocks up to 6 damage that would destroy the C&C, a reactor, a scanner, an engine or a structure block' },
		{ id: 'OFF_NAV', label: 'Expert Navigator', post: 'cnc', adjust: 'navigatorStat',
			summary: 'Turn cost one level lower, pivot and roll cost -1, accel/decel cost -25%' },
		{ id: 'OFF_JUMP', label: 'Expert Jump Officer', post: 'jump',
			summary: 'Jump points opened 1 hex further and held 1 turn longer; jump failure chance -20%; exits scatter less (-1 on the chart, 60 degrees of facing shift cancelled); arrival initiative penalty -20' },
		/* PLACED officers (Stage 8): posted in the weapon the player buys him on, from that system's own menu
		   (a per-system row - systemEnhancements.js), so they have no `post`. `sameAs`: the ship-level officer he
		   IS at another post (one or the other); `anyDamage`: disabled by any damage to his weapon.
		   Stage 10: `movable` - may be moved to another post in Initial Orders (the Matter Weapons Expert between
		   the C&C and his Matter weapons, as one man); `abilities` - the Gunner's once-per-battle three;
		   `reload` - his weapon reloads a turn faster. */
		{ id: 'OFF_GUN', label: 'Expert Gunner', placed: true, movable: true, abilities: true,
			summary: '+1 fire control and +1 damage per die (no die above its maximum) for this weapon. Once per battle each: a turn off the reload after a shot, a Piercing shot (no penalty if the weapon may already pierce), and +15% on a called shot' },
		{ id: 'OFF_MWEW', label: 'Matter Weapons Expert', placed: true, sameAs: 'OFF_MWE', anyDamage: true, movable: true, reload: true,
			summary: '+1 fire control and +1 damage for this Matter weapon, which reloads a turn faster; disabled by any damage to it, killed if it is destroyed' },
		{ id: 'OFF_DOG', label: 'Expert Dogfighter', post: 'craft', flight: true, stats: { iniativebonus: 5 },
			summary: '+5 initiative' },
		{ id: 'OFF_MSL', label: 'Expert Missileer', post: 'craft', flight: true, perMissile: 1, needs: 'missiles',
			summary: '+5 offensive bonus for missiles' },
		{ id: 'OFF_EVAD', label: 'Expert Evader', post: 'craft', flight: true, stats: { offensivebonus: -1 },
			summary: '-5 offensive bonus, -1 damage to hits on this flight' },
		{ id: 'EXP_MOTIV', label: 'Expert Motivator', post: 'craft', flight: true, stats: { critRollMod: -2 },
			summary: '-2 on dropout rolls' },
		{ id: 'OFF_COOR', label: 'Expert Coordinator', post: 'craft', flight: true,
			summary: 'Combat pivots cost half the thrust and give no to-hit penalty, unless they turn the flight a full 180 degrees (-5)' }
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
	     jump     the first jump drive that opens jump points: a legacy drive's range is 0 (markLegacy), the
	              lobby JUMP_ACC case's own test. The one gate drive (JumpgateCapital) is on terrain, which
	              carries no officers.
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
		if (post === 'jump') {
			for (var j = 0; j < systems.length; j++) {
				if (systems[j] && systems[j].name === 'jumpEngine' && Number(systems[j].range) > 0) return systems[j];
			}
			return null;
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
			if (officer.placed) continue; //track 2: placedEntries below
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
		if (!ship.flight) list = list.concat(this.placedEntries(ship, list));
		return list;
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
	   (registry order, then system id). Pre-battle damage that destroys his weapon refunds him (D11, the row
	   is gone); damage that does not still disables the Matter expert from the start (anyDamage).
	   ⚠️ MIRROR PAIR with Officers::pickPlacedPosts / ::keepPlacedRows (PHP). */
	placedEntries: function placedEntries(ship, shipLevel) {
		var rows = this.placedRows(ship);
		if (!rows.length) return [];
		var self = this;
		var aboard = {};
		(shipLevel || []).forEach(function (entry) { aboard[entry.id] = true; });
		var hosted = {}, count = {}, out = [];
		this.REGISTRY.forEach(function (officer) {
			if (!officer.placed || (officer.sameAs && aboard[officer.sameAs])) return;
			rows.filter(function (row) { return row[0] === officer.id; })
				.map(function (row) { return { row: row, system: self.systemById(ship, row[6]) }; })
				.filter(function (c) { return c.system && (!c.row[7] || String(c.system.name) === String(c.row[7])); })
				.sort(function (a, b) { return parseInt(a.system.id, 10) - parseInt(b.system.id, 10); })
				.forEach(function (c) {
					var id = parseInt(c.system.id, 10);
					if (hosted[id] || (count[officer.id] || 0) >= self.placedLimit(ship, officer.id)) return;
					hosted[id] = true;
					count[officer.id] = (count[officer.id] || 0) + 1;
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
			return 'The ' + this.label(officer.sameAs) + ' is posted at the C&C (bought in the Officers section). A ship carries him in one place: remove him there first.';
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
				+ '. A ship carries him in one place: remove him there (that system\'s menu in the ship window) to post him at the C&C.';
		}
		return null;
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

	/* Out on turn N = serves N, gone from N + 1; out at turn 0 (pre-battle damage) never serves. Nor on the turn
	   he moves to another post (`moving`, sent for the current turn only - Stage 10).
	   ⚠️ MIRROR PAIR with Officers::serves (PHP). */
	serves: function serves(entry, turn) {
		if (entry.moving !== undefined && entry.moving !== null) return false;
		if (entry.out === undefined || entry.out === null) return true;
		var out = parseInt(entry.out, 10);
		return out >= 1 && out >= (parseInt(turn, 10) || 0);
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
		return '(' + (entry.how === 'killed' ? 'killed' : 'disabled') + ', turn ' + entry.out + ')';
	},

	//"(used: called shot, turn 3; Piercing shot, turn 4)" - the Gunner's spent once-per-battle abilities, or ''
	ABILITY_NAMES: { reload: 'faster reload', pierce: 'Piercing shot', called: 'called shot' },
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
		return lines.join('\n');
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
		return (called && open('called')) ? this.GUNNER_CALLED_BONUS : 0;
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

	//where he may be moved: the server's `targets`, less posts another officer has been ordered to this turn
	destinations: function destinations(ship, entry) {
		if (!Array.isArray(entry.targets)) return [];
		var self = this;
		var taken = this.listFor(ship).filter(function (other) { return other !== entry; })
			.map(function (other) { return self.movingTo(ship, other); })
			.filter(function (to) { return to !== null; }).map(String);
		return entry.targets.filter(function (id) { return taken.indexOf(String(id)) === -1; });
	},

	systemName: function systemName(ship, systemId) {
		var s = this.systemById(ship, systemId);
		return (s ? (s.displayName || s.name) : 'system') + ' #' + systemId;
	},

	//"(moving to Heavy Laser #12 - serves there from turn 5)", or '' while he is not moving
	moveText: function moveText(ship, entry) {
		var to = this.movingTo(ship, entry);
		if (to === null) return '';
		var pending = (entry.moving === undefined || entry.moving === null);
		return '(' + (pending ? 'ordered to ' : 'moving to ') + this.systemName(ship, to) + ' - serves there from turn ' + (this.currentTurn() + 1) + ')';
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
			this.setTransfer(ship, entry, system.id);
			this.notice(this.label(entry.id) + ' ordered to ' + this.systemName(ship, system.id) + ': he serves nowhere for the rest of this turn.');
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

	/* A ship field as a window should show it: iniativebonus, forwardDefense, sideDefense, toHitBonus, a
	   flight's offensivebonus, and the Navigator's turncost, pivotcost, rollcost and accelcost. ⚠️ MIRROR
	   PAIR with Officers::apply's stat changes (PHP) - enhancementsDifferential.js compares the two through
	   this function. */
	displayStat: function displayStat(ship, field) {
		var value = Number(ship && ship[field]) || 0;
		var serving = this.servingDerived(ship);
		for (var i = 0; i < serving.length; i++) {
			var officer = this.get(serving[i].id);
			if (!officer) continue;
			if (officer.stats && officer.stats[field]) value += officer.stats[field];
			if (officer.adjust) value = this[officer.adjust](field, value);
		}
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
	   ⚠️ MIRROR PAIR with Officers::technicianBonus (PHP). */
	reactorBonus: function reactorBonus(ship, system) {
		if (!system) return 0;
		var serving = this.servingDerived(ship);
		for (var i = 0; i < serving.length; i++) {
			if (serving[i].id !== 'OFF_TECH' || String(serving[i].post) !== String(system.id)) continue;
			var bonus = 2;
			var self = this;
			this.systemsOf(ship).forEach(function (s) {
				if (!s || s === system || String(s.id) === String(system.id)) return;
				var req = parseInt(s.powerReq, 10) || 0;
				if (req > 0 && self.isPostDestroyed(ship, s)) bonus += Math.floor(req / 3);
			});
			return bonus;
		}
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
	       it. A Gunner's +1 replaces the Matter expert's on his own weapon (user ruling 2026-10-16), never adds.
	   Gunsights is already in system.fireControl (systemEnhancements.apply), so the two show stacked.
	   ⚠️ MIRROR PAIR with Officers::applyGunner / applyMatterExpert / applyWeaponMatterExpert (PHP). */
	displayFireControl: function displayFireControl(ship, system) {
		var fc = system ? system.fireControl : null;
		if (!Array.isArray(fc)) return fc;
		if (!this.fireControlRaise(this.servingDerived(ship), system)) return fc;
		return fc.map(function (v) { return (v === null || v === undefined) ? v : (parseInt(v, 10) || 0) + 1; });
	},

	//1 when the officers in `serving` raise this weapon's fire control in its current mode (displayFireControl's rule), else 0
	fireControlRaise: function fireControlRaise(serving, system) {
		var on = function (id) {
			return serving.some(function (entry) { return entry.id === id && String(entry.post) === String(system.id); });
		};
		if (on('OFF_GUN')) return 1;
		return (this.isMatterWeapon(system) && this.isMatterMode(system, system.firingMode)
			&& (on('OFF_MWEW') || serving.some(function (entry) { return entry.id === 'OFF_MWE'; }))) ? 1 : 0;
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
		return this.fireControlRaise(serving, weapon) - this.fireControlRaise(staying, weapon);
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

	/* SystemInfo's one hook into a system's data rows (and its Special text). The weapon tooltip quotes fire
	   control only through data["Fire control (fighter/med/cap)"] - in game too, less an officer just ordered to
	   move (pendingMoveFireControl, user request 2026-10-16) - and a jump drive its range through
	   data["Range"] and the Special sentence "target a hex within N hexes" - all baked into the static
	   blueprint, so the lobby rewrites them as they are shown. N is the drive's range as the lobby has it
	   (after a Jump Accelerator's +2, which lobbyEnhancements writes into both). In game the server's
	   isModified fire control already rebuilt the one, and its payload the other. */
	DATA_KEY_FIRECONTROL: 'Fire control (fighter/med/cap)',
	adjustDataDisplay: function adjustDataDisplay(ship, system, key, value) {
		if (!system) return value;
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
