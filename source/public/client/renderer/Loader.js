class Loader {

    constructor() {

        this.loadedObjects = {};
        this.loadedTextures = {};

        this.objectLoader = new THREE.OBJLoader();
        this.textureLoader = new THREE.TextureLoader()
    }

    loadObject(objectLocation, callback) {
        return this.objectLoader.load(objectLocation, callback)
    }

    loadTexture(textureLocation) {
        return this.textureLoader.load(textureLocation)
    }

    loadTexturesAndAssign(target, args, diffuse, normal) {

        if (diffuse) {
            const diffuseMap = this.loadTexture(diffuse)
            args.map = diffuseMap
        }

        if (normal) {
            const normalMap = this.loadTexture(normal)
            args.normalMap = normalMap
        }

        const material = new THREE.MeshPhongMaterial(args);
        target.material = material;
    }
}

window.Loader = new Loader();