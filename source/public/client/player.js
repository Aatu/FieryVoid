window.playerManager = {

	isOccupiedSlot: function(slot){
	
		for (var i in gamedata.players){
			var player = gamedata.players[i];
			if (player.slot == slot)
				return true;
		}
		
		return false;
	
	},
	
	getPlayerInSlot: function(slot){
		for (var i in gamedata.players){
			var player = gamedata.players[i];
			if (player.slot == slot)
				return player;
		}
		
		return null;
	},
	
	isInGame: function(){
	
		for (var i in gamedata.players){
			var player = gamedata.players[i];
			if (player.id == gamedata.thisplayer)
				return true;
		}
		
		return false;
	
	}


}