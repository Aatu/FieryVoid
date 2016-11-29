window.ShipIcon = (function (){

    function ShipIcon(ship, scene){

        this.shipId = ship.id;
        this.imagePath = ship.imagePath;
        this.movements = null;
        this.mesh = null;
        this.size = ship.canvasSize;
        this.addedToScene = false;
        this.mine = gamedata.isMyShip(ship);


        this.shipSprite = null;
        this.shipEWSprite = null;
        this.ShipSelectedSprite = null;
        this.ShipSideSprite = null;

        this.consumeShipdata(ship);
        create.call(this, ship.imagePath, scene);
    }

    ShipIcon.prototype.consumeShipdata = function (ship){
        this.movements = consumeMovement.call(this, ship.movement);
    };

    ShipIcon.prototype.render = function (scene, coordinateConverter) {
        if (! this.addedToScene) {
            scene.add(this.mesh);
        }

        position.call(this, coordinateConverter);
    };

    ShipIcon.prototype.setPosition = function(position) {
        this.mesh.position.x = position.x;
        this.mesh.position.y = position.y;
    };

    ShipIcon.prototype.getPosition = function() {
        return {x: this.mesh.position.x, y: this.mesh.position.y};
    };

    ShipIcon.prototype.setFacing = function(facing) {
        this.mesh.rotation.z = mathlib.degreeToRadian(facing);
    };

    ShipIcon.prototype.getMovements = function(turn){
        return this.movements.filter(function(movement){
            return (turn === undefined || movement.turn === turn);
        }, this);
    };

    ShipIcon.prototype.setScale = function(width, height){
        this.shipEWSprite.setScale(width, height);
        this.shipSprite.setScale(width, height);
        this.ShipSelectedSprite.setScale(width, height);
        this.ShipSideSprite.setScale(width, height);
    };

    ShipIcon.prototype.displayEW = function(DEW, CCEW){
        this.shipEWSprite.update(DEW, CCEW);
        this.shipEWSprite.show();
    };

    ShipIcon.prototype.hideEW = function(){
        if (this.shipEWSprite) {
            this.shipEWSprite.hide();
        }
    };

    ShipIcon.prototype.showSideSprite = function(value) {
        if (value) {
            this.ShipSideSprite.show();
        } else {
            this.ShipSideSprite.hide();
        }
    };

    ShipIcon.prototype.setSelected = function(value) {
        if (value) {
            this.ShipSelectedSprite.show();
        } else {
            this.ShipSelectedSprite.hide();
        }
    };



    function create(imagePath, scene) {
        this.mesh = new THREE.Object3D();
        this.mesh.position = new THREE.Vector3(500, 0, 0);
        this.mesh.renderDepth = 10;

        this.shipSprite = new window.webglSprite(imagePath, {width: this.size/2, height: this.size/2}, 0);
        this.mesh.add(this.shipSprite.mesh);

        this.shipEWSprite = new window.ShipEWSprite({width: this.size/2, height: this.size/2}, -1);
        this.mesh.add(this.shipEWSprite.mesh);

        this.ShipSelectedSprite = new window.ShipSelectedSprite({width: this.size/2, height: this.size/2}, -2, this.mine ? 'ally' : 'enemy', true).hide();
        this.mesh.add(this.ShipSelectedSprite.mesh);

        this.ShipSideSprite = new window.ShipSelectedSprite({width: this.size/2, height: this.size/2}, -2, this.mine ? 'ally' : 'enemy', false).hide();
        this.mesh.add(this.ShipSideSprite.mesh);

        scene.add(this.mesh);
    }

    function consumeMovement(movements){
        return movements.map(function(movement) {
            return {
                id: movement.id,
                type: movement.type,
                turn: movement.turn,
                facing: movement.facing,
                heading: movement.heading,
                position: {x: movement.x, y: movement.y},
                offset: {x: movement.xOffset, y: movement.yOffset}
            };
        });
    }

    return ShipIcon;
})();