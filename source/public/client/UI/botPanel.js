"use strict";

/* The bottom log panel's shell: tab switching, the expand toggle, and the drag-resize
   grip (LOG_PANEL_REDESIGN_PLAN.md Stage 1). Everything the panel LOOKS like is in
   styles/logPanel.css.

   The tab strip is a flex row now, so a new tab costs nothing but a <div> - no `left:`
   in the base sheet and none in either mobile media query. */

/* Two independent heights, because collapsed and expanded are two different working
   sizes and a player who drags one has not asked to move the other.

   ⭐ THE `.v2` SUFFIX IS LOAD-BEARING (user, 2026-08-31). A remembered height is an
   INLINE style written onto #logcontainer at DOMContentLoaded, so it beats the stylesheet
   defaults every time - which meant raising --fv-log-h / --fv-log-h-large in logPanel.css
   appeared to do nothing: the panel painted at the new height and then visibly shrank back
   to the remembered one a moment later, animated by #logBody's height transition.

   That would have been fair enough for a size a player had chosen on purpose. It was not:
   the grip used to persist on EVERY pointerup, drag or no drag (see initResizeGrip), so a
   single stray click on the panel's 5px top edge - or the double-click that toggles the
   panel open - silently pinned the height. Almost every remembered value out there was
   written by accident.

   So: the saving bug is fixed below, and the keys are renamed once to drop the values it
   wrote. The old pair is deleted on restore rather than read, so nobody carries an
   accidental height forward. DO NOT bump this again just because a default changes - a
   deliberately dragged height is the player's, and it is meant to win. */
var LOG_PANEL_H_KEY = "fv.logPanel.height.v2";
var LOG_PANEL_H_LARGE_KEY = "fv.logPanel.heightLarge.v2";
var LOG_PANEL_H_KEYS_RETIRED = ["fv.logPanel.height", "fv.logPanel.heightLarge"];

jQuery(function () {
	/* Scoped to .logUiEntry: #expandBotPanel is a #logUI div too, and under the old
	   "#logUI div" binding it was picking up `selected` and then calling
	   $(undefined).show() before its own handler ran. */
	jQuery("#logUI").on("click", ".logUiEntry", window.botPanel.onLogUIClicked);

	$("#expandBotPanel").on("click", function () {
		window.botPanel.toggleExpanded();
	}).on("keydown", function (e) {
		if (e.key === "Enter" || e.key === " " || e.key === "Spacebar") {
			e.preventDefault();
			window.botPanel.toggleExpanded();
		}
	});

	window.botPanel.restoreHeights();
	window.botPanel.initResizeGrip();
	window.botPanel.initContextMenuSuppression();
});

