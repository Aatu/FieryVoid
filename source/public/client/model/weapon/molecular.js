"use strict";

var Molecular = function Molecular(json, ship) {
    Weapon.call(this, json, ship);
};
Molecular.prototype = Object.create(Weapon.prototype);
Molecular.prototype.constructor = Molecular;

var FusionCannon = function FusionCannon(json, ship) {
    Molecular.call(this, json, ship);
};
FusionCannon.prototype = Object.create(Molecular.prototype);
FusionCannon.prototype.constructor = FusionCannon;

var HeavyFusionCannon = function HeavyFusionCannon(json, ship) {
    Molecular.call(this, json, ship);
};
HeavyFusionCannon.prototype = Object.create(Molecular.prototype);
HeavyFusionCannon.prototype.constructor = HeavyFusionCannon;

var LightfusionCannon = function LightfusionCannon(json, ship) {
    Molecular.call(this, json, ship);
};
LightfusionCannon.prototype = Object.create(Molecular.prototype);
LightfusionCannon.prototype.constructor = LightfusionCannon;

var MolecularDisruptor = function MolecularDisruptor(json, ship) {
    Molecular.call(this, json, ship);
};
MolecularDisruptor.prototype = Object.create(Molecular.prototype);
MolecularDisruptor.prototype.constructor = MolecularDisruptor;

var SuperHeavyMolecularDisruptor = function SuperHeavyMolecularDisruptor(json, ship) {
    Molecular.call(this, json, ship);
};
SuperHeavyMolecularDisruptor.prototype = Object.create(Molecular.prototype);
SuperHeavyMolecularDisruptor.prototype.constructor = SuperHeavyMolecularDisruptor;



var LightMolecularDisruptorShip = function LightMolecularDisruptorShip(json, ship) {
    Molecular.call(this, json, ship);
};
LightMolecularDisruptorShip.prototype = Object.create(Molecular.prototype);
LightMolecularDisruptorShip.prototype.constructor = LightMolecularDisruptorShip;

var MolecularPenetrator = function MolecularPenetrator(json, ship) {
    Molecular.call(this, json, ship);
};
MolecularPenetrator.prototype = Object.create(Molecular.prototype);
MolecularPenetrator.prototype.constructor = MolecularPenetrator;

var DestabilizerBeam = function DestabilizerBeam(json, ship) {
    Molecular.call(this, json, ship);
};
DestabilizerBeam.prototype = Object.create(Molecular.prototype);
DestabilizerBeam.prototype.constructor = DestabilizerBeam;

var MolecularFlayer = function MolecularFlayer(json, ship) {
    Molecular.call(this, json, ship);
};
MolecularFlayer.prototype = Object.create(Molecular.prototype);
MolecularFlayer.prototype.constructor = MolecularFlayer;

var FusionAgitator = function FusionAgitator(json, ship) {
    Molecular.call(this, json, ship);
};
FusionAgitator.prototype = Object.create(Molecular.prototype);
FusionAgitator.prototype.constructor = FusionAgitator;

FusionAgitator.prototype.clearBoost = function () {
    for (var i in system.power) {
        var power = system.power[i];
        if (power.turn != gamedata.turn) continue;

        if (power.type == 2) {
            system.power.splice(i, 1);

            return;
        }
    }
};

FusionAgitator.prototype.hasMaxBoost = function () {
    return true;
};

FusionAgitator.prototype.getMaxBoost = function () {
    return this.maxBoostLevel;
};

FusionAgitator.prototype.initBoostableInfo = function () {
    switch (shipManager.power.getBoost(this)) {
        case 0:
            this.data["Damage"] = '15 - 60';
            this.data["Boostlevel"] = '0';
            break;
        case 1:
            this.data["Damage"] = '16 - 70';
            this.data["Boostlevel"] = '1';
            break;
        case 2:
            this.data["Damage"] = '17 - 80';
            this.data["Boostlevel"] = '2';
            break;
        case 3:
            this.data["Damage"] = '18 - 90';
            this.data["Boostlevel"] = '3';
            break;
        case 4:
            this.data["Damage"] = '19 - 100';
            this.data["Boostlevel"] = '4';
            break;
        default:
            this.data["Damage"] = '15 - 60';
            this.data["Boostlevel"] = '0';
            break;
    }
    return this;
};

