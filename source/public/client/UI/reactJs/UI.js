import * as React from "react";
import ReactDom from "react-dom";
import PlayerSettings from "./playerSettings/PlayerSettings";
import ShipThrust from "./shipThrust/ShipThrust";
import FullScreen from "./fullScreen/FullScreen"

class UIManager{

    constructor(parentElement) {
        this.parentElement = parentElement;
    }

    FullScreen(args) {
        ReactDom.render(<FullScreen {...args}/>, jQuery("#fullScreen", this.parentElement)[0] );
    }

    PlayerSettings(args) {
        ReactDom.render(<PlayerSettings {...args}/>, jQuery("#playerSettings", this.parentElement)[0] );
    }

    showShipThrustUI(args) {
        ReactDom.render(<ShipThrust {...args}/>, jQuery("#shipThrust", this.parentElement)[0] );
    }

    hideShipThrustUI() {
        ReactDom.unmountComponentAtNode(jQuery("#shipThrust", this.parentElement)[0]);
    }
}

window.UIManager = UIManager;