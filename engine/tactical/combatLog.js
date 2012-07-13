window.combatLog = {

    constructLog: function(){
    
        for (var i in gamedata.ships){
            var ship = gamedata.ships[i];
            
            var fires = weaponManager.getAllFireOrders(ship);
            for (var a in fires){
                var fire = fires[a];
                var target = gamedata.getShip(fire.targetid);
                var weapon = shipManager.systems.getSystem(ship, fire.weaponid);
                var damages = weaponManager.getDamagesCausedBy(fire);
                
                var html = '<div class="logentry"><span class="fire">FIRE: </span><span>';
                html += '<span class="shiplink" data-id="'+ship.id+'" >' + ship.name + '</span>';
                
                if (fire.rolled <= fire.needed){
                    html += ' hitting ';
                }else{
                    html += ' missing ';
                }
                
                html += '<span class="shiplink" data-id="'+target.id+'" >' + target.name + '</span>';
                html += ' with ' + weapon.displayName + ' (rolled: '+fire.rolled+'/'+fire.needed+')';
                html += '<span class="notes"> '+fire.notes+'</span>';
                for (var b in damages){
                    var d = damages[b];
                    var des = "";
                    if (d.destroyed)
                        des = " DESTROYED";
                        
                    if (d.damage-d.armour<0)
                        continue;
                    
                    var system = shipManager.systems.getSystem(target, d.systemid);
                    
                    html += '<span class="damage"> '+shipManager.systems.getDisplayName(system)+des+ ' '+(d.damage-d.armour)+'(A:'+d.armour+') </span>'
                    
                }
                html+='</span></div>';
                
                $(html).prependTo("#log");
                
            
            }
        
        }
    
    },
    
    logDestroyedShip: function(ship){
    
        var html = '<div class="logentry"><span class="destroyed">';
            html += '<span class="shiplink" data-id="'+ship.id+'" >' + ship.name.toUpperCase() + '</span> DESTROYED</span>';
            
        $(html).prependTo("#log");
    },
    
    logFireOrdersOld: function(orders){

        for (var a in orders){
            var fire = orders[a];
            var ship = gamedata.getShip(fire.shooterid);
            var target = gamedata.getShip(fire.targetid);
            var weapon = shipManager.systems.getSystem(ship, fire.weaponid);
            var damages = weaponManager.getDamagesCausedBy(fire);
            
            var html = '<div class="logentry"><span class="fire">FIRE: </span><span>';
            html += '<span class="shiplink" data-id="'+ship.id+'" >' + ship.name + '</span>';
            
            if (fire.rolled <= fire.needed){
                html += ' hitting ';
            }else{
                html += ' missing ';
            }
            
            var damagehtml = "";
            var totaldam = 0;
            for (var b in damages){
                var d = damages[b];
                if (d.damage-d.armour<=0)
                    continue;
                    
                totaldam += d.damage-d.armour;
                var system = shipManager.systems.getSystem(gamedata.getShip(d.shipid), d.systemid); 
                var des = "";
                if (d.destroyed){
                    des = " DESTROYED";
                    
                }else{
                    continue;
                }
                                
                
                
                
                
                damagehtml += '<span class="damage"> '+shipManager.systems.getDisplayName(system)+des+ '</span>'
                
            }
            
            
            
            if (target)
                html += '<span class="shiplink" data-id="'+target.id+'" >' + target.name + '</span>';
            html += ' with ' + weapon.displayName + ' (chance: '+fire.needed+'%, '+fire.shotshit+'/'+fire.shots+' shots hit)  Total damage: ' + totaldam;
            html += '<span class="notes"> '+fire.notes+'</span>';
            html += damagehtml;
            html+='</span></div>';
            
            if (totaldam == 0)
                continue;
                
            $(html).prependTo("#log");
            
        
        }
        
        // FIRE: Who, number, weapon at (target, location), hit change, shots hit/fired, (intercepted), pub notes
        // * DAMAGED: What, how much, destroyed
    },
    
    logFireOrders: function(orders){
        //fire.x != "null" && otherFire.x == fire.x && fire.y != "null"
        var count = 0;
        var ship = gamedata.getShip(orders[0].shooterid);
        var target = gamedata.getShip(orders[0].targetid);
        var shots = 0;
        var shotshit = 0;
        var shotsintercepted = 0;
        var damages = Array();
        var lowC = 100000;
        var highC = 0;
        var notes = "";
        
        for (var a in orders){
            
                        
            count++;
            var fire = orders[a];
            
            var weapon = shipManager.systems.getSystem(ship, fire.weaponid);
            shots += fire.shots;
            shotshit += fire.shotshit;
            shotsintercepted += fire.intercepted;
            weaponManager.getDamagesCausedBy(damages, fire);
            var needed = fire.needed;
            if (needed < 0)
				needed = 0;
				
            if (needed < lowC)
                lowC = needed;
            if (needed >highC)
                highC = needed;
                
            if (fire.pubnotes)
                notes += fire.pubnotes + " ";
                        
        }
            
        
        var html = '<div class="logentry"><span class="fire">FIRE: </span><span>';
            html += '<span class="shiplink" data-id="'+ship.id+'" >' + ship.name + '</span>';   
            
            var counttext = (count>1) ? count+"x " : "";
            var chancetext = "";
            if (lowC == highC)
                chancetext = "Chance to hit: " + lowC + "%";
            else
                chancetext = "Chance to hit: " + lowC + "% - " +highC+"%";
                
            if (!target)
                chancetext = "";
                
            var intertext = "";
            if (shotsintercepted>0)
                intertext = ", " +shotsintercepted + " intercepted"
                
            var targettext = "";
            if (target)
                targettext = '<span> at </span><span class="shiplink target" data-id="'+target.id+'" >' + target.name + '</span>';
            
            var shottext = "";
            if (target)
                shottext = ', '+shotshit+'/'+shots+' shots hit'+intertext+'.';
                
            var notestext = "";
            if (notes)
                notestext = '<span class="notes">'+notes+'</span>';
            
            html += ' firing ' +counttext + weapon.displayName + targettext+'. '+chancetext +shottext + notestext;
        
            
        
                    
    
        
        
        html += '<span class="notes"> '+fire.notes+'</span>';
    //  html += damagehtml;
        html+='</span></div>';
        
        if (damages.length > 0){
            html += "<ul>";
           
            for (var i in damages){
                var victim = damages[i].ship;
                var totaldam = 0; 
                var damagehtml = "";
                for (var a in damages[i].damages){
                    var d = damages[i].damages[a];
                    if (d.damage-d.armour<=0)
                        continue;
                        
                    totaldam += d.damage-d.armour;
                    var system = shipManager.systems.getSystem(gamedata.getShip(d.shipid), d.systemid); 
                    
                    if (!d.destroyed){
                        continue;
                    }
                    
                    var first = "";
                    var comma = ",";
                    if (damagehtml.length == 0){
                        first = " Systems destroyed: "
                        comma = "";
                    }
                    
                    damagehtml += first + '<span class="damage">'+comma+' '+shipManager.systems.getDisplayName(system)+'</span>'
                    
                }
                
                if (totaldam > 0){
                    html += '<li><span class="shiplink victim" data-id="'+ship.id+'" >' + victim.name + '</span> damaged for '+totaldam +'. '+ damagehtml+'</li>';   
                }
                
            }
            
            html += "</ul>";
        }
        
            
        $(html).prependTo("#log");
            
        
        
        
        // FIRE: Who, number, weapon at (target, location), hit change, shots hit/fired, (intercepted), pub notes
        // * DAMAGED: What, how much, destroyed
    }


}
