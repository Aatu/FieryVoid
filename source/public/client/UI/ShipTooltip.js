'use strict';

window.ShipTooltip = function () {

    var HTML = '<div class="shipNameContainer">' + '<div class="namecontainer" style="border-bottom:1px solid white;margin-bottom:3px;"></div>' + '<div class="fire" style=";margin:3px 0px 3px 0px; padding:2px 0px 0px 0px;border-top:1px solid white;color:#b34119;"><span>TARGETING</span></div>' + '<div class="fire targeting"></div>' + '<div class="ballistics" style=";margin:3px 0px 3px 0px; padding:2px 0px 0px 0px;border-top:1px solid white;color:#b34119;"><span>INCOMING:</span></div>' + '<div class="ballistics incoming"></div>' + '<div class="buttons"></div>' + '</div>';

    function ShipTooltip(selectedShip, ships, position, showTargeting, menu, hexagon, ballisticsMenu) {
        this.element = jQuery(HTML);
        this.ships = [].concat(ships);
        this.position = position;
        //TODO: selected ship might be destroyed
        this.selectedShip = selectedShip;
        this.showTargeting = showTargeting;
        this.hexagon = hexagon;
        this.menu = menu;
        this.ballisticsMenu = ballisticsMenu;

        this.element.on('mousedown mouseup mouseover mousemove mouseout', function (e) {
            e.preventDefault();e.stopPropagation();
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

        return this.ships.some(function(ship) {
            return ships.includes(ship)
        })

    }


    function createForSingleShip(ship) {
        jQuery('<span class="name value ' + getAllyClass(ship) + '">' + ship.name + '</span>').appendTo(this.element.find('.namecontainer'));
        
        var jinking = shipManager.movement.getJinking(ship) * 5;
        var flightArmour = shipManager.systems.getFlightArmour(ship);
    
        //add info of flight-wide criticals!
	if (ship.flight === true){
            //get first fighter in flight
            var firstFighter = shipManager.systems.getSystem(ship, 1);
            var sensorDown = shipManager.criticals.hasCritical(firstFighter, "tmpsensordown");
            if (sensorDown > 0){
                sensorDown = sensorDown * 5;
                    this.addEntryElement("<i>OB temporarily lowered by <b>" + sensorDown + "</b></i>", true );
            }
            var iniDown = shipManager.criticals.hasCritical(firstFighter, "tmpinidown");
            if (iniDown > 0){
                iniDown = iniDown * 5;
                    this.addEntryElement("<i>Initiative temporarily lowered by <b>" + iniDown + "</b></i>",true );
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
	if (ship.flight === true && jinking > 0) toDisplay += 'Evasion: -' + jinking + ' to hit; ';
	if (shipManager.movement.isPivoting(ship) !== 'no') toDisplay += 'Pivoting; ';
	if (shipManager.movement.isRolling(ship)) toDisplay += 'Rolling; ';
	if (shipManager.movement.isRolled(ship)) toDisplay += 'Rolled; ';
	this.addEntryElement(toDisplay, toDisplay != '');
	    
	    /*condensed to one line
	    this.addEntryElement("Ballistic navigator aboard", ship.hasNavigator === true);
	    this.addEntryElement("Escorting ships in same hex", shipManager.isEscorting(ship));
	    */
	toDisplay = '';
	if (ship.hasNavigator === true) toDisplay += 'Navigator; ';
	var listEscorting = shipManager.listEscorting(ship);
	if (listEscorting != ''){
		toDisplay += 'Escorting: ';
		//list of unit names
		toDisplay += listEscorting;
	}
	this.addEntryElement(toDisplay, toDisplay != '');	    
	    
        //this.addEntryElement("Iniative Order: " + shipManager.getIniativeOrder(ship) + "    (D100 + " + ship.iniativebonus + ")");
        this.addEntryElement("Ini Order: " + shipManager.getIniativeOrder(ship) + " (total "+ship.iniative+"): base " + ship.iniativebonus + "; mod "+ ship.iniativeadded );
	    
	/*miscellanous info - once inserted, now disappeared; if it's needed, look for source code in Abbai branch!
	toDisplay = shipManager.systems.getMisc(ship);
	this.addEntryElement(toDisplay, toDisplay!=''); //miscellanous info from systems - special information o be shown here
	*/
	    
        //this.addEntryElement('Current turn delay: ' + shipManager.movement.calculateCurrentTurndelay(ship));
	var currDelay = shipManager.movement.calculateCurrentTurndelay(ship)
        var speed = shipManager.movement.getSpeed(ship);
        var turncost = Math.ceil(speed * ship.turncost);
        var turnDelayCost = Math.ceil(speed * ship.turndelaycost);

        this.addEntryElement('Pivot cost: ' + ship.pivotcost + ' Roll cost: ' + ship.rollcost, ship.flight !== true);
        this.addEntryElement('Pivot cost: ' + ship.pivotcost + ' Combat pivot cost: ' + Math.ceil(ship.pivotcost * 1.5), ship.flight === true);
	toDisplay = ''; //display Agile status
	if (ship.agile) toDisplay = ', Agile';
        this.addEntryElement('Turn Cost: ' + turncost + ' ('+ship.turncost+'); Turn Delay: ' + turnDelayCost + ' ('+ship.turndelaycost+')' + toDisplay);

	toDisplay = 'Thrust: ' + shipManager.movement.getRemainingEngineThrust(ship) + '/' + shipManager.movement.getFullEngineThrust(ship);//thrust: remaining/full
	this.addEntryElement(toDisplay, toDisplay!='');
        //this.addEntryElement('Unused thrust: ' + shipManager.movement.getRemainingEngineThrust(ship), ship.flight || gamedata.gamephase === 2);
	    
	toDisplay = 'Speed: ' + shipManager.movement.getSpeed(ship);
	if (currDelay>0) toDisplay += ' (delay '+currDelay+ ')';
	toDisplay += ' (acc cost: ' +ship.accelcost+')';
        this.addEntryElement(toDisplay);
        this.addEntryElement('Armor (F/S/A): ' + flightArmour, ship.flight === true);

        if (this.selectedShip) {
            if (! gamedata.isMyShip(ship)) {
                this.addEntryElement('OEW: ' + ew.getOffensiveEW(this.selectedShip, ship), this.selectedShip !== ship && ship.flight !== true && this.selectedShip.flight !== true);
            }

            if (shipManager.isElint(this.selectedShip)){
                this.addEntryElement('DIST: ' + ew.getOffensiveEW(this.selectedShip, ship, "DIST") / 3, this.selectedShip !== ship && ship.flight !== true);
            }
        }

        if (ew.getSupportedDEW(ship)) {
            this.addEntryElement('Support DEW: ' + ew.getSupportedDEW(ship), ship.flight !== true);
        }

        if (shipManager.isElint(ship)){
            this.addEntryElement('Blanket DEW: ' + ew.getEWByType('BDEW', ship), ship.flight !== true);
        }

        this.addEntryElement('DEW: ' + ew.getDefensiveEW(ship) + ' CCEW: ' + ew.getCCEW(ship), ship.flight !== true);
        var fDef = weaponManager.calculateBaseHitChange(ship, ship.forwardDefense) * 5;
        var sDef = weaponManager.calculateBaseHitChange(ship, ship.sideDefense) * 5;
        this.addEntryElement("Defence (F/S): " + fDef + "(" + ship.forwardDefense * 5 + ") / " + sDef + "(" + ship.sideDefense * 5 + ")%");

        if (this.selectedShip && this.selectedShip !== ship) {
            var dis = mathlib.getDistanceBetweenShipsInHex(this.selectedShip, ship);
            this.addEntryElement('DISTANCE: ' + dis + ' hexes');
        }

        if (this.selectedShip && gamedata.isEnemy(ship, this.selectedShip) && this.showTargeting) {
            weaponManager.targetingShipTooltip(this.selectedShip, ship, this.element, null);
            $(".fire", this.element).show();
        } else {
            $(".fire", this.element).hide();
        }

        this.ballisticsMenu.renderTo(ship, this.element);

        if (this.menu) {
            this.menu.renderTo(jQuery(".buttons", this.element), this);
        }
    }

    function createForMultipleShips(ships) {
	ships.sort(shipManager.hasBetterInitive);
        ships.forEach(function (ship, i) {
            var comma = i < ships.length - 1 ? ',' : '';

            jQuery('<span class="name value ' + getAllyClass(ship) + '">' + ship.name + comma + ' </span>').appendTo(this.element.find('.namecontainer'));

            $(".ballistics", this.element).hide();
        }, this);
        this.addEntryElement("Zoom closer, or click to interact");
    }

    function showBallisticsTooltip(ballistics) {}

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
        return gamedata.isMyOrTeamOneShip(ship) ?  'ally' : 'enemy';
    }

    return ShipTooltip;
}();
