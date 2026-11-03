import * as React from "react";
import styled from "styled-components"
import { Tooltip, TooltipHeader, TooltipEntry } from '../common'
import ShipInfo from "./ShipInfo";
import theme from "../styled/theme";

const InfoHeader = styled(TooltipHeader)`
    /*font-size: 12px;*/
	font-size: 13px;
`;

const SystemInfoTooltip = styled(Tooltip)`
    position: absolute;
    z-index: 20000;
    ${props => Object.keys(props.position).reduce((style, key) => {
    return style + "\n" + key + ':' + props.position[key] + 'px;';
}, '')}
    width: ${props => props.ship ? '320px' : '220px'};
    text-align: left;
    opacity:0.8;
`;

const Divider = styled.div`
    height: 1px;
    background: rgba(189, 234, 250, 0.3);
    margin: 5px 0;
`;

const CRIT_DESCRIPTIONS = {
    MissileLost: "A missile was lost to damage",
    //OrbitalRepairing: "REGENERATING - orbital and weapon fully restored after 5 docked turns"
};

//Crits whose effect magnitude is carried in each crit's `param` (one crit per hit, amount in param).
//These are shown as a single line with the SUMMED reduction (e.g. "Damage reduction reduced by 15")
//rather than a "(N x)" count, since it's the total that matters. Builder gets the summed param.
const PARAM_SUM_CRIT_DESCRIPTIONS = {
    DamageReductionReduced: (total) => `Damage reduction reduced by ${total}`,
};

export const Entry = styled(TooltipEntry)`
    text-align: left;
    /*color: #5e85bc;*/
	color: #BDEAFA; /*replace dark blue above with bluish white, more eyes friendly*/
    font-family: arial;
    /*font-size: 11px;*/
	font-size: 12px;
`;

export const Header = styled.span`
    color: white;
	font-size: 11px;
    font-weight: bold;
`;

const ShipNameHeader = styled.span`
    color: #C6E2FF;
`;

/* The Officers block (OFFICERS_PLAN.md D12) wears the gold set, like every other enhancement surface.
   Exported for ShipInfo's flight OFFICERS block, so a craft's officer reads like a system's. The
   colours are tripled-class (&&&) to win over ShipInfo's compact mode, which recolours every Entry and
   Header through a descendant selector (two classes). */
const OfficerHeader = styled(InfoHeader)`
    color: ${theme.colors.enhTitle};
`;

export const OfficerEntry = styled(Entry)`
    &&& { color: ${props => props.$out ? theme.colors.textDim : theme.colors.enhText}; }
`;

export const OfficerName = styled.span`
    &&& { color: ${theme.colors.enhTitle}; }
`;

//ShipInfo's "OFFICERS:" heading, in its own heading shape but gold
export const OfficerHeading = styled(Header)`
    &&& { color: ${theme.colors.enhTitle}; }
`;

class SystemInfo extends React.Component {