window.botPanel = {

	updateCallback: null,

	onLogUIClicked: function onLogUIClicked(e) {

		var e = $(this);
		$(".logUiEntry").removeClass("selected");
		e.addClass("selected");

		$(".logPanelEntry").hide();

		var select = e.data("select");
		var e = $(select);
		e.show();
		e.trigger("onshow");
	},

	/* Expand / collapse. Unchanged in behaviour from the old #expandBotPanel handler -
	   the initiative drawer still gets out of the way - but the button now reports its
	   state through aria-expanded and a chevron instead of the literal word "Click!". */
	toggleExpanded: function toggleExpanded() {
		var logContainer = $("#logcontainer");

		logContainer.toggleClass('large');
		var large = logContainer.hasClass('large');

		$("#expandBotPanel")
			.attr("aria-expanded", large ? "true" : "false")
			.attr("title", large ? "Collapse log panel" : "Expand log panel")
			.find(".chevron").html(large ? "&#9660;" : "&#9650;");

		// Hide iniGui if logcontainer is large
		if (large) {
			$(iniGui).addClass("closed");
			$(backDiv).addClass("closed");
			$(backDiv).data("on", 0);
			document.getElementById("iniSlider").src = "img/pullOut.png";
		} else {
			// If not large, ensure it behaves normally
			$(iniGui).removeClass("closed");
			$(backDiv).removeClass("closed");
			$(backDiv).data("on", 1);

			// Clean up any potential lingering inline styles
			$("#iniGui").css("display", "");
			$("#backDiv").css("margin-left", "");

			document.getElementById("iniSlider").src = "img/pullIn.png";
		}

		/* THE OPEN TAB IS NOT DISTURBED (user, 2026-08-31). This used to force the panel
		   back to #log on every expand AND every collapse, by calling onLogUIClicked with
		   a hand-made element carrying data-select="#log" - so a player reading the fleet
		   list who opened the panel to see more of it was thrown into the combat log. The
		   selected tab and its panel are already correct; there is nothing to do here. */
	},

	/* ── Panel height ────────────────────────────────────────────────────────────
	   The height is a custom property on #logcontainer rather than an inline height on
	   #logBody, so the mobile media queries can still take it back with a plain
	   `height:` of their own (a declaration always beats a var()-valued one at equal
	   specificity, whatever the variable holds). */
	heightVar: function heightVar() {
		return $("#logcontainer").hasClass("large") ? "--fv-log-h-large" : "--fv-log-h";
	},

	storageKey: function storageKey() {
		return $("#logcontainer").hasClass("large") ? LOG_PANEL_H_LARGE_KEY : LOG_PANEL_H_KEY;
	},

	restoreHeights: function restoreHeights() {
		var el = document.getElementById("logcontainer");
		if (!el) return;
		//localStorage throws in a few privacy configurations; a missing remembered height
		//is not worth taking the panel down for.
		try {
			//The keys the pre-fix grip wrote by accident. Dropped, never read - see the
			//note on LOG_PANEL_H_KEY.
			for (var r = 0; r < LOG_PANEL_H_KEYS_RETIRED.length; r++) {
				window.localStorage.removeItem(LOG_PANEL_H_KEYS_RETIRED[r]);
			}

			botPanel.applyStoredHeight(el, LOG_PANEL_H_KEY, "--fv-log-h");
			botPanel.applyStoredHeight(el, LOG_PANEL_H_LARGE_KEY, "--fv-log-h-large");
		} catch (ex) { /* no remembered height - the stylesheet defaults stand */ }
	},

	/* A stored height only wins if it is a height. parseInt("", 10) is NaN and
	   parseInt("0px") is 0, either of which would have set the panel to `NaNpx` (ignored,
	   harmlessly) or to nothing at all (not harmless) - and the range is the same one the
	   grip clamps a live drag to, so a value saved on a taller screen cannot bury this
	   one's map. Out of range, it is discarded rather than clamped: the stylesheet default
	   is a better guess for THIS screen than an arbitrary edge of the range. */
	applyStoredHeight: function applyStoredHeight(el, key, prop) {
		var raw = window.localStorage.getItem(key);
		if (!raw) return;
		var h = parseInt(raw, 10);
		if (!isFinite(h) || h < 60 || h > Math.max(120, window.innerHeight)) return;
		el.style.setProperty(prop, h + "px");
	},

	/* The panel is NOT a scaled window, so unlike the ship window's grip nothing here
	   divides by a scale factor - see arch_scaled_window_coordinate_spaces. Pointer
	   Events, not mouse events, so a touch drag works without a second code path. */
	initResizeGrip: function initResizeGrip() {
		var grip = document.getElementById("logResizeGrip");
		var body = document.getElementById("logBody");
		var container = document.getElementById("logcontainer");
		if (!grip || !body || !container) return;

		var startY = 0;
		var startH = 0;
		var dragging = false;
		/* ⭐ A CLICK IS NOT A DRAG (user, 2026-08-31). This used to persist on EVERY
		   pointerup, so a stray click on the 5px top edge - or either half of the
		   double-click that toggles the panel open - wrote the CURRENT height to
		   localStorage and pinned it there for good. From then on the panel ignored
		   --fv-log-h / --fv-log-h-large entirely: it painted at the stylesheet height and
		   then shrank back to the accidental one as soon as restoreHeights ran, which is
		   exactly the "my height change did not take" bug. Only a pointer that actually
		   moved is a size the player chose. */
		var moved = false;

		grip.addEventListener("pointerdown", function (e) {
			dragging = true;
			moved = false;
			startY = e.clientY;
			startH = body.getBoundingClientRect().height;
			grip.setPointerCapture(e.pointerId);
			container.classList.add("resizing");
			e.preventDefault();
		});

		grip.addEventListener("pointermove", function (e) {
			if (!dragging) return;
			//3px of slop, so the shake a finger or a stiff mouse button puts into a click
			//does not count as a resize either.
			if (Math.abs(e.clientY - startY) < 3) return;
			moved = true;
			//The panel is anchored to the BOTTOM of the screen, so dragging the grip up
			//(a smaller clientY) makes it taller.
			var h = Math.round(startH + (startY - e.clientY));
			var max = Math.max(120, window.innerHeight - 90);
			h = Math.min(max, Math.max(60, h));
			container.style.setProperty(botPanel.heightVar(), h + "px");
		});

		var end = function (e) {
			if (!dragging) return;
			dragging = false;
			container.classList.remove("resizing");
			try { grip.releasePointerCapture(e.pointerId); } catch (ex) { }
			if (!moved) return;
			moved = false;
			try {
				window.localStorage.setItem(botPanel.storageKey(),
					String(Math.round(body.getBoundingClientRect().height)));
			} catch (ex) { /* nothing to do - the size still applies for this session */ }
		};

		grip.addEventListener("pointerup", end);
		grip.addEventListener("pointercancel", end);

		//Double-click is the shortcut for the same thing the chevron does.
		grip.addEventListener("dblclick", function (e) {
			e.preventDefault();
			botPanel.toggleExpanded();
		});
	},

	/* ── The browser context menu ────────────────────────────────────────────────
	   SUPPRESSED ACROSS THE WHOLE PANEL (user, 2026-08-31). The log panel is 800px of
	   chrome pinned to the bottom-left corner, directly under where a player's cursor
	   sits while working the map, so a right-click meant for a unit that lands an inch
	   low puts Chrome's Back / Reload / Save-as menu over the game.

	   IT DOES NOT TOUCH THE GAME'S OWN RIGHT-CLICKS. Every in-panel gesture is a
	   DELEGATED handler bound closer to the target - fleetListManager.initRowInteractions
	   binds `contextmenu` on #fleetListBody, so a right-click on a fleet-list row reaches
	   that handler and opens the ship window BEFORE the event bubbles this far. This one
	   only ever runs on what nothing else claimed, and preventDefault() is idempotent, so
	   the order of the two costs nothing either way.

	   TWO CARVE-OUTS, both "the menu has something real to do here":
	     - a form field: the chat composer and the combat log's Find box, where the menu
	       is how you paste;
	     - a live text selection inside the panel: right-click -> Copy on a log line is how
	       a player quotes a turn into Discord, and taking that away would be a regression
	       rather than the fix that was asked for. An accidental right-click has no
	       selection under it, so it is still suppressed.

	   Native listener, not jQuery: nothing here needs delegation, and it must not be
	   removable by a `$("#logcontainer").off()` anywhere else. */
	initContextMenuSuppression: function initContextMenuSuppression() {
		var panel = document.getElementById("logcontainer");
		if (!panel) return;

		panel.addEventListener("contextmenu", function (e) {
			var target = e.target;

			//A field the menu can paste into. isContentEditable covers a rich composer if
			//one is ever added; closest() covers the case where the click lands on a child
			//of the field rather than the field itself.
			if (target && target.nodeType === 1) {
				if (target.isContentEditable) return;
				if (target.closest && target.closest("input, textarea, select")) return;
			}

			//Something selected, inside this panel. contains() accepts the text node a
			//range normally ends up on, so there is nothing to normalise here.
			try {
				var sel = window.getSelection && window.getSelection();
				if (sel && !sel.isCollapsed && sel.rangeCount &&
					panel.contains(sel.getRangeAt(0).commonAncestorContainer)) return;
			} catch (ex) { /* no Selection API - fall through and suppress */ }

			e.preventDefault();
		});
	},

	/* The panel readout ("TURN 7 · FIRING", "4 FLEETS · 12 UNITS"). It lives at the
	   right-hand end of each panel's control bar - the per-panel head bars that used to
	   carry it are gone (user, 2026-08-31): the tab above the panel already names it, and
	   at the default height a head bar plus a control bar was a third of the body. */
	setMeta: function setMeta(key, text) {
		var nodes = document.querySelectorAll('[data-log-meta="' + key + '"]');
		for (var i = 0; i < nodes.length; i++) {
			nodes[i].textContent = text || "";
		}
	},

	onShipStatusChanged: function onShipStatusChanged(ship) {
		if (botPanel.updateCallback) {
			botPanel.updateCallback(ship);
		}
	},

	deactivate: function deactivate() {
		botPanel.updateCallback = null;
	},
};
