
var InterceptorMkI = function(json, ship)
{
    Weapon.call( this, json, ship);
    this.defensiveType = "Interceptor";
}
InterceptorMkI.prototype = Object.create( Weapon.prototype );
InterceptorMkI.prototype.constructor = InterceptorMkI;

InterceptorMkI.prototype.getDefensiveHitChangeMod = 
    function(target, shooter, pos)
{
    return shipManager.systems.getOutput(target, this);
}

var InterceptorMkII = function(json, ship)
{
    InterceptorMkI.call( this, json, ship);
}
InterceptorMkII.prototype = Object.create( InterceptorMkI.prototype );
InterceptorMkII.prototype.constructor = InterceptorMkII;

var Shield = function(json, ship)
{
    ShipSystem.call( this, json, ship);
    this.defensiveType = "Shield";
}

Shield.prototype = Object.create( ShipSystem.prototype );
Shield.prototype.constructor = Shield;

Shield.prototype.getDefensiveHitChangeMod = 
    function(target, shooter, pos)
{
    if (shooter.flight && mathlib.getDistanceBetweenShipsInHex(target, shooter) == 0)
        return 0;
    
    return shipManager.systems.getOutput(target, this);
}

