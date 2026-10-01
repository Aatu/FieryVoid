import * as React from "react";
import styled from 'styled-components';
import { ContainerShadowed, Clickable } from "../styled";
import theme from "../styled/theme";

/* A house - "back to the main page" - drawn the same way as FullScreen's corner brackets:
   inline SVG stroked in currentColor and sized off the box, so it needs no art file and
   matches its neighbour's line weight at every breakpoint. */
const LobbyIcon = () => (
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"
         strokeLinecap="round" strokeLinejoin="round" aria-hidden="true" focusable="false">
        <path d="M3 10.5L12 3l9 7.5" />
        <path d="M5 9v12h14V9" />
        <path d="M10 21v-6h4v6" />
    </svg>
);

/* Top-right HUD button that leaves the game for the games list (user request 2026-10-01).

   A real link rather than an onClick handler, so middle-click / ctrl-click opens the lobby in
   a new tab and leaves the game where it is. Nothing to confirm: orders are only ever stored
   by committing them, so leaving loses exactly what closing the tab would. Shown to everyone
   - replay viewers and spectators want a way out too - unlike Surrender beside it. */
class BackToLobby extends React.Component {
    render() {
        return (
            <MainButton as="a" href="games.php" title="Back to Lobby">
                <LobbyIcon />
            </MainButton>
        );
    }
}

/* Box geometry from theme.hud, shared with FullScreen, Surrender and PlayerSettings. It is the
   OUTERMOST of the four (left of FullScreen), so `right` is three buttons plus three gaps -
   see the note on the row order in FullScreen.js. */
const MainButton = styled(ContainerShadowed)`
    width: ${theme.hud.btn};
    height: ${theme.hud.btn};
    position: fixed;
    right: calc((${theme.hud.btn} + ${theme.hud.gap}) * 3);
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
        right: calc((${theme.hud.btnSmall} + ${theme.hud.gapSmall}) * 3);

        svg {
            width: ${theme.hud.iconSmall};
            height: ${theme.hud.iconSmall};
        }
    }
`;

export default BackToLobby;
