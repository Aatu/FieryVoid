
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
    
        window.requestAnimFrame(effects.animationLoop);
    },
    
    clearCanvas: function(){
        graphics.clearCanvas("effects");
    },
    
    addExplosion: function(pos, weapon){
        
        var type = weapon.animationExplosionType;
        if (!type)
            type = "normal";
            
        if (type == "normal")
            effects.addNormalExplosion(pos, weapon);
        if (type == "big")
            effects.addBigExplosion(pos, weapon);
        if (type == "AoE")
            effects.addAoEExplosion(pos, weapon);
            
    },
    
    addAoEExplosion: function(pos, weapon){
        
        var r = hexgrid.hexlenght*gamedata.zoom*weapon.animationExplosionScale*2;
        var speed = Math.floor((2*Math.random()+10));
        var totalTics = Math.ceil(r/speed);
        var explosion = {
        
            tics:0,
            totalTics:totalTics,
            scale:weapon.animationExplosionScale,
            weapon:weapon,
            size:r,
            startsize:1,
            speed:speed,
            dissappear:Math.floor(2*Math.random()+17),
            green:0,
            pos:pos,
            draw:function(self){
                var canvas = effects.getCanvas();
                self.startsize += self.speed;
                var size = self.startsize;
                var a = getAlpha();
                var pos = self.pos;
                
                var color = weapon.explosionColor;
                canvas.strokeStyle = "rgba("+color[0]+","+(color[1]+self.green)+","+color[2]+","+0.18*a+")";
                //canvas.fillStyle = "rgba("+color[0]+","+(color[1]+self.green)+","+color[2]+","+0.08*a+")";
                
                graphics.drawCircle(canvas, pos.x, pos.y, size-20, 40);
                graphics.drawCircle(canvas, pos.x, pos.y, size-10, 30);
                graphics.drawCircle(canvas, pos.x, pos.y, size-5, 20);
                graphics.drawCircle(canvas, pos.x, pos.y, size, 15);
                graphics.drawCircle(canvas, pos.x, pos.y, size, 12);
                graphics.drawCircle(canvas, pos.x, pos.y, size, 8);
                graphics.drawCircle(canvas, pos.x, pos.y, size, 6);
                graphics.drawCircle(canvas, pos.x, pos.y, size, 4);
                graphics.drawCircle(canvas, pos.x, pos.y, size, 2);
                self.tics++;
                
                
                function getAlpha(){
                    a = 1.0;
                    if (self.tics > (self.totalTics - self.dissappear)){
                        
                        var t = self.tics - (self.totalTics - self.dissappear);
                        a *= (1-t/self.dissappear);
                        
                    }
                    
                   return a;
                }
                
            },
            callback:effects.doneDisplayingWeaponFire
        
        }
        
        effects.frontAnimations.push(explosion);
    },
    
    addNormalExplosion: function(pos, weapon){
    
            
        var explosion = {
        
            tics:0,
            totalTics:25+Math.floor(Math.random()*11),
            scale:weapon.animationExplosionScale,
            size:Math.floor(30*Math.random()+30),
            speed:Math.floor(10*Math.random()+2),
            dissaeppear:Math.floor(10*Math.random()+2),
            green:Math.floor(20*Math.random()-10),
            pos:pos,
            draw:function(self){
                var canvas = effects.getCanvas();
                var size = getSize()*self.scale;
                var a = getAlpha();
                var pos = self.pos;
                
                canvas.fillStyle = "rgba(255,"+(75+self.green)+",0,"+0.1*a+")";
                graphics.drawCircleNoStroke(canvas, pos.x, pos.y, size, 0);
                canvas.fillStyle = "rgba(255,"+(50+self.green)+",0,"+0.1*a+")";
                graphics.drawCircleNoStroke(canvas, pos.x, pos.y, size*0.6, 0);
                canvas.fillStyle = "rgba(255,"+(100+self.green)+",0,"+0.1*a+")";
                graphics.drawCircleNoStroke(canvas, pos.x, pos.y, size*0.5, 0);
                graphics.drawCircleNoStroke(canvas, pos.x, pos.y, size*0.3, 0);
                canvas.fillStyle = "rgba(255,"+(200+self.green)+",0,"+0.1*a+")";
                graphics.drawCircleNoStroke(canvas, pos.x, pos.y, size*0.3, 0);
                canvas.fillStyle = "rgba(255,"+(150+self.green)+",0,"+0.5*a+")";
                graphics.drawCircleNoStroke(canvas, pos.x, pos.y, size*0.3, 0);
                canvas.fillStyle = "rgba(255,255,255,"+8*a+")";
                graphics.drawCircleNoStroke(canvas, pos.x, pos.y, size*0.2, 0);

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
        
    displayAllWeaponFire: function(callback){
        effects.callback = callback;

        setTimeout(function(){
            effects.doDisplayAllWeaponFire();

        }, 100);
            },
    
    doDisplayAllWeaponFire: function(){

        var windows = $(".shipwindow:visible").hide();
        gamedata.effectsDrawing = true;
        
        for (var i in gamedata.ships){
            var ship = gamedata.ships[i];
            
            if (shipManager.isDestroyed(ship) && shipManager.getTurnDestroyed(ship) == gamedata.turn){
                if (!ship.destructionAnimated){
                    ship.dontDraw = false;
                    ship.destructionAnimated = false;
                }
            }

            if (ship.base){
                var sys = shipManager.getStructuresDestroyedThisTurn(ship);
                if (sys){
               
                    for (var i = 0; i < sys.length; i++){
                        var system = sys[i];

                        if (!system.destructionAnimated){
                            system.destructionAnimated = false;
                        }
                    }
                }
            }
        }

        var shipFire = [];
        var fighterFire = [];

        for (var i in gamedata.ships){
            var ship = gamedata.ships[i];
            
            var fires = weaponManager.getAllFireOrders(ship);


            for (var y in fires){
                var weapon = shipManager.systems.getSystem(ship, fires[y].weaponid);              
                fires[y].priority = weapon.priority;                
                if (weapon.isRammingAttack){                 
                    shipFire.push(fires[y]); //even if it's attack done by fighter
                } else if (ship.shipSizeClass === -1){
                    fighterFire.push(fires[y]);
                } else {                    
                    shipFire.push(fires[y]);
                }       
            }   
        }
        
      shipFire.sort(function(obj1, obj2){
            if(obj1.targetid !== obj2.targetid){
                 return obj1.targetid-obj2.targetid;
            }else if (obj1.priority !== obj2.priority){
                return obj1.priority-obj2.priority; 
            }/*else if (obj1.firingMode !== obj2.firingMode){
                return obj1.firingMode-obj2.firingMode; 
            }*/
            else {
                var $val = obj1.shooterid - obj2.shooterid;
                if ($val == 0) $val = obj1.id - obj2.id;
                return $val
            } 
            //else return obj1.shooterid - obj2.shooterid;
        });

/*  
        shipFire.sort(function(obj1, obj2){
            if (obj1.shooterid != obj2.shooterid){
                return obj1.shooterid - obj2.shooterid;
            }
            else if(obj1.targetid != obj2.targetid){
                 return obj1.targetid-obj2.targetid;
            }
            else {
                return obj1.priority - obj2.priority;
            }
        });
*/



        for (var x in fighterFire){
            shipFire.push(fighterFire[x]);
        }

//        console.log(fo);


            for (var z in shipFire){
                var fire = shipFire[z];

                if (fire.turn != gamedata.turn || fire.type=='intercept' || !fire.rolled)
                    continue;
                
                if (fire.animated){
                }else{
               
                    fire.animated = true;
                    var fires = Array();
                    fires.push(fire);

                    var shooter = gamedata.getShip(fire.shooterid);                    
                    var weapon = shipManager.systems.getSystem(shooter, fire.weaponid);
                    weapon = weaponManager.getFiringWeapon(weapon, fire);
                    
                    var otherFires = weaponManager.getAllFireOrders(shooter);
                    for (var b in otherFires){
                        var otherFire = otherFires[b];
                        var weapon2 = shipManager.systems.getSystem(shooter, otherFire.weaponid);
                        weapon2 = weaponManager.getFiringWeapon(weapon2, otherFire);
                        
                        if (otherFire.rolled && weapon2.name == weapon.name &&  otherFire.firingMode == fire.firingMode && !otherFire.animated && otherFire.turn == gamedata.turn){
                            if ((otherFire.targetid != -1 && fire.targetid != -1 && otherFire.targetid == fire.targetid)
                          //  || (fire.x !== 0 && otherFire.x == fire.x && fire.y !== 0 && otherFire.y == fire.y && otherFire.targetid == fire.targetid) //let's NOT merge hextarget attacks!
                               ){
                                if (fire.pubnotes == otherFire.pubnotes){
                                    otherFire.animated = true;
                                    fires.push(otherFire);
                                }
                            }
                        }                        
                    }
                   //     console.log(fires);
                    effects.displayWeaponFire(fires, effects.doDisplayAllWeaponFire);
                    
                    combatLog.logFireOrders(fires);
                                        //infowindow.informFire(4000, fire, function(){effects.displayWeaponFire(fire);},effects.doDisplayAllWeaponFire);
                    return;
                }
            }
        
        for (var i in gamedata.ships){
            ship = gamedata.ships[i];
            if (ship.shipSizeClass > 0){
                for (var j = 0; j < ship.systems.length; j++){
                    system = ship.systems[j];
                    if (shipManager.criticals.hasCriticalOnTurn(system, "AmmoExplosion", gamedata.turn) && system.destructionAnimated == false){
                        scrolling.scrollToShip(ship);
                        effects.displayAmmoExplosion(ship, system, effects.doDisplayAllWeaponFire);
                        return;
                    }
                }
            }
            if (ship.base){
                if (! shipManager.isDestroyed(ship)){
                    /*
                    var sys = shipManager.getOuterReactorDestroyedThisTurn(ship);
                    if (sys){
                        for (var k = 0; k < sys.length; k++){
                            var system = sys[k];
                            if (system.destructionAnimated == false){
                                scrolling.scrollToShip(ship);
                                effects.displaySubReactorExplosion(ship, system, effects.doDisplayAllWeaponFire);
                                return;
                            }
                        }
                    }
                    */
                    var sys = shipManager.getStructuresDestroyedThisTurn(ship);
                    if (sys){
                        for (var k = 0; k < sys.length; k++){
                            var system = sys[k];
                            if (system.destructionAnimated == false){
                                scrolling.scrollToShip(ship);
                                effects.displayStructureDetroyed(ship, system, effects.doDisplayAllWeaponFire);
                                return;
                            }
                        }
                    }
                }
            }   
            if (shipManager.isDestroyed(ship) && shipManager.getTurnDestroyed(ship) == gamedata.turn){
                if (ship.destructionAnimated == false){
                    scrolling.scrollToShip(ship);
                    effects.displayShipDestroyed(ship, effects.doDisplayAllWeaponFire);
                    return;
                }
            }        
        }


    /*    for (var i in gamedata.ships){
            var ship = gamedata.ships[i];
            if (ship.shipSizeClass > 0){
                for (var j = 0; j < ship.systems.length; j++){
                    for (var k = 0; k < ship.systems[j].criticals.length; k++){
                        if (ship.systems[j].criticals[k].turn == gamedata.turn){
                            var html = ship.phpclass + " got " + ship.systems[j].criticals[k].phpclass;
                            console.log(html);
                            combatLog.logCriticals(ship, html);
                        }
                    }
                }
            }  
        }
*/


        gamedata.effectsDrawing = false;
        windows.show();
        effects.callback();
    },



    displayAmmoExplosion: function(ship, system, call){
    
        combatLog.logAmmoExplosion(ship, system);
        effects.animationcallback = call;

        var pos = shipManager.getShipPositionInWindowCo(ship);
        
        var animation = {
        
            tics:0,
            totalTics:40+Math.floor(Math.random()*25),
            pos:pos,
            variance: ship.canvasSize / 8*gamedata.zoom,
            draw:function(self){
               
                if (Math.random()*self.totalTics < self.totalTics && Math.random()>0.8 && self.tics < Math.floor(self.totalTics*0.5) ){
                    var tPos = {};
                    tPos ={x:self.pos.x + Math.floor((Math.random()*self.variance-(self.variance/2)))*gamedata.zoom, 
                    y:self.pos.y + Math.floor((Math.random()*self.variance-(self.variance/2)))*gamedata.zoom};
                    
                    effects.addExplosion(tPos, {animationExplosionScale:(Math.random()*0.10)+0.10}); 
                }
                
                if (self.tics > Math.floor(self.totalTics*0.3) && Math.random()>0.8){
                    var tPos = {};
                    tPos ={x:self.pos.x + Math.floor((Math.random()*self.variance-(self.variance/2)))*gamedata.zoom,
                    y:self.pos.y + Math.floor((Math.random()*self.variance-(self.variance/2)))*gamedata.zoom};
                    effects.addBigExplosion(tPos, {animationExplosionScale:(Math.random()*0.10)+0.10});
                }
                
                if (self.tics > Math.floor(self.totalTics*0.6) && self.tics < Math.floor(self.totalTics*0.9) && Math.random()>0.2){
                
                    for (var i = Math.floor(Math.random()*3+1); i>0;i--){
                        sPos = self.pos;
                        var tPos = mathlib.getPointInDirection(
                        (Math.round(Math.random()*20)+20)*gamedata.zoom, Math.floor(Math.random()*180),
                         sPos.x, sPos.y);
                        
                        effects.makeTrailAnimation(sPos, tPos, {projectilespeed:Math.floor(Math.random()*1+1), trailLength:35, animationColor:Array(255, 175, 50), trailColor:Array(255, 75, 50), animationWidth:Math.floor(Math.random()*3+2)}, false);
                    }
                }   
                self.tics++;                   
                if (self.tics == Math.floor(self.totalTics*0.9)){
                    system.destructionAnimated = true;
                }
                             
            },
            callback:effects.doneDisplayingWeaponFire        
        }
        effects.frontAnimations.push(animation);
    },   
    


    displayStructureDetroyed: function(ship, system, call){

    //    console.log("loc: " + system.location);

        var loc = system.location;
        var direction = ship.movement[2].value;

        var rotas = ship.movement.length-3;
        var shift = rotas*direction;
        var shifting = true;

    //    console.log("direct: " + direction);
    //    console.log("rotas: " + rotas);
    //    console.log("shift: " + shift);
    
    //    combatLog.logSubReactorExplosion(ship, system);
        effects.animationcallback = call;

        var pos = shipManager.getShipPositionInWindowCo(ship);


        var locs = [1, 41, 42, 2, 32, 31];
        var offsetX = [28, 21, -21, -28, -21, 21];
        var offsetY = [0, 21, 21, 0, -21, -21];



        for (var i = 0; i < locs.length; i++){
            if (loc == locs[i]){
                index = i;
                break;
            }
        }


        while (shift > 0){
            index++;
            shift--;

            if (index == 6){
                index = 0;
            }
        }

        while (shift < 0){
            index--;
            shift++;

            if (index == -1){
                index = 5;
            }
        }


        pos.x +=  offsetX[index];
        pos.y +=  offsetY[index];

        
        var animation = {
        
            tics:0,
            totalTics:80+Math.floor(Math.random()*25),
            pos:pos,
            variance: ship.canvasSize / 8*gamedata.zoom,
            draw:function(self){
               
                if (Math.random()*self.totalTics < self.totalTics && Math.random()>0.8 && self.tics < Math.floor(self.totalTics*0.5) ){
                    var tPos = {};
                    tPos ={x:self.pos.x + Math.floor((Math.random()*self.variance-(self.variance/2)))*gamedata.zoom, 
                    y:self.pos.y + Math.floor((Math.random()*self.variance-(self.variance/2)))*gamedata.zoom};
                    
                    effects.addExplosion(tPos, {animationExplosionScale:(Math.random()*0.20)+0.50}); 
                }
                
                if (self.tics > Math.floor(self.totalTics*0.3) && Math.random()>0.6){
                    var tPos = {};
                    tPos ={x:self.pos.x + Math.floor((Math.random()*self.variance-(self.variance/2)))*gamedata.zoom,
                    y:self.pos.y + Math.floor((Math.random()*self.variance-(self.variance/2)))*gamedata.zoom};
                    effects.addBigExplosion(tPos, {animationExplosionScale:(Math.random()*0.15)+0.25});
                }
                
                if (self.tics > Math.floor(self.totalTics*0.6) && self.tics < Math.floor(self.totalTics*0.9) && Math.random()>0.2){
                
                    for (var i = Math.floor(Math.random()*1+1); i>0;i--){
                        sPos = self.pos;
                        var tPos = mathlib.getPointInDirection(
                        (Math.round(Math.random()*20)+35)*gamedata.zoom, Math.floor(Math.random()*360),
                         sPos.x, sPos.y);
                        
                        effects.makeTrailAnimation(sPos, tPos, {projectilespeed:Math.floor(Math.random()*1+2), trailLength:35, animationColor:Array(255, 175, 50), trailColor:Array(255, 75, 50), animationWidth:Math.floor(Math.random()*3+2)}, false);
                    }
                }

                self.tics++;

                if (self.tics == Math.floor(self.totalTics*0.9)){
                    system.destructionAnimated = true;
                }
                             
            },
            callback:effects.doneDisplayingWeaponFire        
        }
        effects.frontAnimations.push(animation);
    },   


    displayShipDestroyed: function(ship, call){

        var size = 1;

        if (ship.base){
            size = 1.5;
        }

        combatLog.logDestroyedShip(ship);
        effects.animationcallback = call;

        var pos = shipManager.getShipPositionInWindowCo(ship);
        
        var animation = {
        
            tics:0,
            totalTics:80+Math.floor(Math.random()*25),
            pos:pos,
            variance: ship.canvasSize / 4*gamedata.zoom,
            draw:function(self){
               
                if (Math.random()*self.totalTics < self.totalTics && Math.random()>0.8 && self.tics < Math.floor(self.totalTics*0.5) ){
                    var tPos = {};
                    tPos ={x:self.pos.x + Math.floor((Math.random()*self.variance-(self.variance/2)))*gamedata.zoom, 
                    y:self.pos.y + Math.floor((Math.random()*self.variance-(self.variance/2)))*gamedata.zoom};
                    
                    effects.addExplosion(tPos, {animationExplosionScale:((Math.random()*0.15)+0.15*size)});
                    
                    
                }
                
                if (self.tics > Math.floor(self.totalTics*0.3) && Math.random()>0.8){
                    var tPos = {};
                    tPos ={x:self.pos.x + Math.floor((Math.random()*self.variance-(self.variance/2)))*gamedata.zoom,
                     y:self.pos.y + Math.floor((Math.random()*self.variance-(self.variance/2)))*gamedata.zoom};
                    effects.addBigExplosion(tPos, {animationExplosionScale:((Math.random()*0.20)+0.40*size)});
                }
                
                if (self.tics > Math.floor(self.totalTics*0.6) && self.tics < Math.floor(self.totalTics*0.9) && Math.random()>0.2){
                
                    for (var i = Math.floor(Math.random()*3+1); i>0;i--){
                        sPos = self.pos;
                        var tPos = mathlib.getPointInDirection(
                        (Math.round(Math.random()*50)+50)*gamedata.zoom, Math.floor(Math.random()*360),
                         sPos.x, sPos.y);
                        
                        effects.makeTrailAnimation(sPos, tPos, {projectilespeed:Math.floor(Math.random()*1+2), trailLength:20, animationColor:Array(255, 175, 50), trailColor:Array(255, 75, 50), animationWidth:Math.floor(Math.random()*4+2)}, false);
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
    
    addBigExplosion: function(pos, weapon){
          
        var explosion = {
        
            tics:0,
            totalTics:50+Math.floor(Math.random()*25),
            scale:weapon.animationExplosionScale,
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
                canvas.fillStyle = "rgba(255,"+(150+self.green)+",0,"+0.01*a+")";
                for (var i = size; i>=1; i -= step){
                    
                
                    if (i< size*0.7){
                        canvas.fillStyle = "rgba(255,"+(100+self.green)+",0,"+0.02*a+")";
                    }
                    if (i< size*0.4){
                        canvas.fillStyle = "rgba(200,"+(50+self.green)+",0,"+0.05*a+")";
                    }
                    if (i< Math.ceil(size*0.27)){
                        canvas.fillStyle = "rgba(255,255,255,"+0.08*a+")";
                    }
                    if (i< Math.ceil(size*0.15)){
                        canvas.fillStyle = "rgba(255,255,255,"+0.18*a+")";
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
            if (fire.shots == 0) { continue; } //do not animate technical shots
    
            var target = gamedata.getShip(fire.targetid);
            var shooter = gamedata.getShip(fire.shooterid);
            
            var weapon = shipManager.systems.getSystem(shooter, fire.weaponid);
            weapon = weaponManager.getFiringWeapon(weapon, fire);
            var modeIteration = fire.firingMode; //change weapons data to reflect mode actually used
            if(modeIteration != weapon.firingMode){
                while(modeIteration != weapon.firingMode){
                    weapon.changeFiringMode();
                }
            }
            
            //sometimes hex-target weapon gets incorrectly drawn to an unit..correct animation!
            if(weapon.hextarget){
                fire.targetid = -1;
            }

            effects.setZoom(fire, weapon)

        //    setTimeout(function(){
            effects.animateShots(fire, weapon);

         //   }, 1000);
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
    
    getShotDetails: function(fire, weapon){
        var shooter = gamedata.getShip(fire.shooterid);

        var target = null;
        var tPos;

        if (fire.targetid != -1){
            var target = gamedata.getShip(fire.targetid);
            var tPos = effects.getVariedTarget(shooter, target, weapon, fire);
        }else{
            tPos = hexgrid.positionToPixel({x:fire.x, y:fire.y});
        }
        
        var sPos = effects.getWeaponLocation(shooter, weapon);
        
        
        if (fire.type == "ballistic"){
            var ball = ballistics.getBallisticByFireId(fire.id);
            sPos = hexgrid.hexCoToPixel(ball.position.x, ball.position.y);
        }
        
            
        if (mathlib.getDistance(sPos, tPos)>1500)
            sPos = mathlib.getPointInDistanceBetween(tPos, sPos, 1500);
          
        
        var shots = fire.shots;
        var shotshit = fire.shotshit;
        var intercepted = fire.intercepted;
        var step ={x:Math.random()*1-0.5, y:Math.random()*1-0.5};
        
        
        var ret = {shooter:shooter, target:target, sPos:sPos, tPos:tPos, shots:shots, shotshit:shotshit, intercepted:intercepted, step:step};
        //console.dir(ret);
        return ret;
        
    },
    
    makeShotAnimation: function(sPos, tPos, weapon, hit, cur){
        if (weapon.animation == "laser"){
            effects.makeLaserAnimation(sPos, tPos, weapon, hit, cur);
        }
        if (weapon.animation == "ball"){
            effects.makeBallAnimation(sPos, tPos, weapon, hit, cur);
        }
        if (weapon.animation == "trail"){
            effects.makeTrailAnimation(sPos, tPos, weapon, hit, cur);
        }
        if (weapon.animation == "torpedo"){
            effects.makeTorpedoAnimation(sPos, tPos, weapon, hit, cur);
        }
        if (weapon.animation == "beam"){
            effects.makeBeamAnimation(sPos, tPos, weapon, hit, cur);
        }
    },


    setZoom: function(fire, weapon){

        var details = effects.getShotDetails(fire, weapon);

        var start = details.sPos;
        var stop = details.tPos;

        var dis = mathlib.getDistance(start, stop);
        var hexDistance = (dis/hexgrid.hexWidth());


        if (hexDistance < 5){
            gamedata.zoom = 1.3;
        }
        else if (hexDistance < 10){
            gamedata.zoom = 1;
        }
        else if (hexDistance < 15){
            gamedata.zoom = 0.9;
        }
        else if (hexDistance < 25){
            gamedata.zoom = 0.8;
        }
        else if (hexDistance < 35){
            gamedata.zoom = 0.5;
        }

        var ship = gamedata.getShip(fire.shooterid);

        if (fire.targetid != -1){
            var target = gamedata.getShip(fire.targetid);

            scrolling.scrollToShip(target);

        }else{
            scrolling.scrollToPos({x:fire.x, y:fire.y});
        }

     //   console.log(gamedata.zoom);

        resizeGame();

    },   


    
    animateShots: function(fire, weapon){

  //      console.log(fire);
   //     console.log(weapon);


        var details = effects.getShotDetails(fire, weapon);

        var hitSystem = fire.hitSystem;
        
        var modeIteration = fire.firingMode; //change weapons data to reflect mode actually used
        while(modeIteration != weapon.firingMode){
            weapon.changeFiringMode();
        }

        var animation = {
            tics:0,
            totalTics:5000,
            details:details,
            shooter:details.shooter,
            target:details.target,
            weapon:weapon,
            hit:details.shotshit,
            fired:details.shots,
            intercepted:details.intercepted,
            sPos: details.sPos,
            tPos: details.tPos,
            step: details.step,
            cHit: 0,
            cMiss: 0,
            cIntercepted:0,
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
                        tPos.x = tPos.x + self.step.x*(self.tics - self.startedtic)*gamedata.zoom;
                        tPos.y = tPos.y + self.step.y*(self.tics - self.startedtic)*gamedata.zoom;
                        //if (self.cMiss == self.fired - self.hit || Math.floor(Math.random()*(self.fired+1)) == 1){
                        
                        if (self.intercepted > self.cIntercepted){
                            
                            tPos.x = tPos.x + self.step.x*(self.tics - self.startedtic)*gamedata.zoom;
                            tPos.y = tPos.y + self.step.y*(self.tics - self.startedtic)*gamedata.zoom;
                            
                            var iDistance = ((Math.random()*30)+100)*gamedata.zoom;
                            
                            if (mathlib.getDistance(self.sPos, tPos)<iDistance){
                                tPos = mathlib.getPointBetween(self.sPos, tPos, 0.5);
                            
                            }else{
                                tPos = mathlib.getPointInDistanceBetween(tPos, self.sPos, iDistance);
                            }
                            
                            var cur = {x:0, y:0};
                            effects.makeShotAnimation(self.sPos, tPos, self.weapon, true, cur);
                            
                            effects.animateIntercept(fire, self.weapon, tPos, cur, self.sPos, true, true);
                            
                            self.cMiss++;
                            self.cIntercepted++;
                        }else if (self.cHit < self.hit){
                            
                            var bPos = jQuery.extend({}, tPos);
                            var cur = {x:0, y:0}
                            effects.makeShotAnimation(self.sPos, bPos, self.weapon, true, cur);
                            self.cHit++;
                            effects.animateIntercept(fire, self.weapon, tPos, cur, self.sPos, false, true);

                        }else{
                                                    
                            tPos.x = tPos.x + self.step.x*(self.tics - self.startedtic)*gamedata.zoom;
                            tPos.y = tPos.y + self.step.y*(self.tics - self.startedtic)*gamedata.zoom;
                            tPos = mathlib.getPointInDistanceBetween(self.sPos, tPos, mathlib.getDistance(self.sPos, tPos)+(((Math.random()*101)+200)*gamedata.zoom));
                            var cur = {x:0, y:0}
                            effects.makeShotAnimation(self.sPos, tPos, self.weapon, false, cur);
                            self.cMiss++;
                            effects.animateIntercept(fire, self.weapon, tPos, cur, self.sPos, false, false);

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
    
    animateIntercept: function(fire, weapon, tPos, currentlocation, shotPos, succesfull, targethit){
        if (weapon.animation == "laser")
            return;
        
        var intercepts = weaponManager.getInterceptingFiringOrders(fire.id);
        
        var unUsedIntercepts = Array();
        
        for (var i in intercepts){
            var inter = intercepts[i];
            if (!inter.used || inter.used < inter.shots){
                inter.used = 0;
                unUsedIntercepts.push(inter);
            }
        }
        
        if (unUsedIntercepts.length === 0)
            unUsedIntercepts = intercepts;
        var chosens = Array();
        
        if (succesfull){
            
            chosens.push(unUsedIntercepts[Math.floor(Math.random()*unUsedIntercepts.length)]);
         
        }else{

            chosens = unUsedIntercepts;
        }
            
        
        for (var i in chosens){
            var chosen = chosens[i];
            var shots = 1;
            
            if (!succesfull){
                shots = chosen.shots - chosen.used;
                chosen.used = chosen.shots;
            }else{
                chosen.used++;
            }
            
            
            
            var shooter = gamedata.getShip(chosen.shooterid);
            var InterWeapon = shipManager.systems.getSystem(shooter, chosen.weaponid);
            InterWeapon = shipManager.systems.initializeSystem(InterWeapon);
            
            var sPos = effects.getWeaponLocation(shooter, InterWeapon);
            
            for (var a = 0; a<shots;a++){
                
                var tsPos = {x:sPos.x, y:sPos.y};
                var finPos = {x:tPos.x, y:tPos.y};
                if (succesfull){
                    
                    
                
            
                }else{
                    finPos = {x:0, y:0};
                    var iDistance = ((Math.random()*30)+100)*gamedata.zoom;
                    
                    if (mathlib.getDistance(shotPos, sPos)<iDistance){
                        finPos = mathlib.getPointBetween(sPos, shotPos, 0.5);
                    
                    }else{
                        finPos = mathlib.getPointInDistanceBetween(sPos, shotPos, iDistance);
                    }
                    finPos = {x:finPos.x+(Math.round((Math.random()*20)-10)*gamedata.zoom), y:finPos.y+(Math.round((Math.random()*20)-10)*gamedata.zoom)};
                    
                }
                
                var animation = {
                    tics:0,
                    totalTics:5000,
                    weapon:weapon,
                    interWeapon:InterWeapon,
                    cur: currentlocation,
                    tPos: finPos,
                    sPos: tsPos,
                    succesfull: succesfull,
                    draw: function(self){
                        
                        var shottime = Math.ceil(mathlib.getDistance(self.cur, self.tPos)/(self.weapon.projectilespeed*gamedata.zoom));
                        var intertime = Math.ceil(mathlib.getDistance(self.sPos, self.tPos)/(self.interWeapon.projectilespeed*gamedata.zoom));
                                        
                        if (!self.succesfull){
                            intertime += (Math.round((Math.random()*20)-10));
                        }
                        /*
                        var canvas = effects.getCanvas();
                        graphics.drawCircleNoStroke(canvas, self.tPos.x, self.tPos.y, 5, 0);
                        
                        
                        graphics.drawCircleNoStroke(canvas, self.sPos.x, self.sPos.y, 5, 0);
                        */
                         
                        
                        if(shottime <= intertime){
                            //self.interWeapon.animation = "laser";
                            if (self.interWeapon.animation == "laser" && self.succesfull){
                                
                                effects.makeShotAnimation(self.sPos, self.cur, self.interWeapon, false);
                            }else{
                                effects.makeShotAnimation(self.sPos, self.tPos, self.interWeapon, false);
                            }
                            
                                                    
                            self.totalTics = self.tics;
                            return;
                        }
                        
                        self.tics++;
                    },
                    callback: effects.doneDisplayingWeaponFire
                };
            
                
                effects.backAnimations.push(animation);
            }
        }
        
    },
    
    makeBeamAnimation: function(sPos, tPos, weapon, hit, currentlocation){
         
        var tTics = Math.ceil(mathlib.getDistance(sPos, tPos)/(weapon.projectilespeed*gamedata.zoom))+20;
        if (hit)
            tTics = 5000;
        var animation = {
            tics:0,
            totalTics:tTics,
            weapon:weapon,
            sPos: sPos,
            tPos: tPos,
            distance: mathlib.getDistance(tPos, sPos),
            hit: hit,
            draw: function(self){
                
                var canvas = effects.getCanvas();
                var sPos = self.sPos;
                var tPos = self.tPos;
                var cur;
                var distanceTraveled = self.tics*weapon.projectilespeed*gamedata.zoom;
                
                if (distanceTraveled >= self.distance)
                    cur = tPos;
                else
                    cur = mathlib.getPointInDistanceBetween(sPos, tPos, distanceTraveled);
                
                if (currentlocation){
                    currentlocation.x = cur.x;
                    currentlocation.y = cur.y;
                }
                
                
                var trailLength = weapon.trailLength*gamedata.zoom;
                if (mathlib.getDistance(cur, sPos)<trailLength){
                    trailLength = mathlib.getDistance(cur, sPos);
                }
                
                var trailPos;
                if (cur.x == tPos.x && cur.y == tPos.y){
                    trailPos = mathlib.getPointInDistanceBetween(mathlib.getPointInDistanceBetween(sPos, tPos, distanceTraveled), cur, trailLength);
                }else{
                    trailPos = mathlib.getPointInDistanceBetween(cur, sPos, trailLength);
                }
                
                
                var a = getAlpha();
                
            
                var c = self.weapon.animationColor;
                //graphics.drawCircleNoStroke(canvas, cur.x, cur.y, 5, 0);
                //graphics.drawCircleNoStroke(canvas, sPos.x, sPos.y, 5, 0);
                //graphics.drawCircleNoStroke(canvas, trailPos.x, trailPos.y, 5, 0);
                if (cur.x == tPos.x && cur.y == tPos.y){
                    if (self.hit){
                        if (!self.expodone){
                        effects.addExplosion(cur, weapon);
                        self.expodone = true;
                        }
                        
                        if (Math.random()<0.1)
                            effects.addExplosion(cur, weapon);
                     }
                                  
                }
                
                if (mathlib.isOver(sPos,tPos,trailPos)){
                    self.totalTics = self.tics;
                    return;
                }
                
         //       canvas.fillStyle = "rgba("+c[0]+","+c[1]+","+c[2]+",0.02)";
         //       graphics.drawCircleNoStroke(canvas, cur.x, cur.y, 5*gamedata.zoom, 0);
                
                
                c = self.weapon.trailColor;
                canvas.lineCap = "round";
                for (var i = self.weapon.animationWidth; i>=1; i--){
                
                    if (i==1){
                        canvas.strokeStyle = "rgba("+c[0]+","+c[1]+","+c[2]+","+0.7*a+")";
                        graphics.drawLine(canvas, trailPos.x, trailPos.y, cur.x, cur.y, i);
                        
                    }else{
                        canvas.strokeStyle = "rgba("+c[0]+","+c[1]+","+c[2]+","+0.06*a+")";
                        graphics.drawLine(canvas, trailPos.x, trailPos.y, cur.x, cur.y, i);
                        
                    }
                    
                    
                }
                canvas.lineCap = "butt";                
                
                self.tics++;
                
                function getAlpha(){
                    var a = 0.0;
                    if (self.tics < 10){
                        a = (0.1*self.tics);
                    }else if (self.tics > (self.totalTics - 20) && !hit){
                        var t = self.tics - (self.totalTics - 20);
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
    
    makeLaserAnimation: function(sPos, tPos, weapon, hit, currentlocation){
        
        
                var step ={x:Math.random()*0.5-0.25, y:Math.random()*0.5-0.25};
        
     //   var step = if (self.weapon.step = 0) 
      //  {x = 0, y = 0};
    //  else {x:Math.random()*0.5-0.25, y:Math.random()*0.5-0.25};
                
        var animation = {
            tics:0,
            totalTics:50,
            weapon:weapon,
            hit:hit,
            sPos: sPos,
            tPos: tPos,
            step:step,
            explo:false,
            draw: function(self){
            
                var canvas = effects.getCanvas();
                
                var sPos = self.sPos;
                var tPos = self.tPos;
                var c = self.weapon.animationColor;
                var b = self.weapon.animationColor2;
                var a = getAlpha();
                
                tPos.x += step.x*gamedata.zoom;
                tPos.y += step.y*gamedata.zoom;
                
                if (self.tics == 20 && self.hit && !self.explo)
                    effects.addExplosion(tPos, weapon);
                    
                if (self.hit && Math.random()>0.90)
                    effects.addExplosion(tPos, weapon);
                    
                canvas.lineCap = "round";
                for (var i = self.weapon.animationWidth; i>=1; i--){
                    if (i == 1){
                        canvas.strokeStyle = "rgba("+c[0]+","+c[1]+","+c[2]+","+a*4+")";
                        graphics.drawLine(canvas, sPos.x, sPos.y, tPos.x, tPos.y, 1);
                        break;
                    }
                    canvas.strokeStyle = "rgba("+c[0]+","+c[1]+","+c[2]+","+a+")";
                    graphics.drawLine(canvas, sPos.x, sPos.y, tPos.x, tPos.y, i*gamedata.zoom);
                    canvas.strokeStyle = "rgba("+b[0]+","+b[1]+","+b[2]+","+8*a+")";
                    graphics.drawLine(canvas, sPos.x, sPos.y, tPos.x, tPos.y, i*gamedata.zoom*self.weapon.animationWidth2);


                }
                
                for (var i = Math.round(self.weapon.animationWidth*1.5*gamedata.zoom); i>=1; i--){
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
    
    
    makeTrailAnimation: function(sPos, tPos, weapon, hit, currentlocation){
        //console.log(weapon);
         
        var tTics = Math.ceil(mathlib.getDistance(sPos, tPos)/(weapon.projectilespeed*gamedata.zoom))+20;
        if (hit)
            tTics = 5000;
        var animation = {
            tics:0,
            totalTics:tTics,
            weapon:weapon,
            sPos: sPos,
            tPos: tPos,
            hit: hit,
            distance: mathlib.getDistance(tPos, sPos),
            draw: function(self){
            
                var canvas = effects.getCanvas();
                var sPos = self.sPos;
                var tPos = self.tPos;
                var cur;
                var distanceTraveled = self.tics*weapon.projectilespeed*gamedata.zoom;
                
                if (distanceTraveled >= self.distance)
                    cur = tPos;
                else
                    cur = mathlib.getPointInDistanceBetween(sPos, tPos, distanceTraveled);
                
                if (currentlocation){
                    currentlocation.x = cur.x;
                    currentlocation.y = cur.y;
                }
                
                
                var trailLength = weapon.trailLength*gamedata.zoom;
                if (mathlib.getDistance(cur, sPos)<trailLength){
                    trailLength = mathlib.getDistance(cur, sPos);
                }
                
                var trailPos;
                if (cur.x == tPos.x && cur.y == tPos.y){
                    trailPos = mathlib.getPointInDistanceBetween(mathlib.getPointInDistanceBetween(sPos, tPos, distanceTraveled), cur, trailLength);
                }else{
                    trailPos = mathlib.getPointInDistanceBetween(cur, sPos, trailLength);
                }
                
                
                var a = getAlpha();
                
            
                var c = self.weapon.animationColor;
                //graphics.drawCircleNoStroke(canvas, cur.x, cur.y, 5, 0);
                //graphics.drawCircleNoStroke(canvas, sPos.x, sPos.y, 5, 0);
                //graphics.drawCircleNoStroke(canvas, trailPos.x, trailPos.y, 5, 0);
                if (cur.x == tPos.x && cur.y == tPos.y && !self.expodone){
                    if (self.hit){
                        effects.addExplosion(cur, weapon);
                        self.expodone = true;
                     }
                                  
                }
                if (mathlib.isOver(sPos,tPos,trailPos)){
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
                    }else if (self.tics > (self.totalTics - 20)){
                        var t = self.tics - (self.totalTics - 20);
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
    
    
    makeBallAnimation: function(sPos, tPos, weapon, hit, currentlocation){
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
                
                if (currentlocation){
                    currentlocation.x = cur.x;
                    currentlocation.y = cur.y;
                }
                                
                var c = self.weapon.animationColor;
                                                
                if (mathlib.isOver(sPos,tPos,cur)){
                    if (self.hit){
                        effects.addExplosion(tPos, weapon);
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
    
    
    makeTorpedoAnimation: function(sPos, tPos, weapon, hit, currentlocation){        
        var cones = Array();
        var conecount = Math.floor((Math.random()*3)+6);
        //var conecount = 0;
        var startAngle = Math.floor((Math.random()*360));
        var angleMod = ((Math.random()*1)+1);
        
        for (var i=0;i<conecount;i++){
            var startAngle = mathlib.addToDirection(startAngle, 30*(i+1) + Math.floor((Math.random()*10)));
            
            var coneLength = Math.floor((Math.random()*weapon.animationWidth)+weapon.animationWidth);
            angleMod += (Math.random());
            var angleWidth = Math.floor((Math.random()*3)+2);
            cones.push({angle:startAngle*gamedata.zoom, mod:angleMod*gamedata.zoom, len:coneLength*gamedata.zoom, width:angleWidth*gamedata.zoom});
        }      
              
        var animation = {
            tics:0,
            totalTics:500,
            weapon:weapon,
            sPos: sPos,
            tPos: tPos,
            hit: hit,
            cones: cones,
            draw: function(self){
            
                var canvas = effects.getCanvas();
                var sPos = self.sPos;
                var tPos = self.tPos;
                
                
                
                var cur = mathlib.getPointInDistanceBetween(sPos, tPos, self.tics*weapon.projectilespeed*gamedata.zoom);
                
                if (currentlocation){
                    currentlocation.x = cur.x;
                    currentlocation.y = cur.y;
                }
                                
                var c = self.weapon.animationColor;
                var t = self.weapon.trailColor;          
                                     
                if (mathlib.isOver(sPos,tPos,cur)){
                    if (self.hit){
                        
                        effects.addExplosion(cur, weapon);
                        
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
                    canvas.fillStyle = "rgba("+c[0]+","+c[1]+","+c[2]+",0.02)";
                    graphics.drawCircleNoStroke(canvas, cur.x, cur.y, i*gamedata.zoom, 0);
                    canvas.fillStyle = "rgba(255,255,255,0.6)";
                    graphics.drawCircleNoStroke(canvas, cur.x, cur.y, i*0.1, 0);
                  //  graphics.drawLine(canvas, sPos.x, sPos.y, cur.x, cur.y, i*gamedata.zoom);
                    
                    
                }
                canvas.strokeStyle = "rgba("+t[0]+","+t[1]+","+t[2]+",0.05)";
                canvas.fillStyle = "rgba("+t[0]+","+t[1]+","+t[2]+",0.05)";
                    
                for (var i in self.cones){
                    var cone = self.cones[i];
                    cone.angle = mathlib.addToDirection(cone.angle, cone.mod);
                    
                    
                    var arcs ={
                        start: cone.angle,
                        end: mathlib.addToDirection(cone.angle, cone.width)
                    };
                    
                    var arcs2 ={
                        start: mathlib.addToDirection(arcs.start, 180),
                        end: mathlib.addToDirection(arcs.end, 180)
                    };
                    
                    var l = 5;
                    while(l>0){
                        var p1 = mathlib.getPointInDirection(cone.len/l, arcs.start, cur.x, cur.y);
                        var p2 = mathlib.getPointInDirection(cone.len/l, arcs.end, cur.x, cur.y);
                                        
                        graphics.drawCone(canvas, cur, p1, p2, arcs, 0);
                        
                        p1 = mathlib.getPointInDirection(cone.len/l, arcs2.start, cur.x, cur.y);
                        p2 = mathlib.getPointInDirection(cone.len/l, arcs2.end, cur.x, cur.y);
                                        
                        graphics.drawCone(canvas, cur, p1, p2, arcs2, 0);
                        
                        l--;
                    }
                    /*
                    
                    p1 = mathlib.getPointInDirection(cone.len*0.80, arcs.start, cur.x, cur.y);
                    p2 = mathlib.getPointInDirection(cone.len*0.80, arcs.end, cur.x, cur.y);
                                        
                    graphics.drawCone(canvas, cur, p1, p2, arcs, 0);
                    
                    p1 = mathlib.getPointInDirection(cone.len*0.60, arcs.start, cur.x, cur.y);
                    p2 = mathlib.getPointInDirection(cone.len*0.60, arcs.end, cur.x, cur.y);
                                        
                    graphics.drawCone(canvas, cur, p1, p2, arcs, 0);
                    
                    graphics.drawCone(canvas, cur, p1, p2, arcs, 0);
                    
                    p1 = mathlib.getPointInDirection(cone.len*0.40, arcs.start, cur.x, cur.y);
                    p2 = mathlib.getPointInDirection(cone.len*0.40, arcs.end, cur.x, cur.y);
                                        
                    graphics.drawCone(canvas, cur, p1, p2, arcs, 0);
                    
                    p1 = mathlib.getPointInDirection(cone.len*0.20, arcs.start, cur.x, cur.y);
                    p2 = mathlib.getPointInDirection(cone.len*0.20, arcs.end, cur.x, cur.y);
                                        
                    graphics.drawCone(canvas, cur, p1, p2, arcs, 0);
                    */
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
        
    getWeaponLocation: function(ship, weapon){
        var a = shipManager.getShipHeadingAngle(ship);
        var shippos = shipManager.getShipPositionInWindowCo(ship);
        
        if (ship.flight){
            
            var fighter = shipManager.systems.getFighterBySystem(ship, weapon.id);
            var offset = shipManager.getFighterPosition(fighter.location, 0, 1);
        
            shippos.x += offset.x;
            shippos.y += offset.y;
            
            return shippos;
        }
        
        
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





















