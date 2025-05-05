<?php

class ShipSystem {
    public $jsClass = false;
    public $destroyed = false;
    public $startArc, $endArc;
    public $location; //0:primary, 1:front, 2:rear, 3:left, 4:right;
    public $id, $armour, $maxhealth, $powerReq, $output, $name, $displayName;
	public $outputDisplay = ''; //if not empty - overrides default on-icon display text
    public $outputType = null;
    public $specialAbilities = array();
    
    public $damage = array();
    public $outputMod = 0;
    public $boostable = false;
    public $boostEfficiency = null;
    public $maxBoostLevel = null;
    public $power = array();
    public $fireOrders = array();
    public $canOffLine = false;
	public $fighter = false; //important for actual fighters

    public $data = array();
    public $critData = array();
    public $destructionAnimated = false;
    public $imagePath, $iconPath;
    public $critRollMod = 0; //penalty tu critical damage roll: positive means crit is more likely, negative less likely (for this system)
    
    protected $possibleCriticals = array();
	
    public $primary = false; //is this a core system?
    public $isPrimaryTargetable = false; //can this system be targeted by called shot if it's on PRIMARY?	
    public $isTargetable = true; //false means it cannot be targeted at all by called shots! - good for technical systems :)
    
    public $forceCriticalRoll = false; //true forces critical roll even if no damage was done
	
    public $criticals = array();
	public $advancedArmor = false; //indicates that system has advanced armor
    
    protected $structureSystem;
	
    protected $parentSystem = null;
    protected $unit = null; //unit on which system is mounted
	
	protected $individualNotes = array();
	public $individualNotesTransfer = "";//variable for transferring individual notes from interface to server layer
	
	public $repairPriority = 4;//priority at which system is repaired (by self repair system); higher = sooner; 0 indicates that system cannot be repaired
	
	protected $doCountForCombatValue = true; //false means this system is skipped when evaluating ships' combat value!
	
	protected $tagList = array(); //tags for TAG hit chart entry; REMEMBER TAGS SHOULD BE MADE USING CAPITAL LETTERS!
	
	protected $calledShotBonus = 0;//Some systems, like Aegis Sensor Pod are easier to hit with called shots.		


    function __construct($armour, $maxhealth, $powerReq, $output){
        $this->armour = $armour;
        $this->maxhealth = (int)$maxhealth;
        $this->powerReq = (int)$powerReq;
        $this->output = (int)$output;
    }

    public function stripForJson(){
        $strippedSystem = new stdClass();
        $strippedSystem->id = $this->id;
        $strippedSystem->name = $this->name;
        $strippedSystem->damage = $this->damage;
        $strippedSystem->criticals = $this->criticals;
        $strippedSystem->critData = $this->critData;
        $strippedSystem->power = $this->power;
        $strippedSystem->specialAbilities = $this->specialAbilities;
        $strippedSystem->output = $this->output;
        $strippedSystem->outputMod = $this->outputMod;
        $strippedSystem->destroyed = $this->destroyed;
		$strippedSystem->individualNotesTransfer = $this->individualNotesTransfer; //necessary		 

		//ship enhancements - check if this system is affected...
		$strippedSystem = Enhancements::addSystemEnhancementsForJSON($this->unit, $this, $strippedSystem);//modifies $strippedSystem object
		
		//Hyach Specialists sometimes require additional info to be sent to front end.
		$ship = $this->unit;
		if ($ship->getSystemByName("HyachSpecialists")){ //Does ship have Specialists system?
			$specialists = $ship->HyachSpecialists;
			$specAllocatedArray = $specialists->specAllocatedCount;
			foreach ($specAllocatedArray as $specsUsed=>$specValue){
				if ($specsUsed == 'Thruster'){
					$strippedSystem->boostEfficiency = $this->boostEfficiency; 				
				}		
			}
		}	

		if($ship->getSystemByName("MindriderEngine")){ //Mind's Eye COntraction CAN increase armour!
			$strippedSystem->armour = $this->armour;
		}			
							
        return $strippedSystem;
    }
	
	public function getCountForCombatValue(){
		return $this->doCountForCombatValue;
	}
	
	public function addTag($tag){
		$tag = strtoupper($tag);
		$this->tagList[] = $tag;
	}
		
	public function checkTag($tag){
		$toReturn = false;
		$tag = strtoupper($tag);
		if(in_array($tag,$this->tagList)) $toReturn = true;		
		return $toReturn;
	}

	public function checkforCalledShotBonus(){
		return 0;
	}
	
	public function doIndividualNotesTransfer(){//optionally to be redefined if system can receive any private data from front endthat need immediate attention		
	}

    public function onConstructed($ship, $turn, $phase){
        if($ship->getAdvancedArmor()==true){
            $this->advancedArmor = true;
        }
        $this->structureSystem = $ship->getStructureSystem($this->location);
        $this->effectCriticals();
        $this->destroyed = $this->isDestroyed();
    }
	
	public function criticalPhaseEffects($ship, $gamedata){
	    	    
	    if ($this->isDestroyed()) return; // no point if the system is actually destroyed already
	    			
	    foreach ($this->criticals as $critical) {
	    	  	
	    //Limpet Bore criticals 	    	
	    	if ($critical->phpclass == "LimpetBore" && $critical->turn <= $gamedata->turn) {//Has Limpet Bore crit AND active.
				$this->doLimpetBore($critical, $ship, $gamedata);
			}
			
		//Marine Mission-related Criticals:		
			//Sabotage/Wreak Havoc criticals  	    	
			if (($critical->phpclass == "Sabotage" || $critical->phpclass == "SabotageElite") && $critical->turn <= $gamedata->turn){//Sabotage crit AND active	
				if($this instanceof CnC){//Sabotage crit has been placed on CnC, therefore they are trying to sabotage the ship generally via Wreak Havoc.
					$this->doWreakHavocMission($critical, $ship, $gamedata);
				}else{//Not CnC, so marines have been targeted by Called Shot at a specific system.
					if(!$this->isDestroyed()){//Only continue sabotage if system is not destroyed by other sabotages.
						$this->doSabotageMission($critical, $ship, $gamedata);					
					}else{//System has sabotage mission AND system is now destroyed.  Move to CnC for Wreak Havoc next turn.
						//Create a fireOrder to update player essentially.					
						$rammingSystem = $ship->getSystemByName("RammingAttack");
						$newFireOrder = null;

						if ($rammingSystem) { // actually exists! - it should on every ship!
							$shotsHit = 1; //Marines always attempt their mission.
									
							$newFireOrder = new FireOrder(
								-1, "normal", $ship->id, $ship->id,
								$rammingSystem->id, -1, $gamedata->turn, 1,
								100, 100, 1, $shotsHit, 0,
								0, 0, 'Sabotage', 10000
							);
									
							$newFireOrder->addToDB = true;
							$rammingSystem->fireOrders[] = $newFireOrder;
						}

					$newFireOrder->pubnotes = "<br>SABOTAGE - Marine attempted to damage " . $this->displayName .", but it was already destroyed.  Sabotage operations continue.";	
												
						$cnc = $ship->getSystemByName("CnC");				
						if($cnc){
							if($critical->phpclass == "SabotageElite"){//Are Marines Elite?
								$wreackCrit = new SabotageElite(-1, $ship->id, $cnc->id, 'SabotageElite', $gamedata->turn+1); //Takes effect next turn.
								$wreackCrit->updated = true;
								$cnc->criticals[] =  $wreackCrit;					
							}else{//Not Elite Marines					
								$wreackCrit = new Sabotage(-1, $ship->id, $cnc->id, 'Sabotage', $gamedata->turn+1);  //Takes effect next turn.
								$wreackCrit->updated = true;
								$cnc->criticals[] =  $wreackCrit;
							}	
						}						
					}
				}
			}				
			//Capture Ship criticals  	    	
			if (($critical->phpclass == "CaptureShip" || $critical->phpclass == "CaptureShipElite") && $critical->turn <= $gamedata->turn){//CaptureShip crit AND active
				$this->doCaptureMission($critical, $ship, $gamedata);
			}				
			//Rescue Mission criticals  	    	
			if (($critical->phpclass == "RescueMission" || $critical->phpclass == "RescueMissionElite") && $critical->turn <= $gamedata->turn){//RescueMission crit AND active
				$this->doRescueMission($critical, $ship, $gamedata);	
			}//endof Marine Mission criticals
																
		} //End of search for specific Critical effects		
			
	} //Endof criticalPhaseEffects - A hook for special effects that should happen in Critical phase/end of turn


