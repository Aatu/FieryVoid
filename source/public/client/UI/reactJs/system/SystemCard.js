import * as React from "react";
import styled from "styled-components";
import theme from "../styled/theme";
import placeTooltip, { EDGE } from "../helpers/placeTooltip";
import { parseRulesText, briefCount, rulesBlocks, boldSegments } from "../helpers/rulesText";

/* THE SYSTEM TOOLTIP AS A DATASHEET CARD (user choice 2026-11-08, from a set of options: the Datasheet look,
   "Brief + Shift" for long rules text, the platform UI face, square corners).

   SystemInfo.js decides WHAT a system says; this file decides how it is drawn, where it goes and how much of the
   rules text shows.
   - Built like the ship window it pops out of: square, framed in the SCS line, a header band carrying the class
     chips, structure as the SCS box track, fire control in three cells, numbers in Consolas.
   - One section order on every system: status, criticals, called shot, officers and numbers - what the player
     hovered to learn - then the rules text, then the resolution priority. The old box drew criticals and the
     called-shot chances AFTER the rules, so on a long system they were the first lines pushed off the screen.
   - The rules text shows its first lines and a "+N more" row until Shift is held. Shift is tracked for the
     whole page (below), so a card that opens while it is already down opens expanded.
   - Measured, then placed (helpers/placeTooltip): beside the ship window, never off the screen. A card still too
     tall opens WIDE - numbers on the left, the rules flowing into as many columns as they need.
   - Opened by a touch long-press (SystemIcon), it shows everything, stays up when the finger lifts, scrolls,
     and closes on the next tap outside it.
   - No element opacity anywhere: the old box's opacity 0.8 faded every letter (the alpha-compounding trap,
     project_visual_unification). The fill is opaque.
   - The lobby's gamesNew.css sets Arial on EVERY element (* {...}), which beats an inherited face, so the face
     goes on each element here and the Consolas parts take theirs back with a doubled class (&&). */

const TRACK_MAX = 30;        //structure drawn box by box up to this many boxes; a bar above it
const TOUCH_WINDOW = 1500;   //ms after SystemIcon's touchstart stamp within which a new card counts as opened by touch
const WIDE_LEFT = 284;       //the numbers pane when the card opens wide
const WIDE_COLUMN = 296;     //one rules column when the card opens wide
const WIDE_GAP = 18;
const WIDE_MAX_COLUMNS = 4;
const FC_LABELS = ["Ftr", "Med", "Cap"];

/* THE TYPE SCALE - three sizes and no others (user feedback 2026-11-08: the first build used eight, 8.5px to 16px, and
   read as uneven). Emphasis comes from weight and colour instead: the damage figure is bold white, not bigger. */
const SIZE = {
    micro: "10px",   //uppercase labels, chips, section heads, the footer, the "+N more" row
    body: "12px",    //every value and every line of text
    title: "13px",   //the system's name
};

/* rgba() of a 6-digit theme colour, so a tint stays tied to its token */
const tint = (hex, alpha) => {
    const n = parseInt(String(hex).slice(1, 7), 16);
    return `rgba(${(n >> 16) & 255}, ${(n >> 8) & 255}, ${n & 255}, ${alpha})`;
};

/* a theme colour mixed toward white, so chip text reads on the dark header band whatever the class colour */
const lighten = (hex, amount) => {
    const n = parseInt(String(hex).slice(1, 7), 16);
    const mix = channel => Math.round(channel + (255 - channel) * amount);
    return `rgb(${mix((n >> 16) & 255)}, ${mix((n >> 8) & 255)}, ${mix(n & 255)})`;
};

/* Is Shift down? One page-wide tracker: keyboard events, plus the shiftKey every mouseover carries, so the state
   is right the moment the pointer reaches an icon. Window blur clears it (a key released in another window). */
let shiftDown = false;
const shiftWatchers = new Set();
const setShift = down => {
    if (down === shiftDown) return;
    shiftDown = down;
    shiftWatchers.forEach(watcher => watcher(down));
};
const watchShift = watcher => {
    shiftWatchers.add(watcher);
    return () => shiftWatchers.delete(watcher);
};
if (typeof window !== "undefined" && window.addEventListener) {
    window.addEventListener("keydown", event => { if (event.key === "Shift") setShift(true); }, true);
    window.addEventListener("keyup", event => { if (event.key === "Shift") setShift(false); }, true);
    window.addEventListener("mouseover", event => setShift(Boolean(event.shiftKey)), true);
    window.addEventListener("blur", () => setShift(false));
}

