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
	
		for (var i in gamedata.slots){
			var slot = gamedata.slots[i];
			if (slot.playerid == gamedata.thisplayer)
				return true;
		}
		
		return false;
	
	},

    getSlotById: function(id){
        for (var i in gamedata.slots){
            var slot = gamedata.slots[i];
            if (slot.slot == id)
                return slot;
        }
        
        return null;
    }
}