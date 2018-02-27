import * as React from "react";
import ReactDom from "react-dom";
import PlayerSettings from "./PlayerSettings";

class UIManager{

    constructor(parentElement) {
        this.parentElement = parentElement;
    }

    PlayerSettings(element, args) {
        ReactDom.render(<PlayerSettings {...args}/>, jQuery("#playerSettings", this.parentElement)[0] );
    }
}

window.UIManager = UIManager;