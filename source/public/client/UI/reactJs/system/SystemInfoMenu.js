import * as React from "react";
import styled from "styled-components"
import SystemInfoButtons from "./SystemInfoButtons";
import {Tooltip, TooltipHeader, TooltipEntry} from '../common'

const InfoHeader = TooltipHeader.extend`
    font-size: 12px;
`;

const SystemInfoTooltip = Tooltip.extend`
    position: absolute;
    z-index: 20000;
    ${props => Object.keys(props.position).reduce((style, key) => {
        return style + "\n" + key + ':' + props.position[key] + 'px;';
    }, '')}
    max-width: 200px;
    text-align: left;
    opacity:0.8;
    border: 1px solid #496791;
    padding-bottom: 3px;
`;

const Entry = TooltipEntry.extend`
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
        
        const {boundingBox} = this.props;

        return (
            <SystemInfoTooltip position={getPosition(boundingBox)}>
                <SystemInfoButtons {...this.props}/>
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