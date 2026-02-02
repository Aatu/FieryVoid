import * as React from "react";
import styled from "styled-components"
import SystemIcon from "../system/SystemIcon"

const ShipSectionContainer = styled.div`
    display: flex;
    flex-wrap: wrap-reverse;
    width: ${props => {
        switch (props.location) {
            case 1:
            case 0:
            case 2:
                return '40%';
            default:
                return '30%'
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
                return '2px solid #6089c1';
            default:
                return '2px dotted #496791';
        }
    }};
`

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
    color: ${props => props.$health === 0 ? 'transparent' : 'white'};
    font-family: arial;
    font-size: 11px;
    text-shadow: black 0 0 6px, black 0 0 6px;
    border: 1px solid #496791;
    margin: 2px;
    filter: ${props => props.$health === 0 ? 'blur(1px)' : 'none'};

    &::before {
        box-sizing: border-box;
        content: "";
        position:absolute;
        width:  ${props => props.$health}%;
        height: 100%;
        left: 0;
        bottom: 0;
        z-index: 0;
        background-color: ${props => props.$criticals ? '#ed6738' : '#427231'};
        border: 1px solid black;
    }

`;

class ShipSection extends React.Component {
    render() {
        const { ship, systems, location } = this.props;

        const structure = getStructure(systems);

        return (
            <ShipSectionContainer location={location}>
                {orderSystems(systems, location).map(system => (<SystemIcon scs key={`system-scs-${location}-${ship.id}-${system.id}`} system={system} ship={ship} />))}

                {structure && <StructureContainer $health={getStructureLeft(ship, structure)} $criticals={hasCriticals(structure)}>
                    <StructureText>{structure.maxhealth - damageManager.getDamage(ship, structure)} / {structure.maxhealth} A {shipManager.systems.getArmour(ship, structure)}</StructureText>
                </StructureContainer>}
            </ShipSectionContainer>
        )
    }
}

const getStructureLeft = (ship, system) => (system.maxhealth - damageManager.getDamage(ship, system)) / system.maxhealth * 100;

const hasCriticals = (system) => shipManager.criticals.hasCriticals(system)

const getStructure = systems => systems.find(system => system instanceof Structure)

const filterStructure = systems => systems.filter(system => !(system instanceof Structure))

const orderSystems = (systems, location) => {
    systems = filterStructure(systems);

    if ([4, 41, 42].includes(location)) {
        return orderSystemsThreeWide(systems);
    } else if ([3, 31, 32].includes(location)) {
        return reverseRowsOfThree(orderSystemsThreeWide(systems));
    } else if ([1, 2, 0].includes(location)) {
        return orderSystemsFourWide(systems);
    } else {
        return orderWide(systems)
    }
}

const reverseRowsOfThree = (systems) => {
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
    })

    return list;
}

const orderWide = (systems) => {
    systems = filterStructure(systems);

    let list = [];

    if (systems.length === 3) {
        return orderSystemsThreeWide(systems);
    } else if (systems.length === 4) {
        return orderSystemsFourWide(systems);
    } else {
        return systems;
    }
}

const orderSystemsFourWide = (systems) => {
    systems = filterStructure(systems);

    let list = [];

    //4 equal systems
    while (true) {

        const { picked, remaining } = pickOuter(systems, 4);

        if (picked.length === 0) {
            break;
        }

        systems = remaining;

        list = list.concat(picked);
    }


    //2 systems, plus optionally 2 other systems in the middle
    while (true) {

        const { picked, remaining } = pickOuter(systems, 2);

        if (picked.length === 0) {
            break;
        }


        systems = remaining;

        const secondPick = pickOuter(systems, 2);

        if (secondPick.picked.length > 0) {
            systems = secondPick.remaining;
            list = list.concat([picked[0], secondPick.picked[0], secondPick.picked[1], picked[1]]);
        } else {
            //list = list.concat([picked[0], systems.pop(), systems.pop(), picked[1]]) //use shift so system order is not reversed
            list = list.concat([picked[0], systems.shift(), systems.shift(), picked[1]]);
            list = list.filter(system => system);
        }
    }

    list = list.concat(systems);

    return list;
}

const orderSystemsThreeWide = (systems) => {
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
}

const findFriendForTwo = (two, systems) => {

    const onePick = pick(systems, 1);

    /* singleton in the middle - does not look that good on the sides! changing to singleton on the inside
    if (onePick.picked.length === 1) {
        return {three: [two[0], onePick.picked[0], two[1]], remainingSystems: onePick.remaining}
    }

    if (systems.length > 0) {
        return {three: [two[0], systems.pop(), two[1]], remainingSystems: systems}
    }
    */
    if (onePick.picked.length === 1) {
        return { three: [onePick.picked[0], two[0], two[1]], remainingSystems: onePick.remaining }
    }

    if (systems.length > 0) {
        /*return {three: [systems.pop(), two[0], two[1] ], remainingSystems: systems}*/ //use shift so system order is not reversed
        return { three: [systems.shift(), two[0], two[1]], remainingSystems: systems }
    }

    return { three: [two[0], two[1]], remainingSystems: systems }
}

const pick = (systems, amount = 3) => {
    const one = systems.find(system => {
        const count = systems.reduce((all, otherSystem) => {
            if (otherSystem.name === system.name) {
                return all + 1;
            }

            return all;
        }, 0)

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
    // this gets first X...
    const remaining = systems.filter(otherSystem => {
        if (otherSystem.name === one.name && amount > 0) {
            amount--;
            picked.push(otherSystem);
            return false;
        }

        return true;
    })

    return { picked, remaining };
}


//like pick(), but instead of picking first X elements - picks outer X elements
const pickOuter = (systems, amount = 3) => {
    const one = systems.find(system => {
        const count = systems.reduce((all, otherSystem) => {
            if (otherSystem.name === system.name) {
                return all + 1;
            }

            return all;
        }, 0)

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
    let picked2 = [];
    // this gets outer X...
    const remaining = systems.filter(otherSystem => {
        if (otherSystem.name === one.name /*&& amount > 0*/) {
            //amount--;
            picked2.push(otherSystem);
            return false;
        }

        return true;
    })

    var fromBeginning = Math.ceil(amount / 2);
    var fromEnding = Math.floor(amount / 2);
    for (var i = 0; i < picked2.length; i++) {
        if ((i < fromBeginning) || (i >= (picked2.length - fromEnding))) { //elements from beginning and end get picked
            picked.push(picked2[i]);
        } else {//remaining elements (from the middle) get returned to the pool
            remaining.unshift(picked2[i]); //return to the beginning - so they're picked first in next row!
        }
    }

    return { picked, remaining };
}


export default ShipSection;