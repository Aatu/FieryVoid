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
    return shipManager.systems.getOutput(target, this);
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

/* moved to systems!
var SelfRepair = function(json, ship)
{
    Weapon.call( this, json, ship);
}
SelfRepair.prototype = Object.create( Weapon.prototype );
SelfRepair.prototype.constructor = SelfRepair;
*/