"use strict";

window.declarations = {
   GlobalSide : '',
   GlobalContent : '',
   GlobalDisplay : '',

  //resets EW and re-displays
  doResetEW: function doResetEW(shipID){
    ew.resetEW(shipID);
    declarations.fillDeclarationsActual();
  },
  
  //reads appropriate EW declarations into table
  readDeclarationsEW: function readDeclarationsEW(){
	function dispShipNew() {
		this.id = -1;
		this.name = "";
		this.class = "";
		this.value = "";
		this.EW = new Array();
	}
	function dispEWNew() {
		this.name = "";
		this.targetName = "";
		this.targetClass = "";
		this.value = 0;
	}
	  
    var dispShips = new Array(); 
    var dispShip = new dispShipNew();
    var dispEWEntry = new dispEWNew();
    for (var i in gamedata.ships){
      var ship = gamedata.ships[i];
      if( (declarations.GlobalSide=='Own' && declarations.GlobalDisplay=='Source' && gamedata.isMyShip(ship)) //own ship, own ew, by source
        || (declarations.GlobalSide!='Own' && declarations.GlobalDisplay=='Source' && !gamedata.isMyShip(ship)) //enemy ship, enemy EW, by source
        || (declarations.GlobalSide=='Own' && declarations.GlobalDisplay!='Source' && !gamedata.isMyShip(ship)) //enemy ship, own ew, by target
        || (declarations.GlobalSide!='Own' && declarations.GlobalDisplay!='Source' && gamedata.isMyShip(ship)) //own ship, enemy EW, by target
      ){
	dispShip = new dispShipNew();
        dispShip.id = ship.id;
        dispShip.name = ship.name;
        dispShip.class = ship.shipClass;
        dispShip.value = ship.pointCost;
        dispShip.EW = new Array();
        //now all EW entries...either own or incoming!
        if (ship.flight){//for fighters, show jinking in all circumstances
	  dispEWEntry = new dispEWNew();	
          dispEWEntry.name = 'jinking';
          dispEWEntry.targetName = '';
          dispEWEntry.targetClass = '';
          dispEWEntry.value = shipManager.movement.getJinking(ship);
          dispShip.EW.push(dispEWEntry);
        }else{//for ships, show DEW in all circumstances
          dispEWEntry = new dispEWNew();	
          dispEWEntry.name = 'DEW';
          dispEWEntry.targetName = '';
          dispEWEntry.targetClass = '';
          dispEWEntry.value = ew.getDefensiveEW(ship);
          dispShip.EW.push(dispEWEntry);
        }
        if(declarations.GlobalDisplay=='Source'){ //by source - display EW dished out by self!
          if (!ship.flight){ //fighters do not emit any EW            
            for (var e in ship.EW) {
              var EWentry = ship.EW[e];
	      if (EWentry.type != 'DEW'){ //DEW already listed
		      dispEWEntry = new dispEWNew();		    
		      dispEWEntry.name = EWentry.type;
		      dispEWEntry.value = EWentry.amount;
		      if (EWentry.targetid>0){
			var targetUnit = gamedata.getShip(EWentry.targetid);
			dispEWEntry.targetName = targetUnit.name;
			dispEWEntry.targetClass = targetUnit.shipClass;
		      }else{//targetless EW                
			dispEWEntry.targetName = '';
			dispEWEntry.targetClass = '';
		      }              
		      dispShip.EW.push(dispEWEntry);
	      }
            }
          }
        }else{ //by target - display EW dished out at self BY OPPONENT! (for fighters - CCEW)
          for (var j in gamedata.ships){
            var srcShip = gamedata.ships[j]; 
            if (srcShip.team != ship.team){ //enemy ships only
              for (var e in srcShip.EW) {
                var EWentry = srcShip.EW[e];
                if (EWentry.targetid == ship.id //self is target
                  || (ship.flight && EWentry.type == 'CCEW') //self is fighter and EWentry is CCEW
                ){
		  dispEWEntry = new dispEWNew();	 	
                  dispEWEntry.name = EWentry.type;
                  dispEWEntry.value = EWentry.amount;
                  dispEWEntry.targetName = srcShip.name; //source, in this case
                  dispEWEntry.targetClass = srcShip.shipClass;
		  dispShip.EW.push(dispEWEntry);
                }
              }
            }
          }
        }
        dispShips.push(dispShip);
      }
    }    
      //sort ships by value
      dispShips.sort(function(a, b){
	if (a.flight && !b.flight){//fighters always after ships
		return -1;
	      }else if (!a.flight && b.flight){
		return 1;
	      }else if (a.value > b.value){ //more valuable units first
		return 1;
	      }else if (a.value < b.value){
		return -1;
	      }
	      else return 0;
        });
    
    var txt = '';
    //change to text
    for (var i in dispShips){
      var shpEntry = dispShips[i];
      txt += '<big><b>' + shpEntry.name + ' <i>(' + shpEntry.class + ')</i> ' + '</b></big>';
      //reset EW button - for own ships
	/* ...maybe that won't be necessary...
      if ( gamedata.gamephase == 1 && declarations.GlobalSide=='Own' && declarations.GlobalDisplay=='Source') {//Initial phase, displaying own ships
        if (shpEntry.EW.count > 1 ){ //has something besides DEW!
          txt += '<input type="button" value="Reset EW" onclick="declarations.doResetEW(' + shpEntry.id + ');">';
        }
      }
      */
      txt += '<br>';
      //now actual entries
      for(var e in shpEntry.EW){
      	var EWentry = shpEntry.EW[e];
	txt += ' -- ' + EWentry.name + ' <b>' + EWentry.value + '</b>';
	if (EWentry.targetName != ''){
	  if (declarations.GlobalDisplay=='Source'){
	    txt += ' at '  ;
	  }else{
	    txt += ' by ';
	  }
          txt += EWentry.targetName + ' <i>('+ EWentry.targetClass +')</i>';
	}
	txt += '<br>';
      }
    }
    
    return txt;
  }, //endof function readDeclarationsEW
  
	
 
  //reads appropriate Fire declarations into table
  readDeclarationsFire: function readDeclarationsEW(){
	function dispShipNew() {
		this.id = -1;
		this.name = "";
		this.class = "";
		this.value = "";
		this.fire = new Array();
	}
	function dispFireNew() {
		this.wpnName = "";
		this.oppName = "";
		this.oppClass = "";
		this.oppId = -1;
		this.calledid = -1;
		this.count = 0;
		this.chanceMin = 1000;
		this.chanceMax = -1000;
	}
	  
    var dispShips = new Array(); 
    var dispShip = new dispShipNew();    
    for (var i in gamedata.ships){
      var ship = gamedata.ships[i];
      if( (declarations.GlobalSide=='Own' && declarations.GlobalDisplay=='Source' && gamedata.isMyShip(ship)) //own ship, own fire, by source
        || (declarations.GlobalSide!='Own' && declarations.GlobalDisplay=='Source' && !gamedata.isMyShip(ship)) //enemy ship, enemy fire, by source
        || (declarations.GlobalSide=='Own' && declarations.GlobalDisplay!='Source' && !gamedata.isMyShip(ship)) //enemy ship, own fire, by target
        || (declarations.GlobalSide!='Own' && declarations.GlobalDisplay!='Source' && gamedata.isMyShip(ship)) //own ship, enemy fire, by target
      ){
	dispShip = new dispShipNew();
        dispShip.id = ship.id;
        dispShip.name = ship.name;
        dispShip.class = ship.shipClass;
        dispShip.value = ship.pointCost;
        //now all fire entries...either own or incoming!
        if(declarations.GlobalDisplay=='Source'){ //by source - display fire dished out by self!  
 	  for (var sysNo = 0; sysNo < ship.systems.length; sysNo++){
            var systemsTab = new Array();
            if (!ship.flight){ //actual ship system
	      systemsTab = [ship.systems[sysNo]];
            }else{ //fighter - with subsystems!
              systemsTab = ship.systems[sysNo].systems;
            }
            for (var actSysNo = 0; actSysNo < systemsTab.length; actSysNo++){
	      var actSys = systemsTab[actSysNo];
	      if (actSys.fireOrders.length > 0){
		for (var fireNo = 0; fireNo < actSys.fireOrders.length; fireNo++){
		  var weapon = ship.systems[sysNo];
		  var order = ship.systems[sysNo].fireOrders[fireNo];
		  if (order.type.indexOf('intercept') !== -1){ //this is actual offensive fire!
		    var dispFireEntry = new dispFireNew();
		    dispFireEntry.wpnName = weapon.displayName + ' ('+ weapon.firingModes[order.firingMode] +')';
	            if (order.calledid > -1 ){
		      dispFireEntry.wpnName += ' CALLED';
		      dispFireEntry.calledid = order.calledid;
		    }
		    dispFireEntry.oppId = order.targetid;
	            //if such order exists, on list, find it; else fill basic data and add to list
	            var alreadyExists = false;
	            for (var existingEntry in dispShip.fire){
		      var extEntry = dispShip.fire[existingEntry];
		      if ( extEntry.wpnName == dispFireEntry.wpnName && extEntry.oppId == dispFireEntry.oppId ){
			dispFireEntry = extEntry;    
			alreadyExists = true;
		      }
		    }
	            var targetUnit;
	            if (dispFireEntry.oppId > -1){
		      targetUnit = gamedata.getShip(dispFireEntry.oppId);
		    }
	            if(!alreadyExists){ //fill initial data
		      if (dispFireEntry.oppId > -1){
			dispFireEntry.oppName = targetUnit.name; 
                  	dispFireEntry.oppClass = targetUnit.shipClass;
		      }
		      dispFireEntry.calledid = order.calledid;
		      dispShip.fire.push(dispFireEntry);
		    }
	            dispFireEntry.count++;
	            if(dispFireEntry.oppId > -1){ //fire at actual target
		      var toHit = weaponManager.calculateHitChange(dispShip, targetUnit, weapon, order.calledid);
		      if (toHit < dispFireEntry.chanceMin) dispFireEntry.chanceMin = toHit;
		      if (toHit > dispFireEntry.chanceMax) dispFireEntry.chanceMax = toHit;
		    }			  
		  }
		}    
	      }
	    }
	  }		
        }else{ //by target - display EW dished out at self BY OPPONENT! (for fighters - CCEW)
		/*
          for (var j in gamedata.ships){
            var srcShip = gamedata.ships[j]; 
            if (srcShip.team != ship.team){ //enemy ships only
              for (var e in srcShip.EW) {
                var EWentry = srcShip.EW[e];
                if (EWentry.targetID == ship.id //self is target
                  || (ship.flight && EWentry.type == 'CCEW') //self is fighter and EWentry is CCEW
                ){
		  dispEWEntry = new dispEWNew();	 	
                  dispEWEntry.name = EWentry.type;
                  dispEWEntry.value = EWentry.amount;
                  dispEWEntry.targetName = srcShip.name; //source, in this case
                  dispEWEntry.targetClass = srcShip.shipClass;
		  dispShip.EW.push(dispEWEntry);
                }
              }
            }
          }
	  */
        }
        dispShips.push(dispShip);
      }
    }    
      //sort ships by value
      dispShips.sort(function(a, b){
	if (a.flight && !b.flight){//fighters always after ships
		return -1;
	      }else if (!a.flight && b.flight){
		return 1;
	      }else if (a.value > b.value){ //more valuable units first
		return 1;
	      }else if (a.value < b.value){
		return -1;
	      }
	      else return 0;
        });
    
    var txt = '';
    //change to text
    for (var i in dispShips){
      var shpEntry = dispShips[i];
      txt += '<big><b>' + shpEntry.name + ' <i>(' + shpEntry.class + ')</i> ' + '</b></big>';
      txt += '<br>';
      //now actual entries
      for(var f in shpEntry.fire){
      	var salvo = shpEntry.fire[f];
	txt += ' -- ' + salvo.count + 'x <b>' + salvo.wpnName + '</b>';
	if (declarations.GlobalDisplay=='Source'){
	  txt += ' at '  ;
	}else{
	  txt += ' by ';
	}
	if (salvo.oppName != ''){	  
          txt += salvo.oppName + ' <i>('+ salvo.targetClass +')</i>';
	  txt += ', ' + salvo.chanceMin;
	  if (salvo.chanceMax > salvo.chanceMin){
	     txt += '..' + salvo.chanceMax;
	  }
	  txt += '%';
	}else{
	  txt += 'hex';	
	}
	txt += '<br>';
      }
    }
    return txt;
  }, //endof function readDeclarationsFire
	
	
  
  //writes actual content to declarationsActual div
  fillDeclarationsActual: function fillDeclarationsActual() {
    //fix data (if not done yet)
    if(declarations.GlobalSide=='') declarations.GlobalSide = 'Own';
    if(declarations.GlobalContent=='') declarations.GlobalContent = 'EW';
    if(declarations.GlobalDisplay=='') declarations.GlobalDisplay = 'Source';
    
    //prepare data (actually text!)
    var srcData = '';
    if(declarations.GlobalContent=='EW'){ //display EW declarations
      srcData = declarations.readDeclarationsEW();
    }else{ //display fire declarations
      srcData = declarations.readDeclarationsFire();
    }
    
    //prepare text
    var newText = '';
    //start with header
    newText = '<br><big><b><u>';
    newText += declarations.GlobalSide + ' ' + declarations.GlobalContent + ' by ' + declarations.GlobalDisplay;
    newText += '</b></u></big><br><br>';
    //actual data
    newText += srcData;
    
    //display text
    var targetDiv = document.getElementById("declarationsActual"); //$(".declarationsActual");
    targetDiv.style.display = "block";
    targetDiv.innerHTML = newText;
  }, //endof function fillDeclarationsActual
  

  callOwn: function callOwn() {
    declarations.GlobalSide = 'Own';
    declarations.fillDeclarationsActual();
  },  
  callEnemy: function callEnemy() {
    declarations.GlobalSide = 'Enemy';
    declarations.fillDeclarationsActual();
  },    
  callEW: function callEW() {
    declarations.GlobalContent = 'EW';
    declarations.fillDeclarationsActual();
  },      
  callFire: function callFire() {
    GlobalContent = 'Fire';
    declarations.fillDeclarationsActual();
  },  
  callSource: function callSource() {
    declarations.GlobalDisplay = 'Source';
    declarations.fillDeclarationsActual();
  },    
  callTarget: function callTarget() {
    declarations.GlobalDisplay = 'Target';
    declarations.fillDeclarationsActual();
  }    
  
}
