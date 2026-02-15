import * as React from "react";
import styled from "styled-components";
import { Clickable } from "../styled";

const Container = styled.div`
    display: flex;
    flex-direction: column;
    margin-top: 5px;
    width: 100%;
    min-width: 200px;
    vertical-align: center;
`;

const Header = styled.div`
    padding: 3px;
    background-color: #2b3e51;
    border: 1px solid #496791;
    color: #f2f2f2;
    text-align: center;
    font-size: 12px;
    margin-bottom: 2px;
    font-weight: bold;
`;

const ListContainer = styled.div`
    background-color: rgba(0, 0, 0, 0.9);
    border: 1px solid #496791;
    max-height: 200px;
    overflow-y: auto;
    display: block;

    &::-webkit-scrollbar {
        width: 6px;
    }
    &::-webkit-scrollbar-track {
        background: #0d1620; 
    }
    &::-webkit-scrollbar-thumb {
        background: #2b3e51; 
    }
    &::-webkit-scrollbar-thumb:hover {
        background: #5a7ea8; 
    }
`;

const ListItem = styled.div`
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 3px 5px;
    margin-right: 3px;      
    border-bottom: 1px solid #2b3e51;
    font-size: 12px;
    color: #e6e6e6;

    &:last-child {
        border-bottom: none;
    }
`;

const ItemInfo = styled.div`
    flex-grow: 1;
    display: flex;
    flex-direction: column;
`;

const ItemName = styled.span`
    font-weight: bold;
`;

const ItemStatus = styled.span`
    font-size: 10px;
    color: #c8d5ea;
    margin-top: 2px;
    margin-right: 8px;    
    margin-left: 1px;
`;

const CriticalItemName = styled(ItemName)`
    color: #ffb833;
    font-weight: normal;    
`;

const DestroyedItemName = styled(ItemName)`
    color: #ff3333;
    font-weight: normal;    
`;

const ActionButtons = styled.div`
    display: flex;
    gap: 2px;
`;

const ActionButton = styled.div`
    width: 18px;
    height: 18px;
    background-image: url(${props => props.img});
    background-size: cover;
    cursor: pointer;
    opacity: 0.9;
    margin-left: 3px;
    &:hover {
        opacity: 1;
    }
    
     ${Clickable}
`;

const Footer = styled.div`
    padding: 4px;
    background-color: rgba(0, 0, 0, 0.9);
    border: 1px solid #496791;
    border-top: none;
    text-align: center;
`;

const PropagateButton = styled.div`
    cursor: pointer;
    background-color: #2b3e51;
    border: 1px solid #496791;
    padding: 3px 8px;
    font-size: 12px;
    color: #f2f2f2;
    font-weight: normal;       
    display: inline-block;
    
    &:hover {
        background-color: #496791;
        color: #ffffff;
    }
`;

const Divider = styled.span`
    display: inline-block;
    width: 1px;
    height: 10px;
    background-color: #496791;
    margin: 0 4px;
    font-weight: bold;     
    vertical-align: middle;
    opacity: 0.7;
`;

const CenteredListItem = styled(ListItem)`
    justify-content: center;
    font-style: italic;
    opacity: 0.7;
`;

const InputField = styled.input`
    width: 20px;
    height: 16px;
    background: rgba(0,0,0,0.5);
    border: 1px solid #496791;
    color: #e6e6e6;
    text-align: center;
    font-size: 11px; 
    margin: 0 2px;
    
    // Hide spinner
    &::-webkit-inner-spin-button, 
    &::-webkit-outer-spin-button { 
        -webkit-appearance: none; 
        margin: 0; 
    }
    -moz-appearance: textfield;
`;

