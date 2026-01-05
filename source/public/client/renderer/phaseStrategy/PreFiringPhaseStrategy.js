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

        this.setPhaseHeader("PRE-FIRING ORDERS");
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

    PreFiringPhaseStrategy.prototype.selectShip = function (ship, payload) {
        this.setSelectedShip(ship);
        var menu = new ShipTooltipFireMenu(this.selectedShip, ship, this.gamedata.turn);
        var ballisticsMenu = new ShipTooltipBallisticsMenu(this.shipIconContainer, this.gamedata.turn, true, this.selectedShip);
        if (!gamedata.showLoS) this.showShipTooltip(ship, payload, menu, false, ballisticsMenu);
    };

    PreFiringPhaseStrategy.prototype.deselectShip = function (ship) {
        PhaseStrategy.prototype.deselectShip.call(this, ship);
        this.hideMovementUI();
    };

    PreFiringPhaseStrategy.prototype.targetShip = function (ship, payload) {
        if(shipManager.getTurnDeployed(this.selectedShip) > gamedata.turn){ //Selected ships is not deployed yet - DK May 2025
            this.showShipTooltip(ship, payload, menu, false);
            return;  
        }   
        var menu = new ShipTooltipFireMenu(this.selectedShip, ship, this.gamedata.turn);
        this.showShipTooltip(ship, payload, menu, false);
    };

    PreFiringPhaseStrategy.prototype.onWeaponSelected = function (payload) {
        var ship = payload.ship;
        var weapon = payload.weapon;

        if (this.selectedShip !== ship) {
            this.setSelectedShip(ship);
        }

        PhaseStrategy.prototype.onSystemDataChanged.call(this, {ship: ship});
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

        if (gamedata.isEnemy(ship, this.selectedShip) && gamedata.selectedSystems.length > 0 && weaponManager.canCalledshot(ship, system, this.selectedShip)) {
            weaponManager.targetShip(this.selectedShip, ship, system);
        }

        PhaseStrategy.prototype.onSystemDataChanged.call(this, {ship: ship});
    };

    PreFiringPhaseStrategy.prototype.onSplitOrderRemoved = function(payload) {

        if (this.shipTooltip && this.shipTooltip.ships.includes(payload.target) &&  this.shipTooltip.ships.length === 1) {
            this.shipTooltip.update(payload.target, this.selectedShip);
        }

        this.shipWindowManager.update();
    };

    PreFiringPhaseStrategy.prototype.onShowTargetedHexagonInArc = function(payload){ //When a gravity designates a target add a hexagon equal to move range around target ship.              
        var shooterIcon = this.shipIconContainer.getByShip(payload.shooter);
        var targetIcon = this.shipIconContainer.getByShip(payload.target);
        targetIcon.showTargetedHexagonInArc(payload.shooter, shooterIcon, payload.system, payload.system.moveDistance);
    };  

    PreFiringPhaseStrategy.prototype.onRemoveTargetedHexagonInArc = function(payload){ //When a gravity designates a move target location for its target, remove the hexgon(equal to move range)
        var targetIcon = this.shipIconContainer.getByShip(payload.target);        
        targetIcon.removeTargetedHexagonInArc(payload.system);
    };

    return PreFiringPhaseStrategy;
}();