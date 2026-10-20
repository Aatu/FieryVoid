import React, { Component } from 'react';
import styled from 'styled-components';
import theme from '../styled/theme';
import nonPassiveWheel from '../helpers/nonPassiveWheel';
import {
    MenuRow, MenuRowLabel, MenuLabelText,
    ActionButton, ValueInput, EnhSectionHeader, OfficerSectionHeader, SectionBody, SECTION_INK
} from './menuControls';

/* The gold half of "Add Enhancements & Damage" (WEAPON_ENHANCEMENTS_PLAN.md §6.1):
 * per-system refits for ONE system of a bought lobby ship.
 *
 *   ├ ✦ ENHANCEMENTS ──────────────────────────┤  bronze
 *   │ Gunsights            [-] [ 1 ] [+]   12p │
 *   │ Hardened Armour      [-] [ 0 ] [+]   18p │
 *   │                          Refits: 30 pts  │
 *   ├ OFFICERS ────────────────────────────────┤  the Buy dialog's Officers blue - placed officers
 *   │ Expert Gunner        [-] [ 1 ] [+]   93p │  (OFFICERS_PLAN.md Stage 8)
 *   │ [Move]                 Officers: 93 pts  │  Move: Stage 15, in the footer so no row changes shape
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
    padding: 1px 8px 5px 10px;
    font-family: ${theme.fonts.mono};
    font-size: 10px;
    color: ${props => props.$color || theme.colors.enhText};
    opacity: 0.85;
    user-select: none;
`;

/* OFFICERS_PLAN.md D10 / Stage 8: placed officers have a section of their own under the refits - bought
   here, on the weapon they will serve in, but not refits (no ✦, their own fleet-list line). Its bar is the
   Enhancements bar in the Buy dialog's Officers colours (SECTION_INK.officer, user request 2026-10-16), and
   its rows wear that ink, as every section's do. */
const OFFICER_TEXT = SECTION_INK.officer.btnText;

/* OFFICERS_PLAN.md Stage 15 - MOVE (user request 2026-10-17: "incorporated carefully so as not to ruin the format").
   It lives in the Officers section's FOOTER, the "Officers: n pts" line, which is there whenever an officer is: a
   system hosts one placed officer, so the footer's Move can only mean him, and the rows above keep their one control
   shape. The footer is the Total line made two-sided - Move on the left, the total where it always was. */
const OfficerFooter = styled(Total)`
    justify-content: space-between;
    align-items: center;
    gap: 8px;
    padding-top: 2px;
    opacity: 1;
`;

const FooterTotal = styled.span`
    margin-left: auto;
    opacity: 0.85;
`;

/* A text button in the section's ink, the ticker's height less its padding, so the footer grows by a few pixels at most. */
const MoveButton = styled.div`
    flex: 0 0 auto;
    height: 16px;
    padding: 0 8px;
    box-sizing: border-box;
    display: flex;
    align-items: center;
    background: ${SECTION_INK.officer.btnBg};
    border: 1px solid ${SECTION_INK.officer.rail};
    border-radius: 2px;
    color: ${SECTION_INK.officer.btnText};
    font-family: ${theme.fonts.mono};
    font-size: 10px;
    cursor: pointer;
    user-select: none;

    &:hover {
        background: ${SECTION_INK.officer.rail};
        color: #ffffff;
    }

    ${props => props.$disabled && `
        opacity: 0.35;
        cursor: not-allowed;
        &:hover {
            background: ${SECTION_INK.officer.btnBg};
            color: ${SECTION_INK.officer.btnText};
        }
    `}
`;

