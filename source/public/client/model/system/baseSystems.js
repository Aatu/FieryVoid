"use strict";

var Reactor = function Reactor(json, ship) {
	ShipSystem.call(this, json, ship);
};
Reactor.prototype = Object.create(ShipSystem.prototype);
Reactor.prototype.constructor = Reactor;
Reactor.prototype.hasMaxBoost = function () {
	return true;
};

var MagGravReactor = function MagGravReactor(json, ship) {
	Reactor.call(this, json, ship);
};
MagGravReactor.prototype = Object.create(Reactor.prototype);
MagGravReactor.prototype.constructor = MagGravReactor;
MagGravReactor.prototype.hasMaxBoost = function () {
	return true;
};

var MagGravReactorTechnical = function MagGravReactorTechnical(json, ship) {
	Reactor.call(this, json, ship);
};
MagGravReactorTechnical.prototype = Object.create(Reactor.prototype);
MagGravReactorTechnical.prototype.constructor = MagGravReactorTechnical;
MagGravReactorTechnical.prototype.hasMaxBoost = function () {
	return true;
};

var AdvancedSingularityDrive = function AdvancedSingularityDrive(json, ship) {
	Reactor.call(this, json, ship);
};
AdvancedSingularityDrive.prototype = Object.create(Reactor.prototype);
AdvancedSingularityDrive.prototype.constructor = AdvancedSingularityDrive;
AdvancedSingularityDrive.prototype.hasMaxBoost = function () {
	return true;
};

var SubReactorUniversal = function (json, ship) {
	ShipSystem.call(this, json, ship);
}
SubReactorUniversal.prototype = Object.create(ShipSystem.prototype);
SubReactorUniversal.prototype.constructor = SubReactorUniversal;


var Scanner = function Scanner(json, ship) {
	ShipSystem.call(this, json, ship);
};
Scanner.prototype = Object.create(ShipSystem.prototype);
Scanner.prototype.constructor = Scanner;
Scanner.prototype.isScanner = function () {
	return true;
};

Scanner.prototype.hasMaxBoost = function () {
	if (this.maxBoostLevel > 0) {
		return true;
	} else {
		return false;
	}
};

var SWScanner = function SWScanner(json, ship) {
	Scanner.call(this, json, ship);
};
SWScanner.prototype = Object.create(Scanner.prototype);
SWScanner.prototype.constructor = SWScanner;
SWScanner.prototype.hasMaxBoost = function () {
	return true;
};

var AntiquatedScanner = function AntiquatedScanner(json, ship) {
	Scanner.call(this, json, ship);
};
AntiquatedScanner.prototype = Object.create(Scanner.prototype);
AntiquatedScanner.prototype.constructor = AntiquatedScanner;
AntiquatedScanner.prototype.hasMaxBoost = function () {
	return true;
};

var ElintScanner = function ElintScanner(json, ship) {
	Scanner.call(this, json, ship);
};
ElintScanner.prototype = Object.create(Scanner.prototype);
ElintScanner.prototype.constructor = ElintScanner;

var Engine = function Engine(json, ship) {
	ShipSystem.call(this, json, ship);
};
Engine.prototype = Object.create(ShipSystem.prototype);
Engine.prototype.constructor = Engine;
Engine.prototype.addInfo = function () {
	//this.data["Effiency"] = this.boostEfficiency;
	this.data["Efficiency"] = this.boostEfficiency;
};

Engine.prototype.doIndividualNotesTransfer = function () { //prepare individualNotesTransfer variable - if relevant for this particular system

	if (!this.hasOwnProperty('contraction')) return;//Method below only relates to ships which can Contract.

	var ship = this.ship;
	var activeShips = gamedata.activeship;
	var contractOnTurn = 0;

	if (gamedata.gamephase == 2) {
		if (Array.isArray(activeShips) ? activeShips.includes(ship.id) : activeShips === ship.id) {
			this.individualNotesTransfer = Array();

			for (var i in ship.movement) {
				var move = ship.movement[i];

				if (move.turn != gamedata.turn) continue;

				if (move.type == "contract") {
					contractOnTurn += move.value;//Will +1 depending on Contraction.
				}
			}
		}
	}

	//Now pass a note to amend Contraction level in backend and make stat changes.
	if (contractOnTurn != 0) {
		this.individualNotesTransfer.push(contractOnTurn); //Push change in shield strength to back end for Damage Entry creation if required e.g. over or under 0.
	}

	return true;
};


var CnC = function CnC(json, ship) {
	ShipSystem.call(this, json, ship);
};
CnC.prototype = Object.create(ShipSystem.prototype);
CnC.prototype.constructor = CnC;

CnC.prototype.initializationUpdate = function () {
	var ship = this.ship;
	if (ship.factionAge > 2 || gamedata.isTerrain(ship.shipSizeClass, ship.userid)) {
		this.data["Marine Units"] = 'N/A';
	} else {
		this.data["Marine Units"] = this.marines || 0; // Ensure marines is defined
	}
	return this;
};

var ProtectedCnC = function ProtectedCnC(json, ship) {
	CnC.call(this, json, ship);
};
ProtectedCnC.prototype = Object.create(CnC.prototype);
ProtectedCnC.prototype.constructor = ProtectedCnC;

var PakmaraCnC = function PakmaraCnC(json, ship) {
	CnC.call(this, json, ship);
};
PakmaraCnC.prototype = Object.create(CnC.prototype);
PakmaraCnC.prototype.constructor = PakmaraCnC;

var SecondaryCnC = function SecondaryCnC(json, ship) {
	ShipSystem.call(this, json, ship);
};
SecondaryCnC.prototype = Object.create(ShipSystem.prototype);
SecondaryCnC.prototype.constructor = SecondaryCnC;

var OSATCnC = function OSATCnC(json, ship) {
	ShipSystem.call(this, json, ship);
};
OSATCnC.prototype = Object.create(ShipSystem.prototype);
OSATCnC.prototype.constructor = OSATCnC;

var Thruster = function Thruster(json, ship) {
	ShipSystem.call(this, json, ship);
	this.channeled = 0;
};
Thruster.prototype = Object.create(ShipSystem.prototype);
Thruster.prototype.constructor = Thruster;

var GraviticThruster = function GraviticThruster(json, ship) {
	Thruster.call(this, json, ship);
};
GraviticThruster.prototype = Object.create(Thruster.prototype);
GraviticThruster.prototype.constructor = GraviticThruster;

var MindriderThruster = function MindriderThruster(json, ship) {
	Thruster.call(this, json, ship);
};
MindriderThruster.prototype = Object.create(Thruster.prototype);
MindriderThruster.prototype.constructor = MindriderThruster;

var MagGraviticThruster = function (json, ship) {
	Thruster.call(this, json, ship);
}
MagGraviticThruster.prototype = Object.create(Thruster.prototype);
MagGraviticThruster.prototype.constructor = MagGraviticThruster;

var Hangar = function (json, ship) {
	ShipSystem.call(this, json, ship);
}
Hangar.prototype = Object.create(ShipSystem.prototype);
Hangar.prototype.constructor = Hangar;

var MindriderHangar = function MindriderHangar(json, ship) {
	ShipSystem.call(this, json, ship);
};
MindriderHangar.prototype = Object.create(ShipSystem.prototype);
MindriderHangar.prototype.constructor = MindriderHangar;

var Catapult = function Catapult(json, ship) {
	ShipSystem.call(this, json, ship);
};
Catapult.prototype = Object.create(ShipSystem.prototype);
Catapult.prototype.constructor = Catapult;

var CargoBay = function CargoBay(json, ship) {
	ShipSystem.call(this, json, ship);
};
CargoBay.prototype = Object.create(ShipSystem.prototype);
CargoBay.prototype.constructor = CargoBay;

var Quarters = function Quarters(json, ship) {
	ShipSystem.call(this, json, ship);
};
Quarters.prototype = Object.create(ShipSystem.prototype);
Quarters.prototype.constructor = Quarters;

var DockingCollar = function DockingCollar(json, ship) {
	ShipSystem.call(this, json, ship);
};
DockingCollar.prototype = Object.create(ShipSystem.prototype);
DockingCollar.prototype.constructor = DockingCollar;

var Magazine = function Magazine(json, ship) {
	ShipSystem.call(this, json, ship);
};
Magazine.prototype = Object.create(ShipSystem.prototype);
Magazine.prototype.constructor = Magazine;

var JumpEngine = function JumpEngine(json, ship) {
	ShipSystem.call(this, json, ship);
};
JumpEngine.prototype = Object.create(ShipSystem.prototype);
JumpEngine.prototype.constructor = JumpEngine;

JumpEngine.prototype.initializationUpdate = function () {
	if (shipManager.power.isBoosted(this.ship, this)) {
		this.outputDisplay = "JUMP";
	} else {
		this.outputDisplay = this.output;
	}
	return this;
}

JumpEngine.prototype.hasMaxBoost = function () {
	return true;
};

var Structure = function Structure(json, ship) {
	ShipSystem.call(this, json, ship);
};
Structure.prototype = Object.create(ShipSystem.prototype);
Structure.prototype.constructor = Structure;

var StructureTechnical = function StructureTechnical(json, ship) {
	ShipSystem.call(this, json, ship);
};
StructureTechnical.prototype = Object.create(ShipSystem.prototype);
StructureTechnical.prototype.constructor = StructureTechnical;

var Jammer = function Jammer(json, ship) {
	ShipSystem.call(this, json, ship);
};
Jammer.prototype = Object.create(ShipSystem.prototype);
Jammer.prototype.constructor = Jammer;

var Stealth = function Stealth(json, ship) {
	ShipSystem.call(this, json, ship);
};
Stealth.prototype = Object.create(ShipSystem.prototype);
Stealth.prototype.constructor = Stealth;

