'use strict';

window.PhaseStrategy = function () {

    function PhaseStrategy(coordinateConverter) {
        this.inactive = true;
        this.gamedata = null;
        this.shipIconContainer = null;
        this.ewIconContainer = null;
        this.ballisticIconContainer = null;
        this.shipWindowManager = null;
        this.coordinateConverter = coordinateConverter;
        this.currentlyMouseOveredIds = null;

        this.onMouseOutCallbacks = [];
        this.onZoomCallbacks = [this.repositionTooltip.bind(this), this.positionMovementUI.bind(this), this.repositionSelectFromShips.bind(this)];
        this.onScrollCallbacks = [this.repositionTooltip.bind(this), this.positionMovementUI.bind(this), this.repositionSelectFromShips.bind(this)];
        this.onClickCallbacks = [this.hideSystemInfo.bind(this, true)];

        this.selectedShip = null;
        this.targetedShip = null;
        this.animationStrategy = null;
        this.replayUI = null;

        this.shipTooltip = null;
        this.selectFromShips = null;
        this.movementUI = null;

        this.onDoneCallback = null;

        this.systemInfoState = null;
        this._lastHoveredHex = null;
        this._startHexRuler = null;

        this.uiManager = new window.UIManager($("body")[0]);
    }

    PhaseStrategy.prototype.onOpenShipWindowFor = function (payload) {
        this.shipWindowManager.open(payload.ship);
    }

    PhaseStrategy.prototype.onCloseShipWindow = function (payload) {
        this.shipWindowManager.close(payload.ship);
    }

    PhaseStrategy.prototype.onCloseSystemInfo = function () {
        this.hideSystemInfo(true);
    }

    PhaseStrategy.prototype.hideSystemInfo = function (force) {
        if (!this.systemInfoState) {
            return true;
        }

        if (!this.systemInfoState.menu || force) {
            this.uiManager.hideSystemInfo();
            this.systemInfoState = null;
        }

        this.shipIconContainer.getArray().forEach(function (icon) {
            icon.hideWeaponArcs();
        });

        return true;
    }

    PhaseStrategy.prototype.showSystemInfo = function (ship, system, element, menu) {
        if (this.systemInfoState && this.systemInfoState.menu && !menu) {
            return;
        }

        var boundingBox = element.getBoundingClientRect ? element.getBoundingClientRect() : element.get(0).getBoundingClientRect();

        if (menu) {
            if (!this.uiManager.canShowSystemInfoMenu(ship, system)) {
                return;
            }
            this.uiManager.showSystemInfoMenu({ ship: ship, selectedShip: this.selectedShip, system: system, boundingBox: boundingBox });
        } else {
            this.uiManager.showSystemInfo({ ship: ship, selectedShip: this.selectedShip, system: system, boundingBox: boundingBox });
        }
        this.systemInfoState = { menu: menu, element: element, system: system }
    }

    PhaseStrategy.prototype.consumeGamedata = function () {
        this.shipIconContainer.consumeGamedata(this.gamedata);
        this.animationStrategy.update(this.gamedata);
        this.ewIconContainer.consumeGamedata(this.gamedata, this.shipIconContainer);
        this.ballisticIconContainer.consumeGamedata(this.gamedata, this.shipIconContainer);
        this.redrawMovementUI();
    };

    PhaseStrategy.prototype.render = function (coordinateConverter, scene, zoom) {
        this.animationStrategy.render(coordinateConverter, scene, zoom);
    };

    PhaseStrategy.prototype.update = function (gamedata) {
        this.gamedata = gamedata;
        this.consumeGamedata();
        this.ewIconContainer.hide();
        this.ballisticIconContainer.show();
    };

    PhaseStrategy.prototype.activate = function (shipIcons, ewIconContainer, ballisticIconContainer, gamedata, webglScene, shipWindowManager, doneCallback) {
        this.shipIconContainer = shipIcons;
        this.ewIconContainer = ewIconContainer;
        this.ballisticIconContainer = ballisticIconContainer;
        this.gamedata = gamedata;
        this.inactive = false;
        this.consumeGamedata();
        this.shipIconContainer.setAllSelected(false);
        this.ballisticIconContainer.show();
        this.onDoneCallback = doneCallback;
        this.shipWindowManager = shipWindowManager;
        this.createReplayUI(gamedata);
        this.showAppropriateHighlight();
        this.showAppropriateEW();
        return this;
    };

    PhaseStrategy.prototype.deactivate = function () {
        this.inactive = true;
        this.animationStrategy.deactivate();
        this.replayUI && this.replayUI.deactivate();

        if (this.ballisticIconContainer) {
            this.ballisticIconContainer.hide();
        }

        if (this.ewIconContainer) {
            this.ewIconContainer.hide();
        }

        if (this.shipTooltip) {
            this.shipTooltip.destroy();
        }

        if (this.selectedShip) {
            this.deselectShip(this.selectedShip);
        }

        if (this.selectFromShips) { //To clear selectFromShips correctly if player clicks Commit before clicking anywhere else - DK 10/24
            this.hideSelectFromShips(this.selectFromShips);
        }

        this.currentlyMouseOveredIds = null;

        this.uiManager.hideWeaponList();
        this.hideSystemInfo(true);
        this.shipWindowManager.closeAll();

        this.shipIconContainer.getArray(icon => {
            icon.showSideSprite(false);
            icon.setHighlighted(false);
        })

        return this;
    };

    PhaseStrategy.prototype.onEvent = function (name, payload) {
        var target = this['on' + name];
        if (target && typeof target == 'function') {
            target.call(this, payload);
        }
    };

    PhaseStrategy.prototype.onScrollToShip = function (payload) {
        var icon = this.shipIconContainer.getById(payload.shipId)
        if (!shipManager.shouldBeHidden(icon.ship)) {
            window.webglScene.moveCameraTo(icon.getPosition())
        } else {
            return;
        }
    }

    PhaseStrategy.prototype.onScrollEvent = function (payload) {
        this.onScrollCallbacks = this.onScrollCallbacks.filter(function (callback) {
            return callback(payload);
        });
    };

    PhaseStrategy.prototype.onZoomEvent = function (payload) {
        this.onZoomCallbacks = this.onZoomCallbacks.filter(function (callback) {
            return callback(payload);
        });
    };

    PhaseStrategy.prototype.onClickEvent = function (payload) {
        var icons = getInterestingStuffInPosition.call(this, payload, this.gamedata.turn);

        this.onClickCallbacks = this.onClickCallbacks.filter(function (callback) {
            return callback();
        });

        if (icons.length > 1) {
            this.onShipsClicked(icons.map(function (icon) {
                return this.gamedata.getShip(icon.shipId);
            }, this), payload);
        } else if (icons.length === 1) {
            if (payload.button !== 0 && payload.button !== undefined) {
                this.onShipRightClicked(this.gamedata.getShip(icons[0].shipId), payload);
            } else {
                this.onShipClicked(this.gamedata.getShip(icons[0].shipId), payload);
            }
        } else {
            this.onHexClicked(payload);
        }
    };

    PhaseStrategy.prototype.onMouseDownEvent = function (payload) {
        if (gamedata.showLoS) {
            this._startHexRuler = payload.hex;
            mathlib.clearLosSprite();
        }
    };

    PhaseStrategy.prototype.onHexClicked = function (payload) {
        if (gamedata.showLoS) {
            if (payload.button == 2) { //Right click, just clear and reset to this.selectedShip
                this._startHexRuler = null; //reset.
                mathlib.clearLosSprite();
            } else {
                this._startHexRuler = null; //reset start point on any other type of click
                mathlib.clearLosSprite();
                this._startHexRuler = payload.hex;
            }
        }
    };

    PhaseStrategy.prototype.onShipsClicked = function (ships, payload) {

        // Filter out ships that are not yours or your team's + are stealth ships + not detected + not deployed yet.
        const filteredShips = ships.filter(ship =>
            !(shipManager.shouldBeHidden(ship))
        );

        if (gamedata.showLoS) {
            this._startHexRuler = payload.hex;
            mathlib.clearLosSprite();
        }

        if (filteredShips.length === 1) { //only one ship, we have to pretend the stealth ship(s) aren't on same hex!
            var ship = filteredShips[0];
            if (payload.button === 2) {
                this.onShipRightClicked(ship);
            } else {
                this.onShipClicked(ship, payload);
            }
        } else {
            if (filteredShips.length > 0 && !gamedata.showLoS) this.showSelectFromShips(filteredShips, payload); //More than 1, but not 0.  Prevents graphic from appearing and indicating where hidden ships are.
        }
    };

    PhaseStrategy.prototype.onShipRightClicked = function (ship) {

        if (shipManager.shouldBeHidden(ship)) return;  //Stealth equipped and undetected enemy, or not deployed yet - DK May 2025

        if (this.gamedata.isMyShip(ship)) {
            this.setSelectedShip(ship);
        }
        //Needs to have a separate method here, since this count as a hex clicked apparently.
        if (gamedata.showLoS) {
            this._startHexRuler = null; //reset start point on right-clicking ship
            mathlib.clearLosSprite();
        }

        this.shipWindowManager.open(ship);
    };

    PhaseStrategy.prototype.onShipClicked = function (ship, payload) {//30 June 2024 - DK - Added for Ally targeting.
        if (shipManager.shouldBeHidden(ship)) return;  //Stealth equipped and undetected enemy, or not deployed yet - DK May 2025

        if (gamedata.showLoS) {
            this._startHexRuler = payload.hex;
            mathlib.clearLosSprite();
        }

        if (gamedata.rules && gamedata.rules.friendlyFire === 1) {
            if (this.gamedata.isMyShip(ship)) {
                this.selectShip(ship, payload);
            } else {
                this.targetShip(ship, payload);
            }
        } else {
            if (this.gamedata.isMyShip(ship) && (!this.gamedata.canTargetAlly(ship))) {
                this.selectShip(ship, payload);
            } else {
                this.targetShip(ship, payload);
            }
        }
    };

    PhaseStrategy.prototype.selectShip = function (ship, payload) {
        this.setSelectedShip(ship);
        this.showAppropriateHighlight();
        this.showAppropriateEW();
        if (!gamedata.showLoS) {
            var menu = new ShipTooltipMenu(this.selectedShip, ship, this.gamedata.turn); //Don't show tooltip if ruler is on, as it blocks vision
            this.showShipTooltip(ship, payload, menu, false);
        }
    };

    PhaseStrategy.prototype.setSelectedShip = function (ship) {
        if ($(".confirm").length > 0) return;

        if (this.selectedShip) {
            this.deselectShip(this.selectedShip);
        }

        this.selectedShip = ship;
        this.shipIconContainer.getByShip(ship).setSelected(true);
        this.showAppropriateEW();

        if (this.shipTooltip) {
            this.shipTooltip.update(ship, this.selectedShip);
        }

        this.uiManager.showWeaponList({ ship: ship, gamePhase: gamedata.gamephase });
    };

    PhaseStrategy.prototype.deselectShip = function (ship) {
        this.shipIconContainer.getById(ship.id).setSelected(false);

        gamedata.selectedSystems.slice(0).forEach(function (selected) {
            weaponManager.unSelectWeapon(this.selectedShip, selected);
        }, this);

        this.selectedShip = null;
        this.uiManager.hideWeaponList();
        if (gamedata.showLoS) mathlib.clearLosSprite();
    };

    PhaseStrategy.prototype.targetShip = function (ship, payload) {
        var menu = new ShipTooltipMenu(this.selectedShip, ship, this.gamedata.turn);
        this.showShipTooltip(ship, payload, menu, false);
    };

    /*
    PhaseStrategy.prototype.targetShip = function (ship) {
        if (this.targetedShip) {
            this.untargetShip(this.targetedShip);
        }
        this.targetedShip = ship;
        this.shipIconContainer.getById(ship.id).setSelected(true);
    };

    PhaseStrategy.prototype.untargetShip = function (ship) {
        this.shipIconContainer.getById(ship.id).setSelected(false);
        this.targetedShip = null;
    };
    */

    PhaseStrategy.prototype.onMouseMoveEvent = function (payload) {
        var icons = getInterestingStuffInPosition.call(this, payload, this.gamedata.turn);

        // Initialize _lastHoveredHex if null
        if (!this._lastHoveredHex) this._lastHoveredHex = null;

        if (gamedata.showLoS) {
            // Only update _lastHoveredHex & showLoS if no icons (empty hex hover)
            if (icons.length === 0) {
                // Check if hex changed since last hover
                if (
                    !this._lastHoveredHex ||
                    this._lastHoveredHex.q !== payload.hex.q ||
                    this._lastHoveredHex.r !== payload.hex.r
                ) {
                    // Update with a copy of hex coords (avoid referencing the same object)
                    this._lastHoveredHex = { q: payload.hex.q, r: payload.hex.r };

                    mathlib.showLoS(this._startHexRuler, payload.hex);

                }
            } else {
                // If hovering a ship, reset _lastHoveredHex so next hex hover triggers showLoS
                this._lastHoveredHex = null;
            }
        }

        function doMouseOut() {
            if (this.currentlyMouseOveredIds) {
                this.currentlyMouseOveredIds = null;
            }

            this.onMouseOutCallbacks = this.onMouseOutCallbacks.filter(function (callback) {
                callback();
                return false;
            });

            this.onMouseOutShips(gamedata.ships, payload);

            // Reset hovered hex to force rerun on next move
            this._lastHoveredHex = null;
        }

        if (icons.length === 0 && this.currentlyMouseOveredIds !== null) {
            doMouseOut.call(this);
            return;
        } else if (icons.length === 0) {
            return;
        }

        var mouseOverIds = icons.reduce(function (value, icon) {
            return value + icon.shipId;
        }, '');

        if (mouseOverIds === this.currentlyMouseOveredIds) {
            return;
        }

        doMouseOut.call(this);

        this.currentlyMouseOveredIds = mouseOverIds;

        var ships = icons.map(function (icon) {
            return this.gamedata.getShip(icon.shipId);
        }, this);
        if (ships.length > 1) {
            this.onMouseOverShips(ships, payload);
        } else {
            this.onMouseOverShip(ships[0], payload);
        }
    };

    PhaseStrategy.prototype.onMouseOutShips = function (ships, payload) {
        this.showAppropriateHighlight();
        this.showAppropriateEW();

        if (window.LosSprite) mathlib.clearLosSprite();
    };

    PhaseStrategy.prototype.onMouseOverShips = function (ships, payload) {
        // Filter out ships that are not visible or shouldn't show tooltips
        if (gamedata.showLoS) mathlib.showLoS(this._startHexRuler, payload.hex)

        const visibleShips = ships.filter(ship => {
            if (shipManager.shouldBeHidden(ship)) return false;  //Enemy, stealth equipped and undetected, or not deployed yet - DK May 2025
            return true;
        });

        if (visibleShips.length === 0) return;

        if (this.shipTooltip && this.shipTooltip.isForAnyOf(visibleShips)) {
            return;
        }

        if (this.shipTooltip && this.shipTooltip.menu) {
            return;
        }

        if (!gamedata.showLoS) this.showShipTooltip(visibleShips, payload, null, true);
    };

    PhaseStrategy.prototype.onMouseOverShip = function (ship, payload) {

        if (gamedata.showLoS) mathlib.showLoS(this._startHexRuler, payload.hex);

        if (shipManager.shouldBeHidden(ship)) return;  //Enemy, stealth equipped and undetected, or not deployed yet - DK May 2025

        this.showAppropriateHighlight();
        this.showAppropriateEW();

        //trying to allow hex targeting more easily where there are friendly units located.
        var menu = null;
        var hasHex = weaponManager.hasHexWeaponsSelected()
        if (hasHex && this.gamedata.isMyorMyTeamShip(ship)) {
            if (this.gamedata.gamephase == 3) {
                menu = new ShipTooltipFireMenu(this.selectedShip, ship, this.gamedata.turn);
            }
            if (this.gamedata.gamephase == 1) {
                var position = this.coordinateConverter.fromGameToHex(this.shipIconContainer.getByShip(ship).getPosition());
                menu = new ShipTooltipInitialOrdersMenu(this.selectedShip, ship, this.gamedata.turn, position);
            }
        }

        var icon = this.shipIconContainer.getById(ship.id);
        if (!this.shipTooltip || !this.shipTooltip.menu) {
            //this.showShipTooltip(ship, payload, null, true);            
            if (!gamedata.isTerrain(ship.shipSizeClass, ship.userid) && !gamedata.showLoS) this.showShipTooltip(ship, payload, menu, true);
        }

        if (this.shipTooltip && this.shipTooltip.ships.includes(ship) && this.shipTooltip.ships.length === 1) {
            this.shipTooltip.update(ship, this.selectedShip);
        }



        this.showShipEW(ship);
        icon.showSideSprite(true);
        icon.showBDEW();
        icon.setHighlighted(true);
    };

    PhaseStrategy.prototype.showShipEW = function (ship) {
        this.shipIconContainer.getByShip(ship).showEW();
        this.ewIconContainer.showForShip(ship);
    };

    PhaseStrategy.prototype.hideShipEW = function (ship) {
        this.shipIconContainer.getByShip(ship).hideEW();
        this.ewIconContainer.hide();
    };

    PhaseStrategy.prototype.showShipTooltip = function (ships, payload, menu, hide, ballisticsMenu) {

        if (this.shipTooltip) {
            this.hideShipTooltip(this.shipTooltip)
        }

        ships = [].concat(ships);

        var position = payload.hex;
        if (ships.length === 1) {
            position = this.shipIconContainer.getByShip(ships[0]).getPosition();
        }

        if (!ballisticsMenu) {
            ballisticsMenu = new ShipTooltipBallisticsMenu(this.shipIconContainer, this.gamedata.turn, false);
        }

        var shipTooltip = new window.ShipTooltip(this.selectedShip, ships, position, shipManager.systems.selectedShipHasSelectedWeapons(this.selectedShip), menu, payload.hex, ballisticsMenu);

        this.shipTooltip = shipTooltip;
        this.onClickCallbacks.push(this.hideShipTooltip.bind(this, shipTooltip));

        if (hide) {
            this.onMouseOutCallbacks.push(this.hideShipTooltip.bind(this, shipTooltip));
        }
    };

    PhaseStrategy.prototype.showSelectFromShips = function (ships, payload) {
        var selectFromShips = new window.SelectFromShips(this.selectedShip, ships, payload, this)
        this.selectFromShips = selectFromShips;
        this.onClickCallbacks.push(this.hideSelectFromShips.bind(this, selectFromShips));
    };

    PhaseStrategy.prototype.hideShipTooltip = function (shipTooltip) {
        if (this.shipTooltip && this.shipTooltip === shipTooltip) {
            this.shipTooltip.destroy();
            this.shipTooltip = null;
        }
    };

    PhaseStrategy.prototype.hideSelectFromShips = function (selectFromShips) {
        if (this.selectFromShips && this.selectFromShips === selectFromShips) {
            this.selectFromShips.destroy();
            this.selectFromShips = null;
        }
    };

    PhaseStrategy.prototype.repositionSelectFromShips = function () {
        if (this.selectFromShips) {
            this.selectFromShips.reposition();
        }

        return true;
    };


    PhaseStrategy.prototype.repositionTooltip = function () {
        if (this.shipTooltip) {
            this.shipTooltip.reposition();
        }

        return true;
    };

    PhaseStrategy.prototype.positionMovementUI = function () {
        if (!this.movementUI) {
            return true;
        }

        var pos = this.coordinateConverter.fromGameToViewPort(this.movementUI.icon.getPosition());
        var heading = mathlib.hexFacingToAngle(this.movementUI.icon.getLastMovement().heading);

        UI.shipMovement.reposition(pos, heading);

        return true;
    };

    PhaseStrategy.prototype.redrawMovementUI = function () {

        if (gamedata.waiting) return;

        if (!this.selectedShip) {
            return;
        }

        if (this.movementUI && this.movementUI.ship.movement.some(function (movement) {
            return !movement.commit;
        })) {
            this.hideMovementUI();
            return;
        }

        this.drawMovementUI(this.selectedShip);
    };

    PhaseStrategy.prototype.drawMovementUI = function (ship) {
        if (gamedata.waiting) return;

        var drawn = UI.shipMovement.drawShipMovementUI(ship, new ShipMovementCallbacks(ship, this.onShipMovementChanged.bind(this)));

        if (drawn === false) {
            this.movementUI = null;
            return;
        }

        this.movementUI = {
            element: UI.shipMovement.uiElement,
            ship: ship,
            icon: this.shipIconContainer.getByShip(ship),
            position: null
        };

        UI.shipMovement.show();
        this.positionMovementUI();
    };

    PhaseStrategy.prototype.hideMovementUI = function () {
        UI.shipMovement.hide();
        this.movementUI = null;
    };

    PhaseStrategy.prototype.selectFirstOwnShipOrActiveShip = function () {
        var ship = gamedata.getFirstFriendlyShip();
        //TODO: what about active ship?
        if (ship) {
            this.setSelectedShip(ship);
        }
    };

    PhaseStrategy.prototype.done = function () {
        if (this.onDoneCallback) {
            this.onDoneCallback();
        }
    };

    PhaseStrategy.prototype.onSystemMouseOver = function (payload) {
        var ship = payload.ship;
        var system = payload.system;
        var element = payload.element;
        //systemInfo.showSystemInfo(element, weapon, ship, this.selectedShip);

        this.showSystemInfo(ship, system, element, false);

        this.shipIconContainer.getArray().forEach(function (icon) {
            icon.hideWeaponArcs();
        });

        if (system instanceof Ship) {
            return;
        }

        var icon = this.shipIconContainer.getByShip(ship);
        icon.showWeaponArc(ship, system);
    };

    PhaseStrategy.prototype.onSystemMouseOut = function () {
        this.shipIconContainer.getArray().forEach(function (icon) {
            icon.hideWeaponArcs();
        });

        this.hideSystemInfo();
    };

    PhaseStrategy.prototype.createReplayUI = function (gamedata) {
        this.replayUI = new ReplayUI().activate();
    };

    PhaseStrategy.prototype.changeAnimationStrategy = function (newAnimationStartegy) {
        this.animationStrategy && this.animationStrategy.deactivate();
        this.animationStrategy = newAnimationStartegy;
        this.animationStrategy.activate();
    };

    function getInterestingStuffInPosition(payload, turn) {
        return this.shipIconContainer.getIconsInProximity(payload).filter(function (icon) {
            var turnDestroyed = shipManager.getTurnDestroyed(icon.ship);
            return turnDestroyed === null || turnDestroyed >= turn;
        });
    }

    PhaseStrategy.prototype.setPhaseHeader = function (name, shipName) {

        if (name === false) {
            jQuery("#phaseheader").hide();
            return;
        }

        if (!shipName) {
            shipName = "";
        }

        $("#phaseheader .turn.value").html("TURN: " + this.gamedata.turn + ",");
        $("#phaseheader .phase.value").html(name);
        $("#phaseheader .activeship.value").html(shipName);
        $("#phaseheader").show();
    };

    PhaseStrategy.prototype.onShipEwChanged = function (payload) {
        var ship = payload.ship;

        if (this.shipTooltip) {
            this.shipTooltip.update(ship, this.selectedShip);
        }

        this.shipIconContainer.getByShip(ship).consumeEW(ship);
        this.ewIconContainer.updateForShip(ship);
        this.shipWindowManager.update();
        window.shipWindowManager.addEW(ship)
    };

    PhaseStrategy.prototype.onShipMovementChanged = function (payload) {
        var ship = payload.ship;
        this.shipIconContainer.getByShip(ship).consumeMovement(ship.movement);
        if (this.animationStrategy) {
            this.animationStrategy.shipMovementChanged(ship);
        }
        this.ballisticIconContainer.updateLinesForShip(ship, this.shipIconContainer);
        this.redrawMovementUI(ship);
    };

    PhaseStrategy.prototype.onShowAllEW = function (payload) {
        showGlobalEW.call(this, gamedata.ships, payload);
    };

    PhaseStrategy.prototype.onShowFriendlyEW = function (payload) {
        showGlobalEW.call(this, gamedata.ships.filter(function (ship) { return gamedata.isMyOrTeamOneShip(ship) }), payload);
    };

    PhaseStrategy.prototype.onShowEnemyEW = function (payload) {
        showGlobalEW.call(this, gamedata.ships.filter(function (ship) { return !gamedata.isMyOrTeamOneShip(ship) }), payload);
    };

    PhaseStrategy.prototype.showAppropriateEW = function () {
        this.shipIconContainer.getArray().forEach(icon => {
            icon.hideEW();
            icon.hideBDEW();
        });

        this.ewIconContainer.hide();
        if (this.selectedShip) {
            this.showShipEW(this.selectedShip);
        }
    }

    PhaseStrategy.prototype.showAppropriateHighlight = function () {
        this.shipIconContainer.getArray().forEach(icon => {
            icon.showSideSprite(false);
            icon.setHighlighted(false);
        })
    }

    function showGlobalEW(ships, payload) {
        if (payload.up) {
            this.showAppropriateEW();
        } else {
            ships.forEach(function (ship) {
                var icon = this.shipIconContainer.getById(ship.id);
                this.ewIconContainer.showByShip(ship);
                icon.showEW();
                icon.showBDEW();
            }, this);
        }
    }

    PhaseStrategy.prototype.onSystemDataChanged = function (payload) {
        var ship = payload.ship;
        var system = payload.system;

        if (this.selectedShip === ship) {
            this.uiManager.showWeaponList({ ship: ship, gamePhase: gamedata.gamephase })
        }

        if (this.systemInfoState) {
            this.showSystemInfo(ship, this.systemInfoState.system, this.systemInfoState.element, this.systemInfoState.menu);
        }

        if (system
            && (system.ballistic
                || system.hextarget //same for direct fire hextarget weapons - they use ballistic highlight...
                || system.canSplitShots //same for weapon that split shots, ballistic icons used to track these.
            )
        ) {
            this.ballisticIconContainer.consumeGamedata(this.gamedata, this.shipIconContainer);
        }

        this.shipWindowManager.update();
    }

    PhaseStrategy.prototype.onSystemClicked = function (payload) {
        var ship = payload.ship;
        var system = payload.system;
        var element = payload.element;
        if (shipManager.getTurnDeployed(ship) > gamedata.turn) return;

        this.showSystemInfo(ship, system, element, true);
        PhaseStrategy.prototype.onSystemDataChanged.call(this, { ship: ship, system: system });
    };

    PhaseStrategy.prototype.onHexTargeted = function (payload) {
        this.ballisticIconContainer.consumeGamedata(this.gamedata, this.shipIconContainer);
        this.shipWindowManager.update();

        if (this.selectedShip === payload.shooter) {
            this.uiManager.showWeaponList({ ship: payload.shooter, gamePhase: gamedata.gamephase })
        }
    };

    PhaseStrategy.prototype.onShipTargeted = function (payload) {
        /*        if (payload.weapons.some(function(weapon) {return weapon.ballistic})) {
                    this.ballisticIconContainer.consumeGamedata(this.gamedata, this.shipIconContainer);
                }
        */

        if (payload.weapons.some(function (weapon) {
            return weapon.ballistic || weapon.canSplitShots;
        })) {
            this.ballisticIconContainer.consumeGamedata(this.gamedata, this.shipIconContainer);
        }

        if (this.selectedShip === payload.shooter) {
            this.uiManager.showWeaponList({ ship: payload.shooter, gamePhase: gamedata.gamephase })
        }

        if (this.shipTooltip && this.shipTooltip.ships.includes(payload.target) && this.shipTooltip.ships.length === 1) {
            this.shipTooltip.update(payload.target, this.selectedShip);
        }

        this.shipWindowManager.update();
    };

    PhaseStrategy.prototype.onSplitOrderRemoved = function (payload) {

        if (this.shipTooltip && this.shipTooltip.ships.includes(payload.target) && this.shipTooltip.ships.length === 1) {
            this.shipTooltip.update(payload.target, this.selectedShip);
        }

        this.shipWindowManager.update();
    };

    PhaseStrategy.prototype.onToggleFriendlyBallisticLines = function (payload) {
        toggleBallisticLines.call(this, gamedata.ships.filter(function (ship) { return gamedata.isMyOrTeamOneShip(ship) }), payload);
    };

    PhaseStrategy.prototype.onToggleEnemyBallisticLines = function (payload) {
        toggleBallisticLines.call(this, gamedata.ships.filter(function (ship) { return !gamedata.isMyOrTeamOneShip(ship) }), payload);
    };

    PhaseStrategy.prototype.onShowAllBallistics = function (payload) {
        showAllBallisticLines.call(this, gamedata.ships, payload);
    };

    PhaseStrategy.prototype.onShowFriendlyBallistics = function (payload) {
        showAllBallisticLines.call(this, gamedata.ships.filter(function (ship) { return gamedata.isMyOrTeamOneShip(ship) }), payload);
    };

    PhaseStrategy.prototype.onShowEnemyBallistics = function (payload) {
        showAllBallisticLines.call(this, gamedata.ships.filter(function (ship) { return !gamedata.isMyOrTeamOneShip(ship) }), payload);
    };

    PhaseStrategy.prototype.onToggleLoS = function (payload) {
        if (payload.up) return;

        if (!gamedata.showLoS) {
            gamedata.showLoS = true;
            const hex = this._lastHoveredHex || { q: 0, r: 0 };
            mathlib.showLoS(this._startHexRuler, hex);
        } else {
            gamedata.showLoS = false;
            this._startHexRuler = null; //reset.            
            mathlib.clearLosSprite();
        }

        window.dispatchEvent(new CustomEvent("LoSToggled"));
    };

    PhaseStrategy.prototype.onToggleHexNumbers = function (payload) {

        if (payload.up) return; // Prevent repeating on key hold or keyup

        var scene = webglScene.scene;
        this.ballisticIconContainer.createHexNumbers(scene);
        window.dispatchEvent(new CustomEvent("HexNumbersToggled"));
    };

    function toggleBallisticLines(ships, payload) {
        this.ballisticIconContainer.toggleBallisticLines(ships);
        if (!this.gamedata.replay) this.ballisticIconContainer.consumeGamedata(this.gamedata, this.shipIconContainer);
    };

    function showAllBallisticLines(ships, payload) {
        if (payload.up) {
            this.ballisticIconContainer.hideLines(ships);
        } else {
            this.ballisticIconContainer.showLines(ships);
        }
        if (!this.gamedata.replay) this.ballisticIconContainer.consumeGamedata(this.gamedata, this.shipIconContainer);
    }


    return PhaseStrategy;
}();