/* The card a touch long-press has pinned, if any. SystemIcon asks on release: with a pinned card up it leaves the
   hover alone (the card closes itself on the next tap); with none - a click menu holds the tooltip root, say - it
   clears the hover on release as it always did. */
let pinnedCard = null;
export const isCardPinned = () => pinnedCard !== null;

/* ── text helpers ── */
export const minusSign = value => String(value).replace(/^-(?=\d)/, "−");    //"-5" -> "−5"
export const rangeDash = value => String(value).replace(/(\d)-(\d)/g, "$1–$2"); //"24-60" -> "24–60"
const looksNumeric = value => /\d/.test(String(value)) && String(value).length <= 16;

/* A data value as the server writes it: <br> breaks lines, <b>..</b> is bold, everything else is text. */
export const RichText = ({ text }) => String(text == null ? "" : text).split(/<br\s*\/?>/i).map((line, i) => (
    <React.Fragment key={i}>
        {i > 0 && <br />}
        {boldSegments(line).map((segment, j) => segment.bold
            ? <b key={j}>{segment.text}</b>
            : <React.Fragment key={j}>{segment.text}</React.Fragment>)}
    </React.Fragment>
));

/* ── frame ── */
const Card = styled.div`
    position: absolute;
    z-index: 20000;
    width: 306px;
    color: ${theme.colors.text};
    background-color: ${theme.colors.panelBg};
    border: 1px solid ${theme.colors.line};
    box-shadow: 0 10px 26px rgba(0, 0, 0, 0.55);
    font-size: ${SIZE.body};
    line-height: 1.4;
    text-align: left;
    /*A hover card never takes the pointer: it cannot be reached anyway, and it must not swallow clicks. A card
      pinned by touch takes it back, to scroll.*/
    pointer-events: ${props => props.$pinned ? "auto" : "none"};
    overflow-x: hidden;
    overflow-y: ${props => props.$pinned ? "auto" : "hidden"};
    ${props => props.$pinned ? `
        touch-action: pan-y;
        overscroll-behavior: contain;
        & [data-head] { position: sticky; top: 0; z-index: 1; }
    ` : ""}

    &, & * {
        box-sizing: border-box;
        font-family: ${theme.fonts.ui};
    }

    &[data-wide] { width: auto; }
    &[data-wide] [data-body] { display: flex; align-items: flex-start; gap: ${WIDE_GAP}px; }
    &[data-wide] [data-left] { flex: none; width: ${WIDE_LEFT}px; }
    &[data-wide] [data-rules] { flex: none; margin-top: 0; column-gap: ${WIDE_GAP}px; }
`;

const Head = styled.div`
    padding: 6px 10px 7px;
    background-color: ${theme.colors.card};
    border-bottom: 1px solid ${theme.colors.line};
`;

const Title = styled.div`
    display: flex;
    align-items: baseline;
    gap: 8px;
`;

const Name = styled.span`
    flex: 1;
    min-width: 0;
    font-size: ${SIZE.title};
    font-weight: 700;
    letter-spacing: 0.05em;
    text-transform: uppercase;
`;

const Id = styled.span`
    font-size: ${SIZE.micro};
    color: ${theme.colors.textDim};
    white-space: nowrap;
    && { font-family: ${theme.fonts.mono}; }
`;

const Chips = styled.div`
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 4px 5px;
    margin-top: 5px;
`;

/* Three kinds of chip, three looks, so they never read as one another (user request 2026-11-08):
   - "class":  the weapon class - FILLED in the class colour (theme.chips.weaponClass, from its animation colour);
   - "damage": the damage type - OUTLINED in the type's colour (theme.chips.damageType);
   - "flag":   a property such as Uninterceptable - a faint accent chip (user preference 2026-11-08: the boxless
               diamond tried in its place was reverted). */
const chipLook = (kind, color) => {
    if (kind === "flag") return `
        color: ${theme.colors.accent};
        background-color: ${tint(theme.colors.accent, 0.08)};
        border: 1px solid ${tint(theme.colors.accent, 0.55)};
    `;
    if (kind === "damage") return `
        color: ${lighten(color, 0.3)};
        border: 1px solid ${tint(color, 0.75)};
    `;
    return `
        color: ${lighten(color, 0.45)};
        background-color: ${tint(color, 0.18)};
        border: 1px solid ${tint(color, 0.6)};
    `;
};

