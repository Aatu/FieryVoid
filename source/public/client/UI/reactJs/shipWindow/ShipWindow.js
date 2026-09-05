import * as React from "react";
import styled from "styled-components"

import { Clickable } from "../styled";
import theme from "../styled/theme";
import ShipSection from "./ShipSection";
import ShipWindowEw from "./ShipWindowEw";
import FighterList from "./FighterList";
import HitChartPanel from "./HitChartPanel";
import ShipNotesPanel, { ManoeuvreStats, EnhancementsPanel, StatsIcon } from "./ShipNotesPanel";
import ShipInfo from "../system/ShipInfo";

/*"Digital SCS" ship window (SHIPWINDOW_REDESIGN_PLAN.md Stage 1): sections arranged
  geographically on a CSS grid like the paper Ship Control Sheets - Forward top, Port
  left, Starboard right, Aft bottom, Primary centre - over a monochrome watermark of
  the ship's own art. Six-sided ships and big bases get their four quarter sections at
  side-column height with Primary spanning the middle rows (the layout fix the old
  flex rows could never do: Primary only centres between Forward and Aft if it shares
  one grid band with ALL side sections).

  The grid's top row is chrome: "ctrl fwd ew" - Hit Chart / Notes buttons top-left
  (click opens a popup, clicking anywhere outside closes it), Forward section centre,
  vertical EW panel top-right (2026-07-16 feedback round; replaces both the old
  hover-pin ⊕ in the header and the footer EW strip).

  Rolled ships (user request 2026-07-16): port/starboard grid columns swap while the
  ship is rolled - the window then matches what the enemy actually faces - with an
  amber ROLLED banner at the bottom of the window confirming the mirroring. Section
  headers keep their true names (the port section stays "PORT", it is just drawn on
  the right).

  Lobby mode (Stage 3, gamedata.gamephase === -2): the same component doubles as the
  fleet-selection datasheet. No EW panel (meaningless pre-game); the ctrl column
  keeps the Hit Chart button in its game.php position with the manoeuvre stats
  (ManoeuvreStats) stacked beneath it; the Notes popup is replaced by an
  always-visible ShipNotesPanel rail to the RIGHT of the section grid (complement /
  notes / enhancements can never be obscured by ship icon elements). Window sides:
  store blueprints open left, own-fleet ships right - the legacy lobby's
  userid == 0 split, resolved by ShipWindowManager.isLeftSide.*/

const ShipWindowContainer = styled.div`
    display: flex;
    flex-direction: column;
    position: absolute;
    ${props => {
        if (props.$isMyTeam) {
            return "left: 50px; \n top: 50px;"
        } else {
            return "right: 50px; \n top: 50px;"
        }
    }}
    width: ${props => {
        if (props.$variant === 'terrain') return '250px';
        if (props.$variant === 'flight') return 'auto';
        return 'fit-content';
    }};
    max-width: ${props => {
        if (props.$variant === 'flight') return '400px';
        if (props.$variant === 'flightLobby') return '620px'; /*FighterList + datasheet rail*/
        return 'unset';
    }};
    height: auto;
    border: 1px solid ${theme.colors.line};
    background-color: ${theme.colors.windowBg};
    opacity: 0.95;
    z-index: 10001;
    pointer-events: auto; /*the lobby mounts windows inside a pointer-events: none fixed overlay*/
    overflow: visible; /*lets the Hit Chart / Notes popup extend past the window; the watermark is clipped by the body instead*/
    box-shadow: 5px 5px 10px black;
    font-size: 10px;
    color: ${theme.colors.text};
    font-family: ${theme.fonts.body};

    /* Prevent text selection and callouts on mobile */
    -webkit-user-select: none;
    -webkit-touch-callout: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;

    @media (max-width: 1024px) {
        /*docked a few px in from the screen edge, not flush against it: at top: 0 the drag
          handle sits under the browser's own top-edge gesture area and is awkward to grab
          (user report 2026-07-23)*/
        ${props => {
        if (props.$isMyTeam) {
            return "left: 4px; \n top: 8px; \n right: unset;"
        } else {
            return "right: 4px; \n top: 8px; \n left: unset;"
        }
    }}
        /*Touch screens size the window from its CONTENT ONLY, never from the viewport, so
          it lays out identically in portrait and landscape and applyScreenFit() just scales
          that one fixed layout to whatever screen it lands on (user request 2026-07-23).
          fit-content / auto are available-width dependent: in landscape the extra room
          let a flight window stretch its FighterList into one long row and a single-Primary
          ship spread out, while portrait wrapped them. max-content + the same variant caps
          the desktop rule uses (the caps are what make FighterList wrap at all) removes the
          viewport from the equation. The old 100vw clamp is gone with them - clamping the
          LAYOUT width just squeezed the fixed-width sections into an internally-scrolling
          box (the "too wide in game, too narrow in the lobby" report).*/
        width: ${props => props.$variant === 'terrain' ? '250px' : 'max-content'};
        max-width: ${props => {
        if (props.$variant === 'flight') return '400px';
        if (props.$variant === 'flightLobby') return '620px';
        return 'none';
    }};
        max-height: 100vh;
        /*auto, not scroll: scroll pins a permanent (usually inert) scrollbar to
          the window on classic-scrollbar platforms even when nothing overflows.
          When it does engage, it wears the site-standard scrollbar (same as
          PopupHolder / #gameinfo / the log panel).*/
        overflow-y: auto;
        /*scrolling the window's own overflow must not chain into the page underneath -
          on the lobby that hands the gesture to the page (and to pull-to-refresh at the
          top), which is exactly what steals a drag mid-flight*/
        overscroll-behavior: contain;

        scrollbar-width: thin;
        scrollbar-color: #3c5574 #0d1620;

        &::-webkit-scrollbar {
            width: 10px;
        }
        &::-webkit-scrollbar-track {
            background: #0d1620;
        }
        &::-webkit-scrollbar-thumb {
            background: #3c5574;
        }
        &::-webkit-scrollbar-thumb:hover {
            background: #5a7ea8;
        }
    }
`;

const Header = styled.div`
    /*sticky, not relative (2026-08-06): on a small screen the container is its own scroll
      box (overflow-y: auto + the fitted max-height), and a plain header scrolls straight
      out of it - the player then swipes what looks like the top of the window and gets the
      body, with the only drag handle parked above the visible area. Sticky pins it to the
      top of the scroll box, so the handle is always where the window's top edge is. On
      desktop, where the container never scrolls, this renders identically to relative.
      Still a containing block for the absolutely-positioned close button.*/
    position: sticky;
    top: 0;
    z-index: 4; /*above the section grid (2) so the pinned bar is never drawn through*/
    background-color: ${theme.colors.panelBg};
    border-bottom: 1px solid ${theme.colors.line};
    height: 26px;
    display: flex;
    align-items: baseline; /*name + class share a text baseline (different font sizes)*/
    gap: 6px;
    padding: 0 26px 0 5px; /*right padding clears the ✕ button*/
    width: 100%;
    box-sizing: border-box;
    flex-shrink: 0;
    cursor: move;
    /*hand the touch gesture to our drag instead of scrolling the page (2026-07-23 -
      without this a finger-drag on the header just scrolls the lobby)*/
    touch-action: none;

    /*NO small-screen height override (user request 2026-07-23, round 12): the header
      hugs its text on every screen, exactly like desktop, and shrinks with the rest of
      the window. Round 10 had made it a flat 44px finger strip and round 11 counter-scaled
      that to a constant ~44 VISUAL px - both read as a disproportionately fat bar once the
      window was scaled down. Cost: the grab target is now 26px * scale (~13-16px on a
      phone). If dragging turns fiddly again, grow the TARGET without growing the BAR (a
      transparent hit-area) rather than restoring a taller header.*/
`;

const HeaderName = styled.span`
    font-size: 11px;
    line-height: 26px; /*centres the shared baseline within the 26px header bar*/
    text-transform: uppercase;
    letter-spacing: 0.5px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    min-width: 0;
    flex-shrink: 1; /*long flight names ellipsise instead of pushing past the ✕*/
    color: ${props => props.$tint || theme.colors.text};
`;

const HeaderClass = styled.span`
    font-size: 9px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    color: ${theme.colors.textAccent};
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    min-width: 0; /*allow flex shrink so the ellipsis can engage*/
    flex-shrink: 3; /*the class gives way before the ship name does*/
`;

const CloseButton = styled.div`
    width: 25px;
    height: 25px;
    position: absolute;
    right: 0;
    top: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    padding-left: 5px;
    margin-top: -2px;
    color: ${theme.colors.line};
    ${Clickable}
`;

/*Bottom-corner resize grip (user request 2026-08-06): drag it to scale the whole window,
  double-click/double-tap to go back to 100%. The window's LAYOUT size is dictated by fixed
  section/chrome widths and cannot reflow (see the container's media query), so "resize"
  here means the same CSS scale applyScreenFit() already applies - the player's own
  multiplier on top of the automatic fit. That is also why the mark sits in the flow as the
  window's last row rather than floating over the bottom corner: nothing of the datasheet is
  covered. $overlap is the one exception - see renderStatusStrip.

  WHICH corner follows the dock ($mirror, user request 2026-08-06): bottom-RIGHT for a
  left-docked window, bottom-LEFT for a right-docked one. Each window is pinned to its screen
  edge and grows away from it, so the grip has to sit on the corner that actually moves -
  otherwise the finger drags towards the screen edge it is already against while the window
  expands out of the far side. Everything else mirrors with it: the triangle of rules, the
  diagonal they run along, the resize cursor, and the direction the invisible finger pad
  reaches (always INTO the window - the small-screen container clips its overflow, so a pad
  hanging outside would not be hit-testable).

  Sticky so it stays at the bottom of the window even when the window is scrolling
  internally. The stripes are drawn on ::after so the ::before finger pad, which reaches
  beyond the 16px mark, is not clipped away with them (clip-path applies to the element AND
  its pseudo-elements).*/
const ResizeGrip = styled.div`
    position: sticky;
    bottom: 0;
    align-self: ${props => props.$mirror ? 'flex-start' : 'flex-end'};
    flex-shrink: 0;
    width: 16px;
    height: 16px;
    /*$overlap: draw the mark ON TOP of the row above (a status banner) instead of taking a
      row of its own - the negative margin is exactly the grip's own height, so the row above
      keeps the window's bottom edge. See renderStatusStrip.*/
    ${props => props.$overlap ? 'margin-top: -16px;' : ''}
    box-sizing: border-box;
    z-index: 5;
    cursor: ${props => props.$mirror ? 'nesw-resize' : 'nwse-resize'};
    /*the grip owns the gesture: without this a finger drag scrolls the window/page instead*/
    touch-action: none;

    &::after { /*three diagonal rules clipped to the corner triangle - the standard grip mark*/
        content: "";
        position: absolute;
        inset: 0;
        background: repeating-linear-gradient(${props => props.$mirror ? '45deg' : '315deg'}, ${theme.colors.line} 0 1.5px, transparent 1.5px 4px);
        clip-path: ${props => props.$mirror ? 'polygon(0 0, 0 100%, 100% 100%)' : 'polygon(100% 0, 100% 100%, 0 100%)'};
        opacity: 0.85;
    }

    &::before { /*finger pad - see the note above*/
        content: "";
        position: absolute;
        top: -${props => props.$pad}px;
        bottom: 0;
        left: ${props => props.$mirror ? '0' : '-' + props.$pad + 'px'};
        right: ${props => props.$mirror ? '-' + props.$pad + 'px' : '0'};
    }

    &:hover::after {
        opacity: 1;
    }
`;

/*Hit Chart / Notes button stack, top-left of the window body (the empty corner cell
  of the SCS grid), centred within its column. Click opens the matching popup;
  clicking anywhere outside closes it (document pointerdown listener). Compact
  windows (mines/terrain) render it as a full-width row above the sections instead
  ($row).*/
const ControlsArea = styled.div`
    grid-area: ctrl;
    justify-self: center;
    align-self: start;
    position: relative; /*above the watermark + ship-click underlay*/
    z-index: 2;
    display: flex;
    flex-direction: column;
    ${props => props.$compact ? 'width: 100%; align-items: center; margin-bottom: 5px;' : 'align-items: stretch;'}
    gap: 4px;
`;

const CtrlButton = styled.div`
    display: flex;
    /*center: icon + label are vertically centred in the button, consistent with every
      other chrome title/header bar (user request 2026-07-22)*/
    align-items: center;
    line-height: 1;
    gap: 4px;
    padding: 3px 6px 3px 4px;
    box-sizing: border-box;
    /*chrome column width: 150px datasheet panels in the lobby ($wide), 130px in game
      (user 2026-07-19: 150 was too wide on the game screen, 120 too tight) - matches the
      EW / Enhancements panels on each page so the two chrome columns stay symmetric*/
    min-width: ${props => props.$wide ? '150px' : '130px'};
    border: 1px solid ${theme.colors.line};
    /*idle fill = the shaded header-bar blue (same as the hit chart section names)
      so the chrome buttons read as section headers (feedback 2026-07-17)*/
    background-color: ${props => props.$active ? 'rgba(198, 226, 255, 0.12)' : 'rgba(73, 103, 145, 0.25)'};
    color: ${theme.colors.text}; /*white like the Ship Stats title (feedback round 3)*/
    font-size: 8px;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    white-space: nowrap;
    ${Clickable}
`;

const CtrlIcon = styled.span`
    font-size: 12px;
    line-height: 1;
    color: inherit;
`;

/*Small monochrome "picture" glyph for the Ship Art toggle (item 4, 2026-07-22). Drawn
  in CSS rather than an emoji to stay monochrome and match the chrome - same convention
  as ShipNotesPanel's StatsIcon. A framed box with a sun (top-right dot) and a mountain
  (bottom-left triangle): the universal image/graphic mark. currentColor tracks the
  button's text colour.*/
