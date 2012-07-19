window.confirm = {

	whtml:'<div class="confirm"><div class="ui"><div class="confirmok"></div><div class="confirmcancel"></div></div></div>',

	showConfirmOkCancel: function(message, okcb, cancelcb){
	
		
		
		
	
	},
	
	showShipBuy: function(ship, callback){
		var e = $(this.whtml);
		$('<label>Name your new '+ship.shipClass+':</label><input type="text" name="shipname" value="Nameless"></input>').prependTo(e);
		//$('<div class="message"><span>Name your new '+ship.shipClass+'</span></div>').prependTo(e);
		$(".confirmok", e).on("click", callback);
		$(".confirmcancel",e).on("click", function(){$(".confirm").remove();});
		$(".confirmok",e).data("shipclass", ship.phpclass);
		var a = e.appendTo("body");
		a.fadeIn(250);
		
		
	
	}, 
	
	error: function(msg, callback){
		var e = $('<div class="confirm error"><div class="ui"><div class="confirmok" style="margin:auto;"></div></div></div>');
		$('<span>'+msg+'</span>').prependTo(e);
		//$('<span>ERROR</span></br>').prependTo(e);
		//$('<div class="message"><span>Name your new '+ship.shipClass+'</span></div>').prependTo(e);
		
		
		$(".ok", e).on("click", callback);
		$(".confirmok", e).on("click", function(){$(".confirm").remove();});
		$(".confirmcancel",e).remove();
		$(".ok",e).css("left", "45%");
		var a = e.appendTo("body");
		a.fadeIn(250);
		
	},
    
    confirm: function(msg, callback){
        var e = $('<div class="confirm error"><div class="ui"><div class="confirmok"></div><div class="confirmcancel"></div></div></div>');
		//var e = $('<div class="confirm error"><div class="ui"><div class="confirmok" style="margin:auto;"></div></div></div>');
		$('<span>'+msg+'</span>').prependTo(e);
		//$('<span>ERROR</span></br>').prependTo(e);
		//$('<div class="message"><span>Name your new '+ship.shipClass+'</span></div>').prependTo(e);
		
		
		$(".ok", e).on("click", callback);
		$(".confirmok", e).on("click", function(){$(".confirm").remove();});
		$(".confirmok", e).on("click", callback);
        $(".confirmcancel",e).on("click", function(){$(".confirm").remove();});
		$(".ok",e).css("left", "45%");
		var a = e.appendTo("body");
		a.fadeIn(250);
		
	},
    
    exception: function(data){
		var e = $('<div style="z-index:999999"class="confirm error"></div>');
        console.dir(data);
        $('<h2>SERVER ERROR</h2>').appendTo(e);
        if (data.code)
            $('<div><span>Error code: '+data.code+'</span></div>').appendTo(e);
        
        if (data.logid)
            $('<div><span>Log id: '+data.logid+'</span></div>').appendTo(e);
        
        $('<div style="margin-top:20px;"><span>'+data.error+'</span></div>').appendTo(e);
        
		
		//$('<span>ERROR</span></br>').prependTo(e);
		//$('<div class="message"><span>Name your new '+ship.shipClass+'</span></div>').prependTo(e);
		
        var w = window.innerWidth-1;
        var h = window.innerHeight-1;
		var backdrop = $('<div style="width:'+w+'px;height:'+h+'px;background-color:black;z-index:999998;position:absolute;top:0px;left:0px;opacity:0;"></div>');
        
        var b = backdrop.appendTo("body");
        b.fadeTo(1000, 0.5);
		var a = e.appendTo("body");
		a.fadeIn(250);
		
	}
    
    


}
