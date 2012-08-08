window.gamedata = {

	thisplayer: 0,
	slots: null,
	ships: {},
	gameid: 0,
	turn: 0,
	phase: 0,
	activeship: 0,
	waiting: true,
	maxpoints:0,
	status: "LOBBY",

	canAfford: function(ship){

		var points = 0;
		for (var i in gamedata.ships){
			points += gamedata.ships[i].pointCost;
		}

		points += ship.pointCost;
		if (points > gamedata.maxpoints)
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
		gamedata.ships[a] = ship;
		var h = $('<div class="ship" data-shipindex="'+a+'"><span class="shiptype">'+ship.shipClass+'</span><span class="shipname name">'+ship.name+'</span><span class="pointcost">'+ship.pointCost+'p</span><span class="remove clickable">remove</span></div>');
		$(".remove", h).bind("click", function(){
			delete gamedata.ships[a];
			h.remove();
			gamedata.calculateFleet();
		});
		h.appendTo("#fleet")
		gamedata.calculateFleet();
	},

	calculateFleet: function(){

		var points = 0;
		for (var i in gamedata.ships){
			points += gamedata.ships[i].pointCost;
		}

		$('.current').html(points);
		return points;


	},
    
    isMyShip: function(ship){
        return (ship.userid == gamedata.thisplayer);
    },

	parseShips: function(json){

		gamedata.allShips = json;

		for (var i in gamedata.allShips){
			var faction = gamedata.allShips[i];

			$('<div class="'+i+' faction" data-faction="'+i+'"><div class="factionname name"><span>'+i+'</span></div>').appendTo("#store");

			for (var a in faction){
				var ship = faction[a];
				var h = $('<div class="ship" data-shipclass="'+ship.phpclass+'"><span class="shiptype">'+ship.shipClass+'</span><span class="pointcost">'+ship.pointCost+'p</span><span class="addship clickable">Add to fleet</span></div>');
				h.appendTo("."+i+".faction");
			}
		}

		$(".addship").bind("click", this.buyShip);

	},
    
    goToWaiting: function(){
        
    },

	parseServerData: function(serverdata){

		if (serverdata == null){
			window.location = "games.php";
			return;
		}
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
		$('.takeslot').unbind("click", this.clickTakeslot);
		$('.takeslot').bind("click", this.clickTakeslot);
		this.enableBuy();
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
        for (var i in gamedata.slots){
            var slot = gamedata.slots[i];
            var slotElement = $('.slot.slotid_'+slot.slot);
            
            if (!slotElement.length){
                gamedata.createNewSlot(slot);
            }
            
            slotElement = $('.slot.slotid_'+slot.slot);
            var data = slotElement.data();
            if (playerManager.isOccupiedSlot(slot)){

				var player = playerManager.getPlayerInSlot(i);
				if (data.playerid != player.id){
					slotElement.data("playerid", player.id);
					slotElement.addClass("taken");
					$(".playername", slotElement).html(player.name);
				}


				if	(slot.lastphase == "-2"){
					slotElement.addClass("ready");

				}

			}else{

				if (data.playerid){
					slotElement.attr("data-playerid", "");
					slotElement.removeClass("taken");
					$(".playername", slotElement).html("");
				}

				slotElement.removeClass("ready");
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
    
	createSlotsOld: function(){

		for (var i = 1;i<=2;i++){
			var slot = $('.slot[data-slotid="'+i+'"]');
			var data = slot.data();

			if (playerManager.isOccupiedSlot(i)){

				var player = playerManager.getPlayerInSlot(i);
				if (data.playerid != player.id){
					slot.data("playerid", player.id);
					slot.addClass("taken");
					$(".playername", slot).html(player.name);
				}


				if	(player.lastphase == "-2"){
					slot.addClass("ready");

				}

			}else{

				if (data.playerid){
					slot.attr("data-playerid", "");
					slot.removeClass("taken");
					$(".playername", slot).html("");
				}

				slot.removeClass("ready");



			}

		}

	},

	clickTakeslot: function(){
		var slot = $(this).parent().data().slotid;
		console.log(slot);
		window.location = "gamelobby.php?gameid="+gamedata.gameid+"&slotid="+slot;
	},

	enableBuy: function(){

		if (playerManager.isInGame()){
			$(".buy").show();
		}

	},

	buyShip: function(e){
		var shipclass = $(this).parent().data().shipclass;
		var ship = gamedata.getShipByType(shipclass);
		if (gamedata.canAfford(ship)){
			window.confirm.showShipBuy(ship, gamedata.doBuyShip);
		}else{
			window.confirm.error("You cannot afford that ship!", function(){});
		}
	},

	doBuyShip: function(){
		var shipclass = $(this).data().shipclass;
		var ship = gamedata.getShipByType(shipclass);

		var name = $(".confirm input").val();
		ship.name = name;
		ship.userid = gamedata.thisplayer;
		$(".confirm").remove();
		gamedata.updateFleet(ship);
	},

	getShipByType: function(type){

		for (var race in gamedata.allShips){
			for (var i in gamedata.allShips[race]){
				var ship = gamedata.allShips[race][i];

				if (ship.phpclass == type){
					return jQuery.extend(true, {}, ship);
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
	}




}

window.animation = {
	animateWaiting: function(){}
}