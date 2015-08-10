gamedata = {

    gamewidth: 1600,
    gameheight: 1000,
    zoom: 0.6,
    zoomincrement: 0.1,
    scroll:  {x:0,y:0},
    scrollOffset: {x:0,y:0},
    animating: false,
    ships: Array(),
    ballistics: Array(),
    thisplayer: 1,
    waiting: false,
    selectedShips: Array(),
    targetedShips: Array(),
    selectedSystems: Array(),
    effectsDrawing: false,
    finished: false,
    gamephase: 0,
    subphase: 0,
    selectedSlot:null,
    gamespace: null,
    
    mouseOverShipId: -1,
    
        
    selectShip: function(ship, add){
        if (!add){
            for (var i in gamedata.selectedShips){
                var s2 = gamedata.selectedShips[i];
                gamedata.unSelectShip(s2);
            }
            gamedata.selectedShips = Array();
            
        }
            
        
        
        if (!gamedata.isSelected(ship)){   
            gamedata.selectedShips.push(ship);
            if (gamedata.gamephase == 1)
                ew.adEWindicators(ship);
            
            gamedata.shipStatusChanged(ship);
            shipWindowManager.checkIfAnyStatusOpen(ship);
            gamedata.selectedSystems = Array();
           
        } 
        
        
    },
    
    targetShip: function(ship, add){
        if (!add){
            for (var i in gamedata.targetedShips){
                var s2 = gamedata.targetedShips[i];
                gamedata.unTargetShip(s2);
            }
            gamedata.targetedShips = Array();
            
        }
            
        
        
        if (!gamedata.isTargeted(ship)){   
            gamedata.targetedShips.push(ship);
            
                
            shipWindowManager.checkShipWindow(ship);
        } 
        
    },
    
    elintShips: Array(),
    
    getElintShips: function(){
        if (gamedata.elintShips.length === 0){
            for (var i in gamedata.ships){
                var ship = gamedata.ships[i];
                if (shipManager.isElint(ship))
                    gamedata.elintShips.push(ship);
            }
        }
        return gamedata.elintShips;
    },
    
    unTargetShip: function(ship){
        
    },
    
    unSelectShip: function(ship){
        if (gamedata.gamephase == 3)
            UI.shipMovement.hide();
        ew.RemoveEWEffectsFromShip(ship);
        gamedata.selectedSystems = Array();
    },
    
    isTargeted: function(ship){
        if ($.inArray(ship, gamedata.targetedShips) >= 0)
            return true;
            
        return false;
    },
    
    isSelected: function(ship){
        if ($.inArray(ship, gamedata.selectedShips) >= 0)
            return true;
            
        return false;
    },
    
    getSelectedShip: function(){
        for (var i in gamedata.selectedShips){
            return gamedata.selectedShips[i];
            
        }
        
        return false;
    },
    
    getTargetedShip: function(){
        for (var i in gamedata.targetedShips){
            return gamedata.targetedShips[i];
            
        }
    },
    
    getActiveShip: function(){
        return gamedata.getShip(gamedata.activeship);
            
    },
    
    getShip: function(id){
        for (var i in gamedata.ships){
      //      console.log(gamedata.ships[i].id);
            if (gamedata.ships[i].id == id){
                return gamedata.ships[i];
            }
        }
        
        return null;
    
    },
    
    isMyShip: function(ship){
        return (ship.userid == gamedata.thisplayer);
    },
    
    isEnemy: function(target, shooter){
		if (!shooter && gamedata.getSelectedShip()){
			shooter = gamedata.getSelectedShip();
		}else if(!shooter){
			//console.log("isEnemy called. shooter is null and no ship selected");
			//console.trace();
			return false;
		}
		
		
		var ret = (target.team != shooter.team); 
		return ret;
	},
    
    shipStatusChanged: function(ship){
        botPanel.onShipStatusChanged(ship);
        shipWindowManager.setData(ship);
        gamedata.checkGameStatus();
        hexgrid.unSelectHex();
    },
    
    onCommitClicked: function(e){
        
        if (gamedata.waiting == true)
            return;     
        
        if(gamedata.status == "FINISHED")
            return;

// CHECK for NO EW
        if (gamedata.gamephase == 1){
            var myShips = [];

            for (var ship in gamedata.ships){
                if (gamedata.ships[ship].userid == gamedata.thisplayer){
                    if (!shipManager.isDestroyed(gamedata.ships[ship])){
                        myShips.push(gamedata.ships[ship]);
                    }
                }
            }



            var hasNoEW = [];

            for (var ship in myShips){
                if (!myShips[ship].flight){
                    if (gamedata.turn == 1){
                        if (myShips[ship].EW.length == 0){
                            hasNoEW.push(myShips[ship]);
                        }
                    }
                    else if (gamedata.turn > 1 && myShips[ship].EW.length < 3){
                        hasNoEW.push(myShips[ship]);    
                    }
                }
            }

            if (hasNoEW.length == 0){
                confirm.confirm("Are you sure you wish to COMMIT YOUR INITIAL ORDERS?", gamedata.doCommit);
            }
            else {
                var html = "You have not assigned any EW for the following ships: ";
                    html += "<br>";
                for (var ship in hasNoEW){
                    html += hasNoEW[ship].name + " (" + hasNoEW[ship].shipClass + ")";
                    html += "<br>";
                }
                confirm.confirm((html + "<br>Are you sure you wish to COMMIT YOUR INITIAL ORDERS?"), gamedata.doCommit);
            }
        }


// CHECK for NO FIRE
        else if(gamedata.gamephase == 3){
            var myShips = [];

            for (var ship in gamedata.ships){
                if (gamedata.ships[ship].userid == gamedata.thisplayer){
                    if (!shipManager.isDestroyed(gamedata.ships[ship])){
                        myShips.push(gamedata.ships[ship]);
                    }
                }
            }

            var hasNoFO = [];

            for (var ship in myShips){
                var fired = 0;

                if (!myShips[ship].flight){
                    for (var i = 0; i < myShips[ship].systems.length; i++){
                        if (myShips[ship].systems[i].fireOrders.length > 0){
                            fired = 1;
                            break;
                        }
                    }
                    if (fired == 0){
                        hasNoFO.push(myShips[ship]);
                    }
                }

                else if (myShips[ship].flight){
                    for (var i = 0; i < myShips[ship].systems.length; i++){
                        if (typeof myShips[ship].systems[i] != "undefined"){
                            for (var j = 0; j < myShips[ship].systems[i].systems.length; j++){
                                if (typeof myShips[ship].systems[i].systems[j] != "undefined"){
                                    if (myShips[ship].systems[i].systems[j].fireOrders.length > 0){
                                        fired = 1;
                                        break;
                                    }
                                }
                            }
                        }
                    }
                    if (fired == 0){
                        hasNoFO.push(myShips[ship]);
                    }
                }
            }

            if (hasNoFO.length == 0){
                confirm.confirm("Are you sure you wish to COMMIT YOUR FIRE ORDERS?", gamedata.doCommit);
            }
            else {
                var html = "You have not assigned any fire orders for the following ships: ";
                    html += "<br>";
                for (var ship in hasNoFO){
                    html += hasNoFO[ship].name + " (" + hasNoFO[ship].shipClass + ")";
                    html += "<br>";
                }
                confirm.confirm((html + "<br>Are you sure you wish to COMMIT YOUR FIRE ORDERS?"), gamedata.doCommit);
            } 
        }
        
        else if(gamedata.gamephase != 4){
            confirm.confirm("Are you sure you wish to COMMIT YOUR TURN?", gamedata.doCommit);
//            if (window.helper.autocomm!=true) {
//	            confirm.confirm("Are you sure you wish to COMMIT YOUR TURN?", gamedata.doCommit);
//            } else {
//            	gamedata.doCommit();
//            }	
        }
        else{
            confirm.confirmOrSurrender("Are you sure you wish to COMMIT YOUR TURN?", gamedata.doCommit, gamedata.onSurrenderClicked);
//            if (window.helper.autocomm!=true) {
//	            confirm.confirmOrSurrender("Are you sure you wish to COMMIT YOUR TURN?", gamedata.doCommit, gamedata.onSurrenderClicked);
//            } else {
//	            confirm.askSurrender("Do you wish to SURRENDER?", gamedata.doCommit, gamedata.onSurrenderClicked);
//            }	
        }
    },
    
    onSurrenderClicked: function(e){
        confirm.confirm("Are you sure you wish to SURRENDER THIS MATCH?", gamedata.doSurrender);
    },
    
    doSurrender: function(){
        UI.shipMovement.hide();
        
        gamedata.status = "SURRENDERED";
        ajaxInterface.submitGamedata();
    },
    
    doCommit: function(){
        UI.shipMovement.hide();
        if (gamedata.gamephase == 1){
//        	ajaxInterface.fastpolling=true;
            var shipNames = shipManager.power.getShipsNegativePower();
            
            if (shipNames.length > 0){
                var negPowerError = "The following ships have insufficient power:<br>";
                
                for(var index in shipNames){
                    var name = shipNames[index];
                    negPowerError += "- " + name + "<br>";
                }
                negPowerError += "You need to turn off systems before you can commit the turn.";
                window.confirm.error(negPowerError, function(){});
                return false;
            }
            
            shipNames = shipManager.power.getShipsGraviticShield();
            
            if (shipNames.length > 0){
                var tooManyShieldsError = "The following ships have too many active shields:<br>";
                
                for(var i in shipNames){
                    var shipName = shipNames[i];
                    tooManyShieldsError += "- " + shipName + "<br>";
                }
                tooManyShieldsError += "You need to turn off shields or boost your shield generator before you can commit the turn.";
                window.confirm.error(tooManyShieldsError, function(){});
                return false;
            }
            
            for (var i in gamedata.ships){
                var ship = gamedata.ships[i];
                ew.convertUnusedToDEW(ship);
            }
            
            ajaxInterface.submitGamedata();
            
        }else if (gamedata.gamephase == 2){
            var ship = gamedata.getActiveShip();
            if (shipManager.movement.isMovementReady(ship)){
                combatLog.logMoves(ship);
                shipManager.movement.RemoveMovementIndicators();
                ajaxInterface.submitGamedata();
            }else{
                return false;
            }
        }else if (gamedata.gamephase == 3){
            ajaxInterface.submitGamedata();
        }else if (gamedata.gamephase == 4){
            ajaxInterface.submitGamedata();
        }else if (gamedata.gamephase == -1){
            ajaxInterface.submitGamedata();
        }
        
    
            
    },
    
    onCancelClicked: function(e){
        if (gamedata.gamephase == 2){
            var ship = gamedata.getActiveShip();
            shipManager.movement.deleteMove(ship);
        }
        
        if (gamedata.gamephase == 3){
            var ship = gamedata.getSelectedShip();
            shipManager.movement.deleteMove(ship);
        }
    },
    
    getActiveShipName: function(){
        var ship = gamedata.getActiveShip();
        if (ship)
            return ship.name;
            
        return "";
    },
    
    getPlayerTeam: function(){
        for (var i in gamedata.slots){
            var slot = gamedata.slots[i];
            if (slot.playerid = gamedata.thisplayer)
                return slot.team;
        }
    },
    
    getPlayerNameById: function(id){
        for(var i in gamedata.slots){
            var slot = gamedata.slots[i];
            if(slot.playerid = id){
                return slot.playername;
            }
        }
    },
    
    getPhasename: function(){
        if (gamedata.gamephase == 1)
            return "INITIAL ORDERS";
            
        if (gamedata.gamephase == 2)
            return "MOVEMENT ORDERS:";
        
        if (gamedata.gamephase == 3)
            return "FIRE ORDERS";
            
        if (gamedata.gamephase == 4)
            return "FINAL ORDERS";
        
        if (gamedata.gamephase == -1)
            return "DEPLOYMENT";
            
        return "ERROR"
    },
    
    setPhaseClass: function(){
    
        var b = $("body");
        
        b.removeClass("phase1");
        b.removeClass("phase2");
        b.removeClass("phase3");
        b.removeClass("phase4");
        b.removeClass("phase-1");
    
        b.addClass("phase"+gamedata.gamephase);
    },
    
    initPhase: function(){
		gamedata.subphase = 0;
        shipManager.initShips();
        UI.shipMovement.hide();
        fleetListManager.displayFleetLists();
        
        gamedata.setPhaseClass();
        for (var i in gamedata.ships){
            gamedata.shipStatusChanged(gamedata.ships[i]);
        }
//		window.helper.doUpdateHelpContent(gamedata.gamephase,0);        
        if (gamedata.gamephase == -1){
            if (gamedata.waiting == false){
                combatLog.onTurnStart();
                infowindow.informPhase(5000, null);
                for (var i in gamedata.ships){
                    var ship = gamedata.ships[i];
                    if (ship.userid == gamedata.thisplayer && !shipManager.isDestroyed(ship)){
                        gamedata.selectShip(ship, false);
                        scrolling.scrollToShip(ship);
                        break;
                    }
                }
            }            
        }
        
        if (gamedata.gamephase == 4){
            if (gamedata.waiting == false){
                effects.displayAllWeaponFire(function(){
					gamedata.subphase = 1;
                    damageDrawer.checkDamages();
                    infowindow.informPhase(5000, null);
                    
                    });
            }else{
                gamedata.subphase = 1;
                damageDrawer.checkDamages();
            }
            
            fleetListManager.updateFleetList();
        }
        
               
        if (gamedata.gamephase == 2){
			$(".ballclickable").remove();
			$(".ballisticcanvas").remove();
            ew.RemoveEWEffects();
            animation.setAnimating(animation.animateShipMoves, function(){
                infowindow.informPhase(5000, null);
                scrolling.scrollToShip(gamedata.getActiveShip());
                shipWindowManager.checkIfAnyStatusOpen(gamedata.getActiveShip());
                
                var ship = gamedata.getActiveShip();
                
                if (ship.userid == gamedata.thisplayer){
                    shipManager.movement.doForcedPivot(ship);
                    gamedata.selectShip(ship, false);
                }
           
            });
        }
          
        if (gamedata.gamephase == 1 && gamedata.waiting == false){
            shipManager.power.repeatLastTurnPower();
            infowindow.informPhase(5000, function(){shipWindowManager.prepare()});
            if (gamedata.waiting == false){
                for (var i in gamedata.ships){
                    var ship = gamedata.ships[i];
                    if (ship.userid == gamedata.thisplayer && !shipManager.isDestroyed(ship)){
                        gamedata.selectShip(ship, false);
                        scrolling.scrollToShip(ship);
                        break;
                    }
                }
            }
        }
        
        if (gamedata.gamephase == 3 && gamedata.waiting == false){
            UI.shipMovement.hide();
            ew.RemoveEWEffects();
            animation.setAnimating(animation.animateShipMoves, function(){
                infowindow.informPhase(5000, null);
                                
                if (gamedata.waiting == false){
                    for (var i in gamedata.ships){
                        var ship = gamedata.ships[i];
                        if (ship.userid == gamedata.thisplayer && !shipManager.isDestroyed(ship)){
                            gamedata.selectShip(ship, false);
                            scrolling.scrollToShip(ship);
                            break;
                        }
                    }
                }
                
        
            });

            
        }
        ballistics.initBallistics();
       
        
        if (gamedata.waiting){
            ajaxInterface.startPollingGamedata();
        }
    },


    drawIniGUI: function(){


        var ini_gui = document.getElementById("iniGui");
            ini_gui.innerHTML = "";
            ini_gui.style.height = "auto";

        var topicDiv = document.createElement("div");
            topicDiv.className = "topicDiv";

        var span = document.createElement("span");
            span.id = "iniTopic";
            span.innerHTML = "Order of Battle";

        topicDiv.appendChild(span);

        ini_gui.appendChild(topicDiv);


        var allShips = gamedata.ships;
        var ships = [];

        for (var i = 0; i < allShips.length; i++){
            if (!shipManager.isDestroyed(allShips[i])){
                ships.push(allShips[i]);
            }
        }
        
        ships.sort(function(a, b){
            if (a.iniative > b.iniative){
                return 1;
            } else return -1;
        })
        var table = document.createElement("table");
            table.id = "iniTable";

        for (var i = 0; i < ships.length; i++){

            var tr = document.createElement("tr");
                tr.className = "iniTr";
                tr.id = ships[i].id;

                tr.addEventListener("click", function(){
                    for (var i = 0; i < gamedata.ships.length; i++){
                        if (gamedata.ships[i].id == this.id){
                            scrolling.scrollToShip(gamedata.ships[i]);
                            break;
                        }
                    }
                })

            var td = document.createElement("td");
                td.position = "relative";
                td.style.width = "10%";
                td.id = "iniTd";
                td.style.textAlign = "center";
                td.style.fontSize = "18px";
                td.innerHTML = i+1;

                if (gamedata.isMyShip(ships[i])){
                    td.style.color = "green";
                } else td.style.color = "red";

            tr.appendChild(td);


            var td = document.createElement("td");
                td.position = "relative";
                td.style.width = "60%";
                td.id = "iniTd";

            var span = document.createElement("span");
                span.style.textAlign = "center";
                span.style.fontSize = "12px";
                span.innerHTML += "<p style='margin-top: 6px; margin-bottom: 6px'>" + ships[i].name;
                span.innerHTML += "<p style='margin-top: 6px; margin-bottom: 6px'>" + ships[i].shipClass;

            if (gamedata.activeship == ships[i].id){
                td.className = "iniActive";
            }

                td.appendChild(span);

            tr.appendChild(td);

            var td = document.createElement("td");
                td.position = "relative";
                td.style.width = "20%";
                td.id = "iniTd";

            var img = document.createElement("img");
                img.src = ships[i].imagePath;

                if (!ships[i].flight){
                    img.style.width = "40px";
                    img.style.height = "40px";
                }
                else {
                    img.style.width = "20px";
                    img.style.height = "20px";
                    td.style.paddingLeft = "12px";
                }

            td.appendChild(img)
            tr.appendChild(td);

            table.appendChild(tr);
        }


            ini_gui.appendChild(table);


        var backDiv = document.getElementById("backDiv");
            backDiv.innerHTML = "";
            backDiv.style.paddingBottom = "10px";
            $(backDiv).removeData();

            var img = new Image();
                img.id = "iniSlider";
                img.src = "img/pullIn.png";
                img.style.width = "30px";
                img.style.height = "30px";
                img.style.marginLeft = "12px";



            backDiv.appendChild(img);

            backDiv.addEventListener("click", gamedata.sliderToggle);




    },

    sliderToggle: function(){
        var backDiv = document.getElementById("backDiv");

        if ($(backDiv).data("on") == 0){
            $("#iniGui").show();
            $(backDiv).data("on", 1);
            backDiv.style.marginLeft = "250px";
            document.getElementById("iniSlider").src = "img/pullIn.png";

        }
        else {
            $("#iniGui").hide();
            $(backDiv).data("on", 0);
            backDiv.style.marginLeft = "0px";
            document.getElementById("iniSlider").src = "img/pullOut.png";
        }
    },
            
    checkGameStatus: function(){

        $("#phaseheader .turn.value").html("TURN: " + gamedata.turn+ ",");
        $("#phaseheader .phase.value").html(gamedata.getPhasename());
        $("#phaseheader .activeship.value").html(gamedata.getActiveShipName());


        var commit = $(".committurn");
        var cancel = $(".cancelturn");
        
        if (gamedata.status == "FINISHED"){
            cancel.hide();
            commit.hide();
            $("#phaseheader .finished").show();
            return;
        }
        
        if (gamedata.gamephase == -1){
            if (deployment.validateAllDeployment() && !gamedata.waiting){
                commit.show();
                return;
            }
        }
        
        if (gamedata.gamephase == 4){
            
            commit.show();
            cancel.hide();
            
        }else if (gamedata.gamephase == 3){
            
            commit.show();
            var ship = gamedata.getSelectedShip();
           
                
            
        }else if (gamedata.gamephase == 2){
            var ship = gamedata.getActiveShip();
            if (shipManager.movement.isMovementReady(ship) && gamedata.isMyShip(ship)){
                commit.show();
            }else{
                commit.hide();
            }
            
            
        }else if (gamedata.gamephase == 1){
            
            commit.show();
            cancel.hide();
            
        }else{
            commit.hide();
            cancel.hide();
        }
        
        if (!playerManager.isInGame()){
            cancel.hide();
            commit.hide();
            return;
        }
        
        if (gamedata.waiting){
            $("#phaseheader .waiting.value").show();
            cancel.hide();
            commit.hide();
        }else{
            $("#phaseheader .waiting.value").hide();
        }
        
        cancel.hide();
    },
    
    goToWaiting: function(){
        if (gamedata.waiting == false){
            gamedata.waiting = true;
            ajaxInterface.startPollingGamedata();
            gamedata.checkGameStatus();
        }
    },

    parseServerData: function(serverdata){
    
        if (serverdata == null)
            return;
        
        if (!serverdata.id)
            return;
            
        if (gamedata.waiting == false && serverdata.waiting == true && serverdata.changed == false){
             gamedata.waiting = true;
             ajaxInterface.startPollingGamedata();
        }
            
    
        if (serverdata.changed == true){               
                
            //console.log(serverdata);
            gamedata.turn = serverdata.turn;
            gamedata.gamephase = serverdata.phase;
            gamedata.activeship = serverdata.activeship;
            gamedata.gameid = serverdata.id;
            gamedata.slots = serverdata.slots;
            
            gamedata.thisplayer = serverdata.forPlayer;
            gamedata.waiting = serverdata.waiting;
            gamedata.status = serverdata.status;
            gamedata.ballistics = serverdata.ballistics;
            gamedata.elintShips = Array();
            gamedata.gamespace = serverdata.gamespace;
            shipManager.initiated = 0;

            
            gamedata.setShipsFromJson(serverdata.ships);
            
            
            gamedata.initPhase();
            drawEntities();
            gamedata.drawIniGUI();

        }
        gamedata.checkGameStatus();



    },
            
    setShipsFromJson: function(jsonShips)
    {
        //gamedata.ships = Array();
        
        for (var i in jsonShips)
        {
            var ship = jsonShips[i];
            gamedata.ships[i] = new Ship(ship);
        }
    },
    
    listShipPositions: function(){
        
        scrolling.scrollTo(0,0);
        for (var i in gamedata.ships){
            console.log(shipManager.getShipPositionInWindowCo(gamedata.ships[i]));
        }
    }
    
}


