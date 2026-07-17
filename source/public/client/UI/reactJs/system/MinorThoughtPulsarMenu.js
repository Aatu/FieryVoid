import React, { Component } from 'react';
import styled from 'styled-components';

/* Free thrust-allocation menu for the Minor Thought Pulsar (Mindrider fighter weapon).
 *
 * Replaces the old firing-mode presets (RoF / Damage / Hitchance / Combo) that auto-distributed
 * spare movement thrust. The player now freely allocates the flight's spare thrust (== remaining
 * engine thrust after movement) across three effects, each step costing THRUST_PER_STEP (3):
 *   Hit Chance : +5 shown per step  => +2 OB (~+10% to hit) applied server-side.
 *   Shots      : +1 shot per step.
 *   Damage     : +5 damage per step.
 * "Available thrust" under the header decreases by 3 per allocated step.
 *
 * The allocation is stored on the weapon (hitBoost5 / shotBoost / dmgBoost5) and encoded into the
 * normal fire order's notes as "MTP|<hit5>|<shots>|<dmg5>" (see MinorThoughtPulsar.getBoostNotes),
 * which the server parses in beforeFiringOrderResolution. Propagate copies the allocation to every
 * Minor Thought Pulsar in the same flight.
 */

const STEP = 3; //thrust per allocation step (mirrors MinorThoughtPulsar.THRUST_PER_STEP)

const Container = styled.div`
    display: flex;
    flex-direction: column;
    margin-top: 5px;
    width: 100%;
    min-width: 200px;
    box-sizing: border-box;
    opacity: 0.95;
    background-color: rgba(32, 0, 32, 0.9);
    border: 1px solid #b43131;
`;

const Header = styled.div`
    padding: 3px;
    background-color: #180606;
    border: 1px solid #b43131;
    color: #f2f2f2;
    text-align: center;
    font-size: 12px;
    margin-bottom: 2px;
    opacity: 1 !important;
    font-weight: bold;
`;

const ThrustLine = styled.div`
    text-align: center;
    color: ${props => props.$empty ? '#f0a0a0' : '#f2f2f2'};
    font-size: 10px;
    padding: 2px 4px 3px 4px;
    user-select: none;
`;

const Row = styled.div`
    display: flex;
    align-items: center;
    gap: 4px;
    width: 100%;
    box-sizing: border-box;
    padding: 3px 5px;
`;

const RowLabel = styled.div`
    flex: 1;
    min-width: 0;
    font-size: 11px;
    color: #f2f2f2;
    user-select: none;
`;

const StepButton = styled.div`
    width: 20px;
    height: 20px;
    flex: 0 0 20px;
    background: #683333;
    border: 1px solid #641b1b;
    color: #f2f2f2;
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 13px;
    font-weight: bold;
    user-select: none;

    &:hover {
        background: #854242;
        border: 1px solid #b43131;
        color: #ffffff;
    }

    ${props => props.disabled && `
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #683333; border: 1px solid #641b1b; color: #f2f2f2; }
    `}
`;

const ValueInput = styled.input`
    flex: 0 0 48px;
    width: 60px;
    height: 20px;
    box-sizing: border-box;
    padding: 0;
    text-align: center;
    line-height: 20px;
    font-family: "Segoe UI", "Helvetica Neue", Arial, sans-serif;
    font-size: 13px;
    font-weight: 600;
    letter-spacing: 0.5px;
    color: #ffffff;
    background-color: #200014;
    border: 1px solid #641b1b;
    outline: none;

    &:focus {
        border-color: #b43131;
        box-shadow: 0 0 5px rgba(180, 49, 49, 0.6);
    }
`;

const PropagateButton = styled.div`
    margin: 3px 5px 5px 5px;
    height: 20px;
    background: #683333;
    border: 1px solid #641b1b;
    color: #f2f2f2;
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 11px;
    user-select: none;

    &:hover {
        background: #854242;
        border: 1px solid #b43131;
        color: #ffffff;
    }
`;

/* Row descriptor. `key` is the weapon field it drives; `increment` is the STORED-value step
 * (5 for hit/damage, 1 for shots). `display` maps the stored value to the number shown in the
 * input, and `parse` maps a typed number back to a stored value — the Hit Chance row shows the
 * real ~% to-hit gain (each 3-thrust step = +2 OB = +10%), so it shows stored*2 and parses /2.
 * `prefix`/`suffix` decorate the shown number (e.g. "+10%"). */
const ROWS = [
    { key: 'hitBoost5', label: 'Hit Chance', increment: 5, prefix: '+', suffix: '%',
      display: v => v * 2, parse: n => n / 2 },
    { key: 'shotBoost', label: 'Shots', increment: 1, prefix: '+', suffix: '',
      display: v => v, parse: n => n },
    { key: 'dmgBoost5', label: 'Damage', increment: 5, prefix: '+', suffix: '',
      display: v => v, parse: n => n },
];

