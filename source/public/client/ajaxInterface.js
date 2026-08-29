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
       list - destroyed units, spent mines and Chameleon phantom sheets (which use NEGATIVE ids).

       ⭐ A unit sitting in a HANGAR counts as a survivor (user report 2026-08-23). A docked
       fighter flight and a rail-parked LCV are both `removed` ship rows - off the board, but
       alive, undamaged by being stowed and part of the fleet in every other accounting
       (fleetList.js renders them as "Docked", combat value included). Leaving them out saved a
       carrier without its air wing. isDestroyedByDamage is what makes that possible: plain
       isDestroyed folds `removed` in, so it cannot tell a stowed unit from a dead one. */
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
        if (shipManager.isDestroyedByDamage(ship)) return false;        //dead, or a flight with no survivors
        if (ship.mine && ship.spawned !== -1) return false;             //mine laid during the battle
        if (ship.removed && !ajaxInterface.isSaveableDockedShip(ship)) return false;

        return true;
    },

    /* The two docked units a fleet list must NOT re-buy. Reached only for a `removed`
       (in-a-hangar) unit that is otherwise alive - see isSaveableFleetShip.

       1. An auto-filled faction DEFAULT SHUTTLE was never bought: populateInitialHangarUsage
          issues it free on turn 1 of every battle and will do so again in the next one, so
          saving one mints a phantom unit that then arrives twice. (Only the ones that LAUNCHED
          and re-docked are ship rows at all - a shuttle that never left its bay is an anonymous
          hangarUsage entry with no ship row, so it was never a candidate. Armed shuttles are
          real purchases and are NOT in isDefaultShuttleEntry's class list, so they still save.)
       2. A unit whose CARRIER LEFT through a jump vortex went with it. Save Fleet already
          excludes the carrier itself as departed, so its cargo has to go the same way.
       A carrier that was DESTROYED needs no test here: the server either ejects the contents
       (which un-removes them) or kills them with the ship, so they fail the damage test above. */
    isSaveableDockedShip: function isSaveableDockedShip(ship) {
        if (gamedata.isDefaultShuttleEntry(ship)) return false;
        if (ajaxInterface.isDepartedWithCarrier(ship)) return false;
        return true;
    },

    /* Did this docked unit leave the battle inside its carrier?
       For a fighter FLIGHT the server answers it on every payload - jumpedWithCarrier, set by
       TacGamedata::markJumpedDockedFlights, which is also what paints the fleet list row
       "Jumped" rather than "Docked".
       An LCV parked on a DockingCollar has no such flag (that walk only follows hangarUsage
       dockedFlightId links, and an LCV rail stores its occupant in `lcvDocked` instead), so its
       rail has to be found. Docking is the ONLY thing that ever sets `removed`, so past the
       flight early-out the caller can only be holding a rail-parked LCV - and a fleet has at
       most a handful, so the walk costs nothing until the viewer actually stows one.
       Own units only, so the own-team hangar-contents mask is never in the way. */
    isDepartedWithCarrier: function isDepartedWithCarrier(ship) {
        if (ship.jumpedWithCarrier) return true;
        if (ship.flight) return false;   //a docked flight is fully answered by the flag above

        //Ship ids are STRINGS on anything spawned mid-battle (LAST_INSERT_ID), so compare
        //the parsed numbers, never the raw values.
        var lcvId = parseInt(ship.id, 10);

        for (var i in gamedata.ships) {
            var carrier = gamedata.ships[i];
            if (!carrier || !Array.isArray(carrier.systems)) continue;

            for (var s = 0; s < carrier.systems.length; s++) {
                var rail = carrier.systems[s];
                if (!rail || !rail.lcvDocked) continue;
                if (parseInt(rail.lcvDocked.shipId, 10) !== lcvId) continue;

                /* The same "has it left through a vortex?" pairing fleetList.js uses: a
                   COMMITTED jump-out counts from the moment it is plotted (the server does not
                   resolve it until the end of the Movement phase), and after that the removal
                   itself is the record. */
                if (shipManager.movement.hasCommittedJumpOut(carrier)) return true;
                return Boolean(shipManager.isDestroyed(carrier));
            }
        }

        return false;
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
                /* Keyed on the reinforcement flag as well as the class (REINFORCEMENTS_PLAN.md
                   §0): the group takes the FIRST member's flag, so merging hyperspace and
                   front-line mines of one class would silently re-flag half of them on reload.
                   A reinforcement mine is a nonsense purchase - it cannot arrive through a
                   vortex it has no drive to open - but nothing forbids it, and "nobody would do
                   that" is not a reason to write a merge that would be wrong if they did. */
                var key = ship.phpclass + '|' + (ship.reinforcement ? 1 : 0);
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

    /* ⭐ What a fleet COSTS, as one number - the figure written to tac_saved_list.points,
       which is both what the saved-fleet dropdown shows and what the affordability check
       on load compares against.

       Mines are not priced like other units: a fleet carrying any pays a flat 100pt
       premium to lay a minefield at all, plus 10% for every mine CLASS beyond the first.
       This function is the third statement of that rule, and the three must agree -
       gamedata.fleetCost() (the lobby's live buy-panel total) and fleetList.js (a live
       game's fleet list) are the other two. Saving used to sum bare pointCosts, so a
       mined fleet always listed for less than it actually cost to buy.

       A BULK row (mines, and OSATs since 2026-08-10) is N units at pointCost each. Keyed
       off bulkBuy alone rather than .mine: in a live game every ship reports 1 (the class
       default, never persisted), and its mines are already separate ships. */
    fleetPointsTotal: function fleetPointsTotal(ships) {
        var points = 0;
        var minePoints = 0;
        var mineClasses = [];

        for (var i = 0; i < ships.length; i++) {
            var lship = ships[i];
            var cost = lship.pointCost * (parseInt(lship.bulkBuy, 10) || 1);

            if (lship.mine) {
                minePoints += cost;
                if (mineClasses.indexOf(lship.mineType) === -1) mineClasses.push(lship.mineType);
            } else {
                points += cost;
            }
        }

        if (minePoints > 0) {
            points += Math.round((100 + minePoints) * (1 + ((mineClasses.length - 1) * 0.10)));
        }

        return points;
    },

    /* opts (all optional):
         includeTransient : also save one-turn / self-expiring criticals (game.php's
                            "save temporary critical effects" checkbox, off by default).
       An options object rather than a positional flag, matching loadSavedFleet, so a
       future third choice does not re-sign this at every call site. */
    constructSavedShips: function constructSavedShips(fleetname, isPublic, opts) {

        var saveships = Array();
        var saveable = [];
        var priced = [];

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
            priced.push(lship);
        }

        //Costed in one go rather than per ship: the mine premium is a FLEET-level figure.
        var points = ajaxInterface.fleetPointsTotal(priced);

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
                /* ⚠️ NOT rounded. Not every enhancement is priced in whole points - MINE_DMG
                   is 0.5 per level - while every figure that PRICES a fleet (fleetPointsTotal,
                   gamedata.fleetCost) sums the real per-unit cost. Rounding here, and only
                   here, is what made saved list #96 list at 2949 and reload at 2952: seven
                   mines each quietly gained half a point and the mine premium multiplied the
                   gap. Both enhvalue columns are DECIMAL so the fraction survives the round
                   trip - see db/fractionalEnhancementValue.sql. */
                'pointCostEnh': ship.pointCostEnh,
                'pointCostEnh2': ship.pointCostEnh2
            };

            if (ship.bulkBuy !== undefined) newShip.bulkBuy = ship.bulkBuy;
            //A regrouped set of live mines is bought back as one bulk of that many.
            if (members.length > 1) newShip.bulkBuy = members.length;

            /* REINFORCEMENTS_PLAN.md §0 - A SAVED FLEET REMEMBERS WHICH UNITS WERE BOUGHT AS
               REINFORCEMENTS (user request 2026-08-28, reversing the original ruling that it
               would not). Only the purchase-time flag: arrivalTurn/arrivalVia are in-play state
               and are not saved, so a reloaded reinforcement is back in hyperspace exactly as a
               freshly bought one would be.
               Emitted only when TRUE, the same convention the buy POST uses below, so an
               ordinary fleet's saved payload is byte-identical to before - and an older client
               that sends nothing simply saves everything front-line. */
            if (ship.reinforcement) newShip.reinforcement = true;

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

                /* Per-system enhancements (§5.3), same rules as the buy POST: sent when there is
                   anything to send, re-validated and RE-PRICED server-side on load (§4.7.1), and
                   the offer list is never sent at all. */
                if (window.systemEnhancements && systemEnhancements.count(ship) > 0) {
                    newShip.systemEnhancements = ship.systemEnhancements;
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
                /* ⚠️ NOT rounded. Not every enhancement is priced in whole points - MINE_DMG
                   is 0.5 per level - while every figure that PRICES a fleet (fleetPointsTotal,
                   gamedata.fleetCost) sums the real per-unit cost. Rounding here, and only
                   here, is what made saved list #96 list at 2949 and reload at 2952: seven
                   mines each quietly gained half a point and the mine premium multiplied the
                   gap. Both enhvalue columns are DECIMAL so the fraction survives the round
                   trip - see db/fractionalEnhancementValue.sql. */
                'pointCostEnh': ship.pointCostEnh,
                'pointCostEnh2': ship.pointCostEnh2
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

                /* Per-system enhancements (WEAPON_ENHANCEMENTS_PLAN.md §5.3). Read ONLY by
                   BuyingGamePhase::process, and re-derived there against a freshly built ship -
                   the prices in these rows are a claim, not an authority (D4).
                   systemEnhancementOffers is deliberately NEVER sent: it is blueprint data the
                   server regenerates, and it is by far the bigger of the two arrays. */
                if (window.systemEnhancements && systemEnhancements.count(ship) > 0) {
                    newShip.systemEnhancements = ship.systemEnhancements;
                }

                /* THE MANIFEST (REINFORCEMENTS_PLAN.md §3.5, Stage 4). arrivalVia names the OPENER
                   unit whose jump point exit this unit is riding through - never the vortex,
                   which does not exist yet and for a gate may never exist at all.

                   ⚠️ THIS IS THE ONLY ONE OF THE THREE REINFORCEMENT FIELDS THE CLIENT MAY SEND.
                   `reinforcement` rides the lobby POST alone (and lands in a separate claim
                   property server-side); `arrivalTurn` is written by the server's end-of-turn
                   deviation sweep and NEVER appears in a POST whitelist - a client that could set
                   it could bring its own fleet in a turn early, wherever it liked.

                   Sent only when set, so every other unit's payload is byte-identical to before;
                   InitialOrdersGamePhase::process re-validates it against the server-side ships and
                   writes NULL for anything it does not believe. */
                if (ship.arrivalVia !== null && ship.arrivalVia !== undefined) {
                    newShip.arrivalVia = ship.arrivalVia;
                }

                /* Reinforcements (REINFORCEMENTS_PLAN.md §4 Stage 1). Read ONLY by
                   BuyingGamePhase::process, which checks the game rule before believing it, so
                   this is inert in every other phase - the same contract the two fields above have.
                   Emitted only when TRUE, so every non-reinforcement payload in the game stays
                   byte-identical to before.
                   Inside the ownership gate on purpose: that makes it structurally impossible to
                   attach the flag to a unit you do not own, which is the same guarantee the jump
                   gate branch below depends on. */
                if (ship.reinforcement) newShip.reinforcement = true;

                tidyships.push(newShip);
            } else {
                /* ⭐ JUMP_GATES_PLAN.md section 3.1 fact 2 - THE ONE UNIT A PLAYER MAY ORDER
                   WITHOUT OWNING IT, and this `else` is half of what makes that possible.

                   Everything above is gated on `ship.userid === gamedata.thisplayer`, so a unit
                   you do not own is not in the POST AT ALL - not its systems, not an empty shell.
                   That, and not isMyShip, was the real structural blocker on fixed jump gates: a
                   gate belongs to whoever bought it (often the enemy) and ANY player may signal it.

                   ⚠️ SEND THE GATE AND ITS SIGNAL ORDER AND NOTHING ELSE. No movement, no EW, no
                   power, no ammo, no enhancements, no pre-battle damage - the arrays built above
                   stay empty. The server ignores all of those for a gate anyway (its power and EW
                   loops keep their own userid guard), and sending them would be an invitation to
                   trust them later. The systems array carries exactly one entry: the Jump Engine,
                   with this turn's signal order on it.

                   The server half is InitialOrdersGamePhase::process, which lets a POSTed gate
                   through its fire-order loop and passes only that engine's orders to
                   Firing::validateFireOrders. Neither half is any use without the other. */
                var gateOrders = ajaxInterface.getGateSignalOrders(ship);
                if (gateOrders) {
                    /* AN OBJECT, not the Array() the owner path builds, and the key matters:
                       Manager::getShipsFromJSON resolves each entry with
                       $ship->getSystemById($i) where $i is the KEY - and getSystemById indexes
                       straight into $ship->systems, so a system's "id" IS its position in the
                       construction order. Writing systems[4] into an Array would stringify as
                       [null,null,null,null,{...}] and post four dead entries; an object posts
                       exactly the one system. (PHP coerces the numeric string key back to an int
                       on array access, so getSystemById("4") finds system 4.) */
                    newShip.systems = {};
                    newShip.systems[gateOrders.systemId] = {
                        'id': gateOrders.systemId,
                        'power': Array(),
                        'fireOrders': gateOrders.fireOrders
                    };
                    tidyships.push(newShip);
                }
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

    /* JUMP_GATES_PLAN.md Stage 2 - THIS TURN'S GATE SIGNAL ORDERS ON $ship, or null.
       Returns { systemId, fireOrders }; null means this unit contributes nothing to the POST and
       the caller must not add it, so an unowned unit with no claim on it is dropped exactly as it
       is today.

       FOUR CONDITIONS, all of them narrow on purpose:
         1. Initial Orders (phase 1). A signal is declared there and nowhere else.
         2. The unit is a JumpgateCapital. Nothing else in the game gets this exemption.
         3. It carries a Jump Engine. (Keyed on system NAME - markGate() deliberately leaves
            $name 'jumpEngine' so the client class is reused, and the client tells a gate engine
            from a ship engine by the SHIP, never by the system.)
         4. That engine holds at least one order for THIS turn in firing mode 1-4 (the programmed
            open duration). Modes 5-7 have no meaning on a gate and are refused server-side too.

       ⭐ EVERY SUCH ORDER IS ONE THIS CLIENT JUST CREATED, and that is a property of the payload
       rather than an assumption: TacGamedata::hideSystemFireOrders strips EVERY current-turn
       ballistic order from EVERY phase-1 payload, its author's included, so a committed signal
       never comes back down the wire while Initial Orders are open. There is therefore no
       already-submitted order here to re-send and duplicate.

       The order's targetid is the claiming player's nearest qualifying unit - a HINT.
       Firing::validateVortexDeclaration re-derives it from $gamedata->forPlayer and overwrites it,
       so nothing here is trusted (plan section 3.3 and trap 4). */
    getGateSignalOrders: function getGateSignalOrders(ship) {
        if (gamedata.gamephase !== 1) return null;
        if (!gamedata.isJumpGate(ship)) return null;
        if (!ship.systems) return null;

        for (var i in ship.systems) {
            var system = ship.systems[i];
            if (!system || system.name !== 'jumpEngine') continue;
            if (!Array.isArray(system.fireOrders)) continue;

            var orders = Array();
            for (var b = 0; b < system.fireOrders.length; b++) {
                var fire = system.fireOrders[b];
                if (!fire || fire.turn != gamedata.turn) continue;
                var mode = parseInt(fire.firingMode, 10);
                if (isNaN(mode) || mode < 1 || mode > 4) continue;
                orders.push(fire);
            }

            if (orders.length === 0) return null;
            return { systemId: system.id, fireOrders: orders };
        }

        return null;
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

        // gamedata.php's APCu fast-poll reply carries the chat watermarks as a free
        // rider (see its FAST-POLL EXEMPT branch). Handing them to the chat poller lets
        // it skip its own request entirely while this game is being actively polled.
        // Guarded because chat is not present on every page that reaches this handler,
        // and the coordinator is only built once chat.php has been included.
        if (data && data.chatIds && window.fvChatPoll &&
            typeof window.fvChatPoll.observe === 'function') {
            window.fvChatPoll.observe(data.chatIds);
        }

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
