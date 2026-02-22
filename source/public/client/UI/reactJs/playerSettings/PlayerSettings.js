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

    @media (max-width: 765px) {
        width: 30px;
        height: 36px;
        font-size: 28px;
        padding-left: 2px;
    }
`;

export default PlayerSettings;