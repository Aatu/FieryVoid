window.shipSelectList = {


	haveToShowList: function(ship){
	
		var list = shipManager.getShipsInSameHex(ship);
		
		if (list.length > 1)
			return true;
			
		return false;
		
	
	},
	
	showList: function(ship){
		
		$(".shipSelectList").remove();
		
		var list = shipManager.getShipsInSameHex(ship);
		var pos = shipManager.getShipPositionForDrawing(ship);
		
		var e = $('<div class="shipSelectList"></div>');
		for (var i in list){
			var ship = list[i];
			
			var fac = "ally";
			if (ship.userid != gamedata.thisplayer){
				fac = "enemy";
			}
			
			$('<div oncontextmenu="shipSelectList.onShipContextMenu(this);return false;" class="shiplistentry" data-id="'+ship.id+'"><span class="name '+fac+'">'+ship.name+'</span></div>').appendTo(e);
			
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
