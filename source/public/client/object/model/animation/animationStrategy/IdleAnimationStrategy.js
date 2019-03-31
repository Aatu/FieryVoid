"use strict";

import AnimationStrategy from "./AnimationStrategy";
import { ShipIdleMovementAnimation } from "..";

class IdleAnimationStrategy extends AnimationStrategy {
  constructor(shipIcons, turn, movementService, coordinateConverter) {
    super(shipIcons, turn);
    this.movementService = movementService;
    this.coordinateConverter = coordinateConverter;
  }

  update(gamedata) {
    super.update(gamedata);

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
        this.animations.push(
          new ShipIdleMovementAnimation(
            icon,
            this.movementService,
            this.coordinateConverter,
            this.animations.length * 5000
          )
        );
      }

      if (icon instanceof FlightIcon) {
        icon.hideDestroyedFighters();
      }
    }, this);
    return this;
  }

  shipMovementChanged(ship) {
    const animation = this.animations.find(
      animation => animation.ship === ship
    );

    animation.update();
  }

  deactivate() {
    if (this.shipIconContainer) {
      this.shipIconContainer.getArray().forEach(function(icon) {
        icon.show();
      }, this);
    }

    return super.deactivate();
  }
}

window.IdleAnimationStrategy = IdleAnimationStrategy;
export default IdleAnimationStrategy;
