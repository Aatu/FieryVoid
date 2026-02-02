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
            if (shipManager.getShipsInSameHex(this.selectedShip, hex).length == 0) {
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
    }

    function getSlotById(slotId, deploymentSprites) {
        return deploymentSprites.filter(function (icon) {
            return icon.slotId == slotId;
        }).pop();
    }

    function createSlotSprites(gamedata, scene) {
        return Object.keys(gamedata.slots).map(function (key) {
            var slot = gamedata.slots[key];

            var deploymentData = getDeploymentData(slot);

            var ownSprite = new DeploymentIcon(deploymentData.position, deploymentData.size, 'own', scene, deploymentData.avail);
            var allySprite = new DeploymentIcon(deploymentData.position, deploymentData.size, 'ally', scene, deploymentData.avail);
            var enemySprite = new DeploymentIcon(deploymentData.position, deploymentData.size, 'enemy', scene, deploymentData.avail);

            var mapData = getMapData();

            var terrainSprite = new DeploymentIcon(mapData.position, mapData.size, 'terrain', scene, 1);

            return {
                slotId: key,
                team: slot.team,
                isValidDeploymentPosition: getValidDeploymentCallback(slot, deploymentData),
                ownSprite: ownSprite,
                allySprite: allySprite,
                enemySprite: enemySprite,
                terrainSprite: terrainSprite,
                playerid: deploymentData.playerid,
                available: deploymentData.available
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
        var mapData = getMapData();
        var hexPositionInGame = window.coordinateConverter.fromHexToGame(hex);

        var offsetPosition = {
            x: mapData.position.x - hexPositionInGame.x,
            y: mapData.position.y - hexPositionInGame.y
        };

        return Math.abs(offsetPosition.x) < Math.floor(mapData.size.width / 2) && Math.abs(offsetPosition.y) < Math.floor(mapData.size.height / 2);
    }

    function getMapData() {

        var mapHeight = 0;
        var mapWidth = 0;

        const match = gamedata.gamespace?.match(/^(-?\d+)x(-?\d+)$/);
        if (match) {
            mapHeight = parseInt(match[2]) * window.Config.HEX_SIZE * 1.5;
            mapWidth = (parseInt(match[1])) * window.Config.HEX_SIZE * 1.73;
        }

        if (mapHeight <= 0) mapHeight = 48 * window.Config.HEX_SIZE * 1.5;
        if (mapWidth <= 0) mapWidth = 72 * window.Config.HEX_SIZE * 1.73;


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