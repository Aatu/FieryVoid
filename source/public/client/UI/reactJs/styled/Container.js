import styled from "styled-components";
import theme from "./theme";

/* The map-overlay shell, shared by EwButtons, FullScreen and PlayerSettings.

   This was a styled-components transcription of tactical.css #phaseheader - same teal
   border, same fill, same 5px/5px/10px black shadow - so the jQuery chrome and these
   overlays were one 2011 design declared twice. Roadmap item 6 converged it on the item-5
   SCS language: it now reads the same two values the ship windows paint with
   (ShipWindowContainer in shipWindow/ShipWindow.js uses exactly this line + fill).

   theme.colors.chrome* still hold the OLD values. Nothing paints with them any more, but
   they are the documented revert path for this surface - see the note on them in theme.js. */
const Container = styled.div`
    border: 1px solid ${theme.colors.line};
    color: ${theme.colors.chromeText};
    background-color: ${theme.colors.windowBg};
    font-family:${theme.fonts.body};
`;

const Backdrop = styled.div`
    width: 100%;
    height: 100%;
    position: absolute;
    right: 0;
    top: 0;
    z-index: 99999;
    background-color: rgba(0,0,0,0.5);
`;

/* Container plus a drop shadow - what every map-edge overlay actually wants.

   This replaces THREE components that used to differ only in which corners they rounded:
   ContainerRounded (bottom two), ContainerRoundedRightBottom (bottom-left) and
   ContainerRoundedRightSide (the left pair). Once item 6 squared the overlays, all three
   collapsed to the same declarations - three names, one behaviour, and names that actively
   lied, since none of them rounded anything any more.

   There is no border-radius here at all: square is simply Container's default. If the pill
   corners are ever wanted back, the per-variant corner sets are in git history (they were
   removed 2026-07-30) - restoring them means restoring the three components, not flipping
   a token, because a single shared component cannot express three different corner sets. */
const ContainerShadowed = styled(Container)`
    box-shadow: 5px 5px 10px ${theme.colors.overlayBg};
`;

export { Container, ContainerShadowed, Backdrop };
