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


var GraviticAugmenter = function GraviticAugmenter(json, ship) {
    Weapon.call(this, json, ship); 
};
GraviticAugmenter.prototype = Object.create(Weapon.prototype);
GraviticAugmenter.prototype.constructor = GraviticAugmenter;

GraviticAugmenter.prototype.initBoostableInfo = function() {
	return this;
};

GraviticAugmenter.prototype.initializationUpdate = function() {
	//Modes 1 & 2 both fire in Initial Orders, so both are ballistic (Mode 1 self-activates
	//with no target, Mode 2 targets a friendly Warrior flight). Flagging Mode 1 ballistic is
	//also what lets weaponManager.hasFiringOrder/canOffline recognise its order (their phase
	//gate keys off system.ballistic). Mode 3 is a Pre-Firing prefire weapon instead.

	//Pre-Firing is Mode 3's phase, so default an Augmenter to Mode 3 here — BUT only if it hasn't
	//already committed an order this turn. A weapon that fired Mode 1/2 in Initial Orders must STAY
	//in the mode it fired: flipping it to 3 makes its committed order's mode disagree with the
	//weapon, which breaks the spent-locked detection (and would wrongly offer the Gravity-Shift menu
	//on an already-spent weapon). getOrderThisTurn is defined below but hoisted (prototype method).
	if (gamedata.gamephase == 5) {
		var committed = this.getOrderThisTurn();
		if (committed) {
			this.firingMode = committed.firingMode; //keep the mode it actually fired
		} else {
			this.firingMode = 3;
		}
	}

	//We set this.firingMode directly (not via setFiringMode), so the per-mode flags that live in
	//the *Array properties are NOT re-derived for the new mode. Re-run updateFiringModeData() so
	//autoFireOnly / hextarget / canTargetAllies etc. all match the current mode. Without this they
	//keep the Mode-1 defaults, and Mode 3 (Gravity Shifting) breaks in two ways:
	//  - autoFireOnly stays true, so weaponManager.selectWeapon() bails at its
	//    `if (weapon.autoFireOnly) return;` guard and the weapon can't be selected to pick a target.
	//  - hextarget stays true, so the Pre-Firing strategy treats it as a hex weapon and a click on
	//    the enemy ship never routes to weaponManager.targetShip().
	//The Augmenter has no ballisticArray/preFiresArray/turnsloadedArray, so this call leaves
	//ballistic/preFires/turnsloaded untouched (we set those explicitly below).
	this.updateFiringModeData();

	this.ballistic = (this.firingMode == 1 || this.firingMode == 2);
	this.preFires  = (this.firingMode == 3);

	//Mode 3 rotation defaults, used by the React menu + fire-order encoding.
	if (typeof this.rotationDirection === 'undefined') this.rotationDirection = 1; //1 = clockwise
	if (typeof this.rotationAmount === 'undefined') this.rotationAmount = 1;       //1 = 60deg

	return this;
};

//Returns this Augmenter's live (unrolled, this-turn) fire order, or null. Non-destructive display
//predicate — do NOT clobber turnsloaded to signal "spent" (that corrupts real load state: once
//zeroed it never restores, so removing the order strands the weapon at 0/1 and unfireable).
GraviticAugmenter.prototype.getOrderThisTurn = function () {
	//Use for-in (like weaponManager.hasFiringOrder), NOT a length-indexed loop: fireOrders can be a
	//plain object (from JSON) rather than a JS array, in which case .length is undefined and an
	//indexed loop silently finds nothing.
	for (var i in this.fireOrders) {
		var fo = this.fireOrders[i];
		if (fo.weaponid == this.id && fo.turn == gamedata.turn && !fo.rolled) return fo;
	}
	return null;
};

