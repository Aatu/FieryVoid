window.confirm = {

	whtml:'<div class="confirm"><div class="ui"><div class="confirmok"></div><div class="confirmcancel"></div></div></div>',

	showConfirmOkCancel: function(message, okcb, cancelcb){
	
		
		
		
	
	},


    getMissileOptions: function(ship){
        var returnArray = new Array();
        var numberOfLaunchers = 1;
        
        if(!ship.flight){
            // it's a normal ship
            // Yeah, we have a bombrack with missiles, but that comes fully
            // stocked. So don't pay attention to it atm.
        }else{
            // it's a fighter flight
            // all the systems are fighters of the same type.
            // also, if you buy missiles for one of them,
            // you buy the same amount for all of them.
            
            for(var i in ship.systems){
                var fighter = ship.systems[i];
                
                for(var j in fighter.systems){
                    var weapon = fighter.systems[j];
                    
                    if(weapon.missileArray != null){
                        for(var k in weapon.firingModes){
                            for(var l in weapon.missileArray){
                                var missile = weapon.missileArray[l];
                                
                                if(returnArray[weapon.firingModes[k]]){
                                    var maxAmount = returnArray[weapon.firingModes[k]][1];
                                    returnArray[weapon.firingModes[k]] = ['Type '+ missile.missileClass + ' - ' +missile.displayName, (maxAmount + weapon.maxAmount), missile.cost, numberOfLaunchers];
                                    numberOfLaunchers++;
                                }else{
                                    returnArray[weapon.firingModes[k]] = ['Type '+ missile.missileClass + ' - ' +missile.displayName, weapon.maxAmount, missile.cost, numberOfLaunchers];
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


    getLaunchersPerFighter: function(ship){
		var launchers = 0;
        for(var i in ship.systems){
            var fighter = ship.systems[i];
            
            for(var j in fighter.systems){
                var weapon = fighter.systems[j];
                if (weapon.missileArray != null){
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
    
    doOnPlusMissile: function(e){
		e.stopPropagation(); 

		var button = $(this);
		var missileType = button.data("firingMode");
		var target = $(".selectAmount." + missileType);

		var value = target.data("value");
		var maxVal = target.data("max");
		var inc = 1;

		if(value + inc <= maxVal){
			var newValue = value + inc;

			target.data("value", newValue);
			target.html(newValue);
			confirm.getTotalCost();
		}
	},

    doOnMinusMissile: function(e){
		e.stopPropagation();  

		var button = $(this);
		var missileType = button.data("firingMode");
		var target = $(".selectAmount." + missileType);

		var value = target.data("value");
		var minVal = target.data("min");
		var inc = 1;

		if(value - inc >= minVal){
			var newValue = value - inc;

			target.data("value", newValue);
			target.html(newValue);
			confirm.getTotalCost();
		}
	},



    getTotalCost: function(){ 

		var flightSize = $(".fighterAmount").html();
		if (!flightSize){
			flightSize = 1;
		}

		var fighterCost = $(".fighterAmount").data("pV");
		if (!fighterCost){
			var span = $(".totalUnitCostAmount").data("value");
			fighterCost = span;	
		}

	    var missileType = $(".selectText").data("firingMode");
		var missileAmount = $(".selectAmount." + missileType).data("value");
		var launchers = $(".selectAmount." + missileType).data("launchers");
		var missileCost = $(".selectAmount." + missileType).data("cost");

		if (!missileAmount){
			missileAmount = 0;
		}
		if (!missileCost){
			missileCost = 0;
		}
		if (!launchers){
			launchers = 0;
		}

		console.log(flightSize, fighterCost, missileAmount, launchers, missileCost);

		var totalCost = (flightSize * (fighterCost + (launchers * missileAmount * missileCost)));


		var totalCostSpan = $(".confirm .totalUnitCostAmount");
			totalCostSpan.data("value", totalCost);

			totalCostSpan.html(totalCost);

    },



    increaseFlightSize: function(e){
        e.stopPropagation();

        var flightSize = $(".fighterAmount");
        var current = flightSize.html();
        var max = $(".totalUnitCostAmount").data("maxSize");

		if (current < max){
            flightSize.html(Math.floor(current) + 3);
		}        
        else return;

        confirm.getTotalCost();


    },


    decreaseFlightSize: function(e){
        e.stopPropagation();

        var flightSize = $(".fighterAmount");
        var current = flightSize.html();
        var max = $(".totalUnitCostAmount").data("maxSize");

		if (current > 3){
            flightSize.html(Math.floor(current) - 3);
		}        
        else return;

        confirm.getTotalCost();

    },


	showShipBuy: function(ship, callback){

		var e = $(this.whtml);


		//variable flightsize
        var variableSize = confirm.getVariableSize(ship);
        var missileOptions = confirm.getMissileOptions(ship);

        if (variableSize || missileOptions || ship.superheavy){
            var totalTemplate = $(".totalUnitCost");
            var totalItem = totalTemplate.clone(true).prependTo(e);
            
            $(".totalUnitCostText", totalItem).html("Total unit cost");
            $(".totalUnitCostAmount", totalItem).html(ship.pointCost);
            $(".totalUnitCostAmount", totalItem).data("value", ship.pointCost);

            $(totalItem).show();

            $(".totalUnitCostAmount").data("value", ship.pointCost);

	        if (ship.jinkinglimit > 9){
	            $(".totalUnitCostAmount").data("maxSize", 12);
	        } else $(".totalUnitCostAmount").data("maxSize", 9);

        }



        // Do lots of stuff to account for possible buying of missiles.
        var missileOptions = confirm.getMissileOptions(ship);
        
        // If it is a fighter, put the option in this pane.
        // A ship will need some more tricks.
        if(!mathlib.arrayIsEmpty(missileOptions)){
            
            for(var i in missileOptions){
                var missileOption = missileOptions[i];
                var template = $(".missileSelectItem");
                var item = template.clone(true).prependTo(e);
                

                
                var selectAmountItem = $(".selectAmount", item);

                selectAmountItem.html("0");
                selectAmountItem.addClass(i);
                selectAmountItem.data('value', 0);
                selectAmountItem.data('min', 0);

                if (ship.superheavy){
					$(".selectText", item).html(missileOption[0] +' (maximum amount: '
					+ (missileOption[1] / 6) / (missileOption[3] / 6) +', cost: '+ missileOption[2] + ')');
					$(item).show();

	                selectAmountItem.data('max', (missileOption[1] / 6) / (missileOption[3] / 6));
	                selectAmountItem.data('cost', missileOption[2]);
	                selectAmountItem.data('launchers', confirm.getLaunchersPerFighter(ship));
	                selectAmountItem.data("firingMode", i);
            	} 
            	else {
					$(".selectText", item).html(missileOption[0] +' (maximum amount: '
					+ (missileOption[1] / 6) / (missileOption[3] / 6) +', cost: '+ missileOption[2] + ')');
					$(item).show();

	                selectAmountItem.data('max', (missileOption[1] / 6) / (missileOption[3] / 6));
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

            $(".missileSelectItem .selectButtons .plusButton", e).on("click",confirm.doOnPlusMissile);
            $(".missileSelectItem .selectButtons .minusButton", e).on("click",confirm.doOnMinusMissile);

        }




        if (variableSize){
            var template = $(".missileSelectItem");
            var item = template.clone(true).prependTo(e);
            	item.addClass("fighterSelectItem");
            
            $(".selectText", item).html("Number of fighters in this flight:");
            $(item).show();
            
            var selectAmountItem = $(".selectAmount", item);
            	selectAmountItem.removeClass("selectAmount").addClass("fighterAmount");


            selectAmountItem.html("6");
            selectAmountItem.data('pV', Math.floor(ship.pointCost/6));

            $(".fighterSelectItem .selectButtons .plusButton", e).on("click",confirm.increaseFlightSize);
            $(".fighterSelectItem .selectButtons .minusButton", e).on("click",confirm.decreaseFlightSize);
        }


        $('<label>Name your new '+ship.shipClass+':</label><input type="text" style="text-align:center" name="shipname" value="Nameless"></input><br>').prependTo(e);
                
		//$('<div class="message"><span>Name your new '+ship.shipClass+'</span></div>').prependTo(e);
		$(".confirmok", e).on("click", callback);
		$(".confirmcancel",e).on("click", function(){$(".confirm").remove();});
		$(".confirmok",e).data("shipclass", ship.phpclass);

		var a = e.appendTo("body");
		a.fadeIn(250);
	
	}, 


	getVariableSize: function(ship){
		if (ship.flight && !ship.superheavy){
			return true;
		} else return false;
	},
	
	error: function(msg, callback){
		var e = $('<div class="confirm error"><div class="ui"><div class="confirmok" style="margin:auto;"></div></div></div>');
		$('<span>'+msg+'</span>').prependTo(e);
		//$('<span>ERROR</span></br>').prependTo(e);
		//$('<div class="message"><span>Name your new '+ship.shipClass+'</span></div>').prependTo(e);
		
		
		$(".ok", e).on("click", callback);
		$(".confirmok", e).on("click", function(){$(".confirm").remove();});
		$(".confirmcancel",e).remove();
		$(".ok",e).css("left", "45%");
		var a = e.appendTo("body");
		a.fadeIn(250);
		
	},
    
    confirm: function(msg, callback){
        var e = $('<div class="confirm error"><div class="ui"><div class="confirmok"></div><div class="confirmcancel"></div></div></div>');
		//var e = $('<div class="confirm error"><div class="ui"><div class="confirmok" style="margin:auto;"></div></div></div>');
		$('<span>'+msg+'</span>').prependTo(e);
		//$('<span>ERROR</span></br>').prependTo(e);
		//$('<div class="message"><span>Name your new '+ship.shipClass+'</span></div>').prependTo(e);
		
		
		$(".ok", e).on("click", callback);
		$(".confirmok", e).on("click", function(){$(".confirm").remove();});
		$(".confirmok", e).on("click", callback);
        $(".confirmcancel",e).on("click", function(){$(".confirm").remove();});
		$(".ok",e).css("left", "45%");
		var a = e.appendTo("body");
		a.fadeIn(250);
		
	},
        
    confirmWithOptions: function(msg, trueOptionString, falseOptionString, callback){
        var e = $('<div class="confirm error"><div class="ui"><div class="confirmokoption">'+trueOptionString+'</div><div class="confirmcanceloption">'+falseOptionString+'</div></div></div>');
		$('<span>'+msg+'</span>').prependTo(e);
                // As the callback needs a parameter, you can't just bind the callback.
                // The method would then be executed as you bind the result of the callback,
                // and not the callback itself. Hence the function(){callback(true);} thingy.
		$(".ok", e).on("click", function(){callback(true);});
		
                $(".confirmokoption", e).on("click", function(){$(".confirm").remove();});
		$(".confirmokoption", e).on("click", function(){callback(true);});
                $(".confirmcanceloption",e).on("click", function(){$(".confirm").remove();});
		$(".confirmcanceloption", e).on("click", function(){callback(false);});
		$(".ok",e).css("left", "45%");
		var a = e.appendTo("body");
		a.fadeIn(250);
		
	},

confirmOrSurrender: function(msg, callbackCommit, callbackSurrender){
        var e = $('<div class="confirm error"><div class="ui"><div class="confirmok"></div><div class="surrender"></div><div class="confirmcancel"></div></div></div>');
		$('<span>'+msg+'</span>').prependTo(e);
		
		$(".ok", e).on("click", callbackCommit);
		$(".confirmok", e).on("click", function(){$(".confirm").remove();});
		$(".confirmok", e).on("click", callbackCommit);
                $(".confirmcancel",e).on("click", function(){$(".confirm").remove();});
                $(".surrender",e).on("click", function(){$(".confirm").remove();});
		$(".surrender", e).on("click", callbackSurrender);
		$(".ok",e).css("left", "45%");
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

    exception: function(data){
		var e = $('<div style="z-index:999999"class="confirm error"></div>');
        console.dir(data);
        $('<h2>SERVER ERROR</h2>').appendTo(e);
        if (data.code)
            $('<div><span>Error code: '+data.code+'</span></div>').appendTo(e);
        
        if (data.logid)
            $('<div><span>Log id: '+data.logid+'</span></div>').appendTo(e);
        
        $('<div style="margin-top:20px;"><span>'+data.error+'</span></div>').appendTo(e);
        
		
		//$('<span>ERROR</span></br>').prependTo(e);
		//$('<div class="message"><span>Name your new '+ship.shipClass+'</span></div>').prependTo(e);
		
        var w = window.innerWidth-1;
        var h = window.innerHeight-1;
		var backdrop = $('<div style="width:'+w+'px;height:'+h+'px;background-color:black;z-index:999998;position:absolute;top:0px;left:0px;opacity:0;"></div>');
        
        var b = backdrop.appendTo("body");
        b.fadeTo(1000, 0.5);
		var a = e.appendTo("body");
		a.fadeIn(250);
		
	}
    
    


}
