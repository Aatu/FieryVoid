import * as React from "react";
import styled from "styled-components";
import { Clickable } from "../styled";

// ─── Styled components (visual language matches SelfRepairList) ───────────────
// GTS_Triad
const Container = styled.div`
    display: flex;
    flex-direction: column;
    margin-top: 1px;
    width: 100%;
    min-width: 250px;
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
    overflow-x: hidden;
    display: block;
    position: relative;
    scrollbar-gutter: stable;
    ${props => props.$lockHeight ? `height: ${props.$lockHeight}px; max-height: ${props.$lockHeight}px;` : ''}

    &::-webkit-scrollbar { width: 6px; }
    &::-webkit-scrollbar-track { background: #0d1620; }
    &::-webkit-scrollbar-thumb { background: #2b3e51; }
    &::-webkit-scrollbar-thumb:hover { background: #5a7ea8; }
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
    cursor: ${props => props.$readOnly ? 'default' : 'grab'};
    touch-action: ${props => props.$readOnly ? 'auto' : 'none'};
    user-select: none;
    position: relative;

    ${props => props.$dragging && `
        position: absolute;
        opacity: 0.95;
        z-index: 3;
        cursor: grabbing;
        background-color: rgba(43, 62, 81, 0.92);
        pointer-events: none;
        transition: none;
    `}

    ${props => props.$lineBefore && `
        &::before {
            content: "";
            position: absolute;
            left: 0; right: 0; top: -2px;
            height: 3px;
            background-color: #c9a028;
            border-radius: 2px;
            z-index: 4;
            pointer-events: none;
        }
    `}

    ${props => props.$gapBefore && `
        margin-top: ${props.$gapSize}px;
        &::before {
            content: "";
            position: absolute;
            left: 0; right: 0;
            top: -${props.$gapSize}px;
            height: ${props.$gapSize}px;
            box-shadow: inset 0 0 0 2px #ffcc33, 0 0 6px 1px rgba(255, 204, 51, 0.5);
            background-color: rgba(255, 204, 51, 0.12);
            pointer-events: none;
        }
    `}

    ${props => props.$lineAtEnd && `
        &::after {
            content: "";
            position: absolute;
            left: 0; right: 0; bottom: -1px;
            height: 3px;
            background-color: #c9a028;
            border-radius: 2px;
            z-index: 4;
            pointer-events: none;
        }
    `}

    &:last-child { border-bottom: none; }
`;

const CenteredListItem = styled(ListItem)`
    justify-content: center;
    font-style: italic;
    opacity: 0.7;
`;

const ItemInfo = styled.div`
    flex-grow: 1;
    display: flex;
    flex-direction: column;
`;

const ItemName = styled.span`
    font-weight: bold;
`;

const DestroyedItemName = styled(ItemName)`
    color: #ff3333;
    font-weight: normal;
`;

const ItemStatus = styled.span`
    font-size: 9px;
    color: #c8d5ea;
    margin-top: 2px;
    margin-left: 1px;
`;

const Divider = styled.span`
    display: inline-block;
    width: 1px;
    height: 10px;
    background-color: #496791;
    margin: 0 4px;
    vertical-align: middle;
    opacity: 0.7;
`;

const Footer = styled.div`
    padding: 4px;
    background-color: rgba(0, 0, 0, 0.9);
    border: 1px solid #496791;
    border-top: none;
    text-align: center;
`;

const ResetButton = styled.div`
    cursor: pointer;
    background-color: #2b3e51;
    border: 1px solid #496791;
    padding: 3px 8px;
    font-size: 12px;
    color: #f2f2f2;
    font-weight: normal;
    display: inline-block;
    &:hover { background-color: #496791; color: #ffffff; }
`;

const DragHint = styled.div`
    font-size: 9px;
    color: #7a99bb;
    text-align: center;
    padding: 2px 0 0 0;
    font-style: italic;
`;

// ─── Component ────────────────────────────────────────────────────────────────

class StructureSelfRepairList extends React.Component {
    constructor(props) {
        super(props);
        this.state = { drag: null };

        this.dragRef      = null;
        this.onDragMove   = this.onDragMove.bind(this);
        this.onDragEnd    = this.onDragEnd.bind(this);
        this.autoScrollTick = this.autoScrollTick.bind(this);
        this.autoScrollRAF  = null;
        this.autoScrollDir  = 0;
    }

