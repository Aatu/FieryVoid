window.gamedata = {

	waiting: true,
	games: null,
	thisplayer: 0,
	
	parseServerData: function(serverdata){
	
		if (serverdata == null)
			return;
			
        this.games = serverdata;
		
		this.createGames();
		$('.lobby .game').on("click", this.clickLobbyGame);
		$('.active .game').on("click", this.clickActiveGame);
	},
	
	createGames: function(){
	
		var gamehtml = '<div class="game slot clickable" data-gameid="{gameid}"><span class="name">{gamename}</span><span class="value players">players: {players}/2</span></div>'; 
		var activefound = false;
		var lobbyfound = false;
		for (var i in this.games){
			var game = this.games[i];
			var gameDOM = $('.game[data-gameid="'+game.id+'"]');
			//console.log("game name: "+game.name+ " status: " + game.status +" dom.length: "+ gameDOM.length + " in game: " +playerManager.isInGame());
			if (game.status == "ACTIVE" && this.isInGame(game.id)){
				
				if ( gameDOM.length == 0 ){
					var html = gamehtml;
					html = html.replace("{gameid}", game.id);
					html = html.replace("{gamename}", game.name);
					html = html.replace("{players}", Object.keys(game.slots).length);
					
					gameDOM = $(html);
					gameDOM.appendTo($('.gamecontainer.active'));
					$('.gamecontainer.active').addClass("found");
				}
				activefound = true;
			}
			
			if (game.status == "LOBBY"){
				if (gameDOM.length == 0){
					var html = gamehtml;
					html = html.replace("{gameid}", game.id);
					html = html.replace("{gamename}", game.name);
					html = html.replace("{players}", Object.keys(game.slots).length);
					
					gameDOM = $(html);
					gameDOM.appendTo($('.gamecontainer.lobby'));
					$('.gamecontainer.lobby').addClass("found");
				}else{
					$('.players', gameDOM).html("players: " + Object.keys(game.slots).length+"/2");
				}
				lobbyfound = true;
			}
		}
		
		$(".game").each(function(){
			var id = $(this).data().gameid;
			if (!gamedata.gameIdFound(id)){
				$(this).remove();
			}
		
		});
		
		if (!lobbyfound)
			$(".gamecontainer.lobby").removeClass("found");
			
		if (!activefound)
			$(".gamecontainer.active").removeClass("found");
	}, 
	
	gameIdFound: function(id){
		for (var i in this.games){
			if (this.games[i].id == id)
				return true;
		}
		
		return false;
	},
	
	isInGame: function(id){
	
		for (var i in this.games){
			if (this.games[i].id != id)
				continue;
				
			for (var a in this.games[i].slots){
				if (this.games[i].slots[a].playerid == this.thisplayer)
					return true;
			}
		}
		
		return false;
	
	},
	
	clickActiveGame: function(e){
		var id = $(this).data().gameid;
		window.location = "hex.php?gameid="+id;
	},
	
	clickLobbyGame: function(e){
		var id = $(this).data().gameid;
		window.location = "gamelobby.php?gameid="+id;
	}
	
	
	
	
	


}

window.animation = {
	animateWaiting: function(){}
}



