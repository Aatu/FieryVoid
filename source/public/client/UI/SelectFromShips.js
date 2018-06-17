'use strict';

window.SelectFromShips = function () {

    var HTML = '<div class="shipNameContainer"></div>';

    function ShipTooltip(selectedShip, ships, payload, phaseStrategy) {
        this.element = jQuery(HTML);
        this.ships = [].concat(ships);
        this.position = payload.hex;
        this.payload = payload;
        this.selectedShip = selectedShip;
        this.phaseStrategy = phaseStrategy;
        
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

        create.call(this);

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

    };

    function create() {
        console.log("CREATE select form ships", this.ships)
        this.ships.forEach(function (ship){
            var name = jQuery('<div class="name value button ' + getAllyClass(ship) + '">' + ship.name + ' </div>')
                .on('click', function() {this.phaseStrategy.onShipClicked(ship, this.payload), this.destroy()}.bind(this))
                .on('mouseover', function() {this.phaseStrategy.onMouseOverShip(ship, this.payload)}.bind(this))
                .on('mouseout', function() {this.phaseStrategy.onMouseOutShips(ship, this.payload)}.bind(this))
                name.contextmenu(function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    this.phaseStrategy.onShipRightClicked(ship, this.payload)
                }.bind(this))
            this.element.append(name)
        }, this)
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

        element.css("left", position.x - (element.width() + 30) / 2 + "px").css("top", (position.y - yOffset - element.height()) + "px");
    }

    function getAllyClass(ship) {
        if (!gamedata.thisplayer) {
            return ship.team !== 1 ? 'enemy' : 'ally';
        }

        return ship.userid !== gamedata.thisplayer ? 'enemy' : 'ally';
    }

    return ShipTooltip;
}();