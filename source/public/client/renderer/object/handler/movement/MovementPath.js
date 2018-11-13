class MovementPath{
    constructor(ship, movementService, scene, moved = false) {
        this.ship = ship;
        this.movementService = movementService;
        this.scene = scene;
        this.moved = moved;

        this.objects = [];

        this.create();
    }

    remove() {
        this.objects.forEach(object3d => { this.scene.remove(object3d.mesh); object3d.destroy();});
    }

    create() {
        const firstMovement = this.movementService.getPreviousTurnLastMove(this.ship);
        const lastMovement = this.moved && this.movementService.getMostRecentMove(this.ship);

        console.log("creating line",   window.coordinateConverter.fromHexToGame(firstMovement.position),
        window.coordinateConverter.fromHexToGame(firstMovement.target))
        const line = new window.LineSprite(
            window.coordinateConverter.fromHexToGame(firstMovement.position),
            window.coordinateConverter.fromHexToGame(firstMovement.position.add(firstMovement.target)),
            10,
            new THREE.Color(0, 0, 1),
            0.5,
            {
                blending: THREE.AdditiveBlending
            }
        );

        this.scene.add(line.mesh);
        this.objects.push(line);
    }
}

export default MovementPath;