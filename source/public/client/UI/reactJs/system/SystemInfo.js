import * as React from "react";
import styled from "styled-components"
import { Tooltip, TooltipHeader, TooltipEntry } from '../common'
import ShipInfo from "./ShipInfo";
import SystemCard, { Structure, Cells, Criticals, CalledShot, Profile, SectionLabel, OfficerLine, OfficerLabel, CardNote, minusSign, rangeDash } from "./SystemCard";
import { canApplyMineDamage } from "./SystemInfoButtons";
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

const CRIT_DESCRIPTIONS = {
    MissileLost: "A missile was lost to damage",
    //OrbitalRepairing: "REGENERATING - orbital and weapon fully restored after 5 docked turns"
};

//Crits whose effect magnitude is carried in each crit's `param` (one crit per hit, amount in param).
//These are shown as a single line with the SUMMED reduction (e.g. "Damage reduction reduced by 15")
//rather than a "(N x)" count, since it's the total that matters. Builder gets the summed param.
const PARAM_SUM_CRIT_DESCRIPTIONS = {
    DamageReductionReduced: (total) => `Damage reduction reduced by ${total}`,
    //The EM storm's one-turn effects (EM_STORM_PLAN.md) - a reloaded crit's own description has lost its amount
    EMStormSensors: (total) => `EM storm: sensors -${total}`,
    EMStormPower: (total) => `EM storm: power -${total}`,
    EMStormInitiative: (total) => `EM storm: initiative -${total * 5}`,
    EMStormOffensive: (total) => `EM storm: offensive bonus -${total * 5}`,
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

/* The Officers lines (OFFICERS_PLAN.md D12) wear the gold set, like every other enhancement surface. ShipInfo's flight
   OFFICERS block uses these, so a craft's officer reads like a system's; the system card draws its own (SystemCard
   OfficerLine). The colours are tripled-class (&&&) to win over ShipInfo's compact mode, which recolours every Entry
   and Header through a descendant selector (two classes). */
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
        //avoidBox: the ship window the icon sits in, so the system card opens beside it (PhaseStrategy / gamelobby.js)
        /* structureBar: raised by a section's health bar (ShipSection StructureMouseOver) - the card for that block's Structure
           (user request 2026-11-08). It replaced the officers-only tooltip a bar used to raise only when it held an officer
           (OFFICERS_PLAN.md Stage 19): the card names him in its own officers block, so nothing is lost. */
        const { ship, selectedShip, system, boundingBox, avoidBox, structureBar, craft } = this.props;

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
        //an officer's OB as it is shown (officers.displayStat): an Expert Pilot's in the lobby, an Evade not yet sent in game
        let displayOffensiveBonus = window.officers ? officers.displayStat(ship, 'offensivebonus') : ship.offensivebonus;
        //a nebula halves it, first, as the shot does (NEBULA_PLAN.md #7) - window.nebula is game.php's alone
        const nebulaOb = !!(ship.flight && window.nebula && nebula.isActive());
        if (nebulaOb) displayOffensiveBonus = nebula.showOffensiveBonus(displayOffensiveBonus);
        //an EM storm's 1d3 this turn, as the shot takes it (EM_STORM_PLAN.md #2) - window.emStorm is game.php's alone
        if (ship.flight && window.emStorm && emStorm.isActive()) displayOffensiveBonus = Math.max(0, displayOffensiveBonus - emStorm.offensivePenalty(ship));
        if (ship.flight && gamedata.areMinesPresent) {
			if(ship.minesweeper){
				displayOffensiveBonus -= window.ew.getDetectMEW(ship);
			}else{
                displayOffensiveBonus -= window.ew.getDetectMEW(ship) * 2;
			}
        }
        //OFFICERS_PLAN.md Stage 36: an Expert Electrician's shunted power this turn - a direct-fire weapon's only (officers.shuntOb)
        if (ship.flight && window.officers && !system.ballistic) displayOffensiveBonus += officers.shuntOb(ship);

        //a section's Structure is named for its section, "Aft Structure" - the officers module's naming, as its posts use
        const isStructure = system.name === 'structure';
        var systemDisplayName = (isStructure && window.officers) ? officers.postName(system) : system.displayName;

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
            }
        }

        //a card pinned by touch closes the way its own hover ends (SystemCard): the bar's event also clears the section's wedge
        const closeEvent = structureBar ? 'StructureMouseOut' : 'SystemMouseOut';

        if (isUnrevealedMine) {
            return (
                <SystemCard subject={system} boundingBox={boundingBox} avoidBox={avoidBox} closeEvent={closeEvent} name={systemDisplayName}>
                    <CardNote>No details known, scan with OEW to identify.</CardNote>
                </SystemCard>
            );
        }

        /* THE DATASHEET CARD (system/SystemCard.js, user choice 2026-11-08). This builds what the card says, in its one
           section order; SystemCard draws it, places it and handles the rules text. Every data value still goes through
           adjustDataDisplay, so a weapon's live adjustment and an officer the lobby has not applied yet show as before. */
        const data = system.data || {};
        const has = key => data[key] !== undefined;
        const value = key => adjustDataDisplay(ship, system, key);

        //chips in the class's colour and the damage type's (theme.chips - a name it does not list draws neutral)
        const chips = [];
        if (has('Weapon type')) {
            const weaponClass = String(value('Weapon type'));
            chips.push({ kind: 'class', label: weaponClass, color: theme.chips.weaponClass[weaponClass] });
        }
        if (has('Damage type')) {
            const damageType = String(value('Damage type'));
            chips.push({ kind: 'damage', label: damageType, color: theme.chips.damageType[damageType] });
        }
        //only on something that deals damage: a jump drive is a Weapon too, and uninterceptable by default
        if (system.uninterceptable && has('Damage')) chips.push({ kind: 'flag', label: 'Uninterceptable' });

        const crits = critLines(system);

        /* A bulk mine purchase in the lobby keeps its pre-battle damage per copy, outside the blueprint's own damage, and its
           section bar shows MINE 1 (ShipSection) - the card reads the same numbers, so the two agree. */
        const bulkMine = isStructure && canApplyMineDamage(ship);
        const structureMax = bulkMine ? battleDamage.mineMaxHealth(ship) : system.maxhealth;
        const structureLeft = bulkMine ? battleDamage.mineHealth(ship, 1) : system.maxhealth - damageManager.getDamage(ship, system);

        const arcCell ={ label: 'Arc', value: has('Arc') ? value('Arc') : '—' };
        const cells = isStructure
            ? [{ label: 'Armor', value: shipManager.systems.getArmour(ship, system) }, arcCell] //a block draws no power
            : [
                ship.flight
                    ? { label: 'OB', value: adjustObDisplay(system, displayOffensiveBonus * 5) }
                    : { label: 'Armor', value: shipManager.systems.getArmour(ship, system) },
                { label: 'Power', value: has('Power Used') ? value('Power Used') : '—' },
                arcCell
            ];

        //called shot - the same tests in the same order as before: gamedata.selectedSystems is only read in game
        const enemyCalledShot = !gamedata.isMyShip(ship) &&
            (gamedata.gamephase == 3 || gamedata.gamephase == 1) &&
            gamedata.waiting == false &&
            gamedata.selectedSystems.length > 0 && selectedShip;
        const friendlyCalledShot = gamedata.isMyShip(ship) &&
            gamedata.rules &&
            gamedata.rules.friendlyFire === 1 &&
            (gamedata.gamephase == 3 || gamedata.gamephase == 5 || gamedata.gamephase == 1) &&
            gamedata.waiting == false &&
            gamedata.selectedSystems.length > 0 && selectedShip;
        const calledRows = (enemyCalledShot || friendlyCalledShot) ? calledShotRows(ship, selectedShip, system) : null;
        const ownCalledShot = (gamedata.isMyShip(ship) && system.weapon && weaponManager.hasFiringOrder(ship, system))
            ? weaponManager.getCalledShotInfo(ship, system) : null;

        //the numbers: live values first, then the weapon's own, then whatever else the system sends, in its order
        const rows = [];
        if (shadowBombAvailable !== null) rows.push({ label: 'Fighters available', value: shadowBombAvailable });
        if (dockedPower) rows.push({ label: 'Shared by docked ships', value: '+' + dockedPower.shared + ' of ' + dockedPower.surplus
            + ' pooled from ' + dockedPower.donors + (dockedPower.donors === 1 ? ' ship' : ' ships') });
        if (abductionTarget !== null) rows.push({ label: 'Abduction target', value: abductionTarget });
        if (laserBoost !== null) rows.push({ label: 'Laser boost', value: laserBoost });
        if (ionBoost !== null) rows.push({ label: 'Thrust boost', value: ionBoost });
        if (has('Damage')) rows.push({ label: 'Damage', value: rangeDash(value('Damage')), big: true });
        //every mode, the one in use bright (a single mode only repeats the damage-type chip)
        const modeKeys = system.firingModes ? Object.keys(system.firingModes) : [];
        if (modeKeys.length > 1) {
            rows.push({ label: 'Firing mode', modes: modeKeys.map(key => ({
                name: system.firingModes[key], active: String(key) === String(system.firingMode)
            })) });
        }
        if (system.missileArray && Object.keys(system.missileArray).length > 0 && system.missileArray[system.firingMode]) {
            rows.push({ label: 'Ammo', value: system.missileArray[system.firingMode].amount });
        }
        if (has('Range penalty')) rows.push({ label: 'Range penalty', value: value('Range penalty') });
        if (has('Range')) rows.push({ label: 'Range', value: value('Range') });
        if (has(FIRE_CONTROL)) rows.push({ label: 'Fire control', fc: value(FIRE_CONTROL) });
        if (has('Intercept')) rows.push({ label: 'Intercept', value: minusSign(value('Intercept')) });
        if (has('Number of guns')) rows.push({ label: 'Guns', value: value('Number of guns') });
        if (has('Number of shots')) rows.push({ label: 'Shots', value: value('Number of shots') });
        Object.keys(data).forEach(key => {
            if (CARD_KEYS.has(key)) return;
            if (key === 'Ammunition' && (system.name === 'GrapplingClaw' || system.name === 'Marines')) return;
            rows.push({ label: key, value: value(key) });
        });

        //through the same hook as the other rows: an Expert Jump Officer's "within N hexes" in the lobby
        const rules = (data.Special && data.Special != "") ? String(value('Special')) : null;
        const footer = has(PRIORITY) ? 'Resolution priority ' + value(PRIORITY) + ' (ship/fighter)' : null;

        return (
            <SystemCard subject={system} boundingBox={boundingBox} avoidBox={avoidBox} closeEvent={closeEvent}
                name={systemDisplayName} id={has('ID') ? value('ID') : system.id}
                chips={chips} rules={rules} footer={footer}>
                {!ship.flight && <Structure remaining={structureLeft} max={structureMax} crit={crits.length > 0} />}
                <Cells cells={cells} />
                {crits.length > 0 && <Criticals lines={crits} />}
                {calledRows && <CalledShot title="Called shot, approx. to hit" rows={calledRows} />}
                {ownCalledShot && (
                    <CalledShot title="Called shot target" rows={[{
                        label: ownCalledShot.targetSystem.displayName + ' (ID ' + ownCalledShot.targetSystem.id + ') on '
                            + ownCalledShot.targetShip.name
                    }]} />
                )}
                {cardOfficers(ship, system)}
                {rows.length > 0 && <Profile rows={rows} />}
            </SystemCard>
        );
    }
}

