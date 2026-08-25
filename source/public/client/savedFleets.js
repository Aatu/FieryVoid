"use strict";

/*==========================================================================
  Save Current Fleet — PREBATTLE_DAMAGE_PLAN.md Part 2 (§7).

  The SAVE half of the saved-fleet UI, extracted so BOTH gamelobby.php and
  game.php can drive it. The load/dropdown/delete UI deliberately stays in
  gamelobby.js, where its cachedFleets / fleetDropdownList / fleetDropdownButton
  DOM closures live — game.php never loads a fleet, only saves one.

  The plumbing underneath (ajaxInterface.constructSavedShips / submitSavedFleet,
  UI/confirm.js) was already loaded on both pages, so this file is thin.
  ==========================================================================*/

window.savedFleets = {

	/* Units the current save would write: the viewer's own SURVIVING ships - those on the
	   board AND those stowed in a hangar - minus the mid-battle artefacts a fleet list has no
	   use for. Mirrors the filter constructSavedShips itself applies, so the summary line
	   cannot drift from it. */
	saveableShips: function saveableShips() {
		var ships = [];
		for (var i in gamedata.ships) {
			if (ajaxInterface.isSaveableFleetShip(gamedata.ships[i])) ships.push(gamedata.ships[i]);
		}
		return ships;
	},

	/* {saveable, excluded, docked, present} — how many of the viewer's units will and will
	   not be written, how many of the written ones are sitting in a hangar, and whether they
	   have any units here at all.
	   `present` answers canSaveCurrentFleet's question in the SAME walk, because this runs
	   on every gamedata poll (see refreshSavePanel) and two passes over the ship list for
	   two closely-related counts is one pass too many. It counts phantom sheets, exactly as
	   canSaveCurrentFleet does; `saveable`/`excluded` do not.
	   `docked` is called out separately because those units are INVISIBLE on the board - the
	   whole reason they used to be missed - so the panel has to say they are in the count. */
	saveSummary: function saveSummary() {
		var saveable = 0, owned = 0, present = 0, docked = 0;
		if (!window.gamedata || !gamedata.ships) return { saveable: 0, excluded: 0, docked: 0, present: false };

		for (var i in gamedata.ships) {
			var ship = gamedata.ships[i];
			if (!ship || ship.userid !== gamedata.thisplayer) continue;
			present++;
			if (ship.id < 0) continue;   //Chameleon phantom sheet - not a unit the player owns
			owned++;
			if (!ajaxInterface.isSaveableFleetShip(ship)) continue;
			saveable++;
			if (ship.removed) docked++;
		}
		return { saveable: saveable, excluded: owned - saveable, docked: docked, present: present > 0 };
	},

	/* The viewer has units in this game at all? That, not the phase, is what gates the
	   SAVE FLEET panel — it reads no orders, so it is available in every phase and
	   deliberately in FINISHED games and replay too. Reloading from the end of the
	   previous battle is exactly the case this feature exists for. */
	canSaveCurrentFleet: function canSaveCurrentFleet() {
		if (!window.gamedata || !gamedata.ships) return false;
		for (var i in gamedata.ships) {
			var ship = gamedata.ships[i];
			if (ship && ship.userid === gamedata.thisplayer) return true;
		}
		return false;
	},

	/* Ask for a name, then write the fleet. Shared entry point for the lobby's Save
	   button and game.php's SAVE FLEET panel. */
	saveCurrentFleet: function saveCurrentFleet() {
		$(".confirm").remove();

		if (!savedFleets.canSaveCurrentFleet()) {
			window.confirm.fleetNotice("You have no units in this game to save.");
			return;
		}

		if (savedFleets.saveSummary().saveable === 0) {
			window.confirm.fleetNotice("None of your units survive to be saved.");
			return;
		}

		//Only a LIVE game can have produced a one-turn critical, so only game.php is asked
		//about them - in the lobby the question has no answer.
		window.confirm.showSaveFleet(savedFleets.doSaveCurrentFleet, {
			offerTransient: gamedata.gamephase !== -2
		});
	},

	/* Bound as the confirm's OK handler, so it reads its inputs BEFORE tearing the
	   dialog down (showSaveFleet does not remove it for us). */
	doSaveCurrentFleet: function doSaveCurrentFleet() {
		var fleetname = $(".confirm input[name='fleetname']").val();
		var isPublic = $("#fleetPublicCheckbox").is(":checked");
		//Absent in the lobby, where the box is not offered - .is(":checked") on an empty
		//set is false, which is the right default anyway.
		var includeTransient = $("#fleetTransientCritsCheckbox").is(":checked");

		$(".confirm").remove();

		ajaxInterface.submitSavedFleet(fleetname, isPublic, function (response) {
			//The lobby also keeps its dropdown in step; game.php has no dropdown.
			if (window.gamedata && typeof gamedata.refreshSavedFleets === 'function') {
				gamedata.refreshSavedFleets();
			}
			window.confirm.fleetNotice("<b>" + fleetname + "</b> saved as fleet <b>#"
				+ response.listId + "</b>.<br>Load it from any game lobby with that ID.");
		}, { includeTransient: includeTransient });
	},

	//Last state written to the DOM, so an unchanged poll costs nothing - see below.
	panelState: null,

	/* game.php's SAVE FLEET panel: tab visibility, button state and the "N units will be
	   saved" line. No-op in the lobby, which has no such panel.
	   ⚠️ Called from gamedata.parseServerData, i.e. on EVERY POLL - that is what keeps the
	   count honest as units die during the battle, but it also means everything here is on
	   the hottest client path in the game. The ship walk is one pass (saveSummary answers
	   both questions), and the four jQuery writes are skipped unless the numbers actually
	   moved, which for most polls they have not. */
	refreshSavePanel: function refreshSavePanel() {
		var tab = $("#fleetSaveTab");
		if (!tab.length) return;

		var summary = savedFleets.saveSummary();
		var hasFleet = summary.present;

		var state = hasFleet + "/" + summary.saveable + "/" + summary.excluded + "/" + summary.docked;
		if (state === savedFleets.panelState) return;
		savedFleets.panelState = state;

		//css(), not toggle(): jQuery's show() writes an INLINE display when a stylesheet
		//rule is hiding the element, and the mobile breakpoint hides every .logUiEntry
		//unless #logcontainer is .large - an inline display:block would pin this one tab
		//open in the collapsed state. Clearing the property lets the CSS decide.
		tab.css("display", hasFleet ? "" : "none");
		$("#fleetSaveButton").prop("disabled", !(hasFleet && summary.saveable > 0));

		var text;
		if (!hasFleet) {
			text = "You have no units in this game.";
		} else {
			text = summary.saveable + (summary.saveable === 1 ? " unit" : " units") + " will be saved";
			//Spelled out because a docked unit is nowhere to be seen on the board - without
			//this the count reads as too high and looks like a bug rather than the fix.
			if (summary.docked > 0) {
				text += " (including " + summary.docked
					+ (summary.docked === 1 ? " in a hangar" : " in hangars") + ")";
			}
			/*if (summary.excluded > 0) {
				text += "; " + summary.excluded + " destroyed or departed "
					+ (summary.excluded === 1 ? "unit is" : "units are") + " excluded";
			}*/
			text += ".";
		}
		$("#fleetSaveSummary").text(text);
	}
};

/* game.php bootstrap. The generic data-select handler in UI/botPanel.js already switches
   the panels and fires "onshow" on the one it reveals. Availability is gated on "the
   viewer has ships in this game", NOT on the phase: this panel reads no orders, so it is
   deliberately usable in every phase including FINISHED games and replay - reloading from
   the end of the previous battle is exactly what it is for. */
jQuery(function () {
	if (!$("#fleetsave").length) return;   //lobby, or any page without the panel

	$("#fleetSaveButton").on("click", function () {
		savedFleets.saveCurrentFleet();
	});

	$("#fleetsave").on("onshow", function () {
		savedFleets.refreshSavePanel();
	});

	savedFleets.refreshSavePanel();
});
