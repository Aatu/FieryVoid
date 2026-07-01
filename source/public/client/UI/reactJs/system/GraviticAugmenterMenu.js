import React, { Component } from 'react';
import styled from 'styled-components';

/* Green-styled control menu for the Gravitic Augmenter (3 firing modes).
 *
 * Initial Orders (gamephase 1): a header with < > arrows cycles between the two
 * Initial-Orders modes (1 = Matter Weapon Enhancement, 2 = Warrior Enhancement). A
 * sub-menu below shows the relevant control:
 *   Mode 1 -> Activate / Deactivate (creates / removes the non-targeted ballistic order).
 *   Mode 2 -> "Target friendly Warrior" (selects the weapon so the player clicks a flight).
 * If a fire order already exists no mode-switching is offered (the order is locked in).
 *
 * Pre-Firing (gamephase 5): Mode 3 (Gravity Shifting). Two rows of rotation buttons —
 * Clockwise (60 / 120) and Anti-Clockwise (60 / 120). These stay available after the
 * target is chosen so the player can adjust; selecting a rotation updates any live order.
 */

const Container = styled.div`
    display: flex;
    flex-direction: column;
    margin-top: 1px;
    width: 100%;
    min-width: 180px;
    opacity: 0.95 !important;
    background-color: rgba(8, 28, 12, 0.92);
    border: 1px solid #3f8a3f;
`;

const Header = styled.div`
    padding: 3px;
    background-color: #16401b;
    border: 1px solid #3f8a3f;
    color: #e6ffe6;
    text-align: center;
    font-size: 11px;
    margin-bottom: 2px;
    opacity: 1 !important;
    font-weight: bold;
`;

const HeaderRow = styled.div`
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 3px;
    background-color: #16401b;
    border: 1px solid #3f8a3f;
    color: #e6ffe6;
    font-size: 11px;
    font-weight: bold;
    margin-bottom: 2px;
    min-width: 220px;
`;

const HeaderArrow = styled.div`
    width: 20px;
    height: 18px;
    background: #1b5e20;
    border: 1px solid #2e7d32;
    color: #e6ffe6;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    user-select: none;

    &:hover {
        background: #2e7d32;
        border: 1px solid #66bb6a;
        color: #ffffff;
    }

    ${props => props.disabled && `
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #1b5e20; border: 1px solid #2e7d32; color: #e6ffe6; }
    `}
`;

const HeaderLabel = styled.div`
    flex: 1;
    text-align: center;
    padding: 0 6px;
`;

const SubLabel = styled.div`
    text-align: center;
    color: #bdf0bd;
    font-size: 10px;
    padding: 2px 4px 0 4px;
`;

const Row = styled.div`
    display: flex;
    align-items: center;
    gap: 5px;
    width: 100%;
    padding: 3px;
`;

const RowLabel = styled.div`
    width: 70px;
    font-size: 10px;
    color: #bdf0bd;
    user-select: none;
`;

const ActionButton = styled.div`
    flex: 1;
    height: 20px;
    background: #1b5e20;
    border: 1px solid #2e7d32;
    color: #e6ffe6;
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 11px;
    padding: 0 4px;
    opacity: 0.95;
    user-select: none;

    &:hover {
        background: #2e7d32;
        border: 1px solid #66bb6a;
        color: #ffffff;
        opacity: 1;
    }

    ${props => props.$active && `
        background: #2e7d32;
        border: 1px solid #66bb6a;
        box-shadow: 0 0 5px #4caf50;
        color: #ffffff;
        opacity: 1;
    `}

    ${props => props.disabled && `
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #1b5e20; border: 1px solid #2e7d32; color: #e6ffe6; }
    `}
`;

class GraviticAugmenterMenu extends Component {

    refresh() {
        const { ship, system } = this.props;
        this.forceUpdate();
        webglScene.customEvent('SystemDataChanged', { ship, system });
    }

    // --- Initial Orders (modes 1 & 2) ---------------------------------------
    // NOTE: this menu only renders while the weapon has NO fire order (the
    // canGraviticAugmenter gate hides it otherwise), so none of these handlers
    // need to guard against an existing order - the standard remove-fire-order
    // button takes over once one exists.

    cycleMode(direction) {
        const { system } = this.props;
        if (!gamedata.isMyShip(this.props.ship)) return;
        //Only modes 1 and 2 are available in Initial Orders; toggle between them.
        //We set the mode directly (not via weaponManager.onSetModeClicked) because that
        //helper's ballistic/preFires phase guards reject Mode 1 in Initial Orders.
        //setFiringMode + initializationUpdate keeps the per-mode ballistic/preFires flags in sync.
        const next = system.firingMode == 1 ? 2 : 1;
        system.setFiringMode(next);
        if (typeof system.initializationUpdate === 'function') system.initializationUpdate();
        this.refresh();
    }

