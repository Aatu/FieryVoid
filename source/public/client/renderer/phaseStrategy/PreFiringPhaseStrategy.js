"use strict";

window.PreFiringPhaseStrategy = function () {

    function PreFiringPhaseStrategy(coordinateConverter) {
        PhaseStrategy.call(this, coordinateConverter);
        this.animationStrategy = new window.IdleAnimationStrategy();

        this.deploymentSprites = [];
    }

    PreFiringPhaseStrategy.prototype = Object.create(window.PhaseStrategy.prototype);

    PreFiringPhaseStrategy.prototype.activate = function (shipIcons, ewIconContainer, ballisticIconContainer, gamedata, webglScene, shipWindowManager) {

        this.changeAnimationStrategy(new window.IdleAnimationStrategy(shipIcons, gamedata.turn));

        PhaseStrategy.prototype.activate.call(this, shipIcons, ewIconContainer, ballisticIconContainer, gamedata, webglScene, shipWindowManager);

        infowindow.informPhase(5000, null);
        this.selectFirstOwnShipOrActiveShip();

        gamedata.showCommitButton();

        this.setPhaseHeader("PRE-FIRING");
        this.showAppropriateHighlight();
        this.showAppropriateEW();
        return this;
    };

    PreFiringPhaseStrategy.prototype.deactivate = function () {
        PhaseStrategy.prototype.deactivate.call(this);
        this.shipIconContainer.getArray().forEach(function (icon) {
            icon.removeHexagonArcs();
        });
    };

    PreFiringPhaseStrategy.prototype.onHexClicked = function (payload) {
        this.lastClickedShipId = -1;
        PhaseStrategy.prototype.onHexClicked.call(this, payload);
        var hex = payload.hex;

        if (!this.selectedShip) {
            return;
        }

        var hexTarget = gamedata.selectedSystems.some(function (system) {
            return system instanceof Weapon && system.hextarget === true;
        });

        if (hexTarget) {
            weaponManager.targetHex(this.selectedShip, payload.hex);
        }
    };

    //New version that allows targeting of allies when Friendly Fire Active - DK
    PreFiringPhaseStrategy.prototype.selectShip = function (ship, payload) {

        if (this.lastClickedShipId === ship.id && gamedata.isMyShip(ship) && this.selectedShip !== ship) {
            this.setSelectedShip(ship);
            var menu = new ShipTooltipFireMenu(this.selectedShip, ship, this.gamedata.turn);
            var ballisticsMenu = new ShipTooltipBallisticsMenu(this.shipIconContainer, this.gamedata.turn, true, this.selectedShip);
            if (!gamedata.showLoS) this.showShipTooltip(ship, payload, menu, false, ballisticsMenu);
        }

        this.lastClickedShipId = ship.id;

        var hexWeaponSelected = gamedata.selectedSystems.some(function (system) {
            return system instanceof Weapon && system.hextarget === true;
        });

        if (gamedata.rules && gamedata.rules.friendlyFire === 1 || hexWeaponSelected) {

            //if(gamedata.isMyorMyTeamShip(this.selectedShip) && weaponManager.hasShipWeaponsSelected()){            
            if (gamedata.isMyorMyTeamShip(this.selectedShip)) {
                var menu = new ShipTooltipFireMenu(this.selectedShip, ship, this.gamedata.turn);
                var ballisticsMenu = new ShipTooltipBallisticsMenu(this.shipIconContainer, this.gamedata.turn, true, this.selectedShip);
                menu.addButton("selectShip",
                    function () {
                        return this.selectedShip !== ship;
                    },
                    function () {
                        PhaseStrategy.prototype.setSelectedShip.call(this, ship);
                        this.showShipEW(this.selectedShip);
                    }.bind(this), "Select ship");
                if (!gamedata.showLoS) this.showShipTooltip(ship, payload, menu, false, ballisticsMenu);
            } else { //Remove this else block if we don't want to stadnardise double-click to select in Firing Phases
                this.setSelectedShip(ship);
                var menu = new ShipTooltipFireMenu(this.selectedShip, ship, this.gamedata.turn);
                var ballisticsMenu = new ShipTooltipBallisticsMenu(this.shipIconContainer, this.gamedata.turn, true, this.selectedShip);
                if (!gamedata.showLoS) this.showShipTooltip(ship, payload, menu, false, ballisticsMenu);
            }
        } else {
            this.setSelectedShip(ship);
            var menu = new ShipTooltipFireMenu(this.selectedShip, ship, this.gamedata.turn);
            var ballisticsMenu = new ShipTooltipBallisticsMenu(this.shipIconContainer, this.gamedata.turn, true, this.selectedShip);
            if (!gamedata.showLoS) this.showShipTooltip(ship, payload, menu, false, ballisticsMenu);
        }

    };

    PreFiringPhaseStrategy.prototype.deselectShip = function (ship, keepWeapons) {
        PhaseStrategy.prototype.deselectShip.call(this, ship, keepWeapons);
        this.hideMovementUI();
    };

    PreFiringPhaseStrategy.prototype.targetShip = function (ship, payload) {

        /* Same shape as InitialPhaseStrategy.targetShip, and for the same two reasons (user report
           2026-08-29): getTurnDeployed throws outright on null, and a selection that is not on the
           board yet must cost the menu its SOURCE, not cost the player the menu - the undefined
           `menu` this used to pass meant no button row at all, so Open Ship Details went with it. */
        var orderSource = (this.selectedShip && shipManager.getTurnDeployed(this.selectedShip) > gamedata.turn)
            ? null                      //selected, but not on the board yet - DK May 2025
            : this.selectedShip;

        var menu = new ShipTooltipFireMenu(orderSource, ship, this.gamedata.turn);
        this.showShipTooltip(ship, payload, menu, false);
    };

    PreFiringPhaseStrategy.prototype.onWeaponSelected = function (payload) {
        var ship = payload.ship;
        var weapon = payload.weapon;

        if (this.selectedShip !== ship) {
            this.lastClickedShipId = -1;
            this.setSelectedShip(ship);
        }

        //Deliberately does NOT forward to onSystemDataChanged any more. This event is raised
        //BEFORE the weapon is pushed into gamedata.selectedSystems - weaponManager.selectWeapon
        //has to order it that way, see the note there - so anything rendered from here reads a
        //selection that is one weapon short. selectWeapon now fires SystemDataChanged itself
        //immediately after the push, and that arrives at this strategy's inherited handler with
        //the selection complete. Switching the selected ship above is all this handler still owns.
    };

    PreFiringPhaseStrategy.prototype.setSelectedShip = function (ship) {
        PhaseStrategy.prototype.setSelectedShip.call(this, ship);
        //added extra check for combat pivots to allow cancelling these when flight has 0 thrust - DK 10.24
        if (shipManager.movement.canPivot(ship) || (shipManager.movement.countCombatPivot(ship) > 0)) {
            this.drawMovementUI(this.selectedShip);
        }
    };

    PreFiringPhaseStrategy.prototype.onMouseOutShips = function (ships, payload) {
        PhaseStrategy.prototype.onMouseOutShips.call(this, ships, payload);
    };

    PreFiringPhaseStrategy.prototype.onSystemTargeted = function (payload) {
        var ship = payload.ship;
        var system = payload.system;

        if (gamedata.rules && gamedata.rules.friendlyFire === 1) {
            if (gamedata.selectedSystems.length > 0 && weaponManager.canCalledshot(ship, system, this.selectedShip)) {
                weaponManager.targetShip(this.selectedShip, ship, system);
            }
        } else {
            if (gamedata.isEnemy(ship, this.selectedShip) && gamedata.selectedSystems.length > 0 && weaponManager.canCalledshot(ship, system, this.selectedShip)) {
                weaponManager.targetShip(this.selectedShip, ship, system);
            }
        }

        PhaseStrategy.prototype.onSystemDataChanged.call(this, { ship: ship });
    };

    PreFiringPhaseStrategy.prototype.onShowTargetedHexagonInArc = function (payload) { //When a gravity designates a target add a hexagon equal to move range around target ship.              
        var shooterIcon = this.shipIconContainer.getByShip(payload.shooter);
        var targetIcon = this.shipIconContainer.getByShip(payload.target);
        var size = payload.size !== undefined ? payload.size : payload.system.moveDistance;
        targetIcon.showTargetedHexagonInArc(payload.shooter, shooterIcon, payload.system, size, payload.color, payload.opacity);
    };

    PreFiringPhaseStrategy.prototype.onRemoveTargetedHexagonInArc = function (payload) { //When a gravity designates a move target location for its target, remove the hexgon(equal to move range)
        var targetIcon = this.shipIconContainer.getByShip(payload.target);
        targetIcon.removeTargetedHexagonInArc(payload.system);
    };

    return PreFiringPhaseStrategy;
}();