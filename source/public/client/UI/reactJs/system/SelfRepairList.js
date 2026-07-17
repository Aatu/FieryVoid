import * as React from "react";
import styled from "styled-components";
import { Clickable } from "../styled";

const Container = styled.div`
    display: flex;
    flex-direction: column;
    margin-top: 1px;
    width: 100%;
    min-width: 250px;
    vertical-align: center;

    @media (max-width: 768px) {
        min-width: 250px;       
    }  

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
    /* Never show a horizontal scrollbar — the absolutely-positioned dragged row
       can momentarily be a hair wider than the content box. */
    overflow-x: hidden;
    display: block;
    /* Positioning context for the absolutely-positioned dragged row. */
    position: relative;

    /* Always reserve the scrollbar gutter so the box width never changes when the
       scrollbar appears/disappears — e.g. during a drag the height is pinned and
       the content momentarily fits, which would otherwise hide the scrollbar and
       widen the content (Chromium 94+, which the FV client runs on). */
    scrollbar-gutter: stable;

    /* While a row is being dragged, pin the box to its pre-drag height so the
       source-collapse / target-gap margin animations can't resize it. */
    ${props => props.$lockHeight ? `height: ${props.$lockHeight}px; max-height: ${props.$lockHeight}px;` : ''}

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

    @media (max-width: 768px) {
        text-align: center;         
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

    /* Drag-to-reorder: rows are grabbable; touch-action:none stops the touch
       gesture scrolling the list instead of dragging the row. In view-only mode
       (outside Initial Orders) dragging is disabled, so the row is a plain default
       cursor and touch gestures may scroll the list normally. */
    cursor: ${props => props.$readOnly ? 'default' : 'grab'};
    touch-action: ${props => props.$readOnly ? 'auto' : 'none'};
    user-select: none;
    position: relative;

    /* A live gap opens where the dragged row will land, so the drop target is
       obvious. The gap height matches the dragged row (gapSize, inline). No
       margin transition: an animating gap makes the rows' measured centres a
       moving target for the drop-slot scan, which feels sticky/jittery while
       dragging across several rows. Snapping the gap open is crisper. */

    /* The row currently being dragged. It is pulled OUT OF FLOW (position:
       absolute, positioned imperatively — see onDragMove) so the list closes up
       behind it automatically and, crucially, gaps opening/closing elsewhere
       never shift its baseline. It floats under the pointer via translateY from
       that fixed origin. z-index lifts it above the rest. */
    ${props => props.$dragging && `
        position: absolute;
        opacity: 0.95;
        z-index: 3;
        cursor: grabbing;
        background-color: rgba(43, 62, 81, 0.92);
        pointer-events: none;
        transition: none;
    `}

    /* MIDDLE drop target: a bold glowing yellow marker line sits at the row's top
       edge WITHOUT opening a gap — so no rows relayout as the pointer crosses
       slots, which is what made the full-gap approach judder. Only the very top
       and very bottom of the list still open a real gap (below). */
    ${props => props.$lineBefore && `
        &::before {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            top: -2px;
            height: 3px;
            background-color: #c9a028;
            border-radius: 2px;
            z-index: 4;
            pointer-events: none;
        }
    `}

    /* TOP-of-list drop: open a real gap above the first row (only one row moves,
       so it stays smooth) with the same yellow marker line inside it. */
    ${props => props.$gapBefore && `
        margin-top: ${props.$gapSize}px;
        &::before {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            top: -${props.$gapSize}px;
            height: ${props.$gapSize}px;
            box-shadow: inset 0 0 0 2px #ffcc33, 0 0 6px 1px rgba(255, 204, 51, 0.5);
            background-color: rgba(255, 204, 51, 0.12);
            pointer-events: none;
        }
    `}

    /* BOTTOM-of-list drop: a marker line at the last row's BOTTOM edge. It is
       deliberately NOT a real gap: a bottom margin-bottom grows the container's
       scrollable content, and closing it (dragging back up one) clamps scrollTop
       and shifts the pointer's content position up by a row — which made the
       bottom row jump TWO slots (badly on mobile, where the list is always
       scrolled and rows are tall). A line changes no layout, so no clamp. Nothing
       sits below the last row, so a line here is unambiguous (unlike the TOP). */
    ${props => props.$lineAtEnd && `
        &::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: -1px;
            height: 3px;
            background-color: #c9a028;
            border-radius: 2px;
            z-index: 4;
            pointer-events: none;
        }
    `}

    &:last-child {
        border-bottom: none;
    }

    @media (max-width: 768px) {
        flex-direction: column;
        align-items: stretch;
        margin-right: 0px;
    }
`;

