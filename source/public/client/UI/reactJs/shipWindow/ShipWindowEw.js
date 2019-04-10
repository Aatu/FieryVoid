import * as React from "react";
import styled from "styled-components"

import { Clickable } from "../styled";


const EwContainer = styled.div`
    width: 114px;
    min-height: 114px;
    height: calc(100% - 4px);
    background-color: #04161C;
    border: 1px solid #496791;
    box-sizing: border-box;
    margin: 2px;
`;

const Header = styled.div`
    widht: 100%;
    height: 13px;
    border-bottom: 1px solid #496791;
    box-sizing: border-box;
    font-size: 10px;
    color: white;
    text-transform: uppercase;
    padding: 1px 3px;
    margin: 0;
`


const Entry = styled.div`
    font-size: 9px;
    padding: 1px 2px 0px 2px;
    color: #C6E2FF;
`;

const EntryHeader = styled.span`
    color: white;
    text-transform: uppercase;
    margin-right: 5px;
`;

const ShipLink = styled.span`
    color: #C6E2FF;
    margin-right: 5px;
`;

class ShipWindowEw extends React.Component{

  

    render() {
        const {ship} = this.props;

        return (
            <EwContainer>
                <Header>E. Warfare</Header>
                {getEW(ship)}
            </EwContainer>
        );
    }

}

const getEW = ship => {
    let list = [];

    list.push(<Entry key={`dew-scs-${ship.id}`}><EntryHeader>DEW:</EntryHeader>{ew.getDefensiveEW(ship)}</Entry>);
    var CCEWamount = ew.getCCEW(ship) - ew.getDistruptionEW(ship);
    list.push(<Entry key={`ccew-scs-${ship.id}`}><EntryHeader>CCEW:</EntryHeader>{CCEWamount}</Entry>);


    const bdew = ew.getBDEW(ship) * 0.25;
    
    if (bdew) {
        list.push(<Entry key={`bdew-scs-${ship.id}`}><EntryHeader>BDEW:</EntryHeader>{bdew}</Entry>);
    }

    list = list.concat(ship.EW
        .filter(ewEntry =>  ewEntry.turn === gamedata.turn)
        .filter(ewEntry => ewEntry.type === "OEW" || ewEntry.type === "DIST" || ewEntry.type === "SOEW" || ewEntry.type === "SDEW")
        .map(ewEntry => (<Entry key={`${ewEntry.type}-scs-${ship.id}-${ewEntry.targetid}`}><EntryHeader>{ewEntry.type}:</EntryHeader><ShipLink>{gamedata.getShip(ewEntry.targetid).name}</ShipLink>{getAmount(ewEntry, ship)}</Entry>)))

    return list;
}


const getAmount = (ewEntry, ship) => {
    switch (ewEntry.type) {
        case 'SDEW':
            return ewEntry.amount * 0.5; 
        case 'DIST':
            return ewEntry.amount / 3;
        case 'OEW':
            return ewEntry.amount - ew.getDistruptionEW(ship);
        default:
            return ewEntry.amount;
    }
}

export default ShipWindowEw;
