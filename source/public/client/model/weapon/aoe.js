"use strict";

var Aoe = function Aoe(json, ship) {
	Weapon.call(this, json, ship);
};
Aoe.prototype = Object.create(Weapon.prototype);
Aoe.prototype.constructor = Aoe;

var EnergyMine = function EnergyMine(json, ship) {
	Aoe.call(this, json, ship);
};
EnergyMine.prototype = Object.create(Aoe.prototype);
EnergyMine.prototype.constructor = EnergyMine;


var LightEnergyMine = function LightEnergyMine(json, ship) {
	Aoe.call(this, json, ship);
};
LightEnergyMine.prototype = Object.create(Aoe.prototype);
LightEnergyMine.prototype.constructor = LightEnergyMine;


var CaptorMine = function CaptorMine(json, ship) {
	Aoe.call(this, json, ship);
};
CaptorMine.prototype = Object.create(Aoe.prototype);
CaptorMine.prototype.constructor = CaptorMine;

CaptorMine.prototype.initializationUpdate = function () {
	var ship = this.ship;
	var stealthSystem = shipManager.systems.getSystemByName(ship, "mineStealth");
	if (stealthSystem && !stealthSystem.isMineRevealed(ship)) {
		this.range = 0;
	}

	this.refreshData();
	return this
}

CaptorMine.prototype.getCurrClass = function () { //get current FC class for display; if none, find first!
	if (this.currClass == '') {
		var classes = Object.keys(this.allocatedRanges); //Allocated is always the same for HC, so can serve same purpose as availableAA did.
		if (classes.length > 0) {
			this.currClass = classes[0];
		}
	}
	return this.currClass;
};

CaptorMine.prototype.canIncrease = function () { //check if can increase rating for current class; can do if preallocated points are unused or allocated points are less than available 
	//always needs to check that allocated are less than maximum and allocated total is less than total maximum
	var ship = this.ship;
	var spawned = Number(ship.spawned);
	var deploymentTurn = (spawned === -1) ? 1 : spawned + 1;
	//console.log("Mine: " + ship.name + " (id: " + ship.id + ") | spawned: " + ship.spawned + " | turn: " + gamedata.turn + " | deploymentTurn: " + deploymentTurn);

	if (gamedata.turn !== deploymentTurn) {
		//console.log("  BLOCKED: Not deployment turn");
		return false;
	}
	this.getCurrClass();
	if (this.currClass == '') return false; //this would mean there are no FC classes whatsover! Should never happen.

	//how many are allocated?
	var allocated = (this.allocatedRanges[this.currClass] === null) ? this.range : this.allocatedRanges[this.currClass];
	//how many are allowed?
	var allowed = this.range;
	if (allocated >= allowed) return false; //full allowance for this FC type filled	

	//if (available <= 0) { //Could go under 0 after damage?
	//	return false;
	//}
	return true;
};
CaptorMine.prototype.canDecrease = function () { //can decrease if something was increased
	var ship = this.ship;
	var spawned = Number(ship.spawned);
	var deploymentTurn = (spawned === -1) ? 1 : spawned + 1;
	if (gamedata.turn !== deploymentTurn) return false;
	this.getCurrClass(); //Should be getCurrClass or similar? The method in aoe.js is getCurrClass
	if (this.currClass == '') return false;

	var allocated = (this.allocatedRanges[this.currClass] === null) ? this.range : this.allocatedRanges[this.currClass];
	if (allocated > 0) return true;
	return false;
};
CaptorMine.prototype.doIncrease = function () { //increase BFCP usage
	this.getCurrClass();

	if (this.currClass == '') return false; //this would mean there are no FC classes whatsover! Should never happen.

	var allocated = (this.allocatedRanges[this.currClass] === null) ? this.range : this.allocatedRanges[this.currClass];

	if (allocated < this.range) { //else use regular pool 
		this.allocatedRanges[this.currClass] = allocated + 1;

		//this.BFCPtotal_used++;
	}
	this.mineSet = true; //user changed something, assume they are content.	
	this.refreshData();
};
CaptorMine.prototype.doDecrease = function () { //decrease BFCP usage
	this.getCurrClass();
	if (this.currClass == '') return false; //this would mean there are no FC classes whatsover!
	//Decrease could be in current turn, or from previous turn allocation.
	var allocated = (this.allocatedRanges[this.currClass] === null) ? this.range : this.allocatedRanges[this.currClass];

	if (allocated > 0) {
		this.allocatedRanges[this.currClass] = allocated - 1;
		//this.BFCPtotal_used--;
	}
	this.mineSet = true; //user changed something, assume they are content.	
	this.refreshData();
};

