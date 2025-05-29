"use strict";

jQuery(function () {});

window.fleetListManager = {

    initialized: false,

    prepare: function prepare() {},

    displayFleetLists: function displayFleetLists() {
        if (!fleetListManager.initialized) {

        // Clean up previous fleet list entries to avoid duplicates
        $("#gameinfo .fleetlistentry").remove();
                    
            var template = $("#logcontainer .fleetlistentry");

            // first display the fleet list of the current player
            for (var i in gamedata.slots) {
                var slot = gamedata.slots[i];

                if (slot.playerid != gamedata.thisplayer) {
                    continue;
                }

                fleetListManager.createFleetList(slot, template);
            }

            // now display the rest
            for (var i in gamedata.slots) {
                var slot = gamedata.slots[i];

                if (slot.playerid == gamedata.thisplayer) {
                    continue;
                }

                fleetListManager.createFleetList(slot, template);
            }

            fleetListManager.initialized = true;
        }

        fleetListManager.updateFleetList();
    },

    createFleetList: function createFleetList(slot, template) {
        var shipArray = new Array();
        var fleetlistentry = template.clone(true).appendTo("#gameinfo");

        fleetlistentry.addClass("playerid_" + slot.playerid);

	
        fleetlistentry.find(".fleetheader").html("<span class='headername'>FLEET LIST - </span><span class='playername'>" + slot.playername + "</span>");

        for (var i in gamedata.ships) {
            var ship = gamedata.ships[i];
            if(gamedata.isTerrain(ship)) continue;
            if (ship.userid == slot.playerid) {
                shipArray.push(ship);
            }
        }

        var fleetlisttable = fleetlistentry.find(".fleetlist");
        template = $(".fleetlistentry.playerid_" + slot.playerid + " .fleetlistline");
        var fleetlistline = template.clone(true);
        fleetlistentry.find(".fleetlistline").remove();

        fleetlistline.html("<span><span class='shipname header'>Ship Name</span><span class='shipclass header'>Ship Class</span><span class='shiptype header'>Type</span><span class='initiative header'>Initiative</span><span class='value header'>Value (current/base)</span></span>");
        fleetlistline.appendTo(fleetlisttable);

		var totalBaseValue = 0;
		var totalCurrValue = 0;

        for (var index in shipArray) {
            ship = shipArray[index];
            fleetlistline = template.clone(true);
            var shiptype = "unknown";

            switch (ship.shipSizeClass) {
                case -1:
                    shiptype = "Squadron";
                    break;
                case 1:
                    shiptype = "MCV";
                    break;
                case 2:
                    shiptype = "HCV";
                    break;
                case 3:
                    shiptype = "Capital";
                    break;
                default:
                    break;
            }
			var baseValue = ship.pointCost;
			if (ship.flight === true) { //flight price is always set for 6 fighters, we need to derive actual for this flight!
				baseValue = ship.pointCost * (ship.flightSize/6);
			}
			baseValue = Math.round(baseValue + ship.pointCostEnh + ship.pointCostEnh2); //enhancement price is total for unit
			var currValue = Math.round(baseValue * ship.combatValue / 100);
			totalBaseValue += baseValue;
			totalCurrValue += currValue ;
            fleetlistline.html("<span id='" + ship.id + "'><span class='shipname clickable' data-shipid='" + ship.id + "'>" + ship.name + "</span><span class='shipclass'>" + ship.phpclass + "</span><span class='shiptype'>" + shiptype + "</span><span class='initiative'>" + shipManager.getIniativeOrder(ship) + "</span><span class='value'>"+currValue+'/'+baseValue+"CP</span><span class='shipstatus'></span></span>");
            fleetlistline.appendTo(fleetlisttable);
        }
	
        fleetlistentry.find(".fleetheader").html("<span class='headername'>FLEET LIST - </span><span class='playername'>" + slot.playername + ", fleet value: " + totalCurrValue +" / "+totalBaseValue+ " CP </span>");

        $(".clickable", fleetlistentry).on("click", fleetListManager.doScrollToShip);
    },

    doScrollToShip: function doScrollToShip(e) {
        e.stopPropagation();
        var shipNameEntry = e.currentTarget;

        if (!shipNameEntry.classList.contains("clickable")) {
            return;
        }

        var shipId = shipNameEntry.dataset["shipid"];
        var ship = gamedata.getShip(shipId);

        if(shipManager.shouldBeHidden(ship)){ //Enemy, stealth equipped and undetected, or not deployed yet.
            return; //Do not scroll to Stealthed ships
        } else{
            window.webglScene.customEvent('ScrollToShip', {shipId: shipId});
        }    
    },

    updateFleetList: function updateFleetList() {
        for (var i in gamedata.ships) {
            var ship = gamedata.ships[i];

            if (shipManager.isDestroyed(ship)) {
                // Remove action listener and make everything italic to indicate the
                // ship was destroyed.
                $("#" + ship.id + " .shipname").removeClass("clickable");
                $("#" + ship.id).addClass("destroyed");
                $("#" + ship.id + " .initiative").html("Destroyed");
            }
        }
    },

    reset: function reset() {
        fleetListManager.initialized = false;
    }    

};