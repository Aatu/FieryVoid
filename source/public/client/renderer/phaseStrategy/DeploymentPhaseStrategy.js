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
        this.selectFirstOwnShipOrActiveShip();

        showEnemyDeploymentAreas(this.deploymentSprites, gamedata);
        showAlliedDeploymentAreas(this.deploymentSprites, gamedata);

        this.setPhaseHeader("DEPLOYMENT");

        //Show commit button Deployment Phase if player has no ships to deploy this turn, should never actually happen as server will skip Deployment Phases for these slots.
        if (!shipManager.hasShipsToDeployThisTurn(gamedata.thisplayer)) {
            if (this.selectedShip) this.deselectShip(this.selectedShip);
            this.setPhaseHeader("PRE-TURN ORDERS");
            this.replayUI = new ReplayUI().activate();
            gamedata.showCommitButton();
            /*//Can auto-click it if we want.

            // Can simulate clicking confirm if needed.
            setTimeout(() => {
                $(".confirmok").trigger("click");
            }, 50); // Adjust delay if needed */

        }

        return this;
    };

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

    DeploymentPhaseStrategy.prototype.selectFirstOwnShipOrActiveShip = function () {
        var ship = gamedata.getFirstFriendlyShipDeployment();

        //TODO: what about active ship?
        if (ship) {
            this.setSelectedShip(ship);
        }
    };

    DeploymentPhaseStrategy.prototype.onHexClicked = function (payload) {
        PhaseStrategy.prototype.onHexClicked.call(this, payload);
        var hex = payload.hex;

        if (!this.selectedShip || (shipManager.getTurnDeployed(this.selectedShip) < gamedata.turn)) {
            //No selected ship or ship has ALREADY deployed so don't allow re-deployment!
            return;
        }

        if (validateDeploymentPosition(this.selectedShip, hex, this.deploymentSprites)) {
            var shipsInHex = shipManager.getShipsInSameHex(this.selectedShip, hex);
            var isBlocked = false;

            var hasTerrain = shipsInHex.some(function (s) {
                return gamedata.isTerrain(s.shipSizeClass, s.userid) || (s.Huge > 0 && s.Huge <= 3);
            });

            if (hasTerrain) {
                isBlocked = true;
            } else if (!(this.selectedShip.mine || this.selectedShip.flight)) {
                isBlocked = shipsInHex.some(function (s) { return !(s.mine || s.flight); });
            }

            if (!isBlocked) {
                shipManager.movement.deploy(this.selectedShip, hex);
                this.onShipMovementChanged({ ship: this.selectedShip });
                this.drawMovementUI(this.selectedShip);

                if (validateAllDeployment(this.gamedata, this.deploymentSprites)) {
                    gamedata.showCommitButton();
                }
            }
        }
    };

    DeploymentPhaseStrategy.prototype.onShipsClicked = function (ships, payload) {
        // Shift+Click bypass: place directly without showing the SelectFromShips picker when:
        //   - selected unit is a fighter/mine (drops onto stacked ships/mines/fighters), OR
        //   - selected unit is a regular ship and every stacked unit in the hex is a mine/fighter.
        if (payload && payload.shiftKey && this.selectedShip
            && shipManager.getTurnDeployed(this.selectedShip) >= gamedata.turn
            && validateDeploymentPosition(this.selectedShip, payload.hex, this.deploymentSprites)) {
            var selIsFighterMine = this.selectedShip.mine || this.selectedShip.flight;
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
            var depTurn = shipManager.getTurnDeployed(ship);
            if (depTurn === gamedata.turn || (depTurn < gamedata.turn && ship.canPreOrder)) {
                if (this.selectedShip && this.selectedShip.id !== ship.id) {
                    this.deselectShip(this.selectedShip);
                }
                this.selectShip(ship, payload);
                return;
            }
        }

        // If we have a selected ship actively ready to deploy, and we click a valid DIFFERENT ship that is already placed on the map
        if (this.selectedShip && this.selectedShip.id !== ship.id) {
            var isPlacedOnMap = false;
            if (ship.movement && ship.movement.length > 0) {
                isPlacedOnMap = ship.movement[0].commit === true;
            }

            var isTerrain = gamedata.isTerrain(ship.shipSizeClass, ship.userid) || (ship.Huge > 0 && ship.Huge <= 3);
            if (!isTerrain && isPlacedOnMap && (this.selectedShip.mine || this.selectedShip.flight || ship.mine || ship.flight)) {
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
                        if (payload.shiftKey && (this.selectedShip.mine || this.selectedShip.flight || ship.mine || ship.flight)) {
                            this.onHexClicked(payload);
                            return;
                        }
                        this.showSelectFromShips([ship], payload);
                        return;
                    } else {
                        // The selected ship is indeed legally placed, but it's ALREADY in the hex we clicked on.
                        // We shouldn't show a deploy menu or fall through to auto-deploy. We simply swap the selection.
                        this.selectShip(ship, payload);
                        return;
                    }
                }
            }
        }

        if (this.gamedata.isMyShip(ship) && ((shipManager.getTurnDeployed(ship) == gamedata.turn)
            || (shipManager.getTurnDeployed(ship) < gamedata.turn) && ship.canPreOrder)) { //Own ship and deploys this turn, just select it. Means that late-deployers can't deploy on ships with canPreOrder (unless they click very edge of hex), but that's rare.
            this.selectShip(ship, payload);
        } else { //Neither of the above is true, allow to deploy.  Even on hexes occupied by ships that deployed earlier in game.
            this.onHexClicked(payload);
        }
    };

    DeploymentPhaseStrategy.prototype.setSelectedShip = function (ship) {
        PhaseStrategy.prototype.setSelectedShip.call(this, ship);
        var depTurn = shipManager.getTurnDeployed(ship);
        if (depTurn < gamedata.turn) return;

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
            menu.addLeadingButton("dockFlight",
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

    DeploymentPhaseStrategy.prototype.deselectShip = function (ship) {
        PhaseStrategy.prototype.deselectShip.call(this, ship);
        hideDeploymentArea(ship, this.deploymentSprites, this.gamedata);
        this.hideMovementUI();
    };

    DeploymentPhaseStrategy.prototype.createReplayUI = function (gamedata) { };

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

            if (shipManager.getTurnDeployed(ship) != gamedata.turn) continue; //We're only validating ships that deploy this turn!

            //Stage 7: flights queued for hangar deploy-start dock don't need a
            //hex position — they go straight into the carrier's hangar.
            if (ship.pendingDeployDock) continue;

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

    // === Hangar Operations Stage 7: deployment-phase dock helpers ============
    //
    // Exposed at window scope so DeploymentPhaseStrategy (selectShip menu hook)
    // and confirm.js::hangarDeployDock can both reach them without depending
    // on each other's module structure.
    //
    // Eligibility rules (deployment-phase docking, looser than Stage 5 land):
    //   - Same player slot (carrier and flight owned by the same player)
    //   - Flight AND carrier are both deploying THIS turn — a reinforcement
    //     flight cannot dock into a previously-deployed carrier
    //   - Flight hasn't been placed on the map yet (no commit movement)
    //   - Flight isn't already destroyed/removed
    //   - Carrier has at least one hangar that accepts the flight's category
    //     and has free boxes for the FULL flight (no partial deploy-dock)
    window.DeploymentDock = {
        // Returns true iff $ship is a friendly carrier with at least one hangar
        // AND there's at least one same-slot pending flight that could fit.
        // Used by DeploymentPhaseStrategy.selectShip to decide whether to add
        // the "Dock pending flight here" button to the tooltip menu.
        shipHasOpenableDockDialog: function (ship) {
            if (!ship || !ship.systems) return false;
            if (!gamedata.isMyShip(ship)) return false;
            if (ship.flight || ship.mine) return false;
            //Carrier must be deploying THIS turn — fighters arriving on
            //turn N can only dock into ships also arriving on turn N.
            if (shipManager.getTurnDeployed(ship) !== gamedata.turn) return false;

            // Carrier must have at least one undestroyed hangar with free boxes
            // OR an existing queued deploy-start order (so the dialog can be
            // re-opened to amend/cancel even if every box is now reserved).
            var hasUsableHangar = false;
            for (var i = 0; i < ship.systems.length; i++) {
                var sys = ship.systems[i];
                if (!sys || sys.name !== 'hangar') continue;
                if (shipManager.systems.isDestroyed(ship, sys)) continue;
                if (Array.isArray(sys.pendingDeployStartOrders) && sys.pendingDeployStartOrders.length > 0) {
                    hasUsableHangar = true;
                    break;
                }
                var free = computeFreeBoxes(sys);
                if (free > 0) { hasUsableHangar = true; break; }
            }
            if (!hasUsableHangar) return false;

            // At least one pending flight must exist (or already be queued here).
            var pending = window.DeploymentDock.findPendingFlightsForCarrier(ship);
            return pending.length > 0 || carrierHasQueuedDocks(ship);
        },

        // List of fighter flights eligible to be docked into $carrier during
        // this Deployment Phase. Returns flight ship objects, ordered by id.
        findPendingFlightsForCarrier: function (carrier) {
            var out = [];
            if (!carrier) return out;
            var carrierSlot = parseInt(carrier.slot, 10);
            var carrierOwner = parseInt(carrier.userid, 10);
            for (var key in gamedata.ships) {
                var f = gamedata.ships[key];
                if (!f || !f.flight) continue;
                if (parseInt(f.userid, 10) !== carrierOwner) continue;
                if (parseInt(f.slot, 10) !== carrierSlot) continue;
                if (f.removed) continue;
                if (shipManager.isDestroyed(f)) continue;
                if (shipManager.getTurnDeployed(f) !== gamedata.turn) continue;
                // Skip flights that are already placed on the map this turn
                // (their position is committed via shipManager.movement.deploy).
                if (flightHasCommittedPosition(f)) continue;
                // Skip flights already queued to a DIFFERENT carrier — the
                // re-edit case (queued to THIS carrier) is handled separately
                // by the dialog so the flight still appears in the list.
                if (f.pendingDeployDock && parseInt(f.pendingDeployDock.carrierId, 10) !== parseInt(carrier.id, 10)) continue;
                // Must fit in some hangar on this carrier (full flight only)
                if (window.DeploymentDock.eligibleHangarsForFlight(carrier, f).length === 0
                    && !flightQueuedToCarrier(f, carrier)) continue;
                out.push(f);
            }
            out.sort(function (a, b) { return parseInt(a.id, 10) - parseInt(b.id, 10); });
            return out;
        },

        // List of hangars on $carrier that can hold $flight in full.
        // Returns [{hangar, capacity}, ...]. Used by the dialog to populate
        // the per-flight hangar dropdown.
        eligibleHangarsForFlight: function (carrier, flight) {
            var out = [];
            if (!carrier || !flight || !carrier.systems) return out;
            var size = parseInt(flight.flightSize || 1, 10);
            if (size <= 0) return out;
            var category = trueSizeOfFlightForDock(flight);
            var flightId = parseInt(flight.id, 10);

            // Exact-category first, then size-hierarchy match. Mirrors the
            // ordering in HangarOps::eligibleHangarsForLanding so dialog
            // suggestions match server end-of-phase resolution.
            var exact = [];
            var fallback = [];
            carrier.systems.forEach(function (sys) {
                if (!sys || sys.name !== 'hangar') return;
                if (shipManager.systems.isDestroyed(carrier, sys)) return;
                if (!hangarAcceptsCategoryForDock(sys.hangarType, category)) return;
                var free = computeFreeBoxes(sys);
                // Treat THIS flight's own queued reservation as reclaimable
                // capacity (re-edit case): the dialog will replace it.
                if (Array.isArray(sys.pendingDeployStartOrders)) {
                    sys.pendingDeployStartOrders.forEach(function (o) {
                        if (parseInt(o.flightId, 10) === flightId) free += size;
                    });
                }
                if (free < size) return;
                var entry = { hangar: sys, capacity: free };
                if (sys.hangarType === category) exact.push(entry);
                else fallback.push(entry);
            });
            return exact.concat(fallback);
        },

        // Called from the dialog after the player commits a dock selection.
        // Toggles the commit button so the player can submit immediately if
        // every remaining flight is placed (or hides it if a dock was undone
        // and a position is now missing again).
        refreshDeploymentUI: function () {
            if (!window.gameTimeline) return;
            var strategy = window.gameTimeline.activePhaseStrategy;
            if (!strategy || !strategy.deploymentSprites) return;
            if (validateAllDeployment(strategy.gamedata || gamedata, strategy.deploymentSprites)) {
                gamedata.showCommitButton();
            } else {
                gamedata.hideCommitButton();
            }
        }
    };

    // True if the flight has a committed deploy MovementOrder placing it on the
    // map this turn (i.e. the player has hex-clicked it during this phase).
    function flightHasCommittedPosition(flight) {
        if (!flight.movement || flight.movement.length === 0) return false;
        // The deploy MovementOrder is added by shipManager.movement.deploy at
        // hex-click. It has type "deploy" and commit:true.
        for (var i = 0; i < flight.movement.length; i++) {
            var m = flight.movement[i];
            if (m && m.type === 'deploy' && m.turn === gamedata.turn && m.commit) return true;
        }
        return false;
    }

    function flightQueuedToCarrier(flight, carrier) {
        if (!flight.pendingDeployDock) return false;
        return parseInt(flight.pendingDeployDock.carrierId, 10) === parseInt(carrier.id, 10);
    }

    function carrierHasQueuedDocks(carrier) {
        if (!carrier.systems) return false;
        for (var i = 0; i < carrier.systems.length; i++) {
            var sys = carrier.systems[i];
            if (!sys || sys.name !== 'hangar') continue;
            if (Array.isArray(sys.pendingDeployStartOrders) && sys.pendingDeployStartOrders.length > 0) return true;
        }
        return false;
    }

    // Free boxes accounting for current hangarUsage AND any queued deploy-start
    // orders that are not from the flight we're currently sizing (handled
    // explicitly by the caller). Damage above armour reduces effective capacity.
    function computeFreeBoxes(hangar) {
        var netDamage = 0;
        if (Array.isArray(hangar.damage)) {
            hangar.damage.forEach(function (d) {
                netDamage += Math.max(0, parseInt(d.damage || 0, 10) - parseInt(d.armour || 0, 10));
            });
        }
        var effective = Math.max(0, parseInt(hangar.maxhealth, 10) - netDamage);
        var used = 0;
        if (Array.isArray(hangar.hangarUsage)) {
            hangar.hangarUsage.forEach(function (e) { used += parseInt(e.flightSize || 1, 10); });
        }
        if (Array.isArray(hangar.pendingDeployStartOrders)) {
            hangar.pendingDeployStartOrders.forEach(function (o) {
                var f = gamedata.getShip ? gamedata.getShip(parseInt(o.flightId, 10)) : null;
                if (!f) return;
                used += parseInt(f.flightSize || 1, 10);
            });
        }
        return Math.max(0, effective - used);
    }

    // Mirrors HangarOps::trueSizeOf (PHP) and the same helper in
    // shipTooltipFireMenu.js. Returns the canonical hangar category for $flight.
    function trueSizeOfFlightForDock(flight) {
        var req = String(flight.hangarRequired || '').trim();
        var lower = req.toLowerCase();
        if (lower === '' || lower === 'fighters' || lower === 'normal') {
            var jink = parseInt(flight.jinkinglimit || 0, 10);
            if (jink >= 99) return 'ultralight';
            if (jink >= 10) return 'light';
            if (jink >= 8) return 'medium';
            if (jink >= 6) return 'heavy';
            return 'medium';
        }
        return req;
    }

    // Mirrors HangarOps::hangarAcceptsCategory (PHP) — combat-fighter size
    // hierarchy plus shuttle/BP compatibility. Keep in sync with the server
    // helper so the dialog suggestions match end-of-phase server resolution.
    function hangarAcceptsCategoryForDock(hangarType, category) {
        var hType = String(hangarType || '').toLowerCase().trim();
        var cat = String(category || '').toLowerCase().trim();
        if (hType === '' || cat === '') return false;
        if (hType === 'fighters' || hType === 'normal') return true;
        if (hType === cat) return true;
        var rank = { ultralight: 1, light: 2, medium: 3, heavy: 4 };
        if (rank[hType] && rank[cat]) return rank[cat] <= rank[hType];
        if ((cat === 'shuttles' || cat === 'minesweeping shuttles') && rank[hType]) return true;
        if (hType === 'assault shuttles' && cat === 'breaching pods') return true;
        return false;
    }

    return DeploymentPhaseStrategy;
}();