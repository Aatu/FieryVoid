import React, { Component } from 'react';
import styled from 'styled-components';
import theme from '../styled/theme';
import nonPassiveWheel from '../helpers/nonPassiveWheel';

/* Critical effects carried by ONE damage target — a system (kind 0, ref = systemid) or
 * a fighter ordinal (kind 1, ref = 1..flightSize). Rendered inside ApplyDamageMenu and
 * FighterDamageMenu, below the damage row. See PREBATTLE_DAMAGE_PLAN.md §5.2.
 *
 * `editable` (2026-08-08) lets a player AMEND or REMOVE the criticals a loaded fleet
 * brought with it: a [-][N][+] ticker on each row plus a × to drop it entirely. Every
 * change goes through battleDamage.setCriticals, which is the only way criticals ever
 * enter or leave a payload client-side, so the preview, the buy POST and the saved-fleet
 * write all follow with no extra wiring.
 *
 * A row DROPPED TO ZERO STAYS ON SCREEN, empty, so it can be put back — removing a
 * critical is an edit like any other, and an edit you can only undo by reloading the
 * whole fleet is a trap. The list of classes to keep drawing lives on the SHIP
 * (battleDamage.critMemory), not on this component, so closing and reopening the menu
 * does not quietly lose the row.
 *
 * PARAM-CARRYING criticals (battleDamage.PARAM_CRITICALS — DamageReductionReduced today)
 * keep their magnitude in the crit's param rather than in a count, so their ticker edits
 * the PARAM and the label loses its trailing number: "Damage reduction reduced by [8]".
 *
 * Still no ADD (plan §11): the rows are what the fleet is carrying, and inventing a
 * critical from nothing needs the per-class catalogue endpoint §11.2 specifies. Removing
 * and reducing what a battle actually produced needs none of that — which is why this
 * half ships and that half does not.
 */

const Section = styled.div`
    display: flex;
    flex-direction: column;
    border-top: 1px solid ${theme.colors.line};
`;

const SectionHeader = styled.div`
    padding: 3px;
    background-color: #1b3b50;
    color: ${theme.colors.chromeText};
    text-align: center;
    font-size: 10px;
    letter-spacing: 0.5px;
    user-select: none;
`;

const CritRow = styled.div`
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    padding: 2px 8px;
    font-size: 11px;
    color: ${theme.colors.warning};
    user-select: none;

    /* An effect dialled down to nothing is not carried any more, but its row stays so it
       can be put back — dimmed so it never reads as an active critical. */
    ${props => props.$empty && `
        color: #6f6257;
    `}
`;

const CritLabel = styled.div`
    flex: 1;
    min-width: 0;
`;

/* A one-turn effect is carried on a different promise from a lasting wound — it will be
   in effect during turn 1 of the next battle and then gone — so it says so rather than
   sitting in the list looking permanent. */
const TransientTag = styled.span`
    margin-left: 4px;
    font-size: 9px;
    letter-spacing: 0.3px;
    color: ${theme.colors.textDim};
`;

const CritCount = styled.div`
    flex: 0 0 auto;
    color: ${theme.colors.textDim};
`;

const Controls = styled.div`
    display: flex;
    align-items: center;
    gap: 4px;
    flex: 0 0 auto;
`;

const TickerButton = styled.div`
    width: 18px;
    height: 16px;
    flex: 0 0 18px;
    background: #203348;
    border: 1px solid #496791;
    color: #deebff;
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 10px;
    opacity: 0.9;

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

const CountValue = styled.div`
    flex: 0 0 20px;
    text-align: center;
    font-family: ${theme.fonts.mono};
    font-size: 11px;
    color: ${props => props.$empty ? '#6f6257' : '#ffffff'};
`;

const RemoveButton = styled(TickerButton)`
    color: #ff8a80;
    border-color: #7a4a4a;

    &:hover {
        background: #7a4a4a;
        color: #ffffff;
    }
