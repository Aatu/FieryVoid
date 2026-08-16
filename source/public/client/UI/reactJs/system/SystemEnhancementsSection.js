import React, { Component } from 'react';
import styled from 'styled-components';
import theme from '../styled/theme';
import nonPassiveWheel from '../helpers/nonPassiveWheel';
import {
    MenuRow, MenuRowLabel, MenuLabelText,
    ActionButton, ValueInput, EnhSectionHeader, SectionBody, SECTION_INK
} from './menuControls';

/* The gold half of "Add Enhancements & Damage" (WEAPON_ENHANCEMENTS_PLAN.md §6.1):
 * per-system refits for ONE system of a bought lobby ship.
 *
 *   ├ ✦ ENHANCEMENTS ──────────────────────────┤
 *   │ Gunsights            [-] [ 1 ] [+]   12p │
 *   │ Hardened Armour      [-] [ 0 ] [+]   18p │
 *   │                          Refits: 30 pts  │
 *
 * ⭐ This is a RENDERER, not a decision-maker. It takes `rows` already built by
 * systemEnhancements.menuRowsFor, so "what may this system have" stays in exactly one
 * place - and ultimately on the SERVER, which generates the offer list (D3). Its own file,
 * in the shape CriticalEffectsSection established, so it can be dropped into a future
 * flight or mine menu unchanged.
 *
 * Renders NOTHING when `rows` is empty, which is the common case: most systems on most
 * hulls, every system on an Ancient hull, and every destroyed system (D11).
 */

const Total = styled.div`
    display: flex;
    justify-content: flex-end;
    padding: 2px 8px 4px;
    font-size: 10px;
    color: ${theme.colors.enhText};
    opacity: 0.85;
    user-select: none;
`;

const Price = styled.span`
    flex: 0 0 auto;
    min-width: 34px;
    text-align: right;
    font-family: ${theme.fonts.mono};
    font-size: 10px;
    color: ${theme.colors.enhTitle};
    /*Nothing left to buy: the column has stopped quoting a price and is reporting a spend,
      so it stops looking like a price.*/
    opacity: ${props => props.$spent ? 0.6 : 1};
`;

/* One offer row. Its own component only so the wheel ref can be built once per row rather
   than rebuilt on every render of the parent - a ref callback whose identity changes each
   pass detaches and reattaches the native listener on every keystroke. */
class EnhancementRow extends Component {

    constructor(props) {
        super(props);
        //⚠️ preventDefault() inside React's onWheel is a NO-OP: React 18 attaches its wheel
        //listener passively, so the page scrolls behind the menu regardless. The shared
        //nonPassiveWheel ref helper attaches a real non-passive listener.
        this.wheelRef = nonPassiveWheel(e => this.step(e.deltaY < 0 ? 1 : -1));
    }

    step(direction) {
        const { row, onChange } = this.props;
        const next = Math.max(0, Math.min(row.max, row.count + direction));
        if (next !== row.count) onChange(row.enhID, next);
    }

    onInput(e) {
        const { row, onChange } = this.props;
        const digits = String(e.target.value).replace(/[^0-9]/g, '');
        const value = digits === '' ? 0 : parseInt(digits, 10);
        onChange(row.enhID, Math.max(0, Math.min(row.max, value)));
    }

    render() {
        const { row } = this.props;

        /* ⭐ The price column quotes WHAT THE NEXT LEVEL COSTS, always - never what has been
           spent so far (user request, 2026-08-15). It used to switch to the row's running
           total the moment the first level was bought, which on a multi-level refit reads as
           a price that lags a step behind: Improved Thrust Rating showed 12, 12, 14, 16 where
           the levels actually cost 12, 14, 16.

           The running total has a home already - "Refits: n pts" under the section, and the
           fleet points panel - so the per-row column is free to answer the only question
           being asked while the ticker is under the cursor: what does one more cost? At the
           limit there is no next level, so it falls back to the row's total spend, dimmed and
           labelled, rather than quoting a price that cannot be paid. */
        const spent = row.count >= row.max;

        //A single-level refit reads better as a count of 0/1 than as a spinner with one
        //stop, but keeping ONE control shape means the rows do not reflow as the player
        //clicks between systems - the same reasoning that keeps Destroy greyed rather than
        //removed in the damage half.
        const title = row.max > 1
            ? `${row.label} - ${row.count}/${row.max} levels`
                + (row.count > 0 ? `, ${row.price} pts spent` : '')
                + (spent ? '' : `; next level ${row.nextPrice} pts`)
            : `${row.label} - ${row.price || row.nextPrice} pts`;

        return (
            <MenuRow $gold title={title}>
                {/* ⚠️ The container's max-width is load-bearing (see ApplyDamageMenu), and
                    "Advanced Defensive Targeting" is the longest label in the game. It
                    ellipsises with the full text on hover rather than widening the menu. */}
                <MenuRowLabel>
                    <MenuLabelText>{row.label}</MenuLabelText>
                </MenuRowLabel>
                {/* No $gold on the ticker or the well any more - the ink paints the bar, the
                    rail, the label and the price, and never a control. See menuControls. */}
                <ActionButton
                    title="Remove a level"
                    disabled={row.count <= 0}
                    onClick={() => this.step(-1)}
                >&minus;</ActionButton>
                <ValueInput
                    ref={this.wheelRef}
                    type="text"
                    value={row.count}
                    onChange={e => this.onInput(e)}
                />
                <ActionButton
                    title={row.count >= row.max ? "Already at the maximum" : `Add a level (${row.nextPrice} pts)`}
                    disabled={row.count >= row.max}
                    onClick={() => this.step(1)}
                >+</ActionButton>
                <Price
                    $spent={spent}
                    title={spent
                        ? `Fully bought - ${row.price} pts`
                        : `Cost of the next level`}
                >{spent ? `${row.price}p` : `${row.nextPrice}p`}</Price>
            </MenuRow>
        );
    }
}

class SystemEnhancementsSection extends Component {

    render() {
        const { rows, onChange } = this.props;
        if (!rows || rows.length === 0) return null;

        const total = rows.reduce((sum, row) => sum + (row.count > 0 ? row.price : 0), 0);

        return (
            <React.Fragment>
                <EnhSectionHeader>✦ ENHANCEMENTS</EnhSectionHeader>
                <SectionBody $ink={SECTION_INK.enh}>
                    {rows.map(row => (
                        <EnhancementRow key={row.enhID} row={row} onChange={onChange} />
                    ))}
                    {total > 0 && <Total>Refits: {total} pts</Total>}
                </SectionBody>
            </React.Fragment>
        );
    }
}

export default SystemEnhancementsSection;
