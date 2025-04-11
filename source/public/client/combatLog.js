'use strict';

window.combatLog = {

    displayedTurn: null,

    onTurnStart: function onTurnStart() {
        $('.logentry').remove();
    },

    logDestroyedShip: function logDestroyedShip(ship, jumped) {

        var html = '<div class="logentry"><span class="destroyed">';

        // When the name is only a number, it might not be interpreted as a string.
        // In that case, the toUpperCase goes wrong.
        // Make certain the name is a string.
        if (typeof ship.name == 'string' || ship.name instanceof String) {
            if(jumped){
                html += '<span class="shiplink" data-id="' + ship.id + '" >' + ship.name.toUpperCase() + '</span> <span style="color: green; font-weight: bold;">HAS JUMPED TO HYPERSPACE</span></span>';
            }else{
                html += '<span class="shiplink" data-id="' + ship.id + '" >' + ship.name.toUpperCase() + '</span> IS DESTROYED</span>';
            }    
        } else {
            if(jumped){
                html += '<span class="shiplink" data-id="' + ship.id + '" >' + ship.name.toUpperCase() + '</span> <span style="color: green; font-weight: bold;">HAS JUMPED TO HYPERSPACE</span></span>';
            } else {
                html += '<span class="shiplink" data-id="' + ship.id + '" >' + ship.name + '</span> IS DESTROYED</span>';
            }    
        }

        var element = $(html).appendTo("#log");  //Changed to append - DK

        $("#log").scrollTop($("#log")[0].scrollHeight);
        return element;
        
    },

    logFireOrders: function logFireOrders(orders, printedLog = false) {

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
        
        for (var a in orders){            

            count++;
            var fire = orders[a];

            var weapon = shipManager.systems.getSystem(ship, fire.weaponid);
		
		
            var modeIteration = fire.firingMode; //change weapons data to reflect mode actually used
            if(modeIteration != weapon.firingMode){
                while(modeIteration != weapon.firingMode){ //will loop until correct mode is found
                weapon.changeFiringMode();
                }
            }
		    
            shots += fire.shots;
            shotshit += fire.shotshit;
            shotsintercepted += fire.intercepted;
            if (fire.shots > 0) ordersC += 1;
			if (fire.shotshit>0) ordersChit += 1;
			if (fire.intercepted>0) ordersCintercepted += 1;
            weaponManager.getDamagesCausedBy(fire, damages);
            var needed = fire.needed;
            //if (needed < 0) needed = 0; //I skip this - if intercepted below 0, let's show it.
            //if (fire.shots > 0){ //otherwise shot is purely technical ... BUT show it too!
                if (needed < lowC)
                lowC = needed;
                if (needed > highC)
                highC = needed;

                if (fire.pubnotes)
                notes += fire.pubnotes + " ";
            //}
                        
        }

        var html = '<div class="logentry fire-' + orders[0].id + '"><span class="logheader fire">FIRE: </span><span>';
        html += '<span class="shiplink" data-id="' + ship.id + '" >' + ship.name + '</span>';

        var counttext = count > 1 ? count + "x " : "";
        var chancetext = "";
        if (lowC == highC) chancetext = "Chance to hit: " + lowC + "%";else chancetext = "Chance to hit: " + lowC + "% - " + highC + "%";

        if (!target) chancetext = "";

        var intertext = "";
        //if (shotsintercepted > 0) intertext = ", " + shotsintercepted + " intercepted";
		//if (shotsintercepted > 0) intertext = ", " + ordersCintercepted + '(' + shotsintercepted + ") intercepted";
		if (shotsintercepted > 0){
			if(ordersC != shots){
			 	intertext = ", " + ordersCintercepted + '(' + shotsintercepted + ") intercepted";
			}else{
				if (shotsintercepted > 0) intertext = ", " + shotsintercepted + " intercepted";				
			}	
		}
		
        var targettext = "";
        if (target) targettext = '<span> at </span><span class="shiplink target" data-id="' + target.id + '" >' + target.name + '</span>';

        var shottext = "";
        //if (target) shottext = ', ' + shotshit + '/' + shots + ' shots hit' + intertext + '.';
		//if (target) shottext = ', ' + ordersChit + '(' +shotshit + ')/' + ordersC + '(' +shots + ') shots hit' + intertext + '.';
		if (target){
			if(ordersC != shots){
			    shottext = ', ' + ordersChit + '(' +shotshit + ')/' + ordersC + '(' +shots + ') shots hit' + intertext + '.';
			}else{
			    shottext = ', ' + shotshit + '/' + shots + ' shots hit' + intertext + '.';				
			}	
		}
		
        var notestext = "";
        if (notes) notestext = '<span class="pubotes">' + notes + '</span>';

        var shortText = false;
        if(weaponManager.doShortLogText(fire)) shortText = true;

        //Some orders don't need the full log text, e.g. Reactor overload, hyperspace jump.    
        if(shortText){
            html += notestext;
        }else{
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

                    if (!d.destroyed) {
                        continue;
                    }

                    var first = "";
                    var comma = ",";
                    if (damagehtml.length == 0) {
                        first = " Systems destroyed: ";
                        comma = "";
                    }

                    damagehtml += first + '<span class="damage">' + comma + ' ' + shipManager.systems.getDisplayName(system) + '</span>';
                }

                //if (totaldam > 0){ //display fire orders that did no damage, too!
                //          html += '<li><span class="shiplink victim" data-id="'+ship.id+'" >' + victim.name + '</span> damaged for ' + totaldam + '(+ ' + armour + ' armour). '+ damagehtml+'</li>';
                if(fire.damageclass == "HyperspaceJump") continue; //Do not show damage to Primary Structure when jumping to Hyperspace. 				
                html += '<li><span class="shiplink victim" data-id="' + ship.id + '" >' + victim.name + '</span> damaged for ' + totaldam + ' (total armour mitigation: ' + armour + ').</li>';
                if (damagehtml.length > 1) {
                    html += '<li>' + damagehtml + '</li>';
                }
                //}
            }

            html += "</ul>";
        }
		
		
        if(printedLog){ //Different method fo listing depending on whether player is watching a Replay animation or just browsing the printed log :)
            var targetDiv = document.getElementById("LogActual"); 
            targetDiv.style.display = "block";
            targetDiv.innerHTML += html;
        }else{
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

    logCriticals: function logCriticals(ship, string) {

        var html = '<div class="logentry">';
        html += '<span class="shiplink" data-id="' + ship.id + '" >' + ship.name + '</span>';
        html += string;
        html += '</span></div></ul>';

        $(html).prependTo("#log");
    },

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
        if(this.displayedTurn === null){
            return gamedata.turn;
        }else{
            return this.displayedTurn; 
        } 
    },    

    showPrevious: function showPrevious() {
        if(this.displayedTurn === null) this.displayedTurn = gamedata.turn;
        var turn = this.displayedTurn - 1;
        if (turn < 1) return;
        this.displayedTurn = turn;

        if(this.displayedTurn < gamedata.turn){
            document.getElementById('nextTurnButton').style.display = 'inline-block'; // Display next button when relevant.
            document.getElementById('currentTurnButton').style.display = 'inline-block'; // Display next button when relevant.
        }

        combatLog.fetchAndShowCombatLog();
    },    

    showNext: function showNext() {
        if(this.displayedTurn === null) this.displayedTurn = gamedata.turn;
        var turn = this.displayedTurn+1; //Get the turn we want.
        this.displayedTurn = turn; //Set new displayedTurn for further requests.
        
        if(this.displayedTurn >= gamedata.turn){
            document.getElementById('nextTurnButton').style.display = 'none'; // Hide next turn button
            document.getElementById('currentTurnButton').style.display = 'none'; //Hide Turn number.            
            document.getElementById('LogActual').style.display = 'none'; //Hide Turn number.
            return; //Can't go forward past current turn. 
        } 
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

    fetchAndShowCombatLog: function fetchAndShowCombatLog(){
        jQuery.ajax({
            type: 'GET',
            url: 'replay.php',
            dataType: 'json',
            data: {
                turn: this.displayedTurn,
                gameid: gamedata.gameid,
                time: new Date().getTime()
            },
            success: function (data) {             
                var allFireOrders = []; //Initialise
				allFireOrders = combatLog.groupByShipAndWeapon(weaponManager.getAllFireOrdersForLogPrint(data.ships, data.turn)); //Find and group appropriate fire orders.	
                combatLog.showLog(allFireOrders); //Now print log from selected turn     		
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

    showLog: function showLog(allFireOrders) {
        // Get the current turn from the combat log system
        var currentTurn = window.combatLog.getDisplayTurn();
    
        // Start building the log HTML
        var html = '<br><span style="font-size:12px; font-weight:bold; text-decoration:underline;">Turn ' + currentTurn + ':</span><br>';
    
        // Check if the allFireOrders array is empty
        if (allFireOrders.length === 0) {
            html += '<span style="font-size:12px;"><br>No fire orders were made this turn!</span>';
        }
    
        // Update the content of LogActual with the current turn and optional message
        document.getElementById('LogActual').innerHTML = html;
    
        // Process fire orders if any
        allFireOrders.forEach(function (logEntry) { // allFireOrders is an array of other arrays
            combatLog.logFireOrders(logEntry, true);
        });
    
        // Show the LogActual div
        document.getElementById('LogActual').style.display = 'block'; // Set to 'block' or 'inline-block' depending on your layout
    }

};