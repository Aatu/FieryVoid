'use strict';

window.ShipTooltipMenu = function () {

    function ShipTooltipMenu(selectedShip, targetedShip, turn) {
        this.selectedShip = selectedShip;
        this.targetedShip = targetedShip;
        this.turn = turn;
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
            element.on('click', onClick.bind(this, shipTooltip, buttonData.action));
            element.on('mouseover', getMouseOver.call(this, menu, buttonData.info));
            element.on('mouseout', mouseOut.bind(this, menu));
            jQuery(".action-buttons", menu).append(element);
        }, this);

        jQuery(".info", menu).html(this.currentInfo);
        parent.append(menu);
    };

    ShipTooltipMenu.prototype.getAllButtons = function () {
        return ShipTooltipMenu.buttons.concat(this.extraButtons);
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

    function onClick(shipTooltip, action, event) {
        event.stopPropagation();
        action.call(this);
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
        shipWindowManager.open(this.targetedShip);
    }

    return ShipTooltipMenu;
}();