Stealth.prototype.isDetectedStealth = function (ship) {
 	if (gamedata.gamephase == -1 && gamedata.turn == 1) return true;  //Do not hide in Turn 1 Deployment Phase.          
    if (this.detected) return true; //Already detected.

    // If the ship used offensive or ELINT EW, it is revealed
    const usedEW = ew.getAllEWExceptDEW(ship); //Has used any EW abilities except DEW?
    if (usedEW > 0) {
        return true; //If so, revealed.
    }
    if (shipManager.isDestroyed(ship)) return true;//It's blown up, assume revealed.  
    if (gamedata.gamephase != 3 && gamedata.gamephase != 5) return false;  //Cannot only try to detect at start of Pre-Firing/Firing Phase

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
                totalDetection *= 5;
            } else if (shipManager.hasSpecialAbility(otherShip, "ELINT")) {
                totalDetection *= 3;
                //Then add any Detect Stealth bonus here.
                var bonusDSEW = ew.getEWByType("Detect Stealth", otherShip);
                totalDetection += bonusDSEW * 2;
            } else {
                totalDetection *= 2;
            }
        } else {
            // Fighter unit — use offensive bonus
            if (otherShip.offensivebonus) totalDetection = otherShip.offensivebonus;
        }

        // Get distance to the stealth ship and check line of sight
        const distance = parseFloat(mathlib.getDistanceBetweenShipsInHex(ship, otherShip));
        var loSBlocked = false;
        var blockedLosHex = weaponManager.getBlockedHexes(); //Check if there are any hexes that block LoS
        var shipPos = shipManager.getShipPosition(ship);
        var otherShipPos = shipManager.getShipPosition(otherShip);
        loSBlocked = mathlib.isLoSBlocked(shipPos, otherShipPos, blockedLosHex); // Defaults to false (LoS NOT blocked)            

        // If within detection range, the ship is revealed
        if (totalDetection >= distance && !loSBlocked) { //In range and LoS not blocked.
        	this.detected = true;
            return true; //Just return, if one ship can see the stealthed ship then all can.
        }
    }

    //No one detected the ship
    return false;
};


var Fighteradvsensors = function Fighteradvsensors(json, ship) {
	ShipSystem.call(this, json, ship);
};
Fighteradvsensors.prototype = Object.create(ShipSystem.prototype);
Fighteradvsensors.prototype.constructor = Fighteradvsensors;

var Fighterimprsensors = function Fighterimprsensors(json, ship) {
	ShipSystem.call(this, json, ship);
};
Fighterimprsensors.prototype = Object.create(ShipSystem.prototype);
Fighterimprsensors.prototype.constructor = Fighterimprsensors;

var HkControlNode = function HkControlNode(json, ship) {
	ShipSystem.call(this, json, ship);
};
HkControlNode.prototype = Object.create(ShipSystem.prototype);
HkControlNode.prototype.constructor = HkControlNode;

var DrakhRaiderController = function DrakhRaiderController(json, ship) {
	ShipSystem.call(this, json, ship);
};
DrakhRaiderController.prototype = Object.create(ShipSystem.prototype);
DrakhRaiderController.prototype.constructor = DrakhRaiderController;
DrakhRaiderController.prototype.hasMaxBoost = function () {
	return true;
};

var NexusLCVController = function NexusLCVController(json, ship) {
	ShipSystem.call(this, json, ship);
};
NexusLCVController.prototype = Object.create(ShipSystem.prototype);
NexusLCVController.prototype.constructor = NexusLCVController;
NexusLCVController.prototype.hasMaxBoost = function () {
	return true;
};

var ConnectionStrut = function (json, ship) {
	ShipSystem.call(this, json, ship);
}
ConnectionStrut.prototype = Object.create(ShipSystem.prototype);
ConnectionStrut.prototype.constructor = ConnectionStrut;

var AdaptiveArmorController = function AdaptiveArmorController(json, ship) {
	ShipSystem.call(this, json, ship);
};
AdaptiveArmorController.prototype = Object.create(ShipSystem.prototype);
AdaptiveArmorController.prototype.constructor = AdaptiveArmorController;

AdaptiveArmorController.prototype.initializationUpdate = function () {
	var ship = this.ship;

	var deployTurn = 1;
	if (gamedata.gamephase == -2) {//Buying phase
		var currSlot = playerManager.getSlotById(gamedata.selectedSlot);
		deployTurn = currSlot.depavailable;
	} else {
		var currSlot = 0;
		for (var i in gamedata.ships) {
			var checkShip = gamedata.ships[i];
			if (ship.flightid == checkShip.id) ship = checkShip;
		}
		deployTurn = shipManager.getTurnDeployed(ship);
	}
	//If a ship deploys later in game, it DOES get a chance to pre-allocate.
	if (gamedata.turn <= deployTurn && this.pressignedReset == false) {
		this.AApreallocated_used = 0 + this.AAtotal_used; //Reset but since propagation can still be done in previous turns, so add total used. 
		this.data[" - Pre-assigned remaining"] = this.AApreallocated - this.AApreallocated_used; //Update system data window.			
		this.pressignedReset = true; //Only do this once.
	}
	return this;
}

AdaptiveArmorController.prototype.getCurrClass = function () { //get current damage class for display; if none, find first!
	if (this.currClass == '') {
		var classes = Object.keys(this.availableAA);
		if (classes.length > 0) {
			this.currClass = classes[0];
		}
	}
	return this.currClass;
};
AdaptiveArmorController.prototype.nextCurrClass = function () { //get next damage class for display
	this.getCurrClass();
	if (this.currClass == '') return ''; //this would mean there are no damage classes whatsover!
	var classes = Object.keys(this.availableAA);
	var currId = -1;
	for (var i = 0; i < classes.length; i++) {
		if (this.currClass == classes[i]) {
			currId = i + 1;
			break; //loop
		}
	}
	if (currId >= classes.length) currId = 0;
	this.currClass = classes[currId];
	return this.currClass;
};

AdaptiveArmorController.prototype.prevCurrClass = function () { // get previous damage class for display
	this.getCurrClass();
	if (this.currClass == '') return ''; // no damage classes available
	var classes = Object.keys(this.availableAA);
	var currId = -1;
	for (var i = 0; i < classes.length; i++) {
		if (this.currClass == classes[i]) {
			currId = i - 1;
			break; // exit loop
		}
	}
	if (currId < 0) currId = classes.length - 1;
	this.currClass = classes[currId];
	return this.currClass;
};

AdaptiveArmorController.prototype.canIncrease = function () { //check if can increase rating for current class; can do if preallocated points are unused or allocated points are less than available 
	//always needs to check that allocated are less than maximum and allocated total is less than total maximum
	this.getCurrClass();
	if (this.currClass == '') return false; //this would mean there are no damage classes whatsover!

	//total pool of AA points filled?
	if (this.AAtotal_used >= this.AAtotal) return false;

	//how many are allocated?
	var allocated = this.allocatedAA[this.currClass];
	//how many are allowed?
	var allowed = this.AApertype;
	if (allocated >= allowed) return false; //full allowance for this damage type filled

	//availability for this dmg type remaining - or preallocated points remaining? (eg. false if both are full)
	var available = this.availableAA[this.currClass];
	if ((this.AApreallocated <= this.AApreallocated_used) //preallocated pool filled
		&& (available <= allocated) //pool for this damage type filled
	) {
		return false;
	}

	return true;
};
AdaptiveArmorController.prototype.canDecrease = function () { //can decrease if something was increased
	this.getCurrClass();
	if (this.currClass == '') return false; //this would mean there are no damage classes whatsover!
	if (this.currchangedAA[this.currClass] > 0) return true;
	return false;
};
AdaptiveArmorController.prototype.doIncrease = function () { //increase AA usage
	this.getCurrClass();
	if (this.currClass == '') return false; //this would mean there are no damage classes whatsover!
	//if preallocated are available - use them
	if (this.AApreallocated > this.AApreallocated_used) {
		this.AApreallocated_used++;
		this.allocatedAA[this.currClass]++;
		this.availableAA[this.currClass]++; //preallocated assignment increases availability as well
		if (this.currchangedAA[this.currClass] > 0) {
			this.currchangedAA[this.currClass]++;
		} else {
			this.currchangedAA[this.currClass] = 1;
		}
		this.AAtotal_used++;
	} else if (this.allocatedAA[this.currClass] < this.availableAA[this.currClass]) { //else use regular pool 
		this.allocatedAA[this.currClass]++;
		if (this.currchangedAA[this.currClass] > 0) {
			this.currchangedAA[this.currClass]++;
		} else {
			this.currchangedAA[this.currClass] = 1;
		}
		this.AAtotal_used++;
	}
	this.refreshData();
};
AdaptiveArmorController.prototype.doDecrease = function () { //decrease AA usage
	this.getCurrClass();
	if (this.currClass == '') return false; //this would mean there are no damage classes whatsover!
	//in first turn use preallocated points, later regular pool
	var ship = this.ship;
	if (this.currchangedAA[this.currClass] > 0) {
		if (gamedata.turn == shipManager.getTurnDeployed(ship)) {
			if (this.AApreallocated_used > 0) {
				this.AApreallocated_used--;
				this.currchangedAA[this.currClass]--;
				this.allocatedAA[this.currClass]--;
				this.availableAA[this.currClass]--;
				this.AAtotal_used--;
			}
		} else {
			this.currchangedAA[this.currClass]--;
			this.allocatedAA[this.currClass]--;
			this.AAtotal_used--;
		}
	}
	this.refreshData();
};
AdaptiveArmorController.prototype.refreshData = function () { //refresh description to show correct values
	var classes = Object.keys(this.availableAA);
	var entryName = '';
	var currType = '';
	for (var i = 0; i < classes.length; i++) {
		currType = classes[i];
		//entry should exist, just change it to show current values
		entryName = ' - ' + currType;
		//this.data[entryName] = this.allocatedAA[currType] + '/' + this.availableAA[currType];
		this.data[entryName] = this.allocatedAA[currType];
	}
	//fix pre-allocated data, too!
	//this.data[" - Pre-assigned Amount"] =  this.AApreallocated_used + '/' + this.AApreallocated;
	this.data[" - Pre-assigned remaining"] = this.AApreallocated - this.AApreallocated_used;
	this.data["Total AA Assigned"] = this.AAtotal_used + '/' + this.AAtotal;

	//this.preallocated_used =  this.AApreallocated_used;
};
AdaptiveArmorController.prototype.canPropagate = function () { //can propagate if set to >0
	if (this.currClass == '') return false; //this would mean there are no damage classes whatsover!
	if (this.allocatedAA[this.currClass] > 0) return true;
	return false;
};
AdaptiveArmorController.prototype.getCurrDmgType = function () { //returns current damage type
	return this.currClass;
};
AdaptiveArmorController.prototype.getCurrAllocated = function () { //returns setting for current damage type
	if (this.currClass == '') return 0;
	return this.allocatedAA[this.currClass];
};
AdaptiveArmorController.prototype.setCurrDmgType = function (dmgTypeToSet) { //sets indicated damage type as current (or sets empty as current)
	this.currClass = ''; //will do if desired type does not exist here, which is rare but possible
	var classes = Object.keys(this.availableAA);
	var currType = '';
	for (var i = 0; i < classes.length; i++) {
		currType = classes[i];
		if (currType == dmgTypeToSet) { //exists!
			this.currClass = currType;
			return; //no need to loop further
		}
	}
};
AdaptiveArmorController.prototype.doIndividualNotesTransfer = function () { //prepare individualNotesTransfer variable - if relevant for this particular system
	this.individualNotesTransfer = Array();
	//every point is denoted as single entry with damage class name
	var dmgClasses = Object.keys(this.currchangedAA);
	var currType = '';
	for (var i = 0; i < dmgClasses.length; i++) {
		currType = dmgClasses[i];
		for (var j = 0; j < this.currchangedAA[currType]; j++) this.individualNotesTransfer.push(currType);
	}
	return true;
};
AdaptiveArmorController.prototype.canIncreaseAnything = function () { //returns true if any AA points can currently be allocated
	var toReturn = false;
	var startingFrom = this.getCurrClass(); //so we know where we should stop checking
	var lookingAt = startingFrom;
	do {
		if (this.canIncrease()) {
			toReturn = true;
		} else {
			lookingAt = this.nextCurrClass();
		}
	} while ((toReturn != true) && (lookingAt != startingFrom));
	return toReturn;
};//endof AA Controller



