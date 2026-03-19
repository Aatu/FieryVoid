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

        combatLog.onTurnStart();
        infowindow.informPhase(5000, null);
        this.selectFirstOwnShipOrActiveShip();

        showEnemyDeploymentAreas(this.deploymentSprites, gamedata);
        showAlliedDeploymentAreas(this.deploymentSprites, gamedata);

        this.setPhaseHeader("DEPLOYMENT");

        //Show commit button Deployment Phase if player has no ships, should never actually happen as server will skip Deployment Phases for these slots.
        if (shipManager.playerHasDeployedAllShips(gamedata.thisplayer)) {
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
            
            var hasTerrain = shipsInHex.some(function(s) { 
                return gamedata.isTerrain(s.shipSizeClass, s.userid) || (s.Huge > 0 && s.Huge <= 3); 
            });

            if (hasTerrain) {
                isBlocked = true;
            } else if (!(this.selectedShip.mine || this.selectedShip.flight)) {
                isBlocked = shipsInHex.some(function(s) { return !(s.mine || s.flight); });
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

    DeploymentPhaseStrategy.prototype.onShipClicked = function (ship, payload) {//30 June 2024 - DK - Added for Ally targeting.
        if (shipManager.shouldBeHidden(ship)) return;  //Stealth equipped and undetected enemy, or not deployed yet - DK May 2025

        if (gamedata.showLoS) {
            this._startHexRuler = payload.hex;
            mathlib.clearLosSprite();
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

    return DeploymentPhaseStrategy;
}();