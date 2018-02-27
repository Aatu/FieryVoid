"use strict";

window.systemInfo = {

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
		} else {
			h += '<div><span class="header">Offensive bonus:</span><span class="value">' + ship.offensivebonus * 5 + "</span></div>";
		}

		if (system.firingModes) h += '<div><span class="header">Firing mode:</span><span class="value">' + system.firingModes[system.firingMode] + "</span></div>";

		if (!mathlib.arrayIsEmpty(system.missileArray)) {
			h += '<div><span class="header">Ammo Amount:</span><span class="value">' + system.missileArray[system.firingMode].amount + "</span></div>";
		}

		for (var i in system.data) {
			h += '<div><span class="header">' + i + ':</span><span class="value">' + system.data[i] + "</span></div>";
		}

		if (Object.keys(system.critData).length > 0) {
			h += "<div><span>DAMAGE:</span></div><ul>";

			for (var i in system.critData) {
				var noOfCrits = 0;
				for (var j in system.criticals) {
					if (system.criticals[j].phpclass == i) noOfCrits++;
				}
				if (noOfCrits > 1) {
					//display multiplier too
					h += "<li class='crit'>(" + noOfCrits + "x) " + system.critData[i] + "</li>";
				} else {
					//just crit name
					h += "<li class='crit'>" + system.critData[i] + "</li>";
				}
			}
			h += "</ul>";
		}

		$(".datacontainer", w).html(h);

		if (!gamedata.isMyShip(ship) && gamedata.gamephase == 3 && gamedata.waiting == false && gamedata.selectedSystems.length > 0 && selectedShip) {
			if (weaponManager.canCalledshot(ship, system, selectedShip)) {

				e = $('<div class="calledtargeting"><span>CALLED SHOT</span></div><div class="targeting"></div>');
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