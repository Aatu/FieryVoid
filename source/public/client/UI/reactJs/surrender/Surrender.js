import * as React from "react";
import styled from 'styled-components';
import { ContainerShadowed, Clickable } from "../styled";
import theme from "../styled/theme";

/* Surrender used to be a <td class="surrender"> inside the Initial Orders phase header, which
   meant it existed for exactly one phase of the turn: a player who decided to concede during
   Movement or Firing had to sit through the rest of the turn first (player request 2026-08-03).
   It is now a permanent HUD button in the top-right cluster, alongside FullScreen and
   PlayerSettings, and is available in every phase.

   The action itself is unchanged - gamedata.onSurrenderClicked() puts up the same confirmation -
   but the server no longer treats the resulting POST as a set of orders for the current phase;
   see Manager::completeSurrender(). */
class Surrender extends React.Component {

    constructor(props) {
        super(props);
        this.state = { available: isSurrenderAvailable() };
        this.surrender = this.surrender.bind(this);
    }

    /* Polled rather than event-driven, matching EwButtons' replay check: nothing in the legacy
       client fires an event when the game status changes or a slot's `surrendered` lands, and
       every input this reads is refreshed by the same gamedata poll. */
    componentDidMount() {
        this.availabilityCheck = setInterval(() => {
            const available = isSurrenderAvailable();
            if (this.state.available !== available) {
                this.setState({ available });
            }
        }, 500);
    }

    componentWillUnmount() {
        clearInterval(this.availabilityCheck);
    }

    surrender() {
        window.gamedata.onSurrenderClicked();
    }

    render() {
        if (!this.state.available) {
            return null;
        }

        /* Cache-busted through AssetManager like every other image on this page (see the note on
           appendVersion in assetManager.js): public/.htaccess serves img/ with a 1-year immutable
           cache, so the redrawn 45x45 art would never have reached a client that had already
           loaded the old 30x30 file under this same filename - and the old art sits ~10% of its
           own canvas HIGH, which is exactly the "icon too high" symptom. A bare src would have
           left that stale everywhere but a hard refresh. */
        return (
            <MainButton onClick={this.surrender} title="Surrender">
                <img src={iconPath()} alt="" />
            </MainButton>
        );
    }
}

/* Hidden rather than disabled when surrendering is meaningless, because a dead button in the
   corner of a finished game invites a click that can only produce a server error:
     - replay viewers and spectators have no slot to surrender;
     - SURRENDERED/FINISHED means the game is already over (PhaseDirector has everyone in
       ReplayPhaseStrategy by then anyway);
     - a player whose own slot is already flagged has nothing left to concede - relevant only in
       3+ team games, where one team folding does not end the match. */
const isSurrenderAvailable = () => {
    if (typeof window.gamedata === 'undefined') return false;

    const gamedata = window.gamedata;

    if (gamedata.replay) return false;
    if (!gamedata.isPlayerInGame()) return false;
    if (gamedata.status === "SURRENDERED" || gamedata.status === "FINISHED") return false;

    for (const key in gamedata.slots) {
        const slot = gamedata.slots[key];
        if (slot.playerid != gamedata.thisplayer) continue;
        if (slot.surrendered !== null && slot.surrendered !== undefined) return false;
    }

    return true;
};

/* AssetManager also swaps in the .webp variant where the browser supports it, with a global
   onerror fallback to the .png - same treatment the ship and system icons get. Guarded because
   this component can render before the legacy bundle has defined window.AssetManager. */
const iconPath = () => {
    const path = "./img/surrender_icon.png";
    return window.AssetManager ? window.AssetManager.getSmartImagePath(path) : path;
};

/* Icon size is a LOCAL override of theme.hud.icon (26/17), not a retune of the shared token.
   theme.js already splits `icon` from `glyph` on exactly this reasoning - content types fill
   their box differently, so matching apparent size means different numbers - and a raster icon
   is a third case it does not cover. The flag PNG is a thin diagonal shape that leaves
   transparent margin inside its canvas, where FullScreen's SVG strokes corner to corner, so at
   the shared 26 it read noticeably smaller than its neighbours (user report 2026-08-03). Nudged up ~30% to sit
   level with them. Raising theme.hud.icon instead would have dragged FullScreen up with it.
   The source art is 45x45 (redrawn from the original 30x30 for this button), so these sizes
   downscale and stay crisp; going much past the 45px box would start upscaling. */
const ICON = "34px";
const ICON_SMALL = "22px";

/* ---- MANUAL TUNING KNOB ------------------------------------------------------------------
   Vertical offset of the flag inside its button. POSITIVE moves it DOWN, negative UP; edit
   these two numbers and nothing else. Applied as a transform, so it shifts only what is
   painted - the box, the hit area and the neighbours' spacing are untouched no matter how far
   it is pushed. ICON_NUDGE_Y is the desktop 45px button, ICON_NUDGE_Y_SMALL the 30px phone one
   (keep it roughly 2/3 of the desktop value, the same ratio the two icon sizes have, or the
   nudge will read as bigger on the small button than on the large).

   There is a knob here at all because a raster icon cannot be centred by CSS the way its
   neighbours can. FullScreen's SVG strokes corner to corner, so centring the BOX centres the
   mark; this PNG's ink sits at y13-y44 of a 45px canvas - the transparent margin is all at the
   top - so centring the box does NOT centre what the eye sees, and no amount of flexbox fixes
   that. The alternative is re-cropping the art, which then has to stay cropped through every
   future redraw. A number in one place is cheaper. */
const ICON_NUDGE_Y = "-4px";
const ICON_NUDGE_Y_SMALL = "-2px";

/* Box geometry from theme.hud, shared with FullScreen, PlayerSettings and the EW strip.

   `right` is DERIVED the same way FullScreen's is: this button sits between FullScreen and
   PlayerSettings, so the offset is one button width plus the 10px gap each pair has. Keeping
   it as arithmetic on theme.hud.btn means the row survives any future change to the shared
   box size.

   It swapped places with FullScreen on 2026-08-04 (user request) - it used to be the
   outermost of the three. See the fuller note in FullScreen.js. */
const MainButton = styled(ContainerShadowed)`
    width: ${theme.hud.btn};
    height: ${theme.hud.btn};
    position: fixed;
    right: calc(${theme.hud.btn} + 10px);
    top: 0;
    z-index: 4;
    display: flex;
    align-items: center;
    justify-content: center;
    border-top: none;
    ${Clickable}

    img {
        width: ${ICON};
        height: ${ICON};
        display: block;
        transform: translateY(${ICON_NUDGE_Y});
    }

    /* Clickable's hover cue is text-shadow + colour, which a raster icon cannot answer -
       FullScreen gets away with it because its SVG strokes in currentColor. Lift the PNG
       instead, the same way the EW strip signals state on its background art. */
    &:hover img {
        filter: brightness(1.4);
    }

    /* Shrink on narrow phones (portrait) AND short landscape phones — a phone
       held sideways is wider than 765px, so also match on short viewport height. */
    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        width: ${theme.hud.btnSmall};
        height: ${theme.hud.btnSmall};
        right: calc(${theme.hud.btnSmall} + 10px);

        img {
            width: ${ICON_SMALL};
            height: ${ICON_SMALL};
            transform: translateY(${ICON_NUDGE_Y_SMALL});
        }
    }
`;

export default Surrender;
