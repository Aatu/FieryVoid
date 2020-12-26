'use strict';

window.ShipTooltipBallisticsMenu = function () {

    var template = '<div>' + '<span class="weapon"></span>' + '<span class="hitchange"></span>' + '<span class="interception"> </span>' + '<button class="intercept"> </button>' + '</div>';

    function ShipTooltipBallisticsMenu(shipIconContainer, turn, allowIntercept, selectedShip) {
        this.shipIconContainer = shipIconContainer;
        this.turn = turn;
        this.allowIntercept = false; //obsolete, actually
        this.selectedShip = selectedShip;
    }

    function getBallisticEntry(ball) {
        return {
            weaponid: ball.weapon.id,
            targetid: ball.fireOrder.targetid,
            shooterid: ball.shooter.id,
            fireOrderId: ball.fireOrder.id,
            position: this.shipIconContainer.getByShip(ball.shooter).getFirstMovementOnTurn(this.turn).position
        };
    }

    function groupByOriginAndHitChange(ballistics) {
        let listObject = {};

        ballistics.forEach(ballistic => {
            const key = ballistic.shooter.id + '-' +  ballistic.weapon.displayName + '-' + weaponManager.calculataBallisticHitChange(getBallisticEntry.call(this, ballistic));
     
            if (listObject[key]) {
                listObject[key].amount++;
            } else {
                listObject[key] = {
                    ballistic,
                    amount: 1
                }
            }
        }, this)

        return listObject;
    }

    ShipTooltipBallisticsMenu.prototype.renderTo = function (ship, element) {
        var ballistics = weaponManager.getAllBallisticsAgainst(ship, this.hexagon);

        if (ballistics.length > 0) {
            $(".ballistics", element).show();
        } else {
            $(".ballistics", element).hide();
            return;
        }

        const grouped = groupByOriginAndHitChange.call(this, ballistics)

        Object.keys(grouped).forEach(function (key) {
            var ball = grouped[key].ballistic;
            const amount = grouped[key].amount;
            var ballElement = jQuery(template);

            var ballisticEntry = getBallisticEntry.call(this, ball)

            jQuery(".weapon", ballElement).html(amount ? amount + 'x ' + ball.weapon.displayName : ball.weapon.displayName);
            jQuery(".hitchange", ballElement).html('- Approx: ' + weaponManager.calculataBallisticHitChange(ballisticEntry) + '%');

            /*
            if (this.allowIntercept) {
                var interception = weaponManager.getInterception(ball.fireOrder) * 5;

                if (interception > 0) {
                    jQuery(".interception", ballElement).html("Interception " + interception + "%").show();
                } else {
                    jQuery(".interception", ballElement).hide();
                }

                var hasIntrceptingWeaponsSelected = gamedata.selectedSystems.some(function (weapon) {
                    return weapon.getInterceptRating && weapon.getInterceptRating() > 0;
                });

                var interceptButton = jQuery(".intercept", ballElement);

                if (hasIntrceptingWeaponsSelected) {
                    interceptButton.on('click', function () {
                        weaponManager.targetBallistic(this.selectedShip, ballisticEntry);
                    }.bind(this)).html("INTERCEPT").show();
                } else {
                    console.log("HIDE INTERCEPT");
                    interceptButton.hide();
                }
            } else {
                jQuery(".intercept", ballElement).hide();
            }

            */
            jQuery(".incoming", element).append(ballElement);
        }, this);
    };

    return ShipTooltipBallisticsMenu;
}();