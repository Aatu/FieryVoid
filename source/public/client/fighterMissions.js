'use strict';

/* FIGHTER MISSIONS (FIGHTER_MISSIONS_PLAN.md, repo root) - the client half: the labels, the rule that says which
   mission is in force on a turn, the owner's pending pick in Initial Orders, and the readers every hit-chance preview,
   interception test, jinking limit and status line asks.

   WHERE THE DATA COMES FROM: an ELIGIBLE flight's payload always carries ship.fighterMission = {h: [{t, m, k, f}]}
   (FighterMissions::addForJson) - the history of picks, possibly empty - and an ineligible unit never does, so
   "eligible" is simply "has the key". k (a CSP key ship) reaches the flight's own team only; an opponent's payload
   loses the current turn's pick while Initial Orders are open (the EW rule, D8).

   THE PENDING PICK lives on the ship (ship.fighterMissionOrder = {m, k}), Initial Orders only, and counts as a would-be
   entry for the current turn in every reader here - so each preview matches what the commit will do (the Expert
   Evader's pendingEvadeOb precedent). ajaxInterface.construcGamedata sends it as newShip.fighterMission; the server
   checks it (FighterMissions::saveClaim).

   ⚠️ MIRROR PAIRS with FighterMissions.php: missionOn, adjustOffensiveBonus, profileModifier, isDefensiveIntercept,
   iniativeModifier (applyInitiative) and the REGISTRY. The server writes the initiative; the client only shows it. */
