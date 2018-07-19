class ShipObject {

    constructor(ship, scene) {

        this.ship = ship;
        this.scene = scene;
        this.mesh = new THREE.Object3D()
        this.shipObject = null;


        this.startRotation = {x: 0, y: 0, z: 0}




        this.create()
        this.scene.add(this.mesh)
    }

    create() {}

    setRotation(x, y, z){
        this.shipObject.rotation.set(mathlib.degreeToRadian(x), mathlib.degreeToRadian(y), mathlib.degreeToRadian(y));
    }
}

window.ShipObject = ShipObject;