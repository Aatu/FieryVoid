class ShipObject {

    constructor(ship, scene) {

        this.ship = ship;
        this.scene = scene;
        this.mesh = new THREE.Object3D()
        this.shipObject = null;

        this.defaultHeight = 50;
        this.mine = gamedata.isMyOrTeamOneShip(ship);
        this.sideSpriteSize = 100;

        this.startRotation = {x: 0, y: 0, z: 0}    
    }

    createMesh() {
        
        const opacity = 0.5;
        this.line = new window.LineSprite({x:0, y:0, z:1}, {x:0, y:0, z:this.defaultHeight}, 1, new THREE.Color(0, 1, 0), opacity)
        this.mesh.add(this.line.mesh)


        this.shipSideSprite = new window.ShipSelectedSprite({ width:  this.sideSpriteSize, height:  this.sideSpriteSize}, 0.01, opacity);
        this.shipSideSprite.setOverlayColor(new THREE.Color(0, 1, 0))
        this.shipSideSprite.setOverlayColorAlpha(1)
        this.mesh.add(this.shipSideSprite.mesh);


        this.scene.add(this.mesh)
    }

    create() {
        this.createMesh()
    }

    setPosition(x, y, z = 0){
        this.mesh.position.set(x, y, z)
    }

    setRotation(x, y, z){
        this.shipObject.rotation.set(mathlib.degreeToRadian(x + this.startRotation.x), mathlib.degreeToRadian(y + this.startRotation.y), mathlib.degreeToRadian(z + this.startRotation.z));
    }
}

export default ShipObject