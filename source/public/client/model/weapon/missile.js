"use strict";

var MissileLauncher = function MissileLauncher(json, ship) {
    Weapon.call(this, json, ship);
};

MissileLauncher.prototype = Object.create(Weapon.prototype);
MissileLauncher.prototype.constructor = MissileLauncher;

MissileLauncher.prototype.getAmmo = function (fireOrder) {
    if (!mathlib.arrayIsEmpty(this.missileArray)) {
        var mode = this.firingMode;
        if (fireOrder) mode = fireOrder.mode;

        //console.log("returning ammo: " + this.missileArray[mode].name);
        return this.missileArray[mode];
    } else {
        return null;
    }
};

MissileLauncher.prototype.changeFiringMode = function () {
    var mode = this.firingMode;

    var next = false;
    var nextround = false;
    for (var i = 0; i < this.missileCount.length; i++) {
        if (next) {
            if (this.missileCount[i] > 0) {
                this.firingMode = i;
                return;
            } else {
                if (i == this.missileCount.length - 1) {
                    if (nextround) {
                        this.firingMode = 1;
                        return;
                    }
                    nextround = true;
                    i = -1;
                }
            }
        }

        if (i == mode) next = true;
    }

    while (true) {
        var mode = this.firingMode + 1;
        if (this.firingModes[mode]) {
            this.firingMode = mode;
        } else {
            this.firingMode = 1;
        }
    }
};

var SMissileRack = function SMissileRack(json, ship) {
    MissileLauncher.call(this, json, ship);
};
SMissileRack.prototype = Object.create(MissileLauncher.prototype);
SMissileRack.prototype.constructor = SMissileRack;

var SoMissileRack = function SoMissileRack(json, ship) {
    MissileLauncher.call(this, json, ship);
};
SoMissileRack.prototype = Object.create(MissileLauncher.prototype);
SoMissileRack.prototype.constructor = SoMissileRack;

var RMissileRack = function RMissileRack(json, ship) {
    MissileLauncher.call(this, json, ship);
};
RMissileRack.prototype = Object.create(MissileLauncher.prototype);
RMissileRack.prototype.constructor = RMissileRack;

var LMissileRack = function LMissileRack(json, ship) {
    MissileLauncher.call(this, json, ship);
};
LMissileRack.prototype = Object.create(MissileLauncher.prototype);
LMissileRack.prototype.constructor = LMissileRack;

var LHMissileRack = function LHMissileRack(json, ship) {
    MissileLauncher.call(this, json, ship);
};
LHMissileRack.prototype = Object.create(MissileLauncher.prototype);
LHMissileRack.prototype.constructor = LHMissileRack;

var BMissileRack = function BMissileRack(json, ship) {
    MissileLauncher.call(this, json, ship);
};
BMissileRack.prototype = Object.create(MissileLauncher.prototype);
BMissileRack.prototype.constructor = BMissileRack;

var FighterMissileRack = function FighterMissileRack(json, ship) {
    MissileLauncher.call(this, json, ship);
};
FighterMissileRack.prototype = Object.create(MissileLauncher.prototype);
FighterMissileRack.prototype.constructor = FighterMissileRack;

var FighterTorpedoLauncher = function FighterTorpedoLauncher(json, ship) {
    FighterMissileRack.call(this, json, ship);
};
FighterTorpedoLauncher.prototype = Object.create(FighterMissileRack.prototype);
FighterTorpedoLauncher.prototype.constructor = FighterTorpedoLauncher;

var ReloadRack = function ReloadRack(json, ship) {
    ShipSystem.call(this, json, ship);
};
ReloadRack.prototype = Object.create(ShipSystem.prototype);
ReloadRack.prototype.constructor = ReloadRack;

var BombRack = function BombRack(json, ship) {
    MissileLauncher.call(this, json, ship);
};
BombRack.prototype = Object.create(MissileLauncher.prototype);
BombRack.prototype.constructor = BombRack;

var MultiMissileLauncher = function MultiMissileLauncher(json, ship) {
    Ballistic.call(this, json, ship);
};
MultiMissileLauncher.prototype = Object.create(Ballistic.prototype);
MultiMissileLauncher.prototype.constructor = MultiMissileLauncher;

var MultiBombRack = function MultiBombRack(json, ship) {
    Ballistic.call(this, json, ship);
};
MultiBombRack.prototype = Object.create(Ballistic.prototype);
MultiBombRack.prototype.constructor = MultiBombRack;

var AMissileRack = function AMissileRack(json, ship) {
    Ballistic.call(this, json, ship);
};
AMissileRack.prototype = Object.create(Ballistic.prototype);
AMissileRack.prototype.constructor = AMissileRack;


