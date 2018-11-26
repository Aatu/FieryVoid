class Animation {
  constructor() {
    this.active = false;
  }

  start() {
    this.active = true;
  }

  stop() {
    this.active = false;
  }

  reset() {}

  cleanUp() {}

  update(gameData) {}

  render(now, total, last, delta, goingBack) {}
}

window.Animation = Animation;

export default Animation;
