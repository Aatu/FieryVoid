"use strict";

jQuery(function () {});

window.fleetListManager = {

    initialized: false,
    refreshed: true,

    prepare: function prepare() {},

    displayFleetLists: function displayFleetLists() {
    if (!fleetListManager.initialized) {
        $("#gameinfo .fleetlistentry").remove();
        const template = $("#logcontainer .fleetlistentry");

        for (const i in gamedata.slots) {
            const slot = gamedata.slots[i];
            if (slot.playerid === gamedata.thisplayer) {
                fleetListManager.createFleetList(slot, template);
            }
        }

        for (const i in gamedata.slots) {
            const slot = gamedata.slots[i];
            if (slot.playerid !== gamedata.thisplayer) {
                fleetListManager.createFleetList(slot, template);
            }
        }

        fleetListManager.initialized = true;
    } else if (!fleetListManager.refreshed) { //Just refresh whether orders committed or not.
        // Only update turnTaken text if refreshing
        for (const i in gamedata.slots) {
            const slot = gamedata.slots[i];
            fleetListManager.updateTurnTakenInFleetHeader(slot);
        }

        // Reset the flag
        fleetListManager.refreshed = true;
    }

    fleetListManager.updateFleetList();
},

createFleetList: function createFleetList(slot, template) {
    var shipArray = new Array();

    // Clone the template and append to gameinfo
    var fleetlistentry = template.clone(true).appendTo("#gameinfo");

    // CHANGED: Use a unique class based on slot ID instead of just playerid (to avoid DOM selector collisions)
    fleetlistentry.addClass("slot_" + slot.slot);

    // Set the fleet list header
    fleetlistentry.find(".fleetheader").html(
        "<span class='headername'>FLEET LIST - </span><span class='playername'>" + slot.playername + "</span>"
    );

    // Build list of ships for this player
    for (var i in gamedata.ships) {
        var ship = gamedata.ships[i];
        if (gamedata.isTerrain(ship.shipSizeClass, ship.userid)) continue;
        if (ship.userid == slot.playerid && ship.slot == slot.slot) {
            shipArray.push(ship);
        }
    }

    var fleetlisttable = fleetlistentry.find(".fleetlist");

    // CHANGED: Only search for the template inside this fleetlistentry, not globally
    template = fleetlistentry.find(".fleetlistline");

    var fleetlistline = template.clone(true);

    // Remove original template line (so it doesn’t get duplicated)
    fleetlistentry.find(".fleetlistline").remove();

    // Create and append the header row
    fleetlistline.html("<span><span class='shipname header'>Ship Name</span><span class='shipclass header'>Ship Class</span><span class='shiptype header'>Type</span><span class='initiative header'>Initiative</span><span class='value header'>Current Value</span></span>");
    fleetlistline.appendTo(fleetlisttable);

    var totalBaseValue = 0;
    var totalCurrValue = 0;

    // Add each ship to the list
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
        if (ship.flight === true) {
            // Flights have cost calculated per 6 fighters
            baseValue = ship.pointCost * (ship.flightSize / 6);
        }
        baseValue = Math.round(baseValue + ship.pointCostEnh + ship.pointCostEnh2);
        var currValue = Math.round(baseValue * ship.combatValue / 100);

        totalBaseValue += baseValue;
        totalCurrValue += currValue;

        fleetlistline.html(
            "<span id='" + ship.id + "'>" +
            "<span class='shipname clickable' data-shipid='" + ship.id + "'>" + ship.name + "</span>" +
            "<span class='shipclass'>" + ship.shipClass + "</span>" +
            "<span class='shiptype'>" + shiptype + "</span>" +
            "<span class='initiative'>" + shipManager.getIniativeOrder(ship) + "</span>" +
            "<span class='value'>" + currValue + '/' + baseValue + "CP</span>" +
            "<span class='shipstatus'></span></span>"
        );

        fleetlistline.appendTo(fleetlisttable);
    }

    var phaseLabel = "Initial Orders"
    switch(gamedata.gamephase){

        case -1:
            phaseLabel = "Pre-Turn";
            break;            
        case 2:
            phaseLabel = "Movement";
            break;
        case 5:
            phaseLabel = "Pre-Firing";
            break;  
        case 3:
            phaseLabel = "Firing";
            break;                                                  
    }

    var turnTaken = "<span style='color:orange'>&nbsp;&nbsp;[Waiting for " + phaseLabel + " Orders]</span>";

    if(slot.surrendered !== null){
        if(slot.surrendered <= gamedata.turn){ //Surrendered on this turn or before.
            turnTaken = "<span style='color:red'>&nbsp;&nbsp;[Surrendered on Turn " + slot.surrendered + "]</span>"; //Check surrendered first.
        }
    }else if (slot.waiting){
        turnTaken = "<span style='color:green;'>&nbsp;&nbsp;[Orders committed]</span>";
    } 
    
    var deploys = "";
    if(slot.depavailable > gamedata.turn) deploys = "<span style='color: #00b8e6'>[Deploys on Turn " + slot.depavailable + "]&nbsp;</span>";

    // Update fleet header with value totals
    fleetlistentry.find(".fleetheader").html(
        deploys + "<span class='headername'>FLEET LIST - </span>" +
        "<span class='playername'>" + slot.playername + 
        " - Fleet Value: " + totalCurrValue + " / " + totalBaseValue + " CP" +
         "<span class='turnTaken'>" + turnTaken + "</span>"
    );

    // Add ship click handler
    $(".clickable", fleetlistentry).on("click", fleetListManager.doScrollToShip);
},


    updateTurnTakenInFleetHeader: function updateTurnTakenInFleetHeader(slot) {
        const container = $(".slot_" + slot.slot); // Target the correct fleet list block
        const header = container.find(".fleetheader .turnTaken");

        if (!header.length) return; // Just in case something went wrong

        var phaseLabel = "Initial Orders"
        switch(gamedata.gamephase){
            case -1:
                phaseLabel = "Pre-Turn";
                break;            
            case 2:
                phaseLabel = "Movement";
                break;
            case 5:
                phaseLabel = "Pre-Firing";
                break;  
            case 3:
                phaseLabel = "Firing";
                break;                                                  
        }

        const html = slot.waiting
            ? "<span style='color:green'>&nbsp;&nbsp;[Orders committed]</span>"
            : "<span style='color:orange'>&nbsp;&nbsp;[Waiting for " + phaseLabel + " Orders]</span>";

        header.html(html);
    },

    updateFleetReadiness: function updateFleetReadiness(playerId) {

        for (const i in gamedata.slots) {
            const slot = gamedata.slots[i];
            if (slot.playerid === playerId) {
                slot.waiting = true; //Set this manually for front end to know, gamedata will not refect it yet with page refresh
                fleetListManager.refreshed = false;
                fleetListManager.displayFleetLists();                
            }
        }

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
            var name = ship.name;
            if (shipManager.isDestroyed(ship)) {
                // Remove action listener and make everything italic to indicate the
                // ship was destroyed.
                $("#" + ship.id + " .shipname").removeClass("clickable");
                if(shipManager.hasJumpedNotDestroyed(ship)){
                    $("#" + ship.id).addClass("jumped");
                    $("#" + ship.id + " .initiative").html("Jumped");                     
                } else {                
                    $("#" + ship.id).addClass("destroyed");
                    $("#" + ship.id + " .initiative").html("Destroyed");                    
                }
            }
        }
    },

    reset: function reset() {
        fleetListManager.initialized = false;
    },      

};