"use strict";

/*==========================================================================
  Pre-battle damage & fleet damage persistence — client state + preview.
  Design: PREBATTLE_DAMAGE_PLAN.md. PHP twin: source/server/model/PreBattleDamage.php.

  Holds the one compact per-ship payload that travels everywhere:

      ship.preBattleDamage = {
        sys: { "<systemid>": { d: <int>, k: 0|1, c: {<critClass>: <count>}, p: {<critClass>: <int>} }, ... },
        ftr: { "<ordinal>":  { d: <int>, k: 0|1, c: {<critClass>: <count>}, p: {<critClass>: <int>} }, ... }
      }

  `c` counts criticals; `p` carries the integer magnitude of the few classes that keep
  it in the crit's own param instead of in the number of crits (PARAM_CRITICALS). A
  sibling map rather than a richer `c` value, so every consumer of `c` keeps counting
  and `p` is simply absent for everything else. `p` never outlives `c`.

  ORDINARY SHIPS are keyed by systemid (construction-order ids, stable across the
  static blueprint / buy-POST reconstruction / game load, and enhancements never add
  systems). FLIGHTS are keyed by fighter ORDINAL 1..flightSize, because in the lobby
  a flight is ONE sample fighter plus a number — the N fighters are minted server-side
  by populate(), so per-fighter system ids do not exist here at all.

  Loaded on BOTH gamelobby.php (authoring + preview) and game.php (summariseShip, for
  "Save Current Fleet").
  ==========================================================================*/