const ArtIcon = styled.span`
    position: relative;
    display: inline-block;
    flex: 0 0 auto;
    align-self: center;
    box-sizing: border-box;
    width: 12px;
    height: 10px;
    border: 1px solid currentColor;
    overflow: hidden;
    &::before { /*sun*/
        content: "";
        position: absolute;
        top: 1.5px;
        right: 1.5px;
        width: 2.5px;
        height: 2.5px;
        border-radius: 50%;
        background-color: currentColor;
    }
    &::after { /*mountain*/
        content: "";
        position: absolute;
        left: -1px;
        bottom: -1px;
        width: 0;
        height: 0;
        border-left: 5px solid transparent;
        border-right: 7px solid transparent;
        border-bottom: 5px solid currentColor;
    }
`;

/*Shared popup box for the Hit Chart / Notes panels: anchored below the control
  buttons, spanning the window body. A direct child of the window container so it
  can extend BELOW the window (overflow: visible); capped at 70vh with the site's
  standard scrollbar (matches #gameinfo / the log panel).*/
const PopupHolder = styled.div`
    position: absolute;
    top: ${props => props.$top || 78}px;
    /*left edge aligns with the control buttons (measured - they sit centred in the
      grid's ctrl column, not at the window's 6px margin); feedback 2026-07-19*/
    left: ${props => props.$left != null ? props.$left : 6}px;
    /*$fit (Notes): size to content instead of spanning the window*/
    ${props => props.$fit
        ? 'right: auto; width: fit-content; max-width: calc(100% - 12px);'
        : 'right: 6px;'}
    /*Notes popup never narrower than the 130px Notes button it drops from (both
      border-box), so short notes still read as one block under the button*/
    ${props => props.$notes ? 'min-width: 130px;' : ''}
    z-index: 20;
    max-height: 70vh;
    overflow-y: auto;
    box-sizing: border-box;
    background-color: ${theme.colors.panelBg};
    border: 1px solid ${theme.colors.line};
    box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.7);
    /*Notes ($notes) trims the bottom gap to ~5px (feedback 2026-07-19; paired with
      ShipInfo dropping its trailing blank line); Hit Chart keeps the extra bottom
      padding so the last chart rows never look clipped*/
    padding: 5px 5px ${props => props.$notes ? '5px' : '10px'};
    cursor: default;

    scrollbar-width: thin;
    scrollbar-color: #3c5574 #0d1620;

    &::-webkit-scrollbar {
        width: 10px;
    }
    &::-webkit-scrollbar-track {
        background: #0d1620;
    }
    &::-webkit-scrollbar-thumb {
        background: #3c5574;
    }
    &::-webkit-scrollbar-thumb:hover {
        background: #5a7ea8;
    }
`;

/*The geographic grid. The side tracks are 1fr each so they always resolve to the SAME
  width (the wider of the two) - this keeps the centre column, and therefore the
  sections, aligned with the window's midline and the watermark even when the chrome
  row is asymmetric (small button stack left, 120px EW panel right). Section widths
  (128/156px, set in ShipSection) keep icon rows at the 3-wide / 4-wide lengths the
  symmetric ordering expects.*/
const SectionGrid = styled.div`
    position: relative;
    display: grid;
    grid-template-columns: 1fr auto 1fr;
    grid-template-areas: ${props => props.$areas};
    justify-content: center;
    gap: 8px;
    padding: 5px 5px 5px 5px;
    box-sizing: border-box;
    width: 100%;
    overflow: hidden; /*clips the watermark now that the window itself is overflow: visible*/
`;

/*Lobby flight body: FighterList on the left, always-visible ShipNotesPanel rail to
  its RIGHT (grid windows put the panel in the `ew` grid area instead). nowrap, not
  wrap: with the window's fit-content width the wrap sizing collapsed and dropped the
  rail below the fighters (feedback round 4) - a single row plus FighterList's own
  internal icon wrapping is what we actually want.*/
const LobbyBody = styled.div`
    display: flex;
    flex-wrap: nowrap;
    align-items: stretch;
    width: 100%;
`;

const FlightArea = styled.div`
    flex: 1 1 auto;
    min-width: 120px; /*at least one fighter icon column*/
    max-width: 400px;
`;

/*Hull art behind the sections, nose-up like the old thumbnail. Purely decorative:
  pointer-events pass through to the ship hit underlay below. Sized as a square from the
  body's HEIGHT (clamped by width) so short windows never crop the art vertically - a
  centre-cropped slice is what made satellites look "stretched".

  $art (Ship Art toggle, item 3/4): the SAME layer turns full colour in place - no
  grayscale, full opacity - while the sections are hidden around it. Because it is the
  very same element at the very same size, toggling never resizes or shifts the image
  (the earlier separate-overlay approach nudged the art, which read as jarring).

  $offsetY: pushes the art DOWN by n px (lobby, 2026-07-23). The layer is centred on the
  whole grid, but the lobby's chrome row is far taller than game.php's (Ship Stats +
  datasheet + Enhancements), which pushes the section cluster below the grid's midline -
  the art then sits high of the sections it is meant to sit behind. Applied before the
  rotation so it is a screen-space nudge, not a nose-ward one.*/
const WatermarkLayer = styled.div`
    position: absolute;
    top: 50%;
    left: 50%;
    height: 80%;
    width: auto;
    aspect-ratio: 1 / 1;
    max-width: 80%;
    max-height: 380px;
    transform: translate(-50%, calc(-50% + ${props => props.$offsetY || 0}px)) rotate(-90deg);
    background-image: ${props => `url(${props.$img})`};
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center;
    filter: ${props => props.$art ? 'none' : 'grayscale(1) brightness(2.2)'};
    opacity: ${props => props.$art ? 1 : 0.75};
    pointer-events: none;
    z-index: 0;
`;

/*Ship-level click surface (replaces the old corner thumbnail): covers the grid
  background - the gaps between sections and the watermark - while the sections
  themselves sit above it on z-index 1, so system icons keep their own events.
  Hover deliberately does nothing (2026-07-16 feedback): the old ship tooltip's
  content now lives in the Hit Chart / Notes popups.*/
const ShipHitArea = styled.div`
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 0;
`;

/*Bottom-of-window status banner. The amber ROLLED banner was the first; user request
  2026-07-18 generalised it to carry the map tooltip's ship-level status lines too
  (Undetected / boarding / attached) - $color/$bg select the status colour.*/
const StatusBanner = styled.div`
    width: 100%;
    box-sizing: border-box;
    /*equal top/bottom: the old 2px-top/3px-bottom made the text sit visibly high in
      the tinted strip (the border-top reads as a separator line, not banner fill,
      so it doesn't compensate). At 9px uppercase every half-pixel shows.

      $grip: the resize grip is drawn in this banner's corner (renderStatusStrip), so the
      16px mark plus the usual 6px gutter is reserved - on BOTH sides, because a one-sided
      reserve would push the centred text off the window's midline, and which corner the
      grip sits on follows the dock.*/
    padding: 3px ${props => props.$grip ? '22px' : '6px'};
    text-align: center;
    font-size: 9px;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: ${props => props.$color || theme.colors.warning};
    background-color: ${props => props.$bg || 'rgba(225, 176, 0, 0.10)'};
    border-top: 1px solid ${theme.colors.line};
    flex-shrink: 0;
`;

const CompactBody = styled.div`
    position: relative; /*watermark anchor for the compact variant*/
    width: 100%;
    min-height: 120px; /*room for the watermark art in sparse windows (mines)*/
    display: flex;
    flex-wrap: wrap;
    justify-content: space-around;
    align-items: flex-start;
    padding: 2px;
    box-sizing: border-box;
    overflow: hidden; /*clips the watermark now that the window itself is overflow: visible*/
`;

const UnknownSystemIcon = styled.div`
    position: relative;
    box-sizing: border-box;
    width: 50px;
    height: 50px;
    margin: auto;
    border: 1px solid ${theme.colors.line};
    background-color: black;
    color: #e3c182;
    font-family: ${theme.fonts.body};
    font-size: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    -webkit-user-select: none;
    -webkit-touch-callout: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
`;

//location → grid-area name; rolled ships swap the port/starboard columns
const GRID_AREAS = { 1: 'fwd', 2: 'aft', 0: 'prim', 3: 'left', 4: 'right', 31: 'lfwd', 41: 'rfwd', 32: 'laft', 42: 'raft' };
const MIRRORED_LOCATION = { 3: 4, 4: 3, 31: 41, 41: 31, 32: 42, 42: 32 };
//vertical alignment inside the grid band, per area (the Stage 1 lesson: Primary only
//centres between Forward and Aft because it shares the middle band with all sides).
//fwd is 'end', not 'center': when the chrome stacks DO inflate row 1 (ships whose
//side cells hold systems, e.g. MCVs like the Hawk Frigate, where the round-2 chrome
//spans are blocked), Forward hugs Primary and the spare space moves to the window
//top over the watermark art instead of opening a gap mid-ship; in an uninflated
//row 1 'end' renders identically to 'center'.
const GRID_VALIGN = { fwd: 'end', aft: 'center', prim: 'center', left: 'center', right: 'center', lfwd: 'start', rfwd: 'start', laft: 'end', raft: 'end' };
//horizontal alignment: side sections hug the centre column - port drawn right-aligned
//mirroring starboard's left alignment (feedback 2026-07-17; visible when the side
//tracks are wider than a section, e.g. next to the 150px lobby datasheet)
const GRID_JUSTIFY = { left: 'end', lfwd: 'end', laft: 'end', right: 'start', rfwd: 'start', raft: 'start' };
//render order is irrelevant for grid placement; kept geographic for DOM readability
const GRID_LOCATIONS = [1, 3, 31, 32, 0, 4, 41, 42, 2];
const COMPACT_LOCATIONS = [1, 3, 31, 0, 4, 41, 32, 2, 42];
const QUARTER_LOCATIONS = [31, 32, 41, 42];
//mid + quarter Port/Starboard: the locations that make buildTemplateAreas emit a
//middle row, which is what blocks the ctrl/ew chrome spans (see LOBBY_WATERMARK_OFFSET_Y)
const SIDE_LOCATIONS = [3, 4, 31, 41, 32, 42];

class ShipWindow extends React.Component {
    constructor(props) {
        super(props);
        this.elementRef = React.createRef();
        this.controlsRef = React.createRef();
        this.popupRef = React.createRef();
        //the Hit Chart popup drops from directly below this button (the button sits in the
        //top-left control block; measuring the button rather than the whole block keeps the
        //lobby popup attached to it, above the Ship Stats panel)
        this.hitChartBtnRef = React.createRef();
        //openPanel: clicked open (sticky until click-outside); hoverPanel: desktop
        //hover peek on a button/popup, hides shortly after mouse-out (Notes, and Ship
        //Stats since 2026-07-26); showArt: Ship Art toggle - hides the sections and
        //shows the hull art in full colour
        this.state = { openPanel: null, hoverPanel: null, showArt: false }; //both panels: null | 'hitchart' | 'shipstats' | 'notes'
        this.panelHoverTimer = null;
        this.onDocumentPointerDown = this.onDocumentPointerDown.bind(this);
        this.onDragStart = this.onDragStart.bind(this);
        this.onDragMove = this.onDragMove.bind(this);
        this.onDragEnd = this.onDragEnd.bind(this);
        this.onTouchDragStart = this.onTouchDragStart.bind(this);
        this.onTouchDragMove = this.onTouchDragMove.bind(this);
        this.onTouchDragEnd = this.onTouchDragEnd.bind(this);
        this.onScreenResize = this.onScreenResize.bind(this);
        this.onGripDoubleClick = this.onGripDoubleClick.bind(this);
        this.screenFit = 1; //TOTAL applied scale factor (auto fit x user scale; 1 = untransformed)
        this.screenFitHeight = null; //applied max-height (layout px) that goes with it
        this.autoFit = 1; //the automatic half of screenFit (always 1 on desktop)
        //the other half - the player's own multiplier from the resize grip - is deliberately
        //NOT per-window state; it belongs to the screen side this window docks to (getUserScale)
    }

    /*Which of the two screen-edge slots this window occupies. Everything remembered per side
      - the drag position (savedWindowPositions) and, since 2026-08-06, the chosen size - keys
      off this one call, and so does the grip's corner: the docked edge is the one that must
      stay put while the window grows.*/
    side() {
        return isLeftWindow(this.props.ship) ? 'left' : 'right';
    }

    /*Right-docked windows carry their resize grip on the bottom-LEFT (user request
      2026-08-06). The grip belongs on the corner that MOVES: a right-docked window is pinned
      to the right edge of the screen, so it can only grow leftwards and downwards, and a
      bottom-right grip on it would sit on the one corner that stays still - the finger would
      run off the screen edge with the window barely following.*/
    isMirroredGrip() {
        return this.side() === 'right';
    }