//The text shown inside a row's input field, e.g. "+10%", "+1", "+5".
const displayText = (row, storedValue) => row.prefix + row.display(storedValue | 0) + row.suffix;

class MinorThoughtPulsarMenu extends Component {

    //Steps a given field's display value consumes (hit/damage are in 5s => value/increment steps).
    fieldSteps(row, value) {
        return (value | 0) / row.increment;
    }

    //Total steps currently allocated across all three rows on THIS weapon.
    allocatedSteps(system) {
        return ROWS.reduce((sum, row) => sum + this.fieldSteps(row, system[row.key]), 0);
    }

    //Whole steps the flight's spare thrust can pay for (floor(spare / 3)).
    maxSteps() {
        const { ship, system } = this.props;
        if (typeof system.getMaxSteps === 'function') return system.getMaxSteps(ship);
        return Math.floor(shipManager.movement.getRemainingEngineThrust(ship) / STEP);
    }

    //Total shots this weapon will fire this turn = base guns + allocated extra shots. Each damage
    //step boosts ONE distinct shot by +5 (not cumulative), so damage steps can't exceed this.
    totalShots(system) {
        return (system.guns | 0) + (system.shotBoost | 0);
    }

    //Cap on damage steps: no more than the number of shots fired (one +5 per shot, not cumulative).
    maxDamageSteps(system) {
        return this.totalShots(system);
    }

    //After the Shots allocation changes, trim Damage so its steps never exceed the shot count
    //(refunding the freed thrust). Keeps the "single shot +5" invariant when shots are reduced.
    clampDamageToShots(system) {
        const dmgRow = ROWS.find(r => r.key === 'dmgBoost5');
        const maxDmg = this.maxDamageSteps(system) * dmgRow.increment;
        if ((system.dmgBoost5 | 0) > maxDmg) system.dmgBoost5 = maxDmg;
    }

    refresh() {
        const { ship, system } = this.props;
        this.forceUpdate();
        webglScene.customEvent('SystemDataChanged', { ship, system });
    }

    //Write a row's new display value onto the weapon, honouring the increment + thrust budget,
    //then sync the live order's notes so the submitted allocation matches the menu.
    setValue(row, rawValue) {
        const { ship, system } = this.props;
        if (!gamedata.isMyShip(ship)) return;

        //Snap to the row's increment (5 for hit/damage, 1 for shots) and never negative.
        let value = Math.max(0, Math.round((rawValue | 0) / row.increment) * row.increment);

        //Clamp to what the remaining thrust budget can afford: steps used by the OTHER two rows
        //plus this row's new steps must not exceed maxSteps.
        const otherSteps = this.allocatedSteps(system) - this.fieldSteps(row, system[row.key]);
        const affordableSteps = Math.max(0, this.maxSteps() - otherSteps);
        const requestedSteps = value / row.increment;
        if (requestedSteps > affordableSteps) value = affordableSteps * row.increment;

        //Damage boosts one shot each (+5, not cumulative), so damage steps can't exceed shots fired.
        if (row.key === 'dmgBoost5') {
            const maxDmg = this.maxDamageSteps(system) * row.increment;
            if (value > maxDmg) value = maxDmg;
        }

        system[row.key] = value;

        //Lowering Shots may strand Damage above the new shot count — auto-trim it back (refunding
        //that thrust) so the "single shot +5" cap always holds.
        if (row.key === 'shotBoost') this.clampDamageToShots(system);

        if (typeof system.updateBoostNotes === 'function') system.updateBoostNotes();

        //The allocation is chosen at the FLIGHT level (the whole flight shares one 16-thrust pool
        //and moves as a unit), so mirror this weapon's settings to every Minor Thought Pulsar in
        //the flight automatically. (Replaces the manual Propagate button, which is now hidden.)
        this.syncFlight(system);

        this.refresh();
    }

    //Copy `source`'s allocation to every OTHER Minor Thought Pulsar in the flight + refresh their
    //notes. All pulsars in a flight share the same spare-thrust budget, so no per-weapon re-clamp
    //is needed — they all fit the same budget the source was just clamped to.
    syncFlight(source) {
        const pulsars = this.getFlightPulsars();
        for (let i = 0; i < pulsars.length; i++) {
            const p = pulsars[i];
            if (p === source) continue;
            ROWS.forEach(row => { p[row.key] = source[row.key] | 0; });
            if (typeof p.updateBoostNotes === 'function') p.updateBoostNotes();
        }
    }

    step(row, direction) {
        const { system } = this.props;
        this.setValue(row, (system[row.key] | 0) + direction * row.increment);
    }

