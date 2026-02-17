"use strict";

var Gravitic = function Gravitic(json, ship) {
    Weapon.call(this, json, ship);
};
Gravitic.prototype = Object.create(Weapon.prototype);
Gravitic.prototype.constructor = Gravitic;

var GravitonPulsar = function GravitonPulsar(json, ship) {
    Pulse.call(this, json, ship);
};
GravitonPulsar.prototype = Object.create(Pulse.prototype);
GravitonPulsar.prototype.constructor = GravitonPulsar;

GravitonPulsar.prototype.initBoostableInfo = function () {
    // Needed because it can chance during initial phase
    // because of adding extra power.   

    if (window.weaponManager.isLoaded(this)) {
        /*no longer needed
        this.loadingtime = 1 + shipManager.power.getBoost(this);
        this.turnsloaded = 1 + shipManager.power.getBoost(this);
        this.normalload = 1 + shipManager.power.getBoost(this);
        */
    } else {
        var count = shipManager.power.getBoost(this);

        for (var i = 0; i < count; i++) {
            shipManager.power.unsetBoost(null, this);
        }
    }

    this.intercept = this.getInterceptRating();
    this.data.Intercept = this.getInterceptRating() * -5;

    return this;
};

GravitonPulsar.prototype.getInterceptRating = function () {
    return 1 + shipManager.power.getBoost(this);
};

GravitonPulsar.prototype.clearBoost = function () {
    for (var i in system.power) {
        var power = system.power[i];
        if (power.turn != gamedata.turn) continue;
        if (power.type == 2) {
            system.power.splice(i, 1);
            return;
        }
    }
};

GravitonPulsar.prototype.hasMaxBoost = function () {
    return true;
};

GravitonPulsar.prototype.getMaxBoost = function () {
    return this.maxBoostLevel;
};

var GraviticBolt = function GraviticBolt(json, ship) {
    Gravitic.call(this, json, ship);
};
GraviticBolt.prototype = Object.create(Gravitic.prototype);
GraviticBolt.prototype.constructor = GraviticBolt;

GraviticBolt.prototype.initBoostableInfo = function () {
    // Needed because it can chance during initial phase
    // because of adding extra power.

    if (window.weaponManager.isLoaded(this)) {
        /*no longer needed!
        this.loadingtime = 1 + shipManager.power.getBoost(this);
        this.turnsloaded = 1 + shipManager.power.getBoost(this);
        this.normalload = 1 + shipManager.power.getBoost(this);
        */
    } else {
        var count = shipManager.power.getBoost(this);

        for (var i = 0; i < count; i++) {
            shipManager.power.unsetBoost(null, this);
        }
    }

    this.intercept = this.getInterceptRating();
    this.data.Intercept = this.getInterceptRating() * -5;

    return this;
};

GraviticBolt.prototype.getInterceptRating = function () {
    return 1 + shipManager.power.getBoost(this);
};

GraviticBolt.prototype.clearBoost = function () {
    for (var i in system.power) {
        var power = system.power[i];
        if (power.turn != gamedata.turn) continue;
        if (power.type == 2) {
            system.power.splice(i, 1);
            return;
        }
    }
};

GraviticBolt.prototype.hasMaxBoost = function () {
    return true;
};

GraviticBolt.prototype.getMaxBoost = function () {
    return this.maxBoostLevel;
};

var GravitonBeam = function GravitonBeam(json, ship) {
    Weapon.call(this, json, ship);
};
GravitonBeam.prototype = Object.create(Weapon.prototype);
GravitonBeam.prototype.constructor = GravitonBeam;

var LightGravitonBeam = function LightGravitonBeam(json, ship) {
    Gravitic.call(this, json, ship);
};
LightGravitonBeam.prototype = Object.create(Gravitic.prototype);
LightGravitonBeam.prototype.constructor = LightGravitonBeam;

var GraviticCannon = function GraviticCannon(json, ship) {
    Gravitic.call(this, json, ship);
};
GraviticCannon.prototype = Object.create(Gravitic.prototype);
GraviticCannon.prototype.constructor = GraviticCannon;

