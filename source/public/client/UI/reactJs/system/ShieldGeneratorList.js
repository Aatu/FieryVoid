import React, { Component } from 'react';
import styled from 'styled-components';
import { Clickable } from "../styled";

const Container = styled.div`
    display: flex;
    flex-direction: column;
    margin-top: 5px;
    width: 100%;
    min-width: 250px;
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

const ListContainer = styled.div`
    max-height: 250px;
    overflow-y: auto;
    display: block;
    padding: 0;
`;

const Row = styled.div`
    display: flex;
    align-items: center;
    padding: 3px 8px;
    border-bottom: 1px solid #496791;
    font-size: 11px;
    color: #deebff;
    flex-wrap: wrap;

    &:hover {
        background-color: rgba(73, 103, 145, 0.4);
    }
`;

const Name = styled.div`
    flex: 1;
    min-width: 80px;
    font-weight: normal; 
`;

const Controls = styled.div`
    display: flex;
    align-items: center;     
    gap: 2px;
    margin-left: 10px;
`;

const InputField = styled.input`
    width: 30px;
    height: 18px;
    background: rgba(0,0,0,0.5);
    border: 1px solid #496791;
    color: gold;
    text-align: center;
    font-size: 12px; 
    
    // Hide spinner
    &::-webkit-inner-spin-button, 
    &::-webkit-outer-spin-button { 
        -webkit-appearance: none; 
        margin: 0; 
    }
    -moz-appearance: textfield;
`;

const ActionButton = styled.div`
    width: 22px;
    height: 16px;
    background: #203348;
    border: 1px solid #496791;
    color: #deebff;
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 9px;
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

    &.small {
        width: 18px;
    }
`;

const Divider = styled.span`
    display: inline-block;
    width: 1px;
    height: 10px;
    background-color: #496791;
    margin: 0 6px;
    vertical-align: middle;
    opacity: 0.7;
