window.replayUI = (function(){

    function ReplayUI() {

    }

    ReplayUI.prototype.activate = function() {
        console.log("activate");
        gamedata.replay = true;
        $("#replayUI").addClass('active').removeClass('inactive');
        webglScene.receiveGamedata(gamedata);
    };

    ReplayUI.prototype.deactivate = function() {
        $("#replayUI").addClass('inactive').removeClass('active');
        gamedata.replay = false;
        webglScene.receiveGamedata(gamedata);
    };

    ReplayUI.prototype.forward = function() {
        webglScene.customEvent("ReplayForward");
    };

    return new ReplayUI();
})();

jQuery(function(){
    $("#replayUI #activateReplay").on("click", replayUI.activate);
    $("#replayUI #deactivateReplay").on("click", replayUI.deactivate);
    $("#replayUI #forward").on("click", replayUI.forward);
});
