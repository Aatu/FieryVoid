import {MovementOrder} from '.';
import {movementTypes} from '.';

class MovementService{

    constructor() {
        this.gamedata = null;
    }

    update(gamedata) {
        this.gamedata = gamedata;
    }

    getShipDeployMove(ship) {
        return ship.movement.find(move => move.type === 'deploy');
    }

    getMostRecentMove(ship) {
        const move = ship.movement.slice().reverse().find(move => move.turn === this.gamedata.turn);
        if (move) {
            return move;
        }

        return ship.movement[ship.movement.length - 1];
    }

    getPreviousTurnLastMove(ship) {
        return ship.movement.slice().reverse().find(move => move.turn === this.gamedata.turn - 1);
    }

    getAllMovesOfTurn(ship) {
        return ship.movement.filter(move => move.turn === this.gamedata.turn);
    }

    deploy(ship, pos) {
        let deployMove = this.getShipDeployMove(ship);

        if (!deployMove) {
            const lastMove = this.getMostRecentMove(ship)
            deployMove = new MovementOrder(-1, movementTypes.DEPLOY, pos, lastMove.target, lastMove.facing, this.gamedata.turn);
            ship.movement.push(deployMove);
        } else {
            deployMove.position = pos;
        }
    }

    doDeploymentTurn(ship, right) {

        var step = 1;
        if (!right) {
            step = -1;
        }

        const deployMove = this.getShipDeployMove(ship);
        const newfacing = mathlib.addToHexFacing(ship.deploymove.facing, step);
        deploymove.facing = newfacing;
    }

    canEvade(ship){
        //TODO: get maunouvering systems, get amount of already evaded. Return true if can still evade
    }

    getEvadeMove(ship) {
        return ship.movement.find(move => move.isEvade() && move.turn === this.gamedata.turn);
    }
 
    getEvade(ship) {
        const evadeMove = this.getEvadeMove(ship);
        return evadeMove ? evadeMove.value : 0; 
    }

    evade(ship) {

    }

    getTotalProducedThrust(ship) {

        if (ship.flight) {
            return ship.freethrust;
        }

        return ship.systems
        .filter(system => system.outputType === 'thrust')
        .filter(system => !system.isDestroyed())
        .reduce((accumulated, system) =>{
            var crits = shipManager.criticals.hasCritical(system, "swtargetheld");
            return accumulated + system.getOutput() - crits;
        }, 0)
    }

    getRemainingEngineThrust(ship) {
        const thrustProduced = this.getTotalProducedThrust(ship);
        const thrustChanneled = this.getAllMovesOfTurn(ship).reduce((accumulator, move) => move.getThrustChanneled(), 0);

        return thrustProduced - thrustChanneled;
    }  

    getPositionAtStartOfTurn(ship, currentTurn) {
        if (currentTurn === undefined) {
            currentTurn = this.gamedata.turn;
        }
        
        let move = null;

        for (var i = ship.movement.length - 1; i >= 0; i--) {
            move = ship.movement[i];
            if (move.turn < currentTurn) { 
                break;
            }
        }        
        
        return new hexagon.Offset(move.position);
    }

    getPreviousLocation(ship) {
        var oPos = shipManager.getShipPosition(ship);
        for (var i = ship.movement.length - 1; i >= 0; i--) {
            var move = ship.movement[i];
            if (!oPos.equals(new hexagon.Offset(move.position))) return move.position;
        }
        return oPos;
    }
}

window.MovementService = MovementService;
export default MovementService;