var GraviticShifter = function GraviticShifter(json, ship) {
    Gravitic.call(this, json, ship);
};
GraviticShifter.prototype = Object.create(Gravitic.prototype);
GraviticShifter.prototype.constructor = GraviticShifter;

GraviticShifter.prototype.calculateSpecialHitChanceMod = function (shooter, target, calledid) {
	var mod = 0;

    if(target.gravitic || target.factionAge >= 3) mod = -3; //-15% to hit gravitic and/or Ancient targets.    
    
    /* //Removed since OEW lock on allies enabled - DK 17.1.26    
    if(shooter.team == target.team){
        var distance = mathlib.getDistanceBetweenShipsInHex(shooter, target).toFixed(2);        
        var rangePenalty = weaponManager.calculateRangePenalty(distance, this);
        mod += rangePenalty; //refund range penalty for friendly units since OEW lock on allies not possible.
    }
    */        
    
	return mod; 
};

var GravityNet = function GravityNet(json, ship) {
    Gravitic.call(this, json, ship);
};
GravityNet.prototype = Object.create(Gravitic.prototype);
GravityNet.prototype.constructor = GravityNet;

GravityNet.prototype.calculateSpecialHitChanceMod = function (shooter, target) {
	var mod = 0;

    if(target.gravitic || target.factionAge >= 3) mod = -3; //-15% to hit gravitic and/or Ancient targets.    
    
    /* //Removed since OEW lock on allies enabled - DK 17.1.26
    if(shooter.team == target.team){
        var distance = mathlib.getDistanceBetweenShipsInHex(shooter, target).toFixed(2);        
        var rangePenalty = weaponManager.calculateRangePenalty(distance, this);
        mod += rangePenalty; //refund range penalty for friendly units since OEW lock on allies not possible.
    } 
    */       
    
	return mod; 
};

GravityNet.prototype.initializationUpdate = function() {
    if(gamedata.gamephase == 1 || gamedata.gamephase == 5){ //update weapon data field to show this gravity net's max movement distance or return to TBD
        this.data["Move Distance"] = this.moveDistance;
    } 
    if (this.fireOrders.length > 0) {
        this.hextarget = true;
        this.ignoresLoS = false;
        if (this.fireOrders.length == 1) {
            if (!weaponManager.isSelectedWeapon(this)) {
                webglScene.customEvent("RemoveTargetedHexagonInArc", { target: this.target, system: this });
            } else if (weaponManager.isSelectedWeapon(this) && this.target) {
                webglScene.customEvent("RemoveTargetedHexagonInArc", { target: this.target, system: this });//Remove any old sprites to prevent duplication.
                webglScene.customEvent("ShowTargetedHexagonInArc", { shooter: this.ship, target: this.target, system: this});
            }
        }
    }else{
        this.hextarget = false;
        this.ignoresLoS = false; 
        if(this.target){   
            webglScene.customEvent("RemoveTargetedHexagonInArc", {target: this.target, system: this});
        }                
    } 

    return this;
};

GravityNet.prototype.doMultipleFireOrders = function (shooter, target, system) {
    var shotsOnTarget = 1; //we're only ever allocating one shot at a time for this weapon in Split mode.

    if (this.fireOrders.length > 0) {
        return;
    } 

    var fireOrdersArray = []; // Store multiple fire orders

    for (var s = 0; s < shotsOnTarget; s++) {
        var fireid = shooter.id + "_" + this.id + "_" + (this.fireOrders.length + 1);
        var calledid = -1; //No called shots.     

        var chance = window.weaponManager.calculateHitChange(shooter, target, this, calledid);
        if(chance < 1) continue;

        var fire = {
            id: fireid,
            type: 'prefiring',
            shooterid: shooter.id,
            targetid: target.id,
            weaponid: this.id,
            calledid: calledid,
            turn: gamedata.turn,
            firingMode: this.firingMode,
            shots: 1,
            x: "null",
            y: "null",
            damageclass: 'gravitic', 
            chance: chance,
            hitmod: 0,
            notes: "Split"
        }; 
        this.target = target; //store current target to this gravity net object.       
        fireOrdersArray.push(fire); // Store each fire order
        
        webglScene.customEvent("ShowTargetedHexagonInArc", {shooter: shooter, target: target, system: this});
        this.hextarget = true; //switch gravNet from shipTarget mode to hexTarget mode.        
    }
    
    return fireOrdersArray; // Return all fire orders
};    

