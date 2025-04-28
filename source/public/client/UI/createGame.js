"use strict";

jQuery(function ($) {
    $("#mapselect").on("change", createGame.mapSelect);
    createGame.mapSelect();

    $("input").on("change", createGame.inputChange);
    $("select").on("change", createGame.inputChange);
    $("input").on("focus", createGame.inputFocus);
    $(".addslotbutton").on("click", createGame.createNewSlot);
    $(".close").on("click", createGame.removeSlot);

    let allowSubmit = false;

    // Only set allowSubmit on real mouse or touch interaction
    $("#createGameForm input[type='submit']").on("mousedown touchstart", function () {
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
    $("#gamespacecheck").on("click", createGame.doGameSpaceCheck);
    $("#gamespacecheck").on("click", createGame.doFlightCheck);
    $("#movementcheck").on("click", createGame.doMovementCheck);
    $("#desperatecheck").on("click", createGame.doDesperateCheck);
    $("#asteroidscheck").on("click", createGame.doAsteroidsCheck);
    $("#moonscheck").on("click", createGame.doMoonsCheck);              
    
    $(".setsizeknifefight").on("click", createGame.doSwitchSizeKnifeFight);
    $(".setswitchsizebaseassault").on("click", createGame.doSwitchSizeBaseAssault);    
    $(".setsizestandard").on("click", createGame.doSwitchSizeStandard);

    createGame.createSlotsFromArray();
	createGame.doGameSpaceCheck(); //let's run proper map size setting right at the start - to match marking fixed map as defailt
    createGame.drawMapPreview();
});

window.createGame = {
    gamespace_data: { width: 42, height: 30 },
    rules: {},
    slots: Array({ id: 1, team: 1, name: "TEAM 1", points: 3500, depx: -21, depy: 0, deptype: "box", depwidth: 10, depheight: 30, depavailable: 0 }, { id: 2, team: 2, name: "TEAM 2", points: 3500, depx: 21, depy: 0, deptype: "box", depwidth: 10, depheight: 30, depavailable: 0 }),
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
            createGame.drawMapPreview();
            return;
        }

        if (inputname == "spacey") {
            createGame.gamespace_data.height = parseInt(value);
            createGame.drawMapPreview();
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

        createGame.drawMapPreview();
    },

    drawMapPreview: function drawMapPreview () {
        const canvas = document.getElementById("mapPreview");
        const ctx = canvas.getContext("2d");
        if (!ctx) return;
    
        const isLimited = $("#gamespacecheck").is(":checked");
    
        // Use fixed width/height if unlimited is selected
        const mapWidth = isLimited ? (createGame.gamespace_data.width || 1) : 84;
        const mapHeight = isLimited ? (createGame.gamespace_data.height || 1) : 60;
    
        // Clear canvas
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    
        // Margins and scale
        const margin = 10;
        const scaleX = (canvas.width - margin * 2) / mapWidth;
        const scaleY = (canvas.height - margin * 2) / mapHeight;
        const scale = Math.min(scaleX, scaleY); // Uniform scale
    
        // Calculate offset to center the map in the canvas
        const offsetX = (canvas.width - mapWidth * scale) / 2;
        const offsetY = (canvas.height - mapHeight * scale) / 2;

        // Draw black background inside the blue outline
        ctx.fillStyle = "black";
        ctx.fillRect(offsetX, offsetY, mapWidth * scale, mapHeight * scale);        

        // Draw dotted white center lines, avoiding cross-over at center
        ctx.save();
        ctx.globalAlpha = 0.6; // Semi-transparent
        ctx.strokeStyle = "white";
        ctx.lineWidth = 1;
        ctx.setLineDash([6, 6]); // Dotted pattern: 4px line, 4px gap

        const centerX = offsetX + (mapWidth / 2) * scale;
        const centerY = offsetY + (mapHeight / 2) * scale;

        // Vertical line: from center up
        ctx.beginPath();
        ctx.moveTo(centerX, centerY);
        ctx.lineTo(centerX, offsetY);
        ctx.stroke();

        // Vertical line: from center down
        ctx.beginPath();
        ctx.moveTo(centerX, centerY);
        ctx.lineTo(centerX, offsetY + mapHeight * scale);
        ctx.stroke();

        // Horizontal line: from center left
        ctx.beginPath();
        ctx.moveTo(centerX, centerY);
        ctx.lineTo(offsetX, centerY);
        ctx.stroke();

        // Horizontal line: from center right
        ctx.beginPath();
        ctx.moveTo(centerX, centerY);
        ctx.lineTo(offsetX + mapWidth * scale, centerY);
        ctx.stroke();

        ctx.restore(); // Restore default dash       

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
    
            ctx.fillStyle = "rgba(0,255,0,0.35)";
    
            // Adjust position to treat (x, y) as center
            const drawX = offsetX + (x - w / 2 + mapWidth / 2) * scale;
            const drawY = offsetY + (mapHeight / 2 - y - h / 2) * scale;
    
     //       console.log(`SlotID: ${slotId}, x: ${x}, y: ${y}, w: ${w}, h: ${h}, scale: ${scale}`);
     //       console.log(`Drawing at: x=${drawX}, y=${drawY}`);
    
            ctx.fillRect(drawX+6, drawY, w * scale, h * scale);
            ctx.strokeStyle = "#006600";
            ctx.strokeRect(drawX+6, drawY, w * scale, h * scale);
            
            // Draw slot number in the center
            ctx.save(); // Save context state
            ctx.globalAlpha = 0.8; // Semi-transparent
            ctx.fillStyle = "white";
            ctx.font = `${Math.max(4, Math.floor(4 * scale))}px Arial`; // smaller font with a minimum size
            ctx.textAlign = "center";
            ctx.textBaseline = "middle";
            ctx.fillText(team, (drawX+6) + (w * scale) / 2, (drawY+3) + (h * scale) / 2);
            ctx.restore(); // Restore to default state
        });
    
        // Draw map border (blue rectangle)
        ctx.strokeStyle = "#ffffff";
        ctx.lineWidth = 1;
        ctx.strokeRect(offsetX, offsetY, mapWidth * scale, mapHeight * scale); // Adjusted X offset
    },

    doFlightCheck: function doFlightCheck(data) {
        var checkval = $("#flightSizeCheck:checked").val();
        if (checkval == "on") {
            createGame.variableFlights = 1;
        } else createGame.variableFlights = 0;
    },