    render() {
        const { ship, selectedShip, system, boundingBox, officersOnly, craft } = this.props;

        /* OFFICERS_PLAN.md Stage 19 (user request 2026-10-20): a section's Structure health bar has no tooltip of its own - the
           bar shows its boxes and armour already - but the Expert Targeter lives in one, so hovering a bar that holds an officer
           names him "and nothing else" (ShipSection raises it only then, through StructureMouseOver). */
        if (officersOnly) {
            return (
                <SystemInfoTooltip position={getPosition(boundingBox)}>
                    <InfoHeader>{window.officers ? officers.postName(system) : system.displayName}</InfoHeader>
                    {getOfficers(ship, system, true)}
                </SystemInfoTooltip>
            );
        }

        //`system === ship` matters in the lobby: bought fleet ships are jQuery.extend
        //clones without the Ship prototype, so instanceof alone missed their
        //ship-level events and the render fell into the system branch and crashed
        if (system instanceof Ship || system === ship) {
            var unitName = ship.shipClass;
            var shipName = ship.name;
            if (system.flight) { //display fighter name instead of flight name!
                unitName = system.systems[1].displayName;
            }

            if (ship.mine) {
                var stealthSystem = shipManager.systems.getSystemByName(ship, "mineStealth");
                if (stealthSystem && !stealthSystem.isMineRevealed(ship)) {
                    unitName = "Mine";
                    shipName = "Mine";
                }
            }

            return (
                <SystemInfoTooltip ship position={getPosition(boundingBox)}>
                    <InfoHeader><ShipNameHeader>{shipName}</ShipNameHeader> - {unitName}</InfoHeader>
                    <ShipInfo ship={ship} craft={craft} />
                </SystemInfoTooltip>
            );
        }
        //special treatment for 'Special' entry (due to probable multiline)
        var specialName = 'Special';
        var specialEntry = new Array;
        if (system.data.Special && system.data.Special != "") {
            //through the same hook as the other rows: an Expert Jump Officer's "within N hexes" in the lobby
            specialEntry = String(adjustDataDisplay(ship, system, specialName)).split('<br>');
        }
        var reactKey = 0; //needed for react so each line has unique key!

        //an officer's OB as it is shown (officers.displayStat): an Expert Pilot's in the lobby, an Evade not yet sent in game
        let displayOffensiveBonus = window.officers ? officers.displayStat(ship, 'offensivebonus') : ship.offensivebonus;
        if (ship.flight && gamedata.areMinesPresent) {
			if(ship.minesweeper){
				displayOffensiveBonus -= window.ew.getDetectMEW(ship);
			}else{
                displayOffensiveBonus -= window.ew.getDetectMEW(ship) * 2;
			}
        }
        //OFFICERS_PLAN.md Stage 36: an Expert Electrician's shunted power this turn - a direct-fire weapon's only (officers.shuntOb)
        if (ship.flight && window.officers && !system.ballistic) displayOffensiveBonus += officers.shuntOb(ship);

        var systemDisplayName = system.displayName;
        var firingModeDisplay = system.firingModes ? system.firingModes[system.firingMode] : null;

        //Stage S (S-f): the Fighter Bomb draws from a ShadowHangar pool; show the live
        //count of integrated fighters available to launch. Passing `system` scopes the
        //count to THIS bomb's own bay on a multi-bay hull (shadowRegenBaseBomb, via
        //bombHangarIndex); on a single-bay hull it sums all ShadowHangars. Computed
        //client-side (like the hangar capacity line) because data[] isn't transmitted live.
        var shadowBombAvailable = null;
        if (system.name === 'ShadowFighterBomb' && window.weaponManager
            && typeof weaponManager.shadowFighterBombPool === 'function') {
            //subtractPending=true: drop fighters already queued to launch this turn so the
            //count updates the moment a launch is ordered (the held pool only shrinks on
            //the server at turn resolution).
            shadowBombAvailable = weaponManager.shadowFighterBombPool(ship, system, true);
        }

        /* WALKERS_OF_SIGMA_PLAN.md 3.16 (Stage 18, D20): the Traveler's REACTOR tooltip says how
           much of its available power came out of the ships in its Docking Bay and what surplus they
           are pooling to get it. It cannot come from system.data:
           that is built server-side per blueprint, while this moves the instant the player powers a
           docked hull down. Same shape and the same reason as shadowBombAvailable above.
           Drawn whenever there is a donor at all, INCLUDING when the grant is 0 - "3 pooled, +0" is
           precisely what a player who has powered one system down needs to see.
           Own-side only in effect, with no test for it here: an enemy viewer is masked out of
           shipsDocked, so the summary reports no donors and the line is simply absent. */
        var dockedPower = null;
        if (system.outputType === "power" && window.shipManager && shipManager.power
            && typeof shipManager.power.getDockedPowerSummary === "function") {
            var dockedPowerSummary = shipManager.power.getDockedPowerSummary(ship);
            if (dockedPowerSummary.donors > 0) dockedPower = dockedPowerSummary;
        }

        /* WALKERS_OF_SIGMA_PLAN.md 3.18 (Stage 20): a Walker jump drive declaring an abduction this turn
           names its target (user request 2026-09-13). Read off the order itself, like the menu, so it
           moves the instant the player re-targets or cancels. A lobby object has no orders - null. */
        var abductionTarget = null;
        if (typeof system.getAbductionOrder === 'function') {
            var abductionOrder = system.getAbductionOrder();
            if (abductionOrder) {
                var abductee = gamedata.getShip(abductionOrder.targetid);
                abductionTarget = abductee ? abductee.name : 'Unit ' + abductionOrder.targetid;
            }
        }

        /* OFFICERS_PLAN.md Stage 11: the power put into a laser this turn under an Expert Laser Technician. Read off the
           power entries like the boost highlight, so it moves the instant the player clicks; only the owner's team has
           an officer list, so it is null for anyone else (and in the lobby, where nothing is boosted). */
        var laserBoost = window.officers ? officers.laserBoostText(ship, system) : null;
        //Stage 17: the thrust put into an ion gun this turn under an Expert Ion Technician, and that it must fire - the same reading
        var ionBoost = window.officers ? officers.ionBoostText(ship, system) : null;

        let isUnrevealedMine = false;
        if (ship.mine) {
            var stealthSystem = shipManager.systems.getSystemByName(ship, "mineStealth");
            if (stealthSystem && !stealthSystem.isMineRevealed(ship)) {
                isUnrevealedMine = true;
                systemDisplayName = "Mine";
                specialEntry = ["No details known, scan with OEW to identify."];
            }
        }

        return (
            <SystemInfoTooltip position={getPosition(boundingBox)}>
                <InfoHeader>{systemDisplayName}</InfoHeader>

                {!ship.flight && !isUnrevealedMine && getEntry('Structure', system.maxhealth - damageManager.getDamage(ship, system) + '/' + system.maxhealth)}
                {!ship.flight && !isUnrevealedMine && getEntry('Armor', shipManager.systems.getArmour(ship, system))}
                {ship.flight && !isUnrevealedMine && getEntry('Offensive bonus', adjustObDisplay(system, displayOffensiveBonus * 5))}

                {system.firingModes && !isUnrevealedMine && getEntry('Firing mode', firingModeDisplay)}

                {system.missileArray && Object.keys(system.missileArray).length > 0 && !isUnrevealedMine && getEntry('Ammo Amount', system.missileArray[system.firingMode].amount)}

                {!isUnrevealedMine && Object.keys(system.data).map((key, i) => (key != specialName && !(key === 'Ammunition' && (system.name === 'GrapplingClaw' || system.name === 'Marines')) && getEntry(key, adjustDataDisplay(ship, system, key), 'data' + i)))}

                {shadowBombAvailable !== null && getEntry('Fighters available', shadowBombAvailable)}

                {dockedPower && getEntry('Shared by docked ships',
                    '+' + dockedPower.shared + ' of ' + dockedPower.surplus + ' pooled from '
                    + dockedPower.donors + (dockedPower.donors === 1 ? ' ship' : ' ships'))}

                {abductionTarget !== null && getEntry('Abduction target', abductionTarget)}

                {laserBoost !== null && !isUnrevealedMine && getEntry('Laser boost', laserBoost)}

                {ionBoost !== null && !isUnrevealedMine && getEntry('Thrust boost', ionBoost)}

                {Object.keys(specialEntry).length > 0 && <Entry key={`special-${reactKey++}`}><Header>Special: </Header>&nbsp;</Entry>}
                {Object.keys(specialEntry).length > 0 &&
                    Object.keys(specialEntry).map(i => <Entry key={`special-${reactKey++}`}>{specialEntry[i]}</Entry>)
                }

                {!isUnrevealedMine && getOfficers(ship, system)}

                {(Object.keys(system.critData).length > 0 || (system.criticals && system.criticals.length > 0)) && !isUnrevealedMine && getCriticals(system)}

                {(!gamedata.isMyShip(ship) && !isUnrevealedMine &&
                    (gamedata.gamephase == 3 || gamedata.gamephase == 1) &&
                    gamedata.waiting == false &&
                    gamedata.selectedSystems.length > 0 && selectedShip) &&
                    getCalledShot(ship, selectedShip, system)}


                {(gamedata.isMyShip(ship) && !isUnrevealedMine &&
                    gamedata.rules &&
                    gamedata.rules.friendlyFire === 1 &&
                    (gamedata.gamephase == 3 || gamedata.gamephase == 5 || gamedata.gamephase == 1) &&
                    gamedata.waiting == false &&
                    gamedata.selectedSystems.length > 0 && selectedShip) &&
                    getCalledShot(ship, selectedShip, system)}

                {gamedata.isMyShip(ship) && !isUnrevealedMine && system.weapon &&
                    weaponManager.hasFiringOrder(ship, system) &&
                    getCalledShotDisplay(ship, system)}

            </SystemInfoTooltip>
        )
    }
}

