
import React from "react";
import styled, { css } from "styled-components";
import { ContainerShadowed, Clickable } from "../styled";
import theme from "../styled/theme";

class EwButtons extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            losToggled: false,
            hexToggled: false,
            // NOT a hard-coded true: a page that opens straight into replay may already have
            // been muted by the OPTIONS tab preference before this component mounted.
            soundToggled: gamedata.playAudio !== false,
            bgToggled: false,
            ebToggled: false,
            fbToggled: false,
            originalBgImage: null,
            replayMode: this.getReplayMode()
        };

        this.showFriendlyEW = this.showFriendlyEW.bind(this);
        this.showEnemyEW = this.showEnemyEW.bind(this);
        this.toggleFriendlyBallisticLines = this.toggleFriendlyBallisticLines.bind(this);
        this.toggleEnemyBallisticLines = this.toggleEnemyBallisticLines.bind(this);
        this.toggleLoS = this.toggleLoS.bind(this);
        this.externalToggleLoS = this.externalToggleLoS.bind(this);
        this.toggleHexNumbers = this.toggleHexNumbers.bind(this);
        this.externalToggleHexNumbers = this.externalToggleHexNumbers.bind(this);
        this.toggleSound = this.toggleSound.bind(this);
        this.externalToggleSound = this.externalToggleSound.bind(this);
        this.toggleBackground = this.toggleBackground.bind(this);
        this.externalToggleBackground = this.externalToggleBackground.bind(this);
        this.setBackground = this.setBackground.bind(this);
        this.externalSetBackground = this.externalSetBackground.bind(this);
    }

    getReplayMode() {
        return gamedata.replay || !gamedata.isPlayerInGame();
    }

    componentDidMount() {
        window.addEventListener("LoSToggled", this.externalToggleLoS);
        window.addEventListener("HexNumbersToggled", this.externalToggleHexNumbers);
        window.addEventListener("BackgroundToggled", this.externalToggleBackground);
        window.addEventListener("BackgroundPreferenceChanged", this.externalSetBackground);
        window.addEventListener("soundToggled", this.externalToggleSound);

        // OPTIONS tab -> "Never show map background": the standing preference, applied once
        // here. Clicking the button afterwards is a per-session override and is not saved.
        if (window.gameOptions && gameOptions.get("hideMapBackground")) {
            this.setBackground(true);
        }

        // 👇 periodically check for replay mode change
        this.replayCheck = setInterval(() => {
            const replayValue = this.getReplayMode();
            if (this.state.replayMode !== replayValue) {
                this.setState({ replayMode: replayValue });
            }
        }, 500);
    }

    componentWillUnmount() {
        window.removeEventListener("LoSToggled", this.externalToggleLoS);
        window.removeEventListener("HexNumbersToggled", this.externalToggleHexNumbers);
        window.removeEventListener("BackgroundToggled", this.externalToggleBackground);
        window.removeEventListener("BackgroundPreferenceChanged", this.externalSetBackground);
        window.removeEventListener("soundToggled", this.externalToggleSound);
        clearInterval(this.replayCheck);
    }

    externalToggleLoS() {
        this.setState({ losToggled: gamedata.showLoS });
    }

    externalToggleHexNumbers() {
        this.setState(prevState => ({
            hexToggled: !prevState.hexToggled
        }));
    }

    externalToggleSound() {
        this.setState({ soundToggled: gamedata.playAudio });
    }

    externalToggleBackground() {
        this.toggleBackground();
    }

    externalSetBackground(event) {
        this.setBackground(!!(event.detail && event.detail.off));
    }

    showFriendlyEW(up) {
        webglScene.customEvent("ShowFriendlyEW", { up });
    }

    showEnemyEW(up) {
        webglScene.customEvent("ShowEnemyEW", { up });
    }

    toggleFriendlyBallisticLines(up) {
        if (up) return;
        const newValue = !this.state.fbToggled;
        this.setState({ fbToggled: newValue });
        webglScene.customEvent("ToggleFriendlyBallisticLines", { up });
    }

    toggleEnemyBallisticLines(up) {
        if (up) return;
        const newValue = !this.state.ebToggled;
        this.setState({ ebToggled: newValue });
        webglScene.customEvent("ToggleEnemyBallisticLines", { up });
    }

    toggleLoS(up) {
        if (up) return;
        const newValue = !this.state.losToggled;
        this.setState({ losToggled: newValue });
        webglScene.customEvent("ToggleLoS", { up });
        window.dispatchEvent(new CustomEvent("LoSToggled"));
    }

    toggleHexNumbers(up) {
        if (up) return;
        const newValue = !this.state.hexToggled;
        this.setState({ hexToggled: newValue });
        webglScene.customEvent("ToggleHexNumbers", { up });
        window.dispatchEvent(new CustomEvent("HexNumbersToggled"));
    }

    // 🎵 New Sound Toggle Function
    toggleSound() {
        const newValue = !this.state.soundToggled;
        this.setState({ soundToggled: newValue });

        // Optionally call your audio manager or dispatch an event:
        webglScene.customEvent("ToggleSound", { enabled: newValue });
    }

    toggleBackground() {
        this.setBackground(!this.state.bgToggled);
    }

    // `off` true = background disabled (button faded). Setting the state it is already in is
    // a no-op, so the OPTIONS checkbox can ask for either state without knowing the current one.
    setBackground(off) {
        const bodyBg = document.getElementById("background");
        if (!bodyBg || off === this.state.bgToggled) return;

        let originalImage = this.state.originalBgImage;

        if (off) {
            // Turning ON the feature (background off - plain black)
            if (!originalImage) {
                originalImage = bodyBg.style.backgroundImage;
            }
            bodyBg.style.backgroundImage = 'none';
            bodyBg.style.backgroundColor = 'black';
        } else {
            // Turning OFF the feature (background on - original image)
            bodyBg.style.backgroundImage = originalImage || '';
            bodyBg.style.backgroundColor = '';
        }

        this.setState({ bgToggled: off, originalBgImage: originalImage });
    }

    render() {
        return (
            <Container>
                <FEWButton
                    onMouseDown={this.showFriendlyEW.bind(this, false)}
                    onMouseUp={this.showFriendlyEW.bind(this, true)}
                    onTouchStart={this.showFriendlyEW.bind(this, false)}
                    onTouchEnd={this.showFriendlyEW.bind(this, true)}
                />
                <EEWButton
                    onMouseDown={this.showEnemyEW.bind(this, false)}
                    onMouseUp={this.showEnemyEW.bind(this, true)}
                    onTouchStart={this.showEnemyEW.bind(this, false)}
                    onTouchEnd={this.showEnemyEW.bind(this, true)}
                />
                <FBButton
                    $showing={this.state.fbToggled}
                    onMouseDown={this.toggleFriendlyBallisticLines.bind(this, false)}
                />
                <EBButton
                    $showing={this.state.ebToggled}
                    onMouseDown={this.toggleEnemyBallisticLines.bind(this, false)}
                />
                <LoSButton
                    $showing={this.state.losToggled}
                    onMouseDown={this.toggleLoS.bind(this, false)}
                />
                <HexButton
                    $showing={this.state.hexToggled}
                    onMouseDown={this.toggleHexNumbers.bind(this, false)}
                />
                <BgButton
                    $showing={!this.state.bgToggled}
                    onMouseDown={this.toggleBackground}
                    title={this.state.bgToggled ? "Enable Background" : "Disable Background"}
                />

                {this.state.replayMode && (
                    <SoundButton
                        $toggled={this.state.soundToggled}
                        onMouseDown={this.toggleSound}
                        title={this.state.soundToggled ? "Sound On" : "Sound Off"}
                    />
                )}
            </Container>
        );
    }
}

