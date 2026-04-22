import * as React from "react";
import styled from "styled-components"
import ReactDom from "react-dom";

import { Clickable } from "../styled";
import ShipSection from "./ShipSection";
import ShipWindowEw from "./ShipWindowEw";
import FighterList from "./FighterList";

const ShipWindowContainer = styled.div`
    display: flex;
    flex-wrap: wrap;
    position: absolute;
    ${props => {
        if (props.$isMyTeam) {
            return "left: 50px; \n top: 50px;"
        } else {
            return "right: 50px; \n top: 50px;"
        }
    }}
    max-width: ${props => props.$isTerrain ? '250px' : '400px'};
    width: ${props => props.$isTerrain ? '250px' : 'auto'};
    height: auto;
    border: 1px solid #496791;
    background-color: #0a3340;
    opacity: 0.95;
    z-index: 10001;
    overflow: hidden;
    box-shadow: 5px 5px 10px black;
    font-size: 10px;
    color: white;
    color: white;
    font-family: arial;
    
    /* Prevent text selection and callouts on mobile */
    -webkit-user-select: none;
    -webkit-touch-callout: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;

    @media (max-width: 1024px) {
        ${props => {
        if (props.$isMyTeam) {
            return "left: 0; \n top: 0; \n right: unset;"
        } else {
            return "right: 0; \n top: 0; \n left: unset;"
        }
    }}
        max-height: 100vh;
        overflow-y: scroll;
    }
`;

const Header = styled.div`
    background-color: #04161c;
    border-bottom: 1px solid #496791;
    height: 26px;
    display: flex;
    align-items: center;
    padding: 0 5px;
    width: 100%;
    flex-shrink: 0;

    span {
        font-size: 12px;
        padding-right: 10px;
    }

`;

const CloseButton = styled.div`
    width: 25px;
    height: 25px;
    position: absolute;
    right: 0;
    top: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    padding-left: 5px;
    margin-top: -2px;
    color: #496791;
    ${Clickable}
`;

const Column = styled.div`
    width: 100%;
    max-height: calc(33.3333333% - 11px);
    display: flex;
    flex-direction: row;
    justify-content: ${props => props.$top ? 'space-between' : 'center'};
    overflow: hidden;
`;

const ShipImage = styled.div`
    width: 114px;
    height: 114px;
    background-color: black;
    background-image: ${props => `url(${props.img})`};
    background-size: 85%;
    background-repeat: no-repeat;
    background-position: center;
    border: 1px solid #496791;
    box-sizing: border-box;
    margin: 2px;
    transform: rotate(-90deg);
    -webkit-user-select: none;
    -webkit-touch-callout: none;
    user-select: none;
`;

const UnknownSystemIcon = styled.div`
    position: relative;
    box-sizing: border-box;
    width: 50px;
    height: 50px;
    margin: auto;
    border: 1px solid #496791;
    background-color: black;
    color: #e3c182;
    font-family: arial;
    font-size: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    -webkit-user-select: none;
    -webkit-touch-callout: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
`;


class ShipWindow extends React.Component {
    constructor(props) {
        super(props);
        this.elementRef = React.createRef();
    }

    onShipMouseOver(event) {
        if (this.touchActive) return;
        if (window.lastTouchActiveTime && Date.now() - window.lastTouchActiveTime < 1000) return;

        let { ship } = this.props;

        webglScene.customEvent('SystemMouseOver', {
            ship: ship,
            system: ship,
            element: event.target
        });

    }

    onShipClick(event) {
        event.stopPropagation();
        let { ship } = this.props;

        if (this.ignoreNextClick) {
            this.ignoreNextClick = false;
            return;
        }

        webglScene.customEvent('SystemClicked', {
            ship: ship,
            system: ship,
            element: event.target
        });
    }

    onShipTouchStart(event) {
        this.touchActive = true;
        this.ignoreNextClick = false;
        window.lastTouchActiveTime = Date.now();

        if (this.longPressTimer) {
            clearTimeout(this.longPressTimer);
        }

        const target = event.currentTarget;
        const touch = event.touches[0];
        this.touchStartX = touch.clientX;
        this.touchStartY = touch.clientY;

        this.longPressTimer = setTimeout(() => {
            this.ignoreNextClick = true; // Prevent click from firing after long press
            let { ship } = this.props;

            // Long Press -> generic tooltip
            webglScene.customEvent('SystemMouseOver', {
                ship: ship,
                system: ship,
                element: target,
                showInfo: true
            });

            this.longPressTimer = null;
        }, 400); // 400ms hold required for info window
    }

