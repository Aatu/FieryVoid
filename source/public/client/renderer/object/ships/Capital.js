import ShipObject from './ShipObject'

class Capital extends ShipObject {

    constructor(ship, scene) {
        super(ship, scene);
        this.defaultHeight = 35;
        this.sideSpriteSize = 90;
        this.create();
    }

    create () {
        super.create();

        window.Loader.loadObject( 
            "img/3d/capital/capital.obj", 
            (object) => { 
                    
                window.Loader.loadTexturesAndAssign(object.children[0], {normalScale: new THREE.Vector2(0.5, 0.5), shininess: 10, color: new THREE.Color(1, 1, 1)}, 'img/3d/capital/diffuse.png',  'img/3d/capital/normalEdit.png');
                
               
                object.scale.set(3, 3, 3);
                this.startRotation = {x:90, y:90, z:0}
                this.shipObject = object
                this.setRotation(this.rotation.x, this.rotation.y, this.rotation.z)
                this.mesh.add(this.shipObject)
                object.position.set(0, 0, this.position.z)
            }
        )
    }

}

export default Capital