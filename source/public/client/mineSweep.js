"use strict";

/*==========================================================================
  Mine detection: LIVE SWEEPING - MINE_DETECTION_PLAN.md §1.4 (client: Stage 2b).

  ⭐ OPT-IN, PER UNIT (Q7, ruled 2026-10-04 - plan §6.8). A unit that can detect mines shows a purple
  Minesweeping Mode icon in its movement UI. Off - the default - it plots exactly as before, with free
  undo and speed changes, and what it finds shows when it commits (Stage 1's walk). On, it sweeps as
  described below. Either way it is chosen before the unit's first step into a hex and holds for the
  rest of its move (getModeState).

  A unit in Minesweeping Mode sweeps as it moves. When the player plots a step INTO a hex
  (a move or a slip), this asks the server (mineSweep.php) about the hexes the unit has entered
  since its last answer. The server tests them against every enemy mine this team has not found,
  records any find, and makes every hex it tested FINAL for the unit - found or not (D10, D11, T20).
  So:
  - a find shows the moment the unit enters the hex it is made from, as on the tabletop;
  - a find INTERRUPTS the move there (D13): every row plotted after that hex is taken back - the
    rest of a right-click "move fully", and anything plotted while the reply was on its way, which
    is never sent (T21) - and the unit stands on that hex with its remaining movement to plot;
  - a step that has been asked about cannot be undone, a mis-click included. Nor, from the Cancel
    button, can a step still waiting out the short debounce (isLastRowSweptStep) - only the
    interrupt takes those back.

  ⚠️ THE CLIENT HOLDS NOTHING ABOUT AN UNFOUND MINE, AND MUST NOT (plan §0.3). Any client-side
  "would this hex find a mine?" is an oracle - run it over every hex in reach and the "yes" hexes
  form a disc centred on the mine. The gate below reads public data only; the answer comes from
  the server, one entered hex at a time.

  ⚠️ THE LOCK IS A PREFIX (T13): every row of this turn up to and including the one that entered
  the last locked hex. It is held as a COUNT of hexes and turned into a row index on demand, never
  as marks on rows, so the rows doJink and doContraction splice out of the middle cannot shift it.
  deleteMove and deleteSpeedChange refuse when the LAST row is inside it (isLastRowLocked), and
  every loop that deletes stops when deleteMove removes nothing.

  Wiring: MovementPhaseStrategy.onShipMovementChanged -> onMovementChanged (every plotted action
  and every undo); PhaseDirector -> onStrategyActivated (the reload restore, §1.4.6);
  gamedata.onCommitClicked -> readyToCommit (§1.4.2); movement.js -> isLastRowLocked; and back
  out, redrawUI -> strategy.redrawMovementUI whenever the lock moves on its own.
  ==========================================================================*/

