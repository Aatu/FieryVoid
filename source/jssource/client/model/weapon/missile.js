var MissileLauncher = function(json, ship)
{
    Weapon.call( this, json, ship);
};

MissileLauncher.prototype = Object.create( Weapon.prototype );
MissileLauncher.prototype.constructor = MissileLauncher;

MissileLauncher.prototype.getAmmo = function(fireOrder)
{
    if(!mathlib.arrayIsEmpty(this.missileArray)){
        var mode = this.firingMode;
        if (fireOrder)
            mode = fireOrder.mode;

        //console.log("returning ammo: " + this.missileArray[mode].name);
        return this.missileArray[mode];
    }else{
        return null;
    }
    
}

MissileLauncher.prototype.changeFiringMode = function()
{
    var mode = this.firingMode;
    
    var next = false;
    var nextround = false;
    for (var i = 0; i< this.missileCount.length;i++)
    {
        if (next)
        {
            if (this.missileCount[i]>0)
            {
                this.firingMode = i;
                return;
            }
            else
            {
                if (i == this.missileCount.length-1)
                {
                    if (nextround)
                    {
                        this.firingMode = 1;
                        return;
                    }
                    nextround = true;
                    i = -1;
                }
            }
        }
        
        if (i == mode)
            next = true;
    }
    
    
    while(true)
    {
        var mode = this.firingMode+1;
        if (this.firingModes[mode]){
            this.firingMode = mode;
        }else{
            this.firingMode = 1;
        }
    }
}


var SMissileRack = function(json, ship)
{
    MissileLauncher.call( this, json, ship);
}
SMissileRack.prototype = Object.create( MissileLauncher.prototype );
SMissileRack.prototype.constructor = SMissileRack;

var SoMissileRack = function(json, ship)
{
    MissileLauncher.call( this, json, ship);
}
SoMissileRack.prototype = Object.create( MissileLauncher.prototype );
SoMissileRack.prototype.constructor = SoMissileRack;

var RMissileRack = function(json, ship)
{
    MissileLauncher.call( this, json, ship);
}
RMissileRack.prototype = Object.create( MissileLauncher.prototype );
RMissileRack.prototype.constructor = RMissileRack;

var LMissileRack = function(json, ship)
{
    MissileLauncher.call( this, json, ship);
}
LMissileRack.prototype = Object.create( MissileLauncher.prototype );
LMissileRack.prototype.constructor = LMissileRack;

var LHMissileRack = function(json, ship)
{
    MissileLauncher.call( this, json, ship);
}
LHMissileRack.prototype = Object.create( MissileLauncher.prototype );
LHMissileRack.prototype.constructor = LHMissileRack;

var BMissileRack = function(json, ship)
{
    MissileLauncher.call( this, json, ship);
}
BMissileRack.prototype = Object.create( MissileLauncher.prototype );
BMissileRack.prototype.constructor = BMissileRack;

var FighterMissileRack = function(json, ship)
{
    MissileLauncher.call( this, json, ship);
}
FighterMissileRack.prototype = Object.create( MissileLauncher.prototype );
FighterMissileRack.prototype.constructor = FighterMissileRack;

var FighterTorpedoLauncher = function(json, ship)
{
    FighterMissileRack.call( this, json, ship);
}
FighterTorpedoLauncher.prototype = Object.create( FighterMissileRack.prototype );
FighterTorpedoLauncher.prototype.constructor = FighterTorpedoLauncher;

var ReloadRack = function(json, ship)
{
    ShipSystem.call( this, json, ship);
}
ReloadRack.prototype = Object.create( ShipSystem.prototype );
ReloadRack.prototype.constructor = ReloadRack;

var BombRack = function(json, ship)
{
    MissileLauncher.call( this, json, ship);
}
BombRack.prototype = Object.create( MissileLauncher.prototype );
BombRack.prototype.constructor = BombRack;

