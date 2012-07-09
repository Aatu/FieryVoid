window.shipSelectList = {


	haveToShowList: function(ship){
	
		var list = shipManager.getShipsInSameHex(ship);
		
		if (list.length > 1)
			return true;
        
        if (shipManager.isElint(ship)){
            return true;
		}
		return false;
		
	
	},
	
	showList: function(ship){
		
		$(".shipSelectList").remove();
		
		var list = shipManager.getShipsInSameHex(ship);
		var pos = shipManager.getShipPositionForDrawing(ship);
		
		var e = $('<div class="shipSelectList"></div>');
		for (var i in list){
			var listship = list[i];
			
			var fac = "ally";
			if (listship.userid != gamedata.thisplayer){
				fac = "enemy";
			}
			
			$('<div oncontextmenu="shipSelectList.onShipContextMenu(this);return false;" class="shiplistentry" data-id="'+listship.id+'"><span class="name '+fac+'">'+listship.name+'</span></div>').appendTo(e);
			
            if (shipManager.isElint(ship)){
                if (gamedata.isEnemy(ship, listship)){
                    
                }else{
                    $('<div style="margin-left:20px;" oncontextmenu="shipSelectList.onShipContextMenu(this);return false;" class="shiplistentry" data-action="FOEW" data-id="'+listship.id+'"><span class="name '+fac+'">Assign support OEW</span></div>').appendTo(e);
                    $('<div style="margin-left:20px;" oncontextmenu="shipSelectList.onShipContextMenu(this);return false;" class="shiplistentry" data-action="FDEW" data-id="'+listship.id+'"><span class="name '+fac+'">Assign support DEW</span></div>').appendTo(e);
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
		
		
		shipManager.doShipClick(ship);
		
	
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
