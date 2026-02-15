var TrekLtPhaseCannon = function TrekLtPhaseCannon(json, ship) {
    Weapon.call(this, json, ship);
};
TrekLtPhaseCannon.prototype = Object.create(Weapon.prototype);
TrekLtPhaseCannon.prototype.constructor = TrekLtPhaseCannon;

var TrekFtrPhaseCannon = function TrekFtrPhaseCannon(json, ship) {
    Weapon.call(this, json, ship);
};
TrekFtrPhaseCannon.prototype = Object.create(Weapon.prototype);
TrekFtrPhaseCannon.prototype.constructor = TrekFtrPhaseCannon;

var TrekFtrPhaser = function TrekFtrPhaser(json, ship) {
    Weapon.call(this, json, ship);
};
TrekFtrPhaser.prototype = Object.create(Weapon.prototype);
TrekFtrPhaser.prototype.constructor = TrekFtrPhaser;

var TrekPhaseCannon = function TrekPhaseCannon(json, ship) {
    Weapon.call(this, json, ship);
};
TrekPhaseCannon.prototype = Object.create(Weapon.prototype);
TrekPhaseCannon.prototype.constructor = TrekPhaseCannon;

var TrekHvyPhaseCannon = function TrekHvyPhaseCannon(json, ship) {
    Weapon.call(this, json, ship);
};
TrekHvyPhaseCannon.prototype = Object.create(Weapon.prototype);
TrekHvyPhaseCannon.prototype.constructor = TrekHvyPhaseCannon;


var TrekPhasedPulseAccelerator = function TrekPhasedPulseAccelerator(json, ship) {
    Weapon.call(this, json, ship);
};
TrekPhasedPulseAccelerator.prototype = Object.create(Weapon.prototype);
TrekPhasedPulseAccelerator.prototype.constructor = TrekPhasedPulseAccelerator;

var TrekPhasedPulseCannon = function TrekPhasedPulseCannon(json, ship) {
    Weapon.call(this, json, ship);
};
TrekPhasedPulseCannon.prototype = Object.create(Weapon.prototype);
TrekPhasedPulseCannon.prototype.constructor = TrekPhasedPulseCannon;


var TrekPhotonicTorp = function TrekPhotonicTorp(json, ship) {
    Weapon.call(this, json, ship);
};
TrekPhotonicTorp.prototype = Object.create(Weapon.prototype);
TrekPhotonicTorp.prototype.constructor = TrekPhotonicTorp;

var TrekSpatialTorp = function TrekSpatialTorp(json, ship) {
    Weapon.call(this, json, ship);
};
TrekSpatialTorp.prototype = Object.create(Weapon.prototype);
TrekSpatialTorp.prototype.constructor = TrekSpatialTorp;


var TrekWarpDrive = function TrekWarpDrive(json, ship) {
    ShipSystem.call(this, json, ship);
};
TrekWarpDrive.prototype = Object.create(ShipSystem.prototype);
TrekWarpDrive.prototype.constructor = TrekWarpDrive;

var TrekImpulseDrive = function TrekImpulseDrive(json, ship) {
    Engine.call(this, json, ship);
};
TrekImpulseDrive.prototype = Object.create(Engine.prototype);
TrekImpulseDrive.prototype.constructor = TrekImpulseDrive;

var TrekShieldProjection = function TrekShieldProjection(json, ship) {
    ShipSystem.call(this, json, ship);
    this.defensiveType = "none";
};
TrekShieldProjection.prototype = Object.create(ShipSystem.prototype);
TrekShieldProjection.prototype.constructor = TrekShieldProjection;
TrekShieldProjection.prototype.getDefensiveHitChangeMod = function (target, shooter, weapon) {
    //this is made to be a shield just to display arc visually, no actual protection
    return 0;
};

var TrekShieldProjector = function TrekShieldProjector(json, ship) {
    ShipSystem.call(this, json, ship);
    this.defensiveType = "none";
};
TrekShieldProjector.prototype = Object.create(ShipSystem.prototype);
TrekShieldProjector.prototype.constructor = TrekShieldProjector;
TrekShieldProjector.prototype.hasMaxBoost = function () {
    return true;
};
TrekShieldProjector.prototype.getMaxBoost = function () {
    return this.maxBoostLevel;
};
TrekShieldProjector.prototype.getDefensiveHitChangeMod = function (target, shooter, weapon) {
    //this is made to be a shield just to display arc visually, no actual protection
    return 0;
};

var TrekShieldFtr = function TrekShieldFtr(json, ship) {
    ShipSystem.call(this, json, ship);
};
TrekShieldFtr.prototype = Object.create(ShipSystem.prototype);
TrekShieldFtr.prototype.constructor = TrekShieldFtr;


var TrekPhotonTorp = function TrekPhotonTorp(json, ship) {
    Weapon.call(this, json, ship);
};
TrekPhotonTorp.prototype = Object.create(Weapon.prototype);
TrekPhotonTorp.prototype.constructor = TrekPhotonTorp;