// 🧩 Styled Components
const Container = styled.div`
    position: fixed;
    right: 0;
    top: 55px;
    z-index: 4;

    /* Narrow phones (portrait) OR short landscape phones: nudge up.
       Landscape phones report width > 765px, so key off short height too. */
    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        top: 40px;
    }

`;

/* Box geometry comes from theme.hud, shared with FullScreen and PlayerSettings so the
   three HUD button families cannot drift apart again (they had - see the note in theme.js).

   The old `font-size: 32px` / `20px` is GONE rather than tokenised: every button in this
   strip is a PNG background-image, so nothing here has ever rendered text and the
   font-size was doing nothing at all. It is exactly the "font-size used to scale the
   buttons" pattern the other two files really did suffer from, left behind here as a
   fossil. These icons scale with the BOX (background-size: cover), which is the behaviour
   the other two were changed to match. */
const MainButton = styled(ContainerShadowed)`
    display: flex;
    width: ${theme.hud.btn};
    height: ${theme.hud.btn};
    align-items: center;
    justify-content: center;
    border-right: none;
    margin-top: 3px;
    background-repeat: no-repeat;
    background-size: cover;
    ${Clickable}

    /* Shrink on narrow phones (portrait) AND short landscape phones — a phone
       held sideways is wider than 765px, so also match on short viewport height. */
    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        width: ${theme.hud.btnSmall};
        height: ${theme.hud.btnSmall};
    }
`;

