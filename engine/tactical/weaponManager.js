window.weaponManager = {

    
    onHoldfireClicked: function(e){
        e.stopPropagation();
        var shipwindow = $(".shipwindow").has($(this));
        var systemwindow = $(".system").has($(this));
        var ship = gamedata.getShip(shipwindow.data("ship"));
        var system = ship.systems[systemwindow.data("id")];

        if (gamedata.gamephase != 3 && !system.ballistic)
            return;
            
        if (gamedata.gamephase != 1 && system.ballistic)
            return;
        
        if (ship.userid == gamedata.thisplayer){
            weaponManager.cancelFire(ship, system);
            
        }
        
    },
    
    cancelFire: function(ship, system){
		weaponManager.removeFiringOrder(ship, system);
		ballistics.updateList();
        shipWindowManager.setDataForSystem(ship, system);
	},    
    
    mouseoverTimer: null,
    mouseoverSystem: null,
    
    onWeaponMouseover: function(e){
        if (weaponManager.mouseoverTimer != null)
            return;

        weaponManager.mouseoverSystem = $(this);
        weaponManager.mouseoverTimer = setTimeout(weaponManager.doWeaponMouseOver, 300);
    },
    
    doWeaponMouseOver: function(e){
        if (weaponManager.mouseoverTimer != null){
            clearTimeout(weaponManager.mouseoverTimer); 
            weaponManager.mouseoverTimer = null;
        }

        var t = weaponManager.mouseoverSystem;
        
        var ship = gamedata.getShip(t.data("shipid"));
        var system = null;
        
        if (t.hasClass("fightersystem")){
			system = shipManager.systems.getFighterSystem(ship, t.data("fighterid"), t.data("id"));
		}else{
			system = shipManager.systems.getSystem(ship, t.data("id"));
		}
        
    
        weaponManager.addArcIndicators(ship, system);
        systemInfo.showSystemInfo(t, system, ship);
        drawEntities();
    },
    
    onWeaponMouseout: function(e){
        if (weaponManager.mouseoverTimer != null){
            clearTimeout(weaponManager.mouseoverTimer); 
            weaponManager.mouseoverTimer = null;
        }
        
        systemInfo.hideSystemInfo(t, system, ship);
        
        weaponManager.mouseoverSystem = null;
        
        var t = $(this);        
        var ship = gamedata.getShip(t.data("shipid"));
        var system = shipManager.systems.getSystem(ship, t.data("id"));
        
        weaponManager.removeArcIndicators(ship);
        drawEntities();
        
    },
    
    unSelectWeapon: function(ship, weapon){
    
        for(var i = gamedata.selectedSystems.length-1; i >= 0; i--){  
            if(gamedata.selectedSystems[i] == weapon){              
                gamedata.selectedSystems.splice(i,1);
                
            }
        }
        
        shipWindowManager.setDataForSystem(ship, weapon);

    },
    
    selectWeapon: function(ship, weapon){

        if (!weaponManager.isLoaded(weapon))
            return;
    
        gamedata.selectedSystems.push(weapon);
        shipWindowManager.setDataForSystem(ship, weapon);
        
    },
    
    isSelectedWeapon: function(weapon){
        if ($.inArray(weapon, gamedata.selectedSystems) >= 0)
            return true;
            
        return false;
    },
    
    targetingShipTooltip: function(ship, e){
        //e.find(".shipname").html(ship.name);
        var selectedShip = gamedata.getSelectedShip();
        var f = $(".targeting", e);

        f.html("");
        for (var i in gamedata.selectedSystems){
            var weapon = gamedata.selectedSystems[i];
            
            if (weaponManager.isOnWeaponArc(selectedShip, ship, weapon)){
                $('<div><span class="weapon">'+weapon.displayName+':</span><span class="hitchange"> '+weaponManager.calculateHitChange(selectedShip, ship, weapon)+'%</span></div>').appendTo(f);
                //<span class="hitinfo">'+weaponManager.calculateHitInfo(selectedShip, ship, weapon)+'</span></div>').appendTo(f);
            }else{
                $('<div><span class="weapon">'+weapon.displayName+':</span><span class="notInArc"> NOT IN ARC </span></div>').appendTo(f);
            }
        }
        
    },
    
    isPosOnWeaponArc: function(shooter, position, weapon){

        var shooterFacing = (shipManager.getShipHeadingAngle(shooter));
        var targetCompassHeading = mathlib.getCompassHeadingOfPosition(shooter, position);
        
        var arcs = shipManager.systems.getArcs(shooter, weapon);
        arcs.start = mathlib.addToDirection(arcs.start, shooterFacing);
        arcs.end = mathlib.addToDirection(arcs.end, shooterFacing);
        
        //console.log("shooterFacing: " + shooterFacing + " targetCompassHeading: " +targetCompassHeading);
        
        return (mathlib.isInArc(targetCompassHeading, arcs.start, arcs.end));
        
    
    },
    
    isOnWeaponArc: function(shooter, target, weapon){

        var shooterFacing = (shipManager.getShipHeadingAngle(shooter));
        var targetCompassHeading = mathlib.getCompassHeadingOfShip(shooter, target);
        
        var arcs = shipManager.systems.getArcs(shooter, weapon);
        arcs.start = mathlib.addToDirection(arcs.start, shooterFacing);
        arcs.end = mathlib.addToDirection(arcs.end, shooterFacing);
        var oPos = shipManager.getShipPosition(shooter);
        var tPos = shipManager.getShipPosition(target);
        
        if (weapon.ballistic && oPos.x == tPos.x && oPos.y == tPos.y)
            return true;
        
        //console.log("shooterFacing: " + shooterFacing + " targetCompassHeading: " +targetCompassHeading);
        
        return (mathlib.isInArc(targetCompassHeading, arcs.start, arcs.end));
        
    
    },
    
    calculateRangePenalty: function(shooter, target, weapon){
        var sPos = shipManager.getShipPositionInWindowCo(shooter);
        var tPos = shipManager.getShipPositionInWindowCo(target);
        var dis = mathlib.getDistance(sPos, tPos);
        var disInHex = dis / hexgrid.hexWidth();
        var rangePenalty = (weapon.rangePenalty/hexgrid.hexWidth()*dis);
    
        return rangePenalty;
    },
    
    calculataBallisticHitChange: function(ball){
        if (!ball.targetid)
            return false;
        
        var shooter = gamedata.getShip(ball.shooterid);
        var weapon = shipManager.systems.getSystem(shooter, ball.weaponid);
        var target = gamedata.getShip(ball.targetid);
        
        var rangePenalty = weaponManager.calculateRangePenalty(shooter, target, weapon);
        
        var dew = ew.getDefensiveEW(target);
        var oew = ew.getOffensiveEW(shooter, target);
        
        var defence = weaponManager.getShipDefenceValuePos(ball.position, target);
        
        var firecontrol =  weaponManager.getFireControl(target, weapon);
        
        var intercept = weaponManager.getInterception(ball);
        
        var mod = 0;
        if (!shooter.flight)
			mod -= shipManager.criticals.hasCritical(shipManager.systems.getSystemByName(shooter, "CnC"), "PenaltyToHit");
        
        var goal = (defence - dew - rangePenalty - intercept + oew + firecontrol + mod);
        
        var change = Math.round((goal/20)*100);
        console.log("rangePenalty: " + rangePenalty + "intercept: " + intercept + " dew: " + dew + " oew: " + oew + " defence: " + defence + " firecontrol: " + firecontrol + " mod: " +mod+ " goal: " +goal);
        
        //if (change > 100)
        //  change = 100;
        return change;
        
    },
    
    getInterception: function(ball){
        
        var intercept = 0;
        
        for (var i in gamedata.ships){
            for (var a in gamedata.ships[i].fireOrders){
                var fire = gamedata.ships[i].fireOrders[a];
                if (fire.type == "intercept" && fire.targetid == ball.fireOrderId){
                    var ship = gamedata.getShip(fire.shooterid);
                    var weapon = shipManager.systems.getSystem(ship, fire.weaponid);
                    intercept += weapon.intercept;
                }
            }
            
            
        }
        
        return intercept;
        
    },
    
    calculateHitChange: function(shooter, target, weapon){
    
        var rangePenalty = weaponManager.calculateRangePenalty(shooter, target, weapon);
        
        //console.log("dis: " + dis + " disInHex: " + disInHex + " rangePenalty: " + rangePenalty);
        
        var dew = ew.getDefensiveEW(target);
        if (shooter.flight)
			dew = 0;
			
        var oew = ew.getOffensiveEW(shooter, target);
        
        if (shooter.flight)
			oew = shooter.offensivebonus;
        
        var mod = 0;
        
        if (shipManager.movement.isRolling(shooter)){
            console.log("rolling");
            mod -= 3;
        }
        
        if (shipManager.movement.isPivoting(shooter) != "no"){
            console.log("pivoting");
            mod -= 3;
        }
        if (!shooter.flight)
			mod -= shipManager.criticals.hasCritical(shipManager.systems.getSystemByName(shooter, "CnC"), "PenaltyToHit");
        
        if (oew == 0)
            rangePenalty = rangePenalty*2;
            
        var defence = weaponManager.getShipDefenceValue(shooter, target);
        
        var firecontrol =  weaponManager.getFireControl(target, weapon);
            
        
        var goal = (defence - dew - rangePenalty + oew + firecontrol + mod);
        
        var change = Math.round((goal/20)*100);
        console.log("rangePenalty: " + rangePenalty + " dew: " + dew + " oew: " + oew + " defence: " + defence + " firecontrol: " + firecontrol + " mod: " +mod+ " goal: " +goal);
        
        if (change > 100)
            change = 100;
        return change;
        
    
    },
    
    calculateHitInfo: function(shooter, target, weapon){
    
        var rangePenalty = weaponManager.calculateRangePenalty(shooter, target, weapon);
        
        //console.log("dis: " + dis + " disInHex: " + disInHex + " rangePenalty: " + rangePenalty);
        
        var dew = ew.getDefensiveEW(target);
        var oew = ew.getOffensiveEW(shooter, target);
        var mod = 0;
        
        if (shipManager.movement.isRolling(shooter)){
            mod -= 3;
        }
        
        if (shipManager.movement.isPivoting(shooter)){
            mod -= 3;
        }
        
        if (oew == 0)
            rangePenalty = rangePenalty*2;
            
        var defence = weaponManager.getShipDefenceValue(shooter, target);
        
        var firecontrol =  weaponManager.getFireControl(target, weapon);
            
        
        var goal = (defence - dew - rangePenalty + oew + firecontrol);
        
        var change = Math.round((goal/20)*100);
        //console.log("rangePenalty: " + rangePenalty + " dew: " + dew + " oew: " + oew + " defence: " + defence + " firecontrol: " + firecontrol + " goal: " +goal);
        
        var text = "Base: "+defence+" - DEW " + dew + " - range " + Math.round(rangePenalty) + " + OEW " + oew + " + F/C " + firecontrol +"+ other: "+mod +" = "+ Math.round(goal) ;
        
        return text;
        
    
    },
    
    getFireControl: function(target, weapon){
    
        if (target.shipSizeClass > 1){
            return weapon.fireControl[2];
        }
        if (target.shipSizeClass >= 0){
            return weapon.fireControl[1];
        }
        
        return weapon.fireControl[0];
        
    
    },
    
    getShipDefenceValuePos: function(position, target){
        var targetFacing = (shipManager.getShipHeadingAngle(target));
        var targetPos = shipManager.getShipPosition(target);
                
        var shooterCompassHeading = mathlib.getCompassHeadingOfPosition(target, position);
        
    
        
        //console.log("getShipDefenceValue targetFacing: " + targetFacing + " shooterCompassHeading: " +shooterCompassHeading);
        
        //console.log("ship degree: " +delta); 
        if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(330, targetFacing), mathlib.addToDirection(30, targetFacing) )){
            //console.log("hitting front 1");
            return target.forwardDefense;
        }else if ( mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(150, targetFacing), mathlib.addToDirection(210, targetFacing) )){
            //console.log("hitting rear 2");
            return target.forwardDefense;
        }else if ( mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(210, targetFacing), mathlib.addToDirection(330, targetFacing) )){
            //console.log("hitting port 3");
            return target.sideDefense;
        }else if ( mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(30, targetFacing), mathlib.addToDirection(150, targetFacing) )){
            //console.log("hitting starboard 4");
            return target.sideDefense;
        }
            
        return target.sideDefense;
        
    },
    
    getShipDefenceValue: function(shooter, target){
        var targetFacing = (shipManager.getShipHeadingAngle(target));
        var shooterCompassHeading = mathlib.getCompassHeadingOfShip(target,shooter);
        
    
        
        //console.log("getShipDefenceValue targetFacing: " + targetFacing + " shooterCompassHeading: " +shooterCompassHeading);
        
        //console.log("ship degree: " +delta); 
        if (mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(330, targetFacing), mathlib.addToDirection(30, targetFacing) )){
            //console.log("hitting front 1");
            return target.forwardDefense;
        }else if ( mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(150, targetFacing), mathlib.addToDirection(210, targetFacing) )){
            //console.log("hitting rear 2");
            return target.forwardDefense;
        }else if ( mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(210, targetFacing), mathlib.addToDirection(330, targetFacing) )){
            //console.log("hitting port 3");
            return target.sideDefense;
        }else if ( mathlib.isInArc(shooterCompassHeading, mathlib.addToDirection(30, targetFacing), mathlib.addToDirection(150, targetFacing) )){
            //console.log("hitting starboard 4");
            return target.sideDefense;
        }
            
        return target.sideDefense;
        
    },
    /*
    canIntercept: function(ball){
        var selectedShip = gamedata.getSelectedShip();
        if (shipManager.isDestroyed(selectedShip))
            return false;
            
        if (!ball.targetid || ball.targetid != selectedShip.id)
            return false;
            
        for (var i in gamedata.selectedSystems){
            var weapon = gamedata.selectedSystems[i];
            
            if (shipManager.systems.isDestroyed(selectedShip, weapon) || !weaponManager.isLoaded(weapon))
                continue;
            
            if (weaponManager.isPosOnWeaponArc(selectedShip, ball.position, weapon)){
                return true;
            }
        }
        
        return false;
    },
    */
    targetBallistic: function(ball){
        
         if (gamedata.gamephase != 3)
            return;
            
            
        var selectedShip = gamedata.getSelectedShip();
        if (shipManager.isDestroyed(selectedShip))
            return;
            
        if (!ball.targetid) 
            return;
            
        var target = gamedata.getShip(ball.targetid);
                    
        var toUnselect = Array();
        
        for (var i in gamedata.selectedSystems){
            var weapon = gamedata.selectedSystems[i];
            
            if (ball.targetid != selectedShip.id && !weapon.freeintercept )
                continue;
                
            if (ball.targetid != selectedShip.id && weapon.freeintercept){
                var ballpos = hexgrid.positionToPixel(ball.position);
                var targetpos = shipManager.getShipPositionInWindowCo(target);
                var selectedpos = shipManager.getShipPositionInWindowCo(selectedShip);
                if (mathlib.getDistanceHex(ballpos, targetpos) <= mathlib.getDistanceHex(ballpos, selectedpos) || mathlib.getDistanceHex(targetpos, selectedpos) >3)
                    continue;
            }
            
            if (shipManager.systems.isDestroyed(selectedShip, weapon) || !weaponManager.isLoaded(weapon))
                continue;
                
            if (weapon.intercept == 0)
                continue;
                
            var type = 'intercept';
            
                            
            
            if (weaponManager.isPosOnWeaponArc(selectedShip, ball.position, weapon)){
                
                weaponManager.removeFiringOrder(selectedShip, weapon);
                for (var s=0;s<weapon.guns;s++){
                    selectedShip.fireOrders.push({id:null,type:type, shooterid:selectedShip.id, targetid:ball.fireOrderId, weaponid:weapon.id, calledid:-1, turn:gamedata.turn, firingmode:weapon.firingMode, shots:weapon.defaultShots, x:"null", y:"null"});
                }
                toUnselect.push(weapon);
            }
        }
        
        for (var i in toUnselect){
            weaponManager.unSelectWeapon(selectedShip, toUnselect[i]);
        }
        
        gamedata.shipStatusChanged(selectedShip);
        
    },
    
    targetShip: function(ship){
    
        var selectedShip = gamedata.getSelectedShip();
        if (shipManager.isDestroyed(selectedShip))
            return;
        
        console.log("targetship");
            
        var toUnselect = Array();
        for (var i in gamedata.selectedSystems){
            var weapon = gamedata.selectedSystems[i];
            
            if (shipManager.systems.isDestroyed(selectedShip, weapon) || !weaponManager.isLoaded(weapon))
                continue;
        
            
            if (weapon.ballistic && gamedata.gamephase != 1){
                continue;
            }
            if (!weapon.ballistic && gamedata.gamephase != 3){
                continue;
            }
            
            var type = 'normal';
            if (weapon.ballistic){
                type = 'ballistic';
            }
                
            
            
            
            if (weaponManager.isOnWeaponArc(selectedShip, ship, weapon)){
                //$id, $shooterid, $targetid, $calledid, $turn, $firingmode;
                if (weapon.range == 0 || (mathlib.getDistanceHex(shipManager.getShipPositionInWindowCo(selectedShip), shipManager.getShipPositionInWindowCo(ship))<=weapon.range)){
                    weaponManager.removeFiringOrder(selectedShip, weapon);
                    for (var s=0;s<weapon.guns;s++){
                        
                        var fireid = selectedShip.id+"_"+(selectedShip.fireOrders.length+1);
                        var fire = {id:fireid,type:type, shooterid:selectedShip.id, targetid:ship.id, weaponid:weapon.id, calledid:-1, turn:gamedata.turn, firingmode:weapon.firingMode, shots:weapon.defaultShots, x:"null", y:"null"};
                        selectedShip.fireOrders.push(fire);            
                        
                    }
                    if (weapon.ballistic){
                        gamedata.ballistics.push({id:(gamedata.ballistics.length), fireid:fireid, position:shipManager.getShipPosition(selectedShip),
                        facing:shipManager.movement.getLastCommitedMove(selectedShip).facing,
                        targetposition:{x:null, y:null},
                        targetid:ship.id,
                        shooterid:selectedShip.id,
                        weaponid:weapon.id,
                        shots:fire.shots});
                        
                        ballistics.calculateBallisticLocations();
                        ballistics.calculateDrawBallistics();                        
                        drawEntities();
                        //$id, $fireid, $position, $facing, $targetpos, $targetid, $shooterid, $weaponid, $shots
                    }
                    toUnselect.push(weapon);
                }
            }
        }
        
        for (var i in toUnselect){
            weaponManager.unSelectWeapon(selectedShip, toUnselect[i]);
        }
        
        gamedata.shipStatusChanged(selectedShip);
    
    },
    
    targetHex: function(hexpos){
    
        var selectedShip = gamedata.getSelectedShip();
        if (shipManager.isDestroyed(selectedShip))
            return;
        
        
            
        var toUnselect = Array();
        for (var i in gamedata.selectedSystems){
            var weapon = gamedata.selectedSystems[i];
            
            if (shipManager.systems.isDestroyed(selectedShip, weapon) || !weaponManager.isLoaded(weapon))
                continue;
        
            
            if (weapon.ballistic && gamedata.gamephase != 1){
                continue;
            }
            if (!weapon.ballistic && gamedata.gamephase != 3){
                continue;
            }
            
            if (!weapon.hextarget)
                continue;
            
            var type = 'normal';
            if (weapon.ballistic){
                type = 'ballistic';
            }
                        
            
            if (weaponManager.isPosOnWeaponArc(selectedShip, hexpos, weapon)){
                //$id, $shooterid, $targetid, $calledid, $turn, $firingmode;
                if (weapon.range == 0 || (mathlib.getDistanceHex(shipManager.getShipPositionInWindowCo(selectedShip), hexgrid.positionToPixel(hexpos))<=weapon.range)){
                    weaponManager.removeFiringOrder(selectedShip, weapon);
                    for (var s=0;s<weapon.guns;s++){
                        
                        var fireid = selectedShip.id+"_"+(selectedShip.fireOrders.length+1);
                        var fire = {id:fireid,type:type, shooterid:selectedShip.id, targetid:-1, weaponid:weapon.id, calledid:-1, turn:gamedata.turn, firingmode:weapon.firingMode, shots:weapon.defaultShots, x:hexpos.x, y:hexpos.y};
                        selectedShip.fireOrders.push(fire);
                        
                    }
                    if (weapon.ballistic){
                        gamedata.ballistics.push({id:(gamedata.ballistics.length), fireid:fireid, position:shipManager.getShipPosition(selectedShip),
                        facing:shipManager.movement.getLastCommitedMove(selectedShip).facing,
                        targetposition:hexpos,
                        targetid:-1,
                        shooterid:selectedShip.id,
                        weaponid:weapon.id,
                        shots:fire.shots});
                        
                        ballistics.calculateBallisticLocations();
                        ballistics.calculateDrawBallistics();                        
                        drawEntities();
                        //$id, $fireid, $position, $facing, $targetpos, $targetid, $shooterid, $weaponid, $shots
                    }
                    
                    toUnselect.push(weapon);
                }
            }
        }
        
        for (var i in toUnselect){
            weaponManager.unSelectWeapon(selectedShip, toUnselect[i]);
        }
        
        gamedata.shipStatusChanged(selectedShip);
    
    },
       
    
    removeFiringOrder: function(ship, system){
       
        for(var i = ship.fireOrders.length-1; i >= 0; i--){  
            if(ship.fireOrders[i].weaponid == system.id){              
                  
                for(var a = gamedata.ballistics.length-1; a >= 0; a--){
                    if (gamedata.ballistics[a].fireid == ship.fireOrders[i].id && gamedata.ballistics[a].shooterid == ship.id){
                        var id = gamedata.ballistics[a].id;
                        
                        $('#ballistic_launch_canvas_'+id).remove();
                        $('#ballistic_target_canvas_'+id).remove();
                        $('.ballistic_'+id).remove();
                        gamedata.ballistics.splice(a,1);  
                    }
                }  
                ship.fireOrders.splice(i,1);               
            }
        }
        ballistics.calculateBallisticLocations();
        ballistics.calculateDrawBallistics();                        
        drawEntities();
        
    
    },
    
    hasFiringOrder: function(ship, system){
        
        for (var i in ship.fireOrders){
            var fire = ship.fireOrders[i];
            if (fire.weaponid == system.id && fire.turn == gamedata.turn && !fire.rolled){
                if ((gamedata.gamephase == 1 && system.ballistic) || (gamedata.gamephase == 3 && !system.ballistic)){
                    return true;
                }
            }
                
        }
        return false;
    
    },
    
    getFiringOrder: function(ship, system){
        
        for (var i in ship.fireOrders){
            var fire = ship.fireOrders[i];
            if (fire.weaponid == system.id && fire.turn == gamedata.turn && !fire.rolled)
                return fire;
        }
        
        return false;
    
    },
    
    getInterceptingFiringOrders: function(id){
        var fires = Array();
        
        for (var a in gamedata.ships){
            var ship = gamedata.ships[a];
            for (var i in ship.fireOrders){
                var fire = ship.fireOrders[i];
                if (fire.targetid == id && fire.turn == gamedata.turn && fire.type == "intercept")
                    fires.push(fire);
            }
        }
        
        return fires;
    
    },
    
    changeShots: function(ship, system, mod){
        for (var i in ship.fireOrders){
            var fire = ship.fireOrders[i];
            if (fire.weaponid == system.id && fire.turn == gamedata.turn && !fire.rolled){
                if ((gamedata.gamephase == 1 && system.ballistic) || (gamedata.gamephase == 3 && !system.ballistic))
                    fire.shots += mod;
            }
                
        }
        
        shipWindowManager.setDataForSystem(ship, system);
    },
    
    getDamagesCausedBy: function(damages, fire){
        
        for (var i in gamedata.ships){
            var ship = gamedata.ships[i];
            var list = Array();
            
            for (var a in ship.systems){
                var system = ship.systems[a];
                for (var b in system.damage){
                    var d = system.damage[b];
                    if (d.fireorderid == fire.id){
                        list.push(d);
                    }
                        
                }
            
            }
            
            if (list.length>0){
                //console.log(list);
                var found = false;
                for (var a in damages){
                    var entry = damages[a];
                    if (entry.ship.id == ship.id){
                        found = true;
                        entry.damages = entry.damages.concat(list);
                    }
                }
                if (!found)
                    damages.push({ship:ship, damages:list});
            }
            
        }
        
        return damages;
        
      
        
    },
    
    removeArcIndicators: function(ship){
     
        for(var i = EWindicators.indicators.length-1; i >= 0; i--){  
            if(EWindicators.indicators[i].ship == ship && EWindicators.indicators[i].type == "Arcs"){              
                EWindicators.indicators.splice(i,1);                 
            }
        }

        
    },
    
    addArcIndicators: function(ship, weapon){
        weaponManager.removeArcIndicators(ship);
        var ind = weaponManager.makeWeaponArcindicator(ship, weapon);
        
        if (ind)
            EWindicators.indicators.push(ind);
      
    },
    
    makeWeaponArcindicator: function(ship, weapon){
        var effect = {};
        
        var a = shipManager.getShipHeadingAngle(ship);
        var arcs = shipManager.systems.getArcs(ship, weapon);
        var dis;
        if (weapon.rangePenalty == 0){
            dis =  hexgrid.hexWidth()*weapon.range;
        }else{
            dis =  20*hexgrid.hexWidth()/weapon.rangePenalty;
        }
        
        arcs.start = mathlib.addToDirection(arcs.start, a);
        arcs.end = mathlib.addToDirection(arcs.end, a);
        //console.log("start: " + arcs.start + " end: " +arcs.end);
        effect.ship = ship;
        effect.type = "Arcs"
        effect.arcs = arcs;
        effect.dis = dis;
        effect.draw = function(self){
            var arcs = self.arcs;
            var canvas = EWindicators.getEwCanvas();
            
            
            
            var pos = shipManager.getShipPositionForDrawing(self.ship);
            canvas.strokeStyle = "rgba(20,80,128,0.2)";
            canvas.fillStyle = "rgba(20,80,128,0.2)";
            if (arcs.start == arcs.end){
                graphics.drawCircleAndFill(canvas, pos.x, pos.y, self.dis, 1);
            }else{
            
                
                var p1 = mathlib.getPointInDirection(self.dis, arcs.start, pos.x, pos.y);
                var p2 = mathlib.getPointInDirection(self.dis, arcs.end, pos.x, pos.y);
        
                
                graphics.drawCone(canvas, pos, p1, p2, arcs, 1)
            }
            
        };
        
        return effect;
    
    },
    
    /*
    getHitPulses: function(weapon, target, fire){
        var pulses = 0;
        
        for (var i in target.systems){
            for (var b in target.systems[i].damage){
                var d = target.systems[i].damage[b];
                
                if (d.fireorderid == fire.id && d.damage == weapon.minDamage)
                    pulses++;
                
            }
        }
        
        
        return pulses;
        
        
    },
    */
    
    isLoaded: function(weapon){
        return (weapon.loadingtime <= weapon.turnsloaded);
    },
    
    getFireOrderById: function(id){
        
        for (var i in gamedata.ships){
            for (var a in gamedata.ships[i].fireOrders){
                var fire = gamedata.ships[i].fireOrders[a];
                if (fire.id == id)
                    return fire;
            }
            
            
        }
        
        return false;
        
    }
    
}