`;

/* Turn a {critClass: count} map (plus its {critClass: param} sibling and a description
 * lookup) into the row array this component renders. Exported so both menus build their
 * rows the same way.
 * `transient` comes from the payload's companion map (ship.preBattleCritTransient), which
 * the server fills from the same probe PreBattleDamage uses.
 * `label` is the full description for read-only display; `paramLabel` is the same text
 * without its trailing number, for when the ticker is showing that number instead. */
export const critRowsFromMap = (critMap, critDesc, transientMap, paramMap) => {
    const rows = [];
    for (const type in (critMap || {})) {
        if (!critMap.hasOwnProperty(type)) continue;
        const spec = battleDamage.PARAM_CRITICALS[type];
        const param = parseInt((paramMap || {})[type], 10) || 0;
        rows.push({
            type,
            isParam: Boolean(spec),
            paramLabel: spec ? spec.label : null,
            label: battleDamage.critLabel(type, critDesc, param),
            count: parseInt(critMap[type], 10) || 0,
            param,
            transient: Boolean(transientMap && transientMap[type])
        });
    }
    return rows;
};

class CriticalEffectsSection extends Component {

    constructor(props) {
        super(props);
        //One wheel ref per critical class, cached — see helpers/nonPassiveWheel.
        this.wheelRefs = {};
    }

    wheelRef(type) {
        if (!this.wheelRefs[type]) {
            this.wheelRefs[type] = nonPassiveWheel(e => this.step(type, e.deltaY < 0 ? 1 : -1));
        }
        return this.wheelRefs[type];
    }

    /* Current {critClass: count} / {critClass: param} for this target, straight off the
       payload — the rows prop is a render-time snapshot, so writes always re-read rather
       than patch it. */
    critMap() {
        const entry = battleDamage.getEntry(this.props.ship, this.props.kind, this.props.reference);
        return (entry && entry.c) ? Object.assign({}, entry.c) : {};
    }

    paramMap() {
        const entry = battleDamage.getEntry(this.props.ship, this.props.kind, this.props.reference);
        return (entry && entry.p) ? Object.assign({}, entry.p) : {};
    }

    /* The number the ticker shows and edits: the COUNT for an ordinary critical, the
       PARAM for a param-carrying one (where the count is always 1 and meaningless). */
    valueOf(row) {
        return row.isParam ? row.param : row.count;
    }

    maxValueOf(row) {
        if (!row.isParam) return battleDamage.MAX_CRIT_COUNT;
        const spec = battleDamage.PARAM_CRITICALS[row.type];
        return Math.min(battleDamage.MAX_CRIT_PARAM, (spec && spec.max) || battleDamage.MAX_CRIT_PARAM);
    }

    setValue(row, value) {
        const { ship, kind, reference, onChange } = this.props;

        const map = this.critMap();
        const params = this.paramMap();

        if (value > 0) {
            if (row.isParam) {
                map[row.type] = 1;                                  //collapsed: one crit, magnitude in the param
                params[row.type] = Math.min(value, this.maxValueOf(row));
            } else {
                map[row.type] = Math.min(value, battleDamage.MAX_CRIT_COUNT);
            }
        } else {
            //Dropping to 0 removes the effect from the payload. The ROW survives — the
            //class stays in the ship's crit memory — so it can be put straight back.
            delete map[row.type];
            delete params[row.type];
        }

        battleDamage.setCriticals(ship, kind, reference, map, params);
        if (onChange) onChange();
    }

    step(type, direction) {
        const row = this.rowForType(type);
        this.setValue(row, this.valueOf(row) + direction);
    }

    /* Rebuild one row from the class name alone, reading its current count/param off the
       payload. This is what draws a REMEMBERED-but-removed class: everything the row
       needs beyond the payload (its wording, whether it is one-turn, whether it is
       param-carrying) is derivable from the class. */
    rowForType(type) {
        const { ship } = this.props;
        const spec = battleDamage.PARAM_CRITICALS[type];
        const param = parseInt(this.paramMap()[type], 10) || 0;

        return {
            type,
            isParam: Boolean(spec),
            paramLabel: spec ? spec.label : null,
            label: battleDamage.critLabel(type, ship.preBattleCritDesc, param),
            count: parseInt(this.critMap()[type], 10) || 0,
            param,
            transient: Boolean(ship.preBattleCritTransient && ship.preBattleCritTransient[type])
        };
    }

    /* The rows to draw: every class this target has ever shown, in first-seen order, with
       its CURRENT value re-read from the payload — so one the player removed comes back
       as an empty row rather than disappearing. The memory lives on the ship, so it also
       survives closing and reopening the menu (battleDamage.critMemory).
       Called from render(); rememberCriticals is append-only and derived purely from the
       rows prop. */
    displayRows(rows) {
        const { ship, kind, reference } = this.props;

        const remembered = battleDamage.rememberCriticals(
            ship, kind, reference, (rows || []).map(row => row.type));

        return remembered.map(type => this.rowForType(type));
    }

    render() {
        const { rows, editable } = this.props;

        //Read-only sections never lose a row, so they need no memory of their own.
        const shown = editable ? this.displayRows(rows) : (rows || []);
        if (!shown.length) return null;

        return (
            <Section>
                <SectionHeader>Critical Effects</SectionHeader>
                {shown.map(row => {
                    const value = this.valueOf(row);
                    const max = this.maxValueOf(row);

                    return (
                        <CritRow key={row.type} $empty={editable && value <= 0}>
                            <CritLabel title={row.type}>
                                {(editable && row.isParam) ? row.paramLabel : row.label}
                                {row.transient && <TransientTag>(turn 1 only)</TransientTag>}
                            </CritLabel>

                            {editable ? (
                                <Controls>
                                    <TickerButton
                                        title={row.isParam ? 'Reduce' : 'One fewer'}
                                        disabled={value <= 0}
                                        onClick={() => this.step(row.type, -1)}
                                    >&minus;</TickerButton>
                                    <CountValue $empty={value <= 0} ref={this.wheelRef(row.type)}>
                                        {value}
                                    </CountValue>
                                    <TickerButton
                                        title={row.isParam ? 'Increase' : 'One more'}
                                        disabled={value >= max}
                                        onClick={() => this.step(row.type, 1)}
                                    >+</TickerButton>
                                    <RemoveButton
                                        title="Remove this critical effect (the row stays, so you can put it back)"
                                        onClick={() => this.setValue(row, 0)}
                                    >&times;</RemoveButton>
                                </Controls>
                            ) : (
                                row.count > 1 && <CritCount>(x{row.count})</CritCount>
                            )}
                        </CritRow>
                    );
                })}
            </Section>
        );
    }
}

export default CriticalEffectsSection;
