<?php
/*class responsible for unit enhancements
Most enhancements made are based on official ones, but they're changed and/or repointed
*/
class Enhancements{

    /* Choice-valued options (tuple index 7 = the list of things that may be picked - currently only
       CHAM_DISG) have to enumerate every ship in the faction to build that list, and on a cold
       faction-dir map that means constructing every ship class in the codebase: ~6s and ~170MB.
       Only the BUY dialogs ever render the list, so the in-game blueprint path
       (ShipLoader::getShipsByClass, run on every single game.php load) switches it off.
       Default TRUE deliberately: a caller that forgets to disable it pays time, which is loud and
       measurable, whereas a caller that wrongly got false would show an empty dropdown, which is
       silent. In game the stored pick is resolved by NAME (getDisguiseLabel - one construction),
       so nothing there needs the list. */
    public static $offerChoiceLists = true;

    public static function compareEnhancements($enhA, $enhB)//to set them in order
	//REVERSE order, as they're effectively reversed in front end too
    {
        if ($enhA[6] && (!$enhB[6])) { //Options first
            return 1;
        } else if ($enhB[6] && (!$enhA[6])) {
            return -1;
        } else if ( $enhA[0] < $enhB[0] ) { //by ID, ascending
            return 1;
        } else if ($enhA[0] > $enhB[0] ) {
            return -1;
        } else {
            return 0;
        }
    } //endof function compareInterceptAbility	
	
  /*sets enhancement options for a given ship
  called by setEnhancements if database is empty
  */
  public static function setEnhancementOptions($ship){
	  /* ALL ENHANCEMENT OPTIONS ARE DEFINED HERE 
	  	(or rather in appropriate subroutines for fighters and ships)
	  */
	if($ship instanceof FighterFlight){
		Enhancements::setEnhancementOptionsFighter($ship);
	}else{
		Enhancements::setEnhancementOptionsShip($ship);
	}
	//sort enhancements - options first, then by name
	usort($ship->enhancementOptions, [self::class, 'compareEnhancements']);

	/* PER-SYSTEM enhancement offers (WEAPON_ENHANCEMENTS_PLAN.md §4.2). Deliberately called from
	   HERE rather than from setEnhancementOptions' four call sites, so the two can never be called
	   apart - a path that builds ship-level options but not per-system ones would show an empty
	   gold section with no error anywhere. Self-gated on $offerSystemEnhancements and on hull age. */
	Enhancements::setSystemEnhancementOptions($ship);
  } //endof function setEnhancementOptions
  
  /* block all available enhancements (those that are by default enabled) - ADD ANY NEW STANDARD ENHANCEMENTS HERE!
  */
  public static function blockStandardEnhancements($unit){
	  if($unit instanceOf FighterFlight){ //enhancements for fighters
		$unit->enhancementOptionsDisabled[] = 'EXP_MOTIV'; 
		$unit->enhancementOptionsDisabled[] = 'IMPR_OB'; 
		$unit->enhancementOptionsDisabled[] = 'IMPR_THR'; 
		$unit->enhancementOptionsDisabled[] = 'POOR_TRAIN'; 
	  }else{ //enhancements for ships
		$unit->enhancementOptionsDisabled[] = 'ELITE_CREW';
		$unit->enhancementOptionsDisabled[] = 'HANG_F';
		$unit->enhancementOptionsDisabled[] = 'HANG_AS';
		$unit->enhancementOptionsDisabled[] = 'HANG_BP';
		$unit->enhancementOptionsDisabled[] = 'HANG_MSW';
		$unit->enhancementOptionsDisabled[] = 'HANG_ORD';
		$unit->enhancementOptionsDisabled[] = 'IMPR_ENG'; 
		$unit->enhancementOptionsDisabled[] = 'IMPR_REA'; 
		$unit->enhancementOptionsDisabled[] = 'IMPR_SENS'; 
		$unit->enhancementOptionsDisabled[] = 'MAR_CONT'; 			
		$unit->enhancementOptionsDisabled[] = 'POOR_CREW'; 
		$unit->enhancementOptionsDisabled[] = 'SLUGGISH'; 
		$unit->enhancementOptionsDisabled[] = 'VULN_CRIT'; 	
	  }
  }
  
  /*
  mark appropriate options if the set is other than generic (or enhances generic set)
  */
  public static function nonstandardEnhancementSet($unit, $setName){
	switch($setName) {

		case 'KirshiacFighter':
			Enhancements::blockStandardEnhancements($unit);
			break;	
	
		case 'KirshiacShip':
			Enhancements::blockStandardEnhancements($unit);
			$unit->enhancementOptionsEnabled[] = 'IMPR_SR';		
			break;

		case 'MindriderFighter':
			Enhancements::blockStandardEnhancements($unit);
			break;	
	
		case 'MindriderShip':
			Enhancements::blockStandardEnhancements($unit);
			$unit->enhancementOptionsEnabled[] = 'IMPR_SR';
			$unit->enhancementOptionsEnabled[] = 'IMPR_TS';			
			break;

		case 'Mines':
			Enhancements::blockStandardEnhancements($unit);
			$unit->enhancementOptionsEnabled[] = 'IFF_SYS';
			$unit->enhancementOptionsEnabled[] = 'MINE_SIGN';
			$unit->enhancementOptionsEnabled[] = 'MINE_CTRL';			
			
			if($unit->mineType && ($unit->mineType == 'Captor' || $unit->mineType == 'DEW')){
				$unit->enhancementOptionsEnabled[] = 'MINE_ACC';	
				$unit->enhancementOptionsEnabled[] = 'MINE_RANG';								
			}	
			
			if($unit->mineType && $unit->mineType == 'DEW'){
				$unit->enhancementOptionsEnabled[] = 'MINE_ARM';
				$unit->enhancementOptionsEnabled[] = 'MINE_MULTI';
			}
			
			if($unit->getVariableDamage() > 0){
				$unit->enhancementOptionsEnabled[] = 'MINE_DMG';				
			}

			break;	  				

		case 'OSAT':
			//Enhancements::blockStandardEnhancements($unit);
			$unit->enhancementOptionsDisabled[] = 'ELITE_CREW';
			$unit->enhancementOptionsDisabled[] = 'POOR_CREW'; 
			$unit->enhancementOptionsDisabled[] = 'MAR_CONT';
			//$unit->enhancementOptionsDisabled[] = 'SLUGGISH'; 
			$unit->enhancementOptionsDisabled[] = 'IMPR_ENG'; 
			$unit->enhancementOptionsDisabled[] = 'HANG_BP';
			$unit->enhancementOptionsDisabled[] = 'HANG_MSW';
			$unit->enhancementOptionsDisabled[] = 'HANG_ORD';									 		
			break;

		case 'ShadowShip':
			Enhancements::blockStandardEnhancements($unit);
			$unit->enhancementOptionsEnabled[] = 'IMPR_SR';
			$unit->enhancementOptionsEnabled[] = 'SHAD_FTRL';
			break;
	  
		case 'ShadowFighter':
			Enhancements::blockStandardEnhancements($unit);
			//$unit->enhancementOptionsEnabled[] = 'SHAD_CTRL';
			break;	

		case 'Shuttles':
			Enhancements::blockStandardEnhancements($unit);
				$unit->enhancementOptionsEnabled[] = 'MAKE_MINE';			
			break;		

		case 'Terrain':
			Enhancements::blockStandardEnhancements($unit);
			break;	
			
		case 'ThirdspaceShip':
			Enhancements::blockStandardEnhancements($unit);
			$unit->enhancementOptionsEnabled[] = 'IMPR_SR';	
			$unit->enhancementOptionsEnabled[] = 'IMPR_THSD';						
			foreach ( $unit->enhancementOptionsDisabled as $key=>$value){ 
				if($value=='IMPR_SENS'){ unset($unit->enhancementOptionsDisabled[$key]); }									
			}					
			break;
			
		case 'TorvalusFighter':
			Enhancements::blockStandardEnhancements($unit);
			break;	

		case 'TorvalusShip':
			Enhancements::blockStandardEnhancements($unit);
			$unit->enhancementOptionsEnabled[] = 'IMPR_SR';											
			break;				
			
		case 'VorlonShip':
			Enhancements::blockStandardEnhancements($unit);
			$unit->enhancementOptionsEnabled[] = 'IMPR_SR';
			$unit->enhancementOptionsEnabled[] = 'VOR_AMETHS';
			$unit->enhancementOptionsEnabled[] = 'VOR_AZURS';
			$unit->enhancementOptionsEnabled[] = 'VOR_CRIMS';
			break;
	  
		case 'VorlonFighter':
			Enhancements::blockStandardEnhancements($unit);
			$unit->enhancementOptionsEnabled[] = 'VOR_AZURF';
			break;	  

	}	  
  }//endof function nonstandardEnhancementSet
	
	
	/* all ship enhancement options - availability and cost calculation
	*/
  public static function setEnhancementOptionsShip($ship){

	  //Add option to delay the deployment of a ship, set as Enhancement so selection is remembered in the game itself.	
	  //DO NOT PLACE ANY OPTIONS (E.G. ENH[6] = TRUE) ALPHABETICALLY BEFORE THIS!
	  /*$enhID = 'DEPLOY';
	  if(!in_array($enhID, $ship->enhancementOptionsEnabled)){ //option is not disabled
		  $enhName = 'Choose a turn to deploy this unit:';
		  $enhLimit = 100;	
		  $enhPrice = 0; //no cost
		  $enhPriceStep = 0; //no ocst
		  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,true);
		  //technical ID, human readable name, number taken, maximum number to take, price for one, price increase for each further, is an option (rather than enhancement)
	  }	*/ 

	  //Elite Crew: +5 Initiative, +2 Engine, +1 Sensors, +2 Reactor power, -1 Profile, -2 to critical results
	  //cost: +40% of ship cost (second time: +60%)
	  //all Hangar-related advantages of original Elite Crew are skipped, and so is turn shortening
	  //what's more important, weapons-related advantages are gone
	  //Initiative bonus is added, critical bonus is increased
	  $enhID = 'ELITE_CREW';
	  if(!in_array($enhID, $ship->enhancementOptionsDisabled)){ //option is not disabled
		  $enhName = 'Elite Crew';
		  $enhLimit = 2;	
		  $enhPrice = ceil($ship->pointCost*0.4); //+40%	  
		  $enhPriceStep = ceil($ship->pointCost*0.2); //+20% of base, for total price of 60% for second level
		  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  //technical ID, human readable name, number taken, maximum number to take, price for one, price increase for each further, is an option (rather than enhancement)
	  }	 


	  /*Chameleon Sensor Suite: choose the vessel this ship projects the image of.
	    Free - the suite is already paid for in the hull's point cost - and an OPTION, not an enhancement.

	    This is the first CHOICE-valued entry, and it uses a new tuple slot: index 7 holds the list of
	    array(phpclass, humanLabel) that may be picked, index 0 of which is always "None". numbertaken
	    is then an INDEX into that list rather than a count, and the picked phpclass is mirrored into
	    enhname so the choice survives a saved-fleet reload (which rebuilds the list from scratch and
	    could otherwise renumber it). Index 7 is absent on every other enhancement, so nothing else
	    changes behaviour. "None" is index 0 = "not taken" = no DB row, which is exactly what the
	    existing machinery already produces for an untouched option.*/
	  $enhID = 'CHAM_DISG';
	  //self::$offerChoiceLists is false on the in-game blueprint path - building the pick list is far
	  //too expensive to do on a page load that will never render it (see the property's comment).
	  if(self::$offerChoiceLists && !in_array($enhID, $ship->enhancementOptionsDisabled)){ //option is not disabled
		  $hasChameleonSuite = false;
		  foreach ($ship->systems as $system){
			if ($system instanceof ChameleonSensors){
				$hasChameleonSuite = true;
				break;
			}
		  }
		  if($hasChameleonSuite){
			  $choices = ShipLoader::getDisguiseCandidates($ship->faction, $ship->phpclass);
			  if(count($choices) > 1){ //something other than "None" to pick
				  //Phrased so "<name>: <pick>" reads as a sentence wherever a bought option is listed
				  //with its value - it has to match the in-game enhancementTooltip line below.
				  $enhName = 'Chameleon Suite';
				  $enhLimit = count($choices) - 1; //highest valid index
				  $enhPrice = 0; //free
				  $enhPriceStep = 0;
				  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,true, $choices);
			  }
		  }
	  }