//"Spent & locked": once the weapon has committed its order for the turn, it should read as spent
//(dimmed) and be non-interactive in EVERY phase except the one where the order is still being
//declared/edited. We key off the ORDER's own `type` (set at creation and never rewritten):
//  - 'prefiring' => Mode 3, declared in Pre-Firing (phase 5)
//  - anything else ('ballistic') => Mode 1/2, declared in Initial Orders (phase 1)
//`type` is more robust than firingMode here because the weapon's firingMode is force-flipped in
//phase 5; the order's type is immutable. So a committed order is spent-locked whenever we're OUTSIDE
//its declaration phase (Movement/Firing for a Mode 1/2 order; Firing for a Mode 3 order).
GraviticAugmenter.prototype.isSpentLocked = function () {
	var order = this.getOrderThisTurn();
	if (!order) return false;
	var declarationPhase = (order.type === 'prefiring') ? 5 : 1;
	return gamedata.gamephase != declarationPhase;
};

//Mode 1 uses the green menu's Activate/Deactivate toggle (creates the non-targeted ballistic order).
GraviticAugmenter.prototype.canActivate = function () {
	return (gamedata.gamephase == 1 && this.firingMode == 1 && this.fireOrders.length === 0);
};

GraviticAugmenter.prototype.canDeactivate = function () {
	return (gamedata.gamephase == 1 && this.firingMode == 1 && this.fireOrders.length > 0);
};

//Mode 1: create the non-targeted ballistic "Matter Augment" order (no target ship).
GraviticAugmenter.prototype.doActivate = function () {
	if (this.firingMode != 1) return;
	if (this.fireOrders.length > 0) return; //already active

	var ship = this.ship;
	var fireid = ship.id + "_" + this.id + "_" + (this.fireOrders.length + 1);
	var position = shipManager.getShipPosition(ship);

	var fire = {
		id: fireid,
		type: 'ballistic',
		shooterid: ship.id,
		targetid: -1,
		weaponid: this.id,
		calledid: -1,
		turn: gamedata.turn,
		firingMode: this.firingMode,
		shots: this.defaultShots,
		x: position.q,
		y: position.r,
		damageclass: 'support',
		chance: 100,
		hitmod: 0,
		notes: ""
	};

	this.fireOrders.push(fire);

	//Once the order exists the green menu hides and the standard remove-fire-order button
	//takes over, so unselect the weapon (mirrors how targetShip unselects after declaring).
	if (weaponManager.isSelectedWeapon(this)) {
		weaponManager.unSelectWeapon(this.ship, this);
	}
};

//Mode 1: remove the Matter Augment order (the green menu's Deactivate).
GraviticAugmenter.prototype.doDeactivate = function () {
	if (this.firingMode != 1) return;
	weaponManager.removeFiringOrder(this.ship, this);
};

/* Mode 3 (Gravity Shifting): the React menu sets the chosen direction (1=CW, 2=ACW) and
 * amount (1=60deg, 2=120deg) on the system. We encode them into the fire order's notes as
 * "GA|<dir>|<amt>" so the server (beforeFiringOrderResolution) can read them before
 * calculateHitBase overwrites the field. Mirrors the Hypergraviton Blaster notes pattern. */
GraviticAugmenter.prototype.getRotationNotes = function () {
	var dir = (this.rotationDirection == 2) ? 2 : 1;
	var amt = (this.rotationAmount == 2) ? 2 : 1;
	return "GA|" + dir + "|" + amt;
};

/* Called by the React menu after the player picks a new rotation while a Mode 3 order is
 * already declared, so the live order's notes stay in sync with the menu selection. */
GraviticAugmenter.prototype.updateRotationNotes = function () {
	for (var i = 0; i < this.fireOrders.length; i++) {
		if (this.fireOrders[i].firingMode == 3) {
			this.fireOrders[i].notes = this.getRotationNotes();
		}
	}
};

/* Stamp the rotation notes onto the Mode 3 prefire order at creation time. weaponManager's
 * generic targetShip() builds the order; this hook lets us inject our notes payload. */
GraviticAugmenter.prototype.onFireOrderCreated = function (fire) {
	if (this.firingMode == 3 && fire) {
		fire.notes = this.getRotationNotes();
	}
	return fire;
};

