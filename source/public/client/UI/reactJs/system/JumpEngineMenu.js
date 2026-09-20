import React, { Component } from 'react';
import styled from 'styled-components';
import { Container, Header, Row, Label, Controls, Note, ActionButton } from './activationMenu';

/* JUMP_POINTS_PLAN.md STAGE 5 - THE MAINTAIN CONTROL.
 *
 * A jump vortex closes at the end of every turn unless its holder declares Maintain in that turn's
 * Initial Orders (plan section 2.3/2.4). This is where that declaration is made.
 *
 * ⭐ WHY A MENU AND NOT A MAP GESTURE. The first cut had the player re-target the vortex's own hex
 * with the Jump Engine every turn. It worked and it produced the right order, but it asked the
 * player to repeat a targeting ritual for what is really a switch that is either on or off - and it
 * could not do the other half of the rule, which is taking the ship dark. This control does both in
 * one click, which is the only way the two halves can be kept together.
 *
 * The order it produces is unchanged: firing mode 7 at the vortex's own hex, validated by
 * Firing::getVortexDeclarationBlock and read at end of turn by JumpEngine::getMaintainDeclaration.
 * The server knows nothing about this menu.
 *
 * The Vorlon Power Capacitor's Double Recharge is the same shape and the same chassis - pick a
 * mode in Initial Orders, and the ship's systems are shut down to pay for it.
 *
 * WHEN IT APPEARS: JumpEngine.canMaintainVortex() decides, and SystemInfoButtons gates on it. It is
 * deliberately absent on the turn the vortex was declared (it has not formed yet) and on the turn
 * the four-turn cap closes it (maintaining could not change the outcome).
 *
 * ⭐⭐ HYPERSPACE_IMPROVEMENTS_PLAN.md §4 (Stage H3) - EXCEPT ON A CAPACITOR-FED VORLON DRIVE, which
 * has no four-turn cap and so has no last turn: the toggle stays offered for as long as the jump
 * point is paid for. Its half of the rule is an UPKEEP out of the Power Capacitor rather than the
 * all-systems-dark shutdown, so this control shuts nothing down on one and restores nothing - see
 * the note text below, and JumpEngine::$vortexUpkeep for the three rulings behind it.
 *
 * ⭐ WALKERS_OF_SIGMA_PLAN.md §3.18 (Stage 20) - AND THE ABDUCTION PANEL. A Walker drive holding an
 * abduction order this turn shows its target, the power level (an Extra-Dimensional Jump Drive steps
 * it; a supporting drive's is fixed at double), what the level costs the reactor, and the cost and
 * progress the server published. The ORDER is still the single source of truth - the level is its
 * firing mode - so the panel survives a poll and a reload with nothing of its own to synchronise.
 * The two sections never meet on one engine: a Walker drive is a legacy drive and holds no vortex.
 */

/* ⚠️ WIDTH. SystemInfoMenu's tooltip is shrink-to-fit, so the longest unbroken run of text in here
   is what decides how wide the whole system menu draws - and this panel sits in a tooltip beside a
   system icon, not in a dialog. The first cut listed every system the toggle would shut down and
   pushed the menu to roughly twice the width of the icon row next to it (user report 2026-08-22).
   The note now states the RULE instead of enumerating the ship.
   ⭐ The panel asks for 190px and nothing more (`contain: inline-size` keeps its text out of the
   tooltip's width sum), then stretches to whatever the menu is - so it shares the width of the Power
   Settings panel above it instead of being a fixed 190px beside a wider one (user request 2026-09-13,
   WALKERS §3.18). */
const Panel = styled(Container)`
    width: 100%;
    min-width: 190px;
    contain: inline-size;
`;

/* WALKERS §3.18 (Stage 20) - THE ABDUCTION PANEL IS PURPLE, the exotic-ability palette of the Hyach
   Computer and Specialists menus (and SystemActivation's $isPurple), and the colour the abduction's
   map marker and tooltip line already use (user request 2026-09-13). Standalone components rather
   than overrides of activationMenu's blue ones, so no blue hover or disabled rule can show through. */
const PURPLE = {
    surface: 'rgba(32, 0, 32, 0.9)',
    line: '#5d3564',
    accent: '#7c4686',
    hover: 'rgba(75, 43, 81, 0.6)',
    text: '#f2f2f2',
    textDim: '#d8b9e6',
};

const AbductionPanel = styled(Panel)`
    background-color: ${PURPLE.surface};
    border: 1px solid ${PURPLE.line};
`;

const AbductionHeader = styled.div`
    padding: 3px;
    background-color: ${PURPLE.line};
    border: 1px solid ${PURPLE.accent};
    color: ${PURPLE.text};
    text-align: center;
    font-size: 12px;
    margin-bottom: 2px;
    font-weight: bold;
`;

