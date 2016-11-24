window.webglShipIcon = (function (){

    function webglShipIcon(ship, scene){

        this.shipId = ship.id;
        this.imagePath = ship.imagePath;
        this.movements = consumeMovement.call(this, ship.movement);
        this.mesh = null;
        this.shipSprite = null;

        this.addedToScene = false;

        create.call(this, ship.imagePath, ship.canvasSize, scene);
    }

    webglShipIcon.prototype.render = function (scene, coordinateConverter) {
        if (! this.addedToScene) {
            scene.add(this.mesh);
        }

        position.call(this, coordinateConverter);
    };

    webglShipIcon.prototype.setPosition = function(position) {
        this.mesh.position.x = position.x;
        this.mesh.position.y = position.y;
    };

    webglShipIcon.prototype.setFacing = function(facing) {
        this.mesh.rotation.z = mathlib.degreeToRadian(facing);
    };

    webglShipIcon.prototype.getMovements = function(turn){
        return this.movements.filter(function(movement){
            return (turn === undefined || movement.turn === turn);
        }, this);
    };

    function create(imagePath, size, scene) {
        this.mesh = new THREE.Object3D();
        this.mesh.position = new THREE.Vector3(500, 0, 0);
        this.mesh.renderDepth = 10;

        this.shipSprite = new window.webglSprite(imagePath, {width: size/2, height: size/2}, 0);
        this.mesh.add(this.shipSprite.mesh);
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

    return webglShipIcon;
})();