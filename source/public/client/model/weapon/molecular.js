var Molecular = function(json, ship)
{
    Weapon.call( this, json, ship);
}
Molecular.prototype = Object.create( Weapon.prototype );
Molecular.prototype.constructor = Molecular;


var FusionCannon = function(json, ship)
{
    Molecular.call( this, json, ship);
}
FusionCannon.prototype = Object.create( Molecular.prototype );
FusionCannon.prototype.constructor = FusionCannon;

var HeavyFusionCannon = function(json, ship)
{
    Molecular.call( this, json, ship);
}
HeavyFusionCannon.prototype = Object.create( Molecular.prototype );
HeavyFusionCannon.prototype.constructor = HeavyFusionCannon;


var LightfusionCannon = function(json, ship)
{
    Molecular.call( this, json, ship);
}
LightfusionCannon.prototype = Object.create( Molecular.prototype );
LightfusionCannon.prototype.constructor = LightfusionCannon;


var MolecularDisruptor = function(json, ship)
{
    Molecular.call( this, json, ship);
}
MolecularDisruptor.prototype = Object.create( Molecular.prototype );
MolecularDisruptor.prototype.constructor = MolecularDisruptor;

var DestabilizerBeam = function(json, ship)
{
    Molecular.call( this, json, ship);
}
DestabilizerBeam.prototype = Object.create( Molecular.prototype );
DestabilizerBeam.prototype.constructor = DestabilizerBeam;

var MolecularFlayer = function(json, ship)
{
    Molecular.call( this, json, ship);
}
MolecularFlayer.prototype = Object.create( Molecular.prototype );
MolecularFlayer.prototype.constructor = MolecularFlayer;

var FusionAgitator = function(json, ship)
{
    Molecular.call( this, json, ship);
}
FusionAgitator.prototype = Object.create( Molecular.prototype );
FusionAgitator.prototype.constructor = FusionAgitator;

FusionAgitator.prototype.clearBoost = function(){
        for (var i in system.power){
                var power = system.power[i];
                if (power.turn != gamedata.turn)
                        continue;

                if (power.type == 2){
                    system.power.splice(i, 1);

                    return;
                }
        }
}

FusionAgitator.prototype.hasMaxBoost = function(){
    return true;
}

FusionAgitator.prototype.getMaxBoost = function(){
    return this.maxBoostLevel;
}


FusionAgitator.prototype.initBoostableInfo = function(){

    switch(shipManager.power.getBoost(this)){
        case 0:
            this.data["Damage"] = '15 - 65';
            break;
        case 1:
            this.data["Damage"] = '16 - 75';
            break;
        case 2:
            this.data["Damage"] = '17 - 85';
            break;
        case 3:
            this.data["Damage"] = '18 - 95';
            break;
        case 4:
            this.data["Damage"] = '19 - 105';
            break;
        default:
            this.data["Damage"] = '15 - 65';
            break;
    }            
    return this;
}