var HyachComputer = function HyachComputer(json, ship) {
	ShipSystem.call(this, json, ship);
};
HyachComputer.prototype = Object.create(ShipSystem.prototype);
HyachComputer.prototype.constructor = HyachComputer;

HyachComputer.prototype.initializationUpdate = function () {
	this.data["Max Per Category"] = this.BFCPpertype;

	return this;
}

HyachComputer.prototype.getCurrClass = function () { //get current FC class for display; if none, find first!
	if (this.currClass == '') {
		var classes = Object.keys(this.allocatedBFCP); //Allocated is always the same for HC, so can serve same purpose as availableAA did.
		if (classes.length > 0) {
			this.currClass = classes[0];
		}
	}
	return this.currClass;
};
HyachComputer.prototype.nextCurrClass = function () { //get next FC class for display
	this.getCurrClass();
	if (this.currClass == '') return ''; //this would mean there are no FC classes whatsover!  Should never happen.
	var classes = Object.keys(this.allocatedBFCP);
	var currId = -1;
	for (var i = 0; i < classes.length; i++) {
		if (this.currClass == classes[i]) {
			currId = i + 1;
			break; //loop
		}
	}
	if (currId >= classes.length) currId = 0;
	this.currClass = classes[currId];
	return this.currClass;
};
HyachComputer.prototype.canIncrease = function () { //check if can increase rating for current class; can do if preallocated points are unused or allocated points are less than available 
	//always needs to check that allocated are less than maximum and allocated total is less than total maximum
	this.getCurrClass();
	if (this.currClass == '') return false; //this would mean there are no FC classes whatsover! Should never happen.

	if (this.BFCPtotal_used >= (this.output)) return false; //Is the number of BFCP points used more than possible by this HC?	

	//how many are allocated?
	var allocated = this.allocatedBFCP[this.currClass];
	//how many are allowed?
	var allowed = this.BFCPpertype;
	if (allocated >= allowed) return false; //full allowance for this FC type filled	
	//availability for this FC type remaining.
	var available = this.output - this.BFCPtotal_used;
	if (available <= 0) { //Could go under 0 after damage?
		return false;
	}
	return true;
};
HyachComputer.prototype.canDecrease = function () { //can decrease if something was increased
	this.getCurrClass();
	if (this.currClass == '') return false; //this would mean there are no FC classes whatsover!  Should never happen.		

	if (this.allocatedBFCP[this.currClass] > 0) return true;
	return false;
};
HyachComputer.prototype.doIncrease = function () { //increase BFCP usage
	this.getCurrClass();
	if (this.currClass == '') return false; //this would mean there are no FC classes whatsover! Should never happen.

	if (this.allocatedBFCP[this.currClass] < this.BFCPpertype) { //else use regular pool 
		this.allocatedBFCP[this.currClass]++;

		this.BFCPtotal_used++;
	}
	this.refreshData();
};
HyachComputer.prototype.doDecrease = function () { //decrease BFCP usage
	this.getCurrClass();
	if (this.currClass == '') return false; //this would mean there are no FC classes whatsover!
	//Decrease could be in current turn, or from previous turn allocation.
	if (this.allocatedBFCP[this.currClass] > 0) {

		this.allocatedBFCP[this.currClass]--;
		this.BFCPtotal_used--;
	}
	this.refreshData();
};
HyachComputer.prototype.refreshData = function () { //refresh description to show correct values
	var classes = Object.keys(this.allocatedBFCP);
	var entryName = '';
	var currType = '';
	for (var i = 0; i < classes.length; i++) {
		currType = classes[i];
		//entry should exist, just change it to show current values
		entryName = ' - ' + currType;
		this.data[entryName] = this.allocatedBFCP[currType] + '/' + this.BFCPpertype;
	}

	this.data["Bonus Fire Control Points (BFCP)"] = this.BFCPtotal_used + '/' + this.output;

};
HyachComputer.prototype.canPropagate = function () { //can propagate if set to >0
	if (this.currClass == '') return false; //this would mean there are no FC classes whatsover!
	if (this.allocatedBFCP[this.currClass] > 0) return true;
	return false;
};
HyachComputer.prototype.getCurrFCType = function () { //returns current FC type
	return this.currClass;
};
HyachComputer.prototype.getCurrAllocated = function () { //returns setting for current FC type
	if (this.currClass == '') return 0;
	return this.allocatedBFCP[this.currClass];
};
HyachComputer.prototype.getFCAllocated = function (FCIndex) { //returns setting for current FC type

	var bonusfirecontrol = 0;
	var FCvalues = Object.values(this.allocatedBFCP);
	bonusfirecontrol = FCvalues[FCIndex];
	return bonusfirecontrol;
};
HyachComputer.prototype.setCurrFCType = function (FCType) { //sets indicated FC type as current (or sets empty as current)
	this.currClass = ''; //will do if desired type does not exist here, which is rare but possible
	var classes = Object.keys(this.allocatedBFCP);
	var currType = '';
	for (var i = 0; i < classes.length; i++) {
		currType = classes[i];
		if (currType == FCType) { //exists!
			this.currClass = currType;
			return; //no need to loop further
		}
	}
};
HyachComputer.prototype.doIndividualNotesTransfer = function () { //prepare individualNotesTransfer variable - if relevant for this particular system
	this.individualNotesTransfer = Array();
	//every point is denoted as single entry with damage class name
	var FCCategories = Object.keys(this.allocatedBFCP);
	var currType = '';
	for (var i = 0; i < FCCategories.length; i++) {
		currType = FCCategories[i];
		for (var j = 0; j < this.allocatedBFCP[currType]; j++) this.individualNotesTransfer.push(currType); //Passes a equal number of currType notes as the value of each FC Type in allocatedBFCP
	}
	return true;
};
HyachComputer.prototype.canIncreaseAnything = function () { //returns true if any BFCP points can currently be allocated
	var toReturn = false;
	var startingFrom = this.getCurrClass(); //so we know where we should stop checking
	var lookingAt = startingFrom;
	do {
		if (this.canIncrease()) {
			toReturn = true;
		} else {
			lookingAt = this.nextCurrClass();
		}
	} while ((toReturn != true) && (lookingAt != startingFrom));
	return toReturn;
};//Endof HyachComputer


var HyachSpecialists = function HyachSpecialists(json, ship) {
	ShipSystem.call(this, json, ship);
};
HyachSpecialists.prototype = Object.create(ShipSystem.prototype);
HyachSpecialists.prototype.constructor = HyachSpecialists;

HyachSpecialists.prototype.getCurrClass = function () {
	var ship = this.ship;
	if ((gamedata.turn === shipManager.getTurnDeployed(ship)) && gamedata.gamephase == -1 && this.specCurrClass == '') {
		var classes = Object.keys(this.allSpec);
		if (classes.length > 0) {
			this.specCurrClass = classes[0];
		}
	} else if (this.specCurrClass == '') {
		var classes = Object.keys(this.availableSpec);
		if (classes.length > 0) {
			this.specCurrClass = classes[0];
		}
	}
	return this.specCurrClass;
};
HyachSpecialists.prototype.nextCurrClass = function () { //get next class for display
	this.getCurrClass();
	if (this.specCurrClass == '') return ''; //this would mean there are no classes whatsover!
	var ship = this.ship;
	if (gamedata.turn === shipManager.getTurnDeployed(ship) && gamedata.gamephase == -1) {
		var classes = Object.keys(this.allSpec);
		var currId = -1;
		for (var i = 0; i < classes.length; i++) {
			if (this.specCurrClass == classes[i]) {
				currId = i + 1;
				break; //loop
			}
		}
	} else {
		var classes = Object.keys(this.availableSpec);
		var currId = -1;
		for (var i = 0; i < classes.length; i++) {
			if (this.specCurrClass == classes[i]) {
				currId = i + 1;
				break; //loop
			}
		}
	}
	if (currId >= classes.length) currId = 0;
	this.specCurrClass = classes[currId];


	return this.specCurrClass;
};

HyachSpecialists.prototype.prevCurrClass = function () { //get previous class for display, useful when selecting from 10+ Specialists!
	this.getCurrClass();
	if (this.specCurrClass == '') return ''; //this would mean there are no classes whatsover!
	var ship = this.ship;
	if (gamedata.turn === shipManager.getTurnDeployed(ship) && gamedata.gamephase == -1) {
		var classes = Object.keys(this.allSpec);
		var currId = -1;
		for (var i = 0; i < classes.length; i++) {
			if (this.specCurrClass == classes[i]) {
				currId = i - 1;
				break; //loop
			}
		}
	} else {
		var classes = Object.keys(this.availableSpec);
		var currId = -1;
		for (var i = 0; i < classes.length; i++) {
			if (this.specCurrClass == classes[i]) {
				currId = i - 1;
				break; //loop
			}
		}
	}
	if (currId < 0) currId = classes.length - 1;
	this.specCurrClass = classes[currId];


	return this.specCurrClass;
};

HyachSpecialists.prototype.canSelect = function () { //check if can increase rating for current class; can do if preallocated points are unused or allocated points are less than available 
	//always needs to check that allocated are less than maximum and allocated total is less than total maximum
	this.getCurrClass();
	if (this.specCurrClass == '') return false; //this would mean there are no Specialist classes whatsover!
	var ship = this.ship;
	if ((gamedata.gamephase !== -1) || gamedata.turn != shipManager.getTurnDeployed(ship)) return false;//Can only be selected in deployment phase on turn the ship deploys.

	var totalSpecSelected = Object.values(this.availableSpec).reduce((accumulator, currentValue) => accumulator + currentValue, 0);
	if (totalSpecSelected >= this.specTotal) return false;

	var selected = this.availableSpec[this.specCurrClass];//0 or 1			
	//how many are allowed?
	var allowed = this.specPertype;	//Always 1.		
	if (selected >= allowed) return false; //full allowance for this damage type filled			

	return true;
};