const Chip = styled.span`
    padding: 0 6px;
    font-size: ${SIZE.micro};
    font-weight: 600;
    line-height: 15px;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    border: 1px solid transparent;
    ${props => chipLook(props.$kind, props.$color || theme.chips.neutral)}
`;

const Body = styled.div`
    padding: 8px 10px 9px;
`;

const Left = styled.div`
    display: grid;
    gap: 8px;
`;

const Foot = styled.div`
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 2px 10px;
    padding: 4px 10px 5px;
    border-top: 1px solid ${theme.colors.rule};
    font-size: ${SIZE.micro};
    color: ${theme.colors.textDim};
`;

const FootMono = styled.span`
    && { font-family: ${theme.fonts.mono}; }
`;

/* ── the pieces SystemInfo assembles ── */
export const Lab = styled.span`
    font-size: ${SIZE.micro};
    font-weight: 600;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: ${theme.colors.textDim};
`;

export const SectionLabel = styled.div`
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 4px;
    font-size: ${SIZE.micro};
    font-weight: 600;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: ${props => props.$gold ? theme.colors.enhTitle : theme.colors.accent};

    &::after {
        content: "";
        flex: 1;
        height: 1px;
        background-color: ${props => props.$gold ? theme.colors.enhLine : theme.colors.rule};
    }
`;

const StructRow = styled.div`
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    row-gap: 4px;
    align-items: baseline;
`;

const Num = styled.span`
    font-size: ${SIZE.body};
    color: ${theme.colors.text};
    && { font-family: ${theme.fonts.mono}; }
`;

const Of = styled.span`
    color: ${theme.colors.textDim};
    && { font-family: ${theme.fonts.mono}; }
`;

const Track = styled.span`
    grid-column: 1 / -1;
    display: flex;
    flex-wrap: wrap;
    gap: 2px;
`;

const TrackBox = styled.i`
    width: 7px;
    height: 10px;
    background-color: ${props => props.$lost ? "transparent" : (props.$crit ? theme.colors.healthCrit : theme.colors.healthOk)};
    box-shadow: ${props => props.$lost ? `inset 0 0 0 1px ${tint(theme.colors.line, 0.6)}` : "none"};
`;

const Bar = styled.span`
    grid-column: 1 / -1;
    height: 8px;
    background-color: ${tint(theme.colors.line, 0.12)};
    box-shadow: inset 0 0 0 1px ${tint(theme.colors.line, 0.45)};
`;

const BarFill = styled.i`
    display: block;
    height: 100%;
    background-color: ${props => props.$crit ? theme.colors.healthCrit : theme.colors.healthOk};
`;

/* The SCS structure track: one box per point, lost boxes hollow, orange once the system holds criticals (the
   ship window's own health-bar rule). */
export const Structure = ({ remaining, max, crit }) => {
    const left = Math.max(0, remaining);
    return (
        <StructRow>
            <Lab>Structure</Lab>
            <Num>{left}<Of>/{max}</Of></Num>
            {max > 0 && max <= TRACK_MAX
                ? <Track>{Array.from({ length: max }, (_, i) => <TrackBox key={i} $lost={i >= left} $crit={crit} />)}</Track>
                : <Bar><BarFill $crit={crit} style={{ width: (max > 0 ? 100 * left / max : 0) + "%" }} /></Bar>}
        </StructRow>
    );
};

const CellRow = styled.div`
    display: grid;
    grid-template-columns: repeat(${props => props.$count}, minmax(0, 1fr));
    border: 1px solid ${theme.colors.rule};
`;

/* label over value, both centred, kept tight (user feedback 2026-11-08: the first cells were oversized for a "2") */
const Cell = styled.div`
    display: grid;
    justify-items: center;
    min-width: 0;
    padding: 2px 6px 3px;
    text-align: center;
    line-height: 1.3;

    & + & { border-left: 1px solid ${theme.colors.rule}; }
`;

const CellValue = styled.span`
    max-width: 100%;
    font-size: ${SIZE.body};
    color: ${theme.colors.text};
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    && { font-family: ${theme.fonts.mono}; }
`;

