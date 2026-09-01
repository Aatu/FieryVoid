"use strict";

window.InitialPhaseStrategy = function () {

    function InitialPhaseStrategy(coordinateConverter) {
        PhaseStrategy.call(this, coordinateConverter);
    }

    InitialPhaseStrategy.prototype = Object.create(window.PhaseStrategy.prototype);

    InitialPhaseStrategy.prototype.update = function (gamedata) {
        PhaseStrategy.prototype.update.call(this, gamedata);
        if (this.selectedShip) {
            this.ewIconContainer.showForShip(this.selectedShip);
        }
    };

    InitialPhaseStrategy.prototype.activate = function (shipIcons, ewIconContainer, ballisticIconContainer, gamedata, webglScene, shipWindowManager) {
        shipManager.power.repeatLastTurnPower();
        this.changeAnimationStrategy(new window.IdleAnimationStrategy(shipIcons, gamedata.turn));

        PhaseStrategy.prototype.activate.call(this, shipIcons, ewIconContainer, ballisticIconContainer, gamedata, webglScene, shipWindowManager);

        infowindow.informPhase(5000, function () { });
        this.selectFirstOwnShipOrActiveShip();
        gamedata.showCommitButton();

        combatLog.showCurrent(); //Reset Combat Log printouts.
        fleetListManager.updateFleetList(); //marked destroyed/jumped ships               
        this.setPhaseHeader("INITIAL ORDERS");
        return this;
    };

    InitialPhaseStrategy.prototype.deactivate = function () {
        //REINFORCEMENTS_PLAN.md Stage 4: an armed hex-pick mode belongs to THIS phase and nothing
        //else. Leaving it on would arm the map in Movement, where the first click would be
        //swallowed by a declaration the player can no longer make. Mirrors the way
        //DeploymentPhaseStrategy tears MineDeployment down.
        if (window.ReinforcementEntry) ReinforcementEntry.deactivate();

        PhaseStrategy.prototype.deactivate.call(this, true);

        return this;
    };

    /* REINFORCEMENTS_PLAN.md Stage 4 - THE EXIT HEX-PICK MODE GETS THE CLICK FIRST.

       ⚠️ INTERCEPTED HERE AND NOT IN onHexClicked. onHexClicked is only reached when the click
       landed on NO icon (see PhaseStrategy.onClickEvent's icons.length branch) - but a hex holding
       a ship is a perfectly legal place to open an exit, and a wave arriving on top of somebody
       is the ordinary case. Hooking the later method would silently refuse every occupied hex.

       Consuming the click also means the ordinary select/target dispatch never runs, so arming the
       mode cannot select a ship by accident on the way to picking a hex. */
    InitialPhaseStrategy.prototype.onClickEvent = function (payload) {
        if (window.ReinforcementEntry && ReinforcementEntry.isActive()
            && ReinforcementEntry.onMapClick(payload)) {
            return;
        }

        PhaseStrategy.prototype.onClickEvent.call(this, payload);
    };

    InitialPhaseStrategy.prototype.onHexClicked = function (payload) {
        this.lastClickedShipId = -1;
        PhaseStrategy.prototype.onHexClicked.call(this, payload);
        if (!this.selectedShip) {
            return;
        }

        var ballistics = gamedata.selectedSystems.filter(function (system) {
            return system.ballistic;
        });

        if (ballistics.length > 0) {
            weaponManager.targetHex(this.selectedShip, payload.hex);
        }
    };

    /* //Old version before allied targeting
    InitialPhaseStrategy.prototype.selectShip = function (ship, payload) {
        
        var position = this.coordinateConverter.fromGameToHex(this.shipIconContainer.getByShip(ship).getPosition());



        if (this.selectedShip && shipManager.isElint(this.selectedShip) && ship !== this.selectedShip){
            var menu = new ShipTooltipInitialOrdersMenu(this.selectedShip, ship, this.gamedata.turn, position); 
            menu.addButton("selectShip",
                function() {
                    return this.selectedShip !== ship;
                },
                function () {
                    PhaseStrategy.prototype.setSelectedShip.call(this, ship);
                    this.showShipEW(this.selectedShip);
                }.bind(this), "Select ship");
        } else if (gamedata.isMyShip(ship)) {
            PhaseStrategy.prototype.setSelectedShip.call(this, ship);
            var menu = new ShipTooltipInitialOrdersMenu(this.selectedShip, ship, this.gamedata.turn, position); 
            this.showShipEW(this.selectedShip);
        }

        if (!gamedata.showLoS) this.showShipTooltip(ship, payload, menu, false);
    };
    */

    //New version that allows targeting of allies when friendly fire option active - DK
    InitialPhaseStrategy.prototype.selectShip = function (ship, payload) {
        var position = this.coordinateConverter.fromGameToHex(this.shipIconContainer.getByShip(ship).getPosition());

        //Method to double click to instant own ships always, or single select and get EW/Firing Menus for ELINTs, Hex Weapons and alliedEW weapons 
        if (this.lastClickedShipId === ship.id && gamedata.isMyShip(ship) && this.selectedShip !== ship) {
            PhaseStrategy.prototype.setSelectedShip.call(this, ship);
            this.showShipEW(this.selectedShip);
            //Re-create menu now that this.selectedShip has changed, so it shows the Own-Ship menu instead of Target-Ship menu
            menu = new ShipTooltipInitialOrdersMenu(this.selectedShip, ship, this.gamedata.turn, position);
        }

        this.lastClickedShipId = ship.id;

        var hexWeaponSelected = gamedata.selectedSystems.some(function (system) {
            return system instanceof Weapon && system.hextarget === true;
        });

        var isElintOrAlliedEW = this.selectedShip && (shipManager.isElint(this.selectedShip) || shipManager.hasSpecialAbility(this.selectedShip, "alliedEW"));
        var friendlyFireActive = gamedata.rules && gamedata.rules.friendlyFire === 1;
        var clickedFriendly = gamedata.isMyorMyTeamShip(ship);

        // Add selectShip and don't instant target for:
        // - ELINT or alliedEW weapons
        // - Friendly ships when you have a hex weapon selected, or Friendly Fire rules are in effect
        if (this.selectedShip && ship !== this.selectedShip &&
            (isElintOrAlliedEW || (clickedFriendly && (hexWeaponSelected || friendlyFireActive)))) {

            var menu = new ShipTooltipInitialOrdersMenu(this.selectedShip, ship, this.gamedata.turn, position);
            menu.addButton("selectShip",
                function () {
                    return this.selectedShip !== ship;
                },
                function () {
                    PhaseStrategy.prototype.setSelectedShip.call(this, ship);
                    this.showShipEW(this.selectedShip);
                }.bind(this), "Select ship");

            this.showShipEW(this.selectedShip);

            // Default select ship if it's one of your own.        
        } else if (gamedata.isMyShip(ship)) {
            PhaseStrategy.prototype.setSelectedShip.call(this, ship);
            var menu = new ShipTooltipInitialOrdersMenu(this.selectedShip, ship, this.gamedata.turn, position);
            this.showShipEW(this.selectedShip);
        }

        if (!gamedata.showLoS) this.showShipTooltip(ship, payload, menu, false);
    };


    InitialPhaseStrategy.prototype.deselectShip = function (ship, keepWeapons) {
        PhaseStrategy.prototype.deselectShip.call(this, ship, keepWeapons);
        this.hideShipEW(ship);
    };

    InitialPhaseStrategy.prototype.onMouseOutShips = function (ships) {
        PhaseStrategy.prototype.onMouseOutShips.call(this, ships);
        if (this.selectedShip) {
            this.showShipEW(this.selectedShip);
        }
    };

    InitialPhaseStrategy.prototype.targetShip = function (ship, payload) {
        //TODO: Targeting ship with ballistic weapons
        //TODO: Targeting ship with support EW (defensive or offensive)

        /* ⚠️ THE NULL TEST ON this.selectedShip IS LOAD-BEARING (JUMP_GATES_PLAN.md trap 11).
           shipManager.getTurnDeployed opens with ship.osat and throws outright on null - and since
           Stage 3 "click the gate with NOTHING selected" is the PRIMARY gesture for signalling a
           fixed jump gate, not an edge case: no ship needs to be selected, because which of the
           player's units is within the gate's signal range is never chosen (plan section 2.1).

           ⭐ AN UNDEPLOYED SELECTION COSTS THE MENU ITS SOURCE, NOT THE PLAYER THE MENU (user
           report 2026-08-29). This used to hand showShipTooltip a `menu` that was still undefined
           - the var is hoisted, the assignment is below - which is not "a menu with no orders in
           it" but NO MENU AT ALL: ShipTooltip renders no button row without one and tears itself
           down on the first mouse event. So a unit that cannot issue an order took Open Ship
           Details and both jump gate signal buttons down with it, even though not one of those
           three reads the selected ship. Passing null instead says exactly what is true - there is
           no unit to issue orders FROM - and every EW/targeting condition in the menu already
           fails closed on that, while the target-only buttons stay. */
        var orderSource = (this.selectedShip && shipManager.getTurnDeployed(this.selectedShip) > gamedata.turn)
            ? null                      //selected, but not on the board yet - DK May 2025
            : this.selectedShip;

        var position = this.coordinateConverter.fromGameToHex(this.shipIconContainer.getByShip(ship).getPosition());
        var menu = new ShipTooltipInitialOrdersMenu(orderSource, ship, this.gamedata.turn, position);
        if (!gamedata.showLoS) this.showShipTooltip(ship, payload, menu, false);
    };

    InitialPhaseStrategy.prototype.createReplayUI = function (gamedata) {
        if (gamedata.turn === 1) {
            return;
        }

        this.replayUI = new ReplayUI().activate();
    };

    InitialPhaseStrategy.prototype.onWeaponSelected = function (payload) {
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

    InitialPhaseStrategy.prototype.onSystemTargeted = function (payload) { //25.11.23 - Added onSystemTargeted here to allow Called Shots in Initial Orders phase e.g. Limpet Bore.
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

    return InitialPhaseStrategy;
}();