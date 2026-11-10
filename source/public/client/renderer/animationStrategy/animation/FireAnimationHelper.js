"use strict";

window.FireAnimationHelper = {

    /* Where a hex-targeted shot starts - the hex the ship is ACTUALLY in when it fires, forced moves included
       (the movement animation alone stops where the ship's own movement ended):
       - ballistic: its launch hex - launched in Initial Orders, before any of the turn's movement;
       - Pre-Firing: where Movement left it, then a Gravity Well's pull - every pull plays before any Pre-Firing
         (ReplayAnimationStrategy Pass 0, GRAVITY_WELL_PLAN.md);
       - Firing: after every forced move of the turn (Pre-Firing shifts and pulls alike), the rule
         AllWeaponFireAgainstShipAnimation, ShipDestroyedAnimation and ShipJumpAnimation use. */
    getShipPositionForFiring: function getShipPositionForFiring(icon, time, movementAnimations, weapon, turn, fireOrder) {
        if (weapon.ballistic) {
            return window.coordinateConverter.fromHexToGame(icon.getFirstMovementOnTurn(turn).position);
        }

        var forcedMoves = icon.preFireMovements || [];

        if (weapon.preFires || (fireOrder && fireOrder.type === 'prefiring')) {
            var pulls = forcedMoves.filter(function (move) { return move.type === 'gravity'; });
            if (pulls.length > 0) {
                return window.coordinateConverter.fromHexToGame(pulls[pulls.length - 1].position);
            }
            return window.coordinateConverter.fromHexToGame(icon.getEndMovementOnTurn(turn).position);
        }

        if (forcedMoves.length > 0) {
            return window.coordinateConverter.fromHexToGame(forcedMoves[forcedMoves.length - 1].position);
        }

        return FireAnimationHelper.getShipPositionAtTime(icon, time, movementAnimations);
    },

    getShipPositionAtTime: function getShipPositionAtTime(icon, time, movementAnimations) {

        var animation = movementAnimations[icon.shipId];

        // Guard: a mine (or any unit) that was skipped during animateMovement
        // (e.g. an undetected stealth unit that fires) will have no animation entry.
        // Fall back to the icon's last known position so the replay doesn't crash.
        if (!animation) {
            var lastMove = icon.getLastMovement();
            return window.coordinateConverter.fromHexToGame(lastMove.position);
        }

        var data = animation.getPositionAndFacingAtTime(time);
        return data.position;
    }

};