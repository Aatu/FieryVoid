<?php
/*class responsible for unit enhancements
Most enhancements made are based on official ones, but they're changed and/or repointed
*/
class Enhancements{
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
		$unit->enhancementOptionsDisabled[] = 'IMPR_ENG'; 
		$unit->enhancementOptionsDisabled[] = 'IMPR_REA'; 
		$unit->enhancementOptionsDisabled[] = 'IMPR_SENS'; 
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

		case 'MindriderFighter':
			Enhancements::blockStandardEnhancements($unit);
			break;	
	
		case 'MindriderShip':
			Enhancements::blockStandardEnhancements($unit);
			$unit->enhancementOptionsEnabled[] = 'IMPR_SR';
			$unit->enhancementOptionsEnabled[] = 'IMPR_TS';			
			break;
	
		case 'ShadowShip':
			Enhancements::blockStandardEnhancements($unit);
			$unit->enhancementOptionsEnabled[] = 'IMPR_SR';
			$unit->enhancementOptionsEnabled[] = 'SHAD_FTRL';
			break;
	  
		case 'ShadowFighter':
			Enhancements::blockStandardEnhancements($unit);
			$unit->enhancementOptionsEnabled[] = 'SHAD_CTRL';
			break;	

		case 'Terrain':
			Enhancements::blockStandardEnhancements($unit);
			//$unit->enhancementOptionsDisabled[] = 'DEPLOY'; //Terrain cannot jump into a scenario!  
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