export const Cells = ({ cells }) => (
    <CellRow $count={cells.length}>
        {cells.map(cell => (
            <Cell key={cell.label}>
                <Lab>{cell.label}</Lab>
                <CellValue title={String(cell.value)}>{String(cell.value)}</CellValue>
            </Cell>
        ))}
    </CellRow>
);

const CritRow = styled.div`
    display: flex;
    align-items: baseline;
    gap: 7px;
    padding: 3px 8px;
    color: ${theme.colors.text};
    background-color: ${tint(theme.colors.healthCrit, 0.13)};
    border-left: 3px solid ${theme.colors.healthCrit};

    & + & { margin-top: 2px; }
`;

const CritCount = styled.span`
    font-size: ${SIZE.body};
    font-weight: 700;
    color: ${theme.colors.healthCrit};
    && { font-family: ${theme.fonts.mono}; }
`;

const CritUntil = styled.span`
    color: ${theme.colors.textDim};
`;

export const Criticals = ({ lines }) => (
    <div>
        {lines.map(line => (
            <CritRow key={line.key}>
                {line.count > 1 && <CritCount>{"×" + line.count}</CritCount>}
                <span>{line.text}{line.until && <CritUntil>{" " + line.until}</CritUntil>}</span>
            </CritRow>
        ))}
    </div>
);

const ShotRow = styled.div`
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 10px;
    padding: 2px 0;

    & + & { border-top: 1px dotted ${theme.colors.rule}; }
`;

const ShotPct = styled.b`
    font-size: ${SIZE.body};
    font-weight: 700;
    color: ${theme.colors.text};
    && { font-family: ${theme.fonts.mono}; }
`;

const ShotNote = styled.span`
    color: ${theme.colors.textDim};
    text-align: right;
`;

/* rows: { label, pct } for a chance to hit, { label, note } for why there is none, { label } for a line alone */
export const CalledShot = ({ title, rows }) => (
    <div>
        <SectionLabel>{title}</SectionLabel>
        {rows.map((row, i) => (
            <ShotRow key={i}>
                <span>{row.label}</span>
                {row.pct !== undefined && row.pct !== null
                    ? <ShotPct>{row.pct}%</ShotPct>
                    : (row.note ? <ShotNote>{row.note}</ShotNote> : null)}
            </ShotRow>
        ))}
    </div>
);

/* The officers block wears the gold set, like every enhancement surface (OFFICERS_PLAN.md D12). */
export const OfficerLine = styled.div`
    color: ${props => props.$out ? theme.colors.textDim : theme.colors.enhText};

    & + & { margin-top: 2px; }
`;

export const OfficerLabel = styled.span`
    font-weight: 700;
    color: ${theme.colors.enhTitle};
`;

const ProfGrid = styled.div`
    display: grid;
    grid-template-columns: minmax(92px, max-content) minmax(0, 1fr);
    column-gap: 10px;
    row-gap: 2px;
    align-items: baseline;
`;

const ProfLab = styled(Lab)`
    max-width: 150px;
    ${props => props.$sub ? "padding-left: 8px;" : ""}
`;

//$big (the damage figure) is set apart by weight and colour, not size - see SIZE
const ProfVal = styled.span`
    font-size: ${SIZE.body};
    font-weight: ${props => props.$big ? 700 : 400};
    color: ${props => props.$big ? theme.colors.text : theme.colors.textAccent};
    ${props => props.$mono ? `&& { font-family: ${theme.fonts.mono}; }` : ""}
`;

const Aux = styled.span`
    margin-left: 6px;
    font-size: ${SIZE.micro};
    color: ${theme.colors.textDim};
`;

const ModeName = styled.span`
    color: ${props => props.$active ? theme.colors.text : theme.colors.textDim};
    font-weight: ${props => props.$active ? 600 : 400};
`;

const ModeGap = styled.span`
    color: ${theme.colors.textDim};
`;

/* Every firing mode, the one in use bright and the others dim (user feedback 2026-11-08: "Normal  1 of 2" read as a
   loading count). Drawn in the text face - mode names are words. */
const Modes = ({ modes }) => modes.map((mode, i) => (
    <React.Fragment key={i}>
        {i > 0 && <ModeGap>{" · "}</ModeGap>}
        <ModeName $active={mode.active}>{mode.name}</ModeName>
    </React.Fragment>
));

