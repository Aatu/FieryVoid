"use strict";


function getWindowDimensions() {

    gamedata.gamewidth = window.innerWidth - 1;
    gamedata.gameheight = window.innerHeight - 1;
}

jQuery(function () {

    $(".committurn").on("click", gamedata.onCommitClicked);
    $(".cancelturn").on("click", gamedata.onCancelClicked);
});