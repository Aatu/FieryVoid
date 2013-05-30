
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

var EMShield = function(json, ship)
{
    Shield.call( this, json, ship);
}

Shield.prototype = Object.create( Shield.prototype );
EMShield.prototype.constructor = EMShield;

var GraviticShield = function(json, ship)
{
    Shield.call( this, json, ship);
}

GraviticShield.prototype = Object.create( Shield.prototype );
GraviticShield.prototype.constructor = GraviticShield;

var ShieldGenerator = function(json, ship)
{
    ShipSystem.call( this, json, ship);
    this.defensiveType = "Shield";
}

ShieldGenerator.prototype = Object.create( ShipSystem.prototype );
ShieldGenerator.prototype.constructor = ShieldGenerator;