const EEWButton = styled(MainButton)`
    background-image: url("./img/EEW.png");
`;
const FEWButton = styled(MainButton)`
    background-image: url("./img/FEW.png");
`;
/* On/off look for the toggle buttons. `$showing` = the thing the button controls is
   currently on the map. It is NOT the same as the component's *Toggled state for the
   background button, which is lit by default because the background shows by default.

   Off reads by FADING THE ICON, not by colour: the icon sits on its own pseudo-element
   layer so it can be dimmed without dimming the frame. Element opacity or filter on the
   button itself would fade the border and the windowBg fill too, and that fill already
   carries its own alpha (alpha compounding - see theme.js). On restores the icon and adds
   a quiet highlight from the chrome palette: the primary-section border, a faint
   linePrimary wash and a thin textAccent bar on the inner edge. This replaced the
   limegreen border/glow and the hue-rotate filter that turned the icons green
   (user request 2026-10-27). */
const toggleFrame = css`
    position: relative;
    border: 1px solid ${props => (props.$showing ? theme.colors.linePrimary : theme.colors.line)};
    border-right: none;
    background-image: ${props =>
        props.$showing
            ? "linear-gradient(90deg, rgba(96, 137, 193, 0.32), rgba(96, 137, 193, 0.08))"
            : "none"};
    box-shadow: ${props => (props.$showing ? `inset 2px 0 0 ${theme.colors.greenBtnLineHover}, ` : "")}5px 5px 10px ${theme.colors.overlayBg};
`;
const toggleIcon = css`
    opacity: ${props => (props.$showing ? 1 : 0.45)};
    //Only half grey: fully desaturated, the faded friendly (yellow) and enemy (orange)
    //ballistic icons become indistinguishable.
    filter: ${props => (props.$showing ? "none" : "grayscale(0.5)")};
    transition: opacity 0.15s ease, filter 0.15s ease;
`;
//A faded icon brightens a little under the pointer, so it still reads as a live control.
const toggleIconHover = css`
    opacity: ${props => (props.$showing ? 1 : 0.7)};
`;

const IconToggleButton = styled(MainButton)`
    ${toggleFrame}

    &::before {
        content: '';
        position: absolute;
        inset: 0;
        background-repeat: no-repeat;
        background-size: cover;
        ${toggleIcon}
    }
    &:hover::before {
        ${toggleIconHover}
    }
`;

