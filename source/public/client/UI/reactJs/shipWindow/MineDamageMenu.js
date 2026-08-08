import React, { Component } from 'react';
import styled from 'styled-components';
import theme from '../styled/theme';
import nonPassiveWheel from '../helpers/nonPassiveWheel';

/* Pre-battle damage editor for a BULK MINE PURCHASE. Sibling of FighterDamageMenu, and
 * deliberately the same shape: a bulk buy is ONE lobby object carrying bulkBuy = N, minted
 * into N separate ships server-side, exactly as a flight is one sample fighter plus a
 * number. So this menu is SYNTHETIC too - it renders `bulkBuy` rows from the one blueprint
 * and each row reads and writes mne[<ordinal>].
 *
 *   ┌ Mine Damage ─────────────┐
 *   │ Mine 1  [-][ 8 ][+] / 10 │
 *   │ Mine 2  [-][10 ][+] / 10 │
 *   │  … up to bulkBuy         │
 *   │ [ Apply Mine 1 to all ]  │
 *   └──────────────────────────┘
 *
 * STRUCTURE ONLY (user decision 2026-08-08): a mine's other systems are all untargetable,
 * and a mine cannot carry a critical effect worth taking into the next battle - so there
 * is no per-system menu and no Critical Effects section here, only the one number the
 * unit actually has. Like a fighter it is DAMAGED, NEVER DESTROYED: health floors at 1,
 * and a mine you lost is expressed by buying one fewer.
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
    min-width: 210px;
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

class MineDamageMenu extends Component {

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
        return battleDamage.mineMaxHealth(this.props.ship);
    }

    setRemaining(ordinal, value) {
        const { ship } = this.props;
        const max = this.maxHealth();

        let remaining = parseInt(value, 10);
        if (isNaN(remaining)) remaining = max;
        //Floors at 1: a mine reduced to nothing is one you did not buy.
        remaining = Math.max(1, Math.min(max, remaining));

        battleDamage.setMine(ship, ordinal, { d: max - remaining });
        this.refresh();
    }

    step(ordinal, direction) {
        this.setRemaining(ordinal, battleDamage.mineHealth(this.props.ship, ordinal) + direction);
    }

    onInput(ordinal, e) {
        const digits = String(e.target.value).replace(/[^0-9]/g, '');
        this.setRemaining(ordinal, digits === '' ? 0 : parseInt(digits, 10));
    }

    propagate() {
        const { ship } = this.props;
        const count = battleDamage.mineCount(ship);
        const source = battleDamage.getEntry(ship, battleDamage.KIND_MINE, 1);

        for (let ordinal = 2; ordinal <= count; ordinal++) {
            battleDamage.setWholeEntry(ship, battleDamage.KIND_MINE, ordinal, source);
        }
        this.refresh();
    }

    refresh() {
        const { ship } = this.props;
        battleDamage.applyToShip(ship);
        if (window.shipWindowManagerReact) window.shipWindowManagerReact.update();
        //Keep the fleet-list row's broken-heart badge in step as we edit.
        if (window.gamedata && typeof gamedata.refreshDamagedBadge === 'function') {
            gamedata.refreshDamagedBadge(ship);
        }
        this.forceUpdate();
    }

    render() {
        const { ship, boundingBox } = this.props;
        const count = battleDamage.mineCount(ship);
        const max = this.maxHealth();

        //A mine whose structure is 1 (most proximity mines) has nothing to dial: any
        //damage at all would destroy it, and a destroyed mine is one you did not buy.
        if (!count || max < 2) return null;

        const summary = battleDamage.mineSummary(ship);
        const rows = [];

        for (let ordinal = 1; ordinal <= count; ordinal++) {
            const remaining = battleDamage.mineHealth(ship, ordinal);

            rows.push(
                <Row key={`mne-${ordinal}`}>
                    <RowLabel>Mine {ordinal}</RowLabel>
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

        return (
            <Tooltip $position={getPosition(boundingBox)} onClick={e => e.stopPropagation()}>
                <Header>Mine Damage</Header>
                <Caption>{summary.remaining} / {summary.total} structure</Caption>
                {rows}
                {count > 1 && <PropagateButton
                    title="Copy Mine 1's damage to every mine in this purchase"
                    onClick={() => this.propagate()}
                >Apply Mine 1 to all</PropagateButton>}
            </Tooltip>
        );
    }
}

export default MineDamageMenu;
