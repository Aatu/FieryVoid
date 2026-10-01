import * as React from "react";
import styled from "styled-components"
import { Clickable } from "../styled";
import theme from "../styled/theme";

/* The Apply Thrust panel: the thruster ring drawn round the ship plus the info panel under it.

   EXTENDED_TURNS_PLAN.md Stage 0 (§4.1-4.5) moved the info panel off the shared hover Tooltip
   (rounded 65% black, "ASSIGN THRUST", emoji buttons), with one row per thruster direction instead
   of "3 thrust to aft thrusters" lines. Every click still calls the same shipManager.movement
   function it called before.

   User request 2026-10-01: the panel wears the Gravitic Augmenter menu's green (theme.colors.green*),
   is compact, and has only CONFIRM and CANCEL. AUTO and RESET are gone: thrust is auto-assigned when
   the panel opens, and resetting every thruster to zero was not useful. */

//Fired on #thrustUIContainer by MovementPhaseStrategy.repositionThrustUi once it has moved the box.
//⚠️ That legacy file spells it out as a string literal - keep the two in step.
const THRUST_RELAYOUT_EVENT = 'fv-thrust-relayout';
//Gap kept between the panel and the edge of the screen.
const SCREEN_EDGE = 8;
//Gap between the thruster ring and the panel.
const PANEL_GAP = 6;

/* ── Ring geometry (user report 2026-10-01) ─────────────────────────────────────────────────
   The tiles are a fixed 40px, and the fore/aft columns used to stand a fixed 60px from the ship's
   centre and the port/starboard rows 80px. Ship icons keep their true WORLD size at every zoom
   (ShipIconContainer), so zoomed out a hull shrinks to a few pixels while that ring - and the panel
   hung below it - stayed 120px across, and the whole control looked detached from its ship.
   The ring now hugs the unit's allegiance circle instead: ShipIcon draws that circle
   min(canvasSize * 0.75, 250) game units across, which is that / zoom pixels on screen.
   It is pulled IN only, never pushed out: once the circle is bigger than the fixed layout (zoomed in,
   or a big hull) the ring is exactly where it always was. */
const TILE = 40;
const RING_X_MAX = 60;  //fore/aft columns: distance from the ship's centre to the inner edge
const RING_Y_MAX = 80;  //port/starboard rows: the same
const RING_GAP = 4;     //clearance between the unit's circle and the nearest tile

/* {x, y, clear}, in px. x and y: how far out the columns and rows stand. Each hugs the circle, held
   off just far enough that the tiles cannot collide at the four corners: a column and a row are
   clear of each other when the column stands outside the row's half-width OR the row stands outside
   the column's half-height, so whichever is the smaller push is the one taken. Only the directions
   this manoeuvre actually draws are counted.
   clear: the radius round the ship's centre the PANEL keeps out of as well as the tiles - a turn
   draws tiles on only two sides, and without it the panel would sit on the hull. Capped at the old
   fixed ring's reach, so zoomed in on a big hull it goes no further out than it always did. */
const getRingOffsets = (ship, totalRequired, movement) => {
    const zoom = (window.coordinateConverter && window.coordinateConverter.zoom) || 1;
    const circle = Math.min((ship.canvasSize || 200) * 0.75, 250) / 2 / zoom + RING_GAP;
    const clear = Math.min(circle, RING_Y_MAX + TILE);
    const drawn = direction => movement.type === 'roll' || !Array.isArray(totalRequired) || totalRequired[direction] !== null;
    const tiles = direction => drawn(direction) ? shipManager.systems.getThrusters(ship, direction).length : 0;
    const rowHalf = Math.max(tiles(3), tiles(4)) * TILE / 2;    //half the width of the wider side row
    const columnHalf = Math.max(tiles(1), tiles(2)) * TILE / 2; //half the height of the taller fore/aft column

    let x = Math.min(circle, RING_X_MAX);
    let y = Math.min(circle, RING_Y_MAX);
    if (x < rowHalf && y < columnHalf) {
        const pushX = rowHalf <= RING_X_MAX ? rowHalf - x : Infinity;
        const pushY = columnHalf <= RING_Y_MAX ? columnHalf - y : Infinity;
        if (pushX === Infinity && pushY === Infinity) return { x: RING_X_MAX, y: RING_Y_MAX, clear };
        if (pushX <= pushY) x = rowHalf; else y = columnHalf;
    }
    return { x, y, clear };
};

/* ── Thruster ring ─────────────────────────────────────────────────────────────────────── */

