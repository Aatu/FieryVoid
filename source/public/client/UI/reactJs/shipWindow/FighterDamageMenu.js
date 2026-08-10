import React, { Component } from 'react';
import styled from 'styled-components';
import theme from '../styled/theme';
import CriticalEffectsSection, { critRowsFromMap } from '../system/CriticalEffectsSection';
import nonPassiveWheel from '../helpers/nonPassiveWheel';

/* Pre-battle damage editor for a bought FLIGHT. Design: PREBATTLE_DAMAGE_PLAN.md §5.2.
 *
 * In the lobby a flight is ONE sample fighter plus a number - the N fighters are minted
 * server-side by populate() at buy time - so this menu is SYNTHETIC: it renders
 * `flightSize` rows from the single blueprint fighter, and each row reads and writes
 * ftr[<ordinal>] in the payload. Nothing here touches a fighter object.
 *
 *   ┌ Fighter Damage ──────────┐
 *   │ Fighter 1  [-][ 6 ][+]   │
 *   │ Fighter 2  [-][ 6 ][+]   │
 *   │  … up to flightSize      │
 *   │ [ Apply Fighter 1 to all]│
 *   ├ Critical Effects ────────┤
 *   │ Gun lost      [-][1][+]  │
 *   │ [ + Add effect… ] ☐ All  │
 *   └──────────────────────────┘
 *
 * There is deliberately NO Destroy control: a fighter is only ever DAMAGED here. A dead
 * fighter has no representation in B5W other than "not in the flight", and flight size is
 * already freely adjustable via the lobby's Edit action - so a smaller flight IS the way
 * to say "I lost two". Part 2's in-game save follows the same rule and records only the
 * surviving craft. Health therefore floors at 1.
 *
 * ⭐ ONE Critical Effects section for the whole flight (user request 2026-08-08), under
 * all the health rows, rather than one per fighter. A flight of six drew six section
 * headers, six pickers and six "All" switches for six craft that are identical by
 * construction. It addresses battleDamage.REF_FLIGHT: reading it unions what the ordinals
 * carry, writing it writes the same criticals to every ordinal. Per-fighter DAMAGE is
 * untouched - that is what the rows above are for.
 */

const Tooltip = styled.div`
    position: absolute;
    z-index: 20000;
    ${props => Object.keys(props.$position).reduce((style, key) => {
        return style + "\n" + key + ':' + props.$position[key] + 'px;';
    }, '')}
    /*the lobby mounts #systemInfoReact inside a pointer-events: none overlay*/
    pointer-events: auto;
    max-height: 70vh;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    min-width: 230px;
    /*Shrink-to-fit, so the critical picker's <select> would otherwise stretch this to its
      longest option the moment "All" is ticked - see the note on ApplyDamageMenu's
      Container. A max-width is what clamps the max-content contribution.*/
    max-width: 300px;
    box-sizing: border-box;
    opacity: 0.97;
    background-color: rgba(16, 26, 38, 0.95);
    border: 1px solid ${theme.colors.line};
`;

const Header = styled.div`
    padding: 3px;
    background-color: #215a7a;
    border-bottom: 1px solid ${theme.colors.line};
    color: ${theme.colors.chromeText};
    text-align: center;
    font-size: 12px;
    font-weight: bold;
    position: sticky;
    top: 0;
`;

const Caption = styled.div`
    text-align: center;
    font-size: 10px;
    padding: 2px 4px;
    color: ${theme.colors.textDim};
    user-select: none;
`;

const Row = styled.div`
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 3px 6px;
    font-size: 11px;
    color: ${theme.colors.chromeText};

    &:hover {
        background-color: rgba(73, 103, 145, 0.35);
    }
`;

const RowLabel = styled.div`
    flex: 1;
    min-width: 0;
    user-select: none;
`;

const MaxText = styled.span`
    flex: 0 0 auto;
    color: ${theme.colors.textDim};
    font-size: 10px;
    user-select: none;
`;

const ActionButton = styled.div`
    width: 24px;
    height: 18px;
    flex: 0 0 24px;
    background: #203348;
    border: 1px solid #496791;
    color: #deebff;
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 10px;
    opacity: 0.9;
    user-select: none;

    &:hover {
        background: #496791;
        color: #ffffff;
        opacity: 1;
    }

    ${props => props.disabled && `
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #203348; color: #deebff; }
    `}
`;

const ValueInput = styled.input`
    flex: 0 0 40px;
    width: 40px;
    height: 18px;
    box-sizing: border-box;
    padding: 0;
    text-align: center;
    font-family: ${theme.fonts.mono};
    font-size: 12px;
    color: #ffffff;
    background-color: #101a26;
    border: 1px solid #496791;
    outline: none;

    &:focus { border-color: #6089c1; }
`;

const PropagateButton = styled.div`
    margin: 4px 6px 6px 6px;
    height: 20px;
    background: #203348;
    border: 1px solid #496791;
    color: #deebff;
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 11px;
    user-select: none;

    &:hover { background: #496791; color: #ffffff; }
`;

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
        position.left = boundingBox.left;
    }

    return position;
};