//data keys the card draws in a place of its own; every other key is printed as a row, in the order it came
const FIRE_CONTROL = 'Fire control (fighter/med/cap)';
const PRIORITY = 'Resolution Priority (ship/fighter)';
const CARD_KEYS = new Set(['Special', 'ID', 'Arc', 'Power Used', 'Damage', 'Damage type', 'Weapon type', 'Range penalty',
    'Range', FIRE_CONTROL, 'Intercept', 'Number of guns', 'Number of shots', PRIORITY]);

//the called-shot chance of each selected weapon against this system, or why there is none
const calledShotRows = (ship, selectedShip, system) => {
    if (!weaponManager.canCalledshot(ship, system, selectedShip)) return [{ label: 'Cannot Target' }];
    return gamedata.selectedSystems.map(weapon => {
        if (!weaponManager.isOnWeaponArc(selectedShip, ship, weapon)) return { label: weapon.displayName, note: 'Not in Arc' };
        if (!weaponManager.checkIsInRange(selectedShip, ship, weapon)) return { label: weapon.displayName, note: 'Not in Range' };
        if (system.id != null && !weaponManager.canWeaponCall(weapon)) return { label: weapon.displayName, note: 'Cannot Called Shot' };
        return { label: weapon.displayName, pct: weaponManager.calculateHitChange(selectedShip, ship, weapon, system.id).hitChance };
    });
};

