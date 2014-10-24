window.ajaxInterface = {

    poll: null,
    pollcount: 0,
	submiting: false,
//	fastpolling: false,

    submitGamedata: function(){
	
		if ( ajaxInterface.submiting )
			return;
			
		ajaxInterface.submiting = true;
	
        var gd = ajaxInterface.construcGamedata();
        
        $.ajax({
            type : 'POST',
            url : 'gamedata.php',
            dataType : 'json',
            data: gd,
            success : ajaxInterface.successSubmit,
            error : ajaxInterface.errorAjax
        });
        
        gamedata.goToWaiting();
    },
    
    submitSlotAction: function(action, slotid){
        ajaxInterface.submiting = true;

        $.ajax({
            type : 'POST',
            url : 'slot.php',
            dataType : 'json',
            data: {action:action, gameid:gamedata.gameid, slotid:slotid},
            success : ajaxInterface.successSubmit,
            error : ajaxInterface.errorAjax
        });
    },
            
    construcGamedata: function(){
        
        var tidyships = Array();
        
        for (var i in gamedata.ships)
        {
            var ship = gamedata.ships[i];
            var newShip = {
                'phpclass': ship.phpclass,
                'userid':ship.userid,
                'slot':ship.slot,
                'id':ship.id,
                'name': ship.name
            };
            newShip.movement = Array();
            newShip.EW = Array();
            newShip.systems = Array();
            
            if (gamedata.isMyShip(ship)){
                for (var a = ship.movement.length-1; a>=0; a--){
                    var move = ship.movement[a];
                    if (move.turn == gamedata.turn){
                        newShip.movement[a] = move;
                    }
                }

                for (var a = ship.EW.length-1; a>=0; a--){
                    var ew = ship.EW[a];
                    if (ew.turn == gamedata.turn){
                        newShip.EW[a] = ew;
                    }
                }
                var systems = Array();

                for (var a in ship.systems){
                    var system = ship.systems[a];

                    if (ship.flight){
                        var fighterSystems = Array();
                        
                        for (var c in system.systems){
                            var fightersystem = system.systems[c];
                            var ammoArray = Array();

                            for (var b = fightersystem.fireOrders.length-1; b>=0; b--){
                                var fire = fightersystem.fireOrders[b];
                                if (fire.turn < gamedata.turn){
                                    fightersystem.fireOrders.splice(b,1);
                                }
                            }
                            
                            if(fightersystem.missileArray != null){
                                for (var index in fightersystem.missileArray){
                                    var amount = fightersystem.missileArray[index].amount;
                                    ammoArray[index] = amount;
                                }
                            }
                            
                            fighterSystems[c] = {'id':fightersystem.id, 'fireOrders': fightersystem.fireOrders, 'ammo': ammoArray};
                        }

                        systems[a] = {'id': system.id, 'systems': fighterSystems};


                    }else{
                        var fires = Array();
                        var ammoArray = Array();
                        
                        if (system.dualWeapon || system.duoWeapon){
//                        if (system.duoWeapon){
                            for (var c in system.weapons){
                                var weapon = system.weapons[c];
                                if(weapon.duoWeapon){
                                    for(var d in weapon.weapons){
                                        var subweapon = weapon.weapons[d];
                                        for (var index = subweapon.fireOrders.length-1; index>=0; index--){
                                            var subfire = subweapon.fireOrders[index];
                                            if (subfire.turn < gamedata.turn){
                                                subweapon.fireOrders.splice(index,1);
                                            }
                                        }
                                        fires = fires.concat(subweapon.fireOrders);
                                    }
                                }else{
                                    //var weapon = system.weapons[c];
                                    for (var b = weapon.fireOrders.length-1; b>=0; b--){
                                        var fire = weapon.fireOrders[b];
                                        if (fire.turn < gamedata.turn){
                                            weapon.fireOrders.splice(b,1);
                                        }
                                    }
                                    fires = fires.concat(weapon.fireOrders);
                                }

                                
                                for (var b = weapon.power.length-1; b>=0; b--){
                                    var power = weapon.power[b];
                                    
                                    if (power.turn < gamedata.turn){
                                        weapon.power.splice(b,1);
                                    }
                                }
                                systems[a] = {'id': weapon.id, 'power': weapon.power, 'fireOrders': fires};
                            }
                            
                        }else{
                            for (var b = system.fireOrders.length-1; b>=0; b--){
                                var fire = system.fireOrders[b];
                                if (fire.turn < gamedata.turn){
                                    system.fireOrders.splice(b,1);
                                }
                            }
                            fires = system.fireOrders;
                            
                            for (var b = system.power.length-1; b>=0; b--){
                                var power = system.power[b];
                                if (power.turn < gamedata.turn){
                                    system.power.splice(b,1);
                                }
                            }
                            
                            systems[a] = {'id': system.id, 'power': system.power, 'fireOrders': fires};
                        }
                        
                        if(system.missileArray != null){
                            for (var index in system.missileArray){
                                var amount = system.missileArray[index].amount;
                                ammoArray[index] = amount;
                            }
                        }

                        systems[a] = {'id': system.id, 'power': system.power, 'fireOrders': fires, 'ammo': ammoArray};
                    }

                }
            
                newShip.systems = systems;
                tidyships.push(newShip);
            }
        }
       
        var gd = {
            turn: gamedata.turn,
            phase: gamedata.gamephase,
            activeship: gamedata.activeship,
            gameid: gamedata.gameid,
            playerid: gamedata.thisplayer,
            slotid: gamedata.selectedSlot,
            status: gamedata.status,
            ships: JSON.stringify(tidyships),
        };
  
        return gd;
    },
    
    construcGamedata2: function(){
        
        var tidyships = jQuery.extend(true, {}, gamedata.ships);
        
        for (var i in tidyships){
            var ship = tidyships[i];
            ship.htmlContainer = null;
            ship.shipclickableContainer = null;
            ship.shipStatusWindow = null;
            if (gamedata.isMyShip(ship)){
                for (var a = ship.movement.length-1; a>=0; a--){
                    var move = ship.movement[a];
                    if (move.turn < gamedata.turn){
                        ship.movement.splice(a,1);
                    }
                }

                for (var a = ship.EW.length-1; a>=0; a--){
                    var ew = ship.EW[a];
                    if (ew.turn < gamedata.turn){
                        ship.EW.splice(a,1);
                    }
                }
                var systems = Array();

                for (var a in ship.systems){
                    var system = ship.systems[a];

                    if (ship.flight){
                        var fighterSystems = Array();
                        for (var c in system.systems){
                            var fightersystem = system.systems[c];

                            for (var b = fightersystem.fireOrders.length-1; b>=0; b--){
                                var fire = fightersystem.fireOrders[b];
                                if (fire.turn < gamedata.turn){
                                    fightersystem.fireOrders.splice(b,1);
                                }
                            }
                            fighterSystems[c] = {'id':fightersystem.id, 'fireOrders': fightersystem.fireOrders};
                        }

                        systems[a] = {'id': system.id, 'systems': fighterSystems};


                    }else{
                        var fires = Array();
                        if (system.dualWeapon){
                            for (var c in system.weapons){
                                var weapon = system.weapons[c];
                                for (var b = weapon.fireOrders.length-1; b>=0; b--){
                                    var fire = weapon.fireOrders[b];
                                    if (fire.turn < gamedata.turn){
                                        weapon.fireOrders.splice(b,1);
                                    }
                                }
                                fires = fires.concat(weapon.fireOrders);
                            }
                            
                        }else{
                            for (var b = system.fireOrders.length-1; b>=0; b--){
                                var fire = system.fireOrders[b];
                                if (fire.turn < gamedata.turn){
                                    system.fireOrders.splice(b,1);
                                }
                            }
                            fires = system.fireOrders;
                        }
                        
                        for (var b = system.power.length-1; b>=0; b--){
                            var power = system.power[b];
                            if (power.turn < gamedata.turn){
                                system.power.splice(b,1);
                            }
                        }
                        systems[a] = {'id': system.id, 'power': system.power, 'fireOrders': fires};
                    }

                }
            
                ship.systems = systems;
            }else{
                ship.EW = Array();
                ship.movement = Array();
                ship.systems = Array();
            }
        }
       
        var gd = {
            turn: gamedata.turn,
            phase: gamedata.gamephase,
            activeship: gamedata.activeship,
            gameid: gamedata.gameid,
            playerid: gamedata.thisplayer,
            slotid: gamedata.selectedSlot,
            ships: JSON.stringify(tidyships)
        };
  
        return gd;
    },
    
    successSubmit: function(data){
        ajaxInterface.submiting = false;
        if (data.error){
           window.confirm.exception(data , function(){});
           gamedata.waiting = false;
        }else{
            gamedata.parseServerData(data);
        }
    },
    
    successRequest: function(data){
        ajaxInterface.submiting = false;
        if (data.error){
            window.confirm.exception(data , function(){});
            gamedata.waiting = false;
        }else{
            //gamedata.parseServerData(data);
        }
        gamedata.parseServerData(data);
    },
    
    errorAjax: function(jqXHR, textStatus, errorThrown){
        console.dir(jqXHR);
        console.dir(errorThrown);
        window.confirm.exception({error:"AJAX error: " +textStatus} , function(){});
    },
	
	
    startPollingGamedata: function(){
        
        if (gamedata.poll != null){
			console.log("starting to poll, but poll is not null");
            return;
			}
           
        ajaxInterface.pollcount = 0;
//        ajaxInterface.fastpolling = false;    
        ajaxInterface.pollGamedata();
    },
    
    stopPolling: function(){
        ajaxInterface.poll = null;
        ajaxInterface.pollcount  = 0;
    },
    
    pollGamedata: function(){
        if (gamedata.waiting == false){
			ajaxInterface.stopPolling();
            return;
		}          
        if (!gamedata.animating){
            //console.log("polling for gamedata...");
            animation.animateWaiting();
        
            if (!ajaxInterface.submiting)
                ajaxInterface.requestGamedata();
			
        }
        
        ajaxInterface.pollcount++;
        
        var time = 6000;
        
        
        if (ajaxInterface.pollcount > 10){
            time = 6000;
        }
        
        
        if (ajaxInterface.pollcount > 100){
//        	ajaxInterface.fastpolling = false;
            time = 30000;
        }
        
        if (ajaxInterface.pollcount > 200){
            time = 300000;
        }
        
        if (ajaxInterface.pollcount > 300){
            return;
        }   
        
//        if (ajaxInterface.fastpolling) {
//         	time=1000;
//        }
       
		ajaxInterface.poll = setTimeout(ajaxInterface.pollGamedata, time);
    },
    
    startPollingGames: function(){
        ajaxInterface.pollGames();
    },
    
    pollGames: function(){
        if (gamedata.waiting == false)
            return;
        
        if (!gamedata.animating){

            animation.animateWaiting();
        
            ajaxInterface.requestAllGames();
        }
        
    },
    
    requestGamedata: function(){
        
        ajaxInterface.submiting = true;
        
        $.ajax({
            type : 'GET',
            url : 'gamedata.php',
            dataType : 'json',
            data: {
                turn: gamedata.turn,
                phase: gamedata.gamephase,
                activeship: gamedata.activeship,
                gameid: gamedata.gameid,
                playerid: gamedata.thisplayer,
                time: new Date().getTime()
            },
            success : ajaxInterface.successRequest,
            error : ajaxInterface.errorAjax
        });
    },
    
    requestAllGames: function(){
        
        ajaxInterface.submiting = true;
        
        $.ajax({
            type : 'GET',
            url : 'allgames.php',
            dataType : 'json',
            data: {},
            success : ajaxInterface.successRequest,
            error : ajaxInterface.errorAjax
        });
    },
    
}







