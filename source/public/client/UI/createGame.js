"use strict";

jQuery(function ($) {
    $("#backgroundSelect").on("change", createGame.mapSelect);
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
    $(".slotcontainer").on("click", ".close", createGame.removeSlot);

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

    createGame.createSlotsFromArray();
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

        const centerX = offsetX + (mapWidth / 2) * scale;
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
        $(".slot").each(function () {
            const slot = $(this);
            const slotId = slot.data("slotid");
            const data = createGame.getSlotData(slotId);
            if (!data) return;
            const team = data.team;

            const x = parseInt(data.depx) || 0;
            const y = parseInt(data.depy) || 0;
            const w = parseInt(data.depwidth) || 0;
            const h = parseInt(data.depheight) || 0;

            // Deployment color based on team
            let color = team === 1 ? "rgba(50, 200, 50, 0.4)" : "rgba(200, 50, 50, 0.4)";
            let borderColor = team === 1 ? "#66ff66" : "#ff6666";

            if (team > 2) {
                color = "rgba(50, 50, 200, 0.4)";
                borderColor = "#6666ff";
            }

            ctx.fillStyle = color;
            ctx.strokeStyle = borderColor;
            ctx.lineWidth = 1;

            // Adjust position to treat (x, y) as center
            // coordinate system: center of map is (0,0)
            // canvas origin is topleft

            // Map coord to Canvas coord:
            // MapX [-width/2, width/2] -> CanvasX [offsetX, offsetX + width*scale]
            // MapY [-height/2, height/2] -> CanvasY [offsetY + height*scale, offsetY] (flipped Y if we want standard cartesian, but usually screen coords are Y down)

            // Existing logic assumed typical screen coords where Y increases downwards? 
            // Let's verify standard B5W coord system... usually standard cartesian, but web canvas is Y-down.
            // Looking at existing code: 
            // const drawY = offsetY + (mapHeight / 2 - y - h / 2) * scale;
            // This suggests Y grows UPWARDS in B5W coords (standard math), so we subtract Y from center.

            const drawX = offsetX + (x - w / 2 + mapWidth / 2) * scale;
            // If y is positive (up), on canvas it should be higher (smaller Y value)
            // CenterY on canvas is offsetY + (mapHeight/2)*scale
            // So if y=0, drawY should be CenterY - h/2*scale
            // If y=10, drawY should be CenterY - 10*scale - h/2*scale
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


    onMapDimensionsChange: function () {
        const val = $("#mapDimensionsSelect").val();

        if (val === "unlimited") {
            $(".gamespacedefinition .unlimitedspace").removeClass("invisible");
            $(".gamespacedefinition .limitedspace").addClass("invisible");
        } else {
            $(".gamespacedefinition .unlimitedspace").addClass("invisible");
            $(".gamespacedefinition .limitedspace").removeClass("invisible");

            if (val === "standard") createGame.doSwitchSizeStandard();
            else if (val === "knifefight") createGame.doSwitchSizeKnifeFight();
            else if (val === "baseassault") createGame.doSwitchSizeBaseAssault();
            // custom: do nothing, just show fields
        }

        createGame.drawMapPreview();
    },

    refreshSlotsUI: function () {
        // Simple way: clear and redraw
        $(".slotcontainer").empty();
        createGame.createSlotsFromArray();
    },


    doSwitchSizeKnifeFight: function (data) {
        createGame.gamespace_data.width = 30;
        createGame.gamespace_data.height = 24;
        $(".spacex").val(30);
        $(".spacey").val(24);

        const team1Defaults = { depx: -12, depy: 0, depwidth: 7, depheight: 24 };
        const team2Defaults = { depx: 11, depy: 0, depwidth: 7, depheight: 24 };

        for (let slot of createGame.slots) {
            if (slot.team == 1) Object.assign(slot, team1Defaults);
            else if (slot.team == 2) Object.assign(slot, team2Defaults);
        }
        createGame.refreshSlotsUI();
        createGame.drawMapPreview();
    },


    doSwitchSizeBaseAssault: function (data) {
        createGame.gamespace_data.width = 60;
        createGame.gamespace_data.height = 40;
        $(".spacex").val(60);
        $(".spacey").val(40);

        const team1Defaults = { depx: -28, depy: 0, depwidth: 5, depheight: 40 };
        const team2Defaults = { depx: 27, depy: 0, depwidth: 5, depheight: 40 };

        for (let slot of createGame.slots) {
            if (slot.team == 1) Object.assign(slot, team1Defaults);
            else if (slot.team == 2) Object.assign(slot, team2Defaults);
        }
        createGame.refreshSlotsUI();
        createGame.drawMapPreview();
    },


    doSwitchSizeStandard: function (data) {
        createGame.gamespace_data.width = 42;
        createGame.gamespace_data.height = 30;
        $(".spacex").val(42);
        $(".spacey").val(30);

        const team1Defaults = { depx: -19, depy: 0, depwidth: 5, depheight: 30 };
        const team2Defaults = { depx: 18, depy: 0, depwidth: 5, depheight: 30 };

        for (let slot of createGame.slots) {
            if (slot.team == 1) Object.assign(slot, team1Defaults);
            else if (slot.team == 2) Object.assign(slot, team2Defaults);
        }
        createGame.refreshSlotsUI();
        createGame.drawMapPreview();
    },



    createSlotsFromArray: function createSlotsFromArray() {
        for (var i in createGame.slots) {
            createGame.createSlot(createGame.slots[i]);
        }
    },

    createSlot: function createSlot(data) {
        var template = $("#slottemplatecontainer .slot");
        var target = $("#team" + data.team + ".slotcontainer");
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

        // Only update points if NOT unlimited (to avoid overwriting infinity symbol with -1)
        if (data.points != -1) {
            slot.find("[name='points']").val(data.points);
        }

        slot.find("[name='depx']").val(data.depx);
        slot.find("[name='depy']").val(data.depy);
        slot.find("[name='depwidth']").val(data.depwidth);
        slot.find("[name='depheight']").val(data.depheight);
        slot.find("[name='depavailable']").val(data.depavailable);
    },

    createNewSlot: function createNewSlot(e) {
        var team = $(this).hasClass("team1") ? 1 : 2;
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
            name: "TEAM " + team,
            points: $("#unlimitedPointsCheck").is(":checked") ? -1 : 3500,
            depx: 0,
            depy: 0,
            deptype: "box",
            depwidth: 5,
            depheight: 5,
            depavailable: 1
        };

        if (lastSlotOfTeam) {
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

        var data = { gamename: gamename, background: background, slots: createGame.slots, gamespace: gamespace, flight: flight, rules: createGame.rules, description: description };
        data = JSON.stringify(data);
        $("#createGameData").val(data);
    }
};