HyachSpecialists.prototype.canUnselect = function () { //can unselect Specialists on turn the ship deploys.
	this.getCurrClass();
	var ship = this.ship;
	if ((gamedata.gamephase !== -1) || gamedata.turn != shipManager.getTurnDeployed(ship)) return false;//Can only be selected in deployment phase on turn the ship deploys.
	if (this.specCurrClass == '') return false; //this would mean there are no Specialists whatsover!

	if (this.currSelectedSpec[this.specCurrClass]) return true;	//If it's filled, you can unselect.

	return false;
};

HyachSpecialists.prototype.canUse = function () { //check if can increase rating for current class; can do if preallocated points are unused or allocated points are less than available 
	//always needs to check that allocated are less than maximum and allocated total is less than total maximum
	if (gamedata.gamephase == -1) return false;
	this.getCurrClass();
	if (this.specCurrClass == '') return false; //this would mean there are no Specialist classes whatsover!

	//total pool of Specialists used?
	if (!this.availableSpec[this.specCurrClass]) return false; //Not selected, can't increase or decrease on Turn 1.
	if (this.availableSpec[this.specCurrClass] < 1) return false; //Not selected, can't increase or decrease on Turn 1.

	if (gamedata.gamephase !== 1 &&
		(this.specCurrClass == 'Computer' ||
			this.specCurrClass == 'Power' ||
			this.specCurrClass == 'Repair' ||
			this.specCurrClass == 'Sensor')) return false; //Can only use on Initial Orders	

	if ((gamedata.gamephase !== 1 && gamedata.gamephase !== 2) &&
		(this.specCurrClass == 'Engine' ||
			this.specCurrClass == 'Maneuvering' ||
			this.specCurrClass == 'Thruster')) return false; //Can only use on Initial Orders or Movement phase.	


	//Has it been allocated?
	var allocatedThisTurn = this.specAllocatedCount[this.specCurrClass];//0 or 1
	var allocatedPreviousTurns = 0;
	if (this.allocatedSpec && this.allocatedSpec[this.specCurrClass] !== undefined && this.allocatedSpec[this.specCurrClass] !== null && this.allocatedSpec[this.specCurrClass] !== "") {
		// allocatedSpec is not null, we can use it here
		var allocatedPreviousTurns = this.allocatedSpec[this.specCurrClass];//0 or 1
	}
	var allocated = allocatedThisTurn + allocatedPreviousTurns;	//Should still only ever be 0 or 1!						
	var allowed = this.specPertype;	//Always 1
	if (allocated >= allowed) return false; //full allowance for this Specialist type filled

	return true;
};

HyachSpecialists.prototype.canDecrease = function () { //can decrease if something was increased
	if (gamedata.gamephase == -1) return false;
	this.getCurrClass();
	if (this.specCurrClass == '') return false; //this would mean there are no Specialists whatsover!
	if (!this.availableSpec[this.specCurrClass]) return false; //Not selected, can't increase or decrease on Turn 1.
	if (this.availableSpec[this.specCurrClass] < 1) return false; //Not selected, can't increase or decrease on Turn 1.

	if (this.specAllocatedCount[this.specCurrClass] > 0) return true;	//If it's been increased, you can decrease.

	return false;
};


HyachSpecialists.prototype.doSelect = function () { //increase AA usage
	this.getCurrClass();
	if (this.specCurrClass == '') return false; //this would mean there are no Specialist classes whatsover!	

	this.currSelectedSpec[this.specCurrClass] = 'selected';
	this.availableSpec[this.specCurrClass] = 1;
	this.specAllocatedCount[this.specCurrClass] = 0; //Set this variable for system data window.


	this.refreshData();
};

HyachSpecialists.prototype.doUnselect = function () { //can unslect Specialists in Turn 1.
	this.getCurrClass();
	if (this.specCurrClass == '') return false; //this would mean there are no Specialist classes whatsover!

	this.currSelectedSpec[this.specCurrClass] = "";	//Empty array	
	this.availableSpec[this.specCurrClass] = 0;

	this.specAllocatedCount[this.specCurrClass] = 0; //Remove any allocations made after this Specialist was selected.

	if (this.currAllocatedSpec[this.specCurrClass]) {//If player had allocated, then de-selected before removing allocation, make sure info window doesn't still say used.'

		this.specDecreased[this.specCurrClass] = true;
		this.specIncreased[this.specCurrClass] = false;
		this.currAllocatedSpec[this.specCurrClass] = "";
	}

	this.refreshData();
};

HyachSpecialists.prototype.doUse = function () { //Mark Specialist as used.
	this.getCurrClass();
	if (this.specCurrClass == '') return false; //this would mean there are no Specialist classes whatsover!

	this.currAllocatedSpec[this.specCurrClass] = 'allocated';
	this.specAllocatedCount[this.specCurrClass] = 1;	//Just for Status Window.
	this.specTotal_used++;
	this.specIncreased[this.specCurrClass] = true;
	this.specDecreased[this.specCurrClass] = false;

	var ship = this.ship;

	switch (this.specCurrClass) {
		case 'Computer'://Make front-end changes to Computer Output
			for (var i in ship.systems) {
				var system = ship.systems[i];
				if (system instanceof HyachComputer) {
					system.output += 2;
					system.BFCPpertype += 1; //Increase amount per category
					system.refreshData();
				}
			}
			break;

		case 'Defence'://Make front-end changes to Defence
			ship.forwardDefense -= 2;
			ship.sideDefense -= 2;

			for (var i in ship.systems) {
				var system = ship.systems[i];
				if (typeof system.intercept === "number" && system.intercept > 0) {
					var newIntercept = (system.intercept + 2) * 5;
					system.data["Intercept"] = "-" + newIntercept;
				}
			}
			break;

		case 'Engine':
			var strongestSystem = null;
			var strongestValue = -1;

			for (var i in ship.systems) {
				var system = ship.systems[i];
				if (system instanceof Engine && system.output > strongestValue) {
					strongestValue = system.output;
					strongestSystem = system;
				}
			}

			if (strongestSystem && strongestValue > 0) {
				strongestSystem.baseOutput1 = strongestSystem.output; //Save for any potential reversal				
				var specialistBoost = Math.floor(strongestSystem.baseOutput1 * 0.25);
				strongestSystem.output += specialistBoost;
			}
			break;

		case 'Maneuvering':
			var strongestSystem = null;
			var strongestValue = -1;

			for (var i in ship.systems) {
				var system = ship.systems[i];
				if (system instanceof Engine && system.output > strongestValue) {
					strongestValue = system.output;
					strongestSystem = system;
				}
			}

			if (strongestSystem && strongestValue > 0) {
				strongestSystem.baseOutput2 = strongestSystem.output; //Save for any potential reversal				
				var specialistBoost = Math.floor(strongestSystem.baseOutput2 * 0.1);
				strongestSystem.output += specialistBoost;
			}

			ship.turncost = Math.round(ship.turncost * 100) / 100;
			ship.turndelaycost = Math.round(ship.turndelaycost * 100) / 100;

			if (ship.turncost == 0.5) ship.turncost = 0.25;
			if (ship.turncost == 0.66) ship.turncost = 0.33;
			if (ship.turncost == 1) ship.turncost = 0.5;
			if (ship.turncost == 1.5) ship.turncost = 0.75;

			if (ship.turndelaycost == 0.5) ship.turndelaycost = 0.25;
			if (ship.turndelaycost == 0.66) ship.turndelaycost = 0.33;
			if (ship.turndelaycost == 1) ship.turndelaycost = 0.5;
			if (ship.turndelaycost == 1.5) ship.turndelaycost = 0.75;

			break;

		case 'Power'://Make front-end changes to Power output in Initial Orders phase.
			var powerBoost = 0;
			for (var i in ship.systems) {
				var system = ship.systems[i];
				if (system instanceof Reactor) {
					if (ship.shipSizeClass >= 3) powerBoost = 12;//Capital or larger	
					if (ship.shipSizeClass == 2) powerBoost = 10;//HCV						
					if (ship.shipSizeClass <= 1) powerBoost = 8;//MCV or lower							
					system.output += powerBoost;
				}
			}
			break;

		case 'Sensor'://Make front-end changes to Scanner output in Initial Orders phase.
			for (var i in ship.systems) {
				var system = ship.systems[i];

				if (system instanceof Scanner) {
					system.output += 1;
				}
			}
			break;

		case 'Targeting':
			ship.toHitBonus += 0.6;
			break;

		case 'Thruster': //Make front-end changes to Engine efficiency in Initial Orders phase.
			for (var i in ship.systems) {
				var system = ship.systems[i];

				if (system instanceof Engine) {
					system.boostEfficiency -= 1;
				}
				if (system instanceof Thruster) {
					system.output += 50; //Increase by nominal amount, 50 allows for any amount of thrust
				}
			}
			break;

		case 'Weapon':
			for (var i in ship.systems) {
				var system = ship.systems[i];
				if (system instanceof Weapon) {
					var minDam = system.minDamage;
					var maxDam = system.maxDamage;

					if (minDam > 0) minDam += 3;
					if (maxDam > 0) maxDam += 3;
					system.data["Damage"] = "" + minDam + "-" + maxDam;
				}
			}
			break;
	}

	this.refreshData();

};

