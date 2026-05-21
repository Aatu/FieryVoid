"use strict";

window.systemInfo = {
			//THIS FILE IS USED ONLY IN FLEET SELECTION - in game, see SystemInfo.js in reactJs instead

	showSystemInfo: function showSystemInfo(element, system, ship, selectedShip) {
		system = shipManager.systems.initializeSystem(system);

		$('.UI', element).addClass("active");
		var w = $("#systemInfo");
		var offs = element.offset();

		w.css("left", offs.left + "px");
		w.css("top", offs.top + 35 + "px");

		$("span.name", w).html(system.displayName.toUpperCase());

		var h = "";
		if (!ship.flight) {
			h += '<div><span class="header">Structure:</span><span class="value">' + (system.maxhealth - damageManager.getDamage(ship, system)) + '/' + system.maxhealth + "</span></div>";
			h += '<div><span class="header">Armor:</span><span class="value">' + shipManager.systems.getArmour(ship, system) + "</span></div>";
		} else if (system.weapon) {
			h += '<div><span class="header">Offensive bonus:</span><span class="value">' + ship.offensivebonus * 5 + "</span></div>";
		}

		if (system.firingModes) h += '<div><span class="header">Firing mode:</span><span class="value">' + system.firingModes[system.firingMode] + "</span></div>";

		if (!mathlib.arrayIsEmpty(system.missileArray)) {
			h += '<div><span class="header">Ammo Amount:</span><span class="value">' + system.missileArray[system.firingMode].amount + "</span></div>";
		}

		for (var i in system.data) {
			h += '<div><span class="header">' + i + ':</span><span class="value">' + system.data[i] + "</span></div>";
		}

		//Leftover hangar capacity is auto-filled with shuttles (or minesweeping
		//shuttles / Flyers per faction) — same rule as shipwindow.js. The pool is
		//ship-wide, so show it on a single Hangar tooltip (primary section, else
		//first hangar) rather than repeating the total on every hangar. The
		//breakdown reflects the HANG_BP / HANG_MSW shuttle-slot enhancements.
		if (system.name == "hangar") {
			var defaultHangar = shipManager.systems.getDefaultShuttleHangar(ship);
			if (defaultHangar && defaultHangar.id == system.id) {
				var shuttleRows = shipManager.systems.getDefaultShuttleComposition(ship);
				for (var s = 0; s < shuttleRows.length; s++) {
					//slotOnly rows (HANG_BP) are converted-but-empty capacity, not
					//units present in the hangar — don't list them here.
					if (shuttleRows[s].slotOnly) continue;
					h += '<div><span class="header">' + shuttleRows[s].type + ':</span><span class="value">' + shuttleRows[s].count + "</span></div>";
				}
			}
		}

		if (Object.keys(system.critData).length > 0) {
			var currentCrits = shipManager.criticals.getAllCriticals(system,gamedata.turn); //system.criticals may contain crits that are not current
			h += "<div><span>DAMAGE:</span></div><ul>";
			for (var i in system.critData) {
				var noOfCrits = 0;
				//for (var j in system.criticals) {			
				//	if (system.criticals[j].phpclass == i) noOfCrits++;
				for (var j in currentCrits) {					
					if (currentCrits[j].phpclass == i) noOfCrits++;
				}
				
				if (noOfCrits > 1) {
					//display multiplier too
					h += "<li class='crit'>(" + noOfCrits + "x) " + system.critData[i] + "</li>";
				} else if (noOfCrits == 1) {
					//just crit name
					h += "<li class='crit'>" + system.critData[i] + "</li>";
				}
			}
			h += "</ul>";
		}

		$(".datacontainer", w).html(h);

		if (!gamedata.isMyShip(ship) && gamedata.gamephase == 3 && gamedata.waiting == false && gamedata.selectedSystems.length > 0 && selectedShip) {
			if (weaponManager.canCalledshot(ship, system, selectedShip)) {

				var e = $('<div class="calledtargeting"><span>CALLED SHOT</span></div><div class="targeting"></div>');
				var datac = $(".datacontainer", w);
				datac.append(e);
				weaponManager.targetingShipTooltip(selectedShip, ship, datac, system.id);
			} else {
				e = $('<div class="calledtargeting"><span>CANNOT TARGET</span>');
				$(".datacontainer", w).append(e);
			}
		}

		w.show();
	},

	hideSystemInfo: function hideSystemInfo() {
		$("#systemInfo").hide();
		$('.UI').removeClass("active");
	}
};