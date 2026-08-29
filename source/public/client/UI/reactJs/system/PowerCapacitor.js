import React, { Component } from 'react';
import { Container, Header, Row, Label, Controls, ActionButton } from './activationMenu';

/* The Vorlon Power Capacitor's two switches. Its styling used to live here; it now comes from
   activationMenu.js, which was lifted out of this file when the Jump Engine's Maintain control
   (JUMP_POINTS_PLAN.md Stage 5) needed the same panel. See that file for why the two are meant to
   look identical. (The unused `Value` styled-div was dropped in the move - nothing referenced it.) */

class PowerCapacitor extends Component {

    handleBoost() {
        if (this.canBoost()) {
            shipManager.power.clickPlus(this.props.ship, this.props.system);
            this.forceUpdate();
            webglScene.customEvent('SystemDataChanged', { ship: this.props.ship, system: this.props.system });
        }
    }

    handleDeBoost() {
        if (this.canDeBoost()) {
            shipManager.power.clickMinus(this.props.ship, this.props.system);
            this.forceUpdate();
            webglScene.customEvent('SystemDataChanged', { ship: this.props.ship, system: this.props.system });
        }
    }

    handleActivate() {
        if (this.canActivate()) {
            this.props.system.doActivate();
            this.forceUpdate();
            webglScene.customEvent('SystemDataChanged', { ship: this.props.ship, system: this.props.system });
        }
    }

    handleDeactivate() {
        if (this.canDeactivate()) {
            this.props.system.doDeactivate();
            this.forceUpdate();
            webglScene.customEvent('SystemDataChanged', { ship: this.props.ship, system: this.props.system });
        }
    }

    canBoost() {
        const { ship, system } = this.props;
        return system.boostable && gamedata.gamephase === 1 && shipManager.power.canBoost(ship, system);
    }

    canDeBoost() {
        const { ship, system } = this.props;
        return gamedata.gamephase === 1 && Boolean(shipManager.power.getBoost(system));
    }

    canActivate() {
        return this.props.system.canActivate();
    }

    canDeactivate() {
        return this.props.system.canDeactivate();
    }

    render() {
        const { ship, system } = this.props;
        const boostLevel = shipManager.power.getBoost(system);
        const isActive = system.active;

        return (
            <Container>
                <Header>Power Capacitor</Header>
                {system.boostable &&
                    <Row>
                        <Label>Open Petals</Label>
                        <Controls>
                            <ActionButton onClick={() => this.handleDeBoost()} disabled={!this.canDeBoost()} $active={boostLevel === 0}>OFF</ActionButton>
                            <ActionButton onClick={() => this.handleBoost()} disabled={!this.canBoost()} $active={boostLevel > 0} $variant="activate">ON</ActionButton>
                        </Controls>
                    </Row>
                }
                <Row>
                    <Label>Double Recharge</Label>
                    <Controls>
                        <ActionButton onClick={() => this.handleDeactivate()} disabled={!this.canDeactivate()} $active={!isActive}>OFF</ActionButton>
                        <ActionButton onClick={() => this.handleActivate()} disabled={!this.canActivate()} $active={isActive} $variant="activate">ON</ActionButton>
                    </Controls>
                </Row>
            </Container>
        );
    }
}

export default PowerCapacitor;
