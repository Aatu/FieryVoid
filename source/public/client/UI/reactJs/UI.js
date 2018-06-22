import * as React from "react";
import ReactDom from "react-dom";
import PlayerSettings from "./playerSettings/PlayerSettings";
import ShipThrust from "./shipThrust/ShipThrust";
import FullScreen from "./fullScreen/FullScreen";
import EwButtons from "./ewButtons/EwButtons";
import WeaponList from "./system/WeaponList";
import SystemInfo from "./system/SystemInfo";
import SystemInfoMenu from "./system/SystemInfoMenu";
import {canDoAnything} from "./system/SystemInfoButtons";
import ShipWindowsContainer from "./shipWindow/ShipWindowsContainer";

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

    showSystemInfo(args) {
        ReactDom.render(<SystemInfo {...args}/>, jQuery("#systemInfoReact", this.parentElement)[0] );
    }

    hideSystemInfo() {
        ReactDom.unmountComponentAtNode(jQuery("#systemInfoReact", this.parentElement)[0]);
    }

    showSystemInfoMenu(args) {
        ReactDom.render(<SystemInfoMenu {...args}/>, jQuery("#systemInfoReact", this.parentElement)[0] );
    }

    hideSystemInfoMenu() {
        ReactDom.unmountComponentAtNode(jQuery("#systemInfoReact", this.parentElement)[0]);
    }

    canShowSystemInfoMenu(ship, system) {
        return canDoAnything(ship, system);
    }

    renderShipWindows(args) {
        ReactDom.render(<ShipWindowsContainer {...args}/>, jQuery("#shipWindowsReact", this.parentElement)[0] );
    }
}

window.UIManager = UIManager;