const FcBox = styled.span`
    display: inline-grid;
    grid-template-columns: repeat(3, auto);
    border: 1px solid ${theme.colors.rule};
`;

const FcCell = styled.span`
    padding: 1px 7px 2px;
    text-align: center;
    font-size: ${SIZE.body};
    line-height: 1.3;
    color: ${theme.colors.textAccent};

    & + & { border-left: 1px solid ${theme.colors.rule}; }
    && { font-family: ${theme.fonts.mono}; }
`;

const FcLab = styled.small`
    display: block;
    font-size: ${SIZE.micro};
    font-weight: 600;
    line-height: 1.3;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: ${theme.colors.textDim};
`;

/* A HANGING minus (user feedback 2026-11-08): the digits centre under the heading, as a positive value's do, and the sign
   sits in the cell's left padding. Centring "−20" whole put its digits ~3px right of "FTR". */
const FcNumber = styled.span`
    position: relative;
    && { font-family: ${theme.fonts.mono}; }
`;

const FcSign = styled.span`
    position: absolute;
    right: 100%;
    && { font-family: ${theme.fonts.mono}; }
`;

const fcValue = raw => {
    if (raw === "-") return "—";
    if (/^-\d/.test(raw)) return <FcNumber><FcSign>−</FcSign>{raw.slice(1)}</FcNumber>;
    return raw;
};

/* "fighter/med/cap" as three labelled cells; anything not in that shape is printed as it came */
export const FireControl = ({ value }) => {
    const parts = String(value).split("/");
    if (parts.length !== 3) return <ProfVal $mono><RichText text={value} /></ProfVal>;
    return (
        <FcBox>
            {parts.map((part, i) => (
                <FcCell key={i}><FcLab>{FC_LABELS[i]}</FcLab>{fcValue(part.trim())}</FcCell>
            ))}
        </FcBox>
    );
};

/* rows: { label, value, big?, aux? }, { label, fc } or { label, modes } - a label starting " - " is a sub-item of the
   row above */
export const Profile = ({ rows }) => (
    <ProfGrid>
        {rows.map((row, i) => (
            <React.Fragment key={i}>
                <ProfLab $sub={/^\s*-\s/.test(String(row.label))}>{String(row.label).replace(/^\s*-\s*/, "")}</ProfLab>
                <span>
                    {row.fc !== undefined ? <FireControl value={row.fc} />
                        : row.modes ? <Modes modes={row.modes} />
                            : <ProfVal $big={row.big} $mono={looksNumeric(row.value)}><RichText text={row.value} /></ProfVal>}
                    {row.aux && <Aux>{row.aux}</Aux>}
                </span>
            </React.Fragment>
        ))}
    </ProfGrid>
);

export const CardNote = styled.div`
    color: ${theme.colors.chromeText};
`;

/* ── rules text ── */
const RulesPane = styled.div`
    min-width: 0;
    margin-top: 10px;
`;

const RuleList = styled.ul`
    margin: 0;
    padding: 0;
    list-style: none;
`;

const Rule = styled.li`
    position: relative;
    break-inside: avoid;
    margin: 0;
    margin-left: ${props => props.$kind === "sub" ? "13px" : "0"};
    padding-left: ${props => (props.$kind === "head" || props.$kind === "table") ? "0" : "13px"};
    font-weight: ${props => props.$kind === "head" ? 600 : 400};
    color: ${props => props.$kind === "warn" ? theme.colors.warningSoft
        : (props.$kind === "head" ? theme.colors.text : theme.colors.chromeText)};

    & + & { margin-top: ${props => props.$kind === "head" ? "7px" : (props.$kind === "sub" ? "2px" : "3px")}; }

    &::before {
        content: ${props => (props.$kind === "head" || props.$kind === "table") ? "none" : "\"\""};
        position: absolute;
        left: 2px;
        top: ${props => props.$kind === "sub" ? "0.72em" : "0.62em"};
        width: ${props => props.$kind === "sub" ? "6px" : "4px"};
        height: ${props => props.$kind === "sub" ? "1px" : "4px"};
        background-color: ${props => props.$kind === "warn" ? theme.colors.warning
            : (props.$kind === "sub" ? theme.colors.textDim : theme.colors.linePrimary)};
    }
`;

const Term = styled.b`
    font-weight: 700;
    color: ${theme.colors.text};
`;

