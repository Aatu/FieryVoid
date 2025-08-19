'use strict';

window.ajaxInterface = {

    poll: null,
    pollActive: false,
    pollcount: 0,
    submiting: false,
    //	fastpolling: false,

    /* //OLD VERSION
    getShipsForFaction: function getShipsForFaction(factionRequest, getFactionShipsCallback) {
        $.ajax({
            type: 'POST',
            url: 'gamelobbyloader.php',
            dataType: 'json',
            data: { faction: factionRequest },
            success: getFactionShipsCallback,
            error: ajaxInterface.errorAjax
        });
    },
    */

    //NEW VERSION FOR PHP 8    
    getShipsForFaction: function getShipsForFaction(factionRequest, getFactionShipsCallback) {
        fetch('gamelobbyloader.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ faction: factionRequest })
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            if (data.error) {
                ajaxInterface.errorAjax(null, null, data.error);
                return;
            }
            getFactionShipsCallback(data);
        })
        .catch(error => {
            ajaxInterface.errorAjax(null, null, error.message);
        });
    },

    react: function react() {
        alert("callback");
    },

/* //OLD VERSION
    submitGamedata: function submitGamedata() {

        if (ajaxInterface.submiting) return;

        ajaxInterface.submiting = true;

        var gd = ajaxInterface.construcGamedata();

        $.ajax({
            type: 'POST',
            url: 'gamedata.php',
            dataType: 'json',
            data: gd,
            success: ajaxInterface.successSubmit,
            error: ajaxInterface.errorAjax
        });

        gamedata.goToWaiting();
    },
*/

//New version - DK July 2025
submitGamedata: function submitGamedata() {
    if (ajaxInterface.submiting) return;

    ajaxInterface.submiting = true;

    // ✅ Build the payload using your existing function
    const gd = ajaxInterface.construcGamedata();

    // ✅ Force ships into a proper JSON string
    if (typeof gd.ships !== 'string') {
        gd.ships = JSON.stringify(gd.ships);
    }

    // ✅ Use JSON to avoid PHP array serialization quirks
    $.ajax({
        type: 'POST',
        url: 'gamedata.php',
        contentType: 'application/json; charset=utf-8', // ✅ send JSON body
        dataType: 'json',                               // ✅ expect JSON back
        data: JSON.stringify(gd),                       // ✅ encode full payload
        timeout: 15000,                                 // ✅ prevent long hangs
        success: function (response) {
            ajaxInterface.submiting = false;

            if (response && response.error) {
                console.error("Submit failed:", response);
                ajaxInterface.errorAjax(null, null, response.error);
            } else {
                ajaxInterface.successSubmit(response);
            }
        },
        error: function (xhr, status, error) {
            ajaxInterface.submiting = false;
            ajaxInterface.errorAjax(xhr, status, error);
        }
    });

    // ✅ Indicate we’re waiting for the server response
    gamedata.goToWaiting();
},

/* //OLD VERSION
submitSlotAction: function submitSlotAction(action, slotid, callback) {
    ajaxInterface.submiting = true;

    $.ajax({
        type: 'POST',
        url: 'slot.php',
        dataType: 'json',
        data: { action: action, gameid: gamedata.gameid, slotid: slotid },
        success: function (response) {
            ajaxInterface.successSubmit(response);
            if (typeof callback === "function") callback(response);
        },
        error: ajaxInterface.errorAjax
    });
},
*/