    activateMode1() {
        const { ship, system } = this.props;
        if (!system.canActivate()) return;
        system.doActivate(); //creates the order + unselects the weapon
        //Close the info panel so it rebuilds fresh on the next click: the green menu is then
        //gone (order exists) and the standard remove-fire-order button is available to cancel.
        webglScene.customEvent('SystemDataChanged', { ship, system });
        webglScene.customEvent('CloseSystemInfo');
    }

    targetWarrior() {
        const { ship, system } = this.props;
        //Select the weapon; player then clicks a friendly Warrior flight (normal ballistic
        //ally targeting handles the rest, like Shield Reinforcement).
        if (!weaponManager.isSelectedWeapon(system)) {
            weaponManager.selectWeapon(ship, system);
        }
        //Close the panel so the board click that picks the Warrior flight isn't obstructed.
        webglScene.customEvent('SystemDataChanged', { ship, system });
        webglScene.customEvent('CloseSystemInfo');
    }

    // --- Pre-Firing (mode 3) ------------------------------------------------

    setRotation(direction, amount) {
        const { system } = this.props;
        system.rotationDirection = direction; //1 = CW, 2 = ACW
        system.rotationAmount = amount;        //1 = 60deg, 2 = 120deg
        //Keep any already-declared order's notes in sync with the new selection.
        if (typeof system.updateRotationNotes === 'function') system.updateRotationNotes();
        this.refresh();
    }

    renderInitialOrders() {
        const { system } = this.props;
        const mode = parseInt(system.firingMode, 10);
        const modeName = system.firingModes[mode] || '';

        return (
            <Container>
                <HeaderRow>
                    <HeaderArrow onClick={() => this.cycleMode(-1)} title="Previous mode">&lt;</HeaderArrow>
                    <HeaderLabel>{modeName}</HeaderLabel>
                    <HeaderArrow onClick={() => this.cycleMode(1)} title="Next mode">&gt;</HeaderArrow>
                </HeaderRow>

                {mode == 1 && (
                    <Row>
                        <ActionButton onClick={() => this.activateMode1()}>Activate</ActionButton>
                    </Row>
                )}

                {mode == 2 && (
                    <Row>
                        <ActionButton
                            onClick={() => this.targetWarrior()}
                            $active={weaponManager.isSelectedWeapon(system)}
                        >
                            Target friendly Warrior
                        </ActionButton>
                    </Row>
                )}
            </Container>
        );
    }

    engageMode3() {
        const { ship, system } = this.props;
        if (!gamedata.isMyShip(ship)) return;
        if (weaponManager.hasFiringOrder(ship, system)) return; //don't switch mode under a live order
        system.setFiringMode(3);
        if (typeof system.initializationUpdate === 'function') system.initializationUpdate();
        this.refresh();
    }

    renderPreFiring() {
        const { system } = this.props;

        //In Pre-Firing the weapon may still be on an Initial-Orders mode; offer to switch it
        //to Gravity Shifting before showing the rotation controls.
        if (system.firingMode != 3) {
            return (
                <Container>
                    <Header>Gravitic Augmenter</Header>
                    <Row>
                        <ActionButton onClick={() => this.engageMode3()}>Engage Gravity Shifting</ActionButton>
                    </Row>
                </Container>
            );
        }

        const dir = system.rotationDirection || 1;
        const amt = system.rotationAmount || 1;

        const isSel = (d, a) => dir == d && amt == a;

        return (
            <Container>
                <Header>Gravity Shift Settings</Header>
                <Row>
                    <RowLabel>Clockwise</RowLabel>
                    <ActionButton onClick={() => this.setRotation(1, 1)} $active={isSel(1, 1)}>60&deg;</ActionButton>
                    <ActionButton onClick={() => this.setRotation(1, 2)} $active={isSel(1, 2)}>120&deg;</ActionButton>
                </Row>
                <Row>
                    <RowLabel>Anti-Clockwise</RowLabel>
                    <ActionButton onClick={() => this.setRotation(2, 1)} $active={isSel(2, 1)}>60&deg;</ActionButton>
                    <ActionButton onClick={() => this.setRotation(2, 2)} $active={isSel(2, 2)}>120&deg;</ActionButton>
                </Row>
            </Container>
        );
    }

    render() {
        const { system } = this.props;
        if (!system) return null;

        if (gamedata.gamephase == 1) {
            //In Initial Orders only modes 1 & 2 are relevant; if persisted on mode 3, treat as mode 1.
            if (system.firingMode == 3) {
                system.setFiringMode(1);
                if (typeof system.initializationUpdate === 'function') system.initializationUpdate();
            }
            return this.renderInitialOrders();
        }

        if (gamedata.gamephase == 5) {
            return this.renderPreFiring();
        }

        return null;
    }
}

export default GraviticAugmenterMenu;
