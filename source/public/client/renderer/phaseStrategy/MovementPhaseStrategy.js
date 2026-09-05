"use strict";

window.MovementPhaseStrategy = function () {

    function MovementPhaseStrategy(coordinateConverter) {
        PhaseStrategy.call(this, coordinateConverter);

        this.onZoomCallbacks.push(this.repositionThrustUi.bind(this));
        this.onScrollCallbacks.push(this.repositionThrustUi.bind(this));
        this.shipThrustUIState = null;
    }

    MovementPhaseStrategy.prototype = Object.create(window.PhaseStrategy.prototype);

    MovementPhaseStrategy.prototype.update = function (gamedata) {

        doForcedMovementForActiveShip();
        PhaseStrategy.prototype.update.call(this, gamedata);
        this.selectActiveShip();


        if (isMovementReady(gamedata)) {
            gamedata.showCommitButton();
        } else {
            gamedata.hideCommitButton();
        }

        //A poll is what advances the initiative bracket, so this is where the not-moved markers go
        //stale. Before it was called here they were painted once at activate() and then held the
        //whole phase, so a unit kept its ring after its group had been and gone.
        this.refreshNotMovedMarkers();
    };

    MovementPhaseStrategy.prototype.activate = function (shipIcons, ewIconContainer, ballisticIconContainer, gamedata, webglScene, shipWindowManager) {
        this.changeAnimationStrategy(new window.IdleAnimationStrategy(shipIcons, gamedata.turn));


        doForcedMovementForActiveShip();
        PhaseStrategy.prototype.activate.call(this, shipIcons, ewIconContainer, ballisticIconContainer, gamedata, webglScene, shipWindowManager);
        this.selectActiveShip();

        var shipName = this.selectedShip ? this.selectedShip.name : "";
        this.setPhaseHeader("MOVEMENT ORDERS", shipName);

        if (isMovementReady(gamedata)) {
            gamedata.showCommitButton();
        } else {
            gamedata.hideCommitButton();
        }


        this.showAppropriateHighlight();
        this.showAppropriateEW();

        this.refreshNotMovedMarkers();

        return this;
    };

    /* THE NOT-MOVED MARKERS: the neutral dotted ring, and the movement-group NUMBER over it.

       Both mark the same set - units whose initiative group has not come up yet this turn
       (SimultaneousMovementRule.isNotYetMovedShip) - so they are painted together and can never
       disagree. The number is the group number the Order of Battle prints down its left edge, so a
       player can read "this one moves in group 5" straight off the map instead of hunting the
       panel for the name.

       ONE BADGE PER HEX, not per ship. A dogpiled hex is drawn as a fan of overlapping icons
       (ShipIconContainer.getHexOffset), and a number per hull in that fan would be unreadable
       exactly where it is most wanted. So the hex shows the LOWEST group standing in it - the next
       one of that pile to move, which is the useful number.

       THE '+' MEANS "AND LATER GROUPS TOO", NOT "AND MORE SHIPS" (user ruling 2026-08-31). Six
       hulls stacked in one hex that ALL move in group 4 read "4", not "4+": the badge answers WHEN
       this hex moves, and for those six the answer is complete. It is only when the pile holds a
       group the number doesn't cover that the badge has to admit it - "4+" means group 4 moves
       next out of here and something later is in here as well. So the '+' marks an unanswered
       question, never a headcount; how many hulls are stacked is what the hex stack picker is for.

       Only UN-MOVED units are considered at all. A hex holding one un-moved group-4 ship and three
       that have already gone reads "4": the badge speaks for the units it is numbering, and an
       already-moved hull is not one of them.

       The badge is hung on the icon of the unit whose group it names, which is also the one drawn
       nearest the hex centre - getHexOffset fans units out in initiative order, so the earliest
       mover is the one with no offset at all. That falls out for free, but it is the reason the
       number sits over the middle of a pile rather than off on its edge. */
    MovementPhaseStrategy.prototype.refreshNotMovedMarkers = function () {
        this.shipIconContainer.getArray().forEach(function (icon) {
            icon.setNotMoved(false);
            icon.setIniOrderLabel(null);
        });

        //Belt and braces: this strategy only runs in gamephase 2, but a replay or a mid-phase
        //server flip must never leave a movement marker on the board.
        if (this.gamedata.gamephase !== 2) {
            return;
        }

        var pending = this.gamedata.ships.filter(window.SimultaneousMovementRule.isNotYetMovedShip);

        //Keyed by hex: the lowest group standing there, the icon that carries its badge, and
        //whether any OTHER group is present (which is the only thing that earns a '+').
        var byHex = {};

        pending.forEach(function (ship) {
            var icon = this.shipIconContainer.getByShip(ship);
            if (!icon) {
                return;
            }

            icon.setNotMoved(true);

            //Mines, terrain and not-yet-deployed units have no group of their own, so they get the
            //ring (as they always have) but no number, and they do not swell anyone else's '+'.
            var group = window.SimultaneousMovementRule.getMovementGroup(ship);
            if (!group) {
                return;
            }

            var move = icon.getLastMovement();
            if (!move || !move.position) {
                return;
            }

            var key = move.position.q + ',' + move.position.r;
            var entry = byHex[key];

            if (!entry) {
                byHex[key] = { icon: icon, group: group, mixed: false };
            } else if (group < entry.group) {
                //This displaces the badge onto the earlier mover. Whatever it displaced was by
                //definition a later group, so the hex is mixed however it got here.
                byHex[key] = { icon: icon, group: group, mixed: true };
            } else if (group > entry.group) {
                entry.mixed = true;
            }
            //group === entry.group: another hull that moves at the same time as the one already
            //numbered. It changes nothing a player needs from this badge, so it is not recorded.
        }, this);

        Object.keys(byHex).forEach(function (key) {
            var entry = byHex[key];
            entry.icon.setIniOrderLabel(entry.group + (entry.mixed ? '+' : ''));
        });
    };

    MovementPhaseStrategy.prototype.deactivate = function () {
        PhaseStrategy.prototype.deactivate.call(this, true);
        this.hideMovementUI();
        this.uiManager.hideShipThrustUI();

        gamedata.ships.forEach(function (ship) {
            var icon = this.shipIconContainer.getByShip(ship);
            icon.showSideSprite(false);
            icon.setNotMoved(false);
            icon.setIniOrderLabel(null);
        }, this);

        return this;
    };

    MovementPhaseStrategy.prototype.onShipRightClicked = function (ship) {
        this.shipWindowManager.open(ship);
    };

    MovementPhaseStrategy.prototype.onHexClicked = function (payload) {
        PhaseStrategy.prototype.onHexClicked.call(this, payload);
    };

    //Movement runs a sequential active-ship loop, so only a ship active in the current step
    //can be selected - tighter than the base "any ship of mine". Lives here rather than
    //inline in selectShip so programmatic selection (a commit-dialog ship link routed
    //through PhaseStrategy.onScrollToShip) is held to the same rule as a map click.
    MovementPhaseStrategy.prototype.canSelectShip = function (ship) {
        return gamedata.getMyActiveShips().includes(ship);
    };

    MovementPhaseStrategy.prototype.selectShip = function (ship, payload) {
        if (this.canSelectShip(ship)) {
            this.setSelectedShip(ship);
        }

        //ShipTooltipMovementMenu, not the plain base menu: it adds the Jump Out button when this
        //unit is standing in an open vortex it may leave through (JUMP_POINTS_PLAN.md Stage 4).
        var menu = new ShipTooltipMovementMenu(this.selectedShip, ship, this.gamedata.turn);
        if (!gamedata.showLoS) this.showShipTooltip(ship, payload, menu, false);
    };

    MovementPhaseStrategy.prototype.setSelectedShip = function (ship) {
        PhaseStrategy.prototype.setSelectedShip.call(this, ship);
        this.drawMovementUI(this.selectedShip);
    };

    MovementPhaseStrategy.prototype.targetShip = function (ship, payload) {
        var menu = new ShipTooltipMenu(this.selectedShip, ship, this.gamedata.turn);
        this.showShipTooltip(ship, payload, menu, false);
    };

    MovementPhaseStrategy.prototype.showShipTooltip = function (ships, payload, menu, hide, ballisticsMenu) {
        ships = [].concat(ships);

        if (this.selectedShip && this.shipThrustUIState) {
            return;
        }

        PhaseStrategy.prototype.showShipTooltip.call(this, ships, payload, menu, hide, ballisticsMenu);
    };

    MovementPhaseStrategy.prototype.onAssignThrust = function (payload) {
        if (payload === false) {
            this.uiManager.hideShipThrustUI();
            this.shipThrustUIState = null;
            return;
        }

        var ship = payload.ship;
        var icon = this.shipIconContainer.getByShip(ship);

        this.shipThrustUIState = {
            ship: ship,
            position: window.coordinateConverter.fromGameToViewPort(icon.getPosition()),
            rotation: icon.getFacing(),
            totalRequired: payload.totalRequired,
            remainginRequired: payload.remainginRequired,
            movement: payload.movement
        };

        this.uiManager.showShipThrustUI(this.shipThrustUIState);
    }

    MovementPhaseStrategy.prototype.repositionThrustUi = function () {
        if (this.shipThrustUIState === null) {
            return true;
        }

        var icon = this.shipIconContainer.getByShip(this.shipThrustUIState.ship);
        var position = window.coordinateConverter.fromGameToViewPort(icon.getPosition());
        jQuery("#thrustUIContainer").css({ left: position.x + 'px', top: position.y + 'px' })

        return true;
    }

    function isMovementReady(gamedata) {
        return gamedata.getMyActiveShips().every(function (ship) {
            return shipManager.movement.isMovementReady(ship);
        });
    }


    function doForcedMovementForActiveShip() {
        gamedata.getMyActiveShips().forEach(function (ship) {
            shipManager.movement.doForcedPivot(ship, true);

            if (ship.base && (!ship.nonRotating)) {
                shipManager.movement.doRotate(ship, true);

                //TODO: Test if this autocommit thing works
                gamedata.autoCommitOnMovement(ship);
            }
            //if (ship.hasAttached && Object.keys(ship.hasAttached).length > 0) {
                // Mirroring is now handled by onShipMovementChanged in PhaseStrategy.js - DK 04/26
            //}
        });
    }

    MovementPhaseStrategy.prototype.onShipMovementChanged = function (payload) {
        PhaseStrategy.prototype.onShipMovementChanged.call(this, payload);
        if (isMovementReady(this.gamedata)) {
            this.gamedata.showCommitButton();
        } else {
            this.gamedata.hideCommitButton();
        }

        this.onClickCallbacks = this.onClickCallbacks.filter(function (callback) {
            return callback();
        });

        this.gamedata.drawIniGUI();
    };

    MovementPhaseStrategy.prototype.showAppropriateEW = function () {
        this.shipIconContainer.getArray().forEach(icon => {
            icon.hideEW();
            icon.hideBDEW();
            icon.hideMDEW();
        });

        this.ewIconContainer.hide();
    }


    MovementPhaseStrategy.prototype.showAppropriateHighlight = function () {
        PhaseStrategy.prototype.showAppropriateHighlight.call(this);
        this.highlightUnmovedShips();
    }

    MovementPhaseStrategy.prototype.selectActiveShip = function () {

        var ship = gamedata.getMyActiveShips().filter(function (ship) {
            return !shipManager.movement.isMovementReady(ship) && !shipManager.isDestroyed(ship);
        }).pop();

        if (!ship) {
            ship = gamedata.getMyActiveShips().pop();
        }

        //getMyActiveShips can be empty (e.g. the only active ship is an Uncontrolled HK,
        //which the server drifts and which is excluded there) - don't select a ghost ship.
        if (ship) {
            this.setSelectedShip(ship);
        }
    };

    MovementPhaseStrategy.prototype.highlightUnmovedShips = function () {
        gamedata.ships
            .filter(window.SimultaneousMovementRule.isActiveMovementShip)
            .filter(function (ship) {
                return !shipManager.movement.isMovementReady(ship) || !gamedata.isMyShip(ship);
            })
            .forEach(function (ship) {
                var icon = this.shipIconContainer.getByShip(ship);
                //                icon.showSideSprite(true); //Shows circle, not dotted circle.
                icon.setSelected(true); //This actually sets icon for enemy ships that move during same sim phase - DK 10/24
            }, this);
    }

    return MovementPhaseStrategy;
}();
