import * as React from "react";
import ReactDom from "react-dom";
import PlayerSettings from "./playerSettings/PlayerSettings";
import ShipThrust from "./shipThrust/ShipThrust";
import FullScreen from "./fullScreen/FullScreen";
import EwButtons from "./ewButtons/EwButtons";
import WeaponList from "./system/WeaponList";

class UIManager{

    constructor(parentElement) {
        this.parentElement = parentElement;
    }

    EwButtons(args) {
        ReactDom.render(<EwButtons {...args}/>, jQuery("#showEwButtons", this.parentElement)[0] );
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

    showWeaponList(args) {
        ReactDom.render(<WeaponList {...args}/>, jQuery("#weaponList", this.parentElement)[0] );
    }

    hideWeaponList() {
        ReactDom.unmountComponentAtNode(jQuery("#weaponList", this.parentElement)[0]);
    }

    
}

window.UIManager = UIManager;