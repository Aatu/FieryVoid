"use strict";

var InterceptorMkI = function InterceptorMkI(json, ship) {
    Weapon.call(this, json, ship);
    this.defensiveType = "Interceptor";
};
InterceptorMkI.prototype = Object.create(Weapon.prototype);
InterceptorMkI.prototype.constructor = InterceptorMkI;

InterceptorMkI.prototype.getDefensiveHitChangeMod = function (target, shooter, weapon) {
    return shipManager.systems.getOutput(target, this);
};

var InterceptorMkII = function InterceptorMkII(json, ship) {
    InterceptorMkI.call(this, json, ship);
};
InterceptorMkII.prototype = Object.create(InterceptorMkI.prototype);
InterceptorMkII.prototype.constructor = InterceptorMkII;

var InterceptorPrototype = function InterceptorPrototype(json, ship) {
    InterceptorMkI.call(this, json, ship);
};
InterceptorPrototype.prototype = Object.create(InterceptorMkI.prototype);
InterceptorPrototype.prototype.constructor = InterceptorPrototype;

var Shield = function Shield(json, ship) {
    ShipSystem.call(this, json, ship);
    this.defensiveType = "Shield";
};

Shield.prototype = Object.create(ShipSystem.prototype);
Shield.prototype.constructor = Shield;
Shield.prototype.getDefensiveHitChangeMod = function (target, shooter, weapon) {
    if (!weapon.ballistic) {
        if (shooter.flight && (mathlib.getDistanceBetweenShipsInHex(target, shooter) == 0)) return 0;
    }
    return shipManager.systems.getOutput(target, this);
};

var EMShield = function EMShield(json, ship) {
    Shield.call(this, json, ship);
    this.defensiveType = "Shield";
};
EMShield.prototype = Object.create(Shield.prototype);
EMShield.prototype.constructor = EMShield;

var GraviticShield = function GraviticShield(json, ship) {
    Shield.call(this, json, ship);
    this.defensiveType = "Shield";
};
GraviticShield.prototype = Object.create(Shield.prototype);
GraviticShield.prototype.constructor = GraviticShield;

var ShieldGenerator = function ShieldGenerator(json, ship) {
    ShipSystem.call(this, json, ship);
};

ShieldGenerator.prototype = Object.create(ShipSystem.prototype);
ShieldGenerator.prototype.constructor = ShieldGenerator;

ShieldGenerator.prototype.onTurnOff = function (ship) {
    for (var i in ship.systems) {
        var system = ship.systems[i];
        if (system.name == 'graviticShield') {
            // Shut it down.
            system.power.push({
                id: null,
                shipid: ship.id,
                systemid: system.id,
                type: 1,
                turn: gamedata.turn,
                amount: 0
            });
            shipWindowManager.setDataForSystem(ship, system);
        }
    }
};

ShieldGenerator.prototype.onTurnOn = function (ship) {
    for (var i in ship.systems) {
        var system = ship.systems[i];
        if (system.name == 'graviticShield') {
            // Turn it all on.
            shipManager.power.setOnline(ship, system);
            shipWindowManager.setDataForSystem(ship, system);
        }
    }
};

var Swrayshield = function Swrayshield(json, ship) {
    ShipSystem.call(this, json, ship);
    this.defensiveType = "Shield";
};
Swrayshield.prototype = Object.create(ShipSystem.prototype);
Swrayshield.prototype.constructor = Swrayshield;
Swrayshield.prototype.getDefensiveHitChangeMod = function (target, shooter, weapon) {
    return 0; //Ray shield does not affect hit chance
};
Swrayshield.prototype.hasMaxBoost = function () {
    return true;
};
Swrayshield.prototype.getMaxBoost = function () {
    return this.maxBoostLevel;
};

var CWShield = function CWShield(json, ship) {
    ShipSystem.call(this, json, ship);
    this.defensiveType = "Shield";
};
CWShield.prototype = Object.create(ShipSystem.prototype);
CWShield.prototype.constructor = CWShield;
CWShield.prototype.getDefensiveHitChangeMod = function (target, shooter, weapon) {
    return 0; //Ray shield does not affect hit chance
};
CWShield.prototype.hasMaxBoost = function () {
    return true;
};
CWShield.prototype.getMaxBoost = function () {
    return this.maxBoostLevel;
};

