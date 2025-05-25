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

SoMissileRack.prototype.calculateSpecialRangePenalty = function (distance) { //Added here for KK Missile calculation only.  Not normally used.
    var distancePenalized = Math.max(0,distance - 15); //ignore first 15 hexes
    var rangePenalty = this.rangePenalty * distancePenalized;
    return rangePenalty;
};

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
AmmoMagazine.prototype.doVerifyAmmoUsageFighter = function (currFighter) { //verify whether desired ammo usage is sustainable by rounds actually present in magazine
    var toReturn = true;
    var totalAmmoRequired = 0;
    this.ammoUseArray = [];
    //loop through all weapons for current ship (appropriate to phase) - and check for ammo usage
    for (var i in currFighter.systems) {
        var currWeapon = currFighter.systems[i];
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

AmmoMissileRackS.prototype.calculateSpecialRangePenalty = function (distance) { //Added here for Orieni KK Missile calculation only.  
    var distancePenalized = Math.max(0,distance - 15); //ignore first 15 hexes
    var rangePenalty = this.rangePenalty * distancePenalized;
        
    return rangePenalty;
}; 

 //For HARM Missile
AmmoMissileRackS.prototype.calculateSpecialHitChanceMod = function (target) {
	var mod = 0;
	mod = ew.getAllOEWandCCEW(target);
	return mod; 
};

//For Intercept Missile, to allow Missile Racks to select canSelfIntercept in Firing phase.
AmmoMissileRackS.prototype.canWeaponInterceptAtAll = function (weapon) {
  var canIntercept = false;

  // Check if weapon intercept rating is greater than 0
  if (this.intercept > 0) {
    canIntercept = true;
  }else {
    // Check if any value in the interceptArray is greater than 0
    var interceptArray = Object.values(this.interceptArray);
    canIntercept = interceptArray.some(element => element > 0);
  }
  return canIntercept;
};


var AmmoMissileRackSO = function AmmoMissileRackSO(json, ship) {
    AmmoMissileRackS.call(this, json, ship);
};
AmmoMissileRackSO.prototype = Object.create(AmmoMissileRackS.prototype);
AmmoMissileRackSO.prototype.constructor = AmmoMissileRackSO;

var AmmoMissileRackO = function AmmoMissileRackO(json, ship) {
    AmmoMissileRackS.call(this, json, ship);
};
AmmoMissileRackO.prototype = Object.create(AmmoMissileRackS.prototype);
AmmoMissileRackO.prototype.constructor = AmmoMissileRackO;

var AmmoMissileRackL = function AmmoMissileRackL(json, ship) {
    AmmoMissileRackS.call(this, json, ship);
};
AmmoMissileRackL.prototype = Object.create(AmmoMissileRackS.prototype);
AmmoMissileRackL.prototype.constructor = AmmoMissileRackL;

var AmmoMissileRackLH = function AmmoMissileRackLH(json, ship) {
    AmmoMissileRackS.call(this, json, ship);
};
AmmoMissileRackLH.prototype = Object.create(AmmoMissileRackS.prototype);
AmmoMissileRackLH.prototype.constructor = AmmoMissileRackLH;

var AmmoMissileRackR = function AmmoMissileRackR(json, ship) {
    AmmoMissileRackS.call(this, json, ship);
};
AmmoMissileRackR.prototype = Object.create(AmmoMissileRackS.prototype);
AmmoMissileRackR.prototype.constructor = AmmoMissileRackR;

var AmmoMissileRackD = function AmmoMissileRackD(json, ship) {
    AmmoMissileRackS.call(this, json, ship);
};
AmmoMissileRackD.prototype = Object.create(AmmoMissileRackS.prototype);
AmmoMissileRackD.prototype.constructor = AmmoMissileRackD;

var AmmoMissileRackB = function AmmoMissileRackB(json, ship) {
    AmmoMissileRackS.call(this, json, ship);
};
AmmoMissileRackB.prototype = Object.create(AmmoMissileRackS.prototype);
AmmoMissileRackB.prototype.constructor = AmmoMissileRackB;

var AmmoMissileRackA = function AmmoMissileRackA(json, ship) {
    AmmoMissileRackS.call(this, json, ship);
};
AmmoMissileRackA.prototype = Object.create(AmmoMissileRackS.prototype);
AmmoMissileRackA.prototype.constructor = AmmoMissileRackA;

var AmmoMissileRackG = function AmmoMissileRackG(json, ship) {
    AmmoMissileRackS.call(this, json, ship);
};
AmmoMissileRackG.prototype = Object.create(AmmoMissileRackS.prototype);
AmmoMissileRackG.prototype.constructor = AmmoMissileRackG;

var AmmoBombRack = function AmmoBombRack(json, ship) {
    Ballistic.call(this, json, ship);//I don't think Bomb Rack ever needs any AmmoMissileRackS function?
};
AmmoBombRack.prototype = Object.create(Ballistic.prototype);
AmmoBombRack.prototype.constructor = AmmoBombRack;

var AmmoFighterRack = function AmmoFighterRack(json, ship) {
    Ballistic.call(this, json, ship);//I don't think AmmoFighterRack ever needs any AmmoMissileRackS function?
};
AmmoFighterRack.prototype = Object.create(Ballistic.prototype);
AmmoFighterRack.prototype.constructor = AmmoFighterRack;

/*var MultiDefenseLauncher = function  MultiDefenseLauncher(json, ship) {
    Weapon.call(this, json, ship);
};
MultiDefenseLauncher.prototype = Object.create(Weapon.prototype);
MultiDefenseLauncher.prototype.constructor =  MultiDefenseLauncher;
*/


var AmmoMissileRackF = function  AmmoMissileRackF(json, ship) {
    AmmoMissileRackS.call(this, json, ship);
};
AmmoMissileRackF.prototype = Object.create(AmmoMissileRackS.prototype);
AmmoMissileRackF.prototype.constructor =  AmmoMissileRackF;

//For Intercept Missile, to allow Missile Racks to select canSelfIntercept in Firing phase.
AmmoMissileRackF.prototype.canWeaponInterceptAtAll = function (weapon) {
  var canIntercept = false;

		if (weapon.fireControl[1] == null) { //To stop F-Racks being able to manually intercept after firing Long Range shot.  Technically other modes coul have null FC1, but by the time you get to Firing Phase, F-Rack will default to Basic ammo (which should NOT have null FC unless fired LR last turn).
		  return false;
		}     
  // Check if weapon intercept rating is greater than 0
  if (this.intercept > 0) {
    canIntercept = true;
  }else {
    // Check if any value in the interceptArray is greater than 0
    var interceptArray = Object.values(this.interceptArray);
    canIntercept = interceptArray.some(element => element > 0);
  }
  return canIntercept;
};


AmmoMissileRackF.prototype.doIndividualNotesTransfer = function () { //prepare individualNotesTransfer variable
    // here: transfer information about firing in Rapid mode
    // (e.g., weapon is being fired after 1 turn of arming)
		var toReturn = false;
 		this.individualNotesTransfer = Array();	
  //Check for fire order and check Initial Orders  	
    	if ((this.fireOrders.length > 0) && (gamedata.gamephase == 1)) {		
	//Check for 1 turn loaded, as this will mean it has to be fired in Rapid Mode.	
			if (this.turnsloaded == 1) {
				this.individualNotesTransfer.push('R');
				toReturn = true;
			}
	
	// Code below is for Long Ranged shot conditions.		
		if (this.turnsloaded == 2) {
			
		    var aFireOrder = this.fireOrders[0]; 		    
		    
		    var firingShip = gamedata.getShip(aFireOrder.shooterid);

		    var targetShip = gamedata.getShip(aFireOrder.targetid); 

		    this.range -= 15; //Reduce range to normal range, check if shot is still in range.
		    var normalRange = this.checkIsInRangeFRack(firingShip, targetShip, this);
		    this.range += 15;  // Reset range to full.
								
		    if (!normalRange) { //Is a long range shot.
		        this.individualNotesTransfer.push('L');
		        toReturn = true;	
		    } else {
		        toReturn = false;	
		    }
		}	
	}	
	
    	if ((this.fireOrders.length > 0) && (gamedata.gamephase == 3)) {
    		
    		if (this.firedInRapidMode == true) {
    			this.individualNotesTransfer.push('R');
				toReturn = true;
    		}
    		if (this.firedInLongRangeMode == true) {
    			this.individualNotesTransfer.push('L');
				toReturn = true;
    		} else {
		        toReturn = false;	   			
			}
		}			
	return toReturn;
 
};

AmmoMissileRackF.prototype.checkIsInRangeFRack = function (shooter, target, weapon) { 
        var range = weapon.range;
        var distance = 0;

        if (weapon.hextarget){//For when this function called by FRack to check range of hex targeted missiles e.g. J-Missiles - DK
	        var hexpos = {
                            x: weapon.fireOrders[0].x,
                            y: weapon.fireOrders[0].y,
                        };        	
			var targetPosition = new hexagon.Offset(hexpos.x, hexpos.y);
			distance = shipManager.getShipPosition(shooter).distanceTo(targetPosition);        
		}else{
            distance = mathlib.getDistanceBetweenShipsInHex(shooter, target).toFixed(2);
        }
		
	   if (range === 0) return true;
	   			
       if(!weapon.hextarget){		
	        var stealthSystem = shipManager.systems.getSystemByName(target, "stealth");

        if (stealthSystem && distance > 5 && weapon.ballistic && target.flight) { //Fighters with stealth can't be targeted over 5 hexes.
            return false;
        }

       var jammer = shipManager.systems.getSystemByName(target, "jammer");
			if (jammer || stealthSystem)
			{
			var jammerValue = 0;
					if (jammer && (!shipManager.power.isOfflineOnTurn(target, jammer, (gamedata.turn-1) ))) {//Jammer exists and was enabled last turn.
						jammerValue = shipManager.systems.getOutput(target, jammer);
					}
				var stealthValue = 0;	
                var stealthDistance = 12; //Default for ships
                if(shooter.flight) stealthDistance = 4; //Fighters
                if(shooter.base) stealthDistance = 24; //Bases

				if (stealthSystem && (distance > stealthDistance) && target.shipSizeClass >= 0){
				    stealthValue = shipManager.systems.getOutput(target, stealthSystem);
				} 
					
				if(stealthValue > jammerValue) jammerValue = stealthValue;//larger value is used
				
				if (shipManager.hasSpecialAbility(shooter,"AdvancedSensors") || shipManager.systems.getSystemByName(shooter, "fighteradvsensors")) {
					jammerValue = 0; //negated
				} else if (shipManager.hasSpecialAbility(shooter,"ImprovedSensors") || shipManager.systems.getSystemByName(shooter, "fighterimprsensors")) {
					jammerValue = jammerValue * 0.5; //halved
				}
				range = range / (1+jammerValue);
			}
		}	
        return distance <= range;
    };	
    
var BallisticMineLauncher = function BallisticMineLauncher(json, ship) {
    Weapon.call(this, json, ship);
};
BallisticMineLauncher.prototype = Object.create(Weapon.prototype);
BallisticMineLauncher.prototype.constructor = BallisticMineLauncher;

//Needed for Ballistic Icon to display properly
BallisticMineLauncher.prototype.initializationUpdate = function() {
	var ship = this.ship;	
    if (this.fireOrders.length > 0) {					
		var aFireOrder = this.fireOrders[0]; 
		if(aFireOrder)	aFireOrder.damageclass = 'MultiModeHex';
	}			        
	return this;
};

var AbbaiMineLauncher = function AbbaiMineLauncher(json, ship) {
    Weapon.call(this, json, ship);
};
AbbaiMineLauncher.prototype = Object.create(Weapon.prototype);
AbbaiMineLauncher.prototype.constructor = AbbaiMineLauncher; 

AbbaiMineLauncher.prototype.initializationUpdate = function() {
	var ship = this.ship;	
    if (this.fireOrders.length > 0) {					
		var aFireOrder = this.fireOrders[0]; 
		if(aFireOrder)	aFireOrder.damageclass = 'MultiModeHex';
	}			        
	return this;
};
    