	public function doLimpetBore($critical, $ship, $gamedata){
	    			
		$explodeRoll = Dice::d(10);
		$turnsAttached = $gamedata->turn - $critical->turn;
		$explodesOn = 7 - $turnsAttached;	//Initial success chance is 7 on a d10, improved by -1 for each turn Limpet is attached.				
		$explodechance = (11 - $explodesOn) * 10;	//For fireorder to display correct % in combat log.			           

		$rammingSystem = $ship->getSystemByName("RammingAttack");
		$newFireOrder = null;
		
		    if ($rammingSystem) { // actually exists! - it should on every ship!
		        $shotsHit = 0;
			        if ($explodeRoll >= $explodesOn) { // actual explosion
			            $shotsHit = 1;
			    	}
				    	
		        $newFireOrder = new FireOrder(
		            -1, "normal", $ship->id, $ship->id,
		            $rammingSystem->id, -1, $gamedata->turn, 1,
		            $explodechance, $explodeRoll*10, 1, $shotsHit, 0,
		            0, 0, 'LimpetBore', 10000
		        );
		        $newFireOrder->pubnotes = "<br>Limpet Bore attempts to damage " . $this->displayName ."!  Needed: $explodesOn, Rolled: $explodeRoll.";
		        $newFireOrder->addToDB = true;
		        $rammingSystem->fireOrders[] = $newFireOrder;
		    }

		    if ($explodeRoll >= $explodesOn) { // actual explosion	      
		        $maxDamage = $this->getRemainingHealth();
		        $damageDealt = Dice::d(10, 2) + 10;
				$damageCaused = min($damageDealt, $maxDamage); //Don't cause more damage than system's health remaining.
				    
			    if ($damageDealt >= $maxDamage){	//Deals enough to destroy system	        
			        $damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $this->id, $damageCaused, 0, 0, -1, true, false, "", "LimpetBore");
			        $damageEntry->updated = true;
			        $this->damage[] = $damageEntry;
			    }else{ //Not enough to destroy, just damage system instead.
			        $damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $this->id, $damageCaused, 0, 0, -1, false, false, "", "LimpetBore");
			        $damageEntry->updated = true;
			        $this->damage[] = $damageEntry;
			        $crits = array(); 
					$crits = $this->testCritical($ship, $gamedata, $crits); //Damage caused, need to force critical test outside normal routine
					        
			        $critical->turnend = $gamedata->turn;//End Limpet Bore crit this turn, it blew up!
					$critical->forceModify = true; //actually save the change.
					$critical->updated = true; //actually save the change cd!	        	      	
			    }
					        
