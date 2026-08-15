"use strict";

/*==========================================================================
  Pre-battle damage & fleet damage persistence — client state + preview.
  Design: PREBATTLE_DAMAGE_PLAN.md. PHP twin: source/server/model/PreBattleDamage.php.

  Holds the one compact per-ship payload that travels everywhere:

      ship.preBattleDamage = {
        sys: { "<systemid>": { d: <int>, k: 0|1, c: {<critClass>: <count>}, p: {<critClass>: <int>} }, ... },
        ftr: { "<ordinal>":  { d: <int>, k: 0|1, c: {<critClass>: <count>}, p: {<critClass>: <int>} }, ... },
        mne: { "<ordinal>":  { d: <int> }, ... }
      }

  `c` counts criticals; `p` carries the integer magnitude of the few classes that keep
  it in the crit's own param instead of in the number of crits (PARAM_CRITICALS). A
  sibling map rather than a richer `c` value, so every consumer of `c` keeps counting
  and `p` is simply absent for everything else. `p` never outlives `c`.

  ORDINARY SHIPS are keyed by systemid (construction-order ids, stable across the
  static blueprint / buy-POST reconstruction / game load, and enhancements never add
  systems). FLIGHTS are keyed by fighter ORDINAL 1..flightSize, because in the lobby
  a flight is ONE sample fighter plus a number — the N fighters are minted server-side
  by populate(), so per-fighter system ids do not exist here at all. MINES are the same
  shape (one object plus bulkBuy, minted into N ships by BuyingGamePhase) and so are
  keyed by copy ORDINAL 1..bulkBuy; their damage always lands on that copy's Structure.

  Loaded on BOTH gamelobby.php (authoring + preview) and game.php (summariseShip, for
  "Save Current Fleet").
  ==========================================================================*/

