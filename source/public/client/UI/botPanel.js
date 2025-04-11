"use strict";

jQuery(function () {
	jQuery("#logUI div").on("click", window.botPanel.onLogUIClicked)

	
	$("#expandBotPanel").click(function () {
		$("#logcontainer").toggleClass('large');

		// Manually create a fake element with the right data-select
		var fakeElement = $("<div>").data("select", "#log");

		gamedata.sliderToggle();

		// Call onLogUIClicked with that fake element as "this"
		window.botPanel.onLogUIClicked.call(fakeElement);
		document.getElementById("iniSlider").src = "img/pullIn.png";
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