/*
    doMovementCheck: function doMovementCheck(data) {
        var checkval = $("#movementcheck:checked").val();

        if (checkval == "on") {
            createGame.rules.initiativeCategories = 6;
        } else {
            delete createGame.rules.initiativeCategories;
        }
    },
*/

doDesperateCheck: function doDesperateCheck(data) {
    var checkval = $("#desperatecheck:checked").val();

    if (checkval == "on") {
        // Show the dropdown for selecting initiative categories
        $("#desperateDropdown").show();
        // Set the selected value as initiativeCategories when checkbox is checked
        var selectedValue = $("#desperateSelect").val();
        createGame.rules.desperate = parseInt(selectedValue, 10);
        
        // Add an event listener to update the value if the user changes the dropdown
        $("#desperateSelect").on('change', function() {
            createGame.rules.desperate = parseInt($(this).val(), 10);
        });
    } else {
        // Hide the dropdown when the checkbox is unchecked
        $("#desperateDropdown").hide();
        // Remove Desperate rule
        delete createGame.rules.desperate;
    }
},

doAsteroidsCheck: function doAsteroidsCheck(data) {
    var checkval = $("#asteroidscheck:checked").val();

    if (checkval == "on") {
        // Show the dropdown for selecting initiative categories
        $("#asteroidsDropdown").show();
        // Set the selected value as initiativeCategories when checkbox is checked
        var selectedValue = $("#asteroidsSelect").val();
        createGame.rules.asteroids = parseInt(selectedValue, 10);
        
        // Add an event listener to update the value if the user changes the dropdown
        $("#asteroidsSelect").on('change', function() {
            createGame.rules.asteroids = parseInt($(this).val(), 10);
        });
    } else {
        // Hide the dropdown when the checkbox is unchecked
        $("#asteroidsDropdown").hide();
        // Remove asteroids rule
        delete createGame.rules.asteroids;
    }
},

doMoonsCheck: function doMoonsCheck(data) {
    var checkval = $("#moonscheck:checked").val(); // FIXED ID

    if (checkval == "on") {
        // Show the dropdown for selecting moons
        $("#moonsDropdown").show();
        // Set the selected value when checkbox is checked
        var selectedValue = $("#moonsSelect").val();
        createGame.rules.moons = parseInt(selectedValue, 10);
        
        // Add an event listener to update the value if the user changes the dropdown
        $("#moonsSelect").on('change', function() {
            createGame.rules.moons = parseInt($(this).val(), 10);
        });
    } else {
        // Hide the dropdown when the checkbox is unchecked
        $("#moonsDropdown").hide();
        // Remove moons rule
        delete createGame.rules.moons;
    }
},

