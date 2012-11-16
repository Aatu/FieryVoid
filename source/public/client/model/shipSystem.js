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

var Fighter = function(json, ship)
{
    for (var i in json)
    {
        if (i == 'systems')
        {
            this.systems = SystemFactory.createSystemsFromJson(json[i], this);
        }
        else
        {
            this[i] = json[i];
        }
    }
}

Fighter.prototype = Object.create( ShipSystem.prototype );
Fighter.prototype.constructor = Fighter;


var Weapon = function(json, ship)
{
    ShipSystem.call( this, json, ship);
}

Weapon.prototype = Object.create( ShipSystem.prototype );
Weapon.prototype.constructor = Weapon;

Weapon.prototype.getAmmo = function(fireOrder)
{
    return null;
}
Weapon.prototype.changeFiringMode = function()
{
    var mode = this.firingMode+1;
    if (this.firingModes[mode]){
        this.firingMode = mode;
    }else{
        this.firingMode = 1;
    }
}
var Ballistic = function(json, ship)
{
    Weapon.call( this, json, ship);
}

Ballistic.prototype = Object.create( Weapon.prototype );
Ballistic.prototype.constructor = Ballistic;