HyachSpecialists.prototype.doDecrease = function () { //decrease Specialist allocation in current phase.
	this.getCurrClass();
	if (this.specCurrClass == '') return false; //this would mean there are no Specialist classes whatsover!

	this.currAllocatedSpec[this.specCurrClass] = "";
	this.specAllocatedCount[this.specCurrClass] = 0;//Just for Status Window.
	this.specDecreased[this.specCurrClass] = true;
	this.specIncreased[this.specCurrClass] = false;

	this.specTotal_used--;
	var ship = this.ship;

	switch (this.specCurrClass) {
		case 'Computer'://Make front-end changes to Computer Output
			for (var i in ship.systems) {
				var system = ship.systems[i];

				if (system instanceof HyachComputer) {
					system.output -= 2;
					system.BFCPpertype -= 1; //Increase amount per category
					system.refreshData();
				}
			}
			break;

		case 'Defence'://Make front-end changes to Defence
			ship.forwardDefense += 2;
			ship.sideDefense += 2;

			for (var i in ship.systems) {
				var system = ship.systems[i];
				if (typeof system.intercept === "number" && system.intercept > 0) {
					var newIntercept = system.intercept * 5;
					system.data["Intercept"] = "-" + newIntercept;
				}
			}
			break;

		case 'Engine':
			var strongestSystem = null;
			var strongestValue = -1;

			for (var i in ship.systems) {
				var system = ship.systems[i];
				if (system instanceof Engine && system.output > strongestValue) {
					strongestValue = system.output;
					strongestSystem = system;
				}
			}

			if (strongestSystem && strongestValue > 0) {
				var specialistBoost = Math.floor(strongestSystem.baseOutput1 * 0.25);
				strongestSystem.output -= specialistBoost; //Revert to saved value in memory
			}
			break;

		case 'Maneuvering':
			var strongestSystem = null;
			var strongestValue = -1;

			for (var i in ship.systems) {
				var system = ship.systems[i];
				if (system instanceof Engine && system.output > strongestValue) {
					strongestValue = system.output;
					strongestSystem = system;
				}
			}

			if (strongestSystem && strongestValue > 0) {
				var specialistBoost = Math.floor(strongestSystem.baseOutput2 * 0.1);
				strongestSystem.output -= specialistBoost;
			}

			ship.turncost = Math.round(ship.turncost * 100) / 100;
			ship.turndelaycost = Math.round(ship.turndelaycost * 100) / 100;

			if (ship.turncost == 0.25) ship.turncost = 0.5;
			if (ship.turncost == 0.33) ship.turncost = 0.66;
			if (ship.turncost == 0.5) ship.turncost = 1;
			if (ship.turncost == 0.75) ship.turncost = 1.5;

			if (ship.turndelaycost == 0.25) ship.turndelaycost = 0.5;
			if (ship.turndelaycost == 0.33) ship.turndelaycost = 0.66;
			if (ship.turndelaycost == 0.5) ship.turndelaycost = 1;
			if (ship.turndelaycost == 0.75) ship.turndelaycost = 1.5;

			break;

		case 'Power'://Make front-end changes to Power output in Initial Orders phase.
			var powerBoost = 0;
			for (var i in ship.systems) {
				var system = ship.systems[i];
				if (system instanceof Reactor) {
					if (ship.shipSizeClass >= 3) powerBoost = 12;//Capital or larger	
					if (ship.shipSizeClass == 2) powerBoost = 10;//HCV						
					if (ship.shipSizeClass <= 1) powerBoost = 8;//MCV or lower							
					system.output -= powerBoost;
				}
			}
			break;

		case 'Sensor'://Make front-end changes to Scanner output in Initial Orders phase.
			for (var i in ship.systems) {
				var system = ship.systems[i];

				if (system instanceof Scanner) {
					system.output -= 1;
				}
			}
			break;

		case 'Targeting':
			ship.toHitBonus -= 0.6;
			break;

		case 'Thruster': //Make front-end changes to Engine efficiency in Initial Orders phase.
			for (var i in ship.systems) {
				var system = ship.systems[i];

				if (system instanceof Engine) {
					system.boostEfficiency += 1;
				}
				if (system instanceof Thruster) {
					system.output -= 50; //Increase by nominal amount, 50 allows for any amount of thrust
				}
			}
			break;

		case 'Weapon':
			for (var i in ship.systems) {
				var system = ship.systems[i];
				if (system instanceof Weapon) {
					var minDam = system.minDamage;
					var maxDam = system.maxDamage;

					system.data["Damage"] = "" + minDam + "-" + maxDam;
				}
			}
			break;
	}

	this.refreshData();
};


HyachSpecialists.prototype.refreshData = function () {
	var classes = Object.keys(this.availableSpec);
	var entryName = '';
	var currType = '';
	var usedList = [];
	var ship = this.ship;

	for (var i = 0; i < classes.length; i++) {
		currType = classes[i];
		entryName = ' - ' + currType;

		if (!this.specAllocatedCount[currType]) this.specAllocatedCount[currType] = 0; //Will show 1 if selected but not used, 0 if selected and used.
		this.data[entryName] = Math.max(0, this.availableSpec[currType] - this.specAllocatedCount[currType]);

		if (this.availableSpec[currType] == 0 && (gamedata.turn == shipManager.getTurnDeployed(ship)) && gamedata.gamephase == -1) { //This way it's removed form list on Turn 1 whenever it's deselected.
			delete this.data[entryName];
		}

		if (this.specAllocatedCount[currType] > 0) { //add entry showing which Specialists are being used this turn.
			usedList.push(currType);
		}
	}

	var totalSpecSelected = Object.values(this.availableSpec).reduce((accumulator, currentValue) => accumulator + currentValue, 0);
	if ((gamedata.turn == shipManager.getTurnDeployed(ship)) && gamedata.gamephase == -1) { //Show Specialists selected in Turn 1 Initial Orders only, then change to showing Specilists used.
		this.data["Specialists"] = totalSpecSelected + '/' + this.specTotal;
	} else {
		this.data["Specialists"] = this.specTotal - this.specTotal_used;
	}

	// If usedSpecialists is empty, set it to 'NONE'
	if (usedList.length > 0) {
		this.data["Specialists Used This Turn"] = usedList.join(', ');
	} else {
		delete this.data["Specialists Used This Turn"];
	}

	webglScene.customEvent('SystemDataChanged', { ship: ship, system: this });
};

HyachSpecialists.prototype.doIndividualNotesTransfer = function () {
	this.individualNotesTransfer = {}; // Change to object for better key-value pairing
	var specClasses = Object.keys(this.allSpec);

	for (var i = 0; i < specClasses.length; i++) {
		var currType = specClasses[i];

		// Initialize the array for the current spec
		this.individualNotesTransfer[currType] = [];

		// Check specSelected for 'selected' and specUsed for 'allocated'
		if (this.currSelectedSpec[currType] === 'selected') {
			this.individualNotesTransfer[currType].push(1); // Push 1 if 'selected'
		} else {
			this.individualNotesTransfer[currType].push(0); // Push 0 otherwise
		}

		if (this.currAllocatedSpec[currType] === 'allocated') {
			this.individualNotesTransfer[currType].push(2); // Push 2 if 'allocated'
		} else {
			this.individualNotesTransfer[currType].push(0); // Push 0 otherwise
		}
	}
	return true;
};

HyachSpecialists.prototype.canSelectAnything = function () { //returns true if any AA points can currently be allocated
	var toReturn = false;
	var startingFrom = this.getCurrClass(); //so we know where we should stop checking
	var lookingAt = startingFrom;
	do {
		if (this.canSelect()) {
			toReturn = true;
		} else {
			lookingAt = this.nextCurrClass();
		}
	} while ((toReturn != true) && (lookingAt != startingFrom));
	return toReturn;
};//endof HyachSpecialists


var DiffuserTendril = function DiffuserTendril(json, ship) {
	ShipSystem.call(this, json, ship);
};
DiffuserTendril.prototype = Object.create(ShipSystem.prototype);
DiffuserTendril.prototype.constructor = DiffuserTendril;
var DiffuserTendrilFtr = function DiffuserTendrilFtr(json, ship) {
	ShipSystem.call(this, json, ship);
};
DiffuserTendrilFtr.prototype = Object.create(ShipSystem.prototype);
DiffuserTendrilFtr.prototype.constructor = DiffuserTendrilFtr;
var EnergyDiffuser = function EnergyDiffuser(json, ship) {
	ShipSystem.call(this, json, ship);
};
EnergyDiffuser.prototype = Object.create(ShipSystem.prototype);
EnergyDiffuser.prototype.constructor = EnergyDiffuser;



var SelfRepair = function SelfRepair(json, ship) {
	ShipSystem.call(this, json, ship);
};
SelfRepair.prototype = Object.create(ShipSystem.prototype);
SelfRepair.prototype.constructor = SelfRepair;

SelfRepair.prototype.doIndividualNotesTransfer = function () { //prepare individualNotesTransfer variable - if relevant for this particular system
	this.individualNotesTransfer = Array();
	//every entry contains one system override, in format: systemID;overrideValue
	var overridedArray = Object.keys(this.priorityChanges);
	for (var i = 0; i < overridedArray.length; i++) {
		var currSystem = overridedArray[i];
		var currEntry = "";
		currEntry += currSystem + ';' + this.priorityChanges[currSystem];
		this.individualNotesTransfer.push(currEntry);
	}
	return true;
};
SelfRepair.prototype.setOverride = function (systemID, overrideValue) { //set override of repair priority value of a system
	this.priorityChanges[systemID] = overrideValue;
}
SelfRepair.prototype.getCurrSystem = function () { //gets system ID of currently displayed system 
	if (this.currentlyDisplayedSystem == -1) { //not searched yet!
		this.getNextSystem();//currentlyDisplayedSystem will be updated inside
	}//else searched and either found or not found and no point in further searches
	return this.currentlyDisplayedSystem;
}
SelfRepair.prototype.getNextSystem = function () { //gets system ID of next damaged system
	if (this.currentlyDisplayedSystem != -2) { //-2 means there's no point looking
		var count = this.ship.systems.length;
		var searchID = this.currentlyDisplayedSystem;
		var startID = searchID;
		if (startID < 0) { //search up to last system in this case!
			startID = count - 1;
		}
		do {
			searchID++;
			if (searchID >= count) searchID = 0;
			var checkedSystem = this.ship.systems[searchID];
			if (checkedSystem.repairPriority == 0) continue; //this system cannot be repaired
			//belongs here if is damaged or is on modified priorities list
			if (searchID in this.priorityChanges) { //already modified
				this.currentlyDisplayedSystem = searchID;
				break;
			} else {
				//if Structure - skip if destroyed (can't be repaired anyway)
				if ((checkedSystem.name == 'structure') && (shipManager.systems.isDestroyed(checkedSystem.ship, checkedSystem))) continue;
				//if fitted to destroyed Structure - skip (can't be repaired anyway)
				if ((checkedSystem.name != 'structure') && (checkedSystem.location != 0)) {
					var stru = shipManager.systems.getStructureSystem(checkedSystem.ship, checkedSystem.location);
					if (stru && shipManager.systems.isDestroyed(checkedSystem.ship, stru)) continue;
				}
				//is it damaged?
				var damage = shipManager.systems.getTotalDamage(checkedSystem);
				if (damage > 0) {
					this.currentlyDisplayedSystem = searchID;
					break;
				}
			}
		} while (startID != searchID);
	}//else searched and either found or not found and no point in further searches
	if (this.currentlyDisplayedSystem == -1) this.currentlyDisplayedSystem = -2;//if nothing found - mark so further searches are skipped
	return this.currentlyDisplayedSystem;
}
SelfRepair.prototype.getCurrSystemDescription = function () { //gets description onmouseover for currently displayed system
	var description = '';
	if (this.currentlyDisplayedSystem >= 0) { //something is displayed!
		var displayedSystem = this.ship.systems[this.currentlyDisplayedSystem];
		//first, ID name
		description += displayedSystem.displayName;
		description += ', ID ' + displayedSystem.id;
		//Priority
		var priority = displayedSystem.repairPriority;
		if (this.currentlyDisplayedSystem in this.priorityChanges) if (this.priorityChanges[this.currentlyDisplayedSystem] >= 0) priority = this.priorityChanges[this.currentlyDisplayedSystem];
		description += ', priority ' + priority;
		//current damage
		description += ', dmg ' + shipManager.systems.getTotalDamage(displayedSystem) + '/' + displayedSystem.maxhealth;
		//status (alive/destroyed)
		if (shipManager.systems.isDestroyed(displayedSystem.ship, displayedSystem)) {
			description += ' DESTROYED';
		}
	}
	return description;
}
SelfRepair.prototype.getCurrSystemIcon = function () { //gets description onmouseover for currently displayed system
	var icon = '';
	if (this.currentlyDisplayedSystem >= 0) { //something is displayed!
		var displayedSystem = this.ship.systems[this.currentlyDisplayedSystem];
		if ((displayedSystem.iconPath != null) && (displayedSystem.iconPath != '')) {
			icon = displayedSystem.iconPath;
		} else {
			icon = displayedSystem.name + '.png';
		}
		icon = './img/systemicons/' + icon;
	}
	return icon;
}
SelfRepair.prototype.setRepairPriority = function (newPriority) { //sets priorityoverride for current system
	if (this.currentlyDisplayedSystem >= 0) { //something is displayed!
		this.priorityChanges[this.currentlyDisplayedSystem] = newPriority;
		//propagate the change to ALL other self-repair systems on ship!
		var count = this.ship.systems.length;
		var i = 0;
		for (i = 0; i < count; i++) if (i != this.ID) {
			var displayedSystem = this.ship.systems[i];
			if (displayedSystem.name == 'SelfRepair') {
				displayedSystem.priorityChanges[this.currentlyDisplayedSystem] = newPriority;
			}
		}
	}
}