/* channeled/output in Consolas on a dark backing so it reads over the thruster art.
   $over: channelling past the thruster's rating, i.e. overthrusting. */
const Text = styled.span`
    font-family: ${theme.fonts.mono};
    font-size: 11px;
    line-height: 1;
    padding: 1px 2px;
    color: ${props => props.$over ? theme.colors.healthCrit : theme.colors.text};
    background-color: rgba(0, 0, 0, 0.55);
    pointer-events: none;
`;

/* $box - what a click on this thruster would do right now (user request 2026-10-01):
     'need'   orange - it pays thrust the manoeuvre still needs (the panel's colour for a shortfall)
     'extra'  green  - it is taken as EXTRA thrust beyond the requirement, which shortens a turn's
                       delay (the panel's green "Extra thrust" row)
     unset    no box - the click would be refused (thruster at double its rating, no engine thrust
                       left, nothing more a non-turn manoeuvre will take, ...)
   $destroyed: dimmed and crossed out. getThrusters returns destroyed thrusters too, and the
   panel used to draw them exactly like live ones, so a ship that had lost its thrusters could
   only find out why from the ship window. */
const Thruster = styled.div`
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: space-around;
    position: relative; // Needed for absolute positioning of ::before
    box-sizing: border-box;
    ${props => {
        if (props.$box === 'need') return `box-shadow: inset 0 0 0 1px ${theme.colors.warning};`;
        if (props.$box === 'extra') return `box-shadow: inset 0 0 0 1px ${theme.colors.greenBtnLineHover};`;
        return '';
    }}

    &::before {
        content: "";
        position: absolute;
        width: 40px;
        height: 40px;
        z-index: -1;
        background-image: ${props => {
        switch (props.$crits) {
            case 11:
                return 'url(img/systemicons/thruster1-critical12.png);'
            case 10:
                return 'url(img/systemicons/thruster1-critical1.png);'
            case 1:
                return 'url(img/systemicons/thruster1-critical2.png);'
            default:
                return 'url(img/systemicons/thruster1.png);'
        }
    }}
        background-size: cover;
        transform: ${props => {
        switch (props.$direction) {
            case 4:
                return "rotate(180deg)";
            case 1:
                return "rotate(90deg)";
            case 2:
                return "rotate(270deg)";
            default:
                return "none"
        }
    }};
        ${props => props.$destroyed ? 'opacity: 0.3;' : ''}
    }

    ${props => props.$destroyed ? `
    cursor: default;
    &::after {
        content: "✕";
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        line-height: 1;
        color: ${theme.colors.statusBad};
        pointer-events: none;
    }` : Clickable}
`;

const ThrusterContainer = styled.div`
    display: flex;
    position: absolute;
`;

/* The four groups stand --fv-ring-x / --fv-ring-y out from the centre (see getRingOffsets), which
   ShipThrust.layout writes onto ThrusterSetContainer. The fallbacks are the old fixed layout. */
const ForwardThrusterContainer = styled(ThrusterContainer)`
    flex-direction: row;
    left: var(--fv-ring-x, ${RING_X_MAX}px);
    transform: translate(0, -50%);
    flex-wrap: wrap;
    max-width: ${TILE}px;
`;

const AftThrusterContainer = styled(ForwardThrusterContainer)`
    left: calc(-1 * var(--fv-ring-x, ${RING_X_MAX}px) - ${TILE}px);
`;

const PortThrusterContainer = styled(ThrusterContainer)`
    flex-direction: row;
    top: calc(-1 * var(--fv-ring-y, ${RING_Y_MAX}px) - ${TILE}px);
    transform: translate(-50%, 0);
`;

const StarBoardThrusterContainer = styled(PortThrusterContainer)`
    top: var(--fv-ring-y, ${RING_Y_MAX}px);
`;

const ThrusterSetContainer = styled.div`
    position: relative;
    transform: rotate(${props => props.$rotation}deg);

    & ${Text} {
        transform: rotate(${props => -props.$rotation}deg);
    }
`;

/* A zero-size box on the ship's screen position. MovementPhaseStrategy.repositionThrustUi moves
   it with jQuery (by id) whenever the map scrolls or zooms, then fires THRUST_RELAYOUT_EVENT on it
   so the ring and panel can follow. */
const ThrustUIContainer = styled.div`
    position: absolute;
    transform: translate(-50%, -50%);
    z-index: 7002;
    display: flex;
    align-items: center;
    justify-content: center;
`;

/* ── Info panel ────────────────────────────────────────────────────────────────────────── */

