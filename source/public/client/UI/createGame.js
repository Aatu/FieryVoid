"use strict";

jQuery(function ($) {
    $("input[name='background']").on("change", createGame.mapSelect);

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
        const isComboInput = (e.target.tagName === 'INPUT' && $(e.target).hasClass('cg-combo-input'));

        if (!isNumberInput && !isPointsInput && !isComboInput) return;
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

    // Scenario Description: built from scenarioCard.FIELDS, so it can never offer an option the
    // stored JSON's contract doesn't know. Binds its own reveal/help handlers.
    createGame.renderScenarioFields();

    // Terrain Features: the rules follow the rows, no master checkbox. The combos bind their own
    // clamp-on-change FIRST, so readTerrain always sees a value already inside 0..max.
    createGame.initCountCombos();
    $(".cg-terrain-count").on("change", createGame.readTerrain);
    createGame.readTerrain();

    // In-Service Date: a year, so digits only and four at most (the lobby's own ISD box does the same).
    // The box is shown only while its checkbox is ticked.
    $("#inServiceDate").on("input", function () {
        const digits = this.value.replace(/\D/g, "").slice(0, 4);
        if (digits !== this.value) this.value = digits;
    });
    $("#inServiceDateCheck").on("change", function () {
        createGame.showInServiceDate();
        if (this.checked) $("#inServiceDate").trigger("focus");
    });
    createGame.showInServiceDate(); //a restored form may arrive ticked

    // Private Game: the password box (and Show) only while Require password is ticked.
    createGame.initPrivateGame();

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
    $("#teamsContainer").on("click", ".copy-slot-btn", createGame.copySlot);
    $("#teamsContainer").on("click", ".copy-team-btn", function () {
        createGame.copyTeam(parseInt($(this).closest(".team-section").data("team-id"), 10));
    });

    /* Only the Summary step's Confirm creates the game. Enter in a field on Steps 1-3 submits the
       form implicitly - through Confirm, the form's one submit button - so it is refused here.
       (This replaces a mousedown/touchstart flag, which also refused the keyboard's own Enter or
       Space on the button itself.) Every step is checked once more on the way out, and a second
       press while the first POST is in flight is ignored: each one would create a game. */
    $("#createGameForm").on("submit", function (e) {
        if (createGame.submitting || (!createGame.isFleetTest && createGame.currentStep !== 4)) {
            e.preventDefault();
            return false;
        }

        for (let step = 1; step <= 3; step++) {
            const problem = createGame.validateStep(step);
            if (problem) {
                e.preventDefault();
                createGame.showStep(step, true);
                createGame.showStepError(problem);
                return false;
            }
        }

        createGame.setData();
        createGame.submitting = true;
        $("#cgConfirm").prop("disabled", true);
        //The password is in the posted JSON now. Emptied so a browser does not take this form for a
        //sign-up and offer to save it as the player's own account password.
        $("#gamePassword").val("");
    });

    //Back from the lobby can restore this page from the bfcache with Confirm still disabled.
    $(window).on("pageshow", function () {
        createGame.submitting = false;
        $("#cgConfirm").prop("disabled", false);
    });

    // Bind checkbox events
    $("#mapDimensionsSelect").on("change", createGame.onMapDimensionsChange);

    $("#movementcheck").on("click", createGame.doMovementCheck);
    $("#desperatecheck").on("click", createGame.doDesperateCheck);
    $("#allowMinesCheck").on("click", createGame.doAllowMinesCheck);
    $("#allowReinforcementsCheck").on("click", createGame.doAllowReinforcementsCheck);
    $("#friendlyFireCheck").on("click", createGame.doFriendlyFireCheck);
    $("#laddercheck").on("click", createGame.doLadderCheck);

    // A browser restoring this form (Back from the lobby, or a failed POST) re-ticks the boxes
    // WITHOUT firing click, which left a ticked box whose rule was never set. Read them once.
    createGame.doMovementCheck();
    createGame.doDesperateCheck();
    createGame.doAllowMinesCheck();
    createGame.doAllowReinforcementsCheck();
    createGame.doFriendlyFireCheck();
    if ($("#laddercheck").is(":checked")) createGame.doLadderCheck();
    if ($("#unlimitedPointsCheck").is(":checked")) $("#unlimitedPointsCheck").trigger("change");

    createGame.refreshSlotsUI();
    createGame.onMapDimensionsChange(); // Run on load
    createGame.drawMapPreview();

    createGame.initPresets();
    createGame.initWizard();
});

