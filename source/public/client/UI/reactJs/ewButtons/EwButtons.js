import React from "react";
import styled from "styled-components";
import { ContainerRoundedRightSide, Clickable } from "../styled";

class EwButtons extends React.Component {
    constructor(props) {
        super(props);

        this.showFriendlyEW = this.showFriendlyEW.bind(this);
        this.showEnemyEW = this.showEnemyEW.bind(this);
        this.toggleFriendlyBallisticLines = this.toggleFriendlyBallisticLines.bind(this);
        this.toggleEnemyBallisticLines = this.toggleEnemyBallisticLines.bind(this);
        this.toggleHexNumbers = this.toggleHexNumbers.bind(this);        
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

    toggleHexNumbers(up) {
        webglScene.customEvent("ToggleHexNumbers", { up: up });
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
				<HexButton  
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
    margin-top: 5px;
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
const HexButton = MainButton.extend`
    background-image: url("./img/hexNumber.png");
`;

export default EwButtons;