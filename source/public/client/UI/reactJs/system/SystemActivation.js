import React, { Component } from 'react';
import styled from 'styled-components';

const Container = styled.div`
    display: flex;
    flex-direction: column;
    margin-top: 0px;
    width: 100%;
    min-width: 160px;
    opacity: 0.95 !important;
    background-color: rgba(16, 26, 38, 0.9);
    border: 1px solid #496791;
`;

const Header = styled.div`
    padding: 3px;
    background-color: #4967919a;
    border: 1px solid #496791;
    border-bottom: 1px solid #496791;    
    color: #deebff;
    text-align: center;
    font-size: 11px;
    margin-bottom: 2px;
    opacity: 0.95 !important;     
    font-weight: bold;
`;

const Row = styled.div`
    display: flex;
    align-items: center;
    padding: 1px 1px;
    border-bottom: 1px solid #496791;
    font-size: 12px;
    color: #deebff;
    justify-content: center;

    &:last-child {
        border-bottom: none;
    }

`;

const Label = styled.div`
    flex: 1;
    margin-right: 10px;    
`;

const Controls = styled.div`
    display: flex;
    align-items: center;     
    gap: 5px;
    width: 100%;
    padding: 2px;
`;

const ActionButton = styled.div`
    flex: 1;
    height: 18px;
    background: #203348;
    border: 1px solid #496791;
    color: #deebff;
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 11px;
    padding: 0;
    opacity: 0.9;
    user-select: none;

    &:hover {
        background: #496791;
        border: 1px solid #5d82b6ff;        
        color: #ffffff;
        opacity: 1;
    }

    ${props => props.disabled && `
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #203348; color: #deebff; }
    `}

    ${props => (props.$active || (props.$variant === 'activate' && props.$isWeapon)) && props.$variant === 'activate' && `
        background: #1b5e20; 
        color: white;
        border: 1px solid #4caf50;
        opacity: 1;

        &:hover {
            background: #2e7d32; 
            border: 1px solid #66bb6a;      
            color: #ffffff;
            opacity: 1;
        }
    `}

    ${props => props.$active && props.$variant === 'deactivate' && `
        background: #7f1d1d; 
        color: white;
        border: 1px solid #ef4444;
        opacity: 1;

        &:hover {
            background: #991b1b; 
            border: 1px solid #f87171;      
            color: #ffffff;
            opacity: 1;
        }
    `}
`;


class SystemActivation extends Component {

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

    canActivate() {
        return this.props.system.canActivate && typeof this.props.system.canActivate === 'function' && this.props.system.canActivate();
    }

    canDeactivate() {
        return this.props.system.canDeactivate && typeof this.props.system.canDeactivate === 'function' && this.props.system.canDeactivate();
    }

    render() {
        const { ship, system } = this.props;

        // Robust detection of active state
        const isActive = system.active || (system.weapon && weaponManager.hasFiringOrder(ship, system));

        const activateLabel = system.weapon ? "Fire" : "Activate";
        const deactivateLabel = system.weapon ? "Don't Fire" : "Deactivate";

        return (
            <Container>
                <Header>{system.displayName}</Header>
                <Row>
                    <Controls>
                        <ActionButton onClick={() => this.handleActivate()} disabled={!this.canActivate()} $active={isActive} $variant="activate" $isWeapon={system.weapon}>{activateLabel}</ActionButton>
                        {!system.weapon && (
                            <ActionButton onClick={() => this.handleDeactivate()} disabled={!this.canDeactivate()} $active={!isActive} $variant="deactivate">{deactivateLabel}</ActionButton>
                        )}
                    </Controls>
                </Row>
            </Container>
        );
    }
}

export default SystemActivation;