//New version for PHP8
submitSlotAction: function submitSlotAction(action, slotid, callback) {
    if (ajaxInterface.submiting) return;
    ajaxInterface.submiting = true;

    $.ajax({
        type: 'POST',
        url: 'slot.php',
        dataType: 'json', // ✅ Expect JSON
        data: { 
            action: action,
            gameid: gamedata.gameid,
            slotid: slotid 
        },
        timeout: 15000, // ✅ prevent hanging requests
    })
    .done(function (response, textStatus, xhr) {
        ajaxInterface.submiting = false;

        // ✅ Handle HTTP-level errors first
        if (xhr.status !== 200) {
            console.error(`Slot action failed [${xhr.status}]`);
            ajaxInterface.errorAjax(xhr, textStatus, response?.error || "Server error");
            return;
        }

        // ✅ Handle application-level errors
        if (response && response.error) {
            console.warn("Slot action error:", response.error);
            ajaxInterface.errorAjax(xhr, textStatus, response.error);
            return;
        }

        // ✅ Normal success
        ajaxInterface.successSubmit(response);
        if (typeof callback === "function") callback(response);
    })
    .fail(function (xhr, textStatus, errorThrown) {
        ajaxInterface.submiting = false;
        let message = errorThrown || textStatus || "Unknown network error";
        console.error("Slot action AJAX fail:", message, xhr.responseText);
        ajaxInterface.errorAjax(xhr, textStatus, message);
    });
},


    construcGamedata: function construcGamedata() {

        var tidyships = Array();

        for (var i in gamedata.ships) {
            var ship = gamedata.ships[i];
            var newShip = {
                'phpclass': ship.phpclass,
                'userid': ship.userid,
                'slot': ship.slot,
                'id': ship.id,
                'name': ship.name,
				'pointCostEnh': Math.round(ship.pointCostEnh),
				'pointCostEnh2': Math.round(ship.pointCostEnh2)
            };
            newShip.movement = Array();
            newShip.EW = Array();
            newShip.systems = Array();

            if (ship.userid === gamedata.thisplayer) {
                for (var a = ship.movement.length - 1; a >= 0; a--) {
                    var move = ship.movement[a];
                    if (move.turn == gamedata.turn) {
                        newShip.movement[a] = move;
                    }
                }

                for (var a = ship.EW.length - 1; a >= 0; a--) {
                    var ew = ship.EW[a];
                    if (ew.turn == gamedata.turn) {
                        newShip.EW[a] = ew;
                    }
                }

                var systems = Array();

                for (var a in ship.systems) {
                    var system = ship.systems[a];

                    if (ship.flight) {

                        var fighterSystems = Array();

                        for (var c in system.systems) {
                            var fightersystem = system.systems[c];
                            var ammoArray = Array();

                            for (var b = fightersystem.fireOrders.length - 1; b >= 0; b--) {
                                var fire = fightersystem.fireOrders[b];
                                if (fire.turn < gamedata.turn) {
                                    fightersystem.fireOrders.splice(b, 1);
                                }
                            }

                            if (fightersystem.missileArray != null) {
                                for (var index in fightersystem.missileArray) {
                                    var amount = fightersystem.missileArray[index].amount;
                                    ammoArray[index] = amount;
                                }
                            }
							
							//changed to accomodate new variable for individual data transfer to server - in a generic way
                            //fighterSystems[c] = { 'id': fightersystem.id, 'fireOrders': fightersystem.fireOrders, 'ammo': ammoArray };
							fightersystem.doIndividualNotesTransfer();
							fighterSystems[c] = { 'id': fightersystem.id, 'fireOrders': fightersystem.fireOrders, 'ammo': ammoArray, "individualNotesTransfer": fightersystem.individualNotesTransfer };
                        }
						//changed to accomodate new variable for individual data transfer to server - in a generic way
                        //systems[a] = { 'id': system.id, 'systems': fighterSystems };
						system.doIndividualNotesTransfer();
						systems[a] = { 'id': system.id, 'systems': fighterSystems, "individualNotesTransfer": system.individualNotesTransfer };
                    } else {
                        var fires = Array();
                        var ammoArray = Array();

                        for (var b = system.fireOrders.length - 1; b >= 0; b--) {
                            var fire = system.fireOrders[b];
                            if (fire.turn < gamedata.turn) {
                                system.fireOrders.splice(b, 1);
                            }
                        }
                        fires = system.fireOrders;

                        for (var b = system.power.length - 1; b >= 0; b--) {
                            var power = system.power[b];
                            if (power.turn < gamedata.turn) {
                                system.power.splice(b, 1);
                            }
                        }

                        systems[a] = { 'id': system.id, 'power': system.power, 'fireOrders': fires };
                        // }

                        if (system.missileArray != null) {
                            for (var index in system.missileArray) {
                                var amount = system.missileArray[index].amount;
                                ammoArray[index] = amount;
                            }
                        }
						//changed to accomodate new variable for individual data transfer to server - in a generic way
                        //systems[a] = { 'id': system.id, 'power': system.power, 'fireOrders': fires, 'ammo': ammoArray };
						system.doIndividualNotesTransfer();
						systems[a] = { 'id': system.id, 'power': system.power, 'fireOrders': fires, 'ammo': ammoArray, "individualNotesTransfer": system.individualNotesTransfer };
                    }
                }

                newShip.systems = systems;

                if (ship.flight) {
                    newShip.flightSize = ship.flightSize;
                }
                
                //unit enhancements
                newShip.enhancementOptions = ship.enhancementOptions;

                tidyships.push(newShip);
            }
        }

        var gd = {
            turn: gamedata.turn,
            phase: gamedata.gamephase,
            activeship: gamedata.activeship,
            gameid: gamedata.gameid,
            playerid: gamedata.thisplayer,
            slotid: gamedata.selectedSlot,
            status: gamedata.status,
            ships: JSON.stringify(tidyships)
        };

        return gd;
    },


    //Not sure what this one is for, not used as far as I can see... - DK
    construcGamedata2: function construcGamedata2() {

        var tidyships = jQuery.extend(true, {}, gamedata.ships);

        for (var i in tidyships) {
            var ship = tidyships[i];
            ship.htmlContainer = null;
            ship.shipclickableContainer = null;
            ship.shipStatusWindow = null;
            if (gamedata.isMyShip(ship)) {
                for (var a = ship.movement.length - 1; a >= 0; a--) {
                    var move = ship.movement[a];
                    if (move.turn < gamedata.turn) {
                        ship.movement.splice(a, 1);
                    }
                }

                for (var a = ship.EW.length - 1; a >= 0; a--) {
                    var ew = ship.EW[a];
                    if (ew.turn < gamedata.turn) {
                        ship.EW.splice(a, 1);
                    }
                }
                var systems = Array();

                for (var a in ship.systems) {
                    var system = ship.systems[a];

                    if (ship.flight) {
                        var fighterSystems = Array();
                        for (var c in system.systems) {
                            var fightersystem = system.systems[c];

                            for (var b = fightersystem.fireOrders.length - 1; b >= 0; b--) {
                                var fire = fightersystem.fireOrders[b];
                                if (fire.turn < gamedata.turn) {
                                    fightersystem.fireOrders.splice(b, 1);
                                }
                            }
                            fighterSystems[c] = { 'id': fightersystem.id, 'fireOrders': fightersystem.fireOrders };
                        }

                        systems[a] = { 'id': system.id, 'systems': fighterSystems };
                    } else {
                        var fires = Array();
        				/* Cleaned 19.8.25 - DK	                        
                        if (system.dualWeapon) {
                            for (var c in system.weapons) {
                                var weapon = system.weapons[c];
                                for (var b = weapon.fireOrders.length - 1; b >= 0; b--) {
                                    var fire = weapon.fireOrders[b];
                                    if (fire.turn < gamedata.turn) {
                                        weapon.fireOrders.splice(b, 1);
                                    }
                                }
                                fires = fires.concat(weapon.fireOrders);
                            }
                        } else {
                        */    
                            for (var b = system.fireOrders.length - 1; b >= 0; b--) {
                                var fire = system.fireOrders[b];
                                if (fire.turn < gamedata.turn) {
                                    system.fireOrders.splice(b, 1);
                                }
                            }
                            fires = system.fireOrders;
                        //}

                        for (var b = system.power.length - 1; b >= 0; b--) {
                            var power = system.power[b];
                            if (power.turn < gamedata.turn) {
                                system.power.splice(b, 1);
                            }
                        }
                        systems[a] = { 'id': system.id, 'power': system.power, 'fireOrders': fires };
                    }
                }

                ship.systems = systems;
            } else {
                ship.EW = Array();
                ship.movement = Array();
                ship.systems = Array();
            }
        }

        var gd = {
            turn: gamedata.turn,
            phase: gamedata.gamephase,
            activeship: gamedata.activeship,
            gameid: gamedata.gameid,
            playerid: gamedata.thisplayer,
            slotid: gamedata.selectedSlot,
            ships: JSON.stringify(tidyships)
        };

        return gd;
    },

    successSubmit: function successSubmit(data) {
        ajaxInterface.submiting = false;
        if (data.error) {
            window.confirm.exception(data, function () {});
            gamedata.waiting = false;
        } else {
            gamedata.parseServerData(data);
        }
    },

    successRequest: function successRequest(data) {
        ajaxInterface.submiting = false;
        if (data && data.error) {
            window.confirm.exception(data, function () {});
            gamedata.waiting = false;
        } else {
            //gamedata.parseServerData(data);
        }
        gamedata.parseServerData(data);
    },

    errorAjax: function errorAjax(jqXHR, textStatus, errorThrown) {
        console.dir(jqXHR);
        console.dir(errorThrown);
        window.confirm.exception({ error: "AJAX error: " + textStatus }, function () {});
    },

    startPollingGamedata: function startPollingGamedata() {

        if (gamedata.poll != null) {
            return;
        }

        ajaxInterface.pollActive = true;
        ajaxInterface.pollcount = 0;
        ajaxInterface.pollGamedata();
    },

    stopPolling: function stopPolling() {
        ajaxInterface.poll = null;
        ajaxInterface.pollcount = 0;
        ajaxInterface.pollActive = false;
    },

    pollGamedata: function pollGamedata() {

        if (!ajaxInterface.pollActive) {
            ajaxInterface.stopPolling();
            return;
        }

        if (gamedata.waiting == false) {
            ajaxInterface.stopPolling();
            return;
        }

        if (!ajaxInterface.submiting) ajaxInterface.requestGamedata();

        ajaxInterface.pollcount++;

        var time = 6000;

        if (ajaxInterface.pollcount > 10) {
            time = 6000;
        }

        if (ajaxInterface.pollcount > 100) {
            //        	ajaxInterface.fastpolling = false;
            time = 30000;
        }

        if (ajaxInterface.pollcount > 200) {
            time = 300000;
        }

        if (ajaxInterface.pollcount > 300) {
            return;
        }

        //        if (ajaxInterface.fastpolling) {
        //         	time=1000;
        //        }

        ajaxInterface.poll = setTimeout(ajaxInterface.pollGamedata, time);
    },

    startPollingGames: function startPollingGames() {
        ajaxInterface.pollGames();
    },

    pollGames: function pollGames() {
        if (gamedata.waiting === false) return;

        if (!gamedata.animating) {

            animation.animateWaiting();

            ajaxInterface.requestAllGames();
        }
    },

    requestGamedata: function requestGamedata() {

        ajaxInterface.submiting = true;

        $.ajax({
            type: 'GET',
            url: 'gamedata.php',
            dataType: 'json',
            data: {
                turn: gamedata.turn,
                phase: gamedata.gamephase,
                activeship: gamedata.activeship,
                gameid: gamedata.gameid,
                playerid: gamedata.thisplayer,
                time: new Date().getTime()
            },
            success: ajaxInterface.successRequest,
            error: ajaxInterface.errorAjax
        });
    },

    requestAllGames: function requestAllGames() {

        ajaxInterface.submiting = true;

        $.ajax({
            type: 'GET',
            url: 'allgames.php',
            dataType: 'json',
            data: {},
            success: ajaxInterface.successRequest,
            error: ajaxInterface.errorAjax
        });
    },

    getFirePhaseGames: function getFirePhaseGames() {

        $.ajax({
            type: 'GET',
            url: 'firePhaseGames.php',
            dataType: 'json',
            data: {},
            success: gamedata.createFireDiv,
            error: ajaxInterface.errorAjax
        });
    }
};
