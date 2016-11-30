window.DeploymentPhaseStrategy = (function(){

    function DeploymentPhaseStrategy(coordinateConverter){
        this.animationStrategy = new window.IdleAnimationStrategy();
        PhaseStrategy.call(this, coordinateConverter);

        this.deploymentSprites = [];
    }

    DeploymentPhaseStrategy.prototype = Object.create(window.PhaseStrategy.prototype);

    DeploymentPhaseStrategy.prototype.activate = function (shipIcons, gamedata, webglScene) {
        PhaseStrategy.prototype.activate.call(this, shipIcons, gamedata);
        this.animationStrategy.activate(shipIcons, gamedata.turn, webglScene.scene);
        this.gamedata = gamedata;

        console.log(gamedata);
        this.deploymentSprites = createSlotSprites(gamedata, webglScene.scene);

        var ship = gamedata.getFirstFriendlyShip();

        if (ship) {
            this.selectShip(ship);
        }

        showEnemyDeploymentAreas(this.deploymentSprites, gamedata);

        return this;
    };

    DeploymentPhaseStrategy.prototype.onHexClicked = function(payload) {
        var hex = payload.hex;

        if (!this.selectedShip) {
            return;
        }

        if (validateDeploymentPosition(this.selectedShip, hex, this.deploymentSprites)){
            if (shipManager.getShipsInSameHex(this.selectedShip, hex).length == 0){
                shipManager.movement.deploy(this.selectedShip, hex);
                this.consumeGamedata();

                if (validateAllDeployment(this.gamedata, this.deploymentSprites)) {
                    gamedata.showCommitButton();
                }

            }
        }

    };

    DeploymentPhaseStrategy.prototype.selectShip = function(ship) {
        PhaseStrategy.prototype.selectShip.call(this, ship);
        showDeploymentArea(ship, this.deploymentSprites, this.gamedata);
    };

    DeploymentPhaseStrategy.prototype.deselectShip = function(ship) {
        PhaseStrategy.prototype.deselectShip.call(this, ship);
        hideDeploymentArea(ship, this.deploymentSprites, this.gamedata);
    };

    DeploymentPhaseStrategy.prototype.targetShip = function(ship) {};

    DeploymentPhaseStrategy.prototype.untargetShip = function(ship) {};

    function showEnemyDeploymentAreas(deploymentSprites, gamedata){
        var team = gamedata.getPlayerTeam();
        deploymentSprites.forEach(function(icon) {
            if (icon.team != team) {
                icon.enemySprite.show();
            }
        });
    }

    function showDeploymentArea(ship, deploymentSprites, gamedata){
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
    }

    function getSlotById(slotId, deploymentSprites) {
        return deploymentSprites.filter(function(icon) {
            return icon.slotId == slotId;
        }).pop();
    }

    function createSlotSprites(gamedata, scene){
        return Object.keys(gamedata.slots).map(function(key) {
            var slot = gamedata.slots[key];

            var deploymentData = getDeploymentData(slot);

            return {
                slotId: key,
                team: slot.team,
                isValidDeploymentPosition: getValidDeploymentCallback(slot, deploymentData),
                ownSprite: new DeploymentIcon(deploymentData.position, deploymentData.size, 'own', scene),
                allySprite: new DeploymentIcon(deploymentData.position, deploymentData.size, 'ally', scene),
                enemySprite: new DeploymentIcon(deploymentData.position, deploymentData.size, 'enemy', scene)
            };

        });
    }

    function getValidDeploymentCallback(slot, deploymentData) {
        return function(hex) {
            if (slot.deptype != "box") {
                //TODO: support other deployment types than box;
                console.log("ONLY BOX DEPLOYMENT TYPE IS SUPPORTED AT THE MOMENT");
            }

            var hexPositionInGame = window.coordinateConverter.fromHexToGame(hex);
            var offsetPosition = {
                x: deploymentData.position.x - hexPositionInGame.x,
                y: deploymentData.position.y - hexPositionInGame.y
            };

            var result = Math.abs(offsetPosition.x) < Math.floor(deploymentData.size.width/2) && Math.abs(offsetPosition.y) < Math.floor(deploymentData.size.height/2);

            return result;
        }
    }

    function getDeploymentData(slot) {

        if (slot.deptype != "box") {
            //TODO: support other deployment types;
            console.log("ONLY BOX DEPLOYMENT TYPE IS SUPPORTED AT THE MOMENT");
        }

        var position = window.coordinateConverter.fromHexToGame(new hexagon.FVHex(slot.depx,slot.depy));
        var size = {
            width: window.coordinateConverter.getHexWidth() * slot.depwidth,
            height: window.coordinateConverter.getHexRowHeight() * slot.depheight
        };

        //position.x -= window.coordinateConverter.getHexWidth() / 2;
        return {
            position: position,
            size: size
        }
    }

    function validateAllDeployment(gamedata, deploymentSprites) {
        for (var i in gamedata.ships) {
            var ship = gamedata.ships[i];

            if (! gamedata.isMyShip(ship)) {
                return true;
            }

            if (! validateDeploymentPosition(ship, null, deploymentSprites)){
                return false;
            }
        }

        return true;
    }

    function validateDeploymentPosition(ship, hex, deploymentSprites){
        if (!hex)
            hex = new hexagon.FVHex(shipManager.getShipPosition(ship));

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
})();