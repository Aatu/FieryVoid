import * as React from "react";
import styled from "styled-components";
import { ContainerRounded, Clickable } from "../styled";

const MainButton = styled(ContainerRounded)`
  width: 50px;
  height: 50px;
  position: fixed;
  right: 60px;
  top: 0;
  z-index: 4;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 32px;
  border-top: none;
  ${Clickable}
`;

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

    var requestFullScreen =
      docEl.requestFullscreen ||
      docEl.mozRequestFullScreen ||
      docEl.webkitRequestFullScreen ||
      docEl.msRequestFullscreen;
    var cancelFullScreen =
      doc.exitFullscreen ||
      doc.mozCancelFullScreen ||
      doc.webkitExitFullscreen ||
      doc.msExitFullscreen;

    if (
      !doc.fullscreenElement &&
      !doc.mozFullScreenElement &&
      !doc.webkitFullscreenElement &&
      !doc.msFullscreenElement
    ) {
      requestFullScreen.call(docEl);
    } else {
      cancelFullScreen.call(doc);
    }
  }
  render() {
    return <MainButton onClick={this.fullScreen.bind(this)}>FS</MainButton>;
  }
}

export default FullScreen;