GravityNet.prototype.doMultipleHexFireOrders = function (shooter, hexpos) {
    
    var shotsOnTarget = 1; //we're only ever allocating one shot at a time for this weapon in Split mode.

    if (this.fireOrders.length > 1) {
        return;
    }         
    var targetMoveHexValid = this.validateTargetMoveHex(hexpos, this.moveDistance);

    var fireOrdersArray = []; // Store multiple fire orders

    if(targetMoveHexValid){
        for (var s = 0; s < shotsOnTarget; s++) {
                var fireid = shooter.id + "_" + this.id + "_" + (this.fireOrders.length + 1);
                var fire = {
                    id: fireid,
                    type: 'prefiring',
                    shooterid: shooter.id,
                    targetid: -1,
                    weaponid: this.id,
                    calledid: -1,
                    turn: gamedata.turn,
                    firingMode: this.firingMode,
                    shots: this.defaultShots,
                    x: hexpos.q,
                    y: hexpos.r,
                    damageclass: 'gravNetMoveHex', 
                    notes: "split"                
                };
            fireOrdersArray.push(fire);
        }  
        webglScene.customEvent("RemoveTargetedHexagonInArc", {target: this.target, system: this}); 
    }
    return fireOrdersArray; // Return all fire orders
};  

GravityNet.prototype.validateTargetMoveHex = function(hexpos, maxmoverange){ //function to validate desired target movement hex, will check LOS from target ship to move hex and range and make sure no collisions occur.

    //get gravNetTargetHex to check range and LOS for gravNetTargetMovementHex
    //Target of grav net which will be used as shooter for grav net target hex.
    var valid = false; //default to false
    var gravNetTargetFireOrder = this.fireOrders[0];//get fireorder of grav net firing ship (So we can use it's hex as fireing hex), this should always be the first fire order
    if (gravNetTargetFireOrder){	// check that the grav net firing ship set a fire order
        var targetShip = gamedata.getShip(gravNetTargetFireOrder.targetid);
        var targetShipHex = shipManager.getShipPosition(targetShip);
        var targetMoveHex = hexpos;
        var dist = targetShipHex.distanceTo(targetMoveHex);
        if(dist <= maxmoverange){            
            //var blockedHexes = weaponManager.getBlockedHexes();
	        var blockedHexes = gamedata.blockedHexes; //Are there any blocked hexes, no point checking if no.             
            var loSBlocked = mathlib.isLoSBlocked(targetShipHex, targetMoveHex, blockedHexes);
            if(!loSBlocked && !blockedHexes.some(blocked => blocked.q === targetMoveHex.q && blocked.r === targetMoveHex.r)){//make sure hexpos is a not a blocked hex and LOS is not blocked      
                valid = true ;  
            }    
        }                 
    }

    return valid;
};             

GravityNet.prototype.checkFinished = function () {
	if(this.fireOrders.length > 1) return true;
    return false;
};

var LightGraviticBolt = function LightGraviticBolt(json, ship) {
    Gravitic.call(this, json, ship);
};
LightGraviticBolt.prototype = Object.create(Gravitic.prototype);
LightGraviticBolt.prototype.constructor = LightGraviticBolt;

var UltraLightGraviticBolt = function UltraLightGraviticBolt(json, ship) {
    Gravitic.call(this, json, ship);
};
UltraLightGraviticBolt.prototype = Object.create(Gravitic.prototype);
UltraLightGraviticBolt.prototype.constructor = UltraLightGraviticBolt;


var GraviticLance = function(json, ship) {
    Weapon.call( this, json, ship);
};
GraviticLance.prototype = Object.create( Weapon.prototype );
GraviticLance.prototype.constructor = GraviticLance;