			    if ($rammingSystem) { // add extra data to damage entry - so the firing order can be identified!
			        $damageEntry->shooterid = $ship->id; // additional field
			        $damageEntry->weaponid = $rammingSystem->id; // additional field
			    }
		 	 }

		    if ($explodeRoll < $explodesOn) {
				if ($turnsAttached >= 4){ //After initial +4 attempts Limpet Bore drops off / fails.
					$critical->turnend = $gamedata->turn;//End Limpet Bore crit this turn
					$critical->forceModify = true; //actually save the change.
					$critical->updated = true; //actually save the change cd!
				}
			}		
	}//endof doLimpetBore()	

	public function getMarineRollMod($critical, $ship, $gamedata){
	
		$rollMod = 0;//Modify based on different factors. 		

		if(($critical->phpclass == "SabotageElite" || $critical->phpclass == "CaptureShipElite" || $critical->phpclass == "RescueMissionElite") && $critical->turn <= $gamedata->turn) $rollMod -= 1; //If attacking Marines are Elite, easier to complete missions
		if($ship->faction == "Narn Regime" || $ship->faction == "Gaim Intelligence" )	$rollMod += 1; //Certain factions defend harder!
					
		foreach ($ship->enhancementOptions as $enhancement) {//ID,readableName,numberTaken,limit,price,priceStep
			$enhID = $enhancement[0];
			$enhCount = $enhancement[2];		        
				if($enhCount > 0) {		            
					if ($enhID == 'ELITE_CREW') $rollMod += $enhCount;	//Elite Crews are better at defending.
					if ($enhID == 'POOR_CREW') $rollMod -= $enhCount; //Poor Crews are worse.
		        	if ($enhID == 'MARK_FERV') $rollMod += $enhCount; //Markab Fervor causes defenders to fight harder.
				}
		}
		
		//Capture Ship missions chances of getting 'caught' doesn't increase every two turns, they are in open battle from the start!
		if(!($critical->phpclass == "CaptureShip" || $critical->phpclass == "CaptureShipElite") && $critical->turn <= $gamedata->turn){
			$turnsAttempted = $gamedata->turn - $critical->turn;							
			$rollMod += floor($turnsAttempted/2);//If marines have been trying sabotage or a rescue for a while, increase roll by 1 every two turns.
		}
			
		return $rollMod;
	
	}//endof getMarineRollMod	

	public function doWreakHavocMission($critical, $ship, $gamedata){
		$cnc = $ship->getSystemByName("CnC");//$this should be CnC, but just in case.
		if($cnc){
			foreach($cnc->criticals as $critDisabled){
				if($critDisabled->phpclass == "ShipDisabled"  && $critDisabled->turn <= $gamedata->turn) return;//Already captured, no more havoc!					
			}
		}

		//Create fireOrder to show the attempted marine action.		          
		$rammingSystem = $ship->getSystemByName("RammingAttack");
		$newFireOrder = null;

		if ($rammingSystem) { // actually exists! - it should on every ship!
			$shotsHit = 1; //Marines always attempt their mission.
					
			$newFireOrder = new FireOrder(
				-1, "normal", $ship->id, $ship->id,
				$rammingSystem->id, -1, $gamedata->turn, 1,
				100, 100, 1, $shotsHit, 0,
				0, 0, 'WreakHavoc', 10000
			);
					
			$newFireOrder->addToDB = true;
			$rammingSystem->fireOrders[] = $newFireOrder;
		}

		$rollMod = $this->getMarineRollMod($critical, $ship, $gamedata);					    			
		$wreakHavocRoll = max(0, Dice::d(10) + $rollMod); //Main roll for effect of Marines sabotage.

			//Now apply effect based on the rolled dice!
		if ($wreakHavocRoll <= 1) { 		    //Damage to a random primary system.

				$attackedSystem = null;  // Initialize $attackedSystem to null
				do {
				    $attackedSystem = $ship->getHitSystem($ship, $newFireOrder, $rammingSystem, $gamedata, 0);
				} while ($attackedSystem instanceof Structure);// Re-roll $attackedSystem until it is not an instance of Structure.
	
				$maxDamage = $attackedSystem->getRemainingHealth();
				$damageDealt = Dice::d(6, 1);
				$damageCaused = min($damageDealt, $maxDamage); //Don't cause more damage than system's health remaining.					

				$newFireOrder->pubnotes = "<br>Roll(Mod): $wreakHavocRoll($rollMod) - WREAK HAVOC - A marine unit deals $damageDealt damage to the " . $attackedSystem->displayName .".";
						
				if ($damageDealt >= $maxDamage){	//Deals enough to destroy system	        
					$damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $attackedSystem->id, $damageCaused, 0, 0, -1, true, false, "", "WreakHavoc");
					$damageEntry->updated = true;
					$attackedSystem->damage[] = $damageEntry;			
				}else{ //Not enough to destroy, just damage system instead.
					$damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $attackedSystem->id, $damageCaused, 0, 0, -1, false, false, "", "WreakHavoc");
					$damageEntry->updated = true;
					$attackedSystem->damage[] = $damageEntry;
					$crits = array(); 
					$crits = $attackedSystem->testCritical($ship, $gamedata, $crits);//Damage caused, need to force critical test outside normal routine
				}

				if ($rammingSystem) { // add extra data to damage entry - so the firing order can be identified!
			        $damageEntry->shooterid = $ship->id; // additional field
			        $damageEntry->weaponid = $rammingSystem->id; // additional field
			    }				
									
		}//endof Rolled 1 or less

			if ($wreakHavocRoll == 2) { //Reduce initiative next turn.
				$cnc = $ship->getSystemByName("CnC");
				$effectInitiative = Dice::d(6,1);//strength of effect: 1d6
				$effectInitiative5 = $effectInitiative * 5;
				$newFireOrder->pubnotes = "<br>Roll(Mod): $wreakHavocRoll($rollMod) - WREAK HAVOC - Marines disrupt internal operations, initiative reduced by $effectInitiative5 next turn.";						
										
				if($cnc){
					for($i=1; $i<=$effectInitiative;$i++){
						$crit = new tmpinidown(-1, $ship->id, $cnc->id, 'tmpinidown', $gamedata->turn); 
						$crit->updated = true;
				        $cnc->criticals[] =  $crit;
					}				
				}		
			}//endof Rolled 2

			if ($wreakHavocRoll == 3) { //Reduce EW next turn
				$scanner = $ship->getSystemByName("Scanner");
				$effectSensors = Dice::d(3,1);//strength of effect: 1d3
				$newFireOrder->pubnotes = "<br>Roll(Mod): $wreakHavocRoll($rollMod) - WREAK HAVOC - Marine set up a jamming device, EW reduced by $effectSensors next turn.";						
										
				if($scanner){
					for($i=1; $i<=$effectSensors;$i++){
						$crit = new OutputReduced1(-1, $ship->id, $scanner->id, 'OutputReduced1', $gamedata->turn+1, $gamedata->turn+1); 
						$crit->updated = true;
				        $scanner->criticals[] =  $crit;
					}				
				}
			}//endof Rolled 3

			if ($wreakHavocRoll == 4) { //reduce thrust next turn
				$engine = $ship->getSystemByName("Engine");
				$effectEngine = Dice::d(3,1);//strength of effect: 1d3				
				$newFireOrder->pubnotes = "<br>Roll(Mod): $wreakHavocRoll($rollMod) - WREAK HAVOC - Marines sabotage engine conduits, thrust reduced by $effectEngine next turn unless gravitic ship or an OSAT.";					
				if($ship->gravitic) return; //No effect on Gravitic engines.
				if($ship instanceof OSAT) return; //OSATs have no engines.					

														
				if($engine){
					for($i=1; $i<=$effectEngine;$i++){
						$crit = new OutputReduced1(-1, $ship->id, $engine->id, 'OutputReduced1', $gamedata->turn+1, $gamedata->turn+1); 
						$crit->updated = true;
				        $engine->criticals[] =  $crit;
					}				
				}												
			}//endof Rolled 4

			if ($wreakHavocRoll == 5) { //Increase hit profile next turn
				//+1 to hit this ship next turn
				$newFireOrder->pubnotes = "<br>Roll(Mod): $wreakHavocRoll($rollMod) - WREAK HAVOC - Marines place a signal emitter on target ship, its defence profile is increased by 5% next turn.";				
				$cnc = $ship->getSystemByName("CnC");				
					if($cnc){
						$crit = new ProfileIncreased(-1, $ship->id, $cnc->id, 'ProfileIncreased', $gamedata->turn+1, $gamedata->turn+1); //Ends next turn.
						$crit->updated = true;
						$cnc->criticals[] =  $crit;					
					}							
			}//endof Rolled 5
									
			if ($wreakHavocRoll >= 6 && $wreakHavocRoll <= 8) { //No effect this turn, try again next turn
				$newFireOrder->pubnotes = "<br>Roll(Mod): $wreakHavocRoll($rollMod) - WREAK HAVOC - Marine fail to sabotage ship this turn. They will try again next turn.";
			}//endof Rolled between 6 and 8

			if ($wreakHavocRoll >= 9) { // Failed and Marines eliminated during Sabotage attempt.
				$critical->turnend = $gamedata->turn;//End Marines mission this turn
				$critical->forceModify = true; //actually save the change.
				$critical->updated = true; //actually save the change cd!
				$newFireOrder->pubnotes = "<br>Roll(Mod): $wreakHavocRoll($rollMod) - WREAK HAVOC - Marines eliminated whilst attempting to sabotage enemy ship.";									
			}//endof Rolled 9 or more
					 
	}//endof doWreakHavocMission()



	public function doSabotageMission($critical, $ship, $gamedata){ //loading exisiting notes is done in dbmanager->getSystemDataForShips()	
		$cnc = $ship->getSystemByName("CnC");//$this should be CnC, but just in case.
		if($cnc){
			foreach($cnc->criticals as $critDisabled){
				if($critDisabled->phpclass == "ShipDisabled"  && $critDisabled->turn <= $gamedata->turn) return;//Already captured, no more sabotage!					
			}
		}

		//Create fireOrder to show the attempted marine action.		          
		$rammingSystem = $ship->getSystemByName("RammingAttack");
		$newFireOrder = null;

		if ($rammingSystem) { // actually exists! - it should on every ship!			
			$newFireOrder = new FireOrder(
				-1, "normal", $ship->id, $ship->id,
				$rammingSystem->id, -1, $gamedata->turn, 1,
				100, 100, 1, 1, 0,
				0, 0, 'Sabotage', 10000
			);
					
			$newFireOrder->addToDB = true;
			$rammingSystem->fireOrders[] = $newFireOrder;
		}

		$rollMod = $this->getMarineRollMod($critical, $ship, $gamedata);						    			
		$sabotageRoll = max(0, Dice::d(10) + $rollMod); //Main roll for effect of Marines sabotage.
					
				//Now apply effect based on the rolled dice!				
				if ($sabotageRoll <= 1) { // Explodes	- Full damage      
					$maxDamage = $this->getRemainingHealth();
					$damageDealt = Dice::d(6, 3) + 2;
					$damageCaused = min($damageDealt, $maxDamage); //Don't cause more damage than system's health remaining.
					$newFireOrder->pubnotes = "<br>Roll(Mod): $sabotageRoll($rollMod) - SABOTAGE - Marine unit causes $damageCaused damage to " . $this->displayName ." and will continue sabotage operations.";	
				
					if ($damageDealt >= $maxDamage){	//Deals enough to destroy system	        
						$damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $this->id, $damageCaused, 0, 0, -1, true, false, "", "Sabotage");
						$damageEntry->updated = true;
						$this->damage[] = $damageEntry;
						
							//Then move Marines to CnC for Wreck Havoc
							$cnc = $ship->getSystemByName("CnC");				
							if($cnc){
									if($critical->phpclass == "SabotageElite"){//Are Marines Elite?
										$wreackCrit = new SabotageElite(-1, $ship->id, $cnc->id, 'SabotageElite', $gamedata->turn+1); //Takes effect next turn.
										$wreackCrit->updated = true;
										$cnc->criticals[] =  $wreackCrit;					
									}else{//Not Elite Marines					
										$wreackCrit = new Sabotage(-1, $ship->id, $cnc->id, 'Sabotage', $gamedata->turn+1);  //Takes effect next turn.
										$wreackCrit->updated = true;
										$cnc->criticals[] =  $wreackCrit;
									}	
							}
							
						$critical->turnend = $gamedata->turn;//This Sabotage crit now ends, has moved to CnC.
						$critical->forceModify = true; //actually save the change.
						$critical->updated = true; //actually save the change cd!	
							    		         	
					}else{ //Not enough to destroy, just damage system instead.
						$damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $this->id, $damageCaused, 0, 0, -1, false, false, "", "Sabotage");
						$damageEntry->updated = true;
						$this->damage[] = $damageEntry;
						$crits = array(); 
						$crits = $this->testCritical($ship, $gamedata, $crits); //Damage caused, need to force critical test outside normal routine
					}

					if ($rammingSystem) { // add extra data to damage entry - so the firing order can be identified!
					    $damageEntry->shooterid = $ship->id; // additional field
					    $damageEntry->weaponid = $rammingSystem->id; // additional field
					 }									
				}//endof Rolled 1 or less	
				
				if ($sabotageRoll >= 2 && $sabotageRoll <= 3) { // Explodes - but reduced Damage, marines survive.	      
					$maxDamage = $this->getRemainingHealth();
					$damageDealt = Dice::d(6, 1) + 2;
					$damageCaused = min($damageDealt, $maxDamage); //Don't cause more damage than system's health remaining.
					$newFireOrder->pubnotes = "<br>Roll(Mod): $sabotageRoll($rollMod) - SABOTAGE - A marine unit causes $damageCaused damage to " . $this->displayName ." and will continue sabotage operations.";						
						
						if ($damageDealt >= $maxDamage){	//Deals enough to destroy system	        
									$damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $this->id, $damageCaused, 0, 0, -1, true, false, "", "Sabotage");
									$damageEntry->updated = true;
									$this->damage[] = $damageEntry;
									
								//Then move Marines to CnC for Wreck Havoc
								$cnc = $ship->getSystemByName("CnC");				
								if($cnc){
										if($critical->phpclass == "SabotageElite"){//Are Marines Elite?
											$wreackCrit = new SabotageElite(-1, $ship->id, $cnc->id, 'SabotageElite', $gamedata->turn+1); //Takes effect next turn.
											$wreackCrit->updated = true;
											$cnc->criticals[] =  $wreackCrit;					
										}else{//Not Elite Marines					
											$wreackCrit = new Sabotage(-1, $ship->id, $cnc->id, 'Sabotage', $gamedata->turn+1);  //Takes effect next turn.
											$wreackCrit->updated = true;
											$cnc->criticals[] =  $wreackCrit;
										}		    		
					            }
					            
					    $critical->turnend = $gamedata->turn;//This Sabotage crit now ends, have moved to CnC.
						$critical->forceModify = true; //actually save the change.
						$critical->updated = true; //actually save the change cd!        
					            
					            										
						}else{ //Not enough to destroy, just damage system instead.
								$damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $this->id, $damageCaused, 0, 0, -1, false, false, "", "Sabotage");
								$damageEntry->updated = true;
								$this->damage[] = $damageEntry;
								$crits = array(); 
								$crits = $this->testCritical($ship, $gamedata, $crits); //Damage caused, need to force critical test outside normal routine
						}		

						if ($rammingSystem) { // add extra data to damage entry - so the firing order can be identified!
							$damageEntry->shooterid = $ship->id; // additional field
							$damageEntry->weaponid = $rammingSystem->id; // additional field
						}								
				}//endof Rolled between 2 or 3			

				if ($sabotageRoll >= 4 && $sabotageRoll <= 5) { //Explodes - reduced Damage and Marines eliminated.
					$maxDamage = $this->getRemainingHealth();
					$damageDealt = Dice::d(6, 1) + 2;
					$damageCaused = min($damageDealt, $maxDamage); //Don't cause more damage than system's health remaining.
					$newFireOrder->pubnotes = "<br>Roll(Mod): $sabotageRoll($rollMod) - SABOTAGE - A marine unit causes $damageCaused damage to " . $this->displayName .", but is eliminated during sabotage mission.";						
						
						if ($damageDealt >= $maxDamage){	//Deals enough to destroy system	        
									$damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $this->id, $damageCaused, 0, 0, -1, true, false, "", "Sabotage");
									$damageEntry->updated = true;
									$this->damage[] = $damageEntry;							
						}else{ //Not enough to destroy, just damage system instead.
									$damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $this->id, $damageCaused, 0, 0, -1, false, false, "", "Sabotage");
									$damageEntry->updated = true;
									$this->damage[] = $damageEntry;
									$crits = array(); 
									$crits = $this->testCritical($ship, $gamedata, $crits); //Damage caused, need to force critical test outside normal routine
						}		

						if ($rammingSystem) { // add extra data to damage entry - so the firing order can be identified!
							$damageEntry->shooterid = $ship->id; // additional field
							$damageEntry->weaponid = $rammingSystem->id; // additional field
						}
						
						$critical->turnend = $gamedata->turn;//Marines are eliminated.
						$critical->forceModify = true; //actually save the change.
						$critical->updated = true; //actually save the change cd!										
				}//endof Rolled between 4 or 5		
						
				if ($sabotageRoll >= 6 && $sabotageRoll <= 8) { // No damage, try again next turn.						
					$newFireOrder->pubnotes = "<br>Roll(Mod): $sabotageRoll($rollMod) - SABOTAGE - Marine unit fails to damage " . $this->displayName .". They will try again next turn.";								        	      	
				}//endof Rolled between 6 or 8	

				if ($sabotageRoll >= 9 ) { // Failed and Marines eliminated during Sabotage attempt.					
					$critical->turnend = $gamedata->turn;//End Marines mission this turn
					$critical->forceModify = true; //actually save the change.
					$critical->updated = true; //actually save the change cd!
					$newFireOrder->pubnotes = "<br>Roll(Mod): $sabotageRoll($rollMod) - SABOTAGE - Marine unit was eliminated attempting to damage " . $this->displayName .".";	
					
					$critical->turnend = $gamedata->turn;//Marines are eliminated.
					$critical->forceModify = true; //actually save the change.
					$critical->updated = true; //actually save the change cd!						
									
				}//endof Rolled 9 or more											
	
	}//endof doSabotageMission()
		

	public function doCaptureMission($critical, $ship, $gamedata){ 

		$cnc = $ship->getSystemByName("CnC");	
			foreach ($cnc->criticals as $cncCrit){
				if($cncCrit->phpclass == "ShipDisabled" && $cncCrit->turn <= $gamedata->turn) return;//Ship already captured, no more work here.	
			}		

		//Create fireOrder to show the attempted marine action.
		$rammingSystem = $ship->getSystemByName("RammingAttack");	
		$newFireOrder = null;

		if ($rammingSystem) { // actually exists! - it should on every ship!
			$shotsHit = 1;//Marines always try to carry out their mission.
							
			$newFireOrder = new FireOrder(
				-1, "normal", $ship->id, $ship->id,
				$rammingSystem->id, -1, $gamedata->turn, 1,
				100, 100, 1, $shotsHit, 0,
				0, 0, 'Capture', 10000
			);
							
		$newFireOrder->addToDB = true;
		$rammingSystem->fireOrders[] = $newFireOrder;
		}
	
		$attackerRollMod = $this->getMarineRollMod($critical, $ship, $gamedata);						    			
		$attackerRollMod10 = $attackerRollMod * 10;
		$attackerRoll = max(0, Dice::d(100) + $attackerRollMod10); //Roll for whether attackers kill a defender.							
		
			//Now resolve the attackers roll							
			if ($attackerRoll <= 50) { // Marines kill a defender.
				if($cnc){
						$newCrit = new DefenderLost(-1, $ship->id, $cnc->id, 'DefenderLost', $gamedata->turn); 
						$newCrit->updated = true;
				        $cnc->criticals[] =  $newCrit;				
				}						
					$newFireOrder->pubnotes = "<br>Roll(Mod): $attackerRoll($attackerRollMod10) - CAPTURE - Marines eliminate a defender.";    	      	
			}
					
			if ($attackerRoll >= 51) {				
					$newFireOrder->pubnotes = "<br>Roll(Mod): $attackerRoll($attackerRollMod10) - CAPTURE - Marines do not eliminate a defender.";
			}				

			//Now defenders strike back!
			$defRollMod = $this->getMarineRollMod($critical, $ship, $gamedata);
			$defRollMod10 = $defRollMod * 10;//Multiply by 10
			$defenderRoll = max(0, Dice::d(100) + $defRollMod10);//Roll for whether Defenders kill an attacker.

			if ($defenderRoll >= 75) { // Defenders eliminate the attacking Marines.
				$critical->turnend = $gamedata->turn;//End Capture crit this turn
				$critical->forceModify = true; //actually save the change.
				$critical->updated = true; //actually save the change cd!
				$newFireOrder->pubnotes .= " Defenders eliminate a marine unit. Roll(Mod): $defenderRoll($defRollMod10).";	
			}else{
				$newFireOrder->pubnotes .= " Defenders do not eliminate any marines. Roll(Mod): $defenderRoll($defRollMod10).";					
			}
			
		$this->checkShipCaptured($critical, $ship, $gamedata);	//Check if last defender eliminated and ship still under attack.
					
	}//end of doCaptureMission()	


	public function doRescueMission($critical, $ship, $gamedata){ 

		//Create fireOrder to show the attempted marine action.
		$rammingSystem = $ship->getSystemByName("RammingAttack");
		$newFireOrder = null;

		if ($rammingSystem) { // actually exists! - it should on every ship!							
			$newFireOrder = new FireOrder(
				-1, "normal", $ship->id, $ship->id,
				$rammingSystem->id, -1, $gamedata->turn, 1,
				100, 100, 1, 1, 0,
				0, 0, 'Rescue', 10000
			);
							
			$newFireOrder->addToDB = true;
			$rammingSystem->fireOrders[] = $newFireOrder;
		}

		$cnc = $ship->getSystemByName("CnC");//$this should be CnC, but just in case.
		if($cnc){
			foreach($cnc->criticals as $critDisabled){
				if($critDisabled->phpclass == "ShipDisabled"  && $critDisabled->turn <= $gamedata->turn){
				$newFireOrder->pubnotes = "<br>RESCUE - Enemy ship has been captured, marines automatically completes their rescue mission!";					
					return;//Already captured, no more sabotage!					
				}
			}
		}

		$rollMod = $this->getMarineRollMod($critical, $ship, $gamedata);										    			
		$rescueRoll = max(0, Dice::d(10) + $rollMod); //Main roll for effect of Marines Rescue mission.

		//Now apply effect based on the rolled dice!				
		if ($rescueRoll <= 2) { // Successful rescue!					
				$critical->turnend = $gamedata->turn;//Success, end mission!
				$critical->forceModify = true; //actually save the change.
				$critical->updated = true; //actually save the change cd!
				$newFireOrder->pubnotes = "<br>Roll(Mod): $rescueRoll($rollMod) - RESCUE - Marines successfully complete their rescue mission!";								        	      	
		}			
		
		if ($rescueRoll >= 3 && $rescueRoll <= 4) { // //Success (more or less), end mission!						
				$critical->turnend = $gamedata->turn;//Extend Marine mission to next turn.
				$critical->forceModify = true; //actually save the change.
				$critical->updated = true; //actually save the change cd!
				$newFireOrder->pubnotes = "<br>Roll(Mod): $rescueRoll($rollMod) - RESCUE - Marines complete their rescue mission, but take heavy causalties.";									        	      	
		}			

		if ($rescueRoll >= 5 && $rescueRoll <= 6) { // No result, try again next turn.						
				$newFireOrder->pubnotes = "<br>Roll(Mod): $rescueRoll($rollMod) - RESCUE - Marines fail to complete a rescue mission. They will try again next turn.";								        	      	
		}

		if ($rescueRoll >= 7 ) { // Failed and Marines eliminated during Rescue attempt.					
				$critical->turnend = $gamedata->turn;//End Marines mission this turn
				$critical->forceModify = true; //actually save the change.
				$critical->updated = true; //actually save the change cd!
				$newFireOrder->pubnotes = "<br>Roll(Mod): $rescueRoll($rollMod) - RESCUE - Marines are eliminated attempting their rescue mission.";							        	      	
		}
				
	}//endof doRescueMission()


	public function checkShipCaptured($critical, $ship, $gamedata){
		$cnc = $ship->getSystemByName("CnC");//$this should be CnC, but just in case.
		if($cnc){
			foreach($cnc->criticals as $critDisabled){
				if($critDisabled->phpclass == "ShipDisabled"  && $critDisabled->turn <= $gamedata->turn) return;//Already captured, no more work!					
			}
		}		
		
		$rammingFactor = $ship->getRammingFactor();
		$startingMarines = floor($rammingFactor/20);//Get base level of Marines, after any damage.
		$currentMarines = $ship->howManyMarines();//How many Marines are there currently?   Base level - 'DefenderLost' crits basically.
		$defendersLost = 0;//Initialise
		$defendersLost = $startingMarines - $currentMarines; //If 0 currentMarines, bascially that means all are gone.
		
		$attackersOnboard = 0;//Initialise								
		foreach($cnc->criticals as $critCapture){ 										
			if($critCapture->phpclass == "CaptureShip" || $critCapture->phpclass == "CaptureShipElite")	$attackersOnboard += 1;		
		}
			
		//Check if Marine Defenders eliminated AND if attackers still onboard e.g. defenders weren't lost to structure damage being boarded in a previous turn.		
		if(($defendersLost >= $startingMarines) && ($attackersOnboard > 0)){ //Ship's defenders have been eliminated and attackers onboard.  Ship is disabled.

			if($cnc){
				//If not disabled, apply the crit to do so.						
				$crit = new ShipDisabled(-1, $ship->id, $cnc->id, 'ShipDisabled', $gamedata->turn);
				$crit->updated = true;
				$cnc->criticals[] =  $crit;	
				
				//create fireOrder				
				$rammingSystem = $ship->getSystemByName("RammingAttack");
				$newFireOrder = null;

				if ($rammingSystem) { // actually exists! - it should on every ship!
									
					$newFireOrder = new FireOrder(
						-1, "normal", $ship->id, $ship->id,
						$rammingSystem->id, -1, $gamedata->turn, 1,
						100, 100, 1, 1, 0,
						0, 0, 'Captured', 10000
					);
									
					$newFireOrder->addToDB = true;
					$rammingSystem->fireOrders[] = $newFireOrder;
				}				
				
				$newFireOrder->pubnotes = "<br>Attacking Marines overcome defenders and disable the ship for the rest of the scenario.";	
			}										
		}
	}//endof checkShipCaptured()

	
	
	/*saves individual notes (if any new ones exist) to database*/
	public function saveIndividualNotes(DBManager $dbManager){ //loading exisiting notes is done in dbmanager->getSystemDataForShips()
		foreach ($this->individualNotes as $currNote){
			$dbManager->insertIndividualNote($currNote);//function itself will decide whether this note really needs saving
		}
		//...and delete them, after saving they serve no more function
		$this->individualNotes = array();
	}
	
	/*generates individual notes (if necessary)
	base version is empty, to be redefined by systems as necessary
	*/
	public function generateIndividualNotes($gamedata, $dbManager){	}	
	
	public function addIndividualNote($noteObject){
		$this->individualNotes[] = $noteObject;
	}
	
	/*act on notes just loaded - to be redefined by systems as necessary*/
	public function onIndividualNotesLoaded($gamedata){
		$this->individualNotes = array();//delete notes, after reaction on their load they serve no further purpose
	}
		
	
    public function setUnit($unit){
		$this->unit = $unit;    
    }
	
    public function getUnit(){
		return $this->unit;    
    }
    
    public function getSpecialAbilityList($list)
    {
        if ($this instanceof SpecialAbility)
        {
            if ($this->isDestroyed() || $this->isOfflineOnTurn())
                return;

            foreach ($this->specialAbilities as $effect)
            {
                if (!isset($list[$effect]))
                {
                    $list[$effect] = $this->id;
                }
            }
        }
        
        return $list;
    }
    
	/*function called before firing orders are resolved; weapons with special actions (like auto-fire, combination fire, etc)
		will have their special before firing logic here (like creating additional fire orders!)
		In future, other systems may have similar needs
	*/
    public function beforeFiringOrderResolution($gamedata)
    {
    }
	
    public function beforeTurn($ship, $turn, $phase){
        $this->setSystemDataWindow($turn);
    }
    
    public function setDamage($damage){ //$damage object 
        $this->damage[] = $damage;
    }
    
    public function setDamages($damages){
        $this->damage = $damages;
    }
    
