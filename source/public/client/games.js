window.gamedata = {

	waiting: true,
	games: null,
	thisplayer: 0,

	createFireDiv: function(data){		
		var target = document.getElementById("fireList");
			target.innerHTML = "";

		if (data){
			for (var i = 0; i < data.length; i++){
				var id = data[i].id;

				var div = document.createElement("div");
					div.className = "game slot clickable"

				var link = document.createElement("a");
					link.setAttribute("href", "http://fieryvoid.net/game.php?gameid=" + id);
					//link.innerHTML = "Anonymous Match" + " @ Turn " + data[i].turn;
					link.innerHTML = data[i].name + " @ Turn " + data[i].turn;

				div.appendChild(link);
				
				target.appendChild(div);
			}
		}
		else {
			var div = document.createElement("div");
				div.className = "game slot clickable";
				div.innerHTML = "No Ongoing Fire Phase found";
				
			target.appendChild(div);
		}

		//target.appendChild(div); //moved inside if/else clause
	},
	
	parseServerData: function(serverdata){
	
		if (serverdata == null)
			return;
			
        this.games = serverdata;
		
		this.createGames();
		$('.lobby .game').on("click", this.clickLobbyGame);
		$('.active .game').on("click", this.clickActiveGame);
	},
	
	createGames: function(){
	
		var gamehtml = '<div class="game slot clickable" data-gameid="{gameid}"><span class="name">{gamename}</span><span class="value players">players: {players}/{maxplayers}</span></div>'; 
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
					
					gameDOM = $(html);
                    gameDOM.find('.players').remove();
                    
                    if (game.waitingForThisPlayer)
                        gameDOM.addClass("waitingForTurn");
                    
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
					html = html.replace("{players}", gamedata.getNumberOfPlayers(game));
					html = html.replace("{maxplayers}", Object.keys(game.slots).length);
                    
					gameDOM = $(html);
					gameDOM.appendTo($('.gamecontainer.lobby'));
					$('.gamecontainer.lobby').addClass("found");
				}else{
					$('.players', gameDOM).html("players: "
                        + gamedata.getNumberOfPlayers(game)
                        +"/"
                        + Object.keys(game.slots).length);
                        
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
	
    getNumberOfPlayers: function(game)
    {
        var count = 0;
        for (var i in game.slots)
        {
            if (game.slots[i].playerid != null)
                count++;
        }
   //     console.log(game.name +": "+ count);
        return count;
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
		window.location = "game.php?gameid="+id;
	},
	
	clickLobbyGame: function(e){
		var id = $(this).data().gameid;
		window.location = "gamelobby.php?gameid="+id;
	}
	
	
	
	
	


}

window.animation = {
	animateWaiting: function(){}
}



