import React, { Component } from 'react';
import styled from 'styled-components';
import theme from '../styled/theme';

/* Borders only — see the note in system/PowerCapacitor.js. Note the asymmetry: only the
   NON-weapon branch converts. The $isWeapon red (#b43131) is a signal, not chrome — it is
   what tells you at a glance that the menu you opened belongs to a weapon — so it stays
   exactly as it was.

   $isPurple is a third signal in the same spirit, for "this is an exotic ability, not ordinary
   ship chrome". It reuses the Hyach Computer palette verbatim (system/HyachComputerList.js) so
   the two menus read as one family rather than two near-misses. OPT-IN, driven by
   `system.activationMenuPurple` on the client system object — not by a name match here, so the
   React layer never has to know which classes exist. Chameleon Sensors is the first user. */
const PURPLE = {
    surface: 'rgba(32, 0, 32, 0.9)',
    line: '#5d3564',
    accent: '#7c4686',
    text: '#f2f2f2',
};

const Container = styled.div`
    display: flex;
    flex-direction: column;
    margin-top: 1px;
    width: 100%;
    min-width: 160px;
    opacity: 0.95 !important;
    background-color: ${props => (props.$isWeapon || props.$isPurple) ? PURPLE.surface : 'rgba(16, 26, 38, 0.9)'};
    border: 1px solid ${props => props.$isWeapon ? '#b43131' : props.$isPurple ? PURPLE.line : theme.colors.line};
`;

const Header = styled.div`
    padding: 3px;
    background-color: ${props => props.$isWeapon ? '#571616' : props.$isPurple ? PURPLE.line : '#215a7a'};
    border: 1px solid ${props => props.$isWeapon ? '#b43131' : props.$isPurple ? PURPLE.line : theme.colors.line};
    border-bottom: 1px solid ${props => props.$isWeapon ? '#b43131' : props.$isPurple ? PURPLE.line : theme.colors.line};
    color: ${props => (props.$isWeapon || props.$isPurple) ? PURPLE.text : theme.colors.chromeText};
    text-align: center;
    font-size: 11px;
    margin-bottom: 2px;
    opacity: 1 !important;
    font-weight: bold;
`;

const Row = styled.div`
    display: flex;
    align-items: center;
    padding: 1px 1px;
    border-bottom: 1px solid ${props => props.$isPurple ? PURPLE.line : '#496791'};
    font-size: 12px;
    color: ${props => props.$isPurple ? PURPLE.text : '#deebff'};
    justify-content: center;

    &:last-child {
        border-bottom: none;
    }

`;

const Label = styled.div`
    flex: 1;
    margin-right: 10px;    
`;

const Controls = styled.div`
    display: flex;
    align-items: center;     
    gap: 5px;
    width: 100%;
    padding: 2px;
`;

/* The IDLE chrome follows the theme; the active-state colours below deliberately do NOT. Green
   "on" / red "off" is the same at-a-glance signal on every activatable system in the game, and
   recolouring it per theme would trade a meaning for a decoration. */
const ActionButton = styled.div`
    flex: 1;
    height: 18px;
    background: ${props => props.$isPurple ? PURPLE.line : '#203348'};
    border: 1px solid ${props => props.$isPurple ? PURPLE.accent : '#496791'};
    color: ${props => props.$isPurple ? PURPLE.text : '#deebff'};
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 11px;
    padding: 0;
    opacity: 0.9;
    user-select: none;

    &:hover {
        background: ${props => props.$isPurple ? '#5e3666' : '#496791'};
        border: 1px solid ${props => props.$isPurple ? '#9a5aa6' : '#5d82b6ff'};
        color: #ffffff;
        opacity: 1;
    }

    ${props => props.disabled && (props.$isPurple ? `
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #4b2b51; color: #d8b9e6; border: 1px solid ${PURPLE.accent}; }
    ` : `
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #203348; color: #deebff; }
    `)}

    ${props => (props.$active || (props.$variant === 'activate' && props.$isWeapon)) && props.$variant === 'activate' && !props.$isWeapon && `
        background: #1b5e20;
        color: white;
        border: 1px solid #4caf50;
        opacity: 1;

        &:hover {
            background: #2e7d32;
            border: 1px solid #66bb6a;
            color: #ffffff;
            opacity: 1;
        }
    `}

    ${props => props.$variant === 'activate' && props.$isWeapon && `
        background: #7a3b00e5;
        color: #fff3e0;
        border: 1px solid #ff9900b6;
        opacity: 1;

        &:hover {
            background: #b35900;
            border: 1px solid #ffb74d;
            color: #ffffff;
            opacity: 1;
        }

        ${props.$active ? `
            background: #b35900;
            border: 1px solid #ffb74d;
            box-shadow: 0 0 5px #ff9800;
        ` : ''}
    `}

    ${props => props.$active && props.$variant === 'deactivate' && `
        background: #7f1d1d; 
        color: white;
        border: 1px solid #ef4444;
        opacity: 1;

        &:hover {
            background: #991b1b; 
            border: 1px solid #f87171;      
            color: #ffffff;
            opacity: 1;
        }
    `}
`;


class SystemActivation extends Component {

    handleActivate() {
        if (this.canActivate()) {
            this.props.system.doActivate();
            this.forceUpdate();
            webglScene.customEvent('SystemDataChanged', { ship: this.props.ship, system: this.props.system });
        }
    }