SelfRepair.prototype.hasMaxBoost = function () {
	if (this.maxBoostLevel > 0) {
		return true;
	} else {
		return false;
	}
};

var ThirdspaceSelfRepair = function ThirdspaceSelfRepair(json, ship) {
	SelfRepair.call(this, json, ship);
};
ThirdspaceSelfRepair.prototype = Object.create(SelfRepair.prototype);
ThirdspaceSelfRepair.prototype.constructor = ThirdspaceSelfRepair;

ThirdspaceSelfRepair.prototype.getMaxBoost = function () {
	return this.maxBoostLevel;
};

var BioDrive = function BioDrive(json, ship) {
	Engine.call(this, json, ship);
};
BioDrive.prototype = Object.create(Engine.prototype);
BioDrive.prototype.constructor = BioDrive;
var BioThruster = function BioThruster(json, ship) {
	ShipSystem.call(this, json, ship);
};
BioThruster.prototype = Object.create(ShipSystem.prototype);
BioThruster.prototype.constructor = BioThruster;


var ShadowPilot = function ShadowPilot(json, ship) {
	CnC.call(this, json, ship);
};
ShadowPilot.prototype = Object.create(CnC.prototype);
ShadowPilot.prototype.constructor = ShadowPilot;


var PhasingDrive = function PhasingDrive(json, ship) {
	JumpEngine.call(this, json, ship);
};
PhasingDrive.prototype = Object.create(JumpEngine.prototype);
PhasingDrive.prototype.constructor = PhasingDrive;



var Bulkhead = function (json, ship) {
	ShipSystem.call(this, json, ship);
}
Bulkhead.prototype = Object.create(ShipSystem.prototype);
Bulkhead.prototype.constructor = Bulkhead;



var PowerCapacitor = function PowerCapacitor(json, ship) {
	ShipSystem.call(this, json, ship);
};
PowerCapacitor.prototype = Object.create(ShipSystem.prototype);
PowerCapacitor.prototype.constructor = PowerCapacitor;
/*old version - regenerating power IN initial phase
PowerCapacitor.prototype.initializationUpdate = function () {
	// Needed because it can change during initial phase  
	var effectiveOutput = this.powerCurr;
	if (gamedata.gamephase == 1){//in Initial phase - add output to power available
		effectiveOutput += this.output;
		var boostCount = shipManager.power.getBoost(this);	
		if(boostCount > 0){//boosted!
			effectiveOutput += Math.round(this.output *0.5);
		}
	}else if (gamedata.gamephase > 1){//later phases - actually ADD power used by other systems - that's boosts that are already subtracted from power held!
		//ACTUALLY only Engine and Sensors can have meaningful boosts; still, check everything except obvious exceptions
		this.ship.systems.forEach(function (systemToCheck) {
			if ( (systemToCheck.name != 'powerCapacitor') && (systemToCheck.name != 'reactor') ){ //checking these might end badly!
				effectiveOutput += shipManager.power.countBoostPowerUsed(this.ship, systemToCheck);
			}
		}, this);
	}
	//can be more than maximum - but cannot HOLD more than maximum after Initial phase (server end takes care of that)
	this.powerReq =  - effectiveOutput; //NEGATIVE VALUE - this system adds power to Reactor :)
	return this;
};
*/
PowerCapacitor.prototype.initializationUpdate = function () {
	// Needed because it can change during initial phase  
	var effectiveOutput = this.powerCurr;
	var regeneration = this.getRegeneration();
	this.data["Power regeneration"] = regeneration;
	var boostCount = shipManager.power.getBoost(this);
	if (boostCount > 0) {//boosted!
		regeneration -= 1; //system will automatically add boostlevel to output display in this case...
	}
	this.output = regeneration;
	if (gamedata.gamephase > 1) {//later phases - actually ADD power used by other systems - that's boosts that are already subtracted from power held!
		//ACTUALLY only Engine and Sensors can have meaningful boosts; still, check everything except obvious exceptions
		this.ship.systems.forEach(function (systemToCheck) {
			if ((systemToCheck.name != 'powerCapacitor') && (systemToCheck.name != 'reactor')) { //checking these might end badly!
				effectiveOutput += shipManager.power.countBoostPowerUsed(this.ship, systemToCheck);
			}
		}, this);
	}
	//can be more than maximum - but cannot HOLD more than maximum after Initial phase (server end takes care of that)
	this.powerReq = - effectiveOutput; //NEGATIVE VALUE - this system adds power to Reactor :)
	return this;
};
PowerCapacitor.prototype.getRegeneration = function () {
	var regeneration = this.nominalOutput;
	var boostCount = shipManager.power.getBoost(this);
	if (boostCount > 0) {//boosted!
		regeneration += Math.round(this.nominalOutput * 0.5);
	}
	if(this.active) regeneration += this.nominalOutput; //Double if weapons/shields have been shutdown.
	return regeneration;
};
PowerCapacitor.prototype.hasMaxBoost = function () {
	return true;
};
PowerCapacitor.prototype.getMaxBoost = function () {
	return this.maxBoostLevel;
};
PowerCapacitor.prototype.doIndividualNotesTransfer = function () { //prepare individualNotesTransfer variable - if relevant for this particular system
	this.individualNotesTransfer = Array();
	//note power currently remaining ON REACTOR as charge held
	var powerRemaining = shipManager.power.getReactorPower(this.ship, this);
	powerRemaining = powerRemaining + this.getRegeneration();
	powerRemaining = Math.min(powerRemaining, this.powerMax);
	//this.individualNotesTransfer.push(powerRemaining);
    this.individualNotesTransfer = {
        powerRemaining: powerRemaining,
        doubled: this.active === true
    };

    return true;
};

PowerCapacitor.prototype.canActivate = function () {
	if(gamedata.gamephase == 1 && !this.active) return true;
	
	return false;
};

PowerCapacitor.prototype.canDeactivate = function () {
	if(gamedata.gamephase == 1 && this.active) return true;
	
	return false;
};

PowerCapacitor.prototype.doActivate = function () {
	var ship = this.ship;
		var flight = gamedata.getShip(ship.flightid);//Need to conver tto full ship info.
		//If you boost one Shading field in a flight, boost them all.
		if (ship) {
			for (var i in ship.systems) {
				var system = ship.systems[i]; //The fighter
				if (shipManager.systems.isDestroyed(ship, system)) continue;
					if (system.name == "eMShield" || system instanceof Weapon && system.name !== "RammingAttack") { //Is shading Field but not this one
						system.power.push({ id: null, shipid: ship.id, systemid: system.id, type: 1, turn: gamedata.turn, amount: 0 });
						system.reactivated = true; //To prevent it from immediately being powered back on.
					}				
			}
		}
		this.active = true;
		webglScene.customEvent('SystemDataChanged', { ship: flight, system: this });
};

PowerCapacitor.prototype.doDeactivate = function () {
	var ship = this.ship;
		var flight = gamedata.getShip(ship.flightid);//Need to conver tto full ship info.
		//If you boost one Shading field in a flight, boost them all.
		this.active = false;			
		if (ship) {
			for (var i in ship.systems) {
				var system = ship.systems[i]; //The fighter
				if (shipManager.systems.isDestroyed(ship, system)) continue;
					if (system.name == "eMShield" || system instanceof Weapon && system.name !== "RammingAttack") { //Is shading Field but not this one
						shipManager.power.setOnline(ship, system);
					}				
			}
		}	
		webglScene.customEvent('SystemDataChanged', { ship: flight, system: this });
};

var BSGHybrid = function BSGHybrid(json, ship) {
	ShipSystem.call(this, json, ship);
};
BSGHybrid.prototype = Object.create(ShipSystem.prototype);
BSGHybrid.prototype.constructor = BSGHybrid;


var PlasmaBattery = function PlasmaBattery(json, ship) {
	ShipSystem.call(this, json, ship);
};
PlasmaBattery.prototype = Object.create(ShipSystem.prototype);
PlasmaBattery.prototype.constructor = PlasmaBattery;


PlasmaBattery.prototype.doIndividualNotesTransfer = function () { //prepare individualNotesTransfer variable - if relevant for this particular system
	this.individualNotesTransfer = Array();
	//note power currently remaining ON REACTOR as charge held, but no more than remaining structure - and split between batteries
	var capacity = shipManager.systems.getRemainingHealth(this);
	var reactorSurplus = shipManager.power.getReactorPower(this.ship, this);
	for (var s in this.ship.systems) {
		var system = this.ship.systems[s];
		if (system.displayName == "Plasma Battery") { //no point checking other systems
			reactorSurplus -= system.powerStoredFront;
		}
	}

	while ((this.powerStoredFront < capacity) && (reactorSurplus > 0)) {
		this.powerStoredFront++; //increase charge
		reactorSurplus--; //note reduced surplus
		this.powerReq++; //mark as increased power usage, for further Batteries (if any)
	}
	this.individualNotesTransfer.push(this.powerStoredFront);
	return true;
};