class SelfRepairList extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            priorityInputs: {}
        };
    }

    getRepairableSystems() {
        const { ship, system } = this.props;
        const systems = [];
        const criticals = [];

        // DEBUG: Trace start of calculation
        // console.log("SelfRepairList: Calculating systems...");

        // Ensure systems is iterable (handle Object vs Array)
        const shipSystems = Array.isArray(ship.systems) ? ship.systems : Object.values(ship.systems);

        for (const sys of shipSystems) {

            if (sys.repairPriority === 0) continue; // Priority 0 cannot be repaired

            //if Structure - skip if destroyed (can't be repaired anyway)
            if ((sys.name == 'structure') && (shipManager.systems.isDestroyed(sys.ship, sys))) continue;
            //if fitted to destroyed Structure - skip (can't be repaired anyway)
            if ((sys.name != 'structure') && (sys.location != 0)) {
                var stru = shipManager.systems.getStructureSystem(sys.ship, sys.location);
                if (stru && shipManager.systems.isDestroyed(sys.ship, stru)) continue;
            }

            // Calculate effective priority for the SYSTEM
            // Start with base priority
            let basePriority = sys.repairPriority;

            // Apply override if it exists
            let isOverridden = false;
            if (system.priorityChanges && (sys.id in system.priorityChanges) && system.priorityChanges[sys.id] >= 0) {
                basePriority = system.priorityChanges[sys.id];
                isOverridden = true;
            }

            // Apply implicit +10 for destroyed systems (ONLY if not overridden)
            if (!isOverridden && shipManager.systems.isDestroyed(ship, sys) && basePriority <= 10) {
                basePriority += 10;
            }

            // --- CRITICALS ---
            if (!shipManager.systems.isDestroyed(ship, sys) && sys.criticals) {
                //Ensure criticals is iterable
                const sysCriticals = Array.isArray(sys.criticals) ? sys.criticals : Object.values(sys.criticals);

                for (const crit of sysCriticals) {

                    if (crit.repairPriority === 0) continue; // Cannot be repaired
                    if (crit.turn >= gamedata.turn) {
                        continue; // Caused this turn
                    }
                    if (crit.oneturn || (crit.turnend > 0)) {
                        continue; // Temporary or already fixed
                    }

                    // START CRITICAL PRIORITY LOGIC
                    let critPriority = crit.repairPriority || 0;
                    const compositeKey = sys.id + '-' + crit.id;
                    let critOverridden = false;

                    // Check for Critical Override
                    if (system.priorityChanges && (compositeKey in system.priorityChanges) && system.priorityChanges[compositeKey] >= 0) {
                        critPriority = system.priorityChanges[compositeKey];
                        critOverridden = true;
                        // console.log(`SelfRepairList: Found override for ${compositeKey}: ${critPriority}`);
                    } else {
                        // Standard Logic (if not overridden): Add NATIVE System Priority if < 10
                        if (critPriority < 10) {
                            critPriority += sys.repairPriority;
                        }
                    }

                    criticals.push({
                        type: 'critical',
                        sys: sys,
                        crit: crit,
                        priority: critPriority,
                        cost: crit.repairCost,
                        id: sys.id, // For stable sort,
                        keyId: compositeKey // For action handlers
                    });
                }
            }


            // --- SYSTEM DAMAGE ---
            // Only damaged systems
            const damage = shipManager.systems.getTotalDamage(sys);
            if (damage > 0) {
                systems.push({
                    type: 'system',
                    sys: sys,
                    priority: basePriority,
                    damage: damage,
                    maxHealth: sys.maxhealth,
                    keyId: sys.id // For action handlers
                });
            }
        }

        // Concatenate first
        const allItems = [...criticals, ...systems];

        // Sort Unified List: Priority DESC, then Criticals over Systems, then ID ASC
        allItems.sort((a, b) => {
            if (a.priority !== b.priority) return b.priority - a.priority; // Higher priority first

            // If priorities equal, Criticals come first (legacy behavior/tie-breaker)
            if (a.type !== b.type) {
                return a.type === 'critical' ? -1 : 1;
            }

            // Secondary sorts from previous logic
            if (a.type === 'critical') {
                if (a.cost !== b.cost) return b.cost - a.cost; // Costlier crits first
            } else {
                if (a.maxHealth !== b.maxHealth) return a.maxHealth - b.maxHealth; // Smaller systems first
            }

            return a.id - b.id;
        });

        return allItems;
    }

    handleInputChange(e, targetId, currentPriority) {
        const value = e.target.value;

        // Allow empty string for typing
        if (value === '') {
            this.setState(prevState => ({
                priorityInputs: {
                    ...prevState.priorityInputs,
                    [targetId]: ''
                }
            }));
            return;
        }

        const newValue = parseInt(value, 10);
        if (isNaN(newValue)) return;

        // Update state to reflect input immediately (for smooth typing)
        this.setState(prevState => ({
            priorityInputs: {
                ...prevState.priorityInputs,
                [targetId]: newValue
            }
        }));

        this.setPriority(targetId, newValue);
    }

    handleWheel(e, targetId, currentPriority) {
        e.preventDefault();
        const delta = e.deltaY < 0 ? 1 : -1;
        const newPrio = currentPriority + delta;

        if (newPrio < 1) return; // Minimum priority 1?

        this.setPriority(targetId, newPrio);
    }

    componentDidUpdate(prevProps) {
        // Sync state with props if they change externally (e.g. from server or other components)
        // We only want to update state if the PROP value is different from the STATE value
        // AND the user is not currently editing this specific input (optional, but good for UX)

        // For simplicity, we can just clear inputs that match the prop value to save memory, 
        // or ensure they match. 
        // Let's iterate repairable systems? No, that's expensive.
        // We can just check if any priority changed. 
        // Re-calculating repairable systems in componentDidUpdate might be heavy?
        // Let's just rely on render to pass `value` effectively.

        // Actually, for a controlled input that allows temporary deviation while typing (like "1" then "0" for "10"),
        // we need local state.

        // Let's refresh our idea of "current" priorities
        // We can't easily get "all current priorities" without calling getRepairableSystems.
        // Let's try to match ShieldGeneratorList's approach of syncing in componentDidUpdate.

        const systems = this.getRepairableSystems();
        const currentInputs = this.state.priorityInputs;
        const newInputs = {};
        let changed = false;

        systems.forEach(item => {
            const id = item.keyId;
            const prio = item.priority;

            // If state doesn't match prop, and we aren't focused?
            // Or just always sync if different?
            if (currentInputs[id] !== undefined && currentInputs[id] !== prio && document.activeElement !== document.getElementById(`prio-input-${id}`)) {
                newInputs[id] = prio;
                changed = true;
            }
        });

        if (changed) {
            this.setState(prevState => ({
                priorityInputs: {
                    ...prevState.priorityInputs,
                    ...newInputs
                }
            }));
        }
    }

    handleTop(e, targetId) {
        e.stopPropagation();

        const systems = this.getRepairableSystems();
        if (systems.length === 0) return;

        const maxPrio = systems[0].priority;
        const myItem = systems.find(s => s.keyId === targetId); // Use keyId

        if (!myItem) return;

        // If I am already at the top priority, do nothing
        if (myItem.priority === maxPrio) {
            return;
        }

        let newPrio = maxPrio + 1;

        this.setPriority(targetId, newPrio);
    }

    handleUp(e, targetId, currentPriority) {
        e.stopPropagation();

        let newPrio = currentPriority + 1;

        if (newPrio !== currentPriority) {
            this.setPriority(targetId, newPrio);
        }
    }

    handleDown(e, targetId, currentPriority) {
        e.stopPropagation();

        if (currentPriority <= 1) return; // Min priority
        this.setPriority(targetId, currentPriority - 1);
    }

    handleReset(e, targetId) {
        e.stopPropagation();
        // Resetting simply means removing the override. 
        // We can pass -1 or handle it in setPriority.
        // The previous implementation looked up default priority.
        // For Criticals, default is implied. For Systems, default is on the object.
        // Simplest way: set override to -1 to trigger "unset" logic in server/client.

        this.setPriority(targetId, -1);
    }

    setPriority(targetId, newPriority) {
        const { ship, system } = this.props;

        system.setOverride(targetId, newPriority);
        webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
    }

    handlePropagate(e) {
        e.stopPropagation();
        const { ship, system } = this.props;

        // Propagate current system's priority changes to all other SelfRepair systems
        for (const sys of ship.systems) {
            if (sys.name === 'SelfRepair' && sys.id !== system.id) {

                // 1. Clear overrides on target that are NOT in source (or are unset in source)
                // We iterate target's priorityChanges to find what to remove
                if (sys.priorityChanges) {
                    for (const key in sys.priorityChanges) {
                        // If source doesn't have it, or source has it as -1 (though source should just not have it if -1)
                        // But let's be safe: if key not in source.priorityChanges, unset it on target
                        if (!system.priorityChanges || !(key in system.priorityChanges)) {
                            sys.setOverride(key, -1);
                        }
                    }
                }

                // 2. Copy all valid overrides from source to target
                if (system.priorityChanges) {
                    for (const key in system.priorityChanges) {
                        const prio = system.priorityChanges[key];
                        if (prio >= 0) {
                            sys.setOverride(key, prio);
                        }
                    }
                }
            }
        }
        webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
    }

    render() {
        const { ship } = this.props;
        const repairableSystems = this.getRepairableSystems();

        // Count number of SelfRepair systems
        let selfRepairCount = 0;
        const shipSystems = Array.isArray(ship.systems) ? ship.systems : Object.values(ship.systems);
        for (const sys of shipSystems) {
            if (sys.name === 'SelfRepair') selfRepairCount++;
        }

        return (
            <Container>
                <Header>
                    Manage Repair Queue
                </Header>

                <ListContainer>
                    {repairableSystems.length === 0 && <CenteredListItem>No damaged systems</CenteredListItem>}
                    {repairableSystems.map(item => (
                        <ListItem key={`${item.type}-${item.sys.id}${item.crit ? '-' + item.crit.id : ''}`}>
                            <ItemInfo>
                                {item.type === 'critical' ? (
                                    <>
                                        <CriticalItemName>{item.sys.displayName} ({item.crit.description || item.crit.phpclass})</CriticalItemName>
                                        <ItemStatus>
                                            Cost: {item.cost} <Divider /> Id: {item.sys.id}
                                        </ItemStatus>
                                    </>
                                ) : (
                                    <>
                                        {shipManager.systems.isDestroyed(ship, item.sys) ? (
                                            <DestroyedItemName>{item.sys.displayName}</DestroyedItemName>
                                        ) : (
                                            <ItemName>{item.sys.displayName}</ItemName>
                                        )}
                                        <ItemStatus>
                                            HP: {shipManager.systems.getRemainingHealth(item.sys)} / {item.sys.maxhealth} <Divider /> Id: {item.sys.id}
                                        </ItemStatus>
                                    </>
                                )}
                            </ItemInfo>
                            <ActionButtons>
                                <ActionButton title="Reset Default" onClick={(e) => this.handleReset(e, item.keyId)} img="./img/iconSRCancel.png" />
                                <ActionButton title="Decrease Priority" onClick={(e) => this.handleDown(e, item.keyId, item.priority)} img="./img/systemicons/AAclasses/iconMinus.png" />
                                <InputField
                                    id={`prio-input-${item.keyId}`}
                                    type="number"
                                    value={this.state.priorityInputs[item.keyId] !== undefined ? this.state.priorityInputs[item.keyId] : item.priority}
                                    onChange={(e) => this.handleInputChange(e, item.keyId, item.priority)}
                                    onClick={(e) => e.stopPropagation()}
                                    onWheel={(e) => this.handleWheel(e, item.keyId, item.priority)}
                                />
                                <ActionButton title="Increase Priority" onClick={(e) => this.handleUp(e, item.keyId, item.priority)} img="./img/systemicons/AAclasses/iconPlus.png" />
                                <ActionButton title="Move to Top" onClick={(e) => this.handleTop(e, item.keyId)} img="./img/iconSRHigh.png" />
                            </ActionButtons>
                        </ListItem>
                    ))}
                </ListContainer>
                {selfRepairCount > 1 && (
                    <Footer>
                        <PropagateButton onClick={(e) => this.handlePropagate(e)}>Set all Self Repair systems</PropagateButton>
                    </Footer>
                )}
            </Container>
        );
    }
}

export default SelfRepairList;


