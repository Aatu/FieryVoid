import * as React from "react";
import styled from "styled-components"
import { Header, Entry } from './SystemInfo';

const InfoContainer = styled.div``;

class ShipInfo extends React.Component {


	render() {
		const { ship } = this.props;
		var notes = new Array;
		var hitChart = new Array;
		var enhArray = new Array;

		if (ship.notes) {
			notes = ship.notes.split("<br>");
		}

		if (ship.hitChart) {
			var names = ["Primary", "Front", "Aft", "Port", "Starboard"];
			var hitChartLine = "";
			var name = "";
			var current = 0;
			var hitChance = 0;

			var toDo = 5;
			if ((ship.base && !ship.smallBase)) {
				names[1] = "Sections";
				toDo = 2;
			}
			else if ((ship.SixSidedShip)) {
				names[0] = "Primary";
				names[1] = "Front";
				names[2] = "Aft";
				names[31] = "Port Front";
				names[32] = "Port Aft";
				names[41] = "Starboard Front";
				names[42] = "Starboard Aft";
				toDo = 43;
			}
			else {
				toDo = 5; //(almost) always try to show all 5 sections, there may be holes
			}
			for (var i = 0; i < toDo; i++) {
				if (ship.hitChart[i] === undefined) {
					continue; //no appropriate entry, skip it
				}
				hitChartLine = "";
				current = 0;
				for (var entryKey in ship.hitChart[i]) {
					hitChance = Math.floor((entryKey - current) / 20 * 100);
					current = entryKey;
					name = ship.hitChart[i][entryKey];
					var n = name.indexOf(":");
					if (n > 0) {//hide retargeting to different section
						name = name.substring(n + 1);
					}
					if (hitChartLine != "") hitChartLine = hitChartLine + ', ';
					hitChartLine = hitChartLine + name + " " + hitChance + '%';
				}
				hitChart[names[i]] = hitChartLine;
			}
		}

		if (ship.enhancementTooltip != '') {
			enhArray = ship.enhancementTooltip.split("<br>");
		}

		let attachedSummary = {};
		if (!ship.flight && ship.hasAttached && Object.keys(ship.hasAttached).length > 0) {
			const attachedLabels = {
				1: "Forward",
				2: "Aft",
				3: "Port",
				31: "Port-Forward",
				32: "Port-Aft",
				4: "Starboard",
				41: "Starboard-Forward",
				42: "Starboard-Aft"
			};
			
			for (let attachedId in ship.hasAttached) {
				let locId = ship.hasAttached[attachedId];
				let label = attachedLabels[locId] || "Unknown";
				if (!attachedSummary[label]) {
					attachedSummary[label] = 0;
				}
				attachedSummary[label]++;
			}
		}

		let displayOffensiveBonus = ship.offensivebonus;
		if (ship.flight && gamedata.areMinesPresent) {
			displayOffensiveBonus -= window.ew.getDetectMEW(ship) * 2;
		}

		var reactKey = 0; //needed for react so each line has unique key!

		var isRevealed = true;
		if (ship.mine) {
			var stealthSystem = shipManager.systems.getSystemByName(ship, "mineStealth");
			if (stealthSystem && !stealthSystem.isMineRevealed(ship)) {
				isRevealed = false;
				hitChart = new Array();
				notes = ["No details known, scan with OEW to identify."];
				enhArray = new Array();
			}
		}

		return (
			<InfoContainer>
				{ship.flight && isRevealed && <Entry key={reactKey++}><Header>Offensive bonus: </Header>{displayOffensiveBonus * 5}</Entry>}
				{ship.flight && isRevealed && <Entry key={reactKey++}><Header>Armor (F/S/A): </Header>{shipManager.systems.getFlightArmour(ship)}</Entry>}
				{ship.flight && isRevealed && <Entry key={reactKey++}><Header>Thrust per turn: </Header>{ship.freethrust}</Entry>}
				{ship.flight && isRevealed && <Entry key={reactKey++}>&nbsp;</Entry>}

				{Object.keys(notes).length > 0 && <Entry key={reactKey++}><Header>NOTES:</Header>&nbsp;</Entry>}
				{Object.keys(notes).length > 0 &&
					Object.keys(notes).map(i => <Entry key={reactKey++}>{notes[i]}</Entry>)
				}
				{Object.keys(notes).length > 0 && <Entry key={reactKey++}>&nbsp;</Entry>}

				{Object.keys(hitChart).length > 0 && <Entry key={reactKey++}><Header>HIT CHART:</Header>&nbsp;</Entry>}
				{Object.keys(hitChart).length > 0 &&
					Object.keys(hitChart).map(i => <Entry key={reactKey++}><Header>{i}: </Header>{hitChart[i]}</Entry>)
				}
				{Object.keys(hitChart).length > 0 && <Entry key={reactKey++}>&nbsp;</Entry>}

				{Object.keys(attachedSummary).length > 0 && <Entry key={reactKey++}><Header>UNITS ATTACHED:</Header>&nbsp;</Entry>}
				{Object.keys(attachedSummary).length > 0 &&
					Object.keys(attachedSummary).map(label => <Entry key={reactKey++}><Header>{label}: </Header>{attachedSummary[label]}</Entry>)
				}
				{Object.keys(attachedSummary).length > 0 && <Entry key={reactKey++}>&nbsp;</Entry>}

				{ship.enhancementTooltip != '' && isRevealed && <Entry key={reactKey++}><Header>ENHANCEMENTS:</Header>&nbsp;</Entry>}
				{ship.enhancementTooltip != '' && isRevealed &&
					Object.keys(enhArray).map(i => <Entry key={reactKey++}>{enhArray[i]}</Entry>)
				}
				{ship.enhancementTooltip != '' && isRevealed && <Entry key={reactKey++}>&nbsp;</Entry>}

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
