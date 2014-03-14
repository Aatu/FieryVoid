window.gamedata = {

	thisplayer: 0,
	slots: null,
	ships: [],
	gameid: 0,
	turn: 0,
	phase: 0,
	activeship: 0,
	waiting: true,
	maxpoints:0,
	status: "LOBBY",
    selectedSlot:null,

	canAfford: function(ship){

        var slotid = gamedata.selectedSlot;
        var selectedSlot = playerManager.getSlotById(slotid);

        var points = 0;
		for (var i in gamedata.ships){
            var lship = gamedata.ships[i];
            if (lship.slot != slotid)
                continue;
			points += lship.pointCost;
		}

		points += ship.pointCost;
		if (points > selectedSlot.points)
			return false;

		return true;
	},

	updateFleet: function(ship){
		var a = 0;
		for (var i in gamedata.ships){
			a = i;
		}
		a++;
		ship.id = a;
        ship.slot = gamedata.selectedSlot;
		gamedata.ships[a] = ship;
		var h = $('<div class="ship bought slotid_'+ship.slot+' shipid_'+ship.id+'" data-shipindex="'+a+'"><span class="shiptype">'+ship.shipClass+'</span><span class="shipname name">'+ship.name+'</span><span class="pointcost">'+ship.pointCost+'p</span><span class="remove clickable">remove</span></div>');
		$(".remove", h).bind("click", function(){
			delete gamedata.ships[a];
			h.remove();
			gamedata.calculateFleet();
		});
		h.appendTo("#fleet");
		gamedata.calculateFleet();
	},
    
    constructFleetList: function(){
        
        var slotid = gamedata.selectedSlot;
        var selectedSlot = playerManager.getSlotById(slotid);
        
        $(".ship.bought").remove();
        for (var i in gamedata.ships){
            var ship = gamedata.ships[i];
            if (ship.slot != slotid)
                continue;
            
            var h = $('<div class="ship bought slotid_'+ship.slot+' shipid_'+ship.id+'" data-shipindex="'+ship.id+'"><span class="shiptype">'+ship.shipClass+'</span><span class="shipname name">'+ship.name+'</span><span class="pointcost">'+ship.pointCost+'p</span><span class="remove clickable">remove</span></div>');
            h.appendTo("#fleet");
        }
        $(".ship.bought .remove").bind("click", function(e){
            var id = $(this).parent().data('shipindex');

            for (var i in gamedata.ships)
            {
                if (gamedata.ships[i].id === id)
                {
                    gamedata.ships.splice(i, 1);
                    break;
                }
            }
            $('.ship.bought.shipid_' + id).remove();
            gamedata.calculateFleet();
        });

        gamedata.calculateFleet();
    },

	calculateFleet: function(){

        var slotid = gamedata.selectedSlot;
        if (!slotid)
            return;
        
        var selectedSlot = playerManager.getSlotById(slotid);
		var points = 0;
		for (var i in gamedata.ships){
            if (gamedata.ships[i].slot != slotid)
                continue;
            
			points += gamedata.ships[i].pointCost;
		}

        $('.max').html(selectedSlot.points);
		$('.current').html(points);
		return points;


	},
    
    isMyShip: function(ship){
        return (ship.userid == gamedata.thisplayer);
    },

                orderShipListOnName: function(shipList){
                    var swapped = true;
                    
                    for(var x=1; x< shipList.length && swapped; x++){
                        swapped = false;
                        
                        for(var y=0; y < shipList.length - x; y++){
                            if(shipList[y+1].shipClass < shipList[y].shipClass){
                                var temp = shipList[y];
                                shipList[y] = shipList[y+1];
                                shipList[y+1] = temp;
                                swapped = true;
                            }
                        }
                    }
                },
        
	parseShips: function(json){


        
		gamedata.setShipsFromJson(json);
                
                for (var i in gamedata.allShips){
			var faction = gamedata.allShips[i];
                        
                        this.orderShipListOnName(faction);
                        
			var group = $('<div class="'+i+' faction shipshidden" data-faction="'+i+'"><div class="factionname name"><span>'+i+ '</span><span class="tooltip">(click to expand)</span></div>')
                .appendTo("#store");

                group.find('.factionname').on("click", this.expandFaction);

			for (var index = 0; index < faction.length; index++){
				var ship = faction[index];
				var h = $('<div oncontextmenu="gamedata.onShipContextMenu(this);return false;" class="ship" data-id="'+ship.id+'" data-faction="'+i+'" data-shipclass="'+ship.phpclass+'"><span class="shiptype">'+ship.shipClass+'</span><span class="pointcost">'+ship.pointCost+'p</span><span class="addship clickable">Add to fleet</span></div>');
				h.appendTo("."+i+".faction");
			}
		}



		$(".addship").bind("click", this.buyShip);

	},

    expandFaction: function(event)
    {
        console.log("clicked");
        var clickedElement = $(this);
        clickedElement.parent().toggleClass("shipshidden");
    },

    goToWaiting: function(){
        
    },

	parseServerData: function(serverdata){
		if (serverdata == null){
			window.location = "games.php";
			return;
		}
        
        if (!serverdata.id)
            return;
        
        gamedata.turn = serverdata.turn;
        gamedata.gamephase = serverdata.phase;
        gamedata.activeship = serverdata.activeship;
        gamedata.gameid = serverdata.id;
        gamedata.slots = serverdata.slots;
        //gamedata.ships = serverdata.ships;
        gamedata.thisplayer = serverdata.forPlayer;
        gamedata.maxpoints = serverdata.points;
		gamedata.status = serverdata.status;

		if (gamedata.status == "ACTIVE"){
			window.location = "hex.php?gameid="+gamedata.gameid;
		}




		this.createSlots();
		this.enableBuy();
        this.constructFleetList();
	},
    
    createNewSlot: function(data){
        var template = $("#slottemplatecontainer .slot");
        var target = $("#team"+data.team + ".slotcontainer");
        var actual = template.clone(true).appendTo(target);
        
        actual.data("slotid", data.slot);
        actual.addClass("slotid_"+data.slot);
        gamedata.setSlotData(data);
    },
    
    createSlots: function()
    {
        var selectedSlot = playerManager.getSlotById(gamedata.selectedSlot);
        if (selectedSlot && selectedSlot.playerid != gamedata.thisplayer){
            $('.slot.slotid_'+selectedSlot.slot).removeClass("selected");
            gamedata.selectedSlot = null;
        }
        
        for (var i in gamedata.slots){
            var slot = gamedata.slots[i];
            var slotElement = $('.slot.slotid_'+slot.slot);
            
            if (!slotElement.length){
                gamedata.createNewSlot(slot);
            }
            
            slotElement = $('.slot.slotid_'+slot.slot);
            var data = slotElement.data();
            if (playerManager.isOccupiedSlot(slot)){
                console.log("slot " +slot.slot+" is occupied");
				var player = playerManager.getPlayerInSlot(slot);
                slotElement.data("playerid", player.id);
                slotElement.addClass("taken");
                $(".playername", slotElement).html(player.name);


				if	(slot.lastphase == "-2"){
					slotElement.addClass("ready");

				}
                
                if (player.id == gamedata.thisplayer)
                {
                    if (gamedata.selectedSlot == null)
                        gamedata.selectedSlot = slot.slot;
                    $(".leaveslot", slotElement).show();
                }
                else
                    $(".leaveslot", slotElement).hide();

			}else{
                $(".leaveslot", slotElement).hide();
                
                slotElement.attr("data-playerid", "");
                slotElement.removeClass("taken");
                $(".playername", slotElement).html("");

				slotElement.removeClass("ready");
			}
            
            if (gamedata.selectedSlot == slot.slot){
                gamedata.selectSlot(slot);
            }

        }
    },

    setSlotData: function(data){
        var slot = $(".slot.slotid_"+data.slot);
        $(".name",slot).html(data.name);
        $(".points",slot).html(data.points);
        
        $(".depx",slot).html(data.depx);
        $(".depy",slot).html(data.depy);
        $(".deptype",slot).html(data.deptype);
        $(".depwidth",slot).html(data.depwidth);
        $(".depheight",slot).html(data.depheight);
        $(".depavailable",slot).html(data.depavailable);
        
    },

	clickTakeslot: function(){
        var slot = $(".slot").has($(this));
		var slotid = slot.data("slotid");
        ajaxInterface.submitSlotAction("takeslot", slotid);
	},

    onLeaveSlotClicked: function(){
        var slot = $(".slot").has($(this));
		var slotid = slot.data("slotid");
        ajaxInterface.submitSlotAction("leaveslot", slotid);
    },

	enableBuy: function(){
        var selectedSlot = playerManager.getSlotById(gamedata.selectedSlot);
        if (selectedSlot && selectedSlot.playerid == gamedata.thisplayer){
            $(".buy").show();
        }else{
            $(".buy").hide();
        }
	},

	buyShip: function(e){
		var shipclass = $(this).parent().data().shipclass;
		var ship = gamedata.getShipByType(shipclass);
        
        var slotid = gamedata.selectedSlot;
        var selectedSlot = playerManager.getSlotById(slotid);
        if	(selectedSlot.lastphase == "-2"){
            window.confirm.error("This slot has already bought a fleet!", function(){});
            return false;
        }
        
//		if (gamedata.canAfford(ship)){
			window.confirm.showShipBuy(ship, gamedata.doBuyShip);
//		}else{
//			window.confirm.error("You cannot afford that ship!", function(){});
//		}
	},

	doBuyShip: function(){
		var shipclass = $(this).data().shipclass;
		var ship = gamedata.getShipByType(shipclass);

		var name = $(".confirm input").val();
		ship.name = name;
		ship.userid = gamedata.thisplayer;
                
                if($(".confirm .totalUnitCostAmount").length > 0){
                    ship.pointCost = $(".confirm .totalUnitCostAmount").data("value");
                }
                
                if (!gamedata.canAfford(ship)){
                    $(".confirm").remove();
                    window.confirm.error("You cannot afford that ship!", function(){});
                    return;
                }
                
                if($(".confirm .selectAmount").length > 0){
                    if(ship.flight){
                        // first get the number of fighters in the flight
                        var nrOfFighters = 0;
                        
                        for(var i in ship.systems){
                            nrOfFighters++;
                        }
                        
                        // and get the amount of launchers on a fighter
                        var nrOfLaunchers = 0;

                        for(var j in ship.systems[1].systems){
                            var fighterSystem = ship.systems[1].systems[j];
                            
                            if(!mathlib.arrayIsEmpty(fighterSystem.firingModes) && fighterSystem.missileArray != null){
                                nrOfLaunchers++;
                            }
                        }
                        
                        // get all selections of missiles
                        var missileOptions = $(".confirm .selectAmount");
                        
                        for(var k=0; k < missileOptions.length; k++){
                            var firingMode = $(missileOptions[k]).data("firingMode");

                            // divide the bought missiles over the missileArrays
                            var boughtAmount = $(".confirm .selectAmount." + firingMode).data("value");
                            // perLauncher should always get you an integer as result. The UI handles
                            // buying of missiles that way.
                            var perLauncher = boughtAmount/(nrOfFighters*nrOfLaunchers);

                            for(var i in ship.systems){
                                var fighter = ship.systems[i];

                                for(var j in fighter.systems){
                                    var fighterSystem = fighter.systems[j];

                                    if(!mathlib.arrayIsEmpty(fighterSystem.firingModes) && fighterSystem.missileArray != null){
                                        // find the correct index, depending on the firingMode
                                        for(var index in fighterSystem.firingModes){
                                            if(fighterSystem.firingModes[index] == firingMode){
                                                fighterSystem.missileArray[index].amount = perLauncher;
                                            }
                                        }
                                        
                                    }
                                }
                            }
                        }
                    }else{
                        
                    }
                }
                
		$(".confirm").remove();
		gamedata.updateFleet(ship);
	},

//        arrayIsEmpty: function(array){
//            for(var i in array){
//                return false;
//            }
//
//            return true;
//        },

	getShipByType: function(type){

		for (var race in gamedata.allShips){
			for (var i in gamedata.allShips[race]){
				var ship = gamedata.allShips[race][i];

				if (ship.phpclass == type){
                                    var shipRet = jQuery.extend(true, {}, ship);
                                    
                                    // to avoid two different flights pointing to the
                                    // same fighter object, also extend each fighter
                                    // individually. (This solves the bug of setting
                                    // missile amounts, that suddenly are set for all
                                    // the fighters of the same type.)
                                    for(var i in shipRet.systems){
                                        shipRet.systems[i] = jQuery.extend(true, {}, ship.systems[i]);
                                        
                                        if(shipRet.flight){
                                            // in case of a flight, also do the systems of the fighters
                                            for(var j in shipRet.systems[i].systems){
                                                shipRet.systems[i].systems[j] = jQuery.extend(true, {}, ship.systems[i].systems[j]);
                                            }
                                        }else{
                                            // to avoid problems with ammo and normal ships, also do the
                                            // ship systems
                                            
                                        }
                                    }
                                    
                                    return shipRet;
				}
			}
		}

		return null;

	},

	onReadyClicked: function(){
		var points = gamedata.calculateFleet();

		if (points==0){
			window.confirm.error("You have to buy atleast one ship!", function(){});
			return;
		}

		ajaxInterface.submitGamedata();
	},

    onLeaveClicked: function(){
        window.location = "gamelobby.php?gameid="+gamedata.gameid+"&leave=true";
    },
    
    onSelectSlotClicked: function(e){
        var slotElement = $(".slot").has($(this));
		var slotid = slotElement.data("slotid");
        var slot = playerManager.getSlotById(slotid);
        
        if (slot.playerid == gamedata.thisplayer)
            gamedata.selectSlot(slot);
        
    },

    selectSlot: function(slot){
        $(".slot").removeClass("selected");
        
        $(".slot.slotid_"+slot.slot).addClass("selected");
        gamedata.selectedSlot = slot.slot;
        this.constructFleetList();
    },
            
    onShipContextMenu: function(e){
    
        var id = $(e).data("id");
        var faction = $(e).data("faction");
        
        console.log("id: " + id + " faction: " + faction);
        var ship = gamedata.getShip(id, faction);
        
        if (! ship.shipStatusWindow)
        {
            if (ship.flight){
                ship.shipStatusWindow = flightWindowManager.createShipWindow(ship);
            }else{
                ship.shipStatusWindow = shipWindowManager.createShipWindow(ship);
            }
            
            shipWindowManager.setData(ship);
        }
    
        shipWindowManager.open(ship);
        return false;
    },
            
    getShip: function(id)
    {
        for (var a in gamedata.allShips)
        {
            for (var i in gamedata.allShips[a])
            {
                var ship = gamedata.allShips[a][i];
                if (ship.id == id)
                    return ship;
            }
                
        }
        return null;
    },
            
    setShipsFromJson: function(jsonShips)
    {
        //gamedata.ships = Array();
        
        var factions = Array();
        for (var f in jsonShips)
        {
            var faction = jsonShips[f];
            factions[f] = Array();
            
            for (var i in faction)
            {
                var ship = faction[i];
                factions[f][i] = new Ship(ship);
            }
        }
        
        gamedata.allShips = factions; 
    },


}

window.animation = {
	animateWaiting: function(){}
}