var SatyraShield = function SatyraShield(json, ship) {
    ShipSystem.call(this, json, ship);
    this.defensiveType = "Shield";
};
SatyraShield.prototype = Object.create(ShipSystem.prototype);
SatyraShield.prototype.constructor = SatyraShield;
SatyraShield.prototype.getDefensiveHitChangeMod = function (target, shooter, weapon) {
    return 0; //Ray shield does not affect hit chance
};
SatyraShield.prototype.hasMaxBoost = function () {
    return true;
};
SatyraShield.prototype.getMaxBoost = function () {
    return this.maxBoostLevel;
};

var Absorbtionshield = function Absorbtionshield(json, ship) {
    ShipSystem.call(this, json, ship);
    this.defensiveType = "Shield";
};
Absorbtionshield.prototype = Object.create(ShipSystem.prototype);
Absorbtionshield.prototype.constructor = Absorbtionshield;
Absorbtionshield.prototype.getDefensiveHitChangeMod = function (target, shooter, weapon) {
    return 0; //absorbtion shield does not affect hit chance
};
Absorbtionshield.prototype.hasMaxBoost = function () {
    return true;
};
Absorbtionshield.prototype.getMaxBoost = function () {
    return this.maxBoostLevel;
};

var Particleimpeder = function Particleimpeder(json, ship) {
    Weapon.call(this, json, ship);
    this.defensiveType = "Impeder";
};
Particleimpeder.prototype = Object.create(Weapon.prototype);
Particleimpeder.prototype.constructor = Particleimpeder;
Particleimpeder.prototype.getDefensiveHitChangeMod = function (target, shooter, weapon) {
	return shipManager.systems.getOutput(target, this);
	/* now it affects everything!
    if (shooter.flight) {
        //only affects fighters
        return shipManager.systems.getOutput(target, this);
    } else {
        return 0;
    }
	*/
};
Particleimpeder.prototype.hasMaxBoost = function () {
    return true;
};
Particleimpeder.prototype.getMaxBoost = function () {
    return this.maxBoostLevel;
};
Particleimpeder.prototype.initBoostableInfo = function () {
    // Needed because it can chance during initial phase
    if (window.weaponManager.isLoaded(this)) {} else {
        var count = shipManager.power.getBoost(this);
        for (var i = 0; i < count; i++) {
            shipManager.power.unsetBoost(null, this);
        }
    }

    this.intercept = this.getInterceptRating();
    this.data.Intercept = this.getInterceptRating() * -5;
    this.data.Boostlevel = shipManager.power.getBoost(this);

	//display current boost as output - because that is actual shield rating from Impeder!
	if (this.data.Boostlevel > 0) {
		this.outputDisplay = this.data.Boostlevel;
	} else {
		this.outputDisplay = '-'; //'0' is not shown!
	}
	

    return this;
};
Particleimpeder.prototype.getInterceptRating = function () {
    return 3 + shipManager.power.getBoost(this);
};

var FtrShield = function(json, ship)
{
    ShipSystem.call( this, json, ship);
    this.defensiveType = "Shield";
}
FtrShield.prototype = Object.create( ShipSystem.prototype );
FtrShield.prototype.constructor = FtrShield;
FtrShield.prototype.getDefensiveHitChangeMod = function(target, shooter, weapon)
{
    return shipManager.systems.getOutput(target, this);
}

var HeavyInterceptorBattery = function HeavyInterceptorBattery(json, ship) {
    InterceptorMkI.call(this, json, ship);
};
HeavyInterceptorBattery.prototype = Object.create(InterceptorMkI.prototype);
HeavyInterceptorBattery.prototype.constructor = HeavyInterceptorBattery;

var Interdictor = function Interdictor(json, ship) {
    Weapon.call(this, json, ship);
};
Interdictor.prototype = Object.create(Weapon.prototype);
Interdictor.prototype.constructor = Interdictor;

var FtrInterdictor = function FtrInterdictor(json, ship) {
    Weapon.call(this, json, ship);
};
FtrInterdictor.prototype = Object.create(Weapon.prototype);
FtrInterdictor.prototype.constructor = FtrInterdictor;


var ThirdspaceShield = function ThirdspaceShield(json, ship) {
    ShipSystem.call(this, json, ship);
    this.defensiveType = "none";
};
ThirdspaceShield.prototype = Object.create(ShipSystem.prototype);
ThirdspaceShield.prototype.constructor = ThirdspaceShield;
ThirdspaceShield.prototype.getDefensiveHitChangeMod = function (target, shooter, weapon) {
    //this is made to be a shield just to display arc visually, no actual protection
    return 0;
};

