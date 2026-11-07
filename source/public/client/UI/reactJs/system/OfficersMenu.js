import React, { Component } from 'react';
import styled from 'styled-components';
import { SECTION_INK } from './menuControls';

/* OFFICERS_PLAN.md STAGE 10 - THE OFFICERS MENU (user request 2026-10-16).
 *
 * In Initial Orders a system holding an officer who may move (an Expert Gunner; the Matter Weapons Expert, at
 * the C&C or in a weapon) shows this panel: one row per such officer, with MOVE OFFICER - which closes the menu
 * and waits for the player to click the post to move him to (officers.startPick; SystemIcon outlines every post
 * he may take and hands the next click to officers.pickTarget) - or, once he has been ordered, CANCEL MOVE.
 *
 * The order is kept on the ship until the commit carries it (officers.transferClaims); the server checks it
 * against its own rule before writing it (Officers::saveTransfers). Which posts he may take come from the server
 * with him (`targets`), so this file holds no rule of its own. SystemInfoButtons gates it on officers.canManage.
 *
 * In the Officers blue (SECTION_INK.officer - #90b1ee, the Buy dialog's Officers section and the lobby menu's
 * Officers bar), in the shape of the activation menus beside it (activationMenu.js).
 */

const INK = SECTION_INK.officer;

/* Asks for 190px and no more, then stretches to the menu: `contain: inline-size` keeps its text out of the
   shrink-to-fit tooltip's width sum (JumpEngineMenu's note on WIDTH). */
const Panel = styled.div`
    display: flex;
    flex-direction: column;
    width: 100%;
    min-width: 190px;
    contain: inline-size;
    background-color: rgba(16, 26, 38, 0.92);
    border: 1px solid ${INK.rail};
`;

const Header = styled.div`
    padding: 3px;
    background: ${INK.wash};
    border-left: 3px solid ${INK.bar};
    border-bottom: 1px solid ${INK.rail};
    color: ${INK.title};
    text-align: center;
    font-size: 12px;
    font-weight: bold;
    margin-bottom: 2px;
`;

const Row = styled.div`
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    padding: 3px 8px;
    font-size: 11px;
    color: #deebff;
`;

const Note = styled.div`
    padding: 0 8px 5px 8px;
    font-size: 10px;
    line-height: 1.35;
    color: #8ca5c0;
    border-bottom: 1px solid ${INK.rail};

    &:last-child {
        border-bottom: none;
    }
`;

const Button = styled.div`
    padding: 0 6px;
    height: 18px;
    box-sizing: border-box;
    background: ${INK.btnBg};
    border: 1px solid ${INK.rail};
    color: ${INK.btnText};
    cursor: pointer;
    display: flex;
    align-items: center;
    font-size: 10px;
    white-space: nowrap;
    user-select: none;

    &:hover {
        background: ${INK.bar};
        color: #0b1520;
    }
`;

/* Stage 19: an Expert Targeter on a Medium ship's one block picks a SIDE instead of a post - two buttons, the side he will cover
   lit. The other side is a move to his own post (Officers::mayMoveTo, `switchTo`), so it costs the turn as Move Officer does. */
const SideSwitch = styled.div`
    display: flex;
    gap: 2px;
`;

const SideButton = styled(Button)`
    ${props => props.$on && `
        background: ${INK.bar};
        color: #0b1520;
        cursor: default;
    `}
`;

class OfficersMenu extends Component {

    move(e, entry) {
        e.stopPropagation(); e.preventDefault();
        officers.startPick(this.props.ship, entry);
        webglScene.customEvent('CloseSystemInfo');
    }

    cancel(e, entry) {
        e.stopPropagation(); e.preventDefault();
        officers.cancelTransfer(this.props.ship, entry);
    }

    //the side he covers now: cancels a switch; the other side: orders one (to his own post)
    side(e, entry, side, order) {
        e.stopPropagation(); e.preventDefault();
        if (side.current) {
            if (order) officers.cancelTransfer(this.props.ship, entry);
        } else if (!order) {
            officers.setTransfer(this.props.ship, entry, entry.post);
        }
    }

    renderSides(entry, sides, order, i, nextTurn) {
        const other = sides.find(side => !side.current);
        return (
            <React.Fragment key={`${entry.id}-${i}`}>
                <Row>
                    <span>{officers.label(entry.id, this.props.ship)}</span>
                    <SideSwitch>
                        {sides.map(side => {
                            const on = order ? !side.current : side.current;
                            return (
                                <SideButton key={side.id} $on={on} onClick={e => this.side(e, entry, side, order)}
                                    title={on ? `He covers the ${side.name} weapons` : `Cover the ${side.name} weapons instead`}>
                                    {side.name}
                                </SideButton>
                            );
                        })}
                    </SideSwitch>
                </Row>
                <Note>
                    {order
                        ? `Switching to ${other.name}: he covers nothing for the rest of this turn, and the ${other.name} weapons from turn ${nextTurn}.`
                        : 'Switching sides takes the rest of the turn: he covers nothing until the next.'}
                </Note>
            </React.Fragment>
        );
    }

    render() {
        const { ship, system } = this.props;
        const nextTurn = officers.currentTurn() + 1;
        return (
            <Panel>
                <Header>Officers</Header>
                {officers.movableOn(ship, system.id).map((entry, i) => {
                    const order = officers.transferOf(ship, entry);
                    const sides = officers.sideChoices(ship, entry);
                    if (sides) return this.renderSides(entry, sides, order, i, nextTurn);
                    return (
                        <React.Fragment key={`${entry.id}-${i}`}>
                            <Row>
                                <span>{officers.label(entry.id, ship)}</span>
                                {order
                                    ? <Button title="Keep him at this post" onClick={e => this.cancel(e, entry)}>Cancel Move</Button>
                                    : <Button title="Then click the post to move him to" onClick={e => this.move(e, entry)}>Move Officer</Button>}
                            </Row>
                            <Note>
                                {order
                                    ? `To ${officers.systemName(ship, order.to)}: he serves nowhere for the rest of this turn, and there from turn ${nextTurn}.`
                                    : 'An officer who moves serves nowhere for the rest of the turn.'}
                            </Note>
                        </React.Fragment>
                    );
                })}
            </Panel>
        );
    }
}

export default OfficersMenu;
