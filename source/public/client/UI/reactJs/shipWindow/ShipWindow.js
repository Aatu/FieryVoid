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
    ${  props => {
        if (props.team === 1) {
            return "left: 50px; \n top: 50px;"
        } else {
            return "right: 50px; \n top: 50px;"
        }
    }}
    max-width: 400px;
    height: auto;
    border: 1px solid #496791;
    background-color: #0a3340;
    opacity: 0.95;
    z-index: 10001;
    overflow: hidden;
    box-shadow: 5px 5px 10px black;
    font-size: 10px;
    color: white;
    font-family: arial;

    @media (max-width: 1024px) {
        ${  props => {
            if (props.team === 1) {
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
    height: 24px;
    display: flex;
    align-items: center;
    padding: 0 5px;
    width: 100%;
    flex-shrink: 0;

    span {
        font-size: 14px;
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
    justify-content: ${props => props.top ? 'space-between' : 'center'};
    overflow: hidden;
`;

const ShipImage = styled.div`
    width: 114px;
    height: 114px;
    background-color: black;
    background-image: ${ props => `url(${props.img})`};
    background-size: 85%;
    background-repeat: no-repeat;
    background-position: center;
    border: 1px solid #496791;
    box-sizing: border-box;
    margin: 2px;
    transform: rotate(-90deg);
`;


class ShipWindow extends React.Component{

    onShipMouseOver(event) {
        let {ship} = this.props;

        webglScene.customEvent('SystemMouseOver', {
            ship: ship,
            system: ship,
            element: event.target
        });
        
    }

    onShipMouseOut() {
        webglScene.customEvent('SystemMouseOut');
    }


    componentDidMount() {
        const element = jQuery(ReactDom.findDOMNode(this));
        element.draggable();
    }

    close() {
        webglScene.customEvent('CloseShipWindow', {ship: this.props.ship});
    }

    render() {
        const {ship} = this.props;

        if (ship.flight) {
            return (
                <ShipWindowContainer onClick={shipWindowClicked} onContextMenu={e => {e.preventDefault();e.stopPropagation();}} team={ship.team}>
                    <Header><span>{ship.name}</span> {ship.shipClass}<CloseButton onClick={this.close.bind(this)}>✕</CloseButton></Header>
                    <FighterList ship={ship}/>
                </ShipWindowContainer>
            )
        }

        const systemsByLocation = sortIntoLocations(ship);

        return (<ShipWindowContainer onClick={shipWindowClicked} onContextMenu={e => {e.preventDefault();e.stopPropagation();}} team={ship.team}>
            <Header><span>{ship.name}</span> {ship.shipClass}<CloseButton onClick={this.close.bind(this)}>✕</CloseButton></Header>
            <Column top>
                <ShipImage img={ship.imagePath} onMouseOver={this.onShipMouseOver.bind(this)} onMouseOut={this.onShipMouseOut.bind(this)}/>
                {systemsByLocation[1].length > 0 && <ShipSection location={1} ship={ship} systems={systemsByLocation[1]} />}
                <ShipWindowEw ship={ship}/>
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

    const locations = { 0: [], 1: [], 2: [], 3: [], 4: [], 5: [], 41: [], 42: [], 31: [], 32: []};

    ship.systems.forEach((system) => {
        locations[system.location].push(system);
    });

    return locations;
}


export default ShipWindow;