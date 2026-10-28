"use strict";

/*==========================================================================
  MOVEMENT LOCKS (OFFICERS_PLAN.md Stage 31, §9.38) - the client half of the server's MoveLock
  (source/server/handlers/MoveLock.php).

  A unit's moves this turn up to an order it can no longer take back. Such an order is decided by
  the SERVER when it is given - the first is an Expert Graviton Controller's rotation of the
  gravitic drive, whose d6 the server rolls - so taking it back would let the player roll again,
  and replotting the moves before it after seeing the result would let him choose where he was
  when it happened. Minesweeping Mode's way (user ruling 2026-10-25):
    - ask(): post the order with this turn's rows; the server answers with the rows up to and
      including its new row - the LOCK - which take the place of the unit's own;
    - the lock is every row of the turn up to the LAST row of a LOCK_TYPES type. deleteMove and
      deleteSpeedChange refuse a locked row (movement.js isLastMoveFinal), and doJink(-1) and
      doContraction(-1), which splice rows out of the middle, never take one (protects);
    - onStrategyActivated(): a reload puts the locked rows back (game.php's window.fvMoveLocks),
      after Save Orders' and Minesweeping Mode's restores - a draft that begins with them is kept,
      as it only extends them (beginsWith);
    - the commit must begin with them, or the server refuses it (MoveLock::keepsLock).
  No row is marked: the lock ends at the last lock-type row, which only the server writes, so rows
  spliced out before or after it cannot shift it. Generic: another such order adds its row type to
  LOCK_TYPES (both ends) and asks through ask().
  ==========================================================================*/