    /*Touch-screen fit (user request 2026-07-23). The window's size is dictated by fixed
      section/chrome widths, so on a phone it is simply the wrong size: game.php's window
      overflows the screen while the lobby's - whose layout viewport the browser widens to
      fit the page - looks lost in the middle of it. Rather than fight the layout, the whole
      window is CSS-scaled to fit the screen, origin at the edge it is docked to.

      The scale is the SMALLER of the width and height ratios (2026-07-23 follow-up): a
      width-only fit blew the window up to full width in landscape, where height is the
      scarce dimension, and cut the bottom off. Both natural dimensions are measured with
      the height clamp lifted (inline max-height: none beats the media query's 100vh, and
      absolutely-positioned children like the Hit Chart popup never count toward
      offsetHeight, so an open popup can't shrink the window). max-height is then written
      back as a LAYOUT value (budget height / scale) - a shrunk window would otherwise keep
      an inner scrollbar for space it no longer needs, and a window that bottoms out on the
      floor still scrolls rather than being cut off.

      The BUDGET is per page (round 11, >>> TOUCH-SCREEN FIT <<<): game.php only spends a
      fraction of the screen so the tactical map underneath stays playable, the lobby fills
      it. The header hugs its text and scales with everything else (round 12) - no special
      handling.

      Since 2026-08-06 the applied scale is `autoFit x userScale`: the resize grip lets the
      player set their own multiplier on top of the automatic fit (and on desktop, where the
      fit is always 1, it is the only thing scaling the window). The two are kept apart so
      rotating the device still re-fits sensibly around whatever size the player chose. The
      product is clamped once, here, so nothing else has to worry about limits.*/
    applyScreenFit() {
        const element = this.elementRef.current;
        if (!element) return;

        const small = isSmallScreen();
        const userScale = getUserScale(this.side());

        if (!small && userScale === 1) {
            //desktop at natural size: no transform at all, exactly as before the grip existed
            if (this.screenFit !== 1 || this.screenFitHeight) {
                element.style.transform = '';
                element.style.transformOrigin = '';
                element.style.maxHeight = '';
                this.screenFit = 1;
                this.screenFitHeight = null;
            }
            this.autoFit = 1;
            return;
        }

        const natural = this.measureNatural();
        if (!natural) return;

        const budget = small ? screenFitBudget() : null;
        const viewportHeight = window.innerHeight || document.documentElement.clientHeight;

        let fit = 1;
        if (small) {
            const availableWidth = (document.documentElement.clientWidth || window.innerWidth) * budget.fillW;
            const availableHeight = viewportHeight * budget.fillH;
            fit = Math.min(availableWidth / natural.width, availableHeight / natural.height);
            fit = Math.min(budget.max, Math.max(budget.min, fit));
        }
        this.autoFit = fit;

        let scale = clampScale(fit * userScale);
        scale = Math.round(scale * 100) / 100; //2dp: stops poll re-renders jittering the scale

        /*The height clamp is a LAYOUT value (budget height / scale): a shrunk window would
          otherwise keep an inner scrollbar for space it no longer needs, and one that bottoms
          out on the legibility floor scrolls internally rather than being cut off. A window
          the player has deliberately ENLARGED is them overriding the map budget, so its
          allowance grows with the user scale - but never past MAX_FILL of the screen, or the
          bottom of a top-docked window would hang off it. Desktop keeps no clamp (the
          container has none in the first place).*/
        let maxHeight = null;
        if (small) {
            const heightFill = Math.min(MAX_FILL, budget.fillH * Math.max(1, userScale));
            maxHeight = Math.round(viewportHeight * heightFill / scale);
        }

        if (scale === this.screenFit && maxHeight === this.screenFitHeight) return; //nothing to write

        this.screenFit = scale;
        this.screenFitHeight = maxHeight;
        element.style.transformOrigin = this.transformOrigin();
        element.style.transform = scale === 1 ? '' : 'scale(' + scale + ')';
        element.style.maxHeight = maxHeight == null ? '' : maxHeight + 'px';

        //NOT while the grip is being dragged: shifting the window mid-gesture would move the
        //corner out from under the finger, and the projection measures from where the corner
        //started - it would chase itself. finishGesture runs it once on release instead.
        if (!this.resizeStart) this.keepGripOnScreen();
    }

    /*Natural (untransformed, unclamped) size in layout px. A transform never affects layout
      so offsetWidth/Height stay honest, but the height clamp from the last fit has to come
      off first - inline max-height: none beats the media query's, and absolutely-positioned
      children like the Hit Chart popup never count toward offsetHeight, so an open popup
      cannot shrink the window.*/
    measureNatural() {
        const element = this.elementRef.current;
        if (!element) return null;

        const appliedHeight = element.style.maxHeight;
        element.style.maxHeight = 'none';
        const width = element.offsetWidth;
        const height = element.offsetHeight;
        element.style.maxHeight = appliedHeight;

        return (width && height) ? { width: width, height: height } : null;
    }

    /*Which corner stays put when the window is scaled. A docked window anchors to the edge
      it docks to, so scaling can never push it off that edge. Once it has been RESIZED it is
      positioned by explicit left/top (beginResize freezes the painted geometry) and anchors
      top-left - which is what keeps the bottom-right grip under the finger as the window
      grows. Plain drags deliberately do NOT switch the origin: a right-docked window's
      painted box sits a constant naturalWidth * (1 - scale) left of its layout box, and
      changing the origin mid-life would jump it by exactly that.*/
    transformOrigin() {
        if (this.resizeOrigin) return this.resizeOrigin;
        return isLeftWindow(this.props.ship) ? 'top left' : 'top right';
    }

    onScreenResize() {
        this.applyScreenFit();
    }

    /*Header drag AND corner resize (drag 2026-07-23, replacing jQuery UI `draggable`, whose
      mouse widget binds mousedown/mousemove only - a touchscreen never produced those, so
      windows could not be moved by finger at all; resize 2026-08-06).

      TWO engines, deliberately: a MOUSE/PEN one on pointer events, and a separate TOUCH
      one on touch events. A single pointer-events path looked cleaner but is fragile on
      touch - the browser cancels the whole pointer stream (pointercancel, no further
      pointermove) the moment anything claims the gesture: a long-press context menu, a
      scroll container taking over, a stale captured node. Touch events keep firing
      regardless, and `preventDefault()` on touchstart/touchmove stops the page scrolling
      and the long-press menu at the source. Pointer events with `pointerType === 'touch'`
      are therefore ignored here.

      Both engines DELEGATE from the window container and re-resolve the handle from the
      event target, so a header node React has since replaced can never leave the drag
      wired to a detached element. Both also carry BOTH gestures: which one a press starts is
      decided once, at begin, from what it landed on, and everything after that goes through
      moveGesture / finishGesture - two gestures can never run at the same time.

      Contract: drag by `.shipwindow-drag-handle`, resize by `.shipwindow-resize-grip`, never
      from inside `.shipwindow-nodrag` (the ✕); position remembered per side on release, the
      chosen size remembered for every window (writeUserScale).*/
    isDragHandle(target) {
        if (!target || !target.closest) return false;
        if (target.closest('.shipwindow-nodrag')) return false;
        return Boolean(target.closest('.shipwindow-drag-handle'));
    }

    isResizeHandle(target) {
        return Boolean(target && target.closest && target.closest('.shipwindow-resize-grip'));
    }

    /*Touch slop below the header (2026-08-06). A scaled-down window's header bar is only
      26 * scale VISUAL px - 13 or so on a phone - so a finger aimed at it lands just below
      about as often as it lands on it. A touch that misses LOW still drags, provided it hit
      nothing but the window's own background (the ship-click underlay, which is what carries
      `shipwindow-grab-slop`): everything interactive - the chrome buttons, section headers,
      system icons - sits ABOVE that underlay and is therefore the event target itself, so
      the slop can never steal a tap from any of them.

      Measured in SCREEN px off the header's own rect, so it compensates for the window scale
      by construction. Cost: in that thin band a tap no longer opens the ship-level info
      popup. Set TOUCH_DRAG_SLOP to 0 to switch the whole behaviour off.*/
    isDragSlop(target, clientY) {
        if (!TOUCH_DRAG_SLOP || !target || !target.closest) return false;
        if (!target.closest('.shipwindow-grab-slop')) return false;

        const element = this.elementRef.current;
        const header = element && element.querySelector('.shipwindow-drag-handle');
        if (!header) return false;

        const rect = header.getBoundingClientRect();
        return clientY >= rect.top && clientY <= rect.bottom + TOUCH_DRAG_SLOP;
    }

    gestureActive() {
        return Boolean(this.dragStart || this.resizeStart);
    }

    /*Double-press detection for the size reset, counted HERE rather than left to a DOM
      dblclick (2026-08-06 refinement, user report: the grip's double-click did nothing with a
      mouse). Touch has no dblclick at all, and on a mouse the grip never sees its own: this
      window takes pointer capture on the CONTAINER at pointerdown, which retargets the
      compatibility mouse events - so click/dblclick fire on the container, not on the grip
      inside it - and `preventDefault()` on pointerdown suppresses them anyway on some
      browsers. Both engines therefore count their own presses, at the one place both run.

      BOTH handles reset the size (user request 2026-08-06): the grip, and the header too,
      since the bottom-right corner of an enlarged or dragged window can be off screen. Keyed
      by which handle was pressed, so grip-then-header is not a double press.

      The reset lands on RELEASE, not on the second press, and only if neither press moved
      (moveGesture cancels it) - full double-click semantics. That way a quick tap followed by
      a real drag still drags, instead of the drag being swallowed as a reset.*/
    notePress(kind, clientX, clientY) {
        const now = Date.now();
        const double = Boolean(this.lastPress)
            && this.lastPress.kind === kind
            && (now - this.lastPress.time) < DOUBLE_PRESS_MS;

        this.lastPress = { kind: kind, time: now };
        this.pressPoint = { x: clientX, y: clientY };
        this.pendingReset = double;
    }

    cancelDoublePress() {
        this.lastPress = null;
        this.pendingReset = false;
    }

    beginDrag(clientX, clientY) {
        const element = this.elementRef.current;
        if (!element) return false;

        //freeze the current geometry into left/top: right-side windows are positioned
        //with `right`, so a drag has to switch them to one coordinate system first.
        //Computed left/top are used values (px) even when the rule says auto; offsetLeft
        //is the fallback for anything that reports auto anyway.
        const style = window.getComputedStyle(element);
        let left = parseFloat(style.left);
        let top = parseFloat(style.top);
        if (!isFinite(left)) left = element.offsetLeft;
        if (!isFinite(top)) top = element.offsetTop;

        this.dragStart = { x: clientX, y: clientY, left: left, top: top };
        this.positioned = true;
        element.style.left = left + 'px';
        element.style.top = top + 'px';
        element.style.right = 'auto';
        return true;
    }

    /*Pointer deltas move left/top ONE FOR ONE - no division by the window scale (bug fixed
      2026-08-06; it was the reported "the drag area seems to sit above the header, sometimes"
      and it only ever showed up on touch screens, the only place the scale is not 1).
      `transform: scale()` is applied AFTER layout about an origin that is itself a corner of
      the element, so that corner maps to itself: the painted box moves exactly as far as
      left/top do, whatever the scale. Dividing by 0.5 therefore ran the window away at twice
      the speed of the finger, which after half a second of dragging leaves the finger well
      ABOVE the header it is supposedly holding - and drops the drag the moment the pointer
      leaves the window on a stream the browser has not captured.*/
    moveDrag(clientX, clientY) {
        const element = this.elementRef.current;
        if (!this.dragStart || !element) return;

        element.style.left = (this.dragStart.left + (clientX - this.dragStart.x)) + 'px';
        element.style.top = (this.dragStart.top + (clientY - this.dragStart.y)) + 'px';
        this.clampIntoView();
    }

    /*Resize grip (user request 2026-08-06). The window cannot reflow, so the grip drives the
      user half of the CSS scale instead - see the ResizeGrip comment.

      Two things are set up first. (1) The window is anchored on the corner OPPOSITE the grip,
      so that corner stays still and the grip itself tracks the finger. Left-docked windows
      (grip bottom-right) anchor top-left: the window is switched to an explicit left/top with
      a `top left` transform origin, and because it was scaling about its top-RIGHT corner
      until now - painting its box exactly `width * (1 - scale)` left of its layout box - that
      offset is added back to `left` in the same breath, so the window does not jump. No
      knowledge of the containing block is needed either way (game.php positions these against
      the initial containing block, the lobby against a fixed overlay). Right-docked windows
      (grip bottom-left) anchor top-RIGHT, which is the origin they already use: freezing
      left/top there keeps the layout box - and therefore the painted right edge - exactly
      where it is, and the window grows out to the LEFT as it scales. (2) The natural size and
      the finger's own starting projection are captured once, so the maths below is a pure
      function of how far the finger has moved.*/
    beginResize(clientX, clientY) {
        const element = this.elementRef.current;
        if (!element) return false;

        const natural = this.measureNatural();
        if (!natural) return false;

        //layout position, exactly as beginDrag reads it
        const style = window.getComputedStyle(element);
        let left = parseFloat(style.left);
        let top = parseFloat(style.top);
        if (!isFinite(left)) left = element.offsetLeft;
        if (!isFinite(top)) top = element.offsetTop;

        const scale = this.screenFit || 1;
        const mirror = this.isMirroredGrip();
        const origin = mirror ? 'top right' : 'top left';
        if (!mirror && this.transformOrigin() === 'top right') left += natural.width * (1 - scale);

        element.style.left = left + 'px';
        element.style.top = top + 'px';
        element.style.right = 'auto';
        element.style.transformOrigin = origin;
        this.resizeOrigin = origin;
        this.positioned = true;

        //the anchored corner in SCREEN coords - the same space the pointer reports in
        const rect = element.getBoundingClientRect();
        const originX = mirror ? rect.right : rect.left;
        //the grip's x runs AWAY from the anchor: leftwards for a bottom-left grip, so
        //dragging left is what grows a right-docked window
        const flipX = mirror ? -1 : 1;
        this.resizeStart = {
            originX: originX,
            originY: rect.top,
            flipX: flipX,
            width: natural.width,
            height: natural.height,
            scale: scale,
            /*How big this window may get before its far edge leaves the screen. A right-docked
              window grows leftwards from a fixed right edge, so `scale * width` may not exceed
              the distance from that edge to the left of the viewport (user request 2026-08-06:
              "it can't be expanded further than the left-hand side of the browser screen").
              Left-docked windows are not capped the same way - keepGripOnScreen pans an
              over-wide one back instead, which it can only do because their far edge is the
              one carrying the grip and the ✕.*/
            maxScale: mirror ? originX / natural.width : Infinity,
            //where the finger's own projection started, so grabbing the grip off centre
            //(or by its invisible finger pad) cannot make the window jump on the first move
            base: cornerScale(natural.width, natural.height, flipX * (clientX - originX), clientY - rect.top)
        };
        return true;
    }