    //RMB: activate every same-type system on this ship that currently canActivate().
    //Mirrors allChangeFiringMode - matches by displayName, and each system is still
    //individually gated by its own canActivate(), so systems that can't/shouldn't
    //activate right now are simply skipped.
    handleActivateAll(e) {
        e.preventDefault();
        const { ship, system } = this.props;

        //fighter flights hold their systems one level down (per fighter); flatten if needed
        let allSystems = [];
        if (ship.flight) {
            allSystems = ship.systems
                .map(fighter => fighter.systems)
                .reduce((all, systems) => all.concat(systems), []);
        } else {
            allSystems = ship.systems;
        }

        //Match by name (the phpclass key), NOT displayName: activatable systems like Kirishiac
        //Orbitals carry a per-instance displayName suffix ('Orbital A', 'Orbital B', ...) so a
        //displayName match only ever hits the clicked system. name is shared across instances of
        //the same class. Each candidate is still gated by its own canActivate().
        let activatedAny = false;
        allSystems.forEach(candidate => {
            if (!candidate.name || candidate.name !== system.name) return;
            if (!(candidate.canActivate && typeof candidate.canActivate === 'function' && candidate.canActivate())) return;
            candidate.doActivate();
            activatedAny = true;
        });

        if (activatedAny) {
            this.forceUpdate();
            webglScene.customEvent('SystemDataChanged', { ship, system });
        }
    }

    handleDeactivate() {
        if (this.canDeactivate()) {
            this.props.system.doDeactivate();
            this.forceUpdate();
            webglScene.customEvent('SystemDataChanged', { ship: this.props.ship, system: this.props.system });
        }
    }

    //RMB: deactivate every same-type system on this ship that currently canDeactivate().
    //Symmetric with handleActivateAll - matches by displayName, each system still gated
    //by its own canDeactivate(), so systems that can't/shouldn't deactivate are skipped.
    handleDeactivateAll(e) {
        e.preventDefault();
        const { ship, system } = this.props;

        //fighter flights hold their systems one level down (per fighter); flatten if needed
        let allSystems = [];
        if (ship.flight) {
            allSystems = ship.systems
                .map(fighter => fighter.systems)
                .reduce((all, systems) => all.concat(systems), []);
        } else {
            allSystems = ship.systems;
        }

        //Match by name (see handleActivateAll) - displayName carries a per-instance suffix.
        let deactivatedAny = false;
        allSystems.forEach(candidate => {
            if (!candidate.name || candidate.name !== system.name) return;
            if (!(candidate.canDeactivate && typeof candidate.canDeactivate === 'function' && candidate.canDeactivate())) return;
            candidate.doDeactivate();
            deactivatedAny = true;
        });

        if (deactivatedAny) {
            this.forceUpdate();
            webglScene.customEvent('SystemDataChanged', { ship, system });
        }
    }

    canActivate() {
        return this.props.system.canActivate && typeof this.props.system.canActivate === 'function' && this.props.system.canActivate();
    }

    canDeactivate() {
        return this.props.system.canDeactivate && typeof this.props.system.canDeactivate === 'function' && this.props.system.canDeactivate();
    }

    render() {
        const { ship, system } = this.props;

        // Robust detection of active state
        const isActive = system.active || (system.weapon && weaponManager.hasFiringOrder(ship, system));

        //systems may supply their own button labels (e.g. Kirishiac Orbital: Dock / Deploy)
        const customActivate = typeof system.getActivateLabel === 'function' ? system.getActivateLabel() : null;
        const customDeactivate = typeof system.getDeactivateLabel === 'function' ? system.getDeactivateLabel() : null;
        const activateLabel = customActivate || (system.weapon ? "Fire" : "Activate");
        const deactivateLabel = customDeactivate || (system.weapon ? "Don't Fire" : "Deactivate");

        //singleActivationButton (Kirishiac Orbital): show only the currently applicable
        //action - "Dock" while deployed, "Deploy" while docked - instead of both buttons
        //with one disabled.
        const single = Boolean(system.singleActivationButton);
        const showActivate = !single || this.canActivate();
        const showDeactivate = !system.weapon && (!single || this.canDeactivate());

        //Opt-in purple chrome (see PURPLE above). A weapon menu keeps its red signal either way.
        const isPurple = Boolean(system.activationMenuPurple) && !system.weapon;

        return (
            <Container $isWeapon={system.weapon} $isPurple={isPurple}>
                <Header $isWeapon={system.weapon} $isPurple={isPurple}>{system.displayName}</Header>
                <Row $isPurple={isPurple}>
                    <Controls>
                        {showActivate && (
                            <ActionButton onClick={() => this.handleActivate()} onContextMenu={(e) => this.handleActivateAll(e)} disabled={!this.canActivate()} $active={isActive} $variant="activate" $isWeapon={system.weapon} $isPurple={isPurple}>{activateLabel}</ActionButton>
                        )}
                        {showDeactivate && (
                            <ActionButton onClick={() => this.handleDeactivate()} onContextMenu={(e) => this.handleDeactivateAll(e)} disabled={!this.canDeactivate()} $active={!isActive} $variant="deactivate" $isPurple={isPurple}>{deactivateLabel}</ActionButton>
                        )}
                    </Controls>
                </Row>
            </Container>
        );
    }
}

export default SystemActivation;