    onWheel(row, e) {
        e.preventDefault();
        this.step(row, e.deltaY < 0 ? 1 : -1); //wheel up = increase
    }

    onInput(row, e) {
        //Allow free text entry; treat blank / non-numeric as 0. The typed number is in DISPLAY
        //units (e.g. the Hit row shows +10%), so convert back to a stored value via row.parse
        //before setValue snaps it to the stored increment + clamps to the thrust budget.
        const digits = String(e.target.value).replace(/[^0-9]/g, '');
        const shown = digits === '' ? 0 : parseInt(digits, 10);
        this.setValue(row, row.parse(shown));
    }

    //Copy this weapon's allocation to every Minor Thought Pulsar in the SAME flight, clamped to
    //each one's own affordable budget (they share the flight's spare thrust, so it's the same).
    propagate() {
        const { ship, system } = this.props;
        if (!gamedata.isMyShip(ship)) return;

        const pulsars = this.getFlightPulsars();
        for (let i = 0; i < pulsars.length; i++) {
            const p = pulsars[i];
            if (p === system) continue;
            ROWS.forEach(row => { p[row.key] = system[row.key] | 0; });
            //Re-clamp each target to its own budget (defensive; budget is flight-wide so usually equal).
            this.clampWeaponToBudget(p);
            if (typeof p.updateBoostNotes === 'function') p.updateBoostNotes();
        }
        this.refresh();
    }

    //All Minor Thought Pulsars in the clicked flight (ship == flight, its systems are the fighters).
    getFlightPulsars() {
        const { ship } = this.props;
        const found = [];
        const fighters = (ship && ship.systems) ? ship.systems : [];
        for (let i = 0; i < fighters.length; i++) {
            const weapons = (fighters[i] && fighters[i].systems) ? fighters[i].systems : [];
            for (let j = 0; j < weapons.length; j++) {
                if (weapons[j] && weapons[j].name === 'MinorThoughtPulsar') found.push(weapons[j]);
            }
        }
        return found;
    }

    //Trim a weapon's allocation down (damage first, then shots, then hit chance) until it fits budget.
    clampWeaponToBudget(weapon) {
        const order = ['dmgBoost5', 'shotBoost', 'hitBoost5'];
        const rowFor = key => ROWS.find(r => r.key === key);
        let guard = 0;
        while (guard++ < 200) {
            const used = ROWS.reduce((s, r) => s + ((weapon[r.key] | 0) / r.increment), 0);
            if (used <= this.maxSteps()) break;
            for (let i = 0; i < order.length; i++) {
                const key = order[i];
                if ((weapon[key] | 0) > 0) { weapon[key] = (weapon[key] | 0) - rowFor(key).increment; break; }
            }
        }
    }

    render() {
        const { ship, system } = this.props;
        if (!system) return null;

        const spare = (typeof system.getSpareThrust === 'function')
            ? system.getSpareThrust(ship)
            : shipManager.movement.getRemainingEngineThrust(ship);
        const usedThrust = this.allocatedSteps(system) * STEP;
        const available = Math.max(0, spare - usedThrust);
        const canAllocateMore = available >= STEP;

        return (
            <Container>
                <Header>Minor Thought Pulsar</Header>
                <ThrustLine $empty={available < STEP}>Available thrust: {available}</ThrustLine>

                {ROWS.map(row => {
                    const value = system[row.key] | 0;
                    //Damage is additionally capped at the number of shots fired (one +5 per shot).
                    const atDamageCap = row.key === 'dmgBoost5' &&
                        (value / row.increment) >= this.maxDamageSteps(system);
                    return (
                        <Row key={row.key}>
                            <RowLabel>{row.label}</RowLabel>
                            <StepButton
                                title="Less"
                                disabled={value <= 0}
                                onClick={() => this.step(row, -1)}
                            >&minus;</StepButton>
                            <ValueInput
                                type="text"
                                value={displayText(row, value)}
                                onChange={e => this.onInput(row, e)}
                                onWheel={e => this.onWheel(row, e)}
                            />
                            <StepButton
                                title={atDamageCap ? "One +5 per shot (add shots for more)" : "More"}
                                disabled={!canAllocateMore || atDamageCap}
                                onClick={() => this.step(row, 1)}
                            >+</StepButton>
                        </Row>
                    );
                })}

                {/* Propagate button hidden: the allocation is now flight-level and auto-propagates
                    on every change (see syncFlight in setValue). Kept for reference in case per-weapon
                    allocation with a manual propagate is wanted again.
                <PropagateButton
                    title="Copy this allocation to every Minor Thought Pulsar in this flight"
                    onClick={() => this.propagate()}
                >Propagate to flight</PropagateButton>
                */}
            </Container>
        );
    }
}

export default MinorThoughtPulsarMenu;