	  //Elite Marines for Grappling Claws, cost: 40% craft price (round up), limit: 1	  	
	  $enhID = 'ELT_MRN';	  
	  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option needs to be specifically enabled
		  $enhName = 'Elite Marines';
		  $enhLimit = 1;	
		  $enhPrice = ceil($ship->pointCost * 0.4); //40% ship cost  
		  $enhPriceStep = 0;
		  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,false);
	  }  
	  
	  //Extra Marines for Grappling Claws, cost: 10 per unit, limit: 3	  	
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


	  //To convert Assault Shuttles hangar slots to Fighter Slots
	  if (array_key_exists("assault shuttles", $ship->fighters)) { //Only add if ship has Assault Shuttle hangar space! 	  
	    $enhID = 'HANG_F';
		if(!in_array($enhID, $ship->enhancementOptionsDisabled)){ //Check option is also not disabled.
				$enhName = 'Assault Shuttle to Fighter slot';
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
				$enhName = 'Fighter to Assault Shuttle slot';
				$enhLimit = $totalCount; //The number of assault shuttle slots ship has is max conversion amount.
				$enhPrice = 5; //Flat 5 pts per slot converted	  
				$enhPriceStep = 0; //flat rate
				$ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,true);	
		}
	  }

	  $enhID = 'IFF_SYS';
	  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option needs to be specifically enabled
		  $enhName = 'Identify Friend or Foe (IFF) System';
		  $enhLimit = 1; //Only ever need 1
		  $enhPrice = 0; //fixed.		  
		  foreach ($ship->systems as $system){
			if ($system instanceof BallisticMineLauncher || $system instanceof AbbaiMineLauncher){
		  	$enhPrice += 4;
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
		  $enhName = 'Improved Psychic Field (+1 range)';
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
		  $enhName = 'Improved Sensor Array';
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
		  $enhName = 'Improved Thirdspace Shield';
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
		  $enhName = 'Improved Thought Shield';
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
		  $enhName = 'Religious Fervor - Only if Desperate Rules apply';
		  $enhLimit = 1;	
		  $enhPrice = 0;
		  $enhPriceStep = 0;
		  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,true);//this is NOT an enhancement - rather an OPTION
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
		  $enhName = 'Increased Diffuser Capability';
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

	  //Shadow fighter launched: -1 PRIMARY Structure, limit: hangar capacity
	  $enhID = 'SHAD_FTRL';
	  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option is enabled
		  $enhName = 'Spawn a Medium Fighter';
		  //find total hangar capacity
		  $capacity = 0;	  
		  foreach ($ship->fighters as $name => $count){
			$capacity += $count;
		  }  
		  if($capacity > 0){ //this ship can actually carry fighters!!
			  $enhPrice = 0;	  
			  $enhPriceStep = 0; 
			  $enhLimit = ceil($capacity);	  
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,true);//not an enhancement!
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
		  $enhName = 'Amethyst Skin Coloring';
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
		  $enhName = 'Azure Skin Coloring';
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
		  $enhName = 'Crimson Skin Coloring';
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
			  $enhPriceStep = 0; //flat rate
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
		  $enhLimit = 4;	
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
		  $flight->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep,true); //NOT an enhancement!
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
	  } //end of magazine-requiring options
	  
	  
  } //endof function setEnhancementOptionsFighter
	    
    
  
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
  
  
	/*enhancements for fighters - actual applying of chosen enhancements
	*/
	private static function setEnhancementsFighter($flight){
	   	foreach($flight->enhancementOptions as $entry){			
			//ID,readableName,numberTaken,limit,price,priceStep
			$enhID = $entry[0];
			$enhCount = $entry[2];
			$enhDescription = $entry[1];
			if($enhCount > 0) {
				if($flight->enhancementTooltip != "") $flight->enhancementTooltip .= "<br>";
				//if($enhID == 'DEPLOY' && $enhCount > 1){ //Special type of Enhancement, clarify what it means.
				//	$flight->enhancementTooltip .= "Flight deploys on Turn $enhCount";						
				//}else{				
				$flight->enhancementTooltip .= "$enhDescription";
				if ($enhCount>1) $flight->enhancementTooltip .= " (x$enhCount)";
				//}
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
				if($ship->enhancementTooltip != "") $ship->enhancementTooltip .= "<br>";
				//if($enhID == 'DEPLOY'){ //Special type of Enhancement, clarify what it means.
				//	$ship->enhancementTooltip .= "Ship deploys on Turn $enhCount";						
				//}else{
				$ship->enhancementTooltip .= "$enhDescription";
				if ($enhCount>1) $ship->enhancementTooltip .= " (x$enhCount)";
				//}		

			        switch ($enhID) {	

					case 'DEPLOY':
						//Amend value of turn that ship deploys on.
						//$ship->deploysOnTurn = $enhCount;
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

					case 'HANG_F'://Hangar Conversion to Fighter slot, no actual need to change anything here.  
						break;	

					case 'HANG_AS'://Hangar Conversion to Assault Shuttle slot, no actual need to change anything here.  
						break;	

					case 'EXT_MRN'://Extra marines, increase contingent per Claw by 1.
						foreach ($ship->systems as $system){
							if ($system instanceof GrapplingClaw){							
								$system->ammunition += $enhCount;
							}
						}
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

					case 'SHAD_DIFF': //Increased Diffuser Capability: +1 Output for each Diffuser
						foreach ($ship->systems as $system){
							if ($system instanceof EnergyDiffuser){
								$system->output += $enhCount;
							}
						}  
						break;
						
					case 'SHAD_FTRL': //Shadow fighter launched: -1 Structure point for each launched fighter
						$struct = $ship->getStructureSystem(0);
						if($struct){
							$struct->maxhealth -= $enhCount;
						}
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
					
						case 'SHAD_DIFF': //Increased Diffuser Capability: +1 Output for each Diffuser
							if ($system instanceof EnergyDiffuser) { 
								$strippedSystem->output = $system->output;
							}
							break;	
							

						case 'SHAD_FTRL': //Shadow fighter launched: -1 Structure point for each launched fighter
							if ($system instanceof Structure) { //Shadows ships have only one Structure
								$strippedSystem->maxhealth = $system->maxhealth;
							}
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
		  
} //endof class Enhancements
?>