window.fighterMissions = {

	CHANGE_PENALTY: -60,
	CSP_INI: 5,
	CSP_RANGE: 10,
	OB_PENALTY: 3,
	OB_FLOOR: 1,
	ASA_OB: 1,
	SCOUT_PROFILE: -1,
	SCOUT_JINK: 1,

	//⚠️ MIRROR of FighterMissions::$registry (PHP), same order. `summary` is the dropdown's one-line effect.
	REGISTRY: [
		{ id: 'NONE', label: 'No Mission', summary: 'The normal role: no mission effects' },
		{ id: 'CSP', label: 'Combat Space Patrol', summary: '+5 Ini within 10 hexes of its key ship; -15 OB Vs. ships' },
		{ id: 'ASA', label: 'Anti-Ship', summary: '+5 to hit ships (not with ballistics); -15 OB Vs. fighters' },
		{ id: 'SCOUT', label: 'Scout Penetration', summary: 'Defence profiles -5, one extra jinking level; -15 OB' },
		{ id: 'DEFINT', label: 'Defensive Intercept', summary: 'Intercepts ballistics aimed at a ship in its hex; -15 OB' }
	],

	getLabel: function getLabel(id) {
		for (var i = 0; i < this.REGISTRY.length; i++) {
			if (this.REGISTRY[i].id === id) return this.REGISTRY[i].label;
		}
		return String(id);
	},

	isEligible: function isEligible(ship) {
		return !!(ship && ship.flight && ship.fighterMission);
	},

	history: function history(ship) {
		return (ship && ship.fighterMission && Array.isArray(ship.fighterMission.h)) ? ship.fighterMission.h : [];
	},

	/* ---------------------------------------------------------------- the rule */

	//the history entry dated `turn`, or null (at most one a turn)
	entryOn: function entryOn(ship, turn) {
		var h = this.history(ship);
		for (var i = 0; i < h.length; i++) {
			if (Number(h[i].t) === Number(turn)) return h[i];
		}
		return null;
	},

	/* The entries the rule reads for `turn`: the history, plus - for the owner, in the Initial Orders still open - the
	   pending pick as an entry for the current turn. */
	entries: function entries(ship, turn) {
		var list = this.history(ship).slice();
		var pending = this.pending(ship);
		if (pending && Number(turn) >= gamedata.turn && !this.entryOn(ship, gamedata.turn)) {
			list.push({ t: gamedata.turn, m: pending.m, k: pending.k || 0, f: this.isFreePick(ship) ? 1 : 0 });
		}
		return list;
	},

	latest: function latest(list, turn, strict) {
		var best = null;
		for (var i = 0; i < list.length; i++) {
			var t = Number(list[i].t);
			if (strict ? t >= Number(turn) : t > Number(turn)) continue;
			if (best === null || t >= Number(best.t)) best = list[i];
		}
		return best;
	},

	/* ⭐ THE MISSION IN FORCE ON `turn` (default: this turn) - ⚠️ MIRROR PAIR with FighterMissions::missionOn:
	     no entry                  -> NONE
	     entry dated turn, !free   -> changing: no mission effects, -60 Ini, the new mission from the next turn
	     otherwise                 -> that entry's mission and key ship
	   Returns { m, k, changing, to }. */
	missionOn: function missionOn(ship, turn) {
		if (turn === undefined || turn === null) turn = gamedata.turn;
		var e = this.isEligible(ship) ? this.latest(this.entries(ship, turn), turn, false) : null;
		if (!e) return { m: 'NONE', k: 0, changing: false, to: null };
		if (Number(e.t) === Number(turn) && !Number(e.f)) return { m: 'NONE', k: 0, changing: true, to: e.m, toKey: Number(e.k) || 0 };
		return { m: e.m, k: Number(e.k) || 0, changing: false, to: null };
	},

	//what the flight had selected before `turn` (default: this turn) - history only: { m, k }
	selected: function selected(ship, turn) {
		if (turn === undefined || turn === null) turn = gamedata.turn;
		var e = this.latest(this.history(ship), turn, true);
		return e ? { m: e.m, k: Number(e.k) || 0 } : { m: 'NONE', k: 0 };
	},

	/* ---------------------------------------------------------------- arrival and choosing */

	//§3.3 - a label only; the server decides `free` when it writes the note
	arrivalTurn: function arrivalTurn(ship) {
		var turn = shipManager.getTurnDeployed(ship);
		if (ship.hangarLaunchTurn !== undefined && ship.hangarLaunchTurn !== null) turn = Math.max(turn, Number(ship.hangarLaunchTurn) + 1);
		return turn;
	},

	isFreePick: function isFreePick(ship) {
		return !!ship && this.arrivalTurn(ship) === gamedata.turn;
	},

	isOnBoard: function isOnBoard(ship) {
		return !!ship && !shipManager.isDestroyed(ship) && shipManager.getTurnDeployed(ship) <= gamedata.turn;
	},

	//May this flight take a pick this turn? Own, eligible, on the board, Initial Orders, none committed yet.
	mayPick: function mayPick(ship) {
		if (!ship || gamedata.gamephase != 1) return false;
		if (!this.isEligible(ship) || !gamedata.isMyShip(ship)) return false;
		if (!this.isOnBoard(ship)) return false;
		return !this.entryOn(ship, gamedata.turn); //already committed this turn (the server allows one a turn)
	},

	//...and may the player change it now - not once their Initial Orders are committed
	canChoose: function canChoose(ship) {
		return !gamedata.waiting && this.mayPick(ship);
	},

	pending: function pending(ship) {
		if (!ship || !ship.fighterMissionOrder || gamedata.gamephase != 1) return null;
		return ship.fighterMissionOrder;
	},

	//Choosing what is already selected clears the pending pick.
	setPending: function setPending(ship, mission, key) {
		if (!this.canChoose(ship)) return;
		var sel = this.selected(ship);
		var k = (mission === 'CSP') ? (Number(key) || 0) : 0;
		if (sel.m === mission && sel.k === k) {
			this.clearPending(ship);
			return;
		}
		ship.fighterMissionOrder = { m: mission, k: k };
	},

	clearPending: function clearPending(ship) {
		if (ship) delete ship.fighterMissionOrder;
	},

	//what construcGamedata sends: the pending pick when it differs from the selected mission, else null
	claim: function claim(ship) {
		var p = this.pending(ship);
		if (!p || !this.mayPick(ship)) return null; //never asks `waiting`: the commit may already have set it
		var sel = this.selected(ship);
		if (sel.m === p.m && sel.k === (Number(p.k) || 0)) return null;
		return { m: p.m, k: Number(p.k) || 0 };
	},

	/* ---------------------------------------------------------------- Combat Space Patrol key ships (D7) */

	keyShipValid: function keyShipValid(flight, key) {
		if (!flight || !key || key.flight || key.mine) return false;
		if (gamedata.isTerrain(key.shipSizeClass, key.userid)) return false;
		if (key.team !== flight.team) return false;
		return this.isOnBoard(key);
	},

	//every unit the flight may name, nearest first: [{ ship, distance, inZone }]
	keyShipCandidates: function keyShipCandidates(flight) {
		var list = [];
		for (var i in gamedata.ships) {
			var s = gamedata.ships[i];
			if (!this.keyShipValid(flight, s)) continue;
			var d = mathlib.getDistanceBetweenShipsInHex(flight, s);
			list.push({ ship: s, distance: d, inZone: d <= this.CSP_RANGE });
		}
		list.sort(function (a, b) { return (a.distance - b.distance) || (a.ship.id - b.ship.id); });
		return list;
	},

	//R3 - within 10 hexes of the key ship, both where they stand now; false while it is not a valid key ship
	inZone: function inZone(flight, keyId) {
		var key = keyId ? gamedata.getShip(keyId) : null;
		if (!this.keyShipValid(flight, key)) return false;
		return mathlib.getDistanceBetweenShipsInHex(flight, key) <= this.CSP_RANGE;
	},

	/* ---------------------------------------------------------------- combat readers */

	//"minimum 5": never below 1 point, never raising an OB already below 1
	penalised: function penalised(ob) {
		return Math.max(Math.min(ob, this.OB_FLOOR), ob - this.OB_PENALTY);
	},

	/* The shooter's offensive bonus for a shot at `target`, before the crit / mine deductions (D5).
	   ⚠️ MIRROR PAIR with FighterMissions::adjustOffensiveBonus (weaponManager.computeOEW). */
	adjustOffensiveBonus: function adjustOffensiveBonus(shooter, target, weapon, ob, turn) {
		if (!this.isEligible(shooter)) return ob;
		var s = this.missionOn(shooter, turn);
		var atFlight = !!(target && target.flight);
		switch (s.m) {
			case 'CSP': return atFlight ? ob : this.penalised(ob);
			case 'ASA':
				if (atFlight) return this.penalised(ob);
				return (weapon && weapon.ballistic) ? ob : ob + this.ASA_OB;
			case 'SCOUT':
			case 'DEFINT':
				return this.penalised(ob);
		}
		return ob;
	},

	//Scout Penetration: the TARGET flight's profiles -1. ⚠️ MIRROR PAIR with FighterMissions::profileModifier.
	profileModifier: function profileModifier(target, turn) {
		if (!this.isEligible(target)) return 0;
		return this.missionOn(target, turn).m === 'SCOUT' ? this.SCOUT_PROFILE : 0;
	},

	//D10 - one jinking level beyond the limit on Scout Penetration, paid in thrust as usual (client only; movement.js canJink)
	extraJinkLevels: function extraJinkLevels(ship) {
		if (!this.isEligible(ship)) return 0;
		return this.missionOn(ship).m === 'SCOUT' ? this.SCOUT_JINK : 0;
	},

	/* §3.6 - what this turn's mission does to the flight's initiative when Initial Orders close: -60 on a change, +5 for a
	   Combat Space Patrol within 10 hexes of its key ship where both stood when the phase closed (the start of the turn).
	   Counts the owner's pending pick, so a choice shows on the tooltip's Ini line at once (user, 2026-10-22). An
	   opponent's payload has no key ship (D8); the server measured the zone for it instead (FighterMissions::
	   addZoneForJson, fighterMission.z) - opponents see the +5 too (user, 2026-10-22).
	   ⚠️ MIRROR PAIR with FighterMissions::applyInitiative (CnC.getIniativeProjection, ShipTooltip's Ini line). */
	iniativeModifier: function iniativeModifier(ship) {
		if (!this.isEligible(ship) || !this.isOnBoard(ship)) return 0;
		var s = this.missionOn(ship);
		if (s.changing) return this.CHANGE_PENALTY;
		if (s.m !== 'CSP') return 0;
		if (!s.k) return ship.fighterMission.z ? this.CSP_INI : 0;
		var key = gamedata.getShip(s.k);
		if (!this.keyShipValid(ship, key)) return 0;
		var from = shipManager.movement.getPositionAtStartOfTurn(ship);
		var to = shipManager.movement.getPositionAtStartOfTurn(key);
		return (from.distanceTo(to) <= this.CSP_RANGE) ? this.CSP_INI : 0;
	},

	//⚠️ MIRROR PAIR with FighterMissions::isDefensiveIntercept (weaponManager.canInterceptBallistic)
	isDefensiveIntercept: function isDefensiveIntercept(ship, turn) {
		if (!this.isEligible(ship)) return false;
		return this.missionOn(ship, turn).m === 'DEFINT';
	},

	//the friendly ships a Defensive Intercept flight covers now: non-flight units of its team in its hex
	covering: function covering(ship) {
		var out = [];
		var pos = shipManager.getShipPosition(ship);
		if (!pos) return out;
		for (var i in gamedata.ships) {
			var s = gamedata.ships[i];
			if (s === ship || s.flight || s.team !== ship.team || shipManager.isDestroyed(s)) continue;
			if (gamedata.isTerrain(s.shipSizeClass, s.userid)) continue;
			var p = shipManager.getShipPosition(s);
			if (p && p.equals(pos)) out.push(s);
		}
		return out;
	},

	/* ---------------------------------------------------------------- what the player reads */

	//"Combat Space Patrol (Omega 1, in zone)" - the key ship only where the payload names it (own team, D8)
	missionText: function missionText(ship, state) {
		var label = this.getLabel(state.m);
		if (state.m === 'CSP' && state.k) {
			var key = gamedata.getShip(state.k);
			if (!key) return label;
			if (!this.keyShipValid(ship, key)) return label + ' (' + key.name + ' lost)';
			return label + ' (' + key.name + ', ' + (this.inZone(ship, state.k) ? 'in zone' : 'outside zone') + ')';
		}
		if (state.m === 'DEFINT' && gamedata.gamephase != 1) {
			var cover = this.covering(ship);
			if (cover.length) return label + ': covering ' + cover.map(function (s) { return s.name; }).join(', ');
		}
		return label;
	},

	/* The tooltip's status line for any viewer: { text, color } or null. Yellow (the Triad's #e1b000) while changing,
	   cyan otherwise; the owner's uncommitted pick reads "Pending". */
	tooltipLine: function tooltipLine(ship) {
		if (!this.isEligible(ship) || !this.isOnBoard(ship)) return null;
		var pending = this.pending(ship);
		if (pending && gamedata.isMyShip(ship)) {
			var cost = this.isFreePick(ship) ? 'No penalty' : this.CHANGE_PENALTY + ' Ini';
			return { kind: 'pending', color: 'cyan', text: 'Pending mission: ' + this.missionText(ship, { m: pending.m, k: pending.k }) + ' (' + cost + ')' };
		}
		var s = this.missionOn(ship);
		if (s.changing) return { kind: 'changing', color: '#e1b000', text: 'Changing mission (' + this.CHANGE_PENALTY + ' Ini) to ' + this.getLabel(s.to) };
		if (s.m === 'NONE') return null;
		return { kind: 'active', color: 'cyan', text: this.missionText(ship, s) };
	},

	//the ship window's banner: { text, tone: 'warning' (changing) | 'pending' } or null
	bannerText: function bannerText(ship) {
		var line = this.tooltipLine(ship);
		if (!line) return null;
		return { text: (line.kind === 'active' ? 'Mission: ' : '') + line.text, tone: line.kind === 'changing' ? 'warning' : 'pending' };
	},

	//the tooltip button's hover line - short, so it never widens the tooltip
	buttonInfo: function buttonInfo(ship) {
		if (!ship) return 'Fighter Mission';
		return this.isFreePick(ship) ? 'Fighter Mission (free this turn)' : 'Fighter Mission (' + this.CHANGE_PENALTY + ' Ini to change)';
	},

	/* R3 - the CSP flights among `ships` that end their move outside their zone; the Movement commit's passive notice */
	outsideZone: function outsideZone(ships) {
		var out = [];
		for (var i = 0; i < ships.length; i++) {
			var s = ships[i];
			if (!this.isEligible(s) || !gamedata.isMyShip(s)) continue;
			var state = this.missionOn(s);
			if (state.m === 'CSP' && state.k && !this.inZone(s, state.k)) out.push(s);
		}
		return out;
	}
};
