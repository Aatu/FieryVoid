class Gunship extends ShipObject {


    create () {
        window.Loader.loadObject( 
            "img/3d/gunship/gunship.obj", 
            (object) => { 
                
                console.log("loaded")
                console.log(object)
                console.log(this)
                window.Loader.loadTexturesAndAssign(object.children[0], {}, null,  'img/3d/gunship/normal.png');
                
                window.Loader.loadTexturesAndAssign(object.children[1], {}, null, 'img/3d/turretNormal.png');
                window.Loader.loadTexturesAndAssign(object.children[2], {}, null, 'img/3d/turretNormal.png');
                window.Loader.loadTexturesAndAssign(object.children[3], {}, null, 'img/3d/turretNormal.png');
                window.Loader.loadTexturesAndAssign(object.children[6], {}, null, 'img/3d/turretNormal.png');
                window.Loader.loadTexturesAndAssign(object.children[7], {}, null, 'img/3d/turretNormal.png');
                window.Loader.loadTexturesAndAssign(object.children[4], {}, 'img/3d/diffuseThruster.png', 'img/3d/normalThruster.png');
                window.Loader.loadTexturesAndAssign(object.children[5], {}, 'img/3d/diffuseThruster.png', 'img/3d/normalThruster.png');

               
                object.scale.set(5, 5, 5)
                this.startRotation = {x:90, y:90, z:0}
                //object.rotation.set(mathlib.degreeToRadian(90), mathlib.degreeToRadian(90), 0);
                //object.position.set(0, 60, 0)
                
                this.shipObject = object
                this.setRotation(0, 0, 0)
                this.mesh.add(this.shipObject)
                object.position.set(0, 0, 100)
            }
        )
    }

}

if (!window.shipObjects) {
    window.shipObjects = {}
}

window.shipObjects.Gunship = Gunship;