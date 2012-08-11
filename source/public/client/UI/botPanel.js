

jQuery(function(){
	$(".BPcontainer.thrusters .iconmask").on("click", botPanel.onThrusterClick);
    $(".assignthrustcontainer .cancel").on("click", shipWindowManager.cancelAssignThrustEvent);
    $(".assignthrustcontainer .ok").on("click", shipWindowManager.doneAssignThrust);
    $(".ewentry.CCEW .button1").on("click", ew.buttonDeassignEW);
    $(".ewentry.CCEW .button2").on("click", ew.buttonAssignEW);
    $(".ewentry.BDEW .button1").on("click", ew.buttonDeassignEW);
    $(".ewentry.BDEW .button2").on("click", ew.buttonAssignEW);
});

window.botPanel = {
    
    onLogUIClicked: function(e){
        
        var e = $(this);
        $(".logUiEntry").removeClass("selected");
        e.addClass("selected");
        
        $(".logPanelEntry").hide();
        
        var select = e.data("select");
        var e = $(select);
        e.show();
        e.trigger("onshow");
    },

	onShipStatusChanged: function(ship){
		if (!gamedata.isSelected(ship))
			return;
			
		if (gamedata.gamephase == 1){
			botPanel.setEW(ship);
		}
		
		if (gamedata.gamephase == 2){
			botPanel.setMovement(ship);
		}
	
	},
	
	setEW: function(ship){
		shipWindowManager.addEW(ship, $("#botPanel"));
	},
	
	setMovement: function(ship){
		var speed = shipManager.movement.getSpeed(ship);        
        var turncost = Math.ceil(speed * ship.turncost);
		var turndelay = Math.ceil(speed * ship.turndelaycost);
		
		$("#botPanel .value.currentturndelay").html(shipManager.movement.calculateCurrentTurndelay(ship));
		$("#botPanel .value.turndelay").html(turndelay);
		$("#botPanel .value.turncost").html(turncost);
		
		$("#botPanel .value.accelcost").html(ship.accelcost);
		$("#botPanel .value.pivotcost").html(ship.pivotcost);
		$("#botPanel .value.rollcost").html(ship.rollcost);
		if (ship.flight){
			$("#botPanel .value.evasion").html(shipManager.movement.getJinking(ship));
			$("#botPanel .entry.evasion").show();
		}else{
			$("#botPanel .entry.evasion").hide();
		}
		
		$("#botPanel .value.unusedthrust").html(shipManager.movement.getRemainingEngineThrust(ship));
		
		
	},
	
	setSystemsForAssignThrust: function(ship, requiredThrust, stillReq){
		var loc = "";
		for (var i = 4; i>0;i--){
			if ( i == 1) {loc =".frontcontainer";}
			if ( i == 2) {loc =".aftcontainer";}
			if ( i == 3) {loc =".portcontainer";}
			if ( i == 4) {loc =".starboardcontainer";}
			
			var thrusters = shipManager.systems.getThrusters(ship, i);
			for (var t in thrusters){
				var thruster = thrusters[t];
				var slotnumber = parseInt(t)+1;
				if (shipManager.systems.isDestroyed(ship, thruster))
					continue;
					
				var cont = $(".BPcontainer.thrusters " +loc+" .slot_"+slotnumber);
				cont.addClass("exists");
				cont.data("id", thruster.id);
				cont.data("ship",ship.id);
				cont.data("direction", thruster.direction);
				
				
				if (requiredThrust[i] != null){
				cont.addClass("enableAssignThrust");
				
				}
				if (stillReq[i] == null){
					cont.removeClass("enableAssignThrust");
				}
				
				var field = cont.find(".efficiency.value");
				var channeled = shipManager.movement.getAmountChanneled(ship, thruster);
				var output = shipManager.systems.getOutput(ship, thruster);
			
				if (channeled > output){
					field.addClass("darkred");
				}else{
					field.removeClass("darkred");
				}
				
				if (channeled < 0)
					channeled = 0;
					
				field.html(channeled + "/" + output);
				
			
			}
		}
		
		var field = $(".BPcontainer.thrusters .engine .efficiency.value");
		var rem = shipManager.movement.getRemainingEngineThrust(ship);
		field.html(rem);
	
	},
	
	onThrusterClick: function(e){
		e = $(".system").has($(this));
		
		if (!e.hasClass("enableAssignThrust"))
			return;
			
		var ship = gamedata.getShip(e.data("ship"));
		var system = ship.systems[e.data("id")];
		
		if (shipManager.isDestroyed(ship) || shipManager.isDestroyed(ship, system) || shipManager.isAdrift(ship))
			return;
		
		if (ship.userid != gamedata.thisplayer)
			return;
			
		shipManager.movement.assignThrust(ship, system);
		shipWindowManager.assignThrust(ship);
	

	},
	
	onThrusterContextMenu: function(e){
		e = $(".system").has($(e));
		
		if (!e.hasClass("enableAssignThrust"))
			return;
			
		var ship = gamedata.getShip(e.data("ship"));
		var system = ship.systems[e.data("id")];
		
		if (shipManager.isDestroyed(ship) || shipManager.isDestroyed(ship, system) || shipManager.isAdrift(ship))
			return;
		
		if (ship.userid != gamedata.thisplayer)
			return;
			
		shipManager.movement.unAssignThrust(ship, system);
		shipWindowManager.assignThrust(ship);
		

	}


}
