"use strict";

/*==========================================================================
  System Enhancements — client state, pricing and lobby preview.
  Design: WEAPON_ENHANCEMENTS_PLAN.md. PHP twin: Enhancements.php, the
  "PER-SYSTEM ENHANCEMENTS" section at the bottom of the file.

  Two arrays travel on a lobby ship, and they are NOT the same shape:

    ship.systemEnhancementOffers   what MAY be bought. Blueprint data, generated
                                   server-side (D3) and never sent back.
                                     [enhID, limit, price, priceStep, systemid]
                                        0      1      2        3          4
                                   `price` is the cost of ONE level; level i costs
                                   price + i*priceStep.

    ship.systemEnhancements        what WAS bought. Authored here, sent on the buy
                                   POST and in a saved fleet, re-derived server-side.
                                     [enhID, label, count, limit, total, priceStep, systemid, sysname]
                                        0      1      2      3      4        5          6         7
                                   ⚠️ `total` is the points for the WHOLE row (all
                                   `count` levels), not one level - it is what
                                   tac_sys_enhancements.enhvalue stores and what a
                                   refund pays back. `sysname` is D13's integrity
                                   check: a stored systemid is POSITIONAL, so the
                                   name is what turns a mis-applied refit into a
                                   dropped one.

  ⚠️ This deliberately does NOT live inside lobbyEnhancements. That module's apply()
  is guarded by a once-per-build `ship.enhancementsApplied` flag, and system
  enhancements are bought AFTER that has already fired - the player buys, un-buys and
  re-buys from an open menu, so this module's apply() has to be re-runnable.

  ⚠️ There is no client-side ELIGIBILITY logic here at all, and that is by design: the
  offer list arrives pre-filtered from the server (D3), so the "what may this system
  have" question lives in exactly one place. The only real mirror in this file is
  APPLIERS - see the comment on apply().
  ==========================================================================*/

