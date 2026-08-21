"use strict";

/* JUMP_POINTS_PLAN.md Stage 4 - the Movement phase's ship-tooltip menu.
 *
 * Movement had no menu of its own: MovementPhaseStrategy handed the plain ShipTooltipMenu
 * (one button, "Open ship details") to every tooltip it opened. This is the phase-specific
 * sibling of ShipTooltipInitialOrdersMenu and ShipTooltipFireMenu, and it carries exactly one
 * button today - JUMP OUT, offered only when the selected unit is standing in an open jump
 * vortex and entered its hex through the side the vortex faces (plan sections 2.2 and 2.5).
 *
 * The rule itself lives in shipManager.movement.canJumpOut; this file only draws the button.
 */
window.ShipTooltipMovementMenu = function () {

    function ShipTooltipMovementMenu(selectedShip, targetedShip, turn) {
        ShipTooltipMenu.call(this, selectedShip, targetedShip, turn);
    }

    ShipTooltipMovementMenu.prototype = Object.create(ShipTooltipMenu.prototype);

    ShipTooltipMovementMenu.buttons = [
        {
            className: "jumpOut",
            condition: [isSelf, canJumpOut],
            action: jumpOut,
            info: "Jump to Hyperspace"
        }
    ];

    //Phase buttons render BEFORE the base menu's openSCS, the same order the Initial Orders and
    //Fire menus use.
    ShipTooltipMovementMenu.prototype.getAllButtons = function () {
        return ShipTooltipMovementMenu.buttons.concat(ShipTooltipMenu.prototype.getAllButtons.call(this));
    };

    //Only ever offered for the unit that is actually plotting - clicking someone else's ship
    //while yours is selected opens a tooltip for THAT ship, and the button must not follow it.
    //MovementPhaseStrategy.canSelectShip already restricts selection to the active ini grouping,
    //so "the selected ship" is by definition a unit this player may move right now.
    function isSelf() {
        return this.selectedShip === this.targetedShip;
    }

    function canJumpOut() {
        return shipManager.movement.canJumpOut(this.targetedShip);
    }

    function jumpOut() {
        var ship = this.targetedShip;
        if (!shipManager.movement.doJumpOut(ship)) return;

        //Same refresh seam every movement button uses (webglScene dispatches to
        //PhaseStrategy.on<Name>): redraws the ship's plotted path, re-evaluates the Commit
        //button, and closes this tooltip via the onClickCallbacks sweep.
        webglScene.customEvent("ShipMovementChanged", { ship: ship });
    }

    return ShipTooltipMovementMenu;
}();