var ThirdspaceShieldGenerator = function ThirdspaceShieldGenerator(json, ship) {
	ShipSystem.call(this, json, ship);
};
ThirdspaceShieldGenerator.prototype = Object.create(ShipSystem.prototype);
ThirdspaceShieldGenerator.prototype.constructor = ThirdspaceShieldGenerator;

ThirdspaceShieldGenerator.prototype.initializationUpdate = function () {
	var ship = this.ship;
	var shieldCount = 0;
	var currentShieldHealth = 0;

	for (var i = 0; i < ship.systems.length; i++) {
		var system = ship.systems[i];

		// Find total pool of shield energy
		if (system instanceof ThirdspaceShield) {
			shieldCount++;
			currentShieldHealth += system.currentHealth;
		}
	}

	var boostCount = shipManager.power.getBoost(this); //Find any boost.
	if (shieldCount > 0) boostCount = boostCount * shieldCount; //Multiply boost count by number of shields.
	var totalOutput = this.output + boostCount; // Get total output. 		

	if (gamedata.gamephase == 1) {
		this.outputDisplay = this.storedCapacity;

		if (this.storedCapacity == 0) {
			this.outputDisplay = '-'; //'0' is not shown!
		}
		this.data["Current Output "] = totalOutput;	//Update this to help show player how much they've boosted, since outputDisplay used for transferring.
		this.data["Current Shield Power "] = currentShieldHealth;
		this.data["Boosted by "] = boostCount;
	} else {
		this.outputDisplay = totalOutput;
		this.data["Current Output"] = totalOutput;
		this.data["Current Shield Power "] = currentShieldHealth;
	}

	return this;
};


ThirdspaceShieldGenerator.prototype.hasMaxBoost = function () {
	if (this.maxBoostLevel > 0) {
		return true;
	} else {
		return false;
	}
};

ThirdspaceShieldGenerator.prototype.getCurrClass = function () { //get current preset for display; if none, find first!
	if (this.presetCurrClass == '') {
		var classes = this.shieldPresets; //
		if (classes.length > 0) {
			this.presetCurrClass = classes[0];
		}
	}
	return this.presetCurrClass;
};
ThirdspaceShieldGenerator.prototype.nextCurrClass = function () { //get next preset for display
	this.getCurrClass();
	if (this.presetCurrClass == '') return ''; //this would mean there are no presets whatsover!  Should never happen.
	var classes = this.shieldPresets;
	var currId = -1;
	for (var i = 0; i < classes.length; i++) {
		if (this.presetCurrClass == classes[i]) {
			currId = i + 1;
			break; //loop
		}
	}
	if (currId >= classes.length) currId = 0;
	this.presetCurrClass = classes[currId];
	return this.presetCurrClass;
};

ThirdspaceShieldGenerator.prototype.prevCurrClass = function () { //get previous class for display.
	this.getCurrClass();
	if (this.presetCurrClass == '') return ''; //this would mean there are no presets whatsover!  Should never happen.

	var classes = this.shieldPresets;
	var currId = -1;
	for (var i = 0; i < classes.length; i++) {
		if (this.presetCurrClass == classes[i]) {
			currId = i - 1;
			break; //loop
		}
	}

	if (currId < 0) currId = classes.length - 1;
	this.presetCurrClass = classes[currId];


	return this.presetCurrClass;
};

ThirdspaceShieldGenerator.prototype.canSelect = function () {

	this.getCurrClass();
	if (this.presetCurrClass == '') return false;

	return true;
};

ThirdspaceShieldGenerator.prototype.doSelect = function () { //check if can increase rating for current class; can do if preallocated points are unused or allocated points are less than available 
	this.getCurrClass();
	if (this.presetCurrClass == 'Equalise') {
		this.doEqualise();
	} else {
		this.doPreset(this.presetCurrClass);
	}

	return;
};

ThirdspaceShieldGenerator.prototype.doEqualise = function () { //Check if there is extra power still in Generator.

	var totalShieldPool = 0;
	var shieldsAvailable = [];

	var ship = this.ship;
	for (var i = 0; i < ship.systems.length; i++) {
		var system = ship.systems[i];

		// Find total pool of shield energy
		if (system instanceof ThirdspaceShield) {
			if (shipManager.systems.isDestroyed(ship, system)) continue; // Shield is destroyed.			
			totalShieldPool += system.currentHealth; // Add each shield's current health to the overall pool of shield energy.
			system.currentHealth = 0;
			shieldsAvailable.push(system);
		}
	}

	totalShieldPool += this.storedCapacity;//Add anything currently in Generator.
	this.storedCapacity = 0;

	// Now distribute accordingly!
	if (totalShieldPool > 0 && shieldsAvailable.length > 1) { // There is energy to distribute, and shields to distribute to!
		var shieldsToCharge = shieldsAvailable.slice(); // Create a copy of the shieldsAvailable array

		while (totalShieldPool > 0 && shieldsToCharge.length > 0) {
			var noOfShields = shieldsToCharge.length;
			var amountEachShield = Math.floor(totalShieldPool / noOfShields);

			// Break the loop if the remaining energy is too small to be meaningfully distributed
			if (amountEachShield < 1) {
				break;
			}

			var remainingShieldPool = 0;

			for (var j = 0; j < shieldsToCharge.length; j++) {
				var shield = shieldsToCharge[j];
				var shieldHeadRoom = shield.maxhealth - shield.currentHealth;
				var amountToAdd = Math.min(amountEachShield, shieldHeadRoom);

				shield.currentHealth += amountToAdd;
				totalShieldPool -= amountToAdd;

				if (amountToAdd < amountEachShield) {
					remainingShieldPool += amountEachShield - amountToAdd;
				}
			}

			// Remove fully charged shields from the shieldsToCharge array
			shieldsToCharge = shieldsToCharge.filter(shield => shield.maxhealth > shield.currentHealth);


		}

		// Distribute any remaining energy (if totalShieldPool is less than the number of shields)
		if (totalShieldPool > 0) {
			for (var j = 0; j < shieldsToCharge.length; j++) {
				var shield = shieldsToCharge[j];
				var amountToAdd = Math.min(1, totalShieldPool); // Distribute 1 unit at a time
				shield.currentHealth += amountToAdd;
				totalShieldPool -= amountToAdd;
				if (totalShieldPool <= 0) break;
			}
		}
	}
};

ThirdspaceShieldGenerator.prototype.doPreset = function (presetCurrClass) { // Check if there is extra power still in Generator.
	var totalShieldPool = 0;
	var shieldsAvailable = [];
	var priorityShield = null; // Single priority shield
	var reinforceLocation = -1;

	//Note which shield we are reinforcing.	
	if (presetCurrClass == 'Forward') reinforceLocation = 'F';
	if (presetCurrClass == 'Starboard') reinforceLocation = 'R';
	if (presetCurrClass == 'Aft') reinforceLocation = 'A';
	if (presetCurrClass == 'Port') reinforceLocation = 'L';
	if (presetCurrClass == 'ForwardPort') reinforceLocation = 'FP';
	if (presetCurrClass == 'ForwardStarboard') reinforceLocation = 'FS';
	if (presetCurrClass == 'AftPort') reinforceLocation = 'AP';
	if (presetCurrClass == 'AftStarboard') reinforceLocation = 'AS';

	var ship = this.ship;
	// Find total pool of shield energy		 	
	for (var i = 0; i < ship.systems.length; i++) {
		var system = ship.systems[i];
		if (system instanceof ThirdspaceShield) {
			if (system.side == reinforceLocation && shipManager.systems.isDestroyed(ship, system)) return; // Priority Shield is destroyed, cannot reinforce!				
			if (shipManager.systems.isDestroyed(ship, system)) continue; //Non-priority Shield is destroyed.			
			totalShieldPool += system.currentHealth; // Add each shield's current health to the overall pool of shield energy.
			system.currentHealth = 0;
			shieldsAvailable.push(system);
		}
	}

	totalShieldPool += this.storedCapacity;//Add anything currently in Generator.
	this.storedCapacity = 0;

	// Now distribute accordingly!
	if (totalShieldPool > 0 && shieldsAvailable.length > 1) { // There is energy to distribute and more than one shield!
		var shieldsToCharge = shieldsAvailable.slice(); // Create a copy of the shieldsAvailable array
		var priorityShieldFound = false;

		while (totalShieldPool > 0 && shieldsToCharge.length > 0) {
			var noOfShieldsToCharge = shieldsToCharge.length;
			var totalUnits = (priorityShieldFound ? noOfShieldsToCharge : noOfShieldsToCharge + 2);
			var amountEachShield = Math.floor(totalShieldPool / totalUnits);
			var prioritisedAmount = amountEachShield * 3;
			var remainingShieldPool = 0;

			if (amountEachShield < 1 && totalShieldPool < noOfShieldsToCharge) {
				// If the remaining energy is too small to be meaningfully distributed, break the loop
				break;
			}

			for (var j = 0; j < shieldsToCharge.length; j++) {
				var shield = shieldsToCharge[j];
				var amountToAdd = amountEachShield;

				if (shield.side == reinforceLocation && !priorityShieldFound) { // This is the shield to reinforce!
					amountToAdd = prioritisedAmount;
					priorityShield = shield; // Mark which shield is a priority.
					priorityShieldFound = true;
				}

				var shieldHeadRoom = shield.maxhealth - shield.currentHealth;
				amountToAdd = Math.min(amountToAdd, shieldHeadRoom);

				shield.currentHealth += amountToAdd;
				totalShieldPool -= amountToAdd;

				if (amountToAdd < amountEachShield) {
					remainingShieldPool += amountEachShield - amountToAdd;
				}
			}

			// Remove fully charged shields from the shieldsToCharge array
			shieldsToCharge = shieldsToCharge.filter(shield => shield.maxhealth > shield.currentHealth);

		}

		// Distribute any remaining energy (if totalShieldPool is less than the number of shields)
		if (totalShieldPool > 0) {
			for (var j = 0; j < shieldsToCharge.length; j++) {
				var shield = shieldsToCharge[j];
				var amountToAdd = Math.min(1, totalShieldPool); // Distribute 1 unit at a time
				shield.currentHealth += amountToAdd;
				totalShieldPool -= amountToAdd;
				if (totalShieldPool <= 0) break;
			}
		}
	}
};