ThirdspaceShield.prototype.initializationUpdate = function() {
	var ship = this.ship;	
	this.outputDisplay = this.currentHealth;
	
	if (this.currentHealth == 0) {
		this.outputDisplay = '-'; //'0' is not shown!							
	}		
	
	return this;
};

ThirdspaceShield.prototype.canIncrease = function () { //Can increase if not at max / destroyed.
 //Check if it is at maxHealth / not destroyed etc / Is there spare capacity in Generator?	
 
 	var ship = this.ship;

	if(ship.flight) return false;//Fighters can't increase or decrease shields
	if(this.currentHealth >= this.maxhealth) return false; //Shield is at maximum output.
			
	for (var i in ship.systems) {
		var system = ship.systems[i];

		if (system instanceof ThirdspaceShieldGenerator) {
			var generator = system; //Find generator
		}
	}	

	if(!generator) return false; //This Thirdspace ship has no generator, can't move shields around!
		
	return true;		
};			
	
ThirdspaceShield.prototype.canDecrease = function () { //can decrease if not at zero / destroyed.
 //Check if it is at 0 health / not destroyed etc
 	var ship = this.ship;

	if(ship.flight) return false;//Fighters can't increase or decrease shields
	if(this.currentHealth <= 0) return false; //Shield cannot be reduced more.

	for (var i in ship.systems) {
		var system = ship.systems[i];

		if (system instanceof ThirdspaceShieldGenerator) {
			var generator = system; //Find generator
		}
	}	

	if(!generator) return false; //This Thirdspace ship has no generator, can't move shields around!	
	
	return true;
};


ThirdspaceShield.prototype.doIncrease = function () { //	
//Increase this.maxhealth by 5 (or lower if less available) + decrease Shield Generator by same amount.
	
 	var ship = this.ship;	
	for (var i in ship.systems) {
		var system = ship.systems[i];

		if (system instanceof ThirdspaceShieldGenerator) {
			var generator = system; //Find generator
		}
	}	

	var shieldHealth = this.currentHealth; //
	var shieldHeadroom = this.maxhealth - shieldHealth;//How much room for increase does shield have?
		
	if(shieldHeadroom >= 1){		
		this.currentHealth += 1;
		generator.storedCapacity -= 1;
	}

};

ThirdspaceShield.prototype.doIncrease5 = function () { //	
//Increase this.maxhealth by 5 (or lower if less available) + decrease Shield Generator by same amount.
	
 	var ship = this.ship;	
	for (var i in ship.systems) {
		var system = ship.systems[i];

		if (system instanceof ThirdspaceShieldGenerator) {
			var generator = system; //Find generator
		}
	}	

	var shieldHealth = this.currentHealth; 
	var shieldHeadroom = this.maxhealth - shieldHealth;//How much room for increase does shield have?
		
	if(shieldHeadroom >= 5){		
		this.currentHealth += 5;
		generator.storedCapacity -= 5;
	}else{ //Just increase by how much you can!
		this.currentHealth += shieldHeadroom;
		generator.storedCapacity -= shieldHeadroom;		
	}	

};

ThirdspaceShield.prototype.doIncrease10 = function () { //	
//Increase this.maxhealth by 10 (or lower if less available) + decrease Shield Generator by same amount.
	
 	var ship = this.ship;	
	for (var i in ship.systems) {
		var system = ship.systems[i];

		if (system instanceof ThirdspaceShieldGenerator) {
			var generator = system; //Find generator
		}
	}	

	var shieldHealth = this.currentHealth; 
	var shieldHeadroom = this.maxhealth - shieldHealth;//How much room for increase does shield have?
		
	if(shieldHeadroom >= 10){		
		this.currentHealth += 10;
		generator.storedCapacity -= 10;
	}else{ //Just increase by how much you can!
		this.currentHealth += shieldHeadroom;
		generator.storedCapacity -= shieldHeadroom;		
	}	

};