    onShipTouchMove(event) {
        if (!this.longPressTimer) return;

        const touch = event.touches[0];
        const dx = touch.clientX - this.touchStartX;
        const dy = touch.clientY - this.touchStartY;

        // Cancel if they move more than 10 pixels
        if (Math.abs(dx) > 10 || Math.abs(dy) > 10) {
            clearTimeout(this.longPressTimer);
            this.longPressTimer = null;
        }
    }

    onShipTouchCancel(event) {
        if (this.longPressTimer) {
            clearTimeout(this.longPressTimer);
            this.longPressTimer = null;
        }
        this.touchActive = false;
        webglScene.customEvent('SystemMouseOut');
    }

    onShipTouchEnd(event) {
        if (this.longPressTimer) {
            // Timer didn't pop, meaning this was a short tap
            clearTimeout(this.longPressTimer);
            this.longPressTimer = null;
        } else {
            // Timer already fired (long press). Hide info on release.
            webglScene.customEvent('SystemMouseOut');
        }

        setTimeout(() => {
            this.touchActive = false;
        }, 300); // Clear touch active after giving click events time to fire/be ignored
    }

    onUnknownMouseOver(event) {
        if (this.touchActive) return;
        if (window.lastTouchActiveTime && Date.now() - window.lastTouchActiveTime < 1000) return;

        let { ship } = this.props;
        let stealthSystem = shipManager.systems.getSystemByName(ship, "mineStealth");

        webglScene.customEvent('SystemMouseOver', {
            ship: ship,
            system: stealthSystem ? stealthSystem : ship.systems[0],
            element: event.currentTarget,
            showInfo: true
        });
    }

    onUnknownTouchStart(event) {
        this.touchActive = true;
        this.ignoreNextClick = false;
        window.lastTouchActiveTime = Date.now();

        if (this.longPressTimer) {
            clearTimeout(this.longPressTimer);
        }

        const target = event.currentTarget;
        const touch = event.touches[0];
        this.touchStartX = touch.clientX;
        this.touchStartY = touch.clientY;

        this.longPressTimer = setTimeout(() => {
            this.ignoreNextClick = true; // Prevent click from firing after long press
            let { ship } = this.props;
            let stealthSystem = shipManager.systems.getSystemByName(ship, "mineStealth");

            // Long Press -> generic tooltip
            webglScene.customEvent('SystemMouseOver', {
                ship: ship,
                system: stealthSystem ? stealthSystem : ship.systems[0],
                element: target,
                showInfo: true
            });

            this.longPressTimer = null;
        }, 400); // 400ms hold required for info window
    }

    onShipMouseOut() {
        if (this.touchActive) return;
        if (window.lastTouchActiveTime && Date.now() - window.lastTouchActiveTime < 1000) return;

        webglScene.customEvent('SystemMouseOut');
    }


    componentDidMount() {
        const element = jQuery(this.elementRef.current);
        element.draggable();
    }

    close() {
        webglScene.customEvent('CloseShipWindow', { ship: this.props.ship });
    }

