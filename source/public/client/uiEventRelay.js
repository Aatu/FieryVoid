"use strict";

/* Ship-window redesign Stage 2a (SHIPWINDOW_REDESIGN_PLAN.md §4.1).
 *
 * Page-agnostic relay between the React UI (ship windows, system icons) and
 * whatever the hosting page uses to consume UI events. React components call
 * window.uiEvents.relay(name, payload) instead of webglScene.customEvent
 * directly, so the same components can run on pages without a WebGL map.
 *
 * - game.php: webglScene.js installs a handler that funnels every event INTO
 *   webglScene.customEvent, so the PhaseDirector relay chain and the
 *   render-request (idle render-loop gating) behave exactly as before.
 * - gamelobby.php (Stage 3): installs its own small handler mapping the few
 *   events the lobby cares about (SystemMouseOver/SystemMouseOut/
 *   CloseShipWindow) and ignoring the rest.
 *
 * Events relayed before a handler is installed are dropped, mirroring
 * webglScene.customEvent's own "not initialized yet" guard.
 */
window.uiEvents = {
	handler: null,

	setHandler: function setHandler(handler) {
		this.handler = handler;
	},

	relay: function relay(name, payload) {
		if (this.handler) {
			this.handler(name, payload);
		}
	}
};
