jQuery(function ($) {
    /* Server strings (usernames, game names) are interpolated into HTML templates below.
       gamedata.escapeHtml is not reachable here — games.js is not loaded on
       creategame.php, which also opens this window — so the window carries its own. */
    function esc(value) {
        return String(value == null ? "" : value)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#39;");
    }

    var ladder = {
        myUsername: "Me",     // Default fallback
        oppUsername: "Opponent", // Default fallback
        myPoints: 0,
        oppPoints: 0,
        standingsCount: "",   // redisplayed by hideHistory when the history view closes

        init: function () {
            // Attach event listeners
            $(document).on("click", ".btn-ladder", ladder.open);
            $(document).on("click", ".close-ladder", ladder.close);
            $(document).on("click", "#btnCalculate", ladder.calculate);
            $(document).on("click", "#btnRegisterLadder", ladder.register);
            $(document).on("click", "#btnRemoveAccount", ladder.removeAccount);
            $(document).on("click", "#btnPopulateSlots", ladder.populateSlots);
            $(document).on("click", "#btnHistoryBack", ladder.hideHistory);

            // Mousewheel support for Opponent Rating
            // Mousewheel support for Opponent Rating
            // Use vanilla JS to ensure non-passive listener (so preventDefault works)
            var ratingInput = document.getElementById("calcOppRating");
            if (ratingInput) {
                // Remove jQuery handler if any (defensive)
                $(ratingInput).off("wheel");

                // Use onwheel property to enforce singleton handler (prevents duplicate firing)
                ratingInput.onwheel = function (e) {
                    if (document.activeElement === this) {
                        e.preventDefault();
                        e.stopPropagation();
                        var $this = $(this);
                        var val = parseInt($this.val()) || 100;
                        if (e.deltaY < 0) {
                            $this.val(val + 1);
                        } else {
                            $this.val(val - 1);
                        }
                    }
                };
            }

            // Click to copy rating (delegated to document/table)
            $(document).on("click", "#ladderTable .ladder-row", function () {
                var r = $(this).data("rating");
                var pid = $(this).data("playerid");
                // The first cell contains the name
                var name = $(this).find("td:first").text();

                // If calculator is hidden (Games List), show history instead of copying rating
                if ($(".ladder-calculator").is(":hidden")) {
                    ladder.showHistory(pid, name);
                    return;
                }

                if (r) {
                    // Prevent copying own rating to opponent field
                    if ($(this).data("isme")) return;

                    $("#calcOppRating").val(r);
                    if (name) ladder.oppUsername = name;

                    // Visual feedback
                    $("#calcOppRating").css("background-color", "#7298a7");
                    setTimeout(function () { $("#calcOppRating").css("background-color", ""); }, 200);
                }
            });

            // Close on outside click
            $(window).on("click", function (event) {
                if (event.target == document.getElementById("ladderModal")) {
                    ladder.close();
                }
            });

            //console.log("Ladder JS Initialized");
        },

        open: function (e) {
            e.preventDefault(); // Prevent default anchor behavior
            $("#ladderModal").show();
            $("#btnPopulateSlots").hide(); // Reset check

            var showCalc = true;
            if (e && e.currentTarget) {
                var val = $(e.currentTarget).data("show-calc");
                if (val === false) showCalc = false;
            }

            if (showCalc) {
                $(".ladder-calculator").show();
                $(".ladder-standings").css("border-right", "none");
            } else {
                $(".ladder-calculator").hide();
            }

            ladder.fetchStandings();
        },

        close: function () {
            $("#ladderModal").hide();
        },

        register: function () {
            ajaxInterface.ajaxWithRetry({
                type: 'POST',
                url: 'ladderstandings.php',
                dataType: 'json',
                data: JSON.stringify({
                    action: "register"
                }),
                contentType: 'application/json',
                success: function (data) {
                    if (data.success) {
                        if (typeof confirm !== 'undefined' && confirm.warning) {
                            confirm.warning("Registration successful!");
                        } else {
                            alert("Registration successful!");
                        }
                        ladder.fetchStandings();
                    } else {
                        alert("Registration failed: " + (data.error || "Unknown error"));
                    }
                },
                error: function (xhr, status, error) {
                    alert("Error: " + error);
                }
            });
        },

        removeAccount: function () {
            var msg = "Are you sure you wish to remove you account from the Online Ladder, this will reset your ranking to 100 if you re-register";

            var doRemove = function () {
                ajaxInterface.ajaxWithRetry({
                    type: 'POST',
                    url: 'ladderstandings.php',
                    dataType: 'json',
                    data: JSON.stringify({
                        action: "remove"
                    }),
                    contentType: 'application/json',
                    success: function (data) {
                        if (data.success) {
                            if (typeof confirm !== 'undefined' && confirm.warning) {
                                confirm.warning("Account removed from ladder.");
                            } else {
                                alert("Account removed from ladder.");
                            }
                            ladder.fetchStandings();
                        } else {
                            alert("Removal failed: " + (data.error || "Unknown error"));
                        }
                    },
                    error: function (xhr, status, error) {
                        alert("Error: " + error);
                    }
                });
            };

            if (typeof confirm !== 'undefined' && confirm.confirm) {
                confirm.confirm(msg, doRemove);
            } else {
                if (window.confirm(msg)) {
                    doRemove();
                }
            }
        },

        fetchStandings: function () {
            $("#ladderTable tbody").html('<tr><td colspan="5" class="ladder-state">Loading standings&hellip;</td></tr>');
            ladder.setCount("Loading standings…");

            if (typeof ajaxInterface === 'undefined') {
                console.error("ajaxInterface not defined");
                return;
            }

            ajaxInterface.callServer("Manager::getLadderStandings", [], function (response) {
                var data = response.standings || response;
                var currentUser = response.currentUser || null;

                if (currentUser) {
                    $("#calcMyRating").text(currentUser.rating);
                    if (currentUser.username) ladder.myUsername = currentUser.username;

                    // Toggle buttons based on registration
                    if (currentUser.isRegistered) {
                        $("#btnRegisterLadder").prop("disabled", true).css("opacity", 0.5).css("cursor", "default");
                        $("#btnRemoveAccount").show();
                    } else {
                        $("#btnRegisterLadder").prop("disabled", false).css("opacity", 1).css("cursor", "pointer");
                        $("#btnRemoveAccount").hide();
                    }
                }

                var html = "";
                var sorted = [];
                // data might be object? check.
                if (Array.isArray(data)) {
                    sorted = data;
                } else {
                    for (var key in data) {
                        if (data.hasOwnProperty(key)) sorted.push(data[key]);
                    }
                }

                // Sanitize and sort if needed (PHP should sort, but ensuring types)
                for (var i = 0; i < sorted.length; i++) {
                    sorted[i].wins = parseInt(sorted[i].wins) || 0;
                    sorted[i].losses = parseInt(sorted[i].losses) || 0;
                    sorted[i].rating = parseInt(sorted[i].rating) || 100;
                    sorted[i].ratio = (sorted[i].wins / (sorted[i].wins + sorted[i].losses) * 100) || 0;
                }

                /* The cells used to carry their padding, borders and colours as inline
                   styles, which no stylesheet could reach. They are classes now
                   (ladder.css) — same look as a Recent Games card row. */
                for (var i = 0; i < sorted.length; i++) {
                    var p = sorted[i];
                    var name = p.username || "Player " + p.playerid;
                    var isMe = !!(currentUser && p.playerid == currentUser.id);

                    var rowClass = "ladder-row";
                    var rowTitle = "Click to copy rating to calculator";

                    if (isMe) {
                        rowClass += " ladder-row-highlight";
                        ladder.myUsername = name; // Ensure my username is set from list if possible
                        // the row stays clickable: that is how you open your own history
                        rowTitle = "You";
                    }

                    html += `<tr data-rating="${p.rating}" data-playerid="${p.playerid}" data-isme="${isMe}" class="${rowClass}" title="${esc(rowTitle)}">
                        <td class="ladder-cell ladder-name-cell">${esc(name)}</td>
                        <td class="ladder-rating-cell">${p.rating}</td>
                        <td class="ladder-cell-center ladder-num">${p.wins}</td>
                        <td class="ladder-cell-center ladder-num">${p.losses}</td>
                        <td class="ladder-cell-center ladder-num">${p.ratio.toFixed(1)}%</td>
                    </tr>`;
                }

                if (sorted.length === 0) {
                    html = '<tr><td colspan="5" class="ladder-state">No ranked players yet. Register to join!</td></tr>';
                }

                $("#ladderTable tbody").html(html);

                ladder.standingsCount = sorted.length + (sorted.length === 1 ? " ranked player" : " ranked players");
                ladder.setCount(ladder.standingsCount);
            });
        },

        /* The count line under the control row, mirroring the Recent Games window's. */
        setCount: function (text) {
            $("#ladderCount").text(text);
        },

        calculate: function () {
            var myRating = parseInt($("#calcMyRating").text()) || 100;
            var oppRating = parseInt($("#calcOppRating").val()) || 100;
            var gamePoints = parseInt($("#calcGamePoints").val()) || 0;

            var diff = Math.abs(myRating - oppRating);
            var bonusPoints = Math.round(gamePoints * (diff / 100)) + gamePoints;
            var result = "";

            ladder.myPoints = gamePoints;
            ladder.oppPoints = gamePoints;

            /* Classes, not inline colours — the green branch used to read
               `style="color:00ff00"`, a missing `#`, so it never rendered green at all. */
            if (myRating < oppRating) {
                result = `<span class="bonus-text-yellow">You receive ${bonusPoints} points</span>`;
                result += `<span class="bonus-text-green"><br>Your opponent receives ${gamePoints} points</span><br><span class="bonus-subtext">(${diff}% bonus)</span>`;
                ladder.myPoints = bonusPoints;
            } else if (myRating > oppRating) {
                result = `<span class="bonus-text-yellow">Your opponent receives ${bonusPoints} points</span>`;
                result += `<span class="bonus-text-green"><br>You receive ${gamePoints} points</span><br><span class="bonus-subtext">(${diff}% bonus)</span>`;
                ladder.oppPoints = bonusPoints;
            } else {
                result = "Ratings are equal. No handicap.";
            }

            $("#calcResult").html(result);

            // Show populate button if we are in Create Game context (check if createGame exists)
            if (typeof createGame !== 'undefined') {
                $("#btnPopulateSlots").show();
            }
        },

        populateSlots: function () {
            if (typeof createGame === 'undefined') {
                alert("This feature is only available on the Create Game screen.");
                return;
            }

            var foundMySlot = false;
            var foundOppSlot = false;

            // Heuristic: Slot 1 -> Me, Slot 2 -> Opponent
            // Assumes createGame.slots array exists and has correct team data
            for (var i = 0; i < createGame.slots.length; i++) {
                var slot = createGame.slots[i];
                if (slot.team == 1 && !foundMySlot) {
                    slot.name = ladder.myUsername;
                    slot.points = ladder.myPoints;
                    slot.isLadderPopulated = true;
                    foundMySlot = true;
                } else if (slot.team == 2 && !foundOppSlot) {
                    slot.name = ladder.oppUsername;
                    slot.points = ladder.oppPoints;
                    slot.isLadderPopulated = true;
                    foundOppSlot = true;
                }
            }

            // Redundancy in case loop didn't match (e.g. empty slots array initially?)
            // createGame.slots typically pre-populated with 2 slots.

            createGame.refreshSlotsUI();
            createGame.drawMapPreview();

            if (typeof confirm !== 'undefined' && confirm.warning) {
                confirm.warning("Slots populated!");
            } else {
                alert("Slots populated!");
            }
            ladder.close();
        },

        showHistory: function (playerid, name) {
            $("#ladderStandingsPane").hide();
            $("#ladderHistoryPane").show();
            $("#historyPlayerName").text(name);
            $("#ladderHistoryTable tbody").html('<tr><td colspan="4" class="ladder-state">Loading match history&hellip;</td></tr>');
            ladder.setCount("Loading match history…");

            ajaxInterface.callServer("Manager::getLadderHistory", [playerid], function (data) {
                var html = "";
                var count = (data && data.length) || 0;

                if (count === 0) {
                    html = '<tr><td colspan="4" class="ladder-state">No match history found.</td></tr>';
                } else {
                    for (var i = 0; i < data.length; i++) {
                        var game = data[i];
                        var statusClass = (game.status === 'WIN') ? 'ladder-result--win'
                            : ((game.status === 'LOSS') ? 'ladder-result--loss' : 'ladder-result--draw');
                        // Opponent name or "Unknown"
                        var oppName = game.opponent_name || "Unknown";

                        html += `<tr>
                            <td class="ladder-cell ladder-name-cell">${esc(game.name)} (#${game.id})</td>
                            <td class="ladder-cell">${esc(oppName)}</td>
                            <td class="ladder-cell-center ladder-result ${statusClass}">${esc(game.status)}</td>
                            <td class="ladder-cell-center"><a href="game.php?gameid=${game.id}" class="btn-ladder-inline" target="_blank">View Game</a></td>
                        </tr>`;
                    }
                }
                $("#ladderHistoryTable tbody").html(html);
                if(count === 0) {
                    ladder.setCount(count + " matches available to review");
                }else{
                    ladder.setCount(count + (count === 1 ? " match available to review" : " matches available to review"));                    
                }    
            });
        },

        hideHistory: function () {
            $("#ladderHistoryPane").hide();
            $("#ladderStandingsPane").show();
            ladder.setCount(ladder.standingsCount);
        }
    };

    window.ladder = ladder;
    ladder.init();
});
