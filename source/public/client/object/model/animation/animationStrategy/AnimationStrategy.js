"use strict";

class AnimationStrategy {
  constructor(shipIcons, turn) {
    this.shipIconContainer = null;
    this.turn = 0;
    this.lastAnimationTime = 0;
    this.totalAnimationTime = 0;
    this.currentDeltaTime = 0;
    this.animations = [];
    this.paused = true;
    this.shipIconContainer = shipIcons;
    this.turn = turn;
    this.goingBack = false;
  }

  activate() {
    this.play();

    return this;
  }

  update(gameData) {
    this.animations.forEach(function(animation) {
      animation.update(gameData);
    });

    return this;
  }

  stop(gameData) {
    this.lastAnimationTime = 0;
    this.totalAnimationTime = 0;
    this.currentDeltaTime = 0;
    this.pause();
  }

  back() {
    this.goingBack = true;
    this.paused = false;
  }

  play() {
    this.paused = false;
    this.goingBack = false;
  }

  pause() {
    this.paused = true;
    this.goingBack = false;
  }

  isPaused() {
    return this.paused;
  }

  deactivate() {
    return this;
  }

  goToTime(time) {
    this.totalAnimationTime = time;
    return this;
  }

  render(coordinateConverter, scene, zoom) {
    this.updateDeltaTime.call(this, this.paused);
    this.updateTotalAnimationTime.call(this, this.paused);
    this.animations.forEach(function(animation) {
      animation.render(
        new Date().getTime(),
        this.totalAnimationTime,
        this.lastAnimationTime,
        this.currentDeltaTime,
        zoom,
        this.goingBack,
        this.paused
      );
    }, this);
  }

  positionAndFaceAllIcons() {
    this.shipIconContainer.positionAndFaceAllIcons();
  }

  positionAndFaceIcon(icon) {
    icon.positionAndFaceIcon();
  }

  /*
    AnimationStrategy.prototype.initializeAnimations = function() {
        this.animations.forEach(function (animation) {
            animation.initialize();
        })
    };
    */

  removeAllAnimations() {
    this.animations.forEach(animation => animation.deactivate());
    this.animations = [];
  }

  removeAnimation(toRemove) {
    this.animations = this.animations.filter(function(animation) {
      return animation !== animation;
    });

    toRemove.deactivate();
  }

  shipMovementChanged() {}

  updateTotalAnimationTime(paused) {
    if (paused) {
      return;
    }

    if (this.goingBack) {
      this.totalAnimationTime -= this.currentDeltaTime;
    } else {
      this.totalAnimationTime += this.currentDeltaTime;
    }
  }

  updateDeltaTime(paused) {
    var now = new Date().getTime();

    if (!this.lastAnimationTime) {
      this.lastAnimationTime = now;
      this.currentDeltaTime = 0;
    }

    if (!paused) {
      this.currentDeltaTime = now - this.lastAnimationTime;
    }

    this.lastAnimationTime = now;
  }
}

window.AnimationStrategy = AnimationStrategy;

export default AnimationStrategy;
