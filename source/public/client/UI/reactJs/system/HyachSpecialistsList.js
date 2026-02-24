
import React, { Component } from 'react';
import styled from 'styled-components';
import { Clickable } from "../styled";

const Container = styled.div`
    display: flex;
    flex-direction: column;
    margin-top: 5px;
    width: 100%;
    min-width: 200px;
    opacity: 0.95;
    background-color: rgba(32, 0, 32, 0.9);
    border: 1px solid #5d3564;
`;

const Header = styled.div`
    padding: 3px;
    background-color: #5d3564;
    border: 1px solid #5d3564;
    border-bottom: 1px solid #5d3564;    
    color: #f2f2f2;
    text-align: center;
    font-size: 11px;
    margin-bottom: 2px;
    opacity: 1 !important;    
    font-weight: bold;
`;

const ListContainer = styled.div`
    background-color: rgba(0, 0, 0, 0.8);
    border: 1px solid #4b2b51; 
    max-height: 280px;
    overflow-y: auto;
    display: block;
`;

const ListItem = styled.div`
    display: flex;
    align-items: center;
    padding: 3px 5px;
    border-bottom: 1px solid #5d3564;
    font-size: 11px;
    color: #f2f2f2;

    &:hover {
        background-color: rgba(75, 43, 81, 0.6);
    }
    
    &:last-child {
        border-bottom: none;
    }
`;

const Icon = styled.img`
    width: 20px;
    height: 20px;
    margin-right: 5px;
`;

const ItemName = styled.span`
    flex-grow: 1;
    font-weight: bold;
`;

const ActionButtons = styled.div`
    display: flex;
    gap: 2px;
    align-items: center;
`;

const ActionIcon = styled.div`
    width: 16px; 
    height: 16px;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: ${props => props.disabled ? '#555' : '#7c4686'};
    color: white;
    font-size: 14px;
    font-weight: bold;
    cursor: ${props => props.disabled ? 'default' : 'pointer'};
    border: 1px solid #aaa;
    opacity: ${props => props.disabled ? 0.5 : 1};
    
    &:hover {
         background-color: ${props => props.disabled ? '#555' : '#a06daa'};
    }
    ${props => !props.disabled && Clickable}
`;

// Helper to check for Deployment Phase
const isDeployment = (ship) => {
    return (window.gamedata.turn === window.shipManager.getTurnDeployed(ship) && window.gamedata.gamephase === -1);
};

class HyachSpecialistsList extends Component {
    constructor(props) {
        super(props);
    }

    handleSelect(className) {
        const { system } = this.props;
        // setCurrClass is needed because doSelect uses specCurrClass internally
        system.specCurrClass = className;
        if (system.canSelect()) {
            system.doSelect();
            this.forceUpdate();
        }
    }

    handleUnselect(className) {
        const { system } = this.props;
        system.specCurrClass = className;
        if (system.canUnselect()) {
            system.doUnselect();
            this.forceUpdate();
        }
    }

    handleUse(className) {
        const { system } = this.props;
        system.specCurrClass = className;
        if (system.canUse()) {
            system.doUse();
            this.forceUpdate();
        }
    }

    handleCancel(className) {
        const { system } = this.props;
        system.specCurrClass = className;
        if (system.canDecrease()) {
            system.doDecrease();
            this.forceUpdate();
        }
    }

    render() {
        const { ship, system } = this.props;

        if (!system) return null;

        const isDeploy = isDeployment(ship);

        // Items to display
        let displayClasses = [];

        if (isDeploy) {
            // In Deployment: Show ALL available classes (keys of allSpec)
            // But verify system.allSpec has content. If it's a new system, it might be initialized differently.
            // baseSystems.js -> HyachSpecialists uses this.allSpec for deployment phase
            if (system.allSpec) {
                displayClasses = Object.keys(system.allSpec);
            }
        } else {
            // In Game: Show ONLY selected classes (keys of availableSpec where val > 0)
            if (system.availableSpec) {
                displayClasses = Object.keys(system.availableSpec).filter(key => system.availableSpec[key] > 0);
            }
        }

        // Sort alphabetical
        displayClasses.sort();

        // Footer Stats
        let footerText = "";
        if (isDeploy) {
            const totalSelected = Object.values(system.availableSpec || {}).reduce((a, b) => a + b, 0);
            footerText = `Specialists Selected: ${totalSelected} / ${system.specTotal}`;
        } else {
            const totalUsed = system.specTotal_used || 0;
            footerText = `Specialists Used: ${totalUsed} / ${system.specTotal}`;
        }

        return (
            <Container>
                <Header>
                    Hyach Specialists
                </Header>
                <ListContainer>
                    {displayClasses.map(className => {
                        // Determine actions based on phase
                        const canSelect = isDeploy && (() => { system.specCurrClass = className; return system.canSelect(); })();
                        const canUnselect = isDeploy && (() => { system.specCurrClass = className; return system.canUnselect(); })();

                        const canUse = !isDeploy && (() => { system.specCurrClass = className; return system.canUse(); })();
                        const canCancel = !isDeploy && (() => { system.specCurrClass = className; return system.canDecrease(); })();

                        // Check status for UI feedback (e.g. is it selected? is it used?)
                        const isSelected = system.availableSpec && system.availableSpec[className] > 0;
                        const isUsed = system.currAllocatedSpec && system.currAllocatedSpec[className] === 'allocated';

                        // Icon Path
                        const iconSrc = `./img/systemicons/Specialistclasses/${className}.png?v=2`;

                        return (
                            <ListItem key={className}>
                                <Icon src={iconSrc} alt={className} />
                                <ItemName>{className}</ItemName>
                                <ActionButtons>
                                    {isDeploy ? (
                                        <>
                                            {/* Unselect / Select */}
                                            <ActionIcon onClick={() => this.handleUnselect(className)} disabled={!canUnselect}>
                                                <img src="./img/systemicons/Specialistclasses/iconMinus.png" style={{ width: '12px', height: '12px' }} alt="Cancel" />
                                            </ActionIcon>
                                            <ActionIcon onClick={() => this.handleSelect(className)} disabled={!canSelect}>
                                                <img src="./img/systemicons/Specialistclasses/iconPlus.png" style={{ width: '12px', height: '12px' }} alt="Use" />
                                            </ActionIcon>
                                        </>
                                    ) : (
                                        <>
                                            {/* Cancel / Use */}
                                            <ActionIcon onClick={() => this.handleCancel(className)} disabled={!canCancel}>
                                                <img src="./img/systemicons/Specialistclasses/iconMinus.png" style={{ width: '12px', height: '12px' }} alt="Cancel" />
                                            </ActionIcon>
                                            <ActionIcon onClick={() => this.handleUse(className)} disabled={!canUse}>
                                                <img src="./img/systemicons/Specialistclasses/iconPlus.png" style={{ width: '12px', height: '12px' }} alt="Use" />
                                            </ActionIcon>
                                        </>
                                    )}
                                </ActionButtons>
                            </ListItem>
                        );
                    })}
                    {displayClasses.length === 0 && <ListItem style={{ justifyContent: 'center', fontStyle: 'italic' }}>No Specialists Available</ListItem>}
                </ListContainer>
                <div style={{ padding: "5px", textAlign: "center", fontSize: "10px", color: "#f2f2f2" }}>
                    {footerText}
                </div>
            </Container>
        );
    }
}

export default HyachSpecialistsList;
