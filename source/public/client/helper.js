'use strict';

hidehelper = false;

window.onload = function () {
	helper.doUpdateHelpStatus();
};

window.helper = {

	curphaseid: 0,
	curpageid: 0,
	nextpageid: 0,
	autocomm: false,

	onClickHelpHide: function onClickHelpHide() {
		if (document.getElementById('globalhelp').style.display != 'none') {
			hidehelper = true;
        ajaxinterface.ajaxWithRetry({
				type: 'POST',
				url: 'helperdata.php',
				dataType: 'text',
				data: { hidehelper: hidehelper },
				success: window.helper.successPost,
				error: window.helper.errorAjax
			});
		} else {
			hidehelper = false;
        ajaxinterface.ajaxWithRetry({
				type: 'POST',
				url: 'helperdata.php',
				dataType: 'text',
				data: { hidehelper: hidehelper },
				success: window.helper.successPost,
				error: window.helper.errorAjax
			});
		}
		helper.doUpdateHelpStatus();
	},

	onClickAutoCommit: function onClickAutoCommit() {
		if (document.getElementById('autocommitimg').getAttribute("src") == "img/ok.png") {
			window.helper.autocomm = true;
			document.getElementById('autocommitimg').setAttribute("src", "img/offline.png");
			document.getElementById('autocommittext').innerHTML = "AUTO";
		} else {
			document.getElementById('autocommitimg').setAttribute("src", "img/ok.png");
			document.getElementById('autocommittext').innerHTML = "COMMIT";
			window.helper.autocomm = false;
		}
	},

	onClickInHelp: function onClickInHelp() {
		window.helper.curpageid = window.helper.nextpageid;
		window.helper.doUpdateHelpContent(window.helper.curphaseid, window.helper.curpageid);
	},

	doUpdateHelpStatus: function doUpdateHelpStatus() {
        ajaxinterface.ajaxWithRetry({
			type: 'GET',
			url: 'helperdata.php',
			dataType: 'text',
			data: { hidehelper: hidehelper },
			success: window.helper.successGet,
			error: window.helper.errorAjax
		});
	},

	doUpdateHelpContent: function doUpdateHelpContent(phaseid, helppageid) {
		helpMessage = String(phaseid) + "p" + String(helppageid);
		window.helper.curphaseid = phaseid;
		window.helper.curpageid = helppageid;
		helpimg = 'img/vir2.jpg';
        ajaxinterface.ajaxWithRetry({
			type: 'GET',
			url: 'helperdata.php',
			dataType: 'json',
			data: { message: helpMessage, helpimg: helpimg, nextpageid: window.helper.nextpageid },
			success: window.helper.successGetHelpMessage,
			error: window.helper.errorAjax
		});
	},

	successGet: function successGet(data) {
		if (data == "true") {
			hidehelper = true;
		} else {
			hidehelper = false;
		}
		if (hidehelper) {
			document.getElementById('globalhelp').style.display = 'none';
			document.getElementById('helphideimg').setAttribute("src", "img/vir2.jpg");
		} else {
			document.getElementById('globalhelp').style.display = 'block';
			document.getElementById('helphideimg').setAttribute("src", "img/greyvir.jpg");
		}
	},

	successGetHelpMessage: function successGetHelpMessage(data) {
		document.getElementById('ingamehelpMessages').innerHTML = data.message;
		document.getElementById('ingamehelpcontainer').style.backgroundImage = "url('" + data.helpimg + "')";
		window.helper.nextpageid = data.nextpageid;
	},

	successPost: function successPost() {},

	errorAjax: function errorAjax(jqXHR, textStatus, errorThrown) {
		console.dir(jqXHR);
		console.dir(errorThrown);
		window.confirm.exception({ error: "AJAX error: " + textStatus }, function () {});
	}

};