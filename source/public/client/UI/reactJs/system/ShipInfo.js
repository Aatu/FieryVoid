import * as React from "react";
import styled from "styled-components"
import { Header, Entry, OfficerHeading, OfficerEntry, OfficerName, critLines, sensorDownObPenalty } from './SystemInfo';
import theme from "../styled/theme";
import buildHitChart from "../helpers/buildHitChart";

/* A flight's critical effects (user request 2026-11-16) - headings in the orange the fighter card's bar turns for them
   (theme healthCrit, as the system card's crit rows); the count and the "until" as the system card prints them.
   ⚠️ NOT styled(Header): SystemInfo imports THIS file, so in the real bundle SystemInfo's exports are still undefined
   while this module's top level runs, and styled(undefined) throws at load - taking window.UIManager with it.
   Header's own shape (11px bold span) is restated here instead; imports from ./SystemInfo are read at RENDER only. */
const CritHeading = styled.span`
    font-size: 11px;
    font-weight: bold;
    color: ${theme.colors.healthCrit};
`;

const CritCount = styled.span`
    color: ${theme.colors.healthCrit};
    font-weight: bold;
`;

const CritUntil = styled.span`
    color: ${theme.colors.textDim};
`;

/* On the hover of ONE craft's card (`craft`, from FighterIcon): the effects on the whole flight - parked on its first
   craft, where the server reads them (shipManager.criticals.getFlightCriticals) - and this craft's own, its
   subsystems' included (a magazine's MissileLost). The card's overlays already say when a craft has left the flight
   or is cut off, so those markers are not repeated. critLines is the system card's, so both use the same words. */
const fighterCritLines = (ship, craft) => {
	const criticals = shipManager.criticals;
	const notState = crit => !criticals.isCraftStateCritical(crit);
	const sample = criticals.getSampleCraft(ship);
	const flight = sample ? critLines(sample, notState) : [];
	const own = criticals.isSampleCraft(ship, craft) ? [] : critLines(craft, notState);
	const subsystems = Array.isArray(craft.systems) ? craft.systems : Object.values(craft.systems || {});
	subsystems.forEach(sub => {
		if (!sub) return;
		critLines(sub).forEach(line => own.push(Object.assign({}, line, { key: sub.id + '-' + line.key, system: sub.displayName })));
	});
	return { flight, own };
};

const critLineText = line => (
	<React.Fragment>
		{line.count > 1 && <CritCount>{'×' + line.count + ' '}</CritCount>}
		{line.system ? line.system + ': ' : ''}{line.text}
		{line.until && <CritUntil>{' ' + line.until}</CritUntil>}
	</React.Fragment>
);

//$tightBottom (ship window Notes popup, feedback 2026-07-19): drop the trailing
//blank separator Entry so the popup's small bottom padding sets the gap under the
//last line, instead of a full empty text row plus padding.
/*$compactText (ship window Notes popup, user request 2026-07-26): match the Ship Stats
  popup's type, so the two popups dropping from adjacent buttons read as one datasheet -
  10px body in the notes blue (ShipNotesPanel's StatLabel) and 10px upright white headings
  (its StatValue colour), replacing the 12px body / 11px italic heading the tooltip stack
  uses. Applied here via component selectors rather than by editing the shared
  Entry/Header (SystemInfo.js), which the system tooltips also render.*/
const InfoContainer = styled.div`
    ${props => props.$tightBottom ? '& > *:last-child { display: none; }' : ''}
    ${props => props.$compactText ? `
    ${Entry} {
        font-size: 10px;
        line-height: 1.4;
        color: ${theme.colors.textAccent};
    }
    ${Header} {
        font-size: 10px;
        font-style: normal;
        color: ${theme.colors.text};
    }` : ''}
`;

class ShipInfo extends React.Component {


