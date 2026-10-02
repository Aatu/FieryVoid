"use strict";

/*==========================================================================
  Save Orders - SAVE_ORDERS_PLAN.md (client: Stages 2-3).

  The floppy beside the commit tick parks the orders a player has given so far in
  the current phase WITHOUT committing them. When they open the game again - here or
  on another device - the page puts those orders back exactly as they were, and they
  carry on and commit as normal. Initial Orders (1), Pre-Firing (5) and Firing (3)
  (ruling D2), and Deployment / Pre-Turn Orders (-1, Stages 5-6). Never Movement, which
  commits one activation at a time.

  ⭐ THE INVARIANT (plan §1.1): a restored draft puts the client back into exactly
  the state it was in when Save was clicked, for the order kinds that phase's commit
  sends - nothing more. If that holds, a commit after a reload is indistinguishable
  from one without, and none of the server's validation, masking or resolution has to
  know drafts exist. The server never reads a draft; it only stores it for its author.

  ⚠️ THE CAPTURE IS READ-ONLY (traps T1/T2). Never build a draft with
  ajaxInterface.construcGamedata(), doIndividualNotesTransfer() or any doCommit check:
  they MUTATE - a Hangar empties its launch queue as it serialises it, a Shield
  Reinforcement drops its boosts, ew.convertUnusedToDEW adds DEW
  (arch_commit_time_client_side_effects). Everything here reads the arrays (EW, power,
  fireOrders, movement) and named system fields directly.

  ⚠️ THE RESTORE REPLACES, NEVER ADDS (trap T3). It runs once, straight after the phase
  strategy activates - which has already re-copied last turn's power and re-declared any
  standing abduction or maintained vortex - and swaps this turn's entries for the
  draft's, on EVERY system of every ship in the draft. An entry the player removed
  before saving (a re-declared order they cancelled) must stay removed, so a system the
  draft says nothing about is reset to nothing.

  Transport (plan §1.6): saveOrders.php, actions "save" and "clear". game.php inlines
  the player's draft for the current turn and phase as window.fvSavedOrders, which
  applyPending consumes on first use. Nothing else carries a draft.

  Draft format, version 1 (plan §1.2) - keyed by ship, system and fighter id:
    { v, turn, phase, ships: { <shipid>: {
        EW:   [this turn's EW entries],
        move: [this turn's uncommitted combat pivots (Firing), or deploy rows (Deployment)]
        ship: { arrivalVia, arrivalSpeed, arrivalHangar }       (Initial Orders only)
        dock: { pendingDeployDock, pendingLcvDeployDock, forcedDeployDock }   (Deployment only)
        sys:  { <systemid>: { power, fire, mode, state } },
        ftr:  { <fighterid>: { <systemid>: { power, fire, mode, state } } },
        gate: true      (an UNOWNED jump gate carrying this player's signal - see captureGate) } } }
  System ids are positional (construction order), which is safe because a draft never
  outlives its turn and phase (plan §0.6).
  ==========================================================================*/

