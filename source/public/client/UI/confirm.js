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

    // === LCV Rails (DockingCollar) — whole-ship dock/launch dialogs ===
    //
    // An LCV is a full ship, not a flight, so these dialogs are deliberately
    // minimal: one LCV, one rail, one click. They set the chosen rail's
    // pendingLcvDockOrders / pendingLcvLaunchOrders + dirty flag; the client
    // DockingCollar.doIndividualNotesTransfer (baseSystems.js) serialises them
    // under lcvDocks/lcvLaunches at POST, and the server resolves them
    // end-of-turn via HangarOps::processLCVDockOrders / processLCVLaunchOrders.

    // Dock dialog opened from the LCV's "Enter Hangar" button. ONE row per
    // eligible carrier, each with its own checkbox and spare-capacity readout
    // (how many free LCV rails it has). Mutually exclusive — checking one carrier
    // unchecks the others (an LCV docks to one carrier). Leaving all unchecked
    // (or unchecking the queued one) un-declares the dock. Pre-checks the carrier
    // this LCV is already queued to.
    lcvDock: function lcvDock(lcv) {
        if (!lcv || typeof window.findEligibleLCVRailsForDock !== 'function') return;
        var carriers = window.findEligibleLCVRailsForDock(lcv);
        // The LCV's already-queued rail (if any) is excluded from "free" rails by
        // lcvRailFree, so include its carrier here so the dialog can re-open /
        // un-declare even when that was the carrier's only rail.
        var queued = window.lcvQueuedDockRail(lcv);
        if (carriers.length === 0 && !queued) return;

        var thrustLeft = window.lcvRemainingThrust(lcv);
        var lcvId = parseInt(lcv.id, 10);

        var e = $('<div class="confirm error multi-value-confirm hangar-confirm lcvDock"><div class="ui"><div class="confirmok"></div><div class="confirmcancel"></div></div></div>');
        $('<div class="multi-value-header">Dock ' + lcv.name + ' to an LCV rail</div>').prependTo(e);
        var container = $('<div class="multi-value-container"></div>').insertAfter(e.find('.multi-value-header'));

        // ONE choice per FREE rail (flattened across eligible carriers), so the
        // player picks the exact rail rather than letting the dialog auto-pick the
        // carrier's first free one.
        var choices = [];   // { carrier, rail }
        carriers.forEach(function (c) {
            c.rails.forEach(function (rail) { choices.push({ carrier: c.ship, rail: rail }); });
        });
        // Ensure the already-queued rail appears (and reads checked) even though
        // lcvRailFree excludes it from the "free" list — it's the one we'd reuse.
        if (queued && !choices.some(function (c) { return c.rail.id === queued.rail.id; })) {
            choices.unshift({ carrier: queued.carrier, rail: queued.rail });
        }
        if (choices.length === 0) { e.remove(); return; }

        var rowChecks = [];   // { $chk, choice }
        choices.forEach(function (choice) {
            var isQueued = !!(queued && choice.rail.id === queued.rail.id);
            // "Carrier — LCV Rail <Location> N" so each row identifies both ship and
            // the rail's location (window.lcvRailLabel — shared with every dialog).
            var railName = window.lcvRailLabel(choice.carrier, choice.rail);
            var labelText = choice.carrier.name + ' — ' + railName;
            var row = $('<div class="multi-value-row" style="justify-content:center; gap:8px;"></div>');
            var $chk = $('<input type="checkbox" class="lcvDockCheck">');
            if (isQueued) $chk.prop('checked', true);
            var $labelSpan = $('<span class="multi-value-label" style="flex:0 0 auto; text-align:left; margin:0;"><span class="hangar-craft-name"></span></span>');
            $labelSpan.find('.hangar-craft-name').text(labelText);
            $chk.appendTo(row);
            $labelSpan.appendTo(row);
            container.append(row);
            rowChecks.push({ $chk: $chk, choice: choice });
            // Mutually exclusive: checking one rail unchecks the rest (one LCV → one rail).
            $chk.on('change', function () {
                if ($chk.is(':checked')) {
                    rowChecks.forEach(function (rc) { if (rc.$chk !== $chk) rc.$chk.prop('checked', false); });
                }
            });
        });

        $(".confirmok", e).on("click", function () {
            // Clear any prior queued dock for this LCV across ALL rails first, so
            // unchecking (or switching rails) removes the stale order.
            window.clearQueuedLcvDock(lcv);
            var picked = rowChecks.filter(function (rc) { return rc.$chk.is(':checked'); })[0];
            if (picked && picked.choice.rail) {
                var rail = picked.choice.rail;
                if (!Array.isArray(rail.pendingLcvDockOrders)) rail.pendingLcvDockOrders = [];
                rail.pendingLcvDockOrders = [{ shipId: lcvId, thrustLeft: thrustLeft }];
                rail.pendingLcvDockOrdersDirty = true;
                if (typeof rail.refreshHangarTooltip === 'function') rail.refreshHangarTooltip();
            }
            if (typeof window.refreshFiringHangarTooltips === 'function') window.refreshFiringHangarTooltips();
            e.remove();
        });
        $(".confirmcancel", e).on("click", function () { e.remove(); });
        e.appendTo("body").fadeIn(250);
    },

    // Appends an LCV-recover section (one checkbox row per eligible in-hex LCV)
    // into an existing dialog $container, for $carrier, plus a live LCV-rail
    // capacity pill strip (0/1 → 1/1, ticking as boxes are checked).
    //
    // DEFERRED COMMIT: checkboxes do NOT write orders on change — they only
    // update the live pill projection. The returned object's commit() writes the
    // checked LCVs onto free rails (and clears unchecked ones); the host dialog's
    // OK handler calls it. Returns { count, commit } where count is the number of
    // LCV rows added (0 if none eligible — host shows its own "nothing" message).
    appendLcvRecoverSection: function appendLcvRecoverSection(container, carrier) {
        var noop = { count: 0, commit: function () {} };
        if (!carrier || typeof window.findEligibleLCVsForRecover !== 'function') return noop;

        var lcvs = window.findEligibleLCVsForRecover(carrier);
        // Also include LCVs already queued to THIS carrier this turn (so they can
        // be un-declared on re-open) even though their rail is no longer "free".
        var queuedLcvIds = {};
        if (Array.isArray(carrier.systems)) {
            carrier.systems.forEach(function (s) {
                if (!window.isLCVRailSystem(s) || !Array.isArray(s.pendingLcvDockOrders)) return;
                s.pendingLcvDockOrders.forEach(function (o) {
                    var qid = parseInt(o.shipId, 10);
                    if (queuedLcvIds[qid]) return;
                    queuedLcvIds[qid] = true;
                    if (!lcvs.some(function (l) { return parseInt(l.id, 10) === qid; })) {
                        var qs = gamedata.getShip(qid);
                        if (qs) lcvs.push(qs);
                    }
                });
            });
        }
        if (lcvs.length === 0) return noop;

        // All LCV rails on the carrier, in encounter order. Each holds 0 or 1 LCV.
        var rails = carrier.systems.filter(function (s) { return window.isLCVRailSystem(s); });

        // A destroyed rail can hold nothing — shown as "destroyed", never 0/1 or 1/1.
        function railDestroyed(rail) {
            if (rail.isDestroyed && rail.isDestroyed()) return true;   //defensive
            return shipManager.systems.isDestroyed(carrier, rail);
        }

        // Which rail (id) a given dialog-row LCV is ALREADY queued to dock onto
        // (e.g. the player chose Starboard via the LCV's own dock menu). Such an
        // LCV is PINNED to that rail — the recover dialog must not re-assign it to
        // a different rail greedily (that's the "shows on Forward 1" bug).
        var pinnedRailByLcv = {};   // lcvId → railId
        rails.forEach(function (rail) {
            if (!Array.isArray(rail.pendingLcvDockOrders)) return;
            rail.pendingLcvDockOrders.forEach(function (o) {
                pinnedRailByLcv[parseInt(o.shipId, 10)] = rail.id;
            });
        });

        // rowLcvIds: the LCVs this dialog has a row for. A pending dock order for an
        // LCV NOT in this set is a cross-LCV order on some other rail we leave alone.
        var rowLcvIds = {};
        lcvs.forEach(function (l) { rowLcvIds[parseInt(l.id, 10)] = true; });

        // Rails available for the dialog to greedily assign newly-checked, NOT-yet-
        // pinned LCVs onto: not destroyed, not committed-occupied (lcvDocked), and
        // not already holding a pending dock order (which pins it to its own LCV).
        var baseFreeRailIds = rails.filter(function (r) {
            if (railDestroyed(r)) return false;
            if (r.lcvDocked && r.lcvDocked.shipId) return false;
            if (Array.isArray(r.pendingLcvDockOrders) && r.pendingLcvDockOrders.length > 0) return false;
            return true;
        }).map(function (r) { return r.id; });

        // Centred section header, then the live capacity pill strip.
        $('<div class="multi-value-row" style="justify-content:center;"><span class="hangar-section-name">LCV Rails</span></div>').appendTo(container);
        var $capRow = $('<div class="multi-value-row hangarCapacityHeader" style="font-style:normal;"></div>');
        container.append($capRow);

        var checks = [];   // { lcv, lcvId, $chk }
        function railLabel(rail, idx) {
            //Location-keyed ("LCV Rail Forward 1" …), shared across all LCV dialogs.
            return window.lcvRailLabel(carrier, rail);
        }

        // Recompute the pill strip from the current checkbox state:
        //  - destroyed rails read "destroyed" (grey, never 1/1).
        //  - a rail PINNED to a checked LCV reads 1/1 (its own LCV fills it).
        //  - base-occupied (committed lcvDocked, or pinned to a foreign LCV) read 1/1.
        //  - remaining checked LCVs that are NOT pinned fill base-free rails greedily.
        //  - overflow (more un-pinned checked LCVs than base-free rails) flagged red.
        function recompute() {
            //Pinned rails whose LCV is currently checked are "used by their own LCV";
            //un-pinned checked LCVs are the ones that still need a base-free rail.
            var unpinnedChecked = checks.filter(function (c) {
                return c.$chk.is(':checked') && pinnedRailByLcv[c.lcvId] == null;
            }).length;

            var freeIds = baseFreeRailIds.slice();
            var fillIds = {};
            for (var i = 0; i < unpinnedChecked && i < freeIds.length; i++) fillIds[freeIds[i]] = true;
            var overflow = unpinnedChecked > freeIds.length;

            //Pinned rails are "filled" only while their own LCV stays checked.
            var pinnedFill = {};
            checks.forEach(function (c) {
                var pid = pinnedRailByLcv[c.lcvId];
                if (pid != null && c.$chk.is(':checked')) pinnedFill[pid] = true;
            });

            var $pills = $('<span class="hangar-capacity-pills"></span>');
            rails.forEach(function (rail, idx) {
                if (railDestroyed(rail)) {
                    $('<span class="hangar-capacity-pill" style="color:#ff7070;"></span>')
                        .text(railLabel(rail, idx) + ': Destroyed')
                        .appendTo($pills);
                    return;
                }
                var occupied = (rail.lcvDocked && rail.lcvDocked.shipId)
                    || !!pinnedFill[rail.id]
                    || !!fillIds[rail.id];
                //A rail pinned to a FOREIGN (non-row) LCV is occupied too — that's a
                //cross-LCV dock order this dialog leaves alone. (A rail pinned to one
                //of THIS dialog's LCVs is covered by pinnedFill above, gated on its
                //checkbox.)
                if (!occupied && Array.isArray(rail.pendingLcvDockOrders)) {
                    occupied = rail.pendingLcvDockOrders.some(function (o) { return !rowLcvIds[parseInt(o.shipId, 10)]; });
                }
                var used = occupied ? 1 : 0;
                var color = used > 0 ? '#ffff80' : '#bdbdbd';
                $('<span class="hangar-capacity-pill" style="color:' + color + ';"></span>')
                    .text(railLabel(rail, idx) + ': ' + used + '/1')
                    .appendTo($pills);
            });
            if ($pills.children().length === 0) $pills.append('<span style="color:#bdbdbd;">none</span>');
            $capRow.empty().append('<span class="hangar-capacity-label">LCV rail capacity:</span>').append($pills);
            return overflow;
        }

        lcvs.forEach(function (lcv) {
            var lcvId = parseInt(lcv.id, 10);
            var preChecked = !!queuedLcvIds[lcvId];
            // Checkbox + name kept together, centred (center + gap; the row
            // default space-between would otherwise split them to the edges).
            var row = $('<div class="multi-value-row" style="justify-content:center; gap:8px;"></div>');
            var $chk = $('<input type="checkbox" class="lcvRecoverCheck">');
            if (preChecked) $chk.prop('checked', true);
            var $labelSpan = $('<span class="multi-value-label" style="flex:0 0 auto; text-align:left; margin:0;"><span class="hangar-craft-name"></span></span>');
            $labelSpan.find('.hangar-craft-name').text(lcv.name);
            $chk.appendTo(row);
            $labelSpan.appendTo(row);
            container.append(row);
            checks.push({ lcv: lcv, lcvId: lcvId, $chk: $chk });
            $chk.on('change', recompute);   //live pill update only — no commit yet
        });
        recompute();   //seed pills from pre-checked state

        function commit() {
            // Unchecked → clear any prior order (un-declare). A checked LCV that's
            // PINNED to a rail (docked via its own menu, e.g. Starboard) KEEPS that
            // rail — we don't clear+reassign it, so the dialog can't silently move it
            // onto Forward 1. Only checked, NOT-pinned LCVs are assigned greedily
            // onto the base-free rails.
            checks.forEach(function (c) {
                var checked = c.$chk.is(':checked');
                var pinned = pinnedRailByLcv[c.lcvId] != null;
                if (!checked || !pinned) window.clearQueuedLcvDock(c.lcv);   //pinned+checked: leave its order intact
            });

            var freeIds = baseFreeRailIds.slice();
            var slot = 0;
            checks.forEach(function (c) {
                if (!c.$chk.is(':checked')) return;
                if (pinnedRailByLcv[c.lcvId] != null) return;   //already on its chosen rail — leave it
                if (slot >= freeIds.length) return;   //overflow — silently skipped (recompute warned)
                var rail = carrier.systems.filter(function (s) { return s.id === freeIds[slot]; })[0];
                slot++;
                if (!rail) return;
                if (!Array.isArray(rail.pendingLcvDockOrders)) rail.pendingLcvDockOrders = [];
                rail.pendingLcvDockOrders = [{ shipId: c.lcvId, thrustLeft: window.lcvRemainingThrust(c.lcv) }];
                rail.pendingLcvDockOrdersDirty = true;
                if (typeof rail.refreshHangarTooltip === 'function') rail.refreshHangarTooltip();
            });
            if (typeof window.refreshFiringHangarTooltips === 'function') window.refreshFiringHangarTooltips();
        }

        return { count: lcvs.length, commit: commit, hasOverflow: function () { return recompute(); } };
    },

    // Standalone LCV-recover dialog (used by the LCV-rail icon click and as a
    // fallback). Wraps appendLcvRecoverSection; OK commits, cancel discards.
    lcvRecover: function lcvRecover(carrier) {
        if (!carrier) return;
        //Carry the hangarRecover class so the LCV rows inherit the same dialog-
        //specific CSS as the fighter recover dialog (confirm.css).
        var e = $('<div class="confirm error multi-value-confirm hangar-confirm hangarRecover lcvRecover"><div class="ui"><div class="confirmok"></div><div class="confirmcancel"></div></div></div>');
        $('<div class="multi-value-header">Recover LCVs onto ' + carrier.name + '</div>').prependTo(e);
        var container = $('<div class="multi-value-container"></div>').insertAfter(e.find('.multi-value-header'));
        var section = window.confirm.appendLcvRecoverSection(container, carrier);
        if (section.count === 0) {
            $('<div class="multi-value-row"><span class="multi-value-label" style="font-style:normal;">No eligible LCVs in this hex.</span></div>').appendTo(container);
        }
        $(".confirmok", e).on("click", function () {
            if (section.hasOverflow && section.hasOverflow()) {
                alert('More LCVs selected than free rails. Uncheck some and try again.');
                return;
            }
            section.commit();
            e.remove();
        });
        $(".confirmcancel", e).on("click", function () { e.remove(); });
        e.appendTo("body").fadeIn(250);
    },

    // Appends an LCV-launch section into an existing dialog $container: a centred
    // "LCV Rails" header, then one CHECKBOX row per occupied launchable rail
    // (each rail holds exactly one LCV, so a checkbox — not a 0/1 number input —
    // is the natural control; no redundant capacity note). DEFERS commit — the
    // returned commit() writes pendingLcvLaunchOrders on OK. Returns
    // { count, commit } (count 0 if no launchable LCV rail).
    appendLcvLaunchSection: function appendLcvLaunchSection(container, carrier) {
        var noop = { count: 0, commit: function () {} };
        if (!carrier || !Array.isArray(carrier.systems)) return noop;
        var rails = carrier.systems.filter(function (s) { return window.lcvRailLaunchable(carrier, s); });
        if (rails.length === 0) return noop;

        // Centred section header.
        $('<div class="multi-value-row" style="justify-content:center;"><span class="hangar-section-name">LCV Rails</span></div>').appendTo(container);

        var rowData = [];
        rails.forEach(function (rail, i) {
            var lcvId = rail.lcvDocked && rail.lcvDocked.shipId ? parseInt(rail.lcvDocked.shipId, 10) : null;
            var lcv = lcvId ? gamedata.getShip(lcvId) : null;
            var name = lcv ? lcv.name : 'LCV';
            //Location-keyed label (shared); keys off the FULL rail list so it stays
            //correct even though `rails` here is only the launchable subset.
            var railName = window.lcvRailLabel(carrier, rail) + ': ';
            var preChecked = Array.isArray(rail.pendingLcvLaunchOrders) && rail.pendingLcvLaunchOrders.length > 0;

            // center + gap keeps the checkbox and name together, centred (the
            // row's default space-between would split them to the edges).
            var row = $('<div class="multi-value-row" style="justify-content:center; gap:8px;"></div>');
            var $chk = $('<input type="checkbox" class="lcvLaunchCheck">');
            if (preChecked) $chk.prop('checked', true);
            var $labelSpan = $('<span class="multi-value-label" style="flex:0 0 auto; text-align:left; margin:0;"><span class="hangar-craft-name"></span></span>');
            $labelSpan.find('.hangar-craft-name').text(railName + name);
            $chk.appendTo(row);
            $labelSpan.appendTo(row);
            container.append(row);
            rowData.push({ $chk: $chk, rail: rail, lcvId: lcvId });
        });

        function commit() {
            rowData.forEach(function (rd) {
                var launch = rd.$chk.is(':checked');
                if (!Array.isArray(rd.rail.pendingLcvLaunchOrders)) rd.rail.pendingLcvLaunchOrders = [];
                rd.rail.pendingLcvLaunchOrders = (launch && rd.lcvId) ? [{ shipId: rd.lcvId }] : [];
                rd.rail.pendingLcvLaunchOrdersDirty = true;
                if (typeof rd.rail.refreshHangarTooltip === 'function') rd.rail.refreshHangarTooltip();
            });
            if (typeof window.refreshFiringHangarTooltips === 'function') window.refreshFiringHangarTooltips();
        }

        return { count: rails.length, commit: commit };
    },

    // Appends a DEPLOYMENT-phase LCV un-dock section into an existing dialog
    // $container: a centred "LCV Rails" header, then one CHECKBOX row per rail
    // that currently holds a pending deploy-dock LCV (pendingLcvDeployStartOrders),
    // pre-checked. Unchecking + OK calls DeploymentDock.unqueueLcvDeployDock so the
    // LCV is released back onto the map (no board position yet — the player places
    // it). DEFERS commit — the returned commit() applies the un-checks on OK.
    // Returns { count, commit } (count 0 if no deploy-docked LCV rail). Mirrors
    // appendLcvLaunchSection's shape so hangarDeployDock can fold it in.
    appendLcvDeployDockSection: function appendLcvDeployDockSection(container, carrier) {
        var noop = { count: 0, commit: function () {} };
        if (!carrier || !Array.isArray(carrier.systems)) return noop;
        if (!window.isLCVRailSystem) return noop;

        // Rails holding a pending deploy-dock LCV this session.
        var rails = carrier.systems.filter(function (s) {
            return window.isLCVRailSystem(s)
                && Array.isArray(s.pendingLcvDeployStartOrders)
                && s.pendingLcvDeployStartOrders.length > 0;
        });
        if (rails.length === 0) return noop;

        $('<div class="multi-value-row" style="justify-content:center;"><span class="hangar-section-name">LCV Rails</span></div>').appendTo(container);

        var rowData = [];
        rails.forEach(function (rail) {
            var lcvId = parseInt(rail.pendingLcvDeployStartOrders[0].shipId, 10);
            var lcv = lcvId ? gamedata.getShip(lcvId) : null;
            var name = (lcv && lcv.name) ? lcv.name : 'LCV';
            //Location-keyed label ("LCV Rail Forward 1" …) shared with all dialogs.
            var railName = window.lcvRailLabel(carrier, rail) + ': ';

            var row = $('<div class="multi-value-row" style="justify-content:center; gap:8px;"></div>');
            var $chk = $('<input type="checkbox" class="lcvDeployDockCheck">');
            $chk.prop('checked', true);   //docked here now → checked; uncheck to release
            var $labelSpan = $('<span class="multi-value-label" style="flex:0 0 auto; text-align:left; margin:0;"><span class="hangar-craft-name"></span></span>');
            $labelSpan.find('.hangar-craft-name').text(railName + name);
            $chk.appendTo(row);
            $labelSpan.appendTo(row);
            container.append(row);
            rowData.push({ $chk: $chk, rail: rail, lcv: lcv });
        });

        function commit() {
            rowData.forEach(function (rd) {
                //Still checked → leave the deploy-dock queued. Unchecked → release the
                //LCV back to the map via DeploymentDock (clears the rail's pending
                //order AND the LCV's pendingLcvDeployDock marker).
                if (rd.$chk.is(':checked')) return;
                if (rd.lcv && window.DeploymentDock
                    && typeof window.DeploymentDock.unqueueLcvDeployDock === 'function') {
                    window.DeploymentDock.unqueueLcvDeployDock(rd.lcv);
                }
            });
        }

        return { count: rails.length, commit: commit };
    },

    // Standalone LCV-launch dialog (LCV-rail icon click / fallback). Wraps
    // appendLcvLaunchSection; OK commits, cancel discards.
    lcvLaunch: function lcvLaunch(carrier) {
        if (!carrier) return;
        //Carry the hangarLaunch class so the LCV rows inherit the same launch-
        //dialog CSS (section-header indent etc.) as the fighter launch dialog.
        var e = $('<div class="confirm error multi-value-confirm hangar-confirm hangarLaunch lcvLaunch"><div class="ui"><div class="confirmok"></div><div class="confirmcancel"></div></div></div>');
        $('<div class="multi-value-header">Launch LCVs from ' + carrier.name + '</div>').prependTo(e);
        var container = $('<div class="multi-value-container"></div>').insertAfter(e.find('.multi-value-header'));
        var section = window.confirm.appendLcvLaunchSection(container, carrier);
        if (section.count === 0) {
            $('<div class="multi-value-row"><span class="multi-value-label" style="font-style:normal;">No launchable LCVs on this carrier.</span></div>').appendTo(container);
        }
        $(".confirmok", e).on("click", function () { section.commit(); e.remove(); });
        $(".confirmcancel", e).on("click", function () { e.remove(); });
        e.appendTo("body").fadeIn(250);
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
    // === Hangar Operations Stage 21: no-split launch dialog ===
    //
    // A docked flight is ONE entry (with a dockedFlightId) on its primary bay,
    // its occupancy spanning bays. The dialog shows ONE row per docked flight
    // (not per phpclass-per-bay): the flight name, its docked size, and a 0–N
    // stepper. On OK each row emits {phpclass, size, dockedFlightId, direction}
    // onto the bay where its entry lives; the server's carrier-level launch
    // coalescer (processWholeFlightLaunches) resolves each by dockedFlightId —
    // full launch resurrects the ship, partial spawns a "- Split" K-flight and
    // shrinks the original in place.
    //
    // Catapults (single fighter, no dockedFlightId/occupancy) keep the simple
    // per-phpclass rows since they launch via the legacy per-bay path.
    hangarLaunch: function hangarLaunch(ship) {
        // Collect every hangar-family system. A bay is shown if it holds its own
        // craft OR is referenced by another bay's multi-bay occupancy (so a rail
        // holding 6 boxes of a flight hosted on a sibling rail still appears with
        // an accurate 6/6 header). Stage 21: a flight spanning two rails must show
        // BOTH rails, not just the host.
        var all = [];
        for (var k in ship.systems) {
            var sys = ship.systems[k];
            if (!sys || (sys.name !== 'hangar' && sys.name !== 'catapult' && sys.name !== 'fighterRail')) continue;
            all.push(sys);
        }
        // Set of system ids referenced by any occupancy list on the ship.
        var occupiedIds = {};
        all.forEach(function (h) {
            if (!Array.isArray(h.hangarUsage)) return;
            h.hangarUsage.forEach(function (e) {
                if (Array.isArray(e.occupancy)) e.occupancy.forEach(function (o) { occupiedIds[parseInt(o.systemId, 10)] = true; });
            });
        });
        var hangars = all.filter(function (h) {
            var hasOwn = Array.isArray(h.hangarUsage) && h.hangarUsage.length > 0;
            return hasOwn || occupiedIds[parseInt(h.id, 10)];
        });
        if (hangars.length === 0) return;

        return window.confirm.hangarLaunchNoSplit(ship, hangars);
    },

    // Renders the Stage-21 per-docked-flight launch dialog.
    hangarLaunchNoSplit: function hangarLaunchNoSplit(ship, hangars) {
        var e = $('<div class="confirm error multi-value-confirm hangar-confirm hangarLaunch"><div class="ui"><div class="confirmok"></div><div class="confirmcancel"></div></div></div>');
        $('<div class="multi-value-header">Launch from ' + ship.name + '</div>').prependTo(e);
        var container = $('<div class="multi-value-container"></div>').insertAfter(e.find('.multi-value-header'));

        function launchSizeMaxFor(phpclass) {
            var lower = (phpclass || '').toLowerCase();
            if (lower.indexOf('shuttle') !== -1) return 6;
            if (lower.indexOf('breachingpod') !== -1 || lower.indexOf('breaching') !== -1) return 2;
            if (lower.indexOf('superheavy') !== -1 || lower.indexOf('shf') !== -1) return 3;
            return 12;   //no-split: a docked flight relaunches up to its full size
        }
        function locationPrefixFor(loc) {
            var l = parseInt(loc, 10);
            if (l === 0) return 'Main';
            if (l === 1) return 'Front';
            if (l === 2) return 'Aft';
            if (l === 3 || l === 31 || l === 32) return 'Port';
            if (l === 4 || l === 41 || l === 42) return 'Stbd';
            return 'Hangar';
        }

        // Boxes used in a hangar by its own entries (a multi-bay entry's boxes
        // are counted on the bays its occupancy names; an entry without
        // occupancy counts its full size on its own bay).
        function boxesUsedInHangar(hangar) {
            var used = 0;
            (hangar.hangarUsage || []).forEach(function (e) {
                //Fractional-safe per-craft box cost (ultralight 0.5). Occupancy boxes
                //are whole ints; non-occupancy entries cost flightSize*bpc.
                var bpc = e.boxesPerCraft ? (parseFloat(e.boxesPerCraft) || 1) : 1;
                if (Array.isArray(e.occupancy)) {
                    e.occupancy.forEach(function (o) {
                        if (parseInt(o.systemId, 10) === parseInt(hangar.id, 10)) used += parseInt(o.boxes || 0, 10);
                    });
                } else {
                    used += parseInt(e.flightSize || 1, 10) * bpc;
                }
            });
            // Plus boxes other bays' occupancy places on this one.
            hangars.forEach(function (oh) {
                if (oh === hangar) return;
                (oh.hangarUsage || []).forEach(function (e) {
                    if (!Array.isArray(e.occupancy)) return;
                    e.occupancy.forEach(function (o) {
                        if (parseInt(o.systemId, 10) === parseInt(hangar.id, 10)) used += parseInt(o.boxes || 0, 10);
                    });
                });
            });
            return used;
        }

        // Hangar display labels (Main/Front/.../Rail N).
        var prefixCounts = {}, catTotal = 0, railTotal = 0;
        hangars.forEach(function (h) {
            if (h.isCatapult || h.name === 'catapult') { catTotal++; return; }
            if (h.isRail || h.name === 'fighterRail') { railTotal++; return; }
            var p = locationPrefixFor(h.location);
            prefixCounts[p] = (prefixCounts[p] || 0) + 1;
        });
        var prefixSeen = {}, railSeen = 0;
        function hangarLabelFor(hangar) {
            if (hangar.isRail || hangar.name === 'fighterRail') { railSeen++; return (railTotal > 1) ? ('Fighter Rail ' + railSeen) : 'Fighter Rail'; }
            var prefix = locationPrefixFor(hangar.location);
            prefixSeen[prefix] = (prefixSeen[prefix] || 0) + 1;
            return prefixCounts[prefix] > 1 ? (prefix + ' Hangar ' + prefixSeen[prefix]) : (prefix + ' Hangar');
        }

        // ----- Per-hangar sections: capacity header + docked-flight rows +
        //       anonymous-stash (shuttle/orphan) rows -----
        // Each non-catapult bay has its own launch budget (output - this-turn
        // launches/lands - queued docks). A launch CHARGES the bays it draws
        // from: an anonymous-stash row charges its own bay; a docked-flight row
        // charges its occupancy bays smallest-first (matching the server drain).
        var rowData = [];   // {$input, dockedFlightId, phpclass, entryHangar, max, isAnon, isCat, occBays}
        var dirChoice = new Map();    // hangar -> chosen direction (multi-dir bays)
        var bayBudget = new Map();    // hangar -> base launch budget (number)
        var budgetSpans = new Map();  // hangar -> jQuery .launch-budget-remaining span
        var bayLabels = new Map();    // hangar -> display label (captured once)
        function hangarLabelForId(hangar) { return bayLabels.get(hangar) || 'Bay'; }

        function baseBudget(hangar) {
            var out = parseInt(hangar.output || 0, 10);
            var used = parseInt(hangar.launchedThisTurn || 0, 10) + parseInt(hangar.landedThisTurn || 0, 10);
            if (Array.isArray(hangar.pendingDockOrders)) {
                hangar.pendingDockOrders.forEach(function (o) { used += parseInt(o.count || 0, 10); });
            }
            return Math.max(0, out - used);
        }
        // Distribute K craft of a docked row across its occupancy bays
        // smallest-bay-first → {hangarId: charge}. perCraftBoxes converts boxes
        // to craft; bays drain by craft.
        function distributeDockedCharge(rd, k) {
            var out = {};
            var remaining = k;
            (rd.occBays || []).forEach(function (b) {
                if (remaining <= 0) return;
                var take = Math.min(remaining, b.craft);
                if (take <= 0) return;
                out[b.hangarId] = (out[b.hangarId] || 0) + take;
                remaining -= take;
            });
            return out;
        }
        // Craft this row charges to each bay it draws from, for a given value k.
        function chargesForRow(rd, k) {
            if (k <= 0) return {};
            if (rd.dockedFlightId > 0 && rd.occBays && rd.occBays.length) {
                return distributeDockedCharge(rd, k);
            }
            var out = {};
            out[parseInt(rd.entryHangar.id, 10)] = k;
            return out;
        }
        // Sum the craft every row charges to each bay → {hangarId: craft}.
        function tallyCharges() {
            var charged = {};
            rowData.forEach(function (rd) {
                if (rd.isCat) return;   // catapults have no budget
                var c = chargesForRow(rd, parseInt(rd.$input.val() || 0, 10));
                Object.keys(c).forEach(function (hid) { charged[hid] = (charged[hid] || 0) + c[hid]; });
            });
            return charged;
        }
        // True if the current allocation pushes any bay past its launch budget.
        function anyBayOverBudget(charged) {
            var over = false;
            bayBudget.forEach(function (base, hangar) {
                if ((charged[parseInt(hangar.id, 10)] || 0) > base) over = true;
            });
            return over;
        }
        // Recompute every bay's "remaining" readout, and stop a row from being
        // raised once its bay's budget is spent: if the latest edit tips any bay
        // over, revert ONLY that row to its last accepted value (other rows are
        // never altered to make room).
        function updateBudgets() {
            rowData.forEach(function (rd) {
                if (rd.isCat) return;   // catapults have no budget
                var cur = parseInt(rd.$input.val() || 0, 10);
                if (cur < 0) cur = 0;
                // Only an INCREASE can newly break a budget; if this row went up
                // and the result is over budget, roll just this row back.
                if (cur > (rd.lastVal || 0) && anyBayOverBudget(tallyCharges())) {
                    rd.$input.val(rd.lastVal || 0);
                } else {
                    rd.lastVal = cur;
                }
            });

            var charged = tallyCharges();
            budgetSpans.forEach(function ($span, hangar) {
                var base = bayBudget.get(hangar) || 0;
                var rem = base - (charged[parseInt(hangar.id, 10)] || 0);
                $span.text(rem);
                $span.css('color', rem <= 0 ? '#ff6666' : '');
            });
        }

        // Pre-fill helper: sum prior pendingLaunchOrders on a bay matching a key.
        function presetFor(hangar, matchFn) {
            var n = 0;
            if (Array.isArray(hangar.pendingLaunchOrders)) {
                hangar.pendingLaunchOrders.forEach(function (o) { if (o && matchFn(o)) n += parseInt(o.size || 0, 10); });
            }
            return n;
        }

        hangars.forEach(function (hangar) {
            var isCat = !!(hangar.isCatapult || hangar.name === 'catapult');
            if (isCat) return;   // catapults handled below
            if (!Array.isArray(hangar.hangarUsage)) hangar.hangarUsage = [];

            var cap = parseInt(hangar.maxhealth || 0, 10);   // box capacity
            var used = boxesUsedInHangar(hangar);            // incl. foreign occupancy
            var budget = baseBudget(hangar);
            bayBudget.set(hangar, budget);

            // Multi-direction picker (rails/Hyperion).
            var dirOptions = Array.isArray(hangar.directions) ? hangar.directions.slice() : [];
            var prevDir = null;
            if (Array.isArray(hangar.pendingLaunchOrders)) {
                for (var pi = 0; pi < hangar.pendingLaunchOrders.length; pi++) {
                    var po = hangar.pendingLaunchOrders[pi];
                    if (po && typeof po.direction !== 'undefined') { prevDir = parseInt(po.direction, 10); break; }
                }
            }
            var dirSelectHtml = '';
            if (dirOptions.length > 1) {
                var picked = (prevDir !== null && dirOptions.indexOf(prevDir) !== -1) ? prevDir : parseInt(dirOptions[0], 10);
                dirChoice.set(hangar, picked);
                var opts = '';
                for (var di = 0; di < dirOptions.length; di++) {
                    var d = parseInt(dirOptions[di], 10);
                    opts += '<option value="' + d + '"' + (d === picked ? ' selected' : '') + '>' + mathlib.hexFacingToAngle(d) + '°</option>';
                }
                dirSelectHtml = ' <span class="multi-value-max">Launch at: <select class="multiConfirmInput hangarDirSelect" data-hid="' + hangar.id + '">' + opts + '</select></span>';
            } else if (dirOptions.length === 1) {
                dirChoice.set(hangar, parseInt(dirOptions[0], 10));
                var ed = parseInt(dirOptions[0], 10);
                if (ed !== 0) dirSelectHtml = ' <span class="multi-value-max">(launches at: ' + mathlib.hexFacingToAngle(ed) + '°)</span>';
            }

            // Hangar header row: capacity used/total + LIVE launch budget.
            // (Pass 1 — all bay headers are grouped first, launch rows follow in
            // Pass 2 below, so a multi-bay flight's input isn't wedged between two
            // bay labels.)
            var label = hangarLabelFor(hangar);
            bayLabels.set(hangar, label);
            var headerRow = $('<div class="multi-value-row"></div>');
            $('<span class="multi-value-label"><span class="hangar-section-name">' + label + '</span> <span class="multi-value-max">(Capacity: ' + Math.ceil(used) + '/' + cap + ' boxes · launch budget: <span class="launch-budget-remaining">' + budget + '</span>/' + budget + ')</span>' + dirSelectHtml + '</span>').appendTo(headerRow);
            container.append(headerRow);
            budgetSpans.set(hangar, headerRow.find('.launch-budget-remaining'));
        });

        // Separator between the bay-capacity overview and the launch rows.
        //$('<div class="multi-value-row" style="border-top:1px solid rgba(120,160,200,0.35); margin-top:4px; padding-top:4px;"><span class="multi-value-label"><span class="hangar-section-name">Launch</span></span></div>').appendTo(container);

        // ----- Pass 2: launch rows (one per docked flight, then anonymous) -----
        hangars.forEach(function (hangar) {
            var isCat = !!(hangar.isCatapult || hangar.name === 'catapult');
            if (isCat) return;
            if (!Array.isArray(hangar.hangarUsage)) return;
            var budget = bayBudget.get(hangar) || 0;

            // Docked-flight rows (one per dockedFlightId entry).
            hangar.hangarUsage.forEach(function (entry) {
                if (!entry || entry.cannotLaunch) return;
                var dfid = parseInt(entry.dockedFlightId || 0, 10);
                if (dfid <= 0) return;   // anonymous handled below
                var cls = entry.phpclass || 'unknown';
                var size = parseInt(entry.flightSize || 1, 10);
                //Fractional-safe (ultralight 0.5): occupancy box→craft is floor(boxes/bpc).
                var bpc = entry.boxesPerCraft ? (parseFloat(entry.boxesPerCraft) || 1) : 1;
                var max = Math.min(size, launchSizeMaxFor(cls));
                var nm = entry.name || cls;

                // Occupancy bays for live budget charging (smallest-first).
                var occBays = [];
                if (Array.isArray(entry.occupancy)) {
                    entry.occupancy.forEach(function (o) {
                        occBays.push({ hangarId: parseInt(o.systemId, 10), craft: Math.floor(parseInt(o.boxes || 0, 10) / bpc) });
                    });
                } else {
                    occBays.push({ hangarId: parseInt(hangar.id, 10), craft: size });
                }
                occBays.sort(function (a, b) { return a.craft - b.craft; });
                var bayNote = (occBays.length > 1) ? (' across ' + occBays.length + ' bays') : '';

                var preset = Math.min(presetFor(hangar, function (o) { return parseInt(o.dockedFlightId || 0, 10) === dfid; }), max);

                var row = $('<div class="multi-value-row"></div>');
                $('<span class="multi-value-label"><span class="hangar-craft-name">' + nm + '</span> <span class="multi-value-max">(' + size + ' docked' + bayNote + ', max ' + max + ')</span></span>').appendTo(row);
                var iw = $('<div style="display:flex; align-items:center;"></div>').appendTo(row);
                var $in = $('<input type="number" class="multiConfirmInput multi-value-input main-input launchSize" value="' + preset + '" min="0" max="' + max + '">').appendTo(iw);
                container.append(row);
                $in.on('input change', updateBudgets);
                rowData.push({ $input: $in, dockedFlightId: dfid, phpclass: cls, entryHangar: hangar, max: max, occBays: occBays });
            });

            // Anonymous stash rows (auto-filled shuttles / orphans), grouped by
            // phpclass — these fresh-spawn on launch (no dockedFlightId).
            var anon = {};
            hangar.hangarUsage.forEach(function (entry) {
                if (!entry || entry.cannotLaunch) return;
                if (parseInt(entry.dockedFlightId || 0, 10) > 0) return;   // docked, handled above
                var cls = entry.phpclass || 'unknown';
                if (!anon[cls]) anon[cls] = { name: entry.name || cls, count: 0 };
                anon[cls].count += parseInt(entry.flightSize || 1, 10);
            });
            Object.keys(anon).forEach(function (cls) {
                var info = anon[cls];
                var max = Math.min(info.count, launchSizeMaxFor(cls), budget);
                var preset = Math.min(presetFor(hangar, function (o) { return (o.phpclass === cls) && !(parseInt(o.dockedFlightId || 0, 10) > 0); }), max);
                var fromBay = bayLabels.get(hangar) || '';
                var row = $('<div class="multi-value-row"></div>');
                $('<span class="multi-value-label"><span class="hangar-craft-name">' + info.count + 'x ' + info.name + '</span> <span class="multi-value-max">(' + fromBay + ', max ' + max + ')</span></span>').appendTo(row);
                var iw = $('<div style="display:flex; align-items:center;"></div>').appendTo(row);
                var $in = $('<input type="number" class="multiConfirmInput multi-value-input main-input launchSize" value="' + preset + '" min="0" max="' + max + '">').appendTo(iw);
                container.append(row);
                $in.on('input change', updateBudgets);
                rowData.push({ $input: $in, dockedFlightId: 0, phpclass: cls, entryHangar: hangar, max: max, isAnon: true });
            });
        });

        // ----- Catapults: single-fighter, launched via the legacy per-bay path -----
        var catSeen = 0, catTotal = 0;
        hangars.forEach(function (h) { if (h.isCatapult || h.name === 'catapult') catTotal++; });
        hangars.forEach(function (hangar) {
            if (!(hangar.isCatapult || hangar.name === 'catapult')) return;
            if (!Array.isArray(hangar.hangarUsage)) return;
            hangar.hangarUsage.forEach(function (entry) {
                if (!entry) return;
                var cls = entry.phpclass || 'unknown';
                catSeen++;
                var label = (catTotal > 1 ? ('Catapult ' + catSeen) : 'Catapult');
                if (entry.cannotLaunch) {
                    $('<div class="multi-value-row"><span class="multi-value-label" style="opacity:0.5;"><span class="hangar-craft-name">' + label + ': ' + (entry.name || cls) + '</span> <span class="multi-value-max">(wrecked — cannot relaunch)</span></span></div>').appendTo(container);
                    return;
                }
                var row = $('<div class="multi-value-row"></div>');
                $('<span class="multi-value-label"><span class="hangar-craft-name">' + label + ': ' + (entry.name || cls) + '</span> <span class="multi-value-max">(max 1)</span></span>').appendTo(row);
                var inputWrapper = $('<div style="display:flex; align-items:center;"></div>').appendTo(row);
                var $input = $('<input type="number" class="multiConfirmInput multi-value-input main-input launchSize" value="0" min="0" max="1">').appendTo(inputWrapper);
                container.append(row);
                // Catapults route through the legacy per-bay processLaunchOrders, so
                // they use {phpclass,size} (no dockedFlightId needed). entryHangar is
                // the catapult itself; mark isCat so OK puts it on that bay.
                rowData.push({ $input: $input, dockedFlightId: 0, phpclass: cls, entryHangar: hangar, max: 1, isCat: true });
            });
        });

        // Wire direction selects.
        container.find('.hangarDirSelect').on('change', function () {
            var hid = parseInt($(this).attr('data-hid'), 10);
            var val = parseInt($(this).val(), 10);
            hangars.forEach(function (h) { if (parseInt(h.id, 10) === hid) dirChoice.set(h, val); });
        });

        if (rowData.length === 0) {
            $('<div class="multi-value-row"><span class="multi-value-label">No docked flights to launch.</span></div>').appendTo(container);
        }

        // Seed the live budget readouts from any pre-filled (re-edit) values.
        updateBudgets();

        //Combined view: append any launchable LCV rails below the fighter rows so
        //a carrier with both shows them together. Deferred commit — called from
        //the OK handler below after the fighter launch orders are built.
        var lcvLaunchSection = window.confirm.appendLcvLaunchSection(container, ship);

        $(".confirmok", e).on("click", function () {
            // Build per-bay order lists keyed by the entry's hangar. Seed EVERY
            // bay we showed (incl. catapults) so a cleared row ships an explicit
            // empty list and cancels any prior queued order.
            var byHangar = new Map();
            hangars.forEach(function (h) { byHangar.set(h, []); });

            // Validate per-bay budget (each bay's output is its own launch rate;
            // a multi-bay docked launch charges its occupancy bays smallest-first).
            var charged = {};   // hangarId -> craft charged
            rowData.forEach(function (rd) {
                var size = parseInt(rd.$input.val() || 0, 10);
                if (size <= 0) return;
                if (size > rd.max) size = rd.max;
                if (!rd.isCat) {
                    if (rd.dockedFlightId > 0 && rd.occBays && rd.occBays.length) {
                        var dist = distributeDockedCharge(rd, size);
                        Object.keys(dist).forEach(function (hid) { charged[hid] = (charged[hid] || 0) + dist[hid]; });
                    } else {
                        var hid = parseInt(rd.entryHangar.id, 10);
                        charged[hid] = (charged[hid] || 0) + size;
                    }
                }
                var order = { phpclass: rd.phpclass, size: size };
                if (rd.dockedFlightId > 0) order.dockedFlightId = rd.dockedFlightId;
                if (dirChoice.has(rd.entryHangar)) order.direction = dirChoice.get(rd.entryHangar);
                if (!byHangar.has(rd.entryHangar)) byHangar.set(rd.entryHangar, []);
                byHangar.get(rd.entryHangar).push(order);
            });

            var over = null;
            bayBudget.forEach(function (base, hangar) {
                var c = charged[parseInt(hangar.id, 10)] || 0;
                if (c > base) over = { label: hangarLabelForId(hangar), c: c, base: base };
            });
            if (over) {
                alert("Launch from " + over.label + " (" + over.c + ") exceeds that bay's launch rate (" + over.base + "). Reduce and retry.");
                return;
            }

            byHangar.forEach(function (orders, hangar) {
                hangar.pendingLaunchOrders = orders;
                hangar.pendingLaunchOrdersDirty = true;
            });

            //Commit the LCV launch selections alongside the fighter launches.
            if (lcvLaunchSection.commit) lcvLaunchSection.commit();

            if (typeof window.refreshFiringHangarTooltips === 'function') window.refreshFiringHangarTooltips();
            $(".confirm").remove();
        });
        $(".confirmcancel", e).on("click", function () { $(".confirm").remove(); });
        e.appendTo("body").fadeIn(250);
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
            //Stage 16: catapults labelled "Catapult"/"Catapult N", counted apart
            //from the hangar prefix tallies.
            var prefixCounts = {};
            var catapultTotal = 0;
            var railTotal = 0;
            c.hangars.forEach(function (h) {
                if (h.hangar.isCatapult || h.hangar.name === 'catapult') { catapultTotal++; return; }
                if (h.hangar.isRail || h.hangar.name === 'fighterRail') { railTotal++; return; }
                var p = locationPrefixFor(h.hangar.location);
                prefixCounts[p] = (prefixCounts[p] || 0) + 1;
            });
            var prefixSeen = {};
            var catapultSeen = 0;
            var railSeen = 0;
            c.hangars.forEach(function (h, idx) {
                var preset = 0;
                if (Array.isArray(h.hangar.pendingDockOrders)) {
                    h.hangar.pendingDockOrders.forEach(function (o) {
                        if (parseInt(o.flightId, 10) === parseInt(flight.id, 10)) {
                            preset += parseInt(o.count || 0, 10);
                        }
                    });
                }
                var hangarName;
                if (h.hangar.isCatapult || h.hangar.name === 'catapult') {
                    catapultSeen++;
                    hangarName = (catapultTotal > 1) ? ('Catapult ' + catapultSeen) : 'Catapult';
                } else if (h.hangar.isRail || h.hangar.name === 'fighterRail') {
                    railSeen++;
                    hangarName = (railTotal > 1) ? ('Fighter Rail ' + railSeen) : 'Fighter Rail';
                } else {
                    var prefix = locationPrefixFor(h.hangar.location);
                    prefixSeen[prefix] = (prefixSeen[prefix] || 0) + 1;
                    hangarName = prefixCounts[prefix] > 1
                        ? (prefix + ' Hangar ' + prefixSeen[prefix])
                        : (prefix + ' Hangar');
                }
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

        //$('<div class="multi-value-row"><span class="multi-value-label" style="font-style:normal;">Allocate craft to dock per hangar.</span></div>').appendTo(container);

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
            //Stage 10.2: project the new dock allocation into every hangar
            //tooltip so the player sees a "(Recovering)" line for the
            //incoming craft and the projected "Carrying" total updates
            //immediately rather than waiting for the next gamedata reload.
            if (typeof window.refreshFiringHangarTooltips === 'function') {
                window.refreshFiringHangarTooltips();
            }
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

    // === Firing-phase carrier-side bulk recover dialog ===
    //
    // Mirror of hangarDeployDock but for Firing Phase: open from the CARRIER's
    // tooltip via the recoverFlights button. Lists every same-hex eligible
    // friendly flight with a checkbox + hangar dropdown so the player can
    // fill the carrier's hangars without clicking each flight individually.
    //
    // State model uses the existing firing-phase pendingDockOrders pipeline
    // (same as the per-flight Dock dialog), so the per-flight and bulk paths
    // coexist transparently — the server's processDockOrders walks every
    // pendingDockOrder regardless of which UI added it.
    //
    // Differences vs hangarDeployDock:
    //   - same-hex/heading/speed checks via findEligibleFlightsForDocking
    //   - full-flight only (active-craft count); per-craft split stays on the
    //     per-flight Dock dialog
    //   - capacity readout subtracts queued docks from OTHER flights AND
    //     queued launches on the same hangar (shared output budget)
    //   - re-edit: a checked flight whose prior orders were split across
    //     hangars (e.g. the per-flight Dock dialog set a 3+3 split) gets
    //     consolidated to one full-flight order on the chosen hangar;
    //     unchecking only clears THIS carrier's queue (cross-carrier orders
    //     are left alone — the player can manage those from the other
    //     carrier's tooltip).
    hangarRecover: function hangarRecover(carrier) {
        if (!carrier) return;
        if (typeof window.findEligibleFlightsForDocking !== 'function') return;

        var eligibleEntries = window.findEligibleFlightsForDocking(carrier);

        // Stage 16: a Catapult (name "catapult") is a dock-capable hangar that
        // holds exactly ONE fighter regardless of box count / damage and has no
        // launch+land output budget.
        var isDockHangar = function (sys) { return !!(sys && (sys.name === 'hangar' || sys.name === 'catapult' || sys.name === 'fighterRail')); };
        var effectiveHangarBoxes = function (h) {
            if (!h) return 0;
            if (h.isCatapult || h.name === 'catapult') return 1;
            var nd = 0;
            if (Array.isArray(h.damage)) h.damage.forEach(function (d) { nd += Math.max(0, parseInt(d.damage || 0, 10) - parseInt(d.armour || 0, 10)); });
            return Math.max(0, parseInt(h.maxhealth, 10) - nd);
        };
        var isCatapultSys = function (sys) { return !!(sys && (sys.isCatapult || sys.name === 'catapult')); };
        // Box-cost helpers: a unitSize<1 craft (Vorlon Assault Fighter et al.)
        // occupies >1 box each; a unitSize>1 ultralight (Zorth) packs several per
        // box (fractional 0.5 box/craft); catapults are single-fighter rails (1:1).
        // Mirrors HangarOps::boxesPerCraftForClass / boxesPerCraftForEntry (PHP).
        var boxesPerCraftFromUnitSize = function (u) { u = (u != null) ? parseFloat(u) : 1; if (u > 0 && u < 1) return Math.ceil(1 / u); if (u > 1) return 1 / u; return 1; };
        var boxesPerCraftForEntry = function (e) { if (e && e.boxesPerCraft) { var b = parseFloat(e.boxesPerCraft); return b > 0 ? b : 1; } return boxesPerCraftFromUnitSize(e ? e.unitSize : 1); };
        var entryBoxesIn = function (sys, e) { var n = parseInt(e.flightSize || 1, 10); return isCatapultSys(sys) ? n : n * boxesPerCraftForEntry(e); };
        var craftBoxesIn = function (sys, count, unitSize) { var n = parseInt(count || 0, 10); return isCatapultSys(sys) ? n : n * boxesPerCraftFromUnitSize(unitSize); };

        // Exclude flights queued to a DIFFERENT carrier this turn — the player
        // can re-route via the per-flight "Enter Hangar" dialog (which shows
        // every eligible carrier in one place). Letting the carrier-side bulk
        // dialog silently override a cross-carrier order would surprise users:
        // the server would dock the flight wherever resolves first and drop
        // the other with a "flight already removed" fail note.
        var queuedOnOtherCarrier = new Set();
        for (var skey in gamedata.ships) {
            var s = gamedata.ships[skey];
            if (!s || s.id === carrier.id) continue;
            if (!Array.isArray(s.systems)) continue;
            s.systems.forEach(function (sys) {
                if (!sys || (sys.name !== 'hangar' && sys.name !== 'catapult' && sys.name !== 'fighterRail')) return;
                if (!Array.isArray(sys.pendingDockOrders)) return;
                sys.pendingDockOrders.forEach(function (o) {
                    if (parseInt(o.count || 0, 10) > 0) queuedOnOtherCarrier.add(parseInt(o.flightId, 10));
                });
            });
        }
        eligibleEntries = eligibleEntries.filter(function (e) {
            return !queuedOnOtherCarrier.has(parseInt(e.flight.id, 10));
        });

        // Pre-check anything currently queued on THIS carrier (re-edit case).
        // Build a map: flightId → existing {hangar, count} that the dialog
        // can use to default the checkbox + dropdown selection.
        var queuedByFlight = new Map();
        if (Array.isArray(carrier.systems)) {
            carrier.systems.forEach(function (sys) {
                if (!sys || (sys.name !== 'hangar' && sys.name !== 'catapult' && sys.name !== 'fighterRail')) return;
                if (!Array.isArray(sys.pendingDockOrders)) return;
                sys.pendingDockOrders.forEach(function (o) {
                    var fid = parseInt(o.flightId, 10);
                    var c   = parseInt(o.count || 0, 10);
                    if (c <= 0) return;
                    var prior = queuedByFlight.get(fid);
                    // Prefer the hangar with the largest allocation so the
                    // dropdown defaults to the player's clear primary choice
                    // when an existing split exists. bayCount tracks how many
                    // distinct bays hold the flight — >1 means it was auto-
                    // distributed and must re-edit as an auto-distribute row.
                    if (!prior) {
                        queuedByFlight.set(fid, { hangar: sys, count: c, bayCount: 1 });
                    } else {
                        prior.bayCount += 1;
                        if (c > prior.count) { prior.hangar = sys; prior.count = c; }
                    }
                });
            });
        }

        // Include any pre-queued flight that no longer meets the same-hex
        // eligibility (carrier or flight moved this turn after queuing) so
        // the player can still uncheck/cancel from this dialog. Walking
        // pendingDockOrders on THIS carrier covers that.
        var eligibleIds = new Set(eligibleEntries.map(function (e) { return parseInt(e.flight.id, 10); }));
        queuedByFlight.forEach(function (_v, fid) {
            if (eligibleIds.has(fid)) return;
            if (queuedOnOtherCarrier.has(fid)) return;        //don't reintroduce the cross-carrier case
            var f = gamedata.getShip(fid);
            if (!f) return;
            //Stale-eligibility flight gets a row but only its queued hangar
            //is offered — re-checking it without a valid same-hex match
            //would just be dropped server-side.
            eligibleEntries.push({ flight: f, hangars: [{ hangar: _v.hangar, capacity: parseInt(f.flightSize || 1, 10) }], stale: true });
        });

        var e = $('<div class="confirm error multi-value-confirm hangar-confirm hangarRecover"><div class="ui"><div class="confirmok"></div><div class="confirmcancel"></div></div></div>');
        $('<div class="multi-value-header">Recover flights into ' + carrier.name + '</div>').prependTo(e);
        var container = $('<div class="multi-value-container"></div>').insertAfter(e.find('.multi-value-header'));

        if (eligibleEntries.length === 0) {
            // No fighter flights — but the carrier may still have eligible LCVs.
            var lcvSectionOnly = window.confirm.appendLcvRecoverSection(container, carrier);
            if (lcvSectionOnly.count === 0) {
                $('<div class="multi-value-row"><span class="multi-value-label" style="font-style:normal;">No friendly flights in this hex are eligible for recovery.</span></div>').appendTo(container);
            }
            //OK commits the LCV selections (no fighter allocation in this branch).
            $(".confirmok", e).on("click", function () {
                if (lcvSectionOnly.hasOverflow && lcvSectionOnly.hasOverflow()) {
                    alert('More LCVs selected than free rails. Uncheck some and try again.');
                    return;
                }
                lcvSectionOnly.commit();
                e.remove();
            });
            $(".confirmcancel", e).on("click", function () { e.remove(); });
            e.appendTo("body").fadeIn(250);
            return;
        }

        $('<div class="multi-value-row"><span class="multi-value-label" style="font-style:normal;">Check flights to dock into a hangar bay at end of turn.</span></div>').appendTo(container);

        // Live per-hangar capacity readout. Mirrors hangarDeployDock's pill
        // strip; baseFree counts OTHER flights' queued orders as committed and
        // treats THIS dialog's rows as the editable allocation. Launch orders
        // queued elsewhere on the same hangar consume the shared budget.
        var $capacityHeader = $('<div class="multi-value-row hangarCapacityHeader" style="font-style:normal;"></div>');
        container.append($capacityHeader);

        var rowFlightIds = new Set(eligibleEntries.map(function (e) { return parseInt(e.flight.id, 10); }));
        // baseFreeByHangar tracks the *physical box* free count usable by THIS
        // dialog. baseBudgetByHangar tracks the shared launch+land budget.
        // Both treat rows IN the dialog as reclaimable, rows NOT in the
        // dialog as committed.
        var baseFreeByHangar = new Map();
        var baseBudgetByHangar = new Map();
        carrier.systems.forEach(function (sys) {
            if (!sys || !isDockHangar(sys)) return;
            var isCat = isCatapultSys(sys);
            if (!isCat && shipManager.systems.isDestroyed(carrier, sys)) return;

            var effective = effectiveHangarBoxes(sys);   //catapult → 1, regardless of damage
            //committed is in BOXES (fractional for ultralights); round the TOTAL up
            //to whole boxes so free space is whole boxes (mirrors occupiedBoxes).
            var committed = 0;
            if (Array.isArray(sys.hangarUsage)) {
                sys.hangarUsage.forEach(function (entry) { committed += entryBoxesIn(sys, entry); });
            }
            if (Array.isArray(sys.pendingDockOrders)) {
                sys.pendingDockOrders.forEach(function (o) {
                    var fid = parseInt(o.flightId, 10);
                    if (rowFlightIds.has(fid)) return;        //in dialog → reclaimable
                    var of = gamedata.getShip(fid);
                    committed += craftBoxesIn(sys, o.count, of ? of.unitSize : 1);
                });
            }
            var freeBoxes = Math.max(0, effective - Math.ceil(committed));
            baseFreeByHangar.set(sys.id, freeBoxes);

            //Catapults have no launch+land output budget — set the budget to the
            //free-box count so capacity = min(free, budget) = free.
            if (isCat) {
                baseBudgetByHangar.set(sys.id, freeBoxes);
                return;
            }
            var output = parseInt(sys.output || 0, 10);
            var spent  = parseInt(sys.launchedThisTurn || 0, 10) + parseInt(sys.landedThisTurn || 0, 10);
            var budgetUsed = spent;
            if (Array.isArray(sys.pendingDockOrders)) {
                sys.pendingDockOrders.forEach(function (o) {
                    var fid = parseInt(o.flightId, 10);
                    if (rowFlightIds.has(fid)) return;        //reclaimable
                    budgetUsed += parseInt(o.count || 0, 10);
                });
            }
            if (Array.isArray(sys.pendingLaunchOrders)) {
                sys.pendingLaunchOrders.forEach(function (o) {
                    budgetUsed += parseInt(o.size || 0, 10);
                });
            }
            baseBudgetByHangar.set(sys.id, Math.max(0, output - budgetUsed));
        });

        var rowData = [];
        eligibleEntries.forEach(function (entry) {
            var flight = entry.flight;
            var hangars = entry.hangars;
            var preExisting = queuedByFlight.get(parseInt(flight.id, 10));

            var active = countActiveCraftInFlightLocal(flight);
            if (active <= 0) return;

            // A flight already queued across >1 bay was auto-distributed (it's
            // bigger than any single rail) and must re-edit as an auto-distribute
            // row — never collapse back onto one recorded bay (which would assign
            // the whole flight to a 6-box rail and trip the overflow guard).
            var wasAutoDistributed = !!(preExisting && preExisting.bayCount > 1);

            // No SINGLE bay holds the whole flight, but the carrier's rails/bays
            // together do (entry.combinedFit) — offer an auto-distribute row that
            // greedily spreads the flight across bays on OK (matches the
            // Deployment-Phase "auto-distribute only" behaviour for rails).
            var autoDistribute = false;
            if (wasAutoDistributed || hangars.length === 0) {
                if (!wasAutoDistributed && !entry.combinedFit) return;
                var plan0 = distributeFlightAcrossBays(flight, active);
                if (plan0.length === 0) return;       //combined capacity gone since open — skip
                autoDistribute = true;
            } else if (preExisting && preExisting.hangar) {
                // Single-bay re-edit: ensure the previously-queued hangar appears
                // as an option even if eligibility recomputed without it (e.g.
                // stale-eligibility flight injected above).
                var alreadyListed = hangars.some(function (h) { return h.hangar === preExisting.hangar; });
                if (!alreadyListed) {
                    hangars = hangars.slice();
                    hangars.unshift({ hangar: preExisting.hangar, capacity: parseInt(flight.flightSize || 1, 10) });
                }
            }

            var label = flight.name + ' (' + active + ' x ' + flight.shipClass + ')';
            if (entry.stale) label += ' — queued (no longer in hex)';

            var row = $('<div class="multi-value-row"></div>');
            var $check = $('<input type="checkbox" class="deployDockCheck">');
            if (preExisting) $check.prop('checked', true);
            var $labelSpan = $('<span class="multi-value-label"><span class="hangar-craft-name"></span></span>');
            $labelSpan.find('.hangar-craft-name').text(label);
            $check.appendTo(row);
            $labelSpan.appendTo(row);

            var $hangarPick;
            if (autoDistribute) {
                row.append($('<span class="multi-value-max"> → across rails/bays</span>'));
                $hangarPick = null;
                row.data('autoDistribute', true);
            } else if (hangars.length === 1) {
                var only = hangars[0];
                var hangarName = hangarLabelFor(carrier, only.hangar);
                row.append($('<span class="multi-value-max"> → ' + hangarName + '</span>'));
                $hangarPick = null;
                row.data('chosenHangar', only.hangar);
            } else {
                $hangarPick = $('<select class="multi-value-input deployDockHangar"></select>');
                hangars.forEach(function (h, i) {
                    var opt = $('<option></option>').attr('value', i).text(hangarLabelFor(carrier, h.hangar));
                    if (preExisting && preExisting.hangar === h.hangar) opt.prop('selected', true);
                    $hangarPick.append(opt);
                });
                row.append($('<span class="multi-value-max"> → </span>'));
                row.append($hangarPick);
            }

            row.data('flight', flight);
            row.data('eligibleHangars', hangars);
            row.data('flightSize', active);
            $check.on('change', recomputeCapacity);
            if ($hangarPick) $hangarPick.on('change', recomputeCapacity);
            container.append(row);
            rowData.push(row);
        });

        if (rowData.length === 0) {
            e.remove();
            return;
        }

        recomputeCapacity();

        //Combined view: append any eligible LCVs below the fighter rows so a
        //carrier with both shows them in one dialog. Deferred commit — the OK
        //handler below calls lcvSection.commit() after the fighter allocation.
        var lcvSection = window.confirm.appendLcvRecoverSection(container, carrier);

        $(".confirmok", e).on("click", function () {
            // Reject if any hangar would overflow free boxes OR the shared
            // launch+land budget. Physical capacity is measured in BOXES
            // (unitSize<1 craft cost >1 box each); the launch+land budget is in
            // CRAFT. Mirrors hangarDeployDock's guard. Each checked row resolves
            // to a per-bay plan: a single chosen hangar for ordinary flights, a
            // greedy multi-bay split for auto-distribute rows (rails).
            var perBoxes = new Map();
            var perCraft = new Map();
            rowData.forEach(function ($row) {
                if (!$row.find('.deployDockCheck').is(':checked')) return;
                var f = $row.data('flight');
                planForRow($row).forEach(function (slot) {
                    perCraft.set(slot.hangar.id, (perCraft.get(slot.hangar.id) || 0) + slot.count);
                    perBoxes.set(slot.hangar.id, (perBoxes.get(slot.hangar.id) || 0) + craftBoxesIn(slot.hangar, slot.count, f ? f.unitSize : 1));
                });
            });
            var overflow = [];
            perBoxes.forEach(function (boxesUsed, hangarId) {
                var avail = baseFreeByHangar.get(hangarId) || 0;
                var budget = baseBudgetByHangar.get(hangarId) || 0;
                var craftUsed = perCraft.get(hangarId) || 0;
                if (boxesUsed > avail || craftUsed > budget) {
                    overflow.push(hangarLabelByIdFor(carrier, hangarId) + ' (' + boxesUsed + '/' + avail + ' boxes)');
                }
            });
            if (overflow.length > 0) {
                alert('Cannot recover: capacity or launch+land budget exceeded in ' + overflow.join(', ') + '. Uncheck flights or pick a different hangar.');
                return;
            }

            //Stage 10.6.2: aggregate customFighter cap across checked flights.
            //Per-row eligibility already requires each flight's full count to
            //fit individually, but multiple same-named flights can still
            //collectively exceed the carrier's cap.
            var customOverflow = checkCustomFighterAggregate(carrier, rowData);
            if (customOverflow.length > 0) {
                alert('Cannot recover: customFighter cap exceeded — ' + customOverflow.join(', ') + '. Uncheck flights.');
                return;
            }

            //LCV section: bail before any write if more LCVs are checked than free rails.
            if (lcvSection.hasOverflow && lcvSection.hasOverflow()) {
                alert('More LCVs selected than free LCV rails. Uncheck some and try again.');
                return;
            }

            rowData.forEach(function ($row) {
                var flight = $row.data('flight');
                if (!flight) return;
                if (!$row.find('.deployDockCheck').is(':checked')) {
                    //Unchecked: strip THIS carrier's queued orders for this flight.
                    //Cross-carrier orders (player queued elsewhere) survive — they
                    //get amended from that carrier's tooltip or the per-flight Dock.
                    stripFlightFromCarrier(carrier, flight);
                    return;
                }
                var plan = planForRow($row);
                if (plan.length === 0) return;
                //Atomic rewrite on THIS carrier — strip any pre-existing split for
                //the flight, then write the new per-bay order(s). A single-bay
                //dock writes one order; an auto-distribute row writes one per bay.
                stripFlightFromCarrier(carrier, flight);
                plan.forEach(function (slot) {
                    if (slot.count <= 0) return;
                    if (!Array.isArray(slot.hangar.pendingDockOrders)) slot.hangar.pendingDockOrders = [];
                    slot.hangar.pendingDockOrders.push({ flightId: parseInt(flight.id, 10), count: slot.count });
                    slot.hangar.pendingDockOrdersDirty = true;
                });
            });

            //Commit the LCV section's selections (deferred until OK, same as the
            //fighter rows). Done after the fighter allocation so both apply together.
            if (lcvSection.commit) lcvSection.commit();

            //Stage 10.2: project bulk-recover orders into every hangar tooltip
            //so the carrier's hangar systemInfo shows "(Recovering)" lines and
            //the projected "Carrying" total immediately on dialog close.
            if (typeof window.refreshFiringHangarTooltips === 'function') {
                window.refreshFiringHangarTooltips();
            }

            e.remove();
        });
        $(".confirmcancel", e).on("click", function () { e.remove(); });

        e.appendTo("body").fadeIn(250);

        function computePerHangarUsage() {
            var per = new Map();
            rowData.forEach(function ($row) {
                if (!$row.find('.deployDockCheck').is(':checked')) return;
                planForRow($row).forEach(function (slot) {
                    per.set(slot.hangar.id, (per.get(slot.hangar.id) || 0) + slot.count);
                });
            });
            return per;
        }

        // Resolve a checked row to its per-bay [{hangar, count}] plan. Ordinary
        // rows put the whole flight in the chosen/dropdown hangar; an auto-
        // distribute row (flight bigger than any single bay) greedily spreads it
        // across the carrier's bays. Returns [] if nothing is allocable.
        function planForRow($row) {
            var count = parseInt($row.data('flightSize') || 0, 10);
            if (count <= 0) return [];
            if ($row.data('autoDistribute')) {
                return distributeFlightAcrossBays($row.data('flight'), count);
            }
            var hangar = $row.data('chosenHangar');
            if (!hangar) {
                var idx = parseInt($row.find('.deployDockHangar').val() || 0, 10);
                var eligible = $row.data('eligibleHangars');
                if (!eligible || !eligible[idx]) return [];
                hangar = eligible[idx].hangar;
            }
            return [{ hangar: hangar, count: count }];
        }

        // Greedily spread $count craft of $flight across the carrier's bays
        // (biggest free first), using the dialog's already-computed free-box and
        // launch+land budget maps. Capacity is in BOXES (unitSize<1 craft cost
        // >1 box each); the launch+land budget is in CRAFT. Returns
        // [{hangar, count}] or [] if combined capacity can't hold the flight.
        function distributeFlightAcrossBays(flight, count) {
            var u = flight ? parseFloat(flight.unitSize) : 1;
            //per-craft boxes: >1 superheavy, fractional 0.5 ultralight, else 1.
            var perCraftBoxes = (u > 0 && u < 1) ? Math.ceil(1 / u) : (u > 1 ? 1 / u : 1);
            var bays = [];
            carrier.systems.forEach(function (sys) {
                if (!sys || !isDockHangar(sys)) return;
                if (!baseFreeByHangar.has(sys.id)) return;
                var freeBoxes = baseFreeByHangar.get(sys.id);
                var budget    = baseBudgetByHangar.get(sys.id);
                var isCat = isCatapultSys(sys);
                //Craft this bay can take: catapult counts 1:1, else floor(boxes /
                //per-craft) capped by the launch+land budget.
                var craftFit = isCat ? Math.min(freeBoxes, budget)
                                     : Math.min(Math.floor(freeBoxes / perCraftBoxes), budget);
                if (craftFit > 0) bays.push({ hangar: sys, fit: craftFit, free: freeBoxes });
            });
            bays.sort(function (a, b) { return b.free - a.free; });   //biggest free first

            var remaining = count;
            var plan = [];
            for (var i = 0; i < bays.length && remaining > 0; i++) {
                var take = Math.min(remaining, bays[i].fit);
                if (take <= 0) continue;
                plan.push({ hangar: bays[i].hangar, count: take });
                remaining -= take;
            }
            return (remaining > 0) ? [] : plan;
        }

        //Stage 10.6.2: aggregate customFighter demand across the checked rows.
        //Pending dock orders that the dialog is about to REWRITE (rows about
        //to be re-queued) are treated as reclaimable — they get stripped via
        //stripFlightFromCarrier before the new push. Other pending orders on
        //THIS carrier (not in the dialog) stay committed.
        function checkCustomFighterAggregate(carrier, rows) {
            var demand = new Map();
            var inDialog = new Set();
            rows.forEach(function ($row) {
                var f = $row.data('flight');
                if (f) inDialog.add(parseInt(f.id, 10));
                if (!$row.find('.deployDockCheck').is(':checked')) return;
                if (!f) return;
                var name = String(f.customFtrName || '');
                if (!name) return;
                var count = parseInt($row.data('flightSize') || 0, 10);
                demand.set(name, (demand.get(name) || 0) + count);
            });
            if (demand.size === 0) return [];

            var overflows = [];
            demand.forEach(function (count, name) {
                var declared = (carrier.customFighter && carrier.customFighter[name])
                    ? parseInt(carrier.customFighter[name], 10) : 0;
                var committed = 0;
                carrier.systems.forEach(function (sys) {
                    if (!sys || (sys.name !== 'hangar' && sys.name !== 'catapult' && sys.name !== 'fighterRail')) return;
                    if (Array.isArray(sys.hangarUsage)) {
                        sys.hangarUsage.forEach(function (entry) {
                            if (entry.customFtrName !== name) return;
                            committed += parseInt(entry.flightSize || 1, 10);
                        });
                    }
                    if (Array.isArray(sys.pendingDockOrders)) {
                        sys.pendingDockOrders.forEach(function (o) {
                            if (inDialog.has(parseInt(o.flightId, 10))) return;     //reclaimable
                            var f2 = gamedata.getShip(o.flightId);
                            if (!f2 || String(f2.customFtrName || '') !== name) return;
                            committed += parseInt(o.count || 0, 10);
                        });
                    }
                });
                if (committed + count > declared) {
                    overflows.push(name + ' ' + (committed + count) + '/' + declared);
                }
            });
            return overflows;
        }

        function recomputeCapacity() {
            var per = computePerHangarUsage();
            var anyOverflow = false;
            var $pillContainer = $('<span class="hangar-capacity-pills"></span>');
            carrier.systems.forEach(function (sys) {
                if (!sys || (sys.name !== 'hangar' && sys.name !== 'catapult' && sys.name !== 'fighterRail')) return;
                if (!baseFreeByHangar.has(sys.id)) return;
                var avail = baseFreeByHangar.get(sys.id);
                var budget = baseBudgetByHangar.get(sys.id);
                var cap   = Math.min(avail, budget);
                var used  = per.get(sys.id) || 0;
                var color = (used > cap) ? '#ff5050' : (used > 0 ? '#ffff80' : '#bdbdbd');
                if (used > cap) anyOverflow = true;
                $('<span class="hangar-capacity-pill" style="color:' + color + ';"></span>')
                    .text(hangarLabelFor(carrier, sys) + ': ' + used + '/' + cap)
                    .appendTo($pillContainer);
            });
            if ($pillContainer.children().length === 0) {
                $pillContainer.append('<span style="color:#bdbdbd;">none</span>');
            }
            $capacityHeader.empty()
                .append('<span class="hangar-capacity-label">Hangar capacity:</span>')
                .append($pillContainer);
            $('.confirmok', e).css('opacity', anyOverflow ? 0.6 : 1);
        }

        // Remove all queued dock-order entries for $flight from every hangar
        // on $carrier. Mirrors the per-flight Dock dialog's
        // replaceDockOrdersForFlight but scoped to a single carrier so we
        // don't disturb queues a player may have set up on OTHER carriers.
        function stripFlightFromCarrier(carrier, flight) {
            var fid = parseInt(flight.id, 10);
            if (!Array.isArray(carrier.systems)) return;
            carrier.systems.forEach(function (sys) {
                if (!sys || (sys.name !== 'hangar' && sys.name !== 'catapult' && sys.name !== 'fighterRail')) return;
                if (!Array.isArray(sys.pendingDockOrders)) return;
                var before = sys.pendingDockOrders.length;
                sys.pendingDockOrders = sys.pendingDockOrders.filter(function (o) {
                    return parseInt(o.flightId, 10) !== fid;
                });
                if (sys.pendingDockOrders.length !== before) {
                    sys.pendingDockOrdersDirty = true;
                }
            });
        }

        function countActiveCraftInFlightLocal(flight) {
            if (!Array.isArray(flight.systems)) return 0;
            var n = 0;
            flight.systems.forEach(function (ftr) {
                if (!shipManager.systems.isDestroyed(flight, ftr)) n++;
            });
            return n;
        }

        function hangarLabelFor(carrier, hangar) {
            //Stage 16: catapults are labelled "Catapult" / "Catapult N" (numbered
            //across all catapults on the carrier), independent of ship location.
            if (hangar && (hangar.isCatapult || hangar.name === 'catapult')) {
                var cats = carrier.systems.filter(function (s) { return s && (s.isCatapult || s.name === 'catapult'); });
                if (cats.length <= 1) return 'Catapult';
                return 'Catapult ' + (cats.indexOf(hangar) + 1);
            }
            //Fighter Rails are labelled "Fighter Rail" / "Fighter Rail N" (numbered
            //across all rails on the carrier), independent of ship location.
            if (hangar && (hangar.isRail || hangar.name === 'fighterRail')) {
                var rails = carrier.systems.filter(function (s) { return s && (s.isRail || s.name === 'fighterRail'); });
                if (rails.length <= 1) return 'Fighter Rail';
                return 'Fighter Rail ' + (rails.indexOf(hangar) + 1);
            }
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
                if (!s || s.name !== 'hangar') return false;     //hangars only — catapults/rails labelled separately
                var groupOf = function (l) {
                    if (l === 31 || l === 32) return 3;
                    if (l === 41 || l === 42) return 4;
                    return l;
                };
                return groupOf(parseInt(s.location, 10)) === groupOf(parseInt(hangar.location, 10));
            });
            if (siblings.length <= 1) return prefix + ' Hangar';
            var idx = siblings.indexOf(hangar);
            return prefix + ' Hangar ' + (idx + 1);
        }

        function hangarLabelByIdFor(carrier, hangarId) {
            for (var i = 0; i < carrier.systems.length; i++) {
                var sys = carrier.systems[i];
                if (sys && (sys.name === 'hangar' || sys.name === 'catapult' || sys.name === 'fighterRail') && sys.id === hangarId) return hangarLabelFor(carrier, sys);
            }
            return 'Hangar';
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
        $('<div class="multi-value-row"><span class="multi-value-label" style="font-style:normal;">Pick which carrier the flight should dock into.</span></div>').appendTo(container);

        carriers.forEach(function (entry) {
            var carrier = entry.ship;
            //Sum free boxes across the carrier's eligible hangars for the readout.
            var totalCapacity = 0;
            entry.hangars.forEach(function (h) { totalCapacity += parseInt(h.capacity || 0, 10); });
            //Boxes this flight needs: unitSize<1 craft cost >1 box each, unitSize>1
            //ultralights a fractional box each; round the total need UP to whole boxes.
            var size = parseInt(flight.flightSize || 1, 10);
            var fu = parseFloat(flight.unitSize);
            var bpc = (fu > 0 && fu < 1) ? Math.ceil(1 / fu) : (fu > 1 ? 1 / fu : 1);
            var boxesNeeded = Math.ceil(size * bpc);

            var row = $('<div class="multi-value-row"></div>');
            var btn = $('<div class="name-value-button-ally" style="flex:1;">DOCK IN ' + carrier.name.toUpperCase() + ' (' + boxesNeeded + '/' + totalCapacity + ' boxes)</div>');
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

    // LCV Rails: per-rail picker for deploy-docking an LCV. Lists every FREE rail
    // across the eligible carrier(s) sharing the clicked hex as a radio row
    // ("Carrier — LCV Rail N"), so the player picks the EXACT rail (not just the
    // carrier's first free one). One LCV → one rail (mutually exclusive). Mirrors
    // the Firing-Phase per-rail lcvDock dialog. $carriers is [{ship, free}, ...].
    lcvDeployDockCarrierPicker: function lcvDeployDockCarrierPicker(lcv, carriers) {
        if (!lcv || !Array.isArray(carriers) || carriers.length === 0) return;
        if (!window.DeploymentDock || typeof window.DeploymentDock.queueLcvDeployDock !== 'function') return;
        if (typeof window.DeploymentDock.freeLcvDeployRails !== 'function') return;
        if (typeof window.isLCVRailSystem !== 'function') return;

        // Flatten to one choice per free rail across all eligible carriers.
        var choices = [];   // { carrier, rail }
        carriers.forEach(function (entry) {
            var carrier = entry.ship;
            window.DeploymentDock.freeLcvDeployRails(carrier, lcv.id).forEach(function (rail) {
                choices.push({ carrier: carrier, rail: rail });
            });
        });
        if (choices.length === 0) return;

        var e = $('<div class="confirm error multi-value-confirm hangar-confirm hangarDeployCarrierPicker"><div class="ui"><div class="confirmok"></div><div class="confirmcancel"></div></div></div>');
        $('<div class="multi-value-header">Dock ' + lcv.name + ' — choose LCV rail</div>').prependTo(e);
        var container = $('<div class="multi-value-container"></div>').insertAfter(e.find('.multi-value-header'));
        //$('<div class="multi-value-row"><span class="multi-value-label" style="font-style:normal;">Pick which LCV rail to dock onto.</span></div>').appendTo(container);

        var rowChecks = [];   // { $chk, choice }
        choices.forEach(function (choice, idx) {
            //"Carrier — LCV Rail <Location> N" (window.lcvRailLabel — shared label).
            var railName = window.lcvRailLabel(choice.carrier, choice.rail);
            var labelText = choice.carrier.name + ' — ' + railName;
            var row = $('<div class="multi-value-row" style="justify-content:center; gap:8px;"></div>');
            var $chk = $('<input type="checkbox" class="lcvDeployDockCheck">');
            if (idx === 0) $chk.prop('checked', true);   //default to the first rail
            var $labelSpan = $('<span class="multi-value-label" style="flex:0 0 auto; text-align:left; margin:0;"><span class="hangar-craft-name"></span></span>');
            $labelSpan.find('.hangar-craft-name').text(labelText);
            $chk.appendTo(row);
            $labelSpan.appendTo(row);
            container.append(row);
            rowChecks.push({ $chk: $chk, choice: choice });
            // Mutually exclusive: checking one rail unchecks the rest.
            $chk.on('change', function () {
                if ($chk.is(':checked')) {
                    rowChecks.forEach(function (rc) { if (rc.$chk !== $chk) rc.$chk.prop('checked', false); });
                }
            });
        });

        $(".confirmok", e).on("click", function () {
            var picked = rowChecks.filter(function (rc) { return rc.$chk.is(':checked'); })[0];
            if (picked && window.DeploymentDock.queueLcvDeployDock(picked.choice.carrier, lcv, picked.choice.rail)) {
                e.remove();
                if (typeof window.refreshDeploymentUIForDeployStart === 'function') {
                    window.refreshDeploymentUIForDeployStart();
                }
                if (typeof window.selectShipInDeploymentPhase === 'function') {
                    window.selectShipInDeploymentPhase(picked.choice.carrier);
                }
            } else {
                e.remove();
            }
        });
        $(".confirmcancel", e).on("click", function () { e.remove(); });
        e.appendTo("body").fadeIn(250);
    },

    hangarDeployDock: function hangarDeployDock(carrier) {
        if (!carrier) return;
        if (!window.DeploymentDock || typeof window.DeploymentDock.findPendingFlightsForCarrier !== 'function') return;
        if (typeof window.DeploymentDock.eligibleHangarsForFlight !== 'function') return;

        // Stage 16: a Catapult (name "catapult") is a dock-capable hangar that
        // holds exactly ONE fighter regardless of box count / damage.
        var isDockHangar = function (sys) { return !!(sys && (sys.name === 'hangar' || sys.name === 'catapult' || sys.name === 'fighterRail')); };
        var effectiveHangarBoxes = function (h) {
            if (!h) return 0;
            if (h.isCatapult || h.name === 'catapult') return 1;
            var nd = 0;
            if (Array.isArray(h.damage)) h.damage.forEach(function (d) { nd += Math.max(0, parseInt(d.damage || 0, 10) - parseInt(d.armour || 0, 10)); });
            return Math.max(0, parseInt(h.maxhealth, 10) - nd);
        };
        // Box-cost helpers: unitSize<1 craft occupy >1 box each; unitSize>1
        // ultralights pack several per box (fractional 0.5 box/craft); catapults are
        // single-fighter rails (1:1). Mirrors HangarOps::boxesPerCraftForClass.
        var isCatapultSys = function (sys) { return !!(sys && (sys.isCatapult || sys.name === 'catapult')); };
        var boxesPerCraftFromUnitSize = function (u) { u = (u != null) ? parseFloat(u) : 1; if (u > 0 && u < 1) return Math.ceil(1 / u); if (u > 1) return 1 / u; return 1; };
        var boxesPerCraftForEntry = function (en) { if (en && en.boxesPerCraft) { var b = parseFloat(en.boxesPerCraft); return b > 0 ? b : 1; } return boxesPerCraftFromUnitSize(en ? en.unitSize : 1); };
        var entryBoxesIn = function (sys, en) { var n = parseInt(en.flightSize || 1, 10); return isCatapultSys(sys) ? n : n * boxesPerCraftForEntry(en); };
        var craftBoxesIn = function (sys, count, unitSize) { var n = parseInt(count || 0, 10); return isCatapultSys(sys) ? n : n * boxesPerCraftFromUnitSize(unitSize); };

        var pending = window.DeploymentDock.findPendingFlightsForCarrier(carrier);

        // Pre-check flights already queued to THIS carrier (re-edit case).
        // Build a map: flightId → existing {hangar, bayCount} so OK can detect
        // "uncheck = cancel". bayCount tracks how many distinct bays the flight
        // is queued across — a flight spread over >1 bay was auto-distributed
        // (a flight bigger than any single rail) and must re-edit as an
        // auto-distribute row, NOT collapse back onto its first recorded bay.
        var preCheckedByFlight = new Map();
        if (Array.isArray(carrier.systems)) {
            carrier.systems.forEach(function (sys) {
                if (!sys || !isDockHangar(sys)) return;
                if (!Array.isArray(sys.pendingDeployStartOrders)) return;
                sys.pendingDeployStartOrders.forEach(function (o) {
                    var fid = parseInt(o.flightId, 10);
                    var prior = preCheckedByFlight.get(fid);
                    if (prior) { prior.bayCount += 1; }
                    else { preCheckedByFlight.set(fid, { hangar: sys, bayCount: 1 }); }
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

        //LCV Rails: a deploy-docked LCV (pendingLcvDeployStartOrders) is a full
        //ship, not a flight, so it never appears in the fighter `pending` list.
        //Count its rails up-front so the empty-state guard below stays open when
        //the ONLY thing to manage is an LCV un-dock. The actual rows + commit are
        //appended after the fighter rows further down.
        var lcvDeployRailCount = 0;
        if (Array.isArray(carrier.systems) && window.isLCVRailSystem) {
            carrier.systems.forEach(function (s) {
                if (window.isLCVRailSystem(s)
                    && Array.isArray(s.pendingLcvDeployStartOrders)
                    && s.pendingLcvDeployStartOrders.length > 0) {
                    lcvDeployRailCount++;
                }
            });
        }

        if (pending.length === 0 && lcvDeployRailCount === 0) {
            $('<div class="multi-value-row"><span class="multi-value-label" style="font-style:normal;">No Hangar Operations available.</span></div>').appendTo(container);
            //Hide OK — there's nothing to commit. Cancel just closes.
            $('.confirmok', e).hide();
            $(".confirmcancel", e).on("click", function () { e.remove(); });
            e.appendTo("body").fadeIn(250);
            return;
        }

        //Only the fighter-dock instruction when there ARE fighter flights to dock
        //(an LCV-only dialog skips it — its own "LCV Rails" section header suffices).
        if (pending.length > 0) {
            $('<div class="multi-value-row"><span class="multi-value-label" style="font-style:normal;">Check flights to dock into a hangar bay instead of placing on the map.</span></div>').appendTo(container);
        }

        //Live per-hangar capacity readout. Recomputed on every checkbox/dropdown
        //change so the player sees overflow before pressing OK. Without this the
        //independent per-row eligibility checks at dialog open let the player
        //queue multiple flights that each individually fit but together exceed
        //the hangar — the server then silently dropped the overflow with a fail
        //note, leaving the player confused about why fewer fighters docked.
        var $capacityHeader = $('<div class="multi-value-row hangarCapacityHeader" style="font-style:normal;"></div>');
        if (pending.length > 0) container.append($capacityHeader);

        //Base free per hangar (treats reservations from flights NOT in the
        //dialog as committed; reservations from flights IN the dialog are
        //reclaimable since the dialog will replace them on OK).
        var rowFlightIds = new Set(pending.map(function (f) { return parseInt(f.id, 10); }));
        var baseFreeByHangar = new Map();
        carrier.systems.forEach(function (sys) {
            if (!sys || !isDockHangar(sys)) return;
            if (shipManager.systems.isDestroyed(carrier, sys)) return;
            var effective = effectiveHangarBoxes(sys);
            //committed is in BOXES (fractional for ultralights); round the TOTAL up
            //to whole boxes so free space is whole boxes (mirrors occupiedBoxes).
            var committed = 0;
            if (Array.isArray(sys.hangarUsage)) {
                sys.hangarUsage.forEach(function (entry) { committed += entryBoxesIn(sys, entry); });
            }
            if (Array.isArray(sys.pendingDeployStartOrders)) {
                sys.pendingDeployStartOrders.forEach(function (o) {
                    var fid = parseInt(o.flightId, 10);
                    if (rowFlightIds.has(fid)) return;     //in dialog → reclaimable, don't double-count
                    var f = gamedata.getShip(fid);
                    if (f) committed += craftBoxesIn(sys, f.flightSize, f.unitSize);
                });
            }
            baseFreeByHangar.set(sys.id, Math.max(0, effective - Math.ceil(committed)));
        });

        // Build rows. Track per-flight {row, flight, hangarSelect (or fixed hangar)}.
        var rowData = [];
        pending.forEach(function (flight) {
            var preExisting = preCheckedByFlight.get(parseInt(flight.id, 10));
            var eligibleHangars = window.DeploymentDock.eligibleHangarsForFlight(carrier, flight);

            // A flight already queued across >1 bay was auto-distributed (it's
            // bigger than any single rail) — it must re-edit as an auto-distribute
            // row, never collapse back onto one recorded bay (that would assign
            // the whole flight to a 6-box rail and trip the overflow guard, e.g.
            // "Fighter Rail 4 (12/6)").
            var wasAutoDistributed = !!(preExisting && preExisting.bayCount > 1);

            // No SINGLE bay holds the whole flight. If the carrier's COMBINED free
            // capacity fits it (e.g. a 9-flight across a StrikeCarrier's 6+3 rails),
            // offer an auto-distribute row — the flight greedily spreads across bays
            // on OK (no per-bay UI, per the "auto-distribute only" design call).
            var autoDistribute = false;
            if (wasAutoDistributed || eligibleHangars.length === 0) {
                //Reclaim THIS flight's own reservations so a re-edit re-plans
                //against true free capacity (not its already-queued boxes).
                var plan = window.DeploymentDock.distributeFlightAcrossHangars(carrier, flight, flight.id);
                if (plan.length === 0) return;            //even combined capacity can't hold it — skip the row
                autoDistribute = true;
            } else if (preExisting && preExisting.hangar) {
                // Single-bay re-edit: keep the queued hangar selectable even if
                // other pre-checked rows re-counted capacity below it.
                var alreadyListed = eligibleHangars.some(function (h) { return h.hangar === preExisting.hangar; });
                if (!alreadyListed) {
                    eligibleHangars.unshift({ hangar: preExisting.hangar, capacity: parseInt(flight.flightSize || 1, 10) });
                }
            }

            var size = parseInt(flight.flightSize || 1, 10);
            var label = flight.name + ' (' + size + ' x ' + flight.shipClass + ')';
            var row = $('<div class="multi-value-row"></div>');
            //margin/vertical-align handled by .deployDockCheck CSS in confirm.css
            var $check = $('<input type="checkbox" class="deployDockCheck">');
            if (preExisting) $check.prop('checked', true);
            var $labelSpan = $('<span class="multi-value-label"><span class="hangar-craft-name"></span></span>');
            $labelSpan.find('.hangar-craft-name').text(label);
            $check.appendTo(row);
            $labelSpan.appendTo(row);

            // Hangar dropdown (or single static label when only one option). An
            // auto-distribute flight shows a fixed "across bays" label and no picker.
            var $hangarPick;
            if (autoDistribute) {
                row.append($('<span class="multi-value-max"> → across rails/bays</span>'));
                $hangarPick = null;
                row.data('autoDistribute', true);
            } else if (eligibleHangars.length === 1) {
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

        //LCV Rails: fold in an un-dock section for any LCV deploy-docked onto a
        //rail this session (Issue 1 — previously the only way to release a
        //deploy-docked LCV was a page refresh). Unchecking + OK releases it.
        var lcvDeploySection = window.confirm.appendLcvDeployDockSection(container, carrier);

        if (rowData.length === 0 && lcvDeploySection.count === 0) {
            e.remove();
            return;
        }

        if (pending.length > 0) recomputeCapacity();      //seed the header with the pre-checked state on open (fighters only)

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

            //Stage 10.6.2: aggregate customFighter cap across all checked rows.
            //Per-row eligibilityHangars already clamps against the cap at open
            //time, but a player checking multiple same-named flights can still
            //collectively exceed it. Server would silently drop the overflow
            //with a fail note; warn the player explicitly instead.
            var customOverflow = checkCustomFighterAggregate(carrier, rowData);
            if (customOverflow.length > 0) {
                alert('Cannot dock: customFighter cap exceeded — ' + customOverflow.join(', ') + '. Uncheck flights.');
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
                //Clear any prior queue (different hangar or different carrier)
                //before re-queueing.
                if (flight.pendingDeployDock) {
                    window.DeploymentDock.unqueueDeployStartDock(flight);
                }
                //Auto-distribute rows (flight too big for any single bay) spread
                //across the carrier's rails/bays via DeploymentDock.queueDeployStartDock,
                //which pushes a per-bay {flightId, count} order set.
                if ($row.data('autoDistribute')) {
                    window.DeploymentDock.queueDeployStartDock(carrier, flight);
                    return;
                }
                var hangar = $row.data('chosenHangar');
                if (!hangar) {
                    var idx = parseInt($row.find('.deployDockHangar').val() || 0, 10);
                    var eligible = $row.data('eligibleHangars');
                    hangar = eligible[idx].hangar;
                }
                queueDeployStartOrder(hangar, flight, carrier);
            });

            //LCV Rails: apply any LCV un-dock selections (unchecked rails release
            //their deploy-docked LCV back onto the map). No overflow guard — each
            //rail holds at most one LCV and we only ever remove here.
            lcvDeploySection.commit();

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

        // $forDisplay: when true, INCLUDE auto-distribute rows by projecting their
        // greedy per-bay plan into the map so the capacity pills show where the
        // flight actually lands. When false (the overflow REJECTION guard), skip
        // auto-distribute rows — their plan never overflows by construction and
        // the server re-validates per-bay capacity at commit time, so counting
        // them in the single-hangar rejection would false-positive.
        function computePerHangarUsage(forDisplay) {
            var perHangar = new Map();
            rowData.forEach(function ($row) {
                if (!$row.find('.deployDockCheck').is(':checked')) return;
                var flight = $row.data('flight');
                if ($row.data('autoDistribute')) {
                    if (!forDisplay) return;
                    //Project the same greedy plan OK will commit (reclaiming this
                    //flight's own queued boxes) so the pills reflect the split.
                    var plan = window.DeploymentDock.distributeFlightAcrossHangars(carrier, flight, flight.id);
                    plan.forEach(function (slot) {
                        perHangar.set(slot.hangar.id,
                            (perHangar.get(slot.hangar.id) || 0) + craftBoxesIn(slot.hangar, slot.count, flight.unitSize));
                    });
                    return;
                }
                var hangar = $row.data('chosenHangar');
                if (!hangar) {
                    var idx = parseInt($row.find('.deployDockHangar').val() || 0, 10);
                    var eligible = $row.data('eligibleHangars');
                    if (!eligible || !eligible[idx]) return;
                    hangar = eligible[idx].hangar;
                }
                //Whole flight docks; usage is in BOXES (unitSize<1 → >1 box/craft).
                var size = parseInt(flight.flightSize || 1, 10);
                perHangar.set(hangar.id, (perHangar.get(hangar.id) || 0) + craftBoxesIn(hangar, size, flight.unitSize));
            });
            return perHangar;
        }

        //Stage 10.6.2: aggregate customFighter demand across checked rows by
        //customFtrName and compare against the carrier's per-name cap. Returns
        //a list of overflow descriptions like ["Thunderbolt 18/12"] or [].
        //Pending deploy-start orders for OTHER carriers don't affect this
        //carrier's cap; orders queued elsewhere on THIS carrier (from prior
        //unchecked rows) are treated as reclaimable (will be un-queued).
        function checkCustomFighterAggregate(carrier, rows) {
            var demand = new Map();      //customFtrName → fighters being docked here
            rows.forEach(function ($row) {
                if (!$row.find('.deployDockCheck').is(':checked')) return;
                var flight = $row.data('flight');
                var name = String(flight.customFtrName || '');
                if (!name) return;
                var size = parseInt(flight.flightSize || 1, 10);
                demand.set(name, (demand.get(name) || 0) + size);
            });
            if (demand.size === 0) return [];

            var overflows = [];
            demand.forEach(function (count, name) {
                var declared = (carrier.customFighter && carrier.customFighter[name])
                    ? parseInt(carrier.customFighter[name], 10) : 0;
                //Existing usage from committed dock entries (other turns).
                var committed = 0;
                carrier.systems.forEach(function (sys) {
                    if (!sys || !isDockHangar(sys)) return;
                    if (!Array.isArray(sys.hangarUsage)) return;
                    sys.hangarUsage.forEach(function (e) {
                        if (e.customFtrName !== name) return;
                        committed += parseInt(e.flightSize || 1, 10);
                    });
                });
                if (committed + count > declared) {
                    overflows.push(name + ' ' + (committed + count) + '/' + declared);
                }
            });
            return overflows;
        }

        function recomputeCapacity() {
            var perHangar = computePerHangarUsage(true);   //include auto-distribute split in the pills
            var anyOverflow = false;
            //Walk hangars in declared order so the readout matches the dropdown labels.
            //Build each pill as its own element so the row can flex-wrap onto
            //multiple lines when many hangars are listed — the previous single-
            //line "A: x/y &middot; B: x/y &middot; C: x/y" string couldn't break
            //and got squashed on multi-hangar carriers.
            var $pillContainer = $('<span class="hangar-capacity-pills"></span>');
            carrier.systems.forEach(function (sys) {
                if (!sys || !isDockHangar(sys)) return;
                if (!baseFreeByHangar.has(sys.id)) return;
                var avail = baseFreeByHangar.get(sys.id);
                var used = perHangar.get(sys.id) || 0;
                var color = (used > avail) ? '#ff5050' : (used > 0 ? '#ffff80' : '#bdbdbd');
                if (used > avail) anyOverflow = true;
                $('<span class="hangar-capacity-pill" style="color:' + color + ';"></span>')
                    .text(hangarLabelFor(carrier, sys) + ': ' + used + '/' + avail)
                    .appendTo($pillContainer);
            });
            if ($pillContainer.children().length === 0) {
                $pillContainer.append('<span style="color:#bdbdbd;">none</span>');
            }
            $capacityHeader.empty()
                .append('<span class="hangar-capacity-label">Hangar capacity:</span>')
                .append($pillContainer);
            //Visual cue on OK button when overflowing — leave it clickable so the
            //alert above can explain WHICH hangar is over (clearer than greying it out).
            $('.confirmok', e).css('opacity', anyOverflow ? 0.6 : 1);
        }
        $(".confirmcancel", e).on("click", function () { e.remove(); });
        e.appendTo("body").fadeIn(250);

        function hangarLabelFor(carrier, hangar) {
            //Stage 16: catapults are labelled "Catapult" / "Catapult N" (numbered
            //across all catapults on the carrier), independent of ship location.
            if (hangar && (hangar.isCatapult || hangar.name === 'catapult')) {
                var cats = carrier.systems.filter(function (s) { return s && (s.isCatapult || s.name === 'catapult'); });
                if (cats.length <= 1) return 'Catapult';
                return 'Catapult ' + (cats.indexOf(hangar) + 1);
            }
            //Fighter Rails are labelled "Fighter Rail" / "Fighter Rail N".
            if (hangar && (hangar.isRail || hangar.name === 'fighterRail')) {
                var rails = carrier.systems.filter(function (s) { return s && (s.isRail || s.name === 'fighterRail'); });
                if (rails.length <= 1) return 'Fighter Rail';
                return 'Fighter Rail ' + (rails.indexOf(hangar) + 1);
            }
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
                if (!s || s.name !== 'hangar') return false;     //hangars only — catapults labelled separately
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
                if (sys && (sys.name === 'hangar' || sys.name === 'catapult' || sys.name === 'fighterRail') && sys.id === hangarId) return hangarLabelFor(carrier, sys);
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
