"use strict";

jQuery(function ($) {
    $("#backgroundSelect").on("change", createGame.mapSelect);

    // Bind global Add Team button
    $("#addTeamBtn").on("click", function () {
        createGame.addTeam();
    });

    createGame.mapSelect();

    $("body").on("change", "input", createGame.inputChange);
    $("body").on("change", "select", createGame.inputChange);
    $("body").on("focus", "input", createGame.inputFocus);

    // Mousewheel support for number inputs
    // We use a non-passive listener on the document to ensure we can preventDefault() the scroll
    document.addEventListener("wheel", function (e) {
        // Support standard number inputs AND our special .points text input
        const isNumberInput = (e.target.tagName === 'INPUT' && e.target.type === 'number');
        const isPointsInput = (e.target.tagName === 'INPUT' && $(e.target).hasClass('points'));

        if (!isNumberInput && !isPointsInput) return;
        if (document.activeElement !== e.target) return; // Only if focused

        e.preventDefault();

        // Determine direction
        const delta = e.deltaY;
        const input = $(e.target);
        const step = parseFloat(input.attr("step")) || 1;
        let val = parseFloat(input.val()) || 0;

        if (delta > 0) { // Scrolling down -> decrement
            val -= step;
        } else { // Scrolling up -> increment
            val += step;
        }

        // Respect min/max if present
        const min = input.attr("min");
        const max = input.attr("max");

        if (min !== undefined && val < parseFloat(min)) val = parseFloat(min);
        if (max !== undefined && val > parseFloat(max)) val = parseFloat(max);

        input.val(val);
        input.trigger("change"); // Update model

    }, { passive: false });

    // Scenario Custom Input Logic
    const scenarioCustoms = [
        { id: 'req', trigger: 'Other' },
        { id: 'tier', trigger: 'Other' },
        { id: 'enhancements', trigger: 'Up to X points' },
        { id: 'victory', trigger: 'Other' }
    ];

    scenarioCustoms.forEach(item => {
        $(`#${item.id}`).on('change', function () {
            const val = $(this).val();
            if (val === item.trigger) {
                $(`#${item.id}_custom`).show().focus();
            } else {
                $(`#${item.id}_custom`).hide();
            }
        });
        // Init state
        if ($(`#${item.id}`).val() === item.trigger) {
            $(`#${item.id}_custom`).show();
        }
    });

    // UNLIMITED POINTS LOGIC
    $("#unlimitedPointsCheck").on("change", function () {
        const isUnlimited = $(this).is(":checked");

        if (isUnlimited) {
            $(".points").hide();
            $(".unlimited-label").show(); // Show our custom label

            // Force update data model for all slots
            createGame.slots.forEach(slot => {
                slot.points = -1;
            });

        } else {
            $(".points").show();
            $(".unlimited-label").hide();

            // Reset visual and model
            $(".points").each(function () {
                $(this).val("3000"); // Standard default visual
                $(this).trigger("change"); // Trigger change to update model
            });
        }
    });

    // BLOCK INVALID CHARS FOR ENHANCEMENTS (INTEGERS ONLY)
    $("#enhancements_custom").on("keydown", function (e) {
        // Prevent characters that are invalid for a positive integer: 'e', 'E', '.', '+', '-'
        if (["e", "E", "+", "-", "."].includes(e.key)) {
            e.preventDefault();
        }
    });

    // Use body delegation for dynamic elements if needed, though structure suggests static buttons for adding slots
    $(".addslotbutton").on("click", createGame.createNewSlot);
    // Delegate close button click since slots are dynamic
    // Delegate close button click to a static parent since slots and their containers are dynamic
    $("#teamsContainer").on("click", ".close", createGame.removeSlot);
    // Delegate remove team button
    $("#teamsContainer").on("click", ".remove-team-btn", function () {
        console.log("Remove Team Clicked");
        const rawId = $(this).closest(".team-section").data("team-id");
        const teamId = parseInt(rawId);
        console.log("Target Team ID:", teamId);
        createGame.removeTeam(teamId);
    });

    let allowSubmit = false;

    // Only set allowSubmit on real mouse or touch interaction
    $("#createGameForm button[type='submit']").on("mousedown touchstart", function () {
        allowSubmit = true;
    });

    $("#createGameForm").on("submit", function (e) {
        if (!allowSubmit) {
            e.preventDefault(); // Block submission from pressing Enter
            return false;
        }

        // Call your original setData function before submitting
        createGame.setData();

        allowSubmit = false; // Reset flag after submission
    });

    // Bind checkbox events
    $("#mapDimensionsSelect").on("change", createGame.onMapDimensionsChange);

    $("#movementcheck").on("click", createGame.doMovementCheck);
    $("#desperatecheck").on("click", createGame.doDesperateCheck);
    $("#terraincheck").on("click", createGame.doTerrainCheck);
    $("#allowMinesCheck").on("click", createGame.doAllowMinesCheck);
    $("#friendlyFireCheck").on("click", createGame.doFriendlyFireCheck);
    $("#laddercheck").on("click", createGame.doLadderCheck);

    createGame.refreshSlotsUI();
    createGame.onMapDimensionsChange(); // Run on load
    createGame.drawMapPreview();
});

