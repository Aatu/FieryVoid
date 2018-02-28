import * as React from "react";
import PlayerSettingsForm from "./PlayerSettingsForm";
import {ContainerRoundedRightBottom, Clickable} from "../styled";

class PlayerSettings extends React.Component{

    constructor(props) {
        super(props);
        this.state = {open: false};
    }

    open() {
        this.setState({open: true});
    };

    close() {
        this.setState({open: false});
    };

    render(){
        if (! this.state.open) {
            return (<MainButton onClick={this.open.bind(this)}>⚙</MainButton>);
        }

        return (<PlayerSettingsForm close={this.close.bind(this)} {...this.props}/>)
    }
}

const MainButton = ContainerRoundedRightBottom.extend`
    width: 50px;
    height: 50px;
    position: absolute;
    right: 0;
    top: 0;
    z-index: 4;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 46px;
    padding-left: 5px;
    border-right: none;
    ${Clickable}
`;

export default PlayerSettings;