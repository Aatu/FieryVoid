import * as React from "react";
import styled from 'styled-components';
import {ContainerRoundedRightSide, Clickable} from "../styled";

class EwButtons extends React.Component{

    showFriendly(up) {
        webglScene.customEvent('ShowFriendlyEW', {up: up})
    }

    showEnemy(up) {
        webglScene.customEvent('ShowEnemyEW', {up: up})
    }

    render(){
        return (
            <Container>
                <FEWButton 
                    onMouseDown={this.showFriendly.bind(this, false)}
                    onMouseUp={this.showFriendly.bind(this, true)}
                    onTouchStart={this.showFriendly.bind(this, false)}
                    onTouchEnd={this.showFriendly.bind(this, true)}
                ></FEWButton>
                <EEWButton  
                    onMouseDown={this.showEnemy.bind(this, false)}
                    onMouseUp={this.showEnemy.bind(this, true)}
                    onTouchStart={this.showEnemy.bind(this, false)}
                    onTouchEnd={this.showEnemy.bind(this, true)}
                ></EEWButton>
            </Container>
            );
    }
}

const Container = styled.div`
    position: fixed;
    right:0;
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
`

const FEWButton = MainButton.extend`
    background-image: url("./img/FEW.png");
`

export default EwButtons;