window.createGame = {
    gamespace_data: { width: 42, height: 30 },
    rules: {},
    slots: [
        { id: 1, team: 1, name: "Team 1", points: 3500, depx: -21, depy: 0, deptype: "box", depwidth: 10, depheight: 30, depavailable: 1 },
        { id: 2, team: 2, name: "Team 2", points: 3500, depx: 21, depy: 0, deptype: "box", depwidth: 10, depheight: 30, depavailable: 1 }
    ],
    slotid: 2,

    mapSelect: function mapSelect() {
        $("#default_option").remove();
        const val = $("#backgroundSelect").val();
        $("body").css("background-image", "url(img/maps/" + val + ")");
    },

    inputFocus: function inputFocus(e) {
        const input = $(this);
        const value = input.val();
        input.data("oldvalue", value);
    },

    inputChange: function inputChange(e) {

        const input = $(this);
        const value = input.val();
        const inputname = input.attr("name");

        if (input.data("validation")) {
            const patt = new RegExp(input.data("validation"));
            if (value.length == 0 || !patt.test(value)) {
                input.val(input.data("oldvalue"));
                return;
            }
        }

        if (inputname == "spacex") {
            createGame.gamespace_data.width = parseInt(value);
            $("#mapDimensionsSelect").val("custom");
            createGame.drawMapPreview();
            return;
        }

        if (inputname == "spacey") {
            createGame.gamespace_data.height = parseInt(value);
            $("#mapDimensionsSelect").val("custom");
            createGame.drawMapPreview();
            return;
        }

        // Find parent slot
        const slot = input.closest(".slot");
        if (slot.length === 0) return; // Not inside a slot

        const slotId = slot.data("slotid");
        const data = createGame.getSlotData(slotId);

        if (!data) return;

        data[inputname] = value;

        if (inputname == "deptype") {
            // Logic for deptype if we re-enable it later
        }

        createGame.drawMapPreview();
    },

    drawMapPreview: function drawMapPreview() {
        const canvas = document.getElementById("mapPreview");
        if (!canvas) return;
        const ctx = canvas.getContext("2d");
        if (!ctx) return;

        const isLimited = $("#mapDimensionsSelect").val() !== "unlimited";

        // Use fixed width/height if unlimited is selected
        const mapWidth = isLimited ? (createGame.gamespace_data.width || 1) : 84;
        const mapHeight = isLimited ? (createGame.gamespace_data.height || 1) : 60;

        // Clear canvas
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // Background
        ctx.fillStyle = "#000000";
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        // Margins and scale
        const margin = 10;
        const scaleX = (canvas.width - margin * 2) / mapWidth;
        const scaleY = (canvas.height - margin * 2) / mapHeight;
        const scale = Math.min(scaleX, scaleY); // Uniform scale

        // Calculate offset to center the map in the canvas
        const offsetX = (canvas.width - mapWidth * scale) / 2;
        const offsetY = (canvas.height - mapHeight * scale) / 2;

        // Draw Map Boundary
        ctx.fillStyle = "#050a10";
        ctx.fillRect(offsetX, offsetY, mapWidth * scale, mapHeight * scale);

        ctx.strokeStyle = "#deebffaf";
        ctx.lineWidth = 2;
        ctx.strokeRect(offsetX, offsetY, mapWidth * scale, mapHeight * scale);

        // Grid lines / Center lines
        ctx.save();
        ctx.globalAlpha = 0.4;
        ctx.strokeStyle = "#496791";
        ctx.lineWidth = 1;
        ctx.setLineDash([4, 4]);

        const centerX = offsetX + 6 + (mapWidth / 2) * scale;
        const centerY = offsetY + (mapHeight / 2) * scale;

        // Vertical Center Line
        ctx.beginPath();
        ctx.moveTo(centerX, offsetY);
        ctx.lineTo(centerX, offsetY + mapHeight * scale);
        ctx.stroke();

        // Horizontal Center Line
        ctx.beginPath();
        ctx.moveTo(offsetX, centerY);
        ctx.lineTo(offsetX + mapWidth * scale, centerY);
        ctx.stroke();

        ctx.restore();

        // Draw deployment zones
        // Iterate data model directly to ensure we catch all teams even if DOM is lagging
        createGame.slots.forEach(function (slot) {
            const data = slot;
            const team = data.team;

            const x = parseInt(data.depx) || 0;
            const y = parseInt(data.depy) || 0;
            const w = parseInt(data.depwidth) || 0;
            const h = parseInt(data.depheight) || 0;

            const colors = [
                { fill: "rgba(50, 200, 50, 0.4)", stroke: "#66ff66" }, // Green
                { fill: "rgba(200, 50, 50, 0.4)", stroke: "#ff6666" }, // Red
                { fill: "rgba(50, 50, 200, 0.4)", stroke: "#6666ff" }, // Blue
                { fill: "rgba(200, 200, 50, 0.4)", stroke: "#ffff66" }, // Yellow
                { fill: "rgba(50, 200, 200, 0.4)", stroke: "#66ffff" }, // Cyan
                { fill: "rgba(200, 50, 200, 0.4)", stroke: "#ff66ff" }  // Magenta
            ];

            const colorIdx = (team - 1) % colors.length;
            const style = colors[colorIdx];

            ctx.fillStyle = style.fill;
            ctx.strokeStyle = style.stroke;
            ctx.lineWidth = 1;

            // Adjust position to treat (x, y) as center
            // coordinate system: center of map is (0,0)
            // canvas origin is topleft

            const drawX = offsetX + (x - w / 2 + mapWidth / 2) * scale;
            const drawY = offsetY + ((mapHeight / 2) - y - (h / 2)) * scale;

            ctx.fillRect(drawX + 6, drawY, w * scale, h * scale);
            ctx.strokeRect(drawX + 6, drawY, w * scale, h * scale);

            // Draw slot number/TeamID
            ctx.save();
            ctx.fillStyle = "white";
            ctx.font = "bold 14px Arial";
            ctx.textAlign = "center";
            ctx.textBaseline = "middle";
            // Center of the box
            ctx.fillText(team, drawX + 6 + (w * scale) / 2, drawY + (h * scale) / 2);
            ctx.restore();
        });
    },

    doFlightCheck: function doFlightCheck(data) {
        var checkval = $("#flightSizeCheck:checked").val();
        if (checkval == "on") {
            createGame.variableFlights = 1;
        } else createGame.variableFlights = 0;
    },


    doDesperateCheck: function doDesperateCheck(data) {
        var checkval = $("#desperatecheck:checked").val();

        if (checkval == "on") {
            $("#desperateDropdown").show();
            var selectedValue = $("#desperateSelect").val();
            createGame.rules.desperate = parseInt(selectedValue, 10);

            $("#desperateSelect").off('change').on('change', function () {
                createGame.rules.desperate = parseInt($(this).val(), 10);
            });
        } else {
            $("#desperateDropdown").hide();
            delete createGame.rules.desperate;
        }
    },


    doTerrainCheck: function doTerrainCheck(data) {
        createGame.doAsteroidsCheck(data);
        createGame.doMoonsCheck(data);
    },

    doAsteroidsCheck: function () {
        var checkval = $("#terraincheck:checked").val();

        if (checkval == "on") {
            $("#asteroidsDropdown").show();
            var selectedValue = $("#asteroidsSelect").val();
            createGame.rules.asteroids = parseInt(selectedValue, 10);

            $("#asteroidsSelect").off('change').on('change', function () {
                createGame.rules.asteroids = parseInt($(this).val(), 10);
            });
        } else {
            $("#asteroidsDropdown").hide();
            delete createGame.rules.asteroids;
        }
    },


    doMoonsCheck: function () {
        const enabled = $("#terraincheck").is(":checked");

        if (enabled) {
            $("#moonsDropdown").show();

            const small = parseInt($("#moonsSmallSelect").val(), 10) || 0;
            const medium = parseInt($("#moonsMediumSelect").val(), 10) || 0;
            const large = parseInt($("#moonsLargeSelect").val(), 10) || 0;

            createGame.rules.moons = { small, medium, large };

            $("#moonsSmallSelect").off('change.moons').on('change.moons', function () {
                createGame.rules.moons.small = parseInt(this.value, 10) || 0;
            });
            $("#moonsMediumSelect").off('change.moons').on('change.moons', function () {
                createGame.rules.moons.medium = parseInt(this.value, 10) || 0;
            });
            $("#moonsLargeSelect").off('change.moons').on('change.moons', function () {
                createGame.rules.moons.large = parseInt(this.value, 10) || 0;
            });

        } else {
            $("#moonsDropdown").hide();
            delete createGame.rules.moons;
        }
    },

    doMovementCheck: function doMovementCheck(data) {
        var checkval = $("#movementcheck:checked").val();

        if (checkval == "on") {
            $("#movementDropdown").show();
            var selectedValue = $("#initiativeSelect").val();
            createGame.rules.initiativeCategories = parseInt(selectedValue, 10);

            $("#initiativeSelect").off('change').on('change', function () {
                createGame.rules.initiativeCategories = parseInt($(this).val(), 10);
            });
        } else {
            $("#movementDropdown").hide();
            delete createGame.rules.initiativeCategories;
        }
    },

    doAllowMinesCheck: function doAllowMinesCheck(data) {
        var checkval = $("#allowMinesCheck:checked").val();

        if (checkval == "on") {
            createGame.rules.allowMines = 1;
        } else {
            delete createGame.rules.allowMines;
        }
    },

    doFriendlyFireCheck: function doFriendlyFireCheck(data) {
        var checkval = $("#friendlyFireCheck:checked").val();

        if (checkval == "on") {
            createGame.rules.friendlyFire = 1;
        } else {
            delete createGame.rules.friendlyFire;
        }
    },

    doLadderCheck: function doLadderCheck(data) {
        var checkval = $("#laddercheck:checked").val();
        var mapSelect = $("#mapDimensionsSelect");

        if (checkval == "on") {
            createGame.rules.ladder = 1;

            // Ladder requires strictly 1 slot per team.
            // Prune any extras.
            var team1 = createGame.slots.find(function (s) { return s.team === 1; });
            var team2 = createGame.slots.find(function (s) { return s.team === 2; });

            var newSlots = [];
            if (team1) newSlots.push(team1);
            if (team2) newSlots.push(team2);

            createGame.slots = newSlots;
            createGame.refreshSlotsUI();

            // Grey out forbidden maps
            var currentMap = mapSelect.val();
            createGame.forbiddenLadderMaps.forEach(function (mapVal) {
                var option = mapSelect.find('option[value="' + mapVal + '"]');
                option.prop('disabled', true);
                // Visual feedback (optional, but good for clarity)
                option.css('color', '#999');
            });

            // If current map is forbidden, switch to standard
            if (createGame.forbiddenLadderMaps.includes(currentMap)) {
                mapSelect.val("standard").trigger("change");
            }

            createGame.drawMapPreview();

        } else {
            delete createGame.rules.ladder;

            // Re-enable all maps
            createGame.forbiddenLadderMaps.forEach(function (mapVal) {
                var option = mapSelect.find('option[value="' + mapVal + '"]');
                option.prop('disabled', false);
                option.css('color', '');
            });

            createGame.refreshSlotsUI();
        }
    },

    forbiddenLadderMaps: ["2v2", "ambush", "baseAssault", "convoyRaid", "3teams", "4teams"],


    mapData: {
        "custom": {
            width: null, height: null,
            slotsRequired: { 1: 1, 2: 1 },
            teams: [
                { name: "Team 1", id: 1, depx: -19, depy: 0, depwidth: 5, depheight: 30, depavailable: 1 },
                { name: "Team 2", id: 2, depx: 18, depy: 0, depwidth: 5, depheight: 30, depavailable: 1 }
            ]
        },
        "small": {
            width: 30, height: 24,
            slotsRequired: { 1: 1, 2: 1 },
            teams: [
                { name: "Team 1", id: 1, depx: -12, depy: 0, depwidth: 7, depheight: 24, depavailable: 1 },
                { name: "Team 2", id: 2, depx: 11, depy: 0, depwidth: 7, depheight: 24, depavailable: 1 }
            ]
        },
        "standard": {
            width: 42, height: 30,
            slotsRequired: { 1: 1, 2: 1 },
            teams: [
                { name: "Team 1", id: 1, depx: -19, depy: 0, depwidth: 5, depheight: 30, depavailable: 1 },
                { name: "Team 2", id: 2, depx: 18, depy: 0, depwidth: 5, depheight: 30, depavailable: 1 }
            ]
        },
        "large": {
            width: 60, height: 40,
            slotsRequired: { 1: 1, 2: 1 },
            teams: [
                { name: "Team 1", id: 1, depx: -28, depy: 0, depwidth: 5, depheight: 40, depavailable: 1 },
                { name: "Team 2", id: 2, depx: 27, depy: 0, depwidth: 5, depheight: 40, depavailable: 1 }
            ]
        },
        "2v2": {
            width: 42, height: 40,
            // Enforce strictly 2 slots per team
            slotsRequired: { 1: 2, 2: 2 },
            teams: [
                {
                    id: 1,
                    depx: -19, depy: 0, depwidth: 5, depheight: 40,
                    slots: [
                        { name: "Team 1 (North)", depx: -19, depy: 10, depwidth: 5, depheight: 20, depavailable: 1 },
                        { name: "Team 1 (South)", depx: -19, depy: -10, depwidth: 5, depheight: 20, depavailable: 1 }
                    ]
                },
                {
                    id: 2,
                    depx: 18, depy: 0, depwidth: 5, depheight: 40,
                    slots: [
                        { name: "Team 2 (North)", depx: 18, depy: 10, depwidth: 5, depheight: 20, depavailable: 1 },
                        { name: "Team 2 (South)", depx: 18, depy: -10, depwidth: 5, depheight: 20, depavailable: 1 }
                    ]
                }
            ]
        },
        "ambush": {
            width: 42, height: 30,
            // Enforce strictly 2 slots per team
            slotsRequired: { 1: 1, 2: 2 },
            scenario: {
                //other: "The defender has been caught in an ambush! They must survive for 8 turns.",
            },
            teams: [
                {
                    id: 1,
                    depx: 0, depy: 0, depwidth: 12, depheight: 6,
                    slots: [
                        { name: "Ambushed", points: 3000, depx: 0, depy: 0, depwidth: 12, depheight: 6, depavailable: 1 }
                    ]
                },
                {
                    id: 2,
                    depx: 0, depy: 0, depwidth: 30, depheight: 5,
                    slots: [
                        { name: "Ambusher (North)", points: 2000, depx: 0, depy: 12, depwidth: 30, depheight: 5, depavailable: 1 },
                        { name: "Ambusher (South)", points: 2000, depx: 0, depy: -12, depwidth: 30, depheight: 5, depavailable: 1 }
                    ]
                }
            ]
        },
        "baseAssault": {
            width: 60, height: 40,
            // Enforce strictly 2 slots per team
            slotsRequired: { 1: 2, 2: 1 },
            teams: [
                {
                    id: 1,
                    depx: -19, depy: 0, depwidth: 5, depheight: 40,
                    slots: [
                        { points: 5000, name: "Fixed Defences", depx: -19, depy: 0, depwidth: 5, depheight: 20, depavailable: 1 },
                        { points: 5000, name: "Reinforcements", depx: -28, depy: 0, depwidth: 5, depheight: 40, depavailable: 3 }
                    ]
                },
                {
                    id: 2,
                    depx: 26, depy: 0, depwidth: 6, depheight: 40,
                    slots: [
                        { points: 10000, name: "Attackers", depx: 26, depy: 0, depwidth: 6, depheight: 40, depavailable: 1 }
                    ]
                }
            ]
        },
        "convoyRaid": {
            width: 42, height: 30,
            // Enforce strictly 2 slots per team
            slotsRequired: { 1: 3, 2: 1 },
            teams: [
                {
                    id: 1,
                    depx: -19, depy: 5, depwidth: 5, depheight: 10,
                    slots: [
                        { name: "Defenders", depx: -19, depy: 5, depwidth: 5, depheight: 10, depavailable: 2 },
                        { name: "Freighters", depx: -15, depy: -12, depwidth: 10, depheight: 3, depavailable: 1 },
                        { name: "Jumpgate", points: 1000, depx: 15, depy: 12, depwidth: 2, depheight: 2, depavailable: 1 }
                    ]
                },
                {
                    id: 2,
                    depx: 18, depy: 7, depwidth: 5, depheight: 16,
                    slots: [
                        { name: "Attackers", depx: 18, depy: 7, depwidth: 5, depheight: 16, depavailable: 1 }
                    ]
                }
            ]
        },
        "northvsouth": {
            width: 60, height: 40,
            // Enforce strictly 2 slots per team
            slotsRequired: { 1: 1, 2: 1 },
            teams: [
                { name: "North", id: 1, depx: -1, depy: 17, depwidth: 59, depheight: 5, depavailable: 1 },
                { name: "South", id: 2, depx: -1, depy: -17, depwidth: 59, depheight: 5, depavailable: 1 }
            ]
        },
        "3teams": {
            width: 42, height: 30,
            slotsRequired: { 1: 1, 2: 1, 3: 1 },
            teams: [
                { name: "Team 1", id: 1, depx: -19, depy: -7, depwidth: 5, depheight: 15, depavailable: 1 },
                { name: "Team 2", id: 2, depx: 18, depy: -7, depwidth: 5, depheight: 15, depavailable: 1 },
                { name: "Team 3", id: 3, depx: 0, depy: 12, depwidth: 15, depheight: 5, depavailable: 1 }
            ]
        },
        "4teams": {
            width: 42, height: 30,
            slotsRequired: { 1: 1, 2: 1, 3: 1, 4: 1 },
            teams: [
                { name: "Team 1", id: 1, depx: -19, depy: 0, depwidth: 5, depheight: 15, depavailable: 1 },
                { name: "Team 2", id: 2, depx: 18, depy: 0, depwidth: 5, depheight: 15, depavailable: 1 },
                { name: "Team 3", id: 3, depx: 0, depy: 12, depwidth: 15, depheight: 5, depavailable: 1 },
                { name: "Team 4", id: 4, depx: 0, depy: -12, depwidth: 15, depheight: 5, depavailable: 1 }
            ]
        },
        "unlimited": {
            width: null, height: null,
            slotsRequired: { 1: 1, 2: 1 },
            teams: [
                { name: "Team 1", id: 1, depx: -28, depy: 0, depwidth: 5, depheight: 40, depavailable: 1 },
                { name: "Team 2", id: 2, depx: 27, depy: 0, depwidth: 5, depheight: 40, depavailable: 1 }
            ]
        },
    },

    onMapDimensionsChange: function () {
        const val = $("#mapDimensionsSelect").val();
        const mapConfig = createGame.mapData[val];

        if (val === "unlimited") {
            $(".gamespacedefinition .unlimitedspace").removeClass("invisible");
            $(".gamespacedefinition .limitedspace").addClass("invisible");
        } else {
            $(".gamespacedefinition .unlimitedspace").addClass("invisible");
            $(".gamespacedefinition .limitedspace").removeClass("invisible");
        }

        if (mapConfig) {
            createGame.applyMapConfig(mapConfig);
        }

        createGame.drawMapPreview();
    },

    applyMapConfig: function (config) {
        if (config.width && config.height) {
            createGame.gamespace_data.width = config.width;
            createGame.gamespace_data.height = config.height;
            $(".spacex").val(config.width);
            $(".spacey").val(config.height);
        }

        // Handle slots
        // Handle slots
        if (config.slotsRequired) {
            // Enforce specific number of slots
            const teamIds = Object.keys(config.slotsRequired).map(Number);

            // First, remove teams that are not in the requirements (if we are being strict, but maybe better to just ensure the ones we need exist)
            // For now, let's just ensure the required ones exist.

            // NEW: Remove teams that are NOT in the required list
            // This allows switching from "4 Teams" back to "Standard" to cleanup
            const currentTeams = [...new Set(createGame.slots.map(s => s.team))];
            currentTeams.forEach(teamId => {
                if (!teamIds.includes(teamId)) {
                    // Remove all slots for this team
                    // createGame.removeTeam(teamId); // Suppressed to avoid confirmation

                    // Note: createGame.removeTeam usually asks for confirmation...
                    // But here we might want to force it?
                    // The removeTeam function:
                    // window.confirm.confirm("Are you sure you want to remove Team " + teamId + "?", function () { ... });

                    // We can't easily bypass the confirm in the current `removeTeam` implementation without modifying it.
                    // Instead, let's manually remove the slots for this team.

                    createGame.slots = createGame.slots.filter(s => s.team !== teamId);
                }
            });

            // Re-fetch slots after removal
            // Then ensure required teams exist
            teamIds.forEach(teamId => {
                const required = config.slotsRequired[teamId] || 0;
                if (required > 0) {
                    const defaults = config.teams ? config.teams.find(t => t.id === teamId) : null;
                    createGame.ensureTeamSlots(teamId, required, defaults);
                }
            });

        } else if (config.teams) {
            // Just update existing slots with defaults (Legacy behavior)
            for (let slot of createGame.slots) {
                const defaults = config.teams.find(t => t.id === slot.team);
                if (defaults) {
                    // Only copy specific properties to avoid overwriting everything
                    Object.assign(slot, {
                        depx: defaults.depx,
                        depy: defaults.depy,
                        depwidth: defaults.depwidth,
                        depheight: defaults.depheight
                    });
                }
            }
        }

        // Populating Aditional Info when you select a map is supported, but it's quite tricky to know when to clear it.  So for the moment it's not used.
        if (config.scenario) {
            for (const [key, value] of Object.entries(config.scenario)) {
                // If it's a dropdown that needs 'Other' to show custom input
                if (["req", "tier", "victory", "enhancements"].includes(key)) {
                    // Check if value is one of the options
                    const select = $(`#${key}`);
                    const optionExists = select.find(`option[value="${value}"]`).length > 0;

                    if (optionExists) {
                        select.val(value).trigger("change");
                    } else {
                        // Assume custom input
                        const triggerVal = {
                            "req": "Other",
                            "tier": "Other",
                            "victory": "Other",
                            "enhancements": "Up to X points"
                        }[key];

                        if (triggerVal) {
                            select.val(triggerVal).trigger("change");
                            $(`#${key}_custom`).val(value);
                        }
                    }
                } else {
                    // Standard input/textarea
                    $(`#${key}`).val(value);
                }
            }
        }

        createGame.refreshSlotsUI();
    },

    ensureTeamSlots: function (team, count, defaults) {
        // Find current slots for this team
        let teamSlots = createGame.slots.filter(s => s.team === team);

        // Add slots if needed
        while (teamSlots.length < count) {
            createGame.createNewSlot.call({ className: "team" + team }, null, defaults);
            // Refresh list
            teamSlots = createGame.slots.filter(s => s.team === team);
        }

        // Remove slots if too many
        while (teamSlots.length > count) {
            const slotToRemove = teamSlots[teamSlots.length - 1];
            createGame.removeSlotData(slotToRemove.id);
            // Also remove from DOM immediately to keep UI in sync before full refresh
            $(".slot.slotid_" + slotToRemove.id).remove();
            teamSlots = createGame.slots.filter(s => s.team === team);
        }

        // Update all slots with defaults or specific slot overrides
        if (defaults) {
            let i = 0;
            for (let slot of teamSlots) {
                let config = defaults;
                // Check for per-slot override
                if (defaults.slots && defaults.slots[i]) {
                    // Create a merged config where slot override takes precedence
                    config = $.extend({}, defaults, defaults.slots[i]);
                }

                Object.assign(slot, {
                    depx: config.depx,
                    depy: config.depy,
                    depwidth: config.depwidth,
                    depheight: config.depheight
                });

                if (config.points !== undefined) {
                    slot.points = config.points;
                }
                if (config.name !== undefined) {
                    if (!createGame.rules.ladder || !slot.isLadderPopulated) {
                        slot.name = config.name;
                    }
                }
                if (config.depavailable !== undefined) {
                    slot.depavailable = config.depavailable;
                }
                i++;
            }
        }

        createGame.refreshSlotsUI();
    },

    refreshSlotsUI: function () {
        createGame.renderTeams();
        $(".slotcontainer").empty();
        createGame.createSlotsFromArray();
        createGame.drawMapPreview(); // Ensure map updates

        if (createGame.rules.ladder) {
            $(".addslotbutton").hide();
            $(".slot .remove-btn").hide();
            $("#addTeamBtn").hide();
            $(".remove-team-btn").hide();
        } else {
            // Only show Add Team if map supports it (or is custom/unlimited)
            const mapType = $("#mapDimensionsSelect").val();
            // Allow adding teams on all maps for now as per user request/workflow
            const allowAddTeam = true; // ["custom", "unlimited"].includes(mapType);

            if (allowAddTeam) {
                $("#addTeamBtn").show();
                $(".remove-team-btn").show();
            } else {
                $("#addTeamBtn").hide();
                $(".remove-team-btn").hide();
            }

            $(".addslotbutton").show();
            createGame.updateSlotButtons();
        }
    },

    updateSlotButtons: function () {
        // Reset display first (or ensure we show/hide correctly)
        // $(".slot .remove-btn").css("display", ""); // Optional if we explicitly show/hide below

        const teams = [...new Set(createGame.slots.map(s => s.team))];
        teams.forEach(teamId => {
            const teamSlots = createGame.slots.filter(s => s.team === teamId);
            if (teamSlots.length <= 1) {
                $(`#team${teamId} .slot .remove-btn`).hide();
            } else {
                $(`#team${teamId} .slot .remove-btn`).show();
            }
        });
    },


    renderTeams: function () {
        // Identify all unique teams
        const teams = [...new Set(createGame.slots.map(s => s.team))].sort((a, b) => a - b);
        const container = $("#teamsContainer");

        // Remove teams that no longer exist
        container.find(".team-section").each(function () {
            const id = parseInt($(this).data("team-id"));
            if (!teams.includes(id)) {
                $(this).remove();
            }
        });

        // Add missing teams
        teams.forEach(teamId => {
            if (container.find(`.team-section[data-team-id="${teamId}"]`).length === 0) {
                const template = $("#teamtemplatecontainer .team-section").clone();
                template.attr("data-team-id", teamId);
                template.find(".team-number").text(teamId);

                // Add specific ID for slot container targeting
                template.find(".slotcontainer").attr("id", "team" + teamId);

                // Update Add Slot button
                template.find(".addslotbutton").addClass("team" + teamId).data("team", teamId);

                // Bind remove team
                // Show/Hide remove team button based on team ID
                if (teamId > 2) {
                    template.find(".remove-team-btn").show();
                } else {
                    template.find(".remove-team-btn").hide();
                }

                // Bind add slot
                template.find(".addslotbutton").on("click", createGame.createNewSlot);

                container.append(template);
            }
        });
    },

    addTeam: function () {
        const teams = [...new Set(createGame.slots.map(s => s.team))];
        const nextTeamId = (teams.length > 0 ? Math.max(...teams) : 0) + 1;

        // Determine default deployment based on Odd/Even
        let defaults = {};
        if (nextTeamId % 2 !== 0) {
            // Odd -> Mimic Team 1
            const t1 = createGame.slots.find(s => s.team === 1);
            if (t1) {
                defaults = { depx: t1.depx, depy: t1.depy, depwidth: t1.depwidth, depheight: t1.depheight };
            } else {
                defaults = { depx: -19, depy: 0, depwidth: 5, depheight: 30 };
            }
        } else {
            // Even -> Mimic Team 2
            const t2 = createGame.slots.find(s => s.team === 2);
            if (t2) {
                defaults = { depx: t2.depx, depy: t2.depy, depwidth: t2.depwidth, depheight: t2.depheight };
            } else {
                defaults = { depx: 18, depy: 0, depwidth: 5, depheight: 30 };
            }
        }

        // Find a new slot ID
        let maxSlot = 0;
        createGame.slots.forEach(s => maxSlot = Math.max(maxSlot, s.id));
        createGame.slotid = maxSlot + 1;

        createGame.slots.push({
            id: createGame.slotid,
            team: nextTeamId,
            name: "Team " + nextTeamId,
            points: 3500,
            depx: parseInt(defaults.depx) || 0,
            depy: parseInt(defaults.depy) || 0,
            depwidth: parseInt(defaults.depwidth) || 5,
            depheight: parseInt(defaults.depheight) || 5,
            depavailable: 1
        });

        createGame.refreshSlotsUI();
    },

    removeTeam: function (teamId) {
        console.log("Executing removeTeam for:", teamId);
        window.confirm.confirm("Are you sure you want to remove Team " + teamId + "?", function () {
            createGame.slots = createGame.slots.filter(s => s.team !== teamId);
            createGame.refreshSlotsUI();
        });
    },



    createSlotsFromArray: function createSlotsFromArray() {
        for (var i in createGame.slots) {
            createGame.createSlot(createGame.slots[i]);
        }
    },

    createSlot: function createSlot(data) {
        var template = $("#slottemplatecontainer .slot").clone();
        var target = $("#team" + data.team + ".slotcontainer");

        if (target.length === 0) {
            console.error("Target container for team " + data.team + " not found!");
            return;
        }

        template.addClass("slotid_" + data.id);
        template.data("slotid", data.id);
        template.data("team", data.team);

        // ... (rest of slot population)
        var actual = template.clone(true).appendTo(target);

        actual.data("slotid", data.id);
        actual.addClass("slotid_" + data.id);

        // Disable points input if unlimited
        if (data.points == -1) {
            actual.find("[name='points']").hide();
            // Ensure label exists if not in template (it might be added to template later, but safe to add if missing)
            if (actual.find(".unlimited-label").length === 0) {
                actual.find("[name='points']").after('<span class="unlimited-label" style="display:inline-block; font-weight:bold; color:#DEEBFF; margin-left:5px;">Unlimited</span>');
            } else {
                actual.find(".unlimited-label").show();
            }
        } else {
            // For standard slots, ensure label is hidden if it exists
            if (actual.find(".unlimited-label").length === 0) {
                actual.find("[name='points']").after('<span class="unlimited-label" style="display:none; font-weight:bold; color:#DEEBFF; margin-left:5px;">Unlimited</span>');
            }
        }

        createGame.setSlotData(data);
    },

    setSlotData: function setSlotData(data) {
        var slot = $(".slot.slotid_" + data.id);
        // Note: We used to just use class selectors, but now we have Inputs with Names.
        // We can use [name='...']
        slot.find("[name='name']").val(data.name);

        // Only update points if NOT unlimited
        if (data.points != -1) {
            slot.find("[name='points']").val(data.points);
        }

        slot.find("[name='depx']").val(data.depx);
        slot.find("[name='depy']").val(data.depy);
        slot.find("[name='depwidth']").val(data.depwidth);
        slot.find("[name='depheight']").val(data.depheight);
        slot.find("[name='depavailable']").val(data.depavailable);
    },

    createNewSlot: function createNewSlot(e, explicitDefaults) {
        if (this.id === "addTeamBtn") return; // Prevent accidental trigger if class matches
        var team;
        // Check if 'this' is a DOM-like object or jQuery object
        // Use data-team attribute if available (Best practice)
        if ($(this).data("team")) {
            team = parseInt($(this).data("team"));
        } else {
            // Fallback for ensureTeamSlots which uses .call({className...}) or legacy class names
            const className = this.className || "";
            const match = className.match(/team(\d+)/);
            if (match) {
                team = parseInt(match[1]);
            } else {
                team = 1; // Default fallback
            }
        }

        createGame.slotid++;

        // Default to copying the LAST slot of that team, or standard defaults if none
        let lastSlotOfTeam = null;
        for (let i = createGame.slots.length - 1; i >= 0; i--) {
            if (createGame.slots[i].team === team) {
                lastSlotOfTeam = createGame.slots[i];
                break;
            }
        }

        let newData = {
            id: createGame.slotid,
            team: team,
            name: "Team " + team,
            points: $("#unlimitedPointsCheck").is(":checked") ? -1 : 3500,
            depx: 0,
            depy: 0,
            deptype: "box",
            depwidth: 5,
            depheight: 5,
            depavailable: 1
        };

        if (explicitDefaults) {
            Object.assign(newData, {
                depx: explicitDefaults.depx,
                depy: explicitDefaults.depy,
                depwidth: explicitDefaults.depwidth,
                depheight: explicitDefaults.depheight
            });
            if (explicitDefaults.points !== undefined) {
                newData.points = explicitDefaults.points;
            }
            if (explicitDefaults.name !== undefined) {
                newData.name = explicitDefaults.name;
            }
            if (explicitDefaults.depavailable !== undefined) {
                newData.depavailable = explicitDefaults.depavailable;
            }
        } else if (lastSlotOfTeam) {
            // Copy relevant deployment data
            newData.depx = lastSlotOfTeam.depx;
            newData.depy = lastSlotOfTeam.depy;
            newData.depwidth = lastSlotOfTeam.depwidth;
            newData.depheight = lastSlotOfTeam.depheight;
            newData.depavailable = lastSlotOfTeam.depavailable;
            newData.points = lastSlotOfTeam.points;
        } else {
            // Fallback if no slots exist for team (shouldn't happen often)
            if (team === 1) { newData.depx = -19; newData.depheight = 30; }
            if (team === 2) { newData.depx = 18; newData.depheight = 30; }
        }

        createGame.slots.push(newData);
        createGame.createSlot(newData);
        createGame.drawMapPreview();
        createGame.updateSlotButtons();
    },

    getSlotData: function getSlotData(id) {
        for (var i in createGame.slots) {
            var slot = createGame.slots[i];
            if (slot.id == id) return slot;
        }
    },

    removeSlotData: function removeSlotData(id) {
        for (var i = createGame.slots.length - 1; i >= 0; i--) {
            if (createGame.slots[i].id === id) {
                createGame.slots.splice(i, 1);
            }
        }
    },

    removeSlot: function removeSlot(e) {
        const removeBtn = $(this);
        const slot = removeBtn.closest(".slot");
        const slotId = slot.data("slotid");
        const data = createGame.getSlotData(slotId);

        if (!data) return;

        // Check if it's the last slot of a team
        const slotsOfTeam = createGame.slots.filter(s => s.team === data.team);
        if (slotsOfTeam.length <= 1) {
            window.confirm.error("You cannot delete the last slot of a team!", function () { });
            return false;
        }

        // Confirmation is generally annoying for simple removes, but if desired:
        // window.confirm.show("Are you sure?", ...)
        // For now, let's just do it to be zippy.

        createGame.removeSlotData(data.id);
        createGame.drawMapPreview();
        slot.remove();
        createGame.updateSlotButtons();
    },

    getScenarioDescriptionFromFields: function getScenarioDescriptionFromFields() {
        const fields = [
            { label: 'REQUIREMENTS', id: 'req' },
            { label: 'CUSTOM FACTIONS / UNITS', id: 'customfactions' },
            //{ label: 'CUSTOM UNITS IN OFFICIAL FACTIONS', id: 'customunits' },
            { label: 'ENHANCEMENTS', id: 'enhancements' },
            { label: 'EXPECTED POWER LEVEL', id: 'tier' },
            { label: 'FORBIDDEN FACTIONS', id: 'forbidden' },
            { label: 'MAP BORDERS', id: 'borders' },
            { label: 'CALLED SHOTS', id: 'called' },
            { label: 'VICTORY CONDITIONS', id: 'victory' },
            { label: 'ADDITIONAL INFO', id: 'other' }
        ];

        let result = '*** SCENARIO DESCRIPTION ***\n';
        for (const field of fields) {
            const el = document.getElementById(field.id);
            if (el) {
                let value = el.value.trim();

                // Check for custom input override
                const customEl = document.getElementById(field.id + '_custom');
                if (customEl && customEl.style.display !== 'none') {
                    const customVal = customEl.value.trim();
                    if (field.id === 'enhancements') {
                        value = `Up to ${customVal}pts allowed`;
                    } else {
                        value = customVal;
                    }
                }

                result += `${field.label}: ${value}\n`;
            }
        }
        return result;
    },

    submitFleetTest: function () {
        createGame.isFleetTest = true;
        $("#createGameForm button[type='submit']").trigger("mousedown"); // Trigger validation/allowSubmit flag if needed
        $("#createGameForm").submit();
    },

    setData: function setData() {
        var gamename = $("#gamename").val();
        var background = $("#backgroundSelect").val();
        var description = createGame.getScenarioDescriptionFromFields();
        var gamespace = "-1x-1";
        var flight = "";

        if ($("#mapDimensionsSelect").val() !== "unlimited") {
            gamespace = "" + createGame.gamespace_data.width + "x" + createGame.gamespace_data.height;
        }

        if ($("#flightSizeCheck:checked").val() == "on") {
            flight = 1;
        }

        // Add Fleet Test rule if flag is set
        if (createGame.isFleetTest) {
            createGame.rules.fleetTest = 1;
        }

        var data = { gamename: gamename, background: background, slots: createGame.slots, gamespace: gamespace, flight: flight, rules: createGame.rules, description: description };
        data = JSON.stringify(data);
        $("#createGameData").val(data);
    }
};