ThirdspaceShield.prototype.doDecrease = function () { 
//Reduce this.maxhealth by 5 (or lower if less available) + increase Shield Generator by same amount.
	
 	var ship = this.ship;	
	for (var i in ship.systems) {
		var system = ship.systems[i];

		if (system instanceof ThirdspaceShieldGenerator) {
			var generator = system; //Find generator
		}
	}

	var shieldHealth = this.currentHealth;
	if(shieldHealth >= 1){		
		this.currentHealth -= 1;
		generator.storedCapacity += 1;
	}

	if (this.shieldHealth == 0) {
		this.outputDisplay = '-'; //'0' is not shown!							
	}

};

ThirdspaceShield.prototype.doDecrease5 = function () { 
//Reduce this.maxhealth by 5 (or lower if less available) + increase Shield Generator by same amount.
	
 	var ship = this.ship;	
	for (var i in ship.systems) {
		var system = ship.systems[i];

		if (system instanceof ThirdspaceShieldGenerator) {
			var generator = system; //Find generator
		}
	}

	var shieldHealth = this.currentHealth;
	if(shieldHealth >= 5){		
		this.currentHealth -= 5;
		generator.storedCapacity += 5;
	}else{
		var shieldIncrement = Math.max(0, shieldHealth);
		this.currentHealth -= shieldIncrement;
		generator.storedCapacity += shieldIncrement;		
	}
	
	if (this.shieldHealth == 0) {
		this.outputDisplay = '-'; //'0' is not shown!							
	}	
		
};

ThirdspaceShield.prototype.doDecrease10 = function () { 
//Reduce this.maxhealth by 10 (or lower if less available) + increase Shield Generator by same amount.
	
 	var ship = this.ship;	
	for (var i in ship.systems) {
		var system = ship.systems[i];

		if (system instanceof ThirdspaceShieldGenerator) {
			var generator = system; //Find generator
		}
	}

	var shieldHealth = this.currentHealth;
	if(shieldHealth >= 10){		
		this.currentHealth -= 10;
		generator.storedCapacity += 10;
	}else{
		var shieldIncrement = Math.max(0, shieldHealth);
		this.currentHealth -= shieldIncrement;
		generator.storedCapacity += shieldIncrement;		
	}	
	
	if (this.shieldHealth == 0) {
		this.outputDisplay = '-'; //'0' is not shown!							
	}	
	
};


ThirdspaceShield.prototype.doIndividualNotesTransfer = function () { //prepare individualNotesTransfer variable - if relevant for this particular system
	this.individualNotesTransfer = Array();
	//Now pass a note to create a damage entry that will either increase or decrease shields strength.
	var startHealth = shipManager.systems.getRemainingHealth(this); //What was the health at start of Initial Orders?
	var endHealth = this.currentHealth; //currentHealth is effectively the counter for where shield strength ends up.
	var shieldChange = startHealth - endHealth;//What is the change that should go into Damage Entry. Positive if decreased, negative if increased.
	
	if(gamedata.gamephase == 1 && (shieldChange != 0)){
		this.individualNotesTransfer.push(shieldChange); //Push change in shield strength to back end for Damage Entry creation if required e.g. over or under 0.
	}
	
	return true;
};


//MINDRIDER THOUGHT SHIELDS
var ThoughtShield = function ThoughtShield(json, ship) {
    ThirdspaceShield.call(this, json, ship);
    this.defensiveType = "none";
};
ThoughtShield.prototype = Object.create(ThirdspaceShield.prototype);
ThoughtShield.prototype.constructor = ThoughtShield;

ThoughtShield.prototype.getDefensiveHitChangeMod = function (target, shooter, weapon) {
    var thisShip = this.ship;
    var defenceMod = 0; // Default value

    // Iterate through all ships
    gamedata.ships.forEach(ship => {
        if (ship.phpclass !== 'Consortium') return; // Only Consortium ships
        if (ship.team !== target.team) return; // Same team as the target
        if (shipManager.isDestroyed(ship)) return; // Skip destroyed ships

        // Process each system in the current ship
        ship.systems.forEach(system => {
            if (!(system instanceof ShieldReinforcement)) return; // Only Shield Reinforcement systems
            
            // Calculate baseOutput once
            const baseOutput = shipManager.systems.getRemainingHealth(system);
            let noOfShieldsBoosted = 0;

            // Process all fire orders for the system
            system.fireOrders.forEach(fireOrder => {
                if (fireOrder.targetid === thisShip.id) {
                    defenceMod = system.reinforceAmount; // Set defenceMod for this ship
                }

                // Check the target ship's systems for Thought Shields
                const reinforceTarget = gamedata.getShip(fireOrder.targetid);
                noOfShieldsBoosted += reinforceTarget.systems.filter(s => s instanceof ThoughtShield).length;
            });

            // Handle the case where the Consortium is the target ship
            if (ship.id === thisShip.id) {
                const usedOutput = noOfShieldsBoosted * system.reinforceAmount;
                const remainingOutput = baseOutput - usedOutput;

                // Count Thought Shields on this ship
                const ownShields = thisShip.systems.filter(s => s instanceof ThoughtShield).length;

                if (ownShields > 0) {
                    defenceMod = Math.round(remainingOutput / ownShields);
                }
            }
        });
    });

    return defenceMod; // Return the calculated defenceMod
};