const Price = styled.span`
    flex: 0 0 auto;
    min-width: 34px;
    text-align: right;
    font-family: ${theme.fonts.mono};
    font-size: 10px;
    color: ${props => props.$color || theme.colors.enhTitle};
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
        if (row.fixed) return; //Stage 15: a C&C officer's row - his ticker is inert (the wheel too)
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
        //row.note: a refit that excludes others on this system says so here (systemEnhancements.GROUP_NOTES)
        const title = (row.max > 1
            ? `${row.label} - ${row.count}/${row.max} levels`
                + (row.count > 0 ? `, ${row.price} pts spent` : '')
                + (spent ? '' : `; next level ${row.nextPrice} pts`)
            : `${row.label} - ${row.price || row.nextPrice} pts`)
            + (row.note ? `\n${row.note}` : '')
            //a placed officer who may not be bought here now says why (officers.placedBlock); set() refuses it too
            + (row.blocked ? `\n${row.blocked}` : '');
        const blocked = Boolean(row.blocked) && row.count < 1;
        //a placed officer's row wears the Officers section's ink (blue), a refit's the gold
        const ink = row.officer ? SECTION_INK.officer : SECTION_INK.enh;
        /* Stage 15: the C&C officer bought in the Officers section (officers.decorateMenuRows) - the same row, its ticker
           inert: he is bought and removed in the buy dialog, and moved with the footer's Move. */
        const fixed = Boolean(row.fixed);
        const FIXED_TITLE = `${row.label} - bought with the ship, in its Officers section (Edit the ship to remove him). Move posts him elsewhere.`;

        return (
            <MenuRow $gold={!row.officer} title={fixed ? FIXED_TITLE : title} style={blocked ? { opacity: 0.55 } : undefined}>
                {/* ⚠️ The container's max-width is load-bearing (see ApplyDamageMenu), and
                    "Advanced Defensive Targeting" is the longest label in the game. It
                    ellipsises with the full text on hover rather than widening the menu. */}
                <MenuRowLabel>
                    <MenuLabelText>{row.label}</MenuLabelText>
                </MenuRowLabel>
                {/* The ticker wears this section's ink, like every other section's does - see
                    SECTION_INK. This is the old $gold variant restored as one case of a
                    general rule rather than as a one-off. */}
                <ActionButton
                    $ink={ink}
                    title={fixed ? FIXED_TITLE : "Remove a level"}
                    disabled={fixed || row.count <= 0}
                    onClick={() => { if (!fixed) this.step(-1); }}
                >&minus;</ActionButton>
                <ValueInput
                    ref={this.wheelRef}
                    $ink={ink}
                    type="text"
                    value={row.count}
                    readOnly={fixed}
                    onChange={e => { if (!fixed) this.onInput(e); }}
                />
                <ActionButton
                    $ink={ink}
                    title={fixed ? FIXED_TITLE : (blocked ? row.blocked
                        : (row.count >= row.max ? "Already at the maximum" : `Add a level (${row.nextPrice} pts)`))}
                    disabled={fixed || blocked || row.count >= row.max}
                    onClick={() => { if (!fixed) this.step(1); }}
                >+</ActionButton>
                <Price
                    $spent={spent}
                    $color={row.officer ? ink.title : undefined}
                    title={spent
                        ? `Fully bought - ${row.price} pts`
                        : `Cost of the next level`}
                >{spent ? `${row.price}p` : `${row.nextPrice}p`}</Price>
            </MenuRow>
        );
    }
}

class SystemEnhancementsSection extends Component {

    /* Stage 15: the footer's Move, for the officer posted here who may move (`moveTo` - officers.decorateMenuRows). Greyed,
       and saying why, when there is nowhere for him to go. */
    renderMove(mover) {
        const { onMove } = this.props;
        if (!mover || typeof onMove !== 'function') return null;
        const can = mover.moveTo.length > 0;
        const title = can
            ? `Move the ${mover.label} to another post: click Move, then a highlighted system in the ship window (free in Fleet Selection)`
            : `There is no other post the ${mover.label} can take on this ship right now`;
        return (
            <MoveButton $disabled={!can} title={title} onClick={e => { e.stopPropagation(); if (can) onMove(mover.enhID); }}>Move</MoveButton>
        );
    }

    render() {
        const { rows, onChange } = this.props;
        if (!rows || rows.length === 0) return null;

        //refits first, then the placed officers in a section of their own (OFFICERS_PLAN.md D10, Stage 8)
        const refits = rows.filter(row => !row.officer);
        const officers = rows.filter(row => row.officer);
        const spent = list => list.reduce((sum, row) => sum + (row.count > 0 ? row.price : 0), 0);
        const refitTotal = spent(refits);
        const officerTotal = spent(officers);
        //a system hosts one officer who holds a post, so one Move at most (Stage 15)
        const mover = officers.find(row => row.count > 0 && Array.isArray(row.moveTo));

        return (
            <React.Fragment>
                {refits.length > 0 && <EnhSectionHeader>✦ ENHANCEMENTS</EnhSectionHeader>}
                {refits.length > 0 && (
                    <SectionBody $ink={SECTION_INK.enh}>
                        {refits.map(row => (
                            <EnhancementRow key={row.enhID} row={row} onChange={onChange} />
                        ))}
                        {refitTotal > 0 && <Total>Refits: {refitTotal} pts</Total>}
                    </SectionBody>
                )}
                {officers.length > 0 && <OfficerSectionHeader>Officers</OfficerSectionHeader>}
                {officers.length > 0 && (
                    <SectionBody $ink={SECTION_INK.officer}>
                        {officers.map(row => (
                            <EnhancementRow key={row.enhID} row={row} onChange={onChange} />
                        ))}
                        {mover
                            ? (
                                <OfficerFooter $color={OFFICER_TEXT}>
                                    {this.renderMove(mover)}
                                    {officerTotal > 0 && <FooterTotal>Officers: {officerTotal} pts</FooterTotal>}
                                </OfficerFooter>
                            )
                            : officerTotal > 0 && <Total $color={OFFICER_TEXT}>Officers: {officerTotal} pts</Total>}
                    </SectionBody>
                )}
            </React.Fragment>
        );
    }
}

export default SystemEnhancementsSection;