    moveResize(clientX, clientY) {
        const resize = this.resizeStart;
        if (!resize) return;

        const implied = cornerScale(resize.width, resize.height, resize.flipX * (clientX - resize.originX), clientY - resize.originY);
        //cap first, clamp second: the legibility floor still wins on a screen so narrow that
        //even a minimum-size window would overflow it
        const total = clampScale(Math.min(resize.maxScale, resize.scale + (implied - resize.base)));
        const side = this.side();
        const next = Math.round((total / (this.autoFit || 1)) * 100) / 100;
        if (next === getUserScale(side)) return;

        setUserScale(side, next); //in memory only - storage is written once, on release
        this.applyScreenFit();
    }

    moveGesture(clientX, clientY) {
        //real movement means this press is a drag/resize, not half of a double-click
        if (this.pressPoint
            && (Math.abs(clientX - this.pressPoint.x) > DOUBLE_PRESS_SLOP
                || Math.abs(clientY - this.pressPoint.y) > DOUBLE_PRESS_SLOP)) {
            this.cancelDoublePress();
        }

        if (this.resizeStart) this.moveResize(clientX, clientY);
        else this.moveDrag(clientX, clientY);
    }

    finishGesture() {
        const element = this.elementRef.current;
        const resized = Boolean(this.resizeStart);
        this.dragStart = null;
        this.resizeStart = null;

        //a second still press on the same handle, released still: back to 100% (notePress)
        if (this.pendingReset) {
            this.cancelDoublePress(); //so a third press doesn't count as another double
            this.resetUserScale();
        }

        if (resized) {
            const side = this.side();
            writeUserScale(side, getUserScale(side)); //the chosen size is a setting, not a one-off
            this.keepGripOnScreen(); //suppressed during the gesture - see applyScreenFit
            this.clampIntoView();
        }

        //remember where the player left this side's window (feedback 2026-07-17)
        if (element) {
            savedWindowPositions[this.side()] = {
                top: parseFloat(element.style.top) || 0,
                left: parseFloat(element.style.left) || 0
            };
        }
    }

    /*A window could be dragged (or, now, scaled) until its header sat off the top of the
      screen, and a header that is off-screen can never be grabbed again - the window is
      stranded, closeable only by its ✕ if that is still reachable (2026-08-06). Keep the
      header row and a slice of the window on screen. Works off the PAINTED rect, so it is
      right at any scale and for either transform origin, and only for windows the player has
      actually placed - a docked small-screen window is positioned by the media query's
      right/top and must not be converted to left/top behind its back.*/
    clampIntoView(fullyOnScreen) {
        const element = this.elementRef.current;
        if (!element || !this.positioned) return;

        const rect = element.getBoundingClientRect();
        const viewWidth = document.documentElement.clientWidth || window.innerWidth;
        const viewHeight = window.innerHeight || document.documentElement.clientHeight;

        let dx = 0;
        let dy = 0;
        if (rect.top < 0) dy = -rect.top;
        else if (rect.top > viewHeight - CLAMP_MARGIN) dy = (viewHeight - CLAMP_MARGIN) - rect.top;

        /*`fullyOnScreen` (the size reset, not a drag): a window that has just SHRUNK may still
          be sitting where keepGripOnScreen pushed it while it was too wide, with its left-hand
          sections off screen for no reason any more. Only asked for where it can be granted -
          and never during a drag, where pushing a window half off the left edge to see the map
          under it is a perfectly good thing to want.*/
        if (fullyOnScreen && rect.width <= viewWidth && rect.left < 0) dx = -rect.left;
        else if (rect.right < CLAMP_MARGIN) dx = CLAMP_MARGIN - rect.right;
        else if (rect.left > viewWidth - CLAMP_MARGIN) dx = (viewWidth - CLAMP_MARGIN) - rect.left;
        if (!dx && !dy) return;

        //left/top move the painted box one for one - see the note on moveDrag. The drag's own
        //reference point is deliberately NOT adjusted: every move recomputes the position from
        //it, so the window simply sticks at the boundary while the finger runs past and picks
        //the finger up again the moment it comes back, with nothing accumulating either way.
        element.style.left = ((parseFloat(element.style.left) || 0) + dx) + 'px';
        element.style.top = ((parseFloat(element.style.top) || 0) + dy) + 'px';
    }

    /*An over-large window must not park its own resize grip off the screen - it would then be
      neither shrinkable nor (for a left-docked window, whose ✕ shares that edge) closable
      until it had been panned back (2026-08-06). Whenever the scale changes, a window that
      overflows the edge its GRIP is on is nudged back just far enough to bring that edge into
      view; the far side goes off screen instead, which is the trade the player asked for by
      making it that big, and panning it back out afterwards is their call (this never runs
      from a drag).

      The edge in question mirrors with the grip: a left-docked window is pulled LEFT off the
      right of the screen, a right-docked one is pushed RIGHT off the left. The right-docked
      case is a backstop only - moveResize caps the scale so the window cannot be expanded past
      the left edge in the first place - for a window that was already large when the viewport
      got narrower (rotation, desktop window resize). It never pushes so far that the ✕ in the
      top-right corner leaves the screen: a window wider than the whole viewport keeps its
      close button and loses its grip, which is the safer of the two. Only ever moves a window
      that genuinely overflows, so windows that fit - all of them, at the automatic size - are
      untouched.*/
    keepGripOnScreen() {
        const element = this.elementRef.current;
        if (!element) return;

        const rect = element.getBoundingClientRect();
        const viewWidth = document.documentElement.clientWidth || window.innerWidth;

        let shift;
        if (this.isMirroredGrip()) {
            if (rect.left >= 0) return;
            shift = Math.min(-rect.left, viewWidth - rect.right); //positive: move right
            if (shift <= 0) return; //no room to give without losing the ✕
        } else {
            shift = viewWidth - rect.right; //negative: move left
            if (shift >= 0) return;
        }

        //a still-docked window is placed by the media query's left/right, so switch it to an
        //explicit left first (left/top move the painted box one for one at any scale or origin)
        const style = window.getComputedStyle(element);
        let left = parseFloat(style.left);
        if (!isFinite(left)) left = element.offsetLeft;

        element.style.left = (left + shift) + 'px';
        element.style.right = 'auto';
        this.positioned = true;
    }

    //back to the automatic size (double-click the grip on a mouse, double-tap it on a
    //touch screen): the escape hatch from a window scaled down to something unreadable
    resetUserScale() {
        const side = this.side();
        if (getUserScale(side) === 1) return;
        setUserScale(side, 1);
        writeUserScale(side, 1);
        this.applyScreenFit();
        this.clampIntoView(true); //back to natural size, so put the whole window back on screen
    }

    onGripDoubleClick(event) {
        event.stopPropagation();
        this.resetUserScale();
    }

    //--- mouse / pen (pointer events) ---
    onDragStart(event) {
        //set window.FV_DRAG_DEBUG = true in the console to see which engine fires and
        //whether the press landed on the handle (remote-debugging a touch device)
        if (window.FV_DRAG_DEBUG) console.log('[shipwindow] pointerdown', event.pointerType, 'handle:', this.isDragHandle(event.target), 'grip:', this.isResizeHandle(event.target));
        if (event.pointerType === 'touch') return; //touch is handled by the touch engine below
        if (event.button != null && event.button > 0) return; //primary button only

        //the grip and the header share these listeners (and the capture/teardown below):
        //the two gestures are mutually exclusive by construction that way
        const resize = this.isResizeHandle(event.target);
        if (!resize && !this.isDragHandle(event.target)) return;

        //a second still press on the same handle inside DOUBLE_PRESS_MS resets the size on
        //release; the gesture below still starts, and a moved gesture cancels the reset
        this.notePress(resize ? 'grip' : 'header', event.clientX, event.clientY);

        if (!(resize ? this.beginResize(event.clientX, event.clientY) : this.beginDrag(event.clientX, event.clientY))) {
            this.cancelDoublePress(); //no gesture, so no release to apply a pending reset on
            return;
        }

        //capture on the container (not the header): the pointer routinely leaves the small
        //header bar mid-drag. Falls back to document listeners if capture is unavailable.
        const element = this.elementRef.current;
        let captured = false;
        try {
            element.setPointerCapture(event.pointerId);
            captured = true;
        } catch (e) { /*fall through to the document listeners*/ }

        this.dragTarget = captured ? element : document;
        this.dragTarget.addEventListener('pointermove', this.onDragMove);
        this.dragTarget.addEventListener('pointerup', this.onDragEnd);
        this.dragTarget.addEventListener('pointercancel', this.onDragEnd);
        this.dragPointerId = event.pointerId;

        event.preventDefault(); //no text selection / native drag while moving the window
    }

    onDragMove(event) {
        if (!this.gestureActive() || event.pointerId !== this.dragPointerId) return;
        this.moveGesture(event.clientX, event.clientY);
    }

    onDragEnd(event) {
        if (!this.gestureActive() || (event && event.pointerId !== this.dragPointerId)) return;
        this.stopDragListening();
        this.finishGesture();
    }

    //--- touch ---
    onTouchDragStart(event) {
        if (window.FV_DRAG_DEBUG) console.log('[shipwindow] touchstart', event.touches && event.touches.length, 'handle:', this.isDragHandle(event.target), 'grip:', this.isResizeHandle(event.target));
        if (this.gestureActive()) return; //already dragging or resizing
        if (!event.touches || event.touches.length !== 1) return; //ignore pinches

        const touch = event.touches[0];
        const resize = this.isResizeHandle(event.target);
        if (!resize && !this.isDragHandle(event.target) && !this.isDragSlop(event.target, touch.clientY)) return;

        //double-tap either handle to reset the size (the same counter the mouse uses)
        this.notePress(resize ? 'grip' : 'header', touch.clientX, touch.clientY);

        if (!(resize ? this.beginResize(touch.clientX, touch.clientY) : this.beginDrag(touch.clientX, touch.clientY))) {
            this.cancelDoublePress(); //as above
            return;
        }

        this.touchDragId = touch.identifier;
        //Where the page sat when the drag began. preventDefault alone can't stop a scroll
        //that has ALREADY started - the browser reports the touch as non-cancelable
        //("Ignored attempt to cancel a touchstart event with cancelable=false", seen in the
        //lobby, the one page that scrolls, where a fling or pull-to-refresh is often still
        //running when the finger lands on the window). Re-pinning the scroll on every move
        //holds the page still anyway, and kills the fling in the process.
        this.dragScroll = { x: window.scrollX || window.pageXOffset || 0, y: window.scrollY || window.pageYOffset || 0 };

        //document-level so the finger can leave the header; non-passive because the moves
        //must preventDefault to stop the page scrolling underneath
        document.addEventListener('touchmove', this.onTouchDragMove, { passive: false });
        document.addEventListener('touchend', this.onTouchDragEnd);
        document.addEventListener('touchcancel', this.onTouchDragEnd);

        //also suppresses the long-press context menu, which would otherwise abort the drag.
        //Guarded: calling it on a non-cancelable event does nothing but log an Intervention.
        if (event.cancelable) event.preventDefault();
    }

    onTouchDragMove(event) {
        const touch = findTouch(event.touches, this.touchDragId);
        if (!this.gestureActive() || !touch) return;

        this.moveGesture(touch.clientX, touch.clientY);

        //hold the page where it was - see the note in onTouchDragStart
        const scroll = this.dragScroll;
        if (scroll && ((window.scrollX || window.pageXOffset || 0) !== scroll.x || (window.scrollY || window.pageYOffset || 0) !== scroll.y)) {
            window.scrollTo(scroll.x, scroll.y);
        }

        if (event.cancelable) event.preventDefault();
    }

    onTouchDragEnd(event) {
        //another finger may still be down - only end when OUR touch has lifted
        if (event && event.touches && findTouch(event.touches, this.touchDragId)) return;

        this.stopTouchDragListening();
        if (this.gestureActive()) this.finishGesture();
    }

    stopTouchDragListening() {
        document.removeEventListener('touchmove', this.onTouchDragMove, { passive: false });
        document.removeEventListener('touchend', this.onTouchDragEnd);
        document.removeEventListener('touchcancel', this.onTouchDragEnd);
        this.touchDragId = null;
        this.dragScroll = null;
    }

    stopDragListening() {
        const target = this.dragTarget;
        if (target) {
            target.removeEventListener('pointermove', this.onDragMove);
            target.removeEventListener('pointerup', this.onDragEnd);
            target.removeEventListener('pointercancel', this.onDragEnd);
            if (this.dragPointerId != null && target.hasPointerCapture && target.hasPointerCapture(this.dragPointerId)) {
                target.releasePointerCapture(this.dragPointerId);
            }
        }
        this.dragTarget = null;
        this.dragPointerId = null;
    }

    onShipClick(event) {
        event.stopPropagation();
        let { ship } = this.props;

        if (this.ignoreNextClick) {
            this.ignoreNextClick = false;
            return;
        }

        /* SHIPWINDOW_REDESIGN: in the LOBBY a click on the window background raised the
           old ship-level SystemInfo popup - the pre-redesign hit chart / notes tooltip,
           which this window has had dedicated buttons for since Stage 1. Two ways to see
           the same thing, one of them an accident of clicking the backdrop, and it landed
           on top of whatever damage menu was open. Suppressed here rather than in the
           lobby's uiEvents handler so the click still CLOSES an open menu, which is what
           clicking the backdrop should do. game.php is untouched - the popup is the only
           route to that information there. */
        if (isLobby()) {
            window.uiEvents.relay('CloseSystemInfo');
            return;
        }

        window.uiEvents.relay('SystemClicked', {
            ship: ship,
            system: ship,
            element: event.target
        });
    }

    onShipTouchMove(event) {
        if (!this.longPressTimer) return;

        const touch = event.touches[0];
        const dx = touch.clientX - this.touchStartX;
        const dy = touch.clientY - this.touchStartY;

        // Cancel if they move more than 10 pixels
        if (Math.abs(dx) > 10 || Math.abs(dy) > 10) {
            clearTimeout(this.longPressTimer);
            this.longPressTimer = null;
        }
    }