/* //OLD VERSION BEFORE OPTIMISATION - LEAVE HERE FOR NOW
ThoughtShield.prototype.getDefensiveHitChangeMod = function (target, shooter, weapon) {

 	var thisShip = this.ship;

    for (var i in gamedata.ships) {
        var ship = gamedata.ships[i];
        
        if(!ship.phpclass == 'Consortium') continue;  //Only one type of ship can Reinforce Thought Shield 
    	if(!ship.team == target.team) continue; //And in this ship's team.
    	if(shipManager.isDestroyed(ship)) continue;	//Not dead!

		//Search Consortium's systems for Shield Reinforcement    		
		for (var j in ship.systems) {
			var system = ship.systems[j];

			//Only interested in Shield Reinforcement systems
			if (system instanceof ShieldReinforcement) {
					var baseOutput = shipManager.systems.getRemainingHealth(system);
					var noOfShieldsBoosted = 0;//Initialise	
					var usedOutput = 0;					
					
					//Now check it's fireOrders
					if(system.fireOrders.length > 0){
						for(var k in system.fireOrders){
							var fireOrder = system.fireOrders[k];
							//Looking for a fireOrder boosting shields and amend defenceMod
							if(fireOrder.targetid == thisShip.id) this.defenceMod = system.reinforceAmount;	
								
							//Also record how much shield power was used so we know how much is left to boost Consortium below
							var reinforceTarget = gamedata.getShip(fireOrder.targetid);
							for (var l in reinforceTarget.systems) {//Check all systems for Thought Shield
								var targetSystem = reinforceTarget.systems[l];
								
								if (targetSystem instanceof ThoughtShield) {
									noOfShieldsBoosted += 1;//Shield found, add it to tally.
								}
							}						
						}
					}
				
					if(ship.id == thisShip.id){ //The Consortium IS the targeted ship!
						usedOutput = noOfShieldsBoosted * system.reinforceAmount;
						var remainingOutput = baseOutput - usedOutput;
						var ownShields = 0;
						
						for (var m in thisShip.systems) {//Check all systems for Thought Shield
							var ownSystem = thisShip.systems[m];
									
								if (ownSystem instanceof ThoughtShield) {
									ownShields += 1;//Shield found, add it to tally.
								}
						}
						
						this.defenceMod = Math.round(remainingOutput / ownShields);								
					}	
			}
		}    	
        
	}
    
    return this.defenceMod; //Usually 0, unless reinforced.
};
*/

ThoughtShield.prototype.canIncrease = function () { //Can increase if not at max / destroyed.
 //Check if it is at maxHealth / not destroyed etc / Is there spare capacity in Generator?	
 
 	var ship = this.ship;

	if(ship.flight) return false;//Fighters can't increase or decrease shields
	if(this.currentHealth >= this.maxhealth) return false; //Shield is at maximum output.
			
	for (var i in ship.systems) {
		var system = ship.systems[i];

		if (system instanceof ThoughtShieldGenerator) {
			var generator = system; //Find generator
		}
	}	

	if(!generator) return false; //This Thirdspace ship has no generator, can't move shields around!
		
	return true;		
};			
	
ThoughtShield.prototype.canDecrease = function () { //can decrease if not at zero / destroyed.
 //Check if it is at 0 health / not destroyed etc
 	var ship = this.ship;

	if(ship.flight) return false;//Fighters can't increase or decrease shields
	if(this.currentHealth <= 0) return false; //Shield cannot be reduced more.

	for (var i in ship.systems) {
		var system = ship.systems[i];

		if (system instanceof ThoughtShieldGenerator) {
			var generator = system; //Find generator
		}
	}	

	if(!generator) return false; //This Thirdspace ship has no generator, can't move shields around!	
	
	return true;
};
