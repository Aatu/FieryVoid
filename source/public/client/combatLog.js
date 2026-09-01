'use strict';

window.combatLog = {

    displayedTurn: null,
    critsShown: {},
    critAnimations: {}, //Just a convenient place to have this array for AllWeaponFireAgainstShipAnimation to use
    logCache: {}, // key: turn number, value: processed fire order data

    /* ── PRINTED-LOG VIEW STATE (LOG_PANEL_REDESIGN_PLAN.md Stage 2b/2c) ──────────
       Sort and filters are PURE PRESENTATION and are applied in showLog ONLY. They must
       never reach groupByShipAndWeapon, which is the canonical RESOLUTION order shared
       with the replay animation. Remembered across sessions, so a player who always
       reads by attacker does not re-pick it every game. */
    sortMode: 'resolution',
    sideFilter: 'all',
    hitsOnly: false,
    findText: '',
    hiddenCount: 0,

    // Damage lists longer than this start collapsed - one alpha strike against a big hull
    // can otherwise be thirty lines and push every other entry off a 150px panel.
    COLLAPSE_ROWS_OVER: 4,

    PREF_KEY: 'fv.combatLog.view',

    // Clears the LIVE replay stream only (#logLive). The printed log (#LogActual) owns its
    // own lifecycle via showCurrent / showLog and must not be touched here.
    onTurnStart: function onTurnStart() {
        /* ONE selector, and not a class selector at that. This used to be a pair -
           $('#log > .logentry') plus $('#log > ul') - because logFireOrders emitted its
           damage <ul> as a SIBLING of the .logentry div, so removing the entries left
           orphaned damage lists with their "FIRE:" headers stripped off. The <ul> is
           nested inside its entry now (Stage 2e) and the live stream has a container of
           its own, so emptying that container is the whole job. */
        $('#logLive').empty();
        combatLog.updateTurnControls();
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

        var element = $(html).appendTo("#logLive");  //Changed to append - DK

        combatLog.scrollLiveToBottom();
        return element;

    },

    /* #logLive is the box that actually scrolls. This used to be
       $("#log").scrollTop($("#log")[0].scrollHeight) and was a NO-OP: #log was
       overflow-y:visible, and a box that does not clip cannot scroll, so the live stream
       simply overflowed the panel instead of following the newest entry. */
    scrollLiveToBottom: function scrollLiveToBottom() {
        var live = document.getElementById("logLive");
        if (live) live.scrollTop = live.scrollHeight;
    },

    /* The 3px allegiance rail on a log entry, from the SAME source as the "FIRE:" header's
       colour - so the rail follows getShipLogColorCss through all three of its arms
       (observer / 2-team participant / 3+-team participant) instead of being a second,
       CSS-only gate that only 2-team games would ever see. See arch_team_colour_logic. */
    logRailStyle: function logRailStyle(ship) {
        if (!ship) return '';
        var m = /color\s*:\s*([^;]+)/.exec(gamedata.getShipLogColorCss(ship));
        return m ? 'border-left-color:' + m[1] + ';' : '';
    },

    // damageIndex is optional: showLog builds one for the whole printed log so each order is a
    // map lookup instead of a full fleet sweep. The replay path (LogAnimation) renders one group
    // at a time and passes none, which keeps the original sweep.
    logFireOrders: function logFireOrders(orders, printedLog = false, ships = null, damageIndex = null) {

        orders = [].concat(orders);

        //fire.x != "null" && otherFire.x == fire.x && fire.y != "null"
        var count = 0;
        var ship = gamedata.getShip(orders[0].shooterid);
        var target = gamedata.getShip(orders[0].targetid);

        // The terrain's own return damage from a collision is bookkeeping rather than an attack -
        // see weaponManager.isTerrainReturnDamage. The replay path already filters it out before it
        // reaches an animation or a log entry; this catches the PRINTED log, which reads fire orders
        // straight from replay.php and so never passes through that filter.
        if (orders.every(function (fire) { return weaponManager.isTerrainReturnDamage(fire); })) {
            return null;
        }

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


            //Defensive: an order naming a mode this weapon does not declare made the loop below
            //spin forever and hang the tab. The Chameleon fire-order remap can produce exactly that
            //(a Twin Array in mode 2 substituted by a single-mode Plasma Accelerator) and is clamped
            //server-side, but nothing structurally prevents another source of it, and the cost of
            //being wrong here is the whole page. changeFiringMode() cycles, so the number of
            //declared modes is a complete bound: if it has not matched after one full cycle, it
            //never will.
            if (!weapon) continue;
            var modeIteration = fire.firingMode; //change weapons data to reflect mode actually used
            if (modeIteration != weapon.firingMode) {
                var modeGuard = weapon.firingModes ? Object.keys(weapon.firingModes).length : 1;
                while (modeIteration != weapon.firingMode && modeGuard-- > 0) { //loops until the correct mode is found
                    weapon.changeFiringMode();
                }
            }

            shots += fire.shots;
            shotshit += fire.shotshit;
            shotsintercepted += fire.intercepted;
            if (fire.shots > 0) ordersC += 1;
            if (fire.shotshit > 0) ordersChit += 1;
            if (fire.intercepted > 0) ordersCintercepted += 1;
            weaponManager.getDamagesCausedBy(fire, damages, ships, damageIndex);
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

        // The FIRE: header is keyed on the shooter; see gamedata.getShipLogColorCss
        // for the observer / 2-team / 3+-team rule it follows.
        var fireColor = gamedata.getShipLogColorCss(ship);

        /* The entry's OPENING TAG is assembled at the very bottom of this function, not
           here: the collapse class depends on how many damage rows come out of the loop
           below, and the rail colour is easier to read next to the header colour it comes
           from. `html` is the entry's INNER markup from this point on. */
        var html = '<span class="logheader fire" style="' + fireColor + '">FIRE: </span><span>';
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
            // Same helper as the FIRE: header above, so shooter and target read
            // consistently within one log line.
            var targetColor = gamedata.getShipLogColorCss(target);
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
        if (weaponManager.doShortLogText(fire, ship)) shortText = true;

        // A crash into a fighter flight is split server-side into one fire order per fighter, but
        // only the first carries the "COLLISION!" pubnotes and DBManager::submitDamages hangs every
        // fighter's damage off that same first order. In short form the rest have nothing left to
        // say, so drop them rather than print a bare "FIRE: <terrain>" line. The replay path calls
        // this one group at a time, so it is that path these empties would otherwise show up in.
        if (shortText && !notes && damages.length === 0 && fire.damageclass === "TerrainCrash") {
            return null;
        }

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
        html += '</span>';

        /* THE DAMAGE LIST IS A CHILD OF THE ENTRY, NOT A SIBLING (Stage 2e). It used to be
           emitted after the entry's </div>, which is what forced onTurnStart to run two
           selectors and made a per-entry collapse impossible - there was no element that
           contained an entry AND its damage. Built into its own string so the row count is
           known before the entry's opening tag is written. */
        var damageList = "";
        var damageRows = 0;

        if (damages.length > 0) {
            damageList += "<ul>";

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
                // Capacity spent soaking this fire order by shield projections (Thirdspace /
                // Thought / Trek) and by Shadow diffuser tendrils. Each reported on its own line -
                // see the exclusion below.
                var shieldAbsorbed = 0;
                var tendrilAbsorbed = 0;
                for (var a in damages[i].damages) {

                    var d = damages[i].damages[a];

                    // A shield projection or a diffuser tendril records what it absorbed as a damage
                    // entry on ITSELF, tagged with its own class (the absorbDamage() of
                    // ThirdspaceShield / ThoughtShield / TrekShieldProjection / DiffuserTendril).
                    // That is absorber capacity spent, not damage to the ship, so it must stay out
                    // of the ship's damage total - and out of the criticals and destroyed-systems
                    // lists, which these systems never join. Each gets its own line below, so a
                    // fully absorbed shot no longer reads as if nothing happened.
                    if (d.damageclass === "ThirdspaceShield"
                        || d.damageclass === "ThoughtShield"
                        || d.damageclass === "TrekShieldProjection") {
                        shieldAbsorbed += Number(d.damage); // never string-concatenate a JSON value
                        continue;
                    }
                    if (d.damageclass === "Tendril") {
                        tendrilAbsorbed += Number(d.damage);
                        continue;
                    }

                    var system = shipManager.systems.getSystem(gamedata.getShip(d.shipid), d.systemid);
                    // A damage row whose ship or system cannot be resolved on this page must not be
                    // able to kill the whole log: sufferedCritThisTurn() dereferences .criticals
                    // immediately, so one unresolvable row threw a TypeError out of logFireOrders
                    // and the viewer lost EVERY entry, including their own shots. Mirrors the null
                    // guards on the server-side damage/critical loaders. (Seen with Chameleon
                    // phantom rows, which are stored under a negative shipid that no client knows —
                    // fixed at source in stripForJsonDisguised, guarded here as well.)
                    if (!system) continue;
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

                damageList += '<li><span class="shiplink victim" data-id="' + ship.id + '" >' + victim.name + '</span> damaged for ' + totaldam + ' (total armour mitigation: ' + armour + ').</li>';
                damageRows++;

                if (shieldAbsorbed > 0) {
                    damageList += '<li><span class="shieldabsorb">Shields absorbed ' + shieldAbsorbed + ' damage.</span></li>';
                    damageRows++;
                }

                if (tendrilAbsorbed > 0) {
                    damageList += '<li><span class="shieldabsorb">Tendrils absorbed ' + tendrilAbsorbed + ' damage.</span></li>';
                    damageRows++;
                }

                if (criticalshtml.length > 1) {
                    damageList += '<li>' + criticalshtml + '</li>';
                    damageRows++;
                }

                // Disengaged (orange) first, then destroyed (red), in one row.
                var fighterNames = disengagedFighters.concat(destroyedFighters);
                if (fighterNames.length > 0) {
                    damageList += '<li> Fighters disengaged / destroyed: ' + fighterNames.join(', ') + '</li>';
                    damageRows++;
                }

                if (damagehtml.length > 1) {
                    damageList += '<li>' + damagehtml + '</li>';
                    damageRows++;
                }
                //}
            }

            damageList += "</ul>";
        }

        var entryClass = 'logentry fire-' + orders[0].id;
        if (damageRows > combatLog.COLLAPSE_ROWS_OVER) {
            // One alpha strike can be thirty rows; start those folded and let the entry be
            // clicked open. The count goes in the affordance so the fold is never silent.
            entryClass += ' collapsible collapsed';
            html += '<span class="logexpand" role="button" tabindex="0">'
                + damageRows + ' damage lines</span>';
        }

        html = '<div class="' + entryClass + '" style="' + combatLog.logRailStyle(ship) + '">'
            + html + damageList + '</div>';

        /* Stage 2d: the printed path RETURNS its markup and showLog joins the lot in one
           assignment. It used to do `targetDiv.innerHTML += html` per fire group, which
           reparses the entire container once per group - O(n^2) on a busy turn. */
        if (printedLog) {
            return html;
        }

        var element = $(html).appendTo("#logLive");
        combatLog.scrollLiveToBottom();
        return element;
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

        $(html).prependTo("#logLive");
    },

    logSubReactorExplosion: function logSubReactorExplosion(ship, system) {
        var html = '<div class="logentry">';
        html += '<span class="shiplink" data-id="' + ship.id + '" >' + ship.name + '</span>';
        html += ' lost parts of its outer structure due to a chain reaction after a reactor exploded.';
        html += '</span></div></ul>';

        $(html).prependTo("#logLive");
    },

    /*
    logCriticals: function logCriticals(ship, string) {

        var html = '<div class="logentry">';
        html += '<span class="shiplink" data-id="' + ship.id + '" >' + ship.name + '</span>';
        html += string;
        html += '</span></div></ul>';

        $(html).prependTo("#logLive");
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

        //$(details).prependTo("#logLive");
        $(log).prependTo("#logLive");
    },

    getDisplayTurn: function getDisplayTurn() {
        if (this.displayedTurn === null) {
            return gamedata.turn;
        } else {
            return this.displayedTurn;
        }
    },

    /* The turn stepper's three cells are a single control with collapsed borders, so the
       arrows are DISABLED at the ends of the range rather than hidden - taking a middle
       cell out of the flow would break the control in half. "Live" is the only cell that
       still comes and goes, because it is a separate control and has nothing to say while
       the current turn is on screen.

       Also writes the panel readout, which appears in the head bar when the panel is tall
       enough for one and at the right-hand end of the control bar otherwise - see
       botPanel.setMeta. */
    updateTurnControls: function updateTurnControls() {
        var turn = combatLog.getDisplayTurn();
        var live = (combatLog.displayedTurn === null || combatLog.displayedTurn >= gamedata.turn);

        var el = document.getElementById('combatLogTurnLabel');
        if (el) el.textContent = 'TURN ' + turn;

        el = document.getElementById('previousTurnButton');
        if (el) el.disabled = (turn <= 1);

        el = document.getElementById('nextTurnButton');
        if (el) el.disabled = live;

        el = document.getElementById('currentTurnButton');
        if (el) el.style.display = live ? 'none' : '';

        /* The filter run comes off the bar while there is no print to filter - see the note
           on #combatLogButtons.no-print in logPanel.css. #LogActual's inline display is the
           whole test: showLog sets it to block and showCurrent to none, and they are its only
           two writers, so there is no third state to account for. This is the right home for
           it because every transition between the two views already ends here - showLog and
           showCurrent both finish by calling this, and initPhase calls it on every phase
           change. */
        var bar = document.getElementById('combatLogButtons');
        var print = document.getElementById('LogActual');
        if (bar) bar.classList.toggle('no-print', !print || print.style.display === 'none');

        //The Mine chip is gated on gamedata, which is why it is refreshed from HERE:
        //gamedata.initPhase calls updateTurnControls on every phase change, and that is the
        //first moment the slot map can answer "is this viewer a player".
        combatLog.syncSideControl();

        /* The readout used to open with "TURN n" as well. That made sense in the head bar,
           which stood alone; in the control bar it sits a few centimetres from a stepper
           that already says TURN n in mono, so it was a repeat costing ~55px of a row the
           panel cannot spare. The phase, and what the filters are hiding, are the two
           things nothing else on the bar says. */
        if (window.botPanel && botPanel.setMeta) {
            var meta = combatLog.phaseLabel();
            if (combatLog.hiddenCount > 0) meta += ' · ' + combatLog.hiddenCount + ' HIDDEN';
            botPanel.setMeta('log', meta);
        }
    },

    /* Whether the Mine chip can mean anything for this viewer. Split out of the control
       bar's syncControls because it is the one control whose answer depends on GAMEDATA
       rather than on view state - and gamedata is not populated when syncControls first
       runs.

       ⭐ IT SHOWS AS WELL AS HIDES, and it refuses to answer early. The old version only
       ever called .hide(), from a syncControls that runs at DOM-ready - when gamedata.slots
       is still empty, so isPlayerInGame() says "not a player" and the control went away for
       everyone, permanently. That is the Mine chip flashing on load and vanishing; the
       All | Mine | Enemy segment it replaced had exactly the same bug, which is why no
       player ever saw that control either.

       An empty slot map means the payload has not arrived, NOT that this viewer is an
       observer, so answer nothing and let a later call decide. updateTurnControls calls
       this and gamedata.initPhase calls updateTurnControls on every phase change including
       the first, so there is always a later call. */
    syncSideControl: function syncSideControl() {
        if (!window.gamedata || !gamedata.slots) return;
        if (!Object.keys(gamedata.slots).length) return;
        if (typeof gamedata.isPlayerInGame !== 'function') return;

        var isPlayer = gamedata.isPlayerInGame();
        var el = document.getElementById('combatLogMineOnly');
        if (el) el.style.display = isPlayer ? '' : 'none';

        /* An OBSERVER is on nobody's side, so "mine" would filter EVERY group away - there
           is no unit in the game isMyorMyTeamShip will say yes to. Same two-arm reasoning as
           every other allegiance surface here (arch_team_colour_logic). */
        if (!isPlayer) combatLog.sideFilter = 'all';
    },

    phaseLabel: function phaseLabel() {
        switch (gamedata.gamephase) {
            case -1: return 'DEPLOYMENT';
            case 1: return 'INITIAL ORDERS';
            case 2: return 'MOVEMENT';
            case 3: return 'FIRING';
            case 4: return 'FIRE RESOLUTION';
            case 5: return 'PRE-FIRING';
            default: return 'TURN ' + gamedata.turn;
        }
    },

    showPrevious: function showPrevious() {
        if (this.displayedTurn === null) this.displayedTurn = gamedata.turn;
        var turn = this.displayedTurn - 1;
        if (turn < 1) return;
        this.displayedTurn = turn;

        combatLog.onTurnStart(); // Clear leftover live replay messages before showing the print.
        combatLog.fetchAndShowCombatLog();
    },

    showNext: function showNext() {
        if (this.displayedTurn === null) this.displayedTurn = gamedata.turn;
        var turn = this.displayedTurn + 1; //Get the turn we want.

        if (turn >= gamedata.turn) { //Can't go forward past the current turn.
            combatLog.showCurrent();
            return;
        }
        this.displayedTurn = turn; //Set new displayedTurn for further requests.
        combatLog.onTurnStart(); // Clear leftover live replay messages before showing the print.
        combatLog.fetchAndShowCombatLog();
    },

    showCurrent: function showCurrent() {
        this.displayedTurn = gamedata.turn;

        document.getElementById('LogActual').style.display = 'none';
        document.getElementById('LogActual').innerHTML = '';  //Reset Combat Log text
        combatLog.hiddenCount = 0;
        combatLog.updateTurnControls();
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

        // Nothing is cached, so the container is about to sit there showing the PREVIOUS turn's
        // print until the response arrives. Blank it now: showLog overwrites it wholesale anyway,
        // and a stale turn on screen reads as if it belonged to the turn being requested. The
        // cached branch above returns first, so it still renders in one synchronous step.
        document.getElementById('LogActual').innerHTML = '';

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

    /* ── SORT (Stage 2c) ─────────────────────────────────────────────────────────
       A PURE function over the group array, applied in showLog only. groupByShipAndWeapon
       above stays the canonical RESOLUTION order because the replay animation reads the
       same grouping; nothing here may reach it.

       With DAMAGE cut (user, 2026-08-31) this reads nothing but the two ship names, so it
       needs no damage index and no second sweep of the fire orders. Ties fall back to the
       group's original index, which keeps the sort STABLE - i.e. within one attacker the
       resolution order survives. */
    sortGroups: function sortGroups(groups, mode) {
        if (mode !== 'attacker' && mode !== 'target') return groups;

        var nameOf = function (group, which) {
            var id = which === 'attacker' ? group[0].shooterid : group[0].targetid;
            var s = gamedata.getShip(id);
            return s && s.name ? String(s.name).toLowerCase() : '￿'; //no target sorts last
        };

        return groups
            .map(function (g, i) { return { g: g, i: i, key: nameOf(g, mode) }; })
            .sort(function (a, b) {
                if (a.key !== b.key) return a.key < b.key ? -1 : 1;
                return a.i - b.i;
            })
            .map(function (e) { return e.g; });
    },

    /* ── FILTERS (Stage 2c) ──────────────────────────────────────────────────────
       A pre-render .filter() on the group array, and like the sort it lives only on the
       printed path. gamedata.isMyorMyTeamShip is safe here even though the printed log
       carries its OWN raw ships from replay.php: logFireOrders resolves the shooter
       through gamedata.getShip on both paths, so the object this tests is the same one
       the entry is drawn from.

       "Mine" is MY SIDE, allies included - the same isMyorMyTeamShip the rest of this
       panel gates on, not isMyShip. Two states survive, all and mine; the enemy-only third
       went with the segment it was a chip of - see the note in combatLog.php. */
    filterGroups: function filterGroups(groups) {
        var side = combatLog.sideFilter;
        var find = (combatLog.findText || '').trim().toLowerCase();
        var hitsOnly = combatLog.hitsOnly;

        if (side === 'all' && !find && !hitsOnly) return groups;

        return groups.filter(function (group) {
            var shooter = gamedata.getShip(group[0].shooterid);

            if (side !== 'all') {
                //An observer is on nobody's side, so isMyorMyTeamShip is false for every
                //unit and "mine" would empty the log. The control is hidden for observers
                //(see the init block below), but guard the state anyway.
                if (!shooter) return false;
                if (!gamedata.isMyorMyTeamShip(shooter)) return false;
            }

            if (hitsOnly) {
                var hit = group.some(function (fire) { return fire.shotshit > 0; });
                if (!hit) return false;
            }

            if (find) {
                var target = gamedata.getShip(group[0].targetid);
                var weapon = shooter ? shipManager.systems.getSystem(shooter, group[0].weaponid) : null;
                var hay = [
                    shooter && shooter.name,
                    target && target.name,
                    weapon && weapon.displayName
                ].join(' ').toLowerCase();
                if (hay.indexOf(find) === -1) return false;
            }

            return true;
        });
    },

    showLog: function showLog(allFireOrders, ships = null) {
        var groups = combatLog.filterGroups(allFireOrders);
        combatLog.hiddenCount = allFireOrders.length - groups.length;
        groups = combatLog.sortGroups(groups, combatLog.sortMode);

        // One reverse map of damage entries by fire order id for the whole print, built from the
        // same ship set logFireOrders will be handed (the printed log carries its own raw ships
        // from replay.php, not gamedata.ships). Lives only for this call.
        var damageIndex = weaponManager.buildDamageIndex(ships);

        /* Stage 2d: ONE assignment. logFireOrders returns its markup on the printed path
           now, so this joins the lot instead of doing innerHTML += per fire group - which
           reparsed the whole container once per group, O(n^2) on a busy turn. */
        /* NO "Turn N:" HEADING (user, 2026-08-31). The turn stepper in the control bar
           directly above says which turn this is, and the heading plus its two <br>s cost
           three lines of a panel that only has about six. updateTurnControls at the foot
           of this function is what keeps that stepper honest. */
        var parts = [];

        if (allFireOrders.length === 0) {
            parts.push('<span class="noCombatLog">No fire orders were made this turn!</span>');
        } else if (groups.length === 0) {
            //Never let a filter read as an empty turn.
            parts.push('<span class="noCombatLog">Every fire group this turn is hidden by the current filters.</span>');
        }

        groups.forEach(function (logEntry) { // allFireOrders is an array of other arrays
            var entry = combatLog.logFireOrders(logEntry, true, ships, damageIndex);
            if (entry) parts.push(entry);
        });

        //The count is ALWAYS shown while anything is filtered out, so a filter left on
        //from a previous turn can never be mistaken for a quiet turn.
        if (combatLog.hiddenCount > 0) {
            parts.push('<div class="logfiltered">' + combatLog.hiddenCount
                + (combatLog.hiddenCount === 1 ? ' fire group' : ' fire groups')
                + ' hidden by the current filters.</div>');
        }

        var target = document.getElementById('LogActual');
        target.innerHTML = parts.join('');
        target.style.display = 'block';

        combatLog.critsShown = {}; //Empty crti tracker for next print.
        combatLog.updateTurnControls();
    },

    /* Re-draw the printed log from the cache after a sort or filter change. Deliberately
       does NOTHING while the print is not on screen: a filter click must not turn the live
       replay view into a turn print behind the player's back. */
    rerender: function rerender() {
        var target = document.getElementById('LogActual');
        if (!target || target.style.display === 'none') {
            combatLog.updateTurnControls();
            return;
        }
        combatLog.fetchAndShowCombatLog();
    },

    /* View preferences survive a reload - a player who reads by attacker should not have
       to re-pick it every game. The find box is deliberately NOT remembered: a stale text
       filter looks exactly like an empty turn. */
    loadPrefs: function loadPrefs() {
        try {
            var raw = window.localStorage.getItem(combatLog.PREF_KEY);
            if (!raw) return;
            var p = JSON.parse(raw);
            if (p.sortMode) combatLog.sortMode = p.sortMode;
            //Validated, not trusted: `enemy` was a real stored value until 2026-08-31, and
            //nothing filters on it any more - a browser still holding one would have shown an
            //empty log with no control up to explain why.
            if (p.sideFilter === 'mine' || p.sideFilter === 'all') combatLog.sideFilter = p.sideFilter;
            combatLog.hitsOnly = !!p.hitsOnly;
        } catch (e) { /* defaults stand */ }
    },

    savePrefs: function savePrefs() {
        try {
            window.localStorage.setItem(combatLog.PREF_KEY, JSON.stringify({
                sortMode: combatLog.sortMode,
                sideFilter: combatLog.sideFilter,
                hitsOnly: combatLog.hitsOnly
            }));
        } catch (e) { /* the choice still applies for this session */ }
    }

};
/* ── The combat log's control bar (LOG_PANEL_REDESIGN_PLAN.md Stage 2b/2f) ──────────
   Everything here is view state. Not one line of it reaches groupByShipAndWeapon or the
   replay animation - see the note on combatLog.sortGroups. */
$(function () {

    combatLog.loadPrefs();

    /* Paint the controls from the (possibly remembered) state, and hide the ones that
       cannot mean anything for this viewer. Called again on every tab show because
       gamedata is not necessarily populated at DOM-ready. */
    var syncControls = function syncControls() {
        //Two controls, one state - see the note in combatLog.php. Only one of them is ever
        //on screen, but both are painted so a viewport change never shows a stale one.
        $("#combatLogSort").val(combatLog.sortMode);
        $("#combatLogSortSeg .fv-log-chip").each(function () {
            $(this).attr("aria-pressed", $(this).data("sort") === combatLog.sortMode ? "true" : "false");
        });
        combatLog.syncSideControl();
        $("#combatLogMineOnly").attr("aria-pressed", combatLog.sideFilter === 'mine' ? "true" : "false");
        $("#combatLogHitsOnly").attr("aria-pressed", combatLog.hitsOnly ? "true" : "false");

        if (window.gamedata && gamedata.turn) combatLog.updateTurnControls();
    };

    var applySort = function applySort(mode) {
        combatLog.sortMode = mode;
        syncControls();
        combatLog.savePrefs();
        combatLog.rerender();
    };

    $("#combatLogSort").on("change", function () { applySort(this.value); });
    $("#combatLogSortSeg").on("click", ".fv-log-chip", function () {
        applySort(String($(this).data("sort")));
    });

    //A TOGGLE, not one chip of a segment: on = my side only, off = the whole turn.
    $("#combatLogMineOnly").on("click", function () {
        combatLog.sideFilter = (combatLog.sideFilter === 'mine') ? 'all' : 'mine';
        $(this).attr("aria-pressed", combatLog.sideFilter === 'mine' ? "true" : "false");
        combatLog.savePrefs();
        combatLog.rerender();
    });

    $("#combatLogHitsOnly").on("click", function () {
        combatLog.hitsOnly = !combatLog.hitsOnly;
        $(this).attr("aria-pressed", combatLog.hitsOnly ? "true" : "false");
        combatLog.savePrefs();
        combatLog.rerender();
    });

    //Not debounced on purpose: the filter runs over the cached group array for one turn,
    //which is tens of entries, and a keystroke delay on a find box reads as lag.
    $("#combatLogFind").on("input search", function () {
        combatLog.findText = this.value;
        combatLog.rerender();
    });

    $("#log").on("onshow", syncControls);
    syncControls();

    /* A long damage list starts folded (Stage 2f). Clicking anywhere in the entry opens
       it, except on a ship name - those are marked up as links and will one day behave
       like ones. */
    $("#combatLogContainer").on("click", ".logentry.collapsible", function (e) {
        if ($(e.target).closest(".shiplink").length) return;
        $(this).toggleClass("collapsed");
    });

    /* Left / right step turns WHILE THE PANEL HAS FOCUS - never globally, so the map keeps
       its own arrow keys, and never while the player is typing in the find box. */
    $(document).on("keydown", function (e) {
        if (e.key !== "ArrowLeft" && e.key !== "ArrowRight") return;
        var panel = document.getElementById("logcontainer");
        if (!panel || !document.activeElement || !panel.contains(document.activeElement)) return;
        if (!$("#logTab").hasClass("selected")) return;
        var tag = document.activeElement.tagName;
        if (tag === "INPUT" || tag === "SELECT" || tag === "TEXTAREA") return;
        e.preventDefault();
        if (e.key === "ArrowLeft") combatLog.showPrevious();
        else combatLog.showNext();
    });
});

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