var TrekPhaser = function TrekPhaser(json, ship) {
    Weapon.call(this, json, ship);
};
TrekPhaser.prototype = Object.create(Weapon.prototype);
TrekPhaser.prototype.constructor = TrekPhaser;

var TrekPhaserLance = function TrekPhaserLance(json, ship) {
    Weapon.call(this, json, ship);
};
TrekPhaserLance.prototype = Object.create(Weapon.prototype);
TrekPhaserLance.prototype.constructor = TrekPhaserLance;

var TrekLightPhaser = function TrekLightPhaser(json, ship) {
    Weapon.call(this, json, ship);
};
TrekLightPhaser.prototype = Object.create(Weapon.prototype);
TrekLightPhaser.prototype.constructor = TrekLightPhaser;

var TrekLightPhaserLance = function TrekLightPhaserLance(json, ship) {
    Weapon.call(this, json, ship);
};
TrekLightPhaserLance.prototype = Object.create(Weapon.prototype);
TrekLightPhaserLance.prototype.constructor = TrekLightPhaserLance;

var HvyPlasmaProjector = function HvyPlasmaProjector(json, ship) {
    Weapon.call(this, json, ship);
};
HvyPlasmaProjector.prototype = Object.create(Weapon.prototype);
HvyPlasmaProjector.prototype.constructor = HvyPlasmaProjector;

var LtPlasmaProjector = function LtPlasmaProjector(json, ship) {
    Weapon.call(this, json, ship);
};
LtPlasmaProjector.prototype = Object.create(Weapon.prototype);
LtPlasmaProjector.prototype.constructor = LtPlasmaProjector;

var TrekPlasmaBurst = function TrekPlasmaBurst(json, ship) {
    Weapon.call(this, json, ship);
};
TrekPlasmaBurst.prototype = Object.create(Weapon.prototype);
TrekPlasmaBurst.prototype.constructor = TrekPlasmaBurst;
TrekPlasmaBurst.prototype.clearBoost = function () {
    for (var i in system.power) {
        var power = system.power[i];
        if (power.turn != gamedata.turn) continue;

        if (power.type == 2) {
            system.power.splice(i, 1);

            return;
        }
    }
};
TrekPlasmaBurst.prototype.hasMaxBoost = function () {
    return true;
};
TrekPlasmaBurst.prototype.getMaxBoost = function () {
    return this.maxBoostLevel;
};
TrekPlasmaBurst.prototype.initBoostableInfo = function () {
    switch (shipManager.power.getBoost(this)) {
        case 0:
            this.data["Damage"] = '2-12';
            this.data["Boostlevel"] = '0';
            break;
        case 1:
            this.data["Damage"] = '4 - 24';
            this.data["Boostlevel"] = '1';
            break;

        default:
            this.data["Damage"] = '2-12';
            this.data["Boostlevel"] = '0';
            break;
    }
    return this;
};


var TrekFtrPhotonTorpedo = function TrekFtrPhotonTorpedo(json, ship) {
    FighterMissileRack.call(this, json, ship);
};
TrekFtrPhotonTorpedo.prototype = Object.create(FighterMissileRack.prototype);
TrekFtrPhotonTorpedo.prototype.constructor = TrekFtrPhotonTorpedo;

var TrekFtrPhotonTorpedoAmmo = function TrekFtrPhotonTorpedoAmmo(json, ship) {
    Ammo.call(this, json, ship);
    this.range = 10;
    this.distanceRange = 20;
    this.hitChanceMod = 0;
};
TrekFtrPhotonTorpedoAmmo.prototype = Object.create(Ammo.prototype);
TrekFtrPhotonTorpedoAmmo.prototype.constructor = TrekFtrPhotonTorpedoAmmo;



var TrekEarlyFighterDisabler = function TrekEarlyFighterDisabler(json, ship) {
    Weapon.call(this, json, ship);
};
TrekEarlyFighterDisabler.prototype = Object.create(Weapon.prototype);
TrekEarlyFighterDisabler.prototype.constructor = TrekEarlyFighterDisabler;

var TrekFighterDisabler = function TrekFighterDisabler(json, ship) {
    Weapon.call(this, json, ship);
};
TrekFighterDisabler.prototype = Object.create(Weapon.prototype);
TrekFighterDisabler.prototype.constructor = TrekFighterDisabler;


var TrekLightDisabler = function TrekLightDisabler(json, ship) {
    Weapon.call(this, json, ship);
};
TrekLightDisabler.prototype = Object.create(Weapon.prototype);
TrekLightDisabler.prototype.constructor = TrekLightDisabler;

var TrekMediumDisabler = function TrekMediumDisabler(json, ship) {
    Weapon.call(this, json, ship);
};
TrekMediumDisabler.prototype = Object.create(Weapon.prototype);
TrekMediumDisabler.prototype.constructor = TrekMediumDisabler;


