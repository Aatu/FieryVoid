'use strict';

window.confirm = {

    whtml: '<div class="confirm"><div class="ui"><div class="confirmok"></div><div class="confirmcancel"></div></div></div>',

    showConfirmOkCancel: function showConfirmOkCancel(message, okcb, cancelcb) {},

    getMissileOptions: function getMissileOptions(ship) {
        var returnArray = new Array();
        var numberOfLaunchers = 1;

        if (!ship.flight) {
            // it's a normal ship
            // Yeah, we have a bombrack with missiles, but that comes fully
            // stocked. So don't pay attention to it atm.
        } else {
            // it's a fighter flight
            // all the systems are fighters of the same type.
            // also, if you buy missiles for one of them,
            // you buy the same amount for all of them.

            for (var i in ship.systems) {
                var fighter = ship.systems[i];

                for (var j in fighter.systems) {
                    var weapon = fighter.systems[j];

                    if (weapon.missileArray != null) {
                        for (var k in weapon.firingModes) {
                            for (var l in weapon.missileArray) {
                                var missile = weapon.missileArray[l];

                                if (returnArray[weapon.firingModes[k]]) {
                                    var maxAmount = returnArray[weapon.firingModes[k]][1];
                                    returnArray[weapon.firingModes[k]] = ['Type ' + missile.missileClass + ' - ' + missile.displayName, maxAmount + weapon.maxAmount, missile.cost, numberOfLaunchers];
                                    numberOfLaunchers++;
                                } else {
                                    returnArray[weapon.firingModes[k]] = ['Type ' + missile.missileClass + ' - ' + missile.displayName, weapon.maxAmount, missile.cost, numberOfLaunchers];
                                    numberOfLaunchers++;
                                }
                            }
                        }
                    }
                }
            }
        }
        return returnArray;
    },

    getLaunchersPerFighter: function getLaunchersPerFighter(ship) {
        var launchers = 0;
        for (var i in ship.systems) {
            var fighter = ship.systems[i];

            for (var j in fighter.systems) {
                var weapon = fighter.systems[j];
                if (weapon.missileArray != null) {
                    launchers++;
                }
            }

            return launchers;
        }
    },

    //    arrayIsEmpty: function(array){
    //        for(var i in array){
    //            return false;
    //        }
    //        
    //        return true;
    //    },

    doOnPlusMissile: function doOnPlusMissile(e) {
        e.stopPropagation();

        var button = $(this);
        var missileType = button.data("firingMode");
        var target = $(".selectAmount." + missileType);

        var value = target.data("value");
        var maxVal = target.data("max");
        var inc = 1;

        if (value + inc <= maxVal) {
            var newValue = value + inc;

            target.data("value", newValue);
            target.html(newValue);
            confirm.getTotalCost();
        }
    },

    doOnMinusMissile: function doOnMinusMissile(e) {
        e.stopPropagation();

        var button = $(this);
        var missileType = button.data("firingMode");
        var target = $(".selectAmount." + missileType);

        var value = target.data("value");
        var minVal = target.data("min");
        var inc = 1;

        if (value - inc >= minVal) {
            var newValue = value - inc;

            target.data("value", newValue);
            target.html(newValue);
            confirm.getTotalCost();
        }
    },

    getTotalCost: function getTotalCost() {

        var flightSize = $(".fighterAmount").html();
        if (!flightSize) {
            flightSize = 1;
        }

        var fighterCost = $(".fighterAmount").data("pV");
        if (!fighterCost) {
            var span = $(".totalUnitCostAmount").data("value");
            fighterCost = span;
        }

        var missileType = $(".selectText").data("firingMode");
        var missileAmount = $(".selectAmount." + missileType).data("value");
        var launchers = $(".selectAmount." + missileType).data("launchers");
        var missileCost = $(".selectAmount." + missileType).data("cost");

        if (!missileAmount) {
            missileAmount = 0;
        }
        if (!missileCost) {
            missileCost = 0;
        }
        if (!launchers) {
            launchers = 0;
        }

        //	console.log(flightSize, fighterCost, missileAmount, launchers, missileCost);

        var totalCost = flightSize * (fighterCost + launchers * missileAmount * missileCost);

        //add enhancement cost	   
        var enhCost = 0;
        var enhNo = 0;
        var target = $(".selectAmount.shpenh" + enhNo);
        while(typeof target.data("enhPrice") != 'undefined'){ //as long as there are enhancements defined...
            enhCost += target.data("enhCost");
            //go to next enhancement
            enhNo++;
            target = $(".selectAmount.shpenh" + enhNo);
        }
        totalCost += flightSize * enhCost;

        var totalCostSpan = $(".confirm .totalUnitCostAmount");
        totalCostSpan.data("value", totalCost);

        totalCostSpan.html(totalCost);
    },

    increaseFlightSize: function increaseFlightSize(e) {
        e.stopPropagation();

        var flightSize = $(".fighterAmount");
        var current = flightSize.html();
        var max = $(".totalUnitCostAmount").data("maxSize");

        if (current < max) {
		if (current < 6){ //allow 1-6 in flight, then by 3s!				
			flightSize.html(Math.floor(current) + 1);
		}else{
			flightSize.html(Math.floor(current) + 3);
		}
        } else return;

        confirm.getTotalCost();
    },

    decreaseFlightSize: function decreaseFlightSize(e) {
        e.stopPropagation();

        var flightSize = $(".fighterAmount");
        var current = flightSize.html();
        var max = $(".totalUnitCostAmount").data("maxSize");
		
        if (current > 6) { //allow 1-6 in flight, then by 3s!	
		flightSize.html(Math.floor(current) - 3);
        } else if (current > 1){ 
		flightSize.html(Math.floor(current) - 1);
	} else return;

        confirm.getTotalCost();
    },

    

    doOnPlusEnhancement: function(e){
	e.stopPropagation();  

	var button = $(this);
	var enhNo = button.data("enhNo");
	var target = $(".selectAmount.shpenh" + enhNo);
	    
	var noTaken = target.data("count");
	var enhLimit = target.data("max");	
	var enhPrice = target.data("enhPrice");	
	var enhPriceStep = target.data("enhPriceStep");	

	if(noTaken < enhLimit){ //increase possible
		var newCount = noTaken+1;
		target.data("count", newCount);
		target.html(newCount);
		var cost = enhPrice + (noTaken*enhPriceStep); //base value, plus additional price charged for further levels
		var newCost = target.data("enhCost")+cost;	
		target.data("enhCost", newCost);
		
		//options cost - remembering they're included in enhancements cost as well!
		if (target.data("enhIsOption")){
			cost = enhPrice + (noTaken*enhPriceStep); //base value, plus additional price charged for further levels
			newCost = target.data("enhOptionCost")+cost;		
			target.data("enhOptionCost", newCost);
		}
		
		confirm.getTotalCost();
	}
    },

    doOnMinusEnhancement: function(e){
	e.stopPropagation();  

	var button = $(this);
	var enhNo = button.data("enhNo");
	var target = $(".selectAmount.shpenh" + enhNo);
	    
	var noTaken = target.data("count");
	var enhLimit = target.data("max");	
	var enhPrice = target.data("enhPrice");	
	var enhPriceStep = target.data("enhPriceStep");	

	if(noTaken > 0){ //decrease possible
		var newCount = noTaken-1;
		target.data("count", newCount);
		target.html(newCount);
		var cost = enhPrice + (newCount*enhPriceStep); //base value, plus additional price charged for further levels
		var newCost = target.data("enhCost")-cost;		
		target.data("enhCost", newCost);
		
		//options cost - remembering they're included in enhancements cost as well!
		if (target.data("enhIsOption")){
			cost = enhPrice + (newCount*enhPriceStep); //base value, plus additional price charged for further levels
			newCost = target.data("enhOptionCost")-cost;		
			target.data("enhOptionCost", newCost);
		}
		
		confirm.getTotalCost();
	}
    },
	

    // Helper function to select all text on focus
    selectAllTextOnFocus: function() {
        var range = document.createRange();
        range.selectNodeContents(this);
        var sel = window.getSelection();
        sel.removeAllRanges();
        sel.addRange(range);
    },


    // Helper function to handle input changes
    handleInputChange: function handleInputChange(e) {
        var currentText = $(this).text();
        var value = parseInt(currentText) || 0;

        // Get the min and max limits
        var min = $(this).data('min');
        var max = $(this).data('max');

        // Enforce min/max
        if (value < min) value = min;
        if (value > max) value = max;

        // Update the displayed and stored value
        $(this).text(value);
        $(this).data('value', value);
        $(this).data('count', value);

        // Move the cursor to the end
        var range = document.createRange();
        var sel = window.getSelection();
        range.selectNodeContents(this);
        range.collapse(false); // Move to end
        sel.removeAllRanges();
        sel.addRange(range);

        // Calculate the enhancement cost
        var enhPrice = $(this).data('enhPrice');
        var enhPriceStep = $(this).data('enhPriceStep');
        var enhCost = (value > 0) ? (value * enhPrice) + ((value - 1) * enhPriceStep) : 0;
        $(this).data('enhCost', enhCost);

        // Trigger any necessary cost update function
        confirm.getTotalCost();
    },


    // Helper function to prevent non-numeric input
    preventNonNumericInput: function preventNonNumericInput(e) {
        // Allow only numbers, backspace, delete, arrows, and enter
        if (
            (e.key >= "0" && e.key <= "9") || 
            ["Backspace", "Delete", "ArrowLeft", "ArrowRight", "Enter"].includes(e.key)
        ) {
            return;
        }
        e.preventDefault();
    },   
    
    // Helper function to handle mouse wheel changes
    handleMouseWheel: function handleMouseWheel(e) {
        e.preventDefault();
        var increment = (e.originalEvent.deltaY < 0) ? 1 : -1;
        var value = parseInt($(this).text()) || 0;

        // Get the min and max limits
        var min = $(this).data('min');
        var max = $(this).data('max');

        // Adjust value based on scroll direction
        value += increment;

        // Enforce min/max
        if (value < min) value = min;
        if (value > max) value = max;

        // Update the value and trigger the input change handler
        $(this).text(value);
        $(this).data('value', value);
        $(this).trigger('input'); // Simulate an input change
    },    

handleMouseWheelFighter: function handleMouseWheelFighter(e) {
    e.preventDefault();
    e.stopPropagation();

    var $elem = $(this);
    var current = parseInt($elem.text()) || 0;
    var max = $(".totalUnitCostAmount").data("maxSize");
    var direction = (e.originalEvent.deltaY < 0) ? 'up' : 'down';

    if (direction === 'up') {
        if (current < max) {
            if (current < 6) {
                current += 1;
            } else {
                current += 3;
            }
            if (current > max) current = max;
        }
    } else {
        if (current > 6) {
            current -= 3;
        } else if (current > 1) {
            current -= 1;
        }
        if (current < 1) current = 1;
    }

    $elem.text(current);
    confirm.getTotalCost();
},    
        
    showShipBuy: function showShipBuy(ship, callback) {
        var e = $(this.whtml);

        //variable flightsize
        var variableSize = confirm.getVariableSize(ship);
        var missileOptions = confirm.getMissileOptions(ship);

        //if (variableSize || missileOptions.length > 0 || ship.superheavy) {
            var totalTemplate = $(".totalUnitCost");
            var totalItem = totalTemplate.clone(true).prependTo(e);

	    //allow maximum flight size pre-set in design...
	    if (ship.maxFlightSize!=0){
		    $(".totalUnitCostAmount").data("maxSize", ship.maxFlightSize);
	    }else{
		    if (ship.jinkinglimit > 9) {
			$(".totalUnitCostAmount").data("maxSize", 12);
		    } else $(".totalUnitCostAmount").data("maxSize", 9);
	    }
	    
		var pointCost = ship.pointCost;
		if (ship.maxFlightSize==3){ //for single-unit flight cost is for a fighter; for usual 6+ flight, for 6 craft (and 6 craft will be set)
			//but for 3-strong flight cost is still set for 6-strong flight...
			pointCost = pointCost/2;
		}
			
		//ship.pointCost
            $(".totalUnitCostText", totalItem).html("Total unit cost");
            $(".totalUnitCostAmount", totalItem).html(pointCost);
            $(".totalUnitCostAmount", totalItem).data("value", pointCost);

            $(totalItem).show();
		
            $(".totalUnitCostAmount").data("value", pointCost);	    
        //}
	    
        
    //ship enhancements
        for(var i in ship.enhancementOptions){
            //enhancementOption: ID,readableName,numberTaken,limit,price,priceStep	
            var enhancement = ship.enhancementOptions[i];
            var enhID = enhancement[0];
            var enhName = enhancement[1];
            var enhLimit = enhancement[3];		
            var enhPrice = enhancement[4];
            var enhPriceStep = enhancement[5];
		    var enhIsOption = enhancement[6];

            var template = $(".missileSelectItem");
            var item = template.clone(true).prependTo(e);

            var selectAmountItem = $(".selectAmount", item);

                selectAmountItem.html("0");
                selectAmountItem.attr("contenteditable", "true"); // Make it editable - DK 12.5.25
                selectAmountItem.addClass("shpenh"+i);
                selectAmountItem.data('enhID', enhID);
                selectAmountItem.data('count', 0);
                selectAmountItem.data('enhCost', 0);
                selectAmountItem.data('min', 0);
                selectAmountItem.data('max', enhLimit);
                selectAmountItem.data('enhPrice', enhPrice);
                selectAmountItem.data('enhPriceStep', enhPriceStep);
                //selectAmountItem.data('launchers', confirm.getLaunchersPerFighter(ship));
                //selectAmountItem.data("firingMode", i);

            // New fucntions to allow player to free type/mousewheel in number fields for enhancements/ammo - DK 12.5.25
            selectAmountItem.on("focus", confirm.selectAllTextOnFocus);

            selectAmountItem.on("input", confirm.handleInputChange);

            selectAmountItem.on("keydown", confirm.preventNonNumericInput);
            
            selectAmountItem.on("wheel", confirm.handleMouseWheel);

        var slotid = gamedata.selectedSlot;
        var slot = playerManager.getSlotById(slotid);
        var deployTurn = Math.max(1, slot.depavailable); 

		if(enhIsOption && enhID != 'DEPLOY') enhName = " <span style='color:rgb(224, 185, 57) ;'>(OPTION)</span> " + enhName; //add (OPTION) at the beginning of name of options (to differentiate them from enhancements)
        if(enhIsOption && enhID == 'DEPLOY'){
            selectAmountItem.html(deployTurn);
            selectAmountItem.data('min', deployTurn);
            enhName = " <span style='color:rgb(95, 206, 95);' >(DEPLOYMENT)</span> " + enhName;
        } 
        if (enhName.includes('(AMMO)')) {
            // Remove '(AMMO)' from enhName to prevent duplication
            enhName = enhName.replace('(AMMO)', '').trim();
            enhName = " <span style='color:rgb(106, 195, 255)  ;'>(AMMO)</span> " + enhName;
        }        
                
        var nameExpanded = enhName;
        if(enhID != 'DEPLOY'){
            nameExpanded = nameExpanded + ' {';
			if(enhLimit>1) nameExpanded += 'up to ' + enhLimit + ' levels, ';
			nameExpanded += enhPrice + 'PV ';
			//+ ' (up to ' + enhLimit + ' levels, ' + enhPrice + 'PV ';
            if((enhPriceStep!=0) && (enhLimit>1)){
                nameExpanded = nameExpanded + ' plus ' + enhPriceStep + 'PV per level';		
            }
            nameExpanded = nameExpanded + ')';
        }    
            $(".selectText", item).html(nameExpanded);
            $(item).show();

                    var plusButton = $(".plusButton", item);
                    plusButton.data("enhNo", i); 
            var minusButton = $(".minusButton", item);
                    minusButton.data("enhNo", i);


            $(".plusButton", item).on("click",confirm.doOnPlusEnhancement);
            $(".minusButton", item).on("click",confirm.doOnMinusEnhancement);
        }
        $('<div class="missileselect"><label>Here you may select any available Ammo, Options, and Enhancements.<br><span>(NOTE - For fighter flights, all fighters in flight will be similarly outfitted)</span></label></div>').prependTo(e);

        // Do lots of stuff to account for possible buying of missiles.
        var missileOptions = confirm.getMissileOptions(ship);

        // If it is a fighter, put the option in this pane.
        // A ship will need some more tricks.
        if (!mathlib.arrayIsEmpty(missileOptions)) {

            for (var i in missileOptions) {
                var missileOption = missileOptions[i];
                var template = $(".missileSelectItem");
                var item = template.clone(true).prependTo(e);

                var selectAmountItem = $(".selectAmount", item);

                selectAmountItem.html("0");
                selectAmountItem.addClass(i);
                selectAmountItem.data('value', 0);
                selectAmountItem.data('min', 0);

				
                //if (ship.superheavy) {
				if (ship.maxFlightSize<3) { //here it's question of single vs multiple craft per flight, not of being superheavy
                    $(".selectText", item).html(missileOption[0] + ' (maximum amount: ' + missileOption[1] / 6 / (missileOption[3] / 6) + ', cost: ' + missileOption[2] + ')');
                    $(item).show();

                    selectAmountItem.data('max', Math.round( missileOption[1] / 6 / (missileOption[3] / 6) ));
                    selectAmountItem.data('cost', missileOption[2]);
                    selectAmountItem.data('launchers', confirm.getLaunchersPerFighter(ship));
                    selectAmountItem.data("firingMode", i);
                } else {
                    $(".selectText", item).html(missileOption[0] + ' (maximum amount: ' + missileOption[1] / 6 / (missileOption[3] / 6) + ', cost: ' + missileOption[2] + ')');
                    $(item).show();

                    selectAmountItem.data('max', Math.round(missileOption[1] / 6 / (missileOption[3] / 6)));
                    selectAmountItem.data('cost', missileOption[2]);
                    selectAmountItem.data('launchers', confirm.getLaunchersPerFighter(ship));
                    selectAmountItem.data("firingMode", i);
                }

                $(".selectText").data("firingMode", i);

                var plusButton = $(".plusButton", item);
                plusButton.data("firingMode", i);
                $(".minusButton", item).data("firingMode", i);
            }

            $('<div class="missileselect"><label>This fighter type can carry fighter missiles.<br>\
                    Please select the amount you wish to purchase PER MISSILE LAUNCHER.<br></label>').prependTo(e);

            //$(".missileSelectItem .selectButtons .plusButton", e).on("click", confirm.doOnPlusMissile);
            //$(".missileSelectItem .selectButtons .minusButton", e).on("click", confirm.doOnMinusMissile);
            //change for enhancements:
            $(".plusButton", item).on("click",confirm.doOnPlusMissile);
            $(".minusButton", item).on("click",confirm.doOnMinusMissile);
        }

        if (variableSize) {
            var template = $(".missileSelectItem");
            var item = template.clone(true).prependTo(e);
            item.addClass("fighterSelectItem");

            $(".selectText", item).html("Number of fighters in this flight:");
            $(item).show();

            var selectAmountItem = $(".selectAmount", item);
            selectAmountItem.removeClass("selectAmount").addClass("fighterAmount");

	    //special treatment for flight size 3 - as it's less than default 6...
	    if (ship.maxFlightSize==3){
            	selectAmountItem.html("3");
	    }else{//default 
            	selectAmountItem.html("6");
	    }
            selectAmountItem.data('pV', Math.floor(ship.pointCost / 6));

            selectAmountItem.on("wheel", confirm.handleMouseWheelFighter);
            $(".fighterSelectItem .selectButtons .plusButton", e).on("click", confirm.increaseFlightSize);
            $(".fighterSelectItem .selectButtons .minusButton", e).on("click", confirm.decreaseFlightSize);
        }
	    
	/* try to make default unit name other than nameless */
	var nameCore = ship.shipClass;
	var nameNumber = gamedata.lastShipNumber + 1;
	var fullName = '';//by default: nameCore + ' #' + number ; name cannot be repeated!
	var accepted = false;
	var exists = false;
	while (accepted != true){
		fullName = nameCore + ' #' +nameNumber;
		//check whether such a name doesn't yet exist...
		exists = false;
		for (var i in gamedata.ships) {
            		var currShip = gamedata.ships[i];
            		if (currShip.name == fullName) exists = true;
        	}
		if (exists == true){
			nameNumber++;
		}else{
			accepted=true;
		}
	}	    
	gamedata.lastShipNumber = nameNumber;
	/*end of preparing default name*/
	    $('<label>Name your new ' + ship.shipClass + ':</label><input type="text" style="text-align:center" name="shipname" value="' + fullName + '"></input><br>').prependTo(e);
		
	    /* old, with Nameless default
        $('<label>Name your new ' + ship.shipClass + ':</label><input type="text" style="text-align:center" name="shipname" value="Nameless"></input><br>').prependTo(e);
		*/
        //$('<div class="message"><span>Name your new '+ship.shipClass+'</span></div>').prependTo(e);
        $(".confirmok", e).on("click", callback);
        $(".confirmcancel", e).on("click", function () {
            console.log("remove");
            $(".confirm").remove();
        });
        $(".confirmok", e).data("shipclass", ship.phpclass);

        var a = e.appendTo("body");
        a.fadeIn(250);
    },


// Helper function to handle input changes (edit mode)
handleInputChangeEdit: function handleInputChangeEdit(e) {
    var currentText = $(this).text();
    var value = parseInt(currentText) || 0;
    var oldCount = $(this).data('count');

    // Get the min and max limits
    var min = $(this).data('min');
    var max = $(this).data('max');

    // Enforce min/max
    if (value < min) value = min;
    if (value > max) value = max;

    // Update the displayed and stored value
    $(this).text(value);
    $(this).data('value', value);
    $(this).data('count', value);

    // Move the cursor to the end
    var range = document.createRange();
    var sel = window.getSelection();
    range.selectNodeContents(this);
    range.collapse(false); // Move to end
    sel.removeAllRanges();
    sel.addRange(range);

    // Calculate the enhancement cost
    var enhPrice = $(this).data('enhPrice');
    var enhPriceStep = $(this).data('enhPriceStep');
    var countDifference = value - oldCount;

    // Update the enhancement cost incrementally
    var enhCost = $(this).data('enhCost') || 0;
    var costChange = (countDifference > 0) 
        ? (countDifference * enhPrice) + ((countDifference - 1) * enhPriceStep)
        : (countDifference * enhPrice) - ((Math.abs(countDifference) - 1) * enhPriceStep);
    enhCost += costChange;
    $(this).data('enhCost', enhCost);

    // Trigger any necessary cost update function
    confirm.getTotalCost();
},

    
    showShipEdit: function showShipEdit(ship, callback) {
        var e = $(this.whtml);

        // Store the original ship state before any edits
        let originalShipData = {
            name: ship.name,
            pointCost: ship.pointCost,
            flightSize: ship.flightSize,
            enhancementOptions: ship.enhancementOptions ? [...ship.enhancementOptions] : [],
        };

        //variable flightsize
        var variableSize = confirm.getVariableSize(ship);
        var missileOptions = confirm.getMissileOptions(ship);

        //if (variableSize || missileOptions.length > 0 || ship.superheavy) {
            var totalTemplate = $(".totalUnitCost");
            var totalItem = totalTemplate.clone(true).prependTo(e);

	    //allow maximum flight size pre-set in design...
	    if (ship.maxFlightSize!=0){
		    $(".totalUnitCostAmount").data("maxSize", ship.maxFlightSize);
	    }else{
		    if (ship.jinkinglimit > 9) {
			$(".totalUnitCostAmount").data("maxSize", 12);
		    } else $(".totalUnitCostAmount").data("maxSize", 9);
	    }
	    
		var pointCost = ship.pointCost;
		if (ship.maxFlightSize==3){ //for single-unit flight cost is for a fighter; for usual 6+ flight, for 6 craft (and 6 craft will be set)
			//but for 3-strong flight cost is still set for 6-strong flight...
			pointCost = pointCost/2;
		}     
        
		//ship.pointCost
            $(".totalUnitCostText", totalItem).html("Total unit cost");
            $(".totalUnitCostAmount", totalItem).html(pointCost);
            $(".totalUnitCostAmount", totalItem).data("value", pointCost);

            $(totalItem).show();
		
            $(".totalUnitCostAmount").data("value", pointCost);	    
        //}
	    
        
    //ship enhancements
        for(var i in ship.enhancementOptions){
            //enhancementOption: ID,readableName,numberTaken,limit,price,priceStep	
            var enhancement = ship.enhancementOptions[i];
            var enhID = enhancement[0];
            var enhCount = enhancement[2];
            var enhName = enhancement[1];
            var enhLimit = enhancement[3];		
            var enhPrice = enhancement[4];
            var enhPriceStep = enhancement[5];
		    var enhIsOption = enhancement[6];

            var template = $(".missileSelectItem");
            var item = template.clone(true).prependTo(e);

            var selectAmountItem = $(".selectAmount", item);
                    selectAmountItem.attr("contenteditable", "true"); // Make it editable - DK 12.5.25
                selectAmountItem.html(enhCount);
                selectAmountItem.addClass("shpenh"+i);
                selectAmountItem.data('enhID', enhID);
                selectAmountItem.data('count', enhCount);
                selectAmountItem.data('enhCost', 0);
                selectAmountItem.data('min', 0);
                selectAmountItem.data('max', enhLimit);
                selectAmountItem.data('enhPrice', enhPrice);
                selectAmountItem.data('enhPriceStep', enhPriceStep);
                //selectAmountItem.data('launchers', confirm.getLaunchersPerFighter(ship));
                //selectAmountItem.data("firingMode", i);

            // New fucntions to allow player to free type in number fields for enhancements/ammo - DK 12.5.25
            selectAmountItem.on("focus", confirm.selectAllTextOnFocus);

            selectAmountItem.on("input", confirm.handleInputChangeEdit);

            selectAmountItem.on("keydown", confirm.preventNonNumericInput);

            selectAmountItem.on("wheel", confirm.handleMouseWheel);

        var slotid = gamedata.selectedSlot;
        var slot = playerManager.getSlotById(slotid);
        var deployTurn = Math.max(1, slot.depavailable);          

		if(enhIsOption && enhID != 'DEPLOY') enhName = " <span style='color:rgb(224, 185, 57) ;'>(OPTION)</span> " + enhName; //add (OPTION) at the beginning of name of options (to differentiate them from enhancements)
        if(enhIsOption && enhID == 'DEPLOY'){
            if(enhCount == 0) selectAmountItem.html(deployTurn);
            selectAmountItem.data('min', deployTurn);
            enhName = " <span style='color:rgb(95, 206, 95);' >(DEPLOYMENT)</span> " + enhName;
        } 
        if (enhName.includes('(AMMO)')) {
            // Remove '(AMMO)' from enhName to prevent duplication
            enhName = enhName.replace('(AMMO)', '').trim();
            enhName = " <span style='color:rgb(106, 195, 255)  ;'>(AMMO)</span> " + enhName;
        }        
                
        var nameExpanded = enhName;
        if(enhID != 'DEPLOY'){
            nameExpanded = nameExpanded + ' {';
			if(enhLimit>1) nameExpanded += 'up to ' + enhLimit + ' levels, ';
			nameExpanded += enhPrice + 'PV ';
			//+ ' (up to ' + enhLimit + ' levels, ' + enhPrice + 'PV ';
            if((enhPriceStep!=0) && (enhLimit>1)){
                nameExpanded = nameExpanded + ' plus ' + enhPriceStep + 'PV per level';		
            }
            nameExpanded = nameExpanded + ')';
        }    

            $(".selectText", item).html(nameExpanded);
            $(item).show();

                    var plusButton = $(".plusButton", item);
                    plusButton.data("enhNo", i); 
            var minusButton = $(".minusButton", item);
                    minusButton.data("enhNo", i);


            $(".plusButton", item).on("click",confirm.doOnPlusEnhancement);
            $(".minusButton", item).on("click",confirm.doOnMinusEnhancement);
        }
        $('<div class="missileselect"><label>Here you may select any available Ammo, Options, and Enhancements.<br><span>(NOTE - For fighter flights, all fighters in flight will be similarly outfitted)</span></label></div>').prependTo(e);

        // Do lots of stuff to account for possible buying of missiles.
        var missileOptions = confirm.getMissileOptions(ship);

        // If it is a fighter, put the option in this pane.
        // A ship will need some more tricks.
        if (!mathlib.arrayIsEmpty(missileOptions)) {

            for (var i in missileOptions) {
                var missileOption = missileOptions[i];
                var template = $(".missileSelectItem");
                var item = template.clone(true).prependTo(e);

                var selectAmountItem = $(".selectAmount", item);

                selectAmountItem.html("0");
                selectAmountItem.addClass(i);
                selectAmountItem.data('value', 0);
                selectAmountItem.data('min', 0);

				
                //if (ship.superheavy) {
				if (ship.maxFlightSize<3) { //here it's question of single vs multiple craft per flight, not of being superheavy
                    $(".selectText", item).html(missileOption[0] + ' (maximum amount: ' + missileOption[1] / 6 / (missileOption[3] / 6) + ', cost: ' + missileOption[2] + ')');
                    $(item).show();

                    selectAmountItem.data('max', Math.round( missileOption[1] / 6 / (missileOption[3] / 6) ));
                    selectAmountItem.data('cost', missileOption[2]);
                    selectAmountItem.data('launchers', confirm.getLaunchersPerFighter(ship));
                    selectAmountItem.data("firingMode", i);
                } else {
                    $(".selectText", item).html(missileOption[0] + ' (maximum amount: ' + missileOption[1] / 6 / (missileOption[3] / 6) + ', cost: ' + missileOption[2] + ')');
                    $(item).show();

                    selectAmountItem.data('max', Math.round(missileOption[1] / 6 / (missileOption[3] / 6)));
                    selectAmountItem.data('cost', missileOption[2]);
                    selectAmountItem.data('launchers', confirm.getLaunchersPerFighter(ship));
                    selectAmountItem.data("firingMode", i);
                }

                $(".selectText").data("firingMode", i);

                var plusButton = $(".plusButton", item);
                plusButton.data("firingMode", i);
                $(".minusButton", item).data("firingMode", i);
            }

            $('<div class="missileselect"><label>This fighter type can carry fighter missiles.<br>\
                    Please select the amount you wish to purchase PER MISSILE LAUNCHER.<br></label>').prependTo(e);

            //$(".missileSelectItem .selectButtons .plusButton", e).on("click", confirm.doOnPlusMissile);
            //$(".missileSelectItem .selectButtons .minusButton", e).on("click", confirm.doOnMinusMissile);
            //change for enhancements:
            $(".plusButton", item).on("click",confirm.doOnPlusMissile);
            $(".minusButton", item).on("click",confirm.doOnMinusMissile);
        }

        if (variableSize) {
            var template = $(".missileSelectItem");
            var item = template.clone(true).prependTo(e);
            item.addClass("fighterSelectItem");

            $(".selectText", item).html("Number of fighters in this flight:");
            $(item).show();

            var selectAmountItem = $(".selectAmount", item);
            selectAmountItem.removeClass("selectAmount").addClass("fighterAmount");

            selectAmountItem.html(ship.flightSize);

            selectAmountItem.data('pV', Math.floor(ship.pointCost / ship.flightSize));

            selectAmountItem.on("wheel", confirm.handleMouseWheelFighter);
            $(".fighterSelectItem .selectButtons .plusButton", e).on("click", confirm.increaseFlightSize);
            $(".fighterSelectItem .selectButtons .minusButton", e).on("click", confirm.decreaseFlightSize);
        }
	    
	    $('<label>Edit your ' + ship.shipClass + ':</label><input type="text" style="text-align:center" name="shipname" value="' + ship.name + '"></input><br>').prependTo(e);

        $(".confirmok", e).on("click", callback);

        $(".confirmcancel", e).on("click", function () {
            console.log("remove");
            $(".confirm").remove();
        });

        $(".confirmok", e).data("ship", ship);
        $(".confirmok", e).data("originalShipData", originalShipData);



var a = e.appendTo("body");
a.fadeIn(250);
},


    getVariableSize: function getVariableSize(ship) {
        //if (ship.flight && !ship.superheavy) { //superheavy is no longer a good marker
	if (ship.flight && ship.maxFlightSize!=1) { //max flight size = 1 indicates single superheavy fighter
            return true;
        } else return false;
    },

    error: function error(msg, callback) {
        var e = $('<div class="confirm error"><div class="ui"><div class="confirmok" style="margin:auto;"></div></div></div>');
        $('<span>' + msg + '</span>').prependTo(e);
        //$('<span>ERROR</span></br>').prependTo(e);
        //$('<div class="message"><span>Name your new '+ship.shipClass+'</span></div>').prependTo(e);


        $(".ok", e).on("click", callback);
        $(".confirmok", e).on("click", function () {
            $(".confirm").remove();
        });
        $(".confirmcancel", e).remove();
        $(".ok", e).css("left", "45%");
        var a = e.appendTo("body");
        a.fadeIn(250);
    },

    confirm: function confirm(msg, callback) {
        var e = $('<div class="confirm error"><div class="ui"><div class="confirmok"></div><div class="confirmcancel"></div></div></div>');
        //var e = $('<div class="confirm error"><div class="ui"><div class="confirmok" style="margin:auto;"></div></div></div>');
        $('<span>' + msg + '</span>').prependTo(e);
        //$('<span>ERROR</span></br>').prependTo(e);
        //$('<div class="message"><span>Name your new '+ship.shipClass+'</span></div>').prependTo(e);


        $(".ok", e).on("click", callback);
        $(".confirmok", e).on("click", function () {
            $(".confirm").remove();
        });
        $(".confirmok", e).on("click", callback);
        $(".confirmcancel", e).on("click", function () {
            $(".confirm").remove();
        });
        $(".ok", e).css("left", "45%");
        var a = e.appendTo("body");
        a.fadeIn(250);
    },

	//New window type, simplies provide a wanring to players about a specific action they are taking, does not prevent any.
	warning: function warning(msg) {
	    var e = $('<div class="confirm error"><div class="ui"><div class="confirmok" style="margin:auto;"></div></div></div>');
	    $('<span>' + msg + '</span>').prependTo(e);

	    $(".confirmok", e).on("click", function() {
	        e.remove(); // Remove the warning dialog
	    });

	    e.appendTo("body").fadeIn(250);
	},


    confirmWithOptions: function confirmWithOptions(msg, trueOptionString, falseOptionString, callback) {
        var e = $('<div class="confirm error"><div class="ui"><div class="confirmokoption">' + trueOptionString + '</div><div class="confirmcanceloption">' + falseOptionString + '</div></div></div>');
        $('<span>' + msg + '</span>').prependTo(e);
        // As the callback needs a parameter, you can't just bind the callback.
        // The method would then be executed as you bind the result of the callback,
        // and not the callback itself. Hence the function(){callback(true);} thingy.
        $(".ok", e).on("click", function () {
            callback(true);
        });

        $(".confirmokoption", e).on("click", function () {
            $(".confirm").remove();
        });
        $(".confirmokoption", e).on("click", function () {
            callback(true);
        });
        $(".confirmcanceloption", e).on("click", function () {
            $(".confirm").remove();
        });
        $(".confirmcanceloption", e).on("click", function () {
            callback(false);
        });
        $(".ok", e).css("left", "45%");
        var a = e.appendTo("body");
        a.fadeIn(250);
    },

    confirmOrSurrender: function confirmOrSurrender(msg, callbackCommit, callbackSurrender) {
        var e = $('<div class="confirm error"><div class="ui"><div class="confirmok"></div><div class="surrender"></div><div class="confirmcancel"></div></div></div>');
        $('<span>' + msg + '</span>').prependTo(e);

        $(".ok", e).on("click", callbackCommit);
        $(".confirmok", e).on("click", function () {
            $(".confirm").remove();
        });
        $(".confirmok", e).on("click", callbackCommit);
        $(".confirmcancel", e).on("click", function () {
            $(".confirm").remove();
        });
        $(".surrender", e).on("click", function () {
            $(".confirm").remove();
        });
        $(".surrender", e).on("click", callbackSurrender);
        $(".ok", e).css("left", "45%");
        var a = e.appendTo("body");
        a.fadeIn(250);
    },

    //    askSurrender: function(msg, callbackCommit, callbackSurrender){
    //        var e = $('<div class="confirm error"><div class="ui"><div class="surrender2"></div><div class="confirmcancel"></div></div></div>'); //<div class="confirmok"></div>
    //		$('<span>'+msg+'</span>').prependTo(e);
    //		
    //		$(".ok", e).on("click", callbackCommit);
    //		$(".confirmcancel", e).on("click", function(){$(".confirm").remove();});
    //		$(".confirmcancel", e).on("click", callbackCommit);
    //        $(".surrender2",e).on("click", function(){$(".confirm").remove();});
    //		$(".surrender2", e).on("click", callbackSurrender);
    //		$(".ok",e).css("left", "45%");
    //		var a = e.appendTo("body");
    //		a.fadeIn(250);
    //	},

    exception: function exception(data) {
        var e = $('<div style="z-index:999999"class="confirm error"></div>');
        console.dir(data);
        $('<h2>SERVER ERROR</h2>').appendTo(e);
        if (data.code) $('<div><span>Error code: ' + data.code + '</span></div>').appendTo(e);

        if (data.logid) $('<div><span>Log id: ' + data.logid + '</span></div>').appendTo(e);

        $('<div style="margin-top:20px;"><span>' + data.error + '</span></div>').appendTo(e);

        //$('<span>ERROR</span></br>').prependTo(e);
        //$('<div class="message"><span>Name your new '+ship.shipClass+'</span></div>').prependTo(e);

        var w = window.innerWidth - 1;
        var h = window.innerHeight - 1;
        var backdrop = $('<div style="width:' + w + 'px;height:' + h + 'px;background-color:black;z-index:999998;position:absolute;top:0px;left:0px;opacity:0;"></div>');

        var b = backdrop.appendTo("body");
        b.fadeTo(1000, 0.5);
        var a = e.appendTo("body");
        a.fadeIn(250);
    }

};