/* The Gravitic Augmenter menu's green (theme.colors.green*), square-cornered. The translucency is
   in the fill's own alpha - deliberately WITHOUT the `opacity: 0.95` that menu carries, which
   would fade the text too and compound with the fill (see the alpha-compounding note in
   VISUAL_UNIFICATION_PLAN.md).
   left/top are written by ShipThrust.layout, relative to the ship. The width is explicit
   because the panel is absolutely positioned inside a zero-width box: left to shrink-to-fit
   it would collapse to its narrowest word. */
const ThrustPanel = styled.div`
    position: absolute;
    left: 0;
    top: 0;
    z-index: 1;
    width: 180px;
    box-sizing: border-box;
    background-color: ${theme.colors.greenBg};
    border: 1px solid ${theme.colors.greenLine};
    box-shadow: 5px 5px 10px ${theme.colors.overlayBg};
    font-family: ${theme.fonts.body};
    color: ${theme.colors.greenText};
    text-align: left;
    user-select: none;
`;

//The Augmenter menu's Header: centred, bold, on the darker green bar.
const PanelTitle = styled.div`
    box-sizing: border-box;
    padding: 3px 6px;
    line-height: 1.2;
    font-size: 11px;
    font-weight: bold;
    text-align: center;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    color: ${theme.colors.greenText};
    background-color: ${theme.colors.greenTitleBg};
    border-bottom: 1px solid ${theme.colors.greenLine};
`;

const PanelBody = styled.div`
    padding: 3px 6px 4px;
`;

const StatRow = styled.div`
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 6px;
    padding: 1px 0;
`;

const StatLabel = styled.span`
    font-size: 10px;
    color: ${theme.colors.greenLabel};
    white-space: nowrap;
`;

/* $state: 'ok' (paid in full), 'open' (still owed) or nothing (plain readout). */
const StatValue = styled.span`
    font-family: ${theme.fonts.mono};
    font-size: 11px;
    white-space: nowrap;
    color: ${props => {
        if (props.$state === 'ok') return theme.colors.statusOk;
        if (props.$state === 'open') return theme.colors.warning;
        return theme.colors.greenText;
    }};
`;

const Rule = styled.div`
    height: 1px;
    margin: 3px 0;
    background-color: ${theme.colors.greenLine};
`;

const ButtonRow = styled.div`
    display: flex;
    gap: 4px;
    padding: 0 6px 6px;
`;

/* The Augmenter menu's ActionButton. Text, not the old ✔ / 🛇, which drew differently on every
   platform (🛇 has no glyph at all in several Windows fonts).
   $primary: CONFIRM, which wears that menu's "active" look so it stands out from CANCEL.
   disabled: shown, not hidden, so the player can see it exists and hover it to read why it is not
   ready (its `title`). 32px tall on touch screens (§4.5). */
const PanelButton = styled.div`
    flex: 1 1 0;
    display: flex;
    align-items: center;
    justify-content: center;
    box-sizing: border-box;
    height: 20px;
    padding: 0 4px;
    font-size: 11px;
    white-space: nowrap;
    cursor: pointer;
    background: ${theme.colors.greenBtnBg};
    border: 1px solid ${theme.colors.greenBtnLine};
    color: ${theme.colors.greenText};

    &:hover {
        background: ${theme.colors.greenBtnLine};
        border: 1px solid ${theme.colors.greenBtnLineHover};
        color: #ffffff;
    }

    ${props => props.$primary && !props.disabled && `
        background: ${theme.colors.greenBtnLine};
        border: 1px solid ${theme.colors.greenBtnLineHover};
        box-shadow: 0 0 5px ${theme.colors.greenGlow};
        color: #ffffff;
    `}

    ${props => props.disabled && `
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: ${theme.colors.greenBtnBg}; border: 1px solid ${theme.colors.greenBtnLine}; color: ${theme.colors.greenText}; }
    `}

    @media (pointer: coarse) {
        height: 32px;
    }
`;

/* ── Helpers ───────────────────────────────────────────────────────────────────────────── */

const DIRECTION_NAMES = ['Either', 'Front', 'Aft', 'Port', 'Stbd'];
//Front, aft, port, starboard, then the "either" slot - the order the eye reads the ring.
const ROW_ORDER = [1, 2, 3, 4, 0];

const sideName = right => right ? 'Starboard' : 'Port';

