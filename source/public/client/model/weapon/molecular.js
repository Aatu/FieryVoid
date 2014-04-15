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