const getCalledShot = (ship, selectedShip, system) => {
    if (weaponManager.canCalledshot(ship, system, selectedShip)) {
        return [<InfoHeader key="calledHeader">Called shot</InfoHeader>].concat(
            gamedata.selectedSystems.map((weapon, i) => {
                if (weaponManager.isOnWeaponArc(selectedShip, ship, weapon)) {
                    if (weaponManager.checkIsInRange(selectedShip, ship, weapon)) {
                        var value = weapon.firingMode;
                        value = weapon.firingModes[value];
                        if (system.id != null && !weaponManager.canWeaponCall(weapon)) {
                            return (<Entry key={`called-${i}`}><Header>{weapon.displayName}</Header>: Cannot Called Shot</Entry>);
                        } else {
                            return (<Entry key={`called-${i}`}><Header>{weapon.displayName}</Header> - Approx:  {weaponManager.calculateHitChange(selectedShip, ship, weapon, system.id).hitChance}%</Entry>);
                        }
                    } else {
                        return (<Entry key={`called-${i}`}><Header>{weapon.displayName}</Header>: Not in Range</Entry>);
                    }
                } else {
                    return (<Entry key={`called-${i}`}><Header>{weapon.displayName}</Header>: Not in Arc</Entry>);
                }
            }))
    } else {
        return [<InfoHeader key="calledHeader">Called shot</InfoHeader>].concat(
            <Entry key="cannotTarget">Cannot Target</Entry>
        )
    }
}

