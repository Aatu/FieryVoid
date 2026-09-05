"use strict";

/* JUMP_GATES_PLAN.md STAGE 3 - THE FIXED JUMP GATE SIGNAL PANEL.

   The whole control is: IS IT ON, AND FOR HOW LONG.

       click the gate  (no ship selected - none is needed, and none is ever chosen)
         -> tooltip button "Signal Jump Gate"
             -> this panel anchors to the gate's hex:
                          Open for  [-] [ 3 ] [+]  turns
                              [ Signal Jump Gate ]
                 -> Signal ..... weaponManager builds the FireOrder, the panel closes
                 -> click away .. discards; no order, nothing to clean up
                                  (a one-shot on PhaseStrategy.onClickCallbacks)

   ⭐ REINFORCEMENTS_PLAN.md STAGE 8 - THE SAME PANEL SIGNALS FOR ARRIVAL, and the difference is a
   class and a word. A second tooltip button, "Signal Gate for Arrival", raises the identical
   transaction with pending.exit set; open() then puts .gateSignalExit on the container -
   which re-liveries the whole panel in FV's "not here yet" cyan through the --gs-* tokens
   tactical.css defines it with - and relabels the commit "Signal for Arrival". THE DURATION IS
   STILL THE ONLY THING THE PLAYER CHOOSES: an exit has no facing to aim any more than an entrance
   does, because a gate's facing was fixed when the gate was placed.

   Pressing it builds a claim with damageclass 'gateexit' and then opens the Jump Point Manifest
   dialog (user request 2026-08-28) - see weaponManager.createGateSignalOrder, which owns both.

   ⭐ IT IS THE REACT POWER SETTINGS MENU'S BOOST ROW (user request 2026-08-23), redrawn in plain
   HTML+CSS: Label / Controls / Value, the same gold chrome, and the Signal button in that menu's
   own $variant="warning" yellow. reactJs/system/SystemPowerSettings.js is the source of truth for
   the styling; tactical.css carries the mirror and the reasoning.

   Two earlier revisions are worth not repeating. It was a bespoke box first - self-consistent but
   foreign - and then wore the ship TOOLTIP's menu markup, which read far worse again: a row of
   40x40 icon buttons needs more width than a panel this narrow has, so it wrapped into a column
   and the heading broke in half. Reuse is not free when the thing reused is sized for another job;
   the Boost row is the right thing to copy because it already solves this one. The X is gone
   throughout: clicking off the panel discards it, which is how every other floating control on
   this screen is dismissed.

   ⭐ THE DURATION IS A REAL <input>, WITH THREE WAYS IN AND ONE CLAMP - and it is the one place
   this goes further than the Boost row, whose Value is display-only. Typing, the mousewheel and
   the two stepper buttons all land in setHold(), the only function that writes pending.hold, so
   the gate's cap cannot be dodged by one route while the other two honour it. The field is
   authoritative only at the moment it is READ: input events do NOT write back to it (that would
   fight the caret mid-typing), so confirm() re-reads it before building the order rather than
   trusting the last event to have fired.

   ⭐ THERE IS NO FACING CONTROL, AND THAT IS THE DESIGN (plan section 2.2, user ruling
   2026-08-23). A gate's vortex ALWAYS takes the gate's own facing, and that facing is set when the
   gate is placed and fixed for the rest of the game. The player cannot aim it, re-aim it or project
   it - so the sibling control UI.vortexFacing, which exists to set exactly that, has no gate
   equivalent and never will. What the gate DOES let the player choose is the programmed open
   duration, and that is a number.

   ⭐ WHICH IS WHY THIS IS PLAIN ANCHORED HTML AND NOT A CANVAS RING. UI.vortexFacing's ring of
   drawn glyphs exists because a FACING has to swing with the thing it sets and stay legible at six
   angles; a duration needs none of that. What IS reused is the anchoring - the convert-to-viewport
   step and PhaseStrategy's zoom/scroll callback lists - and nothing else. No sprite, no glyph
   constants, no drawCurvedArrow, and no preview marker: the gate is already sitting on the hex with
   its own permanent facing arrow, which is the only thing a preview could usefully show.

   SAME TRANSACTION DISCIPLINE AS THE FACING CONTROL: THE ORDER IS BORN ON SIGNAL. Nothing is
   declared while the panel is open, so there is no half-made claim to nag about before the Initial
   Orders commit and nothing to unwind on a discard. Changing your mind is remove-and-redeclare -
   the ordinary ballistic idiom, and what the tooltip's "Cancel Gate Signal" button does.

   window.UI is CREATED by shipMovement.js (it assigns the whole object), so this file must load
   after it in game.php or that assignment would wipe this module - exactly the hazard vortexFacing.js
   documents. The defensive ||= below keeps a future reordering from being silently fatal. */