var ThoughtShieldGenerator = function ThoughtShieldGenerator(json, ship) {
	ThirdspaceShieldGenerator.call(this, json, ship);
};
ThoughtShieldGenerator.prototype = Object.create(ThirdspaceShieldGenerator.prototype);
ThoughtShieldGenerator.prototype.constructor = ThoughtShieldGenerator;

ThoughtShieldGenerator.prototype.onTurnOff = function (ship) {
	for (var i in ship.systems) {
		var system = ship.systems[i];
		if (system.name == 'ThoughtShield') {
			system.currentHealth = 0;  // Shut it down.
		}
	}
};

ThoughtShieldGenerator.prototype.onTurnOn = function (ship) {
	var CnCvalue = 1;
	var CnC = shipManager.systems.getSystemByName(ship, "cnC");
	var CnC2 = shipManager.systems.getSystemByName(ship, "SecondaryCnC");

	if (shipManager.systems.isDestroyed(ship, CnC2)) CnCvalue = 0.5;//50% if Secondary CnC is destroyed.        
	if (shipManager.systems.isDestroyed(ship, CnC)) CnCvalue = 0;//0% if CnC is destroyed.

	for (var i in ship.systems) {
		var system = ship.systems[i];

		if (system.name == 'ThoughtShield') {
			system.currentHealth = Math.round(system.baseRating * CnCvalue); //Power back up
		}
	}
};

var ShadingField = function ShadingField(json, ship) {
	ShipSystem.call(this, json, ship);
	this.defensiveType = "Shield";
};
ShadingField.prototype = Object.create(ShipSystem.prototype);
ShadingField.prototype.constructor = ShadingField;

ShadingField.prototype.initializationUpdate = function () {
	if (this.active) {
		this.outputDisplay = "SHADE";
	} else {
		this.outputDisplay = this.output;
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

ShadingField.prototype.getDefensiveHitChangeMod = function (target, shooter, weapon) {
	if (target.flight) {
		if (this.active) {
			return this.output; //No modifiers for fighters, just return basic output.			
		} else {
			return 0;
		}
	} else {
		return this.getOutput(target, this);
	}
};

ShadingField.prototype.canActivate = function () {
    var ship = this.ship;	
	if(gamedata.gamephase == -1 && !this.active && !shipManager.power.isOfflineOnTurn(ship, this, gamedata.turn)) return true;
	
	return false;
};

ShadingField.prototype.canDeactivate = function () {
	if(gamedata.gamephase == -1 && this.active) return true;
	
	return false;
};

ShadingField.prototype.doActivate = function () {
	var ship = this.ship;
	if (ship.fighter) {
		var flight = gamedata.getShip(ship.flightid);//Need to conver tto full ship info.
		//If you boost one Shading field in a flight, boost them all.
		if (flight) {
			for (var i in flight.systems) {
				var system = flight.systems[i]; //The fighter
				if (!shipManager.systems.isDestroyed(ship, flight.systems[i])) {
					for (var j in system.systems) {
						var fighterSystem = system.systems[j];	//The fighter's systems.
						if (fighterSystem.name == "ShadingField") { //Is shading Field but not this one
							if (!fighterSystem.active) { //Is not boosted.
								fighterSystem.active = true; //Set boost marker for notes.										
							}
						}

					}
				}
			}
			webglScene.customEvent('SystemDataChanged', { ship: flight, system: this });
		}
	}else{
		this.active = true;
	}
};

ShadingField.prototype.doDeactivate = function () {
	var ship = this.ship;
	if (ship.fighter) {
		var flight = gamedata.getShip(ship.flightid);//Need to conver tto full ship info.
		//If you boost one Shading field in a flight, boost them all.
		if (flight) {
			for (var i in flight.systems) {
				var system = flight.systems[i]; //The fighter
				if (!shipManager.systems.isDestroyed(ship, flight.systems[i])) {
					for (var j in system.systems) {
						var fighterSystem = system.systems[j];	//The fighter's systems.
						if (fighterSystem.name == "ShadingField") { //Is shading Field but not this one
							if (fighterSystem.active) { //Is boosted.
								fighterSystem.active = false; //Set boost marker for notes.										
							}
						}

					}
				}
			}
			webglScene.customEvent('SystemDataChanged', { ship: flight, system: this });
		}
	}else{
		this.active = false;
	}
};

ShadingField.prototype.doIndividualNotesTransfer = function () {

	if (gamedata.gamephase == -1) {
		var active = this.active; //Was shaded this turn.		
		this.individualNotesTransfer = Array();
		if (active) {
			this.individualNotesTransfer.push(1);
		}
	}
};

ShadingField.prototype.getOutput = function (ship, system) {
	if (!system) {
		console.log("ERROR: getOutput system missing");
		console.trace();
	}

	if (shipManager.systems.isDestroyed(ship, system))
		return 0;

	if (shipManager.power.isOffline(ship, system))
		return 0;

	var output = system.output;
	output = output + system.outputMod;	//Mod is negative.	
	if (this.active && !ship.flight) output = output * 2; //Boosted ships get double output.	
	output = Math.max(0, output); //output cannot be negative!

	return output;
};

ShadingField.prototype.isDetectedTorvalus = function (ship, detection = 15) {
    if (gamedata.gamephase == -1 && gamedata.turn == 1) return true;  //Do not hide in Turn 1 Deployment Phase.  
    if (shipManager.isDestroyed(ship)) return true;//It's blown up, assume revealed.        
    //var shadingField = shipManager.systems.getSystemByName(ship, "ShadingField");
    if (this.detected) return true; //Already detected.
    if (shipManager.systems.isDestroyed(ship, this)) return true; 
    if (shipManager.power.isOffline(ship, this)) return true;                

    if (gamedata.gamephase != 3 && gamedata.gamephase != 5) return false;  //Cannot only try to detect at start of Firing Phase (and Initial Phase should be handled on server via detected value).

    // Check all enemy ships to see if any can detect this ship
    for (const otherShip of gamedata.ships) {
        if (otherShip.team === ship.team) continue; // Skip friendly ships
        if (gamedata.isTerrain(otherShip.shipSizeClass, otherShip.userid)) continue; //Skip Terrain 
        if (shipManager.isDestroyed(otherShip)) continue; //Skip destroyed

        let totalDetection = detection; //Shading Field detection range is always 15.

        // Get distance to the stealth ship and check line of sight
        const distance = parseFloat(mathlib.getDistanceBetweenShipsInHex(ship, otherShip));
        var loSBlocked = false;
        var blockedLosHex = weaponManager.getBlockedHexes(); //Check if there are any hexes that block LoS
        var shipPos = shipManager.getShipPosition(ship);
        var otherShipPos = shipManager.getShipPosition(otherShip);
        loSBlocked = mathlib.isLoSBlocked(shipPos, otherShipPos, blockedLosHex); // True is LoSBlocked          

        // If within detection range, the ship is revealed
        if (distance <= totalDetection && !loSBlocked) { //In range and LoS not blocked.
            this.detected = true;
            return true; //Just return, if one ship can see the stealthed ship then all can.
        }
    }

    // No one detected the ship
    return false;
};


var FtrPetals = function FtrPetals(json, ship) {
	ShipSystem.call(this, json, ship);
};
FtrPetals.prototype = Object.create(ShipSystem.prototype);
FtrPetals.prototype.constructor = FtrPetals;

//As well as usualy places this is also called from systemFactory.js so tooltip updates on hover without having to open shipwindow - DK
FtrPetals.prototype.initializationUpdate = function () {
	if (this.active) {
		this.outputDisplay = "OPEN";
		var ship = this.ship;
		var flight = gamedata.getShip(ship.flightid);//Need to convert to full ship info.
		
		//Incredibly specific here, but it avoids alot of issues trying to pass effects from server
		if(ship.name == "VorlonHeavyFighterFlight"){
			flight.forwardDefense = 8; 
			flight.sideDefense = 10;
			flight.freethrust = 16;
			ship.armour[2] = 1; //Reduce side armour of fighters
			ship.armour[3] = 1; //Reduce side armour of fighters			
		}else if(ship.name == "VorlonAssaultFighterFlight"){
			flight.forwardDefense = 11; 
			flight.sideDefense = 13;
			flight.freethrust = 15;
			ship.armour[2] = 2; //Reduce side armour of fighters
			ship.armour[3] = 2; //Reduce side armour of fighters
		}
	} else{
		this.outputDisplay = "-";		
	}

	this.data["Power Used"] = 'None';

	return this;
}


FtrPetals.prototype.canActivate = function () {
	if(gamedata.gamephase == 1 && !this.active) return true;
	
	return false;
};

FtrPetals.prototype.canDeactivate = function () {
	if(gamedata.gamephase == 1 && this.active) return true;
	
	return false;
};

FtrPetals.prototype.doActivate = function () {
	var ship = this.ship;
	var flight = gamedata.getShip(ship.flightid);//Need to conver tto full ship info.
	//If you boost one Petals in a flight, boost them all.
	if (flight) {
		for (var i in flight.systems) {
			var system = flight.systems[i]; //The fighter	
			if (!shipManager.systems.isDestroyed(ship, flight.systems[i])) {
				for (var j in system.systems) {
					var fighterSystem = system.systems[j];	//The fighter's systems.
					if (fighterSystem.name == "FtrPetals") { //Is shading Field but not this one
						if (!fighterSystem.active) { //Is not boosted.
							fighterSystem.active = true; //Set boost marker for notes.										
						}
					}

				}
			}
		}		

		webglScene.customEvent('SystemDataChanged', { ship: flight, system: this });
	}

};

FtrPetals.prototype.doDeactivate = function () {
	var ship = this.ship;
	var flight = gamedata.getShip(ship.flightid);//Need to conver tto full ship info.
	//If you boost one Petals in a flight, boost them all.
	if (flight) {
		for (var i in flight.systems) {
			var system = flight.systems[i]; //The fighter			
			if (!shipManager.systems.isDestroyed(ship, flight.systems[i])) {
				for (var j in system.systems) {
					var fighterSystem = system.systems[j];	//The fighter's systems.
					if (fighterSystem.name == "FtrPetals") { //Is shading Field but not this one
						if (fighterSystem.active) { //Is  boosted.
							fighterSystem.active = false; //Set boost marker for notes.										
						}
					}
				}
			}
		}	

		webglScene.customEvent('SystemDataChanged', { ship: flight, system: this });
	}
};

FtrPetals.prototype.doIndividualNotesTransfer = function () {

	if (gamedata.gamephase == 1) {
		var active = this.active; //Was shaded this turn.		
		this.individualNotesTransfer = Array();
		if (active) {
			this.individualNotesTransfer.push(1);
		}
	}
};


