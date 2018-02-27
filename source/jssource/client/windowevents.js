$(window).resize(function () { 
    getWindowDimensions();
    resizeGame();
 });

/*
 window.requestAnimFrame = (function(){
    return  window.requestAnimationFrame       || 
            window.webkitRequestAnimationFrame || 
            window.mozRequestAnimationFrame    || 
            window.oRequestAnimationFrame      || 
            window.msRequestAnimationFrame     || 
            function( callback ){
            window.setTimeout(callback, 1000 / 60);
            };
})();
*/
 

 function getWindowDimensions(){
 
    gamedata.gamewidth = window.innerWidth-1;
    gamedata.gameheight = window.innerHeight-1;
 
 }
 
 function resizeGame(){
    $("#hexgrid").attr("width", gamedata.gamewidth);
    $("#hexgrid").attr("height", gamedata.gameheight);
    $("#EWindicators").attr("width", gamedata.gamewidth);
    $("#EWindicators").attr("height", gamedata.gameheight);
	$("#effects").attr("width", gamedata.gamewidth);
    $("#effects").attr("height", gamedata.gameheight);
    $("#pagecontainer").css("height", gamedata.gameheight + "px");
    //$("#ships").attr("width", gamedata.gamewidth);
    //$("#ships").attr("height", gamedata.gameheight);
    

 }

 
jQuery(function(){
    /*
    $("#zoomin").on("click", zooming.zoomin);
    $("#zoomout").on("click", zooming.zoomout);
    $("#pagecontainer").on("mousedown", scrolling.mousedown);
    $("#pagecontainer").on("mouseup", scrolling.mouseup);
    $("#pagecontainer").on("mousemove", scrolling.mousemove);
    $("#pagecontainer").on("mouseout", scrolling.mouseout);
    $("#pagecontainer").on("click", hexgrid.onHexClicked);
    $(document).on("keyup", windowEvents.onKeyUp);
    */
    
    $(".committurn").on("click", gamedata.onCommitClicked);
    $(".cancelturn").on("click", gamedata.onCancelClicked);
    $(".logUiEntry").on("click", botPanel.onLogUIClicked);
    /*
//	$("#helphide").on("click", window.helper.onClickHelpHide);
//	$("#autocommit").on("click", window.helper.onClickAutoCommit);
//	$(".ingamehelppanel").on("click", window.helper.onClickInHelp);

    hookEvent('pagecontainer', 'mousewheel', zooming.mouseWheel);
    document.onkeydown = function( event ){
        event = event || window.event;
        
    }
    */
});

window.windowEvents = {
    
    botElementMouseOver: function (e)
    {
        $(this).addClass("mouseover");
    },
    
    botElementMouseOut: function (e)
    {
        $(this).removeClass("mouseover");
    },
            
    onKeyUp: function(e){
        
        if (windowEvents.chatfocus)
            return;
        
    //    console.log("keycode: " +  e.keyCode);

        switch(e.keyCode){
            case 81:
                animation.cancelAnimation();
                break;
            case 69:
                ew.showAllEnemyEW();
                break;
            case 87:
                ew.showAllFriendlyEW();
                break;
            default:
                return;
         
        }
    }
}

window.zooming = {

    wheeltimer: null,
    wheelzoom: 0,
    wheelpos: {x:0, y:0},
    zoominprogress: false,
    
    mouseWheel: function(e){

   //     console.log("ding");
		if (gamedata.effectsDrawing)
			return;
			
        e = e ? e : window.event;
        var wheelData = e.detail ? e.detail * -1 : e.wheelDelta / 40;

        var x = e.pageX - this.offsetLeft;
        var y = e.pageY - this.offsetTop;
        
        zooming.wheelpos.x = x;
        zooming.wheelpos.y = y;
        
        if ( wheelData < 0)
            zooming.wheelzoom--;
        else
            zooming.wheelzoom++;
        
            

        if (zooming.wheeltimer == null && zooming.zoominprogress == false)
            zooming.wheeltimer = setTimeout(zooming.wheelCallback, 100)

        return cancelEvent(e);
    },
    
    wheelCallback: function(){
	
		shipSelectList.remove();
		
		if (gamedata.effectsDrawing)
			return;
        
        zooming.zoominprogress = true;
        var x = zooming.wheelpos.x;
        var y = zooming.wheelpos.y;
        var m = zooming.wheelzoom;
        zooming.wheelzoom = 0;
        
        zooming.wheeltimer = null;
        
        if (m < 0){
            zooming.zoom(true, x, y, Math.abs(m));
        }else{
            zooming.zoom(false, x, y, Math.abs(m));
        }
    },
    
    zoomin: function(){zooming.zoom(false, Math.round(gamedata.gamewidth/2), Math.round(gamedata.gameheight/2), 1);},
    zoomout: function(){zooming.zoom(true, Math.round(gamedata.gamewidth/2), Math.round(gamedata.gameheight/2), 1);},

    zoom: function(out, cX, cY, multiply){
            
        var offsetX = 0;
        var offsetY = 0;
        var newzoom = gamedata.zoomincrement*multiply;
        
        if (out)
            newzoom *= -1;
        
        newzoom += gamedata.zoom;
        
        if (newzoom <=0.3 && out){
            newzoom = 0.3;
        }
        if (newzoom >= 2 && !out){
            newzoom = 2;
        }
        
        var oldzoom = gamedata.zoom;
        
        if (oldzoom == newzoom)
        {
            zooming.zoominprogress = false;
            return;
        }
        
        var zoomdifference = 1-(newzoom / oldzoom);
            
        var halfX = gamedata.gamewidth/2;
        var halfY = gamedata.gameheight/2;
        
        
        
        offsetX = Math.round(zoomdifference*halfX);// + ((cX - halfX));
        offsetY = Math.round(zoomdifference*halfY);// + ((cY - halfY));
        
        gamedata.zoom = newzoom;

   //     console.log(gamedata.zoom);
        
    //    console.log("zd: " + zoomdifference + ", " + offsetX + "," + offsetY);

        //resizeGame();
        
        scrolling.scroll(offsetX, offsetY);

        zooming.zoominprogress = false;
                
            
       
        
     }

}
 

