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

var PsychicField = function(json, ship)
{
    Weapon.call( this, json, ship);
}
PsychicField.prototype = Object.create( Weapon.prototype );
PsychicField.prototype.constructor = PsychicField;
PsychicField.prototype.initBoostableInfo = function(){
    // Needed because it can change during initial phase
    // because of adding extra power.
    if(window.weaponManager.isLoaded(this)){
        this.range = 2 + 2*shipManager.power.getBoost(this);
        this.data["Range"] = this.range;
  //      this.minDamage = 2 - shipManager.power.getBoost(this);
  //      this.minDamage = Math.max(0,this.minDamage);
  //      this.maxDamage =  7 - shipManager.power.getBoost(this);
  //      this.data["Damage"] = "" + this.minDamage + "-" + this.maxDamage;
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
};

var PsionicConcentrator = function PsionicConcentrator(json, ship) {
    Weapon.call(this, json, ship);
};
PsionicConcentrator.prototype = Object.create(Weapon.prototype);
PsionicConcentrator.prototype.constructor = PsionicConcentrator;

var HeavyPsionicLance = function HeavyPsionicLance(json, ship) {
    Molecular.call(this, json, ship);
};
HeavyPsionicLance.prototype = Object.create(Molecular.prototype);
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
    switch (shipManager.power.getBoost(this)) {
        case 0:
            this.data["Damage"] = '48 - 120';
            this.data["Boostlevel"] = '0';
            break;
        case 1:
            this.data["Damage"] = '50 - 140';
            this.data["Boostlevel"] = '1';
            break;
        case 2:
            this.data["Damage"] = '52 - 160';
            this.data["Boostlevel"] = '2';
            break;
        case 3:
            this.data["Damage"] = '54 - 180';
            this.data["Boostlevel"] = '3';
            break;
        default:
            this.data["Damage"] = '48 - 120';
            this.data["Boostlevel"] = '0';
            break;
    }
    return this;
};

var PsionicLance = function PsionicLance(json, ship) {
    Molecular.call(this, json, ship);
};
PsionicLance.prototype = Object.create(Molecular.prototype);
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
    switch (shipManager.power.getBoost(this)) {
        case 0:
            this.data["Damage"] = '28 - 55';
            this.data["Boostlevel"] = '0';
            break;
        case 1:
            this.data["Damage"] = '29 - 65';
            this.data["Boostlevel"] = '1';
            break;
        case 2:
            this.data["Damage"] = '30 - 75';
            this.data["Boostlevel"] = '2';
            break;
        case 3:
            this.data["Damage"] = '31 - 85';
            this.data["Boostlevel"] = '3';
            break;
        default:
            this.data["Damage"] = '28 - 55';
            this.data["Boostlevel"] = '0';
            break;
    }
    return this;
};
