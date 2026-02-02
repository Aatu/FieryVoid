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
};   

ShieldReinforcement.prototype.onBoostDecrease = function () { 

	var aFireOrder = this.fireOrders[0];

	if(aFireOrder != null){
		confirm.error("Cannot change Shield Reinforcement after weapon is targeted.");
		shipManager.power.setBoost(null, this);		
      	return;	
	}
			
	return;	   	
};    

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
};

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

var ShadeModulator = function ShadeModulator(json, ship) {
    Weapon.call(this, json, ship); 
};
ShadeModulator.prototype = Object.create(Weapon.prototype);
ShadeModulator.prototype.constructor = ShadeModulator;

ShadeModulator.prototype.initBoostableInfo = function() {

	this.outputDisplay = this.output;
	//this.noHexTargeting = true; 
		
	if (this.output == 0) {
		this.outputDisplay = '-'; //'0' is not shown properly!							
	}

	this.data["Capacity"] = this.outputDisplay;

	return this;
};

ShadeModulator.prototype.hasMaxBoost = function () {
    return true;
};

ShadeModulator.prototype.getMaxBoost = function () {
    return this.maxBoostLevel;
};

ShadeModulator.prototype.onBoostIncrease = function () { 
	this.output += 1;
 		
};    

ShadeModulator.prototype.onBoostDecrease = function () { 
	this.output -= 1;

};   

ShadeModulator.prototype.canActivate = function () { 
	if(gamedata.gamephase == 3 && (this.firingMode == 1 || this.firingMode == 3)){
		return true;	
	}
	return false; 
};  


//This creates Fire Orders for Modes 1 and 3.
ShadeModulator.prototype.doActivate = function () { 

	//Check capacity.
	if(this.firingMode == 1 && this.output <= 3 || this.firingMode == 3 && this.output <= 1){
		confirm.error("Shade Modulator does not have enough output.");		
		return; //No more shots to allocated!
	} 
		var ship = this.ship;
		var fireid = ship.id + "_" + this.id + "_" + (this.fireOrders.length + 1);
		var position = shipManager.getShipPosition(ship);			

		var fire = {
			id: fireid,
			type: 'normal',
			shooterid: ship.id,
			targetid: -1,
			weaponid: this.id,
			calledid: -1,
			turn: gamedata.turn,
			firingMode: this.firingMode,
			shots: this.defaultShots,
			x: position.q,
			y: position.r,
			damageclass: 'Blanket',
			chance: 100,
			hitmod: 0,
			notes: "Split" //Used to identify split targeting.
		};
				
		// Push to arrays / fire orders
		this.fireOrders.push(fire);

		// Ensure counters exist
		if (!this.blanketShield) this.blanketShield = 0;
		if (!this.blanketShade) this.blanketShade = 0;

		// Add tooltip note based on firing mode
		if (this.firingMode == 1) {
			this.output -= 4;
			this.blanketShield += 1;
				
		} else {
			this.output -= 2;
			this.blanketShade += 1;			
		}
		
		this.data["Blanket Shield Enhancement"] = this.blanketShield;	
		this.data["Blanket Shade Enhancement"] = this.blanketShade;				

};   

//This creates Fire Orders for Modes 2 and 4.
ShadeModulator.prototype.doMultipleFireOrders = function (shooter, target, system) {

	if(target.faction !== "Torvalus Speculators"){
		confirm.error("Shade Modulator cannot target non-Toralus ships.");		
		return; //No more shots to allocated!
	} 

	if(this.firingMode == 2 && target.flight){
		confirm.error("Shade Modulator cannot target fighters for Shield Enhancement!");		
		return; //No more shots to allocated!
	} 

	if(this.firingMode == 4){
        var shadingField = shipManager.systems.getSystemByName(target, "ShadingField");
        if(!shadingField.active){			
			confirm.error("Shade Modulator can only target Shaded units for Shade Enhancement!");		
			return; //No more shots to allocated!
		}	
	} 

	//Don't add Firing Order and give player error message if they are out of capacity.
	if(this.firingMode == 2 && this.output <= 1 || this.firingMode == 4 && this.output <= 0){
		confirm.error("Shade Modulator does not have enough output.");		
		return; //No more shots to allocated!
	} 

	if(this.firingMode == 2 || this.firingMode == 4){
		for (var s = 0; s < this.guns; s++) {
			var fireid = shooter.id + "_" + this.id + "_" + (this.fireOrders.length + 1);

			var fire = {
				id: fireid,
				type: 'normal',
				shooterid: shooter.id,
				targetid: target.id,
				weaponid: this.id,
				calledid: -1,
				turn: gamedata.turn,
				firingMode: this.firingMode,
				shots: this.defaultShots,
				x: "null",
				y: "null",
				damageclass: 'Sweeping',
				chance: 100,
				hitmod: 0,
				notes: "Split" //Used to identify split targeting.
			};
				
			if(this.firingMode == 2){
				this.output -= 2;				
			} else{
				this.output -= 1;				
			}	
							
			return fire;
		}
	}else{
		return;
	};
}		


