"use strict";

$(window).resize(function () {
    getWindowDimensions();
    resizeGame();
});

/*
 window.requestAnimFrame = (function(){
    return  window.requestAnimationFrame       || 
            window.webkitRequestAnimationFrame || 
            window.mozRequestAnimationFrame    || 
            window.oRequestAnimationFrame      || 
            window.msRequestAnimationFrame     || 
            function( callback ){
            window.setTimeout(callback, 1000 / 60);
            };
})();
*/

function getWindowDimensions() {

    gamedata.gamewidth = window.innerWidth - 1;
    gamedata.gameheight = window.innerHeight - 1;
}

function resizeGame() {
    $("#hexgrid").attr("width", gamedata.gamewidth);
    $("#hexgrid").attr("height", gamedata.gameheight);
    $("#EWindicators").attr("width", gamedata.gamewidth);
    $("#EWindicators").attr("height", gamedata.gameheight);
    $("#effects").attr("width", gamedata.gamewidth);
    $("#effects").attr("height", gamedata.gameheight);
    $("#pagecontainer").css("height", gamedata.gameheight + "px");
    //$("#ships").attr("width", gamedata.gamewidth);
    //$("#ships").attr("height", gamedata.gameheight);

}

jQuery(function () {
    /*
    $("#zoomin").on("click", zooming.zoomin);
    $("#zoomout").on("click", zooming.zoomout);
    $("#pagecontainer").on("mousedown", scrolling.mousedown);
    $("#pagecontainer").on("mouseup", scrolling.mouseup);
    $("#pagecontainer").on("mousemove", scrolling.mousemove);
    $("#pagecontainer").on("mouseout", scrolling.mouseout);
    $("#pagecontainer").on("click", hexgrid.onHexClicked);
    $(document).on("keyup", windowEvents.onKeyUp);
    */

    $(".committurn").on("click", gamedata.onCommitClicked);
    $(".cancelturn").on("click", gamedata.onCancelClicked);
    /*
    //	$("#helphide").on("click", window.helper.onClickHelpHide);
    //	$("#autocommit").on("click", window.helper.onClickAutoCommit);
    //	$(".ingamehelppanel").on("click", window.helper.onClickInHelp);
     hookEvent('pagecontainer', 'mousewheel', zooming.mouseWheel);
    document.onkeydown = function( event ){
        event = event || window.event;
        
    }
    */
});