window.battleDamage = {

	TURN: 0,
	DAMAGECLASS: 'PreGame',
	PUBNOTES: 'Pre-battle damage',
	MAX_CRIT_COUNT: 5,
	MAX_CRIT_PARAM: 300,

	KIND_SYSTEM: 0,
	KIND_FIGHTER: 1,
	/* MINES. A bulk mine purchase is ONE lobby object carrying bulkBuy = N, minted into N
	   separate ships server-side - the same "one object plus a number" shape a flight has,
	   which is why it is keyed by ordinal rather than by system id. STRUCTURE ONLY: every
	   other system on a mine is untargetable and a mine has no criticals worth carrying,
	   so a `mne` entry is {d} and nothing else, and (like a fighter) a mine is damaged but
	   never destroyed - one you lost is one fewer in the bulk. */
	KIND_MINE: 2,

	/* ⭐ THE WHOLE FLIGHT, as a critical-effects target. Fighter ordinals are 1-based, so 0
	   can never collide with a real one.

	   The fighter menu used to draw a Critical Effects section under EVERY fighter row -
	   six section headers, six pickers and six "All" switches on a flight of six, for a
	   set of craft that are identical by construction. It now draws ONE, underneath all the
	   health rows (user request 2026-08-08), and that section addresses the flight: reading
	   it unions what the ordinals carry, writing it writes the same criticals to every
	   ordinal. Per-fighter DAMAGE is untouched - that is what the rows above are for. */
	REF_FLIGHT: 0,

	/* bucket name => kind. THE list every loop walks, so a fourth kind is one line here.
	   Mirrors PreBattleDamage::BUCKETS. */
	BUCKETS: { sys: 0, ftr: 1, mne: 2 },

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

	/* ⭐ PER-CLASS CRITICAL LIMITS. MIRRORS PreBattleDamage::$critLimits in PHP - edit
	   BOTH, exactly like the deny list. Anything not named here may be carried up to
	   MAX_CRIT_COUNT; the server clamps regardless, this copy stops the editor offering a
	   count that would be silently reduced.

	   Every entry is a critical the game reads as a FLAG - `if (hasCritical('X'))`, never
	   multiplied by the count, and no outputMod - so a second instance changes nothing
	   observable. Effects that DO multiply by their count (ReducedIniative, GunLost,
	   ArmorReduced, PenaltyToHit, HalfEfficiency…) or stack through outputMod (the
	   OutputReduced family, FieldFluctuations) are deliberately absent. The burnouts and
	   OutputHalved are the judgement calls: they stack arithmetically, but "efficiency
	   halved" twice is not something a B5W sheet can say. */
	CRIT_LIMITS: {
		DamageReductionRemoved: 1,
		ReducedArcs: 1,
		ArmorReduced: 10,
		ContainmentBreach: 1,
		ChargeHalve: 1,
		ChargeEmpty: 1,
		ShipDisabled: 1,
		ShipDisabledOneTurn: 1,
		ForcedOfflineOneTurn: 1,
		OSATThrusterCrit: 1,
		//GravThrusterCritIgnored: 1,
		//AmmoExplosion: 1,
		//EngineShorted: 1,
		//ControlsStuck: 1,
		TendrilDestroyed: 1,
		PartialBurnout: 1,
		SevereBurnout: 1,
		OutputHalved: 1,
		OutputHalvedOneTurn: 1,
		DamageReductionReduced: 1
	},

	/* How many of this critical one damage target may carry. The catalogue's `meta` (when
	   it has been fetched for this ship class) is authoritative - it is built from the PHP
	   table - and CRIT_LIMITS is the offline mirror used before it arrives. */
	critLimit: function critLimit(type) {
		var meta = battleDamage.critMeta(type);
		if (meta && meta.limit > 0) return meta.limit;
		var limit = battleDamage.CRIT_LIMITS[type];
		return (limit > 0) ? limit : battleDamage.MAX_CRIT_COUNT;
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
	   payload, everything else uses a per-class description.

	   THREE sources, in order, because each covers a case the others cannot:
	     1. critDesc — the server's describeCriticals map, sent with a LOADED fleet. Only
	        ever holds the classes that fleet actually carried.
	     2. the CATALOGUE's meta — every class the picker can offer. This is what names a
	        critical the player has just ADDED in the lobby: it was in no loaded fleet, so
	        critDesc has never heard of it and the row read as `OutputReduced1` instead of
	        "Output altered by -1" (user report 2026-08-08).
	     3. the raw class name, which is what SystemInfo falls back to as well. */
	critLabel: function critLabel(type, critDesc, param) {
		var spec = battleDamage.PARAM_CRITICALS[type];
		if (spec) return spec.label + ' ' + (parseInt(param, 10) || 0);
		if (critDesc && critDesc[type]) return critDesc[type];

		var meta = battleDamage.critMeta(type);
		return (meta && meta.label) || type;
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
		'AmmoExplosion',
        'ControlsStuck',
        'EngineShorted', 
        'ForcedOfflineForTurns',
        'GravThrusterCritIgnored',		
        'MayOverheat',		
		//Hangar Ops / state markers
		'DockedFighter', 'SplitLaunchedFighter', 'LaunchedThisTurn',
		'LCVLaunchedThisTurn', 'HangarOperations', 'OrbitalRepairing',
		'DisengagedFighter', 'ShadowFighterCutOff', 'LimpetBoreTravelling', 'Uncontrolled',
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
	 *  Catalogue (systemCriticals.php) — plan §11.2
	 * ---------------------------------------------------------------- */

	/* Per-SHIP-CLASS catalogue, fetched once per phpclass+flightSize per session:
	     { systems: {<systemid>: {crits:[…], ssd:true}}, fighters: {<ordinal>: […]},
	       meta: {<class>: {label, limit, param, transient}}, all: […] }

	   Two consumers, both of which need something PROTECTED server-side and therefore
	   absent from the static ship blueprints the lobby builds its windows from:

	     1. the crit PICKER - $possibleCriticals is protected and in neither the static
	        JSON nor stripForJson;
	     2. the structure CASCADE - survivesStructureDestruction is protected too. It does
	        reach the static blueprint via ShipCompactor::annotateSystems, but only after a
	        static REGEN, so a shield projection went dark with its section on any tree
	        whose bundle had not been rebuilt (user report 2026-08-08). Reading it here
	        makes the preview right regardless of the bundle's age. */
	catalogues: {},
	catalogueRequests: {},

	/* {critClass: meta} flattened across every catalogue fetched so far. Every catalogue
	   carries the same global `meta`, so a per-class question does not need to know which
	   ship class it came from - and critMeta is asked once per critical inside applyToShip,
	   which runs on every keystroke in the damage editor. Filling a flat index when a
	   catalogue LANDS turns that from a scan of every cached catalogue into one lookup.
	   ⚠️ INVARIANT: loadCatalogue below is the ONLY thing that writes `catalogues`, and it
	   fills this in the same breath. Anything that ever populates `catalogues` by another
	   route must fill this too, or critMeta will not see it. */
	critMetaIndex: {},

	catalogueKey: function catalogueKey(ship) {
		if (!ship || !ship.phpclass) return null;
		return ship.phpclass + '/' + (ship.flight ? (parseInt(ship.flightSize, 10) || 1) : 1);
	},

	catalogueFor: function catalogueFor(ship) {
		var key = battleDamage.catalogueKey(ship);
		return key ? (battleDamage.catalogues[key] || null) : null;
	},

	/* Fetch it if we do not have it yet. `onLoaded` fires only on a fresh arrival, so a
	   caller can re-render exactly once; already-cached is a synchronous no-op returning
	   the catalogue. Failures are cached as an EMPTY catalogue so a broken endpoint costs
	   one request per ship class, not one per menu open. */
	loadCatalogue: function loadCatalogue(ship, onLoaded) {
		var key = battleDamage.catalogueKey(ship);
		if (!key) return null;
		if (battleDamage.catalogues[key]) return battleDamage.catalogues[key];
		if (battleDamage.catalogueRequests[key]) return null;
		if (!window.ajaxInterface || typeof ajaxInterface.getSystemCriticals !== 'function') return null;

		battleDamage.catalogueRequests[key] = true;
		ajaxInterface.getSystemCriticals(ship.phpclass,
			ship.flight ? (parseInt(ship.flightSize, 10) || 1) : 1,
			function (response) {
				delete battleDamage.catalogueRequests[key];
				var catalogue = (response && response.success) ? response : {};
				battleDamage.catalogues[key] = catalogue;
				//first entry wins, matching the scan this replaced
				for (var type in (catalogue.meta || {})) {
					if (!battleDamage.critMetaIndex[type]) {
						battleDamage.critMetaIndex[type] = catalogue.meta[type];
					}
				}
				if (typeof onLoaded === 'function') onLoaded(catalogue);
			});

		return null;
	},

	/* {label, limit, param, transient} for one critical class, from whichever catalogue
	   has been fetched (they all carry the same global `meta`). */
	critMeta: function critMeta(type) {
		return type ? (battleDamage.critMetaIndex[type] || null) : null;
	},

	/* The criticals this damage target may be OFFERED. A DIFFERENT question from what may
	   be STORED (see PreBattleDamage::offerableCriticalTypes) - this is the system's own
	   list: its hit chart plus its $preBattleCriticals extras, both served per target by
	   the catalogue.

	   `all` WIDENS that list, it does not replace it. It adds the generally-applicable
	   effects (PreBattleDamage::$generalCriticals, the catalogue's `all`) on top of what
	   this system can do in its own right. Replacing was the first implementation and it
	   was wrong in the obvious way: ticking "All" on a weapon HID Gun Lost and Turret
	   jammed, the two effects most worth authoring there (user report 2026-08-08). */
	offerableCriticals: function offerableCriticals(ship, kind, ref, all) {
		var catalogue = battleDamage.catalogueFor(ship);
		if (!catalogue) return [];

		var own;
		if (kind === battleDamage.KIND_FIGHTER) {
			//Every craft in a flight is the same blueprint, so the flight-wide target
			//(REF_FLIGHT) is offered ordinal 1's list.
			var ordinal = battleDamage.isFlightRef(kind, ref) ? 1 : ref;
			own = (catalogue.fighters && catalogue.fighters[String(ordinal)]) || [];
		} else {
			var entry = catalogue.systems && catalogue.systems[String(ref)];
			own = (entry && entry.crits) || [];
		}

		if (!all) return own;

		//Union, this system's own effects first. The picker sorts by label anyway, so the
		//order only matters in that it decides which duplicate is dropped.
		var seen = {};
		var out = [];
		own.concat(catalogue.all || []).forEach(function (type) {
			if (!type || seen[type]) return;
			seen[type] = true;
			out.push(type);
		});

		return out;
	},

	/* Does this system survive its structure block being destroyed? Catalogue first (always
	   current), then the static blueprint's own flag (present once the ship bundle has been
	   regenerated). */
	survivesStructureDestruction: function survivesStructureDestruction(ship, system) {
		if (!system) return false;
		if (system.survivesStructureDestruction) return true;

		var catalogue = battleDamage.catalogueFor(ship);
		var entry = catalogue && catalogue.systems && catalogue.systems[String(system.id)];
		return Boolean(entry && entry.ssd);
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
		Object.keys(battleDamage.BUCKETS).forEach(function (bucketName) {
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
		if (kind === battleDamage.KIND_FIGHTER || kind === 'ftr') return 'ftr';
		if (kind === battleDamage.KIND_MINE || kind === 'mne') return 'mne';
		return 'sys';
	},

	isFlightRef: function isFlightRef(kind, ref) {
		return kind === battleDamage.KIND_FIGHTER
			&& parseInt(ref, 10) === battleDamage.REF_FLIGHT;
	},

	getEntry: function getEntry(ship, kind, ref) {
		//The flight-wide critical target is SYNTHETIC - it has no bucket entry of its own,
		//it is the union of what the ordinals carry (see REF_FLIGHT).
		if (battleDamage.isFlightRef(kind, ref)) return battleDamage.flightCritEntry(ship);

		var payload = battleDamage.peek(ship);
		if (!payload) return null;
		var bucket = payload[battleDamage.bucketName(kind)];
		if (!bucket) return null;
		return bucket[String(ref)] || null;
	},

	/* A read-only {c, p} standing for the WHOLE flight: every critical class any ordinal
	   carries, at the highest count/param any of them carries it at. Null when the flight
	   carries none, so the section renders as empty rather than as a row of zeroes.

	   The max, not the first ordinal's: a fleet saved out of a real battle can legitimately
	   have different criticals on different craft, and showing the worst of them is honest
	   about what the flight is carrying. Editing then levels them (see setFlightCriticals) -
	   which is the trade the one-section layout makes, and is what "apply to the flight"
	   has to mean when the lobby draws a flight as a single card. */
	flightCritEntry: function flightCritEntry(ship) {
		var size = parseInt(ship && ship.flightSize, 10) || 0;
		var entry = { c: {}, p: {} };

		for (var ordinal = 1; ordinal <= size; ordinal++) {
			var each = battleDamage.getEntry(ship, battleDamage.KIND_FIGHTER, ordinal) || {};
			for (var type in (each.c || {})) {
				if (!each.c.hasOwnProperty(type)) continue;
				var count = parseInt(each.c[type], 10) || 0;
				if (count > (entry.c[type] || 0)) entry.c[type] = count;
			}
			for (var ptype in (each.p || {})) {
				if (!each.p.hasOwnProperty(ptype)) continue;
				var param = parseInt(each.p[ptype], 10) || 0;
				if (param > (entry.p[ptype] || 0)) entry.p[ptype] = param;
			}
		}

		if (!Object.keys(entry.c).length) return null;
		if (!Object.keys(entry.p).length) delete entry.p;
		return entry;
	},

	/* Write one critical map to EVERY fighter in the flight, through the ordinary
	   per-ordinal door so the clamps, the deny list and the param collapse all still
	   apply. Each ordinal's damage is preserved (setCriticals only ever touches c/p). */
	setFlightCriticals: function setFlightCriticals(ship, critMap, paramMap) {
		var size = parseInt(ship && ship.flightSize, 10) || 0;
		for (var ordinal = 1; ordinal <= size; ordinal++) {
			battleDamage.setCriticals(ship, battleDamage.KIND_FIGHTER, ordinal, critMap, paramMap);
		}
		return battleDamage.flightCritEntry(ship);
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

	/* ⭐ Systems that may be DAMAGED but never destroyed, and never dialled to 0 structure.
	   Only reactors so far. Destroying the main reactor destroys the primary structure
	   block (BaseShip::destroySection), and a ship whose primary structure is gone IS a
	   destroyed ship (BaseShip::isDestroyed) - so a "damaged" ship bought that way would
	   arrive at the battle already dead.

	   ⚠️ Duck-typed on `name`, never `instanceof`: lobby fleet ships are jQuery.extend
	   clones with no prototype chain left, so every instanceof against them is false (see
	   the MineStructure lookup below, done the same way). 'reactor' is the shared name of
	   Reactor, MagGravReactor, MagGravReactorTechnical, AdvancedSingularityDrive and
	   SubReactor; SubReactorUniversal declares its own.

	   MIRRORS PreBattleDamage::$indestructibleSystems - the server is the boundary that
	   enforces, this copy stops the editor offering what the server would clamp. EDIT BOTH. */
	INDESTRUCTIBLE_SYSTEMS: ['reactor', 'SubReactorUniversal'],

	isIndestructible: function isIndestructible(system) {
		return Boolean(system)
			&& battleDamage.INDESTRUCTIBLE_SYSTEMS.indexOf(system.name) !== -1;
	},

	systemById: function systemById(ship, systemid) {
		if (!ship || !ship.systems) return null;
		for (var i in ship.systems) {
			if (ship.systems[i] && ship.systems[i].id == systemid) return ship.systems[i];
		}
		return null;
	},

	/* The one write point for per-system damage, so an indestructible system cannot be
	   killed by any route into it - the menu's Destroy tick, typing 0, or wheeling down. */
	setSystem: function setSystem(ship, systemid, entry) {
		var system = battleDamage.systemById(ship, systemid);

		if (battleDamage.isIndestructible(system)) {
			var cap = Math.max(0, (parseInt(system.maxhealth, 10) || 0) - 1);
			var patch = { k: 0 };
			if (entry && entry.hasOwnProperty('d')) {
				patch.d = Math.min(cap, parseInt(entry.d, 10) || 0);
			}
			return battleDamage.setEntry(ship, battleDamage.KIND_SYSTEM, systemid, patch);
		}

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

	/* ---------------------------------------------------------------- *
	 *  Mines (synthetic per-copy state)
	 * ---------------------------------------------------------------- */

	/* How many mines this one lobby object stands for. */
	mineCount: function mineCount(ship) {
		if (!ship || !ship.mine) return 0;
		var count = parseInt(ship.bulkBuy, 10);
		return (count > 0) ? count : 1;
	},

	/* The only system on a mine that takes damage. Duck-typed on `name`, never
	   `instanceof Structure`: lobby fleet ships are jQuery.extend clones with no
	   prototype chain left. */
	mineStructure: function mineStructure(ship) {
		if (!ship || !ship.systems) return null;
		for (var i in ship.systems) {
			var system = ship.systems[i];
			if (system && system.name === 'structure') return system;
		}
		return null;
	},

	mineMaxHealth: function mineMaxHealth(ship) {
		var structure = battleDamage.mineStructure(ship);
		return structure ? structure.maxhealth : 0;
	},

	/* Remaining structure of mine #ordinal. Like a fighter it floors at 1: a mine you lost
	   is one fewer in the bulk, not a wreck with 0 structure. */
	mineHealth: function mineHealth(ship, ordinal) {
		var max = battleDamage.mineMaxHealth(ship);
		var entry = battleDamage.getEntry(ship, battleDamage.KIND_MINE, ordinal);
		if (!entry) return max;
		return Math.max(0, max - (parseInt(entry.d, 10) || 0));
	},

	setMine: function setMine(ship, ordinal, entry) {
		var cap = Math.max(0, battleDamage.mineMaxHealth(ship) - 1);
		var patch = { k: 0 };
		if (entry && entry.hasOwnProperty('d')) {
			patch.d = Math.min(cap, parseInt(entry.d, 10) || 0);
		}
		return battleDamage.setEntry(ship, battleDamage.KIND_MINE, ordinal, patch);
	},

	/* {remaining, total, size} across every mine in the bulk, for the window's caption. */
	mineSummary: function mineSummary(ship) {
		var size = battleDamage.mineCount(ship);
		var max = battleDamage.mineMaxHealth(ship);
		var summary = { remaining: max * size, total: max * size, size: size };
		if (!size || !max) return summary;

		var remaining = 0;
		for (var o = 1; o <= size; o++) remaining += battleDamage.mineHealth(ship, o);
		summary.remaining = remaining;
		return summary;
	},

	/* REPLACE a whole entry (a deep copy of it), criticals included, or remove it when
	   given nothing. Used by the mine menu's "apply to all" — a `mne` entry is only ever
	   {d}, so copying it wholesale copies exactly what the row shows.
	   The FIGHTER menu deliberately does NOT use this: since criticals there are authored
	   for the whole flight at once (REF_FLIGHT), its propagate copies damage only. */
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

	/* Writes the `c` (and `p`) keys — the ONE door criticals enter a payload by on the
	   client, so the preview, the buy POST and the saved-fleet write all follow from a
	   single place. The lobby's crit picker (CriticalEffectsSection) calls exactly this
	   and nothing else, which is why adding authoring needed no other change.
	   critMap  = {critClass: count}; an empty/falsy map clears the criticals half.
	   paramMap = {critClass: int} for the PARAM_CRITICALS classes only. A param class
	   with no usable param is dropped: its magnitude IS the effect, so storing it at 0
	   would carry a wound that does nothing. */
	setCriticals: function setCriticals(ship, kind, ref, critMap, paramMap) {
		//The flight-wide target fans out to every ordinal (REF_FLIGHT). Done here rather
		//than in the menu so the ONE door criticals enter a payload by stays one door.
		if (battleDamage.isFlightRef(kind, ref)) {
			return battleDamage.setFlightCriticals(ship, critMap, paramMap);
		}

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
				//Per-class ceiling (default MAX_CRIT_COUNT): some effects are flags the
				//game reads once, so a second would be a wound that does nothing.
				clean[type] = Math.min(n, battleDamage.critLimit(type));
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

	/* ---------------------------------------------------------------- *
	 *  Health-before-Destroy memory (editor only, never submitted)
	 * ---------------------------------------------------------------- */

	/* The remaining health a player had dialled in on ONE target before ticking Destroy,
	   so unticking gives it back instead of silently resetting to full. 0 = nothing
	   remembered (restore full health).

	   ⚠️ WHY IT LIVES ON THE SHIP, keyed per target, and NOT on the React component.
	   `UIManager.showSystemInfoMenu` re-renders the SAME #systemInfoReact root, so clicking
	   a second system reuses the existing ApplyDamageMenu INSTANCE with new props - React
	   reconciles same-type elements in the same position rather than remounting. An instance
	   field therefore leaks from one system to the next: destroy a 36-box Structure, destroy
	   an 8-box gun, untick the Structure, and it came back with EIGHT (user report,
	   2026-08-08, Vree Xonn). Keyed per target it cannot bleed, and as a bonus the undo now
	   survives closing and reopening the menu, exactly like critMemory beside it.

	   Same lifetime rules as critMemory: never submitted (only `preBattleDamage` is copied
	   into the buy/save payloads), dropped by clear() and, per ordinal, by trimOrdinals,
	   and gone when the ship object is rebuilt - the memory is about an editing session,
	   not about the fleet. */
	healthMemory: function healthMemory(ship, kind, ref) {
		if (!ship || !ship.preBattleHealthSeen) return 0;
		var value = parseInt(ship.preBattleHealthSeen[battleDamage.bucketName(kind) + ':' + ref], 10);
		return (value > 0) ? value : 0;
	},

	rememberHealth: function rememberHealth(ship, kind, ref, value) {
		if (!ship) return;
		if (!ship.preBattleHealthSeen || typeof ship.preBattleHealthSeen !== 'object') {
			ship.preBattleHealthSeen = {};
		}

		var key = battleDamage.bucketName(kind) + ':' + ref;
		var remaining = parseInt(value, 10);

		if (remaining > 0) {
			ship.preBattleHealthSeen[key] = remaining;
		} else {
			delete ship.preBattleHealthSeen[key];
		}
	},

	/* Drop everything. For a ship whose phpclass changes, where nothing in the payload
	   still refers to anything real.
	   NOT for a resize any more - a flight or bulk row that changes size is reshaped by
	   onShipRebuilt (trimOrdinals / padOrdinals) rather than emptied. */
	clear: function clear(ship) {
		if (!ship) return;
		ship.preBattleDamage = {};
		delete ship.preBattleCritSeen;
		delete ship.preBattleHealthSeen;
		battleDamage.applyToShip(ship);
	},

	clone: function clone(payload) {
		if (!payload || typeof payload !== 'object') return {};
		return JSON.parse(JSON.stringify(payload));
	},

	/* ⭐ The size whose ORDINALS this unit's payload is keyed by, or null for a unit that
	   has no ordinals at all. ONE helper, because every caller that reports a "before" size
	   to onShipRebuilt has to name the same field this does - otherwise the before/after
	   comparison is between two different quantities and a resize goes unnoticed.

	   ⚠️ MINE IS TESTED FIRST, and that order matters. The 8 flight-shaped MineClass customs
	   are BOTH (`mine` = true, `flight` = true inherited from FighterFlight), and their
	   damage is keyed by copy ordinal 1..bulkBuy like every other mine - never by fighter
	   ordinal. Reading flightSize for them would compare a number that never changes.

	   A bulk OSAT is deliberately null: its payload is per-SYSTEM and applies to every copy
	   in the row (canApplyPreBattleDamage excludes mines only), so changing the quantity
	   does not invalidate any of it. */
	ordinalCount: function ordinalCount(ship) {
		if (!ship) return null;
		if (ship.mine) return battleDamage.mineCount(ship);
		if (ship.flight) return ship.flightSize;
		return null;
	},

	/* Which bucket an ordinal-keyed unit's payload lives in, or null if it has none.
	   ⚠️ MINE FIRST, for the same reason as ordinalCount: the flight-shaped MineClass
	   customs are both, and their damage is keyed by copy ordinal in `mne`. */
	ordinalBucket: function ordinalBucket(ship) {
		if (!ship) return null;
		if (ship.mine) return 'mne';
		if (ship.flight) return 'ftr';
		return null;
	},

	/* Invalidation after the lobby rebuilds a ship's systems from its blueprint (Edit,
	   Copy). The PAYLOAD survives on the ship object, so all that is normally needed is
	   re-rendering the preview onto the new system objects.

	   A RESIZE is the exception. Both kinds of ordinal-keyed unit - a bulk row's copies and
	   a flight's craft - are now RESHAPED rather than thrown away, because an ordinal is a
	   stable identity: mine #3 (or fighter #3) is the same unit before and after the row
	   changed size. So:

	     shrink  - discard exactly the ordinals that no longer exist, keep the rest.
	     grow    - keep everything and PAD the new ordinals with a copy of #1's state, so a
	               flight bought "all lightly damaged" stays that way when it is enlarged
	               instead of silently arriving pristine.

	   This replaces dropping a resized flight's payload wholesale, which lost work on every
	   size change (user report 2026-08-10). The flight-wide critical target (REF_FLIGHT) is
	   ordinal 0 and synthetic - a union computed over the ordinals - so it is untouched by
	   both passes and simply re-derives.

	   Returns true when something was DISCARDED, so the caller can say so. Padding is not a
	   loss and is never reported. */
	onShipRebuilt: function onShipRebuilt(ship, previousSize) {
		var discarded = false;

		var currentSize = battleDamage.ordinalCount(ship);
		var before = parseInt(previousSize, 10);
		var after = parseInt(currentSize, 10);

		var resized = currentSize !== null && currentSize !== undefined
			&& previousSize !== undefined && previousSize !== null
			&& before !== after;

		if (resized && !battleDamage.isEmpty(battleDamage.peek(ship))) {
			if (after < before) {
				discarded = battleDamage.trimOrdinals(ship, after);
			} else {
				battleDamage.padOrdinals(ship, before, after);
			}
		}

		battleDamage.applyToShip(ship);
		return discarded;
	},

	/* Drop every ordinal above `count`, along with the editor-only memories keyed to them.
	   Returns true when anything was actually dropped - shrinking onto ordinals that were
	   never damaged is not worth warning about. */
	trimOrdinals: function trimOrdinals(ship, count) {
		var payload = battleDamage.peek(ship);
		var bucketName = battleDamage.ordinalBucket(ship);
		if (!payload || !bucketName) return false;

		var limit = parseInt(count, 10);
		if (!(limit > 0)) return false;

		var dropped = false;
		var bucket = payload[bucketName];

		for (var ref in bucket) {
			if (!Object.prototype.hasOwnProperty.call(bucket, ref)) continue;
			if (parseInt(ref, 10) <= limit) continue;

			delete bucket[ref];
			if (ship.preBattleCritSeen) delete ship.preBattleCritSeen[bucketName + ':' + ref];
			if (ship.preBattleHealthSeen) delete ship.preBattleHealthSeen[bucketName + ':' + ref];
			dropped = true;
		}

		if (bucket && Object.keys(bucket).length === 0) delete payload[bucketName];

		return dropped;
	},

	/* Fill ordinals `from + 1` .. `to` with a DEEP copy of ordinal 1's entry. #1 is the
	   sample the whole unit is described from elsewhere too (fighterMaxHealth, the lobby's
	   "one sample fighter"), so it is the natural answer to "what does a new craft in this
	   flight look like".
	   Undamaged #1, or an ordinal that somehow already has an entry, leaves the slot empty -
	   padding never overwrites. The editor-only crit/health memories are deliberately NOT
	   copied: they describe an editing session, not the fleet, and a padded ordinal behaves
	   exactly like a freshly loaded one without them. */
	padOrdinals: function padOrdinals(ship, from, to) {
		var payload = battleDamage.peek(ship);
		var bucketName = battleDamage.ordinalBucket(ship);
		if (!payload || !bucketName) return false;

		var bucket = payload[bucketName];
		var sample = bucket && bucket['1'];
		if (!sample) return false;

		var start = Math.max(1, parseInt(from, 10) || 0);
		var end = parseInt(to, 10);
		var padded = false;

		for (var ordinal = start + 1; ordinal <= end; ordinal++) {
			if (bucket[String(ordinal)]) continue;
			bucket[String(ordinal)] = battleDamage.clone(sample);
			padded = true;
		}

		return padded;
	},

	isEmpty: function isEmpty(payload) {
		if (!payload || typeof payload !== 'object') return true;
		return !Object.keys(battleDamage.BUCKETS).some(function (bucketName) {
			var bucket = payload[bucketName];
			return bucket && Object.keys(bucket).length > 0;
		});
	},

	/* {damage, criticals} — mirrors PreBattleDamage::contents. */
	contents: function contents(payload) {
		var has = { damage: false, criticals: false };
		if (!payload || typeof payload !== 'object') return has;

		Object.keys(battleDamage.BUCKETS).forEach(function (bucketName) {
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

		Object.keys(battleDamage.BUCKETS).forEach(function (bucketName) {
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

		//The cascade below needs survivesStructureDestruction, which is protected
		//server-side and only reaches the static blueprint after a ship-bundle regen. Kick
		//the per-class catalogue (de-duplicated, one request per ship class per session)
		//and repaint when it lands, so the preview is right on a tree whose bundle is old.
		//The lobby is the only page that renders a preview at all.
		if (window.gamedata && gamedata.gamephase === -2 && ship.userid != 0) {
			battleDamage.loadCatalogue(ship, function () {
				battleDamage.applyToShip(ship);
				if (window.shipWindowManagerReact) window.shipWindowManagerReact.update();
			});
		}

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

			//survivesStructureDestruction is PROTECTED server-side, so it reaches us
			//either on the static blueprint (needs a ship-bundle regen) or through the
			//per-class catalogue (always current) - this asks both.
			if (battleDamage.survivesStructureDestruction(ship, target)) continue;

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

	/* {critClass: count} for one ordinal, or for the WHOLE FLIGHT when passed REF_FLIGHT —
	   which is what the menu's single Critical Effects section asks for. */
	fighterCriticals: function fighterCriticals(ship, ordinal) {
		var entry = battleDamage.getEntry(ship, battleDamage.KIND_FIGHTER, ordinal);
		return (entry && entry.c) ? entry.c : {};
	},

	/* {critClass: param}, same references — only the PARAM_CRITICALS classes appear. */
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

		//A live mine is its OWN ship, so it summarises as ordinal 1 of a bulk of one;
		//constructSavedShips then merges the group into a single saved unit and renumbers
		//the ordinals over it. Structure only - a mine carries nothing else.
		if (ship.mine) {
			var structure = battleDamage.mineStructure(ship);
			var mineEntry = structure
				? battleDamage.summariseSystem(ship, structure, true, opts) : null;
			//criticals never travel with a mine, whatever a battle put on it
			if (mineEntry) {
				delete mineEntry.c;
				delete mineEntry.p;
				if (mineEntry.d) payload.mne = { '1': { d: mineEntry.d } };
			}
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

		//A reactor is carried wounded, never as a wreck - the same rule the lobby editor
		//enforces, applied here so a fleet saved OUT of a battle cannot reintroduce one.
		if (battleDamage.isIndestructible(system)) damageOnly = true;

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
