window.confirm = {

	whtml:'<div class="confirm"><div class="ui"><div class="confirmok"></div><div class="confirmcancel"></div></div></div>',

	showConfirmOkCancel: function(message, okcb, cancelcb){
	
		
		
		
	
	},
	
	showShipBuy: function(ship, callback){
		var e = $(this.whtml);
		$('<label>Name your new '+ship.shipClass+':</label><input type="text" name="shipname" value="Nameless"></input>').prependTo(e);
		//$('<div class="message"><span>Name your new '+ship.shipClass+'</span></div>').prependTo(e);
		$(".confirmok", e).bind("click", callback);
		$(".confirmcancel",e).bind("click", function(){$(".confirm").remove();});
		$(".confirmok",e).data("shipclass", ship.phpclass);
		var a = e.appendTo("body");
		a.fadeIn(250);
		
		
	
	}, 
	
	error: function(msg, callback){
	
		var e = $('<div class="confirm error"><div class="ui"><div class="confirmok" style="margin:auto;"></div></div></div>');
		$('<span>'+msg+'</span>').prependTo(e);
		//$('<span>ERROR</span></br>').prependTo(e);
		//$('<div class="message"><span>Name your new '+ship.shipClass+'</span></div>').prependTo(e);
		
		
		$(".ok", e).bind("click", callback);
		$(".confirmok", e).bind("click", function(){$(".confirm").remove();});
		$(".confirmcancel",e).remove();
		$(".ok",e).css("left", "45%");
		var a = e.appendTo("body");
		a.fadeIn(250);
		
	}


}