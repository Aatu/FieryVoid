"use strict";


function getWindowDimensions() {

    gamedata.gamewidth = window.innerWidth - 1;
    gamedata.gameheight = window.innerHeight - 1;
}

jQuery(function () {

    $(".committurn").on("click", gamedata.onCommitClicked);
    $(".cancelturn").on("click", gamedata.onCancelClicked);
});

// BFCache restore freshness (game page).
// game.php bakes its gamedata JSON into the page at server-render time and
// parses it once inside the window "load" handler. That handler does not
// re-fire when Chrome restores a frozen page from the back/forward cache
// (e.g. session restore on browser startup), so the page comes back showing
// the stale render-time snapshot. The polling loop has also usually decayed to
// its 30-minute interval (and its setTimeout was suspended while frozen), so it
// can be a long time before fresh data arrives on its own.
// On a persisted restore: force one immediate fetch, then restart the loop with
// its decay reset if the game is still live.
window.addEventListener("pageshow", function (event) {
    if (!event.persisted) return;                       // only BFCache restores
    if (typeof ajaxInterface === "undefined") return;   // not on a polling page

    ajaxInterface.stopPolling();                        // clear any suspended/decayed timer
    ajaxInterface.submiting = false;                    // a frozen in-flight XHR never completed
    ajaxInterface.requestGamedata();                    // one guaranteed catch-up fetch

    if (typeof gamedata !== "undefined" && gamedata.waiting) {
        ajaxInterface.startPollingGamedata();           // resume live polling, decay reset
    }
});