//this is actually a core (technical) system, not a weapon - but closely cooperates with weapons, hence placed here
var AmmoMagazine = function AmmoMagazine(json, ship) {
    ShipSystem.call(this, json, ship);
};
AmmoMagazine.prototype = Object.create(ShipSystem.prototype);
AmmoMagazine.prototype.constructor = AmmoMagazine;
AmmoMagazine.prototype.doIndividualNotesTransfer = function () { //prepare individualNotesTransfer variable - if relevant for this particular system
    //transfer every round used as a separate entry, by mode name
	this.individualNotesTransfer = [];
    var ammoKeysArray = Object.keys(this.ammoUseArray);
    for (var i = 0; i < ammoKeysArray.length; i++) {
		var currAmmoKey = ammoKeysArray[i];
        var currAmmoUsed = this.ammoUseArray[currAmmoKey];
		for (var j=0; j<currAmmoUsed; j++){
		    this.individualNotesTransfer.push(currAmmoKey);
        }
	}
	return true;
};
AmmoMagazine.prototype.doVerifyAmmoUsage = function (currShip) { //verify whether desired ammo usage is sustainable by rounds actually present in magazine
    var toReturn = true;
    var totalAmmoRequired = 0;
    this.ammoUseArray = [];
    //loop through all weapons for current ship (appropriate to phase) - and check for ammo usage
    for (var i in currShip.systems) {
        var currWeapon = currShip.systems[i];
        if ( (currWeapon.checkAmmoMagazine) //this confirms both that it is a weapon and that it requires ammo
            && ( ((gamedata.gamephase == 1) && (currWeapon.ballistic)) || ((gamedata.gamephase == 3) && (!currWeapon.ballistic)) ) //appropriate phase - Initial for ballistics, Firing Declaration for direct fire
        ) {
            //loop through firing orders
            for (var fo = 0; fo < currWeapon.fireOrders.length; fo++) {
                var currFireOrder = currWeapon.fireOrders[fo];
                var modeName = currWeapon.firingModes[currFireOrder.firingMode];
                //mark ammo drawn (particular ammo)
                if(this.ammoUseArray[modeName]){
                    this.ammoUseArray[modeName] += 1;
                }else{
                    this.ammoUseArray[modeName] = 1;
                }
                //mark ammo drawn (total capacity)
                var ammoSize = this.ammoSizeArray[modeName];
                totalAmmoRequired += ammoSize;
            }
        }
    }
    //check total usage
    if (totalAmmoRequired > this.remainingAmmo) toReturn = false;
    //check every individual kind of missile, too
    var ammoKeysArray = Object.keys(this.ammoUseArray);
    for (var a = 0; a < ammoKeysArray.length; a++) {
        var currAmmoKey = ammoKeysArray[a];
        var ammoPresent = this.ammoCountArray[currAmmoKey];
        var ammoRequired = this.ammoUseArray[currAmmoKey];
        if (ammoPresent < ammoRequired) toReturn = false;
    }
    return toReturn;
};


var AmmoMissileRackS = function AmmoMissileRackS(json, ship) {
    Ballistic.call(this, json, ship);
};
AmmoMissileRackS.prototype = Object.create(Ballistic.prototype);
AmmoMissileRackS.prototype.constructor = AmmoMissileRackS;

var AmmoMissileRackSO = function AmmoMissileRackSO(json, ship) {
    Ballistic.call(this, json, ship);
};
AmmoMissileRackSO.prototype = Object.create(Ballistic.prototype);
AmmoMissileRackSO.prototype.constructor = AmmoMissileRackSO;

var AmmoMissileRackL = function AmmoMissileRackL(json, ship) {
    Ballistic.call(this, json, ship);
};
AmmoMissileRackL.prototype = Object.create(Ballistic.prototype);
AmmoMissileRackL.prototype.constructor = AmmoMissileRackL;

var AmmoMissileRackLH = function AmmoMissileRackLH(json, ship) {
    Ballistic.call(this, json, ship);
};
AmmoMissileRackLH.prototype = Object.create(Ballistic.prototype);
AmmoMissileRackLH.prototype.constructor = AmmoMissileRackLH;

var AmmoMissileRackR = function AmmoMissileRackR(json, ship) {
    Ballistic.call(this, json, ship);
};
AmmoMissileRackR.prototype = Object.create(Ballistic.prototype);
AmmoMissileRackR.prototype.constructor = AmmoMissileRackR;

var AmmoMissileRackB = function AmmoMissileRackB(json, ship) {
    Ballistic.call(this, json, ship);
};
AmmoMissileRackB.prototype = Object.create(Ballistic.prototype);
AmmoMissileRackB.prototype.constructor = AmmoMissileRackB;

var AmmoBombRack = function AmmoBombRack(json, ship) {
    Ballistic.call(this, json, ship);
};
AmmoBombRack.prototype = Object.create(Ballistic.prototype);
AmmoBombRack.prototype.constructor = AmmoBombRack;

var MultiDefenseLauncher = function  MultiDefenseLauncher(json, ship) {
    Weapon.call(this, json, ship);
};
MultiDefenseLauncher.prototype = Object.create(Weapon.prototype);
MultiDefenseLauncher.prototype.constructor =  MultiDefenseLauncher;
