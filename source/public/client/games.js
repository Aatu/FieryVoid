"use strict";

window.gamedata = {

	waiting: true,
	games: null,
	thisplayer: 0,

	createFireDiv: function createFireDiv(data) {
		var target = document.getElementById("fireList");
		target.innerHTML = "";

		if (data) {
			for (var i = 0; i < data.length; i++) {
				var id = data[i].id;

				var div = document.createElement("div");
				div.className = "game slot clickableGames";

				var link = document.createElement("a");
				link.setAttribute("href", "game.php?gameid=" + id); //Amended during PHP8 update - DK 25.6.25
				//link.innerHTML = "Anonymous Match" + " @ Turn " + data[i].turn;
				link.innerHTML = data[i].name + " @ Turn " + data[i].turn;

				div.appendChild(link);

				target.appendChild(div);
			}
		} else {
			var div = document.createElement("div");
			div.className = "notfound";
			div.innerHTML = "No recent games";

			target.appendChild(div);
		}

		//target.appendChild(div); //moved inside if/else clause
	},

	parseServerData: function parseServerData(serverdata) {

		if (serverdata == null) return;

		this.games = serverdata;

		this.createGames();
		$('.lobby .game').on("click", this.clickLobbyGame);
		$('.active .game').on("click", this.clickActiveGame);
	},

	createGames: function createGames() {

		var gamehtml = '<div class="game slot clickableGames" data-gameid="{gameid}"><span class="name">{gamename}</span><br><span class="value players">Players: {players}/{maxplayers}</span></div>';
		var activefound = false;
		var lobbyfound = false;
		console.log("GAMES LOLS", this.games)
		for (var i in this.games) {
			var game = this.games[i];
			var gameDOM = $('.game[data-gameid="' + game.id + '"]');
			if (game.status == "ACTIVE") {

				if (gameDOM.length == 0) {
					var html = gamehtml;
					html = html.replace("{gameid}", game.id);
					html = html.replace("{gamename}", game.name);

					gameDOM = $(html);
					gameDOM.find('.players').remove();

					if (!game.waiting) gameDOM.addClass("waitingForTurn");

					gameDOM.appendTo($('.gamecontainer.active'));
					$('.gamecontainer.active').addClass("found");
				}
				activefound = true;
			}

			if (game.status == "LOBBY") {
				if (gameDOM.length == 0) {
					var html = gamehtml;
					html = html.replace("{gameid}", game.id);
					html = html.replace("{gamename}", game.name);
					html = html.replace("{players}", game.playerCount);
					html = html.replace("{maxplayers}", game.slots);

					gameDOM = $(html);
					gameDOM.appendTo($('.gamecontainer.lobby'));
					$('.gamecontainer.lobby').addClass("found");
				} else {
					$('.players', gameDOM).html("Players: " + game.playerCount + "/" + game.slots);
				}
				lobbyfound = true;
			}
		}

		$(".game").each(function () {
			var id = $(this).data().gameid;
			if (!gamedata.gameIdFound(id)) {
				$(this).remove();
			}
		});

		if (!lobbyfound) $(".gamecontainer.lobby").removeClass("found");

		if (!activefound) $(".gamecontainer.active").removeClass("found");
	},

	getNumberOfPlayers: function getNumberOfPlayers(game) {
		var count = 0;
		for (var i in game.slots) {
			if (game.slots[i].playerid != null) count++;
		}
		return count;
	},

	gameIdFound: function gameIdFound(id) {
		for (var i in this.games) {
			if (this.games[i].id == id) return true;
		}

		return false;
	},

	isInGame: function isInGame(id) {

		for (var i in this.games) {
			if (this.games[i].id != id) continue;

			for (var a in this.games[i].slots) {
				if (this.games[i].slots[a].playerid == this.thisplayer) return true;
			}
		}

		return false;
	},

	clickActiveGame: function clickActiveGame(e) {
		var id = $(this).data().gameid;
		window.location = "game.php?gameid=" + id;
	},

	clickLobbyGame: function clickLobbyGame(e) {
		var id = $(this).data().gameid;
		window.location = "gamelobby.php?gameid=" + id;
	}

};

window.animation = {
	animateWaiting: function animateWaiting() {}
};