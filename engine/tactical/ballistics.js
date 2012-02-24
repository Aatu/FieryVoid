window.ballistics = {

	initBallistics: function(){
	
		for (var i in gamedata.ballistics){
			ballistics.createHexBallistics(gamedata.ballistics[i]);
			ballistics.calculateBallisticLocation(gamedata.ballistics[i]);
		}
		
	},
	
	calculateBallisticLocations: function(ball){
		for (var i in gamedata.ballistics){
			ballistics.calculateBallisticLocation(gamedata.ballistics[i]);
		}
	},
	
	calculateBallisticLocation: function(ball){
	
	},
	
	createHexBallistics: function(ball){
		
		
        if (ball.launchContainer)
            return;
        
        var e = $("#pagecontainer #ballistic_launch_canvas_"+ball.id);

        if (!e.length){
            

            
            var s = 20;
            e = $('<canvas width="'+s+'" height="'+s+'" id="ballistic_launch_canvas_'+ball.id+'" ></canvas>');
            var n = e.appendTo("#pagecontainer");
            n.data("ship", ball.id);
            ball.launchContainer = n;
			
            e = $('<canvas width="'+s+'" height="'+s+'" id="ballistic_target_canvas_'+ball.id+'" ></canvas>');
            n = e.appendTo("#pagecontainer");
            n.data("ship", ball.id);
            ball.targetContainer = n;
			
            ball.ballclickableContainer = $('<div oncontextmenu="ballistics.onBallContextMenu(this);return false;" class="ballclickable ballistic_'+ball.id+'"></div>').appendTo("#pagecontainer");
            ball.ballclickableContainer.data("id", ball.id);
			
        }else{
            ball.launchContainer = e;
			ball.targetContainer = $("#pagecontainer #ballistic_target_canvas_"+ball.id);
            ball.ballclickableContainer = $(".ballclickable.ship_"+ball.id);
        }
	
		
	
	},
	
	
    drawBallistics: function(){
        for (var i in gamedata.ballistics){
            ballistics.drawBallistic(gamedata.ballistics[i]);
        }
    },
    
    drawBallistic: function(ball){
    
        if (!ball.htmlContainer)
            ballistics.createHexBallistics(ball);
        
		if (gamedata.animating){
			ball.ballclickableContainer.css("z-index", "1");
			ball.htmlContainer.hide();
			return;
		}
        //graphics.clearCanvas("shipcanvas_" + ship.id);         
        var launchCanvas = window.graphics.getCanvas("ballistic_launch_canvas_" + ball.id);
		var targetCanvas = window.graphics.getCanvas("ballistic_target_canvas_" + ball.id);
		
        var pos = ball.position;//get in window co
        var targetPos = ball.targetPos;
        
        
        var s = 20;
        var h = Math.round(s/2)
        var hexZ = 2000+ball.id;
        var scZ = 4000+ball.id;
        
        ball.launchContainer.css("top", pos.y -h + "px").css("left", pos.x -h + "px").css("z-index", hexZ);
        ball.targetContainer.css("top", targetPos.y -h + "px").css("left", targetPos.x -h + "px").css("z-index", hexZ);
        
        
		
		
		
        var sc = ship.ballclickableContainer;
        scSize = s*gamedata.zoom;
        sc.css("width", scSize+"px");
        sc.css("height", scSize+"px");
        sc.css("left", ((pos.x) - (scSize*0.5))+"px");
        sc.css("top", ((pos.y) - (scSize*0.5))+"px");
        sc.css("z-index", scZ);
        
		
		if (ball.drawLaunch){
			var launchImg = new Image();
			launchImg.src = "/img/ballisticLaunch.png";      
			$(launchImg).bind("load", function(){
				canvas.clearRect(0, 0, s, s);
							
				canvas.drawImage(launchImg, s, s, s, s);
				
			});
		}
		
		if (ball.drawTarget){
		var targetImg = new Image();
			targetImg.src = "/img/ballisticTarget.png"; 
			$(targetImg).bind("load", function(){
				canvas.clearRect(0, 0, s, s);
							
				canvas.drawImage(targetImg, s, s, s, s);
				
			});
        }
    },

	onBallisticDblClick: function(){
	
	},
	
    onBallisticClick: function(){
	
	},
	
	ballclickableMouseOver: function(){
	
	},
	
    ballclickableMouseOut: function(){
	
	},


}