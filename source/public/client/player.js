window.playerManager = {

	isOccupiedSlot: function(slot){
	
		
        if (slot.playerid)
            return true;
		
		return false;
	
	},
	
	getPlayerInSlot: function(slot){
		return {id:slot.playerid, name:slot.playername};
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