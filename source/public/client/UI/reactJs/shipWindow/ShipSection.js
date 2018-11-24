import * as React from "react";
import styled from "styled-components";
import SystemIcon from "../system/SystemIcon";

const ShipSectionContainer = styled.div`
  display: flex;
  flex-wrap: wrap-reverse;
  width: ${props => {
    switch (props.location) {
      case 1:
      case 0:
      case 2:
        return "40%";
      default:
        return "30%";
    }
  }};
  align-items: end;
  justify-content: space-around;
  overflow: hidden;
  box-sizing: border-box;
  margin: 2px;

  border: ${props => {
    switch (props.location) {
      case 0:
        return "2px solid #6089c1";
      default:
        return "2px dotted #496791";
    }
  }};
`;

const StructureText = styled.div`
  z-index: 1;
`;

const StructureContainer = styled.div`
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  box-sizing: border-box;
  width: calc(100% - 4px);
  height: 16px;
  box-sizing: border-box;
  background-color: black;
  color: ${props => (props.health === 0 ? "transparent" : "white")};
  font-family: arial;
  font-size: 11px;
  text-shadow: black 0 0 6px, black 0 0 6px;
  border: 1px solid #496791;
  margin: 2px;
  filter: ${props => (props.health === 0 ? "blur(1px)" : "none")};

  :before {
    box-sizing: border-box;
    content: "";
    position: absolute;
    width: ${props => `${props.health}%`};
    height: 100%;
    left: 0;
    bottom: 0;
    z-index: 0;
    background-color: ${props => (props.criticals ? "#ed6738" : "#427231")};
    border: 1px solid black;
  }
`;

class ShipSection extends React.Component {
  render() {
    const { ship, systems, location, movementService } = this.props;

    const structure = getStructure(systems);

    return (
      <ShipSectionContainer location={location}>
        {orderSystems(systems, location).map(system => (
          <SystemIcon
            scs
            key={`system-scs-${location}-${ship.id}-${system.id}`}
            system={system}
            ship={ship}
            movementService={movementService}
          />
        ))}

        {structure && (
          <StructureContainer
            health={getStructureLeft(ship, structure)}
            criticals={hasCriticals(structure)}
          >
            <StructureText>
              {structure.maxhealth - damageManager.getDamage(ship, structure)} /{" "}
              {structure.maxhealth} A{" "}
              {shipManager.systems.getArmour(ship, structure)}
            </StructureText>
          </StructureContainer>
        )}
      </ShipSectionContainer>
    );
  }
}

const getStructureLeft = (ship, system) =>
  ((system.maxhealth - damageManager.getDamage(ship, system)) /
    system.maxhealth) *
  100;

const hasCriticals = system => shipManager.criticals.hasCriticals(system);

const getStructure = systems =>
  systems.find(system => system instanceof Structure);

const filterStructure = systems =>
  systems.filter(system => !(system instanceof Structure));

const orderSystems = (systems, location) => {
  systems = filterStructure(systems);

  if ([4, 41, 41].includes(location)) {
    return orderSystemsThreeWide(systems);
  } else if ([3, 31, 32].includes(location)) {
    return reverseRowsOfThree(orderSystemsThreeWide(systems));
  } else if ([1, 2, 0].includes(location)) {
    return orderSystemsFourWide(systems);
  } else {
    return orderWide(systems);
  }
};

const reverseRowsOfThree = systems => {
  let list = [];

  systems.forEach((system, i) => {
    const j = i % 3;
    if (j === 0) {
      list[i + 2] = system;
    } else if (j === 1) {
      list[i] = system;
    } else {
      list[i - 2] = system;
    }
  });

  return list;
};

const orderWide = systems => {
  systems = filterStructure(systems);

  let list = [];

  if (systems.length === 3) {
    return orderSystemsThreeWide(systems);
  } else if (systems.length === 4) {
    return orderSystemsFourWide(systems);
  } else {
    return systems;
  }
};

const orderSystemsFourWide = systems => {
  systems = filterStructure(systems);

  let list = [];

  while (true) {
    const { picked, remaining } = pick(systems, 4);

    if (picked.length === 0) {
      break;
    }

    systems = remaining;

    list = list.concat(picked);
  }

  while (true) {
    const { picked, remaining } = pick(systems, 2);

    if (picked.length === 0) {
      break;
    }

    systems = remaining;

    const secondPick = pick(systems, 2);

    if (secondPick.picked.length > 0) {
      systems = secondPick.remaining;
      list = list.concat([
        picked[0],
        secondPick.picked[0],
        secondPick.picked[1],
        picked[1]
      ]);
    } else {
      list = list.concat([picked[0], systems.pop(), systems.pop(), picked[1]]);
      list = list.filter(system => system);
    }
  }

  list = list.concat(systems);

  return list;
};

const orderSystemsThreeWide = systems => {
  systems = filterStructure(systems);

  let list = [];

  while (true) {
    const { picked, remaining } = pick(systems, 3);

    if (picked.length === 0) {
      break;
    }

    systems = remaining;

    list = list.concat(picked);
  }

  while (true) {
    const { picked, remaining } = pick(systems, 2);

    if (picked.length === 0) {
      break;
    }

    const { three, remainingSystems } = findFriendForTwo(picked, remaining);

    systems = remainingSystems;

    list = list.concat(three);
  }

  list = list.concat(systems);

  return list;
};

const findFriendForTwo = (two, systems) => {
  const onePick = pick(systems, 1);

  if (onePick.picked.length === 1) {
    return {
      three: [two[0], onePick.picked[0], two[1]],
      remainingSystems: onePick.remaining
    };
  }

  if (systems.length > 0) {
    return {
      three: [two[0], systems.pop(), two[1]],
      remainingSystems: systems
    };
  }

  return { three: [two[0], two[1]], remainingSystems: systems };
};

const pick = (systems, amount = 3) => {
  const one = systems.find(system => {
    const count = systems.reduce((all, otherSystem) => {
      if (otherSystem.name === system.name) {
        return all + 1;
      }

      return all;
    }, 0);

    if (amount === 1) {
      return count === amount;
    } else {
      return count >= amount;
    }
  });

  if (!one) {
    return { picked: [], remaining: systems };
  }

  let picked = [];
  const remaining = systems.filter(otherSystem => {
    if (otherSystem.name === one.name && amount > 0) {
      amount--;
      picked.push(otherSystem);
      return false;
    }

    return true;
  });

  return { picked, remaining };
};

export default ShipSection;
