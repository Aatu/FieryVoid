window.FirePhaseStrategy = (function(){

    function FirePhaseStrategy(coordinateConverter){
        PhaseStrategy.call(this, coordinateConverter);
        this.animationStrategy = new window.IdleAnimationStrategy();

        this.deploymentSprites = [];
    }

    FirePhaseStrategy.prototype = Object.create(window.PhaseStrategy.prototype);

    FirePhaseStrategy.prototype.activate = function (shipIcons, ewIconContainer, ballisticIconContainer, gamedata, webglScene) {
        PhaseStrategy.prototype.activate.call(this, shipIcons, ewIconContainer, ballisticIconContainer, gamedata, webglScene);

        infowindow.informPhase(5000, null);
        this.selectFirstOwnShipOrActiveShip();

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

    FirePhaseStrategy.prototype.selectShip = function(ship) {
        PhaseStrategy.prototype.selectShip.call(this, ship);
    };

    FirePhaseStrategy.prototype.deselectShip = function(ship) {
        PhaseStrategy.prototype.deselectShip.call(this, ship);
    };

    FirePhaseStrategy.prototype.targetShip = function(ship) {

        if (!this.selectedShip) {
            return;
        }

        console.log("target ship");
        weaponManager.targetShip(this.selectedShip, ship);
    };

    FirePhaseStrategy.prototype.untargetShip = function(ship) {};

    FirePhaseStrategy.prototype.onWeaponMouseOver = function(payload) {
        var ship = payload.ship;
        var weapon = payload.weapon;

        console.log("weapon mouseover");

        var icon = this.shipIconContainer.getByShip(ship);
        icon.showWeaponArc(ship, weapon);
    };

    FirePhaseStrategy.prototype.onWeaponMouseOut = function() {
        console.log("weapon mouseout");
        this.shipIconContainer.getArray().forEach(function (icon){ icon.hideWeaponArcs();});
    };

    FirePhaseStrategy.prototype.onWeaponSelected = function(payload) {
        var ship = payload.ship;
        var weapon = payload.weapon;

        console.log("weapon selected");

        this.selectShip(ship);

    };


    return FirePhaseStrategy;
})();