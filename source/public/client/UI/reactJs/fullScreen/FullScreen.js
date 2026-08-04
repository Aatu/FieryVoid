import * as React from "react";
import styled from 'styled-components';
import { ContainerShadowed, Clickable } from "../styled";
import theme from "../styled/theme";

/* The four-corner-brackets icon, the conventional "expand to full screen" mark. Replaces
   the literal text "FS" (user request 2026-07-30), which was both untranslatable jargon
   and the reason this button had to be 50px wide when its neighbours were 45 - two letters
   need more room than one glyph.

   Inline SVG rather than another PNG: it needs no new art file, it stays crisp at any DPI,
   it inherits the button's text colour through currentColor (so it followed the item-6
   reskin for free), and it is sized in CSS off the BOX rather than off a font-size, which
   is the whole point of the change. */
const FullScreenIcon = () => (
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"
         strokeLinecap="round" strokeLinejoin="round" aria-hidden="true" focusable="false">
        <path d="M9 3H3v6" />
        <path d="M15 3h6v6" />
        <path d="M9 21H3v-6" />
        <path d="M15 21h6v-6" />
    </svg>
);

class FullScreen extends React.Component {

    fullScreen() {
        /*
        if (! document.fullscreenElement ) {
            var doc = window.document;
            var docEl = doc.documentElement;
        
            var requestFullScreen = docEl.requestFullscreen || docEl.mozRequestFullScreen || docEl.webkitRequestFullScreen || docEl.msRequestFullscreen;
            requestFullScreen.call(docEl);
        }
        */

        var doc = window.document;
        var docEl = doc.documentElement;

        var requestFullScreen = docEl.requestFullscreen || docEl.mozRequestFullScreen || docEl.webkitRequestFullScreen || docEl.msRequestFullscreen;
        var cancelFullScreen = doc.exitFullscreen || doc.mozCancelFullScreen || doc.webkitExitFullscreen || doc.msExitFullscreen;

        if (!doc.fullscreenElement && !doc.mozFullScreenElement && !doc.webkitFullscreenElement && !doc.msFullscreenElement) {
            requestFullScreen.call(docEl);
        }
        else {
            cancelFullScreen.call(doc);
        }

    }
    render() {
        return (
            <MainButton onClick={this.fullScreen.bind(this)} title="Full screen">
                <FullScreenIcon />
            </MainButton>
        );
    }
}

/* Box geometry from theme.hud, shared with the EW strip and PlayerSettings. Was 50x45 -
   the odd one out among 45x45 neighbours - purely because the "FS" text needed the extra
   width; with an icon it does not, so it squares up with the rest.

   `right` is DERIVED rather than hard-coded: this is the OUTERMOST of the three buttons in
   the row, so the offset is two button widths plus the 10px gap each pair has. Previously
   these were literals (60px / 40px) that had to be re-guessed every time either button was
   resized - and had already drifted to a 15px/14px gap. Now the spacing survives any future
   change to the shared box size.

   It swapped places with Surrender on 2026-08-04 (user request): the row reads FullScreen,
   Surrender, PlayerSettings left to right, where it used to read Surrender, FullScreen,
   PlayerSettings. If you swap them back, the two `right` expressions here and in
   Surrender.js are the whole change - nothing else encodes the order. */
const MainButton = styled(ContainerShadowed)`
    width: ${theme.hud.btn};
    height: ${theme.hud.btn};
    position: fixed;
    right: calc((${theme.hud.btn} * 2) + 20px);
    top: 0;
    z-index: 4;
    display: flex;
    align-items: center;
    justify-content: center;
    border-top: none;
    ${Clickable}

    svg {
        width: ${theme.hud.icon};
        height: ${theme.hud.icon};
        display: block;
    }

    /* Shrink on narrow phones (portrait) AND short landscape phones — a phone
       held sideways is wider than 765px, so also match on short viewport height. */
    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        width: ${theme.hud.btnSmall};
        height: ${theme.hud.btnSmall};
        right: calc((${theme.hud.btnSmall} * 2) + 20px);

        svg {
            width: ${theme.hud.iconSmall};
            height: ${theme.hud.iconSmall};
        }
    }
`;

export default FullScreen;