var EarlyFusionAgitator = function EarlyFusionAgitator(json, ship) {
    Molecular.call(this, json, ship);
};
EarlyFusionAgitator.prototype = Object.create(Molecular.prototype);
EarlyFusionAgitator.prototype.constructor = EarlyFusionAgitator;

var FusionCutter = function FusionCutter(json, ship) {
    Molecular.call(this, json, ship);
};
FusionCutter.prototype = Object.create(Molecular.prototype);
FusionCutter.prototype.constructor = FusionCutter;

var FtrPolarityCannon = function FtrPolarityCannon(json, ship) {
    Weapon.call(this, json, ship);
};
FtrPolarityCannon.prototype = Object.create(Weapon.prototype);
FtrPolarityCannon.prototype.constructor = FtrPolarityCannon;


var MolecularSlicerBeamL = function MolecularSlicerBeamL(json, ship) {
    Weapon.call(this, json, ship);
};
MolecularSlicerBeamL.prototype = Object.create(Weapon.prototype);
MolecularSlicerBeamL.prototype.constructor = MolecularSlicerBeamL;

MolecularSlicerBeamL.prototype.initializationUpdate = function() {
	var shots = 0; //Initialise
	
	switch(this.turnsloaded){
		case 1:
			shots = 4;
		break;
		case 2:
			shots = 6;		
		break;
		case 3:
			shots = 8;		
		break;		
		default:
			shots = 8;		
		break;
	}		

	this.data["Remaining shots"] = shots - this.fireOrders.length;
	return this;
};

MolecularSlicerBeamL.prototype.doMultipleFireOrders = function (shooter, target, system) {
	
 	//Used to restruct only one shot against a ship.
	var fireOrders = this.fireOrders;
	for (var i = fireOrders.length - 1; i >= 0; i--) {
		if(target.shipSizeClass >= 0 && target.id === fireOrders[i].targetid && this.firingMode == 1) return; //Ships cannot be targeted more than once when allocating shots.
	}	
		
	//Don't add Firing Order and give player error message if they are out of ammo.
	if((this.data["Max number of shots"] - this.fireOrders.length) <= 0){
		confirm.error("Molecular Slicer does not have enough damage dice to target another shot.");		
		return; //No more shots to allocated!
	}
	
	for (var s = 0; s < this.guns; s++) {
		var fireid = shooter.id + "_" + this.id + "_" + (this.fireOrders.length + 1);
                        
		var calledid = -1; //Slicers are Raking or Piercing Damage, cannot called sot!

	    var chance = window.weaponManager.calculateHitChange(shooter, target, this, calledid);
        if(chance < 1) continue;

	    var fire = {
	        id: fireid,
	        type: 'normal',
	        shooterid: shooter.id,
	        targetid: target.id,
	        weaponid: this.id,
	        calledid: calledid,
	        turn: gamedata.turn,
	        firingMode: this.firingMode,
	        shots: this.defaultShots,
	        x: "null",
	        y: "null",
	        damageclass: 'Sweeping',
	        chance: chance,
	        hitmod: 5,
	        notes: "Split"
	        };
		
		this.maxVariableShots -= fire.shots;
			        
    	return fire;
	}
};

MolecularSlicerBeamL.prototype.calculateSpecialHitChanceMod = function (target) {
	var mod = 0;
	if(this.firingMode == 1){
		//Check fireOrders length and deduct (length -1 *5)
		var currentShots = this.fireOrders.length; //
		mod -= Math.max(0, currentShots); //This is called when considering the NEXT shot.  So can just use current length as mod.
	}
	return mod; 
};

MolecularSlicerBeamL.prototype.recalculateFireOrders = function (shooter, fireOrderNo) {

    for (let i = 0; i < this.fireOrders.length; i++) {
        const fireOrder = this.fireOrders[i];

        // Ensure we only include fireOrders for the current turn and weapon, and only fireORders AFTER the one we are currently removing.
        if (fireOrder.weaponid === this.id && fireOrder.turn === gamedata.turn && i > fireOrderNo) {
        	var target = gamedata.getShip(fireOrder.targetid);
			fireOrder.chance += fireOrder.hitmod;        	
        }
    }    

};

