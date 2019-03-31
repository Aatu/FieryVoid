import MovementService from "../movement/MovementService";
import PhaseState from "./PhaseState";

class PhaseDirector {
  constructor() {
    this.shipIconContainer = null;
    this.ewIconContainer = null;
    this.ballisticIconContainer = null;
    this.timeline = [];

    this.animationStrategy = null;
    this.phaseStrategy = null;
    this.coordinateConverter = null;
    this.shipWindowManager = null;
    this.movementService = new MovementService();
    this.phaseState = new PhaseState();
  }

  init(coordinateConverter, scene) {
    this.coordinateConverter = coordinateConverter;
    this.shipIconContainer = new ShipIconContainer(
      this.coordinateConverter,
      scene,
      this.movementService
    );
    this.ewIconContainer = new EWIconContainer(
      this.coordinateConverter,
      scene,
      this.shipIconContainer
    );
    this.ballisticIconContainer = new BallisticIconContainer(
      this.coordinateConverter,
      scene
    );
    this.shipWindowManager = new ShipWindowManager(
      new window.UIManager($("body")[0]),
      this.movementService
    );
  }

  receiveGamedata(gamedata, webglScene) {
    this.resolvePhaseStrategy(gamedata, webglScene);
  }

  relayEvent(name, payload) {
    if (!this.phaseStrategy || this.phaseStrategy.inactive) {
      return;
    }

    this.phaseStrategy.onEvent(name, payload);
    this.shipIconContainer.onEvent(name, payload);
    this.ewIconContainer.onEvent(name, payload);
  }

  render(scene, coordinateConverter, zoom) {
    if (!this.phaseStrategy || this.phaseStrategy.inactive) {
      return;
    }

    this.phaseStrategy.render(coordinateConverter, scene, zoom);
  }

  resolvePhaseStrategy(gamedata, scene) {
    if (
      !gamedata.isPlayerInGame() ||
      gamedata.replay ||
      gamedata.status === "SURRENDERED" ||
      gamedata.status === "FINISHED"
    ) {
      return this.activatePhaseStrategy(
        window.ReplayPhaseStrategy,
        gamedata,
        scene
      );
    }

    if (gamedata.waiting) {
      return this.activatePhaseStrategy(
        window.WaitingPhaseStrategy,
        gamedata,
        scene
      );
    }

    switch (gamedata.gamephase) {
      case -1:
        return this.activatePhaseStrategy(
          window.DeploymentPhaseStrategy,
          gamedata,
          scene
        );
      case 1:
        return this.activatePhaseStrategy(
          window.InitialPhaseStrategy,
          gamedata,
          scene
        );
      case 2:
        return this.activatePhaseStrategy(
          window.MovementPhaseStrategy,
          gamedata,
          scene
        );
      case 3:
        return this.activatePhaseStrategy(
          window.FirePhaseStrategy,
          gamedata,
          scene
        );
      default:
        return this.activatePhaseStrategy(
          window.WaitingPhaseStrategy,
          gamedata,
          scene
        );
    }
  }

  activatePhaseStrategy(phaseStrategy, gamedata, scene) {
    if (this.phaseStrategy && this.phaseStrategy instanceof phaseStrategy) {
      this.phaseStrategy.update(gamedata);
      return;
    }

    if (this.phaseStrategy) {
      this.phaseStrategy.deactivate();
    }

    this.phaseStrategy = new phaseStrategy(
      this.coordinateConverter,
      this.phaseState
    ).activate(
      this.shipIconContainer,
      this.ewIconContainer,
      this.ballisticIconContainer,
      gamedata,
      scene,
      this.shipWindowManager,
      this.movementService
    );
  }
}

export default PhaseDirector;