//    public function setPowers($power){
//        $this->power = $power;
//    }
//    
    public function setPower($power){
        $this->power[] = $power;
    }
    
    public function getFireOrders($turn = -1){
	if($turn<1){
        	return $this->fireOrders;
	}else{ //indicated a particular turn
		$fireOrders = array();
		foreach($this->fireOrders as $fireOrder){
			if($fireOrder->turn == $turn){
				$fireOrders[] = $fireOrder;
			}
		}
		return $fireOrders;
	}
    }
    
    public function setFireOrder($fireOrder){
        $this->fireOrders[] = $fireOrder;
    }
    
    public function setFireOrders($fireOrders){
        $this->fireOrders = $fireOrders;
    }
    
    public function setId($id){
        $this->id = $id;
    }
    
    public function setCritical($critical, $turn = 0){ //turn no longer relevant
		/* obsolete now that Criticals are considered with turn restriction
        if ($critical->param){            
            $currentTurn = TacGamedata::$currentTurn;
            if ($currentTurn > $critical->turn + $critical->param){
                return;
            }
        }        
        if (!$critical->oneturn || ($critical->oneturn && $critical->turn >= $turn-1))
			*/
            $this->criticals[] = $critical; 
    }
    
    public function setCriticals($criticals, $turn = 0){ 
        foreach( $criticals as $crit){
            $this->setCritical($crit, $turn);
        }
    }
    
	/*used by estiamtions only, doesn't take all things into account - do not use in actual damage dealing routines!*/
    public  function getArmourComplete($target, $shooter, $dmgClass, $pos=null){ //gets armour - from indicated direction if necessary direction 
		//$pos is to be included if launch position is different than firing unit position
		$armour = $this->getArmourBase($target, $shooter, $dmgClass, $pos);    
		$armour += $this->getArmourAdaptive($target, $shooter, $dmgClass, $pos);    
		return $armour;
    }
		
    public  function getArmourBase($target, $shooter, $dmgClass, $pos=null){ //gets armour - from indicated direction if necessary direction 
		//$pos is to be included if launch position is different than firing unit position
		$armour = $this->armour;    
		return $armour;
    }
	
    public function getArmourAdaptive($target, $shooter, $dmgClass, $pos=null){ //gets adaptive part of armour
		$armour = 0;
		$AAController = $this->unit->getAdaptiveArmorController(); 
		if($AAController){
			$armour = $AAController->getArmourValue($dmgClass);			
		}
		return $armour;
	}
	    
	
    
    public function setSystemDataWindow($turn){
		//now with advent of self repair - system ID might get important...
		$this->data["ID"] = $this->id;
		
		if($this->startArc !== null){
			$this->data["Arc"] = $this->startArc . ".." . $this->endArc;
		}
			
		if($this->powerReq > 0){
			$this->data["Power used"] = $this->powerReq;
		} else {
			$this->data["Power used"] = 'none';
		}
	    
		/* no longer needed, info available in Notes
			if($this->advancedArmor == true){
				$this->data["Others"] = "Advanced Armor";
			}
		*/
	    
        $counts = array();
        
        foreach ($this->criticals as $crit)
		{
            if (isset($counts[$crit->phpclass])){
                $counts[$crit->phpclass]++;
            }else{
                $counts[$crit->phpclass] = 1;
            }            
            
            $forturn = "";
            if ($crit->oneturn && $crit->turn == $turn)
                $forturn = " next turn.";
            
            if ($crit->oneturn && $crit->turn+1 == $turn)
                $forturn = " this turn.";
                
            $this->critData[$crit->phpclass] = $crit->description .$forturn ;
        }
    }
    
    public function testCritical($ship, $gamedata, $crits, $add = 0){
		//use additional value to critical!
		$bonusCrit = $this->critRollMod + $ship->critRollMod;	
		$crits = array_values($crits); //in case some criticals were deleted!		
	    
        $damageMulti = 1;

        if ($ship instanceof OSAT){ //leaving instanceof OSAT here - MicroSATs will have crits appropriate for their size (eg. dropout tests)
            if ($this->displayName == "Thruster" && sizeof($this->criticals) == 0){
                if ($this->getTotalDamage()+$bonusCrit > ($this->maxhealth/2)){
                    $crit = $this->addCritical($ship->id, "OSatThrusterCrit", $gamedata);
                    $crits[] = $crit;
                }
            }
        }        

	/*moved to potentially exploding systems themselves
        if ($this instanceof MissileLauncher || $this instanceof ReloadRack){
            $crit = $this->testAmmoExplosion($ship, $gamedata);
            $crits[] = $crit;
        }
        else */
	/*SubReactor is now obsoleted, replaced by SubReactorUniversal
	if ($this instanceof SubReactor){
            //debug::log("subreactor, multiple damage by 0.5");
            $damageMulti = 0.5;
        }
	*/

        $roll = Dice::d(20) + floor(($this->getTotalDamage())*$damageMulti) + $add +$bonusCrit;
        $criticalTypes = -1;

        foreach ($this->possibleCriticals as $i=>$value){
            if ($roll >= $i){
                $criticalTypes = $value;
            }
        }            
        if ($criticalTypes != -1){
            if (is_array($criticalTypes)){
                foreach ($criticalTypes as $phpclass){
                    $crit = $this->addCritical($ship->id, $phpclass, $gamedata);
                    if ($crit)
                        $crits[] = $crit;
                }
            }else{
                $crit = $this->addCritical($ship->id, $criticalTypes, $gamedata);
                if ($crit)
                    $crits[] = $crit;
            }
        }
        return $crits;         
    }
	
    
    public function addCritical($shipid, $phpclass, $gamedata){
        $crit = new $phpclass(-1, $shipid, $this->id, $phpclass, $gamedata->turn);
        $crit->updated = true;
		$this->setCritical($crit);
//        $this->criticals[] =  $crit;
        return $crit;
    }