CaptorMine.prototype.refreshData = function () { //refresh description to show correct values
	var classes = Object.keys(this.allocatedRanges);
	var entryName = '';
	var currType = '';
	var range = null;
	var hiddenDisplay = '';
	var ship = this.ship;
	if(gamedata.gamephase !== -2) if(!gamedata.isMyOrTeamOneShip(ship)) hiddenDisplay = '?';

    var stealthSystem = shipManager.systems.getSystemByName(ship, "mineStealth");
    //if (stealthSystem && !stealthSystem.isMineRevealed(ship)) {
    //    //hiddenDisplay = "?";
	//	this.data["Max Range"] = hiddenDisplay;		
    //}else{
	//	this.data["Max Range"] = this.range;			
	//}

	this.data["Fire control (fighter/med/cap)"] = this.fireControl[0]*5 + '/' + this.fireControl[1]*5 + '/' + this.fireControl[2]*5;

	for (var i = 0; i < classes.length; i++) {
		currType = classes[i];
		range = this.allocatedRanges[currType];
		if(range == null) range = this.range;
		if(hiddenDisplay == '?') range = hiddenDisplay;
		//entry should exist, just change it to show current values
		entryName = ' - ' + currType;
		this.data[entryName + " range"] = range;
	}

	//rebuild damage display from current minDamage/maxDamage
	if (this.minDamage === this.maxDamage) {
		this.data["Damage"] = this.maxDamage;
	} else {
		this.data["Damage"] = this.minDamage + "-" + this.maxDamage;
	}
};

CaptorMine.prototype.canPropagate = function () { //can propagate if set to >0
	if (this.currClass == '') return false; //this would mean there are no FC classes whatsover!
	if (this.allocatedRanges[this.currClass] > 0) return true;
	return false;
};

CaptorMine.prototype.getRangeAllocated = function (rangeIndex) { //returns setting for current FC type

	var rangeSet = 0;
	var rangeValues = Object.values(this.allocatedRanges);
	rangeSet = rangeValues[rangeIndex];
	return rangeSet;
};
CaptorMine.prototype.setCurrShipType = function (shipType) { //sets indicated FC type as current (or sets empty as current)
	this.currClass = ''; //will do if desired type does not exist here, which is rare but possible
	var classes = Object.keys(this.allocatedRanges);
	var currType = '';
	for (var i = 0; i < classes.length; i++) {
		currType = classes[i];
		if (currType == shipType) { //exists!
			this.currClass = currType;
			return; //no need to loop further
		}
	}
};
CaptorMine.prototype.doIndividualNotesTransfer = function () { //prepare individualNotesTransfer variable - if relevant for this particular system

	if (gamedata.gamephase == -1) {
		this.individualNotesTransfer = {};
		//every point is denoted as single entry with damage class name
		var shipCategories = Object.keys(this.allocatedRanges);
		var rangeValues = Object.values(this.allocatedRanges);

		for (var i = 0; i < shipCategories.length; i++) {
			var currType = shipCategories[i];
			if (rangeValues[i] == null) rangeValues[i] = this.range; //Set to max range if nothing set by player.

			// Initialize the array for the current spec
			this.individualNotesTransfer[currType] = rangeValues[i];
		}
	}
	return true;
};


var ProximityMine = function ProximityMine(json, ship) {
	Aoe.call(this, json, ship);
};
ProximityMine.prototype = Object.create(Aoe.prototype);
ProximityMine.prototype.constructor = ProximityMine;

ProximityMine.prototype.initializationUpdate = function () {
	var ship = this.ship;
	var stealthSystem = shipManager.systems.getSystemByName(ship, "mineStealth");
	if (stealthSystem && !stealthSystem.isMineRevealed(ship)) {
		this.range = 0;
	}

	this.refreshData();
	return this
}

ProximityMine.prototype.getCurrClass = function () { //get current FC class for display; if none, find first!
	if (this.currClass == '') {
		var classes = Object.keys(this.allocatedShipTypes); //Allocated is always the same for HC, so can serve same purpose as availableAA did.
		if (classes.length > 0) {
			this.currClass = classes[0];
		}
	}
	return this.currClass;
};

ProximityMine.prototype.canSet = function () { //check if can increase rating for current class; can do if preallocated points are unused or allocated points are less than available 
	//always needs to check that allocated are less than maximum and allocated total is less than total maximum
	var ship = this.ship;
	var spawned = Number(ship.spawned);
	var deploymentTurn = (spawned === -1) ? 1 : spawned + 1;

	if (gamedata.turn !== deploymentTurn) {
		return false;
	}
	this.getCurrClass();
	if (this.currClass == '') return false; //this would mean there are no FC classes whatsover! Should never happen.

	//how many are allocated?
	var allocated = (this.allocatedShipTypes[this.currClass]);
	//Is it set to false?
	if (allocated) return false; //Already set to true	

	return true;
};