window.createGame = {
    gamespace_data: { width: 42, height: 30 },
    rules: {},
    slots: [
        { id: 1, team: 1, name: "Team 1", points: 3500, depx: -21, depy: 0, deptype: "box", depwidth: 10, depheight: 30, depavailable: 1 },
        { id: 2, team: 2, name: "Team 2", points: 3500, depx: 21, depy: 0, deptype: "box", depwidth: 10, depheight: 30, depavailable: 1 }
    ],
    slotid: 2,

    //The chosen background doubles as the page's own backdrop - a full-size preview for free.
    mapSelect: function mapSelect() {
        const checked = $("input[name='background']:checked");
        const val = checked.val();
        if (!val) return;

        $(".cg-bg-tile").removeClass("is-selected");
        checked.closest(".cg-bg-tile").addClass("is-selected");
        $("#bgSelectedName").text(checked.closest(".cg-bg-tile").attr("title") || val);
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

        //A hand-typed size is a Custom map - and a template's pre-placed terrain was laid out for
        //the template's own size, so it goes with it.
        if (inputname == "spacex") {
            createGame.gamespace_data.width = parseInt(value);
            $("#mapDimensionsSelect").val("custom");
            createGame.setTerrainLayout(null);
            return;
        }

        if (inputname == "spacey") {
            createGame.gamespace_data.height = parseInt(value);
            $("#mapDimensionsSelect").val("custom");
            createGame.setTerrainLayout(null);
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
        createGame.paintMap(document.getElementById("mapPreview"));
        createGame.renderLegend(createGame.getTeamIds().length);
    },

    /* The map preview, onto any canvas: Step 3's preview and the Summary's copy of it. The
       drawing is mapPreview.js's, shared with the lobby's Map Preview so both show one map; this
       page supplies what goes on it - every slot's zone in its team's colour, labelled with its
       team, and the template's pre-placed terrain. */
    paintMap: function paintMap(canvas) {
        const isLimited = $("#mapDimensionsSelect").val() !== "unlimited";
        const teamCount = createGame.getTeamIds().length;

        mapPreview.paint(canvas, {
            // Use fixed width/height if unlimited is selected
            width: isLimited ? (createGame.gamespace_data.width || 1) : 84,
            height: isLimited ? (createGame.gamespace_data.height || 1) : 60,
            // Iterate data model directly to ensure we catch all teams even if DOM is lagging
            zones: createGame.slots.map(slot => ({
                x: slot.depx, y: slot.depy, w: slot.depwidth, h: slot.depheight,
                rgb: mapPreview.teamColor(slot.team, teamCount),
                label: "TEAM " + slot.team
            })),
            terrain: createGame.rules.terrainLayout
        });
    },

    /* The template's layout becomes rules.terrainLayout (or none), and both terrain notes and the
       preview follow it. Units are copied, so the rules never hold a reference into mapData. */
    setTerrainLayout: function setTerrainLayout(config) {
        const esc = scenarioCard.escapeHtml;

        if (config && config.terrain && config.terrain.length) {
            createGame.rules.terrainLayout = {
                name: config.name,
                units: config.terrain.map(unit => Object.assign({}, unit))
            };
            const count = config.terrain.length;
            $("#mapTerrainNote").html("<strong>" + esc(config.name) + ":</strong> " + esc(config.blurb || "")
                + " " + count + " terrain features, the same every game.").prop("hidden", false);
            $("#terrainLayoutNote").html("The <strong>" + esc(config.name) + "</strong> map template (Teams &amp; Map) places "
                + count + " terrain features of its own. Counts here add random terrain around them.").prop("hidden", false);
        } else {
            delete createGame.rules.terrainLayout;
            $("#mapTerrainNote, #terrainLayoutNote").empty().prop("hidden", true);
        }

        createGame.drawMapPreview();
    },

    getTeamIds: function getTeamIds() {
        return [...new Set(createGame.slots.map(s => s.team))].sort((a, b) => a - b);
    },

    /* Team colours: mapPreview.teamColor, the game's own rule as the creator will see it (they
       take slot 1 on Team 1) - so the map zones, the team cards' rails and the legend always agree,
       and agree with the colours the game itself will use (plan §11.4). */
    renderLegend: function renderLegend(teamCount) {
        let html = createGame.getTeamIds().map(function (team) {
            const rgb = mapPreview.teamColor(team, teamCount);
            return '<span class="cg-legend-item"><span class="cg-swatch" style="background:rgb(' + rgb.join(",") + ')"></span>Team ' + team + "</span>";
        }).join("");

        const kinds = mapPreview.terrainKinds(createGame.rules.terrainLayout);
        if (kinds.solid) html += '<span class="cg-legend-item"><span class="cg-swatch cg-swatch--terrain" style="opacity:' + mapPreview.TERRAIN_ALPHA + '"></span>Asteroids &amp; Moons</span>';
        if (kinds.fields) html += '<span class="cg-legend-item"><span class="cg-swatch cg-swatch--terrain" style="opacity:' + mapPreview.FIELD_TERRAIN_ALPHA + '"></span>Dust &amp; Meteor Swarms</span>';
        $("#mapLegend").html(html);
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


    /* Count combos (creategame.php cgCountCombo): Asteroids, Dust, Meteor Swarms. Best of both, as
       the old asteroid box was - ANY value 0..max can be typed or stepped with the mouse wheel (the
       page's shared wheel handler), and the ▾ offers only the named presets.
       An ARIA combobox: the input keeps focus throughout and the highlighted preset is announced
       through aria-activedescendant. ArrowDown / Alt+ArrowDown opens, arrows move, Enter picks,
       Escape closes. The list opens on a click or tap of the field too, like the original, but
       NOT on Tab focus - a keyboard user tabbing past should not get a list in their way. */
    initCountCombos: function initCountCombos() {
        $(".cg-combo").each(function () {
            const combo = $(this);
            const input = combo.find(".cg-combo-input");
            const max = parseInt(input.attr("max"), 10);

            //Typed text: digits only as they type, then clamped once they are done.
            input.on("input", function () {
                const digits = this.value.replace(/[^0-9]/g, "");
                if (digits !== this.value) this.value = digits;
            });
            input.on("change", function () {
                let val = parseInt(this.value, 10);
                if (isNaN(val) || val < 0) val = 0;
                if (val > max) val = max;
                this.value = val;
            });

            input.on("click", function () { createGame.openCombo(combo); });

            //Focus the field after a MOUSE press on the ▾ (so the arrow keys and wheel work next),
            //but not after a tap: that would raise a phone's keyboard over the list just opened.
            let lastPointer = "mouse";
            combo.find(".cg-combo-toggle").on("pointerdown", function (e) {
                lastPointer = e.pointerType || "mouse";
            }).on("click", function () {
                if (combo.hasClass("is-open")) createGame.closeCombo(combo);
                else createGame.openCombo(combo);
                if (lastPointer === "mouse") input.trigger("focus");
            });

            //mousedown, not click: picking must not first blur the input.
            combo.on("mousedown", ".cg-combo-option", function (e) {
                e.preventDefault();
                createGame.pickComboValue(combo, $(this).data("value"));
            });

            input.on("keydown", function (e) {
                const open = combo.hasClass("is-open");
                const options = combo.find(".cg-combo-option");
                let active = options.index(options.filter(".is-active"));

                if (e.key === "ArrowDown" || e.key === "ArrowUp") {
                    e.preventDefault();
                    if (!open) { createGame.openCombo(combo); return; }
                    active = (e.key === "ArrowDown") ? Math.min(active + 1, options.length - 1) : Math.max(active - 1, 0);
                    createGame.setComboActive(combo, options.eq(active));
                } else if (e.key === "Enter" && open && active >= 0) {
                    e.preventDefault(); //never submit the form from here
                    createGame.pickComboValue(combo, options.eq(active).data("value"));
                } else if (e.key === "Escape" && open) {
                    e.preventDefault();
                    createGame.closeCombo(combo);
                } else if (e.key === "Tab" && open) {
                    createGame.closeCombo(combo);
                }
            });
        });

        //Any click or tap outside a combo closes whichever one is open.
        $(document).on("mousedown touchstart", function (e) {
            if ($(e.target).closest(".cg-combo").length) return;
            $(".cg-combo.is-open").each(function () { createGame.closeCombo($(this)); });
        });
    },

    openCombo: function openCombo(combo) {
        $(".cg-combo.is-open").not(combo).each(function () { createGame.closeCombo($(this)); });
        const input = combo.find(".cg-combo-input");
        combo.addClass("is-open");
        combo.find(".cg-combo-list").prop("hidden", false);
        input.attr("aria-expanded", "true");

        //Start on the preset matching the current value, if there is one.
        const current = String(parseInt(input.val(), 10) || 0);
        const options = combo.find(".cg-combo-option");
        options.attr("aria-selected", "false");
        const match = options.filter(function () { return String($(this).data("value")) === current; });
        match.attr("aria-selected", "true");
        createGame.setComboActive(combo, match.length ? match : options.first());
    },

    closeCombo: function closeCombo(combo) {
        combo.removeClass("is-open");
        combo.find(".cg-combo-list").prop("hidden", true);
        combo.find(".cg-combo-option").removeClass("is-active");
        combo.find(".cg-combo-input").attr("aria-expanded", "false").removeAttr("aria-activedescendant");
    },

    setComboActive: function setComboActive(combo, option) {
        combo.find(".cg-combo-option").removeClass("is-active");
        if (!option.length) return;
        option.addClass("is-active");
        combo.find(".cg-combo-input").attr("aria-activedescendant", option.attr("id"));
        option[0].scrollIntoView({ block: "nearest" });
    },

    //No refocus here: a mouse pick never lost focus (the mousedown is cancelled), and a tap on a
    //phone should not bring the keyboard up after the choice is already made.
    pickComboValue: function pickComboValue(combo, value) {
        combo.find(".cg-combo-input").val(value).trigger("change");
        createGame.closeCombo(combo);
    },

    /* Terrain Features rows -> rules. A type is in the rules only when its count is above zero,
       so a game with no terrain carries no terrain keys at all (it used to carry a zero asteroid
       count and an all-zero moons object whenever "Add Terrain" had been ticked). Server-side,
       each key is a GameRules entry: asteroids -> AsteroidsRule, moons -> MoonsRule,
       dustAndMeteors -> DustAndMeteorsRule. */
    readTerrain: function readTerrain() {
        const count = function (id) {
            const el = $("#" + id);
            const val = parseInt(el.val(), 10);
            const max = parseInt(el.attr("max"), 10);
            if (isNaN(val) || val < 0) return 0;
            return isNaN(max) ? val : Math.min(val, max);
        };

        const asteroids = count("asteroidsSelect");
        if (asteroids > 0) createGame.rules.asteroids = asteroids;
        else delete createGame.rules.asteroids;

        const moons = { small: count("moonsSmallSelect"), medium: count("moonsMediumSelect"), large: count("moonsLargeSelect") };
        if (moons.small + moons.medium + moons.large > 0) createGame.rules.moons = moons;
        else delete createGame.rules.moons;

        const fields = { dust: count("dustSelect"), meteors: count("meteorsSelect") };
        if (fields.dust + fields.meteors > 0) createGame.rules.dustAndMeteors = fields;
        else delete createGame.rules.dustAndMeteors;
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

    doAllowReinforcementsCheck: function doAllowReinforcementsCheck(data) {
        var checkval = $("#allowReinforcementsCheck:checked").val();

        if (checkval == "on") {
            createGame.rules.allowReinforcements = 1;
        } else {
            delete createGame.rules.allowReinforcements;
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

    forbiddenLadderMaps: ["2v2", "ambush", "baseAssault", "convoyRaid", "3teams", "4teams", "crossroads", "fracturedFront"],


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
                { name: "North", id: 1, depx: 0, depy: 17, depwidth: 59, depheight: 5, depavailable: 1 },
                { name: "South", id: 2, depx: 0, depy: -17, depwidth: 59, depheight: 5, depavailable: 1 }
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

        /* Maps with Terrain: a `base` template's size and teams, plus terrain that is placed on
           exactly these hexes every game (see mapPreview.TERRAIN_TYPES / setTerrainLayout). Each layout is
           symmetric about the map's true centre - half a hex left of hex 0,0 on an even width -
           so both sides of a two-team map see the same ground, keeps two hexes clear of every
           deployment zone and never overlaps itself (non-field terrain keeps a one-hex gap).
           Read through getMapConfig(). */
        "closeQuarters": {
            base: "small", name: "Close Quarters",
            blurb: "A tight map with asteroid cover throughout and a hazardous centre.",
            terrain: [
                { type: "asteroidL", q: -4, r: 2 }, { type: "asteroidM", q: -1, r: 5 }, { type: "asteroid2", q: -3, r: 8, h: 0 },
                { type: "asteroidS", q: -5, r: 6 }, { type: "dust", q: -2, r: 1 }, { type: "dust", q: -5, r: 4 },
                { type: "meteors", q: 0, r: 3 }, { type: "asteroidS", q: -2, r: 10 }, { type: "asteroidL", q: 3, r: 2 },
                { type: "asteroidM", q: 1, r: 5 }, { type: "asteroid2", q: 2, r: 8, h: 3 }, { type: "asteroidS", q: 4, r: 6 },
                { type: "dust", q: 2, r: 1 }, { type: "dust", q: 4, r: 4 }, { type: "asteroidS", q: 1, r: 10 },
                { type: "asteroidL", q: -4, r: -2 }, { type: "asteroidM", q: -1, r: -5 }, { type: "asteroid2", q: -3, r: -8, h: 0 },
                { type: "asteroidS", q: -5, r: -6 }, { type: "dust", q: -2, r: -1 }, { type: "dust", q: -5, r: -4 },
                { type: "meteors", q: 0, r: -3 }, { type: "asteroidS", q: -2, r: -10 }, { type: "asteroidL", q: 3, r: -2 },
                { type: "asteroidM", q: 1, r: -5 }, { type: "asteroid2", q: 2, r: -8, h: 3 }, { type: "asteroidS", q: 4, r: -6 },
                { type: "dust", q: 2, r: -1 }, { type: "dust", q: 4, r: -4 }, { type: "asteroidS", q: 1, r: -10 }
            ]
        },
        "asteroidBelt": {
            base: "standard", name: "Asteroid Belt",
            blurb: "A broken asteroid belt down the middle, crossed by three lanes.",
            terrain: [
                { type: "asteroidL", q: -2, r: 2 }, { type: "asteroidM", q: 0, r: 3 }, { type: "asteroid3", q: -3, r: 5, h: 0 },
                { type: "asteroidM", q: -1, r: 7 }, { type: "asteroidS", q: -4, r: 3 }, { type: "dust", q: -6, r: 2 },
                { type: "dust", q: -6, r: 6 }, { type: "dust", q: -1, r: 0 }, { type: "meteors", q: -1, r: 8 },
                { type: "asteroid2", q: -3, r: 10, h: 0 }, { type: "asteroidM", q: 0, r: 11 }, { type: "asteroidS", q: -2, r: 13 },
                { type: "dust", q: -9, r: 4 }, { type: "asteroidS", q: -10, r: 8 }, { type: "dust", q: -8, r: 12 },
                { type: "asteroidL", q: 1, r: 2 }, { type: "asteroid3", q: 3, r: 5, h: 2 }, { type: "asteroidM", q: 1, r: 7 },
                { type: "asteroidS", q: 4, r: 3 }, { type: "dust", q: 5, r: 2 }, { type: "dust", q: 5, r: 6 },
                { type: "dust", q: 0, r: 0 }, { type: "meteors", q: 0, r: 8 }, { type: "asteroid2", q: 2, r: 10, h: 3 },
                { type: "asteroidS", q: 2, r: 13 }, { type: "dust", q: 8, r: 4 }, { type: "asteroidS", q: 9, r: 8 },
                { type: "dust", q: 7, r: 12 }, { type: "asteroidL", q: -2, r: -2 }, { type: "asteroidM", q: 0, r: -3 },
                { type: "asteroid3", q: -3, r: -5, h: 5 }, { type: "asteroidM", q: -1, r: -7 }, { type: "asteroidS", q: -4, r: -3 },
                { type: "dust", q: -6, r: -2 }, { type: "dust", q: -6, r: -6 }, { type: "meteors", q: -1, r: -8 },
                { type: "asteroid2", q: -3, r: -10, h: 0 }, { type: "asteroidM", q: 0, r: -11 }, { type: "asteroidS", q: -2, r: -13 },
                { type: "dust", q: -9, r: -4 }, { type: "asteroidS", q: -10, r: -8 }, { type: "dust", q: -8, r: -12 },
                { type: "asteroidL", q: 1, r: -2 }, { type: "asteroid3", q: 3, r: -5, h: 3 }, { type: "asteroidM", q: 1, r: -7 },
                { type: "asteroidS", q: 4, r: -3 }, { type: "dust", q: 5, r: -2 }, { type: "dust", q: 5, r: -6 },
                { type: "meteors", q: 0, r: -8 }, { type: "asteroid2", q: 2, r: -10, h: 3 }, { type: "asteroidS", q: 2, r: -13 },
                { type: "dust", q: 8, r: -4 }, { type: "asteroidS", q: 9, r: -8 }, { type: "dust", q: 7, r: -12 }
            ]
        },
        "twinMoons": {
            base: "standard", name: "Twin Moons",
            blurb: "Two moons north and south of a dusty centre, with asteroid cover on both approaches.",
            terrain: [
                { type: "moonM", q: 0, r: 7 }, { type: "asteroidS", q: -4, r: 10 }, { type: "asteroidM", q: -3, r: 4 },
                { type: "dust", q: 0, r: 1 }, { type: "dust", q: -2, r: 0 }, { type: "meteors", q: -1, r: 3 },
                { type: "asteroid2", q: -9, r: 2, h: 0 }, { type: "asteroidL", q: -8, r: 10 }, { type: "dust", q: -7, r: 6 },
                { type: "asteroidS", q: -12, r: 5 }, { type: "asteroidS", q: 3, r: 10 }, { type: "asteroidM", q: 2, r: 4 },
                { type: "dust", q: 1, r: 0 }, { type: "meteors", q: 1, r: 3 }, { type: "asteroid2", q: 8, r: 2, h: 3 },
                { type: "asteroidL", q: 7, r: 10 }, { type: "dust", q: 6, r: 6 }, { type: "asteroidS", q: 12, r: 5 },
                { type: "moonM", q: 0, r: -7 }, { type: "asteroidS", q: -4, r: -10 }, { type: "asteroidM", q: -3, r: -4 },
                { type: "dust", q: 0, r: -1 }, { type: "meteors", q: -1, r: -3 }, { type: "asteroid2", q: -9, r: -2, h: 0 },
                { type: "asteroidL", q: -8, r: -10 }, { type: "dust", q: -7, r: -6 }, { type: "asteroidS", q: -12, r: -5 },
                { type: "asteroidS", q: 3, r: -10 }, { type: "asteroidM", q: 2, r: -4 }, { type: "meteors", q: 1, r: -3 },
                { type: "asteroid2", q: 8, r: -2, h: 3 }, { type: "asteroidL", q: 7, r: -10 }, { type: "dust", q: 6, r: -6 },
                { type: "asteroidS", q: 12, r: -5 }
            ]
        },
        "crossroads": {
            base: "4teams", name: "Crossroads",
            blurb: "A large moon at the centre of four fleets, with cover in every quarter.",
            terrain: [
                { type: "moonL", q: 0, r: 0 }, { type: "asteroidM", q: -5, r: 6 }, { type: "asteroid3", q: -9, r: 4, h: 0 },
                { type: "asteroidS", q: -12, r: 7 }, { type: "dust", q: -4, r: 4 }, { type: "meteors", q: -7, r: 2 },
                { type: "dust", q: -11, r: 1 }, { type: "asteroidM", q: 4, r: 6 }, { type: "asteroid3", q: 8, r: 4, h: 2 },
                { type: "asteroidS", q: 12, r: 7 }, { type: "dust", q: 3, r: 4 }, { type: "meteors", q: 6, r: 2 },
                { type: "dust", q: 11, r: 1 }, { type: "asteroidM", q: -5, r: -6 }, { type: "asteroid3", q: -9, r: -4, h: 5 },
                { type: "asteroidS", q: -12, r: -7 }, { type: "dust", q: -4, r: -4 }, { type: "meteors", q: -7, r: -2 },
                { type: "dust", q: -11, r: -1 }, { type: "asteroidM", q: 4, r: -6 }, { type: "asteroid3", q: 8, r: -4, h: 3 },
                { type: "asteroidS", q: 12, r: -7 }, { type: "dust", q: 3, r: -4 }, { type: "meteors", q: 6, r: -2 },
                { type: "dust", q: 11, r: -1 }
            ]
        },
        "fracturedFront": {
            base: "2v2", name: "Fractured Front",
            blurb: "An asteroid line splits the north and south fights; each has a small moon.",
            terrain: [
                { type: "asteroidL", q: -2, r: 0 }, { type: "asteroid2", q: -6, r: 0, h: 0 }, { type: "asteroidS", q: -10, r: 1 },
                { type: "dust", q: -8, r: 2 }, { type: "meteors", q: -4, r: 2 }, { type: "moonS", q: 0, r: 11 },
                { type: "asteroidM", q: -5, r: 12 }, { type: "dust", q: -9, r: 9 }, { type: "asteroidS", q: -3, r: 16 },
                { type: "dust", q: -11, r: 15 }, { type: "asteroidS", q: -9, r: 6 }, { type: "dust", q: -3, r: 6 },
                { type: "asteroidL", q: 1, r: 0 }, { type: "asteroid2", q: 5, r: 0, h: 3 }, { type: "asteroidS", q: 10, r: 1 },
                { type: "dust", q: 7, r: 2 }, { type: "meteors", q: 3, r: 2 }, { type: "asteroidM", q: 4, r: 12 },
                { type: "dust", q: 9, r: 9 }, { type: "asteroidS", q: 2, r: 16 }, { type: "dust", q: 11, r: 15 },
                { type: "asteroidS", q: 8, r: 6 }, { type: "dust", q: 2, r: 6 }, { type: "asteroidS", q: -10, r: -1 },
                { type: "dust", q: -8, r: -2 }, { type: "meteors", q: -4, r: -2 }, { type: "moonS", q: 0, r: -11 },
                { type: "asteroidM", q: -5, r: -12 }, { type: "dust", q: -9, r: -9 }, { type: "asteroidS", q: -3, r: -16 },
                { type: "dust", q: -11, r: -15 }, { type: "asteroidS", q: -9, r: -6 }, { type: "dust", q: -3, r: -6 },
                { type: "asteroidS", q: 10, r: -1 }, { type: "dust", q: 7, r: -2 }, { type: "meteors", q: 3, r: -2 },
                { type: "asteroidM", q: 4, r: -12 }, { type: "dust", q: 9, r: -9 }, { type: "asteroidS", q: 2, r: -16 },
                { type: "dust", q: 11, r: -15 }, { type: "asteroidS", q: 8, r: -6 }, { type: "dust", q: 2, r: -6 }
            ]
        },
        "shatteredMoon": {
            base: "large", name: "Shattered Moon",
            blurb: "A large moon ringed by its own debris, with medium moons north and south.",
            terrain: [
                { type: "moonL", q: 0, r: 1 }, { type: "moonM", q: 0, r: 13 }, { type: "asteroidS", q: -5, r: 4 },
                { type: "asteroidM", q: -6, r: 1 }, { type: "dust", q: -4, r: 5 }, { type: "asteroid2", q: -3, r: 7, h: 0 },
                { type: "meteors", q: -9, r: 3 }, { type: "asteroidL", q: -13, r: 6 }, { type: "asteroid3", q: -15, r: 11, h: 1 },
                { type: "dust", q: -12, r: 9 }, { type: "asteroidS", q: -17, r: 2 }, { type: "dust", q: -8, r: 16 },
                { type: "asteroidM", q: -5, r: 17 }, { type: "asteroidS", q: 4, r: 4 }, { type: "asteroidM", q: 6, r: 1 },
                { type: "dust", q: 4, r: 5 }, { type: "asteroid2", q: 3, r: 7, h: 3 }, { type: "meteors", q: 9, r: 3 },
                { type: "asteroidL", q: 12, r: 6 }, { type: "asteroid3", q: 15, r: 11, h: 1 }, { type: "dust", q: 12, r: 9 },
                { type: "asteroidS", q: 16, r: 2 }, { type: "dust", q: 7, r: 16 }, { type: "asteroidM", q: 5, r: 17 },
                { type: "moonM", q: 0, r: -13 }, { type: "asteroidS", q: -5, r: -4 }, { type: "asteroidM", q: -6, r: -1 },
                { type: "dust", q: -4, r: -5 }, { type: "asteroid2", q: -3, r: -7, h: 0 }, { type: "meteors", q: -9, r: -3 },
                { type: "asteroidL", q: -13, r: -6 }, { type: "asteroid3", q: -15, r: -11, h: 4 }, { type: "dust", q: -12, r: -9 },
                { type: "asteroidS", q: -17, r: -2 }, { type: "dust", q: -8, r: -16 }, { type: "asteroidM", q: -5, r: -17 },
                { type: "asteroidS", q: 4, r: -4 }, { type: "asteroidM", q: 6, r: -1 }, { type: "dust", q: 4, r: -5 },
                { type: "asteroid2", q: 3, r: -7, h: 3 }, { type: "meteors", q: 9, r: -3 }, { type: "asteroidL", q: 12, r: -6 },
                { type: "asteroid3", q: 15, r: -11, h: 4 }, { type: "dust", q: 12, r: -9 }, { type: "asteroidS", q: 16, r: -2 },
                { type: "dust", q: 7, r: -16 }, { type: "asteroidM", q: 5, r: -17 }
            ]
        },
        "meteorStorm": {
            base: "large", name: "Meteor Storm",
            blurb: "Two parallel meteor streams to cross, and small moons in opposite corners.",
            terrain: [
                { type: "meteors", q: -17, r: 15 }, { type: "meteors", q: -16, r: 13 }, { type: "dust", q: -16, r: 12 },
                { type: "meteors", q: -15, r: 10 }, { type: "meteors", q: -12, r: 7 }, { type: "meteors", q: -11, r: 5 },
                { type: "asteroidM", q: -11, r: 4 }, { type: "meteors", q: -10, r: 3 }, { type: "dust", q: -10, r: 2 },
                { type: "meteors", q: -9, r: 1 }, { type: "meteors", q: -9, r: 0 }, { type: "meteors", q: -8, r: -2 },
                { type: "meteors", q: -7, r: -3 }, { type: "dust", q: -7, r: -4 }, { type: "meteors", q: -5, r: -7 },
                { type: "dust", q: -4, r: -8 }, { type: "meteors", q: -3, r: -9 }, { type: "asteroidS", q: -3, r: -10 },
                { type: "meteors", q: -2, r: -11 }, { type: "meteors", q: -1, r: -13 }, { type: "meteors", q: -1, r: -14 },
                { type: "dust", q: 0, r: -15 }, { type: "asteroidL", q: 0, r: 1 }, { type: "dust", q: -1, r: 0 },
                { type: "moonS", q: -19, r: -9 }, { type: "asteroid2", q: -20, r: -14, h: 0 }, { type: "meteors", q: 17, r: -15 },
                { type: "meteors", q: 16, r: -13 }, { type: "dust", q: 15, r: -12 }, { type: "meteors", q: 14, r: -10 },
                { type: "meteors", q: 12, r: -7 }, { type: "meteors", q: 11, r: -5 }, { type: "asteroidM", q: 10, r: -4 },
                { type: "meteors", q: 10, r: -3 }, { type: "dust", q: 9, r: -2 }, { type: "meteors", q: 9, r: -1 },
                { type: "meteors", q: 8, r: 0 }, { type: "meteors", q: 7, r: 2 }, { type: "meteors", q: 7, r: 3 },
                { type: "dust", q: 6, r: 4 }, { type: "meteors", q: 5, r: 7 }, { type: "dust", q: 3, r: 8 },
                { type: "meteors", q: 3, r: 9 }, { type: "asteroidS", q: 2, r: 10 }, { type: "meteors", q: 2, r: 11 },
                { type: "meteors", q: 1, r: 13 }, { type: "meteors", q: 0, r: 14 }, { type: "dust", q: 0, r: 15 },
                { type: "asteroidL", q: 0, r: -1 }, { type: "dust", q: 0, r: 0 }, { type: "moonS", q: 19, r: 9 },
                { type: "asteroid2", q: 19, r: 14, h: 3 }
            ]
        },
    },

    //A template, or a Map with Terrain resolved onto its base template's size and teams.
    getMapConfig: function getMapConfig(val) {
        const config = createGame.mapData[val];
        if (!config || !config.base) return config;
        return Object.assign({}, createGame.mapData[config.base], { name: config.name, blurb: config.blurb, terrain: config.terrain });
    },

    onMapDimensionsChange: function () {
        const val = $("#mapDimensionsSelect").val();
        const mapConfig = createGame.getMapConfig(val);

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

        createGame.setTerrainLayout(mapConfig); //draws the preview
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
            $(".copy-slot-btn, .copy-team-btn").hide(); //a ladder game is one slot a team
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
            $(".copy-slot-btn, .copy-team-btn").show();
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

        // Repaint EVERY team, not just new ones: going from 2 teams to 3 switches the palette
        // (Team 2 goes from enemy red to its absolute orange), see mapPreview.teamColor().
        container.find(".team-section").each(function () {
            const rgb = "rgb(" + mapPreview.teamColor(parseInt($(this).data("team-id")), teams.length).join(",") + ")";
            this.style.setProperty("--rail", rgb);
            $(this).find(".cg-team-name").css("color", rgb);
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

    /* ── Copy Slot / Copy Team (plan §3.4) ─────────────────────────────────────────────────
       A copy of the SOURCE - its name, points, deploy turn and zone - where Add Slot / Add Team
       start from defaults. The copy lands where Add would put it: at the end of its team, or
       as the next team. Its zone is the source's own, overlapping it until moved (Add Team
       does the same with Team 1's or Team 2's zone). */
    slotFields: ["name", "points", "depx", "depy", "deptype", "depwidth", "depheight", "depavailable"],

    copySlotData: function copySlotData(source, team, id) {
        const copy = { id: id, team: team };
        createGame.slotFields.forEach(function (key) {
            if (source[key] !== undefined) copy[key] = source[key];
        });
        return copy;
    },

    nextSlotId: function nextSlotId() {
        return createGame.slots.reduce((max, slot) => Math.max(max, parseInt(slot.id, 10) || 0), 0) + 1;
    },

    //Appended the way Add Slot appends, so the button pressed stays put - and keeps focus.
    copySlot: function copySlot() {
        const source = createGame.getSlotData($(this).closest(".slot").data("slotid"));
        if (!source) return;

        const id = createGame.nextSlotId();
        const copy = createGame.copySlotData(source, source.team, id);
        createGame.slotid = id;
        createGame.slots.push(copy);
        createGame.createSlot(copy);
        createGame.drawMapPreview();
        createGame.updateSlotButtons();

        const added = $(".slot.slotid_" + id)[0];
        if (added) added.scrollIntoView({ block: "nearest" });
    },

    copyTeam: function copyTeam(teamId) {
        const sources = createGame.slots.filter(slot => slot.team === teamId);
        if (!sources.length) return;

        const team = Math.max(...createGame.getTeamIds()) + 1;
        let id = createGame.nextSlotId();
        sources.forEach(function (source) {
            const copy = createGame.copySlotData(source, team, id++);
            //A slot still named after its team - "Team 1", "Team 1 (North)" - follows the new one.
            copy.name = String(copy.name).replace(new RegExp("^Team " + teamId + "(?!\\d)"), "Team " + team);
            createGame.slots.push(copy);
        });
        createGame.slotid = id - 1;
        createGame.refreshSlotsUI();

        const added = $('#teamsContainer .team-section[data-team-id="' + team + '"]')[0];
        if (added) added.scrollIntoView({ block: "nearest" });
    },

    /* Create Game's half of each scenario field; scenarioCard.FIELDS is the shared half.
       `id` keeps the element ids the form has always had (applyMapConfig's config.scenario
       support addresses fields by them), `value` is the preselected option, and `legacy` is the
       line label in the old free-text description - see buildLegacyDescription(). */
    scenarioUI: {
        tier:              { id: "tier", value: "Tier 1", placeholder: "Enter power level...", legacy: "EXPECTED POWER LEVEL" },
        fleetRequirements: { id: "req", placeholder: "Enter requirements...", legacy: "REQUIREMENTS" },
        customFactions:    { id: "customfactions", value: "Not allowed", legacy: "CUSTOM FACTIONS / UNITS" },
        forbiddenFactions: { id: "forbidden", value: "None", legacy: "FORBIDDEN FACTIONS" },
        enhancements:      { id: "enhancements", placeholder: "Enter points...", legacy: "ENHANCEMENTS" },
        mapBorders:        { id: "borders", legacy: "MAP BORDERS" },
        victoryConditions: { id: "victory", placeholder: "Enter victory conditions...", legacy: "VICTORY CONDITIONS" },
        additionalInfo:    { id: "other", legacy: "ADDITIONAL INFO" }
    },

    //The old description's line order - NOT FIELDS order. Called Shots is gone (plan §3.3).
    legacyDescriptionOrder: ["fleetRequirements", "customFactions", "enhancements", "tier",
        "forbiddenFactions", "mapBorders", "victoryConditions", "additionalInfo"],

    renderScenarioFields: function renderScenarioFields() {
        const esc = scenarioCard.escapeHtml;
        let html = "";

        scenarioCard.FIELDS.forEach(function (field) {
            const ui = createGame.scenarioUI[field.key];
            const id = ui.id;
            let control;

            if (field.options) {
                control = '<select id="' + id + '" class="cg-input">' + field.options.map(function (opt) {
                    return '<option value="' + esc(opt) + '"' + (opt === ui.value ? " selected" : "") + ">" + esc(opt) + "</option>";
                }).join("") + "</select>";
            } else if (field.multiline) {
                control = '<textarea id="' + id + '" class="cg-input" rows="3" maxlength="4000"></textarea>';
            } else {
                control = '<input type="text" id="' + id + '" class="cg-input" maxlength="200" value="' + esc(ui.value || "") + '">';
            }

            //"Other" / "Up to X points" reveal a second input, collapsed until chosen.
            let reveal = "";
            if (field.otherKey || field.pointsKey) {
                const extra = field.pointsKey
                    ? '<input type="text" id="' + id + '_custom" class="cg-input cg-input--points" inputmode="numeric" pattern="[0-9]*" maxlength="6" placeholder="' + esc(ui.placeholder) + '">'
                    : '<input type="text" id="' + id + '_custom" class="cg-input" maxlength="200" placeholder="' + esc(ui.placeholder) + '">';
                reveal = '<div class="cg-reveal" id="' + id + '_reveal"><div class="cg-reveal-inner">' + extra + "</div></div>";
            }

            //"?" opens a floating help window over the card (the mockup's bubble) - a click/tap
            //toggle, so it works the same on a phone as with a mouse. positionHelp() places it.
            let helpButton = "", helpText = "";
            if (field.help) {
                helpButton = '<button type="button" class="cg-help" aria-expanded="false" aria-controls="' + id + '_help">'
                    + '<span aria-hidden="true">?</span><span class="cg-sr">About ' + esc(field.label) + "</span></button>";
                helpText = '<div class="cg-help-bubble" id="' + id + '_help" hidden>' + esc(field.help) + "</div>";
            }

            //One grid cell each, Additional Info included, which puts it under Map Borders beside
            //Victory Conditions (the mockup). FIELDS' `wide` is for the read-only fact grid only.
            const optional = field.multiline ? ' <span class="cg-optional">(optional)</span>' : "";
            html += '<div class="cg-scn-card">'
                + '<div class="cg-scn-head"><label for="' + id + '" class="cg-card-label">' + esc(field.label) + optional + "</label>" + helpButton + "</div>"
                + helpText + control + reveal
                + "</div>";
        });

        $("#scenarioFields").html(html);

        scenarioCard.FIELDS.forEach(function (field) {
            if (!field.otherKey && !field.pointsKey) return;
            const id = createGame.scenarioUI[field.key].id;
            const trigger = field.pointsKey ? "Up to X points" : "Other";

            $("#" + id).on("change", function (e) {
                const open = $(this).val() === trigger;
                $("#" + id + "_reveal").toggleClass("is-open", open);
                //Focus only when a PERSON chose it - applyMapConfig triggers this too.
                if (open && e.originalEvent) $("#" + id + "_custom").trigger("focus");
            }).trigger("change");
        });

        //Digits only, typed or pasted - a points cap.
        $("#enhancements_custom").on("input", function () {
            const digits = this.value.replace(/[^0-9]/g, "");
            if (digits !== this.value) this.value = digits;
        });

        //One help window open at a time; a click anywhere else, or Escape, closes it.
        $("#scenarioFields").on("click", ".cg-help", function () {
            const wasOpen = createGame.openHelpButton === this;
            createGame.closeHelp();
            if (!wasOpen) createGame.openHelp(this);
        });
        $(document).on("click", function (e) {
            if (!$(e.target).closest(".cg-help, .cg-help-bubble").length) createGame.closeHelp();
        }).on("keydown", function (e) {
            const button = createGame.openHelpButton;
            if (e.key !== "Escape" || !button) return;
            createGame.closeHelp();
            button.focus();
        });
        $(window).on("resize", function () {
            if (createGame.openHelpButton) createGame.positionHelp(createGame.openHelpButton);
        });
    },

    openHelpButton: null,

    openHelp: function openHelp(button) {
        $(button).attr("aria-expanded", "true");
        $("#" + $(button).attr("aria-controls")).prop("hidden", false);
        createGame.openHelpButton = button;
        createGame.positionHelp(button);
    },

    closeHelp: function closeHelp() {
        const button = createGame.openHelpButton;
        if (!button) return;
        $(button).attr("aria-expanded", "false");
        $("#" + $(button).attr("aria-controls")).prop("hidden", true);
        createGame.openHelpButton = null;
    },

    //Just under the "?", arrow pointing at it, slid left as far as it must to stay inside the
    //card - on a phone the "?" can sit nearer the card's right edge than the window is wide.
    //Offsets are from the card's padding box, which is what `left`/`top` are measured from.
    positionHelp: function positionHelp(button) {
        const bubble = document.getElementById($(button).attr("aria-controls"));
        const card = button.closest(".cg-scn-card");
        const cardBox = card.getBoundingClientRect();
        const glyph = button.querySelector("[aria-hidden]").getBoundingClientRect();
        const centre = glyph.left + glyph.width / 2 - cardBox.left - card.clientLeft;
        const maxLeft = Math.max(8, card.clientWidth - bubble.offsetWidth - 8);
        const left = Math.min(Math.max(8, centre - 16), maxLeft);

        bubble.style.left = left + "px";
        bubble.style.top = (glyph.bottom - cardBox.top - card.clientTop + 9) + "px";
        bubble.style.setProperty("--cg-help-arrow", (centre - left) + "px");
    },

    //The form -> the scenario object stored in tac_game.scenario (via scenarioCard.normalise).
    //An "Other"/"Up to X points" text is only read while that choice is selected.
    readScenario: function readScenario() {
        const raw = {};
        scenarioCard.FIELDS.forEach(function (field) {
            const id = createGame.scenarioUI[field.key].id;
            const value = String($("#" + id).val() || "");
            raw[field.key] = value;

            if (field.otherKey && value === "Other") raw[field.otherKey] = $("#" + id + "_custom").val();
            if (field.pointsKey && value === "Up to X points") raw[field.pointsKey] = $("#" + id + "_custom").val();
        });
        return scenarioCard.normalise(raw);
    },

    /* The old free-text tac_game.description, still written beside the JSON: gamelobby.php
       parses it (until the structured render replaces that, plan Stage 4) and its inline JS
       regex-matches "CUSTOM FACTIONS / UNITS: Allowed". Same labels, same line order, same
       "Up to Npts allowed" wording as the code this replaced - only the Called Shots line is gone. */
    buildLegacyDescription: function buildLegacyDescription(scenario) {
        let result = "*** SCENARIO DESCRIPTION ***\n";
        createGame.legacyDescriptionOrder.forEach(function (key) {
            const field = scenarioCard.FIELDS.find(f => f.key === key);
            let value = scenario[key] || "";
            if (field.pointsKey && value === "Up to X points" && scenario[field.pointsKey]) {
                value = "Up to " + scenario[field.pointsKey] + "pts allowed";
            } else {
                value = scenarioCard.factValue(field, scenario);
            }
            result += createGame.scenarioUI[key].legacy + ": " + value + "\n";
        });
        return result;
    },

    /* ── The wizard (plan §3.1 / §3.5) ─────────────────────────────────────────────────────
       One form, one POST: the four steps are the .cg-step-panel sections, one shown at a time.
       Every tab stays clickable, but going FORWARD - by Next or by a later tab - checks each step
       being passed first, and stops on the first one that is not ready, saying why. Going back
       never checks anything. */
    currentStep: 1,

    nextLabels: { 1: "Next: Scenario Description", 2: "Next: Teams & Map", 3: "Next: Summary & Confirm" },

    initWizard: function initWizard() {
        $(".cg-step").on("click", function () {
            createGame.goToStep(parseInt($(this).data("step"), 10));
        });
        $("#cgNext").on("click", function () { createGame.goToStep(createGame.currentStep + 1); });
        $("#cgBack").on("click", function () { createGame.goToStep(createGame.currentStep - 1); });
        $("#cgStep4").on("click", "[data-goto]", function () {
            createGame.goToStep(parseInt($(this).data("goto"), 10));
        });

        //A field that was flagged clears its flag once it is edited.
        $("#createGameForm").on("input change", "[aria-invalid='true']", function () {
            $(this).removeAttr("aria-invalid");
        });

        createGame.showStep(1, false);
    },

    goToStep: function goToStep(target) {
        target = Math.max(1, Math.min(4, target));

        for (let step = createGame.currentStep; step < target; step++) {
            const problem = createGame.validateStep(step);
            if (problem) {
                if (step !== createGame.currentStep) createGame.showStep(step, true);
                createGame.showStepError(problem);
                return false;
            }
        }

        createGame.showStep(target, true);
        return true;
    },

    //`moved`: a person changed step - scroll the wizard's top into view and move focus to the
    //new step's heading, so a screen reader announces it and Tab carries on from there.
    showStep: function showStep(step, moved) {
        createGame.clearStepError();
        createGame.closeHelp();
        createGame.closePresetMenu(false);
        createGame.closeSavePanel(false);
        if (step === 4) createGame.renderSummary();
        createGame.currentStep = step;

        $(".cg-step-panel").each(function () {
            const on = parseInt($(this).data("step"), 10) === step;
            this.hidden = !on;
            if (on && moved) {
                //restart the entry animation
                this.classList.remove("is-entering");
                void this.offsetWidth;
                this.classList.add("is-entering");
            }
        });

        $(".cg-step").each(function () {
            const tab = parseInt($(this).data("step"), 10);
            $(this).toggleClass("is-current", tab === step).toggleClass("is-done", tab < step);
            if (tab === step) $(this).attr("aria-current", "step");
            else $(this).removeAttr("aria-current");
        });

        $("#cgCancel").prop("hidden", step !== 1);
        $("#cgBack").prop("hidden", step === 1);
        $("#cgNext").prop("hidden", step === 4);
        $("#cgSave").prop("hidden", step !== 4);
        $("#cgConfirm").prop("hidden", step !== 4);
        $("#cgNext .cg-next-long").text(createGame.nextLabels[step] || "");

        if (moved) {
            const top = $(".cg-steps")[0].getBoundingClientRect().top;
            if (top < 0) window.scrollBy(0, top - 8);
            $("#cgStep" + step + " .cg-section-head").trigger("focus");
        }
    },

    /* What stops a step being left, as {message, field} - or null. Deliberately short: only what
       would otherwise reach the lobby blank or broken. */
    validateStep: function validateStep(step) {
        if (step === 1) {
            if (!String($("#gamename").val() || "").trim()) {
                return { message: "Give the game a name.", field: "#gamename" };
            }
            //A private game with no password could never be joined.
            if ($("#privateGameCheck").is(":checked") && createGame.readPassword() === null) {
                return { message: "Private Game: enter a password, or untick Require password.", field: "#gamePassword" };
            }
            //A short year would lock the lobby's ISD filter below every unit there is.
            const isd = String($("#inServiceDate").val() || "").trim();
            if ($("#inServiceDateCheck").is(":checked") && !(/^\d{4}$/.test(isd) && parseInt(isd, 10) >= 1000)) {
                return { message: "In-Service Date: enter a four-digit year, or untick In-Service Date.", field: "#inServiceDate" };
            }
        }

        if (step === 2) {
            for (const field of scenarioCard.FIELDS) {
                if (!field.otherKey && !field.pointsKey) continue;
                const id = createGame.scenarioUI[field.key].id;
                const choice = $("#" + id).val();
                const custom = String($("#" + id + "_custom").val() || "").trim();

                if (field.pointsKey && choice === "Up to X points" && !custom) {
                    return { message: field.label + ": enter the points limit, or choose another option.", field: "#" + id + "_custom" };
                }
                if (field.otherKey && choice === "Other" && !custom) {
                    return { message: field.label + ": describe your “Other” choice, or pick one of the options.", field: "#" + id + "_custom" };
                }
            }
        }

        if (step === 3) {
            if ($("#mapDimensionsSelect").val() !== "unlimited"
                && !(createGame.gamespace_data.width > 0 && createGame.gamespace_data.height > 0)) {
                return { message: "Give the map a width and height, or choose No Boundaries.", field: "#spacex" };
            }
            const unnamed = createGame.slots.find(slot => !String(slot.name || "").trim());
            if (unnamed) {
                return { message: "Every slot needs a name.", field: ".slot.slotid_" + unnamed.id + " [name='name']" };
            }
        }

        return null;
    },

    showStepError: function showStepError(problem) {
        $("#cgStepError").text(problem.message).prop("hidden", false);
        const field = $(problem.field).first();
        if (field.length) field.attr("aria-invalid", "true").trigger("focus");
    },

    clearStepError: function clearStepError() {
        $("#cgStepError").empty().prop("hidden", true);
        $("#createGameForm [aria-invalid='true']").removeAttr("aria-invalid");
    },

    //The In-Service Date cutoff as a year, or null for none (unticked, or blank). It is not a rule: it
    //is posted on its own and stored in tac_game.in_service_date (plan §4.4).
    readInServiceDate: function readInServiceDate() {
        if (!$("#inServiceDateCheck").is(":checked")) return null;
        const year = parseInt(String($("#inServiceDate").val() || "").trim(), 10);
        return year > 0 ? year : null;
    },

    showInServiceDate: function showInServiceDate() {
        $("#inServiceDateWrap").toggle($("#inServiceDateCheck").is(":checked"));
    },

    /* ── Private Game (plan §3.2, Stage 8) ──────────────────────────────────────────────────────
       Ticked, the game is created with a password (tac_game.password_hash); players need it to
       open the lobby. It is posted on its own, like the In-Service Date - not a rule - and it is
       never saved with the settings (Save Settings keeps only the tick). */
    initPrivateGame: function initPrivateGame() {
        $("#privateGameCheck").on("change", function () {
            createGame.showPrivatePassword();
            if (this.checked) $("#gamePassword").trigger("focus");
        });

        //Shown, so the creator can check what they are about to hand on - it cannot be changed later.
        $("#gamePasswordShow").on("click", function () {
            const input = $("#gamePassword");
            const show = input.attr("type") === "password";
            input.attr("type", show ? "text" : "password");
            $(this).text(show ? "Hide" : "Show");
        });

        createGame.showPrivatePassword(); //a restored form may arrive ticked
    },

    showPrivatePassword: function showPrivatePassword() {
        $("#privatePasswordWrap").prop("hidden", !$("#privateGameCheck").is(":checked"));
    },

    /* The password as the server will store it - trimmed (Manager::normaliseGamePassword does the
       same, and the lobby's password form too) - or null: not private, or nothing typed. */
    readPassword: function readPassword() {
        if (!$("#privateGameCheck").is(":checked")) return null;
        const password = String($("#gamePassword").val() || "").trim();
        return password === "" ? null : password;
    },

    //Step 4: a read-only recap, grouped the way the lobby will show the game.
    renderSummary: function renderSummary() {
        const background = $("input[name='background']:checked");
        $("#sumBackground").attr("src", background.length ? "img/maps/" + background.val() : "");
        $("#sumName").text(String($("#gamename").val() || "").trim());
        $("#sumBackgroundName").text("Background: " + (background.closest(".cg-bg-tile").attr("title") || background.val() || ""));

        //The lobby's Game Rules chips, from the same function, so this is exactly what it will show.
        $("#sumRules").html(scenarioCard.renderRuleChips(scenarioCard.ruleChips(createGame.rules, {
            unlimitedPoints: $("#unlimitedPointsCheck").is(":checked"),
            inServiceDate: createGame.readInServiceDate(),
            isPrivate: createGame.readPassword() !== null
        })));

        $("#sumScenario").html(scenarioCard.render(createGame.readScenario(), { plain: true })
            || '<p class="cg-caption">No scenario details.</p>');

        createGame.paintMap(document.getElementById("sumMap"));
        $("#sumMapMeta").html(createGame.summaryMap());
        $("#sumTeams").html(createGame.summaryTeams());
    },

    summaryMap: function summaryMap() {
        const esc = scenarioCard.escapeHtml;
        const template = $("#mapDimensionsSelect").val();
        let text;

        if (template === "unlimited") text = "No Boundaries";
        else if (template === "custom") text = "Custom map, " + createGame.gamespace_data.width + " × " + createGame.gamespace_data.height;
        else text = $("#mapDimensionsSelect option:selected").text();

        let html = esc(text);
        if (createGame.rules.terrainLayout) {
            html += ' <span class="cg-sum-sep">&middot;</span> ' + createGame.rules.terrainLayout.units.length + " pre-placed terrain features";
        }
        return html;
    },

    summaryTeams: function summaryTeams() {
        const esc = scenarioCard.escapeHtml;
        const teams = createGame.getTeamIds();
        const unlimited = $("#unlimitedPointsCheck").is(":checked");

        return teams.map(function (team) {
            const rgb = "rgb(" + mapPreview.teamColor(team, teams.length).join(",") + ")";
            const slots = createGame.slots.filter(slot => slot.team === team);
            const rows = slots.map(function (slot) {
                let meta = (unlimited || slot.points == -1) ? "Unlimited" : esc(slot.points) + " pts";
                if (parseInt(slot.depavailable, 10) > 1) meta += " &middot; deploys turn " + esc(slot.depavailable);
                return '<li><span class="cg-sum-slot-name">' + esc(slot.name) + '</span><span class="cg-sum-slot-meta">' + meta + "</span></li>";
            }).join("");

            return '<div class="cg-sum-team" style="--rail:' + rgb + '">'
                + '<div class="cg-sum-team-head" style="color:' + rgb + '">Team ' + team + " &middot; " + slots.length + (slots.length === 1 ? " slot" : " slots") + "</div>"
                + '<ul class="cg-sum-slots">' + rows + "</ul></div>";
        }).join("");
    },

    /* ── Saved settings (plan §3.2) ────────────────────────────────────────────────────────
       The whole form, saved by name in THIS browser (localStorage - no account, no server: the
       plan's v1) and loaded back onto it. Load sits beside Game Name; Save sits beside Confirm,
       so only a form that has passed every step's checks is ever saved.
       A preset is the FORM, not the posted data: loading replays it through the page's own
       handlers, so the rules, a template's pre-placed terrain and the map preview are rebuilt
       exactly as if it had been entered by hand. Only the slots are put back as data.
       ⚠️ localStorage throws outright in some privacy modes: every access is wrapped, and a
       failure just means an empty list (Load) or a message (Save). */
    PRESET_KEY: "fv.createGamePresets.v1",
    PRESET_VERSION: 1,

    presetName: "", //the preset last loaded or saved - what Save offers as the name
    navStatusTimer: null,

    //Rule checkboxes -> the handler that turns each into rules. Ladder and Unlimited Points are
    //read the same way but applied apart: both rewrite the slots (see applySettings).
    presetChecks: {
        movementcheck: "doMovementCheck",
        allowMinesCheck: "doAllowMinesCheck",
        allowReinforcementsCheck: "doAllowReinforcementsCheck",
        desperatecheck: "doDesperateCheck",
        friendlyFireCheck: "doFriendlyFireCheck"
    },
    presetSelects: ["initiativeSelect", "desperateSelect"],

    readPresets: function readPresets() {
        try {
            const list = JSON.parse(window.localStorage.getItem(createGame.PRESET_KEY) || "[]");
            if (!Array.isArray(list)) return [];
            return list.filter(preset => preset && typeof preset.name === "string" && preset.settings && typeof preset.settings === "object");
        } catch (e) {
            return [];
        }
    },

    writePresets: function writePresets(list) {
        try {
            window.localStorage.setItem(createGame.PRESET_KEY, JSON.stringify(list));
            return true;
        } catch (e) {
            return false;
        }
    },

    //One name whatever its case or spacing: "2v2 league" and "2V2 League " are the same preset.
    presetKey: function presetKey(name) {
        return String(name || "").trim().toLowerCase();
    },

    findPreset: function findPreset(list, name) {
        const key = createGame.presetKey(name);
        return list.findIndex(preset => createGame.presetKey(preset.name) === key);
    },

    //The form as it stands - by element id wherever the element is the only record of a value.
    readSettings: function readSettings() {
        const checks = {};
        ["laddercheck", "unlimitedPointsCheck"].concat(Object.keys(createGame.presetChecks)).forEach(function (id) {
            checks[id] = $("#" + id).is(":checked");
        });
        const selects = {};
        createGame.presetSelects.forEach(function (id) { selects[id] = $("#" + id).val(); });
        const terrain = {};
        $(".cg-terrain-count").each(function () { terrain[this.id] = parseInt(this.value, 10) || 0; });

        return {
            v: createGame.PRESET_VERSION,
            gamename: String($("#gamename").val() || ""),
            background: $("input[name='background']:checked").val() || "",
            //blank while unticked, so a saved year always means ticked (and older settings read the same)
            inServiceDate: $("#inServiceDateCheck").is(":checked") ? String($("#inServiceDate").val() || "").trim() : "",
            //the tick only - a password never goes into localStorage
            privateGame: $("#privateGameCheck").is(":checked"),
            checks: checks,
            selects: selects,
            terrain: terrain,
            scenario: createGame.readScenario(),
            map: { template: $("#mapDimensionsSelect").val(), width: createGame.gamespace_data.width, height: createGame.gamespace_data.height },
            slots: createGame.slots.map(slot => createGame.copySlotData(slot, slot.team, slot.id))
        };
    },

    /* A saved form back onto the page. The ORDER matters: the map template resets the teams,
       Unlimited Points resets every slot's points, and Ladder prunes the slots and greys out
       maps - so the slots go back after the first two, and Ladder goes last. Anything the page
       no longer offers (a background, a template, an option) is skipped, not forced. */
    applySettings: function applySettings(settings) {
        const checked = id => !!(settings.checks && settings.checks[id]);
        const choose = function (select, value) {
            if (value == null) return false;
            const offered = select.find("option").filter(function () { return this.value === String(value); });
            if (!offered.length) return false;
            select.val(String(value));
            return true;
        };

        createGame.clearStepError();

        //Ladder off first, so no map is greyed out while the template is chosen.
        if ($("#laddercheck").is(":checked")) {
            $("#laddercheck").prop("checked", false);
            createGame.doLadderCheck();
        }

        if (typeof settings.gamename === "string" && settings.gamename.trim()) $("#gamename").val(settings.gamename);

        //Blank for settings saved before the field existed - they had no cutoff. A year means ticked.
        const inServiceDate = settings.inServiceDate == null ? "" : String(settings.inServiceDate).replace(/\D/g, "").slice(0, 4);
        $("#inServiceDate").val(inServiceDate);
        $("#inServiceDateCheck").prop("checked", inServiceDate !== "");
        createGame.showInServiceDate();

        //Private: the tick comes back, the password does not (it was never saved) - one already typed
        //stays, and Next asks for one if the box is empty. Settings saved before Stage 8 were public.
        $("#privateGameCheck").prop("checked", settings.privateGame === true);
        if (settings.privateGame !== true) $("#gamePassword").val("");
        createGame.showPrivatePassword();

        const background = $("input[name='background']").filter(function () { return this.value === settings.background; });
        if (background.length) {
            background.prop("checked", true);
            createGame.mapSelect();
            const strip = background.closest(".cg-bg-strip")[0];
            const tile = background.closest(".cg-bg-tile")[0];
            strip.scrollLeft += tile.getBoundingClientRect().left - strip.getBoundingClientRect().left - 2;
        }

        //Scenario: a select's own change handler opens or closes its "Other" box.
        const scenario = settings.scenario || {};
        scenarioCard.FIELDS.forEach(function (field) {
            const ui = createGame.scenarioUI[field.key];
            const el = $("#" + ui.id);
            if (field.options) {
                if (choose(el, scenario[field.key])) el.trigger("change");
            } else {
                el.val(scenario[field.key] != null ? scenario[field.key] : (ui.value || ""));
            }
            const customKey = field.otherKey || field.pointsKey;
            if (customKey) $("#" + ui.id + "_custom").val(scenario[customKey] || "");
        });

        //Terrain: each count clamps itself on change, and readTerrain() follows it.
        $(".cg-terrain-count").each(function () {
            const count = settings.terrain ? parseInt(settings.terrain[this.id], 10) : 0;
            $(this).val(count > 0 ? count : 0).trigger("change");
        });

        //Map: a template brings its size, teams and any pre-placed terrain; a Custom map - or a
        //template this page no longer has - takes the saved size.
        const map = settings.map || {};
        const template = $("#mapDimensionsSelect");
        if (!choose(template, map.template)) template.val("custom");
        createGame.onMapDimensionsChange();
        const width = parseInt(map.width, 10), height = parseInt(map.height, 10);
        if (template.val() === "custom" && width > 0 && height > 0) {
            createGame.gamespace_data.width = width;
            createGame.gamespace_data.height = height;
            $(".spacex").val(width);
            $(".spacey").val(height);
        }

        const unlimited = checked("unlimitedPointsCheck");
        if ($("#unlimitedPointsCheck").is(":checked") !== unlimited) {
            $("#unlimitedPointsCheck").prop("checked", unlimited).trigger("change");
        }

        const slots = createGame.presetSlots(settings.slots);
        if (slots) {
            createGame.slots = slots;
            createGame.slotid = slots.length;
        }
        createGame.refreshSlotsUI();

        createGame.presetSelects.forEach(id => choose($("#" + id), settings.selects && settings.selects[id]));
        Object.keys(createGame.presetChecks).forEach(function (id) {
            $("#" + id).prop("checked", checked(id));
            createGame[createGame.presetChecks[id]]();
        });

        if (checked("laddercheck")) {
            $("#laddercheck").prop("checked", true);
            createGame.doLadderCheck();
        }
    },

    //Saved slots, checked over and numbered 1..n in their saved order (the creator takes slot 1).
    //Null - keep the template's own - unless they still make up Teams 1 and 2.
    presetSlots: function presetSlots(saved) {
        if (!Array.isArray(saved) || !saved.length) return null;
        const int = function (value, fallback) {
            const n = parseInt(value, 10);
            return isNaN(n) ? fallback : n;
        };

        const slots = saved.filter(slot => slot && typeof slot === "object")
            .sort((a, b) => int(a.id, 0) - int(b.id, 0))
            .map(function (slot, i) {
                return {
                    id: i + 1, team: int(slot.team, 0), name: String(slot.name == null ? "" : slot.name),
                    points: int(slot.points, 0), depx: int(slot.depx, 0), depy: int(slot.depy, 0), deptype: "box",
                    depwidth: int(slot.depwidth, 0), depheight: int(slot.depheight, 0),
                    depavailable: Math.max(1, int(slot.depavailable, 1))
                };
            });

        const teams = new Set(slots.map(slot => slot.team));
        if (slots.some(slot => slot.team < 1) || !teams.has(1) || !teams.has(2)) return null;
        return slots;
    },

    initPresets: function initPresets() {
        $("#cgLoadToggle").on("click", function () {
            if ($("#cgPresetMenu").prop("hidden")) createGame.openPresetMenu();
            else createGame.closePresetMenu(false);
        });
        $("#cgPresetList").on("click", ".cg-preset-load", function () {
            createGame.loadPreset($(this).closest(".cg-preset").data("name"));
        }).on("click", ".cg-preset-delete", function () {
            createGame.deletePreset($(this).closest(".cg-preset").data("name"));
        });

        //Escape, a click or tap elsewhere, or Tab out of it closes the menu - but not a click in
        //the delete confirmation, which is a dialog of its own (.confirm) over the page.
        $(".cg-presets").on("keydown", function (e) {
            if (e.key === "Escape" && !$("#cgPresetMenu").prop("hidden")) {
                e.preventDefault();
                createGame.closePresetMenu(true);
            }
        }).on("focusout", function (e) {
            const to = e.relatedTarget;
            if (to && !this.contains(to) && !$(to).closest(".confirm").length) createGame.closePresetMenu(false);
        });
        $(document).on("mousedown touchstart", function (e) {
            if (!$(e.target).closest(".cg-presets, .confirm").length) createGame.closePresetMenu(false);
        });

        $("#cgSave").on("click", function () {
            if ($("#cgSavePanel").prop("hidden")) createGame.openSavePanel();
            else createGame.closeSavePanel(true);
        });
        $("#cgSaveConfirm").on("click", createGame.savePreset);
        $("#cgSaveCancel").on("click", function () { createGame.closeSavePanel(true); });
        $("#cgPresetName").on("input", createGame.updateSaveNote).on("keydown", function (e) {
            //⚠️ Enter must not reach the form: on this step its implicit submit CREATES the game.
            if (e.key === "Enter") {
                e.preventDefault();
                createGame.savePreset();
            } else if (e.key === "Escape") {
                e.preventDefault();
                createGame.closeSavePanel(true);
            }
        });
    },

    openPresetMenu: function openPresetMenu() {
        createGame.renderPresetList();
        $("#cgPresetMenu").prop("hidden", false);
        $("#cgLoadToggle").attr("aria-expanded", "true");
    },

    closePresetMenu: function closePresetMenu(refocus) {
        if ($("#cgPresetMenu").prop("hidden")) return;
        $("#cgPresetMenu").prop("hidden", true);
        $("#cgLoadToggle").attr("aria-expanded", "false");
        if (refocus) $("#cgLoadToggle").trigger("focus");
    },

    //Newest first. A row loads on a click; its × deletes it, after a confirmation.
    renderPresetList: function renderPresetList() {
        const list = createGame.readPresets();
        const ul = $("#cgPresetList").empty();
        if (!list.length) {
            $('<li class="cg-preset-empty"></li>').text("No saved settings yet. Save them with Save Settings on the Confirm step.").appendTo(ul);
            return;
        }

        list.forEach(function (preset) {
            const saved = new Date(preset.saved);
            const date = isNaN(saved.getTime()) ? "" : saved.toLocaleDateString(undefined, { day: "numeric", month: "short", year: "numeric" });
            const meta = [typeof preset.mapLabel === "string" ? preset.mapLabel : "", date].filter(Boolean).join(" · ");

            const row = $('<li class="cg-preset"></li>').data("name", preset.name);
            $('<button type="button" class="cg-preset-load"></button>')
                .append($('<span class="cg-preset-name"></span>').text(preset.name))
                .append($('<span class="cg-preset-meta"></span>').text(meta))
                .appendTo(row);
            $('<button type="button" class="cg-preset-delete" title="Delete"><span aria-hidden="true">&times;</span></button>')
                .attr("aria-label", "Delete " + preset.name)
                .appendTo(row);
            ul.append(row);
        });
    },

    loadPreset: function loadPreset(name) {
        const list = createGame.readPresets();
        const index = createGame.findPreset(list, name);
        if (index < 0) { //deleted in another tab since the list was drawn
            createGame.renderPresetList();
            return;
        }

        const preset = list[index];
        createGame.closePresetMenu(true);
        try {
            createGame.applySettings(preset.settings);
        } catch (e) {
            console.error("Create Game: saved settings could not be applied", e);
            createGame.showNavStatus("Those saved settings could not be loaded in full.");
            return;
        }
        createGame.presetName = preset.name;
        createGame.showNavStatus("Loaded “" + preset.name + "”.");
    },

    deletePreset: function deletePreset(name) {
        //confirm() builds its message as HTML
        window.confirm.confirm("Delete the saved settings “" + scenarioCard.escapeHtml(name) + "”?", function () {
            const list = createGame.readPresets();
            const index = createGame.findPreset(list, name);
            if (index >= 0) {
                list.splice(index, 1);
                createGame.writePresets(list);
            }
            if (createGame.presetKey(createGame.presetName) === createGame.presetKey(name)) createGame.presetName = "";

            //The row's buttons are gone: focus the first row left, or the toggle.
            createGame.renderPresetList();
            const next = $("#cgPresetMenu").prop("hidden") ? $() : $("#cgPresetList .cg-preset-load").first();
            (next.length ? next : $("#cgLoadToggle")).trigger("focus");
        });
    },

    openSavePanel: function openSavePanel() {
        createGame.clearStepError();
        const input = $("#cgPresetName");
        input.val(createGame.presetName || String($("#gamename").val() || "").trim()).removeAttr("aria-invalid");
        $("#cgSavePanel").prop("hidden", false);
        $("#cgSave").attr("aria-expanded", "true");
        createGame.updateSaveNote();
        input[0].focus();
        input[0].select();
    },

    closeSavePanel: function closeSavePanel(refocus) {
        if ($("#cgSavePanel").prop("hidden")) return;
        $("#cgSavePanel").prop("hidden", true);
        $("#cgSave").attr("aria-expanded", "false");
        if (refocus) $("#cgSave").trigger("focus");
    },

    //A name already saved is replaced - the note and the button both say so.
    updateSaveNote: function updateSaveNote() {
        const list = createGame.readPresets();
        const index = createGame.findPreset(list, $("#cgPresetName").val());
        $("#cgSaveNote").text((index >= 0 ? "Replaces your saved “" + list[index].name + "”." : "Saved in this browser only.")
            + ($("#privateGameCheck").is(":checked") ? " The password is not saved." : ""));
        $("#cgSaveConfirm").text(index >= 0 ? "Replace" : "Save");
    },

    savePreset: function savePreset() {
        const input = $("#cgPresetName");
        const name = String(input.val() || "").trim();
        if (!name) {
            $("#cgSaveNote").text("Give these settings a name.");
            input.attr("aria-invalid", "true").trigger("focus");
            return;
        }

        const list = createGame.readPresets();
        const index = createGame.findPreset(list, name);
        if (index >= 0) list.splice(index, 1);
        list.unshift({ name: name, saved: Date.now(), mapLabel: createGame.presetMapLabel(), settings: createGame.readSettings() });

        if (!createGame.writePresets(list)) {
            $("#cgSaveNote").text("This browser would not store them (private browsing, or its storage is full).");
            return;
        }
        createGame.presetName = name;
        createGame.closeSavePanel(true);
        createGame.showNavStatus("Settings saved as “" + name + "”.");
    },

    presetMapLabel: function presetMapLabel() {
        if ($("#mapDimensionsSelect").val() === "custom") {
            return "Custom " + createGame.gamespace_data.width + "x" + createGame.gamespace_data.height;
        }
        return $("#mapDimensionsSelect option:selected").text();
    },

    //A short confirmation floating just above the nav bar.
    showNavStatus: function showNavStatus(text) {
        clearTimeout(createGame.navStatusTimer);
        $("#cgNavStatus").text(text);
        createGame.navStatusTimer = setTimeout(function () { $("#cgNavStatus").empty(); }, 4000);
    },

    submitFleetTest: function () {
        createGame.isFleetTest = true;
        $("#createGameForm button[type='submit']").trigger("mousedown"); // Trigger validation/allowSubmit flag if needed
        $("#createGameForm").submit();
    },

    setData: function setData() {
        var gamename = $("#gamename").val();
        var background = $("input[name='background']:checked").val();
        var scenario = createGame.readScenario();
        var description = createGame.buildLegacyDescription(scenario);
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

        var data = { gamename: gamename, background: background, slots: createGame.slots, gamespace: gamespace, flight: flight, rules: createGame.rules, description: description, scenario: scenario, inServiceDate: createGame.readInServiceDate(), password: createGame.readPassword() };
        data = JSON.stringify(data);
        $("#createGameData").val(data);
    }
};
