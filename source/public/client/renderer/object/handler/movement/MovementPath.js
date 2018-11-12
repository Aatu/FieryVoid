class MovementPath{
    constructor(ship, movementService, scene) {
        this.ship = ship;
        this.movementService = movementService;
        this.scene = scene;

        this.create();
    }

    remove() {}

    create() {
        const firstMovement = this.movementService.getStartMoveOfTurn(this.ship);
    }
}

export default MovementPath;