const getTitle = (ship, movement) => {
    switch (movement.type) {
        case 'turnleft':
        case 'turnright':
            if (movement.value === 'turnIntoPivot') return 'Turn into Pivot · ' + sideName(movement.type === 'turnright');
            return 'Turn ' + sideName(movement.type === 'turnright');
        case 'pivotleft':
        case 'pivotright':
            return 'Pivot ' + sideName(movement.type === 'pivotright');
        case 'slipleft':
        case 'slipright':
            return 'Slip ' + sideName(movement.type === 'slipright');
        case 'roll':
            return movement.value === 'emergencyRoll' ? 'Emergency Roll' : 'Roll';
        case 'speedchange': {
            //A deceleration from speed 0 flips the heading and comes out at speed 1, so "slower
            //than before" alone would call it an acceleration.
            const index = ship.movement.lastIndexOf(movement);
            const previous = index > 0 ? ship.movement[index - 1] : null;
            const decel = previous && (movement.speed < previous.speed || movement.heading != previous.heading);
            return decel ? 'Decelerate' : 'Accelerate';
        }
        case 'jink':
            return 'Jink';
        case 'halfPhase':
            return 'Half-Phase';
        case 'contract':
            return 'Contraction';
        default:
            return 'Assign Thrust';
    }
};

/* One row per direction the manoeuvre asks for: paid/required. Mirrors the arithmetic of
   calculateThrustStillReq: thrust paid beyond a direction's own requirement overflows into the
   "either" slot, so slot 0 goes negative once the whole manoeuvre is overpaid - which, on a
   turn, is how the turn delay is shortened. A pivot's stillReq nulls the pair of directions
   the player did not choose, so those rows drop out once the first point is assigned. */
const getRequirementRows = (totalRequired, remainingRequired) => {
    if (!Array.isArray(totalRequired) || !Array.isArray(remainingRequired)) return { rows: [], extra: 0 };

    //"Any" when nothing else is asked for (rolls, jinks, Contraction, a turn at speed 0),
    //"Either" when it is the odd point left over beside a directional requirement.
    const onlyAny = [1, 2, 3, 4].every(i => !(totalRequired[i] > 0));

    const rows = [];
    ROW_ORDER.forEach(i => {
        const required = totalRequired[i];
        if (!(required > 0)) return;
        if (i > 0 && remainingRequired[i] === null) return;
        const open = Math.max(0, remainingRequired[i] || 0);
        rows.push({
            slot: i,
            label: i === 0 ? (onlyAny ? 'Any' : 'Either') : DIRECTION_NAMES[i],
            paid: required - open,
            required: required,
            open: open
        });
    });

    const extra = remainingRequired[0] < 0 ? -remainingRequired[0] : 0;
    return { rows, extra };
};

//The same test doneAssignThrust makes before it commits.
const isFullyPaid = remainingRequired =>
    Array.isArray(remainingRequired) && !remainingRequired.some(required => required > 0);

/* The on-screen box the panel must stay out of: the ring's tiles, and the ship itself out to `clear`
   (see getRingOffsets). The tiles are measured rather than worked out, because the ring turns with
   the ship; empty groups (directions this manoeuvre does not draw) are skipped. */
const getRingBox = (ring, ship, clear) => {
    const box = { left: ship.left - clear, top: ship.top - clear, right: ship.left + clear, bottom: ship.top + clear };
    Array.from(ring.children).forEach(group => {
        const r = group.getBoundingClientRect();
        if (r.width === 0 || r.height === 0) return;
        box.left = Math.min(box.left, r.left);
        box.top = Math.min(box.top, r.top);
        box.right = Math.max(box.right, r.right);
        box.bottom = Math.max(box.bottom, r.bottom);
    });
    return box;
};

class ShipThrust extends React.Component {

    constructor(props) {
        super(props);
        this.containerRef = React.createRef();
        this.ringRef = React.createRef();
        this.panelRef = React.createRef();
        this.layout = this.layout.bind(this);
    }

    componentDidMount() {
        this.layout();
        window.addEventListener('resize', this.layout);

        //repositionThrustUi moves the container with jQuery on every scroll and zoom, which React
        //never hears about, and then fires this event on it. An explicit signal rather than a watch
        //on the container's style: a zoom changes how far out the ring stands as well as where the
        //panel goes, and a ship at the very centre of the zoom does not move at all.
        this.container = this.containerRef.current;
        if (this.container) this.container.addEventListener(THRUST_RELAYOUT_EVENT, this.layout);
    }

    componentDidUpdate() {
        this.layout();
    }

