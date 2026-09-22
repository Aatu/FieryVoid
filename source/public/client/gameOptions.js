"use strict";

/* THE OPTIONS TAB'S PLAYER PREFERENCES — game.php's bottom log panel.

   The tab used to be SAVE FLEET and hold exactly one thing. It is OPTIONS now, and Save
   Fleet is the section at the bottom of it; this file owns everything ABOVE that: a list
   of checkboxes whose state follows the player from game to game.

   ⭐ ADDING AN OPTION IS ONE ENTRY IN `OPTIONS` AND NOTHING ELSE. The row, its label, its
   default, its persistence and the read-back through gameOptions.get() all fall out of
   that entry - there is no second place to register it and no markup to add to game.php,
   which is the whole point of building a list rather than hand-writing the first checkbox.

   PER PLAYER, NOT PER GAME, and stored in localStorage exactly like the log panel's other
   preferences (botPanel's heights, fleetList's filters, combatLog's filters). Nothing here
   is an ORDER - it changes only what this browser draws - so none of it goes to the server.
   ⚠️ localStorage throws outright in a few privacy configurations, so every access is
   wrapped and a failure just means the defaults stand. */
window.gameOptions = (function () {

    var PREF_KEY = "fv.gameOptions.v1";

    /* ⚠️ THE RENDER IS DEFERRED, THE READ IS NOT. Icons are built inside webglScene.init,
       and a badge can be asked about before jQuery's ready callback has drawn a single
       row - so the values load lazily on the first get() rather than at render time. */
    var values = null;

    var OPTIONS = [
        {
            key: "showIniOverlay",
            label: "Show Initiative Overlay",
            title: "The movement-group number drawn over every unit that has not moved yet",
            def: true,
            onChange: repaintIniOverlay
        }
    ];

    function byKey(key) {
        for (var i = 0; i < OPTIONS.length; i++) {
            if (OPTIONS[i].key === key) return OPTIONS[i];
        }
        return null;
    }

    function load() {
        values = {};
        for (var i = 0; i < OPTIONS.length; i++) {
            values[OPTIONS[i].key] = OPTIONS[i].def;
        }

        try {
            var raw = window.localStorage.getItem(PREF_KEY);
            if (!raw) return;
            var saved = JSON.parse(raw);
            for (var k in values) {
                //Only a real boolean overrides the default: a key written by an older
                //version, or one that has since been retired, must not turn into `false`.
                if (typeof saved[k] === "boolean") values[k] = saved[k];
            }
        } catch (e) { /* defaults stand */ }
    }

    function save() {
        try {
            window.localStorage.setItem(PREF_KEY, JSON.stringify(values));
        } catch (e) { /* a preference that cannot be remembered still works this session */ }
    }

    /* The initiative badge is drawn by MovementPhaseStrategy, which is the only thing that
       knows the "one badge per hex, lowest group wins" rule - so the toggle asks IT to
       repaint rather than reaching into the icons itself. Every other phase has already
       cleared the badges (deactivate does it), so there is nothing to repaint there and the
       missing method is the correct answer, not a guard against one.

       ⚠️ requestRender (arch_render_loop_idle_gating): the render loop is idle-gated, so a
       scene mutation made outside the animation list paints nothing until a frame is asked
       for. */
    function repaintIniOverlay() {
        var director = window.webglScene && webglScene.phaseDirector;
        var strategy = director && director.phaseStrategy;

        if (strategy && typeof strategy.refreshNotMovedMarkers === "function") {
            strategy.refreshNotMovedMarkers();
        }

        if (window.webglScene && webglScene.requestRender) {
            webglScene.requestRender();
        }
    }

    return {

        PREF_KEY: PREF_KEY,

        //Reading an unregistered key is a coding error, not a missing preference: answering
        //`undefined` would silently read as "off" at the call site and switch a feature off
        //for everybody. The registered default is the only honest answer.
        get: function get(key) {
            if (values === null) load();
            if (!(key in values)) {
                throw new Error("Unrecognized game option '" + key + "'");
            }
            return values[key];
        },

        set: function set(key, value) {
            if (values === null) load();
            var option = byKey(key);
            if (!option) {
                throw new Error("Unrecognized game option '" + key + "'");
            }

            value = !!value;
            if (values[key] === value) return;

            values[key] = value;
            save();
            if (option.onChange) option.onChange(value);
        },

        /* Draws every registered option into #gameOptionsList. The checkbox carries the key
           on itself, so the change handler is one delegated binding rather than one per row
           - and a row added to OPTIONS later needs no wiring of its own. */
        render: function render() {
            var list = $("#gameOptionsList");
            if (!list.length) return;
            if (values === null) load();

            var html = "";
            for (var i = 0; i < OPTIONS.length; i++) {
                var option = OPTIONS[i];
                html += "<label class='fv-opt-row' title='" + option.title + "'>"
                    + "<input type='checkbox' class='fv-opt-check' data-option='" + option.key + "'"
                    + (values[option.key] ? " checked" : "") + ">"
                    + "<span class='fv-opt-label'>" + option.label + "</span>"
                    + "</label>";
            }

            list.html(html);

            if (!list.attr("data-fv-bound")) {
                list.attr("data-fv-bound", "1");
                list.on("change", ".fv-opt-check", function () {
                    gameOptions.set(String($(this).data("option")), this.checked);
                });
            }
        }
    };
})();

/* game.php bootstrap. The generic data-select handler in UI/botPanel.js switches the panels
   and fires "onshow" on the one it reveals; savedFleets.js binds its own "onshow" to the
   same #gameoptions element for the Save Fleet section below these rows. */
jQuery(function () {
    if (!$("#gameOptionsList").length) return;   //lobby, or any page without the panel
    gameOptions.render();
});
