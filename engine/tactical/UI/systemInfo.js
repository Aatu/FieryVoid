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
		
		
		
		w.show();
		
	},
	
	hideSystemInfo: function(){
		$("#systemInfo").hide();
	}


}