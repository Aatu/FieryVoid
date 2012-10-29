var ShipSystem = function(json, ship)
{
    this.ship = ship;
    
    for (var i in json)
    {
        this[i] = json[i];
    }
}

ShipSystem.prototype = {

	constructor: ShipSystem
}

var DefensiveSystem = function(json, ship)
{
    this.defensiveType = "none";
    ShipSystem.call( this, json, ship);
}

DefensiveSystem.prototype = Object.create( ShipSystem.prototype );

DefensiveSystem.prototype.constructor = DefensiveSystem;

DefensiveSystem.prototype.getDefensiveType = function()
{
    return this.defensiveType;
}

DefensiveSystem.prototype.getDefensiveHitChangeMod = 
    function(target, shooter, pos)
{
    return shipManager.systems.getOutput(target, this);
}

var Interceptor = function(json, ship)
{
    DefensiveSystem.call( this, json, ship);
    this.defensiveType = "Interceptor";
}

Interceptor.prototype = Object.create( DefensiveSystem.prototype );

var Shield = function(json, ship)
{
    DefensiveSystem.call( this, json, ship);
    this.defensiveType = "Shield";
}

Shield.prototype = Object.create( DefensiveSystem.prototype );

Shield.prototype.getDefensiveHitChangeMod = 
    function(target, shooter, pos)
{
    if (shooter.flight && mathlib.getDistanceBetweenShipsInHex(target, shooter) == 0)
        return 0;
    
    return shipManager.systems.getOutput(target, this);
}