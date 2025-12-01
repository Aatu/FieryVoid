"use strict";

var Torpedo = function Torpedo(json, ship) {
    Ballistic.call(this, json, ship);
};
Torpedo.prototype = Object.create(Ballistic.prototype);
Torpedo.prototype.constructor = Torpedo;

var BallisticTorpedo = function BallisticTorpedo(json, ship) {
    Torpedo.call(this, json, ship);
};
BallisticTorpedo.prototype = Object.create(Torpedo.prototype);
BallisticTorpedo.prototype.constructor = BallisticTorpedo;

BallisticTorpedo.prototype.initializationUpdate = function() {
	if(this.fireOrders.length == 0){
		this.data["Number of shots"] = this.turnsloaded;
		this.maxVariableShots = this.turnsloaded; 
	}else{
		this.data["Number of shots"] = this.maxVariableShots;
	}
	return this;
};

BallisticTorpedo.prototype.doMultipleFireOrders = function (shooter, target, system) {
	
/* //Can be used to restruct only one shot against a ship.
	var fireOrders = this.fireOrders;
	for (var i = fireOrders.length - 1; i >= 0; i--) {
		if(target.shipSizeClass >= 0 && target.id === fireOrders[i].targetid && this.firingMode == 1) return; //Ships cannot be targeted more than once in normal firing mode.
	}	
*/		
	//Don't add Firing Order and give player error message if they are out of ammo.
	if(this.data["Number of shots"] <= 0){
		confirm.error("A Ballistic Torpedo launcher does not have enough ammo to target another shot.");		
		return; //No more shots to allocated!
	}
	
	for (var s = 0; s < this.guns; s++) {
		var fireid = shooter.id + "_" + this.id + "_" + (this.fireOrders.length + 1);
                        
		var calledid = -1; //Ballistic Topredo CANNOT make called shots.

	    var damageClass = this.data["Weapon type"].toLowerCase();
	    var chance = window.weaponManager.calculateHitChange(shooter, target, this, calledid);
/*
//Ballisitic Torpedo is, unsurprisingly, always a ballistic weapon :)
	    if ((chance < 1)&&(!this.ballistic)) {//now ballistics can be launched when hit chance is 0 or less - important for Packet Torpedo!
		    //debug && console.log("Can't fire, change < 0");
		    continue;
	    }
*/

	    var fire = {
	        id: fireid,
	        type: 'ballistic',
	        shooterid: shooter.id,
	        targetid: target.id,
	        weaponid: this.id,
	        calledid: calledid,
	        turn: gamedata.turn,
	        firingMode: this.firingMode,
	        shots: this.defaultShots,
	        x: "null",
	        y: "null",
	        damageclass: damageClass,
	        chance: chance,
			hitmod: 0,	        
	        notes: "Split"	        
	        };
		
		this.maxVariableShots -= fire.shots; 
			        
    	return fire;
	}
};

BallisticTorpedo.prototype.checkFinished = function () {
	if(this.fireOrders.length == this.turnsloaded) return true; 
    return false;
};

var IonTorpedo = function IonTorpedo(json, ship) {
    Torpedo.call(this, json, ship);
};
IonTorpedo.prototype = Object.create(Torpedo.prototype);
IonTorpedo.prototype.constructor = IonTorpedo;

var PlasmaWaveTorpedo = function PlasmaWaveTorpedo(json, ship) {
    Torpedo.call(this, json, ship);
};
PlasmaWaveTorpedo.prototype = Object.create(Torpedo.prototype);
PlasmaWaveTorpedo.prototype.constructor = PlasmaWaveTorpedo;

var PacketTorpedo = function PacketTorpedo(json, ship) {
    Torpedo.call(this, json, ship);
};
PacketTorpedo.prototype = Object.create(Torpedo.prototype);
PacketTorpedo.prototype.constructor = PacketTorpedo;
PacketTorpedo.prototype.calculateSpecialRangePenalty = function (distance) {
    var distancePenalized = Math.max(0,distance - 10); //ignore first 10 hexes
    var rangePenalty = this.rangePenalty * distancePenalized;
    return rangePenalty;
};

