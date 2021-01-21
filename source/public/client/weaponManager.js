"use strict";

window.weaponManager = {
    mouseoverTimer: null,
    mouseOutTimer: null,
    mouseoverSystem: null,
    currentSystem: null,
    currentShip: null,

    getWeaponCurrentLoading: function getWeaponCurrentLoading(weapon) {
		/*obsolete
        if (weapon.duoWeapon) {
            var returnArray = new Array(weapon.weapons[1].getTurnsloaded(), weapon.weapons[2].getTurnsloaded());
            return returnArray;
        }*/
        return weapon.turnsloaded;
    },

    onModeClicked: function onModeClicked(ship, system) {
        //throw new Error("Route trough phase strategy to get selected ship");
        if (!system) return;

        if (gamedata.gamephase != 3 && !system.ballistic) return;

        if (gamedata.gamephase != 1 && system.ballistic) return;

        if (weaponManager.hasFiringOrder(ship, system)) return;

        if (gamedata.isMyShip(ship)) {
            //weaponManager.unSelectWeapon(ship, system); //do NOT do so - that would be much better for next mode change!

			/* no dual weapons around any more!
            if (system.dualWeapon) {
                console.log("changing dual weapon?")
                var parentSystem = shipManager.systems.getSystem(ship, system.parentId);
                parentSystem.changeFiringMode();
                shipWindowManager.setDataForSystem(ship, parentSystem);
            } else */{
                system.changeFiringMode();
                shipWindowManager.setDataForSystem(ship, system);
            }
            
            webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
			
        }
    },

    onHoldfireClicked: function onHoldfireClicked(e) {
        e.stopPropagation();
        var shipwindow = $(".shipwindow").has($(this));
        var systemwindow = $(".system").has($(this));
        var ship = gamedata.getShip(shipwindow.data("ship"));
        var system = shipManager.systems.getSystem(ship, systemwindow.data("id"));

        if (gamedata.gamephase != 3 && !system.ballistic) return;

        if (gamedata.gamephase != 1 && system.ballistic) return;

        if (ship.userid == gamedata.thisplayer) {
            if (!system.duoWeapon) {
                weaponManager.cancelFire(ship, system);
            } else {
                systemwindow.removeClass("duofiring");

                for (var i in system.weapons) {
                    var duoweapon = system.weapons[i];

                    if (weaponManager.hasFiringOrder(ship, duoweapon)) {
                        weaponManager.cancelFire(ship, duoweapon);
                    }
                }
            }
        }
    },

    cancelFire: function cancelFire(ship, system) {
        weaponManager.removeFiringOrder(ship, system);
        ballistics.updateList();
        shipWindowManager.setDataForSystem(ship, system);
        gamedata.shipStatusChanged(ship);
    },

    onWeaponMouseover: function onWeaponMouseover(e) {
        if (weaponManager.mouseOutTimer != null) {
            clearTimeout(weaponManager.mouseOutTimer);
            weaponManager.mouseOutTimer = null;
        }

        if (weaponManager.mouseoverTimer != null) return;

        var id = $(this).data("shipid");
        weaponManager.currentShip = gamedata.getShip(id);

        if ($(this).hasClass("fightersystem")) {
            weaponManager.currentSystem = shipManager.systems.getFighterSystem(weaponManager.currentShip, $(this).data("fighterid"), $(this).data("id"));
        } else {
            weaponManager.currentSystem = shipManager.systems.getSystem(weaponManager.currentShip, $(this).data("id"));
        }

        var targetElement = $(this);

        weaponManager.mouseoverSystem = targetElement;

        weaponManager.mouseoverTimer = setTimeout(weaponManager.doWeaponMouseOver, 150);
    },

    onWeaponMouseoverDuoSystem: function onWeaponMouseoverDuoSystem(e) {
        // ignore this. We've already entered the parent of this duosystem
    },

    onWeaponMouseOutDuoSystem: function onWeaponMouseOutDuoSystem(e) {
        // ignore this. We've already entered the parent of this duosystem
    },

    onWeaponMouseOut: function onWeaponMouseOut(e) {
        if (weaponManager.mouseoverTimer != null) {
            clearTimeout(weaponManager.mouseoverTimer);
            weaponManager.mouseoverTimer = null;
        }

        weaponManager.mouseOutTimer = setTimeout(weaponManager.doWeaponMouseout, 50);
    },

    doWeaponMouseOver: function doWeaponMouseOver(e) {
        if (weaponManager.mouseoverTimer != null) {
            clearTimeout(weaponManager.mouseoverTimer);
            weaponManager.mouseoverTimer = null;
        }

        systemInfo.hideSystemInfo();

        if (weaponManager.mouseoverSystem == null) {
            return;
        }

        var weapon = shipManager.systems.initializeSystem(weaponManager.currentSystem);
        webglScene.customEvent('SystemMouseOver', {
            ship: weaponManager.currentShip,
            system: weapon,
            element: weaponManager.mouseoverSystem
        });
    },

    doWeaponMouseout: function doWeaponMouseout() {
        if (weaponManager.mouseOutTimer != null) {
            clearTimeout(weaponManager.mouseOutTimer);
            weaponManager.mouseOutTimer = null;
        }

        systemInfo.hideSystemInfo();
        weaponManager.mouseoverSystem = null;
        webglScene.customEvent('SystemMouseOut');
    },

    unSelectWeapon: function unSelectWeapon(ship, weapon) {
        for (var i = gamedata.selectedSystems.length - 1; i >= 0; i--) {
            if (gamedata.selectedSystems[i] == weapon) {
                gamedata.selectedSystems.splice(i, 1);
            }

            if (weapon.duoWeapon) {
                for (var j in weapon.weapons) {
                    var subweapon = weapon.weapons[j];

                    weaponManager.unSelectWeapon(ship, subweapon);
                }
            }
        }

        shipWindowManager.setDataForSystem(ship, weapon);
        webglScene.customEvent('SystemDataChanged', { ship: ship, system: weapon });
    },

    checkConflictingFireOrder: function checkConflictingFireOrder(ship, weapon, alert) {
        var p = ship;
        if (ship.flight) {
            p = shipManager.systems.getFighterBySystem(ship, weapon.id);
        }

        for (var i in p.systems) {
            var system = p.systems[i];
            if (system.id == weapon.id) continue; //can't conflict itself

            if (weaponManager.hasFiringOrder(ship, system)) {
                /*I make it so guns prevent guns from firing, but not missiles - and vice versa*/
                if ((weapon.exclusive || system.exclusive) && weapon.ballistic == system.ballistic) {
                    if (alert) confirm.error("You cannot fire <b>" + weapon.displayName + "</b> and <b>" + system.displayName + "</b> together!");
                    return true;
                }
            }
        }

        return false;
    },

    checkOutOfAmmo: function checkOutOfAmmo(ship, weapon) {

        var p = ship;
        if (ship.flight) {
            p = shipManager.systems.getFighterBySystem(ship, weapon.id);
        } else {
            return false;
        }

        if (weapon.hasOwnProperty("ammunition")) {
            if (weapon.ammunition > 0) {
                return false;
            } else {
                //confirm.error("This fighter gun is out of ammunition.");
                confirm.error("This weapon is out of ammunition.");
                return true;
            }
        }

        for (var i in p.systems) {
            var system = p.systems[i];
            if (system.id != weapon.id) continue;

            if (system.missileArray) {
                for (var j in system.missileArray) {
                    var missile = system.missileArray[j];

                    if (missile.amount > 0) {
                        return false;
                    } else {
                        confirm.error("This missile rack is out of ammo.");
                        return true;
                    }
                }
            }
        }

        return false;
    },

    selectWeapon: function selectWeapon(ship, weapon) {
        if (weaponManager.checkOutOfAmmo(ship, weapon)) {
            return;
        }

        if (weaponManager.checkConflictingFireOrder(ship, weapon, alert)) {
            return;
        }

		if (!weaponManager.isLoaded(weapon))
            return;
            
        if (shipManager.power.isOffline(ship, weapon)) {
            return;
        }

        if (shipManager.systems.isDestroyed(ship, weapon)) {
            return;
        }

        if (weapon.autoFireOnly) return; //this is auto-fire only weapon, should not be fired manually!


        if (ship.shipSizeClass < 0) {
            for (var i = 0; i < ship.systems.length; i++) {
                for (var b = 0; i < ship.systems.systems; b++) {
                    if (ship.systems[i].systems[b].weapon) {
                        gamedata.selectedSystems.push(ship.systems[i].systems[b].weapon);
                        webglScene.customEvent('WeaponSelected', {
                            ship: ship,
                            weapon: ship.systems[i].systems[b].weapon
                        });
                        shipWindowManager.setDataForSystem(ship, ship.systems[i].systems[b].weapon);
                    }
                }
            }
        }

        gamedata.selectedSystems.push(weapon);
        shipWindowManager.setDataForSystem(ship, weapon);
        webglScene.customEvent('WeaponSelected', { ship: ship, weapon: weapon });
    },

    isSelectedWeapon: function isSelectedWeapon(weapon) {
        if ($.inArray(weapon, gamedata.selectedSystems) >= 0) return true;

        return false;
    },

    selectAllWeapons: function selectAllWeapons(ship, system) {        
		if (!gamedata.isMyShip(ship)) {
			return;
		}        
        var array = [];
        var systems = [];
        if (ship.flight) {
            systems = ship.systems
                .map(fighter => fighter.systems)
                .reduce((all, weapons) => all.concat(weapons), [])
                .filter(system => system.weapon);
        } else {
            systems = ship.systems.filter(system => system.weapon);
        }
        
        array = systems.filter(function(weapon) {return weapon.displayName === system.displayName});
		
		var currentWasSelected = weaponManager.isSelectedWeapon(system); //all others affected weapons will have state set the same as current! 

		for (var i = 0; i < array.length; i++) {
			var system = array[i];

			if (gamedata.waiting) return;

			if (shipManager.isDestroyed(ship) || shipManager.isAdrift(ship)) {
				return;
			}

			if (system.destroyed) {
				continue;
			}


			if (system.weapon) {
				if (gamedata.gamephase != 3 && !system.ballistic) continue; //improper at this moment
				if (gamedata.gamephase != 1 && system.ballistic) continue;	//improper at this moment
				if (weaponManager.hasFiringOrder(ship, system)) continue;//already declared, do not touch it!
				
				if (currentWasSelected){//unselect
					if (weaponManager.isSelectedWeapon(system)) weaponManager.unSelectWeapon(ship, system);
				} else {//select
					if (!weaponManager.isSelectedWeapon(system)) weaponManager.selectWeapon(ship, system);
				}
			}

		}
    },

    targetingShipTooltip: function targetingShipTooltip(selectedShip, ship, e, calledid) {
        //e.find(".shipname").html(ship.name);
        var f = $(".targeting", e);
        f.html("");

        if (gamedata.selectedSystems.length === 0) {
            return;
        }

        if (!(calledid > 0)) {
            //(calledid == null){
            var html = "";
            var section = weaponManager.getShipHittingSide(selectedShip, ship);

            for (var i = 0; i < section.length; i++) {
                switch (section[i]) {
                    case 1:
                        html += "-FORWARD-";
                        break;
                    case 2:
                        html += "-AFT-";
                        break;
                    case 3:
                        html += "-PORT-";
                        break;
                    case 4:
                        html += "-STARBORD-";
                        break;
                    case 31:
                        html += "-PORT.FWD-";
                        break;
                    case 32:
                        html += "-PORT.AFT-";
                        break;
                    case 41:
                        html += "-STBD.FWD-";
                        break;
                    case 42:
                        html += "-STBD.AFT-";
                        break;
                    default:
                        html += "-OTHER-";
                        break;
                }
            }
            $('<div><span class="weapon">' + html + '</span></div>').appendTo(f);
        }

        for (var i in gamedata.selectedSystems) {
            var weapon = gamedata.selectedSystems[i];
            if (weaponManager.isOnWeaponArc(selectedShip, ship, weapon)) {
                if (weaponManager.checkIsInRange(selectedShip, ship, weapon)) {
                    var value = weapon.firingMode;
                    value = weapon.firingModes[value];
                    if (calledid != null && !weaponManager.canWeaponCall(weapon)) {
                        //called shot, weapon not eligible!
                        $('<div><span class="weapon">' + weapon.displayName + ':</span><span class="hitchange"> CANNOT CALL SHOT</span></div>').appendTo(f);
                    } else {
                        $('<div><span class="weapon">' + weapon.displayName + ':</span><span class="hitchange"> - Approx: ' + weaponManager.calculateHitChange(selectedShip, ship, weapon, calledid) + '%</span></div>').appendTo(f);
                    }
                } else {
                    $('<div><span class="weapon">' + weapon.displayName + ':</span><span class="hitchange"> NOT IN RANGE</span></div>').appendTo(f);
                }
            } else {
                $('<div><span class="weapon">' + weapon.displayName + ':</span><span class="notInArc"> NOT IN ARC </span></div>').appendTo(f);
            }
        }
    },

    canWeaponCall: function canWeaponCall(weapon) {
        //is this weapon eleigible for calling precision shot?...
        //Standard or Pulse, not Ballistic!
        if (weapon.ballistic || weapon.hextarget) return false;
        if (weapon.damageType == 'Standard' || weapon.damageType == 'Pulse') return true;
        return false;
    },

    canCalledshot: function canCalledshot(target, system, shooter) {
        /*Marcin Sawicki, new version $outerSections-based - October 2017*/
        var sectionEligible = false; //section that system is mounted on is eligible for caled shots
        if (!shooter) return false;
		if (system.isTargetable != true) return false; //cannot be targeted by called shots under any conditions

        if (target.flight) return true; //allow called shots at fighters (in effect it will affect particular fighter, not fighter system)

        var shooterCompassHeading = mathlib.getCompassHeadingOfShip(target, shooter);
        var targetFacing = shipManager.getShipHeadingAngle(target);

        for (var i = 0; i < target.outerSections.length; i++) {
            var currSectionData = target.outerSections[i];
			var arcFrom = 0;
			var arcTo = 0;
            if (system.location == currSectionData.loc) {
				
				if (shipManager.movement.isRolled(target)) {
					arcTo = mathlib.addToDirection(currSectionData.min, currSectionData.min * -2);
					arcFrom = mathlib.addToDirection(currSectionData.max, currSectionData.max * -2);
				} else { //ship NOT rolled
					arcFrom = currSectionData.min;
					arcTo = currSectionData.max;
				}
                if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(arcFrom, targetFacing), mathlib.addToDirection(arcTo, targetFacing))) {
                    if (currSectionData.call == true) return true;
                }

				/*old version - not taking Rolled state into account
                if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(currSectionData.min, targetFacing), mathlib.addToDirection(currSectionData.max, targetFacing))) {
                    if (currSectionData.call == true) return true;
                }
				*/
                sectionEligible = currSectionData.call;
            }
            //"loc" => $curr['loc'], "min" => $curr['min'], "max" => $curr['max'], "call" => $call
        }
        //options here: PRIMARY, incorrect facing of targeted section, section not eligible for called shots (eg. on MCVs)
        if (system.location > 0 && sectionEligible == true) {
            return false; //non-PRIMARY and eligible for called shots, but still here => must be out of arc!
        }
        //option here: section not normally eligible for target shots (PRIMARY or outer section on MCV)
        //check whether system is PRIMARY-targetable!
        if (system.isPrimaryTargetable != true) return false; //cannot be targeted under these conditions
        //check whether it's in arc
        if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(system.startArc, targetFacing), mathlib.addToDirection(system.endArc, targetFacing))) {
            return true;
        }
        return false;
    }, //endof function canCalledshot


	
    getTargetableThruster: function getTargetableThruster(shooter, target) {
        var targetFacing = shipManager.getShipHeadingAngle(target);
        var shooterCompassHeading = mathlib.getCompassHeadingOfShip(target, shooter);

        //if (target.draziHCV){ //ALWAYS, not just for Drazi HCV layout!
        if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(330, targetFacing), mathlib.addToDirection(30, targetFacing))) {
            return 1;
        }
        if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(150, targetFacing), mathlib.addToDirection(210, targetFacing))) {
            return 2;
        }
        //}
        if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(210, targetFacing), mathlib.addToDirection(330, targetFacing))) {
            return 3;
        }
        if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(30, targetFacing), mathlib.addToDirection(150, targetFacing))) {
            return 4;
        }
    },

    isPosOnWeaponArc: function isPosOnWeaponArc(shooter, position, weapon) {
        var shooterFacing = shipManager.getShipHeadingAngle(shooter);
        var targetCompassHeading = mathlib.getCompassHeadingOfPoint(shipManager.getShipPosition(shooter), position);

        var arcs = shipManager.systems.getArcs(shooter, weapon);
        arcs.start = mathlib.addToDirection(arcs.start, shooterFacing);
        arcs.end = mathlib.addToDirection(arcs.end, shooterFacing);

        return mathlib.isInArc(targetCompassHeading, arcs.start, arcs.end);
    },

    isOnWeaponArc: function isOnWeaponArc(shooter, target, weapon) {
        //console.log("is on arc");
        var shooterFacing = shipManager.getShipHeadingAngle(shooter);
        var targetCompassHeading = mathlib.getCompassHeadingOfShip(shooter, target);

        var arcs = shipManager.systems.getArcs(shooter, weapon);
        arcs.start = mathlib.addToDirection(arcs.start, shooterFacing);
        arcs.end = mathlib.addToDirection(arcs.end, shooterFacing);
        var oPos = shipManager.getShipPosition(shooter);
        var tPos = shipManager.getShipPosition(target);

		/*if two ships are at same hex, then their relative position depends on THIS TURN Ini;
		and it should on PREVIOUS turn Ini... this may affect ability to launch missiles. 
		Hence at range 0 missile launch is always allowed, no matter the arc.
		*/
        if (weapon.ballistic && oPos.equals(tPos)) return true;
		
		
        return mathlib.isInArc(targetCompassHeading, arcs.start, arcs.end);
    },

    calculateRangePenalty: function calculateRangePenalty(distance, weapon) {
        var rangePenalty = 0;
		if (weapon.specialRangeCalculation){
			rangePenalty = weapon.calculateSpecialRangePenalty(distance);
		}else{ //standard calculation
			rangePenalty = weapon.rangePenalty * distance;
		}
        return rangePenalty;
    },

	/*Marcin Sawicki, September 2019: simplified to using calculateHitChange instead*/
	calculataBallisticHitChange: function calculataBallisticHitChange(ball, calledid) {
		var shooter = gamedata.getShip(ball.shooterid);
        	var weapon = shipManager.systems.getSystem(shooter, ball.weaponid);
        	var target = gamedata.getShip(ball.targetid);
		return weaponManager.calculateHitChange(shooter, target, weapon, calledid);
	},//endof calculataBallisticHitChange

    getInterception: function getInterception(ball) {

        var intercept = 0;

        for (var i in gamedata.ships) {
            var ship = gamedata.ships[i];
            var fires = weaponManager.getAllFireOrders(ship);
            for (var a in fires) {
                var fire = fires[a];
                if (fire.type == "intercept" && fire.targetid == ball.fireOrderId) {
                    var weapon = shipManager.systems.getSystem(ship, fire.weaponid);
                    intercept += weapon.getInterceptRating();
                }
            }
        }

        return intercept;
    },

    calculateBaseHitChange: function calculateBaseHitChange(target, base, shooter, weapon) {
        var jink = 0;
        var dew = 0;

        if (target.flight && shooter) {
            if (!shooter.flight) {
                jink = shipManager.movement.getJinking(target);
            } else {
                if (shooter) {
                    var sPosHex = shipManager.getShipPosition(shooter);
                    var tPosHex = shipManager.getShipPosition(target);

                    if (!sPosHex.equals(tPosHex) || shipManager.movement.getJinking(shooter) > 0) {
                        jink = shipManager.movement.getJinking(target);
                    }
                }
            }
        } else {
            dew = ew.getDefensiveEW(target);
        }

        var bdew = 0;
        var sdew = 0;

        sdew = ew.getSupportedDEW(target);
        bdew = ew.getSupportedBDEW(target);

        if (shooter && shooter.flight && !weapon.ballistic) {
            dew = 0;
            bdew = 0;
            sdew = 0;
        }

		//advanced sensors: negates BDEW and SDEW, unless target is unit of advanced race
		if ( shooter && (target.factionAge < 3) && (shipManager.hasSpecialAbility(shooter, "AdvancedSensors")) ){
			bdew = 0;
			sdew = 0;
		}	
		
		//half-phasing target is more difficult to hit
		var halfphase = 0;
		if(shooter && weapon){ //consider half-phasing in calculations for a particular shot, but not in base profile
			if (shipManager.movement.isHalfPhased(target)){
				halfphase = 4; //basic penalty induced by half-phasing target
				//ballistics double the penalty
				if (weapon && weapon.ballistic) halfphase = 8;
			}
			//if firing unit is itself half phasing, that's -10
			if (shooter && shipManager.movement.isHalfPhased(shooter)) halfphase = 10;
		}

        return base - dew - jink - bdew - sdew - halfphase;
    },


    /*calculate hit chance for ramming attack - different procedure*/
    /*also, it would be a bit different (simplified) from B5Wars original*/
    calculateRamChance: function(shooter, target, weapon, calledid){
        if (calledid > 0) return 0;//can't call ramming attack!
        if ((!shooter.flight) && (target.flight)) return 0;//ship has no chance to ram a fighter!
        var hitChance = 8; //base: 40%
		
		//half-phased and non-half-phased ship cannot ram each other
		var shooterHalfphased = shipManager.movement.isHalfPhased(shooter);
		var targetHalfphased = shipManager.movement.isHalfPhased(target);
		if (shooterHalfphased != targetHalfphased) return 0;

        if (target.Enormous) hitChance+=6;//+6 vs Enormous units
        if (shooter.Enormous) hitChance+=6;//+6 if ramming unit is Enormous
        if ((target.shipSizeClass >= 3) && (shooter.shipSizeClass <3))hitChance += 2;//+2 if target is Capital and ramming unit is not
        if ((shooter.shipSizeClass >= 3) && (target.shipSizeClass <3))hitChance -= 2;//-2 if shooter is Capital and rammed unit is not
        if ((shooter.flight) && (!target.flight))hitChance += 4;//+4 for fighter trying to ram a ship
        var targetSpeed = Math.abs(shipManager.movement.getSpeed(target)); //I think speed cannot be negative, but just in case ;)
        switch(targetSpeed) {
            case 0: //+5 if the target is not moving.
                hitChance += 5;
                break;
            case 1://+3 if the target is moving speed 1.
                hitChance += 3;
                break;
            case 2://+2 if the target is moving speed 2 or 3.
            case 3:
                hitChance += 2;
                break;
            case 4://+1 if the target is moving speed 4 or 5.
            case 5:
                hitChance += 1;
                break;
            default: //this means >5; ‐1 for every 5 points of speed (or fraction thereof) that the target is moving faster than 5.
                hitChance -= Math.ceil((targetSpeed-5)/5);
        }
        //‐1 for every level of jinking the ramming or target unit is using
        hitChance -= shipManager.movement.getJinking(shooter);
        hitChance -= shipManager.movement.getJinking(target);

        //fire control: usually 0, but units specifically designed for ramming may have some bonus!
        hitChance += weaponManager.getFireControl(target, weapon);
	    
		//range penalty - based on ramming units' speed (typical ramming has no range penalty, but HKs do!
		var ownSpeed = Math.abs(shipManager.movement.getSpeed(shooter));    
		var rangePenalty = weapon.rangePenalty * ownSpeed;
		hitChance -= rangePenalty;
	
        hitChance = Math.round(hitChance * 5); //convert d20->d100
        return hitChance;
    }, //endof calculateRamChance


    calculateHitChange: function calculateHitChange(shooter, target, weapon, calledid) {
		if (weapon.isRammingAttack) {
			return weaponManager.calculateRamChance(shooter, target, weapon, calledid);
		}
	    var defence = 0;
	    var distance = 0;
	    if (weapon.ballistic){		    
		var sPosLaunch = shipManager.movement.getPositionAtStartOfTurn(shooter, gamedata.turn); 		    
		var sPosTarget = shipManager.getShipPosition(target);
		defence = weaponManager.getShipDefenceValuePos(sPosLaunch, target);
	        distance = sPosLaunch.distanceTo(sPosTarget).toFixed(2); 
	    }else{    
        	defence = weaponManager.getShipDefenceValue(shooter, target);
			distance = mathlib.getDistanceBetweenShipsInHex(shooter, target).toFixed(2);
	    } 	    
        var rangePenalty = weaponManager.calculateRangePenalty(distance, weapon);
	    
        //console.log("dis: " + dis + " disInHex: " + disInHex + " rangePenalty: " + rangePenalty);

        var baseDef = weaponManager.calculateBaseHitChange(target, defence, shooter, weapon);
		

        var soew = ew.getSupportedOEW(shooter, target);
        var dist = ew.getDistruptionEW(shooter);

        var oew = 0;

        if (weapon.useOEW) {
            oew = ew.getTargetingEW(shooter, target);
            oew -= dist;
            if (oew <= 0) {
				oew = 0;
				soew = 0;//no lock-on negates SOEW!
			}
        }

        var mod = 0;

        mod -= target.getHitChangeMod(shooter, weapon);

        if (shooter.flight===true) {
            //oew = shooter.offensivebonus;

            //Abbai critical...
            //var firstFighter = shipManager.systems.getSystem(shooter, 1); //should be the same as below...
            var firstFighter = shooter.systems[1];
            var OBcrit = shipManager.criticals.hasCritical(firstFighter, "tmpsensordown");
            oew = shooter.offensivebonus - OBcrit;
			if (weapon.ballistic && (!shooter.hasNavigator) ){ //for ballistics, if there is no Navigator, use OB only if target is in weapon arc!
				if (!weaponManager.isOnWeaponArc(shooter, target, weapon)) {
					oew = 0;
				}
			}		
            oew = Math.max(0, oew); //OBCrit cannot bring Offensive Bonus below 0
			if(oew == 0) soew = 0;

            mod -= shipManager.movement.getJinking(shooter);

            if (shipManager.movement.hasCombatPivoted(shooter)) mod--;
        } else {
			if (shooter.agile === true){
				if (shipManager.movement.hasRolled(shooter)) {
					//		console.log("is rolling -3");
					mod -= 3;
				}
			} else {
				if (shipManager.movement.isRolling(shooter)) {
					//		console.log("is rolling -3");
					mod -= 3;
				}
			}

            if (shipManager.movement.hasPivotedForShooting(shooter)) {
                //		console.log("pivoting");
                mod -= 3;
            }

            if (shooter.osat && shipManager.movement.hasTurned(shooter)) {
                //		console.log("osat turn -1");
                mod -= 1;
            }

            if (!shooter.osat) {
                mod -= shipManager.criticals.hasCritical(shipManager.systems.getSystemByName(shooter, "cnC"), "PenaltyToHit");
                mod -= shipManager.criticals.hasCritical(shipManager.systems.getSystemByName(shooter, "cnC"), "ShadowPilotPain");
            }
        }
        if (calledid > 0) {
            mod += weapon.calledShotMod;
            if (target.base) mod += weapon.calledShotMod; //double penalty vs bases!
        }

        var ammo = weapon.getAmmo(null);
        if (ammo) mod += ammo.hitChanceMod;

	    /*replac ed by lock penalty to allow half lock...
        if (oew < 1 && !shooter.flight) {
            rangePenalty = rangePenalty * 2;
        } else*/
		var noLockPenalty = 0;
		var noLockMod = 0;
		if (oew < 0.5){
			noLockPenalty = 1;
		}else if (oew < 1){ //OEW beteen 0.5 and 1 is achievable for targets of Distortion EW
			noLockPenalty = 0.5;
		}
		noLockMod =  rangePenalty * noLockPenalty;    
        var jammermod = 0;
	    
		//if (shooter.faction != target.faction){ //moved to getJammerValueFromTo!
		
			jammermod = ew.getJammerValueFromTo(shooter,target); //accounts for both jammer and stealth!
			/* replaced by code above
            var jammer = shipManager.systems.getSystemByName(target, "jammer");
            var stealth = shipManager.systems.getSystemByName(target, "stealth");
            if (jammer && !shipManager.power.isOffline(target, jammer)) jammermod = rangePenalty * shipManager.systems.getOutput(target, jammer);		
            if (stealth && mathlib.getDistanceBetweenShipsInHex(shooter, target) > 5) jammermod = rangePenalty;
			*/
			if (jammermod > 0){
				soew = 0;//jammer negates SOEW
			}
			jammermod = jammermod * rangePenalty; //actual reduction
		
			jammermod = jammermod - noLockMod; //noLock does the same thing as Jammer, but cannot be overcame by sensors
			if (jammermod < 0) jammermod = 0; //cannot reduce below 0
	
	/*already taken care of by ew.getJammerValueFromTo
	   if (jammermod > 0){	 //else Improved Sensors do nothing
		//Improved/Advanced Sensors bonus; hasSpecialAbility isn't working correctly on fighter, ability is not propagated beyond system level - possibly needs correction
		if (shipManager.hasSpecialAbility(shooter, "AdvancedSensors") || shipManager.systems.getSystemByName(shooter, "fighteradvsensors")){
			jammermod = 0; //Advanced Sensors negate Jammer
		} else if (shipManager.hasSpecialAbility(shooter, "ImprovedSensors") || shipManager.systems.getSystemByName(shooter, "fighterimprsensors")){
			jammermod = jammermod / 2; //Improved Sensors halve Jammer effect
		}
	   }
	   */
		
			//jammer and jinking do not stack
            if (target.flight) {
                var jinking = shipManager.movement.getJinking(target);
                if (jinking > jammermod) {
                    jammermod = 0;
                } else {
                    jammermod = jammermod - jinking;
                }
            }
        

        var firecontrol = weaponManager.getFireControl(target, weapon);
		
        var goal = baseDef - jammermod - noLockMod - rangePenalty + oew + soew + firecontrol + mod;
        var hitChance = Math.round(goal / 20 * 100);
        return hitChance;
    },


    getFireControl: function getFireControl(target, weapon) {
        if (target.shipSizeClass > 1) {
            return weapon.fireControl[2];
        }
        if (target.shipSizeClass >= 0) {
            return weapon.fireControl[1];
        }

        return weapon.fireControl[0];
    },

    // 'position' should be in HEX coordinate
    getShipDefenceValuePos: function getShipDefenceValuePos(position, target) {
        var targetFacing = shipManager.getShipHeadingAngle(target);
        var targetPos = shipManager.getShipPosition(target);

        var shooterCompassHeading = mathlib.getCompassHeadingOfPoint(targetPos, position);

        //console.log("getShipDefenceValue targetFacing: " + targetFacing + " shooterCompassHeading: " +shooterCompassHeading);

        //console.log("ship degree: " +delta);
        if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(330, targetFacing), mathlib.addToDirection(30, targetFacing))) {
            //console.log("hitting front 1");
            return target.forwardDefense;
        } else if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(150, targetFacing), mathlib.addToDirection(210, targetFacing))) {
            //console.log("hitting rear 2");
            return target.forwardDefense;
        } else if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(210, targetFacing), mathlib.addToDirection(330, targetFacing))) {
            //console.log("hitting port 3");
            return target.sideDefense;
        } else if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(30, targetFacing), mathlib.addToDirection(150, targetFacing))) {
            //console.log("hitting starboard 4");
            return target.sideDefense;
        }

        return target.sideDefense;
    },

    getShipHittingSide: function getShipHittingSide(shooter, target) {
        //Marcin Sawicki, October 2017: new approach!
        var shooterCompassHeading = mathlib.getCompassHeadingOfShip(target, shooter);
        var targetFacing = shipManager.getShipHeadingAngle(target);
        var toReturn = [];

        for (var i = 0; i < target.outerSections.length; i++) {
            var currSectionData = target.outerSections[i];
            if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(currSectionData.min, targetFacing), mathlib.addToDirection(currSectionData.max, targetFacing))) {
                toReturn.push(currSectionData.loc);
            }
            //"loc" => $curr['loc'], "min" => $curr['min'], "max" => $curr['max'], "call" => $call
        }
        toReturn.sort();
        return toReturn;
    },


    getShipDefenceValue: function getShipDefenceValue(shooter, target) {
        var targetFacing = shipManager.getShipHeadingAngle(target);
        var shooterCompassHeading = mathlib.getCompassHeadingOfShip(target, shooter);

        if (target.base) {
            return target.forwardDefense;
        }

        //console.log("getShipDefenceValue targetFacing: " + targetFacing + " shooterCompassHeading: " +shooterCompassHeading);

        //console.log("ship degree: " +delta);
        if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(330, targetFacing), mathlib.addToDirection(30, targetFacing))) {
            //console.log("hitting front 1");
            return target.forwardDefense;
        } else if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(150, targetFacing), mathlib.addToDirection(210, targetFacing))) {
            //console.log("hitting rear 2");
            return target.forwardDefense;
        } else if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(210, targetFacing), mathlib.addToDirection(330, targetFacing))) {
            //console.log("hitting port 3");
            return target.sideDefense;
        } else if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(30, targetFacing), mathlib.addToDirection(150, targetFacing))) {
            //console.log("hitting starboard 4");
            return target.sideDefense;
        }

        return target.sideDefense;
    },
	
    targetBallistic: function targetBallistic(ship, ball) {
        console.log("target Ballistics", ship, ball);

        if (gamedata.gamephase !== 3) return;

        var selectedShip = ship;
        if (shipManager.isDestroyed(selectedShip)) return;

        if (!ball.targetid) return;

        var target = gamedata.getShip(ball.targetid);

        var toUnselect = Array();

        for (var i in gamedata.selectedSystems) {
            var weapon = gamedata.selectedSystems[i];

            if (ball.targetid !== selectedShip.id && !weapon.freeintercept && !shipManager.isEscorting(selectedShip, target)) continue;

            if (ball.targetid !== selectedShip.id && weapon.freeintercept) {

                var ballPosHex = new hexagon.Offset(ball.position);
                var targetPosHex = shipManager.getShipPosition(target);
                var selectedPosHex = shipManager.getShipPosition(selectedShip);

                if (ballPosHex.distanceTo(targetPosHex) <= ballPosHex.distanceTo(selectedPosHex) || targetPosHex.distanceTo(selectedPosHex) > 3) continue;
            }
            if (shipManager.systems.isDestroyed(selectedShip, weapon) || !weaponManager.isLoaded(weapon)) continue;
            if (weapon.getInterceptRating() === 0) continue;

            var type = 'intercept';

            if (weaponManager.isPosOnWeaponArc(selectedShip, ball.position, weapon)) {
                weaponManager.removeFiringOrder(selectedShip, weapon);

                var damageClass = weapon.data["Weapon type"].toLowerCase();
                var chance = weaponManager.calculataBallisticHitChange(ball, -1);

                for (var s = 0; s < weapon.guns; s++) {
                    weapon.fireOrders.push({
                        id: null,
                        type: type,
                        shooterid: selectedShip.id,
                        targetid: ball.fireOrderId,
                        weaponid: weapon.id,
                        calledid: -1,
                        turn: gamedata.turn,
                        firingMode: weapon.firingMode,
                        shots: weapon.defaultShots,
                        x: "null",
                        y: "null",
                        damageclass: weapon.data["Weapon type"].toLowerCase(),
                        chance: chance
                    });
                }
                toUnselect.push(weapon);
            }
        }

        for (var i in toUnselect) {
            weaponManager.unSelectWeapon(selectedShip, toUnselect[i]);
        }

        gamedata.shipStatusChanged(selectedShip);
    },

    canSelfIntercept: function canSelfIntercept(ship) {
        for (var i in gamedata.selectedSystems) {
            var weapon = gamedata.selectedSystems[i];
		var loadingTimeActual = Math.max(weapon.loadingtime,weapon.normalload);//Accelerator (or multi-mode) weapons may have loading time of 1, yet reach full potential only after longer charging 
            if (weaponManager.isLoaded(weapon) && weapon.intercept >= 1 && loadingTimeActual > 1) {
                return true;
            }
        }
        return false;
    },

    checkSelfIntercept: function checkSelfIntercept(ship) {
        var invalid = [];
        var valid = [];

        for (var i in gamedata.selectedSystems) {
            var weapon = gamedata.selectedSystems[i];

            if (weaponManager.hasFiringOrder(ship, weapon)) {
                weaponManager.removeFiringOrder(ship, weapon);
            }
		
		var loadingTimeActual = Math.max(weapon.loadingtime,weapon.normalload);//Accelerator (or multi-mode) weapons may have loading time of 1, yet reach full potential only after longer charging
            if (weaponManager.isLoaded(weapon) && weapon.intercept >= 1 && loadingTimeActual > 1) {
                valid.push(weapon);
            } else invalid.push(weapon);
        }

        if (valid.length > 0) {
            weaponManager.confirmSelfIntercept(ship, valid, invalid, "Do you want to order the selected weapons to intercept incoming fire ?");
        }
    },

    confirmSelfIntercept: function confirmSelfIntercept(ship, valid, invalid, message) {
        confirm.confirmWithOptions(message, "Yessss", "Nope", function (response) {
            if (response) {
                weaponManager.setSelfIntercept(ship, valid);
                for (var i in invalid) {
                    weaponManager.unSelectWeapon(ship, invalid[i]);
                }
            }
        });
    },

	/*check whether a long-recharge weapon is eligible for interception*/
    canSelfInterceptSingle: function checkSelfIntercept(ship, weapon) {
	if (gamedata.gamephase != 3 ) return false;//declaration in firing phase only
	if (!weapon.weapon) return false;//only weapons can intercept ;)
		var loadingTimeActual = Math.max(weapon.loadingtime,weapon.normalload);//Accelerator (or multi-mode) weapons may have loading time of 1, yet reach full potential only after longer charging
	if ( (weapon.intercept < 1) || (loadingTimeActual <= 1) ) return false;//cannot intercept or quick to recharge anyway and will be auto-assigned
	if (weapon.ballistic) return false;//no interception using ballistic weapons    
	if (weaponManager.hasFiringOrder(ship, weapon)) return false;//already declared
	if (!weaponManager.isLoaded(weapon)) return false;//not ready to fire
	return true;
    },	
	
    onDeclareSelfInterceptSingle: function onDeclareSelfInterceptSingle(ship, weapon) {
	    if(!weaponManager.canSelfInterceptSingle(ship, weapon)) return; //last check whether weapon is eligible for that!
	    var fireid = ship.id + "_" + weapon.id + "_" + (weapon.fireOrders.length + 1);
	    var fire = {
		id: fireid,
		type: "selfIntercept",
		shooterid: ship.id,
		targetid: ship.id,
		weaponid: weapon.id,
		calledid: -1,
		turn: gamedata.turn,
		firingMode: weapon.firingMode,
		shots: weapon.defaultShots,
		x: "null",
		y: "null",
		addToDB: true,
		damageclass: weapon.data["Weapon type"].toLowerCase()
	    };
	    weapon.fireOrders.push(fire);
	    weaponManager.unSelectWeapon(ship, weapon);
    },
	
	/*declare self-intercept for all similar undeclared weapons*/
    onDeclareSelfInterceptSingleAll: function onDeclareSelfInterceptSingleAll(ship, weapon) {
		var allWeapons = [];
        if (ship.flight) {
            allWeapons = ship.systems
                .map(fighter => fighter.systems)
                .reduce((all, weapons) => all.concat(weapons), [])
                .filter(system => system.weapon);
        } else {
            allWeapons = ship.systems.filter(system => system.weapon);
        }
		var similarWeapons = new Array();
		for (var i = 0; i < allWeapons.length; i++) {
			if (weapon.displayName === allWeapons[i].displayName) { //this will include this particular system too, of course
				similarWeapons.push(allWeapons[i]);
			}
		}
		for (var i = 0; i < similarWeapons.length; i++) {
			var otherWeapon = similarWeapons[i];
			weaponManager.onDeclareSelfInterceptSingle(ship, otherWeapon); //will check whether weapon is actually eligible for such declaration
		}
    },	


    setSelfIntercept: function setSelfIntercept(ship, valid) {
        for (var weapon in valid) {
            var weapon = valid[weapon];

            var fireid = ship.id + "_" + weapon.id + "_" + (weapon.fireOrders.length + 1);

            var fire = {
                id: fireid,
                type: "selfIntercept",
                shooterid: ship.id,
                targetid: ship.id,
                weaponid: weapon.id,
                calledid: -1,
                turn: gamedata.turn,
                firingMode: weapon.firingMode,
                shots: weapon.defaultShots,
                x: "null",
                y: "null",
                addToDB: true,
                damageclass: weapon.data["Weapon type"].toLowerCase()
            };

            weapon.fireOrders.push(fire);
            weaponManager.unSelectWeapon(ship, weapon);
        }
        //	gamedata.shipStatusChanged(ship);
    },
	
	
    //system is for called shot!
    targetShip: function targetShip(selectedShip, ship, system) {
        var debug = false;

        debug && console.log("weaponManager target ship", ship, system);

        if (shipManager.isDestroyed(selectedShip)) return;

        var toUnselect = [];
        for (var i in gamedata.selectedSystems) {
            var weapon = gamedata.selectedSystems[i];

            if (shipManager.systems.isDestroyed(selectedShip, weapon) || !weaponManager.isLoaded(weapon)) {
                debug && console.log("Weapon destroyed or not loaded");
                continue;
            }

            if (weapon.hextarget) {
                debug && console.log("This weapon targets only hexagons");
                continue;
            }

            if (weapon.ballistic && gamedata.gamephase != 1) {
                debug && console.log("trying to fire in wrong phase for ballistic weapon");
                continue;
            }
            if (!weapon.ballistic && gamedata.gamephase != 3) {
                debug && console.log("trying to fire in wrong phase for normal weapon");
                continue;
            }

            if (weapon.ballistic && system) {
                debug && console.log("trying to call shot with ballistic");
                continue;
            }

            if (weaponManager.checkConflictingFireOrder(selectedShip, weapon, true)) {
                debug && console.log("has conflicting fire orders");
                for (var j = gamedata.selectedSystems.length - 1; j >= 0; j--) {
                    var sel_weapon = gamedata.selectedSystems[j];
                    weaponManager.removeFiringOrder(selectedShip, sel_weapon);
                    weaponManager.unSelectWeapon(selectedShip, sel_weapon);
                }
                return;
            }

            if (ship.flight && weapon.fireControl[0] === null) {
                debug && console.log("cant fire flight");
                continue;
            }

            if (!ship.flight && ship.shipSizeClass < 2 && weapon.fireControl[1] === null) {
                debug && console.log("can't fire small ships");
                continue;
            }

            if (ship.shipSizeClass >= 2 && weapon.fireControl[2] === null) {
                debug && console.log("can't fire big ships");
                continue;
            }

            var type = 'normal';
            if (weapon.ballistic) {
                type = 'ballistic';
            }

            if (weaponManager.isOnWeaponArc(selectedShip, ship, weapon)) {
                debug && console.log("is on arc");
                if (weaponManager.checkIsInRange(selectedShip, ship, weapon)) {
                    debug && console.log("is in range");
                    weaponManager.removeFiringOrder(selectedShip, weapon);
                    for (var s = 0; s < weapon.guns; s++) {
                        var fireid = selectedShip.id + "_" + weapon.id + "_" + (weapon.fireOrders.length + 1);
                        var calledid = -1;

                        if (system) {
                            //check if weapon is eligible for called shot!
                            if (!weaponManager.canWeaponCall(weapon)) continue;

                            // When the system is a subsystem, make all damage go through
                            // the parent.
                            while (system.parentId > 0) {
                                system = shipManager.systems.getSystem(ship, system.parentId);
                            }

                            calledid = system.id;
                        }

                        var damageClass = weapon.data["Weapon type"].toLowerCase();
                        var chance = weaponManager.calculateHitChange(selectedShip, ship, weapon, calledid);

                        if ((chance < 1)&&(!weapon.ballistic)) {//now ballistics can be launched when hit chance is 0 or less - important for Packet Torpedo!
                            //debug && console.log("Can't fire, change < 0");
                            continue;
                        }

                        var fire = {
                            id: fireid,
                            type: type,
                            shooterid: selectedShip.id,
                            targetid: ship.id,
                            weaponid: weapon.id,
                            calledid: calledid,
                            turn: gamedata.turn,
                            firingMode: weapon.firingMode,
                            shots: weapon.defaultShots,
                            x: "null",
                            y: "null",
                            damageclass: damageClass,
                            chance: chance
                        };
                        weapon.fireOrders.push(fire);
			toUnselect.push(weapon);
                    }

		    //Marcin Sawicki: moving this statement so only weapons actually declared are unselected
                    //toUnselect.push(weapon);
                }
            }
        }

        for (var i in toUnselect) {
            weaponManager.unSelectWeapon(selectedShip, toUnselect[i]);
        }

        webglScene.customEvent('ShipTargeted', {shooter: selectedShip, target: ship, weapons: toUnselect})
    },

	    
    checkIsInRange: function checkIsInRange(shooter, target, weapon) {
        var range = weapon.range;
        var distance = mathlib.getDistanceBetweenShipsInHex(shooter, target).toFixed(2);

        var stealthSystem = shipManager.systems.getSystemByName(target, "stealth");

        if (stealthSystem && distance > 5 && weapon.ballistic) {
            return false;
        }

        if (range === 0) return true;

        var jammer = shipManager.systems.getSystemByName(target, "jammer");
		if (jammer)
		{
			//check whether it was enabled last turn... if so, allow missile launch :)
			if (!shipManager.power.isOfflineOnTurn(target, jammer, (gamedata.turn-1) )){
				/*Improved/Advanced Sensors effect*/
				var jammerValue = shipManager.systems.getOutput(target, jammer);
				if (shipManager.hasSpecialAbility(shooter,"AdvancedSensors") || shipManager.systems.getSystemByName(shooter, "fighteradvsensors")) {
					jammerValue = 0; //negated
				} else if (shipManager.hasSpecialAbility(shooter,"ImprovedSensors") || shipManager.systems.getSystemByName(shooter, "fighterimprsensors")) {
					jammerValue = jammerValue * 0.5; //halved
				}
				range = range / (1+jammerValue);
				//range = range / (shipManager.systems.getOutput(target, jammer)+1);
			}
		}

        return distance <= range;
    },

	    
    targetHex: function targetHex(selectedShip, hexpos) {
        if (shipManager.isDestroyed(selectedShip)) return;

        var toUnselect = Array();
        for (var i in gamedata.selectedSystems) {
            var weapon = gamedata.selectedSystems[i];

            if (shipManager.systems.isDestroyed(selectedShip, weapon) || !weaponManager.isLoaded(weapon)) continue;

            if (shipManager.power.isOffline(selectedShip, weapon)) {
                toUnselect.push(weapon);
                continue;
            }

            if (!weapon.hextarget) {
                continue;
            }

            if (weapon.ballistic && gamedata.gamephase != 1) {
                continue;
            }
            if (!weapon.ballistic && gamedata.gamephase != 3) {
                continue;
            }

            if (weaponManager.checkConflictingFireOrder(selectedShip, weapon)) {
                continue;
            }

            var type = 'normal';
            if (weapon.ballistic) {
                type = 'ballistic';
            }

            if (weaponManager.isPosOnWeaponArc(selectedShip, hexpos, weapon)) {
                if (weapon.range === 0 || shipManager.getShipPosition(selectedShip).distanceTo(hexpos) <= weapon.range) {
                    weaponManager.removeFiringOrder(selectedShip, weapon);
                    for (var s = 0; s < weapon.guns; s++) {

                        var fireid = selectedShip.id + "_" + weapon.id + "_" + (weapon.fireOrders.length + 1);
                        var fire = {
                            id: fireid,
                            type: type,
                            shooterid: selectedShip.id,
                            targetid: -1,
                            weaponid: weapon.id,
                            calledid: -1,
                            turn: gamedata.turn,
                            firingMode: weapon.firingMode,
                            shots: weapon.defaultShots,
                            x: hexpos.q,
                            y: hexpos.r,
                            damageclass: weapon.data["Weapon type"].toLowerCase()
                        };
                        weapon.fireOrders.push(fire);
                    }

                    toUnselect.push(weapon);
                }
            }
        }

        for (var i in toUnselect) {
            weaponManager.unSelectWeapon(selectedShip, toUnselect[i]);
        }

        
        webglScene.customEvent('HexTargeted', {shooter: selectedShip, hexagon: hexpos})
    },

    removeFiringOrder: function removeFiringOrder(ship, system) {

        for (var i = system.fireOrders.length - 1; i >= 0; i--) {
            if (system.fireOrders[i].weaponid == system.id) {
                system.fireOrders.splice(i, 1);
            }
        }

        webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
    },

	    
    hasFiringOrder: function hasFiringOrder(ship, system) {
        for (var i in system.fireOrders) {
            var fire = system.fireOrders[i];
            if (fire.weaponid == system.id && fire.turn == gamedata.turn && !fire.rolled) {
                if ((gamedata.gamephase == 1 || gamedata.gamephase == 3) && system.ballistic || gamedata.gamephase == 3 && !system.ballistic) {
                    if (fire.type == "selfIntercept") {
                        return "self";
                    } else return true;
                }
            }
        }

        if (system.duoWeapon) {
            for (var i in system.weapons) {
                if (weaponManager.hasFiringOrder(ship, system.weapons[i])) {
                    return true;
                }
            }
        }

        return false;
    },

	    
    shipHasFiringOrder: function shipHasFiringOrder(ship) {
        //TODO:implement
    },

	    
    canCombatTurn: function canCombatTurn(ship) {
        var fires = weaponManager.getAllFireOrders(ship);
        for (var i in fires) {
            var fire = fires[i];
            var weapon = shipManager.systems.getSystem(ship, fire.weaponid);
            if (fire.turn == gamedata.turn && !fire.rolled && !weapon.ballistic) {
                return false;
            }
        }

        return true;
    },

	    
    getFiringOrder: function getFiringOrder(ship, system) {
        var fires = weaponManager.getAllFireOrders(ship);
        for (var i in fires) {
            var fire = fires[i];
            if (fire.weaponid == system.id && fire.turn == gamedata.turn && !fire.rolled) return fire;
        }

        return false;
    },

	    
    getAllFireOrders: function getAllFireOrders(ship) {
        var fires = new Array();
        for (var i in ship.systems) {
            if (ship.flight) {
                var fighter = ship.systems[i];
                for (var a in fighter.systems) {
                    var system = fighter.systems[a];
                    var sysFires = weaponManager.getAllFireOrdersFromSystem(system);
                    if (sysFires) fires = fires.concat(sysFires);
                }
            } else {
                var system = ship.systems[i];
                var sysFires = weaponManager.getAllFireOrdersFromSystem(system);
                if (sysFires) fires = fires.concat(sysFires);
            }
        }
        return fires;
    },

	    
    getAllBallisticsAgainst: function getAllBallisticsAgainst(ships, hex) {
        ships = [].concat(ships);

        return gamedata.ships.reduce(function (fires, shooter) {
            return fires.concat(weaponManager.getAllFireOrders(shooter).filter(function (fire) {

                var targetingShip = ships.some(function (ship) {
                    return ship.id === fire.targetid;
                });

                //TODO: show weapons targeted at hex
                //var targetingHex = fire.targetid === -1 && new hexagon.Offset(fire.x, fire.q).equals(hex);

                return targetingShip; // || targetingHex;
            }));
        }, []).filter(function (fire) {
            return fire.type === "ballistic";
        }).map(function (fireOrder) {
            var shooter = gamedata.getShip(fireOrder.shooterid);
            return {
                id: fireOrder.id,
                fireOrder: fireOrder,
                shooter: shooter,
                weapon: shipManager.systems.getSystem(shooter, fireOrder.weaponid)
            };
        });
    },

    getAllHexTargetedBallistics: function getAllHexTargetedBallistics() { //that's all hex targeted weapons, not just ballistics
        return gamedata.ships.reduce(function (fires, shooter) {
            return fires.concat(weaponManager.getAllFireOrders(shooter).filter(function (fire) {
                return fire.targetid === -1;
            }));
        }, []).filter(function (fire) {
            return fire.rolled !== 0;
        }).map(function (fireOrder) {
            var shooter = gamedata.getShip(fireOrder.shooterid);
            return {
                id: fireOrder.id,
                fireOrder: fireOrder,
                shots: fireOrder.shots,
                shooter: shooter,
                weapon: shipManager.systems.getSystem(shooter, fireOrder.weaponid)
            };
        });
    },

    getAllFireOrdersForDisplayingAgainst: function getAllFireOrdersForDisplayingAgainst(target) {
        return gamedata.ships.reduce(function (fires, shooter) {
            return fires.concat(weaponManager.getAllFireOrders(shooter).filter(function (fire) {
                return fire.targetid === target.id && (fire.type === "normal" || fire.type === "ballistic");
            }));
        }, []).filter(function (fire) {
            return fire.rolled !== 0;
        }).map(function (fireOrder) {
            var shooter = gamedata.getShip(fireOrder.shooterid);
            return {
                id: fireOrder.id,
                fireOrder: fireOrder,
                shots: fireOrder.shots,
                hits: fireOrder.shotshit,
                firingMode: fireOrder.firingMode,
                shooter: shooter,
                weapon: shipManager.systems.getSystem(shooter, fireOrder.weaponid),
                targetSystem: shipManager.systems.getSystem(target, fireOrder.calledid),
                damagesCaused: weaponManager.getDamagesCausedBy(fireOrder).reduce(function (damages, damage) {
                    return damages.concat(damage.damages);
                }, []).map(function (damage) {
                    return {
                        armour: damage.armour,
                        damage: damage.damage,
                        damageclass: damage.damageclass,
                        destroyed: damage.destroyed,
                        system: shipManager.systems.getSystem( gamedata.getShip(damage.shipid), damage.systemid)
                    };
                }),
                intercepts: weaponManager.getInterceptingFiringOrders(fireOrder.id).map(function (intercept) {
                    var interceptShooter = gamedata.getShip(intercept.shooterid);
                    return {
                        fireOrder: intercept,
                        shooter: interceptShooter,
                        weapon: shipManager.systems.getSystem(interceptShooter, intercept.weaponid)
                    };
                })
            };
        }).sort(function (obj1, obj2) {
            if (obj1.weapon.priority !== obj2.weapon.priority) {
                return obj1.weapon.priority - obj2.weapon.priority;
            } else {
                var $val = obj1.shooter.id - obj2.shooter.id;
                if ($val === 0) $val = obj1.id - obj2.id;
                return $val;
            }
        });
    },

    getAllFireOrdersFromSystem: function getAllFireOrdersFromSystem(system) {
        if (!system.weapon) return;

        var fires = system.fireOrders;

        if (system.dualWeapon || system.duoWeapon) {
            for (var i in system.weapons) {
                var weapon = system.weapons[i];

                if (weapon.duoWeapon) {
                    for (var index in weapon.weapons) {
                        var subweapon = weapon.weapons[index];
                        fires = fires.concat(weaponManager.getAllFireOrdersFromSystem(subweapon));
                    }
                } else {
                    fires = fires.concat(weaponManager.getAllFireOrdersFromSystem(weapon));
                }
            }
        }

        return fires;
    },

    getInterceptingFiringOrders: function getInterceptingFiringOrders(id) {
        var intercepts = Array();

        for (var a in gamedata.ships) {
            var ship = gamedata.ships[a];
            var fires = weaponManager.getAllFireOrders(ship);
            for (var i in fires) {
                var fire = fires[i];
                if (fire.targetid == id && fire.turn == gamedata.turn && fire.type == "intercept" || fire.type == "selfIntercept" && fire.targetid == id && fire.turn == gamedata.turn) {
                    intercepts.push(fire);
                }
            }
        }

        return intercepts;
    },

    changeShots: function changeShots(ship, system, mod) {
        var fires = weaponManager.getAllFireOrders(ship);
        for (var i in fires) {
            var fire = fires[i];
            if (fire.weaponid == system.id && fire.turn == gamedata.turn && !fire.rolled) {
                if (gamedata.gamephase == 1 && system.ballistic || gamedata.gamephase == 3 && !system.ballistic) fire.shots += mod;
            }
        }

        shipWindowManager.setDataForSystem(ship, system);
        webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
    },

    getDamagesCausedBy: function getDamagesCausedBy(fire, damages) {

        if (!damages) {
            damages = [];
        }

        for (var i in gamedata.ships) {
            var ship = gamedata.ships[i];
            var list = Array();

            for (var a in ship.systems) {
                var system = ship.systems[a];
                for (var b in system.damage) {
                    var d = system.damage[b];
                    if (d.fireorderid == fire.id) {
                        list.push(d);
                    }
                }
            }

            if (list.length > 0) {
                var found = false;
                for (var a in damages) {
                    var entry = damages[a];
                    if (entry.ship.id == ship.id) {
                        found = true;
                        entry.damages = entry.damages.concat(list);
                    }
                }
                if (!found) damages.push({ ship: ship, damages: list });
            }
        }

        return damages;
    },

    isLoaded: function isLoaded(weapon) {
        return weapon.loadingtime <= weapon.turnsloaded || weapon.loadingtime <= weapon.overloadturns;
    },

    getFireOrderById: function getFireOrderById(id) {

        for (var i in gamedata.ships) {
            for (var a in gamedata.ships[i].fireOrders) {
                var fire = gamedata.ships[i].fireOrders[a];
                if (fire.id == id) return fire;
            }
        }

        return false;
    },

    getFiringWeapon: function getFiringWeapon(weapon, fire) {

        return weapon;
    },

    canRam: function canRam(ship) {
        if (ship.hasOwnProperty("hunterkiller")) {}
    },

    askForRam: function askForRam(target) {

        var selectedShip = gamedata.getSelectedShip();

        confirm.confirmWithOptions("CONFIRM movement ?", "Yup", "Nah, too risky yo", function (respons) {
            if (respons) {
                console.log("ye");
            } else {
                console.log("na");
            }
        });
    },

    getAllFireOrdersForAllShipsForTurn: function getAllFireOrdersForAllShipsForTurn(turn, type) {
        var fires = [];
        gamedata.ships.forEach(function (ship) {
            fires = fires.concat(weaponManager.getAllFireOrders(ship));
        });

        fires = fires.filter(function (fireOrder) {
            return fireOrder.turn == turn;
        });

        if (type) {
            fires = fires.filter(function (fireOrder) {
                return fireOrder.type == type;
            });
        }

        return fires;
    }
};
