"use strict";

var TractorBeam = function TractorBeam(json, ship) {
    Weapon.call(this, json, ship);
};
TractorBeam.prototype = Object.create(Weapon.prototype);
TractorBeam.prototype.constructor = TractorBeam;

var CommDisruptor = function CommDisruptor(json, ship) {
    Weapon.call(this, json, ship);
};
CommDisruptor.prototype = Object.create(Weapon.prototype);
CommDisruptor.prototype.constructor = CommDisruptor;

var CommJammer = function CommJammer(json, ship) {
    Weapon.call(this, json, ship);
};
CommJammer.prototype = Object.create(Weapon.prototype);
CommJammer.prototype.constructor = CommJammer;

var ImpCommJammer = function ImpCommJammer(json, ship) {
    Weapon.call(this, json, ship);
};
ImpCommJammer.prototype = Object.create(Weapon.prototype);
ImpCommJammer.prototype.constructor = ImpCommJammer;

var SensorSpear = function SensorSpear(json, ship) {
    Weapon.call(this, json, ship);
};
SensorSpear.prototype = Object.create(Weapon.prototype);
SensorSpear.prototype.constructor = SensorSpear;

var SensorSpike = function SensorSpike(json, ship) {
    Weapon.call(this, json, ship);
};
SensorSpike.prototype = Object.create(Weapon.prototype);
SensorSpike.prototype.constructor = SensorSpike;


var EmBolter = function(json, ship)
{
    Weapon.call( this, json, ship);
}
EmBolter.prototype = Object.create( Weapon.prototype );
EmBolter.prototype.constructor = EmBolter;

var SparkField = function(json, ship)
{
    Weapon.call( this, json, ship);
}
SparkField.prototype = Object.create( Weapon.prototype );
SparkField.prototype.constructor = SparkField;
SparkField.prototype.initBoostableInfo = function(){
    // Needed because it can change during initial phase
    // because of adding extra power.
    if(window.weaponManager.isLoaded(this)){
        this.range = this.baseOutput + 2*shipManager.power.getBoost(this);
        this.data["Range"] = this.range;
        this.minDamage = 2 - shipManager.power.getBoost(this);
        this.minDamage = Math.max(0,this.minDamage);
        this.maxDamage =  7 - shipManager.power.getBoost(this);
        this.data["Damage"] = "" + this.minDamage + "-" + this.maxDamage;
    }
    else{
        var count = shipManager.power.getBoost(this);
        for(var i = 0; i < count; i++){
            shipManager.power.unsetBoost(null, this);
        }
    }
    return this;
}
SparkField.prototype.clearBoost = function(){
        for (var i in system.power){
                var power = system.power[i];
                if (power.turn != gamedata.turn) continue;
                if (power.type == 2){
                    system.power.splice(i, 1);
                    return;
                }
        }
}
SparkField.prototype.hasMaxBoost = function(){
    return true;
}
SparkField.prototype.getMaxBoost = function(){
    return this.maxBoostLevel;
}
//needed for Spark Curtain upgrade
SparkField.prototype.getDefensiveHitChangeMod = function (target, shooter, weapon) {
    if (!weapon.ballistic) return 0;//only ballistic weapons are affected
	var out = shipManager.systems.getOutput(target, this);
	if (shipManager.power.getBoost(this) >= out){ //if boost is equal to output - this means base output is 0 = no Spark Curtain mod!
		out = 0;
	}
	return out;
};



var SurgeCannon = function(json, ship)
{
    Weapon.call( this, json, ship);
}
SurgeCannon.prototype = Object.create( Weapon.prototype );
SurgeCannon.prototype.constructor = SurgeCannon;

var SurgeLaser = function(json, ship)
{
    Weapon.call( this, json, ship);
}
SurgeLaser.prototype = Object.create( Weapon.prototype );
SurgeLaser.prototype.constructor = SurgeLaser;

var LtSurgeBlaster = function(json, ship)
{
    Weapon.call( this, json, ship);
}
LtSurgeBlaster.prototype = Object.create( Weapon.prototype );
LtSurgeBlaster.prototype.constructor = LtSurgeBlaster;


var EmPulsar = function(json, ship)
{
    Weapon.call( this, json, ship);
}
EmPulsar.prototype = Object.create( Weapon.prototype );
EmPulsar.prototype.constructor = EmPulsar;


var ResonanceGenerator = function(json, ship)
{
    Weapon.call( this, json, ship);
}
ResonanceGenerator.prototype = Object.create( Weapon.prototype );
ResonanceGenerator.prototype.constructor = ResonanceGenerator;


var SurgeBlaster = function(json, ship)
{
    Weapon.call( this, json, ship);
}
SurgeBlaster.prototype = Object.create( Weapon.prototype );
SurgeBlaster.prototype.constructor = SurgeBlaster;


var RammingAttack = function(json, ship)
{
    Weapon.call( this, json, ship);
}
RammingAttack.prototype = Object.create( Weapon.prototype );
RammingAttack.prototype.constructor = RammingAttack;

RammingAttack.prototype.initializationUpdate = function() {
    this.data["Range"] = 0; //Not 0.1.
    return this;
}    

var LtEMWaveDisruptor = function(json, ship)
{
    Weapon.call( this, json, ship);
}
LtEMWaveDisruptor.prototype = Object.create( Weapon.prototype );
LtEMWaveDisruptor.prototype.constructor = LtEMWaveDisruptor;

var RadCannon = function(json, ship)
{
    Weapon.call( this, json, ship);
}
RadCannon.prototype = Object.create( Weapon.prototype );
RadCannon.prototype.constructor = RadCannon;

var IonFieldGenerator = function(json, ship)
{
    Weapon.call( this, json, ship);
}
IonFieldGenerator.prototype = Object.create( Weapon.prototype );
IonFieldGenerator.prototype.constructor = IonFieldGenerator;


var ParticleConcentrator = function(json, ship)
{
    Weapon.call( this, json, ship);
}
ParticleConcentrator.prototype = Object.create( Weapon.prototype );
ParticleConcentrator.prototype.constructor = ParticleConcentrator;

ParticleConcentrator.prototype.initializationUpdate = function() {
    delete this.data["Combine projection"];
    if (gamedata.gamephase !== 3) return this;

    var myOrder = null;
    for (var i in this.fireOrders) {
        var fo = this.fireOrders[i];
        if (fo.turn !== gamedata.turn) continue;
        if (fo.type !== 'normal') continue;
        myOrder = fo;
        break;
    }
    if (!myOrder || myOrder.firingMode <= 1) return this;

    var firingShip = this.ship;
    var target = gamedata.getShip(myOrder.targetid);
    if (!target) return this;

    var firingShipPos = shipManager.getShipPosition(firingShip);
    var targetPos = shipManager.getShipPosition(target);
    var primarySides = weaponManager.getShipHittingSide(firingShip, target);

    var sameSides = function(a, b) {
        if (!a || !b || a.length !== b.length) return false;
        for (var k = 0; k < a.length; k++) {
            if (a[k] !== b[k]) return false;
        }
        return true;
    };

    // Firing ship included: multiple Concentrators on the same ship can combine.
    // Primary weapon is skipped by object identity below.
    var candidates = [];
    for (var s in gamedata.ships) {
        var otherShip = gamedata.ships[s];
        if (!otherShip) continue;
        if (otherShip.unavailable) continue;
        if (shipManager.isDestroyed && shipManager.isDestroyed(otherShip)) continue;

        var otherPos = shipManager.getShipPosition(otherShip);
        if (firingShipPos.distanceTo(otherPos) > 1) continue;

        var otherSides = weaponManager.getShipHittingSide(otherShip, target);
        if (!sameSides(primarySides, otherSides)) continue;

        for (var sys in otherShip.systems) {
            var w = otherShip.systems[sys];
            if (!w || w.name !== "ParticleConcentrator") continue;
            // System ids are per-ship integers, not global — use object identity to skip the primary.
            if (w === this) continue;
            var matched = false;
            for (var f in w.fireOrders) {
                var fo2 = w.fireOrders[f];
                if (fo2.turn !== gamedata.turn) continue;
                if (fo2.type !== 'normal') continue;
                if (fo2.id === myOrder.id) continue;
                if (fo2.targetid != myOrder.targetid) continue;
                if (fo2.firingMode != myOrder.firingMode) continue;
                matched = true;
                break;
            }
            if (!matched) continue;
            candidates.push({
                ship: otherShip,
                weapon: w,
                pos: otherPos,
                distToTarget: otherPos.distanceTo(targetPos),
            });
        }
    }

    candidates.sort(function(a, b) {
        return a.distToTarget - b.distToTarget;
    });

    // Greedy clique fill: every accepted ship must be within 1 hex of every other.
    var accepted = [{ ship: firingShip, pos: firingShipPos }];
    var partners = [];
    var needed = myOrder.firingMode;
    for (var c = 0; c < candidates.length && partners.length < needed; c++) {
        var cand = candidates[c];
        var fits = true;
        for (var a = 0; a < accepted.length; a++) {
            if (cand.pos.distanceTo(accepted[a].pos) > 1) { fits = false; break; }
        }
        if (!fits) continue;
        accepted.push({ ship: cand.ship, pos: cand.pos });
        partners.push(cand);
    }

    var line;
    if (partners.length === 0) {
        line = "No eligible partner weapons visible - will fire as single shot";
    } else {
        // Group all participating Concentrators by ship; seed firing ship with its own primary PC.
        var counts = {};
        var order = [];
        var currentWeapons = partners.length+1;
        counts[firingShip.name] = 1;
        order.push(firingShip.name);
        partners.forEach(function(p) {
            if (counts[p.ship.name] === undefined) order.push(p.ship.name);
            counts[p.ship.name] = (counts[p.ship.name] || 0) + 1;
        });
        var names = order.map(function(n) {
            return counts[n] > 1 ? (n + " (" + counts[n] + "),") : n + " (1)";
        }).join(',<br>');
        var actualMode = partners.length + 1;
        if (actualMode < myOrder.firingMode) {
            //line = currentWeapons + " of " + needed + " concentrators available [" + names + "] - will downgrade to " + actualMode + "combined";
            line = currentWeapons + " of " + needed + " available:<br>" + names + "<br>- will downgrade to " + actualMode + "combined";
        } else {
            line = "Will combine with: <br>" + names;
        }
    }
    this.data["Combination"] = line;
    return this;
};

var VorlonDischargeGun = function VorlonDischargeGun(json, ship) {
    Weapon.call(this, json, ship);
};
VorlonDischargeGun.prototype = Object.create(Weapon.prototype);
VorlonDischargeGun.prototype.constructor = VorlonDischargeGun;

VorlonDischargeGun.prototype.initializationUpdate = function() {
    // Needed because it can change power consumption during firing phase, depending on power and number of shots being changed
    var ship = this.ship;
    if(!this.reactivated && gamedata.gamephase === 1 && shipManager.power.isOffline(ship, this)){
		shipManager.power.setOnline(ship, this);
        this.reactivated = true;        
    }

	this.powerReq = 0;
    if(gamedata.gamephase == 3){     
        var isFiring = weaponManager.hasFiringOrder(this.ship, this);
        this.data["Defensive Shots"] = 0;
        if (isFiring) {
            for (var i in this.fireOrders) {
                var fireOrder = this.fireOrders[i];
                if(fireOrder.type == "selfIntercept") this.data["Defensive Shots"]++; 

                //var firing = weaponManager.getFiringOrder(this.ship, this);
                this.powerReq += 2*fireOrder.firingMode;        
            } 
         
        }
        this.data["Shots Remaining"] = 4 - this.fireOrders.length;
    }

    this.outputDisplay = "1/1";
    return this;
};

