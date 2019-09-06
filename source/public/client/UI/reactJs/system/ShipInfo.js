import * as React from "react";
import styled from "styled-components"
import {Header, Entry} from './SystemInfo';

const InfoContainer = styled.div``;

class ShipInfo extends React.Component {


    render() {		
        const {ship} = this.props;
		var notes = new Array;
		var hitChart = new Array;
		
		if(ship.notes){
			notes = ship.notes.split("<br>");
		}
		
		if(ship.hitChart){
			var names = ["Primary", "Front", "Aft", "Port", "Starboard"];
			var hitChartLine = "";
			var name = "";
			var current = 0;
			var hitChance = 0;
			
			var toDo = 5;
			if (ship.base && !ship.smallBase) {
				names[1] = "Sections";
				toDo = 2;
			} else {
				toDo = 5; //(almost) always try to show all 5 sections, there may be holes
			}
			for (var i = 0; i < toDo; i++) {
				if (ship.hitChart[i] === undefined) {
					continue; //no appropriate entry, skip it
				}
				hitChartLine = "";
				current = 0;
				for (var entryKey in ship.hitChart[i]){
					hitChance = Math.floor((entryKey - current) / 20 * 100);
					current = entryKey;
					name = ship.hitChart[i][entryKey];
					var n = name.indexOf(":");
					if (n > 0) {//hide retargeting to different section
						name = name.substring(n + 1);
					}
					if(hitChartLine != "") hitChartLine = hitChartLine + ', ';
					hitChartLine = hitChartLine + name + " " + hitChance + '%';					
				}			
				hitChart[names[i]] = hitChartLine;
			}
		}		
		
		
		return(
		    <InfoContainer>
                {ship.flight && <Entry><Header>Offensive bonus: </Header>{ship.offensivebonus * 5}</Entry>}
                {ship.flight && <Entry><Header>Armor (F/S/A): </Header>{shipManager.systems.getFlightArmour(ship)}</Entry>}
                {ship.flight && <Entry><Header>Thrust per turn: </Header>{ship.freethrust}</Entry>}
				{ship.flight && <Entry>-</Entry>}
				
				{Object.keys(notes).length > 0 && <Entry><Header>NOTES: </Header>-</Entry>}
				{Object.keys(notes).length > 0 && 
					Object.keys(notes).map(i => <Entry>{notes[i]}</Entry> )
				}
				{Object.keys(notes).length > 0 && <Entry>-</Entry>}

				{Object.keys(hitChart).length > 0 && <Entry><Header>HIT CHART: </Header>-</Entry>}
				{Object.keys(hitChart).length > 0 && 
					Object.keys(hitChart).map(i => <Entry><Header>{i}: </Header>{hitChart[i]}</Entry> )
				}
				{Object.keys(hitChart).length > 0 && <Entry>-</Entry>}
				
            </InfoContainer>
		);	
			
        

        
        /* above is the same... among other things.
         return (
            <InfoContainer>
                {ship.flight && <Entry><Header>Offensive bonus: </Header>{ship.offensivebonus * 5}</Entry>}
                {ship.flight && <Entry><Header>Armor (F/S/A): </Header>{shipManager.systems.getFlightArmour(ship)}</Entry>}
                {ship.flight && <Entry><Header>Thrust per turn: </Header>{ship.freethrust}</Entry>}
            </InfoContainer>
         );
         */
    }
}

export default ShipInfo;
