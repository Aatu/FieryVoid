window.shipSelectList = {


	haveToShowList: function(ship, event){
	
		var list = shipManager.getShipsInSameHex(ship);
		
		if (list.length > 1)
			return true;
        
        if (event && event.which === 1){
            var selectedShip = gamedata.getSelectedShip();
            if (selectedShip != ship && shipManager.isElint(selectedShip) && ew.checkInELINTDistance(selectedShip, ship)){
                return true;
            }
        }
		return false;
		
	
	},
	
	showList: function(ship){
		
		$(".shipSelectList").remove();
		
		var list = shipManager.getShipsInSameHex(ship);
		var pos = shipManager.getShipPositionForDrawing(ship);
		var selectedShip = gamedata.getSelectedShip();
        
       
       
        
		var e = $('<div class="shipSelectList"></div>');
		for (var i in list){
			var listship = list[i];
			var fac = "ally";
			if (listship.userid != gamedata.thisplayer){
				fac = "enemy";
			}
			
			$('<div oncontextmenu="shipSelectList.onShipContextMenu(this);return false;" class="shiplistentry" data-id="'+listship.id+'"><span class="name '+fac+'">'+listship.name+'</span></div>').appendTo(e);
			
            if (selectedShip != listship && shipManager.isElint(selectedShip)){
                if (gamedata.isEnemy(selectedShip, listship) && ew.checkInELINTDistance(selectedShip, listship)){
                    
                }else{
                    $('<div oncontextmenu="shipSelectList.onShipContextMenu(this);return false;" class="shiplistentry action" data-action="SOEW" data-id="'+listship.id+'"><span class="'+fac+'">Assign support OEW</span></div>').appendTo(e);
                    $('<div oncontextmenu="shipSelectList.onShipContextMenu(this);return false;" class="shiplistentry action" data-action="SDEW" data-id="'+listship.id+'"><span class="'+fac+'">Assign support DEW</span></div>').appendTo(e);
                }
            }
		}
		
		var dis = 10 + (40*gamedata.zoom);
		
		if (dis > 60)
			dis = 60;
		
		e.css("left", (pos.x+dis) + "px").css("top", (pos.y - 50) +"px");
		
		e.appendTo("body");
		
		$('.shiplistentry',e).bind('mouseover', shipClickable.shipclickableMouseOver);
		$('.shiplistentry',e).bind('mouseout', shipClickable.shipclickableMouseOut);
		$('.shiplistentry',e).bind('click', shipSelectList.onListClicked);
		
		e.show();
		
		
	
	},
	
	onListClicked: function(e){
	
		var id = $(this).data("id");
        var ship = gamedata.getShip(id);
		
		var action = $(this).data("action");
        if (!action){
            shipManager.doShipClick(ship);
		}else if (action == "SOEW"){
            ew.AssignOEW(ship, "SOEW");
        }else if (action == "SDEW"){
            ew.AssignOEW(ship, "SDEW");
        }
	
	},
	
	onShipContextMenu: function(e){
	
		var id = $(e).data("id");
        var ship = gamedata.getShip(id);
		
		
		shipManager.doShipContextMenu(ship);
	
	},
	
	remove: function(){
		shipClickable.shipclickableMouseOut();
		$(".shipSelectList").remove();
	}



}
