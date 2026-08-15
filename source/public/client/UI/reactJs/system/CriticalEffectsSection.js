import React, { Component } from 'react';
import styled from 'styled-components';
import theme from '../styled/theme';
import nonPassiveWheel from '../helpers/nonPassiveWheel';
import { CritSectionHeader, SectionDivider } from './menuControls';

/* Critical effects carried by ONE damage target — a system (kind 0, ref = systemid) or
 * a fighter ordinal (kind 1, ref = 1..flightSize). Rendered inside ApplyDamageMenu and
 * FighterDamageMenu, below the damage row. See PREBATTLE_DAMAGE_PLAN.md §5.2.
 *
 * `editable` gives the section its ADD / AMEND / REMOVE controls: a picker at the foot of
 * the list and a [-][N][+] ticker on each row. Every change goes through
 * battleDamage.setCriticals, which is the only way criticals ever enter or leave a payload
 * client-side, so the preview, the buy POST and the saved-fleet write all follow with no
 * extra wiring. Read-only (`editable` absent) is just the list.
 *
 * A row DROPPED TO ZERO STAYS ON SCREEN, empty, so it can be put back — removing a
 * critical is an edit like any other, and an edit you can only undo by reloading the
 * whole fleet is a trap. The list of classes to keep drawing lives on the SHIP
 * (battleDamage.critMemory), not on this component, so closing and reopening the menu
 * does not quietly lose the row. There is deliberately NO ✕ button: it did exactly what
 * ticking the count down to 0 does, and two controls for one action read as two actions.
 *
 * PARAM-CARRYING criticals (battleDamage.PARAM_CRITICALS — DamageReductionReduced today)
 * keep their magnitude in the crit's param rather than in a count, so their ticker edits
 * the PARAM and the label loses its trailing number: "Damage reduction reduced by [8]".
 *
 * ADDING (plan §11, built 2026-08-08) comes from the per-class catalogue endpoint
 * systemCriticals.php: a picker listing the criticals this system can carry in its own
 * right (its hit chart plus its $preBattleCriticals extras), with an "All" switch that
 * ADDS the generally-applicable effects on top — it widens the list, it never replaces
 * it. The picker only decides what is OFFERED — what may be STORED is
 * PreBattleDamage::isValidCriticalType and is deliberately wider than both.
 * Each class also has a LIMIT (battleDamage.CRIT_LIMITS, mirroring the PHP table): most
 * effects stack, but the ones the game reads as a flag are capped at one, so the [+] goes
 * dead rather than letting a player dial in a wound that does nothing.
 */

const Section = styled.div`
    display: flex;
    flex-direction: column;
    /*The menus above are shrink-to-fit tooltips capped with a max-width; nothing in here
      may ask to be wider than the menu it sits in.*/
    min-width: 0;
    max-width: 100%;
    box-sizing: border-box;
    /*No border-top: CritSectionHeader carries its own hairline, in the section's own hue,
      and a rule here as well would double it.*/
`;

/* The bar itself lives in ./menuControls beside the damage and enhancement bars - three
   tints of one geometry, in one file, so they cannot drift apart (see the comment there).
   Aliased locally so the JSX below still reads as "this section's header". */
const SectionHeader = CritSectionHeader;

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
    /*"Damage reduction reduced by" and friends wrap inside the menu rather than widening
      it - the menus are shrink-to-fit and capped.*/
    overflow-wrap: anywhere;
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

/* The damage row above the criticals used to re-export this component's bar, because the
   two were the same colour (user request 2026-08-08: the Damage block had no header while
   Critical Effects did). They are now two tints of one geometry and both live in
   ./menuControls, so ApplyDamageMenu imports DamageSectionHeader from there directly. */