window.battleDamage = {

	TURN: 0,
	DAMAGECLASS: 'PreGame',
	PUBNOTES: 'Pre-battle damage',
	MAX_CRIT_COUNT: 10,
	MAX_CRIT_PARAM: 100,

	KIND_SYSTEM: 0,
	KIND_FIGHTER: 1,

	/* PARAM-CARRYING CRITICALS. MIRRORS PreBattleDamage::$paramCriticals in PHP - add or
	   remove a class in BOTH, exactly like the deny list below.

	   These keep their MAGNITUDE in each crit's `param` rather than in the number of
	   crits: one DamageReductionReduced is a whole 1d10 of Phased Gravitic Torpedo
	   phasing, and every consumer SUMS the params (ShipSystem::sumCriticalParam server
	   side, the paramTotal loops in systems.js / SystemInfo.js here) instead of counting
	   the crits. So the N crits on a system collapse to ONE saved row carrying the TOTAL:
	   identical for every consumer, and it gives the lobby a single number to edit.

	   `label` mirrors PARAM_SUM_CRIT_DESCRIPTIONS in UI/reactJs/system/SystemInfo.js -
	   the server's critDesc map is keyed by CLASS and so cannot describe these (the same
	   class carries a different param on every system), which is why the wording lives
	   here. `max` bounds the editor; the server clamps to MAX_CRIT_PARAM regardless. */
	PARAM_CRITICALS: {
		DamageReductionReduced: { label: 'Damage reduction reduced by', max: 100 }
	},

	isParamCritical: function isParamCritical(type) {
		return Object.prototype.hasOwnProperty.call(battleDamage.PARAM_CRITICALS, type || '');
	},

	/* A stored param: a positive integer within bounds, or 0 for "none". Mirrors
	   PreBattleDamage::sanitiseParam. */
	sanitiseParam: function sanitiseParam(value, type) {
		var param = parseInt(value, 10);
		if (!(param > 0)) return 0;
		var spec = battleDamage.PARAM_CRITICALS[type];
		var max = Math.min(battleDamage.MAX_CRIT_PARAM, (spec && spec.max) || battleDamage.MAX_CRIT_PARAM);
		return Math.min(param, max);
	},

	/* The label for one carried critical: param classes read their magnitude out of the
	   payload, everything else uses the server's per-class description (falling back to
	   the raw class name, which is what SystemInfo does too). */
	critLabel: function critLabel(type, critDesc, param) {
		var spec = battleDamage.PARAM_CRITICALS[type];
		if (spec) return spec.label + ' ' + (parseInt(param, 10) || 0);
		return (critDesc && critDesc[type]) || type;
	},

	/* ⭐ THE DENY LIST — criticals that are NEVER carried between battles, by name.
	   MIRRORS PreBattleDamage::$denyList in PHP.

	   TO BAN a critical: add its class name here AND to $denyList in
	   source/server/model/PreBattleDamage.php. TO ALLOW one again: remove it from BOTH.
	   The PHP list is the one that actually enforces (it is the security boundary and it
	   re-validates everything); this one stops the client offering to save something the
	   server would silently drop, so a name in only one place is a bug in the other.

	   NOT on this list, because they are caught generically instead:
	     - `forInfo` markers (every marine/boarding critical forces forInfo = true in its
	       own constructor) - refused by isValidCriticalType server-side and by
	       summariseCriticals here. They ARE named below as well, belt and braces.
	     - TEMPORARY criticals (oneturn / self-expiring) - those are not banned, they are
	       OPTIONAL: the "save temporary critical effects" checkbox decides, and the server
	       stamps the ones you keep at turn 1 so they apply during the first turn of the
	       next battle rather than expiring as it begins. */
	EXCLUDED_CRITICALS: [
		/* NOTE 2026-08-08: ReducedArcs and DamageReductionReduced were banned here as
		   "param-carrying". ReducedArcs carries no param at all - the restricted arc lives
		   on the weapon instance in the ship blueprint and the crit is only the flag - and
		   DamageReductionReduced's param is now stored (PARAM_CRITICALS above). Both are
		   permanent wounds and both are now carried between battles. */
		//weapon-cooldown marker carrying a real turnend
		'ForcedOfflineForTurns',
		//Hangar Ops / state markers
		'DockedFighter', 'SplitLaunchedFighter', 'LaunchedThisTurn',
		'LCVLaunchedThisTurn', 'HangarOperations', 'OrbitalRepairing',
		'DisengagedFighter', 'ShadowFighterCutOff', 'LimpetBoreTravelling',
		//marine / boarding actions - they belong to the battle being fought, not to the
		//fleet: no marines are aboard when the next one starts
		'Sabotage', 'SabotageElite', 'CaptureShip', 'CaptureShipElite',
		'RescueMission', 'RescueMissionElite', 'DefenderLost',
		//momentary combat effect that is neither forInfo nor self-expiring, so nothing
		//generic catches it - one exchange of fire, not a campaign
		'tmphitreduction'
	],

	/* Is this critical INSTANCE a temporary one - in effect for a single turn and then
	   gone? Mirrors PreBattleDamage::isTransientCriticalType, but measured off the live
	   instance the server sent rather than off a probe: `oneturn` and `turnend` both ride
	   the gamedata payload.
	   These are what the "save temporary critical effects" checkbox governs. */
	isTransientCritical: function isTransientCritical(crit) {
		if (!crit) return false;
		return Boolean(crit.oneturn) || Boolean(crit.turnend);
	},

	/* ---------------------------------------------------------------- *
	 *  State
	 * ---------------------------------------------------------------- */

	/* The ship's payload, lazily created. Always returns a live object.

	   ⚠️ EVERY payload that crosses PHP arrives as a JSON ARRAY when it is EMPTY, because
	   PHP's array() encodes as []. That includes `BaseShip::$preBattleDamage = array()` on
	   every static ship blueprint the lobby clones, and Manager::loadSavedFleet's payload
	   for a fleet with no damage. An Array with named properties works perfectly in memory
	   - payload.sys = {...} reads back fine, so the lobby PREVIEW rendered correctly - but
	   JSON.stringify SERIALISES ONLY THE INDEXED ELEMENTS of an array, so `sys`/`ftr` were
	   silently dropped from the buy POST and the saved-fleet POST and every authored point
	   of damage vanished on submit (user report 2026-08-08, both save paths).
	   Normalise the payload itself, then its two buckets: ordinary-ship system ids start at
	   0 (BaseShip::addSystem uses sizeof($systems)), so a bucket whose keys run 0,1,2… comes
	   back from PHP as an array too. */
	get: function get(ship) {
		if (!ship) return {};
		if (!ship.preBattleDamage || typeof ship.preBattleDamage !== 'object'
			|| Array.isArray(ship.preBattleDamage)) {
			ship.preBattleDamage = battleDamage.toPlainObject(ship.preBattleDamage);
		}
		['sys', 'ftr'].forEach(function (bucketName) {
			if (Array.isArray(ship.preBattleDamage[bucketName])) {
				ship.preBattleDamage[bucketName] =
					battleDamage.toPlainObject(ship.preBattleDamage[bucketName]);
			}
		});
		return ship.preBattleDamage;
	},

	/* Array (or anything else) -> plain object, keyed by index, holes dropped. Named
	   properties already hanging off an array are carried across too, so calling this on a
	   payload that has ALREADY been written to in memory loses nothing. */
	toPlainObject: function toPlainObject(value) {
		var out = {};
		if (!value || typeof value !== 'object') return out;

		for (var key in value) {
			if (!Object.prototype.hasOwnProperty.call(value, key)) continue;
			if (value[key] === null || value[key] === undefined) continue;
			out[key] = value[key];
		}

		return out;
	},

	/* Read-only peek — never creates the object (so isEmpty tests stay side-effect free). */
	peek: function peek(ship) {
		return (ship && ship.preBattleDamage && typeof ship.preBattleDamage === 'object')
			? ship.preBattleDamage : null;
	},

	bucketName: function bucketName(kind) {
		return (kind === battleDamage.KIND_FIGHTER || kind === 'ftr') ? 'ftr' : 'sys';
	},

	getEntry: function getEntry(ship, kind, ref) {
		var payload = battleDamage.peek(ship);
		if (!payload) return null;
		var bucket = payload[battleDamage.bucketName(kind)];
		if (!bucket) return null;
		return bucket[String(ref)] || null;
	},

	/* Merge {d, k} into an entry, PRESERVING any criticals it already carries.
	   Pass d = 0 and k = 0 to clear the damage half. An entry left with nothing at
	   all is removed, and an empty bucket with it. */
	setEntry: function setEntry(ship, kind, ref, patch) {
		var payload = battleDamage.get(ship);
		var bucketName = battleDamage.bucketName(kind);
		var bucket = payload[bucketName] || (payload[bucketName] = {});
		var key = String(ref);
		var entry = bucket[key] || {};

		if (patch && patch.hasOwnProperty('d')) {
			var d = parseInt(patch.d, 10);
			if (!(d > 0)) delete entry.d; else entry.d = d;
		}
		if (patch && patch.hasOwnProperty('k')) {
			if (patch.k) entry.k = 1; else delete entry.k;
		}

		if (Object.keys(entry).length === 0) {
			delete bucket[key];
		} else {
			bucket[key] = entry;
		}

		if (Object.keys(bucket).length === 0) delete payload[bucketName];
		return entry;
	},

	setSystem: function setSystem(ship, systemid, entry) {
		return battleDamage.setEntry(ship, battleDamage.KIND_SYSTEM, systemid, entry);
	},

	/* A FIGHTER IS ONLY EVER DAMAGED, NEVER DESTROYED. A dead fighter has no
	   representation in B5W other than "not in the flight", and flightSize already says
	   that - it is freely editable in the lobby, and Part 2's in-game save records only
	   the SURVIVING craft. So damage caps one point short of the fighter's structure and
	   `k` is forced off; the PHP twin (PreBattleDamage::sanitise) does the same. */
	setFighter: function setFighter(ship, ordinal, entry) {
		var cap = Math.max(0, battleDamage.fighterMaxHealth(ship) - 1);
		var patch = { k: 0 };
		if (entry && entry.hasOwnProperty('d')) {
			patch.d = Math.min(cap, parseInt(entry.d, 10) || 0);
		}
		return battleDamage.setEntry(ship, battleDamage.KIND_FIGHTER, ordinal, patch);
	},

	/* REPLACE a whole entry (a deep copy of it), criticals included, or remove it when
	   given nothing. Used by the fighter menu's "apply to all" so propagation copies the
	   ENTIRE entry rather than named d/k fields - when lobby crit authoring (§11) lands,
	   criticals propagate too with no change here. */
	setWholeEntry: function setWholeEntry(ship, kind, ref, entry) {
		var payload = battleDamage.get(ship);
		var bucketName = battleDamage.bucketName(kind);
		var key = String(ref);

		if (!entry || Object.keys(entry).length === 0) {
			if (payload[bucketName]) {
				delete payload[bucketName][key];
				if (Object.keys(payload[bucketName]).length === 0) delete payload[bucketName];
			}
			return null;
		}

		var bucket = payload[bucketName] || (payload[bucketName] = {});
		bucket[key] = JSON.parse(JSON.stringify(entry));
		return bucket[key];
	},

	/* §11 SEAM. Writes the `c` (and `p`) keys — the ONLY way criticals enter a payload
	   client-side, so the preview, the buy POST and the saved-fleet write all follow from
	   one place. A lobby crit picker would call exactly this and nothing else.
	   critMap  = {critClass: count}; an empty/falsy map clears the criticals half.
	   paramMap = {critClass: int} for the PARAM_CRITICALS classes only. A param class
	   with no usable param is dropped: its magnitude IS the effect, so storing it at 0
	   would carry a wound that does nothing. */
	setCriticals: function setCriticals(ship, kind, ref, critMap, paramMap) {
		var payload = battleDamage.get(ship);
		var bucketName = battleDamage.bucketName(kind);
		var bucket = payload[bucketName] || (payload[bucketName] = {});
		var key = String(ref);
		var entry = bucket[key] || {};

		var clean = {};
		var params = {};
		for (var type in (critMap || {})) {
			if (!critMap.hasOwnProperty(type)) continue;
			if (battleDamage.EXCLUDED_CRITICALS.indexOf(type) !== -1) continue;
			var n = parseInt(critMap[type], 10);
			if (!(n > 0)) continue;

			if (battleDamage.isParamCritical(type)) {
				//Collapsed to ONE crit carrying the total - the effect is the sum, so the
				//count is meaningless and the editor edits the param instead.
				var param = battleDamage.sanitiseParam(paramMap && paramMap[type], type);
				if (!param) continue;
				clean[type] = 1;
				params[type] = param;
			} else {
				clean[type] = Math.min(n, battleDamage.MAX_CRIT_COUNT);
			}
		}

		if (Object.keys(clean).length) entry.c = clean; else delete entry.c;
		if (Object.keys(params).length) entry.p = params; else delete entry.p;

		if (Object.keys(entry).length === 0) {
			delete bucket[key];
		} else {
			bucket[key] = entry;
		}

		if (Object.keys(bucket).length === 0) delete payload[bucketName];
		return entry;
	},

	/* ---------------------------------------------------------------- *
	 *  Removed-critical memory (editor only, never submitted)
	 * ---------------------------------------------------------------- */

	/* Which critical classes the editor has SHOWN for a damage target, in first-seen
	   order — including the ones the player has since dialled down to nothing.

	   Why it exists: dropping a critical to 0 takes it out of the payload, and a row that
	   vanishes the moment you touch it cannot be undone without reloading the whole fleet.
	   The editor keeps drawing an empty row for a remembered class so it can be put back
	   (user report 2026-08-08).

	   Why it lives on the SHIP: not in the payload, which has to keep meaning exactly one
	   thing (what this unit carries into battle) - and not on the React component, which
	   is torn down every time the menu closes, so "I changed my mind" would only work
	   while the menu happened to still be open. Never POSTed: construcGamedata and
	   constructSavedShips copy `preBattleDamage` and nothing else. A fleet reload or a
	   lobby Edit builds a new ship object and so starts clean, which is right - the
	   memory is about an editing session, not about the fleet. */
	critMemory: function critMemory(ship, kind, ref) {
		if (!ship) return [];
		if (!ship.preBattleCritSeen || typeof ship.preBattleCritSeen !== 'object') {
			ship.preBattleCritSeen = {};
		}
		var key = battleDamage.bucketName(kind) + ':' + ref;
		if (!Array.isArray(ship.preBattleCritSeen[key])) ship.preBattleCritSeen[key] = [];
		return ship.preBattleCritSeen[key];
	},

	/* Append any class not seen before, preserving first-seen order, and hand the whole
	   ordered list back. */
	rememberCriticals: function rememberCriticals(ship, kind, ref, types) {
		var seen = battleDamage.critMemory(ship, kind, ref);
		(types || []).forEach(function (type) {
			if (type && seen.indexOf(type) === -1) seen.push(type);
		});
		return seen;
	},

	/* Drop everything. Used when a flight's size changes (ordinals beyond the new size
	   would silently vanish - clearing is honest) or a ship's phpclass changes. */
	clear: function clear(ship) {
		if (!ship) return;
		ship.preBattleDamage = {};
		delete ship.preBattleCritSeen;
		battleDamage.applyToShip(ship);
	},

	clone: function clone(payload) {
		if (!payload || typeof payload !== 'object') return {};
		return JSON.parse(JSON.stringify(payload));
	},

	/* Invalidation after the lobby rebuilds a ship's systems from its blueprint (Edit,
	   Copy). The PAYLOAD survives on the ship object, so all that is normally needed is
	   re-rendering the preview onto the new system objects.
	   The exception is a FLIGHT whose size changed: ordinals past the new size would
	   silently vanish, so the payload is dropped wholesale and the caller says so.
	   Returns true when it cleared something. */
	onShipRebuilt: function onShipRebuilt(ship, previousFlightSize) {
		var cleared = false;

		if (ship && ship.flight && previousFlightSize !== undefined && previousFlightSize !== null
			&& parseInt(previousFlightSize, 10) !== parseInt(ship.flightSize, 10)
			&& !battleDamage.isEmpty(battleDamage.peek(ship))) {
			ship.preBattleDamage = {};
			delete ship.preBattleCritSeen;
			cleared = true;
		}

		battleDamage.applyToShip(ship);
		return cleared;
	},

	isEmpty: function isEmpty(payload) {
		if (!payload || typeof payload !== 'object') return true;
		var sys = payload.sys && Object.keys(payload.sys).length;
		var ftr = payload.ftr && Object.keys(payload.ftr).length;
		return !sys && !ftr;
	},

	/* {damage, criticals} — mirrors PreBattleDamage::contents. */
	contents: function contents(payload) {
		var has = { damage: false, criticals: false };
		if (!payload || typeof payload !== 'object') return has;

		['sys', 'ftr'].forEach(function (bucketName) {
			var bucket = payload[bucketName];
			for (var ref in bucket) {
				if (!bucket.hasOwnProperty(ref)) continue;
				var entry = bucket[ref] || {};
				if (entry.d || entry.k) has.damage = true;
				if (entry.c && Object.keys(entry.c).length) has.criticals = true;
			}
		});

		return has;
	},

	/* Mirrors PreBattleDamage::filter. Kept for symmetry and for any client-side
	   preview of a partial load; the AUTHORITATIVE split happens server-side so the
	   two can never disagree about what actually gets written. */
	filter: function filter(payload, withDamage, withCriticals) {
		var out = {};
		if (!payload || typeof payload !== 'object') return out;

		['sys', 'ftr'].forEach(function (bucketName) {
			var bucket = payload[bucketName];
			if (!bucket) return;
			var kept = {};
			for (var ref in bucket) {
				if (!bucket.hasOwnProperty(ref)) continue;
				var entry = bucket[ref] || {};
				var e = {};
				if (withDamage) {
					if (entry.d) e.d = entry.d;
					if (entry.k) e.k = 1;
				}
				if (withCriticals && entry.c && Object.keys(entry.c).length) {
					e.c = JSON.parse(JSON.stringify(entry.c));
					//`p` travels with `c` or not at all, keyed the same way.
					if (entry.p) {
						var p = {};
						for (var type in entry.c) {
							if (entry.p[type] !== undefined) p[type] = entry.p[type];
						}
						if (Object.keys(p).length) e.p = p;
					}
				}
				if (Object.keys(e).length) kept[ref] = e;
			}
			if (Object.keys(kept).length) out[bucketName] = kept;
		});

		return out;
	},

	/* Any of the player's own bought units carries damage or criticals?
	   Drives onReadyClicked's warning (D1). Lobby-only: gamedata.ships there is the
	   player's fleet list, and userid 0 marks the store blueprints. */
	fleetHasDamage: function fleetHasDamage() {
		if (!window.gamedata || !gamedata.ships) return false;

		for (var i in gamedata.ships) {
			var ship = gamedata.ships[i];
			if (!ship || ship.userid == 0) continue;
			if (!battleDamage.isEmpty(battleDamage.peek(ship))) return true;
		}

		return false;
	},

	/* ---------------------------------------------------------------- *
	 *  Preview rendering
	 * ---------------------------------------------------------------- */

	/* Render the payload onto the ship's system objects so the existing icon/health-bar/
	   section-header code paths show it with no changes of their own.

	   IDEMPOTENT by construction: every run first strips the pre-battle entries it put
	   there last time, so it can be called after every keystroke, and a server-applied
	   payload arriving through loadSavedFleet's JSON is re-derived rather than doubled.

	   ⚠️ Client system fields SHARE REFERENCES across same-phpclass instances, so
	   `damage` / `criticals` / `critData` must be REPLACED with fresh copies before the
	   first write — otherwise damaging one Omega damages every Omega in the fleet. The
	   filter/assign below is that copy: it always produces a new array. */
	applyToShip: function applyToShip(ship) {
		if (!ship) return;

		var payload = battleDamage.peek(ship) || {};
		var critDesc = ship.preBattleCritDesc || {};

		var eachSystem = function (fn) {
			for (var i in ship.systems) {
				var system = ship.systems[i];
				if (!system) continue;
				fn(system);
				//flight: fighters carry their own subsystem arrays
				if (Array.isArray(system.systems)) {
					for (var j in system.systems) {
						if (system.systems[j]) fn(system.systems[j]);
					}
				}
			}
		};

		//1. strip whatever a previous run left behind (and copy-on-write in the process)
		eachSystem(function (system) {
			system.damage = (system.damage || []).filter(function (d) {
				return d.damageclass !== battleDamage.DAMAGECLASS;
			});
			system.criticals = (system.criticals || []).filter(function (c) {
				return !c.preBattle && !(c.turn === 0 && (c.turnend === 0 || c.turnend === undefined));
			});
			system.destroyed = false;
		});

		//2. apply the `sys` bucket. `ftr` entries are deliberately NOT written into system
		//   arrays - the lobby holds one sample fighter object for the whole flight, so
		//   per-ordinal state is read straight from the payload by the fighter UI.
		var sys = payload.sys || {};
		for (var ref in sys) {
			if (!sys.hasOwnProperty(ref)) continue;
			//Own lookup rather than shipManager.systems.getSystem: doLoadFleet deliberately
			//leaves null HOLES in a flight's systems array, and getSystem dereferences
			//without a null check.
			var system = battleDamage.findSystem(ship, parseInt(ref, 10));
			if (!system) continue;

			var entry = sys[ref] || {};
			var destroyed = entry.k ? 1 : 0;
			var damage = parseInt(entry.d, 10) || 0;
			if (destroyed && damage < 1) damage = system.maxhealth;

			if (damage > 0 || destroyed) {
				system.damage.push({
					id: -1, shipid: ship.id, systemid: system.id, turn: battleDamage.TURN,
					damage: damage, armour: 0, shields: 0, fireorderid: 0,
					destroyed: destroyed, undestroyed: 0,
					pubnotes: battleDamage.PUBNOTES, damageclass: battleDamage.DAMAGECLASS
				});
			}

			if (destroyed) system.destroyed = true;

			for (var type in (entry.c || {})) {
				if (!entry.c.hasOwnProperty(type)) continue;
				var count = parseInt(entry.c[type], 10) || 0;
				//Param crits carry their magnitude, not their multiplicity - SystemInfo and
				//the shield colour test both SUM system.criticals[].param.
				var param = (entry.p && entry.p[type] !== undefined)
					? (parseInt(entry.p[type], 10) || null) : null;
				for (var n = 0; n < count; n++) {
					system.criticals.push({
						id: -1, shipid: ship.id, systemid: system.id, phpclass: type,
						turn: battleDamage.TURN, turnend: 0, param: param,
						forInfo: false, preBattle: true
					});
				}
				//critData is how crit text already reaches SystemInfo; it falls back to the
				//raw class name when a description is missing. critLabel builds the
				//param-carrying ones from the payload - the server's critDesc map is keyed
				//by class and cannot know this system's param.
				system.critData = Object.assign({}, system.critData || {});
				system.critData[type] = battleDamage.critLabel(type, critDesc, param);
			}
		}

		//3. mirror ShipSystem::isDestroyed's structure cascade. The client's isDestroyed
		//   reads the server-computed `destroyed` boolean and does NOT re-derive it from
		//   damage, so the preview has to do the cascade itself.
		battleDamage.cascadeStructure(ship);
	},

	/* Mirror of ShipSystem::isDestroyed's structure cascade, rule for rule:
	     - a non-Structure system falls with the structure block it belongs to, UNLESS it
	       sets survivesStructureDestruction (shield projections);
	     - the block is structureHomeLocation when set, otherwise the system's own location
	       - a system displayed apart from its block (Kirishiac orbitals) dies with its HOME
	       section, not with the section it is drawn in;
	     - structureHomeLocation may be an ARRAY, and then the system only falls when EVERY
	       one of those structures is gone;
	     - a non-primary Structure falls when the PRIMARY structure does, which is what takes
	       the whole ship with it.
	   ⚠️ Duck-type on name, never `instanceof Structure`: lobby fleet ships are
	   jQuery.extend clones whose systems have lost their prototype chain. */
	cascadeStructure: function cascadeStructure(ship) {
		if (!ship || ship.flight) return;

		//location => that section's structure is destroyed
		var goneByLocation = {};
		var anyGone = false;
		for (var i in ship.systems) {
			var system = ship.systems[i];
			if (!system || system.name !== 'structure' || !system.destroyed) continue;
			goneByLocation[String(system.location)] = true;
			anyGone = true;
		}
		if (!anyGone) return;

		var primaryGone = Boolean(goneByLocation['0']);

		for (var j in ship.systems) {
			var target = ship.systems[j];
			if (!target) continue;

			if (target.name === 'structure') {
				//a non-primary Structure falls with PRIMARY, and only with PRIMARY
				if (primaryGone) target.destroyed = true;
				continue;
			}

			if (target.survivesStructureDestruction) continue;

			var home = (target.structureHomeLocation !== undefined
				&& target.structureHomeLocation !== null)
				? target.structureHomeLocation : target.location;

			var blocks = Array.isArray(home) ? home : [home];
			if (!blocks.length) continue;

			var allGone = blocks.every(function (loc) {
				//primary carries every block with it (its own Structure kills the rest)
				return primaryGone || goneByLocation[String(loc)];
			});

			if (allGone) target.destroyed = true;
		}
	},

	/* Strip the preview and rebuild it from the payload. applyToShip already begins with
	   the strip, so this is that operation named for its intent (used after an edit that
	   may have REMOVED entries). */
	revertShip: function revertShip(ship) {
		battleDamage.applyToShip(ship);
	},

	/* ---------------------------------------------------------------- *
	 *  Flights (synthetic per-ordinal state)
	 * ---------------------------------------------------------------- */

	/* Hole-tolerant system lookup by id. */
	findSystem: function findSystem(ship, id) {
		if (!ship || !ship.systems) return null;
		for (var i in ship.systems) {
			var system = ship.systems[i];
			if (system && system.id == id) return system;
		}
		return null;
	},

	/* The one blueprint fighter every synthetic row is rendered from. */
	sampleFighter: function sampleFighter(ship) {
		if (!ship || !ship.systems) return null;
		for (var i in ship.systems) {
			if (ship.systems[i]) return ship.systems[i];
		}
		return null;
	},

	fighterMaxHealth: function fighterMaxHealth(ship) {
		var sample = battleDamage.sampleFighter(ship);
		return sample ? sample.maxhealth : 0;
	},

	/* Remaining health of fighter #ordinal, 1 .. maxhealth (never 0 — see setFighter). */
	fighterHealth: function fighterHealth(ship, ordinal) {
		var max = battleDamage.fighterMaxHealth(ship);
		var entry = battleDamage.getEntry(ship, battleDamage.KIND_FIGHTER, ordinal);
		if (!entry) return max;
		return Math.max(0, max - (parseInt(entry.d, 10) || 0));
	},

	/* {critClass: count} for that ordinal. */
	fighterCriticals: function fighterCriticals(ship, ordinal) {
		var entry = battleDamage.getEntry(ship, battleDamage.KIND_FIGHTER, ordinal);
		return (entry && entry.c) ? entry.c : {};
	},

	/* {critClass: param} for that ordinal — only the PARAM_CRITICALS classes appear. */
	fighterCriticalParams: function fighterCriticalParams(ship, ordinal) {
		var entry = battleDamage.getEntry(ship, battleDamage.KIND_FIGHTER, ordinal);
		return (entry && entry.p) ? entry.p : {};
	},

	/* Flight-level state for the ONE fighter card the lobby draws:
	   {percent, remaining, total, size}. Every fighter in the flight is alive by
	   construction — a destroyed one would simply not be in it. */
	flightSummary: function flightSummary(ship) {
		var size = parseInt(ship && ship.flightSize, 10) || 0;
		var max = battleDamage.fighterMaxHealth(ship);
		var summary = { percent: 100, remaining: max * size, total: max * size, size: size };
		if (!size || !max) return summary;

		var remaining = 0;
		for (var o = 1; o <= size; o++) {
			remaining += battleDamage.fighterHealth(ship, o);
		}

		summary.remaining = remaining;
		summary.percent = (remaining / summary.total) * 100;
		return summary;
	},

	/* ---------------------------------------------------------------- *
	 *  Part 2: summarise a LIVE game ship into the wire format
	 * ---------------------------------------------------------------- */

	/* Every fighter in a flight that is still ALIVE at the end of the battle. Destroyed,
	   dropped-out, docked and split-off craft are not part of the flight any more, so
	   this is the flightSize the fleet is saved with — a lost fighter shrinks the flight
	   rather than riding along as a wreck. */
	survivingFighters: function survivingFighters(ship) {
		var alive = [];
		if (!ship || !ship.flight || !ship.systems) return alive;

		for (var i in ship.systems) {
			var fighter = ship.systems[i];
			if (!fighter) continue;
			if (shipManager.systems.isDestroyed(ship, fighter)) continue;
			alive.push(fighter);
		}

		return alive;
	},

	survivingFlightSize: function survivingFlightSize(ship) {
		return battleDamage.survivingFighters(ship).length;
	},

	/* Collapse a live ship's many DamageEntry rows and in-effect criticals into one
	   payload, so the end state of a battle becomes the start state of the next.

	   ⚠️ stripForJson has already aggregated pre-(currentTurn-1) damage into ONE synthetic
	   entry, but the TOTAL is preserved — so this must SUM, never count. */
	/* opts.includeTransient — carry one-turn / self-expiring criticals too. Driven by the
	   "save temporary critical effects" checkbox in the Save Fleet dialog, OFF by default:
	   most of the time a wound is worth carrying and a one-turn effect is not. */
	summariseShip: function summariseShip(ship, opts) {
		var payload = {};
		if (!ship || !ship.systems) return payload;

		if (ship.flight) {
			//Ordinals are numbered over the SURVIVORS, in flight order, to match the
			//shrunken flightSize the fleet is saved with.
			var alive = battleDamage.survivingFighters(ship);
			var ftr = {};
			for (var i = 0; i < alive.length; i++) {
				var entry = battleDamage.summariseSystem(ship, alive[i], true, opts);
				if (entry) ftr[String(i + 1)] = entry;
			}
			if (Object.keys(ftr).length) payload.ftr = ftr;
			return payload;
		}

		var sys = {};
		for (var j in ship.systems) {
			var system = ship.systems[j];
			if (!system) continue;
			var e = battleDamage.summariseSystem(ship, system, false, opts);
			if (e) sys[String(system.id)] = e;
		}
		if (Object.keys(sys).length) payload.sys = sys;

		return payload;
	},

	/* $damageOnly (fighters): cap the damage one point short of destruction and never
	   emit `k`. Mirrors PreBattleDamage::sanitiseEntry's third argument. */
	summariseSystem: function summariseSystem(ship, system, damageOnly, opts) {
		if (!system || !(system.maxhealth > 0)) return null;

		var entry = {};
		var cap = damageOnly ? system.maxhealth - 1 : system.maxhealth;

		var damage = Math.min(cap, damageManager.getDamage(ship, system));
		if (damage > 0) entry.d = damage;
		if (!damageOnly && (shipManager.systems.isDestroyed(ship, system) || damage >= system.maxhealth)) {
			entry.k = 1;
		}

		var crits = battleDamage.summariseCriticals(system, opts);
		if (Object.keys(crits.counts).length) entry.c = crits.counts;
		if (Object.keys(crits.params).length) entry.p = crits.params;

		return Object.keys(entry).length ? entry : null;
	},

	/* In-effect criticals for one system, as {counts: {class: n}, params: {class: int}}.
	   Three filters, in order:
	     1. `forInfo` — a display marker for something that happened during the battle (a
	        boarding attempt, "may overheat"), never a lasting wound. Always dropped; this
	        is what catches every marine/boarding critical generically.
	     2. EXCLUDED_CRITICALS — the by-name deny list above. Always dropped.
	     3. TEMPORARY (one-turn / self-expiring) — dropped UNLESS the caller asked for them
	        via opts.includeTransient, i.e. the Save Fleet checkbox. Kept ones are stamped
	        at turn 1 by the server (PreBattleDamage::TRANSIENT_TURN) so they are in effect
	        for the first turn of the next battle rather than expiring as it begins.
	   A PARAM_CRITICALS class is SUMMED into a single collapsed crit rather than counted:
	   its magnitude is the sum of the params, so one row carrying the total is exactly
	   equivalent (and is the same collapse PreBattleDamage::sanitiseEntry enforces).
	   Mirrors PreBattleDamage::isValidCriticalType + isTransientCriticalType. */
	summariseCriticals: function summariseCriticals(system, opts) {
		var counts = {};
		var params = {};
		var includeTransient = Boolean(opts && opts.includeTransient);

		for (var i in system.criticals) {
			var crit = system.criticals[i];
			if (!crit || !crit.phpclass) continue;
			if (crit.forInfo) continue;
			if (battleDamage.EXCLUDED_CRITICALS.indexOf(crit.phpclass) !== -1) continue;
			if (!includeTransient && battleDamage.isTransientCritical(crit)) continue;

			if (battleDamage.isParamCritical(crit.phpclass)) {
				params[crit.phpclass] = (params[crit.phpclass] || 0) + (parseInt(crit.param, 10) || 0);
				counts[crit.phpclass] = 1;
			} else {
				counts[crit.phpclass] = Math.min(
					(counts[crit.phpclass] || 0) + 1, battleDamage.MAX_CRIT_COUNT);
			}
		}

		//A param class whose params summed to nothing has no magnitude left to carry.
		for (var type in params) {
			params[type] = battleDamage.sanitiseParam(params[type], type);
			if (!params[type]) {
				delete params[type];
				delete counts[type];
			}
		}

		return { counts: counts, params: params };
	}
};