    componentWillUnmount() {
        window.removeEventListener('resize', this.layout);
        if (this.container) this.container.removeEventListener(THRUST_RELAYOUT_EVENT, this.layout);
    }

    /* Sizes the ring for the current zoom (getRingOffsets), then places the panel against it. Runs
       after every render (componentDidMount runs before the first paint, so nothing flashes at the
       old size), on window resize, and whenever repositionThrustUi has moved the container. */
    layout() {
        const anchor = this.containerRef.current;
        const ring = this.ringRef.current;
        const panel = this.panelRef.current;
        if (!anchor || !ring || !panel) return;

        const { ship, totalRequired, movement } = this.props;
        const offsets = getRingOffsets(ship, totalRequired, movement);
        ring.style.setProperty('--fv-ring-x', offsets.x + 'px');
        ring.style.setProperty('--fv-ring-y', offsets.y + 'px');

        this.placePanel(anchor, ring, panel, offsets.clear);
    }

    /* Always against the ring, never docked away from it (user report 2026-10-01: at phone width the
       panel used to dock to the bottom of the screen, nowhere near its ship). Below the ring, else
       above, else to its right, else its left - the first that fits on screen. If none does, below,
       clamped into view. The ring is MEASURED (it turns with the ship), and so is the panel: the
       container is transformed, which makes it the containing block even for position: fixed. */
    placePanel(anchor, ring, panel, clear) {
        const ship = anchor.getBoundingClientRect();
        const box = getRingBox(ring, ship, clear);
        const screenWidth = window.innerWidth;
        const screenHeight = window.innerHeight;
        const width = panel.offsetWidth;
        const height = panel.offsetHeight;
        const clampX = x => Math.min(Math.max(x, SCREEN_EDGE), screenWidth - width - SCREEN_EDGE);
        const clampY = y => Math.min(Math.max(y, SCREEN_EDGE), screenHeight - height - SCREEN_EDGE);

        const below = { left: clampX(ship.left - width / 2), top: box.bottom + PANEL_GAP };
        const candidates = [
            below,
            { left: clampX(ship.left - width / 2), top: box.top - PANEL_GAP - height },
            { left: box.right + PANEL_GAP, top: clampY(ship.top - height / 2) },
            { left: box.left - PANEL_GAP - width, top: clampY(ship.top - height / 2) },
        ];
        const fits = place => place.left >= SCREEN_EDGE && place.top >= SCREEN_EDGE
            && place.left + width <= screenWidth - SCREEN_EDGE && place.top + height <= screenHeight - SCREEN_EDGE;
        const chosen = candidates.find(fits) || { left: below.left, top: clampY(below.top) };

        panel.style.left = Math.round(chosen.left - ship.left) + 'px';
        panel.style.top = Math.round(chosen.top - ship.top) + 'px';
    }

    //Stage 2b (SHIPWINDOW_REDESIGN_PLAN.md §4.3): the assign-thrust logic moved from the
    //legacy shipWindowManager to shipManager.movement. The old shipWindowManager.setData
    //calls were a legacy-window refresh — a no-op with that DOM never built — so they
    //are dropped here rather than carried over.
    ready() {
        window.shipManager.movement.doneAssignThrust(this.props.ship)
    };

    cancel() {
        window.shipManager.movement.cancelAssignThrustEvent(this.props.ship)
    };

