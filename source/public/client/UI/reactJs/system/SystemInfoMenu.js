import * as React from "react";
import styled from "styled-components"
import SystemInfoButtons, { canDoAnything } from "./SystemInfoButtons";
import { Tooltip, TooltipHeader, TooltipEntry } from '../common'

const InfoHeader = styled(TooltipHeader)`
    font-size: 12px;
`;

const SystemInfoTooltip = styled(Tooltip)`
    position: absolute;
    z-index: 20000;
    ${props => Object.keys(props.position).reduce((style, key) => {
    return style + "\n" + key + ':' + props.position[key] + 'px;';
}, '')}
    max-width: 600px;
    text-align: left;
    opacity: ${props => props.opacity || 0.8};
    border: 1px solid #496791;
    padding-bottom: 3px;
`;

const Entry = styled(TooltipEntry)`
    text-align: left;
    color: #5e85bc;
    font-family: arial;
    font-size: 11px;
`;

const Header = styled.span`
    color: white;
`;

class SystemInfoMenu extends React.Component {

    render() {

        const { ship, system, boundingBox } = this.props;

        if (!canDoAnything(ship, system)) {
            return null;
        }

        return (
            <SystemInfoTooltip position={getPosition(boundingBox)} opacity={(
                system.name === 'SelfRepair' ||
                system.name === 'adaptiveArmorController' ||
                system.name === 'hyachComputer' ||
                system.name === 'hyachSpecialists' ||
                system.name === 'ThoughtShield' ||
                system.name === 'ThirdspaceShield'||
                system.name === 'ThoughtShieldGenerator' ||
                system.name === 'ThirdspaceShieldGenerator'||
                system.name === 'powerCapacitor') ? 0.9 : 0.8}>
                <SystemInfoButtons {...this.props} />
            </SystemInfoTooltip>
        )
    }
}

const getPosition = boundingBox => {
    const position = {};

    if (boundingBox.top > window.innerHeight / 2) {
        position.bottom = window.innerHeight - boundingBox.top;
    } else {
        position.top = boundingBox.top + boundingBox.height;
    }

    if (boundingBox.left > window.innerWidth / 2) {
        position.right = window.innerWidth - boundingBox.right;
    } else {
        position.left = boundingBox.left;
    }

    return position;
}


export default SystemInfoMenu;