	render() {
		//hideHitChart: the ship window's Notes popup reuses this block but has its own
		//dedicated HitChartPanel, so it suppresses the chart lines here.
		const { ship, hideHitChart, tightBottom, compactText, craft } = this.props;

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

		//an officer's stat in the lobby is added as it is shown (officers.displayStat); in game the server applied it
		let displayOffensiveBonus = window.officers ? officers.displayStat(ship, 'offensivebonus') : ship.offensivebonus;
		//a nebula halves it, first, as the shot does (NEBULA_PLAN.md #7) - window.nebula is game.php's alone
		const nebulaOb = !!(ship.flight && window.nebula && nebula.isActive());
		if (nebulaOb) displayOffensiveBonus = nebula.showOffensiveBonus(displayOffensiveBonus);
		//an EM storm's 1d3 this turn, as the shot takes it (EM_STORM_PLAN.md #2) - window.emStorm is game.php's alone
		if (ship.flight && window.emStorm && emStorm.isActive()) displayOffensiveBonus = Math.max(0, displayOffensiveBonus - emStorm.offensivePenalty(ship));
		//...and a tmpsensordown on its first craft, -5 each, as the shot takes it - the system card's figure (user request 2026-11-16)
		if (ship.flight) displayOffensiveBonus = Math.max(0, displayOffensiveBonus - sensorDownObPenalty(ship));
		if (ship.flight && gamedata.areMinesPresent) {
			if(ship.minesweeper){
				displayOffensiveBonus -= window.ew.getDetectMEW(ship);
			}else{
				displayOffensiveBonus -= window.ew.getDetectMEW(ship) * 2;
			}	
		}
		//OFFICERS_PLAN.md Stage 36: an Expert Electrician's shunted power this turn - its OB is direct fire's only, its thrust the flight's
		const shuntOb = (ship.flight && window.officers) ? officers.shuntOb(ship) : 0;
		const shuntThrust = (ship.flight && window.officers) ? officers.shuntThrust(ship) : 0;

		/* A FLIGHT's officer (OFFICERS_PLAN.md D12, Stage 4): his post is the first craft, and hovering a
		   craft's card shows this block rather than a system tooltip - so his line lives here: name, effect
		   and how he left action once he has. Own team only, through officers.listFor (D4). Gold, and dimmed
		   once he is out, exactly as SystemInfo's Officers block (its styled parts are shared). */
		const officerTurn = window.officers ? officers.currentTurn() : 0;
		const officerLines = [];
		/* Expert Pilots (Stage 18) are one entry per craft: identical lines show once, "Expert Pilot (3)", and one lost with
		   his craft gets a line of his own (its state differs).
		   Hovering one craft's card (`craft`, from FighterIcon): an officer bought one to a craft (`multi` - Expert Pilots, a breaching
		   pod's Breaching Experts and Troop Leaders) shows on his own craft only; a flight's single officer serves the whole flight
		   and still shows on every card. */
		if (ship.flight && window.officers) {
			const byKey = {};
			officers.listFor(ship).forEach(entry => {
				const officer = officers.get(entry.id);
				if (craft && officer && officer.multi && String(entry.post) !== String(craft.id)) return;
				const state = officers.stateText(entry);
				const line = {
					name: officers.label(entry.id, ship),
					text: (officer ? officer.summary : '') + (state ? ' ' + state : ''),
					out: !officers.serves(entry, officerTurn),
					count: 1
				};
				const key = line.name + '|' + line.text + '|' + line.out;
				if (byKey[key]) { byKey[key].count++; return; }
				byKey[key] = line;
				officerLines.push(line);
			});
			officerLines.forEach(line => { if (line.count > 1) line.name += ' (' + line.count + ')'; });
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

		//a craft's card hovered: the flight's and this craft's critical effects (see fighterCritLines)
		const crits = (ship.flight && craft) ? fighterCritLines(ship, craft) : { flight: [], own: [] };

		return (
			<InfoContainer $tightBottom={tightBottom} $compactText={compactText}>
				{ship.flight && isRevealed && <Entry key={reactKey++}><Header>Offensive bonus: </Header>{(displayOffensiveBonus + shuntOb) * 5}{shuntOb > 0 ? ' (+' + shuntOb * 5 + ' shunted, direct fire only)' : ''}</Entry>}
				{ship.flight && isRevealed && <Entry key={reactKey++}><Header>Armor (F/S/A): </Header>{shipManager.systems.getFlightArmour(ship)}</Entry>}
				{/*profile, same base numbers the ship window's stat panels show (user request
				   2026-07-23); the map tooltip's "Defence (F/S)" line adds the range/EW
				   modified chance on top of these*/}
				{ship.flight && isRevealed && <Entry key={reactKey++}><Header>Profile - Front/Side: </Header>{ship.forwardDefense * 5}/{ship.sideDefense * 5}</Entry>}
				{ship.flight && isRevealed && <Entry key={reactKey++}><Header>Initiative: </Header>{window.officers ? officers.displayIniativeBonus(ship) : ship.iniativebonus}</Entry>}
				{ship.flight && isRevealed && <Entry key={reactKey++}><Header>Thrust: </Header>{(window.officers ? officers.displayStat(ship, 'freethrust') : ship.freethrust) + shuntThrust}{shuntThrust > 0 ? ' (+' + shuntThrust + ' shunted)' : ''}</Entry>}
				{ship.flight && isRevealed && <Entry key={reactKey++}><Header>Turn Cost: </Header>{ship.turncost}</Entry>}										
				{ship.flight && isRevealed && ship.turndelay && <Entry key={reactKey++}><Header>Turn Delay: </Header>{ship.turndelaycost}</Entry>}											
				{ship.flight && isRevealed && <Entry key={reactKey++}>&nbsp;</Entry>}

				{crits.flight.length > 0 && <Entry key={reactKey++}><CritHeading>CRITICALS (WHOLE FLIGHT):</CritHeading>&nbsp;</Entry>}
				{crits.flight.map(line => <Entry key={reactKey++}>{critLineText(line)}</Entry>)}
				{crits.own.length > 0 && <Entry key={reactKey++}><CritHeading>CRITICALS (THIS FIGHTER):</CritHeading>&nbsp;</Entry>}
				{crits.own.map(line => <Entry key={reactKey++}>{critLineText(line)}</Entry>)}
				{(crits.flight.length > 0 || crits.own.length > 0) && <Entry key={reactKey++}>&nbsp;</Entry>}

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

				{ship.flight && officerLines.length > 0 && <Entry key={reactKey++}><OfficerHeading>OFFICERS:</OfficerHeading>&nbsp;</Entry>}
				{ship.flight && officerLines.map(line => <OfficerEntry key={reactKey++} $out={line.out}><OfficerName>{line.name}: </OfficerName>{line.text}</OfficerEntry>)}
				{ship.flight && officerLines.length > 0 && <Entry key={reactKey++}>&nbsp;</Entry>}

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