ProximityMine.prototype.doSet = function () { //increase BFCP usage
	this.getCurrClass();

	if (this.currClass == '') return false; //this would mean there are no FC classes whatsover! Should never happen.

	this.allocatedShipTypes[this.currClass] = true;

	this.mineSet = true; //user changed something, assume they are content.	
	this.refreshData();
};

ProximityMine.prototype.canUnset = function () { //can decrease if something was increased
	//always needs to check that allocated are less than maximum and allocated total is less than total maximum
	var ship = this.ship;
	var spawned = Number(ship.spawned);
	var deploymentTurn = (spawned === -1) ? 1 : spawned + 1;

	if (gamedata.turn !== deploymentTurn) {
		return false;
	}
	this.getCurrClass();
	if (this.currClass == '') return false; //this would mean there are no FC classes whatsover! Should never happen.

	//how many are allocated?
	var allocated = (this.allocatedShipTypes[this.currClass]);
	//Is it set to false?
	if (!allocated) return false; //Already set to false	

	return true;
};

ProximityMine.prototype.doUnset = function () { //decrease BFCP usage
	this.getCurrClass();

	if (this.currClass == '') return false; //this would mean there are no FC classes whatsover! Should never happen.

	this.allocatedShipTypes[this.currClass] = false;

	this.mineSet = true; //user changed something, assume they are content.	
	this.refreshData();
};

ProximityMine.prototype.refreshData = function () { //refresh description to show correct values
	var classes = Object.keys(this.allocatedShipTypes);
	var entryName = '';
	var currType = '';
	var attack = null;
	var hiddenDisplay = '';
	var ship = this.ship;
	if(gamedata.gamephase !== -2) if(!gamedata.isMyOrTeamOneShip(ship)) hiddenDisplay = '?';
	
    var stealthSystem = shipManager.systems.getSystemByName(ship, "mineStealth");
    //if (stealthSystem && !stealthSystem.isMineRevealed(ship)) {
	//	this.data["Max Range"] = hiddenDisplay;		
    //}else{
	//	this.data["Max Range"] = this.range;		
	//}
	
	for (var i = 0; i < classes.length; i++) {
		currType = classes[i];
		attack = this.allocatedShipTypes[currType];
		if(hiddenDisplay == '?'){ attack = hiddenDisplay;
		}else if(attack == true){ 
			attack = "Yes";
		}else{
			attack = "No";			
		}	
		//entry should exist, just change it to show current values
		//entryName = ' - ' + currType;
		this.data[" - Attack " + currType] = attack;
	}

	//rebuild damage display from current minDamage/maxDamage
	if (this.minDamage === this.maxDamage) {
		this.data["Damage"] = this.maxDamage;
	} else {
		this.data["Damage"] = this.minDamage + "-" + this.maxDamage;
	}
};

ProximityMine.prototype.canPropagate = function () { //can propagate if set to >0
	if (this.currClass == '') return false; //this would mean there are no FC classes whatsover!
	//if (this.allocatedShipTypes[this.currClass] > 0) return true;
	return false;
};


ProximityMine.prototype.setCurrShipType = function (shipType) { //sets indicated FC type as current (or sets empty as current)
	this.currClass = ''; //will do if desired type does not exist here, which is rare but possible
	var classes = Object.keys(this.allocatedShipTypes);
	var currType = '';
	for (var i = 0; i < classes.length; i++) {
		currType = classes[i];
		if (currType == shipType) { //exists!
			this.currClass = currType;
			return; //no need to loop further
		}
	}
};
ProximityMine.prototype.doIndividualNotesTransfer = function () { //prepare individualNotesTransfer variable - if relevant for this particular system

	if (gamedata.gamephase == -1) {
		this.individualNotesTransfer = {};
		//every point is denoted as single entry with damage class name
		var shipCategories = Object.keys(this.allocatedShipTypes);
		var attackValues = Object.values(this.allocatedShipTypes);

		for (var i = 0; i < shipCategories.length; i++) {
			var currType = shipCategories[i];
			//if (attackValues[i] == null) attackValues[i] = this.range; //Set to max range if nothing set by player.

			// Initialize the array for the current spec
			this.individualNotesTransfer[currType] = attackValues[i];
		}
	}
	return true;
};