const EBButton = styled(IconToggleButton)`
    &::before { background-image: url("./img/ballisticTarget2.png"); }
`;
const FBButton = styled(IconToggleButton)`
    &::before { background-image: url("./img/ballisticLaunch2.png"); }
`;
const LoSButton = styled(IconToggleButton)`
    &::before { background-image: url("./img/los1.png"); }
`;
const HexButton = styled(IconToggleButton)`
    &::before { background-image: url("./img/hexNumber.png"); }
`;

//No PNG for this one - its icon is the little drawn picture in ::after, so that is what fades.
const BgButton = styled(MainButton)`
    ${toggleFrame}

    &::after {
        content: '';
        position: absolute;
        width: 50%;
        height: 50%;
        background: linear-gradient(135deg, black 40%, #49915fff 60%);
        border: 2px solid #fdfdfdb4;
        border-radius: 4px;
        box-shadow: 1px 1px 3px rgba(0,0,0,0.5);
        margin-left: 4px;
        ${toggleIcon}
    }
    &:hover::after {
        ${toggleIconHover}
    }
`;

// 🎧 New Sound Button Style
const SoundButton = styled(MainButton)`
    background-image: ${props =>
        props.$toggled
            ? 'url("./img/soundOn.png")'
            : 'url("./img/soundOff.png")'};
    border: 1px solid ${theme.colors.line};
    border-right: none;
`;

export default EwButtons;