var CloakingDevice = function CloakingDevice(json, ship) {
	ShipSystem.call(this, json, ship);
};
CloakingDevice.prototype = Object.create(ShipSystem.prototype);
CloakingDevice.prototype.constructor = CloakingDevice;

CloakingDevice.prototype.initializationUpdate = function () {
	if (this.active) {
		this.outputDisplay = "CLOAK";
	} else {
		this.outputDisplay = '-';
	}
	var power = this.powerReq;

    if(gamedata.gamephase == -1){
        var ship = this.ship;
        if(shipManager.power.isOfflineOnTurn(ship, this, gamedata.turn)) this.active = false;    
    }

	if(power == 0){
		this.data["Power Used"] = 'None';
	}else{
		this.data["Power Used"] = this.powerReq;		
	}	
	return this;
}

CloakingDevice.prototype.canActivate = function () {
    var ship = this.ship;
	if(gamedata.gamephase == -1 && !this.active && !shipManager.power.isOfflineOnTurn(ship, this, gamedata.turn)) return true;
	
	return false;
};

CloakingDevice.prototype.canDeactivate = function () {
	if(gamedata.gamephase == -1 && this.active) return true;
	
	return false;
};

CloakingDevice.prototype.doActivate = function () {
	this.active = true;
};

CloakingDevice.prototype.doDeactivate = function () {
	this.active = false;
};

CloakingDevice.prototype.doIndividualNotesTransfer = function () {

	if (gamedata.gamephase == -1) {
		var active = this.active; //Was shaded this turn.		
		this.individualNotesTransfer = Array();
		if (active) {
			this.individualNotesTransfer.push(1);
		}
	}
};

CloakingDevice.prototype.isDetectedTrek = function (ship) {
    if (gamedata.gamephase == -1 && gamedata.turn == 1) return true;  //Do not hide in Turn 1 Deployment Phase.  
    if (shipManager.isDestroyed(ship)) return true;//It's blown up, assume revealed.       
    if (this.detected) return true; //Already detected. 
    if (shipManager.systems.isDestroyed(ship, this)) return true; 
    if (shipManager.power.isOffline(ship, this)) return true;              

    if (gamedata.gamephase != 3 && gamedata.gamephase != 5) return false;  //Cannot only try to detect at start of Firing Phase (and Initial Phase should be handled on server via detected value).

    // Check all enemy ships to see if any can detect this ship
    for (const otherShip of gamedata.ships) {
        if (otherShip.team === ship.team) continue; // Skip friendly ships
        if (gamedata.isTerrain(otherShip.shipSizeClass, otherShip.userid)) continue; //Skip Terrain 
        if (shipManager.isDestroyed(otherShip)) continue; //Skip destroyed

        let totalDetection = 0;

        if (!otherShip.flight) {
            if (shipManager.isDisabled(otherShip)) continue; //Skip disabled ships               
            // Not a fighter — use scanner systems for detection
            const standardScanners = shipManager.systems.getSystemListByName(otherShip, "scanner");
            const elintScanners = shipManager.systems.getSystemListByName(otherShip, "elintScanner");
            const scanners = [...standardScanners, ...elintScanners];

            for (const scanner of scanners) {
                if (!shipManager.systems.isDestroyed(otherShip, scanner) && !shipManager.power.isOfflineOnTurn(otherShip, scanner, gamedata.turn)) {
                    totalDetection += scanner.output;
                }
            }

            // Apply detection multiplier based on ship type
            if (otherShip.base) {
                totalDetection = Math.floor(totalDetection * 1.5);
            } else if (shipManager.hasSpecialAbility(otherShip, "ELINT")) {
                //Then add any Detect Stealth bonus here.
                var bonusDSEW = ew.getEWByType("Detect Stealth", otherShip);
                totalDetection += bonusDSEW;
            } else {
                totalDetection = Math.floor(totalDetection * 0.5);
            }
        } else {
            // Fighter unit — use offensive bonus
            if (otherShip.offensivebonus) totalDetection = Math.ceil(otherShip.offensivebonus / 3);
        }

        // Get distance to the stealth ship and check line of sight
        const distance = parseFloat(mathlib.getDistanceBetweenShipsInHex(ship, otherShip));
        var loSBlocked = false;
        //var blockedLosHex = weaponManager.getBlockedHexes(); //Check if there are any hexes that block LoS
	    var blockedLosHex = gamedata.blockedHexes; //Are there any blocked hexes, no point checking if no.        
        var shipPos = shipManager.getShipPosition(ship);
        var otherShipPos = shipManager.getShipPosition(otherShip);
        loSBlocked = mathlib.isLoSBlocked(shipPos, otherShipPos, blockedLosHex); // Defaults to false (LoS NOT blocked)            

        // If within detection range, the ship is revealed
        if (totalDetection >= distance && !loSBlocked) { //In range and LoS not blocked.
            this.detected = true;
            return true; //Just return, if one ship can see the stealthed ship then all can.
        }
    }

    // No one detected the ship
    return false;
};