window.UI = window.UI || {};

window.UI.gateSignal = {

    iniated: false,
    uiElement: null,
    panelElement: null,
    downElement: null,
    upElement: null,
    valueElement: null,
    unitElement: null,
    confirmElement: null,

    /* The transaction in progress, or null. Held as the payload OBJECT weaponManager raised, so
       PhaseStrategy can ask "is the panel still showing the transaction I registered?" by identity
       - the same token pattern hideShipTooltip and hideVortexFacingUI use. */
    pending: null,

    gamePosition: null,
    currentPosition: null,
    //When and on which control a TOUCH last landed - the only thing swallowSyntheticClick reads.
    lastTouchTime: 0,
    lastTouchKey: null,

    initGateSignalUI: function initGateSignalUI() {
        if (UI.gateSignal.iniated === true) return;

        var ui = $("#gateSignalUI");
        UI.gateSignal.uiElement = ui;
        UI.gateSignal.panelElement = $("#gateSignalPanel", ui);
        UI.gateSignal.downElement = $("#gateSignalDown", ui);
        UI.gateSignal.upElement = $("#gateSignalUp", ui);
        UI.gateSignal.valueElement = $("#gateSignalValue", ui);
        //Cached once rather than re-queried per draw() - draw() runs on every stepper press.
        UI.gateSignal.unitElement = $(".gateSignalUnit", ui);
        UI.gateSignal.confirmElement = $("#gateSignalConfirm", ui);

        UI.gateSignal.downElement.on("click touchstart", UI.gateSignal.stepDownCallback);
        UI.gateSignal.upElement.on("click touchstart", UI.gateSignal.stepUpCallback);
        UI.gateSignal.confirmElement.on("click touchstart", UI.gateSignal.confirmCallback);

        /* THE FIELD. `input` reads it WITHOUT writing back - rewriting on every keystroke fights
           the caret - so the field may briefly show a value the clamp has already rejected;
           `change` and `blur` settle it, and confirm() re-reads it either way. Focus selects the
           whole value so a click straight into the field can be typed over (the same courtesy
           MineDeployment's count fields extend). */
        UI.gateSignal.valueElement.on("input", function () { UI.gateSignal.readField(false); });
        UI.gateSignal.valueElement.on("change blur", function () { UI.gateSignal.readField(true); });
        UI.gateSignal.valueElement.on("focus dblclick", function (e) { e.target.select(); });
        UI.gateSignal.valueElement.on("keydown", UI.gateSignal.keydownCallback);

        /* ⚠️ THE FIELD MUST NOT BE COVERED BY THE BLANKET MASK BELOW. That mask preventDefaults
           every press on the panel, and mousedown's default action is exactly what focuses an input
           and places the caret - and preventDefault called anywhere in the propagation path cancels
           it, bubble phase included. So the field stops its own events short of the panel instead:
           no preventDefault (it keeps its focus), but the press never reaches the map either, which
           it must not - the click-away discard would close the panel the moment it was clicked in. */
        UI.gateSignal.valueElement.on("mousedown mouseup click touchstart touchend", function (e) {
            e.stopPropagation();
        });

        /* THE MOUSEWHEEL, ON THE WHOLE PANEL rather than on the field alone: the panel is small and
           entirely about this one number, and a wheel anywhere over it that fell through to the map
           would ZOOM THE BOARD under an open dialog.
           ⚠️ ALL THREE WHEEL EVENT NAMES ARE SWALLOWED. webglScene binds the LEGACY 'mousewheel'
           and 'DOMMouseScroll' on #pagecontainer, which this panel sits inside, and those are
           separate dispatches from the standard 'wheel' - preventDefaulting one does not cancel the
           others, so a browser that fires two would step the duration AND zoom. Only 'wheel' steps
           it, for the same reason.
           passive:false because a listener that means to preventDefault must say so. */
        ['wheel', 'mousewheel', 'DOMMouseScroll'].forEach(function (name) {
            UI.gateSignal.panelElement[0].addEventListener(name, function (e) {
                e.preventDefault();
                e.stopPropagation();
                if (name !== 'wheel') return;
                UI.gateSignal.step(e.deltaY < 0 ? 1 : -1);
            }, { passive: false });
        });

        //Same masking #shipMovementUI and #vortexFacingUI use: these divs sit above the WebGL
        //canvas, and without it a press on a button also reads as a map click - which would run the
        //click-away discard and close the panel from under the finger.
        jQuery('#gateSignalUI div, #gateSignalUI button, #gateSignalUI .gateSignalButton').on('mousedown touchend touchmove', cancelEvent);
        jQuery('#gateSignalUI div, #gateSignalUI button, #gateSignalUI .gateSignalButton').on('mouseup touchend touchmove', cancelEvent);
        ui[0].addEventListener('contextmenu', function (e) {
            e.preventDefault();
        }, true);

        function cancelEvent(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        UI.gateSignal.iniated = true;
    },

    /* ⭐⭐ THE GUARD IS FOR ONE GESTURE ARRIVING TWICE, NOT FOR TWO DELIBERATE PRESSES (user report
       2026-09-02: "if I press twice in quick succession the 'Open for' value only changes once").

       The buttons are bound to "click touchstart" because a TOUCH fires touchstart and then a
       synthetic click ~300ms behind it, which would step the duration twice per tap. The first
       version of this refused any second event on the same control inside 350ms - which did
       de-duplicate the tap, and also ate the second of two deliberate mouse clicks, because a real
       double press and a ghost click are INDISTINGUISHABLE when all you look at is the clock. On a
       stepper, where pressing repeatedly is the whole gesture, that is the wrong half to throw away.

       SO LOOK AT THE EVENT TYPE INSTEAD, which separates them exactly:
         - a touchstart always acts, and stamps the window;
         - a click acts UNLESS it is the tail of a touch on this same control - which is precisely
           what the synthetic one is, and what a real mouse click can never be.
       Two rapid taps still behave: the second touchstart re-stamps the window, and it is that tap's
       own ghost click the window then swallows. Two rapid mouse clicks are two clicks with no touch
       behind them, so neither is ever refused, however fast they come.

       ⚠️ STILL KEYED PER CONTROL, as it has been since the first version: a single shared stamp
       swallowed the NEXT control's first press too, so ticking the duration up and immediately
       pressing SIGNAL did nothing.

       700ms rather than the ~300ms a ghost click actually takes: the window is only ever consulted
       by a click, and a genuine tap's own touchstart re-stamps it first, so a generous window costs
       a touch user nothing and covers a slow device. */
    swallowSyntheticClick: function swallowSyntheticClick(key, e) {
        var now = new Date().getTime();

        if (e.type === "touchstart") {
            UI.gateSignal.lastTouchKey = key;
            UI.gateSignal.lastTouchTime = now;
            return false;
        }

        return key === UI.gateSignal.lastTouchKey && now - UI.gateSignal.lastTouchTime < 700;
    },

    stepDownCallback: function stepDownCallback(e) {
        e.preventDefault();
        e.stopPropagation();
        if (UI.gateSignal.swallowSyntheticClick("down", e)) return;
        UI.gateSignal.step(-1);
    },

    stepUpCallback: function stepUpCallback(e) {
        e.preventDefault();
        e.stopPropagation();
        if (UI.gateSignal.swallowSyntheticClick("up", e)) return;
        UI.gateSignal.step(1);
    },

    confirmCallback: function confirmCallback(e) {
        e.preventDefault();
        e.stopPropagation();
        if (UI.gateSignal.swallowSyntheticClick("confirm", e)) return;
        UI.gateSignal.confirm();
    },

    /* Enter commits from inside the field, which is where the hand already is after typing a
       duration; Escape discards, matching the click-away. Both stop the key reaching the map's own
       shortcut handling. */
    keydownCallback: function keydownCallback(e) {
        if (e.key === "Enter") {
            e.preventDefault();
            e.stopPropagation();
            UI.gateSignal.confirm();
            return;
        }

        if (e.key === "Escape") {
            e.preventDefault();
            e.stopPropagation();
            UI.gateSignal.close();
        }
    },

    isOpen: function isOpen() {
        return UI.gateSignal.pending !== null;
    },

    //Identity, not equality - see `pending` above.
    isOpenFor: function isOpenFor(pending) {
        return UI.gateSignal.pending !== null && UI.gateSignal.pending === pending;
    },

    /* pending = { gate, engine, hexpos, hold, maxHold, exit, onConfirm } - raised by
       weaponManager.queueGateSignalOrder and relayed here by PhaseStrategy.onGateSignalRequested. */
    open: function open(pending) {
        UI.gateSignal.initGateSignalUI();
        UI.gateSignal.close(); //one transaction at a time; a new panel replaces the old

        /* ⭐ REINFORCEMENTS_PLAN.md STAGE 8 - THE BLUE LIVERY, AND IT IS ONE CLASS AND ONE LABEL.
           An ARRIVAL claim asks the gate for a doorway IN, and #00b8e6 is FV's established "not
           here yet" cyan - the colour of the Forming marker, the exit vortex and the fleet
           list's hyperspace row (plan section 3.7). The panel is otherwise the identical control:
           the one thing a gate lets a player choose is still the duration, and an exit has no
           facing to aim any more than an entrance does.

           ⚠️ SET ON EVERY OPEN, BOTH WAYS. The panel is a singleton reused across transactions, so
           an exit claim followed by an ordinary one would leave the blue behind - toggleClass
           with an explicit second argument, never a bare add. Same for the label: it is written
           here rather than in game.php because the markup has one button and two meanings. */
        UI.gateSignal.uiElement.toggleClass("gateSignalExit", !!pending.exit);
        UI.gateSignal.confirmElement.text(pending.exit ? "Signal for Arrival" : "Signal Jump Gate");

        UI.gateSignal.pending = pending;
        UI.gateSignal.gamePosition = window.coordinateConverter.fromHexToGame(pending.hexpos);
        UI.gateSignal.currentPosition = null;
        UI.gateSignal.lastTouchTime = 0;
        UI.gateSignal.lastTouchKey = null;

        //`max` is per GATE, not a constant - see setHold - so it is stamped on the field each time
        //the panel opens rather than written into the markup.
        UI.gateSignal.valueElement.attr("max", pending.maxHold);

        UI.gateSignal.draw();
        UI.gateSignal.uiElement.show();
    },

    close: function close() {
        if (!UI.gateSignal.pending) return;

        UI.gateSignal.pending = null;
        UI.gateSignal.gamePosition = null;
        UI.gateSignal.currentPosition = null;

        if (UI.gateSignal.uiElement) UI.gateSignal.uiElement.hide();
    },

    /* ⭐ THE ONLY FUNCTION THAT WRITES pending.hold, so the cap cannot be dodged by one route while
       the other two honour it: the arrows, the wheel and the keyboard all arrive here.

       THE CAP IS THE GATE'S OWN, not a constant. A wounded gate cannot hold the door open as long -
       every 15 points on its Reactor costs a turn, floor 1 - and maxHold is that number, computed
       once when the panel opened (JumpEngine::getGateMaxHold). Reactor damage cannot change while
       Initial Orders are open, so it does not need re-reading per step.

       writeField=false is the TYPING path: rewriting the field on every keystroke fights the caret,
       so an out-of-range value is allowed to sit on screen until change/blur - or until confirm(),
       which re-reads the field precisely so that never reaches an order. */
    setHold: function setHold(hold, writeField) {
        var pending = UI.gateSignal.pending;
        if (!pending) return;

        if (hold < 1) hold = 1;
        if (hold > pending.maxHold) hold = pending.maxHold;

        pending.hold = hold;
        UI.gateSignal.draw(writeField);
    },

    step: function step(delta) {
        var pending = UI.gateSignal.pending;
        if (!pending) return;
        UI.gateSignal.setHold(pending.hold + delta, true);
    },

    /* Pull the field's current text into pending.hold. An empty or half-typed field parses to NaN,
       which is not an error - it is a player mid-edit - so the last good value stands, and only a
       settling read (writeField) puts it back on screen. */
    readField: function readField(writeField) {
        if (!UI.gateSignal.pending) return;

        var raw = parseInt(UI.gateSignal.valueElement.val(), 10);
        if (isNaN(raw)) {
            if (writeField) UI.gateSignal.draw(true);
            return;
        }

        UI.gateSignal.setHold(raw, writeField);
    },

    draw: function draw(writeField) {
        var pending = UI.gateSignal.pending;
        if (!pending) return;

        //Defaults to writing: every caller except the typing path wants the field settled.
        if (writeField !== false) UI.gateSignal.valueElement.val(pending.hold);
        //"turn" / "turns" reads as a rule the player can trust; "1 turns" reads as a placeholder.
        UI.gateSignal.unitElement.text(pending.hold === 1 ? "turn" : "turns");

        //A cap of 1 (a badly damaged gate) leaves both steppers dead - say so by disabling them
        //rather than letting the player press a button that does nothing.
        UI.gateSignal.downElement.toggleClass("disabled", pending.hold <= 1);
        UI.gateSignal.upElement.toggleClass("disabled", pending.hold >= pending.maxHold);
    },

    confirm: function confirm() {
        var pending = UI.gateSignal.pending;
        if (!pending) return;

        /* ⚠️ RE-READ THE FIELD FIRST, and do not assume a blur has settled it. Pressing SIGNAL does
           NOT blur the field: the panel's press mask preventDefaults mousedown, which is exactly
           what would have moved focus. So a typed duration is still sitting unread in the input at
           this moment, and without this the order would carry the value from before it was typed. */
        UI.gateSignal.readField(true);

        //Close FIRST: onConfirm raises HexTargeted, which rebuilds the ballistic icons - and the
        //committed claim draws its own yellow "Jump Gate Signalled" hex on this very spot.
        UI.gateSignal.close();
        pending.onConfirm(pending.hold);
    },

    //Game-space position of the gate's hex - what PhaseStrategy.positionGateSignalUI converts to
    //viewport coordinates on every zoom and scroll.
    getPosition: function getPosition() {
        return UI.gateSignal.gamePosition;
    },

    /* The container is anchored to the hex CENTRE and the panel itself is lifted clear of it by
       CSS (translate + a fixed gap), so nothing here has to know the hex's on-screen size. That is
       the one place this control is simpler than UI.vortexFacing, whose ring has to be re-laid-out
       on every zoom because it is measured off the hex radius. */
    reposition: function reposition(position) {
        if (!UI.gateSignal.uiElement || !UI.gateSignal.pending) return;

        var current = UI.gateSignal.currentPosition;
        if (current && current.x === position.x && current.y === position.y) return;

        UI.gateSignal.uiElement.css("top", position.y + "px").css("left", position.x + "px");
        UI.gateSignal.currentPosition = position;
    }

};
