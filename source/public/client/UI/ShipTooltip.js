'use strict';

window.ShipTooltip = function () {

    // Every rule this markup needs now lives in styles/shipTooltip.css. The `tt-head`
    // class is what the two section headings (TARGETING / INCOMING) are styled by: it
    // exists because `.fire` and `.ballistics` are each carried by TWO divs — the heading
    // and the content beneath it — and only the heading wants the rule above it. The
    // existing `.fire` / `.ballistics` / `.targeting` / `.incoming` classes are untouched;
    // they are the selectors the show/hide and fill code below (and weaponManager) use.
    var HTML = '<div class="shipNameContainer">'
        + '<button type="button" class="tt-close" aria-label="Close" title="Close">\u2715</button>'
        + '<div class="namecontainer"></div>'
        + '<div class="fire tt-head"><span>TARGETING</span></div>'
        + '<div class="fire targeting"></div>'
        + '<div class="ballistics tt-head"><span>INCOMING:</span></div>'
        + '<div class="ballistics incoming"></div>'
        + '<div class="buttons"></div>'
        + '</div>';

    /* The Walkers' Energy Draining Field. The one purple already in the game's UI is
       EWIconContainer's COLOR_JAM (210, 80, 255), used for the other "your unit is suppressed
       this turn" state, so the EDF's messages borrow it rather than inventing a second purple.
       Kept in step with the shipWindow status banner in ShipWindow.js, which uses the same value. */
    var EDF_PURPLE = '#d250ff';

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

        /* The close button is for the PERSISTENT tooltip only - the one a click on a unit
           leaves standing, which is the one that sits over the hex you are trying to target or
           over the movement UI (user report 2026-08-30). A hover tooltip (menu === null) already
           tears itself down on the handler directly above: the pointer cannot reach an X inside
           it without destroying it first, so drawing one there would be a button that can never
           be pressed. */
        if (menu) {
            //The modifier reserves room at both ends of the name row so a long ship name cannot
            //run under the X - symmetric, because the row is centre-aligned and padding one side
            //only would shove every name off-centre. See shipTooltip.css.
            this.element.addClass('shipNameContainer--closable');
            this.element.find('.tt-close').on('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                this.close();
            }.bind(this));
        } else {
            this.element.find('.tt-close').remove();
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

    /* Dismiss THE WAY THE OWNER WOULD. destroy() only takes the element out of the DOM -
       PhaseStrategy.shipTooltip would go on pointing at the corpse, and onMouseOverShip's
       `if (!this.shipTooltip || !this.shipTooltip.menu)` guard would then refuse to build any
       further tooltip for the rest of the phase. onClose is set by PhaseStrategy.showShipTooltip
       to its own hideShipTooltip, which clears that handle (and restores the coarse-pointer
       highlights). The bare destroy() is the fallback for any other owner. */
    ShipTooltip.prototype.close = function () {
        if (typeof this.onClose === 'function') {
            this.onClose();
        } else {
            this.destroy();
        }
    };

    ShipTooltip.prototype.addEntryElement = function (value, condition) {
        if (condition === false || condition === 0 || condition === null) return;

        jQuery('<div class="entry"><span>' + value + '</span></div>').insertAfter(this.element.find('.namecontainer'));
    };

    ShipTooltip.prototype.update = function (ship, selectedShip) {
        if (selectedShip) {
            this.selectedShip = selectedShip;
        }

        if (selectedShip && this.menu) {
            this.menu.selectedShip = selectedShip;
            this.menu.currentInfo = "";
        }

        jQuery(".buttons", this.element).html("");
        jQuery(".namecontainer", this.element).html("");
        //⚠️ `.fire.targeting`, NOT `.fire`. The TARGETING heading and the rows beneath it are two
        //sibling divs that SHARE the `fire` class (see HTML above), so a bare `.fire` wipe deletes
        //the heading's <span> as well - and nothing ever puts it back, because
        //weaponManager.targetingShipTooltip fills only `.targeting`. The INCOMING pair below was
        //always cleared by its content class alone; this is the same rule applied to its twin.
        jQuery(".fire.targeting", this.element).html("");
        jQuery(".entry", this.element).remove();
        jQuery(".incoming", this.element).html("");

        if (this.ships.length > 1) {
            createForMultipleShips.call(this, this.ships);
        } else {
            createForSingleShip.call(this, this.ships[0]);
        }
    };

    /* Re-render ONLY the TARGETING half — the heading and the rows under it — against the
       CURRENT weapon selection. Split out of createForSingleShip so a weapon going on or off
       can refresh this in place: a full update() would rebuild the name, the button row and the
       INCOMING list as well, and PhaseStrategy.onSystemDataChanged deliberately will not do that
       under whatever the player is clicking.

       ⚠️ `showTargeting` is a SNAPSHOT — PhaseStrategy builds the tooltip with
       selectedShipHasSelectedWeapons and it goes stale the moment the selection changes, which it
       does constantly: targeting a ship CONSUMES the selection, and the player can click a weapon
       off at any time. So re-ask rather than trust it. Without this a stale `true` leaves the
       TARGETING heading standing over an empty section, because weaponManager.targetingShipTooltip
       returns immediately once nothing is selected — it only ever LOOKED right because the old
       bare `.fire` wipe in update() took the heading's own <span> with it as collateral damage.
       (selectedShip may be null here; selectedShipHasSelectedWeapons reads gamedata.selectedSystems
       and ignores the ship it is handed.) */
    ShipTooltip.prototype.refreshTargeting = function () {
        //The stack tooltip has no targeting half: createForMultipleShips never shows `.fire`, and
        //there is no single ship to work out a bearing and a hit chance against.
        if (this.ships.length !== 1) return;

        var ship = this.ships[0];
        this.showTargeting = shipManager.systems.selectedShipHasSelectedWeapons(this.selectedShip);

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
    };

    /* Re-run the button row's condition lists. Every button that can act on a weapon asks
       gamedata.selectedSystems whether it has one - hasWeaponsSelected, hasHexWeaponsSelected,
       FFWeaponSelected, hasSplitWeaponFiringOrder - so "Target Weapons" and "Remove a Firing
       Order" would otherwise sit there inert after the player emptied the selection, and stay
       missing after they refilled it (user report 2026-08-24). The conditions were always right;
       nothing was asking them again.

       renderTo APPENDS, so the row has to be emptied first - that is why this is not simply a
       call to renderTo. Safe to run from inside a button's own click handler: onClick calls the
       action and returns without touching the element (shipTooltipMenu.js), and ShipTooltip's
       own update() already destroys these buttons the same way on every retarget. */
    ShipTooltip.prototype.refreshButtons = function () {
        //Same two guards the render site had: no menu on a plain hover tooltip, and
        //createForMultipleShips never draws a button row for a stack.
        if (!this.menu || this.ships.length !== 1) return;

        var buttons = jQuery(".buttons", this.element);
        buttons.html("");
        this.menu.renderTo(buttons, this);
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
        jQuery('<span class="name value ' + getAllyClass(ship) + '"' + getNameStyle(ship) + '>' + shipNameDisplay + '</span>').appendTo(this.element.find('.namecontainer'));

        //The rule under the name takes the NAME's own colour rather than a flat white —
        //see .namecontainer in shipTooltip.css. One property, read by the CSS, so the two
        //can never drift: getNameColor answers the same question getNameStyle does.
        this.element.css('--tt-name', getNameColor(ship));

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
            //HK Jamming: red UNCONTROLLED message on the turn the crit is in effect.
            //Uncontrolled is a oneturn crit rolled on turn T (turnend = T+1); it is in
            //effect on T+1, so show it when crit.turn + 1 === current turn. Gated on
            //remoteControl (very rare) so ordinary flights skip the crit scan entirely.
            if (ship.remoteControl && firstFighter && uncontrolledInEffect(firstFighter)) {
                this.addEntryElement('<span style="color:red;"><b>Uncontrolled</b></span>', true);
            }
            /* WALKERS_OF_SIGMA_PLAN.md 2.2 - "even if the fighter/shuttle does not drop out, it
               will not be able to shoot the next turn". Firing::withdrawGroundedFighterFireOrders
               enforces it on the advance path, so without this line the player declares a full
               turn's shooting and only finds out when the orders vanish (user request 2026-09-04).
               PURPLE, matching EWIconContainer's COLOR_JAM: the two Walker/EW "your unit is
               suppressed this turn" states then read as one family, distinct from red damage. */
            if (firstFighter && edfGroundedInEffect(firstFighter)) {
                this.addEntryElement('<span style="color:' + EDF_PURPLE + ';"><b>Energy Drained &mdash; cannot fire</b></span>', true);
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
            //Two turns can qualify now that placement and arrival are separate: the turn the unit
            //picks its entry hex, and the turn it actually arrives (when a late slot gets its own
            //Pre-Turn phase). Neither has a meaningful detection result to report.
            if (gamedata.gamephase == -1 && (shipManager.getTurnPlaced(ship) == gamedata.turn
                || shipManager.getTurnDeployed(ship) == gamedata.turn)) {
                toDisplay += '<span style="color:limegreen;">Deploying</span>; '; //Always say undetected on Deployment phase.
            } else {
                //A Shading Field / Cloaking Device still toggleable this phase is answered by
                //shipManager.getStealthToggleForecast - which is what isDetected would return
                //anyway, so take it directly rather than sweeping the enemy fleet twice. It has to
                //win outright: the stored detected/detectedNew arrays the own-ship fallback below
                //reads still hold the LAST committed check, and would otherwise pin the tooltip to
                //"Detected" for the whole Pre-Turn phase no matter how the player toggles.
                var forecast = shipManager.getStealthToggleForecast(ship);
                var isShipDetected = (forecast !== null) ? forecast : shipManager.isDetected(ship);
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

                if (!isShipDetected && forecast === null && ship.team == gamedata.getPlayerTeam()) {
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
                    //forecast !== null means the verdict came from the pending toggle, not from a
                    //committed check - detectedNew still lists the teams from LAST time, so naming
                    //them here would be wrong. The plain "Detected" is the honest answer.
                    if (forecast === null && ship.team == gamedata.getPlayerTeam()) { //Only own player needs to see full team list that's detected their ship.
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

        /* JUMP_POINTS_PLAN.md Stage 6 - this unit has declared a jump point, or has plotted a
           jump-out it has not committed yet. Yellow, the jump-point colour used everywhere else in
           the feature. See shipManager.isJumpingToHyperspace for what counts and why. */
        if (shipManager.isJumpingToHyperspace(ship)) {
            toDisplay += '<span style="color:#e1b000;">Jumping to Hyperspace</span>; ';
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
            var firstFighter = shipManager.systems.getSystem(ship, 1);
            if (firstFighter && shipManager.criticals.hasCritical(firstFighter, "LaunchedThisTurn")) {
                toDisplay += '<span style="color:cyan;">Just Launched</span>; ';
            }
        } else if (shipManager.criticals.hasCriticalInAnySystem(ship, "LCVLaunchedThisTurn")) {
            //LCV Rails: an LCV launched from a rail carries LCVLaunchedThisTurn on
            //its CnC (the -50 init that turn). Show the same cyan "Just Launched".
            toDisplay += '<span style="color:cyan;">Just Launched</span>; ';
        }
        if (shipManager.criticals.hasCriticalInAnySystem(ship, "HangarOperations")) {
            toDisplay += '<span style="color:cyan;">Hangar Operations</span>; ';
        }
        /* REINFORCEMENTS_PLAN.md STAGE 9 - this unit came out of hyperspace off course and is
           disordered for the turn it arrives on (user request 2026-08-29). Deliberately the same
           cyan span as Hangar Operations and Just Launched above it: all three are benign
           initiative penalties from something the unit just DID, not damage, and the colour is what
           says so. The figure is the server's own (shipManager.getArrivalIniPenalty). */
        var arrivalIni = shipManager.getArrivalIniPenalty(ship);
        if (arrivalIni !== 0) {
            toDisplay += '<span style="color:cyan;">Arrival Scatter (' + arrivalIni + ' Ini)</span>; ';
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
            //LCV Rails: each docked LCV adds +1 thrust to this turn's turn cost AND
            //turn delay (a flat surcharge on the per-turn value, NOT the rate shown
            //in parens). Matches the movement engine's getDockedLcvTurnSurcharge.
            var lcvTurnSurcharge = shipManager.movement.getDockedLcvTurnSurcharge(ship);
            //Turn cost is never less than 1 (matches the movement engine's
            //Math.max(1, ...) / speed-0 = 1-thrust rule); the tooltip previously
            //showed 0 at speed 0 even though a turn there actually costs 1. Turn
            //DELAY is genuinely 0 at speed 0 (a stationary ship has no delay), so
            //it is not clamped.
            var turncost = Math.max(1, Math.ceil(speed * baseTurnCost)) + lcvTurnSurcharge;
            var turnDelayCost = Math.ceil(speed * shipManager.movement.getTurnDelayCost(ship)) + lcvTurnSurcharge;

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

        this.refreshTargeting();

        this.ballisticsMenu.renderTo(ship, this.element);

        this.refreshButtons();
    }

    // The hover tooltip for a STACKED hex — several units under one cursor, none of them
    // committed to yet. It used to be a run-on comma-separated list of names, which is
    // the least useful shape the information has: the map shows you silhouettes, so a
    // wall of similar-coloured red text ("Nial Flight, Nial Flight, Sharlin, Mine, Mine")
    // asked the player to read where they had just been looking.
    //
    // It is now a grid of the same silhouettes, each on the hex picker's 3px allegiance
    // rail, with a flight's active fighter count printed over its art. That answers "what
    // is in this hex" at a glance and — deliberately — nothing more: this surface is a
    // PREVIEW. Identifying a particular unit is the picker's job, one click away, and
    // duplicating it here is how these two got confused with each other in the first
    // place (SELECT_FROM_SHIPS_PLAN.md §2.8).
    //
    // Touch is unaffected: there is no hover on a touchscreen, so this tooltip only ever
    // appears for a mouse.
    function createForMultipleShips(ships) {
        var grid = jQuery('<div class="tt-stack"></div>');

        ships.forEach(function (ship) {
            var shipNameDisplay = ship.name;
            var masked = false;
            if (ship.mine) {
                var stealthSystem = shipManager.systems.getSystemByName(ship, "mineStealth");
                if (stealthSystem && !stealthSystem.isMineRevealed(ship)) {
                    shipNameDisplay = "Mine";
                    masked = true;
                }
            }

            var cell = jQuery('<div class="tt-stack__cell"></div>').addClass(getAllyClass(ship));
            //Observers and 3+-team games override the rail and count colours per team,
            //exactly as the picker's rows do. Inline beats the allegiance class.
            var teamVars = getTeamColorVars(ship);
            if (teamVars) cell.attr('style', teamVars);

            cell.append(jQuery('<span class="tt-stack__bar"></span>'));

            //⚠️ A masked mine's imagePath still identifies the mine type that masking the
            //NAME exists to hide — the same trap the picker's thumbnails have to dodge.
            //It gets the generic glyph, never its own art.
            if (!masked && ship.imagePath && window.AssetManager) {
                //Same URL the map texture already fetched, so this is an HTTP cache hit
                //rather than a new download.
                cell.append(jQuery('<img class="tt-stack__art">')
                    .attr('alt', shipNameDisplay)
                    .attr('src', window.AssetManager.getSmartImagePath(ship.imagePath))
                    //Fall back to the glyph rather than hiding: in a grid with no names
                    //beside it, an empty cell says nothing at all.
                    .on('error', function () {
                        jQuery(this).replaceWith('<span class="tt-stack__art tt-stack__art--generic"></span>');
                    }));
            } else {
                cell.append(jQuery('<span class="tt-stack__art tt-stack__art--generic"></span>'));
            }

            if (ship.flight === true) {
                cell.append(jQuery('<span class="tt-stack__count"></span>')
                    .text(shipManager.systems.getActiveFighterCount(ship)));
            }

            grid.append(cell);
        });

        //No name here means nothing for the container's bottom rule to underline — see
        //.namecontainer--stack in shipTooltip.css.
        this.element.find('.namecontainer').addClass('namecontainer--stack').append(grid);

        jQuery(".ballistics", this.element).hide();
        //this.addEntryElement("Zoom closer, or click to interact");
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

    // What the CSS allegiance classes in tactical.css paint, as values rather than as
    // classes, so the rule under the name can be drawn in the name's own colour. The
    // BRIGHT tier (2026-08-20) — these must track .mine.name / .ally.name / .enemy.name
    // exactly, because this map and those rules answer the same question on the same
    // element and a drift shows up as a name whose underline is a different green.
    // Terrain has no bright twin and stays neutral.
    var ALLEGIANCE_NAME = {
        mine: 'var(--fv-own-bright)',
        ally: 'var(--fv-ally-bright)',
        enemy: 'var(--fv-enemy-bright)',
        terrain: 'var(--fv-neutral)'
    };

    // THE gate, in one place: does this ship take the absolute per-team palette, or the
    // relative mine/ally/enemy CSS classes? Mirrors the fleetList / combat-log scheme
    // (see gamedata.getFleetHeaderColorRGB):
    //   - Terrain: neutral — always the CSS 'terrain' class, never a team colour.
    //   - 2-team participant: relative mine/ally/enemy from getAllyClass, so your own
    //     fleet reads green whichever team number you happen to be.
    //   - Observer, OR 3+-team participant: absolute per-team palette, so each team is
    //     identifiable rather than everyone collapsing to two colours.
    function usesTeamColor(ship) {
        if (gamedata.isTerrain(ship.shipSizeClass, ship.userid)) return false;
        return !(gamedata.isPlayerInGame() && gamedata.getDistinctTeamCount() === 2);
    }

    // Inline colour for the ship name. Empty string on the class path — the CSS class
    // from getAllyClass is already carrying it, and returning nothing is what lets it.
    //
    // The two arms now agree on BRIGHTNESS as well as on scheme (2026-08-20): this one
    // has always returned the raw palette, and the .mine/.ally/.enemy .name rules in
    // tactical.css moved onto --fv-*-bright, which is that same palette as tokens. Until
    // then a 2-team participant saw pastel names and everyone else saw full chroma, on
    // the same tooltip — the mismatch the user reported. Keep them level.
    function getNameStyle(ship) {
        if (!usesTeamColor(ship)) {
            return '';
        }

        var rgb = gamedata.getTeamColorRGB(ship.team);
        return ' style="color:rgb(' + Math.round(rgb[0]) + ',' + Math.round(rgb[1]) + ',' + Math.round(rgb[2]) + ');"';
    }

    // The colour the name will ACTUALLY render in — the same decision as getNameStyle,
    // but resolved to a value on both arms instead of deferring to the class on one of
    // them. The rule under the name is painted with it, so the two can never drift.
    function getNameColor(ship) {
        if (!usesTeamColor(ship)) {
            return ALLEGIANCE_NAME[getAllyClass(ship)] || 'var(--fv-text)';
        }

        var rgb = gamedata.getTeamColorRGB(ship.team);
        return 'rgb(' + Math.round(rgb[0]) + ',' + Math.round(rgb[1]) + ',' + Math.round(rgb[2]) + ')';
    }

    // The stack grid's two per-cell channels, for the observer / 3+-team case only.
    // Byte-for-byte the same idea as SelectFromShips.getTeamColorVars: the RAW team
    // colour on the 3px rail, where a full-chroma value reads as a signal precisely
    // because there is so little of it, and a tone-mapped twin on the count badge, so an
    // arbitrary team colour lands in the same brightness band as the four fixed tints.
    function getTeamColorVars(ship) {
        if (!usesTeamColor(ship)) return '';

        var raw = gamedata.getTeamColorRGB(ship.team);
        var toned = (typeof gamedata.getMidTeamColorRGB === 'function')
            ? gamedata.getMidTeamColorRGB(ship.team)
            : raw;

        return '--row-bar:rgb(' + Math.round(raw[0]) + ',' + Math.round(raw[1]) + ',' + Math.round(raw[2]) + ');'
            + '--row-name:rgb(' + Math.round(toned[0]) + ',' + Math.round(toned[1]) + ',' + Math.round(toned[2]) + ');';
    }

    // HK Jamming: true when the flight's sample fighter carries an Uncontrolled crit
    // that is in effect THIS turn. Uncontrolled is a oneturn crit rolled on turn T
    // (turnend = T+1) and active on T+1, so match crit.turn + 1 === current turn.
    function uncontrolledInEffect(firstFighter) {
        if (!firstFighter || !firstFighter.criticals) return false;
        for (var i in firstFighter.criticals) {
            var crit = firstFighter.criticals[i];
            if (crit.phpclass === "Uncontrolled" && (crit.turn + 1) === gamedata.turn) {
                return true;
            }
        }
        return false;
    }

    /* Energy Draining Field: the flight cannot fire this turn. EdfFighterGrounded is a `oneturn`
       crit rolled in turn T's Critical Hit step (turnend = T+1) and in effect on T+1, so the test
       is the same crit.turn + 1 === current turn as Uncontrolled above. Read off the flight's
       sample fighter, which is where EdfExposure hangs every flight-wide record. */
    function edfGroundedInEffect(firstFighter) {
        if (!firstFighter || !firstFighter.criticals) return false;
        for (var i in firstFighter.criticals) {
            var crit = firstFighter.criticals[i];
            if (crit.phpclass === "EdfFighterGrounded" && (crit.turn + 1) === gamedata.turn) {
                return true;
            }
        }
        return false;
    }

    return ShipTooltip;
}();
