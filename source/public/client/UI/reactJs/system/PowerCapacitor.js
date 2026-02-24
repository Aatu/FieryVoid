import React, { Component } from 'react';
import styled from 'styled-components';

const Container = styled.div`
    display: flex;
    flex-direction: column;
    margin-top: 5px;
    width: 100%;
    min-width: 180px;
    opacity: 0.95;
    background-color: rgba(16, 26, 38, 0.9);
    border: 1px solid #496791;
`;

const Header = styled.div`
    padding: 3px;
    background-color: #496791;
    border: 1px solid #496791;
    border-bottom: 1px solid #496791;    
    color: #deebff;
    text-align: center;
    font-size: 12px;
    margin-bottom: 2px;
    opacity: 1 !important;     
    font-weight: bold;
`;

const Row = styled.div`
    display: flex;
    align-items: center;
    padding: 3px 8px;
    border-bottom: 1px solid #496791;
    font-size: 11px;
    color: #deebff;
    justify-content: space-between;

    &:last-child {
        border-bottom: none;
    }

    &:hover {
        background-color: rgba(73, 103, 145, 0.4);
    }
`;

const Label = styled.div`
    flex: 1;
`;

const Controls = styled.div`
    display: flex;
    align-items: center;     
    gap: 5px;
`;

const Value = styled.div`
    min-width: 20px;
    text-align: center;
`;

const ActionButton = styled.div`
    width: 24px;
    height: 18px;
    background: #203348;
    border: 1px solid #496791;
    color: #deebff;
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 10px;
    padding: 0;
    opacity: 0.9;
    user-select: none;

    &:hover {
        background: #496791;
        color: #ffffff;
        opacity: 1;
    }

    ${props => props.disabled && `
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #203348; color: #deebff; }
    `}

    ${props => props.$active && `
        background:  #806c00; 
        color: white;
        border: 1px solid #e6c300;
        opacity: 1;
    `}
`;


class PowerCapacitor extends Component {

    handleBoost() {
        if (this.canBoost()) {
            shipManager.power.clickPlus(this.props.ship, this.props.system);
            this.forceUpdate();
            webglScene.customEvent('SystemDataChanged', { ship: this.props.ship, system: this.props.system });
        }
    }

    handleDeBoost() {
        if (this.canDeBoost()) {
            shipManager.power.clickMinus(this.props.ship, this.props.system);
            this.forceUpdate();
            webglScene.customEvent('SystemDataChanged', { ship: this.props.ship, system: this.props.system });
        }
    }

    handleActivate() {
        if (this.canActivate()) {
            this.props.system.doActivate();
            this.forceUpdate();
            webglScene.customEvent('SystemDataChanged', { ship: this.props.ship, system: this.props.system });
        }
    }

    handleDeactivate() {
        if (this.canDeactivate()) {
            this.props.system.doDeactivate();
            this.forceUpdate();
            webglScene.customEvent('SystemDataChanged', { ship: this.props.ship, system: this.props.system });
        }
    }

    canBoost() {
        const { ship, system } = this.props;
        return system.boostable && gamedata.gamephase === 1 && shipManager.power.canBoost(ship, system);
    }

    canDeBoost() {
        const { ship, system } = this.props;
        return gamedata.gamephase === 1 && Boolean(shipManager.power.getBoost(system));
    }

    canActivate() {
        return this.props.system.canActivate();
    }

    canDeactivate() {
        return this.props.system.canDeactivate();
    }

    render() {
        const { ship, system } = this.props;
        const boostLevel = shipManager.power.getBoost(system);
        const isActive = system.active;

        return (
            <Container>
                <Header>Power Capacitor</Header>
                {system.boostable &&
                    <Row>
                        <Label>Open Petals</Label>
                        <Controls>
                            <ActionButton onClick={() => this.handleDeBoost()} disabled={!this.canDeBoost()} $active={boostLevel === 0}>OFF</ActionButton>
                            <ActionButton onClick={() => this.handleBoost()} disabled={!this.canBoost()} $active={boostLevel > 0}>ON</ActionButton>
                        </Controls>
                    </Row>
                }
                <Row>
                    <Label>Double Recharge</Label>
                    <Controls>
                        <ActionButton onClick={() => this.handleDeactivate()} disabled={!this.canDeactivate()} $active={!isActive}>OFF</ActionButton>
                        <ActionButton onClick={() => this.handleActivate()} disabled={!this.canActivate()} $active={isActive}>ON</ActionButton>
                    </Controls>
                </Row>
            </Container>
        );
    }
}

export default PowerCapacitor;
