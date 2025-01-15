import React from "react";
import ReactDOM from "react-dom";
import styled from "styled-components";
import { ContainerRoundedRightSide, Clickable } from "../styled";

class EwButtons extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            tooltipVisible: false,
            tooltipText: "",
            tooltipPosition: { top: 0, left: 0 }
        };

        this.handleMouseEnter = this.handleMouseEnter.bind(this);
        this.handleMouseLeave = this.handleMouseLeave.bind(this);
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


handleMouseEnter(event) {
    const buttonType = event.currentTarget.dataset.type; // Get the type of button
    let tooltipText = "";
    let offsetX = -60; // Default horizontal offset (left of the button)
    let offsetY = 0;   // Default vertical offset (centered vertically)

    // Determine the tooltip text and offsets based on button type
    switch (buttonType) {
        case "friendlyEW":
            tooltipText = "Show Friendly EW";
            offsetX = -107; // Move further left
            offsetY = -5; // Slightly above the center
            break;
        case "enemyEW":
            tooltipText = "Show Enemy EW";
            offsetX = -100; // Slightly less left
            offsetY = -10;  // Slightly below the center
            break;
        case "friendlyBallistic":
            tooltipText = "Toggle Friendly Ballistics";
            offsetX = -140; // Farther left
            offsetY = -7;  // Higher above
            break;
        case "enemyBallistic":
            tooltipText = "Toggle Enemy Ballistics";
            offsetX = -135;  // Closer to the button
            offsetY = -5;    // Centered vertically
            break;
        case "hexNumbers":
            tooltipText = "Toggle Hex Numbers";
            offsetX = -135;  // Closer to the button
            offsetY = -5;    // Centered vertically
            break;            
        default:
            tooltipText = "Unknown Button";
    }

    // Get button position and apply offsets
    const buttonRect = event.currentTarget.getBoundingClientRect();
    this.setState({
        tooltipVisible: true,
        tooltipText: tooltipText,
        tooltipPosition: {
            top: buttonRect.top + (buttonRect.height / 2) + offsetY,
            left: buttonRect.left + offsetX
        }
    });
}

    handleMouseLeave() {
        this.setState({ tooltipVisible: false });
    }

    renderTooltip() {
        if (!this.state.tooltipVisible) return null;

        return ReactDOM.createPortal(
            <Tooltip style={{ top: this.state.tooltipPosition.top, left: this.state.tooltipPosition.left }}>
                {this.state.tooltipText}
            </Tooltip>,
            document.body // Render tooltip at the root of the DOM
        );
    }

    render() {
        return (
            <Container>
				<FEWButton 
				    data-type="friendlyEW"
				    onMouseEnter={this.handleMouseEnter.bind(this)}
				    onMouseLeave={this.handleMouseLeave.bind(this)}
				    onMouseDown={this.showFriendlyEW.bind(this, false)}
				    onMouseUp={this.showFriendlyEW.bind(this, true)}
				></FEWButton>
				<EEWButton  
				    data-type="enemyEW"
				    onMouseEnter={this.handleMouseEnter.bind(this)}
				    onMouseLeave={this.handleMouseLeave.bind(this)}
				    onMouseDown={this.showEnemyEW.bind(this, false)}
				    onMouseUp={this.showEnemyEW.bind(this, true)}
				></EEWButton>
				<FBButton 
				    data-type="friendlyBallistic"
				    onMouseEnter={this.handleMouseEnter.bind(this)}
				    onMouseLeave={this.handleMouseLeave.bind(this)}
				    onMouseDown={this.toggleFriendlyBallisticLines.bind(this, false)}
				></FBButton>
				<EBButton  
				    data-type="enemyBallistic"
				    onMouseEnter={this.handleMouseEnter.bind(this)}
				    onMouseLeave={this.handleMouseLeave.bind(this)}
				    onMouseDown={this.toggleEnemyBallisticLines.bind(this, false)}
				></EBButton>
           {/* <HexButton  
                data-type="hexNumbers"
                onMouseEnter={this.handleMouseEnter.bind(this)}
                onMouseLeave={this.handleMouseLeave.bind(this)}
                onMouseDown={this.toggleHexNumbers.bind(this, false)}
            ></HexButton> */}				
                {this.renderTooltip()}
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
    width: 50px;
    height: 50px;
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

const Tooltip = styled.div`
    position: absolute;
    background-color: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 5px 10px;
    border-radius: 5px;
    font-size: 12px;
    pointer-events: none;
    z-index: 10;
    white-space: nowrap; /* Prevent text wrapping */
`;

export default EwButtons;