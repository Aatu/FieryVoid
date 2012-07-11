window.shipClickable = {

	shipClickableTimer: null,
	ship: null,
	shipNameElement: null,
	weaponTargetingElement: null,
	testStacked: true,
	
	shipclickableMouseOver: function(e){
		clearTimeout(shipClickable.shipClickableTimer); 
		shipClickable.shipClickableTimer = setTimeout(shipClickable.doMouseOver, 250);
		shipClickable.ship = gamedata.getShip($(this).data("id"));
		if ($(this).hasClass('shiplistentry'))
			shipClickable.testStacked = false;
		else
			shipClickable.testStacked = true;
	},
	
	shipclickableMouseOut: function(e){
		clearTimeout(shipClickable.shipClickableTimer); 
		shipClickable.ship = null;
		gamedata.mouseOverShipId = -1;
		if (shipClickable.shipNameElement != null){
			shipClickable.shipNameElement.hide();
		}
		
		if (shipClickable.weaponTargetingElement != null){
			shipClickable.weaponTargetingElement.hide();
		}
				
		if (gamedata.gamephase > 1){
			ew.RemoveEWEffects();
			
		}
		drawEntities();
	},
	
	doMouseOver: function(){
        var selectedShip = gamedata.getSelectedShip();
		var ship = shipClickable.ship;
		gamedata.mouseOverShipId = ship.id;
		shipManager.drawShips();
		
		if (shipClickable.shipNameElement == null){
			shipClickable.shipNameElement = $("#shipNameContainer");
		}
		
		
		var e = shipClickable.shipNameElement;
		var sc = ship.shipclickableContainer;
		var pos = shipManager.getShipPositionForDrawing(ship);
		e.hide();
		
		
		
		var fac = "ally";
		if (ship.userid != gamedata.thisplayer){
			fac = "enemy";
		}
		
		$('#shipNameContainer .namecontainer').html("");
		
		if (shipSelectList.haveToShowList(ship) && shipClickable.testStacked){
		
			var list = shipManager.getShipsInSameHex(ship);
			for (var i in list){
				var p = ', ';
				if (i == 0)
					p = "";
				$('<span class="name value '+fac+'">'+p+list[i].name+'</span>').appendTo('#shipNameContainer .namecontainer');
			}
            $(".entry", e).remove();
            /*
			$(".rolling.value", e).html("");
			$(".turndelay.value", e).html("");
			$(".pivoting.value", e).html("");
			$(".speed.value", e).html("");
			$(".iniative.value", e).html("");
			$(".rolled.value", e).html("");
            $(".unused.value", e).html("");
            */
		}else{
				
			$('<span class="name value '+fac+'">'+ship.name+'</span>').appendTo('#shipNameContainer .namecontainer');
			$(".entry", e).remove();
			
            shipClickable.addEntryElement('Unused thrust: ' + shipManager.movement.getRemainingEngineThrust(ship), ship.flight === true);
            shipClickable.addEntryElement('Pivoting ' + shipManager.movement.isPivoting(ship), shipManager.movement.isPivoting(ship) !== 'no');
            shipClickable.addEntryElement('Rolling', shipManager.movement.isRolling(ship));
            shipClickable.addEntryElement('Rolled', shipManager.movement.isRolled(ship));
            shipClickable.addEntryElement('Turn delay: ', shipManager.movement.calculateCurrentTurndelay(ship));
            shipClickable.addEntryElement('Speed: ' + shipManager.movement.getSpeed(ship));
            
            if (selectedShip && selectedShip != ship){
                var dis = Math.ceil(mathlib.getDistanceBetweenShipsInHex(selectedShip, ship));
                shipClickable.addEntryElement('DISTANCE: ' + dis);
            }
		}
		
		var dis = 10 + (40*gamedata.zoom);
		
		if (dis > 60)
			dis = 60;
			
		
		
		e.css("left", (pos.x-100) + "px").css("top", (pos.y + dis) +"px");
		
		if (shipSelectList.haveToShowList(ship) && shipClickable.testStacked){
			$(".fire",e).hide();
		}else{
			if (gamedata.isEnemy(ship) 
				&& ((gamedata.gamephase == 3 && shipManager.systems.selectedShipHasSelectedWeapons())
				|| (gamedata.gamephase == 1 && shipManager.systems.selectedShipHasSelectedWeapons(true)))
				&& gamedata.waiting == false){
						
				weaponManager.targetingShipTooltip(ship, e, null);
				$(".fire",e).show();
			}else{
			
				$(".fire",e).hide();
			}
		}
		
		
		e.fadeTo(500, 0.65);
		
		if (gamedata.gamephase > 1){
			//ew.adHostileOEWindicatiors(ship);
			ew.adEWindicators(ship);
			drawEntities();
		}
		
		
		
	},
    
    addEntryElement: function(value, condition){
        if (condition === false || condition === 0)
            return;
        
        var s = value;
        if ( condition !== true && condition)
            s += condition;
                
        $('<div class="entry"><span class="value">'+s+'</span></div>')
            .insertAfter('#shipNameContainer .namecontainer');;
    }
	
	
}
