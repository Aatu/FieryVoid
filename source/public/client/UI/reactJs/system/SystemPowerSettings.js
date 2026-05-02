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
    border: 1px solid #587e8d;
`;

const Header = styled.div`
    padding: 3px;
    background-color: #215a7a;
    border: 1px solid #587e8d;
    border-bottom: 1px solid #587e8d;    
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
    font-size: 11px;
    color: #deebff;
    justify-content: space-between;
    min-width: 120px;    

    &:last-child {
        border-bottom: none;
    }

    &:hover {
        background-color: rgba(73, 103, 145, 0.2);
    }
`;

const Label = styled.div`
    flex: 1;
    padding-left: 8px;
    padding-right: 8px;
`;

const Controls = styled.div`
    display: flex;
    align-items: center;     
    gap: 5px;
    padding: 2px;
`;

const Value = styled.div`
    min-width: 20px;
    text-align: center;
    font-size: 11px;
    font-weight: bold;
`;

const ActionButton = styled.div`
    width: ${props => props.$narrow ? '18px' : '30px'};
    height: 18px;
    background: #203348;
    border: 1px solid #587e8d;
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
        border: 1px solid #587e8d;        
        color: #ffffff;
        opacity: 1;
    }

    ${props => props.disabled && `
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #203348; color: #deebff; }
    `}

    ${props => props.$active && props.$variant === 'activate' && `
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

    ${props => props.$active && props.$variant === 'warning' && `
        background: #806c00; 
        color: white;
        border: 1px solid #e6c300;
        opacity: 1;

        &:hover {
            background: #998100; 
            border: 1px solid #ffda00;      
            color: #ffffff;
            opacity: 1;
        }
    `}

    ${props => props.$active && props.$variant === 'risk' && `
        background: #a65d00; 
        color: white;
        border: 1px solid #ff9800;
        opacity: 1;

        &:hover {
            background: #cc7a00; 
            border: 1px solid #ffb74d;      
            color: #ffffff;
            opacity: 1;
        }
    `}

    ${props => props.$active && props.$variant === 'info' && `
        background: #215a7a;
        color: white;
        border: 1px solid #587e8d;
        opacity: 1;

        &:hover {
            background: #36577a;
            border: 1px solid #6db5ed;
            color: #ffffff;
            opacity: 1;
        }
    `}
`;


class SystemPowerSettings extends Component {

    handleOnline() {
        if (this.canOnline()) {
            shipManager.power.onOnlineClicked(this.props.ship, this.props.system);
            this.handleUpdate();
        }
    }

    handleOnlineAll(e) {
        e.preventDefault();
        if (this.canOnline()) {
            shipManager.power.onlineAll(this.props.ship, this.props.system);
            this.handleUpdate();
        }
    }

    handleOffline() {
        if (this.canOffline()) {
            const { ship, system } = this.props;

            // Clear all boost levels before offlining
            while (shipManager.power.getBoost(system) > 0) {
                shipManager.power.clickMinus(ship, system);
            }

            shipManager.power.onOfflineClicked(ship, system);
            this.handleUpdate();
        }
    }

    handleOfflineAll(e) {
        e.preventDefault();
        if (this.canOffline()) {
            const { ship, system } = this.props;

            // Clear boost for current system first
            while (shipManager.power.getBoost(system) > 0) {
                shipManager.power.clickMinus(ship, system);
            }

            shipManager.power.offlineAll(ship, system);
            this.handleUpdate();
        }
    }

    handleBoost() {
        if (this.canBoost()) {
            shipManager.power.clickPlus(this.props.ship, this.props.system);
            this.handleUpdate();
        }
    }

    handleDeBoost() {
        if (this.canDeBoost()) {
            shipManager.power.clickMinus(this.props.ship, this.props.system);
            this.handleUpdate();
        }
    }

    handleOverload() {
        if (this.canOverload()) {
            shipManager.power.onOverloadClicked(this.props.ship, this.props.system);
            this.handleUpdate();
        }
    }

    handleStopOverload() {
        if (this.canStopOverload()) {
            shipManager.power.onStopOverloadClicked(this.props.ship, this.props.system);
            this.handleUpdate();
        }
    }

    handleUpdate() {
        this.forceUpdate();
        webglScene.customEvent('SystemDataChanged', { ship: this.props.ship, system: this.props.system });
    }

    canOffline() {
        const { ship, system } = this.props;
        return gamedata.gamephase === 1 && (system.canOffLine || system.powerReq > 0) && !shipManager.power.isOffline(ship, system) && !weaponManager.hasFiringOrder(ship, system);
    }

    canOnline() {
        const { ship, system } = this.props;
        return gamedata.gamephase === 1 && shipManager.power.isOffline(ship, system);
    }

    canBoost() {
        const { ship, system } = this.props;
        return system.boostable && gamedata.gamephase === 1 && shipManager.power.canBoost(ship, system) && (!system.isScanner() || system.id == shipManager.power.getHighestSensorsId(ship)) && system.name !== 'ThirdspaceShieldGenerator' && system.name !== 'powerCapacitor' && system.name !== 'PowerCapacitor';
    }

    canDeBoost() {
        const { ship, system } = this.props;
        return gamedata.gamephase === 1 && Boolean(shipManager.power.getBoost(system)) && system.name !== 'ThirdspaceShieldGenerator' && system.name !== 'powerCapacitor' && system.name !== 'PowerCapacitor';
    }

    canOverload() {
        const { ship, system } = this.props;
        return gamedata.gamephase === 1 && !shipManager.power.isOffline(ship, system) && system.weapon && system.overloadable && !shipManager.power.isOverloading(ship, system);
    }

    canStopOverload() {
        const { ship, system } = this.props;
        return gamedata.gamephase === 1 && system.weapon && system.overloadable && shipManager.power.isOverloading(ship, system) && (system.overloadshots >= system.extraoverloadshots || system.overloadshots == 0);
    }

    render() {
        const { ship, system } = this.props;

        const showOffline = this.canOffline() || this.canOnline();
        const showBoost = system.boostable && (this.canBoost() || this.canDeBoost());
        const showOverload = system.overloadable && (this.canOverload() || this.canStopOverload());

        if (!showOffline && !showBoost && !showOverload) return null;

        const isOffline = shipManager.power.isOffline(ship, system);
        const boostLevel = shipManager.power.getBoost(system);
        const isOverloading = shipManager.power.isOverloading(ship, system);

        const isReactor = system.name === 'reactor';
        const isJumpEngine = system.name === 'jumpEngine';

        let boostLabel = "Boost Level";
        if (isReactor) boostLabel = "Self-Destruct";
        if (isJumpEngine) boostLabel = "Jump to Hyperspace";

        return (
            <Container>
                <Header>Power Settings</Header>

                {showOffline && (
                    <Row>
                        <Label>Power</Label>
                        <Controls>
                            <ActionButton onClick={() => this.handleOnline()} onContextMenu={(e) => this.handleOnlineAll(e)} disabled={!this.canOnline()} $active={!isOffline} $variant="activate">On</ActionButton>
                            <ActionButton onClick={() => this.handleOffline()} onContextMenu={(e) => this.handleOfflineAll(e)} disabled={!this.canOffline()} $active={isOffline} $variant="deactivate">Off</ActionButton>
                        </Controls>
                    </Row>
                )}

                {showBoost && (
                    <Row>
                        <Label>{boostLabel}</Label>
                        {(isReactor || isJumpEngine) ? (
                            <Controls>
                                <ActionButton
                                    onClick={() => this.handleBoost()}
                                    disabled={boostLevel > 0 || !this.canBoost()}
                                    $active={boostLevel > 0}
                                    $variant={isReactor ? "deactivate" : "risk"}
                                >
                                    Yes
                                </ActionButton>
                                <ActionButton
                                    onClick={() => this.handleDeBoost()}
                                    disabled={boostLevel === 0 || !this.canDeBoost()}
                                    $active={boostLevel === 0}
                                    $variant="activate"
                                >
                                    No
                                </ActionButton>
                            </Controls>
                        ) : (
                            <Controls>
                                <ActionButton onClick={() => this.handleDeBoost()} $narrow={true}>-</ActionButton>
                                <Value>{boostLevel}</Value>
                                <ActionButton onClick={() => this.handleBoost()} $narrow={true}>+</ActionButton>
                            </Controls>
                        )
                        }
                    </Row>
                )}

                {showOverload && (
                    <Row>
                        <Label>Overcharge</Label>
                        <Controls>
                            <ActionButton onClick={() => this.handleOverload()} disabled={!this.canOverload()} $active={isOverloading} $variant="warning">Yes</ActionButton>
                            <ActionButton onClick={() => this.handleStopOverload()} disabled={!this.canStopOverload()} $active={!isOverloading} $variant="deactivate">No</ActionButton>
                        </Controls>
                    </Row>
                )}
            </Container>
        );
    }
}

export default SystemPowerSettings;
