import React, { Component } from 'react';
import styled from 'styled-components';
import theme from '../styled/theme';
import CriticalEffectsSection, { critRowsFromMap, CritSectionHeader, CheckBox, CheckText } from './CriticalEffectsSection';
import SystemEnhancementsSection from './SystemEnhancementsSection';
import nonPassiveWheel from '../helpers/nonPassiveWheel';
import { ActionButton, ValueInput, SectionDivider } from './menuControls';

/* Enhancement + pre-battle-damage editor for ONE system of a bought lobby ship.
 * Design: PREBATTLE_DAMAGE_PLAN.md §5.2 and WEAPON_ENHANCEMENTS_PLAN.md §6.1.
 * State lives in window.battleDamage and window.systemEnhancements.
 *
 *   ┌ Add Enhancements & Damage ───────────────┐
 *   ├ ✦ ENHANCEMENTS ──────────────────────────┤  gold
 *   │ Gunsights            [-] [ 1 ] [+]   12p │
 *   │                          Refits: 30 pts  │
 *   ╞══════════════════════════════════════════╡  the hard visual break
 *   ├ Damage ──────────────────────────────────┤  blue
 *   │ Twin Array #14  [-] [  8 ] [+] ☐ Destroy │
 *   ├ Critical Effects ────────────────────────┤
 *   │ Output altered by -1      [-] [ 2 ] [+]  │
 *   │ [ + Add effect…            ] ☐ All       │
 *   └──────────────────────────────────────────┘
 *
 * The two halves are deliberately one menu with a hard break, not two: the player is
 * dressing ONE system, and a refit and a wound are both things they are doing to it.
 * ⚠️ That also means they can destroy a system they refitted thirty seconds ago - see
 * refresh(), which is where D11's sweep lives.
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

/* ActionButton and ValueInput now live in ./menuControls, shared with
   SystemEnhancementsSection - a gold variant of the same ticker is one prop, and two
   copies would be two places to fix the next time either is nudged. */