GraviticLance.prototype.initializationUpdate = function() {
	if(this.firingMode == 3){
		this.data["Shots Remaining"] = this.guns - this.fireOrders.length;
	} else {
		delete this.data["Shots Remaining"];
	}

    var ship = this.ship;
	if(gamedata.gamephase !== -2 && shipManager.power.isOverloading(ship, this) && Object.keys(this.sustainedTarget).length > 0){
        const targetId = Object.keys(this.sustainedTarget)[0];
        const target = gamedata.getShip(targetId);
		this.data["Current Target"] = target.name;
	}else{
        delete this.data["Current Target"];       
    }

	return this;
};

GraviticLance.prototype.doMultipleFireOrders = function (shooter, target, system) {

    /*var shotsOnTarget = this.guns; // Default guns initially.  We never want teh palyer to miss firing a shot for such a powerful weapon (and it can't intercept).
    if (this.fireOrders.length == 2) { // Two shots have been locked in, remove the first.
        this.fireOrders.splice(0, 1); // Remove the first fire order.
        shotsOnTarget--; //Reduce guns to 1, the one currently being retargeted!
    }else if(this.fireOrders.length == 1){
        shotsOnTarget--; //Reduce guns to 1, one shot is already locked and we don't want to target 3 :)        
    }
    */

	if(this.firingMode == 3 && this.fireOrders.length > 1) return;  

    var shotsOnTarget = 1;  
    var fireOrdersArray = []; // Store multiple fire orders

    for (var s = 0; s < shotsOnTarget; s++) {
        var fireid = shooter.id + "_" + this.id + "_" + (this.fireOrders.length + 1);
        var calledid = -1; //Grav Beams are raking, can never called shot.

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

GraviticLance.prototype.checkFinished = function () {
	if(this.firingMode == 3 && this.fireOrders.length > 1) return true;    
    return false;
};

var GraviticCutter = function GraviticCutter(json, ship) {
    Gravitic.call(this, json, ship);
};
GraviticCutter.prototype = Object.create(Gravitic.prototype);
GraviticCutter.prototype.constructor = GraviticCutter;

GraviticCutter.prototype.initBoostableInfo = function () {
    // Needed because it can chance during initial phase
    // because of adding extra power.

    this.data["Weapon type"] = "Raking";
    this.data["Damage type"] = "Standard";

    switch (shipManager.power.getBoost(this)) {
        case 0:
            this.data["Damage"] = '10-28';
            break;
        case 1:
            this.data["Damage"] = '13-40';
            break;
        default:
            this.data["Damage"] = '10-28';
            break;
    }

    if (window.weaponManager.isLoaded(this)) {
        this.loadingtime = 2 + shipManager.power.getBoost(this);
        this.turnsloaded = 2 + shipManager.power.getBoost(this);
        this.normalload = 2 + shipManager.power.getBoost(this);
    } else {
        var count = shipManager.power.getBoost(this);

        for (var i = 0; i < count; i++) {
            shipManager.power.unsetBoost(null, this);
        }
    }

    return this;
};

GraviticCutter.prototype.clearBoost = function () {
    for (var i in system.power) {
        var power = system.power[i];
        if (power.turn != gamedata.turn) continue;

        if (power.type == 2) {
            system.power.splice(i, 1);

            return;
        }
    }
};
GraviticCutter.prototype.hasMaxBoost = function () {
    return true;
};
GraviticCutter.prototype.getMaxBoost = function () {
    return this.maxBoostLevel;
};

var HypergravitonBeam = function HypergravitonBeam(json, ship) {
    Gravitic.call(this, json, ship);
};
HypergravitonBeam.prototype = Object.create(Gravitic .prototype);
HypergravitonBeam.prototype.constructor = HypergravitonBeam;

HypergravitonBeam.prototype.initBoostableInfo = function () {
    // Needed because it can change during initial phase
    // because of adding extra power.

    if (window.weaponManager.isLoaded(this)) {

    } else {
        var count = shipManager.power.getBoost(this);

        for (var i = 0; i < count; i++) {
            shipManager.power.unsetBoost(null, this);
        }
    }
    return this;
};

HypergravitonBeam.prototype.clearBoost = function () {
    for (var i in system.power) {
        var power = system.power[i];
        if (power.turn != gamedata.turn) continue;

        if (power.type == 2) {
            system.power.splice(i, 1);

            return;
        }
    }
};

var HypergravitonBlaster = function HypergravitonBlaster(json, ship) {
    Gravitic.call(this, json, ship);
};
HypergravitonBlaster.prototype = Object.create(Gravitic .prototype);
HypergravitonBlaster.prototype.constructor = HypergravitonBlaster;

HypergravitonBlaster.prototype.initBoostableInfo = function () {
    // Needed because it can change during initial phase
    // because of adding extra power.

    if (window.weaponManager.isLoaded(this)) {

    } else {
        var count = shipManager.power.getBoost(this);

        for (var i = 0; i < count; i++) {
            shipManager.power.unsetBoost(null, this);
        }
    }
    return this;
};

HypergravitonBlaster.prototype.clearBoost = function () {
    for (var i in system.power) {
        var power = system.power[i];
        if (power.turn != gamedata.turn) continue;

        if (power.type == 2) {
            system.power.splice(i, 1);

            return;
        }
    }
};

var MedAntigravityBeam = function MedAntigravityBeam(json, ship) {
    Gravitic.call(this, json, ship);
};
MedAntigravityBeam.prototype = Object.create(Gravitic.prototype);
MedAntigravityBeam.prototype.constructor = MedAntigravityBeam;

MedAntigravityBeam.prototype.initializationUpdate = function() {
	if(this.firingMode == 2){
		this.data["Shots Remaining"] = this.guns - this.fireOrders.length;
	} else {
		delete this.data["Shots Remaining"];
	}
	return this;
};

MedAntigravityBeam.prototype.doMultipleFireOrders = function (shooter, target, system) {

    var shotsOnTarget = 1; //we're only ever allocating one shot at a time for this weapon.
    /*
    if (this.fireOrders.length > 0) {
        if (this.fireOrders.length >= this.guns) {
            // All guns already fired → retarget one gun by removing oldest fireorder.
            this.fireOrders.splice(0, 1);
        }
    } 
    */
	if(this.firingMode == 2 && this.fireOrders.length > 1) return; 

    var fireOrdersArray = []; // Store multiple fire orders

    for (var s = 0; s < shotsOnTarget; s++) {
        var fireid = shooter.id + "_" + this.id + "_" + (this.fireOrders.length + 1);
        var calledid = -1; //Raking, cannot called shot.       

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

MedAntigravityBeam.prototype.checkFinished = function () {
	if(this.firingMode == 2 && this.fireOrders.length > 1) return true;    
    return false;
};

var AntigravityBeam = function AntigravityBeam(json, ship) {
    Gravitic.call(this, json, ship);
};
AntigravityBeam.prototype = Object.create(Gravitic.prototype);
AntigravityBeam.prototype.constructor = AntigravityBeam;

AntigravityBeam.prototype.initializationUpdate = function() {
	if(this.firingMode == 2){
		this.data["Shots Remaining"] = this.guns - this.fireOrders.length;
	} else {
		delete this.data["Shots Remaining"];
	}
	return this;
};

AntigravityBeam.prototype.doMultipleFireOrders = function (shooter, target, system) {

    var shotsOnTarget = 1; //we're only ever allocating one shot at a time for this weapon.
    /*
    if (this.fireOrders.length > 0) {
        if (this.fireOrders.length >= this.guns) {
            // All guns already fired → retarget one gun by removing oldest fireorder.
            this.fireOrders.splice(0, 1);
        }
    } 
    */
	if(this.firingMode == 2 && this.fireOrders.length > 2) return; 

    var fireOrdersArray = []; // Store multiple fire orders

    for (var s = 0; s < shotsOnTarget; s++) {
        var fireid = shooter.id + "_" + this.id + "_" + (this.fireOrders.length + 1);
        var calledid = -1; //Raking, cannot called shot.       

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

AntigravityBeam.prototype.checkFinished = function () {
	if(this.firingMode == 2 && this.fireOrders.length > 1) return true;    
    return false;
};