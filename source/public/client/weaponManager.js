"use strict";

window.weaponManager = {
    mouseoverTimer: null,
    mouseOutTimer: null,
    mouseoverSystem: null,
    currentSystem: null,
    currentShip: null,

    getWeaponCurrentLoading: function getWeaponCurrentLoading(weapon) {
        if (weapon.duoWeapon) {
            var returnArray = new Array(weapon.weapons[1].getTurnsloaded(), weapon.weapons[2].getTurnsloaded());
            return returnArray;
        }

        return weapon.turnsloaded;
    },

    onModeClicked: function onModeClicked(shipwindow, systemwindow, ship, system) {
        throw new Error("Route trough phase strategy to get selected ship");
        if (!system) return;

        if (gamedata.gamephase != 3 && !system.ballistic) return;

        if (gamedata.gamephase != 1 && system.ballistic) return;

        if (weaponManager.hasFiringOrder(ship, system)) return;

        if (gamedata.isMyShip(ship)) {
            weaponManager.unSelectWeapon(ship, system);

            if (system.dualWeapon) {
                var parentSystem = shipManager.systems.getSystem(ship, system.parentId);
                parentSystem.changeFiringMode();
                shipWindowManager.setDataForSystem(ship, parentSystem);

                var newSystem = parentSystem.weapons[parentSystem.firingMode];

                var parentwindow = shipwindow.find(".parentsystem_" + newSystem.parentId);
                parentwindow.removeClass("system_" + system.id);
                parentwindow.addClass("modes");
                parentwindow.removeClass(system.name);
                shipWindowManager.addDualSystem(ship, parentSystem, parentwindow);
                shipWindowManager.setDataForSystem(ship, newSystem);
                parentwindow.find(".UI").addClass("active");
                parentwindow.find(".UI").focus();
                weaponManager.mouseoverSystem = parentwindow;
                clearTimeout(weaponManager.mouseOutTimer);
                clearTimeout(weaponManager.mouseoverTimer);
                weaponManager.mouseOutTimer = null;
                weaponManager.mouseoverTimer = null;
                systemInfo.showSystemInfo(parentwindow, newSystem, ship, selectedship);
            } else {
                system.changeFiringMode();
                shipWindowManager.setDataForSystem(ship, system);
            }
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
        //if($(this).is(weaponManager.mouseoverSystem) || $(this).is($(".UI"))){

        if (weaponManager.mouseoverTimer != null) {
            clearTimeout(weaponManager.mouseoverTimer);
            weaponManager.mouseoverTimer = null;
        }

        weaponManager.mouseOutTimer = setTimeout(weaponManager.doWeaponMouseout, 50);
        //}
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
        webglScene.customEvent('WeaponMouseOver', {
            ship: weaponManager.currentShip,
            weapon: weapon,
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
        webglScene.customEvent('WeaponMouseOut');
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

        webglScene.customEvent('WeaponUnSelected', { ship: ship, weapon: weapon });
        shipWindowManager.setDataForSystem(ship, weapon);
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
                /* compressed into a single statement above
                if ( weapon.exclusive && (weapon.ballistic==system.ballistic)){
                    if (alert)
                        confirm.error("You cannot fire another weapon at the same time as " +weapon.displayName + ".");
                    return true;
                }
                  if (system.exclusive && (weapon.ballistic==system.ballistic)){
                    if (alert)
                        confirm.error("You cannot fire another weapon at the same time as " +system.displayName + ".");
                    return true;
                }*/
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
                confirm.error("This fighter gun is out of ammunition.");
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

        webglScene.customEvent('WeaponSelected', { ship: ship, weapon: weapon });
        gamedata.selectedSystems.push(weapon);
        shipWindowManager.setDataForSystem(ship, weapon);
    },

    isSelectedWeapon: function isSelectedWeapon(weapon) {
        if ($.inArray(weapon, gamedata.selectedSystems) >= 0) return true;

        return false;
    },

    targetingShipTooltip: function targetingShipTooltip(selectedShip, ship, e, calledid) {
        //e.find(".shipname").html(ship.name);
        var f = $(".targeting", e);
        f.html("");

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
                /*
                if (section[i] == 1){
                    html += "-FRONT-";
                }
                if (section[i] == 2){
                    html += "-AFT-";
                }
                if (section[i] == 3){
                    html += "-PORT-";
                }
                if (section[i] == 4){
                    html += "-STARBORD-";
                }
                */
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

        if (target.flight) return true; //experiment - allow called shots at fighters?...

        var shooterCompassHeading = mathlib.getCompassHeadingOfShip(target, shooter);
        var targetFacing = shipManager.getShipHeadingAngle(target);

        for (var i = 0; i < target.outerSections.length; i++) {
            var currSectionData = target.outerSections[i];
            if (system.location == currSectionData.loc) {
                if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(currSectionData.min, targetFacing), mathlib.addToDirection(currSectionData.max, targetFacing))) {
                    if (currSectionData.call == true) return true;
                }
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


    canCalledshotOld: function canCalledshotOld(target, system) {
        /*Marcin Sawicki, October 2017 - let's disable this function, and use new $outerSections-based*/
        var shooter = gamedata.getSelectedShip();

        if (!shooter) return false;

        var loc = weaponManager.getShipHittingSide(shooter, target);
        if (target.flight) {
            return false;
        } else if (target.base) {
            return true;
        }

        if (target.shipSizeClass == 3) {
            if (system.location == 0 && system.weapon) {
                return true;
            }
            for (var i = 0; i < loc.length; i++) {
                if (system.location == loc[i]) {
                    return true;
                }
            }
            if (target.draziCap && system.name == "thruster" && system.location == 2) {
                for (var i = 0; i < loc.length; i++) {
                    if (loc[i] == 2) {
                        return true;
                    }
                }
            }
        } else if (target.draziHCV) {
            for (var i = 0; i < loc.length; i++) {
                if (system.location == loc[i]) {
                    return true;
                }
            }
            if (system.location == 0 && system.weapon) {
                return true;
            }
            if (system.name == "thruster" && system.location == 0) {
                var thruster = weaponManager.getTargetableThruster(shooter, target);
                if (system.direction == thruster) {
                    return true;
                }
            }
        } else if ( /*target.shipSizeClass == 1 ||*/target.shipSizeClass == 2 && system.name == "thruster" && system.location == 0) {
            var thruster = weaponManager.getTargetableThruster(shooter, target);
            if (system.direction == thruster) {
                return true;
            }
        }
        if ( /*target.shipSizeClass == 1 ||*/target.shipSizeClass == 2) {
            if (system.location == 0 && system.weapon) {
                return true;
            }
            for (var i = 0; i < loc.length; i++) {
                if (system.location == loc[i]) {
                    return true;
                }
            }
        }

        //treat MCVs as one huge PRIMARY section!
        if (target.shipSizeClass == 1) {
            if (system.weapon) {
                return true; //all weapons are targetable if in arc
            }
            if (system.name == "thruster") {
                //all thrusters are targetable if in arc
                var thruster = weaponManager.getTargetableThruster(shooter, target);
                if (system.direction == thruster) {
                    return true;
                }
            }
            return false; //other systems on MCVs are not targetable at all
        }

        return false;
    },

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
        console.log("is on arc");
        var shooterFacing = shipManager.getShipHeadingAngle(shooter);
        var targetCompassHeading = mathlib.getCompassHeadingOfShip(shooter, target);

        var arcs = shipManager.systems.getArcs(shooter, weapon);
        arcs.start = mathlib.addToDirection(arcs.start, shooterFacing);
        arcs.end = mathlib.addToDirection(arcs.end, shooterFacing);
        var oPos = shipManager.getShipPosition(shooter);
        var tPos = shipManager.getShipPosition(target);

        if (weapon.ballistic && oPos.equals(tPos)) return true;

        return mathlib.isInArc(targetCompassHeading, arcs.start, arcs.end);
    },

    calculateRangePenalty: function calculateRangePenalty(distance, weapon) {
        var rangePenalty = weapon.rangePenalty * distance;

        return rangePenalty;
    },

    calculataBallisticHitChange: function calculataBallisticHitChange(ball, calledid) {
        var shooter = gamedata.getShip(ball.shooterid);
        var weapon = shipManager.systems.getSystem(shooter, ball.weaponid);
        var target = gamedata.getShip(ball.targetid);

        if (shooter.flight) {
            return weaponManager.calculateFighterBallisticHitChange(shooter, target, weapon, calledid);
        }

        if (!ball.targetid) return false;

        var distance = mathlib.getDistanceBetweenShipsInHex(shooter, target).toFixed(2);

        var rangePenalty = weaponManager.calculateRangePenalty(distance, weapon);

        var defence = weaponManager.getShipDefenceValuePos(ball.position, target);
        var baseDef = weaponManager.calculateBaseHitChange(target, defence, shooter, weapon);

        var soew = ew.getSupportedOEW(shooter, target);
        var dist = ew.getDistruptionEW(shooter);

        var oew = 0;

        if (weapon.useOEW) {
            oew = ew.getTargetingEW(shooter, target);
            oew -= dist;

            if (oew < 0) oew = 0;
        }

        var firecontrol = weaponManager.getFireControl(target, weapon);

        var intercept = weaponManager.getInterception(ball);

        var mod = 0;

        mod -= target.getHitChangeMod(shooter, ball.position);

        if (!shooter.flight && !shooter.osat) mod -= shipManager.criticals.hasCritical(shipManager.systems.getSystemByName(shooter, "cnC"), "PenaltyToHit");

        if (shooter.osat && shipManager.movement.hasTurned(shooter)) {
            mod -= 1;
        }

        if (calledid) mod -= 8;

        var ammo = weapon.getAmmo(weaponManager.getFireOrderById(ball.fireOrderId));
        if (ammo) mod += ammo.hitChanceMod;

        var goal = baseDef - rangePenalty - intercept + oew + soew + firecontrol + mod;

        var change = Math.round(goal / 20 * 100);
        //	console.log("rangePenalty: " + rangePenalty + "intercept: " + intercept + " baseDef: " + baseDef + " oew: " + oew + " defence: " + defence + " firecontrol: " + firecontrol + " mod: " +mod+ " goal: " +goal);

        return change;
    },

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

        return base - dew - jink - bdew - sdew;
    },

    /*calculate hit chance for ramming attack - different procedure*/
    /*also, it would be a bit different (simplified) from B5Wars original*/
    calculateRamChance: function(shooter, target, weapon, calledid){
        if (calledid > 0) return 0;//can't call ramming attack!
        if ((!shooter.flight) && (target.flight)) return 0;//ship has no chance to ram a fighter!
        var hitChance = 8; //base: 40%

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

        hitChance = hitChance * 5; //convert d20->d100
        return hitChance;
    }, //endof calculateRamChance

    calculateHitChange: function calculateHitChange(shooter, target, weapon, calledid) {
        var distance = mathlib.getDistanceBetweenShipsInHex(shooter, target).toFixed(2);
        var rangePenalty = weaponManager.calculateRangePenalty(distance, weapon);
        var sPosHex = shipManager.getShipPosition(shooter);
        //var defence = weaponManager.getShipDefenceValuePos(sPosHex, target);
        /*Marcin Sawicki: I _think_ getShipDefenceValue should be used instead*/
        var defence = weaponManager.getShipDefenceValue(shooter, target);

        //console.log("dis: " + dis + " disInHex: " + disInHex + " rangePenalty: " + rangePenalty);

        var baseDef = weaponManager.calculateBaseHitChange(target, defence, shooter, weapon);

        var soew = ew.getSupportedOEW(shooter, target);
        var dist = ew.getDistruptionEW(shooter);

        var oew = 0;

        if (weapon.useOEW) {
            oew = ew.getTargetingEW(shooter, target);
            oew -= dist;
            if (oew < 0) oew = 0;
        }

        var mod = 0;

        mod -= target.getHitChangeMod(shooter);

        if (shooter.flight) {
            //oew = shooter.offensivebonus;

            //Abbai critical...
            //var firstFighter = shipManager.systems.getSystem(shooter, 1); //should be the same as below...
            var firstFighter = shooter.systems[1];
            var OBcrit = shipManager.criticals.hasCritical(firstFighter, "tmpsensordown");
            oew = shooter.offensivebonus - OBcrit;
            oew = Math.max(0, oew); //OBCrit cannot bring Offensive Bonus below 0

            mod -= shipManager.movement.getJinking(shooter);

            if (shipManager.movement.hasCombatPivoted(shooter)) mod--;
        } else {
            /* no longer needed, Piercing mode stats will be included in FC itself
            if (weapon.piercing && weapon.firingMode == 2 && weapon.firingModes[1] !== "Piercing"){
                mod -= 4;
            }
            */

            //			if (shipManager.movement.hasRolled(shooter)){
            if (shipManager.movement.isRolling(shooter)) {
                //		console.log("is rolling -3");
                mod -= 3;
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
            }
        }
        if (calledid > 0) {
            mod += weapon.calledShotMod;
            if (target.base) mod += weapon.calledShotMod; //double penalty vs bases!
        }

        var ammo = weapon.getAmmo(null);
        if (ammo) mod += ammo.hitChanceMod;

        var jammermod = 0;

        //		if (target.flight && distance > 10){
        //			oew = 0;
        //		}

        if (oew < 1 && !shooter.flight) {
            rangePenalty = rangePenalty * 2;
        } else if (shooter.faction != target.faction) {
            var jammer = shipManager.systems.getSystemByName(target, "jammer");
            var stealth = shipManager.systems.getSystemByName(target, "stealth");

            if (jammer && !shipManager.power.isOffline(target, jammer)) jammermod = rangePenalty * shipManager.systems.getOutput(target, jammer);

            if (stealth && mathlib.getDistanceBetweenShipsInHex(shooter, target) > 5) jammermod = rangePenalty;

            if (target.flight) {
                var jinking = shipManager.movement.getJinking(target);
                if (jinking > jammermod) {
                    jammermod = 0;
                } else {
                    jammermod = jammermod - jinking;
                }
            }
        }

        var firecontrol = weaponManager.getFireControl(target, weapon);

        var goal = baseDef - jammermod - rangePenalty + oew + soew + firecontrol + mod;

        var change = Math.round(goal / 20 * 100);
        //	console.log("rangePenalty: " + rangePenalty + "jammermod: "+jammermod+" baseDef: " + baseDef + " oew: " + oew + " soew: "+soew+" firecontrol: " + firecontrol + " mod: " +mod+ " goal: " +goal);
        //	console.log(change);

        return change;
    },

    calculateFighterBallisticHitChange: function calculateFighterBallisticHitChange(shooter, target, weapon, calledid) {
        var distance = mathlib.getDistanceBetweenShipsInHex(shooter, target).toFixed(2);
        var rangePenalty = weaponManager.calculateRangePenalty(distance, weapon);
        var sPosHex = shipManager.getShipPosition(shooter);
        var tPosHex = shipManager.getShipPosition(target);
        var defence = weaponManager.getShipDefenceValuePos(sPosHex, target);
        //console.log("dis: " + dis + " disInHex: " + disInHex + " rangePenalty: " + rangePenalty);
        var baseDef = weaponManager.calculateBaseHitChange(target, defence, shooter, weapon);

        var soew = ew.getSupportedOEW(shooter, target);
        var dist = ew.getDistruptionEW(shooter);

        var oew = 0;

        if (weapon.useOEW) {
            oew = ew.getTargetingEW(shooter, target);
            oew -= dist;

            if (oew < 0) oew = 0;
        }

        var mod = 0;

        mod -= target.getHitChangeMod(shooter);

        if (shooter.hasNavigator || weaponManager.isPosOnWeaponArc(shooter, tPosHex, weapon)) {
            oew = shooter.offensivebonus;
        }

        mod -= shipManager.movement.getJinking(shooter);

        if (shipManager.movement.hasCombatPivoted(shooter)) {
            mod--;
        }

        if (calledid) {
            //hmm... ballistics shouldn't be able to do called shots...
            mod += weapon.calledShotMod;
        }

        var ammo = weapon.getAmmo(null);
        if (ammo) {
            mod += ammo.hitChanceMod;
        }

        var jammermod = 0;
        if (shooter.faction !== target.faction) {
            var jammer = shipManager.systems.getSystemByName(target, "jammer");
            var stealth = shipManager.systems.getSystemByName(target, "stealth");

            if (jammer && !shipManager.power.isOffline(target, jammer)) jammermod = rangePenalty * shipManager.systems.getOutput(target, jammer);

            if (stealth && mathlib.getDistanceBetweenShipsInHex(shooter, target) > 5) jammermod = rangePenalty;

            if (target.flight) {
                var jinking = shipManager.movement.getJinking(target);
                if (jinking > jammermod) {
                    jammermod = 0;
                } else {
                    jammermod = jammermod - jinking;
                }
            }
        }

        var firecontrol = weaponManager.getFireControl(target, weapon);

        var goal = baseDef - jammermod - rangePenalty + oew + soew + firecontrol + mod;

        var change = Math.round(goal / 20 * 100);
        //console.log("rangePenalty: " + rangePenalty + "jammermod: "+jammermod+" baseDef: " + baseDef + " oew: " + oew + " soew: "+soew+" firecontrol: " + firecontrol + " mod: " +mod+ " goal: " +goal);

        if (change > 100) change = 100;
        return change;
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

    /* Marcin Sawicki - no longer needed, but leaving just in case!
    getShipHittingSideOld: function(shooter, target){ //Marcin Sawicki, October 2017: change that to new approach!
        var targetFacing = (shipManager.getShipHeadingAngle(target));
        var shooterCompassHeading = mathlib.getCompassHeadingOfShip(target,shooter);
          if (target.base){
            return [1, 41, 42, 2, 31, 32];
        }
           else if (target.draziCap){
             if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(330, targetFacing), mathlib.addToDirection(30, targetFacing))){
                if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(210, targetFacing), mathlib.addToDirection(330, targetFacing))){
                    return [1, 3]
                }
                if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(30, targetFacing), mathlib.addToDirection(150, targetFacing))){
                    return [1, 4]
                }
                return [1];
            }
            if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(150, targetFacing), mathlib.addToDirection(210, targetFacing))){
                if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(210, targetFacing), mathlib.addToDirection(330, targetFacing))){
                    return [2, 3]
                }
                if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(30, targetFacing), mathlib.addToDirection(150, targetFacing))){
                    return [2, 4]
                }
                return [2];
            }
            if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(210, targetFacing), mathlib.addToDirection(330, targetFacing))){
                return [3];
            }
            if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(30, targetFacing), mathlib.addToDirection(150, targetFacing))){
                return [4];
            }
              else return [0]
        }
          else if (target.draziHCV){
            if 	(mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(180, targetFacing), mathlib.addToDirection(360, targetFacing)) &&
                (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(0, targetFacing), mathlib.addToDirection(180, targetFacing)))){
                return [3, 4];
                }
            if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(180, targetFacing), mathlib.addToDirection(360, targetFacing))){
                return [3];
            }
            if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(0, targetFacing), mathlib.addToDirection(180, targetFacing))){
                return [4];
            }
        }
          else if (target.shipSizeClass > 0 && target.shipSizeClass < 3){
            if  (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(90, targetFacing), mathlib.addToDirection(270, targetFacing)) &&
                (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(270, targetFacing), mathlib.addToDirection(90, targetFacing)))){
                return [1, 2];
            }
            if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(270, targetFacing), mathlib.addToDirection(90, targetFacing))){
                return [1];
            }
            if ( mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(90, targetFacing), mathlib.addToDirection(270, targetFacing))){
                return [2];
            }
        }
          else if (target.shipSizeClass == 3){
             if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(330, targetFacing), mathlib.addToDirection(30, targetFacing))){
                if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(210, targetFacing), mathlib.addToDirection(330, targetFacing))){
                    return [1, 3]
                }
                if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(30, targetFacing), mathlib.addToDirection(150, targetFacing))){
                    return [1, 4]
                }
                return [1];
            }
            if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(150, targetFacing), mathlib.addToDirection(210, targetFacing))){
                if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(210, targetFacing), mathlib.addToDirection(330, targetFacing))){
                    return [2, 3]
                }
                if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(30, targetFacing), mathlib.addToDirection(150, targetFacing))){
                    return [2, 4]
                }
                return [2];
            }
            if ( mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(210, targetFacing), mathlib.addToDirection(330, targetFacing))){
                return [3];
            }
            if ( mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(30, targetFacing), mathlib.addToDirection(150, targetFacing))){
                return [4];
            }
        }
          return 0;
    },
    */

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
    /*
    canIntercept: function(ball){
        var selectedShip = gamedata.getSelectedShip();
        if (shipManager.isDestroyed(selectedShip))
            return false;
          if (!ball.targetid || ball.targetid != selectedShip.id)
            return false;
          for (var i in gamedata.selectedSystems){
            var weapon = gamedata.selectedSystems[i];
              if (shipManager.systems.isDestroyed(selectedShip, weapon) || !weaponManager.isLoaded(weapon))
                continue;
              if (weaponManager.isPosOnWeaponArc(selectedShip, ball.position, weapon)){
                return true;
            }
        }
          return false;
    },
    */
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

            if (weaponManager.isLoaded(weapon) && weapon.intercept >= 1 && weapon.loadingtime > 1) {
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

            if (weaponManager.isLoaded(weapon) && weapon.intercept >= 1 && weapon.loadingtime > 1) {
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
        var debug = true;

        debug && console.log("weaponManager target ship", ship, system);

        if (shipManager.isDestroyed(selectedShip)) return;

        var toUnselect = Array();
        for (var i in gamedata.selectedSystems) {
            var weapon = gamedata.selectedSystems[i];

            if (shipManager.systems.isDestroyed(selectedShip, weapon) || !weaponManager.isLoaded(weapon)) {
                debug && console.log("Weapon destroyed or not loaded");
                continue;
            }

            if (!weapon.targetsShips) {
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

                        if (chance < 1) {
                            debug && console.log("Can't fire, change < 0");
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
                    }
                    if (weapon.ballistic) {
                        gamedata.ballistics.push({
                            id: gamedata.ballistics.length,
                            fireid: fireid,
                            position: shipManager.getShipPosition(selectedShip),
                            facing: shipManager.movement.getLastCommitedMove(selectedShip).facing,
                            targetposition: { x: null, y: null },
                            targetid: ship.id,
                            shooterid: selectedShip.id,
                            weaponid: weapon.id,
                            shots: fire.shots
                        });
                    }
                    toUnselect.push(weapon);
                }
            }
        }

        for (var i in toUnselect) {
            weaponManager.unSelectWeapon(selectedShip, toUnselect[i]);
        }

        gamedata.shipStatusChanged(selectedShip);
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
				range = range / (shipManager.systems.getOutput(target, jammer)+1);
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

            if (weapon.targetsShips) {
                continue;
            }

            if (weapon.ballistic && gamedata.gamephase != 1) {
                continue;
            }
            if (!weapon.ballistic && gamedata.gamephase != 3) {
                continue;
            }

            if (!weapon.hextarget) continue;

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

        gamedata.shipStatusChanged(selectedShip);
    },

    removeFiringOrder: function removeFiringOrder(ship, system) {

        for (var i = system.fireOrders.length - 1; i >= 0; i--) {
            if (system.fireOrders[i].weaponid == system.id) {

                for (var a = gamedata.ballistics.length - 1; a >= 0; a--) {
                    if (gamedata.ballistics[a].fireid == system.fireOrders[i].id && gamedata.ballistics[a].shooterid == ship.id) {
                        var id = gamedata.ballistics[a].id;

                        $('#ballistic_launch_canvas_' + id).remove();
                        $('#ballistic_target_canvas_' + id).remove();
                        $('.ballistic_' + id).remove();
                        gamedata.ballistics.splice(a, 1);
                    }
                }
                system.fireOrders.splice(i, 1);
            }
        }
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

    getAllHexTargetedBallistics: function getAllHexTargetedBallistics() {
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
                        system: shipManager.systems.getSystem(target, damage.systemid)
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