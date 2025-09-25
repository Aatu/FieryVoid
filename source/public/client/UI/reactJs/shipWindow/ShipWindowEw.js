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
    width: 100%;
    height: 16px;
    border-bottom: 1px solid #496791;
    box-sizing: border-box;
    font-size: 8.5px;
    color: white;
    text-transform: uppercase;
    padding: 2px 2px;
    margin: 0;
    line-height: 12px;
    display: flex;
    align-items: center;
    justify-content: flex-start;    
`


const Entry = styled.div`
    font-size: 8.5px;
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
                <Header>Electronic Warfare</Header>
                {getEW(ship)}
            </EwContainer>
        );
    }

}

const getEW = ship => {
    let list = [];
	let deployTurn = shipManager.getTurnDeployed(ship);
    if(deployTurn > gamedata.turn){ //Selected ships is not deployed yet - DK May 2025
        list.push(<Entry key={`dew-scs-${ship.id}`}><EntryHeader><br></br>DEPLOYS ON TURN</EntryHeader>{deployTurn}</Entry>);       
        return list;  
    }  

    list.push(<Entry key={`dew-scs-${ship.id}`}><EntryHeader>DEW:</EntryHeader>{ew.getDefensiveEW(ship)}</Entry>);
    var CCEWamount = Math.max(0,ew.getCCEW(ship) - ew.getDistruptionEW(ship));
    list.push(<Entry key={`ccew-scs-${ship.id}`}><EntryHeader>CCEW:</EntryHeader>{CCEWamount}</Entry>);

   	let bdew = ew.getBDEW(ship) * 0.25;
   	let DetectSEW = ew.getDetectSEW(ship);

	if(shipManager.hasSpecialAbility(ship, "ConstrainedEW")) bdew = ew.getBDEW(ship) * 0.2;
	    
    if (bdew) {
        list.push(<Entry key={`bdew-scs-${ship.id}`}><EntryHeader>BDEW:</EntryHeader>{bdew.toFixed(2)}</Entry>);
    }
    if (DetectSEW) {
        list.push(<Entry key={`DetectSEW-scs-${ship.id}`}><EntryHeader>Detect Stealth:</EntryHeader>{DetectSEW}</Entry>);
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
			if(shipManager.hasSpecialAbility(ship, "ConstrainedEW")){
			    let result = ewEntry.amount * 0.333;
			    result = Math.round(result * 3) / 3;
  				return result.toFixed(2);		
			}else{        
            	return ewEntry.amount * 0.5;
			} 
        case 'DIST':
			if(shipManager.hasSpecialAbility(ship, "ConstrainedEW")){
            	return ewEntry.amount / 4;				
			}else{        
            	return ewEntry.amount / 3;
			}
        case 'OEW':
            return Math.max(0,ewEntry.amount - ew.getDistruptionEW(ship));
        default:
            return ewEntry.amount;
    }
}

export default ShipWindowEw;
