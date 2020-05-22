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
    this.data["Effiency"] = this.boostEfficiency;
};

//  this.data["Weapon type"] ="Gravitic";
//  this.data["Damage type"] ="Standard";


var CnC = function CnC(json, ship) {
    ShipSystem.call(this, json, ship);
};
CnC.prototype = Object.create(ShipSystem.prototype);
CnC.prototype.constructor = CnC;

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
