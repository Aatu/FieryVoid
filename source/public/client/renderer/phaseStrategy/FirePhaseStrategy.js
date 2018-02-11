window.FirePhaseStrategy = (function(){

    function FirePhaseStrategy(coordinateConverter){
        PhaseStrategy.call(this, coordinateConverter);
        this.animationStrategy = new window.IdleAnimationStrategy();

        this.deploymentSprites = [];
    }

    FirePhaseStrategy.prototype = Object.create(window.PhaseStrategy.prototype);

    FirePhaseStrategy.prototype.activate = function (shipIcons, ewIconContainer, ballisticIconContainer, gamedata, webglScene) {

        this.changeAnimationStrategy(new window.IdleAnimationStrategy(shipIcons, gamedata.turn));

        PhaseStrategy.prototype.activate.call(this, shipIcons, ewIconContainer, ballisticIconContainer, gamedata, webglScene);

        infowindow.informPhase(5000, null);
        this.selectFirstOwnShipOrActiveShip();

        gamedata.showCommitButton();

        this.setPhaseHeader("FIRE ORDERS");
        return this;
    };

    FirePhaseStrategy.prototype.deactivate = function () {
        PhaseStrategy.prototype.deactivate.call(this);
    };

    FirePhaseStrategy.prototype.onHexClicked = function(payload) {
        var hex = payload.hex;

        if (!this.selectedShip) {
            return;
        }
    };

    FirePhaseStrategy.prototype.selectShip = function(ship, payload) {
        var menu = new ShipTooltipFireMenu(this.selectedShip, ship, this.gamedata.turn);
        menu.addButton("selectShip", null, function() {
            PhaseStrategy.prototype.selectShip.call(this, ship);
        }.bind(this), "Select ship");

        var ballisticsMenu = new ShipTooltipBallisticsMenu(this.shipIconContainer, this.gamedata.turn, true, this.selectedShip);

        this.showShipTooltip(ship, payload, menu, false, ballisticsMenu);
    };

    FirePhaseStrategy.prototype.deselectShip = function(ship) {
        PhaseStrategy.prototype.deselectShip.call(this, ship);
    };

    FirePhaseStrategy.prototype.targetShip = function(ship, payload) {
        var menu = new ShipTooltipFireMenu(this.selectedShip, ship, this.gamedata.turn);
        this.showShipTooltip(ship, payload, menu, false);
    };

    FirePhaseStrategy.prototype.untargetShip = function(ship) {};

    FirePhaseStrategy.prototype.onWeaponSelected = function(payload) {
        var ship = payload.ship;
        var weapon = payload.weapon;

        if (this.selectedShip !== ship) {
            this.setSelectedShip(ship);
        }
    };

    FirePhaseStrategy.prototype.setSelectedShip = function(ship) {
        PhaseStrategy.prototype.setSelectedShip.call(this, ship);
        this.showShipEW(ship);
    };

    FirePhaseStrategy.prototype.onMouseOutShips = function(ships, payload) {
        PhaseStrategy.prototype.onMouseOutShips.call(this, ships, payload);
        if (this.selectedShip) {
            this.showShipEW(this.selectedShip);
        }
    };

    return FirePhaseStrategy;
})();