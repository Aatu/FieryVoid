import * as React from "react";
import PlayerSettingsForm from "./PlayerSettingsForm";
import styled from 'styled-components';
import { ContainerShadowed, Clickable } from "../styled";
import theme from "../styled/theme";

class PlayerSettings extends React.Component {

    constructor(props) {
        super(props);
        this.state = { open: false };
    }

    open() {
        this.setState({ open: true });
    };

    close() {
        this.setState({ open: false });
    };

    render() {
        if (!this.state.open) {
            return (<MainButton onClick={this.open.bind(this)}>⚙</MainButton>);
        }

        return (<PlayerSettingsForm close={this.close.bind(this)} {...this.props} />)
    }
}

/* Box geometry from theme.hud, shared with the EW strip and FullScreen.

   The phone box was the odd one out here (26x28 against its neighbours' 30x30) and it got
   that way honestly: when item 6 squared this button it genuinely did look oversized, and
   the box was shrunk to compensate. The real culprit was the GLYPH, not the box - a 22px ⚙
   in a 26px box is nearly wall-to-wall - so the fix is now applied where the problem was.
   The box matches its neighbours again and theme.hud.glyph sizes the gear.

   The paddings are an optical nudge, not layout: this button is flush into the top-RIGHT
   screen corner (no border on either of those sides), so a strictly flex-centred glyph
   reads as sitting low and left. Left+bottom padding shifts it back toward the corner.
   They are small and deliberately asymmetric; adjust by eye if the glyph size changes. */
const MainButton = styled(ContainerShadowed)`
    width: ${theme.hud.btn};
    height: ${theme.hud.btn};
    position: fixed;
    right: 0;
    top: 0;
    z-index: 4;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: ${theme.hud.glyph};
    border-right: none;
    border-top: none;
    ${Clickable}

    /* Shrink on narrow phones (portrait) AND short landscape phones — a phone
       held sideways is wider than 765px, so also match on short viewport height. */
    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        width: ${theme.hud.btnSmall};
        height: ${theme.hud.btnSmall};
        font-size: ${theme.hud.glyphSmall};

    }
`;

export default PlayerSettings;