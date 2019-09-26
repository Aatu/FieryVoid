'use strict';

window.gamedata = {
    thisplayer: 0,
    slots: null,
    ships: [],
    gameid: 0,
    turn: 0,
    phase: 0,
    activeship: 0,
    waiting: true,
    maxpoints: 0,
    status: "LOBBY",
    selectedSlot: null,
    allShips: null,
 	displayedShip: '',
 	displayedFaction: '',

    canAfford: function canAfford(ship) {

        var slotid = gamedata.selectedSlot;
        var selectedSlot = playerManager.getSlotById(slotid);

        var points = 0;
        for (var i in gamedata.ships) {
            var lship = gamedata.ships[i];
            if (lship.slot != slotid) continue;
            points += lship.pointCost;
        }

        points += ship.pointCost;
        if (points > selectedSlot.points) return false;

        return true;
    },

    updateFleet: function updateFleet(ship) {
        var a = 0;
        for (var i in gamedata.ships) {
            a = i;
        }
        a++;
        ship.id = a;
        ship.slot = gamedata.selectedSlot;
        gamedata.ships[a] = ship;
        var h = $('<div class="ship bought slotid_' + ship.slot + ' shipid_' + ship.id + '" data-shipindex="' + a + '"><span class="shiptype">' + ship.shipClass + '</span><span class="shipname name">' + ship.name + '</span><span class="pointcost">' + ship.pointCost + 'p</span><span class="remove clickable">remove</span></div>');
        $(".remove", h).bind("click", function () {
            delete gamedata.ships[a];
            h.remove();
            gamedata.calculateFleet();
        });
        h.appendTo("#fleet");
        gamedata.calculateFleet();
    },
	
	/*returns ship variant as a single letter*/
	variantLetter: function(ship){
		var vLetter = '';		
		switch(ship.occurence) {
		    case 'unique':
			vLetter = 'Q';
			break;
		    case 'rare':
			vLetter = 'R';
			break;
		    case 'uncommon':
			vLetter = 'U';
			break;
		    case 'common':
			vLetter = 'C';
			break;
		    default: //assume something atypical
			vLetter = 'X'; 
		}	
		return(vLetter);
	},
	
	/*checks fleet composition and displays alert with result*/
    checkChoices: function(){
	    var checkResult = "";
	    var problemFound = false;
	    var warningFound = false;
	    var slotid = gamedata.selectedSlot;
            var selectedSlot = playerManager.getSlotById(slotid);
	    
	    var units10 = 0;
	    var units33 = 0;
	    var points10 = 0;
	    var points33 = 0;
	    var totalU = 0;
	    var totalR = 0;
	    var jumpDrivePresent = false;
	    var capitalShips = 0;
	    var totalShips = 0;
	    var customShipPresent = false;
	    var staticPresent = false;
	    var shipTable = []; 
	    
	    var totalHangarH = 0; //hangarspace for heavy fighters
	    var totalHangarM = 0; //hangarspace for medium fighters
	    var totalHangarL = 0; //hangarspace for light fighters
	    var totalHangarOther = new Array( ); //other hangarspace
	    var totalFtrH = 0;//total heavy fighters
	    var totalFtrM = 0;//total medium fighters
	    var totalFtrL = 0;//total light fighters
	    var totalFtrOther = new Array( );//total other small craft
		var smallCraftUsed = new Array( );//small craft sizes that happen to be present, whether as hangar space or actual craft

	    for (var i in gamedata.ships){
            	var lship = gamedata.ships[i];
            	if (lship.slot != slotid) continue;
		if (lship.limited==10){
			points10 += lship.pointCost;
			units10 += 1;
		}
		if (lship.limited==33){
			points33 += lship.pointCost;
			units33 += 1;
		}
		var vLetter = gamedata.variantLetter(lship);
		var hull = lship.variantOf; 
		var hullFound;
		hullFound = false;
		if (hull == "") hull = lship.shipClass; //ship is either base itself, or base is indicated in variantOf variable
		for (var j in  shipTable){
			var oHull = shipTable[j];
			if (oHull.name == hull){
				hullFound = true;
				oHull.Total++;
				switch(vLetter) {
				    case 'Q':
					oHull.Q++;
					totalR++;
					break;
				    case 'R':
					oHull.R++;
					totalR++;
					break;
				    case 'U':
					oHull.U++;
					totalU++;
					break;
				    case 'C':
					oHull.C++;
					break;
				    default:
					nHull.X++;
			     	}
			}
		}
		if (hullFound == false){
		    var nHull = {name:hull, Total: 1, Q:0, R: 0, U: 0, C: 0, X: 0, isFtr:false};
	            if(lship.flight){
	            	nHull.isFtr = lship.flight;
		    }
		    switch(vLetter) {
			    case 'Q':
				nHull.Q++;
				totalR++; //Unique is treated more or less the same as Rare
				break;
			    case 'R':
				nHull.R++;
				totalR++;
				break;
			    case 'U':
				nHull.U++;
				totalU++;
				break;
			    case 'C':
				nHull.C++;
				break;
			    default:
				nHull.X++;
		     }
		     shipTable.push(nHull);
		}
		if(!lship.flight){
	            totalShips++;
	            //check hangar space available...
		    for(var h in lship.fighters){
				var amount = lship.fighters[h];
			    if(h == "normal" || h =="heavy"){
					totalHangarH += amount;
			    }else if(h=="medium"){ 
					totalHangarM += amount;
			    }else if(h=="light" || h=="ultralight"){ 
					totalHangarL += amount;
			    }else{ //something other than fighters
					var found = false;
					for(var nh in totalHangarOther){ 					
						if (totalHangarOther[nh][0] == h){//this is small craft type we're looking for!
							found = true;
							totalHangarOther[nh][1] += amount;
						}
					}
					if (found != true){ //such craft wasn't encountered yet
						totalHangarOther.push(new Array(h,amount));
						smallCraftUsed.push(h);
					}
			    }
		    }
		}else{//note presence of fighters
			var smallCraftSize = 'not recognized';			
			if (lship.hangarRequired != '') { //classify based on explicit info from craft
				smallCraftSize = lship.hangarRequired;
			}else{//classify depending on jinking limit...
				if (lship.jinkinglimit>=10){
					smallCraftSize = 'light';
				}else if (lship.jinkinglimit>=8){
					smallCraftSize = 'medium';
				}else if (lship.jinkinglimit>=6){
					smallCraftSize = 'heavy';
				}
			}
			//now translate size into hangar space used...
			if(smallCraftSize =="heavy"){
				totalFtrH += lship.flightSize;
			}else if(smallCraftSize=="medium"){ 
				totalFtrM += lship.flightSize;
			}else if(smallCraftSize=="light" || smallCraftSize=="ultralight"){ 
				totalFtrL += lship.flightSize;
			}else{ //something other than fighters
				var found = false;
				for(var nh in totalFtrOther){ 					
					if (totalFtrOther[nh][0] == smallCraftSize){//this is small craft type we're looking for!
						found = true;
						totalFtrOther[nh][1] += lship.flightSize;
					}
				}
				if (found != true){ //such craft wasn't encountered yet
					totalFtrOther.push(new Array(smallCraftSize,lship.flightSize));
					smallCraftUsed.push(smallCraftSize);
				}
			}
		}
		if (jumpDrivePresent == false){ //if already found there's no point
			for (var a in lship.systems){
				var sSystem = lship.systems[a];
				if (sSystem.name=='jumpEngine') jumpDrivePresent = true;
			}
		}
		if (lship.shipSizeClass >= 3) capitalShips++;
		if (lship.unofficial){
			customShipPresent = true;
			warningFound = true;
		}
		if ((lship.base == true) || (lship.osat == true)) staticPresent = true;
		    
		    
	    } //end of loop at ships preparing data
	    
	    

	    checkResult = "Total fleet limit: " + selectedSlot.points + "<br><br>";
	    
	    //check: overall fleet traits
	    checkResult += "Jump engine: "; //Jump Engine present?
	    if (jumpDrivePresent){
		    checkResult += " present";
	    }else{		    
		    checkResult += " NOT present! (at least one is required)";
		    problemFound = true;
	    }
	    checkResult += "<br>";
	    
	    checkResult += "Capital ships: " + capitalShips + ": "; //Capital Ship present?
	    //var capsRequired = Math.floor(selectedSlot.points/3000);//1 per 3000, round down; so 1 at 3000, 2 at 6000, 3 at 9000, 10 at 30000
	    //let's decrease the requirement at larger battles: 1 per 4000, round up, with first 2499 not counted; so 1 at 3000, 2 at 6500, 3 at 10500, 10 at 42500
	    var capsRequired = 0;
	    if (selectedSlot.points >= 3000){
		    capsRequired = Math.ceil((selectedSlot.points-2499)/4000);
	    }    
	    
	   
	    if (capitalShips >= capsRequired){ //tournament rules: at least 1; changed for scalability
		    checkResult += " OK";
	    }else{		    
		    checkResult += " FAILED! (at least " + capsRequired + " required)";
		    problemFound = true;
	    }
	    checkResult += "<br>";
	    
	    //Custom units present?
	    if (customShipPresent){
		checkResult += "Custom unit(s) present! Opponent's permission required.<br>"; 	
		warningFound = true;
	    }
	    
	    //Static structures present?
	    if (staticPresent){
		   checkResult += "Static structures present! They're not allowed in pickup battle."; 
		   problemFound = true;
	    }
	    checkResult += "<br>";
	    
	    
	    var limit10 = Math.floor(selectedSlot.points*0.1);
	    var limit33 = Math.floor(selectedSlot.points*0.33);
	    var oneOverAllowed = false;	    
	    checkResult += "<br><u><b>Deployment restrictions:</b></u><br>";
	    checkResult += " - 10% bracket: " + points10 +"/" + limit10 + ": ";
	    if (points10<=limit10){
		    checkResult += "OK";
	    }else{		
		    if(units10==1 && oneOverAllowed == false){ //only 1 unit, and this exception wasn't used yet
			oneOverAllowed = true;
			checkResult += "OK (one single ship is allowed to break limit)";
		    }else{
			checkResult += "FAILED! (too many points in this deployment bracket)";
		    	problemFound = true;
		    }
	    }
	    checkResult += "<br>";
	    checkResult += " - 33% bracket: " + points33 +"/" + limit33 + ": ";
	    if (points33<=limit33){
		    checkResult += "OK";
	    }else{		
		    if(units33==1 && oneOverAllowed == false){ //only 1 unit, and this exception wasn't used yet
			oneOverAllowed = true;
			checkResult += "OK (one single ship is allowed to break limit)";
		    }else{
			checkResult += "FAILED! (too many points in this deployment bracket)";
		    	problemFound = true;
		    }
	    }
	    if(points10>0 && totalShips<2){
		checkResult += "<br>Restricted (10%) ship present without escort! Such a rare ship needs to be accompanied by at least one other ship, unless it's Dargan or a Minbari ship.";
		problemFound = true;
	    }	    
	    checkResult += "<br><br>";
	    
	    //variant restrictions
	    checkResult += "<br>><u><b>Variant restrictions:</b></u><br><br>";
	    var limitPerHull = Math.floor(selectedSlot.points/1000); //turnament rules: 3, but it's for 3500 points
	    limitPerHull = Math.max(limitPerHull,2); //always allow at least 2!
	    var currRlimit = 0;
	    var currUlimit = 0;
	    var sumVar = 0;
	    for (var j in  shipTable){
		var currHull = shipTable[j];
		checkResult += " <i>" + currHull.name + "</i><br>";			
		checkResult +=  " - Total: " + currHull.Total;
		if (!currHull.isFtr){ //fighter total is not limited
		    	checkResult +=  " (allowed " +limitPerHull+ ")";
			if (currHull.Total>limitPerHull ){
				checkResult += " TOO MANY!";
				problemFound = true;
			}
		}
		checkResult += "<br>";
		currRlimit = Math.ceil(currHull.Total/9);
		currUlimit = Math.ceil(currHull.Total/3);
		sumVar = currHull.R + currHull.Q + currHull.U;
		if (sumVar > 0){
			checkResult += " - Uncommon/Rare/Unique: " + sumVar + " (allowed " +currUlimit+ ")";
			if (sumVar>currUlimit){
				checkResult += " TOO MANY!";
				problemFound = true;
			}
			checkResult += "<br>";
		}
		sumVar = currHull.R + currHull.Q;
		if (sumVar > 0){
			checkResult += " - Rare/Unique: " + sumVar + " (allowed " +currRlimit+ ")";
			if (sumVar>currRlimit){
				checkResult += " TOO MANY!";
				problemFound = true;
			}
			checkResult += "<br>";
		}
		sumVar = currHull.X;
		if (sumVar > 0){
			checkResult += " - Special: "+sumVar;
			checkResult += " CORRECTNESS NOT CHECKED!";
			warningFound = true;
			checkResult += "<br>";
		}  		
	        checkResult += "<br>";
	    }
	    checkResult += "<br>";
	    
	    //total Uncommon/Rare units in fleet	    
	    var limitUTotal =  0;
	    if((selectedSlot.points-1500) > 0){
	    	limitUTotal = Math.floor((selectedSlot.points-1500)/1000); //limit Uncommon units per fleet; turnament rules: 2, but it's for 3500 points
	    }
	    limitUTotal = Math.max(limitUTotal,2); //always allow at least 2! 
	    var totalCombined = totalU + 2*totalR; //Rares take 2 slots
	    if (totalCombined>limitUTotal){
		    checkResult += "FAILED: You have " + totalU + " Uncommon and " + totalR + " Rare units , out of total " + limitUTotal + " Uncommon allowed (Rare units count double).<br><br>" ;
		    problemFound = true;
	    }	    
	    
	    
	    //fighters!
	    var totalHangarAvailable = totalHangarH+totalHangarM+totalHangarL;
	    var minFtrRequired = Math.ceil(totalHangarAvailable/2);
	    var totalFtrPresent = totalFtrH+totalFtrM+totalFtrL;
	    var totalFtrCurr = 0;
	    var totalHangarCurr = 0;
	    checkResult += "<br><b><u>Fighters:</u></b><br>";
		checkResult +=  " - Total fighters: " + totalFtrPresent;
	    checkResult +=  " (allowed between " +minFtrRequired+ " and " + totalHangarAvailable + ")";
		if (totalFtrPresent > totalHangarAvailable || totalFtrPresent < minFtrRequired){ //fighter total is not within limits
			checkResult += " FAILURE!";
			problemFound = true;
		}else{
			checkResult += " OK";
		}
		checkResult += "<br>";
	    
		totalFtrCurr = totalFtrH+totalFtrM;
		totalHangarCurr = totalHangarH+totalHangarM;
		checkResult +=  " - Medium/Heavy Fighters: " + totalFtrCurr;
		checkResult +=  " (allowed up to " + totalHangarCurr + ")";
		if (totalFtrCurr > totalHangarCurr){ //fighter total is not within limits
			checkResult += " TOO MANY!";
			problemFound = true;
		}else{
			checkResult += " OK";
		}
		checkResult += "<br>";
	    
		totalFtrCurr = totalFtrH;
		totalHangarCurr = totalHangarH;
		checkResult +=  " - Heavy Fighters: " + totalFtrCurr;
	    	checkResult +=  " (allowed up to " + totalHangarCurr + ")";
		if (totalFtrCurr > totalHangarCurr){ //fighter total is not within limits
			checkResult += " TOO MANY!";
			problemFound = true;
		}else{
			checkResult += " OK";
		}
		checkResult += "<br>";
	    
		//make list of small craft in fleet contain only unique values...
		var smallCraftUsedUnique = smallCraftUsed.filter(function(item, pos) {
			return smallCraftUsed.indexOf(item) == pos;
		})
		
		//list each small craft size used separately!
		for(var sc in smallCraftUsedUnique){
			var scSize = smallCraftUsedUnique[sc];
			totalFtrCurr = 0;
			totalHangarCurr = 0;
			for(var nh in totalFtrOther){ 					
				if (totalFtrOther[nh][0] == scSize){//this is small craft type we're looking for!
					totalFtrCurr = totalFtrOther[nh][1] ;
				}
			}
			for(var nh in totalHangarOther){ 					
				if (totalHangarOther[nh][0] == scSize){//this is small craft type we're looking for!
					totalHangarCurr = totalHangarOther[nh][1] ;
				}
			}	
			checkResult +=  " - " + scSize + ": " + totalFtrCurr;
			checkResult +=  " (allowed up to " + totalHangarCurr + ")";
			if (totalFtrCurr > totalHangarCurr){ //small craft total is not within limits
				checkResult += " TOO MANY!";
				problemFound = true;
			}else{
				checkResult += " OK";
			}
			checkResult += "<br>";				
		}
		checkResult += "<br>";		
			
	    
	    if (warningFound){
		    checkResult = "Unchecked or non-canon elements found - check text for details.<br><br>"+checkResult;
	    }
	    
	    if (problemFound){
		    checkResult = "Overall: <b><u>FAILED</u></b>!<br><br>"+checkResult;
	    }else{
		    checkResult = "Overall: <b><u>OK</u></b>.<br><br>"+checkResult;
	    }
	    
	    checkResult = "<b>FLEET CORRECTNESS REPORT</b><br>based on tournament rules, modified for scalability.<br><br>"+checkResult;   
	    
	    //alert(checkResult); //alert will be truncated by browser
	    var targetDiv = document.getElementById("fleetcheck");
	    targetDiv.style.display = "block";
	    var targetSpan = document.getElementById("fleetchecktxt");
	    targetSpan.innerHTML = checkResult;	    
	    
	    alert("Fleet check updated!");
    }, //endof function checkChoices
	

    constructFleetList: function constructFleetList() {

        var slotid = gamedata.selectedSlot;
        var selectedSlot = playerManager.getSlotById(slotid);

        $(".ship.bought").remove();
        for (var i in gamedata.ships) {
            // Reset ship ids to avoid ending up with elements with the same id
            // a number of times after you have removed a ship.
            gamedata.ships[i].id = i;

            var ship = gamedata.ships[i];
            if (ship.slot != slotid) continue;

            var h = $('<div class="ship bought slotid_' + ship.slot + ' shipid_' + ship.id + '" data-shipindex="' + ship.id + '"><span class="shiptype">' + ship.shipClass + '</span><span class="shipname name">' + ship.name + '</span><span class="pointcost">' + ship.pointCost + 'p</span><span class="remove clickable">remove</span></div>');
            h.appendTo("#fleet");
        }
        $(".ship.bought .remove").bind("click", function (e) {
            var id = $(this).parent().data('shipindex');

            for (var i in gamedata.ships) {
                if (gamedata.ships[i].id == id) {
                    gamedata.ships.splice(i, 1);
                    break;
                }
            }
            $('.ship.bought.shipid_' + id).remove();
            gamedata.calculateFleet();
            // This is done to update it immediately and more importantly,
            // to assign new id's to all fleet entries
            gamedata.constructFleetList();
        });

        gamedata.calculateFleet();
    },

    calculateFleet: function calculateFleet() {

        var slotid = gamedata.selectedSlot;
        if (!slotid) return;

        var selectedSlot = playerManager.getSlotById(slotid);
        var points = 0;
        for (var i in gamedata.ships) {
            if (gamedata.ships[i].slot != slotid) continue;

            points += gamedata.ships[i].pointCost;
        }

        $('.max').html(selectedSlot.points);
        $('.current').html(points);
        return points;
    },

    isMyShip: function isMyShip(ship) {
        return ship.userid == gamedata.thisplayer;
    },

    orderShipListOnName: function orderShipListOnName(shipList) {
        var swapped = true;

        for (var x = 1; x < shipList.length && swapped; x++) {
            swapped = false;

            for (var y = 0; y < shipList.length - x; y++) {
                if (shipList[y + 1].shipClass < shipList[y].shipClass) {
                    var temp = shipList[y];
                    shipList[y] = shipList[y + 1];
                    shipList[y + 1] = temp;
                    swapped = true;
                }
            }
        }
    },

    /*alternate sorting method - by point value*/
    orderShipListOnPV: function orderShipListOnPV(shipList) {
        var swapped = true;

        for (var x = 1; x < shipList.length && swapped; x++) {
            swapped = false;

            for (var y = 0; y < shipList.length - x; y++) {
                if (shipList[y + 1].pointCost > shipList[y].pointCost) {
                    //top-down
                    var temp = shipList[y];
                    shipList[y] = shipList[y + 1];
                    shipList[y + 1] = temp;
                    swapped = true;
                }
            }
        }
    },

    orderStringList: function orderStringList(stringList) {
        var swapped = true;

        for (var x = 1; x < stringList.length && swapped; x++) {
            swapped = false;

            for (var y = 0; y < stringList.length - x; y++) {
                if (stringList[y + 1] < stringList[y]) {
                    var temp = stringList[y];
                    stringList[y] = stringList[y + 1];
                    stringList[y + 1] = temp;
                    swapped = true;
                }
            }
        }
    },

    parseFactions: function parseFactions(jsonFactions) {
        this.orderStringList(jsonFactions);
        var factionList = new Array();

        for (var i in jsonFactions) {
            var faction = jsonFactions[i];

            factionList[faction] = new Array();

            var group = $('<div id="' + faction + '" class="' + faction + ' faction shipshidden listempty" data-faction="' + faction + '"><div class="factionname name"><span>' + faction + '</span><span class="tooltip">(click to expand)</span></div>').appendTo("#store");

            group.find('.factionname').on("click", this.expandFaction);
        }

        gamedata.allShips = factionList;
    },
    
	/*old, simple version*/
	/*
	parseShips: function(jsonShips){
		for (var faction in jsonShips){
			var shipList = jsonShips[faction];
			
			this.orderShipListOnName(shipList);
			gamedata.setShipsFromFaction(faction, shipList);

			for (var index = 0; index < jsonShips[faction].length; index++){
				var ship = shipList[index];
				var targetNode = document.getElementById(ship.faction);

				var h = $('<div oncontextmenu="gamedata.onShipContextMenu(this);return false;" class="ship" data-id="'+ship.id+'" data-faction="'+ faction +'" data-shipclass="'+ship.phpclass+'"><span class="shiptype">'+ship.shipClass+'</span><span class="pointcost">'+ship.pointCost+'p</span><span class="addship clickable">Add to fleet</span></div>');
				    h.appendTo(targetNode);
			}
	
			$(".addship").bind("click", this.buyShip);
		}
	},*/
	
	/*prepares ship class name for display - will contain lots of information besides class name itself!*/
	prepareClassName: function(ship){
		//name: actualname (limited variant custom)
		//italics if actual variant!
		var displayName = ship.shipClass;
		var addOn = '';
		
		switch(ship.occurence) {
		    case 'unique':
			addOn = 'Q';
			break;
		    case 'rare':
			addOn = 'R';
			break;
		    case 'uncommon':
			addOn = 'U';
			break;
		    case 'common':
			addOn = 'C';
			break;
		    default: //assume something atypical
			addOn = 'X';
		}		
		if((ship.limited>0) && (ship.limited < 100)){ //else no such info necessary
			addOn = addOn +' '+ ship.limited + '%';
		}		
		if(ship.unofficial == true){
			addOn = addOn +' '+'CUSTOM';
		}
		
		displayName=displayName+' ('+addOn+')';
		if(ship.variantOf !=''){
			displayName = '&nbsp;&nbsp;&nbsp;<i>'+displayName+'</i>';
		}else{
			displayName = '<b>'+displayName+'</b>';
		}
		
		return displayName;
	}, //endof prepareClassName
	
	/*prepares fleet list for purchases for display*/
	parseShips: function(jsonShips){
		for (var faction in jsonShips){
			var targetNode = document.getElementById(faction);
			var h;
			var ship;
			var shipV;
			var shipDisplayName;
			var shipList = jsonShips[faction];
			
			//this.orderShipListOnName(shipList); //alphabetical sort
			this.orderShipListOnPV(shipList); //perhaps more appropriate here, as alphabetical order will be shot to hell anyway
			
			gamedata.setShipsFromFaction(faction, shipList);
			
			//show separately: immobile objects (bases/OSATs), every ship size, fighters
			var sizeClassHeaders = ['Fighters','Medium Ships','Heavy Ships', 'Capital Ships', 'Immobile Structures'];
			for(var desiredSize=4; desiredSize>=0;desiredSize--){
				//display header
				h = $('<div class="shipsizehdr" data-faction="'+ faction +'"><span class="shiptype">'+sizeClassHeaders[desiredSize]+'</span></div>');
                    		h.appendTo(targetNode);
				for (var index = 0; index < jsonShips[faction].length; index++){
					ship = shipList[index];
					if(desiredSize==4){ //bases and OSATs, size does not matter
						if((ship.base != true) && (ship.osat != true)) continue; //check if it's a base or OSAT
				        }else if(desiredSize>0){ //ships (check actual size)
						if(ship.shipSizeClass!=desiredSize) continue;//check if it's of correct size
						if((ship.base == true) || (ship.osat == true)) continue; //check if it's not a base or OSAT
					}else{ //fighters! check max size - they should be -1, but 0 isn't used...
						if(ship.shipSizeClass>0) continue;//check if it's of correct size
						if((ship.base == true) || (ship.osat == true)) continue; //check if it's not a base or OSAT
					}
					if(ship.variantOf!='') continue;//check if it's not a variant, we're looking only for base designs here...
					//ok, display...
					shipDisplayName = this.prepareClassName(ship);
					h = $('<div oncontextmenu="return false;" class="ship"><span class="shiptype">'+shipDisplayName+'</span><span class="pointcost">'+ship.pointCost+'p</span> -<span class="addship clickable">Add to fleet</span> -<span class="showship clickable">Show details</span></div>');
                    $(".addship", h).on("click", this.buyShip.bind(this, ship.phpclass));
                    $(".showship", h).on("click", gamedata.onShipContextMenu.bind(this, ship.phpclass, faction));
                        
                    h.appendTo(targetNode);
					//search for variants of the base design above...
					for (var indexV = 0; indexV < jsonShips[faction].length; indexV++){
						shipV = shipList[indexV];
						if(shipV.variantOf != ship.shipClass) continue;//that's not a variant of current base ship
						shipDisplayName = this.prepareClassName(shipV);
						h = $('<div oncontextmenu="return false;" class="ship"><span class="shiptype">'+shipDisplayName+'</span><span class="pointcost">'+shipV.pointCost+'p</span> -<span class="addship clickable">Add to fleet</span> -<span class="showship clickable">Show details</span></div>');
                        $(".addship", h).on("click",  this.buyShip.bind(this, shipV.phpclass));
                        $(".showship", h).on("click", gamedata.onShipContextMenu.bind(this, shipV.phpclass, faction));
                        
                        h.appendTo(targetNode);
					} //end of variant
				} //end of base design
			} //end of size
			
            
		} //end of faction
	}, //endof parseShips
	

    expandFaction: function expandFaction(event) {
        var clickedElement = $(this);
        var faction = clickedElement.parent().data("faction");

        if (clickedElement.parent().hasClass("shipshidden")) {
            if (clickedElement.parent().hasClass("listempty")) {
                window.ajaxInterface.getShipsForFaction(faction, function (factionShips) {
                    gamedata.parseShips(factionShips);
                });

                clickedElement.parent().removeClass("listempty");
            }
        }

        clickedElement.parent().toggleClass("shipshidden");
    },

    goToWaiting: function goToWaiting() {},

    parseServerData: function parseServerData(serverdata) {
        if (serverdata == null) {
            window.location = "games.php";
            return;
        }

        if (!serverdata.id) return;

        gamedata.turn = serverdata.turn;
        gamedata.gamephase = serverdata.phase;
        gamedata.activeship = serverdata.activeship;
        gamedata.gameid = serverdata.id;
        gamedata.slots = serverdata.slots;
        //gamedata.ships = serverdata.ships;
        gamedata.thisplayer = serverdata.forPlayer;
        gamedata.maxpoints = serverdata.points;
        gamedata.status = serverdata.status;

        if (gamedata.status == "ACTIVE") {
            window.location = "game.php?gameid=" + gamedata.gameid;
        }

        this.createSlots();
        this.enableBuy();
        this.constructFleetList();
    },

    createNewSlot: function createNewSlot(data) {
        var template = $("#slottemplatecontainer .slot");
        var target = $("#team" + data.team + ".slotcontainer");
        var actual = template.clone(true).appendTo(target);

        actual.data("slotid", data.slot);
        actual.addClass("slotid_" + data.slot);
        gamedata.setSlotData(data);
    },

    createSlots: function createSlots() {
        var selectedSlot = playerManager.getSlotById(gamedata.selectedSlot);
        if (selectedSlot && selectedSlot.playerid != gamedata.thisplayer) {
            $('.slot.slotid_' + selectedSlot.slot).removeClass("selected");
            gamedata.selectedSlot = null;
        }

        for (var i in gamedata.slots) {
            var slot = gamedata.slots[i];
            var slotElement = $('.slot.slotid_' + slot.slot);

            if (!slotElement.length) {
                gamedata.createNewSlot(slot);
            }

            slotElement = $('.slot.slotid_' + slot.slot);
            var data = slotElement.data();
            if (playerManager.isOccupiedSlot(slot)) {
                var player = playerManager.getPlayerInSlot(slot);
                slotElement.data("playerid", player.id);
                slotElement.addClass("taken");
                $(".playername", slotElement).html(player.name);

                if (slot.lastphase == "-2") {
                    slotElement.addClass("ready");
                }

                if (player.id == gamedata.thisplayer) {
                    if (gamedata.selectedSlot == null) gamedata.selectedSlot = slot.slot;
                    $(".leaveslot", slotElement).show();
                } else $(".leaveslot", slotElement).hide();
            } else {
                $(".leaveslot", slotElement).hide();

                slotElement.attr("data-playerid", "");
                slotElement.removeClass("taken");
                $(".playername", slotElement).html("");

                slotElement.removeClass("ready");
            }

            if (gamedata.selectedSlot == slot.slot) {
                gamedata.selectSlot(slot);
            }
        }
    },

    setSlotData: function setSlotData(data) {
        var slot = $(".slot.slotid_" + data.slot);
        $(".name", slot).html(data.name);
        $(".points", slot).html(data.points);

        $(".depx", slot).html(data.depx);
        $(".depy", slot).html(data.depy);
        $(".deptype", slot).html(data.deptype);
        $(".depwidth", slot).html(data.depwidth);
        $(".depheight", slot).html(data.depheight);
        $(".depavailable", slot).html(data.depavailable);
    },

    clickTakeslot: function clickTakeslot() {
        var slot = $(".slot").has($(this));
        var slotid = slot.data("slotid");
        ajaxInterface.submitSlotAction("takeslot", slotid);
    },

    onLeaveSlotClicked: function onLeaveSlotClicked() {
        var slot = $(".slot").has($(this));
        var slotid = slot.data("slotid");
		ajaxInterface.submitSlotAction("leaveslot", slotid);
		window.location = "games.php";
    },

    enableBuy: function enableBuy() {
        var selectedSlot = playerManager.getSlotById(gamedata.selectedSlot);
        if (selectedSlot && selectedSlot.playerid == gamedata.thisplayer) {
            $(".buy").show();
        } else {
            $(".buy").hide();
        }
    },

    buyShip: function buyShip(shipclass) {
        var ship = gamedata.getShipByType(shipclass);

        var slotid = gamedata.selectedSlot;
        var selectedSlot = playerManager.getSlotById(slotid);
        if (selectedSlot.lastphase == "-2") {
            window.confirm.error("This slot has already bought a fleet!", function () {});
            return false;
        }

        $(".confirm").remove();

        //		if (gamedata.canAfford(ship)){
        window.confirm.showShipBuy(ship, gamedata.doBuyShip);
        //		}else{
        //			window.confirm.error("You cannot afford that ship!", function(){});
        //		}
    },

    doBuyShip: function doBuyShip() {
        var shipclass = $(this).data().shipclass;
        var ship = gamedata.getShipByType(shipclass);

        var name = $(".confirm input").val();
        ship.name = name;
        ship.userid = gamedata.thisplayer;

        if ($(".confirm .totalUnitCostAmount").length > 0) {
            ship.pointCost = $(".confirm .totalUnitCostAmount").data("value");
        }

        if (!gamedata.canAfford(ship)) {
            $(".confirm").remove();
            window.confirm.error("You cannot afford that ship!", function () {});
            return;
        }

        if (ship.flight) {
            var flightSize = $(".fighterAmount").html();
            if (!flightSize) {
                flightSize = 1;
            }
            ship.flightSize = Math.floor(flightSize);
        }
	    
		//do note enhancements bought (if any)
		var enhNo = 0;
		var noTaken = 0;
		var target = $(".selectAmount.shpenh" + enhNo);
		while(typeof target.data("enhPrice") != 'undefined'){ //as long as there are enhancements defined...
			noTaken = target.data("count");
			if(noTaken > 0){ //enhancement picked - note!
				ship.enhancementOptions[enhNo][2] = noTaken;
			}
			//go to next enhancement
			enhNo++;
			target = $(".selectAmount.shpenh" + enhNo);
		}	    

        if ($(".confirm .selectAmount").length > 0) {
            if (ship.flight) {

                // and get the amount of launchers on a fighter
                var nrOfLaunchers = 0;

                for (var j in ship.systems[1].systems) {
                    var fighterSystem = ship.systems[1].systems[j];

                    if (!mathlib.arrayIsEmpty(fighterSystem.firingModes) && fighterSystem.missileArray != null) {
                        nrOfLaunchers++;
                    }
                }

                // get all selections of missiles
                var missileOptions = $(".confirm .selectAmount");

                for (var k = 0; k < missileOptions.length; k++) {
                    var firingMode = $(missileOptions[k]).data("firingMode");

                    // divide the bought missiles over the missileArrays
                    var boughtAmount = $(".confirm .selectAmount." + firingMode).data("value");

                    // perLauncher should always get you an integer as result. The UI handles
                    // buying of missiles that way.
                    var perLauncher = boughtAmount;

                    for (var i in ship.systems) {
                        var fighter = ship.systems[i];

                        for (var j in fighter.systems) {
                            var fighterSystem = fighter.systems[j];

                            if (!mathlib.arrayIsEmpty(fighterSystem.firingModes) && fighterSystem.missileArray != null) {
                                // find the correct index, depending on the firingMode
                                for (var index in fighterSystem.firingModes) {
                                    if (fighterSystem.firingModes[index] == firingMode) {
                                        fighterSystem.missileArray[index].amount = perLauncher;
                                    }
                                }
                            }
                        }
                    }
                }
            } else {}
        }

        $(".confirm").remove();
        gamedata.updateFleet(ship);
    },

    //        arrayIsEmpty: function(array){
    //            for(var i in array){
    //                return false;
    //            }
    //
    //            return true;
    //        },

    getShipByType: function getShipByType(type) {

        for (var race in gamedata.allShips) {
            for (var i in gamedata.allShips[race]) {
                var ship = gamedata.allShips[race][i];

                if (ship.phpclass == type) {
                    var shipRet = jQuery.extend(true, {}, ship);

                    // to avoid two different flights pointing to the
                    // same fighter object, also extend each fighter
                    // individually. (This solves the bug of setting
                    // missile amounts, that suddenly are set for all
                    // the fighters of the same type.)
                    for (var i in shipRet.systems) {
                        shipRet.systems[i] = jQuery.extend(true, {}, ship.systems[i]);

                        if (shipRet.flight) {
                            // in case of a flight, also do the systems of the fighters
                            for (var j in shipRet.systems[i].systems) {
                                shipRet.systems[i].systems[j] = jQuery.extend(true, {}, ship.systems[i].systems[j]);
                            }
                        } else {
                            // to avoid problems with ammo and normal ships, also do the
                            // ship systems

                        }
                    }

                    return shipRet;
                }
            }
        }

        return null;
    },

    onReadyClicked: function onReadyClicked() {
        var points = gamedata.calculateFleet();

        if (points == 0) {
            window.confirm.error("You have to buy atleast one ship!", function () {});
            return;
        }

        ajaxInterface.submitGamedata();
    },

    onLeaveClicked: function onLeaveClicked() {
        window.location = "gamelobby.php?gameid=" + gamedata.gameid + "&leave=true";
    },

    onSelectSlotClicked: function onSelectSlotClicked(e) {
        var slotElement = $(".slot").has($(this));
        var slotid = slotElement.data("slotid");
        var slot = playerManager.getSlotById(slotid);

        if (slot.playerid == gamedata.thisplayer) gamedata.selectSlot(slot);
    },

    selectSlot: function selectSlot(slot) {
        $(".slot").removeClass("selected");

        $(".slot.slotid_" + slot.slot).addClass("selected");
        gamedata.selectedSlot = slot.slot;
        this.constructFleetList();
    },

    onShipContextMenu: function onShipContextMenu(phpclass, faction) {

        var ship = gamedata.getShip(phpclass, faction);

        if (!ship.shipStatusWindow) {
            if (ship.flight) {
                ship.shipStatusWindow = flightWindowManager.createShipWindow(ship);
            } else {
                ship.shipStatusWindow = shipWindowManager.createShipWindow(ship);
            }

            shipWindowManager.setData(ship);
        }

        shipWindowManager.open(ship);
        return false;
    },

    getShip: function getShip(phpclass, faction) {
    	var actPhpclass;
    	var actFaction;
    	if (faction != null ){ //faftion provided
			actPhpclass = phpclass;
			actFaction = faction;
			gamedata.displayedShip = phpclass;
			gamedata.displayedFaction = faction;
    	}else{ //recall last opened!
 			actPhpclass = gamedata.displayedShip;
			actFaction =  gamedata.displayedFaction;
    	}
	    
        if (! gamedata.allShips[actFaction]) {
            throw new Error("Unable to find faction " + actFaction)
        }

        return gamedata.allShips[actFaction].find(ship => ship.phpclass == actPhpclass);
    },

    setShipsFromFaction: function setShipsFromFaction(faction, jsonShips) {
        gamedata.allShips[faction] = Object.keys(window.staticShips[faction]).map(function(shipClass) {
            return new Ship(window.staticShips[faction][shipClass]);
        })
    }

};

window.animation = {
    animateWaiting: function animateWaiting() {}
};