window.mineSweep = {

	DEBOUNCE_MS: 400,   //a run of clicks, or a right-click "move fully", is one request
	RETRY_MS: 2500,
	MAX_RETRIES: 3,

	turn: null,         //the turn `units` and `pending` belong to
	units: {},          //by unit id: { mode: Minesweeping Mode is on, final: hexes the server has made final, sent: hexes in the request on its way (0 when none), off: this unit no longer asks }
	pending: {},        //unit ids with steps not yet asked about
	inFlight: null,     //{ shipId, hexes } - ONE request at a time for all units: the server serialises them on the player's commit lock anyway
	timer: null,
	retries: 0,
	waiters: [],        //commits waiting for every answer (readyToCommit)
	interruptedWhileWaiting: false,
	interrupting: false,//the interrupt's own take-back must not ask anything
	strategy: null,     //the Movement phase strategy, which draws a find
	switchedOff: false, //the server answered "disabled": the switch was turned off after this page loaded

	/* THE SWITCH (server side: MineSweep::$liveSweeping, handed over by game.php). Off, nothing here
	   asks, locks or restores anything - units plot with free undo, and finds show when they commit
	   (Stage 1's walk, which stays on either way). */
	isLive: function isLive() {
		return window.fvLiveMineSweeping === true && !mineSweep.switchedOff;
	},

	/* This turn's state for one unit - all of it is dropped when the turn changes. */
	state: function state(ship) {
		var turn = Number(gamedata.turn);
		if (mineSweep.turn !== turn) {
			mineSweep.turn = turn;
			mineSweep.units = {};
			mineSweep.pending = {};
		}

		var id = String(ship.id);
		if (!mineSweep.units[id]) mineSweep.units[id] = { mode: false, final: 0, sent: 0, off: false };
		return mineSweep.units[id];
	},

	/* ============================== THE GATE (§1.4.2) ============================== */

	/* Does this unit sweep as it moves? It can (canSweep), and the player has put it in Minesweeping
	   Mode. A unit that does not never asks, and plots exactly as before. */
	isSweeper: function isSweeper(ship) {
		return mineSweep.canSweep(ship) && mineSweep.state(ship).mode === true;
	},

	/* Could this unit sweep - is it offered Minesweeping Mode? The player's own unit, active in
	   Movement, not riding a host (the host's path covers it, T18), with a Detect Mines rating above 0,
	   while at least one enemy mine is still unfound. Public data only, so the gate itself reveals
	   nothing. ⚠️ The rating, not the Detect Mines EW: a minesweeper's bonus counts with no EW at all
	   (D12), and ew.getDetectMEW adds it - it is the same figure the purple MDEW overlay draws. */
	canSweep: function canSweep(ship) {
		if (!mineSweep.isLive()) return false;
		if (!window.gamedata || !ship) return false;
		if (gamedata.replay || gamedata.waiting || Number(gamedata.gamephase) !== 2) return false;
		if (ship.userid !== gamedata.thisplayer) return false;
		if (Object.keys(ship.attached || {}).length !== 0 && !ship.detached) return false;
		if (gamedata.getMyActiveShips().indexOf(ship) === -1) return false;
		if (mineSweep.state(ship).off) return false;
		if (!((Number(ew.getDetectMEW(ship)) || 0) > 0)) return false;

		return mineSweep.hasUnfoundEnemyMine();
	},

	hasUnfoundEnemyMine: function hasUnfoundEnemyMine() {
		var team = gamedata.getPlayerTeam();
		for (var i in gamedata.ships) {
			var unit = gamedata.ships[i];
			if (!unit || !unit.mine || unit.team == team) continue; //loose: team ids arrive as JSON and drift string/int
			if (shipManager.isDestroyed(unit)) continue;
			if (shipManager.getTurnDeployed(unit) > gamedata.turn) continue;
			if (!shipManager.isDetected(unit)) return true;
		}
		return false;
	},

	/* ============================== MINESWEEPING MODE (Q7) ============================== */

	/* What the purple icon in the movement UI shows for this unit: null (no icon), 'off', 'on',
	   'locked' (on for the rest of the turn) or 'unavailable' (off for the rest of the turn).
	   ⭐ A UNIT MOVES WHOLLY IN THE MODE OR WHOLLY OUT OF IT (user 2026-10-05): the icon can be clicked
	   only before the unit's first step INTO a hex this turn. Speed changes, turns, pivots and the like
	   first are fine - the same line Cancel draws (isLastRowSweptStep). A unit that moved out of the
	   mode can take its moves back to its starting hex and turn it on then. A unit with swept steps
	   keeps its icon even once there is nothing left to find, so the player can see why those steps
	   will not undo. */
	getModeState: function getModeState(ship) {
		if (!mineSweep.isLive() || !window.gamedata || !ship) return null;
		if (Number(gamedata.gamephase) !== 2 || gamedata.replay || gamedata.waiting) return null;
		if (ship.userid !== gamedata.thisplayer || gamedata.getMyActiveShips().indexOf(ship) === -1) return null;

		var st = mineSweep.state(ship);
		if (st.mode && mineSweep.lockedHexes(ship) > 0) return 'locked';
		if (!mineSweep.canSweep(ship)) return null;
		if (mineSweep.hexRows(ship).length > 0) return st.mode ? 'locked' : 'unavailable';
		return st.mode ? 'on' : 'off';
	},

	/* The icon's click: on or off, while the unit has not yet moved into a hex (getModeState), so
	   there is never anything already plotted to sweep, or waiting to be sent. Returns whether
	   anything changed. */
	toggleMode: function toggleMode(ship) {
		var state = mineSweep.getModeState(ship);
		if (state !== 'off' && state !== 'on') return false;

		mineSweep.state(ship).mode = (state === 'off');
		return true;
	},

	/* ============================== THE LOCK (§1.4.2, T13) ============================== */

	/* Indexes into ship.movement of this turn's rows that ENTERED a hex, in order: committed moves and
	   slips. An uncommitted slip is still on its thrust panel - it has not been entered yet, and the
	   panel's Cancel must be able to take it back. */
	hexRows: function hexRows(ship) {
		var out = [];
		var moves = ship.movement || [];
		for (var i = 0; i < moves.length; i++) {
			var move = moves[i];
			if (!move || move.turn != gamedata.turn || move.commit === false) continue;
			if (move.type === "move" || move.type === "slipleft" || move.type === "slipright") out.push(i);
		}
		return out;
	},

	/* Locked from the moment a request leaves, because the server may make those hexes final before
	   its answer arrives; the answer then shrinks the lock to what is final. */
	lockedHexes: function lockedHexes(ship) {
		var st = mineSweep.state(ship);
		return Math.max(st.final, st.sent);
	},

	/* Index in ship.movement of the last locked row, or -1 when nothing is locked. */
	lockIndex: function lockIndex(ship) {
		if (!mineSweep.isLive()) return -1; //switched off: nothing binds, a step swept before included
		if (!window.gamedata || Number(gamedata.gamephase) !== 2 || !ship) return -1;
		var locked = mineSweep.lockedHexes(ship);
		if (locked <= 0) return -1;
		var rows = mineSweep.hexRows(ship);
		if (!rows.length) return -1;
		return rows[Math.min(locked, rows.length) - 1];
	},

	/* What deleteMove and deleteSpeedChange ask: is the row they would remove a final one? */
	isLastRowLocked: function isLastRowLocked(ship) {
		if (!ship || !ship.movement || !ship.movement.length) return false;
		var index = mineSweep.lockIndex(ship);
		return index >= 0 && ship.movement.length - 1 <= index;
	},

	hasLockedRows: function hasLockedRows(ship) {
		return mineSweep.lockIndex(ship) >= 0;
	},

	/* What the Cancel button asks on top of the lock (user 2026-10-05): a unit in Minesweeping Mode
	   cannot take back a step INTO a hex at all, not even in the moment before its request leaves - a
	   Cancel that showed for 0.4s and then did nothing read as broken. The button only: deleteMove
	   still allows an unsent step, because the interrupt (D13) takes back steps that were plotted
	   behind a find and never sent (T21). */
	isLastRowSweptStep: function isLastRowSweptStep(ship) {
		if (!ship || !ship.movement || !ship.movement.length || !mineSweep.isSweeper(ship)) return false;
		var rows = mineSweep.hexRows(ship);
		return rows.length > 0 && rows[rows.length - 1] === ship.movement.length - 1;
	},

	/* ============================== THE TRIGGER ============================== */

	/* Every plotted action and every undo, through MovementPhaseStrategy.onShipMovementChanged. Undo
	   never sends anything: a step taken back before its request left was never asked about. */
	onMovementChanged: function onMovementChanged(ship, strategy) {
		if (strategy) mineSweep.strategy = strategy;
		if (mineSweep.interrupting || !ship) return;
		if (!mineSweep.isSweeper(ship)) return;

		var id = String(ship.id);
		if (mineSweep.hexRows(ship).length > mineSweep.lockedHexes(ship)) {
			mineSweep.pending[id] = true;
			mineSweep.retries = 0; //a new step earns a failed request fresh retries
		} else {
			delete mineSweep.pending[id];
		}
		mineSweep.schedule(mineSweep.DEBOUNCE_MS);
	},

	schedule: function schedule(delay) {
		clearTimeout(mineSweep.timer);
		mineSweep.timer = setTimeout(mineSweep.flush, delay);
	},

	/* Send the next unit's unasked steps - unless a request is already on its way, whose answer
	   calls this again. Steps plotted meanwhile wait for that answer (§1.4.2). */
	flush: function flush() {
		clearTimeout(mineSweep.timer);
		mineSweep.timer = null;
		if (mineSweep.inFlight) return;

		for (var id in mineSweep.pending) {
			delete mineSweep.pending[id];
			var ship = gamedata.getShip(id);
			if (!ship || !mineSweep.isSweeper(ship)) continue;
			//Against what is FINAL, not the lock: after a failed request the lock still covers the
			//steps it carried, and they are sent again until the server has answered for them.
			if (mineSweep.hexRows(ship).length <= mineSweep.state(ship).final) continue;
			mineSweep.send(ship);
			return;
		}

		mineSweep.settle();
	},

	send: function send(ship) {
		var st = mineSweep.state(ship);
		var rows = mineSweep.hexRows(ship);
		var last = rows[rows.length - 1];

		//This turn's rows up to the one entering the last hex - the same rows the commit sends.
		var moves = [];
		for (var i = 0; i <= last; i++) {
			var move = ship.movement[i];
			if (move && move.turn == gamedata.turn) moves.push(move);
		}

		st.sent = rows.length;
		mineSweep.inFlight = { shipId: String(ship.id), hexes: rows.length };
		mineSweep.redrawUI(); //the lock has just grown: Cancel goes, the mode icon locks

		$.ajax({
			type: "POST",
			url: "mineSweep.php",
			contentType: "application/json; charset=utf-8",
			dataType: "json",
			data: JSON.stringify({
				gameid: gamedata.gameid,
				turn: gamedata.turn,
				shipid: ship.id,
				known: st.final,
				//A STRING inside the body (T12): mineSweep.php decodes the body as an array, which would
				//turn every {} in the rows into [] - and it stores them for a reload as they were sent.
				moves: JSON.stringify(moves)
			}),
			timeout: 15000
		}).done(function (reply) {
			mineSweep.onReply(ship.id, reply || {});
		}).fail(function () {
			mineSweep.onReply(ship.id, { error: "The server could not be reached.", retry: true });
		});
	},

	/* ============================== THE ANSWER ============================== */

	onReply: function onReply(shipId, reply) {
		mineSweep.inFlight = null;

		/* The unit committed, or the phase or turn moved on, while the request was out (T17). The
		   commit's own walk has recorded anything it would have found. */
		var ship = gamedata.getShip(shipId);
		if (!ship || gamedata.waiting || Number(gamedata.gamephase) !== 2 || mineSweep.turn !== Number(gamedata.turn)) {
			mineSweep.settle();
			return;
		}

		var id = String(ship.id);
		var st = mineSweep.state(ship);

		if (reply.error) {
			if (reply.disabled) {
				mineSweep.switchOff();
				return;
			}

			if (reply.reload) {
				//Out of step with the server - a second tab, say. A reload puts the final rows back (§1.4.6).
				clearTimeout(mineSweep.timer);
				mineSweep.pending = {};
				mineSweep.waiters = [];
				mineSweep.hideOverlay();
				window.confirm.error(mineSweep.escape(reply.error), function () {
					window.location.reload();
				});
				return;
			}

			console.warn("Mine sweep: " + reply.error);

			if (reply.retry || reply.busy || reply.logid) {
				/* Not answered - the request failed, or the server was busy with this player's commit.
				   The lock stays where the request left it, because the server may have made those
				   hexes final, and they are asked about again: shortly, a few times, or with the next
				   step. Never while a commit waits - it goes ahead, and its own walk reports what it
				   finds at the end of the move (D5). */
				if (!mineSweep.waiters.length && mineSweep.retries < mineSweep.MAX_RETRIES) {
					mineSweep.retries++;
					mineSweep.pending[id] = true;
					mineSweep.schedule(mineSweep.RETRY_MS);
					return;
				}
				mineSweep.flush();
				return;
			}

			/* Refused - the unit is not moving now, or its rows do not read as a path. A refused
			   request made nothing final; this unit stops asking this turn, and the commit's walk
			   reports what it finds at the end of the move (D5). */
			st.sent = 0;
			st.off = true;
			mineSweep.redrawUI(); //unlocked again: Cancel comes back
			mineSweep.flush();
			return;
		}

		mineSweep.retries = 0;
		st.final = Number(reply.final) || 0;
		st.sent = 0;

		if (reply.noRating) {
			//The server sees no Detect Mines rating (its scanner is down, say): an answer that can never
			//be "found" teaches nothing, so nothing was made final and this unit stops asking.
			st.off = true;
			mineSweep.redrawUI();
			mineSweep.flush();
			return;
		}

		var found = Array.isArray(reply.found) ? reply.found : [];
		if (found.length) {
			found.forEach(mineSweep.reveal);
			mineSweep.interrupt(ship);
			//T21: steps plotted behind the find are taken back, never sent.
			delete mineSweep.pending[id];
			if (mineSweep.waiters.length) mineSweep.interruptedWhileWaiting = true;
			mineSweep.notify(ship, found.length);
		} else if (mineSweep.hexRows(ship).length > st.final) {
			//Plotted while the answer was on its way: sent at once.
			mineSweep.pending[id] = true;
		}

		mineSweep.redrawUI(); //the lock is now what the server made final
		mineSweep.flush();
	},

	/* The lock moves WITHOUT a row changing - when a request leaves, and when its answer comes back -
	   so no ShipMovementChanged redraws the movement UI, and the Cancel, speed and mode icons it draws
	   from the lock would sit stale until the next click: a Cancel still showing over a step that has
	   just become final does nothing when clicked. This redraws the selected unit's movement UI, and
	   nothing else (no rows changed, so the icon and path need no consumeMovement).
	   ⚠️ NEVER under an open thrust panel: the panel's own redraw hid the movement UI and dropped
	   strategy.movementUI, so redrawMovementUI would draw the icon ring over the panel. Closing the
	   panel fires ShipMovementChanged, which draws it from the lock as it then stands. Nor under a
	   confirm dialog, where drawShipMovementUI refuses and the strategy would lose track of the ring. */
	redrawUI: function redrawUI() {
		var strategy = mineSweep.strategy;
		if (!strategy || typeof strategy.redrawMovementUI !== "function") return;
		if (strategy.shipThrustUIState || $(".confirm").length > 0) return;
		var ship = strategy.selectedShip;
		if (!ship || !ship.movement || ship.movement.some(function (move) { return !move.commit; })) return;
		strategy.redrawMovementUI();
	},

	/* D13 - the move stops at the hex the find was made from. Every row after it goes, last first,
	   through the same deleteMove the Cancel button uses. The thrust panel first if it is open on
	   this unit (T23): its row is always the unit's last, and cancelAssignThrustEvent removes it and
	   closes the panel as the panel's own Cancel does. A panel open on another unit is left alone. */
	interrupt: function interrupt(ship) {
		var keep = mineSweep.lockIndex(ship);
		if (keep < 0) return;

		mineSweep.interrupting = true;
		try {
			var strategy = mineSweep.strategy;
			if (strategy && strategy.shipThrustUIState && strategy.shipThrustUIState.ship === ship
				&& ship.movement.length - 1 > keep) {
				shipManager.movement.cancelAssignThrustEvent(ship);
			}

			var guard = 200;
			while (ship.movement.length - 1 > keep && guard-- > 0) {
				var before = ship.movement.length;
				shipManager.movement.deleteMove(ship);
				if (ship.movement.length === before) break;
			}
		} finally {
			mineSweep.interrupting = false;
		}

		//Redrawn whether or not it is the selected unit.
		webglScene.customEvent("ShipMovementChanged", { ship: ship });
	},

	/* §1.4.5 - draw a find without a reload (T10: an active client does not poll, and a reload would
	   throw away the rows being plotted). The page already has the mine - its masked row and its
	   blueprint - so it needs its real hex and this team in its `detected`. The next full load says
	   the same: the server's mask lifts for a team in `detected`. */
	reveal: function reveal(found) {
		var mine = gamedata.getShip(found.id);
		if (!mine) return;

		var stealth = shipManager.systems.getSystemByName(mine, "mineStealth");
		if (stealth) {
			/* A FRESH array, never a push. The server sends `detected` only when it is non-empty, so an
			   unfound mine's is the static blueprint's - ONE array shared by every mine of its class
			   (arch_client_system_shared_reference): a push would show them all. */
			var team = gamedata.getPlayerTeam();
			var detected = Array.isArray(stealth.detected) ? stealth.detected.slice() : [];
			if (detected.indexOf(team) === -1) detected.push(team);
			stealth.detected = detected;
		}

		var facing = Number(found.f) || 0;
		mine.movement = [{
			id: -1,
			type: "deploy",
			position: new hexagon.Offset(Number(found.q), Number(found.r)),
			xOffset: 0,
			yOffset: 0,
			facing: facing,
			heading: facing,
			speed: 0,
			animating: false,
			animated: true,
			animationtics: 0,
			requiredThrust: Array(null, null, null, null, null),
			assignedThrust: Array(),
			commit: true,
			preturn: false,
			at_initiative: 0,
			turn: gamedata.turn,
			forced: false,
			value: 0
		}];

		var strategy = mineSweep.strategy;
		if (strategy && strategy.shipIconContainer) {
			var icon = strategy.shipIconContainer.getByShip(mine);
			if (icon) icon.consumeMovement(mine.movement);
			//Places every icon and shows or hides each by shouldBeHidden, which now lets the mine through.
			if (strategy.animationStrategy) strategy.animationStrategy.update(gamedata);
		}

		if (window.fleetListManager && typeof fleetListManager.updateFleetList === "function") {
			fleetListManager.updateFleetList();
		}
		if (window.webglScene && typeof webglScene.requestRender === "function") webglScene.requestRender();
	},

	/* The passive notice (feedback: informational messages fade on their own and take no clicks). */
	notify: function notify(ship, count) {
		var text = (ship.name || "A unit") + " detected " + (count === 1 ? "a mine" : count + " mines") + " - its move stops there.";
		if (window.savedOrders && typeof savedOrders.showNotice === "function") savedOrders.showNotice(text);
	},

	/* The switch was turned off after this page loaded - the server answered "disabled". Everything
	   unlocks (the server no longer holds anyone to a swept hex), a waiting commit goes ahead, and the
	   player's units are redrawn so their Cancel icons come back. */
	switchOff: function switchOff() {
		mineSweep.switchedOff = true;
		clearTimeout(mineSweep.timer);
		mineSweep.timer = null;
		mineSweep.pending = {};
		mineSweep.units = {};

		if (window.savedOrders && typeof savedOrders.showNotice === "function") {
			savedOrders.showNotice("Live mine sweeping is switched off - mines a unit finds now show when it commits.");
		}
		gamedata.getMyActiveShips().forEach(function (ship) {
			webglScene.customEvent("ShipMovementChanged", { ship: ship });
		});

		mineSweep.settle();
	},

	/* ============================== THE COMMIT (§1.4.2) ============================== */

	/* Before a Movement commit: send whatever is still waiting out the debounce, and wait until
	   nothing is in flight. True when the commit may go ahead at once. Otherwise false, and `proceed`
	   runs once every answer is in - unless one brought a find, which stops the commit there with the
	   notice so the player can react. That includes a find on the last hex: the unit can still turn
	   or pivot there. */
	readyToCommit: function readyToCommit(proceed) {
		gamedata.getMyActiveShips().forEach(function (ship) {
			if (mineSweep.isSweeper(ship) && mineSweep.hexRows(ship).length > mineSweep.state(ship).final) {
				mineSweep.pending[String(ship.id)] = true;
			}
		});

		if (!mineSweep.inFlight && Object.keys(mineSweep.pending).length === 0) return true;

		if (!mineSweep.waiters.length) mineSweep.interruptedWhileWaiting = false;
		mineSweep.waiters.push(proceed);
		mineSweep.showOverlay();
		mineSweep.flush();
		return false;
	},

	/* Nothing in flight and nothing left to send: let a waiting commit go - or not, after a find. */
	settle: function settle() {
		if (mineSweep.inFlight || Object.keys(mineSweep.pending).length > 0) return;
		if (!mineSweep.waiters.length) return;

		var waiters = mineSweep.waiters;
		var interrupted = mineSweep.interruptedWhileWaiting;
		mineSweep.waiters = [];
		mineSweep.interruptedWhileWaiting = false;
		mineSweep.hideOverlay();

		if (interrupted) return; //the notice is up; the player commits again when ready
		waiters.forEach(function (proceed) {
			proceed();
		});
	},

	showOverlay: function showOverlay() {
		if (window.savedOrders && typeof savedOrders.showOverlay === "function") savedOrders.showOverlay("SWEEPING FOR MINES...");
	},

	hideOverlay: function hideOverlay() {
		if (window.savedOrders && typeof savedOrders.hideOverlay === "function") savedOrders.hideOverlay();
	},

	escape: function escape(text) {
		return $("<div>").text(String(text)).html();
	},

	/* ============================== THE RESTORE (§1.4.6) ============================== */

	/* Called by PhaseDirector after every phase-strategy activation, straight after Save Orders'
	   restore (T14: that one REPLACES this turn's rows, and the final rows are reconciled after it).
	   game.php inlines this player's swept units as window.fvMineSweep; each comes back locked. A
	   restored draft that begins with a unit's final hexes is kept - it only extends them; otherwise
	   the final rows replace it, through Save Orders' own applyPlottedMoves (sparse assignedThrust,
	   contraction, detach - Stage 7 T16). Spent on first use. */
	onStrategyActivated: function onStrategyActivated(gd, strategy) {
		if (strategy) mineSweep.strategy = strategy;

		var pending = window.fvMineSweep;
		if (!pending || !gd || !gd.gameid) return;
		window.fvMineSweep = null;

		if (!mineSweep.isLive()) return; //the server sends none while switched off; belt and braces
		if (gd.replay || gd.waiting || Number(gd.gamephase) !== 2 || Number(pending.turn) !== Number(gd.turn)) return;

		var reselect = false;
		for (var id in (pending.ships || {})) {
			var rec = pending.ships[id] || {};
			var ship = gamedata.getShip(id);
			if (!ship || ship.userid !== gamedata.thisplayer || gamedata.getMyActiveShips().indexOf(ship) === -1) continue;

			var final = Number(rec.hexes) || 0;
			var rows = mineSweep.playerRows(rec.moves, gd.turn);
			if (final <= 0) continue;

			mineSweep.state(ship).final = final;
			mineSweep.state(ship).sent = 0;
			mineSweep.state(ship).mode = true; //it swept those hexes, so it was in Minesweeping Mode - and stays in it

			if (!mineSweep.beginsWith(mineSweep.hexPositions(ship.movement, gd.turn), mineSweep.hexPositions(rows, gd.turn), final)) {
				savedOrders.applyPlottedMoves(ship, rows, gd.turn);
				savedOrders.refreshMovementCaches(ship);
				reselect = true;
			}

			webglScene.customEvent("ShipMovementChanged", { ship: ship });
		}

		//As after a draft restore: the unit activation picked may have finished moving now.
		if (reselect && strategy && typeof strategy.selectActiveShip === "function") strategy.selectActiveShip();
	},

	/* The player's own plotted rows among those stored - the shape a Save Orders draft keeps (id -1,
	   not preturn, committed). Rows the server sent stay where the load put them. */
	playerRows: function playerRows(moves, turn) {
		var out = [];
		if (!Array.isArray(moves)) return out;
		for (var i = 0; i < moves.length; i++) {
			var move = moves[i];
			if (!move || move.turn != turn || move.id != -1 || move.preturn) continue;
			if (move.commit === false) break;
			out.push(move);
		}
		return out;
	},

	hexPositions: function hexPositions(moves, turn) {
		var out = [];
		for (var i = 0; i < (moves || []).length; i++) {
			var move = moves[i];
			if (!move || move.turn != turn || move.commit === false || !move.position) continue;
			if (move.type === "move" || move.type === "slipleft" || move.type === "slipright") {
				out.push(Number(move.position.q) + "," + Number(move.position.r));
			}
		}
		return out;
	},

	beginsWith: function beginsWith(list, prefix, count) {
		if (prefix.length < count || list.length < count) return false;
		for (var i = 0; i < count; i++) {
			if (list[i] !== prefix[i]) return false;
		}
		return true;
	}
};
