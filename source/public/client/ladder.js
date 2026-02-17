jQuery(function ($) {
    var ladder = {
        myUsername: "Me",     // Default fallback
        oppUsername: "Opponent", // Default fallback
        myPoints: 0,
        oppPoints: 0,

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
            $("#ladderTable tbody").html('<tr><td colspan="5" style="text-align:center;">Loading...</td></tr>');

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

                for (var i = 0; i < sorted.length; i++) {
                    var p = sorted[i];
                    var name = p.username || "Player " + p.playerid;

                    var rowStyle = "";
                    var rowClass = "ladder-row";
                    var rowTitle = "Click to copy rating to calculator";

                    if (currentUser && p.playerid == currentUser.id) {
                        rowStyle = "background-color: rgba(33, 90, 122, 0.3);";
                        ladder.myUsername = name; // Ensure my username is set from list if possible
                        // rowClass = ""; // REMOVED: Allow clicking for history
                        rowTitle = "You";
                    }

                    html += `<tr style="${rowStyle}" data-rating="${p.rating}" data-playerid="${p.playerid}" data-isme="${(p.playerid == currentUser.id)}" class="${rowClass}" title="${rowTitle}">
                        <td style="padding:8px; border-bottom:1px solid #333;">${name}</td>
                        <td style="text-align:center; padding:8px; border-bottom:1px solid #333; font-weight:bold; color:#8bcaf2;">${p.rating}</td>
                        <td style="text-align:center; padding:8px; border-bottom:1px solid #333;">${p.wins}</td>
                        <td style="text-align:center; padding:8px; border-bottom:1px solid #333;">${p.losses}</td>
                        <td style="text-align:center; padding:8px; border-bottom:1px solid #333;">${p.ratio.toFixed(1)}%</td>
                    </tr>`;
                }

                if (sorted.length === 0) {
                    html = '<tr><td colspan="5" style="text-align:center;">No ranked players yet. Register to join!</td></tr>';
                }

                $("#ladderTable tbody").html(html);
            });
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

            if (myRating < oppRating) {
                result = `<span style="color:#ffcc00;">You receive ${bonusPoints} points</span>`;
                result += `<span style="color:00ff00;"><br>Your opponent receives ${gamePoints} points</span><br><span style="font-size:11px; color:#aaa;">(${diff}% bonus)</span>`;
                ladder.myPoints = bonusPoints;
            } else if (myRating > oppRating) {
                result = `<span style="color:#ffcc00;">Your opponent receives ${bonusPoints} points</span>`;
                result += `<span style="color:00ff00;"><br>You receive ${gamePoints} points</span><br><span style="font-size:11px; color:#aaa;">(${diff}% bonus)</span>`;
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
            $("#ladderHistoryTable tbody").html('<tr><td colspan="4" style="text-align:center;">Loading...</td></tr>');

            ajaxInterface.callServer("Manager::getLadderHistory", [playerid], function (data) {
                var html = "";
                if (!data || data.length === 0) {
                    html = '<tr><td colspan="4" style="text-align:center;">No match history found.</td></tr>';
                } else {
                    for (var i = 0; i < data.length; i++) {
                        var game = data[i];
                        var statusColor = (game.status === 'WIN') ? '#00ff00' : ((game.status === 'LOSS') ? '#ff0000' : '#aaa');
                        // Opponent name or "Unknown"
                        var oppName = game.opponent_name || "Unknown";

                        html += `<tr>
                            <td class="ladder-cell">${game.name} (#${game.id})</td>
                            <td class="ladder-cell">${oppName}</td>
                            <td class="ladder-cell-center" style="font-weight:bold; color:${statusColor};">${game.status}</td>
                            <td class="ladder-cell-center"><a href="game.php?gameid=${game.id}" class="btn-ladder-inline" target="_blank">View Game</a></td>
                        </tr>`;
                    }
                }
                $("#ladderHistoryTable tbody").html(html);
            });
        },

        hideHistory: function () {
            $("#ladderHistoryPane").hide();
            $("#ladderStandingsPane").show();
        }
    };

    window.ladder = ladder;
    ladder.init();
});
