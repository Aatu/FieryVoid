class Loader {

    constructor() {

        this.loadedObjects = {};
        this.loadedTextures = {};

        this.objectLoader = new THREE.OBJLoader();
        this.textureLoader = new THREE.TextureLoader()
    }

    loadObject(objectLocation) {
        return this.objectLoader.load(objectLocation)
    }

    loadTexture(textureLocation) {
        return this.textureLoader.load(textureLocation)
    }
}

window.Loader = Loader;