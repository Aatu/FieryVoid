import React, { Component } from 'react';
import styled from 'styled-components';
import theme from '../styled/theme';
import CriticalEffectsSection, { critRowsFromMap, CritSectionHeader, CheckBox, CheckText } from './CriticalEffectsSection';
import nonPassiveWheel from '../helpers/nonPassiveWheel';

/* Pre-battle damage editor for ONE system of a bought lobby ship.
 * Design: PREBATTLE_DAMAGE_PLAN.md §5.2. State lives in window.battleDamage.
 *
 *   ┌ Apply Damage & Critical Effects ─────────┐
 *   ├ Damage ──────────────────────────────────┤
 *   │ Twin Array #14  [-] [  8 ] [+] ☐ Destroy │
 *   ├ Critical Effects ────────────────────────┤
 *   │ Output altered by -1      [-] [ 2 ] [+]  │
 *   │ [ + Add effect…            ] ☐ All       │
 *   └──────────────────────────────────────────┘
 *
 * The damage row NAMES THE SYSTEM (user request 2026-08-08) rather than saying
 * "Structure / 8": with several menus open, or on a ship carrying six Twin Arrays, the
 * only thing that told them apart was which icon you happened to have clicked. The id is
 * shown beside it because it is what the payload, tac_damage and the ship blueprint are
 * all keyed by, so a report can name a system unambiguously.
 *
 * The field shows REMAINING HEALTH, not damage — that is the number on the SCS the
 * player is looking at. Wheel up heals, wheel down damages. Reaching 0 ticks Destroy,
 * because a system with no structure left IS destroyed (the payload enforces the same
 * invariant server-side, so preview and game agree).
 */

/* ⚠️ max-width is LOAD-BEARING, not cosmetic. This menu's only ancestor is
   SystemInfoMenu's absolutely-positioned tooltip, which is SHRINK-TO-FIT (capped at
   500px) - so every descendant's max-content width feeds straight back into how wide the
   menu draws. The critical picker's <select> takes its intrinsic width from its LONGEST
   OPTION, so ticking "All" (which swaps a handful of hit-chart entries for every storable
   critical in the game) stretched the whole menu to the 500px ceiling (user report
   2026-08-08). A max-width here clamps the max-content contribution at source, which is
   the only thing that reins a <select> in: flex:1 / min-width:0 on the select itself let
   it SHRINK but do not stop it ASKING. */
const Container = styled.div`
    display: flex;
    flex-direction: column;
    margin-top: 0px;
    width: 100%;
    min-width: 200px;
    max-width: 300px;
    box-sizing: border-box;
    opacity: 0.95;
    background-color: rgba(16, 26, 38, 0.9);
    border: 1px solid ${theme.colors.line};
`;

const Header = styled.div`
    padding: 3px;
    background-color: #215a7a;
    border-bottom: 1px solid ${theme.colors.line};
    color: ${theme.colors.chromeText};
    text-align: center;
    font-size: 12px;
    opacity: 1 !important;
    font-weight: bold;
`;

const Row = styled.div`
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 4px 6px;
    font-size: 11px;
    color: ${theme.colors.chromeText};
`;

const RowLabel = styled.div`
    flex: 1;
    min-width: 0;
    user-select: none;
    display: flex;
    align-items: baseline;
    gap: 4px;
`;

const ActionButton = styled.div`
    width: 24px;
    height: 18px;
    flex: 0 0 24px;
    background: #203348;
    border: 1px solid #496791;
    color: #deebff;
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 10px;
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
`;

const ValueInput = styled.input`
    flex: 0 0 44px;
    width: 44px;
    height: 18px;
    box-sizing: border-box;
    padding: 0;
    text-align: center;
    font-family: ${theme.fonts.mono};
    font-size: 12px;
    color: ${props => props.$destroyed ? '#ff8a80' : '#ffffff'};
    background-color: #101a26;
    border: 1px solid #496791;
    outline: none;

    &:focus {
        border-color: #6089c1;
    }

    &:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
`;

const DestroyLabel = styled.label`
    display: flex;
    align-items: center;
    gap: 3px;
    flex: 0 0 auto;
    cursor: pointer;
    user-select: none;
    color: ${props => props.$on ? '#ff8a80' : theme.colors.textDim};
`;

const MaxText = styled.span`
    flex: 0 0 auto;
    color: ${theme.colors.textDim};
    font-size: 10px;
`;

/* The system's name is the row label now, and a long one ("Antimatter Shredder") must not
   push the ticker off the menu. */
const SystemName = styled.span`
    flex: 1 1 auto;
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
`;

class ApplyDamageMenu extends Component {

    constructor(props) {
        super(props);
        //Built once, not in render(): a ref callback rebuilt every pass would detach and
        //reattach the native listener on every keystroke. See helpers/nonPassiveWheel.
        this.wheelRef = nonPassiveWheel(e => this.step(e.deltaY < 0 ? 1 : -1));
        //Health the player had dialled in before ticking Destroy, so unticking gives it
        //back instead of silently resetting to full (user report 2026-08-08). Lives on the
        //component rather than in the payload: it is UI undo state, not fleet state, and
        //the wire format must keep meaning exactly one thing.
        this.healthBeforeDestroy = null;
    }

