'use strict';

window.SelectFromShips = function () {

    var HTML = '<div class="shipNameContainer"></div>';

    function ShipTooltip(selectedShip, ships, payload, phaseStrategy) {
        this.element = jQuery(HTML);
        this.ships = [].concat(ships);
        //this.ships.sort(shipManager.hasBetterInitive); //so they're displayed in Ini order
        this.ships.sort(shipManager.hasWorseInitiveSort); //actualy make it so highest Ini is on top
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
            e.preventDefault(); e.stopPropagation();
        });
        this.element.on('mousemove', function (e) {
            e.preventDefault();
        });
        this.element.on('mouseout', function (e) {
            e.preventDefault(); e.stopPropagation();
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
        
        // --- INJECT CUSTOM "DEPLOY HERE" BUTTON DURING DEPLOYMENT PHASE ---
        if (gamedata.gamephase == -1 && this.phaseStrategy.selectedShip) {
            // Guarantee we don't present the deployment option if the unit is ALREADY right here
            var rawPos = shipManager.getShipPosition(this.phaseStrategy.selectedShip);
            var parsedSelectedPos = new hexagon.Offset(rawPos);
            
            // Replicate the exact isBlocked occupancy check from DeploymentPhaseStrategy to explicitly deny the button appearing when dropping onto illegal stacked hexes
            var isBlocked = false;
            var shipsInHex = shipManager.getShipsInSameHex(this.phaseStrategy.selectedShip, this.payload.hex);
            
            var hasTerrain = shipsInHex.some(function(s) {
                return gamedata.isTerrain(s.shipSizeClass, s.userid) || (s.Huge > 0 && s.Huge <= 3);
            });
            
            if (hasTerrain) {
                isBlocked = true;
            } else if (!(this.phaseStrategy.selectedShip.mine || this.phaseStrategy.selectedShip.flight)) {
                isBlocked = shipsInHex.some(function(s) { return !(s.mine || s.flight); });
            }
            
            if (!isBlocked && (!parsedSelectedPos || parsedSelectedPos.q !== this.payload.hex.q || parsedSelectedPos.r !== this.payload.hex.r)) {
                var selectedName = this.phaseStrategy.selectedShip.name;
                var deployButton = jQuery(
                    '<div class="name-value-button-ally">DEPLOY ' + selectedName.toUpperCase() + ' HERE</div>'
                ).on('click', function () {
                    this.phaseStrategy.onHexClicked(this.payload);
                    this.destroy();
                }.bind(this));
                
                this.element.append(deployButton);
            }
        }
        // ------------------------------------------------------------------

        this.ships.forEach(function (ship) {
            var shipNameDisplay = ship.name;
            if (ship.mine) {
                var stealthSystem = shipManager.systems.getSystemByName(ship, "mineStealth");
                if (stealthSystem && !stealthSystem.isMineRevealed(ship)) {
                    shipNameDisplay = "Mine";
                }
            }

            var deployedText = "";
            var deployTurn = shipManager.getTurnDeployed(ship);
            //if(shipManager.getTurnDeployed(ship) > gamedata.turn)  deployedText = '<span class="not-deployed">(Not Deployed)</span> ';
            if (deployTurn > gamedata.turn) deployedText = '<span class="not-deployed"> (Deploys Turn ' + deployTurn + ')</span> ';

            if (ship.flight) {
                var noOfFighters = 0;
                ship.systems.forEach(ftr => {
                    if (!shipManager.systems.isDestroyed(ship, ftr)) {
                        noOfFighters++;
                    }
                });
                var name = jQuery(
                    '<div class="name value button ' + getAllyClass(ship) + '">' + '(' + noOfFighters + ') ' + shipNameDisplay + deployedText + ' </div>'
                )
                    .on('click', function () {
                        if (gamedata.gamephase === -1) {
                            if (this.phaseStrategy.selectedShip) {
                                this.phaseStrategy.deselectShip(this.phaseStrategy.selectedShip);
                            }
                            this.phaseStrategy.selectShip(ship, this.payload);
                            this.destroy();
                        } else {
                            this.phaseStrategy.onShipClicked(ship, this.payload);
                            if (this.phaseStrategy.selectedShip && this.phaseStrategy.selectedShip.id === ship.id) {
                                this.destroy();
                            }
                        }
                    }.bind(this))
                    .on('mouseover', function () { this.phaseStrategy.onMouseOverShip(ship, this.payload) }.bind(this))
                    .on('mouseout', function () { this.phaseStrategy.onMouseOutShips(ship, this.payload) }.bind(this))
                name.contextmenu(function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    this.phaseStrategy.onShipRightClicked(ship, this.payload)
                }.bind(this))
                this.element.append(name)

            } else {
                var name = jQuery('<div class="name value button ' + getAllyClass(ship) + '">' + shipNameDisplay + deployedText + ' </div>')
                    .on('click', function () {
                        if (gamedata.gamephase === -1) {
                            if (this.phaseStrategy.selectedShip) {
                                this.phaseStrategy.deselectShip(this.phaseStrategy.selectedShip);
                            }
                            this.phaseStrategy.selectShip(ship, this.payload);
                            this.destroy();
                        } else {
                            this.phaseStrategy.onShipClicked(ship, this.payload);
                            if (this.phaseStrategy.selectedShip && this.phaseStrategy.selectedShip.id === ship.id) {
                                this.destroy();
                            }
                        }
                    }.bind(this))
                    .on('mouseover', function () { this.phaseStrategy.onMouseOverShip(ship, this.payload) }.bind(this))
                    .on('mouseout', function () { this.phaseStrategy.onMouseOutShips(ship, this.payload) }.bind(this))
                name.contextmenu(function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    this.phaseStrategy.onShipRightClicked(ship, this.payload)
                }.bind(this))
                this.element.append(name)
            }
        }, this)
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

        element.css("left", position.x - (element.width() + 30) / 2 + "px").css("top", (position.y - yOffset - element.height()) + "px");
    }

    function getAllyClass(ship) {
        /*
        if(ship.shipSizeClass == 5){
            return 'terrain'; //Return a neutral white colour for Terrain.
        }else{
            return gamedata.isMyOrTeamOneShip(ship) ?  'ally' : 'enemy';
        }   
        */
        return gamedata.isTerrain(ship.shipSizeClass, ship.userid) ? 'terrain' : (gamedata.isMyShip(ship) ? 'mine' : (gamedata.isMyorMyTeamShip(ship) ? 'ally' : 'enemy'))
    }

    return ShipTooltip;
}();