    render() {
        const { ship, position, rotation, totalRequired, remainginRequired, movement } = this.props;
        const ringRotation = Math.round(Math.abs(rotation));
        const { rows, extra } = getRequirementRows(totalRequired, remainginRequired);
        const done = isFullyPaid(remainginRequired);
        const stillNeeded = rows.filter(row => row.open > 0).map(row => `${row.label} ${row.open}`).join(' · ');

        return (
            <ThrustUIContainer ref={this.containerRef} onMouseOver={(e) => e.preventDefault()} onContextMenu={(e) => e.preventDefault()} id="thrustUIContainer" style={{ left: `${position.x}px`, top: `${position.y}px` }}>
                <ThrusterSetContainer ref={this.ringRef} style={{ transform: `rotate(${ringRotation}deg)` }} $rotation={ringRotation}>
                    <ForwardThrusterContainer>
                        {getThrusters(ship, 1, totalRequired, remainginRequired, movement)}
                    </ForwardThrusterContainer>

                    <PortThrusterContainer>
                        {getThrusters(ship, 3, totalRequired, remainginRequired, movement)}
                    </PortThrusterContainer>

                    <StarBoardThrusterContainer>
                        {getThrusters(ship, 4, totalRequired, remainginRequired, movement)}
                    </StarBoardThrusterContainer>

                    <AftThrusterContainer>
                        {getThrusters(ship, 2, totalRequired, remainginRequired, movement)}
                    </AftThrusterContainer>
                </ThrusterSetContainer>

                <ThrustPanel ref={this.panelRef}>
                    <PanelTitle>{getTitle(ship, movement)}</PanelTitle>
                    <PanelBody>
                        {rows.map(row => (
                            <StatRow key={`thrust-row-${row.slot}`}>
                                <StatLabel>{row.label}</StatLabel>
                                <StatValue $state={row.open > 0 ? 'open' : 'ok'}>{row.paid}/{row.required}</StatValue>
                            </StatRow>
                        ))}
                        {extra > 0 &&
                            <StatRow><StatLabel>Extra thrust</StatLabel><StatValue $state="ok">+{extra}</StatValue></StatRow>}
                        <Rule />
                        <StatRow><StatLabel>Engine thrust left</StatLabel><StatValue>{shipManager.movement.getRemainingEngineThrust(ship)}</StatValue></StatRow>
                        {getTurnDelay(ship, movement)}
                    </PanelBody>
                    <ButtonRow>
                        <PanelButton
                            $primary
                            disabled={!done}
                            title={done ? 'Confirm this manoeuvre' : `Still needed: ${stillNeeded}`}
                            onClick={done ? this.ready.bind(this) : undefined}>
                            Confirm
                        </PanelButton>
                        <PanelButton onClick={this.cancel.bind(this)}>Cancel</PanelButton>
                    </ButtonRow>
                </ThrustPanel>
            </ThrustUIContainer>
        )
    }
}

const getTurnDelay = (ship, movement) => {

    if (!shipManager.movement.isTurn(movement)) {
        return null;
    }

    const turndelay = shipManager.movement.calculateTurndelay(ship, movement, movement.speed);
    return (<StatRow><StatLabel>Turn delay</StatLabel><StatValue>{turndelay}</StatValue></StatRow>)
}

/* A direction's tiles are drawn only when the manoeuvre asks for that direction (null means
   "not involved"). A roll draws all four whatever its requirement says, since any thruster may
   pay for it.
   This used to be declared (ship, direction, totalRequired, movement) and called with five
   arguments, so `movement` actually held the stillReq array and the roll test never fired:
   rolls only worked because their requirement uses 0 rather than null. */
const getThrusters = (ship, direction, totalRequired, remainingRequired, movement) => {
    const thrusters = shipManager.systems.getThrusters(ship, direction);

    if (movement.type !== 'roll' && totalRequired[direction] === null) {
        return null;
    }

    //Still owed in this direction, or in the "either" slot, which any drawn thruster can pay. A point
    //this thruster takes then pays what is needed; once neither is owed, it can only be extra thrust.
    const needed = Array.isArray(remainingRequired) && (remainingRequired[direction] > 0 || remainingRequired[0] > 0);

    return thrusters.map((thruster, index) => {
        const destroyed = shipManager.systems.isDestroyed(ship, thruster);
        //Boxed only if a click would actually be taken (a dry run of assignThrust).
        const box = destroyed || !shipManager.movement.wouldAcceptThrust(ship, thruster)
            ? undefined
            : (needed ? 'need' : 'extra');

        const assignThrust = () => {
            shipManager.movement.assignThrust(ship, thruster);
            shipManager.movement.updateAssignThrust(ship);
        }

        const unAssignThrust = (e) => {
            e.preventDefault();
            shipManager.movement.unAssignThrust(ship, thruster);
            shipManager.movement.updateAssignThrust(ship);
        }

        let crits = shipManager.criticals.hasCritical(thruster, "HalfEfficiency") ? 10 : 0;


        if (shipManager.criticals.hasCritical(thruster, "FirstThrustIgnored")) {
            crits += 1
        }


        const channeled = shipManager.movement.getAmountChanneled(ship, thruster);
        const output = shipManager.systems.getOutput(ship, thruster);
        return (
            <Thruster
                $crits={crits}
                $direction={direction}
                $destroyed={destroyed}
                $box={box}
                onClick={destroyed ? undefined : assignThrust}
                onContextMenu={destroyed ? (e) => e.preventDefault() : unAssignThrust}
                key={`thruster-${direction}-${index}`}>
                <Text $over={channeled > output}>{channeled}/{output}</Text>
            </Thruster>
        )
    });
}

export default ShipThrust;