window.scrolling = {

    scrollingstarted: 0,
    scrolling: false,
    lastpos: {x:0, y:0},
    
    mousedown: function(event){
	
		shipSelectList.remove();
        
		if (!event || event.which !== 3)
            return;
        
        //event.stopPropagation(event);
        if (gamedata.effectsDrawing)
			return;
			
        //console.log(event.handled);
        scrolling.scrolling = true;
        scrolling.scrollingstarted = ((new Date()).getTime())
        
        var x = event.pageX - this.offsetLeft;
        var y = event.pageY - this.offsetTop;
        scrolling.lastpos.x = x;
        scrolling.lastpos.y = y;
        
        //console.log(x + "," + y);
    },
    
    mouseup: function(event){
		if ((((new Date()).getTime()) - scrolling.scrollingstarted ) <= 150){
			//console.log("click on hex");
		}
        //console.log(scrolling.scrolling);
        scrolling.scrolling = false;
        
    },
    
    mouseout: function(event){
		scrolling.scrolling = false;
		
        if (event.clientX <= 0 || event.clientX >= gamedata.gamewidth || event.clientY <= 0 || event.clientY >= gamedata.gameheight){
            scrolling.scrolling = false;
        }

        
    },
    
    mousemove: function(event){
        //event.stopPropagation(event);
		
		if (gamedata.effectsDrawing)
			return;
    
        hexgrid.onMouseOnHex(event, this);
        if (scrolling.scrolling == false){
            return;
        }
        
        var x = event.pageX - this.offsetLeft;
        var y = event.pageY - this.offsetTop;
        
        var dx= x - scrolling.lastpos.x;
        var dy= y - scrolling.lastpos.y;
    
        scrolling.scroll(dx,dy);
        
        scrolling.lastpos.x = x;
        scrolling.lastpos.y = y;
    },
    
    scroll: function (dx, dy){

        offsetX = gamedata.scrollOffset.x - dx;
        if ((offsetX / hexgrid.hexWidth()) >= 1){
            gamedata.scroll.x += Math.floor(offsetX / hexgrid.hexWidth());
            gamedata.scrollOffset.x = offsetX % hexgrid.hexWidth();
        }else if ((offsetX / hexgrid.hexWidth()) <= -1){
            gamedata.scroll.x += Math.floor(offsetX / hexgrid.hexWidth());
            gamedata.scrollOffset.x = offsetX % hexgrid.hexWidth() + hexgrid.hexWidth() ;
        }else{
            gamedata.scrollOffset.x = offsetX % hexgrid.hexWidth();
        }
        
        offsetY = gamedata.scrollOffset.y - dy;
        
        if ((offsetY / hexgrid.hexHeight()) >= 1){
            gamedata.scroll.y += Math.floor(offsetY / hexgrid.hexHeight());
            gamedata.scrollOffset.y = offsetY % hexgrid.hexHeight();
        }else if ((offsetY / hexgrid.hexHeight()) <= -1){
            gamedata.scroll.y += Math.ceil(offsetY / hexgrid.hexHeight());
            gamedata.scrollOffset.y = offsetY % hexgrid.hexHeight();
            
            
        }else{
            gamedata.scrollOffset.y = offsetY % hexgrid.hexHeight();
        }
            
            
        
        
        drawEntities();
    
    },
    
    scrollToShip: function(ship){
   // console.log("scrolling to ship: " + ship.shipClass);

        var gameWidth = gamedata.gamewidth;
        var gameHeight = gamedata.gameheight;

        var hexHeight = hexgrid.hexHeight();
        var hexWidth = hexgrid.hexWidth();
        
        var x = Math.ceil((gameWidth / hexWidth) /2);
        var y = Math.ceil((gameHeight / hexHeight) /2);
        var pos = shipManager.getShipPosition(ship);

      //  console.log(x, y, pos);

        gamedata.scroll.x = pos.x - x;
        gamedata.scroll.y = pos.y - y;
        gamedata.scrollOffset.x = 0;
        gamedata.scrollOffset.y = 0;
        
        drawEntities();
    },
    
    scrollToPos: function(pos){


        var gameWidth = gamedata.gamewidth;
        var gameHeight = gamedata.gameheight;
        var hexHeight = hexgrid.hexHeight();
        var hexWidth = hexgrid.hexWidth();
        
        var x = Math.ceil((gameWidth / hexWidth) /2);
        var y = Math.ceil((gameHeight / hexHeight) /2);

      //  console.log(x, y, pos); {x: -11, y: -2, xO: 0, yO: 0}

        gamedata.scroll.x = pos.x - x;
        gamedata.scroll.y = pos.y - y;
        gamedata.scrollOffset.x = 0;
        gamedata.scrollOffset.y = 0;
        
        drawEntities();
    },

	scrollTo: function(x, y){

		gamedata.scroll.x = x;
        gamedata.scroll.y = y;
        gamedata.scrollOffset.x = 0;
        gamedata.scrollOffset.y = 0;
        
        drawEntities();
	}
    
}