`;

class ShieldGeneratorList extends Component {
    constructor(props) {
        super(props);
        this.listRef = React.createRef();
        this.state = {
            shieldInputs: {}
        };
    }

    getShieldLabel(shield) {
        // Calculate mid-arc to determine facing
        let start = shield.startArc;
        let end = shield.endArc;

        // Handle 360 degree coverage or missing arcs
        if (start === undefined || end === undefined) return shield.displayName;

        let mid = (start + end) / 2;
        if (start > end) {
            mid = (start + end + 360) / 2;
        }
        mid = mid % 360;

        let prefix = '';

        // Map angle to 8-way direction (0 is Up/Front, Clockwise)
        // Segments are 45 degrees, centered on 0, 45, 90...
        // 0 (F): 337.5 - 22.5
        // 45 (FS): 22.5 - 67.5
        // 90 (S): 67.5 - 112.5
        // 135 (AS): 112.5 - 157.5
        // 180 (A): 157.5 - 202.5
        // 225 (AP): 202.5 - 247.5
        // 270 (P): 247.5 - 292.5
        // 315 (FP): 292.5 - 337.5

        if (mid >= 337.5 || mid < 22.5) prefix = 'F';
        else if (mid >= 22.5 && mid < 67.5) prefix = 'FS';
        else if (mid >= 67.5 && mid < 112.5) prefix = 'S';
        else if (mid >= 112.5 && mid < 157.5) prefix = 'AS';
        else if (mid >= 157.5 && mid < 202.5) prefix = 'A';
        else if (mid >= 202.5 && mid < 247.5) prefix = 'AP';
        else if (mid >= 247.5 && mid < 292.5) prefix = 'P';
        else if (mid >= 292.5 && mid < 337.5) prefix = 'FP';

        if (prefix) {
            return `${prefix} - ${shield.displayName}`;
        }
        return shield.displayName;
    }

    getShieldSortPriority(shield) {
        // Extract prefix from label or recalculate
        const label = this.getShieldLabel(shield).split(' - ')[0];

        // User requested order: Front, Port, Starboard, Aft
        const priorities = {
            'F': 1,
            'FP': 2, // Front-Port
            'P': 3,  // Port
            'AP': 4, // Aft-Port
            'FS': 5, // Front-Starboard
            'S': 6,  // Starboard
            'AS': 7, // Aft-Starboard
            'A': 8   // Aft
        };

        return priorities[label] || 99;
    }

    getGeneratorAndShields() {
        const { ship, system: selectedSystem } = this.props;

        let generator = null;
        let shields = [];
        let systemName = '';
        let generatorName = '';

        // Determine type based on selected system
        if (selectedSystem.name === 'ThirdspaceShield' || selectedSystem.name === 'ThirdspaceShieldGenerator') {
            systemName = 'ThirdspaceShield';
            generatorName = 'ThirdspaceShieldGenerator';
        } else if (selectedSystem.name === 'ThoughtShield' || selectedSystem.name === 'ThoughtShieldGenerator') {
            systemName = 'ThoughtShield';
            generatorName = 'ThoughtShieldGenerator';
        }

        if (!systemName) return { generator: null, shields: [] };

        // Iterate through all systems to find generator and shields
        if (ship.systems) {
            // Handle both array and object formats if necessary, though ship.systems is usually array/map-like
            const systemsList = Array.isArray(ship.systems) ? ship.systems : Object.values(ship.systems);

            systemsList.forEach(sys => {
                if (sys.name === generatorName) {
                    generator = sys;
                }
                if (sys.name === systemName) {
                    shields.push(sys);
                }
            });
        }

        // Sort shields
        shields.sort((a, b) => {
            const priorityA = this.getShieldSortPriority(a);
            const priorityB = this.getShieldSortPriority(b);

            if (priorityA !== priorityB) return priorityA - priorityB;
            return a.id - b.id;
        });

        return { generator, shields, systemName };
    }

    handleIncrease(shield, amount) {
        // Logic to increase by amount
        // The BaseSystem logic handles constraints, we just need to call it repeatedly or check capacity

        // Check if we can increase at all
        if (!shield.canIncrease()) return;

        // Optimization: Use the bulk methods if available and amount matches
        if (amount === 25 && shield.doIncrease25) {
            shield.doIncrease25();
        } else if (amount === 10 && shield.doIncrease10) {
            shield.doIncrease10();
        } else if (amount === 5 && shield.doIncrease5) {
            shield.doIncrease5();
        } else {
            // Fallback or smaller amounts
            // We need to be careful not to trigger infinite loops or lag if 25 is clicked but only 1 capacity available
            // The doIncrease method checks capacity internally
            let remaining = amount;
            for (let i = 0; i < remaining; i++) {
                if (shield.canIncrease()) {
                    shield.doIncrease();
                } else {
                    break;
                }
            }
        }

        this.forceUpdate();
        webglScene.customEvent('SystemDataChanged', { ship: this.props.ship, system: shield });

        // Update input field to match new value
        this.updateInputState(shield);
    }

    handleDecrease(shield, amount) {
        if (!shield.canDecrease()) return;

        if (amount === 25 && shield.doDecrease25) {
            shield.doDecrease25();
        } else if (amount === 10 && shield.doDecrease10) {
            shield.doDecrease10();
        } else if (amount === 5 && shield.doDecrease5) {
            shield.doDecrease5();
        } else {
            let remaining = amount;
            for (let i = 0; i < remaining; i++) {
                if (shield.canDecrease()) {
                    shield.doDecrease();
                } else {
                    break;
                }
            }
        }

        this.forceUpdate();
        webglScene.customEvent('SystemDataChanged', { ship: this.props.ship, system: shield });
        this.updateInputState(shield);
    }

    handleInputChange(shield, value) {
        // Allow empty string for typing
        if (value === '') {
            this.setState(prevState => ({
                shieldInputs: {
                    ...prevState.shieldInputs,
                    [shield.id]: ''
                }
            }));
            return;
        }

        const newValue = parseInt(value, 10);
        if (isNaN(newValue)) return;

        // Calculate difference
        const current = shield.currentHealth; // Or whatever property holds the value
        const diff = newValue - current;

        if (diff > 0) {
            this.handleIncrease(shield, diff);
        } else if (diff < 0) {
            this.handleDecrease(shield, Math.abs(diff));
        }

        // We don't set state here immediately because handleIncrease/Decrease calls forceUpdate
        // which will re-read the values from the system.
        // However, if the operation allocated less than requested (e.g. not enough pool),
        // the input should reflect the ACTUAL value, not the typed value.
    }

    updateInputState(shield) {
        this.setState(prevState => ({
            shieldInputs: {
                ...prevState.shieldInputs,
                [shield.id]: shield.currentHealth
            }
        }));
    }

    handleWheel(e, shield) {
        e.preventDefault(); // Prevent page scroll
        const delta = e.deltaY < 0 ? 1 : -1;

        if (delta > 0) {
            this.handleIncrease(shield, 1);
        } else {
            this.handleDecrease(shield, 1);
        }
    }

    handleMouseEnter(shield) {
        if (window.webglScene && window.webglScene.phaseDirector && window.webglScene.phaseDirector.shipIconContainer) {
            const shipIcon = window.webglScene.phaseDirector.shipIconContainer.getByShip(this.props.ship);
            if (shipIcon) {
                shipIcon.showWeaponArc(this.props.ship, shield);
            }
        }
    }

    handleMouseLeave(shield) {
        if (window.webglScene && window.webglScene.phaseDirector && window.webglScene.phaseDirector.shipIconContainer) {
            const shipIcon = window.webglScene.phaseDirector.shipIconContainer.getByShip(this.props.ship);
            if (shipIcon) {
                shipIcon.hideWeaponArcs();
            }
        }
    }

    handleBoost(generator) {
        if (!generator) return;
        shipManager.power.clickPlus(this.props.ship, generator);
        this.forceUpdate();
        webglScene.customEvent('SystemDataChanged', { ship: this.props.ship, system: generator });
    }

    handleDeBoost(generator) {
        if (!generator) return;
        shipManager.power.clickMinus(this.props.ship, generator);
        this.forceUpdate();
        webglScene.customEvent('SystemDataChanged', { ship: this.props.ship, system: generator });
    }

    componentDidMount() {
        const { generator, shields } = this.getGeneratorAndShields();
        const initialInputs = {};
        shields.forEach(s => initialInputs[s.id] = s.currentHealth);
        this.setState({ shieldInputs: initialInputs });

        // Add global listener for SystemDataChanged to update UI
        if (window.webglScene) {
            // This is a bit hacky in React if not using context/flux/redux, 
            // but the game seems to rely on custom events or parent re-renders.
            // If SystemInfoButtons re-renders, this component will re-render.
        }
    }

    // Update inputs when props (and thus system data) changes
    componentDidUpdate(prevProps) {
        // A naive check, better would be to see if values actually changed
        // But since we rely on forceUpdate from parent, we should sync input state

        const { generator, shields } = this.getGeneratorAndShields();
        const currentInputs = this.state.shieldInputs;
        const newInputs = {};
        let changed = false;

        shields.forEach(s => {
            // Only update if not currently being edited? 
            // Actually, for a "controlled" input logic where input reflects game state,
            // we should always overwrite unless focused?
            // But if user is typing "10", they type "1", value becomes 1, input shows 1.
            // Then they type "0", value becomes 10.
            // If we behave like a standard controlled input, it's fine.

            // To support typing "10" without jumping:
            // The handleInputChange logic already tries to apply the diff immediately.
            // If the user types "1", we apply +1 (from 0). State becomes 1.
            // If the user types "0" (making "10"), we apply +9. State becomes 10.
            // So we can just keep state in sync with system.currentHealth.
            if (currentInputs[s.id] !== s.currentHealth && document.activeElement !== document.getElementById(`shield-input-${s.id}`)) {
                newInputs[s.id] = s.currentHealth;
                changed = true;
            }
        });

        if (changed) {
            this.setState(prevState => ({
                shieldInputs: {
                    ...prevState.shieldInputs,
                    ...newInputs
                }
            }));
        }
    }

    render() {
        const { generator, shields, systemName } = this.getGeneratorAndShields();

        if (!generator) return <div>No Generator Found</div>;

        return (
            <Container>
                <Header>
                    {systemName === 'ThirdspaceShield' ? 'Thirdspace Shields' : 'Thought Shields'}
                </Header>
                <div style={{ padding: '5px', textAlign: 'center', fontSize: '11px', color: '#deebff', borderBottom: '1px solid #496791' }}>
                    Unallocated Energy: {generator.storedCapacity}
                </div>
                {systemName === 'ThirdspaceShield' && (
                    <div style={{ padding: '5px', textAlign: 'center', fontSize: '11px', color: '#deebff', borderBottom: '1px solid #496791', display: 'flex', justifyContent: 'center', alignItems: 'center', gap: '5px' }}>
                        Regeneration Rate: {shipManager.systems.getOutputNoBoost(this.props.ship, generator) + (shipManager.power.getBoost(generator) * shields.length)}
                        <ActionButton className="small" onClick={() => this.handleDeBoost(generator)} title="Reduce Boost">-</ActionButton>
                        <ActionButton className="small" onClick={() => this.handleBoost(generator)} title="Boost Generator">+</ActionButton>
                    </div>
                )}
                <ListContainer ref={this.listRef}>
                    {shields.map(shield => (
                        <Row key={shield.id} onMouseEnter={() => this.handleMouseEnter(shield)} onMouseLeave={() => this.handleMouseLeave(shield)}>
                            <Name>{this.getShieldLabel(shield)}</Name>
                            <Controls>
                                <ActionButton className="small" onClick={() => this.handleDecrease(shield, 25)} disabled={!shield.canDecrease()}>-25</ActionButton>
                                <ActionButton className="small" onClick={() => this.handleDecrease(shield, 10)} disabled={!shield.canDecrease()}>-10</ActionButton>
                                <ActionButton className="small" onClick={() => this.handleDecrease(shield, 5)} disabled={!shield.canDecrease()}>-5</ActionButton>
                                <ActionButton className="small" onClick={() => this.handleDecrease(shield, 1)} disabled={!shield.canDecrease()}>-1</ActionButton>

                                <InputField
                                    id={`shield-input-${shield.id}`}
                                    type="number"
                                    value={this.state.shieldInputs[shield.id] !== undefined ? this.state.shieldInputs[shield.id] : shield.currentHealth}
                                    onChange={(e) => this.handleInputChange(shield, e.target.value)}
                                    onWheel={(e) => this.handleWheel(e, shield)}
                                />

                                <ActionButton className="small" onClick={() => this.handleIncrease(shield, 1)} disabled={!shield.canIncrease()}>+1</ActionButton>
                                <ActionButton className="small" onClick={() => this.handleIncrease(shield, 5)} disabled={!shield.canIncrease()}>+5</ActionButton>
                                <ActionButton className="small" onClick={() => this.handleIncrease(shield, 10)} disabled={!shield.canIncrease()}>+10</ActionButton>
                                <ActionButton className="small" onClick={() => this.handleIncrease(shield, 25)} disabled={!shield.canIncrease()}>+25</ActionButton>
                            </Controls>
                        </Row>
                    ))}
                    {shields.length === 0 && <Row>No Shields Found</Row>}
                </ListContainer>
            </Container>
        );
    }
}

export default ShieldGeneratorList;
