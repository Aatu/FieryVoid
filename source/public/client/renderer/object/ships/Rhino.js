import ShipObject from './ShipObject'

class Rhino extends ShipObject {

    constructor(ship, scene) {
        super(ship, scene);
        this.sideSpriteSize = 30;
        this.create();
    }

    create () {
        super.create();

        window.Loader.loadObject( 
            "img/3d/rhino/rhino.obj", 
            (object) => { 
                console.log("Rhino")
                console.log(object)
            
                
                window.Loader.loadTexturesAndAssign(object.children[0], {normalScale: new THREE.Vector2(1, 1), shininess: 10, color: new THREE.Color(1, 1, 1)}, 'img/3d/rhino/texture.png', 'img/3d/rhino/sculptNormal.png');
                window.Loader.loadTexturesAndAssign(object.children[1], {}, 'img/3d/diffuseDoc.png', 'img/3d/normalDoc.png');
                window.Loader.loadTexturesAndAssign(object.children[2], {}, 'img/3d/diffuseThruster.png', 'img/3d/normalThruster.png');

               
                object.scale.set(2, 2, 2)
                this.startRotation = {x:90, y:90, z:0}
                
                this.shipObject = object
                this.setRotation(this.rotation.x, this.rotation.y, this.rotation.z)
                this.mesh.add(this.shipObject)
                object.position.set(0, 0, this.position.z)
            }
        )
    }

}

export default Rhino