    entry() {
        const { ship, system } = this.props;
        return battleDamage.getEntry(ship, battleDamage.KIND_SYSTEM, system.id) || {};
    }

    /* Remaining health currently represented by the payload. */
    remaining() {
        const { system } = this.props;
        const entry = this.entry();
        if (entry.k) return 0;
        return Math.max(0, system.maxhealth - (parseInt(entry.d, 10) || 0));
    }

    isDestroyed() {
        return Boolean(this.entry().k);
    }

    /* Write remaining health back as damage. Reaching 0 means destroyed, always. */
    setRemaining(value) {
        const { ship, system } = this.props;
        const max = system.maxhealth;

        let remaining = parseInt(value, 10);
        if (isNaN(remaining)) remaining = max;
        remaining = Math.max(0, Math.min(max, remaining));

        const damage = max - remaining;
        const destroyed = remaining === 0 && max > 0;

        //Typing/ticking down to 0 destroys the system, so remember what it was on the way
        //past - unticking Destroy afterwards should still return to the dialled-in value.
        if (destroyed) this.rememberHealth();

        battleDamage.setSystem(ship, system.id, { d: damage, k: destroyed ? 1 : 0 });
        this.refresh();
    }

    /* Stash the current remaining health, unless it is already 0/destroyed (that would
       overwrite the value we are trying to preserve). */
    rememberHealth() {
        if (this.isDestroyed()) return;
        const remaining = this.remaining();
        this.healthBeforeDestroy = remaining > 0 ? remaining : null;
    }

    setDestroyed(on) {
        const { ship, system } = this.props;

        if (on) {
            this.rememberHealth();
            battleDamage.setSystem(ship, system.id, { d: system.maxhealth, k: 1 });
            this.refresh();
            return;
        }

        //Un-destroying restores the health the player had dialled in before ticking
        //Destroy; full health only when they never amended it.
        const restored = (this.healthBeforeDestroy !== null && this.healthBeforeDestroy > 0)
            ? Math.min(system.maxhealth, this.healthBeforeDestroy)
            : system.maxhealth;

        battleDamage.setSystem(ship, system.id, { d: system.maxhealth - restored, k: 0 });
        this.refresh();
    }

    refresh() {
        const { ship } = this.props;
        battleDamage.applyToShip(ship);
        //Repaints icons, health bars and section headers of every open ship window.
        if (window.shipWindowManagerReact) window.shipWindowManagerReact.update();
        //Lobby only: keep the fleet-list row's broken-heart badge in step as we edit.
        if (window.gamedata && typeof gamedata.refreshDamagedBadge === 'function') {
            gamedata.refreshDamagedBadge(ship);
        }
        this.forceUpdate();
    }

    step(direction) {
        this.setRemaining(this.remaining() + direction);
    }

    onInput(e) {
        const digits = String(e.target.value).replace(/[^0-9]/g, '');
        this.setRemaining(digits === '' ? 0 : parseInt(digits, 10));
    }

    render() {
        const { ship, system } = this.props;
        if (!system || !(system.maxhealth > 0)) return null;

        const remaining = this.remaining();
        const destroyed = this.isDestroyed();
        const entry = this.entry();
        const critRows = critRowsFromMap(
            entry.c, ship.preBattleCritDesc, ship.preBattleCritTransient, entry.p);

        return (
            <Container onClick={e => e.stopPropagation()}>
                <Header>Apply Damage & Critical Effects</Header>

                <CritSectionHeader>Damage</CritSectionHeader>

                <Row>
                    <RowLabel title={`${system.displayName || system.name} (system id ${system.id})`}>
                        <SystemName>{system.displayName || system.name}</SystemName>
                        <MaxText>#{system.id}</MaxText>
                    </RowLabel>
                    <ActionButton
                        title="More damage"
                        disabled={destroyed}
                        onClick={() => this.step(-1)}
                    >&minus;</ActionButton>
                    {/*ref, not onWheel: React's own wheel listener is passive, so
                       preventDefault there cannot stop the page scrolling behind the menu.*/}
                    <ValueInput
                        ref={this.wheelRef}
                        type="text"
                        $destroyed={destroyed}
                        disabled={destroyed}
                        value={destroyed ? 0 : remaining}
                        onChange={e => this.onInput(e)}
                    />
                    <ActionButton
                        title="Repair"
                        disabled={destroyed || remaining >= system.maxhealth}
                        onClick={() => this.step(1)}
                    >+</ActionButton>
                    <DestroyLabel $on={destroyed} title="Mark this system destroyed before the battle starts">
                        <CheckBox
                            type="checkbox"
                            checked={destroyed}
                            onChange={e => this.setDestroyed(e.target.checked)}
                        />
                        <CheckText>Destroy</CheckText>
                    </DestroyLabel>
                </Row>

                {/* Carried criticals can be AMENDED or REMOVED here (2026-08-08); adding
                    one from nothing still needs the §11 catalogue and is not offered. */}
                <CriticalEffectsSection
                    ship={ship}
                    kind={battleDamage.KIND_SYSTEM}
                    reference={system.id}
                    rows={critRows}
                    editable
                    onChange={() => this.refresh()}
                />
            </Container>
        );
    }
}

export default ApplyDamageMenu;
