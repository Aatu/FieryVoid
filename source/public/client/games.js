"use strict";

/*
 * games.php — YOUR GAMES + JOIN GAMES lists.
 *
 * The server (DBManager::getPlayerGames / getLobbyGames) sends STRUCTURED rows: a plain
 * game name plus flags (ladder, test, rules, map, counts). It used to send pre-built
 * HTML in `name` — inline-styled "LADDER:" spans and a <br><span class="gameRules">
 * fragment — which is why the cards could not be restyled without editing SQL result
 * assembly. All presentation now lives in this file.
 *
 * Names are player-supplied text and are escaped on the way into the DOM. The previous
 * version interpolated them into an HTML template unescaped.
 */
window.gamedata = {

	waiting: true,
	games: null,
	thisplayer: 0,

	parseServerData: function parseServerData(serverdata) {
		if (serverdata == null) return;
		this.games = serverdata;
		this.renderGames();
	},

	/*
	 * Full re-render from state, every time.
	 *
	 * The old createGames() only ever APPENDED: the whole card build — including the
	 * waitingForTurn highlight — sat behind an "is this card already in the DOM?" guard,
	 * so a refetch could never update a card that already existed. A game that became
	 * your turn kept showing the stale state until a full page reload. It also re-bound
	 * click handlers on every call, accumulating duplicates on every BFCache restore.
	 * Cards are now plain <a> elements, so there are no handlers to bind at all.
	 */
	renderGames: function renderGames() {
		var active = [];
		var lobby = [];

		for (var i in this.games) {
			var game = this.games[i];
			//the stampede-protection sentinel is a bare {status:'GENERATING'} row
			if (!game || !game.id) continue;

			if (game.status === "ACTIVE") active.push(game);
			else if (game.status === "LOBBY") lobby.push(game);
		}

		//Your-turn games first, then newest. Without this the lists arrive in DB order,
		//and a player with a lot of games can have the ones waiting on them sitting
		//below the fold of a scrolling list — which defeats the highlight.
		active.sort(function (a, b) {
			if (!a.waiting !== !b.waiting) return a.waiting ? 1 : -1;
			return b.id - a.id;
		});
		lobby.sort(function (a, b) { return b.id - a.id; });

		this.fillList(jQuery(".gamecontainer.active"), active,
			"No active games.", "Create one, or join a game opposite.");
		this.fillList(jQuery(".gamecontainer.lobby"), lobby,
			"No games are starting right now.", "Create one to open a lobby.");

		var yourTurn = 0;
		for (var a = 0; a < active.length; a++) if (!active[a].waiting) yourTurn++;

		jQuery('[data-fv-count="active"]').text(
			yourTurn ? yourTurn + " waiting on you"
				: (active.length ? active.length + " active" : ""));
		jQuery('[data-fv-count="lobby"]').text(lobby.length ? lobby.length + " open" : "");
	},

	fillList: function fillList($well, games, emptyTitle, emptyHint) {
		if ($well.length === 0) return;

		if (games.length === 0) {
			$well.html('<div class="fv-empty">' + this.escapeHtml(emptyTitle) +
				"<br>" + this.escapeHtml(emptyHint) + "</div>");
			return;
		}

		var html = "";
		for (var i = 0; i < games.length; i++) html += this.gameCardHtml(games[i]);
		$well.html(html);
	},

	gameCardHtml: function gameCardHtml(game) {
		var isLobby = (game.status === "LOBBY");
		var href = isLobby ? "gamelobby.php?gameid=" + game.id : "game.php?gameid=" + game.id;
		//fleet-test games are all named after their creator ("Dougie's Game"); the label
		//is what players recognise, so keep showing that rather than the row's name
		var name = game.test ? "Fleet Builder" : (game.name || "Unnamed game");

		var classes = ["fv-card"];
		var tags = "";

		if (isLobby) {
			if (game.test) {
				classes.push("is-fleet");
				tags += this.tagHtml("fleet", "Fleet Build Only");
			} else {
				//a joinable game keeps the green rail whether or not it is ranked — in this
				//column the rail answers "can I join this?" and the tag answers "is it
				//ranked?" (user 2026-07-26). Your Games still takes the gold rail, where
				//there is no green to preserve.
				classes.push("is-lobby");
				if (game.ladder) tags += this.tagHtml("ladder", "Ladder");
			}
		} else {
			//waiting == 0 means this player still owes an action in that game
			classes.push(game.waiting ? "is-waiting" : "is-yourturn");
			//is-ladder LAST: its gold rail wins over the your-turn rail while the
			//your-turn fill stays (the precedence is documented in gamesPanel.css)
			if (game.ladder) {
				classes.push("is-ladder");
				tags += this.tagHtml("ladder", "Ladder");
			}
			if (!game.waiting) tags += this.tagHtml("you", "Your turn");
		}

		var bits = [];
		if (game.turn > 0) bits.push("TURN " + game.turn);
		bits.push(game.test ? "SOLO" : (game.playerCount + "/" + game.slots + " PLAYERS"));
		if (game.map) bits.push(this.mapLabel(game.map));
		if (game.rules && game.rules.length) bits = bits.concat(game.rules);

		return '<a class="' + classes.join(" ") + '" href="' + href +
			'" title="' + this.escapeHtml(name) + '">' +
			'<span class="fv-card-top">' +
			'<span class="fv-card-name">' + this.escapeHtml(name) + "</span>" + tags +
			"</span>" +
			'<span class="fv-card-data">' + this.escapeHtml(bits.join(" · ")) + "</span>" +
			"</a>";
	},

	tagHtml: function tagHtml(kind, label) {
		return '<span class="fv-tag fv-tag--' + kind + '">' + this.escapeHtml(label) + "</span>";
	},

	//"42x30" reads better as "42×30"; "OPEN" passes through untouched
	mapLabel: function mapLabel(map) {
		return String(map).replace(/x/i, "×");
	},

	escapeHtml: function escapeHtml(value) {
		return String(value == null ? "" : value).replace(/[&<>"']/g, function (c) {
			return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c];
		});
	},

	submitFleetTest: function submitFleetTest() {
		var gamename = gamedata.defaultGameName || "Test Game";
		var background = gamedata.defaultBackground || "21.PurpleNebula.jpg";

		var slots = [
			{ id: 1, team: 1, name: "Team 1", points: 3500, depx: -19, depy: 0, deptype: "box", depwidth: 5, depheight: 30, depavailable: 1 },
			{ id: 2, team: 2, name: "Team 2", points: 3500, depx: 18, depy: 0, deptype: "box", depwidth: 5, depheight: 30, depavailable: 1 }
		];

		var rules = {
			fleetTest: 1
		};

		var gamespace = "42x30"; // Standard map dimensions
		var flight = ""; // Default no variable flight size
		var description = "";

		var data = {
			gamename: gamename,
			background: background,
			slots: slots,
			gamespace: gamespace,
			flight: flight,
			rules: rules,
			description: description
		};

		var form = $('<form>', {
			'action': 'creategame.php',
			'method': 'post'
		}).append($('<input>', {
			'type': 'hidden',
			'name': 'docreate',
			'value': 'true'
		})).append($('<input>', {
			'type': 'hidden',
			'name': 'data',
			'value': JSON.stringify(data)
		}));

		$('body').append(form);
		form.submit();
	}

};

window.animation = {
	animateWaiting: function animateWaiting() { }
};
