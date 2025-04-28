"use strict";

jQuery(function () {
	jQuery("#logUI div").on("click", window.botPanel.onLogUIClicked)

	
	$("#expandBotPanel").click(function () {
		var logContainer = $("#logcontainer");
	
		logContainer.toggleClass('large');
	
		// Hide iniGui if logcontainer is large
		if (logContainer.hasClass('large')) {
            $("#iniGui").hide();
            $(backDiv).data("on", 0);
            backDiv.style.marginLeft = "0px";
            document.getElementById("iniSlider").src = "img/pullOut.png";
		} else {
			// If not large, ensure it behaves normally
            $("#iniGui").show();
            $(backDiv).data("on", 1);
            backDiv.style.marginLeft = "250px";
            document.getElementById("iniSlider").src = "img/pullIn.png";
		}

		// Manually create a fake element with the right data-select
		var fakeElement = $("<div>").data("select", "#log");		
		// Call onLogUIClicked with that fake element as "this"
		window.botPanel.onLogUIClicked.call(fakeElement);
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