import * as React from "react";
import styled from "styled-components"
import SystemIcon from "../system/SystemIcon"

const ShipSectionContainer = styled.div`
    display: flex;
    flex-wrap: wrap-reverse;
    width: ${props => props.location === 1 || props.location === 0 ||  props.location === 2 ? '40%' : '30%' };
    height: 100%;
    align-items: end;
    justify-content: space-around;
    overflow: hidden;
    box-sizing: border-box;

    border-top: ${props => {
        if (props.location === 0) {
            return '2px dotted #7e9dc7';
        } else if (props.location === 3 || props.location === 4 || props.location === 31 || props.location === 41) {
            return '2px dotted #7e9dc7';
        } else {
            return 'none';
        }
    }};

    border-bottom: ${props => {
        if (props.location === 0) {
            return '2px dotted #7e9dc7';
        } else if (props.location === 3 || props.location === 4|| props.location === 31 || props.location === 41) {
            return '2px dotted #7e9dc7';
        } else {
            return 'none';
        }
    }};

    border-left: ${props => {
        if (props.location === 0) {
            return '2px dotted #7e9dc7';
        } else if (props.location === 1 || props.location === 2 || props.location === 32) {
            return '2px dotted #7e9dc7';
        } else {
            return 'none';
        }
    }};

    border-right: ${props => {
        if (props.location === 0) {
            return '2px dotted #7e9dc7';
        } else if (props.location === 1 || props.location === 2 || props.location === 42 || props.location === 32) {
            return '2px dotted #7e9dc7';
        } else {
            return 'none';
        }
    }};
`

const StructureContainer = styled.div`
    box-sizing: border-box;
    width: 100%;
    height: 20px;
    background-color: black;
`;

class ShipSection extends React.Component {

    render() {
        const {ship, systems, location} = this.props;

        return (
            <ShipSectionContainer location={location}>
                {orderSystems(systems).map(system => (<SystemIcon scs key={`system-scs-${ship.id}-${system.id}`} system={system} ship={ship}/>))}

                <StructureContainer></StructureContainer>
            </ShipSectionContainer>
        )
    }
}

const getStructure = systems => systems.find(system => system instanceof Structure)

const filterStructure = systems => systems.filter(system => !(system instanceof Structure))

const orderSystems = (systems) => {
    systems = filterStructure(systems);

    let list = [];

    while(true) {

        const {picked, remaining} = pick(systems, 3);

        if(picked.length === 0) {
            break;
        }

        systems = remaining;

        list = list.concat(picked);
    }

    while(true) {

        const {picked, remaining} = pick(systems, 2);

        if(picked.length === 0) {
            break;
        }

        const {three, remainingSystems} = findFriendForTwo(picked, remaining);

        systems = remainingSystems;

        list = list.concat(three);
    }

    list = list.concat(systems);

    return list;
}

const findFriendForTwo = (two, systems) => {

    const onePick = pick(systems, 1);
    
    if (onePick.picked.length === 1) {
        return {three: [two[0], onePick.picked[0], two[1]], remainingSystems: onePick.remaining}
    }

    if (systems.length > 0) {
        return {three: [two[0], systems.pop(), two[1]], remainingSystems: systems}
    }

    return {three: [two[0], two[1]], remainingSystems: systems}
}

const pick = (systems, amount = 3) => {
    const one = systems.find(system => {
        const count = systems.reduce((all, otherSystem) => {
            if (otherSystem.name === system.name) {
                return all+1;
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
        return {picked: [], remaining: systems};
    }

    let picked = [];
    const remaining = systems.filter(otherSystem => {
        if (otherSystem.name === one.name && amount > 0) {
            amount--;
            picked.push(otherSystem);
            return false;
        }

        return true;
    })

    return {picked, remaining};
}

export default ShipSection;