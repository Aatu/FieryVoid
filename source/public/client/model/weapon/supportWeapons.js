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

var ShieldReinforcement = function ShieldReinforcement(json, ship) {
    Weapon.call(this, json, ship); 
};
ShieldReinforcement.prototype = Object.create(Weapon.prototype);
ShieldReinforcement.prototype.constructor = ShieldReinforcement;

ShieldReinforcement.prototype.initBoostableInfo = function() {
	//If fired last turn, keep same boost level.  Will zero boost if NOT fired this turn anyway.
    var count = shipManager.power.getBoost(this);
	this.reinforceAmount = count;       	
	this.outputDisplay = this.reinforceAmount;
		
	if (this.reinforceAmount == 0) {
		this.outputDisplay = '-'; //'0' is not shown!							
	}

	this.data["Capacity"] = shipManager.systems.getRemainingHealth(this);
		
	return this;
};

ShieldReinforcement.prototype.hasMaxBoost = function () {
    return true;
};

ShieldReinforcement.prototype.getMaxBoost = function () {
    return this.maxBoostLevel;
};

ShieldReinforcement.prototype.onBoostIncrease = function () { 

	var aFireOrder = this.fireOrders[0];

	if(aFireOrder != null){
		confirm.error("Cannot change Shield Reinforcement after weapon is targeted.");
		shipManager.power.unsetBoost(null, this);		
      	return;	
	}

	return;	 		
}    

ShieldReinforcement.prototype.onBoostDecrease = function () { 

	var aFireOrder = this.fireOrders[0];

	if(aFireOrder != null){
		confirm.error("Cannot change Shield Reinforcement after weapon is targeted.");
		shipManager.power.setBoost(null, this);		
      	return;	
	}
			
	return;	   	
}    

ShieldReinforcement.prototype.checkReinforcement = function (shooter, target){
            
        if (this.reinforceAmount > 0){ //Check and change reinforce amount for Mindrider Shield Reinforcement!
			if(!this.confirmReinforcement(shooter, target)){
			    confirm.error("You do not have enough capacity to reinforce allied unit's shields by that amount.");
	   			return false;	
			}	
        }else if(this.reinforceAmount == 0){
            var html = '';		
	        html += "WARNING - You have not allocated an amount to reinforce allied ship's shield.";
	        html += "<br>";
			confirm.warning(html);	
			return true;	            	            	
	    }
	    
	    return true;
}

ShieldReinforcement.prototype.confirmReinforcement = function (shooter, target) {

	var canTarget = true;
	var noOfshields = 0;//Initialise

	for (var i in target.systems) {//Check all systems for Thought Shield
		var system = target.systems[i];

		if (system instanceof ThoughtShield) {
				    noOfshields += 1;//Shield found, add it to tally.
		}
	}

	var output = shipManager.systems.getRemainingHealth(this);
	var totalCost = noOfshields * this.reinforceAmount;
	
	if(totalCost > output){	
		canTarget = false;		
	}
	
	return canTarget;	
};

ShieldReinforcement.prototype.doIndividualNotesTransfer = function () { //prepare individualNotesTransfer variable - if relevant for this particular system
	this.individualNotesTransfer = Array();
	//Now pass a note to create a damage entry that will either increase or decrease shields strength.

	var aFireOrder = this.fireOrders[0];

		if(aFireOrder != null){	//Only pass notes if there is a Fire Order.
			var shieldBoost = this.reinforceAmount; //What was level of Reinforcement at end of Initial Orders?	
			if(gamedata.gamephase == 1 && (shieldBoost != 0)){
				this.individualNotesTransfer.push(shieldBoost); //Push change in shield strength to back end for Damage Entry creation if required e.g. over or under 0.
			}
		}else{//No point being boosted if not fired.
		    var count = shipManager.power.getBoost(this);

		    for (var i = 0; i < count; i++) {
		        shipManager.power.unsetBoost(null, this);
		    }					
		}
	
		
	return true;
};
