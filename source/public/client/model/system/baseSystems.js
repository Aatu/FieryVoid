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


var SubReactorUniversal = function(json, ship)
{
    ShipSystem.call( this, json, ship);
}
SubReactorUniversal.prototype = Object.create( ShipSystem.prototype );
SubReactorUniversal.prototype.constructor = SubReactorUniversal;


var Scanner = function Scanner(json, ship) {
    ShipSystem.call(this, json, ship);
};
Scanner.prototype = Object.create(ShipSystem.prototype);
Scanner.prototype.constructor = Scanner;
Scanner.prototype.isScanner = function () {
    return true;
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

//  this.data["Weapon type"] ="Gravitic";
//  this.data["Damage type"] ="Standard";


var CnC = function CnC(json, ship) {
    ShipSystem.call(this, json, ship);
};
CnC.prototype = Object.create(ShipSystem.prototype);
CnC.prototype.constructor = CnC;

var ProtectedCnC = function ProtectedCnC(json, ship) {
    CnC.call(this, json, ship);
};
ProtectedCnC.prototype = Object.create(CnC.prototype);
ProtectedCnC.prototype.constructor = ProtectedCnC;

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

var MagGraviticThruster = function(json, ship)
{
    Thruster.call( this, json, ship);
}
MagGraviticThruster.prototype = Object.create( Thruster.prototype );
MagGraviticThruster.prototype.constructor = MagGraviticThruster;

var Hangar = function(json, ship)
{
    ShipSystem.call( this, json, ship);
}
Hangar.prototype = Object.create( ShipSystem.prototype );
Hangar.prototype.constructor = Hangar;

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

var JumpEngine = function JumpEngine(json, ship) {
    ShipSystem.call(this, json, ship);
};
JumpEngine.prototype = Object.create(ShipSystem.prototype);
JumpEngine.prototype.constructor = JumpEngine;

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

var ConnectionStrut = function(json, ship)
{
    ShipSystem.call( this, json, ship);
}
ConnectionStrut.prototype = Object.create( ShipSystem.prototype );
ConnectionStrut.prototype.constructor = ConnectionStrut;

var AdaptiveArmorController = function AdaptiveArmorController(json, ship) {
    ShipSystem.call(this, json, ship);
};
AdaptiveArmorController.prototype = Object.create(ShipSystem.prototype);
AdaptiveArmorController.prototype.constructor = AdaptiveArmorController;

AdaptiveArmorController.prototype.getCurrClass = function () { //get current damage class for display; if none, find first!
    if (this.currClass == ''){
		var classes = Object.keys(this.availableAA);
		if (classes.length>0){
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
		if (this.currClass == classes[i]){
			currId = i+1;
			break; //loop
		}
	}
	if (currId >= classes.length) currId = 0;
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
	if ( (this.AApreallocated <= this.AApreallocated_used) //preallocated pool filled
	  && (available <= allocated) //pool for this damage type filled
	) {
		return false;
	}
	
	return true;
};
AdaptiveArmorController.prototype.canDecrease = function () { //can decrease if something was increased
	this.getCurrClass();
	if (this.currClass == '') return false; //this would mean there are no damage classes whatsover!
	if (this.currchangedAA[this.currClass]>0) return true;
	return false;
};
AdaptiveArmorController.prototype.doIncrease = function () { //increase AA usage
	this.getCurrClass();
	if (this.currClass == '') return false; //this would mean there are no damage classes whatsover!
	//if preallocated are available - use them
	if (this.AApreallocated > this.AApreallocated_used){
		this.AApreallocated_used++;
		this.allocatedAA[this.currClass]++;
		this.availableAA[this.currClass]++; //preallocated assignment increases availability as well
		if (this.currchangedAA[this.currClass]>0){
			this.currchangedAA[this.currClass]++;
		}else{
			this.currchangedAA[this.currClass] = 1;
		}
		this.AAtotal_used++;
	}else if (this.allocatedAA[this.currClass] < this.availableAA[this.currClass]) { //else use regular pool 
		this.allocatedAA[this.currClass]++;
		if (this.currchangedAA[this.currClass]>0){
			this.currchangedAA[this.currClass]++;
		}else{
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
	if (this.currchangedAA[this.currClass]>0){
		if (gamedata.turn == 1){
			if (this.AApreallocated_used > 0){
				this.AApreallocated_used--;
				this.currchangedAA[this.currClass]--;
				this.allocatedAA[this.currClass]--;
				this.availableAA[this.currClass]--;
				this.AAtotal_used--;
			}
		}else{
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
		this.data[entryName] = this.allocatedAA[currType] + '/' + this.availableAA[currType];
	}
	//fix pre-allocated data, too!
	this.data[" - preassigned"] =  this.AApreallocated_used + '/' + this.AApreallocated;
	this.data["Adaptive Armor"] =  this.AAtotal_used + '/' + this.AAtotal;
	
	//this.preallocated_used =  this.AApreallocated_used;
};
AdaptiveArmorController.prototype.canPropagate = function () { //can propagate if set to >0
	if (this.currClass == '') return false; //this would mean there are no damage classes whatsover!
	if (this.allocatedAA[this.currClass]>0) return true;
	return false;
};
AdaptiveArmorController.prototype.getCurrDmgType = function () { //returns current damage type
	return this.currClass;
};
AdaptiveArmorController.prototype.getCurrAllocated = function () { //returns setting for current damage type
	if (this.currClass == '') return 0;
	return this.allocatedAA[this.currClass];
};
AdaptiveArmorController.prototype.setCurrDmgType= function (dmgTypeToSet) { //sets indicated damage type as current (or sets empty as current)
	this.currClass = ''; //will do if desired type does not exist here, which is rare but possible
	var classes = Object.keys(this.availableAA);
	var currType = '';
	for (var i = 0; i < classes.length; i++) {
		currType = classes[i];
		if (currType == dmgTypeToSet){ //exists!
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
		for (var j = 0; j< this.currchangedAA[currType];j++) this.individualNotesTransfer.push(currType);
	}
	return true;
};
AdaptiveArmorController.prototype.canIncreaseAnything = function () { //returns true if any AA points can currently be allocated
	var toReturn = false;
	var startingFrom = this.getCurrClass(); //so we know where we should stop checking
	var lookingAt = startingFrom;
	do{
		if (this.canIncrease()) {
			toReturn = true;
		}else{
			lookingAt = this.nextCurrClass();
		}
	} while ( (toReturn!=true) && (lookingAt != startingFrom) );
	return toReturn;
};




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
	if(this.currentlyDisplayedSystem == -1){ //not searched yet!
		this.getNextSystem();//currentlyDisplayedSystem will be updated inside
	}//else searched and either found or not found and no point in further searches
	return this.currentlyDisplayedSystem;
}
SelfRepair.prototype.getNextSystem = function () { //gets system ID of next damaged system
	if (this.currentlyDisplayedSystem != -2){ //-2 means there's no point looking
		var count = this.ship.systems.length;
		var searchID = this.currentlyDisplayedSystem;
		var startID = searchID;
		if (startID < 0){ //search up to last system in this case!
			startID = count -1;
		}
		do{
			searchID++;
			if (searchID>=count) searchID = 0;
			var checkedSystem =  this.ship.systems[searchID];
			if (checkedSystem.repairPriority ==0) continue; //this system cannot be repaired
			//belongs here if is damaged or is on modified priorities list
			if (searchID in this.priorityChanges){ //already modified
				this.currentlyDisplayedSystem = searchID;
				break;
			}else{
				//if Structure - skip if destroyed (can't be repaired anyway)
				if ((checkedSystem.name == 'structure')&&(shipManager.systems.isDestroyed(checkedSystem.ship, checkedSystem))) continue;
				//if fitted to destroyed Structure - skip (can't be repaired anyway)
				if ((checkedSystem.name != 'structure') && (checkedSystem.location != 0)){
					var stru = shipManager.systems.getStructureSystem(checkedSystem.ship, checkedSystem.location);
					if (stru && shipManager.systems.isDestroyed(checkedSystem.ship, stru)) continue;
				}
				//is it damaged?
				var damage = shipManager.systems.getTotalDamage(checkedSystem);
				if (damage > 0){
					this.currentlyDisplayedSystem = searchID;
					break;
				}
			}
		}while (startID!= searchID);
	}//else searched and either found or not found and no point in further searches
	if (this.currentlyDisplayedSystem == -1) this.currentlyDisplayedSystem = -2;//if nothing found - mark so further searches are skipped
	return this.currentlyDisplayedSystem;
}
SelfRepair.prototype.getCurrSystemDescription = function () { //gets description onmouseover for currently displayed system
	var description = '';
	if (this.currentlyDisplayedSystem >= 0){ //something is displayed!
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
	if (this.currentlyDisplayedSystem >= 0){ //something is displayed!
		var displayedSystem = this.ship.systems[this.currentlyDisplayedSystem];
		if ( (displayedSystem.iconPath != null) && (displayedSystem.iconPath != '')){
			icon = displayedSystem.iconPath;
		}else{
			icon = displayedSystem.name+'.png';
		}
		icon = './img/systemicons/'+icon;
	}	
	return icon;
}
SelfRepair.prototype.setRepairPriority = function (newPriority) { //sets priorityoverride for current system
	if (this.currentlyDisplayedSystem >= 0){ //something is displayed!
		this.priorityChanges[this.currentlyDisplayedSystem] = newPriority;
		//propagate the change to ALL other self-repair systems on ship!
		var count = this.ship.systems.length;
		var i = 0;
		for (i = 0; i < count; i++) if (i != this.ID){
			var displayedSystem = this.ship.systems[i];
			if (displayedSystem.name == 'SelfRepair'){
				displayedSystem.priorityChanges[this.currentlyDisplayedSystem] = newPriority;
			}
		}
	}
}



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



var Bulkhead = function(json, ship)
{
    ShipSystem.call( this, json, ship);
}
Bulkhead.prototype = Object.create( ShipSystem.prototype );
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
	if(boostCount > 0){//boosted!
		regeneration -= 1; //system will automatically add boostlevel to output display in this case...
	}
	this.output = regeneration;
	if (gamedata.gamephase > 1){//later phases - actually ADD power used by other systems - that's boosts that are already subtracted from power held!
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
PowerCapacitor.prototype.getRegeneration = function () {
	var regeneration = this.nominalOutput;
	var boostCount = shipManager.power.getBoost(this);	
	if(boostCount > 0){//boosted!
		regeneration += Math.round(this.nominalOutput *0.5);
	}
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
	powerRemaining = Math.min(powerRemaining,this.powerMax);
	this.individualNotesTransfer.push(powerRemaining);
	return true;
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
       var chargeHeld = 0;
       var reactorSurplus = shipManager.power.getReactorPower(this.ship, this);

       while ((chargeHeld < capacity) && (reactorSurplus > 0)){
          chargeHeld++; //increase charge
          reactorSurplus--; //note reduced surplus
          this.powerReq++; //mark as increased power usage, for further Batteries (if any)
       }
    this.individualNotesTransfer.push(chargeHeld);
    return true;
};
 //end of Plasma Battery 

//PlasmaBattery.prototype.initializationUpdate = function () {
    // Needed because it can change during initial phase  
//    var effectiveOutput = this.powerCurr;
//	var regeneration = this.getRegeneration();
//	this.data["Power regeneration"] = regeneration;
//	var boostCount = shipManager.power.getBoost(this);	
//	if(boostCount > 0){//boosted!
//		regeneration = this.boostCount; //system will automatically add boostlevel to output display in this case...
//}
//this.output = regeneration;
//	if (gamedata.gamephase > 1){//later phases - actually ADD power used by other systems - that's boosts that are already subtracted from power held!
		//ACTUALLY only Engine and Sensors can have meaningful boosts; still, check everything except obvious exceptions
//		this.ship.systems.forEach(function (systemToCheck) {
//			if ( (systemToCheck.name != 'PlasmaBattery') && (systemToCheck.name != 'reactor') ){ //checking these might end badly!
//				effectiveOutput += shipManager.power.countBoostPowerUsed(this.ship, systemToCheck);
//			}
//		}, this);
//	}
	//can be more than maximum - but cannot HOLD more than maximum after Initial phase (server end takes care of that)
//    this.powerReq =  - effectiveOutput; //NEGATIVE VALUE - this system adds power to Reactor :)
//    return this;
//};
//PlasmaBattery.prototype.getRegeneration = function () {
//	var regeneration = this.nominalOutput; //No nomial output.
//	var boostCount = shipManager.power.getBoost(this);	
//	if(boostCount > 0){//boosted!
//		regeneration += this.boostCount; //regenerates power available by amount boosted.
//	}
//	return regeneration;
//};

//PlasmaBattery.prototype.hasMaxBoost = function () {
//    return true;
//};
//PlasmaBattery.prototype.getMaxBoost = function () {
//    return this.maxBoostLevel;
//};

 //end of Plasma Battery 
