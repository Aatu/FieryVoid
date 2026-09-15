"use strict";

window.declarations = {
   GlobalSide : '',
   GlobalContent : '',
   GlobalDisplay : '',
   //What SHOW was on before the Briefing button was pressed, so pressing it again comes
   //back to the same view rather than to a default.
   lastDataContent : 'EW',

  //reads appropriate EW declarations into table
  readDeclarationsEW: function readDeclarationsEW(){
	function dispShipNew() {
		this.id = -1;
		this.name = "";
		this.class = "";
		this.value = "";
		this.flight = false;
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
	  if(gamedata.isTerrain(ship.shipSizeClass, ship.userid)) continue;
	  if(shipManager.shouldBeHidden(ship)) continue; //Enemy, stealth equipped and undetected, or not deployed yet.	  
      if ( (!shipManager.isDestroyed(ship)) || (shipManager.getTurnDestroyed(ship)>=gamedata.turn) ) if( (declarations.GlobalSide=='Own' && declarations.GlobalDisplay=='Source' && gamedata.isMyShip(ship)) //own ship, own ew, by source
        || (declarations.GlobalSide!='Own' && declarations.GlobalDisplay=='Source' && !gamedata.isMyShip(ship)) //enemy ship, enemy EW, by source
        || (declarations.GlobalSide=='Own' && declarations.GlobalDisplay!='Source' && !gamedata.isMyShip(ship)) //enemy ship, own ew, by target
        || (declarations.GlobalSide!='Own' && declarations.GlobalDisplay!='Source' && gamedata.isMyShip(ship)) //own ship, enemy EW, by target
      ){
	dispShip = new dispShipNew();
        dispShip.id = ship.id;
        dispShip.name = ship.name;
        dispShip.class = ship.shipClass;
        dispShip.value = ship.pointCost || 0;
        //Allegiance rail for the card, from the same helper the combat log uses - so it
        //follows all three of that helper's arms rather than being a CSS-class colour that
        //only 2-team participants would ever see (arch_team_colour_logic).
        dispShip.rail = gamedata.getShipLogColorCss(ship);
        dispShip.EW = new Array();
        //now all EW entries...either own or incoming!
        if (ship.flight || ship.jinkinglimit > 0){//for fighters (or jinking ships), show jinking in all circumstances
	  dispShip.flight = ship.flight;
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
		if (EWentry.turn != gamedata.turn) continue;
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
		if (EWentry.turn != gamedata.turn) continue;
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
		return 1;
	      }else if (!a.flight && b.flight){
		return -1;
	      }else if (a.value > b.value){ //more valuable units first
		return -1;
	      }else if (a.value < b.value){
		return 1;
	      }
	      else return 0;
        });

    return declarations.renderEWCards(dispShips);
  }, //endof function readDeclarationsEW

  /* ── RENDERING (LOG_PANEL_REDESIGN_PLAN.md Stage 4) ─────────────────────────────
     Per-unit cards in the .fv-card grammar, replacing the <big><b>/<br> dump. Same
     information, same reads out of gamedata - only the shape changes.

     The relation word ("at" / "by") is its own dim span with a real gap after it: run
     together with the name, "OEW 3 at Vorchan Talon" reads as one token rather than as
     three fields. */
  railStyle: function railStyle(logColorCss) {
    var m = /color\s*:\s*([^;]+)/.exec(logColorCss || '');
    return m ? 'border-left-color:' + m[1] + ';' : '';
  },

  cardHead: function cardHead(shpEntry, readout) {
    return '<div class="decl-card-top">'
      + '<span class="decl-name">' + shpEntry.name + '</span>'
      + '<span class="decl-class">' + shpEntry.class + '</span>'
      + '<span class="decl-total">' + readout + '</span>'
      + '</div>';
  },

  renderEWCards: function renderEWCards(dispShips) {
    var out = [];
    var relation = (declarations.GlobalDisplay == 'Source') ? 'at' : 'by';

    for (var i in dispShips) {
      var shpEntry = dispShips[i];

      /* PER-UNIT TOTALS - currently a mental sum. Passive DEW/jinking is reported on its
         own because it is a property of the unit, not something aimed anywhere; the
         directed total is what the player is actually comparing between units. */
      var directed = 0;
      var passive = 0;
      var passiveName = '';
      for (var e in shpEntry.EW) {
        var entry = shpEntry.EW[e];
        var amount = parseFloat(entry.value) || 0;
        if (entry.targetName === '') {
          passive += amount;
          passiveName = entry.name;
        } else {
          directed += amount;
        }
      }

      var readout = [];
      if (passiveName) readout.push(passiveName.toUpperCase() + ' ' + passive);
      readout.push((relation === 'at' ? 'EMITTED ' : 'RECEIVED ') + directed);

      var rows = '';
      for (var e2 in shpEntry.EW) {
        var EWentry = shpEntry.EW[e2];
        rows += '<li><span class="decl-what">' + EWentry.name + '</span>'
          + '<span class="decl-val">' + EWentry.value + '</span>';
        if (EWentry.targetName != '') {
          rows += '<span class="decl-rel">' + relation + '</span>'
            + '<span class="decl-who">' + EWentry.targetName
            + ' <i>(' + EWentry.targetClass + ')</i></span>';
        } else {
          rows += '<span class="decl-rel"></span><span class="decl-who"></span>';
        }
        rows += '</li>';
      }

      out.push('<div class="decl-card" style="' + declarations.railStyle(shpEntry.rail) + '">'
        + declarations.cardHead(shpEntry, readout.join(' · '))
        + '<ul class="decl-rows">' + rows + '</ul></div>');
    }

    if (out.length === 0) return '<div class="decl-empty">Nothing declared.</div>';
    return out.join('');
  },

  renderFireCards: function renderFireCards(dispShips) {
    var out = [];
    var relation = (declarations.GlobalDisplay == 'Source') ? 'at' : 'by';

    for (var i in dispShips) {
      var shpEntry = dispShips[i];

      /* PER-TARGET ROLL-UP: the salvos are grouped by the unit at the other end, so
         "everything shooting at my Vorchan" is one heading rather than a scan down a flat
         list. Insertion order is preserved so the grouping never reorders the salvos
         themselves. */
      var groups = {};
      var order = [];
      var salvos = 0;
      var weapons = 0;

      for (var f in shpEntry.fire) {
        var salvo = shpEntry.fire[f];
        var key = salvo.oppName !== '' ? (salvo.oppName + ' \u0001 ' + salvo.oppClass) : '\u0001hex';
        if (!groups[key]) { groups[key] = []; order.push(key); }
        groups[key].push(salvo);
        salvos++;
        weapons += (parseInt(salvo.count, 10) || 0);
      }

      var body = '';
      for (var g = 0; g < order.length; g++) {
        var list = groups[order[g]];
        var head = order[g].split(' \u0001 ');

        if (order[g] !== '\u0001hex') {
          body += '<div class="decl-group">'
            + '<span class="decl-rel">' + relation + '</span>'
            + '<span class="decl-who">' + head[0] + ' <i>(' + head[1] + ')</i></span>'
            + '<span class="decl-group-count">' + list.length
            + (list.length === 1 ? ' salvo' : ' salvos') + '</span></div>';
        } else {
          body += '<div class="decl-group"><span class="decl-who">at hex</span></div>';
        }

        body += '<ul class="decl-rows">';
        for (var s = 0; s < list.length; s++) {
          var sal = list[s];
          var chance = '';
          var meter = '';
          if (sal.oppName !== '') {
            chance = sal.chanceMin + (sal.chanceMax > sal.chanceMin ? '–' + sal.chanceMax : '') + '%';
            //A small meter off the LOW end of the range, which is the number a player
            //plans against. Clamped: to-hit can be reported below 0 and above 100.
            var pct = Math.max(0, Math.min(100, parseFloat(sal.chanceMin) || 0));
            meter = '<span class="decl-meter"><span style="width:' + pct + '%"></span></span>';
          }
          body += '<li><span class="decl-what">' + sal.count + 'x ' + sal.wpnName + '</span>'
            + '<span class="decl-val">' + chance + '</span>'
            + meter + '</li>';
        }
        body += '</ul>';
      }

      var readout = salvos + (salvos === 1 ? ' SALVO' : ' SALVOS') + ' · ' + weapons + ' WPN';

      out.push('<div class="decl-card" style="' + declarations.railStyle(shpEntry.rail) + '">'
        + declarations.cardHead(shpEntry, readout)
        + body + '</div>');
    }

    if (out.length === 0) return '<div class="decl-empty">Nothing declared.</div>';
    return out.join('');
  },
  
	
 
  //reads appropriate Fire declarations into table
  readDeclarationsFire: function readDeclarationsEW(){
	function dispShipNew() {
		this.id = -1;
		this.name = "";
		this.class = "";
		this.value = "";
		this.flight = false;
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
      if ( (!shipManager.isDestroyed(ship)) || (shipManager.getTurnDestroyed(ship)>=gamedata.turn) ) if( (declarations.GlobalSide=='Own' && declarations.GlobalDisplay=='Source' && gamedata.isMyShip(ship)) //own ship, own fire, by source
        || (declarations.GlobalSide!='Own' && declarations.GlobalDisplay=='Source' && !gamedata.isMyShip(ship)) //enemy ship, enemy fire, by source
        || (declarations.GlobalSide=='Own' && declarations.GlobalDisplay!='Source' && !gamedata.isMyShip(ship)) //enemy ship, own fire, by target
        || (declarations.GlobalSide!='Own' && declarations.GlobalDisplay!='Source' && gamedata.isMyShip(ship)) //own ship, enemy fire, by target
      ){
	dispShip = new dispShipNew();
        dispShip.id = ship.id;
        dispShip.name = ship.name;
        dispShip.class = ship.shipClass;
        dispShip.value = ship.pointCost || 0;
	dispShip.flight = ship.flight;
        dispShip.rail = gamedata.getShipLogColorCss(ship); //see the note in readDeclarationsEW
        //now all fire entries...either own or incoming!
        if(declarations.GlobalDisplay=='Source'){ //by source - display fire dished out by self!  
 	  for (var sysNo = 0; sysNo < ship.systems.length; sysNo++){
            var systemsTab = new Array();
            if (!ship.flight){ //actual ship system
	      systemsTab = [ship.systems[sysNo]];
            }else{ //fighter - with subsystems!
              //BUT both fighter and subsystem numeration is strange (eg. 10-elements table with only 1 or 2 elements)
	      systemsTab = new Array(); //if fighter does not exist, this will be just left empty
	      if (ship.systems[sysNo]){ //such fighter exists
			for (var subSysNo = 0;subSysNo<ship.systems[sysNo].systems.length;subSysNo++){
			  if ( ship.systems[sysNo].systems[subSysNo]) {  
			    systemsTab.push(ship.systems[sysNo].systems[subSysNo]); //creating table with actual systems only...
			  }
			}
	      }
            }
	    for (var actSysNo = 0; actSysNo < systemsTab.length; actSysNo++){
	      var actSys = systemsTab[actSysNo];	    
	      if (actSys.fireOrders.length > 0){
		for (var fireNo = 0; fireNo < actSys.fireOrders.length; fireNo++){
		  var weapon = actSys;
		  var order = actSys.fireOrders[fireNo]; 
		  if (order.turn != gamedata.turn) continue;
		  if (order.type.indexOf('ntercept') == -1){ //this is actual offensive fire! skip 'intercept' and 'selfIntercept' orders
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
			var modeIteration = 0;
			modeIteration = order.firingMode; //change weapons data to reflect mode actually used
			    if(modeIteration != weapon.firingMode){
				while(modeIteration != weapon.firingMode){ //will loop until correct mode is found
				weapon.changeFiringMode();
				}
			    }
		      //`order` passed so a ballistic resolves ITS OWN launch hex - a homing missile on a
		      //later pass flies in from its target's previous hex, not from its launcher.
		      var toHit = weaponManager.calculateHitChange(ship, targetUnit, weapon, order.calledid, order).hitChance;
		      if (toHit < dispFireEntry.chanceMin) dispFireEntry.chanceMin = toHit;
		      if (toHit > dispFireEntry.chanceMax) dispFireEntry.chanceMax = toHit;
		    }			  
		  }
		}    
	      }
	    }
	  }		
        }else{ //by target - display EW dished out at self BY OPPONENT! (for fighters - CCEW)
	  for (var j in gamedata.ships){
            var srcShip = gamedata.ships[j]; 
            if (srcShip.team != ship.team) { //enemy units only!		    
		  for (var sysNo = 0; sysNo < srcShip.systems.length; sysNo++){
		    var systemsTab = new Array();
		    if (!srcShip.flight){ //actual ship system
		      systemsTab = [srcShip.systems[sysNo]];
		    }else{ //fighter - with subsystems!
		      //BUT both fighter and subsystem numeration is strange (eg. 10-elements table with only 1 or 2 elements)
		      systemsTab = new Array(); //if fighter does not exist, this will be just left empty
		      if (srcShip.systems[sysNo]){ //such fighter exists
			for (var subSysNo = 0;subSysNo<srcShip.systems[sysNo].systems.length;subSysNo++){
			  if ( srcShip.systems[sysNo].systems[subSysNo]) {  
			    systemsTab.push(srcShip.systems[sysNo].systems[subSysNo]); //creating table with actual systems only...
			  }
			}
		      }
		    }
		    for (var actSysNo = 0; actSysNo < systemsTab.length; actSysNo++){
		      var actSys = systemsTab[actSysNo];	      
		      if (actSys.fireOrders.length > 0){
			for (var fireNo = 0; fireNo < actSys.fireOrders.length; fireNo++){
			  var weapon = actSys;
			  var order = actSys.fireOrders[fireNo]; 
		  	  if (order.turn != gamedata.turn) continue;
			  if (order.type.indexOf('ntercept') == -1 && order.targetid == ship.id){ //fire at self! skip 'intercept' and 'selfIntercept' orders
			    var dispFireEntry = new dispFireNew();
			    dispFireEntry.wpnName = weapon.displayName + ' ('+ weapon.firingModes[order.firingMode] +')';
			    if (order.calledid > -1 ){
			      dispFireEntry.wpnName += ' CALLED';
			      dispFireEntry.calledid = order.calledid;
			    }
			    dispFireEntry.oppId = srcShip; //actually, here firing ship id!
			    //if such order exists, on list, find it; else fill basic data and add to list
			    var alreadyExists = false;
			    for (var existingEntry in dispShip.fire){
			      var extEntry = dispShip.fire[existingEntry];
			      if ( extEntry.wpnName == dispFireEntry.wpnName && extEntry.oppId == dispFireEntry.oppId ){
				dispFireEntry = extEntry;    
				alreadyExists = true;
			      }
			    }
			    if(!alreadyExists){ //fill initial data
			      dispFireEntry.oppName = srcShip.name; 
			      dispFireEntry.oppClass = srcShip.shipClass;
			      dispFireEntry.calledid = order.calledid;
			      dispShip.fire.push(dispFireEntry);
			    }
			    dispFireEntry.count++;		
				var modeIteration = 0;		  
				modeIteration = order.firingMode; //change weapons data to reflect mode actually used
				    if(modeIteration != weapon.firingMode){
					while(modeIteration != weapon.firingMode){ //will loop until correct mode is found
					weapon.changeFiringMode();
					}
				    }
			      //`order` passed - see the matching call in the outgoing sweep above.
			      var toHit = weaponManager.calculateHitChange(srcShip, ship, weapon, order.calledid, order).hitChance;
			      if (toHit < dispFireEntry.chanceMin) dispFireEntry.chanceMin = toHit;
			      if (toHit > dispFireEntry.chanceMax) dispFireEntry.chanceMax = toHit;
			  }
			}    
		      }
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
		return 1;
	      }else if (!a.flight && b.flight){
		return -1;
	      }else if (a.value > b.value){ //more valuable units first
		return -1;
	      }else if (a.value < b.value){
		return 1;
	      }
	      else return 0;
        });
    
    return declarations.renderFireCards(dispShips);
  }, //endof function readDeclarationsFire
	
	
  
  //writes actual content to declarationsActual div
  fillDeclarationsActual: function fillDeclarationsActual() {
    //fix data (if not done yet)
    if(declarations.GlobalSide=='') declarations.GlobalSide = 'Own';
    if(declarations.GlobalContent=='') declarations.GlobalContent = 'EW';
    if(declarations.GlobalDisplay=='') declarations.GlobalDisplay = 'Source';
    
    /* BRIEFING IS A MODE. It used to be a button that overwrote this div while the SIDE
       and BY chips carried on claiming the panel showed, say, "Own EW by Source" - the
       controls lied about what was on screen. Routing it through the same dispatch is what
       fixes that, and syncControls dims the two groups that mean nothing for it. */
    if (declarations.GlobalContent == 'Briefing') {
      declarations.callGameDescriptionActual();
      declarations.syncControls();
      return;
    }

    //prepare data (actually text!)
    var srcData = '';
    if(declarations.GlobalContent=='EW'){ //display EW declarations
      srcData = declarations.readDeclarationsEW();
    }else{ //display fire declarations
      srcData = declarations.readDeclarationsFire();
    }

    //display text. The mode used to be restated as an underlined <big> heading above the
    //data; the chips say it, and the panel readout says it again, so a third copy was one
    //line of a 150px panel spent repeating the controls.
    var targetDiv = document.getElementById("declarationsActual"); //$(".declarationsActual");
    targetDiv.style.display = "block";
    targetDiv.innerHTML = srcData;

    declarations.syncControls();
  }, //endof function fillDeclarationsActual

  /* Paint the chips from the state and write the panel readout. Called after every mode
     change and when the tab is shown, so the controls can never disagree with the content
     the way the old "Show description" button did. */
  syncControls: function syncControls() {
    var briefing = (declarations.GlobalContent == 'Briefing');

    $("#declSide .fv-log-chip").each(function () {
      $(this).attr("aria-pressed", $(this).data("side") === declarations.GlobalSide ? "true" : "false");
    });
    $("#declContent .fv-log-chip").each(function () {
      $(this).attr("aria-pressed", $(this).data("content") === declarations.GlobalContent ? "true" : "false");
    });
    $("#declBriefing").attr("aria-pressed", briefing ? "true" : "false");
    $("#declDisplay .fv-log-chip").each(function () {
      $(this).attr("aria-pressed", $(this).data("display") === declarations.GlobalDisplay ? "true" : "false");
    });

    /* BRIEFING IS ITS OWN CONTROL (user, 2026-08-31), sitting apart from the filters on the
       far right, because it is not a filter - it replaces the whole panel. So all THREE
       filter groups dim while it is on, not just two: none of them means anything for a
       briefing, including SHOW, which no longer carries Briefing as a third value. */
    $("#declSide, #declContent, #declDisplay").toggleClass("is-disabled", briefing);

    if (window.botPanel && botPanel.setMeta) {
      botPanel.setMeta('declarations', briefing
        ? 'BRIEFING'
        : (declarations.GlobalSide + ' ' + declarations.GlobalContent
           + ' BY ' + declarations.GlobalDisplay).toUpperCase());
    }
  },

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
    declarations.GlobalContent = 'Fire';
    declarations.fillDeclarationsActual();
  },
  callBriefing: function callBriefing() {
    declarations.GlobalContent = 'Briefing';
    declarations.fillDeclarationsActual();
  },
  callSource: function callSource() {
    declarations.GlobalDisplay = 'Source';
    declarations.fillDeclarationsActual();
  },
  callTarget: function callTarget() {
    declarations.GlobalDisplay = 'Target';
    declarations.fillDeclarationsActual();
  },

  /* THE BRIEFING, in the log panel's own grammar (user, 2026-08-31). Same three pieces as
     before - game name, rules of engagement, scenario text - but as a card with a micro-cap
     head and the rules as .fv-tag chips rather than a run of "<br>Xxx enabled." lines. The
     rules read better as chips: a player wants to know at a glance WHICH of the optional
     rules are on, and a few short tags answer that in one scan.
     The chip list is one-sided by design, exactly as the old list was: only a rule that IS
     in play gets a tag, and a rule that is off is simply absent. */
  callGameDescriptionActual: function callGameDescriptionActual() {
    var tags = [];

    //Preserved verbatim from the old version, including the quirk that the friendlyFire
    //branch is keyed on 'friendlyFire' being PRESENT rather than on its value.
    if (gamedata.rules && 'friendlyFire' in gamedata.rules) tags.push('Friendly fire');
    if (gamedata.rules && 'allowMines' in gamedata.rules) tags.push('Mines');
    if (gamedata.rules && 'allowReinforcements' in gamedata.rules) tags.push('Reinforcements');

    //Desperate is the one rule with a SIDE to it, so it gets a worded tag of its own.
    var desperate = '';
    if (gamedata.rules && 'desperate' in gamedata.rules) {
      switch (gamedata.rules.desperate) {
        case 1: desperate = 'Desperate: Team 1'; break;
        case 2: desperate = 'Desperate: Team 2'; break;
        case -1: desperate = 'Desperate: both teams'; break;
        default: break;   //normal engagement - nothing worth a tag
      }
    }
    if (desperate) tags.push(desperate);

    var html = '<div class="decl-briefing">';
    if (gamedata.name) {
      html += '<div class="decl-briefing-name">' + gamedata.name + '</div>';
    }

    html += '<div class="decl-briefing-rules">'
      + '<span class="fv-decl-rules">Rules of engagement:</span>';
    if (tags.length) {
      for (var i = 0; i < tags.length; i++) {
        html += '<span class="fv-tag fv-tag--rule">' + tags[i] + '</span>';
      }
    } else {
      html += '<span class="fv-tag">Standard</span>';
    }
    html += '</div>';

    if (gamedata.description) {
      html += '<div class="decl-briefing-text">'
        + gamedata.description.replace(/\n/g, "<br>") + '</div>';
    }
    html += '</div>';

    var targetDiv = document.getElementById("declarationsActual");
    targetDiv.style.display = "block";
    targetDiv.innerHTML = html;
  }

}

/* The declarations control bar (LOG_PANEL_REDESIGN_PLAN.md Stage 4). Three segmented chip
   groups replacing four rows of <input type="button"> and their <br>s. Each chip just sets
   its axis and re-runs the same dispatch the old buttons did. */
$(function () {
    $("#declSide").on("click", ".fv-log-chip", function () {
        if ($("#declSide").hasClass("is-disabled")) return;
        declarations.GlobalSide = String($(this).data("side"));
        declarations.fillDeclarationsActual();
    });

    $("#declContent").on("click", ".fv-log-chip", function () {
        if ($("#declContent").hasClass("is-disabled")) return;
        declarations.GlobalContent = String($(this).data("content"));
        declarations.fillDeclarationsActual();
    });

    /* Briefing TOGGLES: pressing it again returns to whichever EW/Fire view was up before,
       so it can never strand the panel in a mode whose only way out is a guess. */
    $("#declBriefing").on("click", function () {
        if (declarations.GlobalContent === 'Briefing') {
            declarations.GlobalContent = declarations.lastDataContent || 'EW';
        } else {
            declarations.lastDataContent = declarations.GlobalContent;
            declarations.GlobalContent = 'Briefing';
        }
        declarations.fillDeclarationsActual();
    });

    $("#declDisplay").on("click", ".fv-log-chip", function () {
        if ($("#declDisplay").hasClass("is-disabled")) return;
        declarations.GlobalDisplay = String($(this).data("display"));
        declarations.fillDeclarationsActual();
    });

    /* The panel used to open blank until a button was pressed. It has three defaults
       already (Own / EW / Source, applied at the top of fillDeclarationsActual), so
       drawing them on first show costs nothing and gives the tab something to say. */
    $("#declarations").on("onshow", function () {
        declarations.fillDeclarationsActual();
    });
});
