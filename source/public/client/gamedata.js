"use strict";

window.gamedata = {

    gamewidth: 1600,
    gameheight: 1000,
    zoom: 0.6,
    zoomincrement: 0.1,
    scroll: { x: 0, y: 0 },
    scrollOffset: { x: 0, y: 0 },
    animating: false,
    ships: Array(),
    ballistics: Array(),
    thisplayer: -1,
    waiting: false,
    selectedShips: Array(),
    targetedShips: Array(),
    selectedSystems: Array(),
    effectsDrawing: false,
    finished: false,
    gamephase: 0,
    subphase: 0,
    selectedSlot: null,
    gamespace: null,
    replay: false,
    playAudio: true, //To allow toggling of audio during Replay.    
    showLoS: false,
    blockedHexes: Array(),

    mouseOverShipId: -1,

    /*
    selectShip: function(ship, add){
        if (!add){
            for (var i in gamedata.selectedShips){
                var s2 = gamedata.selectedShips[i];
                gamedata.unSelectShip(s2);
            }
            gamedata.selectedShips = Array();
            
        }
            
        
        
        if (!gamedata.isSelected(ship)){   
            gamedata.selectedShips.push(ship);
            
            gamedata.shipStatusChanged(ship);
            shipWindowManager.checkIfAnyStatusOpen(ship);
            gamedata.selectedSystems = Array();
           
        } 
        
        
    },
     
    targetShip: function(ship, add){
        if (!add){
            for (var i in gamedata.targetedShips){
                var s2 = gamedata.targetedShips[i];
                gamedata.unTargetShip(s2);
            }
            gamedata.targetedShips = Array();
            
        }
            
        
        
        if (!gamedata.isTargeted(ship)){   
            gamedata.targetedShips.push(ship);
            
                
            shipWindowManager.checkShipWindow(ship);
        } 
        
    },
    */
    elintShips: Array(),

    getElintShips: function getElintShips() {
        if (gamedata.elintShips.length === 0) {
            for (var i in gamedata.ships) {
                var ship = gamedata.ships[i];
                if (shipManager.isElint(ship)) gamedata.elintShips.push(ship);
            }
        }
        return gamedata.elintShips;
    },
    /*
    unTargetShip: function(ship){
        
    },
    
    unSelectShip: function(ship){
        if (gamedata.gamephase == 3)
            UI.shipMovement.hide();
        gamedata.selectedSystems = Array();
    },
    isTargeted: function(ship){
        if ($.inArray(ship, gamedata.targetedShips) >= 0)
            return true;
            
        return false;
    },
    
    isSelected: function(ship){
        if ($.inArray(ship, gamedata.selectedShips) >= 0)
            return true;
            
        return false;
    },
     */
    getSelectedShip: function getSelectedShip() {

        throw new Error("This won't work anymore. Get ship from phase strategy");

        for (var i in gamedata.selectedShips) {
            return gamedata.selectedShips[i];
        }

        return false;
    },

    getTargetedShip: function getTargetedShip() {
        for (var i in gamedata.targetedShips) {
            return gamedata.targetedShips[i];
        }
    },

    getFirstFriendlyShip: function getFirstFriendlyShip() {
        for (var i in gamedata.ships) {
            var ship = gamedata.ships[i];
            if (gamedata.isMyShip(ship)) {
                return ship;
            }
        }
    },

    getFirstFriendlyShipDeployment: function getFirstFriendlyShipDeployment() {
        for (var i in gamedata.ships) {
            var ship = gamedata.ships[i];

            if (shipManager.getTurnDeployed(ship) > gamedata.turn) continue;

            if (gamedata.isMyShip(ship)) {
                return ship;
            }
        }
    },


    getFirstEnemyShip: function getFirstEnemyShip() {
        for (var i in gamedata.ships) {
            var ship = gamedata.ships[i];
            if (!gamedata.isMyShip(ship)) {
                return ship;
            }
        }
    },

    /*Marcin Sawicki: re-created so there are no dumps during replay...*/
    //TODO: remove this function AND ALL CALLS TO IT (delete or replace by new approach, as appropriate)
    /*commenting out...
    getActiveShip: function getActiveShip() {
        return null;
    },
    */

    getActiveShips: function getActiveShips() {
        if (Array.isArray(gamedata.activeship)) {
            return gamedata.activeship.map(function (id) {
                return gamedata.getShip(id);
            }).filter(function (ship) {
                return ship && !gamedata.isTerrain(ship.shipSizeClass, ship.userid) && !(shipManager.getTurnDeployed(ship) > gamedata.turn);
            });
        } else {
            return [gamedata.getShip(gamedata.activeship)].filter(function (ship) {
                return ship && !gamedata.isTerrain(ship.shipSizeClass, ship.userid) && !(shipManager.getTurnDeployed(ship) > gamedata.turn);
            });
        }
    },

    getMyActiveShips: function getMyActiveShips() {
        return gamedata.getActiveShips().filter(gamedata.isMyShip)
    },

    getShip: function getShip(id) {
        for (var i in gamedata.ships) {
            if (gamedata.ships[i].id == id) {
                return gamedata.ships[i];
            }
        }

        return null;
    },

    isMyShip: function isMyShip(ship) {
        if (gamedata.isTerrain(ship.shipSizeClass, ship.userid) && (gamedata.gamephase !== -1)) return false; //Players can purchase Terrain, and will need to select to deploy it.
        return ship.userid === gamedata.thisplayer;
    },

    isMyorMyTeamShip: function isMyorMyTeamShip(ship) {
        if (gamedata.isTerrain(ship.shipSizeClass, ship.userid) && (gamedata.gamephase !== -1)) return false; //Players can purchase Terrain, and will need to select to deploy it. 
        if (ship.userid === gamedata.thisplayer) return true;
        if (ship.team === gamedata.getPlayerTeam()) return true;

        return false;
    },

    isEnemy: function isEnemy(target, shooter) {
        if (!shooter) {
            throw new Error("You need to give shooter for this one");
        }

        if (gamedata.isTerrain(target.shipSizeClass, target.userid)) {
            return true; // Always treat Terrain as enemies
        }

        return target.team !== shooter.team;
    },

    isTerrain: function isTerrain(shipSizeClass, userid) {
        if (shipSizeClass == 5 || userid == -5) return true;
        return false;

    },

    isMyOrTeamOneShip: function isMyOrTeamOneShip(ship) {
        if (gamedata.isTerrain(ship.shipSizeClass, ship.userid)) {
            return false; // Ensure terrain units are never considered friendly
        }

        if (gamedata.isPlayerInGame()) {
            return ship.team === gamedata.getPlayerTeam();
        } else {
            return ship.team === 1;
        }
    },


    canTargetAlly: function canTargetAlly(ship) {//30 June 2024 - DK - Added for Ally targeting.
        for (var i in gamedata.selectedSystems) {
            if (gamedata.selectedSystems[i].canTargetAllies || gamedata.selectedSystems[i].canTargetAll) return true;
        }
    },

    isPlayerInGame: function isPlayerInGame() {
        if (gamedata.thisplayer === null || gamedata.thisplayer === -1) {
            return false;
        }

        var slot = Object.keys(gamedata.slots).find(function (key) {
            var slot = gamedata.slots[key];
            return slot.playerid === gamedata.thisplayer;
        })
        return Boolean(slot);
    },

    shipStatusChanged: function shipStatusChanged(ship) {
        shipWindowManager.setData(ship);
        gamedata.checkGameStatus();
        window.webglScene.receiveGamedata(this);
    },

    onCommitClicked: function onCommitClicked(e) {

        if (gamedata.waiting == true) return;

        if (gamedata.status == "FINISHED") return;

        // CHECK for Base Rotation
        if (gamedata.gamephase == -1 && gamedata.turn == 1) {
            var bases = [];

            for (var i in gamedata.ships) {
                var ship = gamedata.ships[i];
                if (ship.userid == gamedata.thisplayer) {
                    if (ship.base) {
                        bases.push(ship);
                    }
                }
            }
            if (bases) {
                for (var i = 0; i < bases.length; i++) {
                    if ((bases[i].movement[1].value == 0) && (!bases[i].nonRotating)) {
                        confirm.error("Please setup the rotation of your starbase.", function () { });
                        return false;
                    }
                }
            }
        }

        // CHECK for NO EW
        if (gamedata.gamephase == 1) {
            var myShips = [];

            for (var ship in gamedata.ships) {
                if (gamedata.ships[ship].userid == gamedata.thisplayer) {
                    if (!shipManager.isDestroyed(gamedata.ships[ship]) && !gamedata.isTerrain(gamedata.ships[ship].shipSizeClass, gamedata.ships[ship].userid)) {
                        var deployTurn = shipManager.getTurnDeployed(gamedata.ships[ship]);
                        if (deployTurn <= gamedata.turn) {   //Don't bother checking for ships that haven't deployed yet. 
                            myShips.push(gamedata.ships[ship]);
                        }
                    }
                }
            }

            var hasNoEW = [];
            var selfDestructing = [];
            var jumping = [];
            var notLaunching = [];
            var notSetAA = [];//available Adaptive Armor points remaining!
            var notSetFC = [];//available BFCP points remaining for Hyach!
            var powerSurplus = [];//power surplus

            for (var ship in myShips) {

                if (!myShips[ship].flight) {

                    //loop at systems looking for overloading reactor(s)
                    for (var syst in myShips[ship].systems) {
                        if (myShips[ship].systems[syst].name == "reactor") {
                            for (var pow in myShips[ship].systems[syst].power) {
                                if (myShips[ship].systems[syst].power[pow].turn == gamedata.turn && myShips[ship].systems[syst].power[pow].type == 2) {
                                    selfDestructing.push(myShips[ship]);
                                }
                            }
                        } else if (myShips[ship].systems[syst].name == "jumpEngine") {
                            for (var pow in myShips[ship].systems[syst].power) {
                                if (myShips[ship].systems[syst].power[pow].turn == gamedata.turn && myShips[ship].systems[syst].power[pow].type == 2) {
                                    jumping.push(myShips[ship]);
                                }
                            }
                        } else if (myShips[ship].systems[syst].name == "adaptiveArmorController") {
                            if (myShips[ship].systems[syst].canIncreaseAnything()) {
                                notSetAA.push(myShips[ship]);
                            }
                        } else if (myShips[ship].systems[syst].name == "hyachComputer") {
                            if (myShips[ship].systems[syst].canIncreaseAnything()) {
                                notSetFC.push(myShips[ship]);
                            }
                        }
                    }

                    if (shipManager.isDisabled(myShips[ship])) {
                        continue;
                    }

                    //checking for power surplus
                    if (shipManager.power.getReactorPower(myShips[ship], shipManager.systems.getSystemByName(myShips[ship], "reactor")) > 0) {
                        powerSurplus.push(myShips[ship]);
                    }

                    if (gamedata.turn == 1) {
                        if (myShips[ship].EW.length == 0) {
                            hasNoEW.push(myShips[ship]);
                        }
                    } else if (gamedata.turn > 1) {
                        var hasEW = 0;
                        for (var entry in myShips[ship].EW) {
                            var ew = myShips[ship].EW[entry];
                            if (ew.turn == gamedata.turn && ew.type != "DEW") {
                                hasEW = 1;
                                break;
                            }
                        }
                        if (hasEW == 0) {
                            hasNoEW.push(myShips[ship]);
                        };
                    }

                    //check for ballistic launch
                    var fired = 0;
                    var hasReadyLaunchers = false;
                    for (var i = 0; i < myShips[ship].systems.length; i++) {
                        var currWeapon = myShips[ship].systems[i];
                        if (currWeapon.ballistic) { //only ballistic weapons are of interest now
                            if (currWeapon.fireOrders.length > 0) {
                                fired = 1;
                                break;
                            }
                            if (weaponManager.isLoaded(currWeapon) && (!shipManager.systems.isDestroyed(myShips[ship], currWeapon))
                                && (!weaponManager.checkOutOfAmmo(myShips[ship], currWeapon)) //check for ammo (if relevant - GTS
                            ) { //non-ballistic weapon ready to fire
                                hasReadyLaunchers = true;
                            }
                        }
                    }
                    if ((fired == 0) && hasReadyLaunchers) { //no missile launch was declared, and there are ready launchers
                        notLaunching.push(myShips[ship]);
                    }
                } else { //fighter flight
                    //check for ballistic launch
                    //and Adaptive Armor
                    var fired = 0;
                    var didNotSetAA = false;
                    var hasReadyLaunchers = false;
                    for (var i = 0; i < myShips[ship].systems.length; i++) {
                        if (typeof myShips[ship].systems[i] != "undefined") {
                            for (var j = 0; j < myShips[ship].systems[i].systems.length; j++) {
                                if (typeof myShips[ship].systems[i].systems[j] != "undefined") {
                                    var currWeapon = myShips[ship].systems[i].systems[j];
                                    if ((fired == 0) && currWeapon.ballistic) { //only ballistic weapons are of interest now
                                        if (currWeapon.fireOrders.length > 0) {
                                            fired = 1;
                                            //break;
                                        }
                                        /*
                                        if (weaponManager.isLoaded(currWeapon) ){ //ballistic weapon ready to fire
                                            hasReadyLaunchers = true;
                                        }*/
                                        if (weaponManager.isLoaded(currWeapon) && (!shipManager.systems.isDestroyed(myShips[ship], myShips[ship].systems[i]))
                                            && (!weaponManager.checkOutOfAmmo(myShips[ship], currWeapon)) //check for ammo (if relevant
                                        ) { //non-ballistic weapon ready to fire
                                            hasReadyLaunchers = true;
                                        }
                                    } else if (currWeapon.name == "adaptiveArmorController") {
                                        if (currWeapon.canIncreaseAnything()) {
                                            didNotSetAA = true;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if ((fired == 0) && hasReadyLaunchers) { //no missile launch was declared, and there are ready launchers
                        notLaunching.push(myShips[ship]);
                    }
                    if (didNotSetAA) { //available Adaptive Armor has not been set
                        notSetAA.push(myShips[ship]);
                    }
                }
            }

            /*
                      if (hasNoEW.length == 0){
                          confirm.confirm("Are you sure you wish to COMMIT YOUR INITIAL ORDERS?", gamedata.doCommit);
                      }
                      else {
                          var html = "You have not assigned any EW for the following ships: ";
                              html += "<br>";
                          for (var ship in hasNoEW){
                              html += hasNoEW[ship].name + " (" + hasNoEW[ship].shipClass + ")";
                              html += "<br>";
                          }
                          confirm.confirm((html + "<br>Are you sure you wish to COMMIT YOUR INITIAL ORDERS?"), gamedata.doCommit);
                      }
               */
            var html = '';
            if (selfDestructing.length > 0) {
                html += "You have ordering following ships to SELF DESTRUCT: ";
                html += "<br>";
                for (var ship in selfDestructing) {
                    //html += selfDestructing[ship].name + " (" + selfDestructing[ship].shipClass + ")";
                    html += '<span class="ship-name">' + selfDestructing[ship].name + ' (' + selfDestructing[ship].shipClass + ')</span>';
                    html += "<br>";
                }
                html += "<br>";
            }
            if (jumping.length > 0) {
                html += "You have ordering following ships to JUMP TO HYPERSPACE: ";
                html += "<br>";
                for (var ship in jumping) {
                    //html += jumping[ship].name + " (" + jumping[ship].shipClass + ")";
                    html += '<span class="ship-name">' + jumping[ship].name + ' (' + jumping[ship].shipClass + ')</span>';
                    html += "<br>";
                }
                html += "<br>";
            }
            if (hasNoEW.length > 0) {
                // New check to see if Scanner exists / has positive output before giving warning - DK 01/25
                for (var i = hasNoEW.length - 1; i >= 0; i--) {
                    var ship = hasNoEW[i];
                    const standardScanners = shipManager.systems.getSystemListByName(ship, "scanner");
                    const elintScanners = shipManager.systems.getSystemListByName(ship, "elintScanner");
                    const scanners = [...standardScanners, ...elintScanners];

                    // Check if all scanners for this ship are either destroyed or have output <= 0
                    var allScannersDisabled = scanners.every(function (scanner) {
                        return shipManager.systems.isDestroyed(ship, scanner) ||
                            shipManager.systems.getOutput(ship, scanner) <= 0;
                    });

                    // If all scanners are disabled, remove the ship from hasNoEW
                    if (allScannersDisabled) {
                        hasNoEW.splice(i, 1);
                    }
                }

                //Now check again and give message if hasNoEW length still over 0.            
                if (hasNoEW.length > 0) {
                    html += "You have not assigned any EW for the following ships: ";
                    html += "<br>";
                    for (var ship in hasNoEW) {
                        //html += hasNoEW[ship].name + " (" + hasNoEW[ship].shipClass + ")";
                        html += '<span class="ship-name">' + hasNoEW[ship].name + ' (' + hasNoEW[ship].shipClass + ')</span>';
                        html += "<br>";
                    }
                    html += "<br>";
                }
            }
            if (notLaunching.length > 0) {
                html += "You have not assigned any ballistic launch for the following ships: ";
                html += "<br>";
                for (var ship in notLaunching) {
                    //html += notLaunching[ship].name + " (" + notLaunching[ship].shipClass + ")";
                    html += '<span class="ship-name">' + notLaunching[ship].name + ' (' + notLaunching[ship].shipClass + ')</span>';
                    html += "<br>";
                }
                html += "<br>";
            }
            if (notSetAA.length > 0) {
                html += "You have not assigned available AA points for the following units: ";
                html += "<br>";
                for (var ship in notSetAA) {
                    //html += notSetAA[ship].name + " (" + notSetAA[ship].shipClass + ")";
                    html += '<span class="ship-name">' + notSetAA[ship].name + ' (' + notSetAA[ship].shipClass + ')</span>';
                    html += "<br>";
                }
                html += "<br>";
            }
            if (notSetFC.length > 0) {
                html += "You have not assigned available BFCP points for the following units: ";
                html += "<br>";
                for (var ship in notSetFC) {
                    //html += notSetFC[ship].name + " (" + notSetFC[ship].shipClass + ")";
                    html += '<span class="ship-name">' + notSetFC[ship].name + ' (' + notSetFC[ship].shipClass + ')</span>';
                    html += "<br>";
                }
                html += "<br>";
            }
            if (powerSurplus.length > 0) {
                html += "The following ships have unassigned Power reserves: ";
                html += "<br>";
                for (var ship in powerSurplus) {
                    //show actual surplus, too - like: Surplusser (PowerShip) - <10>
                    var surplusVal = shipManager.power.getReactorPower(powerSurplus[ship], shipManager.systems.getSystemByName(powerSurplus[ship], "reactor"));
                    //html += powerSurplus[ship].name + " (" + powerSurplus[ship].shipClass + "): <b>&#60;" + surplusVal + '&#62;</b>';
                    html += '<span class="ship-name">' + powerSurplus[ship].name + ' (' + powerSurplus[ship].shipClass + '): <b>&#60;' + surplusVal + '&#62;</b></span>';
                    html += "<br>";
                }
                html += "<br>";
            }
            confirm.confirm(html + "<br>Are you sure you wish to COMMIT YOUR INITIAL ORDERS?", gamedata.doCommit);
        }

        else if (gamedata.gamephase == 2) {
            var zeroSpeedShips = [];
            var activeShips = gamedata.getActiveShips();
            var html = '';

            for (var i in activeShips) {
                var ship = activeShips[i];
                if (!gamedata.isTerrain(ship.shipSizeClass, ship.userid)) {
                    if (shipManager.movement.canChangeSpeed(ship, true) && ship.userid == gamedata.thisplayer) {
                        zeroSpeedShips.push(ship);
                    }
                }
            }

            if (zeroSpeedShips.length > 0) {
                html += "<br>";
                html += "The following ships can still move: <br>";

                for (var j in zeroSpeedShips) {
                    var movingShip = zeroSpeedShips[j];
                    html += '<span class="ship-name">' + movingShip.name + '</span><br>';
                }
            }

            UI.shipMovement.hide();

            confirm.confirm(
                html + "<br>Are you sure you wish to COMMIT YOUR MOVEMENT ORDERS?",
                gamedata.doCommit,
                function () {
                    UI.shipMovement.show();
                }
            );

            //CHECK for NO PRE FIRE            
        } else if (gamedata.gamephase == 5) {
            var myShips = [];

            for (var ship in gamedata.ships) {
                if (gamedata.ships[ship].userid == gamedata.thisplayer) {
                    if (!shipManager.isDestroyed(gamedata.ships[ship]) && !gamedata.isTerrain(gamedata.ships[ship].shipSizeClass, gamedata.ships[ship].userid)) {
                        var deployTurn = shipManager.getTurnDeployed(gamedata.ships[ship]);
                        if (deployTurn <= gamedata.turn) {   //Don't bother checking for ships that haven't deployed yet. 
                            myShips.push(gamedata.ships[ship]);
                        }
                    }
                }
            }

            var hasNoFO = [];
            var hasSplitFO = [];

            for (var ship in myShips) {
                var fired = 0;
                var hasReadyGuns = false;
                var hasShotsLeft = false; //For split shot weapons that might not have used al their shots.
                if (!myShips[ship].flight) {
                    for (var i = 0; i < myShips[ship].systems.length; i++) {
                        var currWeapon = myShips[ship].systems[i];
                        if (currWeapon.preFires) {
                            if (!currWeapon.ballistic && currWeapon.weapon && (currWeapon.displayName != "Ramming Attack")) { //ballistic weapons ore of no interest now
                                if (currWeapon.fireOrders.length > 0) {
                                    fired = 1;
                                    if (currWeapon.canSplitShots && currWeapon.fireOrders.length < currWeapon.guns) {
                                        hasShotsLeft = true;
                                    }
                                    break;
                                }
                                if (weaponManager.isLoaded(currWeapon) && (!shipManager.systems.isDestroyed(myShips[ship], currWeapon))
                                    && (!weaponManager.checkOutOfAmmo(myShips[ship], currWeapon)) //check for ammo (if relevant - GTS
                                ) { //non-ballistic weapon ready to fire
                                    hasReadyGuns = true;
                                }
                            }
                        }
                    }
                    if ((fired == 0) && hasReadyGuns) { //no gun was fired, and there are ready guns
                        hasNoFO.push(myShips[ship]);
                    }
                    if (hasShotsLeft) { //Some shots used, but not all.
                        hasSplitFO.push(myShips[ship]);
                    }
                } else if (myShips[ship].flight) {
                    for (var i = 0; i < myShips[ship].systems.length; i++) {
                        if (typeof myShips[ship].systems[i] != "undefined") {
                            for (var j = 0; j < myShips[ship].systems[i].systems.length; j++) {
                                if (typeof myShips[ship].systems[i].systems[j] != "undefined") {
                                    var currWeapon = myShips[ship].systems[i].systems[j];
                                    if (currWeapon.preFires) {
                                        if (!currWeapon.ballistic && currWeapon.weapon && (currWeapon.displayName != "Ramming Attack")) { //ballistic weapons ore of no interest now
                                            if (currWeapon.fireOrders.length > 0) {
                                                fired = 1;
                                                break;
                                            }
                                            if (weaponManager.isLoaded(currWeapon) && (!shipManager.systems.isDestroyed(myShips[ship], myShips[ship].systems[i]))
                                                && (!weaponManager.checkOutOfAmmo(myShips[ship], currWeapon)) //check for ammo (if relevant											
                                            ) { //non-ballistic weapon ready to fire
                                                hasReadyGuns = true;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if ((fired == 0) && hasReadyGuns) { //no gun was fired, and there are ready guns
                        hasNoFO.push(myShips[ship]);
                    }
                }
            }

            if (hasNoFO.length == 0 && hasSplitFO.length == 0) { //Has no ships with no fireOrders at all.
                confirm.confirm("Are you sure you wish to COMMIT YOUR FIRE ORDERS?", gamedata.doCommit);
            } else {
                var html = '';
                if (hasNoFO.length > 0) {
                    html += "You have not assigned any fire orders for the following ships: ";
                    html += "<br>";
                    for (var ship in hasNoFO) {
                        //html += hasNoFO[ship].name + " (" + hasNoFO[ship].shipClass + ")";
                        html += '<span class="ship-name">' + hasNoFO[ship].name + ' (' + hasNoFO[ship].shipClass + ')</span>';
                        html += "<br>";
                    }
                }
                if (hasSplitFO.length > 0) {
                    html += "<br>";
                    html += "The following ships have weapons with unused shots: ";
                    html += "<br>";
                    for (var ship in hasSplitFO) {
                        //html += hasSplitFO[ship].name + " (" + hasSplitFO[ship].shipClass + ")";
                        html += '<span class="ship-name">' + hasSplitFO[ship].name + ' (' + hasSplitFO[ship].shipClass + ')</span>';
                        html += "<br>";
                    }
                }
                confirm.confirm(html + "<br>Are you sure you wish to COMMIT YOUR FIRE ORDERS?", gamedata.doCommit);
            }
        } else if (gamedata.gamephase == 3) {
            var myShips = [];

            for (var ship in gamedata.ships) {
                if (gamedata.ships[ship].userid == gamedata.thisplayer) {
                    if (!shipManager.isDestroyed(gamedata.ships[ship]) && !gamedata.isTerrain(gamedata.ships[ship].shipSizeClass, gamedata.ships[ship].userid)) {
                        var deployTurn = shipManager.getTurnDeployed(gamedata.ships[ship]);
                        if (deployTurn <= gamedata.turn) {   //Don't bother checking for ships that haven't deployed yet. 
                            myShips.push(gamedata.ships[ship]);
                        }
                    }
                }
            }

            var hasNoFO = [];
            var hasSplitFO = [];

            for (var ship in myShips) {
                var fired = 0;
                var hasReadyGuns = false;
                var hasShotsLeft = false; //For split shot weapons that might not have used al their shots.
                if (!myShips[ship].flight) {
                    for (var i = 0; i < myShips[ship].systems.length; i++) {
                        var currWeapon = myShips[ship].systems[i];
                        if (currWeapon.preFires) continue;
                        if (!currWeapon.ballistic && currWeapon.weapon && (currWeapon.displayName != "Ramming Attack")) { //ballistic weapons ore of no interest now
                            if (currWeapon.fireOrders.length > 0) {
                                fired = 1;
                                if (currWeapon.canSplitShots && (currWeapon.fireOrders.length < currWeapon.guns || (currWeapon.checkForWastedShots()))) {
                                    hasShotsLeft = true;
                                }
                                break;
                            }
                            if (weaponManager.isLoaded(currWeapon) && (!shipManager.systems.isDestroyed(myShips[ship], currWeapon))
                                && (!weaponManager.checkOutOfAmmo(myShips[ship], currWeapon)) //check for ammo (if relevant - GTS
                            ) { //non-ballistic weapon ready to fire
                                hasReadyGuns = true;
                            }
                        }
                    }
                    if ((fired == 0) && hasReadyGuns) { //no gun was fired, and there are ready guns
                        hasNoFO.push(myShips[ship]);
                    }
                    if (hasShotsLeft) { //Some shots used, but not all.
                        hasSplitFO.push(myShips[ship]);
                    }
                } else if (myShips[ship].flight) {
                    for (var i = 0; i < myShips[ship].systems.length; i++) {
                        if (typeof myShips[ship].systems[i] != "undefined") {
                            for (var j = 0; j < myShips[ship].systems[i].systems.length; j++) {
                                if (typeof myShips[ship].systems[i].systems[j] != "undefined") {
                                    var currWeapon = myShips[ship].systems[i].systems[j];
                                    if (!currWeapon.ballistic && currWeapon.weapon && (currWeapon.displayName != "Ramming Attack")) { //ballistic weapons ore of no interest now
                                        if (currWeapon.fireOrders.length > 0) {
                                            fired = 1;
                                            break;
                                        }
                                        if (weaponManager.isLoaded(currWeapon) && (!shipManager.systems.isDestroyed(myShips[ship], myShips[ship].systems[i]))
                                            && (!weaponManager.checkOutOfAmmo(myShips[ship], currWeapon)) //check for ammo (if relevant											
                                        ) { //non-ballistic weapon ready to fire
                                            hasReadyGuns = true;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if ((fired == 0) && hasReadyGuns) { //no gun was fired, and there are ready guns
                        hasNoFO.push(myShips[ship]);
                    }
                    UI.shipMovement.hide();  //To hide combat pivot UI again on commit clicked                  
                }
            }

            if (hasNoFO.length == 0 && hasSplitFO.length == 0) { //Has no ships with no fireOrders at all.
                confirm.confirm("Are you sure you wish to COMMIT YOUR FIRE ORDERS?", gamedata.doCommit);
            } else {
                var html = '';
                if (hasNoFO.length > 0) {
                    html += "You have not assigned any fire orders for the following ships: ";
                    html += "<br>";
                    for (var ship in hasNoFO) {
                        //html += hasNoFO[ship].name + " (" + hasNoFO[ship].shipClass + ")";
                        html += '<span class="ship-name">' + hasNoFO[ship].name + ' (' + hasNoFO[ship].shipClass + ')</span>';
                        html += "<br>";
                    }
                }
                if (hasSplitFO.length > 0) {
                    html += "<br>";
                    html += "The following ships have weapons with unused shots: ";
                    html += "<br>";
                    for (var ship in hasSplitFO) {
                        //html += hasSplitFO[ship].name + " (" + hasSplitFO[ship].shipClass + ")";
                        html += '<span class="ship-name">' + hasSplitFO[ship].name + ' (' + hasSplitFO[ship].shipClass + ')</span>';
                        html += "<br>";
                    }
                }
                //confirm.confirm(html + "<br>Are you sure you wish to COMMIT YOUR FIRE ORDERS?", gamedata.doCommit);
                confirm.confirm(
                    html + "<br>Are you sure you wish to COMMIT YOUR MOVEMENT ORDERS?",
                    gamedata.doCommit,
                    function () {
                        UI.shipMovement.show(); //To show combat pivot UI again on Cancel
                    }
                );
            }
        } else if (gamedata.gamephase != 4) {
            confirm.confirm("Are you sure you wish to COMMIT YOUR TURN?", gamedata.doCommit);
            //            if (window.helper.autocomm!=true) {
            //	            confirm.confirm("Are you sure you wish to COMMIT YOUR TURN?", gamedata.doCommit);
            //            } else {
            //            	gamedata.doCommit();
            //            }	
        } else {
            confirm.confirmOrSurrender("Are you sure you wish to COMMIT YOUR TURN?", gamedata.doCommit, gamedata.onSurrenderClicked);
            //            if (window.helper.autocomm!=true) {
            //	            confirm.confirmOrSurrender("Are you sure you wish to COMMIT YOUR TURN?", gamedata.doCommit, gamedata.onSurrenderClicked);
            //            } else {
            //	            confirm.askSurrender("Do you wish to SURRENDER?", gamedata.doCommit, gamedata.onSurrenderClicked);
            //            }	
        }
    },

    onSurrenderClicked: function onSurrenderClicked(e) {
        confirm.confirm("Are you sure you wish to SURRENDER THIS MATCH?", gamedata.doSurrender);
    },

    doSurrender: function doSurrender() {
        UI.shipMovement.hide();

        gamedata.status = "SURRENDERED";
        ajaxInterface.submitGamedata();
    },

    doCommit: function doCommit() {
        UI.shipMovement.hide();

        //DEPLOYMENT PHASE
        if (gamedata.gamephase == -1) {
            shipNames = shipManager.systems.getUnusedSpecialists();

            if (shipNames.length > 0) {
                var specialistsError = "The following ships have not selected Specialists:<br>";

                for (var i in shipNames) {
                    var shipName = shipNames[i];
                    //specialistsError += "- " + shipName + "<br>";
                    specialistsError += '<span class="ship-name">- ' + shipName + '</span><br>';
                }
                specialistsError += "<br>You need to choose Specialists for these ships.";
                window.confirm.error(specialistsError, function () { });
                return false;
            }
            ajaxInterface.submitGamedata();

            //INITIAL ORDERS    
        } else if (gamedata.gamephase == 1) {
            //        	ajaxInterface.fastpolling=true;
            var shipNames = shipManager.power.getShipsNegativePower();

            if (shipNames.length > 0) {
                var negPowerError = "The following ships have insufficient power:<br>";

                for (var index in shipNames) {
                    var name = shipNames[index];
                    //negPowerError += "- " + name + "<br>";
                    negPowerError += '<span class="ship-name">- ' + name + '</span><br>';
                }
                negPowerError += "<br>You need to turn off systems before you can commit the turn.";
                window.confirm.error(negPowerError, function () { });
                return false;
            }

            //We have one thrust-boosted weapon in Initial Orders Phase, let's put in a check for it and future - DK 26.11.24
            shipNames = shipManager.movement.getShipsNegativeThrust();

            if (shipNames.length > 0) {
                var negThrustError = "The following ships have insufficient Engine Thrust:<br>";

                for (var index in shipNames) {
                    var name = shipNames[index];
                    //negThrustError += "- " + name + "<br>";
                    negThrustError += '<span class="ship-name">- ' + name + '</span><br>';
                }
                negThrustError += "<br>You need to lower channelled thrust before you can commit the turn.";
                window.confirm.error(negThrustError, function () { });
                return false;
            }

            shipNames = shipManager.power.getShipsGraviticShield();

            if (shipNames.length > 0) {
                var tooManyShieldsError = "The following ships have too many active shields:<br>";

                for (var i in shipNames) {
                    var shipName = shipNames[i];
                    //tooManyShieldsError += "- " + shipName + "<br>";
                    tooManyShieldsError += '<span class="ship-name">- ' + shipName + '</span><br>';
                }
                tooManyShieldsError += "<br>You need to turn off shields or boost your shield generator before you can commit the turn.";
                window.confirm.error(tooManyShieldsError, function () { });
                return false;
            }

            shipNames = shipManager.systems.getNegativeBFCP();

            if (shipNames.length > 0) {
                var tooManyBFCPError = "The following ships have too many Bonus Fire Control Points (BFCP) set:<br>";

                for (var i in shipNames) {
                    var shipName = shipNames[i];
                    //tooManyBFCPError += "- " + shipName + "<br>";
                    tooManyBFCPError += '<span class="ship-name">- ' + shipName + '</span><br>';
                }
                tooManyBFCPError += "<br>You need to decrease the number of allocated BFCPs.";
                window.confirm.error(tooManyBFCPError, function () { });
                return false;
            }
            /*
            shipNames = shipManager.systems.getUnusedSpecialists();        	

            if (shipNames.length > 0) {
                var specialistsError = "The following ships have not selected Specialists:<br>";
                
                for (var i in shipNames) {
                    var shipName = shipNames[i];
                    //specialistsError += "- " + shipName + "<br>";
                    specialistsError += '<span class="ship-name">- ' + shipName + '</span><br>'; 
                }
                specialistsError += "<br>You need to choose Specialists for these ships.";
                window.confirm.error(specialistsError, function () {});
                return false;                
            }		
            */
            shipNames = shipManager.systems.checkShieldGenValue();

            if (shipNames.length > 0) {
                var shieldCapacityError = "The following ships have directed too much or too little power to their shields:<br>";

                for (var i in shipNames) {
                    var shipName = shipNames[i];
                    //shieldCapacityError += "- " + shipName + "<br>";
                    shieldCapacityError += '<span class="ship-name">- ' + shipName + '</span><br>';
                }
                shieldCapacityError += "<br>You need to change their allocation of shield power.";
                window.confirm.error(shieldCapacityError, function () { });
                return false;
            }

            var myShips = [];

            for (var ship in gamedata.ships) {
                if (gamedata.ships[ship].userid == gamedata.thisplayer) {
                    if (!shipManager.isDestroyed(gamedata.ships[ship]) && !gamedata.isTerrain(gamedata.ships[ship].shipSizeClass, gamedata.ships[ship].userid)) {
                        var deployTurn = shipManager.getTurnDeployed(gamedata.ships[ship]);
                        if (deployTurn <= gamedata.turn) {   //Don't bother checking for ships that haven't deployed yet. 
                            myShips.push(gamedata.ships[ship]);
                        }
                    }
                }
            }

            //ammo usage check - AmmoMagazine equipped units
            var ammoMagazineError = [];
            for (var shipID in myShips) { //actually this will check for fighters, too
                var currShip = myShips[shipID];
                if (!currShip.flight) { //actual ship - check for every magazine on board!			
                    for (var i in currShip.systems) if (currShip.systems[i].name == 'ammoMagazine') {
                        var currMagazine = currShip.systems[i];
                        var checkResult = currMagazine.doVerifyAmmoUsage(currShip);
                        if (!checkResult) ammoMagazineError.push(currShip);
                    }
                } else { //fighter flight - check for every fighter separately!
                    var flightCheckResult = true;
                    for (var j in currShip.systems) for (var i in currShip.systems[j].systems) if (currShip.systems[j].systems[i].name == 'ammoMagazine') {
                        var currMagazine = currShip.systems[j].systems[i];
                        var checkResult = currMagazine.doVerifyAmmoUsageFighter(currShip.systems[j]);
                        if (!checkResult) flightCheckResult = false;
                    }
                    if (!flightCheckResult) ammoMagazineError.push(currShip); //at least one fighter uses nonexisting ammo
                }
            }


            //EW correctness check
            var EWIncorrect = []; //too many EW points set
            var EWRestrictedIncorrect = [];//RestrictedEW critical circumvented
            var EWLCVIncorrect = [];//LCV set too many EW to tasks other than OEW
            for (var shipID in myShips) {
                if (!myShips[shipID].flight) {
                    if (ew.convertUnusedToDEW(myShips[shipID]) != true) {
                        EWIncorrect.push(myShips[shipID]);
                    }
                    if (ew.checkRestrictedEW(myShips[shipID]) != true) {
                        EWRestrictedIncorrect.push(myShips[shipID]);
                    }
                    if (ew.checkLCVSensors(myShips[shipID]) != true) {
                        EWLCVIncorrect.push(myShips[shipID]);
                    }
                }
            }


            //Derelict ship firing check (for Initial phase - ballistics only - assuming direct fire weapons all require power...
            var derelictFiring = []; //too many EW points set
            for (var shipID in myShips) {
                if (!myShips[shipID].flight) if (shipManager.power.isPowerless(myShips[shipID])) if (weaponManager.shipHasFiringOrder(myShips[shipID])) {
                    derelictFiring.push(myShips[shipID]);
                }
            }


            var errorText = '';
            if (EWIncorrect.length > 0) {
                errorText += "The following ships have too many EW points set:<br>";
                for (var shipID in EWIncorrect) {
                    //errorText += EWIncorrect[shipID].name + " (" + EWIncorrect[shipID].shipClass + ")";
                    errorText += '<span class="ship-name">' + EWIncorrect[shipID].name + ' (' + EWIncorrect[shipID].shipClass + ')</span>';
                    errorText += "<br>";
                }
                errorText += "<br>";
            }
            if (EWRestrictedIncorrect.length > 0) {
                errorText += "The following ships have too many EW points set:<br>";
                for (var shipID in EWRestrictedIncorrect) {
                    //errorText += EWRestrictedIncorrect[shipID].name + " (" + EWRestrictedIncorrect[shipID].shipClass + ")";
                    errorText += '<span class="ship-name">' + EWRestrictedIncorrect[shipID].name + ' (' + EWRestrictedIncorrect[shipID].shipClass + ')</span>';
                    errorText += "<br>";
                }
                errorText += "<br>";
            }
            if (EWLCVIncorrect.length > 0) {
                errorText += "The following LCVs have too many EW points set on non-OEW:<br>";
                for (var shipID in EWLCVIncorrect) {
                    //errorText += EWLCVIncorrect[shipID].name + " (" + EWLCVIncorrect[shipID].shipClass + ")";
                    errorText += '<span class="ship-name">' + EWLCVIncorrect[shipID].name + ' (' + EWLCVIncorrect[shipID].shipClass + ')</span>';
                    errorText += "<br>";
                }
                errorText += "<br>";
            }


            if (ammoMagazineError.length > 0) {
                errorText += "The following units are trying to launch more ordnance than available (see Ammunition Magazine):<br>";
                for (var shipID in ammoMagazineError) {
                    //errorText += ammoMagazineError[shipID].name + " (" + ammoMagazineError[shipID].shipClass + ")";
                    errorText += '<span class="ship-name">' + ammoMagazineError[shipID].name + ' (' + ammoMagazineError[shipID].shipClass + ')</span>';
                    errorText += "<br>";
                }
                errorText += "<br>";
            }

            if (derelictFiring.length > 0) {
                errorText += "The following units are derelict and should be considered shut down - cancel all firing orders:<br>";
                for (var shipID in derelictFiring) {
                    //errorText += derelictFiring[shipID].name + " (" + derelictFiring[shipID].shipClass + ")";
                    errorText += '<span class="ship-name">' + derelictFiring[shipID].name + ' (' + derelictFiring[shipID].shipClass + ')</span>';
                    errorText += "<br>";
                }
                errorText += "<br>";
            }


            if (errorText != '') {
                window.confirm.error(errorText, function () { });
                return false;
            }



            ajaxInterface.submitGamedata();

            //MOVEMENT PHASE    
        } else if (gamedata.gamephase == 2) {

            var mustPivotError = "The following ships must pivot during their movement<br>";
            var foundPShip = false; //Toggle to show error or not
            //Hyach Specialist can actually reduce Thurst below zero through toggling - DK
            var negThrustError = "The following ships have insufficient engine thrust:<br>";
            var foundTShip = false; //Toggle to show error or not

            var active = gamedata.getActiveShips();

            for (var i in active) {
                var pShip = active[i];

                if (pShip.mustPivot) {
                    if (pShip.unavailable) continue;
                    if (pShip.userid != gamedata.thisplayer) continue;
                    if (shipManager.isDestroyed(pShip)) continue;
                    var deployTurn = shipManager.getTurnDeployed(pShip);
                    if (deployTurn > gamedata.turn) continue;  //Don't bother checking for ships that haven't deployed yet.

                    var pivoted = shipManager.movement.hasPivoted(pShip)
                    if (!pivoted.left && !pivoted.right) {
                        foundPShip = true;
                        mustPivotError += '<span class="ship-name">- ' + pShip.name + '</span><br>';
                    }
                }

                var tShip = active[i];

                //Limited thrust check to Hyach Specialist now for efficiency, but we can expand it as needed - DK
                if (shipManager.hasSpecialAbility(tShip, "HyachSpecialists") && shipManager.movement.hasNegativeThrust(tShip)) {
                    foundTShip = true;
                    negThrustError += '<span class="ship-name">- ' + tShip.name + '</span><br>';
                }

            }

            if (foundPShip) {
                mustPivotError += "<br>You need to order them to pivot.";
                window.confirm.error(mustPivotError, function () { });
                return false;
            }

            if (foundTShip) {
                negThrustError += "<br>You need to lower channelled thrust before you can commit the turn.";
                window.confirm.error(negThrustError, function () { });
                return false;
            }

            ajaxInterface.submitGamedata();

            /* //Old version of mustPivot check.  Remove if no issues - DK - Dec 2025
            var pivotShips = shipManager.checkConstantPivot();        	

            if (pivotShips.length > 0) {
            	
                // Get the active ships array
                //var active = gamedata.getActiveShips();            	
                var mustPivotError = "The following ships must pivot during their movement<br>";

                // Check if any of the ship ids exist in the active array
                var foundActiveShip = false;
                for (var i in pivotShips) {
                    var ship = pivotShips[i];
                    
                    if (active.some(activeShip => activeShip.id == ship.id)) {
                        foundActiveShip = true;
                        //mustPivotError += "- " + ship.name + "<br>";
                        mustPivotError += '<span class="ship-name">- ' + ship.name + '</span><br>';
                    }
                }

                if (foundActiveShip) {
                    mustPivotError += "<br>You need to order them to pivot.";
                    window.confirm.error(mustPivotError, function () {});
                    return false;
                }
            }	        	        	
            */

            //PRE FIRING PHASE        
        } else if (gamedata.gamephase == 5) {

            //check ammo magazine, there miiight be ammo weapons in Pre-Firing?		
            //ammo usage check - AmmoMagazine equipped units
            var myShips = [];
            for (var ship in gamedata.ships) {
                if (gamedata.ships[ship].userid == gamedata.thisplayer) {
                    if (!shipManager.isDestroyed(gamedata.ships[ship]) && !gamedata.isTerrain(gamedata.ships[ship].shipSizeClass, gamedata.ships[ship].userid)) {
                        var deployTurn = shipManager.getTurnDeployed(gamedata.ships[ship]);
                        if (deployTurn <= gamedata.turn) {   //Don't bother checking for ships that haven't deployed yet. 
                            myShips.push(gamedata.ships[ship]);
                        }
                    }
                }
            }
            var ammoMagazineError = [];
            for (var shipID in myShips) { //actually this will check for fighters, too
                var currShip = myShips[shipID];
                //check for every magazine on board!
                for (var i in currShip.systems) if (currShip.systems[i].name == 'ammoMagazine') {
                    var currMagazine = currShip.systems[i];
                    var checkResult = currMagazine.doVerifyAmmoUsage(currShip);
                    if (!checkResult) ammoMagazineError.push(currShip);
                }
            }
            if (ammoMagazineError.length > 0) {
                var ammoMagError = "The following units are trying to fire more ordnance than available (see Ammunition Magazine):<br>";
                for (var shipID in ammoMagazineError) {
                    //ammoMagError += ammoMagazineError[shipID].name + " (" + ammoMagazineError[shipID].shipClass + ")";
                    ammoMagError += '<span class="ship-name">' + ammoMagazineError[shipID].name + ' (' + ammoMagazineError[shipID].shipClass + ')</span>';
                    ammoMagError += "<br>";
                }
                ammoMagError += "You need to reduce number of shots (or change mode) before you can commit the turn.";
                window.confirm.error(ammoMagError, function () { });
                return false;
            }


            ajaxInterface.submitGamedata();

            //FIRING PHASE
        } else if (gamedata.gamephase == 3) {

            //prevent Vorlons from borrowing future power for firing 
            //Capacitor-equipped ships cannot commit firing with negative power balance (they actively use power in this phase, AND they don't have any legal option of achieving negative balance by other means)
            var shipNames = shipManager.power.getCapacitorShipsNegativePower();
            if (shipNames.length > 0) {
                var negPowerError = "The following ships have insufficient power:<br>";
                for (var index in shipNames) {
                    var name = shipNames[index];
                    //negPowerError += "- " + name + "<br>";
                    negPowerError += '<span class="ship-name">- ' + name + '</span><br>';
                }
                negPowerError += "You need to reduce your firing declarations before you can commit the turn.";
                window.confirm.error(negPowerError, function () { });
                return false;
            }

            //Likewise, Plasma Battery-equipped ships cannot commit firing with negative power balance (they actively use power in this phase for Plasma Webs, AND they don't have any legal option of achieving negative balance by other means)
            var batteryShips = shipManager.power.getPlasmaBatteryShipsNegativePower();
            if (batteryShips.length > 0) {
                var negPowerError = "The following ships have insufficient plasma battery power:<br>";
                for (var index in batteryShips) {
                    var name = batteryShips[index];
                    //negPowerError += "- " + name + "<br>";
                    negPowerError += '<span class="ship-name">- ' + name + '</span><br>';
                }
                negPowerError += "You need to reduce the number of unboosted Plasma Webs firing in Offensive Mode before you can commit the turn.";
                window.confirm.error(negPowerError, function () { });
                return false;
            }


            //check ammo magazine		
            //ammo usage check - AmmoMagazine equipped units
            var myShips = [];
            for (var ship in gamedata.ships) {
                if (gamedata.ships[ship].userid == gamedata.thisplayer) {
                    if (!shipManager.isDestroyed(gamedata.ships[ship]) && !gamedata.isTerrain(gamedata.ships[ship].shipSizeClass, gamedata.ships[ship].userid)) {
                        var deployTurn = shipManager.getTurnDeployed(gamedata.ships[ship]);
                        if (deployTurn <= gamedata.turn) {   //Don't bother checking for ships that haven't deployed yet. 
                            myShips.push(gamedata.ships[ship]);
                        }
                    }
                }
            }
            var ammoMagazineError = [];
            for (var shipID in myShips) { //actually this will check for fighters, too
                var currShip = myShips[shipID];
                //check for every magazine on board!
                for (var i in currShip.systems) if (currShip.systems[i].name == 'ammoMagazine') {
                    var currMagazine = currShip.systems[i];
                    var checkResult = currMagazine.doVerifyAmmoUsage(currShip);
                    if (!checkResult) ammoMagazineError.push(currShip);
                }
            }
            if (ammoMagazineError.length > 0) {
                var ammoMagError = "The following units are trying to fire more ordnance than available (see Ammunition Magazine):<br>";
                for (var shipID in ammoMagazineError) {
                    //ammoMagError += ammoMagazineError[shipID].name + " (" + ammoMagazineError[shipID].shipClass + ")";
                    ammoMagError += '<span class="ship-name">' + ammoMagazineError[shipID].name + ' (' + ammoMagazineError[shipID].shipClass + ')</span>';
                    ammoMagError += "<br>";
                }
                ammoMagError += "You need to reduce number of shots (or change mode) before you can commit the turn.";
                window.confirm.error(ammoMagError, function () { });
                return false;
            }


            ajaxInterface.submitGamedata();
        } else if (gamedata.gamephase == 4) {
            ajaxInterface.submitGamedata();
        } //else if (gamedata.gamephase == -1) {
        //ajaxInterface.submitGamedata();
        //}
    },


    autoCommitOnMovement: function autoCommitOnMovement(ship) {
        //if (ship.base) {
        //combatLog.logMoves(ship);
        //shipManager.movement.RemoveMovementIndicators();
        //ajaxInterface.submitGamedata();
        //}
    },

    onCancelClicked: function onCancelClicked(e) {
        /* no longer valid
        if (gamedata.gamephase == 2) {
            var ship = gamedata.getActiveShip();
            shipManager.movement.deleteMove(ship);
        }
    */

        if (gamedata.gamephase == 3) {
            var ship = gamedata.getSelectedShip();
            shipManager.movement.deleteMove(ship);
        }
    },

    /*no longer valid
getActiveShipName: function getActiveShipName() {
    var ship = gamedata.getActiveShip();
    if (ship) return ship.name;
    return "";
},
*/

    getPlayerTeam: function getPlayerTeam() {
        for (var i in gamedata.slots) {
            var slot = gamedata.slots[i];
            if (slot.playerid == gamedata.thisplayer) return slot.team;
        }
    },

    getPlayerSlot: function getPlayerSlot() {
        for (var i in gamedata.slots) {
            var slot = gamedata.slots[i];
            if (slot.playerid == gamedata.thisplayer) return slot.slot;
        }
    },

    hasSlotSurrendered: function hasSlotSurrendered(slotid) {
        var slot = playerManager.getSlotById(slotid);

        if (slot.surrendered !== null) {
            if (slot.surrendered <= gamedata.turn) { //Surrendered on this turn or before.
                return true;
            }
        }

        return false;
    },

    getPlayerNameById: function getPlayerNameById(id) {
        for (var i in gamedata.slots) {
            var slot = gamedata.slots[i];
            if (slot.playerid == id) {
                return slot.playername;
            }
        }
    },

    getPhasename: function getPhasename() {
        if (gamedata.gamephase == 1) return "INITIAL ORDERS";

        if (gamedata.gamephase == 2) return "MOVEMENT ORDERS:";

        if (gamedata.gamephase == 5) return "PRE-FIRING ORDERS";

        if (gamedata.gamephase == 3) return "FIRE ORDERS";

        if (gamedata.gamephase == 4) return "FINAL ORDERS";

        if (gamedata.gamephase == -1) {
            if (shipManager.playerHasDeployedAllShips(gamedata.thisplayer)) {
                return "PRE-TURN ORDERS";
            } else {
                return "DEPLOYMENT";
            }
        }

        return "ERROR";
    },

    setPhaseClass: function setPhaseClass() {

        var b = $("body");

        b.removeClass("phase1");
        b.removeClass("phase2");
        b.removeClass("phase3");
        b.removeClass("phase4");
        b.removeClass("phase-1");

        b.addClass("phase" + gamedata.gamephase);
    },

    initPhase: function initPhase() {
        gamedata.subphase = 0;
        //shipManager.initShips();
        UI.shipMovement.hide();
        if (gamedata.gamephase == 1) {
            //To recalculate fleet list values in Info Tab without refreshing page
            fleetListManager.reset();
            fleetListManager.displayFleetLists();
        } //else {
        //To refresh whether player has committed their orders when a new phase begins.
        //fleetListManager.refreshed = false;
        //fleetListManager.displayFleetLists();
        //}

        gamedata.setPhaseClass();
        //		window.helper.doUpdateHelpContent(gamedata.gamephase,0);        

    },

    drawIniGUI: function drawIniGUI() {

        var ini_gui = document.getElementById("iniGui");
        ini_gui.innerHTML = "";

        var topicDiv = document.createElement("div");
        topicDiv.className = "topicDiv";

        var span = document.createElement("span");
        span.id = "iniTopic";
        span.innerHTML = "Order of Battle";

        topicDiv.appendChild(span);

        ini_gui.appendChild(topicDiv);

        //var allShips = gamedata.ships;
        var ships = gamedata.ships.filter(function (ship) {
            return !shipManager.isDestroyed(ship)
                && !gamedata.isTerrain(ship.shipSizeClass, ship.userid)
                && !gamedata.hasSlotSurrendered(ship.slot)
                && shipManager.getTurnDeployed(ship) <= gamedata.turn;
        });


        //ships.sort(shipManager.hasBetterInitive);
        var table = document.createElement("table");
        table.id = "iniTable";

        for (var i = 0; i < ships.length; i++) {

            var tr = document.createElement("tr");
            tr.className = "iniTr";
            tr.id = ships[i].id;

            jQuery(tr).addClass('button').on('click', function () {
                window.webglScene.customEvent('ScrollToShip', { shipId: this.id });
            })

            //var categoryIndex = window.SimultaneousMovementRule.getShipCategoryIndex(ships[i]);

            var td = document.createElement("td");
            td.position = "relative";
            td.style.width = "10%";
            td.id = "iniTd";
            td.style.textAlign = "center";
            td.style.fontSize = "18px";
            //td.innerHTML = categoryIndex !== null ? categoryIndex : i + 1;
            //Marcin Sawicki, display actual movement order instead:
            td.innerHTML = shipManager.getIniativeOrder(ships[i]);

            if (gamedata.isMyShip(ships[i])) {
                td.style.color = "green";
            } else if (gamedata.isMyorMyTeamShip(ships[i])) {
                td.style.color = "#6091d2"; // Lighter blue
            } else {
                td.style.color = "red";
            }

            tr.appendChild(td);

            var td = document.createElement("td");
            td.position = "relative";
            td.style.width = "60%";
            td.id = "iniTd";

            var span = document.createElement("span");
            span.style.textAlign = "center";
            span.style.fontSize = "12px";
            span.innerHTML += "<p style='margin-top: 6px; margin-bottom: 6px; font-size: 12px'>" + ships[i].name;
            span.innerHTML += "<p style='margin-top: 6px; margin-bottom: 6px; font-weight: bold; font-size: 11px'>" + ships[i].shipClass;

            var active = window.SimultaneousMovementRule.isActiveMovementShip(ships[i]);
            if (active !== null) {
                if (active === true && gamedata.isMyShip(ships[i]) && shipManager.movement.isMovementReady(ships[i]) && shipManager.movement.hasDeletableMovements(ships[i])) {
                    //hasDeletableMovements means player actually did _something_ with this ship! otherwise speed 0 units are immediately shown as moved and are easily skipped
                    td.className = "iniActiveMoved";
                } else if (active === true && gamedata.isMyShip(ships[i])) {
                    td.className = "iniActive";
                } else if (active === true && gamedata.isMyorMyTeamShip(ships[i])) {
                    td.className = "iniActiveAlly";
                } else if (active === true && !gamedata.isMyShip(ships[i])) {
                    td.className = "iniActiveEnemy";
                }
            } else {
                if (gamedata.getActiveShips().includes(ships[i])) {
                    td.className = gamedata.isMyShip(ships[i]) ? "iniActive" : "iniActiveEnemy";
                }
            }


            td.appendChild(span);

            tr.appendChild(td);

            var td = document.createElement("td");
            td.position = "relative";
            td.style.width = "20%";
            td.id = "iniTd";

            var img = document.createElement("img");
            img.src = ships[i].imagePath;

            if (!ships[i].flight) {
                img.style.width = "40px";
                img.style.height = "40px";
            } else {
                img.style.width = "20px";
                img.style.height = "20px";
                td.style.paddingLeft = "12px";
            }

            td.appendChild(img);
            tr.appendChild(td);

            table.appendChild(tr);
        }

        ini_gui.appendChild(table);

        var backDiv = document.getElementById("backDiv");
        backDiv.innerHTML = "";
        backDiv.style.paddingBottom = "10px";
        $(backDiv).removeData();

        var img = new Image();
        img.id = "iniSlider";
        img.src = "img/pullIn.png";
        img.style.width = "30px";
        img.style.height = "30px";
        img.style.marginLeft = "12px";

        backDiv.appendChild(img);

        backDiv.addEventListener("click", gamedata.sliderToggle);
    },

    sliderToggle: function sliderToggle() {
        var backDiv = document.getElementById("backDiv");

        if ($(backDiv).data("on") == 0) {
            $("#iniGui").show();
            $(backDiv).data("on", 1);
            backDiv.style.marginLeft = "250px";
            document.getElementById("iniSlider").src = "img/pullIn.png";
        } else {
            $("#iniGui").hide();
            $(backDiv).data("on", 0);
            backDiv.style.marginLeft = "0px";
            document.getElementById("iniSlider").src = "img/pullOut.png";
        }
    },

    showCommitButton: function showCommitButton() {
        $(".committurn").show();
    },

    hideCommitButton: function hideCommitButton() {
        $(".committurn").hide();
    },

    showSurrenderButton: function showSurrenderButton() {
        $(".surrender").on('click', gamedata.onSurrenderClicked).show();
    },

    hideSurrenderButton: function hideSurrenderButton() {
        $(".surrender").off('click', gamedata.onSurrenderClicked).hide();
    },


    checkGameStatus: function checkGameStatus() {

        //TODO: to phase strategy


    },

    goToWaiting: function goToWaiting() {
        if (gamedata.waiting == false) {
            gamedata.waiting = true;
            ajaxInterface.startPollingGamedata();
            gamedata.checkGameStatus();
            webglScene.receiveGamedata(this);
        }
    },

    parseServerData: function parseServerData(serverdata) {
        if (serverdata == null) return;

        // APCu Optimization: Always update timestamp if present
        if (serverdata.last_update) {
            gamedata.lastUpdateTimestamp = serverdata.last_update;
        }

        if (!serverdata.id) return;

        gamedata.turn = serverdata.turn;
        gamedata.gamephase = serverdata.phase;
        gamedata.activeship = serverdata.activeship;
        gamedata.gameid = serverdata.id;
        gamedata.slots = serverdata.slots;
        gamedata.rules = serverdata.rules;
        gamedata.description = serverdata.description;

        if (!gamedata.replay) {
            gamedata.thisplayer = serverdata.forPlayer;
            gamedata.waiting = serverdata.waiting;
        }
        gamedata.status = serverdata.status;
        gamedata.elintShips = Array();
        gamedata.gamespace = serverdata.gamespace;
        gamedata.blockedHexes = serverdata.blockedHexes;

        shipManager.initiated = 0;

        gamedata.setShipsFromJson(serverdata.ships);

        gamedata.initPhase();
        gamedata.drawIniGUI();
        window.webglScene.receiveGamedata(this);

        // ✅ Update Info Tab (Waiting Status) with new data
        if (window.fleetListManager) {
            fleetListManager.refreshed = false;
            fleetListManager.displayFleetLists();
        }

        gamedata.checkGameStatus();
    },

    setShipsFromJson: function setShipsFromJson(jsonShips) {
        //gamedata.ships = Array();

        for (var i in jsonShips) {
            var ship = jsonShips[i];
            gamedata.ships[i] = new Ship(ship);
        }
    },

    checkPlayerHasDeployedShips: function checkPlayerHasDeployedShips() {
        return gamedata.ships.some(ship =>
            shipManager.getTurnDeployed(ship) <= gamedata.turn && gamedata.isMyShip(ship) && !gamedata.isTerrain(ship.shipSizeClass, ship.userid)
        );
    },

};
