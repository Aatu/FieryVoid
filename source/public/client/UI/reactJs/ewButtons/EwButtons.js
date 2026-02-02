
import React from "react";
import styled from "styled-components";
import { ContainerRoundedRightSide, Clickable } from "../styled";

class EwButtons extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            losToggled: false,
            hexToggled: false,
            soundToggled: true,
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
    }

    getReplayMode() {
        return gamedata.replay || !gamedata.isPlayerInGame();
    }

    componentDidMount() {
        window.addEventListener("LoSToggled", this.externalToggleLoS);
        window.addEventListener("HexNumbersToggled", this.externalToggleHexNumbers);
        window.addEventListener("soundToggled", this.externalToggleSound);
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

    showFriendlyEW(up) {
        webglScene.customEvent("ShowFriendlyEW", { up });
    }

    showEnemyEW(up) {
        webglScene.customEvent("ShowEnemyEW", { up });
    }

    toggleFriendlyBallisticLines(up) {
        webglScene.customEvent("ToggleFriendlyBallisticLines", { up });
    }

    toggleEnemyBallisticLines(up) {
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
                <FBButton onMouseDown={this.toggleFriendlyBallisticLines.bind(this, false)} />
                <EBButton onMouseDown={this.toggleEnemyBallisticLines.bind(this, false)} />
                <LoSButton
                    $toggled={this.state.losToggled}
                    onMouseDown={this.toggleLoS.bind(this, false)}
                />
                <HexButton
                    $toggled={this.state.hexToggled}
                    onMouseDown={this.toggleHexNumbers.bind(this, false)}
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
    top: 60px;
    z-index: 4;
`;

const MainButton = styled(ContainerRoundedRightSide)`
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

const EEWButton = styled(MainButton)`
    background-image: url("./img/EEW.png");
`;
const FEWButton = styled(MainButton)`
    background-image: url("./img/FEW.png");
`;
const EBButton = styled(MainButton)`
    background-image: url("./img/ballisticTarget2.png");
`;
const FBButton = styled(MainButton)`
    background-image: url("./img/ballisticLaunch2.png");
`;
const LoSButton = styled(MainButton)`
    background-image: url("./img/los1.png");
    filter: ${props =>
        props.$toggled
            ? "brightness(1.6) sepia(0.85) hue-rotate(60deg) saturate(4)"
            : "none"};
    border: 1px solid ${props => (props.$toggled ? "limegreen" : "#496791")};
    border-right: none;
    box-shadow: 0px 0px 0px black;
`;
const HexButton = styled(MainButton)`
    background-image: url("./img/hexNumber.png");
    filter: ${props =>
        props.$toggled
            ? "brightness(1.6) sepia(0.85) hue-rotate(60deg) saturate(4)"
            : "none"};
    border: 1px solid ${props => (props.$toggled ? "limegreen" : "#496791")};
    border-right: none;
`;

// 🎧 New Sound Button Style
const SoundButton = styled(MainButton)`
    background-image: ${props =>
        props.$toggled
            ? 'url("./img/soundOn.png")'
            : 'url("./img/soundOff.png")'};
    border-right: none;
`;

export default EwButtons;

/* //Old version without audio
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