VorlonDischargeGun.prototype.doMultipleFireOrders = function (shooter, target, system) {

    var shotsOnTarget = 1; //we're only ever allocating one shot at a time for this weapon.

    if (this.fireOrders.length > 3) {
        return;
    } 

    var fireOrdersArray = []; // Store multiple fire orders

    for (var s = 0; s < shotsOnTarget; s++) {
        var fireid = shooter.id + "_" + this.id + "_" + (this.fireOrders.length + 1);
        var calledid = -1; //Raking weapons not eligible for Called Shots

        var chance = window.weaponManager.calculateHitChange(shooter, target, this, calledid).hitChance;
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

VorlonDischargeGun.prototype.checkSelfInterceptSystem = function() {
	if(this.fireOrders.length > 3) return false;
    return true;
};

/* A defensive shot is always declared in mode 1, whatever mode the weapon is set to.
   VorlonDischargeCannon.initializationUpdate bills `powerReq += 5 * fireOrder.firingMode` for EVERY
   order it finds, which is exactly why doMultipleSelfIntercept below stamps firingMode 1 - a
   defensive shot costs 5, not 5 x mode. Without this a manual intercept declared in mode 3 would be
   billed 15 power and could block an otherwise legal commit.
   Safe because the rating is a flat ->intercept with no interceptArray.
   MANUAL_INTERCEPTION_PLAN.md Stage 7. */
VorlonDischargeGun.prototype.getInterceptOrderMode = function () { return 1; };

VorlonDischargeGun.prototype.doMultipleSelfIntercept = function(ship) {

    for (var s = 0; s < 1; s++) {    
        var fireid = ship.id + "_" + this.id + "_" + (this.fireOrders.length + 1);
        var fire = {
        id: fireid,
        type: "selfIntercept",
        shooterid: ship.id,
        targetid: ship.id,
        weaponid: this.id,
        calledid: -1,
        turn: gamedata.turn,
        firingMode: 1, //So that powerReqd display accurately always.
        shots: 1,
        x: "null",
        y: "null",
        addToDB: true,
        damageclass: this.data["Weapon type"].toLowerCase()
        };

        this.fireOrders.push(fire);
    } 
    webglScene.customEvent('SystemDataChanged', { ship: ship, system: this });   
};

VorlonDischargeGun.prototype.checkFinished = function () {
	if(this.fireOrders.length > 3) return true;
    return false;
};


var VorlonDischargeCannon = function VorlonDischargeCannon(json, ship) {
    VorlonDischargeGun.call(this, json, ship);
};
VorlonDischargeCannon.prototype = Object.create(VorlonDischargeGun.prototype);
VorlonDischargeCannon.prototype.constructor = VorlonDischargeCannon;

VorlonDischargeCannon.prototype.initializationUpdate = function() {

    var ship = this.ship;
    // Turns systems back on after Capacitor was double charged the previous turn    
	if(!this.reactivated && gamedata.gamephase === 1 && shipManager.power.isOffline(ship, this)){
		shipManager.power.setOnline(ship, this);
		this.reactivated = true;        
	}

    // Needed because it can change power consumption during firing phase, depending on power and number of shots being changed
	this.powerReq = 0;
    if(gamedata.gamephase == 3){     
        var isFiring = weaponManager.hasFiringOrder(this.ship, this);
        this.data["Defensive Shots"] = 0;
        if (isFiring) {
            for (var i in this.fireOrders) {
                var fireOrder = this.fireOrders[i];
                if(fireOrder.type == "selfIntercept") this.data["Defensive Shots"]++; 

                //var firing = weaponManager.getFiringOrder(this.ship, this);
                this.powerReq += 5*fireOrder.firingMode;        
            } 
         
        }
        this.data["Shots Remaining"] = 4 - this.fireOrders.length;
    }
    this.outputDisplay = "1/1";

    return this;
};

VorlonDischargeCannon.prototype.checkFinished = function () {
	if(this.fireOrders.length > 3) return true;
    return false;
};

var VorlonLightningCannon = function VorlonLightningCannon(json, ship) {
    Weapon.call(this, json, ship);
};
VorlonLightningCannon.prototype = Object.create(Weapon.prototype);
VorlonLightningCannon.prototype.constructor = VorlonLightningCannon;

VorlonLightningCannon.prototype.initializationUpdate = function() {
    var ship = this.ship;
    // Turns systems back on after Capacitor was double charged the previous turn    
	if(!this.reactivated && gamedata.gamephase === 1 && shipManager.power.isOffline(ship, this)){
		shipManager.power.setOnline(ship, this);
		this.reactivated = true;        
	}

    // Needed because it can change power consumption during firing phase, depending on power and number of shots being changed
	this.powerReq = 0;
	var isFiring = weaponManager.hasFiringOrder(this.ship, this);
    if (isFiring) {
		var firing = weaponManager.getFiringOrder(this.ship, this);
		this.powerReq = this.powerRequiredArray[firing.firingMode][1]; //element is array Number of prongs/Power)		
	}
    this.outputDisplay = "1/1";    
    return this;
};


var VorlonLtDischargeGun = function VorlonLtDischargeGun(json, ship) {
    Weapon.call(this, json, ship);
};
VorlonLtDischargeGun.prototype = Object.create(Weapon.prototype);
VorlonLtDischargeGun.prototype.constructor = VorlonLtDischargeGun;


var VorlonLightningGun = function VorlonLightningGun(json, ship) {
    Weapon.call(this, json, ship);
};
VorlonLightningGun.prototype = Object.create(Weapon.prototype);
VorlonLightningGun.prototype.constructor = VorlonLightningGun;
VorlonLightningGun.prototype.initializationUpdate = function() {
    var ship = this.ship;
    // Turns systems back on after Capacitor was double charged the previous turn    
	if(!this.reactivated && gamedata.gamephase === 1 && shipManager.power.isOffline(ship, this)){
		shipManager.power.setOnline(ship, this);
		this.reactivated = true;        
	}

    // Needed because it can change power consumption during firing phase, depending on power and number of shots being changed
	this.powerReq = 0;
	var isFiring = weaponManager.hasFiringOrder(this.ship, this);
    if (isFiring) {
		var firing = weaponManager.getFiringOrder(this.ship, this);
		this.powerReq = this.powerRequiredArray[firing.firingMode][1]; //element is array Number of prongs/Power)		
	}
    this.outputDisplay = "1/1";    
    return this;
};


var VorlonLightningGun2 = function VorlonLightningGun2(json, ship) {
    Weapon.call(this, json, ship);
};
VorlonLightningGun2.prototype = Object.create(Weapon.prototype);
VorlonLightningGun2.prototype.constructor = VorlonLightningGun2;
VorlonLightningGun2.prototype.initializationUpdate = function() {
    var ship = this.ship;
    // Turns systems back on after Capacitor was double charged the previous turn    
	if(!this.reactivated && gamedata.gamephase === 1 && shipManager.power.isOffline(ship, this)){
		shipManager.power.setOnline(ship, this);
		this.reactivated = true;        
	}

    // Needed because it can change power consumption during firing phase, depending on power and number of shots being changed
	this.powerReq = 0;
	var isFiring = weaponManager.hasFiringOrder(this.ship, this);
    if (isFiring) {
		var firing = weaponManager.getFiringOrder(this.ship, this);
		this.powerReq = this.powerRequiredArray[firing.firingMode][1]; //element is array Number of prongs/Power)		
	}
    this.outputDisplay = "1/1";    
    return this;
};

var VorlonDischargePulsar = function VorlonDischargePulsar(json, ship) {
    Weapon.call(this, json, ship);
};
VorlonDischargePulsar.prototype = Object.create(Weapon.prototype);
VorlonDischargePulsar.prototype.constructor = VorlonDischargePulsar;

VorlonDischargePulsar.prototype.initializationUpdate = function() {
    var ship = this.ship;
    // Turns systems back on after Capacitor was double charged the previous turn    
	if(!this.reactivated && gamedata.gamephase === 1 && shipManager.power.isOffline(ship, this)){
		shipManager.power.setOnline(ship, this);
		this.reactivated = true;        
	}

    // Needed because it can change power consumption during firing phase, depending on power and number of shots being changed
	this.powerReq = 0;
	var isFiring = weaponManager.hasFiringOrder(this.ship, this);
    if (isFiring) {
		var firing = weaponManager.getFiringOrder(this.ship, this);
		this.powerReq = 4*firing.firingMode;		
	}
    this.outputDisplay = "1/1";    
    return this;
};

var PsionicConcentrator = function PsionicConcentrator(json, ship) {
    Weapon.call(this, json, ship);
};
PsionicConcentrator.prototype = Object.create(Weapon.prototype);
PsionicConcentrator.prototype.constructor = PsionicConcentrator;

PsionicConcentrator.prototype.initializationUpdate = function() {
	if(this.firingMode == 4 || this.firingMode == 5){
		this.data["Shots Remaining"] = this.guns - this.fireOrders.length;
	} else {
		delete this.data["Shots Remaining"];
	}
	return this;
};

PsionicConcentrator.prototype.doMultipleFireOrders = function (shooter, target, system) {

    var shotsOnTarget = 1; //we're only ever allocating one shot at a time for this weapon in Split mode.
    /*
    if (this.fireOrders.length > 0) {
        if (this.fireOrders.length >= this.guns) {
            // All guns already fired → retarget one gun by removing oldest fireorder.
            this.fireOrders.splice(0, 1);
        }
    } 
    */
	if(this.firingMode == 1 && this.fireOrders.length > 3) return;
	if(this.firingMode == 2 && this.fireOrders.length > 1) return; 
    
    var fireOrdersArray = []; // Store multiple fire orders

    for (var s = 0; s < shotsOnTarget; s++) {
        var fireid = shooter.id + "_" + this.id + "_" + (this.fireOrders.length + 1);
        var calledid = -1; 

        if (system) {
            // When the system is a subsystem, make all damage go through the parent.
            while (system.parentId > 0) {
                system = shipManager.systems.getSystem(ship, system.parentId);
            }

            calledid = system.id;
        }        

        var chance = window.weaponManager.calculateHitChange(shooter, target, this, calledid).hitChance;
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

PsionicConcentrator.prototype.checkFinished = function () {
	if(this.firingMode == 1 && this.fireOrders.length > 3) return true;
	if(this.firingMode == 2 && this.fireOrders.length > 1) return true;    
    return false;
};

var PsionicConcentratorLight = function PsionicConcentratorLight(json, ship) {
    Weapon.call(this, json, ship);
};
PsionicConcentratorLight.prototype = Object.create(Weapon.prototype);
PsionicConcentratorLight.prototype.constructor = PsionicConcentratorLight;

var HeavyPsionicLance = function HeavyPsionicLance(json, ship) {
    Weapon.call(this, json, ship);
};
HeavyPsionicLance.prototype = Object.create(Weapon.prototype);
HeavyPsionicLance.prototype.constructor = HeavyPsionicLance;

HeavyPsionicLance.prototype.clearBoost = function () {
    for (var i in system.power) {
        var power = system.power[i];
        if (power.turn != gamedata.turn) continue;

        if (power.type == 2) {
            system.power.splice(i, 1);

            return;
        }
    }
};

HeavyPsionicLance.prototype.hasMaxBoost = function () {
    return true;
};

HeavyPsionicLance.prototype.getMaxBoost = function () {
    return this.maxBoostLevel;
};

HeavyPsionicLance.prototype.initBoostableInfo = function () {
   if (window.weaponManager.isLoaded(this)) {} else {
        var count = shipManager.power.getBoost(this);
        for (var i = 0; i < count; i++) {
            shipManager.power.unsetBoost(null, this);
        }
    }	

    this.data.Boostlevel = shipManager.power.getBoost(this);	
	//Manually set system data window
    switch (shipManager.power.getBoost(this)) {
        case 0:
            this.data["Damage"] = '66-120';
            this.data["Boostlevel"] = '0';
            break;
        case 1:
            this.data["Damage"] = '76-148';
            this.data["Boostlevel"] = '1';
            break;
        case 2:
            this.data["Damage"] = '86-176';
            this.data["Boostlevel"] = '2';
            break;
        case 3:
            this.data["Damage"] = '96-204';
            this.data["Boostlevel"] = '3';
            break;
        default:
            this.data["Damage"] = '66-120';
            this.data["Boostlevel"] = '0';
            break;
	}
	
    return this;
};
var PsionicLance = function PsionicLance(json, ship) {
    Weapon.call(this, json, ship);
};
PsionicLance.prototype = Object.create(Weapon.prototype);
PsionicLance.prototype.constructor = PsionicLance;

PsionicLance.prototype.clearBoost = function () {
    for (var i in system.power) {
        var power = system.power[i];
        if (power.turn != gamedata.turn) continue;

        if (power.type == 2) {
            system.power.splice(i, 1);

            return;
        }
    }
}; 

PsionicLance.prototype.hasMaxBoost = function () {
    return true;
};

PsionicLance.prototype.getMaxBoost = function () {
    return this.maxBoostLevel;
};

PsionicLance.prototype.initBoostableInfo = function () {
    if (window.weaponManager.isLoaded(this)) {} else {
        var count = shipManager.power.getBoost(this);
        for (var i = 0; i < count; i++) {
            shipManager.power.unsetBoost(null, this);
        }
    }	

    this.data.Boostlevel = shipManager.power.getBoost(this);
    	
    switch (shipManager.power.getBoost(this)) {
        case 0:
            this.data["Damage"] = '38-65';
            this.data["Boostlevel"] = '0';
            break;
        case 1:
            this.data["Damage"] = '40-85';
            this.data["Boostlevel"] = '1';
            break;
        case 2:
            this.data["Damage"] = '42-105';
            this.data["Boostlevel"] = '2';
            break;
        default:
            this.data["Damage"] = '38-65';
            this.data["Boostlevel"] = '0';
            break;
    }

    return this;
};

var PsychicField = function PsychicField(json, ship)
{
    Weapon.call( this, json, ship);
}
PsychicField.prototype = Object.create( Weapon.prototype );
PsychicField.prototype.constructor = PsychicField;

PsychicField.prototype.initBoostableInfo = function() {
    // Needed because it can change during initial phase
    // because of adding extra power.
    if (window.weaponManager.isLoaded(this)) {
        var boost = shipManager.power.getBoost(this);    	
    	if(gamedata.gamephase == 1){
	        // Use a baseRange property to store the original range if not already defined
	        if (this.baseRange === undefined) {
	            this.baseRange = this.range; // Save the initial range value
	        }
	        
        // Calculate the boosted range dynamically without modifying baseRange
        this.range = this.baseRange + boost;	        
		}

        this.data["Range"] = this.range;
        // Calculate damage based on boost level
        this.minDamage = 1 + boost; // Psychic Field does flat damage
        this.minDamage = Math.max(1, this.minDamage); // Ensure minimum damage is at least 1
        this.maxDamage = 1 + boost;
        this.data["Damage"] = "" + this.minDamage;
    } else {
        // Reset any applied boosts if not loaded
        var count = shipManager.power.getBoost(this);
        for (var i = 0; i < count; i++) {
            shipManager.power.unsetBoost(null, this);
        }
    }

    return this;
}
PsychicField.prototype.clearBoost = function(){
        for (var i in system.power){
                var power = system.power[i];
                if (power.turn != gamedata.turn) continue;
                if (power.type == 2){
                    system.power.splice(i, 1);
                    return;
                }
        }
}
PsychicField.prototype.hasMaxBoost = function(){
    return true;
}
PsychicField.prototype.getMaxBoost = function(){
    return this.maxBoostLevel;
}

var ProximityLaserLauncher = function ProximityLaserLauncher(json, ship) {
    Weapon.call(this, json, ship);
};
ProximityLaserLauncher.prototype = Object.create(Weapon.prototype);
ProximityLaserLauncher.prototype.constructor = ProximityLaserLauncher;

var ProximityLaser = function ProximityLaser(json, ship) {
    Weapon.call(this, json, ship);
};
ProximityLaser.prototype = Object.create(Weapon.prototype);
ProximityLaser.prototype.constructor = ProximityLaser;

ProximityLaser.prototype.getFiringHex = function(shooter, weapon){ //Need to calculate hit chance from where Launcher targets.	
	var sPosLaunch; 
    var launcher = this.launcher;
    var ship = this.ship;
    //var launcherOrder = weaponManager.getFiringOrder(ship, launcher)
    var launcherOrder = launcher.fireOrders[0] || weaponManager.getFiringOrder(ship, launcher);

	   	if (launcherOrder)	{	// check that launcher has firing orders.  
			sPosLaunch = new hexagon.Offset(launcherOrder.x, launcherOrder.y); 
		} else{
		    sPosLaunch = shipManager.movement.getPositionAtStartOfTurn(shooter, gamedata.turn); 	
		}	
	return sPosLaunch;
	
	};

var ProximityLaserNew = function ProximityLaserNew(json, ship) {
    Weapon.call(this, json, ship);
};
ProximityLaserNew.prototype = Object.create(Weapon.prototype);
ProximityLaserNew.prototype.constructor = ProximityLaserNew;

ProximityLaserNew.prototype.initializationUpdate = function() {
    if (this.fireOrders.length > 0) {
        this.hextarget = false;
        this.startArc = 0; //Hex target has arc, laser shot does not.
        this.endArc = 360;
        this.ignoresLoS = true;
        this.range = 0;
    }else{
        this.hextarget = true;
        this.startArc = this.startArcArray[0]; //Use Arc arrays to reset to default
        this.endArc = this.endArcArray[0]; 
        this.ignoresLoS = false;
        this.range = 30;                       
    } 

    return this;
};

ProximityLaserNew.prototype.getFiringHex = function(shooter, weapon){ //Need to calculate hit chance from where Launcher targets.	
    var sPosLaunch;       
        if (this.fireOrders.length > 0) {	//A hex has been targeted, firing hex changes to those coordinates
            var sPosLaunch; 
            var launcherOrder = this.fireOrders[0];       
                if (launcherOrder)	{	// check that launcher has firing orders.  
                    sPosLaunch = new hexagon.Offset(launcherOrder.x, launcherOrder.y); 
                } else{
                    sPosLaunch = shipManager.movement.getPositionAtStartOfTurn(shooter, gamedata.turn);            	
                }
        }else{ //Lasers not locked in yet, use firing ship position.
            sPosLaunch = shipManager.movement.getPositionAtStartOfTurn(shooter, gamedata.turn); 
        }

	return sPosLaunch;
	
};


ProximityLaserNew.prototype.doMultipleHexFireOrders = function (shooter, hexpos) {
    
    var shotsOnTarget = 1; //we're only ever allocating one shot at a time for this weapon in Split mode.

    if (this.fireOrders.length > 0) {
        return;
    } 

    var fireOrdersArray = []; // Store multiple fire orders

    for (var s = 0; s < shotsOnTarget; s++) {
            var fireid = shooter.id + "_" + this.id + "_" + (this.fireOrders.length + 1);
            var fire = {
                id: fireid,
                type: 'ballistic',
                shooterid: shooter.id,
                targetid: -1,
                weaponid: this.id,
                calledid: -1,
                turn: gamedata.turn,
                firingMode: this.firingMode,
                shots: this.defaultShots,
                x: hexpos.q,
                y: hexpos.r,
                damageclass: 'Targeter', 
                notes: "split"                
            };
        fireOrdersArray.push(fire); // Store fire order
    }

    this.hextarget = false;

    return fireOrdersArray; // Return all fire orders
};  

ProximityLaserNew.prototype.doMultipleFireOrders = function (shooter, target, system) {
    
    var shotsOnTarget = 1; //we're only ever allocating one shot at a time for this weapon in Split mode.

    if (this.fireOrders.length > 1) {
        return;
    } 

    var fireOrdersArray = []; // Store multiple fire orders

    for (var s = 0; s < shotsOnTarget; s++) {
        var fireid = shooter.id + "_" + this.id + "_" + (this.fireOrders.length + 1);
        var calledid = -1; //No called shots.     

        var chance = window.weaponManager.calculateHitChange(shooter, target, this, calledid).hitChance;
        //if(chance < 1) continue;

        var fire = {
            id: fireid,
            type: 'ballistic',
            shooterid: shooter.id,
            targetid: target.id,
            weaponid: this.id,
            calledid: calledid,
            turn: gamedata.turn,
            firingMode: this.firingMode,
            shots: 1,
            x: "null",
            y: "null",
            damageclass: 'Laser', 
            chance: chance,
            hitmod: 0,
            notes: "Split"
        };
        
        fireOrdersArray.push(fire); // Store each fire order
    }
        //shipWindowManager.setDataForSystem(ship, weapon);
    


    return fireOrdersArray; // Return all fire orders
};    

ProximityLaserNew.prototype.checkFinished = function () {
	if(this.fireOrders.length > 1) return true;
    return false;
};

var GromeTargetingArray = function GromeTargetingArray(json, ship) {
    Weapon.call(this, json, ship);
};
GromeTargetingArray.prototype = Object.create(Weapon.prototype);
GromeTargetingArray.prototype.constructor = GromeTargetingArray;

GromeTargetingArray.prototype.initializationUpdate = function() {
var ship = this.ship;	
this.outputDisplay = shipManager.systems.getOutput(ship, this);
return this;
};

var PulsarMine = function PulsarMine(json, ship) {
    Weapon.call(this, json, ship);
};
PulsarMine.prototype = Object.create(Weapon.prototype);
PulsarMine.prototype.constructor = PulsarMine;

var AegisSensorPod = function AegisSensorPod(json, ship) {
    Weapon.call(this, json, ship);
};
AegisSensorPod.prototype = Object.create(Weapon.prototype);
AegisSensorPod.prototype.constructor = AegisSensorPod;

AegisSensorPod.prototype.initializationUpdate = function() {
	var ship = this.ship;	
	this.outputDisplay = shipManager.systems.getOutput(ship, this);
	return this;
};

var Marines = function Marines(json, ship) {
    Weapon.call(this, json, ship);
};
Marines.prototype = Object.create(Weapon.prototype);
Marines.prototype.constructor = Marines;


var SecondSight = function SecondSight(json, ship) {
    Weapon.call(this, json, ship);
};
SecondSight.prototype = Object.create(Weapon.prototype);
SecondSight.prototype.constructor = SecondSight;
	
SecondSight.prototype.canActivate = function () { 
	if(gamedata.gamephase == 3 && this.fireOrders.length == 0) return true;
	return false; 
};  

SecondSight.prototype.doActivate = function () { 

	var ship = this.ship;
	var fireid = ship.id + "_" + this.id + "_" + (this.fireOrders.length + 1);
	var position = shipManager.getShipPosition(ship);			

	var fire = {
		id: fireid,
		type: 'normal',
		shooterid: ship.id,
		targetid: -1,
		weaponid: this.id,
		calledid: -1,
		turn: gamedata.turn,
		firingMode: this.firingMode,
		shots: this.defaultShots,
		x: position.q,
		y: position.r,
		damageclass: 'Electromagnetic',
		chance: 100,
		hitmod: 0,
		notes: "SecondSight"
	};

	// Push to arrays / fire orders
	this.fireOrders.push(fire);
};

/* Second Sight reaches every enemy unit on the board - beforeFiringOrderResolution has no position
   or arc test at all - so the area it declares is a plain blanket of its own range, arc ignored.
   That range is nominally 100 hexes, which ShipIcon caps at MAX_ARC_HEXES; the cap is well past any
   engagement, so the blanket covers the map exactly as the effect does.

   Its own animation orange at a low opacity: an area this large has to sit UNDER what the player is
   reading rather than over it. See Weapon.prototype.getDeclaredArea. */
SecondSight.prototype.getDeclaredArea = function () {
	if(gamedata.gamephase != 3) return null;
	if(this.fireOrders.length == 0) return null;

	return { shape: 'radius', hexes: 40, opacity: 0.1, borderOpacity: 0.2 };
};

var PlanetCrackerBeam = function PlanetCrackerBeam(json, ship) {
    Weapon.call(this, json, ship);
};
PlanetCrackerBeam.prototype = Object.create(Weapon.prototype);
PlanetCrackerBeam.prototype.constructor = PlanetCrackerBeam;

PlanetCrackerBeam.prototype.canActivate = function () {
	if(gamedata.gamephase == 3 && this.fireOrders.length == 0) return true;
	return false;
};

/* Deliberately NO canDeactivate: SystemActivation renders whenever a system can activate OR
   deactivate, so declaring one kept the green menu on screen for a weapon that has already fired.
   Second Sight and the Thought Wave behave the same way - once the order exists the menu goes and
   the standard remove-fire-order button is how it gets withdrawn. */

PlanetCrackerBeam.prototype.doActivate = function () {

	var ship = this.ship;
	var fireid = ship.id + "_" + this.id + "_" + (this.fireOrders.length + 1);
	var position = shipManager.getShipPosition(ship);

	var fire = {
		id: fireid,
		type: 'normal',
		shooterid: ship.id,
		targetid: -1,
		weaponid: this.id,
		calledid: -1,
		turn: gamedata.turn,
		firingMode: this.firingMode,
		shots: this.defaultShots,
		x: position.q,
		y: position.r,
		damageclass: 'Electromagnetic',
		chance: 100,
		hitmod: 0,
		notes: "PlanetCracker"
	};

	// Push to arrays / fire orders
	this.fireOrders.push(fire);
};

/* The hexes the beam will sweep, highlighted on the map for as long as the order stands. The server
   picks the same line off the same weapon range (PlanetCrackerBeam::getBeamHexes), so what is
   highlighted is what will actually be destroyed. Everything but the shape is left out on purpose:
   the reach defaults to the weapon's own, which is the one number both ends already agree on, and
   the colour to the shared declared-area yellow.

   Nothing calls this: PhaseStrategy.syncDeclaredAreas polls it, so the overlay follows the order in
   and out of existence by itself. See Weapon.prototype.getDeclaredArea. */
PlanetCrackerBeam.prototype.getDeclaredArea = function () {
	if(gamedata.gamephase != 3) return null;
	if(this.fireOrders.length == 0) return null;

	return { shape: 'forward' };
};

/* The weapon carries specialArcs, so weaponManager.isPosOnWeaponArc hands every arc question here
   rather than testing a wedge. Its arc is not a wedge at all: it is the straight line of hexes off
   the ship's nose, out to $range - the same set the server sweeps in PlanetCrackerBeam::getBeamHexes.
   Same 0.5 degree tolerance as the Transverse Drive, which asks the same shape of question. */
PlanetCrackerBeam.prototype.isPosOnSpecialArc = function (shooter, position) {
	var shooterPos = shipManager.getShipPosition(shooter);

	if (shooterPos.q == position.q && shooterPos.r == position.r) return false; //the ship's own hex is never swept
	if (shooterPos.distanceTo(position) > this.range) return false;

	var bearing = mathlib.getCompassHeadingOfPoint(shooterPos, position);
	var delta = Math.abs(bearing - shipManager.getShipHeadingAngle(shooter)); //despite the name, that is the ship's FACING
	if (delta > 180) delta = 360 - delta; //wrap around 360

	return delta <= 0.5;
};

var ThoughtWave = function ThoughtWave(json, ship) {
    Weapon.call(this, json, ship);
};
ThoughtWave.prototype = Object.create(Weapon.prototype);
ThoughtWave.prototype.constructor = ThoughtWave;
	
ThoughtWave.prototype.canActivate = function () { 
    var ship = this.ship;
	if(gamedata.gamephase == 1 && this.fireOrders.length == 0 && !shipManager.power.isOffline(ship, this)) return true;
	return false; 
};  

ThoughtWave.prototype.doActivate = function () { 

		var ship = this.ship;
		var fireid = ship.id + "_" + this.id + "_" + (this.fireOrders.length + 1);
		var position = shipManager.getShipPosition(ship);			

		var fire = {
			id: fireid,
			type: 'ballistic',
			shooterid: ship.id,
			targetid: -1,
			weaponid: this.id,
			calledid: -1,
			turn: gamedata.turn,
			firingMode: this.firingMode,
			shots: this.defaultShots,
			x: position.q,
			y: position.r,
			damageclass: 'Plasma',
			chance: 100,
			hitmod: 0,
			notes: "Thoughtwave"
		};

		// Push to arrays / fire orders
		this.fireOrders.push(fire);
	};

/* Like Second Sight, the Thought Wave reaches every eligible unit on the board (every non-Mindrider
   one) with no position or arc test, so it declares the same map-wide blanket - its own range, arc
   ignored, capped by ShipIcon at MAX_ARC_HEXES.

   In its own animation magenta at a low opacity, so a Second Sight and a Thought Wave up at once stay
   tellable apart and neither drowns the map. Declared in Initial Orders, so gated to that phase. */
ThoughtWave.prototype.getDeclaredArea = function () {
	if(gamedata.gamephase != 1) return null;
	if(this.fireOrders.length == 0) return null;

	return { shape: 'radius', hexes: 40, color: this.getAnimationColourCss(), opacity: 0.1, borderOpacity: 0.2 };
};


var GrapplingClaw = function GrapplingClaw(json, ship) {
    Weapon.call(this, json, ship);
};
GrapplingClaw.prototype = Object.create(Weapon.prototype);
GrapplingClaw.prototype.constructor = GrapplingClaw;

GrapplingClaw.prototype.initializationUpdate = function() {
	if(this.hostShipId !== -1 && !this.hostShipDisplayed){
        this.hostShipDisplayed = true;
        var hostShip = gamedata.getShip(this.hostShipId);
        this.data["Attached to"] = hostShip.name;
    }

    this.data["Range"] = 0; //Not 0.1.

	return this;
};


// Stage S (S-f): Shadow Fighter Bomb. A thin Weapon subclass — its hex-target
// firing/targeting UI (arc fan, range, hex picker) is driven entirely by the
// json blueprint fields (hextarget:true, range, startArc/endArc) the server
// ships, so no behaviour override is needed here. The actual fighter burst is
// resolved server-side (ShadowFighterBomb::fire → HangarOps::performBombLaunch).
var ShadowFighterBomb = function ShadowFighterBomb(json, ship) {
    Weapon.call(this, json, ship);
};
ShadowFighterBomb.prototype = Object.create(Weapon.prototype);
ShadowFighterBomb.prototype.constructor = ShadowFighterBomb;

// Friendly mode label in the fire menu (no enemy-facing reveal needed — the
// burst is visible once fighters appear).
ShadowFighterBomb.prototype.getModeNameForEnemy = function (fireOrder) {
    return "Fighter Bomb";
};

/* =============================================================================
 * SingularityMine — JS stubs
 * All logic is PHP-side. These stubs allow the JS engine to construct
 * the system objects from JSON without errors.
 * =========================================================================== */

var SingularityMine = function SingularityMine(json, ship) {
    Weapon.call(this, json, ship);
};
SingularityMine.prototype = Object.create(Weapon.prototype);
SingularityMine.prototype.constructor = SingularityMine;

SingularityMine.prototype.initializationUpdate = function () {
    if (this.selected) {
        this.showHexagonArc = 10;
    } else {
        this.showHexagonArc = 0;
    }
    return this;
};

var SingularityRammingAttack = function SingularityRammingAttack(json, ship) {
    RammingAttack.call(this, json, ship);
};
SingularityRammingAttack.prototype = Object.create(RammingAttack.prototype);
SingularityRammingAttack.prototype.constructor = SingularityRammingAttack;

var SingularityCore = function SingularityCore(json, ship) {
    ShipSystem.call(this, json, ship);
};
SingularityCore.prototype = Object.create(ShipSystem.prototype);
SingularityCore.prototype.constructor = SingularityCore;

var spawnSingularity = function spawnSingularity(json) {
    Ship.call(this, json);
};
spawnSingularity.prototype = Object.create(Ship.prototype);
spawnSingularity.prototype.constructor = spawnSingularity;

// GTS_Triad
var SpatialCutter = function SpatialCutter(json, ship) {
    Weapon.call(this, json, ship);
};
SpatialCutter.prototype = Object.create(Weapon.prototype);
SpatialCutter.prototype.constructor = SpatialCutter;

SpatialCutter.prototype.onFireOrderCreated = function (fire) {
    var shooter = gamedata.getShip(fire.shooterid);
    var target  = gamedata.getShip(fire.targetid);
    if (!shooter || !target) return;
    if (ew.getTargetingEW(shooter, target) <= 0) {
        confirm.warning("Spatial Cutter requires a lock-on (OEW) to fire. The shot will not resolve without one.");
        var weapon = this;
        window.setTimeout(function () {
            for (var i = weapon.fireOrders.length - 1; i >= 0; i--) {
                if (weapon.fireOrders[i].id === fire.id) {
                    weapon.fireOrders.splice(i, 1);
                    break;
                }
            }
            webglScene.customEvent('SystemDataChanged', { ship: shooter, system: weapon });
        }, 0);
    }
};
/* ================================================================================================
 * WALKERS OF SIGMA-957 - Lightning Array family        WALKERS_OF_SIGMA_PLAN.md sections 3.1 / 3.2
 * ================================================================================================
 *
 * Client half of LightningArray / MediumLightningArray (specialWeapons.php). ⚠️ Every number here
 * MUST be kept in step with the PHP property of the same name - the two are read independently (the
 * server resolves the shot, the client predicts its hit chance, damage and remaining discharges), so
 * a table that drifts shows the player one number and rolls another, with nothing to warn either of
 * them. There is a test that dumps the PHP side by reflection and compares field for field; run it
 * after ANY re-stat.
 *
 * FIRING MODES, matching the PHP constants:
 *   1 Combined Fire - one click declares one discharge; a further click on the SAME target fuses
 *     another discharge into that shot instead of declaring a new one, so its fire control, range
 *     penalty and damage all move to that row of the tables. No dialog. The count rides to the
 *     server in the order's ->shots, which the server re-clamps against the real pool.
 *   2 Single Shots  - every click is a separate one-discharge order, never fused. The server forces
 *     the count to 1 for a mode-2 order regardless of what ->shots says.
 * ⭐⭐ WIDE BEAM IS A TOGGLE, NOT A THIRD MODE. The SYS_WBLA / SYS_WBMLA refit publishes
 * `wideBeamFitted` on that one mount; a fitted array then shows a "Wide Beam" / "Normal Beam" pair
 * in its <SystemActivation> box during the Fire phase, and arming it applies to EVERY shot it fires
 * this turn in EITHER firing mode - -2 on every damage die (floor 1 per die, rolled server-side one
 * die at a time), doubled flash collateral, and a one turn cooldown.
 *
 * The arm lives in `active`, because that is the field the generic activation box reads, and it
 * travels to the server as this system's `individualNotesTransfer` (doIndividualNotesTransfer
 * below) - the same route ChameleonSensors' disguise toggle takes. It comes BACK on every load as
 * `active` again, so the ship window keeps quoting the right damage span after a refresh.
 *
 * ⚠️ It was briefly modelled as two extra FIRING MODES (2026-09-06). Four entries in the selector
 * for what is really two independent choices was the wrong trade; the toggle costs one boolean.
 * Nothing in this file compares a mode id even so: grouping asks LightningArray.isSingleShotMode().
 *
 * ⚠️ THE COLLATERAL AND COOLDOWN RULES ARE SERVER-SIDE ONLY, deliberately. Nothing the client
 * predicts (hit chance, range penalty, discharges left) is touched by a wide beam, so mirroring
 * them here would be a second source of truth for numbers the player never reads before committing.
 * The cooldown in particular needs NO client code: the server writes the array's turn-advance
 * reload as 0, so weaponManager.isLoaded already greys the mount out and refuses the declaration.
 *
 * ⭐ multiModeSplit - the two modes are a PER-SHOT choice, not a per-turn one. Declare a couple of
 * combined shots, switch to Single Shots, pepper a flight with the rest, switch back. The flag is
 * published from the PHP stripForJson (it is protected on Weapon and the base does not pass it) and
 * it is what keeps the firing-mode selector unlocked after the first declaration. It also hands
 * withdrawal to this weapon: weaponManager.removeFiringOrderMulti / removeFiringOrder do nothing but
 * call removeMultiModeSplit / removeAllMultiModeSplit below.
 *
 * ⚠️ A declared order carries damageclass 'Sweeping' and notes "Split", exactly as the Discharge Gun
 * and the Psionic Concentrator do. Those two values are what put the shot in the INCOMING list
 * (weaponManager.getAllBallisticsAgainst) and drive its ballistic line - not decoration.
 *
 * ⭐ WHO ACTUALLY READS THAT LIST for a weapon like this one: the FIRING player, and only them. A
 * direct-fire weapon declares in the Firing phase, and an opponent's gamedata does not carry those
 * orders until the phase resolves - so the INCOMING list on an enemy ship's tooltip is a tally of
 * the shots YOU have committed to it, not a warning to the defender. Manual interception is a
 * BALLISTIC affair (declared in Initial Orders, resolved a phase later, visible in between). Do not
 * build defender-facing explanation into a Firing-phase weapon's row; it just has to count honestly.
 *
 * ->guns is NOT the blueprint value here. weaponManager's manual-intercept cap counts ORDERS while
 * this weapon spends DISCHARGES, so updateGunAccounting() rewrites it with the same formula the
 * server uses. See getGunsForInterceptAccounting in specialWeapons.php for the derivation.
 * ------------------------------------------------------------------------------------------------ */

var LightningArray = function LightningArray(json, ship) {
	Weapon.call(this, json, ship);
};
LightningArray.prototype = Object.create(Weapon.prototype);
LightningArray.prototype.constructor = LightningArray;

/* Firing modes - MUST match the MODE_* constants in specialWeapons.php. There are two, and the
   wide beam is NOT one of them: it is an orthogonal per-turn toggle (see the block comment). */
LightningArray.MODE_COMBINED = 1;
LightningArray.MODE_SINGLE   = 2;

/* Does this firing mode never fuse? One line today, because there is one such mode. It exists so
   the grouping question is asked by NAME everywhere - the wide beam is a separate choice, and the
   two were briefly modelled as four firing modes (2026-09-06) with exactly the confusion that
   invites. ⚠️ MIRROR PAIR with LightningArray::isSingleShotMode in specialWeapons.php. */
LightningArray.isSingleShotMode = function (mode) {
	return parseInt(mode, 10) === LightningArray.MODE_SINGLE;
};

//Mirrors WIDEBEAM_DIE_PENALTY / WIDEBEAM_DIE_FLOOR in specialWeapons.php. Used for the damage span
//in the ship window only - the server rolls the dice.
LightningArray.prototype.wideBeamDiePenalty = 2;
LightningArray.prototype.wideBeamDieFloor   = 1;

/* ⚠️ The generic <SystemActivation> box treats a WEAPON's "has a fire order" as active, because for
   most weapons the box IS the fire button. This one is a persistent toggle that has nothing to do
   with whether a shot has been declared, so it opts out of that. */
LightningArray.prototype.activationIsToggle = true;

/* ⚠️ THE SIX TABLES BELOW MUST MATCH THE PHP ONES EXACTLY. The server rolls the shot from its copy
   and the client predicts it from this one, so drift shows the player one number and rolls another,
   silently. There is a test for it: dump the PHP tables by reflection and compare field for field.
   Mirrors $combinedDamageArray in specialWeapons.php. */
LightningArray.prototype.combinedDamage = {
	1: { dice: 5,  add: 20 },
	2: { dice: 10, add: 20 },
	3: { dice: 15, add: 20 },
	4: { dice: 20, add: 20 }
};

/* Mirrors $combinedFireControlArray. Rows are [fighters, <=mediums, <=capitals], the same shape as
   fireControl. Note the two columns pull in OPPOSITE directions: fusing makes the shot much harder
   to land on a fighter (8 -> 2) and easier on a capital (4 -> 6). */
LightningArray.prototype.combinedFireControl = {
	1: [8, 6, 4],
	2: [6, 6, 5],
	3: [4, 6, 6],
	4: [2, 6, 6]
};

/* Mirrors $combinedRangePenaltyArray. Per-hex, the same units as weapon.rangePenalty - and fusing
   IMPROVES the reach here (-1/3 hexes down to -1/5), which is the opposite of what the fighter
   column above does. */
LightningArray.prototype.combinedRangePenalty = {
	1: 0.33,
	2: 0.25,
	3: 0.25,
	4: 0.2
};

//Mirrors $dischargePool. Flat here; the Medium variant derives it from charge time.
LightningArray.prototype.baseDischargePool = 4;

/* Discharges this array may spend this turn. Mirrors PHP getDischargePool(). */
LightningArray.prototype.getDischargePool = function () {
	return this.baseDischargePool;
};

/* Discharges committed to one fire order. The count lives in ->shots and nowhere else - the same
   field every other split-shot weapon carries its shot count in - and it survives both the POST
   rebuild and a mid-phase page reload. parseInt, not a typeof check: an order that has round-tripped
   through the database comes back with a string there. */
LightningArray.prototype.getOrderDischarges = function (fireOrder) {
	var n = parseInt(fireOrder && fireOrder.shots, 10);
	return (isNaN(n) || n < 1) ? 1 : n;
};

/* Discharges spent on OFFENSIVE shots. A manual 'intercept' order also costs a discharge and is
   counted by getDischargesUsed below; a 'selfIntercept' marker is consent only and costs nothing.
   Kept separate because the gun-accounting formula needs the offensive half on its own. */
LightningArray.prototype.getOffensiveDischarges = function () {
	var used = 0;
	for (var i = 0; i < this.fireOrders.length; i++) {
		var fire = this.fireOrders[i];
		if (fire.type === 'selfIntercept' || fire.type === 'intercept') continue;
		//A single-shot mode - Single Shots OR Wide Single - is always one discharge, whatever the
		//order says: the server forces the same thing in beforeFiringOrderResolution and would
		//ignore a bigger number here.
		used += LightningArray.isSingleShotMode(fire.firingMode) ? 1 : this.getOrderDischarges(fire);
	}
	return used;
};

LightningArray.prototype.getOffensiveOrderCount = function () {
	var n = 0;
	for (var i = 0; i < this.fireOrders.length; i++) {
		var t = this.fireOrders[i].type;
		if (t !== 'selfIntercept' && t !== 'intercept') n++;
	}
	return n;
};

LightningArray.prototype.getManualInterceptCount = function () {
	var n = 0;
	for (var i = 0; i < this.fireOrders.length; i++) {
		if (this.fireOrders[i].type === 'intercept') n++;
	}
	return n;
};

LightningArray.prototype.getDischargesUsed = function () {
	//One discharge per manual interception, exactly as the server charges it.
	return this.getOffensiveDischarges() + this.getManualInterceptCount();
};

LightningArray.prototype.getRemainingDischarges = function () {
	return Math.max(0, this.getDischargePool() - this.getDischargesUsed());
};

/* Drives the "this ship still has shots left" warning at commit.
   Refreshes the gun accounting on the way past for the same reason checkFinished does. */
LightningArray.prototype.checkForWastedShots = function () {
	this.updateGunAccounting();
	return this.getRemainingDischarges() > 0;
};

/* ⚠️ Also the seam that keeps ->guns fresh after a declaration. weaponManager.targetShip pushes the
   order this weapon returned and then asks checkFinished() - that is the first moment the new order
   is visible to us, and updateGunAccounting has to run before anything reads the manual-intercept
   cap. It cannot wait for initializationUpdate, which only runs when a system icon renders
   (arch_lazy_window_side_effects), and the player can reach the INCOMING list without re-rendering
   anything. */
LightningArray.prototype.checkFinished = function () {
	this.updateGunAccounting();
	return this.getRemainingDischarges() <= 0;
};

/* THE SAME FORMULA THE SERVER USES - see getGunsForInterceptAccounting in specialWeapons.php for
   the derivation. weaponManager caps manual interception at
   `counts.offensive + counts.intercept >= weapon.guns`, i.e. it counts ORDERS, while this weapon
   spends DISCHARGES; one combined shot of four is a single order that empties the pool. Setting
   guns = pool - offensiveDischarges + offensiveOrders makes that cap land on the discharges
   actually left, and matches Firing::isValidInterceptor exactly.

   Called from initializationUpdate, from checkFinished/checkForWastedShots (the declaration seam)
   and from doMultipleSelfIntercept. */
LightningArray.prototype.updateGunAccounting = function () {
	var pool = this.getDischargePool();
	this.guns = Math.max(0, pool - this.getOffensiveDischarges()) + this.getOffensiveOrderCount();
	return this.guns;
};

/* Is the array sitting in a mode that never fuses? TRUE of Single Shots and of Wide Single - the
   grouping axis, asked without reference to the wide one. */
LightningArray.prototype.isSingleShotMode = function () {
	return LightningArray.isSingleShotMode(this.firingMode);
};

/* ── The Wide Beam toggle ─────────────────────────────────────────────────────────────────────
   Two fields, and they are not the same question. `wideBeamFitted` is the REFIT (a purchase, from
   the lobby or the server's per-instance payload); `active` is THIS TURN's arm, and it is called
   that because the generic <SystemActivation> box reads system.active. Both are needed before a
   single die moves, which is what isWideBeamArmed() answers. */

LightningArray.prototype.hasWideBeam = function () {
	return !!this.wideBeamFitted;
};

LightningArray.prototype.isWideBeamArmed = function () {
	return this.hasWideBeam() && !!this.active;
};

/* Only the Fire phase, on my own ship, with the array able to shoot at all. ⚠️ NOT gated on
   "nothing declared yet": the toggle covers every shot the array fires this turn, so changing your
   mind after the first click has to re-price the shots already standing - which is why
   doActivate/doDeactivate re-price them rather than just flipping the flag. */
LightningArray.prototype.canToggleWideBeam = function () {
	if (!this.hasWideBeam()) return false;
	if (gamedata.gamephase != 3) return false;
	if (!this.ship || !gamedata.isMyShip(this.ship)) return false;
	if (shipManager.systems.isDestroyed(this.ship, this)) return false;
	if (shipManager.power.isOffline(this.ship, this)) return false;
	return weaponManager.isLoaded(this);
};

LightningArray.prototype.canActivate = function () {
	return this.canToggleWideBeam() && !this.active;
};

LightningArray.prototype.canDeactivate = function () {
	return this.canToggleWideBeam() && !!this.active;
};

LightningArray.prototype.getActivateLabel = function () {
	return "Wide Beam";
};

LightningArray.prototype.getDeactivateLabel = function () {
	return "Normal Beam";
};

LightningArray.prototype.doActivate = function () {
	this.active = true;
	this.onWideBeamToggled();
};

LightningArray.prototype.doDeactivate = function () {
	this.active = false;
	this.onWideBeamToggled();
};

/* Toggling changes what every standing shot will DO, so their quoted damage and their stored hit
   chance both have to be brought up to date. The hit chance does not actually move - a wide beam
   is the same bolt spread wider - but re-pricing is what keeps `chance` honest if that ever stops
   being true, and it is one call. */
LightningArray.prototype.onWideBeamToggled = function () {
	this.initializationUpdate();
	for (var i = 0; i < this.fireOrders.length; i++) {
		var fire = this.fireOrders[i];
		if (fire.type !== 'normal') continue;
		if (fire.weaponid != this.id) continue;
		if (fire.turn != gamedata.turn) continue;
		this.setOrderDischarges(fire, this.getOrderDischarges(fire));
	}
};

/* THE ROUND TRIP TO THE SERVER. weaponManager/shipManager post `individualNotesTransfer` for every
   system that fills it in, on every commit, in every phase - Manager::parseShips reads it back and
   hands it to the PHP doIndividualNotesTransfer(). Returning false posts nothing.
   ⚠️ Fire phase only, and only on a fitted array: an ordinary game must never write a note. */
LightningArray.prototype.doIndividualNotesTransfer = function () {
	this.individualNotesTransfer = "";   //never leave an earlier phase's value standing
	if (!this.hasWideBeam()) return false;
	if (gamedata.gamephase != 3) return false;
	this.individualNotesTransfer = [this.active ? 1 : 0];
	return true;
};

/* "Combined" / "Single" while the array is firing normally, "Combined-Wide" / "Single-Wide" while
   it is armed - the INCOMING list's row for the shot, and its per-shot sub-rows.
   ⭐ The suffix comes from the LIVE arm rather than from anything stored on the order, and that is
   correct BY CONSTRUCTION: arming covers every shot the array fires this turn in either mode, so
   toggling re-prices the standing orders (onWideBeamToggled) and their rows must move with them.
   ⚠️ Mode names themselves stay untouched - firingModes is a shared per-class object and the
   selector still offers exactly two entries (WALKERS_OF_SIGMA_PLAN.md 3.3: the wide beam is a
   toggle, NOT a third mode). This decorates the DISPLAY of a declared shot and nothing else. */
LightningArray.prototype.getFiringModeDisplayName = function (fireOrder) {
	var name = Weapon.prototype.getFiringModeDisplayName.call(this, fireOrder);
	if (!name || !this.isWideBeamArmed()) return name;
	return name + "-Wide";
};

/* Highest a single damage die can roll: 10, or 8 while the array is armed. The per-die floor never
   binds at the top of the range, so the penalty is the whole difference. Applies to BOTH firing
   modes - the arm and the mode are independent.
   ⚠️ Mirrors getDieCeiling() in specialWeapons.php - the ship window quotes this span and the
   server rolls it, so the two must agree. */
LightningArray.prototype.getDieCeiling = function () {
	if (!this.isWideBeamArmed()) return 10;
	return Math.max(this.wideBeamDieFloor, 10 - this.wideBeamDiePenalty);
};

/* Keeps the split-shot ceiling, the gun count and the tooltip in step with the pool. On the Medium
   variant the pool moves with charge time, so all three have to be re-derived every update. */
LightningArray.prototype.initializationUpdate = function () {
	var pool = this.getDischargePool();

	this.maxVariableShots = pool;
	//⚠️ ALWAYS true, in both modes and at a pool of one. canSplitShots is what routes a click through
	//doMultipleFireOrders; turning it off sends targetShip down the ordinary path instead, which
	//declares `guns` orders of `defaultShots` each and stamps neither the 'Sweeping' damageclass nor
	//the "Split" note - so the shot would silently vanish from the target's INCOMING list.
	this.canSplitShots    = true;
	this.updateGunAccounting();

	this.data["Discharges"] = pool;
	if (gamedata.gamephase == 3) {
		this.data["Discharges Remaining"] = this.getRemainingDischarges();
	} else {
		delete this.data["Discharges Remaining"];
	}

	//The two questions, read one at a time and independent by construction. The FIRING MODE picks
	//the row (Single Shots can only ever produce the 1-discharge one, so quoting the whole combined
	//span would overstate it); the WIDE BEAM TOGGLE picks the ceiling on a single die. All four
	//combinations fall out. The MINIMUM is unchanged by a wide beam: a d10 already rolls a 1 at
	//worst and the per-die floor is 1 too.
	var lo = this.combinedDamage[1];
	var hi = this.isSingleShotMode() ? lo
	                                 : this.combinedDamage[Math.min(pool, this.getMaxTabledCount())];
	if (lo && hi) {
		this.data["Damage"] = "" + (lo.dice + lo.add) + "-" + (hi.dice * this.getDieCeiling() + hi.add);
	}

	return this;
};

LightningArray.prototype.getMaxTabledCount = function () {
	var max = 1;
	for (var k in this.combinedDamage) {
		var n = parseInt(k, 10);
		if (n > max) max = n;
	}
	return max;
};

/* WHICH SHOT THESE TWO MIRRORS ARE DESCRIBING, in priority order:
     1. pendingCombinedCount - set only while a click is being turned into a fire order, so the
        chance stored on that order is priced at the count it carries.
     2. the fire order handed in - the INCOMING list and the ballistic hit chance both pass one
        (weaponManager.calculateHitChange's 5th argument, threaded down to here). A shot already
        declared must read at ITS OWN count, never at a look-ahead, or the shooter's own tally of
        what it has committed to that ship disagrees with what it will roll.
     3. otherwise a look-ahead - getPreviewCombinedCount reports what the NEXT click on that ship
        would fire, so the targeting list and the hover tooltips quote the chance the click gives.
   Both mirrors resolve it the same way, so the fire-control half and the range half always
   describe the same shot. */

/* Mirror of the server's combined-fire fire-control delta, in d20 units (weaponManager works in d20
   and multiplies by 5 at the end; the server adds delta*5 to ->needed, which is d100). */
LightningArray.prototype.calculateSpecialHitChanceMod = function (shooter, target, calledid, fireOrder) {
	var n = this.resolveCombinedCount(target, calledid, fireOrder);
	var row = this.combinedFireControl[n];
	if (!row) return 0;

	//weaponManager.getFireControlIndex is the client's canonical mirror of the server's
	//getFireControlIndex(). Use it rather than re-deriving from target.flight: a shuttle flight has
	//shipSizeClass 1 and FighterFlight overrides the index to 1, not 0, so a hand-rolled "is it a
	//flight" test would disagree with the server for exactly those units.
	var fcIndex = weaponManager.getFireControlIndex(target);
	if (fcIndex === null || fcIndex === undefined) return 0;
	if (row[fcIndex] === undefined || !this.fireControl || this.fireControl[fcIndex] === undefined) return 0;

	return row[fcIndex] - this.fireControl[fcIndex];
};

/* Combined fire also moves the RANGE penalty. Mirrors the server's calculateRangePenalty() override,
   and is reached because the blueprint carries specialRangeCalculation = true - weaponManager then
   asks the weapon instead of using rangePenalty * distance.

   ⚠️ `target`, `calledid` and `fireOrder` reach this hook only because weaponManager.calculateRangePenalty
   threads them through (every other weapon ignores the extra arguments). Without them this half of
   the prediction would be stuck on the single-discharge row while the fire-control half moved with
   the shot, and the two would describe different shots - worse than both being wrong the same way. */
LightningArray.prototype.calculateSpecialRangePenalty = function (distance, target, calledid, fireOrder) {
	var n = this.resolveCombinedCount(target, calledid, fireOrder);
	var perHex = this.combinedRangePenalty[n];
	//No row for this count (a table that was not extended with the pool): fall back to the weapon's
	//own rangePenalty, exactly as the server does.
	if (perHex === undefined) perHex = this.rangePenalty;
	return perHex * distance;
};

/* The three-way choice both mirrors make - see the block comment above. */
LightningArray.prototype.resolveCombinedCount = function (target, calledid, fireOrder) {
	if (this.pendingCombinedCount) return this.pendingCombinedCount;
	var declared = this.getCountForOrder(fireOrder);
	if (declared !== null) return declared;
	return this.getPreviewCombinedCount(target, calledid);
};

/* Discharges an ALREADY DECLARED order carries, or null when there is no such order to read. Null
   rather than 1 on purpose: 1 is a legitimate answer and would shadow the look-ahead. */
LightningArray.prototype.getCountForOrder = function (fireOrder) {
	if (!fireOrder) return null;
	if (fireOrder.weaponid !== undefined && fireOrder.weaponid != this.id) return null;
	//A single-shot order - Single Shots or Wide Single - is one discharge whatever ->shots claims;
	//the server forces it.
	if (LightningArray.isSingleShotMode(fireOrder.firingMode)) return 1;
	return this.getOrderDischarges(fireOrder);
};

/* One click declares ONE discharge - these are ordinary split-shot weapons, exactly like the Vorlon
   Discharge Gun, and a "gun" here IS a discharge. There is no allocation dialog.

   COMBINED FIRE mode: a second click on the SAME target does not add a second shot, it fuses
   another discharge into the shot already standing against that target - so the order that goes to
   the server carries 2, then 3, then 4, and its fire control, range penalty and damage all move to
   that row of the tables. Four clicks on one ship = one 4-discharge shot; four clicks on four ships
   = four single ones.
   SINGLE SHOTS mode: every click is a separate one-discharge order, never fused.

   The order is RETURNED rather than pushed: weaponManager.targetShip pushes it, then asks
   checkFinished() and unselects the weapon when the pool is spent. Pushing here and unselecting
   from inside this call would splice gamedata.selectedSystems while targetShip is iterating it. */
LightningArray.prototype.doMultipleFireOrders = function (shooter, target, system) {
	if (this.getRemainingDischarges() <= 0) {
		confirm.error(this.displayName + " has no discharges left this turn.");
		return [];
	}

	var calledid = -1;
	if (system && weaponManager.canWeaponCall(this)) {
		var calledSystem = system;
		while (calledSystem.parentId > 0) {
			calledSystem = shipManager.systems.getSystem(target, calledSystem.parentId);
		}
		calledid = calledSystem.id;
	}

	//The shot this click lands on, and what it grows to. getCombinableOrder is null in Single Shots
	//mode and whenever the standing shot is already at the largest tabled count, so both cases fall
	//through to a fresh 1-discharge order.
	var existing = this.getCombinableOrder(target, calledid);
	var count    = existing ? this.getOrderDischarges(existing) + 1 : 1;

	//Show - and store - the hit chance for THIS fused count, not for a single discharge. Both
	//mirrors read pendingCombinedCount, so the fire control and the range penalty describe the same
	//shot. Cleared immediately: everything outside a declaration derives its own preview count.
	this.pendingCombinedCount = count;
	var chance = window.weaponManager.calculateHitChange(shooter, target, this, calledid).hitChance;
	this.pendingCombinedCount = null;
	if (chance < 1) return [];

	//Growing a shot = replacing its order with one carrying the extra discharge. Dropping the old
	//one here (rather than mutating it in place) puts the declaration back on targetShip's ordinary
	//push/checkFinished/unselect path, and the regenerated id is identical because the array is the
	//same length again.
	var fireid;
	if (existing) {
		fireid = existing.id;
		var idx = this.fireOrders.indexOf(existing);
		if (idx >= 0) this.fireOrders.splice(idx, 1);
	} else {
		fireid = shooter.id + "_" + this.id + "_" + (this.fireOrders.length + 1);
	}

	return [{
		id: fireid,
		type: 'normal',
		shooterid: shooter.id,
		targetid: target.id,
		weaponid: this.id,
		calledid: calledid,
		turn: gamedata.turn,
		firingMode: this.firingMode,
		//THE fused count. ->shots is what every other split-shot weapon carries its shot count in,
		//and it is a whitelisted FireOrder constructor argument, so it survives the POST rebuild.
		shots: count,
		x: "null",
		y: "null",
		//⚠️ 'Sweeping' and "Split" are not decoration: getAllBallisticsAgainst admits a type "normal"
		//order to the INCOMING list only when its damageclass is 'Sweeping', and BallisticIconContainer
		//keys line re-creation on notes === "Split". Without both, a Lightning Array shot cannot be
		//seen - or manually intercepted - by the ship it is aimed at, which is the same pair of
		//values the Discharge Gun and the Psionic Concentrator carry.
		damageclass: 'Sweeping',
		chance: chance,
		hitmod: 0,
		notes: "Split"
	}];
};

/* The shot this weapon already has standing against that target IN THE MODE IT IS SITTING IN, or
   null when the next click should start a new one. Matched on target AND called system, so a called
   shot and a hull shot at the same ship stay separate shots. Never matches in a single-shot mode,
   never matches an earlier turn's order, and never matches a shot already at the largest tabled
   count.

   ⚠️⚠️ THE MODE MATCH IS AN EQUALITY, NOT "is it combinable" - equivalent today, and kept anyway.
   It used to read "is this order not Single Shots", which is the same test only while exactly ONE
   mode fuses. During the brief four-mode build of the wide beam it was not: a Wide Combined click
   fused into a standing ordinary Combined shot, and because growing a shot REPLACES its order (see
   doMultipleFireOrders) the discharge already committed was silently converted into a wide beam,
   taking the -2 per die and the cooldown with it. A shot's mode is stamped per order and
   multiModeSplit lets the player switch mid-turn, so shots only ever fuse with their own mode. */
LightningArray.prototype.getCombinableOrder = function (target, calledid) {
	if (this.isSingleShotMode()) return null;
	if (!target) return null;

	var targetid = (target.id !== undefined) ? target.id : target;
	var called   = (calledid > 0) ? calledid : -1;
	var maxRow   = this.getMaxTabledCount();
	var mode     = parseInt(this.firingMode, 10);

	for (var i = this.fireOrders.length - 1; i >= 0; i--) {
		var fire = this.fireOrders[i];
		if (fire.type !== 'normal') continue;
		if (fire.turn != gamedata.turn) continue;
		if (fire.targetid != targetid) continue;
		if (((fire.calledid > 0) ? fire.calledid : -1) !== called) continue;
		if (parseInt(fire.firingMode, 10) !== mode) continue;
		if (this.getOrderDischarges(fire) >= maxRow) continue;
		return fire;
	}
	return null;
};

/* How many discharges the NEXT click on that target would fire. This is what the hover previews
   quote - the targeting list, the ship tooltip - so the hit chance the player reads before clicking
   is the hit chance the click produces, rather than a single-discharge number that stops being true
   the moment the shot starts growing. With nothing left in the pool it reports the standing shot,
   because that is what the tables will actually be read at. */
LightningArray.prototype.getPreviewCombinedCount = function (target, calledid) {
	var existing = this.getCombinableOrder(target, calledid);
	var current  = existing ? this.getOrderDischarges(existing) : 0;
	if (this.getRemainingDischarges() <= 0) return Math.max(1, current);
	return Math.min(current + 1, this.getMaxTabledCount());
};

/* How many SHOTS one of this weapon's orders represents in the INCOMING list. A combined order is
   one fire order carrying several discharges, so the row reads "2x Lightning Array (Combined Fire)"
   rather than "1x". ⚠️ That list is a targeting aid for the FIRING player here, not a defence
   control: a direct-fire weapon declares in the Firing phase and the defender never sees the order
   before it resolves, so nothing about it needs to explain the shot to an opponent - it just has to
   count honestly. Read off ->shots, where the fused count lives until the server resolves the order
   and resets it. */
LightningArray.prototype.getIncomingShotCount = function (fireOrder) {
	if (!fireOrder) return 1;
	//A single-shot order - Single Shots or Wide Single - is one discharge whatever ->shots claims;
	//the server forces it.
	if (LightningArray.isSingleShotMode(fireOrder.firingMode)) return 1;
	return this.getOrderDischarges(fireOrder);
};

/* ── Withdrawing a shot ───────────────────────────────────────────────────────────────────────
   These are `multiModeSplit` weapons, so `weaponManager.removeFiringOrderMulti` and
   `removeFiringOrder` both hand the job straight to the weapon and do nothing else - the events are
   ours to fire.

   ⭐ Withdrawing ONE shot from a combined order PEELS ONE DISCHARGE off it: a 4-discharge shot
   becomes a 3-discharge shot, not nothing. Removing the whole order would hand back four discharges
   for one click on a button that says "remove a firing order", and the player has no way to put
   three of them back except by re-declaring. The stored hit chance is recomputed at the new count,
   because fusing moves it. A Single Shots order is one discharge already, so it just goes. */
LightningArray.prototype.removeMultiModeSplit = function (ship, target) {
	var fire = this.findWithdrawableOrder(target);
	if (!fire) return;

	var n = this.getOrderDischarges(fire);
	if (!LightningArray.isSingleShotMode(fire.firingMode) && n > 1) {
		this.setOrderDischarges(fire, n - 1);
	} else {
		var idx = this.fireOrders.indexOf(fire);
		if (idx >= 0) this.fireOrders.splice(idx, 1);
	}

	this.updateGunAccounting();
	webglScene.customEvent('SystemDataChanged', { ship: ship, system: this });
	webglScene.customEvent('SplitOrderRemoved', {
		shooter: ship,
		target: target || gamedata.getShip(fire.targetid)
	});
};

/* The shot a withdrawal should come out of: the most recent one in the mode the weapon is sitting
   in, and failing that the most recent one in ANY mode.

   ⚠️ The two callers gate themselves differently, which is why the fallback exists. The ship
   window only offers the button when weaponManager.hasOrderForMode says this mode HAS a shot, so
   pass 1 always finds it there. The enemy ship tooltip asks only hasTargetedThisShip - no mode test
   at all - so with a combined shot standing and the weapon switched to Single Shots, a mode-only
   search would leave that button doing nothing at all, silently. Preferring the current mode keeps
   "each mode owns its own shots" true wherever it can be. */
LightningArray.prototype.findWithdrawableOrder = function (target) {
	var mode = parseInt(this.firingMode, 10);
	var fallback = null;

	for (var i = this.fireOrders.length - 1; i >= 0; i--) {
		var fire = this.fireOrders[i];
		//Offensive orders only: a selfIntercept marker is withdrawn by its own button
		//(weaponManager.removeSelfInterceptSingle) and must not be eaten by this one.
		if (fire.type !== 'normal') continue;
		if (fire.weaponid != this.id) continue;
		if (fire.turn != gamedata.turn) continue;
		//Called from the enemy ship tooltip, `target` names the ship to take the shot back from.
		if (target && fire.targetid != target.id) continue;

		if (parseInt(fire.firingMode, 10) === mode) return fire;
		if (fallback === null) fallback = fire;
	}
	return fallback;
};

/* Everything this weapon has declared this turn, in every mode - what
   `weaponManager.removeFiringOrder` would have done itself for a non-multiModeSplit weapon, so it
   takes the selfIntercept markers too. */
LightningArray.prototype.removeAllMultiModeSplit = function (ship) {
	for (var i = this.fireOrders.length - 1; i >= 0; i--) {
		if (this.fireOrders[i].weaponid == this.id) this.fireOrders.splice(i, 1);
	}
	this.updateGunAccounting();
	webglScene.customEvent('SystemDataChanged', { ship: ship, system: this });
};

/* Rewrite an order to carry `n` discharges, re-pricing its stored hit chance at that count - the
   fire control and the range penalty both move with it, so a peeled shot that kept the 4-discharge
   number would show the player a chance the server will not roll. Leaves the chance alone if the
   target has gone (destroyed, withdrawn): a stale number beats a crash. */
LightningArray.prototype.setOrderDischarges = function (fireOrder, n) {
	fireOrder.shots = n;

	var target = gamedata.getShip(fireOrder.targetid);
	if (!target || !this.ship) return;

	this.pendingCombinedCount = n;
	var chance = window.weaponManager.calculateHitChange(this.ship, target, this, fireOrder.calledid).hitChance;
	this.pendingCombinedCount = null;
	fireOrder.chance = chance;
};

/* ── Interception ─────────────────────────────────────────────────────────────────────────────
   The full Array's effective loading time is 1, so Firing::isValidInterceptor never asks for a
   marker and weaponManager.canSelfInterceptSingle never offers one: it is simply auto-assigned with
   whatever discharges it did not fire. The MEDIUM's effective loading time is max(loadingtime,
   normalload) = 2, so it IS asked - and because canSplitShots is true, weaponManager routes that
   question through checkSelfInterceptSystem(), which is false on ShipSystem.prototype. Without the
   two overrides below the Medium could never consent, and therefore could never defend at all. */
LightningArray.prototype.checkSelfInterceptSystem = function () {
	//A single marker is consent for the whole weapon - the server's arithmetic cancels markers out
	//(see getGunsForInterceptAccounting), so a second one would buy nothing and only confuse the
	//"shots remaining" readout.
	for (var i = 0; i < this.fireOrders.length; i++) {
		if (this.fireOrders[i].type === 'selfIntercept') return false;
	}
	return this.getRemainingDischarges() > 0;
};

LightningArray.prototype.doMultipleSelfIntercept = function (ship) {
	if (!this.checkSelfInterceptSystem()) return;

	this.fireOrders.push({
		id: ship.id + "_" + this.id + "_" + (this.fireOrders.length + 1),
		type: "selfIntercept",
		shooterid: ship.id,
		//A selfIntercept order's targetid is the SHIP's own id, not a fire order's - every client
		//creation site does this and Firing::automateIntercept relies on it.
		targetid: ship.id,
		weaponid: this.id,
		calledid: -1,
		turn: gamedata.turn,
		firingMode: this.getInterceptOrderMode(),
		shots: 1,
		x: "null",
		y: "null",
		addToDB: true,
		damageclass: this.data["Weapon type"] ? this.data["Weapon type"].toLowerCase() : 'standard'
	});

	//The marker costs no discharge, but ->guns still has to be re-derived: the formula counts
	//offensive orders, and a stale value would misprice the manual-intercept cap.
	this.updateGunAccounting();
	webglScene.customEvent('SystemDataChanged', { ship: ship, system: this });
};

/* A defensive discharge is by definition a single one, so defensive orders are stamped Single Shots
   whatever mode the weapon is sitting in. That is about the order reading honestly in the log and
   never being priced as a fused shot.
   ⚠️ It says nothing about the wide beam, which is a property of the ARRAY this turn rather than of
   any order - the cooldown is kept off interception by firedWideBeamOnTurn() counting only type
   'normal' orders. */
LightningArray.prototype.getInterceptOrderMode = function () {
	return LightningArray.MODE_SINGLE;
};


/* Accelerator variant. The pool grows by one discharge per turn charged, so at one turn of charge
   there is exactly one discharge and nothing to combine - the pool, not a rule of its own, is what
   enforces "may not split until charged two turns". It starts the scenario at 1/2, part-charged. */
var MediumLightningArray = function MediumLightningArray(json, ship) {
	LightningArray.call(this, json, ship);
};
MediumLightningArray.prototype = Object.create(LightningArray.prototype);
MediumLightningArray.prototype.constructor = MediumLightningArray;

//Mirrors MediumLightningArray::$combinedDamageArray. Only TWO rows - its pool never exceeds 2.
MediumLightningArray.prototype.combinedDamage = {
	1: { dice: 4, add: 12 },
	2: { dice: 8, add: 12 }
};

//Mirrors MediumLightningArray::$combinedFireControlArray.
MediumLightningArray.prototype.combinedFireControl = {
	1: [6, 4, 2],
	2: [4, 5, 5]
};

//Mirrors MediumLightningArray::$combinedRangePenaltyArray.
MediumLightningArray.prototype.combinedRangePenalty = {
	1: 0.33,
	2: 0.25
};

MediumLightningArray.prototype.getDischargePool = function () {
	//Always show the full pool in the fleet builder, where nothing has charged yet - the same thing
	//the Slicer does with its own charge-keyed pools.
	if (gamedata.gamephase == -2) return this.normalload;

	var pool = parseInt(this.turnsloaded, 10);
	if (isNaN(pool)) pool = 0;
	if (pool > this.normalload) pool = this.normalload;
	if (pool < 1) pool = 1;
	return pool;
};


/* =====================================================================================
 * SENSOR CHARGE TRANSCEIVER - client half of SensorChargeTransceiver (specialWeapons.php).
 * WALKERS_OF_SIGMA_PLAN.md 3.9 (Stage 9), decision D2.
 *
 * ⚠️ EVERY NUMBER HERE IS A MIRROR of a constant on the PHP class. The server re-walks the
 * course from scratch and is the only authority on what it does; this half exists so the
 * player can see a legal course being built while they build it. Re-stat one side and the
 * other silently disagrees - and the failure mode is a course the client drew and the server
 * truncated, which reads as the game eating clicks.
 *
 * HOW A COURSE IS DECLARED
 * The transceiver is a hex-target split-shot weapon, so weaponManager.targetHex routes every
 * right-click on a hex into doMultipleHexFireOrders below. Each accepted click appends ONE
 * fire order carrying that waypoint's hex plus a "SCT|w:<n>" token in ->notes, and the chain
 * of those orders IS the course. Withdrawing uses the shared path
 * (weaponManager.removeFiringOrderMulti with no target), which pops the LAST order - exactly
 * right for a chain, and why this weapon needs no removeMultiModeSplit hook of its own.
 *
 * ⚠️ NOT the 'Sweeping' + "Split" contract the Lightning Arrays use. That pair exists to put a
 * split shot into the INCOMING list and to make BallisticIconContainer redraw its line, and
 * neither applies here: a waypoint is not a shot at a unit, and ->notes is spoken for by the
 * token. The course is drawn by BallisticIconContainer.refreshSensorChargeCourses instead.
 * ===================================================================================== */
var SensorChargeTransceiver = function SensorChargeTransceiver(json, ship) {
	Weapon.call(this, json, ship);
	//Plan trap 6: client system fields are shared BY REFERENCE across same-phpclass instances,
	//and the charge lines written below are per instance. Two transceivers on one hull would
	//otherwise share a tooltip and the second one built would win.
	this.data = Object.assign({}, this.data);
};
SensorChargeTransceiver.prototype = Object.create(Weapon.prototype);
SensorChargeTransceiver.prototype.constructor = SensorChargeTransceiver;

/* ⚠️ MIRROR PAIR with SensorChargeTransceiver::CHARGE_RANGE / CHARGE_MANOEUVRES /
   BOOST_POWER_PER_LEVEL in specialWeapons.php. */
SensorChargeTransceiver.prototype.chargeRange = 16;
SensorChargeTransceiver.prototype.chargeManoeuvres = 4;
SensorChargeTransceiver.prototype.boostPowerPerLevel = 2;

//The waypoint token. ⚠️ MIRROR PAIR with SensorChargeTransceiver::WAYPOINT_TOKEN.
SensorChargeTransceiver.WAYPOINT_TOKEN = 'SCT|w:';

/* The OPTIONAL second half of it: "|t:<shipid>", the unit the player picked out of a shared hex.
   ⚠️ MIRROR PAIR with SensorChargeTransceiver::TARGET_TOKEN. Written by the ship tooltip's
   "Target Ship" button (shipTooltipFireMenu.js) and ADVISORY only - the server re-tests the named
   unit against every eligibility rule and falls back to its own pick if it fails one.
   ⚠️ It is masked out of an enemy's payload by Weapon::$hideNotesFromEnemies, alongside the x/y
   hidetarget already blanks - so this must never become the way the OWNER'S client identifies a
   waypoint, only extra detail hanging off one. */
SensorChargeTransceiver.TARGET_TOKEN = '|t:';

//Boost levels bought this turn - one level is one extra hex OR one extra manoeuvre, spent as
//the course needs it. Mirrors SensorChargeTransceiver::getChargeBoost.
SensorChargeTransceiver.prototype.getChargeBoost = function () {
	var boost = parseInt(shipManager.power.getBoost(this), 10);
	return isNaN(boost) ? 0 : boost;
};

//This turn's waypoint orders, in the order they were clicked.
SensorChargeTransceiver.prototype.getWaypointOrders = function () {
	var orders = [];

	for (var i = 0; i < this.fireOrders.length; i++) {
		var fire = this.fireOrders[i];
		if (fire.weaponid != this.id) continue;
		if (fire.turn != gamedata.turn) continue;
		if (SensorChargeTransceiver.readWaypointIndex(fire) === null) continue;
		orders.push(fire);
	}

	orders.sort(function (a, b) {
		return SensorChargeTransceiver.readWaypointIndex(a) - SensorChargeTransceiver.readWaypointIndex(b);
	});

	return orders;
};

//The waypoint's place in the course, or null when this order carries no token at all.
SensorChargeTransceiver.readWaypointIndex = function (fire) {
	if (!fire || !fire.notes) return null;
	var match = /SCT\|w:(\d+)/.exec(fire.notes);
	return match ? parseInt(match[1], 10) : null;
};

//The unit the player named for this waypoint's hex, or null when they named none.
SensorChargeTransceiver.readWaypointTarget = function (fire) {
	if (!fire || !fire.notes) return null;
	var match = /SCT\|w:\d+\|t:(\d+)/.exec(fire.notes);
	return match ? parseInt(match[1], 10) : null;
};

/* ⭐ THE COURSE AS THE CLIENT UNDERSTANDS IT - the mirror of
   SensorChargeTransceiver::getChargeOutcome, minus the parts only the server needs (target
   selection and the receiver's self-damage). Everything that draws, validates or explains a
   course goes through this one function, so there is one definition of a legal path.

   ⚠️ It works off the ship's CURRENT position, which is what a direct-fire weapon launches
   from (weaponManager.getFiringHex). A course plotted before the ship finished moving would
   start from the wrong hex, which is one reason this is a Fire phase weapon.

   Returns null when the shooter cannot be resolved. Otherwise:
     origin              the launch hex
     waypoints           [{q, r}] in flight order, origin NOT included
     legs                [{from, to, bearing, length}] for the renderer
     hexes               every hex the charge enters, origin excluded, deduplicated
     hexesUsed / manoeuvresUsed   what the course has spent
     hexesLeft / manoeuvresLeft   the most a further leg could still spend on that axis alone
     boost / boostLeft   boost levels bought, and those not yet committed to either axis
     lastBearing         null before the first leg
     head                the hex a further leg would start from
     receiver            the friendly ship whose transceiver would take the charge, or null
     markers             [{q, r, text}] - the hexes worth marking on the map (getCourseMarkers) */
SensorChargeTransceiver.prototype.getCoursePlan = function (shooter) {
	if (!shooter) shooter = this.ship;
	if (!shooter || !shooter.movement || shooter.movement.length === 0) return null;

	var origin = shipManager.getShipPosition(shooter);
	if (!origin) return null;

	var plan = {
		//Carried so measureLeg can ask the LAUNCH ARC question - see isLaunchBearingOnArc. It is
		//the only thing on the plan that is not a number or a hex, and nothing that builds a
		//renderer signature reads it.
		shooter: shooter,
		origin: { q: origin.q, r: origin.r },
		waypoints: [], legs: [], hexes: [],
		hexesUsed: 0, manoeuvresUsed: 0,
		lastBearing: null, head: { q: origin.q, r: origin.r },
		boost: this.getChargeBoost(), receiver: null
	};

	var seen = {};
	seen[origin.q + ',' + origin.r] = true;

	var orders = this.getWaypointOrders();
	for (var i = 0; i < orders.length; i++) {
		var to = { q: parseInt(orders[i].x, 10), r: parseInt(orders[i].y, 10) };
		//A masked order: hidetarget blanks x/y to the STRING "null" for anyone not on the shooter's
		//team, so an enemy course resolves to an empty plan by construction and never draws.
		//⚠️ break, not continue - the legs of a course are a chain, and skipping a blanked one in
		//the middle would join the two hexes either side of it into a leg nobody plotted.
		if (isNaN(to.q) || isNaN(to.r)) break;

		var step = this.measureLeg(plan, to);
		if (!step.legal) break;                     //the server truncates at the same leg

		var walk = plan.head;
		for (var h = 0; h < step.length; h++) {
			walk = mathlib.moveInDirection(walk, step.bearing, 1);
			var key = walk.q + ',' + walk.r;
			if (seen[key]) continue;                //a course may cross itself; a hex is hit once
			seen[key] = true;
			plan.hexes.push({ q: walk.q, r: walk.r });
		}

		plan.legs.push({
			from: plan.head, to: to, bearing: step.bearing, length: step.length,
			//What this leg COST and what the player decided at the waypoint it ends on. Both are
			//what the map marks, and both are cheaper to record here than to re-derive later from
			//a bare list of hexes.
			turnCost: step.turnCost,
			targetId: SensorChargeTransceiver.readWaypointTarget(orders[i])
		});
		plan.waypoints.push(to);
		plan.hexesUsed += step.length;
		plan.manoeuvresUsed += step.turnCost;
		plan.head = to;
		plan.lastBearing = step.bearing;
	}

	/* WHAT IS STILL AFFORDABLE. The boost pool is shared between hexes and manoeuvres, so each
	   of these is "the most this axis could still take if nothing more is spent on the other" -
	   which is the number a player reading the ship window wants. The authoritative test for one
	   specific leg is measureLeg, which prices both axes together. */
	var hexOver = Math.max(0, plan.hexesUsed - this.chargeRange);
	var manOver = Math.max(0, plan.manoeuvresUsed - this.chargeManoeuvres);
	plan.boostLeft = Math.max(0, plan.boost - hexOver - manOver);
	plan.hexesLeft = Math.max(0, this.chargeRange + (plan.boost - manOver) - plan.hexesUsed);
	plan.manoeuvresLeft = Math.max(0, this.chargeManoeuvres + (plan.boost - hexOver) - plan.manoeuvresUsed);

	if (plan.waypoints.length > 0) plan.receiver = this.findReceiver(shooter, plan.head);

	plan.markers = this.getCourseMarkers(plan);

	return plan;
};

/* WHICH HEXES OF A COURSE GET A MARKER (user ruling 2026-09-06). Not every waypoint: a course run
   along one axis can hold a dozen of them, and marking them all buries the line under hexes that
   say nothing. What earns a hex is a place where something HAPPENED -

     - a hex where the charge MANOEUVRED, because manoeuvres are the scarce half of the budget and
       a turn is the only place the course could have gone somewhere else;
     - the HEAD, the last hex clicked so far, because that is where the next leg starts and where
       the reachable fan and the budget read-out are drawn;
     - any hex where the player NAMED A UNIT to hit. A waypoint that carries straight on costs no
       manoeuvre, which is exactly what makes it a free way to pick a unit out of a crowded hex -
       and without a marker that choice would have nothing on screen to confirm it.

   A manoeuvre is paid AT the waypoint a leg starts from, so leg i's turn cost marks legs[i].from,
   which is legs[i-1].to - and the choice recorded there is legs[i-1]'s. The first leg is the
   launch and is free (measureLeg), so the previous leg always exists in that branch. */
SensorChargeTransceiver.prototype.getCourseMarkers = function (plan) {
	var markers = [];
	var seen = {};

	function add(hex, targetId) {
		var key = hex.q + ',' + hex.r;
		if (seen[key]) return;                 //a course may cross itself; one marker per hex
		seen[key] = true;

		var target = (targetId !== null && targetId !== undefined) ? gamedata.getShip(targetId) : null;

		markers.push({ q: hex.q, r: hex.r, text: target ? target.name : "" });
	}

	for (var i = 1; i < plan.legs.length; i++) {
		var previous = plan.legs[i - 1];
		if (plan.legs[i].turnCost > 0 || previous.targetId !== null) add(previous.to, previous.targetId);
	}

	if (plan.legs.length) {
		var last = plan.legs[plan.legs.length - 1];
		add(plan.head, last.targetId);
	}

	return markers;
};

/* ⭐⭐ THE MOUNT AIMS THE LAUNCH, NOT THE COURSE (user ruling 2026-09-06). The Scribe's
   transceiver is a 300..60 mount; what that arc bounds is the direction the charge LEAVES ON, and
   after the first leg it steers wherever its manoeuvres will take it.

   ⚠️ Asked through weaponManager.isPosOnWeaponArc, about the hex ONE STEP along the launch
   bearing, rather than by comparing degrees here - the same call targetHex itself would have made,
   so this inherits the facing arithmetic, the roll mirror, split arcs and a jammed turret's
   reduced arc for free, and never has to assume a hex direction and a compass heading are the
   same number.
   ⚠️ MIRROR PAIR with SensorChargeTransceiver::isLaunchBearingOnArc, which asks the server's own
   getBearingOnPos the identical question about the identical hex. */
SensorChargeTransceiver.prototype.isLaunchBearingOnArc = function (shooter, bearing) {
	if (!shooter || bearing === null) return false;

	var origin = shipManager.getShipPosition(shooter);
	if (!origin) return false;

	return weaponManager.isPosOnWeaponArc(shooter, mathlib.moveInDirection(origin, bearing, 1), this);
};

/* The hex bearings a charge may be launched on - the six, less any the mount cannot point at.
   This is what the blue fan draws before the first waypoint is placed, so the fan and the click
   can never disagree about which directions are open. */
SensorChargeTransceiver.prototype.getLaunchBearings = function (shooter) {
	var bearings = [];
	var all = [0, 60, 120, 180, 240, 300];

	for (var i = 0; i < all.length; i++) {
		if (this.isLaunchBearingOnArc(shooter, all[i])) bearings.push(all[i]);
	}

	return bearings;
};

/* ⭐ THE HOOK weaponManager.targetHex CALLS INSTEAD OF ITS OWN ARC TEST, and the whole reason it
   exists (user report 2026-09-06: waypoints past the fourth were being refused with no message).
   targetHex gates every hex click on isPosOnWeaponArc(shooter, hex, weapon) - the mount's wedge
   measured from the SHIP - which is exactly right for a weapon that SHOOTS at a hex, and exactly
   wrong for one that flies a course through it: as the course wandered out of the Scribe's forward
   120 degrees the clicks were silently dropped, while the charge's own budget was untouched.
   Here the arc governs the LAUNCH and nothing else.

   ⭐ AND IT ANSWERS THE OFF-AXIS CASE TOO (user request 2026-09-07), which it used to wave
   through so that measureLeg could explain it in a sentence. Now that the blue fan draws the
   reachable hexes, a click outside it has already been answered on the map, and a pop-up saying
   so again is noise - so a hex that is not on a hex axis from the head of the course is simply
   NOT ON THE ARC, and targetHex drops the click in silence exactly as it does for every other
   straight-arc weapon (that `if` has no else). measureLeg still refuses the same two cases, with
   no reason attached, because it is also what truncates a course replayed out of the database. */
SensorChargeTransceiver.prototype.isHexOnFiringArc = function (shooter, hexpos) {
	var plan = this.getCoursePlan(shooter);
	if (!plan) return false;

	/* ⚠️ From the HEAD, not the origin: after the launch it is the end of the course that a new
	   leg runs from, and "does a straight leg reach that hex" is the question the fan answered.
	   getHexDirection returns 0 - not null - for the head itself, so a click there stays on the
	   arc and measureLeg gives it the "one hex further on" sentence it has always had. */
	var bearing = mathlib.getHexDirection(plan.head, hexpos);
	if (bearing === null) return false;             //not a straight leg; the fan never offered it

	if (plan.legs.length > 0) return true;          //already launched; the charge steers from here

	return this.isLaunchBearingOnArc(shooter, bearing);
};

/* Would a leg from the course's head to `to` be legal, and what does it cost? The shared budget
   test, used both when replaying the standing orders above and when judging a new click.
   ⚠️ MIRROR PAIR with the validation loop in SensorChargeTransceiver::getChargeOutcome, down to
   the single overspend expression - hexes overspent and manoeuvres overspent come out of the
   same pool of boost levels, which is what lets the player decide the split by flying. */
SensorChargeTransceiver.prototype.measureLeg = function (plan, to) {
	var start = new hexagon.Offset(plan.head.q, plan.head.r);
	var end = new hexagon.Offset(to.q, to.r);
	var length = start.distanceTo(end);
	var bearing = mathlib.getHexDirection(start, end);

	var step = { legal: false, length: length, bearing: bearing, turnCost: 0, reason: null };

	if (length < 1) {
		step.reason = "A waypoint has to be at least one hex further on than the last one.";
		return step;
	}
	/* ⚠️⚠️ THESE TWO REFUSALS CARRY NO REASON, ON PURPOSE (user request 2026-09-07). Both are
	   geometry the blue fan already draws - a leg has to run along a hex axis, and the first one
	   has to run along an axis the mount covers - so isHexOnFiringArc answers them first and
	   targetHex drops the click silently, the way every other straight-arc weapon refuses a hex
	   outside its arc. They stay HERE because measureLeg is also what replays the standing orders
	   in getCoursePlan: a course read back out of the database, or posted by a client that skipped
	   the check, has to truncate at leg one exactly as the server's resolver truncates it. A null
	   reason is the signal to refuse without saying anything - see doMultipleHexFireOrders. */
	if (bearing === null) return step;

	//THE LAUNCH IS THE ARC'S ONLY SAY.
	if (plan.lastBearing === null && !this.isLaunchBearingOnArc(plan.shooter, bearing)) return step;

	step.turnCost = (plan.lastBearing === null)
		? 0                                                      //the launch is free
		: mathlib.getHexTurnCost(plan.lastBearing, bearing);

	var overspend = Math.max(0, plan.hexesUsed + length - this.chargeRange)
		+ Math.max(0, plan.manoeuvresUsed + step.turnCost - this.chargeManoeuvres);

	if (overspend > plan.boost) {
		step.reason = (step.turnCost > 0 && plan.manoeuvresUsed + step.turnCost > this.chargeManoeuvres + plan.boost)
			? "The charge has no manoeuvres left to turn that far."
			: "That leg is longer than the charge has range for.";
		return step;
	}

	step.legal = true;
	return step;
};

/* The friendly ship on `hex` whose transceiver could take the charge, or null. Mirrors
   SensorChargeTransceiver::findReceiver - same TEAM, still alive, transceiver not destroyed,
   and the firing ship's own hex counts ("or possibly returning to the originator"). */
SensorChargeTransceiver.prototype.findReceiver = function (shooter, hex) {
	for (var i in gamedata.ships) {
		var ship = gamedata.ships[i];
		if (!ship || ship.team != shooter.team) continue;
		if (!ship.movement || ship.movement.length === 0) continue;
		if (shipManager.isDestroyed(ship)) continue;

		var position = shipManager.getShipPosition(ship);
		if (!position || position.q != hex.q || position.r != hex.r) continue;

		for (var s in ship.systems) {
			var system = ship.systems[s];
			if (!system || system.name !== 'SensorChargeTransceiver') continue;
			if (shipManager.systems.isDestroyed(ship, system)) continue;
			return ship;
		}
	}

	return null;
};

/* Every direction a further leg could legally take, with how far it could go: the six rays out
   of the course's head, each stopped at whatever the remaining budget pays for. The renderer
   draws one line per entry, and a ray is straight by construction, so nothing needs the
   individual hexes.

   The arithmetic is measureLeg's constraint solved for length. Turning is charged FIRST because
   it is the fixed cost of facing that way at all: with mOver manoeuvres of overspend, only
   (boost - mOver) levels are left to lengthen the leg. */
SensorChargeTransceiver.prototype.getReachableRays = function (plan) {
	var rays = [];
	if (!plan) return rays;

	/* ⚠️ BEFORE THE FIRST LEG THE MOUNT'S ARC APPLIES, so the fan must offer only the directions
	   the charge may launch on - otherwise it draws hexes a click on would be refused, which is the
	   one thing this overlay exists not to do. Afterwards the charge steers and all six are open. */
	var bearings = (plan.lastBearing === null)
		? this.getLaunchBearings(plan.shooter)
		: [0, 60, 120, 180, 240, 300];

	for (var i = 0; i < bearings.length; i++) {
		var turnCost = (plan.lastBearing === null)
			? 0 : mathlib.getHexTurnCost(plan.lastBearing, bearings[i]);

		var manOver = Math.max(0, plan.manoeuvresUsed + turnCost - this.chargeManoeuvres);
		if (manOver > plan.boost) continue;              //cannot afford to face that way at all

		var length = this.chargeRange + (plan.boost - manOver) - plan.hexesUsed;
		if (length < 1) continue;

		rays.push({ bearing: bearings[i], length: length, turnCost: turnCost });
	}

	return rays;
};

/* THE REACHABLE FAN AS HEXES rather than as lines (user request 2026-09-06). A ray is straight by
   construction, so this is only its length walked out along its bearing - but drawn as an actual
   patch of grid it answers "can I click there?" without the player having to judge whether a hex
   centre sits on a line.

   The head is excluded: a leg has to go somewhere (measureLeg's length >= 1 test), and the head
   already carries the course's own marker. Six distinct bearings out of one hex cannot overlap,
   so nothing here needs deduplicating. */
SensorChargeTransceiver.prototype.getReachableHexes = function (plan) {
	var hexes = [];
	if (!plan) return hexes;

	var rays = this.getReachableRays(plan);
	for (var i = 0; i < rays.length; i++) {
		var walk = plan.head;
		for (var n = 0; n < rays[i].length; n++) {
			walk = mathlib.moveInDirection(walk, rays[i].bearing, 1);
			hexes.push({ q: walk.q, r: walk.r });
		}
	}

	return hexes;
};

/* The unit the player named for this hex, or null. Mirrors the eligibility half of
   SensorChargeTransceiver::pickTargetInHex - an enemy, alive, and actually standing there - so a
   choice the server would silently drop is never written into the order to begin with.
   ⚠️ The client is not the authority and does not try to be: every one of these is re-tested at
   resolution, against positions that cannot have changed (the Fire phase is after movement) but
   against a unit that may since have been destroyed by an earlier shot. */
SensorChargeTransceiver.prototype.resolveHexTargetChoice = function (shooter, hexpos, targetId) {
	if (targetId === null || targetId === undefined || targetId === -1) return null;

	var target = gamedata.getShip(targetId);
	if (!target || !shooter) return null;
	if (target.team == shooter.team) return null;
	if (shipManager.isDestroyed(target)) return null;
	if (!target.movement || target.movement.length === 0) return null;

	var position = shipManager.getShipPosition(target);
	if (!position || position.q != hexpos.q || position.r != hexpos.r) return null;

	return target;
};

/* One click, one waypoint. weaponManager.targetHex has already checked the phase, the arc, the
   loading and the ship; everything below is this weapon's own.

   preferredTargetId arrives only from the ship tooltip's "Target Ship" button
   (shipTooltipFireMenu.js); a plain right-click on a hex names nobody and leaves the choice to the
   server's automatic pick, which is what the rules' "one target per hex" defaults to. */
SensorChargeTransceiver.prototype.doMultipleHexFireOrders = function (shooter, hexpos, preferredTargetId) {
	var plan = this.getCoursePlan(shooter);
	if (!plan) return [];

	var step = this.measureLeg(plan, { q: hexpos.q, r: hexpos.r });
	if (!step.legal) {
		//⚠️ A refusal with no reason is a SILENT one - the geometry cases isHexOnFiringArc has
		//already dropped, which only reach here from a course replayed out of the database. Never
		//concatenate a null into the player's face.
		if (step.reason) confirm.error("<b>" + this.displayName + "</b>: " + step.reason);
		return [];
	}

	//The index is what puts the course back in order after a round trip through the database -
	//see getWaypointOrders. It is a position in the chain, so it counts the standing orders.
	var index = plan.waypoints.length + 1;
	var fireid = shooter.id + "_" + this.id + "_" + index;

	//The choice rides ON the waypoint token rather than beside it, so readWaypointIndex keeps
	//matching either form and a course declared before this existed still reads correctly.
	var notes = SensorChargeTransceiver.WAYPOINT_TOKEN + index;
	var chosen = this.resolveHexTargetChoice(shooter, hexpos, preferredTargetId);
	if (chosen) notes += SensorChargeTransceiver.TARGET_TOKEN + chosen.id;

	return [{
		id: fireid,
		type: 'normal',
		shooterid: shooter.id,
		targetid: -1,
		weaponid: this.id,
		calledid: -1,
		turn: gamedata.turn,
		firingMode: this.firingMode,
		shots: 1,
		x: hexpos.q,
		y: hexpos.r,
		/* ⚠️ THIS STRING IS WHAT KEEPS A WAYPOINT OFF THE BALLISTIC LAYER. A hex order with
		   targetid -1 is admitted by weaponManager.getAllFireOrdersForAllShipsForTurn as a
		   ballistic, and BallisticIconContainer.consumeGamedata drops the order on exactly this
		   damageclass instead - so a waypoint gets no red "incoming fire" hex and no white arrow
		   back to the shooter, and the course draws itself instead (user request 2026-09-06).
		   ⚠️ Case matters and is not an accident: the shots the charge scores are built
		   server-side as 'electromagnetic', and the informational log row is 'SensorCharge' -
		   neither of which is this, and neither of which is a hex order anyway. */
		damageclass: 'sensorcharge',
		notes: notes
	}];
};

/* Is there anything left for a further click to do? Two ways there is not, and they are treated
   alike - the weapon unselects and its remaining-shots read-out goes to zero:

     - the charge has made CONTACT with a receiver (user ruling 2026-09-07). Ending on a friendly
       transceiver is what makes a course legal, and it is also where the flight is over: the
       charge is home, so the declaration is done and the weapon gets out of the player's way;
     - no further leg is affordable on any of the six bearings - it ran out of hexes.

   ⚠️ Unselecting is not a lock. The course lives in ->fireOrders, getCoursePlan rebuilds it from
   them, and nothing gates re-selecting a weapon on checkFinished - so a player who does want to
   fly on past a receiver to cross one more hex picks the transceiver up again and keeps clicking.
   ⚠️ plan.receiver is only resolved once a course exists (getCoursePlan), so a transceiver
   parked in a friendly's hex with nothing plotted does not finish the moment it is selected. */
SensorChargeTransceiver.prototype.isCourseFinished = function (plan) {
	if (!plan) return false;
	if (plan.receiver) return true;
	return this.getReachableRays(plan).length === 0;
};

SensorChargeTransceiver.prototype.checkFinished = function () {
	return this.isCourseFinished(this.getCoursePlan(this.ship));
};

/* The commit-time warning (gamedata.js' hasSplitFO sweep). A course that does not end on a friendly
   transceiver loses the charge and does NO damage at all, whatever it flew over - which is the one
   mistake with this weapon that costs the player their whole turn, and the map's yellow line is the
   only other thing that says so. The generic message ("weapons with unused shots") is not a perfect
   fit for it, but it names the ship, which is what the player needs to go and look. */
SensorChargeTransceiver.prototype.checkForWastedShots = function () {
	var plan = this.getCoursePlan(this.ship);
	if (!plan || plan.waypoints.length === 0) return false;
	return plan.receiver === null;
};

/* The ship window's read-out of the course. ⚠️ REMAINING IS DERIVED, never counted down from a
   stored field: maxVariableShots comes back from the server at its full value on every poll, so
   anything kept in it drifts the moment the page is reloaded mid-declaration.
   ⚠️ This runs only when a system icon renders (arch_lazy_window_side_effects), which is why the
   live feedback while plotting is the map overlay rather than these lines. */
SensorChargeTransceiver.prototype.initializationUpdate = function () {
	var plan = this.getCoursePlan(this.ship);
	if (!plan) return this;

	//⚠️ The SAME predicate the unselect uses, not getReachableRays on its own: a course that has
	//reached its receiver is finished, and a shots-remaining figure still counting up beside a
	//weapon that has just deselected itself reads as a bug.
	this.maxVariableShots = this.isCourseFinished(plan) ? 0 : (plan.manoeuvresLeft + 1);

	this.data["Charge course"] = plan.hexesUsed + " hexes, " + plan.manoeuvresUsed + " manoeuvres";
	/* ⚠️ THE TWO FIGURES COMPETE FOR THE SAME BOOST, so a boosted charge has to say so or they
	   read as two independent allowances that can both be spent (user report 2026-09-06). Each is
	   "the most this axis could still take if nothing more goes to the other"; the unspent levels
	   named here are what they are both drawing on. Same information the map's `+N` row carries. */
	this.data["Charge remaining"] = plan.hexesLeft + " hexes, " + plan.manoeuvresLeft + " manoeuvres"
		+ ((plan.boost > 0) ? " (sharing " + plan.boostLeft + " unspent boost)" : "");
	this.data["Charge received by"] = (plan.waypoints.length === 0)
		? "No course plotted"
		: (plan.receiver ? plan.receiver.name : "Invalid Route - charge will be lost");

	return this;
};


var LightChromaticPulsar = function LightChromaticPulsar(json, ship) {
    Weapon.call(this, json, ship);
};
LightChromaticPulsar.prototype = Object.create(Weapon.prototype);
LightChromaticPulsar.prototype.constructor = LightChromaticPulsar;