/* ⭐ The checkbox + its word, as ONE aligned pair. Exported so "Destroy" in ApplyDamageMenu
   and "All" here cannot drift apart.

   Two separate things push a checkbox off its label, and BOTH have to be answered:

   1. The UA's own `margin: 3px 3px 3px 4px` plus its intrinsic box make its MARGIN box
      several pixels taller than the text beside it, so `align-items: center` faithfully
      centres two boxes of different heights and the ink lands off-centre. Answered by
      zeroing the margin, fixing the box, and giving the text `line-height: 1`.

   2. ⚠️ `base.css` carries a GLOBAL `input[type="checkbox"] { position: relative; top: 2px }`
      that nudges every checkbox in the app down by 2px. `input[type=…]` is specificity
      0,1,1 and a styled-components class is 0,1,0, so the global rule BEATS this one and
      the 2px offset survived the first fix (user report 2026-08-08). `&[type='checkbox']`
      makes it 0,2,1 and takes it back — an explicit `top: 0` rather than an `!important`,
      so the ordinary rule keeps applying to every other checkbox on the page. */
export const CheckBox = styled.input`
    margin: 0;
    width: 12px;
    height: 12px;
    flex: 0 0 12px;
    cursor: pointer;

    &[type='checkbox'] {
        position: relative;
        top: 0;
    }
`;

export const CheckText = styled.span`
    flex: 0 0 auto;
    line-height: 1;
    margin-top: 2px;
`;

/* The add-an-effect row. A native <select> on purpose: it is the one control that gets
   a usable list on a phone for free, and the lobby's damage menus are used on one. */
const AddRow = styled.div`
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 3px 8px 4px;
`;

const AddSelect = styled.select`
    flex: 1 1 auto;
    /*Both needed: min-width:0 lets a flex item shrink below its content, width:100% stops
      it claiming its longest option's width once the menu's max-width has bounded it.*/
    min-width: 0;
    width: 100%;
    height: 18px;
    box-sizing: border-box;
    padding: 0 2px;
    font-family: inherit;
    font-size: 10px;
    color: ${theme.colors.chromeText};
    background-color: #101a26;
    border: 1px solid #496791;
    outline: none;

    &:focus { border-color: #6089c1; }
`;

