window.shipManager = {

    shipImages: Array(),
    initiated: 0,

    initShips: function(){
        if (window.webglScene) {
            return;
        }

        shipManager.initiated = 1;
        for (var i in gamedata.ships){
            shipManager.createHexShipDiv(gamedata.ships[i]);
        }
        shipManager.initiated = 2;
        shipManager.drawShips();
    },

    createHexShipDiv: function(ship){


        if (ship.htmlContainer)
            return;

        var e = $("#pagecontainer #hexship_"+ship.id+".hexship");

        if (!e.length){


            e = $("#templatecontainer .hexship");
            e.attr("id", "hexship_" + ship.id);
            var s = shipManager.getShipCanvasSize(ship);
            var w = s;
            var h = s;
            $("canvas.hexshipcanvas", e).attr("id", "shipcanvas_"+ship.id).attr("width", w).attr("height", h);
            var n = e.clone(true).appendTo("#pagecontainer");
            n.data("ship", ship.id);
            ship.htmlContainer = $("#pagecontainer #hexship_"+ship.id);
            ship.shipclickableContainer = $('<div oncontextmenu="shipManager.onShipContextMenu(this);return false;" class="shipclickable ship_'+ship.id+'"></div>').appendTo("#pagecontainer");
            ship.shipclickableContainer.data("id", ship.id);
            ship.shipclickableContainer.on("dblclick", shipManager.onShipDblClick);
            ship.shipclickableContainer.on("click", shipManager.onShipClick);
            ship.shipclickableContainer.on("mouseover", shipClickable.shipclickableMouseOver);
            ship.shipclickableContainer.on("mouseout", shipClickable.shipclickableMouseOut);
            if (ship.flight){
		        ship.shipStatusWindow = flightWindowManager.createShipWindow(ship);
            }else{
                ship.shipStatusWindow = shipWindowManager.createShipWindow(ship);
            }
            
            shipWindowManager.setData(ship);
            $("canvas.hexshipcanvas", e).attr("id", "shipcanvas_");
            e.attr("id", "hexship_");
            var img = new Image();
            img.src = ship.imagePath;
            shipManager.shipImages[ship.id] = {
                orginal:img,
                modified:null,
                rolled:null,
                drawData:Array()
            };
            $(shipManager.shipImages[ship.id].orginal).on("load", function(){
                shipManager.shipImages[ship.id].orginal.loaded = true;
            });

        }else{
            ship.htmlContainer = e;
            ship.shipclickableContainer = $(".shipclickable.ship_"+ship.id);
            ship.shipStatusWindow = $(".shipwindow.ship_"+ship.id);
            shipWindowManager.setData(ship);
        }

        if (shipManager.isDestroyed(ship))
            ship.dontDraw = true;
    },

    drawShips: function(){

        if (shipManager.initiated == 0){
            shipManager.initShips();
            return;
        }

        if (shipManager.initiated == 1)
            return;

        for (var i in gamedata.ships){
            shipManager.drawShip(gamedata.ships[i]);
        }
    },

    /*
    drawShip: function(ship){

        if (shipManager.initiated == 0){
            shipManager.initShips();
            return;
        }

        if (shipManager.initiated == 1)
            return;

        if (gamedata.gamephase != -1){
            if (ship.dontDraw || ship.unavailable){
                ship.shipclickableContainer.css("z-index", "1");
                ship.htmlContainer.hide();
                return;
            }   
        }
        //graphics.clearCanvas("shipcanvas_" + ship.id);
        var canvas = window.graphics.getCanvas("shipcanvas_" + ship.id);

        canvas.fillStyle    = hexgrid.hexlinecolor;;
        canvas.font         = 'italic 12px sans-serif';
        canvas.textBaseline = 'top';

        var pos = shipManager.getShipPositionForDrawing(ship);



        var s = shipManager.getShipCanvasSize(ship);
        var h = Math.round(s/2)
        var hexShipZ = 1000; //+ship.id;
        var scZ = 4500;//+ship.id;
        if (gamedata.mouseOverShipId == ship.id){
            hexShipZ+=500;
            scZ+=500;
        }

        if (gamedata.activeship == ship.id || gamedata.isSelected(ship)){
            hexShipZ+=250;
            scZ+=250;
        }
        ship.htmlContainer.css("top", pos.y -h + "px").css("left", pos.x -h + "px").css("z-index", hexShipZ);
        ship.htmlContainer.show();

        var img = damageDrawer.getShipImage(ship);

        var sc = ship.shipclickableContainer;
        scSize = s*0.15*gamedata.zoom;
        sc.css("width", scSize+"px");
        sc.css("height", scSize+"px");
        sc.css("left", ((pos.x) - (scSize*0.5))+"px");
        sc.css("top", ((pos.y) - (scSize*0.5))+"px");
        sc.css("z-index", scZ);



        //console.log("gamedata.gamephase: " + gamedata.gamephase + " gamedata.activeship: " + gamedata.activeship + " ship.id: " + ship.id);
        if (gamedata.gamephase == 2 && gamedata.activeship == ship.id && gamedata.animating == false && gamedata.waiting == false && gamedata.isMyShip(ship))
            UI.shipMovement.drawShipMovementUI(ship);

        if (gamedata.gamephase == -1 && gamedata.isMyShip(ship) && gamedata.isSelected(ship))
            UI.shipMovement.drawShipMovementUI(ship);

        if (gamedata.gamephase == 3 && ship.flight && gamedata.isSelected(ship))
            UI.shipMovement.drawShipMovementUI(ship);

        if (img.loaded){
            shipManager.doDrawShip(canvas, s, ship, img);
        }else{
            $(img).on("load", function(){
                img = damageDrawer.getShipImage(ship);
                if (img.loaded){
                    shipManager.doDrawShip(canvas, s, ship, img);
                }else{
                    $(img).on("load", function(){
                        img = damageDrawer.getShipImage(ship);
                        shipManager.doDrawShip(canvas, s, ship, img);
                    });
                }
            });
        }

    },

    doDrawShip: function(canvas, s, ship, img){
        var dew = ew.getDefensiveEW(ship);
        if (ship.flight)
			dew = shipManager.movement.getJinking(ship);

        var ccew = ew.getCCEW(ship);

        var shipdrawangle = shipManager.getShipHeadingAngleForDrawing(ship);
        var selected = gamedata.isSelected(ship);
        var mouseover = (gamedata.mouseOverShipId == ship.id);

        if (ship.drawn && shipdrawangle == ship.shipdrawangle && ship.drawnzoom == gamedata.zoom
			&& ship.drawmouseover == mouseover && ship.drawselected == selected && ship.drawDamage == false
			&& ship.drawDEW == dew && ship.drawCCEW == ccew)
		{
			return;
        }


        var myship = gamedata.isMyShip(ship);
        //console.log("draw");
        canvas.clearRect(0, 0, s, s);

		if ((selected && myship && gamedata.gamephase == 1) || (mouseover && gamedata.gamephase > 1) || (mouseover && myship)){
			if (gamedata.zoom > 0){

				if (dew > 0){
					dew = Math.ceil(( dew )*gamedata.zoom*0.5);
					canvas.strokeStyle = "rgba(144,185,208,0.40)";
					graphics.drawCircle(canvas, s/2, s/2, s*0.18*gamedata.zoom, dew);
				}

				if (ccew > 0){
					ccew = Math.ceil(( ccew )*gamedata.zoom*0.5);
                    if (myship)
                    {
                        canvas.strokeStyle = "rgba(20,80,128,0.50)";
                    }
                    else
                    {
                        canvas.strokeStyle = "rgba(179,65,25,0.50)";
                    }

					graphics.drawCircle(canvas, s/2, s/2, ((s*0.18*gamedata.zoom)+(dew*0.5) + (ccew*0.5) + 2), ccew);
				}
			}

		}

		if (selected && !mouseover && !(gamedata.gamephase == 2 && ship.id == gamedata.activeship)) {
			canvas.strokeStyle = "rgba(144,185,208,0.40)";
			canvas.fillStyle = "rgba(255,255,255,0.18)";

			graphics.drawCircleAndFill(canvas, s/2, s/2, s*0.15*gamedata.zoom+1, 1);
		}else if ( mouseover ){

			if (gamedata.isMyShip(ship)){
				canvas.strokeStyle = "rgba(86,200,45,0.60)";
				canvas.fillStyle = "rgba(50,122,24,0.50)";
			}else{
				canvas.strokeStyle = "rgba(229,87,38,0.60)";
				canvas.fillStyle = "rgba(179,65,25,0.50)";

			}

			graphics.drawCircleAndFill(canvas, s/2, s/2, s*0.15*gamedata.zoom+1, 1);


		}

		if (gamedata.isTargeted(ship)) {
			canvas.strokeStyle = "rgba(144,185,208,0.40)";
			canvas.fillStyle = "rgba(255,255,255,0.18)";

			graphics.drawCircleAndFill(canvas, s/2, s/2, s*0.15*gamedata.zoom+1, 1);
		}

        var rolled = shipManager.movement.isRolled(ship);
		graphics.drawAndRotate(canvas, s, s, s*gamedata.zoom, s*gamedata.zoom, shipdrawangle, img, rolled);

        if (mouseover
            && (!gamedata.isMyShip(ship) || gamedata.gamephase != 2 || gamedata.activeship != ship.id)){

            canvas.strokeStyle = "rgba(86,200,45,0.90)";
            canvas.fillStyle = "rgba(50,122,24,0.70)";

            var c = Math.floor(s/2);
            var a = shipManager.getShipDoMAngle(ship);
            var r = s*0.18*gamedata.zoom;
            var p = mathlib.getPointInDirection(r, a , c, c);
            //graphics.drawCircleAndFill(canvas, p.x, p.y, 5*gamedata.zoom, 2);
            graphics.drawArrow(canvas, p.x, p.y, a, 30, 1);
        }

		ship.shipdrawangle = shipdrawangle;
		ship.drawn = true;
		ship.drawnzoom = gamedata.zoom;
		ship.drawselected = selected;
		ship.drawmouseover = mouseover;
		ship.drawDamage = false;
		ship.drawDEW = dew;
		ship.drawCCEW = ccew;


    },

*/
    getShipCanvasSize: function(ship){
       return ship.canvasSize;



    },

    hasAnimationsDone: function(ship){

        for (var i in ship.movement){
            movement = ship.movement[i];
            if (movement.animated == false || movement.commit == false){
                return false
            }

        }

        return true;
    },


    getShipDoMAngle: function(ship){
        var d = shipManager.movement.getLastCommitedMove(ship).heading;
        if (d == 0){
            return 0;
        }
        if (d == 1){
            return 60;
        }
        if (d == 2){
            return 120;
        }
        if (d == 3){
            return 180;
        }
        if (d == 4){
            return 240;
        }
        if (d == 5){
            return 300;
        }


    },

    getShipHeadingAngle: function(ship){

        var d = shipManager.movement.getLastCommitedMove(ship).facing;
        if (d == 0){
            return 0;
        }
        if (d == 1){
            return 60;
        }
        if (d == 2){
            return 120;
        }
        if (d == 3){
            return 180;
        }
        if (d == 4){
            return 240;
        }
        if (d == 5){
            return 300;
        }


    },

    /*
    getShipHeadingAngleForDrawing: function(ship){

        var movement = null;
        for (var i in ship.movement){
            movement = ship.movement[i];
            if (movement.animated == true)
                continue;

            if (movement.type=="turnleft" || movement.type=="turnright"){
                var last = ship.movement[i-1];
                if (!last)
                    return shipManager.getShipHeadingAngle(ship);

                var lastheading = mathlib.hexFacingToAngle(last.facing);
                var destination = mathlib.hexFacingToAngle(movement.facing);
                var perc = movement.animationtics / animation.turningspeed;

                var right = (movement.type=="turnright");

                return mathlib.getFacingBetween(lastheading, destination, perc, right);

            }

            if (movement.type=="pivotleft" || movement.type=="pivotright"){
                var last = ship.movement[i-1];
                if (!last)
                    return shipManager.getShipHeadingAngle(ship);

                var lastheading = mathlib.hexFacingToAngle(last.facing);
                var destination = mathlib.hexFacingToAngle(movement.facing);
                var perc = movement.animationtics / animation.turningspeed;

                var right = (movement.type=="pivotright");

                return mathlib.getFacingBetween(lastheading, destination, perc, right);

            }

            break;

        }


        return shipManager.getShipHeadingAngle(ship);

    },

*/
    getShipPositionInTurn: function(ship, turn){

        if (turn <= 0)
            turn = 1;

        var movement = null;

        for (var i in ship.movement)
        {
            if (ship.movement[i].turn === turn){
                movement = ship.movement[i];
            }
        }

        if(movement === null && ship.movement.length > 0){
            movement = ship.movement[ship.movement.length-1];
        }
        
        var x = movement.x;
        var y = movement.y;
        var xO = movement.xOffset;
        var yO = movement.yOffset;
        return {x:x, y:y, xO:xO, yO:yO};
    },

    getShipPosition: function(ship){
        var movement = shipManager.movement.getLastCommitedMove(ship);
        return new hexagon.Offset(movement.position);
    },

    getShipPositionInWindowCoWithoutOffset: function(ship){
        var hexpos = shipManager.getShipPosition(ship);
        var pos = hexgrid.hexCoToPixel(hexpos.x, hexpos.y);
        return pos;
    },

    getShipPositionInWindowCo: function(ship){
        var hexpos = shipManager.getShipPosition(ship);
        var pos = hexgrid.hexCoToPixel(hexpos.x, hexpos.y);

        pos.x = pos.x + (hexpos.xO*gamedata.zoom);
        pos.y = pos.y + (hexpos.yO*gamedata.zoom);

        return pos;

    },

    getShipPositionForDrawing: function(ship){
        var movement = null;
        for (var i in ship.movement){
            if (ship.movement[i].commit == false)
                break;

            movement = ship.movement[i];

            if (movement.animated == true)
                continue;

            if (movement.type=="move" || movement.type=="slipright" || movement.type=="slipleft"){
                var last = ship.movement[i-1];

                if (!last)
                {
                    break;
                }
                var lastpos = hexgrid.hexCoToPixel(last.x, last.y);
                lastpos.x = lastpos.x + (last.xOffset*gamedata.zoom);
                lastpos.y = lastpos.y + (last.yOffset*gamedata.zoom);
                var destination = hexgrid.hexCoToPixel(movement.x, movement.y);
                destination.x = destination.x + (movement.xOffset*gamedata.zoom);
                destination.y = destination.y + (movement.yOffset*gamedata.zoom);
                var perc = movement.animationtics / animation.movementspeed;

                return mathlib.getPointBetween(lastpos, destination, perc);

            }

            break;


        }


        var x = movement.x;
        var y = movement.y;

        var lastpos = hexgrid.hexCoToPixel(x, y);
        lastpos.x = Math.floor(lastpos.x + (movement.xOffset*gamedata.zoom));
        lastpos.y = Math.floor(lastpos.y + (movement.yOffset*gamedata.zoom));
        return lastpos;
    },

    onShipContextMenu: function(e){
        var id = $(e).data("id");
        var ship = gamedata.getShip(id);

        if (shipSelectList.haveToShowList(ship, e)){
            shipSelectList.showList(ship);
        }else{
            shipManager.doShipContextMenu(ship);
        }

    },

    doShipContextMenu: function(ship){

        shipSelectList.remove();

        if (shipManager.isDestroyed(ship))
            return;

        if (ship.userid == gamedata.thisplayer && (gamedata.gamephase == 1 || gamedata.gamephase >2)){
            shipWindowManager.open(ship);
            gamedata.selectShip(ship, false);
            gamedata.shipStatusChanged(ship);
            drawEntities();
        }else{
            shipWindowManager.open(ship);
        }
        return false;

    },

    onShipDblClick: function(e){


    },

    onShipClick: function(e){
        //console.log("click on ship");

        if (!e || e.which !== 1)
            return;

        e.stopPropagation();
        var id = $(this).data("id");
        var ship = gamedata.getShip(id);

        if (shipSelectList.haveToShowList(ship, e)){
            shipSelectList.showList(ship);
        }else{
            shipManager.doShipClick(ship);
        }

    },

    doShipClick: function(ship){

        shipSelectList.remove();

        if (ship == null){
            return;
        }

        if (gamedata.thisplayer == -1)
            return;

        if (shipManager.isDestroyed(ship))
            return;

        if (gamedata.gamephase == 2)
            return;

        if (gamedata.waiting)
            return;

        if (ship.userid == gamedata.thisplayer){
            gamedata.selectShip(ship, false);
        }

        if (ship.userid != gamedata.thisplayer && gamedata.gamephase == 3){
           weaponManager.targetShip(ship, false);
        }

        if (gamedata.gamephase == 1 && ship.userid != gamedata.thisplayer){
            if (gamedata.selectedSystems.length > 0){
                weaponManager.targetShip(ship, false);
            }else if (!ship.flight){
                ew.AssignOEW(ship);
            }
        }
        gamedata.shipStatusChanged(ship);
        drawEntities();
        //scrolling.scrollToShip(ship);

    },

    getPrimaryCnC: function(ship){
        var cncs = [];

        for (var system in ship.systems){
            if (ship.systems[system].displayName == "C&C"){
                cncs.push(ship.systems[system]);
            }
        }

        cncs.sort(function(a, b){
            if (shipManager.systems.getRemainingHealth(a) > shipManager.systems.getRemainingHealth(b) ){
                return 1;
            }
            else {
                return -1;
            }
        });

        var primary = cncs[0];

        return primary;
    },

    isDisabled: function(ship){
        if (ship.base){
            var primary = shipManager.getPrimaryCnC(ship);

            if (!shipManager.criticals.hasCriticalOnTurn(primary, "ShipDisabledOneTurn", gamedata.turn-1)){
                return false;
            }
        }
        else {
            for (var i = 0; i < ship.systems.length; i++){
                if (ship.systems[i].displayName == "C&C"){
                    if (shipManager.criticals.hasCriticalOnTurn(ship.systems[i], "ShipDisabledOneTurn", gamedata.turn-1)){
                        return true;
                    }
                }
            }
        }
        return false;
    },


    isDestroyed: function(ship){

        if (ship == null){
            return;
        }

        if (ship.flight){
			for (var i in ship.systems){
				var fighter = ship.systems[i];
				if (!shipManager.systems.isDestroyed(ship, fighter)){
					return false;
                }
			}
			return true;
		}
        else{            
            if (!ship.base){
    			var stru = shipManager.systems.getStructureSystem(ship, 0);
    			if (shipManager.systems.isDestroyed(ship, stru)){
    				return true;
                }   

                var react = shipManager.systems.getSystemByName(ship, "reactor");
                if (shipManager.systems.isDestroyed(ship, react)){
                    return true;
                }
            }
            else {
                var stru = shipManager.systems.getStructureSystem(ship, 0);
                if (shipManager.systems.isDestroyed(ship, stru)){
                    return true;     
                }

                var mainReactor = shipManager.systems.getSystemByNameInLoc(ship, "reactor", 0);
                if (shipManager.systems.isDestroyed(ship, mainReactor)){
                    return true;
                }           
            }
        }

        return false;

    },


    getStructuresDestroyedThisTurn: function(ship){

        var array = [];

        for (var j = 0; j < ship.systems.length; j++){
            system = ship.systems[j];
            if (system.displayName == "Structure" && system.location != 0){
                if (system.destroyed){
                    for (var k = 0; k < system.damage.length; k++){
                        var dmg = system.damage[k];
                        if (dmg.destroyed){
                            if (gamedata.turn == dmg.turn){
                                array.push(system);
                                break;
                            }
                        }
                    }
                }
            }
        }

        if (array.length > 0){
            return array;
        }
        else return null;
    },

    getOuterReactorDestroyedThisTurn: function(ship){

        var array = [];

        for (var j = 0; j < ship.systems.length; j++){
            system = ship.systems[j];
            if (system.displayName == "Reactor" && system.location != 0){
                if (system.destroyed){
                    for (var k = 0; k < system.damage.length; k++){
                        var dmg = system.damage[k];
                        if (dmg.destroyed){
                            if (gamedata.turn == dmg.turn){
                                array.push(system);
                                break;
                            }
                        }
                    }
                }
            }
        }

        if (array.length > 0){
            return array;
        }
        else return null;
    },


    isAdrift: function(ship){
        if (ship.flight || ship.osat || ship.base)
            return false;

        if (shipManager.criticals.hasCriticalInAnySystem(ship, "ShipDisabledOneTurn"))
            return true;


        if (shipManager.systems.isDestroyed(ship, shipManager.systems.getSystemByName(ship, "cnC"))){
            return true;
        }
        return false;
    },

    isEngineless: function(ship){
        var engines = [];
        for (var sys in ship.systems){
            if (ship.systems[sys].displayName == "Engine"){
                engines.push(ship.systems[sys]);
            }
        }

        for (var i = 0; i < engines.length; i++){
            if (engines[i].destroyed == false){
                return false;
            }
        }

        return true;
    },

    getTurnDestroyed: function(ship){
		var turn = null;
		if (ship.flight){

		    var fightersSurviving = ship.systems.some(function(fighter) {
		        return damageManager.getTurnDestroyed(ship, fighter) === null;
            });

		    if (fightersSurviving) {
		        return null;
            }

            ship.systems.forEach(function(fighter){
				var dturn = damageManager.getTurnDestroyed(ship, fighter);
				if (dturn > turn)
					turn = dturn;
			});

		}else{

            var react = shipManager.systems.getSystemByName(ship, "reactor");
            var rturn = damageManager.getTurnDestroyed(ship, react);
			var stru = shipManager.systems.getStructureSystem(ship, 0);
			var sturn = damageManager.getTurnDestroyed(ship, stru);

            if (rturn != null && (rturn < sturn || sturn == null))
                turn = rturn;
            else
                turn = sturn;
		}

        return turn;

    },

    getIniativeOrder: function(ship){
        var order = 1;

        for (var i in gamedata.ships){
            if (shipManager.isDestroyed(gamedata.ships[i]))
                continue;

            if (gamedata.ships[i] == ship)
                return order;

            order++;
        }

        return 0;
    },

    hasBetterInitive: function(a, b){
        //console.log(a.name);
        if (a.iniative > b.iniative)
            return true;

        if (a.iniative < b.iniative)
            return false;

        if (a.iniative == b.iniative){
            if (a.iniativebonus > b.iniativebonus)
                return true;

            if (b.iniativebonus > a.iniativebonus)
                return false;

            for (var i in gamedata.ships){
                if (gamedata.ships[i] == a)
                    return false;

                if (gamedata.ships[i] == b)
                    return true;

            }
        }

        return false;

    },

    getShipsInSameHex: function(ship, pos1){

        if (!pos1)
            var pos1 = shipManager.getShipPosition(ship);

        var shipsInHex = Array();
        for (var i in gamedata.ships){
            var ship2 = gamedata.ships[i];

            if (shipManager.isDestroyed(ship2))
				continue;

            //if (ship.id = ship2.d)
            //  continue;

            var pos2 = shipManager.getShipPosition(ship2);


            if (pos1.equals(pos2)){
                shipsInHex.push(ship2);
            }
        }

        return shipsInHex;

    },

    getFighterPosition: function(pos, angle, zoom){

		var dir = 0;
		if (pos == 0){
			dir = mathlib.addToDirection(0, angle);
			return mathlib.getPointInDirection(19*zoom, dir, 0, 0 );
		}
		else if (pos == 1){
			dir = mathlib.addToDirection(300, angle);
			return mathlib.getPointInDirection(13*zoom, dir, 0, 0 );
		}
        else if (pos == 2){
			dir = mathlib.addToDirection(60, angle);
			return mathlib.getPointInDirection(13*zoom, dir, 0, 0 );
		}
        else if (pos == 3){
			dir = mathlib.addToDirection(180, angle);
			return mathlib.getPointInDirection(12*zoom, dir, 0, 0 );
		}
        else if (pos == 4){
			dir = mathlib.addToDirection(250, angle);
			return mathlib.getPointInDirection(21*zoom, dir, 0, 0 );
		}
        else if (pos == 5){
            dir = mathlib.addToDirection(110, angle);
            return mathlib.getPointInDirection(21*zoom, dir, 0, 0 );
        }
        else if (pos == 6){
            dir = mathlib.addToDirection(180, angle);
            return mathlib.getPointInDirection(29*zoom, dir, 0, 0 );
        }
        else if (pos == 7){
            dir = mathlib.addToDirection(230, angle);
            return mathlib.getPointInDirection(32*zoom, dir, 0, 0 );
        }
        else if (pos == 8){
            dir = mathlib.addToDirection(130, angle);
            return mathlib.getPointInDirection(32*zoom, dir, 0, 0 );
        }
        else if (pos == 9){
            dir = mathlib.addToDirection(0, angle);
            return mathlib.getPointInDirection(35*zoom, dir, 0, 0 );
        }
        else if (pos == 10){
            dir = mathlib.addToDirection(295, angle);
            return mathlib.getPointInDirection(28*zoom, dir, 0, 0 );
        }
        else if (pos == 11){
            dir = mathlib.addToDirection(65, angle);
            return mathlib.getPointInDirection(28*zoom, dir, 0, 0 );
        }


		return {x:0, y:0};

	},


    getSpecialAbilitySystem: function(ship, ability)
    {
        for (var i in ship.systems)
        {
            var system = ship.systems[i];

            if (shipManager.systems.isDestroyed(ship, system))
                continue;

            if (shipManager.power.isOffline(ship, system))
                continue;

            for (var a in system.specialAbilities){
                if (system.specialAbilities[a] == ability)
                    return system;
            }
        }

        return false;
    },

    hasSpecialAbility: function(ship, ability)
    {
        if (shipManager.getSpecialAbilitySystem(ship, ability))
            return true;

        return false;
    },

    isElint: function(ship){
        if (shipManager.hasSpecialAbility(ship, "ELINT")){
            return true;
        }

        return false;

    },

    isEscorting: function(ship, target)
    {
        if ( ! ship.flight)
            return false;

        var ships = shipManager.getShipsInSameHex(ship);

        for (var i in ships)
        {
            var othership = ships[i];

            if (othership.id == ship.id)
                continue;

            if (gamedata.isEnemy(ship, othership))
                continue;

            var oPos = shipManager.getShipPositionInTurn(othership, gamedata.turn-1);
            var tPos = shipManager.getShipPositionInTurn(ship, gamedata.turn-1);

            if ( ! target || target.id == othership.id)
            {
                if (oPos.x == tPos.x && oPos.y == tPos.y)
                    return true;
            }
        }

        return false;
    }
}
