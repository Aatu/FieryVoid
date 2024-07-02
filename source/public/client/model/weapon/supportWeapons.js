"use strict";

var AbbaiShieldProjector = function AbbaiShieldProjector(json, ship) {
    Weapon.call(this, json, ship);
    this.defensiveType = "Shield";    
};
AbbaiShieldProjector.prototype = Object.create(Weapon.prototype);
AbbaiShieldProjector.prototype.constructor = AbbaiShieldProjector;

AbbaiShieldProjector.prototype.getDefensiveHitChangeMod = function (target, shooter, weapon) {
    if (!weapon.ballistic) {
        if (shooter.flight && (mathlib.getDistanceBetweenShipsInHex(target, shooter) == 0)) return 0;
    }
    if(this.turnsloaded < 1) return 0;
    	   
    return shipManager.systems.getOutput(target, this);
};