/* OFFICERS_PLAN.md D12 - the card's officers block: one line per officer posted on this system (his name, his one-line
   effect, and how he left action once he has) plus one per officer moving onto it this turn (Stage 10). Own team only,
   through officers.listFor (D4). Not system.data: that is rebuilt per turn, replaced wholesale by a server `data`, and
   shared by reference across same-class systems ([[arch_system_info_tooltip_data_flow]]). A Structure block's card
   carries its Expert Targeter here (Stage 19) - it replaced the officers-only tooltip the bar used to raise. */
const cardOfficers = (ship, system) => {
    if (!window.officers || !ship || !system) return null;
    const posted = officers.onPost(ship, system.id);
    const arriving = officers.arrivingAt(ship, system.id);
    if (posted.length === 0 && arriving.length === 0) return null;
    const turn = officers.currentTurn();
    return (
        <div>
            <SectionLabel $gold>Officers</SectionLabel>
            {posted.map((entry, i) => {
                const state = officers.moveText(ship, entry) || officers.stateText(entry);
                return (
                    <OfficerLine key={`officer-${entry.id}-${i}`} $out={!officers.serves(entry, turn) || officers.movingTo(ship, entry) !== null}>
                        <OfficerLabel>{officers.label(entry.id, ship)}: </OfficerLabel>
                        {officers.summaryOf(ship, entry)}{state ? ' ' + state : ''}
                    </OfficerLine>
                );
            })}
            {arriving.map((arrival, i) => (
                <OfficerLine key={`arriving-${arrival.entry.id}-${i}`} $out>
                    <OfficerLabel>{officers.label(arrival.entry.id, ship)} </OfficerLabel>
                    {officers.arrivalText(ship, arrival.entry)}
                </OfficerLine>
            ))}
        </div>
    );
};