// add GTS
	//Standard procedure for repairing a critical, called from selfRepair system.
    public function repairCritical($critDmg, $turn){
        $critDmg->turnend = $turn;//actual repair 😉
        $critDmg->forceModify = true; //actually save the repair...
        $critDmg->updated = true; //actually save the repair cd!...

    }//endof repairCritical()    
// end add GTS	
    
    public function hasCritical($type, $turn = false){
        $count = 0;
		if($turn===false){ //this only means it should be checked for CURRENT turn, not ANY turn!
			$turn=TacGamedata::$currentTurn;
		}
        foreach ($this->criticals as $critical){
            if (strcmp($critical->phpclass, $type) == 0 && $critical->inEffect){				
                if ($turn === false){ //now should never go here...
                    $count++;
                }else if ((($critical->oneturn && $critical->turn+1 == $turn) || !$critical->oneturn) && $critical->turn<= $turn){
			//additional test for turn of ending effect!
			if(($critical->turnend==0) || ($critical->turnend>=$turn)){
				$count++;
			}
                }
            }
        }
        return $count;
    }
    
    public function getOutput(){        
        if ($this->isOfflineOnTurn())
            return 0;
        
        if ($this->isDestroyed())
            return 0;
        
        $output = $this->output;
        $output += $this->outputMod; //outputMod negative is negative in itself!
		$output = max(0,$output); //don't let output be negative!
        return $output;
    }

    public function getOutputWhenOffline(){        
        
        $output = $this->output;
        $output += $this->outputMod; //outputMod negative is negative in itself!
		$output = max(0,$output); //don't let output be negative!
        return $output;
    }
    
    
    public function effectCriticals(){ 
        $percentageMod = 0;
        foreach ($this->criticals as $crit){
            $this->outputMod += $crit->outputMod;
			$percentageMod += $crit->outputModPercentage;
        }
        //convert percentage mod to absolute value...
        if($percentageMod != 0){
            //$this->outputMod += round($percentageMod * $this->output /100 );
		$this->outputMod += round($percentageMod * $this->output /100 );
        }    
    }
	
    
    public function getTotalDamage($turn = false){
        $totalDamage = 0;
        
        foreach ($this->damage as $damage){
			if($turn && $damage->turn > $turn) continue; //if damage is from turn further than called for - skip it!
            $d = ($damage->damage - $damage->armour); //procedure of creating DamageEntry objects guarantees that this is non-negative unless actual healing happened!
            //if ( $d < 0 && ($damage->turn <=$turn || $turn === false)) $d = 0;
                
            $totalDamage += $d;
        }        
        return $totalDamage;    
    }
    

    public function isDestroyed($turn = false){
	    /*system is destroyed if it was destroyed directly, up to indicated turn*/
	    /*or if it's Structure system is destroyed AT LEAST ONE TURN EARLIER*/
	    $currTurn = TacGamedata::$currentTurn;
	    if($turn !== false) $currTurn = $turn;
		if($currTurn < TacGamedata::$currentTurn){ //if we're looking for past game state, system is destroyed even if structure was destroyed on the same turn
			$prevTurn = $currTurn;
		}else{ //system has fallen off if structure was destroyed a turn earlier
			$prevTurn = $currTurn-1; 
		}
        
		/*18.02.2023: DO check base Structure for Structure, stopping at PRIMARY Structure :) */
        //if ( (!($this instanceof Structure)) && $this->structureSystem && $this->structureSystem->isDestroyed($prevTurn)) return true;
		if ( !($this instanceof Structure) ) { //not Structure
			if ($this->structureSystem && $this->structureSystem->isDestroyed($prevTurn)) return true; //underlying Structure is destroyed
		} else if ($this->location!=0) { //Structure (checked earlier) but not PRIMARY one
			$primaryStruct = $this->unit->getStructureSystem(0);
			if($primaryStruct && $primaryStruct->isDestroyed($prevTurn)) return true;
		}
  
		$isDestroyed=false;
        foreach ($this->damage as $damage){ //was actually destroyed up to indicated turn
			//allow undestroying, too!
            if (($damage->turn <= $currTurn) && $damage->destroyed) $isDestroyed=true;
            if (($damage->turn <= $currTurn) && $damage->undestroyed) $isDestroyed=false;			
        }
  
        return $isDestroyed;        
    }
	
	

    public function wasDestroyedThisTurn($turn){
        foreach ($this->damage as $damage){
            if ($damage->turn == $turn && $damage->destroyed){
                return true;
            }
        }  
        return false;
    }

    
    public function isDamagedOnTurn($turn){
		if($this->forceCriticalRoll) return true; //allow forced crit roll        
        foreach ($this->damage as $damage){
            if ($damage->turn == $turn || $damage->turn == -1){
                if ($damage->damage > $damage->armour)
                    return true;
            }
        }        
        return false;    
    }
	
	public function damageReceivedOnTurn($turn){
		$damageReceived = 0;
        foreach ($this->damage as $damage){
            if ($damage->turn == $turn || $damage->turn == -1){
                if ($damage->damage > $damage->armour){
                    $damageReceived += $damage->damage - $damage->armour;
				}
            }
        }        
        return $damageReceived;    
    }
	
    
    public function getRemainingHealth(){
        $damage = $this->getTotalDamage();
        
        $rem = $this->maxhealth - $damage;
        if ($rem < 0 ) $rem = 0;
            
        return $rem;
    }
      
    public function isOfflineOnTurn($turn = null){
        if ($turn === null)
            $turn = TacGamedata::$currentTurn;
        
        if ($this->parentSystem && $this->parentSystem instanceof DualWeapon)
            return $this->parentSystem->isOfflineOnTurn($turn);
    
        foreach ($this->power as $power){
            if ($power->type == 1 && $power->turn == $turn){
                return true;
            }
        }
        
		/* no longer needed... I think... 
        if ($this->hasCritical("ForcedOfflineOneTurn", $turn-1)){
            return true;
        }*/
		

        if ($this->hasCritical("ForcedOfflineForTurn")){
            return true;
        }
        
        return false;
    
    }
    
    public function isOverloadingOnTurn($turn = null){
        if ($turn === null)
            $turn = TacGamedata::$currentTurn;
        
//        if ($this->parentSystem && $this->parentSystem instanceof DualWeapon)
//            return $this->parentSystem->isOverloadingOnTurn($turn);
        
        foreach ($this->power as $power){
            if ($power->type == 3 && $power->turn == $turn){
                return true;
            }
        }
        
        return false;
    
    }
    
    public function onAdvancingGamedata($ship, $gamedata)
    {
    }
    
    public function setSystemData($data, $subsystem)
    {
    }
    
    public function setInitialSystemData($ship)
    {
    }
	
	
	public function doesProtectFromDamage($expectedDmg, $systemProtected = null, $damageWasDealt = false){ //hook - systems that can affect damage dealing will return positive value; strongest one will be chosen to interact
		return 0;
	}
	public function doProtect($gamedata, $fireOrder, $target, $shooter, $weapon, $systemProtected, $effectiveDamage,$effectiveArmor){ //hook for actual effect of protection - return modified values of damage and armor that should be used in further calculations
		$returnValues=array('dmg'=>$effectiveDamage, 'armor'=>$effectiveArmor);
		return $returnValues;
	}
	
	/*first attempt at StarTrek shield
	public function doesReduceImpactDamage($expectedDmg){ //hook - systems that can affect damage dealing at the moment of impact will return positive value; strongest one will be chosen to interact
		return 0;
	}
	public function doReduceImpactDamage($gamedata, $fireOrder, $target, $shooter, $weapon, $effectiveDamage){ //hook for actual effect of protection - return modified value of damage that should be used in further calculations
		return $effectiveDamage;
	}
	*/
	
	/*assigns damage, returns remaining (overkilling) damage and how much armor was actually pierced
	all extra data needed for defensive modifying damage as it's being dealt - like Shadow Energy Diffusers and Gaim Bulkheads
	returns array: dmgDealt, dmgRemaining, armorPierced
	special treatment for Flash: assign only as much damage as necessary to destroy system and treat it as an entire shot! (important for defensive systems such as Diffusers and Bulkheads)
	*/
	//public function assignDamageReturnOverkill($target, $shooter, $weapon, $gamedata, $fireOrder, $damage, $armour, $pos = null){ //earlier version
	public function assignDamageReturnOverkill($target, $shooter, $weapon, $gamedata, $fireOrder, $damage, $armour, $pos, $damageWasDealt){
		$returnArray = array("dmgDealt"=>0, "dmgRemaining"=>0, "armorPierced"=>0);
		$effectiveDamage = $damage;
		$remainingDamage = 0;
		$overkillDamage = 0;
		$effectiveArmor = $armour;
		$systemHealth = $this->getRemainingHealth();
		$systemDestroyed = false;

		//Before we look at armour, make any reductions due to Sustained fire from a previous turn(s)	
		if ($weapon->isOverloadingOnTurn($gamedata->turn) && $weapon->loadingtime <= $weapon->overloadturns) {		
			$sustainedSystemsHit = $weapon->getsustainedSystemsHit();
			if($sustainedSystemsHit != null){			
				$intSustainedSystemsHit = array_map('intval', $sustainedSystemsHit); // Convert strings to integers					
				$counts = array_count_values($intSustainedSystemsHit); // Count occurrences
				
				$armourPiercedAlready = isset($counts[$this->id]) ? $counts[$this->id] : 0; // Get count for $this->id				
				$effectiveArmor = max(0, $effectiveArmor - $armourPiercedAlready); //Reduce armour by amount pierced n previous turns by Sustained shots.	
			}        
		}

		if($weapon->damageType =='Flash'){//treat only enough damage to destroy system as entire shot! the rest is only overkill 
			$maxDamageToDeal = $systemHealth +$effectiveArmor;
			if ($maxDamageToDeal < $effectiveDamage){
				$overkillDamage = $effectiveDamage - $maxDamageToDeal;
				$effectiveDamage = $maxDamageToDeal;
			}
		}
		$expectedDmg = max(0,$effectiveDamage-$effectiveArmor);
		
		
		//CALL SYSTEMS PROTECTING FROM DAMAGE HERE! 
		$systemProtectingDmg = $target->getSystemProtectingFromDamage($shooter, $pos, $gamedata->turn, $weapon, $this, $expectedDmg, $damageWasDealt);
		if($systemProtectingDmg){
			$effectOfProtection = $systemProtectingDmg->doProtect($gamedata, $fireOrder, $target, $shooter,$weapon,$this,$effectiveDamage,$effectiveArmor);
			$effectiveDamage = $effectOfProtection['dmg'];
			$effectiveArmor = $effectOfProtection['armor'];
		}
		
		
		//if damage is more than system has structure left - mark rest as overkilling
		if ($effectiveDamage-$effectiveArmor >= $systemHealth){
			$systemDestroyed = true;
			$remainingDamage = $effectiveDamage-$effectiveArmor - $systemHealth;
			$effectiveDamage = $effectiveDamage-$remainingDamage;
		}
				
		//if damage remaining is less than affecting armor - decrease armor appropriately
		if ($effectiveDamage < $effectiveArmor) $effectiveArmor = $effectiveDamage;
		
		//mark damage done
		$damageEntry = new DamageEntry(-1, $target->id, -1, $fireOrder->turn, $this->id, $effectiveDamage, $effectiveArmor, 0, $fireOrder->id, $systemDestroyed, false, "", $weapon->weaponClass, $shooter->id, $weapon->id);
		$damageEntry->updated = true;
		$this->damage[] = $damageEntry;
		
		//Flash vs fighters: if fighter was not destroyed and not all damage was allocated, overkill into SAME fighter immediately! (until damage pool is depleted or fighter is destroyed)
		if( ($weapon->damageType =='Flash')
		  && ($overkillDamage > 0)
		  && (!$systemDestroyed)
		  && ($this instanceOf Fighter)
		){
			$remainingDamage = $remainingDamage+$overkillDamage;
			$overkillDamage = 0; //just poured into remaining damage
			
			$receivedArray = $this->assignDamageReturnOverkill($target, $shooter, $weapon, $gamedata, $fireOrder, $remainingDamage, $armour, $pos, $damageWasDealt);
			
			$effectiveDamage += $receivedArray["dmgDealt"]; //sum of dealt now and in recursive call
			$remainingDamage = $receivedArray["dmgRemaining"]; //should be 0 unless fighter was destroyed
			//armor pierced stays the same...
		}		

		$returnArray["dmgDealt"] = $effectiveDamage;
		$returnArray["dmgRemaining"] = $remainingDamage+$overkillDamage; //add damage set aside at the start of the procedure
		$returnArray["armorPierced"] = $effectiveArmor;
		return $returnArray;
	}

}


?>
