"use strict";


function getWindowDimensions() {

    gamedata.gamewidth = window.innerWidth - 1;
    gamedata.gameheight = window.innerHeight - 1;
}

jQuery(function () {

    $(".committurn").on("click", gamedata.onCommitClicked);
    $(".cancelturn").on("click", gamedata.onCancelClicked);
});

// Restored-page freshness (game page).
// game.php bakes its gamedata JSON into the page at server-render time and
// parses it once inside the window "load" handler. That handler does not re-fire
// when the browser restores the page rather than freshly navigating to it, so the
// page comes back showing the stale render-time snapshot.
//
// Two distinct restore paths, both handled here:
//   1. BFCache restore (back/forward button, reopen closed tab) — page thawed
//      from memory; pageshow fires with event.persisted === true.
//   2. Session restore after a browser/computer restart — the in-memory BFCache
//      is gone, so Chrome reloads the tab from the HTTP DISK cache. This fires
//      pageshow with persisted === FALSE, and (because game.php sends no
//      Cache-Control, see session_cache_limiter('') in global.php) the cached
//      HTML — with its stale inline JSON — is replayed without hitting the server.
//      Navigation Timing reports this as a "back_forward" navigation type.
//
// In both cases the polling loop has also usually decayed to its 30-minute
// interval (and its timer was suspended while away), so fresh data can be a long
// time coming on its own. On a restore: force one immediate fetch, then restart
// the loop with its decay reset if the game is still live.
function fvGetNavigationType() {
    // Prefer the modern Navigation Timing Level 2 API.
    try {
        var navEntries = performance.getEntriesByType && performance.getEntriesByType("navigation");
        if (navEntries && navEntries.length) {
            return navEntries[0].type; // "navigate" | "reload" | "back_forward" | "prerender"
        }
    } catch (e) { /* fall through */ }
    // Legacy fallback (deprecated but widely present).
    if (performance.navigation) {
        // TYPE_BACK_FORWARD === 2
        return performance.navigation.type === 2 ? "back_forward" : "navigate";
    }
    return "navigate";
}

function fvRefreshAfterRestore() {
    if (typeof ajaxInterface === "undefined") return;   // not on a polling page

    ajaxInterface.stopPolling();                        // clear any suspended/decayed timer
    ajaxInterface.submiting = false;                    // a frozen/aborted in-flight XHR never completed
    ajaxInterface.requestGamedata();                    // one guaranteed catch-up fetch

    if (typeof gamedata !== "undefined" && gamedata.waiting) {
        ajaxInterface.startPollingGamedata();           // resume live polling, decay reset
    }
}

window.addEventListener("pageshow", function (event) {
    // persisted === true  → BFCache restore.
    // persisted === false → could be a fresh load OR a disk-cache session restore;
    //                       the navigation type disambiguates ("back_forward").
    if (event.persisted || fvGetNavigationType() === "back_forward") {
        fvRefreshAfterRestore();
    }
});