const AllToggle = styled.label`
    display: flex;
    align-items: center;
    gap: 3px;
    flex: 0 0 auto;
    font-size: 9px;
    letter-spacing: 0.3px;
    color: ${theme.colors.textDim};
    cursor: pointer;
    user-select: none;
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
        //"all effects" is per-menu UI state: which list the picker offers, not fleet state.
        this.state = { showAll: false };
    }

    /* The catalogue is per SHIP CLASS and fetched once per session; loadCatalogue
       de-duplicates, so an already-fetched class costs a map lookup. The callback fires
       only on a fresh arrival, so this repaints exactly once. */
    componentDidMount() {
        this.fetchCatalogue();
    }

    /* ⚠️ ALSO on update, not just on mount. The damage menus re-render into ONE React root
       (#systemInfoReact), so opening a second ship's menu REUSES this instance with new
       props rather than remounting it - componentDidMount would never run again and the new
       ship's catalogue would never be requested. Cheap to repeat: loadCatalogue is keyed by
       phpclass/flightSize and a cached class is a map lookup. */
    componentDidUpdate(prevProps) {
        if (prevProps.ship !== this.props.ship) this.fetchCatalogue();
    }

    fetchCatalogue() {
        if (!this.props.editable) return;
        battleDamage.loadCatalogue(this.props.ship, () => this.forceUpdate());
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

    /* The ticker's ceiling: for a param class the PARAM's bound, otherwise the per-class
       critical limit — most effects stack, but the ones the game reads as a flag are
       capped at one so the [+] goes dead instead of storing a wound that does nothing. */
    maxValueOf(row) {
        if (!row.isParam) return battleDamage.critLimit(row.type);
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
                map[row.type] = Math.min(value, battleDamage.critLimit(row.type));
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

    /* What the picker offers: the catalogue's list for this target minus everything
       already drawn (an on-screen row IS the way to put an effect back, so offering it
       again would give one class two controls). Empty until the catalogue arrives, which
       is why the whole add row hides itself rather than showing an empty dropdown. */
    addableTypes(shownTypes) {
        const { ship, kind, reference } = this.props;

        const offered = battleDamage.offerableCriticals(ship, kind, reference, this.state.showAll);
        const drawn = {};
        shownTypes.forEach(type => { drawn[type] = true; });

        return offered
            .filter(type => !drawn[type])
            .map(type => ({ type, label: this.pickerLabel(type) }))
            .sort((a, b) => a.label.localeCompare(b.label));
    }

    /* A picker entry's wording. Param classes read their magnitude out of the payload,
       which a not-yet-added effect has none of, so they use the same number-less label
       their ticker row shows; everything else goes through critLabel, which is also what
       draws the row once it has been added - one source, so the two cannot disagree. */
    pickerLabel(type) {
        const spec = battleDamage.PARAM_CRITICALS[type];
        if (spec) return spec.label;
        return battleDamage.critLabel(type, this.props.ship.preBattleCritDesc, 0);
    }

    /* Adding = setting the effect to ONE, through the same setValue every ticker uses,
       so the payload has exactly one door. A param class starts at 1 too - a magnitude
       the player then dials up. */
    onAdd(type) {
        if (!type) return;
        this.setValue(this.rowForType(type), 1);
    }

    render() {
        const { ship, rows, editable } = this.props;

        //Read-only sections never lose a row, so they need no memory of their own.
        const shown = editable ? this.displayRows(rows) : (rows || []);
        //The picker appears as soon as the catalogue is in - NOT only when it has
        //something to offer. A fighter's own hit chart lists no criticals at all, so
        //gating on `addable` would hide the "All" switch that is the only way to reach
        //them, and the control would be permanently unreachable.
        const catalogueReady = Boolean(editable && battleDamage.catalogueFor(ship));
        const addable = catalogueReady ? this.addableTypes(shown.map(row => row.type)) : [];
        if (!shown.length && !catalogueReady) return null;

        return (
            <Section>
                {/* The break from whatever editor sits above - the damage row in
                    ApplyDamageMenu, the fighter grid in FighterDamageMenu. It lives HERE
                    rather than at the two call sites because this component can decide to
                    render nothing at all (no criticals, catalogue not in yet), and a
                    divider left dangling over an empty gap reads as a broken menu. */}
                <SectionDivider $chrome />
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
                                        title={row.isParam ? 'Increase'
                                            : (value >= max && max === 1
                                                ? 'This effect only applies once'
                                                : 'One more')}
                                        disabled={value >= max}
                                        onClick={() => this.step(row.type, 1)}
                                    >+</TickerButton>
                                </Controls>
                            ) : (
                                row.count > 1 && <CritCount>(x{row.count})</CritCount>
                            )}
                        </CritRow>
                    );
                })}

                {/* Hidden until the catalogue arrives - an empty dropdown before then
                    would read as a broken control rather than as one still loading. 
                    //Removed All toggle, to re-add place following fragment after </AddSelect> but before </AddRow>
                        <AllToggle title="Also offer the effects that apply to any system, on top of this one's own">
                            <CheckBox
                                type="checkbox"
                                checked={this.state.showAll}
                                onChange={e => this.setState({ showAll: e.target.checked })}
                            />
                            <CheckText>All</CheckText>
                        </AllToggle>                    
                                                         */}
                {catalogueReady && (
                    <AddRow>
                        <AddSelect
                            value=""
                            disabled={addable.length === 0}
                            title="Add a critical effect to this unit before the battle"
                            onChange={e => this.onAdd(e.target.value)}
                        >
                            <option value="">
                                {addable.length ? '+ Add effect…' : 'Nothing to add'}
                            </option>
                            {addable.map(option => (
                                <option key={option.type} value={option.type}>{option.label}</option>
                            ))}
                        </AddSelect>

                    </AddRow>
                )}
            </Section>
        );
    }
}

export default CriticalEffectsSection;
