class Animation {
  constructor() {
    this.active = false;
    this.started = false;
    this.done = false;
  }

  start() {
    this.active = true;
  }

  stop() {
    this.active = false;
  }

  setIsDone(done) {
    this.done = done;
    return this;
  }

  setStartCallback(callback) {
    this.startCallback = callback;
    return this;
  }

  setDoneCallback(callback) {
    this.doneCallback = callback;
    return this;
  }

  callStartCallback(total) {
    if (!this.started && total > this.time) {
      this.startCallback && this.startCallback();
      this.started = true;
    }
  }

  callDoneCallback(total) {
    if (total > this.time + this.duration && !this.done) {
      this.doneCallback && this.doneCallback();
      this.done = true;
    }
  }

  reset() {}

  cleanUp() {}

  update(gameData) {}

  render(now, total, last, delta, goingBack) {}
}

window.Animation = Animation;

export default Animation;
