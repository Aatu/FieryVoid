import * as React from "react";
import styled from "styled-components"
import ReactDom from "react-dom";

import { Clickable } from "../styled";
import ShipSection from "./ShipSection"

const ShipWindowContainer = styled.div`
    display: flex;
    flex-wrap: wrap;
    position: absolute;
    left: 50px;
    top: 50px;
    width: 400px;
    height: auto;
    border: 1px solid #496791;
    background-color: #0a3340;
    opacity: 0.85;
    z-index: 10001;
    overflow: hidden;
    box-shadow: 5px 5px 10px black;
    font-size: 10px;
    color: white;
    font-family: arial;
    padding-bottom: 4px;
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
    justify-content: center;
    overflow: hidden;
`;

const ShipImage = styled.div`
    width: 30%;
    height: 100%;
    background-color: black;
    background-image: ${ props => `url(${props.img})`};
    background-size: cover;
    background-position: center;
`;


class ShipWindow extends React.Component{

    componentDidMount() {
        const element = jQuery(ReactDom.findDOMNode(this));
        element.draggable();
    }

    close() {
        webglScene.customEvent('CloseShipWindow', {ship: this.props.ship});
    }

    render() {
        const {ship} = this.props;

        const systemsByLocation = sortIntoLocations(ship);

        return (<ShipWindowContainer onClick={shipWindowClicked} onContextMenu={e => {e.preventDefault();e.stopPropagation();}}>
            <Header><span>{ship.name}</span> {ship.shipClass}<CloseButton onClick={this.close.bind(this)}>✕</CloseButton></Header>
            <Column>
                <ShipImage img={ship.imagePath}/>
                {systemsByLocation[1].length > 0 && <ShipSection location={1} ship={ship} systems={systemsByLocation[1]} />}
                <ShipImage img={ship.imagePath}/>
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