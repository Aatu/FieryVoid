'use strict';

window.ShipTooltipMenu = function () {

    function ShipTooltipMenu(selectedShip, targetedShip, turn) {
        this.selectedShip = selectedShip;
        this.targetedShip = targetedShip;
        this.turn = turn;
        //leadingButtons render BEFORE the static ShipTooltipMenu.buttons (i.e.
        //to the left of "Open ship details"). extraButtons render AFTER.
        //This mirrors how ShipTooltipFireMenu places its phase-specific
        //buttons (targetWeapons, launchFighters, dockFlight) ahead of the
        //base menu's openSCS — keeps the icon order consistent across phases.
        this.leadingButtons = [];
        this.extraButtons = [];
        this.currentInfo = "";
        //Set by renderTo; the tooltip currently displaying this menu, so an action can redraw it.
        this.shipTooltip = null;
    }

    ShipTooltipMenu.template = '<div class="menu"><div class="action-buttons"></div><div class="info"></div></div>';

    ShipTooltipMenu.buttonTemplate = '<button></button>';

    /* The ship card's placement fields (SHIP_TOOLTIP_REDESIGN_PLAN.md §5.2) - additive and optional,
       read only by ShipTooltip renderActions; renderTo ignores them:
         label             the caption under a tile / on a button (a string, or a function of the menu)
         kind: 'footer'    the "Ship details" button in the card's footer
         kind: 'primary'   the full-width Target button under TARGETING
         kind: 'clear'     "Clear all EW", at the foot of the EW section
         group + role      one half ('add' / 'remove') of an EW −/+ stepper; group = the EW type
       Anything without them is drawn as a captioned tile. */
    ShipTooltipMenu.buttons = [{ className: "openSCS", condition: null, action: openSCS, info: "Open ship details", label: "Show ship details", kind: "footer" }];

    ShipTooltipMenu.prototype.renderTo = function (parent, shipTooltip) {

        //Keep a handle on the tooltip that drew this menu so an ACTION can ask for a redraw.
        //ShipTooltip.update() re-runs every button's condition list, which is the only way a
        //button can turn into its opposite without the player having to re-click the unit - see
        //cancelJumpGateSignal in shipTooltipInitialOrdersMenu.js. renderTo is called again by
        //update() itself, so this stays pointed at the live tooltip.
        //(The ship card no longer calls renderTo: it places each button in a slot of its own - see
        //ShipTooltip renderActions - through getVisibleButtons / bindAction / bindInfo below, which
        //are this function's own steps, split out. renderTo stays for any other owner.)
        this.shipTooltip = shipTooltip;

        var menu = jQuery(ShipTooltipMenu.template);

        this.getVisibleButtons().forEach(function (buttonData) {
            var element = jQuery(ShipTooltipMenu.buttonTemplate);
            element.addClass(buttonData.className);
            this.bindAction(element, buttonData, shipTooltip);
            /* A button's info line may be a FUNCTION of the menu it is being drawn into, not just
               a fixed string - the same action can mean something different depending on what is
               selected and what was right-clicked. Resolved at render time, with the menu as
               `this`, so it sees the same selectedShip/targetedShip every condition above does.
               (WALKERS_OF_SIGMA_PLAN.md 3.9: "Target Hex" reads "Target Ship" when a Sensor Charge
               Transceiver is up and the unit under the cursor is one the charge could hit.) */
            var info = (typeof buttonData.info === 'function')
                ? buttonData.info.call(this)
                : buttonData.info;

            this.bindInfo(element, menu, info);
            jQuery(".action-buttons", menu).append(element);
        }, this);

        jQuery(".info", menu).html(this.currentInfo);
        parent.append(menu);
    };

    /* Which buttons this menu offers right now: every entry whose conditions ALL pass.
       ⚠️ THIS IS THE FILTER THE HARNESSES LIFT (tests/replay/initialOrdersTooltipHarness.js,
       lateEwElintHarness.js): [].concat(condition), then .every(c => c.call(menu)). A throwing
       condition throws out of here and takes the whole card with it - every predicate must be
       null-safe ([[arch_tooltip_menu_null_selection]]). */
    ShipTooltipMenu.prototype.getVisibleButtons = function () {
        return this.getAllButtons().filter(function (buttonData) {
            if (!buttonData.condition) {
                return true;
            }

            var conditions = [].concat(buttonData.condition);
            return conditions.every(function (condition) {
                return condition.call(this);
            }, this);
        }, this);
    };

    /* Bind one button exactly as renderTo always has: click, right-click max and the 500ms touch
       long press (supportsMaxClick), plus the four touch properties. The ship card binds every
       stepper, tile and button it places through this (SHIP_TOOLTIP_REDESIGN_PLAN.md §5.1). */
    ShipTooltipMenu.prototype.bindAction = function (element, buttonData, shipTooltip) {
        element.css({
            'touch-action': 'manipulation',
            '-webkit-touch-callout': 'none',
            '-webkit-user-select': 'none',
            'user-select': 'none'
        });
        bindButton.call(this, element, shipTooltip, buttonData.action, buttonData.supportsMaxClick);
    };

    //The hover line under the buttons: this button's info while the pointer rests on it.
    ShipTooltipMenu.prototype.bindInfo = function (element, menuElement, info) {
        element.on('mouseover', getMouseOver.call(this, menuElement, info));
        element.on('mouseout', mouseOut.bind(this, menuElement));
    };

    ShipTooltipMenu.prototype.getAllButtons = function () {
        return this.leadingButtons.concat(ShipTooltipMenu.buttons, this.extraButtons);
    };

    ShipTooltipMenu.prototype.isEmpty = function () {
        return this.getAllButtons().filter(function (buttonData) {
            var conditions = [].concat(buttonData.condition);
            return conditions.every(function (condition) {
                return condition.call(this);
            }, this);
        }).length > 0;
    };

    ShipTooltipMenu.prototype.addButton = function (className, condition, action, info) {
        this.extraButtons.push({ className: className, condition: condition, action: action, info: info });
    };

    //Same as addButton but places the button to the LEFT of the static
    //base buttons (eg. openSCS). Use for phase-specific buttons that should
    //sit alongside the firing-phase action icons, not after them.
    ShipTooltipMenu.prototype.addLeadingButton = function (className, condition, action, info) {
        this.leadingButtons.push({ className: className, condition: condition, action: action, info: info });
    };

    var LONG_PRESS_MS = 500;

    function bindButton(element, shipTooltip, action, supportsMaxClick) {
        var self = this;
        var longPressTimer = null;
        var suppressNextClick = false;

        function clearLongPress() {
            if (longPressTimer) {
                clearTimeout(longPressTimer);
                longPressTimer = null;
            }
        }

        element.on('click', function (event) {
            if (suppressNextClick) {
                suppressNextClick = false;
                event.preventDefault();
                event.stopPropagation();
                return;
            }
            onClick.call(self, shipTooltip, action, false, event);
        });

        if (!supportsMaxClick) {
            element.on('contextmenu', suppressContextMenu);
            return;
        }

        element.on('contextmenu', function (event) {
            clearLongPress();
            suppressNextClick = false;
            onClick.call(self, shipTooltip, action, true, event);
        });

        element.on('touchstart', function () {
            clearLongPress();
            suppressNextClick = false;
            longPressTimer = setTimeout(function () {
                longPressTimer = null;
                suppressNextClick = true;
                action.call(self, true);
            }, LONG_PRESS_MS);
        });

        element.on('touchend', function (event) {
            clearLongPress();
            if (suppressNextClick) {
                event.preventDefault();
            }
        });

        element.on('touchmove touchcancel', clearLongPress);
    }

    //The event rides along as a second argument for an action that anchors something to its button (the fighter
    //mission dropdown); every other action ignores it. A long press calls its action with no event.
    function onClick(shipTooltip, action, isMaxClick, event) {
        event.preventDefault();
        event.stopPropagation();
        action.call(this, isMaxClick, event);
    }

    function suppressContextMenu(event) {
        event.preventDefault();
        event.stopPropagation();
    }

    function mouseOut(menu) {
        this.currentInfo = "";
        jQuery(".info", menu).html("");
    }

    function getMouseOver(menu, info) {
        return function () {
            this.currentInfo = info;
            jQuery(".info", menu).html(info);
        }.bind(this);
    }

    function openSCS() {
        //toggle (round 5, user 2026-11-10): a second press closes that ship's window again.
        //Only this button asks for it - the fleet list's row still always opens.
        webglScene.customEvent("OpenShipWindowFor", {ship: this.targetedShip, toggle: true});
    }

    return ShipTooltipMenu;
}();