	  //Elite Marines for Grappling Claws, cost: 40% craft price (round up), limit: 1
	  $enhID = 'ELT_MRN';
	  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option needs to be specifically enabled
		  $enhName = 'Elite Marines';
		  $enhLimit = 1;	
		  $enhPrice = ceil($ship->pointCost * 0.4); //40% ship cost  
		  $enhPriceStep = 0;
		  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
	  }  
	  
	  //Extra Marines for Grappling Claws / Combat Transporters, cost: 10 per unit, limit: 3	  	
	  $enhID = 'EXT_MRN';	  
	  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option needs to be specifically enabled
		  $enhName = 'Extra Marine Units';
		  $enhLimit = 3;	
		  $enhPrice = 0; //fixed.		  
		  foreach ($ship->systems as $system){
			if ($system instanceof GrapplingClaw){
		  	$enhPrice += 10;
		    }
		  } 	  
		  foreach ($ship->systems as $system){
			if ($system instanceof CombatTransporter){
		  	$enhPrice += 10;
		    }
		  } 	  
		  $enhPriceStep = 0;
		  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,true);
	  } 

	  
	  $enhID = 'GUNSIGHT';
	  if(!in_array($enhID, $ship->enhancementOptionsDisabled)){ //option is not disabled
		  $enhName = 'Repeater Gunsights';
		  $count = 0;	 
		  foreach ($ship->systems as $system){
			if ($system instanceof ParticleRepeater){
				$count++;
			}
		  }  
		  if($count > 0){ //ship is actually equipped with a Particle Repeater(s)	  
			  $enhPrice = 12 * $count;	
			  $enhPriceStep = 0; 
			  $enhLimit = 1;	  
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,true);
		  }
	  }

	  //Improved Blaster Thrust: reduces the thrust needed to boost each
	  //Hypergraviton Blaster to 4 (from 6). Only offered on ships that actually
	  //mount a Blaster. Cost: 150 + 50 per Blaster on the ship.
	  $enhID = 'HBLAST_THR';
	  if(!in_array($enhID, $ship->enhancementOptionsDisabled)){ //option is not disabled
		  $enhName = 'Improved Gravitic Converters';
		  $count = 0;
		  foreach ($ship->systems as $system){
			if ($system instanceof HypergravitonBlaster){
				$count++;
			}
		  }
		  if($count > 0){ //ship is actually equipped with a Hypergraviton Blaster(s)
			  $enhPrice = 150 + (50 * $count);
			  $enhPriceStep = 0;
			  $enhLimit = 1;
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }
	  }


	  //To convert Assault Shuttles hangar slots to Fighter Slots
	  if (array_key_exists("assault shuttles", $ship->fighters)) { //Only add if ship has Assault Shuttle hangar space! 	  
	    $enhID = 'HANG_F';
		if(!in_array($enhID, $ship->enhancementOptionsDisabled)){ //Check option is also not disabled.
				$enhName = 'Assault Shuttle to Fighter';
				$enhLimit = $ship->fighters["assault shuttles"]; //The number of assault shuttle slots ship has is max conversion amount.
				$enhPrice = 5; //Flat 5 pts per slot converted	  
				$enhPriceStep = 0; //flat rate
				$ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,true);	
		}
	  }

	  //To convert Fighter hangar slots to Assault Shuttle Slots
	  $keysToCheck = ["normal", "heavy", "medium"]; //Light and Ultralight cannot be converted.
	  $matchingKeys = array_intersect_key($ship->fighters, array_flip($keysToCheck)); // Find matching keys
	  $totalCount = array_sum($matchingKeys); // Sum up the values of the found keys
	  if ($totalCount > 0) {
	    $enhID = 'HANG_AS';
		if(!in_array($enhID, $ship->enhancementOptionsDisabled)){ //Check option is also not disabled.
				$enhName = 'Fighter to Assault Shuttle';
				$enhLimit = $totalCount; //The number of assault shuttle slots ship has is max conversion amount.
				$enhPrice = 5; //Flat 5 pts per slot converted
				$enhPriceStep = 0; //flat rate
				$ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,true);
		}
	  }

	  //Shuttle-slot conversions — default shuttles auto-populate leftover
	  //hangar capacity (HangarOps::populateInitialHangarUsage step 3), so the
	  //available pool is derived from HangarOps::getDefaultShuttles rather
	  //than an explicit 'shuttles' key in $ship->fighters.
	  $defaultShuttles = HangarOps::getDefaultShuttles($ship);
	  if ($defaultShuttles['count'] > 0) {

	    //To convert default Shuttle (or Minesweeping Shuttle) slots to Breaching Pod slots.
	    //Works on both regular-shuttle and minesweeper-bonus carriers — adding to
	    //"Breaching Pods" steals from whichever default pool the leftover capacity is
	    //feeding (Shuttles or MinesweepingShuttles).
	    $enhID = 'HANG_BP';
		if(!in_array($enhID, $ship->enhancementOptionsDisabled)){ //Check option is also not disabled.
				$enhName = ($defaultShuttles['key'] === 'minesweeping shuttles')
					? 'Shuttle to Breaching Pod' //Can add a specific minesweeping shuttle reference, but brevity is better in Confirm window for now.
					: 'Shuttle to Breaching Pod';
				$enhLimit = $defaultShuttles['count']; //leftover hangar capacity = default shuttle pool
				$enhPrice = 10; //Flat 10 pts per slot converted
				$enhPriceStep = 0; //flat rate
				$ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,true);
		}

	    //To convert default Shuttle units to Minesweeping Shuttle units — only meaningful
	    //when the default pool is regular shuttles; minesweeper-bonus carriers already
	    //auto-populate Minesweeping Shuttles via HangarOps step 3.
	    if ($defaultShuttles['key'] === 'shuttles') {
	      $enhID = 'HANG_MSW';
		  if(!in_array($enhID, $ship->enhancementOptionsDisabled)){ //Check option is also not disabled.
				$enhName = 'Shuttle to Minesweeper';
				$enhLimit = $defaultShuttles['count'];
				//Per B5W: 10pts or 20% of MinesweepingShuttle cost, whichever is higher.
				//MinesweepingShuttle auto-populates with pointCost=0, so floor of 10 currently wins.
				$enhPrice = 10;
				$enhPriceStep = 0; //flat rate
				$ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,true);
		  }
	    }
	  }

	  //Stage 15 — Extra Ordnance: carrier-level reload-points pool for docked
	  //fighter missiles. 1 CP buys 1 reload point; a docked flight's
	  //AmmoMagazine::whileDocked spends points equal to a restocked missile's
	  //own enhancementPrice when re-arming. Read on demand from this option's
	  //count by HangarOps::reloadPoolCapacity — no $ship mutation needed.
	  //
	  //Gated to carriers in factions that actually field missile-equipped
	  //fighters (the only fleets where the pool can be drawn from). Restricted
	  //to ships with a Hangar so non-carrier missile-faction ships (escorts,
	  //bases without hangars) don't see it.
	  $missileFactionWhitelist = array(
		  'Earth Alliance', 'Earth Alliance (Early)',
		  'Centauri Republic', 'Centauri Republic (WotCR)',
		  'Narn Regime',
		  'Dilgar Imperium',
		  'Orieni Imperium', 'Orieni Imperium (defenses)',
		  'Gaim Intelligence',
		  'Hurr Republic',
		  'Cascor Commonwealth',
		  'Kor-Lyan Kingdoms',
		  'Belt Alliance',
		  'Rogolon Dynasty',
		  'Raiders', 'Custom Ships',
	  );
	  $fighterSlotKeys = ['normal', 'heavy', 'medium', 'light', 'ultralight'];
	  $hasFighters = array_sum(array_intersect_key($ship->fighters ?? [], array_flip($fighterSlotKeys))) > 0;
	  if ($hasFighters && in_array($ship->faction, $missileFactionWhitelist, true)) {
		  $enhID = 'HANG_ORD';
		  if(!in_array($enhID, $ship->enhancementOptionsDisabled)){ //Check option is also not disabled.
				$enhName = 'Ordnance Reserve';
				$enhLimit = 2000;            //practical ceiling — six AMMO_FH heavies (8 PV ea) on a 6-fighter heavy missile flight
				$enhPrice = 1;             //1 CP = 1 reload point
				$enhPriceStep = 0;         //flat rate
				$ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,true);
		  }
	  }

	  //Stage 17 extension — Extra Marine Contingents: ship-level pool used to
	  //restock Marines weapons on Breaching Pods docked in this ship's hangars.
	  //Each enhancement point buys 1 marine unit at 10 CP; pool capacity is
	  //read on demand by HangarOps::marinePoolCapacity from this entry's count.
	  //Spent total persists on the primary hangar via the hangarMarineReserve
	  //note (parallel to Stage 15's hangarOrdReserve). Available to all ships
	  //per design — non-carrier ships can buy it but never trigger drawdown
	  //(serviceDockedFlights iterates hangar contents, so no hangar = no draw).
	  //Limit: 1% of ship's base point cost, rounded up.
	  $enhID = 'MAR_CONT';
	  if(!in_array($enhID, $ship->enhancementOptionsDisabled)){
		  $enhName = 'Extra Marines';
		  $enhLimit = max(1, (int)ceil($ship->pointCost / 100));   //1% of PV, rounded up; minimum 1
		  $enhPrice = 10;            //10 CP per marine
		  $enhPriceStep = 0;         //flat rate
		  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,true);
	  }

	  $enhID = 'IFF_SYS';
	  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option needs to be specifically enabled
		  $enhName = 'Identify Friend or Foe (IFF)';
		  $enhLimit = 1; //Only ever need 1
		  $enhPrice = 0; //fixed.		  
		  foreach ($ship->systems as $system){
			if ($system instanceof BallisticMineLauncher || $system instanceof AbbaiMineLauncher){
		  	$enhPrice += 4;
		    }
			if($system instanceof CaptorMine || $system instanceof ProximityMine || $system instanceof MineControllerDEW){
				$enhPrice += ceil($ship->pointCost * 0.1); //10% cost.
			}
		  }  
		  $enhPriceStep = 0; //flat rate
		  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		}
		    
	  //Improved Engine: +1 Thrust, cost: 12+4/turn cost, round up, limit: up to +50%
	  $enhID = 'IMPR_ENG';
	  if(!in_array($enhID, $ship->enhancementOptionsDisabled)){ //option is not disabled
		  $enhName = 'Improved Engine';
		  //find strongest engine.. which don't need to be called Engine!
		  $strongestValue = -1;	  
		  foreach ($ship->systems as $system){
			if ($system instanceof Engine){
				if($system->output > $strongestValue) {
					$strongestValue = $system->output;
				}
			}
		  }  
		  if($strongestValue > 0){ //Engine actually exists to be enhanced!
			  $enhPrice = ceil(12+(4/($ship->turncost)));	  
			  $enhPriceStep = 0; 
			  $enhLimit = ceil($strongestValue/2);	  
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }
	  }

	  //Improved PSychic Field for Thirdspace	
	  $enhID = 'IMPR_PSY';
	  if(!in_array($enhID, $ship->enhancementOptionsDisabled)){ //option is not disabled
		  $enhName = 'Improved Psychic Field';
		  //count Psychic Fields
		  $count = 0;	 
		  foreach ($ship->systems as $system){
			if ($system instanceof PsychicField){
				$count++;
			}
		  }  
		  if($count > 0){ //ship is actually equipped with a Psychic Field(s)	  
			  $enhPrice = 300;	
			  $enhPriceStep = 0; 
			  $enhLimit = 1;	  
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }
	  }		

		//Improved Reactor: +1/2/3/4 Power (depending on unit size), cost: 10 *Power added (double if ship has power deficit to begin with), limit: o1
	  $enhID = 'IMPR_REA';
	  if(!in_array($enhID, $ship->enhancementOptionsDisabled)){ //option is not disabled
		  $enhName = 'Improved Reactor';
		  //find strongest reactor.. which don't need to be called Reactor! also, determine strength by size rather than output (main reactor may well have negative output while secondary has 0)	
		  $strongestValue = -1000;	 
			$actualOutput = 0;
		  foreach ($ship->systems as $system){
			if ($system instanceof Reactor){
				if($system->maxhealth > $strongestValue) {
					$strongestValue = $system->maxhealth;
					$actualOutput = $system->output;
				}
			}
		  }  
		  if($strongestValue > 0){ //Reactor actually exists to be enhanced! ...although it'd better!
				$addedPower = 0;
			  if($ship->Enormous == true){
				  $addedPower = 4;
			  }else{
				  $addedPower = $ship->shipSizeClass; //+1 for MCV, +2 for HCV, +3 for Capital
			  }		  
			  $enhPrice = $addedPower*10;	  
			  if($actualOutput<0){ //if actual power output starts negative - double the price
				  $enhPrice = $enhPrice *2;
			  }
			  $enhPriceStep = 0; 
			  $enhLimit = 1;	  
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }		  
	  }	  
	  
	  //Improved Sensors : +1 Sensors, cost: new rating *5 (double for ElInt, double for Advanced Sensors), limit: 1
	  $enhID = 'IMPR_SENS';
	  if(!in_array($enhID, $ship->enhancementOptionsDisabled)){ //option is not disabled
		  $enhName = 'Improved Sensors';
		  $enhLimit = 1;	  
		  //find strongest sensors... which don't need to be called Sensors!
		  $strongestValue = -1;	  
		  $multiplier = 1;
		  $elint = false;
		  $advanced = false;
		  foreach ($ship->systems as $system){
			if ($system instanceof Scanner){
				if($system->output > $strongestValue) {
					$strongestValue = $system->output;
					if ($system instanceof ElintScanner) $elint = true;
					if ($system->boostEfficiency == 14) $advanced = true; //advanced sensors have fixed boost cost of 14
				}
			}
		  } 
		  if($elint) $multiplier++;
		  if($advanced) $multiplier++;
		  if($strongestValue > 0){ //Sensors actually exist to be enhanced!
			  $enhPrice = max(1,($strongestValue+1)*5) * $multiplier;	  
			  $enhPriceStep = 0;
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }
	  }	    
	  
	  //Improved Self Repair - +1 Self-Repair rating
	  $enhID = 'IMPR_SR';	  
	  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option needs to be specifically enabled
		  $enhName = 'Improved Self Repair';
		  //find number and output of Self-Repair modules
		  $count = 0;	
		  $outputTotal = 0;
		  $outputMin = 10000;
		  foreach ($ship->systems as $system){
			if ($system instanceof SelfRepair){
				$count++;
				$outputTotal += $system->output;//count CURRENT output!
				$outputMin = Min($outputMin, $system->output); //weakest system - to determine maxiumum upgrade
			}
		  }  
		  if(($count > 0) && ($outputMin>=2)){ //ship is actually equipped with Self Repair system(s) strong enough to be upgraded
			  $enhPrice = ($outputTotal+$count)*100; //every self repair system increased by one
			  $enhPriceStep = $count*100; //additional 100 points for every self-repair on ship
			  $enhLimit = floor($outputMin/2);	  
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }
	  }  	

	  //Improved Thirdspace Shield		    
	  $enhID = 'IMPR_THSD';
	  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option needs to be specifically enabled
		  $enhName = 'Improved Shields';
		  $enhLimit = 5; //Maximum 5 upgrades.
		  $shields = 0;
		  $rating = 0;		  
		  foreach ($ship->systems as $system){
			if ($system instanceof ThirdspaceShield){
		  	$shields++;
		  	$rating = $system->baseRating;
		    }
		  } 
		  $enhPrice = (50 * $shields);//New rating multiplied by number of shields.
		  $enhPriceStep = 0;
		  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }

	  //Improved Thought Shield for Mindriders		    
	  $enhID = 'IMPR_TS';
	  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option needs to be specifically enabled
		  $enhName = 'Improved Shields';
		  $enhLimit = 5; //Maximum 5 upgrades.
		  $shields = 0;
		  $rating = 0;		  
		  foreach ($ship->systems as $system){
			if ($system instanceof ThoughtShield){
		  	$shields++;
		  	$rating = $system->baseRating;
		    }
		  } 
		  $enhPrice = (($rating+1) * $shields) * $shields;//New rating multiplied by number of shields, for EACH shield 
		  $enhPriceStep = $shields * $shields;
		  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }
		  	  
	  //Ipsha-specific - Eethan Barony refit (available for generic Ipsha designs only, Eethan-specific may have it already incorporated in some form)
	  $enhID = 'IPSH_EETH';	  
	  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option needs to be specifically enabled
		  $enhName = 'Eethan Barony refit';
		  $enhLimit = 1;	
		  $enhPrice = ceil($ship->pointCost*0.1); //+10%	
		  $enhPriceStep = 0;
		  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,true);//this is NOT an enhancement - rather an OPTION
	  }  	
	  
	  //Ipsha-specific - Essan Barony refit (available for generic Ipsha designs only, Essan-specific ones may have it already incorporated in some form)
	  $enhID = 'IPSH_ESSAN';	  
	  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option needs to be specifically enabled
		  $enhName = 'Essan Barony refit';
		  $enhLimit = 1;	
		  $enhPrice = 0;
		  $enhPriceStep = 0;
		  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,true);//this is NOT an enhancement - rather an OPTION
	  }  	

	  //Markab-specific - Enables 'Religious Fervor' refit to selected vessel which comes with some bonus and some penalties.
	  $enhID = 'MARK_FERV';	  
	  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option needs to be specifically enabled
		  $enhName = 'Religious Fervor';
		  $enhLimit = 1;	
		  $enhPrice = 0;
		  $enhPriceStep = 0;
		  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,true);//this is NOT an enhancement - rather an OPTION
	  }  
	  
		//Improve Accuracy rating of Mines		
		$enhID = 'MINE_ACC';
		if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option needs to be specifically enabled
			$enhName = 'Improved Accuracy';
			$enhLimit = 5; 
			$enhPrice = 0;
			if($ship->mineType && $ship->mineType == 'Captor'){
				$enhPrice = ceil($ship->pointCost * 0.1);		
			}else{
				$enhPrice = ceil($ship->pointCost * 0.2);	//DEW mines	
			}		
			$enhPriceStep = 0; //flat rate
			$ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		}			

		//Improve Armour of Mines		
		$enhID = 'MINE_ARM';
		if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option needs to be specifically enabled
			$enhName = 'Improved Armour';
			$enhLimit = 5; 
			
			$mineArmour = 0;
			foreach ($ship->systems as $system) {
				if ($system instanceof Structure) {
					$mineArmour = $system->armour;
				}
			}
			
			$enhPrice = max(4, $mineArmour+1); //New armour +1.	Minimum 4pts.	  
			$enhPriceStep = 1; 
			$ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		}			

		//Improve Signature rating of Mines		
		$enhID = 'MINE_CTRL';
		if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option needs to be specifically enabled
			$enhName = 'Command Controller';
			$enhLimit = 1; 
		  	$enhPrice = ceil($ship->pointCost*0.33); //33%	  
			$enhPriceStep = 0; 
			$ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		}	

		//Improve set Damage amount for Mines		
		$enhID = 'MINE_DMG';
		if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option needs to be specifically enabled
			$enhName = 'Extra Damage';
			$variableDamage = $ship->getVariableDamage();
			$enhLimit = $variableDamage; 
			$enhPrice = 0.5; //Adds 1 point of damage per PV	  
			$enhPriceStep = 0; 
			$ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		}	

		//Improve Range of Mines		
		$enhID = 'MINE_RANG';
		if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option needs to be specifically enabled
			$enhName = 'Improved Range';
			$enhLimit = 5; 
			
			$mineRange = 0;
			foreach ($ship->systems as $system) {
				if ($system instanceof CaptorMine) {
					$mineRange = max($mineRange, $system->range);
				} elseif ($system instanceof MineControllerDEW) {
					$mineRange = max($mineRange, $system->rangeSetting);
				}
			}
			
			$enhPrice = max(4, $mineRange); //New sign (+1) +1.	Minimum 4pts.	  
			$enhPriceStep = 1; 
			$ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		}	  	  

		//Improve Signature rating of Mines
		$enhID = 'MINE_SIGN';
		if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option needs to be specifically enabled
			$enhName = 'Improved Signature';
			$enhLimit = 5;
			$enhPrice = max(4, $ship->signature + 2); //New sign (+1) +1.	Minimum 4pts.
			$enhPriceStep = 1;
			$ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		}

		//Multiple Targets - independent ranges per weapon for DEW mines with 2+ weapons
		$enhID = 'MINE_MULTI';
		if(in_array($enhID, $ship->enhancementOptionsEnabled)){
			$weaponCount = 0;
			foreach ($ship->systems as $system) {
				if ($system instanceof Weapon && $system->name !== 'RammingAttack') $weaponCount++;
			}
			if ($weaponCount >= 2) {
				$enhName = 'Flexible Targeting';
				$enhLimit = 1;
				$enhPrice = ceil($ship->pointCost * 0.25);
				$enhPriceStep = 0;
				$ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
			}
		}

	  //Poor Crew (official but modified): -5 Initiative, -1 Engine, -1 Sensors, -1 Reactor power, +1 Profile, +2 to critical results, -1 to hit all weapons
	  //cost: -15% of ship cost (second time: -10%)
	  $enhID = 'POOR_CREW';
	  if(!in_array($enhID, $ship->enhancementOptionsDisabled)){ //option is not disabled
		  $enhName = 'Poor Crew';
		  $enhLimit = 2;	
		  $enhPrice = -ceil($ship->pointCost*0.15); //-15%	  
		  $enhPriceStep = -ceil($enhPrice/3); //+5%, for total price of -10% for second level
		  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
	  }
	  
	  //Increased Diffuser Capability: +1 output for every Energy Diffuser; cost: 2.5x new capability, step: 2.5x number of diffusers
	  $enhID = 'SHAD_DIFF';
	  if(!in_array($enhID, $ship->enhancementOptionsDisabled)){ //option is not disabled
		  $enhName = 'Increased Diffusers';
		  //find number and output of Diffusers
		  $count = 0;	
		  $output = 0;
		  foreach ($ship->systems as $system){
			if ($system instanceof EnergyDiffuser){
				$count++;
				$output += $system->output+1;//count NEW output!
			}
		  }  
		  if($count > 0){ //ship is actually equipped with Energy Diffuser(s)	  
			  $enhPrice = round($output*2.5);
			  $enhPriceStep = round($count*2.5);
			  $enhLimit = 5;	  
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }		  
	  }	  

	  //Shadow fighter launched, limit: hangar capacity (= the hangar icon's fighter max)
	  $enhID = 'SHAD_FTRL';
	  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option is enabled
		  //find total hangar capacity
			if (HangarOps::shipHasShadowHangar($ship)) {		  
				$capacity = 0;
				foreach ($ship->fighters as $name => $count){
					$capacity += $count;
				}
				if($capacity > 0){ //this ship can actually carry fighters!!
					$enhLimit = ceil($capacity);
					//Stage S: a ship with an integrated-fighter bay (ShadowHangar) BUYS its
					//fighters here — "integrated fighters are not free, paid at a fighter's
					//listed cost". Price = one ShadowMediumFighterFlight's per-craft cost
					//(900/6 = 150 CP). The bought count drives the integrated-fighter initial
					//population (HangarOps::populateInitialHangarUsage) and the per-fighter
					//structure-box marks (later S-stages) — NOT a static maxhealth reduction.
					//Legacy Shadow ships (plain Hangar) keep the original FREE option whose
					//count statically shaves PRIMARY Structure (applied in setEnhancementsShip).
					//if (HangarOps::shipHasShadowHangar($ship)) {
						$enhName = 'Integrated Fighter';
						$enhPrice = 150;
						$enhPriceStep = 0;//Each fighter is 150pts, no increase per step.
						$ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,true);//costed option
					/*} else {
						$enhName = 'Spawn a Medium Fighter';
						$enhPrice = 0;
						$enhPriceStep = 0;
						$ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,true);//not an enhancement!
					}*/
				}
			}	
	  }
	  
	  /*Extra Tendrils: a matched PAIR of new Diffuser Tendrils, port and starboard, grown on the
	    ship's STRONGEST Energy Diffuser pair. One pair per ship - hence a single enhancement whose
	    COUNT is the capacity in fives (1 = 5, 2 = 10, 3 = 15) rather than a number of tendrils.

	    Shadow Association only, by explicit design decision: other factions do field Shadowtech
	    diffusers (the Drakh omegaEpsilon, gaimRaxas, the Vree ZShadowXonn) but none of them get
	    this refit, so the gate is the faction and not merely the presence of a diffuser.

	    Capacity is capped at the SMALLEST tendril already on the hull - a bolt-on may not out-size
	    what the hull grew for itself - so a ship whose smallest tendril is 10 is offered 5 and 10
	    only, and one whose smallest is 30 gets the full 5/10/15.

	    Cost is (capacity * 2) * the diffuser's rating, for the pair. That is strictly proportional
	    to capacity, so ONE flat price per level with no step reproduces it exactly at every level:
	    confirm.js totals a level as enhPrice + i*enhPriceStep, so enhPriceStep = 0 gives
	    N * (10 * rating) = (5N * 2) * rating. Nothing here needs the choice-list widget.*/
	  $enhID = 'SHAD_TEND';
	  if($ship->faction === 'Shadow Association' && !in_array($enhID, $ship->enhancementOptionsDisabled)){ //option is not disabled
		  //Rating of the strongest diffuser that actually HAS tendrils to grow alongside.
		  $bestRating = 0;
		  foreach ($ship->systems as $system){
			if (($system instanceof EnergyDiffuser) && (count($system->tendrils) > 0)){
				$bestRating = max($bestRating, $system->output);
			}
		  }
		  //Smallest tendril anywhere on the hull - the cap on what may be bought.
		  $smallestTendril = 0;
		  foreach ($ship->systems as $system){
			if ($system instanceof DiffuserTendril){
				$smallestTendril = ($smallestTendril == 0) ? $system->maxhealth : min($smallestTendril, $system->maxhealth);
			}
		  }
		  $enhLimit = min(3, (int)floor($smallestTendril/5)); //5/10/15, minus whatever the cap removes
		  if(($bestRating > 0) && ($enhLimit > 0)){
			  //The buy dialog appends the quantity and price itself ("up to 10 capacity, 150pts
			  //per 5" - confirm.expandEnhancementName), so the name only has to say what it is.
			  //No '&' in it: the buy dialog renders the name as HTML, but the ship window's
			  //Enhancements panel renders the tooltip as React TEXT (splitHtmlLines strips tags),
			  //so an entity would show up literally there.
			  $enhName = 'Extra Tendrils (matched pair)';
			  $enhPrice = 10 * $bestRating; //(5 capacity * 2) * rating - one level, for BOTH tendrils
			  $enhPriceStep = 0; //flat: price is strictly proportional to capacity bought
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }
	  }

	//Spark Curtain: CUSTOM/CAMPAIGN ballistic defense (2+boost) for Spark Field, cost: 40 + 10/Spark Field present, limit: 1
	  $enhID = 'SPARK_CURT';
	  if(!in_array($enhID, $ship->enhancementOptionsDisabled)){ //option is not disabled
		  $enhName = 'Spark Curtain';
		  //count Spark Fields
		  $count = 0;	 
		  foreach ($ship->systems as $system){
			if ($system instanceof SparkField){
				$count++;
			}
		  }  
		  if($count > 0){ //ship is actually equipped with Spark Field(s)	  
			  $enhPrice = 40+$count*10;	
			  $enhPriceStep = 0; 
			  $enhLimit = 1;	  
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }
	  }

		//Sluggish: officially -2d6 Initiative, let's make it fixed (for technical reasons) and scalable (so it's usable for many scenarios)
		//let's make it very cheap too
		//effect: -1 Ini, Price: -6, step 0, max count 7 (for a total of 42 points at max value)
	  $enhID = 'SLUGGISH';
	  if(!in_array($enhID, $ship->enhancementOptionsDisabled)){ //option is not disabled
		  $enhName = 'Sluggish';
		  $enhLimit = 7;	
		  $enhPrice = -6; //fixed, very low value
		  $enhPriceStep = 0; //flat rate
		  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
	  }
	  

	//Vorlon Amethyst Skin (for ship). +1 Adaptive Armor point, AA maximum and available for pre-assignment increased as well for every even total.
	//cost: ramming value (let's simplify to total structure) x number of point / 5; max. +50%
	  $enhID = 'VOR_AMETHS';
	  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option is not disabled
		  $enhName = 'Amethyst Skin';
		  //find AA controller and count Structure boxes while at it		  
		  $AActrl = $ship->getSystemByName("AdaptiveArmorController");
		  if($AActrl){
			  $structureTotal = 0;			  
			  foreach ($ship->systems as $system){
				if ($system instanceof Structure){
					$structureTotal += $system->maxhealth;
				}
			  }  
			  $enhPrice = round($structureTotal*($AActrl->AAtotal+1)/5);	
			  $enhPriceStep = round($structureTotal/5);
			  $enhLimit = floor($AActrl->AAtotal/2);
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }
	  }	  

	/* Vorlon Azure Skin Coloring:
	Effect: +1 Shield rating, for all shields.
	Cost: unit size factor x new shield rating x nuber of emitters
	Limit: 50% of base shield rating
	Init size factor is 30 for Enormous units, 25 for Capitals, 20 for anything smaller (including fighters).
	*/
	  $enhID = 'VOR_AZURS';
	  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option is not disabled
		  $enhName = 'Azure Skin';
		  $count = 0;	
		  $rating = 0;
		  foreach ($ship->systems as $system){
			if ($system instanceof EMShield){
				$count++;
				$rating = $system->output;
			}
		  }		  
		  if($count>0 && $rating>1){
			  $sizeFactor = 20;//all units except Caps and Enormous)
			  if($ship->shipSizeClass ==3){
				  $sizeFactor = 25;
				  if($ship->Enormous) $sizeFactor = 30;
			  }
			  $enhPrice = $sizeFactor*$count*($rating+1);	
			  $enhPriceStep = $sizeFactor*$count;
			  $enhLimit = floor($rating/2);
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }
	  }	  
	  
	/* Vorlon Crimson Skin Coloring (ship only):
	Effect: Power Capacitor gains +2 storage points and +1 recharge point.
	Cost: 20x new recharge rate
	Limit: 6*/
	  $enhID = 'VOR_CRIMS';
	  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option is enabled
		  $enhName = 'Crimson Skin';
		  $capacitor = $ship->getSystemByName("PowerCapacitor");//find Power Capacitor
		  if($capacitor){
			  $enhPrice = ($capacitor->output+1) *20;	
			  $enhPriceStep = 20;
			  $enhLimit = 6;
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }
	  }
	
	  
		//Vulnerable to Criticals: +1 to critical rolls, and let's make it scalable :)
		//let's make it very cheap too
		//effect: +1 Critical roll modifier, Price: -4, step 0, max count 4 (for a total of 16 points at max value)
	  $enhID = 'VULN_CRIT';
	  if(!in_array($enhID, $ship->enhancementOptionsDisabled)){ //option is not disabled
		  $enhName = 'Vulnerable to Criticals';
		  $enhLimit = 4;	
		  $enhPrice = -4; //fixed, very low value
		  $enhPriceStep = 0; //flat rate
		  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
	  }
	  
	  
	  //REEVALUATION - for official units that got CUSTOM reevaluation
 	  $enhID = 'RE-EVAL';
	  if(!in_array($enhID, $ship->enhancementOptionsDisabled)){ //option is not disabled
		  $enhName = 'CUSTOM price reevaluation';
		  $enhLimit = 1;
		  $enhPriceStep = 0; //flat rate
		  $enhPrice = 0;
		  
		  switch($ship->phpclass){
			  //Cascor reevaluation to catch to Tier 2 (price cuts, generally)
			  case 'Qoccata': //Cascor Qoccata Supercarrier: 950->875
				  $enhPrice = -75;
				  break;
			  case 'Coqari': //Cascor Coqari Scout: 750->650
				  $enhPrice = -100;
				  break;
			  case 'Norsca': //Cascor Norsca Battlecruiser: 700->625
				  $enhPrice = -75;
				  break;
			  case 'Norscator': //Cascor Norscator Gunship: 825->650
				  $enhPrice = -175;
				  break;
			  case 'Nesacc': //Cascor Nesacc Explorer: 700->650
				  $enhPrice = -50;
				  break;
			  case 'Qoricc': //Cascor Qoricc Destroyer: 500->425
				  $enhPrice = -75;
				  break;
			  case 'Drocca': //Cascor Drocca Torpedo Destroyer: 600->540
				  $enhPrice = -60;
				  break;
			  case 'Crocti': //Cascor Crocti Patrol Carrier: 420->400
				  $enhPrice = -20;
				  break;
			  case 'Tacacci': //Cascor Tacacci Strike Frigate: 440->420
				  $enhPrice = -20;
				  break;
			  case 'Talacca': //Cascor Talacca Frigate Leader: 500->480
				  $enhPrice = -20;
				  break;	

			  //Yolu reevaluation to catch to Tier 1 (price increases, generally)
			  case 'Yuan': //Yolu Yuan Dreadnought: 2100->2400
				  $enhPrice = 300;
				  break;
			  case 'Ulana': //Yolu Ulana Patrol Cruiser: 1200->1360
				  $enhPrice = 160;
				  break;
			  case 'Udran': //Yolu Udran Command Cruiser: 1375->1650
				  $enhPrice = 275;
				  break;
			  case 'Aluin': //Yolu Aluin Gunship: 1100->1400
				  $enhPrice = 300;
				  break;
			  case 'Notali': //Yolu Notali Carrier: 950->1150
				  $enhPrice = 200;
				  break;
			  case 'Notai': //Yolu Notai Assault Carrier: 950->950
				  $enhPrice = 0; //just so enhancement itself is present
				  break;
			  case 'Nashana': //Yolu Nashana Light Cruiser: 950->1150
				  $enhPrice = 200;
				  break;
			  case 'Maltra': //Yolu Maltra Scout: 900->1000
				  $enhPrice = 100;
				  break;
			  case 'Hastan': //Yolu Hastan Escort Frigate: 800->800
				  $enhPrice = 0;//just so enhancement itself is present
				  break;
			  case 'Maitau': //Yolu Maitau Pursuit Frigate: 600->750
				  $enhPrice = 150;
				  break;
/*			  case 'Maishan': //Yolu Maishan Strike Frigate: 710->750 - None for now.
				  $enhPrice = 40;
				  break;
*/
			  case 'Malau': //Yolu Malau Attack Frigate: 625->650
				  $enhPrice = 25;
				  break;
			  
			  default:
				  $enhPrice = 0;
				  break;
		  }
		  
		  if ($enhPrice != 0){
		  	$ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,true); //this is NOT an enhancement - rather an OPTION (but for custom faction only)
		  }
	  }
	  
	  
	  //consumable ammunition
	  //find magazine capacity to set appropriate limits!
	  //ASSUMING either single magazine (for ships), or mltiple but equal ones (for fighter flights) (no magazine is fine too)
	  //availablility in name - for EA and other factions respectably - just in case someone wishes for in-universe accurate availability
	  $magazineCapacity = 0;
	  foreach($ship->systems as $magazine) if($magazine->name == 'ammoMagazine'){
		  $magazineCapacity = $magazine->capacity;
		  break; //foreach
	  }
	  if ($magazineCapacity > 0){ //otherwise no point
		  $enhID = 'AMMO_B'; //Basic Missiles - actually most ships have these fitted as standard
		  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option is enabled
				$ammoClass = new AmmoMissileB();
				$ammoSize = $ammoClass->size;
				$actualCapacity = floor($magazineCapacity/$ammoSize);
			  $enhName = $ammoClass->enhancementDescription;
			  $enhLimit = $actualCapacity; //effectively limited by magazine capacity	
			  $enhPrice = $ammoClass->getPrice($ship); 
			  $enhPriceStep = 0; //flat rate
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
			  //special missiles are NOT enhancements
		  }
		  $enhID = 'AMMO_L'; //Long Range Missiles
		  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option is enabled
				$ammoClass = new AmmoMissileL();
				$ammoSize = $ammoClass->size;
				$actualCapacity = floor($magazineCapacity/$ammoSize);
			  $enhName = $ammoClass->enhancementDescription;
			  $enhLimit = $actualCapacity;		
			  $enhPrice = $ammoClass->getPrice($ship); 
			  $enhPriceStep = 0; //flat rate
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }
		  $enhID = 'AMMO_H'; //Heavy Missiles
		  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option is enabled
				$ammoClass = new AmmoMissileH();
				$ammoSize = $ammoClass->size;
				$actualCapacity = floor($magazineCapacity/$ammoSize);
			  $enhName = $ammoClass->enhancementDescription;
			  $enhLimit = $actualCapacity;		
			  $enhPrice = $ammoClass->getPrice($ship); 
			  $enhPriceStep = 0; //flat rate
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }
		  $enhID = 'AMMO_F'; //Flash Missiles
		  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option is enabled
				$ammoClass = new AmmoMissileF();
				$ammoSize = $ammoClass->size;
				$actualCapacity = floor($magazineCapacity/$ammoSize);
			  $enhName = $ammoClass->enhancementDescription;
			  $enhLimit = $actualCapacity;		
			  $enhPrice = $ammoClass->getPrice($ship); 
			  $enhPriceStep = 0; //flat rate
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }
		  $enhID = 'AMMO_A'; //Antifighter Missiles
		  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option is enabled
				$ammoClass = new AmmoMissileA();
				$ammoSize = $ammoClass->size;
				$actualCapacity = floor($magazineCapacity/$ammoSize);
			  $enhName = $ammoClass->enhancementDescription;
			  $enhLimit = $actualCapacity;		
			  $enhPrice = $ammoClass->getPrice($ship); 
			  $enhPriceStep = 0; //flat rate
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }
		  $enhID = 'AMMO_P'; //Piercing Missiles
		  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option is enabled
				$ammoClass = new AmmoMissileP();
				$ammoSize = $ammoClass->size;
				$actualCapacity = floor($magazineCapacity/$ammoSize);
			  $enhName = $ammoClass->enhancementDescription;
			  $enhLimit = $actualCapacity;		
			  $enhPrice = $ammoClass->getPrice($ship); 
			  $enhPriceStep = 0; //flat rate
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }
		  $enhID = 'AMMO_D'; //Light Missiles
		  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option is enabled
				$ammoClass = new AmmoMissileD();
				$ammoSize = $ammoClass->size;
				$actualCapacity = floor($magazineCapacity/$ammoSize);
			  $enhName = $ammoClass->enhancementDescription;
			  $enhLimit = $actualCapacity;		
			  $enhPrice = $ammoClass->getPrice($ship); 
			  $enhPriceStep = 1; //flat rate
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }
		  $enhID = 'AMMO_I'; //Interceptor Missiles
		  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option is enabled
				$ammoClass = new AmmoMissileI();
				$ammoSize = $ammoClass->size;
				$actualCapacity = floor($magazineCapacity/$ammoSize);
			  $enhName = $ammoClass->enhancementDescription;
			  $enhLimit = $actualCapacity;		
			  $enhPrice = $ammoClass->getPrice($ship); 
			  $enhPriceStep = 0; //flat rate
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }
		  $enhID = 'AMMO_C'; //Chaff Missiles - -15 to hit
		  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option is enabled
				$ammoClass = new AmmoMissileC();
				$ammoSize = $ammoClass->size;
				$actualCapacity = floor($magazineCapacity/$ammoSize);
			  $enhName = $ammoClass->enhancementDescription;
			  $enhLimit = $actualCapacity;		
			  $enhPrice = $ammoClass->getPrice($ship); 
			  $enhPriceStep = 0; //flat rate
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }				  
		  $enhID = 'AMMO_J'; //Jammer Missiles - add BDEW
		  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option is enabled
				$ammoClass = new AmmoMissileJ();
				$ammoSize = $ammoClass->size;
				$actualCapacity = floor($magazineCapacity/$ammoSize);
			  $enhName = $ammoClass->enhancementDescription;
			  $enhLimit = $actualCapacity;
			  $enhPrice = $ammoClass->getPrice($ship); 
			  $enhPriceStep = 0; //flat rate
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }
		  $enhID = 'AMMO_K'; //Starburst Missiles - Fire in Pulse mode
		  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option is enabled
				$ammoClass = new AmmoMissileK();
				$ammoSize = $ammoClass->size;
				$actualCapacity = floor($magazineCapacity/$ammoSize);
			  $enhName = $ammoClass->enhancementDescription;
			  $enhLimit = $actualCapacity;		
			  $enhPrice = $ammoClass->getPrice($ship); 
			  $enhPriceStep = 0; //flat rate
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }	
		  $enhID = 'AMMO_M'; //Multiwarhead Missiles
		  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option is enabled
				$ammoClass = new AmmoMissileM();
				$ammoSize = $ammoClass->size;
				$actualCapacity = floor($magazineCapacity/$ammoSize);
			  $enhName = $ammoClass->enhancementDescription;
			  $enhLimit = $actualCapacity;		
			  $enhPrice = $ammoClass->getPrice($ship); 
			  $enhPriceStep = 0; //flat rate
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }
		  $enhID = 'AMMO_KK'; //Kinetic Missiles
		  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option is enabled
				$ammoClass = new AmmoMissileKK();
				$ammoSize = $ammoClass->size;
				$actualCapacity = floor($magazineCapacity/$ammoSize);
			  $enhName = $ammoClass->enhancementDescription;
			  $enhLimit = $actualCapacity;		
			  $enhPrice = $ammoClass->getPrice($ship); 
			  $enhPriceStep = 0; //flat rate
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }
		  $enhID = 'AMMO_S'; //Stealth Missiles - Target is hidden
		  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option is enabled
				$ammoClass = new AmmoMissileS();
				$ammoSize = $ammoClass->size;
				$actualCapacity = floor($magazineCapacity/$ammoSize);
			  $enhName = $ammoClass->enhancementDescription;
			  $enhLimit = $actualCapacity / 10;		//10% limit
			  $enhPrice = $ammoClass->getPrice($ship); 
			  $enhPriceStep = 0; //flat rate
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }
		  $enhID = 'AMMO_X'; //HARM Missiles
		  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option is enabled
			$ammoClass = new AmmoMissileX();
			$ammoSize = $ammoClass->size;
			$actualCapacity = floor($magazineCapacity/$ammoSize);
			$enhName = $ammoClass->enhancementDescription;
			$enhLimit = $actualCapacity;		
			$enhPrice = $ammoClass->getPrice($ship); 
			$enhPriceStep = 0; //flat rate
			$ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }
		  $enhID = 'AMMO_Z'; //Antimine Missiles
		  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option is enabled
				$ammoClass = new AmmoMissileZ();
				$ammoSize = $ammoClass->size;
				$actualCapacity = floor($magazineCapacity/$ammoSize);
			  $enhName = $ammoClass->enhancementDescription;
			  $enhLimit = $actualCapacity;		
			  $enhPrice = $ammoClass->getPrice($ship); 
			  $enhPriceStep = 0; //flat rate
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }		  
		  $enhID = 'MINE_BLB'; //Ballistic Launcher Basic Mine
		  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option is enabled
				$ammoClass = new AmmoBLMineB();
				$ammoSize = $ammoClass->size;
				$actualCapacity = floor($magazineCapacity/$ammoSize);
			  $enhName = $ammoClass->enhancementDescription;
			  $enhLimit = $actualCapacity;		
			  $enhPrice = $ammoClass->getPrice($ship); 
			  $enhPriceStep = 0; //flat rate
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }		
		  $enhID = 'MINE_BLH'; //Ballistic Launcher Heavy Mine
		  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option is enabled
				$ammoClass = new AmmoBLMineH();
				$ammoSize = $ammoClass->size;
				$actualCapacity = floor($magazineCapacity/$ammoSize);
			  $enhName = $ammoClass->enhancementDescription;
			  $enhLimit = $actualCapacity;		
			  $enhPrice = $ammoClass->getPrice($ship); 
			  $enhPriceStep = 0; //flat rate
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }
		  $enhID = 'MINE_BLW'; //Ballistic Launcher Wide-Ranged Mine
		  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option is enabled
				$ammoClass = new AmmoBLMineW();
				$ammoSize = $ammoClass->size;
				$actualCapacity = floor($magazineCapacity/$ammoSize);
			  $enhName = $ammoClass->enhancementDescription;
			  $enhLimit = $actualCapacity;		
			  $enhPrice = $ammoClass->getPrice($ship); 
			  $enhPriceStep = 0; //flat rate
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }			  	
		  $enhID = 'MINE_MLB'; //Abbai Mine Launcher Basic Mine
		  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option is enabled
				$ammoClass = new AmmoBistifA();
				$ammoSize = $ammoClass->size;
				$actualCapacity = floor($magazineCapacity/$ammoSize);
			  $enhName = $ammoClass->enhancementDescription;
			  $enhLimit = $actualCapacity;		
			  $enhPrice = $ammoClass->getPrice($ship); 
			  $enhPriceStep = 0; //flat rate
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }		
		  $enhID = 'MINE_MLW'; //Abbai Mine Launcher Wide-Ranged Mine
		  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option is enabled
				$ammoClass = new AmmoBistifB();
				$ammoSize = $ammoClass->size;
				$actualCapacity = floor($magazineCapacity/$ammoSize);
			  $enhName = $ammoClass->enhancementDescription;
			  $enhLimit = $actualCapacity;		
			  $enhPrice = $ammoClass->getPrice($ship); 
			  $enhPriceStep = 0; //flat rate
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }	
		  $enhID = 'MINE_AML'; //Chouka Mine Launcher Vedas-A Mine  --  GTS
		  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option is enabled
				$ammoClass = new AmmoVedasA();
				$ammoSize = $ammoClass->size;
				$actualCapacity = floor($magazineCapacity/$ammoSize);
			  $enhName = $ammoClass->enhancementDescription;
			  $enhLimit = $actualCapacity;		
			  $enhPrice = $ammoClass->getPrice($ship); 
			  $enhPriceStep = 0; //flat rate
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }	
		  $enhID = 'MINE_BML'; //Chouka Mine Launcher Vedas-B Mine  --  GTS
		  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option is enabled
				$ammoClass = new AmmoVedasB();
				$ammoSize = $ammoClass->size;
				$actualCapacity = floor($magazineCapacity/$ammoSize);
			  $enhName = $ammoClass->enhancementDescription;
			  $enhLimit = $actualCapacity;		
			  $enhPrice = $ammoClass->getPrice($ship); 
			  $enhPriceStep = 0; //flat rate
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }	
		  $enhID = 'MINE_CML'; //Chouka Mine Launcher Vedas-C Mine  --  GTS
		  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option is enabled
				$ammoClass = new AmmoVedasC();
				$ammoSize = $ammoClass->size;
				$actualCapacity = floor($magazineCapacity/$ammoSize);
			  $enhName = $ammoClass->enhancementDescription;
			  $enhLimit = $actualCapacity;		
			  $enhPrice = $ammoClass->getPrice($ship); 
			  $enhPriceStep = 0; //flat rate
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }	

	//Ammo for Direct Fire Weapons	
		  $enhID = 'SHELL_HBSC'; //Standard Ammo for Heavy Railgun
		  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option is enabled
				$ammoClass = new AmmoHShellBasic();
				$ammoSize = $ammoClass->size;
				$actualCapacity = floor($magazineCapacity/$ammoSize);
			  $enhName = $ammoClass->enhancementDescription;
			  $enhLimit = $actualCapacity;		
			  $enhPrice = $ammoClass->getPrice($ship); 
			  $enhPriceStep = 0; //flat rate
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }			  
		  $enhID = 'SHELL_MBSC'; //Standard Ammo for Heavy Railgun
		  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option is enabled
				$ammoClass = new AmmoMShellBasic();
				$ammoSize = $ammoClass->size;
				$actualCapacity = floor($magazineCapacity/$ammoSize);
			  $enhName = $ammoClass->enhancementDescription;
			  $enhLimit = $actualCapacity;		
			  $enhPrice = $ammoClass->getPrice($ship); 
			  $enhPriceStep = 0; //flat rate
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }				  
		  $enhID = 'SHELL_LBSC'; //Standard Ammo for Heavy Railgun
		  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option is enabled
				$ammoClass = new AmmoLShellBasic();
				$ammoSize = $ammoClass->size;
				$actualCapacity = floor($magazineCapacity/$ammoSize);
			  $enhName = $ammoClass->enhancementDescription;
			  $enhLimit = $actualCapacity;		
			  $enhPrice = $ammoClass->getPrice($ship); 
			  $enhPriceStep = 0; //flat rate
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }			  
		  $enhID = 'SHELL_HFLH'; //Flash Ammo for Heavy Railgun
		  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option is enabled
				$ammoClass = new AmmoHShellFlash();
				$ammoSize = $ammoClass->size;
			  $enhName = $ammoClass->enhancementDescription;
			  $enhLimit = 0;
			  foreach ($ship->systems as $system){
					if ($system instanceof AmmoHeavyRailGun){
				  	$enhLimit += 3;
				  }
			  }  			  	  		
			  $enhPrice = $ammoClass->getPrice($ship); 
			  $enhPriceStep = 0; //flat rate
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }			  
		  $enhID = 'SHELL_MFLH'; //Flash Ammo for Medium Railgun
		  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option is enabled
				$ammoClass = new AmmoMShellFlash();
				$ammoSize = $ammoClass->size;
			  $enhName = $ammoClass->enhancementDescription;
			  $enhLimit = 0;
			  foreach ($ship->systems as $system){
					if ($system instanceof AmmoMediumRailGun){
				  	$enhLimit += 3;
				  }
			  }  		
			  $enhPrice = $ammoClass->getPrice($ship); 
			  $enhPriceStep = 0; //flat rate
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }	
		  $enhID = 'SHELL_LFLH'; //Flash Ammo for Heavy Railgun
		  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option is enabled
				$ammoClass = new AmmoLShellFlash();
				$ammoSize = $ammoClass->size;
			  $enhName = $ammoClass->enhancementDescription;
			  $enhLimit = 0;
			  foreach ($ship->systems as $system){
					if ($system instanceof AmmoLightRailGun){
				  	$enhLimit += 3;
				  }
			  }  		
			  $enhPrice = $ammoClass->getPrice($ship); 
			  $enhPriceStep = 0; //flat rate
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }	
		  $enhID = 'SHELL_HSCT'; //Scatter Ammo for Heavy Railgun
		  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option is enabled
				$ammoClass = new AmmoHShellScatter();
				$ammoSize = $ammoClass->size;
			  $enhName = $ammoClass->enhancementDescription;
			  $enhLimit = 0;
			  foreach ($ship->systems as $system){
					if ($system instanceof AmmoHeavyRailGun){
				  	$enhLimit += 3;
				  }
			  }  			  	  		
			  $enhPrice = $ammoClass->getPrice($ship); 
			  $enhPriceStep = 0; //flat rate
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }				    
		  $enhID = 'SHELL_MSCT'; //Scatter Ammo for Medium Railgun
		  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option is enabled
				$ammoClass = new AmmoMShellScatter();
				$ammoSize = $ammoClass->size;
			  $enhName = $ammoClass->enhancementDescription;
			  $enhLimit = 0;
			  foreach ($ship->systems as $system){
					if ($system instanceof AmmoMediumRailGun){
				  	$enhLimit += 3;
				  }
			  }  			  	  		
			  $enhPrice = $ammoClass->getPrice($ship); 
			  $enhPriceStep = 0; //flat rate
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }	
		  $enhID = 'SHELL_LSCT'; //Scatter Ammo for Light Railgun
		  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option is enabled
				$ammoClass = new AmmoLShellScatter();
				$ammoSize = $ammoClass->size;
			  $enhName = $ammoClass->enhancementDescription;
			  $enhLimit = 0;
			  foreach ($ship->systems as $system){
					if ($system instanceof AmmoLightRailGun){
				  	$enhLimit += 3;
				  }
			  }  			  	  		
			  $enhPrice = $ammoClass->getPrice($ship); 
			  $enhPriceStep = 0; //flat rate
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }
		  $enhID = 'SHELL_HHVY'; //Heavy Ammo for Heavy Railgun
		  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option is enabled
				$ammoClass = new AmmoHShellHeavy();
				$ammoSize = $ammoClass->size;
			  $enhName = $ammoClass->enhancementDescription;
			  $enhLimit = 0;
			  foreach ($ship->systems as $system){
					if ($system instanceof AmmoHeavyRailGun){
				  	$enhLimit += 1;
				  }
			  }  			  	  		
			  $enhPrice = $ammoClass->getPrice($ship); 
			  $enhPriceStep = 0; //flat rate
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }				    
		  $enhID = 'SHELL_MHVY'; //Heavy Ammo for Medium Railgun
		  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option is enabled
				$ammoClass = new AmmoMShellHeavy();
				$ammoSize = $ammoClass->size;
			  $enhName = $ammoClass->enhancementDescription;
			  $enhLimit = 0;
			  foreach ($ship->systems as $system){
					if ($system instanceof AmmoMediumRailGun){
				  	$enhLimit += 1;
				  }
			  }  			  	  		
			  $enhPrice = $ammoClass->getPrice($ship); 
			  $enhPriceStep = 0; //flat rate
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }	
		  $enhID = 'SHELL_LHVY'; //Heavy Ammo for Light Railgun
		  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option is enabled
				$ammoClass = new AmmoLShellHeavy();
				$ammoSize = $ammoClass->size;
			  $enhName = $ammoClass->enhancementDescription;
			  $enhLimit = 0;
			  foreach ($ship->systems as $system){
					if ($system instanceof AmmoLightRailGun){
				  	$enhLimit += 1;
				  }
			  }  			  	  		
			  $enhPrice = $ammoClass->getPrice($ship); 
			  $enhPriceStep = 0; //flat rate
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }
		  $enhID = 'SHELL_HLR'; //Long Range Ammo for Heavy Railgun
		  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option is enabled
				$ammoClass = new AmmoHShellLRange();
				$ammoSize = $ammoClass->size;
			  $enhName = $ammoClass->enhancementDescription;
			  $enhLimit = 0;
			  foreach ($ship->systems as $system){
					if ($system instanceof AmmoHeavyRailGun){
				  	$enhLimit += 3;
				  }
			  }  			  	  		
			  $enhPrice = $ammoClass->getPrice($ship); 
			  $enhPriceStep = 0; //flat rate
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }				    
		  $enhID = 'SHELL_MLR'; //Long Range Ammo for Medium Railgun
		  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option is enabled
				$ammoClass = new AmmoMShellLRange();
				$ammoSize = $ammoClass->size;
			  $enhName = $ammoClass->enhancementDescription;
			  $enhLimit = 0;
			  foreach ($ship->systems as $system){
					if ($system instanceof AmmoMediumRailGun){
				  	$enhLimit += 3;
				  }
			  }  			  	  		
			  $enhPrice = $ammoClass->getPrice($ship); 
			  $enhPriceStep = 0; //flat rate
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }			  
		  $enhID = 'SHELL_HULR'; //Ultra Long Range Ammo for Heavy Railgun
		  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option is enabled
				$ammoClass = new AmmoHShellULRange();
				$ammoSize = $ammoClass->size;
			  $enhName = $ammoClass->enhancementDescription;
			  $enhLimit = 0;
			  foreach ($ship->systems as $system){
					if ($system instanceof AmmoHeavyRailGun){
				  	$enhLimit += 3;
				  }
			  }  			  	  		
			  $enhPrice = $ammoClass->getPrice($ship); 
			  $enhPriceStep = 0; //flat rate
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }			    				  			  		  		  			  		  		  						  	  
	  } //end of magazine-requiring options
	  
	  
  } //endof function setEnhancementOptionsShip
	
	
	/* all fighter enhancement options - availability and cost calculation
	*/
  public static function setEnhancementOptionsFighter($flight){

	  //Add option to delay the deployment of a ship, set as Enhancement so selection is remembered in the game itself.
	  //DO NOT PLACE ANY OPTIONS (E.G. ENH[6] = TRUE) ALPHABETICALLY BEFORE THIS!	  	
	  /*$enhID = 'DEPLOY';
	  if(!in_array($enhID, $flight->enhancementOptionsEnabled)){ //option is enabled
		  $enhName = 'Choose which turn to deploy this unit:';
		  $enhLimit = 100;	
		  $enhPrice = 0; //no cost
		  $enhPriceStep = 0; //no cost
		  $flight->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,true);
		  //technical ID, human readable name, number taken, maximum number to take, price for one, price increase for each further, is an option (rather than enhancement)
	  }	 */

	  //Elite Marines for Breaching Pods, cost: 40% craft price (round up), limit: 1	  	
	  $enhID = 'ELT_MAR';	  
	  if(in_array($enhID, $flight->enhancementOptionsEnabled)){ //option needs to be specifically enabled
		  $enhName = 'Elite Marines';
		  $enhLimit = 1;	
		  $enhPrice = ceil($flight->pointCost/15); //price per craft, while flight price is per 6-craft flight	  
		  $enhPriceStep = 0;
		  $flight->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
	  }  

	  //Elite Pilot: StarWars only, sets pivot cost to 1, Initiative +1(5),  OB +1, profile -1, cost: 40% craft price (round up), limit: 1
	  $enhID = 'ELITE_SW';	  
	  if(in_array($enhID, $flight->enhancementOptionsEnabled)){ //option needs to be specifically enabled
		  $enhName = 'Elite Pilot';
		  $enhLimit = 1;		  
		  if($flight instanceof SuperHeavyFighter){//single-craft flight! 
			$enhPrice = ceil($flight->pointCost*0.4);
		  }else{
		  	$enhPrice = ceil(0.4*$flight->pointCost/6); //price per craft, while flight price is per 6-craft flight	  
		  }  
		  $enhPriceStep = 0;
		  $flight->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
	  }  	  
	  
		//Expert Motivator: -2 dropout modifier, cost: 10% craft price (round up), limit: 1
	  $enhID = 'EXP_MOTIV';	  
	  if(!in_array($enhID, $flight->enhancementOptionsDisabled)){ //option needs to be specifically enabled
		  $enhName = 'Expert Motivator';
		  $enhLimit = 1;	
		  if($flight instanceof SuperHeavyFighter){//single-craft flight! 
			$enhPrice = ceil($flight->pointCost/10);
		  }else{
		  	$enhPrice = ceil($flight->pointCost/60); //price per craft, while flight price is per 6-craft flight	  
		  }
		  $enhPriceStep = 0;
		  $flight->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
	  }
	  
	  //Extra Ammo for Fighters with a limited supply, cost: 3 per extra shot, limit: 5	  	
	  $enhID = 'EXT_AMMO';	  
	  if(in_array($enhID, $flight->enhancementOptionsEnabled)){ //option needs to be specifically enabled
		  $enhName = 'Additional Ammo for Gun';
		  $enhLimit = 2;	
		  $enhPrice = 3; //price per craft, while flight price is per 6-craft flight	  
		  $enhPriceStep = 0;
		  $flight->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,true);
	  } 
	  	  
	  //Extra Ammo for heavy guns on Fighters with a limited supply, cost: 3 per extra shot, limit: 2	  	
	  $enhID = 'EXT_HAMMO';	  
	  if(in_array($enhID, $flight->enhancementOptionsEnabled)){ //option needs to be specifically enabled
		  $enhName = 'Additional Ammo for Heavy Gun';
		  $enhLimit = 2;	
		  $enhPrice = 3; //price per craft, while flight price is per 6-craft flight	  
		  $enhPriceStep = 0;
		  $flight->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,true);
	  } 


	  //Extra Marines for Breaching Pods, cost: 10 per pod, limit: 2	  	
	  $enhID = 'EXT_MAR';	  
	  if(in_array($enhID, $flight->enhancementOptionsEnabled)){ //option needs to be specifically enabled
		  $enhName = 'Extra Marine Units';
		  $enhLimit = 2;	
		  $enhPrice = 10; //price per craft, while flight price is per 6-craft flight	  
		  $enhPriceStep = 0;
		  $flight->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,true);
	  }  
	  //Markab specific - 'Religious Ferver' refit than provides some benefits along with some penalties.
	  $enhID = 'FTR_FERV';	  
	  if(in_array($enhID, $flight->enhancementOptionsEnabled)){ //option needs to be specifically enabled
		  $enhName = 'Religious Fervor - Only use when Desperate Rules apply';
		  $enhLimit = 1;	
		  $enhPrice = 0;	  
		  $enhPriceStep = 0;
		  $flight->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
	  }

	  //Improved Targeting Computer: +1 OB, cost: new rating *3, limit: 1
	  $enhID = 'IMPR_OB';	  
	  if(!in_array($enhID, $flight->enhancementOptionsDisabled)){ //option is not disabled
		  $enhName = 'Improved Targeting Computer';
		  $enhLimit = 1;	
		  $enhPrice = max(1,($flight->offensivebonus+1)*3);	  
		  $enhPriceStep = 0; //3; //limit is 1 but if anyone increases it - step is ready...or would be but the effect looks silly ;)
		  $flight->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
	  }

	  //Improved Thrust: +1 free thrust, cost: new rating, limit: 1
	  $enhID = 'IMPR_THR';	  
	  if(!in_array($enhID, $flight->enhancementOptionsDisabled)){ //option is not disabled
		  $enhName = 'Improved Thrust';
		  $enhLimit = 1;	
		  $enhPrice = max(1,$flight->freethrust+1);
		  $enhPriceStep = 0;
		  $flight->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
	  }

	  //Convert to Minesweeper: sets $minesweeper = true (full OB to mine detection).
	  //Only offered to shuttle-type units ($hangarRequired === 'shuttles').
	  //Cost (per craft in flight): 10pts or 20% of one craft's cost (round up),
	  //whichever is higher. Flight pointCost is for the whole 6-craft flight, so
	  //divide by 6 first (SuperHeavyFighter is a single-craft flight). Limit: 1
	  $enhID = 'MAKE_MINE';
	  if(in_array($enhID, $flight->enhancementOptionsEnabled)){ //option needs to be specifically enabled
		  $enhName = 'Minesweeper Conversion';
		  $enhLimit = 1;
		  if($flight instanceof SuperHeavyFighter){//single-craft flight!
			$perCraftCost = $flight->pointCost;
		  }else{
			$perCraftCost = $flight->pointCost / 6;
		  }
		  $enhPrice = max(10, (int)ceil($perCraftCost * 0.2));
		  $enhPriceStep = 0;
		  $flight->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,true);
	  }

	  //Navigator: missile guidance, +1(5) Ini, cost: 10, limit: 1
	  $enhID = 'NAVIGATOR';	  
	  if(in_array($enhID, $flight->enhancementOptionsEnabled)){ //option needs to be specifically enabled
		  $enhName = 'Navigator';
		  $enhLimit = 1;	
		  $enhPrice = 10;	  
		  $enhPriceStep = 0;
		  $flight->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,true); //NOT an enhancement!
	  }  
	  
	  
	  //Poor Training (equivalent of Poor Crew for ships):
	  //Effect: -5 Initiative, -1 Free Thrust, -1 OB, +1 Profile, +2 to dropout rolls
	  //Cost: -10% of craft price (round up)
	  //Limit: 1 
	  $enhID = 'POOR_TRAIN';	  
	  if(!in_array($enhID, $flight->enhancementOptionsDisabled)){ //option is not disabled
		  $enhName = 'Poor Training';
		  $enhLimit = 1;	
		  if($flight instanceof SuperHeavyFighter){//single-craft flight! 
			$enhPrice = -ceil($flight->pointCost/10);
		  }else{
		  	$enhPrice = -ceil($flight->pointCost/60); //price per craft, while flight price is per 6-craft flight	  
		  } 	  
		  $enhPriceStep = 0;
		  $flight->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
	  }
	  
	  //Shadow fighter deployed without carrier control: -2 OB, -3(15) Ini, cost: 0, limit: 1
	  $enhID = 'SHAD_CTRL';
	  if(in_array($enhID, $flight->enhancementOptionsEnabled)){ //option needs to be specifically enabled
		  $enhName = 'Uncontrolled Fighter';
		  $enhLimit = 1;
		  $enhPrice = 0;
		  $enhPriceStep = 0;
		  //Stage S: after the integrated-fighter patch, separate Shadow fighters are
		  //scenario-only and are ALWAYS deployed uncontrolled — it is no longer
		  //optional. Default the current count to 1 (was 0) so SHAD_CTRL is applied
		  //by default; limit stays 1 so it can't be stacked. (Carrier-controlled
		  //Shadow fighters now come from the carrier's integrated bay, not here.)
		  $flight->enhancementOptions[] = array($enhID, $enhName,1,$enhLimit, $enhPrice, $enhPriceStep,true); //NOT an enhancement!
	  }
	  
	/* Vorlon Azure Skin Coloring:
	Effect: +1 Shield rating, for all shields.
	Cost: unit size factor x new shield rating x nuber of emitters
	Limit: 50% of base shield rating
	Init size factor is 30 for Enormous units, 25 for Capitals, 20 for anything smaller (including fighters).
	*/
	  $enhID = 'VOR_AZURF';	  
	  if(in_array($enhID, $flight->enhancementOptionsEnabled)){ //option needs to be specifically enabled
		  $enhName = 'Azure Skin Coloring';
		$sampleFighter = $flight->getSampleFighter();
		$count = 0;
		$rating = 0;
		foreach($sampleFighter->systems as $sys) if($sys instanceOf FtrShield){
			$count++;
			$rating = $sys->output;
		}
		if($count>0 && $rating>1){
		  $enhLimit = floor($rating/2);
		  $enhPrice = 20*$count*($rating+1);		  
		  $enhPriceStep = 20*$count;
		  $flight->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		}
	  }
	  
	  //REEVALUATION - for official units that got CUSTOM reevaluation
 	  $enhID = 'RE-EVAL';
	  if(!in_array($enhID, $flight->enhancementOptionsDisabled)){ //option is not disabled
		  $enhName = 'CUSTOM price reevaluation';
		  $enhLimit = 1;
		  $enhPriceStep = 0; //flat rate
		  $enhPrice = 0;
		  
		  switch($flight->phpclass){
			  case 'Calaq': //Cascor Calaq Assault Fighter: 60->45
				  $enhPrice = -15;
				  break;
			  case 'Caltus': //Cascor Caltus Torpedo Fighter: 65->40
				  $enhPrice = -25;
				  break;
			  
			  case 'Utan': //Yolu Utan Heavy Fighter: 110->90
				  $enhPrice = -20;
				  break;
			  case 'Yonor': //Yolu Yonor Breaching Pods: 50->50
				  $enhPrice = 0; //just so there's reevaluation visible
				  break;
			  case 'Nathor': //Yolu Nathor Assault Shuttles: 40->75
				  $enhPrice = 35;
				  break;
			  
			  default:
				  $enhPrice = 0;
				  break;
		  }
		  
		  if ($enhPrice != 0){
		  	$flight->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }
	  }
	  
	  

	  //consumable ammunition
	  //find magazine capacity to set appropriate limits!
	  //ASSUMING multiple but equal magazines (for fighter flights) (no magazine is fine too)
	  //availablility in name - for EA and other factions respectably - just in case someone wishes for in-universe accurate availability
	  $magazineCapacity = 0;
	  foreach($flight->getSampleFighter()->systems as $magazine) if($magazine->name == 'ammoMagazine'){
		  $magazineCapacity = $magazine->capacity;
		  break; //foreach
	  }
	  if ($magazineCapacity > 0){ //otherwise no point
		  $enhID = 'AMMO_FB'; //Basic Fighter Missiles
		  if(in_array($enhID, $flight->enhancementOptionsEnabled)){ //option is enabled
				$ammoClass = new AmmoMissileFB();
				$ammoSize = $ammoClass->size;
				$actualCapacity = floor($magazineCapacity/$ammoSize);
			  $enhName = $ammoClass->enhancementDescription;
			  $enhLimit = $actualCapacity; //effectively limited by magazine capacity	
			  $enhPrice = $ammoClass->getPrice($flight); 
			  $enhPriceStep = 0; //flat rate
			  $flight->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false );
			  //variable missiles are NOT enhancements!
		  }
		  $enhID = 'AMMO_FL'; //Long Range Fighter Missiles
		  if(in_array($enhID, $flight->enhancementOptionsEnabled)){ //option is enabled
				$ammoClass = new AmmoMissileFL();
				$ammoSize = $ammoClass->size;
				$actualCapacity = floor($magazineCapacity/$ammoSize);
			  $enhName = $ammoClass->enhancementDescription;
			  $enhLimit = $actualCapacity;		
			  $enhPrice = $ammoClass->getPrice($flight); 
			  $enhPriceStep = 0; //flat rate
			  $flight->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }
		  $enhID = 'AMMO_FH'; //Heavy FighterMissiles
		  if(in_array($enhID, $flight->enhancementOptionsEnabled)){ //option is enabled
				$ammoClass = new AmmoMissileFH();
				$ammoSize = $ammoClass->size;
				$actualCapacity = floor($magazineCapacity/$ammoSize);
				
				//limited to 2 per SHF, or 1 per lighter craft
				$maxLimit = 1;
				if($flight->superheavy) $maxLimit = 2;
				$actualCapacity = min($actualCapacity,$maxLimit);
				
			  $enhName = $ammoClass->enhancementDescription;
			  $enhLimit = $actualCapacity;		
			  $enhPrice = $ammoClass->getPrice($flight); 
			  $enhPriceStep = 0; //flat rate
			  $flight->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }
		  $enhID = 'AMMO_FY'; //Dogfight FighterMissiles
		  if(in_array($enhID, $flight->enhancementOptionsEnabled)){ //option is enabled
				$ammoClass = new AmmoMissileFY();
				$ammoSize = $ammoClass->size;
				$actualCapacity = floor($magazineCapacity/$ammoSize);
			  $enhName = $ammoClass->enhancementDescription;
			  $enhLimit = $actualCapacity;		
			  $enhPrice = $ammoClass->getPrice($flight); 
			  $enhPriceStep = 0; //flat rate
			  $flight->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }
		  $enhID = 'AMMO_FD'; //Dropout FighterMissiles
		  if(in_array($enhID, $flight->enhancementOptionsEnabled)){ //option is enabled
				$ammoClass = new AmmoMissileFD();
				$ammoSize = $ammoClass->size;
				$actualCapacity = floor($magazineCapacity/$ammoSize);
			  $enhName = $ammoClass->enhancementDescription;
			  $enhLimit = $actualCapacity;
			  $enhPrice = $ammoClass->getPrice($flight); 
			  $enhPriceStep = 0; //flat rate
			  $flight->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }
		  $enhID = 'AMMO_DUM'; //Dummy Fighter Missiles
		  if(in_array($enhID, $flight->enhancementOptionsEnabled)){ //option is enabled
			$ammoClass = new AmmoMissileFDum();
			$ammoSize = $ammoClass->size;
			$actualCapacity = floor($magazineCapacity/$ammoSize);
			$enhName = $ammoClass->enhancementDescription;
			$enhLimit = $actualCapacity;
			$enhPrice = $ammoClass->getPrice($flight); 
			$enhPriceStep = 0; //flat rate
			$flight->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
		  }		  
	  } //end of magazine-requiring options
	  
	  
  } //endof function setEnhancementOptionsFighter
	    
    
  
	/*Create the systems that an enhancement ADDS to a hull. Separate from setEnhancements(), which
	  changes the numbers on systems that already exist, because the two must run at completely
	  different moments:

	    - setEnhancements() runs from BaseShip::onConstructed(), which DBManager::getTacGamedata
	      calls only AFTER getTacShips() has read tac_critical and tac_damage.
	    - a system that does not exist yet cannot be given its rows. getDamageForShips and
	      getCriticalsForShips resolve every row through getSystemById() and SKIP - silently - what
	      does not resolve. A tendril created as late as onConstructed() would therefore lose every
	      point of energy it had absorbed on every single page load, because a tendril stores its
	      absorbed energy AS damage (DiffuserTendril::absorbDamage).

	  So this runs early, from DBManager::getTacShips, straight after the tac_enhancements rows are
	  read and BEFORE the criticals/damage queries. It must do nothing but mount systems - every
	  other effect of an enhancement still belongs in setEnhancements().

	  New systems are APPENDED (BaseShip::addEnhancementSystem -> addSystem), so ids are stable in
	  both directions: nothing already on the hull moves, and the new systems land on the same ids
	  every load, because the same enhancement rows rebuild the same systems in the same order.*/
	public static function addEnhancementSystems($ship){
		if($ship->enhancementSystemsAdded) return; //once per ship object - see the property
		$ship->enhancementSystemsAdded = true;
		if($ship instanceof FighterFlight) return; //no enhancement mounts systems on a flight

		foreach($ship->enhancementOptions as $entry){ //ID,readableName,numberTaken,limit,price,priceStep
			$enhID = $entry[0];
			$enhCount = (int)$entry[2];
			if($enhCount <= 0) continue;
			switch($enhID){
				case 'SHAD_TEND': //Extra Tendrils
					Enhancements::addExtraTendrils($ship, $enhCount);
					break;
			}
		}
	} //endof function addEnhancementSystems

	/*Extra Tendrils (SHAD_TEND) - grow one new Diffuser Tendril of $enhCount*5 absorption capacity
	  on each half of the ship's strongest Energy Diffuser pair.

	  "The strongest pair" is the highest-rated diffuser on each SIDE. Shadow hulls mount their
	  diffusers as mirror images, but not always two of them: the Battlecruiser has six, three
	  port/starboard pairs rated 5, 10 and 20, where the top rating already picks out exactly one
	  pair - but ShadowRegenBase has four ALL rated 20, two port and two starboard, one per quarter
	  arc. Taking every diffuser at the top rating would hand that base four tendrils for the price
	  of a pair, so at most one per side is taken, the first one found. That is stable across loads
	  because construction order is. A hull with only one diffuser at the top rating grows a single
	  tendril - there is no phantom other half to hang one on.

	  Each new tendril copies its SECTION and its L/R artwork from a tendril already on that same
	  diffuser, rather than deriving them from the diffuser's arc: tendril placement is per hull
	  (usually the Port/Stbd sections, but front/aft on some customs), and matching a sibling is the
	  only rule that is correct on all of them. That section doubles as the "side" for the one-per-
	  side rule above.*/
	private static function addExtraTendrils($ship, $enhCount){
		$capacity = $enhCount * 5;

		$bestRating = null;
		foreach ($ship->systems as $system){
			if (!($system instanceof EnergyDiffuser)) continue;
			if (count($system->tendrils) == 0) continue; //nothing to match, and nothing to grow from
			if (($bestRating === null) || ($system->output > $bestRating)) $bestRating = $system->output;
		}
		if($bestRating === null) return; //no diffuser with tendrils - option should never have been offered

		//Collected first, then mounted: addEnhancementSystem appends to the very array being walked.
		//Keyed by SIDE (the section the diffuser's own tendrils sit in) so at most one diffuser per
		//side is picked - see the note above on ShadowRegenBase.
		$targets = array();
		foreach ($ship->systems as $system){
			if (!($system instanceof EnergyDiffuser)) continue;
			if (count($system->tendrils) == 0) continue;
			if ($system->output != $bestRating) continue;
			$side = $system->tendrils[0]->location;
			if (isset($targets[$side])) continue; //this side already has its tendril
			$targets[$side] = $system;
		}

		foreach ($targets as $diffuser){
			$sibling = $diffuser->tendrils[0];
			//iconPath is built as 'EnergyDiffuserTendril' . side . sizeBand . '.png' - the side is
			//the only place the L/R choice survives on a constructed tendril.
			$side = (strpos($sibling->iconPath, 'EnergyDiffuserTendrilL') === 0) ? 'L' : 'R';

			$tendril = new DiffuserTendril($capacity, $side);
			$tendril->addedByEnhancement = true; //no static blueprint entry - see ShipSystem
			$ship->addEnhancementSystem($tendril, $sibling->location);
			$diffuser->addTendrilSorted($tendril); //ordered list - largest first
		}
	} //endof function addExtraTendrils

	/*actually enhances unit (sets enhancement options if enhancements themselves are empty)
	*/
	public static function setEnhancements($ship){
		//actually implement enhancements - it's more convenient to divide fighters and ships here
		if($ship instanceof FighterFlight){
			Enhancements::setEnhancementsFighter($ship);
		}else{
			Enhancements::setEnhancementsShip($ship);
		}
		   
		//clear array of options - no further point keeping it
		//$ship->enhancementOptions = array();
		$ship->enhancementOptionsEnabled = array();
		$ship->enhancementOptionsDisabled = array();
	} //endof function setEnhancements
  
  
	/*Consumable ammunition enhancements (missiles, railgun shells, launcher-loaded
	  mines) load an AmmoMagazine via addAmmoEntry, so their counts already appear in
	  the AmmoMagazine system tooltip. They are kept OUT of enhancementTooltip so the
	  ship window's Enhancements box does not duplicate that (2026-07-19 shipwindow
	  refinement). Prefix-matched so new AMMO_/SHELL_ types are covered automatically;
	  launcher-loaded mines share the MINE_ prefix with the mine-SHIP enhancements
	  (MINE_ACC/ARM/DMG/...), which are NOT ammo, so they are listed explicitly.
	  To restore ammo in the list, drop the isAmmoEnhancement() guards in
	  setEnhancementsFighter / setEnhancementsShip.*/
	private static function isAmmoEnhancement($enhID){
		if(strpos($enhID, 'AMMO_') === 0) return true;   //ship + fighter missiles
		if(strpos($enhID, 'SHELL_') === 0) return true;  //direct-fire railgun shells
		static $launcherMines = array('MINE_BLB','MINE_BLH','MINE_BLW','MINE_MLB','MINE_MLW','MINE_AML','MINE_BML','MINE_CML');
		return in_array($enhID, $launcherMines, true);
	}

	/*What to persist in tac_enhancements.enhname (or tac_saved_enh.enhname) for one bought option.
	  Normally just the human-readable name. For a CHOICE-valued option it is the PICK itself - a
	  phpclass - resolved HERE, server-side, from the submitted index against a freshly built list:
	  the client only ever submits an index, so a doctored payload cannot name an arbitrary class,
	  and the option's index 1 stays the display label in every buy/edit dialog.
	  Storing the name as well as the index is what makes the choice survive a saved-fleet reload -
	  the candidate list is rebuilt from disk each time and could otherwise be silently renumbered by
	  a ship being added to or retired from the faction.*/
	public static function getStoredEnhancementName($ship, $enhancementEntry){
		if($enhancementEntry[0] === 'CHAM_DISG'){
			$choices = ShipLoader::getDisguiseCandidates($ship->faction, $ship->phpclass);
			$index = (int)$enhancementEntry[2];
			return isset($choices[$index][0]) ? $choices[$index][0] : '';
		}
		return $enhancementEntry[1];
	}

	/*Resolve a stored CHAM_DISG entry to a phpclass, or null for "None" / anything unresolvable.
	  Two encodings have to be honoured, because the entry reaches here by two different routes:
	    - IN GAME: DBManager rebuilds the tuple as [enhID, enhname, numbertaken, 0,0,0], so index 1 is
	               the stored phpclass and there is no choice list left to index into.
	    - IN LOBBY: the option still carries its choice list at index 7, and index 1 holds whatever the
	               buy dialog wrote there.
	  Prefer the NAME (it survives the list being rebuilt or reordered between sessions), fall back to
	  the INDEX, and fall back to None rather than to an arbitrary ship if neither resolves - a choice
	  that no longer exists must leave an ordinary working ship, not a randomly disguised one.*/
	private static function resolveChameleonDisguise($ship, $entry){
		$byName = isset($entry[1]) ? (string)$entry[1] : '';
		if(ShipLoader::isLegalDisguise($ship->faction, $byName, $ship->phpclass)) return $byName;

		$choices = (isset($entry[7]) && is_array($entry[7])) ? $entry[7] : null;
		$index = (int)$entry[2];
		if($choices !== null && isset($choices[$index][0])){
			$byIndex = (string)$choices[$index][0];
			if(ShipLoader::isLegalDisguise($ship->faction, $byIndex, $ship->phpclass)) return $byIndex;
		}

		return null; //None
	}

	/*The ship's Chameleon suite, or null. Walks $ship->systems rather than going through
	  BaseShip::getChameleonSensors(): this runs from BaseShip::onConstructed BEFORE the loop that
	  fills the special-ability index that method reads, so the index is still empty here (the same
	  trap a POST-side ship hits permanently).*/
	private static function getChameleonSuite($ship){
		foreach($ship->systems as $system){
			if($system instanceof ChameleonSensors) return $system;
		}
		return null;
	}

	/*enhancements for fighters - actual applying of chosen enhancements
	*/
	private static function setEnhancementsFighter($flight){
	   	foreach($flight->enhancementOptions as $entry){			
			//ID,readableName,numberTaken,limit,price,priceStep
			$enhID = $entry[0];
			$enhCount = $entry[2];
			$enhDescription = $entry[1];
			if($enhCount > 0) {
				if(!self::isAmmoEnhancement($enhID)){ //ammo already shows in the AmmoMagazine tooltip - keep it out of the Enhancements box
				if($flight->enhancementTooltip != "") $flight->enhancementTooltip .= "<br>";
				//if($enhID == 'DEPLOY' && $enhCount > 1){ //Special type of Enhancement, clarify what it means.
				//	$flight->enhancementTooltip .= "Flight deploys on Turn $enhCount";
				//}else{
				$flight->enhancementTooltip .= "$enhDescription";
				if ($enhCount>1) $flight->enhancementTooltip .= " (x$enhCount)";
				//}
				}
				switch ($enhID) {
					
					case 'DEPLOY':
						//Amend value of turn that ship deploys on.
						//$flight->deploysOnTurn = $enhCount;
						break;	

					case 'ELT_MAR'://Elite marines, mark every Marines system as Elite.
						foreach($flight->systems as $ftr) foreach($ftr->systems as $sys) if($sys instanceOf Marines){
						$sys->eliteMarines = true;
						}
						break;						
					case 'ELITE_SW': //Elite Pilot (SW): pivot cost 1, Ini +1, OB +1, Profiles -1
						$flight->pivotcost = 1;
						$flight->offensivebonus += $enhCount;
						$flight->iniativebonus += $enhCount*5;
						$flight->forwardDefense -= $enhCount;
						$flight->sideDefense -= $enhCount;
						break;
					case 'EXP_MOTIV': //Expert Motivator: -2 on dropout rolls
						$flight->critRollMod -= $enhCount*2;
						break;
					case 'EXT_AMMO'://Extra ammo, for fighter with a limited supply.  Max 5 extra shots.
						foreach($flight->systems as $ftr) foreach($ftr->systems as $sys)
							//Find relevant weapons 
							if(	$sys instanceOf PairedGatlingGun || 
								$sys instanceOf LtGatlingGun || 
								$sys instanceOf MatterGun || 
								$sys instanceOf SlugCannon || 
								$sys instanceOf GatlingGunFtr ||
								$sys instanceof NexusAutogun ||
								$sys instanceof NexusMinigunFtr ||
								$sys instanceof NexusShatterGunFtr ||
								$sys instanceof NexusLightDefenseGun){
							$sys->ammunition += $enhCount;//Increase ammo for these weapons by $enhCount
							}
						break;							
					case 'EXT_HAMMO'://Extra heavy ammo, for fighter with a limited supply.  Max 2 extra shots.
						foreach($flight->systems as $ftr) foreach($ftr->systems as $sys)
							//Find relevant weapons 
							if(	$sys instanceOf NexusAutocannonFtr || 
								$sys instanceof NexusLightGasGunFtr){
							$sys->ammunition += $enhCount;//Increase ammo for these weapons by $enhCount
							}
						break;							
					case 'EXT_MAR'://Extra marines, increase contingent per pod by 1.
						foreach($flight->systems as $ftr) foreach($ftr->systems as $sys) if($sys instanceOf Marines){
						$sys->ammunition += $enhCount;
						}
						break;							
					case 'FTR_FERV': //Markab Religious Fervor: +1 to hit for all weapons, +10 Initiative, +2 Defence Profiles, -3 dropout rolls
						$flight->offensivebonus += $enhCount;
						$flight->iniativebonus += $enhCount*10;
						$flight->forwardDefense += $enhCount*2;
						$flight->sideDefense += $enhCount*2;
						$flight->critRollMod -= $enhCount*3;
						break;												
					case 'IMPR_OB': //Improved Targeting Computer: +1 OB
						$flight->offensivebonus += $enhCount;
						break;
					case 'IMPR_THR': //Improved Thrust: +1 free thrust
						$flight->freethrust += $enhCount;
						break;
					case 'MAKE_MINE': //Minesweeper Conversion: shuttle gains minesweeper flag
						$flight->minesweeper = true;
						if($flight->offensivebonus < 4) $flight->offensivebonus = 4;
						break;
					case 'NAVIGATOR': //Navigator: navigator flag - it activates appropriate segments of code
						$flight->hasNavigator = true;
						break;
					case 'POOR_TRAIN': //Poor Training: -5 Initiative, -1 Free Thrust, -1 OB, +1 Profile, +2 to dropout rolls
						$flight->critRollMod += $enhCount*2;
						$flight->offensivebonus -= $enhCount;
						$flight->freethrust -= $enhCount;
						$flight->iniativebonus -= $enhCount*5;
						$flight->forwardDefense += $enhCount;
						$flight->sideDefense += $enhCount;
						break;
					case 'SHAD_CTRL': //Shadow fighter deployed without carrier control: -2 OB, -3(15) Ini
						$flight->offensivebonus -= $enhCount*2;
						$flight->iniativebonus -= $enhCount*3*5;
						break;
					case 'VOR_AZURF':// Vorlon Azure Skin Coloring: +1 shield factor - needst to be separately applied for every shield in flight...
						foreach($flight->systems as $ftr) foreach($ftr->systems as $sys) if($sys instanceOf FtrShield){
							$sys->output += $enhCount;
						}
						break;
						
						
					//consumable ammunition - add to ALL missile magazines on flight!
					case 'AMMO_FB': //Basic Fighter Missile
						foreach($flight->systems as $craft) foreach($craft->systems as $ftrAM) if ($ftrAM->name == 'ammoMagazine') {
							$ftrAM->addAmmoEntry(new AmmoMissileFB(), $enhCount, true); //do notify dependent weapons, too!
						}
						break;
					case 'AMMO_FH': //Heavy Fighter Missile
						foreach($flight->systems as $craft) foreach($craft->systems as $ftrAM) if ($ftrAM->name == 'ammoMagazine') {
							$ftrAM->addAmmoEntry(new AmmoMissileFH(), $enhCount, true); //do notify dependent weapons, too!
						}
						break;
					case 'AMMO_FL': //Long Range Fighter Missile
						foreach($flight->systems as $craft) foreach($craft->systems as $ftrAM) if ($ftrAM->name == 'ammoMagazine') {
							$ftrAM->addAmmoEntry(new AmmoMissileFL(), $enhCount, true); //do notify dependent weapons, too!
						}
						break;
					case 'AMMO_FY': //Dogfight Fighter Missile
						foreach($flight->systems as $craft) foreach($craft->systems as $ftrAM) if ($ftrAM->name == 'ammoMagazine') {
							$ftrAM->addAmmoEntry(new AmmoMissileFY(), $enhCount, true); //do notify dependent weapons, too!
						}
						break;
					case 'AMMO_FD': //Dropout Fighter Missile
						foreach($flight->systems as $craft) foreach($craft->systems as $ftrAM) if ($ftrAM->name == 'ammoMagazine') {
							$ftrAM->addAmmoEntry(new AmmoMissileFD(), $enhCount, true); //do notify dependent weapons, too!
						}
						break;
					case 'AMMO_DUM': //Dummy Fighter Missile
						foreach($flight->systems as $craft) foreach($craft->systems as $ftrAM) if ($ftrAM->name == 'ammoMagazine') {
							$ftrAM->addAmmoEntry(new AmmoMissileFDum(), $enhCount, true); //do notify dependent weapons, too!
						}
						break;						
						
				}
			}			
		}
	   }//endof function setEnhancementsFighter
  
	
	   /*enhancements for ships - actual applying of chosen enhancements
	   */
	   private static function setEnhancementsShip($ship){
		//ammo magazine is necessary for some options
		$ammoMagazine = null;
		foreach($ship->systems as $magazine) if($magazine->name == 'ammoMagazine'){
			  $ammoMagazine = $magazine;
			  break; //foreach
		}
		   
	   	foreach($ship->enhancementOptions as $entry){
			//ID,readableName,numberTaken,limit,price,priceStep
			$enhID = $entry[0];
			$enhCount = $entry[2];
			$enhDescription = $entry[1];
			if($enhCount > 0) {
				//CHAM_DISG is choice-valued, so enhDescription is a phpclass, not prose, and the count
				//is an index - the generic line would read "kendariUpgraded (x7)". SHAD_TEND's count is
				//a capacity in fives, so the generic line would read "(x2)" where the player bought a
				//10-capacity tendril. Both write their own line below.
				if(!self::isAmmoEnhancement($enhID) && $enhID !== 'CHAM_DISG' && $enhID !== 'SHAD_TEND'){ //ammo already shows in the AmmoMagazine tooltip - keep it out of the Enhancements box
				if($ship->enhancementTooltip != "") $ship->enhancementTooltip .= "<br>";
				//if($enhID == 'DEPLOY'){ //Special type of Enhancement, clarify what it means.
				//	$ship->enhancementTooltip .= "Ship deploys on Turn $enhCount";
				//}else{
				$ship->enhancementTooltip .= "$enhDescription";
				if ($enhCount>1) $ship->enhancementTooltip .= " (x$enhCount)";
				//}
				}

			        switch ($enhID) {

					case 'DEPLOY':
						//Amend value of turn that ship deploys on.
						//$ship->deploysOnTurn = $enhCount;
						break;

					case 'CHAM_DISG': //Chameleon Sensor Suite - the vessel this ship pretends to be
						$disguise = self::resolveChameleonDisguise($ship, $entry);
						$ship->chameleonDisguiseClass = $disguise;
						$chamSuite = self::getChameleonSuite($ship);
						/*isProjecting(): once every enemy team has seen through the deception (or the
						  owner has dropped it) this line stops being a reminder of what the enemy is
						  looking at and becomes a lie - they are looking at a Dargan. Same question
						  ChameleonSensors::stripForJson asks before it paints the array as active,
						  asked through the same method so the two can never drift apart.*/
						if($disguise !== null && $ship->isRevealedToCurrentViewer()
							&& $chamSuite !== null && $chamSuite->isProjecting()){
							//the disguise is the secret this whole system exists to keep - never put it
							//in a payload built for an enemy (enemy payloads also drop enhancementTooltip
							//wholesale once stripForJsonDisguised exists, but do not rely on that alone)
							$label = ShipLoader::getDisguiseLabel($ship->faction, $disguise, $ship->phpclass);
							if($ship->enhancementTooltip != "") $ship->enhancementTooltip .= "<br>";
							//with more than one opponent, WHICH of them still believes it is the whole
							//point - a reveal is per team and permanent (empty string in a 1v1)
							$ship->enhancementTooltip .= "Disguised as: " . ($label !== null ? $label : $disguise)
								. $chamSuite->getDeceivedTeamsLabel();
						}
						break;

					case 'ELITE_CREW': //Elite Crew: +5 Initiative, +2 Engine, +1 Sensors, +2 Reactor power, -1 Profile, -2 to critical results, +1 to hit all weapons
						//fixed values
						$ship->forwardDefense -= $enhCount;
						$ship->sideDefense -= $enhCount;
						$ship->iniativebonus += $enhCount*5;
						$ship->critRollMod -= $enhCount*2;
						$ship->toHitBonus += $enhCount;						
						
						//system mods: Scanner						
						$strongestSystem = null;
						$strongestValue = -1;	  
						foreach ($ship->systems as $system){
							if ($system instanceof Scanner){
								if($system->output > $strongestValue) {
									$strongestValue = $system->output;
									$strongestSystem = $system;
								}
							}
						}  
						if($strongestValue > 0){ //Scanner actually exists to be enhanced!
							$strongestSystem->output += $enhCount;
						}
						//system mods: Engine	
						$strongestSystem = null;
						$strongestValue = -1;	  
						foreach ($ship->systems as $system){
							if ($system instanceof Engine){
								if($system->output > $strongestValue) {
									$strongestValue = $system->output;
									$strongestSystem = $system;
								}
							}
						}  
						if($strongestValue > 0){ //Engine actually exists to be enhanced!
							$strongestSystem->output += $enhCount*2;
						}
						//system mods: Reactor (here I assume main reactor is the biggest one! - ot the strongest, as eg. any malus would be on main reactor as well)
						$strongestSystem = null;
						$strongestValue = -1000;
						foreach ($ship->systems as $system){
							if ($system instanceof Reactor){
								if($system->maxhealth > $strongestValue) {
									$strongestValue = $system->maxhealth;
									$strongestSystem = $system;
								}
							}
						}  
						if($strongestSystem != null){ //Reactor actually exists to be enhanced! although it has to ;)
							$strongestSystem->output += $enhCount*2;
						}						
						//modify thruster ratings as well! - of all thrusters
						foreach ($ship->systems as $system){
							if ($system instanceof Thruster){
								$system->output += $enhCount;
							}
						} 						
						break;						

					case 'ELT_MRN'://Elite marines, mark every Marines system as Elite.
						foreach ($ship->systems as $system){
							if ($system instanceof GrapplingClaw){							
								$system->eliteMarines = true;
							}
						}
						foreach ($ship->systems as $system){
							if ($system instanceof CombatTransporter){							
								$system->eliteMarines = true;
							}
						}
						break;	


					case 'EXT_MRN'://Extra marines, increase contingent per Claw by 1.
						foreach ($ship->systems as $system){
							if ($system instanceof GrapplingClaw){							
								$system->ammunition += $enhCount;
							}
						}
						foreach ($ship->systems as $system){
							if ($system instanceof CombatTransporter){							
								$system->ammunition += $enhCount;
							}
						}
						break;	

					case 'GUNSIGHT'://Split fire: allows Particle Repeaters to split their shots.
						foreach ($ship->systems as $system){
							if ($system instanceof ParticleRepeater){
								$damageTaken = $system->maxhealth - ($system->getRemainingHealth()); //Check for damge taken.
								if($damageTaken > 0) break; //Lose gunsights if even 1 point of damage taken.
								$system->specialHitChanceCalculation = true;
								$system->canSplitShots = true;
							}
						}
						break;

					case 'HBLAST_THR'://Improved Blaster Thrust: lower each Hypergraviton Blaster's boost cost to 4 thrust.
						foreach ($ship->systems as $system){
							if ($system instanceof HypergravitonBlaster){
								$system->setThrustPerBoost(4);
							}
						}
						break;

					case 'HANG_F'://Hangar Conversion of AS slot to (Heavy) Fighter slot.
						//Mirror of gamelobby.js shipProfile.slots tracking. Mutating
						//$ship->fighters lets universal-slot hangarAcceptsCategory
						//and downstream consumers see the post-conversion shape;
						//addShipEnhancementsForJSON emits the array to the client.
						//"normal" is a legacy alias for "heavy" — preserve whichever
						//key the ship file declared (gamelobby.js does the same via
						//lship.fighters["heavy"] || lship.fighters["normal"]).
						$ship->fighters["assault shuttles"] = max(0, ($ship->fighters["assault shuttles"] ?? 0) - $enhCount);
						$heavyKey = isset($ship->fighters["heavy"]) ? "heavy"
								  : (isset($ship->fighters["normal"]) ? "normal" : "heavy");
						$ship->fighters[$heavyKey] = ($ship->fighters[$heavyKey] ?? 0) + $enhCount;
						break;

					case 'HANG_AS'://Hangar Conversion of (Heavy/Medium) Fighter slot to Assault Shuttle slot.
						//Deduct from heavy/normal first, then medium for the
						//remainder — same order as gamelobby.js shipProfile.slots
						//tracking. "normal" is a legacy alias for "heavy" (see the
						//$keysToCheck list in HANG_AS registration above).
						$toDeduct = $enhCount;
						foreach (array("heavy", "normal") as $heavyKey) {
							if ($toDeduct <= 0) break;
							$avail = ($ship->fighters[$heavyKey] ?? 0);
							if ($avail <= 0) continue;
							$taken = min($toDeduct, $avail);
							$ship->fighters[$heavyKey] = $avail - $taken;
							$toDeduct -= $taken;
						}
						if ($toDeduct > 0) {
							$ship->fighters["medium"] = max(0, ($ship->fighters["medium"] ?? 0) - $toDeduct);
						}
						$ship->fighters["assault shuttles"] = ($ship->fighters["assault shuttles"] ?? 0) + $enhCount;
						break;

					case 'HANG_BP'://Convert default Shuttle slots to Breaching Pod slots
						//Default shuttles auto-populate leftover hangar capacity, so adding
						//to "Breaching Pods" implicitly steals from that leftover pool —
						//no explicit "shuttles" decrement needed.
						$ship->fighters["Breaching Pods"] = ($ship->fighters["Breaching Pods"] ?? 0) + $enhCount;
						break;

					case 'HANG_MSW'://Convert default Shuttle units to Minesweeping Shuttle units
						//Does NOT change $ship->fighters — minesweeping shuttles still
						//count as default shuttle capacity for fleet-check purposes.
						//Instead, HangarOps::populateInitialHangarUsage reads HANG_MSW
						//count directly and splits leftover-capacity auto-population
						//between MinesweepingShuttle and regular Shuttle records.
						break;

					case 'HANG_ORD'://Stage 15 — Extra Ordnance Reserve (carrier-level reload pool)
						//No $ship mutation needed. Pool capacity is re-derived on every
						//load by HangarOps::reloadPoolCapacity reading this enhancement
						//count directly; spent total persists via the hangarOrdReserve
						//note on the primary hangar.
						break;

					case 'MAR_CONT'://Stage 17 ext — Extra Marine Contingents (ship-level marine pool)
						//No $ship mutation needed. Pool capacity is re-derived on every
						//load by HangarOps::marinePoolCapacity reading this enhancement
						//count directly; spent total persists via the hangarMarineReserve
						//note on the primary hangar. Drawn from by Marines::whileDocked.
						break;
												
					case 'IFF_SYS': //Add IFF system for Mine Launcher ships.
						//Mark true
						$ship->setIFFSystem();
						break;

					case 'IMPR_ENG': //Improved Engine: +1 Engine output (strongest Engine), may be taken multiple times
						$strongestSystem = null;
						$strongestValue = -1;	  
						foreach ($ship->systems as $system){
							if ($system instanceof Engine){
								if($system->output > $strongestValue) {
									$strongestValue = $system->output;
									$strongestSystem = $system;
								}
							}
						}  
						if($strongestValue > 0){ //Engine actually exists to be enhanced!
							$strongestSystem->output += $enhCount;
						}
						break;

					case 'IMPR_PSY': //Improved Psychic Field
						foreach ($ship->systems as $system){
							if ($system instanceof PsychicField){
								$system->range += $enhCount;
							}
						}  
						break;

					case 'IMPR_REA': //Improved Reactor: more power output (depending on ship size
						$strongestSystem = null;
						$strongestValue = -1;	  
						foreach ($ship->systems as $system){
							if ($system instanceof Reactor){
								if($system->maxhealth > $strongestValue) {
									$strongestValue = $system->maxhealth;
									$strongestSystem = $system;
								}
							}
						}  
						if($strongestValue > 0){ //Reactor actually exists to be enhanced!
							$addedPower = 0;
							if($ship->Enormous == true){
							  $addedPower = 4;
							}else{
							  $addedPower = $ship->shipSizeClass; //+1 for MCV, +2 for HCV, +3 for Capital
							}		  
							$strongestSystem->output += $enhCount*$addedPower;
						}
						break;		
						
					case 'IMPR_SENS': //Improved Scanner: +1 Scanner output (strongest Scanner)
						$strongestSystem = null;
						$strongestValue = -1;	  
						foreach ($ship->systems as $system){
							if ($system instanceof Scanner){
								if($system->output > $strongestValue) {
									$strongestValue = $system->output;
									$strongestSystem = $system;
								}
							}
						}  
						if($strongestValue > 0){ //Scanner actually exists to be enhanced!
							$strongestSystem->output += $enhCount;
						}
						break;					

					case 'IMPR_SR': //Improved Self Repair: +1 Output for each Self Repair
						foreach ($ship->systems as $system){
							if ($system instanceof SelfRepair){
								$system->output += $enhCount;
							}
						}  
						break;	

					case 'IMPR_THSD': //Improved Thirdspace Shield: +1 rating for each Thought Shield
						foreach ($ship->systems as $system){							
							if ($system instanceof ThirdspaceShield){
								$system->baseRating += $enhCount;
    							if($ship->shipSizeClass == 3){
    								$system->maxhealth += $enhCount * 3;
								}else{
    								$system->maxhealth += $enhCount;									
								}	
							}
							if ($system instanceof ThirdspaceShieldGenerator){
								$system->output += $enhCount;
							}							
						}																	 
						break;	
						
					case 'IMPR_TS': //Improved Thought Shield: +1 rating for each Thought Shield
						foreach ($ship->systems as $system){							
							if ($system instanceof ThoughtShield){
								$system->baseRating += $enhCount;
    							$system->maxhealth += $enhCount * 2;
							}
							if ($system instanceof ThoughtShieldGenerator){
								$system->output += $enhCount;
							}							
						}																	 
						break;	
						
					case 'IPSH_EETH': //Ipsha Eethan Barony refit: +2 free thrust, +25% available power (round arithmetically), +0.1 turn delay, -5 Initiative, +4 critical roll modifier for Reactor and Engine
						//fixed values:
						$ship->iniativebonus -= $enhCount*5;
						$ship->turndelaycost += 0.1;
						//system mods: 
						foreach ($ship->systems as $system){
							if ($system instanceof MagGravReactor){ //Reactor - tailored for Mag-Gravitic
								$system->output = round($system->output*1.25);//+25%
								$system->critRollMod += 4;
							}else if ($system instanceof Engine){ //Engine
								$system->output = $system->output +2;
								$system->critRollMod += 4;
							}
						}  	
						break;			
						
						
					case 'IPSH_ESSAN': //Ipsha Essan Barony refit: +1 Engine (and +2 boxes), -1 Sensors (and -2 boxes), +1 structural armor (no higher than 5)
						//system mods: 
						foreach ($ship->systems as $system){
							if ($system instanceof Scanner){ //Scanner
								$system->output = $system->output -1;
								$system->maxhealth = $system->maxhealth -2;
							}else if ($system instanceof Engine){ //Engine
								$system->output = $system->output +1;
								$system->maxhealth = $system->maxhealth +2;
							}else if ($system instanceof Structure){ //Structure block
								if ($system->armour<5) $system->armour = $system->armour + 1;
							}
						}  	
						break;			

					case 'MARK_FERV': //Markab Religious Fervor: +1 to hit all weapons, +10 Initiative, +2 Defence Profiles
							$ship->toHitBonus += $enhCount;
							$ship->iniativebonus += $enhCount*10;
							$ship->forwardDefense += $enhCount*2;
							$ship->sideDefense += $enhCount*2;
					break;

					case 'MINE_ACC': //Improved Accuracy for Mines
						foreach ($ship->systems as $system){
							if ($system instanceof Weapon){
								if($system->fireControl[0] !== null) $system->fireControl[0] += $enhCount;
								if($system->fireControl[1] !== null) $system->fireControl[1] += $enhCount;
								if($system->fireControl[2] !== null) $system->fireControl[2] += $enhCount;							
							}
						}								

					break;								

					case 'MINE_ARM': //Improved Armour for Mines
						foreach ($ship->systems as $system) {
							if ($system instanceof Structure) {
								$system->armour += $enhCount;
							}
						}
						
					break;

					case 'MINE_CTRL': //Command Controller for Mines
						//Mark true
						$ship->setCommandControl(true);
						foreach ($ship->systems as $system){
							if ($system instanceof Weapon){
								$system->autoFireOnly = false;
								$system->canOffLine = true;
								$system->canTargetAll = true;	
								if($system instanceof ProximityMine) {
									$system->preFires = true;
									$system->hextarget = false;									
								}	
							}
						}	

						break;	

					case 'MINE_DMG': //Extra Damage for Mines
					foreach ($ship->systems as $system){
						if ($system instanceof ProximityMine || $system instanceof CaptorMine){
							$system->addToDamageBonus($enhCount);
							$system->setMinDamage();
							$system->setMaxDamage();
						}
					}								

					break;	

					case 'MINE_RANG': //Improved Range for Mines
						foreach ($ship->systems as $system){
							if ($system instanceof Weapon){
								$system->range += $enhCount;
								foreach($system->rangeArray as $mode => $val) {
									$system->rangeArray[$mode] += $enhCount;
								}
							}
						}								

					break;						

					case 'MINE_SIGN': //Improved Signature for Mines
						//Mark true
						$ship->signature += $enhCount;
						if($ship->mineType == 'DEW') $ship->detectedSignature += $enhCount;
						break;								

					case 'POOR_CREW': //Poor Crew: -1 Engine, -1 Sensors, -1 Reactor power, +1 Profile, +2 to critical results, -5 Initiative, -1 to hit all weapons
						//fixed values
						$ship->forwardDefense += $enhCount;
						$ship->sideDefense += $enhCount;
						$ship->iniativebonus -= $enhCount*5;
						$ship->critRollMod += $enhCount*2;
						$ship->toHitBonus -= $enhCount;								
						
						//system mods: Scanner						
						$strongestSystem = null;
						$strongestValue = -1;	  
						foreach ($ship->systems as $system){
							if ($system instanceof Scanner){
								if($system->output > $strongestValue) {
									$strongestValue = $system->output;
									$strongestSystem = $system;
								}
							}
						}  
						if($strongestValue > 0){ //Scanner actually exists to be enhanced!
							$strongestSystem->output -= $enhCount;
						}
						//system mods: Engine	
						$strongestSystem = null;
						$strongestValue = -1;	  
						foreach ($ship->systems as $system){
							if ($system instanceof Engine){
								if($system->output > $strongestValue) {
									$strongestValue = $system->output;
									$strongestSystem = $system;
								}
							}
						}  
						if($strongestValue > 0){ //Engine actually exists to be enhanced!
							$strongestSystem->output -= $enhCount*2;
						}
						//system mods: Reactor (here I assume main reactor is the biggest one! - ot the strongest, as eg. any malus would be on main reactor as well)
						$strongestSystem = null;
						$strongestValue = -1000;
						foreach ($ship->systems as $system){
							if ($system instanceof Reactor){
								if($system->maxhealth > $strongestValue) {
									$strongestValue = $system->maxhealth;
									$strongestSystem = $system;
								}
							}
						}  
						if($strongestSystem != null){ //Reactor actually exists to be enhanced! although it has to ;)
							$strongestSystem->output -= $enhCount;
						}						
						break;
						
					case 'MINE_MULTI': //Flexible Targeting for Mines
						$ship->multiSettings = true;
						break;						

					case 'SHAD_DIFF': //Increased Diffuser Capability: +1 Output for each Diffuser
						foreach ($ship->systems as $system){
							if ($system instanceof EnergyDiffuser){
								$system->output += $enhCount;
							}
						}  
						break;
						
					case 'SHAD_FTRL': //Shadow fighter launched: -1 Structure point for each launched fighter
						//Stage S: integrated-fighter (ShadowHangar) ships do NOT statically
						//shave Structure — a formed fighter dynamically MARKS a box (freed on
						//recovery), and the bought count instead seeds the bay's initial
						//hangarUsage (HangarOps::populateInitialHangarUsage). Only the LEGACY
						//plain-Hangar Shadow ships keep the static -1 maxhealth per count.
						if (!HangarOps::shipHasShadowHangar($ship)) {
							$struct = $ship->getStructureSystem(0);
							if($struct){
								//$struct->maxhealth -= $enhCount;
							}
						}
						break;
						
					case 'SHAD_TEND': //Extra Tendrils
						//The tendrils themselves were mounted much earlier, by
						//Enhancements::addEnhancementSystems from DBManager::getTacShips - see the note
						//there on why they cannot wait until now. Nothing left to apply here; just
						//report what was bought in ABSORPTION rather than in levels of five.
						if($ship->enhancementTooltip != "") $ship->enhancementTooltip .= "<br>";
						$ship->enhancementTooltip .= "Extra Tendrils: " . ($enhCount*5) . " Capacity";
						break;

					case 'SPARK_CURT': //Spark Curtain - direct effect is setting $output=$baseOutput for every Spark Field on board
						foreach ($ship->systems as $system){
							if ($system instanceof SparkField){
								$system->output = $system->baseOutput;
							}
						}  
						break;
						
					case 'SLUGGISH': //Sluggish: -1(5) Initiative
						//fixed values
						$ship->iniativebonus -= $enhCount*5;
						break;
						
					case 'VOR_AMETHS': //Vorlon Amethyst Skin (for ship). +1 Adaptive Armor point, AA maximum and available for pre-assignment increased for every even total.
						$AActrl = $ship->getSystemByName("AdaptiveArmorController");
						  if($AActrl){
							  $AActrl->AAtotal += $enhCount;
							  $AActrl->output = $AActrl->AAtotal;
							  $AActrl->AApertype = floor($AActrl->AAtotal/2); //Vorlon standard
							  $AActrl->AApreallocated = floor($AActrl->AAtotal/2); //Vorlon standard
						  }
						  break;
						  
					case 'VOR_AZURS': //Vorlon Azure Skin (for ship). +1 Shield rating					 
						foreach ($ship->systems as $system){
							if ($system instanceof EMShield){
								$system->output += $enhCount;
							}
						}
						break;
						  
					case 'VOR_CRIMS': // Vorlon Crimson Skin (for ship). Power Capacitor gains +2 storage points and +1 recharge point.		
						  $capacitor = $ship->getSystemByName("PowerCapacitor");//find Power Capacitor
						  if($capacitor){
							  $capacitor->output += $enhCount;
							  $capacitor->capacityBonus = 2*$enhCount;
						  }
						  break;
		  
					case 'VULN_CRIT': //Vulnerable to Criticals: +1 Crit roll mod
						//fixed values
						$ship->critRollMod += $enhCount;
						break;
						
						
					//consumable ammunition
					case 'AMMO_B': //Basic Missile						
						if($ammoMagazine) $ammoMagazine->addAmmoEntry(new AmmoMissileB(), $enhCount, true); //do notify dependent weapons, too!
						break;
					case 'AMMO_L': //Long Range Missile
						if($ammoMagazine) $ammoMagazine->addAmmoEntry(new AmmoMissileL(), $enhCount, true); //do notify dependent weapons, too!
						break;
					case 'AMMO_H': //Heavy Missile
						if($ammoMagazine) $ammoMagazine->addAmmoEntry(new AmmoMissileH(), $enhCount, true); //do notify dependent weapons, too!
						break;
					case 'AMMO_F': //Flash Missile
						if($ammoMagazine) $ammoMagazine->addAmmoEntry(new AmmoMissileF(), $enhCount, true); //do notify dependent weapons, too!
						break;
					case 'AMMO_A': //Antifighter Missile
						if($ammoMagazine) $ammoMagazine->addAmmoEntry(new AmmoMissileA(), $enhCount, true); //do notify dependent weapons, too!
						break;
					case 'AMMO_P': //Piercing Missile
						if($ammoMagazine) $ammoMagazine->addAmmoEntry(new AmmoMissileP(), $enhCount, true); //do notify dependent weapons, too!
						break;
					case 'AMMO_D': //Light Missile						
						if($ammoMagazine) $ammoMagazine->addAmmoEntry(new AmmoMissileD(), $enhCount, true); //do notify dependent weapons, too!
						break;						
					case 'AMMO_I': //Interceptor Missile 						
						if($ammoMagazine) $ammoMagazine->addAmmoEntry(new AmmoMissileI(), $enhCount, true); //do notify dependent weapons, too!
						break;
					case 'AMMO_C': //Chaff Missile						
						if($ammoMagazine) $ammoMagazine->addAmmoEntry(new AmmoMissileC(), $enhCount, true); //do notify dependent weapons, too!
						break;	
					case 'AMMO_J': //Jammer Missile						
						if($ammoMagazine) $ammoMagazine->addAmmoEntry(new AmmoMissileJ(), $enhCount, true); //do notify dependent weapons, too!
						break;
					case 'AMMO_K': //Starburst Missile						
						if($ammoMagazine) $ammoMagazine->addAmmoEntry(new AmmoMissileK(), $enhCount, true); //do notify dependent weapons, too!
						break;						
					case 'AMMO_M': //Multiwarhead Missile						
						if($ammoMagazine) $ammoMagazine->addAmmoEntry(new AmmoMissileM(), $enhCount, true); //do notify dependent weapons, too!
						break;
					case 'AMMO_KK': //Kinetic Missile						
						if($ammoMagazine) $ammoMagazine->addAmmoEntry(new AmmoMissileKK(), $enhCount, true); //do notify dependent weapons, too!
						break;
					case 'AMMO_S': //Stealth Missile						
						if($ammoMagazine) $ammoMagazine->addAmmoEntry(new AmmoMissileS(), $enhCount, true); //do notify dependent weapons, too!
						break;						
					case 'AMMO_X': //HARM Missile						
						if($ammoMagazine) $ammoMagazine->addAmmoEntry(new AmmoMissileX(), $enhCount, true); //do notify dependent weapons, too!
						break;
					case 'AMMO_Z': //Antimine Missile						
						if($ammoMagazine) $ammoMagazine->addAmmoEntry(new AmmoMissileZ(), $enhCount, true); //do notify dependent weapons, too!
						break;								
					case 'MINE_BLB': //Ballistic Launcher Basic Mine						
						if($ammoMagazine) $ammoMagazine->addAmmoEntry(new AmmoBLMineB(), $enhCount, true); //do notify dependent weapons, too!
						break;
					case 'MINE_BLH': //Ballistic Launcher Heavy Mine						
						if($ammoMagazine) $ammoMagazine->addAmmoEntry(new AmmoBLMineH(), $enhCount, true); //do notify dependent weapons, too!
						break;
					case 'MINE_BLW': //Ballistic Launcher Wide-Range Mine						
						if($ammoMagazine) $ammoMagazine->addAmmoEntry(new AmmoBLMineW(), $enhCount, true); //do notify dependent weapons, too!
						break;
					case 'MINE_MLB': //Abbai Mine Launcher Basic Mine													
						if($ammoMagazine) $ammoMagazine->addAmmoEntry(new AmmoBistifA(), $enhCount, true); //do notify dependent weapons, too!
						break;	
					case 'MINE_MLW': //Abbai Mine Launcher Wide-Ranged Mine						
						if($ammoMagazine) $ammoMagazine->addAmmoEntry(new AmmoBistifB(), $enhCount, true); //do notify dependent weapons, too!
						break;
					case 'MINE_AML': //Chouka Mine Launcher Vedas-A Mine -- GTS												
						if($ammoMagazine) $ammoMagazine->addAmmoEntry(new AmmoVedasA(), $enhCount, true); //do notify dependent weapons, too!
						break;	
					case 'MINE_BML': //Chouka Mine Launcher Vedas-B Mine -- GTS												
						if($ammoMagazine) $ammoMagazine->addAmmoEntry(new AmmoVedasB(), $enhCount, true); //do notify dependent weapons, too!
						break;	
					case 'MINE_CML': //Chouka Mine Launcher Vedas-C Mine -- GTS												
						if($ammoMagazine) $ammoMagazine->addAmmoEntry(new AmmoVedasC(), $enhCount, true); //do notify dependent weapons, too!
						break;	
						
					//AMMO TYPES FOR DIRECT FIRE WEAPONS					
					case 'SHELL_HBSC': //Standard Ammo for Heavy Railgun						
						if($ammoMagazine) $ammoMagazine->addAmmoEntry(new AmmoHShellBasic(), $enhCount, true); //do notify dependent weapons, too!
						break;							
					case 'SHELL_MBSC': //Standard Ammo for Medium Railgun						
						if($ammoMagazine) $ammoMagazine->addAmmoEntry(new AmmoMShellBasic(), $enhCount, true); //do notify dependent weapons, too!
						break;								
					case 'SHELL_LBSC': //Standard Ammo for Light Railgun						
						if($ammoMagazine) $ammoMagazine->addAmmoEntry(new AmmoLShellBasic(), $enhCount, true); //do notify dependent weapons, too!
						break;								
					case 'SHELL_HFLH': //Flash Ammo for Heavy Railgun						
						if($ammoMagazine) $ammoMagazine->addAmmoEntry(new AmmoHShellFlash(), $enhCount, true); //do notify dependent weapons, too!
						break;							
					case 'SHELL_MFLH': //Flash Ammo for Medium Railgun						
						if($ammoMagazine) $ammoMagazine->addAmmoEntry(new AmmoMShellFlash(), $enhCount, true); //do notify dependent weapons, too!
						break;								
					case 'SHELL_LFLH': //Flash Ammo for Light Railgun						
						if($ammoMagazine) $ammoMagazine->addAmmoEntry(new AmmoLShellFlash(), $enhCount, true); //do notify dependent weapons, too!
						break;							
					case 'SHELL_HSCT': //Scatter Ammo for Heavy Railgun						
						if($ammoMagazine) $ammoMagazine->addAmmoEntry(new AmmoHShellScatter(), $enhCount, true); //do notify dependent weapons, too!
						break;	
					case 'SHELL_MSCT': //Scatter Ammo for Medium Railgun						
						if($ammoMagazine) $ammoMagazine->addAmmoEntry(new AmmoMShellScatter(), $enhCount, true); //do notify dependent weapons, too!
						break;																												case 'SHELL_LSCT': //Scatter Ammo for Light Railgun						
						if($ammoMagazine) $ammoMagazine->addAmmoEntry(new AmmoLShellScatter(), $enhCount, true); //do notify dependent weapons, too!
						break;	
					case 'SHELL_HHVY': //Heavy Ammo for Heavy Railgun						
						if($ammoMagazine) $ammoMagazine->addAmmoEntry(new AmmoHShellHeavy(), $enhCount, true); //do notify dependent weapons, too!
						break;	
					case 'SHELL_MHVY': //Heavy Ammo for Medium Railgun						
						if($ammoMagazine) $ammoMagazine->addAmmoEntry(new AmmoMShellHeavy(), $enhCount, true); //do notify dependent weapons, too!
						break;	
					case 'SHELL_LHVY': //Heavy Ammo for Light Railgun						
						if($ammoMagazine) $ammoMagazine->addAmmoEntry(new AmmoLShellHeavy(), $enhCount, true); //do notify dependent weapons, too!
						break;																												case 'SHELL_HLR': //Long Range Ammo for Heavy Railgun						
						if($ammoMagazine) $ammoMagazine->addAmmoEntry(new AmmoHShellLRange(), $enhCount, true); //do notify dependent weapons, too!
						break;	
					case 'SHELL_MLR': //Long Range Ammo for Medium Railgun						
						if($ammoMagazine) $ammoMagazine->addAmmoEntry(new AmmoMShellLRange(), $enhCount, true); //do notify dependent weapons, too!
						break;
					case 'SHELL_HULR': //Long Range Ammo for Heavy Railgun						
						if($ammoMagazine) $ammoMagazine->addAmmoEntry(new AmmoHShellULRange(), $enhCount, true); //do notify dependent weapons, too!
						break;							
				}
			}
			
		}
	   }//endof function setEnhancementsShip
	   	  
		  
		/*modifies data for stripForJSON method of fighter flight
	     - for modifications that do require such additional modification (most do not! Essentially ship attributes do, system modifications do not or are handled separately)
	   */
	   private static function addFighterEnhancementsForJSON($ship, $strippedShip){
		   	foreach($ship->enhancementOptions as $entry){ //ID,readableName,numberTaken,limit,price,priceStep
				$enhID = $entry[0];
				$enhCount = $entry[2];
				$enhDescription = $entry[1];
				if($enhCount > 0) {
					switch ($enhID) {
						
						case 'DEPLOY': //Delayed Deployment marker
							//if($ship instanceof FighterFlight){
								//$strippedShip->deploysOnTurn = $ship->deploysOnTurn;
							//}
							break;
						case 'ELITE_SW': //Elite Pilot: modify pivot cost, OB, profile and Initiative
							if($ship instanceof FighterFlight){
								$strippedShip->pivotcost = $ship->pivotcost;
								$strippedShip->offensivebonus = $ship->offensivebonus;
								$strippedShip->iniativebonus = $ship->iniativebonus;
								$strippedShip->forwardDefense = $ship->forwardDefense;
								$strippedShip->sideDefense = $ship->sideDefense;
							}
							break;

						case 'FTR_FERV': //Markab Religious Fervor: OB, Initiative and Profiles modified
							if($ship instanceof FighterFlight){
								$strippedShip->offensivebonus = $ship->offensivebonus;
								$strippedShip->iniativebonus = $ship->iniativebonus;
								$strippedShip->forwardDefense = $ship->forwardDefense;
								$strippedShip->sideDefense = $ship->sideDefense;
							}
							break;
														
						case 'EXP_MOTIV': //Expert Motivator: modify dropout modifier
							/* actually irrelevant for front end
							if($ship instanceof FighterFlight){
								$strippedShip->critRollMod = $ship->critRollMod;
							}
							*/
							break;
							
						case 'IMPR_OB': //Improved Targeting Computer: modify offensive bonus 
							if($ship instanceof FighterFlight){
								$strippedShip->offensivebonus = $ship->offensivebonus;
							}
							break;
													
						case 'IMPR_THR': //Improved Thrust: modify free thrust
							if($ship instanceof FighterFlight){
								$strippedShip->freethrust = $ship->freethrust;
							}
							break;

						case 'MAKE_MINE': //Minesweeper Conversion: emit minesweeper flag so the
										  //client (ew.js / ShipInfo / SystemInfo) reads full-OB mine detection and improved OB.
										  //Static blueprint says minesweeper=false, so the strip must override it.
							if($ship instanceof FighterFlight){
								$strippedShip->minesweeper = $ship->minesweeper;
								$strippedShip->offensivebonus = $ship->offensivebonus;								
							}
							break;

						case 'NAVIGATOR': //Navigator: hasNavigator trait
							if($ship instanceof FighterFlight){
								$strippedShip->hasNavigator = $ship->hasNavigator;
							}
							break;		
									
						case 'POOR_TRAIN': //Poor Training: modify Initiative, Free Thrust, OB, Profile
							if($ship instanceof FighterFlight){
								$strippedShip->freethrust = $ship->freethrust;
								$strippedShip->offensivebonus = $ship->offensivebonus;
								$strippedShip->iniativebonus = $ship->iniativebonus;
								$strippedShip->forwardDefense = $ship->forwardDefense;
								$strippedShip->sideDefense = $ship->sideDefense;
							}
							break;
							
						case 'SHAD_CTRL': //Uncontrolled Shadow fighter: modify Initiative and OB
							if($ship instanceof FighterFlight){
								$strippedShip->offensivebonus = $ship->offensivebonus;
								$strippedShip->iniativebonus = $ship->iniativebonus;
							}
							break;
					}					
				}
		    }
			return $strippedShip;
	   }//endof function addShipEnhancementsForJSON
		  
		  
 
		/*modifies data for stripForJSON method of ship 
	     - for modifications that do require such additional modification (most do not! Essentially ship attributes do, system modifications do not or are handled separately)
	   */
	   private static function addShipEnhancementsForJSON($ship, $strippedShip){
		   	foreach($ship->enhancementOptions as $entry){ //ID,readableName,numberTaken,limit,price,priceStep
				$enhID = $entry[0];
				$enhCount = $entry[2];
				$enhDescription = $entry[1];
				if($enhCount > 0) {
					switch ($enhID) {

						case 'DEPLOY': //Delayed Deployment marker
							//$strippedShip->deploysOnTurn = $ship->deploysOnTurn;
							break;

						case 'ELITE_CREW': //Elite crew: Initiative and Profiles modified
							$strippedShip->forwardDefense = $ship->forwardDefense;
							$strippedShip->sideDefense = $ship->sideDefense;
							$strippedShip->iniativebonus = $ship->iniativebonus;
							$strippedShip->toHitBonus = $ship->toHitBonus;	///Just in case needed on Front End.							
							break;

						case 'MARK_FERV': //Markab Religious Fervor: Initiative and Profiles modified
							$strippedShip->forwardDefense = $ship->forwardDefense;
							$strippedShip->sideDefense = $ship->sideDefense;
							$strippedShip->iniativebonus = $ship->iniativebonus;
							$strippedShip->toHitBonus = $ship->toHitBonus;	///Just in case needed on Front End.					
							break;

						case 'MINE_SIGN': //Improved signature for mines
							$strippedShip->signature = $ship->signature;
							$strippedShip->detectedSignature = $ship->detectedSignature;							
							break;								

						case 'IPSH_EETH': //Ipsha Eethan Barony refit: +2 free thrust, +25% available power (round up), +0.1 turn delay, -5 Initiative, +4 critical roll modifier for Reactor and Engine
							$strippedShip->iniativebonus = $ship->iniativebonus;
							$strippedShip->turndelaycost = $ship->turndelaycost;
							break;			
							
						case 'POOR_CREW': //Poor crew: Initiative and Profiles modified
							$strippedShip->forwardDefense = $ship->forwardDefense;
							$strippedShip->sideDefense = $ship->sideDefense;
							$strippedShip->iniativebonus = $ship->iniativebonus;
							$strippedShip->toHitBonus = $ship->toHitBonus;	///Just in case needed on Front End.								
							break;							
						
						case 'SLUGGISH': //Sluggish: Initiative  modified
							$strippedShip->iniativebonus = $ship->iniativebonus;
							break;

						case 'HANG_F':  //AS slot → Heavy fighter slot
						case 'HANG_AS': //Heavy/Medium fighter slot → AS slot
						case 'HANG_BP': //Default shuttle slot → Breaching Pod slot
							//stripForJson omits $ship->fighters (client normally pulls
							//it from window.staticShips), so universal-hangar
							//hangarAcceptsCategory checks on the client would miss
							//these conversions. Emit the post-enhancement array so
							//the client sees the same shape the server uses.
							$strippedShip->fighters = $ship->fighters;
							break;

					}
				}
		    }
			return $strippedShip;
	   }//endof function addShipEnhancementsForJSON
	   
	   
	  /*modifies data for stripForJSON method of unit - splitting into separate methods for fighter and ship
	  */
	  public static function addUnitEnhancementsForJSON($ship, $strippedShip){
		if($ship instanceof FighterFlight){
			$strippedShip = Enhancements::addFighterEnhancementsForJSON($ship, $strippedShip);
		}else{
			$strippedShip = Enhancements::addShipEnhancementsForJSON($ship, $strippedShip);
		}	
		return $strippedShip;
	  } //endof function addUnitEnhancementsForJSON
		  
	   /*modifies data for stripForJSON method of ship system 
	     - for modifications that do require such additional modification (most do not!)
	   */
	   public static function addSystemEnhancementsForJSON($ship, $system, $strippedSystem ){
		   	/* PER-SYSTEM enhancements first, and it early-exits on an empty array before doing
		   	   anything else: this whole method runs for EVERY system of EVERY ship on EVERY load
		   	   (ShipSystem::stripForJson), so the new track must cost one empty() on the hot path
		   	   and nothing more (D3). */
		   	$strippedSystem = Enhancements::addSystemEnhancementsOwnForJSON($ship, $system, $strippedSystem);

		   	foreach($ship->enhancementOptions as $entry){ //ID,readableName,numberTaken,limit,price,priceStep
				$enhID = $entry[0];
				$enhCount = $entry[2];
				$enhDescription = $entry[1];
				if($enhCount > 0) {					
					switch ($enhID) {	
						case 'ELITE_CREW': //Elite Crew modifies thrusters' ratings!
							if($system instanceof Thruster){
								$strippedSystem->output = $system->output;
							}
							break;
						case 'GUNSIGHT': //improved Thought Shield
							if ($system instanceof ParticleRepeater){
								$strippedSystem->canSplitShots = $system->canSplitShots ;
								$strippedSystem->specialHitChanceCalculation = $system->specialHitChanceCalculation ;						
							}													
							break;								
						case 'IMPR_PSY': //Spark Curtain - affects output of Spark Field
							if($system instanceof PsychicField){
								$strippedSystem->range = $system->range;
							}
							break;
						case 'IMPR_SR': //improved self repair: modifies output of Self Repair
							if ($system instanceof SelfRepair){ //SelfRepair
								$strippedSystem->output = $system->output ;
								$strippedSystem->critRollMod = $system->critRollMod ;
							}
							break;		
						case 'IMPR_THSD': //improved Thirdspace Shield
							if ($system instanceof ThirdspaceShield){
								$strippedSystem->maxhealth = $system->maxhealth ;
							}													
							break;	
						case 'IMPR_TS': //improved Thought Shield
							if ($system instanceof ThoughtShield){
								$strippedSystem->maxhealth = $system->maxhealth ;
							}													
							break;	
							
						case 'IPSH_EETH': //modifies output and crit mod of Engine and Reactor
							if ($system instanceof MagGravReactor){ //Reactor
								$strippedSystem->output = $system->output ;
								$strippedSystem->critRollMod = $system->critRollMod ;
							}else if ($system instanceof Engine){ //Engine
								$strippedSystem->output = $system->output ;
								$strippedSystem->critRollMod = $system->critRollMod ;
							}
							break;
							
							
						foreach ($ship->systems as $system){
							if ($system instanceof MagGravReactor){ //Reactor - tailored for Mag-Gravitic
								$system->output = ceil($system->output*1.25);//+25%
								$system->critRollMod += 4;
							}else if ($system instanceof Engine){ //Engine
								$system->output = $system->output +2;
								$system->critRollMod += 4;
							}
						}  	

						case 'IPSH_ESSAN': //modifies output and structure of Engine and Sensor, and armor of Structure
							if ($system instanceof Scanner){ //Scanner
								$strippedSystem->output = $system->output ;
								$strippedSystem->maxhealth = $system->maxhealth ;
							}else if ($system instanceof Engine){ //Engine
								$strippedSystem->output = $system->output ;
								$strippedSystem->maxhealth = $system->maxhealth ;
							}else if ($system instanceof Structure){ //Structure block
								$strippedSystem->armour = $system->armour ;
							}
							break;							
					
						case 'MINE_ARM':
							if ($system instanceof Structure) { //Improved Armour for Mines.
								$strippedSystem->armour = $system->armour;
							}						
						break;

						case 'MINE_DMG':
							if ($system instanceof Weapon) { //Improved Damage for Mines.
								$strippedSystem->minDamage = $system->minDamage;
								$strippedSystem->maxDamage = $system->maxDamage;
								$strippedSystem->minDamageArray = $system->minDamageArray;
								$strippedSystem->maxDamageArray = $system->maxDamageArray;
							}						
						break;
						
						case 'MINE_RANG':
							if ($system instanceof Weapon) { //Improved Range for Mines.
								$strippedSystem->range = $system->range;                                     
								$strippedSystem->rangeArray = $system->rangeArray;
							}						
						break;								

						case 'SHAD_DIFF': //Increased Diffuser Capability: +1 Output for each Diffuser
							if ($system instanceof EnergyDiffuser) { 
								$strippedSystem->output = $system->output;
							}
							break;	
							

						case 'SHAD_FTRL': //Shadow fighter launched / integrated fighters
							//Structure maxhealth is now sent UNCONDITIONALLY by Structure::stripForJson
							//(baseSystems.php) — the canonical, enhancement-agnostic place to keep the
							//client's live maxhealth in sync after an integrated-fighter structure-box
							//loss. Re-sending it here too would be redundant, so this case is a no-op.
							break;

						case 'SPARK_CURT': //Spark Curtain - affects output of Spark Field
							if($system instanceof SparkField){
								$strippedSystem->output = $system->output;
							}
							break;
							

						case 'VOR_AMETHS': //Adaptive Armor Controller properties are affected
							if ($system instanceof AdaptiveArmorController) { 
								$strippedSystem->AAtotal = $system->AAtotal;
								$strippedSystem->AApertype = $system->AApertype;
								$strippedSystem->AApreallocated = $system->AApreallocated;
								$strippedSystem->output = $system->output;
							}
							break;	

						case 'VOR_AZURF':// Vorlon Azure Skin Coloring (for fighter): this is applied at the level of individual subsystem (shield emitter)...
							if ($system instanceof FtrShield){
								$strippedSystem->output = $system->output;
							}
							break;
							
						case 'VOR_AZURS': // Vorlon Azure Skin (for ship). Sfields gain +1 Rating.
							if ($system instanceof EMShield) { 
								$strippedSystem->output = $system->output;
							}
							break;	
							
						case 'VOR_CRIMS': // Vorlon Crimson Skin (for ship). Power Capacitor gains +2 storage points and +1 recharge point.
							if ($system instanceof PowerCapacitor) { 
								$strippedSystem->output = $system->output;
								$strippedSystem->capacityBonus = $system->capacityBonus;
							}
							break;							
							
					}					
				}
		    }			
			return $strippedSystem;
	   }//endof function addEnhancementsForJSON


	/* ==========================================================================================
	   PER-SYSTEM ENHANCEMENTS  -  WEAPON_ENHANCEMENTS_PLAN.md
	   ==========================================================================================

	   Five refits attached to ONE SYSTEM rather than to the whole ship, offered on young-tech
	   hulls (factionAge <= 2). Everything below is a PARALLEL TRACK beside the ship-level
	   machinery above, deliberately kept BESIDE it rather than folded INTO it:

	     - a separate $ship->systemEnhancements array, NOT index 8 on enhancementOptions (D2):
	       confirm.js renders one buy-dialog row per enhancementOptions INDEX and
	       gamedata.readBulkPurchase walks .selectAmount.shpenh<N> by that same index UNTIL THE
	       FIRST GAP, so filtering per-system entries out of the dialog would make the indices
	       sparse and silently zero every enhancement after the gap;
	     - separate tables (D1), so no existing query, replay or deploy path moves;
	     - a separate cost bucket, $ship->pointCostSysEnh (D5).

	   ⚠️ Do NOT refactor the ship-level switch blocks above onto this registry. They work, and
	   the churn buys nothing (D12).
	   ========================================================================================== */

	/* D3 - mirrors $offerChoiceLists above, for the same reason. Only the LOBBY needs "what may
	   be bought"; in game only the BOUGHT rows matter and they come from the DB. The in-game
	   blueprint path (ShipLoader::getShipsByClass, run on every game.php load) switches this off.
	   Default TRUE for the same reason as $offerChoiceLists: a caller that forgets to disable it
	   pays measurable time, whereas a caller that wrongly got false shows an empty menu, which is
	   silent. */
	public static $offerSystemEnhancements = true;

	/* ⭐ THE definition of every SYSTEM-level enhancement (D12). Adding one is a single literal;
	   nothing else in this file grows a `case`.

	   eligible / price / limit / apply  are the names of static methods below, dispatched through
	                                     self::regCall(). Keeping them here rather than in four
	                                     switch statements is what stops the "added it in three
	                                     places out of four" bug.
	   serialise                         the stripped-payload fields addSystemEnhancementsOwnForJSON
	                                     must re-send. Naming them HERE rather than in a fourth
	                                     switch is what stops the "applied but not sent" class of
	                                     bug, which is invisible until someone reads a tooltip.
	                                     ⚠️ §12 (per-viewer masking) splits this into serialise +
	                                     serialiseOwnerOnly - one line per entry, BECAUSE it is a
	                                     registry. */
	private static $systemEnhancementRegistry = array(
		/* ⚠️ `data` is on these two and on no others, and it is not decoration. The ship window's
		   SystemInfo tooltip renders $system->data VERBATIM, and the two refits that change a
		   WEAPON's numbers change fields the tooltip only ever quotes THROUGH data:
		     "Intercept"                      <- ->intercept   (Weapon::setSystemDataWindow)
		     "Fire control (fighter/med/cap)" <- ->fireControl
		   The other three refits move ->output and ->armour, which the client displays from the
		   live field, so they showed up correctly from day one and these two did not (user report,
		   2026-08-15). data is rebuilt per load in beforeTurn() from the ALREADY-enhanced values,
		   so re-sending it is the whole fix - but stripForJson does not send data for an ordinary
		   system, which is why it has to be named here. The lobby has no server round trip and
		   patches the same two entries client-side; ⚠️ MIRROR PAIR with
		   systemEnhancements.syncInterceptData / syncFireControlData. */
		'SYS_ADT' => array(
			'label'     => 'Advanced Defensive Targeting',
			'eligible'  => 'sysEnhEligibleADT',
			'price'     => 'sysEnhPriceADT',
			'limit'     => 'sysEnhLimitADT',
			'apply'     => 'sysEnhApplyADT',
			'serialise' => array('intercept','interceptArray','data'),
		),
		'SYS_GSGT' => array(
			'label'     => 'Gunsights',
			'eligible'  => 'sysEnhEligibleGSGT',
			'price'     => 'sysEnhPriceGSGT',
			'limit'     => 'sysEnhLimitOne',
			'apply'     => 'sysEnhApplyGSGT',
			//isModified is what makes weapon::stripForJson re-send fireControl at all - see §2.2.
			'serialise' => array('fireControl','fireControlArray','isModified','data'),
		),
		'SYS_HSHLD' => array(
			'label'     => 'Hardened Shields',
			'eligible'  => 'sysEnhEligibleHSHLD',
			'price'     => 'sysEnhPriceHSHLD',
			'limit'     => 'sysEnhLimitOne',
			'apply'     => 'sysEnhApplyOutput',
			'serialise' => array('output'),
		),
		'SYS_HARM' => array(
			'label'     => 'Hardened Armour',
			'eligible'  => 'sysEnhEligibleHARM',
			'price'     => 'sysEnhPriceHARM',
			'limit'     => 'sysEnhLimitOne',
			'apply'     => 'sysEnhApplyHARM',
			'serialise' => array('armour'),
		),
		'SYS_THR' => array(
			'label'     => 'Improved Thrust Rating',
			'eligible'  => 'sysEnhEligibleTHR',
			'price'     => 'sysEnhPriceTHR',
			'limit'     => 'sysEnhLimitTHR',
			'apply'     => 'sysEnhApplyOutput',
			'serialise' => array('output'),
		),
	);

	/* The registry, for anything that needs to read it (the JSON fixups, the client label map
	   emitter, the tests). Returned by value - callers must not mutate it. */
	public static function getSystemEnhancementRegistry(){
		return self::$systemEnhancementRegistry;
	}

	/* ⭐ THE one line this feature adds to a ship's Enhancements box, and - below - the one
	   function that takes it back out again. Both live here so the string exists ONCE: the line is
	   written at construction time, when there is no viewer yet, and removed at serialisation time,
	   when there is. A literal at each end would drift the first time the wording is nudged and the
	   only symptom would be an enemy quietly seeing it again.
	   ⚠️ MIRROR PAIR with systemEnhancements.summaryLine (JS), which writes the identical string in
	   the lobby - so the lobby and the game agree. */
	const SYS_ENH_SUMMARY_PREFIX = 'System Enhancements (';

	public static function systemEnhancementSummaryLine($count){
		return self::SYS_ENH_SUMMARY_PREFIX . $count . ')';
	}

	/* Remove the summary line from a tooltip, for a viewer who is not on the ship's team.
	   D8 said the ✦ badge was own-team-only; the SUMMARY LINE was not, and "System Enhancements (4)"
	   on an enemy hull is the same tell in words (user request, 2026-08-15). The ship-LEVEL lines
	   above it stay public, exactly as they always were.
	   ⚠️ This hides the LABEL, not the fact - §6.3 still applies. Several of the enhanced stat
	   values must reach the enemy for their own hit-chance and damage previews to be right. */
	public static function stripSystemEnhancementSummary($tooltip){
		if($tooltip === '' || strpos($tooltip, self::SYS_ENH_SUMMARY_PREFIX) === false) return $tooltip;
		$kept = array();
		foreach(explode('<br>', $tooltip) as $line){
			if($line === '') continue;
			if(strpos($line, self::SYS_ENH_SUMMARY_PREFIX) === 0) continue;
			$kept[] = $line;
		}
		return implode('<br>', $kept);
	}

	/* The server-side label table. enhname NEVER comes from the client tuple's index 1 - it is
	   interpolated into SQL by the writers and shown in tooltips. */
	public static function systemEnhancementLabel($enhID){
		return isset(self::$systemEnhancementRegistry[$enhID]['label'])
			? self::$systemEnhancementRegistry[$enhID]['label']
			: '';
	}

	/* Dispatch one registry slot. call_user_func rather than `self::$fn()`: the latter parses as
	   a variable static-method call and works, but it reads like a static PROPERTY access and one
	   day somebody will "fix" it into one. */
	private static function regCall($enhID, $slot, array $args){
		if(!isset(self::$systemEnhancementRegistry[$enhID][$slot])) return null;
		return call_user_func_array(array(__CLASS__, self::$systemEnhancementRegistry[$enhID][$slot]), $args);
	}

	/* ------------------------------------------------------------------ shared eligibility */

	/* The gate every one of the five sits behind (§4.3). Deliberately mirrors the client's
	   isPseudoSystem in SystemInfoButtons.js - ⚠️ MIRROR PAIR, change both. */
	public static function systemMayBeEnhanced($ship, $system){
		if(!is_object($ship) || !is_object($system)) return false;
		if($ship->factionAge > 2) return false;                     //D6 - hull age is the primary gate
		if($ship instanceof FighterFlight) return false;            //D7 - per-fighter system ids do not exist in the lobby
		if(!empty($ship->mine)) return false;                       //D7 - a bulk mine's clone rebuilds systems 0-indexed
		//Pseudo-system filter. This is what excludes InvulnerableThruster, whose
		//getArmourInvulnerable returns 99 regardless - enhancing it would charge for nothing.
		if(!($system->maxhealth > 0)) return false;
		if($system->isTargetable === false) return false;
		if(!empty($system->hideInShipWindow)) return false;
		/* D6 - the one case the ship-level gate lets through by accident: an ANCIENT weapon bolted
		   onto a young hull (customDevelopment.php sets factionAge = 3 on ~15 weapon classes).
		   ⚠️ factionAge is NOT declared on ShipSystem, only on those weapon classes, so this must
		   be an isset() test and never a bare read. */
		if(($system instanceof Weapon) && isset($system->factionAge) && $system->factionAge >= 3) return false;
		return true;
	}

	/* Every enhID this system may be OFFERED, or an empty array. Pure - no mutation, no DB. */
	public static function systemEnhancementsFor($ship, $system){
		$out = array();
		if(!self::systemMayBeEnhanced($ship, $system)) return $out;
		foreach(self::$systemEnhancementRegistry as $enhID => $def){
			if(!self::regCall($enhID, 'eligible', array($ship, $system))) continue;
			if(self::systemEnhancementLimit($ship, $system, $enhID) < 1) continue; //nothing left to buy
			$out[] = $enhID;
		}
		return $out;
	}

	/* THE pricing question (D4). $level is 0-BASED: the cost of buying the (level+1)th point.
	   Total for N levels is sum(price(0..N-1)).
	   ⚠️ D10 - every formula reads BLUEPRINT values. ELITE_CREW raises every thruster's output and
	   VOR_AZURS raises every EM Shield's; if the offer were priced after them, buying Elite Crew
	   would retroactively change what a thruster refit costs and how many levels the cap allows.
	   Offer generation runs on a freshly-constructed ship (setEnhancementOptions, before any
	   applier), so "current" IS "blueprint" at every site that calls this. Keep it that way. */
	public static function systemEnhancementPrice($ship, $system, $enhID, $level){
		$p = self::regCall($enhID, 'price', array($ship, $system, (int)$level));
		return $p === null ? 0 : (int)$p;
	}

	/* THE limit question. 0 means "not purchasable", which systemEnhancementsFor treats as
	   not-offered (a weapon already at intercept 4 cannot buy SYS_ADT at all). */
	public static function systemEnhancementLimit($ship, $system, $enhID){
		$l = self::regCall($enhID, 'limit', array($ship, $system));
		return $l === null ? 0 : max(0, (int)$l);
	}

	/* ------------------------------------------------------------------ offer generation (D3) */

	/* Build $ship->systemEnhancementOffers: one tuple per (eligible system x eligible enhancement).
	   Emitted in the static blueprint JSON; ABSENT in game (the $offerSystemEnhancements gate).

	       OFFER tuple: [enhID, limit, price, priceStep, systemid]
	                       0      1      2        3          4

	   ⚠️ This is a LEANER shape than $ship->systemEnhancements' 8-slot purchase tuple, which the
	   plan's §3.2 assumed it would share. Measured 2026-08-15 on the whole corpus: the full shape
	   cost 1,921 bytes per ship (2,557 classes, 85,025 offers, +217KB on Earth Alliance.json) -
	   roughly double the "under 1 KB per ship" budget §3.2 set as the condition for keeping the
	   human name on the wire. Dropping the two STRING slots the client can reconstruct for itself
	   is 62% of that back:
	     - the label comes from systemEnhancements.LABELS, the JS mirror of the registry above;
	     - sysname is D13's integrity check on a STORED PURCHASE, and is irrelevant on an offer -
	       the client is rendering the menu for a system it already holds, so it fills the slot in
	       when it turns an offer into a purchase.
	   Nothing else shares this shape; offersFor() in systemEnhancements.js is the only reader, and
	   it hands the menu an object, not a tuple. ⚠️ MIRROR PAIR - change both. */
	public static function setSystemEnhancementOptions($ship){
		$ship->systemEnhancementOffers = array();
		if(!self::$offerSystemEnhancements) return;
		if(!is_object($ship) || empty($ship->systems)) return;
		if($ship->factionAge > 2) return;                //cheap whole-ship exit before the per-system loop
		if($ship instanceof FighterFlight) return;
		if(!empty($ship->mine)) return;

		foreach($ship->systems as $system){
			foreach(self::systemEnhancementsFor($ship, $system) as $enhID){
				$ship->systemEnhancementOffers[] = array(
					$enhID,
					self::systemEnhancementLimit($ship, $system, $enhID),
					self::systemEnhancementPrice($ship, $system, $enhID, 0),
					self::systemEnhancementPriceStep($ship, $system, $enhID),
					(int)$system->id,
				);
			}
		}
	}

	/* The tuple's priceStep slot. The lobby totals N levels as sum(price + i*step), which is what
	   confirm.js already does for ship-level enhancements - so the step has to be the CONSTANT
	   difference between consecutive levels. Every one of the five is linear in level, so this is
	   just price(1) - price(0). Deriving it rather than declaring it means a formula and its step
	   can never disagree. */
	public static function systemEnhancementPriceStep($ship, $system, $enhID){
		if(self::systemEnhancementLimit($ship, $system, $enhID) < 2) return 0; //single-level refit: no step to take
		return self::systemEnhancementPrice($ship, $system, $enhID, 1)
			 - self::systemEnhancementPrice($ship, $system, $enhID, 0);
	}

	/* ------------------------------------------------------------------ blueprint value helpers */

	/* $system->guns defaults to 1 but is MUTATED at runtime by several classes (defensive.php
	   does `$this->guns = 2 + $this->getBoostLevel($turn)`; Trek weapons assign it from shot
	   counts) and by changeFiringMode, which re-reads gunsArray[$mode] straight over it.
	   Price off the largest value the mount can ever present, so a mode switch cannot make a
	   purchase retroactively cheap (§2.1). */
	private static function sysEnhGuns($system){
		$guns = isset($system->guns) ? (int)$system->guns : 1;
		if(!empty($system->gunsArray) && is_array($system->gunsArray)){
			foreach($system->gunsArray as $g) $guns = max($guns, (int)$g);
		}
		return max(1, $guns);
	}

	/* Intercept rating, taken as the maximum across firing modes for the same reason as guns.
	   ⚠️ interceptArray DOES exist - missile launchers derive a per-mode rating from the loaded
	   ammo (missile.php: $this->interceptArray[$mode] = $currAmmo->intercept) and
	   Weapon::changeFiringMode re-reads it over ->intercept exactly the way it re-reads
	   fireControlArray. The plan's §2.1 says there is none; that was true when it was written and
	   is not any more, so SYS_ADT carries the SAME mode-switch hazard §2.2 flags for Gunsights and
	   sysEnhApplyADT bumps both. */
	private static function sysEnhIntercept($system){
		$intercept = isset($system->intercept) ? (int)$system->intercept : 0;
		if(!empty($system->interceptArray) && is_array($system->interceptArray)){
			foreach($system->interceptArray as $v) $intercept = max($intercept, (int)$v);
		}
		return $intercept;
	}

	/* maxDamage is 0 on many weapons - variable-damage ones carry maxDamageArray and support
	   weapons genuinely deal none - so take the largest of both before applying the price floor. */
	private static function sysEnhMaxDamage($system){
		$dmg = isset($system->maxDamage) ? (int)$system->maxDamage : 0;
		if(!empty($system->maxDamageArray) && is_array($system->maxDamageArray)){
			foreach($system->maxDamageArray as $d) $dmg = max($dmg, (int)$d);
		}
		return $dmg;
	}

	/* Arcs a shield covers, in 60-degree steps.
	   ⚠️ arcWidth 0 means a FULL CIRCLE, not "no arc". Getting this wrong makes an omni-shield
	   free - it is the single easiest mistake in this file to make and the hardest to notice. */
	private static function sysEnhArcCount($system){
		$start = isset($system->startArc) ? (int)$system->startArc : 0;
		$end   = isset($system->endArc)   ? (int)$system->endArc   : 0;
		$width = (($end - $start) % 360 + 360) % 360;
		if($width === 0) $width = 360;
		return max(1, (int)round($width / 60));
	}

	/* ------------------------------------------------------------------ SYS_ADT */

	private static function sysEnhEligibleADT($ship, $system){
		return ($system instanceof Weapon) && self::sysEnhIntercept($system) > 0;
	}

	/* The resulting rating caps at 4. A weapon already at 4 gets limit 0, i.e. is not offered. */
	private static function sysEnhLimitADT($ship, $system){
		return max(0, 4 - self::sysEnhIntercept($system));
	}

	/* Cost is per RESULTING rating, multiplied by the mount's gun count (O2). Rating is a single
	   per-weapon value - there is no per-gun rating - and reads as -5% per point to the incoming
	   to-hit, so a Twin Array at 2 is -10%.
	       price(level) = 8 * guns * (intercept + level)
	   i.e. the current rating IS the tier index. Twin Array (intercept 2, guns 2):
	       level 0 -> 8*2*2 = 32,  level 1 -> 8*2*3 = 48,  limit 4-2 = 2.   (§2.1 worked example)
	   A single-gun weapon starting at 1 reproduces the requested 8 / 16 / 24. */
	private static function sysEnhPriceADT($ship, $system, $level){
		return 8 * self::sysEnhGuns($system) * (self::sysEnhIntercept($system) + (int)$level);
	}

	private static function sysEnhApplyADT($ship, $system, $count){
		$system->intercept = (int)$system->intercept + (int)$count;
		/* Mirror across firing modes, or the refit EVAPORATES on the first mode switch -
		   changeFiringMode re-reads interceptArray[$mode] straight over ->intercept.
		   Only NON-ZERO entries: a mode carrying non-interceptor missiles has rating 0 and must
		   keep it, exactly as a null fireControl must stay null. */
		if(!empty($system->interceptArray) && is_array($system->interceptArray)){
			foreach($system->interceptArray as $mode => $value){
				if((int)$value > 0) $system->interceptArray[$mode] = (int)$value + (int)$count;
			}
		}
	}

	/* ------------------------------------------------------------------ SYS_GSGT */

	/* Weapon classes that carry a fire control but get NOTHING from +1 to it - utility mounts, not
	   guns. Without this list they are all offered Gunsights at the price floor of 4, which is a
	   trap purchase: the player pays and nothing changes.

	   It is a DENY-LIST rather than a "must deal damage" rule on purpose. Of the 30 zero-damage
	   classes that pass the fireControl test (498 mounts across the corpus, measured 2026-08-15),
	   most DO roll to hit and genuinely want the refit - Burst Beam, Stun Beam, Electro Pulse Gun,
	   Tractor Beam, Gravity Net. A blanket maxDamage > 0 test would take those out with the rest.

	   ⚠️ Matched on CLASS NAME, so a subclass is NOT covered automatically - if a new variant of an
	   excluded mount appears it needs its own entry here. That is deliberate: silently excluding
	   everything under a base class is how a real weapon loses its refit without anyone noticing. */
	private static $gunsightExcluded = array(
		'AbbaiShieldProjector',
		'AegisSensorPod',
		'CombatTransporter',
		'GrapplingClaw',
		'GraviticShifter',
		'GromeTargetingArray',
		'MicroJumpSystem',
		'NexusChaffLauncher',
	);

	private static function sysEnhEligibleGSGT($ship, $system){
		if(!($system instanceof Weapon)) return false;
		if(in_array(get_class($system), self::$gunsightExcluded, true)) return false;
		if(empty($system->fireControl) || !is_array($system->fireControl)) return false;
		foreach($system->fireControl as $fc){
			if($fc !== null) return true;   //0 is a legal fire control - test against null, not truthiness
		}
		return false;
	}

	/* max(4, ceil(maxDamage * 0.25)) per gun. */
	private static function sysEnhPriceGSGT($ship, $system, $level){
		return (int)max(4, ceil(self::sysEnhMaxDamage($system) * 0.25)) * self::sysEnhGuns($system);
	}

	private static function sysEnhApplyGSGT($ship, $system, $count){
		self::sysEnhBumpFireControl($system->fireControl, $count);
		/* ⚠️ THE single most important trap in this feature (§2.2). Weapon::changeFiringMode
		   re-reads fireControlArray[$mode] STRAIGHT OVER fireControl, so a refit that bumps only
		   fireControl evaporates on the player's first mode switch - silently, mid-game. */
		if(!empty($system->fireControlArray) && is_array($system->fireControlArray)){
			foreach($system->fireControlArray as $mode => $fcSet){
				if(!is_array($fcSet)) continue;
				self::sysEnhBumpFireControl($fcSet, $count);
				$system->fireControlArray[$mode] = $fcSet;
			}
		}
		/* Reuse the EXISTING generic "this system was changed, force it to serialize" flag rather
		   than inventing a second one - the Gravitic Augmenter established it, and
		   weapon::stripForJson already re-sends fireControl AND fireControlArray off it. */
		$system->isModified = true;
	}

	/* +1 per level to every NON-NULL entry, in place.
	   ⚠️ null means "cannot target this size class" and MUST stay null: `null + 1` is 1 in PHP,
	   which would silently give a Piercing missile (fireControl [null,3,3]) a fighter-targeting
	   capability it does not have. Guard with !== null, never with truthiness. */
	private static function sysEnhBumpFireControl(&$fireControl, $count){
		if(!is_array($fireControl)) return;
		foreach($fireControl as $i => $fc){
			if($fc === null) continue;
			$fireControl[$i] = (int)$fc + (int)$count;
		}
	}

	/* ------------------------------------------------------------------ SYS_HSHLD */

	/* AbbaiShieldProjector is excluded FOR FREE and deliberately: it `extends Weapon implements
	   DefensiveSystem`, so it is not a Shield subclass and this instanceof already skips it.
	   No special case needed - but there IS a test asserting it, so a future reparenting cannot
	   quietly make it eligible. */
	private static function sysEnhEligibleHSHLD($ship, $system){
		return ($system instanceof EMShield) || ($system instanceof GraviticShield);
	}

	private static function sysEnhPriceHSHLD($ship, $system, $level){
		return 10 * (int)$system->output * self::sysEnhArcCount($system);
	}

	/* ------------------------------------------------------------------ SYS_HARM */

	private static function sysEnhEligibleHARM($ship, $system){
		return isset($system->armour) && (int)$system->armour > 0;
	}

	private static function sysEnhPriceHARM($ship, $system, $level){
		return (int)ceil((int)$system->maxhealth * max(2, (int)$system->armour) / 2);
	}

	/* ShipSystem::getArmourBase returns $this->armour verbatim, so a += 1 works generically for
	   every system including Structure blocks (IPSH_ESSAN already does exactly this). */
	private static function sysEnhApplyHARM($ship, $system, $count){
		$system->armour = (int)$system->armour + (int)$count;
	}

	/* ------------------------------------------------------------------ SYS_THR */

	private static function sysEnhEligibleTHR($ship, $system){
		return ($system instanceof Thruster);
	}

	/* "Up to double the rating". */
	private static function sysEnhLimitTHR($ship, $system){
		return max(0, (int)$system->output);
	}

	/* 2 x the DIRECTION's total blueprint thrust, +2 per level - because each level bought raises
	   that direction's total rating by 1, so the next level is 2 * (total + 1) (O4).
	   ⚠️ ELITE_CREW adds its count to EVERY thruster's output, server-side here and client-side in
	   lobbyEnhancements.js. D10 pins this price and sysEnhLimitTHR to the BLUEPRINT, so the two are
	   independent by design - do not "fix" one to follow the other. */
	private static function sysEnhPriceTHR($ship, $system, $level){
		$directionTotal = 0;
		foreach($ship->systems as $other){
			if(!($other instanceof Thruster)) continue;
			if((int)$other->direction !== (int)$system->direction) continue;
			$directionTotal += (int)$other->output;
		}
		return 2 * ($directionTotal + (int)$level);
	}

	/* ------------------------------------------------------------------ shared appliers */

	private static function sysEnhLimitOne($ship, $system){
		return 1;
	}

	/* SYS_HSHLD and SYS_THR are both "output += level".
	   ⚠️ ORDERING MATTERS for the shield and is already right: Shield::onConstructed derives
	   tohitPenalty/damagePenalty from getOutput(), and setSystemEnhancements is called from
	   BaseShip::onConstructed BEFORE the per-system onConstructed loop. If it ran later the shield
	   would absorb the bonus damage but not confer the bonus to-hit penalty - a half-working
	   shield, which is worse than a broken one because nobody notices. */
	private static function sysEnhApplyOutput($ship, $system, $count){
		$system->output = (int)$system->output + (int)$count;
	}

	/* ------------------------------------------------------------------ apply (in game) */

	/* APPLY $ship->systemEnhancements to the ship's systems.
	   Called from BaseShip::onConstructed immediately after setEnhancements(), i.e. BEFORE the
	   per-system onConstructed loop - see the ordering note on sysEnhApplyOutput. Runs once per
	   construction, so a plain += is correct here; the CLIENT mirror is the one that has to be
	   idempotent from a remembered blueprint value, because the player buys and un-buys from an
	   open menu (§5.1).
	   ⚠️ MIRROR PAIR with systemEnhancements.apply() in systemEnhancements.js - change both. */
	public static function setSystemEnhancements($ship){
		if(empty($ship->systemEnhancements)) return;

		/* ONE summary line in the ship's Enhancements box, last, after the ship-level ones
		   (§6.4). A line per refit would push the `enh` grid panel past the section cluster it
		   sits beside; the detail lives in each system's own SystemInfo tooltip.
		   Counted over rows that actually resolve below would be more honest, but the count
		   has to be stable across the two sides and the client cannot see a drop - so both
		   count the ROWS, and a dropped row is reported by the saved-fleet notice instead.
		   ⚠️ Written unconditionally here, because at construction time there is no viewer to ask.
		   BaseShip::stripForJson takes it back off again for anyone outside the ship's team -
		   see stripSystemEnhancementSummary(). */
		$refitCount = 0;
		foreach($ship->systemEnhancements as $row){
			if(isset($row[2]) && (int)$row[2] > 0) $refitCount++;
		}
		if($refitCount > 0){
			if($ship->enhancementTooltip != "") $ship->enhancementTooltip .= "<br>";
			$ship->enhancementTooltip .= self::systemEnhancementSummaryLine($refitCount);
		}

		foreach($ship->systemEnhancements as $row){
			$enhID = isset($row[0]) ? (string)$row[0] : '';
			$count = isset($row[2]) ? (int)$row[2] : 0;
			$systemid = isset($row[6]) ? (int)$row[6] : -1;
			if($count < 1) continue;
			if(!isset(self::$systemEnhancementRegistry[$enhID])) continue;

			$system = $ship->getSystemById($systemid);
			if(!$system) continue;                                    //stale id - drop, never guess

			/* D13 integrity check. A stored systemid is POSITIONAL - pure constructor order - so a
			   system inserted mid-constructor shifts every id after it and id 14 now names a
			   DIFFERENT system. Verifying the name turns a MIS-APPLIED refit into a DROPPED one.
			   Older rows may carry no name; those fall through on the id alone. */
			if(isset($row[7]) && $row[7] !== '' && (string)$system->name !== (string)$row[7]) continue;

			/* Deliberately NO eligibility re-check here, unlike loadSavedFleet (§4.7.1). These rows
			   were validated at buy time and the points are already spent and recorded in
			   tac_ship.enhvalue; a mid-campaign rebalance must not silently strip a refit the
			   player paid for in a game already under way. Saved FLEETS re-validate because they
			   are re-priced and the player can re-buy. */
			self::regCall($enhID, 'apply', array($ship, $system, $count));
		}
	}

	/* ------------------------------------------------------------------ JSON fixups */

	/* Per-system JSON fixups for the new track - GENERIC, driven entirely by the registry's
	   `serialise` list (D12). No per-enhancement branch, so a new enhancement cannot ship with its
	   client-side half forgotten, which is the bug class that is invisible until someone reads a
	   tooltip.
	   Called from the existing addSystemEnhancementsForJSON, which runs for EVERY system of EVERY
	   ship on EVERY load - hence the early exit before anything else happens (D3). */
	public static function addSystemEnhancementsOwnForJSON($ship, $system, $strippedSystem){
		if(empty($ship->systemEnhancements)) return $strippedSystem;

		$systemid = (int)$system->id;
		foreach($ship->systemEnhancements as $row){
			if(!isset($row[6]) || (int)$row[6] !== $systemid) continue;
			if(!isset($row[2]) || (int)$row[2] < 1) continue;
			$enhID = (string)$row[0];
			if(empty(self::$systemEnhancementRegistry[$enhID]['serialise'])) continue;

			foreach(self::$systemEnhancementRegistry[$enhID]['serialise'] as $field){
				//interceptArray/fireControlArray exist only on some classes - never invent a field.
				if(!property_exists($system, $field)) continue;
				$value = $system->$field;
				//An empty PHP array() encodes as the JSON ARRAY [], which on the client silently
				//replaces a map-shaped blueprint value with nothing. Send nothing instead.
				if(is_array($value) && empty($value)) continue;
				$strippedSystem->$field = $value;
			}
		}
		return $strippedSystem;
	}

	/* ------------------------------------------------------------------ buy-time validation (D4) */

	/* Validate a CLIENT-SUBMITTED list against a freshly built ship. Never throws.
	   Resolves each systemid, re-checks the name (D13), re-checks eligibility, drops anything on a
	   system the pre-battle payload destroys (D11), clamps the count to the current limit, and
	   REPLACES the price with the server's own - ship enhancements today trust the client's
	   pointCostEnh wholesale, and that trust is inherited, not endorsed.

	   $cleanPreBattle is PreBattleDamage::sanitise()'s output for the same ship, or null. The
	   client runs its own sweep for immediate feedback; THIS is the guarantee.

	   Returns array(
	     'rows'    => the clean PURCHASE tuples,
	     'total'   => the authoritative point total for pointCostSysEnh,
	     'notices' => human-readable strings, one per row that was dropped / clamped / re-priced
	   ). */
	public static function sanitiseSystemEnhancements($ship, $raw, $cleanPreBattle = null){
		$out = array('rows' => array(), 'total' => 0, 'notices' => array());
		if(!is_array($raw) || !is_object($ship)) return $out;

		$destroyed = self::sysEnhDestroyedSystemIds($ship, $cleanPreBattle);
		$seen = array(); //one row per (systemid, enhID) - the table's PK

		foreach($raw as $row){
			if(!is_array($row) || count($row) < 3) continue;
			$enhID = (string)$row[0];
			$count = isset($row[2]) ? (int)$row[2] : 0;
			$systemid = isset($row[6]) ? (int)$row[6] : -1;
			if($count < 1) continue;                                   //nothing bought - not an error
			if(!isset(self::$systemEnhancementRegistry[$enhID])) continue;   //bogus enhID

			$key = $systemid . '|' . $enhID;
			if(isset($seen[$key])) continue;                           //duplicate row
			$seen[$key] = true;

			$system = $ship->getSystemById($systemid);
			if(!$system){
				$out['notices'][] = self::systemEnhancementLabel($enhID) . ": system #$systemid no longer exists - removed.";
				continue;
			}
			$sysname = (string)$system->name;
			if(isset($row[7]) && $row[7] !== '' && (string)$row[7] !== $sysname){
				$out['notices'][] = self::systemEnhancementLabel($enhID) . ": system #$systemid is now '$sysname', not '" . (string)$row[7] . "' - removed.";
				continue;
			}
			if(isset($destroyed[$systemid])){
				//D11 - you cannot refit a wreck, and the removal is one-way.
				$out['notices'][] = self::systemEnhancementLabel($enhID) . " on $sysname #$systemid was removed: the system is destroyed.";
				continue;
			}
			if(!self::systemMayBeEnhanced($ship, $system) || !self::regCall($enhID, 'eligible', array($ship, $system))){
				$out['notices'][] = self::systemEnhancementLabel($enhID) . " is no longer available on $sysname #$systemid - removed.";
				continue;
			}

			$limit = self::systemEnhancementLimit($ship, $system, $enhID);
			if($limit < 1){
				$out['notices'][] = self::systemEnhancementLabel($enhID) . " is no longer available on $sysname #$systemid - removed.";
				continue;
			}
			if($count > $limit){
				$out['notices'][] = self::systemEnhancementLabel($enhID) . " on $sysname #$systemid was reduced from $count to $limit.";
				$count = $limit;
			}

			$total = self::systemEnhancementTotalPrice($ship, $system, $enhID, $count);
			$claimed = isset($row[4]) ? (float)$row[4] : null;
			if($claimed !== null && abs($claimed - $total) > 0.001){
				$out['notices'][] = self::systemEnhancementLabel($enhID) . " on $sysname #$systemid was re-priced from $claimed to $total.";
			}

			$out['rows'][] = array(
				$enhID,
				self::systemEnhancementLabel($enhID),   //NEVER the client tuple's index 1
				$count,
				$limit,
				$total,                                  //index 4 = TOTAL PAID, see the tuple note on BaseShip
				self::systemEnhancementPriceStep($ship, $system, $enhID),
				$systemid,
				$sysname,
			);
			$out['total'] += $total;
		}
		return $out;
	}

	/* Points for buying $count levels: the sum of the per-level prices, NOT count x price. */
	public static function systemEnhancementTotalPrice($ship, $system, $enhID, $count){
		$total = 0;
		for($i = 0; $i < (int)$count; $i++){
			$total += self::systemEnhancementPrice($ship, $system, $enhID, $i);
		}
		return $total;
	}

	/* Which system ids the pre-battle payload leaves DESTROYED, INCLUDING the structure cascade
	   (D11). Destroying a Structure block destroys every system in its location, so sweeping only
	   the boxes the player ticked would leave refits alive on systems that are in fact wrecked.

	   Derived from the sanitised payload plus each system's own structure LOCATION rather than
	   from ShipSystem::isDestroyed(): isDestroyed() reads ->structureSystem, which is populated in
	   onConstructed, and a POST-side reconstructed ship is exactly the kind of thing that may not
	   have been through it ([[arch_post_side_ship_reconstruction]]). ->location and
	   getStructureLocation() are set in the constructor and are always there. */
	private static function sysEnhDestroyedSystemIds($ship, $cleanPreBattle){
		$destroyed = array();
		if(empty($cleanPreBattle['sys']) || !is_array($cleanPreBattle['sys'])) return $destroyed;

		foreach($cleanPreBattle['sys'] as $ref => $entry){
			if(!empty($entry['k'])) $destroyed[(int)$ref] = true;
		}
		if(empty($destroyed)) return $destroyed;

		//Which LOCATIONS lost their Structure block?
		$deadLocations = array();
		$primaryDead = false;
		foreach($ship->systems as $sys){
			if(!($sys instanceof Structure)) continue;
			if(empty($destroyed[(int)$sys->id])) continue;
			$deadLocations[(int)$sys->location] = true;
			if((int)$sys->location === 0) $primaryDead = true;
		}
		if(empty($deadLocations)) return $destroyed;

		foreach($ship->systems as $sys){
			$id = (int)$sys->id;
			if(isset($destroyed[$id])) continue;
			if($sys instanceof Structure){
				//A non-PRIMARY Structure falls with PRIMARY - isDestroyed()'s own rule.
				if($primaryDead && (int)$sys->location !== 0) $destroyed[$id] = true;
				continue;
			}
			//Outer-structure-ring hulls (the Vree saucers): a breached block does NOT take its
			//systems with it ([[arch_outer_structure_ring]]).
			if($sys->getSurvivesStructureDestruction()) continue;

			$loc = $sys->getStructureLocation();
			if(is_array($loc)){
				if(empty($loc)) continue;
				$all = true;
				foreach($loc as $l){ if(empty($deadLocations[(int)$l])){ $all = false; break; } }
				if($all) $destroyed[$id] = true;
			}else{
				if(!empty($deadLocations[(int)$loc])) $destroyed[$id] = true;
			}
		}
		return $destroyed;
	}

} //endof class Enhancements
?>
