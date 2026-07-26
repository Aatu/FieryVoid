/*
 * Recent Games window (games.php).
 *
 * Replaces the old RECENT ACTIVITY column, which un-hid a flat list of "name @ Turn N"
 * links in the games panel's third column. This is a modal on the same pattern as the
 * ladder window, listing every game across Fiery Void with activity in the last 7 days
 * (server side: DBManager::getRecentGames via recentgameslist.php).
 *
 * Fetch, filter and render are deliberately three separate steps: filtering runs over the
 * payload already in memory, so typing in the search box costs no network requests. If the
 * 7-day set ever outgrows the LIMIT in getRecentGames, only fetchOrRender/apply need to
 * move server-side — cardHtml and the control bar stay as they are.
 */
jQuery(function ($) {

    var STORE_KEY = "fv_recentGamesFilters";

    var recentGames = {

        games: [],
        loaded: false,
        state: { query: "", mine: false, ladder: false, active: false, sort: "recent" },

        init: function () {
            $(document).on("click", ".btn-recent-games", recentGames.open);
            $(document).on("click", "#rgClose", recentGames.close);
            $(document).on("click", "#rgClear, #rgClearInline", recentGames.clearFilters);
            $(document).on("click", "#rgRetry", recentGames.fetch);

            $(document).on("input", "#rgSearch", function () {
                clearTimeout(recentGames.debounce);
                recentGames.debounce = setTimeout(function () {
                    recentGames.state.query = $("#rgSearch").val() || "";
                    recentGames.render();
                }, 150);
            });

            $(document).on("click", ".fv-chip", function () {
                var key = $(this).data("filter");
                if (!key) return;
                recentGames.state[key] = !recentGames.state[key];
                $(this).attr("aria-pressed", recentGames.state[key] ? "true" : "false");
                recentGames.save();
                recentGames.render();
            });

            $(document).on("change", "#rgSort", function () {
                recentGames.state.sort = $(this).val();
                recentGames.save();
                recentGames.render();
            });

            //backdrop click, and Esc — the ladder window has no Esc handler, so this one
            //covers both windows
            $(document).on("click", "#rgModal", function (e) {
                if (e.target === document.getElementById("rgModal")) recentGames.close();
            });
            $(document).on("keydown", function (e) {
                if (e.key !== "Escape") return;
                if ($("#rgModal").hasClass("is-open")) recentGames.close();
                else if (typeof ladder !== "undefined" && $("#ladderModal").is(":visible")) ladder.close();
            });

            recentGames.restore();
        },

        /* ---------- state persistence -------------------------------------------- */

        /* Chips and sort persist, the search box does not: a remembered filter is a
           convenience, a remembered search string is a puzzle. The count line always
           reports the active filters so a persisted one is never invisible. */
        save: function () {
            try {
                window.localStorage.setItem(STORE_KEY, JSON.stringify({
                    mine: recentGames.state.mine,
                    ladder: recentGames.state.ladder,
                    active: recentGames.state.active,
                    sort: recentGames.state.sort
                }));
            } catch (e) { /* private mode / quota — filters just won't persist */ }
        },

        restore: function () {
            var saved = null;
            try { saved = JSON.parse(window.localStorage.getItem(STORE_KEY)); } catch (e) { saved = null; }
            if (!saved) return;

            recentGames.state.mine = !!saved.mine;
            recentGames.state.ladder = !!saved.ladder;
            recentGames.state.active = !!saved.active;
            if (saved.sort) recentGames.state.sort = saved.sort;

            $(".fv-chip").each(function () {
                var key = $(this).data("filter");
                $(this).attr("aria-pressed", recentGames.state[key] ? "true" : "false");
            });
            $("#rgSort").val(recentGames.state.sort);
        },

        /* ---------- open / close -------------------------------------------------- */

        open: function (e) {
            if (e) e.preventDefault();
            $("#rgModal").addClass("is-open");
            recentGames.fetch();
            $("#rgSearch").trigger("focus");
        },

        close: function () {
            $("#rgModal").removeClass("is-open");
            $(".btn-recent-games").trigger("focus");
        },

        /* ---------- fetch --------------------------------------------------------- */

        fetch: function () {
            recentGames.setState("Loading recent games…");

            ajaxInterface.getRecentGames(function (data) {
                recentGames.games = Array.isArray(data) ? data : [];
                recentGames.loaded = true;
                recentGames.render();
            }, function () {
                recentGames.loaded = false;
                recentGames.setState(
                    "Couldn't load recent games. Try again." +
                    '<br><button class="fv-btn" type="button" id="rgRetry">Try again</button>');
            });
        },

        setState: function (html) {
            $("#rgBody").html('<div class="fv-state">' + html + "</div>");
            $("#rgCount").text("");
            $("#rgClear").prop("hidden", true);
        },

        /* ---------- filter + sort ------------------------------------------------- */

        anyFilter: function () {
            var s = recentGames.state;
            return !!(String(s.query).trim() || s.mine || s.ladder || s.active);
        },

        apply: function () {
            var s = recentGames.state;
            var q = String(s.query).trim().toLowerCase();

            var rows = recentGames.games.filter(function (g) {
                if (s.mine && !g.mine) return false;
                if (s.ladder && !g.ladder) return false;
                if (s.active && g.status !== "ACTIVE") return false;
                //one box matches the game name AND the participant list: people type a
                //name without first deciding which field it belongs to
                if (q && ((g.name || "") + " " + (g.players || "")).toLowerCase().indexOf(q) === -1) return false;
                return true;
            });

            rows.sort(function (a, b) {
                if (s.sort === "turn") return (b.turn || 0) - (a.turn || 0);
                if (s.sort === "name") return String(a.name || "").localeCompare(String(b.name || ""));
                return String(b.lastActivity || "").localeCompare(String(a.lastActivity || ""));
            });

            return rows;
        },

        /* ---------- render -------------------------------------------------------- */

        render: function () {
            if (!recentGames.loaded) return;

            var rows = recentGames.apply();
            var filtered = recentGames.anyFilter();

            if (rows.length === 0) {
                $("#rgBody").html('<div class="fv-state">' + (filtered
                    ? 'No games match those filters.<br><button class="fv-btn" type="button" id="rgClearInline">Clear filters</button>'
                    : "No games have been active in the last 7 days.") + "</div>");
            } else {
                var html = "";
                for (var i = 0; i < rows.length; i++) html += recentGames.cardHtml(rows[i]);
                $("#rgBody").html(html);
            }

            $("#rgCount").text("Showing " + rows.length + " of " + recentGames.games.length + " games");
            $("#rgClear").prop("hidden", !filtered);
        },

        cardHtml: function (game) {
            var ended = (game.status !== "ACTIVE");

            /* Three rail states only — ended, yours, everyone else's. The rail answers
               "what is this game TO ME", and ladder is not an answer to that: the gold
               LADDER tag already says it on the same row, and a fourth colour competing
               with the green only made "am I in this one?" harder to scan (user 2026-07-26).
               Ended outranks yours because "this one is over" is the fact that changes what
               you'd do with the card, and the muted-red ENDED tag would read oddly beside a
               live green rail.

               The last branch is is-active, not the is-waiting grey the games panel uses:
               every non-ended row in THIS window is an ACTIVE game, so that bucket is
               exactly "someone else's live game" and deserves a live colour. */
            var cls = ended ? "is-done" : (game.mine ? "is-mine" : "is-active");

            var tags = "";
            if (game.mine) tags += gamedata.tagHtml("you", "You");
            if (game.ladder) tags += gamedata.tagHtml("ladder", "Ladder");
            //tac_game.status is ACTIVE / SURRENDERED / LOBBY; lobbies never reach this
            //list (the query requires turn > 0), so anything not ACTIVE has ended
            if (ended) tags += gamedata.tagHtml("done", "Ended");

            var bits = [];
            if (game.turn > 0) bits.push("TURN " + game.turn);
            bits.push(game.playerCount + "/" + game.slots + " PLAYERS");
            if (game.map) bits.push(gamedata.mapLabel(game.map));
            if (game.rules && game.rules.length) bits = bits.concat(game.rules);

            var name = game.name || "Unnamed game";
            var players = game.players || "No players recorded";

            return '<a class="fv-card ' + cls + '" href="game.php?gameid=' + game.id +
                '" title="' + gamedata.escapeHtml(name + " — " + players) + '">' +
                '<span class="fv-card-top">' +
                '<span class="fv-card-name">' + gamedata.escapeHtml(name) + "</span>" + tags +
                "</span>" +
                '<span class="fv-card-data">' + gamedata.escapeHtml(bits.join(" · ")) + "</span>" +
                '<span class="fv-card-players">' + gamedata.escapeHtml(players) + "</span>" +
                "</a>";
        },

        clearFilters: function () {
            recentGames.state.query = "";
            recentGames.state.mine = false;
            recentGames.state.ladder = false;
            recentGames.state.active = false;

            $("#rgSearch").val("");
            $(".fv-chip").attr("aria-pressed", "false");
            recentGames.save();
            recentGames.render();
            $("#rgSearch").trigger("focus");
        }
    };

    window.recentGames = recentGames;
    recentGames.init();
});
