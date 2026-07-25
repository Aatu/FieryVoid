import * as React from "react";
import styled from "styled-components"

import { Clickable } from "../styled";
import theme from "../styled/theme";
import ShipSection from "./ShipSection";
import ShipWindowEw from "./ShipWindowEw";
import FighterList from "./FighterList";
import HitChartPanel from "./HitChartPanel";
import ShipNotesPanel, { ManoeuvreStats, EnhancementsPanel } from "./ShipNotesPanel";
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
    position: relative;
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

    /*Touch screens get a finger-sized grab strip: the whole window is scaled DOWN to fit
      the screen, so a big lobby window at scale 0.6 turned this bar into ~15 visual px -
      a target you miss more often than you hit (2026-07-23 drag report). 44px is the
      usual minimum touch target and survives the scaling.*/
    @media (max-width: 1024px) {
        height: 44px;
    }
`;

const HeaderName = styled.span`
    font-size: 11px;
    line-height: 26px; /*centres the shared baseline within the 26px header bar*/

    @media (max-width: 1024px) {
        line-height: 44px; /*follows the taller touch grab strip above*/
    }
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

    /*matches the taller touch header - keeps the ✕ centred in the bar and gives it a
      finger-sized target of its own*/
    @media (max-width: 1024px) {
        height: 44px;
        width: 30px;
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
    padding: 8px;
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
      so it doesn't compensate). At 9px uppercase every half-pixel shows.*/
    padding: 3px 6px;
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
        //openPanel: clicked open (sticky until click-outside); hoverNotes: desktop
        //hover peek on the Notes button/popup, hides shortly after mouse-out; showArt:
        //Ship Art toggle - hides the sections and shows the hull art in full colour
        this.state = { openPanel: null, hoverNotes: false, showArt: false }; //openPanel: null | 'hitchart' | 'notes'
        this.notesHoverTimer = null;
        this.onDocumentPointerDown = this.onDocumentPointerDown.bind(this);
        this.onDragStart = this.onDragStart.bind(this);
        this.onDragMove = this.onDragMove.bind(this);
        this.onDragEnd = this.onDragEnd.bind(this);
        this.onTouchDragStart = this.onTouchDragStart.bind(this);
        this.onTouchDragMove = this.onTouchDragMove.bind(this);
        this.onTouchDragEnd = this.onTouchDragEnd.bind(this);
        this.onScreenResize = this.onScreenResize.bind(this);
        this.screenFit = 1; //applied scale factor (touch screens; 1 = untransformed)
        this.screenFitHeight = null; //applied max-height (layout px) that goes with it
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
      back as innerHeight / scale because it is a LAYOUT value - a shrunk window would
      otherwise keep an inner scrollbar for space it no longer needs, and a window that
      bottoms out on SCREEN_FIT_MIN still scrolls rather than being cut off.*/
    applyScreenFit() {
        const element = this.elementRef.current;
        if (!element) return;

        if (!isSmallScreen()) {
            if (this.screenFit !== 1 || this.screenFitHeight) {
                element.style.transform = '';
                element.style.transformOrigin = '';
                element.style.maxHeight = '';
                this.screenFit = 1;
                this.screenFitHeight = null;
            }
            return;
        }

        //measure unclamped: a transform never affects layout, so offsetWidth/Height stay
        //honest, but the height clamp from the last fit has to come off first
        const applied = element.style.maxHeight;
        element.style.maxHeight = 'none';
        const naturalWidth = element.offsetWidth;
        const naturalHeight = element.offsetHeight;
        element.style.maxHeight = applied;
        if (!naturalWidth || !naturalHeight) return;

        const availableWidth = (document.documentElement.clientWidth || window.innerWidth) * SCREEN_FIT_FILL;
        const availableHeight = (window.innerHeight || document.documentElement.clientHeight) * SCREEN_FIT_FILL;
        const fit = Math.min(availableWidth / naturalWidth, availableHeight / naturalHeight);

        let scale = Math.min(SCREEN_FIT_MAX, Math.max(SCREEN_FIT_MIN, fit));
        scale = Math.round(scale * 100) / 100; //2dp: stops poll re-renders jittering the scale

        const maxHeight = Math.round(window.innerHeight / scale);
        if (scale === this.screenFit && maxHeight === this.screenFitHeight) return; //nothing to write

        this.screenFit = scale;
        this.screenFitHeight = maxHeight;
        element.style.transformOrigin = isLeftWindow(this.props.ship) ? 'top left' : 'top right';
        element.style.transform = scale === 1 ? '' : 'scale(' + scale + ')';
        element.style.maxHeight = maxHeight + 'px';
    }

    onScreenResize() {
        this.applyScreenFit();
    }

    /*Header drag (2026-07-23, replaces jQuery UI `draggable`, whose mouse widget binds
      mousedown/mousemove only - a touchscreen never produced those, so windows could not
      be moved by finger at all).

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
      wired to a detached element.

      Contract unchanged: drag by `.shipwindow-drag-handle`, never from inside
      `.shipwindow-nodrag` (the ✕), position remembered per side on release.*/
    isDragHandle(target) {
        if (!target || !target.closest) return false;
        if (target.closest('.shipwindow-nodrag')) return false;
        return Boolean(target.closest('.shipwindow-drag-handle'));
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

        //a screen-fitted window is CSS-scaled, so a 100px finger move is only 100/scale
        //px of layout movement
        this.dragStart = { x: clientX, y: clientY, left: left, top: top, scale: this.screenFit || 1 };
        element.style.left = left + 'px';
        element.style.top = top + 'px';
        element.style.right = 'auto';
        return true;
    }

    moveDrag(clientX, clientY) {
        const element = this.elementRef.current;
        if (!this.dragStart || !element) return;

        const scale = this.dragStart.scale || 1;
        element.style.left = (this.dragStart.left + (clientX - this.dragStart.x) / scale) + 'px';
        element.style.top = (this.dragStart.top + (clientY - this.dragStart.y) / scale) + 'px';
    }

    finishDrag() {
        const element = this.elementRef.current;
        this.dragStart = null;

        //remember where the player left this side's window (feedback 2026-07-17)
        if (element) {
            savedWindowPositions[isLeftWindow(this.props.ship) ? 'left' : 'right'] = {
                top: parseFloat(element.style.top) || 0,
                left: parseFloat(element.style.left) || 0
            };
        }
    }

    //--- mouse / pen (pointer events) ---
    onDragStart(event) {
        //set window.FV_DRAG_DEBUG = true in the console to see which engine fires and
        //whether the press landed on the handle (remote-debugging a touch device)
        if (window.FV_DRAG_DEBUG) console.log('[shipwindow] pointerdown', event.pointerType, 'handle:', this.isDragHandle(event.target));
        if (event.pointerType === 'touch') return; //touch is handled by the touch engine below
        if (event.button != null && event.button > 0) return; //primary button only
        if (!this.isDragHandle(event.target)) return;
        if (!this.beginDrag(event.clientX, event.clientY)) return;

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
        if (!this.dragStart || event.pointerId !== this.dragPointerId) return;
        this.moveDrag(event.clientX, event.clientY);
    }

    onDragEnd(event) {
        if (!this.dragStart || (event && event.pointerId !== this.dragPointerId)) return;
        this.stopDragListening();
        this.finishDrag();
    }

    //--- touch ---
    onTouchDragStart(event) {
        if (window.FV_DRAG_DEBUG) console.log('[shipwindow] touchstart', event.touches && event.touches.length, 'handle:', this.isDragHandle(event.target));
        if (this.dragStart) return; //already dragging
        if (!event.touches || event.touches.length !== 1) return; //ignore pinches
        if (!this.isDragHandle(event.target)) return;

        const touch = event.touches[0];
        if (!this.beginDrag(touch.clientX, touch.clientY)) return;

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
        if (!this.dragStart || !touch) return;

        this.moveDrag(touch.clientX, touch.clientY);

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
        if (this.dragStart) this.finishDrag();
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
        }

        //close the Hit Chart / Notes popup on any press outside it (2026-07-16 feedback)
        document.addEventListener('pointerdown', this.onDocumentPointerDown);

        //touch screens: scale the window to the screen width (and keep it right through
        //rotation / browser-chrome resizes)
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
        if (this.notesHoverTimer) clearTimeout(this.notesHoverTimer);
    }

    onNotesHoverStart() {
        if (this.notesHoverTimer) {
            clearTimeout(this.notesHoverTimer);
            this.notesHoverTimer = null;
        }
        if (!this.state.hoverNotes) this.setState({ hoverNotes: true });
    }

    //short grace period so the pointer can cross from the button into the popup
    onNotesHoverEnd() {
        if (this.notesHoverTimer) clearTimeout(this.notesHoverTimer);
        this.notesHoverTimer = setTimeout(() => {
            this.notesHoverTimer = null;
            this.setState({ hoverNotes: false });
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

    renderHeader(shipName, unitName, tint) {
        return (
            <Header className="shipwindow-drag-handle">
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
                onMouseEnter={this.onNotesHoverStart.bind(this)}
                onMouseLeave={this.onNotesHoverEnd.bind(this)}
            >
                <CtrlIcon>✎</CtrlIcon>Notes
            </CtrlButton>
        );
    }

    /*Top-left control block, identical in structure on both pages (2026-07-23): Hit Chart,
      then Ship Art, then game.php's Notes button / the lobby's manoeuvre stats. The lobby's
      round-8/9 bottom-left `artbtn` grid cell is gone - the two toggles now always read as
      one stack. Compact windows (mines/terrain) render the same block as a centred row.*/
    renderControls(withHitChart, withNotes, compact, withStats) {
        const lobby = isLobby();
        const artHere = artAvailable(this.props.ship);

        if (!withHitChart && !withNotes && !withStats && !artHere) return null;

        const wide = lobby; //150px datasheet panels in the lobby, 130px in game

        return (
            <ControlsArea ref={this.controlsRef} $compact={compact}>
                {withHitChart && this.renderHitChartButton(wide)}
                {artHere && this.renderArtButton(wide)}
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
        //reports outer-border coords - without this the popup sits 1px right of the buttons
        return Math.round(
            controls.getBoundingClientRect().left
            - container.getBoundingClientRect().left
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
        return {
            left: Math.round(eRect.left - cRect.left - container.clientLeft),
            top: Math.round(eRect.bottom - cRect.top - container.clientTop) + 4,
        };
    }

    renderPopup(withHitChart, withNotes, top) {
        const { ship } = this.props;
        const { openPanel, hoverNotes } = this.state;

        //clicked panel wins; otherwise a desktop hover on the Notes button peeks the notes
        const shown = openPanel || (hoverNotes && withNotes ? 'notes' : null);
        if (!shown) return null;

        //the lobby stacks Hit Chart + the tall Ship Stats panel in the control block, so
        //its Hit Chart popup drops from the BUTTON (staying attached to it, over Ship Stats)
        //rather than the whole block; game.php / Notes / compact drop below the whole block
        //(keeps the popup clear of the buttons now that game.php stacks three of them).
        const anchorRef = (shown === 'hitchart' && isLobby()) ? this.hitChartBtnRef : this.controlsRef;
        const { top: anchorTop, left } = this.getAnchorBelow(anchorRef, top);

        if (shown === 'hitchart' && withHitChart) {
            //$fit (feedback 2026-07-17, supersedes round 5's full-width span): the
            //geographic columns size themselves, so the popup shrink-wraps them
            return <PopupHolder ref={this.popupRef} $top={anchorTop} $left={left} $fit><HitChartPanel ship={ship} /></PopupHolder>;
        }
        if (shown === 'notes' && withNotes) {
            return (
                <PopupHolder
                    ref={this.popupRef}
                    $top={anchorTop}
                    $left={left}
                    $fit
                    $notes
                    onMouseEnter={this.onNotesHoverStart.bind(this)}
                    onMouseLeave={this.onNotesHoverEnd.bind(this)}
                >
                    {/*ShipInfo itself decides whether to list enhancements inline: hidden
                       for full grid ships (they have the gold Enhancements box), shown for
                       mines / fighters / terrain (no box) - so no flag is needed here.
                       tightBottom drops its trailing blank line so PopupHolder's 5px
                       bottom padding sets the gap under the last note.*/}
                    <ShipInfo ship={ship} hideHitChart tightBottom />
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
                    </ShipWindowContainer>
                )
            }
            return (
                <ShipWindowContainer ref={this.elementRef} onClick={shipWindowClicked} onContextMenu={e => { e.preventDefault(); e.stopPropagation(); }} $isMyTeam={isMyTeam} $variant="flight">
                    {this.renderHeader(shipName, unitName, getHeaderTint(ship))}
                    <FighterList ship={ship} />
                    {renderStatusBanners(ship)}
                </ShipWindowContainer>
            )
        }

        if (isUnrevealedMine) {
            return (<ShipWindowContainer ref={this.elementRef} onClick={shipWindowClicked} onContextMenu={e => { e.preventDefault(); e.stopPropagation(); }} $isMyTeam={isMyTeam} $variant="terrain">
                {this.renderHeader(shipName, unitName, null)}
                <CompactBody>
                    <ShipHitArea onClick={this.onShipClick.bind(this)} />
                    <WatermarkLayer $img={window.AssetManager.getSmartImagePath(ship.imagePath)} />
                    <UnknownSystemIcon onMouseOver={this.onUnknownMouseOver.bind(this)} onMouseOut={this.onUnknownMouseOut.bind(this)} onTouchStart={this.onUnknownTouchStart.bind(this)} onTouchMove={this.onShipTouchMove.bind(this)} onTouchEnd={this.onShipTouchEnd.bind(this)} onTouchCancel={this.onShipTouchCancel.bind(this)}>?</UnknownSystemIcon>
                </CompactBody>
            </ShipWindowContainer>)
        }

        const systemsByLocation = sortIntoLocations(ship);
        //single-side-structure ships label that structure plain "Port"/"Starboard"
        const nameOverrides = getSectionNameOverrides(systemsByLocation);
        const isTerrain = window.gamedata.isTerrain(ship.shipSizeClass, ship.userid) || ship.mine;

        //terrain + revealed mines keep the compact thumbnail variant; in the lobby
        //(purchased mines) the datasheet renders as a full-width block underneath
        if (isTerrain) {
            return (<ShipWindowContainer ref={this.elementRef} onClick={shipWindowClicked} onContextMenu={e => { e.preventDefault(); e.stopPropagation(); }} $isMyTeam={isMyTeam} $variant="terrain">
                {this.renderHeader(shipName, unitName, null)}
                <CompactBody>
                    <ShipHitArea onClick={this.onShipClick.bind(this)} />
                    {/*Ship Art (item 3): the SAME watermark turns full colour in place while
                       the sections hide - no resize*/}
                    <WatermarkLayer $img={window.AssetManager.getSmartImagePath(ship.imagePath)} $art={this.state.showArt} />
                    {/*compact windows: vertical button list above the sections*/}
                    {this.renderControls(withHitChart, withNotes, true)}
                    {COMPACT_LOCATIONS.map(location => systemsByLocation[location].length > 0 && (
                        <ShipSection key={`section-${ship.id}-${location}`} location={location} nameOverride={nameOverrides[location]} ship={ship} systems={systemsByLocation[location]} isTerrain hidden={this.state.showArt} />
                    ))}
                </CompactBody>
                {renderStatusBanners(ship)}
                {lobby && <ShipNotesPanel ship={ship} full />}
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
                <ShipHitArea onClick={this.onShipClick.bind(this)} />
                {/*Ship Art (item 3): the SAME watermark turns full colour in place while the
                   sections hide - keeps the window/image at the exact same size (no resize).
                   $offsetY: the lobby's taller chrome pushes the sections below the grid
                   midline, so the art follows them down (2026-07-23).*/}
                <WatermarkLayer
                    $img={window.AssetManager.getSmartImagePath(ship.imagePath)}
                    $art={this.state.showArt}
                    $offsetY={lobby ? LOBBY_WATERMARK_OFFSET_Y : 0}
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
            {rolled && <StatusBanner>⟲ Rolled — port / starboard reversed</StatusBanner>}
            {renderStatusBanners(ship)}
            {this.renderPopup(withHitChart, withNotes)}
        </ShipWindowContainer>)
    }

}

const shipWindowClicked = () => window.uiEvents.relay('CloseSystemInfo');

//last dragged-to position per window side, session-lifetime (feedback 2026-07-17:
//reopened windows should appear where the player previously moved them)
const savedWindowPositions = { left: null, right: null };

/*>>> TOUCH-SCREEN FIT <<< (user request 2026-07-23, ShipWindow.applyScreenFit). Below
  this breakpoint the window is CSS-scaled to SCREEN_FIT_FILL of the screen in BOTH
  dimensions (whichever binds - height is what binds in landscape); the min/max keep that
  honest: never shrink past legibility, never blow a small window up into a poster. Raise
  SCREEN_FIT_MIN if scaled-down text gets unreadable - the window then scrolls internally
  instead of shrinking further. Same breakpoint as the ShipWindowContainer media query
  that docks windows to the screen edge.*/
const SMALL_SCREEN_QUERY = '(max-width: 1024px)';
const SCREEN_FIT_MIN = 0.5;
const SCREEN_FIT_MAX = 1.75;
//how much of the screen a fitted window may cover - the slack leaves the docking inset
//(4px/8px in the media query) visible on the opposite edge too
const SCREEN_FIT_FILL = 0.96;

const isSmallScreen = () => Boolean(window.matchMedia) && window.matchMedia(SMALL_SCREEN_QUERY).matches;

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
  taste.*/
const LOBBY_WATERMARK_OFFSET_Y = 20;

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

    //trueStealth ships: green "Undetected" (a Detected banner is deliberately omitted)
    if (ship.trueStealth && isUndetected(ship)) {
        banners.push({ key: 'undetected', color: theme.colors.statusOk, bg: 'rgba(50, 205, 50, 0.08)', text: 'Undetected' });
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
const isUndetected = (ship) => {
    //deployment phase, deploying this turn: the tooltip always treats this as unseen
    if (gamedata.gamephase == -1 && shipManager.getTurnDeployed(ship) == gamedata.turn) return true;

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

const renderStatusBanners = (ship) => getStatusBanners(ship).map(banner => (
    <StatusBanner key={banner.key} $color={banner.color} $bg={banner.bg}>{banner.text}</StatusBanner>
));

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
