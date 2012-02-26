window.ballistics = {
	
	initBallistics: function(){
	
		for (var i in gamedata.ballistics){
			ballistics.createHexBallistics(gamedata.ballistics[i]);
			ballistics.calculateBallisticLocation(gamedata.ballistics[i]);
		}
		ballistics.calculateDrawBallistics();
		
	},
	
	calculateBallisticLocations: function(ball){
		for (var i in gamedata.ballistics){
			ballistics.calculateBallisticLocation(gamedata.ballistics[i]);
		}
	},
	
	hideBallistics: function(){
		$(".ballclickable").hide();
		$(".ballisticcanvas").hide();
	},	
	
	calculateBallisticLocation: function(ball){
		if (!ball.launchPos){
			var angle = shipManager.hexFacingToAngle(ball.facing);
			ball.launchPos = hexgrid.getOffsetPositionInHex(ball.position, angle, 0.7, false);
		}
		
		if (ball.targetid){
			var target = gamedata.getShip(ball.targetid);
			var targetPos = shipManager.getShipPosition(target);
			
			var angle = mathlib.getCompassHeadingOfPoint(targetPos, ball.launchPos);
			ball.targetPos = hexgrid.getOffsetPositionInHex(targetPos, angle, 0.7, false);
		}
		
		
	
	},
	
	calculateDrawBallistics: function(){
					
		for (var i in gamedata.ballistics){
			var ball = gamedata.ballistics[i];
			ball.drawLaunch = true;
			ball.drawTarget = true;
			
			var launchPos1 = hexgrid.positionToPixel(ball.launchPos);
			var targetPos1 = hexgrid.positionToPixel(ball.targetPos);
			
			for (var a in gamedata.ballistics){
				var ball2 = gamedata.ballistics[a];
				if (i == a)
					break;
					
				var launchPos2 = hexgrid.positionToPixel(ball2.launchPos);
				var targetPos2 = hexgrid.positionToPixel(ball2.targetPos);
				
									
				if (mathlib.getDistance(launchPos1, launchPos2)<(10*gamedata.zoom)){
					ball.drawLaunch = false;
				}
					
				if (targetPos1 == null || (mathlib.getDistance(targetPos1, targetPos2)<(10*gamedata.zoom))){
					ball.drawTarget = false;
				}
				
			}
			
		}	
		
	},
		
	createHexBallistics: function(ball){
		
		
        if (ball.launchContainer)
            return;
        
        var e = $("#pagecontainer #ballistic_launch_canvas_"+ball.id);

        if (!e.length){
            

            
            var s = 20;
            e = $('<canvas width="'+s+'" height="'+s+'" id="ballistic_launch_canvas_'+ball.id+'" class="ballisticcanvas" ></canvas>');
            var n = e.appendTo("#pagecontainer");
            n.data("ball", ball.id);
            ball.launchContainer = n;
			
            e = $('<canvas width="'+s+'" height="'+s+'" id="ballistic_target_canvas_'+ball.id+'" class="ballisticcanvas" ></canvas>');
            n = e.appendTo("#pagecontainer");
            n.data("ball", ball.id);
            ball.targetContainer = n;
			
            ball.ballclickableLaunch = $('<div oncontextmenu="ballistics.onBallContextMenu(this);return false;" class="ballclickable launch ballistic_'+ball.id+'"></div>').appendTo("#pagecontainer");
            ball.ballclickableLaunch.data("id", ball.id);
            
            ball.ballclickableTarget = $('<div oncontextmenu="ballistics.onBallContextMenu(this);return false;" class="ballclickable target ballistic_'+ball.id+'"></div>').appendTo("#pagecontainer");
            ball.ballclickableTarget.data("id", ball.id);
			
        }else{
            ball.launchContainer = e;
			ball.targetContainer = $("#pagecontainer #ballistic_target_canvas_"+ball.id);
            ball.ballclickableLaunch = $(".ballclickable.launch.ballistic_"+ball.id);
            ball.ballclickableTarget = $(".ballclickable.target.ballistic_"+ball.id);
        }
	
		
	
	},
	
	
    drawBallistics: function(){
        for (var i in gamedata.ballistics){
            ballistics.drawBallistic(gamedata.ballistics[i]);
        }
    },
    
    drawBallistic: function(ball){
    
        if (!ball.launchContainer)
            ballistics.createHexBallistics(ball);
        
		if (gamedata.animating){
			ball.ballclickableLaunch.css("z-index", "1");
            ball.ballclickableTarget.css("z-index", "1");
			ball.targetContainer.hide();
			ball.launchContainer.hide();
			return;
		}
        //graphics.clearCanvas("shipcanvas_" + ship.id);         
        var launchCanvas = window.graphics.getCanvas("ballistic_launch_canvas_" + ball.id);
		var targetCanvas = window.graphics.getCanvas("ballistic_target_canvas_" + ball.id);
		
		
        var launchPos = hexgrid.positionToPixel(ball.launchPos);
        
        
        
        var s = 20;
        var h = Math.round(s/2)
        var hexZ = 2000+ball.id;
        var scZ = 4000+ball.id;
        
        ball.launchContainer.css("top", launchPos.y -h + "px").css("left", launchPos.x -h + "px").css("z-index", hexZ).show();
        
        
        
		
		
		
        var sc = ball.ballclickableLaunch;
        scSize = s*gamedata.zoom;
        sc.css("width", scSize+"px");
        sc.css("height", scSize+"px");
        sc.css("left", ((launchPos.x) - (scSize*0.5))+"px");
        sc.css("top", ((launchPos.y) - (scSize*0.5))+"px");
        sc.css("z-index", scZ).show();
        
        if (ball.drawTarget){
			var targetPos = hexgrid.positionToPixel(ball.targetPos);
			ball.targetContainer.css("top", targetPos.y -h + "px").css("left", targetPos.x -h + "px").css("z-index", hexZ).show();
			
			sc = ball.ballclickableTarget;
			scSize = s*gamedata.zoom;
			sc.css("width", scSize+"px");
			sc.css("height", scSize+"px");
			sc.css("left", ((targetPos.x) - (scSize*0.5))+"px");
			sc.css("top", ((targetPos.y) - (scSize*0.5))+"px");
			sc.css("z-index", scZ).show();
		}
        
		
		if (ball.drawLaunch){
			var launchImg = new Image();
			launchImg.src = "img/ballisticLaunch.png";      
			$(launchImg).bind("load", function(){
				launchCanvas.clearRect(0, 0, s, s);
							
				launchCanvas.drawImage(launchImg, 0,0);
				
			});
		}
		
		if (ball.drawTarget){
			var targetImg = new Image();
			targetImg.src = "img/ballisticTarget.png"; 
			$(targetImg).bind("load", function(){
				targetCanvas.clearRect(0, 0, s, s);
							
				targetCanvas.drawImage(targetImg, 0,0);
			});
        }
    },

	getBallisticById: function(id){
		for (var i in gamedata.ballistics){
			var ball = gamedata.ballistics[i];
			
			if (ball.id == id)
				return ball;
		}
		
		return null;
		
	},
	
	getBallisticsWithSame: function (ball, launch){
		var balls = Array();
		var pos = ball.launchPos;
		if (!launch)
			pos = ball.targetPos;
		
		pos = hexgrid.positionToPixel(pos);
		
		for (var i in gamedata.ballistics){
			var ball2 = gamedata.ballistics[i];
			var pos2 = ball2.launchPos;
			if (!launch)
				pos2 = ball2.targetPos;
			
			pos2 = hexgrid.positionToPixel(pos2);
				
			if (mathlib.getDistance(pos, pos2)<(10*gamedata.zoom)){
				balls.push(ball2);
			}
			
		}
		
		return balls;
			
	},

	onBallisticDblClick: function(){
	
	},
	
    onBallisticClick: function(){
		console.log("click");
		var ball = ballistics.getBallisticById($(this).data("id"));
		
		var launch = $(this).hasClass("launch");
		
		var balls = Array();
		
		if (launch){
			balls = ballistics.getBallisticsWithSame(ball, true);
		}else{
			balls = ballistics.getBallisticsWithSame(ball, false);
		}
		
		ballistics.showList(balls, launch)
	},
	
	ballclickableMouseOver: function(){
		
		var ball = ballistics.getBallisticById($(this).data("id"));
		var balls = Array();
		
		if ($(this).hasClass("launch")){
			balls = ballistics.getBallisticsWithSame(ball, true);
		}else{
			balls = ballistics.getBallisticsWithSame(ball, false);
		}
		ballistics.adBallisticIndicators(balls);
		
		
	},
	
    ballclickableMouseOut: function(){
		ballistics.RemoveBallisticEffects();
	},
	
	RemoveBallisticEffects: function(){
        for(var i = EWindicators.indicators.length-1; i >= 0; i--){  
            if(EWindicators.indicators[i].type == "ballistic"){              
                EWindicators.indicators.splice(i,1);                 
            }
        }
        EWindicators.drawEWindicators();                
        
    },
    
    adBallisticIndicators: function(balls){
        
        var indicators = Array(); 
        for ( var i in balls){
			var ball = balls[i];
			indicators.push(ballistics.makeBallisticIndicator(ball));
		}
              
        if (indicators.length > 0)
            EWindicators.indicators = EWindicators.indicators.concat(indicators);
      
		EWindicators.drawEWindicators();
    }, 
    
    
	
	makeBallisticIndicator: function(ball){
		if (!ball.drawTarget)
			return null;
			
		var effect = {};
            
         
		effect.ball = ball;
		effect.type = "ballistic"
		effect.launchPos = hexgrid.positionToPixel(ball.launchPos);
		effect.targetPos = hexgrid.positionToPixel(ball.targetPos);
		effect.draw = function(self){
			var ball = self.ball;
			var start = self.launchPos;
			var end = self.targetPos;
			
			var canvas = EWindicators.getEwCanvas();
				
				canvas.strokeStyle = "rgba(255,60,0,0.30)";
				canvas.fillStyle = "rgba(255,60,0,0.30)";
				graphics.drawLine(canvas, start.x, start.y, end.x, end.y, 2);

		};
		
		return effect;
	
	},
	
	showList: function(balls, launch){
		
		if (!balls || balls.lenght == 0)
			return;
			
		$(".shipSelectList").remove();
		
		var pos = hexgrid.positionToPixel(balls[0].launchPos);
		if (!launch){
			pos = hexgrid.positionToPixel(balls[0].targetPos);
		}
		
		var e = $('<div class="shipSelectList"></div>');
		for (var i in balls){
			var ball = balls[i];
			var ship = gamedata.getShip(ball.shooterid);
			var weapon = shipManager.systems.getSystem(ship, ball.weaponid);
						
			var target = null;
			if (ball.targetid){
				targetship = gamedata.getShip(ball.targetid);
				
				var fac = "ally";
				if (targetship.userid != gamedata.thisplayer){
					fac = "enemy";
				}
				
				target = '<span>&gt;</span><span class="name '+fac+'">'+targetship.name+'</span>';
			}
			
			
			$('<div oncontextmenu="return false;" class="shiplistentry '+fac+'" data-id="'+ball.id+'"><span>'+weapon.displayName+'</span>'+target+'</div>').appendTo(e);
			
		}
		
		var dis = 20;
		
		
		e.css("left", (pos.x+dis) + "px").css("top", pos.y - 15 +"px");
		
		e.appendTo("body");
		
		/*
		$('.shiplistentry',e).bind('mouseover', shipClickable.shipclickableMouseOver);
		$('.shiplistentry',e).bind('mouseout', shipClickable.shipclickableMouseOut);
		$('.shiplistentry',e).bind('click', shipSelectList.onListClicked);
		*/
		e.show();
		
		
	
	}


}
