import ShipObject from './ShipObject'

class Rhino extends ShipObject {

    constructor(ship, scene) {
        super(ship, scene);
        this.sideSpriteSize = 30;
        this.create();
    }

    create () {
        window.Loader.loadObject( 
            "img/3d/rhino/rhino.obj", 
            (object) => { 
                
                window.Loader.loadTexturesAndAssign(object.children[0], {}, 'img/3d/rhino/texture.png', 'img/3d/rhino/sculptNormal.png');
                
                window.Loader.loadTexturesAndAssign(object.children[1], {}, 'img/3d/diffuseDoc.png', 'img/3d/normalDoc.png');
                window.Loader.loadTexturesAndAssign(object.children[2], {}, 'img/3d/diffuseThruster.png', 'img/3d/normalThruster.png');

               
                object.scale.set(2, 2, 2)
                this.startRotation = {x:90, y:90, z:0}
                
                this.shipObject = object
                this.setRotation(0, 0, 0)
                this.mesh.add(this.shipObject)
                object.position.set(0, 0, this.defaultHeight)
            }
        )

        super.create();
    }

}

export default Rhino