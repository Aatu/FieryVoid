"use strict";

jQuery(function () {
	$(".system .on").on("contextmenu", function (e) {
		e.preventDefault();
	});
	$(".system .off").on("contextmenu", function (e) {
		e.preventDefault();
	});
});

window.shipWindowManager = {

	prepare: function prepare() {

		for (var i in gamedata.ships) {
			var ship = gamedata.ships[i];
			var n = ship.shipStatusWindow;
			/*            if (shipManager.movement.isRolled(ship)){
                   n.addClass("rolled");
               }else{
                   n.removeClass("rolled");
               }*/
		}
	},

	close: function close() {
		$(this).parent().parent().hide();
	},

	open: function open(ship) {

		var old;
		if (ship.userid == gamedata.thisplayer) {
			old = $(".shipwindow.owned:visible");
		} else {
			old = $(".shipwindow.enemy:visible");
		}

		var n = ship.shipStatusWindow;

		if (!n) return;

		if (n.css("display") == "block") return;

		if (old.length) {
			old.hide();
			n.css("top", old.css("top")).css("left", old.css("left"));
		}

		this.updateNotes(ship);

		n.show();
	},

	checkIfAnyStatusOpen: function checkIfAnyStatusOpen(ship) {
		var old;

		if (ship.userid == gamedata.thisplayer) {
			old = $(".shipwindow.owned:visible");
		} else {
			old = $(".shipwindow.enemy:visible");
		}

		if (old.length) shipWindowManager.open(ship);
	},

	createShipWindow: function createShipWindow(ship) {
		var template;

		if (ship.base && !ship.smallBase) {
			template = $("#shipwindowtemplatecontainer .shipwindow.base");
		} else {
			template = $("#shipwindowtemplatecontainer .shipwindow.ship");
		}

		var shipwindow = template.clone(true).appendTo("body");

		shipwindow.draggable();

		if (ship.userid == gamedata.thisplayer) {
			shipwindow.addClass("owned");
		} else {
			shipwindow.addClass("enemy");
		}

		if (gamedata.getPlayerTeam) {
			if ( gamedata.getPlayerTeam() === 1) {
				if (ship.userid == gamedata.thisplayer) {
					shipwindow.addClass("left");
				} else {
					shipwindow.addClass("right");
				}
			} else {
				if (ship.userid == gamedata.thisplayer) {
					shipwindow.addClass("right");
				} else {
					shipwindow.addClass("left");
				}
			}
		}

		shipwindow.data("ship", ship.id);
		shipwindow.addClass("ship_" + ship.id);
		shipwindow.attr('id', 'shipWindow' + ship.id);

		shipWindowManager.populateShipWindow(ship, shipwindow);

		if (ship.hitChart.length > 0 && gamedata.gamephase > -2 || typeof ship.hitChart != "undefined" && gamedata.gamephase > -2) {
			shipWindowManager.hitChartSetup(ship, shipwindow);
		}

		shipWindowManager.bindEvents(shipwindow);

		return shipwindow;
	},

	bindEvents: function bindEvents(shipwindow) {

		$(".close", shipwindow).on("click", shipWindowManager.close);
		$(".system .plus", shipwindow).on("click", shipWindowManager.clickPlus);
		$(".system .minus", shipwindow).on("click", shipWindowManager.clickMinus);

		$(".system .off", shipwindow).on("click", shipManager.power.onOfflineClicked);
		$(".system .on", shipwindow).on("click", shipManager.power.onOnlineClicked);

		$(".system .off", shipwindow).on("contextmenu", shipManager.power.offlineAll);
		$(".system .on", shipwindow).on("contextmenu", shipManager.power.onlineAll);

		$(".system .overload", shipwindow).on("click", shipManager.power.onOverloadClicked);
		$(".system .stopoverload", shipwindow).on("click", shipManager.power.onStopOverloadClicked);
		$(".system .holdfire", shipwindow).on("click", window.weaponManager.onHoldfireClicked);
	},

	populateShipWindow: function populateShipWindow(ship, shipwindow) {

		shipwindow.find(".icon img").attr("src", "./" + ship.imagePath);

		if (gamedata.turn != 0) {
			shipwindow.find(".topbar .value.name").html("");
			shipwindow.find(".topbar .valueheader.name").html(ship.name);
			shipwindow.find(".topbar .value.shipclass").html(ship.shipClass); // + " (" + ship.occurence + ")");
		} else {
			shipwindow.find(".topbar .value.name").html("");
			shipwindow.find(".topbar .valueheader.name").html("");
			shipwindow.find(".topbar .value.shipclass").html(ship.shipClass); // + " (" + ship.occurence + ")");
		}

		shipWindowManager.addSystems(ship, shipwindow, 1);
		shipWindowManager.addSystems(ship, shipwindow, 0);
		shipWindowManager.addSystems(ship, shipwindow, 2);

		if (ship.base && !ship.smallBase) {
			shipWindowManager.addSystems(ship, shipwindow, 31);
			shipWindowManager.addSystems(ship, shipwindow, 32);
			shipWindowManager.addSystems(ship, shipwindow, 41);
			shipWindowManager.addSystems(ship, shipwindow, 42);
		} else {
			shipWindowManager.addSystems(ship, shipwindow, 3);
			shipWindowManager.addSystems(ship, shipwindow, 4);
		}
	},

	hitChartSetup: function hitChartSetup(ship, shipwindow) {

		var names = [];
		var parentDiv = shipwindow.find(".buttons")[0];
		var toDo = ship.hitChart.length; //not always works correctly!

		var div = shipwindow.find(".hitChartDiv");
		div = div[0];
		div.id = "hitChartDiv" + ship.id;
		$(div).addClass("hitChartDisabled");

		var names = ["Primary", "Front", "Aft", "Port", "Starboard"];

		if (ship.base && !ship.smallBase) {
			names[1] = "Sections";
			toDo = 2;
		} else {
			toDo = 5; //(almost) always try to show all 5 sections, there may be holes
		}

		for (var i = 0; i < toDo; i++) {

			//skip if appropriate entry does not exist
			if (ship.hitChart[i] === undefined) {
				continue; //no appropriate entry, skip it
			}

			var template = $("#hitChartTable");
			var table = template.clone(true);
			table = table[0];
			table.className = "hitChartTable";

			var tr = document.createElement("tr");
			var th = document.createElement("th");
			th.colSpan = 2;
			th.innerHTML = names[i];

			tr.appendChild(th);
			table.appendChild(tr);

			var list = [];
			var current = 0;

			for (var key in ship.hitChart[i]) {
				var name = shipWindowManager.getName(ship.hitChart[i][key]);
				var hitChance = Math.floor((key - current) / 20 * 100);
				var item = [name, hitChance];

				current = key;

				list.push(item);

				list.sort(function (a, b) {
					if (a[1] < b[1]) {
						return 1;
					} else return -1;
				});
			}

			for (var j = 0; j < list.length; j++) {
				var tr = document.createElement("tr");

				var td = document.createElement("td");
				td.innerHTML = list[j][0];
				td.style.borderBottom = "1px solid #496791";
				tr.appendChild(td);

				var td = document.createElement("td");
				td.innerHTML = list[j][1] + "%";
				td.style.borderBottom = "1px solid #496791";
				tr.appendChild(td);
				table.appendChild(tr);
			}

			div.appendChild(table);
		}

		var target = shipwindow[0];
		target.appendChild(div);

		var button = document.createElement("button");
		button.type = "input";
		button.innerHTML = "Display Hit Chart";
		button.id = "hitChartButton" + ship.id;
		button.className = "interceptButton";
		button.style.marginLeft = "12px";
		$(button).data("id", ship.id);
		button.addEventListener("click", function () {
			var div = document.getElementById("hitChartDiv" + $(this).data("id"));
			if (div.className == "hitChartDiv hitChartDisabled") {
				div.className = "hitChartDiv";
			} else if (div.className == "hitChartDiv") {
				div.className = "hitChartDiv hitChartDisabled";
			}
		});

		$(button).appendTo(parentDiv);
	},

	getName: function getName(name) {
		//first hide retargeting
		var n = name.indexOf(":");
		if (n > 0) {
			//there is retargeting
			name = name.substring(n + 1);
		}

		/* since table rows don't grow needlessly now, I believe this is just misleading and not helpful
  if (name == "Heavy Particle Cannon"){
  	return "Heavy Particle";
  }
  else if (name == "Light Particle Cannon"){
  	return "Light Particle";
  }
  else if (name == "Light Particle Beam"){
  	return "LPB";
  }
  else if (name == "Standard Particle Beam"){
  	return "SPB";
  }
  else if (name == "Quad Particle Beam"){
  	return "Quad PB";
  }
  else if (name == "Laser/Pulse Array"){
  	return "L/P Array";
  }
  else if (name == "Light Pulse Cannon"){
  	return "Light Pulse";
  }
  else if (name == "Medium Pulse Cannon"){
  	return "Medium Pulse";
  }
  else if (name == "Heavy Pulse Cannon"){
  	return "Heavy Pulse";
  }
  else if (name == "Medium Plasma Cannon"){
  	return "Medium Plasma";
  }
  else if (name == "Heavy Plasma Cannon"){
  	return "Heavy Plasma";
  }
  else if (name == "Class-S Missile Rack"){
  	return "S Rack";
  }
  else if (name == "Class-L Missile Rack"){
  	return "L Rack";
  }
  else if (name == "Class-LH Missile Rack"){
  	return "LH Rack";
  }
  else if (name == "Class-B Missile Rack"){
  	return "B Rack";
  }
  else if (name == "Interceptor Prototype"){
  	return "Interceptor";
  }
  else if (name == "Interceptor I"){
  	return "Interceptor";
  }
  else if (name == "Interceptor II"){
  	return "Interceptor";
  }
  if (name == "Improved Blast Laser"){
  	return "Imp. Bl. Laser";
  }
  if (name == "EM-Wave Disruptor"){
  	return "EMW Disruptor";
  }
  if (name == "Medium Burst Beam"){
  	return "Medium BB";
  }
  if (name == "Heavy Burst Beam"){
  	return "Heavy BB";
  }
  if (name == "Dual Burst Beam"){
  	return "Dual BB";
  }
  if (name == "Burst Pulse Cannon"){
  	return "Burst PC";
  }
  else return name;
  */
		return name;
	},

	updateNotes: function updateNotes(ship) {
		var shipWindow = ship.shipStatusWindow;
		shipWindow.find(".notes").html("");

		var abilities = Array();
		var notes = Array();

		/*unnecessary*/ /*
                  if (ship.hitChart.length > 0 || typeof ship.hitChart[0] != "undefined"){
                  notes.push("&nbsp;has B5W Hit Table.");
                  }*/

		//BUTTONS like Defensive Fire
		var belowIcon = shipWindow.find(".notes");

		var input = document.createElement("input");
		input.type = "button";
		input.value = "Defensive Fire";
		input.className = "interceptButton";
		input.className += " interceptDisabled";

		$(input).click(function () {
			weaponManager.checkSelfIntercept(ship);
		});

		$(belowIcon).append(input);

		if (gamedata.gamephase == 3 && ship.userid == gamedata.thisplayer) {

			if (weaponManager.canSelfIntercept(ship)) {
				input.className = "interceptButton";
				input.className += " interceptEnabled";
			}
		}

		// adaptive Armour
		var input = document.createElement("input");
		input.type = "button";
		input.value = "Adaptive Armour";
		input.className = "interceptButton";
		input.className += " interceptDisabled";

		/*	$(input).click(function(){
  		ajaxInterface.getAdaptiveArmour(ship.id, function(id){
  			console.log(id);
  		});
  	});
  */

		$(input).click(function () {
			if (document.getElementById("outerArmourDiv" + ship.id) == null) {
				shipWindowManager.createAdaptiveArmourGUI(ship);
			} else if (document.getElementById("outerArmourDiv" + ship.id).style.display == "none") {
				document.getElementById("outerArmourDiv" + ship.id).style.display = "block";
			}
		});

		$(belowIcon).append(input);

		if (ship.adaptiveArmour) {
			if (gamedata.gamephase == 1 && ship.userid == gamedata.thisplayer) {
				input.className = "interceptButton";
				input.className += " interceptEnabled";
			}
		}
	    
        if(!ship.fighter){
            abilities.push("&nbsp;TC: " + ship.turncost + " TD: " + ship.turndelaycost  );
		var fDef = ship.forwardDefense*5;
		var sDef = ship.sideDefense*5
		abilities.push("&nbsp;Profile (F/S): " + fDef + "/" + sDef + "; Ini: " + ship.iniativebonus );
        }

	if(ship.flight){
		var flightArmour = shipManager.systems.getFlightArmour(ship);
		abilities.push("&nbsp;" + flightArmour);
		abilities.push("&nbsp;Thrust: " + ship.freethrust);
	}

		if (ship.agile) {
			abilities.push("&nbsp;Agile ship");
		}

		if (shipManager.movement.isRolled(ship)) {
			notes.push("&nbsp;Ship is rolled.");
		}

		if (shipManager.movement.isRolling(ship)) {
			notes.push("&nbsp;Ship is rolling.</p><p>");
		}

		if (gamedata.turn == 0) {
			if (ship.fighters.length != 0) {
				for (var i in ship.fighters) {
					var amount = ship.fighters[i];
					if (i == "normal") {
						//skip description of kind of fighters
						notes.push("&nbsp;&nbsp;&nbsp;" + amount + " fighters");
					} else if (i == "superheavy" || i == "heavy" || i == "medium" || i == "light" || i == "ultralight") {
						//fighters with description
						notes.push("&nbsp;&nbsp;&nbsp;" + amount + " " + i + " fighters");
					} else {
						//something other than fighters
						notes.push("&nbsp;&nbsp;&nbsp;" + amount + " " + i);
					}
				}
			}

			if (ship.notes != '') {
				notes.push("&nbsp;" + ship.notes);
			}

			if (ship.limited != 0) {
				notes.push("&nbsp;limited: " + ship.limited + "%");
			}

			if (ship.variantOf != '') {
				notes.push("&nbsp;" + ship.occurence + ' variant of ' + ship.variantOf);
			}

			if (ship.isd != '') {
				notes.push("&nbsp;in service: " + ship.isd);
			}

			if (ship.unofficial == true) {
				notes.push("&nbsp;<b><i>CUSTOM UNIT</i></b>");
			}
		}

		/* Set everything into the notes decently. */
		/* If we have both abilities and notes, insert a hr as seperator*/
		for (var i = 0; i < abilities.length; i++) {
			shipWindow.find(".notes").append("<p>");
			shipWindow.find(".notes").append(abilities[i]);
			shipWindow.find(".notes").append("</p>");
		}

		if (abilities.length > 0 && notes.length > 0) {
			/* Insert fancy hr. */
			shipWindow.find(".notes").append('<hr width=90% height=1px border=0 color=#496791>');
		}

		for (var index = 0; index < notes.length; index++) {
			shipWindow.find(".notes").append("<p>");
			shipWindow.find(".notes").append(notes[index]);
			shipWindow.find(".notes").append("</p>");
		}
	},

	addSystems: function addSystems(ship, shipwindow, location) {

		var type;
		var table = " table";
		var dest = "";

		if (ship.base && !ship.smallBase) {
			type = ".shipwindow.base_";

			if (location > 4) {
				table = " #" + location;
			}

			dest = shipwindow.find("#shipSection_" + location.toString()[0]).find(table);
		} else {
			type = ".shipwindow.ship_";
			dest = type + ship.id + " #shipSection_" + location + table;
		}

		if (ship.draziHCV) {
			var front = shipwindow.find("#shipSection_" + 0);
			front.css("margin-bottom", "105px");
			front.css("margin-top", "55px");
		}

		var systems = shipManager.systems.getSystemsForShipStatus(ship, location);
		var structure = shipManager.systems.getStructureSystem(ship, location);

		var destination = $(dest);

		if (systems.length == 0) {
			destination.css("display", "none");
			return;
		}

		var arrangement;
		var col2 = 2;
		var col3 = 2; //for columns that may be equal or wider to col2
		var col4 = 4;
		if (location == 0) {
			arrangement = shipWindowManager.getFinalArrangementFour(ship, systems, structure);
		} else if (location < 3) {
			arrangement = shipWindowManager.getFinalArrangementFour(ship, systems, structure);
		}
		else if (location > 30){
			arrangement = shipWindowManager.getFinalArrangementFour(ship, systems, structure);		
		}
		else{
			col2 = 1; //single column here
			col3 = 2; //double column even here!
			col4 = 3;
			//arrangement = shipWindowManager.getFinalArrangementTwo(ship, systems, structure, location);
			//Marcin Sawicki: I think 3 icons in a row would be fine on sides, and will help ships with lots of systems there (...especially when they have no Aft!)
			arrangement = shipWindowManager.getFinalArrangementThree(ship, systems, structure, location);
		}

		var index = 0;
		for (var i in arrangement) {
			var group = arrangement[i];
			var row;
			if (group.length == 1){
				row = $('<tr><td colspan="'+col4+'" class="systemcontainer_'+index+'"></td></tr>');
			}
			else if (group.length == 2){
				if (location == 4){//reverse order for Stbd!
					row = $('<tr><td colspan="'+col3+'" class="systemcontainer_'+(index+1)+'"></td><td colspan="'+col2+'" class="systemcontainer_'+(index)+'"></td></tr>');
				}
				else {
					row = $('<tr><td colspan="'+col2+'" class="systemcontainer_'+index+'"></td><td colspan="'+col3+'" class="systemcontainer_'+(index+1)+'"></td></tr>');
				}
			}
			else if (group.length == 3) {
				if (location == 4){//reverse order for Stbd!
					row = $('<tr><td class="systemcontainer_'+(index+2)+'"></td>'
						+'<td colspan="'+col2+'" class="systemcontainer_'+(index+1)+'"></td>'
						+'<td class="systemcontainer_'+(index)+'"></td></tr>').appendTo(destination);
				}else{
					row = $('<tr><td class="systemcontainer_'+index+'"></td>'
						+'<td colspan="'+col2+'" class="systemcontainer_'+(index+1)+'"></td>'
						+'<td class="systemcontainer_'+(index+2)+'"></td></tr>').appendTo(destination);
				}
			}				
			else if (group.length == 4){	
				row = $('<tr><td class="systemcontainer_'+index+'"></td><td class="systemcontainer_'+(index+1)+'"></td>'
				+'<td class="systemcontainer_'+(index+2)+'"></td><td class="systemcontainer_'+(index+3)+'"></td></tr>')
			}

			if (location == 2 || location == 32 || location == 42) {
				row.prependTo(destination);
			} else {
				row.appendTo(destination);
			}

			for (var a in group) {
				var system = group[a];
				shipWindowManager.addSystem(ship, system, $(".systemcontainer_" + index, destination));
				index++;
			}
		}

		if (location == 3) {
			$('<div style="height:' + arrangement.length * 30 + 'px"></div>').appendTo(".shipwindow.ship_" + ship.id + " .col1");
		}
		if (location == 4) {
			$('<div style="height:' + arrangement.length * 30 + 'px"></div>').appendTo(".shipwindow.ship_" + ship.id + " .col3");
		}
	},
	

	getFinalArrangementTwo: function(ship, systems, structure, location){
		
		var structDone = false;

		var grouped = Array();
		var list = Array();

		if (structure) {
			if (location == 32 || location == 42) {
				grouped.push(Array(structure));
				structDone = true;
			}
		}

		for (var i = 0; i < systems.length; i++) {
			var system = systems[i];
			if (systems.length % 2 == 1 && i == 0) {
				grouped.push(Array(system));
			} else {
				list.push(system);
				if (list.length == 2) {
					grouped.push(list);
					list = Array();
				}
			}
		}

		if (!structDone){
			grouped.push(Array(structure));
		}

		return grouped;

	},


	//Marcin Sawicki: i think there's enough room for 3 icons on the sides, and it will help when many systems are present
	getFinalArrangementThree: function(ship, systems, structure, location){
		var structDone = false;
		var grouped = Array();
		var list = Array();

		if (structure){
			if (location == 32 || location == 42){
				grouped.push(Array(structure));
				structDone = true;
			}
		}

		for (var i= 0;i<systems.length;i++){
			var system = systems[i];
			/* let's try with top-heavy arrangement (orphan on the bottom instead of on top)
			if (systems.length % 2 == 1 && i == 0){
				grouped.push(Array(system));
			}*/
			//else {
				list.push(system);
				if (list.length == 3){
					grouped.push(list);
					list = Array();
				}
			//}
		}

		if (list.length > 0){ //something was left over!
					grouped.push(list);
					list = Array();
		}

		if (!structDone) {
			grouped.push(Array(structure));
		}

		return grouped;
	},

	getFinalArrangementFour: function getFinalArrangementFour(ship, systems, structure) {

		var grouped = shipManager.systems.groupSystems(systems);

		if (ship.base && ship.smallBase) {
			grouped = shipWindowManager.combineGroupsForBase(grouped);
		} else {
			grouped = shipWindowManager.combineGroups(grouped);
		}

		grouped = shipWindowManager.addStructure(grouped, structure);

		return grouped;
	},

	addStructure: function addStructure(grouped, structure) {

		if (!structure) return grouped;

		var ones = Array();
		var deletes = Array();

		for (var i in grouped) {
			var group = grouped[i];

			if (group.length == 2) {
				grouped.push(Array(group[0], structure, group[1]));
				grouped.splice(i, 1);
				return grouped;
			}

			if (group.lenght == 1 && ones.length < 2) {
				ones.push(group[i]);
				deletes.push(i);
				if (ones.length == 2) {
					grouped.push(Array(ones[0][0], structure, ones[1][0]));
					for (var d in deletes) {
						grouped.splice(deletes[d], 1);
					}
					return grouped;
				}
			}
		}

		grouped.push(Array(structure));

		return grouped;
	},

	combineGroups: function combineGroups(grouped) {
		var finals = Array();

		for (var i in grouped) {
			var group = grouped[i];
			if (shipWindowManager.isInFinal(finals, group)) continue;

			if (group.length == 1 || group.length == 2) {
				var found = false;
				for (var a in grouped) {
					var other = grouped[a];
					if (!found && other.length == 2 && !shipWindowManager.isInFinal(finals, other) && group != other) {
						if (group.length == 1) {
							finals.push(Array(other[0], group[0], other[1]));
						} else {
							finals.push(Array(other[0], group[0], group[1], other[1]));
						}

						found = true;
						break;
					}
				}
				if (!found) {
					finals.push(group);
				}
			} else {
				finals.push(group);
			}
		}

		return finals;
	},

	combineGroupsForBase: function combineGroupsForBase(grouped) {
		var finals = Array();

		for (var i in grouped) {
			var group = grouped[i];
			if (shipWindowManager.isInFinal(finals, group)) continue;

			if (group.length == 1 || group.length == 2) {
				var found = false;
				for (var a in grouped) {
					var other = grouped[a];
					if (!found && (other.length == 2 || other.length == 1) && !shipWindowManager.isInFinal(finals, other) && group != other && group[0].weapon && other[0].weapon) {
						if (group.length == 1) {
							if (typeof other[1] != "undefined") {
								finals.push(Array(other[0], group[0], other[1]));
							} else {
								finals.push(Array(other[0], group[0]));
							}
						} else {
							finals.push(Array(other[0], group[0], group[1], other[1]));
						}

						found = true;
						break;
					}
				}
				if (!found) {
					finals.push(group);
				}
			} else {
				finals.push(group);
			}
		}

		var ulti = [];
		var remains = [];

		for (var i in finals) {
			if (finals[i].length == 1) {
				remains.push(finals[i]);
			} else {
				ulti.push(finals[i]);
			}
		}

		if (remains.length <= 3) {
			var group = [];
			for (var i in remains) {
				for (var j in remains[i]) {
					group.push(remains[i][j]);
				}
			}

			ulti.push(group);
			return ulti;
		}

		return finals;
	},

	isInFinal: function isInFinal(finals, group) {

		for (var i in group) {
			var system = group[i];
			for (var a in finals) {
				for (var b in finals[a]) {
					var other = finals[a][b];
					if (other == system) return true;
				}
			}
		}

		return false;
	},

	getDestinationForSystem: function getDestinationForSystem(ship, location) {
		return $(".shipwindow.ship_" + ship.id + " #shipSection_" + location + " table");
	},

	setDataForSystem: function setDataForSystem(ship, system) {

		var shipwindow = ship.shipStatusWindow;
		if (shipwindow) {
			if (ship.adaptiveArmour == true) {
				shipWindowManager.setAdaptiveArmour(ship);
				if (gamedata.gamephase != 1) {
					var div = document.getElementById("outerArmourDiv" + ship.id);

					if (div) {
						div.style.display = "none";
					}
				}
			}

			if (ship.flight) {
				flightWindowManager.setData(ship, system, shipwindow);
			} else {
				if (ship.base && system.name == "reactor") {
					var reactors = shipManager.power.getAllReactors(ship);
					for (var i = 0; i < reactors.length; i++) {
						if (!reactors[i].destroyed) {
							shipWindowManager.setSystemData(ship, reactors[i], shipwindow);
						}
					}
				} else {
					shipWindowManager.setSystemData(ship, system, shipwindow);

					if (system.dualWeapon && system.weapons) {
						var dualsystem = system.weapons[system.firingMode];
						shipWindowManager.setSystemData(ship, dualsystem, shipwindow);

						if (system.duoWeapon) {
							for (var i in system.weapons) {
								var subweapon = system.weapons[i];
								shipWindowManager.setSystemData(ship, subweapon, shipwindow);
							}
						}
					} else {
						shipWindowManager.setSystemData(ship, system, shipwindow);
					}

					//if (system.name == "scanner"){
					if (system.isScanner()) {
						shipWindowManager.addEW(ship, shipwindow);
					}
				}
			}
		}
	},

	setAdaptiveArmour: function setAdaptiveArmour(ship) {
		var shipwindow = ship.shipStatusWindow;
		var div = shipwindow.find(".AA");
		var table = div.find("table");

		if (table.length > 0) {
			table = table[0];
			table.innerHTML = "";

			var tr = document.createElement("tr");
			var th = document.createElement("th");
			th.style.border = "1px solid #496791";
			th.colSpan = "2";
			th.innerHTML = "Adaptive Armour";
			tr.appendChild(th);
			table.appendChild(tr);

			var tr = document.createElement("tr");
			var th = document.createElement("th");
			th.innerHTML = "Type";
			tr.appendChild(th);
			table.appendChild(tr);

			var th = document.createElement("th");
			var th = document.createElement("th");
			th.innerHTML = "Active Amount";
			tr.appendChild(th);
			table.appendChild(tr);
			if (div[0].style.display == "none") {
				for (var type in ship.armourSettings) {
					if (ship.armourSettings[type][1] > 0) {
						var tr = document.createElement("tr");

						var td = document.createElement("td");
						td.innerHTML = type;
						td.style.textAlign = "center";
						tr.appendChild(td);

						var td = document.createElement("td");
						td.innerHTML = ship.armourSettings[type][1];
						td.style.textAlign = "center";
						tr.appendChild(td);
					}

					$(table).append(tr);
				}
			} else {
				for (var type in ship.armourSettings) {
					if (ship.armourSettings[type][1] > 0) {
						var tr = document.createElement("tr");

						var td = document.createElement("td");
						td.innerHTML = type;
						td.style.textAlign = "center";
						tr.appendChild(td);

						var td = document.createElement("td");
						td.innerHTML = ship.armourSettings[type][1];
						td.style.textAlign = "center";
						tr.appendChild(td);
					}

					$(table).append(tr);
				}
			}

			$(div).append(table);
			$(div).show();
		}
	},

	setData: function setData(ship) {
		var shipwindow = ship.shipStatusWindow;
		if (shipwindow) {
			if (ship.adaptiveArmour == true) {
				shipWindowManager.setAdaptiveArmour(ship);
				if (gamedata.gamephase != 1) {
					var div = document.getElementById("outerArmourDiv" + ship.id);

					if (div) {
						div.style.display = "none";
					}
				}
			}

			if (ship.flight) {
				for (var i in ship.systems) {
					var fighter = ship.systems[i];
					flightWindowManager.setData(ship, fighter, shipwindow);
				}
			} else {
				shipWindowManager.addEW(ship, shipwindow);

				for (var i in ship.systems) {
					var system = ship.systems[i];

					if (system.dualWeapon) {
						for (var index in system.weapons) {
							var weapon = system.weapons[index];

							// ignore the weapon that isn't currently selected
							if (weapon.id != system.weapons[system.firingMode].id) {
								continue;
							}

							if (weapon.duoWeapon) {
								if (!shipManager.power.isOffline(ship, system)) {
									// Don't bother setting anything if the top system is offline
									for (var subindex in weapon.weapons) {
										shipWindowManager.setSystemData(ship, weapon.weapons[subindex], shipwindow);
									}
								}
							} else if (!system.weapons[system.firingMode].duoWeapon) {
								shipWindowManager.setSystemData(ship, weapon, shipwindow);
							}
						}
					} else {
						shipWindowManager.setSystemData(ship, system, shipwindow);
					}
				}
			}
		}
	},

	addEW: function addEW(ship, shipwindow) {
		var dew =  ew.getDefensiveEW(ship);
		var ccew = ew.getCCEW(ship);
		var bdew = ew.getBDEW(ship) * 0.25;
		var elint = shipManager.isElint(ship);

		if (!shipwindow) {
			shipwindow = ship.shipStatusWindow;
		}

		shipwindow.find(".value.DEW").html(dew);
		shipwindow.find(".value.CCEW").html(ccew);

		var ccewElement = shipwindow.find(".value.CCEW").parent();
		if (ccew === 0) {
			ccewElement.data("ship", ship).data("EW", "CCEW");
		} else {
			ccewElement.data("ship", ship).data("EW", ew.getCCEWentry(ship));
		}

		var BDEWcont = shipwindow.find(".ewentry.BDEW");
		if (elint) {
			BDEWcont.show();
			var bdewElement = BDEWcont.find(".value.BDEW");
			bdewElement.html(bdew);
			if (bdew === 0) {
				BDEWcont.data("ship", ship).data("EW", "BDEW");
			} else {
				BDEWcont.data("ship", ship).data("EW", ew.getBDEWentry(ship));
			}
		} else {
			BDEWcont.hide();
		}

		var template = $("#templatecontainer .ewentry");
		shipwindow.find(".ewentry.deletable").remove();

		for (var i in ship.EW) {
			var entry = ship.EW[i];
			if (entry.type != "OEW" && entry.type != "DIST" && entry.type != "SOEW" && entry.type != "SDEW" || entry.turn != gamedata.turn) continue;

			var element = template.clone(true).appendTo(shipwindow.find(".EW .EWcontainer"));

			element.data("EW", entry);
			element.data("ship", ship);
			element.find(".button1").on("click", ew.buttonDeassignEW);
			element.find(".button2").on("click", ew.buttonAssignEW);

			var h = entry.type + ' (<span class="shiplink">' + gamedata.getShip(entry.targetid).name + '</span>):';
			if (entry.type == "SOEW") {
				element.find(".button2").remove();
				element.find(".value").html(entry.amount);
			} else if (entry.type == "SDEW") {
				element.find(".value").html(entry.amount * 0.5);
			} else if (entry.type == "DIST") {
				element.find(".value").html(entry.amount / 3);
			} else if (entry.type == "OEW") {
				element.find(".value").html(entry.amount - ew.getDistruptionEW(ship));
			} else {
				element.find(".value").html(entry.amount);
			}

			element.find(".valueheader").html(h);
		}
	},

	addStructureSystem: function addStructureSystem(ship, system, destination) {
		var template = $("#systemtemplatecontainer .structure.system");
		var systemwindow = template.clone(true).appendTo(destination);
		systemwindow.addClass(system.name);

		systemwindow.addClass(system.name);
		systemwindow.addClass("system_" + system.id);
		systemwindow.data("shipid", ship.id);
		systemwindow.data("id", system.id);
	},

	addSystem: function addSystem(ship, system, destination) {

		if (!system) {
			return;
		}

		if (system.name == "structure") {
			shipWindowManager.addStructureSystem(ship, system, destination);
			return;
		}

		var template = $("#systemtemplatecontainer .system.regular");
		var systemwindow = template.clone(true).appendTo(destination);

		if (system.duoWeapon) {
			shipWindowManager.addDuoSystem(ship, system, systemwindow);
		} else if (system.dualWeapon) {
			shipWindowManager.addDualSystem(ship, system, systemwindow);
		} else {
			shipWindowManager.addRegularSystem(ship, system, systemwindow);
		}
	},

	addRegularSystem: function addRegularSystem(ship, system, systemwindow) {
		var icon = systemwindow.find(".systemcontainer").html("");
		icon.removeClass("duosystem");
		icon.removeClass("dualsystem");
		icon.addClass("regular");
		var icon_template = $("#systemtemplatecontainer .system.regular .icon");
		icon_template.clone(true).appendTo(icon);
		icon_template = $("#systemtemplatecontainer .system.regular .health.systembarcontainer");
		icon_template.clone(true).appendTo(icon);

		var iconplace = systemwindow.find(".icon");

		systemwindow.addClass(system.name);
		if (system.iconPath) {
			systemwindow.find(".icon").css("background-image", "url(./img/systemicons/" + system.iconPath + ")");
		} else {
			systemwindow.find(".icon").css("background-image", "url(./img/systemicons/" + system.name + ".png)");
		}

		systemwindow.addClass(system.name);
		systemwindow.addClass("system_" + system.id);
		systemwindow.data("shipid", ship.id);
		systemwindow.data("id", system.id);

		if (system.weapon) {
			systemwindow.addClass("weapon");
		}

		systemwindow.find(".off").off("click", shipManager.power.onOfflineClicked);
		systemwindow.find(".on").off("click", shipManager.power.onOnlineClicked);
		systemwindow.off("mouseover", weaponManager.onWeaponMouseover);
		systemwindow.off("mouseout", weaponManager.onWeaponMouseOut);
		systemwindow.off("click", shipWindowManager.clickSystem);
		systemwindow.off("contextmenu", shipWindowManager.selectAllGuns);

		systemwindow.find(".mode").off("click", shipWindowManager.onModeClicked);

		systemwindow.find(".off").on("click", shipManager.power.onOfflineClicked);
		systemwindow.find(".on").on("click", shipManager.power.onOnlineClicked);
		systemwindow.on("mouseover", weaponManager.onWeaponMouseover);
		systemwindow.on("mouseout", weaponManager.onWeaponMouseOut);

		systemwindow.on("click", shipWindowManager.clickSystem);
		systemwindow.on("contextmenu", shipWindowManager.selectAllGuns);

		systemwindow.find(".holdfire").on("click", window.weaponManager.onHoldfireClicked);

		systemwindow.find(".mode").on("click", shipWindowManager.onModeClicked);
	},

	addDualSystem: function addDualSystem(ship, system, dualwindow) {
		var dualsystem = system.weapons[system.firingMode];

		if (dualsystem.duoWeapon) {
			shipWindowManager.addDuoSystem(ship, dualsystem, dualwindow);
			dualwindow.find(".systemcontainer").addClass("dualsystem");
			dualwindow.find(".systemcontainer").addClass("duosystem");
			dualwindow.addClass("modes");
			dualwindow.addClass("parentsystem_" + dualsystem.parentId);
			dualwindow.addClass("weapon");

			if (dualsystem.parentId >= 0) {
				var parentSystem = shipManager.systems.getSystem(ship, dualsystem.parentId);

				if (parentSystem.parentId >= 0) {
					parentSystem = shipManager.systems.getSystem(ship, parentSystem.parentId);
					$(".parentsystem_" + parentSystem.id).addClass("modes");
					var modebutton = $(".mode", $(".parentsystem_" + parentSystem.id));
				} else {
					$(".parentsystem_" + parentSystem.id).addClass("modes");
					var modebutton = $(".mode", dualwindow);
				}

				modebutton.html("<span>" + parentSystem.firingModes[parentSystem.firingMode].substring(0, 1) + "</span>");
			}
		} else {
			shipWindowManager.addRegularSystem(ship, dualsystem, dualwindow);
			dualwindow.find(".systemcontainer").removeClass("duosystem");
			dualwindow.find(".systemcontainer").removeClass("regular");
			dualwindow.find(".systemcontainer").addClass("dualsystem");
			dualwindow.addClass("parentsystem_" + dualsystem.parentId);
			dualwindow.addClass("weapon");
		}
	},

	addDuoSystem: function addDuoSystem(ship, system, duowindow) {
		var icon = duowindow.find(".systemcontainer").html("");
		icon.addClass("duosystem");
		icon.addClass("dualsystem");
		duowindow.addClass("system_" + system.id);

		duowindow.data("shipid", ship.id);
		duowindow.data("id", system.id);

		icon.removeClass("regular");
		var icon_template = $("#systemtemplatecontainer .system.regular .icon");
		icon_template.clone(true).appendTo(icon);
		icon_template = $("#systemtemplatecontainer .system.regular .health.systembarcontainer");
		icon_template.clone(true).appendTo(icon);
		icon.find(".efficiency.value").remove();

		for (var i in system.weapons) {
			var weapon = system.weapons[i];
			var iconduo_template = $("#systemtemplatecontainer .iconduo");
			var iconduo_temp = iconduo_template.clone(true).appendTo(duowindow.find(".icon"));

			if (weapon.iconPath) {
				duowindow.find(".iconduo").css("background-image", "url(./img/systemicons/" + iconPath + ")");
			} else {
				duowindow.find(".iconduo").css("background-image", "url(./img/systemicons/" + weapon.name + "Duo.png)");
			}

			iconduo_temp.css("left", "" + (i - 1) * 16 + "px");

			iconduo_temp.addClass(weapon.name);
			iconduo_temp.addClass("system_" + weapon.id);
			iconduo_temp.data("shipid", ship.id);
			iconduo_temp.data("id", weapon.id);

			iconduo_temp.addClass("weapon");

			iconduo_temp.on("mouseover", weaponManager.onWeaponMouseoverDuoSystem);
			iconduo_temp.on("mouseout", weaponManager.onWeaponMouseOutDuoSystem);
			iconduo_temp.on("click", shipWindowManager.clickSystem);
		}

		// remove the iconmask because it's not needed (it will be added when
		// necessary.
		duowindow.find(".iconmask").remove();
		duowindow.off("mouseover", weaponManager.onWeaponMouseover);
		duowindow.off("mouseout", weaponManager.onWeaponMouseOut);
		duowindow.find(".off").off("click", shipManager.power.onOfflineClicked);
		duowindow.find(".on").off("click", shipManager.power.onOnlineClicked);

		duowindow.on("mouseover", weaponManager.onWeaponMouseover);
		duowindow.on("mouseout", weaponManager.onWeaponMouseOut);
		duowindow.find(".off").on("click", shipManager.power.onOfflineClicked);
		duowindow.find(".on").on("click", shipManager.power.onOnlineClicked);
		duowindow.find(".holdfire").on("click", window.weaponManager.onHoldfireClicked);
		$(".system .icon .UI .mode").on("click", shipWindowManager.onModeClicked);
	},

	removeSystemClasses: function removeSystemClasses(systemwindow) {
		var classes = Array("destroyed", "loading", "selected", "firing", "duofiring", "critical", "canoffline", "offline", "canboost", "boosted", "canoverload", "overload", "forcedoffline", "modes", "ballistic", "selfIntercept");

		for (var i in classes) {
			systemwindow.removeClass(classes[i]);

			var duowindows = $(systemwindow).find(".iconduo");
			if (duowindows.length > 0) {
				$(systemwindow).find(".iconduo").removeClass(classes[i]);
			}
		}
	},

	setSystemData: function setSystemData(ship, system, shipwindow) {
		this.updateNotes(ship);
		var parentWeapon = null;
		var parentWindow = null;

		if (system.parentId > 0) {
			parentWeapon = system;

			while (parentWeapon.parentId > 0) {
				parentWeapon = shipManager.systems.getSystem(ship, parentWeapon.parentId);
			}

			system.damage = parentWeapon.damage;

			parentWindow = shipwindow.find(".parentsystem_" + parentWeapon.id);
		}

		shipManager.systems.initializeSystem(system);

		if (system.dualWeapon && system.weapons != null) {
			var weapon = system.weapons[system.firingMode];
			shipManager.systems.initializeSystem(weapon);
		}

		var systemwindow = shipwindow.find(".system_" + system.id);

		if (systemwindow.length == 0 && system.parentId > -1) {
			systemwindow = shipwindow.find(".parentsystem_" + system.parentId);
		}

		var output = shipManager.systems.getOutput(ship, system);
		var field = systemwindow.find(".efficiency.value");

		if (system.name == "structure") {
			systemwindow.find(".healthvalue ").html(system.maxhealth - damageManager.getDamage(ship, system) + "/" + system.maxhealth + " A" + shipManager.systems.getArmour(ship, system));
		}

		if (system.parentId > 0) {
			parentWindow.find(".healthbar").css("width", (system.maxhealth - damageManager.getDamage(ship, system)) / system.maxhealth * 100 + "%");
		} else {
			systemwindow.find(".healthbar").css("width", (system.maxhealth - damageManager.getDamage(ship, system)) / system.maxhealth * 100 + "%");
		}

		if (system.name == "thruster") {
			systemwindow.data("direction", system.direction);
			systemwindow.find(".icon").css("background-image", "url(./img/systemicons/thruster" + system.direction + ".png)");
		}

		shipWindowManager.removeSystemClasses(systemwindow);

		if (shipManager.systems.isDestroyed(ship, system)) {
			if (system.parentId > 0) {
				if (shipManager.systems.getSystem(ship, system.parentId).duoWeapon) {
					// create an iconMask at the top of the DOM for the system.
					var iconmask_element = document.createElement('div');
					iconmask_element.className = "iconmask";
					parentWindow.find(".iconmask").remove();
					parentWindow.find(".icon").append(iconmask_element);
				}

				parentWindow.addClass("destroyed");
			} else {
				systemwindow.addClass("destroyed");
			}
			return;
		}

		if (shipManager.criticals.hasCriticals(system)) {
			if (system.parentId > 0) {
				parentWindow.addClass("critical");
			} else {
				systemwindow.addClass("critical");
			}
		}

		if (shipManager.power.setPowerClasses(ship, system, systemwindow)) return;

		if (system.weapon) {
			var firing = weaponManager.hasFiringOrder(ship, system);

			// To avoid double overlay of loading icon mask in case of a
			// duoWeapon in a dualWeapon
			if (!weaponManager.isLoaded(system) && !(system.duoWeapon && system.parentId > 0)) {
				systemwindow.addClass("loading");
			} else {
				systemwindow.removeClass("loading");
			}

			if (weaponManager.isSelectedWeapon(system)) {
				systemwindow.addClass("selected");
			} else {
				systemwindow.removeClass("selected");
			}

			if (firing && firing != "self" && !system.duoWeapon && !systemwindow.hasClass("loading")) {
				systemwindow.addClass("firing");

				if (system.parentId > -1) {
					var parentSystem = shipManager.systems.getSystem(ship, system.parentId);

					if (parentSystem.duoWeapon) {
						$(".system_" + system.parentId).addClass("duofiring");
					}
				}
			} else if (firing == "self") {
				systemwindow.addClass("firing");
				systemwindow.addClass("selfIntercept");
			} else {
				firing = false;
				systemwindow.removeClass("firing");
				systemwindow.removeClass("selfIntercept");
			}

			if (system.ballistic) {
				systemwindow.addClass("ballistic");
			} else {
				systemwindow.removeClass("ballistic");
			}

			if (!firing && (Object.keys(system.firingModes).length > 1 || system.dualWeapon)) {
				if (system.parentId >= 0) {
					var parentSystem = shipManager.systems.getSystem(ship, system.parentId);

					if (parentSystem.parentId >= 0) {
						parentSystem = shipManager.systems.getSystem(ship, parentSystem.parentId);
						$(".parentsystem_" + parentSystem.id).addClass("modes");
						var modebutton = $(".mode", $(".parentsystem_" + parentSystem.id));
					} else {
						$(".parentsystem_" + parentSystem.id).addClass("modes");
						var modebutton = $(".mode", systemwindow);
					}

					modebutton.html("<span>" + parentSystem.firingModes[parentSystem.firingMode].substring(0, 1) + "</span>");
				} else {
					systemwindow.addClass("modes");

					var modebutton = $(".mode", systemwindow);
					modebutton.html("<span>" + system.firingModes[system.firingMode].substring(0, 1) + "</span>");
				}
			}

			if (firing && system.canChangeShots) {
				var fire = weaponManager.getFiringOrder(ship, system);

				if (fire.shots < system.shots) {
					systemwindow.addClass("canAddShots");
				} else {
					systemwindow.removeClass("canAddShots");
				}

				if (fire.shots > 1) {
					systemwindow.addClass("canReduceShots");
				} else {
					systemwindow.removeClass("canReduceShots");
				}

				field.html(fire.shots + "/" + system.shots);
			} else if (!firing) {
				if (system.duoWeapon) {
					var UI_active = systemwindow.find(".UI").hasClass("active");

					shipWindowManager.addDuoSystem(ship, system, systemwindow);

					if (UI_active) {
						systemwindow.find(".UI").addClass("active");
					}
				} else {
					if (system.dualWeapon && system.weapons) {
						system = system.weapons[system.firingMode];
					}

					var load = weaponManager.getWeaponCurrentLoading(system);
					var loadingtime = system.loadingtime;

					if (system.normalload > 0) {
						loadingtime = system.normalload;
					}

					if (load > loadingtime) {
						load = loadingtime;
					}

					var overloadturns = "";

					if (system.overloadturns > 0 && shipManager.power.isOverloading(ship, system)) {
						overloadturns = "(" + system.overloadturns + ")";
					}

					if (system.overloadshots > 0) {
						field.html("S" + system.overloadshots);
					} else {
						field.html(load + overloadturns + "/" + loadingtime);
					}
				}
			}
		} else if (system.name == "thruster") {
			systemwindow.data("direction", system.direction);
			systemwindow.find(".icon").css("background-image", "url(./img/systemicons/thruster" + system.direction + ".png)");

			var channeled = shipManager.movement.getAmountChanneled(ship, system);
			if (channeled > output) {
				field.addClass("darkred");
			} else {
				field.removeClass("darkred");
			}

			if (channeled < 0) {
				channeled = 0;
			}

			field.html(channeled + "/" + output);
		} else if (system.name == "engine") {
			var rem = shipManager.movement.getRemainingEngineThrust(ship);

			field.html(rem + "/" + output);
		} else if (system.name == "reactor") {
			field.html(shipManager.power.getReactorPower(ship, system));
		} else if (system.output > 0) {
			field.html(output);
		}
	},

	assignThrust: function assignThrust(ship) {
		var movement = ship.movement[ship.movement.length - 1];

		if (movement.commit) return false;

		var requiredThrust = movement.requiredThrust;
		var stillReq = shipManager.movement.calculateThrustStillReq(ship, movement);
	
		var shipwindow = ship.shipStatusWindow;
	
		$(".thruster", shipwindow).each(function () {
			var direction = $(this).data("direction");

			if (requiredThrust[direction] != null) {
				$(this).addClass("enableAssignThrust");
			}
			if (stillReq[direction] == null) {
				$(this).removeClass("enableAssignThrust");
			}
		});

		shipwindow.addClass("assignThrust");
		window.webglScene.customEvent("AssignThrust", {ship: ship, totalRequired: requiredThrust, remainginRequired: stillReq, movement: movement})
	},

	selectAllGuns: function selectAllGuns(e) {
		e.stopPropagation();
		e.preventDefault();

		var shipwindow = $(".shipwindow").has($(this));
		var systemwindow = $(this);
		var ship = gamedata.getShip(shipwindow.data("ship"));
		var system = shipManager.systems.getSystem(ship, systemwindow.data("id"));

		weaponManager.selectAllWeapons(ship, system);
	},

	clickSystem: function clickSystem(e) {

		//TODO: Move to phase strategy
		e.stopPropagation();
		var shipwindow = $(".shipwindow").has($(this));
		var systemwindow = $(this);
		var ship = gamedata.getShip(shipwindow.data("ship"));
		var system = shipManager.systems.getSystem(ship, systemwindow.data("id"));
		system = shipManager.systems.initializeSystem(system);

		if (gamedata.waiting) return;

		if (shipManager.isDestroyed(ship) || shipManager.isDestroyed(ship, system) /*|| shipManager.isAdrift(ship)*/) return;//should work with disabled ship after all!

		if (system.weapon) {

			if (gamedata.gamephase != 3 && !system.ballistic) return;

			if (gamedata.gamephase != 1 && system.ballistic) return;

			if (gamedata.isMyShip(ship)) {
				if (weaponManager.isSelectedWeapon(system)) {
					weaponManager.unSelectWeapon(ship, system);
				} else {
					weaponManager.selectWeapon(ship, system);
				}
			}
		}

		webglScene.customEvent('SystemTargeted', { ship: ship, system: system });
	},

	clickPlus: function clickPlus(e) {
		e.stopPropagation();
		var shipwindow = $(".shipwindow").has($(this));
		var systemwindow = $(".system").has($(this));
		var ship = gamedata.getShip(shipwindow.data("ship"));
		var system = ship.systems[systemwindow.data("id")];

		if (shipManager.isDestroyed(ship) || shipManager.isDestroyed(ship, system)) return;

		if (ship.userid != gamedata.thisplayer) return;

		if (gamedata.gamephase == 2 && shipwindow.hasClass("assignThrust") && system.name == "thruster") {
			shipManager.movement.assignThrust(ship, system);
			shipWindowManager.assignThrust(ship);
		}

		if (system.weapon && system.canChangeShots && (system.ballistic && gamedata.gamephase == 1 || !system.ballistic && gamedata.gamephase == 3)) {
			weaponManager.changeShots(ship, system, 1);
		} else if (gamedata.gamephase == 1) {
			shipManager.power.clickPlus(ship, system);
		}
	},

	clickMinus: function clickMinus(e) {
		e.stopPropagation();
		var shipwindow = $(".shipwindow").has($(this));
		var systemwindow = $(".system").has($(this));
		var ship = gamedata.getShip(shipwindow.data("ship"));
		var system = ship.systems[systemwindow.data("id")];

		if (shipManager.isDestroyed(ship) || shipManager.isDestroyed(ship, system)) return;

		if (ship.userid != gamedata.thisplayer) return;

		if (gamedata.gamephase == 2 && shipwindow.hasClass("assignThrust") && system.name == "thruster") {
			shipManager.movement.unAssignThrust(ship, system);
			shipWindowManager.assignThrust(ship);
		}

		if (system.weapon && system.canChangeShots && (system.ballistic && gamedata.gamephase == 1 || !system.ballistic && gamedata.gamephase == 3)) {
			weaponManager.changeShots(ship, system, -1);
		} else if (gamedata.gamephase == 1) {
			shipManager.power.clickMinus(ship, system);
		}
	},

	doneAssignThrust: function doneAssignThrust(ship) {

		var shipwindow;
		if (! ship){
			shipwindow = $(".assignthrustcontainer").has($(this));
			ship = gamedata.getShip(shipwindow.data("ship"));
		} else {
			shipwindow = ship.shipStatusWindow
		}
		
		var movement = ship.movement[ship.movement.length - 1];;
		var requiredThrust = movement.requiredThrust;
		var stillReg = shipManager.movement.calculateThrustStillReq(ship, movement);

		var done = true;
		for (var i in stillReg) {

			if (stillReg[i] > 0) done = false;
		}

		if (done) {
			movement.commit = true;
			$(".assignThrust").removeClass("assignThrust");
			$(".enableAssignThrust").removeClass("enableAssignThrust");
			shipWindowManager.setData(ship);
			webglScene.customEvent("ShipMovementChanged", { ship: ship });
			window.webglScene.customEvent("AssignThrust", false)
		}
	},

	cancelAssignThrustEvent: function cancelAssignThrustEvent(ship) {

		var shipwindow;
		if (! ship){
			shipwindow = $(".assignthrustcontainer").has($(this));
			ship = gamedata.getShip(shipwindow.data("ship"));
		} else {
			shipwindow = ship.shipStatusWindow
		}

		var e = $(".shipwindow").has($(this));

		if (!e.length) {
			e = $(".assignthrustcontainer.assignThrust");
		}

		shipWindowManager.cancelAssignThrust(ship);
		webglScene.customEvent("ShipMovementChanged", { ship: ship });
	},

	cancelAssignThrust: function cancelAssignThrust(ship) {
		if (!ship) {
			throw new Error("This requires ship")
		}

		var element = ship.shipStatusWindow
	
		$(".assignThrust").removeClass("assignThrust");
		$(".enableAssignThrust").removeClass("enableAssignThrust");


		shipManager.movement.revertAutoThrust(ship);

		ship.movement.splice(ship.movement.length - 1, 1);

		shipWindowManager.setData(ship);
		window.webglScene.customEvent("AssignThrust", false)
	},

	onModeClicked: function onModeClicked(e) {
		e.stopPropagation();

		var shipwindow = $(".shipwindow").has($(this));
		var systemwindow = $(".system").has($(this));

		if (shipwindow.length == 0 || systemwindow.length == 0) {
			// I have no idea why a mode switch should generate so many
			// events. Just let the ones through that seem to make sense.
			// by Jazz
			return;
		}

		var ship = gamedata.getShip(shipwindow.data("ship"));
		var system = shipManager.systems.getSystem(ship, systemwindow.data("id"));

		window.weaponManager.onModeClicked(shipwindow, systemwindow, ship, system);
	},

	createAdaptiveArmourGUI: function createAdaptiveArmourGUI(ship) {

		var damageTypes = [];
		var damageValues = [];

		for (var key in ship.armourSettings) {
			if (ship.armourSettings[key][0] > 0) {
				damageTypes.push(key);
				damageValues.push(ship.armourSettings[key]);
			}
		}

		var div = document.createElement("div");
		div.id = "outerArmourDiv" + ship.id;

		var table = document.createElement("table");
		table.className = "armourTable";

		var tr = document.createElement("tr");
		tr.style.border = "1px solid #496791";
		var th = document.createElement("th");
		th.innerHTML = "Type";
		tr.appendChild(th);

		var th = document.createElement("th");
		th.innerHTML = "Type Maximum";
		tr.appendChild(th);

		var th = document.createElement("th");
		th.innerHTML = "Allocated";
		tr.appendChild(th);

		table.appendChild(tr);

		for (var i = 0; i < damageValues.length; i++) {

			var tr = document.createElement("tr");

			var td = document.createElement("td");
			td.innerHTML = damageTypes[i];
			td.className = "damageTypes";
			tr.appendChild(td);

			var td = document.createElement("td");
			td.innerHTML = damageValues[i][0];
			td.id = damageTypes[i].toLowerCase() + "Max" + ship.id;
			tr.appendChild(td);

			var td = document.createElement("td");
			td.innerHTML = damageValues[i][1];
			td.id = damageTypes[i].toLowerCase() + "Current" + ship.id;
			tr.appendChild(td);

			var td = document.createElement("td");
			var span = document.createElement("span");
			span.id = damageTypes[i].toLowerCase();
			$(span).data("max", ship.adaptiveArmourLimits[1]);
			$(span).data("shipid", ship.id);
			span.className = "armourPlusButton";

			span.onclick = function () {
				var globalAvail = Math.floor(document.getElementById("available" + $(this).data("shipid")).innerHTML);
				var globalAlloc = Math.floor(document.getElementById("allocated" + $(this).data("shipid")).innerHTML);

				var localAvail = Math.floor(document.getElementById(this.id + "Max" + $(this).data("shipid")).innerHTML);
				var localAlloc = Math.floor(document.getElementById(this.id + "Current" + $(this).data("shipid")).innerHTML);

				if (globalAlloc < globalAvail) {
					if (localAlloc < localAvail) {
						if (localAlloc < $(this).data("max")) {
							document.getElementById(this.id + "Current" + $(this).data("shipid")).innerHTML = localAlloc + 1;
							document.getElementById("allocated" + $(this).data("shipid")).innerHTML = globalAlloc + 1;
						}
					}
				}
			};

			td.appendChild(span);

			tr.appendChild(td);

			var td = document.createElement("td");

			var span = document.createElement("span");
			span.id = damageTypes[i].toLowerCase();
			span.className = "armourMinusButton";
			$(span).data("min", damageValues[i][1]);
			$(span).data("shipid", ship.id);

			span.onclick = function () {
				var globalAvail = Math.floor(document.getElementById("available" + $(this).data("shipid")).innerHTML);
				var globalAlloc = Math.floor(document.getElementById("allocated" + $(this).data("shipid")).innerHTML);

				var localAvail = Math.floor(document.getElementById(this.id + "Max" + $(this).data("shipid")).innerHTML);
				var localAlloc = Math.floor(document.getElementById(this.id + "Current" + $(this).data("shipid")).innerHTML);

				if (localAlloc > 0 && localAlloc > $(this).data("min")) {
					document.getElementById(this.id + "Current" + $(this).data("shipid")).innerHTML = localAlloc - 1;
					document.getElementById("allocated" + $(this).data("shipid")).innerHTML = globalAlloc - 1;
				}
			};

			td.appendChild(span);

			tr.appendChild(td);

			table.appendChild(tr);
		}

		var tr = document.createElement("tr");
		tr.style.color = "red";

		var th = document.createElement("th");
		th.innerHTML = "Total Maximum";
		tr.appendChild(th);

		var th = document.createElement("th");
		th.innerHTML = ship.adaptiveArmourLimits[0];
		th.id = "available" + ship.id;
		tr.appendChild(th);

		var th = document.createElement("th");

		var alloc = 0;
		for (var i in ship.armourSettings) {
			if (ship.armourSettings[i][1] > 0) {
				alloc += ship.armourSettings[i][1];
			}
		}

		th.innerHTML = alloc;
		th.id = "allocated" + ship.id;
		tr.appendChild(th);

		var th = document.createElement("th");
		th.colSpan = "2";

		var input = document.createElement("button");
		input.innerHTML = "confirm";
		input.className = "interceptButton";
		input.style.margin = "auto";

		$(input).data("shipid", ship.id);

		input.onclick = function () {

			var ret = {};

			var subDiv = document.getElementById("outerArmourDiv" + $(this).data("shipid"));
			var types = subDiv.getElementsByClassName("damageTypes");

			for (var i = 0; i < types.length; i++) {
				var string = types[i].innerHTML.toLowerCase();
				var value = document.getElementById(string + "Current" + $(this).data("shipid")).innerHTML;

				if (!value) {
					return;
				}

				if (value == 0) {
					ret[string] = 0;
					continue;
				}

				while (value > 0) {
					if (ret.hasOwnProperty(string)) {
						ret[string] += 1;
						value--;
					} else {
						ret[string] = 1;
						value--;
					}
				}
			}

			var ship = gamedata.getShip($(this).data("shipid"));

			for (var a in ship.armourSettings) {
				for (var b in ret) {
					if (a == b) {
						if (ret[b] > 0 && ret[b] > ship.armourSettings[a][1]) {
							ship.armourSettings[a][1] += ret[b];
						} else {
							ship.armourSettings[a][1] = ret[b];
						}
					}
				}
			}
			shipWindowManager.setData(ship);
			document.getElementById("outerArmourDiv" + ship.id).style.display = "none";

			console.log(ship.armourSettings);
		};

		th.appendChild(input);

		tr.appendChild(th);

		table.appendChild(tr);

		div.appendChild(table);

		var name = "shipwindow ship ui-draggable owned ship_" + ship.id;

		var goal = document.getElementsByClassName(name);

		goal[0].appendChild(div);
	}
};