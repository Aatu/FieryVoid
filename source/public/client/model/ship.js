var Ship = function(json)
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

Ship.prototype = 
{
    constructor: Ship,
    
    getHitChangeMod: function(shooter, pos)
    {
        // plopje
        var pixelpos = hexgrid.positionToPixel(pos);
        
        var affectingSystems = Array();
        for (var i in this.systems)
        {
            var system = this.systems[i];

            if (!this.checkIsValidAffectingSystem(system, shipManager.getShipPosition(shooter)))
                continue;

            if(typeof system == 'Shield'
                && mathlib.getDistanceBetweenShipsInHex(shooter, this).toFixed(2) == 0
                && shooter.flight
            ){
                // Shooter is a flight, and the flight is under the shield
                continue;
            }

            var mod = system.getDefensiveHitChangeMod(this, shooter, pixelpos);

            if ( ! (affectingSystems[system.defensiveType])
                || affectingSystems[system.defensiveType] < mod)
            {
      //          console.log("getting defensive: " + system.name + " mod: " + mod);
                affectingSystems[system.getDefensiveType] = mod;
            }

        }
        var sum = 0;
        for (var i in affectingSystems)
        {
            sum += affectingSystems[i];
        }
        return sum;
    },
       
    checkIsValidAffectingSystem: function(system, pos)
    {
        if (!(system.defensiveType))
            return false;

        //If the system was destroyed last turn continue 
        //(If it has been destroyed during this turn, it is still usable)
        if (system.destroyed)
           return false;

        //If the system is offline either because of a critical or power management, continue
        if (shipManager.power.isOffline(this, system))
            return false;

        //if the system has arcs, check that the position is on arc
        if(typeof system.startArc == 'number' &&  typeof system.endArc == 'number'){

            var tf = shipManager.getShipHeadingAngle(this);

            //get the heading of position, not ship (in case ballistic)
            var heading = mathlib.getCompassHeadingOfPosition(this, pos);

            //if not on arc, continue!
            if (!mathlib.isInArc(
                heading, 
                mathlib.addToDirection(system.startArc, tf),
                mathlib.addToDirection(system.endArc, tf) ))
            {
                return false;
            }
        }

        return true;
    },
    
    checkShieldGenerator: function(){
        var shieldCapacity = 0;
        var activeShields = 0;
        
        for(var i in this.systems){
            var system = this.systems[i];
            
            if(system.name == "shieldGenerator"){
                if(system.destroyed || shipManager.power.isOffline(this, system)){
                    return true;
                }
                
                shieldCapacity = system.output + shipManager.power.getBoost(system);
            }
            
            if( system.name == "graviticShield"
                && !(system.destroyed || shipManager.power.isOffline(this, system))
              )
            {
                activeShields = activeShields + 1;
            }
        }
        
        return shieldCapacity >= activeShields;
    }
    
}

        
        
        
        