ShadeModulator.prototype.removeMultiModeSplit = function (ship, target) {

	for (var i = this.fireOrders.length - 1; i >= 0; i--) {
        var fireOrder = this.fireOrders[i];	
		if(this.firingMode == fireOrder.firingMode){ //Find the latest fireOrder for this mode.

            this.fireOrders.splice(i, 1); // Remove the specific fire order
			if(fireOrder.firingMode == 1){
				this.output += 4;
				this.blanketShield -= 1;
				this.data["Blanket Shield Enhancement"] = this.blanketShield;															
			} else if(fireOrder.firingMode == 2){
				this.output += 2;				
			} else if(fireOrder.firingMode == 3){
				this.output += 2;
				this.blanketShade -= 1;
				this.data["Blanket Shade Enhancement"] = this.blanketShade;																
			} else{
				this.output += 1;						
			}
   		 	webglScene.customEvent('SystemDataChanged', { ship: ship, system: this });
	                    
            break; // Exit the loop after removing one matching fire order and recalculating the rest (if required).
		}	
	}  
};

ShadeModulator.prototype.removeAllMultiModeSplit = function (ship) {

	for (var i = this.fireOrders.length - 1; i >= 0; i--) {
        var fireOrder = this.fireOrders[i];	

            this.fireOrders.splice(i, 1); // Remove the specific fire order
			if(fireOrder.firingMode == 1){
				this.output += 4;	
				this.blanketShield -= 1;
				this.data["Blanket Shield Enhancement"] = this.blanketShield;														
			} else if(fireOrder.firingMode == 2){
				this.output += 2;				
			} else if(fireOrder.firingMode == 3){
				this.output += 2;
				this.blanketShade -= 1;
				this.data["Blanket Shade Enhancement"] = this.blanketShade;																
			} else{
				this.output += 1;						
			}
	}
    webglScene.customEvent('SystemDataChanged', { ship: ship, system: this });
};


var TransverseDrive = function TransverseDrive(json, ship) {
    Weapon.call(this, json, ship);
    this.defensiveType = "Blink"; 	 
};
TransverseDrive.prototype = Object.create(Weapon.prototype);
TransverseDrive.prototype.constructor = TransverseDrive;

TransverseDrive.prototype.getDefensiveHitChangeMod = function (target, shooter, weapon) {
	if(weapon.ballistic && this.fireOrders.length > 0 && gamedata.gamephase == 3){
		var fireOrder = this.fireOrders[0];

		const notes = fireOrder.notes; // e.g. "shooter: 2,-2 target: 2,0 dis: 2"

		// Default distance
		let dis = 0;

		// Try to extract the number after "dis:"
		const match = notes.match(/dis:\s*(\d+)/);

		if (match) {
			dis = parseInt(match[1], 10);
		}

		const mod = dis * 4;
		return mod;		
	}else{
		return 0;
	}

};

TransverseDrive.prototype.isPosOnSpecialArc = function (shooter, target) {
    var shooterPos = shipManager.getShipPosition(shooter);
    var heading = mathlib.getCompassHeadingOfPoint(shooterPos, target);

    // The 6 hex directions
    const hexDirections = [0, 60, 120, 180, 240, 300];
    const tolerance = 0.5; // degrees

    // Check if heading is within tolerance of any hex direction
    for (let dir of hexDirections) {
        let delta = Math.abs(heading - dir);
        // wrap around 360
        delta = delta > 180 ? 360 - delta : delta;

        if (delta <= tolerance) {
            return true;
        }
    }
    return false;
};
