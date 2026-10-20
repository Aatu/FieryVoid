import * as React from "react";
import styled from "styled-components"
import SystemInfoButtons, { canDoAnything, hasStyledMenu } from "./SystemInfoButtons";
import { Tooltip, TooltipHeader, TooltipEntry } from '../common'
import besideWindow from '../helpers/besideWindow'
import { setMenuTarget } from '../helpers/menuTarget'

const InfoHeader = styled(TooltipHeader)`
    font-size: 12px;
`;

const SystemInfoTooltip = styled(Tooltip)`
    position: absolute;
    z-index: 20000;
    ${props => Object.keys(props.position).reduce((style, key) => {
    return style + "\n" + key + ':' + props.position[key] + 'px;';
}, '')}
    max-width: 500px;
    text-align: left;
    opacity: ${props => props.opacity || 0.8};
    border: 1px solid #496791;
    padding-bottom: 3px;
    /*the lobby mounts #systemInfoReact inside a pointer-events: none fixed overlay
      (same as #shipWindowsReact) - this menu is interactive, so it must opt back in.
      No-op in game.php, where the mount point has no pointer-events override.*/
    pointer-events: auto;
    /*Lobby: the menu inside (ApplyDamageMenu) is a framed panel of its own, so this tooltip
      draws nothing round it - no second border, padding or fill, and no element opacity.*/
    ${props => props.$bare && `
        padding: 0;
        border: 0;
        border-radius: 0;
        background: none;
        opacity: 1;
    `}
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

    /* The lobby menu beside a ship window marks its system, whose icon is outlined while it is open
       (helpers/menuTarget, user request 2026-10-16). */
    componentDidMount() {
        this.markTarget();
    }

    componentDidUpdate() {
        this.markTarget();
    }

    componentWillUnmount() {
        setMenuTarget(null, null);
    }

    markTarget() {
        const { ship, system, avoidBox } = this.props;
        const open = Boolean(avoidBox) && canDoAnything(ship, system);
        setMenuTarget(open ? ship : null, open ? system : null);
    }

    render() {

        const { ship, system, boundingBox, avoidBox } = this.props;

        if (!canDoAnything(ship, system)) {
            return null;
        }

        return (
            <SystemInfoTooltip position={besideWindow(boundingBox, avoidBox, viewWidth(), viewHeight()) || getPosition(boundingBox)}
                opacity={hasStyledMenu(ship, system) ? 0.95 : 0.8}
                $bare={gamedata.gamephase === -2}>
                <SystemInfoButtons {...this.props} />
            </SystemInfoTooltip>
        )
    }
}

/* The size of the box the menu's right/bottom offsets are measured from: the lobby mounts it in a fixed inset:0
   overlay, whose edges are the viewport's WITHOUT its scrollbars. window.innerWidth includes a scrollbar, so with
   the lobby page scrolling a menu set on the window's left fell a scrollbar's width (~17px) short of it
   (user report 2026-10-16). */
const viewWidth = () => document.documentElement.clientWidth || window.innerWidth;
const viewHeight = () => document.documentElement.clientHeight || window.innerHeight;

/* avoidBox: the ship window's rectangle, which the lobby passes so its menu opens beside the window rather
   than over it (helpers/besideWindow); game.php passes none, and getPosition places it at the icon as before. */
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