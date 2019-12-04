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
    getActiveShip: function getActiveShip() {
        return null;
    },
    
    getActiveShips: function getActiveShips() {

        if (Array.isArray(gamedata.activeship)){
            return gamedata.activeship.map(function (id) {
                return gamedata.getShip(id);
            }).filter(function(ship) { 
                return Boolean(ship);
            });
        } else {
            return [gamedata.getShip(gamedata.activeship)].filter(function(ship) { 
                return Boolean(ship);
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
        return ship.userid == gamedata.thisplayer;
    },

    isEnemy: function isEnemy(target, shooter) {
        if (!shooter) {
            throw new Error("You need to give shooter for this one");
        }

        return target.team !== shooter.team;
    },

    isMyOrTeamOneShip: function isMyOrTeamOneShip(ship) {
        if (gamedata.isPlayerInGame()) {
            return gamedata.isMyShip(ship);
        } else {
            return ship.team === 1;
        }
    },

    isPlayerInGame: function isPlayerInGame() {
        if (gamedata.thisplayer === null || gamedata.thisplayer === -1) {
            return false;
        }

        var slot = Object.keys(gamedata.slots).find(function(key){
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
                    if ((bases[i].movement[1].value == 0) && (!bases[i].nonRotating)){
                        confirm.error("Please setup the rotation of your starbase.", function () {});
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
                    if (!shipManager.isDestroyed(gamedata.ships[ship])) {
                        myShips.push(gamedata.ships[ship]);
                    }
                }
            }

            var hasNoEW = [];
            var selfDestructing = [];
	    var EWIncorrect = []; //too many EW points set
	    var EWRestrictedIncorrect = [];//RestrictedEW critical circumvented
	    var EWLCVIncorrect = [];//LCV set too many EW to tasks other than OEW

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
                        }
                    }

                    if (shipManager.isDisabled(myShips[ship])) {
                        continue;
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
			
			if (ew.convertUnusedToDEW(ship) != true){
				EWIncorrect.push(myShips[ship]);
			}
			if (ew.checkRestrictedEW(ship) != true){
				EWRestrictedIncorrect.push(myShips[ship]);
			}
			if (ew.checkLCVSensors(ship) != true){
				EWLCVIncorrect.push(myShips[ship]);
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
                    html += selfDestructing[ship].name + " (" + selfDestructing[ship].shipClass + ")";
                    html += "<br>";
                }
                html += "<br>";
            }
            if (hasNoEW.length > 0) {
                html += "You have not assigned any EW for the following ships: ";
                html += "<br>";
                for (var ship in hasNoEW) {
                    html += hasNoEW[ship].name + " (" + hasNoEW[ship].shipClass + ")";
                    html += "<br>";
                }
            }
            confirm.confirm(html + "<br>Are you sure you wish to COMMIT YOUR INITIAL ORDERS?", gamedata.doCommit);
        }

        // CHECK for NO FIRE
        else if (gamedata.gamephase == 3) {
                var myShips = [];

                for (var ship in gamedata.ships) {
                    if (gamedata.ships[ship].userid == gamedata.thisplayer) {
                        if (!shipManager.isDestroyed(gamedata.ships[ship])) {
                            myShips.push(gamedata.ships[ship]);
                        }
                    }
                }

                var hasNoFO = [];

                for (var ship in myShips) {
                    var fired = 0;

                    if (!myShips[ship].flight) {
                        for (var i = 0; i < myShips[ship].systems.length; i++) {
                            if (myShips[ship].systems[i].fireOrders.length > 0) {
                                fired = 1;
                                break;
                            }
                        }
                        if (fired == 0) {
                            hasNoFO.push(myShips[ship]);
                        }
                    } else if (myShips[ship].flight) {
                        for (var i = 0; i < myShips[ship].systems.length; i++) {
                            if (typeof myShips[ship].systems[i] != "undefined") {
                                for (var j = 0; j < myShips[ship].systems[i].systems.length; j++) {
                                    if (typeof myShips[ship].systems[i].systems[j] != "undefined") {
                                        if (myShips[ship].systems[i].systems[j].fireOrders.length > 0) {
                                            fired = 1;
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                        if (fired == 0) {
                            hasNoFO.push(myShips[ship]);
                        }
                    }
                }

                if (hasNoFO.length == 0) {
                    confirm.confirm("Are you sure you wish to COMMIT YOUR FIRE ORDERS?", gamedata.doCommit);
                } else {
                    var html = "You have not assigned any fire orders for the following ships: ";
                    html += "<br>";
                    for (var ship in hasNoFO) {
                        html += hasNoFO[ship].name + " (" + hasNoFO[ship].shipClass + ")";
                        html += "<br>";
                    }
                    confirm.confirm(html + "<br>Are you sure you wish to COMMIT YOUR FIRE ORDERS?", gamedata.doCommit);
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

        if (gamedata.gamephase == 1) {
            //        	ajaxInterface.fastpolling=true;
            var shipNames = shipManager.power.getShipsNegativePower();

            if (shipNames.length > 0) {
                var negPowerError = "The following ships have insufficient power:<br>";

                for (var index in shipNames) {
                    var name = shipNames[index];
                    negPowerError += "- " + name + "<br>";
                }
                negPowerError += "You need to turn off systems before you can commit the turn.";
                window.confirm.error(negPowerError, function () {});
                return false;
            }

            shipNames = shipManager.power.getShipsGraviticShield();

            if (shipNames.length > 0) {
                var tooManyShieldsError = "The following ships have too many active shields:<br>";

                for (var i in shipNames) {
                    var shipName = shipNames[i];
                    tooManyShieldsError += "- " + shipName + "<br>";
                }
                tooManyShieldsError += "You need to turn off shields or boost your shield generator before you can commit the turn.";
                window.confirm.error(tooManyShieldsError, function () {});
                return false;
            }


            ajaxInterface.submitGamedata();
        } else if (gamedata.gamephase == 2) {
            ajaxInterface.submitGamedata();
        } else if (gamedata.gamephase == 3) {
            ajaxInterface.submitGamedata();
        } else if (gamedata.gamephase == 4) {
            ajaxInterface.submitGamedata();
        } else if (gamedata.gamephase == -1) {
            ajaxInterface.submitGamedata();
        }
    },

    autoCommitOnMovement: function autoCommitOnMovement(ship) {
        //if (ship.base) {
            //combatLog.logMoves(ship);
            //shipManager.movement.RemoveMovementIndicators();
            //ajaxInterface.submitGamedata();
        //}
    },

    onCancelClicked: function onCancelClicked(e) {
        if (gamedata.gamephase == 2) {
            var ship = gamedata.getActiveShip();
            shipManager.movement.deleteMove(ship);
        }

        if (gamedata.gamephase == 3) {
            var ship = gamedata.getSelectedShip();
            shipManager.movement.deleteMove(ship);
        }
    },

    getActiveShipName: function getActiveShipName() {
        var ship = gamedata.getActiveShip();
        if (ship) return ship.name;

        return "";
    },

    getPlayerTeam: function getPlayerTeam() {
        for (var i in gamedata.slots) {
            var slot = gamedata.slots[i];
            if (slot.playerid == gamedata.thisplayer) return slot.team;
        }
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

        if (gamedata.gamephase == 3) return "FIRE ORDERS";

        if (gamedata.gamephase == 4) return "FINAL ORDERS";

        if (gamedata.gamephase == -1) return "DEPLOYMENT";

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
        fleetListManager.displayFleetLists();

        gamedata.setPhaseClass();
        //		window.helper.doUpdateHelpContent(gamedata.gamephase,0);        

        fleetListManager.updateFleetList();
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

        var allShips = gamedata.ships;
        var ships = gamedata.ships.filter(function (ship){
            return !shipManager.isDestroyed(ship);
        })

       

        //ships.sort(shipManager.hasBetterInitive);
        var table = document.createElement("table");
        table.id = "iniTable";

        for (var i = 0; i < ships.length; i++) {

            var tr = document.createElement("tr");
            tr.className = "iniTr";
            tr.id = ships[i].id;

            jQuery(tr).addClass('button').on('click', function() {
                window.webglScene.customEvent('ScrollToShip', {shipId: this.id});
            })

            var categoryIndex = window.SimultaneousMovementRule.getShipCategoryIndex(ships[i]);

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
            } else td.style.color = "red";

            tr.appendChild(td);

            var td = document.createElement("td");
            td.position = "relative";
            td.style.width = "60%";
            td.id = "iniTd";

            var span = document.createElement("span");
            span.style.textAlign = "center";
            span.style.fontSize = "12px";
            span.innerHTML += "<p style='margin-top: 6px; margin-bottom: 6px; font-size: 12px'>" + ships[i].name;
            span.innerHTML += "<p style='margin-top: 6px; margin-bottom: 6px; font-style: italic; font-weight: bold'>" + ships[i].shipClass;

            var active = window.SimultaneousMovementRule.isActiveMovementShip(ships[i]);
            if (active !== null) {
                if (active === true && gamedata.isMyShip(ships[i]) && shipManager.movement.isMovementReady(ships[i])  && shipManager.movement.hasDeletableMovements(ships[i]) ) {
                    //hasDeletableMovements means player actually did _something_ with this ship! otherwise speed 0 units are immediately shown as moved and are easily skipped
                    td.className = "iniActiveMoved";
                }else if (active === true && gamedata.isMyShip(ships[i])) {
                    td.className = "iniActive";
                } else if (active === true  && !gamedata.isMyShip(ships[i])){
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

        if (!serverdata.id) return;

        gamedata.turn = serverdata.turn;
        gamedata.gamephase = serverdata.phase;
        gamedata.activeship = serverdata.activeship;
        gamedata.gameid = serverdata.id;
        gamedata.slots = serverdata.slots;
        gamedata.rules = serverdata.rules;

        if (!gamedata.replay) {
            gamedata.thisplayer = serverdata.forPlayer;
            gamedata.waiting = serverdata.waiting;
        }
        gamedata.status = serverdata.status;
        gamedata.elintShips = Array();
        gamedata.gamespace = serverdata.gamespace;
        shipManager.initiated = 0;

        gamedata.setShipsFromJson(serverdata.ships);

        gamedata.initPhase();
        gamedata.drawIniGUI();
        window.webglScene.receiveGamedata(this);

        gamedata.checkGameStatus();
    },

    setShipsFromJson: function setShipsFromJson(jsonShips) {
        //gamedata.ships = Array();

        for (var i in jsonShips) {
            var ship = jsonShips[i];
            gamedata.ships[i] = new Ship(ship);
        }
    }

};
