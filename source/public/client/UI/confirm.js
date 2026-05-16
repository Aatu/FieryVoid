'use strict';

window.confirm = {

    whtml: '<div class="confirm"><div class="ui"><div class="confirmok"></div><div class="confirmcancel"></div></div></div>',

    showConfirmOkCancel: function showConfirmOkCancel(message, okcb, cancelcb) { },

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
                                    returnArray[weapon.firingModes[k]] = ['Type ' + missile.missileClass + ' - ' + missile.displayName, maxAmount + weapon.maxAmount, missile.cost, numberOfLaunchers, missile.amount];
                                    numberOfLaunchers++;
                                } else {
                                    returnArray[weapon.firingModes[k]] = ['Type ' + missile.missileClass + ' - ' + missile.displayName, weapon.maxAmount, missile.cost, numberOfLaunchers, missile.amount];
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

    getMaxAmmoFit: function (ship, currentID) {
        var baseShip = gamedata.getShipByType($(".confirmok").data("shipclass") || ship.phpclass);
        if (!baseShip) baseShip = ship;

        var ammoMag = null;
        if (baseShip && baseShip.flight) {
            var firstFighterKey = Object.keys(baseShip.systems)[0];
            if (firstFighterKey && baseShip.systems[firstFighterKey].systems) {
                for (var i in baseShip.systems[firstFighterKey].systems) {
                    if (baseShip.systems[firstFighterKey].systems[i].name == "ammoMagazine") { ammoMag = baseShip.systems[firstFighterKey].systems[i]; break; }
                }
            }
        } else if (baseShip) {
            for (var i in baseShip.systems) {
                if (baseShip.systems[i].name == "ammoMagazine") { ammoMag = baseShip.systems[i]; break; }
            }
        }

        if (!ammoMag || typeof ammoMag.capacity === 'undefined') return 9999;

        var capacity = ammoMag.capacity;

        var totalExtraRequested = 0;

        // Sum up other enhancements
        var _enhNo = 0;
        var _target = $(".selectAmount.shpenh" + _enhNo);
        while (typeof _target.data("enhPrice") != 'undefined') {
            var _noTaken = _target.data("count");
            var _enhID = _target.data("enhID");

            if (_enhID !== currentID && _noTaken > 0 && _enhID && (_enhID.startsWith("AMMO_") || _enhID.startsWith("MINE_") || _enhID.startsWith("SHELL_"))) {
                var slots = 1;
                if (_enhID == 'AMMO_K' || _enhID == 'AMMO_M') slots = 2;
                totalExtraRequested += _noTaken * slots;
            }
            _enhNo++;
            _target = $(".selectAmount.shpenh" + _enhNo);
        }

        var addedSlots = 1;
        if (currentID == 'AMMO_K' || currentID == 'AMMO_M') addedSlots = 2;

        return Math.floor((capacity - totalExtraRequested) / addedSlots);
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
            var ship = $(".confirmok").data("ship") || $(".confirmok").data("originalShipData");
            if (!ship) ship = gamedata.getShipByType($(".confirmok").data("shipclass"));
            var maxFit = confirm.getMaxAmmoFit(ship, missileType);
            if (maxFit < inc) return;

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
        if ($(".confirm #bulkQuantity").length > 0) {
            return confirm.getTotalCostBulk();
        }

        var flightSize = parseInt($(".fighterAmount").html()) || 1;
        var fighterCost = $(".fighterAmount").data("pV");
        if (!fighterCost) {
            fighterCost = $(".totalUnitCostAmount").data("value") || 0;
        }

        var totalMissileCost = 0;
        // Sometime when editing this will already have an amount build into fighter cost.
        // But since we start calculation from pristine base cost (naked), we only add cost of ALL selected missiles.
        // If a missile is "built-in" and free, its cost is 0 anyway.
        // If it's built-in and NOT free, but included in base cost, it should have been in pristineBaseCost.

        // Iterate over all missile/ammo options
        $(".confirm .selectAmount").each(function () {
            var $amt = $(this);
            var firingMode = $amt.data("firingMode");

            // Check if it's a missile option (has firingMode and NOT an enhancement)
            if (typeof firingMode !== 'undefined' && !$amt.hasClass("shpenh0") && !/shpenh\d+/.test($amt.attr('class'))) {
                var amount = $amt.data("value") || 0;
                var cost = $amt.data("cost") || 0;
                var launchers = $amt.data("launchers") || 0;

                totalMissileCost += (amount * cost * flightSize * launchers);
            }
        });

        var totalCost = (flightSize * fighterCost) + totalMissileCost;

        // Add enhancement cost	   
        var enhCost = 0;
        var enhNo = 0;
        var target = $(".selectAmount.shpenh" + enhNo);
        while (typeof target.data("enhPrice") != 'undefined') { //as long as there are enhancements defined...
            enhCost += target.data("enhCost");
            //go to next enhancement
            enhNo++;
            target = $(".selectAmount.shpenh" + enhNo);
        }
        totalCost += flightSize * enhCost;
        totalCost = Math.ceil(totalCost);

        var totalCostSpan = $(".confirm .totalUnitCostAmount");
        totalCostSpan.data("value", totalCost);
        totalCostSpan.html(totalCost);
    },

    getTotalCostBulk: function getTotalCostBulk() {
        // Fallback to generic .totalUnitCostAmount for safety if the class isn't strictly defined
        var baseCostTarget = $(".confirm .totalBulkCostAmount").length ? $(".confirm .totalBulkCostAmount") : $(".confirm .totalUnitCostAmount");
        var baseCost = parseFloat(baseCostTarget.data("baseCost")) || parseFloat(baseCostTarget.first().data("value"));

        //add enhancement cost	   
        var enhCost = 0;
        var enhNo = 0;
        var target = $(".confirm .selectAmount.shpenh" + enhNo);
        while (typeof target.data("enhPrice") != 'undefined') { //as long as there are enhancements defined...
            enhCost += target.data("enhCost");
            //go to next enhancement
            enhNo++;
            target = $(".confirm .selectAmount.shpenh" + enhNo);
        }

        var costPerUnit = baseCost + enhCost;
        var totalCost = costPerUnit;

        // If buying units in bulk, multiply final total by designated quantity
        var bulkQuantity = parseInt($(".confirm #bulkQuantity").val());
        if (!isNaN(bulkQuantity) && bulkQuantity > 0) {
            totalCost *= bulkQuantity;
        }

        //costPerUnit = Math.ceil(costPerUnit);
        totalCost = Math.ceil(totalCost);

        // Update specifically the "Cost Per Unit" and "Total Unit Cost"
        var costPerUnitSpan = $(".confirm .costPerUnitSpan");
        if (costPerUnitSpan.length) {
            costPerUnitSpan.data("value", costPerUnit);
            costPerUnitSpan.html(costPerUnit);
        }

        var totalCostSpan = $(".confirm .totalBulkCostAmount");
        if (totalCostSpan.length) {
            totalCostSpan.data("value", totalCost); // This updates the DOM data
            totalCostSpan.html(totalCost);
        } else {
            // Fallback
            $(".confirm .totalUnitCostAmount").first().html(totalCost);
        }
    },

    increaseFlightSize: function increaseFlightSize(e) {
        e.stopPropagation();

        var flightSize = $(".fighterAmount");
        var current = flightSize.html();
        var max = $(".totalUnitCostAmount").data("maxSize");

        if (current < max) {
            if (current < 6) { //allow 1-6 in flight, then by 3s!				
                flightSize.html(Math.floor(current) + 1);
            } else {
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
        } else if (current > 1) {
            flightSize.html(Math.floor(current) - 1);
        } else return;

        confirm.getTotalCost();
    },



    doOnPlusEnhancement: function (e) {
        e.stopPropagation();

        var button = $(this);
        var enhNo = button.data("enhNo");
        var target = $(".selectAmount.shpenh" + enhNo);

        var noTaken = target.data("count");
        var enhLimit = target.data("max");
        var enhPrice = target.data("enhPrice");
        var enhPriceStep = target.data("enhPriceStep");
        var enhID = target.data("enhID");

        if (enhID && (enhID.startsWith("AMMO_") || enhID.startsWith("MINE_") || enhID.startsWith("SHELL_"))) {
            var ship = $(".confirmok").data("ship") || $(".confirmok").data("originalShipData");
            if (!ship) ship = gamedata.getShipByType($(".confirmok").data("shipclass"));
            var maxFit = confirm.getMaxAmmoFit(ship, enhID);
            if (maxFit <= noTaken) {
                return;
            }
        }

        if (noTaken < enhLimit) { //increase possible
            var newCount = noTaken + 1;
            target.data("count", newCount);
            target.html(newCount);
            var cost = enhPrice + (noTaken * enhPriceStep); //base value, plus additional price charged for further levels
            var newCost = target.data("enhCost") + cost;
            target.data("enhCost", newCost);

            //options cost - remembering they're included in enhancements cost as well!
            if (target.data("enhIsOption")) {
                cost = enhPrice + (noTaken * enhPriceStep); //base value, plus additional price charged for further levels
                newCost = target.data("enhOptionCost") + cost;
                target.data("enhOptionCost", newCost);
            }

            confirm.getTotalCost();
        }
    },

    doOnMinusEnhancement: function (e) {
        e.stopPropagation();

        var button = $(this);
        var enhNo = button.data("enhNo");
        var target = $(".selectAmount.shpenh" + enhNo);

        var noTaken = target.data("count");
        var enhLimit = target.data("max");
        var enhPrice = target.data("enhPrice");
        var enhPriceStep = target.data("enhPriceStep");

        if (noTaken > 0) { //decrease possible
            var newCount = noTaken - 1;
            target.data("count", newCount);
            target.html(newCount);
            var cost = enhPrice + (newCount * enhPriceStep); //base value, plus additional price charged for further levels
            var newCost = target.data("enhCost") - cost;
            target.data("enhCost", newCost);

            //options cost - remembering they're included in enhancements cost as well!
            if (target.data("enhIsOption")) {
                cost = enhPrice + (newCount * enhPriceStep); //base value, plus additional price charged for further levels
                newCost = target.data("enhOptionCost") - cost;
                target.data("enhOptionCost", newCost);
            }

            confirm.getTotalCost();
        }
    },


    // Helper function to select all text on focus
    selectAllTextOnFocus: function () {
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

        var enhID = $(this).data("enhID");
        if (enhID && (enhID.startsWith("AMMO_") || enhID.startsWith("MINE_") || enhID.startsWith("SHELL_"))) {
            var ship = $(".confirmok").data("ship") || $(".confirmok").data("originalShipData");
            if (!ship) ship = gamedata.getShipByType($(".confirmok").data("shipclass"));
            var maxFit = confirm.getMaxAmmoFit(ship, enhID);
            if (value > maxFit) value = Math.max(0, maxFit);
        }

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

        var enhID = $(this).data("enhID");
        if (enhID && (enhID.startsWith("AMMO_") || enhID.startsWith("MINE_") || enhID.startsWith("SHELL_"))) {
            var ship = $(".confirmok").data("ship") || $(".confirmok").data("originalShipData");
            if (!ship) ship = gamedata.getShipByType($(".confirmok").data("shipclass"));
            var maxFit = confirm.getMaxAmmoFit(ship, enhID);
            if (value > maxFit) value = Math.max(0, maxFit);
        }

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

    handleMouseWheelMissile: function (e) {
        e.preventDefault();
        e.stopPropagation();

        var $amt = $(this);
        var current = $amt.data('value');
        var min = $amt.data('min');
        var max = $amt.data('max');
        var dir = (e.originalEvent.deltaY < 0) ? +1 : -1;

        // adjust by 1 missile per wheel‑click
        current = current + dir;

        // clamp
        if (current < min) current = min;
        if (current > max) current = max;

        var missileType = $amt.data("firingMode");
        var ship = $(".confirmok").data("ship") || $(".confirmok").data("originalShipData");
        if (!ship) ship = gamedata.getShipByType($(".confirmok").data("shipclass"));
        var maxFit = confirm.getMaxAmmoFit(ship, missileType);
        if (current > maxFit) current = Math.max(0, maxFit);

        // write both text & data
        $amt
            .text(current)
            .data('value', current);

        // recalc total cost / ammo
        confirm.getTotalCost();
    },

    /*
    showShipBuy: function showShipBuy(ship, callback) {
        var e = $(this.whtml);

        //variable flightsize
        var variableSize = confirm.getVariableSize(ship);
        var missileOptions = confirm.getMissileOptions(ship);

        //if (variableSize || missileOptions.length > 0 || ship.superheavy) {
        var totalTemplate = $(".totalUnitCost");
        var totalItem = totalTemplate.clone(true).prependTo(e);

        //allow maximum flight size pre-set in design...
        if (ship.maxFlightSize != 0) {
            $(".totalUnitCostAmount").data("maxSize", ship.maxFlightSize);
        } else {
            if (ship.jinkinglimit > 9) {
                $(".totalUnitCostAmount").data("maxSize", 12);
            } else $(".totalUnitCostAmount").data("maxSize", 9);
        }

        var pointCost = ship.pointCost;
        if (ship.maxFlightSize == 3) { //for single-unit flight cost is for a fighter; for usual 6+ flight, for 6 craft (and 6 craft will be set)
            //but for 3-strong flight cost is still set for 6-strong flight...
            pointCost = pointCost / 2;
        }

        //ship.pointCost
        $(".totalUnitCostText", totalItem).html("Total cost");
        $(".totalUnitCostAmount", totalItem).html(pointCost);
        $(".totalUnitCostAmount", totalItem).data("value", pointCost);

        $(totalItem).show();

        $(".totalUnitCostAmount").data("value", pointCost);
        //}


        //ship enhancements
        for (var i in ship.enhancementOptions) {
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
            selectAmountItem.addClass("shpenh" + i);
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
            //var deployTurn = Math.max(1, slot.depavailable); 

            //Add (OPTION) at the beginning of name of options (to differentiate them from enhancements)
            if (enhIsOption) enhName = " <span style='color:rgb(224, 185, 57) ;'>(OPTION)</span> " + enhName;
            //if(enhIsOption && enhID != 'DEPLOY') enhName = " <span style='color:rgb(224, 185, 57) ;'>(OPTION)</span> " + enhName;


            const ammoTypes = ['(HEAVY AMMO)', '(MEDIUM AMMO)', '(LIGHT AMMO)', '(AMMO)'];
            for (const type of ammoTypes) {
                if (enhName.includes(type)) {
                    enhName = enhName.replace(type, '').trim();
                    enhName = ` <span style="color:rgb(106, 195, 255);">${type}</span> ` + enhName;
                    break; // Assuming only one ammo type appears in the string
                }
            }

            var nameExpanded = enhName;
            //if(enhID != 'DEPLOY'){
            nameExpanded = nameExpanded + ' (';
            if (enhLimit > 1) nameExpanded += 'up to ' + enhLimit + ' levels, ';
            nameExpanded += enhPrice + 'pts';
            //+ ' (up to ' + enhLimit + ' levels, ' + enhPrice + 'PV ';
            if ((enhPriceStep != 0) && (enhLimit > 1)) {
                nameExpanded = nameExpanded + ' plus ' + enhPriceStep + 'pts per level';
            }
            nameExpanded = nameExpanded + ')';
            //}    
            $(".selectText", item).html(nameExpanded);
            $(item).show();

            var plusButton = $(".plusButton", item);
            plusButton.data("enhNo", i);
            var minusButton = $(".minusButton", item);
            minusButton.data("enhNo", i);


            $(".plusButton", item).on("click", confirm.doOnPlusEnhancement);
            $(".minusButton", item).on("click", confirm.doOnMinusEnhancement);
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
                selectAmountItem.data('initialValue', 0); // store initial value for diff calculation                
                selectAmountItem.data('min', 0);


                //if (ship.superheavy) {
                if (ship.maxFlightSize < 3) { //here it's question of single vs multiple craft per flight, not of being superheavy
                    $(".selectText", item).html(missileOption[0] + ' (maximum amount: ' + missileOption[1] / 6 / (missileOption[3] / 6) + ', cost: ' + missileOption[2] + ')');
                    $(item).show();

                    selectAmountItem.data('max', Math.round(missileOption[1] / 6 / (missileOption[3] / 6)));
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
            $(".plusButton", item).on("click", confirm.doOnPlusMissile);
            $(".minusButton", item).on("click", confirm.doOnMinusMissile);
            // **NEW**: bind wheel to our new handler
            selectAmountItem.on("wheel", confirm.handleMouseWheelMissile);
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
            if (ship.maxFlightSize == 3) {
                selectAmountItem.html("3");
            } else {//default 
                selectAmountItem.html("6");
            }
            selectAmountItem.data('pV', Math.floor(ship.pointCost / 6));

            selectAmountItem.on("wheel", confirm.handleMouseWheelFighter);
            $(".fighterSelectItem .selectButtons .plusButton", e).on("click", confirm.increaseFlightSize);
            $(".fighterSelectItem .selectButtons .minusButton", e).on("click", confirm.decreaseFlightSize);
        }


        var nameCore = ship.shipClass;
        var nameNumber = gamedata.lastShipNumber + 1;
        var fullName = '';//by default: nameCore + ' #' + number ; name cannot be repeated!
        var accepted = false;
        var exists = false;
        while (accepted != true) {
            fullName = nameCore + ' #' + nameNumber;
            //check whether such a name doesn't yet exist...
            exists = false;
            for (var i in gamedata.ships) {
                var currShip = gamedata.ships[i];
                if (currShip.name == fullName) exists = true;
            }
            if (exists == true) {
                nameNumber++;
            } else {
                accepted = true;
            }
        }
        gamedata.lastShipNumber = nameNumber;

        $('<label>Name your new ' + ship.shipClass + ':</label><input type="text" style="text-align:center" name="shipname" value="' + fullName + '"></input><br>').prependTo(e);


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
    */

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

        var enhID = $(this).data("enhID");
        if (enhID && (enhID.startsWith("AMMO_") || enhID.startsWith("MINE_") || enhID.startsWith("SHELL_"))) {
            var ship = $(".confirmok").data("ship") || $(".confirmok").data("originalShipData");
            if (!ship) ship = gamedata.getShipByType($(".confirmok").data("shipclass"));
            var maxFit = confirm.getMaxAmmoFit(ship, enhID);
            if (value > maxFit) value = Math.max(0, maxFit);
        }

        // Update displayed value
        $(this).text(value);
        $(this).data('value', value);
        $(this).data('count', value);

        // Move cursor to end
        var range = document.createRange();
        var sel = window.getSelection();
        range.selectNodeContents(this);
        range.collapse(false);
        sel.removeAllRanges();
        sel.addRange(range);

        var enhPrice = $(this).data('enhPrice');
        var enhPriceStep = $(this).data('enhPriceStep');

        // Compute old cost
        var oldCost = 0;
        for (let i = 0; i < oldCount; i++) {
            oldCost += enhPrice + (i * enhPriceStep);
        }

        // Compute new cost
        var newCost = 0;
        for (let i = 0; i < value; i++) {
            newCost += enhPrice + (i * enhPriceStep);
        }

        // Update cost data
        var totalEnhCost = ($(this).data('enhCost') || 0) - oldCost + newCost;
        $(this).data('enhCost', totalEnhCost);

        // Same logic for enhancement options, if applicable
        if ($(this).data("enhIsOption")) {
            var oldOptionCost = 0;
            var newOptionCost = 0;
            for (let i = 0; i < oldCount; i++) {
                oldOptionCost += enhPrice + (i * enhPriceStep);
            }
            for (let i = 0; i < value; i++) {
                newOptionCost += enhPrice + (i * enhPriceStep);
            }

            var totalOptionCost = ($(this).data('enhOptionCost') || 0) - oldOptionCost + newOptionCost;
            $(this).data('enhOptionCost', totalOptionCost);
        }

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
            pointCostEnh: ship.pointCostEnh,
            pointCostEnh2: ship.pointCostEnh2
        };

        //variable flightsize
        var variableSize = confirm.getVariableSize(ship);
        var missileOptions = confirm.getMissileOptions(ship);

        //if (variableSize || missileOptions.length > 0 || ship.superheavy) {
        var totalTemplate = $(".totalUnitCost");
        var totalItem = totalTemplate.clone(true).prependTo(e);

        //allow maximum flight size pre-set in design...
        if (ship.maxFlightSize != 0) {
            $(".totalUnitCostAmount").data("maxSize", ship.maxFlightSize);
        } else {
            if (ship.jinkinglimit > 9) {
                $(".totalUnitCostAmount").data("maxSize", 12);
            } else $(".totalUnitCostAmount").data("maxSize", 9);
        }

        var pristineBaseShip = gamedata.getShipByType(ship.phpclass);
        var pointCost = pristineBaseShip ? pristineBaseShip.pointCost : ship.pointCost;
        /*
        if (ship.maxFlightSize==3){ //for single-unit flight cost is for a fighter; for usual 6+ flight, for 6 craft (and 6 craft will be set)
            //but for 3-strong flight cost is still set for 6-strong flight...
            pointCost = pointCost/2;
        }     
        */
        //ship.pointCost
        $(".totalUnitCostText", totalItem).html("Total cost");
        $(".totalUnitCostAmount", totalItem).html(pointCost);
        $(".totalUnitCostAmount", totalItem).data("value", pointCost);

        $(totalItem).show();

        $(".totalUnitCostAmount").data("value", pointCost);
        //}


        //ship enhancements
        for (var i in ship.enhancementOptions) {
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
            selectAmountItem.addClass("shpenh" + i);
            selectAmountItem.data('enhID', enhID);
            selectAmountItem.data('count', enhCount);

            var initialEnhCost = 0;
            for (let eCount = 0; eCount < enhCount; eCount++) {
                initialEnhCost += enhPrice + (eCount * enhPriceStep);
            }
            selectAmountItem.data('enhCost', initialEnhCost);
            if (enhIsOption) {
                selectAmountItem.data('enhOptionCost', initialEnhCost);
                selectAmountItem.data('enhIsOption', true);
            }

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

            //add (OPTION) at the beginning of name of options (to differentiate them from enhancements)
            if (enhIsOption) enhName = " <span style='color:rgb(224, 185, 57) ;'>(OPTION)</span> " + enhName;

            const ammoTypes = ['(HEAVY AMMO)', '(MEDIUM AMMO)', '(LIGHT AMMO)', '(AMMO)'];
            for (const type of ammoTypes) {
                if (enhName.includes(type)) {
                    enhName = enhName.replace(type, '').trim();
                    enhName = ` <span style="color:rgb(106, 195, 255);">${type}</span> ` + enhName;
                    break; // Assuming only one ammo type appears in the string
                }
            }

            var nameExpanded = enhName;
            nameExpanded = nameExpanded + ' (';
            if (enhLimit > 1) nameExpanded += 'up to ' + enhLimit + ' levels, ';
            nameExpanded += enhPrice + 'pts';
            //+ ' (up to ' + enhLimit + ' levels, ' + enhPrice + 'PV ';
            if ((enhPriceStep != 0) && (enhLimit > 1)) {
                nameExpanded = nameExpanded + ' plus ' + enhPriceStep + 'pts per level';
            }
            nameExpanded = nameExpanded + ')';

            $(".selectText", item).html(nameExpanded);
            $(item).show();

            var plusButton = $(".plusButton", item);
            plusButton.data("enhNo", i);
            var minusButton = $(".minusButton", item);
            minusButton.data("enhNo", i);


            $(".plusButton", item).on("click", confirm.doOnPlusEnhancement);
            $(".minusButton", item).on("click", confirm.doOnMinusEnhancement);
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

                // --- SET INITIAL MISSILE COUNT here ---
                // missileOption[4] assumed to be initial missile count baked into fighter cost
                var initialMissileCount = missileOption[4] || 0;

                selectAmountItem.html(initialMissileCount); // show initial count in UI
                selectAmountItem.addClass(i);
                selectAmountItem.data('value', initialMissileCount);      // current value
                selectAmountItem.data('initialValue', initialMissileCount); // store initial value for diff calculation
                selectAmountItem.data('min', 0);

                if (ship.maxFlightSize < 3) {
                    $(".selectText", item).html(missileOption[0] + ' (maximum amount: ' + missileOption[1] / 6 / (missileOption[3] / 6) + ', cost: ' + missileOption[2] + ')');
                    $(item).show();

                    selectAmountItem.data('max', Math.round(missileOption[1] / 6 / (missileOption[3] / 6)));
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

                $(".selectText", item).data("firingMode", i);

                var plusButton = $(".plusButton", item);
                plusButton.data("firingMode", i);
                $(".minusButton", item).data("firingMode", i);
            }

            $('<div class="missileselect"><label>This fighter type can carry fighter missiles.<br>\
                    Please select the amount you wish to purchase PER MISSILE LAUNCHER.<br></label>').prependTo(e);

            $(".plusButton", item).on("click", confirm.doOnPlusMissile);
            $(".minusButton", item).on("click", confirm.doOnMinusMissile);
            selectAmountItem.on("wheel", confirm.handleMouseWheelMissile);
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

            var pristineBaseShip = gamedata.getShipByType(ship.phpclass);
            var pristineBaseCost = pristineBaseShip ? pristineBaseShip.pointCost : ship.pointCost;
            selectAmountItem.data('pV', Math.floor(pristineBaseCost / 6));


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
        confirm.getTotalCost();
        a.fadeIn(250);
    },


    getVariableSize: function getVariableSize(ship) {
        //if (ship.flight && !ship.superheavy) { //superheavy is no longer a good marker
        if (ship.flight && ship.maxFlightSize != 1) { //max flight size = 1 indicates single superheavy fighter
            return true;
        } else return false;
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
        if (ship.maxFlightSize != 0) {
            $(".totalUnitCostAmount").data("maxSize", ship.maxFlightSize);
        } else {
            if (ship.jinkinglimit > 9) {
                $(".totalUnitCostAmount").data("maxSize", 12);
            } else $(".totalUnitCostAmount").data("maxSize", 9);
        }

        var pointCost = ship.pointCost;
        if (ship.maxFlightSize >= 2 && ship.maxFlightSize < 6) {
            //design pointCost is set for a 6-strong flight (cost-per-craft * 6).
            //For any fixed sub-six flight (e.g. 3-strong Breaching Pods, 2-strong BPs), scale down to actual flight size.
            //maxFlightSize 1 is single superheavy with its own pointCost; 6+ already matches the stored cost.
            pointCost = pointCost * ship.maxFlightSize / 6;
        }


        $(".totalUnitCostText", totalItem).html("Total cost");
        $(".totalUnitCostAmount", totalItem).html(pointCost);
        $(".totalUnitCostAmount", totalItem).data("value", pointCost);

        $(totalItem).show();

        $(".totalUnitCostAmount").data("value", pointCost);


        //ship enhancements
        for (var i in ship.enhancementOptions) {
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
            selectAmountItem.addClass("shpenh" + i);
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

            //Add (OPTION) at the beginning of name of options (to differentiate them from enhancements)
            if (enhIsOption) enhName = " <span style='color:rgb(224, 185, 57) ;'>(OPTION)</span> " + enhName;

            const ammoTypes = ['(HEAVY AMMO)', '(MEDIUM AMMO)', '(LIGHT AMMO)', '(AMMO)'];
            for (const type of ammoTypes) {
                if (enhName.includes(type)) {
                    enhName = enhName.replace(type, '').trim();
                    enhName = ` <span style="color:rgb(106, 195, 255);">${type}</span> ` + enhName;
                    break; // Assuming only one ammo type appears in the string
                }
            }

            var nameExpanded = enhName;
            nameExpanded = nameExpanded + ' (';
            if (enhLimit > 1) nameExpanded += 'up to ' + enhLimit + ' levels, ';
            nameExpanded += enhPrice + 'pts';
            //+ ' (up to ' + enhLimit + ' levels, ' + enhPrice + 'PV ';
            if ((enhPriceStep != 0) && (enhLimit > 1)) {
                nameExpanded = nameExpanded + ' plus ' + enhPriceStep + 'pts per level';
            }
            nameExpanded = nameExpanded + ')';

            $(".selectText", item).html(nameExpanded);
            $(item).show();

            var plusButton = $(".plusButton", item);
            plusButton.data("enhNo", i);
            var minusButton = $(".minusButton", item);
            minusButton.data("enhNo", i);


            $(".plusButton", item).on("click", confirm.doOnPlusEnhancement);
            $(".minusButton", item).on("click", confirm.doOnMinusEnhancement);
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
                selectAmountItem.data('initialValue', 0); // store initial value for diff calculation                
                selectAmountItem.data('min', 0);


                //if (ship.superheavy) {
                if (ship.maxFlightSize < 3) { //here it's question of single vs multiple craft per flight, not of being superheavy
                    $(".selectText", item).html(missileOption[0] + ' (maximum amount: ' + missileOption[1] / 6 / (missileOption[3] / 6) + ', cost: ' + missileOption[2] + ')');
                    $(item).show();

                    selectAmountItem.data('max', Math.round(missileOption[1] / 6 / (missileOption[3] / 6)));
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

                $(".selectText", item).data("firingMode", i);

                var plusButton = $(".plusButton", item);
                plusButton.data("firingMode", i);
                $(".minusButton", item).data("firingMode", i);
            }

            $('<div class="missileselect"><label>This fighter type can carry fighter missiles.<br>\
                    Please select the amount you wish to purchase PER MISSILE LAUNCHER.<br></label>').prependTo(e);

            //$(".missileSelectItem .selectButtons .plusButton", e).on("click", confirm.doOnPlusMissile);
            //$(".missileSelectItem .selectButtons .minusButton", e).on("click", confirm.doOnMinusMissile);
            //change for enhancements:
            $(".plusButton", item).on("click", confirm.doOnPlusMissile);
            $(".minusButton", item).on("click", confirm.doOnMinusMissile);
            // **NEW**: bind wheel to our new handler
            selectAmountItem.on("wheel", confirm.handleMouseWheelMissile);
        }

        if (variableSize) {
            var template = $(".missileSelectItem");
            var item = template.clone(true).prependTo(e);
            item.addClass("fighterSelectItem");

            $(".selectText", item).html("Number of fighters in this flight:");
            $(item).show();

            var selectAmountItem = $(".selectAmount", item);
            selectAmountItem.removeClass("selectAmount").addClass("fighterAmount");

            //for any fixed sub-six flight size, start at that size; otherwise default to 6
            if (ship.maxFlightSize >= 2 && ship.maxFlightSize < 6) {
                selectAmountItem.html(ship.maxFlightSize);
            } else {//default
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
        while (accepted != true) {
            fullName = nameCore + ' #' + nameNumber;
            //check whether such a name doesn't yet exist...
            exists = false;
            for (var i in gamedata.ships) {
                var currShip = gamedata.ships[i];
                if (currShip.name == fullName) exists = true;
            }
            if (exists == true) {
                nameNumber++;
            } else {
                accepted = true;
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
        confirm.getTotalCost();
        a.fadeIn(250);
    },

    showBuyBulk: function showBuyBulk(ship, callback) {
        var e = $(this.whtml);

        // Added to support Enhancement select recalculations in getTotalCost()
        var totalTemplate = $(".totalUnitCost");
        var totalItem = totalTemplate.clone(true).prependTo(e);

        $(".totalUnitCostText", totalItem).html("Total Purchase Cost");
        var totalCostAmountSpan = $(".totalUnitCostAmount", totalItem);
        totalCostAmountSpan.html(ship.pointCost);
        totalCostAmountSpan.data("value", ship.pointCost);
        totalCostAmountSpan.data("baseCost", ship.pointCost);
        totalCostAmountSpan.addClass("totalBulkCostAmount");
        $(totalItem).show();

        //ship enhancements
        for (var i in ship.enhancementOptions) {
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
            selectAmountItem.attr("contenteditable", "true");
            selectAmountItem.addClass("shpenh" + i);
            selectAmountItem.data('enhID', enhID);
            selectAmountItem.data('count', 0);
            selectAmountItem.data('enhCost', 0);
            selectAmountItem.data('min', 0);
            selectAmountItem.data('max', enhLimit);
            selectAmountItem.data('enhPrice', enhPrice);
            selectAmountItem.data('enhPriceStep', enhPriceStep);

            selectAmountItem.on("focus", confirm.selectAllTextOnFocus);
            selectAmountItem.on("input", confirm.handleInputChange);
            selectAmountItem.on("keydown", confirm.preventNonNumericInput);
            selectAmountItem.on("wheel", confirm.handleMouseWheel);

            //Add (OPTION) at the beginning of name of options
            if (enhIsOption) enhName = " <span style='color:rgb(224, 185, 57) ;'>(OPTION)</span> " + enhName;

            const ammoTypes = ['(HEAVY AMMO)', '(MEDIUM AMMO)', '(LIGHT AMMO)', '(AMMO)'];
            for (const type of ammoTypes) {
                if (enhName.includes(type)) {
                    enhName = enhName.replace(type, '').trim();
                    enhName = ` <span style="color:rgb(106, 195, 255);">${type}</span> ` + enhName;
                    break; // Assuming only one ammo type appears in the string
                }
            }

            var nameExpanded = enhName;
            nameExpanded = nameExpanded + ' (';
            if (enhLimit > 1) nameExpanded += 'up to ' + enhLimit + ' levels, ';
            nameExpanded += enhPrice + 'pts';
            if ((enhPriceStep != 0) && (enhLimit > 1)) {
                nameExpanded = nameExpanded + ' plus ' + enhPriceStep + 'pts per level';
            }
            nameExpanded = nameExpanded + ')';

            $(".selectText", item).html(nameExpanded);
            $(item).show();

            var plusButton = $(".plusButton", item);
            plusButton.data("enhNo", i);
            var minusButton = $(".minusButton", item);
            minusButton.data("enhNo", i);

            $(".plusButton", item).on("click", confirm.doOnPlusEnhancement);
            $(".minusButton", item).on("click", confirm.doOnMinusEnhancement);
        }

        if (ship.enhancementOptions && ship.enhancementOptions.length > 0) {
            $('<div class="missileselect"><label>Here you may select enhancements (applied to ALL units in this purchase).</label></div>').prependTo(e);
        }

        var totalTemplate = $(".totalUnitCost");
        var totalItem = totalTemplate.clone(true).prependTo(e);

        var pointCost = ship.pointCost;

        $(".totalUnitCostText", totalItem).html("Cost Per Unit");
        var perUnitAmountSpan = $(".totalUnitCostAmount", totalItem);
        perUnitAmountSpan.html(pointCost);
        perUnitAmountSpan.data("value", pointCost);
        perUnitAmountSpan.addClass("costPerUnitSpan");

        $(totalItem).show();


        // Unit Settings Fields
        var html = '<div class="unitSettings">';
        //html += '<div style="margin-bottom: 5px;">Mines will be placed randomly within the player\'s deployment zone boundaries based on the quantity specified. (NOTE: 10% class surcharge added separately to fleet total)</div>';
        html += '<label>Quantity: <input type="number" id="bulkQuantity" value="1" min="1" style="width: 50px; text-align: center;"></label><br>';
        html += '</div>';

        var settingsBlock = $(html).prependTo(e);

        // Add mousewheel scroll support to the input field
        $('#bulkQuantity', settingsBlock).on('wheel', function (e) {
            e.preventDefault();
            var step = parseInt($(this).attr('step')) || 1;
            var val = parseInt($(this).val()) || 1;

            if (e.originalEvent.deltaY < 0) {
                $(this).val(val + step);
            } else {
                var min = parseInt($(this).attr('min')) || 1;
                if (val - step >= min) {
                    $(this).val(val - step);
                }
            }
            confirm.getTotalCost();
        });

        $('#bulkQuantity', settingsBlock).on('input', function () {
            confirm.getTotalCost();
        });

        $('<label>Configure ' + ship.shipClass + ' Purchase:</label><br>').prependTo(e);

        $(".confirmok", e).on("click", function () {
            var q = parseInt($('#bulkQuantity', e).val());

            if (isNaN(q) || q < 1) q = 1;

            var results = {
                quantity: q
            };

            var shipclass = $(this).data("shipclass");
            callback(results, shipclass);
            $(".confirm").remove();
        });

        $(".confirmcancel", e).on("click", function () {
            $(".confirm").remove();
        });

        $(".confirmok", e).data("shipclass", ship.phpclass);

        var a = e.appendTo("body");
        confirm.getTotalCost();
        a.fadeIn(250);
    },
    /*
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

        // Update displayed value
        $(this).text(value);
        $(this).data('value', value);
        $(this).data('count', value);

        // Move cursor to end
        var range = document.createRange();
        var sel = window.getSelection();
        range.selectNodeContents(this);
        range.collapse(false);
        sel.removeAllRanges();
        sel.addRange(range);

        var enhPrice = $(this).data('enhPrice');
        var enhPriceStep = $(this).data('enhPriceStep');

        // Compute old cost
        var oldCost = 0;
        for (let i = 0; i < oldCount; i++) {
            oldCost += enhPrice + (i * enhPriceStep);
        }

        // Compute new cost
        var newCost = 0;
        for (let i = 0; i < value; i++) {
            newCost += enhPrice + (i * enhPriceStep);
        }

        // Update cost data
        var totalEnhCost = ($(this).data('enhCost') || 0) - oldCost + newCost;
        $(this).data('enhCost', totalEnhCost);

        // Same logic for enhancement options, if applicable
        if ($(this).data("enhIsOption")) {
            var oldOptionCost = 0;
            var newOptionCost = 0;
            for (let i = 0; i < oldCount; i++) {
                oldOptionCost += enhPrice + (i * enhPriceStep);
            }
            for (let i = 0; i < value; i++) {
                newOptionCost += enhPrice + (i * enhPriceStep);
            }

            var totalOptionCost = ($(this).data('enhOptionCost') || 0) - oldOptionCost + newOptionCost;
            $(this).data('enhOptionCost', totalOptionCost);
        }

        confirm.getTotalCost();
    },
    */

    showSaveFleet: function showSaveFleet(callback) {
        var e = $(this.whtml);

        /*
        var points = 0;
        for (var i in gamedata.ships) {
            var lship = gamedata.ships[i];
            if (lship.slot != gamedata.selectedSlot) continue;
            points += lship.pointCost;
        }
        */
        var defaultName = 'Unnamed Fleet';

        // Fleet name input
        $('<label>Enter Fleet Name:</label><input type="text" style="text-align:center" name="fleetname" value="' + defaultName + '"></input><br>').prependTo(e);

        // Checkbox for "public" option
        $('<label style="display:block; margin-top:8px; font-size: 12px"><input type="checkbox" id="fleetPublicCheckbox"> Tick this box to allow others to access this fleet via its ID</label><br>')
            .insertAfter(e.find("input[name='fleetname']"));

        $(".confirmok", e).on("click", callback);
        $(".confirmcancel", e).on("click", function () {
            console.log("remove");
            $(".confirm").remove();
        });

        var a = e.appendTo("body");
        a.fadeIn(250);
    },



    error: function error(msg, callback) {
        var e = $('<div class="confirm error"><div class="ui"><div class="confirmok" style="margin:auto;"></div></div></div>');
        $('<span>' + msg + '</span>').prependTo(e);
        //$('<span>ERROR</span></br>').prependTo(e);
        //$('<div class="message"><span>Name your new '+ship.shipClass+'</span></div>').prependTo(e);


        $(".confirmok", e).on("click", callback);
        $(".confirmok", e).on("click", function () {
            $(".confirm").remove();
        });
        $(".confirmcancel", e).remove();
        var a = e.appendTo("body");
        a.fadeIn(250);
    },

    confirm: function confirm(msg, callback, cancelCallback) {
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
            if (cancelCallback) cancelCallback();
        });
        $(".ok", e).css("left", "45%");
        var a = e.appendTo("body");
        a.fadeIn(250);
    },

    //New window type, simplies provide a wanring to players about a specific action they are taking, does not prevent any.
    warning: function warning(msg) {
        var e = $('<div class="confirm warning"><div class="ui"><div class="confirmok" style="margin:auto;"></div></div></div>');
        $('<span>' + msg + '</span>').prependTo(e);

        $(".confirmok", e).on("click", function () {
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
    },



    askForMultipleValues: function (msg, inputs, callback) {
        var e = $('<div class="confirm error multi-value-confirm"><div class="ui"><div class="confirmok"></div><div class="confirmcancel"></div></div></div>');
        $('<div class="multi-value-header">' + msg + '</div>').prependTo(e);

        var container = $('<div class="multi-value-container"></div>').insertAfter(e.find('.multi-value-header'));

        inputs.forEach(function (item) {
            var row = $('<div class="multi-value-row"></div>');
            var maxDisplay = item.max;
            if (item.multiplier) {
                // If multiplier is on, max refers to TOTAL pool.
                maxDisplay = item.max;
            }
            $('<span class="multi-value-label"><span class="multi-value-name">' + item.label + '</span> <span class="multi-value-max">(Max: ' + maxDisplay + ')</span></span>').appendTo(row);

            var initialValue = (item.value !== undefined) ? item.value : (item.multiplier ? 1 : (item.max || 1));
            var maxAttr = (item.max !== undefined) ? 'max="' + item.max + '"' : '';

            var inputWrapper = $('<div style="display:flex; align-items:center;"></div>').appendTo(row);

            var input = $('<input type="number" class="multiConfirmInput multi-value-input main-input" data-id="' + item.id + '" value="' + initialValue + '" min="1" ' + maxAttr + '>').appendTo(inputWrapper);

            if (item.multiplier) {
                $('<span class="multi-value-max" style="margin-left:5px; color:#DEFBFF;">dice</span>').appendTo(inputWrapper);
                // Multiplier input (count)
                var countInput = $('<input type="number" class="multiConfirmInput multi-value-input mult-input" data-id="' + item.id + '_mult" value="1" min="1" style="width:50px;">').appendTo(inputWrapper);
                $('<span class="multi-value-max" style="margin-left:5px; color:#DEFBFF;">shots</span>').appendTo(inputWrapper);
                input.on("change", function () {
                    var val = parseInt($(this).val());
                    var count = parseInt(countInput.val());
                    if (val < 1) { val = 1; $(this).val(1); }

                    // If val * count > max, reduce count
                    if (val * count > item.max) {
                        count = Math.floor(item.max / val);
                        if (count < 1) count = 1; // Should not happen if val <= max
                        countInput.val(count);
                    }
                    // Also ensure val itself isn't > max (if count is 1)
                    if (val > item.max) {
                        val = item.max;
                        $(this).val(val);
                        countInput.val(1);
                    }
                });

                countInput.on("input change keyup", function () {
                    var count = parseInt($(this).val());
                    var val = parseInt(input.val());
                    if (isNaN(val) || val < 1) val = 1;

                    // Let's cap count based on val.
                    var maxCount = Math.floor(item.max / val);

                    if (count > maxCount) {
                        count = maxCount;
                        $(this).val(count);
                    }
                    if (count < 1 && $(this).val() !== "") $(this).val(1);
                });
            } else {
                if (item.max !== undefined) {
                    // Cap on input (typing) as well as change
                    input.on("input change keyup", function () {
                        var val = parseInt($(this).val());
                        var max = parseInt($(this).attr("max")); // Use attr as source of truth if updated, or item.max

                        if (!isNaN(max)) {
                            if (val > max) $(this).val(max);
                            if (val < 1 && $(this).val() !== "") $(this).val(1);
                        }
                    });
                }
            }

            // Store reference to multiplier config on the row/input for retrieval?
            input.data("hasMultiplier", !!item.multiplier);

            row.appendTo(container);
        });

        $(".confirmok", e).on("click", function () {
            var results = {};
            var valid = true;
            $(".multiConfirmInput.main-input", e).each(function () {
                var id = $(this).data("id");
                var val = parseInt($(this).val());
                var max = parseInt($(this).attr("max"));
                var hasMult = $(this).data("hasMultiplier");

                if (isNaN(val) || val < 1) val = 1;

                if (hasMult) {
                    var multInput = $(this).siblings(".mult-input"); // Use traversal for safety
                    var count = parseInt(multInput.val());
                    if (isNaN(count)) count = 1;

                    // Final safeguard
                    if (val * count > max && !isNaN(max)) {
                        // Clamp count?
                        count = Math.floor(max / val);
                        if (count < 1) count = 1;
                    }

                    results[id] = { value: val, count: count };
                } else {
                    if (!isNaN(max) && val > max) val = max;
                    results[id] = val; // Keep legacy format for non-mult inputs? Or Standardize?
                    // Previous implementation returned raw value.
                    // Implementation plan says: "return data structure capable of handling multiplier"
                    // IF I change this to always object, I break existing `L` logic unless I update it.
                    // `L` expects `val` as number.
                    // I'll keep it as number for non-mult, and object for mult.
                    // `molecular.js` will handle the type check.
                }
            });

            callback(results);
            $(".confirm").remove();
        });

        $(".confirmcancel", e).on("click", function () {
            $(".confirm").remove();
        });

        var a = e.appendTo("body");
        a.fadeIn(250);
        $(".multiConfirmInput", e).first().focus();
    },

    // === Hangar Operations Stage 4: launch fighters/shuttles dialog ===
    //
    // Builds a per-hangar list of stored craft with size selectors and on
    // confirm pushes the picks into each hangar's pendingLaunchOrders array.
    // The Hangar's client-side doIndividualNotesTransfer (in baseSystems.js)
    // serialises pendingLaunchOrders into individualNotesTransfer right
    // before the gamedata POST — server's Hangar::doIndividualNotesTransfer
    // then persists them as hangarLaunchOrder notes for end-of-turn resolution.
    // Setting individualNotesTransfer DIRECTLY here would be wiped by the base
    // ShipSystem.doIndividualNotesTransfer at submit time.
    hangarLaunch: function hangarLaunch(ship) {
        var hangars = [];
        for (var k in ship.systems) {
            var sys = ship.systems[k];
            if (!sys || sys.name !== 'hangar') continue;
            if (!Array.isArray(sys.hangarUsage) || sys.hangarUsage.length === 0) continue;
            hangars.push(sys);
        }
        if (hangars.length === 0) return;

        // Reuse askForMultipleValues row/input styling (multi-value-confirm)
        // and layer hangar-confirm on top for the steel/cyan hangar theme.
        var e = $('<div class="confirm error multi-value-confirm hangar-confirm hangarLaunch"><div class="ui"><div class="confirmok"></div><div class="confirmcancel"></div></div></div>');
        $('<div class="multi-value-header">Launch fighters/shuttles from ' + ship.name + '</div>').prependTo(e);
        var container = $('<div class="multi-value-container"></div>').insertAfter(e.find('.multi-value-header'));

        // Track budget per hangar so we can validate the total on OK and so
        // the header label can update live as inputs change.
        var hangarBudgets = new Map();
        var budgetLabels  = new Map();         //hangar → jQuery .launch-budget span

        // Stage 8: resulting fighter facing = (carrier facing + hangar direction) % 6.
        // Carrier facing is locked in Firing Phase, so the last movement order's
        // facing is authoritative. Ships always have at least one movement entry
        // (deploy) so the array-length guard is belt-and-braces.
        var carrierFacing = 0;
        if (Array.isArray(ship.movement) && ship.movement.length > 0) {
            carrierFacing = parseInt(ship.movement[ship.movement.length - 1].facing || 0, 10);
        }

        // Stage 8: header labels use the hangar's ship location instead of a
        // bare index. Location codes mirror addPrimary/Front/Aft/Left/Right
        // (0/1/2/3/4) plus the SixSidedShip subsections (31/32 = port subs,
        // 41/42 = stbd subs). If a location has more than one hangar, suffix
        // with a 1-based counter so they remain distinguishable.
        var locationPrefixFor = function (loc) {
            var l = parseInt(loc, 10);
            if (l === 0) return 'Main';
            if (l === 1) return 'Front';
            if (l === 2) return 'Aft';
            if (l === 3 || l === 31 || l === 32) return 'Port';
            if (l === 4 || l === 41 || l === 42) return 'Stbd';
            return 'Hangar';
        };
        var prefixCounts = {};
        hangars.forEach(function (h) {
            var p = locationPrefixFor(h.location);
            prefixCounts[p] = (prefixCounts[p] || 0) + 1;
        });
        var prefixSeen = {};

        hangars.forEach(function (hangar, hidx) {
            var output = parseInt(hangar.output || 0, 10);
            var used = parseInt(hangar.launchedThisTurn || 0, 10) + parseInt(hangar.landedThisTurn || 0, 10);
            // Subtract docks already queued this submit cycle (shared budget),
            // but NOT pendingLaunchOrders — those are what THIS dialog is editing.
            if (Array.isArray(hangar.pendingDockOrders)) {
                hangar.pendingDockOrders.forEach(function (o) { used += parseInt(o.count || 0, 10); });
            }
            var budget = Math.max(0, output - used);
            hangarBudgets.set(hangar, budget);

            // Stage 8: show resulting launch facing per hangar. Suppress for
            // direction 0 (matches carrier) to keep the legacy forward-launch
            // header uncluttered.
            var hangarDir = parseInt(hangar.direction || 0, 10);
            var facingSuffix = '';
            if (hangarDir !== 0) {
                var resultFacing = (((carrierFacing + hangarDir) % 6) + 6) % 6;
                facingSuffix = ' <span class="multi-value-max">(launches at: ' + mathlib.hexFacingToAngle(resultFacing) + '°)</span>';
            }

            // Hangar header row (no input — just labels). The "remaining" span is updated
            // live by updateBudgetLabel as the user changes inputs in this hangar's rows.
            var prefix = locationPrefixFor(hangar.location);
            prefixSeen[prefix] = (prefixSeen[prefix] || 0) + 1;
            var hangarLabel = prefixCounts[prefix] > 1
                ? (prefix + ' Hangar ' + prefixSeen[prefix])
                : (prefix + ' Hangar');
            var headerRow = $('<div class="multi-value-row"></div>');
            var label = $('<span class="multi-value-label"><span class="hangar-section-name">' + hangarLabel + '</span> <span class="multi-value-max">(Hangar Capacity: <span class="launch-budget-remaining">' + budget + '</span> / ' + budget + ')</span>' + facingSuffix + '</span>');
            label.appendTo(headerRow);
            budgetLabels.set(hangar, label.find('.launch-budget-remaining'));
            container.append(headerRow);

            // Map existing pendingLaunchOrders by phpclass so we can pre-fill.
            var preByClass = {};
            if (Array.isArray(hangar.pendingLaunchOrders)) {
                hangar.pendingLaunchOrders.forEach(function (o) {
                    if (!o) return;
                    var k = o.phpclass || 'unknown';
                    preByClass[k] = (preByClass[k] || 0) + parseInt(o.size || 0, 10);
                });
            }

            // Group stored craft by phpclass for the selector
            var byClass = {};
            hangar.hangarUsage.forEach(function (entry) {
                var key = entry.phpclass || 'unknown';
                if (!byClass[key]) byClass[key] = { name: entry.name || key, count: 0 };
                byClass[key].count += parseInt(entry.flightSize || 1, 10);
            });

            Object.keys(byClass).forEach(function (cls) {
                var info = byClass[cls];
                var maxByRules = launchSizeMaxFor(cls);     // 1-6 default; SHF/BPod/Shuttle handled
                var max = Math.min(info.count, maxByRules, budget);
                var preset = Math.min(parseInt(preByClass[cls] || 0, 10), max);

                var row = $('<div class="multi-value-row"></div>');
                $('<span class="multi-value-label"><span class="hangar-craft-name">' + info.count + 'x ' + info.name + '</span> <span class="multi-value-max">(max launch: ' + max + ')</span></span>').appendTo(row);
                var inputWrapper = $('<div style="display:flex; align-items:center;"></div>').appendTo(row);
                var $input = $('<input type="number" class="multiConfirmInput multi-value-input main-input launchSize" value="' + preset + '" min="0" max="' + max + '">').appendTo(inputWrapper);

                row.data('hangar', hangar);
                row.data('phpclass', cls);
                container.append(row);

                // Live update the hangar's remaining-budget readout as the input changes.
                $input.on('input change', function () { updateBudgetLabel(hangar); });
            });
            // Seed the readout with the preset total
            updateBudgetLabel(hangar);
        });

        // Recompute "launch budget: X remaining of Y" for one hangar from the
        // current values of every launchSize input belonging to that hangar.
        function updateBudgetLabel(hangar) {
            var $span = budgetLabels.get(hangar);
            if (!$span) return;
            var budget = hangarBudgets.get(hangar) || 0;
            var allocated = 0;
            container.find('.multi-value-row').each(function () {
                var $row = $(this);
                if ($row.data('hangar') !== hangar) return;
                allocated += parseInt($('.launchSize', this).val() || 0, 10);
            });
            var remaining = budget - allocated;
            $span.text(remaining);
            $span.css('color', remaining < 0 ? '#ff6666' : '');     //red when oversubscribed
        }

        $(".confirmok", e).on("click", function () {
            // Aggregate selections per hangar (including hangars that received
            // an OK with all-zeros — we need to ship an explicit empty list so
            // the server replaces any prior order).
            var byHangar = new Map();
            // Seed every hangar we showed so a "cleared all" OK still records an empty list.
            hangars.forEach(function (h) { byHangar.set(h, []); });
            container.find('.multi-value-row').each(function () {
                var $row = $(this);
                var hangar = $row.data('hangar');
                var cls = $row.data('phpclass');
                if (!hangar) return;     // header rows have no data
                var size = parseInt($('.launchSize', this).val() || 0, 10);
                if (size <= 0) return;
                if (!byHangar.has(hangar)) byHangar.set(hangar, []);
                byHangar.get(hangar).push({ phpclass: cls, size: size });
            });

            // Validate aggregate per-hangar against the shared output budget.
            var oversub = null;
            byHangar.forEach(function (orders, hangar) {
                var total = 0;
                orders.forEach(function (o) { total += parseInt(o.size || 0, 10); });
                var budget = hangarBudgets.get(hangar) || 0;
                if (total > budget) oversub = { total: total, budget: budget };
            });
            if (oversub) {
                alert("Launch total (" + oversub.total + ") exceeds shared launch+land budget (" + oversub.budget + "). Reduce the count and try again.");
                return;
            }

            byHangar.forEach(function (orders, hangar) {
                hangar.pendingLaunchOrders = orders;
                hangar.pendingLaunchOrdersDirty = true;
            });

            $(".confirm").remove();
        });

        $(".confirmcancel", e).on("click", function () {
            $(".confirm").remove();
        });

        e.appendTo("body").fadeIn(250);

        // Per-class flight size caps per B5W §10.1.2:
        //   Shuttles 1-6, Super-Heavy Fighters 1-3, Breaching Pods 1-2,
        //   everything else: full flight (capped at 6 by default; partial
        //   is allowed when fewer than 6 remain stored).
        function launchSizeMaxFor(phpclass) {
            var lower = (phpclass || '').toLowerCase();
            if (lower === 'shuttle' || lower === 'minesweepingshuttle') return 6;
            if (lower.indexOf('shuttle') !== -1) return 6;
            if (lower.indexOf('breachingpod') !== -1 || lower.indexOf('breaching') !== -1) return 2;
            // Heuristic for super-heavy: phpclass usually contains 'SHF'/'SuperHeavy'.
            if (lower.indexOf('superheavy') !== -1 || lower.indexOf('shf') !== -1) return 3;
            return 6;
        }
    },

    // === Hangar Operations Stage 5: dock fighters dialog ===
    //
    // Flow:
    //   1. Enumerate eligible carriers via findEligibleCarriersForDock(flight).
    //   2. Open a unified splitter showing every hangar across every eligible
    //      carrier as its own row ("Ship – Hangar N" label). No carrier picker.
    //   3. Player allocates counts freely; any unallocated craft remain in space.
    //      Each input is capped at that hangar's available space so a flight
    //      larger than a single hangar cannot overflow it.
    //   4. Pre-fills from queued orders on re-edit; OK with all zeros cancels.
    //   5. replaceDockOrdersForFlight rewrites every hangar in the map so an
    //      explicit zero clears a prior queued entry; Hangar.doIndividualNotesTransfer
    //      flushes updated pendingDockOrders into the {launches, docks} payload.
    hangarDock: function hangarDock(flight) {
        if (!flight || !flight.flight) return;
        if (typeof window.findEligibleCarriersForDock !== 'function') return;

        var carriers = window.findEligibleCarriersForDock(flight);
        if (carriers.length === 0) return;

        var flightCount = countActiveCraftInFlight(flight);
        if (flightCount <= 0) return;

        // Build a flat list of all hangars across all eligible carriers.
        // hardCap = physical per-hangar limit (free boxes + reclaimable preset),
        // independent of what other rows contain.
        //
        // Stage 8: hangar labels mirror the launch dialog — use the hangar's
        // ship location (Main/Front/Aft/Port/Stbd) instead of a bare index.
        // Disambiguation counter is per-carrier so a ship with two port
        // hangars reads "Port Hangar 1 / Port Hangar 2".
        var locationPrefixFor = function (loc) {
            var l = parseInt(loc, 10);
            if (l === 0) return 'Main';
            if (l === 1) return 'Front';
            if (l === 2) return 'Aft';
            if (l === 3 || l === 31 || l === 32) return 'Port';
            if (l === 4 || l === 41 || l === 42) return 'Stbd';
            return 'Hangar';
        };
        var allRows = [];
        carriers.forEach(function (c) {
            var multiHangar = c.hangars.length > 1;
            var prefixCounts = {};
            c.hangars.forEach(function (h) {
                var p = locationPrefixFor(h.hangar.location);
                prefixCounts[p] = (prefixCounts[p] || 0) + 1;
            });
            var prefixSeen = {};
            c.hangars.forEach(function (h, idx) {
                var preset = 0;
                if (Array.isArray(h.hangar.pendingDockOrders)) {
                    h.hangar.pendingDockOrders.forEach(function (o) {
                        if (parseInt(o.flightId, 10) === parseInt(flight.id, 10)) {
                            preset += parseInt(o.count || 0, 10);
                        }
                    });
                }
                var prefix = locationPrefixFor(h.hangar.location);
                prefixSeen[prefix] = (prefixSeen[prefix] || 0) + 1;
                var hangarName = prefixCounts[prefix] > 1
                    ? (prefix + ' Hangar ' + prefixSeen[prefix])
                    : (prefix + ' Hangar');
                var label = multiHangar
                    ? c.ship.name + ' – ' + hangarName
                    : c.ship.name;
                allRows.push({
                    hangar:   h.hangar,
                    capacity: h.capacity,       // free boxes (reclaimable for re-edit)
                    preset:   preset,
                    hardCap:  h.capacity + preset, // absolute physical limit for this hangar
                    label:    label
                });
            });
        });

        var headerText = carriers.length === 1
            ? 'Dock ' + flight.name + ' (' + flightCount + ' craft) into ' + carriers[0].ship.name
            : 'Dock ' + flight.name + ' (' + flightCount + ' craft) — allocate across carriers';

        var e = $('<div class="confirm error multi-value-confirm hangar-confirm hangarDock"><div class="ui"><div class="confirmok"></div><div class="confirmcancel"></div></div></div>');
        $('<div class="multi-value-header">' + headerText + '</div>').prependTo(e);
        var container = $('<div class="multi-value-container"></div>').insertAfter(e.find('.multi-value-header'));

        $('<div class="multi-value-row"><span class="multi-value-label" style="font-style:italic;">Allocate craft to dock per hangar.</span></div>').appendTo(container);

        // Live cross-row total: updates each input's max so the sum can never exceed flightCount.
        // Built as a single span so the flex row treats it as one child (avoiding space-between split).
        var initialTotal = allRows.reduce(function (s, r) { return s + r.preset; }, 0);
        var $allocRow = $('<div class="multi-value-row" style="font-weight:bold;padding-bottom:4px;"><span>Allocating: <span class="dock-alloc-count">' + initialTotal + '</span> / ' + flightCount + ' craft</span></div>');
        var $allocLabel = $allocRow.find('.dock-alloc-count');
        container.append($allocRow);

        allRows.forEach(function (r) {
            var freeBoxes = r.capacity;
            // Initial max: physical cap clamped to flight size (other rows may tighten it live).
            var maxThis = Math.min(r.hardCap, flightCount);
            var row = $('<div class="multi-value-row"></div>');
            $('<span class="multi-value-label"><span class="hangar-section-name">' + r.label + '</span> <span class="multi-value-max">(free: ' + freeBoxes + ', max: ' + maxThis + ')</span></span>').appendTo(row);
            var inputWrapper = $('<div style="display:flex; align-items:center;"></div>').appendTo(row);
            $('<input type="number" class="multiConfirmInput multi-value-input main-input dockCount" value="' + r.preset + '" min="0" max="' + maxThis + '">').appendTo(inputWrapper);
            row.data('rowData', r);
            container.append(row);
        });

        // Recomputes the running total and tightens/relaxes each input's max so
        // the aggregate across all rows can never exceed flightCount.
        function updateDockCaps() {
            var total = 0;
            container.find('.multi-value-row').each(function () {
                var r = $(this).data('rowData');
                if (!r) return;
                total += Math.max(0, parseInt($('.dockCount', this).val() || 0, 10));
            });
            $allocLabel.text(total);
            $allocLabel.css('color', total > flightCount ? '#ff6666' : '');
            container.find('.multi-value-row').each(function () {
                var $row = $(this);
                var r = $row.data('rowData');
                if (!r) return;
                var myVal = Math.max(0, parseInt($('.dockCount', $row).val() || 0, 10));
                var remaining = flightCount - (total - myVal);   // budget available to this row
                $('.dockCount', $row).attr('max', Math.max(0, Math.min(r.hardCap, remaining)));
            });
        }
        container.on('input change', '.dockCount', updateDockCaps);
        updateDockCaps();   // seed label and caps from pre-filled presets

        $(".confirmok", e).on("click", function () {
            var allocated = 0;
            // Initialise every hangar to 0 so an explicit empty input clears
            // any prior queued entry for this flight (cancel path).
            var newOrdersByHangar = new Map();
            allRows.forEach(function (r) { newOrdersByHangar.set(r.hangar, 0); });
            container.find('.multi-value-row').each(function () {
                var $row = $(this);
                var r = $row.data('rowData');
                if (!r) return;                     // instructions / total rows
                var count = parseInt($('.dockCount', this).val() || 0, 10);
                if (count > parseInt($('.dockCount', this).attr('max') || 0, 10))
                    count = parseInt($('.dockCount', this).attr('max') || 0, 10);
                if (allocated + count > flightCount) count = flightCount - allocated;
                if (count < 0) count = 0;
                allocated += count;
                newOrdersByHangar.set(r.hangar, count);
            });
            if (allocated > flightCount) {
                alert('Total allocated (' + allocated + ') exceeds flight size (' + flightCount + '). Reduce the count and try again.');
                return;
            }
            replaceDockOrdersForFlight(flight, newOrdersByHangar);
            e.remove();
        });
        $(".confirmcancel", e).on("click", function () { e.remove(); });
        e.appendTo("body").fadeIn(250);

        // Atomically rewrite this flight's dock allocation across all hangars in
        // the map. Touches EVERY hangar (even those receiving 0) so "set to 0"
        // clears the prior queue entry rather than leaving a stale order.
        function replaceDockOrdersForFlight(flight, newOrdersByHangar) {
            var flightId = parseInt(flight.id, 10);
            newOrdersByHangar.forEach(function (count, hangar) {
                if (!Array.isArray(hangar.pendingDockOrders)) hangar.pendingDockOrders = [];
                hangar.pendingDockOrders = hangar.pendingDockOrders.filter(function (o) {
                    return parseInt(o.flightId, 10) !== flightId;
                });
                if (count > 0) hangar.pendingDockOrders.push({ flightId: flightId, count: parseInt(count, 10) });
                hangar.pendingDockOrdersDirty = true;
            });
        }

        function countActiveCraftInFlight(flight) {
            if (!Array.isArray(flight.systems)) return 0;
            var n = 0;
            flight.systems.forEach(function (ftr) {
                if (!shipManager.systems.isDestroyed(flight, ftr)) n++;
            });
            return n;
        }
    },

    // === Hangar Operations Stage 7: deployment-phase dock dialog ===
    //
    // Flow (called from DeploymentPhaseStrategy when player clicks a friendly
    // hangar ship while flights are pending deployment):
    //   1. Enumerate same-slot fighter flights that are deploying THIS turn
    //      and haven't been placed/docked yet.
    //   2. Show one row per flight with a checkbox + the hangar it would land in.
    //   3. OK: for each checked flight, queue a pendingDeployStartOrders entry
    //      on the chosen hangar AND set flight.pendingDeployDock so the client
    //      validator skips its missing position. Unchecked flights with a
    //      pre-existing pendingDeployDock get UN-docked atomically.
    //
    // On the next dialog open, currently-queued flights show pre-checked so
    // the player can amend or cancel without re-opening.
    // Issue 6: when the player clicks "DOCK flight" in the SelectFromShips
    // picker and there's more than one eligible carrier in the hex, this
    // sub-picker dialog lets them choose which carrier to dock with. Mirrors
    // the Firing-Phase carrier picker (SelectFromShips uses for that) but does
    // not allow splitting the flight — the player picks ONE carrier and the
    // whole flight goes into that carrier's first compatible hangar.
    //
    // $carriers is an array of {ship, hangars:[{hangar, capacity}, ...]} as
    // produced by SelectFromShips' eligible-carrier collector.
    hangarDeployDockCarrierPicker: function hangarDeployDockCarrierPicker(flight, carriers) {
        if (!flight || !Array.isArray(carriers) || carriers.length === 0) return;
        if (!window.DeploymentDock || typeof window.DeploymentDock.autoQueueDockOnCarrier !== 'function') return;

        var e = $('<div class="confirm error multi-value-confirm hangar-confirm hangarDeployCarrierPicker"><div class="ui"><div class="confirmcancel"></div></div></div>');
        $('<div class="multi-value-header">Dock ' + flight.name + ' — choose carrier</div>').prependTo(e);
        var container = $('<div class="multi-value-container"></div>').insertAfter(e.find('.multi-value-header'));
        $('<div class="multi-value-row"><span class="multi-value-label" style="font-style:italic;">Pick which carrier the flight should dock into.</span></div>').appendTo(container);

        carriers.forEach(function (entry) {
            var carrier = entry.ship;
            //Sum free boxes across the carrier's eligible hangars for the readout.
            var totalCapacity = 0;
            entry.hangars.forEach(function (h) { totalCapacity += parseInt(h.capacity || 0, 10); });
            var size = parseInt(flight.flightSize || 1, 10);

            var row = $('<div class="multi-value-row"></div>');
            var btn = $('<div class="name-value-button-ally" style="flex:1;">DOCK IN ' + carrier.name.toUpperCase() + ' (' + size + '/' + totalCapacity + ' boxes)</div>');
            btn.on('click', function () {
                if (window.DeploymentDock.autoQueueDockOnCarrier(carrier, flight)) {
                    e.remove();
                    if (typeof window.refreshDeploymentUIForDeployStart === 'function') {
                        window.refreshDeploymentUIForDeployStart();
                    }
                    if (typeof window.selectShipInDeploymentPhase === 'function') {
                        window.selectShipInDeploymentPhase(carrier);
                    }
                }
            });
            row.append(btn);
            container.append(row);
        });

        $(".confirmcancel", e).on("click", function () { e.remove(); });
        e.appendTo("body").fadeIn(250);
    },

    hangarDeployDock: function hangarDeployDock(carrier) {
        if (!carrier) return;
        if (!window.DeploymentDock || typeof window.DeploymentDock.findPendingFlightsForCarrier !== 'function') return;
        if (typeof window.DeploymentDock.eligibleHangarsForFlight !== 'function') return;

        var pending = window.DeploymentDock.findPendingFlightsForCarrier(carrier);

        // Pre-check flights already queued to THIS carrier (re-edit case).
        // Build a map: flightId → existing {hangar} so OK can detect "uncheck = cancel".
        var preCheckedByFlight = new Map();
        if (Array.isArray(carrier.systems)) {
            carrier.systems.forEach(function (sys) {
                if (!sys || sys.name !== 'hangar') return;
                if (!Array.isArray(sys.pendingDeployStartOrders)) return;
                sys.pendingDeployStartOrders.forEach(function (o) {
                    preCheckedByFlight.set(parseInt(o.flightId, 10), { hangar: sys });
                });
            });
        }

        // Anything queued here counts as eligible (it WAS eligible at queue time;
        // ensure it appears in the re-edit list even if no fresh hangar capacity
        // remains, since the existing reservation IS its capacity).
        var pendingIds = new Set(pending.map(function (f) { return parseInt(f.id, 10); }));
        preCheckedByFlight.forEach(function (_v, flightId) {
            if (pendingIds.has(flightId)) return;
            var f = gamedata.getShip(flightId);
            if (f) pending.push(f);
        });

        // Stage 7 / Issue 7: open the dialog even when there's nothing actionable,
        // so the player sees "No Hangar Operations available" rather than the
        // tooltip silently dropping the click. The empty-state branch below
        // renders that message and only the Cancel button.
        var e = $('<div class="confirm error multi-value-confirm hangar-confirm hangarDeployDock"><div class="ui"><div class="confirmok"></div><div class="confirmcancel"></div></div></div>');
        $('<div class="multi-value-header">Dock flights into ' + carrier.name + '</div>').prependTo(e);
        var container = $('<div class="multi-value-container"></div>').insertAfter(e.find('.multi-value-header'));

        if (pending.length === 0) {
            $('<div class="multi-value-row"><span class="multi-value-label" style="font-style:italic;">No Hangar Operations available.</span></div>').appendTo(container);
            //Hide OK — there's nothing to commit. Cancel just closes.
            $('.confirmok', e).hide();
            $(".confirmcancel", e).on("click", function () { e.remove(); });
            e.appendTo("body").fadeIn(250);
            return;
        }

        $('<div class="multi-value-row"><span class="multi-value-label" style="font-style:italic;">Check flights to dock into a hangar bay instead of placing on the map.</span></div>').appendTo(container);

        //Live per-hangar capacity readout. Recomputed on every checkbox/dropdown
        //change so the player sees overflow before pressing OK. Without this the
        //independent per-row eligibility checks at dialog open let the player
        //queue multiple flights that each individually fit but together exceed
        //the hangar — the server then silently dropped the overflow with a fail
        //note, leaving the player confused about why fewer fighters docked.
        var $capacityHeader = $('<div class="multi-value-row hangarCapacityHeader" style="font-style:italic;"></div>');
        container.append($capacityHeader);

        //Base free per hangar (treats reservations from flights NOT in the
        //dialog as committed; reservations from flights IN the dialog are
        //reclaimable since the dialog will replace them on OK).
        var rowFlightIds = new Set(pending.map(function (f) { return parseInt(f.id, 10); }));
        var baseFreeByHangar = new Map();
        carrier.systems.forEach(function (sys) {
            if (!sys || sys.name !== 'hangar') return;
            if (shipManager.systems.isDestroyed(carrier, sys)) return;
            var netDamage = 0;
            if (Array.isArray(sys.damage)) {
                sys.damage.forEach(function (d) {
                    netDamage += Math.max(0, parseInt(d.damage || 0, 10) - parseInt(d.armour || 0, 10));
                });
            }
            var effective = Math.max(0, parseInt(sys.maxhealth, 10) - netDamage);
            var committed = 0;
            if (Array.isArray(sys.hangarUsage)) {
                sys.hangarUsage.forEach(function (entry) { committed += parseInt(entry.flightSize || 1, 10); });
            }
            if (Array.isArray(sys.pendingDeployStartOrders)) {
                sys.pendingDeployStartOrders.forEach(function (o) {
                    var fid = parseInt(o.flightId, 10);
                    if (rowFlightIds.has(fid)) return;     //in dialog → reclaimable, don't double-count
                    var f = gamedata.getShip(fid);
                    if (f) committed += parseInt(f.flightSize || 1, 10);
                });
            }
            baseFreeByHangar.set(sys.id, Math.max(0, effective - committed));
        });

        // Build rows. Track per-flight {row, flight, hangarSelect (or fixed hangar)}.
        var rowData = [];
        pending.forEach(function (flight) {
            var preExisting = preCheckedByFlight.get(parseInt(flight.id, 10));
            var eligibleHangars = window.DeploymentDock.eligibleHangarsForFlight(carrier, flight);

            // If a queued allocation exists, the queued hangar must remain selectable
            // even if other rows pre-checked above re-counted capacity. Ensure it
            // shows up at the top of the list.
            if (preExisting && preExisting.hangar) {
                var alreadyListed = eligibleHangars.some(function (h) { return h.hangar === preExisting.hangar; });
                if (!alreadyListed) {
                    eligibleHangars.unshift({ hangar: preExisting.hangar, capacity: parseInt(flight.flightSize || 1, 10) });
                }
            }

            if (eligibleHangars.length === 0) return;     //no hangar can hold this flight — skip the row

            var size = parseInt(flight.flightSize || 1, 10);
            var label = flight.name + ' (' + size + ' x ' + flight.shipClass + ')';
            var row = $('<div class="multi-value-row"></div>');
            var $check = $('<input type="checkbox" class="deployDockCheck" style="margin-right:8px;">');
            if (preExisting) $check.prop('checked', true);
            var $labelSpan = $('<span class="multi-value-label"><span class="hangar-craft-name"></span></span>');
            $labelSpan.find('.hangar-craft-name').text(label);
            $check.appendTo(row);
            $labelSpan.appendTo(row);

            // Hangar dropdown (or single static label when only one option).
            var $hangarPick;
            if (eligibleHangars.length === 1) {
                var only = eligibleHangars[0];
                var hangarName = hangarLabelFor(carrier, only.hangar);
                row.append($('<span class="multi-value-max"> → ' + hangarName + '</span>'));
                $hangarPick = null;
                row.data('chosenHangar', only.hangar);
            } else {
                $hangarPick = $('<select class="multi-value-input deployDockHangar"></select>');
                eligibleHangars.forEach(function (h, i) {
                    var opt = $('<option></option>').attr('value', i).text(hangarLabelFor(carrier, h.hangar));
                    if (preExisting && preExisting.hangar === h.hangar) opt.prop('selected', true);
                    $hangarPick.append(opt);
                });
                row.append($('<span class="multi-value-max"> → </span>'));
                row.append($hangarPick);
            }

            row.data('flight', flight);
            row.data('eligibleHangars', eligibleHangars);
            //Recompute aggregate usage whenever this row's check or destination changes.
            $check.on('change', recomputeCapacity);
            if ($hangarPick) $hangarPick.on('change', recomputeCapacity);
            container.append(row);
            rowData.push(row);
        });

        if (rowData.length === 0) {
            e.remove();
            return;
        }

        recomputeCapacity();      //seed the header with the pre-checked state on open

        $(".confirmok", e).on("click", function () {
            //Issue 1: aggregate the user's selections per hangar and reject if
            //any hangar would be overfilled. Without this guard each row's
            //independent eligibility check at open time lets the player check
            //more flights than will fit (e.g. three 6-fighter flights into a
            //single 12-box hangar); the server silently drops the overflow.
            var perHangar = computePerHangarUsage();
            var overflow = [];
            perHangar.forEach(function (used, hangarId) {
                var avail = baseFreeByHangar.get(hangarId) || 0;
                if (used > avail) overflow.push(hangarLabelByIdFor(carrier, hangarId) + ' (' + used + '/' + avail + ')');
            });
            if (overflow.length > 0) {
                alert('Cannot dock: capacity exceeded in ' + overflow.join(', ') + '. Uncheck flights or pick a different hangar.');
                return;
            }

            // For each row: if checked, queue (or re-route) the dock; if unchecked,
            // un-queue via DeploymentDock.unqueueDeployStartDock so the flight
            // gets its deploy position snapped to the carrier's current hex
            // (Issue 8). Routing through DeploymentDock keeps queue mutation
            // and re-deploy logic in one place.
            rowData.forEach(function ($row) {
                var flight = $row.data('flight');
                if (!$row.find('.deployDockCheck').is(':checked')) {
                    if (flight.pendingDeployDock) {
                        window.DeploymentDock.unqueueDeployStartDock(flight);
                    }
                    return;
                }
                var hangar = $row.data('chosenHangar');
                if (!hangar) {
                    var idx = parseInt($row.find('.deployDockHangar').val() || 0, 10);
                    var eligible = $row.data('eligibleHangars');
                    hangar = eligible[idx].hangar;
                }
                //Clear any prior queue (different hangar or different carrier)
                //before re-queueing on the chosen hangar.
                if (flight.pendingDeployDock) {
                    window.DeploymentDock.unqueueDeployStartDock(flight);
                }
                queueDeployStartOrder(hangar, flight, carrier);
            });

            e.remove();
            if (typeof window.refreshDeploymentUIForDeployStart === 'function') {
                window.refreshDeploymentUIForDeployStart();
            }
            //Issue 2: after the dock commits, the selected flight may now be
            //invisible/inside the hangar — switch focus to the carrier so the
            //player can immediately move it or dock more flights.
            if (typeof window.selectShipInDeploymentPhase === 'function') {
                window.selectShipInDeploymentPhase(carrier);
            }
        });

        function computePerHangarUsage() {
            var perHangar = new Map();
            rowData.forEach(function ($row) {
                if (!$row.find('.deployDockCheck').is(':checked')) return;
                var flight = $row.data('flight');
                var hangar = $row.data('chosenHangar');
                if (!hangar) {
                    var idx = parseInt($row.find('.deployDockHangar').val() || 0, 10);
                    var eligible = $row.data('eligibleHangars');
                    if (!eligible || !eligible[idx]) return;
                    hangar = eligible[idx].hangar;
                }
                var size = parseInt(flight.flightSize || 1, 10);
                perHangar.set(hangar.id, (perHangar.get(hangar.id) || 0) + size);
            });
            return perHangar;
        }

        function recomputeCapacity() {
            var perHangar = computePerHangarUsage();
            var parts = [];
            var anyOverflow = false;
            //Walk hangars in declared order so the readout matches the dropdown labels.
            carrier.systems.forEach(function (sys) {
                if (!sys || sys.name !== 'hangar') return;
                if (!baseFreeByHangar.has(sys.id)) return;
                var avail = baseFreeByHangar.get(sys.id);
                var used = perHangar.get(sys.id) || 0;
                var color = (used > avail) ? '#ff5050' : (used > 0 ? '#ffff80' : '#bdbdbd');
                if (used > avail) anyOverflow = true;
                parts.push('<span style="color:' + color + ';">' + hangarLabelFor(carrier, sys) + ': ' + used + '/' + avail + '</span>');
            });
            $capacityHeader.html('Hangar capacity: ' + (parts.length ? parts.join(' &middot; ') : '<span style="color:#bdbdbd;">none</span>'));
            //Visual cue on OK button when overflowing — leave it clickable so the
            //alert above can explain WHICH hangar is over (clearer than greying it out).
            $('.confirmok', e).css('opacity', anyOverflow ? 0.6 : 1);
        }
        $(".confirmcancel", e).on("click", function () { e.remove(); });
        e.appendTo("body").fadeIn(250);

        function hangarLabelFor(carrier, hangar) {
            //Stage 8: ship-location prefixes (Main/Front/Aft/Port/Stbd) with
            //per-prefix disambiguation when a carrier has multiple hangars on
            //the same location.
            var prefix = (function (loc) {
                var l = parseInt(loc, 10);
                if (l === 0) return 'Main';
                if (l === 1) return 'Front';
                if (l === 2) return 'Aft';
                if (l === 3 || l === 31 || l === 32) return 'Port';
                if (l === 4 || l === 41 || l === 42) return 'Stbd';
                return 'Hangar';
            })(hangar.location);
            var siblings = carrier.systems.filter(function (s) {
                if (!s || s.name !== 'hangar') return false;
                var sl = parseInt(s.location, 10);
                var hl = parseInt(hangar.location, 10);
                // Group SixSidedShip sub-locations (31/32, 41/42) under the same prefix.
                var groupOf = function (l) {
                    if (l === 31 || l === 32) return 3;
                    if (l === 41 || l === 42) return 4;
                    return l;
                };
                return groupOf(sl) === groupOf(hl);
            });
            if (siblings.length <= 1) return prefix + ' Hangar';
            var idx = siblings.indexOf(hangar);
            return prefix + ' Hangar ' + (idx + 1);
        }

        function hangarLabelByIdFor(carrier, hangarId) {
            for (var i = 0; i < carrier.systems.length; i++) {
                var sys = carrier.systems[i];
                if (sys && sys.name === 'hangar' && sys.id === hangarId) return hangarLabelFor(carrier, sys);
            }
            return 'Hangar';
        }

        function queueDeployStartOrder(hangar, flight, carrier) {
            if (!Array.isArray(hangar.pendingDeployStartOrders)) hangar.pendingDeployStartOrders = [];
            hangar.pendingDeployStartOrders.push({ flightId: parseInt(flight.id, 10) });
            hangar.pendingDeployStartOrdersDirty = true;
            // Mark the flight so the client validator skips its missing position
            // and the deployment list reflects "this is going to a hangar."
            flight.pendingDeployDock = { carrierId: parseInt(carrier.id, 10), hangarId: parseInt(hangar.id, 10) };
        }
    }

};
