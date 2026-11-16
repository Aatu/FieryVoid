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
    /* The Energy Draining Field's (user, 2026-11-16), worded exactly as their PHP constructors word them when they are
       given the param (cricialClasses.php) - the text a reload loses. Each param is the RUNNING total for the turn
       (EdfExposure::accumulate), and only one crit of a class is in force on a turn, so the "sum" is that total. */
    EdfThrustDrain: (total) => `Engine output drained by ${total}`,
    EdfPowerDrain: (total) => `Reactor output drained by ${total}`,
    EdfIniDrain: (total) => `Initiative reduced by ${total * 5}`,
    EdfEwDrain: (total) => `EW capacity drained by ${total}`,
    EdfExposed: (total) => total > 1 ? `Caught in an Energy Draining Field (${total} turns running)` : 'Caught in an Energy Draining Field',
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
        const stormOb = (ship.flight && window.emStorm && emStorm.isActive()) ? emStorm.offensivePenalty(ship) : 0;
        if (stormOb) displayOffensiveBonus = Math.max(0, displayOffensiveBonus - stormOb);
        /* ...and a tmpsensordown on the flight's first craft, -1 OB (-5) each, as the shot takes it (weaponManager's OBcrit,
           Weapon::calculateHitBase). User request 2026-11-16: the OB cell is coloured when a crit changes it, and it must then
           show the number the shot uses - it used to leave this one out. */
        const sensorDownOb = ship.flight ? sensorDownObPenalty(ship) : 0;
        if (sensorDownOb) displayOffensiveBonus = Math.max(0, displayOffensiveBonus - sensorDownOb);
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
        /* A fighter weapon's OB is the FLIGHT's, and so are the crits that lower it - parked on its first craft, where the
           server reads them - so its card lists those too, under the weapon's own (user request 2026-11-16): the orange OB
           cell then says why on the same card. */
        if (ship.flight) {
            const sample = shipManager.criticals.getSampleCraft(ship);
            if (sample && sample !== system) {
                critLines(sample, crit => FLIGHT_OB_CRITS.indexOf(crit.phpclass) !== -1)
                    .forEach(line => crits.push(Object.assign({}, line, { key: 'flight-' + line.key })));
            }
        }

        /* A bulk mine purchase in the lobby keeps its pre-battle damage per copy, outside the blueprint's own damage, and its
           section bar shows MINE 1 (ShipSection) - the card reads the same numbers, so the two agree. */
        const bulkMine = isStructure && canApplyMineDamage(ship);
        const structureMax = bulkMine ? battleDamage.mineMaxHealth(ship) : system.maxhealth;
        const structureLeft = bulkMine ? battleDamage.mineHealth(ship, 1) : system.maxhealth - damageManager.getDamage(ship, system);

        //the stats a critical in force has changed - drawn in the critical orange (see critStats)
        const critHit = critStats(system, has);
        if (stormOb || sensorDownOb) critHit.add('OB');

        const arcCell ={ label: 'Arc', value: has('Arc') ? value('Arc') : '—', crit: critHit.has('Arc') };
        const armorCell = { label: 'Armor', value: shipManager.systems.getArmour(ship, system), crit: critHit.has('Armor') };
        /* Engines, reactors and scanners have no arc, and their output is the number that matters (user request
           2026-11-16): Output takes the Arc cell - getOutput's figure, so crits, drains, boosts and an offline or
           destroyed system all show in it, and it goes orange when a critical lowered it (critStats). */
        const thirdCell = OUTPUT_TYPES.has(system.outputType)
            ? { label: 'Output', value: shipManager.systems.getOutput(ship, system), crit: critHit.has('Output') }
            : arcCell;
        const cells = isStructure
            ? [armorCell, arcCell] //a block draws no power
            : [
                ship.flight
                    ? { label: 'OB', value: adjustObDisplay(system, displayOffensiveBonus * 5), crit: critHit.has('OB') }
                    : armorCell,
                { label: 'Power', value: has('Power Used') ? value('Power Used') : '—', crit: critHit.has('Power') },
                thirdCell
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
        if (hidesAmmoModes(ship, system) && modeKeys.length > 0) {
            //drawn even when the rack holds one type, so whether the row is there says nothing about how many it holds
            const firstKey = system.firingModes[1] !== undefined ? '1' : modeKeys[0];
            rows.push({ label: 'Firing mode', modes: [{ name: system.firingModes[firstKey], active: true }] });
        } else if (modeKeys.length > 1) {
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
        rows.forEach(row => { if (critHit.has(row.label)) row.crit = true; });

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

/* A magazine-fed weapon's firing modes ARE the ordnance its magazine holds (AmmoMissileRackS / ammoWeapons recompileFiringModes list
   only the types aboard), and those types are secret from other teams (AmmoMagazine::stripForJson). The payload still carries every
   mode, so the card lists only mode 1 to a viewer outside the ship's team (user request 2026-11-16). The card's damage, range and fire
   control already read mode 1 for them: nothing on the client ever switches an enemy weapon's mode.
   game.php only - gamedata.isMyorMyTeamShip is not defined in the lobby, which shows no other team's ships. */
const hidesAmmoModes = (ship, system) => system.checkAmmoMagazine === true
    && typeof gamedata.isMyorMyTeamShip === 'function' && !gamedata.isMyorMyTeamShip(ship);

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

/* WHICH CARD STAT EACH CRITICAL CHANGES (user request 2026-11-16): the card draws that stat's value in the critical
   orange, so the player sees what was affected. The values themselves already carry the effect - the server builds a
   weapon's data AFTER its effectCriticals, getArmour subtracts ArmorReduced - except where the effect is applied at
   fire time (the Antimatter pair: the damage formula and the effective distance), which is still a changed stat.
   Keys are the CARD's labels (cells: Armor, Power, Arc; rows: as SystemInfo pushes them). A stat the card does not
   show for this system is simply not coloured; the Criticals block lists every effect regardless. */
const CRIT_STATS = {
    ArmorReduced: ['Armor'],
    EdnPowerDoubled: ['Power'],
    ReducedArcs: ['Arc'],
    ReducedDamage: ['Damage'],
    ReducedDamageAntimatter: ['Damage'],
    DiceLost: ['Damage'],
    ReducedRangeAntimatter: ['Range penalty'],
    ReducedRangeValue: ['Range'],
    GunLost: ['Guns'],
};
/* Output crits, the one-turn drains and a crit-forced shutdown (getOutput answers 0 for an offline system) - on the
   Output cell of an engine / reactor / scanner, and on the few other systems whose card shows an output figure. */
const OUTPUT_CRIT = /^(Output(Halved|Reduced)\w*|EdfThrustDrain|EdfPowerDrain|EdfEwDrain|EMStormSensors|EMStormPower|ForcedOffline(OneTurn|ForTurns))$/;
const OUTPUT_STATS = ['Output', 'Current Output ', 'Shield rating', 'Own thrust'];
//the systems whose card shows Output in place of Arc: reactors and sub-reactors, engines, scanners (their outputType)
const OUTPUT_TYPES = new Set(['power', 'thrust', 'EW']);
//the flight crits that lower a fighter weapon's OB (weaponManager's OBcrit, Weapon::calculateHitBase)
const FLIGHT_OB_CRITS = ['EMStormOffensive', 'tmpsensordown'];

const critStats = (system, has) => {
    const stats = new Set();
    shipManager.criticals.getAllCriticals(system).forEach(crit => {
        let keys = CRIT_STATS[crit.phpclass] || (OUTPUT_CRIT.test(crit.phpclass) ? OUTPUT_STATS : null);
        /* Weapon::effectCriticals: a weapon WITH a range penalty gets a steeper one; one without loses 20% of its
           range instead (a ballistic, a Plasma Wave). The card shows both rows when both exist. */
        if (crit.phpclass === 'ReducedRange') keys = [has('Range penalty') ? 'Range penalty' : 'Range'];
        (keys || []).forEach(key => stats.add(key));
    });
    return stats;
};

//a flight's tmpsensordown crits (on its first craft, where the server reads them): one OB point each. Exported for
//ShipInfo's fighter-card hover, whose "Offensive bonus" line takes it off the same way.
export const sensorDownObPenalty = (flight) => {
    const sample = shipManager.criticals.getSampleCraft(flight);
    return sample ? shipManager.criticals.hasCritical(sample, 'tmpsensordown') : 0;
};

/* The criticals in effect on this system, one line per critical class: { key, count, text, until }. Was getCriticals,
   which drew them itself; the turn and param arithmetic is unchanged. A class with no crit in effect gives no line, so
   the card shows no empty "Criticals" block (the old one drew the heading anyway).
   `keep` (optional): only the crits it passes are counted - ShipInfo's fighter-card hover uses it to leave out the
   craft-state markers the card draws as overlays. Exported for that hover, so both say an effect in the same words. */
export const critLines = (system, keep) => {
    const critData = system.critData || {};
    const allCriticals = Array.isArray(system.criticals) ? system.criticals : Object.values(system.criticals || {});
    const criticals = keep ? allCriticals.filter(keep) : allCriticals;
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