    onShipTouchCancel(event) {
        if (this.longPressTimer) {
            clearTimeout(this.longPressTimer);
            this.longPressTimer = null;
        }
        this.touchActive = false;
        window.uiEvents.relay('SystemMouseOut');
    }

    onShipTouchEnd(event) {
        if (this.longPressTimer) {
            // Timer didn't pop, meaning this was a short tap
            clearTimeout(this.longPressTimer);
            this.longPressTimer = null;
        } else {
            // Timer already fired (long press). Hide info on release.
            window.uiEvents.relay('SystemMouseOut');
        }

        setTimeout(() => {
            this.touchActive = false;
        }, 300); // Clear touch active after giving click events time to fire/be ignored
    }

    onUnknownMouseOver(event) {
        if (this.touchActive) return;
        if (window.lastTouchActiveTime && Date.now() - window.lastTouchActiveTime < 1000) return;

        let { ship } = this.props;
        let stealthSystem = shipManager.systems.getSystemByName(ship, "mineStealth");

        window.uiEvents.relay('SystemMouseOver', {
            ship: ship,
            system: stealthSystem ? stealthSystem : ship.systems[0],
            element: event.currentTarget,
            showInfo: true
        });
    }

    onUnknownMouseOut() {
        if (this.touchActive) return;
        if (window.lastTouchActiveTime && Date.now() - window.lastTouchActiveTime < 1000) return;

        window.uiEvents.relay('SystemMouseOut');
    }

    onUnknownTouchStart(event) {
        this.touchActive = true;
        this.ignoreNextClick = false;
        window.lastTouchActiveTime = Date.now();

        if (this.longPressTimer) {
            clearTimeout(this.longPressTimer);
        }

        const target = event.currentTarget;
        const touch = event.touches[0];
        this.touchStartX = touch.clientX;
        this.touchStartY = touch.clientY;

        this.longPressTimer = setTimeout(() => {
            this.ignoreNextClick = true; // Prevent click from firing after long press
            let { ship } = this.props;
            let stealthSystem = shipManager.systems.getSystemByName(ship, "mineStealth");

            // Long Press -> generic tooltip
            window.uiEvents.relay('SystemMouseOver', {
                ship: ship,
                system: stealthSystem ? stealthSystem : ship.systems[0],
                element: target,
                showInfo: true
            });

            this.longPressTimer = null;
        }, 400); // 400ms hold required for info window
    }


    componentDidMount() {
        const element = this.elementRef.current;
        const side = isLeftWindow(this.props.ship) ? 'left' : 'right';

        //drag by the header bar only (plan §6) - body drags would fight the ship-click
        //underlay and system icon interactions; the header ✕ stays click-only.
        //Drag end records the position per SIDE so the next window opened on that side
        //(reopen or a different ship) appears where the player left it (feedback
        //2026-07-17) instead of snapping back to the default corner.
        //Both listeners sit on the CONTAINER and re-resolve the handle per event (see the
        //drag block above) - nothing here can go stale when React re-renders the header.
        element.addEventListener('pointerdown', this.onDragStart);
        element.addEventListener('touchstart', this.onTouchDragStart, { passive: false });

        //restore the side's remembered position (desktop only - the ≤1024px layout
        //pins windows to a screen edge via the media query). Clamped so a stale position
        //can't strand the window off-screen after a resize.
        const saved = savedWindowPositions[side];
        if (saved && !isSmallScreen()) {
            const top = Math.max(0, Math.min(saved.top, window.innerHeight - 60));
            const left = Math.max(60 - element.offsetWidth, Math.min(saved.left, window.innerWidth - 60));
            element.style.top = top + 'px';
            element.style.left = left + 'px';
            element.style.right = 'auto';
            this.positioned = true; //placed by us, so clampIntoView may adjust it
        }

        //close the Hit Chart / Notes popup on any press outside it (2026-07-16 feedback)
        document.addEventListener('pointerdown', this.onDocumentPointerDown);

        //touch screens: scale the window to the screen width (and keep it right through
        //rotation / browser-chrome resizes). This is also where the size the player set with
        //the resize grip, in an earlier window or an earlier session, is applied.
        this.applyScreenFit();
        window.addEventListener('resize', this.onScreenResize);
        window.addEventListener('orientationchange', this.onScreenResize);
    }

    //the window's natural width changes with its content (EW targets appearing, sections
    //being destroyed, the lobby re-applying enhancements), so the fit is re-checked after
    //every render; applyScreenFit only writes when the rounded scale actually moves.
    componentDidUpdate() {
        this.applyScreenFit();
    }

    componentWillUnmount() {
        document.removeEventListener('pointerdown', this.onDocumentPointerDown);
        window.removeEventListener('resize', this.onScreenResize);
        window.removeEventListener('orientationchange', this.onScreenResize);
        const element = this.elementRef.current;
        if (element) {
            element.removeEventListener('pointerdown', this.onDragStart);
            element.removeEventListener('touchstart', this.onTouchDragStart, { passive: false });
        }
        this.stopDragListening();
        this.stopTouchDragListening();
        this.dragStart = null;
        this.resizeStart = null;
        if (this.panelHoverTimer) clearTimeout(this.panelHoverTimer);
    }

    //desktop hover peek, shared by the Notes and Ship Stats buttons (and their popups):
    //hovering shows the panel, a click still pins it, and a clicked panel always wins
    onPanelHoverStart(name) {
        if (this.panelHoverTimer) {
            clearTimeout(this.panelHoverTimer);
            this.panelHoverTimer = null;
        }
        if (this.state.hoverPanel !== name) this.setState({ hoverPanel: name });
    }

    //short grace period so the pointer can cross from the button into the popup
    onPanelHoverEnd() {
        if (this.panelHoverTimer) clearTimeout(this.panelHoverTimer);
        this.panelHoverTimer = setTimeout(() => {
            this.panelHoverTimer = null;
            this.setState({ hoverPanel: null });
        }, 150);
    }

    onDocumentPointerDown(event) {
        if (!this.state.openPanel) return;

        const controls = this.controlsRef.current;
        const popup = this.popupRef.current;
        if (controls && controls.contains(event.target)) return; //button clicks toggle themselves
        if (popup && popup.contains(event.target)) return; //clicks inside the popup keep it open

        this.setState({ openPanel: null });
    }

    close() {
        window.uiEvents.relay('CloseShipWindow', { ship: this.props.ship });
    }

    togglePanel(name, event) {
        event.stopPropagation();
        this.setState(state => ({ openPanel: state.openPanel === name ? null : name }));
    }

    //Ship Art toggle (item 4, 2026-07-22): overlay the window body with the ship's own
    //art in full colour, hiding the section grid/icons. Closing any open popup on entry
    //keeps the focused-art view clean.
    toggleArt(event) {
        if (event) event.stopPropagation();
        this.setState(state => ({ showArt: !state.showArt, openPanel: null }));
    }

    /*Resize grip, on the bottom corner AWAY from the screen edge the window docks to (user
      request 2026-08-06: portrait windows are still too small sometimes, the right size
      differs per player/device, and the right-hand window's grip belongs bottom-left) - see
      the ResizeGrip comment for why the corner has to follow the dock. Carries
      `shipwindow-nodrag` as well so it can never be mistaken for a drag handle if it ever ends
      up inside one. The pointer/touch listeners are the container's own - see onDragStart -
      which is also where the double-press reset is counted; the onDoubleClick here is belt and
      braces for the paths where pointer capture is unavailable and the grip does see its own
      dblclick (resetUserScale is idempotent, so both firing is harmless).

      `overlap`: draw it over the row above instead of below it - see renderStatusStrip.*/
    renderResizeGrip(overlap) {
        return (
            <ResizeGrip
                className="shipwindow-resize-grip shipwindow-nodrag"
                $pad={GRIP_TOUCH_PAD}
                $mirror={this.isMirroredGrip()}
                $overlap={Boolean(overlap)}
                onDoubleClick={this.onGripDoubleClick}
                title="Drag to resize this window — double-click to reset its size"
            />
        );
    }

    /*The window's bottom strip: the status banners, with the resize grip drawn ON TOP of the
      last one rather than in a row below it (user request 2026-08-12). The grip had its own
      flow row, so on any ship carrying a banner - ROLLED, Undetected, boarded, attached - the
      banner was shunted up off the window's bottom edge and the 16px band beneath it read as
      a stray gap. Overlapping costs the grip its row (ResizeGrip $overlap) and puts the mark
      in the banner's corner, which is empty: the text is centred and the overlapped banner
      reserves that corner on both sides ($grip), so the centring is untouched.

      With no banner to sit on, the grip keeps its own row exactly as before - the alternative
      is covering the corner of a section panel, which is what the grip was put in the flow to
      avoid in the first place.

      ROLLED is passed in rather than read here because it is a property of how the window is
      DRAWN (the port/starboard columns are swapped), not of the ship's status; it leads the
      list, as it did when it was rendered separately.*/
    renderStatusStrip(ship, rolled) {
        const banners = rolled
            ? [{ key: 'rolled', text: '⟲ Rolled — port / starboard reversed' }].concat(getStatusBanners(ship))
            : getStatusBanners(ship);

        return (
            <React.Fragment>
                {banners.map((banner, index) => (
                    <StatusBanner
                        key={banner.key}
                        $color={banner.color}
                        $bg={banner.bg}
                        $grip={index === banners.length - 1}
                    >{banner.text}</StatusBanner>
                ))}
                {this.renderResizeGrip(banners.length > 0)}
            </React.Fragment>
        );
    }

    renderHeader(shipName, unitName, tint) {
        return (
            /*the bar's own tooltip (the name/class spans keep theirs) advertises both gestures -
              double-press to reset the size is on the header as well as the grip, because an
              enlarged or dragged window can have its bottom-right corner off screen*/
            <Header className="shipwindow-drag-handle" title="Drag to move — double-click to reset window size">
                <HeaderName $tint={tint} title={shipName}>{shipName}</HeaderName>
                <HeaderClass title={unitName}>{unitName}</HeaderClass>
                <CloseButton className="shipwindow-nodrag" onClick={this.close.bind(this)}>✕</CloseButton>
            </Header>
        );
    }

    renderHitChartButton(wide) {
        const { openPanel } = this.state;
        return (
            <CtrlButton ref={this.hitChartBtnRef} $wide={wide} $active={openPanel === 'hitchart'} onClick={this.togglePanel.bind(this, 'hitchart')}>
                <CtrlIcon>⊕</CtrlIcon>Hit Chart
            </CtrlButton>
        );
    }

    /*Ship Stats button (game.php only, user request 2026-07-26): opens the lobby's
      Ship Stats block as a popup. Same StatsIcon glyph as the lobby block, imported from
      ShipNotesPanel so the two can't drift. Hover-peeks like the Notes button and pins on
      click (user request 2026-07-26) - stats are a glance-at stat, not a read-through one.*/
    renderStatsButton(wide) {
        const { openPanel } = this.state;
        return (
            <CtrlButton
                $wide={wide}
                $active={openPanel === 'shipstats'}
                onClick={this.togglePanel.bind(this, 'shipstats')}
                onMouseEnter={this.onPanelHoverStart.bind(this, 'shipstats')}
                onMouseLeave={this.onPanelHoverEnd.bind(this)}
            >
                <StatsIcon><i /><i /><i /></StatsIcon>Ship Stats
            </CtrlButton>
        );
    }

    renderArtButton(wide) {
        return (
            <CtrlButton $wide={wide} $active={this.state.showArt} onClick={this.toggleArt.bind(this)}>
                <ArtIcon />Ship Art
            </CtrlButton>
        );
    }

    renderNotesButton(wide) {
        const { openPanel } = this.state;
        return (
            <CtrlButton
                $wide={wide}
                $active={openPanel === 'notes'}
                onClick={this.togglePanel.bind(this, 'notes')}
                onMouseEnter={this.onPanelHoverStart.bind(this, 'notes')}
                onMouseLeave={this.onPanelHoverEnd.bind(this)}
            >
                <CtrlIcon>✎</CtrlIcon>Notes
            </CtrlButton>
        );
    }

    /*Top-left control block, identical in structure on both pages (2026-07-23): Hit Chart,
      then Ship Art, then game.php's Ship Stats button (2026-07-26), then game.php's Notes
      button / the lobby's manoeuvre stats. The lobby's round-8/9 bottom-left `artbtn` grid
      cell is gone - the two toggles now always read as one stack. Compact windows
      (mines/terrain) render the same block as a centred row.*/
    renderControls(withHitChart, withNotes, compact, withStats) {
        const lobby = isLobby();
        const artHere = artAvailable(this.props.ship);
        const statsBtnHere = statsAvailable(this.props.ship);

        if (!withHitChart && !withNotes && !withStats && !artHere && !statsBtnHere) return null;

        const wide = lobby; //150px datasheet panels in the lobby, 130px in game

        return (
            <ControlsArea ref={this.controlsRef} $compact={compact}>
                {withHitChart && this.renderHitChartButton(wide)}
                {artHere && this.renderArtButton(wide)}
                {statsBtnHere && this.renderStatsButton(wide)}
                {withNotes && this.renderNotesButton(wide)}
                {/*lobby: manoeuvre stats live under the buttons (user layout
                   decision 2026-07-17)*/}
                {withStats && <ManoeuvreStats ship={this.props.ship} />}
            </ControlsArea>
        );
    }

    //left offset (relative to the window container) of the control-button stack, so
    //the popup's left edge lines up with the buttons. Measured live because the offset
    //depends on the ctrl column width, which the grid resolves from the section sizes.
    getButtonLeft() {
        const controls = this.controlsRef.current;
        const container = this.elementRef.current;
        if (!controls || !container) return null;
        //subtract the container's left border: the popup's `left` is measured from the
        //container's padding edge (inside the 1px border), but getBoundingClientRect
        //reports outer-border coords - without this the popup sits 1px right of the buttons.
        //Divide by the touch-screen scale FIRST: rects are screen px on a transformed
        //window but `left` is a layout value (see getAnchorBelow).
        return Math.round(
            (controls.getBoundingClientRect().left - container.getBoundingClientRect().left)
            / (this.screenFit || 1)
            - container.clientLeft
        );
    }

