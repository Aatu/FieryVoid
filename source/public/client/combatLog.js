'use strict';

window.combatLog = {

    displayedTurn: null,
    critsShown: {},
    critAnimations: {}, //Just a convenient place to have this array for AllWeaponFireAgainstShipAnimation to use   
    logCache: {}, // key: turn number, value: processed fire order data

    onTurnStart: function onTurnStart() {
        $('.logentry').remove();
        // logFireOrders emits the damage <ul> as a SIBLING of its .logentry div
        // (the </div> closes before the <ul>), so it lands as a direct child of
        // #log and survives the .logentry removal. Clear those orphaned damage
        // lists too — but only the ones directly under #log, never the print's
        // lists nested inside #combatLogContainer > #LogActual.
        $('#log > ul').remove();
    },

    logDestroyedShip: function logDestroyedShip(ship, jumped) {

        var html = '<div class="logentry"><span class="destroyed">';

        // When the name is only a number, it might not be interpreted as a string.
        // In that case, the toUpperCase goes wrong.
        // Make certain the name is a string.
        if (typeof ship.name == 'string' || ship.name instanceof String) {
            if (jumped) {
                html += '<span class="shiplink" data-id="' + ship.id + '" >' + ship.name.toUpperCase() + '</span> <span style="color: #cc8500; font-weight: bold;">HAS JUMPED TO HYPERSPACE</span></span>';
            } else {
                html += '<span class="shiplink" data-id="' + ship.id + '" >' + ship.name.toUpperCase() + '</span> IS DESTROYED</span>';
            }
        } else {
            if (jumped) {
                html += '<span class="shiplink" data-id="' + ship.id + '" >' + ship.name.toUpperCase() + '</span> <span style="color: #cc8500; font-weight: bold;">HAS JUMPED TO HYPERSPACE</span></span>';
            } else {
                html += '<span class="shiplink" data-id="' + ship.id + '" >' + ship.name + '</span> IS DESTROYED</span>';
            }
        }

        var element = $(html).appendTo("#log");  //Changed to append - DK

        $("#log").scrollTop($("#log")[0].scrollHeight);
        return element;

    },

    logFireOrders: function logFireOrders(orders, printedLog = false, ships = null) {

        orders = [].concat(orders);

        //fire.x != "null" && otherFire.x == fire.x && fire.y != "null"
        var count = 0;
        var ship = gamedata.getShip(orders[0].shooterid);
        var target = gamedata.getShip(orders[0].targetid);
        var shots = 0;
        var shotshit = 0;
        var shotsintercepted = 0;
        /*let's count orders as well!*/
        var ordersC = 0;
        var ordersChit = 0;
        var ordersCintercepted = 0;

        var damages = Array();
        var lowC = 100000;
        var highC = -100000;
        var notes = "";
        var totalInterceptPenalty = 0;
        var totalInterceptorsCount = 0;
        var tooltipTextParts = [];
        var rollsTooltipTextParts = [];
        var shotIndex = 1;

        for (var a in orders) {

            count++;
            var fire = orders[a];

            var weapon = shipManager.systems.getSystem(ship, fire.weaponid);


            var modeIteration = fire.firingMode; //change weapons data to reflect mode actually used
            if (modeIteration != weapon.firingMode) {
                while (modeIteration != weapon.firingMode) { //will loop until correct mode is found
                    weapon.changeFiringMode();
                }
            }

            shots += fire.shots;
            shotshit += fire.shotshit;
            shotsintercepted += fire.intercepted;
            if (fire.shots > 0) ordersC += 1;
            if (fire.shotshit > 0) ordersChit += 1;
            if (fire.intercepted > 0) ordersCintercepted += 1;
            weaponManager.getDamagesCausedBy(fire, damages, ships);
            var needed = fire.needed;
            //if (needed < 0) needed = 0; //I skip this - if intercepted below 0, let's show it.
            if (fire.shots > 0) { //ignore hit chance of purely technical fire orders
                if (needed < lowC) lowC = needed;
                if (needed > highC) highC = needed;

                var interceptPenalty = 0;
                var interceptorsCount = 0;

                if (fire.notes) {
                    var match = fire.notes.match(/Interception: (\d+) sources:(\d+)/);
                    if (match) {
                        interceptPenalty = parseInt(match[1], 10);
                        interceptorsCount = parseInt(match[2], 10);
                    }
                }

                totalInterceptPenalty += interceptPenalty;
                totalInterceptorsCount += interceptorsCount;

                if (interceptorsCount > 0) {
                    var wWord = interceptorsCount === 1 ? "shot" : "shots";
                    tooltipTextParts.push("Shot " + shotIndex + ": -" + interceptPenalty + "% (" + interceptorsCount + " intercepting " + wWord + ")");
                } else {
                    tooltipTextParts.push("Shot " + shotIndex + ": No interception");
                }
                shotIndex++;

                var rollRegex = /rolled: (\d+), needed: (\d+)/g;
                var rollMatch;
                while ((rollMatch = rollRegex.exec(fire.notes)) !== null) {
                    var rolled = parseInt(rollMatch[1], 10);
                    var needed = parseInt(rollMatch[2], 10);
                    var rollText = "Shot " + (rollsTooltipTextParts.length + 1) + ": " + rolled;
                    if (rolled <= needed) {
                        rollText = "<span style='color: limegreen; font-weight: bold;'>" + rollText + "</span>";
                    }
                    rollsTooltipTextParts.push(rollText);
                }
            }

            if (fire.pubnotes) notes += fire.pubnotes + " ";
        }

        // The FIRE: header is coloured RELATIVE to the viewer (keyed on the
        // shooter): participants see mine=green / ally=blue / enemy=red, so a
        // 2-player team-2 reviewer still sees their own fleet green. Observers
        // (not in the game) have no mine/ally/enemy, so they fall back to the
        // absolute per-team palette. Terrain shooters are neutral white.
        // (Ship NAMES, by contrast, use the absolute team colour below.)
        var fireColor = "";
        if (gamedata.isTerrain(ship.shipSizeClass, ship.userid)) {
            fireColor = "color:#ffffff;";
        } else if (!gamedata.isPlayerInGame()) {
            var rgb = gamedata.getTeamColorRGB(ship.team);
            fireColor = "color:rgb(" + Math.round(rgb[0]) + "," + Math.round(rgb[1]) + "," + Math.round(rgb[2]) + ");";
        } else if (gamedata.isMyShip(ship)) {
            fireColor = "color:rgb(50,205,50);";   // green (mine)
        } else if (gamedata.isMyorMyTeamShip(ship)) {
            fireColor = "color:rgb(51,173,255);";  // blue (ally)
        } else {
            fireColor = "color:rgb(255,80,80);";   // red (enemy)
        }

        var html = '<div class="logentry fire-' + orders[0].id + '"><span class="logheader fire" style="' + fireColor + '">FIRE: </span><span>';
        html += '<span class="shiplink" data-id="' + ship.id + '" >' + ship.name + '</span>';

        var counttext = count > 1 ? count + "x " : "";

        var tooltipAttr = "";
        if (totalInterceptorsCount > 0) {
            var wWord = totalInterceptorsCount === 1 ? "shot" : "shots";
            var summaryText = 'Interception: ' + totalInterceptorsCount + " " + wWord + ' applied a -' + totalInterceptPenalty + '% hit penalty.';
            var tooltipText = summaryText;

            // If there's more than one shot, append the per-shot breakdown
            if (shotIndex > 2) {
                tooltipText += "\n" + tooltipTextParts.join("\n");
            }

            tooltipAttr = ' class="intercept-tooltip" data-tooltip="' + tooltipText + '"';
        } else {
            tooltipAttr = '';
        }

        var rollsTooltipAttr = "";
        if (rollsTooltipTextParts.length > 0) {
            var rollsTooltipText = "Dice Rolls";
            rollsTooltipText += "\n" + rollsTooltipTextParts.join("\n");
            rollsTooltipAttr = ' class="intercept-tooltip" data-tooltip="' + rollsTooltipText + '"';
        }

        var chancetext = "";
        if (lowC !== 100000) {
            if (lowC == highC) chancetext = "<span" + tooltipAttr + ">Chance to hit: " + lowC + "%</span>";
            else chancetext = "<span" + tooltipAttr + ">Chance to hit: " + lowC + "% - " + highC + "%</span>";
        }

        if (!target) chancetext = "";

        var intertext = "";
        if (shotsintercepted > 0) {
            if (ordersC != shots) {
                intertext = ', <span>' + ordersCintercepted + '(' + shotsintercepted + ') intercepted</span>';
            } else {
                intertext = ', <span>' + shotsintercepted + ' intercepted</span>';
            }
        }

        var targettext = "";
        if (target) {
            // Colour the attacked ship's name. Terrain has no meaningful team, so
            // render it white. In a two-sided participant game the absolute team
            // palette is ambiguous (your fleet could be "red"), so use a RELATIVE
            // mine=green / enemy=red scheme instead. All other cases (multiplayer
            // participant, observer) keep the absolute per-team palette so each
            // specific ship is identifiable. getTeamColorRGB guards bad team values.
            var targetColor;
            if (gamedata.isTerrain(target.shipSizeClass, target.userid)) {
                targetColor = "color:#ffffff;";
            } else if (gamedata.isPlayerInGame() && gamedata.getDistinctTeamCount() === 2) {
                targetColor = gamedata.isMyorMyTeamShip(target)
                    ? "color:rgb(50,205,50);"   // green (mine/ally)
                    : "color:rgb(255,80,80);";  // red (enemy)
            } else {
                var trgb = gamedata.getTeamColorRGB(target.team);
                targetColor = "color:rgb(" + Math.round(trgb[0]) + "," + Math.round(trgb[1]) + "," + Math.round(trgb[2]) + ");";
            }
            targettext = '<span> at </span><span class="shiplink target" data-id="' + target.id + '" style="' + targetColor + 'font-weight:normal;">' + target.name + '</span>';
        }

        var shottext = "";
        //if (target) shottext = ', ' + shotshit + '/' + shots + ' shots hit' + intertext + '.';
        //if (target) shottext = ', ' + ordersChit + '(' +shotshit + ')/' + ordersC + '(' +shots + ') shots hit' + intertext + '.';
        if (target) {
            var shotContent = "";
            if (ordersC != shots) {
                shotContent = ordersChit + '(' + shotshit + ')/' + ordersC + '(' + shots + ') shots hit';
            } else {
                shotContent = shotshit + '/' + shots + ' shots hit';
            }

            if (rollsTooltipAttr !== "") {
                shottext = ', <span' + rollsTooltipAttr + '>' + shotContent + '</span>' + intertext + '.';
            } else {
                shottext = ', ' + shotContent + intertext + '.';
            }
        }

        var notestext = "";
        if (notes) notestext = '<span class="pubotes">' + notes + '</span>';

        var shortText = false;
        if (weaponManager.doShortLogText(fire)) shortText = true;

        //Some orders don't need the full log text, e.g. Reactor overload, hyperspace jump.    
        if (shortText) {
            html += notestext;
        } else {
            if (mathlib.arrayIsEmpty(weapon.missileArray)) {
                html += ' firing ' + counttext + weapon.displayName + ' (' + weapon.firingModes[weapon.firingMode] + ') ' + targettext + '. ' + chancetext + shottext + notestext;
            } else {
                html += ' firing ' + counttext + weapon.missileArray[weapon.firingMode].displayName + targettext + '. ' + chancetext + shottext + notestext;
            }
        }

        html += '<span class="notes"> ' + fire.notes + '</span>';
        //  html += damagehtml;
        html += '</span></div>';


        if (damages.length > 0) {
            html += "<ul>";

            for (var i in damages) {
                var victim = damages[i].ship;
                var totaldam = 0;
                var armour = 0;
                var criticalshtml = ""; //Needs to be outside of damage block below to prevent overwriting.
                // Combined "Fighters disengaged / destroyed:" row. Collected as two
                // lists of coloured name spans so disengaged (orange) always lists
                // before destroyed (red) regardless of damage-entry processing order;
                // joined with the header at emit time.
                var disengagedFighters = [];
                var destroyedFighters = [];
                var damagehtml = "";
                for (var a in damages[i].damages) {

                    var d = damages[i].damages[a];
                    var damageDone = d.damage - d.armour;
                    var damageStopped = d.armour;
                    /*healing is up, so negative values are just fine
                    if (damageDone < 0) {
                        damageStopped = d.damage;
                        damageDone = 0;
                    }
                    */
                    /*if (d.damage-d.armour<=0) continue;*/

                    totaldam += damageDone; //d.damage-d.armour;
                    armour += damageStopped; //d.armour;
                    var system = shipManager.systems.getSystem(gamedata.getShip(d.shipid), d.systemid);
                    var comma = ",";

                    //New section to create critical entries when damage is done but system no destroyed.
                    var firstCrit = "";
                    var hasCrit = shipManager.criticals.sufferedCritThisTurn(system, d.turn);

                    if (hasCrit && damageDone > 0) {
                        // Fighter craft have no .ship back-reference (the Fighter
                        // constructor never sets it), so the "System criticals:"
                        // path below — which keys its dedupe tracker on
                        // system.ship.id — can't handle them. A fighter that took
                        // enough damage to DROP OUT gets a DisengagedFighter crit
                        // this turn; add it to the combined fighters list in ORANGE
                        // (.critical), deduped against the owning flight (d.shipid).
                        // Destroyed fighters get added in RED by the block below.
                        if (!system.ship) {
                            var droppedOut = shipManager.criticals.hasCriticalOnTurn(system, "DisengagedFighter", d.turn);
                            if (droppedOut && !combatLog.critsShown[d.shipid]?.includes(system.id)) {
                                disengagedFighters.push('<span class="critical">' + shipManager.systems.getDisplayName(system) + '</span>');

                                if (!combatLog.critsShown[d.shipid]) {
                                    combatLog.critsShown[d.shipid] = [];
                                }
                                combatLog.critsShown[d.shipid].push(system.id);
                            }
                            continue; //Fighter handled (or a non-dropout fighter crit); skip the ship-system path.
                        }
                        if (criticalshtml.length == 0) {
                            firstCrit = " System criticals: ";
                            comma = "";
                        }
                        if (!combatLog.critsShown[system.ship.id]?.includes(system.id)) {
                            criticalshtml += firstCrit + '<span class="critical">' + comma + ' ' + shipManager.systems.getDisplayName(system) + '</span>';
                        }

                        if (!combatLog.critsShown[system.ship.id]) {
                            combatLog.critsShown[system.ship.id] = [];
                        }
                        if (!combatLog.critsShown[system.ship.id].includes(system.id)) {
                            combatLog.critsShown[system.ship.id].push(system.id);
                        }
                    }


                    if (!d.destroyed) {
                        continue;
                    }

                    // Destroyed fighter craft (no .ship back-reference) join the
                    // combined fighters list in RED (.damage), after the orange
                    // disengaged names. Ship systems keep the "Systems destroyed:" list.
                    if (!system.ship) {
                        destroyedFighters.push('<span class="damage">' + shipManager.systems.getDisplayName(system) + '</span>');
                        continue;
                    }

                    var firstDam = "";

                    if (damagehtml.length == 0) {
                        firstDam = " Systems destroyed: ";
                        comma = "";
                    }

                    damagehtml += firstDam + '<span class="damage">' + comma + ' ' + shipManager.systems.getDisplayName(system) + '</span>';

                }

                //if (totaldam > 0){ //display fire orders that did no damage, too! - MS
                //          html += '<li><span class="shiplink victim" data-id="'+ship.id+'" >' + victim.name + '</span> damaged for ' + totaldam + '(+ ' + armour + ' armour). '+ damagehtml+'</li>';

                if (fire.damageclass == "HyperspaceJump") continue; //Do not show damage to Primary Structure when jumping to Hyperspace. 

                html += '<li><span class="shiplink victim" data-id="' + ship.id + '" >' + victim.name + '</span> damaged for ' + totaldam + ' (total armour mitigation: ' + armour + ').</li>';

                if (criticalshtml.length > 1) {
                    html += '<li>' + criticalshtml + '</li>';
                }

                // Disengaged (orange) first, then destroyed (red), in one row.
                var fighterNames = disengagedFighters.concat(destroyedFighters);
                if (fighterNames.length > 0) {
                    html += '<li> Fighters disengaged / destroyed: ' + fighterNames.join(', ') + '</li>';
                }

                if (damagehtml.length > 1) {
                    html += '<li>' + damagehtml + '</li>';
                }
                //}
            }

            html += "</ul>";
        }


        if (printedLog) { //Different method of listing depending on whether player is watching a Replay animation or just browsing the printed log :)
            var targetDiv = document.getElementById("LogActual");
            targetDiv.style.display = "block";
            targetDiv.innerHTML += html;
        } else {
            var element = $(html).appendTo("#log");
            $("#log").scrollTop($("#log")[0].scrollHeight);
            return element;
        }
    },

    removeFireOrders: function removeFireOrders(element) {
        jQuery(element).remove();
    },

    logAmmoExplosion: function logAmmoExplosion(ship, system) {

        var dmg;

        var damages = "Systems damaged: ";
        var destroyed = "Systems destroyed: ";

        if (system.displayName == "Bomb Rack") {
            dmg = 35;
        } else if (system.displayName == "Reload Rack") {
            dmg = 120;
        } else dmg = 70;

        for (var i = 0; i < ship.systems.length; i++) {
            var sys = ship.systems[i];
            for (var j = 0; j < sys.damage.length; j++) {
                var entry = sys.damage[j];
                if (entry.destroyed == 1) {
                    if (entry.fireorderid == -1 && entry.turn == gamedata.turn) {
                        destroyed += '<span class="damage">' + shipManager.systems.getDisplayName(sys) + '</span>';
                        destroyed += ', ';
                        break;
                    } else break;
                }
            }
        }

        var html = '<div class="logentry">';
        html += '<span class="shiplink" data-id="' + ship.id + '" >' + ship.name + '</span>';
        html += ' suffered ' + dmg + ' damage due to exploding ammunition from its ' + system.displayName + '.';

        /*         if (damages.length >15){
                    var length = damages.length;
                     damages = damages.substring(0, length-2);
                     html +=  '<li>' + damages + '</li>';
                 }
         */if (destroyed.length > 15) {
            var length = destroyed.length;
            destroyed = destroyed.substring(0, length - 2);
            html += '<li>' + destroyed + '</li>';
        }

        html += '</span></div></ul>';

        $(html).prependTo("#log");
    },

    logSubReactorExplosion: function logSubReactorExplosion(ship, system) {
        var html = '<div class="logentry">';
        html += '<span class="shiplink" data-id="' + ship.id + '" >' + ship.name + '</span>';
        html += ' lost parts of its outer structure due to a chain reaction after a reactor exploded.';
        html += '</span></div></ul>';

        $(html).prependTo("#log");
    },

    /*
    logCriticals: function logCriticals(ship, string) {

        var html = '<div class="logentry">';
        html += '<span class="shiplink" data-id="' + ship.id + '" >' + ship.name + '</span>';
        html += string;
        html += '</span></div></ul>';

        $(html).prependTo("#log");
    },
    */

    logMoves: function logMoves(ship) {

        var e = $('.logentry.' + ship.id + ' .move.t' + gamedata.turn);
        if (e.length > 0) return;

        var start = shipManager.movement.getFirstMoveOfTurn(ship);
        var end = shipManager.movement.getLastCommitedMove(ship);

        if (!start || !end) return;

        var html = '<div class="logentry ' + ship.id + '" data-shipid="' + ship.id + '"><span class="logheader move t' + gamedata.turn + '">MOVE: </span> <span class="shiplink" data-id="' + ship.id + '" >' + ship.name + '</span>';
        html += '<span> From (' + start.x + ',' + start.y + ') to (' + end.x + ',' + end.y + ') </span></div>';
        var log = $(html);
        //var details = $('<ul><li><span> From ('+start.x+','+start.y+') to ('+end.x+','+end.y+') </span></ul></li>')

        //$(details).prependTo("#log");
        $(log).prependTo("#log");
    },

    getDisplayTurn: function getDisplayTurn() {
        if (this.displayedTurn === null) {
            return gamedata.turn;
        } else {
            return this.displayedTurn;
        }
    },

    showPrevious: function showPrevious() {
        if (this.displayedTurn === null) this.displayedTurn = gamedata.turn;
        var turn = this.displayedTurn - 1;
        if (turn < 1) return;
        this.displayedTurn = turn;

        if (this.displayedTurn < gamedata.turn) {
            document.getElementById('nextTurnButton').style.display = 'inline-block'; // Display next button when relevant.
            document.getElementById('currentTurnButton').style.display = 'inline-block'; // Display next button when relevant.
        }

        combatLog.onTurnStart(); // Clear leftover live replay messages (.logentry in #log) before showing the print.
        combatLog.fetchAndShowCombatLog();
    },

    showNext: function showNext() {
        if (this.displayedTurn === null) this.displayedTurn = gamedata.turn;
        var turn = this.displayedTurn + 1; //Get the turn we want.
        this.displayedTurn = turn; //Set new displayedTurn for further requests.

        if (this.displayedTurn >= gamedata.turn) {
            document.getElementById('nextTurnButton').style.display = 'none'; // Hide next turn button
            document.getElementById('currentTurnButton').style.display = 'none'; //Hide Turn number.
            document.getElementById('LogActual').style.display = 'none'; //Hide Turn number.
            return; //Can't go forward past current turn.
        }
        combatLog.onTurnStart(); // Clear leftover live replay messages (.logentry in #log) before showing the print.
        combatLog.fetchAndShowCombatLog();
    },

    showCurrent: function showCurrent() {
        this.displayedTurn = gamedata.turn;

        document.getElementById('nextTurnButton').style.display = 'none'; // Hide next turn button
        document.getElementById('currentTurnButton').style.display = 'none'; //Hide Turn number.
        document.getElementById('LogActual').style.display = 'none'; //Hide Turn number.        
        document.getElementById('LogActual').innerHTML = '';  //Reset Combat Log text          
        return;
    },

    /*
    fetchAndShowCombatLog: function fetchAndShowCombatLog() {
        var turn = this.displayedTurn;
    
        // Check if this turn's data is already cached
        if (combatLog.logCache[turn]) {
            combatLog.showLog(combatLog.logCache[turn]);
            return;
        }
    
        jQuery.ajax({
            type: 'GET',
            url: 'replay.php',
            dataType: 'json',
            data: {
                turn: turn,
                gameid: gamedata.gameid,
                time: new Date().getTime() // prevent caching by browser
            },
            success: function (data) {
                var allFireOrders = combatLog.groupByShipAndWeapon(
                    weaponManager.getAllFireOrdersForLogPrint(data.ships, data.turn)
                );
    
                // Store in cache
                combatLog.logCache[turn] = allFireOrders;
    
                combatLog.showLog(allFireOrders);
            }.bind(this),
            error: ajaxInterface.errorAjax
        });
    },
    */

    //New version using ajaxWithRetry()
    fetchAndShowCombatLog: function fetchAndShowCombatLog() {
        var turn = this.displayedTurn;

        // Check if this turn's data is already cached
        if (combatLog.logCache[turn]) {
            combatLog.showLog(combatLog.logCache[turn].allFireOrders, combatLog.logCache[turn].ships);
            return;
        }

        ajaxInterface.ajaxWithRetry({
            type: 'GET',
            url: 'replay.php',
            dataType: 'json',
            data: {
                turn: turn,
                gameid: gamedata.gameid,
                time: new Date().getTime() // prevent browser caching
            },
            success: function (data) {
                var allFireOrders = combatLog.groupByShipAndWeapon(
                    weaponManager.getAllFireOrdersForLogPrint(data.ships, data.turn)
                );

                // Store in cache
                combatLog.logCache[turn] = { allFireOrders: allFireOrders, ships: data.ships };

                combatLog.showLog(allFireOrders, data.ships);
            }.bind(this),
            error: ajaxInterface.errorAjax
        });
    },

    groupByShipAndWeapon: function groupByShipAndWeapon(incomingFire) {
        const grouped = {};

        incomingFire.forEach(function (fire) {
            if (fire.type === "intercept" || fire.type === "selfIntercept") return;

            const ship = gamedata.getShip(fire.shooterid);
            const weapon = shipManager.systems.getSystem(ship, fire.weaponid);
            const key = `${fire.shooterid}-${weapon.constructor.name}-${fire.firingMode}-${fire.targetid}`;

            grouped[key] = grouped[key] || [];
            grouped[key].push(fire);
        });

        const groupedKeys = Object.keys(grouped);

        groupedKeys.sort(function (a, b) {
            const obj1 = grouped[a][0];
            const obj2 = grouped[b][0];

            const s1 = gamedata.getShip(obj1.shooterid);
            const s2 = gamedata.getShip(obj2.shooterid);
            const w1 = shipManager.systems.getSystem(s1, obj1.weaponid);
            const w2 = shipManager.systems.getSystem(s2, obj2.weaponid);
            /*    
                        // Sort by resolution order first
                        if (obj1.resolutionOrder !== obj2.resolutionOrder) {
                            return obj1.resolutionOrder - obj2.resolutionOrder;
                        }
                
                        // Fighters after ships
                        if (s1.flight !== s2.flight) {
                            return s1.flight ? 1 : -1;
                        }
            */
            // Weapon priority
            if (w1.priority !== w2.priority) {
                return w1.priority - w2.priority;
            }

            // Fallback: shooter ID and fire order ID
            let val = s1.id - s2.id;
            if (val === 0) val = obj1.id - obj2.id;
            return val;
        });

        return groupedKeys.map(function (key) {
            return grouped[key];
        });
    },

    showLog: function showLog(allFireOrders, ships = null) {
        // Get the current turn from the combat log system
        var currentTurn = window.combatLog.getDisplayTurn();

        // Start building the log HTML
        var html = '<br><span class = "combatTurn";>Turn ' + currentTurn + ':</span><br>';

        // Check if the allFireOrders array is empty
        if (allFireOrders.length === 0) {
            html += '<span class = "noCombatLog";><br>No fire orders were made this turn!</span>';
        }

        // Update the content of LogActual with the current turn and optional message
        document.getElementById('LogActual').innerHTML = html;

        // Process fire orders if any
        allFireOrders.forEach(function (logEntry) { // allFireOrders is an array of other arrays
            combatLog.logFireOrders(logEntry, true, ships);
        });

        // Show the LogActual div
        document.getElementById('LogActual').style.display = 'block'; // Set to 'block' or 'inline-block' depending on your layout
        combatLog.critsShown = {}; //Empty crti tracker for next print.
    }

};
$(function () {
    $(document).on('mouseenter touchstart', '.intercept-tooltip', function (e) {
        var tooltip = $('#custom-intercept-tooltip');
        if (!tooltip.length) {
            tooltip = $('<div id="custom-intercept-tooltip" class="custom-intercept-tooltip"></div>').appendTo('body');
        }
        var raw = String($(this).data('tooltip') || '');
        var lines = raw.split('\n');
        var $header = $('<div class="hctt-header"></div>').text(lines[0] || '');
        tooltip.empty().append($header);
        for (var i = 1; i < lines.length; i++) {
            tooltip.append($('<div class="hctt-row"></div>').html(lines[i]));
        }
        var rect = this.getBoundingClientRect();
        var topPos = rect.top - tooltip.outerHeight() - 5;
        if (topPos < 0) topPos = rect.bottom + 5;
        var leftPos = rect.left + rect.width / 2 - tooltip.outerWidth() / 2;
        if (leftPos < 0) leftPos = 5;
        if (leftPos + tooltip.outerWidth() > window.innerWidth) leftPos = window.innerWidth - tooltip.outerWidth() - 5;
        tooltip.css({ top: topPos + 'px', left: leftPos + 'px' }).show();
    }).on('mouseleave touchend touchmove', '.intercept-tooltip', function (e) {
        $('#custom-intercept-tooltip').hide();
    });
});

