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
    }

    ShipTooltipMenu.template = '<div class="menu"><div class="action-buttons"></div><div class="info"></div></div>';

    ShipTooltipMenu.buttonTemplate = '<button></button>';

    ShipTooltipMenu.buttons = [{ className: "openSCS", condition: null, action: openSCS, info: "Open ship details" }];

    ShipTooltipMenu.prototype.renderTo = function (parent, shipTooltip) {

        var menu = jQuery(ShipTooltipMenu.template);
        var allButtons = this.getAllButtons();

        allButtons.filter(function (buttonData) {
            if (!buttonData.condition) {
                return true;
            }

            var conditions = [].concat(buttonData.condition);
            return conditions.every(function (condition) {
                return condition.call(this);
            }, this);
        }, this).forEach(function (buttonData) {
            var element = jQuery(ShipTooltipMenu.buttonTemplate);
            element.addClass(buttonData.className);
            element.css({
                'touch-action': 'manipulation',
                '-webkit-touch-callout': 'none',
                '-webkit-user-select': 'none',
                'user-select': 'none'
            });
            bindButton.call(this, element, shipTooltip, buttonData.action, buttonData.supportsMaxClick);
            element.on('mouseover', getMouseOver.call(this, menu, buttonData.info));
            element.on('mouseout', mouseOut.bind(this, menu));
            jQuery(".action-buttons", menu).append(element);
        }, this);

        jQuery(".info", menu).html(this.currentInfo);
        parent.append(menu);
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

    function onClick(shipTooltip, action, isMaxClick, event) {
        event.preventDefault();
        event.stopPropagation();
        action.call(this, isMaxClick);
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
        webglScene.customEvent("OpenShipWindowFor", {ship: this.targetedShip});
    }

    return ShipTooltipMenu;
}();