    //popup anchor (top/left relative to the window container): the popup drops just below
    //the given control block, its left edge aligned to it. Measured live because the
    //control column's size depends on grid tracks the CSS resolves at render. Falls back
    //to a fixed top (and the measured left) if the ref isn't mounted yet.
    getAnchorBelow(ref, fallbackTop) {
        const el = ref && ref.current;
        const container = this.elementRef.current;
        if (!el || !container) return { top: fallbackTop, left: this.getButtonLeft() };
        const eRect = el.getBoundingClientRect();
        const cRect = container.getBoundingClientRect();
        /*getBoundingClientRect reports SCREEN px, and on a touch screen the window carries
          a scale() transform - so a raw rect delta is `scale` times the layout offset the
          popup's top/left actually want. Undo it (2026-07-23 round 11: harmless at the
          near-1 scales round 10 produced, but the map-visibility budget shrinks windows far
          enough that the popup was landing well above its button).*/
        const scale = this.screenFit || 1;
        return {
            left: Math.round((eRect.left - cRect.left) / scale - container.clientLeft),
            top: Math.round((eRect.bottom - cRect.top) / scale - container.clientTop) + 4,
        };
    }

    renderPopup(withHitChart, withNotes, top) {
        const { ship } = this.props;
        const { openPanel, hoverPanel } = this.state;

        //clicked panel wins; otherwise a desktop hover on the Notes / Ship Stats button
        //peeks that panel. Only a rendered button can set hoverPanel, and each branch
        //below re-checks its own availability, so no validation is needed here.
        const shown = openPanel || hoverPanel;
        if (!shown) return null;

        //the lobby stacks Hit Chart + the tall Ship Stats panel in the control block, so
        //its Hit Chart popup drops from the BUTTON (staying attached to it, over Ship Stats)
        //rather than the whole block; game.php / Notes / compact drop below the whole block
        //(keeps the popup clear of the buttons now that game.php stacks up to four).
        const anchorRef = (shown === 'hitchart' && isLobby()) ? this.hitChartBtnRef : this.controlsRef;
        const { top: anchorTop, left } = this.getAnchorBelow(anchorRef, top);

        if (shown === 'hitchart' && withHitChart) {
            //$fit (feedback 2026-07-17, supersedes round 5's full-width span): the
            //geographic columns size themselves, so the popup shrink-wraps them
            return <PopupHolder ref={this.popupRef} $top={anchorTop} $left={left} $fit><HitChartPanel ship={ship} /></PopupHolder>;
        }
        if (shown === 'shipstats' && statsAvailable(ship)) {
            /*the lobby's Ship Stats block (user request 2026-07-26) - a fixed-width panel,
              so $fit shrink-wraps the popup around it. `live`: the rows read the game
              state for THIS turn (turn costs as thrust with the rate in parens, profile
              incl. the ProfileIncreased crit, initiative incl. its modifiers) and go
              yellow where that has moved them off the hull's own figure. `bare`: rows
              only - this popup's own frame and the button it drops from already say
              "Ship Stats", so the block's title bar and border would say it twice.*/
            return (
                <PopupHolder
                    ref={this.popupRef}
                    $top={anchorTop}
                    $left={left}
                    $fit
                    onMouseEnter={this.onPanelHoverStart.bind(this, 'shipstats')}
                    onMouseLeave={this.onPanelHoverEnd.bind(this)}
                >
                    <ManoeuvreStats ship={ship} live bare />
                </PopupHolder>
            );
        }
        if (shown === 'notes' && withNotes) {
            return (
                <PopupHolder
                    ref={this.popupRef}
                    $top={anchorTop}
                    $left={left}
                    $fit
                    $notes
                    onMouseEnter={this.onPanelHoverStart.bind(this, 'notes')}
                    onMouseLeave={this.onPanelHoverEnd.bind(this)}
                >
                    {/*ShipInfo itself decides whether to list enhancements inline: hidden
                       for full grid ships (they have the gold Enhancements box), shown for
                       mines / fighters / terrain (no box) - so no flag is needed here.
                       tightBottom drops its trailing blank line so PopupHolder's 5px
                       bottom padding sets the gap under the last note; compactText matches
                       the Ship Stats popup's type (user request 2026-07-26).*/}
                    <ShipInfo ship={ship} hideHitChart tightBottom compactText />
                </PopupHolder>
            );
        }
        return null;
    }

    render() {
        const { ship } = this.props;
        const lobby = isLobby();
        const isMyTeam = isLeftWindow(ship);

        var unitName = ship.shipClass;
        var shipName = ship.name;
        let isUnrevealedMine = false;

        if (ship.mine) {
            var stealthSystem = shipManager.systems.getSystemByName(ship, "mineStealth");
            if (stealthSystem && !stealthSystem.isMineRevealed(ship)) {
                unitName = "Mine";
                shipName = "Mine";
                isUnrevealedMine = true;
            }
        }

        //nameless ships (lobby store blueprints) promote the class into the white
        //name slot so the header doesn't read as missing (feedback 2026-07-17)
        if (!shipName) {
            shipName = unitName;
            unitName = "";
        }

        const withHitChart = !isUnrevealedMine && hasHitChart(ship);
        //lobby: notes live in the always-visible ShipNotesPanel rail, not a popup
        const withNotes = !lobby && !isUnrevealedMine && hasNotes(ship);

        if (ship.flight) {
            if (lobby) {
                return (
                    <ShipWindowContainer ref={this.elementRef} onClick={shipWindowClicked} onContextMenu={e => { e.preventDefault(); e.stopPropagation(); }} $isMyTeam={isMyTeam} $variant="flightLobby">
                        {this.renderHeader(shipName, unitName, getHeaderTint(ship))}
                        <LobbyBody>
                            <FlightArea><FighterList ship={ship} /></FlightArea>
                            <ShipNotesPanel ship={ship} />
                        </LobbyBody>
                        {this.renderResizeGrip()}
                    </ShipWindowContainer>
                )
            }
            return (
                <ShipWindowContainer ref={this.elementRef} onClick={shipWindowClicked} onContextMenu={e => { e.preventDefault(); e.stopPropagation(); }} $isMyTeam={isMyTeam} $variant="flight">
                    {this.renderHeader(shipName, unitName, getHeaderTint(ship))}
                    <FighterList ship={ship} />
                    {this.renderStatusStrip(ship)}
                </ShipWindowContainer>
            )
        }

        if (isUnrevealedMine) {
            return (<ShipWindowContainer ref={this.elementRef} onClick={shipWindowClicked} onContextMenu={e => { e.preventDefault(); e.stopPropagation(); }} $isMyTeam={isMyTeam} $variant="terrain">
                {this.renderHeader(shipName, unitName, null)}
                <CompactBody>
                    <ShipHitArea className="shipwindow-grab-slop" onClick={this.onShipClick.bind(this)} />
                    <WatermarkLayer $img={window.AssetManager.getSmartImagePath(ship.imagePath)} />
                    <UnknownSystemIcon onMouseOver={this.onUnknownMouseOver.bind(this)} onMouseOut={this.onUnknownMouseOut.bind(this)} onTouchStart={this.onUnknownTouchStart.bind(this)} onTouchMove={this.onShipTouchMove.bind(this)} onTouchEnd={this.onShipTouchEnd.bind(this)} onTouchCancel={this.onShipTouchCancel.bind(this)}>?</UnknownSystemIcon>
                </CompactBody>
                {this.renderResizeGrip()}
            </ShipWindowContainer>)
        }

        const systemsByLocation = sortIntoLocations(ship);
        //single-side-structure ships label that structure plain "Port"/"Starboard"
        const nameOverrides = getSectionNameOverrides(systemsByLocation);
        /*Compact thumbnail variant = terrain + revealed mines; in the lobby (purchased
          mines) the datasheet renders as a full-width block underneath.
          ...but ONLY for the side-less hulls that variant was designed around. The compact
          body is a 250px flex-wrap box holding 125px sections, so a unit that really does
          carry Fwd/Port/Stbd/Aft Structures wraps them into ONE COLUMN instead of an SCS
          layout (user report 2026-08-23: JumpgateCapital, which is terrain purely so
          isTerrain() keeps it unselectable and unplottable - see JUMP_GATES_PLAN.md - but
          is built like a normal capital). Every other terrain unit and every mine carries
          Primary only, so hasSideSections() is exactly the "is this shaped like a ship?"
          test: they are unaffected, and anything with real sides falls through to the full
          grid window below.*/
        const isTerrain = (window.gamedata.isTerrain(ship.shipSizeClass, ship.userid) || ship.mine)
            && !hasSideSections(systemsByLocation);

        if (isTerrain) {
            /*Compact windows stack their action buttons ACROSS THE TOP of the very box the
              art is centred in (ControlsArea $compact is full-width, z-index 2), so on a
              mine - which has barely any systems, making the art most of the window - the
              buttons sit squarely over the image (user report 2026-07-24). Nudge the art
              down clear of them, the same screen-space trick as the lobby grid nudge.
              Gated on the buttons EXISTING: renderControls returns null when the ship has
              no hit chart, notes or art, and an offset with nothing on top of it would just
              push the image off centre (the same mistake the lobby nudge made for
              side-less hulls). Terrain proper is left alone - it wasn't reported, and its
              windows carry enough system icons that the art is incidental.*/
            const compactHasControls = withHitChart || withNotes || artAvailable(ship);
            return (<ShipWindowContainer ref={this.elementRef} onClick={shipWindowClicked} onContextMenu={e => { e.preventDefault(); e.stopPropagation(); }} $isMyTeam={isMyTeam} $variant="terrain">
                {this.renderHeader(shipName, unitName, null)}
                <CompactBody>
                    <ShipHitArea className="shipwindow-grab-slop" onClick={this.onShipClick.bind(this)} />
                    {/*Ship Art (item 3): the SAME watermark turns full colour in place while
                       the sections hide - no resize*/}
                    <WatermarkLayer
                        $img={window.AssetManager.getSmartImagePath(ship.imagePath)}
                        $art={this.state.showArt}
                        $offsetY={ship.mine && compactHasControls ? MINE_WATERMARK_OFFSET_Y : 0}
                    />
                    {/*compact windows: vertical button list above the sections*/}
                    {this.renderControls(withHitChart, withNotes, true)}
                    {COMPACT_LOCATIONS.map(location => systemsByLocation[location].length > 0 && (
                        <ShipSection key={`section-${ship.id}-${location}`} location={location} nameOverride={nameOverrides[location]} ship={ship} systems={systemsByLocation[location]} isTerrain hidden={this.state.showArt} />
                    ))}
                </CompactBody>
                {/*lobby datasheet ahead of the strip: the grip has to stay on the window's
                   bottom corner, and the lobby renders no status banners at all*/}
                {lobby && <ShipNotesPanel ship={ship} full />}
                {this.renderStatusStrip(ship)}
                {this.renderPopup(withHitChart, withNotes, 72)}
            </ShipWindowContainer>)
        }

        //full "digital SCS" grid window
        const rolled = shipManager.movement.isRolled(ship);
        //purchased enhancements get their own bottom-right gold panel (the `enh` grid
        //area). In the lobby it keeps the datasheet stack in `ew` short (feedback round
        //3); in game.php it sits below the EW panel and replaces the ENHANCEMENTS lines
        //that used to live in the Notes popup (2026-07-19). Ammo enhancements are already
        //excluded server-side (Enhancements::isAmmoEnhancement) so ship.enhancementTooltip
        //carries only the enhancements worth surfacing here.
        const withEnhPanel = Boolean(ship.enhancementTooltip);
        const areas = buildTemplateAreas(systemsByLocation, withEnhPanel);
        const isBigBase = ship.base && !ship.smallBase;

        //lobby: manoeuvre stats under the ctrl buttons, and the always-visible
        //datasheet panel in the `ew` grid area - the exact place the EW panel has in
        //game.php (feedback 2026-07-17; the EW panel itself is meaningless pre-game)
        const sectionGrid = (
            <SectionGrid $areas={areas}>
                <ShipHitArea className="shipwindow-grab-slop" onClick={this.onShipClick.bind(this)} />
                {/*Ship Art (item 3): the SAME watermark turns full colour in place while the
                   sections hide - keeps the window/image at the exact same size (no resize).
                   $offsetY: the lobby's taller chrome pushes the sections below the grid
                   midline, so the art follows them down (2026-07-23) - but only on hulls
                   whose side sections block the chrome spans (2026-07-24).*/}
                <WatermarkLayer
                    $img={window.AssetManager.getSmartImagePath(ship.imagePath)}
                    $art={this.state.showArt}
                    $offsetY={lobby && hasSideSections(systemsByLocation) ? LOBBY_WATERMARK_OFFSET_Y : 0}
                />
                {this.renderControls(withHitChart, withNotes, false, lobby && !ship.mine)}
                {lobby ? <ShipNotesPanel ship={ship} grid hideEnhancements={withEnhPanel} /> : <ShipWindowEw ship={ship} />}
                {withEnhPanel && <EnhancementsPanel ship={ship} />}
                {GRID_LOCATIONS.map(location => {
                    if (systemsByLocation[location].length === 0) return null;

                    const displayLocation = rolled && MIRRORED_LOCATION[location] !== undefined ? MIRRORED_LOCATION[location] : location;
                    const area = GRID_AREAS[displayLocation];
                    //big-base quarter sections spread 4-wide so their many systems don't
                    //stack into a metre-tall window
                    const wide = location === 0 || location === 1 || location === 2
                        || (isBigBase && QUARTER_LOCATIONS.includes(location));
                    //quarter sections (31/32/41/42) size to their content, exactly like
                    //Port/Starboard (3/4). They previously carried an 84px min-height floor
                    //to give sparse six-sided hulls "presence", but a one-row section is only
                    //~57px, so that floor inflated every single-row quarter by a phantom
                    //second row of empty space. align-self start/end (GRID_VALIGN) still pins
                    //Fwd quarters to the top and Aft quarters to the bottom of the
                    //Primary-stretched column, so the spare space now falls in the
                    //transparent gap over the watermark instead of inside the panel.
                    const minHeight = undefined;

                    return (
                        <ShipSection
                            key={`section-${ship.id}-${location}`}
                            location={location}
                            displayLocation={displayLocation}
                            area={area}
                            valign={GRID_VALIGN[area]}
                            justify={GRID_JUSTIFY[area]}
                            wide={wide}
                            minHeight={minHeight}
                            nameOverride={nameOverrides[location]}
                            hidden={this.state.showArt}
                            ship={ship}
                            systems={systemsByLocation[location]}
                        />
                    );
                })}
            </SectionGrid>
        );

        return (<ShipWindowContainer ref={this.elementRef} onClick={shipWindowClicked} onContextMenu={e => { e.preventDefault(); e.stopPropagation(); }} $isMyTeam={isMyTeam} $variant="ship">
            {this.renderHeader(shipName, unitName, getHeaderTint(ship))}
            {sectionGrid}
            {this.renderStatusStrip(ship, rolled)}
            {this.renderPopup(withHitChart, withNotes)}
        </ShipWindowContainer>)
    }

}