const RuleTable = styled.span`
    display: grid;
    grid-template-columns: max-content minmax(0, 1fr);
    column-gap: 12px;
    row-gap: 2px;
    margin-left: 13px;
    padding: 4px 8px;
    background-color: ${tint(theme.colors.line, 0.1)};
    border-left: 1px solid ${tint(theme.colors.line, 0.6)};
`;

const RuleTableTerm = styled.span`
    font-size: ${SIZE.body};
    line-height: 1.4;
    white-space: nowrap;
    color: ${theme.colors.textAccent};
    && { font-family: ${theme.fonts.mono}; }
`;

const RuleTableText = styled.span`
    color: ${theme.colors.chromeText};
`;

const More = styled.div`
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 4px 10px;
    margin-top: 8px;
    padding: 4px 8px;
    font-size: ${SIZE.micro};
    font-weight: 600;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: ${theme.colors.textDim};
    border: 1px dashed ${tint(theme.colors.line, 0.6)};
`;

const Kbd = styled.kbd`
    padding: 0 5px;
    font-size: ${SIZE.micro};
    letter-spacing: 0;
    text-transform: none;
    line-height: 1.4;
    color: ${theme.colors.text};
    background-color: ${theme.colors.card};
    border: 1px solid ${theme.colors.line};
    border-bottom-width: 2px;
    && { font-family: ${theme.fonts.mono}; }
`;

const RuleLine = ({ item }) => item.term
    ? <React.Fragment><Term>{item.term}:</Term> {item.rest}</React.Fragment>
    : <RichText text={item.line} />;

const Rules = ({ blocks }) => (
    <RuleList>
        {blocks.map((block, i) => block.type === "table"
            ? (
                <Rule key={i} $kind="table">
                    <RuleTable>
                        {block.rows.map((row, j) => (
                            <React.Fragment key={j}>
                                <RuleTableTerm>{row.term}</RuleTableTerm>
                                <RuleTableText>{row.rest}</RuleTableText>
                            </React.Fragment>
                        ))}
                    </RuleTable>
                </Rule>
            )
            : <Rule key={i} $kind={block.item.kind}><RuleLine item={block.item} /></Rule>)}
    </RuleList>
);

/* ── the card ── */
class SystemCard extends React.Component {

    constructor(props) {
        super(props);
        this.ref = React.createRef();
        this.state = { shift: shiftDown };
        this.touchSubject = undefined;
        this.touch = false;
        this.onDocumentPointerDown = this.onDocumentPointerDown.bind(this);
    }

    componentDidMount() {
        this.stopWatchingShift = watchShift(down => this.setState({ shift: down }));
        document.addEventListener("pointerdown", this.onDocumentPointerDown, true);
        this.syncPinned();
        this.layout();
    }

    componentDidUpdate() {
        this.syncPinned();
        this.layout();
    }

    componentWillUnmount() {
        if (pinnedCard === this) pinnedCard = null;
        if (this.stopWatchingShift) this.stopWatchingShift();
        document.removeEventListener("pointerdown", this.onDocumentPointerDown, true);
    }

    syncPinned() {
        if (this.touch) pinnedCard = this;
        else if (pinnedCard === this) pinnedCard = null;
    }

    /* Opened by a touch long-press? Decided once per system shown, from the stamp SystemIcon's touchstart leaves
       (the same stamp its ghost-mouse shield reads), so a later re-render of the same card keeps its mode. */
    isTouch() {
        if (this.touchSubject !== this.props.subject) {
            this.touchSubject = this.props.subject;
            this.touch = Boolean(window.lastTouchActiveTime && Date.now() - window.lastTouchActiveTime < TOUCH_WINDOW);
        }
        return this.touch;
    }

    /* A card pinned by touch closes on the next tap outside it; a tap on the card itself is a scroll. It closes the way
       its own hover would end - `closeEvent`: SystemMouseOut for an icon, StructureMouseOut for a section's health bar
       (which also takes the section's wedge off the map). */
    onDocumentPointerDown(event) {
        if (!this.touch) return;
        const node = this.ref.current;
        if (node && node.contains(event.target)) return;
        if (window.uiEvents) window.uiEvents.relay(this.props.closeEvent || "SystemMouseOut");
    }