var MolecularSlicerBeamM = function MolecularSlicerBeamM(json, ship) {
    MolecularSlicerBeamL.call(this, json, ship);
};
MolecularSlicerBeamM.prototype = Object.create(MolecularSlicerBeamL.prototype);
MolecularSlicerBeamM.prototype.constructor = MolecularSlicerBeamM;

MolecularSlicerBeamM.prototype.initializationUpdate = function() {
	var shots = 0; //Initialise
	
	switch(this.turnsloaded){
		case 1:
			shots = 8;
		break;
		case 2:
			shots = 12;		
		break;
		case 3:
			shots = 16;		
		break;		
		default:
			shots = 16;		
		break;
	}		

	this.data["Remaining shots"] = shots - this.fireOrders.length;
	return this;
};

var MolecularSlicerBeamH = function MolecularSlicerBeamH(json, ship) {
    MolecularSlicerBeamL.call(this, json, ship);
};
MolecularSlicerBeamH.prototype = Object.create(MolecularSlicerBeamL.prototype);
MolecularSlicerBeamH.prototype.constructor = MolecularSlicerBeamH;

MolecularSlicerBeamH.prototype.initializationUpdate = function() {
	var shots = 0; //Initialise
	
	switch(this.turnsloaded){
		case 1:
			shots = 8;
		break;
		case 2:
			shots = 16;		
		break;
		case 3:
			shots = 24;		
		break;		
		default:
			shots = 24;		
		break;
	}		

	this.data["Remaining shots"] = shots - this.fireOrders.length;
	return this;
};

var MultiphasedCutterL = function MultiphasedCutterL(json, ship) {
    Weapon.call(this, json, ship);
};
MultiphasedCutterL.prototype = Object.create(Weapon.prototype);
MultiphasedCutterL.prototype.constructor = MultiphasedCutterL;

var MultiphasedCutter = function MultiphasedCutter(json, ship) {
    Weapon.call(this, json, ship);
};
MultiphasedCutter.prototype = Object.create(Weapon.prototype);
MultiphasedCutter.prototype.constructor = MultiphasedCutter;

MultiphasedCutter.prototype.initializationUpdate = function() {
	if (this.firingMode == 2) {
		this.data["Shots Remaining"] = this.guns - this.fireOrders.length;
	} else {
		delete this.data["Shots Remaining"];
	}
	return this;
};

MultiphasedCutter.prototype.doMultipleFireOrders = function (shooter, target, system) {

    var shotsOnTarget = 1; //we're only ever allocating one shot at a time for this weapon.

    if (this.fireOrders.length > 0) {
        if (this.fireOrders.length >= this.guns) {
            // All guns already fired → retarget one gun by removing oldest fireorder.
            this.fireOrders.splice(0, 1);
        }
    } 

    var fireOrdersArray = []; // Store multiple fire orders

    for (var s = 0; s < shotsOnTarget; s++) {
        var fireid = shooter.id + "_" + this.id + "_" + (this.fireOrders.length + 1);
        var calledid = -1; 

        if (system) {
            // When the system is a subsystem, make all damage go through
            // the parent.
            while (system.parentId > 0) {
                system = shipManager.systems.getSystem(ship, system.parentId);
            }

            calledid = system.id;
        }        

        var chance = window.weaponManager.calculateHitChange(shooter, target, this, calledid);
        if(chance < 1) continue;

        var fire = {
            id: fireid,
            type: 'normal',
            shooterid: shooter.id,
            targetid: target.id,
            weaponid: this.id,
            calledid: calledid,
            turn: gamedata.turn,
            firingMode: this.firingMode,
            shots: 1,
            x: "null",
            y: "null",
            damageclass: 'Sweeping', 
            chance: chance,
            hitmod: 0,
            notes: "Split"
        };
        
        fireOrdersArray.push(fire); // Store each fire order
    }
    
    return fireOrdersArray; // Return all fire orders
};
