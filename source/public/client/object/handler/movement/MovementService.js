import { MovementOrder } from ".";
import { movementTypes } from ".";
import { MovementResolver } from ".";
import { OverChannelResolver } from ".";

class MovementService {
  constructor() {
    this.gamedata = null;
  }

  getCurrentMovementVector(ship) {
    const moves = this.getThisTurnMovement(ship);
    return moves.reduce((vector, move) => {
      if (move.isDeploy() || move.isEnd()) {
        return move.target;
      } else if (move.isSpeed()) {
        return vector.add(move.target);
      }

      return vector;
    }, new hexagon.Offset(0, 0));
  }

  update(gamedata, phaseStrategy) {
    this.gamedata = gamedata;
    this.phaseStrategy = phaseStrategy;
  }

  replaceTurnMovement(ship, newMovement) {
    ship.movement = [
      ...ship.movement.filter(
        move =>
          move.turn !== this.gamedata.turn ||
          (move.turn === this.gamedata.turn && !move.isPlayerAdded())
      ),
      ...newMovement.filter(move => move.isPlayerAdded())
    ];
  }

  getDeployMove(ship) {
    return ship.movement.find(move => move.type === "deploy");
  }

  getMostRecentMove(ship) {
    const move = ship.movement
      .slice()
      .reverse()
      .find(move => move.turn === this.gamedata.turn);
    if (move) {
      return move;
    }

    return ship.movement[ship.movement.length - 1];
  }

  isMoved(ship, turn) {
    const end = this.getLastEndMove(ship);

    if (!end || !end.isEnd()) {
      return false;
    }

    return end.turn === turn;
  }

  getLastEndMove(ship) {
    let end = ship.movement
      .slice()
      .reverse()
      .find(move => move.isEnd());

    if (!end) {
      end = this.getDeployMove(ship);
    }

    if (!end) {
      end = ship.movement[0];
    }

    return end;
  }

  getLastTurnEndMove(ship) {
    let end = ship.movement
      .slice()
      .reverse()
      .find(move => move.isEnd() && move.turn === this.turn - 1);

    if (!end) {
      end = this.getDeployMove(ship);
    }

    if (!end) {
      end = ship.movement[0];
    }

    return end;
  }

  getAllMovesOfTurn(ship) {
    return ship.movement.filter(move => move.turn === this.gamedata.turn);
  }

  getShipsInSameHex(ship, hex) {
    hex = hex && this.getMostRecentMove(ship).position;
    return this.gamedata.ships.filter(
      ship2 =>
        !shipManager.isDestroyed(ship2) &&
        ship !== ship2 &&
        this.getMostRecentMove(ship2).position.equals(hex)
    );
  }

  deploy(ship, pos) {
    let deployMove = this.getDeployMove(ship);

    if (!deployMove) {
      const lastMove = this.getMostRecentMove(ship);
      deployMove = new MovementOrder(
        -1,
        movementTypes.DEPLOY,
        pos,
        lastMove.target,
        lastMove.facing,
        lastMove.rolled,
        this.gamedata.turn
      );
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

    const deployMove = this.getDeployMove(ship);
    const newfacing = mathlib.addToHexFacing(deploymove.facing, step);
    deploymove.facing = newfacing;
  }

  getEvadeMove(ship) {
    return this.getThisTurnMovement(ship).find(move => move.isEvade());
  }

  getRollMove(ship) {
    return this.getThisTurnMovement(ship).find(move => move.isRoll());
  }

  getEvasion(ship) {
    const evadeMove = this.getEvadeMove(ship);
    return evadeMove ? evadeMove.value : 0;
  }

  getMaximumEvasion(ship) {
    const max = ship.systems
      .filter(system => !system.isDestroyed() && system.maxEvasion > 0)
      .reduce((total, system) => total + system.maxEvasion, 0);

    return max;
  }

  getOverChannel(ship) {
    return new OverChannelResolver(
      this.getThrusters(ship),
      this.getThisTurnMovement(ship)
    ).getAmountOverChanneled();
  }

  getTotalProducedThrust(ship) {
    if (ship.flight) {
      return ship.freethrust;
    }

    return ship.systems
      .filter(system => system.outputType === "thrust")
      .filter(system => !system.isDestroyed())
      .reduce((accumulated, system) => {
        var crits = shipManager.criticals.hasCritical(system, "swtargetheld");
        return (
          accumulated + shipManager.systems.getOutput(ship, system) - crits
        );
      }, 0);
  }

  getRemainingEngineThrust(ship) {
    return this.getTotalProducedThrust(ship) - this.getUsedEngineThrust(ship);
  }

  getUsedEngineThrust(ship) {
    return this.getThisTurnMovement(ship)
      .filter(move => move.requiredThrust)
      .reduce(
        (total, move) => total + move.requiredThrust.getTotalAmountRequired(),
        0
      );
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

  getThisTurnMovement(ship) {
    return ship.movement.filter(
      move =>
        move.turn === this.gamedata.turn ||
        (move.isEnd() && move.turn === this.gamedata.turn - 1) ||
        move.isDeploy()
    );
  }

  shipMovementChanged(ship) {
    this.phaseStrategy.onShipMovementChanged({ ship });
  }

  canThrust(ship, direction) {
    return new MovementResolver(ship, this, this.gamedata.turn).canThrust(
      direction
    );
  }

  thrust(ship, direction) {
    new MovementResolver(ship, this, this.gamedata.turn).thrust(direction);
  }

  canCancel(ship) {
    return new MovementResolver(ship, this, this.gamedata.turn).canCancel();
  }

  canRevert(ship) {
    return new MovementResolver(ship, this, this.gamedata.turn).canRevert();
  }

  cancel(ship) {
    new MovementResolver(ship, this, this.gamedata.turn).cancel();
  }

  revert(ship) {
    new MovementResolver(ship, this, this.gamedata.turn).revert();
  }

  canPivot(ship, turnDirection) {
    return new MovementResolver(ship, this, this.gamedata.turn).canPivot(
      turnDirection
    );
  }

  pivot(ship, turnDirection) {
    return new MovementResolver(ship, this, this.gamedata.turn).pivot(
      turnDirection
    );
  }

  canRoll(ship) {
    return new MovementResolver(ship, this, this.gamedata.turn).canRoll();
  }

  roll(ship) {
    return new MovementResolver(ship, this, this.gamedata.turn).roll();
  }

  canEvade(ship, step) {
    return new MovementResolver(ship, this, this.gamedata.turn).canEvade(step);
  }

  evade(ship, step) {
    return new MovementResolver(ship, this, this.gamedata.turn).evade(step);
  }

  getThrusters(ship) {
    return ship.systems
      .filter(system => system.thruster)
      .filter(system => !system.isDestroyed());
  }
}

window.MovementService = MovementService;
export default MovementService;
