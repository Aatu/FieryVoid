
jQuery(function(){
      $(".iconmask").bind("contextmenu", function(e) {
            e.preventDefault();
        }); 
});

flightWindowManager = {

	close: function(){
		//shipWindowManager.cancelAssignThrust($(this).parent().parent());
		$(this).parent().parent().hide();
	},	
	
	open: function(ship){
	
		
		var old;
		if (ship.userid == gamedata.thisplayer){
			old = $(".shipwindow.owned:visible");
		}else{
			old = $(".shipwindow.enemy:visible");
		}
		
		
		
		var n = ship.shipStatusWindow;
		
		if (!n)
			return;
			
		if (n.css("display") == "block")
			return;
			
		if (old.length){
			old.hide();
			n.css("top", old.css("top")).css("left", old.css("left"));
		}
			
		
		n.show();
		
	
	},

	checkIfAnyStatusOpen: function(ship){
		var old;
		
		if (ship.userid == gamedata.thisplayer){
			old = $(".shipwindow.owned:visible");
		}else{
			old = $(".shipwindow.enemy:visible");
		}

		if (old.length)
			shipWindowManager.open(ship);
		
		
	},
	
	
	createShipWindow: function(ship){
                var template;
                
                if(!ship.superheavy){
                    template = $("#shipwindowtemplatecontainer .shipwindow.flight");
                }else{
                    template = $("#shipwindowtemplatecontainer .shipwindow.heavyfighter");
                }
                
		var shipwindow = template.clone(true).appendTo("body");
		
		shipwindow.draggable();
		
		if (ship.userid == gamedata.thisplayer){
			shipwindow.addClass("owned");
			shipwindow.css("left", "50px");
		}else{
			shipwindow.addClass("enemy");
			shipwindow.css("right", "50px");
		}
				
		
		
		shipwindow.data("ship", ship.id);
		shipwindow.addClass("ship_"+ship.id);
		flightWindowManager.populateShipWindow(ship, shipwindow);
                flightWindowManager.bindEvents(shipwindow);
		return shipwindow;
		
	},
    
    bindEvents: function(shipwindow){

        $(".fightersystem", shipwindow).on("contextmenu", flightWindowManager.clickSystem);
        $(".fightersystem", shipwindow).on("click", flightWindowManager.clickSystem);

        $(".close", shipwindow).on("click", shipWindowManager.close);
    },

	populateShipWindow: function(ship, shipwindow){
		shipwindow.find(".icon img").attr("src", "./"+ship.imagePath);
		
                if(gamedata.turn != 0){
                    shipwindow.find(".topbar .value.name").html(ship.name);
                    shipwindow.find(".topbar .value.shipclass").html("");
                    shipwindow.find(".topbar .valueheader.shipclass").html("");
                }
                else{
                    shipwindow.find(".topbar .value.name").html("");
                    shipwindow.find(".topbar .valueheader.name").html("");
                    shipwindow.find(".topbar .value.shipclass").html(ship.shipClass);
                }

                if(!ship.superheavy){
                    flightWindowManager.addSystems(ship, shipwindow);
                }else{
                    flightWindowManager.addHeavyFighter(ship, shipwindow);
                }
		

	},
	
	addSystems: function (ship, shipwindow, location){
		
		var systems = ship.systems;
		
		for (var i in systems){
			var fighter = systems[i];
			
			var dest = shipwindow.find(".fightercontainer."+fighter.location);
			dest.addClass("occupied");
			var template = $("#systemtemplatecontainer .fighter");
			var systemwindow = template.clone(true).appendTo(dest);
			
			systemwindow.find(".icon").css("background-image", "url("+fighter.iconPath+")");
			
			systemwindow.addClass("fighter_" + fighter.id);
			systemwindow.data("shipid", ship.id);
			systemwindow.data("id", fighter.id);
			
			for (var a in fighter.systems){
				var fightersystem = fighter.systems[a];
				dest = flightWindowManager.getDestinationForSystem(ship, fighter, fightersystem.location);
				template = $("#systemtemplatecontainer .fightersystem");
				
				fightersystemwindow = template.clone(true).appendTo(dest);
				fightersystemwindow.wrap('<td/>');
                if (fightersystem.iconPath){
                    fightersystemwindow.find(".icon").css("background-image", "url(./img/systemicons/"+fightersystem.iconPath +")");
                }else{
                    fightersystemwindow.find(".icon").css("background-image", "url(./img/systemicons/"+fightersystem.name +".png)");
                }
				fightersystemwindow.addClass(fightersystem.name);
				fightersystemwindow.addClass("system_" + fightersystem.id);
				fightersystemwindow.data("shipid", ship.id);
				fightersystemwindow.data("fighterid", fighter.id);
				fightersystemwindow.data("id", fightersystem.id);
				
				fightersystemwindow.on("mouseover", weaponManager.onWeaponMouseover);
				fightersystemwindow.on("mouseout", weaponManager.onWeaponMouseOut);
                
				
			}
		
			
		}
		
		
	},

	addHeavyFighter: function (ship, shipwindow, location){
		
            var fighter = ship.systems[1];

            var dest = shipwindow.find(".fightercontainer");
            dest.addClass("occupied");
            var template = $("#systemtemplatecontainer .heavyfighter");
            var systemwindow = template.clone(true).appendTo(dest);

            systemwindow.find(".icon").css("background-image", "url("+fighter.iconPath+")");

            systemwindow.addClass("heavyfighter_" + fighter.id);
            systemwindow.data("shipid", ship.id);
            systemwindow.data("id", fighter.id);

            for (var a in fighter.systems){
                var fightersystem = fighter.systems[a];
                dest = flightWindowManager.getDestinationForSystem(ship, fighter, fightersystem.location);
                template = $("#systemtemplatecontainer .fightersystem");

                fightersystemwindow = template.clone(true).appendTo(dest);
                fightersystemwindow.wrap('<td/>');

                if (fightersystem.iconPath){
                    fightersystemwindow.find(".icon").css("background-image", "url(./img/systemicons/"+fightersystem.iconPath +")");
                }else{
                    fightersystemwindow.find(".icon").css("background-image", "url(./img/systemicons/"+fightersystem.name +".png)");
                }

                fightersystemwindow.addClass(fightersystem.name);
                fightersystemwindow.addClass("system_" + fightersystem.id);
                fightersystemwindow.data("shipid", ship.id);
                fightersystemwindow.data("fighterid", fighter.id);
                fightersystemwindow.data("id", fightersystem.id);

                fightersystemwindow.on("mouseover", weaponManager.onWeaponMouseover);
                fightersystemwindow.on("mouseout", weaponManager.onWeaponMouseOut);
            }
	},

	getDestinationForSystem: function(flight, fighter, location){
            if(flight.superheavy){
		return $(".shipwindow.ship_"+flight.id+" .heavyfighter_" +fighter.id+ " .fightersystemcontainer." + location + " tr");
            }else{
                return $(".shipwindow.ship_"+flight.id+" .fighter_" +fighter.id+ " .fightersystemcontainer." + location + " tr");
            }
		
	},
	
	
	removeSystemClasses: function(systemwindow){
		var classes = Array(
			"destroyed",
			"loading",
			"droppedout",
			"selected",
			"firing",
			"disengaged"
			
		);
		
		for (var i in classes){
			systemwindow.removeClass(classes[i]);
		}
	},
	
	setData: function(flight, system, shipwindow){
		if (system.fighter){
			flightWindowManager.setFighterData(flight, system, shipwindow);
			
		}else{
			flightWindowManager.setFighterSystemData(flight, system, shipwindow);
		}
	},
	
	setFighterData: function(flight, fighter, shipwindow){
            if(flight.superheavy){
		var systemwindow = shipwindow.find(".heavyfighter_"+fighter.id);
                var healtWidth = 120;
            }else{
		var systemwindow = shipwindow.find(".fighter_"+fighter.id);
                var healtWidth = 90;
            }


            systemwindow.find(".healthvalue ").html((fighter.maxhealth - damageManager.getDamage(flight, fighter)) +"/"+ fighter.maxhealth );
            systemwindow.find(".healthbar").css("width", (((fighter.maxhealth - damageManager.getDamage(flight, fighter)) / fighter.maxhealth)*healtWidth) + "px");


            flightWindowManager.removeSystemClasses(systemwindow);

            if (shipManager.systems.isDestroyed(flight, fighter)){
                    systemwindow.addClass("destroyed");
                    if (shipManager.criticals.hasCritical(fighter, "DisengagedFighter"))
                            systemwindow.addClass("disengaged");

                    return;
            }

            for (var i in fighter.systems){
                    var system = fighter.systems[i];
                    flightWindowManager.setFighterSystemData(flight, system, shipwindow);
            }
	},
	
	setFighterSystemData: function(flight, system, shipwindow){
		var systemwindow = shipwindow.find(".system_"+system.id);
		var fighter = shipManager.systems.getSystem(flight, systemwindow.data("fighterid"));
	
		var field = systemwindow.find(".efficiency.value");
		
		if (shipManager.systems.isDestroyed(flight, fighter)){
			systemwindow.addClass("destroyed");
			
			
		}
		
		if (system.weapon){
			var firing = weaponManager.hasFiringOrder(flight, system);
			if (!weaponManager.isLoaded(system)){systemwindow.addClass("loading");}else{systemwindow.removeClass("loading");}
			if (weaponManager.isSelectedWeapon(system)){systemwindow.addClass("selected");}else{systemwindow.removeClass("selected");}
			if (firing  && !(systemwindow.hasClass("loading"))){systemwindow.addClass("firing");}else{firing= false;systemwindow.removeClass("firing");}
			if (system.ballistic){	systemwindow.addClass("ballistic");	}else{	systemwindow.removeClass("ballistic");	}
			
			if (!firing){
                var load = weaponManager.getWeaponCurrentLoading(system);
				
				var loadingtime = system.loadingtime;
				if (system.normalload > 0)
					loadingtime = system.normalload;
					
				field.html(load+ "/" + loadingtime);
			}
				
		}
		
	},
	
	clickSystem: function(e){

        var type;
        if (e.type == "click"){
            type = 1;
        } else type = 2;

		e.stopPropagation();    

        var shipwindow = $(".shipwindow").has($(this));
        var systemwindow = $(this);

        var flight = gamedata.getShip(systemwindow.data("shipid"));
        var fighter = shipManager.systems.getSystem(flight, systemwindow.data("fighterid"));
        var system = shipManager.systems.getSystem(flight, systemwindow.data("id"));
        
        if (type == 1){
    		
    		if (shipManager.isDestroyed(flight) || shipManager.isDestroyed(flight, system) || shipManager.isDestroyed(flight, fighter) )
    			return;
    			
    		if (flight.userid != gamedata.thisplayer)
    			return;
    			
    		if (system.weapon){
    			
    			if (gamedata.gamephase != 3 && !system.ballistic)
    				return;
    			
    			if (gamedata.gamephase != 1 && system.ballistic)
    				return;
    		
    			if (weaponManager.hasFiringOrder(flight, system)){
    				weaponManager.cancelFire(flight, system);
    				
    			} else if (weaponManager.isSelectedWeapon(system)){
    				weaponManager.unSelectWeapon(flight, system);
    			}else{
    				weaponManager.selectWeapon(flight, system);
    			}    			
    		}
        }
        else {
            for (var i = 0; i < flight.systems.length; i++){
                if (flight.systems[i] != undefined){   
                    var fighter = flight.systems[i]        

                    if (shipManager.isDestroyed(flight) || shipManager.isDestroyed(flight, system) || shipManager.isDestroyed(flight, fighter) )
                        continue;
                        
                    if (flight.userid != gamedata.thisplayer)
                        continue;
                        
                    if (system.weapon){
                        
                        if (gamedata.gamephase != 3 && !system.ballistic)
                            continue;
                        
                        if (gamedata.gamephase != 1 && system.ballistic)
                            continue;

                        else{
                            weaponManager.selectWeapon(flight, system);

                        }               
                    }
                }
            }
        }
	},
	
	




}
