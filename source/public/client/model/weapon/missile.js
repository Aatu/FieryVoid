var MissileLauncher = function(json, ship)
{
    Weapon.call( this, json, ship);
}
MissileLauncher.prototype = Object.create( Weapon.prototype );
MissileLauncher.prototype.constructor = MissileLauncher;

MissileLauncher.prototype.getAmmo = function(fireOrder)
{
    var mode = this.firingMode;
    if (fireOrder)
        mode = fireOrder.mode;
    
    console.log("returning ammo: " + this.firingModes[mode]);
    return new window[this.firingModes[mode]];
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


var LMissileRack = function(json, ship)
{
    MissileLauncher.call( this, json, ship);
}
LMissileRack.prototype = Object.create( MissileLauncher.prototype );
LMissileRack.prototype.constructor = LMissileRack;



