"use strict";

window.FireAnimationHelper = {

    getShipPositionForFiring: function getShipPositionForFiring(icon, time, movementAnimations, weapon, turn) {
        if (weapon.ballistic) {
            return window.coordinateConverter.fromHexToGame(icon.getFirstMovementOnTurn(turn).position);
        }

        if (weapon.preFires) {
            return window.coordinateConverter.fromHexToGame(icon.getEndMovementOnTurn(turn).position);
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