class FighterDamageMenu extends Component {

    constructor(props) {
        super(props);
        //One ref callback per ordinal, cached: rebuilding them in render() would detach and
        //reattach the native listener on every keystroke. See helpers/nonPassiveWheel.
        this.wheelRefs = {};
    }

    wheelRef(ordinal) {
        if (!this.wheelRefs[ordinal]) {
            this.wheelRefs[ordinal] =
                nonPassiveWheel(e => this.step(ordinal, e.deltaY < 0 ? 1 : -1));
        }
        return this.wheelRefs[ordinal];
    }

    maxHealth() {
        return battleDamage.fighterMaxHealth(this.props.ship);
    }

    setRemaining(ordinal, value) {
        const { ship } = this.props;
        const max = this.maxHealth();

        let remaining = parseInt(value, 10);
        if (isNaN(remaining)) remaining = max;
        //Floors at 1: a fighter reduced to nothing is not in the flight at all, which is
        //said by lowering flightSize, not by a wreck with 0 structure.
        remaining = Math.max(1, Math.min(max, remaining));

        battleDamage.setFighter(ship, ordinal, { d: max - remaining });
        this.refresh();
    }

    step(ordinal, direction) {
        this.setRemaining(ordinal, battleDamage.fighterHealth(this.props.ship, ordinal) + direction);
    }

    onInput(ordinal, e) {
        const digits = String(e.target.value).replace(/[^0-9]/g, '');
        this.setRemaining(ordinal, digits === '' ? 0 : parseInt(digits, 10));
    }

    /* Copy fighter 1's DAMAGE to every other ordinal - and only its damage.
       This used to copy the whole entry, criticals included, back when each fighter had
       its own crit section. Criticals are now authored for the whole flight at once
       (REF_FLIGHT), so carrying them here would do nothing at best and, on a fleet loaded
       from a battle where only the other craft were crit, would silently wipe them. */
    propagate() {
        const { ship } = this.props;
        const size = parseInt(ship.flightSize, 10) || 0;
        const damage = battleDamage.fighterMaxHealth(ship) - battleDamage.fighterHealth(ship, 1);

        for (let ordinal = 2; ordinal <= size; ordinal++) {
            battleDamage.setFighter(ship, ordinal, { d: damage });
        }
        this.refresh();
    }

    refresh() {
        const { ship } = this.props;
        battleDamage.applyToShip(ship);
        if (window.shipWindowManagerReact) window.shipWindowManagerReact.update();
        //Lobby only: keep the fleet-list row's broken-heart badge in step as we edit.
        if (window.gamedata && typeof gamedata.refreshDamagedBadge === 'function') {
            gamedata.refreshDamagedBadge(ship);
        }
        this.forceUpdate();
    }

    render() {
        const { ship, boundingBox } = this.props;
        const size = parseInt(ship && ship.flightSize, 10) || 0;
        const max = this.maxHealth();

        if (!size || !max) return null;

        const summary = battleDamage.flightSummary(ship);
        const rows = [];

        for (let ordinal = 1; ordinal <= size; ordinal++) {
            const remaining = battleDamage.fighterHealth(ship, ordinal);

            rows.push(
                <Row key={`ftr-${ordinal}`}>
                    <RowLabel>Fighter {ordinal}</RowLabel>
                    <ActionButton
                        title="More damage"
                        disabled={remaining <= 1}
                        onClick={() => this.step(ordinal, -1)}
                    >&minus;</ActionButton>
                    {/*ref, not onWheel: React's own wheel listener is passive, so
                       preventDefault there cannot stop the page scrolling behind it.*/}
                    <ValueInput
                        ref={this.wheelRef(ordinal)}
                        type="text"
                        value={remaining}
                        onChange={e => this.onInput(ordinal, e)}
                    />
                    <ActionButton
                        title="Repair"
                        disabled={remaining >= max}
                        onClick={() => this.step(ordinal, 1)}
                    >+</ActionButton>
                    <MaxText>/ {max}</MaxText>
                </Row>
            );
        }

        /* ONE section for the flight, below every health row. REF_FLIGHT unions what the
           ordinals carry on the way in and writes to all of them on the way out. */
        const flightCrits = battleDamage.flightCritEntry(ship) || {};
        const critRows = critRowsFromMap(
            flightCrits.c, ship.preBattleCritDesc, ship.preBattleCritTransient, flightCrits.p);

        return (
            <Tooltip $position={getPosition(boundingBox)} onClick={e => e.stopPropagation()}>
                <Header>Fighter Damage</Header>

                {rows}
                {size > 1 && <PropagateButton
                    title="Copy Fighter 1's damage to every fighter in this flight"
                    onClick={() => this.propagate()}
                >Apply Fighter 1's damage to all</PropagateButton>}
                <CriticalEffectsSection
                    ship={ship}
                    kind={battleDamage.KIND_FIGHTER}
                    reference={battleDamage.REF_FLIGHT}
                    rows={critRows}
                    editable
                    onChange={() => this.refresh()}
                />
            </Tooltip>
        );
    }
}

export default FighterDamageMenu;
