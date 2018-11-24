import * as React from "react";
import styled from "styled-components";

const HealthBar = styled.div`
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 7px;
  border: 2px solid black;
  box-sizing: border-box;

  background-color: black;

  :before {
    content: "";
    position: absolute;
    width: ${props => `${props.health}%`};
    height: 100%;
    left: 0;
    bottom: 0;
    background-color: ${props => (props.criticals ? "#ed6738" : "#427231")};
  }
`;

const SystemText = styled.div`
  width: 100%;
  height: calc(100% - 5px);
  color: white;
  font-family: arial;
  font-size: 10px;
  display: flex;
  align-items: flex-end;
  justify-content: center;
  text-shadow: black 0 0 6px, black 0 0 6px;
`;

const System = styled.div`
  position: relative;
  box-sizing: border-box;
  width: 32px;
  height: 32px;
  margin: ${props => (props.scs ? "3px 0" : "2px")};
  border: ${props => {
    if (props.firing) {
      return "1px solid #eb5c15";
    } else {
      return "1px solid #496791";
    }
  }};
  background-color: ${props => {
    if (props.selected) {
      return "#4e6c91";
    } else if (props.firing) {
      return "#e06f01";
    } else {
      return "black";
    }
  }};
  box-shadow: ${props => {
    if (props.selected) {
      return "0px 0px 15px #0099ff";
    } else if (props.firing) {
      return "box-shadow: 0px 0px 15px #eb5c15";
    } else {
      return "none";
    }
  }};
  background-image: ${props => `url(${props.background})`};
  background-size: cover;
  filter: ${props => (props.destroyed ? "blur(1px)" : "none")};
  cursor: pointer;

  ${SystemText} {
    display: ${props => (props.offline ? "none" : "flex")};
  }

  :before {
    content: "";
    position: absolute;
    width: 100%;
    height: 100%;
    opacity: ${props => {
      if (props.destroyed || props.offline || props.loading) {
        return "0.5";
      }

      return "0";
    }};

    background-color: ${props => {
      if (props.destroyed || props.offline) {
        return "black";
      } else if (props.loading) {
        return "orange";
      }

      return "transparent";
    }};

    background-image: ${props => {
      if (props.offline) {
        return "url(./img/offline.png)";
      }

      return "none";
    }};
  }
`;

class SystemIcon extends React.Component {
  constructor(props) {
    super(props);
  }

  clickSystem(e) {
    e.stopPropagation();
    e.preventDefault();

    let { system, ship } = this.props;

    if (gamedata.waiting) return;

    if (
      shipManager.isDestroyed(ship) ||
      shipManager.isDestroyed(ship, system) ||
      shipManager.isAdrift(ship)
    )
      return;

    if (
      (system.weapon && (gamedata.gamephase === 3 && !system.ballistic)) ||
      (gamedata.gamephase === 1 && system.ballistic)
    ) {
      if (gamedata.isMyShip(ship)) {
        if (weaponManager.isSelectedWeapon(system)) {
          weaponManager.unSelectWeapon(ship, system);
        } else {
          weaponManager.selectWeapon(ship, system);
        }
      }
    }

    if (gamedata.isMyShip(ship)) {
      webglScene.customEvent("SystemClicked", {
        ship: ship,
        system: system,
        element: e.target
      });
    } else {
      webglScene.customEvent("SystemTargeted", { ship: ship, system: system });
    }
  }

  onSystemMouseOver(event) {
    event.stopPropagation();
    event.preventDefault();

    let { system, ship } = this.props;

    webglScene.customEvent("SystemMouseOver", {
      ship: ship,
      system: system,
      element: event.target
    });
  }

  onSystemMouseOut(event) {
    event.stopPropagation();
    event.preventDefault();
    webglScene.customEvent("SystemMouseOut");
  }

  onContextMenu(e) {
    e.stopPropagation();
    e.preventDefault();

    let { system, ship } = this.props;

    if (system.weapon) {
      weaponManager.selectAllWeapons(ship, system);
    }
  }

  render() {
    let { system, ship, scs, fighter, destroyed, movementService } = this.props;

    if (getDestroyed(ship, system) || destroyed) {
      return (
        <System background={getBackgroundImage(system)} destroyed>
          <HealthBar health="0" />
        </System>
      );
    }

    return (
      <System
        scs={scs}
        onClick={this.clickSystem.bind(this)}
        onMouseOver={this.onSystemMouseOver.bind(this)}
        onMouseOut={this.onSystemMouseOut.bind(this)}
        onContextMenu={this.onContextMenu.bind(this)}
        background={getBackgroundImage(system)}
        offline={isOffline(ship, system)}
        loading={isLoading(system)}
        selected={isSelected(system)}
        firing={isFiring(ship, system)}
      >
        <SystemText>{getText(ship, system, movementService)}</SystemText>
        {!fighter && (
          <HealthBar
            scs={scs}
            health={getStructureLeft(ship, system)}
            criticals={hasCriticals(system)}
          />
        )}
      </System>
    );
  }
}

const isFiring = (ship, system) => weaponManager.hasFiringOrder(ship, system);

const isLoading = system => system.weapon && !weaponManager.isLoaded(system);

const isOffline = (ship, system) => shipManager.power.isOffline(ship, system);

const getStructureLeft = (ship, system) =>
  ((system.maxhealth - damageManager.getDamage(ship, system)) /
    system.maxhealth) *
  100;

const getDestroyed = (ship, system) =>
  shipManager.systems.isDestroyed(ship, system);

const getBackgroundImage = system => {
  if (system.name == "thruster") {
    return "./img/systemicons/thruster" + system.direction + ".png";
  } else if (system.iconPath) {
    return `./img/systemicons/${system.iconPath}`;
  } else {
    return `./img/systemicons/${system.name}.png`;
  }
};

const hasCriticals = system => shipManager.criticals.hasCriticals(system);

const isSelected = system => weaponManager.isSelectedWeapon(system);

const getText = (ship, system, movementService) => {
  if (system.weapon) {
    const firing = weaponManager.hasFiringOrder(ship, system);

    if (firing && system.canChangeShots) {
      const fire = weaponManager.getFiringOrder(ship, system);
      return fire.shots + "/" + system.shots;
    } else if (!firing) {
      /*
            if (system.duoWeapon) {
                var UI_active = systemwindow.find(".UI").hasClass("active");

                shipWindowManager.addDuoSystem(ship, system, systemwindow);

                if (UI_active) {
                    systemwindow.find(".UI").addClass("active");
                }
            }*/

      let load = weaponManager.getWeaponCurrentLoading(system);
      let loadingtime = system.loadingtime;

      if (system.normalload > 0) {
        loadingtime = system.normalload;
      }

      if (load > loadingtime) {
        load = loadingtime;
      }

      let overloadturns = "";

      if (
        system.overloadturns > 0 &&
        shipManager.power.isOverloading(ship, system)
      ) {
        overloadturns = "(" + system.overloadturns + ")";
      }

      if (system.overloadshots > 0) {
        return "S" + system.overloadshots;
      } else {
        return load + overloadturns + "/" + loadingtime;
      }
    }
  } else if (system.outputType === "thrust") {
    return movementService.getRemainingEngineThrust(ship);
  } else if (system.outputType === "power") {
    return shipManager.power.getReactorPower(ship, system);
  } else {
    return shipManager.systems.getOutput(ship, system);
  }
};

export default SystemIcon;
