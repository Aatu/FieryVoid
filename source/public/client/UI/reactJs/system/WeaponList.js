import * as React from "react";
import styled from "styled-components";
import SystemIcon from "./SystemIcon";

const WeaponListContainer = styled.div`
  display: flex;
  z-index: 2;
  position: fixed;
  left: 805px;
  width: calc(100% - 810px);
  bottom: 0;
  flex-wrap: wrap-reverse;

  @media (max-width: 1024px) {
    left: 0;
    width: calc(100% - 50px);
  }
`;

class WeaponList extends React.Component {
  constructor(props) {
    super(props);
  }

  getWeapons(ship, gamePhase) {
    if (ship.flight) {
      return ship.systems
        .map(fighter => fighter.systems)
        .reduce((all, weapons) => all.concat(weapons), [])
        .filter(system => system.weapon);
    }

    return ship.systems.filter(
      system =>
        system.weapon ||
        system.outputType === "thrust" ||
        system.outputType === "EW" ||
        system.outputType === "power"
    );
    //.filter(weapon => (gamePhase === 1 && weapon.ballistic) || (gamePhase === 3 && !weapon.ballistic))
  }

  render() {
    const { ship, gamePhase, movementService } = this.props;

    if (!ship) {
      return null;
    }

    const weapons = this.getWeapons(ship, gamePhase);

    return (
      <WeaponListContainer>
        {weapons.map((weapon, index) => (
          <SystemIcon
            fighter={ship.flight}
            key={`system-${index}`}
            system={weapon}
            ship={ship}
            movementService={movementService}
          />
        ))}
      </WeaponListContainer>
    );
  }
}

export default WeaponList;