const shipWindowClicked = () => window.uiEvents.relay('CloseSystemInfo');

//last dragged-to position per window side, session-lifetime (feedback 2026-07-17:
//reopened windows should appear where the player previously moved them)
const savedWindowPositions = { left: null, right: null };

/*>>> TOUCH-SCREEN FIT <<< (user request 2026-07-23, ShipWindow.applyScreenFit). Below
  this breakpoint the window is CSS-scaled to fit a BUDGET - a fraction of the screen it
  may cover - with the min/max keeping that honest: never shrink past legibility, never
  blow a small window up into a poster.

  Round 11 (2026-07-23) split the budget per page, because the two screens want opposite
  things from a phone. game.php's window floats over the TACTICAL MAP, and a window that
  fills the screen (round 10's flat 0.96 both ways, plus a 1.75 cap that MAGNIFIED small
  windows up to the screen) leaves nothing of the map to play on - so it gets a genuine
  budget: ~60% of the width, ~85% of the height, and it is never scaled ABOVE its natural
  size. Docked to a screen edge, that keeps a usable strip of map beside the window. The
  lobby has nothing behind it, so it keeps the round-10 fill-the-screen behaviour, which
  is simply the most legible thing there.

  Retuning: MAP_FIT is the pair of knobs to touch - lower fillW for more visible map,
  raise it for bigger text. The min is the legibility floor: once the fit bottoms out on
  it the window stops shrinking and scrolls internally instead. Same breakpoint as the
  ShipWindowContainer media query that docks windows to the screen edge.

  PORTRAIT BOOST (user request 2026-08-01): a phone held upright is the case the budget
  treats worst - the window's LAYOUT width is fixed (max-content, viewport-independent by
  design, see the media query above), so on a ~390px-wide screen 60% of the width has to
  swallow a ~600px window and the fit bottoms out on the legibility floor. Landscape has
  the width to spend and is not touched. Each budget therefore carries its own `portrait`
  multiplier, applied to fillW/fillH/min only when the screen is BOTH small and upright.*/
const SMALL_SCREEN_QUERY = '(max-width: 1024px)';
const PORTRAIT_QUERY = '(orientation: portrait)';
/*fillW/fillH: the share of screen width/height a fitted window may cover (the slack also
  leaves the docking inset - 4px/8px in the media query - visible on the opposite edge).
  portrait: what those two (and the floor) are multiplied by on an upright small screen -
  1 = no boost. THE knob for "windows are too small on my phone".*/
//game.php: map stays visible; +20% upright, where the map has vertical room to spare
const MAP_FIT = { fillW: 0.60, fillH: 0.85, min: 0.40, max: 1, portrait: 1.40 };
//lobby: nothing behind it, so it already fills the screen - a boost has nowhere to go
const LOBBY_FIT = { fillW: 0.96, fillH: 0.96, min: 0.50, max: 1.75, portrait: 1.2 };
//no fitted window may cover MORE than this share of an axis, however big the boost: the
//docking inset (top: 8px) has to stay on screen or the window is cut off at the bottom
const MAX_FILL = 0.98;

const isSmallScreen = () => Boolean(window.matchMedia) && window.matchMedia(SMALL_SCREEN_QUERY).matches;

const isPortrait = () => (Boolean(window.matchMedia)
    ? window.matchMedia(PORTRAIT_QUERY).matches
    : window.innerHeight >= window.innerWidth);

/*The boost deliberately leaves `max` alone: raising it would let SMALL windows (mines,
  terrain, flights) be magnified above their natural size, which is the round-10 behaviour
  round 11 was asked to remove. `min` DOES move with it - a window big enough to bottom out
  on the floor is exactly the one being called too small - at the cost of a little more
  internal scrolling, and never above `max`.*/
const boostBudget = (budget, factor) => (factor === 1 ? budget : {
    fillW: Math.min(MAX_FILL, budget.fillW * factor),
    fillH: Math.min(MAX_FILL, budget.fillH * factor),
    min: Math.min(budget.max, budget.min * factor),
    max: budget.max,
});

const screenFitBudget = () => {
    const budget = isLobby() ? LOBBY_FIT : MAP_FIT;
    return isPortrait() ? boostBudget(budget, budget.portrait) : budget;
};

/*>>> WINDOW RESIZE GRIP <<< (user request 2026-08-06). The automatic fit above can only
  guess how big a window should be; the grip lets the player say. It drives `userScale`, a
  multiplier applied on top of the fit (and the only scaling on desktop, where the fit is 1),
  so the two stay separable - rotating the device re-fits around the size the player chose
  rather than throwing it away.

  The limits are on the PRODUCT, in one place (clampScale, used by both applyScreenFit and
  moveResize), so a phone window can be dragged from unreadably small up to three times its
  natural size without the grip having to know anything about the per-page budgets.*/
const RESIZE_MIN_SCALE = 0.35;
const RESIZE_MAX_SCALE = 3;
//how far the grip's invisible finger pad reaches up and left of its 16px mark. It sits above
//the window's content (z-index 5), so keep it modest: on a compact mine window it overlaps
//the bottom edge of whatever icon happens to end up in that corner.
const GRIP_TOUCH_PAD = 6;
//how far BELOW the header bar a touch may land and still drag the window (screen px, on the
//window background only - see isDragSlop). 0 disables the behaviour.
const TOUCH_DRAG_SLOP = 8;
/*Size reset: how close together two presses on the same handle count as a double press, and
  how far the pointer may wander before the press is a drag instead (see notePress). 400ms is
  pitched between the ~300ms of a comfortable double-TAP and the ~500ms Windows allows a
  double-CLICK - too short and a deliberate mouse double-click on the grip does nothing, which
  is the complaint this replaced. False positives cost nothing here: both presses must also
  hold still, and the reset only undoes a scaling the player can redo with one drag.*/
const DOUBLE_PRESS_MS = 400;
const DOUBLE_PRESS_SLOP = 6;
//how much of a dragged window must stay on screen (px), so its header can always be grabbed
const CLAMP_MARGIN = 40;
/*The chosen size is remembered per browser and PER SIDE: "hard to judge the correct size for
  different users" is a setting, so it outlives the window and the session - but the left and
  right windows show different things (own fleet vs enemy in game, the store vs your fleet in
  the lobby) and are used differently, so one shared value meant resizing the right window and
  then reopening the left one at the right one's size (user report 2026-08-06). Storage can
  throw (private mode), and a stored value from an older/odd build is clamped on the way in.*/
const USER_SCALE_KEY = 'fv.shipwindow.userScale'; //+ '.left' / '.right'; the bare key is the pre-split value

const clampScale = (scale) => Math.min(RESIZE_MAX_SCALE, Math.max(RESIZE_MIN_SCALE, scale));

/*The scale a bottom-right grip at (dx, dy) from the window's top-left corner implies, as the
  least-squares fit of `dx = scale * width, dy = scale * height`. A uniform scale cannot track
  both axes exactly, so both get a say in proportion to the window's own shape: dragging right
  OR down grows it, diagonally most of all, and the reverse shrinks it - which is how a corner
  grip is expected to behave. At the grip's own resting position it returns exactly the current
  scale, so the maths and the mark agree.*/
const cornerScale = (width, height, dx, dy) => (dx * width + dy * height) / (width * width + height * height);

/*The chosen size is one value per SIDE of the screen, not per window: at most two windows are
  open at once, one docked left and one docked right, and each side is really one slot that
  different ships pass through. Sizing the slot is what the player means; sizing "this
  particular ship's window" is not, or every new ship would come back at the default.

  Module-level (keyed by side) also means a second window opening on the same side picks the
  size up immediately, and the OTHER side is left alone - the round-16 first cut shared one
  value, so resizing the right window and then opening a left one dragged the left one to the
  right one's size (user report 2026-08-06). Read lazily so storage is touched once per side
  per page rather than once per window opened.*/
const sessionUserScale = { left: null, right: null };

const getUserScale = (side) => {
    if (sessionUserScale[side] == null) sessionUserScale[side] = readUserScale(side);
    return sessionUserScale[side];
};

const setUserScale = (side, value) => {
    sessionUserScale[side] = value;
};

//falls back to the pre-split single key so a size chosen before the left/right split seeds
//both sides once, instead of silently resetting to 100%
const readUserScale = (side) => {
    try {
        let stored = parseFloat(window.localStorage.getItem(USER_SCALE_KEY + '.' + side));
        if (!isFinite(stored)) stored = parseFloat(window.localStorage.getItem(USER_SCALE_KEY));
        if (isFinite(stored) && stored > 0) return clampScale(stored);
    } catch (e) { /*storage unavailable - natural size is a fine default*/ }
    return 1;
};

const writeUserScale = (side, value) => {
    try {
        window.localStorage.setItem(USER_SCALE_KEY + '.' + side, String(value));
    } catch (e) { /*as above: not being able to remember it is not worth an error*/ }
};

//the still-down touch that started a drag (TouchList has no .find)
const findTouch = (touches, identifier) => {
    if (!touches || identifier == null) return null;
    for (let i = 0; i < touches.length; i++) {
        if (touches[i].identifier === identifier) return touches[i];
    }
    return null;
};

//gamelobby.php is the only page whose gamedata reports gamephase -2 (buy phase)
const isLobby = () => Boolean(window.gamedata) && window.gamedata.gamephase === -2;

/*>>> LOBBY WATERMARK NUDGE <<< how far DOWN (px) the hull art sits in the lobby grid
  window, where the tall Ship Stats / datasheet / Enhancements chrome pushes the section
  cluster below the grid's vertical midline (user request 2026-07-23). game.php's chrome
  is short enough that its art already lines up, so it stays at 0. One knob - retune to
  taste.

  Only ships WITH side sections are pushed down (correction 2026-07-24): a middle row
  makes buildTemplateAreas' ctrl/ew spans stop at row 1, so the tall lobby chrome inflates
  that row alone and the sections sink. On a side-less hull (Heavy Combat Vessels, most
  small craft) the same spans run the full height of the grid, the chrome shares the
  window's height with the fwd/prim/aft column instead of stacking on top of it, and the
  sections stay centred - nudging the art there only pulls it off them.*/
const LOBBY_WATERMARK_OFFSET_Y = 35;

//side sections present = buildTemplateAreas will emit at least one middle row
const hasSideSections = (locations) => SIDE_LOCATIONS.some(location => locations[location].length > 0);

/*>>> MINE WATERMARK NUDGE <<< the compact-window twin of the knob above (user request
  2026-07-24): how far DOWN (px) a mine's art sits so the compact action-button stack,
  which runs full-width across the TOP of the same box the art is centred in, stops
  covering it. Its own constant rather than a shared one because the two windows are
  different shapes and will retune independently. Applies on both pages - the compact
  mine window and its button stack are identical in game and lobby - and only when
  buttons actually render.*/
const MINE_WATERMARK_OFFSET_Y = 20;

/*Which side of the screen the window docks to. Single source of truth is
  ShipWindowManager.isLeftSide (renderer/shipWindowManager.js) so the manager's
  one-window-per-side filter and the container's CSS placement can never disagree;
  the fallback mirrors its game.php branch for safety.*/
const isLeftWindow = (ship) => {
    if (window.ShipWindowManager && typeof window.ShipWindowManager.isLeftSide === 'function') {
        return window.ShipWindowManager.isLeftSide(ship);
    }
    return ship.team === window.gamedata.getPlayerTeam();
};

const hasHitChart = (ship) => Boolean(ship.hitChart) && Object.keys(ship.hitChart).length > 0;

//the Ship Art toggle (item 4) needs hull art to display and is meaningless for flight
//windows (they show their fighters, not a single hull)
const artAvailable = (ship) => Boolean(ship && ship.imagePath) && !ship.flight;

/*Ship Stats popup button - game.php ONLY (user request 2026-07-26). The lobby already
  shows the very same ManoeuvreStats block always-visible under the Hit Chart button, so
  a button there would just duplicate it. Manoeuvre stats are meaningless for mines and
  terrain (which is why the lobby hides the block for mines too), and game flight windows
  render no control block at all, so both are excluded rather than shown empty.*/
const statsAvailable = (ship) => Boolean(ship) && !isLobby() && !ship.flight && !ship.mine
    && !window.gamedata.isTerrain(ship.shipSizeClass, ship.userid);

const hasNotes = (ship) => Boolean(ship.notes)
    || Boolean(ship.enhancementTooltip)
    || Boolean(ship.hasAttached && Object.keys(ship.hasAttached).length > 0);