window.moveLock = {

	//⚠️ MIRROR PAIR with MoveLock::LOCK_TYPES (PHP): the row types only a lock may hold - today shipManager.movement.GRAV_ROTATE
	LOCK_TYPES: ['gravRotate'],

	inFlight: null, //{ shipId } - ONE order at a time; the blocking overlay holds the board meanwhile

	isLockType: function isLockType(type) {
		return moveLock.LOCK_TYPES.indexOf(String(type)) !== -1;
	},

	/* ============================== THE LOCK ============================== */

	/* Index in ship.movement of the last locked row - this turn's last lock-type row, in the Movement phase - or -1. */
	lockIndex: function lockIndex(ship) {
		if (!ship || !ship.movement || !window.gamedata || Number(gamedata.gamephase) !== 2) return -1;
		for (var i = ship.movement.length - 1; i >= 0; i--) {
			var move = ship.movement[i];
			if (move && move.turn == gamedata.turn && moveLock.isLockType(move.type)) return i;
		}
		return -1;
	},

	//What deleteMove and deleteSpeedChange ask, through isLastMoveFinal: is the row they would remove a locked one?
	isLastRowLocked: function isLastRowLocked(ship) {
		if (!ship || !ship.movement || !ship.movement.length) return false;
		var index = moveLock.lockIndex(ship);
		return index >= 0 && ship.movement.length - 1 <= index;
	},

	//What a splice out of the middle asks (doJink(-1), doContraction(-1)): is the row at `index` locked?
	protects: function protects(ship, index) {
		var lock = moveLock.lockIndex(ship);
		return lock >= 0 && Number(index) <= lock;
	},

	/* ============================== THE ORDER ============================== */

	/* ⭐ Post a locking order for `ship` - its endpoint `url` (gravRotate.php) answers {"rows": [...]} or {"error": ...}. This
	   turn's rows go with it as a JSON STRING inside the body (mineSweep.js' T12: the endpoint decodes the body as an array, which
	   would turn every {} into [] - and it stores the rows as they come). The answer's rows replace the unit's own, and done(reply,
	   ship) follows. The blocking overlay reads `label` meanwhile. false when another order is still out. */
	ask: function ask(url, ship, label, done) {
		if (moveLock.inFlight || !ship) return false;
		var moves = [];
		for (var i = 0; i < ship.movement.length; i++) {
			var move = ship.movement[i];
			if (move && move.turn == gamedata.turn) moves.push(move);
		}

		moveLock.inFlight = { shipId: String(ship.id) };
		if (window.savedOrders && typeof savedOrders.showOverlay === "function") savedOrders.showOverlay(label);

		$.ajax({
			type: "POST",
			url: url,
			contentType: "application/json; charset=utf-8",
			dataType: "json",
			data: JSON.stringify({
				gameid: gamedata.gameid,
				turn: gamedata.turn,
				shipid: ship.id,
				moves: JSON.stringify(moves)
			}),
			timeout: 15000
		}).done(function (reply) {
			moveLock.onReply(ship.id, reply || {}, done);
		}).fail(function () {
			//asked again, the server answers what it already decided - never a second roll
			moveLock.onReply(ship.id, { error: "The server could not be reached. Try again." }, done);
		});
		return true;
	},

	onReply: function onReply(shipId, reply, done) {
		moveLock.inFlight = null;
		if (window.savedOrders && typeof savedOrders.hideOverlay === "function") savedOrders.hideOverlay();

		//The unit committed, or the phase or turn moved on, while the order was out: the server refused it, and nothing changes here.
		var ship = gamedata.getShip(shipId);
		if (!ship || gamedata.waiting || Number(gamedata.gamephase) !== 2) return;

		if (reply.error || !Array.isArray(reply.rows)) {
			var text = reply.error || "The order could not be given.";
			if (window.confirm && typeof window.confirm.error === "function") window.confirm.error(moveLock.escape(text), function () {});
			return;
		}

		moveLock.apply(ship, reply.rows, gamedata.turn);
		if (typeof done === "function") done(reply, ship);
	},

	//The lock's rows in place of the unit's own - Save Orders' applyPlottedMoves (sparse assignedThrust, contraction, detach)
	apply: function apply(ship, rows, turn) {
		savedOrders.applyPlottedMoves(ship, moveLock.playerRows(rows, turn), turn);
		savedOrders.refreshMovementCaches(ship);
		webglScene.customEvent("ShipMovementChanged", { ship: ship });
	},

	/* ============================== THE RESTORE ============================== */

	/* Called by PhaseDirector after every phase-strategy activation, after Save Orders' and Minesweeping Mode's restores (each
	   REPLACES this turn's rows). game.php inlines this player's locked units as window.fvMoveLocks; a unit's rows come back unless
	   what it holds already begins with them (a restored draft that only extends them). Spent on first use. */
	onStrategyActivated: function onStrategyActivated(gd, strategy) {
		var pending = window.fvMoveLocks;
		if (!pending || !gd || !gd.gameid) return;
		window.fvMoveLocks = null;
		if (gd.replay || gd.waiting || Number(gd.gamephase) !== 2 || Number(pending.turn) !== Number(gd.turn)) return;

		var reselect = false;
		for (var id in (pending.ships || {})) {
			var ship = gamedata.getShip(id);
			if (!ship || ship.userid !== gamedata.thisplayer || gamedata.getMyActiveShips().indexOf(ship) === -1) continue;
			var rows = moveLock.playerRows(pending.ships[id], gd.turn);
			if (!rows.length) continue;
			if (moveLock.beginsWith(moveLock.playerRows(ship.movement, gd.turn), rows)) continue;
			savedOrders.applyPlottedMoves(ship, rows, gd.turn);
			savedOrders.refreshMovementCaches(ship);
			webglScene.customEvent("ShipMovementChanged", { ship: ship });
			reselect = true;
		}

		//As after a draft restore: the unit activation picked may have finished moving now.
		if (reselect && strategy && typeof strategy.selectActiveShip === "function") strategy.selectActiveShip();
	},

	/* The player's own rows among `moves` - the shape a Save Orders draft keeps (id -1, not preturn, committed). Rows the server
	   sent stay where the load put them. */
	playerRows: function playerRows(moves, turn) {
		var out = [];
		if (!Array.isArray(moves)) return out;
		for (var i = 0; i < moves.length; i++) {
			var move = moves[i];
			if (!move || move.turn != turn || move.id != -1 || move.preturn || move.forced) continue;
			if (move.commit === false) break;
			out.push(move);
		}
		return out;
	},

	/* Does `list` begin with `prefix` - the same type, hex, facing, heading and speed in each place, and a lock-type row's value?
	   ⚠️ MIRROR PAIR with MoveLock::keepsLock (PHP), which refuses a commit that does not. */
	beginsWith: function beginsWith(list, prefix) {
		if (!Array.isArray(list) || !Array.isArray(prefix) || list.length < prefix.length) return false;
		for (var i = 0; i < prefix.length; i++) {
			var a = prefix[i], b = list[i];
			if (!a || !b || String(a.type) !== String(b.type)) return false;
			if (!a.position || !b.position || Number(a.position.q) !== Number(b.position.q) || Number(a.position.r) !== Number(b.position.r)) return false;
			if (Number(a.facing) !== Number(b.facing) || Number(a.heading) !== Number(b.heading) || Number(a.speed) !== Number(b.speed)) return false;
			if (moveLock.isLockType(a.type) && String(a.value) !== String(b.value)) return false;
		}
		return true;
	},

	escape: function escape(text) {
		return $("<div>").text(String(text)).html();
	}
};
