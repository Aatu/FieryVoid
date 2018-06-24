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

        this.setPhaseHeader("DEPLOYMENT");
        return this;
    };

    DeploymentPhaseStrategy.prototype.deactivate = function () {
        PhaseStrategy.prototype.deactivate.call(this);
        this.deploymentSprites.forEach(function (icon) {
            icon.ownSprite.hide();
            icon.enemySprite.hide();
            icon.allySprite.hide();
        });
    };

    DeploymentPhaseStrategy.prototype.onHexClicked = function (payload) {
        var hex = payload.hex;

        if (!this.selectedShip) {
            return;
        }

        if (validateDeploymentPosition(this.selectedShip, hex, this.deploymentSprites)) {
            if (shipManager.getShipsInSameHex(this.selectedShip, hex).length == 0) {
                shipManager.movement.deploy(this.selectedShip, hex);
                this.onShipMovementChanged({ship: this.selectedShip});
                this.drawMovementUI(this.selectedShip);

                if (validateAllDeployment(this.gamedata, this.deploymentSprites)) {
                    gamedata.showCommitButton();
                }
            }
        }
    };

    DeploymentPhaseStrategy.prototype.setSelectedShip = function (ship) {
        PhaseStrategy.prototype.setSelectedShip.call(this, ship);
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

    DeploymentPhaseStrategy.prototype.createReplayUI = function (gamedata) {};

    function showEnemyDeploymentAreas(deploymentSprites, gamedata) {
        var team = gamedata.getPlayerTeam();
        deploymentSprites.forEach(function (icon) {
            if (icon.team != team) {
                icon.enemySprite.show();
            }
        });
    }

    function showDeploymentArea(ship, deploymentSprites, gamedata) {
        var icon = getSlotById(ship.slot, deploymentSprites);
        if (gamedata.isMyShip(ship)) {
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

            var ownSprite = new DeploymentIcon(deploymentData.position, deploymentData.size, 'own', scene);
            var allySprite = new DeploymentIcon(deploymentData.position, deploymentData.size, 'ally', scene);
            var enemySprite = new DeploymentIcon(deploymentData.position, deploymentData.size, 'enemy', scene);

            return {
                slotId: key,
                team: slot.team,
                isValidDeploymentPosition: getValidDeploymentCallback(slot, deploymentData),
                ownSprite: ownSprite,
                allySprite: allySprite,
                enemySprite: enemySprite
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

        //position.x -= window.coordinateConverter.getHexWidth() / 2;
        return {
            position: position,
            size: size
        };
    }

    function validateAllDeployment(gamedata, deploymentSprites) {
        for (var i in gamedata.ships) {
            var ship = gamedata.ships[i];

            if (!gamedata.isMyShip(ship)) {
                continue;
            }

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

        var icon = getSlotById(ship.slot, deploymentSprites);
        return icon.isValidDeploymentPosition(hex);
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