window.systemInfo = {


	showSystemInfo: function(t, system, ship){
		var w = $("#systemInfo");
		var offs = t.offset();

		w.css("left", offs.left + "px");
		w.css("top", offs.top +55+ "px");
		
		
		$("span.name", w).html(system.displayName.toUpperCase());
		
		var h = "";
		
		for (var i in system.data){
			h += '<div><span class="header">'+i+':</span><span class="value">' + system.data[i]+"</span></div>";
		}
		
		//console.log(system.critData);
		if (Object.keys(system.critData).length > 0){
			h +="<div><span>DAMAGE:</span></div><ul>"
			
			for (var i in system.critData){
				h += "<li class='crit'>"+system.critData[i]+"</li>";
			}
			h += "</ul>"
		}
		
		
		
		$(".datacontainer", w).html(h);
		
		
		if (!gamedata.isMyShip(ship) && gamedata.gamephase == 3 && gamedata.waiting == false && gamedata.selectedSystems.length > 0){
			if (weaponManager.canCalledshot(ship, system)){
							
				e = $('<div class="calledtargeting"><span>CALLED SHOT</span></div><div class="targeting"></div>');
				var datac = $(".datacontainer", w);
				datac.append(e);
				weaponManager.targetingShipTooltip(ship, datac, system.id);
			}else{
				e = $('<div class="calledtargeting"><span>CANNOT TARGET</span>');
				$(".datacontainer", w).append(e);
			}
		}
		
		
		
		w.show();
		
	},
	
	hideSystemInfo: function(){
		$("#systemInfo").hide();
	}


}
