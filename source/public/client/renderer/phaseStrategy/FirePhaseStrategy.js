"use strict";

window.FirePhaseStrategy = function () {

    function FirePhaseStrategy(coordinateConverter) {
        PhaseStrategy.call(this, coordinateConverter);
        this.animationStrategy = new window.IdleAnimationStrategy();

        this.deploymentSprites = [];
    }

    FirePhaseStrategy.prototype = Object.create(window.PhaseStrategy.prototype);

    FirePhaseStrategy.prototype.activate = function (shipIcons, ewIconContainer, ballisticIconContainer, gamedata, webglScene, shipWindowManager) {

        this.changeAnimationStrategy(new window.IdleAnimationStrategy(shipIcons, gamedata.turn));

        PhaseStrategy.prototype.activate.call(this, shipIcons, ewIconContainer, ballisticIconContainer, gamedata, webglScene, shipWindowManager);

        infowindow.informPhase(5000, null);
        this.selectFirstOwnShipOrActiveShip();

        gamedata.showCommitButton();

        this.setPhaseHeader("FIRE ORDERS");
        this.showAppropriateHighlight();
        this.showAppropriateEW();
        return this;
    };

    FirePhaseStrategy.prototype.deactivate = function () {
        PhaseStrategy.prototype.deactivate.call(this);
        this.shipIconContainer.getArray().forEach(function (icon) {
            icon.removeHexagonArcs();
        });        
    };

    FirePhaseStrategy.prototype.onHexClicked = function (payload) {
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

    /* //Old version before allied targeting
    FirePhaseStrategy.prototype.selectShip = function (ship, payload) {
        this.setSelectedShip(ship);
        var menu = new ShipTooltipFireMenu(this.selectedShip, ship, this.gamedata.turn);
        var ballisticsMenu = new ShipTooltipBallisticsMenu(this.shipIconContainer, this.gamedata.turn, true, this.selectedShip);
        if (!gamedata.showLoS) this.showShipTooltip(ship, payload, menu, false, ballisticsMenu);
    };
    */

    //New version that allows targeting of allies when Friendly Fire Active - DK
    FirePhaseStrategy.prototype.selectShip = function (ship, payload) {

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

        if (gamedata.rules && gamedata.rules.friendlyFire  === 1 || hexWeaponSelected) {              

            //if(gamedata.isMyorMyTeamShip(this.selectedShip) && weaponManager.hasShipWeaponsSelected()){            
            if(gamedata.isMyorMyTeamShip(this.selectedShip)){
                var menu = new ShipTooltipFireMenu(this.selectedShip, ship, this.gamedata.turn); 
                var ballisticsMenu = new ShipTooltipBallisticsMenu(this.shipIconContainer, this.gamedata.turn, true, this.selectedShip);                   
                menu.addButton("selectShip",
                    function() {
                        return this.selectedShip !== ship;
                    },
                    function () {
                        PhaseStrategy.prototype.setSelectedShip.call(this, ship);
                        this.showShipEW(this.selectedShip);
                    }.bind(this), "Select ship"); 
                if (!gamedata.showLoS) this.showShipTooltip(ship, payload, menu, false, ballisticsMenu);                
            }else{ //Remove this else block if we don't want to stadnardise double-click to select in Firing Phases
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
    

    FirePhaseStrategy.prototype.deselectShip = function (ship) {
        PhaseStrategy.prototype.deselectShip.call(this, ship);
        this.hideMovementUI();
    };

    FirePhaseStrategy.prototype.targetShip = function (ship, payload) {
        if(shipManager.getTurnDeployed(this.selectedShip) > gamedata.turn){ //Selected ships is not deployed yet - DK May 2025
            this.showShipTooltip(ship, payload, menu, false);
            return;  
        }   
        var menu = new ShipTooltipFireMenu(this.selectedShip, ship, this.gamedata.turn);
        if (!gamedata.showLoS) this.showShipTooltip(ship, payload, menu, false);
    };

    FirePhaseStrategy.prototype.onWeaponSelected = function (payload) {
        var ship = payload.ship;
        var weapon = payload.weapon;

        if (this.selectedShip !== ship) {
            this.lastClickedShipId = -1;             
            this.setSelectedShip(ship);
        }

        PhaseStrategy.prototype.onSystemDataChanged.call(this, {ship: ship});
    };

    FirePhaseStrategy.prototype.setSelectedShip = function (ship) {
        PhaseStrategy.prototype.setSelectedShip.call(this, ship);
		//added extra check for combat pivots to allow cancelling these when flight has 0 thrust - DK 10.24
        if (shipManager.movement.canPivot(ship) || (shipManager.movement.countCombatPivot(ship) > 0)) { 
            this.drawMovementUI(this.selectedShip);
        }
    };

    FirePhaseStrategy.prototype.onMouseOutShips = function (ships, payload) {
        PhaseStrategy.prototype.onMouseOutShips.call(this, ships, payload);
    };

    FirePhaseStrategy.prototype.onSystemTargeted = function (payload) {
        var ship = payload.ship;
        var system = payload.system;

        if (gamedata.rules && gamedata.rules.friendlyFire  === 1) {        
            if (gamedata.selectedSystems.length > 0 && weaponManager.canCalledshot(ship, system, this.selectedShip)) {        
                weaponManager.targetShip(this.selectedShip, ship, system);
            }
        }else{
            if (gamedata.isEnemy(ship, this.selectedShip) && gamedata.selectedSystems.length > 0 && weaponManager.canCalledshot(ship, system, this.selectedShip)) { 
                weaponManager.targetShip(this.selectedShip, ship, system);
            }                               
        }    

        PhaseStrategy.prototype.onSystemDataChanged.call(this, {ship: ship});
    };

    FirePhaseStrategy.prototype.onShowTargetedHexagonInArc = function(payload){ //When a gravity designates a target add a hexagon equal to move range around target ship.              
        var shooterIcon = this.shipIconContainer.getByShip(payload.shooter);
        var targetIcon = this.shipIconContainer.getByShip(payload.target);
        targetIcon.showTargetedHexagonInArc(payload.shooter, shooterIcon, payload.system, payload.size, payload.color, payload.opacity);
    };  

    FirePhaseStrategy.prototype.onRemoveTargetedHexagonInArc = function(payload){ //When a gravity designates a move target location for its target, remove the hexgon(equal to move range)
        var targetIcon = this.shipIconContainer.getByShip(payload.target);        
        targetIcon.removeTargetedHexagonInArc(payload.system);
    };


    return FirePhaseStrategy;
}();