doMovementCheck: function doMovementCheck(data) {
    var checkval = $("#movementcheck:checked").val();

    if (checkval == "on") {
        // Show the dropdown for selecting initiative categories
        $("#movementDropdown").show();
        // Set the selected value as initiativeCategories when checkbox is checked
        var selectedValue = $("#initiativeSelect").val();
        createGame.rules.initiativeCategories = parseInt(selectedValue, 10);
        
        // Add an event listener to update the value if the user changes the dropdown
        $("#initiativeSelect").on('change', function() {
            createGame.rules.initiativeCategories = parseInt($(this).val(), 10);
        });
    } else {
        // Hide the dropdown when the checkbox is unchecked
        $("#movementDropdown").hide();
        // Remove initiativeCategories rule
        delete createGame.rules.initiativeCategories;
    }
},


    doGameSpaceCheck: function doGameSpaceCheck(data) {
        var checkval = $("#gamespacecheck:checked").val();

        if (checkval == "on") {
            $(".gamespacedefinition .unlimitedspace").addClass("invisible");
            $(".gamespacedefinition .limitedspace").removeClass("invisible");
            createGame.gamespace_data.width = 42; //Reset this
            createGame.gamespace_data.height = 30;	//reset this
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
			//modify ALL SLOTS, rather than flat slots 0 and 1!
			for (var slotID in createGame.slots) {
				var slotData = createGame.slots[slotID];
				if (slotData.team == 1){ //data for team 1
					slotData.depx = $("#team1 .depx").val();
					slotData.depy = $("#team1 .depy").val();
					slotData.depwidth = $("#team1 .depwidth").val();
					slotData.depheight = $("#team1 .depheight").val();
					slotData.deptype = $(".deptype").val();
					slotData.depavailable = 0;
				} else { //data for team 2
					slotData.depx = $("#team2 .depx").val();
					slotData.depy = $("#team2 .depy").val();
					slotData.depwidth = $("#team2 .depwidth").val();
					slotData.depheight = $("#team2 .depheight").val();
					slotData.deptype = $(".deptype").val();
					slotData.depavailable = 0;
				}
			}
			/*
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
			*/
        } else {
            $(".gamespacedefinition .unlimitedspace").removeClass("invisible");
            $(".gamespacedefinition .limitedspace").addClass("invisible");
        }

        createGame.drawMapPreview();
    },


    doSwitchSizeKnifeFight: function doSwitchSizeKnifeFight(data) {
		createGame.gamespace_data.width = 30;
		createGame.gamespace_data.height = 24;		
        $(".spacex").val(30);
        $(".spacey").val(24);
        $(".deptype").val("box");
        $("#team1 .depx").val(-12);
        $("#team2 .depx").val(11);
        $("#team1 .depy").val(0);
        $("#team2 .depy").val(0);
        $("#team1 .depwidth").val(7);
        $("#team2 .depwidth").val(7);
        $("#team1 .depheight").val(24);
        $("#team2 .depheight").val(24);
		//modify ALL SLOTS, rather than flat slots 0 and 1!
		for (var slotID in createGame.slots) {
			var slotData = createGame.slots[slotID];
			if (slotData.team == 1){ //data for team 1
				slotData.depx = $("#team1 .depx").val();
				slotData.depy = $("#team1 .depy").val();
				slotData.depwidth = $("#team1 .depwidth").val();
				slotData.depheight = $("#team1 .depheight").val();
				slotData.deptype = $(".deptype").val();
				slotData.depavailable = 0;
			} else { //data for team 2
				slotData.depx = $("#team2 .depx").val();
				slotData.depy = $("#team2 .depy").val();
				slotData.depwidth = $("#team2 .depwidth").val();
				slotData.depheight = $("#team2 .depheight").val();
				slotData.deptype = $(".deptype").val();
				slotData.depavailable = 0;
			}
        }

        createGame.drawMapPreview();
		/*
        createGame.slots[0].depx = -12;
        createGame.slots[1].depx = 11;
        createGame.slots[0].depy = 0;
        createGame.slots[1].depy = 0;
        createGame.slots[0].depwidth = 7;
        createGame.slots[1].depwidth = 7;
        createGame.slots[0].depheight = 30;
        createGame.slots[1].depheight = 30;
        createGame.slots[0].deptype = "box";
        createGame.slots[1].deptype = "box";
        createGame.slots[0].depavailable = 0;
        createGame.slots[1].depavailable = 0;
		*/
    },


    doSwitchSizeBaseAssault: function doSwitchSizeBaseAssault(data) {
		createGame.gamespace_data.width = 60;
		createGame.gamespace_data.height = 40;		
        $(".spacex").val(60);
        $(".spacey").val(40);
        $(".deptype").val("box");
        $("#team1 .depx").val(-28);
        $("#team2 .depx").val(27);
        $("#team1 .depy").val(0);
        $("#team2 .depy").val(0);
        $("#team1 .depwidth").val(5);
        $("#team2 .depwidth").val(5);
        $("#team1 .depheight").val(40);
        $("#team2 .depheight").val(40);
		//modify ALL SLOTS, rather than flat slots 0 and 1!
		for (var slotID in createGame.slots) {
			var slotData = createGame.slots[slotID];
			if (slotData.team == 1){ //data for team 1
				slotData.depx = $("#team1 .depx").val();
				slotData.depy = $("#team1 .depy").val();
				slotData.depwidth = $("#team1 .depwidth").val();
				slotData.depheight = $("#team1 .depheight").val();
				slotData.deptype = $(".deptype").val();
				slotData.depavailable = 0;
			} else { //data for team 2
				slotData.depx = $("#team2 .depx").val();
				slotData.depy = $("#team2 .depy").val();
				slotData.depwidth = $("#team2 .depwidth").val();
				slotData.depheight = $("#team2 .depheight").val();
				slotData.deptype = $(".deptype").val();
				slotData.depavailable = 0;
			}
        }

        createGame.drawMapPreview();
    },
	
	
    doSwitchSizeStandard: function doSwitchSizeStandard(data) {
		createGame.gamespace_data.width = 42;
		createGame.gamespace_data.height = 30;		
        $(".spacex").val(42);
        $(".spacey").val(30);
        $(".deptype").val("box");
        $("#team1 .depx").val(-19);
        $("#team2 .depx").val(18);
        $("#team1 .depy").val(0);
        $("#team2 .depy").val(0);
        $("#team1 .depwidth").val(5);
        $("#team2 .depwidth").val(5);
        $("#team1 .depheight").val(30);
        $("#team2 .depheight").val(30);
		//modify ALL SLOTS, rather than flat slots 0 and 1!
		for (var slotID in createGame.slots) {
			var slotData = createGame.slots[slotID];
			if (slotData.team == 1){ //data for team 1
				slotData.depx = $("#team1 .depx").val();
				slotData.depy = $("#team1 .depy").val();
				slotData.depwidth = $("#team1 .depwidth").val();
				slotData.depheight = $("#team1 .depheight").val();
				slotData.deptype = $(".deptype").val();
				slotData.depavailable = 0;
			} else { //data for team 2
				slotData.depx = $("#team2 .depx").val();
				slotData.depy = $("#team2 .depy").val();
				slotData.depwidth = $("#team2 .depwidth").val();
				slotData.depheight = $("#team2 .depheight").val();
				slotData.deptype = $(".deptype").val();
				slotData.depavailable = 0;
			}
        }

        createGame.drawMapPreview();
		/*
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
		*/
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
        var data = { id: createGame.slotid, team: team, name: "TEAM " + team, points: 3500, depx: 0, depy: 0, deptype: "box", depwidth: 0, depheight: 0, depavailable: 0 };
			//copy data from team data!
			if (data.team == 1){ //data for team 1
				data.depx = $("#team1 .depx").val();
				data.depy = $("#team1 .depy").val();
				data.depwidth = $("#team1 .depwidth").val();
				data.depheight = $("#team1 .depheight").val();
				data.deptype = $(".deptype").val();
				data.depavailable = 0;
			} else { //data for team 2
				data.depx = $("#team2 .depx").val();
				data.depy = $("#team2 .depy").val();
				data.depwidth = $("#team2 .depwidth").val();
				data.depheight = $("#team2 .depheight").val();
				data.deptype = $(".deptype").val();
				data.depavailable = 0;
			}
		
        createGame.slots.push(data);
        createGame.createSlot(data);
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
        var slot = $(".slot").has($(this));
        var data = createGame.getSlotData(slot.data("slotid"));

        if (data.id < 3) {
            window.confirm.error("You cant delete the first slot", function () {});
            return false;
        }
        createGame.removeSlotData(data.id);
        createGame.drawMapPreview();
        slot.remove();
    },

    setData: function setData() {
        var gamename = $("#gamename").val();
        var background = $("#mapselect").val();
        var description = $("#description").val();
        var gamespace = "-1x-1";
        var flight = "";

        if ($("#gamespacecheck:checked").val() == "on") {
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
