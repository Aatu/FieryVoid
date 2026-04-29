'use strict';

window.ShipTooltip = function () {

    var HTML = '<div class="shipNameContainer">' + '<div class="namecontainer" style="border-bottom:1px solid white;margin-bottom:3px;"></div>' +
        '<div class="fire" style=";margin:3px 0px 3px 0px; padding:2px 0px 0px 0px;border-top:1px solid white;color:red; text-decoration: bold;"><span>TARGETING</span></div>' +
        '<div class="fire targeting"></div>' + '<div class="ballistics" style=";margin:3px 0px 3px 0px; padding:2px 0px 0px 0px;border-top:1px solid white;color:red;"><span>INCOMING:</span></div>' +
        '<div class="ballistics incoming"></div>' + '<div class="buttons"></div>' + '</div>';

    function ShipTooltip(selectedShip, ships, position, showTargeting, menu, hexagon, ballisticsMenu) {
        this.element = jQuery(HTML);
        this.ships = [].concat(ships);
        this.ships.sort(shipManager.hasBetterInitive); //so they're displayed in Ini order
        this.position = position;
        //TODO: selected ship might be destroyed
        this.selectedShip = selectedShip;
        this.showTargeting = showTargeting;
        this.hexagon = hexagon;
        this.menu = menu;
        this.ballisticsMenu = ballisticsMenu;

        this.element.on('mousedown mouseup mouseover mousemove mouseout', function (e) {
            e.preventDefault(); e.stopPropagation();
        });

        if (!menu) {
            this.element.on('mousedown mouseup mouseover mousemove mouseout', function (e) {
                this.destroy();
            }.bind(this));
        }

        if (ships.length > 1) {
            createForMultipleShips.call(this, this.ships);
        } else {
            createForSingleShip.call(this, this.ships[0]);
        }

        this.show();
    }

    ShipTooltip.prototype.show = function () {
        this.element.appendTo('body');
        this.element.show();
        positionElement(this.element, this.position);
    };

    ShipTooltip.prototype.reposition = function (position) {
        if (position) {
            this.position = position;
        }

        positionElement(this.element, this.position);

        return true;
    };

    ShipTooltip.prototype.destroy = function () {
        this.element.remove();
    };

    ShipTooltip.prototype.addEntryElement = function (value, condition) {
        if (condition === false || condition === 0 || condition === null) return;

        jQuery('<div class="entry"><span>' + value + '</span></div>').insertAfter(this.element.find('.namecontainer'));
    };

    ShipTooltip.prototype.update = function (ship, selectedShip) {
        if (selectedShip) {
            this.selectedShip = selectedShip;
            //this.showTargeting = shipManager.systems.selectedShipHasSelectedWeapons(this.selectedShip);
        }

        if (selectedShip && this.menu) {
            this.menu.selectedShip = selectedShip;
            this.menu.currentInfo = "";
        }

        jQuery(".buttons", this.element).html("");
        jQuery(".namecontainer", this.element).html("");
        jQuery(".fire", this.element).html("");
        jQuery(".entry", this.element).remove();
        jQuery(".incoming", this.element).html("");

        if (this.ships.length > 1) {
            createForMultipleShips.call(this, this.ships);
        } else {
            createForSingleShip.call(this, this.ships[0]);
        }
    };

    ShipTooltip.prototype.isForAnyOf = function (ships) {
        ships = [].concat(ships)

        if (this.ships.length > 1) {
            return false;
        }

        return this.ships.some(function (ship) {
            return ships.includes(ship)
        })

    }


    function createForSingleShip(ship) {
        var shipNameDisplay = ship.name;
        if (ship.mine) {
            var stealthSystem = shipManager.systems.getSystemByName(ship, "mineStealth");
            if (stealthSystem && !stealthSystem.isMineRevealed(ship)) {
                shipNameDisplay = "Mine";
            }
        }
        jQuery('<span class="name value ' + getAllyClass(ship) + '">' + shipNameDisplay + '</span>').appendTo(this.element.find('.namecontainer'));

        var jinking = shipManager.movement.getJinking(ship) * 5;
        var flightArmour = shipManager.systems.getFlightArmour(ship);

        //add info of flight-wide criticals!
        if (ship.flight === true) {
            //get first fighter in flight
            var firstFighter = shipManager.systems.getSystem(ship, 1);
            var sensorDown = shipManager.criticals.hasCritical(firstFighter, "tmpsensordown");
            if (sensorDown > 0) {
                sensorDown = sensorDown * 5;
                this.addEntryElement("<i>OB temporarily lowered by <b>" + sensorDown + "</b></i>", true);
            }
            var iniDown = shipManager.criticals.hasCritical(firstFighter, "tmpinidown");
            if (iniDown > 0) {
                iniDown = iniDown * 5;
                this.addEntryElement("<i>Initiative temporarily lowered by <b>" + iniDown + "</b></i>", true);
            }
        }

        if (ship.base && ship.movement[1]) {
            var direction;

            if (ship.movement[1].value === -1) {
                direction = "port";
            } else if (ship.movement[1].value === 1) {
                direction = "starboard";
            }

            if (direction) {
                this.addEntryElement("Rotation towards " + direction);
            }
        }


        /*condensed to one line
            this.addEntryElement('Evasion: -' + jinking + ' to hit', ship.flight === true && jinking > 0);	
            this.addEntryElement('Pivoting ' + shipManager.movement.isPivoting(ship), shipManager.movement.isPivoting(ship) !== 'no');
            this.addEntryElement('Rolling', shipManager.movement.isRolling(ship));
            this.addEntryElement('Rolled', shipManager.movement.isRolled(ship));
        */
        var toDisplay = '';
        var rollPivotModifier = 0;
        //if (ship.flight === true && jinking > 0) toDisplay += 'Evasion: -' + jinking + ' to hit; ';
        if (jinking > 0) toDisplay += 'Evasion: -' + jinking + ' to hit; ';    //Ships can jink too now - DK Oct 2025
        if (shipManager.movement.isPivoting(ship) !== 'no') toDisplay += 'Pivoting; ';
        if (ship.agile && (!ship.flight)) {
            if (shipManager.movement.hasRolled(ship)) {
                toDisplay += 'Has rolled; ';
                if (!ship.ignoreManoeuvreMods) rollPivotModifier -= 15;
            }
        } else if ((!ship.flight)) {
            if (shipManager.movement.isRolling(ship)) {
                toDisplay += 'Rolling; ';
                if (!ship.ignoreManoeuvreMods) rollPivotModifier -= 15;
            }
        }
        if ((!ship.flight) && shipManager.movement.isRolled(ship)) toDisplay += 'Rolled; '; //fighters don't roll, no point looking for it
        if ((!ship.flight) && shipManager.movement.isHalfPhased(ship)) { //fighters don't half phase, no point looking for it
            toDisplay += 'Half-Phased; ';
            rollPivotModifier -= 50;
        }
        if (ship.trueStealth) {
            if (gamedata.gamephase == -1 && shipManager.getTurnDeployed(ship) == gamedata.turn) {
                toDisplay += '<span style="color:limegreen;">Deploying</span>; '; //Always say undetected on Deployment phase.  
            } else {
                var isShipDetected = shipManager.isDetected(ship);
                var stealthSys = null;

                if (ship.mine) {
                    stealthSys = shipManager.systems.getSystemByName(ship, "mineStealth");
                } else if (ship.faction == "Torvalus Speculators") {
                    stealthSys = shipManager.systems.getSystemByName(ship, "ShadingField");
                } else if (shipManager.getSpecialAbilityStealth(ship, "Cloaking")) {
                    stealthSys = shipManager.systems.getSystemByName(ship, "CloakingDevice");
                } else if (shipManager.getSpecialAbilityStealth(ship, "Stealth")) {
                    stealthSys = shipManager.systems.getSystemByName(ship, "stealth");
                }

                if (!isShipDetected && ship.team == gamedata.getPlayerTeam()) {
                    if (stealthSys) {
                        if (Array.isArray(stealthSys.detected) && stealthSys.detected.length > 0) {
                            isShipDetected = true;
                        } else if (stealthSys.detected === true) {
                            isShipDetected = true;
                        } else if (Array.isArray(stealthSys.detectedNew) && stealthSys.detectedNew.length > 0) {
                            isShipDetected = true;
                        } else if (stealthSys.detectedNew === true) {
                            isShipDetected = true;
                        }
                    }
                }

                if (isShipDetected) {
                    var detectedTeamsStr = "";
                    if (ship.team == gamedata.getPlayerTeam()) { //Only own player needs to see full team list that's detected their ship.
                        // Check if we have more than 2 teams in the game
                        var uniqueTeams = [];
                        for (var i in gamedata.slots) {
                            var team = parseInt(gamedata.slots[i].team, 10);
                            if (team > 0 && !uniqueTeams.includes(team)) {
                                uniqueTeams.push(team);
                            }
                        }

                        if (uniqueTeams.length > 2) {
                            var detectedArray = [];
                            if (stealthSys && Array.isArray(stealthSys.detectedNew)) {
                                detectedArray = stealthSys.detectedNew;
                            } else if (stealthSys && Array.isArray(stealthSys.detected)) {
                                detectedArray = stealthSys.detected;
                            }

                            if (detectedArray.length > 0) {
                                // Ensure unique team numbers
                                var uniqueDetectedTeams = [];
                                for (var i = 0; i < detectedArray.length; i++) {
                                    var detectedTeam = parseInt(detectedArray[i], 10);
                                    if (detectedTeam > 0 && !uniqueDetectedTeams.includes(detectedTeam)) {
                                        uniqueDetectedTeams.push(detectedTeam);
                                    }
                                }

                                // Sort team numbers for readability
                                uniqueDetectedTeams.sort(function (a, b) { return a - b; });

                                if (uniqueDetectedTeams.length > 0) {
                                    detectedTeamsStr = " (Teams: " + uniqueDetectedTeams.join(", ") + ")";
                                }
                            }
                        }
                    }

                    toDisplay += '<span style="color:red;">Detected' + detectedTeamsStr + '</span>; '; //Notify player that their Stealth ship is detected.
                } else {
                    toDisplay += '<span style="color:limegreen;">Undetected</span>; '; //Notify player that their Stealth ship is detected.            
                }
            }
        }

        if (gamedata.gamephase == 3) {
            if (Object.values(ship.skinDancing).includes(true)) {
                toDisplay += '<span style="color:limegreen;">Skin Dancing</span>; '; //Notify player that unit is skin dancing this turn.                  
            } else if (Object.values(ship.skinDancing).includes("Aborted")) {
                toDisplay += '<span style="color:orange;">Skin Dance Aborted</span>; '; //Notify player that unit is skin dancing this turn.  
            } else if (Object.values(ship.skinDancing).includes("Failed")) {
                toDisplay += '<span style="color:red;">Failed Skin Dancing</span>; '; //Notify player that unit is skin dancing this turn.  
            }
        }

        if (ship.attached && Object.keys(ship.attached).length > 0 && !ship.detached) {
            var targetId = Object.keys(ship.attached)[0];
            var location = Object.values(ship.attached)[0];
            var locationTip = '';
            
            if (location == 1){
                locationTip = 'Front';
            }else if (location == 2){
                locationTip = 'Aft';                
            }else if (location == 3 || location == 31 || location == 32){
                locationTip = 'Port';             
            }else if (location == 4 || location == 41 || location == 42){
                locationTip = 'Starboard';   
            }    

            var targetShip = gamedata.getShip(targetId);
            if (targetShip) {
                toDisplay += '<span style="color:limegreen;">Attached to ' + targetShip.name + ' [' + locationTip + ']</span>; ';
            }
        }
        
        if (ship.hasAttached && Object.keys(ship.hasAttached).length > 0) {
            var keys = Object.keys(ship.hasAttached);
            if (keys.length > 0) {
                toDisplay += '<span style="color:orange;">Ship is being Boarded!</span>; ';
            }
        }
        if (ship.flight === true) {
            if (shipManager.movement.hasCombatPivoted(ship) && (!ship.ignoreManoeuvreMods)) rollPivotModifier -= 5;
        } else if (ship.osat) {
            if (shipManager.movement.hasTurned(ship)) rollPivotModifier -= 5;
        } else {
            if (shipManager.movement.hasPivotedForShooting(ship) && (!ship.ignoreManoeuvreMods)) rollPivotModifier -= 15;
        }
        if (rollPivotModifier != 0) toDisplay += 'Firing modifier: ' + rollPivotModifier; //display firing modifier from roll/pivot/combat pivot


        if (toDisplay != '') toDisplay = '<b><i>' + toDisplay + '</i></b>';
        this.addEntryElement(toDisplay, toDisplay != '');

        /*condensed to one line
        this.addEntryElement("Ballistic navigator aboard", ship.hasNavigator === true);
        this.addEntryElement("Escorting ships in same hex", shipManager.isEscorting(ship));
        */
        toDisplay = '';
        if (ship.hasNavigator === true) toDisplay += 'Navigator; ';
        var listEscorting = shipManager.listEscorting(ship);
        if (listEscorting != '') {
            toDisplay += '<span class="escorting">Escorting: </span>';
            //list of unit names
            toDisplay += listEscorting;
        }
        this.addEntryElement(toDisplay, toDisplay != '');

        if (ship.mine) {
            if (gamedata.isMyorMyTeamShip(ship)) toDisplay = 'Signature: ' + ship.signature;
            this.addEntryElement(toDisplay);

            if (this.selectedShip) {
                if (!gamedata.isMyShip(ship)) {
                    this.addEntryElement('OEW: ' + ew.getOffensiveEW(this.selectedShip, ship), this.selectedShip !== ship && ship.flight !== true && this.selectedShip.flight !== true);
                }
            }

        } else {
            //this.addEntryElement("Iniative Order: " + shipManager.getIniativeOrder(ship) + "    (D100 + " + ship.iniativebonus + ")");
            this.addEntryElement("Ini Order: " + shipManager.getIniativeOrder(ship) + " (total " + ship.iniative + "): base " + ship.iniativebonus + "; mod " + ship.iniativeadded);

            /*miscellanous info - once inserted, now disappeared; if it's needed, look for source code in Abbai branch!
            toDisplay = shipManager.systems.getMisc(ship);
            this.addEntryElement(toDisplay, toDisplay!=''); //miscellanous info from systems - special information o be shown here
            */

            //this.addEntryElement('Current turn delay: ' + shipManager.movement.calculateCurrentTurndelay(ship));
            var currDelay = shipManager.movement.calculateCurrentTurndelay(ship)
            var speed = shipManager.movement.getSpeed(ship);
            var baseTurnCost = shipManager.movement.getTurnCost(ship);
            if (ship.submarine && shipManager.movement.isGoingBackwards(ship)) baseTurnCost = baseTurnCost * 1.33;
            var turncost = Math.ceil(speed * baseTurnCost);
            var turnDelayCost = Math.ceil(speed * shipManager.movement.getTurnDelayCost(ship));

            this.addEntryElement('Pivot cost: ' + ship.pivotcost + ' Roll cost: ' + ship.rollcost, ship.flight !== true);
            this.addEntryElement('Pivot cost: ' + ship.pivotcost + ' Combat pivot cost: ' + Math.ceil(ship.pivotcost * 1.5), ship.flight === true);
            toDisplay = ''; //display Agile status
            if (ship.agile) toDisplay = ', Agile';
            this.addEntryElement('Turn Cost: ' + turncost + ' (' + shipManager.movement.getTurnCost(ship) + '); Turn Delay: ' + turnDelayCost + ' (' + shipManager.movement.getTurnDelayCost(ship) + ')' + toDisplay);

            var thrustRemaining = Math.max(shipManager.movement.getRemainingEngineThrust(ship), 0);//EngineShorted can make this go negative.

            toDisplay = 'Thrust: ' + thrustRemaining + '/' + shipManager.movement.getFullEngineThrust(ship);//thrust: remaining/full
            this.addEntryElement(toDisplay, toDisplay != '');
            //this.addEntryElement('Unused thrust: ' + shipManager.movement.getRemainingEngineThrust(ship), ship.flight || gamedata.gamephase === 2);

            toDisplay = 'Speed: ' + shipManager.movement.getSpeed(ship);
            if (currDelay > 0) toDisplay += ' (delay ' + currDelay + ')';
            toDisplay += ' (acc cost: ' + ship.accelcost + ')';
            this.addEntryElement(toDisplay);
            this.addEntryElement('Armor (F/S/A): ' + flightArmour, ship.flight === true);

            if (this.selectedShip) {
                if (!gamedata.isMyShip(ship)) {
                    this.addEntryElement('OEW: ' + ew.getOffensiveEW(this.selectedShip, ship), this.selectedShip !== ship && ship.flight !== true && this.selectedShip.flight !== true);
                }

                if (shipManager.isElint(this.selectedShip)) {
                    if (shipManager.hasSpecialAbility(this.selectedShip, "ConstrainedEW")) {//Mindrider ships have less efficient ELINT abilities - DK 19.07.24.            	
                        this.addEntryElement('DIST: ' + ew.getOffensiveEW(this.selectedShip, ship, "DIST") / 4, this.selectedShip !== ship && ship.flight !== true);
                    } else {
                        this.addEntryElement('DIST: ' + ew.getOffensiveEW(this.selectedShip, ship, "DIST") / 3, this.selectedShip !== ship && ship.flight !== true);
                    }
                }
            }

            var dewValue = ew.getSupportedDEW(ship).toFixed(2);
            //if (ew.getSupportedDEW(ship)) {//Amended because Mindrider Constrained EW can create over 2 decimal places in Ship Tooltip! DK - 20.7.24	
            if (dewValue > 0) {//Amended because Mindrider Constrained EW can create over 2 decimal places in Ship Tooltip! DK - 20.7.24 
                this.addEntryElement('Support DEW: ' + dewValue, ship.flight !== true);
            }

            var MDEW = ew.getDetectMEW(ship);           
            if (MDEW > 0) {//Amended because Mindrider Constrained EW can create over 2 decimal places in Ship Tooltip! DK - 20.7.24	
                this.addEntryElement('Detect Mines: ' + MDEW);
            }

            var BDEW = ew.getEWByType('BDEW', ship) * 0.25;
            BDEW = parseFloat(BDEW.toFixed(2));
            if (shipManager.isElint(ship)) {
                if (gamedata.isStealthPresent) this.addEntryElement('Detect Stealth: ' + ew.getEWByType('Detect Stealth', ship), ship.flight !== true);
                this.addEntryElement('Blanket DEW: ' + BDEW, ship.flight !== true);
            }

            this.addEntryElement('DEW: ' + ew.getDefensiveEW(ship) + ' CCEW: ' + ew.getCCEW(ship), ship.flight !== true);
        }
        //Amended because Mindrider Constrained EW can create over 2 decimal places in Ship Tooltip! DK - 20.7.24
        var fDef = weaponManager.calculateBaseHitChange(ship, ship.forwardDefense) * 5;
        fDef = parseFloat(fDef.toFixed(2));
        var sDef = weaponManager.calculateBaseHitChange(ship, ship.sideDefense) * 5;
        sDef = parseFloat(sDef.toFixed(2));

        this.addEntryElement("Defence (F/S): " + fDef + "(" + ship.forwardDefense * 5 + ") / " + sDef + "(" + ship.sideDefense * 5 + ")%");

        if (this.selectedShip && this.selectedShip !== ship) {
            var dis = mathlib.getDistanceBetweenShipsInHex(this.selectedShip, ship);
            this.addEntryElement('DISTANCE: ' + dis + ' hexes');
        }

        if (gamedata.rules && gamedata.rules.friendlyFire === 1) {
            if (this.selectedShip && this.showTargeting && this.selectedShip.id != ship.id) {
                weaponManager.targetingShipTooltip(this.selectedShip, ship, this.element, null);
                this.element.find(".fire").css({ "display": "block", "visibility": "visible" });
            } else {
                this.element.find(".fire").css("display", "none");
            }
        } else {
            if (this.selectedShip && gamedata.isEnemy(ship, this.selectedShip) && this.showTargeting) { //Old version before allied targeting
                weaponManager.targetingShipTooltip(this.selectedShip, ship, this.element, null);
                this.element.find(".fire").css({ "display": "block", "visibility": "visible" });
            } else if (this.selectedShip && gamedata.canTargetAlly(ship) && this.showTargeting) {//30 June 2024 - DK - Added for Ally targeting.
                weaponManager.targetingShipTooltip(this.selectedShip, ship, this.element, null);
                this.element.find(".fire").css({ "display": "block", "visibility": "visible" });
            } else {
                this.element.find(".fire").css("display", "none");
            }
        }

        this.ballisticsMenu.renderTo(ship, this.element);

        if (this.menu) {
            this.menu.renderTo(jQuery(".buttons", this.element), this);
        }
    }

    function createForMultipleShips(ships) {
        ships.forEach(function (ship, i) {
            var comma = i < ships.length - 1 ? ',' : '';
            var shipNameDisplay = ship.name;
            if (ship.mine) {
                var stealthSystem = shipManager.systems.getSystemByName(ship, "mineStealth");
                if (stealthSystem && !stealthSystem.isMineRevealed(ship)) {
                    shipNameDisplay = "Mine";
                }
            }
            jQuery('<span class="name value ' + getAllyClass(ship) + '">' + shipNameDisplay + comma + ' </span>').appendTo(this.element.find('.namecontainer'));

            $(".ballistics", this.element).hide();
        }, this);
        this.addEntryElement("Zoom closer, or click to interact");
    }

    function showBallisticsTooltip(ballistics) { }

    function positionElement(element, position) {
        if (position instanceof hexagon.Offset) {
            position = window.coordinateConverter.fromHexToViewport(position);
        } else {
            position = window.coordinateConverter.fromGameToViewPort(position);
        }

        var yOffset = window.coordinateConverter.getHexHeightViewport() / 2;

        if (yOffset > 100) {
            yOffset = 100;
        }

        if (yOffset < 20) {
            yOffset = 20;
        }

        element.css("left", position.x - (element.width() + 30) / 2 + "px").css("top", position.y + yOffset + "px");
    }

    function getAllyClass(ship) {
        /* if(ship.shipSizeClass == 5){
             return 'terrain'; //Return a neutral white colour for Terrain.
         }else{
             return gamedata.isMyOrTeamOneShip(ship) ?  'ally' : 'enemy';
         }*/
        //Let's make allied team ships blue text, and terrain white - DK May 2025
        return gamedata.isTerrain(ship.shipSizeClass, ship.userid) ? 'terrain' : (gamedata.isMyShip(ship) ? 'mine' : (gamedata.isMyorMyTeamShip(ship) ? 'ally' : 'enemy'));
    }

    return ShipTooltip;
}();