    componentWillUnmount() {
        this.removeDragListeners();
        this.stopAutoScroll();
    }

    removeDragListeners() {
        window.removeEventListener('pointermove', this.onDragMove);
        window.removeEventListener('pointerup',   this.onDragEnd);
        window.removeEventListener('pointercancel', this.onDragEnd);
    }

    // ── Data ─────────────────────────────────────────────────────────────────

    /* Returns structure blocks in the order the player has set (or default order
       if no override). Each entry: { id, displayName, hp, maxhealth, destroyed } */
    getOrderedBlocks() {
        const { ship, system } = this.props;

        // structureBlocks is sent by PHP stripForJson — use it as the authoritative
        // block list (id, displayName, location) and augment with live health data.
        const blocks = (system.structureBlocks || []).map(b => {
            const liveSys = (Array.isArray(ship.systems) ? ship.systems : Object.values(ship.systems))
                .find(s => s.id === b.id);
            const hp         = liveSys ? shipManager.systems.getRemainingHealth(liveSys) : b.maxhealth;
            const maxhealth  = liveSys ? liveSys.maxhealth : (b.maxhealth || 0);
            const destroyed  = liveSys ? shipManager.systems.isDestroyed(ship, liveSys) : false;
            return { id: b.id, displayName: b.displayName, hp, maxhealth, destroyed };
        });

        const repairOrder = system.repairOrder || [];

        if (repairOrder.length === 0) {
            // Default display order: destroyed first, then most damaged
            return blocks.slice().sort((a, b) => {
                const da = b.destroyed ? 1 : 0, db = a.destroyed ? 1 : 0;
                if (da !== db) return da - db;
                return (b.maxhealth - b.hp) - (a.maxhealth - a.hp);
            });
        }

        // Player order: ordered blocks first, then unordered remainder
        const ordered   = [];
        const seen      = new Set();
        for (const id of repairOrder) {
            const entry = blocks.find(b => b.id === id);
            if (entry) { ordered.push(entry); seen.add(id); }
        }
        for (const entry of blocks) {
            if (!seen.has(entry.id)) ordered.push(entry);
        }
        return ordered;
    }

    // ── Drag-to-reorder (identical machinery to SelfRepairList) ──────────────

    onRowPointerDown(e, id, startIdx, order) {
        if (this.props.readOnly) return;
        if (e.button != null && e.button !== 0) return;
        if (e.target && e.target.closest && e.target.closest('.ssr-action-button')) return;

        const rowEl         = e.currentTarget;
        const gapSize       = rowEl ? rowEl.offsetHeight : 24;
        const lockHeight    = this.listRef ? this.listRef.offsetHeight : 0;
        const anchorWidth   = rowEl ? rowEl.offsetWidth : 0;
        const grabOffsetInRow = rowEl ? (e.clientY - rowEl.getBoundingClientRect().top) : 0;

        this.dragRef = {
            keyId: id, pointerId: e.pointerId,
            startY: e.clientY, startIdx,
            order, gapSize, lockHeight, anchorWidth, grabOffsetInRow,
            lastClientY: e.clientY, started: false
        };

        e.preventDefault();
        window.addEventListener('pointermove', this.onDragMove);
        window.addEventListener('pointerup',   this.onDragEnd);
        window.addEventListener('pointercancel', this.onDragEnd);
    }

    onDragMove(e) {
        const d = this.dragRef;
        if (!d || e.pointerId !== d.pointerId) return;
        if (!d.started) {
            if (Math.abs(e.clientY - d.startY) < 4) return;
            d.started = true;
        }
        e.preventDefault();
        d.lastClientY = e.clientY;
        this.updateDragForPointer(e.clientY);
        this.updateAutoScroll(e.clientY);
    }