const AbductionRow = styled.div`
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 3px 8px;
    border-bottom: 1px solid ${PURPLE.line};
    font-size: 11px;
    color: ${PURPLE.text};

    &:hover {
        background-color: ${PURPLE.hover};
    }
`;

const AbductionNote = styled.div`
    padding: 2px 8px 5px 8px;
    font-size: 10px;
    line-height: 1.35;
    color: ${PURPLE.textDim};
`;

//$wide: a worded button (CANCEL) sizes to its label instead of the 24px square of a -/+ step.
const AbductionButton = styled.div`
    min-width: 24px;
    width: ${props => props.$wide ? 'auto' : '24px'};
    padding: ${props => props.$wide ? '0 6px' : '0'};
    height: 18px;
    box-sizing: border-box;
    background: ${PURPLE.line};
    border: 1px solid ${PURPLE.accent};
    color: ${PURPLE.text};
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 10px;
    opacity: 0.9;
    user-select: none;

    &:hover {
        background: #5e3666;
        border: 1px solid #9a5aa6;
        color: #ffffff;
        opacity: 1;
    }

    ${props => props.$active && `
        background: ${PURPLE.accent};
        border: 1px solid #a06daa;
        color: #ffffff;
        opacity: 1;
        cursor: default;
        &:hover { background: ${PURPLE.accent}; border: 1px solid #a06daa; }
    `}

    ${props => props.disabled && `
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #4b2b51; color: ${PURPLE.textDim}; border: 1px solid ${PURPLE.accent}; }
    `}
`;

class JumpEngineMenu extends Component {

    handleActivate() {
        if (!this.props.system.canActivate()) return;
        this.props.system.doActivate();
        this.forceUpdate();
    }

    handleDeactivate() {
        if (!this.props.system.canDeactivate()) return;
        this.props.system.doDeactivate();
        this.forceUpdate();
    }

    //Stage 20 - step the abduction's power level. Only in Initial Orders, only on the owner's screen.
    handlePower(delta) {
        const { ship, system } = this.props;
        if (!canEditAbduction(ship, system)) return;
        const order = system.getAbductionOrder();
        if (!system.setAbductionPowerLevel(system.getAbductionPowerLevel(order) + delta)) return;
        //The reactor balance, the icon and every open window read the order - one event redraws them.
        webglScene.customEvent('SystemDataChanged', { ship: system.getOwningUnit() || ship, system: system });
        this.forceUpdate();
    }

    /* Stage 20 - withdraw the abduction. The generic remove button cannot do it in Initial Orders:
       weaponManager.hasFiringOrder counts a phase-1 order only on a BALLISTIC weapon, and a Walker drive
       is a legacy one. removeFiringOrder clears this drive's orders and fires SystemDataChanged, which
       redraws the marker and the reactor balance. */
    handleCancel() {
        const { ship, system } = this.props;
        if (gamedata.gamephase !== 1 || !gamedata.isMyShip(system.getOwningUnit() || ship)) return;
        weaponManager.removeFiringOrder(system.getOwningUnit() || ship, system);
        this.forceUpdate();
    }

    renderMaintain() {
        const { system } = this.props;
        const isMaintaining = system.isMaintainingVortex();
        /* ⭐⭐ HYPERSPACE_IMPROVEMENTS_PLAN.md §4 (Stage H3) - THE VORLON DRIVE'S NOTE IS A DIFFERENT
           RULE, not a softer wording of the same one. A capacitor-fed drive pays its powerReq out of
           the Power Capacitor for every turn the jump point is held (R4) and has no four-turn limit
           (R5), so neither half of the standard note is true for it: nothing is shut down, and it
           does not close on its own. The cost is stated because the reactor balance reserves it and
           the player has to leave the power for it. */
        const upkeep = system.chargesVortexUpkeep() ? system.getVortexUpkeepCost() : 0;

        return (
            <React.Fragment>
                <Header>Jump Point</Header>
                <Row>
                    <Label>Maintain Vortex</Label>
                    <Controls>
                        <ActionButton
                            onClick={() => this.handleDeactivate()}
                            disabled={!system.canDeactivate()}
                            $active={!isMaintaining}
                        >OFF</ActionButton>
                        <ActionButton
                            onClick={() => this.handleActivate()}
                            disabled={!system.canActivate()}
                            $active={isMaintaining}
                            $variant="activate"
                        >ON</ActionButton>
                    </Controls>
                </Row>
                <Note>
                    {upkeep > 0
                        ? (isMaintaining
                            ? 'Held open. ' + upkeep + ' power is drawn from the Power Capacitor at end of turn. No turn limit while it is paid.'
                            : 'Closes at end of turn unless maintained. Costs ' + upkeep + ' power from the Power Capacitor on each turn it is used - no systems are shut down, and nothing is drawn on a turn it is idle.')
                        : (isMaintaining
                            ? 'Held open. All powered systems except the Scanner are shut down this turn.'
                            : 'Closes at end of turn unless maintained. Shuts down all powered systems except the Scanner.')}
                </Note>
            </React.Fragment>
        );
    }

    renderAbduction() {
        const { ship, system } = this.props;
        const order = system.getAbductionOrder();
        const target = gamedata.getShip(order.targetid);
        const level = system.getAbductionPowerLevel(order);
        const editable = canEditAbduction(ship, system);
        const edjd = system.isExtraDimensional();
        const max = system.abductionMaxPower || 1;
        const powerTurns = window.JumpEngine.formatAbductionHalves(system.getAbductionHalves(order));

        const chain = window.JumpEngine.getAbductionChain(order.targetid);
        const preview = window.JumpEngine.getAbductionCostPreview(order.targetid);
        //D64: with no abduction standing against the target this turn only TAKES HOLD - no power, no Power row.
        const powered = system.isAbductionPowered(order);

        let note;
        if (powered) {
            note = powerTurns + ' power-turn' + (powerTurns === '1' ? '' : 's') + ' for ' + system.getAbductionPowerDraw() + ' power. '
                + 'So far ' + window.JumpEngine.formatAbductionHalves(chain.total) + ' of ' + chain.cost + ' power-turns.';
            //D65: the field and EW conditions were for taking hold only - nothing to meet from here on.
        } else {
            /* §3.18b: terrain does not move, and a multi-hex asteroid or a moon needs its WHOLE footprint
               inside the field rather than one hex - which is a rule the player has to be told before
               spending a turn taking hold. Mirrors EdjdAbduction::getConditionBlock's two messages. */
            const terrain = Boolean(target) && gamedata.isTerrain(target.shipSizeClass, target.userid);
            const multiHex = Boolean(target) && (((target.hexOffsets || []).length > 0) || target.Huge > 0);
            const condition = terrain
                ? 'Targeting: takes hold if ' + (multiHex ? 'every hex it occupies is' : 'it is')
                  + ' inside your connected field and your OEW beats its DEW.'
                : 'Targeting: takes hold if the target ends its move in your connected field and your OEW beats its DEW.';

            note = condition
                + ' Power can be applied from next turn'
                + (preview !== null ? ', and ' + preview + ' power-turns will abduct it.' : '.');
        }

        return (
            <AbductionPanel>
                <AbductionHeader>Abduction{target ? ': ' + target.name : ''}</AbductionHeader>
                {powered && (
                    <AbductionRow>
                        <Label>{edjd ? 'Power' : 'Double power'}</Label>
                        {edjd ? (
                            <Controls>
                                <AbductionButton
                                    onClick={() => this.handlePower(-1)}
                                    disabled={!editable || level <= 1}
                                >-</AbductionButton>
                                <AbductionButton $active>{'x' + level}</AbductionButton>
                                <AbductionButton
                                    onClick={() => this.handlePower(1)}
                                    disabled={!editable || level >= max}
                                >+</AbductionButton>
                            </Controls>
                        ) : null}
                    </AbductionRow>
                )}
                {gamedata.gamephase === 1 && gamedata.isMyShip(system.getOwningUnit() || ship) && (
                    <AbductionRow>
                        <Label>Declaration</Label>
                        <Controls>
                            <AbductionButton $wide onClick={() => this.handleCancel()}>CANCEL</AbductionButton>
                        </Controls>
                    </AbductionRow>
                )}
                <AbductionNote>{note}</AbductionNote>
            </AbductionPanel>
        );
    }

    render() {
        const { system } = this.props;
        const showMaintain = system.canMaintainVortex() || system.canDeactivate();
        const showAbduction = typeof system.getAbductionOrder === 'function' && Boolean(system.getAbductionOrder());

        //The two never meet on one engine (a Walker drive holds no vortex), so each brings its own panel.
        return (
            <React.Fragment>
                {showMaintain && <Panel>{this.renderMaintain()}</Panel>}
                {showAbduction && this.renderAbduction()}
            </React.Fragment>
        );
    }
}

//The level may change while the order is still the owner's to change: Initial Orders, their own unit.
const canEditAbduction = (ship, system) => gamedata.gamephase === 1
    && gamedata.isMyShip(system.getOwningUnit() || ship)
    && system.isExtraDimensional()
    && system.isAbductionPowered();   //D64: nothing to step on a targeting turn

export default JumpEngineMenu;
