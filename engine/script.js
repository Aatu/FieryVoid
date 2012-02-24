function populateUI(){
	for(var i in gamedata.taskforces){
		var tf = gamedata.taskforces[i];
		drawTaskforce(tf);
		var shipnumber = 0;
		for (var a in tf.ships){
			shipnumber++;
			drawShip(tf, tf.ships[a], shipnumber);
		}
		var x = tf.x;
		var y = tf.y;
		
		for (var a in tf.orders){
			drawOrder(tf, tf.orders[a], x, y);
			x = tf.orders[a].x;
			y = tf.orders[a].y;
		}
	}
}

function drawTaskforce(taskforce){

	var id = taskforce.id;
	var o = $("#taskforcecontainer .taskforce.id-"+id);
	var alreadyExists = true;
	
	if (o.length==0){
		o = $("#templatecontainer .taskforce");
		alreadyExists = false;
	}
		
	if (!o.hasClass("id-" + id))
		o.addClass("id-" + id);
	
	if (taskforce.isjumpcapable == true){
		if (!o.hasClass("jumpcapable"))
			o.addClass("jumpcapable");
			
		$(".dataentry.jumpengine", o).html(taskforce.jumpspeed).show();
		$(".symbol.jumpengine", o).show();
	}else{
		o.removeClass("jumpcapable");
		$(".dataentry.jumpengine", o).hide();
		$(".symbol.jumpengine", o).hide();
	}
		
	o.data("id", id);
	
	$(".dataentry.name", o).html(taskforce.name);
	$(".dataentry.location", o).html("("+taskforce.x+","+taskforce.y+")");
	$(".dataentry.bestscanner", o).html(taskforce.bestscanner);
	$(".dataentry.totalfighters", o).html(taskforce.totalfighters);
	$(".dataentry.speed", o).html(taskforce.speed)
	var height = (Math.floor(taskforce.shipcount / 5) * 50) + 50;
	
	$(".taskforceships", o).css("height", height + "px");
	
	if (!alreadyExists){
		o.clone(true).appendTo("#taskforcecontainer");
		o.removeClass("id-" + id);
		}
	
	
	//map marker
	
	o = $("#mapcontainer .mapmarkertaskforce.id-"+id);
	alreadyExists = true;
	
	if (o.length==0){
		o = $("#templatecontainer .mapmarkertaskforce");
		alreadyExists = false;
	}
	
	if (taskforce.isjumpcapable == true){
		if (!o.hasClass("jumpcapable"))
			o.addClass("jumpcapable");
	}else{
		o.removeClass("jumpcapable");
	}
	
	if (!o.hasClass("id-" + id))
		o.addClass("id-" + id);
		
	o.addClass("friendly");
	$(".dataentry.name", o).html(taskforce.name);
	
	o.data("id", id);
	
	o.css("left", parseInt(taskforce.x) - 11 + "px");
	o.css("bottom", parseInt(taskforce.y) + 11 + "px");
	if (!alreadyExists){
		o.clone(true).appendTo("#mapcontainer");
		o.removeClass("id-" + id);
	}
	
	
	//canvas
	/*
	o = $("#mapcontainer .mapordercanvas#canvasid-"+id);
	alreadyExists = true;
	
	if (o.length==0){
		o = $("#templatecontainer .mapordercanvas");
		alreadyExists = false;
	}
	
	o.attr("id", "canvasid-"+id);
	
	if (!alreadyExists){
		o.clone(true).appendTo("#mapcontainer");
		
	}
	*/

}

function drawShip(taskforce, ship, shipnumber){
	var tid = taskforce.id;
	var sid = ship.id;
	var alreadyExists = true;
	
	var o = $("#taskforcecontainer .taskforce.id-"+tid + " .taskforceships .ship.container.id-"+sid);
	
	if (o.length==0){
		o = $("#templatecontainer .ship.container");
		alreadyExists = false;
	}
	
	if (!o.hasClass("id-" + sid))
		o.addClass("id-" + sid);
	
	$(".dataentry.name", o).html(ship.name);
	$(".dataentry.type", o).html(ship.type.toUpperCase());
	
	var loaded = gamedata.turn - ship.lastjumpturn;
	if (loaded > ship.jumpengine)
		loaded = ship.jumpengine;
	
	$(".dataentry.jumpengine", o).html(loaded +"/"+ship.jumpengine);
	
	if (ship.jumpengine == 0)
		$(".systementry.jumpengine", o).hide();
	else
		$(".systementry.jumpengine", o).show();
	
	var scanner = parseInt(ship.scanner) + parseInt(ship.scannerbonus) - parseInt(ship.scannerdamage);
	$(".dataentry.scanner", o).html(scanner);
	
	$(".dataentry.fighters", o).html(ship.fighters);
	
	var image = "";
	
	if (ship.shipclass == 5){
	
	}else if (ship.shipclass == 4){
		image = "ship_capital.gif";
	}else if (ship.shipclass == 3){
		image = "ship_hvy.gif";
	}else if (ship.shipclass == 2){
		image = "ship_medium.gif";
	}else{
	
	}
	$(".ship.symbol", o).css("background-image", "url(./img/"+image+")");
	
	
	var top = Math.floor((shipnumber-1) / 5) * 50;
	var left = (((shipnumber-1) % 5) * 138) + 5;
	
	o.css("top", top + "px");
	o.css("left", left + "px");
	
	if (!alreadyExists){
		o.clone(true).appendTo("#taskforcecontainer .taskforce.id-"+tid + " .taskforceships");
		o.removeClass("id-" + sid);
	}

}

