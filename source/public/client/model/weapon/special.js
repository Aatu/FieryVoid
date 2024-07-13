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
        this.range = 2 + 2*shipManager.power.getBoost(this);
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

var VorlonDischargeGun = function VorlonDischargeGun(json, ship) {
    Weapon.call(this, json, ship);
};
VorlonDischargeGun.prototype = Object.create(Weapon.prototype);
VorlonDischargeGun.prototype.constructor = VorlonDischargeGun;
VorlonDischargeGun.prototype.initializationUpdate = function() {
    // Needed because it can change power consumption during firing phase, depending on power and number of shots being changed
	this.powerReq = 0;
	var isFiring = weaponManager.hasFiringOrder(this.ship, this);
    if (isFiring) {
		var firing = weaponManager.getFiringOrder(this.ship, this);
		this.powerReq = 2*firing.shots*firing.firingMode;		
	}
    return this;
};

var VorlonDischargeCannon = function VorlonDischargeCannon(json, ship) {
    Weapon.call(this, json, ship);
};
VorlonDischargeCannon.prototype = Object.create(Weapon.prototype);
VorlonDischargeCannon.prototype.constructor = VorlonDischargeCannon;
VorlonDischargeCannon.prototype.initializationUpdate = function() {
    // Needed because it can change power consumption during firing phase, depending on power and number of shots being changed
	this.powerReq = 0;
	var isFiring = weaponManager.hasFiringOrder(this.ship, this);
    if (isFiring) {
		var firing = weaponManager.getFiringOrder(this.ship, this);
		this.powerReq = 5*firing.shots*firing.firingMode;		
	}
    return this;
};

var VorlonLightningCannon = function VorlonLightningCannon(json, ship) {
    Weapon.call(this, json, ship);
};
VorlonLightningCannon.prototype = Object.create(Weapon.prototype);
VorlonLightningCannon.prototype.constructor = VorlonLightningCannon;
VorlonLightningCannon.prototype.initializationUpdate = function() {
    // Needed because it can change power consumption during firing phase, depending on power and number of shots being changed
	this.powerReq = 0;
	var isFiring = weaponManager.hasFiringOrder(this.ship, this);
    if (isFiring) {
		var firing = weaponManager.getFiringOrder(this.ship, this);
		this.powerReq = this.powerRequiredArray[firing.firingMode][1]; //element is array Number of prongs/Power)		
	}
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
    // Needed because it can change power consumption during firing phase, depending on power and number of shots being changed
	this.powerReq = 0;
	var isFiring = weaponManager.hasFiringOrder(this.ship, this);
    if (isFiring) {
		var firing = weaponManager.getFiringOrder(this.ship, this);
		this.powerReq = this.powerRequiredArray[firing.firingMode][1]; //element is array Number of prongs/Power)		
	}
    return this;
};

var VorlonDischargePulsar = function VorlonDischargePulsar(json, ship) {
    Weapon.call(this, json, ship);
};
VorlonDischargePulsar.prototype = Object.create(Weapon.prototype);
VorlonDischargePulsar.prototype.constructor = VorlonDischargePulsar;
VorlonDischargePulsar.prototype.initializationUpdate = function() {
    // Needed because it can change power consumption during firing phase, depending on power and number of shots being changed
//TO BE ACTUALLY IMPLEMENTED!!!!!!!
};

var PsionicConcentrator = function PsionicConcentrator(json, ship) {
    Weapon.call(this, json, ship);
};
PsionicConcentrator.prototype = Object.create(Weapon.prototype);
PsionicConcentrator.prototype.constructor = PsionicConcentrator;

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
	
    switch (shipManager.power.getBoost(this)) {
        case 0:
            this.data["Damage"] = '66 - 120';
            this.data["Boostlevel"] = '0';
            break;
        case 1:
            this.data["Damage"] = '76 - 148';
            this.data["Boostlevel"] = '1';
            break;
        case 2:
            this.data["Damage"] = '86 - 176';
            this.data["Boostlevel"] = '2';
            break;
        case 3:
            this.data["Damage"] = '96 - 204';
            this.data["Boostlevel"] = '3';
            break;
        default:
            this.data["Damage"] = '66 - 120';
            this.data["Boostlevel"] = '0';
            break;
	}
	
/*	
	if (!window.weaponManager.isLoaded(this)) {
		this.outputDisplay = window.weaponManager.isLoaded(this);
	} else if (this.data.Boostlevel > 0) {
		this.outputDisplay = this.data.Boostlevel;
	} else {
		this.outputDisplay = '-'; //'0' is not shown!
	}        
*/
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
            this.data["Damage"] = '38 - 65';
            this.data["Boostlevel"] = '0';
            break;
        case 1:
            this.data["Damage"] = '40 - 85';
            this.data["Boostlevel"] = '1';
            break;
        case 2:
            this.data["Damage"] = '42 - 105';
            this.data["Boostlevel"] = '2';
            break;
        default:
            this.data["Damage"] = '38 - 65';
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

PsychicField.prototype.initBoostableInfo = function(){
    // Needed because it can change during initial phase
    // because of adding extra power.
    if(window.weaponManager.isLoaded(this)){
        this.range = 4 + 1*shipManager.power.getBoost(this);
        this.data["Range"] = this.range;
        this.minDamage = 1 + shipManager.power.getBoost(this);//Psychic Field does flat damage, mainly to prioritise against other fields.
        this.minDamage = Math.max(1,this.minDamage);
        this.maxDamage =  1 + shipManager.power.getBoost(this);
        this.data["Damage"] = "" + this.minDamage;
    }
    else{
        var count = shipManager.power.getBoost(this);
        for(var i = 0; i < count; i++){
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
/*
//needed for Spark Curtain upgrade
PsychicField.prototype.getDefensiveHitChangeMod = function (target, shooter, weapon) {
    if (!weapon.ballistic) return 0;//only ballistic weapons are affected
	var out = shipManager.systems.getOutput(target, this);
	if (shipManager.power.getBoost(this) >= out){ //if boost is equal to output - this means base output is 0 = no Spark Curtain mod!
		out = 0;
	}
	return out;
}; 
*/
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

	   	if (this.launcher.fireOrders.length > 0)	{	// check that launcher has firing orders.
			var aFireOrder = this.launcher.fireOrders[0]; 		    

			sPosLaunch = new hexagon.Offset(aFireOrder.x, aFireOrder.y); 
		} else{
		sPosLaunch = shipManager.movement.getPositionAtStartOfTurn(shooter, gamedata.turn); 	
		}	
	return sPosLaunch;
	
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