window.savedOrders = {

	VERSION: 1,

	//What this page knows about the server's copy: when it was saved, and for which turn and
	//phase. Set from game.php's inline draft on load, or by a save made on this page.
	savedAt: null,
	savedTurn: null,
	savedPhase: null,

	busy: false,        //a save or discard request is in flight
	panelState: null,   //last state written to the OPTIONS block - see refreshPanel (trap T12)
	noticeTimer: null,

	isSavePhase: function isSavePhase(phase) {
		phase = Number(phase);
		return phase === -1 || phase === 1 || phase === 5 || phase === 3;
	},

	//The deploy-dock markers a unit carries while it is queued to start the battle inside a carrier
	//(DeploymentDock.js). Deployment only.
	DOCK_KEYS: ['pendingDeployDock', 'pendingLcvDeployDock', 'forcedDeployDock'],

	phaseName: function phaseName(phase) {
		switch (Number(phase)) {
			case -1:
				//The same split the phase header makes (gamedata.getPhasename).
				return (window.shipManager && shipManager.hasShipsToDeployThisTurn(gamedata.thisplayer))
					? "Deployment" : "Pre-Turn Orders";
			case 1: return "Initial Orders";
			case 5: return "Pre-Firing";
			case 3: return "Firing";
		}
		return "";
	},

	/* Could this viewer save right now? A player in the game, in a live game, in a saveable phase,
	   with orders still to give - the moments the commit tick shows in phases 1, 5 and 3. */
	canSaveNow: function canSaveNow() {
		if (!window.gamedata || !gamedata.gameid) return false;
		if (gamedata.replay || gamedata.waiting) return false;
		if (gamedata.status !== "ACTIVE") return false;
		if (!savedOrders.isSavePhase(gamedata.gamephase)) return false;
		return gamedata.isPlayerInGame();
	},

	/* Does the server hold a copy for the phase on screen? Only as far as this page knows: a save
	   made on another device after this page loaded is not seen until it reloads. */
	hasSaveForThisPhase: function hasSaveForThisPhase() {
		return savedOrders.savedAt !== null
			&& savedOrders.savedTurn === Number(gamedata.turn)
			&& savedOrders.savedPhase === Number(gamedata.gamephase);
	},

	rememberSave: function rememberSave(savedAt, turn, phase) {
		savedOrders.savedAt = Number(savedAt) || null;
		savedOrders.savedTurn = Number(turn);
		savedOrders.savedPhase = Number(phase);
	},

	/* A fresh, unshared copy. Every array and object that goes into a draft or comes back out of
	   one goes through here, so nothing in the live game ever holds a reference into a draft and
	   no two systems ever share one restored object (trap T8). */
	copy: function copy(value) {
		return JSON.parse(JSON.stringify(value));
	},

	thisTurn: function thisTurn(list, turn) {
		var out = [];
		if (!list) return out;
		for (var i = 0; i < list.length; i++) {
			if (list[i] && list[i].turn == turn) out.push(list[i]);
		}
		return out;
	},

	/* Drop this turn's entries from a live array IN PLACE, then append fresh copies of the
	   draft's. In place, not by assigning a new array: a ship window or menu may hold the array
	   itself, and must see the restored entries rather than an orphaned old list. */
	replaceThisTurn: function replaceThisTurn(list, turn, entries) {
		for (var i = list.length - 1; i >= 0; i--) {
			if (list[i] && list[i].turn == turn) list.splice(i, 1);
		}
		if (entries && entries.length) {
			var copies = savedOrders.copy(entries);
			for (var c = 0; c < copies.length; c++) list.push(copies[c]);
		}
	},

	/* ============================== CAPTURE ============================== */

	capture: function capture() {
		var turn = Number(gamedata.turn);
		var phase = Number(gamedata.gamephase);
		var draft = { v: savedOrders.VERSION, turn: turn, phase: phase, ships: {} };

		for (var i in gamedata.ships) {
			var ship = gamedata.ships[i];
			if (!ship) continue;

			if (ship.userid === gamedata.thisplayer) {
				//EVERY own ship gets an entry, even an empty one: the restore resets each drafted
				//ship to exactly what the draft holds, and leaves a ship it does not list alone.
				draft.ships[ship.id] = savedOrders.captureShip(ship, turn, phase);
			} else {
				var gate = savedOrders.captureGate(ship, turn, phase);
				if (gate) draft.ships[ship.id] = gate;
			}
		}

		//Through JSON once, so the draft is plain data and shares nothing with the live game.
		return savedOrders.copy(draft);
	},

	captureShip: function captureShip(ship, turn, phase) {
		var rec = {};

		var ew = savedOrders.thisTurn(ship.EW, turn);
		if (ew.length) rec.EW = ew;

		//Firing is the only one of the three phases whose commit carries movement - the combat
		//pivots (movement.js doPivot). A row the server sent has a real id; only id -1 is ours.
		if (phase === 3) {
			var moves = [];
			for (var m = 0; m < (ship.movement || []).length; m++) {
				var move = ship.movement[m];
				if (move && move.turn == turn && move.id == -1 && move.value == "combatpivot") moves.push(move);
			}
			if (moves.length) rec.move = moves;
		}

		/* Deployment (Stage 5): the placement. Every uncommitted row of this turn - in practice the
		   one `deploy` row shipManager.movement.deploy() makes, which the speed and turn arrows
		   edit in place and which carries a base's rotation in its `value`. `ship.deploymove` is that
		   same object and is re-pointed at the restored copy (applyShip). */
		if (phase === -1) {
			var placed = [];
			for (var d = 0; d < (ship.movement || []).length; d++) {
				var row = ship.movement[d];
				if (row && row.turn == turn && row.id == -1) placed.push(row);
			}
			if (placed.length) rec.move = placed;

			//...and a unit queued to start inside a carrier rather than on the board (Stage 6).
			var dock = {};
			savedOrders.DOCK_KEYS.forEach(function (key) {
				if (ship[key] !== undefined && ship[key] !== null && ship[key] !== false) dock[key] = ship[key];
			});
			if (Object.keys(dock).length) rec.dock = dock;
		}

		//The Jump Manifest (REINFORCEMENTS_PLAN.md §3.5): the berth, speed and carrier a unit in
		//hyperspace comes out with, sent by the Initial Orders commit. Only a reinforcement rides a
		//doorway - every other unit carries the blueprint's nulls - so it is kept for those, and for
		//anything that somehow holds a value. A cleared berth is null, which is why a reinforcement
		//is kept even with all three empty.
		var hasManifest = function (v) { return v !== undefined && v !== null; };
		if (phase === 1 && (ship.reinforcement
			|| hasManifest(ship.arrivalVia) || hasManifest(ship.arrivalSpeed) || hasManifest(ship.arrivalHangar))) {
			rec.ship = {
				arrivalVia: (ship.arrivalVia === undefined) ? null : ship.arrivalVia,
				arrivalSpeed: (ship.arrivalSpeed === undefined) ? null : ship.arrivalSpeed,
				arrivalHangar: (ship.arrivalHangar === undefined) ? null : ship.arrivalHangar
			};
		}

		if (ship.flight) {
			var ftr = {};
			for (var f in ship.systems) {
				var fighter = ship.systems[f];
				if (!fighter) continue;
				var fsys = {};
				var any = false;
				for (var s in fighter.systems) {
					var fs = fighter.systems[s];
					var r = fs ? savedOrders.captureSystem(fs, turn, phase) : null;
					if (r) {
						fsys[fs.id] = r;
						any = true;
					}
				}
				if (any) ftr[fighter.id] = fsys;
			}
			if (Object.keys(ftr).length) rec.ftr = ftr;
		} else {
			var sys = {};
			for (var a in ship.systems) {
				var system = ship.systems[a];
				var rs = system ? savedOrders.captureSystem(system, turn, phase) : null;
				if (rs) sys[system.id] = rs;
			}
			if (Object.keys(sys).length) rec.sys = sys;
		}

		return rec;
	},

	/* One system's share of the draft, or null when it holds nothing for this turn. */
	captureSystem: function captureSystem(system, turn, phase) {
		var rec = {};
		var any = false;

		//Power is committed by Initial Orders alone - PreFiringGamePhase and FireGamePhase both
		//leave it out of process() - so it is kept for that phase alone.
		if (phase === 1 && system.power) {
			var power = savedOrders.thisTurn(system.power, turn);
			if (power.length) {
				rec.power = power;
				any = true;
			}
		}

		var fire = savedOrders.thisTurn(system.fireOrders, turn);
		if (fire.length) {
			rec.fire = fire;
			any = true;
			//The mode the weapon SHOWS lives on the weapon; every order carries its own (trap T9).
			if (system.firingMode !== undefined) rec.mode = system.firingMode;
		}

		//Stage 3 (plan §1.4): settings a few systems hold outside the arrays above.
		if (typeof system.getDraftState === "function") {
			var state = system.getDraftState(phase);
			if (state) {
				rec.state = state;
				any = true;
			}
		}

		return any ? rec : null;
	},

	/* ⭐ JUMP_GATES_PLAN.md §3.1 - THE ONE UNIT A PLAYER MAY ORDER WITHOUT OWNING IT. Any player
	   may signal a fixed jump gate in Initial Orders, and construcGamedata posts that signal on the
	   gate even when the enemy owns it - so the draft has to carry it too, or a restored phase
	   would quietly lose it. Every current-turn order on a gate's engine in phase 1 is one THIS
	   client made (TacGamedata::hideSystemFireOrders strips the rest from every phase-1 payload -
	   see ajaxInterface.getGateSignalOrders), so nothing belonging to anyone else is taken. */
	captureGate: function captureGate(ship, turn, phase) {
		if (phase !== 1 || !gamedata.isJumpGate(ship)) return null;

		var sys = {};
		for (var a in ship.systems) {
			var system = ship.systems[a];
			if (!system || system.name !== "jumpEngine") continue;
			var fire = savedOrders.thisTurn(system.fireOrders, turn);
			if (fire.length) sys[system.id] = { fire: fire };
		}

		return Object.keys(sys).length ? { gate: true, sys: sys } : null;
	},

	/* ============================== RESTORE ============================== */

	/* Called by PhaseDirector at the end of every phase-strategy activation (plan §1.3). The
	   pending draft is consumed by the first activation that has the game's data, so a later
	   phase change in the same session never sees it. */
	onStrategyActivated: function onStrategyActivated(gd, strategy) {
		savedOrders.applyPending(gd, strategy);
		savedOrders.syncButton();
		savedOrders.refreshPanel();
	},

	applyPending: function applyPending(gd, strategy) {
		//Before parseServerData has run there is no phase to compare with - wait for the
		//activation that has one rather than spending the draft on this one.
		if (!gd || !gd.gameid) return;

		var pending = window.fvSavedOrders;
		if (!pending) return;
		window.fvSavedOrders = null;

		if (gd.replay || gd.waiting || gd.status !== "ACTIVE") return;
		if (!savedOrders.isSavePhase(gd.gamephase)) return;

		var draft = pending.draft;
		if (!draft || draft.v !== savedOrders.VERSION) return;   //a format this page cannot read
		if (Number(draft.turn) !== Number(gd.turn) || Number(draft.phase) !== Number(gd.gamephase)) return;

		//The server copy exists whatever happens below - remember it so OPTIONS can discard it.
		savedOrders.rememberSave(pending.savedAt, gd.turn, gd.gamephase);

		var result;
		try {
			result = savedOrders.apply(draft, strategy);
		} catch (e) {
			//A draft that cannot be applied must never take the phase down with it. Whatever went
			//in before the throw stays; Discard in OPTIONS gives the player a clean phase.
			console.error("Save Orders: restore failed", e);
			savedOrders.showNotice("Your saved orders could not be restored. Discard them in the OPTIONS tab.");
			return;
		}

		var text = "Saved orders from " + savedOrders.formatTime(pending.savedAt) + " restored.";
		if (result.skipped > 0) {
			text += " " + result.skipped + (result.skipped === 1 ? " order" : " orders") + " could not be placed.";
		}
		text += " Discard them in the OPTIONS tab.";
		savedOrders.showNotice(text);
	},

	/* Put a draft back. Returns {ships, skipped}. Ships are looked up with gamedata.getShip -
	   the client has no getShipById - and anything that does not resolve is skipped and counted. */
	apply: function apply(draft, strategy) {
		var turn = Number(gamedata.turn);
		var phase = Number(gamedata.gamephase);
		var restored = [];
		var skipped = 0;

		for (var shipId in draft.ships) {
			var rec = draft.ships[shipId] || {};
			var ship = gamedata.getShip(shipId);

			if (rec.gate) {
				if (!ship || !gamedata.isJumpGate(ship)) {
					skipped += savedOrders.countShipOrders(rec);
					continue;
				}
				skipped += savedOrders.applyGate(ship, rec, turn, phase);
				restored.push({ ship: ship, moved: false });
				continue;
			}

			//The same ownership test construcGamedata applies to what it posts.
			if (!ship || ship.userid !== gamedata.thisplayer) {
				skipped += savedOrders.countShipOrders(rec);
				continue;
			}

			var out = savedOrders.applyShip(ship, rec, turn, phase);
			skipped += out.skipped;
			restored.push({ ship: ship, moved: out.moved });
		}

		savedOrders.refreshAfterRestore(restored, strategy);

		return { ships: restored.length, skipped: skipped };
	},

	applyShip: function applyShip(ship, rec, turn, phase) {
		var skipped = 0;
		var moved = false;

		if (!Array.isArray(ship.EW)) ship.EW = [];
		savedOrders.replaceThisTurn(ship.EW, turn, rec.EW);

		if (phase === 3 && Array.isArray(ship.movement)) {
			for (var m = ship.movement.length - 1; m >= 0; m--) {
				var move = ship.movement[m];
				if (move && move.turn == turn && move.id == -1 && move.value == "combatpivot") {
					ship.movement.splice(m, 1);
					moved = true;
				}
			}
			if (rec.move && rec.move.length) {
				var moves = savedOrders.copy(rec.move);
				for (var c = 0; c < moves.length; c++) ship.movement.push(moves[c]);
				moved = true;
			}
		}

		/* Deployment: this turn's uncommitted rows become exactly the draft's - which also drops the
		   deploy rows activation just made for an arriving wave (autoPlaceArrivingReinforcements), so
		   a unit never ends up with two (validateDeployment refuses "more than one deployment entry").
		   ship.deploymove must be the SAME object as its row in ship.movement - deploy() and the
		   speed/turn arrows edit it in place - so it is pointed at the restored copy, or removed. */
		if (phase === -1 && Array.isArray(ship.movement)) {
			var hadRows = false;
			for (var r = ship.movement.length - 1; r >= 0; r--) {
				var row = ship.movement[r];
				if (row && row.turn == turn && row.id == -1) {
					ship.movement.splice(r, 1);
					hadRows = true;
				}
			}
			delete ship.deploymove;
			if (rec.move && rec.move.length) {
				var rows = savedOrders.copy(rec.move);
				for (var k = 0; k < rows.length; k++) {
					ship.movement.push(rows[k]);
					if (rows[k].type === "deploy") ship.deploymove = rows[k];
				}
			}
			moved = moved || hadRows || !!(rec.move && rec.move.length);

			var dock = rec.dock || {};
			savedOrders.DOCK_KEYS.forEach(function (key) {
				if (dock[key] !== undefined) ship[key] = savedOrders.copy(dock[key]);
				else delete ship[key];
			});
		}

		if (phase === 1 && rec.ship) {
			ship.arrivalVia = rec.ship.arrivalVia;
			ship.arrivalSpeed = rec.ship.arrivalSpeed;
			ship.arrivalHangar = rec.ship.arrivalHangar;
		}

		if (ship.flight) {
			var ftr = rec.ftr || {};
			var seenFighters = {};
			for (var f in ship.systems) {
				var fighter = ship.systems[f];
				if (!fighter) continue;
				seenFighters[fighter.id] = true;
				var fsys = ftr[fighter.id] || {};
				var seenSys = {};
				for (var s in fighter.systems) {
					var fs = fighter.systems[s];
					if (!fs) continue;
					seenSys[fs.id] = true;
					savedOrders.applySystem(ship, fs, fsys[fs.id] || null, turn, phase);
				}
				skipped += savedOrders.countUnseen(fsys, seenSys);
			}
			for (var fid in ftr) {
				if (!seenFighters[fid]) skipped += savedOrders.countOrders(ftr[fid]);
			}
		} else {
			var sys = rec.sys || {};
			var seen = {};
			for (var a in ship.systems) {
				var system = ship.systems[a];
				if (!system) continue;
				seen[system.id] = true;
				savedOrders.applySystem(ship, system, sys[system.id] || null, turn, phase);
			}
			skipped += savedOrders.countUnseen(sys, seen);
		}

		return { skipped: skipped, moved: moved };
	},

	/* One system: this turn's fire orders (and, in Initial Orders, its power) become exactly the
	   draft's - nothing at all when the draft has no record for it. */
	applySystem: function applySystem(ship, system, rec, turn, phase) {
		if (!Array.isArray(system.fireOrders)) system.fireOrders = [];
		savedOrders.replaceThisTurn(system.fireOrders, turn, rec ? rec.fire : null);

		if (phase === 1) {
			if (!Array.isArray(system.power)) system.power = [];
			savedOrders.replaceThisTurn(system.power, turn, rec ? rec.power : null);
		}

		if (!rec) return;

		/* The weapon's own mode switch, never a bare assignment: changeFiringMode / setFiringMode
		   rebuild range, fire control, damage and the info readouts for the mode (trap T9).
		   restoreFiringMode is the helper that already does exactly this for the display paths,
		   bounded so a mode the weapon does not have cannot spin. */
		if (rec.mode !== undefined && rec.mode !== null && system.firingMode !== undefined
			&& window.weaponManager && typeof weaponManager.restoreFiringMode === "function") {
			weaponManager.restoreFiringMode(system, rec.mode);
		}

		if (rec.state && typeof system.applyDraftState === "function") {
			system.applyDraftState(rec.state, phase);
		}
	},

	applyGate: function applyGate(ship, rec, turn, phase) {
		var skipped = 0;
		if (phase !== 1) return skipped;

		var sys = rec.sys || {};
		for (var id in sys) {
			var system = shipManager.systems.getSystem(ship, id);
			if (!system || system.name !== "jumpEngine") {
				skipped += savedOrders.countOrders(sys[id]);
				continue;
			}
			if (!Array.isArray(system.fireOrders)) system.fireOrders = [];
			savedOrders.replaceThisTurn(system.fireOrders, turn, sys[id].fire);
		}
		return skipped;
	},

	/* Orders in draft records that no longer resolve - counted for the notice. Approximate by
	   design: a fire order is one, and a system's power or settings count as one between them. */
	countShipOrders: function countShipOrders(rec) {
		var n = (rec.EW ? rec.EW.length : 0) + (rec.move ? rec.move.length : 0);
		for (var id in (rec.sys || {})) n += savedOrders.countOrders(rec.sys[id]);
		for (var fid in (rec.ftr || {})) n += savedOrders.countOrders(rec.ftr[fid]);
		return n;
	},

	countUnseen: function countUnseen(records, seen) {
		var n = 0;
		for (var id in records) {
			if (!seen[id]) n += savedOrders.countOrders(records[id]);
		}
		return n;
	},

	countOrders: function countOrders(rec) {
		if (!rec) return 0;
		if (rec.fire || rec.power || rec.state || rec.mode !== undefined) {
			return Math.max(1, (rec.fire ? rec.fire.length : 0));
		}
		var n = 0;   //a fighter's map of systems
		for (var id in rec) n += savedOrders.countOrders(rec[id]);
		return n;
	},

	/* Everything that draws from the arrays the restore just rewrote (plan §1.3). The same
	   events the order-giving paths raise themselves, so each listener refreshes the way it
	   already does for a click. */
	refreshAfterRestore: function refreshAfterRestore(restored, strategy) {
		for (var i = 0; i < restored.length; i++) {
			var ship = restored[i].ship;
			webglScene.customEvent("ShipEwChanged", { ship: ship });
			webglScene.customEvent("SystemDataChanged", { ship: ship });
			if (restored[i].moved) webglScene.customEvent("ShipMovementChanged", { ship: ship });
		}

		//Launch and target markers for ballistic and hex-targeted orders. SystemDataChanged only
		//redraws them when the payload names such a system, and these name none.
		if (strategy && strategy.ballisticIconContainer && strategy.shipIconContainer) {
			strategy.ballisticIconContainer.consumeGamedata(gamedata, strategy.shipIconContainer);
		}

		if (window.fleetListManager && typeof fleetListManager.updateFleetList === "function") {
			fleetListManager.updateFleetList();
		}

		/* Deployment: the helper the deploy-dock dialog uses after it moves units in or out of a
		   carrier - it re-runs the commit gate (validateAllDeployment), hides docked units' icons and
		   shows the rest, refreshes the EDF previews and every hangar's "Carrying" line. Activation
		   armed the tick against the pre-restore placements; this re-arms it against the restored ones. */
		if (Number(gamedata.gamephase) === -1 && typeof window.refreshDeploymentUIForDeployStart === "function") {
			window.refreshDeploymentUIForDeployStart();
		}
	},

	/* The floppy normally rides on gamedata.showCommitButton. Deployment is the exception: its tick
	   stays hidden until every unit has a legal placement, and the point of saving there is to stop
	   BEFORE that - so in phase -1 the floppy shows on its own whenever this player can save.
	   hideCommitButton (waiting) and the replay header still take it away. */
	syncButton: function syncButton() {
		if (Number(gamedata.gamephase) !== -1 || !savedOrders.canSaveNow()) return;
		$(".saveturn").show();
		$("#phaseheader").addClass("fv-save-shown");
	},

	/* ============================== SAVE ============================== */

	onSaveClicked: function onSaveClicked() {
		savedOrders.save();
	},

	save: function save() {
		if (savedOrders.busy) return;
		if (!savedOrders.canSaveNow()) return;
		//A commit already on its way supersedes the save.
		if (window.ajaxInterface && ajaxInterface.submiting) return;

		var draft;
		try {
			draft = savedOrders.capture();
		} catch (e) {
			console.error("Save Orders: capture failed", e);
			window.confirm.error("Your orders could not be saved: " + savedOrders.escape(e.message || String(e)), function () { });
			return;
		}

		/* ⚠️ NOT through ajaxInterface.submiting (trap T5): submitGamedata returns early while that
		   flag is set, so a commit clicked during a save would silently do nothing. The blocking
		   overlay is what keeps the tick out of reach instead. */
		savedOrders.busy = true;
		savedOrders.showOverlay("SAVING ORDERS...");

		savedOrders.post({
			action: "save",
			gameid: gamedata.gameid,
			turn: draft.turn,
			phase: draft.phase,
			//A STRING inside the body, as submitGamedata sends `ships`: saveOrders.php decodes the
			//body as an array, which would turn every empty {} in the draft into [].
			orders: JSON.stringify(draft)
		}, function (response) {
			savedOrders.busy = false;
			savedOrders.hideOverlay();

			savedOrders.rememberSave(response.savedAt, draft.turn, draft.phase);
			savedOrders.refreshPanel();
			savedOrders.showNotice("Orders saved (" + savedOrders.formatTime(response.savedAt) + ")");
		}, function (message) {
			savedOrders.busy = false;
			savedOrders.hideOverlay();
			window.confirm.error("Your orders could not be saved. " + savedOrders.escape(message), function () { });
		});
	},

	/* ============================== DISCARD (plan §1.8) ============================== */

	discard: function discard() {
		if (savedOrders.busy) return;

		window.confirm.confirm("Discard your saved orders? This also resets every order you have given this phase, "
			+ "including changes you have not saved, and reloads the game.", function () {
			savedOrders.busy = true;
			savedOrders.showOverlay("DISCARDING SAVED ORDERS...");

			savedOrders.post({ action: "clear", gameid: gamedata.gameid }, function () {
				/* ⚠️ ONLY NOW (trap T13). Reloading before the row is gone would let game.php find
				   it and restore it all over again. A reload rather than an undo in place: it is the
				   same path as opening the game with nothing saved, so it cannot miss a field. The
				   overlay stays up until the page goes. */
				window.location.reload();
			}, function (message) {
				savedOrders.busy = false;
				savedOrders.hideOverlay();
				window.confirm.error("Your saved orders could not be discarded. " + savedOrders.escape(message), function () { });
			});
		});
	},

	post: function post(body, onSuccess, onFailure) {
		$.ajax({
			type: "POST",
			url: "saveOrders.php",
			contentType: "application/json; charset=utf-8",
			dataType: "json",
			data: JSON.stringify(body),
			timeout: 15000
		}).done(function (response) {
			if (!response || response.error) {
				onFailure((response && response.error) || "The server did not answer.");
				return;
			}
			onSuccess(response);
		}).fail(function (xhr, status) {
			onFailure(status === "timeout" ? "The server took too long to answer." : "The server could not be reached.");
		});
	},

	/* The blocking overlay reads "TRANSMITTING ORDERS..." - right for a commit, wrong here, where
	   it would read as one. Its label is swapped for the length of the request. */
	overlayLabel: null,

	showOverlay: function showOverlay(label) {
		var node = savedOrders.overlayTextNode();
		if (node) {
			if (savedOrders.overlayLabel === null) savedOrders.overlayLabel = node.nodeValue;
			node.nodeValue = label;
		}
		ajaxInterface.showBlockingOverlay();
	},

	hideOverlay: function hideOverlay() {
		ajaxInterface.hideBlockingOverlay();
		var node = savedOrders.overlayTextNode();
		if (node && savedOrders.overlayLabel !== null) node.nodeValue = savedOrders.overlayLabel;
		savedOrders.overlayLabel = null;
	},

	overlayTextNode: function overlayTextNode() {
		var span = document.querySelector("#global-blocking-overlay > span");
		if (!span) return null;
		for (var i = 0; i < span.childNodes.length; i++) {
			var node = span.childNodes[i];
			if (node.nodeType === 3 && node.nodeValue.trim() !== "") return node;
		}
		return null;
	},

	/* ============================== NOTICE (ruling D3) ============================== */

	/* The passive one-liner: fades in and out on its own and takes no clicks
	   (feedback_passive_notices). Directly beneath the phase banner while the banner is up - the
	   restore notice comes up in the same activation as the banner, so the two fade in together -
	   and where the banner sits otherwise. Its own element, never a line inside #infowindow, which
	   informFire reuses during replays. */
	showNotice: function showNotice(text, timeout) {
		var el = $("#savedOrdersNotice");
		if (!el.length) return;
		timeout = timeout || 5000;

		var top = 130;
		var banner = $("#infowindow");
		if (banner.length && banner.is(":visible")) {
			top = (parseFloat(banner.css("top")) || 130) + banner.outerHeight() + 6;
		}

		clearTimeout(savedOrders.noticeTimer);
		el.stop(true, true).text(text).css({ top: top + "px", display: "block", opacity: 0 }).fadeTo(1000, 0.65);
		savedOrders.noticeTimer = setTimeout(function () {
			el.fadeTo(1000, 0, function () {
				el.hide();
			});
		}, timeout);
	},

	/* "14:32" in the player's own time zone, with the day in front when it was not today. */
	formatTime: function formatTime(unixSeconds) {
		var d = new Date(Number(unixSeconds) * 1000);
		if (isNaN(d.getTime())) return "earlier";

		var time = d.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" });
		if (d.toDateString() === new Date().toDateString()) return time;

		return d.toLocaleDateString([], { weekday: "short", day: "numeric", month: "short" }) + ", " + time;
	},

	escape: function escape(text) {
		return $("<div>").text(String(text)).html();
	},

	/* ============================== OPTIONS BLOCK + FLOPPY TITLE ============================== */

	/* The Saved Orders block in the OPTIONS tab, and the floppy's tooltip. Called from
	   parseServerData - i.e. on EVERY POLL - so the DOM is only written when the state string
	   moves (trap T12), exactly as savedFleets.refreshSavePanel does. */
	refreshPanel: function refreshPanel() {
		if (!window.gamedata) return;

		var visible = savedOrders.canSaveNow();
		var saved = visible && savedOrders.hasSaveForThisPhase();
		var state = visible + "/" + (saved ? savedOrders.savedAt : "-") + "/" + gamedata.turn + "/" + gamedata.gamephase;
		if (state === savedOrders.panelState) return;
		savedOrders.panelState = state;

		var when = saved ? savedOrders.formatTime(savedOrders.savedAt) : null;
		$(".saveturn .save").attr("title", when ? "Save orders - last saved " + when : "Save orders");

		var panel = $("#savedOrdersPanel");
		if (!panel.length) return;

		//css("") rather than show(), as refreshSavePanel does: it hands the decision back to the
		//stylesheet instead of writing an inline display a later rule would have to fight.
		panel.css("display", visible ? "" : "none");
		if (!visible) return;

		$("#savedOrdersDiscard").prop("disabled", !saved);
		$("#savedOrdersStatus").text(saved
			? "Turn " + gamedata.turn + ", " + savedOrders.phaseName(gamedata.gamephase) + " \u2014 saved " + when + "."
			: "Nothing saved for this phase.");
	}
};

/* game.php bootstrap. The floppy's click, and the OPTIONS block's button and refresh - the tab's
   generic data-select handler (UI/botPanel.js) fires "onshow" on the panel it reveals. */
jQuery(function () {
	$(".saveturn").on("click", savedOrders.onSaveClicked);

	if (!$("#savedOrdersPanel").length) return;

	$("#savedOrdersDiscard").on("click", function () {
		savedOrders.discard();
	});

	$("#gameoptions").on("onshow", function () {
		savedOrders.refreshPanel();
	});
});