/* //Old version without audio.
   ⚠️ DEAD, and NOT revivable by uncommenting - it would need porting first. Two reasons:
   it calls `ContainerRoundedRightSide.extend`, which is the styled-components v3 API that
   was removed in v4, and that component no longer exists at all (item 6 collapsed the
   three ContainerRounded* variants into ContainerShadowed). Kept only as a record of the
   pre-audio behaviour; it was deliberately left out of the item-6 reskin.
import React from "react";
import styled from "styled-components";
import { ContainerRoundedRightSide, Clickable } from "../styled";

class EwButtons extends React.Component {
    constructor(props) {
        super(props);

    this.state = {
        losToggled: false,
        hexToggled: false
    };

        this.showFriendlyEW = this.showFriendlyEW.bind(this);
        this.showEnemyEW = this.showEnemyEW.bind(this);
        this.toggleFriendlyBallisticLines = this.toggleFriendlyBallisticLines.bind(this);
        this.toggleEnemyBallisticLines = this.toggleEnemyBallisticLines.bind(this);
        this.toggleLoS = this.toggleLoS.bind(this);
        this.externalToggleLoS = this.externalToggleLoS.bind(this); // 🔁 bind this method too                       
        this.toggleHexNumbers = this.toggleHexNumbers.bind(this);
        this.externalToggleHexNumbers = this.externalToggleHexNumbers.bind(this); // 🔁 bind this method too
                    
    }

    componentDidMount() {
        // 🔔 Listen for external toggle
        window.addEventListener("LoSToggled", this.externalToggleLoS); 
        window.addEventListener("HexNumbersToggled", this.externalToggleHexNumbers);       
    }

    componentWillUnmount() {
        // 🧹 Clean up listener
        window.removeEventListener("LoSToggled", this.externalToggleLoS); 
        window.removeEventListener("HexNumbersToggled", this.externalToggleHexNumbers);       
    }

    externalToggleLoS() {
        this.setState({
            losToggled: gamedata.showLoS
        });
    } 

    externalToggleHexNumbers() {
        this.setState((prevState) => ({
            hexToggled: !prevState.hexToggled
        }));
    }

    showFriendlyEW(up) {
        webglScene.customEvent("ShowFriendlyEW", { up: up });
    }

    showEnemyEW(up) {
        webglScene.customEvent("ShowEnemyEW", { up: up });
    }

    toggleFriendlyBallisticLines(up) {
        webglScene.customEvent("ToggleFriendlyBallisticLines", { up: up });
    }

    toggleEnemyBallisticLines(up) {
        webglScene.customEvent("ToggleEnemyBallisticLines", { up: up });
    }

    toggleLoS(up) {
        if (up) return; // Optional: prevent onMouseUp firing if needed
        const newValue = !this.state.losToggled;
        this.setState({ losToggled: newValue });        
        webglScene.customEvent("ToggleLoS", { up: up });
        // 🔔 Notify other components (like this one) of the toggle
        window.dispatchEvent(new CustomEvent("LoSToggled"));        
    }

    toggleHexNumbers(up) {
        if (up) return;

        const newValue = !this.state.hexToggled;
        this.setState({ hexToggled: newValue });

        // 🔁 Keep external logic (like PhaseStrategy) in sync
        webglScene.customEvent("ToggleHexNumbers", { up: up });

        // 🔔 Notify other components (like this one) of the toggle
        window.dispatchEvent(new CustomEvent("HexNumbersToggled"));
    }


    render() {
        return (
            <Container>
                <FEWButton 
                    onMouseDown={this.showFriendlyEW.bind(this, false)}
                    onMouseUp={this.showFriendlyEW.bind(this, true)}
                    onTouchStart={this.showFriendlyEW.bind(this, false)}
                    onTouchEnd={this.showFriendlyEW.bind(this, true)}                    
                ></FEWButton>
                <EEWButton  
                    onMouseDown={this.showEnemyEW.bind(this, false)}
                    onMouseUp={this.showEnemyEW.bind(this, true)}
                    onTouchStart={this.showEnemyEW.bind(this, false)}
                    onTouchEnd={this.showEnemyEW.bind(this, true)}                     
                ></EEWButton>
                <FBButton 
                    onMouseDown={this.toggleFriendlyBallisticLines.bind(this, false)}
                ></FBButton>
                <EBButton  
                    onMouseDown={this.toggleEnemyBallisticLines.bind(this, false)}
                ></EBButton>
                <LoSButton
                    toggled={this.state.losToggled}  
                    onMouseDown={this.toggleLoS.bind(this, false)}
                ></LoSButton>	                
                <HexButton
                    toggled={this.state.hexToggled}   
                    onMouseDown={this.toggleHexNumbers.bind(this, false)}
                ></HexButton>				
            </Container>
        );
    }
}

const Container = styled.div`
    position: fixed;
    right: 0;
    top: 60px;
    z-index: 4;
`;

const MainButton = ContainerRoundedRightSide.extend`
    display: flex;
    width: 45px;
    height: 45px;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    border-right: none;
    margin-top: 3px;
    background-repeat: no-repeat;
    background-size: cover;
    ${Clickable}
`;

const EEWButton = MainButton.extend`
    background-image: url("./img/EEW.png");
`;
const FEWButton = MainButton.extend`
    background-image: url("./img/FEW.png");
`;
const EBButton = MainButton.extend`
    background-image: url("./img/ballisticTarget2.png");
`;
const FBButton = MainButton.extend`
    background-image: url("./img/ballisticLaunch2.png");
`;
const LoSButton = MainButton.extend`
    background-image: url("./img/los1.png");
    filter: ${props => props.toggled ? 'brightness(1.6) sepia(0.85) hue-rotate(60deg) saturate(4)' : 'none'};
    border: 1px solid ${props => props.toggled ? 'limegreen' : '1px solid #496791'};
    border-right: none; 
    box-shadow: 0px 0px 0px black;        
`;
const HexButton = MainButton.extend`
    background-image: url("./img/hexNumber.png");
    filter: ${props => props.toggled ? 'brightness(1.6) sepia(0.85) hue-rotate(60deg) saturate(4)' : 'none'};
    border: 1px solid ${props => props.toggled ? 'limegreen' : '1px solid #496791'};
    border-right: none;    
`;

export default EwButtons;
*/