/* The criticals in effect on this system, one line per critical class: { key, count, text, until }. Was getCriticals,
   which drew them itself; the turn and param arithmetic is unchanged. A class with no crit in effect gives no line, so
   the card shows no empty "Criticals" block (the old one drew the heading anyway). */
const critLines = (system) => {
    const critData = system.critData || {};
    const criticals = Array.isArray(system.criticals) ? system.criticals : Object.values(system.criticals || {});
    const critKeys = Object.keys(critData).length > 0
        ? Object.keys(critData)
        : [...new Set(criticals.map(c => c.phpclass))];

    const lines = [];
    critKeys.forEach(phpClass => {
        let noOfCrits = 0;
        let paramTotal = 0; //sum of `param` across in-effect crits of this type (param-sum crits)
        let endEffectMin = 0;
        let endEffectMax = 0;
        let infinitePresent = false;

        criticals.forEach(crit => {
            if (crit.phpclass != phpClass) return;
            if (!((crit.turn <= gamedata.turn) /*check whether it's actually a current critical*/
                && ((crit.turnend == 0) || (crit.turnend >= gamedata.turn)))) return;
            noOfCrits++;
            paramTotal += parseInt(crit.param, 10) || 0;
            if (noOfCrits == 1) {
                endEffectMin = crit.turnend;
                endEffectMax = crit.turnend;
                infinitePresent = (crit.turnend == 0); //0 means infinite
            }
            if (crit.turnend > 0) {
                if (crit.turnend > endEffectMax) endEffectMax = crit.turnend;
                if ((crit.turnend < endEffectMin) || (endEffectMin == 0)) endEffectMin = crit.turnend;
            } else infinitePresent = true;
        });
        if (noOfCrits === 0) return;

        let until = "";
        if (endEffectMin > 0) {
            until = "(until end of turn " + endEffectMin;
            if (infinitePresent) {
                until = until + "+";
            } else if (endEffectMax > endEffectMin) {
                until = until + "-" + endEffectMax;
            }
            until = until + ")";
        }

        //Param-sum crits (e.g. DamageReductionReduced): one line showing the TOTAL reduction, no count.
        if (PARAM_SUM_CRIT_DESCRIPTIONS[phpClass]) {
            lines.push({ key: phpClass, count: 1, text: PARAM_SUM_CRIT_DESCRIPTIONS[phpClass](paramTotal), until });
            return;
        }
        lines.push({ key: phpClass, count: noOfCrits, text: critData[phpClass] || CRIT_DESCRIPTIONS[phpClass] || phpClass, until });
    });
    return lines;
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

//the old placement - still used by the ship-level tooltip; the system card uses helpers/placeTooltip
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
