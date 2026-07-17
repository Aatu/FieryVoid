import * as React from "react";
import styled from 'styled-components';
import { ContainerRounded, Clickable } from "../styled";

class FullScreen extends React.Component {

    fullScreen() {
        /*
        if (! document.fullscreenElement ) {
            var doc = window.document;
            var docEl = doc.documentElement;
        
            var requestFullScreen = docEl.requestFullscreen || docEl.mozRequestFullScreen || docEl.webkitRequestFullScreen || docEl.msRequestFullscreen;
            requestFullScreen.call(docEl);
        }
        */

        var doc = window.document;
        var docEl = doc.documentElement;

        var requestFullScreen = docEl.requestFullscreen || docEl.mozRequestFullScreen || docEl.webkitRequestFullScreen || docEl.msRequestFullscreen;
        var cancelFullScreen = doc.exitFullscreen || doc.mozCancelFullScreen || doc.webkitExitFullscreen || doc.msExitFullscreen;

        if (!doc.fullscreenElement && !doc.mozFullScreenElement && !doc.webkitFullscreenElement && !doc.msFullscreenElement) {
            requestFullScreen.call(docEl);
        }
        else {
            cancelFullScreen.call(doc);
        }

    }
    render() {
        return (<MainButton onClick={this.fullScreen.bind(this)}>FS</MainButton>);
    }
}

const MainButton = styled(ContainerRounded)`
    width: 50px;
    height: 45px;
    position: fixed;
    right: 60px;
    top: 0;
    z-index: 4;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    border-top: none;
    ${Clickable}

    /* Shrink on narrow phones (portrait) AND short landscape phones — a phone
       held sideways is wider than 765px, so also match on short viewport height. */
    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        width: 30px;
        height: 30px;
        right: 40px;
        /* "FS" sized to sit comfortably inside the 30px box (was 20px, too large). */
        font-size: 16px;
    }
`;

export default FullScreen;