jQuery(function($){

    effects.animationLoop();

});

window.effects = {

    frontAnimations: Array(),
    backAnimations: Array(),
    canvas: null,
    callback: null,
    animationcallback: null,

    animationLoop: function(){
        
        if (effects.backAnimations.length > 0 || effects.frontAnimations.length > 0){
            effects.clearCanvas();
        }else{
            
        }
        
        var del = Array();

        for (var i in effects.backAnimations){
            var ani = effects.backAnimations[i];
                
            if (ani.tics >= ani.totalTics){
                del.push(ani);
                continue;
            }
                
                        
            ani.draw(ani);
        }
        
        for (var i in effects.frontAnimations){
            var ani = effects.frontAnimations[i];
            
            if (ani.tics >= ani.totalTics){
                del.push(ani);
                continue;
            }
                
            ani.draw(ani);
        }
        
        for (var i in del){
            var ani = del[i];
            
            for(var a = effects.backAnimations.length-1; a >= 0; a--){ 
                    if (effects.backAnimations[a] == ani){
                            effects.backAnimations.splice(a,1);
                            
                        }
                }
                
            for(var a = effects.frontAnimations.length-1; a >= 0; a--){ 
                    if (effects.frontAnimations[a] == ani){
                            effects.frontAnimations.splice(a,1);
                            
                        }
                }
                
            ani.callback();
            
        }
    
        setTimeout(effects.animationLoop, 30);
    },
    
    clearCanvas: function(){
        graphics.clearCanvas("effects");
    },
    
    addExplosion: function(pos, scale){
    
            
        var explosion = {
        
            tics:0,
            totalTics:25+Math.floor(Math.random()*11),
            scale:scale,
            size:Math.floor(30*Math.random()+30),
            speed:Math.floor(10*Math.random()+2),
            dissaeppear:Math.floor(10*Math.random()+2),
            green:Math.floor(20*Math.random()-10),
            pos:pos,
            draw:function(self){
                var canvas = effects.getCanvas();
                var size = getSize()*scale;
                var a = getAlpha();
                var pos = self.pos;
                
                canvas.fillStyle = "rgba(250,"+(230+self.green)+",80,"+0.1*a+")";
                graphics.drawCircleNoStroke(canvas, pos.x, pos.y, size, 0);
                graphics.drawCircleNoStroke(canvas, pos.x, pos.y, size*0.7, 0);
                canvas.fillStyle = "rgba(240,"+(155+self.green)+",12,"+0.1*a+")";
                graphics.drawCircleNoStroke(canvas, pos.x, pos.y, size*0.5, 0);
                canvas.fillStyle = "rgba(255,"+(255+self.green)+",170,"+0.1*a+")";
                graphics.drawCircleNoStroke(canvas, pos.x, pos.y, size*0.4, 0);
                graphics.drawCircleNoStroke(canvas, pos.x, pos.y, size*0.3, 0);
                graphics.drawCircleNoStroke(canvas, pos.x, pos.y, size*0.2, 0);
                canvas.fillStyle = "rgba(255,255,255,"+0.8*a+")";
                graphics.drawCircleNoStroke(canvas, pos.x, pos.y, size*0.1, 0);
                self.tics++;
                
                function getSize(){
                    var s = 0;
                    
                    if(self.tics < self.speed){
                        
                        s =self.size* ((1/self.speed)*self.tics)*gamedata.zoom;
                        
                    }else{
                        s = self.size*gamedata.zoom;
                    }
                    
                    return s;
                }
                
                function getAlpha(){
                    a = 1.0;
                    if (self.tics > (self.totalTics - self.dissaeppear)){
                        
                        var t = self.tics - (self.totalTics - 10);
                        a = 1-((1/self.dissaeppear)*t);
                        
                    }
                    
                   return a;
                }
                
            },
            callback:effects.doneDisplayingWeaponFire
        
        }
        
        effects.frontAnimations.push(explosion);
    
    },
    
    addExplosion2: function(pos, scale){
    
        var sprite = "img/explosions/1.png";
        var img = new Image();
        img.src = sprite; 
        
        var explosion = {
        
            tics:0,
            totalTics:25,
            img:img,
            rows:5,
            cols:5,
            size:64,
            scale:scale,
            pos:pos,
            draw:function(self){
                var size = self.size;
                var canvas = effects.getCanvas();
                var col =(self.tics % 5);
                var row = Math.floor(self.tics / 5);
                
                var sx = (col*size);
                var sy = (row*size);
                var sw = size;
                var sh = size;
                var dw = size*scale*gamedata.zoom;
                var dh = size*scale*gamedata.zoom;
                
                var dx = pos.x - Math.round(size*scale*gamedata.zoom*0.5);
                var dy = pos.y - Math.round(size*scale*gamedata.zoom*0.5);
                var image = self.img;
                
                //console.log("tick: "+self.tics+", col: " +col+ ", row: " +row+ ", sx: "+ sx +", sy: " +sy);
                //console.log("sx: "+ sx +", sy: " +sy+ ", sw: " +sw+ ", sh: " +sh+ ", dx: " +dx+", dy: " + dy + ", dw: " + dw +", dh: " + dh);
                canvas.drawImage(image, sx, sy, sw, sh, dx, dy, dw, dh);
                //takes an image, clips it to the rectangle (sx, sy, sw, sh), scales it to dimensions (dw, dh), and draws it on the canvas at coordinates (dx, dy).
                
                self.tics++;
            },
            callback:effects.doneDisplayingWeaponFire
        
        }
        
        effects.frontAnimations.push(explosion);
    
    },
    
    displayAllWeaponFire: function(callback){
        effects.callback = callback;
        effects.doDisplayAllWeaponFire();
    },
    
    doDisplayAllWeaponFire: function(){
		var windows = $(".shipwindow:visible").hide();
        gamedata.effectsDrawing = true;
        
		for (var i in gamedata.ships){
			var ship = gamedata.ships[i];
			
			if (shipManager.isDestroyed(ship) && shipManager.getTurnDestroyed(ship) == gamedata.turn && !ship.destructionAnimated ){
				ship.dontDraw = false;
				ship.destructionAnimated = false;
			}
		
		}
		    
        for (var i in gamedata.ships){
            var ship = gamedata.ships[i];
            
            
            
            for (var a in ship.fireOrders){
                var fire = ship.fireOrders[a];
                if (fire.turn != gamedata.turn || fire.type=='intercept')
                    continue;
                
                if (fire.animated){
                    
                }else{
                    var target = gamedata.getShip(fire.targetid);
                    scrolling.scrollToShip(target);
                    fire.animated = true;
                    var fires = Array();
                    fires.push(fire);
                    
                    var weapon = shipManager.systems.getSystem(ship, fire.weaponid);
                    for (var b in ship.fireOrders){
                        var otherFire = ship.fireOrders[b];
                        var weapon2 = shipManager.systems.getSystem(ship, otherFire.weaponid);
                        if (weapon2.name == weapon.name && otherFire.targetid == fire.targetid && !otherFire.animated && otherFire.turn == gamedata.turn){
                            otherFire.animated = true;
                            fires.push(otherFire);
                        }
                        
                    }
                    
                    combatLog.logFireOrders(fires);
                    effects.displayWeaponFire(fires, effects.doDisplayAllWeaponFire);
                    //infowindow.informFire(4000, fire, function(){effects.displayWeaponFire(fire);},effects.doDisplayAllWeaponFire);
                    
                    
                    
                    return;
                }
            }
            
            
        }
		
		for (var i in gamedata.ships){
			var ship = gamedata.ships[i];
			
			if (shipManager.isDestroyed(ship) && shipManager.getTurnDestroyed(ship) == gamedata.turn && ship.destructionAnimated == false){
				scrolling.scrollToShip(ship);
				effects.displayShipDestroyed(ship, effects.doDisplayAllWeaponFire);
				return;
			}
		
		}
        
        gamedata.effectsDrawing = false;
		windows.show();
        effects.callback();
    
    
    
    },
    
	displayShipDestroyed: function(ship, call){
	
		combatLog.logDestroyedShip(ship);
		effects.animationcallback = call;

		var pos = shipManager.getShipPositionInWindowCo(ship);
		
		 var animation = {
        
            tics:0,
            totalTics:80+Math.floor(Math.random()*25),
            pos:pos,
            draw:function(self){
               
				if (Math.random()*self.totalTics < self.totalTics && Math.random()>0.8 && self.tics < Math.floor(self.totalTics*0.5) ){
					var tPos = {};
					tPos ={x:self.pos.x + Math.floor((Math.random()*30-15))*gamedata.zoom, y:self.pos.y + Math.floor((Math.random()*30-15))*gamedata.zoom};
					
					effects.addExplosion(tPos, (Math.random()*0.15)+0.15);
					
					
				}
				
				if (self.tics > Math.floor(self.totalTics*0.3) && Math.random()>0.8){
					var tPos = {};
					tPos ={x:self.pos.x + Math.floor((Math.random()*30-15))*gamedata.zoom, y:self.pos.y + Math.floor((Math.random()*30-15))*gamedata.zoom};
					effects.addBigExplosion(tPos, (Math.random()*0.20)+0.40);
				}
				
				if (self.tics > Math.floor(self.totalTics*0.6) && self.tics < Math.floor(self.totalTics*0.9) && Math.random()>0.2){
				
					for (var i = Math.floor(Math.random()*3+1); i>0;i--){
						sPos = self.pos;
						var tPos = mathlib.getPointInDirection((Math.round(Math.random()*50)+50)*gamedata.zoom, Math.floor(Math.random()*360), sPos.x, sPos.y);
						
						effects.makeTrailAnimation(sPos, tPos, {projectilespeed:Math.floor(Math.random()*3+2), trailLength:40, animationColor:Array(160, 95, 10), trailColor:Array(248, 216, 65), animationWidth:Math.floor(Math.random()*3+1)}, false);
					}
				}
				
				
				
				self.tics++;
				
				if (self.tics == Math.floor(self.totalTics*0.9)){
					ship.dontDraw = true;
					ship.destructionAnimated = true;
					shipManager.drawShip(ship);
				}
                
            },
            callback:effects.doneDisplayingWeaponFire
        
        }
        
        effects.frontAnimations.push(animation);
	},
	
	addBigExplosion: function(pos, scale){
		  
        var explosion = {
        
            tics:0,
            totalTics:50+Math.floor(Math.random()*25),
            scale:scale,
            size:Math.floor(25*Math.random()+70),
            speed:Math.floor(10*Math.random()+5),
            dissaeppear:Math.floor(15*Math.random()+10),
            green:Math.floor(20*Math.random()-10),
            pos:pos,
            draw:function(self){
                var canvas = effects.getCanvas();
                var size = Math.round(getSize()*self.scale);
                var a = getAlpha();
                var pos = self.pos;
				var step = Math.ceil(size/30);
				//console.log("step " + step);
				canvas.fillStyle = "rgba(250,"+(230+self.green)+",80,"+0.01*a+")";
				for (var i = size; i>=1; i -= step){
					
				
					if (i< size*0.7){
						canvas.fillStyle = "rgba(250,"+(155+self.green)+",12,"+0.02*a+")";
					}
					if (i< size*0.4){
						canvas.fillStyle = "rgba(255,"+(255+self.green)+",170,"+0.02*a+")";
					}
					if (i< Math.ceil(size*0.3)){
						canvas.fillStyle = "rgba(255,255,255,"+0.05*a+")";
					}
					
					graphics.drawCircleNoStroke(canvas, pos.x, pos.y, i, 0);
					
				}
              
                self.tics++;
				
				if (self.tics == Math.floor(self.totalTics*0.5)){
					
				}

                
                function getSize(){
                    var s = 0;
                    
                    if(self.tics < self.speed){
                        
                        s =self.size* ((1/self.speed)*self.tics)*gamedata.zoom;
                        
                    }else{
                        s = self.size*gamedata.zoom;
                    }
                    
                    return s;
                }
                
                function getAlpha(){
                    a = 1.0;
                    if (self.tics > (self.totalTics - self.dissaeppear)){
                        
                        var t = self.tics - (self.totalTics - 10);
                        a = 1-((1/self.dissaeppear)*t);
                        
                    }
                    
                   return a;
                }
                
            },
            callback:effects.doneDisplayingWeaponFire
        
        }
        
        effects.frontAnimations.push(explosion);
	},
	
    displayWeaponFire: function(fires, call){
    
        effects.animationcallback = call;
    
        for (var i in fires){
            var fire = fires[i];
    
            var target = gamedata.getShip(fire.targetid);
            var shooter = gamedata.getShip(fire.shooterid);

            
            var weapon = shipManager.systems.getSystem(shooter, fire.weaponid);
            
                        
            if (weapon.animation == "laser"){
                effects.animateLaser(fire, weapon);
            }
            if (weapon.animation == "ball"){
                effects.animateBall(fire, weapon);
            }
            if (weapon.animation == "pulse"){
                effects.animatePulse(fire, weapon);
            }
			if (weapon.animation == "trail"){
                effects.animateTrail(fire, weapon);
            }
            
        }
        
    },
    
    doneDisplayingWeaponFire: function(){
        if (effects.backAnimations.length == 0 && effects.frontAnimations.length == 0){
            effects.animationcallback();
        }
    },
    
    getCanvas: function(){
        if (effects.canvas == null){
            effects.canvas = graphics.getCanvas("effects");
            }
        return effects.canvas;
            
    },
	
	animateTrail: function(fire, weapon){
        var shooter = gamedata.getShip(fire.shooterid);
        var target = gamedata.getShip(fire.targetid);
        var sPos = effects.getWeaponLocation(shooter, weapon);
        var hit = (fire.rolled <= fire.needed);
        
        var tPos = effects.getVariedTarget(shooter, target, weapon, fire);
        
                        
        var animation = {
            tics:0,
            totalTics:20,
            shooter:shooter,
            target:target,
            weapon:weapon,
            hit:hit,
            sPos: sPos,
            tPos: tPos,
            draw: function(self){
            
                if (Math.random()>0.95 || self.tics == 19){
                
                    effects.makeTrailAnimation(self.sPos, self.tPos, self.weapon, self.hit);
                    
                    self.tics = self.totalTics;
                    return;
                }
                    
                
                self.tics++;
                
                
            
            },
            callback: effects.doneDisplayingWeaponFire
        };
        
        effects.backAnimations.push(animation);
    
    },
	
	makeTrailAnimation: function(sPos, tPos, weapon, hit){
        //console.log(weapon);
         
		var tTics = Math.ceil(mathlib.getDistance(sPos, tPos)/weapon.projectilespeed)+10;
        var animation = {
            tics:0,
            totalTics:tTics,
            weapon:weapon,
            sPos: sPos,
            tPos: tPos,
            hit: hit,
            draw: function(self){
            
                var canvas = effects.getCanvas();
                var sPos = self.sPos;
                var tPos = self.tPos;
                
                var cur = mathlib.getPointInDistanceBetween(sPos, tPos, self.tics*weapon.projectilespeed*gamedata.zoom);
                
				var trailLength = weapon.trailLength*gamedata.zoom;
				if (mathlib.getDistance(cur, sPos)<trailLength){
					trailLength = mathlib.getDistance(cur, sPos);
				}
				
				var a = getAlpha();
				var trailPos = mathlib.getPointInDistanceBetween(cur, sPos, trailLength);
			
                var c = self.weapon.animationColor;
                //graphics.drawCircleNoStroke(canvas, cur.x, cur.y, 5, 0);
				//graphics.drawCircleNoStroke(canvas, sPos.x, sPos.y, 5, 0);
				//graphics.drawCircleNoStroke(canvas, trailPos.x, trailPos.y, 5, 0);
                if (mathlib.isOver(sPos,tPos,cur)){
                    if (self.hit){
                        effects.addExplosion(cur, weapon.animationExplosionScale);
                    }
                    
                    self.totalTics = self.tics;
                    return;
                }
                
                //canvas.fillStyle = "rgba("+c[0]+","+c[1]+","+c[2]+",0.02)";
                //graphics.drawCircleNoStroke(canvas, cur.x, cur.y, 5*gamedata.zoom, 0);
                
				for (var i = self.weapon.animationWidth; i>=1; i--){
					if (i == 1){
						canvas.fillStyle = "rgba(255,255,255,"+0.6*a+")";
						graphics.drawCircleNoStroke(canvas, cur.x, cur.y, 1*gamedata.zoom, 0);
						continue;
					}
					if (i == 2){
						canvas.fillStyle = "rgba("+c[0]+","+c[1]+","+c[2]+","+0.3*a+")";
						graphics.drawCircleNoStroke(canvas, cur.x, cur.y, 2*gamedata.zoom, 0);
						continue;
					}
					canvas.fillStyle = "rgba("+c[0]+","+c[1]+","+c[2]+","+0.06*a+")";
					graphics.drawCircleNoStroke(canvas, cur.x, cur.y, i*gamedata.zoom, 0);
					//graphics.drawLine(canvas, sPos.x, sPos.y, cur.x, cur.y, i*gamedata.zoom);
					
					
				}
				c = self.weapon.trailColor;
				canvas.lineCap = "round";
				for (var i = self.weapon.animationWidth; i>=1; i--){
				
					var p = mathlib.getPointInDistanceBetween(cur, trailPos, trailLength*(i/self.weapon.animationWidth));
				
					canvas.strokeStyle = "rgba("+c[0]+","+c[1]+","+c[2]+","+0.06*a+")";
					graphics.drawLine(canvas, p.x, p.y, cur.x, cur.y, self.weapon.animationWidth);
					
					
				}
				canvas.lineCap = "butt";				
                
                self.tics++;
				
				function getAlpha(){
                    var a = 0.0;
                    if (self.tics < 10){
                        a = (0.1*self.tics);
                    }else if (self.tics > (self.totalTics - 10)){
                        var t = self.tics - (self.totalTics - 10);
                        a = 1-(0.1*t);
                        
                    }else{
                        a = 1;
                    }
                                       
                    if (a < 0)
                        a = 0.0;

                    return a;
                    
                }
                
                
            
            },
            callback: effects.doneDisplayingWeaponFire
        };
        
        effects.backAnimations.push(animation);
    },
    
    makeBallAnimation: function(sPos, tPos, weapon, hit){
                
                        
        var animation = {
            tics:0,
            totalTics:500,
            weapon:weapon,
            sPos: sPos,
            tPos: tPos,
            hit: hit,
            draw: function(self){
            
                var canvas = effects.getCanvas();
                var sPos = self.sPos;
                var tPos = self.tPos;
                
                var cur = mathlib.getPointInDistanceBetween(sPos, tPos, self.tics*weapon.projectilespeed*gamedata.zoom);
                                
                var c = self.weapon.animationColor;
                                                
                if (mathlib.isOver(sPos,tPos,cur)){
                    if (self.hit){
						effects.addExplosion(cur, weapon.animationExplosionScale);
                    }
                    
                    self.totalTics = self.tics;
                    return;
                }
                
                //canvas.fillStyle = "rgba("+c[0]+","+c[1]+","+c[2]+",0.02)";
                //graphics.drawCircleNoStroke(canvas, cur.x, cur.y, 5*gamedata.zoom, 0);
                
				for (var i = self.weapon.animationWidth; i>=1; i--){
					if (i == 1){
						canvas.fillStyle = "rgba(255,255,255,0.6)";
						graphics.drawCircleNoStroke(canvas, cur.x, cur.y, 1*gamedata.zoom, 0);
						continue;
					}
					if (i == 2){
						canvas.fillStyle = "rgba("+c[0]+","+c[1]+","+c[2]+",0.30)";
						graphics.drawCircleNoStroke(canvas, cur.x, cur.y, 2*gamedata.zoom, 0);
						continue;
					}
					canvas.fillStyle = "rgba("+c[0]+","+c[1]+","+c[2]+",0.05)";
					graphics.drawCircleNoStroke(canvas, cur.x, cur.y, i*gamedata.zoom, 0);
					//graphics.drawLine(canvas, sPos.x, sPos.y, cur.x, cur.y, i*gamedata.zoom);
					
					
				}               
                
                self.tics++;
                
                
            
            },
            callback: effects.doneDisplayingWeaponFire
        };
        
        effects.backAnimations.push(animation);
    },
    
    getVariedTarget: function(shooter, target, weapon, fire){
    
        var tPos = shipManager.getShipPositionInWindowCo(target);
        var sPos = effects.getWeaponLocation(shooter, weapon);
        
        if (fire.rolled > fire.needed){
            tPos = mathlib.getPointInDistanceBetween(sPos, tPos, mathlib.getDistance(sPos, tPos)+(((Math.random()*101)+200)*gamedata.zoom));
            tPos.x += Math.floor((Math.random()*51+40)*gamedata.zoom)-65*gamedata.zoom;
            tPos.y += Math.floor((Math.random()*51+40)*gamedata.zoom)-65*gamedata.zoom;
        }else{
            tPos ={x:tPos.x + Math.floor((Math.random()*20-10))*gamedata.zoom, y:tPos.y + Math.floor((Math.random()*20-10))*gamedata.zoom};
        }
        
        
        
        return tPos;
    },
    
    animatePulse: function(fire, weapon){
        var shooter = gamedata.getShip(fire.shooterid);
        var target = gamedata.getShip(fire.targetid);
        var sPos = effects.getWeaponLocation(shooter, weapon);
        var hit = fire.shotshit;
        
        var tPos = effects.getVariedTarget(shooter, target, weapon, fire);
        var step ={x:Math.random()*1-0.5, y:Math.random()*1-0.5};
                
            
                        
        var animation = {
            tics:0,
            totalTics:5000,
            shooter:shooter,
            target:target,
            weapon:weapon,
            hit:hit,
            fired:weapon.shots,
            sPos: sPos,
            tPos: tPos,
            step: step,
            cHit: 0,
            cMiss: 0,
            started:false,
            startedtic: 0,
            draw: function(self){
            
                if (self.started == false && (Math.random()>0.95 || self.tics == 20)){
                    self.started = true;
                    self.startedtic = self.tics;
                }
                    
                if (self.started){
                    if (self.tics % weapon.rof == 0){
                        var tPos = self.tPos;
                        tPos.x = tPos.x + step.x*(self.tics - self.startedtic)*gamedata.zoom;
                        tPos.y = tPos.y + step.y*(self.tics - self.startedtic)*gamedata.zoom;
                        //if (self.cMiss == self.fired - self.hit || Math.floor(Math.random()*(self.fired+1)) == 1){
                        if (self.cHit < self.hit){
                            
                            var bPos = jQuery.extend({}, tPos);
                            effects.makeTrailAnimation(self.sPos, bPos, self.weapon, true);
                            self.cHit++;
                        }else{
                            
                    
                            tPos.x = tPos.x + step.x*(self.tics - self.startedtic)*gamedata.zoom;
                            tPos.y = tPos.y + step.y*(self.tics - self.startedtic)*gamedata.zoom;
                            tPos = mathlib.getPointInDistanceBetween(self.sPos, tPos, mathlib.getDistance(sPos, tPos)+(((Math.random()*101)+200)*gamedata.zoom));
                            effects.makeTrailAnimation(self.sPos, tPos, self.weapon, false);
                            self.cMiss++;
                        }
                    
                    }
                }
                
                if (self.cHit + self.cMiss == self.fired){
                    self.totalTics = self.tics;
                    return;
                }
                    
                    
                
                self.tics++;
                
                
            
            },
            callback: effects.doneDisplayingWeaponFire
        };
        
        effects.backAnimations.push(animation);
    
    },
    
    animateBall: function(fire, weapon){
        var shooter = gamedata.getShip(fire.shooterid);
        var target = gamedata.getShip(fire.targetid);
        var sPos = effects.getWeaponLocation(shooter, weapon);
        var hit = (fire.rolled <= fire.needed);
        
        var tPos = effects.getVariedTarget(shooter, target, weapon, fire);
        
                        
        var animation = {
            tics:0,
            totalTics:20,
            shooter:shooter,
            target:target,
            weapon:weapon,
            hit:hit,
            sPos: sPos,
            tPos: tPos,
            draw: function(self){
            
                if (Math.random()>0.95 || self.tics == 19){
                
                    effects.makeBallAnimation(self.sPos, self.tPos, self.weapon, self.hit);
                    
                    self.tics = self.totalTics;
                    return;
                }
                    
                
                self.tics++;
                
                
            
            },
            callback: effects.doneDisplayingWeaponFire
        };
        
        effects.backAnimations.push(animation);
    
    },
    
    animateLaser: function(fire, weapon){
        var shooter = gamedata.getShip(fire.shooterid);
        var target = gamedata.getShip(fire.targetid);
        var hit = (fire.rolled <= fire.needed);
        
        var tPos = effects.getVariedTarget(shooter, target, weapon, fire);
        
        var step ={x:Math.random()*0.5-0.25, y:Math.random()*0.5-0.25};
                
        var animation = {
            tics:0,
            totalTics:50,
            shooter:shooter,
            target:target,
            weapon:weapon,
            hit:hit,
            sPos: effects.getWeaponLocation(shooter, weapon),
            tPos: tPos,
            step:step,
            explo:false,
            draw: function(self){
            
                var canvas = effects.getCanvas();
                
                var sPos = self.sPos;
                var tPos = self.tPos;
                var c = self.weapon.animationColor;
                var a = getAlpha();
                
                tPos.x += step.x* gamedata.zoom;
                tPos.y += step.y* gamedata.zoom;
                
                if (self.tics == 20 && self.hit && !self.explo)
                    effects.addExplosion(tPos, weapon.animationExplosionScale);
                    
                if (self.hit && Math.random()>0.90)
                    effects.addExplosion(tPos, weapon.animationExplosionScale);
					
				canvas.lineCap = "round";
				for (var i = self.weapon.animationWidth; i>=1; i--){
					if (i == 1){
						canvas.strokeStyle = "rgba("+c[0]+","+c[1]+","+c[2]+","+a*4+")";
						graphics.drawLine(canvas, sPos.x, sPos.y, tPos.x, tPos.y, 1);
						break;
					}
					canvas.strokeStyle = "rgba("+c[0]+","+c[1]+","+c[2]+","+a+")";
					graphics.drawLine(canvas, sPos.x, sPos.y, tPos.x, tPos.y, i*gamedata.zoom);
					
					
				}
				
				for (var i = Math.round(self.weapon.animationWidth*2*gamedata.zoom); i>=1; i--){
					if (i == 1){
						canvas.fillStyle = "rgba(255,255,255,"+a*2+")";
					}else{
						canvas.fillStyle = "rgba("+c[0]+","+c[1]+","+c[2]+","+a*0.5+")";
					}
					graphics.drawCircleNoStroke(canvas, sPos.x, sPos.y, i, 0);
				}
				canvas.lineCap = "butt";
               
                
                
                
                self.tics++;
                
                function getAlpha(){
                    var a = 0.0;
                    if (self.tics < 10){
                        a = (0.1*self.tics);
                    }else if (self.tics > (self.totalTics - 10)){
                        var t = self.tics - (self.totalTics - 10);
                        a = 1-(0.1*t);
                        
                    }else{
                        a = 1;
                    }
                    
                    a *= 0.2;
                    
                    if (a < 0)
                        a = 0.0;

                    return a;
                    
                }
            
            },
            callback: effects.doneDisplayingWeaponFire
        };
        
        effects.backAnimations.push(animation);
    
    },
    
    getWeaponLocation: function(ship, weapon){
        var a = shipManager.getShipHeadingAngle(ship);
        var shippos = shipManager.getShipPositionInWindowCo(ship);
        
        
        if (weapon.location == 0)
            return shippos;
            
        var count = 0;
        
        for (var i in ship.systems){
            var w = ship.systems[i];
            if (w.location == weapon.location && w.name == weapon.name){
                count++;
            }
            if (w == weapon)
                break;
        }
        
        if (weapon.location == 2){
            a = mathlib.addToDirection(a, 180);
        }else if (weapon.location == 3){
            a = mathlib.addToDirection(a, -90);
        }else if (weapon.location == 4){
            a = mathlib.addToDirection(a, 90);
        }
        
        if (count % 2 == 0){
            a = mathlib.addToDirection(a, -4*count);
        }else{
            a = mathlib.addToDirection(a, (4*count+4));
        }
        
        var pos = mathlib.getPointInDirection( 20*gamedata.zoom, a, shippos.x, shippos.y);
        return pos;
    }
}





