/*Faction/team tint for the header name. Reuses the fleet list's canonical palette call
  (gamedata.getFleetHeaderColorRGB carries the team-colour gate internally) rather than
  adding another gate site - see arch_team_colour_logic.*/
const getHeaderTint = (ship) => {
    /*if (typeof playerManager === 'undefined' || !window.gamedata) return null;
    if (typeof gamedata.getFleetHeaderColorRGB !== 'function') return null;

    const slot = playerManager.getSlotById(ship.slot);
    if (!slot) return null;

    return gamedata.getFleetHeaderColorRGB(slot);*/
    /*Keep off-white for now. Was `new THREE.Color(224/255, 231/255, 239/255)
      .convertSRGBToLinear()` - but a THREE.Color object is not a CSS colour (the
      styled prop stringified to an invalid declaration, so the header always fell
      back to the container's theme text colour anyway), and the lobby (Stage 3)
      doesn't load THREE at all, where it would throw. null keeps the exact same
      rendered colour on both pages via HeaderName's `$tint || theme.colors.text`.*/
    return null;
};

/*Ship-level status lines lifted from the map tooltip (ShipTooltip.js), rendered as
  bottom banners like ROLLED (user request 2026-07-18). Game/replay only — none of
  this state exists in the lobby.*/
const getStatusBanners = (ship) => {
    if (isLobby()) return [];

    const banners = [];

    /*NOT ON THE BOARD YET. Reinforcements pick their entry hex the turn BEFORE they arrive, so
      a player can be looking at a fully-detailed ship window for a ship that will not exist for
      another turn - and, on the placement turn, at one they are actively positioning. This is
      the primary signal for that, in the same cyan the fleet list uses for its
      "[Deploys on Turn N]" header. Placed FIRST so it heads the banner stack.
      Deliberately keyed off getTurnDeployed (the board question), so it stays up for every phase
      of every turn until the unit genuinely arrives - not just the phase it was placed in.*/
    const deployTurn = shipManager.getTurnDeployed(ship);
    if (deployTurn > window.gamedata.turn && deployTurn < 999) {
        banners.push({
            key: 'deploying', color: theme.colors.statusPending, bg: 'rgba(0, 184, 230, 0.10)',
            text: 'Deploying on Turn ' + deployTurn
        });
    }

    /*REINFORCEMENTS_PLAN.md STAGE 9 - this unit came out of hyperspace off course and spends the
      turn it arrived on sorting itself out (user request 2026-08-29). Same cyan as the Deploying
      banner above and as the map tooltip's own "Arrival Scatter" line, and for the same reason
      Hangar Operations is cyan on the system icon: it is a benign initiative penalty from something
      the unit just did, not damage.
      Straight after Deploying, deliberately - they are the two halves of one story, and a unit
      never carries both (Deploying is "not on the board yet", this is "arrived this turn").
      The figure is the server's own, so it can never disagree with the roll (shipManager.getArrivalIniPenalty).*/
    const arrivalIni = shipManager.getArrivalIniPenalty(ship);
    if (arrivalIni !== 0) {
        banners.push({
            key: 'arrivalScatter', color: theme.colors.statusPending, bg: 'rgba(0, 184, 230, 0.10)',
            text: 'Arrival Scatter ' + arrivalIni + ' Ini'
        });
    }

    //Resolved once per render and threaded through: when a forecast is live it sweeps every enemy
    //unit, so it is not something to ask for three times over on the way to two banners.
    const stealthForecast = ship.trueStealth ? shipManager.getStealthToggleForecast(ship) : null;

    /*trueStealth ships: green Undetected / red Detected. Mines split the detected half in two,
      because the server tracks finding a mine and identifying it separately (MineStealth:
      `detected` = teams that found it, `revealInfo` = teams that have locked it with OEW to
      learn its type), so one can sit detected-but-anonymous for turns - amber until the type
      is known, red after.
      Read throughout from the OPPOSING side's point of view: on your own unit the question is
      what the enemy has on it, on theirs it is what you have.*/
    if (ship.trueStealth) {
        if (isUndetected(ship, stealthForecast)) {
            banners.push({ key: 'undetected', color: theme.colors.statusOk, bg: 'rgba(50, 205, 50, 0.08)', text: 'Undetected' });
        } else if (ship.mine) {
            const mineStealth = shipManager.systems.getSystemByName(ship, 'mineStealth');
            //No stealth system at all means nothing is hiding it - treat as fully revealed.
            if (!mineStealth || mineStealth.isMineRevealedToOpponent(ship)) {
                banners.push({ key: 'detected', color: theme.colors.statusBad, bg: 'rgba(255, 80, 80, 0.10)', text: 'Detected - Revealed' });
            } else {
                banners.push({ key: 'detected', color: theme.colors.statusAlert, bg: 'rgba(255, 165, 0, 0.10)', text: 'Detected - Not Revealed' });
            }
        } else if (stealthForecast === true) {
            //Nothing is committed until the Pre-Turn phase advances, so the pending Shading Field /
            //Cloaking Device toggle is worded as the forecast it is rather than as fact.
            banners.push({ key: 'detected', color: theme.colors.statusBad, bg: 'rgba(255, 80, 80, 0.10)', text: 'Would be Detected' });
        } else {
            banners.push({ key: 'detected', color: theme.colors.statusBad, bg: 'rgba(255, 80, 80, 0.10)', text: 'Detected' });
        }
    }

    /*JUMP_POINTS_PLAN.md Stage 6: a declared jump point, or an uncommitted jump-out. Same
      --fv-warn yellow the jump-point hex marker, the facing arrow and the Jump Engine's map arc
      all use, so the whole feature reads as one thing. shipManager.isJumpingToHyperspace holds
      what counts - the fire order IS the marker, so cancelling it clears this.*/
    if (shipManager.isJumpingToHyperspace(ship)) {
        banners.push({
            key: 'jumping', color: theme.colors.warning, bg: 'rgba(225, 176, 0, 0.10)',
            text: 'Jumping to Hyperspace'
        });
    }

    //this ship rides a host (e.g. breaching pod attached to its target)
    if (ship.attached && Object.keys(ship.attached).length > 0 && !ship.detached) {
        const hostShip = window.gamedata.getShip(Object.keys(ship.attached)[0]);
        if (hostShip) {
            const location = Object.values(ship.attached)[0];
            let side = '';
            if (location == 1) side = 'Front';
            else if (location == 2) side = 'Aft';
            else if (location == 3 || location == 31 || location == 32) side = 'Port';
            else if (location == 4 || location == 41 || location == 42) side = 'Starboard';
            banners.push({
                key: 'attached', color: theme.colors.statusOk, bg: 'rgba(50, 205, 50, 0.08)',
                text: 'Attached to ' + hostShip.name + (side ? ' [' + side + ']' : '')
            });
        }
    }

    //something hostile is attached to THIS ship
    if (ship.hasAttached && Object.keys(ship.hasAttached).length > 0) {
        banners.push({ key: 'boarded', color: theme.colors.statusAlert, bg: 'rgba(255, 165, 0, 0.10)', text: 'Ship is being boarded!' });
    }

    return banners;
};

/*Mirrors ShipTooltip.js's trueStealth block (including the own-ship check of the
  stealth system's per-team detected/detectedNew lists, which the plain isDetected
  call misses) so the banner can never claim Undetected while the tooltip says
  Detected. Loose compares kept deliberately — same as the tooltip.*/
const isUndetected = (ship, forecast) => {
    //deployment phase, being placed or arriving this turn: the tooltip always treats this as
    //unseen. Two turns qualify now that placement and arrival are separate - see ShipTooltip.js.
    if (gamedata.gamephase == -1 && (shipManager.getTurnPlaced(ship) == gamedata.turn
        || shipManager.getTurnDeployed(ship) == gamedata.turn)) return true;

    /*A Shading Field / Cloaking Device the player can still toggle this phase is answered by the
      caller's shipManager.getStealthToggleForecast - which is exactly what isDetected would return
      anyway, so take it directly and skip the second sweep. It has to win outright: the stored
      detected/detectedNew arrays the fallback below reads still hold the LAST committed check and
      would pin the banner off for the whole Pre-Turn phase. Same guard as ShipTooltip.js.*/
    if (forecast !== null && forecast !== undefined) return !forecast;

    let detected = shipManager.isDetected(ship);

    if (!detected && ship.team == gamedata.getPlayerTeam()) {
        let stealthSys = null;
        if (ship.mine) {
            stealthSys = shipManager.systems.getSystemByName(ship, 'mineStealth');
        } else if (ship.faction == 'Torvalus Speculators') {
            stealthSys = shipManager.systems.getSystemByName(ship, 'ShadingField');
        } else if (shipManager.getSpecialAbilityStealth(ship, 'Cloaking')) {
            stealthSys = shipManager.systems.getSystemByName(ship, 'CloakingDevice');
        } else if (shipManager.getSpecialAbilityStealth(ship, 'Stealth')) {
            stealthSys = shipManager.systems.getSystemByName(ship, 'stealth');
        }
        if (stealthSys) {
            if ((Array.isArray(stealthSys.detected) && stealthSys.detected.length > 0) || stealthSys.detected === true) {
                detected = true;
            } else if ((Array.isArray(stealthSys.detectedNew) && stealthSys.detectedNew.length > 0) || stealthSys.detectedNew === true) {
                detected = true;
            }
        }
    }

    return !detected;
};

/*Ships that use both quarter sections on a side purely for system placement but have
  only ONE side structure (e.g. Vorlon capitals: the real Port structure lives in 32
  "Port Aft", with 31 a structureless weapons shelf) label that lone structure header
  plain "Port"/"Starboard" (user request 2026-07-18). Sides with structures in more
  than one section keep the quarter names.*/
const getSectionNameOverrides = (systemsByLocation) => {
    const overrides = {};

    [{ locations: [3, 31, 32], name: 'Port' }, { locations: [4, 41, 42], name: 'Starboard' }].forEach(side => {
        const withStructure = side.locations.filter(location =>
            systemsByLocation[location].some(system => system.name === 'structure'));

        //a lone structure in location 3/4 already reads "Port"/"Starboard" - only the
        //quarter locations (31/32/41/42) need the override
        if (withStructure.length === 1 && withStructure[0] !== side.locations[0]) {
            overrides[withStructure[0]] = side.name;
        }
    });

    return overrides;
};

const sortIntoLocations = (ship) => {

    const locations = { 0: [], 1: [], 2: [], 3: [], 4: [], 5: [], 41: [], 42: [], 31: [], 32: [] };

    ship.systems.forEach((system) => {
        //hideInShipWindow: technical-only systems (e.g. InvulnerableThruster) are omitted from the
        //icon grid. Purely cosmetic - thrust mechanics iterate ship.systems directly, not this list.
        if (system.hideInShipWindow) return;
        locations[system.location].push(system);
    });

    return locations;
}

/*Grid template rows, built from whichever locations exist so absent sections collapse:
      "ctrl fwd   ew"      always - window chrome (buttons / Forward / EW panel)
      "lfwd prim  rfwd"    six-sided ships / big bases (31/41)
      "left prim  right"   ships with Port/Starboard (3/4)
      "laft prim  raft"    six-sided ships / big bases (32/42)
      ".    aft   ."       always when Aft exists
  Primary spans every middle row present (a contiguous rectangle, as grid requires).
  The three port-side rows are ordered fore→amidships→aft (lfwd, left, laft) so that
  a rare ship carrying BOTH a mid Port section (loc 3) AND its quarter sections
  (31 Port Fwd / 32 Port Aft) draws Port BETWEEN the two quarters rather than above
  them (user request 2026-07-18, aimed at Vree hulls). Ships with only the mid row,
  or only the quarter rows, are unaffected - just one of the three exists.

  The chrome areas then EXTEND DOWNWARD through consecutive rows whose side cell is
  otherwise empty (ctrl in column 1, ew in column 3). Without this, a tall
  buttons/stats or EW/datasheet stack inflates row 1 and Forward floats away from
  Primary (Stage 3 feedback round 2: huge Forward↔Primary gap on ships without side
  sections). Side-section rows always name BOTH side areas (left+right etc. as a
  pair) so a rolled ship's mirrored displayLocation always finds its area.

  withEnhArea (ship has purchased enhancements): an `enh` area is carved out
  of the BOTTOM-RIGHT cell (feedback round 3: keeping Enhancements out of the `ew`
  stack stops it lengthening row 1 further); the ew span stops above it.*/
const buildTemplateAreas = (locations, withEnhArea) => {
    //rows as [col1, col2, col3]; null = free cell (becomes "." unless a chrome span claims it)
    const rows = [['ctrl', 'fwd', 'ew']];
    let middleRows = 0;

    //fore→amidships→aft: quarter-forward (31/41), then mid Port/Starboard (3/4), then
    //quarter-aft (32/42) - so a mid section coexisting with quarters sits between them
    if (locations[31].length || locations[41].length) { rows.push(['lfwd', 'prim', 'rfwd']); middleRows++; }
    if (locations[3].length || locations[4].length) { rows.push(['left', 'prim', 'right']); middleRows++; }
    if (locations[32].length || locations[42].length) { rows.push(['laft', 'prim', 'raft']); middleRows++; }
    if (middleRows === 0 && locations[0].length) rows.push([null, 'prim', null]);
    if (locations[2].length) rows.push([null, 'aft', null]);

    if (withEnhArea) {
        const last = rows[rows.length - 1];
        if (rows.length > 1 && last[2] === null) {
            last[2] = 'enh'; //usually the Aft row's free right cell
        } else {
            rows.push([null, null, 'enh']); //last row's right cell occupied (e.g. quarter sections with no Aft)
        }
    }

    for (let i = 1; i < rows.length && rows[i][0] === null; i++) rows[i][0] = 'ctrl';
    for (let i = 1; i < rows.length && rows[i][2] === null; i++) rows[i][2] = 'ew';

    return rows.map(row => `"${row[0] || '.'}  ${row[1] || '.'}  ${row[2] || '.'}"`).join(' ');
}


export default ShipWindow;
