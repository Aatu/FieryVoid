'use strict';

window.DeploymentPhaseStrategy = function () {

    function DeploymentPhaseStrategy(coordinateConverter) {
        PhaseStrategy.call(this, coordinateConverter);

        this.deploymentSprites = [];
    }

    DeploymentPhaseStrategy.prototype = Object.create(window.PhaseStrategy.prototype);

    DeploymentPhaseStrategy.prototype.activate = function (shipIcons, ewIconContainer, ballisticIconContainer, gamedata, webglScene, shipWindowManager) {

        this.changeAnimationStrategy(new window.IdleAnimationStrategy(shipIcons, gamedata.turn));

        PhaseStrategy.prototype.activate.call(this, shipIcons, ewIconContainer, ballisticIconContainer, gamedata, webglScene, shipWindowManager);

        this.deploymentSprites = createSlotSprites(gamedata, webglScene.scene);

        // Give MineDeployment access to deployment sprites for validation
        if (window.MineDeployment) window.MineDeployment.setDeploymentSprites(this.deploymentSprites);

        // Stage 7: expose the sprite list so DeploymentDock can re-run the
        // commit-button gate from the dock dialog without needing a back-ref
        // to this strategy instance. Cleared on deactivate to avoid leaking
        // a stale reference into later phases.
        window._deploymentSprites = this.deploymentSprites;

        combatLog.onTurnStart();
        infowindow.informPhase(5000, null);

        /* REINFORCEMENTS_PLAN.md Stage 7 - THE WAVE PLACES ITSELF (user request 2026-08-28).
           BEFORE selectFirstOwnShipOrActiveShip, so the selection lands on a unit that is already
           standing in its doorway and its movement UI (the speed arrows) draws straight away. */
        var arrived = this.autoPlaceArrivingReinforcements();

        this.selectFirstOwnShipOrActiveShip();

        showEnemyDeploymentAreas(this.deploymentSprites, gamedata);
        showAlliedDeploymentAreas(this.deploymentSprites, gamedata);

        //Say WHY there is nothing to place when the whole phase was a wave arriving. Only when
        //there is genuinely nothing else outstanding - a late slot placing on the same turn still
        //has real deployment to do and the header must not claim otherwise.
        this.setPhaseHeader(arrived.length > 0 && onlyOptionalPlacementsRemain(gamedata)
            ? "DEPLOYMENT: REINFORCEMENTS"
            : "DEPLOYMENT");

        //Show commit button Deployment Phase if player has no ships to deploy this turn, should never actually happen as server will skip Deployment Phases for these slots.
        if (!shipManager.hasShipsToDeployThisTurn(gamedata.thisplayer)) {
            if (this.selectedShip) this.deselectShip(this.selectedShip);
            this.setPhaseHeader("PRE-TURN ACTIONS");
            //Only if createReplayUI has not already made one (turn > 1). Turn 1 still gets a
            //Replay button here, exactly as it always has - a player with nothing to deploy is
            //sitting on PRE-TURN ACTIONS, not on a deployment.
            if (!this.replayUI) this.replayUI = new ReplayUI().activate();
            gamedata.showCommitButton();
            /*//Can auto-click it if we want.

            // Can simulate clicking confirm if needed.
            setTimeout(() => {
                $(".confirmok").trigger("click");
            }, 50); // Adjust delay if needed */

        } else if (onlyOptionalPlacementsRemain(gamedata)) {
            /* ⭐ REINFORCEMENTS_PLAN.md STAGE 7 - A PHASE WITH NO CLICK LEFT IN IT STILL HAS TO BE
               COMMITTABLE. The commit button is otherwise armed in exactly one place - onHexClicked,
               after a successful placement - which is right while every unit in the phase has to be
               placed by hand, and a dead end the moment none of them does. Both Stage 7 cases land
               here: a wave that autoPlaceArrivingReinforcements has just put on the board (nothing
               left to click), and the failure case of one whose doorway could not be resolved, which
               stays in hyperspace and must not hold the phase hostage.
               Deliberately not just `validateAllDeployment`: on turn 1 every ship sits at its
               slot's box centre, which validates, so that alone would arm the button before
               anybody had placed anything. */
            gamedata.showCommitButton();
        }

        return this;
    };

    /* Is every placement still outstanding an OPTIONAL one? Used only to arm the commit button
       above. Anything with a placement turn of THIS turn that has not been placed and is not one
       of the three optional kinds - a reinforcement arriving through a jump point, a flight queued
       for a deploy-start hangar dock, an LCV queued for a rail - is mandatory, and the phase is not
       finishable until it is on the board. */
    function onlyOptionalPlacementsRemain(gamedata) {
        for (var i in gamedata.ships) {
            var ship = gamedata.ships[i];
            if (!gamedata.isMyShip(ship)) continue;
            if (shipManager.getTurnPlaced(ship) != gamedata.turn) continue;
            if (ship.deploymove) continue;                                   //already placed this phase
            if (ship.pendingDeployDock || ship.pendingLcvDeployDock) continue;
            if (shipManager.isArrivingReinforcement(ship)) continue;

            return false;
        }

        return true;
    }

    DeploymentPhaseStrategy.prototype.deactivate = function () {
        PhaseStrategy.prototype.deactivate.call(this);
        // Clean up mine deployment mode and clear sprite reference
        if (window.MineDeployment) {
            window.MineDeployment.deactivate();
            window.MineDeployment.setDeploymentSprites(null);
        }
        window._deploymentSprites = null;
        this.deploymentSprites.forEach(function (icon) {
            icon.ownSprite.hide();
            icon.enemySprite.hide();
            icon.allySprite.hide();
            icon.terrainSprite.hide();
            if (icon.mineSprite) icon.mineSprite.hide();
        });
        //You can refresh screen if player has no ships, but not sure it's really necessary.        
        //if(!shipManager.playerHasDeployedShips(gamedata.thisplayer)) window.location.reload(); 
    };

    /* ⭐ REINFORCEMENTS_PLAN.md STAGE 7 - AN ARRIVAL PLACES ITSELF (user request 2026-08-28).
     *
     * There was never a choice to make. A reinforcement may stand in exactly one hex on exactly one
     * facing - the jump point it is riding decided both, a turn ago and by dice - so asking the
     * player to click that hex was ceremony, and ceremony that misbehaved: the doorway is Terrain,
     * so every click on it fired "You cannot deploy on terrain." (harmless, since the arrival
     * bypasses the block, but it popped up on every single unit of every wave). Placing them here
     * removes the click, the message and the "find the icon at the off-map start marker" hunt in
     * one go. Returns the units it placed.
     *
     * ⭐ SPEED IS STILL THE PLAYER'S. shipManager.movement.deploy carries the unit's existing speed
     * onto the deploy move and canChangeSpeed already allows the 0-10 accel arrows during
     * Deployment, so the phase keeps the one decision that was ever real (§2.4).
     *
     * ⚠️ THE `deploy` MOVE IS NOT PERSISTED UNTIL COMMIT, so this runs against a ship that has only
     * its off-board `start` row. The two guards are what stop a SECOND deploy row being added to a
     * unit that already has one - `deploymove` for a re-activate inside this page load, and the
     * movement scan for a reload after the phase was committed. validateDeployment throws
     * "Found more than one deployment entry" on a duplicate, which would take the whole submission
     * down.
     *
     * ⚠️ A UNIT WITH NO DOORWAY IS LEFT ALONE, deliberately, and every downstream path already
     * handles it: validateAllDeployment skips it so the commit button still arms, the phase -1
     * commit warning names it, and DeploymentGamePhase::releaseUnplacedReinforcements sends it back
     * to hyperspace with nothing spent. That is the only route by which a reinforcement can now
     * fail to arrive, so it must stay open. */
    DeploymentPhaseStrategy.prototype.autoPlaceArrivingReinforcements = function () {
        var placed = [];

        for (var i in gamedata.ships) {
            var ship = gamedata.ships[i];

            if (!gamedata.isMyShip(ship)) continue;
            if (!shipManager.isArrivingReinforcement(ship)) continue;
            if (shipManager.isDestroyed(ship)) continue;
            //Both hangar routes arrive INTO a hold and take no hex of their own.
            if (ship.pendingDeployDock || ship.pendingLcvDeployDock) continue;
            if (ship.deploymove) continue;                //already placed in this page load
            if (hasDeployMoveThisTurn(ship)) continue;    //...or on a load after the commit

            var vortex = shipManager.movement.getArrivalVortex(ship);
            if (!vortex) continue;

            //deploy() reads the facing off the same vortex, so the hex and the heading cannot
            //disagree - see shipManager.movement.deploy.
            shipManager.movement.deploy(ship, new hexagon.Offset(shipManager.getShipPosition(vortex)));
            placed.push(ship);

            //onShipMovementChanged goes straight to getByShip(...).consumeMovement, which throws on
            //a unit with no icon. One should always exist here (shouldBeHidden is false for an
            //arrival on its own turn), but the placement itself is the part that must not be lost
            //to a rendering accident - so the move is made first and the repaint is guarded.
            if (this.shipIconContainer && this.shipIconContainer.getByShip(ship)) {
                this.onShipMovementChanged({ ship: ship });
            }
        }

        return placed;
    };

    //A deploy row already committed for THIS turn, as the server counts them
    //(DeploymentGamePhase::releaseUnplacedReinforcements asks the same question of the same rows).
    function hasDeployMoveThisTurn(ship) {
        if (!ship.movement) return false;

        for (var i = 0; i < ship.movement.length; i++) {
            var move = ship.movement[i];
            if (move.type === "deploy" && move.turn == gamedata.turn) return true;
        }

        return false;
    }

    DeploymentPhaseStrategy.prototype.selectFirstOwnShipOrActiveShip = function () {
        var ship = gamedata.getFirstFriendlyShipDeployment();

        //TODO: what about active ship?
        if (ship) {
            this.setSelectedShip(ship);
        }
    };

    //Returns TRUE only when the click actually placed the selected unit. onShipClicked uses
    //that so a click which turns out not to be a placement can fall back to the ship tooltip
    //instead of being silently swallowed. No other caller reads the value.
    DeploymentPhaseStrategy.prototype.onHexClicked = function (payload) {
        PhaseStrategy.prototype.onHexClicked.call(this, payload);
        var hex = payload.hex;

        if (!this.selectedShip || (shipManager.getTurnPlaced(this.selectedShip) != gamedata.turn)) {
            //No selected ship, or the ship does not place on THIS turn. Note this is the PLACEMENT
            //turn, not the arrival turn: a reinforcement commits its entry hex the turn before it
            //arrives, and from the arrival turn on it is locked in like any other deployed unit.
            //
            //⚠️ `!=`, NOT `<` (user report 2026-08-29). `<` reads as "already placed on an earlier
            //turn, so no re-placement", which is right, but it also lets a unit that places LATER
            //through - and a unit still in hyperspace answers 999, the "not on the board" sentinel.
            //Such a click then ran the whole placement path against a unit that has no business
            //being placed at all, whose most visible symptom was getShipsInSameHex shouting "You
            //cannot deploy on terrain." at a click on a jump gate. This turn or nothing.
            return false;
        }

        if (validateDeploymentPosition(this.selectedShip, hex, this.deploymentSprites)) {
            var shipsInHex = shipManager.getShipsInSameHex(this.selectedShip, hex);
            var isBlocked = false;

            var hasTerrain = shipsInHex.some(function (s) {
                return gamedata.isTerrain(s.shipSizeClass, s.userid) || (s.Huge > 0 && s.Huge <= 3);
            });

            //LCVs are the smallest vessels and must be able to share the carrier's
            //hex to dock, so they're exempt from the "no two ships in one hex" block
            //(like mines/fighters). Terrain still blocks them.
            var selIsLcvUnit = !this.selectedShip.flight && !this.selectedShip.mine
                && String(this.selectedShip.hangarRequired || '').toLowerCase() === 'lcvs';

            /* REINFORCEMENTS_PLAN.md STAGE 7 - AN ARRIVAL BYPASSES BOTH BLOCKS, and it is not an
               optional nicety either way (plan §2.4).

               The terrain block would refuse it outright: the vortex IS terrain, and it is standing
               in the only hex this unit is allowed to occupy - so without this exemption a
               reinforcement could never be placed anywhere at all.

               The one-ship-per-hex block has to go too, because a whole wave comes through one
               doorway and they stack there freely until they separate on their first movement. The
               server has no such rule to relax (validateReinforcementArrival tests the hex and the
               facing and nothing else), so this is the only place it lives. */
            if (shipManager.isArrivingReinforcement(this.selectedShip)) {
                isBlocked = false;
            } else if (hasTerrain) {
                isBlocked = true;
            } else if (!(this.selectedShip.mine || this.selectedShip.flight || selIsLcvUnit)) {
                isBlocked = shipsInHex.some(function (s) { return !(s.mine || s.flight); });
            }

            if (!isBlocked) {
                shipManager.movement.deploy(this.selectedShip, hex);
                this.onShipMovementChanged({ ship: this.selectedShip });
                this.drawMovementUI(this.selectedShip);

                if (validateAllDeployment(this.gamedata, this.deploymentSprites)) {
                    gamedata.showCommitButton();
                }

                return true;
            }
        }

        return false;
    };

    DeploymentPhaseStrategy.prototype.onShipsClicked = function (ships, payload) {
        // Shift+Click bypass: place directly without showing the SelectFromShips picker when:
        //   - selected unit is a fighter/mine (drops onto stacked ships/mines/fighters), OR
        //   - selected unit is a regular ship and every stacked unit in the hex is a mine/fighter.
        if (payload && payload.shiftKey && this.selectedShip
            && shipManager.getTurnPlaced(this.selectedShip) >= gamedata.turn
            && validateDeploymentPosition(this.selectedShip, payload.hex, this.deploymentSprites)) {
            //REINFORCEMENTS_PLAN.md Stage 7: a whole wave stacks in one hex by rule, so shift-click
            //straight past the picker for an arrival too - the same convenience a fighter gets.
            var selIsFighterMine = this.selectedShip.mine || this.selectedShip.flight
                || shipManager.isArrivingReinforcement(this.selectedShip);
            var allStackedFighterMine = ships.every(function (s) {
                return s.id === this.selectedShip.id || s.mine || s.flight;
            }, this);
            if (selIsFighterMine || allStackedFighterMine) {
                this.onHexClicked(payload);
                return;
            }
        }

        PhaseStrategy.prototype.onShipsClicked.call(this, ships, payload);
    };

    DeploymentPhaseStrategy.prototype.onShipClicked = function (ship, payload) {//30 June 2024 - DK - Added for Ally targeting.
        if (shipManager.shouldBeHidden(ship)) return;  //Stealth equipped and undetected enemy, or not deployed yet - DK May 2025

        if (gamedata.showLoS) {
            this._startHexRuler = payload.hex;
            mathlib.clearLosSprite();
        }

        // Double-click on a friendly ship while a fighter/mine is selected: select that ship
        // directly instead of letting the SelectFromShips "Deploy here" popup intercept the click.
        var now = Date.now();
        var isDoubleClick = this._lastShipClickId === ship.id
            && (now - (this._lastShipClickTime || 0)) < 400;
        this._lastShipClickId = ship.id;
        this._lastShipClickTime = now;

        if (isDoubleClick && this.gamedata.isMyShip(ship)) {
            var placeTurn = shipManager.getTurnPlaced(ship);
            if (placeTurn === gamedata.turn || (placeTurn < gamedata.turn && ship.canPreOrder)) {
                if (this.selectedShip && this.selectedShip.id !== ship.id) {
                    this.deselectShip(this.selectedShip);
                }
                this.selectShip(ship, payload);
                return;
            }
        }

        // LCV Rails: an LCV can't share a deploy hex with other ships, so it docks
        // directly onto a carrier's free LCV rail instead of being placed. When an
        // LCV is selected and we click a different friendly LCV-capable carrier
        // with a free rail, show the SelectFromShips popup so its "DOCK <LCV> TO"
        // button appears — bypassing the mine/flight-only gate AND the deployment-
        // position check below (a docking LCV needs no board position of its own).
        if (this.selectedShip && this.selectedShip.id !== ship.id
            && !this.selectedShip.flight && !this.selectedShip.mine
            && String(this.selectedShip.hangarRequired || '').toLowerCase() === 'lcvs'
            && window.DeploymentDock
            && typeof window.DeploymentDock.carrierAcceptsLcvDeployDock === 'function'
            && window.DeploymentDock.carrierAcceptsLcvDeployDock(ship, this.selectedShip)) {
            this.showSelectFromShips([ship], payload);
            return;
        }

        // If we have a selected ship actively ready to deploy, and we click a valid DIFFERENT ship that is already placed on the map
        if (this.selectedShip && this.selectedShip.id !== ship.id) {
            var isPlacedOnMap = false;
            if (ship.movement && ship.movement.length > 0) {
                isPlacedOnMap = ship.movement[0].commit === true;
            }

            //LCVs are the smallest vessels: like mines/fighters they may share a hex,
            //so a click on a hex already holding a ship must still surface the
            //SelectFromShips popup (which offers DEPLOY LCV HERE + the cyan DOCK
            //button when a valid LCV carrier shares the hex). Without this the LCV
            //falls through to plain ship-selection and the player can never reach
            //the popup over an occupied hex.
            var selIsLcvUnit = !this.selectedShip.flight && !this.selectedShip.mine
                && String(this.selectedShip.hangarRequired || '').toLowerCase() === 'lcvs';

            /* REINFORCEMENTS_PLAN.md STAGE 7 - AND AN ARRIVAL, for the reason the LCV is here.
               A wave shares one hex, so from the SECOND unit onward every click on the doorway
               lands on a shipmate already standing in it. Without this the click falls through to
               the plain "select that ship" branch further down and the rest of the wave can never
               be placed at all - the stacking bypass in onHexClicked is never even reached. */
            var selIsArrival = shipManager.isArrivingReinforcement(this.selectedShip);

            var isTerrain = gamedata.isTerrain(ship.shipSizeClass, ship.userid) || (ship.Huge > 0 && ship.Huge <= 3);
            if (!isTerrain && isPlacedOnMap && (this.selectedShip.mine || this.selectedShip.flight || ship.mine || ship.flight || selIsLcvUnit || selIsArrival)) {
                // Ensure we only ever show the deployment stacking pop-up if the clicked location is actually 
                // a valid, legal deployment drop for our CURRENTLY selected piece.
                // This implicitly strips the pop-up out of the "deployment bay" clicking interaction.
                if (validateDeploymentPosition(this.selectedShip, payload.hex, this.deploymentSprites)) {
                    // Finally, don't show the deploy pop-up if the selected unit is already occupying this exact hex!
                    // getShipPosition can return raw {x,y} from the movement array, so we guarantee it's formatted as {q,r} hex coordinates
                    var rawPos = shipManager.getShipPosition(this.selectedShip);
                    var selectedPos = new hexagon.Offset(rawPos);

                    if (!selectedPos || selectedPos.q !== payload.hex.q || selectedPos.r !== payload.hex.r) {
                        // Shift+Click: skip the "Deploy here" popup and place directly on this hex.
                        // Allowed when the selected unit is a fighter/mine, or when the only thing in
                        // the hex (other than terrain, already excluded above) is a fighter/mine.
                        if (payload.shiftKey && (this.selectedShip.mine || this.selectedShip.flight || ship.mine || ship.flight || selIsArrival)) {
                            this.onHexClicked(payload);
                            return;
                        }
                        this.showSelectFromShips([ship], payload);
                        return;
                    } else {
                        /* ⭐ REINFORCEMENTS_PLAN.md STAGE 7a - AUTO-PLACEMENT PUT THE FLIGHT IN THE
                           CARRIER'S HEX, AND THAT BROKE THE DOCK GESTURE (user report 2026-08-28,
                           game 4318). "Select the flight, click the carrier" is how a fighter is
                           deploy-docked, and it works by falling into the SelectFromShips branch
                           above - which is reached only when the two are in DIFFERENT hexes. Before
                           Stage 7a an arriving flight sat at its own off-map start marker, so it
                           always was; now the whole wave is auto-placed in one doorway, every click
                           on the carrier lands on the hex the flight is already standing in, and
                           this branch swallowed it into a plain selection swap. The DOCK button
                           became unreachable, which left the carrier's tooltip button as the only
                           route in - the user's report.

                           Scoped to an arriving reinforcement FLIGHT deliberately: that is the only
                           case Stage 7a created. Two units that merely happen to share a hex on
                           turn 1 keep the old selection-swap, which is what a player expects when
                           nothing put them there automatically.

                           The picker decides for itself whether a DOCK is actually on offer (it
                           re-runs eligibleHangarsForFlight), so a carrier with no room simply shows
                           the ship list - it never claims a dock that would be refused. */
                        if (selIsArrival && this.selectedShip.flight
                            && window.DeploymentDock
                            && typeof window.DeploymentDock.shipHasOpenableDockDialog === 'function'
                            && window.DeploymentDock.shipHasOpenableDockDialog(ship)
                            && window.DeploymentDock.arrivesOnSameTurn(ship, this.selectedShip)) {
                            this.showSelectFromShips([ship], payload);
                            return;
                        }

                        // The selected ship is indeed legally placed, but it's ALREADY in the hex we clicked on.
                        // We shouldn't show a deploy menu or fall through to auto-deploy. We simply swap the selection.
                        this.selectShip(ship, payload);
                        return;
                    }
                }
            }
        }

        if (this.gamedata.isMyShip(ship) && ((shipManager.getTurnPlaced(ship) == gamedata.turn)
            || (shipManager.getTurnPlaced(ship) < gamedata.turn) && ship.canPreOrder)) { //Own ship and places this turn, just select it. Means that late-deployers can't deploy on ships with canPreOrder (unless they click very edge of hex), but that's rare.
            this.selectShip(ship, payload);
            return;
        }

        //Not a ship we can select, so try the click as a placement first — that is what lets a
        //unit deploy onto a hex an earlier-deployed one already occupies. Unchanged behaviour;
        //onHexClicked now just reports back whether it placed anything.
        if (this.onHexClicked(payload)) return;

        //Otherwise the click had nowhere to go and used to be swallowed in silence: clicking an
        //enemy ship during Deployment/Pre-Turn (and any ship at all during Pre-Turn, where nothing
        //is selected) did nothing whatsoever. Desktop players could still hover for the tooltip or
        //right-click for the ship window, but a touchscreen has neither, so an enemy ship was
        //completely uninspectable in these phases. Fall back to the standard ship tooltip menu —
        //exactly what every other phase does with a click that isn't an order (PhaseStrategy.
        //targetShip). Suppressed while the LoS ruler is up, which is the same rule selectShip
        //follows: the tooltip covers the line being measured.
        if (!gamedata.showLoS) this.targetShip(ship, payload);
    };

    DeploymentPhaseStrategy.prototype.setSelectedShip = function (ship) {
        PhaseStrategy.prototype.setSelectedShip.call(this, ship);
        var placeTurn = shipManager.getTurnPlaced(ship);
        if (placeTurn < gamedata.turn) return;

        showDeploymentArea(ship, this.deploymentSprites, this.gamedata);

        var hex = this.coordinateConverter.fromGameToHex(this.shipIconContainer.getByShip(ship).getPosition());
        if (validateDeploymentPosition(this.selectedShip, hex, this.deploymentSprites)) {
            this.drawMovementUI(this.selectedShip);
        }
    };

    // Stage 7: override selectShip so the tooltip menu for own ships gets a
    // "Dock pending flight here" button when the selected ship has a hangar
    // with free capacity AND the slot has flights still in the deployment
    // queue that fit. Base PhaseStrategy.selectShip already creates the
    // standard menu — we recreate it here so we can add the dock button
    // before it's rendered (addButton-after-render is a no-op).
    DeploymentPhaseStrategy.prototype.selectShip = function (ship, payload) {
        this.setSelectedShip(ship);
        this.showAppropriateHighlight();
        this.showAppropriateEW();
        if (gamedata.showLoS) return;

        var menu = new ShipTooltipMenu(this.selectedShip, ship, this.gamedata.turn);

        if (window.DeploymentDock && window.DeploymentDock.shipHasOpenableDockDialog(ship)) {
            //Reuse the firing-phase Dock button styling (dockFlight class →
            //img/dockFlight.png) so the icon is consistent across phases.
            //addLeadingButton places it to the LEFT of "Open ship details",
            //matching the Firing Phase tooltip order (action icons first,
            //info icon last).
            menu.addLeadingButton("recoverFlights",
                function () { return window.DeploymentDock.shipHasOpenableDockDialog(ship); },
                function () {
                    if (window.confirm && typeof window.confirm.hangarDeployDock === 'function') {
                        window.confirm.hangarDeployDock(ship);
                    }
                },
                "Deploy Flights in Hangar"
            );
        }

        this.showShipTooltip(ship, payload, menu, false);
    };

    DeploymentPhaseStrategy.prototype.deselectShip = function (ship, keepWeapons) {
        PhaseStrategy.prototype.deselectShip.call(this, ship, keepWeapons);
        hideDeploymentArea(ship, this.deploymentSprites, this.gamedata);
        this.hideMovementUI();
    };

    /* THE REPLAY BUTTON BELONGS IN ANY DEPLOYMENT PHASE THAT IS NOT TURN 1 (user request
       2026-08-30). This override used to suppress it outright, which was right while Deployment
       was a turn-1-only phase with no history behind it to replay. REINFORCEMENTS made Deployment
       recur mid-battle - a DEPLOYMENT: REINFORCEMENTS phase on turn 5 sits on four turns the
       player had no way to review without committing the phase first - so the gate is now the same
       one InitialPhaseStrategy uses, for the same reason.

       ⚠️ ALSO THE ONLY CREATION POINT WORTH HAVING. PhaseStrategy.activate calls this BEFORE
       activate()'s "no ships to deploy" branch below, which creates its own ReplayUI for the
       PRE-TURN ACTIONS case; that branch is now guarded on this.replayUI so the two cannot both
       append #replayUI to #topcontainer. */
    DeploymentPhaseStrategy.prototype.createReplayUI = function (gamedata) {
        if (gamedata.turn === 1) return;

        this.replayUI = new ReplayUI().activate();
    };

    function showEnemyDeploymentAreas(deploymentSprites, gamedata) {
        var team = gamedata.getPlayerTeam();
        var slot = gamedata.getPlayerSlot();
        deploymentSprites.forEach(function (icon) {
            if (icon.team != team && icon.available >= gamedata.turn) {
                icon.enemySprite.show();
            }
        });
    }

    function showAlliedDeploymentAreas(deploymentSprites, gamedata) {
        var team = gamedata.getPlayerTeam();
        var slot = gamedata.getPlayerSlot();
        deploymentSprites.forEach(function (icon) {
            if (icon.team == team && icon.slotId != "" + slot + "" && icon.playerid != gamedata.thisplayer && icon.available >= gamedata.turn) {
                // Let's try and also show the blue ally box.
                icon.allySprite.show();
            } //else if (icon.team == team && icon.slotId != "" + slot + "" && icon.playerid == gamedata.thisplayer) {
            //icon.ownSprite.show();   
            // }    
        });
    }

    function showDeploymentArea(ship, deploymentSprites, gamedata) {
        /* REINFORCEMENTS_PLAN.md STAGE 7 - an arrival's slot box is a LIE and must not be drawn.
           Its legal area is one hex on the far side of the map (the jump point exit), and
           lighting up the fleet's original deployment rectangle would point the player at the one
           place they definitely cannot go. The blue exit vortex is already on the board with
           its outward-pointing arrow, so the cue this replaces is there without drawing anything. */
        if (shipManager.isArrivingReinforcement(ship)) return;

        var icon = getSlotById(ship.slot, deploymentSprites);
        if (gamedata.isTerrain(ship.shipSizeClass, ship.userid)) {
            icon.terrainSprite.show();
        } else if (ship.mine) {
            // Mines can be selected from any slot, display visual boundary of map
            icon.mineSprite.show();
            icon.ownSprite.show();
        } else if (gamedata.isMyShip(ship)) {
            icon.ownSprite.show();
        } else {
            icon.enemySprite.show();
        }
    }

    function hideDeploymentArea(ship, deploymentSprites) {
        var icon = getSlotById(ship.slot, deploymentSprites);
        icon.ownSprite.hide();
        icon.enemySprite.hide();
        icon.allySprite.hide();
        icon.terrainSprite.hide();
        if (icon.mineSprite) icon.mineSprite.hide();
    }

    function getSlotById(slotId, deploymentSprites) {
        return deploymentSprites.filter(function (icon) {
            return icon.slotId == slotId;
        }).pop();
    }

    function createSlotSprites(gamedata, scene) {
        var myTeam = gamedata.getPlayerTeam();
        var enemyHoles = [];

        // 10 hex buffer required around enemy deployment zones (reduced by 1/5th hex for edge slack)
        var hexWidth = window.HexagonMath.getHexWidth();
        var hexHeight = window.HexagonMath.getHexRowHeight();
        var bufferX = hexWidth * 9.5;
        var bufferY = hexHeight * 9.5;

        Object.keys(gamedata.slots).forEach(function (key) {
            var slot = gamedata.slots[key];
            if (slot.team != myTeam) {
                var deploymentData = getDeploymentData(slot);
                enemyHoles.push({
                    position: deploymentData.position,
                    size: {
                        width: deploymentData.size.width + (bufferX * 2),
                        height: deploymentData.size.height + (bufferY * 2)
                    }
                });
            }
        });

        return Object.keys(gamedata.slots).map(function (key) {
            var slot = gamedata.slots[key];

            var deploymentData = getDeploymentData(slot);

            var ownSprite = new DeploymentIcon(deploymentData.position, deploymentData.size, 'own', scene, deploymentData.avail);
            var allySprite = new DeploymentIcon(deploymentData.position, deploymentData.size, 'ally', scene, deploymentData.avail);
            var enemySprite = new DeploymentIcon(deploymentData.position, deploymentData.size, 'enemy', scene, deploymentData.avail);

            var mapData = getMapData();

            var terrainSprite = new DeploymentIcon(mapData.position, mapData.size, 'terrain', scene, 1);

            // Give mines 1 extra hex of padding so they can deploy on the extreme board edges
            var mineMapData = getMapData(true);
            var mineSprite = new DeploymentIcon(mineMapData.position, mineMapData.size, 'mine', scene, 1, enemyHoles);

            return {
                slotId: key,
                team: slot.team,
                isValidDeploymentPosition: getValidDeploymentCallback(slot, deploymentData),
                ownSprite: ownSprite,
                allySprite: allySprite,
                enemySprite: enemySprite,
                terrainSprite: terrainSprite,
                mineSprite: mineSprite,
                playerid: deploymentData.playerid,
                available: deploymentData.available,
                deploymentData: deploymentData // Added to check bounds later 
            };
        });
    }

    function getValidDeploymentCallback(slot, deploymentData) {
        return function (hex) {
            if (slot.deptype != "box") {
                //TODO: support other deployment types than box;
                console.log("ONLY BOX DEPLOYMENT TYPE IS SUPPORTED AT THE MOMENT");
            }

            var hexPositionInGame = window.coordinateConverter.fromHexToGame(hex);

            var offsetPosition = {
                x: deploymentData.position.x - hexPositionInGame.x,
                y: deploymentData.position.y - hexPositionInGame.y
            };

            return Math.abs(offsetPosition.x) < Math.floor(deploymentData.size.width / 2) && Math.abs(offsetPosition.y) < Math.floor(deploymentData.size.height / 2);
        };
    }

    function validateTerrainDeployment(hex) {
        var mapData = getMapData(false);
        var hexPositionInGame = window.coordinateConverter.fromHexToGame(hex);

        var offsetPosition = {
            x: mapData.position.x - hexPositionInGame.x,
            y: mapData.position.y - hexPositionInGame.y
        };

        return Math.abs(offsetPosition.x) < Math.floor(mapData.size.width / 2) && Math.abs(offsetPosition.y) < Math.floor(mapData.size.height / 2);
    }

    function validateMineDeployment(hex, ship, deploymentSprites) {
        // Mines use the +1 hex padded bounds for edge deployment
        var mapData = getMapData(true);
        var hexPositionInGame = window.coordinateConverter.fromHexToGame(hex);

        var offsetPosition = {
            x: mapData.position.x - hexPositionInGame.x,
            y: mapData.position.y - hexPositionInGame.y
        };

        if (!(Math.abs(offsetPosition.x) < Math.floor(mapData.size.width / 2) && Math.abs(offsetPosition.y) < Math.floor(mapData.size.height / 2))) {
            return false;
        }

        var myTeam = gamedata.getPlayerTeam();
        var hexPositionInGame = window.coordinateConverter.fromHexToGame(hex);

        // 10 hex buffer required around enemy deployment zones (reduced by 1/5th hex for edge slack)
        var hexWidth = window.HexagonMath.getHexWidth();
        var hexHeight = window.HexagonMath.getHexRowHeight();

        var bufferX = hexWidth * 9.8;
        var bufferY = hexHeight * 9.8;

        for (var i = 0; i < deploymentSprites.length; i++) {
            var icon = deploymentSprites[i];

            // Only consider enemy areas
            if (icon.team == myTeam) continue;

            var depData = icon.deploymentData;

            var offsetPosition = {
                x: depData.position.x - hexPositionInGame.x,
                y: depData.position.y - hexPositionInGame.y
            };

            // Expanded bounding box with the 10-hex buffer
            var isWithinX = Math.abs(offsetPosition.x) <= Math.floor(depData.size.width / 2) + bufferX;
            var isWithinY = Math.abs(offsetPosition.y) <= Math.floor(depData.size.height / 2) + bufferY;

            if (isWithinX && isWithinY) {
                return false; // Found inside a restricted enemy zone
            }
        }

        return true;
    }

    function getMapData(padding) {

        var mapHeight = 0;
        var mapWidth = 0;

        const match = gamedata.gamespace?.match(/^(-?\d+)x(-?\d+)$/);
        if (match) {
            mapHeight = parseInt(match[2]) * window.Config.HEX_SIZE * 1.5;
            mapWidth = (parseInt(match[1])) * window.Config.HEX_SIZE * 1.73;
        }

        if (mapHeight <= 0) mapHeight = 48 * window.Config.HEX_SIZE * 1.5;
        if (mapWidth <= 0) mapWidth = 72 * window.Config.HEX_SIZE * 1.73;

        if (padding) {
            mapHeight += window.HexagonMath.getHexRowHeight();
            mapWidth += window.HexagonMath.getHexWidth();
        }


        //position.x -= window.coordinateConverter.getHexWidth() / 2;
        return {
            position: { x: -40, y: 0 },
            size: { height: mapHeight, width: mapWidth },
        };
    }

    function getDeploymentData(slot) {

        if (slot.deptype != "box") {
            //TODO: support other deployment types;
            console.log("ONLY BOX DEPLOYMENT TYPE IS SUPPORTED AT THE MOMENT");
        }

        var position = window.coordinateConverter.fromHexToGame(new hexagon.Offset(slot.depx, slot.depy));
        var size = {
            width: window.HexagonMath.getHexWidth() * slot.depwidth,
            height: window.HexagonMath.getHexRowHeight() * slot.depheight
        };
        var available = slot.depavailable;
        var playerid = slot.playerid;
        var depavailable = slot.depavailable;

        //position.x -= window.coordinateConverter.getHexWidth() / 2;
        return {
            position: position,
            size: size,
            avail: available,
            playerid: playerid,
            available: depavailable
        };
    }

    function validateAllDeployment(gamedata, deploymentSprites) {
        for (var i in gamedata.ships) {
            var ship = gamedata.ships[i];

            if (!gamedata.isMyShip(ship)) {
                continue;
            }

            if (shipManager.getTurnPlaced(ship) != gamedata.turn) continue; //We're only validating ships that pick their entry hex this turn!

            //Stage 7: flights queued for hangar deploy-start dock don't need a
            //hex position — they go straight into the carrier's hangar.
            if (ship.pendingDeployDock) continue;
            //LCV Rails: an LCV queued to deploy-dock onto a rail likewise needs no
            //hex position — it starts docked on the carrier.
            if (ship.pendingLcvDeployDock) continue;

            /* REINFORCEMENTS_PLAN.md STAGE 7 - PLACEMENT IS OPTIONAL FOR AN ARRIVAL (plan §2.4), so
               an unplaced one must not hold the commit button hostage. It is the player's right to
               leave part of a wave in hyperspace - the deviation may have put the doorway somewhere
               they would rather not stand - and what they give up by doing so is the berth, which
               DeploymentGamePhase::releaseUnplacedReinforcements takes on commit.
               ⚠️ THE `deploymove` TEST IS LOAD-BEARING, not a shortcut. Without it the call below
               would validate the unit's 'start' position - the off-map deployment-box centre every
               ship is given - against the exit hex, fail, and the commit button would never
               arm for anybody who chose to leave a unit behind. A PLACED arrival still falls
               through and is validated normally. */
            if (shipManager.isArrivingReinforcement(ship) && !ship.deploymove) continue;

            if (!validateDeploymentPosition(ship, null, deploymentSprites)) {
                return false;
            }
        }

        return true;
    }

    function validateDeploymentPosition(ship, hex, deploymentSprites) {
        if (!hex) {
            hex = new hexagon.Offset(shipManager.getShipPosition(ship));
        }
        /* REINFORCEMENTS_PLAN.md STAGE 7 - a unit arriving out of hyperspace has exactly ONE legal
           hex: the jump point exit it is riding (plan §2.4). Its slot's deployment box is
           irrelevant and would say yes to a hex nowhere near the doorway, so this is taken first
           and returns outright. Mirrors DeploymentGamePhase::validateReinforcementArrival, minus
           the facing half - the facing is not a choice on this side (movement.deploy sets it and
           canTurn refuses to change it), so there is nothing here to validate. */
        if (shipManager.isArrivingReinforcement(ship)) {
            var exit = shipManager.movement.getArrivalVortex(ship);
            if (!exit) return false;

            var exitHex = new hexagon.Offset(shipManager.getShipPosition(exit));
            return (exitHex.q == hex.q && exitHex.r == hex.r);
        }

        if (gamedata.isTerrain(ship.shipSizeClass, ship.userid)) {//return true;
            return validateTerrainDeployment(hex);
        } else if (ship.mine) {
            return validateMineDeployment(hex, ship, deploymentSprites);
        } else {
            var icon = getSlotById(ship.slot, deploymentSprites);
            return icon.isValidDeploymentPosition(hex);
        }
        /*
         var slot = deployment.getValidDeploymentArea(ship);
        hexpos = hexgrid.hexCoToPixel(hexpos.x, hexpos.y);
        var deppos = hexgrid.hexCoToPixel(slot.depx, slot.depy);
         if (slot.deptype == "box"){
            var depw = slot.depwidth*hexgrid.hexWidth();
            var deph = slot.depheight*hexgrid.hexHeight();
            if (hexpos.x <= (deppos.x+(depw/2)) && hexpos.x > (deppos.x-(depw/2))){
                if (hexpos.y <= (deppos.y+(deph/2)) && hexpos.y >= (deppos.y-(deph/2))){
                    return true;
                }
            }
        }else if (slot.deptype=="distance"){
            if (mathlib.distance(deppos.x, deppos.y, hexpos.x, hexpos.y) <= slot.depheight*hexgrid.hexWidth()){
                if (mathlib.distance(deppos.x, deppos.y, hexpos.x, hexpos.y) > slot.depwidth*hexgrid.hexWidth()){
                    return true;
                }
            }
        }else{
            if (mathlib.distance(deppos.x, deppos.y, hexpos.x, hexpos.y) <= slot.depwidth*hexgrid.hexWidth()){
                return true;
            }
        }
        return false;
        */
    }

    // Expose mine deployment validation globally for MineDeployment.js
    window.validateMineDeploymentHex = function (hex, deploymentSprites) {
        return validateMineDeployment(hex, null, deploymentSprites);
    };

    // Expose full-deployment validation so MineDeployment.js can gate the commit button correctly
    window.validateAllDeploymentGlobal = function (gamedataRef, deploymentSprites) {
        return validateAllDeployment(gamedataRef, deploymentSprites);
    };

    // Expose single-ship deployment-position validation (uses the active
    // deployment sprite list). Used by SelectFromShips to decide whether the
    // "DEPLOY HERE" button is legal for an LCV clicked onto an occupied hex —
    // the LCV dock shortcut surfaces the popup without pre-validating position,
    // so the button must self-gate. Fail-soft to false on a missing sprite list.
    window.validateDeploymentPositionForShip = function (ship, hex) {
        var sprites = window._deploymentSprites;
        if (!ship || !sprites) return false;
        try { return validateDeploymentPosition(ship, hex, sprites); }
        catch (e) { return false; }
    };

    // Hangar Operations Stage 7 deployment-phase dock helpers (window.DeploymentDock
    // + computeFreeBoxes / trueSizeOfFlightForDock / hangarAcceptsCategoryForDock
    // / flightHasCommittedPosition / flightQueuedToCarrier / carrierHasQueuedDocks)
    // were defined here in an earlier pass, then re-implemented in
    // DeploymentDock.js — which loads AFTER this file and overwrites
    // window.DeploymentDock with the canonical IIFE-encapsulated version.
    // The duplicate definitions and helpers have been removed; the live
    // implementation is in DeploymentDock.js.

    return DeploymentPhaseStrategy;
}();