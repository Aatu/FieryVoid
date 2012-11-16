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
        var affectingSystems = Array();
        for (var i in this.systems)
        {
            var system = this.systems[i];
            if (!this.checkIsValidAffectingSystem(system, pos))
                continue;

            var mod = system.getDefensiveHitChangeMod(this, shooter, pos);

            if ( ! (affectingSystems[system.defensiveType])
                || affectingSystems[system.defensiveType] < mod)
            {
                console.log("getting defensive: " + system.name + " mod: " + mod);
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
        if(system.startArc && system.endArc){

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
    }
    
}

        
        
        
        
