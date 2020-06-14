<?php
/*class responsible for unit enhancements
Most enhancements made are based on official ones, but they're changed and/or repointed
*/
class Enhancements{
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
  } //endof function setEnhancementOptions
	
	
	/* all ship enhancement options - availability and cost calculation
	*/
  public static function setEnhancementOptionsShip($ship){
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
		  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep);
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
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep);
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
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep);
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
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep);
		  }
	  }	  
	    
	  
	  //Ipsha-specific - Eethan Barony refit (available for generic Ipsha designs only, Eethan-specific may have it already incorporated in some form)
	  $enhID = 'IPSH_EETH';	  
	  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option needs to be specifically enabled
		  $enhName = 'Eethan Barony refit';
		  $enhLimit = 1;	
		  $enhPrice = ceil($ship->pointCost*0.1); //+10%	
		  $enhPriceStep = 0;
		  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep);
	  }  	
	  
	  //Ipsha-specific - Essan Barony refit (available for generic Ipsha designs only, Essan-specific ones may have it already incorporated in some form)
	  $enhID = 'IPSH_ESSAN';	  
	  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option needs to be specifically enabled
		  $enhName = 'Essan Barony refit';
		  $enhLimit = 1;	
		  $enhPrice = 0;
		  $enhPriceStep = 0;
		  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep);
	  }  	

	  
	  //Poor Crew (official but modified): -5 Initiative, -1 Engine, -1 Sensors, -1 Reactor power, +1 Profile, +2 to critical results
	  //cost: -15% of ship cost (second time: -10%)
	  $enhID = 'POOR_CREW';
	  if(!in_array($enhID, $ship->enhancementOptionsDisabled)){ //option is not disabled
		  $enhName = 'Poor Crew';
		  $enhLimit = 2;	
		  $enhPrice = -ceil($ship->pointCost*0.15); //-15%	  
		  $enhPriceStep = -ceil($enhPrice/3); //+5%, for total price of -10% for second level
		  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep);
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
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep);
		  }		  
	  }	  

	  //Shadow fighter launched: -1 PRIMARY Structure, limit: hangar capacity
	  $enhID = 'SHAD_FTRL';
	  if(in_array($enhID, $ship->enhancementOptionsEnabled)){ //option is enabled
		  $enhName = 'Fighter launched';
		  //find total hangar capacity
		  $capacity = 0;	  
		  foreach ($ship->fighters as $name => $count){
			$capacity += $count;
		  }  
		  if($capacity > 0){ //this ship can actually carry fighters!!
			  $enhPrice = 0;	  
			  $enhPriceStep = 0; 
			  $enhLimit = ceil($capacity);	  
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep);
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
			  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep);
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
		  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep);
	  }	  
	  
		//Vulnerable to Criticals: +1 to critical rolls, and let's make it scalable :)
		//let's make it very cheap too
		//effect: -1 Ini, Price: -4, step 0, max count 4 (for a total of 16 points at max value)
	  $enhID = 'VULN_CRIT';
	  if(!in_array($enhID, $ship->enhancementOptionsDisabled)){ //option is not disabled
		  $enhName = 'Vulnerable to Criticals';
		  $enhLimit = 4;	
		  $enhPrice = -4; //fixed, very low value
		  $enhPriceStep = 0; //flat rate
		  $ship->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep);
	  }	  
	  
	  	  
  } //endof function setEnhancementOptionsShip
	
	
	/* all fighter enhancement options - availability and cost calculation
	*/
  public static function setEnhancementOptionsFighter($flight){
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
		  $flight->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep);
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
		  $flight->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep);
	  }
	  
	  //Improved Targeting Computer: +1 OB, cost: new rating *3, limit: 1
	  $enhID = 'IMPR_OB';	  
	  if(!in_array($enhID, $flight->enhancementOptionsDisabled)){ //option is not disabled
		  $enhName = 'Improved Targeting Computer';
		  $enhLimit = 1;	
		  $enhPrice = max(1,($flight->offensivebonus+1)*3);	  
		  $enhPriceStep = 0; //3; //limit is 1 but if anyone increases it - step is ready...or would be but the effect looks silly ;)
		  $flight->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep);
	  }

	  //Improved Thrust: +1 free thrust, cost: new rating, limit: 1
	  $enhID = 'IMPR_THR';	  
	  if(!in_array($enhID, $flight->enhancementOptionsDisabled)){ //option is not disabled
		  $enhName = 'Improved Thrust';
		  $enhLimit = 1;	
		  $enhPrice = max(1,$flight->freethrust+1);	  
		  $enhPriceStep = 0;
		  $flight->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep);
	  }

	  //Navigator: missile guidance, +1(5) Ini, cost: 10, limit: 1
	  $enhID = 'NAVIGATOR';	  
	  if(in_array($enhID, $flight->enhancementOptionsEnabled)){ //option needs to be specifically enabled
		  $enhName = 'Navigator';
		  $enhLimit = 1;	
		  $enhPrice = 10;	  
		  $enhPriceStep = 0;
		  $flight->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep);
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
		  $flight->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep);
	  }
	  
	  //Shadow fighter deployed without carrier control: -2 OB, -3(15) Ini, cost: 0, limit: 1
	  $enhID = 'SHAD_CTRL';	  
	  if(in_array($enhID, $flight->enhancementOptionsEnabled)){ //option needs to be specifically enabled
		  $enhName = 'Uncontrolled';
		  $enhLimit = 1;	
		  $enhPrice = 0;	  
		  $enhPriceStep = 0;
		  $flight->enhancementOptions[] = array($enhID, $enhName,0,$enhLimit, $enhPrice, $enhPriceStep);
	  }  
	  
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
				$flight->enhancementTooltip .= "$enhDescription";
				if ($enhCount>1) $ship->enhancementTooltip .= " (x$enhCount)";
				switch ($enhID) { 
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
				}
			}			
		}
	   }//endof function setEnhancementsFighter
  
	
	   /*enhancements for ships - actual applying of chosen enhancements
	   */
	   private static function setEnhancementsShip($ship){
	   	foreach($ship->enhancementOptions as $entry){
			//ID,readableName,numberTaken,limit,price,priceStep
			$enhID = $entry[0];
			$enhCount = $entry[2];
			$enhDescription = $entry[1];
			if($enhCount > 0) {
				if($ship->enhancementTooltip != "") $ship->enhancementTooltip .= "<br>";
				$ship->enhancementTooltip .= "$enhDescription";
				if ($enhCount>1) $ship->enhancementTooltip .= " (x$enhCount)";
			        switch ($enhID) {
						
					case 'ELITE_CREW': //Elite Crew: +5 Initiative, +2 Engine, +1 Sensors, +2 Reactor power, -1 Profile, -2 to critical results
						//fixed values
						$ship->forwardDefense -= $enhCount;
						$ship->sideDefense -= $enhCount;
						$ship->iniativebonus += $enhCount*5;
						$ship->critRollMod -= $enhCount*2;
						
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
						if($strongestValue > 0){ //Engine actually exists to be enhanced!
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
						if($strongestValue > 0){ //Engine actually exists to be enhanced!
							$strongestSystem->output += $enhCount;
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
						
					case 'POOR_CREW': //Poor Crew: -1 Engine, -1 Sensors, -1 Reactor power, +1 Profile, +2 to critical results, -5 Initiative
						//fixed values
						$ship->forwardDefense += $enhCount;
						$ship->sideDefense += $enhCount;
						$ship->iniativebonus -= $enhCount*5;
						$ship->critRollMod += $enhCount*2;
						
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
						
					case 'VULN_CRIT': //Vulnerable to Criticals: +1(5) Crit roll mod
						//fixed values
						$ship->critRollMod += $enhCount;
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
						case 'ELITE_SW': //Elite Pilot: modify pivot cost, OB, profile and Initiative
							if($ship instanceof FighterFlight){
								$strippedShip->pivotcost = $ship->pivotcost;
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
						case 'ELITE_CREW': //Elite crew: Initiative and Profiles modified
							$strippedShip->forwardDefense = $ship->forwardDefense;
							$strippedShip->sideDefense = $ship->sideDefense;
							$strippedShip->iniativebonus = $ship->iniativebonus;
							break;
							
						case 'IPSH_EETH': //Ipsha Eethan Barony refit: +2 free thrust, +25% available power (round up), +0.1 turn delay, -5 Initiative, +4 critical roll modifier for Reactor and Engine
							$strippedShip->iniativebonus = $ship->iniativebonus;
							$strippedShip->turndelaycost = $ship->turndelaycost;
							break;			
							
						case 'POOR_CREW': //Poor crew: Initiative and Profiles modified
							$strippedShip->forwardDefense = $ship->forwardDefense;
							$strippedShip->sideDefense = $ship->sideDefense;
							$strippedShip->iniativebonus = $ship->iniativebonus;
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
	  } //endof function setEnhancementOptions
		  
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
					}					
				}
		    }			
			return $strippedSystem;
	   }//endof function addEnhancementsForJSON
		  
} //endof class Enhancements
?>
