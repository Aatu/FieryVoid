import React, { Component } from 'react';
import styled from 'styled-components';

const Container = styled.div`
    display: flex;
    flex-direction: column;
    margin-top: 5px;
    width: 100%;
    min-width: 200px;
    opacity: 0.95;
    background-color: rgba(0, 0, 0, 0.9);
    border: 1px solid #808080;
`;

const Header = styled.div`
    padding: 3px;
    background-color: #4d4d4d;
    border: 1px solid #4d4d4d;
    border-bottom: 1px solid #808080;    
    color: #ffffff;
    text-align: center;
    font-size: 12px;
    margin-bottom: 2px;
    opacity: 1 !important;    
    font-weight: bold;
`;

const ListContainer = styled.div`
    max-height: 200px;
    overflow-y: auto;
    display: block;
    padding: 0;
`;

const Row = styled.div`
    display: flex;
    align-items: center;
    padding: 3px 5px;
    border-bottom: 1px solid #808080;
    font-size: 11px;
    color: #e6e6e6;

    &:hover {
        background-color: rgba(43, 62, 81, 0.6);
    }
`;

const Icon = styled.img`
    width: 20px;
    height: 20px;
    margin-right: 8px;
`;

const Name = styled.div`
    flex: 1;  
    margin-right: 5px;      
    font-weight: normal; 
`;

const Controls = styled.div`
    display: flex;
    align-items: center;
    gap: 2px;
`;

const Value = styled.div`
    width: 20px;
    text-align: center;
`;

const ActionButton = styled.div`
    width: 16px;
    height: 16px;
    background: #666666;
    border: 1px solid #808080;
    color: #f2f2f2;
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 14px;
    padding: 0;
    opacity: 0.9;

    &:hover {
        background: #3a536e;
        color: #ffffff;
        opacity: 1;
    }

    ${props => props.disabled && `
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #2b3e51; color: #f2f2f2; }
    `}
`;

const Divider = styled.span`
    display: inline-block;
    width: 1px;
    height: 10px;
    background-color: #f2f2f2;
    margin: 0 4px;
    font-weight: bold;     
    vertical-align: middle;
    opacity: 0.7;
`;

class AdaptiveArmorList extends Component {
    constructor(props) {
        super(props);
        this.listRef = React.createRef();
    }

    handleIncrease(className) {
        const { system } = this.props;
        system.setCurrDmgType(className);

        if (system.canIncrease()) {
            system.doIncrease();
            this.forceUpdate();;
        }
    }

    handleDecrease(className) {
        const { system } = this.props;
        system.setCurrDmgType(className);

        if (system.canDecrease()) {
            system.doDecrease();
            this.forceUpdate();
        }
    }

    handlePropagate(className) {
        const { ship, system } = this.props;

        // Ensure globals are available
        const gamedata = window.gamedata;
        const shipManager = window.shipManager;
        const webglScene = window.webglScene;

        console.log("Propagating AA setting for:", className);

        system.setCurrDmgType(className);
        const allocated = system.allocatedAA[className];

        var allOwnAA = [];
        for (var i in gamedata.ships) {
            var otherUnit = gamedata.ships[i];
            if (otherUnit.userid != ship.userid) continue;
            if (shipManager.isDestroyed(otherUnit)) continue;

            if (otherUnit.flight) {
                for (var iFtr = 0; iFtr < otherUnit.systems.length; iFtr++) {
                    var ftr = otherUnit.systems[iFtr];
                    if (ftr) for (var iSys = 0; iSys < ftr.systems.length; iSys++) {
                        var ctrl = ftr.systems[iSys];
                        if (ctrl) if (ctrl.displayName == "Adaptive Armor Controller") {
                            allOwnAA.push(ctrl);
                            break;
                        }
                    }
                }
            } else {
                for (var iSys = 0; iSys < otherUnit.systems.length; iSys++) {
                    var ctrl = otherUnit.systems[iSys];
                    if (ctrl.displayName == "Adaptive Armor Controller") {
                        allOwnAA.push(ctrl);
                        break;
                    }
                }
            }
        }

        console.log("Found AA controllers:", allOwnAA.length);

        for (var c = 0; c < allOwnAA.length; c++) {
            var ctrl = allOwnAA[c];
            ctrl.setCurrDmgType(className);

            let safety = 0;
            while (
                ctrl.getCurrAllocated() < allocated
                && ctrl.canIncrease()
                && safety < 100
            ) {
                ctrl.doIncrease();
                safety++;
            }
            if (safety >= 100) console.warn("AA Propagation safety break for", ctrl);
        }

        webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
    }

    render() {
        const { ship, system } = this.props;

        if (!system || !system.availableAA) return null;

        const armorTypes = Object.keys(system.availableAA).sort();
        const allocatedAA = system.allocatedAA;

        const canIncrease = (type) => {
            const totalUsed = system.AAtotal_used;
            const totalMax = system.AAtotal;
            const allocated = allocatedAA[type] || 0;
            const perTypeMax = system.AApertype;
            const availableForType = system.availableAA[type];
            const preAllocated = system.AApreallocated;
            const preAllocatedUsed = system.AApreallocated_used;

            if (totalUsed >= totalMax) return false;
            if (allocated >= perTypeMax) return false;

            // Availability check
            if ((preAllocated <= preAllocatedUsed) && (availableForType <= allocated)) return false;

            return true;
        };

        const canDecrease = (type) => {
            return (system.currchangedAA[type] > 0);
        };

        const canPropagate = (type) => {
            return (allocatedAA[type] > 0);
        };

        return (
            <Container>
                <Header>
                    Manage Adaptive Armor
                </Header>
                <ListContainer ref={this.listRef}>
                    {armorTypes.map(type => (
                        <Row key={type}>
                            <Icon src={`./img/systemicons/AAclasses/${type}.png`} alt={type} />
                            <Name>{type}</Name>
                            <Controls>
                                <ActionButton
                                    onClick={() => this.handleDecrease(type)}
                                    disabled={!canDecrease(type)}
                                >
                                    -
                                </ActionButton>
                                <Value>{allocatedAA[type]}</Value>
                                <ActionButton
                                    onClick={() => this.handleIncrease(type)}
                                    disabled={!canIncrease(type)}
                                >
                                    +
                                </ActionButton>
                                <ActionButton
                                    title="Propagate to all units"
                                    onClick={() => this.handlePropagate(type)}
                                    disabled={!canPropagate(type)}
                                    style={{ marginLeft: '5px' }}
                                >
                                    <img src="./img/systemicons/AAclasses/iconPropagate.png" alt="Propagate" style={{ width: '12px', height: '12px' }} />
                                </ActionButton>
                            </Controls>
                        </Row>
                    ))}
                    {armorTypes.length === 0 && <Row>No armor types available</Row>}
                </ListContainer>
                <div style={{ padding: '5px', textAlign: 'center', fontSize: '10px', color: '#f2f2f2' }}>
                    Total: {system.AAtotal_used} / {system.AAtotal} <Divider /> Max Per Type: {system.AApertype}
                </div>
            </Container>
        );
    }
}

export default AdaptiveArmorList;
