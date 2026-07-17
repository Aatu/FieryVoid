import * as React from "react";
import PlayerSettingsForm from "./PlayerSettingsForm";
import styled from 'styled-components';
import { ContainerRoundedRightBottom, Clickable } from "../styled";

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

const MainButton = styled(ContainerRoundedRightBottom)`
    width: 50px;
    height: 45px;
    position: fixed;
    right: 0;
    top: 0;
    z-index: 4;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 40px;
    padding-left: 5px;
    border-right: none;
    border-top: none;
    ${Clickable}

    /* Shrink on narrow phones (portrait) AND short landscape phones — a phone
       held sideways is wider than 765px, so also match on short viewport height. */
    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        width: 30px;
        height: 36px;
        /* Sized to sit inside the smaller box (28px overflowed — the ⚙ advance is
           wider than its font-size). This button is the flush top-RIGHT corner, so
           nudge the glyph toward that corner: left+bottom padding shifts a
           flex-centred glyph right+up so it doesn't read as low and left. */
        font-size: 24px;
        padding: 0 0 4px 4px;
    }
`;

export default PlayerSettings;