const getCalledShotDisplay = (ship, system) => {
    var calledInfo = weaponManager.getCalledShotInfo(ship, system);
    if (!calledInfo) return null;

    return [
        <Divider key="calledShotDivider" />,
        <InfoHeader key="calledShotHeader">Called Shot</InfoHeader>,
        <Entry key="calledShotTarget">
            <Header>Target: </Header>{calledInfo.targetSystem.displayName} (Id: {calledInfo.targetSystem.id}) on {calledInfo.targetShip.name}
        </Entry>
    ];
}

/* OFFICERS_PLAN.md D12 - one line per officer posted on this system: his name, his one-line effect,
   and how he left action once he has. Own team only, through officers.listFor (D4). Not system.data:
   that is rebuilt per turn, replaced wholesale by a server `data`, and shared by reference across
   same-class systems ([[arch_system_info_tooltip_data_flow]]). */
const getOfficers = (ship, system, alone = false) => {
    if (!window.officers || !ship || !system) return null;
    const posted = officers.onPost(ship, system.id);
    //Stage 10: an officer moving onto this post this turn (ordered now or committed) has a line here too
    const arriving = officers.arrivingAt(ship, system.id);
    if (posted.length === 0 && arriving.length === 0) return null;
    const turn = officers.currentTurn();
    //`alone` (Stage 19, the Structure bar's tooltip): the block's officers are all there is, so no divider above them
    return (alone ? [] : [<Divider key="officerDivider" />]).concat([
        <OfficerHeader key="officerHeader">Officers</OfficerHeader>
    ]).concat(posted.map((entry, i) => {
        const state = officers.moveText(ship, entry) || officers.stateText(entry);
        return (
            <OfficerEntry key={`officer-${entry.id}-${i}`} $out={!officers.serves(entry, turn) || officers.movingTo(ship, entry) !== null}>
                <OfficerName>{officers.label(entry.id, ship)}: </OfficerName>
                {officers.summaryOf(ship, entry)}{state ? ' ' + state : ''}
            </OfficerEntry>
        );
    }), arriving.map((arrival, i) => (
        <OfficerEntry key={`arriving-${arrival.entry.id}-${i}`} $out>
            <OfficerName>{officers.label(arrival.entry.id, ship)} </OfficerName>
            {officers.arrivalText(ship, arrival.entry)}
        </OfficerEntry>
    )));
};

