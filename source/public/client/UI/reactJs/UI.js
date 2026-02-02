import * as React from "react";
import { createRoot } from "react-dom/client";
import PlayerSettings from "./playerSettings/PlayerSettings";
import ShipThrust from "./shipThrust/ShipThrust";
import FullScreen from "./fullScreen/FullScreen";
import EwButtons from "./ewButtons/EwButtons";
import WeaponList from "./system/WeaponList";
import SystemInfo from "./system/SystemInfo";
import SystemInfoMenu from "./system/SystemInfoMenu";
import { canDoAnything } from "./system/SystemInfoButtons";
import ShipWindowsContainer from "./shipWindow/ShipWindowsContainer";

class UIManager {

    constructor(parentElement) {
        this.parentElement = parentElement;
        this.roots = new Map();
    }

    getRoot(selector) {
        const element = jQuery(selector, this.parentElement)[0];
        if (!element) return null;

        if (!this.roots.has(element)) {
            this.roots.set(element, createRoot(element));
        }
        return this.roots.get(element);
    }

    unmountRoot(selector) {
        const element = jQuery(selector, this.parentElement)[0];
        if (element && this.roots.has(element)) {
            const root = this.roots.get(element);
            root.unmount();
            this.roots.delete(element);
        }
    }

    EwButtons(args) {
        const root = this.getRoot("#showEwButtons");
        if (root) root.render(<EwButtons {...args} />);
    }

    FullScreen(args) {
        const root = this.getRoot("#fullScreen");
        if (root) root.render(<FullScreen {...args} />);
    }

    PlayerSettings(args) {
        const root = this.getRoot("#playerSettings");
        if (root) root.render(<PlayerSettings {...args} />);
    }

    showShipThrustUI(args) {
        const root = this.getRoot("#shipThrust");
        if (root) root.render(<ShipThrust {...args} />);
    }

    hideShipThrustUI() {
        this.unmountRoot("#shipThrust");
    }

    showWeaponList(args) {
        const root = this.getRoot("#weaponList");
        if (root) root.render(<WeaponList {...args} />);
    }

    hideWeaponList() {
        this.unmountRoot("#weaponList");
    }

    showSystemInfo(args) {
        const root = this.getRoot("#systemInfoReact");
        if (root) root.render(<SystemInfo {...args} />);
    }

    hideSystemInfo() {
        this.unmountRoot("#systemInfoReact");
    }

    showSystemInfoMenu(args) {
        const root = this.getRoot("#systemInfoReact");
        if (root) root.render(<SystemInfoMenu {...args} />);
    }

    hideSystemInfoMenu() {
        this.unmountRoot("#systemInfoReact");
    }

    canShowSystemInfoMenu(ship, system) {
        return canDoAnything(ship, system);
    }

    renderShipWindows(args) {
        const root = this.getRoot("#shipWindowsReact");
        if (root) root.render(<ShipWindowsContainer {...args} />);
    }
}

window.UIManager = UIManager;