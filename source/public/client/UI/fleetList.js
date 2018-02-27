"use strict";

jQuery(function () {});

window.fleetListManager = {

    initialized: false,

    prepare: function prepare() {},

    displayFleetLists: function displayFleetLists() {
        if (!fleetListManager.initialized) {
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

            if (ship.userid == slot.playerid) {
                shipArray.push(ship);
            }
        }

        var fleetlisttable = fleetlistentry.find(".fleetlist");
        template = $(".fleetlistentry.playerid_" + slot.playerid + " .fleetlistline");
        var fleetlistline = template.clone(true);
        fleetlistentry.find(".fleetlistline").remove();

        fleetlistline.html("<span><span class='shipname header'>Ship Name</span><span class='shipclass header'>Ship Class</span><span class='shiptype header'>Type</span><span class='initiative header'>Initiative</span></span>");
        fleetlistline.appendTo(fleetlisttable);

        for (var index in shipArray) {
            ship = shipArray[index];
            fleetlistline = template.clone(true);
            var shiptype = "unknown";

            switch (ship.shipSizeClass) {
                case -1:
                    shiptype = "squadron";
                    break;
                case 1:
                    shiptype = "MCV";
                    break;
                case 2:
                    shiptype = "HCV";
                    break;
                case 3:
                    shiptype = "capital";
                    break;
                default:
                    break;
            }

            fleetlistline.html("<span id='" + ship.id + "'><span class='shipname clickable' data-shipid='" + ship.id + "'>" + ship.name + "</span><span class='shipclass'>" + ship.phpclass + "</span><span class='shiptype'>" + shiptype + "</span><span class='initiative'>" + shipManager.getIniativeOrder(ship) + "</span><span class='shipstatus'></span></span>");
            fleetlistline.appendTo(fleetlisttable);
        }

        $(".clickable", fleetlistentry).on("click", fleetListManager.doScrollToShip);
    },

    doScrollToShip: function doScrollToShip(e) {
        e.stopPropagation();
        var shipNameEntry = e.currentTarget;

        if (!shipNameEntry.classList.contains("clickable")) {
            return;
        }

        var shipId = shipNameEntry.dataset["shipid"];
        var target = gamedata.getShip(shipId);
        scrolling.scrollToShip(target);
    },

    updateFleetList: function updateFleetList() {
        for (var i in gamedata.ships) {
            var ship = gamedata.ships[i];

            if (shipManager.isDestroyed(ship)) {
                // Remove action listener and make everything italic to indicate the
                // ship was destroyed.
                $("#" + ship.id + " .shipname").removeClass("clickable");
                $("#" + ship.id).addClass("destroyed");
                $("#" + ship.id + " .initiative").html("destroyed");
            }
        }
    }
};