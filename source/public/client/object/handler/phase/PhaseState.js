class PhaseState {
  constructor() {
    this.state = {};
  }

  set(key, payload) {
    this.state[key] = payload;
  }

  get(key) {
    return this.state[key];
  }
}

export default PhaseState;
