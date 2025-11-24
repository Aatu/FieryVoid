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

        var data = animation.getPositionAndFacingAtTime(time);
        return data.position;
    }

};