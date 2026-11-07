"use strict";

window.FirePhaseStrategy = function () {

    function FirePhaseStrategy(coordinateConverter) {
        PhaseStrategy.call(this, coordinateConverter);
        this.animationStrategy = new window.IdleAnimationStrategy();

        this.deploymentSprites = [];

        //An Expert Anticipator's turn (OFFICERS_PLAN.md Stage 23) pays its thrust on the Movement phase's panel - see onAssignThrust
        this.onZoomCallbacks.push(this.repositionThrustUi.bind(this));
        this.onScrollCallbacks.push(this.repositionThrustUi.bind(this));
        this.shipThrustUIState = null;
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
        this.uiManager.hideShipThrustUI();
        this.shipThrustUIState = null;
        this.shipIconContainer.getArray().forEach(function (icon) {
            icon.removeHexagonArcs();
        });
    };

    /* EXPERT ANTICIPATOR (OFFICERS_PLAN.md Stage 23): his ship's Firing-phase turn is an ordinary turn row (movement.js doNormalTurn),
       whose thrust the player assigns on the same panel as in the Movement phase - so the panel's two handlers are that phase's own. */
    FirePhaseStrategy.prototype.onAssignThrust = function (payload) {
        return MovementPhaseStrategy.prototype.onAssignThrust.call(this, payload);
    };

    FirePhaseStrategy.prototype.repositionThrustUi = function () {
        return MovementPhaseStrategy.prototype.repositionThrustUi.call(this);
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

    /* An Expert War Leader's icon waiting for its unit (OFFICERS_PLAN.md Stage 34): the unit clicked - an enemy, which arrives through targetShip,
       or one of his own fleet, through selectShip - is his wrath or his center of glory, when he may name it (officers.pickWarLeaderUnit). true
       when it was taken; anything else leaves the icon waiting and the click does what it always does. */
    FirePhaseStrategy.prototype.takeWarLeaderPick = function (ship) {
        if (!window.officers || !this.selectedShip || !officers.warLeaderPickingFor(this.selectedShip)) return false;
        if (!officers.pickWarLeaderUnit(this.selectedShip, ship)) return false;
        this.onShipMovementChanged({ ship: this.selectedShip }); //redraws the movement UI - the icon in its new colour - and the crosshairs
        return true;
    };

    //New version that allows targeting of allies when Friendly Fire Active - DK
    FirePhaseStrategy.prototype.selectShip = function (ship, payload) {
        if (this.takeWarLeaderPick(ship)) return; //Stage 34

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

        /* WALKERS OF SIGMA-957 (WALKERS_OF_SIGMA_PLAN.md 3.8, Stage 10B follow-up - user report
           2026-09-10, game 4344): AN ELINT KEEPS THE SELECTION WHILE ITS LATE EW WINDOW IS OPEN,
           which is the only thing that lets it spend a saved point on SOEW/SDEW.

           THE MISSING BUTTONS WERE NEVER THE MENU'S FAULT. ShipTooltipFireMenu already borrows the
           WHOLE Initial Orders EW button set (see its getAllButtons), so SOEW, SDEW, DIST, BDEW and
           Detect Stealth were all present and all correctly conditioned. What they could not get
           was a MENU IN WHICH THEY APPLY: SOEW/SDEW ask for isFriendly AND notSelf, and in these
           two phases clicking one of my own ships RE-SELECTED it, so the source and the target were
           forever the same unit. The player was left with exactly the two that shape allows - CCEW
           (isSelf) and OEW/DIST on an enemy, which route through targetShip instead - and every
           friendly-target ELINT function was unreachable. isElint was fine all along.

           InitialPhaseStrategy.selectShip has carried the same branch for years (its
           isElintOrAlliedEW test); this is that rule, narrowed to the window Stage 10B opened.

           NARROWED DELIBERATELY. Gating on ew.isLateEwWindowOpen rather than on isElint alone means
           an ordinary Firing phase - no EW Detector, or the allowance already spent - clicks exactly
           as it always has, so a fleet with an ELINT in it does not silently acquire a
           double-click-to-select in the phase where players are targeting weapons. The "Select ship"
           button is the escape hatch, identically to Initial Orders. */
        var canSourceLateEw = this.selectedShip && ship !== this.selectedShip
            && window.ew && ew.isLateEwWindowOpen(this.selectedShip)
            && (shipManager.isElint(this.selectedShip) || shipManager.hasSpecialAbility(this.selectedShip, "alliedEW"));

        /* MANUAL INTERCEPTION (MANUAL_INTERCEPTION_PLAN.md §19, user report 2026-10-06): A FLIGHT
           KEEPS THE SELECTION WHILE ITS SELECTED WEAPONS CAN COVER THE SHIP IT IS ESCORTING.

           The INCOMING list takes its interceptor from this.selectedShip, and clicking one of my own
           ships re-selected it - so an escorted ship's list only ever offered the ship's own weapons,
           and the flight the escort rule exists for (canInterceptBallistic's ship.flight branch,
           isLegalIntercept on the server) could never be hand-assigned. The re-select also threw
           the flight's weapon selection away. §17.3 recorded it and left escorts to the automation.

           NARROWED DELIBERATELY, like the ELINT branch above: only while a SELECTED weapon on the
           flight could take at least one shot aimed at this ship - the same predicate the row's
           click declares on. With nothing selected, or nothing in reach, the click selects the ship
           exactly as it always has, and "Select ship" is the escape hatch. */
        var canEscortIntercept = this.selectedShip && ship !== this.selectedShip
            && this.selectedShip.flight
            && weaponManager.getAllBallisticsAgainst(ship).some(function (ball) {
                return weaponManager.getSelectedInterceptorsFor(this.selectedShip, ball).length > 0;
            }, this);

        if (canSourceLateEw || canEscortIntercept) {
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
        } else if (gamedata.rules && gamedata.rules.friendlyFire === 1 || hexWeaponSelected) {

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


    FirePhaseStrategy.prototype.deselectShip = function (ship, keepWeapons) {
        PhaseStrategy.prototype.deselectShip.call(this, ship, keepWeapons);
        this.hideMovementUI();
    };

    FirePhaseStrategy.prototype.targetShip = function (ship, payload) {
        if (this.takeWarLeaderPick(ship)) return; //Stage 34: an Expert War Leader's wrath
        /* Same shape as InitialPhaseStrategy.targetShip, and for the same two reasons (user report
           2026-08-29): getTurnDeployed throws outright on null, and a selection that is not on the
           board yet must cost the menu its SOURCE, not cost the player the menu - the undefined
           `menu` this used to pass meant no button row at all, so Open Ship Details went with it. */
        var orderSource = (this.selectedShip && shipManager.getTurnDeployed(this.selectedShip) > gamedata.turn)
            ? null                      //selected, but not on the board yet - DK May 2025
            : this.selectedShip;

        var menu = new ShipTooltipFireMenu(orderSource, ship, this.gamedata.turn);
        if (!gamedata.showLoS) this.showShipTooltip(ship, payload, menu, false);
    };

    FirePhaseStrategy.prototype.onWeaponSelected = function (payload) {
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

    FirePhaseStrategy.prototype.setSelectedShip = function (ship) {
        PhaseStrategy.prototype.setSelectedShip.call(this, ship);
        //added extra check for combat pivots to allow cancelling these when flight has 0 thrust - DK 10.24
        //...and an Expert Evader's Evade, to give or to cancel (OFFICERS_PLAN.md), and a ship's Expert Anticipator turn (Stage 23)
        if (shipManager.movement.canPivot(ship) || (shipManager.movement.countCombatPivot(ship) > 0)
            || shipManager.movement.canEvade(ship) || shipManager.movement.hasEvaded(ship)
            || shipManager.movement.canAnticipate(ship) || shipManager.movement.hasAnticipated(ship)
            || (window.officers && officers.warLeaderState(ship))) { //...and an Expert War Leader's icon (Stage 34)
            this.drawMovementUI(this.selectedShip);
        }
    };

    FirePhaseStrategy.prototype.onMouseOutShips = function (ships, payload) {
        PhaseStrategy.prototype.onMouseOutShips.call(this, ships, payload);
    };

    FirePhaseStrategy.prototype.onSystemTargeted = function (payload) {
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

    FirePhaseStrategy.prototype.onShowTargetedHexagonInArc = function (payload) { //When a gravity designates a target add a hexagon equal to move range around target ship.              
        var shooterIcon = this.shipIconContainer.getByShip(payload.shooter);
        var targetIcon = this.shipIconContainer.getByShip(payload.target);
        var size = payload.size !== undefined ? payload.size : payload.system.moveDistance;
        targetIcon.showTargetedHexagonInArc(payload.shooter, shooterIcon, payload.system, size, payload.color, payload.opacity);
    };

    FirePhaseStrategy.prototype.onRemoveTargetedHexagonInArc = function (payload) { //When a gravity designates a move target location for its target, remove the hexgon(equal to move range)
        var targetIcon = this.shipIconContainer.getByShip(payload.target);
        targetIcon.removeTargetedHexagonInArc(payload.system);
    };


    return FirePhaseStrategy;
}();