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
 */

/* ⚠️ WIDTH. SystemInfoMenu's tooltip is shrink-to-fit, so the longest unbroken run of text in here
   is what decides how wide the whole system menu draws - and this panel sits in a tooltip beside a
   system icon, not in a dialog. The first cut listed every system the toggle would shut down and
   pushed the menu to roughly twice the width of the icon row next to it (user report 2026-08-22).
   The note now states the RULE instead of enumerating the ship, and the panel is capped. Keep any
   future line short enough to wrap inside this. */
const Panel = styled(Container)`
    min-width: 0;
    width: 190px;
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

    render() {
        const { system } = this.props;
        const isMaintaining = system.isMaintainingVortex();

        return (
            <Panel>
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
                    {isMaintaining
                        ? 'Held open. All powered systems except the Scanner are shut down this turn.'
                        : 'Closes at end of turn unless maintained. Shuts down all powered systems except the Scanner.'}
                </Note>
            </Panel>
        );
    }
}

export default JumpEngineMenu;
