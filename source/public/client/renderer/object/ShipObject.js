class ShipObject {

    constructor(ship, scene) {

        this.ship = ship;
        this.scene = scene;
        this.mesh = new THREE.Object3D()
        this.shipObject = null;


        this.startRotation = {x: 0, y: 0, z: 0}

        this.create()

        this.line = new window.LineSprite({x:0, y:0, z:0}, {x:0, y:0, z:100}, 10, new THREE.Color(0, 1, 0), 0.7)
        this.mesh.add(this.line.mesh)
        this.scene.add(this.mesh)
    }

    create() {}

    setRotation(x, y, z){
        this.shipObject.rotation.set(mathlib.degreeToRadian(x + this.startRotation.x), mathlib.degreeToRadian(y + this.startRotation.y), mathlib.degreeToRadian(z + this.startRotation.z));
    }
}

window.ShipObject = ShipObject;