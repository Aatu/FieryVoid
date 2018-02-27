"use strict";

jQuery(function ($) {
    $("#mapselect").on("change", createGame.mapSelect);
    createGame.mapSelect();

    $("input").on("change", createGame.inputChange);
    $("select").on("change", createGame.inputChange);
    $("input").on("focus", createGame.inputFocus);
    $(".addslotbutton").on("click", createGame.createNewSlot);
    $(".close").on("click", createGame.removeSlot);
    $("#createGameForm").on("submit", createGame.setData);
    $("#gamespacecheck").on("click", createGame.doGameSpaceCheck);
    $("#gamespacecheck").on("click", createGame.doFlightCheck);

    createGame.createSlotsFromArray();
});

window.createGame = {
    gamespace_data: { width: 42, height: 30 },

    slots: Array({ id: 1, team: 1, name: "BLUE", points: 3500, depx: -21, depy: 0, deptype: "box", depwidth: 10, depheight: 30, depavailable: 0 }, { id: 2, team: 2, name: "RED", points: 3500, depx: 21, depy: 0, deptype: "box", depwidth: 10, depheight: 30, depavailable: 0 }),
    slotid: 2,

    mapSelect: function mapSelect() {

        $("#default_option").remove();
        var val = $("#mapselect").val();
        $("body").css("background-image", "url(img/maps/" + val + ")");
    },

    inputFocus: function inputFocus(e) {
        var input = $(this);
        var value = input.val();
        input.data("oldvalue", value);
    },

    inputChange: function inputChange(e) {

        var input = $(this);
        var value = input.val();
        var inputname = input.attr("name");

        if (input.data("validation")) {
            var patt = new RegExp(input.data("validation"));
            if (value.lenght == 0 || !patt.test(value)) {
                input.val(input.data("oldvalue"));
                return;
            }
        }

        if (inputname == "spacex") {
            createGame.gamespace_data.width = parseInt(value);
            return;
        }

        if (inputname == "spacey") {
            createGame.gamespace_data.height = parseInt(value);
            return;
        }

        var slot = $(".slot").has($(this));
        var data = createGame.getSlotData(slot.data("slotid"));
        if (!slot || !data) return;

        data[inputname] = value;

        if (inputname == "deptype") {
            var width = $(".depwidthheader", slot);
            var height = $(".depheightheader", slot);
            var inputHeight = $(".depheight", slot);

            if (value == "box") {
                width.html("Width:");
                height.html("Height:");
                inputHeight.show();
                height.show();
            }

            if (value == "circle") {
                width.html("Radius:");
                inputHeight.hide();
                height.hide();
            }

            if (value == "distance") {
                width.html("From:");
                height.html("To:");
                inputHeight.show();
                height.show();
            }
        }
    },

    doFlightCheck: function doFlightCheck(data) {
        var checkval = $("#flightSizeCheck:checked").val();
        if (checkval == "on") {
            createGame.variableFlights = 1;
        } else createGame.variableFlights = 0;
    },

    doGameSpaceCheck: function doGameSpaceCheck(data) {
        var checkval = $("#gamespacecheck:checked").val();

        if (checkval == "on") {
            $(".gamespacedefinition .unlimitedspace").addClass("invisible");
            $(".gamespacedefinition .limitedspace").removeClass("invisible");

            $(".spacex").val(createGame.gamespace_data.width);
            $(".spacey").val(createGame.gamespace_data.height);
            $(".deptype").val("box");
            $("#team1 .depx").val(-19);
            $("#team2 .depx").val(18);
            $("#team1 .depy").val(0);
            $("#team2 .depy").val(0);
            $("#team1 .depwidth").val(5);
            $("#team2 .depwidth").val(5);
            $("#team1 .depheight").val(30);
            $("#team2 .depheight").val(30);
            createGame.slots[0].depx = -19;
            createGame.slots[1].depx = 18;
            createGame.slots[0].depy = 0;
            createGame.slots[1].depy = 0;
            createGame.slots[0].depwidth = 5;
            createGame.slots[1].depwidth = 5;
            createGame.slots[0].depheight = 30;
            createGame.slots[1].depheight = 30;
            createGame.slots[0].deptype = "box";
            createGame.slots[1].deptype = "box";
            createGame.slots[0].depavailable = 0;
            createGame.slots[1].depavailable = 0;
        } else {
            $(".gamespacedefinition .unlimitedspace").removeClass("invisible");
            $(".gamespacedefinition .limitedspace").addClass("invisible");
        }
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
        createGame.setSlotData(data);
    },

    setSlotData: function setSlotData(data) {
        var slot = $(".slot.slotid_" + data.id);
        $(".name", slot).val(data.name);
        $(".points", slot).val(data.points);

        $(".depx", slot).val(data.depx);
        $(".depy", slot).val(data.depy);
        $(".deptype", slot).val(data.deptype);
        $(".depwidth", slot).val(data.depwidth);
        $(".depheight", slot).val(data.depheight);
        $(".depavailable", slot).val(data.depavailable);
    },

    createNewSlot: function createNewSlot(e) {
        var team = $(this).hasClass("team1") ? 1 : 2;
        createGame.slotid++;
        var data = { id: createGame.slotid, team: team, name: "RED", points: 3500, depx: 0, depy: 0, deptype: "box", depwidth: 0, depheight: 0, depavailable: 0 };
        createGame.slots.push(data);
        createGame.createSlot(data);
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
        var slot = $(".slot").has($(this));
        var data = createGame.getSlotData(slot.data("slotid"));

        if (data.id < 3) {
            window.confirm.error("You cant delete the first slot", function () {});
            return false;
        }
        createGame.removeSlotData(data.id);
        slot.remove();
    },

    setData: function setData() {
        var gamename = $("#gamename").val();
        var background = $("#mapselect").val();
        var gamespace = "-1x-1";
        var flight = "";

        if ($("#gamespacecheck:checked").val() == "on") {
            gamespace = "" + createGame.gamespace_data.width + "x" + createGame.gamespace_data.height;
        }

        if ($("#flightSizeCheck:checked").val() == "on") {
            flight = 1;
        }

        var data = { gamename: gamename, background: background, slots: createGame.slots, gamespace: gamespace, flight: flight };
        data = JSON.stringify(data);
        $("#createGameData").val(data);
    }
};