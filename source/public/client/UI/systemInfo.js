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

		//Leftover hangar capacity is auto-filled with shuttles (or minesweeping
		//shuttles / Flyers per faction) — same rule as shipwindow.js. The pool is
		//ship-wide; per-hangar attribution is delegated to systems.js, mirroring
		//HangarOps::populateInitialHangarUsage: the pool splits evenly across the
		//primary-structure hangars (Pirocia's three → 2+2+2), or across all
		//hangars when none are primary (Marata's 6 leftover shuttles → 3+3).
		//The breakdown reflects the HANG_BP / HANG_MSW shuttle-slot enhancements.
		//Precompute it here so the Capacity line below can fold the auto-filled
		//shuttle count into its "stored" number (the blueprint Capacity reads
		//"0 / N" pre-game because hangarUsage is empty; combat fighters auto-deploy
		//to space so only the shuttles actually sit in the hangar in-game).
		var isDefaultShuttleHangar = false;
		var defaultShuttleRows = [];
		var defaultShuttleStored = 0;
		if (system.name == "hangar") {
			defaultShuttleRows = shipManager.systems.getDefaultShuttleCompositionForHangar(ship, system);
			isDefaultShuttleHangar = defaultShuttleRows.length > 0;
			if (isDefaultShuttleHangar) {
				for (var s = 0; s < defaultShuttleRows.length; s++) {
					//slotOnly rows (HANG_BP) are converted-but-empty capacity, not units present.
					if (defaultShuttleRows[s].slotOnly) continue;
					defaultShuttleStored += parseInt(defaultShuttleRows[s].count, 10) || 0;
				}
			}
		}

		for (var i in system.data) {
			var dataValue = system.data[i];
			//Fold the auto-filled default shuttles into the Capacity stored count
			//(default-shuttle hangar only) so the lobby previews the in-game state.
			if (isDefaultShuttleHangar && i == "Capacity" && defaultShuttleStored > 0) {
				dataValue = defaultShuttleStored + " / " + (parseInt(system.maxhealth, 10) || 0) + " slots";
			}
			h += '<div><span class="header">' + i + ':</span><span class="value">' + dataValue + "</span></div>";
		}

		if (isDefaultShuttleHangar) {
			for (var s2 = 0; s2 < defaultShuttleRows.length; s2++) {
				//slotOnly rows (HANG_BP) are converted-but-empty capacity, not
				//units present in the hangar — don't list them here.
				if (defaultShuttleRows[s2].slotOnly) continue;
				h += '<div><span class="header">' + defaultShuttleRows[s2].type + ':</span><span class="value">' + defaultShuttleRows[s2].count + "</span></div>";
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