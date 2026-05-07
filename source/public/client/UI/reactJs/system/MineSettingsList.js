import React, { Component } from 'react';
import styled from 'styled-components';

const Container = styled.div`
    display: flex;
    flex-direction: column;
    margin-top: 0px;
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

const HeaderRow = styled.div`
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 3px;
    background-color: #180606;
    border: 1px solid #b43131;
    color: #f2f2f2;
    font-size: 12px;
    font-weight: bold;
    margin-bottom: 2px;
`;

const HeaderArrow = styled.div`
    width: 20px;
    height: 18px;
    background: #683333;
    border: 1px solid #641b1b;
    color: #f2f2f2;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    user-select: none;

    &:hover {
        background: #854242;
    }

    ${props => props.disabled && `
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #683333; }
    `}
`;

const HeaderLabel = styled.div`
    flex: 1;
    text-align: center;
    padding: 0 6px;
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

        const isMulti = system.hasMultiTarget && system.hasMultiTarget();
        const sourceWeaponId = isMulti ? system.getCurrWeaponId() : null;
        const sourceWeapons = isMulti ? system.getMineWeapons() : [];
        const sourceWeapon = isMulti ? sourceWeapons.find(w => String(w.id) === String(sourceWeaponId)) : null;
        if (isMulti && !sourceWeapon) return;

        system.setCurrShipType(className);
        const systemMaxRange = system.range || system.rangeSetting;
        const sourceAlloc = isMulti ? system.allocatedRanges[sourceWeaponId] : system.allocatedRanges;
        if (!sourceAlloc) return;
        const targetValue = (sourceAlloc[className] === null || sourceAlloc[className] === undefined) ? systemMaxRange : sourceAlloc[className];

        var allOwnMines = [];
        for (var i in gamedata.ships) {
            var otherUnit = gamedata.ships[i];
            if (otherUnit.userid != ship.userid) continue;
            if (shipManager.isDestroyed(otherUnit)) continue;
            if (otherUnit.shipClass != ship.shipClass) continue;

            for (var iSys = 0; iSys < otherUnit.systems.length; iSys++) {
                var ctrl = otherUnit.systems[iSys];
                if (ctrl.name !== system.name) continue;

                //Only propagate between mines of matching enhancement state — never cross the boundary.
                var ctrlMulti = !!(ctrl.hasMultiTarget && ctrl.hasMultiTarget());
                if (ctrlMulti !== isMulti) continue;

                allOwnMines.push({ unit: otherUnit, ctrl: ctrl });
            }
        }

        for (var c = 0; c < allOwnMines.length; c++) {
            var entry = allOwnMines[c];
            var ctrl = entry.ctrl;

            if (isMulti) {
                //Match weapon by (displayName, indexInGroup); set that weapon as current then adjust.
                ctrl.ensureMultiAllocatedShape && ctrl.ensureMultiAllocatedShape();
                var ctrlWeapons = ctrl.getMineWeapons();
                var match = ctrlWeapons.find(w => w.displayName === sourceWeapon.displayName && w.indexInGroup === sourceWeapon.indexInGroup);
                if (!match) continue;
                ctrl.setCurrWeaponId(match.id);
            }

            ctrl.setCurrShipType(className);

            let safety = 0;
            const getCtrlAlloc = () => isMulti ? ctrl.allocatedRanges[ctrl.getCurrWeaponId()] : ctrl.allocatedRanges;
            const getCtrlMax = () => ctrl.range || ctrl.rangeSetting;
            const readCurr = () => {
                const alloc = getCtrlAlloc();
                if (!alloc) return getCtrlMax();
                const v = alloc[className];
                return (v === null || v === undefined) ? getCtrlMax() : v;
            };

            while (readCurr() < targetValue && ctrl.canIncrease() && safety < 100) {
                ctrl.doIncrease();
                safety++;
            }
            while (readCurr() > targetValue && ctrl.canDecrease() && safety < 100) {
                ctrl.doDecrease();
                safety++;
            }
            if (safety >= 100) console.warn("Mine Settings Propagation safety break for", ctrl);
        }

        webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
    }

    cycleWeapon(direction) {
        const { system } = this.props;
        if (!system.hasMultiTarget || !system.hasMultiTarget()) return;
        const weapons = system.getMineWeapons();
        if (weapons.length === 0) return;
        const currId = system.getCurrWeaponId();
        let idx = weapons.findIndex(w => String(w.id) === String(currId));
        if (idx < 0) idx = 0;
        const nextIdx = (idx + direction + weapons.length) % weapons.length;
        system.setCurrWeaponId(weapons[nextIdx].id);
        this.forceUpdate();
    }

    render() {
        const { system } = this.props;

        if (!system) return null;

        system.range = system.range || system.rangeSetting;

        if (!system.range) return null;

        const isMulti = system.hasMultiTarget && system.hasMultiTarget();
        let weapons = [];
        let currWeaponId = null;
        let currWeapon = null;
        let activeAlloc = system.allocatedRanges || {};

        if (isMulti) {
            system.ensureMultiAllocatedShape && system.ensureMultiAllocatedShape();
            weapons = system.getMineWeapons();
            currWeaponId = system.getCurrWeaponId();
            currWeapon = weapons.find(w => String(w.id) === String(currWeaponId)) || weapons[0] || null;
            activeAlloc = (currWeaponId != null && system.allocatedRanges[currWeaponId]) ? system.allocatedRanges[currWeaponId] : {};
        } else {
            //Defensive: if a previous render with the enhancement on left allocatedRanges nested,
            //collapse it back to flat so we render Cap/Med/Ftr rows, not weapon-id rows.
            system.ensureFlatAllocatedShape && system.ensureFlatAllocatedShape();
            activeAlloc = system.allocatedRanges || {};
        }

        const shipTypes = Object.keys(activeAlloc);
        const allowedTargets = system.validTargets || shipTypes;

        const getVisibleValue = (type) => {
            if (!allowedTargets.includes(type)) return 'N/A';
            const val = activeAlloc[type];
            return (val === null || val === undefined) ? system.range : val;
        };

        const canIncrease = (type) => {
            if (!allowedTargets.includes(type)) return false;
            const allocated = getVisibleValue(type);
            return allocated < system.range;
        };

        const canDecrease = (type) => {
            if (!allowedTargets.includes(type)) return false;
            const allocated = getVisibleValue(type);
            return allocated > 0;
        };

        const canPropagate = (type) => {
            if (!allowedTargets.includes(type)) return false;
            return true;
        };

        const headerNode = isMulti && weapons.length > 0
            ? (
                <HeaderRow>
                    <HeaderArrow
                        onClick={() => this.cycleWeapon(-1)}
                        disabled={weapons.length < 2}
                        title="Previous weapon"
                    >&lt;</HeaderArrow>
                    <HeaderLabel>{currWeapon ? currWeapon.label : ''}</HeaderLabel>
                    <HeaderArrow
                        onClick={() => this.cycleWeapon(1)}
                        disabled={weapons.length < 2}
                        title="Next weapon"
                    >&gt;</HeaderArrow>
                </HeaderRow>
            )
            : <Header>Set Mine Range</Header>;

        return (
            <Container>
                {headerNode}
                <ListContainer ref={this.listRef}>
                    {shipTypes.map(type => (
                        <Row key={type}>
                            <Icon src={`./img/systemicons/BFCPclasses/${type}.png`} alt={type} />
                            <Name>{type}</Name>
                            <Controls
                                onWheel={(e) => {
                                    if (e.deltaY < 0 && canIncrease(type)) this.handleIncrease(type);
                                    else if (e.deltaY > 0 && canDecrease(type)) this.handleDecrease(type);
                                }}
                            >
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
                                    title={isMulti ? "Propagate this weapon's settings to same-class mines with Multiple Targets" : "Propagate to all mines of same type"}
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
