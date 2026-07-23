import * as React from "react";
import styled from "styled-components"
import { Header, Entry } from './SystemInfo';
import buildHitChart from "../helpers/buildHitChart";

//$tightBottom (ship window Notes popup, feedback 2026-07-19): drop the trailing
//blank separator Entry so the popup's small bottom padding sets the gap under the
//last line, instead of a full empty text row plus padding.
const InfoContainer = styled.div`
    ${props => props.$tightBottom ? '& > *:last-child { display: none; }' : ''}
`;

class ShipInfo extends React.Component {


	render() {
		//hideHitChart: the ship window's Notes popup reuses this block but has its own
		//dedicated HitChartPanel, so it suppresses the chart lines here.
		const { ship, hideHitChart, tightBottom } = this.props;

		//Purchased enhancements are surfaced in the always-visible gold Enhancements box
		//for full grid ship windows, so they are NOT repeated inline here for those ships
		//(2026-07-19). Mines, fighters and terrain have no gold box (compact / flight
		//variants), so they keep the inline listing. Decided here (from ship type) rather
		//than via a caller prop, so every consumer - the Notes popup AND the ship-info
		//popup, game and lobby - follows the same rule. Mirrors ShipWindow's variant pick.
		const isTerrainOrMine = Boolean(ship.mine)
			|| (window.gamedata && typeof gamedata.isTerrain === 'function'
				&& gamedata.isTerrain(ship.shipSizeClass, ship.userid));
		const showEnhancements = Boolean(ship.flight) || isTerrainOrMine;
		var notes = new Array;
		var hitChart = new Array;
		var enhArray = new Array;

		if (ship.notes) {
			notes = ship.notes.split("<br>");
		}

		if (ship.hitChart && !hideHitChart) {
			//shared builder (helpers/buildHitChart) so this text view and the ship
			//window's HitChartPanel flyout can never drift apart
			buildHitChart(ship).forEach(function (section) {
				hitChart[section.name] = section.entries
					.map(function (entry) { return entry.name + " " + entry.chance + "%"; })
					.join(", ");
			});
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
			if(ship.minesweeper){
				displayOffensiveBonus -= window.ew.getDetectMEW(ship);
			}else{
				displayOffensiveBonus -= window.ew.getDetectMEW(ship) * 2;
			}	
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
			<InfoContainer $tightBottom={tightBottom}>
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

				{showEnhancements && ship.enhancementTooltip != '' && isRevealed && <Entry key={reactKey++}><Header>ENHANCEMENTS:</Header>&nbsp;</Entry>}
				{showEnhancements && ship.enhancementTooltip != '' && isRevealed &&
					Object.keys(enhArray).map(i => <Entry key={reactKey++}>{enhArray[i]}</Entry>)
				}
				{showEnhancements && ship.enhancementTooltip != '' && isRevealed && <Entry key={reactKey++}>&nbsp;</Entry>}

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