function drawOrder(taskforce, order, lastx, lasty){
	var tid = taskforce.id;
	var id = order.id;

	var alreadyExists = true;
	
	var o = $("#taskforcecontainer .taskforce.id-"+tid +" .order.id-"+id);
	
	if (o.length==0){
		o = $("#templatecontainer .order");
		alreadyExists = false;
	}
	var time = order.time;
	if (order.ordertype == "move"){
		time = gamelogic.calculateMoveTime(taskforce, order);
		if (taskforce.jumpcapable == 0){
			if (!o.hasClass("nojump")){
				o.addClass("nojump");
	}
		}
	}
	
	if (!o.hasClass(order.ordertype)){
		o.addClass(order.ordertype);
	}
	var w = convertTurntimeToPixel(time);
	o.css("width", w+"px");
	
	if (w>29){
		$(".dataentry.type",o).html(order.ordertype.toUpperCase());
	}else{
		$(".dataentry.type",o).html("");
	}
	
	
	if (!o.hasClass("inhyperspace"))
		o.addClass("inhyperspace");
		
	if (!o.hasClass("id-" + id))
		o.addClass("id-" + id);
	
	if (!alreadyExists){
		o.clone(true).appendTo("#taskforcecontainer .taskforce.id-"+tid + " .ordercontainer");
		o.removeClass("id-" + id);
		o.removeClass("inhyperspace");
		o.removeClass("nojump");
		o.removeClass(order.ordertype);
	}
	
	//canvas
	
	drawOrderCanvas(taskforce, order, lastx, lasty);
	

	
	
}

function redrawOrders(tf){

	var o = $("#taskforcecontainer .taskforce.id-"+tf.id +" .taskforceorders.container td.order").remove();
	clearCanvas();
	var x = tf.x;
	var y = tf.y;
		
	for (var a in tf.orders){
		drawOrder(tf, tf.orders[a], x, y);
		x = tf.orders[a].x;
		y = tf.orders[a].y;
	}

}

function drawOrdersOnCanvas(taskforce){
	
	var x = taskforce.x;
	var y = taskforce.y;
	
	for (var a in taskforce.orders){
			drawOrderCanvas(taskforce, taskforce.orders[a], x, y);
			x = taskforce.orders[a].x;
			y = taskforce.orders[a].y;
		}
		
	showCanvas();
	
}

function drawOrderCanvas(taskforce, order, lastx, lasty){
	if (order.ordertype == "move"){
		drawMoveOrder(order, lastx, lasty);
	}else if (order.ordertype == "patr"){
		drawPatrolOrder(order, gamelogic.getDetectionRadius(taskforce, order));
	}
}


function debug(s){
	if (console && console.log){
		console.log(s);
	}
}

function convertTurntimeToPixel(v){
	return Math.floor(v*0.3);
}
 
var lastclickoncontrol = ((new Date()).getTime());

function sinceLastClicked(){
	var answer = (((new Date()).getTime()) - lastclickoncontrol);
	
	lastclickoncontrol = ((new Date()).getTime());
	
	return (answer < 100);
	
 }
 

function calculateDistance (x1,y1,x2,y2){
	var a = Math.sqrt(Math.pow((x2-x1),2) + Math.pow((y2-y1),2));
	return a;
}


		
function selectTaskforce(){
		
	if ($(this).hasClass("selected") || $(this).parent().hasClass("selected"))
		return;
		
	if (sinceLastClicked())
		return;
		
	hideMapChooseBox();
	hideCanvas();
	
	var id = $(this).data("id");
	if (!id){
		id = $(this).parent().data("id");
	}
	
	
	
	selectedid = id;
	
	
	$(".taskforce.container.selected .taskforceships.container").slideToggle(); 
	
	$(".taskforce.container").each(function(){$(this).removeClass("selected");$(this).animate({opacity: 0.5}, "fast");});
	$(".mapmarkertaskforce").each(function(){$(this).removeClass("selected");$(this).animate({opacity: 0.5}, "fast");});
	$(".mapcanvas.container").each(function(){$(this).removeClass("selected").hide();});
	
	$(".taskforce.container.id-"+ id).addClass("selected");
	$(".mapmarkertaskforce.id-" + id).addClass("selected");
	$(".mapcanvas.container.id-" + id).addClass("selected").show();
	
	$(".mapmarkertaskforce.id-" + id).animate({opacity: 1}, "fast");
	$(".taskforceships.container", $(".taskforce.container.id-"+id)).slideToggle();
	$(".taskforce.container.id-"+id).animate({opacity: 1}, "fast");
	
	drawOrdersOnCanvas(gamedata.taskforces[id]);
}

function endTurn(){
	var JSONstring = JSON.stringify(gamedata);
	$("#toPHP").attr("value", JSONstring);
	$("#endturnform").submit();
}





