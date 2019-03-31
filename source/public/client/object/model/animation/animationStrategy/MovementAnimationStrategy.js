"use strict";

import AnimationStrategy from "./AnimationStrategy";
import { ShipMovementAnimation } from "..";

const MOVEMENT_TIME = 5000;

const isReplayed = (phaseState, turn, ship) => {
  const state = phaseState.get(`MovementShownTurn${turn}`);
  if (!state) {
    return false;
  }

  return state[ship.id];
};

const setReplayed = (phaseState, turn, ship) => () => {
  const state = phaseState.get(`MovementShownTurn${turn}`) || {};

  state[ship.id] = true;
  phaseState.set(`MovementShownTurn${turn}`, state);
  console.log("set replayed");
};

class MovementAnimationStrategy extends AnimationStrategy {
  constructor(
    shipIcons,
    turn,
    movementService,
    coordinateConverter,
    phaseState
  ) {
    super(shipIcons, turn);
    this.movementService = movementService;
    this.coordinateConverter = coordinateConverter;
    this.phaseState = phaseState;
  }

  update(gamedata) {
    super.update(gamedata);
    this.buildAnimations();
  }

  buildAnimations() {
    this.shipIconContainer.getArray().forEach(function(icon) {
      var ship = icon.ship;

      var turnDestroyed = shipManager.getTurnDestroyed(ship);
      var destroyed = shipManager.isDestroyed(ship);

      if (turnDestroyed !== null && turnDestroyed < this.turn) {
        icon.hide();
      } else if (turnDestroyed === null && destroyed) {
        icon.hide();
      } else {
        icon.show();

        if (this.movementService.isMoved(ship, this.turn)) {
          this.animations.push(
            new ShipMovementAnimation(
              icon,
              this.movementService,
              this.coordinateConverter
            )
              .setIsDone(isReplayed(this.phaseState, this.turn, ship))
              .setStartCallback(setReplayed(this.phaseState, this.turn, ship))
          );
        } else {
          this.animations.push(
            new ShipIdleMovementAnimation(
              icon,
              this.movementService,
              this.coordinateConverter
            )
          );
        }
      }

      if (icon instanceof FlightIcon) {
        icon.hideDestroyedFighters();
      }
    }, this);
    this.timeAnimations();
    return this;
  }

  timeAnimations() {
    let time = 0;
    this.animations.forEach(animation => {
      if (!animation.done) {
        animation.time = time;
        time += MOVEMENT_TIME;
      }
    });
  }

  shipMovementChanged(ship) {}

  deactivate() {
    if (this.shipIconContainer) {
      this.shipIconContainer.getArray().forEach(function(icon) {
        icon.show();
      }, this);
    }

    return super.deactivate();
  }
}

window.MovementAnimationStrategy = MovementAnimationStrategy;
export default MovementAnimationStrategy;