    updateDragForPointer(clientY) {
        const d = this.dragRef;
        if (!d || !this.listRef) return;

        const rows = this.listRef.querySelectorAll('[data-keyid]');
        const listRect = this.listRef.getBoundingClientRect();
        const pointerContentY = (clientY - listRect.top) + this.listRef.scrollTop;
        const topGapOpen   = this.state.drag && this.state.drag.dropIdx === 0;
        const topGapOffset = topGapOpen ? d.gapSize : 0;
        let dropIdx = 0, draggedEl = null;

        for (let i = 0; i < rows.length; i++) {
            if (rows[i].getAttribute('data-keyid') === String(d.keyId)) {
                draggedEl = rows[i]; continue;
            }
            const restingCentre = rows[i].offsetTop - topGapOffset + rows[i].offsetHeight / 2;
            if (pointerContentY < restingCentre) break;
            dropIdx++;
        }

        if (draggedEl) this.positionDraggedEl(draggedEl, d, clientY);

        const cur = this.state.drag;
        if (!cur || cur.keyId !== d.keyId || cur.dropIdx !== dropIdx) {
            this.setState({ drag: { keyId: d.keyId, startIdx: d.startIdx, dropIdx, gapSize: d.gapSize, lockHeight: d.lockHeight } });
        }
    }

    positionDraggedEl(el, d, clientY) {
        const listRect = this.listRef.getBoundingClientRect();
        let top = (clientY - listRect.top) + this.listRef.scrollTop - d.grabOffsetInRow;
        const maxTop = Math.max(0, this.listRef.scrollHeight - d.gapSize);
        if (top < 0) top = 0;
        else if (top > maxTop) top = maxTop;
        el.style.top   = top + 'px';
        el.style.left  = '0px';
        el.style.width = d.anchorWidth + 'px';
        el.style.transform = 'none';
    }

    updateAutoScroll(clientY) {
        const list = this.listRef;
        if (!list) { this.stopAutoScroll(); return; }
        const EDGE_ZONE = 30;
        const rect   = list.getBoundingClientRect();
        const canUp  = list.scrollTop > 0;
        const canDown = list.scrollTop < list.scrollHeight - list.clientHeight - 1;
        const distTop    = clientY - rect.top;
        const distBottom = rect.bottom - clientY;
        if (canUp && distTop < EDGE_ZONE) {
            this.autoScrollDir   = -1;
            this.autoScrollSpeed = 2 + 12 * (1 - Math.max(0, distTop) / EDGE_ZONE);
            this.ensureAutoScrollRunning();
        } else if (canDown && distBottom < EDGE_ZONE) {
            this.autoScrollDir   = 1;
            this.autoScrollSpeed = 2 + 12 * (1 - Math.max(0, distBottom) / EDGE_ZONE);
            this.ensureAutoScrollRunning();
        } else {
            this.stopAutoScroll();
        }
    }

    ensureAutoScrollRunning() {
        if (this.autoScrollRAF == null)
            this.autoScrollRAF = requestAnimationFrame(this.autoScrollTick);
    }

    stopAutoScroll() {
        this.autoScrollDir = 0;
        if (this.autoScrollRAF != null) {
            cancelAnimationFrame(this.autoScrollRAF);
            this.autoScrollRAF = null;
        }
    }

    autoScrollTick() {
        this.autoScrollRAF = null;
        const list = this.listRef, d = this.dragRef;
        if (!list || !d || this.autoScrollDir === 0) return;
        const before = list.scrollTop;
        list.scrollTop = before + this.autoScrollDir * (this.autoScrollSpeed || 6);
        if (list.scrollTop === before) { this.stopAutoScroll(); return; }
        this.updateDragForPointer(d.lastClientY);
        this.updateAutoScroll(d.lastClientY);
    }

    onDragEnd(e) {
        const d = this.dragRef;
        if (!d) return;
        if (e && e.pointerId != null && e.pointerId !== d.pointerId) return;

        this.removeDragListeners();
        this.stopAutoScroll();

        if (this.listRef) {
            const el = this.listRef.querySelector('[data-keyid="' + d.keyId + '"]');
            if (el) { el.style.transform = ''; el.style.top = ''; el.style.left = ''; el.style.width = ''; }
        }
        this.dragRef = null;
        const dropState = this.state.drag;
        this.setState({ drag: null });

        if (!d.started || !dropState) return;
        if (dropState.dropIdx === d.startIdx) return;

        this.applyDropReorder(d.order, d.keyId, dropState.dropIdx);
    }

