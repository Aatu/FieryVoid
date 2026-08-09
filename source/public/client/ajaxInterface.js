'use strict';

window.ajaxInterface = {

    poll: null,
    pollActive: false,
    pollcount: 0,
    submiting: false,

    // Home screen
    submitingGames: false,
    currentRequest: null,
    nextRequest: null,
    lastRequestTimeGames: 0,
    debounceDelayGames: 300,

    // Fleet selection
    currentFaction: null,
    nextFaction: null,
    lastClickTime: {},
    debounceDelay: 300,

    // GLOBAL AJAX SERIAL QUEUE 🔥
    requestQueue: Promise.resolve(),

    // Blocking overlay helpers to prevent navigation during critical submissions
    showBlockingOverlay: function () {
        var overlay = document.getElementById('global-blocking-overlay');
        if (overlay) {
            overlay.style.display = 'flex';
        }
    },

    hideBlockingOverlay: function () {
        var overlay = document.getElementById('global-blocking-overlay');
        if (overlay) {
            overlay.style.display = 'none';
        }
    },
    /*
    // Blocking overlay helpers to prevent navigation during critical submissions
    showLoadingOverlay: function () {
        var overlay = document.getElementById('global-loading-overlay');
        if (overlay) {
            overlay.style.display = 'flex';
        }
    },

    hideLoadingOverlay: function () {
        var overlay = document.getElementById('global-loading-overlay');
        if (overlay) {
            overlay.style.display = 'none';
        }
    },    
    */
    getShipsForFaction: function (factionRequest, callback, errorCallback) {
        const now = Date.now();

        if (this.lastClickTime[factionRequest] &&
            now - this.lastClickTime[factionRequest] < this.debounceDelay) {
            return;
        }
        this.lastClickTime[factionRequest] = now;

        if (this.submiting) {
            this.nextFaction = { factionRequest, callback, errorCallback };
            return;
        }

        if (factionRequest === this.currentFaction) return;

        // Caching disabled to fix ship display issues. 
        // Relies on HTTP Caching (ETag/304) implemented in gamelobbyloader.php
        /*
        // Check client-side cache first
        const cacheKey = 'fv_ships_' + factionRequest;
        try {
            const cached = sessionStorage.getItem(cacheKey);
            if (cached) {
                const parsed = JSON.parse(cached);
                // Cache is valid for the session, serve immediately
                callback(parsed);
                return;
            }
        } catch (e) {
            // Cache read failed, proceed with request
            console.warn('Cache read failed:', e);
        }
        */

        this._sendRequest(factionRequest, callback, errorCallback);
    },

    _sendRequest: function (factionRequest, callback, errorCallback) {
        // Validate faction before sending request
        if (!factionRequest) {
            console.warn('_sendRequest called with empty faction, ignoring');
            this.submiting = false;
            return;
        }

        this.currentFaction = factionRequest;
        this.nextFaction = null;
        this.submiting = true;

        // Cache-bust per faction: append the static JSON file's version (mtime,
        // emitted into window.factionVersions by gamelobby.php) to the URL. A new
        // patch regenerates static/json/<faction>.json with a fresh mtime, so the
        // URL changes and the browser cannot serve a stale cached response —
        // robust where ETag/Last-Modified revalidation gets skipped (mobile/BFCache).
        var loaderUrl = 'gamelobbyloader.php';
        var factionVersion = (window.factionVersions || {})[factionRequest];
        if (factionVersion) {
            loaderUrl += '?v=' + encodeURIComponent(factionVersion);
        }

        // Use _doAjaxWithRetry to handle transient 507 errors
        this._doAjaxWithRetry({
            type: 'POST',
            url: loaderUrl,
            dataType: 'json',
            contentType: 'application/json',
            data: JSON.stringify({ faction: String(factionRequest) }),
            timeout: 30000,
            retryCodes: [503, 507], // 400 is fatal, do not retry
            maxAttempts: 3, // Limit retries to prevent piling on load

            success: (data) => {
                if (data.error) {
                    this.errorAjax(null, null, data.error);
                } else {
                    // Try to cache the response for future use
                    const cacheKey = 'fv_ships_' + factionRequest;
                    try {
                        const jsonData = JSON.stringify(data);
                        sessionStorage.setItem(cacheKey, jsonData);
                    } catch (e) {
                        // Quota exceeded - try clearing old faction caches first
                        if (e.name === 'QuotaExceededError') {
                            try {
                                for (let i = sessionStorage.length - 1; i >= 0; i--) {
                                    const key = sessionStorage.key(i);
                                    if (key && key.startsWith('fv_ships_')) {
                                        sessionStorage.removeItem(key);
                                    }
                                }
                                sessionStorage.setItem(cacheKey, JSON.stringify(data));
                            } catch (e2) {
                                // Still too large - silently skip caching
                            }
                        }
                    }
                    callback(data);
                }
            },

            error: (xhr, status, error) => {
                // Silently ignore transient errors - user can try again
                const ignoredStatuses = [400, 503, 507]; // 400=bad request (no popup), 503/507=server busy
                if (xhr && ignoredStatuses.includes(xhr.status)) {
                    console.log('Faction load issue (status ' + xhr.status + '), try again');
                    // Don't call errorAjax for transient errors
                } else {
                    this.errorAjax(xhr, status, error);
                }

                if (errorCallback) {
                    errorCallback(xhr, status, error);
                }
            },

            complete: () => {
                this.currentFaction = null;
                this.submiting = false;

                if (this.nextFaction) {
                    const { factionRequest: nextF, callback: nextCb, errorCallback: nextErr } = this.nextFaction;
                    this.nextFaction = null;
                    this._sendRequest(nextF, nextCb, nextErr);
                }
            }
        });
    },




    ajaxWithRetry: function (options, attempt = 1) {
        const deferred = $.Deferred();

        // Chain execution onto the queue - only the initial request goes through queue
        ajaxInterface.requestQueue = ajaxInterface.requestQueue.then(() => {
            return new Promise((resolve) => {
                // Call internal method that handles the actual request and retries
                ajaxInterface._doAjaxWithRetry(options, attempt)
                    .done(function () { deferred.resolveWith(this, arguments); })
                    .fail(function () { deferred.rejectWith(this, arguments); })
                    .always(() => resolve()); // Queue resolved when complete (success or fail)
            });
        });

        return deferred.promise();
    },

    // Internal method - handles the actual AJAX call and retries (bypasses queue)
    _doAjaxWithRetry: function (options, attempt) {
        const maxAttempts = options.maxAttempts || 5;
        const baseDelay = 200;
        const deferred = $.Deferred();
        let isRetrying = false;

        $.ajax({
            ...options,

            success: function (data, textStatus, xhr) {
                if (options.success) options.success(data, textStatus, xhr);
                deferred.resolve(data, textStatus, xhr);
            },

            error: function (xhr, textStatus, errorThrown) {
                // Retry if status matches allowed codes and attempts remain
                const retryCodes = options.retryCodes || [503, 507];

                // Retry if status matches allowed codes, OR is network error (0)
                // Note: Not retrying on 'timeout' for faction loads padding UI with errors
                const isRetryableCode = xhr && retryCodes.includes(xhr.status);
                const isNetworkError = xhr && xhr.status === 0 && textStatus !== 'abort';

                if ((isRetryableCode || isNetworkError) && attempt < maxAttempts) {
                    const delay = baseDelay * Math.pow(2, attempt) + Math.random() * 50;
                    console.warn(`AJAX issue (${textStatus || xhr.status}), retrying in ${Math.round(delay)}ms (attempt ${attempt}/${maxAttempts})`);
                    isRetrying = true;

                    setTimeout(() => {
                        // RECURSION: Call self directly, bypassing the queue
                        ajaxInterface._doAjaxWithRetry(options, attempt + 1)
                            .done(function () { deferred.resolveWith(this, arguments); })
                            .fail(function () { deferred.rejectWith(this, arguments); });
                    }, delay);
                    return;
                }

                // Final failure - call error handler and release slot
                if (options.error) options.error(xhr, textStatus, errorThrown);
                deferred.reject(xhr, textStatus, errorThrown);
            },

            complete: function (xhr, status) {
                // Only call complete when not retrying
                if (!isRetrying && options.complete) {
                    options.complete(xhr, status);
                }
            }
        });

        return deferred.promise();
    },




    /* //Replaced version 25.11.25 - DK
    _sendRequest: function(factionRequest, callback) {
        this.currentFaction = factionRequest;
        this.nextFaction = null;
        this.submiting = true;

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
            } else {
                callback(data);
            }
        })
        .catch(error => this.errorAjax(null, null, error.message))
        .finally(() => {
            // mark request finished
            this.currentFaction = null;
            this.submiting = false;

            // If user clicked again while busy, run that latest request now
            if (this.nextFaction) {
                const { factionRequest: nextF, callback: nextCb } = this.nextFaction;
                this.nextFaction = null; // clear buffer
                this._sendRequest(nextF, nextCb);
            }
        });
    },

    ajaxWithRetry: function ajaxWithRetry(options, attempt = 1) {
        const maxAttempts = 5;
        const baseDelay = 200;

        const jqXHR = $.ajax({
            ...options,
            error: function(xhr, status, error) {
                if (xhr.status === 507 && attempt <= maxAttempts) {
                    const delay = baseDelay * Math.pow(2, attempt) + Math.random() * 100;
                    console.warn(`507 error, retrying in ${Math.round(delay)}ms (attempt ${attempt})`);
                    setTimeout(() => ajaxInterface.ajaxWithRetry(options, attempt + 1), delay);
                } else if (options.error) {
                    options.error(xhr, status, error);
                }
            }
        });

        return jqXHR; // ⚠ critical
    },
    */

    //New version - DK July 2025
    submitGamedata: function submitGamedata() {
        if (ajaxInterface.submiting) return;

        ajaxInterface.submiting = true;
        ajaxInterface.showBlockingOverlay();

        // ✅ Build the payload using your existing function
        const gd = ajaxInterface.construcGamedata();

        // ✅ Force ships into a proper JSON string
        if (typeof gd.ships !== 'string') {
            gd.ships = JSON.stringify(gd.ships);
        }

        // ✅ Use JSON to avoid PHP array serialization quirks
        ajaxInterface.ajaxWithRetry({
            type: 'POST',
            url: 'gamedata.php',
            contentType: 'application/json; charset=utf-8', // ✅ send JSON body
            dataType: 'json',                               // ✅ expect JSON back
            data: JSON.stringify(gd),                       // ✅ encode full payload
            timeout: 15000,                                 // ✅ prevent long hangs
            success: function (response) {
                ajaxInterface.submiting = false;
                ajaxInterface.hideBlockingOverlay();

                if (response && response.error) {
                    console.error("Submit failed:", response);
                    ajaxInterface.errorAjax(null, null, response.error);
                } else {
                    ajaxInterface.successSubmit(response);
                }
            },
            error: function (xhr, status, error) {
                ajaxInterface.submiting = false;
                ajaxInterface.hideBlockingOverlay();
                ajaxInterface.errorAjax(xhr, status, error);
            }
        });

        // ✅ Indicate we’re waiting for the server response
        gamedata.goToWaiting();
    },

    submitSavedFleet: function submitSavedFleet(fleetname, isPublic, callback, opts) {
        if (ajaxInterface.submiting) return;
        ajaxInterface.submiting = true;
        // Build the payload using your existing function
        const saveData = ajaxInterface.constructSavedShips(fleetname, isPublic, opts);

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
            ajaxInterface.submiting = false;
            window.confirm.fleetNotice("You must have at least one unit before saving a fleet.");
            return; // stop execution
        }

        // Send the POST request
        ajaxInterface.ajaxWithRetry({
            type: 'POST',
            url: 'saveFleet.php',
            contentType: 'application/json; charset=utf-8',
            dataType: 'json',
            data: JSON.stringify(saveData),
            timeout: 15000,
            success: function (response) {
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
            error: function (xhr, status, error) {
                ajaxInterface.submiting = false;
                ajaxInterface.errorAjax(xhr, status, error);
            }
        });
    },

    /* Is this unit eligible to be written into a saved fleet?
       Lobby: everything the player owns. game.php ("Save Current Fleet", PREBATTLE_DAMAGE_PLAN
       §7.2): the SURVIVORS only, minus the mid-battle artefacts that make no sense in a fleet
       list - destroyed/docked units, launched-fighter "Split" rows, spent mines and Chameleon
       phantom sheets (which use NEGATIVE ids). */
    isSaveableFleetShip: function isSaveableFleetShip(ship) {
        if (!ship) return false;
        if (ship.userid !== gamedata.thisplayer) return false;

        /* TERRAIN is scenery, not fleet. Map terrain belongs to userid -5 and is already
           excluded by the ownership test above, but terrain a player placed themselves is
           bought into their own slot and rides their team, so nothing else here catches it -
           an asteroid field would ride along in every saved fleet and be re-bought in the
           next lobby. Excluded on BOTH pages: a fleet list is ships. (user report 2026-08-08) */
        if (gamedata.isTerrain(ship.shipSizeClass, ship.userid)) return false;

        //lobby: no battle state to filter on
        if (gamedata.gamephase === -2) return true;

        if (ship.id < 0) return false;                                  //Chameleon phantom sheet
        if (ship.removed) return false;                                 //docked into a hangar
        if (shipManager.isDestroyed(ship)) return false;                //dead, or a flight with no survivors
        if (ship.mine && ship.spawned !== -1) return false;             //mine laid during the battle

        return true;
    },

    /* Collapse the saveable units into the ROWS a fleet list holds.
       Everything is one row per unit, EXCEPT mines in a live game: the lobby buys them in
       bulk (one object carrying bulkBuy = N) and BuyingGamePhase mints N separate ships
       from it, so saving a battle's survivors one row at a time reloaded a fleet of ten
       mines as ten separate units (user report 2026-08-08). Regrouping by class puts them
       back in the shape they were bought in - and each copy's structure damage rides
       along as its ordinal in the `mne` bucket.

       Grouped only in a live game: in the lobby they are ALREADY bulk rows, and merging
       two separate purchases of the same class would silently fuse two lines of the
       player's fleet list into one.

       Returns [{ ship, members }] - `ship` is the representative (the first, whose
       enhancements/ammo/name the row takes), `members` every unit it stands for. */
    groupSaveableShips: function groupSaveableShips(ships) {
        var groups = [];
        var mineGroups = {};
        var groupMines = window.gamedata && gamedata.gamephase !== -2;

        for (var i = 0; i < ships.length; i++) {
            var ship = ships[i];

            if (groupMines && ship.mine) {
                var key = ship.phpclass;
                if (!mineGroups[key]) {
                    mineGroups[key] = { ship: ship, members: [] };
                    groups.push(mineGroups[key]);
                }
                mineGroups[key].members.push(ship);
                continue;
            }

            groups.push({ ship: ship, members: [ship] });
        }

        return groups;
    },

    /* opts (all optional):
         includeTransient : also save one-turn / self-expiring criticals (game.php's
                            "save temporary critical effects" checkbox, off by default).
       An options object rather than a positional flag, matching loadSavedFleet, so a
       future third choice does not re-sign this at every call site. */
    constructSavedShips: function constructSavedShips(fleetname, isPublic, opts) {

        var saveships = Array();
        var saveable = [];
        var points = 0;

        /* ONE pass, one isSaveableFleetShip call per ship: it is the filter for BOTH the
           units written and the points figure, and on game.php it walks every system of
           every ship (shipManager.isDestroyed).
           gamedata.selectedSlot is a LOBBY concept and is null in game.php, which used to
           make every ship fail the points filter and save the fleet at 0 points - so there
           the saveable set simply IS the fleet. Base pointCost only: damage is not a
           discount (D2). */
        var slot = gamedata.selectedSlot;
        var bySlot = (slot !== null && slot !== undefined);

        for (var i in gamedata.ships) {
            var lship = gamedata.ships[i];
            if (!ajaxInterface.isSaveableFleetShip(lship)) continue;
            saveable.push(lship);

            if (bySlot && lship.slot != slot) continue;
            //A lobby mine row is N mines at pointCost each - counting it once stored a
            //fleet whose `points` was short by (bulkBuy - 1) units, and that figure is
            //what the affordability check on load compares against.
            points += lship.pointCost * (lship.mine ? (parseInt(lship.bulkBuy, 10) || 1) : 1);
        }

        var groups = ajaxInterface.groupSaveableShips(saveable);
        for (var g = 0; g < groups.length; g++) {
            var ship = groups[g].ship;
            var members = groups[g].members;
            var newShip = {
                'phpclass': ship.phpclass,
                'userid': ship.userid,
                'team': ship.team,
                'id': ship.id,
                'name': ship.name,
                'pointCostEnh': Math.round(ship.pointCostEnh),
                'pointCostEnh2': Math.round(ship.pointCostEnh2)
            };

            if (ship.bulkBuy !== undefined) newShip.bulkBuy = ship.bulkBuy;
            //A regrouped set of live mines is bought back as one bulk of that many.
            if (members.length > 1) newShip.bulkBuy = members.length;

            newShip.systems = Array();

            if (ship.userid === gamedata.thisplayer) {

                var systems = Array();
                //Saving OUT of a live game records the SURVIVING flight size (D8): a lost
                //fighter is expressed by a smaller flight, not by a wreck riding along. In
                //the lobby every fighter is alive, so the two agree.
                var saveFlightSize = !ship.flight ? 0
                    : ((window.battleDamage && gamedata.gamephase !== -2)
                        ? battleDamage.survivingFlightSize(ship)
                        : ship.flightSize);

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
                                    newShip.pointCostEnh2 += fightersystem.missileArray[index].cost * amount * saveFlightSize;
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
                    newShip.flightSize = saveFlightSize;
                }

                //unit enhancements
                newShip.enhancementOptions = ship.enhancementOptions;

                /* Battle damage & criticals (PREBATTLE_DAMAGE_PLAN.md §6 / §7.2). In the
                   lobby this is the payload the player authored; in a live game it is
                   summariseShip's collapse of the battle so far - and for a flight its
                   ordinals are numbered over the survivors, matching flightSize above. */
                if (window.battleDamage) {
                    var damagePayload = (gamedata.gamephase === -2)
                        ? ship.preBattleDamage
                        : ajaxInterface.summariseGroup(members, opts);
                    if (!battleDamage.isEmpty(damagePayload)) {
                        newShip.preBattleDamage = damagePayload;
                    }
                }

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

    /* The wire-format payload for one ROW of the fleet list. A single unit is just
       summariseShip; a REGROUPED set of live mines has each copy's structure damage
       renumbered as its ordinal in the bulk, matching how BuyingGamePhase writes the
       rows back out (one tac_ship per ordinal). */
    summariseGroup: function summariseGroup(members, opts) {
        if (!members || members.length === 0) return {};
        if (members.length === 1) return battleDamage.summariseShip(members[0], opts);

        var mne = {};
        for (var i = 0; i < members.length; i++) {
            var payload = battleDamage.summariseShip(members[i], opts);
            //summariseShip gives a live mine its damage as ordinal 1 of a bulk of one.
            var entry = payload && payload.mne && payload.mne['1'];
            if (entry && entry.d) mne[String(i + 1)] = { d: entry.d };
        }

        return Object.keys(mne).length ? { mne: mne } : {};
    },

    getSavedFleets: function getSavedFleets(callback) {
        if (ajaxInterface.submiting) return;
        ajaxInterface.submiting = true;

        ajaxInterface.ajaxWithRetry({
            type: 'GET',
            url: 'getSavedFleets.php',
            dataType: 'json',
            cache: false,
            timeout: 15000
        })
            .done(function (response) {
                ajaxInterface.submiting = false;
                if (!response || !response.fleets) return callback([]);

                callback(response.fleets);
            })
            .fail(function (xhr, textStatus, errorThrown) {
                ajaxInterface.submiting = false;
                console.error("Failed to load fleets:", errorThrown || textStatus);
                callback([]);
            });
    },

    /* opts (all optional) - an OBJECT, not positional booleans, so a future third kind of
       saved state does not re-sign the function at every call site:
         includeDamage    : load the fleet's saved battle damage      (default true)
         includeCriticals : load the fleet's saved critical effects   (default true)
       Legacy shape loadSavedFleet(listId, callback) still works. */
    loadSavedFleet: function loadSavedFleet(listId, opts, callback) {
        if (typeof opts === 'function') { callback = opts; opts = {}; }
        opts = opts || {};

        if (ajaxInterface.submiting) return;
        ajaxInterface.submiting = true;

        var body = { listid: listId };
        if (opts.includeDamage !== undefined) body.includeDamage = Boolean(opts.includeDamage);
        if (opts.includeCriticals !== undefined) body.includeCriticals = Boolean(opts.includeCriticals);

        ajaxInterface.ajaxWithRetry({
            type: 'POST', // POST to match PHP JSON reading
            url: 'loadSavedFleet.php',
            contentType: 'application/json; charset=utf-8',
            data: JSON.stringify(body),
            dataType: 'json',
            cache: false,
            timeout: 15000
        })
            .done(function (response) {
                ajaxInterface.submiting = false;
                if (!response || !response.ships) return callback([]);
                callback(response);
            })
            .fail(function (xhr, textStatus, errorThrown) {
                ajaxInterface.submiting = false;
                console.error("Failed to load fleet:", textStatus, errorThrown);
                callback([]);
            });
    },


    /* Per-system critical CATALOGUE + cascade traits for one ship class, for the lobby's
       pre-battle damage editor (PREBATTLE_DAMAGE_PLAN.md §11.2).

       Deliberately NOT routed through ajaxInterface.submiting: that flag serialises the
       page's ONE user-initiated request at a time, and this is a background lookup that
       fires while a menu opens - sharing the flag would make a catalogue fetch swallow a
       fleet load, or vice versa. battleDamage.loadCatalogue does its own de-duplication
       per ship class. The callback always fires, with {success:false} on failure, so the
       caller can cache the miss and stop asking. */
    getSystemCriticals: function getSystemCriticals(phpclass, flightSize, callback) {
        ajaxInterface.ajaxWithRetry({
            type: 'POST',
            url: 'systemCriticals.php',
            contentType: 'application/json; charset=utf-8',
            data: JSON.stringify({ phpclass: phpclass, flightSize: flightSize || 1 }),
            dataType: 'json',
            cache: false,
            timeout: 15000
        })
            .done(function (response) {
                callback(response || { success: false });
            })
            .fail(function (xhr, textStatus, errorThrown) {
                console.error("Failed to load system criticals:", textStatus, errorThrown);
                callback({ success: false });
            });
    },

    changeFleetPublic: function changeFleetPublic(id, callback) {
        if (ajaxInterface.submiting) return;
        ajaxInterface.submiting = true;
        // Send the POST request
        ajaxInterface.ajaxWithRetry({
            type: 'POST',
            url: 'changeAvailabilityFleet.php',
            contentType: 'application/json; charset=utf-8',
            dataType: 'json',
            data: JSON.stringify({ id: id }),
            timeout: 15000,
            success: function (response) {
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
            error: function (xhr, status, error) {
                ajaxInterface.submiting = false;
                ajaxInterface.errorAjax(xhr, status, error);
            }
        });
    },


    deleteSavedFleet: function deleteSavedFleet(id, callback) {
        if (ajaxInterface.submiting) return;
        ajaxInterface.submiting = true;
        // Send the POST request
        ajaxInterface.ajaxWithRetry({
            type: 'POST',
            url: 'deleteSavedFleet.php',
            contentType: 'application/json; charset=utf-8',
            dataType: 'json',
            data: JSON.stringify({ id: id }),
            timeout: 15000,
            success: function (response) {
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
            error: function (xhr, status, error) {
                ajaxInterface.submiting = false;
                ajaxInterface.errorAjax(xhr, status, error);
            }
        });
    },


    //New version for PHP8
    submitSlotAction: function submitSlotAction(action, slotid, callback) {
        if (ajaxInterface.submiting) return;
        ajaxInterface.submiting = true;
        ajaxInterface.showBlockingOverlay();

        ajaxInterface.ajaxWithRetry({
            type: 'POST',
            url: 'slot.php',
            dataType: 'json',
            contentType: 'application/json',
            data: JSON.stringify({
                action: action,
                gameid: gamedata.gameid,
                slotid: slotid
            }),
            timeout: 15000, // ✅ prevent hanging requests
        })
            .done(function (response, textStatus, xhr) {
                ajaxInterface.submiting = false;
                ajaxInterface.hideBlockingOverlay();

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
                ajaxInterface.hideBlockingOverlay();
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
                'team': ship.team,
                'slot': ship.slot,
                'id': ship.id,
                'name': ship.name,
                'pointCostEnh': Math.round(ship.pointCostEnh),
                'pointCostEnh2': Math.round(ship.pointCostEnh2)
            };

            if (ship.bulkBuy !== undefined) newShip.bulkBuy = ship.bulkBuy;

            newShip.movement = Array();
            newShip.EW = Array();
            newShip.systems = Array();

            if (ship.userid === gamedata.thisplayer) {
                if (!(Object.keys(ship.attached).length !== 0 && !ship.detached)) {
                    /* Ascending + push, NOT descending + assign-at-source-index. Writing
                       each kept move back at its ORIGINAL index left a hole wherever a
                       previous-turn move was skipped, so JSON.stringify emitted leading
                       nulls and getShipsFromJSON turned each one into a junk MovementOrder
                       (id -1, type null, turn 0, hex 0,0). submitMovement drops those on its
                       turn check so the DB stayed clean, but MovementGamePhase's
                       "$activeShip->movement = $ship->movement" copies them onto the
                       authoritative ship - so the submit RESPONSE carried them and
                       consumeMovement read defaultPosition off a phantom move at hex (0,0).
                       Ascending order is required, not incidental: submitMovement inserts in
                       array order and the resulting row ids are what orders replay. */
                    for (var a = 0; a < ship.movement.length; a++) {
                        var move = ship.movement[a];
                        if (move.turn == gamedata.turn) {
                            newShip.movement.push(move);
                        }
                    }
                }

                //Same index-assign defect as the movement loop above. Benign today (submitEW
                //skips entries whose turn isn't the current one, and validateEW is a no-op
                //stub), but it padded the payload with the same nulls.
                for (var a = 0; a < ship.EW.length; a++) {
                    var ew = ship.EW[a];
                    if (ew.turn == gamedata.turn) {
                        newShip.EW.push(ew);
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

                            //Some fighter systems CAN be boosted now
                            for (var d = fightersystem.power.length - 1; d >= 0; d--) {
                                var power = fightersystem.power[d];
                                if (power.turn < gamedata.turn) {
                                    fightersystem.power.splice(d, 1);
                                }
                            }

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
                            fighterSystems[c] = { 'id': fightersystem.id, 'fireOrders': fightersystem.fireOrders, 'ammo': ammoArray, "individualNotesTransfer": fightersystem.individualNotesTransfer, 'power': fightersystem.power, };
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

                //Pre-battle damage (PREBATTLE_DAMAGE_PLAN.md §6). Read ONLY by
                //BuyingGamePhase::process, so this is inert in every other phase.
                //preBattleAvailable is deliberately NOT sent: it records what a saved fleet
                //HAD on offer, not what the player chose to load, and must never be written.
                if (window.battleDamage && !battleDamage.isEmpty(ship.preBattleDamage)) {
                    newShip.preBattleDamage = ship.preBattleDamage;
                }

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
            window.confirm.exception(data, function () { });
            gamedata.waiting = false;
        } else {
            gamedata.parseServerData(data);
        }
    },

    successRequest: function successRequest(data) {
        ajaxInterface.submiting = false;
        if (data && data.error) {
            // "Omitting required data" is what the server returns for a gameid-less
            // request (e.g. a stray poll during a page restore before gamedata is
            // ready). It's not a real error worth interrupting the user for — the
            // next poll cycle will refetch once a gameid exists. Ignore quietly.
            if (data.error === "Omitting required data") return;

            window.confirm.exception(data, function () { });
            gamedata.waiting = false;
            return;
        }
        gamedata.parseServerData(data);
    },

    errorAjax: function errorAjax(jqXHR, textStatus, errorThrown) {
        console.dir(jqXHR);
        console.dir(errorThrown);
        window.confirm.exception({ error: "AJAX error: " + textStatus }, function () { });
    },

    startPollingGamedata: function startPollingGamedata() {

        // Guard against starting a second concurrent poll loop. The timer handle
        // lives on ajaxInterface.poll (set by setTimeout in pollGamedata); the old
        // check read gamedata.poll, which is never set, so this guard never fired
        // and double-calls leaked overlapping pollers.
        if (ajaxInterface.poll != null) {
            return;
        }

        ajaxInterface.pollActive = true;
        ajaxInterface.pollcount = 0;

        ajaxInterface.pollGamedata();
    },

    stopPolling: function stopPolling() {
        if (ajaxInterface.poll) {
            clearTimeout(ajaxInterface.poll);
        }

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

        // Safety: Reset stuck submiting flag (e.g. after tab sleep killed the XHR)
        if (ajaxInterface.submiting && ajaxInterface._lastPollTime &&
            Date.now() - ajaxInterface._lastPollTime > 30000) {
            console.warn("Polling: Resetting stuck submiting flag");
            ajaxInterface.submiting = false;
        }

        var time = 4000;

        // detect environment
        var isLocal = (location.hostname === "localhost" || location.hostname === "127.0.0.1");
        var phase = gamedata.gamephase;


        // OPTIMIZATION: Throttling for background tabs
        if (document.hidden && !isLocal) {
            if (!ajaxInterface.submiting) ajaxInterface.requestGamedata();
            // Slow down to 1 minute, don't increment pollcount (pause decay)
            ajaxInterface.poll = setTimeout(ajaxInterface.pollGamedata, 60000);
            return;
        }

        if (phase === -2 && gamedata.rules && gamedata.rules.fleetTest === 1) return; //Don't poll for Fleet Test games.

        if (!ajaxInterface.submiting) ajaxInterface.requestGamedata();
        ajaxInterface.pollcount++;

        // --- base timings depending on mode ---
        if (isLocal) {
            // Local testing timings
            time = 3000;
        } else if (phase === -2) {
            var notReadiedYet = false;
            for (var i in gamedata.slots) {
                var slot = gamedata.slots[i];
                if (slot.playerid !== null && slot.playerid == gamedata.thisplayer && slot.lastphase == "-3") {
                    notReadiedYet = true; //Has not readied all slots yet.
                    break;
                }
            }
            // Phase -2 timings (customize as you like)
            if (notReadiedYet) {
                time = 20000;
            } else {
                time = 4000;
                if (ajaxInterface.pollcount > 3) time = 6000;
                if (ajaxInterface.pollcount > 6) time = 8000;
                if (ajaxInterface.pollcount > 15) time = 30000;
                if (ajaxInterface.pollcount > 40) time = 1800000;
            }
        } else {
            // In-Game timings
            time = 4000;
            if (ajaxInterface.pollcount > 3) time = 6000;
            if (ajaxInterface.pollcount > 6) time = 8000;
            if (ajaxInterface.pollcount > 15) time = 30000;
            if (ajaxInterface.pollcount > 40) time = 1800000;
        }

        if (ajaxInterface.pollcount > 300) {
            ajaxInterface.stopPolling();
            return;
        }


        ajaxInterface.poll = setTimeout(ajaxInterface.pollGamedata, time);
    },

    requestGamedata: function requestGamedata() {
        // No gameid means gamedata was never parsed (e.g. an error/empty baked
        // snapshot, or a restore firing before the inline parse ran). Firing the
        // fetch anyway sends gameid-less request that the server answers with
        // {"error":"Omitting required data"} — surfaced to the user as a bogus
        // "SERVER ERROR" popup. Nothing useful to fetch without a gameid, so skip.
        if (!gamedata.gameid) return;

        const now = Date.now();
        const lastRequest = parseInt(localStorage.getItem('fv_lastTacGamedataRequest')) || 0;

        // F5 Spam protection: 500ms debounce across reloads
        if (now - lastRequest < 500) return;
        localStorage.setItem('fv_lastTacGamedataRequest', now);

        // prevent overlap if already running
        if (ajaxInterface.submiting) return;

        ajaxInterface.submiting = true;
        ajaxInterface._lastPollTime = now;

        ajaxInterface.ajaxWithRetry({
            type: 'GET',
            url: 'gamedata.php',
            dataType: 'json',
            timeout: 20000, // Prevent indefinite hang on background tab / mobile sleep
            maxAttempts: 2, // Limit retries for in-game polling too
            data: {
                turn: gamedata.turn,
                phase: gamedata.gamephase,
                activeship: gamedata.activeship,
                gameid: gamedata.gameid,
                playerid: gamedata.thisplayer,
                last_time: gamedata.lastUpdateTimestamp || 0,
                time: Date.now()
            },
            success: ajaxInterface.successRequest,
            error: function (xhr, textStatus, errorThrown) {
                // Silent for polling — next poll cycle will retry automatically
                console.warn("Gamedata poll failed:", textStatus, errorThrown);
            },
            complete: function () {
                // always clear flag, even on error/timeout
                ajaxInterface.submiting = false;
            }
        });
    },

    startPollingGames: function () {
        // Polling removed as per user request (games.php loads data via PHP)
    },

    // Polling entry point for home screen
    pollGames: function () {
        if (gamedata.waiting === false) return;
        if (!gamedata.animating) {
            //animation.animateWaiting();
            ajaxInterface.requestAllGames();
        }
    },

    requestAllGames: function () {
        const now = Date.now();
        const lastRequest = parseInt(localStorage.getItem('fv_lastGamesRequest')) || 0;

        // Debounce rapid triggers (using persistent storage for F5 spam protection)
        if (now - lastRequest < ajaxInterface.debounceDelayGames) return;
        localStorage.setItem('fv_lastGamesRequest', now);

        ajaxInterface.lastRequestTimeGames = now;

        // Defensive check: prevent overlap if already running
        if (ajaxInterface.submitingGames) {
            // Queue only the last requested call
            ajaxInterface.nextRequest = {};
            return;
        }

        // Mark as submitting (defensive)
        ajaxInterface.submitingGames = true;
        //ajaxInterface.submiting = true;  // your original flag

        // Send the AJAX request
        ajaxInterface._sendGameRequest();
    },

    _sendGameRequest: function () {
        ajaxInterface.currentRequest = {};  // placeholder for inflight request
        ajaxInterface.nextRequest = null;

        ajaxInterface.ajaxWithRetry({
            type: 'GET',
            url: 'allgames.php',
            dataType: 'json',
            maxAttempts: 2, // Limit retries to prevent piling on load
            data: {},
            success: ajaxInterface.successRequest,
            error: ajaxInterface.errorAjax,
            complete: () => {
                // Clear flags when request finishes
                ajaxInterface.submitingGames = false;
                //ajaxInterface.submiting = false;
                ajaxInterface.currentRequest = null;

                // If a request was queued while this ran, send it now
                if (ajaxInterface.nextRequest) {
                    ajaxInterface.nextRequest = null;
                    ajaxInterface._sendGameRequest();
                }
            }
        });
    },


    // Recent Games window on games.php. Takes callbacks rather than hardcoding a
    // renderer: the window owns its own loading/error states and must NOT raise the
    // global exception dialog on a failed fetch (it shows "Try again" in place instead).
    // Replaces getFirePhaseGames, whose endpoint built a full TacGamedata per row.
    getRecentGames: function getRecentGames(onSuccess, onError) {

        ajaxInterface.ajaxWithRetry({
            type: 'GET',
            url: 'recentgameslist.php',
            dataType: 'json',
            data: {},
            success: onSuccess,
            error: onError || ajaxInterface.errorAjax
        });
    },

    callServer: function (method, args, callback, errorCallback) {
        // Simplified generic call, similar to older versions or expected by some simpler logic
        // This is a wrapper to use standard ajax or the retry mechanism

        // Construct standard payload if method is class::method
        // But here we might just want a simple POST to a generic handler or specific scripts.
        // Given existing code structure, we don't have a single entry point for "Manager::method".
        // HOWEVER, `gamelobbyloader.php` seems to take various requests, or `gamedata.php`.

        // Wait, looking at the plan: "Fetch ladder standings (via ajaxInterface -> Manager)".
        // The codebase doesn't seem to have a generic RPC mechanism exposed to public JS this easily.
        // We implemented `getLadderStandings` in `Manager.php`.
        // BUT `Manager.php` is server-side. Accessing it requires a public PHP script.

        // I need to implement a public entry point for this, OR reuse an existing one.
        // `gamedata.php` calls `Manager::submitTacGamedata` or returns gamedata.
        // `allgames.php` calls `Manager::getTacGames`.

        // I need a new public script `ladderstandings.php` or similar to bridge the gap.
        // So `callServer` here will specifically route to that.

        if (method === "Manager::getLadderStandings") {
            ajaxInterface.ajaxWithRetry({
                type: 'GET',
                url: 'ladderstandings.php',
                dataType: 'json',
                success: function (data) {
                    if (callback) callback(data);
                },
                error: function (xhr, status, error) {
                    if (errorCallback) errorCallback(xhr, status, error);
                    else ajaxInterface.errorAjax(xhr, status, error);
                }
            });
            return;
        }

        if (method === "Manager::getLadderHistory") {
            var playerid = args[0];
            ajaxInterface.ajaxWithRetry({
                type: 'GET',
                url: 'ladderstandings.php',
                data: { action: 'history', playerid: playerid },
                dataType: 'json',
                success: function (data) {
                    if (callback) callback(data);
                },
                error: function (xhr, status, error) {
                    if (errorCallback) errorCallback(xhr, status, error);
                    else ajaxInterface.errorAjax(xhr, status, error);
                }
            });
            return;
        }

        console.error("Unknown method in callServer: " + method);
    }

};

// Tab reactivation: Immediately poll when user returns to the tab
document.addEventListener('visibilitychange', function () {
    if (!document.hidden && typeof gamedata !== 'undefined' &&
        gamedata.waiting && ajaxInterface.pollActive) {
        // Tab just became visible — do an immediate poll and reset decay
        ajaxInterface.pollcount = 0;
        if (!ajaxInterface.submiting) {
            ajaxInterface.requestGamedata();
        }
    }
});
