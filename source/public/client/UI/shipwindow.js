jQuery(function(){
   //   $(".iconmask").bind("contextmenu", function(e) {
    //        e.preventDefault();
     //   }); 
});

shipWindowManager = {

    prepare: function(){
        
        for (var i in gamedata.ships){
            var ship = gamedata.ships[i];
            var n = ship.shipStatusWindow;
/*            if (shipManager.movement.isRolled(ship)){
                n.addClass("rolled");
            }else{
                n.removeClass("rolled");
            }*/
        }
    
    },
    
	close: function(){
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
			
/*		if (shipManager.movement.isRolled(ship)){
            n.addClass("rolled");
        }else{
            n.removeClass("rolled");
        }*/
        
        this.updateNotes(ship);

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

		
	
		var template = $("#shipwindowtemplatecontainer .shipwindow.ship");
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
		shipWindowManager.populateShipWindow(ship, shipwindow);
                shipWindowManager.bindEvents(shipwindow);
		
		return shipwindow;
	},
    
    bindEvents: function(shipwindow){




        $(".close", shipwindow).on("click", shipWindowManager.close);
        $(".system .plus", shipwindow).on("click", shipWindowManager.clickPlus);
        $(".system .minus", shipwindow).on("click", shipWindowManager.clickMinus);

        $(".system .off", shipwindow).on("click", shipManager.power.onOfflineClicked);
        $(".system .on", shipwindow).on("click", shipManager.power.onOnlineClicked);
        $(".system .overload", shipwindow).on("click", shipManager.power.onOverloadClicked);
        $(".system .stopoverload", shipwindow).on("click", shipManager.power.onStopOverloadClicked);
        $(".system .holdfire", shipwindow).on("click", window.weaponManager.onHoldfireClicked);
    },

	populateShipWindow: function(ship, shipwindow){
		shipwindow.find(".icon img").attr("src", "./"+ship.imagePath);
		var thumb = shipwindow.find(".icon img");


                if(gamedata.turn != 0){
                    shipwindow.find(".topbar .valueheader.shipclass").html("");
                    shipwindow.find(".topbar .value.name").html(ship.name);
                    shipwindow.find(".topbar .value.shipclass").html(ship.shipClass);
                }
                else{
                    shipwindow.find(".topbar .value.name").html("");
                    shipwindow.find(".topbar .valueheader.name").html("");
                    shipwindow.find(".topbar .value.shipclass").html(ship.shipClass + " (" + ship.occurence + ")");
                }
                
		
		
		shipWindowManager.addSystems(ship, shipwindow, 1);
		shipWindowManager.addSystems(ship, shipwindow, 0);
		shipWindowManager.addSystems(ship, shipwindow, 2);
		shipWindowManager.addSystems(ship, shipwindow, 3);
		shipWindowManager.addSystems(ship, shipwindow, 4);

	},
	
        updateNotes: function(ship){
            var shipWindow = ship.shipStatusWindow;
            shipWindow.find(".notes").html("");
            
            var abilities = Array();
            var notes = Array();
            

			var belowIcon = shipWindow.find(".notes");
	//		$(belowIcon).addClass("selfIntercept");

			var input = document.createElement("input");
				input.type = "button";
				input.value = "Defensive Fire";
				input.className = "interceptButton";
				input.className += " interceptDisabled";

			$(input).click(function(){
				weaponManager.checkSelfIntercept(ship);
			})

			$(belowIcon).append(input);

			if (gamedata.gamephase == 3 &&
				ship.userid == gamedata.thisplayer){
				
				if (weaponManager.canSelfIntercept(ship)){
					input.className = "interceptButton";
					input.className += " interceptEnabled";
				}
			}


            if(ship.agile){
                abilities.push("&nbsp;Agile ship");
            }

            if(shipManager.movement.isRolled(ship)){
                notes.push("&nbsp;Ship is rolled.");
            }

            if(shipManager.movement.isRolling(ship)){
                notes.push("&nbsp;Ship is rolling.</p><p>");
            }

            if(gamedata.turn == 0){
                notes.push("&nbsp;This ship carries:");
                if(ship.fighters.length != 0){
                    for (var i in ship.fighters){
                        var amount = ship.fighters[i];
                        
                        if(i == "normal"){
                            notes.push("&nbsp;&nbsp;&nbsp;"+amount+" fighters");
                        }
                        else{
                            notes.push("&nbsp;&nbsp;&nbsp;"+amount+" "+ i +" fighters");
                        }
                    }
                }
                else{
                    notes.push("&nbsp;&nbsp;&nbsp;no fighters");
                }
               
                if(ship.limited != 0){
                    notes.push("&nbsp;Limited: " + ship.limited + "%");
                }
            }
            
            /* Set everything into the notes decently. */
            /* If we have both abilities and notes, insert a hr as seperator*/
            for(var i = 0; i < abilities.length; i++){
                shipWindow.find(".notes").append("<p>");
                shipWindow.find(".notes").append(abilities[i]);
                shipWindow.find(".notes").append("</p>");
            }
            
            if(abilities.length > 0 && notes.length > 0){
                /* Insert fancy hr. */
                shipWindow.find(".notes").append('<hr width=90% height=1px border=0 color=#496791>');
            }
            
            for(var index = 0; index < notes.length; index++){
                shipWindow.find(".notes").append("<p>");
                shipWindow.find(".notes").append(notes[index]);
                shipWindow.find(".notes").append("</p>");
            }
        },
        
	addSystems: function (ship, shipwindow, location){
		
		var systems = shipManager.systems.getSystemsForShipStatus(ship, location);
		var structure = shipManager.systems.getStructureSystem(ship, location);
		var destination = $(".shipwindow.ship_"+ship.id+" #shipSection_" + location + " table");
		
		if (systems.length == 0){
			destination.css("display", "none");
			return;
		}
		
	
		var arrangement;
		var col2 = 2;
		var col4 = 4;
		if (location == 0){
			arrangement = shipWindowManager.getFinalArrangementFour(ship, systems, structure);
		}else if (location < 3){
			arrangement = shipWindowManager.getFinalArrangementFour(ship, systems, structure);
		}else{
			col2 = 1;
			col4 = 2;
			arrangement = shipWindowManager.getFinalArrangementTwo(ship, systems, structure);
		}
		
		var index = 0;
		for (var i in arrangement){
			var group = arrangement[i];
			var row;
			if (group.length == 1){row = $('<tr><td colspan="'+col4+'" class="systemcontainer_'+index+'"></td></tr>');}
			if (group.length == 2){	
			
				if (location == 4){
					row = $('<tr><td colspan="'+col2+'" class="systemcontainer_'+(index+1)+'"></td><td colspan="'+col2+'" class="systemcontainer_'+(index)+'"></td></tr>');
				}else{
					row = $('<tr><td colspan="'+col2+'" class="systemcontainer_'+index+'"></td><td colspan="'+col2+'" class="systemcontainer_'+(index+1)+'"></td></tr>');
				}
				
			}
			
			if (group.length == 3){	
			
				row = $('<tr><td class="systemcontainer_'+index+'"></td><td colspan="2" class="systemcontainer_'+(index+1)+'"></td>'
				+'<td class="systemcontainer_'+(index+2)+'"></td></tr>').appendTo(destination);
			}
				
			if (group.length == 4){	
				row = $('<tr><td class="systemcontainer_'+index+'"></td><td class="systemcontainer_'+(index+1)+'"></td>'
				+'<td class="systemcontainer_'+(index+2)+'"></td><td class="systemcontainer_'+(index+3)+'"></td></tr>')
			}
			
			if (location == 2){
				row.prependTo(destination);
			}else{
				row.appendTo(destination);
			}
			
	
			for (var a in group){
				var system = group[a];
				shipWindowManager.addSystem(ship, system, $(".systemcontainer_"+index, destination));
				index++;
			}
			
		}
        
        if (location == 3){
            $('<div style="height:'+(arrangement.length*41)+'px"></div>').appendTo(".shipwindow.ship_"+ship.id+" .col1");
        }
        if (location == 4){
            $('<div style="height:'+(arrangement.length*41)+'px"></div>').appendTo(".shipwindow.ship_"+ship.id+" .col3");
        }
      
		
		
	},
	
	getFinalArrangementTwo: function(ship, systems, structure){
				
		var grouped = Array();
		
		var list = Array();
		
		for (var i= 0;i<systems.length;i++){
			var system = systems[i];
			if (systems.length % 2 == 1 && i == 0){
				grouped.push(Array(system));
			}else{
				list.push(system);
				if (list.length == 2){
					grouped.push(list);
					list = Array();
				}
			}
			
		}

		if (structure){
			grouped.push(Array(structure));
		}
		
		return grouped;
		
		
		
	},
	
	
	getFinalArrangementFour: function(ship, systems, structure){
				
		var grouped = shipManager.systems.groupSystems(systems);

		grouped = shipWindowManager.combineGroups(grouped);
		grouped = shipWindowManager.addStructure(grouped, structure);
		
		return grouped;
		
		
		
	},
	
	addStructure: function(grouped, structure){
		
		if (!structure)
			return grouped;
		
		var ones = Array();
		var deletes = Array();
		
		for (var i in grouped){
			var group = grouped[i];
		
			if (group.length == 2){
				grouped.push(Array(group[0],structure, group[1]));
				grouped.splice(i, 1);
				return grouped;
			}
			
			if (group.lenght == 1 && ones.length < 2){
				ones.push(group[i]);
				deletes.push(i);
				if (ones.length == 2){
					grouped.push(Array(ones[0][0], structure, ones[1][0]));
					for (var d in deletes){
						grouped.splice(deletes[d], 1);
					}
					return grouped;
				}
			}
			
		}
				
		grouped.push(Array(structure));
		
		return grouped;
	
	},

	
	combineGroups: function(grouped){
		var finals = Array();
	
		for (var i in grouped){
			var group = grouped[i];
			if (shipWindowManager.isInFinal(finals, group))
				continue;
				
			if (group.length == 1 || group.length == 2){
				var found = false;
				for (var a in grouped){
					var other = grouped[a];
					if (!found && other.length == 2 && !shipWindowManager.isInFinal(finals, other) && group != other){
						if (group.length == 1){
							finals.push(Array(other[0],group[0],other[1]));
						}else{	
							finals.push(Array(other[0],group[0], group[1],other[1]));
						}
											
						found = true;
						break;
					}
				}
				if (!found){
					finals.push(group);
				}
				
			}else{
				finals.push(group);
			}
			
				
			
		}
		
		return finals;
		
	
	},
	
	isInFinal: function(finals, group){
	
		for (var i in group){
			var system = group[i];
			for (var a in finals){
				for (var b in finals[a]){
					var other = finals[a][b];
					if (other == system)
						return true;
				}
			}
		}
	
		return false;
	},
	

	getDestinationForSystem: function(ship, location){
		
		return $(".shipwindow.ship_"+ship.id+" #shipSection_" + location + " table");
		
	},
	
	setDataForSystem: function(ship, system){
            var shipwindow = ship.shipStatusWindow;
            if (shipwindow){
                if (ship.flight){
                    flightWindowManager.setData(ship, system, shipwindow);
                }else{
                    shipWindowManager.setSystemData(ship, system, shipwindow)
                    
                    if(system.dualWeapon && system.weapons){
                        var dualsystem = system.weapons[system.firingMode];
                        shipWindowManager.setSystemData(ship, dualsystem, shipwindow);
                        
                        if(system.duoWeapon){
                            for(var i in system.weapons){
                                var subweapon = system.weapons[i];
                                shipWindowManager.setSystemData(ship, subweapon, shipwindow);
                            }
                        }
                    }else
                    {
                        shipWindowManager.setSystemData(ship, system, shipwindow);
                    }

                    if (system.name == "scanner"){
                        shipWindowManager.addEW(ship, shipwindow);
                        botPanel.setEW(ship);
                    }
                }
            }
	},
	
	setData: function(ship){
            var shipwindow = ship.shipStatusWindow;
            if (shipwindow){
                if (ship.flight){
                    for (var i in ship.systems){
                        var fighter = ship.systems[i];
                        flightWindowManager.setData(ship, fighter, shipwindow);
                    }
                }else{
                    shipWindowManager.addEW(ship, shipwindow);

                    for (var i in ship.systems){
                        var system = ship.systems[i];

                        if(system.dualWeapon){
                            for(var index in system.weapons){
                                var weapon = system.weapons[index];
                                
                                // ignore the weapon that isn't currently selected
                                if(weapon.id != system.weapons[system.firingMode].id){
                                    continue;
                                }
                                
                                if(weapon.duoWeapon){
                                    if (!shipManager.power.isOffline(ship, system)){
                                        // Don't bother setting anything if the top system is offline
                                        for(var subindex in weapon.weapons){
                                            shipWindowManager.setSystemData(ship, weapon.weapons[subindex], shipwindow);
                                        }
                                    }
                                }else if(!system.weapons[system.firingMode].duoWeapon){
                                    shipWindowManager.setSystemData(ship, weapon, shipwindow);
                                }
                            }
                        } else {
                            shipWindowManager.setSystemData(ship, system, shipwindow);
                        }
                    }
                }
            }
	},
	
	
	addEW: function(ship, shipwindow){
		var dew = (!gamedata.isMyShip(ship) && gamedata.gamephase == 1) ? "?" : ew.getDefensiveEW(ship);
		var ccew = (!gamedata.isMyShip(ship) && gamedata.gamephase == 1) ? "?": ew.getCCEW(ship);
        var bdew = (!gamedata.isMyShip(ship) && gamedata.gamephase == 1) ? "?": ew.getBDEW(ship)*0.25;
		var elint = shipManager.isElint(ship);
		shipwindow.find(".value.DEW").html(dew);
		shipwindow.find(".value.CCEW").html(ccew);

        var ccewElement = shipwindow.find(".value.CCEW").parent();
		if (ccew === 0){
			ccewElement.data("ship", ship).data("EW", "CCEW");
		}else{
			ccewElement.data("ship", ship).data("EW", ew.getCCEWentry(ship));
		}
		
        var BDEWcont = shipwindow.find(".ewentry.BDEW")
        if (elint){
            BDEWcont.show();
            var bdewElement = BDEWcont.find(".value.BDEW");
            bdewElement.html(bdew);
            if (bdew === 0){
                BDEWcont.data("ship", ship).data("EW", "BDEW");
            }else{
                BDEWcont.data("ship", ship).data("EW", ew.getBDEWentry(ship)); 
            }
        }else{
            BDEWcont.hide();  
        }
	
		
		var template = $("#templatecontainer .ewentry");
		shipwindow.find(".ewentry.deletable").remove();
		
		
		
		for (var i in ship.EW){
			var entry = ship.EW[i];
			if ((entry.type != "OEW" && entry.type != "DIST" && entry.type != "SOEW" && entry.type != "SDEW" )|| entry.turn != gamedata.turn)
				continue;
				
			element = template.clone(true).appendTo(shipwindow.find(".EW .EWcontainer"));

			element.data("EW", entry);
			element.data("ship", ship);
            element.find(".button1").on("click", ew.buttonDeassignEW);
            element.find(".button2").on("click", ew.buttonAssignEW);

            var h = entry.type +' (<span class="shiplink">' + gamedata.getShip(entry.targetid).name + '</span>):';
            if (entry.type == "SOEW"){
                element.find(".button2").remove();
                element.find(".value").html(entry.amount);
            }else if (entry.type == "SDEW"){
                element.find(".value").html(entry.amount*0.5);
            }else if (entry.type == "DIST"){
                element.find(".value").html((entry.amount/3));
            }else if (entry.type == "OEW"){
                element.find(".value").html((entry.amount - ew.getDistruptionEW(ship)));
            }else{
                element.find(".value").html(entry.amount);
            }
            
            
			element.find(".valueheader").html(h);
			
			
		}
			
		
	},
	
	
	
	
	
	addStructureSystem: function(ship, system, destination){
		var template = $("#systemtemplatecontainer .structure.system");
		var systemwindow = template.clone(true).appendTo(destination);
		systemwindow.addClass(system.name);

		systemwindow.addClass(system.name);
		systemwindow.addClass("system_" + system.id);
		systemwindow.data("shipid", ship.id);
		systemwindow.data("id", system.id);
		
		
	},
	
	addSystem:function (ship, system, destination){
            if (system.name == "structure"){
                shipWindowManager.addStructureSystem(ship, system, destination);
                return;
            }
            
            var template = $("#systemtemplatecontainer .system.regular");
            var systemwindow = template.clone(true).appendTo(destination);
            
            if(system.duoWeapon){
                shipWindowManager.addDuoSystem(ship, system, systemwindow);
            }
            else if(system.dualWeapon){
                shipWindowManager.addDualSystem(ship, system, systemwindow);
            }
            else{
                shipWindowManager.addRegularSystem(ship, system, systemwindow);
            }
	},

        addRegularSystem:function (ship, system, systemwindow){
            var icon = systemwindow.find(".systemcontainer").html("");
            icon.removeClass("duosystem");
            icon.removeClass("dualsystem");
            icon.addClass("regular");
            var icon_template = $("#systemtemplatecontainer .system.regular .icon");
            icon_template.clone(true).appendTo(icon);
            icon_template = $("#systemtemplatecontainer .system.regular .health.systembarcontainer");
            icon_template.clone(true).appendTo(icon);

            var iconplace = systemwindow.find(".icon");

            systemwindow.addClass(system.name);
            if (system.iconPath){
                systemwindow.find(".icon").css("background-image", "url(./img/systemicons/"+system.iconPath +")");
            }else{
                systemwindow.find(".icon").css("background-image", "url(./img/systemicons/"+system.name +".png)");
            }
            
            systemwindow.addClass(system.name);
            systemwindow.addClass("system_" + system.id);
            systemwindow.data("shipid", ship.id);
            systemwindow.data("id", system.id);

            if (system.weapon){
                systemwindow.addClass("weapon");
            }
	
            systemwindow.find(".off").off("click", shipManager.power.onOfflineClicked);
            systemwindow.find(".on").off("click", shipManager.power.onOnlineClicked);
            systemwindow.off("mouseover", weaponManager.onWeaponMouseover);
            systemwindow.off("mouseout", weaponManager.onWeaponMouseOut);
            systemwindow.off("click", shipWindowManager.clickSystem);
            systemwindow.off("contextmenu", shipWindowManager.selectAllGuns);
            
            systemwindow.find(".mode").off("click", shipWindowManager.onModeClicked);
            
            systemwindow.find(".off").on("click", shipManager.power.onOfflineClicked);
            systemwindow.find(".on").on("click", shipManager.power.onOnlineClicked);
            systemwindow.on("mouseover", weaponManager.onWeaponMouseover);
            systemwindow.on("mouseout", weaponManager.onWeaponMouseOut);

            systemwindow.on("click", shipWindowManager.clickSystem);
            systemwindow.on("contextmenu", shipWindowManager.selectAllGuns);


            systemwindow.find(".holdfire").on("click", window.weaponManager.onHoldfireClicked);
            
            systemwindow.find(".mode").on("click", shipWindowManager.onModeClicked);
        },

        addDualSystem: function (ship, system, dualwindow){
            var dualsystem = system.weapons[system.firingMode];
            
            if(dualsystem.duoWeapon){
                shipWindowManager.addDuoSystem(ship, dualsystem, dualwindow);
                dualwindow.find(".systemcontainer").addClass("dualsystem");
                dualwindow.find(".systemcontainer").addClass("duosystem");
                dualwindow.addClass("modes");
                dualwindow.addClass("parentsystem_"+dualsystem.parentId);
                dualwindow.addClass("weapon");
                
                if(dualsystem.parentId >= 0){
                    var parentSystem = shipManager.systems.getSystem(ship, dualsystem.parentId);

                    if(parentSystem.parentId >= 0){
                        parentSystem = shipManager.systems.getSystem(ship, parentSystem.parentId);
                        $(".parentsystem_"+parentSystem.id).addClass("modes");
                        var modebutton =  $(".mode", $(".parentsystem_"+parentSystem.id));
                    }else{
                        $(".parentsystem_"+parentSystem.id).addClass("modes");
                        var modebutton =  $(".mode", dualwindow);
                    }

                    modebutton.html("<span>"+parentSystem.firingModes[parentSystem.firingMode].substring(0, 1)+"</span>");
                }                
                
            }else{
                shipWindowManager.addRegularSystem(ship, dualsystem, dualwindow);
                dualwindow.find(".systemcontainer").removeClass("duosystem");
                dualwindow.find(".systemcontainer").removeClass("regular");
                dualwindow.find(".systemcontainer").addClass("dualsystem");
                dualwindow.addClass("parentsystem_"+dualsystem.parentId);
                dualwindow.addClass("weapon");
            }
        },
        
	addDuoSystem:function (ship, system, duowindow){
            var icon = duowindow.find(".systemcontainer").html("");
            icon.addClass("duosystem");
            icon.addClass("dualsystem");
            duowindow.addClass("system_"+system.id);
            
            duowindow.data("shipid", ship.id);
            duowindow.data("id", system.id);

            icon.removeClass("regular");
            var icon_template = $("#systemtemplatecontainer .system.regular .icon");
            icon_template.clone(true).appendTo(icon);
            icon_template = $("#systemtemplatecontainer .system.regular .health.systembarcontainer");
            icon_template.clone(true).appendTo(icon);
            icon.find(".efficiency.value").remove();
                
            for(var i in system.weapons){
                var weapon = system.weapons[i];
                var iconduo_template = $("#systemtemplatecontainer .iconduo");
                var iconduo_temp = iconduo_template.clone(true).appendTo(duowindow.find(".icon"));
                
                if (weapon.iconPath){
                    duowindow.find(".iconduo").css("background-image", "url(./img/systemicons/"+iconPath +")");
                }else{
                    duowindow.find(".iconduo").css("background-image", "url(./img/systemicons/"+weapon.name +"Duo.png)");
                }
                
                iconduo_temp.css("left", ""+(i-1)*16 + "px");
                
                iconduo_temp.addClass(weapon.name);
                iconduo_temp.addClass("system_" + weapon.id);
                iconduo_temp.data("shipid", ship.id);
                iconduo_temp.data("id", weapon.id);
                
                iconduo_temp.addClass("weapon");

                iconduo_temp.on("mouseover", weaponManager.onWeaponMouseoverDuoSystem);
                iconduo_temp.on("mouseout", weaponManager.onWeaponMouseOutDuoSystem);
                iconduo_temp.on("click", shipWindowManager.clickSystem);
           }

           // remove the iconmask because it's not needed (it will be added when
           // necessary.
           duowindow.find(".iconmask").remove();
           duowindow.off("mouseover", weaponManager.onWeaponMouseover);
           duowindow.off("mouseout", weaponManager.onWeaponMouseOut);
           duowindow.find(".off").off("click", shipManager.power.onOfflineClicked);
           duowindow.find(".on").off("click", shipManager.power.onOnlineClicked);

           duowindow.on("mouseover", weaponManager.onWeaponMouseover);
           duowindow.on("mouseout", weaponManager.onWeaponMouseOut);
           duowindow.find(".off").on("click", shipManager.power.onOfflineClicked);
           duowindow.find(".on").on("click", shipManager.power.onOnlineClicked);
           duowindow.find(".holdfire").on("click", window.weaponManager.onHoldfireClicked);
           $(".system .icon .UI .mode").on("click", shipWindowManager.onModeClicked);
	},
        
	removeSystemClasses: function(systemwindow){
		var classes = Array(
			"destroyed",
			"loading",
			"selected",
			"firing",
            "duofiring",
			"critical",
			"canoffline",
			"offline",
			"canboost",
			"boosted",
			"canoverload",
			"overload",
			"forcedoffline",
            "modes",
            "ballistic",
            "selfIntercept"
		);
		
		for (var i in classes){
			systemwindow.removeClass(classes[i]);
                        
                        var duowindows = $(systemwindow).find(".iconduo");
                        if(duowindows.length > 0){
                            $(systemwindow).find(".iconduo").removeClass(classes[i]);
                        }
		}
	},
	
setSystemData: function(ship, system, shipwindow){
    this.updateNotes(ship);
    var parentWeapon = null;
    var parentWindow = null;

    if(system.parentId > 0){
        parentWeapon = system;
        
        while(parentWeapon.parentId > 0){
            parentWeapon = shipManager.systems.getSystem(ship, parentWeapon.parentId);
        }
        
        system.damage = parentWeapon.damage;
        
        parentWindow = shipwindow.find(".parentsystem_"+parentWeapon.id);
    }

    shipManager.systems.initializeSystem(system);
    
    if(system.dualWeapon && system.weapons != null){
            var weapon = system.weapons[system.firingMode];
            shipManager.systems.initializeSystem(weapon);;
    }
    
    var systemwindow = shipwindow.find(".system_"+system.id);
    
    if(systemwindow.length == 0 && system.parentId > -1){
        systemwindow = shipwindow.find(".parentsystem_"+system.parentId);
    }
    
    var output = shipManager.systems.getOutput(ship, system);
    var field = systemwindow.find(".efficiency.value");

    if (system.name == "structure"){
        systemwindow.find(".healthvalue ").html((system.maxhealth - damageManager.getDamage(ship, system)) +"/"+ system.maxhealth + " A" + shipManager.systems.getArmour(ship, system));
    }

    if(system.parentId > 0){
        parentWindow.find(".healthbar").css("width", (((system.maxhealth - damageManager.getDamage(ship, system)) / system.maxhealth)*100) + "%");
    }else{
        systemwindow.find(".healthbar").css("width", (((system.maxhealth - damageManager.getDamage(ship, system)) / system.maxhealth)*100) + "%");
    }
    
    if (system.name == "thruster"){
        systemwindow.data("direction", system.direction);
        systemwindow.find(".icon").css("background-image", "url(./img/systemicons/thruster"+system.direction+".png)");
    }

    shipWindowManager.removeSystemClasses(systemwindow);
    
    if (shipManager.systems.isDestroyed(ship, system)){
        if(system.parentId > 0){
            if(shipManager.systems.getSystem(ship, system.parentId).duoWeapon){
                // create an iconMask at the top of the DOM for the system.
                var iconmask_element = document.createElement('div');
                iconmask_element.className = "iconmask";
                parentWindow.find(".iconmask").remove();
                parentWindow.find(".icon").append(iconmask_element);
            }

            parentWindow.addClass("destroyed");
        }else{
            systemwindow.addClass("destroyed");
        }
        return;
    }
    
    if (shipManager.criticals.hasCriticals(system)){
        if(system.parentId > 0){
            parentWindow.addClass("critical");
        }else{
            systemwindow.addClass("critical");
        }
    }
    
    if (shipManager.power.setPowerClasses(ship, system,  systemwindow))
        return;
    
    if (system.weapon){
        var firing = weaponManager.hasFiringOrder(ship, system);

        
        // To avoid double overlay of loading icon mask in case of a 
        // duoWeapon in a dualWeapon
        if (!weaponManager.isLoaded(system) && !(system.duoWeapon && system.parentId > 0)){
            systemwindow.addClass("loading");
        }else{
            systemwindow.removeClass("loading");
        }
        
        if (weaponManager.isSelectedWeapon(system)){
            systemwindow.addClass("selected");
        }else{
            systemwindow.removeClass("selected");
        }
        
        if (firing && firing != "self" && !system.duoWeapon  && !(systemwindow.hasClass("loading"))){
            systemwindow.addClass("firing");
            
            if(system.parentId > -1){
                var parentSystem = shipManager.systems.getSystem(ship, system.parentId);
                
                if(parentSystem.duoWeapon){
                    $(".system_"+system.parentId).addClass("duofiring");
                }
            }
        }

        else if (firing == "self"){
        	systemwindow.addClass("firing");
        	systemwindow.addClass("selfIntercept");
        }

        else{
            firing = false;
            systemwindow.removeClass("firing");
        	systemwindow.removeClass("selfIntercept");
        }
        
        if (system.ballistic){
            systemwindow.addClass("ballistic");
        }else{
            systemwindow.removeClass("ballistic");
        }
	
        if (!firing && (Object.keys(system.firingModes).length > 1 || system.dualWeapon))
        {
            if(system.parentId >= 0){
                var parentSystem = shipManager.systems.getSystem(ship, system.parentId);
                
                if(parentSystem.parentId >= 0){
                    parentSystem = shipManager.systems.getSystem(ship, parentSystem.parentId);
                    $(".parentsystem_"+parentSystem.id).addClass("modes");
                    var modebutton =  $(".mode", $(".parentsystem_"+parentSystem.id));
                }else{
                    $(".parentsystem_"+parentSystem.id).addClass("modes");
                    var modebutton =  $(".mode", systemwindow);
                }
                
                modebutton.html("<span>"+parentSystem.firingModes[parentSystem.firingMode].substring(0, 1)+"</span>");
            }else{
                systemwindow.addClass("modes");

                var modebutton =  $(".mode", systemwindow);
                modebutton.html("<span>"+system.firingModes[system.firingMode].substring(0, 1)+"</span>");
            }
        }
        
        if (firing && system.canChangeShots){
            var fire = weaponManager.getFiringOrder(ship, system);
            
            if (fire.shots<system.shots){
                systemwindow.addClass("canAddShots");
            }else{
                systemwindow.removeClass("canAddShots");
            }
            
            if (fire.shots>1){
                systemwindow.addClass("canReduceShots");
            }else{
                systemwindow.removeClass("canReduceShots");
            }
            
            field.html(fire.shots+ "/" + system.shots);
        }else if (!firing){
            if(system.duoWeapon){
                var UI_active = systemwindow.find(".UI").hasClass("active");
                
                shipWindowManager.addDuoSystem(ship, system, systemwindow);
                
                if(UI_active){
                    systemwindow.find(".UI").addClass("active");
                }
                
                
            }else{
                if(system.dualWeapon && system.weapons){
                    system = system.weapons[system.firingMode];
                }
                
                var load = weaponManager.getWeaponCurrentLoading(system);
                var loadingtime = system.loadingtime;

                if (system.normalload > 0){
                    loadingtime = system.normalload;
                }

                if(load > loadingtime){
                    load = loadingtime;
                }

                var overloadturns = "";

                if (system.overloadturns > 0 && shipManager.power.isOverloading(ship, system)){
                    overloadturns = "("+system.overloadturns+")";       
                }

                if (system.overloadshots >0){
                    field.html("S"+system.overloadshots);
                }else{
                    field.html(load+overloadturns+ "/" + loadingtime);
                }
            }
	}
    }else if (system.name == "thruster"){
        systemwindow.data("direction", system.direction);
        systemwindow.find(".icon").css("background-image", "url(./img/systemicons/thruster"+system.direction+".png)");
        
        var channeled = shipManager.movement.getAmountChanneled(ship, system);
        if (channeled > output){
            field.addClass("darkred");
        }else{
            field.removeClass("darkred");
        }
        
        if (channeled < 0){
            channeled = 0;
        }

        field.html(channeled + "/" + output);
    }else if(system.name == "engine"){
        var rem = shipManager.movement.getRemainingEngineThrust(ship);
        
        field.html(rem + "/" + output);
    }else if (system.name == "reactor"){
        field.html(shipManager.power.getReactorPower(ship, system));
    }else if (system.output > 0){
	field.html(output);
    }
},
	
	assignThrust: function(ship){
		var movement = ship.movement[ship.movement.length-1];
		if (movement.commit)
			return false;
			
		var requiredThrust = movement.requiredThrust;
		var stillReq = shipManager.movement.calculateThrustStillReq(ship, movement);
		var done = true;
		var names = Array("either", "front", "aft", "port", "starboard");
		if (movement.type == "roll"){
			names[0] = "any";
		}
		
		var additionally = "";
		var objective = "";
		var objectives = Array();
		
		for (var i in requiredThrust){
		
			if (stillReq[i] == null || stillReq[i] <= 0)
				continue;
		
		/*
			if (requiredThrust[i] == null || requiredThrust[i] <= 0 )
				continue;
		*/		
		
			if (objective == "")
				objective = "You need to assign ";
				
			objectives.push(stillReq[i] + " thrust to " + names[i] + " thrusters");
			if (stillReq[i] > 0)
				done = false;
		}
		
		for (var i in objectives){
		
		
			if ( i < objectives.length-1 && i != 0 ){
				objective += ", ";
			}else if (i != 0){
				objective += " and ";
			}
			
			objective += objectives[i];
			
			if (i == objectives.length-1){
				objective += ".";
			}
		}

		
		if (shipManager.movement.isTurn(movement)){
			var turndelay = shipManager.movement.calculateTurndelay(ship, movement, movement.speed);
			additionally = " Additionally, you can assign extra thrust to lower the turn delay to minimum of 1. Current turndelay of this turn will be " 
			+ (turndelay) + ".";
		}
		
		var shipwindow = ship.shipStatusWindow;
		
		
		var obe = $("#logcontainer .assignthrustcontainer .thrustobjective");
		obe.html(objective + additionally);
		
		var cont = $("#logcontainer .assignthrustcontainer");
		cont.data("ship", ship.id);
		if (done){
			
			cont.removeClass("red");
			cont.addClass("green");
		}else{
			cont.removeClass("green");
			cont.addClass("red");
		}
		cont.addClass("assignThrust");
		$("#botPanel").addClass("assignThrust");
		$("#logContainer").addClass("assignThrust");

		$(".thruster", shipwindow).each(function(){
			var direction = $(this).data("direction");
			
			if (requiredThrust[direction] != null){
				$(this).addClass("enableAssignThrust");
				
			}
			if (stillReq[direction] == null){
				$(this).removeClass("enableAssignThrust");
			}
			
		});
		
		botPanel.setSystemsForAssignThrust(ship, requiredThrust, stillReq);
		
		


		shipwindow.addClass("assignThrust");
	},

	selectAllGuns: function(e){

		var array = [];

		e.stopPropagation();
		var shipwindow = $(".shipwindow").has($(this));
		var systemwindow = $(this);
		var ship = gamedata.getShip(shipwindow.data("ship"));
		var system = shipManager.systems.getSystem(ship, systemwindow.data("id"));

		for (var i = 0; i < ship.systems.length; i++){
			if (system.displayName === ship.systems[i].displayName){
				array.push(ship.systems[i]);
			}
		}

		var selectedShip = gamedata.getSelectedShip();

		for (var i = 0; i < array.length; i++){
			var system = array[i];

			if (gamedata.waiting)
				return
			
			if (shipManager.isDestroyed(ship)  || shipManager.isAdrift(ship)){
				return;
			}

			if (system.destroyed){
				continue;
			}

			if (system.weapon && selectedShip.id == ship.id){
				
				if (gamedata.gamephase != 3 && !system.ballistic)
					return;
				
				if (gamedata.gamephase != 1 && system.ballistic)
					return;
			
				if (weaponManager.isSelectedWeapon(system)){
					weaponManager.unSelectWeapon(ship, system);
				}else{
					weaponManager.selectWeapon(ship, system);
				}
				
			}
			
			if (gamedata.isEnemy(ship, selectedShip) 
				&& gamedata.gamephase == 3 
				&& gamedata.selectedSystems.length > 0 
				&& weaponManager.canCalledshot(ship, system))
			{
				weaponManager.targetShip(ship, system);
			}
		}
	},
	
	clickSystem: function(e){
		e.stopPropagation();
		var shipwindow = $(".shipwindow").has($(this));
		var systemwindow = $(this);
		var ship = gamedata.getShip(shipwindow.data("ship"));
		var system = shipManager.systems.getSystem(ship, systemwindow.data("id"));
                system = shipManager.systems.initializeSystem(system);
                
	//	console.log(system);
		var selectedShip = gamedata.getSelectedShip();
		
		if (gamedata.waiting)
			return
		
		if (shipManager.isDestroyed(ship) || shipManager.isDestroyed(ship, system) || shipManager.isAdrift(ship))
			return;
					
		if (system.weapon && selectedShip.id == ship.id){
			
			if (gamedata.gamephase != 3 && !system.ballistic)
				return;
			
			if (gamedata.gamephase != 1 && system.ballistic)
				return;
		
			if (weaponManager.isSelectedWeapon(system)){
				weaponManager.unSelectWeapon(ship, system);
			}else{
				weaponManager.selectWeapon(ship, system);
			}
			
		}
		
		if (gamedata.isEnemy(ship, selectedShip) 
			&& gamedata.gamephase == 3 
			&& gamedata.selectedSystems.length > 0 
			&& weaponManager.canCalledshot(ship, system))
		{
			weaponManager.targetShip(ship, system);
		}
	
	},
	
	clickPlus: function(e){
		e.stopPropagation();
		var shipwindow = $(".shipwindow").has($(this));
		var systemwindow = $(".system").has($(this));
		var ship = gamedata.getShip(shipwindow.data("ship"));
		var system = ship.systems[systemwindow.data("id")];


		if (shipManager.isDestroyed(ship) || shipManager.isDestroyed(ship, system))
			return;
		
		if (ship.userid != gamedata.thisplayer)
			return;
		
		if (gamedata.gamephase == 2 && shipwindow.hasClass("assignThrust") && system.name == "thruster"){
			shipManager.movement.assignThrust(ship, system);
			shipWindowManager.assignThrust(ship);
		}
		
		if(system.weapon && system.canChangeShots && ((system.ballistic && gamedata.gamephase == 1) || (!system.ballistic && gamedata.gamephase == 3))){
			weaponManager.changeShots(ship, system, 1);
		}else if (gamedata.gamephase == 1){
			shipManager.power.clickPlus(ship, system);
		}
		
	},
	
	clickMinus: function(e){
		e.stopPropagation();
		var shipwindow = $(".shipwindow").has($(this));
		var systemwindow = $(".system").has($(this));
		var ship = gamedata.getShip(shipwindow.data("ship"));
		var system = ship.systems[systemwindow.data("id")];
		
		if (shipManager.isDestroyed(ship) || shipManager.isDestroyed(ship, system))
			return;
		
		if (ship.userid != gamedata.thisplayer)
			return;
		
		if (gamedata.gamephase == 2 && shipwindow.hasClass("assignThrust") && system.name == "thruster"){
			shipManager.movement.unAssignThrust(ship, system);
			shipWindowManager.assignThrust(ship);
		}
		
		if(system.weapon && system.canChangeShots && ((system.ballistic && gamedata.gamephase == 1) || (!system.ballistic && gamedata.gamephase == 3))){
			weaponManager.changeShots(ship, system, -1);
		}else if (gamedata.gamephase == 1){
			shipManager.power.clickMinus(ship, system);
		}

		
	},
	
	doneAssignThrust: function(){

		var shipwindow = $(".assignthrustcontainer").has($(this));
		var ship = gamedata.getShip(shipwindow.data("ship"));
		var movement = ship.movement[ship.movement.length-1];;
		var requiredThrust = movement.requiredThrust;
		var stillReg = shipManager.movement.calculateThrustStillReq(ship, movement);
		
		var done = true;
		for (var i in stillReg){

			if (stillReg[i] > 0)
				done = false;
		}
		
		if (done){
			movement.commit = true;
			$(".assignThrust").removeClass("assignThrust");
			$(".enableAssignThrust").removeClass("enableAssignThrust");
			$("#botPanel .exists").removeClass("exists");
			shipWindowManager.setData(ship);
			shipManager.drawShip(ship);
			gamedata.shipStatusChanged(ship);
		}
		
		
		
		
	},
	
	cancelAssignThrustEvent: function(){

		var e = $(".shipwindow").has($(this));
		
		
		
			
		if (!e.length)
			e = $(".assignthrustcontainer").has($(this));
			
		if (!e.length || !e.hasClass("assignThrust"))
			return;

	
		shipWindowManager.cancelAssignThrust(e);
	},
	
	cancelAssignThrust: function(element){
		if (!element || !element.hasClass("assignThrust"))
			return;
			
		$(".assignThrust").removeClass("assignThrust");
		$(".enableAssignThrust").removeClass("enableAssignThrust");
		$("#botPanel .exists").removeClass("exists");
		
		var ship = gamedata.getShip(element.data("ship"));
				
		if (!ship)
			return;

		ship.movement.splice(ship.movement.length -1, 1);
	
		
		shipWindowManager.setData(ship);
		shipManager.drawShip(ship);
	},


/* plopje        initializeDuoSystemWindow: function(ship, system, systemwindow){
            
            
            if(system.weapons[system.firingMode].duoWeapon){
                shipWindowManager.addDuoSystem(ship, system.weapons[system.firingMode], systemwindow);
            }
            else{
                shipWindowManager.addRegularSystem(ship, system.weapons[system.firingMode], systemwindow);
            }
        },*/
        
        onModeClicked: function(e){
            e.stopPropagation();

            var shipwindow = $(".shipwindow").has($(this));
            var systemwindow = $(".system").has($(this));
            
            if(shipwindow.length == 0 || systemwindow.length == 0){
                // I have no idea why a mode switch should generate so many
                // events. Just let the ones through that seem to make sense.
                // by Jazz
                return;
            }

            var ship = gamedata.getShip(shipwindow.data("ship"));
            var system = shipManager.systems.getSystem(ship, systemwindow.data("id"));

            window.weaponManager.onModeClicked(shipwindow, systemwindow, ship, system);

        }   
}