    componentDidUpdate() {
        if (this.dragRef && this.dragRef.started && this.listRef) {
            const el = this.listRef.querySelector('[data-keyid="' + this.dragRef.keyId + '"]');
            if (el) this.positionDraggedEl(el, this.dragRef, this.dragRef.lastClientY);
        }
    }

    applyDropReorder(order, keyId, dropIdx) {
        const { ship, system } = this.props;

        // Reorder the block list
        const items = order.slice();
        const fromIdx = items.findIndex(b => b.id === keyId);
        if (fromIdx === -1) return;
        const [moved] = items.splice(fromIdx, 1);
        items.splice(dropIdx, 0, moved);

        // Write the new order as an array of IDs
        const newOrder = items.map(b => b.id);
        system.setRepairOrder(newOrder);
        webglScene.customEvent('SystemDataChanged', { ship, system });
    }

    handleReset(e) {
        e.stopPropagation();
        const { ship, system } = this.props;
        system.setRepairOrder([]);
        webglScene.customEvent('SystemDataChanged', { ship, system });
    }

    // ── Render ────────────────────────────────────────────────────────────────

    render() {
        const { ship, readOnly } = this.props;
        const blocks = this.getOrderedBlocks();
        const hasOrder = (this.props.system.repairOrder || []).length > 0;

        return (
            <Container>
                <Header>
                    {readOnly ? 'Structure Repair Order (view only)' : 'Manage Structure Repair'}
                </Header>
                {!readOnly && (
                    <DragHint>Drag rows to set repair priority — top = first repaired</DragHint>
                )}
                <ListContainer
                    ref={el => { this.listRef = el; }}
                    $lockHeight={this.state.drag ? this.state.drag.lockHeight : 0}
                >
                    {blocks.length === 0 && (
                        <CenteredListItem>No structure blocks found</CenteredListItem>
                    )}
                    {blocks.map((block, index) => {
                        const drag = this.state.drag;
                        const isDragging = drag && drag.keyId === block.id;
                        let gapBefore = false, lineAtEnd = false, lineBefore = false;
                        if (drag && !isDragging) {
                            const settledCount = blocks.length - 1;
                            const nonDraggedBefore = index > drag.startIdx ? index - 1 : index;
                            if (drag.dropIdx === 0 && nonDraggedBefore === 0) {
                                gapBefore = true;
                            } else if (drag.dropIdx === settledCount && nonDraggedBefore === settledCount - 1) {
                                lineAtEnd = true;
                            } else if (drag.dropIdx === nonDraggedBefore) {
                                lineBefore = true;
                            }
                        }
                        const damage = block.maxhealth - block.hp;
                        return (
                            <ListItem
                                key={block.id}
                                data-keyid={block.id}
                                $dragging={isDragging}
                                $gapBefore={gapBefore}
                                $lineAtEnd={lineAtEnd}
                                $lineBefore={lineBefore}
                                $gapSize={drag ? drag.gapSize : 0}
                                $readOnly={readOnly}
                                onPointerDown={(e) => this.onRowPointerDown(e, block.id, index, blocks)}
                            >
                                <ItemInfo>
                                    {block.destroyed
                                        ? <DestroyedItemName>{block.displayName}</DestroyedItemName>
                                        : <ItemName>{block.displayName}</ItemName>
                                    }
                                    <ItemStatus>
                                        HP: {block.hp} / {block.maxhealth}
                                        {damage > 0 && (
                                            <><Divider />Dmg: {damage}{block.destroyed ? ' — DESTROYED' : ''}</>
                                        )}
                                    </ItemStatus>
                                </ItemInfo>
                            </ListItem>
                        );
                    })}
                </ListContainer>
                {!readOnly && (
                    <Footer>
                        <ResetButton
                            className="ssr-action-button"
                            onClick={(e) => this.handleReset(e)}
                            title="Clear custom order and return to default (destroyed first, then most damaged)"
                        >
                            {hasOrder ? 'Reset to Default Order' : 'Using Default Order'}
                        </ResetButton>
                    </Footer>
                )}
            </Container>
        );
    }
}

export default StructureSelfRepairList;
