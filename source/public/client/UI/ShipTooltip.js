'use strict';

window.ShipTooltip = function () {

    var HTML = '<div class="shipNameContainer">' + '<div class="namecontainer" style="border-bottom:1px solid white;margin-bottom:3px;"></div>' + '<div class="fire" style=";margin:3px 0px 3px 0px; padding:2px 0px 0px 0px;border-top:1px solid white;color:#b34119;"><span>TARGETING</span></div>' + '<div class="fire targeting"></div>' + '<div class="ballistics" style=";margin:3px 0px 3px 0px; padding:2px 0px 0px 0px;border-top:1px solid white;color:#b34119;"><span>INCOMING:</span></div>' + '<div class="ballistics incoming"></div>' + '<div class="buttons"></div>' + '</div>';

    function ShipTooltip(selectedShip, ships, position, showTargeting, menu, hexagon, ballisticsMenu) {
        this.element = jQuery(HTML);
        this.ships = [].concat(ships);
        this.position = position;
        this.selectedShip = selectedShip;
        this.showTargeting = showTargeting;
        this.hexagon = hexagon;
        this.menu = menu;
        this.ballisticsMenu = ballisticsMenu;

        this.element.on('mousedown', function (e) {
            e.preventDefault();
        });
        this.element.on('mouseup', function (e) {
            e.preventDefault();
        });
        this.element.on('mouseover', function (e) {
            e.preventDefault();e.stopPropagation();
        });
        this.element.on('mousemove', function (e) {
            e.preventDefault();
        });
        this.element.on('mouseout', function (e) {
            e.preventDefault();e.stopPropagation();
        });

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

    function createForSingleShip(ship) {

        jQuery('<span class="name value ' + getAllyClass(ship) + '">' + ship.name + '</span>').appendTo(this.element.find('.namecontainer'));

        var jinking = shipManager.movement.getJinking(ship) * 5;
        var flightArmour = shipManager.systems.getFlightArmour(ship);
        var misc = shipManager.systems.getMisc(ship);

        if (ship.base) {
            var direction;
            var html;

            if (ship.movement[1].value === -1) {
                direction = "port";
            } else if (ship.movement[1].value === 1) {
                direction = "starboard";
            }

            if (direction) {
                html = "Rotation towards " + direction;
                this.addEntryElement(html);
            }
        }

        this.addEntryElement("Ballistic navigator aboard", ship.hasNavigator === true);
        this.addEntryElement('Evasion: -' + jinking + ' to hit', ship.flight === true && jinking > 0);
        this.addEntryElement('Unused thrust: ' + shipManager.movement.getRemainingEngineThrust(ship), ship.flight === true);
        this.addEntryElement('Pivoting ' + shipManager.movement.isPivoting(ship), shipManager.movement.isPivoting(ship) !== 'no');
        this.addEntryElement('Rolling', shipManager.movement.isRolling(ship));
        this.addEntryElement('Rolled', shipManager.movement.isRolled(ship));
        this.addEntryElement('Turn delay: ', shipManager.movement.calculateCurrentTurndelay(ship));
        this.addEntryElement('Speed: ' + shipManager.movement.getSpeed(ship) + "    (" + ship.accelcost + ")");
        this.addEntryElement("Iniative Order: " + shipManager.getIniativeOrder(ship) + "    (D100 + " + ship.iniativebonus + ")");
        this.addEntryElement("Escorting ships in same hex", shipManager.isEscorting(ship));
        this.addEntryElement(misc, ship.flight !== true);
        this.addEntryElement(flightArmour, ship.flight === true);

        if (this.selectedShip) {
            this.addEntryElement('OEW: ' + ew.getOffensiveEW(this.selectedShip, ship), this.selectedShip !== ship, ship.flight !== true);
        }

        this.addEntryElement('DEW: ' + ew.getDefensiveEW(ship) + ' CCEW: ' + ew.getCCEW(ship), ship.flight !== true);
        var fDef = weaponManager.calculateBaseHitChange(ship, ship.forwardDefense) * 5;
        var sDef = weaponManager.calculateBaseHitChange(ship, ship.sideDefense) * 5;
        this.addEntryElement("Defence (F/S): " + fDef + "(" + ship.forwardDefense * 5 + ") / " + sDef + "(" + ship.sideDefense * 5 + ")%");

        if (this.selectedShip && this.selectedShip !== ship) {

            var dis = mathlib.getDistanceBetweenShipsInHex(this.selectedShip, ship);
            this.addEntryElement('DISTANCE: ' + dis);
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
        ships.forEach(function (ship, i) {
            var comma = i < ships.length - 1 ? ',' : '';

            jQuery('<span class="name value ' + getAllyClass(ship) + '">' + ship.name + comma + ' </span>').appendTo(this.element.find('.namecontainer'));

            $(".ballistics", this.element).hide();
        }, this);
        this.addEntryElement("Zoom closer to interact");
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
        if (!gamedata.thisplayer) {
            return 'neutral';
        }

        return ship.userid !== gamedata.thisplayer ? 'enemy' : 'ally';
    }

    return ShipTooltip;
}();