    /* Measure the card as drawn, open it wide if it is taller than the screen, then place it. Runs after every
       render, so it first undoes what the last pass set. */
    layout() {
        const node = this.ref.current;
        if (!node) return;
        const view = {
            width: document.documentElement.clientWidth || window.innerWidth,
            height: document.documentElement.clientHeight || window.innerHeight
        };
        const maxHeight = view.height - 2 * EDGE;
        const rules = node.querySelector("[data-rules]");

        delete node.dataset.wide;
        node.style.maxHeight = "";
        if (rules) {
            rules.style.width = "";
            rules.style.columnCount = "";
        }
        node.style.left = "0px";
        node.style.top = "0px";

        if (this.touch) {
            node.style.maxHeight = maxHeight + "px";           //it scrolls
        } else if (node.offsetHeight > maxHeight) {
            if (rules) this.openWide(node, rules, maxHeight, view.width);
            if (node.offsetHeight > maxHeight) node.style.maxHeight = maxHeight + "px";   //a window too small even for that
        }

        const position = placeTooltip(this.props.boundingBox, this.props.avoidBox,
            { width: node.offsetWidth, height: node.offsetHeight }, view);
        node.style.left = position.left + "px";
        node.style.top = position.top + "px";
    }

    /* Numbers on the left, the rules beside them in balanced columns: add a column until they are short enough, as
       many as the screen is wide enough for. A screen without room for even one column beside the numbers keeps the
       card as it was. */
    openWide(node, rules, maxHeight, viewWidth) {
        node.dataset.wide = "";
        rules.style.width = WIDE_COLUMN + "px";
        const fixed = node.offsetWidth - WIDE_COLUMN;       //frame, padding, the numbers pane and the gap beside it
        const widthAllows = Math.floor((viewWidth - 2 * EDGE - fixed + WIDE_GAP) / (WIDE_COLUMN + WIDE_GAP));
        if (widthAllows < 1) {
            delete node.dataset.wide;
            rules.style.width = "";
            return;
        }
        const body = node.querySelector("[data-body]");
        const bodyStyle = window.getComputedStyle(body);
        const chrome = node.offsetHeight - body.offsetHeight;
        const columnHeight = Math.max(160, maxHeight - chrome
            - parseFloat(bodyStyle.paddingTop) - parseFloat(bodyStyle.paddingBottom));
        const natural = rules.offsetHeight;
        if (natural <= columnHeight) return;
        const most = Math.min(WIDE_MAX_COLUMNS, widthAllows);
        for (let columns = Math.min(most, Math.max(2, Math.ceil(natural / columnHeight))); columns <= most; columns++) {
            rules.style.columnCount = String(columns);
            rules.style.width = (columns * WIDE_COLUMN + (columns - 1) * WIDE_GAP) + "px";
            if (rules.offsetHeight <= columnHeight) return;
        }
    }

    render() {
        const { name, id, chips, children, rules, footer } = this.props;
        const touch = this.isTouch();
        const items = parseRulesText(rules);
        const shown = (touch || this.state.shift) ? items.length : briefCount(items);
        const hidden = items.length - shown;

        return (
            <Card ref={this.ref} $pinned={touch}>
                <Head data-head="">
                    <Title>
                        <Name>{name}</Name>
                        {id !== undefined && id !== null && id !== "" && <Id>ID {id}</Id>}
                    </Title>
                    {chips && chips.length > 0 && (
                        <Chips>{chips.map(chip => (
                            <Chip key={chip.kind + "-" + chip.label} $kind={chip.kind} $color={chip.color}>{chip.label}</Chip>
                        ))}</Chips>
                    )}
                </Head>
                <Body data-body="">
                    <Left data-left="">{children}</Left>
                    {items.length > 0 && (
                        <RulesPane data-rules="">
                            <SectionLabel>Special rules</SectionLabel>
                            <Rules blocks={rulesBlocks(items.slice(0, shown))} />
                            {hidden > 0 && (
                                <More>
                                    <span>+{hidden} more rules {hidden === 1 ? "line" : "lines"}</span>
                                    <span>hold <Kbd>Shift</Kbd> for all</span>
                                </More>
                            )}
                        </RulesPane>
                    )}
                </Body>
                {(footer || touch) && (
                    <Foot>
                        {footer ? <FootMono>{footer}</FootMono> : <span />}
                        {touch && <span>Tap outside to close</span>}
                    </Foot>
                )}
            </Card>
        );
    }
}

export default SystemCard;