const getCriticals = (system) => {
    const critKeys = Object.keys(system.critData).length > 0
        ? Object.keys(system.critData)
        : [...new Set((system.criticals || []).map(c => c.phpclass))];

    if (critKeys.length === 0) return null;

    return [
        <Divider key="critDivider" />,
        <InfoHeader key="criticalHeader">Criticals</InfoHeader>
    ].concat(
        critKeys.map(phpClass => {
            let noOfCrits = 0;
            let paramTotal = 0; //sum of `param` across in-effect crits of this type (param-sum crits)
            var endEffectMin = 0;
            var endEffectMax = 0;
            var infinitePresent = false;
            var wearsOffText = "";
            endEffectMin = 0;
            endEffectMax = 0;
            infinitePresent = false;
            wearsOffText = "";

            for (const j in system.criticals) {
                if (system.criticals[j].phpclass == phpClass) {
                    if ((system.criticals[j].turn <= gamedata.turn) /*check whether it's actually a current critical*/
                        && ((system.criticals[j].turnend == 0) || (system.criticals[j].turnend >= gamedata.turn))
                    ) {
                        noOfCrits++;
                        paramTotal += parseInt(system.criticals[j].param, 10) || 0;
                        if (noOfCrits == 1) {
                            endEffectMin = system.criticals[j].turnend;
                            endEffectMax = system.criticals[j].turnend;
                            infinitePresent = (system.criticals[j].turnend == 0); //0 means infinite
                        }
                        if (system.criticals[j].turnend > 0) {
                            if (system.criticals[j].turnend > endEffectMax) endEffectMax = system.criticals[j].turnend;
                            if ((system.criticals[j].turnend < endEffectMin) || (endEffectMin == 0)) endEffectMin = system.criticals[j].turnend;
                        } else infinitePresent = true;
                    }
                }
            }

            if (endEffectMin > 0) {
                wearsOffText = " (until end of turn " + endEffectMin;
                if (infinitePresent) {
                    wearsOffText = wearsOffText + "+";
                } else if (endEffectMax > endEffectMin) {
                    wearsOffText = wearsOffText + "-" + endEffectMax;
                }
                wearsOffText = wearsOffText + ")";
            }

            //Param-sum crits (e.g. DamageReductionReduced): one line showing the TOTAL reduction, no "(N x)".
            if (noOfCrits >= 1 && PARAM_SUM_CRIT_DESCRIPTIONS[phpClass]) {
                const description = PARAM_SUM_CRIT_DESCRIPTIONS[phpClass](paramTotal);
                return (<Entry key={`critical-${phpClass}`}>{description} {wearsOffText}</Entry>);
            }

            const description = system.critData[phpClass] || CRIT_DESCRIPTIONS[phpClass] || phpClass;

            if (noOfCrits > 1) {
                return (<Entry key={`critical-${phpClass}`}>({noOfCrits} x) {description} {wearsOffText}</Entry>);
            } else if (noOfCrits == 1) {
                return (<Entry key={`critical-${phpClass}`}>{description} {wearsOffText}</Entry>);
            }
            return null;
        })
    );
};

//Let a weapon fold live state into its displayed stats (e.g. Minor Thought Pulsar's thrust
//allocation). Weapons opt in by defining the method; everything else shows the base value.
const adjustObDisplay = (system, baseObPercent) =>
    (typeof system.adjustOffensiveBonusDisplay === 'function')
        ? system.adjustOffensiveBonusDisplay(baseObPercent)
        : baseObPercent;

//...and then let an officer the lobby has not applied yet do the same (the Matter Weapons Expert's
//fire control - officers.adjustDataDisplay; a no-op in game, where the server already applied him).
const adjustDataDisplay = (ship, system, key) => {
    const value = (typeof system.adjustDataValueDisplay === 'function')
        ? system.adjustDataValueDisplay(key, system.data[key])
        : system.data[key];
    return window.officers ? officers.adjustDataDisplay(ship, system, key, value) : value;
};

const getEntry = (header, value, key) => {
    if (typeof value === 'string' && value.indexOf('<br>') !== -1) {
        const parts = value.split('<br>');
        return (
            <Entry key={key}>
                <Header>{header}: </Header>
                {parts.map((part, i) => (
                    <React.Fragment key={i}>
                        {i > 0 && <br />}
                        {part}
                    </React.Fragment>
                ))}
            </Entry>
        );
    }

    return (
        <Entry key={key} ><Header>{header}: </Header>{value}</Entry>
    )
}

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
        position.left = boundingBox.left + boundingBox.width;
    }

    return position;
}

export default SystemInfo;