    render() {
        const { ship } = this.props;
        const isMyTeam = ship.team === window.gamedata.getPlayerTeam();

        var unitName = ship.shipClass;
        var shipName = ship.name;
        let isUnrevealedMine = false;

        if (ship.mine) {
            var stealthSystem = shipManager.systems.getSystemByName(ship, "mineStealth");
            if (stealthSystem && !stealthSystem.isMineRevealed(ship)) {
                unitName = "Mine";
                shipName = "Mine";
                isUnrevealedMine = true;
            }
        }

        if (ship.flight) {
            return (
                <ShipWindowContainer ref={this.elementRef} onClick={shipWindowClicked} onContextMenu={e => { e.preventDefault(); e.stopPropagation(); }} $isMyTeam={isMyTeam} team={ship.team}>
                    <Header><span>{shipName}</span> {unitName}<CloseButton onClick={this.close.bind(this)}>✕</CloseButton></Header>
                    <FighterList ship={ship} />
                </ShipWindowContainer>
            )
        }

        if (isUnrevealedMine) {
            return (<ShipWindowContainer ref={this.elementRef} onClick={shipWindowClicked} onContextMenu={e => { e.preventDefault(); e.stopPropagation(); }} $isMyTeam={isMyTeam} team={ship.team} $isTerrain={true}>
                <Header><span>{shipName}</span> {unitName}<CloseButton onClick={this.close.bind(this)}>✕</CloseButton></Header>
                <Column>
                    <ShipImage img={window.AssetManager.getSmartImagePath(ship.imagePath)} onMouseOver={this.onShipMouseOver.bind(this)} onMouseOut={this.onShipMouseOut.bind(this)} onClick={this.onShipClick.bind(this)} onTouchStart={this.onShipTouchStart.bind(this)} onTouchMove={this.onShipTouchMove.bind(this)} onTouchEnd={this.onShipTouchEnd.bind(this)} onTouchCancel={this.onShipTouchCancel.bind(this)} />
                    <UnknownSystemIcon onMouseOver={this.onUnknownMouseOver.bind(this)} onMouseOut={this.onShipMouseOut.bind(this)} onTouchStart={this.onUnknownTouchStart.bind(this)} onTouchMove={this.onShipTouchMove.bind(this)} onTouchEnd={this.onShipTouchEnd.bind(this)} onTouchCancel={this.onShipTouchCancel.bind(this)}>?</UnknownSystemIcon>
                </Column>
            </ShipWindowContainer>)
        }

        const systemsByLocation = sortIntoLocations(ship);
        const isTerrain = window.gamedata.isTerrain(ship.shipSizeClass, ship.userid) || ship.mine;

        return (<ShipWindowContainer ref={this.elementRef} onClick={shipWindowClicked} onContextMenu={e => { e.preventDefault(); e.stopPropagation(); }} $isMyTeam={isMyTeam} team={ship.team} $isTerrain={isTerrain}>
            <Header><span>{shipName}</span> {unitName}<CloseButton onClick={this.close.bind(this)}>✕</CloseButton></Header>
            <Column $top={!isTerrain}>
                <ShipImage img={window.AssetManager.getSmartImagePath(ship.imagePath)} onMouseOver={this.onShipMouseOver.bind(this)} onMouseOut={this.onShipMouseOut.bind(this)} onClick={this.onShipClick.bind(this)} onTouchStart={this.onShipTouchStart.bind(this)} onTouchMove={this.onShipTouchMove.bind(this)} onTouchEnd={this.onShipTouchEnd.bind(this)} onTouchCancel={this.onShipTouchCancel.bind(this)} />
                {systemsByLocation[1].length > 0 && <ShipSection location={1} ship={ship} systems={systemsByLocation[1]} $isTerrain={isTerrain} />}
                {!isTerrain && <ShipWindowEw ship={ship} />}
            </Column>

            <Column>
                {systemsByLocation[3].length > 0 && <ShipSection location={3} ship={ship} systems={systemsByLocation[3]} />}
                {systemsByLocation[31].length > 0 && <ShipSection location={31} ship={ship} systems={systemsByLocation[31]} />}
                {systemsByLocation[0].length > 0 && <ShipSection location={0} ship={ship} systems={systemsByLocation[0]} />}
                {systemsByLocation[4].length > 0 && <ShipSection location={4} ship={ship} systems={systemsByLocation[4]} />}
                {systemsByLocation[41].length > 0 && <ShipSection location={41} ship={ship} systems={systemsByLocation[41]} />}
            </Column>
            <Column>
                {systemsByLocation[32].length > 0 && <ShipSection location={32} ship={ship} systems={systemsByLocation[32]} />}
                {systemsByLocation[2].length > 0 && <ShipSection location={2} ship={ship} systems={systemsByLocation[2]} />}
                {systemsByLocation[42].length > 0 && <ShipSection location={42} ship={ship} systems={systemsByLocation[42]} />}
            </Column>
        </ShipWindowContainer>)
    }

}

const shipWindowClicked = () => webglScene.customEvent('CloseSystemInfo');
const sortIntoLocations = (ship) => {

    const locations = { 0: [], 1: [], 2: [], 3: [], 4: [], 5: [], 41: [], 42: [], 31: [], 32: [] };

    ship.systems.forEach((system) => {
        locations[system.location].push(system);
    });

    return locations;
}


export default ShipWindow;