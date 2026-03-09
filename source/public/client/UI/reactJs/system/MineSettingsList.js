import React, { Component } from 'react';
import styled from 'styled-components';

const Container = styled.div`
    display: flex;
    flex-direction: column;
    margin-top: 5px;
    width: 100%;
    min-width: 200px;
    opacity: 0.95;
    background-color: rgba(32, 0, 32, 0.9);
    border: 1px solid #b43131;
`;

const Header = styled.div`
    padding: 3px;
    background-color: #180606;
    border: 1px solid #b43131;
    border-bottom: 1px solid #b43131;    
    color: #f2f2f2;
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
    border-bottom: 1px solid #b43131;
    font-size: 11px;
    color: #f2f2f2;

    &:hover {
        background-color: rgba(32, 0, 32, 0.6);
    }
`;

const Icon = styled.img`
    width: 20px;
    height: 20px;
    margin-right: 8px;
`;

const Name = styled.div`
    flex: 1;
    font-weight: normal;
    margin-right: 25px;     
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
    background: #683333;
    border: 1px solid #641b1b;
    color: #f2f2f2;
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 14px;
    padding: 0;
    opacity: 0.9;


    &:hover {
        background: #854242;
        color: #ffffff;
        opacity: 1;
    }

    ${props => props.disabled && `
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #4b2b51; color: #d8b9e6; }
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

class MineSettingsList extends Component {
    constructor(props) {
        super(props);
        this.listRef = React.createRef();
    }

    handleIncrease(className) {
        const { system } = this.props;
        system.setCurrShipType(className);

        if (system.canIncrease()) {
            system.doIncrease();
            this.forceUpdate();
        }
    }

    handleDecrease(className) {
        const { system } = this.props;
        system.setCurrShipType(className);

        if (system.canDecrease()) {
            system.doDecrease();
            this.forceUpdate();
        }
    }

    handlePropagate(className) {
        const { ship, system } = this.props;

        const gamedata = window.gamedata;
        const shipManager = window.shipManager;
        const webglScene = window.webglScene;

        console.log("Propagating Mine settings for:", className);

        system.setCurrShipType(className);
        const allocated = (system.allocatedRanges[className] === null) ? system.range : system.allocatedRanges[className];

        var allOwnMines = [];
        for (var i in gamedata.ships) {
            var otherUnit = gamedata.ships[i];
            if (otherUnit.userid != ship.userid) continue;
            if (shipManager.isDestroyed(otherUnit)) continue;

            for (var iSys = 0; iSys < otherUnit.systems.length; iSys++) {
                var ctrl = otherUnit.systems[iSys];
                if (otherUnit.shipClass == ship.shipClass && ctrl.name === system.name) {
                    allOwnMines.push(ctrl);
                    break;
                }
            }
        }

        console.log("Found Mine Weapons of same type:", allOwnMines.length);

        for (var c = 0; c < allOwnMines.length; c++) {
            var ctrl = allOwnMines[c];
            ctrl.setCurrShipType(className);

            let safety = 0;
            // Get target value from our reference system
            const targetValue = (system.allocatedRanges[className] === null) ? system.range : system.allocatedRanges[className];

            while (
                ((ctrl.allocatedRanges[className] === null ? ctrl.range : ctrl.allocatedRanges[className]) < targetValue)
                && ctrl.canIncrease()
                && safety < 100
            ) {
                ctrl.doIncrease();
                safety++;
            }

            while (
                ((ctrl.allocatedRanges[className] === null ? ctrl.range : ctrl.allocatedRanges[className]) > targetValue)
                && ctrl.canDecrease()
                && safety < 100
            ) {
                ctrl.doDecrease();
                safety++;
            }
            if (safety >= 100) console.warn("Mine Settings Propagation safety break for", ctrl);
        }

        webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
    }

    render() {
        const { system } = this.props;

        if (!system || !system.range) return null;

        const allocatedRangesMap = system.allocatedRanges || {};
        const shipTypes = Object.keys(allocatedRangesMap);

        const getVisibleValue = (type) => {
            const val = allocatedRangesMap[type];
            return val === null ? system.range : val;
        };

        const canIncrease = (type) => {
            const allocated = getVisibleValue(type);
            return allocated < system.range;
        };

        const canDecrease = (type) => {
            const allocated = getVisibleValue(type);
            return allocated > 0;
        };

        const canPropagate = (type) => {
            return true; // Can always propagate current setting
        };

        return (
            <Container>
                <Header>
                    Set Mine Range
                </Header>
                <ListContainer ref={this.listRef}>
                    {shipTypes.map(type => (
                        <Row key={type}>
                            <Icon src={`./img/systemicons/BFCPclasses/${type}.png`} alt={type} />
                            <Name>{type}</Name>
                            <Controls>
                                <ActionButton
                                    onClick={() => this.handleDecrease(type)}
                                    disabled={!canDecrease(type)}
                                >
                                    -
                                </ActionButton>
                                <Value>{getVisibleValue(type)}</Value>
                                <ActionButton
                                    onClick={() => this.handleIncrease(type)}
                                    disabled={!canIncrease(type)}
                                >
                                    +
                                </ActionButton>
                                <ActionButton
                                    title="Propagate to all mines of same type"
                                    onClick={() => this.handlePropagate(type)}
                                    disabled={!canPropagate(type)}
                                    style={{ marginLeft: '5px' }}
                                >
                                    <img src="./img/systemicons/BFCPclasses/minePropagate.png" alt="Propagate" style={{ width: '12px', height: '12px' }} />
                                </ActionButton>
                            </Controls>
                        </Row>
                    ))}
                    {shipTypes.length === 0 && <Row>No ship types available</Row>}
                </ListContainer>
                <div style={{ padding: '5px', textAlign: 'center', fontSize: '10px', color: '#f2f2f2' }}>
                    Max Range: {system.range}
                </div>
            </Container>
        );
    }
}

export default MineSettingsList;
