'use strict';

window.ajaxInterface = {

    poll: null,
    pollActive: false,
    pollcount: 0,
    submiting: false,
    queue: [],
    queuedFactions: new Set(), // track what’s already in queue    
    currentFaction: null,
    lastRequestTime: {},   // track last request time per faction
    debounceDelay: 200,    // ms

    processNext: function() {
        if (ajaxInterface.submiting || this.queue.length === 0) return;

        const { factionRequest, callback } = this.queue.shift();
        this.queuedFactions.delete(factionRequest);
        this.currentFaction = factionRequest;
        ajaxInterface.submiting = true;

        console.log("Requesting faction:", factionRequest);

        fetch('gamelobbyloader.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ faction: String(factionRequest) })
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            if (data.error) {
                this.errorAjax(null, null, data.error);
                return;
            }
            callback(data);
        })
        .catch(error => {
            this.errorAjax(null, null, error.message);
        })
        .finally(() => {
            ajaxInterface.submiting = false;
            this.currentFaction = null;
            this.processNext(); // strictly process one after the other
        });
    },


    getShipsForFaction: function(factionRequest, callback) {
        // skip if faction is currently processing
        if (factionRequest === this.currentFaction) return;

        // skip if faction is already queued
        if (this.queuedFactions.has(factionRequest)) return;

        this.queue.push({ factionRequest, callback });
        this.queuedFactions.add(factionRequest);

        // Only kick off if idle
        if (!ajaxInterface.submiting) {
            this.processNext();
        }
    },

    //NEW VERSION FOR PHP 8   
    /* 
    getShipsForFaction: function getShipsForFaction(factionRequest, getFactionShipsCallback) {
        if (ajaxInterface.submiting) return;

        ajaxInterface.submiting = true;

        fetch('gamelobbyloader.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ faction: factionRequest })
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            ajaxInterface.submiting = false;            
            return response.json();
        })
        .then(data => {
            if (data.error) {
                ajaxInterface.submiting = false;                
                ajaxInterface.errorAjax(null, null, data.error);
                return;
            }
            ajaxInterface.submiting = false;            
            getFactionShipsCallback(data);
        })
        .catch(error => {
            ajaxInterface.submiting = false;            
            ajaxInterface.errorAjax(null, null, error.message);
        });
    },

    react: function react() {
        alert("callback");
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

    submitSavedFleet: function submitSavedFleet(fleetname, isPublic, callback) {
        if (ajaxInterface.submiting) return;
        ajaxInterface.submiting = true;
        // Build the payload using your existing function
        const saveData = ajaxInterface.constructSavedShips(fleetname, isPublic);

        // Ensure ships is a JSON string
        if (typeof saveData.ships !== 'string') {
            saveData.ships = JSON.stringify(saveData.ships);
        }

        // Ensure there’s at least one ship
        let shipsArray;
        try {
            shipsArray = JSON.parse(saveData.ships);
        } catch (e) {
            shipsArray = [];
        }

        if (!Array.isArray(shipsArray) || shipsArray.length === 0) {
            window.confirm.error("You must have at least one ship before saving!", function () {});
            return; // stop execution
        }

        // Send the POST request
        $.ajax({
            type: 'POST',
            url: 'saveFleet.php',
            contentType: 'application/json; charset=utf-8',
            dataType: 'json',
            data: JSON.stringify(saveData),
            timeout: 15000,
            success: function(response) {
                ajaxInterface.submiting = false;

                if (response && response.error) {
                    console.error("Submit failed:", response);
                    ajaxInterface.errorAjax(null, null, response.error);
                } else {
                    ajaxInterface.successSubmit(response);

                    // ✅ Call the callback if provided
                    if (typeof callback === 'function') {
                        callback(response);
                    }
                }
            },
            error: function(xhr, status, error) {
                ajaxInterface.submiting = false;
                ajaxInterface.errorAjax(xhr, status, error);
            }
        });
    },

    constructSavedShips: function constructSavedShips(fleetname, isPublic) {

        var saveships = Array();
        var points = 0;

        for (var i in gamedata.ships) {
            var lship = gamedata.ships[i];
            if (lship.slot != gamedata.selectedSlot) continue;
            points += lship.pointCost;
        }

        for (var i in gamedata.ships) {
            var ship = gamedata.ships[i];
            var newShip = {
                'phpclass': ship.phpclass,
                'userid': ship.userid,
                'id': ship.id,
                'name': ship.name,
				'pointCostEnh': Math.round(ship.pointCostEnh),
				'pointCostEnh2': Math.round(ship.pointCostEnh2)
            };

            newShip.systems = Array();

            if (ship.userid === gamedata.thisplayer) {

                var systems = Array();

                for (var a in ship.systems) {
                    var system = ship.systems[a];

                    if (ship.flight) {

                        var fighterSystems = Array();

                        for (var c in system.systems) {
                            var fightersystem = system.systems[c];
                            var ammoArray = Array();
  
                            if (fightersystem.missileArray != null) {
                                for (var index in fightersystem.missileArray) {
                                    var amount = fightersystem.missileArray[index].amount;
                                    ammoArray[index] = amount;
                                    newShip.pointCostEnh2 += fightersystem.missileArray[index].cost * amount * ship.flightSize;                                    
                                }
                            }
							
							//fightersystem.doIndividualNotesTransfer();
							fighterSystems[c] = { 'id': fightersystem.id, 'fireOrders': fightersystem.fireOrders, 'ammo': ammoArray, "individualNotesTransfer": fightersystem.individualNotesTransfer };
                        }
						//system.doIndividualNotesTransfer();
						systems[a] = { 'id': system.id, 'systems': fighterSystems, "individualNotesTransfer": system.individualNotesTransfer };
                    } else {
                        var ammoArray = Array();
                        var fires = Array();
                        systems[a] = { 'id': system.id, 'power': system.power, 'fireOrders': fires };

                        if (system.missileArray != null) {
                            for (var index in system.missileArray) {
                                var amount = system.missileArray[index].amount;
                                ammoArray[index] = amount;
                                newShip.pointCostEnh2 += system.missileArray[index].cost * amount;
                            }
                        }
						//system.doIndividualNotesTransfer();
						systems[a] = { 'id': system.id, 'power': system.power, 'fireOrders': fires, 'ammo': ammoArray, "individualNotesTransfer": system.individualNotesTransfer };
                    }
                }

                newShip.systems = systems;

                if (ship.flight) {
                    newShip.flightSize = ship.flightSize;
                }
                
                //unit enhancements
                newShip.enhancementOptions = ship.enhancementOptions;

                saveships.push(newShip);
            }
        }

        var saveData = {
            name: fleetname,
            userid: gamedata.thisplayer,
            points: points,
            isPublic: isPublic,
            ships: saveships,
        };

        return saveData;
    },
/*
	reloadFactions: function reloadFactions(callback) {
        if (ajaxInterface.submiting) return;
        ajaxInterface.submiting = true;

		$.ajax({
			type: 'GET',
			url: 'getFactions.php',
			dataType: 'json',    // ✅ Expect JSON
			cache: false,        // ✅ Avoid stale results in some browsers
			timeout: 15000       // ✅ Network protection
		})
		.done(function (factions, textStatus, xhr) {		
			ajaxInterface.submiting = false;  
            // ✅ HTTP status check
			if (xhr.status !== 200) {
				console.error(`Failed to load factions. HTTP ${xhr.status}`);
				window.location.reload(); // fallback
				return;
			}

			// ✅ Validate JSON
			if (!factions || typeof factions !== 'object') {
				console.error("Invalid factions JSON received:", factions);
				window.location.reload(); // fallback
				return;
			}
			callback(factions);
		})
		.fail(function (xhr, textStatus, errorThrown) {		
			//let message = errorThrown || textStatus || "Unknown network error";
			//console.error("Failed to load factions:", message, xhr.responseText);
            ajaxInterface.submiting = false;  
			// ✅ Fallback to hard reload to recover
			window.location.reload();
		});
	},    
*/
	getSavedFleets: function getSavedFleets(callback) {
        if (ajaxInterface.submiting) return;
        ajaxInterface.submiting = true;

		$.ajax({
			type: 'GET',
			url: 'getSavedFleets.php',
			dataType: 'json',
			cache: false,
			timeout: 15000
		})
		.done(function(response) {
            ajaxInterface.submiting = false;            
			if (!response || !response.fleets) return callback([]);

			callback(response.fleets);
		})
		.fail(function(xhr, textStatus, errorThrown) {
            ajaxInterface.submiting = false;            
			console.error("Failed to load fleets:", errorThrown || textStatus);
			callback([]);
		});
	},

    loadSavedFleet: function loadSavedFleet(listId, callback) {
        if (ajaxInterface.submiting) return;
        ajaxInterface.submiting = true;

        $.ajax({
            type: 'POST', // POST to match PHP JSON reading
            url: 'loadSavedFleet.php',
            contentType: 'application/json; charset=utf-8',
            data: JSON.stringify({ listid: listId }),
            dataType: 'json',
            cache: false,
            timeout: 15000
        })
        .done(function(response) {
            ajaxInterface.submiting = false;             
			if (!response || !response.ships) return callback([]);
            callback(response);
        })
        .fail(function(xhr, textStatus, errorThrown) {
            ajaxInterface.submiting = false;             
            console.error("Failed to load fleet:", textStatus, errorThrown);
            callback([]);
        });
    },


    changeFleetPublic: function changeFleetPublic(id, callback) {
        if (ajaxInterface.submiting) return;
        ajaxInterface.submiting = true;      
        // Send the POST request
        $.ajax({
            type: 'POST',
            url: 'changeAvailabilityFleet.php',
            contentType: 'application/json; charset=utf-8',
            dataType: 'json',
            data: JSON.stringify({id: id}),
            timeout: 15000,
            success: function(response) {
                ajaxInterface.submiting = false;

                if (response && response.error) {
                    console.error("Submit failed:", response);
                    ajaxInterface.errorAjax(null, null, response.error);
                }

                // ✅ Call the callback if provided
                if (typeof callback === 'function') {
                    callback(response);
                }
            },
            error: function(xhr, status, error) {
                ajaxInterface.submiting = false;
                ajaxInterface.errorAjax(xhr, status, error);
            }
        });
	},


    deleteSavedFleet: function deleteSavedFleet(id, callback) {
        if (ajaxInterface.submiting) return;
        ajaxInterface.submiting = true;        
        // Send the POST request
        $.ajax({
            type: 'POST',
            url: 'deleteSavedFleet.php',
            contentType: 'application/json; charset=utf-8',
            dataType: 'json',
            data: JSON.stringify({id: id}),
            timeout: 15000,
            success: function(response) {
                ajaxInterface.submiting = false;

                if (response && response.error) {
                    console.error("Submit failed:", response);
                    ajaxInterface.errorAjax(null, null, response.error);
                } else {
                    ajaxInterface.successSubmit(response);

                    // ✅ Call the callback if provided
                    if (typeof callback === 'function') {
                        callback(response);
                    }
                }
            },
            error: function(xhr, status, error) {
                ajaxInterface.submiting = false;
                ajaxInterface.errorAjax(xhr, status, error);
            }
        });
	},


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
                                    newShip.pointCostEnh2 += fightersystem.missileArray[index].cost * amount * ship.flightSize;  
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
                                newShip.pointCostEnh2 += system.missileArray[index].cost * amount;                                  
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

        var dontPollYet = false;
        var time = 5000;  

        // detect environment
        var isLocal = (location.hostname === "localhost" || location.hostname === "127.0.0.1");
        var phase = gamedata.gamephase;        

        if(gamedata.gamephase == -2){
            for (var i in gamedata.slots) {
                var slot = gamedata.slots[i];		
                if(slot.playerid !== null && slot.playerid == gamedata.thisplayer && slot.lastphase == "-3"){
                    dontPollYet = true;
                    break;       
                }      
            }
        }
        
        if(!dontPollYet || isLocal){
            if (!ajaxInterface.submiting) ajaxInterface.requestGamedata();

            ajaxInterface.pollcount++;

            // --- base timings depending on mode ---
            if (isLocal) {
                // Local testing timings
                time = 3000;
            } else if (phase === -2) {
                // Phase -2 timings (customize as you like)
                time = 5000;
                if (ajaxInterface.pollcount > 1) time = 10000;
                if (ajaxInterface.pollcount > 3)  time = 20000;                
                if (ajaxInterface.pollcount > 40) time = 30000;
            } else {
                // In-Game timings
                time = 5000;
                if (ajaxInterface.pollcount > 1)  time = 10000;
                if (ajaxInterface.pollcount > 3)  time = 20000;
                if (ajaxInterface.pollcount > 10) time = 60000;
                if (ajaxInterface.pollcount > 40) time = 1800000;
            }

            if (ajaxInterface.pollcount > 300) {
                return;
            }
        }else{
            time = 8000;        
        }

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
        // prevent overlap if already running
        if (ajaxInterface.submiting) return;

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
                time: Date.now()
            },
            success: ajaxInterface.successRequest,
            error: ajaxInterface.errorAjax,
            complete: function () {
                // always clear flag, even on error/timeout
                ajaxInterface.submiting = false;
            }
        });
    },

    requestAllGames: function requestAllGames() {
        // prevent overlap if already running
        if (ajaxInterface.submiting) return;
        ajaxInterface.submiting = true;

        $.ajax({
            type: 'GET',
            url: 'allgames.php',
            dataType: 'json',
            data: {},
            success: ajaxInterface.successRequest,
            error: ajaxInterface.errorAjax,
            complete: function () {
                // always clear flag, even on error/timeout
                ajaxInterface.submiting = false;
            }            
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


    /* //OLD VERSION GETSHIPSFORFACTION()
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

    /* //OLD VERSION REQUESTGAMEDATA()
    requestGamedata: function requestGamedata() {
        if (ajaxInterface.submiting) return; // 🚫 skip if still running
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
*/


    /* //OLD VERSION SUBMITGAMEDATA()
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

    /* //OLD VERSION OF SUBMITSLOTACTION()
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

    /* //OLD VERSION OF POLLGAMEDATA()
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

        // detect if running locally
        var isLocal = (location.hostname === "localhost" || location.hostname === "127.0.0.1");

        // base poll time
        var time = isLocal ? 3000 : 10000; // local = 2s, remote = 10s


        if (ajaxInterface.pollcount > 3) {
            time = isLocal ? 4000 : 20000; // local faster, remote 20s           
        }

        if (ajaxInterface.pollcount > 10) {
            time = isLocal ? 6000 : 60000; //Increased from 6 secs to 1 min
        }

        if (ajaxInterface.pollcount > 40) { //Decreased from 100 polls e.g. 
           time = isLocal ? 6000 : 1800000; //Increased from 50 secs to 30 mins
        }

        //if (ajaxInterface.pollcount > 80) {
        //    time = 500000;
        //}

        if (ajaxInterface.pollcount > 300) {
            return;
        }

        ajaxInterface.poll = setTimeout(ajaxInterface.pollGamedata, time);
    },
    */

};
