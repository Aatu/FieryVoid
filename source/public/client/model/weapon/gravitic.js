var Gravitic = function(json, ship)
{
    Weapon.call( this, json, ship);
}
Gravitic.prototype = Object.create( Weapon.prototype );
Gravitic.prototype.constructor = Gravitic;


var GravitonPulsar = function(json, ship)
{
    Pulse.call( this, json, ship);
}
GravitonPulsar.prototype = Object.create( Pulse.prototype );
GravitonPulsar.prototype.constructor = GravitonPulsar;

GravitonPulsar.prototype.initBoostableInfo = function(){
    // Needed because it can chance during initial phase
    // because of adding extra power.
    
    this.data["Weapon type"] ="Pulse";
    this.data["Damage type"] ="Standard";
    this.data["Grouping range"] = "" + this.grouping + "%";
    this.data["Max pulses"] = shipManager.power.getBoost(this) + 3;

    switch(shipManager.power.getBoost(this)){
        case 0:
            this.data["Pulses"] = 'D2';
            break;
        case 1:
            this.data["Pulses"] = 'D3 +1';
            break;
        case 2:
            this.data["Pulses"] = 'D3 +2';
            break;
        default:
            this.data["Pulses"] = 'D2';
            break;
    }            

    if(window.weaponManager.isLoaded(this)){
        this.loadingtime = 1 + shipManager.power.getBoost(this);
        this.turnsloaded = 1 + shipManager.power.getBoost(this);
        this.normalload =  1 + shipManager.power.getBoost(this);
    }
    else{
        var count = shipManager.power.getBoost(this);
        
        for(var i = 0; i < count; i++){
            shipManager.power.unsetBoost(null, this);
        }
    }

    this.intercept = this.getInterceptRating();
    this.data.Intercept = this.getInterceptRating()*(-5);

    return this;
}

GravitonPulsar.prototype.getInterceptRating = function()
{
    return (1 + shipManager.power.getBoost(this));
}

GravitonPulsar.prototype.clearBoost = function(){
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

GravitonPulsar.prototype.hasMaxBoost = function(){
    return true;
}

GravitonPulsar.prototype.getMaxBoost = function(){
    return this.maxBoostLevel;
}

var GraviticBolt = function(json, ship)
{
    Gravitic.call( this, json, ship);
}
GraviticBolt.prototype = Object.create( Gravitic.prototype );
GraviticBolt.prototype.constructor = GraviticBolt;

GraviticBolt.prototype.initBoostableInfo = function(){
    // Needed because it can chance during initial phase
    // because of adding extra power.
    
    this.data["Weapon type"] ="Gravitic";
    this.data["Damage type"] ="Standard";

    switch(shipManager.power.getBoost(this)){
        case 0:
            this.data["Damage"] = '9';
            break;
        case 1:
            this.data["Damage"] = '12';
            break;
        case 2:
            this.data["Damage"] = '15';
            break;
        default:
            this.data["Damage"] = '9';
            break;
    }            
    
    if(window.weaponManager.isLoaded(this)){
        this.loadingtime = 1 + shipManager.power.getBoost(this);
        this.turnsloaded = 1 + shipManager.power.getBoost(this);
        this.normalload =  1 + shipManager.power.getBoost(this);
    }
    else{
        var count = shipManager.power.getBoost(this);
        
        for(var i = 0; i < count; i++){
            shipManager.power.unsetBoost(null, this);
        }
    }
    
    this.intercept = this.getInterceptRating();
    this.data.Intercept = this.getInterceptRating()*(-5);

    return this;
}

GraviticBolt.prototype.getInterceptRating = function()
{
    return (1 + shipManager.power.getBoost(this));
}


GraviticBolt.prototype.clearBoost = function(){
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

GraviticBolt.prototype.hasMaxBoost = function(){
    return true;
}

GraviticBolt.prototype.getMaxBoost = function(){
    return this.maxBoostLevel;
}


var GravitonBeam = function(json, ship)
{
    Weapon.call( this, json, ship);
}
GravitonBeam.prototype = Object.create( Weapon.prototype );
GravitonBeam.prototype.constructor = GravitonBeam;

var LightGravitonBeam = function(json, ship)
{
    Gravitic.call( this, json, ship);
}
LightGravitonBeam.prototype = Object.create( Gravitic.prototype );
LightGravitonBeam.prototype.constructor = LightGravitonBeam;

var GraviticCannon = function(json, ship)
{
    Gravitic.call( this, json, ship);
}
GraviticCannon.prototype = Object.create( Gravitic.prototype );
GraviticCannon.prototype.constructor = GraviticCannon;

var LightGraviticBolt = function(json, ship)
{
    Gravitic.call( this, json, ship);
}
LightGraviticBolt.prototype = Object.create( Gravitic.prototype );
LightGraviticBolt.prototype.constructor = LightGraviticBolt;

var UltraLightGraviticBolt = function(json, ship)
{
    Gravitic.call( this, json, ship);
}
UltraLightGraviticBolt.prototype = Object.create( Gravitic.prototype );
UltraLightGraviticBolt.prototype.constructor = UltraLightGraviticBolt;

var GravLance = function(json, ship)
{
    Weapon.call( this, json, ship);
}
GravLance.prototype = Object.create( Weapon.prototype );
GravLance.prototype.constructor = GravLance;

var GraviticCutter = function(json, ship)
{
    Gravitic.call( this, json, ship);
}
GraviticCutter.prototype = Object.create( Gravitic.prototype );
GraviticCutter.prototype.constructor = GraviticCutter;

GraviticCutter.prototype.initBoostableInfo = function(){
    // Needed because it can chance during initial phase
    // because of adding extra power.
    
    this.data["Weapon type"] ="Raking";
    this.data["Damage type"] ="Standard";

    switch(shipManager.power.getBoost(this)){
        case 0:
            this.data["Damage"] = '10-28';
            break;
        case 1:
            this.data["Damage"] = '13-40';
            break;
        default:
            this.data["Damage"] = '10-28';
            break;
    }            

    if(window.weaponManager.isLoaded(this)){
        this.loadingtime = 2 + shipManager.power.getBoost(this);
        this.turnsloaded = 2 + shipManager.power.getBoost(this);
        this.normalload =  2 + shipManager.power.getBoost(this);
    }
    else{
        var count = shipManager.power.getBoost(this);
        
        for(var i = 0; i < count; i++){
            shipManager.power.unsetBoost(null, this);
        }
    }
    
    return this;
}

GraviticCutter.prototype.clearBoost = function(){
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

GraviticCutter.prototype.hasMaxBoost = function(){
    return true;
}

GraviticCutter.prototype.getMaxBoost = function(){
    return this.maxBoostLevel;
}