const ItemInfo = styled.div`
    flex-grow: 1;
    display: flex;
    flex-direction: column;

    @media (max-width: 768px) {
        margin-bottom: 4px;
        text-align: center;          
    }
`;

const ItemName = styled.span`
    font-weight: bold;
`;

const ItemStatus = styled.span`
    font-size: 9px;
    color: #c8d5ea;
    margin-top: 2px;
    margin-right: 10px;    
    margin-left: 1px;

    @media (max-width: 768px) {
        text-align: center;
        margin-right: 0px;    
        margin-left: 0px;                  
    }

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

    @media (max-width: 768px) {
        justify-content: center;       
    }
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

// View-only read-out of a row's priority (replaces the editable InputField + buttons
// outside Initial Orders). Sized like the input so rows keep a consistent width.
const ReadOnlyPriority = styled.span`
    min-width: 20px;
    height: 16px;
    color: #e6e6e6;
    text-align: center;
    font-size: 11px;
    font-weight: bold;
    margin: 0 2px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
`;

class SelfRepairList extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            priorityInputs: {},
            // Drag-to-reorder visual state (null when no drag is active).
            drag: null // { keyId, dropIdx, dy }
        };
        this.lastOrder = [];

        // Mutable, non-render drag bookkeeping (kept off state to avoid
        // re-render churn on every pointermove). Mirrors the jQuery engine's
        // `drag` object but drives a React re-render only when the drop-gap or
        // translate changes materially.
        this.dragRef = null; // { keyId, pointerId, startY, startIdx, order, started }

        // Stable bound refs so we can add/remove the same fn on `window`
        // (gotcha: move/up live on window for the drag's duration).
        this.onDragMove = this.onDragMove.bind(this);
        this.onDragEnd = this.onDragEnd.bind(this);
        this.autoScrollTick = this.autoScrollTick.bind(this);

        // Edge auto-scroll while dragging (see onDragMove). rAF handle + the last
        // known pointer Y so the loop can keep working when the pointer holds
        // still at an edge (no more pointermove events fire).
        this.autoScrollRAF = null;
        this.autoScrollDir = 0; // -1 up, +1 down, 0 off
    }

    componentWillUnmount() {
        // Defensive: never leave window listeners / rAF loops running if the
        // window closes mid-drag.
        this.removeDragListeners();
        this.stopAutoScroll();
    }

    removeDragListeners() {
        window.removeEventListener('pointermove', this.onDragMove);
        window.removeEventListener('pointerup', this.onDragEnd);
        window.removeEventListener('pointercancel', this.onDragEnd);
    }

    // Mirror of server SelfRepair::getEffectiveCriticalRepairCost. B5W: all crits cost 1
    // self-repair point except C&C criticals, which cost 4. The whole CnC class family
    // (CnC + subclasses) carries name 'cnC' on the client; SecondaryCnC (a damage-soak
    // proxy) has its own name and is correctly not treated as a C&C.
    getEffectiveCriticalRepairCost(crit, sys) {
        if (sys.name === 'cnC') return 4;
        return crit.repairCost;
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

            if (sys.name === 'SelfRepair') continue; // Self Repair cannot repair Self Repair - not itself, not another SR (mirror of server)
            if (sys.repairPriority === 0) continue; // Priority 0 cannot be repaired

            // Deployed Kirishiac Heavy Orbital: its orbital / weapon / on-board SR are out of the
            // mother ship's reach - an UNRESTRICTED (whole-ship) Self Repair may not offer them
            // (the orbital's own restricted SR still can, via the repairRestrictedTo gate below).
            if (sys.privateRepairOnly && !system.repairRestrictedTo) continue;

            // Restricted Self Repair (mounted on a Kirishiac Heavy Orbital): may only service
            // the orbital's own systems - the allowed id list comes from the server each load
            // (docked: weapon + combined Structure block; deployed: orbital + weapon + itself)
            if (system.repairRestrictedTo && !system.repairRestrictedTo.includes(sys.id)) continue;

            //if Structure - skip if destroyed (can't be repaired anyway)
            if ((sys.name == 'structure') && (shipManager.systems.isDestroyed(sys.ship, sys))) continue;
            //if fitted to destroyed Structure - skip (can't be repaired anyway)
            //(structureHomeLocation: system may belong to another section's block than the one it is displayed in - Kirishiac orbitals)
            const blockLoc = (sys.structureHomeLocation !== undefined && sys.structureHomeLocation !== null) ? sys.structureHomeLocation : sys.location;
            if ((sys.name != 'structure') && (blockLoc != 0)) {
                var stru = shipManager.systems.getStructureSystem(sys.ship, blockLoc);
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
                        overridden: critOverridden, // explicit override wins ties (mirror of server sort)
                        cost: this.getEffectiveCriticalRepairCost(crit, sys), //C&C crits cost 4 (mirror of server)
                        id: sys.id, // For stable sort,
                        subId: crit.id, // For deterministic tie-breaking
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
                    overridden: isOverridden, // explicit override wins ties (mirror of server sort)
                    damage: damage,
                    maxHealth: sys.maxhealth,
                    id: sys.id,
                    subId: 0, // Ensure System comes before Criticals (if same priority)
                    keyId: sys.id // For action handlers
                });
            }
        }

        // Concatenate first
        const allItems = [...criticals, ...systems];

        // Sort Unified List: Priority (Desc) -> Explicit-override wins ties -> Visual Stability (Previous Order) -> ID/SubID (Asc)
        allItems.sort((a, b) => {
            if (a.priority !== b.priority) return b.priority - a.priority; // Higher priority first

            // Tie-break: an EXPLICIT player override beats an implicit/auto priority (e.g. the
            // +10 destroyed bump) - mirrors sortUnifiedRepairQueue on the server so the menu order
            // reflects the true repair order. Placed BEFORE visual-stability so an override can't be
            // held below a previously-higher auto row.
            const aOv = !!a.overridden, bOv = !!b.overridden;
            if (aOv !== bOv) return aOv ? -1 : 1; // overridden first

            // Visual Stability: Maintain relative order of items with equal priority
            if (this.lastOrder && this.lastOrder.length > 0) {
                const indexA = this.lastOrder.indexOf(a.keyId);
                const indexB = this.lastOrder.indexOf(b.keyId);

                // Only use previous order if BOTH items were previously rendered
                if (indexA !== -1 && indexB !== -1) {
                    return indexA - indexB;
                }
            }

            // Fallback / Deterministic Sort
            if (a.id !== b.id) return a.id - b.id;
            return a.subId - b.subId;
        });

        // Update lastOrder for next comparison
        this.lastOrder = allItems.map(item => item.keyId);

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
        // Re-assert the dragged row's absolute geometry after every commit. A
        // dropIdx re-render (gap opening) can reset the node's inline style; the
        // $dragging class also only takes effect via className, so on the FIRST
        // drag render the position:absolute + geometry must be (re)applied here so
        // the row doesn't snap back into flow. Kept out of React state on purpose
        // (see onDragMove) so the follow stays lag-free.
        if (this.dragRef && this.dragRef.started && this.listRef) {
            const el = this.listRef.querySelector('[data-keyid="' + this.dragRef.keyId + '"]');
            if (el) this.positionDraggedEl(el, this.dragRef, this.dragRef.lastClientY);
        }

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

    // ---- Pointer-based drag-to-reorder (mouse + touch + pen) --------------
    // Adapts the DORMANT jQuery engine in confirm.js (hBlasterTransferList) to
    // React. We keep its hard-won gotchas — Pointer Events for mouse+touch+pen,
    // move/up listeners on window, NO setPointerCapture, touch-action:none on
    // rows, preventDefault on pointerdown, a ~4px move threshold, and skipping
    // drags that start on an interactive child so buttons/input still work —
    // but because React owns the DOM we only translate the grabbed row + show a
    // drop indicator, then recompute priorities on drop (React re-renders the
    // reordered list). See reference_jquery_pointer_drag_reorder memory.

    onRowPointerDown(e, keyId, startIdx, order) {
        // View-only (outside Initial Orders): row-dragging is disabled entirely.
        if (this.props.readOnly) return;
        // Primary button / touch only.
        if (e.button != null && e.button !== 0) return;
        // Don't start a drag from the action buttons or the number input — let
        // them click/type. (They also stopPropagation, but guard here too.)
        if (e.target && e.target.closest &&
            e.target.closest('input, .sr-action-button')) return;

        // Row height drives how big a gap opens at the drop target.
        const rowEl = e.currentTarget;
        const gapSize = rowEl ? rowEl.offsetHeight : 24;
        // Freeze the container to its current pixel height for the drag so the
        // source-collapse / target-gap margin animations (which don't net to zero
        // height mid-transition) can't resize the auto-sized box.
        const lockHeight = this.listRef ? this.listRef.offsetHeight : 0;
        // Capture the row's geometry so we can pull it OUT OF FLOW (absolute) for
        // the drag: absolute + pinned under the pointer, its position anchored to
        // the POINTER's viewport Y (see positionDraggedEl) so it can't detach when
        // gaps open elsewhere and keeps tracking the finger while auto-scrolling.
        const anchorWidth = rowEl ? rowEl.offsetWidth : 0;
        // Where inside the row the pointer grabbed it — keeps the row under the
        // finger (no jump) and is the offset the viewport-anchored positioning
        // subtracts each frame.
        const grabOffsetInRow = rowEl ? (e.clientY - rowEl.getBoundingClientRect().top) : 0;

        this.dragRef = {
            keyId: keyId,
            pointerId: e.pointerId,
            startY: e.clientY,   // pointer Y at grab (used only for the 4px threshold)
            startIdx: startIdx,  // index in the visible (descending) list
            order: order,        // snapshot of the ordered items at grab time
            gapSize: gapSize,    // px of empty space to open at the drop target
            lockHeight: lockHeight, // px height to pin the container to while dragging
            anchorWidth: anchorWidth, // start width for the absolute drag
            grabOffsetInRow: grabOffsetInRow, // pointer offset within the grabbed row
            lastClientY: e.clientY, // most recent pointer Y (fed to the auto-scroll loop)
            started: false       // true once past the move threshold
        };

        e.preventDefault(); // suppress text-selection + synthetic click chain
        window.addEventListener('pointermove', this.onDragMove);
        window.addEventListener('pointerup', this.onDragEnd);
        window.addEventListener('pointercancel', this.onDragEnd);
    }

    onDragMove(e) {
        const d = this.dragRef;
        if (!d || e.pointerId !== d.pointerId) return;

        // Small threshold so a tap/click isn't treated as a drag.
        if (!d.started) {
            if (Math.abs(e.clientY - d.startY) < 4) return;
            d.started = true;
        }
        e.preventDefault();

        d.lastClientY = e.clientY;
        this.updateDragForPointer(e.clientY);
        this.updateAutoScroll(e.clientY);
    }

    // Core drag update for a given pointer viewport-Y: recompute the drop slot,
    // reposition the dragged row, and (change-guarded) setState the dropIdx.
    // Called by onDragMove AND by the auto-scroll loop (with the last pointer Y)
    // so the indicator + row keep tracking while the container scrolls itself.
    updateDragForPointer(clientY) {
        const d = this.dragRef;
        if (!d || !this.listRef) return;

        // Which slot is the pointer currently over? Compare against each settled
        // row's vertical centre (the dragged row is out of flow, so SKIP it).
        // dropIdx is in "grabbed-row-removed" index space (render expects it).
        //
        // Work in CONTENT coordinates (offsetTop + scrollTop), NOT viewport rects.
        // Viewport rects move when the container scrolls, and the BOTTOM gap
        // (margin-bottom) grows the content so closing it clamps scrollTop and
        // shifts every row up — which made "drag bottom row up one" jump TWO slots.
        // Content coords are scroll-independent, so that whole class of shift is
        // gone. The only gap that still moves a row's offsetTop is the TOP gap
        // (its margin-top pushes all settled rows down by gapSize); subtract it so
        // opening the top gap doesn't move the measurement target (oscillation).
        const rows = this.listRef.querySelectorAll('[data-keyid]');
        const listRect = this.listRef.getBoundingClientRect();
        const pointerContentY = (clientY - listRect.top) + this.listRef.scrollTop;
        const topGapOpen = this.state.drag && this.state.drag.dropIdx === 0;
        const topGapOffset = topGapOpen ? d.gapSize : 0;
        let dropIdx = 0;
        let draggedEl = null;
        for (let i = 0; i < rows.length; i++) {
            if (rows[i].getAttribute('data-keyid') === String(d.keyId)) {
                draggedEl = rows[i];
                continue;
            }
            const restingCentre = rows[i].offsetTop - topGapOffset + rows[i].offsetHeight / 2;
            if (pointerContentY < restingCentre) break;
            dropIdx++;
        }

        // Position the dragged row IMPERATIVELY (zero React latency; setState-per-
        // move lags on fast drags). Re-asserted in componentDidUpdate.
        if (draggedEl) this.positionDraggedEl(draggedEl, d, clientY);

        const cur = this.state.drag;
        if (!cur || cur.keyId !== d.keyId || cur.dropIdx !== dropIdx) {
            this.setState({ drag: { keyId: d.keyId, startIdx: d.startIdx, dropIdx: dropIdx, gapSize: d.gapSize, lockHeight: d.lockHeight } });
        }
    }

    // Pin the absolute dragged row under the pointer. Anchored to the POINTER's
    // viewport position (not a fixed grab-time dy) so it keeps tracking the finger
    // while the container auto-scrolls beneath it: content-top = (pointer - list
    // top) + scrollTop - grabOffset. CLAMPED to the content range so it can't slide
    // up under the header or past the bottom — at the extremes it snaps into the
    // top/bottom gap. Used by updateDragForPointer + re-asserted by
    // componentDidUpdate (which passes dragRef.lastClientY).
    positionDraggedEl(el, d, clientY) {
        const listRect = this.listRef.getBoundingClientRect();
        let top = (clientY - listRect.top) + this.listRef.scrollTop - d.grabOffsetInRow;
        const maxTop = Math.max(0, this.listRef.scrollHeight - d.gapSize);
        if (top < 0) top = 0;
        else if (top > maxTop) top = maxTop;

        el.style.top = top + 'px';
        el.style.left = '0px';
        el.style.width = d.anchorWidth + 'px';
        el.style.transform = 'none';
    }

    // Edge auto-scroll: while the pointer sits within EDGE_ZONE px of the
    // container's top/bottom edge, scroll the list toward that edge so the player
    // can drag past the visible rows (essential on mobile, where touch-action:none
    // disables native scroll mid-drag). Speed is PROPORTIONAL to how deep into the
    // zone the pointer is. Runs on a rAF loop (below) because pointermove stops
    // firing when the finger holds still at the edge.
    // Decide direction + speed only (no scheduling — that lives in the two helpers
    // below, guarded so there is never more than one rAF in flight).
    updateAutoScroll(clientY) {
        const list = this.listRef;
        if (!list) { this.stopAutoScroll(); return; }
        const EDGE_ZONE = 30; // px from an edge that triggers scroll
        const rect = list.getBoundingClientRect();
        const canUp = list.scrollTop > 0;
        const canDown = list.scrollTop < list.scrollHeight - list.clientHeight - 1;

        const distTop = clientY - rect.top;             // small near the top edge
        const distBottom = rect.bottom - clientY;       // small near the bottom edge

        if (canUp && distTop < EDGE_ZONE) {
            this.autoScrollDir = -1;
            // Deeper into the zone (smaller dist) => faster, ~2..14 px/frame.
            this.autoScrollSpeed = 2 + 12 * (1 - Math.max(0, distTop) / EDGE_ZONE);
            this.ensureAutoScrollRunning();
        } else if (canDown && distBottom < EDGE_ZONE) {
            this.autoScrollDir = 1;
            this.autoScrollSpeed = 2 + 12 * (1 - Math.max(0, distBottom) / EDGE_ZONE);
            this.ensureAutoScrollRunning();
        } else {
            this.stopAutoScroll();
        }
    }

    ensureAutoScrollRunning() {
        if (this.autoScrollRAF == null) {
            this.autoScrollRAF = requestAnimationFrame(this.autoScrollTick);
        }
    }

    stopAutoScroll() {
        this.autoScrollDir = 0;
        if (this.autoScrollRAF != null) {
            cancelAnimationFrame(this.autoScrollRAF);
            this.autoScrollRAF = null;
        }
    }

    autoScrollTick() {
        this.autoScrollRAF = null; // this frame consumed
        const list = this.listRef;
        const d = this.dragRef;
        if (!list || !d || this.autoScrollDir === 0) return;

        const before = list.scrollTop;
        list.scrollTop = before + this.autoScrollDir * (this.autoScrollSpeed || 6);
        if (list.scrollTop === before) { this.stopAutoScroll(); return; } // hit a limit

        // Track the newly-revealed rows at the (held) pointer position, then
        // re-evaluate whether to keep scrolling. updateAutoScroll re-schedules via
        // ensureAutoScrollRunning (only one rAF ever in flight).
        this.updateDragForPointer(d.lastClientY);
        this.updateAutoScroll(d.lastClientY);
    }

    onDragEnd(e) {
        const d = this.dragRef;
        if (!d) return;
        if (e && e.pointerId != null && e.pointerId !== d.pointerId) return;

        this.removeDragListeners();
        this.stopAutoScroll();

        // Clear the imperative drag geometry off the node so nothing survives the
        // drop re-render (React doesn't manage these — we set them directly).
        if (this.listRef) {
            const draggedEl = this.listRef.querySelector('[data-keyid="' + d.keyId + '"]');
            if (draggedEl) {
                draggedEl.style.transform = '';
                draggedEl.style.top = '';
                draggedEl.style.left = '';
                draggedEl.style.width = '';
            }
        }
        this.dragRef = null;

        const dropState = this.state.drag;
        this.setState({ drag: null });

        // No real drag (a click) or dropped back in the same slot -> no writes.
        if (!d.started || !dropState) return;
        if (dropState.dropIdx === d.startIdx) return;

        this.applyDropReorder(d.order, d.keyId, dropState.dropIdx);
    }

    // Cascade-insert. The dropped row takes (priority of the row now directly
    // BELOW it) + 1 — so a 7 dropped just above a 9 becomes 10. Then we walk UP
    // the list resolving collisions: any row above whose priority isn't strictly
    // greater than the value we just assigned gets bumped +1, cascading until
    // the descending order is restored (the old 10 becomes 11, etc). Rows below
    // the drop are untouched. At the very bottom there is no row below, so the
    // dropped row goes one below the row above it (floored at 1).
    applyDropReorder(order, keyId, dropIdx) {
        const { ship, system } = this.props;

        // Build the post-drop visual order (the grabbed row moved to dropIdx).
        const fromIdx = order.findIndex(it => it.keyId === keyId);
        if (fromIdx === -1) return;
        const items = order.slice();
        const [moved] = items.splice(fromIdx, 1);
        items.splice(dropIdx, 0, moved);

        // items[dropIdx] is the moved row itself; read its neighbours, not it.
        const below = items[dropIdx + 1]; // row now directly below the drop
        const above = items[dropIdx - 1]; // row now directly above the drop

        let assign;
        if (below) {
            assign = below.priority + 1;      // land just above the row below
        } else if (above) {
            assign = Math.max(1, above.priority - 1); // dropped at the bottom
        } else {
            assign = 1;                        // only row in the list
        }

        system.setOverride(keyId, assign);

        // Walk upward, bumping each colliding row so priorities stay strictly
        // descending above the drop point.
        for (let i = dropIdx - 1; i >= 0; i--) {
            if (items[i].priority > assign) break; // already strictly above
            assign += 1;
            system.setOverride(items[i].keyId, assign);
        }

        // Single re-render / server sync for the whole cascade.
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
        const { ship, readOnly } = this.props;
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
                    {readOnly ? 'Repair Queue (view only)' : 'Manage Repair Queue'}
                </Header>

                <ListContainer
                    ref={el => { this.listRef = el; }}
                    $lockHeight={this.state.drag ? this.state.drag.lockHeight : 0}
                >
                    {repairableSystems.length === 0 && <CenteredListItem>No damaged systems</CenteredListItem>}
                    {repairableSystems.map((item, index) => {
                        const drag = this.state.drag;
                        const isDragging = drag && drag.keyId === item.keyId;
                        // Drop-target indicator, in "grabbed-row-removed" index
                        // space mapped back to the rendered list. HYBRID: the very
                        // TOP opens a real gap (disambiguates "above everything" vs
                        // "into the first slot"); the very BOTTOM and every MIDDLE
                        // slot use a yellow MARKER LINE — no gap => no per-row
                        // relayout (no judder) and, critically for the bottom, no
                        // content-height change (a bottom gap grew the scroll area
                        // and its removal clamped scrollTop, jumping two slots).
                        let gapBefore = false, lineAtEnd = false, lineBefore = false;
                        if (drag && !isDragging) {
                            const settledCount = repairableSystems.length - 1; // rows minus dragged
                            const nonDraggedBefore = index > drag.startIdx ? index - 1 : index;
                            if (drag.dropIdx === 0 && nonDraggedBefore === 0) {
                                gapBefore = true;   // above everything -> top gap
                            } else if (drag.dropIdx === settledCount &&
                                       nonDraggedBefore === settledCount - 1) {
                                lineAtEnd = true;   // below everything -> line at last row's bottom
                            } else if (drag.dropIdx === nonDraggedBefore) {
                                lineBefore = true;  // middle -> marker line
                            }
                        }
                        return (
                        <ListItem
                            key={`${item.type}-${item.sys.id}${item.crit ? '-' + item.crit.id : ''}`}
                            data-keyid={item.keyId}
                            $dragging={isDragging}
                            $gapBefore={gapBefore}
                            $lineAtEnd={lineAtEnd}
                            $lineBefore={lineBefore}
                            $gapSize={drag ? drag.gapSize : 0}
                            $readOnly={readOnly}
                            onPointerDown={(e) => this.onRowPointerDown(e, item.keyId, index, repairableSystems)}
                        >
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
                                {readOnly ? (
                                    // View-only (outside Initial Orders): show the priority as a static
                                    // read-out — no editing controls, no drag. The player can still see
                                    // the queue order they set earlier.
                                    <ReadOnlyPriority title="Priority (view only)">{item.priority}</ReadOnlyPriority>
                                ) : (
                                    <>
                                        <ActionButton className="sr-action-button" title="Reset Default" onClick={(e) => this.handleReset(e, item.keyId)} img="./img/iconSRCancel.png" />
                                        <ActionButton className="sr-action-button" title="Decrease Priority" onClick={(e) => this.handleDown(e, item.keyId, item.priority)} img="./img/systemicons/AAclasses/iconMinus.png" />
                                        <InputField
                                            id={`prio-input-${item.keyId}`}
                                            type="number"
                                            value={this.state.priorityInputs[item.keyId] !== undefined ? this.state.priorityInputs[item.keyId] : item.priority}
                                            onChange={(e) => this.handleInputChange(e, item.keyId, item.priority)}
                                            onClick={(e) => e.stopPropagation()}
                                            onWheel={(e) => this.handleWheel(e, item.keyId, item.priority)}
                                        />
                                        <ActionButton className="sr-action-button" title="Increase Priority" onClick={(e) => this.handleUp(e, item.keyId, item.priority)} img="./img/systemicons/AAclasses/iconPlus.png" />
                                        <ActionButton className="sr-action-button" title="Move to Top" onClick={(e) => this.handleTop(e, item.keyId)} img="./img/iconSRHigh.png" />
                                    </>
                                )}
                            </ActionButtons>
                        </ListItem>
                        );
                    })}
                </ListContainer>
                {!readOnly && selfRepairCount > 1 && (
                    <Footer>
                        <PropagateButton onClick={(e) => this.handlePropagate(e)}>Set all Self Repair systems</PropagateButton>
                    </Footer>
                )}
            </Container>
        );
    }
}

export default SelfRepairList;


