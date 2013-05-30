var Gravitic = function(json, ship)
{
    Weapon.call( this, json, ship);
}
Gravitic.prototype = Object.create( Weapon.prototype );
Gravitic.prototype.constructor = Gravitic;


var GravitonPulsar = function(json, ship)
{
    Pulse.call( this, json, ship);
}
GravitonPulsar.prototype = Object.create( Pulse.prototype );
GravitonPulsar.prototype.constructor = GravitonPulsar;

GravitonPulsar.prototype.initBoostableInfo = function(){
    // Needed because it can chance during initial phase
    // because of adding extra power.
    
    this.data["Weapon type"] ="Pulse";
    this.data["Damage type"] ="Standard";
    this.data["Grouping range"] = "" + this.grouping + "%";
    this.data["Max pulses"] = shipManager.power.getBoost(this) + 3;

    switch(shipManager.power.getBoost(this)){
        case 0:
            this.data["Pulses"] = '1-2';
            break;
        case 1:
            this.data["Pulses"] = '1-3';
            break;
        case 2:
            this.data["Pulses"] = '1-3';
            break;
        default:
            this.data["Pulses"] = '1-2';
            break;
    }            
    
    this.loadingtime = shipManager.power.getBoost(this)+1;
    
    return this;
}

GravitonPulsar.prototype.hasMaxBoost = function(){
    return true;
}

GravitonPulsar.prototype.getMaxBoost = function(){
    return this.maxBoostLevel;
}

var GraviticBolt = function(json, ship)
{
    Gravitic.call( this, json, ship);
}
GraviticBolt.prototype = Object.create( Gravitic.prototype );
GraviticBolt.prototype.constructor = GraviticBolt;

var GravitonBeam = function(json, ship)
{
    Weapon.call( this, json, ship);
}
GravitonBeam.prototype = Object.create( Weapon.prototype );
GravitonBeam.prototype.constructor = GravitonBeam;