window.systemEnhancements = {

	/* enhID -> human name. ⚠️ MIRROR PAIR with $systemEnhancementRegistry's `label` in
	   Enhancements.php. Held here rather than sent per offer because the offer tuple is
	   emitted for every eligible system of every ship in the static blueprints - the
	   labels alone measured 1.2MB across the corpus. */
	LABELS: {
		SYS_ADT: 'Advanced Defensive Targeting',
		SYS_GSGT: 'Gunsights',
		SYS_HSHLD: 'Hardened Shields',
		SYS_HARM: 'Hardened Armour',
		SYS_THR: 'Improved Thrust Rating'
	},

	/* Where a system stashes the blueprint values a refit overwrote. See apply(). */
	BASE_KEY: 'sysEnhBase',

	label: function label(enhID) {
		return this.LABELS[enhID] || enhID;
	},

	/* ---------------------------------------------------------------- access */

	/* The purchase array, created on demand. The static blueprint deliberately omits it
	   (ShipCompactor drops the always-empty key), so every ship gets a FRESH array here
	   rather than inheriting one through a jQuery.extend clone. */
	rows: function rows(ship) {
		if (!ship) return [];
		if (!Array.isArray(ship.systemEnhancements)) ship.systemEnhancements = [];
		return ship.systemEnhancements;
	},

	/* ⚠️ Own lookup rather than shipManager.systems.getSystem: doLoadFleet leaves null
	   HOLES in a flight's systems array and getSystem dereferences without a null check.
	   Same reasoning as battleDamage.findSystem. */
	systemById: function systemById(ship, systemid) {
		if (!ship || !ship.systems) return null;
		var id = parseInt(systemid, 10);
		for (var i in ship.systems) {
			var system = ship.systems[i];
			if (system && parseInt(system.id, 10) === id) return system;
		}
		return null;
	},

	/* Every offer for ONE system, as raw offer tuples. */
	offersFor: function offersFor(ship, system) {
		if (!ship || !system || !Array.isArray(ship.systemEnhancementOffers)) return [];
		var id = parseInt(system.id, 10);
		return ship.systemEnhancementOffers.filter(function (offer) {
			return parseInt(offer[4], 10) === id;
		});
	},

	/* What the menu renders: one plain object per offer, with the current count folded in.
	   The section component is a RENDERER - it never asks "what may this system have". */
	menuRowsFor: function menuRowsFor(ship, system) {
		var self = this;
		return this.offersFor(ship, system).map(function (offer) {
			var count = self.taken(ship, offer[4], offer[0]);
			return {
				enhID: offer[0],
				label: self.label(offer[0]),
				count: count,
				max: parseInt(offer[1], 10) || 0,
				systemid: parseInt(offer[4], 10),
				//points this row costs RIGHT NOW, and what one more level would add
				price: self.priceFor(offer, count),
				nextPrice: self.priceForLevel(offer, count)
			};
		});
	},

	offerFor: function offerFor(ship, systemid, enhID) {
		if (!ship || !Array.isArray(ship.systemEnhancementOffers)) return null;
		var id = parseInt(systemid, 10);
		for (var i = 0; i < ship.systemEnhancementOffers.length; i++) {
			var offer = ship.systemEnhancementOffers[i];
			if (offer[0] === enhID && parseInt(offer[4], 10) === id) return offer;
		}
		return null;
	},

	rowFor: function rowFor(ship, systemid, enhID) {
		var rows = this.rows(ship);
		var id = parseInt(systemid, 10);
		for (var i = 0; i < rows.length; i++) {
			if (rows[i][0] === enhID && parseInt(rows[i][6], 10) === id) return rows[i];
		}
		return null;
	},

	taken: function taken(ship, systemid, enhID) {
		var row = this.rowFor(ship, systemid, enhID);
		return row ? (parseInt(row[2], 10) || 0) : 0;
	},

	/* ---------------------------------------------------------------- pricing */

	/* Cost of the (level+1)th point, 0-based - the same linear rule the server uses.
	   ⚠️ MIRROR PAIR with Enhancements::systemEnhancementPrice. */
	priceForLevel: function priceForLevel(offer, level) {
		if (!offer) return 0;
		return (parseInt(offer[2], 10) || 0) + level * (parseInt(offer[3], 10) || 0);
	},

	/* Cost of buying `count` levels: the SUM of the per-level prices, not count x price.
	   ⚠️ MIRROR PAIR with Enhancements::systemEnhancementTotalPrice. */
	priceFor: function priceFor(offer, count) {
		var n = parseInt(count, 10) || 0;
		if (!offer || n < 1) return 0;
		var price = parseInt(offer[2], 10) || 0;
		var step = parseInt(offer[3], 10) || 0;
		return n * price + step * (n * (n - 1) / 2);
	},

	/* Sum of every purchased row -> ship.pointCostSysEnh (D5, the THIRD cost bucket).
	   It cannot share pointCostEnh or pointCostEnh2: readBulkPurchase and doEditShip
	   rewrite both of those from zero off the buy dialog's spinners, and system
	   enhancements are not in that dialog - so folding them in means an Edit silently
	   REFUNDS every refit while leaving it applied. */
	totalCost: function totalCost(ship) {
		var total = 0;
		this.rows(ship).forEach(function (row) {
			if ((parseInt(row[2], 10) || 0) > 0) total += parseFloat(row[4]) || 0;
		});
		if (ship) ship.pointCostSysEnh = total;
		return total;
	},

	/* How many purchased ROWS this ship carries. Used only as a "has it got any?" test - the
	   summary line counts SYSTEMS, see systemsEnhanced(). */
	count: function count(ship) {
		var n = 0;
		this.rows(ship).forEach(function (row) {
			if ((parseInt(row[2], 10) || 0) > 0) n++;
		});
		return n;
	},

	/* How many DISTINCT SYSTEMS carry a refit - what "System Enhancements (n)" reports.
	   Systems, not rows: the number the player is being told is the number of ✦ badges they
	   will find on the ship window, and a gun wearing both Gunsights and Advanced Defensive
	   Targeting is still one enhanced gun (user request, 2026-08-15). Counting rows made the
	   summary drift away from the badges for exactly the ships that had the most going on.
	   ⚠️ MIRROR PAIR with the same count in Enhancements::setSystemEnhancements (PHP). */
	systemsEnhanced: function systemsEnhanced(ship) {
		var seen = {};
		var n = 0;
		this.rows(ship).forEach(function (row) {
			if ((parseInt(row[2], 10) || 0) < 1) return;
			var id = parseInt(row[6], 10);
			if (seen[id]) return;
			seen[id] = true;
			n++;
		});
		return n;
	},

	/* Does THIS system carry any refit? Drives the gold star on the icon. */
	hasAny: function hasAny(ship, systemid) {
		var id = parseInt(systemid, 10);
		return this.rows(ship).some(function (row) {
			return parseInt(row[6], 10) === id && (parseInt(row[2], 10) || 0) > 0;
		});
	},

	/* ---------------------------------------------------------------- writing */

	/* Buy / un-buy / change one refit. Writes the row and re-prices; does NOT apply -
	   callers run apply() once after a batch of writes. Clamped to the offer's limit.
	   Returns the count actually stored. */
	set: function set(ship, systemid, enhID, count) {
		var offer = this.offerFor(ship, systemid, enhID);
		if (!offer) return 0;

		var limit = parseInt(offer[1], 10) || 0;
		var n = parseInt(count, 10) || 0;
		if (n < 0) n = 0;
		if (n > limit) n = limit;

		var rows = this.rows(ship);
		var id = parseInt(systemid, 10);
		var index = -1;
		for (var i = 0; i < rows.length; i++) {
			if (rows[i][0] === enhID && parseInt(rows[i][6], 10) === id) { index = i; break; }
		}

		if (n === 0) {
			if (index !== -1) rows.splice(index, 1);
			this.totalCost(ship);
			this.syncTooltip(ship);
			return 0;
		}

		var system = this.systemById(ship, id);
		var row = [
			enhID,
			this.label(enhID),
			n,
			limit,
			this.priceFor(offer, n),          //index 4 = TOTAL for the row
			parseInt(offer[3], 10) || 0,
			id,
			system ? String(system.name) : '' //D13 integrity check
		];
		if (index === -1) rows.push(row); else rows[index] = row;

		this.totalCost(ship);
		this.syncTooltip(ship);
		return n;
	},

	/* Drop every refit - a phpclass change, where the stored systemids mean nothing any
	   more. Returns the points refunded. */
	clear: function clear(ship) {
		var refunded = this.totalCost(ship);
		if (ship) {
			this.revert(ship);
			ship.systemEnhancements = [];
			ship.pointCostSysEnh = 0;
			this.syncTooltip(ship);
		}
		return refunded;
	},

	/* DEEP copy. ⚠️ Sharing the array - or its ROWS, which every write replaces in place -
	   would make editing one copied row edit the other, exactly the bug
	   cloneEnhancementOptions was written to fix. */
	clone: function clone(payload) {
		if (!Array.isArray(payload)) return [];
		return payload.map(function (row) {
			return Array.isArray(row) ? row.slice() : row;
		});
	},

	/* ---------------------------------------------------------------- apply / revert */

	/* Remember a system's BLUEPRINT value for one field, once, before anything writes it.
	   Arrays are copied, not referenced: client system objects share field references
	   across same-phpclass instances, so stashing the live array would stash a thing that
	   is about to change underneath us. */
	rememberBase: function rememberBase(system, field) {
		var key = this.BASE_KEY;
		if (!system[key]) system[key] = {};
		if (Object.prototype.hasOwnProperty.call(system[key], field)) return;
		system[key][field] = this.copyValue(system[field]);
	},

	copyValue: function copyValue(value) {
		if (Array.isArray(value)) return value.map(systemEnhancements.copyValue);
		if (value && typeof value === 'object') {
			var out = {};
			for (var k in value) {
				if (Object.prototype.hasOwnProperty.call(value, k)) out[k] = systemEnhancements.copyValue(value[k]);
			}
			return out;
		}
		return value;
	},

	/* Put every touched field back to the value it had before any refit was applied, and
	   forget the stash. The edit/rebuild path and apply() both use it. */
	revert: function revert(ship) {
		if (!ship || !ship.systems) return;
		var key = this.BASE_KEY;
		for (var i in ship.systems) {
			var system = ship.systems[i];
			if (!system || !system[key]) continue;
			for (var field in system[key]) {
				if (!Object.prototype.hasOwnProperty.call(system[key], field)) continue;
				system[field] = this.copyValue(system[key][field]);
			}
			delete system[key];
		}
	},

	/* Apply every purchased refit to the ship's systems.

	   ⚠️ RE-APPLICATION IS THE HAZARD HERE, NOT APPLICATION. Every effect is a cumulative
	   +=, and the player buys, un-buys and re-buys from an open menu repeatedly. So this
	   is IDEMPOTENT FROM THE BLUEPRINT rather than incremental: revert() first, then write
	   `original + totalLevels` from the remembered baseline. lobbyEnhancements gets away
	   with a plain += only because it runs exactly once per build.

	   ⚠️ Every array write builds a NEW array. Client system objects share field references
	   across same-phpclass instances, so mutating fireControl in place on one Omega would
	   change it on every Omega in the fleet.

	   ⚠️ MIRROR PAIR with Enhancements::setSystemEnhancements (PHP). This is the one real
	   mirror in this file - the appliers. Change both. */
	apply: function apply(ship) {
		if (!ship || !ship.systems) return;

		this.revert(ship);   //back to blueprint, so what follows cannot compound

		var self = this;
		this.rows(ship).forEach(function (row) {
			var count = parseInt(row[2], 10) || 0;
			if (count < 1) return;

			var system = self.systemById(ship, row[6]);
			if (!system) return;                                      //stale id - drop, never guess
			if (row[7] && String(system.name) !== String(row[7])) return;  //D13

			switch (row[0]) {
				case 'SYS_ADT':
					self.rememberBase(system, 'intercept');
					system.intercept = (parseInt(system[self.BASE_KEY].intercept, 10) || 0) + count;
					/* Mirror across firing modes. changeFiringMode re-reads interceptArray[mode]
					   straight over ->intercept, so a bump to the scalar alone evaporates on the
					   first mode switch. Only NON-ZERO entries: a mode carrying non-interceptor
					   ammo has rating 0 and must keep it. */
					if (Array.isArray(system.interceptArray) || (system.interceptArray && typeof system.interceptArray === 'object')) {
						self.rememberBase(system, 'interceptArray');
						system.interceptArray = self.bumpMap(system[self.BASE_KEY].interceptArray, count, function (v) {
							return (parseInt(v, 10) || 0) > 0;
						});
					}
					self.syncInterceptData(system);
					break;

				case 'SYS_GSGT':
					self.rememberBase(system, 'fireControl');
					system.fireControl = self.bumpFireControl(system[self.BASE_KEY].fireControl, count);
					/* ⚠️ THE single most important trap in this feature. changeFiringMode re-reads
					   fireControlArray[mode] STRAIGHT OVER fireControl, so a refit that bumps only
					   fireControl evaporates on the player's first mode switch - silently. */
					if (system.fireControlArray && typeof system.fireControlArray === 'object') {
						self.rememberBase(system, 'fireControlArray');
						var base = system[self.BASE_KEY].fireControlArray;
						var out = Array.isArray(base) ? [] : {};
						for (var mode in base) {
							if (!Object.prototype.hasOwnProperty.call(base, mode)) continue;
							out[mode] = Array.isArray(base[mode])
								? self.bumpFireControl(base[mode], count)
								: base[mode];
						}
						system.fireControlArray = out;
					}
					self.syncFireControlData(system);
					break;

				case 'SYS_HARM':
					self.rememberBase(system, 'armour');
					system.armour = (parseInt(system[self.BASE_KEY].armour, 10) || 0) + count;
					break;

				case 'SYS_HSHLD':
				case 'SYS_THR':
					self.rememberBase(system, 'output');
					system.output = (parseInt(system[self.BASE_KEY].output, 10) || 0) + count;
					/* A shield's to-hit and damage penalties are derived from its output by
					   Shield::onConstructed server-side; the lobby preview has no such hook, so
					   keep them in step here or the ship window shows a shield whose rating moved
					   and whose penalties did not. */
					if (row[0] === 'SYS_HSHLD') {
						if (system.tohitPenalty !== undefined) {
							self.rememberBase(system, 'tohitPenalty');
							system.tohitPenalty = (parseInt(system[self.BASE_KEY].tohitPenalty, 10) || 0) + count;
						}
						if (system.damagePenalty !== undefined) {
							self.rememberBase(system, 'damagePenalty');
							system.damagePenalty = (parseInt(system[self.BASE_KEY].damagePenalty, 10) || 0) + count;
						}
					}
					break;
			}
		});
	},

	/* +count to every NON-NULL entry, as a NEW array.
	   ⚠️ null means "cannot target this size class" and MUST stay null: `null + 1` is 1 in
	   JS as in PHP, which would silently give a Piercing missile a fighter-targeting
	   capability it does not have. Guard on null, never on truthiness - 0 is a legal fire
	   control, and so is a negative one. */
	bumpFireControl: function bumpFireControl(fireControl, count) {
		if (!Array.isArray(fireControl)) return fireControl;
		return fireControl.map(function (fc) {
			return (fc === null || fc === undefined) ? fc : (parseInt(fc, 10) || 0) + count;
		});
	},

	/* ---------------------------------------------------------------- info-window text */

	/* ⭐ THE reason two of the five refits looked like they did nothing (user report 2026-08-15).
	   A system's SystemInfo tooltip renders `system.data` VERBATIM, and for a weapon the two
	   numbers these refits move are quoted ONLY through it:

	     data["Intercept"]                        <- system.intercept
	     data["Fire control (fighter/med/cap)"]   <- system.fireControl

	   The other three refits move `output` and `armour`, which the ship window reads off the live
	   field - so they appeared correctly from day one and these two silently did not. `data` is
	   baked into the STATIC BLUEPRINT at generation time, long before any refit is bought, so
	   bumping the live field alone leaves the tooltip quoting the hull as designed.

	   In GAME the server does this job instead, by re-sending the whole recomputed `data` for the
	   enhanced system (the registry's `serialise` list in Enhancements.php). Here in the lobby
	   there is no round trip, so the two entries are rewritten in place.
	   ⚠️ MIRROR PAIR with Weapon::setSystemDataWindow / Weapon::formatFCValue (weapon.php) - if the
	   wording of either KEY changes there, it changes here, and the only symptom is a tooltip that
	   goes back to quoting the blueprint. */
	DATA_KEY_INTERCEPT: 'Intercept',
	DATA_KEY_FIRECONTROL: 'Fire control (fighter/med/cap)',

	syncInterceptData: function syncInterceptData(system) {
		//"-15" - the rating reads as a to-hit PENALTY of 5% per point.
		this.setDataEntry(system, this.DATA_KEY_INTERCEPT,
			'-' + ((parseInt(system.intercept, 10) || 0) * 5));
	},

	syncFireControlData: function syncFireControlData(system) {
		if (!Array.isArray(system.fireControl)) return;
		this.setDataEntry(system, this.DATA_KEY_FIRECONTROL,
			this.formatFC(system.fireControl[0]) + '/'
			+ this.formatFC(system.fireControl[1]) + '/'
			+ this.formatFC(system.fireControl[2]));
	},

	/* ⚠️ MIRROR of Weapon::formatFCValue. null means "cannot target this size class" and shows a
	   dash; everything else is the fire control as a percentage. Guard on null, never on
	   truthiness - 0 is a legal fire control and prints as "0". */
	formatFC: function formatFC(fc) {
		if (fc === null || fc === undefined) return '-';
		return String((parseInt(fc, 10) || 0) * 5);
	},

	/* Rewrite ONE key of a system's info-window map.

	   ⚠️ Always a NEW object, never an in-place write: `data` is shared BY REFERENCE across
	   same-phpclass systems, so retitling one Twin Array's tooltip in place would retitle every
	   Twin Array in the fleet - the same trap the applier's array writes guard against.

	   ⚠️ Never INVENTS a row. weapon.php emits "Intercept" only when the rating is above zero, and
	   several classes override setSystemDataWindow without calling the parent at all - so a key
	   that is not already there is a system whose tooltip is built some other way, and adding one
	   would put a line in it the game never shows anywhere else. */
	setDataEntry: function setDataEntry(system, key, value) {
		if (!system || !system.data || typeof system.data !== 'object') return;
		if (!Object.prototype.hasOwnProperty.call(system.data, key)) return;

		this.rememberBase(system, 'data');   //blueprint copy, stashed once - revert() puts it back

		var next = {};
		for (var k in system.data) {
			if (Object.prototype.hasOwnProperty.call(system.data, k)) next[k] = system.data[k];
		}
		next[key] = value;
		system.data = next;
	},

	/* +count to every entry of a mode-keyed map that passes `test`, as a NEW map. */
	bumpMap: function bumpMap(map, count, test) {
		if (!map || typeof map !== 'object') return map;
		var out = Array.isArray(map) ? [] : {};
		for (var k in map) {
			if (!Object.prototype.hasOwnProperty.call(map, k)) continue;
			out[k] = test(map[k]) ? (parseInt(map[k], 10) || 0) + count : map[k];
		}
		return out;
	},

	/* ---------------------------------------------------------------- D11 */

	/* Remove - and refund - every refit sitting on a DESTROYED system, one way.

	   ⚠️ MUST run AFTER battleDamage.applyToShip has written the preview, because that is
	   what performs the STRUCTURE CASCADE: destroying a Structure block destroys every
	   system in its location, and sweeping the payload's `k` flags instead would leave
	   refits alive on systems that are in fact wrecked.

	   One-way by decision (D11): un-ticking Destroy does not bring the refit back. The
	   player re-buys it, which is honest and matches how a flight-size change discards
	   pre-battle damage rather than trying to re-map it.

	   Returns the rows removed, so the caller can name them in ONE warning - a refund the
	   player does not see reads as a pricing bug. */
	dropDestroyed: function dropDestroyed(ship) {
		if (!ship || !ship.systems) return [];
		var rows = this.rows(ship);
		if (!rows.length) return [];

		var self = this;
		var removed = [];
		var kept = rows.filter(function (row) {
			var system = self.systemById(ship, row[6]);
			if (system && system.destroyed) {
				removed.push({
					enhID: row[0],
					label: self.label(row[0]),
					points: parseFloat(row[4]) || 0,
					systemid: parseInt(row[6], 10),
					systemName: system.displayName || system.name || row[7]
				});
				return false;
			}
			return true;
		});

		if (!removed.length) return [];

		ship.systemEnhancements = kept;
		this.totalCost(ship);
		this.syncTooltip(ship);
		this.apply(ship);   //re-apply from blueprint so the removed rows stop being applied
		return removed;
	},

	/* One sentence naming what went, for confirm.warning. */
	describeRemoved: function describeRemoved(removed) {
		if (!removed || !removed.length) return '';
		return removed.map(function (r) {
			return r.systemName + ' #' + r.systemid + ' was destroyed; its ' + r.label
				+ ' refit (' + r.points + ' pts) has been removed.';
		}).join(' ');
	},

	/* ---------------------------------------------------------------- summary */

	/* The single line lobbyEnhancements.apply appends to the ship-window gold box, and the
	   server writes into the in-game enhancementTooltip. Twelve separate lines would push
	   the enh grid panel past the section cluster it sits beside, so this is deliberately
	   a count with the detail left to each system's own SystemInfo tooltip.
	   The number is SYSTEMS ENHANCED, not refits bought - it is the count of ✦ badges on the
	   ship window, which is what the player can actually go and look at.
	   ⚠️ MIRROR PAIR with the same line in Enhancements::setSystemEnhancements (PHP). */
	summaryLine: function summaryLine(ship) {
		var n = this.systemsEnhanced(ship);
		return n > 0 ? 'System Enhancements (' + n + ')' : null;
	},

	/* Keep the ship-window gold box's summary line in step as refits are bought and sold.

	   ⚠️ Needed because lobbyEnhancements.apply is guarded by a once-per-build
	   `ship.enhancementsApplied` flag: it writes the tooltip when the ship is BUILT, and
	   system enhancements are bought long afterwards from an open menu. Rewriting just this
	   one line - rather than re-running apply(), which would compound every ship-level
	   effect - is the only version that is safe to call on every keystroke. */
	syncTooltip: function syncTooltip(ship) {
		if (!ship) return;
		var lines = String(ship.enhancementTooltip || '')
			.split('<br>')
			.filter(function (line) {
				return line !== '' && line.indexOf('System Enhancements (') !== 0;
			});
		var line = this.summaryLine(ship);
		if (line) lines.push(line);
		ship.enhancementTooltip = lines.join('<br>');
	}
};