var PsionicTorpedo = function PsionicTorpedo(json, ship) {
    Weapon.call(this, json, ship);
};
PsionicTorpedo.prototype = Object.create(Weapon.prototype);
PsionicTorpedo.prototype.constructor = PsionicTorpedo;

PsionicTorpedo.prototype.clearBoost = function () {
    for (var i in system.power) {
        var power = system.power[i];
        if (power.turn != gamedata.turn) continue;

        if (power.type == 2) {
            system.power.splice(i, 1);

            return;
        }
    }
}; 

PsionicTorpedo.prototype.hasMaxBoost = function () {
    return true;
};

PsionicTorpedo.prototype.getMaxBoost = function () {
    return this.maxBoostLevel;
};

PsionicTorpedo.prototype.initBoostableInfo = function () {
    if (window.weaponManager.isLoaded(this)) {} else {
        var count = shipManager.power.getBoost(this);
        for (var i = 0; i < count; i++) {
            shipManager.power.unsetBoost(null, this);
        }
    }	

    this.data.Boostlevel = shipManager.power.getBoost(this);
    	
    switch (shipManager.power.getBoost(this)) {
        case 0:
            this.data["Damage"] = '13 - 22';
            this.data["Boostlevel"] = '0';
            break;
        case 1:
            this.data["Damage"] = '17 - 26';
            this.data["Boostlevel"] = '1';
            break;
        case 2:
            this.data["Damage"] = '21 - 30';
            this.data["Boostlevel"] = '2';
            break;          
        default:
            this.data["Damage"] = '13 - 22';
            this.data["Boostlevel"] = '0';
            break;
    }
    
    return this;
};

var LimpetBoreTorpedo = function  LimpetBoreTorpedo(json, ship) {
    Torpedo.call(this, json, ship);
};
LimpetBoreTorpedo.prototype = Object.create(Torpedo.prototype);
LimpetBoreTorpedo.prototype.constructor =  LimpetBoreTorpedo;

var LimpetBoreTorpedoBase = function  LimpetBoreTorpedoBase(json, ship) {
    Torpedo.call(this, json, ship);
};
LimpetBoreTorpedoBase.prototype = Object.create(Torpedo.prototype);
LimpetBoreTorpedoBase.prototype.constructor =  LimpetBoreTorpedoBase;

var FlexPacketTorpedo = function FlexPacketTorpedo(json, ship) {
    Torpedo.call(this, json, ship);
};
FlexPacketTorpedo.prototype = Object.create(Torpedo.prototype);
FlexPacketTorpedo.prototype.constructor = FlexPacketTorpedo;
FlexPacketTorpedo.prototype.calculateSpecialRangePenalty = function (distance) {

	if(this.turnsloaded == 1 || this.firedInRapidMode){ //TurnsLoaded for Intial Orders, Rapid marker for rest of turn.
	    var distancePenalized = Math.max(0,distance - 5); //Normal range penalty in rapid mode
	    var rangePenalty = this.rangePenalty * distancePenalized;
	    return rangePenalty;				
	}else{	
	    var distancePenalized = Math.max(0,distance - 20); //ignore first 10 hexes
	    var rangePenalty = this.rangePenalty * distancePenalized;
	    return rangePenalty;
	}
};

FlexPacketTorpedo.prototype.doIndividualNotesTransfer = function () { //prepare individualNotesTransfer variable
    // here: transfer information about firing in Rapid mode (e.g., weapon is being fired after 1 turn of arming)
	var toReturn = false;
 	this.individualNotesTransfer = Array();	
  	//Check for fire order and check Initial Orders  	
    if ((this.fireOrders.length > 0) && (gamedata.gamephase == 1)) {		
		//Check for 1 turn loaded, as this will mean it has to be fired in Rapid Mode.	
		if (this.turnsloaded == 1) {
			this.individualNotesTransfer.push('R');
			toReturn = true;
		}	
	}	
			
	return toReturn;
 
};



