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
        return this;
    };

    FirePhaseStrategy.prototype.deactivate = function () {
        PhaseStrategy.prototype.deactivate.call(this);
    };

    FirePhaseStrategy.prototype.onHexClicked = function(payload) {
        var hex = payload.hex;

        console.log("target hex");

        if (!this.selectedShip) {
            return;
        }
    };

    FirePhaseStrategy.prototype.selectShip = function(ship, payload) {
        var menu = new ShipTooltipFireMenu(this.selectedShip, ship, this.gamedata.turn);
        this.showShipTooltip(ship, payload, menu, false);
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


    return FirePhaseStrategy;
})();