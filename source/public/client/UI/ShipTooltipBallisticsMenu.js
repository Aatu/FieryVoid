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
            //const key = ballistic.shooter.id + '-' +  ballistic.weapon.displayName + '-' + weaponManager.calculataBallisticHitChange(getBallisticEntry.call(this, ballistic));
			//let's differentiate by mode as well!
			const key = ballistic.shooter.id + '-' +  ballistic.weapon.displayName + '-' +  ballistic.fireOrder.firingMode +'-' + weaponManager.calculataBallisticHitChange(getBallisticEntry.call(this, ballistic));
     
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
        
            var ballisticEntry = getBallisticEntry.call(this, ball);
        
            // Set correct firing mode
            var modeIteration = ball.fireOrder.firingMode;
            if (modeIteration != ball.weapon.firingMode) {
                while (modeIteration != ball.weapon.firingMode) {
                    ball.weapon.changeFiringMode();
                }
            }
        
            // Set display text
            var textToDisplay = ball.weapon.displayName;
            if (amount > 1) textToDisplay = amount + 'x ' + ball.weapon.displayName;
            textToDisplay = ball.shooter.name + ', ' + textToDisplay + ' (' + ball.weapon.firingModes[ball.fireOrder.firingMode] + ') ';
            jQuery(".weapon", ballElement).html(textToDisplay);

			var hitchance = weaponManager.calculataBallisticHitChange(ballisticEntry);        
            var hitchanceNormalMode = ball.fireOrder.chance ?? ball.fireOrder.needed;
        
            // Build hitchance list manually, based on number of ballistics.
            let hitchanceList = [];
            for (let i = 0; i < ballistics.length; i++) {
                let hc = ballistics[i].fireOrder.chance;
                hitchanceList.push(hc);
            }
        
            const minHitchance = Math.min(...hitchanceList);
            const maxHitchance = Math.max(...hitchanceList);
        
            if (ball.fireOrder.type == "normal" && amount > 1 && minHitchance !== maxHitchance) {
                jQuery(".hitchange", ballElement).html('- Between: ' + minHitchance + '% - ' + hitchanceNormalMode + '%');
            } else if (ball.fireOrder.type == "normal") {
                jQuery(".hitchange", ballElement).html('- Approx: ' + hitchanceNormalMode + '%');
            } else {
                jQuery(".hitchange", ballElement).html('- Approx: ' + hitchance + '%');
            }
            /*
			var hitchance = weaponManager.calculataBallisticHitChange(ballisticEntry);
			var hitchanceNormalMode = ball.fireOrder.chance ?? ball.fireOrder.needed;	//Fireorder hitchance as locked in by Normal mode weapons.
            var hitChanceLow = hitchance + ball.fireOrder.hitmod;	//To show the difference between lowest and highest hitchances in tooltip.
            *

			if(ball.fireOrder.type == "normal" && amount > 1 && ball.fireOrder.hitmod){ //Method only works during targeting Normal types, not in Replay :(
				var hitChanceLow = hitchance + ball.fireOrder.hitmod;	//To show the difference between lowest and highest hitchances in tooltip.	
		        jQuery(".hitchange", ballElement).html('- Between: ' + hitChanceLow + '% - ' + hitchanceNormalMode + '%' ); 			
			}else if(ball.fireOrder.type == "normal"){ //Where there's only 1 normal fireOrder or in Replay
				jQuery(".hitchange", ballElement).html('- Approx: ' + hitchanceNormalMode + '%' ); 
			}else{ //Everything else, e.g. all ballistics
			    jQuery(".hitchange", ballElement).html('- Approx: ' + hitchance + '%' ); 
			}

            */
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