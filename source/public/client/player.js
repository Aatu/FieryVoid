"use strict";

window.playerManager = {

	isOccupiedSlot: function isOccupiedSlot(slot) {
		if (slot.playerid) return true;

		return false;
	},

	getPlayerInSlot: function getPlayerInSlot(slot) {
		return { id: slot.playerid, name: slot.playername };
	},

	isInGame: function isInGame() {

		for (var i in gamedata.slots) {
			var slot = gamedata.slots[i];
			if (slot.playerid == gamedata.thisplayer) return true;
		}

		return false;
	},

	getSlotById: function getSlotById(id) {
		for (var i in gamedata.slots) {
			var slot = gamedata.slots[i];
			if (slot.slot == id) return slot;
		}

		return null;
	}
};