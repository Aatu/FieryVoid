"use strict";

jQuery(function () {
	jQuery("#logUI div").on("click", window.botPanel.onLogUIClicked)

	
	$("#expandBotPanel").click(function () {
		if ($("#logcontainer").data("large") == 1) {

			$("#logcontainer").data("large", 0);
			$("#logcontainer").height(150);
			$("#log").height(150);

			$(".chatMessages").height(150);
		} else {

			$("#logcontainer").data("large", 1);
			$("#logcontainer").height(300);
			$("#log").height(300);

			$(".chatMessages").height(300);
		}
	});
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

	onShipStatusChanged: function onShipStatusChanged(ship) {
		if (botPanel.updateCallback) {
			botPanel.updateCallback(ship);
		}
	},

	deactivate: function deactivate() {
		botPanel.updateCallback = null;
	},

	setEW: function setEW(ship) {
		botPanel.updateCallback = botPanel.setEW;
		shipWindowManager.addEW(ship, $("#botPanel"));
	},
};