const DestroyLabel = styled.label`
    display: flex;
    align-items: center;
    gap: 3px;
    flex: 0 0 auto;
    cursor: ${props => props.$disabled ? 'not-allowed' : 'pointer'};
    user-select: none;
    opacity: ${props => props.$disabled ? 0.4 : 1};
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

    /* A reactor may be damaged but not killed: destroying it destroys the primary structure
       block, and a ship with no primary structure is a destroyed ship - so it would arrive
       at the battle already dead. battleDamage.isIndestructible is the shared answer (PHP
       twin: PreBattleDamage::isIndestructible) and battleDamage.setSystem clamps the write
       regardless; this is what stops the UI OFFERING the state. */
    isIndestructible() {
        return battleDamage.isIndestructible(this.props.system);
    }

    /* The lowest structure this system may be left on: 1 for a reactor, 0 (destroyed) for
       everything else. */
    floor() {
        return this.isIndestructible() ? 1 : 0;
    }

    /* Write remaining health back as damage. Reaching 0 means destroyed, always. */
    setRemaining(value) {
        const { ship, system } = this.props;
        const max = system.maxhealth;
        const floor = Math.min(this.floor(), max);

        let remaining = parseInt(value, 10);
        if (isNaN(remaining)) remaining = max;
        remaining = Math.max(floor, Math.min(max, remaining));

        const damage = max - remaining;
        const destroyed = remaining === 0 && max > 0;

        //Typing/ticking down to 0 destroys the system, so remember what it was on the way
        //past - unticking Destroy afterwards should still return to the dialled-in value.
        if (destroyed) this.rememberHealth();

        battleDamage.setSystem(ship, system.id, { d: damage, k: destroyed ? 1 : 0 });
        this.refresh();
    }

    /* Stash the current remaining health, unless it is already 0/destroyed (that would
       overwrite the value we are trying to preserve).

       ⚠️ Kept on the SHIP, keyed by system id (battleDamage.rememberHealth), NEVER on this
       component. UIManager.showSystemInfoMenu re-renders the same #systemInfoReact root, so
       clicking a second system REUSES this instance with new props - React reconciles
       same-type elements in place rather than remounting - and an instance field leaked
       from one system to the next: destroy a 36-box Structure, destroy an 8-box gun, untick
       the Structure, and it came back with EIGHT (user report 2026-08-08, Vree Xonn). */
    rememberHealth() {
        const { ship, system } = this.props;
        if (this.isDestroyed()) return;
        battleDamage.rememberHealth(ship, battleDamage.KIND_SYSTEM, system.id, this.remaining());
    }

    setDestroyed(on) {
        const { ship, system } = this.props;

        //The checkbox is disabled for these, so this is belt-and-braces against a future
        //caller; battleDamage.setSystem would clamp the write in any case.
        if (on && this.isIndestructible()) return;

        if (on) {
            this.rememberHealth();
            battleDamage.setSystem(ship, system.id, { d: system.maxhealth, k: 1 });
            this.refresh();
            return;
        }

        //Un-destroying restores the health the player had dialled in before ticking
        //Destroy; full health only when they never amended it. Clamped to THIS system's
        //maxhealth, so even a stale value can never invent structure it does not have.
        const remembered = battleDamage.healthMemory(ship, battleDamage.KIND_SYSTEM, system.id);
        const restored = remembered > 0
            ? Math.min(system.maxhealth, remembered)
            : system.maxhealth;

        battleDamage.setSystem(ship, system.id, { d: system.maxhealth - restored, k: 0 });
        this.refresh();
    }

    /* ⭐ EVERY damage, critical and enhancement write funnels through here, which is why
       D11's sweep lives here rather than in each writer. */
    refresh() {
        const { ship } = this.props;
        battleDamage.applyToShip(ship);

        /* D11: a refit on a system that has just been destroyed is REMOVED and REFUNDED,
           one way. Deliberately AFTER applyToShip, never before and never off the payload's
           `k` flags: applyToShip is what writes the STRUCTURE CASCADE (destroying a
           Structure block destroys every system in its location), so sweeping any earlier
           catches only the box the player clicked.
           The refund has to be visible in the same tick - a refund the player does not see
           reads as a pricing bug - so calculateFleet re-runs and one warning names what
           went. Un-ticking Destroy does NOT bring it back; the wording says "removed", not
           "suspended", so that is not a surprise. */
        let removed = [];
        if (window.systemEnhancements) {
            const bucketBefore = parseFloat(ship.pointCostSysEnh) || 0;
            removed = systemEnhancements.dropDestroyed(ship);   //recomputes pointCostSysEnh
            if (removed.length) this.settleRefitCost(ship, bucketBefore);
        }

        //Repaints icons, health bars and section headers of every open ship window.
        if (window.shipWindowManagerReact) window.shipWindowManagerReact.update();
        //Lobby only: keep the fleet-list row's broken-heart badge in step as we edit.
        if (window.gamedata && typeof gamedata.refreshDamagedBadge === 'function') {
            gamedata.refreshDamagedBadge(ship);
        }
        this.forceUpdate();

        if (removed.length && window.confirm && typeof confirm.warning === 'function') {
            confirm.warning(systemEnhancements.describeRemoved(removed));
        }
    }

    /* ⭐ THE one place ship.pointCost is moved when the refit bucket changes.

       The invariant every lobby path maintains is
           pointCost = bare hull + pointCostEnh + pointCostEnh2 + pointCostSysEnh
       so when the third bucket moves, pointCost moves by exactly the same amount. Applying
       the DELTA rather than recomputing the total is what makes this safe from here: a
       recompute would have to peel the buy dialog's arithmetic back apart (flight scaling,
       bulk counts), which getPristinePointCost only manages because it has a blueprint to
       fall back on. Pass the bucket value read BEFORE the write. */
    settleRefitCost(ship, bucketBefore) {
        const bucketAfter = parseFloat(ship.pointCostSysEnh) || 0;
        ship.pointCost = (parseFloat(ship.pointCost) || 0) - (bucketBefore - bucketAfter);

        //Panel and fleet-list row move in the SAME tick as the points - a refund the player
        //does not see reads as a pricing bug.
        if (window.gamedata && typeof gamedata.calculateFleet === 'function') {
            gamedata.calculateFleet();
        }
    }

    /* Buy / un-buy one refit on this system.
       REFUSES and restores the previous count rather than letting the fleet go over budget
       (§5.2), because there is no dialog here to cancel out of - the write has already
       landed on the ship by the time we can ask. */
    setEnhancement(enhID, count) {
        const { ship, system } = this.props;
        if (!window.systemEnhancements) return;

        const bucketBefore = parseFloat(ship.pointCostSysEnh) || 0;
        const previous = systemEnhancements.taken(ship, system.id, enhID);
        if (count === previous) return;

        systemEnhancements.set(ship, system.id, enhID, count);
        this.settleRefitCost(ship, bucketBefore);

        const affordable = !window.gamedata
            || typeof gamedata.canAffordRefit !== 'function'
            || gamedata.canAffordRefit(ship);

        if (!affordable) {
            //Put it back exactly as it was, points included, then say so.
            const revertFrom = parseFloat(ship.pointCostSysEnh) || 0;
            systemEnhancements.set(ship, system.id, enhID, previous);
            this.settleRefitCost(ship, revertFrom);
            systemEnhancements.apply(ship);
            this.forceUpdate();
            if (window.confirm && typeof confirm.error === 'function') {
                confirm.error("You cannot afford that enhancement!", function () { });
            }
            return;
        }

        systemEnhancements.apply(ship);
        this.refresh();
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
        const indestructible = this.isIndestructible();
        const entry = this.entry();
        const critRows = critRowsFromMap(
            entry.c, ship.preBattleCritDesc, ship.preBattleCritTransient, entry.p);

        /* The gold half. Empty on most systems of most hulls, on every system of an
           Ancient hull, and - by D11 - on a destroyed one, in which case
           SystemEnhancementsSection renders nothing at all and the menu is the damage
           editor it has always been. */
        const enhRows = (window.systemEnhancements && !destroyed)
            ? systemEnhancements.menuRowsFor(ship, system)
            : [];

        return (
            <Container onClick={e => e.stopPropagation()}>
                <Header>{enhRows.length > 0
                    ? "Add Enhancements & Damage"
                    : "Apply Damage & Critical Effects"}</Header>

                <SystemEnhancementsSection
                    rows={enhRows}
                    onChange={(enhID, count) => this.setEnhancement(enhID, count)}
                />
                {enhRows.length > 0 && <SectionDivider />}

                <CritSectionHeader>Damage</CritSectionHeader>

                <Row>
                    <RowLabel title={`${system.displayName || system.name} (system id ${system.id})`}>
                        <SystemName>{system.displayName || system.name}</SystemName>
                        <MaxText>#{system.id}</MaxText>
                    </RowLabel>
                    <ActionButton
                        title={indestructible && remaining <= 1
                            ? "A reactor cannot be destroyed before the battle"
                            : "More damage"}
                        disabled={destroyed || remaining <= this.floor()}
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
                    {/* No Destroy for a reactor - the ship would arrive already dead. The
                        label is kept (greyed, explained on hover) rather than removed, so
                        the row does not reflow between systems as the player clicks about. */}
                    <DestroyLabel
                        $on={destroyed}
                        $disabled={indestructible}
                        title={indestructible
                            ? "A reactor cannot be destroyed before the battle: losing it destroys the primary structure, which destroys the ship"
                            : "Mark this system destroyed before the battle starts"}
                    >
                        <CheckBox
                            type="checkbox"
                            checked={destroyed}
                            disabled={indestructible}
                            onChange={e => this.setDestroyed(e.target.checked)}
                        />
                        <CheckText>Destroy</CheckText>
                    </DestroyLabel>
                </Row>

                {/* Criticals can be added, amended or removed here. The picker's contents
                    come from the per-class catalogue (systemCriticals.php), which
                    CriticalEffectsSection fetches for itself. */}
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
