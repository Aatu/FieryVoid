<?php

class Jammer extends ShipSystem implements SpecialAbility{    
    public $name = "jammer";
    public $displayName = "Jammer";
    public $specialAbilities = array("Jammer");
    public $primary = true;
	
	//Jammer is very important, being the primary defensive system!
	public $repairPriority = 10;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
    
    protected $possibleCriticals = array(16=>"PartialBurnout", 23=>"SevereBurnout");
    
    function __construct($armour, $maxhealth, $powerReq){
        parent::__construct($armour, $maxhealth, $powerReq, 1);
    }
    
    //args for Jammer ability are array("shooter", "target")
    public function getSpecialAbilityValue($args)
    {
        if (!isset($args["shooter"]) || !isset($args["target"]))
            throw new InvalidArgumentException("Missing arguments for Jammer getSpecialAbilityValue");
        
        $shooter = $args["shooter"];
        $target = $args["target"];
        
        if ($shooter->faction === $target->faction) return 0; //same-faction units ignore Jammer
		
        if (! ($shooter instanceof BaseShip) || ! ($target instanceof BaseShip)) 
            throw new InvalidArgumentException("Wrong argument type for Jammer getSpecialAbilityValue");
        		
		$jammerValue = $this->getOutput();
		
		if ($jammerValue > 0){ //else no point
			//Advanced Sensors negate Jammer, Improved Sensors halve Jammer
			if ($shooter->hasSpecialAbility("AdvancedSensors")) {
				$jammerValue = 0; //negated
			} else if ($shooter->hasSpecialAbility("ImprovedSensors")) {
				$jammerValue = $jammerValue * 0.5; //halved
			}
		} else {
			$jammerValue = 0; //never negative
		}
			
        return $jammerValue;
    }

    public function setSystemDataWindow($turn){
        parent::setSystemDataWindow($turn);
        $this->data["Special"] = "Denies a hostile OEW-lock versus this ship.";
        $this->data["Special"] .= "<br>Doesn't work ws own faction (eg. Minbari Jammer won't work against hostile Minbari).";
		$this->data["Special"] .= "<br>Enabling/Disabling Jammer will affect enemy missile launches on NEXT turn!";	     
    }
} //endof Jammer

class Stealth extends ShipSystem implements SpecialAbility{    
    public $name = "stealth";
    public $displayName = "Stealth systems";
    public $specialAbilities = array("Jammer", "Stealth");
    public $primary = true;
	public $detected = false; // Legacy fallback
	public $detectedNew = array(); // Multi-team tracking array
    
    function __construct($armour, $maxhealth, $powerReq){
        parent::__construct($armour, $maxhealth, $powerReq, 1);
    }
    
    public function setSystemDataWindow($turn){
			$unit = $this->getUnit();
			if($unit instanceof FighterFlight){
            	$this->data["Special"] = "Jammer ability if targeted from over 5 hexes away.";
				$this->data["Special"] .= "<br>Cannot be targeted by ballistic weapons at all from over 5 hexes away.";
			}else{
            	$this->data["Special"] = "Ship is invisible to enemies until reveals itself or is detected.";
				$this->data["Special"] .= "<br>It is revealed immediately if any EW abilities (other than DEW) are used or fires a weapon.";
				$this->data["Special"] .= "<br>Can also be detected by enemy ships at start of Firing Phase if in range (See FAQ for full rules).";
				$this->data["Special"] .= "<br>Once detected ship is revealed it will remain this way unless it breaks line of sight with all enemy ships.";								
				$this->data["Special"] .= "<br>Jammer ability if targeted over 12 hexes away by ships (Fighters - 4 hexes / Bases 24 hexes).";
			}	
	}	
    
    //args for Jammer ability are array("shooter", "target")
    public function getSpecialAbilityValue($args)
    {
        if (!isset($args["shooter"]) || !isset($args["target"]))
            throw new InvalidArgumentException("Missing arguments for Stealth getSpecialAbilityValue");
        
        $shooter = $args["shooter"];
        $target = $args["target"];
        
        if (! ($shooter instanceof BaseShip) || ! ($target instanceof BaseShip))
            throw new InvalidArgumentException("Wrong argument type for Stealth getSpecialAbilityValue");
		
        $jammerValue = 0; 
		if ($target instanceof FighterFlight){
			    if (mathlib::getDistanceHex($shooter, $target) > 5) //kicks in for fighters over 5 hexes!
			        {
					$jammerValue = 1; 
						//Advanced Sensors negate Jammer, Improved Sensors halve Jammer
						if ($shooter->hasSpecialAbility("AdvancedSensors")) {
							$jammerValue = 0; //negated
						} else if ($shooter->hasSpecialAbility("ImprovedSensors")) {
							$jammerValue = $jammerValue * 0.5; //halved
						}
			        }
		}else{ //Ships
				$stealthDistance = 12;
				if($shooter instanceof FighterFlight) $stealthDistance = 4;
				if($shooter->base) $stealthDistance = 24;

			    if (mathlib::getDistanceHex($shooter, $target) > $stealthDistance) //Define range jammer ability applies depending on shooter.
 				{						
			    	$jammerValue = 1; 
						//Advanced Sensors negate Jammer, Improved Sensors halve Jammer
						if ($shooter->hasSpecialAbility("AdvancedSensors")) {
							$jammerValue = 0; //negated
						} else if ($shooter->hasSpecialAbility("ImprovedSensors")) {
							$jammerValue = $jammerValue * 0.5; //halved
						}
			    }
			}	
        return $jammerValue;        
    }


	public function generateIndividualNotes($gameData, $dbManager){ //dbManager is necessary for Initial phase only
        $ship = $this->getUnit();
		if ($ship instanceof FighterFlight) return; //Fighter units don't need notes, they can't be invisible/detected.
		if($ship->isDestroyed()) return; //No point generating new notes if ship destroyed.
		if($ship->getTurnDeployed($gameData) > $gameData->turn)	return; //Ship not deployed yet.		

		$this->onIndividualNotesLoaded($gameData); //Check current detection status.
		if (!is_array($this->detectedNew)) $this->detectedNew = array();

        switch($gameData->phase){
			case 1: //Initial Orders - Check for any ballistic launches
				$newlyDetectingTeams = $this->isDetectedInitial($ship, $gameData);

				foreach ($newlyDetectingTeams as $teamId) {
					if (!in_array($teamId, $this->detectedNew)) {
						$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,'detected','Ship detected',"Team:" . $teamId);
						$this->detectedNew[] = $teamId; // update in memory for this phase
					}
				}
			break;
			case 4: //Firing phase Advance(), always called even if phase not needed in game.
				$newlyDetectingTeams = $this->isDetectedFire($ship, $gameData); 
				foreach ($newlyDetectingTeams as $teamId) {
					if (!in_array($teamId, $this->detectedNew)) {
						$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,'detected','Ship detected',"Team:" . $teamId);
						$this->detectedNew[] = $teamId; // update in memory for this phase
					}
				}

				$undetectedTeams = $this->isUndetected($ship, $gameData); 
				foreach ($undetectedTeams as $teamId) {
					// We only generate undetected notes against teams that previously detected us.
					// We also permit generation if the legacy boolean happens to be stuck 'true' from an old save format.
					if (in_array($teamId, $this->detectedNew) || $this->detected) {
						$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,'undetected','Ship undetected',"Team:" . $teamId);
						$this->detectedNew = array_values(array_diff($this->detectedNew, [$teamId]));
						$this->detected = false; // Crucial: ensure json_encode clears legacy visibility immediately!
					}
				}
			break;			
					
        }
    } //endof function generateIndividualNotes	
 	

	public function onIndividualNotesLoaded($gamedata){
		//Sort notes by turn, and then phase so latest detection note is always last.
		$this->sortNotes();
		if (!is_array($this->detectedNew)) $this->detectedNew = array();

		foreach ($this->individualNotes as $currNote){ //Search all notes, they should be process in order so the latest event applies.
			switch($currNote->notekey){
				case 'detected': 
						$this->detected = true; // Support legacy single-boolean saves
					if (strpos($currNote->notevalue, 'Team:') === 0) {
						$teamId = (int) substr($currNote->notevalue, 5);
						if (!in_array($teamId, $this->detectedNew)) {
							$this->detectedNew[] = $teamId;
						}
					}
				break;
				case 'undetected': 
						$this->detected = false; // Support legacy single-boolean saves
					if (strpos($currNote->notevalue, 'Team:') === 0) {
						$teamId = (int) substr($currNote->notevalue, 5);
						$this->detectedNew = array_values(array_diff($this->detectedNew, [$teamId]));
					}
				break;								
			}
		}
		//and immediately delete notes themselves, they're no longer needed (this will not touch the database, just memory!)
		$this->individualNotes = array();		
	} //endof function onIndividualNotesLoaded


	private function sortNotes() {
		usort($this->individualNotes, function($a, $b) {
			// Compare by turn first
			if ($a->turn == $b->turn) {
				// If turns are equal, compare by phase
				return ($a->phase < $b->phase) ? -1 : 1;
			}
			return ($a->turn < $b->turn) ? -1 : 1;
		});
	}


	private function isDetectedInitial($ship, $gameData) {
		$detected = false;

		foreach($ship->systems as $weapon){ //Check for weapon fire.
			if($weapon instanceof Weapon){
				if($weapon->firedOffensivelyOnTurn($gameData->turn)) {
					$detected = true;
					break;
				}	
			}
		}

		if (!$detected) {
			// If the ship used offensive or ELINT EW, it is revealed
			$usedEW = $ship->getAllEWExceptDEW($gameData->turn); // Has used any EW except DEW?
			if($usedEW > 0) $detected = true;
		}

		$detectedTeams = array();
		if ($detected) {
			// Revealed to all enemy teams
			foreach ($gameData->slots as $slot) {
				$teamId = (int)$slot->team;
				if ($teamId != $ship->team && !in_array($teamId, $detectedTeams)) {
					$detectedTeams[] = $teamId;
				}
			}
		}

		return $detectedTeams;
	}	

	//Callled from MovementPhaseStrategy->advance() at the end of movement round.				
	public function isDetectedMovement($ship, $gameData) {
		// Check all enemy ships to see if any can detect this ship at end of turn
		$blockedHexes = $gameData->blockedHexes; //Just do this once outside loop			
		$pos = $ship->getHexPos(); //Just do this once outside loop		

		if (!is_array($this->detectedNew)) $this->detectedNew = array();

		foreach ($gameData->ships as $otherShip) {
			// Skip friendly ships
			if($otherShip->team === $ship->team) continue;
			if($otherShip->isTerrain()) continue; //Ignore Terrain
			if($otherShip->isDestroyed()) continue; //Ignore destroyed enemy ships.
			if(in_array($otherShip->team, $this->detectedNew)) continue; // team already detects the ship
	
			$totalDetection = 0;
	
			if (!$otherShip instanceof FighterFlight) {
				if($otherShip->isDisabled()) continue;
				// Not a fighter — use scanner systems
				foreach($otherShip->systems as $system){
					if($system instanceof Scanner){
						if(!$system->isDestroyed() && !$system->isOfflineOnTurn()) $totalDetection += $system->output;
						break;
					}
				}	
				// Apply detection multiplier based on ship type
				if ($otherShip->base) {
					$totalDetection *= 5;
				} elseif ($otherShip->hasSpecialAbility("ELINT")) {
					$totalDetection *= 3;				
					$bonusDSEW = $otherShip->getEWByType("Detect Stealth", $gameData->turn);	
					$totalDetection += $bonusDSEW*2;
				} else {
					$totalDetection *= 2;
				}
			} else {
				// Fighter unit — use offensive bonus
				$totalDetection = $otherShip->offensivebonus;
			}
		
			// Get distance to the stealth ship and check line of sight
			$distance = mathlib::getDistanceHex($ship, $otherShip);
			$otherPos = $otherShip->getHexPos();          
			$noLoS = !empty($blockedHexes) && Mathlib::isLoSBlocked($pos, $otherPos, $blockedHexes);

			// If within detection range, and LoS not blocked the ship is detected
			if ($totalDetection >= $distance && !$noLoS) {  

				$note = new IndividualNote(
						-1,
						$gameData->id,
						$gameData->turn,
						$gameData->phase,
						$ship->id,
						$this->id,
						'detected',
						"Detected - M",
						"Team:" . $otherShip->team
				);

				Manager::insertIndividualNote($note);	

				$this->detectedNew[] = $otherShip->team; // Add to in-memory array so we don't insert duplicate notes for this team
			}
		}

		return false; //No other conditions were true, not detected.
	}

	private function isDetectedFire($ship, $gameData) {
		$detected = false;
		// If the ship has fired this turn, it is revealed
		foreach($ship->systems as $weapon){ //Check for weapon fire.
			if($weapon instanceof Weapon){
				$firingOrders = $weapon->getFireOrders($gameData->turn);
				foreach ($firingOrders as $fireOrder) { 
					if($fireOrder->type == "normal"){ //Ballistics already handled in Phase 1.
						$detected = true;
						break 2;
					}	
				}	
			}
		}

		$detectedTeams = array();
		if ($detected) {
			// Revealed to all enemy teams
			foreach ($gameData->slots as $slot) {
				$teamId = (int)$slot->team;
				if ($teamId != $ship->team && !in_array($teamId, $detectedTeams)) {
					$detectedTeams[] = $teamId;
				}
			}
		}
		return $detectedTeams;
	}		

	private function isUndetected($ship, $gameData) {		
		$blockedHexes = $gameData->blockedHexes; //Just do this once outside loop			
		$shipPosition = $ship->getHexPos(); //Save outside loop as this won't change.

		//Check for weapon fire.
		foreach($ship->systems as $weapon){ 
			if($weapon instanceof Weapon){
				$firingOrders = $weapon->getFireOrders($gameData->turn);
				foreach ($firingOrders as $fireOrder) { 
					if($fireOrder->type == "normal"){ 					
						return array(); // Cannot stealth against anyone
					}	
				}	
			}
		}

		$undetectedTeams = array();

		if (!is_array($this->detectedNew)) return array();

		// Check all enemy teams to see if we can stealth against them
		$enemyTeams = array();
		foreach ($gameData->slots as $slot) {
			$teamId = (int)$slot->team;
			if ($teamId != $ship->team && !in_array($teamId, $enemyTeams)) {
				$enemyTeams[] = $teamId;
			}
		}

		foreach ($enemyTeams as $teamId) {
			$canStealthAgainstTeam = true;

			if (!empty($blockedHexes)) {
				// Check all enemy ships in this team
				foreach($gameData->ships as $enemyShip){
					if($enemyShip->team != $teamId) continue;
					if($enemyShip->isTerrain()) continue; 
					if($enemyShip->isDestroyed()) continue; 

					$enemyPosition = $enemyShip->getHexPos();			
					$noLoS = Mathlib::isLoSBlocked($shipPosition, $enemyPosition, $blockedHexes);				

					if(!$noLoS){ // The enemy unit can see this ship
						$canStealthAgainstTeam = false;
						break; 
					}
				}
			} else {
				$canStealthAgainstTeam = false;
			}

			if ($canStealthAgainstTeam) {
				$undetectedTeams[] = $teamId;
			}
		}
 
		return $undetectedTeams;
	}	



	public function criticalPhaseEffects($ship, $gamedata) {	

		parent::criticalPhaseEffects($ship, $gamedata); // Call parent to apply base effects.
	
		// If Hyach Computer or Scanner is destroyed on Hyach Stealth ships, profile is increased by 3/15% permanently.
		if($ship->faction === "Hyach Gerontocracy"){
			if (!$ship instanceof FighterFlight && !$ship->isDestroyed()) {
				$scannerDestroyedThisTurn = false;
				$computerDestroyedThisTurn = false;
				$scannerPreviouslyDestroyed = false;
				$computerPreviouslyDestroyed = false;
		
				foreach ($ship->systems as $system) {
					if ($system instanceof Scanner && $system->isDestroyed()) {
						if ($system->wasDestroyedThisTurn($gamedata->turn)) {
							$scannerDestroyedThisTurn = true;
						} else {
							$scannerPreviouslyDestroyed = true;
						}
					}
					if ($system instanceof HyachComputer && $system->isDestroyed()) {
						if ($system->wasDestroyedThisTurn($gamedata->turn)) {
							$computerDestroyedThisTurn = true;
						} else {
							$computerPreviouslyDestroyed = true;
						}
					}
				}
		
				if (
					($scannerDestroyedThisTurn || $computerDestroyedThisTurn) &&
					!$scannerPreviouslyDestroyed && !$computerPreviouslyDestroyed
				) {
					$cnc = $ship->getSystemByName("CnC");
					if ($cnc) {
						for ($i = 0; $i < 3; $i++) {
							$crit = new ProfileIncreased(-1, $ship->id, $cnc->id, 'ProfileIncreased', $gamedata->turn + 1);
							$crit->updated = true;
							$cnc->criticals[] = $crit;
						}
					}
				}
			}
		}

	}//endof function criticalPhaseEffects


	public function stripForJson(){
        $strippedSystem = parent::stripForJson();
        $strippedSystem->detected = $this->detected;
        if (isset($this->detectedNew) && !empty($this->detectedNew)) {
            $strippedSystem->detectedNew = $this->detectedNew;
        }  			        
        //$strippedSystem->detectedNew = $this->detectedNew;	        
        return $strippedSystem;
    }

} //endof Stealth


class MineStealth extends ShipSystem implements SpecialAbility{    
    public $name = "mineStealth";
    public $displayName = "Stealth System";
	public $iconPath =  "stealth.png";
	public $isTargetable = false; //cannot be targeted ever!	
    public $specialAbilities = array("Stealth");
    public $primary = true;
	public $detected = array();
	public $canOffLine = true;
	protected $revealInfo = array();
    
    function __construct($armour, $maxhealth, $powerReq){
        parent::__construct($armour, $maxhealth, $powerReq, 1);
    }
    
    public function setSystemDataWindow($turn){
			$ship = $this->getUnit();	
			//$this->data["Special"] = "<br>Mine signature: " . $ship->signature;
            $this->data["Special"] = "<br>Ship is invisible to enemies until reveals itself by attacking or is detected.";
            $this->data["Special"] .= "<br>Once detected you can scan the mine to reveal information about its type etc.";			
			$this->data["Special"] .= "<br>Can be detected by enemy ships during Movement Phase if they have greater Detect Mines EW than Distance + Signature.";
			$this->data["Special"] .= "<br>See Fiery Void FAQ for more details on Mine detection.";														
	}	
    

    public function getSpecialAbilityValue($args){
        return $this->specialAbilityValue;        
    }


	public function generateIndividualNotes($gameData, $dbManager){ //dbManager is necessary for Initial phase only
        $mine = $this->getUnit();
		if (!$mine instanceof Mine) return; //This system is for mines only!
		if($mine->isDestroyed()) return; //No point generating new notes if ship destroyed.
		if($mine->getTurnDeployed($gameData) > $gameData->turn)	return; //Ship not deployed yet.		

		$this->onIndividualNotesLoaded($gameData); //Check current detection status.

		$enemyTeams = array();
		foreach($gameData->slots as $s) {
			if ($s->team !== $mine->team && !in_array($s->team, $enemyTeams)) {
				$enemyTeams[] = $s->team;
			}
		}

        switch($gameData->phase){
			case 1: //Initial Orders - Check for any ballistic launches
				if($mine->detectedSignature !== -1){
					$ballisticOrEWOrOffline = $this->isMineDetectedInitial($mine, $gameData);
				
					if($ballisticOrEWOrOffline){ //There was a ballistic launch this turn.  Create note for ship to be marked detected.
						foreach($enemyTeams as $team) {
							if (!is_array($this->detected)) $this->detected = array();
							if (!in_array($team, $this->detected)) {
								$notekey = 'detected';
								$noteHuman = 'Mine detected';
								$noteValue = $team;
								$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$mine->id,$this->id,$notekey,$noteHuman,$noteValue);
							}
						}					
					}
				}	

				$detailsRevealed = $this->isMineRevealedInitial($mine, $gameData);										
				if(!empty($detailsRevealed)){ //Someone has locked Mine with OEW to reveal it's info e.g. type. 
					foreach($detailsRevealed as $revealed){						
						if (!is_array($this->revealInfo)) $this->revealInfo = array();
						if (!in_array($revealed, $this->revealInfo)) {
							//Prepare note for database!		
							$notekey = 'infoRevealed';
							$noteHuman = 'Mine Info Revealed';
							$noteValue = $revealed; //Should be integer of team that knows mine info.
							$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$mine->id,$this->id,$notekey,$noteHuman,$noteValue);
						}							
					}					
				}
			break;

			case 2: //Movement phase Process()
				$detectingTeams = $this->isMineDetectedMovement($mine, $gameData);
				if(!empty($detectingTeams)){ 
					foreach($detectingTeams as $team) {
						if (!is_array($this->detected)) $this->detected = array();
						if (!in_array($team, $this->detected)) {
							$notekey = 'detected';
							$noteHuman = 'Mine detected';
							$noteValue = $team;
							$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$mine->id,$this->id,$notekey,$noteHuman,$noteValue);
						}
					}
				}	
			break;

			case 4: //Post-Firing phase Advance(), always called even if phase not needed in game.
				if($this->isMineDetectedFire($mine, $gameData)){ //Now check if mine just been detected by firing this turn		
					foreach($enemyTeams as $team) {
						if (!is_array($this->detected)) $this->detected = array();
						if (!in_array($team, $this->detected)) {
							$notekey = 'detected';
							$noteHuman = 'Mine detected';
							$noteValue = $team;
							$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$mine->id,$this->id,$notekey,$noteHuman,$noteValue);
						}
					}
					//DEW Mines need a separate note to show they are activated when they first fire and then use their lower signature
					if($mine->detectedSignature !== -1 && !$mine->activated){
							$notekey = 'activated';
							$noteHuman = 'Mine Activated';
							$noteValue = 1;
							$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$mine->id,$this->id,$notekey,$noteHuman,$noteValue);
					}					
				}
			break;			
					
        }
    } //endof function generateIndividualNotes	
 	

	public function onIndividualNotesLoaded($gamedata){
		//Sort notes by turn, and then phase so latest detection note is always last.
		$this->sortNotes();
		$mine = $this->getUnit();

		foreach ($this->individualNotes as $currNote){ //Search all notes, they should be process in order so the latest event applies.
			switch($currNote->notekey){
				case 'detected': 
					if(!is_array($this->detected)) $this->detected = array();
					if(!in_array($currNote->notevalue, $this->detected)) {
						$this->detected[] = $currNote->notevalue;
					}
				break;
				case 'infoRevealed': 
					if(!is_array($this->revealInfo)) $this->revealInfo = array();
					if(!in_array($currNote->notevalue, $this->revealInfo)) {
						$this->revealInfo[] = $currNote->notevalue;
					}
				break;	

				//DEW mine has fired, use lower signature
				case 'activated': 				
					if ($mine->detectedSignature !== -1){
						$mine->signature = $mine->detectedSignature;
						$mine->activated = true;						
					}  
				break;																	
			}
		}
		//and immediately delete notes themselves, they're no longer needed (this will not touch the database, just memory!)
		$this->individualNotes = array();		
	} //endof function onIndividualNotesLoaded


	private function sortNotes() {
		usort($this->individualNotes, function($a, $b) {
			// Compare by turn first
			if ($a->turn == $b->turn) {
				// If turns are equal, compare by phase
				return ($a->phase < $b->phase) ? -1 : 1;
			}
			return ($a->turn < $b->turn) ? -1 : 1;
		});
	}

	//DEW mines can fire.
	private function isMineDetectedInitial($mine, $gameData) {
        if($this->isOfflineOnTurn()) return true; //Stealth deactivated voluntarily.		

		foreach($mine->systems as $weapon){ //Check for weapon fire for DEW mines.
			if($weapon instanceof Weapon){
				if($weapon->firedOffensivelyOnTurn($gameData->turn)) {
					return true;
				}	
			}
		}

		// If the ship used offensive or ELINT EW, it is revealed
		$usedEW = $mine->getAllEWExceptDEW($gameData->turn); // Has used any EW except DEW?
		if($usedEW > 0) return true;

		return false;
	}	


		private function isMineRevealedInitial($mine, $gameData) {

		$toReturn = array();			

		foreach($gameData->ships as $otherShip){
			if($otherShip->team === $mine->team) continue; 
			if($otherShip->isTerrain()) continue; //Ignore Terrain
			if($otherShip->isDestroyed()) continue; //Ignore destroyed enemy ships.
			
			$oew = $otherShip->getOEW($mine, $gameData->turn);		
			if($oew > 0 && !in_array($otherShip->team, $toReturn)) {		
				$toReturn[] = $otherShip->team; //Record which team has revealed Mine details.
			}
		}			
		return $toReturn;
	}	

	public function isMineDetectedMovement($mine, $gameData){

		// Mines are stationary — their position is always their deploy-move position.
		// We deliberately avoid getHexPos() because mines have no subsequent movement
		// records and getHexPos() would crash on a null movement.
		$pos = null;
		foreach ($mine->movement as $move) {
			if ($move->type === 'deploy') {
				$pos = $move->position;
				break;
			}
		}
		if ($pos === null) return array(); // Mine has no deploy move yet, can't be detected

		$blockedHexes = $gameData->blockedHexes;
		$detectingTeams = array();

		foreach ($gameData->ships as $otherShip) {
			// Skip friendly ships
			if($otherShip->team === $mine->team) continue; 
			if($otherShip instanceof Terrain) continue; //Ignore Terrain
			if($otherShip instanceof Mine) continue; //Ignore other mines			
			if($otherShip->isDestroyed()) continue; //Ignore destroyed enemy ships.
	
			$totalDetection = 0;
	
			if(!$otherShip instanceof FighterFlight){
				if($otherShip->isDisabled()) continue;
				// Not a fighter — use scanner systems
				foreach($otherShip->systems as $system){
					if($system instanceof Scanner){
						if(!$system->isDestroyed() && !$system->isOfflineOnTurn()){ //Has functioning scanner!
							$totalDetection += $otherShip->getEWByType("Detect Mines", $gameData->turn);
							break; //No need ot look further.
						}
					}	
				}					
				//Apply mineSweeper bonus	
				if($otherShip->minesweeperbonus > 0) $totalDetection += $otherShip->minesweeperbonus;					
			} else{
				//$totalDetection = ceil($otherShip->offensivebonus / 2);
				$totalDetection += $otherShip->getEWByType("Detect Mines", $gameData->turn);
				
			}		
 
			// Use explicit OffsetCoordinates for distance/LoS so we never call getHexPos() on the mine
			$otherPos = $otherShip->getHexPos();
			$distance = mathlib::getDistanceHex($pos, $otherPos);
			$noLoS = !empty($blockedHexes) && Mathlib::isLoSBlocked($pos, $otherPos, $blockedHexes);
	
			// If within detection range, and LoS not blocked the mine is detected
			if (($totalDetection > $distance + $mine->signature) && !$noLoS) { 	
				if (!in_array($otherShip->team, $detectingTeams)) {
					$detectingTeams[] = $otherShip->team;
				}
			}
		}	

		return $detectingTeams;

	}

	//Runs at end of Initial Orers and Firing Phases.
	private function isMineDetectedFire($mine, $gameData) {

		// If the ship has fired this turn, it is revealed
		foreach($mine->systems as $weapon){ //Check for weapon fire.
			if($weapon instanceof Weapon){
				$firingOrders = $weapon->getFireOrders($gameData->turn);
				foreach ($firingOrders as $fireOrder) { 
					if($fireOrder->type == "normal"){ //Ballistics already handled in Phase 1.
						return true; //Just return, fired in Firing Phase revealing itself again even without LoS. Although who know at what without LoS...
					}	
				}	
			}
		}

		return false; //No other conditions were true, not detected.

	}
	
	public function markDetected($gameData, $mine) {

			$enemyTeams = array();
			$newindividualNotes = array();

			foreach($gameData->slots as $s) {
				if ($s->team !== $mine->team && !in_array($s->team, $enemyTeams)) {
					$enemyTeams[] = $s->team;
				}
			}

			foreach($enemyTeams as $team) {
				if (!is_array($this->detected)) $this->detected = array();
				if (!in_array($team, $this->detected)) {
					$notekey = 'detected';
					$noteHuman = 'Mine detected';
					$noteValue = $team;
					$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$mine->id,$this->id,$notekey,$noteHuman,$noteValue);
				}				
				
				foreach($newindividualNotes as $note){ //Insert notes directly into db.
					Manager::insertIndividualNote($note);	
				}
					
			}												
	}				

	public function markRevealed($gameData, $mine) {

		$enemyTeams = array();
		$newindividualNotes = array();

		foreach($gameData->slots as $s) {
			if ($s->team !== $mine->team && !in_array($s->team, $enemyTeams)) {
				$enemyTeams[] = $s->team;
			}
		}

		foreach($enemyTeams as $team) {	
			if (!is_array($this->revealInfo)) $this->revealInfo = array();
			if (!in_array($team, $this->revealInfo)) {
			//Prepare note for database!		
			$notekey = 'infoRevealed';
			$noteHuman = 'Mine Info Revealed';
			$noteValue = $team; //Should be integer of team that knows mine info.
			$newindividualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$mine->id,$this->id,$notekey,$noteHuman,$noteValue);
			}												
			
			foreach($newindividualNotes as $note){ //Insert notes directly into db.
				Manager::insertIndividualNote($note);	
			}
					
		}												
	}		


	public function criticalPhaseEffects($ship, $gamedata) {	
		parent::criticalPhaseEffects($ship, $gamedata); // Call parent to apply base effects.

	}//endof function criticalPhaseEffects


	public function stripForJson(){
        $strippedSystem = parent::stripForJson();
        if (isset($this->detected) && !empty($this->detected)) {
            $strippedSystem->detected = $this->detected;
        }  		
        //$strippedSystem->detected = $this->detected;
        if (isset($this->revealInfo) && !empty($this->revealInfo)) {
            $strippedSystem->revealInfo = $this->revealInfo;
        }  		
        //$strippedSystem->revealInfo = $this->revealInfo;			        
        return $strippedSystem;
    }

} //endof mineStealth


class Fighterimprsensors extends ShipSystem implements SpecialAbility{    
    public $name = "fighterimprsensors";
    public $displayName = "Improved Sensors";
    public $iconPath = "scannerTechnical.png";
    public $specialAbilities = array("ImprovedSensors");
    public $primary = true;
    
    function __construct($armour, $maxhealth, $powerReq){
        parent::__construct($armour, $maxhealth, $powerReq, 1);
    }
    
    public function setSystemDataWindow($turn){
		$this->data["Special"] = "Halves effectiveness of enemy Jammer (not that of advanced races)."; //not that of advanced races
	}
    
    public function getSpecialAbilityValue($args)
    {     
        return 1; //Improved Sensors just are       
    }     
} //endof Improved Sensors

class Fighteradvsensors extends ShipSystem implements SpecialAbility{    
    public $name = "Fighteradvsensors";
    public $displayName = "Advanced Sensors";
    public $iconPath = "scannerTechnical.png";
    public $specialAbilities = array("AdvancedSensors");
    public $primary = true;
    
    function __construct($armour, $maxhealth, $powerReq){
        parent::__construct($armour, $maxhealth, $powerReq, 1);
    }
    
    public function setSystemDataWindow($turn){
		$this->data["Special"] = "Ignores enemy Jammer."; //not that of advanced races
		//$this->data["Special"] .= "<br>Ignores enemy BDEW and SDEW."; //not that of advanced races (skipped as fighters ignore it anyway
		$this->data["Special"] .= "<br>Ignores any defensive systems lowering enemy profile (shields, EWeb...)."; //not that of advanced races
		$this->data["Special"] .= "<br>All of the above work as usual if operated by advanced races."; 
	}
    
    public function getSpecialAbilityValue($args)
    {     
        return 1; //Improved Sensors just are       
    }     
} //endof Advanced Sensors

interface SpecialAbility{
    public function getSpecialAbilityValue($args);
}

interface DefensiveSystem{
    public function getDefensiveType();
    public function getDefensiveHitChangeMod($target, $shooter, $pos, $turn, $weapon);
    public function getDefensiveDamageMod($target, $shooter, $pos, $turn, $weapon);
}

class Shield extends ShipSystem implements DefensiveSystem{
    public $name = "shield";
    public $displayName = "Shield";
    public $startArc = 0;
    public $endArc = 0;
    
    //defensive system
    public $defensiveSystem = true;
    public $tohitPenalty = 0;
    public $damagePenalty = 0;
    public $rangePenalty = 0;
    public $range = 5;
	
	public $isPrimaryTargetable = true; //can this system be targeted by called shot if it's on PRIMARY?
    
    protected $possibleCriticals = array(
            16=>"OutputReduced1",
            20=>"DamageReductionRemoved",
            25=>array("OutputReduced1", "DamageReductionRemoved"));

    function __construct($armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc){
        // shieldfactor is handled as output.
        parent::__construct($armour, $maxhealth, $powerReq, $shieldFactor);
        
        $this->startArc = (int)$startArc;
        $this->endArc = (int)$endArc;
    }
    
    public function setSystemDataWindow($turn){
        parent::setSystemDataWindow($turn);
		$damageReduction = $this->output;
		$profileReduction = $this->output *5;
		$this->data["Special"] = "Reduces damage done by incoming shots by $damageReduction."; 
		$this->data["Special"] .= "<br>Reduces hit chance of incoming shots by $profileReduction."; //not that of advanced races
		$this->data["Special"] .= "<br>Typical ship shields are ignored by fighter direct fire at range 0 (fighters flying under shields)."; 
	}
	
    public function onConstructed($ship, $turn, $phase){
        parent::onConstructed($ship, $turn, $phase);
		$this->tohitPenalty = $this->getOutput();
		$this->damagePenalty = $this->getOutput();
    }
    
    private function checkIsFighterUnderShield($target, $shooter, $weapon){
	if(!($shooter instanceof FighterFlight)) return false; //only fighters may fly under shield!
	if($weapon && $weapon->ballistic) return false; //fighter missiles may NOT fly under shield
        $dis = mathlib::getDistanceOfShipInHex($target, $shooter);
        if ( $dis == 0 ){ //If shooter are fighers and range is 0, they are under the shield
            return true;
        }
        return false;
    }
    
    public function getDefensiveType()
    {
        return "Shield";
    }
    
    public function getDefensiveHitChangeMod($target, $shooter, $pos, $turn, $weapon){
        if($this->isDestroyed($turn-1) || $this->isOfflineOnTurn($turn))
            return 0;
        
        if ($this->checkIsFighterUnderShield($target, $shooter, $weapon))
            return 0;

        $output = $this->output;
        $output += $this->outputMod; //outputMod itself is negative!
        return $output;
    }
    
    public function getDefensiveDamageMod($target, $shooter, $pos, $turn, $weapon){
        if($this->isDestroyed($turn-1) || $this->isOfflineOnTurn())
            return 0;
        
        if ($this->checkIsFighterUnderShield($target, $shooter, $weapon))
            return 0;
        
        if ($this->hasCritical('DamageReductionRemoved'))
            return 0;

		//Phased Gravitic Torpedo phasing: reduce absorption by the SUM of DamageReductionReduced params
		//(one crit per torpedo, amount in param), not the crit count. Min absorption 0.
		$redMod = $this->sumCriticalParam("DamageReductionReduced", $turn);

        $output = $this->output;
        $output += $this->outputMod; //outputMod itself is negative!
		$output -= $redMod;
        return max(0, $output);
    }
}

class EMShield extends Shield implements DefensiveSystem{
    public $name = "eMShield";
    public $displayName = "EM Shield";
    public $iconPath = "shield.png";
    public $canOffLine = true; //usually You don't want to, but Burst Beam can force it...

    function __construct($armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc){
        // shieldfactor is handled as output.
        parent::__construct($armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc);
    }
}

// GTS_Triad
class FlareShielding extends EMShield{
	public $name = "FlareShielding";
    public $displayName = "Flare Shielding";
    public $iconPath = "FlareShielding.png";

}

class GraviticShield extends Shield implements DefensiveSystem{
    public $name = "graviticShield";
    public $displayName = "Gravitic Shield";
    public $iconPath = "shield.png";
    public $canOffLine = true;

    function __construct($armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc){
        // shieldfactor is handled as output.
        parent::__construct($armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc);
    }
}

class ShieldGenerator extends ShipSystem{
	//Shield Generator repair priority is above average!
	public $repairPriority = 5;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
    
    public $name = "shieldGenerator";
    public $displayName = "Shield Generator";
    public $primary = true;    
    public $boostable = true;

    function __construct($armour, $maxhealth, $powerReq, $output ){
        parent::__construct($armour, $maxhealth, $powerReq, $output );
        
        $this->boostEfficiency = $powerReq;
    }    
}

class Reactor extends ShipSystem implements SpecialAbility {
    public $name = "reactor";
    public $displayName = "Reactor";
    public $primary = true;
    public $fixedPower = false; //important for MagGrav reactors, but defined here!
    public $outputType = "power";
	
	//Reactor is very important, being the ship heart!
	public $repairPriority = 10;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
    
	
    public $boostable = true; //for reactor overload feature!
    public $maxBoostLevel = 1;
    public $boostEfficiency = 0;
    
    protected $possibleCriticals = array(
        11=>"OutputReduced2",
        15=>"OutputReduced4",
        19=>"OutputReduced8",
        //27=>array("OutputReduced10", "ForcedOfflineOneTurn"));
		27=>array("OutputReduced10", "ContainmentBreach")
	);
    
    function __construct($armour, $maxhealth, $powerReq, $output ){
        parent::__construct($armour, $maxhealth, $powerReq, $output );        
    }
    
    public function addCritical($shipid, $phpclass, $gamedata) {
        if(strcmp($phpclass, "ForcedOfflineOneTurn") == 0){
            // This is the reactor. If it takes a ForcedOffLineForOneTurn,
            // propagate this crit to all systems that can be shut down.
            $ship = $gamedata->getShipById($shipid);
            if (!$ship instanceof StarBase){                
                foreach($ship->systems as $system){
                    if(($system->powerReq > 0) || $system instanceof Weapon){
                        $system->addCritical($shipid, $phpclass, $gamedata);
                    }
                }
            }
            else {
                foreach($ship->systems as $system){
                    if ($system->location == $this->location){
                        if(($system->powerReq > 0) || $system instanceof Weapon){
                            $system->addCritical($shipid, $phpclass, $gamedata);
                        }       
                    }
                }
            }
        }

        parent::addCritical($shipid, $phpclass, $gamedata);
    }
	
	
    public function isOverloading($turn){
        foreach ($this->power as $power){
            if ($power->turn == $turn && $power->type == 2){
                return true;
            }
        }
        return false;
    }
	
    public function setSystemDataWindow($turn){
        parent::setSystemDataWindow($turn);     
		if(!isset($this->data["Special"])){
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
        $this->data["Special"] .= "Can be set to overload, self-destroying ship after Firing phase.";	     
    }
	
    public function markPowerFlux(){
        $this->specialAbilities[] = "ReactorFlux";
        $this->specialAbilityValue = true; //so it is actually recognized as special ability!
        if (!isset($this->data["Special"])) {
            $this->data["Special"] = '';
        }else{
            $this->data["Special"] .= '<br>';
        }
        $this->data["Special"] .= '<br>Power fluctuations. Each turn, the reactor rolls for a critical, with a +5% penalty. Any effects last only 1 turn.';
    }

    public function getSpecialAbilityValue($args)
    {
        return $this->specialAbilityValue;
    }

	public function effectCriticals(){
		parent::effectCriticals();

	if (TacGamedata::$currentPhase <= 1) { 
		//account for Plasma Batteries present (if any)
		foreach ($this->unit->systems as $system)
			if ($system instanceof PlasmaBattery){
			$this->outputMod += $system->getOutput(); //outputMod is SUBTRACTED from base output, hence going for negative value here
			}
		}
	}	
	
	public function criticalPhaseEffects($ship, $gamedata) {	
	
		parent::criticalPhaseEffects($ship, $gamedata);//Call parent to apply effects like Limpet Bore.		
		
		//as effects are getting complicate - call them separately
		$this->executeContainmentBreach($ship, $gamedata);	
		$this->executeReactorFlux($ship, $gamedata);	
		$this->destroyShipOnDestruction($ship, $gamedata); //destroy ship on Reactor destruction
	}//endof function criticalPhaseEffects

	public function executeReactorFlux($ship, $gamedata) {		
		if ($this->isDestroyed()) return; //no point if Reactor is actually destroyed already
		$hasPowerFlux = $ship->hasSpecialAbility("ReactorFlux");
		if ($hasPowerFlux) {
			$roll = Dice::d(20) + 1 + $this->getTotalDamage();  //There is a +1 penalty in addition to any damage
			if($roll >= 11 && $roll < 15){ // Output reduced by 2 for one turn
				$finalTurn = $gamedata->turn + 1;
				$crit = new OutputReduced2(-1, $this->unit->id, $this->id, "OutputReduced2", $gamedata->turn, $finalTurn);
				$crit->updated = true;
				$crit->newCrit = true; // force save even if crit is not for current turn
				$this->criticals[] =  $crit;
			} elseif ($roll >=15 && $roll < 19) { // Output reduced by 4 for one turn
				$finalTurn = $gamedata->turn + 1;
				$crit = new OutputReduced4(-1, $this->unit->id, $this->id, "OutputReduced4", $gamedata->turn, $finalTurn);
				$crit->updated = true;
				$crit->newCrit = true; // force save even if crit is not for current turn
				$this->criticals[] =  $crit;
			} elseif ($roll >=19 && $roll < 27) { // Output reduced by 8 for one turn
				$finalTurn = $gamedata->turn + 1;
				$crit = new OutputReduced8(-1, $this->unit->id, $this->id, "OutputReduced8", $gamedata->turn, $finalTurn);
				$crit->updated = true;
				$crit->newCrit = true; // force save even if crit is not for current turn
				$this->criticals[] =  $crit;
			} elseif ($roll >=27) { // Output reduced by 10 for one turn
				$finalTurn = $gamedata->turn + 1;
				$crit = new OutputReduced10(-1, $this->unit->id, $this->id, "OutputReduced10", $gamedata->turn, $finalTurn);
				$crit->updated = true;
				$crit->newCrit = true; // force save even if crit is not for current turn
				$this->criticals[] =  $crit;
			}			
		}	
	}	//endof function executeReactorFlux
	
	//in case of containment breach - roll whether reactor explodes
	public function executeContainmentBreach($ship, $gamedata)
    { 
		if ($this->isDestroyed()) return; //no point if Reactor is actually destroyed already
		if (!$this->hasCritical("ContainmentBreach")) return; //no Containment Breach, everything is fine
			
		$explodeRoll = Dice::d(100);
		$chance = $this->getTotalDamage();
			
		//try to make actual attack to show in log - use Ramming Attack system!	- even if there is no explosion			
		$rammingSystem = $ship->getSystemByName("RammingAttack");
		$newFireOrder=null;
		if($rammingSystem){ //actually exists! - it should on every ship!
			$shotsHit = 0;
			if ($explodeRoll <= $chance) { //actual explosion
				$shotsHit = 1;
			}
			$newFireOrder = new FireOrder(
				-1, "normal", $ship->id, $ship->id,
				$rammingSystem->id, -1, $gamedata->turn, 1, 
				$chance, $explodeRoll, 1, $shotsHit, 0,
				0,0,'ContainmentBreach',10000
			);
			$newFireOrder->pubnotes = " Containment Breach - reactor explosion. Chance $chance %, roll $explodeRoll.";
			$newFireOrder->addToDB = true;
			$rammingSystem->fireOrders[] = $newFireOrder;
		}
			
		if ($explodeRoll <= $chance) { //actual explosion
			//destroy self		
			$remaining = $this->getRemainingHealth();
			$damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $this->id, $remaining, 0, 0, -1, true, false, "", "ContainmentBreach");
			$damageEntry->updated = true;
			$this->damage[] = $damageEntry;			
			if($rammingSystem){ //add extra data to damage entry - so firing order can be identified!
				$damageEntry->shooterid = $ship->id; //additional field
				$damageEntry->weaponid = $rammingSystem->id; //additional field
			}
		}
    } //endof function executeContainmentBreach
	
	

	//destroy PRIMARY section if Reactor is destroyed
	public function destroyShipOnDestruction($ship, $gamedata)
    {
		if (!$this->isDestroyed()) return; //Reactor is not destroyed, no need to act
		if ($this->unit->isDestroyed()) return; //entire ship is already destroyed, no need to act
	
		//try to make actual attack to show in log - use Ramming Attack system!				
		$rammingSystem = $ship->getSystemByName("RammingAttack");
		if($rammingSystem){ //actually exists! - it should on every ship!	
			//check whether firing order by RammingAttack vs own ship already exists!
			$newFireOrder = null;
			$fOrders = $rammingSystem->getFireOrders($gamedata->turn);
			foreach($fOrders as $fOrderAct){
				if($fOrderAct->targetid = $ship->id)
				{
					$newFireOrder = $fOrderAct;
					break; //foreach
				}
			}		
			if($newFireOrder){ //already exists, add to it!
				$newFireOrder->pubnotes .= "<br>";
			}else {//need actual new order!
				$newFireOrder = new FireOrder(
					-1, "normal", $ship->id, $ship->id,
					$rammingSystem->id, -1, $gamedata->turn, 1, 
					100, 100, 1, 1, 0,
					0,0,'Reactor',10000
				);
				$newFireOrder->addToDB = true;
				$rammingSystem->fireOrders[] = $newFireOrder;
			}
			$newFireOrder->pubnotes .= " Reactor destroyed - entire ship is immolated.";
		}else{
			$newFireOrder=null;
		}

		//destroy primary structure
		$primaryStruct = $this->unit->getStructureSystem(0);
		if($primaryStruct){			
            $remaining = $primaryStruct->getRemainingHealth();
            $damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $primaryStruct->id, $remaining, 0, 0, -1, true, false, "", "Reactor");
            $damageEntry->updated = true;
            $primaryStruct->damage[] = $damageEntry;			
			if($rammingSystem){ //add extra data to damage entry - so firing order can be identified!
					$damageEntry->shooterid = $ship->id; //additional field
					$damageEntry->weaponid = $rammingSystem->id; //additional field
			}
        }	
    } //endof function destroyShipOnDestruction
	
	
} //endof Reactor



class MagGravReactor extends Reactor{
/*Mag-Gravitic Reactor, as used by Ipsha (Militaries of the League 2);
	provides fixed power regardless of systems;
	techical implementation: count as Power minus power required by all systems enabled
*/	
	protected $possibleCriticals = array( //different set of criticals than standard Reactor
		13=>"FieldFluctuations",
		17=>array("FieldFluctuations", "FieldFluctuations"),
		21=>array("FieldFluctuations", "FieldFluctuations", "FieldFluctuations"),
		29=>array("FieldFluctuations", "FieldFluctuations", "FieldFluctuations", "ForcedOfflineOneTurn")
	);
	
	function __construct($armour, $maxhealth, $powerReq, $output ){
		parent::__construct($armour, $maxhealth, $powerReq, $output );    
		$this->fixedPower = true;
	}
	
	public function setSystemDataWindow($turn){
		$this->data["Output"] = $this->output;
		parent::setSystemDataWindow($turn);     
		$this->data["Special"] .= "<br>Mag-Gravitic Reactor: provides fixed total power, regardless of destroyed systems.";
	}	
	
}//endof MagGravReactor		


class MagGravReactorTechnical extends MagGravReactor{
/*Mag-Gravitic Reactor, but displayed in grey - as a technical system that cannot be damaged (Vorlons use it)
*/		
	protected $doCountForCombatValue = false;
    public $iconPath = "reactorTechnical.png";
	public $isTargetable = false; //cannot be targeted ever!	
    public $hideInShipWindow = true; //if true, system is omitted from ship-window icon grid (technical-only systems with no gameplay interaction)	
	public function setSystemDataWindow($turn){
		$this->data["Output"] = $this->output;
		parent::setSystemDataWindow($turn);     
		$this->data["Special"] = "This system is here for technical purposes only. Cannot be damaged in any way.";
		$ship = $this->getUnit();
		//I'm reusing this system in Asteroid unit, but don't want this text here - DK
		if($ship->factionAge > 2){
			$this->hideInShipWindow = false; //We do want to show it for Vorlons.
			$this->data["Special"] .= "<br>Mag-Gravitic Reactor: provides fixed total power, regardless of destroyed systems.";
			$this->data["Special"] .= "<br>System icon displays CURRENT power available to this vessel.";			
		}
	}		
}//endof MagGravReactor		

class AdvancedSingularityDrive extends Reactor{
/*Advanced version of Mag-Gravitic Reactor, used by custom Thirdspace faction;
	provides fixed power regardless of systems;
	techical implementation: count as Power minus power required by all systems enabled
*/	
    public $iconPath = "AdvancedSingularityDrive.png";
    
	protected $possibleCriticals = array( //different set of criticals than standard Reactor
		20=>"FieldFluctuations",
		25=>array("FieldFluctuations", "FieldFluctuations"),
		30=>array("FieldFluctuations", "FieldFluctuations", "FieldFluctuations")
	);
	
	function __construct($armour, $maxhealth, $powerReq, $output ){
		parent::__construct($armour, $maxhealth, $powerReq, $output );    
		$this->fixedPower = true;
	}
	
	public function setSystemDataWindow($turn){
		$this->data["Output"] = $this->output;
		parent::setSystemDataWindow($turn);     
		$this->data["Special"] .= "<br>Advanced Singularity Reactor: provides fixed total power, regardless of destroyed systems.";
		$this->data["Special"] .= "<br>'The power of the void, harnessed to their will...'";		
	}	
	
}//endof AdvancedSingularityDrive		


//warning: needs external code to function properly. Intended for starbases only.
/* let's disable it - all use changed to SubReactorUniversal!
class SubReactor extends ShipSystem{	
	//SubReactor is very important, though not as much as primary reactor itself!
	public $repairPriority = 8;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
    
	
    public $name = "reactor";
    public $displayName = "Reactor";
    public $outputType = "power";
    public $primary = false;
    
    protected $possibleCriticals = array(
        11=>"OutputReduced2",
        15=>"OutputReduced4",
        19=>"OutputReduced8",
        27=>"OutputReducedOneTurn"
    );
    
    function __construct($armour, $maxhealth, $powerReq, $output ){
        parent::__construct($armour, $maxhealth, $powerReq, $output );
    }
	
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);     
		$this->data["Special"] = "Secondary reactor: destruction will only destroy a section, not entire ship.";
	}	
	
    public function isOverloading($turn){
        return false;
    }	
}
*/



/*SubReactorUniversal - Sub-Reactor that can be fitted on any ship.
On destruction: will destroy the section it's fitted on.
On damage: will roll critical with half the effect of usual reactor and add that critical to primary reactor.
Official on damage: roll critical normally, it will only affect systems on the same section, maximum effect -10 (after cumulation)
*/
class SubReactorUniversal extends ShipSystem{
	public $name = "SubReactorUniversal";
    public $displayName = "Sub Reactor";
    public $iconPath = "reactor.png";
    public $primary = true; //well, it's intended to be fitted on outer sections, but treated as core system
	
	//SubReactor is very important, though not as much as primary reactor itself!
	public $repairPriority = 8;
    	
    protected $possibleCriticals = array(
        11=>"OutputReduced1", 
        14=>"OutputReduced2",
        17=>"OutputReduced3",
        21=>"OutputReduced4" //lower but also smoother
    );
		
	/*main reactor criticals for comparision
    protected $possibleCriticals = array(
        11=>"OutputReduced2",
        15=>"OutputReduced4",
        19=>"OutputReduced8",
        27=>array("OutputReduced10", "ForcedOfflineOneTurn"));
	*/
	
	function __construct($armour, $maxhealth){
        parent::__construct($armour, $maxhealth, 0, 0 ); 
    }
	
    public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
		$this->data["Special"] .= 'Critials roughly half as high as on main reactor; marked on main reactor.';
		$this->data["Special"] .= '<br>On destruction entire section will be destroyed (but not entire ship).';
    }
	
	//destroy section if destroyed
	public function criticalPhaseEffects($ship, $gamedata)
    { 
    
		parent::criticalPhaseEffects($ship, $gamedata);//Call parent to apply effects like Limpet Bore.	    
    
		if (!$this->isDamagedOnTurn($gamedata->turn)) return; 
		if (!$this->isDestroyed()) return;		
	
		//try to make actual attack to show in log - use Ramming Attack system!				
		$rammingSystem = $ship->getSystemByName("RammingAttack");
		if($rammingSystem){ //actually exists! - it should on every ship!				
			$newFireOrder = new FireOrder(
				-1, "normal", $ship->id, $ship->id,
				$rammingSystem->id, -1, $gamedata->turn, 1, 
				100, 100, 1, 1, 0,
				0,0,'Reactor',10000
			);
			$newFireOrder->pubnotes = " Sub Reactor destroyed - section is immolated.";
			$newFireOrder->addToDB = true;
			$rammingSystem->fireOrders[] = $newFireOrder;
		}else{
			$newFireOrder=null;
		}

		/*which block goes up? A sub reactor mounted on a PSEUDO-section (a "quarter" such as
		31/32/41/42, whose systems are homed on TWO real blocks via setStructureHome) has no
		structure block of its own - getStructureSystem() would silently fall back to PRIMARY
		and take the entire base with it. Detected by the fallback: the block handed back sits
		on a different location than we do.*/
		$ownStruct = $ship->getStructureSystem($this->location);
		$isPseudoSection = (!$ownStruct) || ($ownStruct->location != $this->location);

		if($isPseudoSection){
			/*immolate the quarter itself instead - everything displayed there dies, while both
			real home blocks are left intact. Killing either of them would be out of proportion
			(a quarter reactor is roughly half the size of a full section's) and killing both
			would take out half the base.*/
			foreach($ship->systems as $sys){
				if($sys === $this) continue; //already destroyed - that is why we are here
				if($sys->location != $this->location) continue; //not in this quarter
				if($sys instanceof Structure) continue; //a quarter has none, but never take a block out this way
				if($sys->isDestroyed()) continue;
				$damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $sys->id, $sys->getRemainingHealth(), 0, 0, -1, true, false, "", "Reactor");
				$damageEntry->updated = true;
				$sys->damage[] = $damageEntry;
				if($rammingSystem){ //add extra data to damage entry - so firing order can be identified!
						$damageEntry->shooterid = $ship->id; //additional field
						$damageEntry->weaponid = $rammingSystem->id; //additional field
				}
			}
			return;
		}

		//destroy own structure (systems on the section fall off with it)
		if($ownStruct){
            $remaining = $ownStruct->getRemainingHealth();
            $damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $ownStruct->id, $remaining, 0, 0, -1, true, false, "", "Reactor");
            $damageEntry->updated = true;
            $ownStruct->damage[] = $damageEntry;
			if($rammingSystem){ //add extra data to damage entry - so firing order can be identified!
					$damageEntry->shooterid = $ship->id; //additional field
					$damageEntry->weaponid = $rammingSystem->id; //additional field
			}
        }
    } //endof function criticalPhaseEffects
	
	
	//critical - add to primary reactor instead!
    public function addCritical($shipid, $phpclass, $gamedata) {
		//find main reactor
		$ship = $gamedata->getShipById($shipid);
		$mainReactor = $ship->getSystemByName("Reactor");
		if($mainReactor){
			$mainReactor->addCritical($shipid, $phpclass, $gamedata);
		}
		//do NOT call parent, as tis system will NOT actually suffer the crit!
        //parent::addCritical($shipid, $phpclass, $gamedata);
    }
}//endof class SubReactorUniversal


class Engine extends ShipSystem implements SpecialAbility {
    public $name = "engine";
    public $displayName = "Engine";
    public $thrustused;
    public $primary = true;
    public $boostable = true;
    public $outputType = "thrust";
	
	//Engine  is fairly important, being a core system!
	public $repairPriority = 7;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
    
    
    protected $possibleCriticals = array(
	//official: 15-20 -2, 21-27 either all ahead full or shutdown, 28+ both
        15=>"OutputReduced2",
        21=>"EngineShorted",      
        28=>array("EngineShorted", "OutputReduced2") 
		);
    
    function __construct($armour, $maxhealth, $powerReq, $output, $boostEfficiency, $thrustused = 0 ){
        parent::__construct($armour, $maxhealth, $powerReq, $output );
        
        $this->thrustused = (int)$thrustused;
        $this->boostEfficiency = (int)$boostEfficiency;
    }
	
    public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);   
		$this->data["Own thrust"] = $this->output;
    }
	
    public function markEngineFlux(){
        $this->specialAbilities[] = "EngineFlux";
        $this->specialAbilityValue = true; //so it is actually recognized as special ability!
        if (!isset($this->data["Special"])) {
            $this->data["Special"] = '';
        }else{
            $this->data["Special"] .= '<br>';
        }
        $this->data["Special"] .= 'Engine fluctuations. Each turn, the engine rolls for a critical, with a +5% penalty. Any effects last only 1 turn.';
    }

    public function getSpecialAbilityValue($args)
    {
        return $this->specialAbilityValue;
    }
/*
	public function getBoostLevel($turn){
		$boostLevel = 0;
		foreach ($this->power as $i){
				if ($i->turn != $turn) continue;
				if ($i->type == 2){
						$boostLevel += $i->amount;
				}
		}
	 		
		return $boostLevel;
	}
*/
	public function criticalPhaseEffects($ship, $gamedata) {
		
		parent::criticalPhaseEffects($ship, $gamedata);//Call parent to apply effects like Limpet Bore.		

		if($this->isDestroyed()) return; //Just to double-check.
					
		$hasEngineFlux = $ship->hasSpecialAbility("EngineFlux");

		if ($hasEngineFlux) {

			$roll = Dice::d(20) + 1 + $this->getTotalDamage();  //There is a +1 penalty in addition to any damage
			$finalTurn = $gamedata->turn + 1;
				
			if($roll >= 15 && $roll < 21){ // Output reduced by 2 for one turn

				$crit = new OutputReduced2(-1, $this->unit->id, $this->id, "OutputReduced2", $gamedata->turn, $finalTurn);
				$crit->updated = true;
				$crit->newCrit = true; // force save even if crit is not for current turn
				$this->criticals[] =  $crit;
			}elseif ($roll >= 21 && $roll < 28){ //EngineShorted
				$crit = new EngineShorted(-1, $this->unit->id, $this->id, "EngineShorted", $gamedata->turn, $finalTurn);
				$crit->updated = true;
				$crit->newCrit = true; // force save even if crit is not for current turn
				$this->criticals[] =  $crit;
			}elseif ($roll >= 28){ //Both!
				$crit = new EngineShorted(-1, $this->unit->id, $this->id, "EngineShorted", $gamedata->turn, $finalTurn);
				$crit->updated = true;
				$crit->newCrit = true; // force save even if crit is not for current turn
				$this->criticals[] =  $crit;	
				//Add second output crit.
				$crit2 = new OutputReduced2(-1, $this->unit->id, $this->id, "OutputReduced2", $gamedata->turn, $finalTurn);
				$crit2->updated = true;
				$crit2->newCrit = true; // force save even if crit is not for current turn
				$this->criticals[] =  $crit2;							
			}
		}
				
	}//endof criticalPhaseEffects


	public function doEngineShorted($ship, $gamedata){

		    $roll = Dice::d(20); // Roll the dice		    

		    // Create critical for offline, will always apply regardless of roll.
		    $crit = new ForcedOfflineOneTurn(-1, $this->unit->id, $this->id, "ForcedOfflineOneTurn", $gamedata->turn);
		    $crit->updated = true;
		    $crit->newCrit = true; // Force save even if crit is not for current turn
		    $this->setCritical($crit);

		    if ($roll < 15) {
		        // Engine offline next turn, no further actions
		        return;
		    }

		    // Engine offline + involuntary acceleration logic
		    $hasThrusters = Movement::hasMainThruster($ship);
		    $canAccelerate = Movement::canAccelerate($ship, $gamedata);

		    if (!$hasThrusters) {
		        // No thrusters, nothing else happens
		        return;
		    }

		    if ($canAccelerate) {
		        // Ship can accelerate, add ControlsStuck critical to be found by setPreturnMovementStatusForShip()
		        $crit2 = new ControlsStuck(-1, $this->unit->id, $this->id, "ControlsStuck", $gamedata->turn, $gamedata->turn+1);
		        $crit2->updated = true;
		        $crit2->newCrit = true;
		        $this->setCritical($crit2);		                
		    } else {
		        // No acceleration possible, damage primary structure
		        $this->doStressDamageToHull($ship, $gamedata);
		        
		    }
	
	}//endof doEngineShorted() 
    
    protected function doStressDamageToHull($ship, $gamedata){
    	
	    // Get primary structure
	    $primaryStructure = current(array_filter($ship->systems, function($system) {
	        return $system instanceof Structure && $system->location == 0;
	    }));

	    if (!$primaryStructure) {
	        return; // No primary structure found
	    }   	

    	$damageAble = $this->getOutput(); //Engine output = Damage
		$maxDamagePossible = $primaryStructure->getRemainingHealth(); //Can't do more than structure's current health.
		$damageCaused = min($damageAble, $maxDamagePossible); //Don't cause more damage than system's health remaining.		
		

		//Now create fireOrder to show the damage to the stressed hull.		          
		$rammingSystem = $ship->getSystemByName("RammingAttack");
		$newFireOrder = null;

		if ($rammingSystem) { // actually exists! - it should on every ship!
			$shotsHit = 1;
					
			$newFireOrder = new FireOrder(
				-1, "normal", $ship->id, $ship->id,
				$rammingSystem->id, -1, $gamedata->turn, 1,
				100, 100, 1, $shotsHit, 0,
				0, 0, 'Hull Stress', 10000
			);
					
			$newFireOrder->addToDB = true;
			$rammingSystem->fireOrders[] = $newFireOrder;
			$newFireOrder->pubnotes = "<br>This ship's engine malfunctions and tries to accelerate, dealing " . $damageCaused . " damage to the Primary Structure.";			
		}

		//Now actual damage entry
	    $isCriticalDamage = $damageCaused >= $primaryStructure->getRemainingHealth();

	    $damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $primaryStructure->id, $damageCaused, 0, 0, -1, $isCriticalDamage, false, "", "Hull Stress");
	    $damageEntry->updated = true;
	    $primaryStructure->damage[] = $damageEntry;
					
    }//endof doStressDamageToHull()    	


}//endof Engine class


class MindriderEngine extends Engine implements SpecialAbility{
    public $name = "engine";

	public $contraction = 0;
	private $changeThisTurn = 0;
    public $thrustused;
    public $primary = true;
    public $boostable = true;
    public $outputType = "thrust";	
    public $specialAbilities = array("MindriderEngine"); //Front end looks for this.
	public $specialAbilityValue = true; //so it is actually recognized as special ability!    		
	// this method generates additional non-standard information in the form of individual system notes, in this case: - Initial phase: check setting changes made by user, convert to notes.	
	
	public function getSpecialAbilityValue($args)
    {
		return $this->specialAbilityValue;
	}
	
	public function doIndividualNotesTransfer(){

		$contractionOnTurn = 0;	
	    if (is_array($this->individualNotesTransfer) && isset($this->individualNotesTransfer[0])) { // Check if it's an array and the key exists
	
		    $contractionOnTurn = $this->individualNotesTransfer[0];
	        $this->changeThisTurn = $contractionOnTurn;
	    }
	    	    
	    // Clear the individualNotesTransfer array
	    $this->individualNotesTransfer = array();
	}
	
		
    public function generateIndividualNotes($gameData, $dbManager){ //dbManager is necessary for Initial phase only
		$ship = $this->getUnit();
		switch($gameData->phase){
					
				case 2: //Movement phase
					//data returned as a number to update level of contraction.
					if($ship->userid == $gameData->forPlayer){ //only own ships, otherwise bad things may happen!
						//load existing data first - at this point ship is rudimentary, without data from database!
						$listNotes = $dbManager->getIndividualNotesForShip($gameData, $gameData->turn, $ship->id);	
						foreach ($listNotes as $currNote){
							if($currNote->systemid==$this->id){//note is intended for this system!
								$this->addIndividualNote($currNote);	 								
							}
						}
						$this->onIndividualNotesLoaded($gameData);		

						$changeValue = $this->changeThisTurn;//Extract change value for shield this turn.													
				
						if($changeValue != 0){												
							$notekey = 'contract';
							$noteHuman = 'Contraction value has been changed';
							$notevalue = $changeValue;
							$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$notevalue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue         
						}					
					}			
										
			break;				
		}
	} //endof function generateIndividualNotes
	

	public function onIndividualNotesLoaded($gamedata)
	{

		$ship = $this->getUnit();
		$contractValue = 0;//Initialise.
						
		foreach ($this->individualNotes as $currNote) {
			if($currNote->turn == $gamedata->turn){				    	
		        $contractValue += $currNote->notevalue;//Get value of Contraction this turn
			}    
		}

			$this->contraction = $contractValue;	

		if($contractValue == 0) return; //No effects this turn, just return.

			$ship->forwardDefense -= $this->contraction;
			$ship->sideDefense -= $this->contraction;
					
			//Now set current contraction
			foreach ($ship->systems as $system){//Increase Thought Shield amount by contraction level, and decrease profiles by same.
				
				if ($system instanceof ThoughtShield){
					
					//Need to temporarily lower/raise shield on turn Contraction happens.  Will reset at end of turn based on Generator value.
					foreach ($this->individualNotes as $currNote) {
						if($currNote->turn == $gamedata->turn){
							$system->applyContraction($ship, $gamedata, -$currNote->notevalue);				
						}
					}
				}

			}
		
			if($this->contraction >= 3){ //Additional effects after 3 levels of contraction.
				$ship->Enormous = false;
				//Reduce image size if needed! :)
				$level = floor($this->contraction/3);			
				$ship->imagePath = "img/ships/MindriderMindsEye" . $level . ".png";
				$ship->canvasSize = 280-($level *50);

				$armourBoost = floor($this->contraction/3);
				
				foreach ($ship->systems as $system){
					//Boost all Armour values except for Thought Shields		
					if (!$system instanceof ThoughtShield) $system->armour += $armourBoost;		
				}											
			}		

	        //and immediately delete notes themselves, they're no longer needed (this will not touch the database, just memory!)
	        $this->individualNotes = array();
			  
	}//endof onIndividualNotesLoaded


    public function setSystemDataWindow($turn){
	parent::setSystemDataWindow($turn);
		$this->data["Contraction Level"] = $this->contraction;		       	     
		$this->data["Special"] = "Allows Mind's Eye to Contract, improving its Thought Shields by 1 point and Defence Profiles by 5 points per level of Contraction.";
		$this->data["Special"] .= "<br>In addition, all Mind's Eye systems gain +1 armour for every 3 points of Contraction.";		
		$this->data["Special"] .= "<br>After the first three levels of Contraction applied, the Mind's Eye is no longer considered an Enormous unit.";		
    }


	//always redefine $this->data, variable information goes there...
	public function stripForJson(){
        $strippedSystem = parent::stripForJson();
        $strippedSystem->data = $this->data;	        
        $strippedSystem->contraction = $this->contraction;
        $strippedSystem->changeThisTurn = $this->changeThisTurn;        	
        return $strippedSystem;
    }
	
}//endof MindriderEngine


class Scanner extends ShipSystem implements SpecialAbility{ //on its own Scanner does not implement anything in particular, but classes ovverriding it do!
    public $name = "scanner";
    public $displayName = "Scanner";
    public $primary = true;
    public $boostable = true;
    public $outputType = "EW";
  
	//Scanner  is fairly important, being a core system!
	public $repairPriority = 7;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
    public $specialAbilityValue = false; //changed by modifications marking Improved/Advanced Sensors!
    
    protected $possibleCriticals = array(
        15=>"OutputReduced1",
        19=>"OutputReduced2",
        23=>"OutputReduced3",
        27=>"OutputReduced4");
    
    function __construct($armour, $maxhealth, $powerReq, $output ){
        parent::__construct($armour, $maxhealth, $powerReq, $output );
        
        $this->boostEfficiency = "output+1";
    }
    
    public function getOutput(){
        $output = parent::getOutput();
        if ($output === 0)
            return 0;
        
        foreach ($this->power as $power){
            if ($power->turn == TacGamedata::$currentTurn && $power->type == 2){
                $output += $power->amount;
            }        
        }        
        return $output;        
    }    
	
	/*functions adding Advanced/Improved Sensors trait*/
	public function markImproved(){		
		$this->specialAbilities[] = "ImprovedSensors";	
		$this->specialAbilityValue = true; //so it is actually recognized as special ability!		
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
		$this->data["Special"] .= 'Improved Sensors - halve Jammer effectiveness (to hit penalty and launch range penalty)(not that of advanced races).'; //not that of advanced races
	}
	public function markAdvanced(){		
    	$this->specialAbilities[] = "AdvancedSensors";
		$this->specialAbilityValue = true; //so it is actually recognized as special ability!
		$this->boostEfficiency = 14; //Advanced Sensors are rarely lower than 13, so flat 14 boost cost is advantageous to output+1!
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
		$this->data["Special"] .= 'Advanced Sensors - ignores Jammer, flat 14 boost cost.';//not that of advanced races
		$this->data["Special"] .= "<br>Ignores enemy BDEW, SDEW and DIST."; //not that of advanced races
		$this->data["Special"] .= "<br>Ignores any defensive systems lowering enemy profile (shields, EWeb...)."; //not that of advanced races
		$this->data["Special"] .= "<br>All of the above work as usual if operated by advanced races."; 
	}		

	public function markMindrider(){		
    	$this->specialAbilities[] = "AdvancedSensors";
    	$this->specialAbilities[] = "ConstrainedEW";    	
		$this->specialAbilityValue = true; //so it is actually recognized as special ability!
		$this->boostEfficiency = 14; //Advanced Sensors are rarely lower than 13, so flat 14 boost cost is advantageous to output+1!
		
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
		$this->data["Special"] .= '<br>Advanced Sensors - ignores Jammer, flat 14 boost cost.';//not that of advanced races
		$this->data["Special"] .= "<br>Ignores enemy BDEW, SDEW and DIST and other defensive systems unless operated by Ancient races."; //not that of advanced races
		$this->data["Special"] .= '<br>CONSTRAINED - All ELINT effects below each cost 1 extra EW.';//not that of advanced races		
	}
	
	public function markThirdspace(){	
		$this->iconPath = "Thirdspacescanner.png";			
    	$this->specialAbilities[] = "AdvancedSensors";
		$this->specialAbilityValue = true; //so it is actually recognized as special ability!
    	$this->boostEfficiency = 14; //Advanced Sensors are rarely lower than 13, so flat 14 boost cost is advantageous to output+1!
    	$this->maxBoostLevel = 2; //Unlike Shadows/Vorlons Thirdspace ships have alot of spare power, so limit their max sensor boost for balance. 		
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
		$this->data["Special"] .= '<br>Ignores enemy Jammers, BDEW, SDEW and DIST.';//not that of advanced races
		$this->data["Special"] .= "<br>Also ignores any defensive systems lowering enemy profile (shields, EWeb...)."; //not that of advanced races
		$this->data["Special"] .= "<br>All of the above work as usual if operated by Ancient races.";
		$this->data["Special"] .= "<br>Can only be boosted twice, for " . $this->boostEfficiency . " power each boost.";	
		$this->data["Special"] .= "<br>'You can feel them, reaching into your mind...'";		 
	}	
		
	/*note: StarWarsSensors mark in itself doesn't do anything beyond being recognizable for ship description function
		all actual effects are contained in attribute changes
	*/
	public function markStarWars(){		
    	$this->specialAbilities[] = "StarWarsSensors";
		$this->specialAbilityValue = true; //so it is actually recognized as special ability!
		$this->maxBoostLevel = 2;
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
		$this->data["Special"] .= 'Star Wars Sensors - boostability limited to +2.';
	}
	
	public function markAntiquated(){		
    	$this->specialAbilities[] = "AntiquatedSensors";
		$this->specialAbilityValue = true; //so it is actually recognized as special ability!
		$this->maxBoostLevel = 0;
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
		$this->data["Special"] .= 'Antiquated Sensors cannot be boosted.';
	}

    public function markHyach(){
        $this->specialAbilities[] = "HyachSensors";
        $this->specialAbilityValue = true; //so it is actually recognized as special ability!
        if (!isset($this->data["Special"])) {
            $this->data["Special"] = '';
        }else{
            $this->data["Special"] .= '<br>';
        }
        $this->data["Special"] .= 'Damage sustained by Hyach sensors is halved for purposes of critical rolls.';
    }

	public function testCritical($ship, $gamedata, $crits, $add=0){ 
		$hasHyachSensors = $ship->hasSpecialAbility("HyachSensors");
		$damageBonus = 0;
		if( $hasHyachSensors){
			$damageBonus = -round($this->getTotalDamage() /2); //half of current damage, rounded
		}
		$this->critRollMod += $damageBonus; //apply bonus
		$critsReturn = parent::testCritical($ship, $gamedata, $crits); //add appropriate critical(s)
		$this->critRollMod -= $damageBonus; //unapply bonus
		return $critsReturn; //return new set of critical damage
	}
	

	/*note: LCV Sensors are (or will be) checked at committing Initial Orders, in front end. All but 2 EW points need to be OEW. 
	This is Sensor trait rather than being strictly tied to hull size - while no larger units have it, of LCVs themselves only Young ones have it more or less universally.
	More advanced factions usually do not, and for custom factions it's up to their creator.
	*/
	public function markLCV(){		
    		$this->specialAbilities[] = "LCVSensors";
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
		$this->data["Special"] .= 'LCV Sensors - up to 2 EW points may be allocated freely. All surplus can be allocated ONLY as OEW.';
	}	

    public function markSensorFlux(){
        $this->specialAbilities[] = "SensorFlux";
        $this->specialAbilityValue = true; //so it is actually recognized as special ability!
        if (!isset($this->data["Special"])) {
            $this->data["Special"] = '';
        }else{
            $this->data["Special"] .= '<br>';
        }
        $this->data["Special"] .= 'Sensor fluctuations. Each turn, the sensor rolls for a critical, with a +5% penalty. Any effects last only 1 turn.';
    }

	public function getSpecialAbilityValue($args)
    {
		return $this->specialAbilityValue;
	}

	public function criticalPhaseEffects($ship, $gamedata) {	
	
		parent::criticalPhaseEffects($ship, $gamedata);//Call parent to apply effects like Limpet Bore.		
		
		$hasSensorFlux = $ship->hasSpecialAbility("SensorFlux");
		if ($hasSensorFlux) {
			$roll = Dice::d(20) + 1 + $this->getTotalDamage();  //There is a +1 penalty in addition to any damage

			if($roll >= 15 && $roll < 19){ // Output reduced by 1 for one turn
				$finalTurn = $gamedata->turn + 1;
				$crit = new OutputReduced1(-1, $this->unit->id, $this->id, "OutputReduced1", $gamedata->turn, $finalTurn);
				$crit->updated = true;
				$crit->newCrit = true; // force save even if crit is not for current turn
				$this->criticals[] =  $crit;
			} elseif ($roll >=19 && $roll < 23) { // Output reduced by 2 for one turn
				$finalTurn = $gamedata->turn + 1;
				$crit = new OutputReduced2(-1, $this->unit->id, $this->id, "OutputReduced2", $gamedata->turn, $finalTurn);
				$crit->updated = true;
				$crit->newCrit = true; // force save even if crit is not for current turn
				$this->criticals[] =  $crit;
			} elseif ($roll >=23 && $roll < 27) { // Output reduced by 3 for one turn
				$finalTurn = $gamedata->turn + 1;
				$crit = new OutputReduced3(-1, $this->unit->id, $this->id, "OutputReduced3", $gamedata->turn, $finalTurn);
				$crit->updated = true;
				$crit->newCrit = true; // force save even if crit is not for current turn
				$this->criticals[] =  $crit;
			} elseif ($roll >=27) { // Output reduced by 4 for one turn
				$finalTurn = $gamedata->turn + 1;
				$crit = new OutputReduced4(-1, $this->unit->id, $this->id, "OutputReduced4", $gamedata->turn);
				$crit->updated = true;
				$crit->newCrit = true; // force save even if crit is not for current turn
				$this->criticals[] =  $crit;
            }			
		}		
	}//endof function criticalPhaseEffects
	
} //endof Scanner


class ElintScanner extends Scanner implements SpecialAbility{
    public $name = "elintScanner";
    public $displayName = "ELINT Scanner";
    public $specialAbilities = array("ELINT");
    public $iconPath = "elintArray.png";
	//hit-chart alias (getSystemsByNameLoc matches displayName OR hitChartName): 26 hulls mount an
	//ELINT array as their ONLY scanner and chart that band as plain "Scanner" - without the alias
	//those rolls match nothing and silently drop through to Structure. Hulls that spell out
	//"ELINT Scanner" keep working (displayName still matches); baradaTomguScoutCruiser charts both
	//names but at different locations, so they still resolve to the intended array each.
	//NOTE: TAG: lookups (getSystemsByTag) do NOT consult hitChartName - use a tag for those.
	public $hitChartName = "Scanner";

    function __construct($armour, $maxhealth, $powerReq, $output ){
        parent::__construct($armour, $maxhealth, $powerReq, $output );
    }
    
    public function setSystemDataWindow($turn){
	parent::setSystemDataWindow($turn);     
	$boostability = $this->maxBoostLevel;		
	if (!isset($this->data["Special"])) {
		$this->data["Special"] = '';
	}else{
		$this->data["Special"] .= '<br>';
	}
        $this->data["Special"] .= "Allows additional Sensor operations (see FAQ for more details):";
        $this->data["Special"] .= "<br> - SOEW: Friendly ships gain half of ELINT ships' OEW bonus against targets. Requires line of sight to and being within 30 hexes of target and friendly ship at declaration and firing.";		     
        $this->data["Special"] .= "<br> - SDEW: Boosts target's DEW (by 1 for 2 points allocated). Range 30 hexes at declaration and firing.";		     
        $this->data["Special"] .= "<br> - Blanket Protection: All friendly units within 20 hexes (incl. fighters) get +1 DEW per 4 points allocated. Cannot combine with other ELINT activities.";		     
        $this->data["Special"] .= "<br> - Disruption: Reduce enemy ships' OEW / CCEW by 1 per 3 points allocated (split evenly, cannot bring OEW on a target below 0). Range 30 hexes at both declaration and firing.";	
		$this->data["Special"] .= "<br> - Detect Stealth: Increases stealth detection range of this ship by +2 per point of EW.";
		$this->data["Special"] .= "<br> - Jamming: Can try to jam remote controlled units e.g. Orieni HKs.";				    
	}
	/*
	public function markImproved(){	parent::markImproved();   }
	public function markAdvanced(){	parent::markImproved();	}
	*/

    public function markHyachELINT(){
        $this->specialAbilities[] = "HyachELINTSensors";
        $this->specialAbilityValue = true; //so it is actually recognized as special ability!
        if (!isset($this->data["Special"])) {
            $this->data["Special"] = '';
        }else{
            $this->data["Special"] .= '<br>';
        }
        $this->data["Special"] .= 'Damage sustained by Hyach sensors is halved for purposes of critical rolls.';
    }

	public function testCritical($ship, $gamedata, $crits, $add=0){ 
		$hasHyachELINTSensors = $ship->getSpecialAbilityValue("HyachELINTSensors");
		$damageBonus = 0;
		if( $hasHyachELINTSensors){
			$damageBonus = -round($this->getTotalDamage() /2); //half of current damage, rounded
		}
		$this->critRollMod += $damageBonus; //apply bonus
		$critsReturn = parent::testCritical($ship, $gamedata, $crits); //add appropriate critical(s)
		$this->critRollMod -= $damageBonus; //unapply bonus
		return $critsReturn; //return new set of critical damage
	}

    public function getSpecialAbilityValue($args)
    {
        return $this->specialAbilityValue;
    }

}


/* Chameleon Sensor Suite - an ELINT array that additionally disguises its ship as a different vessel.
   It IS the ship's ELINT array (it replaces ElintScanner in place), so "ELINT" MUST stay in $specialAbilities:
   redeclaring the property in a subclass REPLACES the parent's array, and dropping "ELINT" would silently
   strip SOEW/SDEW/Blanket Protection/Disruption/Jamming from the ship with no error raised.
*/
class ChameleonSensors extends ElintScanner implements SpecialAbility{
    public $name = "chameleonSensors";
    public $displayName = "Chameleon Sensors";
    public $specialAbilities = array("ELINT", "ChameleonSensors");
    public $iconPath = "ChameleonSensors.png";
	public $canOffline = true;

    function __construct($armour, $maxhealth, $powerReq, $output ){
        parent::__construct($armour, $maxhealth, $powerReq, $output );
    }

    public function setSystemDataWindow($turn){
	//parent::setSystemDataWindow($turn);
	if (!isset($this->data["Special"])) {
		$this->data["Special"] = '';
	}else{
		$this->data["Special"] .= '<br>';
	}
        $this->data["Special"] .= "<br>Chameleon Suite - Provide ELINT operations (see FAQ) and can project the image of a different vessel.";
		$this->data["Special"] .= "<br>Enemies see the simulacrum - icon, class, notes, defence profile and damage - instead of this ship.";
        $this->data["Special"] .= "<br>Deception is broken if ship moves, uses EW or weapons that the simulacrum could not do, or if an enemy is within 5 hexes (2 hexes for fighters and shuttles).";        
		//$this->data["Special"] .= "<br>The simulacrum is chosen when the ship is purchased. 'None' means no disguise is projected.";
        $this->data["Special"] .= "<br>Weapon arming status is always masked from enemies.";
        $this->data["Special"] .= "<br>More info in Centauri section of 'Factions-Tiers'.";
        $this->addChameleonWeaponBriefing();
        //$this->data["Special"] .= "<br>The deception is broken, permanently and for that enemy team only, if:";
        //$this->data["Special"] .= "<br> - an enemy unit closes to within 5 hexes (2 hexes for fighters and shuttles) with line of sight;";
        //$this->data["Special"] .= "<br> - the ship changes speed by more than the simulacrum's engines could manage;";
        //$this->data["Special"] .= "<br> - the ship runs ELINT operations the simulacrum could not run, or spends more EW than the simulacrum's sensors could produce;";
        //$this->data["Special"] .= "<br> - the ship fires a weapon the simulacrum does not mount (revealed at the start of the following turn);";
        //$this->data["Special"] .= "<br> - this array is destroyed, or the disguise is switched off.";
    }


	/*STAGE 6 - tell the player, in plain numbers, which of their guns the simulacrum can account for.

	  This is deliberately here rather than as the client-side declaration-time warning §Stage 6
	  sketched. That warning cannot be built as specified: the owner's page holds THIS ship's
	  blueprint, not the simulacrum's (window.staticShips is built from the phpclasses in the
	  viewer's own already-masked gamedata, finding #5), so the client has nothing to test against;
	  and a per-weapon "this will reveal you" flag computed server-side would be wrong as often as
	  right, because plausibility depends on the BEARING of the shot - the same Matter Cannon is
	  covered against a target dead ahead and uncovered against one off the port bow (proved in the
	  acceptance test at bearings 0 and 300). A standing loadout briefing is honest at every bearing;
	  a static flag would not be.

	  Own team only. It reaches nobody else in practice - an enemy who still believes the deception
	  is served the simulacrum's systems, so this system is not in their payload at all - but a ship
	  revealed to one team and not another does serve its real systems to the team that knows, and
	  there is no reason to hand them a summary as well.

	  Withdrawn entirely once the deception is over (2026-08-02). The whole briefing is a statement
	  about a risk - "firing this gun reveals you" - and once every enemy team has seen through the
	  disguise there is no risk left to describe, only a page of advice about a fiction. Same gate,
	  and for the same reason, as the "Disguised as" line in the Enhancements block.*/
	private function addChameleonWeaponBriefing(){
		$ship = $this->getUnit();
		if ($ship === null) return;
		if (empty($ship->chameleonDisguiseClass)) return;
		if (!$this->isProjecting()) return;
		if (TacGamedata::$currentForPlayerTeam !== null
			&& (int)TacGamedata::$currentForPlayerTeam !== (int)$ship->team) return;

		$blueprint = $ship->getChameleonBlueprint();
		if ($blueprint === null) return;

		$real = array(); $fake = array();
		foreach ($ship->systems as $s){
			if (!($s instanceof Weapon) || $s instanceof RammingAttack) continue;
			$cls = get_class($s);
			$real[$cls] = (isset($real[$cls]) ? $real[$cls] : 0) + 1;
		}
		foreach ($blueprint->systems as $s){
			if (!($s instanceof Weapon) || $s instanceof RammingAttack) continue;
			$cls = get_class($s);
			$fake[$cls] = (isset($fake[$cls]) ? $fake[$cls] : 0) + 1;
		}
		if (empty($real)) return;

		$label = ($blueprint->shipClass !== '' && $blueprint->shipClass !== null)
			? $blueprint->shipClass : $blueprint->phpclass;

		$lines = array();
		foreach ($real as $cls => $count){
			$have = isset($fake[$cls]) ? $fake[$cls] : 0;
			$name = $this->chameleonWeaponLabel($ship, $cls);
			$lines[] = ($have === 0)
				? "<br> - " . $name . ": you mount " . $count . ", the " . $label . " mounts NONE"
				: "<br> - " . $name . ": you mount " . $count . ", the " . $label . " mounts " . $have;
		}

		$this->data["Special"] .= "<br><br>Disguised as: " . $label . $this->getDeceivedTeamsLabel()
			. ". Firing a weapon the simulacrum"
			. " does not have - or more of them than it can bring to bear in that arc - reveals this ship";
			//. " to that enemy team.";
		$this->data["Special"] .= implode('', $lines);
		$this->data["Special"] .= "<br>Arcs count: the " . $label . " carries on the other side"
			. " cannot cover your shot.";
	}

	private function chameleonWeaponLabel($ship, $cls){
		foreach ($ship->systems as $s){
			if (get_class($s) === $cls) return $s->displayName;
		}
		return $cls;
	}

	/* ---------------- Reveal state machine (Stage 2) ----------------
	   Two pieces of state, both notes-backed in the ShadingField / Hyach-Stealth style:

	   $active        - the player's toggle. Default ON: a ship whose owner picked a simulacrum is
	                    projecting it. Round-trips through doIndividualNotesTransfer ->
	                    generateIndividualNotes -> onIndividualNotesLoaded.
	   $revealedTeams - enemy teams that have seen through the deception. PERMANENT: once a team is
	                    in this list it never comes out, and switching the suite back on does not
	                    restore the disguise for them.

	   Two note keys rather than one flag is what buys the "next turn" delay for free. A reveal
	   stamped 'revealedNow' applies from the turn it was written; 'revealedNextTurn' applies only
	   once the turn has rolled over, which is what the weapon-plausibility check (Stage 6) needs -
	   it runs at the END of a turn, after the enemy has already watched the shot land.*/
	public $active = true;              //is the suite projecting? (player toggle, D8)
	public $revealedTeams = array();    //teams this deception is broken against, permanently

	public function isActivated(){
		return $this->active;
	}

	/*THE question every consumer asks. Is this ship still wearing its false face, as far as $team
	  is concerned? Everything that can end the deception funnels through here.*/
	public function isDisguisedFrom($team, $turn){
		if (!$this->active) return false;
		if ($this->isDestroyed($turn)) return false;      //a shot-out array projects nothing
		if ($this->isOfflineOnTurn($turn)) return false;
		/*Observers (no team). D15 as written said "observers see the disguise", the code shipped the
		  opposite, and the user's ruling of 2026-08-01 settles it in favour of the plan but with a
		  condition: an observer sees the deception until it has broken against EVERY team, at which
		  point there is nobody left it is hidden from and showing an observer a fiction is just
		  wrong. Same shape as trueStealth, which hides a Torvalus hull from an observer for as long
		  as the per-team detection list has any team missing from it (TacGamedata line ~1162: a null
		  viewer team matches no entry in detectedNew).*/
		if ($team === null) return !$this->isRevealedToEveryTeam();
		return !in_array((int)$team, $this->revealedTeams, true);
	}

	/*"Has every OTHER team in this game seen through this?" - the observer's question, and the one
	  isFullyRevealed() answers when it has a $gamedata to read slots from. A system does not, so the
	  team list is taken off the per-load static TacGamedata::$chameleonAllTeams.

	  Fails CLOSED: with no team list (server-side processing outside a real load, static ship
	  generation) this returns false, so the observer keeps seeing the disguise. Showing a deception
	  to somebody who should not have seen it is a cosmetic error; undressing a ship that is still
	  hiding is a leak, and leaks are the failure mode this feature cannot have.*/
	private function isRevealedToEveryTeam(){
		$ship = $this->getUnit();
		if ($ship === null) return false;
		if (empty(TacGamedata::$chameleonAllTeams)) return false;

		$sawSomebody = false;
		foreach (TacGamedata::$chameleonAllTeams as $teamId){
			if ((int)$teamId === (int)$ship->team) continue; //its own team never counts as deceived
			$sawSomebody = true;
			if (!in_array((int)$teamId, $this->revealedTeams, true)) return false;
		}
		return $sawSomebody;
	}

	/*Enemy teams in this game. Shared by every reveal checkpoint.*/
	private function getEnemyTeams($gamedata, $ship){
		$enemyTeams = array();
		foreach ($gamedata->slots as $slot){
			$teamId = (int)$slot->team;
			if ($teamId != $ship->team && !in_array($teamId, $enemyTeams, true)) $enemyTeams[] = $teamId;
		}
		return $enemyTeams;
	}

	/*Write a reveal note for one team, unless that team already knows. $immediate picks the note key,
	  and therefore whether it bites this turn or next. Notes are written straight to the DB because
	  the reveal sweeps run inside phase advance(), outside the generateIndividualNotes/save pass.*/
	private function revealTo($gamedata, $ship, $teamId, $immediate, $reason){
		if (in_array((int)$teamId, $this->revealedTeams, true)) return; //already revealed
		$notekey = $immediate ? 'revealedNow' : 'revealedNextTurn';
		//tac_individual_notes.notekey_human is varchar(40) and silently FATALS the whole submission
		//on overflow ("Data too long for column") - keep reasons short and clamp anyway
		$note = new IndividualNote(
			-1, $gamedata->id, $gamedata->turn, $gamedata->phase, $ship->id, $this->id,
			$notekey, substr($reason, 0, 40), "Team:" . (int)$teamId
		);
		Manager::insertIndividualNote($note);
		if ($immediate) $this->revealedTeams[] = (int)$teamId; //effective at once, in this load too
	}

	/*D3a - the phantom would die while the real ship flies on. A destroyed hull that keeps
	  manoeuvring and shooting is a worse tell than anything else in this plan, so the deception
	  ends instead, immediately and for every enemy team. Called from BaseShip after a mirrored
	  allocation (D3); the caller does the clamping, this writes the reveal.*/
	public function revealOnDivergentDestruction($gamedata, $reason = 'Simulacrum destroyed'){
		$ship = $this->getUnit();
		if ($ship === null) return;
		foreach ($this->getEnemyTeams($gamedata, $ship) as $teamId){
			$this->revealTo($gamedata, $ship, $teamId, true, $reason);
		}
	}

	/*Have ALL enemy teams seen through this deception? Once they have there is nothing left to
	  protect, so the toggle button is withdrawn (in a 1v1 this is simply "revealed").*/
	public function isFullyRevealed($gamedata){
		$ship = $this->getUnit();
		if ($ship === null) return false;
		$enemyTeams = $this->getEnemyTeams($gamedata, $ship);
		if (empty($enemyTeams)) return false;
		foreach ($enemyTeams as $teamId){
			if (!in_array((int)$teamId, $this->revealedTeams, true)) return false;
		}
		return true;
	}

	private function addRevealedTeam($noteValue){
		if (strpos($noteValue, 'Team:') !== 0) return;
		$teamId = (int) substr($noteValue, 5);
		if (!in_array($teamId, $this->revealedTeams, true)) $this->revealedTeams[] = $teamId;
	}

	public function onIndividualNotesLoaded($gamedata){
		$this->sortNotes();

		foreach ($this->individualNotes as $currNote){
			switch($currNote->notekey){
				case 'Disguised':
					if($this->isToggleNoteCurrent($currNote, $gamedata)) $this->active = true;
				break;
				case 'Undisguised':
					if($this->isToggleNoteCurrent($currNote, $gamedata)) $this->active = false;
				break;
				case 'revealedNow':
					//written during the turn it happened, and observed as it happened
					if($currNote->turn <= $gamedata->turn) $this->addRevealedTeam($currNote->notevalue);
				break;
				case 'revealedNextTurn':
					//written at the END of a turn - invisible until the turn rolls over
					if($currNote->turn < $gamedata->turn) $this->addRevealedTeam($currNote->notevalue);
				break;
			}
		}

		$this->individualNotes = array(); //memory only - does not touch the DB
	}

	/*Same currency rule ShadingField uses for its Shaded/Unshaded pair: this turn's note wins, and
	  during the Deployment/pre-turn phase last turn's note still stands (it was written before the
	  turn counter moved on).*/
	private function isToggleNoteCurrent($note, $gamedata){
		if ($note->turn == $gamedata->turn) return true;
		return ($gamedata->phase == -1 && $note->turn == $gamedata->turn - 1);
	}

	private function sortNotes(){
		usort($this->individualNotes, function($a, $b){
			if ($a->turn == $b->turn) return ($a->phase < $b->phase) ? -1 : 1;
			return ($a->turn < $b->turn) ? -1 : 1;
		});
	}

	/*Set the moment a real toggle value arrives from the client. Manager.php consumes the transfer
	  at PARSE time - it calls doIndividualNotesTransfer() as soon as it has built the system, which
	  applies the value and clears the field - so by the time generateIndividualNotes runs there is
	  nothing left to distinguish "the player clicked Drop Disguise" from "this commit carried no
	  toggle at all". That difference matters: $active defaults to true on a freshly constructed
	  system, so without this flag a commit that sent nothing would silently re-project a disguise
	  the player had dropped.
	  PROTECTED deliberately: a public property here would land in the static ship JSON, which
	  json_encodes raw objects (see the static-bloat finding).*/
	protected $toggleTransferReceived = false;

	public function doIndividualNotesTransfer(){
		//client sends 1 to project the disguise, 0 to drop it - as a bare value or a one-element
		//array, depending on which client path built it, so accept both
		$transfer = $this->individualNotesTransfer;
		if (is_array($transfer)) $transfer = (count($transfer) > 0) ? $transfer[0] : null;
		if ($transfer === null || $transfer === "") return true;
		$this->active = ((int)$transfer === 1);
		$this->toggleTransferReceived = true;
		$this->individualNotesTransfer = "";
		return true;
	}

	/*Persists the toggle. Runs in InitialOrdersGamePhase::PROCESS - the only point at which the
	  POST-side ship still carries what the player clicked (individualNotesTransfer). Phase 1 matches
	  where the client offers the button, alongside the power on/offline control.

	  ⚠️ $this AND $this->getUnit() ARE POST-SIDE OBJECTS, and almost nothing on them is populated.
	  Manager.php rebuilds a submitted ship with a bare `new $className($id,$userid,$name,$slot)`:
	  onConstructed() never runs, so no enhancements are applied and no notes are loaded.
	  chameleonDisguiseClass is therefore ALWAYS null here and revealedTeams ALWAYS empty. Testing
	  either off the POST-side object disables the toggle silently and forever - which is exactly
	  what an `if (empty($ship->chameleonDisguiseClass)) return;` guard on $ship did between
	  2026-07-31 and 2026-08-01: not one Disguised/Undisguised note was ever written, and the button
	  did nothing at all (fixed 2026-08-01). EVERYTHING except "what did the player just click" has
	  to be read off the AUTHORITATIVE ship in $gameData, which is a full DBManager::getTacGamedata
	  load. This is plan trap 3 biting the one thing the plan said was safe to leave in process().
	  The reveal checkpoints stay out of here for the same reason - they run from
	  checkChameleonReveal(), off a real load.*/
	public function generateIndividualNotes($gameData, $dbManager){
		$postShip = $this->getUnit();
		if ($postShip === null) return;
		if ($gameData->phase != 1) return;

		$ship = $gameData->getShipById($postShip->id);
		if ($ship === null) return;
		$css = $ship->getSystemById($this->id);
		if (!($css instanceof ChameleonSensors)) return;

		/*Server-authoritative legality, mirroring what the client offers: no simulacrum to project,
		  array destroyed or offline, or every enemy team has already seen through it. The button is
		  withdrawn in all of those, so a value posted for one is not a player decision - and a
		  doctored POST must not be able to flip a toggle that was never on offer.*/
		if (!$css->canBeToggled($gameData)) return;

		$this->doIndividualNotesTransfer();
		//What the player clicked, if they clicked anything; otherwise the state the DB already
		//holds, so a commit carrying no toggle cannot re-project a disguise that was dropped.
		$active = $this->toggleTransferReceived ? $this->active : $css->active;

		$notekey = $active ? 'Disguised' : 'Undisguised';
		$noteHuman = $active ? 'Projecting a disguise' : 'Disguise switched off';
		$this->individualNotes[] = new IndividualNote(
			-1, TacGamedata::$currentGameID, $gameData->turn, $gameData->phase,
			$ship->id, $this->id, $notekey, $noteHuman, 1
		);
	}

	/* D6c - "ELINT the simulacrum could not have run."
	   From Movement onward the enemy receives this ship's REAL EW entries, types included. A "Haven"
	   carrying a BDEW entry, or spending 10 OEW when a Haven's whole sensor suite is 7, is
	   self-evidently not a Haven - so the deception has to break on the evidence the enemy can
	   already see. Modelled on Stealth::isDetectedInitial (the Hyach subs), but with a CONDITIONAL
	   threshold, because a Dargan disguised as another ELINT ship is entitled to run ELINT.

	   Timing is immediate: EW is hidden from everybody in phase 1 and public from phase 2 of this
	   same turn, so a next-turn delay would leave the enemy staring for a full turn at evidence the
	   game refuses to act on.*/
	private function checkEWPlausibility($gameData, $ship){
		if (!$this->active) return;                       //not disguised, nothing to give away
		if (empty($ship->chameleonDisguiseClass)) return;
		if ($this->isDestroyed() || $this->isOfflineOnTurn()) return;

		$spent = $ship->getAllEWExceptDEW($gameData->turn);
		if ($spent <= 0) return;                          //DEW alone never betrays anything (turtling)

		$blueprint = $ship->getChameleonBlueprint();
		if ($blueprint === null) return;

		$reason = null;

		//E1 - ELINT operations while disguised as a ship with no ELINT array at all.
		//The type list is hardcoded because EW types are bare strings with no registry; any future
		//ELINT-only type MUST be added here or it becomes a silent hole in the reveal.
		$blueprintIsElint = false;
		foreach ($blueprint->systems as $system){
			if ($system instanceof ElintScanner){ $blueprintIsElint = true; break; }
		}
		if (!$blueprintIsElint){
			$elintOnly = array('SOEW', 'SDEW', 'BDEW', 'DIST', 'JAM');
			foreach ($ship->EW as $ew){
				if ($ew->turn != $gameData->turn) continue;
				if (in_array($ew->type, $elintOnly, true)){
					$reason = 'ELINT op (' . $ew->type . ') on a non-ELINT hull';
					break;
				}
			}
		}

		//E2 - more EW than the simulacrum's sensors could produce. Read from the BLUEPRINT, not from
		//a damaged live copy: the ceiling must not wander as the disguise accumulates mirrored damage.
		if ($reason === null){
			$ceiling = EW::getScannerOutput($blueprint, $gameData->turn);
			if ($spent > $ceiling){
				$reason = 'EW spend ' . $spent . ' vs ceiling ' . $ceiling;
			}
		}

		if ($reason === null) return;

		foreach ($this->getEnemyTeams($gameData, $ship) as $teamId){
			$this->revealTo($gameData, $ship, $teamId, true, $reason);
		}
	}

	/* Proximity (5 hexes for ships, 2 for fighters and shuttles, LoS-permitting) and thrust
	   plausibility. Both are observed as they happen, so both reveal immediately. Called from the
	   Deployment and Movement advances via BaseShip::checkChameleonReveal.*/
	/* Every reveal checkpoint enters here. $checkpoint is passed EXPLICITLY rather than read off
	   $gamedata->phase, because each phase's advance() sets the NEXT phase before running its ship
	   loop - at Deployment advance the gamedata already says phase 1, and at Initial Orders advance
	   it already says phase 2. Dispatching on the phase would silently run the wrong checks.

	   'deployment' - proximity only (catches a turn-1 deployment that lands inside 5 hexes)
	   'initial'    - ELINT/EW plausibility (D6c)
	   'movement'   - proximity + thrust plausibility (D6b); the primary checkpoint
	   'firing'     - shutdown only (this is where the array gets shot out)*/
	public function checkChameleonReveal($gamedata, $checkpoint = 'movement'){
		$ship = $this->getUnit();
		if ($ship === null) return;
		if (empty($ship->chameleonDisguiseClass)) return;
		if ($ship->isDestroyed()) return; //destruction ends the deception on its own

		//Anything that stops the suite projecting breaks the deception PERMANENTLY: switching it back
		//on, repairing the array or restoring its power does NOT put the mask back (D8). That
		//permanence is the whole reason this writes a note rather than just reporting "not disguised
		//right now" - without it, a player could drop the disguise for a turn and then resume it.
		if (!$this->active || $this->isDestroyed() || $this->isOfflineOnTurn()){
			if (!$this->active)              $reason = 'Disguise switched off';
			else if ($this->isDestroyed())   $reason = 'Chameleon array destroyed';
			else                             $reason = 'Chameleon array offline';

			foreach ($this->getEnemyTeams($gamedata, $ship) as $teamId){
				$this->revealTo($gamedata, $ship, $teamId, true, $reason);
			}
			return;
		}

		//Runs at EVERY checkpoint, not just deployment. The rule is about fleet composition, so it
		//bites on turn 1 at the Deployment advance - but a team whose Ancient units are held in
		//reserve, or which surrenders a slot and gains one, must not get a free pass, and a Chameleon
		//ship that deploys on turn 5 has to be caught too. Cheap: it returns on the first pass
		//because revealTo() is idempotent per team, and after that revealedTeams short-circuits it.
		$this->checkAncientSensors($gamedata, $ship);

		if ($checkpoint === 'firing'){
			$this->checkWeaponPlausibility($gamedata, $ship);
			return;
		}

		if ($checkpoint === 'initial'){
			$this->checkEWPlausibility($gamedata, $ship);
			return;
		}

		$this->checkProximity($gamedata, $ship);
		if ($checkpoint === 'movement') $this->checkThrustPlausibility($gamedata, $ship);
	}

	/* Ancient sensors see through it, full stop (user's ruling, 2026-08-01).

	   "AdvancedSensors on Ancient faction ships ignore the Chameleon effect entirely - so if there is
	   a unit on a team with factionAge 3 or more, Chameleon ships automatically lose their disguise
	   at the start of the game."

	   The operative test is factionAge >= 3 (3 = Ancient, 4 = Primordial: Shadows, Vorlons,
	   Kirishiac, Mindriders, Thirdspace). The AdvancedSensors ability is NOT additionally required -
	   the ruling is written as a property of the race, and every Ancient hull carries the ability
	   anyway, so requiring both would only add a way for a sensor-crippled Shadow cruiser to be
	   fooled. One line here if that turns out to be wanted.

	   PER TEAM, exactly like checkProximity: in a 3-way game the Shadow player sees the real hull
	   while the two young-race teams keep facing the simulacrum. Same model trueStealth uses.

	   Reserves and destroyed units still count. This is a fleet-composition rule known before a shot
	   is fired, not something the disguised ship can undo by killing the scout - and the reveal is
	   permanent anyway, so excluding them would only create an ordering quirk on turn 1.*/
	private function checkAncientSensors($gamedata, $ship){
		foreach ($gamedata->ships as $otherShip){
			$teamId = (int)$otherShip->team;
			if ($teamId == $ship->team) continue;
			if ($otherShip->isTerrain()) continue;                       //asteroids have a factionAge too
			if ($otherShip->factionAge < 3) continue;
			if (in_array($teamId, $this->revealedTeams, true)) continue; //already knows
			//notekey_human is varchar(40) and overflow ABORTS the whole submission - 33 chars.
			$this->revealTo($gamedata, $ship, $teamId, true, 'Ancient sensors see through it');
		}
	}

	/*Close enough to look at it properly. Same shape as ShadingField::isDetected - hex range plus a
	  line-of-sight test - but the range depends on who is looking: a fighter or shuttle has to be
	  almost on top of the hull, a ship's sensors do it from 5.*/
	private function checkProximity($gamedata, $ship){
		$blockedHexes = $gamedata->blockedHexes;
		$pos = $ship->getHexPos();

		foreach ($gamedata->ships as $otherShip){
			$teamId = (int)$otherShip->team;
			if ($teamId == $ship->team) continue;
			if ($otherShip->isTerrain()) continue;
			if ($otherShip->isDestroyed()) continue;
			if ($otherShip->unavailable) continue;                      //not on the map yet
			if (in_array($teamId, $this->revealedTeams, true)) continue; //already knows

			$range = ($otherShip instanceof FighterFlight) ? 2 : 5;
			if ($otherShip->shipSizeClass === 0) $range = 2;             //shuttles and other tiny craft

			if (mathlib::getDistanceHex($ship, $otherShip) > $range) continue;

			$otherPos = $otherShip->getHexPos();
			if (!empty($blockedHexes) && Mathlib::isLoSBlocked($pos, $otherPos, $blockedHexes)) continue;

			$this->revealTo($gamedata, $ship, $teamId, true, 'Enemy within ' . $range . ' hexes');
		}
	}

	/* D6b - "acceleration the simulacrum could not have managed."
	   The threshold reuses the involuntary-acceleration maths (Engine::doStuckEngine):
	   floor(thrust / accelcost). It is read GENEROUSLY - the simulacrum's UNDAMAGED engine output -
	   because the rule says "much faster than would normally be possible", and a false positive here
	   is far more annoying to a player than a missed reveal.

	   abs() covers deceleration too: a hull that stops dead faster than a Demos could is just as
	   damning as one that sprints.*/
	private function checkThrustPlausibility($gamedata, $ship){
		/*TURN 1 IS CHECKED, and it has to be. FV deploys warships at speed 0 and the whole of a
		  ship's opening burn is plotted in turn 1's Movement phase, paying real thrust - so turn 1
		  is the single most likely turn for a hull to out-accelerate its simulacrum. The baseline is
		  the ship's DEPLOY move, which getLastTurnMovement() returns on the deployment turn because
		  it exempts 'deploy' from its turn filter (and 'start' markers are skipped outright, so the
		  baseline is never the pre-deployment speed). The same path covers a unit deploying from
		  reserve on turn 5.
		  Found in game 4274: a Dargan wearing an Octurion (maxDeltaV 2) accelerated 0 -> 4 on turn 1
		  and was not revealed, because this used to return on turn < 2.*/
		$thisTurn = $ship->getLastTurnMovement($gamedata->turn + 1);
		$lastTurn = $ship->getLastTurnMovement($gamedata->turn);
		if ($thisTurn === null || $lastTurn === null) return;
		if ($thisTurn->turn != $gamedata->turn) return;  //did not move this turn

		$deltaV = abs((int)$thisTurn->speed - (int)$lastTurn->speed);

		/*An Involuntary Acceleration crit is not the player "doing" anything, and letting it reveal
		  would turn a crit on the REAL ship into a self-inflicted unmasking.
		  MovementOrder::$forced is DEAD on the server side - the client sends the field but
		  tac_shipmovement has no column for it, so a loaded move always reads false and the guard
		  that tested it never fired. The real marker is a 'speedchange' carrying preturn, which is
		  what Movement::doStuckEngine writes (stamped with the FOLLOWING turn). Subtract those
		  deltas rather than skipping the turn wholesale, so a stuck engine cannot be used as cover
		  for a voluntary burn on the same turn.*/
		foreach ($ship->movement as $move){
			if ((int)$move->turn !== (int)$gamedata->turn) continue;
			if ($move->type !== 'speedchange') continue;
			if (empty($move->preturn)) continue;
			$deltaV -= abs((int)$move->value);
		}
		if ($deltaV <= 0) return;

		$blueprint = $ship->getChameleonBlueprint();
		if ($blueprint === null) return;
		if ($blueprint->accelcost <= 0) return;

		$thrust = 0;
		foreach ($blueprint->systems as $system){
			if ($system instanceof Engine) $thrust = max($thrust, $system->getOutput());
		}
		if ($thrust <= 0) return;                        //no engine to reason about - do not guess

		$maxDeltaV = floor($thrust / $blueprint->accelcost);
		if ($deltaV <= $maxDeltaV) return;

		$reason = 'Speed change ' . $deltaV . ' vs limit ' . $maxDeltaV;
		foreach ($this->getEnemyTeams($gamedata, $ship) as $teamId){
			$this->revealTo($gamedata, $ship, $teamId, true, $reason);
		}
	}

	/* D6 (Stage 6) - "a weapon the simulacrum could not have fired."

	   Not any fire order: the MISMATCH is what betrays you. A shot is plausible when the simulacrum
	   mounts a weapon of the same class whose arc covers the shot that was actually taken - which is
	   the plan's test A (class presence) with refinement C (arc) folded in, on the user's ruling:
	   "if not in arc, then this counts as a reveal in the same way as if it fired a weapon the
	   phantom is not equipped with", because the simulacrum could not physically have made that shot.

	   Refinement B (count) then comes free, because the match is INJECTIVE: each firing weapon
	   consumes a distinct simulacrum mount, so a Dargan firing three Matter Cannons while wearing a
	   Balvarix - which mounts two - leaves the third with no counterpart and reveals. Two real guns
	   firing have to look like two simulacrum guns firing.

	   Timing is revealedNextTurn, unlike every other checkpoint: this check runs at the END of the
	   Firing phase, after the enemy has already watched the shot land, and the note is stamped with
	   the current turn so the masking test (note.turn < gamedata.turn) hides it until the turn rolls
	   over. That is the tabletop reading - the discrepancy between what they saw fire and the damage
	   it did is worked out afterwards, not as it happens.

	   Runs off $servergamedata inside FireGamePhase::advance, after Firing::fireWeapons, so the
	   orders are resolved and still in memory (they are not written until further down that method).*/
	private function checkWeaponPlausibility($gamedata, $ship){
		$blueprint = $ship->getChameleonBlueprint();
		if ($blueprint === null) return;

		//Blueprint, not the phantom: the limits a player plans around must not wander as the
		//simulacrum accumulates mirrored damage. Same convention as D6b and D6c.
		$fakeWeapons = array();
		foreach ($blueprint->systems as $system){
			if ($system instanceof Weapon) $fakeWeapons[$system->id] = $system;
		}

		$used = array();
		$mismatch = null;

		foreach ($ship->systems as $weapon){
			if (!($weapon instanceof Weapon)) continue;
			foreach ($weapon->fireOrders as $fire){
				if ($fire->turn != $gamedata->turn) continue;
				if ($fire->shots <= 0) continue;              //technical orders are not shots anyone saw

				$target = $gamedata->getShipById($fire->targetid);
				if ($target === null) continue;               //nothing observable to bear on

				$bearing = $ship->getBearingOnUnit($target);

				$matched = false;
				foreach ($fakeWeapons as $fakeId => $fake){
					if (isset($used[$fakeId])) continue;
					if (get_class($fake) !== get_class($weapon)) continue;
					$startArcs = ($fake->splitArcs && !empty($fake->startArcArray)) ? $fake->startArcArray : array();
					$endArcs   = ($fake->splitArcs && !empty($fake->endArcArray))   ? $fake->endArcArray   : array();
					if (!mathlib::isInAnyArc($bearing, $fake->startArc, $fake->endArc, $startArcs, $endArcs)) continue;
					$used[$fakeId] = true;
					$matched = true;
					break;
				}

				if (!$matched && $mismatch === null) $mismatch = $weapon->displayName;
			}
		}

		if ($mismatch === null) return;

		//notekey_human is varchar(40) and OVERFLOW ABORTS THE WHOLE SUBMISSION - revealTo clamps,
		//but keep the reason short at the source too.
		$reason = 'Fired ' . $mismatch;
		foreach ($this->getEnemyTeams($gamedata, $ship) as $teamId){
			$this->revealTo($gamedata, $ship, $teamId, false, $reason);
		}
	}

	/*The in-game toggle. SystemActivation renders this automatically for any client system
	  implementing canActivate/canDeactivate (no React work needed) - this is the server mirror.
	  Allowed in the Deployment/pre-turn phase and in Firing (effective next turn), the same
	  convention Hangar Ops and the Kirishiac Orbitals use.*/
	public function canBeToggled($gamedata){
		if ($gamedata->phase != 1) return false; //Initial Orders, alongside the power on/offline control
		if ($this->isDestroyed($gamedata->turn) || $this->isOfflineOnTurn($gamedata->turn)) return false;
		if ($this->isFullyRevealed($gamedata)) return false; //nothing left to hide
		$ship = $this->getUnit();
		return ($ship !== null && !empty($ship->chameleonDisguiseClass));
	}

	/*THE display question: is this suite still showing SOMEBODY a false face?

	  Distinct from isDisguisedFrom($team), which answers it for one particular viewer. This is the
	  fleet-wide version - "is the deception doing anything at all any more" - and it is what every
	  OWNER-FACING readout should be gated on, because a line that describes a deception nobody is
	  fooled by any longer is not merely redundant, it is wrong.

	  Deliberately NOT testing isDestroyed/isOfflineOnTurn: both of those END the deception through
	  the shutdown checkpoint, which writes a real permanent reveal to every enemy team, so they
	  arrive here as revealedTeams and are already covered. Testing them directly would also be
	  unreliable at one of the two call sites - Enhancements::setEnhancements runs BEFORE
	  ShipSystem::onConstructed links $structureSystem, so isDestroyed() cannot yet see a system that
	  fell off with its structure block.

	  Fails CLOSED at every step (isRevealedToEveryTeam() returns false with no team list), so an
	  unusual load keeps showing a live deception rather than blanking one.*/
	public function isProjecting(){
		$ship = $this->getUnit();
		if ($ship === null || empty($ship->chameleonDisguiseClass)) return false; //nothing to project
		if (!$this->active) return false;                                         //dropped by its owner
		return !$this->isRevealedToEveryTeam();
	}

	/*Owner-facing suffix naming the enemy teams still taken in, e.g. " [for Team 3 only]". Appended
	  to every "Disguised as: X" the owner is shown, because with more than one opponent that line
	  otherwise hides the thing the player most needs: the deception can break against ONE team and
	  stay perfectly good against the rest, and a reveal is per team and permanent.

	  Empty string with a single opponent - "[for Team 2]" in a 1v1 restates the only possibility -
	  and empty when nothing is deceived any more, which is the case isProjecting() already withholds
	  the whole line for. "only" appears exactly when at least one enemy team HAS seen through it,
	  so its presence is itself the signal that the deception is partly blown.

	  Teams are named by number, matching the in-game fleet list ("TEAM " + slot.team,
	  fleetList.js:276) - slot names are per SLOT, and a team can hold several.*/
	public function getDeceivedTeamsLabel(){
		$ship = $this->getUnit();
		if ($ship === null) return '';

		$enemyTeams = 0; $stillDeceived = array();
		foreach (TacGamedata::$chameleonAllTeams as $teamId){
			$teamId = (int)$teamId;
			if ($teamId === (int)$ship->team) continue; //its own team is not being deceived
			$enemyTeams++;
			if (!in_array($teamId, $this->revealedTeams, true)) $stillDeceived[] = $teamId;
		}
		if ($enemyTeams < 2) return '';        //one opponent: naming them adds nothing
		if (empty($stillDeceived)) return '';  //nobody left to deceive

		sort($stillDeceived);
		return ' [for ' . (count($stillDeceived) > 1 ? 'Teams ' : 'Team ')
			. implode(', ', $stillDeceived)
			. (count($stillDeceived) < $enemyTeams ? ' only' : '') . ']';
	}

	public function stripForJson(){
		$strippedSystem = parent::stripForJson();
		//The owner and their team need the toggle state to render the button and the tooltip.
		//An enemy is told nothing: they must not learn that this ship has a disguise to drop, nor
		//which OTHER teams have already seen through it.
		if ($this->isRevealedToCurrentViewer()){
			$ship = $this->getUnit();
			//drives the toggle button: there is nothing to switch on or off without a simulacrum
			$hasDisguise = ($ship !== null && !empty($ship->chameleonDisguiseClass));
			/*What travels is the EFFECTIVE projection state, not the raw toggle. The owner's ship
			  window paints any system with active == true in the boosted yellow
			  (SystemIcon.isBoosted reads system.active), and there are two cases where that told the
			  player something untrue: a suite bought with no simulacrum has nothing to project, and
			  one every enemy team has already seen through is projecting to nobody. Both are
			  ordinary ELINT arrays at that point and must read as idle.
			  Nothing is lost by folding them in here: hasDisguise and revealedTeams both travel, and
			  the toggle button gates on those (canActivate / canDeactivate / isFullyRevealed), not
			  on this flag.*/
			$strippedSystem->active = $this->isProjecting();
			$strippedSystem->revealedTeams = $this->revealedTeams;
			$strippedSystem->hasDisguise = $hasDisguise;

			/*⚠️ WITHOUT THIS THE WEAPON BRIEFING NEVER REACHES THE BROWSER, and does so silently.
			  ShipSystem::stripForJson does not send the $data dict at ALL, so the client's system
			  tooltip is built entirely from window.staticShips - and the static blueprint is a
			  PRISTINE ship with no disguise, on which addChameleonWeaponBriefing() returns at its
			  own empty-chameleonDisguiseClass guard. Measured: live 1157 chars vs static 626, the
			  difference being exactly the briefing. Every other per-instance tooltip in the codebase
			  sends its dict the same way (~20 sites).

			  Safe to send wholesale because systemFactory.js does Object.assign(staticBlueprint,
			  systemJson) - `data` REPLACES the static dict rather than merging into it - and the
			  live dict is a strict superset (same single 'Special' key, the live value starting
			  with the static one verbatim). Sent for the owner in every state, not only while
			  projecting: that is also what makes the briefing VANISH the moment the deception ends,
			  rather than leaving the client on a stale static copy.*/
			$strippedSystem->data = $this->data;
		}else{
			$strippedSystem->active = false;
			$strippedSystem->revealedTeams = array();
			$strippedSystem->hasDisguise = false;
		}
		return $strippedSystem;
	}

}


/*SW Scanners have boostability reduced to +2*/
class SWScanner extends Scanner {
    public $name = "SWScanner";
    public $iconPath = "scanner.png";
	
    function __construct($armour, $maxhealth, $powerReq, $output ){
        parent::__construct($armour, $maxhealth, $powerReq, $output );
	$this->markStarWars();
    }
	
	/* moved to markStarWars function!
    public $maxBoostLevel = 2;

     public function setSystemDataWindow($turn){
	parent::setSystemDataWindow($turn);     
	$boostability = $this->maxBoostLevel;		
	if (!isset($this->data["Special"])) {
		$this->data["Special"] = '';
	}else{
		$this->data["Special"] .= '<br>';
	}
	$this->data["Special"] .= "Boostability limited to +".$boostability.".";	     
    }
    */
} //end of SWScanner

/*Antiquated Scanners cannot be boosted*/
class AntiquatedScanner extends Scanner {
    public $name = "AntiquatedScanner";
    public $iconPath = "scanner.png";
	
    function __construct($armour, $maxhealth, $powerReq, $output ){
        parent::__construct($armour, $maxhealth, $powerReq, $output );
	$this->markAntiquated();
    }

    public function markAntSensorFlux(){
        $this->specialAbilities[] = "AntiquatedSensorFlux";
        $this->specialAbilityValue = true; //so it is actually recognized as special ability!
        if (!isset($this->data["Special"])) {
            $this->data["Special"] = '';
        }else{
            $this->data["Special"] .= '<br>';
        }
        $this->data["Special"] .= 'Sensor fluctuations. Each turn, the sensor rolls for a critical, with a +5% penalty. Any effects last only 1 turn.';
    }

	public function getSpecialAbilityValue($args)
    {
		return $this->specialAbilityValue;
	}

	public function criticalPhaseEffects($ship, $gamedata) {
		
		parent::criticalPhaseEffects($ship, $gamedata);//Call parent to apply effects like Limpet Bore.			
		
		$hasAntSensorFlux = $ship->hasSpecialAbility("AntiquatedSensorFlux");

		if ($hasAntSensorFlux) {

			$roll = Dice::d(20) + 1 + $this->getTotalDamage();  //There is a +1 penalty in addition to any damage

			if($roll >= 15 && $roll < 19){ // Output reduced by 1 for one turn
				$finalTurn = $gamedata->turn + 1;
				$crit = new OutputReduced1(-1, $this->unit->id, $this->id, "OutputReduced1", $gamedata->turn, $finalTurn);
				$crit->updated = true;
				$crit->newCrit = true; // force save even if crit is not for current turn
				$this->criticals[] =  $crit;
			} elseif ($roll >=19 && $roll < 23) { // Output reduced by 2 for one turn
				$finalTurn = $gamedata->turn + 1;
				$crit = new OutputReduced2(-1, $this->unit->id, $this->id, "OutputReduced2", $gamedata->turn, $finalTurn);
				$crit->updated = true;
				$crit->newCrit = true; // force save even if crit is not for current turn
				$this->criticals[] =  $crit;
			} elseif ($roll >=23 && $roll < 27) { // Output reduced by 3 for one turn
				$finalTurn = $gamedata->turn + 1;
				$crit = new OutputReduced3(-1, $this->unit->id, $this->id, "OutputReduced3", $gamedata->turn, $finalTurn);
				$crit->updated = true;
				$crit->newCrit = true; // force save even if crit is not for current turn
				$this->criticals[] =  $crit;
			} elseif ($roll >=27) { // Output reduced by 4 for one turn
				$finalTurn = $gamedata->turn + 1;
				$crit = new OutputReduced4(-1, $this->unit->id, $this->id, "OutputReduced4", $gamedata->turn);
				$crit->updated = true;
				$crit->newCrit = true; // force save even if crit is not for current turn
				$this->criticals[] =  $crit;
            }
			
		}
		
	}

	
} //end of AntiquatedScanner



class CnC extends ShipSystem implements SpecialAbility {
    public $name = "cnC";
    public $displayName = "C&C";
    public $primary = true;
    private $marines = 0;//Front end varibale to dispaly current marine count on ship.
	
	//C&C  is VERY important, although not as much as the reactor!
	public $repairPriority = 9;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
		protected $preBattleCriticals = array(
			'ShipDisabled');
    
    protected $possibleCriticals = array(
    	//1=>"SensorsDisrupted", //not implemented! so I take it out 
		1=>"CommunicationsDisrupted",   //this instead of SensorsDisrupted
		9=>"CommunicationsDisrupted", 
		12=>"PenaltyToHit", 
		15=>"RestrictedEW", 
		18=>array("ReducedIniativeOneTurn","ReducedIniative"), 
		21=>array("RestrictedEW","ReducedIniativeOneTurn","ReducedIniative"), 
		24=>array("RestrictedEW","ReducedIniative","ShipDisabledOneTurn") 
    );
        
    function __construct($armour, $maxhealth, $powerReq, $output ){
        parent::__construct($armour, $maxhealth, $powerReq, $output );
    }
	
    public function markCommsFlux(){
        $this->specialAbilities[] = "CommsFlux";
        $this->specialAbilityValue = true; //so it is actually recognized as special ability!
        if (!isset($this->data["Special"])) {
            $this->data["Special"] = '';
        }else{
            $this->data["Special"] .= '<br>';
        }
        $this->data["Special"] .= '<br>Communications problems. Each turn, the C&C rolls for a critical, with a +5% penalty. Any effects last only 1 turn.';
    }

    public function getSpecialAbilityValue($args)
    {
        return $this->specialAbilityValue;
    }
	
	public function criticalPhaseEffects($ship, $gamedata) {
			
		parent::criticalPhaseEffects($ship, $gamedata);//Call parent to apply effects like Limpet Bore.			

		foreach($ship->movement as $shipMove){ //Look through Movement Orders to see if an Emergency Roll occurred this turn.			
			if($shipMove->turn == $gamedata->turn){	//This turn.					
				if($shipMove->value == "emergencyRoll"){ //Has Emergency rolled!								
							$testCrit = array(); 
							$testCrit = $this->testCritical($ship, $gamedata, $testCrit);//Damage caused, need to force critical test outside normal routine
						$effectIni = 6;
						if(!$this->isDestroyed()){//Check if destroyed, but really shouldn't be rolling if it is!										
							for($i=1; $i<=$effectIni;$i++){
								$crit = new tmpinidown(-1, $ship->id, $this->id, 'tmpinidown', $gamedata->turn); 
								$crit->updated = true;
								$this->criticals[] =  $crit;
							}		        		
						} 
				break; //No need to look further!						   
				}									

			}
		}
				
		
		$hasCommsFlux = $ship->hasSpecialAbility("CommsFlux");

		if ($hasCommsFlux) {

			$roll = Dice::d(20) + 1 + $this->getTotalDamage();  //There is a +1 penalty in addition to any damage

			if($roll >= 9 && $roll < 12){ // Initiative reduced by 5 for one turn
				$finalTurn = $gamedata->turn + 1;
				$crit = new CommunicationsDisruptedOneTurn(-1, $this->unit->id, $this->id, "CommunicationsDisruptedOneTurn", $gamedata->turn);
				$crit->updated = true;
				$crit->newCrit = true; // force save even if crit is not for current turn
				$this->criticals[] =  $crit;
			} elseif ($roll >=12 && $roll < 15) { // Reduce chance to hit for all weapons by 1 for one turn
				$finalTurn = $gamedata->turn + 1;
				$crit = new PenaltyToHitOneTurn(-1, $this->unit->id, $this->id, "PenaltyToHitOneTurn", $gamedata->turn);
				$crit->updated = true;
				$crit->newCrit = true; // force save even if crit is not for current turn
				$this->criticals[] =  $crit;
			} elseif ($roll >=15 && $roll < 18) { // Reduce sensors by 2 and no more than half can be used offensively for one turn
				$finalTurn = $gamedata->turn + 1;
				$crit = new RestrictedEWOneTurn(-1, $this->unit->id, $this->id, "RestrictedEWOneTurn", $gamedata->turn);
				$crit->updated = true;
				$crit->newCrit = true; // force save even if crit is not for current turn
				$this->criticals[] =  $crit;
			} elseif ($roll >=18 && $roll < 21) { // Initiative reduced by 10 for one turn
				$finalTurn = $gamedata->turn + 1;
				$crit = new ReducedIniativeOneTurn(-1, $this->unit->id, $this->id, "ReducedIniativeOneTurn", $gamedata->turn);
				$crit->updated = true;
				$crit->newCrit = true; // force save even if crit is not for current turn
				$this->criticals[] =  $crit;
			} elseif ($roll >=21) { // Output reduced by 4 for one turn
				// Reduce sensors by 2 and no more than half can be used offensively for one turn	
				// Initiative reduced by 10 for one turn				
				$finalTurn = $gamedata->turn + 1;
				$crit = new RestrictedEWOneTurn(-1, $this->unit->id, $this->id, "RestrictedEWOneTurn", $gamedata->turn);
				$crit->updated = true;
				$crit->newCrit = true; // force save even if crit is not for current turn
				$this->criticals[] =  $crit;
				$crit2 = new ReducedIniativeOneTurn(-1, $this->unit->id, $this->id, "ReducedIniativeOneTurn", $gamedata->turn);
				$crit2->updated = true;
				$crit2->newCrit = true; // force save even if crit is not for current turn
				$this->criticals[] =  $crit2;
            }
			
		}
		
		// Boarding Action Detach / Destroy logic
		if (!empty($ship->hasAttached)) {
			foreach ($ship->hasAttached as $shooterId => $location) {
				$boardingShip = $gamedata->getShipById($shooterId);
				if ($boardingShip && !$boardingShip->isDestroyed()) {
					$struct = $ship->getStructureSystem($location);
					if (!$struct) $struct = $ship->getStructureSystem(0); // Fallback to primary structure

					if ($struct && $struct->isDestroyed()) {
						// Target structure destroyed, kill the boarding ship
						if ($boardingShip instanceof FighterFlight) {
							foreach ($boardingShip->systems as $fighter) {
								if (!$fighter->isDestroyed()) {
									$damageEntry = new DamageEntry(-1, $boardingShip->id, -1, $gamedata->turn, $fighter->id, $fighter->getRemainingHealth(), 0, 0, -1, true, false, "Target structure destroyed", "Standard", -1, -1);
									$damageEntry->updated = true;
									$fighter->damage[] = $damageEntry;
								}
							}
						} else {
							// It's a ship attached via Grappling Claw. Destroy the claw(s) holding this connection.
							foreach ($boardingShip->systems as $system) {
								if ($system instanceof GrapplingClaw && !$system->isDestroyed()) {
									// We need to check if THIS specific claw was attached to THIS host ship.
									if (isset($system->hostShipId) && $system->hostShipId == $ship->id) {
										$damageEntry = new DamageEntry(-1, $boardingShip->id, -1, $gamedata->turn, $system->id, $system->getRemainingHealth(), 0, 0, -1, true, false, "Target structure destroyed", "Standard", -1, -1);
										$damageEntry->updated = true;
										$system->damage[] = $damageEntry;
										$system->exchangeMarines($boardingShip, $gamedata); //Move any spare Marines to another Claw on the same ship if available
									}
									if($system->name == "GrapplingClaw"){ //Either way, create note to reset the claw's own 
										$system->hostShipId = -1;
										$clawNote = new IndividualNote(-1,TacGamedata::$currentGameID,$gamedata->turn,$gamedata->phase,$boardingShip->id,$system->id,"ClawDetached","ClawDetached",-1);											
										Manager::insertIndividualNote($clawNote);										
									}								
								}
							}
							// Detach the ship
							$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gamedata->turn,$gamedata->phase,$ship->id,$this->id,"Detached","Detached",$shooterId . "=>Detach");
						}
					} else if ($ship->isDestroyed()) {
						// Parent ship destroyed but not the structure, detach!
						$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gamedata->turn,$gamedata->phase,$ship->id,$this->id,"Detached","Detached",$shooterId . "=>Detach");
					}
				}
			}
		}

	}
	
	
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		$ship = $this->getUnit();
		$marineDefenders = $ship->howManyMarines();
		if($ship->factionAge > 2){//Ancients can't be boarded and who knows what their defenders look like!
			$this->data["Marine Units"] = 'n/a';//Or change to 'N/A'
		}else{
			$this->data["Marine Units"] = $marineDefenders;
			$this->marines = $marineDefenders;
		}
	}

        public function stripForJson() {//Need to send Marines to front-end so it updates count.
            $strippedSystem = parent::stripForJson();    
            $strippedSystem->marines = $this->marines;                             
            return $strippedSystem;
        }

	public function onIndividualNotesLoaded($gamedata) {
		//parent::onIndividualNotesLoaded($gamedata);
		$ship = $this->getUnit();
		
		if (is_array($this->individualNotes)) {
			$remainingNotes = array();
			foreach ($this->individualNotes as $currNote) {
				if ($currNote->notekey === 'Attached') {
					$parts = explode('=>', $currNote->notevalue);
					if (count($parts) === 2) {
						$shooterId = (int)$parts[0];
						// notevalue format: "shooterId=>location" (legacy) or "shooterId=>location:facing" (with entry-side hex offset)
						$locParts = explode(':', $parts[1]);
						$location = (int)$locParts[0];
						$facing = isset($locParts[1]) ? (int)$locParts[1] : null;
						$ship->hasAttached[$shooterId] = $location;
						if ($facing !== null) $ship->hasAttachedFacing[$shooterId] = $facing;

						$boardingShip = $gamedata->getShipById($shooterId);
						if ($boardingShip) {
							$boardingShip->attached[$ship->id] = $location;
							if ($facing !== null) $boardingShip->attachedFacing[$ship->id] = $facing;
						}
					}
				} else if ($currNote->notekey === 'Detached') {
					$parts = explode('=>', $currNote->notevalue);
					if (count($parts) === 2) {
						$shooterId = (int)$parts[0];
						unset($ship->hasAttached[$shooterId]);
						unset($ship->hasAttachedFacing[$shooterId]);

						$boardingShip = $gamedata->getShipById($shooterId);
						if ($boardingShip) {
							unset($boardingShip->attached[$ship->id]);
							unset($boardingShip->attachedFacing[$ship->id]);
						}
					}
				} else {
					$remainingNotes[] = $currNote;
				}
			}
			$this->individualNotes = $remainingNotes;
		}
	}
			
} //endof class CnC


class OSATCnC extends CnC{	//Special technical OSAT CnC system, so criticals effects can be applied to these units etc
    public $iconPath = "cnCtechnical.png";
    public $isPrimaryTargetable = false;
	public $isTargetable = false;   
	public $doCountForCombatValue = false;  
	public $hideInShipWindow = true;	 

	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);     
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
		$this->data["Special"] .= "This system is here for technical purposes only. Cannot be damaged in any way.";
	}
		
}//endof OSATCnC

/*Protected CnC - as compensation for ships lacking two C&Cs, these systems get different (lighter) critical table 
*/
class ProtectedCnC extends CnC{
	
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);     
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
		//actually now secondary C&C is present - Protected C&C-equipped units should be re-equipped with regular C&C + Secondary C&C instead!
		//$this->data["Special"] .= 'This unit should have two separate C&Cs. As this is not possible in FV, critical chart is changed instead.';
		$this->data["Special"] .= "C&C that's more resistant to critical damage.";
	}
	
	protected $possibleCriticals = array(
		8=>"CommunicationsDisrupted", 
		16=>"PenaltyToHit", 
		20=>"RestrictedEW", 
		24=>array("ReducedIniativeOneTurn","ReducedIniative"), 
		32=>array("RestrictedEW","ReducedIniativeOneTurn","ReducedIniative"), 
		40=>array("RestrictedEW","ReducedIniative","ShipDisabledOneTurn")
    );
	
}//endof class ProtectedCnC


class ThirdspaceCnC extends CnC{
	
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);     
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
	}
	
	protected $possibleCriticals = array(
		10=>"CommunicationsDisrupted", 
		17=>"PenaltyToHit", 
		25=>array("ReducedIniativeOneTurn","ReducedIniative"), 
		33=>array("RestrictedEWOneTurn","ReducedIniativeOneTurn","ReducedIniative"), 
		40=>array("RestrictedEW","ReducedIniative","PenaltyToHit")
    );
	
}//endof class ThirdspaceCnC

	
class PakmaraCnC extends CnC{	
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);     
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
		$this->data["Special"] .= "Pak'ma'ra C&C: Initiative penalties for critical hits are doubled.";
		//below is no longer true - Secondary C&C kicks in!
		//$this->data["Special"] .= '<br>This unit should have two separate C&Cs. As this is not possible in FV, critical chart is changed instead.';
	}

/*replaced by doubled Ini penalties, but no reduced crit chance
			protected $possibleCriticals = array(
				8=>array("CommunicationsDisrupted","CommunicationsDisrupted"), 
				16=>"PenaltyToHit", 
				20=>"RestrictedEW", 
				24=>array("ReducedIniativeOneTurn","ReducedIniative","ReducedIniativeOneTurn","ReducedIniative"), 
				32=>array("RestrictedEW","ReducedIniativeOneTurn","ReducedIniative","ReducedIniativeOneTurn","ReducedIniative"), 
				40=>array("RestrictedEW","ReducedIniative","ReducedIniative","ShipDisabledOneTurn")
		    );	*/
			
    protected $possibleCriticals = array(
    	//1=>"SensorsDisrupted", //not implemented! so I take it out 
		1=>array("CommunicationsDisrupted","CommunicationsDisrupted"),    //this instead of SensorsDisrupted
		9=>array("CommunicationsDisrupted","CommunicationsDisrupted"), 
		12=>"PenaltyToHit", 
		15=>"RestrictedEW", 
		18=>array("ReducedIniativeOneTurn","ReducedIniativeOneTurn","ReducedIniative","ReducedIniative"), 
		21=>array("RestrictedEW","ReducedIniativeOneTurn","ReducedIniativeOneTurn","ReducedIniative","ReducedIniative"), 
		24=>array("RestrictedEW","ReducedIniative","ReducedIniative","ShipDisabledOneTurn") 
    );
			
}//endof class PakmaraCnC


class SecondaryCnC extends ShipSystem{	
    public $name = "SecondaryCnC";
    public $displayName = "Secondary C&C";
    public $primary = true;
	public $iconPath = "CnCSecondary.png";
	
	//make it all-around by default - potentially saves work, and the system is only usable with TAG anyway
	public $startArc = 0;
	public $endArc = 360;
	
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);     
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
		$this->data["Special"] .= "Secondary C&C: May take damage on C&C hits (instead of actual C&C).";
		$this->data["Special"] .= '<br>If primary C&C is destroyed while secondary C&C is still alive, primary C&C will be revived with as much health as secondary C&C had.';
	}
	
	
    function __construct($armour, $maxhealth, $powerReq, $output ){
        parent::__construct($armour, $maxhealth, $powerReq, $output );
		$this->addTag('C&C');
    }
	
	//if primary C&C is destroyed while secondary is still alive - revive primary and destroy secondary!
	public function criticalPhaseEffects($ship, $gamedata)
    { 
		if($this->isDestroyed()) return;
		
		//find primary C&C
		$primaryCnC = $ship->getSystemByName("CnC");
		
		if(!$primaryCnC->isDestroyed()) return; //primary C&C is not destroyed, no need to act
		
		//revive primary C&C, kill Secondary
		
		//find the killing shot...
		foreach ($primaryCnC->damage as $damage ) if(($damage->turn == $gamedata->turn) && ($damage->destroyed)){ 
			$healthRemaining = $this->getRemainingHealth();
			$damage->destroyed = false; //not a killing shot after all!
			//add revival of HP - as separate entry so damage from shot is not changed!
			$damageEntry = new DamageEntry(-1, $damage->shipid, -1, $damage->turn, $primaryCnC->id, -$healthRemaining, 0, 0, 0/*no fire order to tie this damage to actually*/, false, false, "Secondary C&C - reviving command", $damage->damageclass, 0/*no shooter*/, 0/*no firing weapon*/);
			$damageEntry->updated = true;
			$primaryCnC->damage[] = $damageEntry;
			//add Secondary C&C destruction - without actual damage, just desstruction, so it can be tied to original weapon impact without affecting damage done numbers
			$damageEntry = new DamageEntry(-1, $damage->shipid, -1, $damage->turn, $this->id, 0, 0, 0, $damage->fireorderid, true, false, "Secondary C&C - marking destroyed", $damage->damageclass, $damage->shooterid, $damage->weaponid);
			$damageEntry->updated = true;
			$this->damage[] = $damageEntry;
		}
    } //endof function criticalPhaseEffects	
	
}//endof class SecondaryCnC

class FlagBridge extends CnC implements SpecialAbility {
    public $name = "cnC";
    public $displayName = "C&C";
    
    public $initiativeBonus = 1;
    public $bonusType = 'Generic';
	public $specialAbilities = array("FlagBridge");	
	public $specialAbilityValue = 0;
	
    public $range = 60;
    public $worksOnFighters = false;
    public $worksOnShips = true;
    public $worksOnAllies = true;
    public $worksOnlyOnBaseHull = false;
    public $worksOnAllFactions = false;

    private static $cachedFlagBridges = null;
    private static $cacheKey = "";

    function __construct($armour, $maxhealth, $powerReq, $initiativeBonus = 1, $bonusType = 'Generic', $range = 60,  $worksOnFighters = false, $worksOnShips = true, $worksOnAllies = true, $worksOnlyOnBaseHull = false, $worksOnAllFactions = false) {
        parent::__construct($armour, $maxhealth, $powerReq, 0);
        
        $this->initiativeBonus = $initiativeBonus;
        $this->bonusType = $bonusType;
        $this->range = $range;
        $this->worksOnFighters = $worksOnFighters;
        $this->worksOnShips = $worksOnShips;
        $this->worksOnAllies = $worksOnAllies;
        $this->worksOnlyOnBaseHull = $worksOnlyOnBaseHull;
        $this->worksOnAllFactions = $worksOnAllFactions;
    }

    public function getSpecialAbilityValue($args)
    {
        return $this->specialAbilityValue;
    }	


    public static function getIniBonus($gamedata, $thisShip){
        $totalBonus = 0;
        $bonusesByType = array(); // Store highest bonus per type

        $currentCacheKey = $gamedata->id . "_" . $gamedata->turn . "_" . $gamedata->phase;

        // Cache FlagBridge locations once per phase to prevent O(N^2) systems iteration
        if (self::$cachedFlagBridges === null || self::$cacheKey !== $currentCacheKey) {
            self::$cachedFlagBridges = array();
            self::$cacheKey = $currentCacheKey;

            if($gamedata->turn > 0 && $gamedata->phase >= 0){
                foreach ($gamedata->ships as $ship) {
                    if ($ship->isDestroyed()) continue;

                    foreach ($ship->systems as $sys) {
                        if ($sys instanceof FlagBridge && !$sys->isDestroyed()) {
                            self::$cachedFlagBridges[] = array(
                                'system' => $sys,
                                'ship' => $ship
                            );
                        }
                    }
                }
            }
        }

        if (empty(self::$cachedFlagBridges)) {
            return 0; // Quick exit if no FlagBridges exist this turn
        }

        // Only calculate distance and restrictions against known FlagBridges
        foreach (self::$cachedFlagBridges as $fbData) {
            $flagBridge = $fbData['system'];
            $ship = $fbData['ship'];
                    
            // Get distance to this ship
            $distance = mathlib::getDistanceHex($thisShip, $ship);

            // Check distance
            if ($distance > $flagBridge->range) continue;

            // Check Faction / Ally restriction
            if (!$flagBridge->worksOnAllies) {
                if ($thisShip->userid != $ship->userid) continue;
            } else {
                // Assuming allied ships are owned by the same user based on standard FieryVoid logic
                if ($thisShip->userid != $ship->userid) continue;
            }

            // Check specific Faction matching
            if ($flagBridge->worksOnAllFactions === false) {
                if ($thisShip->faction !== $ship->faction) continue;
            } elseif (is_array($flagBridge->worksOnAllFactions)) {
                if (!in_array($thisShip->faction, $flagBridge->worksOnAllFactions)) continue;
            } elseif (is_string($flagBridge->worksOnAllFactions)) {
                if ($thisShip->faction !== $flagBridge->worksOnAllFactions) continue;
            }

            // Check Fighter vs Ship restriction
            $isFighter = ($thisShip instanceof FighterFlight);
            if ($isFighter && !$flagBridge->worksOnFighters) continue;
            if (!$isFighter && !$flagBridge->worksOnShips) continue;

            // Check Base Hull/Variant restriction
            if ($flagBridge->worksOnlyOnBaseHull) {
                $baseHull = $flagBridge->worksOnlyOnBaseHull;
                if (!$thisShip->isHull($baseHull)) continue;
            }

            // It applies! Add to our type array
            $type = $flagBridge->bonusType;
            $bonus = $flagBridge->initiativeBonus * 5; // The raw bonus is multiplied by 5

            if (!isset($bonusesByType[$type]) || $bonus > $bonusesByType[$type]) {
                $bonusesByType[$type] = $bonus;
            }
        }

        foreach ($bonusesByType as $bonus) {
            $totalBonus += $bonus;
        }

        return $totalBonus;
    }

    public function setSystemDataWindow($turn) {
        parent::setSystemDataWindow($turn);
        if (!isset($this->data["Special"])) {
            $this->data["Special"] = '';
        } else {
            $this->data["Special"] .= '<br>';
        }

        $targets = array();
        if ($this->worksOnShips) {
            $targets[] = "ships";
        }
        if ($this->worksOnFighters) {
            $targets[] = "fighters";
        }
        $targetStr = implode(" and ", $targets);
        
        $this->data["Special"] .= "Provides +" . ($this->initiativeBonus * 5) . " Initiative to friendly $targetStr within " . $this->range . " hexes.";
        
        if ($this->worksOnlyOnBaseHull) {
            $this->data["Special"] .= "<br>Only affects units based on the " . $this->worksOnlyOnBaseHull . " hull.";
        }
        if (!$this->worksOnAllies) {
            $this->data["Special"] .= "<br>Only affects units from your own fleet.";
        }
        if ($this->worksOnAllFactions === false) {
            $this->data["Special"] .= "<br>Only affects units matching this ship's faction.";
        } elseif (is_array($this->worksOnAllFactions)) {
            $this->data["Special"] .= "<br>Only affects units from these factions: " . implode(", ", $this->worksOnAllFactions) . ".";
        } elseif (is_string($this->worksOnAllFactions)) {
            $this->data["Special"] .= "<br>Only affects units from faction: " . $this->worksOnAllFactions . ".";
        }
        $this->data["Special"] .= "<br>Bonus type: " . $this->bonusType . " (Bonuses of the same type do not stack).";
    }
}



class CargoBay extends ShipSystem{
    public $name = "cargoBay";
    public $displayName = "Cargo Bay";
    
	//Cargo Bay is not important at all!
	public $repairPriority = 1;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
    
    function __construct($armour, $maxhealth){
        parent::__construct($armour, $maxhealth, 0, 0);
    }
}


class Quarters extends ShipSystem{
    public $name = "Quarters";
    public $displayName = "Quarters";
    
	//Quarters is not important at all!
	public $repairPriority = 1;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
    
    function __construct($armour, $maxhealth){
        parent::__construct($armour, $maxhealth, 0, 0);
    }
}

class Magazine extends ShipSystem{
    public $name = "Magazine";
    public $displayName = "Magazine";
    
	//Cargo Bay is not important at all!
	public $repairPriority = 1;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
    
    function __construct($armour, $maxhealth){
        parent::__construct($armour, $maxhealth, 0, 0);
    }
}



class Thruster extends ShipSystem{
    public $name = "thruster";
    public $displayName = "Thruster";
    public $direction;
    public $thrustused;
    public $thrustwasted = 0;
    public $isPrimaryTargetable = true; //can this system be targeted by called shot if it's on PRIMARY?	
    
    protected $possibleCriticals = array(15=>"FirstThrustIgnored", 20=>"HalfEfficiency", 25=>array("FirstThrustIgnored","HalfEfficiency"));
    
    function __construct($armour, $maxhealth, $powerReq, $output, $direction, $thrustused = 0 ){
        parent::__construct($armour, $maxhealth, $powerReq, $output );
         
        $this->thrustused = (int)$thrustused;
        $this->direction = (int)$direction;
        //arc depends on direction!
		switch($this->direction){
			case 1: //retro
				$this->startArc = 330;
				$this->endArc = 30;
				break;
			case 2: //main
				$this->startArc = 150;
				$this->endArc = 210;
				break;	
			case 3://port
				$this->startArc = 210;
				$this->endArc = 330;
				break;
			case 4://Stbd
				$this->startArc = 30;
				$this->endArc = 150;
				break;
		}
		
		//$this->addTag('Thruster'); //no need, as now system name is considered a tag as well
    }
} //endof Thruster


class InvulnerableThruster extends Thruster{
	/*sometimes thruster is techically necessary, despite the fact that it shouldn't be there (eg. on LCVs)*/
	/*this thruster will be almost impossible to damage :) (it should be out of hit table, too!)*/
	public $isPrimaryTargetable = false; //can this system be targeted by called shot if it's on PRIMARY?
	public $isTargetable = false; //cannot be targeted ever!
	public $hideInShipWindow = true; //purely technical (unlimited thrust, untargetable, no crits) → no icon needed in ship window
	protected $doCountForCombatValue = false; //don't count when estimating remaining combat value
	
    function __construct($armour, $maxhealth, $powerReq, $output, $direction, $thrustused = 0 ){
	    parent::__construct($armour, $maxhealth, $powerReq, $output, $direction, $thrustused );
		//use "technical" (grey) images instead of regular system (blue) ones
		switch($this->direction){
			case 1: //retro
				$this->iconPath = "thruster1Technical.png";
				break;
			case 2: //main
				$this->iconPath = "thruster2Technical.png";
				break;	
			case 3://Port
				$this->iconPath = "thruster3Technical.png";
				break;
			case 4://Stbd
				$this->iconPath = "thruster4Technical.png";
				break;
		}
    }
	
    public function getArmourInvulnerable($target, $shooter, $dmgClass, $pos=null){ //this thruster should be invulnerable to anything...
		$activeAA = 99;
		return $activeAA;
    }
    
    public function testCritical($ship, $gamedata, $crits, $add = 0){ //this thruster won't suffer criticals ;)
	    return $crits;
    }
	
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);     
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
		$this->data["Special"] .= 'This system is here for technical purposes only. Cannot be damaged in any way, and has unlimited thrust allowance.';
	}

	
} //endof InvulnerableThruster



class GraviticThruster extends Thruster{    
    function __construct($armour, $maxhealth, $powerReq, $output, $direction, $thrustused = 0 ){
        parent::__construct($armour, $maxhealth, $powerReq, $output, $direction, $thrustused);
    }
    
    public $firstCriticalIgnored = false; //not needed any more
    
    
    public function addCritical($shipid, $phpclass, $gamedata)
    {	    
	    //new approach: does GravThrusterCritIgnored exist? if yes, go ahead. If not, ignore critical and add GravThrusterCritIgnored instead.
	    //should affect only HalfEfficiency crit!
	    if ($phpclass == 'HalfEfficiency') { //such crit can be ignored - should it?!
		    $alreadyIgnored = false;
		    foreach($this->criticals as $preexisting){
			    if ($preexisting instanceof GravThrusterCritIgnored){
				$alreadyIgnored = true;    
			    }
		    }

		    if (!$alreadyIgnored ) {//nothing was negated yet		
			$crit = new GravThrusterCritIgnored(-1, $shipid, $this->id, 'GravThrusterCritIgnored', $gamedata->turn);
			$crit->updated = true;
			$this->criticals[] =  $crit;
			return $crit;
		    }
	    }
	  
            
        $crit = new $phpclass(-1, $shipid, $this->id, $phpclass, $gamedata->turn);
        $crit->updated = true;
        $this->criticals[] =  $crit;
        return $crit;
    }
}

class MindriderThruster extends GraviticThruster{ 

    function __construct($armour, $maxhealth, $powerReq, $output, $direction, $thrustused = 0 ){
        parent::__construct($armour, $maxhealth, $powerReq, $output, $direction, $thrustused);
         
        $this->thrustused = (int)$thrustused;
        $this->direction = (int)$direction;
        //arc depends on direction!
		switch($this->direction){
			case 1: //retro
				$this->startArc = 315;
				$this->endArc = 45;
				break;
			case 2: //main
				$this->startArc = 135;
				$this->endArc = 225;
				break;	
			case 3://port
				$this->startArc = 225;
				$this->endArc = 315;
				break;
			case 4://Stbd
				$this->startArc = 45;
				$this->endArc = 135;
				break;
		}

	}
	
}//endof MindriderThruster 


class MagGraviticThruster extends Thruster{ 
	protected $possibleCriticals = array(20=>"HalfEfficiency");
	
	//Mag-Grav Thrusters are considerd Gravitic, complete with first crit ignored effect:
	public function addCritical($shipid, $phpclass, $gamedata)
    {
	    //does GravThrusterCritIgnored exist? if yes, go ahead. If not, ignore critical and add GravThrusterCritIgnored instead.
	    //should affect only HalfEfficiency crit!
	    if ($phpclass == 'HalfEfficiency') { //such crit can be ignored - should it?!
		    $alreadyIgnored = false;
		    foreach($this->criticals as $preexisting){
			    if ($preexisting instanceof GravThrusterCritIgnored){
				$alreadyIgnored = true;    
			    }
		    }

		    if (!$alreadyIgnored ) {//nothing was negated yet		
			$crit = new GravThrusterCritIgnored(-1, $shipid, $this->id, 'GravThrusterCritIgnored', $gamedata->turn);
			$crit->updated = true;
			$this->criticals[] =  $crit;
			return $crit;
		    }
	    }
            
        $crit = new $phpclass(-1, $shipid, $this->id, $phpclass, $gamedata->turn);
        $crit->updated = true;
        $this->criticals[] =  $crit;
        return $crit;
    }
}

class Hangar extends ShipSystem{

    public $name = "hangar";
    public $displayName = "Hangar";
    public $squadrons = Array();
    public $primary = false; //changed from true on 21.11 - let's not consider it a core system after all!

	//Hangar is not important at all!
	public $repairPriority = 1;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired

	// === Hangar Operations (B5W §10.1) ===
	public $hangarType = 'fighters';      //category key matching FighterFlight->hangarRequired and ship->fighters keys
	//Steers the auto-populated default-shuttle pool AWAY from this hangar
	//(set via the constructor's $excludeFromDefaultShuttles arg). The bay's
	//boxes still count toward total hangar capacity — so the leftover-shuttle
	//COUNT is unchanged — but the shuttles pile into the ship's other
	//hangar(s), leaving this one free for a full fighter flight. Used by
	//ships with a dedicated heavy-fighter bay (e.g. ScoravarefittedAM) where
	//a stray shuttle would otherwise block a full 6-flight from docking.
	//Honoured server-side by HangarOps::excludesDefaultShuttles and mirrored
	//client-side in systems.js. Distinct from catapult/rail exclusion, which
	//drops the boxes from the pool entirely.
	public $excludeFromDefaultShuttles = false;
	//B5W "Inadequate Hangars (Unreliable)": hangar bays not part of the vessel's
	//original design. When true, every launch from this bay rolls 1d6 — a "1"
	//aborts the whole launch this turn (retry next turn); and every fighter that
	//LANDS rolls 1d6 — a "1" scores 1d6 damage on that fighter ignoring armour,
	//destroying (and freeing the box of) any fighter it kills. Set on the bay in
	//the ship's SCS (e.g. GromeGralacAM). Mirrored to the client via stripForJson.
	//Replay-deterministic: the per-launch / per-fighter rolls are persisted in
	//hangarInadequateRoll notes and read back in onIndividualNotesLoaded, the same
	//pattern FighterRail's 1d20 crit uses (railCritRoll).
	public $inadequate = false;
	//Per-bay fighter-class allow-list. When non-empty, this hangar accepts ONLY
	//FighterFlights whose phpclass is in the list — every other flight is rejected
	//even if it would fit by size category. Empty (the default) = unrestricted,
	//so existing ships are unaffected. Set via the constructor's trailing
	//$allowedFighterClasses arg (e.g. GaimSuom's bays: array('gaimReskaFighter')).
	//Carrier-DRIVEN exclusivity — the inverse of $customFtrName, which is fighter-
	//driven. Honoured server-side by HangarOps::hangarAcceptsFighterClass at every
	//bay-eligibility / dock gate, mirrored to the client via stripForJson and
	//systems.js so the launch/dock UI never offers a bay the server will reject.
	public $allowedFighterClasses = array();
	public $direction = 0;                //0..5 hex offset from carrier facing on launch (0 = same heading)
	//Stage 8.5: optional list of allowed launch directions for hangars whose
	//bays open onto multiple arcs (e.g. EA Hyperion: ports out either side, so
	//directions = [1,5]). When non-empty, the launch dialog shows a picker and
	//the player's per-launch choice overrides $direction. The carrier-destruction
	//escape spawn picks directions[0] as a sensible default so a "side-launch"
	//hangar doesn't eject forward.
	public $directions = array();
	public $hangarUsage = array();        //list of stored craft records: [['phpclass'=>...,'name'=>...,'flightSize'=>N,'hangarType'=>...], ...]
	public $spawnableClasses = array();   //FighterFlight phpclasses this hangar can launch (consumed by game.php blueprint preload)
	//$output is the SHARED launch+land budget per turn:
	//(launchedThisTurn + landedThisTurn) <= $output. A 6-output hangar can launch 6,
	//OR land 6, OR any split (4 launch + 2 land, etc.).
	public $launchedThisTurn = 0;         //resets each turn
	public $landedThisTurn = 0;           //resets each turn
	public $usagePopulated = false;       //idempotency guard — first hangar on a ship runs initial population once
	private $lastSavedUsage = null;       //serialized snapshot of last persisted hangarUsage (avoids duplicate notes)
	//Stage 15: per-carrier ordnance reload pool. Only the PRIMARY (first)
	//hangar on a ship carries the spent counter — HangarOps::drawReload writes
	//here and the per-load note pipeline persists it via hangarOrdReserve. Pool
	//capacity is re-derived from the carrier's HANG_ORD enhancement on every load.
	public $reloadPoolSpent = 0;
	private $lastSavedOrdReserve = null;  //serialized snapshot of last persisted reloadPoolSpent
	//Stage 17 ext: per-carrier marine contingents pool. Same primary-hangar
	//pattern as $reloadPoolSpent — HangarOps::drawMarineReload writes here,
	//persisted via hangarMarineReserve note. Capacity re-derived from the
	//carrier's MAR_CONT enhancement on every load.
	public $marinePoolSpent = 0;
	private $lastSavedMarinePool = null;  //serialized snapshot of last persisted marinePoolSpent
	//Stage S (S-d): integrated-fighter structure coupling. Only the PRIMARY
	//ShadowHangar carries these (same primary-hangar pattern as reloadPoolSpent),
	//persisted via the shadowIntegratedState note. $shadowAttached = integrated
	//fighters still tethered to the carrier (held in bay + launched-and-alive, NOT
	//cut off); $shadowCutOff = fighters severed by structure-box loss (fight on,
	//can't land). $shadowLaunched = a MAP {flightId => lastKnownActiveCraftCount} of
	//this carrier's integrated flights currently IN SPACE — the carrier's back-link
	//to its own launched fighters, used by HangarOps::syncIntegratedStructureCoupling
	//to detect per-turn COMBAT losses (Direction 2) by comparing each flight's current
	//countActiveCraft against its stored baseline (the baseline is decremented, not
	//treated as a loss, when craft reabsorb on landing — so docking isn't mistaken for
	//combat death). Entries are pruned when a flight lands fully or dies. The invariant
	//enforced each turn is carrier-remaining-structure >= $shadowAttached. Seeded from
	//the SHAD_FTRL count (populateInitialHangarUsage). Non-ShadowHangar: unused.
	public $shadowAttached = 0;
	public $shadowCutOff   = 0;
	public $shadowLaunched = array();      //{flightId => lastKnownActiveCraftCount} of integrated fighters in space
	//Cumulative carrier-Structure boxes PERMANENTLY lost to attached-fighter deaths
	//(B5W "the corresponding structure box is also lost (marked destroyed)"). Each
	//loss is BOTH 1 point of structure damage AND a -1 to the Structure's maxhealth —
	//so SelfRepair can never restore that box (it heals damage, but maxhealth is the
	//ceiling). Persisted in shadowIntegratedState and RE-APPLIED to the carrier's
	//Structure->maxhealth on load (maxhealth is a blueprint value rebuilt fresh each
	//load, so the reduction must be replayed). onIndividualNotesLoaded applies it.
	public $shadowStructLost = 0;
	private $lastSavedShadowState = null;  //serialized snapshot of last persisted {attached,cutOff,launched,structLost}
	//Stage 18: carrier-destruction escape roll. ONE-SHOT per carrier — set
	//when HangarOps::processCarrierDestructionEscapes fires the d20 (or when
	//a hangarEscapeRoll note is loaded). Only the PRIMARY hangar carries
	//these; persisted via the hangarEscapeRoll note. escapeRoll/Max/Total/
	//Names are shipped via stripForJson so the client can render the
	//replay/audit state on the destroyed-carrier row.
	public $escapeRolled = false;
	public $escapeRoll = 0;
	public $escapeMax = 0;
	public $escapeTotal = 0;
	public $escapeNames = array();
	public $pendingLaunchOrder = null;    //decoded latest hangarLaunchOrder for this turn (set by onIndividualNotesLoaded)
	public $pendingDockOrder = null;      //decoded latest hangarDockOrder for this turn (set by onIndividualNotesLoaded)

	//B5W Inadequate Hangars replay-determinism cache. setCriticals re-runs on
	//every replay scrub and Dice::d is non-deterministic, so each inadequate
	//launch/land roll is persisted in a hangarInadequateRoll note and read back
	//here on load. $inadequateLoadedTurn marks the turn the cache is valid for;
	//$inadequateLoadedRolls is an ordered FIFO queue of the rolls taken that turn
	//(consumed in the same order they were rolled). Mirrors railCritLoaded*.
	public $inadequateLoadedTurn  = 0;
	public $inadequateLoadedRolls = array();
	private $pendingLaunchTransfer = null;//launch payload received from client via doIndividualNotesTransfer; consumed in generateIndividualNotes
	private $pendingDockTransfer = null;  //dock payload received from client via doIndividualNotesTransfer; consumed in generateIndividualNotes

	// === LCV Rails (DockingCollar) — whole-ship dock state ===
	// An LCV rail holds at most ONE docked LCV. The persistent link is a per-rail
	// 'lcvDocked' note carrying {shipId, dockTurn, railDmgAtDock} (null = empty).
	// This is the LCV-rail analogue of $hangarUsage: mutated by the LCV perform
	// handlers, snapshot-persisted in generateIndividualNotes, reloaded (and the
	// LCV's $removed flag re-applied) in onIndividualNotesLoaded. Only meaningful
	// on $isLCVRail systems; left null/empty on ordinary hangars.
	public $lcvDocked = null;             //decoded {shipId, dockTurn, railDmgAtDock} of the LCV on this rail, or null
	private $lastSavedLcvDocked = null;   //serialized snapshot of last persisted lcvDocked (avoids duplicate notes)
	public $pendingLcvDockOrder   = null; //decoded latest lcvDockOrder for this turn (set by onIndividualNotesLoaded)
	public $pendingLcvLaunchOrder = null; //decoded latest lcvLaunchOrder for this turn (set by onIndividualNotesLoaded)
	private $pendingLcvDockTransfer   = null; //LCV dock payload from client; consumed in generateIndividualNotes
	private $pendingLcvLaunchTransfer = null; //LCV launch payload from client; consumed in generateIndividualNotes
	public $pendingDeployStartTransfer = null; //Stage 7: deployment-phase dock payload; consumed in generateIndividualNotes. Public so DeploymentGamePhase can exempt docked flights from movement validation BEFORE notes are generated.
	public $pendingLcvDeployStartTransfer = null; //LCV deploy-dock payload from client; consumed in generateIndividualNotes. Public so DeploymentGamePhase can exempt deploy-docked LCVs from movement validation.

    function __construct($armour, $maxhealth, $output = null, $direction = 0, $hangarType = 'fighters',  $spawnableClasses = array(), $excludeFromDefaultShuttles = false, $allowedFighterClasses = array()){
		if($output === null){ //if output is not explicitly indicated, assume it to be 6 per every full 6 boxes! (that's the msot typical capacity)
			//$output = floor($maxhealth/6)*6;
			$output = 6;
		}
		//Legacy ship files (eg. torata) pass a literal 0 as the 4th positional
		//arg, expecting it to be a no-op carried over from the pre-HangarOps
		//constructor signature. Coerce non-string/empty values back to the
		//universal default — inferHangarType (called on load) will then
		//narrow it from the ship's $fighters declaration when possible.
		if (!is_string($hangarType) || trim($hangarType) === '') {
			$hangarType = 'fighters';
		}
		$this->hangarType = $hangarType;
		$this->excludeFromDefaultShuttles = (bool)$excludeFromDefaultShuttles;
		//Per-bay fighter-class allow-list (empty = unrestricted). Stored as a
		//re-indexed list of phpclass strings; gated by HangarOps::hangarAcceptsFighterClass.
		$this->allowedFighterClasses = is_array($allowedFighterClasses) ? array_values($allowedFighterClasses) : array();
		$this->direction = (int)$direction;
		//Always include the generic shuttle classes — every hangar can launch
		//shuttles per B5W §10.1, and these are faction-agnostic. Faction-specific
		//default shuttles (e.g. Flyer for Minbari) are NOT baked into every
		//Hangar's spawnableClasses; they would force the client-side blueprint
		//preload to load every faction's shuttle regardless of who's in the
		//current game. Instead, game.php appends them via HangarOps::shuttleClassForFactionName
		//only for factions actually present (and carrying a Hangar) in this game.
		$defaults = array('Shuttle', 'MinesweepingShuttle');
		$extras = is_array($spawnableClasses) ? $spawnableClasses : array();
		$this->spawnableClasses = array_values(array_unique(array_merge($defaults, $extras)));
        parent::__construct($armour, $maxhealth, 0, $output );
    }

	public function onIndividualNotesLoaded($gamedata){
		//Sort notes chronologically so the most recent hangarUsage wins.
		//Phase numbers are NOT in chronological order (PreFiring=5 happens
		//between Movement=2 and Fire=3 due to historical addition), so we
		//sort by auto-increment id within a turn — ids are monotonic by
		//actual insertion time.
		usort($this->individualNotes, function($a, $b){
			if ($a->turn !== $b->turn) return ($a->turn < $b->turn) ? -1 : 1;
			return ($a->id < $b->id) ? -1 : 1;
		});

		foreach ($this->individualNotes as $note){
			if ($note->notekey === 'hangarUsage'){
				$decoded = json_decode($note->notevalue, true);
				if (is_array($decoded)){
					$this->hangarUsage = $decoded;
					$this->lastSavedUsage = $note->notevalue;
					$this->usagePopulated = true;
				}
			} else if ($note->notekey === 'hangarLaunchOrder' && $note->turn == $gamedata->turn){
				//Latest order wins (notes are pre-sorted by id ASC above)
				$decoded = json_decode($note->notevalue, true);
				if (is_array($decoded)) $this->pendingLaunchOrder = $decoded;
			} else if ($note->notekey === 'hangarDockOrder' && $note->turn == $gamedata->turn){
				//Latest dock order wins (notes are pre-sorted by id ASC above)
				$decoded = json_decode($note->notevalue, true);
				if (is_array($decoded)) $this->pendingDockOrder = $decoded;
			} else if ($note->notekey === 'hangarInadequateRoll' && $note->turn == $gamedata->turn){
				//B5W Inadequate Hangars: rebuild this turn's FIFO roll queue so a
				//replay scrub reproduces the launch-abort / landing-damage rolls
				//exactly (setCriticals re-runs and Dice::d is non-deterministic).
				//Notes are pre-sorted by id ASC above, so appending here preserves
				//the order the rolls were taken; nextInadequateRoll consumes them
				//in the same order. Mirrors FighterRail's railCritRoll read-back.
				$decoded = json_decode($note->notevalue, true);
				if (is_array($decoded) && isset($decoded['roll'])){
					$this->inadequateLoadedTurn    = (int)$gamedata->turn;
					$this->inadequateLoadedRolls[] = (int)$decoded['roll'];
				}
			} else if ($note->notekey === 'hangarOrdReserve'){
				//Stage 15: total reload points spent so far on this carrier.
				//Only the primary (first) hangar persists this; secondary
				//hangars' notes (if any leaked through) get ignored on the
				//non-primary copies because HangarOps::reloadPoolSpent only
				//reads from the primary.
				$spent = (int)$note->notevalue;
				if ($spent < 0) $spent = 0;
				$this->reloadPoolSpent = $spent;
				$this->lastSavedOrdReserve = (string)$spent;
			} else if ($note->notekey === 'hangarMarineReserve'){
				//Stage 17 ext: total marine pool points spent so far. Parallel
				//to hangarOrdReserve — only the primary hangar persists this.
				$spent = (int)$note->notevalue;
				if ($spent < 0) $spent = 0;
				$this->marinePoolSpent = $spent;
				$this->lastSavedMarinePool = (string)$spent;
			} else if ($note->notekey === 'shadowIntegratedState'){
				//Stage S (S-d): integrated-fighter coupling {attached,cutOff,launchedIds}.
				//Parallel to hangarOrdReserve — only the primary ShadowHangar persists
				//this. Presence marks the state as initialised, so populateInitial...
				//won't re-seed it on a mid-game reload.
				$decoded = json_decode($note->notevalue, true);
				if (is_array($decoded)){
					$this->shadowAttached = max(0, (int)($decoded['attached'] ?? 0));
					$this->shadowCutOff   = max(0, (int)($decoded['cutOff']   ?? 0));
					$map = (isset($decoded['launched']) && is_array($decoded['launched'])) ? $decoded['launched'] : array();
					$clean = array();
					foreach ($map as $fid => $cnt){ $clean[(int)$fid] = max(0, (int)$cnt); }
					$this->shadowLaunched = $clean;
					$this->shadowStructLost = max(0, (int)($decoded['structLost'] ?? 0));
					$this->lastSavedShadowState = $note->notevalue;
					//maxhealth reduction is applied ONCE after the loop (below) using the
					//FINAL loaded value — NOT here, because the loop visits every
					//shadowIntegratedState note (one per changed turn) and a per-note
					//cumulative subtraction would over-reduce. Latest-wins on the value.
				}
			} else if ($note->notekey === 'hangarEscapeRoll'){
				//Stage 18: d20 result + escapees from the moment this carrier
				//was destroyed. Presence of this note is the one-shot gate
				//that stops processCarrierDestructionEscapes from re-rolling
				//on a later turn's setCriticals sweep.
				$decoded = json_decode($note->notevalue, true);
				if (is_array($decoded)){
					$this->escapeRolled = true;
					$this->escapeRoll   = (int)($decoded['roll']  ?? 0);
					$this->escapeMax    = (int)($decoded['max']   ?? 0);
					$this->escapeTotal  = (int)($decoded['total'] ?? 0);
					$this->escapeNames  = is_array($decoded['names'] ?? null) ? $decoded['names'] : array();
				}
			} else if ($note->notekey === 'lcvDocked'){
				//LCV Rails: persistent rail→LCV link. A null/empty notevalue means
				//the rail was emptied (launch / forced launch). Latest note wins
				//(notes pre-sorted by id ASC) so dock-then-launch in one turn ends
				//empty. lastSavedLcvDocked tracks the snapshot to avoid re-writing.
				$decoded = json_decode($note->notevalue, true);
				$this->lcvDocked = (is_array($decoded) && !empty($decoded['shipId'])) ? $decoded : null;
				$this->lastSavedLcvDocked = $note->notevalue;
			} else if ($note->notekey === 'lcvDockOrder' && $note->turn == $gamedata->turn){
				$decoded = json_decode($note->notevalue, true);
				if (is_array($decoded)) $this->pendingLcvDockOrder = $decoded;
			} else if ($note->notekey === 'lcvLaunchOrder' && $note->turn == $gamedata->turn){
				$decoded = json_decode($note->notevalue, true);
				if (is_array($decoded)) $this->pendingLcvLaunchOrder = $decoded;
			}
		}
		$this->individualNotes = array();

		//LCV Rails: re-apply $removed to the LCV docked on THIS rail so it stays
		//off the board across reloads (parallel to the dockedFlightId restoration
		//below). The LCV is a full ship row, removed at dock time and resurrected
		//on launch. getUnit()/getShipById resolve below; do it after $ship is set.

		//Re-apply $removed flag to any flights stored in THIS hangar (via
		//dockedFlightId markers). Walking only $this->hangarUsage avoids
		//duplicating work — every hangar on the ship runs this for its own
		//entries, so the union covers every docked flight on the carrier.
		//$gamedata->ships is numerically-indexed when loaded from DB; only
		//getShipById() is reliable for looking up by ship id.
		$ship = $this->getUnit();

		//Stage S (S-d): RE-APPLY the permanent integrated-fighter structure-box loss,
		//ONCE, using the final loaded $shadowStructLost. maxhealth is a blueprint value
		//rebuilt fresh on every load, so the cumulative reduction must be replayed here.
		//Applied once (after the note loop) so multiple shadowIntegratedState notes don't
		//each subtract. Only the primary ShadowHangar carries the count; the matching
		//per-box DamageEntries are persisted separately (replay-safe). This only lowers
		//the ceiling so SelfRepair (heals damage, can't raise maxhealth) can't restore it.
		//Structure::stripForJson sends the live maxhealth so the CLIENT matches on replay.
		if ($ship && !empty($this->isShadowHangar) && (int)$this->shadowStructLost > 0
			&& HangarOps::primaryShadowHangar($ship) === $this) {
			$struct = $ship->getStructureSystem(0);
			if ($struct){
				$struct->maxhealth = max(0, (int)$struct->maxhealth - (int)$this->shadowStructLost);
			}
		}

		//LCV Rails: restore the docked LCV's $removed flag (whole-ship dock).
		if ($ship && !empty($this->isLCVRail) && is_array($this->lcvDocked)) {
			$lcvId = (int)($this->lcvDocked['shipId'] ?? 0);
			if ($lcvId > 0) {
				$lcv = $gamedata->getShipById($lcvId);
				if ($lcv) {
					$lcv->removed = true;
					$lcv->removedTurn = (int)($this->lcvDocked['dockTurn'] ?? $gamedata->turn);
				}
			}
		}
		if ($ship && is_array($this->hangarUsage)) {
			foreach ($this->hangarUsage as $entry) {
				$flightId = isset($entry['dockedFlightId']) ? (int)$entry['dockedFlightId'] : 0;
				if ($flightId <= 0) continue;
				$flight = $gamedata->getShipById($flightId);
				if (!$flight) continue;
				$flight->removed = true;
				if (isset($entry['dockedTurn'])) $flight->removedTurn = (int)$entry['dockedTurn'];
				//A fragment flight (partial-dock detachment) was born $removed at
				//the dock turn and never existed on the board. $spawned isn't a
				//tac_ship column, so restore it here from dockedTurn — the replay
				//hides a flight whose spawned == removedTurn on that turn instead
				//of rendering a phantom detachment beside the still-full source.
				if (!empty($entry['fragment']) && isset($entry['dockedTurn'])) {
					$flight->spawned = (int)$entry['dockedTurn'];
				}
			}
		}

		//Narrow universal-default hangarType from the ship's $fighters
		//declaration (eg. Var'Nic's lone medium bay). Runs every load so a
		//ship-file change picks up without DB migration; idempotent because
		//inferHangarType only acts on universal/empty types.
		if ($ship) HangarOps::inferHangarType($this, $ship);

		if ($this->usagePopulated) return;

		if (!$ship) return;

		//First hangar on a multi-hangar ship runs the initial population for all hangars
		foreach ($ship->systems as $sys){
			if ($sys instanceof Hangar && $sys !== $this && $sys->usagePopulated){
				$this->usagePopulated = true;
				return;
			}
		}

		HangarOps::populateInitialHangarUsage($ship, $gamedata);
	}

	public function generateIndividualNotes($gamedata, $dbManager){
		$ship = $this->getUnit();
		if (!$ship) return;
		/* PLACEMENT turn, not arrival turn. The deploy-start dock block below is resolved RIGHT
		   NOW rather than at end of turn, and for a reinforcement carrier "now" is the Deployment
		   phase of the turn BEFORE it arrives - its only Deployment phase. Reading getTurnDeployed
		   here returned before that block ever ran, so a queued dock was silently dropped: the
		   carrier arrived correctly on turn N and its fighters appeared at their off-board 'start'
		   markers instead of in the hangar (user report, game 4302).
		   Safe to run a turn early: the launch/dock/LCV order blocks are all no-ops unless the
		   client POSTed a matching transfer (impossible for a unit with no phases of its own), and
		   every snapshot tail is change-detected, so nothing spurious is written. */
		if ($ship->getTurnPlaced($gamedata) > $gamedata->turn) return;

		//A destroyed carrier never processes launch/dock/pool orders, but it MUST
		//still reach the hangarUsage snapshot tail below: Stage 18's carrier-
		//destruction escape (processCarrierDestructionEscapes, Pass 3) clears each
		//hangar's $hangarUsage in memory after resurrecting/spawning escapees. If
		//that empty snapshot isn't persisted, the latest hangarUsage note in the
		//DB stays the pre-destruction one (with its dockedFlightId entries), and
		//onIndividualNotesLoaded re-applies $removed=true to the resurrected
		//escapees on the next load — they vanish from the board. The snapshot
		//tail's own change-detection ($current === $this->lastSavedUsage) keeps a
		//carrier destroyed on a PREVIOUS turn (already-empty, already-persisted)
		//from emitting a redundant note. The jump path (processJumpingCarrierDockOrders)
		//docks pending flights into a jumping carrier's hangar in the same pass and
		//likewise relies on this snapshot persisting. So: skip the order blocks for
		//a destroyed carrier, but always fall through to the hangarUsage snapshot.
		$destroyed = $ship->isDestroyed();
		if (!$destroyed) {

		//Persist any pending launch order received from the client. Validation
		//(canLaunch) happens at end-of-turn during processLaunchOrders — this
		//path just records the player's intent.
		if ($this->pendingLaunchTransfer !== null && HangarOps::isFlowEnabled($gamedata->id)) {
			$this->individualNotes[] = new IndividualNote(
				-1,
				$gamedata->id,
				$gamedata->turn,
				$gamedata->phase,
				$ship->id,
				$this->id,
				'hangarLaunchOrder',
				'Hangar launch order',
				json_encode($this->pendingLaunchTransfer)
			);
			$this->pendingLaunchTransfer = null;   //consumed
		}

		//Same pattern for dock orders (Stage 5).
		if ($this->pendingDockTransfer !== null && HangarOps::isFlowEnabled($gamedata->id)) {
			$this->individualNotes[] = new IndividualNote(
				-1,
				$gamedata->id,
				$gamedata->turn,
				$gamedata->phase,
				$ship->id,
				$this->id,
				'hangarDockOrder',
				'Hangar dock order',
				json_encode($this->pendingDockTransfer)
			);
			$this->pendingDockTransfer = null;     //consumed
		}

		//LCV Rails: persist pending LCV dock/launch orders (resolved end-of-turn
		//in criticalPhaseEffects via processLCVDockOrders/processLCVLaunchOrders).
		//Guarded on isLCVRail so a normal hangar never persists an LCV note even if
		//a malformed payload set the transfer field.
		if (!empty($this->isLCVRail) && $this->pendingLcvDockTransfer !== null && HangarOps::isFlowEnabled($gamedata->id)) {
			$this->individualNotes[] = new IndividualNote(
				-1, $gamedata->id, $gamedata->turn, $gamedata->phase, $ship->id, $this->id,
				'lcvDockOrder', 'LCV rail dock order', json_encode($this->pendingLcvDockTransfer)
			);
			$this->pendingLcvDockTransfer = null;  //consumed
		}
		if (!empty($this->isLCVRail) && $this->pendingLcvLaunchTransfer !== null && HangarOps::isFlowEnabled($gamedata->id)) {
			$this->individualNotes[] = new IndividualNote(
				-1, $gamedata->id, $gamedata->turn, $gamedata->phase, $ship->id, $this->id,
				'lcvLaunchOrder', 'LCV rail launch order', json_encode($this->pendingLcvLaunchTransfer)
			);
			$this->pendingLcvLaunchTransfer = null;  //consumed
		}

		//Stage 7: deployment-phase dock. Unlike launch/dock orders (resolved at
		//end-of-turn via criticalPhaseEffects in Fire Phase), deploy-start docks
		//are resolved RIGHT NOW: this hook fires during DeploymentGamePhase::process,
		//which is the only chance to mutate $hangarUsage before the snapshot is
		//written below. HangarOps::processDeployStartTransfer validates each
		//entry, applies the dock (appends to $hangarUsage, marks flight $removed),
		//writes a hangarDeployStartEvent audit note, and clears the transfer.
		//
		//IMPORTANT: $this is the POST-side Hangar (rebuilt from the client's
		//submission) and has an empty $hangarUsage by default — system objects
		//are reconstructed from POST data without their persisted individual
		//notes. Seed from the DB-loaded counterpart hangar so pre-existing
		//entries (auto-filled shuttles, prior docks) survive the snapshot write
		//below; otherwise a Stage 7 dock would replace the whole hangar with
		//just the newly-docked flight.
		if ($this->pendingDeployStartTransfer !== null && HangarOps::isFlowEnabled($gamedata->id)) {
			//Seed THIS POST-side hangar's usage from the DB counterpart so its
			//pre-existing entries (auto-shuttles, prior docks) survive the snapshot.
			$dbShip = $gamedata->getShipById($ship->id);
			if ($dbShip && is_array($dbShip->systems)) {
				foreach ($dbShip->systems as $dbSys) {
					if (!($dbSys instanceof Hangar)) continue;
					if ((int)$dbSys->id !== (int)$this->id) continue;
					if (is_array($dbSys->hangarUsage)) {
						$this->hangarUsage = $dbSys->hangarUsage;
					}
					break;
				}
			}
			//Stage 21 (no-split): coalesce this flight's per-bay deploy orders
			//across all POST-side bays into ONE occupancy entry, using the client's
			//per-bay counts (which were distributed against true capacity client
			//-side — the POST-side siblings have empty usage so the server must NOT
			//re-distribute). Runs from each hangar that still holds orders; the
			//helper consumes the orders it folds in so siblings no-op.
			HangarOps::processDeployStartTransfer($this, $ship, $gamedata, $dbShip);
		}

		//LCV Rails: resolve a deployment-phase LCV deploy-dock immediately (like
		//the fighter deployStart above). Marks the LCV removed + sets this rail's
		//lcvDocked; the lcvDocked snapshot tail below persists it.
		if ($this->pendingLcvDeployStartTransfer !== null && !empty($this->isLCVRail) && HangarOps::isFlowEnabled($gamedata->id)) {
			HangarOps::processLcvDeployStartTransfer($this, $ship, $gamedata);
		}

		//Stage 15: persist hangarOrdReserve (ordnance pool spent) ONLY on the
		//primary hangar. drawReload always writes to the primary; the snapshot
		//compare here ensures we only write a note when the value actually
		//changed. Must run BEFORE the hangarUsage early-return below — while
		//fighters sit docked turn after turn hangarUsage is stable, but the
		//ordnance pool can still be drawing down each turn and needs to persist.
		//Mirror of hangarUsage's "don't write a useless first-time note" guard:
		//player-submission paths (DeploymentGamePhase::process etc.) rebuild
		//ships from POST JSON via Manager::getShipsFromJSON, producing fresh
		//Hangar instances with reloadPoolSpent=0 and lastSavedOrdReserve=null
		//(notes are only loaded for server-side reads via getTacGamedata).
		//Without this guard, a spurious "0" note would be written every player
		//submission and clobber the authoritative server-side "spent" value.
		if (HangarOps::primaryHangar($ship) === $this
			&& !($this->lastSavedOrdReserve === null && (int)$this->reloadPoolSpent === 0)) {
			$currentOrd = (string)(int)$this->reloadPoolSpent;
			if ($currentOrd !== $this->lastSavedOrdReserve){
				$this->individualNotes[] = new IndividualNote(
					-1,
					$gamedata->id,
					$gamedata->turn,
					$gamedata->phase,
					$ship->id,
					$this->id,
					'hangarOrdReserve',
					'Hangar ordnance pool spent',
					$currentOrd
				);
				$this->lastSavedOrdReserve = $currentOrd;
			}
		}

		//Stage 17 ext: same primary-only persistence for marine pool, same
		//POST-side reconstruction guard.
		if (HangarOps::primaryHangar($ship) === $this
			&& !($this->lastSavedMarinePool === null && (int)$this->marinePoolSpent === 0)) {
			$currentMar = (string)(int)$this->marinePoolSpent;
			if ($currentMar !== $this->lastSavedMarinePool){
				$this->individualNotes[] = new IndividualNote(
					-1,
					$gamedata->id,
					$gamedata->turn,
					$gamedata->phase,
					$ship->id,
					$this->id,
					'hangarMarineReserve',
					'Hangar marine pool spent',
					$currentMar
				);
				$this->lastSavedMarinePool = $currentMar;
			}
		}

		} //endif (!$destroyed) — order/pool blocks skipped for a destroyed carrier

		//LCV Rails: persist the rail→LCV link snapshot (parallel to hangarUsage).
		//Runs for BOTH alive and destroyed carriers: a forced launch (destroyed
		//rail or destroyed carrier) clears lcvDocked, and that cleared state MUST
		//persist or reload would re-apply $removed to the launched LCV. Change-
		//detection via lastSavedLcvDocked avoids a redundant note while an LCV
		//sits docked turn after turn. The empty-first-time guard mirrors hangarUsage.
		if (!empty($this->isLCVRail)) {
			$currentLcv = json_encode($this->lcvDocked === null ? null : $this->lcvDocked);
			$lcvUnchanged = ($currentLcv === $this->lastSavedLcvDocked);
			$lcvEmptyFirst = ($this->lastSavedLcvDocked === null && $this->lcvDocked === null);
			if (!$lcvUnchanged && !$lcvEmptyFirst) {
				$this->individualNotes[] = new IndividualNote(
					-1, $gamedata->id, $gamedata->turn, $gamedata->phase, $ship->id, $this->id,
					'lcvDocked', 'LCV rail occupant', $currentLcv
				);
				$this->lastSavedLcvDocked = $currentLcv;
			}
		}

		//Stage S (S-d): persist the integrated-fighter coupling counts {attached,cutOff}
		//on the PRIMARY ShadowHangar (parallel to hangarOrdReserve, but OUTSIDE the
		//!$destroyed guard — the final attached-fighter loss that destroys the carrier
		//must record its terminal state for replay, like hangarUsage/lcvDocked do).
		//POST-side reconstruction guard mirrors the pool notes: skip the spurious
		//first-time "0/0" note that a rebuilt-from-JSON Hangar (lastSavedShadowState
		//null, both counts 0) would otherwise write and clobber server state.
		if (!empty($this->isShadowHangar) && HangarOps::primaryHangar($ship) === $this
			&& !($this->lastSavedShadowState === null && (int)$this->shadowAttached === 0 && (int)$this->shadowCutOff === 0 && (int)$this->shadowStructLost === 0 && empty($this->shadowLaunched))) {
			//Re-key the {id=>count} map with string keys so json_encode emits an object
			//(not a list), and so it round-trips identically for change-detection.
			$launchedOut = array();
			foreach ((array)$this->shadowLaunched as $fid => $cnt){ $launchedOut[(string)(int)$fid] = (int)$cnt; }
			$currentShadow = json_encode(array(
				'attached'   => (int)$this->shadowAttached,
				'cutOff'     => (int)$this->shadowCutOff,
				'launched'   => (object)$launchedOut,
				'structLost' => (int)$this->shadowStructLost,
			));
			if ($currentShadow !== $this->lastSavedShadowState){
				$this->individualNotes[] = new IndividualNote(
					-1, $gamedata->id, $gamedata->turn, $gamedata->phase, $ship->id, $this->id,
					'shadowIntegratedState', 'Integrated fighter coupling', $currentShadow
				);
				$this->lastSavedShadowState = $currentShadow;
			}
		}

		//Persist hangarUsage snapshot, but only if it has actually changed
		//since the last saved snapshot. Stage 4+ docking/launching mutate
		//$hangarUsage and rely on this to snapshot it. A destroyed carrier reaches
		//here too (escape cleared its hangarUsage — see the $destroyed note above).
		$current = json_encode($this->hangarUsage);
		if ($current === $this->lastSavedUsage) return;
		//Don't write a useless first-time note for an empty hangar
		if ($this->lastSavedUsage === null && empty($this->hangarUsage)) return;

		$this->individualNotes[] = new IndividualNote(
			-1,
			$gamedata->id,
			$gamedata->turn,
			$gamedata->phase,
			$ship->id,
			$this->id,
			'hangarUsage',
			'Hangar contents',
			$current
		);
		$this->lastSavedUsage = $current;
	}

	public function criticalPhaseEffects($ship, $gamedata){
		parent::criticalPhaseEffects($ship, $gamedata);   //preserve base hooks (limpet bore, marine missions, etc.)

		//LCV Rails (B5W §10.1): a rail docks/launches a WHOLE LCV — none of the
		//FighterFlight stash pipeline applies. Order: (1) if this rail is destroyed
		//this turn and holds a docked LCV, force the LCV to launch + take fragment
		//damage (onLCVRailDestroyed), so a wiped rail can't also dock/launch a
		//fighter-style order; (2) otherwise process LCV dock then launch orders.
		//Carrier-death disposition of docked LCVs is handled centrally by
		//HangarOps::processLCVCarrierDestruction (Pass 3 sibling), not here.
		if (!empty($this->isLCVRail)) {
			if ($ship->isDestroyed()) return;   //carrier dead → Pass 3 owns docked LCVs
			if (HangarOps::onLCVRailDestroyed($this, $ship, $gamedata)) return;
			HangarOps::processLCVDockOrders($this, $ship, $gamedata);
			HangarOps::processLCVLaunchOrders($this, $ship, $gamedata);
			return;
		}

		//Hangar Ops Stage 16.3/16.4: a catapult RECOVERS (rear-approach, 16.4)
		//and LAUNCHES (no initiative penalty, fixed forward, regardless of damage,
		//16.3) its single fighter. Dock is processed before launch, matching the
		//hangar order. The hangar-style damage eviction (a catapult ignores its
		//own damage) and reload servicing are deliberately NOT run for catapults;
		//the landing-damage rule is added in 16.5.
		if (!empty($this->isCatapult)) {
			HangarOps::processDockOrders($this, $ship, $gamedata);
			HangarOps::processLaunchOrders($this, $ship, $gamedata);
			return;
		}

		//Fighter Rails (B5W §10.1): a rail launches/lands like an ordinary hangar
		//(it has an output budget and respects its own damage) PLUS two unique
		//mechanics — the structure-coupled 1d20 "whole rail destroyed" crit and
		//per-box fighter escape. The rail-specific destruction runs FIRST so a
		//rail wiped this turn can't also launch/recover this turn.
		if (!empty($this->isRail)) {
			//R0: a destroyed carrier's docked craft escape is owned by Stage 18's
			//Pass 3 (processCarrierDestructionEscapes) — don't double-spawn here.
			//(Mirrors HangarOps::onHangarCriticalPhase's $ship->isDestroyed guard;
			//covers the cascade case where a still-alive ship at Pass 1 start is
			//destroyed mid-pass and still has its Pass 2 hooks run.)
			if ($ship->isDestroyed()) return;

			//R1a: full external-structure-block loss. If this rail's parent
			//structure was destroyed this turn, the whole block (all its rails)
			//is gone — every docked fighter attempts escape, and the 1d20 crit is
			//moot (skip it). Carrier-death (PRIMARY structure) is handled by Stage
			//18 instead; an external block dying leaves the carrier alive.
			if (HangarOps::onRailStructureLost($this, $ship, $gamedata)) {
				//Block destroyed — fall through to the ordinary-hangar pipeline so
				//onHangarCriticalPhase evicts the now-zero-capacity rail cleanly.
				HangarOps::onHangarCriticalPhase($this, $ship, $gamedata);
				return;
			}

			//R1b: 1d20-on-16-20 whole-rail destruction + per-box escape, fired when
			//this rail's parent structure took damage this turn (per-structure,
			//per-turn dedup inside onRailStructureDamage).
			HangarOps::onRailStructureDamage($this, $ship, $gamedata);

			//R2-R5: same pipeline as an ordinary hangar. onHangarCriticalPhase
			//evicts stored craft to fit any boxes the rail crit destroyed (and
			//resets the per-turn launch/land budget); serviceDockedFlights honours
			//the rail half-cadence gate internally (RAIL-3).
			HangarOps::onHangarCriticalPhase($this, $ship, $gamedata);
			//Stage 21: docks resolve once per carrier via the whole-flight coalescer
			//(no fragments). Guarded; the first non-catapult bay to reach this runs
			//it, the rest no-op. Replaces the per-bay processDockOrders.
			HangarOps::processWholeFlightDocks($ship, $gamedata);
			HangarOps::serviceDockedFlights($this, $ship, $gamedata);
			//Kirishiac Warrior regeneration: carrier-level sweep (guarded, first
			//bay runs it) — must run BEFORE launches so a launch ordered on the
			//turn the dwell completes carries the regenerated flight out. NOT
			//behind the rail half-cadence gate: the regrowth clock is biological,
			//not airlock throughput, so a rail-docked flight regenerates on the
			//same 5-full-turn schedule as a hangar-docked one.
			HangarOps::applyDockedRegeneration($ship, $gamedata);
			//Stage 21: launches resolve once per carrier via the whole-flight
			//coalescer (no fragments). Guarded; first bay runs it, the rest no-op.
			HangarOps::processWholeFlightLaunches($ship, $gamedata);
			return;
		}

		//1. Apply damage eviction first (per B5W rules: boxes destroyed before
		//   Post-Turn Actions). This may also reduce stored craft a launch
		//   order was relying on — if so, the launch's canLaunch() check fails
		//   gracefully below. Pass $ship/$gamedata so eviction of dockedFlightId
		//   stash records can also disengage the corresponding fighters in the
		//   source flight, keeping its rendered combat value in sync.
		HangarOps::onHangarCriticalPhase($this, $ship, $gamedata);

		//2. Process queued dock orders BEFORE launches. Stage 21: whole-flight
		//   coalescer (no fragments), once per carrier — a flight docks as ONE
		//   ship with occupancy spanning bays. landedThisTurn increments here so
		//   the launch path sees the correct used budget.
		HangarOps::processWholeFlightDocks($ship, $gamedata);

		//3. Service flights docked a full turn (reload ammo on reloadable
		//   weapons, etc.) BEFORE launches, so a flight that spent this turn
		//   docked still earns its reload on the very turn it relaunches.
		//   serviceDockedFlights skips flights that docked THIS turn
		//   (dockedTurn == current turn); a launch later this step just carries
		//   the freshly-reloaded ammo out with it.
		HangarOps::serviceDockedFlights($this, $ship, $gamedata);

		//3b. Kirishiac Warrior regeneration: carrier-level sweep (guarded, first
		//    bay runs it). A docked flight whose entry's regenTurn has arrived is
		//    restored to full strength — destroyed craft regrown, damage healed.
		//    Runs AFTER docks (a flight docking this turn stamps regenTurn in the
		//    future, so it's skipped) and BEFORE launches (a launch ordered on the
		//    completion turn carries the regenerated flight out; launching earlier
		//    forfeits regeneration entirely).
		HangarOps::applyDockedRegeneration($ship, $gamedata);

		//4. Process queued launch orders (Post-Turn Actions Step). Stage 21:
		//   whole-flight coalescer, once per carrier — a docked flight is ONE
		//   entry; full launch resurrects it, partial spawns a "- Split" K-flight
		//   and shrinks the original in place. Only runs in the Fire Phase advance.
		HangarOps::processWholeFlightLaunches($ship, $gamedata);
	}

	/* Receives a JSON-encoded list of launch orders from the client. Per the
	 * standard FV pattern (see HyachComputer, AdaptiveArmorController) this
	 * runs DURING ship reconstruction — TacGamedata statics aren't yet set
	 * and writing notes here would fail with a NULL gameid. So we just
	 * stash the validated payload into $pendingLaunchTransfer, which
	 * generateIndividualNotes consumes later in the same request once
	 * gamedata is hydrated.
	 *
	 * Expected payload shape (JSON string):
	 *   [{"phpclass":"Shuttle","size":3}, {"phpclass":"MinesweepingShuttle","size":6}]
	 */
	public function doIndividualNotesTransfer(){
		$raw = $this->individualNotesTransfer;
		$this->individualNotesTransfer = '';
		if (!is_string($raw) || $raw === '') return;

		$payload = json_decode($raw, true);
		if (!is_array($payload) || empty($payload)) return;

		//Three accepted shapes:
		//  legacy (Stage 4): a list of {phpclass, size}                    → all launches
		//  Stage 5:          {"launches": [...], "docks": [...]}
		//  Stage 7:          + {"deployStarts": [{"flightId": X}, ...]}    → deployment-phase dock orders
		//  LCV Rails:        + {"lcvDocks": [{shipId, thrustLeft}], "lcvLaunches": [{shipId}]}
		$launches     = array();
		$docks        = array();
		$deployStarts = array();
		//Whether the client EXPLICITLY sent a launches/docks/deployStarts key — distinguishes
		//"untouched" (no key) from "intentionally cleared" (key present, empty).
		//The empty-array case must still create a note so onIndividualNotesLoaded
		//can replace any prior order from earlier in the same phase.
		$hasLaunchKey      = false;
		$hasDockKey        = false;
		$hasDeployStartKey = false;
		$lcvDocks    = array();
		$lcvLaunches = array();
		$lcvDeployStarts = array();
		$hasLcvDockKey   = false;
		$hasLcvLaunchKey = false;
		$hasLcvDeployStartKey = false;
		$keyed = array_key_exists('launches', $payload) || array_key_exists('docks', $payload)
			|| array_key_exists('deployStarts', $payload) || array_key_exists('lcvDocks', $payload)
			|| array_key_exists('lcvLaunches', $payload) || array_key_exists('lcvDeployStarts', $payload);
		if ($keyed) {
			$hasLaunchKey      = array_key_exists('launches',     $payload);
			$hasDockKey        = array_key_exists('docks',        $payload);
			$hasDeployStartKey = array_key_exists('deployStarts', $payload);
			$hasLcvDockKey     = array_key_exists('lcvDocks',     $payload);
			$hasLcvLaunchKey   = array_key_exists('lcvLaunches',  $payload);
			$hasLcvDeployStartKey = array_key_exists('lcvDeployStarts', $payload);
			$launches     = is_array($payload['launches']     ?? null) ? $payload['launches']     : array();
			$docks        = is_array($payload['docks']        ?? null) ? $payload['docks']        : array();
			$deployStarts = is_array($payload['deployStarts'] ?? null) ? $payload['deployStarts'] : array();
			$lcvDocks     = is_array($payload['lcvDocks']     ?? null) ? $payload['lcvDocks']     : array();
			$lcvLaunches  = is_array($payload['lcvLaunches']  ?? null) ? $payload['lcvLaunches']  : array();
			$lcvDeployStarts = is_array($payload['lcvDeployStarts'] ?? null) ? $payload['lcvDeployStarts'] : array();
		} else {
			//assume legacy launch-only payload
			$launches = $payload;
			$hasLaunchKey = true;
		}

		//Sanitise launches: keep only well-formed {phpclass, size} entries.
		//Stage 8.5: an optional per-entry "direction" (0..5) is preserved when
		//the hangar advertises a multi-direction picker; it overrides the
		//hangar's default $direction at end-of-turn resolution.
		$cleanLaunches = array();
		foreach ($launches as $order) {
			if (!is_array($order)) continue;
			$phpclass = isset($order['phpclass']) ? (string)$order['phpclass'] : '';
			$size     = isset($order['size'])     ? (int)$order['size']        : 0;
			if ($phpclass === '' || $size <= 0) continue;
			$clean = array('phpclass' => $phpclass, 'size' => $size);
			if (array_key_exists('direction', $order)) {
				$dir = (int)$order['direction'];
				$dir = (($dir % 6) + 6) % 6;
				$clean['direction'] = $dir;
			}
			//Stage 21: the docked flight's id so the carrier-level launch
			//coalescer targets the exact docked flight (vs two same-class flights
			//or a multi-bay entry living on another bay). Optional for legacy.
			if (isset($order['dockedFlightId']) && (int)$order['dockedFlightId'] > 0) {
				$clean['dockedFlightId'] = (int)$order['dockedFlightId'];
			}
			$cleanLaunches[] = $clean;
		}
		if ($hasLaunchKey) $this->pendingLaunchTransfer = $cleanLaunches;

		//Sanitise docks: {flightId, count}
		$cleanDocks = array();
		foreach ($docks as $order) {
			if (!is_array($order)) continue;
			$flightId = isset($order['flightId']) ? (int)$order['flightId'] : 0;
			$count    = isset($order['count'])    ? (int)$order['count']    : 0;
			if ($flightId <= 0 || $count <= 0) continue;
			$cleanDocks[] = array('flightId' => $flightId, 'count' => $count);
		}
		if ($hasDockKey) $this->pendingDockTransfer = $cleanDocks;

		//Stage 7: sanitise deployStarts: {flightId} (+ optional per-hangar count
		//for a Fighter-Rail auto-distribute spread — a flight too big for any one
		//bay is split across several, each order carrying its slice).
		$cleanDeployStarts = array();
		foreach ($deployStarts as $order) {
			if (!is_array($order)) continue;
			$flightId = isset($order['flightId']) ? (int)$order['flightId'] : 0;
			if ($flightId <= 0) continue;
			$clean = array('flightId' => $flightId);
			if (isset($order['count']) && (int)$order['count'] > 0) {
				$clean['count'] = (int)$order['count'];
			}
			$cleanDeployStarts[] = $clean;
		}
		if ($hasDeployStartKey) $this->pendingDeployStartTransfer = $cleanDeployStarts;

		//LCV Rails: sanitise lcvDocks {shipId, thrustLeft} and lcvLaunches {shipId}.
		//thrustLeft is the client's getRemainingEngineThrust for the LCV (the dock
		//gate's "1 thrust unspent" check, bounds-checked server-side at resolution).
		$cleanLcvDocks = array();
		foreach ($lcvDocks as $order) {
			if (!is_array($order)) continue;
			$shipId = isset($order['shipId']) ? (int)$order['shipId'] : 0;
			if ($shipId <= 0) continue;
			$cleanLcvDocks[] = array(
				'shipId'     => $shipId,
				'thrustLeft' => isset($order['thrustLeft']) ? (int)$order['thrustLeft'] : 0,
			);
		}
		if ($hasLcvDockKey) $this->pendingLcvDockTransfer = $cleanLcvDocks;

		$cleanLcvLaunches = array();
		foreach ($lcvLaunches as $order) {
			if (!is_array($order)) continue;
			$shipId = isset($order['shipId']) ? (int)$order['shipId'] : 0;
			if ($shipId <= 0) continue;
			$cleanLcvLaunches[] = array('shipId' => $shipId);
		}
		if ($hasLcvLaunchKey) $this->pendingLcvLaunchTransfer = $cleanLcvLaunches;

		//LCV Rails: deployment-phase deploy-dock orders {shipId}. Resolved
		//immediately in DeploymentGamePhase::process (like fighter deployStarts).
		$cleanLcvDeployStarts = array();
		foreach ($lcvDeployStarts as $order) {
			if (!is_array($order)) continue;
			$shipId = isset($order['shipId']) ? (int)$order['shipId'] : 0;
			if ($shipId <= 0) continue;
			$cleanLcvDeployStarts[] = array('shipId' => $shipId);
		}
		if ($hasLcvDeployStartKey) $this->pendingLcvDeployStartTransfer = $cleanLcvDeployStarts;
	}

	public function stripForJson(){
		$strippedSystem = parent::stripForJson();
		$strippedSystem->hangarType = $this->hangarType;
		$strippedSystem->direction = $this->direction;
		$strippedSystem->directions = is_array($this->directions) ? array_values($this->directions) : array();
		$strippedSystem->isCatapult = !empty($this->isCatapult);   //Stage 16: catapult discriminator (false for ordinary hangars)
		$strippedSystem->isRail = !empty($this->isRail);           //Fighter Rails: rail discriminator (false for ordinary hangars/catapults)
		$strippedSystem->isLCVRail = !empty($this->isLCVRail);     //LCV Rails: whole-ship dock discriminator (false for ordinary hangars)
		$strippedSystem->isShadowHangar = !empty($this->isShadowHangar); //Stage S: integrated-fighter bay discriminator (false for ordinary hangars). $name stays 'hangar' so the launch/dock UI still applies; the client reads this flag for shuttle-pool exclusion / display.
		if (isset($this->bombGroupIndex)) $strippedSystem->bombGroupIndex = (int)$this->bombGroupIndex; //Stage S multi-bay: pairs this bay to its own Fighter Bomb (client per-bay pool display)
		$strippedSystem->excludeFromDefaultShuttles = !empty($this->excludeFromDefaultShuttles); //steers default shuttles away from this bay (boxes still count toward capacity)
		$strippedSystem->inadequate = !empty($this->inadequate); //Inadequate Hangars (Unreliable): client renders the trait + launch-abort/landing-damage outcomes
		$strippedSystem->allowedFighterClasses = is_array($this->allowedFighterClasses) ? array_values($this->allowedFighterClasses) : array(); //per-bay fighter-class allow-list (empty = unrestricted); client mirrors the dock/launch eligibility gate
		/*Bay contents and any queued launch/dock orders are own-team-only.
		  $hangarUsage names every stored craft, so it discloses the shuttle composition an
		  opponent never watched load (a MinesweepingShuttle aboard is real intel) and the exact
		  free-box count of a bay they might be planning to cripple. The pending*Order fields are
		  the sharper leak of the two: they are COMMITTED-BUT-UNRESOLVED Firing-Phase orders, so
		  an opponent who has not committed yet could read off which bay is about to launch, in
		  which direction, or which flight is being recovered - the same bleed shape the
		  deleteHiddenData rules exist to close, masked here instead because these ride the system
		  payload rather than a fire order.
		  EXTERNAL MOUNTS ARE EXEMPT FROM THE CONTENTS MASK, but NOT from the order mask: a
		  catapult's superheavy, a fighter rail's fighters, a docking collar's LCV and a
		  ShadowHangar's integrated fighters all ride OUTSIDE the hull, so whether the mount is
		  occupied is plainly apparent to an opponent (user ruling, same reasoning that keeps a
		  fighter's hardpoint ammo public in AmmoMagazine below). Only an ENCLOSED hangar bay
		  conceals what is inside it. Their queued orders still mask - an intention to launch next
		  turn is not something you can see by looking at the hull.
		  ShadowHangar has a SECOND, independent reason to stay disclosed: fleetList's
		  integratedFighterCarrierAdjust derives the held integrated-fighter count from these
		  entries to net LAUNCHED fighters off the carrier's Order-of-Battle value, and blanking
		  them would silently drop an enemy Shadow carrier's displayed value by its whole
		  complement. It leaks nothing either way - integratedFighterCount and
		  integratedFighterPerCraft already ship to every viewer, and every launched integrated
		  fighter is a visible flight row, so held = purchased - visible regardless.
		  $launchedThisTurn/$landedThisTurn stay public - they are incremented at turn resolution,
		  by which point the opponent has watched the craft cross the map.*/
		$disclosed = $this->isDisclosedToCurrentViewer();
		$externalMount = !empty($this->isCatapult) || !empty($this->isRail)
					  || !empty($this->isLCVRail) || !empty($this->isShadowHangar);
		$disclosedUsage = $disclosed || $externalMount;
		//A list, so the empty case is a JSON [] and every Array.isArray() consumer still matches.
		$strippedSystem->hangarUsage = $disclosedUsage ? $this->hangarUsage : array();
		//Tells the client the bay is UNKNOWN rather than EMPTY - without it refreshHangarTooltip
		//would render a confident "0 / N slots" off the blanked list, which is a false statement
		//rather than a withheld one.
		if (!$disclosedUsage) $strippedSystem->hangarUsageHidden = true;
		$strippedSystem->launchedThisTurn = $this->launchedThisTurn;
		$strippedSystem->landedThisTurn = $this->landedThisTurn;
		//Stage 15: only the primary hangar carries the carrier-level reload pool.
		//Send capacity (from HANG_ORD) AND spent so the client can render
		//remaining without needing to re-derive enhancement totals client-side.
		$ship = $this->getUnit();
		if ($ship && HangarOps::primaryHangar($ship) === $this) {
			$cap = HangarOps::reloadPoolCapacity($ship);
			if ($cap > 0) {
				$strippedSystem->reloadPoolCapacity = $cap;
				$strippedSystem->reloadPoolSpent    = (int)$this->reloadPoolSpent;
			}
			//Stage 17 ext: same primary-only pattern for marine pool.
			$marCap = HangarOps::marinePoolCapacity($ship);
			if ($marCap > 0) {
				$strippedSystem->marinePoolCapacity = $marCap;
				$strippedSystem->marinePoolSpent    = (int)$this->marinePoolSpent;
			}
			//Stage 18: ship the escape-roll outcome so the client can render
			//replay state on the destroyed-carrier row. Only emitted when an
			//escape roll actually fired (escapeRolled true); a live carrier's
			//primary hangar omits these fields, matching the pool-capacity
			//pattern above.
			if ($this->escapeRolled) {
				$strippedSystem->escapeRolled = true;
				$strippedSystem->escapeRoll   = (int)$this->escapeRoll;
				$strippedSystem->escapeMax    = (int)$this->escapeMax;
				$strippedSystem->escapeTotal  = (int)$this->escapeTotal;
				$strippedSystem->escapeNames  = $this->escapeNames;
			}
		}
		//Send last-submitted pending orders so the client can pre-fill the
		//launch/dock dialogs after a page reload (re-edit a queued order
		//mid-Firing-Phase, or cancel a queued dock). Own team only - see above.
		//The client hydrates both into empty arrays when absent, so withholding
		//them needs no client-side guard of its own.
		if ($disclosed) {
			if ($this->pendingLaunchOrder !== null) $strippedSystem->pendingLaunchOrder = $this->pendingLaunchOrder;
			if ($this->pendingDockOrder !== null)   $strippedSystem->pendingDockOrder   = $this->pendingDockOrder;
		}
		//LCV Rails: ship the rail→LCV link + last-submitted LCV orders so the client
		//tooltip/dialog renders the occupant and pre-fills/cancels a queued order.
		//The link and the orders split across the two gates: $lcvDocked IS this mount's
		//occupancy (a rail holds one whole LCV and carries no hangarUsage), so it follows
		//$disclosedUsage - which an LCV rail always satisfies, being an external mount -
		//while the queued orders follow $disclosed with every other pending order.
		if (!empty($this->isLCVRail)) {
			if ($disclosedUsage && is_array($this->lcvDocked)) $strippedSystem->lcvDocked = $this->lcvDocked;
			if ($disclosed) {
				if ($this->pendingLcvDockOrder   !== null) $strippedSystem->pendingLcvDockOrder   = $this->pendingLcvDockOrder;
				if ($this->pendingLcvLaunchOrder !== null) $strippedSystem->pendingLcvLaunchOrder = $this->pendingLcvLaunchOrder;
			}
		}
		return $strippedSystem;
	}

	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		$isCatapult = !empty($this->isCatapult);
		$isLCVRail  = !empty($this->isLCVRail);
		if ($isLCVRail) {
			//LCV Rail: docks/launches a single whole LCV (not fighters). Launches
			//forward at the carrier's speed; lands only a stationary, same-heading
			//LCV that ends in the carrier's hex with thrust to spare. A docked LCV
			//makes the carrier less manoeuvrable (+1 turn cost/delay, -10 initiative).
			$this->data["Special"]  = "Docking rail for a single LCV.";
			$this->data["Special"] .= "<br>Launches forward at the carrier's speed; lands a stationary, same-heading LCV.";
			$this->data["Special"] .= "<br>Each docked LCV: +1 turn cost, +1 turn delay, -10 initiative to the carrier.";
			$this->data["Special"] .= "<br>Details of Hangar Operations can be found in Fiery Void FAQ.";
		} else if ($isCatapult) {
			//Stage 16: a catapult holds ONE superheavy fighter; its extra boxes are
			//structural HP only (capacity is 1, not box count) and it launches
			//forward / lands from the rear, ignoring its own damage.
			$this->data["Special"]  = "Fixed launch rail for a single superheavy fighter.";
			$this->data["Special"] .= "<br>Launches forward only.";
			$this->data["Special"] .= "<br>Details of Hangar Operations can be found in Fiery Void FAQ.";
		} else {
			$this->data["Special"]  = "System responsible for launching and carrying docked fighter craft.";
			if (!empty($this->inadequate)) {
				//B5W Inadequate Hangars (Unreliable): bays not part of the original design.
				$this->data["Special"] .= "<br>Inadequate Hangars (Unreliable): each launch rolls 1d6 — a 1 aborts the launch this turn (retry next turn).";
				$this->data["Special"] .= "<br>Each landing fighter rolls 1d6 — a 1 scores 1d6 damage on it, ignoring armour.";
			}
			$this->data["Special"] .= "<br>Details of Hangar Operations can be found in Fiery Void FAQ.";
			$this->data["Launch Rate"] = $this->output;
		}
		$this->data["Type"] = $isLCVRail ? "LCVs" : ucwords($this->hangarType);

		//LCV rails hold exactly one LCV; their box count is HP, not capacity.
		if ($isLCVRail) {
			$dockedLcv   = HangarOps::lcvDockedOn($this);
			$totalStored = ($dockedLcv !== null) ? 1 : 0;
			$maxCapacity = 1;
		} else {
			$totalStored = HangarOps::usageCountFor($this);
			$maxCapacity = $isCatapult ? 1 : $this->maxhealth;
		}
		$this->data["Capacity"] = $totalStored . " / " . $maxCapacity . " slots";
		//"Stored Craft" line is computed client-side via Hangar.refreshHangarTooltip
		//(baseSystems.js) — it has access to pendingDockOrders/pendingLaunchOrders
		//for the live projection, which the server-side render doesn't.
	}
}


class Catapult extends Hangar{
    public $name = "catapult";
    public $displayName = "Catapult";
    public $primary = false; //changed from true on 21.11 - let's not consider it a core system after all!

	//Catapult is not impotant at all!
	public $repairPriority = 1;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired

	// === Hangar Ops Stage 16 ===
	// A Catapult is a constrained Hangar: it holds exactly ONE superheavy
	// fighter, launches only forward (direction 0), lands only from the rear,
	// services a single fighter type, applies NO launch initiative penalty, and
	// operates regardless of damage. $isCatapult is the single discriminator the
	// HangarOps call sites branch on. Its extra boxes are structural HP only and
	// must NOT contribute to the default-shuttle pool (HangarOps excludes them).
	//
	// Stage 16.1/16.2 wire up the data model (capacity tracking, no shuttle-pool
	// contribution, "1 slot" capacity display). Launch/land/landing-damage are
	// staged separately (16.3-16.5); until then Hangar::criticalPhaseEffects
	// early-returns for catapults and the client launch/dock UI excludes them
	// (every helper filters on name === 'hangar').
	public $isCatapult = true;

    function __construct($armour, $maxhealth, $output = 1){
		// Hangar ctor: ($armour, $maxhealth, $output, $direction, $hangarType, $spawnableClasses)
		// Fixed forward launch (direction 0); superheavy-only by design.
        parent::__construct($armour, $maxhealth, $output, 0, 'superheavy');
    }
}


/* Fighter Rail (B5W "Fighter Racks") — external launch rail.
 *
 * A FighterRail is a constrained Hangar whose boxes are bolted to a structure
 * block: each box holds one fighter that launches/lands INDEPENDENTLY of every
 * other fighter, with NO day-after initiative penalty on the launched fighter
 * (the carrier still takes the normal launch/land penalty that turn). The
 * trade-offs vs. a hangar bay:
 *   - $maxhealth = the rail length in BOXES (not extra HP). effectiveCapacity()
 *     returns getRemainingHealth(), so capacity shrinks as boxes are destroyed.
 *   - Rail boxes are destroyed by (a) a unique unmodified 1d20 on 16-20 that
 *     wipes one ENTIRE rail when its parent structure takes damage this turn,
 *     and (b) full structure-block destruction (the inherited structure-loss
 *     fall-off). There is NO per-structure-point box attrition.
 *   - Fighters on a destroyed rail attempt escape via the existing escape rules
 *     (the carrier-destruction d20 table); escapees DO get the -50 next-turn
 *     penalty (HangarOps::railBoxEscape, Stage 18 machinery reuse).
 *   - Docked-flight reload takes twice as long (narrow airlocks) — half cadence
 *     via the rail gate in HangarOps::serviceDockedFlights.
 *
 * $isRail is the single discriminator the HangarOps call sites branch on,
 * parallel to Catapult's $isCatapult. The launch/dock/service pipeline runs
 * through the standard Hangar path (rails respect their output budget and their
 * own damage, unlike catapults). The rail-specific mechanics live in the
 * Hangar::criticalPhaseEffects rail branch + HangarOps::onRailStructureDamage.
 */
class FighterRail extends Hangar{
    public $name           = "fighterRail";
    public $displayName    = "Fighter Rail";
    public $primary        = false;
    public $repairPriority = 1;   //like Catapult — tactically unimportant to repair

    public $isRail = true;        //single discriminator, parallel to $isCatapult

    //Detached boxes: destroyed via structure hits/crits, never targeted directly.
    //Mirrors ConnectionStrut / AdaptiveArmorController, which exclude themselves
    //from the damage allocator. (The ship's hit chart also has no rail entry.)
    public $isTargetable        = false;
    public $isPrimaryTargetable = false;
	public $iconPath = "FighterRail3.png";

    //Rail boxes are PART OF THE STRUCTURE BLOCK — their HP lives in the section's
    //Structure (e.g. the StrikeCarrier's 78-box front structure already INCLUDES
    //the rail boxes). So a rail must NOT add its own HP to the ship's combat value
    /// structural-integrity total, or the carrier would be over-valued (and over-
    //tough) by the box count. $maxhealth here is a CAPACITY number (how many
    //fighters the rail holds), consumed by effectiveCapacity; it is excluded from
    //calculateCombatValue via this flag (same mechanism ConnectionStrut uses).
    //The rail is destroyed only by the 1d20 whole-rail crit or by full structure
    //loss — never by independently soaking hits.
    protected $doCountForCombatValue = false;

    //RAIL-4: replay-deterministic 1d20 rail crit. setCriticals re-runs on every
    //replay scrub and Dice::d is non-deterministic, so the rolled value for the
    //structure-damage turn is persisted in a railCritRoll note on the OWNING
    //rail (the lowest-id rail on a given structure — see HangarOps::railCritOwner)
    //and read back here. railCritLoadedTurn/Value hold the loaded roll for the
    //current turn so onRailStructureDamage uses the stored value instead of
    //rolling fresh. (0 turn = nothing loaded → roll fresh + persist.)
    public $railCritLoadedTurn  = 0;
    public $railCritLoadedValue = 0;

    // ($armour, $maxhealth = rail length in boxes, $output = launch+land budget,
    //  $direction = 0 forward, $hangarType = the rail's combat-fighter category)
    function __construct($armour, $maxhealth, $output = null, $direction = 0, $hangarType = 'fighters'){
		switch($maxhealth){
			case 3: //retro
				$this->iconPath = "FighterRail3.png";
				break;
			case 6: //main
				$this->iconPath = "FighterRail6.png";
				break;	
			Default://Port
				$this->iconPath = "FighterRail3.png";
				break;
		}        
	
		parent::__construct($armour, $maxhealth, $output, $direction, $hangarType);
    }

    public function onIndividualNotesLoaded($gamedata){
        //Read the railCritRoll note(s) BEFORE the parent clears $individualNotes.
        //Keep the value for the current turn (the latest, by id, if duplicated)
        //so onRailStructureDamage can reuse the stored roll on a replay scrub.
        foreach ($this->individualNotes as $note){
            if ($note->notekey === 'railCritRoll' && (int)$note->turn === (int)$gamedata->turn){
                $decoded = json_decode($note->notevalue, true);
                if (is_array($decoded)){
                    $this->railCritLoadedTurn  = (int)$gamedata->turn;
                    $this->railCritLoadedValue = (int)($decoded['roll'] ?? 0);
                }
            }
        }
        parent::onIndividualNotesLoaded($gamedata);
    }

    public function setSystemDataWindow($turn){
        //Hangar::setSystemDataWindow handles the ordinary (box-count) capacity
        //line; we only override the "Special" flavour text. Skip straight to
        //ShipSystem so we don't inherit Hangar's hangar/catapult Special block.
        ShipSystem::setSystemDataWindow($turn);
        $this->data["Special"]  = "External launch rail — carries fighters that can launch without initiative penalty.";
        $this->data["Special"] .= "<br>Part of the structure block: Structure critical hits may destroy an entire rail.";
        $this->data["Special"] .= "<br>Reloading docked craft takes twice as long.";
        $this->data["Special"] .= "<br>Details of Hangar Operations can be found in Fiery Void FAQ.";
        $this->data["Type"] = ucwords($this->hangarType);
        $this->data["Launch Rate"] = $this->output;

        $totalStored = HangarOps::usageCountFor($this);
        $this->data["Capacity"] = $totalStored . " / " . $this->maxhealth . " slots";
    }
}


/* Shadow Integrated-Fighter Hangar (B5W "integrated fighters", FV Stage S).
 *
 * Certain advanced races (Shadows) don't STORE fighters in a bay — they form
 * them out of their own Structure. A ShadowHangar is an ordinary Hangar in all
 * the launch/land plumbing (it IS instanceof Hangar, so collectHangars and the
 * launch/land pipeline pick it up), but with two Stage-S differences wired up
 * here in S-a:
 *
 *   1. CRIT-IMMUNE. "Integrated fighter hangars do not suffer from critical
 *      hits." testCritical is overridden to a no-op so box damage still lands
 *      but no Hangar critical is ever rolled or applied.
 *   2. NOT a default-shuttle bay. Shadows don't carry shuttles in it; the bay's
 *      boxes hold integrated fighters (populated from the SHAD_FTRL enhCount in
 *      S-b), so HangarOps partitions a ShadowHangar out of the shuttle pool the
 *      same way it does catapults/rails — keyed on the $isShadowHangar flag.
 *
 * $isShadowHangar is the single discriminator the HangarOps call sites branch
 * on, parallel to Catapult's $isCatapult, FighterRail's $isRail, and
 * DockingCollar's $isLCVRail. The integrated-fighter mechanics proper
 * (structure<->fighter coupling, land-and-reabsorb) arrive in later S-stages;
 * S-a only adds the class, crit-immunity, and the discriminator/exclusions —
 * NO behaviour change beyond crit-immunity (a ShadowCruiser bay already
 * auto-filled 0 shuttles, so the shuttle-pool exclusion is a no-op there).
 */
class ShadowHangar extends Hangar{
    public $isShadowHangar = true;   //single discriminator, parallel to $isCatapult / $isRail / $isLCVRail
    public $isTargetable = false;
    public $isPrimaryTargetable = false;
    //Stage S (multi-bay): on a carrier with SEVERAL ShadowHangars each served by its
    //OWN Fighter Bomb (e.g. shadowRegenBaseBomb's 4 arc-keyed bays), this pairs the bay
    //to its bomb. A ShadowFighterBomb with a matching $bombHangarIndex draws/drains ONLY
    //this bay's held pool. null = unpaired (single-bay hulls like shadowCruiserBomb,
    //where the bomb falls back to the primary ShadowHangar). The structure coupling stays
    //carrier-wide regardless (one Structure binds every bay's fighters).
    public $bombGroupIndex = null;

    // ($armour, $maxhealth = bay boxes, $output = launch+land budget,
    //  $direction = launch offset, $hangarType = the ship's fighter category)
    function __construct($armour, $maxhealth, $output = null, $direction = 0, $hangarType = 'fighters'){
        //A ShadowHangar only ever forms/launches ShadowMediumFighterFlights, so bake
        //that into spawnableClasses unconditionally — it's what game.php preloads as
        //a blueprint so performLaunch can instantiate the flight (and the client can
        //resolve its type for the launch dialog). Without it the bay would only carry
        //the generic Shuttle/MinesweepingShuttle defaults and a launch resolves the
        //wrong class. The base ctor merges this with those defaults.
        parent::__construct($armour, $maxhealth, $output, $direction, $hangarType, array('ShadowMediumFighterFlight'));
    }

    //Crit-immunity: an integrated fighter hangar never suffers critical hits.
    //Return the incoming crit list untouched — no d20 roll, no addCritical.
    public function testCritical($ship, $gamedata, $crits, $add = 0){
        return $crits;
    }

    public function setSystemDataWindow($turn){
        parent::setSystemDataWindow($turn);   //inherit the standard Hangar capacity/Special block
        $this->data["Special"] .= "<br>Integrated fighter hangar: does not suffer critical hits.";
        $this->data["Special"] .= "<br>Cannot launch fighters using normal procedure, but may dock / reabsorb fighters to heal the, by passing their damage onto the ship.";
        $this->data["Special"] .= "<br>See Faction-Tiers: Shadows for more details.";				
    }
}


/* LCV Rail (B5W "LCV Rails") — docks/launches whole LCVs, NOT fighters.
 *
 * A DockingCollar is structurally a Hangar subclass (so it picks up the output
 * budget / launchedThisTurn / landedThisTurn bookkeeping and is included in the
 * generic Hangar pipeline), but its cargo is a full BaseShip — an LCV — not a
 * FighterFlight. An LCV already exists on the map with its own movement, damage,
 * ammo, and ship row; docking it is "remove the ship + remember which rail holds
 * it", launching is "put the ship back at the carrier's hex/facing/speed". None
 * of the FighterFlight stash machinery ($hangarUsage occupancy, phpclass
 * coalescing) is used — that is gated to FighterFlight everywhere and is wrong
 * for a unique full ship. All LCV-specific logic lives in HangarOps::*LCV*.
 *
 * $isLCVRail is the single discriminator the HangarOps call sites branch on,
 * parallel to Catapult's $isCatapult and FighterRail's $isRail. Unlike a
 * FighterRail, an LCV Rail is an ORDINARY targetable system with its own HP pool
 * and its own hit-chart entry (see baLCVCarrier.php) — so it is NOT excluded from
 * combat value and CAN be destroyed by direct fire. Landing on a damaged rail
 * deals the rail's sustained damage to the docked LCV (HangarOps::performLCVDock);
 * destroying an occupied rail forces the LCV to launch + 2d10 fragment damage
 * (HangarOps::onLCVRailDestroyed).
 *
 * Capacity is 1 LCV per rail regardless of box count (boxes are HP). The single-
 * occupancy limit is enforced by the dock handler (is this rail already linked to
 * a docked LCV?), NOT by box accounting.
 *
 * NOTE: declared AFTER Hangar (and after Catapult/FighterRail) on purpose. A class
 * whose parent is declared LATER in the same file forces the autoloader to fire
 * for the parent at parse time, which re-includes this whole file and fatals with
 * "Cannot declare class ... already in use". Keep all Hangar subclasses below the
 * Hangar class.
 */
class DockingCollar extends Hangar{
    public $name = "dockingCollar";
    public $displayName = "LCV Rail";
    public $iconPath = "DockingCollar.png";	

	public $repairPriority = 1;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired

    public $isLCVRail = true;   //single discriminator, parallel to $isCatapult / $isRail

    // ($armour, $maxhealth = rail HP, $output = launch+land budget; default 1 =
    //  one LCV launched OR landed per rail per turn). Fixed forward launch
    //  (direction 0); hangarType 'LCVs' matches LCV->hangarRequired.
    function __construct($armour, $maxhealth, $output = 1){
        parent::__construct($armour, $maxhealth, $output, 0, 'LCVs');
    }

    public function setSystemDataWindow($turn){
        //Hangar::setSystemDataWindow handles the ordinary (box-count) capacity
        //line; we only override the "Special" flavour text. Skip straight to
        //ShipSystem so we don't inherit Hangar's hangar/catapult Special block.
        ShipSystem::setSystemDataWindow($turn);
        $this->data["Special"]  = "External launch rail for LCVs.";
        $this->data["Special"] .= "<br>LCVs can dock and launch from this system.";		
        $this->data["Special"] .= "<br>Further details of Hangar Operations can be found in Fiery Void FAQ.";
    }

}


/* JUMP_POINTS_PLAN.md STAGES 1-2 - the Jump Engine is a Weapon that never fires.
   It is not a gun: the conversion exists because opening a hyperspace vortex is a HEX-TARGETED
   DECLARATION (plan section 3.1), and the ballistic/hextarget fire-order pipeline - client
   targeting, tac_fireorder persistence, enemy-side masking during Initial Orders - is what carries
   the target hex and the vortex facing. Nothing else in the codebase expresses that.

   STAGE 1 (landed) made it a Weapon with no behaviour change. STAGE 2 (here) turns the declaration
   on and retires the old boost-to-jump path. What a declaration currently DOES is: nothing. It
   persists a hex and a facing and is skipped by Firing::fireWeapons. Stage 3 spawns the vortex unit
   from it, Stage 4 lets units fly into it and Stage 5 gives it a lifecycle.

   The four knock-on effects the conversion has are handled, not discovered - see plan section 3.1:
   combat value (ShipClasses::calculateCombatValue keeps this system in the CORE bucket, NOT the
   weapon bucket), the weapon list (the engine now shows as an icon in the bottom bar on every
   capital ship - kept deliberately, it is the click target for the declaration), the critical chart
   (below), and the static blueprints (regenerated). */
class JumpEngine extends Weapon{
    public $name = "jumpEngine";
    public $displayName = "Jump Engine";
    public $delay = 0;
    public $primary = true;
	//⚠️ lower-case "j": the file Stage 6 added is img/systemicons/jumpEngine1.png. This said
	//"JumpEngine1.png" from Stage 6 until 2026-08-22 - which works on a Windows dev box and 404s on
	//the live Linux server, the exact trap checkShipData's sysimage check exists to catch.
	public $iconPath = "jumpEngine1.png";
    
    /* STAGE 2 - THE BOOST-TO-JUMP PATH IS RETIRED, AND THIS FLAG IS THE WHOLE OF IT.
       $boostable = false stops the client offering a boost and stops shipManager.power.canBoost
       from allowing one, so no NEW boost-jump can be ordered. Everything else about boosting is
       deliberately LEFT ALONE - isOverloading(), doHyperspaceJump(), the end-of-Fire sweep in
       Firing::fireWeapons, the isJumpEngine branch in SystemPowerSettings.js and the boost-driven
       jumping[] commit checklist in gamedata.js - because a boost already committed on the live
       server must still resolve after this deploys: submitPower validates nothing against this
       flag and ShipSystem::setPower loads a type-2 row back unconditionally.
       Do NOT tidy the boost code away in the same deploy that flips this flag; that is the
       cleanup deploy, one cycle later (plan section 4 Stage 2 and section 5 trap 10). */
    public $boostable = false;
    public $maxBoostLevel = 1;
    public $boostEfficiency = 0;

    /* STAGE 2 - THE VORTEX DECLARATION. The Jump Engine is a hex-targeted ballistic weapon:
       declared in Initial Orders ($ballistic), aimed at a HEX rather than a unit ($hextarget), and
       able to project a vortex up to $range hexes away. It still never rolls to hit and never deals
       damage - Firing::fireWeapons skips every order of type 'ballistic', and this class has no
       beforeFiringOrderResolution hook - so the declaration is completely inert until the Stage 3
       spawn sweep consumes it. All it does today is persist the target hex and the facing. */
    public $ballistic = true;
    public $hextarget = true;

    /* HOW FAR THIS ENGINE CAN PROJECT ITS VORTEX, in hexes. 4 is the B5W standard and stays the
       default; the constructor's 5th argument is how a faction says otherwise (Vorlon Empire
       hulls pass 12 - user ruling 2026-08-25). Read it, never assume 4: every reader already
       does, and there is deliberately no second copy of the number anywhere.

         - declaration legality, server side .. Firing::getVortexDeclarationBlock
         - declaration legality, client side .. weaponManager.targetHex ($range rides the static
                                                blueprint - ShipCompactor does not strip it)
         - the "holder drifted too far" closure rule .. getVortexCloseReason
         - the tooltip .. setSystemDataWindow, which interpolates it twice

       ⚠️ markLegacy() zeroes it and markGate() overwrites it with a SIGNAL range of 10; both run
       after the constructor, so a 5th argument passed alongside either is silently discarded.
       That is correct in both cases - neither kind of engine projects a vortex at all. */
    public $range = 4;

    /* STAGE 5 - the MAINTAIN declaration's firing mode. Named once because three places have to
       agree on where the six facings stop and Maintain begins: getVortexDeclaration (openings
       only), getMaintainDeclaration (this mode only) and Firing::getVortexDeclarationBlock, which
       judges the two by different rule lists. */
    const MAINTAIN_MODE = 7;

    /* STAGE 5 - HOW MANY TURNS A VORTEX CAN STAY OPEN (plan section 2.3, user ruling 2026-08-22).
       ⚠️ The turn it is DECLARED does not count. Declared on N, it forms at the end of N, is
       open on N+1 through N+4, and closes unconditionally at the end of N+4 - so the last turn on
       which declaring Maintain can change anything is N+3. Named once because three things count
       in it: the closure sweep, the client's decision to offer the toggle, and the N/4 counter on
       the engine's icon. */
    const MAX_VORTEX_TURNS = 4;

    /* MODES 1-6 ARE THE VORTEX FACING (mode = facing + 1); mode 7 is the Stage 5 Maintain
       declaration. firingMode is the STORAGE for the facing - it persists to
       tac_fireorder.firingmode, so no schema change and no new column - but it is NOT the input
       method: the mode selector is suppressed below and Stage 2b sets the facing with an on-map
       arrow that writes the mode under the hood. The modes are functionally identical (no per-mode
       arrays), so every changeFiringMode() call in the codebase is a no-op on this system. */
    public $firingModes = array(
        1 => "Vortex 0°",   2 => "Vortex 60°",  3 => "Vortex 120°",
        4 => "Vortex 180°", 5 => "Vortex 240°", 6 => "Vortex 300°",
        7 => "Maintain Vortex",
    );
    /* Declared on THIS class rather than on Weapon on purpose: a public property on the base class
       would write "hideFiringModeSelector":false into every weapon of every static blueprint, and
       plan section 8 measured what default values already cost that tree. Absent reads as
       undefined - falsy - on every other weapon, which is exactly the wanted answer. */
    public $hideFiringModeSelector = true;

    /* A declaration, not a shot: there is nothing in flight to shoot down, and no projectile for
       the front end to fly from the ship to the hex. */
    public $doNotIntercept = true;
    public $uninterceptable = true;
    public $noProjectile = true;

    /* Doubles as the discriminator. weaponManager.targetHex stamps the fire order's damageclass
       from data["Weapon type"].toLowerCase(), so every vortex declaration carries damageclass
       'jumppoint' - which is how the Stage 3 spawn sweep will find them, and how the ballistic
       icon knows to draw a jump point rather than an anonymous red hex. */
    public $weaponClass = "JumpPoint";

    /* STAGE 6 - THE RECHARGE IS REAL, AND $loadingtime / $turnsloaded ARE WHERE IT LIVES.
       Both are overwritten in the constructor from $delay (the B5W jump delay, 8-36 across the
       fleet) - these declarations only give the properties a value for anything that reads the
       class before an instance exists. A jump engine STARTS a scenario fully loaded, spends its
       whole charge opening a jump point, and recharges from the turn AFTER that jump point
       closes: see getVortexRechargeLoad, which is what actually answers the question. */
    public $loadingtime = 1;
    public $turnsloaded = 1;

    /* Weapon's chart (ReducedRange 14 / ReducedDamage 19) describes a gun, and neither penalty
       means anything on a system with no range and no damage. Keep ShipSystem's empty chart -
       that is what this system rolled on before the conversion. */
    protected $possibleCriticals = array();

    /* STAGE 3 - the vortex unit this engine can put on the board mid-game. game.php reads
       $spawnableClasses off every weapon of every blueprint in the game and preloads the listed
       classes into window.staticShips, which is what lets a vortex that appears on a POLL (no page
       reload) resolve to a blueprint and render. Same mechanism BallisticMineLauncher uses for its
       loitering mines; without it the first vortex of a session draws as nothing until F5.
       ⚠️ SpawnJumpPointExit is on the list for exactly the same reason and NOT because this
       engine can open one today - an exit is spawned by the Stage 6 sweep, not by openVortex -
       but a preloaded blueprint is what stops the first one to form rendering as an empty hex
       (REINFORCEMENTS_PLAN.md §4 Stage 3). BlueprintCache::build reads this list verbatim.
       ⚠️ AND SpawnJumpPointPhaseIn IS ON IT TOO (Stage 9) EVEN THOUGH IT IS NEVER DRAWN. The
       client still has to RESOLVE it - model/ship.js merges the live payload against the blueprint
       by faction + phpclass, and a unit with no blueprint is a unit with no phpclass, which is
       exactly what shipManager.movement.isJumpVortexExit tests. Leave it off and the phase-in
       doorway stops being recognised as a doorway at all: no arrival hex, and the wave has nowhere
       to stand. "Invisible" is a rendering rule, not an excuse to skip the blueprint. */
    public $spawnableClasses = array('SpawnJumpPoint', 'SpawnJumpPointExit', 'SpawnJumpPointPhaseIn');

    /* STAGE 3 - LIVE VORTEX STATE, rebuilt on every load by onIndividualNotesLoaded from the one
       IndividualNote this engine writes when it opens a vortex (see openVortex).

       SERVER-SIDE ONLY, on purpose: none of the three is in stripForJson's allow-list, so nothing
       here reaches the client. That sidesteps the shared-reference trap - client system objects
       share fields across same-phpclass instances, so a naively mirrored activeVortexId would have
       every Jump Engine in the game showing the same vortex.

       $vortexCloseTurn is -1 while the vortex is open. Stage 5 is what ever sets a real turn. */
    public $activeVortexId = null;
    public $vortexOpenTurn = null;
    public $vortexCloseTurn = -1;

    /* STAGE 5. PROTECTED, not public, on purpose: json_encode serialises public properties only,
       so a protected field costs the static blueprints nothing (plan section 8) - and nothing
       outside this class hierarchy needs either of them.
         $vortexCloseReason  - why the vortex closed, for the log and the closure note's 3rd field.
         $vortexFailureApplied - set by rollVortexJumpFailure when it has just destroyed the ship,
           read by PhasingDrive::criticalPhaseEffects so a Shadow hull cannot be killed twice in
           one Critical phase (parent rolls first, the half-phase self-destruct runs after it). */
    protected $vortexCloseReason = '';
    protected $vortexFailureApplied = false;

    /* ⭐ JUMP GATES (PHASE 2) - THE PROGRAMMED HOLD, rebuilt from the 'VortexHold' note the same
       pass restores the 'Vortex' one (JUMP_GATES_PLAN.md section 3.4).

       A gate's jump point is open for a number of turns PROGRAMMED WHEN IT WAS SIGNALLED, 1 to 4,
       and there is no Maintain declaration to re-state it turn by turn - so the duration has to
       survive a load, and the existing note cannot carry it (its notevalue's third field is free
       text containing commas; see writeVortexHoldNote, and plan trap 8).

       ⭐ NULL IS THE WHOLE OF WHAT MAKES PHASE 2 INVISIBLE TO PHASE 1: a vortex with no hold note
       is a SHIP-opened vortex, and every gate branch keyed off these two reads exactly as it did
       before Phase 2 existed.

       $vortexClaimantId is the player who WON the claim. It is recorded because the roll-off must
       never be re-derived on load (plan section 2.4) - not because it grants anybody anything: an
       open gate vortex may be used by any unit of any side (plan section 2.6).

       Protected for the same reason as the two above - json_encode takes public properties only, so
       neither reaches a payload or a static blueprint. */
    protected $vortexHoldTurns = null;
    protected $vortexClaimantId = null;

    /* ⭐ REINFORCEMENTS STAGE 6 - HOW FAR THIS ENGINE'S EXIT SCATTERED, rebuilt from its
       'VortexScatter' note (see writeVortexScatterNote and getVortexScatter).

       NULL ON EVERY ENTRANCE VORTEX AND EVERY GATE'S - only an exit rolls on the deviation table -
       and null is what getVortexScatter() answers with, so a reader can never mistake "did not
       scatter" for "scattered zero hexes", which is a real and rather good outcome.

       Protected for the same reason the four fields above are: json_encode serialises PUBLIC
       properties only, so neither of these reaches a payload or a static blueprint. */
    protected $vortexScatterHexes = null;
    protected $vortexScatterFacingSteps = null;

    /* ⭐ VORTEX DISRUPTOR - THIS ENGINE'S JUMP POINT HAS BEEN SHOT INTO AND IS COLLAPSING.
       Set by VortexDisruptor::fire (model/weapons/specialWeapons.php) the instant its shot hits,
       read by getVortexClosureReason, which answers for it BEFORE every other branch on the list -
       so a collapsing jump point closes at the end of this turn however long it was otherwise going
       to last: a Maintain declaration does not save it, and neither does a fixed gate's programmed
       hold.

       IN MEMORY ONLY, AND THAT IS SUFFICIENT rather than an oversight. Both halves run inside ONE
       FireGamePhase::advance, off ONE gamedata load, in this order: Firing::fireWeapons -> the
       disruptor's fire() -> this flag, then JumpEngine::closeExpiredVortices a few lines later.
       What PERSISTS is the closure that comes out of it - the ordinary phase-2 'Vortex' note
       recordVortexClosure writes - so a second advance() over the same turn re-reads a vortex that
       is already closed and never consults this flag at all. (Firing::fire's
       `if ($fire->rolled > 0) return;` is what makes the shot itself idempotent, so it is not
       re-rolled either.) Protected, so it costs no payload and no static blueprint. */
    protected $vortexDisrupted = false;

    /* SECTION 9 - THE LEGACY ONE-CLICK JUMP, AND WHY IT IS A FLAG RATHER THAN A SUBCLASS.
     *
     * Not every setting in the fleet has a B5 jump vortex. A Trek Nacelle, a BSG FTL Drive and a
     * Star Wars Hyperdrive all take their ship away by themselves at the end of the turn and leave
     * nothing on the board, and 195 of the 776 jump engines in the tree are one of those three
     * (user ruling 2026-08-22). markLegacy() is how a blueprint says so.
     *
     * ⭐ PROTECTED, so it is not serialised at all - json_encode takes public properties only, and
     * the static generator encodes the constructed ship (plan section 8: a public default costs
     * 776 blueprint entries for nothing). The client does not need it: every switch it would drive
     * is already carried by a property markLegacy() sets - $boostable brings the Jump-to-Hyperspace
     * row back in SystemPowerSettings, $hextarget / $autoFireOnly keep the engine out of
     * weaponManager.targetHex, and JumpEngine.getVortexIconLoad returns null with no vortex.
     *
     * ⭐ A FLAG AND NOT A `LegacyJumpEngine extends JumpEngine`, which plan section 9 originally
     * sketched. Both work; this one is cheaper and safer in four specific ways:
     *   - no new class means no autoload regeneration, which on live needs the maintenance gate;
     *   - `phpclass` in every reverted blueprint stays "JumpEngine", so a game in progress across
     *     the deploy sees no class identity change at all;
     *   - every `instanceof JumpEngine` site in the codebase keeps its current answer without an
     *     audit, and no `get_class() ===` comparison anywhere can silently stop matching;
     *   - it is per-INSTANCE, so one hull in a faction can be reverted without a class of its own.
     * The cost is that there is no `instanceof LegacyJumpEngine` to test - ask isLegacyJump().
     *
     * ⚠️ WHAT IT DOES NOT TOUCH: the vortex LIFECYCLE. restoreVortexState, closeExpiredVortices,
     * recordVortexClosure and the jump-failure roll all still run on a legacy engine, so a vortex
     * that was opened BEFORE a hull was reverted still closes and still logs properly. Only the
     * DECLARATION path is shut off, which is the half that can create new state. */
    protected $legacyJump = false;

    /* ⭐ DOES THIS ENGINE'S 4th CONSTRUCTOR ARGUMENT MEAN A JUMP DELAY? (user report 2026-08-29.)
     *
     * True on all 776 of them but the Star Trek Nacelle, which passes an IMPULSE RATING there
     * instead (TrekWarpDrive feeds TrekImpulseDrive's sublight thrust off it). Only markLegacy(false)
     * clears it, and only customTrek.php calls that.
     *
     * ⭐ IT IS NOT "is this a legacy drive". It used to be, by accident: markLegacy() zeroed the
     * recharge for every legacy drive on the reasoning that a legacy drive never opens a jump point
     * and so never spends a charge. Stage 9 made that false - a Shadow, Star Wars or BSG hull now
     * PHASES IN through a doorway of its own and spends the charge doing it - so the Trek-specific
     * reason had to be separated from the legacy flag it was hiding behind, or every one of those
     * hulls would go on drawing a flat 1/1 for a drive that really is recharging.
     *
     * ⚠️ PROTECTED, for the same reason $legacyJump is: json_encode takes public properties only
     * and the static generator encodes the CONSTRUCTED ship, so a public default would cost 776
     * blueprint entries for a flag the client never asks about (it reads the loading pair
     * stripForJson sends). */
    protected $hasJumpRecharge = true;

	//JumpEngine tactically  is not important at all!
	public $repairPriority = 6;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired

	private $preJumpValue = 0; //Will be used to store ship's Combat Value at the moment it jumped.
    
    function __construct($armour, $maxhealth, $powerReq, $delay, $range = 4){
        /* The first four arguments are the signature every one of the 610 ship files already
           passes, so a 5th with a default changes none of them. 0/360 arcs are passed internally:
           the engine has no firing arc, and Stage 2 projects its vortex onto a hex in any
           direction. Weapon's 6th argument ($output) still defaults to 0, which is what ShipSystem
           was handed before. */
        parent::__construct($armour, $maxhealth, $powerReq, 0, 360);

        /* STAGE 6 - THE 4th ARGUMENT IS THE RECHARGE TIME, AND IT IS THE LOADING TIME.

           It is the B5W JUMP DELAY (0 to 65 across the fleet, 16 on a Primus): how long the drive
           needs to charge before it can open a jump point. Stage 5 parked it in $delay and drove
           the icon off $turnsloaded instead; that made $turnsloaded a vortex counter rather than a
           loading state, which is the wrong meaning for the one property every other weapon in
           the game uses for exactly one thing (user ruling 2026-08-22). It now says what it means:

             - The engine STARTS the scenario fully loaded ($turnsloaded == $loadingtime), because
               a fleet arrives with charged drives. A Primus reads 16/16 on turn 1.
             - Opening a jump point spends the charge: 0/16 for the rest of that turn.
             - While the vortex stands the icon shows the VORTEX counter instead (N/4, see
               getVortexAge) and the charge does not move.
             - From the turn AFTER the vortex closes it climbs one per turn, back to 16/16.

           getVortexRechargeLoad derives all four states from the vortex note, so none of it
           depends on a stored tac_systemdata loading row (see stripForJson).

           $delay keeps the raw value because that is the name the rulebook uses, and one ship file
           in the fleet passes 0 - the max(1, ...) below is what stops that meaning "never loaded".
           ⚠️ Nothing else in the codebase reads $delay; as a Weapon subclass it would be taken
           for an INITIATIVE delay if anything ever did, so do not start. */
        $this->delay       = (int)$delay;
        $this->loadingtime = max(1, (int)$delay);
        $this->turnsloaded = $this->loadingtime;

        /* THE 5th ARGUMENT IS THE PROJECTION RANGE, and it defaults to the B5W standard 4 - see
           $range above for the four things that read it. Faction rule, not a hull rule: it is
           passed per ship file because that is where a faction's hulls are, but every hull of a
           faction is expected to carry the same number (Vorlon Empire: 12, user ruling
           2026-08-25).

           ⚠️ FLOORED AT 1, and that floor is not decoration. The two ends disagree about what a
           range of 0 means - weaponManager.targetHex reads it as UNLIMITED (`weapon.range === 0
           ||`), Firing::getVortexDeclarationBlock reads it as "no hex is legal" - so 0 is a state
           no ship file should be able to reach by accident. markLegacy() sets it deliberately and
           is safe there precisely because it also turns the whole declaration path off. */
        $this->range = max(1, (int)$range);
    }

    /* SECTION 9 - PUT THIS ENGINE BACK ON THE PRE-2026 ONE-CLICK JUMP.
     *
     * Called from a ship file (or from a subclass constructor) immediately after the engine is
     * built, and BEFORE anything reads it - setSystemDataWindow and the static generator both run
     * long afterwards, so there is no ordering hazard in practice:
     *
     *     $hyperdrive = new JumpEngine(4, 12, 6, 20);
     *     $hyperdrive->displayName = 'Hyperdrive';
     *     $hyperdrive->markLegacy();
     *     $this->addPrimarySystem($hyperdrive);
     *
     * Returns $this so the one-liner form works too: addPrimarySystem((new JumpEngine(...))->markLegacy()).
     *
     * It flips exactly two things, in opposite directions:
     *
     * 1. THE BOOST PATH, BACK ON. $boostable is the whole of it, in the same way $boostable = false
     *    was the whole of retiring it at Stage 2 (see the flag's own comment above): submitPower
     *    validates nothing against it, ShipSystem::setPower loads a type-2 row back regardless, and
     *    every client boost helper ignores it. The only thing it drives is `showBoost` in
     *    SystemPowerSettings.js, a truthiness test - so setting it true restores the
     *    "Jump to Hyperspace: Yes/No" row and nothing else. The rest of the boost machinery
     *    (isOverloading, doHyperspaceJump, the end-of-Fire sweep in Firing::fireWeapons, the
     *    isJumpEngine branch in SystemPowerSettings, the jumping[] commit checklist in gamedata.js)
     *    was deliberately never deleted and does not need re-writing.
     *
     *    ⚠️ THIS MAKES THAT MACHINERY PERMANENT. Plan section 4 Stage 2 and section 5 trap 10 both
     *    promise a "cleanup deploy, one cycle later" that deletes all five. That promise is now
     *    void: 195 hulls depend on the boost path. The two plans were always mutually exclusive.
     *
     * 2. THE VORTEX DECLARATION PATH, OFF. $autoFireOnly keeps the engine out of
     *    weaponManager.selectWeapon and targetHex; $hextarget / $ballistic / $range 0 mean it
     *    cannot produce a hex order even if one were forged; and getVortexDeclaration /
     *    getMaintainDeclaration / hasVortexDeclaration each refuse a legacy engine outright, which
     *    is what makes the CONCEALMENT rule come out right (see hasVortexDeclaration).
     *
     * ⭐ $name STAYS "jumpEngine". SystemFactory.createSystemFromJson picks the client class from
     * systemJson.name - `new window[name](args, ship)` - not from phpclass, so the existing client
     * JumpEngine is reused with no new JS at all, and its initializationUpdate (the "JUMP" output
     * display while boosted) and hasMaxBoost are exactly what the boost path needs. The name is
     * also how movement.js canHalfPhase finds the drive, and how three client `instanceof Weapon`
     * guards exclude it (plan section 3.1). Nothing here touches it. */
    public function markLegacy($keepsRecharge = true)
    {
        $this->legacyJump = true;

        //1. the boost path, back on
        $this->boostable       = true;
        $this->maxBoostLevel   = 1;
        $this->boostEfficiency = 0;

        //2. the declaration path, off
        $this->autoFireOnly = true;
        $this->ballistic    = false;
        $this->hextarget    = false;
        $this->range        = 0;
        $this->firingModes  = array(1 => "Standard");

        /* ⚠️ THE PER-MODE ARRAYS ARE ALREADY BUILT BY THE TIME THIS RUNS. Weapon::__construct walks
           $this->firingModes and fills four of them, so an engine reaching markLegacy() carries
           seven entries in each - one per vortex facing plus Maintain - and shrinking $firingModes
           alone would leave them behind: six dead entries per array in every reverted blueprint,
           and a mode list that disagrees with the arrays keyed off it. Nothing reads modes 2-7 on a
           system that cannot change mode, so this is tidiness rather than a bug fix, but a stale
           array that only LOOKS live is exactly what the next reader would trust.
           changeFiringMode(1) below re-reads the pruned arrays so the live values still match. */
        foreach (array('minDamageArray', 'maxDamageArray', 'priorityAFArray',
                       'animationExplosionScaleArray') as $modeArray){
            if (!is_array($this->$modeArray) || empty($this->$modeArray)) continue;
            $this->$modeArray = isset($this->{$modeArray}[1]) ? array(1 => $this->{$modeArray}[1]) : array();
        }
        $this->changeFiringMode(1);

        /* ⭐⭐ THE RECHARGE IS REAL AGAIN ON EVERY LEGACY DRIVE BUT THE TREK NACELLE (user report
           2026-08-29). Stage 9 gave legacy drives a way to PHASE IN (Firing::getVortexDeclarationBlock
           takes the arrival branch above the legacy refusal), so a Phasing Drive, a Hyperdrive and a
           BSG FTL Drive all spend and recover a charge exactly as a B5 Jump Engine does - and every
           one of them was still drawing the flat 1/1 this used to force, which reads as a system
           with no state at all.

           So the reset is now OPT-IN, and the ONE caller that opts in is TrekWarpDrive
           (out of scope by the same user ruling, and for a concrete reason): its 4th constructor
           argument is an IMPULSE RATING - it feeds TrekImpulseDrive's sublight thrust - not a jump
           delay, so a Nacelle rated 6 would claim a 6-turn jump recharge it does not have. $delay
           keeps the raw value either way; $hasJumpRecharge is what stripForJson reads to decide
           whether the number means anything.

           ⚠️ THE DEFAULT IS true, so every existing one-argument markLegacy() call in the 88 ship
           files keeps its OWN $delay - which is what those files always passed and what the
           rulebook calls the jump delay. Only customTrek.php passes false. */
        if (!$keepsRecharge){
            $this->hasJumpRecharge = false;
            $this->loadingtime     = 1;
            $this->turnsloaded     = 1;
        }

        return $this;
    }

    /* Is this engine on the old one-click jump rather than the vortex? See markLegacy(). */
    public function isLegacyJump()
    {
        return $this->legacyJump;
    }

    /* ================= JUMP GATES (PHASE 2) - THE FIXED-GATE ENGINE ===============
     *
     * JUMP_GATES_PLAN.md section 3.2. A FLAG, not a subclass, for exactly the four reasons
     * markLegacy() gives above and with equal force: no new class means no autoload regeneration
     * (which on live needs the maintenance gate), `phpclass` stays "JumpEngine" so a game in
     * progress across the deploy sees no identity change, every `instanceof JumpEngine` site keeps
     * its answer without an audit, and it is per-INSTANCE.
     *
     * ⚠️ PROTECTED, so it is not serialised - json_encode takes public properties only, and the
     * static generator encodes the constructed ship. The client does not need it: it tells a gate
     * engine from a ship engine by the SHIP it is mounted on (gamedata.isJumpGate), never by the
     * system. Ask isGateJump() server-side.
     *
     * ⚠️ THE GATE UNIT IS JumpgateCapital, AND IT IS THE ONLY ONE. JumpgateNew (terrain) and the
     * civilian Jumpgate also mount a JumpEngine and will turn up in any grep; both are obsolete and
     * explicitly out of scope (user ruling 2026-08-23, plan trap 12). Neither gets markGate(), and
     * anything that keys off gate behaviour must key off isGateJump() rather than off a hull name.
     */
    protected $gateJump = false;

    /* PUT THIS ENGINE ON THE FIXED-GATE RULES. Called from a ship file immediately after the engine
     * is built, in the one-liner form markLegacy() established:
     *
     *     $this->addPrimarySystem((new JumpEngine(8, 10, 20, 20))->markGate());
     *
     * What it changes, and what it deliberately does not:
     *
     * 1. $range BECOMES A SIGNAL RANGE, NOT A PROJECTION RANGE. A gate opens its vortex on its OWN
     *    hex and never projects one, so the 4 hexes a ship engine may throw a vortex is the wrong
     *    number and the wrong question. 10 is how far away the claiming player's nearest live,
     *    deployed, non-terrain unit may be (plan section 2.1). ⚠️ NO LINE OF SIGHT is required for
     *    that reach - signalling is a transmission, not an aimed effect (user ruling 2026-08-23).
     *
     * 2. THE FIRING MODES BECOME THE PROGRAMMED OPEN DURATION, 1-4 TURNS. A gate's facing is fixed
     *    when the gate is placed and can never be chosen (plan section 2.2), so the six facing modes
     *    have nothing left to say; the one number a player DOES choose is how long to hold the door
     *    open, and firingMode is where it persists (tac_fireorder.firingmode - no schema change).
     *    Mode 7 (MAINTAIN) is gone with them: a gate has no Maintain, the duration is programmed
     *    once and cannot be changed afterwards.
     *
     * 3. $loadingtime / $turnsloaded ARE LEFT ALONE, on purpose. The constructor already set both
     *    from the 4th argument - 20 on JumpgateCapital - and Stage 6 of Phase 1 gave that argument
     *    the meaning "turns to charge", which is precisely the 20-turn gate recharge plan section
     *    2.5 asks for. getVortexRechargeLoad derives the whole state off the vortex note, so the
     *    recharge rule needs no new code at all.
     *
     * 4. $hideFiringModeSelector STAYS TRUE. The duration is picked in the signal panel that opens
     *    off the gate's tooltip, not by cycling a letter in the gate's ship window.
     *
     * ⭐ $name STAYS "jumpEngine", for the same reason markLegacy() keeps it:
     * SystemFactory.createSystemFromJson picks the client class from systemJson.name, so the
     * existing client JumpEngine is reused with no new JS.
     *
     * Returns $this so the one-liner form works. */
    public function markGate()
    {
        $this->gateJump = true;

        $this->range = 10;                  //SIGNAL range (section 2.1), never a projection range

        $this->firingModes = array(
            1 => "Open 1 turn",
            2 => "Open 2 turns",
            3 => "Open 3 turns",
            4 => "Open 4 turns",
        );

        /* ⚠️ THE PER-MODE ARRAYS ARE ALREADY BUILT BY THE TIME THIS RUNS - identical hazard to the
           one markLegacy() documents. Weapon::__construct has already walked the SEVEN vortex modes
           and filled four arrays from them, so shrinking $firingModes alone would leave three dead
           entries in each. Keep keys 1-4 and drop the rest, then changeFiringMode(1) so the live
           values are re-read off the pruned arrays. */
        foreach (array('minDamageArray', 'maxDamageArray', 'priorityAFArray',
                       'animationExplosionScaleArray') as $modeArray){
            if (!is_array($this->$modeArray) || empty($this->$modeArray)) continue;
            $kept = array();
            for ($mode = 1; $mode <= 4; $mode++){
                if (isset($this->{$modeArray}[$mode])) $kept[$mode] = $this->{$modeArray}[$mode];
            }
            $this->$modeArray = $kept;
        }
        $this->changeFiringMode(1);

        return $this;
    }

    /* Is this engine a FIXED GATE's rather than a ship's? See markGate(). */
    public function isGateJump()
    {
        return $this->gateJump;
    }

    /* ================= THE GATE DAMAGE MODEL (JUMP_GATES_PLAN.md section 2.5) =====
     *
     * ⭐ THE REACTOR IS THE WHOLE OF IT. A fixed gate has no criticals - JumpgateCapital calls
     * clearPossibleCriticals() on its Reactor and this engine's own chart has been empty since
     * Phase 1 - so the gate's condition is expressed as ONE NUMBER, D, the points of damage on its
     * Reactor, and three rules read off it:
     *
     *     recharge time      20 + floor(D / 3) turns   (Stage 4 - getLoadingTime)
     *     maximum hold       max(1, 4 - floor(D / 15)) turns
     *     total reactor loss (D >= maxhealth)          the gate is destroyed (Stage 4)
     *
     * Same measure rollVortexJumpFailure uses on the engine: maxhealth - getRemainingHealth(),
     * which is the DAMAGE TAKEN, clamped at maxhealth by getRemainingHealth's own floor of 0.
     *
     * ⚠️ STATIC AND SHIP-KEYED, not an instance method on the engine, because the number lives on
     * a DIFFERENT system. Both the submit path (Firing) and the resolution sweep have the gate in
     * hand and need the same answer, and threading it through the engine would only invite an
     * engine-damage reading somewhere. A ship with no Reactor answers 0 rather than throwing -
     * every hull has one, but a POST-side object may not have been fully rebuilt. */
    public static function getGateReactorDamage($gate)
    {
        if (!$gate) return 0;
        $reactor = $gate->getSystemByName("Reactor");
        if (!$reactor) return 0;

        return max(0, $reactor->maxhealth - $reactor->getRemainingHealth());
    }

    /* THE LONGEST OPEN DURATION THIS GATE CAN BE PROGRAMMED FOR, 1 to MAX_VORTEX_TURNS.
     *
     * A wounded gate cannot hold the door open as long: every 15 points on the Reactor costs one
     * turn, and it never drops below 1 - a gate that can still signal at all can always manage a
     * single turn. The client's signal panel caps its stepper at this, and the submit path CLAMPS
     * to it rather than rejecting (see Firing::getGateSignalBlock for why). */
    const GATE_HOLD_PER_DAMAGE = 15;

    public static function getGateMaxHold($gate)
    {
        $damage = self::getGateReactorDamage($gate);

        return max(1, self::MAX_VORTEX_TURNS - (int)floor($damage / self::GATE_HOLD_PER_DAMAGE));
    }

    /* ⭐ HOW MANY TURNS THIS ENGINE TAKES TO CHARGE A VORTEX - the one authority, for a ship and a
     * gate alike, and the number every vortex charge test compares against.
     *
     * ⚠️ IT IS DERIVED FROM $delay, NOT FROM $loadingtime, AND THAT IS LOAD-BEARING. Phase 1
     * Stage 6 gave the 4th constructor argument the meaning "turns to charge" and set $loadingtime
     * from it - but Weapon::setLoading OVERWRITES $loadingtime from the stored tac_systemdata row
     * on every load, and in a game recorded before Stage 6 that row still holds the pre-Stage-6
     * value of 1. $delay is set by the constructor and nothing writes it afterwards, so it is the
     * only field that says what the ship file actually asked for. (This is exactly why Stage 6's
     * stripForJson sent max(1, (int)$this->delay) rather than $this->loadingtime; reading the
     * stored value here instead silently gave every jump engine in the replay corpus a 1-turn
     * recharge, which the harness caught.)
     *
     * ⚠️ NOT AN OVERRIDE OF getLoadingTime(). That is asked by the whole generic weapon-loading
     * machinery, on every weapon, in every phase; this question is "how long does a VORTEX take to
     * charge", which is only ever asked at the four vortex sites. Keeping them apart is what stops
     * a gate rule leaking into weapon loading.
     *
     * ⭐ A SHIP ENGINE'S ANSWER IS EXACTLY THE EXPRESSION IT ALWAYS WAS. The gate term is the third
     * rule of the gate damage model (plan section 2.5): 20 + floor(D / 3) turns, where D is points
     * of damage on the gate's Reactor. getUnit() is set in BaseShip::addSystem, so it is available
     * from construction onwards - a gate that somehow has none falls back to the undamaged
     * recharge rather than throwing. */
    const GATE_RECHARGE_PER_DAMAGE = 3;

    public function getVortexRechargeTime()
    {
        $recharge = max(1, (int)$this->delay);

        if (!$this->gateJump) return $recharge;

        $gate = $this->getUnit();
        if (!$gate) return $recharge;

        return $recharge + (int)floor(self::getGateReactorDamage($gate) / self::GATE_RECHARGE_PER_DAMAGE);
    }

    /* ⭐ TOTAL REACTOR LOSS DESTROYS THE GATE (plan section 2.5) - the last rule of the damage
     * model, and the only one that is an EVENT rather than a number read off the Reactor.
     *
     * A gate is a BaseShip, so nothing else would ever destroy it: its four side Structures and its
     * primary can be shot away one by one, but "the reactor is gone, therefore the installation is
     * gone" is a gate rule with no general equivalent. Expressed the way every other
     * destroy-this-ship path in the game is - a damage entry that takes the primary Structure's
     * remaining health - so the fleet list, the combat log, the replay and the vortex closure all
     * see an ordinary destruction and need no gate branch of their own.
     *
     * ⚠️ TIMING. This runs in Pass 2 of Criticals::setCriticals inside FireGamePhase::advance, so
     * it sees the damage the Reactor took THIS turn - and closeExpiredVortices runs after
     * setCriticals, which is what makes the gate's own jump point close on the same turn (its gate
     * closure branch reads isDestroyed). Idempotent: once the primary Structure is destroyed the
     * guard below refuses a second entry.
     *
     * damageclass 'JumpFailure' is reused rather than invented: Firing::isHyperspaceLogOrder skips
     * it in the fire-order gathers, weaponManager.doShortLogText prints the sentence alone, and
     * HangarOps treats it as "everything aboard dies with it" - all three of which are what should
     * happen here, and none of which a new damage class would get for free. */
    protected function destroyGateOnReactorLoss($gate, $gamedata)
    {
        $turn = (int)$gamedata->turn;

        $reactor = $gate->getSystemByName("Reactor");
        if (!$reactor) return;
        if ($reactor->getRemainingHealth() > 0) return;      //still has boxes - nothing to do

        $primaryStruct = $gate->getStructureSystem(0);
        if (!$primaryStruct || $primaryStruct->isDestroyed($turn)) return;   //already gone

        $rammingSystem = $gate->getSystemByName("RammingAttack");
        if ($rammingSystem){
            $newFireOrder = new FireOrder(
                -1, "normal", $gate->id, $gate->id,
                $rammingSystem->id, -1, $turn, 1,
                100, 100, 1, 1, 0,
                0, 0, 'JumpFailure', 10001
            );
            $newFireOrder->pubnotes = " loses its reactor entirely - the jump gate collapses.";
            $newFireOrder->addToDB = true;
            $rammingSystem->fireOrders[] = $newFireOrder;
        }

        $damageEntry = new DamageEntry(
            -1, $gate->id, -1, $turn,
            $primaryStruct->id, $primaryStruct->getRemainingHealth(), 0, 0, -1, true, false,
            "", 'JumpFailure'
        );
        $damageEntry->updated = true;
        if ($rammingSystem){
            $damageEntry->shooterid = $gate->id;
            $damageEntry->weaponid  = $rammingSystem->id;
        }
        $primaryStruct->damage[] = $damageEntry;

        Debug::log("Jump gate " . $gate->id . " (game " . $gamedata->id . ") destroyed on turn " . $turn
            . " - total reactor loss.");
    }

    /* THE CLAIMING PLAYER'S NEAREST QUALIFYING UNIT, or null when they have none in range.
     *
     * ⭐ WHICH UNIT IS NEVER CHOSEN BY THE PLAYER (user ruling 2026-08-23, plan section 2.1). The
     * requirement is "you have a unit within signal range of the gate", not "this ship signals" -
     * so this answers two questions at once and they are the same question: whether the claim is
     * legal at all, and the DISTANCE a contested claim is settled on, which is why it returns the
     * NEAREST rather than the first it finds.
     *
     * ⭐ NO LINE-OF-SIGHT TEST, ANYWHERE, ON EITHER SIDE (user ruling 2026-08-23). Signalling a
     * gate is a transmission, not an aimed effect - unlike a ship projecting its own vortex, which
     * runs mathlib.isLoSBlocked / $gamedata->blockedHexes. Its absence here is the RULE, not an
     * omission; do not "fix" it.
     *
     * ⭐ AND IT NEVER REVEALS THE UNIT IT FINDS. A stealthed, shaded or cloaked ship may be the
     * signaller and keeps its concealment - see hasVortexDeclaration, where that ruling holds by
     * construction. The one place the unit can leak is the claim order's targetid, which
     * TacGamedata::hideSystemFireOrders masks for every viewer it does not belong to.
     *
     * QUALIFYING = live, deployed, non-terrain, owned by $playerId, within $this->range hexes
     * (10 on a gate engine - markGate makes $range a SIGNAL range). Note that a mine or an OSAT
     * qualifies: the plan's test is ownership and presence, not what the unit is.
     *
     * ⚠️ Called with a REAL gamedata load on both paths (Firing gets $gd, the resolution sweep
     * runs in advance()), which is what makes getHexPos() and getTurnDeployed() trustworthy - a
     * POST-side ship carries no movement history (plan trap 2). */
    public function getNearestGateSignaller($gate, $gamedata, $playerId)
    {
        if (!$gate || !$gamedata) return null;

        $gateHex  = $gate->getHexPos();
        $best     = null;
        $bestDist = null;

        foreach ($gamedata->ships as $unit){
            if ($unit->userid != $playerId) continue;
            if (!empty($unit->removed)) continue;
            if ($unit->isDestroyed($gamedata->turn)) continue;
            if ($unit->isTerrain()) continue;                              //a gate cannot signal itself
            if ($unit->getTurnDeployed($gamedata) > $gamedata->turn) continue; //not on the board yet

            $distance = $gateHex->distanceTo($unit->getHexPos());
            if ($distance > $this->range) continue;

            if ($bestDist === null || $distance < $bestDist){
                $bestDist = $distance;
                $best     = $unit;
            }
        }

        return $best;
    }

    /* THE ENGINE'S REAL LOADING STATE on $turn, derived from the vortex note rather than stored.

       Four states, in the order they occur (see the constructor):
         no vortex ever opened                  -> fully charged
         a vortex is open, or closes THIS turn   -> 0; the charge was spent opening it and does not
                                                   come back while the jump point stands
         the turn after it closed                -> 1, climbing one per turn to the cap
         a later vortex seen from an earlier replay turn -> fully charged, which is what was true then

       DERIVED, NOT STORED, on purpose. The ordinary Weapon loading machinery would ALMOST do this
       - a ballistic order zeroes the count at the phase-2 advance and phase -1 adds one per turn -
       but it comes out a turn early whenever the vortex closes without a Maintain declaration on
       its last turn (unmaintained, out of range, the four-turn cap), because the recharge starts
       from the turn after the CLOSURE, not from the turn after the last order. The note already
       carries both turns, so asking it is both shorter and exact.

       ⚠️ (int) on both sides: $gamedata->turn / TacGamedata::$currentTurn are STRINGS out of
       mysqli (plan section 5 trap 10) while the note's turns are int-cast. */
    public function getVortexRechargeLoad($turn)
    {
        $turn = (int)$turn;

        /* ⚠️ THE CAP IS THE RECHARGE TIME, AND ON A FIXED GATE THAT IS NOT $delay ALONE (plan
           section 2.5). A gate's recharge lengthens with reactor damage - 20 + floor(D/3) - so a cap
           of $delay would leave a damaged gate climbing to 20 and stopping there, one short of a
           target it can never reach: the gate would simply never recharge again.
           getVortexRechargeTime() returns max(1, (int)$this->delay) unchanged for a ship engine,
           which is the expression this line has always carried. */
        $charge = $this->getVortexRechargeTime();

        if ($this->vortexOpenTurn === null) return $charge;      //never opened one
        if ((int)$this->vortexOpenTurn > $turn) return $charge;   //a later vortex, from an earlier replay turn

        $closeTurn = (int)$this->vortexCloseTurn;
        if ($closeTurn < 0 || $turn <= $closeTurn) return 0;      //open, or closing at the end of this turn

        return min($charge, $turn - $closeTurn);                  //recharging, from the turn AFTER it closed
    }

    /* How many turns the vortex this engine holds has been OPEN as of $turn, or null when it holds
       none. 0 on the turn it was DECLARED (it has formed; it is not open yet), 1 on the first turn
       a unit can fly into it, up to MAX_VORTEX_TURNS.

       This is the counter Stage 5 used to smuggle out through $turnsloaded. It now travels as a
       field of its own (see stripForJson) so that the loading state can mean loading. */
    public function getVortexAge($turn)
    {
        $turn = (int)$turn;

        if ($this->activeVortexId === null || $this->vortexOpenTurn === null) return null;
        if ((int)$this->vortexOpenTurn > $turn) return null;      //a later vortex, from an earlier replay turn
        if (!$this->hasOpenVortex($turn)) return null;            //closed - the engine is free again

        return min($turn - (int)$this->vortexOpenTurn, self::MAX_VORTEX_TURNS);
    }

    /* Has $ship declared a vortex on $turn? Public static because the CONCEALMENT systems need to
       ask it, not the engine itself: opening a jump point reveals a hidden ship (plan section 2.1),
       and ShadingField and CloakingDevice each act on the answer in their own
       generateIndividualNotes. Kept here so the "what counts as a declaration" rule lives with the
       engine and cannot drift between the two callers.

       ⚠️ Called from InitialOrdersGamePhase::process, which runs note generation BEFORE
       Firing::validateFireOrders - so ->rejected is not set yet and an ILLEGAL declaration would
       still reveal the ship. That is deliberate: Stealth::isDetectedInitial has always behaved the
       same way (it reveals on firedOffensivelyOnTurn, also before validation), the client refuses
       illegal hexes anyway, and the only way to reach it is a tampered POST. */
    public static function hasVortexDeclaration($ship, $turn){
        if (!$ship || !is_array($ship->systems)) return false;
        foreach ($ship->systems as $system){
            if (!($system instanceof JumpEngine)) continue;
            /* ⭐⭐ JUMP GATES (PHASE 2) - DO NOT "FIX" THIS METHOD TO COVER GATES. IT IS A RULING.
               (JUMP_GATES_PLAN.md sections 2.1 and 3.3, trap 5; user ruling 2026-08-23.)

               SIGNALLING A FIXED JUMP GATE NEVER REVEALS A HIDDEN UNIT. A stealthed, shaded or
               cloaked ship may signal a gate and keeps its concealment - which is the OPPOSITE of
               the rule for a ship opening its own vortex, and it is deliberate: a gate signal is a
               transmission that points at nothing, and nothing about the declaration says which of
               the player's units sent it.

               This loop walks the SHIP'S OWN engines, and a gate claim is a fire order on the
               GATE'S engine, so a signaller already fails this test and keeps its cloak. That is
               the rule holding by construction, not an oversight - the mirror image of the LoS
               note in section 2.1 of the Phase 1 plan. Widening this to "does any gate in the game
               hold an order naming one of my units?" would silently reverse a ruling.

               (The one place the signaller CAN leak is the claim order's targetid, which names a
               real unit and becomes public from phase 2 onward. That is masked in
               TacGamedata::hideSystemFireOrders, not here.) */
            /* ⭐ SECTION 9 - THIS SKIP IS THE CLOAK GUARANTEE, AND IT IS THE WHOLE OF IT.
               This method is what CloakingDevice / ShadingField ask before revealing their ship,
               and it asks only "does this engine hold ANY order this turn?" - no firing-mode test,
               because a legal declaration is any of the seven modes. A legacy engine jumps by
               BOOST, which writes a tac_power type-2 row and no fire order at all, so a cloaked
               Trek hull boosting its Nacelle already fails this test and keeps its cloak. Skipping
               legacy engines outright is what makes that true by construction rather than by
               coincidence: it also covers a stale order left on a hull that was reverted to legacy
               mid-campaign, and it cannot be undone by a future edit that relaxes the test above.
               (User ruling 2026-08-22: boosting to jump must NOT break concealment.) */
            if ($system->isLegacyJump()) continue;
            foreach ($system->fireOrders as $fire){
                if ($fire->turn == $turn && empty($fire->rejected)) return true;
            }
        }
        return false;
    }

    /* The notes a CONCEALMENT system must write when its ship opens a jump point (plan section 2.1).
       Returns an empty array when no vortex was declared, so the caller can merge unconditionally.

       Two notes' worth of work, and both are required:
         1. $offNoteKey ('Unshaded' / 'Decloaked') drops the concealment for this turn. Notes load
            sorted by turn then PHASE, so this phase-1 note beats the phase -1 'Shaded'/'Cloaked'
            note of the same turn and wins.
         2. a 'detected' note per enemy team, which is the reveal itself - the same shape
            Stealth::isDetectedInitial writes when a hidden ship fires or uses non-DEW EW.
       Note 1 without note 2 leaves the ship concealed until the next detection sweep; note 2
       without note 1 is UNDONE by that sweep, because checkStealthNextPhase re-runs at the end of
       Movement and writes 'undetected' for every team out of range while $active is still true.

       RETURNED rather than pushed because ShipSystem::$individualNotes is protected - each system
       owns its own note list, and this only states the rule.

       ⚠️ notekey_human is varchar(40); the string below is 17. Do not lengthen it carelessly. */
    public static function vortexRevealNotes($ship, $systemId, $gamedata, $offNoteKey){
        $notes = array();
        if (!self::hasVortexDeclaration($ship, $gamedata->turn)) return $notes;

        $human = 'Jump point opened';

        $notes[] = new IndividualNote(-1, TacGamedata::$currentGameID, $gamedata->turn, $gamedata->phase,
                                      $ship->id, $systemId, $offNoteKey, $human, 1);

        $seenTeams = array();
        foreach ($gamedata->slots as $slot){
            $teamId = (int)$slot->team;
            if ($teamId == $ship->team) continue;
            if (in_array($teamId, $seenTeams)) continue; //several slots can share one team
            $seenTeams[] = $teamId;
            $notes[] = new IndividualNote(-1, TacGamedata::$currentGameID, $gamedata->turn, $gamedata->phase,
                                          $ship->id, $systemId, 'detected', $human, "Team:" . $teamId);
        }

        return $notes;
    }

    /* ================= STAGE 3 - THE VORTEX UNIT ==================================
     *
     * THE SPAWN SWEEP. Every legal vortex declaration made this Initial Orders turns into a
     * SpawnJumpPoint terrain unit on the board. Called once, from the END of
     * InitialOrdersGamePhase::advance.
     *
     * ⚠️ Must run off a REAL getTacGamedata load, never from generateIndividualNotes: POST-side
     * ships carry no enhancements and no loaded notes, so $activeVortexId would read null on every
     * one of them and a ship would re-open a vortex it already holds (plan section 5 trap 2).
     * advance() is handed exactly such a load, which is also why $ship->getHexPos() is trustworthy
     * there.
     *
     * ⚠️ Do NOT branch on $gamedata->phase in here. advance() has already set the next phase, so it
     * reads 2 by the time this runs (plan section 5 trap 1). The turn is what identifies "this
     * declaration", and that is what getVortexDeclaration matches on.
     *
     * ⚠️ Position in advance() matters: this runs AFTER the active-ship selection. The freshly
     * inserted vortex joins $gamedata->ships immediately (Manager::insertSingleShip), and while
     * hasShipsAtIniative filters terrain out, the array_filter inside
     * SimultaneousMovementRule::getNewActiveShip does NOT - so a vortex spawned before that call
     * could be handed to the Movement phase as an active unit. The two early returns above it are
     * unreachable for a ship that just declared a vortex: such a ship is on the board, undestroyed,
     * not terrain and not a mine, which is precisely what hasShipsAtIniative requires. */
    public static function spawnDeclaredVortices($gamedata, $dbManager = null)
    {
        foreach ($gamedata->ships as $ship){
            if ($ship->isTerrain()) continue; //terrain never declares; Phase 2's fixed gates get their own path
            if ($ship->isDestroyed($gamedata->turn)) continue;
            /* REINFORCEMENTS_PLAN.md §3.4 - NOTHING IN HYPERSPACE OPENS AN ENTRANCE. This sweep makes
               yellow SpawnJumpPoints, which units fly into to LEAVE the battle; a unit that is not
               on the board has nothing to leave from and its only legal declaration is an exit,
               which the Stage 6 sweep owns.
               Belt and braces with getVortexDeclaration's damageclass skip: that one stops a
               well-formed exit order being read here, this one stops ANY order on a hyperspace
               unit reaching the entrance path at all. Either alone would do; both together mean a
               future order shape cannot reintroduce the bug by accident. */
            if ($ship->isReinforcement()) continue;

            foreach ($ship->systems as $system){
                if (!($system instanceof JumpEngine)) continue;

                $declaration = $system->getVortexDeclaration($gamedata->turn);
                if (!$declaration) continue;

                $system->openVortex($ship, $declaration, $gamedata);
            }
        }

        /* STAGE 6 - openVortex writes a COMBAT-LOG ORDER, and InitialOrdersGamePhase::advance has
         * no submit of its own, so this sweep has to persist its own. Same shape
         * Movement::resolveJumpOuts uses at the other end of the turn.
         *
         * Phase 2 explicitly: submitFireorders reads the phase only to filter ballistic and
         * pre-firing orders, and these are neither - passing 1 would silently drop every one.
         *
         * ⚠️ Narrowed to the orders THIS sweep wrote rather than to everything getNewFireOrders
         * returns. Nothing else in advance() creates fire orders today, but a submit that hoovers
         * up whatever happens to be marked addToDB is a trap for whatever gets added next: the
         * ships' own declarations went in during process(), and re-submitting one would duplicate
         * a row rather than fail loudly. */
        if ($dbManager === null) return;

        $logOrders = array();
        foreach ($gamedata->getNewFireOrders() as $fire){
            if ($fire->damageclass === 'JumpVortex') $logOrders[] = $fire;
        }
        if (empty($logOrders)) return;

        $dbManager->submitFireorders($gamedata->id, $logOrders, $gamedata->turn, 2);
    }

    /* ================= JUMP GATES (PHASE 2) - THE CLAIM RESOLUTION SWEEP ==========
     *
     * Every FIXED GATE that was signalled in the Initial Orders that just closed opens its jump
     * point here. The sibling of spawnDeclaredVortices above, run immediately after it from
     * InitialOrdersGamePhase::advance, and it exists precisely BECAUSE that sweep skips terrain
     * (JUMP_GATES_PLAN.md trap 2: that skip is CORRECT - the two resolve different rules and must
     * not be unified).
     *
     * WHAT IS DIFFERENT FROM A SHIP'S DECLARATION, and why it needs a sweep of its own:
     *
     *   A SHIP'S declaration is uncontested by construction - a ship may hold one vortex and only
     *   its owner can order it, so "the declaration" is a single order and opening it is one call.
     *
     *   A GATE belongs to nobody in particular and ANY player may signal it, so several claims from
     *   several players can arrive on the same gate in the same turn and exactly one of them can
     *   win. Resolution is NEAREST-FIRST WITH NO OWNER PRIORITY (plan section 2.4, user ruling
     *   2026-08-23): each claiming player's distance is to THEIR nearest qualifying unit, smallest
     *   wins, and a tie is settled by a bounded roll-off.
     *
     * ⚠️ RUNS OFF A REAL getTacGamedata LOAD, never off POST-side ships - the same requirement
     * spawnDeclaredVortices documents. The engine's vortex state comes from its notes and the
     * units' positions from their movement, and a POST-side object has neither (plan trap 2).
     *
     * ⚠️ DO NOT BRANCH ON $gamedata->phase IN HERE. advance() has already set the next phase, so it
     * reads 2 by the time this runs. The TURN is what identifies "this turn's claim".
     *
     * ⚠️ THE ROLL-OFF IS PERSISTED, NOT RE-DERIVED (plan section 2.4). It happens once, here, and
     * its outcome is what the 'Vortex' and 'VortexHold' notes record - so a reload, a replay and a
     * second advance all read the same winner. Nothing re-rolls on load. */
    public static function openSignalledGates($gamedata, $dbManager = null)
    {
        $logOrders = array();

        foreach ($gamedata->ships as $gate){
            if (!$gate->isTerrain()) continue;              //only terrain can be a gate...
            if ($gate->isDestroyed($gamedata->turn)) continue;

            foreach ($gate->systems as $system){
                if (!($system instanceof JumpEngine)) continue;
                if (!$system->isGateJump()) continue;       //...and only a MARKED engine is a gate's

                $system->resolveGateClaims($gate, $gamedata, $logOrders);
            }
        }

        /* THE LOG ORDERS THIS SWEEP WROTE, AND ONLY THOSE. spawnDeclaredVortices submits by
         * re-scanning $gamedata->getNewFireOrders() for damageclass 'JumpVortex', which is correct
         * while it is the only sweep writing them - but it runs FIRST, so a second scan here would
         * pick its orders up again and insert every one of them twice. writeVortexLogOrder returns
         * what it created for exactly this reason.
         *
         * Phase 2 explicitly, for the same reason spawnDeclaredVortices passes 2: submitFireorders
         * reads the phase only to filter ballistic and pre-firing orders, and these are neither -
         * passing 1 would silently drop all of them. */
        if ($dbManager === null || empty($logOrders)) return;

        $dbManager->submitFireorders($gamedata->id, $logOrders, $gamedata->turn, 2);
    }

    /* ============ STAGE 9 - ONLY THE NEAREST TEAM'S SIGNAL IS DRAWN ON A CONTESTED GATE ==========
     *
     * "When two or more teams signal a Jump Gate to open, we should only show the ballistic hex
     * icon for the closest team at the moment of signalling (to show who will win). If it's tied we
     * can show both as it's still unclear." (User request 2026-08-29.)
     *
     * WHAT WAS WRONG. A gate claim is a fire order on the GATE's engine, and fire orders become
     * public from phase 2 (hideSystemFireOrders strips them in phase 1 only). So from the Movement
     * phase onward every claim made on a gate drew its own marker on the gate's single hex - two
     * teams signalling one gate put two markers on one hex, in whichever colours they happened to
     * be, one of which was already dead. The player could see that the gate had been signalled and
     * could not see by whom, for how long, or - the useful part - who was going to get it.
     *
     * ⭐ IT IS A MASK, NOT A RENDERING RULE, and it could not have been anything else. The distance
     * that settles the contest is measured to the claimant's NEAREST QUALIFYING UNIT, which the
     * claim records in targetid - and targetid is masked to -1 for every viewer it does not belong
     * to (JUMP_GATES_PLAN.md section 2.1: which unit signalled is never revealed). A client cannot
     * measure a distance from a unit it has been told nothing about. So the server decides, and
     * what it sends is one marker.
     *
     * ⭐ AND IT LEAKS STRICTLY LESS THAN BEFORE, which is why it needs no concealment argument of
     * its own. Every claim it drops was already public; nothing it keeps was not. The signaller is
     * still never named, the duration is still never shown, and a viewer learns exactly one new
     * thing - that somebody nearer than them has claimed the same gate - which is the thing the
     * user asked to be told.
     *
     * ⚠️ BY TEAM, NOT BY PLAYER. Two allies may both signal one gate; between them they hold one
     * position on the map and it would be nonsense to show one ally's claim and hide the other's
     * at the same distance. The RESOLUTION is still per player (resolveGateClaims takes one claim
     * per userid and rolls off ties between userids) - this is a drawing rule and it deliberately
     * does not try to predict the roll-off. A distance tie shows every tied team, exactly as asked:
     * at that point it genuinely is unclear.
     *
     * ⚠️ RUN BEFORE hideSystemFireOrders, which is what makes targetid readable at all here.
     *
     * ⚠️ NOT GATED ON THE REINFORCEMENTS RULE, deliberately. A contested gate is a Jump Gates
     * Phase 2 situation and predates reinforcements entirely; both flavours of claim ('jumppoint'
     * and 'gateexit') are contested by the same rule and both are masked here. The efficiency gate
     * is the terrain test - a game with no jump gate pays one pass over its terrain and stops.
     *
     * ⚠️ REPLAY IS UNAFFECTED and that is correct: deleteHiddenData does not run for a past turn
     * ($all = true), so a post-mortem still shows every claim that was made. The mask is about
     * what a player may know while the turn is live. */
    public static function maskLosingGateClaims($gamedata)
    {
        $turn = (int)$gamedata->turn;

        foreach ($gamedata->ships as $gate){
            if (!$gate->isTerrain()) continue;
            if ($gate->isDestroyed($turn)) continue;

            foreach ($gate->systems as $system){
                if (!($system instanceof JumpEngine)) continue;
                if (!$system->isGateJump()) continue;

                $system->maskLosingClaimsOnGate($gate, $gamedata);
            }
        }
    }

    /* ONE GATE'S CLAIMS, THINNED TO THE NEAREST TEAM. See maskLosingGateClaims for the whole rule.
     *
     * Deliberately a MIRROR of resolveGateClaims's distance pass rather than a call into it: that
     * one opens the vortex, writes notes, spends a charge and logs to the combat record, none of
     * which may happen on a per-viewer payload load. What the two share is
     * getNearestGateSignaller, which is the only part that has to agree - and it is the whole of
     * the distance rule. */
    protected function maskLosingClaimsOnGate($gate, $gamedata)
    {
        $turn = (int)$gamedata->turn;

        //index into $this->fireOrders => the claiming TEAM, for the claims that are still live
        $claimTeam = array();
        $bestByTeam = array();

        foreach ($this->fireOrders as $i => $fire){
            if ((int)$fire->turn !== $turn) continue;
            if (!empty($fire->rejected)) continue;

            $mode = (int)$fire->firingMode;
            if ($mode < 1 || $mode > self::MAX_VORTEX_TURNS) continue;

            /* AN ORDER NAMING NOTHING CLAIMS NOTHING. resolveGateClaims skips it too, so it can
               never open anything - which makes its marker a lie about a doorway nobody is getting.
               Recorded with a null team rather than skipped outright, so it COUNTS as a claim for
               the contest test below and is dropped with the other losers: a `continue` here would
               leave it standing beside the winner, saying two players had signalled when one had. */
            $claimant = $gamedata->getShipById((int)$fire->targetid);
            if (!$claimant){
                $claimTeam[$i] = null;
                continue;
            }

            /* THE DISTANCE IS RE-MEASURED, NOT READ OFF THE ORDER, for the same reason
               resolveGateClaims re-measures it: the number that decides anything must never be one
               the client sent. A claimant with nothing in range any more has a lapsed claim
               (resolveGateClaims skips it too), so it can never be the nearest and is dropped. */
            $signaller = $this->getNearestGateSignaller($gate, $gamedata, (int)$claimant->userid);
            if (!$signaller){
                $claimTeam[$i] = null;
                continue;
            }

            $team     = (int)$claimant->team;
            $distance = (int)$gate->getHexPos()->distanceTo($signaller->getHexPos());

            $claimTeam[$i] = $team;
            if (!isset($bestByTeam[$team]) || $distance < $bestByTeam[$team]) $bestByTeam[$team] = $distance;
        }

        /* NOTHING CONTESTED - one claim, or none. A single claim is left alone even when it has
           LAPSED (the claimant has nothing in signal range any more, so resolveGateClaims will
           refuse it): there is nobody it is losing to, and hiding a player's own signal with no
           rival to explain it would read as the order having gone missing. The combat log says why
           it failed at the end of the turn, which is where that belongs.
           ⚠️ COUNTED IN CLAIMS, NOT IN TEAMS. A lapsed claim has no team and would not be counted
           at all by a $bestByTeam test - so a contest between one live claim and one lapsed one
           would look uncontested and both markers would stand, the far one included. */
        if (count($claimTeam) < 2) return;

        //Every claim lapsed: there is no nearest to keep, and dropping the lot would leave the gate
        //looking unsignalled when it demonstrably was.
        if (empty($bestByTeam)) return;

        $best = min($bestByTeam);

        /* DESCENDING, so unset() cannot renumber an index this loop has not reached yet - the same
           reason every fire-order sweep in hideSystemFireOrders counts down. krsort rather than a
           reverse for(), because $claimTeam is keyed by the ORIGINAL fireOrders indices. */
        krsort($claimTeam);

        foreach ($claimTeam as $i => $team){
            if ($team !== null && $bestByTeam[$team] === $best) continue;   //this team is (equal) nearest
            unset($this->fireOrders[$i]);
        }

        //Re-index: json_encode emits a JSON OBJECT rather than an array for a gappy list, and the
        //client iterates fireOrders positionally ([[arch_php_empty_array_json]]).
        $this->fireOrders = array_values($this->fireOrders);
    }

    /* ================= REINFORCEMENTS STAGE 6 - THE EXIT FORMS, AND IT DEVIATES ============
     *
     * THE THIRD SWEEP. Every legal jump point EXIT declared in the Initial Orders of this turn
     * turns into a SpawnJumpPointExit on the board here - at a hex the DEVIATION ROLL decides,
     * which is usually not the hex the player named. Called once, from
     * InitialOrdersGamePhase::advance, immediately after openSignalledGates.
     *
     * ⭐⭐ THE END OF INITIAL ORDERS, NOT THE END OF THE FIRING PHASE (user ruling 2026-08-29,
     * REINFORCEMENTS_PLAN.md section 2.3). Both players have committed their orders by the time
     * this runs, so rolling here lets BOTH OF THEM SEE WHERE THE DOORWAY ACTUALLY LANDED for the
     * whole of the turn it forms on, and move and shoot accordingly - instead of finding out at the
     * start of the arrival turn, with nothing left to do about it.
     *
     * ⭐ AND THE DECLARATION IS REWRITTEN TO THE ROLLED HEX (rewriteDeclaration below), which is
     * what makes that visible without one line of client code. The blue "Jump Point Forming" marker
     * is drawn from the fire order - by its owner directly, and for an opponent through the
     * republished PlayerSlot entry TacGamedata::republishFormingExits lifts off the same order - so
     * moving the order moves both markers at once, to the same hex, from phase 2 onward.
     *
     * ⚠️ THIS DELIBERATELY REVERSES SECTION 2.3'S ORIGINAL CONCEALMENT ARGUMENT, which was that a
     * vortex created before the Firing phase would publish its DEVIATED hex in every viewer's
     * payload for the whole of turn N. It does, and that is now the POINT. Nothing else about the
     * lifecycle moved: $spawned is still openTurn + 1, so the vortex UNIT still appears on the board
     * on the arrival turn and not before ([[arch_info_bleed_masking]]).
     *
     * ⚠️ THE MANIFEST HALF DID NOT MOVE WITH IT - see stampExitManifests, which still runs at the
     * end of the Firing phase and MUST. Splitting them is not tidiness: collectGateExits asks
     * whether a gate's doorway will still be open NEXT turn, and that answer is only correct after
     * closeExpiredVortices has run.
     *
     * ⚠️ RUNS OFF A REAL getTacGamedata LOAD, never off POST-side ships - the requirement both
     * sibling sweeps carry, and the one this sweep depends on most: isReinforcement(), arrivalVia
     * and the engine's own vortex state are all absent on a posted object (plan trap 3).
     *
     * ⚠️ DO NOT BRANCH ON $gamedata->phase. advance() has already set the next phase (trap 1). The
     * TURN is what identifies "this turn's declaration".
     *
     * ⚠️ IDEMPOTENT, because advance() can run twice. hasOpenVortex is what makes it so (it is
     * spawnVortexUnit's first line), and it is also what stops the rewritten declaration being put
     * through the deviation table a second time.
     *
     * THE RULE GATE IS AN EFFICIENCY GUARD AS WELL - the same one persistManifest opens with. A
     * game without the rule cannot contain a reinforcement, so there is nothing here to find. */
    public static function spawnExitVortices($gamedata, $dbManager = null)
    {
        if (!$gamedata->rules || !$gamedata->rules->hasRuleName('allowReinforcements')) return;

        $turn = (int)$gamedata->turn;

        /* THE LOG LINES AND THE MOVED DECLARATIONS THIS SWEEP WROTE, AND ONLY THOSE.
           InitialOrdersGamePhase::advance has no submit of its own, and re-scanning
           getNewFireOrders() the way spawnDeclaredVortices does would pick up that sweep's orders a
           second time and insert every one of them twice - the same trap openSignalledGates
           documents, and this sweep runs after both of them. */
        $logOrders = array();
        $moved     = array();

        foreach ($gamedata->ships as $ship){
            /* TERRAIN IS SKIPPED. A gate's doorway is contested, owner-less and does not deviate,
               so it is opened by openSignalledGates a few lines earlier in the same advance() -
               the same reason openSignalledGates is not spawnDeclaredVortices. What a gate still
               needs is the MANIFEST half, which is collectGateExits' job in the Firing phase. */
            if ($ship->isTerrain()) continue;
            //Only a unit still IN HYPERSPACE opens an exit (section 2.2). Anything on the board
            //that carries such an order is a tampered POST, and getExitDeclarationBlock refused it.
            if (!$ship->isReinforcement()) continue;
            //Pre-battle damage can destroy a system, and in principle a whole unit, before turn 1.
            if ($ship->isDestroyed($turn)) continue;

            foreach ($ship->systems as $system){
                if (!($system instanceof JumpEngine)) continue;

                $declaration = $system->getExitDeclaration($turn);
                if (!$declaration) continue;

                /* ALREADY FORMED - a second advance() over the same turn. Nothing to do: the
                   doorway is on the board, the declaration has already been rewritten to its hex,
                   and the manifest is stamped by the Firing-phase sweep from the board state rather
                   than from anything this pass remembers.
                   break, not continue, for the same one-doorway-per-unit reason as below - and this
                   branch is only ever reached on the engine that holds THIS turn's exit, since
                   getExitDeclaration has just answered on it. */
                if ($system->hasOpenVortex($turn)) break;

                /* ONE DOORWAY PER UNIT, and the break is what enforces it HERE. hasOpenVortex is
                   asked of each ENGINE, so a hull carrying two of them would open two exits -
                   the submit path already refuses a second JumpEngine order in one submission
                   (Firing::getExitDeclarationBlock rule 4), and this makes that true of the
                   resolution as well, for a row that reached the table another way. */
                if ($system->openExitVortex($ship, $declaration, $gamedata, $logOrders, $moved)) break;
            }
        }

        if ($dbManager === null) return;

        /* THE MOVED DECLARATIONS FIRST, so a load that races the log insert still finds the hex the
           doorway is really at. Both are writes to rows this sweep owns outright.
           ⚠️ updateFireOrders writes x, y and firingmode among its columns - which is exactly the
           three fields rewriteDeclaration changed - and matches on the order's real DB id, which
           every declaration has by now: it was persisted in InitialOrdersGamePhase::process, one
           commit before this advance(). */
        if (!empty($moved)) $dbManager->updateFireOrders($moved);

        /* Phase 2 explicitly, for the same reason spawnDeclaredVortices and openSignalledGates pass
           2: submitFireorders reads the phase only to filter ballistic and pre-firing orders, and a
           'JumpVortex' log line is neither - passing 1 would silently drop every one. */
        if (!empty($logOrders)) $dbManager->submitFireorders($gamedata->id, $logOrders, $gamedata->turn, 2);
    }

    /* ⭐⭐ REINFORCEMENTS_PLAN.md STAGE 6/8 - THE MANIFEST HALF, AND IT STAYS AT THE END OF THE
     * FIRING PHASE. Every unit riding a doorway that will be open next turn is stamped with an
     * arrival turn; every unit riding one that will not gets its berth refunded.
     *
     * Called once, from FireGamePhase::advance, AFTER closeExpiredVortices and BEFORE the slot loop
     * that hands out next turn's Deployment phases.
     *
     * ⚠️⚠️ IT CANNOT MOVE UP TO INITIAL ORDERS WITH THE SPAWN HALF, AND THAT IS THE WHOLE REASON
     * THE TWO ARE SPLIT (2026-08-29). The question $opened answers is "will this doorway still be
     * there NEXT turn", and closeExpiredVortices is what decides it:
     *
     *   - A GATE on the LAST turn of its programmed hold closes at the end of THIS turn. Asked
     *     before that sweep has run, $vortexCloseTurn is still -1 and holdsExitOpenOn answers YES -
     *     so a whole wave would be stamped for a turn on which its doorway no longer exists, get a
     *     Deployment phase with no legal hex in it, and be stuck there.
     *   - A SHIP'S exit is one-shot and closes at the end of the arrival turn. Asked before the
     *     closure, a rider that stayed behind would have its berth renewed for another turn instead
     *     of refunded.
     *
     * ⭐ AND $opened IS REBUILT FROM THE BOARD, not carried over from the spawn half. The two sweeps
     * now run in different phases off different gamedata loads, so there is nothing to carry; the
     * doorway itself is the record, which is what holdsExitOpenOn reads. That also makes the whole
     * thing idempotent for free - a second advance() re-derives the same list.
     *
     * THE RULE GATE IS AN EFFICIENCY GUARD: a game without the rule cannot contain a reinforcement. */
    public static function stampExitManifests($gamedata, $dbManager = null)
    {
        if (!$gamedata->rules || !$gamedata->rules->hasRuleName('allowReinforcements')) return;

        $turn = (int)$gamedata->turn;

        /* WHICH OPENERS HOLD A DOORWAY THAT SURVIVES INTO NEXT TURN, keyed by opener ship id. The
           manifest pass turns this into arrival turns - and its ABSENCE into a cleared berth - so
           an opener missing from it strands its whole wave. */
        $opened = array();

        foreach ($gamedata->ships as $ship){
            if ($ship->isTerrain()) continue;            //gates are collectGateExits' half, below
            if (!$ship->isReinforcement()) continue;     //an opener rides its own doorway, so it is still in hyperspace

            foreach ($ship->systems as $system){
                if (!($system instanceof JumpEngine)) continue;

                /* THE SAME QUESTION THE GATE PASS ASKS, deliberately: is the jump point this engine
                   holds an EXIT, and will it still be open on the arrival turn. Stronger than the
                   bare hasOpenVortex the old combined sweep used - it also rejects an ENTRANCE,
                   which is not a doorway in (section 2.6). */
                if ($system->holdsExitOpenOn($gamedata, $turn + 1)){
                    $opened[(int)$ship->id] = true;
                    break;   //one doorway per unit
                }
            }
        }

        self::collectGateExits($gamedata, $opened);

        if (empty($opened) && !self::hasExitBerths($gamedata)) return;

        self::stampArrivingReinforcements($gamedata, $dbManager, $opened);
    }

    /* ⭐⭐ REINFORCEMENTS_PLAN.md STAGE 8 - EVERY FIXED GATE HOLDING A DOORWAY IN THAT WILL STILL BE
     * OPEN NEXT TURN, added to $opened so the manifest pass treats it exactly as it treats a ship
     * that opened one. That is the whole of "a wave on each turn of a gate's programmed hold"
     * (section 0): the gate is not re-opened - it opened once, at the end of the turn it was
     * signalled - it is simply counted again, every turn, for as long as it will still be there.
     *
     * ⭐ WHY $opened AND NOT A SWEEP OF ITS OWN. Everything the manifest pass does is already right
     * for a gate: it stamps arrivalturn on anything whose arrivalVia names an id in this list, and
     * it CLEARS the berth of anything whose arrivalVia names an id that is not - which is precisely
     * the Stage 8 refund (an enemy entrance claim won the contest, or the hold has run out). Writing a
     * second sweep would mean writing that refund twice.
     *
     * ⚠️ THE TEST IS hasOpenVortex($turn + 1), NOT ($turn). A manifest named this turn arrives in
     * the DEPLOYMENT PHASE OF NEXT TURN, so the question is whether the doorway is there THEN - and
     * on the last turn of a gate's hold it is not. closeExpiredVortices has already run by the time
     * this sweep does (FireGamePhase::advance calls them in that order) and has written
     * $vortexCloseTurn in memory as well as to the notes, so the answer is correct on the very turn
     * the hold expires rather than a turn late.
     *
     * ⚠️ AND IT MUST BE AN EXIT. A gate holding an ordinary yellow ENTRANCE is not a doorway in
     * (section 2.6), and a berth naming it is a claim that lost - which the manifest pass refunds.
     * The class is the discriminator, as everywhere else in this feature. */
    protected static function collectGateExits($gamedata, &$opened)
    {
        foreach ($gamedata->ships as $gate){
            if (!$gate->isTerrain()) continue;

            foreach ($gate->systems as $system){
                if (!($system instanceof JumpEngine)) continue;
                if (!$system->isGateJump()) continue;

                if ($system->holdsExitOpenOn($gamedata, (int)$gamedata->turn + 1)){
                    $opened[(int)$gate->id] = true;
                    break;   //one doorway per gate, exactly as one per unit above
                }
            }
        }
    }

    /* Is the jump point this engine holds an EXIT, and will it still be open on $turn?
     *
     * Both halves in one place because they are always asked together and the second is the one
     * that is easy to get wrong: hasOpenVortex is "not closed BEFORE $turn", so a vortex closing at
     * the end of turn T answers true for T and false for T+1 - which is exactly the window a
     * manifest needs (a vortex is usable for the whole of the turn it closes on, section 2.3).
     *
     * ⚠️ $spawned IS openTurn + 1, so a doorway signalled this turn already answers true for next
     * turn - which is right, and is what lets the FIRST wave ride a gate on the turn it was
     * signalled. */
    public function holdsExitOpenOn($gamedata, $turn)
    {
        if (!$this->hasOpenVortex($turn)) return false;

        $vortex = $gamedata->getShipById((int)$this->activeVortexId);
        if (!($vortex instanceof SpawnJumpPointExit)) return false;

        return ((int)$vortex->spawned <= (int)$turn);
    }

    /* Does anybody in this game hold a berth that might need clearing? Asked only when NO exit
       formed at all, so the common "rule on, nothing declared" turn walks the ship list and writes
       nothing at all. */
    protected static function hasExitBerths($gamedata)
    {
        foreach ($gamedata->ships as $unit){
            if (!$unit->isReinforcement()) continue;
            if ($unit->arrivalVia !== null) return true;
        }

        return false;
    }

    /* ⭐ THE MANIFEST BECOMES AN ARRIVAL TURN. This is the moment a bought reinforcement stops
     * being "somewhere in hyperspace" and becomes a unit with a deploy turn - section 3.2's one
     * load-bearing change, read from the other end.
     *
     * ⚠️ arrivalturn IS WRITTEN HERE AND NOWHERE ELSE, and no POST can reach it (see
     * DBManager::setShipArrivalTurn). A player naming their own arrival turn would be placing units
     * on a board with no vortex on it.
     *
     * ⚠️ IN MEMORY AS WELL AS IN THE DATABASE. The slot loop at the end of FireGamePhase::advance
     * runs after this and asks getTurnDeployed, so a unit stamped only in the database would be
     * granted its Deployment phase a turn late - which is to say never, because the exit closes
     * at the end of the turn it was meant to be used on.
     *
     * THREE POPULATIONS, and the third is the one worth stating:
     *   the OPENER itself - it always arrives through its own doorway (section 2.2), and it is
     *                       stamped from $opened rather than from its own arrivalVia so a berth
     *                       that never reached the database cannot strand the unit that opened
     *                       the door.
     *   its MANIFEST      - everything whose arrivalVia names an opener that formed one.
     *   a DEAD BERTH      - a manifest whose exit did not form (the opener was destroyed during
     *                       the turn, or its declaration never became a vortex). The berth is void,
     *                       so it is cleared and the unit waits in hyperspace for another doorway.
     *                       That is a REFUND, not a punishment: nothing about the unit is spent.
     *
     * ⭐ A BERTH NAMING A GATE IS SETTLED HERE TOO, from Stage 8 (this used to skip terrain
     * outright, precisely so that it could be). collectGateExits has just put every gate
     * holding a doorway-in that survives into next turn into $opened, so a gate berth takes the
     * ordinary stamp above - and a gate berth that is NOT in that list falls to the clear below,
     * which is the whole of the Stage 8 refund:
     *   - an enemy ENTRANCE claim won the contest, so the gate opened yellow and there is no doorway in;
     *   - or the programmed hold ran out at the end of this turn.
     * Either way nothing about the unit is spent and it waits in hyperspace for another doorway. */
    protected static function stampArrivingReinforcements($gamedata, $dbManager, $opened)
    {
        $arrival = (int)$gamedata->turn + 1;

        foreach ($gamedata->ships as $unit){
            if (!$unit->isReinforcement()) continue;

            /* ⚠️⚠️ A GATE IS IN $opened AS THE DOORWAY'S HOLDER, NOT AS A UNIT RIDING IT (user
               report 2026-08-29, game 4319). The three populations above are all things that ARRIVE;
               collectGateExits adds a gate to the same list because that is what makes its
               MANIFEST arrive, and the id it adds is the gate's own. So a gate that also happens to
               carry the reinforcement flag matched the opener test on itself and was stamped with an
               arrival turn every single turn its jump point stood - which won its owner a phantom
               Deployment phase (TacGamedata::hasReinforcementsArriving reads exactly this field), one
               with nothing in it to place, announcing itself as PRE-TURN ACTIONS.

               The unit cannot be an arrival because it is already on the board: alwaysDeploysTurnOne
               is the same ⚠️ hideHyperspaceReinforcements carries a few hundred lines away, and the
               reason both need it is that isReinforcement() asks where a unit is, not what it is.
               BuyingGamePhase now refuses the flag on such a hull outright, so this can only be
               reached by a game bought before that fix - but it stays, because a stamp written here
               is what every later phase believes. */
            if ($unit->alwaysDeploysTurnOne()) continue;

            $via = ($unit->arrivalVia === null) ? null : (int)$unit->arrivalVia;

            if (isset($opened[(int)$unit->id]) || ($via !== null && isset($opened[$via]))){
                $unit->arrivalTurn = $arrival;
                if ($dbManager !== null) $dbManager->setShipArrivalTurn($unit->id, $arrival);

                Debug::log("Jump point exit: ship {$unit->id} arrives on turn {$arrival} (game "
                    . $gamedata->id . ", via " . ($via === null ? 'its own exit' : $via) . ").");
                continue;
            }

            if ($via === null) continue;

            $unit->arrivalVia = null;
            if ($dbManager !== null) $dbManager->setShipArrivalVia($unit->id, null);

            Debug::log("Jump point exit: ship {$unit->id} loses its berth - exit {$via} did not "
                . "form (game {$gamedata->id}, turn {$gamedata->turn}).");
        }
    }

    /* ================= REINFORCEMENTS STAGE 7 - THE DOORWAY A UNIT ARRIVES THROUGH ==============
     *
     * ⭐ THE ONE QUESTION STAGE 7 ASKS, and both sides ask it: WHICH hex may this unit be placed in
     * and on WHAT facing (plan §2.4). Everything else in the stage - the Deployment phase being
     * granted, the stacking bypass, the forced facing, the optional placement - is downstream of
     * this answer. Returns the SpawnJumpPointExit $unit must arrive through, or null.
     *
     * ⭐⭐ THE LINK IS arrivalVia -> vortexHolderId, AND NEITHER HALF IS NEW. arrivalVia names the
     * OPENER (§3.1: the vortex did not exist when the manifest was named, and for a gate it may
     * never exist at all), and JumpEngine::restoreVortexState already stamps every vortex unit with
     * the id of the ship whose engine holds it - the field the client's getVortexHeldBy has read
     * since Jump Points Stage 5. So the join needs no new column, no new note and no new payload
     * field: the arriving unit names the opener, the doorway names the opener, and they meet.
     *
     * ⚠️ A NULL arrivalVia MEANS "ITS OWN DOORWAY", not "unassigned". The opener always comes
     * through the exit it opened (§2.2), and stampArrivingReinforcements stamps it from the
     * $opened list rather than from a berth - so an opener that never had one still arrives. Read
     * as "unassigned" here, it would be the one unit that could not walk through its own door.
     *
     * ⚠️ THE OPEN/CLOSED WINDOW IS Movement::getOpenVortexInHex's, deliberately verbatim, minus its
     * one-way line (which is what THIS reader wants and that one refuses). A vortex is usable for
     * the whole of the turn it closes on, hence >= on removedTurn - see hasOpenVortex.
     *
     * ⚠️ ASK IT OF A SERVER-SIDE SHIP. A POST-side unit carries neither $reinforcement nor
     * $arrivalTurn (plan trap 3 / Manager::getShipsFromJSON), so it answers null to everything and
     * the caller would silently fall through to the slot-box test. Every caller here resolves
     * through $gamedata->getShipById() first. */
    public static function getArrivalVortex($unit, $gamedata)
    {
        if (!self::isArrivingReinforcement($unit, $gamedata)) return null;

        //Null arrivalVia = its own doorway - see the ⚠️ above.
        $openerId = ($unit->arrivalVia === null) ? (int)$unit->id : (int)$unit->arrivalVia;

        foreach ($gamedata->ships as $vortex){
            if (!($vortex instanceof SpawnJumpPointExit)) continue;
            if ($vortex->vortexHolderId === null) continue;          //no 'Vortex' note - nothing to join on
            if ((int)$vortex->vortexHolderId !== $openerId) continue;
            if ($vortex->spawned > $gamedata->turn) continue;        //still forming
            if ($vortex->removed && $vortex->removedTurn !== null
                && $gamedata->turn >= $vortex->removedTurn) continue; //closed
            if (!$vortex->getLastMovement()) continue;               //no deploy row: no hex, no facing

            return $vortex;
        }

        return null;
    }

    /* ⭐⭐ REINFORCEMENTS_PLAN.md STAGE 9 - HOW BADLY THE DOORWAY THIS UNIT IS ARRIVING THROUGH
     * MISSED, as array('hexes' => int, 'facingSteps' => int), or null.
     *
     * The bridge between the roll (Stage 6, which recorded it in a 'VortexScatter' note precisely
     * so that Stage 9 could read it back - a d20 and two dice cannot be re-derived afterwards) and
     * the arrival initiative penalty (BaseShip::getReinforcementArrivalIniModifier).
     *
     * ⭐ THE SCATTER BELONGS TO THE DOORWAY, NOT TO THE UNIT, so it is looked up on the OPENER's
     * engine and every unit riding that doorway gets the same answer. That is the rule: a wave that
     * comes out four hexes off course and turned sideways is disordered as a wave. The unit's own
     * sensors had nothing to do with it - the OPENER's did, which is what §2.5 rolls against.
     *
     * NULL FOR A GATE, and that is a rule too, not a gap: a fixed gate's jump point does not
     * deviate (§2.4 - it opens in the gate's own mouth on the gate's own facing), so nothing riding
     * one is disordered by the arrival. getVortexScatter answers null on every engine that never
     * rolled, and null is the whole of "no penalty".
     *
     * ⚠️ THE FIRST NON-NULL ENGINE WINS, which is unambiguous because one hull may hold one doorway
     * at a time (spawnExitVortices breaks after the first, and Firing refuses a second JumpEngine
     * order in one submission). A hull with two engines where the SECOND opened the doorway still
     * answers correctly, because the first one's getVortexScatter() is null. */
    public static function getArrivalScatter($unit, $gamedata)
    {
        if (!self::isArrivingReinforcement($unit, $gamedata)) return null;

        //Null arrivalVia = its own doorway - the same convention getArrivalVortex documents.
        $openerId = ($unit->arrivalVia === null) ? (int)$unit->id : (int)$unit->arrivalVia;
        $opener   = $gamedata->getShipById($openerId);

        if (!$opener || !is_array($opener->systems)) return null;

        foreach ($opener->systems as $system){
            if (!($system instanceof JumpEngine)) continue;

            $scatter = $system->getVortexScatter();
            if ($scatter !== null) return $scatter;
        }

        return null;
    }

    /* Is this unit leaving hyperspace THIS turn? The predicate behind every Stage 7 branch, in one
     * place so the server's five call sites cannot drift from each other (the client mirror is
     * shipManager.isArrivingReinforcement).
     *
     * Note what it is NOT: isReinforcement() is the HYPERSPACE test (arrivalTurn === null) and this
     * is its complement for one specific turn. A unit that arrived on an earlier turn is an
     * ordinary deployed ship and must answer false here, or it would be re-placed every turn. */
    public static function isArrivingReinforcement($unit, $gamedata)
    {
        if (!$unit || !$unit->reinforcement) return false;
        if ($unit->arrivalTurn === null) return false;

        return ((int)$unit->arrivalTurn === (int)$gamedata->turn);
    }

    /* THIS ENGINE'S EXIT DECLARATION FOR $turn, or null. The mirror of getVortexDeclaration and
     * deliberately NOT a widening of it: that one is the ENTRANCE reader and skips 'jumpexit' PRECISELY
     * so an exit can never be opened as an entrance (section 3.4). Two readers, one discriminator,
     * and no way for a future order shape to be read by both.
     *
     * Everything here has already been checked at submit time by
     * Firing::getExitDeclarationBlock; it is re-asked because a fire order is a row in a table
     * that other code paths can reach, and because the sweep must never hand spawnVortexUnit a
     * nonsense facing.
     *
     * ⚠️ A GATE ENGINE IS NOT EXCLUDED HERE, and does not need to be: gate-ness is asked of the SHIP
     * by the sweep (which skips terrain), and a gate is not a hyperspace reinforcement. When Stage 8
     * gives gates an exit, this reader is the one it will share. */
    public function getExitDeclaration($turn)
    {
        /* ⭐ STAGE 9 - NO LEGACY REFUSAL HERE, and its ABSENCE is the rule. getVortexDeclaration
           (the ENTRANCE reader, a few methods down) still opens with one, because a legacy drive
           has no way OUT of the battle to declare. Coming back is the other direction and a phasing
           hull does it every bit as legitimately as a B5 drive does - it just leaves no vortex
           behind, which openExitVortex decides by picking a different class. Putting the flag test
           back here would silently switch Shadow reinforcements off again: the declaration would
           still be accepted at submit time, the fire order would still be written, and the sweep
           would then find nothing to open - a wave stranded in hyperspace with no error anywhere. */

        foreach ($this->fireOrders as $fire){
            if ($fire->turn != $turn) continue;
            if (!empty($fire->rejected)) continue;
            if ($fire->damageclass !== 'jumpexit') continue;

            //Modes 1-6 are the six facings. 7 is MAINTAIN, which an exit does not have (2.3).
            $mode = (int)$fire->firingMode;
            if ($mode < 1 || $mode > 6) continue;

            if ($fire->x === null || $fire->y === null || $fire->x === "null" || $fire->y === "null") continue;

            return $fire;
        }

        return null;
    }

    /* ONE EXIT, ROLLED AND PUT ON THE BOARD. The exit twin of openVortex, and it shares that
     * method's spawn body (spawnVortexUnit) for the reason recorded there: $spawned, the deploy
     * MovementOrder that carries the facing and the 'Vortex' note that restoreVortexState reads back
     * are the same three records whichever way the door faces, and they must not drift.
     *
     * WHAT IS DIFFERENT is entirely the two lines in the middle: the hex and the facing are the
     * DECLARED ones put through the deviation table, and a note of its own records how far it
     * missed by. Returns true when a doorway now exists.
     *
     * ⭐ $logOrders AND $moved ARE OUT-PARAMETERS, and both are the same idea: this method now runs
     * at the end of INITIAL ORDERS, where advance() has no submit of its own, so each caller has to
     * persist exactly the rows IT created. Collecting them here rather than re-scanning
     * getNewFireOrders() is what stops spawnDeclaredVortices' log lines - written a few lines
     * earlier in the same advance() and already inserted - being inserted a second time. */
    protected function openExitVortex($opener, $fire, $gamedata, &$logOrders = null, &$moved = null)
    {
        $declaredFacing = ((int)$fire->firingMode - 1) % 6; //modes 1-6 -> facings 0-5; range validated
        $declaredHex    = new OffsetCoordinate($fire->x, $fire->y);

        $scatter = self::rollExitDeviation($opener, $gamedata);

        //THE CLAMP applies to the ROLLED hex, never to the declared one.
        list($hex, $distance) = self::findLegalExitHex(
            $declaredHex, $scatter['direction'], $scatter['distance'], $gamedata);

        $facing = $scatter['randomFacing']
            ? (Dice::d(6) - 1)
            : ((($declaredFacing + $scatter['facingDelta']) % 6) + 6) % 6;

        //Signed, shortest way round: -2..3. Stage 9's initiative penalty is 2 per 60 degrees and so
        //takes abs() of this; the sign is kept because a log line reads better for having it.
        $facingSteps = ((($facing - $declaredFacing) % 6) + 6) % 6;
        if ($facingSteps > 3) $facingSteps -= 6;

        /* ⭐⭐ STAGE 9 - A PHASING HULL GETS A DOORWAY WITH NO PICTURE (user ruling 2026-08-29).
           A Shadow ship fades out rather than tearing a vortex open (PhasingDrive, markLegacy), and
           it now fades IN the same way. SpawnJumpPointPhaseIn is a SpawnJumpPointExit in every
           respect the rules care about - the same instanceof, the same one-way rule, the same
           arrival lookup, the same closure - and the client draws nothing at all for it. See that
           class for why the difference is a phpclass rather than a flag.
           The deviation above is rolled either way: navigating out of hyperspace is the same job
           however the hull does it, and an Ancient's -5 already makes a Shadow arrival precise most
           of the time (§2.5). */
        $vortexClass = $this->legacyJump ? 'SpawnJumpPointPhaseIn' : 'SpawnJumpPointExit';

        $vortex = $this->spawnVortexUnit($opener, $hex, $facing, $gamedata, $vortexClass);
        if (!$vortex) return false;

        $this->writeVortexScatterNote($opener, $vortex->id, $distance, $facingSteps, $gamedata);
        $this->rewriteDeclaration($fire, $hex, $facing, $moved);

        /* THE LOG LINE NAMES THE BAND (section 2.5). A player who lands nine hexes away should be
           able to see that it was the 2d10+2 band rather than bad luck inside a good one - the
           modifiers are large and the bands are what they act on.
           The order goes back to spawnExitVortices to be submitted - see the ⭐ on this method's
           signature for why it is collected rather than re-scanned for. */
        $where = ($distance === 0)
            ? "exactly where it was aimed"
            : ($distance . ($distance == 1 ? " hex" : " hexes") . " from the declared hex");

        $turned = ($facingSteps === 0) ? "" : ", facing turned " . abs($facingSteps) * 60 . " degrees";

        //STAGE 9 - a phasing hull opens nothing, so the log must not say it did. Same numbers, same
        //bands, same sentence shape - the verb is the only thing that changes.
        $opens = $this->legacyJump
            ? " phases in from hyperspace"
            : " opens a jump point exit";

        $log = self::writeVortexLogOrder($opener, $gamedata,
            //$opens . " - sensors " . $scatter['sensors'] . ", roll " . $scatter['roll']
			$opens . "- rolled " . $scatter['roll']
            . " (" . $scatter['band'] . "). It forms " . $where . $turned
            . ", and the units riding it arrive through it next turn.");
        if ($log && is_array($logOrders)) $logOrders[] = $log;

        Debug::log("Jump point exit: ship {$opener->id} opens vortex {$vortex->id} at "
            . $hex->q . "," . $hex->r . " facing " . $facing . " (declared " . $declaredHex->q . ","
            . $declaredHex->r . " facing " . $declaredFacing . "; sensors " . $scatter['sensors']
            . ", roll " . $scatter['roll'] . ", " . $scatter['band'] . ", scattered " . $distance
            . ", facing steps " . $facingSteps . ") in game {$gamedata->id}, turn {$gamedata->turn}.");

        return true;
    }

    /* ⭐⭐ THE DECLARATION IS MOVED TO WHERE THE DOORWAY ACTUALLY FORMED (user ruling 2026-08-29,
     * REINFORCEMENTS_PLAN.md §2.3). This is the whole of "both players can see where the actual
     * exit will be on the formation turn", and it is deliberately done by moving the ORDER rather
     * than by teaching the client a second source:
     *
     *   - THE OWNER draws their blue marker straight off this order
     *     (BallisticIconContainer::generateExitHexes), and gets it back from the database from
     *     phase 2 onward - hideSystemFireOrders strips a current-turn ballistic order in phase 1
     *     only.
     *   - AN OPPONENT has no opening ship in their payload at all, so the hex and the facing are
     *     republished onto the PlayerSlot by TacGamedata::republishFormingExits - which reads the
     *     SAME three fields off the SAME order.
     *
     * So one write moves both markers, to the same hex, with no client change and no second
     * concealment rule to keep in step.
     *
     * ⚠️ x/y ARE STRINGS in tac_fireorder (varchar columns, and mysqli hands them back as strings),
     * which is why the ballistic pipeline compares them against the literal "null". Written as ints
     * here and cast on the way in by the DB layer; every reader either casts or builds an
     * OffsetCoordinate out of them.
     *
     * ⚠️ firingMode IS THE FACING, mode = facing + 1, the same convention the entrance uses. It is
     * rewritten because the marker's ARROW is drawn from it, and a doorway that came out of the
     * 1d10 band with its facing shifted 60 degrees would otherwise advertise the wrong mouth.
     * Modes 1-6 only: MAINTAIN (7) is not a thing an exit has, and getExitDeclaration refuses it.
     *
     * ⚠️ AND IT IS ONLY EVER DONE ONCE. spawnExitVortices breaks out on hasOpenVortex before
     * reaching openExitVortex on a second advance(), so the rolled hex can never be put through the
     * deviation table again as though it were the declared one.
     *
     * $moved is an out-parameter and may be null - the harnesses call openExitVortex directly. The
     * in-memory order is updated either way, so the rest of THIS request agrees with the database. */
    protected function rewriteDeclaration($fire, OffsetCoordinate $hex, $facing, &$moved = null)
    {
        $fire->x          = (int)$hex->q;
        $fire->y          = (int)$hex->r;
        $fire->firingMode = ((((int)$facing % 6) + 6) % 6) + 1;

        //A row that has never been inserted has nothing to update - and cannot be reached from the
        //sweep, where every declaration was persisted by the preceding process() commit.
        if (is_array($moved) && (int)$fire->id > 0) $moved[] = $fire;
    }

    /* ⭐ THE DEVIATION TABLE (section 2.5), rolled once per exit. Returns everything the caller
     * and the log line need: the sensor rating, the modified roll, the band it landed in, how far to
     * walk and in which direction, and what becomes of the facing.
     *
     * ⚠️⚠️ S IS THE OPENER'S SENSOR RATING AND IT MUST NOT BE ZERO. A unit in hyperspace has no
     * power allocation and no EW entries at all, so this leans on getScannerOutput answering with
     * the BLUEPRINT output for such a ship (ShipSystem::getOutput returns $output + $outputMod when
     * the system is neither offline nor destroyed, and a ship with no power rows is offline nowhere).
     * If that ever stops being true, every exit falls into the roll >= 2S band and the feature
     * breaks in the shape of bad luck rather than of a bug - which is why the Stage 6 test asserts a
     * non-zero S explicitly rather than only asserting the bands.
     *
     * ⚠️ THE BAND ORDER IS THE TABLE'S, and the overlaps are deliberate. 1-3 is the 1d3 band even on
     * a ship with a huge sensor rating, and a roll of 4 on a ship with S=3 falls through to
     * "S < roll < 2S" rather than into the empty "4..S". */
    public static function rollExitDeviation($opener, $gamedata)
    {
        $turn    = (int)$gamedata->turn;
        $sensors = (int)EW::getScannerOutput($opener, $turn);
        $roll    = Dice::d(20) + self::getExitDeviationModifier($opener, $gamedata);

        return self::rollExitScatter($roll, $sensors);
    }

    /* THE TABLE ITSELF, split from the roll above so it can be EXERCISED. Everything in
     * rollExitDeviation that is not this is a die and two lookups; everything that can be
     * wrong is here, and a band that is one comparison out looks exactly like bad luck in play.
     * Given a modified roll and a sensor rating it returns the band, how far to walk and in which
     * direction, and what becomes of the facing - so a test can drive every (roll, sensors) pair
     * that matters and read the answer directly.
     *
     * public static for that reason and no other: nothing in the game calls it but its own roll. */
    public static function rollExitScatter($roll, $sensors)
    {
        $result = array(
            'sensors'      => $sensors,
            'roll'         => $roll,
            'band'         => 'precise placement',
            'distance'     => 0,
            'direction'    => Dice::d(6) - 1,   //rolled even when unused: one d6, one meaning
            'facingDelta'  => 0,
            'randomFacing' => false,
        );

        if ($roll < 1) return $result;          //the modifiers can beat the die outright

        if ($roll <= 3){
            $result['band']     = '1d3 band';
            $result['distance'] = Dice::d(3);
            return $result;
        }

        if ($roll <= $sensors){
            $result['band']     = '1d6 band';
            $result['distance'] = Dice::d(6);
            return $result;
        }

        if ($roll < 2 * $sensors){
            $result['band']     = '1d10 band';
            $result['distance'] = Dice::d(10);

            //1-2 turns the doorway one hex side left, 5-6 one right, 3-4 leaves it as declared.
            $shift = Dice::d(6);
            if ($shift <= 2)      $result['facingDelta'] = -1;
            else if ($shift >= 5) $result['facingDelta'] = 1;

            return $result;
        }

        $result['band']         = '2d10+2 band';
        $result['distance']     = Dice::d(10, 2) + 2;
        $result['randomFacing'] = true;

        return $result;
    }

    /* THE MODIFIERS, all four of them negative - a jump point exit is aimed by the fleet that is
     * already here as much as by the drive that opens it (section 2.5).
     *
     * ⭐ -5 MAKES ANCIENTS PRECISE MOST OF THE TIME AND THAT IS THE INTENT - do not "fix" it. A bare
     * Ancient is precise on 1-5 of a d20 and never worse than 1d3 until an 8; with a friendly base on
     * the map it is precise 40% of the time. A Vorlon or Shadow force arrives where it says it will,
     * while a Young race with S=10 is precise only on a natural 1.
     *
     * "ON THE MAP" is the full test - same team, not terrain, not destroyed, and actually deployed: a
     * base still waiting on a late slot is not helping anybody navigate, and neither is the opener's
     * own sister ship still in hyperspace beside it (getTurnDeployed answers 999 for one, which is
     * the section 3.2 sentinel this leans on). */
    public static function getExitDeviationModifier($opener, $gamedata)
    {
        $turn = (int)$gamedata->turn;
        $mod  = 0;

        if ($opener->faction === "Minbari Federation") $mod -= 1;  //they do this for a living
        if ((int)$opener->factionAge >= 3) $mod -= 5;              //Ancient and Primordial alike

        $base  = false;
        $elint = false;

        foreach ($gamedata->ships as $unit){
            if ($base && $elint) break;
            if ($unit->team != $opener->team) continue;
            if ($unit->isTerrain()) continue;
            if (!empty($unit->removed)) continue;
            if ($unit->isDestroyed($turn)) continue;
            if ($unit->getTurnDeployed($gamedata) > $turn) continue;

            //osat covers the MicroSAT flights too - it is the same "fixed installation" flag
            //getTurnDeployed keys its turn-1 placement off.
            if ($unit->base || $unit->osat) $base = true;
            if ($unit->isElint()) $elint = true;
        }

        if ($base)  $mod -= 3;
        if ($elint) $mod -= 1;

        return $mod;
    }

    /* THE CLAMP (section 2.5): the nearest LEGAL hex to the one the scatter rolled, searched
     * DIRECTION BEFORE DISTANCE exactly as the tabletop says - rotate alternately right and left at
     * the same distance, and only when the whole ring fails step one hex closer and try again.
     *
     * Returns array($hex, $distanceActuallyScattered), because the note and the log line must record
     * where the doorway IS, not where the dice first pointed.
     *
     * ⚠️ DISTANCE 0 IS THE LAST RESORT AND IT IS UNCONDITIONAL. The declared hex was legal when it
     * was declared, but a turn has passed since: a mine can have drifted onto it, or another player's
     * vortex opened on it. Refusing to spawn would strand the whole wave in hyperspace with their
     * berths cleared, which is far worse than a doorway sharing a hex with something - so the search
     * always terminates in a jump point, never in nothing. */
    protected static function findLegalExitHex(OffsetCoordinate $declared, $direction, $distance, $gamedata)
    {
        for ($steps = (int)$distance; $steps > 0; $steps--){
            //0 first (the direction actually rolled), then +1/-1, +2/-2, +3/-3: the whole ring,
            //nearest rotation first. +3 and -3 are the same direction; the repeat is harmless.
            foreach (array(0, 1, -1, 2, -2, 3, -3) as $delta){
                $dir = ((($direction + $delta) % 6) + 6) % 6;
                $hex = $declared->moveToDirection($dir, $steps);

                if (self::getExitHexBlock($gamedata, $hex) === null) return array($hex, $steps);
            }
        }

        return array($declared, 0);
    }

    /* ⭐ IS THIS HEX A LEGAL PLACE FOR A JUMP POINT EXIT? Null when it is, or a short reason for
     * the log when it is not - the same contract Firing's declaration tests have, because
     * Firing::getExitDeclarationBlock IS one of the two callers.
     *
     * ⚠️ SHARED ON PURPOSE, AND IT MUST STAY SHARED. The submit path asks it of the hex a player
     * NAMED and the clamp above asks it of every hex the deviation could land on. If the two ever
     * disagreed, either a declaration would be accepted onto a hex the clamp then refuses to use (so
     * the exit walks away from a legal hex for no reason at all) or the clamp would place a
     * doorway somewhere the rules say cannot hold one.
     *
     * ON THE MAP: gamespace is a "WIDTHxHEIGHT" string in which -1x-1 means "unlimited" - and
     * unlimited is not unbounded, it is the 60x40 default BuyingGamePhase::getGamespace substitutes,
     * which is what every fleet is actually deployed into.
     *
     * FREE OF OBSTRUCTIONS: the hex may hold ships, friendly or enemy - arriving units stack on it by
     * design (section 2.4) - but not any part of a Terrain unit, which is what a jump gate and a
     * vortex of either kind also are, and not an Enormous unit. Terrain is tested across its WHOLE
     * footprint, because a hex with no unit CENTRE in it can still be solid rock. */
    public static function getExitHexBlock($gamedata, OffsetCoordinate $target)
    {
        $width = 60; $height = 40;
        sscanf((string)$gamedata->gamespace, "%dx%d", $w, $h);
        if ((int)$w > 0) $width = (int)$w;
        if ((int)$h > 0) $height = (int)$h;

        if (abs($target->q) > intdiv($width, 2) || abs($target->r) > intdiv($height, 2))
            return "target hex ({$target->q},{$target->r}) is off the map";

        foreach ($gamedata->ships as $unit) {
            if (!empty($unit->removed)) continue;
            if ($unit->isDestroyed($gamedata->turn)) continue;

            if ($unit->isTerrain()) {
                foreach (RammingAttack::getTerrainOccupiedHexes($unit) as $hex) {
                    if ($hex->q == $target->q && $hex->r == $target->r)
                        return "target hex holds terrain (unit {$unit->id})";
                }
                continue; //Terrain is Enormous too - do not also run the test below on it
            }

            if ($unit->Enormous && $unit->getHexPos()->equals($target))
                return "target hex holds an Enormous unit (unit {$unit->id})";
        }

        return null;
    }

    /* ⭐ THE SCATTER NOTE - a THIRD additive note on the same engine, keyed by the same vortex id,
     * exactly like the gate's 'VortexHold' and for the identical reason (trap 8): the 'Vortex' note's
     * notevalue is "<openTurn>,<closeTurn>[,<reason>]" and that third field is FREE TEXT CONTAINING
     * COMMAS, parsed with explode(',', $v, 3). A fourth field there would silently swallow the
     * closure reason of every note in every live game.
     *
     * notevalue is "<hexes>,<facingSteps>" - two ints, so no explode limit is needed.
     *
     * WHAT READS IT: Stage 9's optional arrival initiative penalty (scatter hexes + 2 per 60 degrees
     * of facing shift), which is why it is recorded now rather than when that lands - the roll
     * happens once and cannot be re-derived afterwards. Nothing today depends on it, and
     * restoreVortexState hands it back through getVortexScatter().
     *
     * Turn 1 / phase 1 for the same reason the opening note is: getIndividualNotesForGame fetches
     * "turn <= the turn being viewed", so a note stamped with the real turn would be invisible to
     * every earlier replay turn.
     *
     * ⚠️ notekey_human is varchar(40) and an overflow is a mysqli 1406 that aborts the whole
     * submission rather than truncating (trap 7). 'VortexScatter' is 13. */
    protected function writeVortexScatterNote($opener, $vortexId, $hexes, $facingSteps, $gamedata)
    {
        $note = new IndividualNote(
            -1,
            $gamedata->id,
            1, //turn 1 / phase 1 - see above, and see spawnVortexUnit for why turn 1
            1,
            $opener->id,
            $this->id,
            $vortexId,              //notekey = the vortex's ship id, the key all three notes share
            'VortexScatter',        //notekey_human
            (int)$hexes . ',' . (int)$facingSteps
        );
        Manager::insertIndividualNote($note);
    }

    /* HOW BADLY THIS ENGINE'S CURRENT EXIT MISSED, as array('hexes' => int, 'facingSteps' => int),
     * or null when it holds no vortex or holds one that did not scatter (every ENTRANCE, and every gate).
     * Rebuilt from the 'VortexScatter' note by restoreVortexState. Stage 9 is the only intended
     * reader; it exists now because the roll happens once and cannot be recovered afterwards. */
    public function getVortexScatter()
    {
        if ($this->vortexScatterHexes === null) return null;

        return array(
            'hexes'       => (int)$this->vortexScatterHexes,
            'facingSteps' => (int)$this->vortexScatterFacingSteps,
        );
    }

    /* ONE GATE'S CONTESTED CLAIM, RESOLVED AND OPENED. See openSignalledGates for the rules.
     *
     * The gate conditions are re-tested here rather than trusted from submit time: a gate can have
     * been destroyed since the claim was made, and - more to the point - this must be IDEMPOTENT,
     * because advance() can run twice. hasOpenVortex is what makes it so, exactly as it is for a
     * ship's declaration. */
    protected function resolveGateClaims($gate, $gamedata, &$logOrders)
    {
        $turn = (int)$gamedata->turn;

        if ($this->hasOpenVortex($turn)) return;            //a gate holds ONE jump point at a time
        if ($this->isDestroyed($turn)) return;
        if ($this->isOfflineOnTurn($turn)) return;
        if ($this->getVortexRechargeLoad($turn) < $this->getVortexRechargeTime()) return;

        /* ONE CLAIM PER PLAYER, AND THE FIRST IS THE ONE THAT COUNTS - the same rule and the same
         * reason as the ship one-vortex-per-submission test in Firing (scanning for the last would
         * reject the first and keep the last, which is the wrong way round). Firing already refuses
         * a player's second claim at submit time; this is what makes the rule hold whatever else
         * reached the DB.
         *
         * THE CLAIMING PLAYER IS READ OFF targetid, which Firing::getGateSignalBlock re-derived
         * from $gamedata->forPlayer and OVERWROTE before the row was written - so it is the
         * SERVER's reckoning of who claimed, never the client's (plan section 3.3, trap 4). The
         * unit it names is used for nothing but finding its owner; the distance below is recomputed
         * from scratch. */
        /* ⭐ REINFORCEMENTS_PLAN.md STAGE 8 - EACH CLAIM CARRIES A DIRECTION AS WELL AS A
         * DURATION. damageclass 'gateexit' asks for a doorway IN (a SpawnJumpPointExit that
         * reinforcements ride out of); anything else is the Phase 2 entrance. The two travel in the
         * identical fire-order shape and are settled by the identical contest - only the WINNER's
         * flavour is ever read, because a gate holds one jump point and it points one way.
         *
         * ⚠️ A LOSING ARRIVAL CLAIM IS THE REFUND CASE (plan Stage 8). Nothing is unwound here: the
         * manifest names the GATE, the gate opens an entrance, no exit vortex ever exists for those
         * units to join on, and JumpEngine::stampArrivingReinforcements clears their berths at the
         * end of the turn. The refund is the absence of a stamp, exactly as section 3.1 designed. */
        $claims = array();   //userid => array('hold' => 1-4, 'exit' => bool)
        foreach ($this->fireOrders as $fire){
            if ((int)$fire->turn !== $turn) continue;
            if (!empty($fire->rejected)) continue;

            $mode = (int)$fire->firingMode;
            if ($mode < 1 || $mode > self::MAX_VORTEX_TURNS) continue;

            $claimant = $gamedata->getShipById((int)$fire->targetid);
            if (!$claimant) continue;                       //an order naming nothing claims nothing

            $userId = (int)$claimant->userid;
            if (isset($claims[$userId])) continue;          //first claim wins

            $claims[$userId] = array(
                'hold'     => $mode,
                'exit' => ($fire->damageclass === 'gateexit'),
            );
        }

        if (empty($claims)) return;

        /* EACH CLAIMANT'S DISTANCE, RECOMPUTED FROM THE DB. Nothing has moved since the claims were
         * made - Initial Orders is the first phase of the turn - so recomputing changes no outcome;
         * what it does is make a tampered POST pointless, because the number that settles the
         * contest is never one the client sent. */
        $distances = array();
        foreach ($claims as $userId => $claim){
            $signaller = $this->getNearestGateSignaller($gate, $gamedata, $userId);
            if (!$signaller) continue;                      //nothing in range any more - the claim lapses

            //(int) so the strict === in pickGateClaimWinner is comparing like with like. The hex
            //maths is integral, but its inputs come off mysqli as strings and PHP's / can hand back
            //a float - and a tie that fails to be recognised as one would silently skip the roll-off.
            $distances[$userId] = (int)$gate->getHexPos()->distanceTo($signaller->getHexPos());
        }

        if (empty($distances)) return;

        $winner   = self::pickGateClaimWinner($distances, $gate, $gamedata);
        $maxHold  = self::getGateMaxHold($gate);
        $hold     = min((int)$claims[$winner]['hold'], $maxHold);
        $exit = !empty($claims[$winner]['exit']);

        if (!$this->openVortexAtGate($gate, $hold, $winner, $gamedata, $logOrders, $exit)) return;

        /* THE LOSERS ARE TOLD, BY PLAYER AND BY DISTANCE (plan section 2.4). A beaten claim costs
         * nothing - no charge is spent and nothing goes on cooldown - so there is no state to
         * unwind, but silence would read as a bug. */
        foreach ($distances as $userId => $distance){
            if ($userId === $winner) continue;

            $log = self::writeVortexLogOrder($gate, $gamedata,
                " refuses the signal from " . self::playerLabel($userId, $gamedata) . ": their nearest unit is "
                . $distance . ($distance == 1 ? " hex" : " hexes") . " away, against "
                . $distances[$winner] . " for " . self::playerLabel($winner, $gamedata) . ".");
            if ($log) $logOrders[] = $log;
        }

        //A clamped claim is worth a line too - the player asked for longer, and the gate's reactor
        //damage is why they did not get it (plan section 2.5, test 18).
        if ($hold < (int)$claims[$winner]['hold']){
            $log = self::writeVortexLogOrder($gate, $gamedata,
                " cannot hold its jump point for " . (int)$claims[$winner]['hold'] . " turns - reactor damage caps it at "
                . $hold . ($hold == 1 ? " turn" : " turns") . ".");
            if ($log) $logOrders[] = $log;
        }

        Debug::log("Jump gate " . $gate->id . " (game " . $gamedata->id . ") opens for player " . $winner
            . " on turn " . $turn . " - hold " . $hold . " turn(s), " . count($distances) . " claim(s).");
    }

    /* NEAREST WINS; A TIE IS ROLLED OFF (plan section 2.4). Returns the winning userid.
     *
     * ⭐ NO OWNER PRIORITY, deliberately (user ruling 2026-08-23, superseding the earlier sketch in
     * JUMP_POINTS_PLAN.md section 7). A gate is contested ground, not a home-team asset: whoever
     * bought it has no claim on it that somebody else's unit standing closer does not beat.
     *
     * THE ROLL-OFF IS BOUNDED so it can never hang: at most 10 rounds of d100, and if the dice are
     * somehow still tied after that the lowest player id takes it. That last line will effectively
     * never run - it is there so this function has no path that does not return. */
    protected static function pickGateClaimWinner($distances, $gate, $gamedata)
    {
        $best = min($distances);

        $tied = array();
        foreach ($distances as $userId => $distance){
            if ($distance === $best) $tied[] = $userId;
        }

        if (count($tied) === 1) return $tied[0];

        for ($round = 0; $round < 10 && count($tied) > 1; $round++){
            $rolls = array();
            $high  = -1;
            foreach ($tied as $userId){
                $roll = Dice::d(100);
                $rolls[$userId] = $roll;
                if ($roll > $high) $high = $roll;

                Debug::log("Jump gate " . $gate->id . " (game " . $gamedata->id . ") claim roll-off: player "
                    . $userId . " rolls " . $roll . " at " . $best . " hexes.");
            }

            $next = array();
            foreach ($tied as $userId){
                if ($rolls[$userId] === $high) $next[] = $userId;
            }
            $tied = $next;
        }

        sort($tied);

        return $tied[0];
    }

    /* This engine's OPENING declaration for $turn, or null. Deliberately a mirror of the rules
     * Firing::getVortexDeclarationBlock enforces at submit time rather than a re-run of them: by
     * the time this is asked, an illegal order has already been marked ->rejected and never
     * reached the DB, so all that is left to do is recognise the shape.
     *
     * Modes 1-6 are the six facings (mode = facing + 1). Mode 7 is Stage 5's Maintain declaration
     * and is NOT an opening - it must never spawn a second unit, which is why it is filtered here
     * and not merely tolerated.
     *
     * ⭐ JUMP GATES (PHASE 2) - ON A GATE ENGINE THE MODE MEANS SOMETHING ELSE ENTIRELY, so the two
     * are asked as two questions and share no range test (plan section 4 Stage 1). A gate's modes
     * are the programmed OPEN DURATION, 1-4 turns; 5, 6 and 7 are not facings it happens not to use
     * but values that have no meaning at all on a gate, and mode 7 in particular must be refused
     * outright - a tampered MAINTAIN order on a gate would otherwise walk into the ship closure
     * path and take the range and power tests with it (plan section 3.3). */
    public function getVortexDeclaration($turn)
    {
        if ($this->legacyJump) return null;   //section 9 - a legacy engine never opens a vortex

        $maxMode = $this->gateJump ? 4 : 6;   //duration 1-4 on a gate; facing 1-6 on a ship

        foreach ($this->fireOrders as $fire){
            if ($fire->turn != $turn) continue;
            if (!empty($fire->rejected)) continue;

            /* ⚠️⚠️ REINFORCEMENTS_PLAN.md §3.4 - AN EXIT DECLARATION IS NOT AN ENTRANCE DECLARATION,
               and without this line it would be opened as one. A 'jumpexit' order satisfies every
               other condition in this loop by construction: it is ballistic, it is this turn's, it
               carries an x/y and its firing mode is facing+1, i.e. 1-6. So spawnDeclaredVortices -
               which runs at the end of Initial Orders and skips only terrain and destroyed ships,
               neither of which a hyperspace reinforcement is - would put a YELLOW ENTRANCE VORTEX at
               the exit hex; and hasOpenVortex would then make the Stage 6 exit sweep return
               null at spawnVortexUnit's first line, so the exit never forms at all.
               The class of vortex is decided by the SWEEP that opens it, and this method belongs to
               the entrance sweep. damageclass is the discriminator, mirroring the client. */
            if ($fire->damageclass === 'jumpexit') continue;

            $mode = (int)$fire->firingMode;
            if ($mode < 1 || $mode > $maxMode) continue;

            if ($fire->x === null || $fire->y === null || $fire->x === "null" || $fire->y === "null") continue;

            return $fire;
        }

        return null;
    }

    /* STAGE 5 - this engine's MAINTAIN declaration for $turn (firing mode 7), or null.
     *
     * Maintaining is declared exactly like opening - the Jump Engine is selected in Initial Orders
     * and the player targets a hex - except that the hex is the vortex's OWN hex, which is how the
     * client and Firing::getVortexDeclarationBlock both tell the two gestures apart. No facing is
     * involved (RAW: a vortex's facing can never be altered once it forms), so there is no facing
     * control and the mode is a flat 7.
     *
     * Read at the END of the turn, by closeVortexIfDue and by the jump-failure roll. Both run off
     * a real getTacGamedata load, so this turn's ballistic orders are on the weapon by then. */
    public function getMaintainDeclaration($turn)
    {
        if ($this->legacyJump) return null;   //section 9 - and so never maintains one either

        /* ⭐ JUMP GATES (PHASE 2) - A GATE HAS NO MAINTAIN, AND THIS IS WHERE THAT IS ENFORCED
           (JUMP_GATES_PLAN.md sections 2.3 and 3.3). The open duration is PROGRAMMED once, in the
           signal order's firing mode, and cannot be changed afterwards; there is no second
           declaration and nothing to maintain. Refusing here rather than merely leaving mode 7 off
           $firingModes is what makes that true against a TAMPERED POST as well as against the UI:
           getVortexClosureReason, rollVortexJumpFailure and the client's Maintain toggle all ask
           this method, so a mode-7 order forged onto a gate engine would otherwise put the gate on
           the ship closure path - taking its range test and its all-systems-offline test with it,
           neither of which is a gate rule. */
        if ($this->gateJump) return null;

        foreach ($this->fireOrders as $fire){
            if ($fire->turn != $turn) continue;
            if (!empty($fire->rejected)) continue;
            if ((int)$fire->firingMode !== self::MAINTAIN_MODE) continue;

            return $fire;
        }

        return null;
    }

    /* STAGE 5 - every power-absorbing system that is still ONLINE on a ship trying to MAINTAIN a
     * jump point, and so is not allowed to be (plan section 2.4). Returns display names, so the
     * caller can say which ones; an empty array means the ship is legal.
     *
     * Exempt by rule: the Scanner - and its ELINT / SW / Antiquated subclasses, which is why this
     * asks instanceof rather than matching a name - and the Jump Engine itself. The Reactor is
     * exempt because it PRODUCES power rather than drawing it.
     *
     * A powerLocked system (a deployed Kirishiac orbital's beam, say) draws power and CANNOT be
     * switched off, so it is deliberately NOT exempted: such a ship simply cannot hold a vortex
     * open until it docks the thing. The client warns about exactly this list before the commit.
     *
     * PUBLIC STATIC because the rule is about the SHIP, not about one engine: nothing here reads
     * $this, and stating it once is what keeps the end-of-turn test and any future caller (a
     * Stage 6 tooltip, Phase 2's fixed gates) from drifting apart. */
    public static function getVortexPowerViolations($ship, $turn)
    {
        $violations = array();

        foreach ($ship->systems as $system){
            if ($system instanceof JumpEngine) continue;
            if ($system instanceof Scanner) continue;
            if ($system instanceof Reactor) continue;
            if ((int)$system->powerReq <= 0) continue;      //nothing to switch off
            if ($system->isDestroyed($turn)) continue;      //draws nothing
            if ($system->isOfflineOnTurn($turn)) continue;  //already off

            $violations[] = $system->displayName;
        }

        return $violations;
    }

    /* Is this engine currently holding an OPEN vortex, as of $turn? One vortex per ship at a time
     * (plan section 2.1), and this is the test that enforces it across turns - the one inside
     * Firing::getVortexDeclarationBlock only covers a second declaration in the SAME submission.
     *
     * A closed vortex frees the engine to open another, which is why the close turn is consulted
     * rather than just the id. Stage 5's closeExpiredVortices is what sets one, and it sets it to
     * the turn the vortex closes ON - a vortex is usable for the whole of that turn, hence the
     * strictly-greater test rather than >=. */
    public function hasOpenVortex($turn)
    {
        if ($this->activeVortexId === null) return false;
        if ($this->vortexCloseTurn !== null && $this->vortexCloseTurn > -1 && $turn > $this->vortexCloseTurn) return false;
        return true;
    }

    /* ⭐ VORTEX DISRUPTOR - START THE JUMP POINT $vortexId COLLAPSING. Returns true when this
     * engine is the one holding it, so the caller can stop looking.
     *
     * THE ID IS CHECKED RATHER THAN TRUSTED. The disruptor finds its victim by HEX and then walks
     * from the vortex unit to its holder through $vortex->vortexHolderId, and a hull may carry more
     * than one Jump Engine (11 Vorlon classes do). Without this test the first engine on the ship
     * would take the flag whether or not it was the one holding the door open - and would then be
     * unable to open a jump point of its own for the rest of the game, while the real one closed
     * nothing.
     *
     * ⚠️ NO TURN ARGUMENT ON PURPOSE. The window has already been judged by the caller, against
     * the vortex UNIT (forming counts, which hasOpenVortex knows nothing about - a doorway that has
     * been declared but has not formed yet is a legal target, plan §2.3 of REINFORCEMENTS_PLAN.md).
     * Asking hasOpenVortex here as well would silently refuse exactly that case. */
    public function disruptVortex($vortexId)
    {
        if ($this->activeVortexId === null) return false;
        if ((int)$this->activeVortexId !== (int)$vortexId) return false;

        $this->vortexDisrupted = true;
        return true;
    }

    /* Is this engine's jump point collapsing? Read by the disruptor so a second shot into the same
     * hex on the same turn can report "already collapsing" instead of claiming a fresh kill. */
    public function isVortexDisrupted()
    {
        return $this->vortexDisrupted;
    }

    /* THE ENGINE HOLDING $vortex OPEN, or null. The walk is vortex -> vortexHolderId -> that ship's
     * Jump Engine whose activeVortexId is this vortex - the same join getArrivalVortex makes in the
     * other direction, and the same field the client's getVortexHeldBy has read since Stage 5.
     *
     * ⚠️ A GATE IS A SHIP HERE. restoreVortexState stamps vortexHolderId from the 'Vortex' note's
     * shipid whoever wrote it, so a fixed gate's own id is what a gate vortex carries and
     * getShipById finds the terrain unit exactly as it finds a hull. That is what lets one caller
     * disrupt both kinds without a branch.
     *
     * ⚠️ NULL IS A REAL ANSWER, not a defensive shrug: a vortex whose 'Vortex' note never arrived,
     * or whose holder's row has gone, has no engine to tell. The doorway then simply stays as it
     * was - which is the same thing getVortexClosureReason's 'vortex unit is gone' branch does from
     * the other side. */
    public static function getHoldingEngine($vortex, $gamedata)
    {
        if (!($vortex instanceof SpawnJumpPoint)) return null;
        if ($vortex->vortexHolderId === null) return null;

        $holder = $gamedata->getShipById((int)$vortex->vortexHolderId);
        if (!$holder || !is_array($holder->systems)) return null;

        foreach ($holder->systems as $system){
            if (!($system instanceof JumpEngine)) continue;
            if ($system->activeVortexId === null) continue;
            if ((int)$system->activeVortexId !== (int)$vortex->id) continue;

            return $system;
        }

        return null;
    }

    /* Put the vortex on the board. Spawn path is BallisticMineLauncher::createLoiteringMine's,
     * verbatim in shape - insertSingleShip, mark $spawned, write a deploy MovementOrder, write the
     * IndividualNote - minus its weapon-loading block, which a unit with no weapons does not need.
     * (Deliberately no SystemData work here at all: Manager::insertSystemData takes
     * SystemData::getAndPurgeAllSystemData(), and PURGING mid-advance would steal the pending
     * system data that advanceGameState flushes after its onAdvancingGamedata sweep.)
     *
     * ⚠️ Route the insert through Manager::insertSingleShip, never DBManager::submitShip: submitShip
     * returns LAST_INSERT_ID() as a STRING and getShipById compares ids with a strict ===.
     * insertSingleShip is where the cast to int lives.
     *
     * THE FACING lives in the deploy MovementOrder rather than on the unit - free persistence, free
     * rendering (ShipIcon rotates the ship sprite to movement.facing) and free replay. heading is
     * set to match so anything reading getLastMovement()->heading on an immobile unit agrees with
     * what is drawn. */
    protected function openVortex($ship, $fire, $gamedata)
    {
        $facing = ((int)$fire->firingMode - 1) % 6; //modes 1-6 -> facings 0-5; the range is already validated
        $hex    = new OffsetCoordinate($fire->x, $fire->y);

        $vortex = $this->spawnVortexUnit($ship, $hex, $facing, $gamedata);
        if (!$vortex) return;

		//An undamaged drive never fails. Same measure doHyperspaceJump uses for the boost path.
		$healthDiff = $this->maxhealth - $this->getRemainingHealth();

		$missingHealthPercentage = round(($healthDiff / $this->maxhealth) * 100);
		//Ancients have half the normal chance of Jump Engine failure. 
		if($ship->factionAge >= 3) $missingHealthPercentage = round($missingHealthPercentage / 2);		

        $distance = $ship->getHexPos()->distanceTo($vortex->getHexPos());
        self::writeVortexLogOrder($ship, $gamedata,
            " opens a jump point " . $distance . ($distance == 1 ? " hex" : " hexes")
            . " away. It forms at the end of this turn and can be entered from next turn. Chance of failure (" . $missingHealthPercentage . ").");
    }

    /* ⭐⭐ JUMP GATES (PHASE 2) - A FIXED GATE OPENS ONE OF THESE TOO, AND THE BODY IS SHARED.
     *
     * The spawn path is exactly the same three things - insert the unit, mark $spawned as
     * openTurn + 1, write the deploy MovementOrder and the 'Vortex' state note - and only the
     * INPUTS differ: a gate's hex is its own and its facing is its own (set when it was placed and
     * never chosen), so there is no projection to measure and no facing in the order.
     *
     * ⚠️ IT IS SHARED RATHER THAN COPIED FOR ONE SPECIFIC REASON (JUMP_GATES_PLAN.md section 3.4):
     * `$spawned = openTurn + 1` is the rule the whole forming/open/closing lifecycle turns on, and
     * a second copy of the spawn path is exactly how that rule drifts out of step between the two
     * kinds of vortex. Returns the vortex UNIT so each caller can write its own log sentence.
     *
     * $holder is whatever owns the jump point - the declaring ship, or the gate. Its userid, slot
     * and team are what the vortex is created with, which for a gate means the vortex belongs to
     * the gate rather than to the player who claimed it. That is deliberate: WHO won the claim is
     * recorded in the 'VortexHold' note, and it governs nothing about using the vortex - any unit
     * of any side may fly into an open one (plan section 2.6). */
    protected function spawnVortexUnit($holder, OffsetCoordinate $hex, $facing, $gamedata,
                                       $class = 'SpawnJumpPoint')
    {
        if ($this->hasOpenVortex($gamedata->turn)) return null;

        $ship = $holder;

        /* REINFORCEMENTS_PLAN.md §3.3 - THE CLASS IS A PARAMETER, and the default is what keeps both
           existing callers (openVortex and openVortexAtGate) byte-identical. The reasoning that made
           this method shared between a ship's vortex and a gate's holds for a third kind with equal
           force: `$spawned = openTurn + 1`, the deploy MovementOrder that carries the facing, and the
           'Vortex' note that restoreVortexState reads back are the same three records whichever
           direction the door faces, and they must not drift.
           The name is a LABEL and the subclass overwrites it in its own constructor, so it stays
           "Jump Point" here rather than becoming a second thing to keep in step. */
        $vortex = new $class($gamedata->id, $ship->userid, "Jump Point", $ship->slot);
        //Ships get their team from their slot on load; set it by hand so the rest of THIS request
        //agrees with what the next load will produce.
        $vortex->team = $ship->team;

        $vortexId = Manager::insertSingleShip($gamedata, $vortex, $ship->userid);
        //$spawned is the turn the unit APPEARS ON THE BOARD, which for a vortex is the turn it
        //OPENS - one after the turn it was declared. See restoreVortexFromNote for the whole rule.
        $vortex->spawned = $gamedata->turn + 1;

        $deploy = new MovementOrder(null, "deploy", $hex,
                                    0, 0, 0, $facing, $facing, false, $gamedata->turn, 0, 0);
        Manager::insertSingleMovement($gamedata->id, $vortexId, $deploy);
        $vortex->movement[] = $deploy; //so getHexPos() works on the in-memory unit for the rest of this advance

        /* THE STATE NOTE. Hung on the OPENING SHIP's Jump Engine, not on the vortex: that way it
         * survives the opener's destruction and the replay can still render the vortex's full life.
         *
         * turn 1 / phase 1, copying the mine's spawn note, because getIndividualNotesForGame fetches
         * "turn <= the turn being viewed" - a note stamped with the real open turn would be invisible
         * to any earlier replay turn, and the vortex would lose its $spawned marker there. The open
         * turn is carried in the VALUE instead, where every reader compares it against the turn on
         * hand.
         *
         * notevalue is "<openTurn>,<closeTurn or -1>". Stage 5 records a closure by APPENDING a
         * second note (there is no UPDATE path for notes, by design - insertIndividualNote refuses
         * anything that already has an id); notes load ordered by turn then phase, and
         * onIndividualNotesLoaded is last-wins per vortex id, so a phase-2 note at turn 1 overrides
         * this one cleanly.
         *
         * ⚠️ notekey_human is varchar(40) - an overflow is a mysqli 1406 that aborts the whole
         * player submission, not a truncation. "Vortex" is 6. */
        $note = new IndividualNote(
            -1,
            $gamedata->id,
            1, //see above - turn 1 so the note is loaded at every replay turn
            1,
            $ship->id,
            $this->id,
            $vortexId,          //notekey = the vortex's ship id
            'Vortex',           //notekey_human
            $gamedata->turn . ',-1'
        );
        Manager::insertIndividualNote($note);

        $this->activeVortexId = $vortexId;
        $this->vortexOpenTurn = $gamedata->turn;
        $this->vortexCloseTurn = -1;

        return $vortex;
    }

    /* ⭐⭐ JUMP GATES (PHASE 2) - OPEN THIS GATE'S JUMP POINT (JUMP_GATES_PLAN.md section 3.4).
     *
     * The sibling of openVortex above, over the same shared spawn body. THREE INPUTS DIFFER, and
     * every one of them is a rule rather than a choice:
     *
     *   THE HEX is the gate's own. A gate does not project a vortex anywhere - the doorway is the
     *   gate's mouth, which is where the gate is standing.
     *
     *   THE FACING is the gate's own, off its deploy MovementOrder, and cannot be aimed or re-aimed
     *   (plan section 2.2, user ruling 2026-08-23). It was fixed when the gate was placed. The gate
     *   itself draws NO mouth arrow - that was tried and dropped as clutter (user ruling 2026-08-24,
     *   see JumpgateCapital.php); the arrow appears on the vortex this call spawns, which carries
     *   $facingArrow, so the mouth becomes readable exactly when there is one to fly into. A unit
     *   still enters travelling in direction (facing + 3) % 6, exactly as it does into a ship's
     *   vortex - getUsableVortex and Movement::applyJumpOut need no gate branch at all.
     *
     *   THERE IS NO PROJECTION RANGE to measure, so the log line says what a gate line should say.
     *
     * ⭐ AND IT WRITES A SECOND NOTE, which is the whole of what a gate vortex needs that a ship's
     * does not: the PROGRAMMED HOLD. See writeVortexHoldNote for why it is its own note and not a
     * fourth field on the existing one.
     *
     * ⚠️ EVERY LOG LINE NAMES THE PLAYER, NEVER THE SIGNALLING UNIT (plan section 2.4 and 2.1).
     * Signalling a gate never reveals a hidden unit, and a combat-log line saying which ship sent
     * the signal would undo that on the very turn it mattered. */
    protected function openVortexAtGate($gate, $hold, $winnerUserId, $gamedata, &$logOrders,
                                        $exit = false)
    {
        $move   = $gate->getLastMovement();
        $facing = $move ? ((int)$move->facing % 6 + 6) % 6 : 0;

        /* ⭐ REINFORCEMENTS_PLAN.md STAGE 8 - THE WINNING CLAIM'S FLAVOUR PICKS THE CLASS, and that
           is the whole of the difference between a gate entrance and a gate exit on this side. Both
           are the same unit at the same hex on the same facing with the same hold note; the class is
           what makes one blue, one-way inbound and joinable by a manifest (plan section 3.3 - the
           class is the discriminator, there is no flag to keep in step).

           The DEFAULT is false so every Phase 2 caller and every existing recorded game is
           byte-identical, exactly as spawnVortexUnit's own $class default is. */
        $class = $exit ? 'SpawnJumpPointExit' : 'SpawnJumpPoint';

        $vortex = $this->spawnVortexUnit($gate, $gate->getHexPos(), $facing, $gamedata, $class);
        if (!$vortex) return false;

        $this->writeVortexHoldNote($gate, $vortex->id, $hold, $winnerUserId, $gamedata);

        /* ⚠️ THE ARRIVAL LINE SAYS "ARRIVAL", AND THAT IS A DISCLOSURE ONE TURN EARLY - deliberately.
           The gate CLAIM is secret until Initial Orders close (TacGamedata::hideSystemFireOrders
           strips every phase-1 ballistic order from every payload), but the vortex it opens is a
           public blue unit from the next turn, so the only thing this sentence brings forward is one
           turn of "somebody is coming through here". That is the identical trade section 2.3 already
           made for a ship's exit, whose blue Forming marker is public for the whole of the turn
           it was declared on - and it is what makes the log readable rather than reporting an entrance
           and then producing an exit.
           ⚠️ IT STILL NAMES ONLY THE PLAYER, never a unit and never a count. Who is coming through
           and how many stay concealed by hideHyperspaceReinforcements (section 3.6). */
        $log = self::writeVortexLogOrder($gate, $gamedata,
            " is signalled by " . self::playerLabel($winnerUserId, $gamedata) . " and opens "
            . ($exit ? "an ARRIVAL jump point" : "its jump point") . " for "
            . $hold . ($hold == 1 ? " turn" : " turns")
            . ". It forms at the end of this turn and can be "
            . ($exit ? "arrived through" : "entered") . " from next turn.");
        if ($log) $logOrders[] = $log;

        return true;
    }

    /* HOW A PLAYER IS NAMED IN A GATE LOG LINE - and it is a PLAYER, never a unit.
     *
     * ⚠️ THE WHOLE POINT (JUMP_GATES_PLAN.md sections 2.1 and 2.4). Signalling a gate never reveals
     * a hidden unit, and "the Shadow Scout signalled the gate" would undo that on the very turn it
     * mattered. Every sentence openSignalledGates writes says who CLAIMED, and nothing about which
     * of their ships was in range. Falls back to the raw id rather than to nothing, so a log line is
     * never mysteriously anonymous if a slot lookup fails. */
    protected static function playerLabel($userId, $gamedata)
    {
        foreach ($gamedata->slots as $slot){
            if ($slot->playerid == $userId && !empty($slot->playername)) return $slot->playername;
        }

        return "player " . (int)$userId;
    }

    /* ⭐ THE HOLD NOTE - a SECOND, ADDITIVE note on the same engine, keyed by the same vortex id.
     *
     * ⚠️ THE 'Vortex' NOTE'S FORMAT MUST NOT BE TOUCHED. Its notevalue is
     * "<openTurn>,<closeTurn>[,<reason>]" and the closure reason is FREE TEXT THAT CONTAINS COMMAS,
     * which is why restoreVortexState parses it with explode(',', $v, 3). Adding a fourth field
     * would silently swallow the reason on every existing note in every live game
     * (JUMP_GATES_PLAN.md trap 8). So the hold gets a note of its own, and a vortex with no such
     * note is a SHIP-opened vortex behaving exactly as it does today - which is what makes the
     * whole of Phase 2 invisible to Phase 1.
     *
     * Turn 1 / phase 1, for the same reason the opening note is: getIndividualNotesForGame fetches
     * "turn <= the turn being viewed", so a note stamped with the real open turn would be invisible
     * to every earlier replay turn.
     *
     * notevalue is "<hold>,<winning userid>". Two ints, no free text, so it needs no explode limit.
     *
     * ⚠️ notekey_human is varchar(40) and an overflow is a mysqli 1406 that aborts the whole player
     * submission, not a truncation (plan trap 7). "VortexHold" is 10. */
    protected function writeVortexHoldNote($gate, $vortexId, $hold, $winnerUserId, $gamedata)
    {
        $note = new IndividualNote(
            -1,
            $gamedata->id,
            1, //turn 1 / phase 1 - see above, and see spawnVortexUnit for why turn 1
            1,
            $gate->id,
            $this->id,
            $vortexId,              //notekey = the vortex's ship id, the same key the 'Vortex' note uses
            'VortexHold',           //notekey_human
            (int)$hold . ',' . (int)$winnerUserId
        );
        Manager::insertIndividualNote($note);
    }

    /* STAGE 6 - THE COMBAT-LOG LINE FOR A JUMP POINT OPENING OR CLOSING.
     *
     * The combat log is fire-order driven, so an event that is not a shot needs an order to hang
     * itself on. That is exactly what JumpEngine::doHyperspaceJump, Movement::applyJumpOut and
     * PhasingDrive's half-phase self-destruct all already do, and this is the same three lines:
     * a RammingAttack order at 100/100 against the ship itself, carrying the sentence to print.
     *
     * ⭐ RammingAttack, not the Jump Engine, and it matters. An order sitting on the engine's own
     * fireOrders would be indistinguishable from a DECLARATION on the next load -
     * getVortexDeclaration matches on turn plus firing mode - and firing mode 1 is a real facing.
     *
     * ⭐ damageclass 'JumpVortex' does two jobs. Firing::isHyperspaceLogOrder matches on it, so
     * the four fire-order gathers skip it instead of re-resolving it as a ram on a later turn
     * (see the note there - this is the trap Stage 4 hit). And weaponManager.doShortLogText lists
     * it, so the log prints the sentence alone rather than "firing 1x Ramming Attack ... 1/1 shots
     * hit" at a ship that was never shot at.
     *
     * No damage entry, deliberately: nothing is being hurt. */
    protected static function writeVortexLogOrder($ship, $gamedata, $pubNotes)
    {
        $rammingSystem = $ship->getSystemByName("RammingAttack");
        if (!$rammingSystem) return null;   //every ship has one, and BaseShip gives a jump gate one too

        $newFireOrder = new FireOrder(
            -1, "normal", $ship->id, $ship->id,
            $rammingSystem->id, -1, $gamedata->turn, 1,
            100, 100, 1, 1, 0,
            0, 0, 'JumpVortex', 10001
        );
        $newFireOrder->pubnotes = $pubNotes;
        $newFireOrder->addToDB = true;
        $rammingSystem->fireOrders[] = $newFireOrder;

        /* RETURNED (Phase 2) so a caller can submit exactly the orders IT wrote. spawnDeclaredVortices
         * submits by re-scanning getNewFireOrders(), which is fine as the only sweep in advance()
         * that writes any - but openSignalledGates runs immediately after it, and re-scanning there
         * would pick the ship sweep's orders up a second time and duplicate every one of them.
         * spawnDeclaredVortices ignores this return; nothing about it changed. */
        return $newFireOrder;
    }

    /* ================= STAGE 5 - THE VORTEX LIFECYCLE =============================
     *
     * THE CLOSURE SWEEP. Every open vortex whose holder has stopped meeting the conditions for
     * holding it closes at the END OF THE TURN, after Firing (plan section 2.3) - which is what
     * makes a vortex declared closed still usable for the whole of that turn. Called once, from
     * FireGamePhase::advance, immediately after Criticals::setCriticals.
     *
     * ⚠️ Destroyed ships are NOT skipped. "Its holder is destroyed" is itself a closure
     * condition, and so is "its holder flew into its own vortex", which arrives here as the same
     * thing.
     *
     * ⚠️⚠️ TERRAIN IS SKIPPED - EXCEPT A FIXED JUMP GATE, AND THAT EXCEPTION IS THE SINGLE
     * HIGHEST-CONSEQUENCE LINE IN PHASE 2 (JUMP_GATES_PLAN.md trap 1, confirmed by the user
     * 2026-08-23). This sweep is the ONLY thing that can end a jump point. A gate is terrain
     * (JumpgateCapital sets shipSizeClass 5 by hand), so the bare skip this used to carry made a
     * gate's vortex invisible to it: spawned by openSignalledGates and then open FOREVER.
     *
     * ⚠️ AND THE EXCEPTION IS NARROWED ON THE ENGINE, NEVER ON A HULL NAME (plan trap 12).
     * jumgateNew (terrain) and the civilian Jumpgate also mount a JumpEngine, are obsolete and are
     * explicitly out of scope; neither is marked with markGate(), so isGateJump() is false on both
     * and they keep the Phase 1 behaviour they have today. Ordinary terrain - asteroids, moons,
     * mines - has no JumpEngine at all and falls straight through the inner loop.
     *
     * ⚠️ Runs AFTER setCriticals, not before, so the jump-failure roll (criticalPhaseEffects)
     * has already had its say. A ship the roll has just destroyed closes its vortex on the same
     * turn, and on the OPENING turn that gives closeTurn == openTurn, which is how "it never
     * forms" is expressed - restoreVortexState then keeps the unit off the board for good. */
    public static function closeExpiredVortices($gamedata)
    {
        foreach ($gamedata->ships as $ship){
            if ($ship->isTerrain() && !self::holdsGateEngine($ship)) continue;

            foreach ($ship->systems as $system){
                if (!($system instanceof JumpEngine)) continue;

                $system->closeVortexIfDue($ship, $gamedata);
            }
        }
    }

    /* Does this unit carry a FIXED GATE's Jump Engine? The narrow test the terrain skip above uses,
     * kept as its own named thing so nothing is ever tempted to ask it by hull name instead. */
    protected static function holdsGateEngine($ship)
    {
        if (!$ship || !is_array($ship->systems)) return false;

        foreach ($ship->systems as $system){
            if ($system instanceof JumpEngine && $system->isGateJump()) return true;
        }

        return false;
    }

    /* Close this engine's vortex if any closure condition has become true, and record it.
     *
     * Idempotent three ways over: a closed vortex fails hasOpenVortex, an already-recorded closure
     * has a real $vortexCloseTurn, and a load of an earlier turn cannot reach a vortex that had
     * not been opened yet. So a double advance is a no-op - the same guarantee
     * Movement::resolveJumpOuts gives on the other side of the turn. */
    protected function closeVortexIfDue($ship, $gamedata)
    {
        if (!$this->hasOpenVortex($gamedata->turn)) return;
        if ($this->vortexCloseTurn > -1) return;
        if ($this->vortexOpenTurn === null || $this->vortexOpenTurn > $gamedata->turn) return;

        $vortex = $gamedata->getShipById((int)$this->activeVortexId);
        $reason = $this->getVortexClosureReason($ship, $vortex, $gamedata);
        if ($reason === null) return; //still held

        $this->recordVortexClosure($ship, $gamedata, $reason);
    }

    /* WHY the vortex closes this turn, or null if it survives into the next one. The list is plan
     * section 2.3's, ordered so the reason worth reporting wins: a destroyed holder is the story
     * even when it was also out of range.
     *
     * The OPENING turn is exempt from the Maintain test and from the all-systems-offline test:
     * opening a jump point costs nothing and does not stop the ship fighting (plan section 2.4).
     * It is NOT exempt from range or from destruction - a ship that opens a vortex and then runs
     * eight hexes away never gets one.
     *
     * THE FOUR-TURN CAP is FOUR TURNS OPEN, and the declaring turn is not one of them: declared on
     * N, open on N+1 through N+4, gone at the end of N+4. openTurn + MAX_VORTEX_TURNS is that last
     * turn. (Corrected 2026-08-22 - it read openTurn + 3, which cost the vortex a turn.) */
    protected function getVortexClosureReason($ship, $vortex, $gamedata)
    {
        $turn = $gamedata->turn;

        //Defensive: the note outlived its unit (deleted game data, a recycled ship id). There is
        //nothing left to hold open, so stop asking about it every turn.
        if (!($vortex instanceof SpawnJumpPoint)) return 'vortex unit is gone';

        /* ⭐⭐ VORTEX DISRUPTOR - FIRST ON THE LIST, AND THE POSITION IS THE RULE. A jump point that
           has been shot into collapses at the end of THIS turn "regardless of whether it's being
           maintained, or a jump gate that is scheduled to stay open longer" (user ruling
           2026-08-29). Every branch below this line is something that could have kept it open:

             - the EXIT branch answers null on the forming turn, which is precisely the turn a blue
               doorway is most likely to be shot at (its manifest has not come through yet);
             - the GATE branch runs on a programmed hold this rule is explicitly allowed to cut short;
             - a ship's own list is exempt from Maintain on the opening turn and honours it after.

           Put anywhere else, the disruptor would work on some jump points and silently not on
           others. The vortex unit test above stays ahead of it only because there is nothing left
           to collapse when the unit itself is gone.

           The UNITS in the doorway are not killed here - VortexDisruptor::fire does that at the
           moment of the hit, where the to-hit margin it needs for the ancient-drive escape roll
           still exists. This branch is only the doorway's own fate. */
        if ($this->vortexDisrupted) return 'disrupted by a Vortex Disruptor';

        /* ⭐ REINFORCEMENTS_PLAN.md §2.3 - AN EXIT IS ONE-SHOT, and this is the whole of that
           rule. Forms at the end of the turn it was declared (N), the manifest arrives through it in
           the Deployment phase of N+1, and it closes at the end of N+1. Nothing on a ship's closure
           list below applies: there is no Maintain to declare, no range to keep (the opener is in
           hyperspace and has no hex to measure from), and the four-turn cap is longer than its whole
           life.

           ⚠️ TRAP 5 - IT MUST REACH THIS METHOD AND IT MUST CLOSE. spawnVortexUnit opens with
           `if ($this->hasOpenVortex(...)) return null;`, so an exit that never closes locks the
           unit that opened it out of ever opening anything - including an entrance to leave the battle
           by - for the rest of the game.

           ⭐⭐ STAGE 8 REVERSED THE ORDERING THIS BRANCH WAS WRITTEN WITH, and the reversal is a
           RULE, not a tidy-up. Stage 7 put this before the gate branch on the reasoning that
           exit-ness belongs to the VORTEX while gate-ness belongs to the ENGINE, so the one-shot
           rule should win. Section 0 says otherwise, and it is the user ruling that governs: A
           GATE'S EXIT RUNS FOR THE GATE'S PROGRAMMED HOLD (1-4 turns), with a fresh wave allowed
           on each of them. Left as it was, a gate signalled for a 4-turn arrival would have slammed
           shut after one - silently, and looking exactly like a working feature.

           So ONE-SHOT IS A SHIP'S RULE. A gate falls through to getGateVortexClosureReason below,
           which is where the hold lives, and which already closes a gate vortex of either flavour.
           Trap 5 is unaffected: a gate's engine is released by the hold expiring, and a gate that
           never closed its jump point would be broken for Phase 2 entrances too. */
        if ($vortex instanceof SpawnJumpPointExit && !$this->isGateJump()){
            return ($turn > (int)$this->vortexOpenTurn) ? 'reinforcements have arrived' : null;
        }

        /* ⭐⭐ JUMP GATES (PHASE 2) - THE GATE BRANCH, TAKEN FIRST AND RETURNING, because a gate's
           list is SHORTER than a ship's and the difference is entirely things that do NOT close a
           gate's jump point (JUMP_GATES_PLAN.md section 2.3, user rulings 2026-08-23):

             NOT "not maintained" - a gate has no Maintain. The duration is programmed once, when
               the gate is signalled, and cannot be changed afterwards.
             NOT "systems left online" - a gate does not have to go dark to hold its OWN jump point
               open. getVortexPowerViolations is never consulted for one.
             NOT "holder is N hexes away" - the gate IS the hex. And the SIGNALLER may fly away, be
               destroyed, or leave through the vortex itself; once signalled, the gate holds it.
               There is no end-of-turn range recheck of any kind.
             NOT the four-turn cap - the programmed hold replaces it, and it may be shorter.

           What is left is: the unit is gone (above), the gate is destroyed, or the programmed hold
           has run out. Closure is still END OF TURN, after Firing, so a vortex closing this turn is
           usable for the whole of it. */
        if ($this->isGateJump()) return $this->getGateVortexClosureReason($ship, $gamedata);

        if ($ship->isDestroyed($turn)){
            return $ship->hasJumpedToHyperspace() ? 'holder left through a vortex' : 'holder destroyed';
        }

        if ($turn >= $this->vortexOpenTurn + self::MAX_VORTEX_TURNS) return 'four-turn limit reached';

        $distance = $ship->getHexPos()->distanceTo($vortex->getHexPos());
        if ($distance > $this->range) return 'holder is ' . $distance . ' hexes away';

        if ($turn == $this->vortexOpenTurn) return null; //the turn it was declared - nothing more to ask

        if (!$this->getMaintainDeclaration($turn)) return 'not maintained';

        $violations = self::getVortexPowerViolations($ship, $turn);
        if (!empty($violations)){
            //Capped: notevalue is varchar(4096), but a log line naming forty weapons helps nobody.
            $named = array_slice($violations, 0, 6);
            if (count($violations) > count($named)) $named[] = '+' . (count($violations) - count($named)) . ' more';
            return 'systems left online: ' . implode('; ', $named);
        }

        return null;
    }

    /* ⭐ WHY A FIXED GATE'S JUMP POINT CLOSES THIS TURN, or null if it stands into the next one.
     * The whole of the gate list - see the branch in getVortexClosureReason for what is deliberately
     * NOT on it, which is most of the ship rules.
     *
     * ⚠️ (int) ON EVERY TURN COMPARISON. $gamedata->turn is a STRING out of mysqli (Phase 1
     * trap 10) while $vortexOpenTurn is int-cast when the note is parsed, and the expiry test below
     * is exactly the kind of comparison that silently comes out a turn wrong.
     *
     * THE OFF-BY-ONE, stated once so it is not re-derived: the turn a gate is signalled is NOT one
     * of the open turns. Signalled on N, open on N+1 through N+hold, gone at the end of N+hold - the
     * same shape as a ship's four-turn cap, with the programmed hold in place of the constant. */
    protected function getGateVortexClosureReason($gate, $gamedata)
    {
        $turn = (int)$gamedata->turn;

        //Total reactor loss destroys the gate outright (plan section 2.5), so this covers that too -
        //isDestroyed is what the reactor rule ends in.
        if ($gate->isDestroyed($turn)) return 'jump gate destroyed';

        //A hold note that never arrived: defensive only. Fall back to the ship cap rather than
        //holding the door open forever, which is the failure mode trap 1 exists to prevent.
        $hold = ($this->vortexHoldTurns !== null) ? (int)$this->vortexHoldTurns : self::MAX_VORTEX_TURNS;

        if ($turn >= (int)$this->vortexOpenTurn + $hold){
            return 'programmed ' . $hold . '-turn hold expired';
        }

        return null;
    }

    /* Write the closure down. There is NO UPDATE PATH for individual notes, by design
     * (insertIndividualNote refuses anything that already has an id), so a closure is recorded by
     * APPENDING a second note for the same vortex id at turn 1 / phase 2 - notes load ordered by
     * turn then phase, and restoreVortexState is last-wins PER VORTEX ID, so the phase-2 note
     * overrides its own phase-1 opening note and nobody else's.
     *
     * ⚠️ The reason is free text and can contain commas, so notevalue is parsed with an
     * explode LIMIT of 3 on the way back in. Do not tidy that limit away.
     *
     * ⚠️ notekey_human is varchar(40) - an overflow is a mysqli 1406 that aborts the whole
     * player submission, not a truncation. 'Vortex' is 6, and it MUST stay the same string the
     * opening note uses or onIndividualNotesLoaded will not recognise this as a vortex note. */
    protected function recordVortexClosure($ship, $gamedata, $reason)
    {
        $note = new IndividualNote(
            -1,
            $gamedata->id,
            1, //turn 1 / phase 2 - see above, and see openVortex for why turn 1
            2,
            $ship->id,
            $this->id,
            $this->activeVortexId,
            'Vortex',
            $this->vortexOpenTurn . ',' . $gamedata->turn . ',' . $reason
        );
        Manager::insertIndividualNote($note);

        $this->vortexCloseTurn   = $gamedata->turn;
        $this->vortexCloseReason = $reason;

        /* STAGE 6 - the reason reaches the PLAYER, not just fieryvoid.log. It is the only part of
         * this feature a player can act on ("systems left online: Heavy Laser; ...") and it was
         * Stage 5's first reported gap. FireGamePhase::advance submits new fire orders after this
         * sweep, so the order needs no submit of its own - unlike the opening one. */
        self::writeVortexLogOrder($ship, $gamedata,
            " loses its jump point at the end of this turn - " . $reason . ".");

        /*Debug::log("Jump vortex " . $this->activeVortexId . " (opened turn " . $this->vortexOpenTurn
            . " by ship " . $ship->id . ", game " . $gamedata->id . ") closes at the end of turn "
            . $gamedata->turn . " - " . $reason . ".");*/
    }

    /* Rebuild this engine's vortex state from the 'Vortex' notes it has written, and put each
     * vortex unit itself into the state its note describes. Called from onIndividualNotesLoaded,
     * so it runs on every load, for the live game and for every replay turn alike.
     *
     * ⭐ $spawned IS THE TURN THE VORTEX OPENS, WHICH IS openTurn + 1 - NOT the declaration turn.
     * shipManager.shouldBeHidden hides any unit whose spawned turn is later than the turn being
     * viewed, and on the FORMING turn (the turn it was declared) the vortex is deliberately not on
     * the board at all: the player sees the yellow "Jump Point Forming" ballistic hex and its
     * facing arrow instead, and the unit itself appears the turn it can actually be entered (user
     * ruling 2026-08-21, plan section 2.3). It has to be restored from the note because tac_ship
     * has no column for it - exactly how a mid-game mine works.
     *
     * $removed / $removedTurn is the closure half (Stage 5 writes the close turns; nothing does
     * yet). Removal, not destruction: the vortex vanishes from the board on the turns after it
     * closed while staying correct in the replay of the turns it was open.
     *
     * ⚠️ removedTurn is closeTurn + 1, i.e. the first turn the vortex is GONE - a vortex stays
     * usable for the whole of the turn it closes on (plan section 2.3). Keeping the two fields on
     * the same "first turn this is true" footing is also what keeps the born-and-removed-same-turn
     * rule honest: shouldBeHidden and ReplayAnimationStrategy both hide a unit outright when
     * spawned >= removedTurn, which here reduces to openTurn >= closeTurn - true only for a vortex
     * that closed on the very turn it was declared (a Stage 5 jump-failure), which never formed and
     * should indeed never be drawn. Use closeTurn itself and an ordinary unmaintained vortex - the
     * common case, open one turn - would vanish from its own replay.
     *
     * ⭐ STAGE 5 - ONE NOTE PER VORTEX, NOT ONE PER ENGINE. A closed vortex frees its engine to
     * open another (hasOpenVortex), so over a long game one engine accumulates several vortices'
     * worth of notes, plus a second phase-2 note for each one that closed. Notes arrive ordered by
     * turn then PHASE and every vortex note is stamped turn 1, so a naive last-note-wins loop
     * would read A open (p1), B open (p1), A closed (p2) - and leave the engine believing its
     * CURRENT vortex is the long-dead A. Keying by vortex id and then picking the LATEST-OPENED
     * one is what makes the two orderings independent.
     *
     * $vortexNotes is keyed by vortex ship id, last-wins per key - which is how a phase-2 closure
     * note overrides its own opening note. */
    protected function restoreVortexState($vortexNotes, $gamedata, $holdNotes = array())
    {
        $this->activeVortexId    = null;
        $this->vortexOpenTurn    = null;
        $this->vortexCloseTurn   = -1;
        $this->vortexCloseReason = '';
        $this->vortexHoldTurns   = null;
        $this->vortexClaimantId  = null;
        //A disruption describes the CURRENT vortex, and this method is what decides which one that
        //is. Ordering makes it academic in practice (loads happen before firing, never after), but
        //leaving one of the per-vortex fields out of the reset is how the next one drifts.
        $this->vortexDisrupted   = false;

        foreach ($vortexNotes as $note){
            //LIMIT 3: the closure reason is free text and can contain commas.
            $parts     = explode(',', (string)$note->notevalue, 3);
            $openTurn  = (int)$parts[0];
            $closeTurn = isset($parts[1]) ? (int)$parts[1] : -1;
            $reason    = isset($parts[2]) ? (string)$parts[2] : '';

            /* THE UNIT half, applied for EVERY vortex this engine has ever opened - not just the
             * current one - so a replay turn renders each of them in the state it was in then.
             * (int): notekey is a varchar column, so mysqli hands it back as a STRING, and
             * getShipById's fallback loop compares with a strict ===. */
            $vortex = $gamedata->getShipById((int)$note->notekey);
            if ($vortex instanceof SpawnJumpPoint){ //instanceof, not a null test: ship ids get recycled
                $vortex->spawned        = $openTurn + 1;
                $vortex->vortexHolderId = (int)$note->shipid; //who may MAINTAIN it - read by the client

                if ($closeTurn > -1 && $gamedata->turn > $closeTurn){
                    $vortex->removed     = true;
                    $vortex->removedTurn = $closeTurn + 1;
                }
            }

            /* THE ENGINE half. Only ONE vortex can be this engine's current one, and because a ship
             * may hold only one at a time that is always the latest-opened of the notes. A vortex
             * opened on a LATER turn than the one being viewed does not exist yet as far as this
             * load is concerned - which matters in replay, where every vortex note, whatever turn
             * its vortex belongs to, is visible from turn 1 onwards. */
            if ($openTurn > $gamedata->turn) continue;
            if ($this->vortexOpenTurn !== null && $openTurn < $this->vortexOpenTurn) continue;

            $this->activeVortexId    = (int)$note->notekey;
            $this->vortexOpenTurn    = $openTurn;
            $this->vortexCloseTurn   = $closeTurn;
            $this->vortexCloseReason = $reason;
        }

        /* ⭐ JUMP GATES (PHASE 2) - THE HOLD, keyed by the SAME vortex id, applied AFTER the loop
         * above has settled which vortex is the current one (plan section 3.4).
         *
         * A vortex with no hold note is a SHIP-opened vortex and these two stay null, which is what
         * makes every gate branch downstream invisible to Phase 1. Ordering matters: the current
         * vortex is only known once the latest-opened note has won, so the lookup cannot ride
         * inside the loop.
         *
         * notevalue is "<hold>,<winning userid>" - two ints, no free text, so no explode limit is
         * needed here (unlike the 'Vortex' note, whose third field is a reason full of commas). */
        if ($this->activeVortexId !== null && isset($holdNotes[(string)$this->activeVortexId])){
            $parts = explode(',', (string)$holdNotes[(string)$this->activeVortexId]->notevalue);

            $this->vortexHoldTurns  = max(1, (int)$parts[0]);
            $this->vortexClaimantId = isset($parts[1]) ? (int)$parts[1] : null;
        }
    }

    /* The programmed open duration of the jump point this engine is holding, or null when it holds
     * none - or when the one it holds is a SHIP'S, which runs on the four-turn cap and the Maintain
     * declaration instead. See $vortexHoldTurns. */
    public function getVortexHoldTurns()
    {
        return $this->vortexHoldTurns;
    }

    public function isOverloading($turn){
        foreach ($this->power as $power){
            if ($power->turn == $turn && $power->type == 2){
                return true;
            }
        }
        return false;
    }

	public function doHyperspaceJump($ship, $gamedata)
	{
		$reactorList = $ship->getSystemsByName('Reactor', true);
		foreach($reactorList as $reactorCurr){     //Don't do Hyperspace jump for ships that have blown their own reactors!
			if($reactorCurr->isDestroyed()) return;
		}

		$primaryStruct = $ship->getStructureSystem(0); //If ship is otherwise destroyed also don't jump.
		if($primaryStruct->isDestroyed()) return;

		//The Jump Engine itself (and the section it sits on) must still be intact THIS turn.
		//getSystemsByName's isDestroyed() filter only treats a section-mounted system as gone the
		//turn AFTER its structure dies (B5W "falls off next turn" rule), so a Jump Engine whose host
		//section was destroyed during this same fire phase would otherwise still complete the jump.
		//Check directly: if the engine has no health left, or its host structure was destroyed this
		//turn, the jump fails outright (no functioning drive = no jump).
		if($this->getRemainingHealth() <= 0) return;
		$hostStruct = $ship->getStructureSystem($this->location);
		if($hostStruct && $hostStruct->isDestroyed($gamedata->turn)) return;

		$currHealth = $this->getRemainingHealth();
		$maxhealth = $this->maxhealth;
		$healthDiff = $maxhealth - $currHealth;
	
		// Calculate the percentage of health missing
		$missingHealthPercentage = round(($healthDiff / $maxhealth) * 100);

		// Roll a D100
		$d100Roll = Dice::d(100);

		/* ⭐ SOME DRIVES DO NOT GET A ROLL AT ALL. getCertainJumpFailureNote returns the log line
		   to write when this turn's jump is doomed outright and null when the percentage above is
		   what decides it; only the Shadow Phasing Drive overrides it (see that class). Asked
		   BEFORE the roll rather than instead of it so the d100 is still consumed either way -
		   Dice draws are part of the game's random sequence and a rule that silently skipped one
		   would make otherwise-identical games diverge. */
		$certainFailureNote = $this->getCertainJumpFailureNote($ship, $gamedata);

		// Determine if the jump fails
		$jumpFailure = ($certainFailureNote !== null) || ($missingHealthPercentage > 0 && $d100Roll <= $missingHealthPercentage);

		// Try to create the fire order for logs
		$rammingSystem = $ship->getSystemByName("RammingAttack");
		$fireOrderType = $jumpFailure ? 'JumpFailure' : 'HyperspaceJump';
		if ($certainFailureNote !== null){
			$pubNotes = $certainFailureNote;
		}else{
			$pubNotes = $jumpFailure
				? " attempts to jump to hyperspace, but damage to the Jump Drive causes the ship to be destroyed (" . $missingHealthPercentage . "% chance of failure)."
				: " successfully jumps to hyperspace (" . $missingHealthPercentage . "% chance of failure).";
		}
	
		if ($rammingSystem) {
			$newFireOrder = new FireOrder(
				-1, "normal", $ship->id, $ship->id,
				$rammingSystem->id, -1, $gamedata->turn, 1,
				100, 100, 1, 1, 0,
				0, 0, $fireOrderType, 10001
			);
			$newFireOrder->pubnotes = $pubNotes;
			$newFireOrder->addToDB = true;
			$rammingSystem->fireOrders[] = $newFireOrder;
		}

		//Create note BEFORE we destroy the primary structure, so CV is not automatically zeroed.
		if($fireOrderType == 'HyperspaceJump'){										
			$notekey = 'jumped';
			$noteHuman = 'jumped';
			$noteValue = $ship->calculateCombatValue();
			$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gamedata->turn,$gamedata->phase,$ship->id,$this->id,$notekey,$noteHuman,$noteValue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue
		}		

		// Destroy the primary structure in either event
		$primaryStruct = $this->unit->getStructureSystem(0);
		if ($primaryStruct) {
			$remaining = $primaryStruct->getRemainingHealth();
			$damageEntry = new DamageEntry(
				-1, $ship->id, -1, $gamedata->turn,
				$primaryStruct->id, $remaining, 0, 0, -1, true, false,
				"", $fireOrderType
			);
			$damageEntry->updated = true;
			$primaryStruct->damage[] = $damageEntry;
	
			if ($rammingSystem) {
				// Add extra data to damage entry
				$damageEntry->shooterid = $ship->id;
				$damageEntry->weaponid = $rammingSystem->id;
			}
		}

	}

	/* HOOK - IS THIS TURN'S BOOST-TO-HYPERSPACE JUMP DOOMED REGARDLESS OF THE DICE?
	 *
	 * Returns the public combat-log line to write when it is, or null to let doHyperspaceJump's
	 * ordinary missing-health percentage roll decide - which is the answer for every drive in the
	 * tree except the Shadow Phasing Drive, whose override is the only reason this exists.
	 *
	 * Deliberately returns the NOTE rather than a bool: a rule that overrides the roll also has to
	 * override the "(N% chance of failure)" sentence, which would otherwise report a percentage
	 * that had nothing to do with the outcome. One return value carries both halves and there is
	 * no way to supply one without the other. */
	protected function getCertainJumpFailureNote($ship, $gamedata){
		return null;
	}

	public function onIndividualNotesLoaded($gamedata){
		/* STAGE 3 - two KINDS of note hang on a Jump Engine, so the type has to be read rather than
		   assumed. 'Vortex' notes rebuild the jump-point state; everything else falls through to
		   the pre-existing behaviour below, which is the 'jumped' note.

		   STAGE 5 - vortex notes are COLLECTED here and applied in one pass afterwards, keyed by
		   the vortex's ship id. Notes arrive ordered by turn then phase, so this map is last-wins
		   PER VORTEX, which is how a phase-2 closure note overrides its own phase-1 opening note
		   without a later vortex's opening note getting in between. restoreVortexState explains
		   why that distinction is load-bearing. */
		$vortexNotes = array();

		/* ⭐ JUMP GATES (PHASE 2) - a THIRD kind, collected in the same pass and keyed the same way.
		   A 'VortexHold' note carries a FIXED GATE's programmed open duration and the player who
		   won the claim; a vortex with none is a ship's and behaves exactly as it did before Phase 2
		   (JUMP_GATES_PLAN.md section 3.4). It is a separate note rather than a fourth field on the
		   'Vortex' one because that note's third field is a free-text closure reason CONTAINING
		   COMMAS, and widening its format would silently swallow the reason on every existing note
		   in every live game (plan trap 8). */
		$holdNotes = array();

		/* ⭐ REINFORCEMENTS STAGE 6 - a FOURTH kind, collected in the same pass and keyed the same
		   way. A 'VortexScatter' note carries how far an EXIT missed the hex it was aimed at,
		   for Stage 9's arrival initiative penalty; a vortex with none never scattered.

		   ⚠️ IT ALSO HAS TO BE RECOGNISED HERE OR IT IS A SILENT BUG. The fall-through below treats
		   any note it does not know as the legacy 'jumped' note and assigns its value to
		   $preJumpValue - so an unclaimed scatter note would quietly overwrite the pre-jump combat
		   value of the ship that opened the exit. */
		$scatterNotes = array();

		foreach ($this->individualNotes as $currNote) {
			if ($currNote->notekey_human === 'Vortex'){
				$vortexNotes[(string)$currNote->notekey] = $currNote;
				continue;
			}

			if ($currNote->notekey_human === 'VortexHold'){
				$holdNotes[(string)$currNote->notekey] = $currNote;
				continue;
			}

			if ($currNote->notekey_human === 'VortexScatter'){
				$scatterNotes[(string)$currNote->notekey] = $currNote;
				continue;
			}

			//Insert the noteValue (e.g. combatValue when ship jumped) in appropriate variable
			$this->preJumpValue = $currNote->notevalue;
		}

		$this->restoreVortexState($vortexNotes, $gamedata, $holdNotes);

		//AFTER restoreVortexState, never before: which vortex is the CURRENT one is settled in
		//there, and the scatter is a property of that one vortex - the same ordering the hold note
		//needs, and for the same reason.
		$this->restoreVortexScatter($scatterNotes);
	}//endof onIndividualNotesLoaded

	/* ⭐ REINFORCEMENTS STAGE 6 - the scatter half of the note restore, keyed by vortex ship id
	   exactly as the other two are.

	   ⚠️ IT RESETS AS WELL AS APPLIES, and must: onIndividualNotesLoaded runs on EVERY load, a
	   system object outlives one, and a field that is only ever assigned to is how a closed
	   vortex's scatter ends up reported against the next one this engine opens.

	   Only the CURRENT vortex's note is applied - a long game leaves one note per vortex this
	   engine has ever opened, and restoreVortexState has just decided which of them is live. */
	protected function restoreVortexScatter($scatterNotes)
	{
		$this->vortexScatterHexes = null;
		$this->vortexScatterFacingSteps = null;

		if ($this->activeVortexId === null) return;
		if (!isset($scatterNotes[(string)$this->activeVortexId])) return;

		//"<hexes>,<facingSteps>" - two ints, so no explode limit is needed (unlike the 'Vortex'
		//note, whose third field is a closure reason full of commas).
		$parts = explode(',', (string)$scatterNotes[(string)$this->activeVortexId]->notevalue);

		$this->vortexScatterHexes = max(0, (int)$parts[0]);
		$this->vortexScatterFacingSteps = isset($parts[1]) ? (int)$parts[1] : 0;
	}


	/* ================= STAGE 5 - THE END-OF-TURN JUMP-FAILURE ROLL ================
	 *
	 * A DAMAGED Jump Drive may not survive holding a vortex open (plan section 2.6). The ship rolls
	 * at the end of every turn on which it opened a vortex or maintained one; d100 <= the
	 * percentage of engine boxes lost destroys it outright, down the existing JumpFailure path -
	 * same damageclass, same log-order shape, so HangarOps' JumpFailure handling (no d20 escape
	 * roll, every docked craft dies with the ship) picks it up unchanged.
	 *
	 * WHY HERE. criticalPhaseEffects is Pass 2 of Criticals::setCriticals, which runs inside
	 * FireGamePhase::advance after all fire has resolved - so the roll sees the damage the engine
	 * took THIS turn - and before Pass 3's processCarrierDestructionEscapes, which is what reads
	 * the JumpFailure entry this may write. Pass 2 iterates the ships that were alive when
	 * setCriticals started, which is also exactly the population that should be rolling: a ship
	 * already destroyed by fire, or one that flew out through its own vortex during Movement, has
	 * no jump left to fail.
	 *
	 * The vortex itself is closed by closeExpiredVortices, which runs after setCriticals and reads
	 * the destruction this may have caused as its 'holder destroyed' condition. On the OPENING turn
	 * that gives closeTurn == openTurn, i.e. a vortex that never forms - which is the rule. */
	public function criticalPhaseEffects($ship, $gamedata)
	{
		parent::criticalPhaseEffects($ship, $gamedata);

		/* ⭐ JUMP GATES (PHASE 2): total reactor loss destroys the gate (plan section 2.5). FIRST,
		   before the roll below: a gate already dead of reactor loss has nothing left to lose to a
		   failed jump, and the failure path's own guard (primary Structure already destroyed) then
		   makes the pair mutually exclusive without either needing to know about the other. */
		if ($this->gateJump) $this->destroyGateOnReactorLoss($ship, $gamedata);

		/* ⭐ AND THE JUMP-FAILURE ROLL IS KEPT FOR GATES (user ruling 2026-08-23 - it was offered as
		   an exemption and deliberately not taken). A gate with a damaged Jump Engine rolls on the
		   turn it opens a vortex, exactly as a ship does; with no Maintain declaration to make, the
		   guard inside already gives precisely one roll per opening and needs no gate branch.
		   ⚠️ JumpgateCapital's engine has TEN boxes, so each point of damage on it is a flat 10%
		   chance of destroying the whole gate the next time it is signalled. */
		$this->rollVortexJumpFailure($ship, $gamedata);
	}

	protected function rollVortexJumpFailure($ship, $gamedata)
	{
		//(int) THROUGHOUT, and it is load-bearing: TacGamedata::setTurn stores what mysqli handed
		//it, which is a STRING, while $vortexOpenTurn is cast to int when the note is parsed. A
		//strict comparison between the two is false for the same turn, which silently skips the
		//roll on the very turn a vortex is opened - the commonest case there is.
		$turn = (int)$gamedata->turn;

		//Only a ship that OPENED a vortex this turn, or is MAINTAINING one this turn, is asking
		//anything of its jump drive. A vortex simply left to expire costs no roll.
		if (!$this->hasOpenVortex($turn)) return;
		if ((int)$this->vortexOpenTurn !== $turn && !$this->getMaintainDeclaration($turn)) return;

		/* ⚠️⚠️ REINFORCEMENTS - A SHIP'S JUMP POINT EXIT NEVER ROLLS, AND THAT IS A RULE
		   (REINFORCEMENTS_PLAN.md Stage 6: "an exit never rolls for jump failure ... Deliberate -
		   do not 'fix' it into existence"). A damaged drive costs a reinforcement nothing.

		   ⚠️ IT USED TO FALL OUT FOR FREE AND NO LONGER DOES. Until 2026-08-29 the exit was spawned
		   at the END OF THE FIRING PHASE, i.e. after this roll, so on the opening turn there was
		   simply no vortex here to ask about. Moving the spawn up to the end of Initial Orders (so
		   both players can see where the doorway landed) put one in front of it - and its opener is
		   a unit sitting in HYPERSPACE, which pre-battle damage can perfectly well have damaged.
		   Without this line such a wave would start being destroyed before it ever reached the map.

		   ⭐ THE GATE IS EXEMPTED FROM THE EXEMPTION, exactly as it is in getVortexClosureReason: a
		   fixed gate rolls on the turn it opens a jump point of EITHER flavour (user ruling
		   2026-08-23, offered as an exemption and deliberately not taken).

		   After the two cheap tests above, so the ship lookup is only paid when a roll is pending. */
		if (!$this->gateJump){
			$openVortex = $gamedata->getShipById((int)$this->activeVortexId);
			if ($openVortex instanceof SpawnJumpPointExit) return;
		}

		//An undamaged drive never fails. Same measure doHyperspaceJump uses for the boost path.
		$healthDiff = $this->maxhealth - $this->getRemainingHealth();
		if ($healthDiff <= 0) return;

		//Already gone this turn - by fire, by ramming, by its own half-phase - so there is nothing
		//left to destroy and no second log line worth writing.
		$primaryStruct = $ship->getStructureSystem(0);
		if (!$primaryStruct || $primaryStruct->isDestroyed($turn)) return;

		$missingHealthPercentage = round(($healthDiff / $this->maxhealth) * 100);
		//Ancieents have half the normal chance of Jump Engine failure. 
		if($ship->factionAge >= 3) $missingHealthPercentage = round($missingHealthPercentage / 2);

		if (Dice::d(100) > $missingHealthPercentage) return; //held

		//try to make an actual attack to show in the log - use the Ramming Attack system, exactly
		//as doHyperspaceJump and PhasingDrive do. damageclass 'JumpFailure' is what
		//Firing::isHyperspaceLogOrder matches on, so the four fire-order gathers skip it instead of
		//re-resolving it as a ram on later turns.
		$rammingSystem = $ship->getSystemByName("RammingAttack");
		if ($rammingSystem){
			$newFireOrder = new FireOrder(
				-1, "normal", $ship->id, $ship->id,
				$rammingSystem->id, -1, $turn, 1,
				100, 100, 1, 1, 0,
				0, 0, 'JumpFailure', 10001
			);
			$newFireOrder->pubnotes = " loses control of its jump vortex - damage to the Jump Drive destroys the ship ("
				. $missingHealthPercentage . "% chance of failure).";
			$newFireOrder->addToDB = true;
			$rammingSystem->fireOrders[] = $newFireOrder;
		}

		$remaining = $primaryStruct->getRemainingHealth();
		$damageEntry = new DamageEntry(
			-1, $ship->id, -1, $turn,
			$primaryStruct->id, $remaining, 0, 0, -1, true, false,
			"", 'JumpFailure'
		);
		$damageEntry->updated = true;
		if ($rammingSystem){ //extra data, so the damage entry can be tied back to the log order
			$damageEntry->shooterid = $ship->id;
			$damageEntry->weaponid  = $rammingSystem->id;
		}
		$primaryStruct->damage[] = $damageEntry;

		//Read by PhasingDrive::criticalPhaseEffects, which runs its half-phase self-destruct AFTER
		//this and would otherwise destroy the same ship a second time in the same phase.
		$this->vortexFailureApplied = true;

		//Debug::log("Jump vortex failure: ship " . $ship->id . " (game " . $gamedata->id . ") destroyed"
		//	. " at the end of turn " . $turn . " - " . $missingHealthPercentage . "% chance of failure.");
	}

	//True when the roll above has just destroyed this ship. Protected read for PhasingDrive.
	protected function hasAppliedVortexFailure(){
		return $this->vortexFailureApplied;
	}

	public function hasJumped() {		
		$ship = $this->getUnit();

		//Check damage entries, and remove Hyperspace jump entry, to see if ship was 'destroyed' by jumping not actual damage.	    
		$struct = $ship->getStructureSystem(0);       
        $maxHealth = $struct->maxhealth;
        $totalDamage = 0;
        foreach ($struct->damage as $entry) {
            if ($entry->damageclass !== 'HyperspaceJump') $totalDamage += max(0, $entry->damage - $entry->armour);  //Only count non-jump damage, as jumping destroys ship anyway.
		}
             
        if($totalDamage < $maxHealth) return true; //The other damage sustained has not destroyed this ship, jumping has.
		
        return false;
	}   	

	public function getCVBeforeJump() {		
        return $this->preJumpValue;
	}   	

     public function setSystemDataWindow($turn){
        /* SECTION 9 - a legacy engine gets the boost-era tooltip back, verbatim from before Stage 2
           (git d50c41929^). Describing the vortex rules on a system that cannot open one is worse
           than saying nothing: every sentence of the text below is an instruction the player cannot
           carry out. ShipSystem rather than Weapon for the same reason the vortex branch uses it -
           Weapon's block is a gun's Damage / Fire control / Priority rows, all zero here - and with
           no "Weapon type" or "Range" row either, because this engine targets nothing. The client's
           own Weapon constructor sets data["Weapon type"] from $weaponClass on load, so no caller
           that reads it can find it undefined. */
        if ($this->legacyJump){
            $this->data["Special"]  = "<br>Boost in Initial Orders to jump to hyperspace at end of turn.";
            $this->data["Special"] .= "<br>WARNING - Jumping to hyperspace REMOVES ship from rest of the battle.";
            $this->data["Special"] .= "<br>If Jump Engine is damaged, ship has a % chance of being destroyed opening jump point.";
            $this->data["Special"] .= "<br>SHOULD NOT be shut down for power (unless damaged >50% or if Desperate rules apply).";
            ShipSystem::setSystemDataWindow($turn);
            return;
        }

        /* ⭐ JUMP GATES (PHASE 2) STAGE 5 - A FIXED GATE GETS ITS OWN TEXT, AND IT MUST.
           Almost every sentence of the ship version below is WRONG on a gate and would read as an
           instruction the player cannot carry out - which is exactly the argument the legacy branch
           above makes. A gate is not selected and does not target: it is CLICKED, from the Initial
           Orders tooltip, with no ship selected. Its vortex opens on its own hex with its own fixed
           facing, so there is nothing to aim, no 10-hex projection (the 10 is a SIGNAL range) and no
           line of sight. And it has no Maintain at all. */
        if ($this->gateJump){
            $this->setGateSystemDataWindow($turn);
            return;
        }

        /* STAGE 6 - the tooltip describes the VORTEX rules end to end. It used to describe the
           retired boost-to-jump method, and then Stage 5's half-way version; this is the whole
           thing, in the order a player meets it. */
        $recharge = max(1, (int)$this->delay);

        $this->data["Special"]  = "<br>Select this system in Initial Orders and target a hex within " . $this->range . " hexes.";
        $this->data["Special"] .= "Set the vortex FACING with the on-map arrow, then confirm. The jump point forms at the end of that turn and can be entered from the NEXT turn.";
        $this->data["Special"] .= "<br>A damaged Jump Engine may fail: at the end of every turn it opens or maintains a jump point, the ship is destroyed on a d100 roll at or under the percentage of Jump Engine boxes lost.";
        $this->data["Special"] .= "<br>See FAQ for full rules for Jump Drives.";
        $this->data["Special"] .= "<br>SHOULD NOT be shut down for power (unless damaged >50% or if Desperate rules apply).";
		/* ShipSystem, not parent. Weapon::setSystemDataWindow appends a gun's tooltip block -
		   Damage, Fire control, Resolution Priority - which would be meaningless (and mostly zero)
		   on a jump engine. Same idiom LCVRail uses two classes up. Plan Stage 6 rewrites this
		   whole Special text once the vortex lifecycle is complete.

		   The two entries that ARE wanted are set by hand. "Weapon type" is not cosmetic:
		   weaponManager.targetHex reads data["Weapon type"] to stamp the fire order's damageclass
		   and would throw on undefined, so without this line the declaration cannot be made at all. */
		ShipSystem::setSystemDataWindow($turn);
		$this->data["Weapon type"] = $this->weaponClass;
		$this->data["Range"] = $this->range;
    }

    /* ⭐ THE FIXED GATE TOOLTIP (JUMP_GATES_PLAN.md Stage 5). Its own text, not a variant of the
     * ship one, because on a gate almost every sentence of that text is an instruction the player
     * cannot carry out - the same argument the legacy branch makes, and the reason section 0 of the
     * plan carried this as a known gap from Stage 1.
     *
     * WHAT IT SAYS THAT THE SHIP TEXT DOES NOT:
     *   - the gesture is CLICK THE GATE, with no ship selected and none needed;
     *   - the 10 hexes are a SIGNAL range - how far away YOUR nearest unit may be - not a
     *     projection range, and no line of sight is required for it;
     *   - the facing cannot be chosen: it is the gate's own, fixed when the gate was placed, and
     *     the arrow on the map is how you read it;
     *   - the duration is PROGRAMMED once, 1-4 turns, and there is no Maintain;
     *   - a contested gate goes to the NEAREST claimant, with a roll-off for a tie;
     *   - the damage model is the Reactor, and it is three numbers.
     *
     * ⚠️ NO LIVE NUMBERS IN HERE, AND THAT IS NOT AN OVERSIGHT. ShipSystem::stripForJson does not
     * send $data at all, so this text reaches the client on the STATIC BLUEPRINT - generated once
     * at build time, on an undamaged hull, on turn 1. A "charge: now 7/20" line would therefore be
     * frozen at whatever the generator saw and would read as a lie for the rest of the game. The
     * LIVE charge is on the system icon (turnsloaded/loadingtime, which stripForJson does send);
     * this text states the RULES, and the damage paragraph says how damage moves them. */
    protected function setGateSystemDataWindow($turn)
    {
        //Undamaged values on purpose - see above. $delay is the ship file's own argument, so this
        //is the gate's base recharge whatever hull it is mounted on.
        $recharge = max(1, (int)$this->delay);
        $maxHold  = self::MAX_VORTEX_TURNS;

        /*$this->data["Special"]  = "<br><b>SIGNALLING THE GATE.</b> In Initial Orders, CLICK THE GATE - no ship needs to be";
        $this->data["Special"] .= " selected. The button is offered if you have any live unit within " . $this->range . " hexes of it;";
        $this->data["Special"] .= " which unit does not matter, and NO line of sight is needed. Signalling never reveals a";
        $this->data["Special"] .= " stealthed, shaded or cloaked unit. ANY player may signal ANY gate, including one the enemy bought.";
        $this->data["Special"] .= "<br><b>THE DURATION.</b> Set how many turns to hold the jump point open - 1 to " . $maxHold;
        $this->data["Special"] .= " on an undamaged gate - and press SIGNAL. It cannot be changed afterwards: there is no Maintain. The jump point";
        $this->data["Special"] .= " forms at the end of that turn and can be entered from the NEXT turn.";
        $this->data["Special"] .= "<br><b>THE FACING CANNOT BE CHOSEN.</b> The vortex always takes the GATE'S OWN facing, set when";
        $this->data["Special"] .= " the gate was placed. The arrow drawn over the gate is its mouth: a unit must be TRAVELLING";
        $this->data["Special"] .= " INTO that side on the step that carries it into the hex, then press Jump to Hyperspace.";
        $this->data["Special"] .= " Movement ends there and the unit leaves the battle keeping its full combat value.";
        $this->data["Special"] .= "<br><b>CONTESTED GATES.</b> If several players signal the same gate in one turn, the one whose";
        $this->data["Special"] .= " nearest unit is CLOSEST wins - the owner has no priority - and an exact tie is rolled off.";
        $this->data["Special"] .= " The winner's duration is used; the losers lose nothing but the turn's claim.";
        $this->data["Special"] .= "<br><b>RECHARGE.</b> Opening a jump point spends the gate's whole charge. It recharges from the";
        $this->data["Special"] .= " turn after that jump point closes, 1 per turn, and cannot be signalled again until it reads";
        $this->data["Special"] .= " " . $recharge . "/" . $recharge . " on this system's icon.";
        $this->data["Special"] .= "<br><b>DAMAGE.</b> The gate's condition is its REACTOR. Every 3 points of damage on it adds a";
        $this->data["Special"] .= " turn to the recharge, every 15 points costs a turn off the longest hold, and losing the";
        $this->data["Special"] .= " reactor entirely destroys the gate.";
        $this->data["Special"] .= "<br>A DAMAGED Jump Engine may fail: at the end of a turn the gate opens a jump point, the gate";
        $this->data["Special"] .= " is destroyed on a d100 roll at or under the percentage of Jump Engine boxes lost.";*/

		$this->data["Special"]  = "<br>Gate can be signalled to open by any unit within " . $this->range . " hexes";
        $this->data["Special"] .= "<br>Set how many turns to hold the jump point open - 1 to " . $maxHold . ". It cannot be changed afterwards and the jump point forms at the end of that turn and can be entered from the NEXT turn.";
        $this->data["Special"] .= "<br>The vortex always takes the gate's OWN facing.";
        $this->data["Special"] .= "<br>The gate's condition is its REACTOR. Every 3 points of damage on it adds a turn to the recharge, every 15 points costs a turn off the longest hold.";
        $this->data["Special"] .= "<br>A Jump Engine may fail: at the end of a turn the gate opens a jump point, the gate is destroyed on a d100 roll at or under the percentage of Jump Engine boxes lost";

        /* ShipSystem, not parent - same reason as the ship branch: Weapon's block is a gun's
           Damage / Fire control / Priority rows, all meaningless here.

           "Weapon type" is not cosmetic: the client stamps a fire order's damageclass from it.
           "Range" is relabelled, because on a gate the number means something else entirely - it is
           how far away the SIGNALLER may be, not how far the vortex can be thrown. */
        ShipSystem::setSystemDataWindow($turn);
        $this->data["Weapon type"]  = $this->weaponClass;
        $this->data["Signal range"] = $this->range;
    }

	public function calculateHitBase($gamedata, $fireOrder)
		{
			$fireOrder->needed = 100; //always true
			$fireOrder->updated = true;			
		}              

    public function fire($gamedata, $fireOrder)
    {
	        $fireOrder->rolled = 0; //To prevent animationa nd dispaly in Comabt Log 
	}	

    /* STAGE 6 - THE PAYLOAD CARRIES TWO SEPARATE THINGS, BECAUSE THEY ARE TWO SEPARATE THINGS.
     *
     * 1. THE LOADING STATE, in $turnsloaded / $loadingtime, which every other weapon in the game
     *    uses for exactly one purpose and which now means the same here: how charged the drive is,
     *    out of the recharge time the ship file passed as $delay (see the constructor). Sent from
     *    getVortexRechargeLoad rather than from the stored value so it cannot drift out of step
     *    with the vortex note - and $loadingtime is sent explicitly because Weapon::stripForJson
     *    does not send it, so it would otherwise come off a STATIC BLUEPRINT that may predate this
     *    change.
     *
     *    ⭐ It also does the job Stage 5's counter was doing by accident: weaponManager.isLoaded
     *    is `loadingtime <= turnsloaded`, so a recharging engine reads as NOT LOADED and drops out
     *    of weaponManager.targetHex's weapon sweep and off the fire-order buttons. While a vortex
     *    stands the charge is 0, which is the one-vortex-per-ship rule; after it closes the count
     *    climbs, which is the recharge rule. Neither has to be restated in the UI.
     *
     * 2. THE VORTEX COUNTER, in $vortexTurnsOpen / $vortexMaxTurns, sent only while a jump point
     *    actually stands. The system icon shows this INSTEAD of the loading pair for as long as it
     *    is present (SystemIcon.getText via JumpEngine.getVortexIconLoad), so the player sees
     *    "2/4 turns open" while it matters and "7/16 charged" the rest of the time.
     *    Emitted only when there is a vortex, so every other load is a byte-for-byte no-change. */
    public function stripForJson(){
        $strippedSystem = parent::stripForJson();

        /* ⭐ THE ONE ENGINE THAT SENDS THE ORDINARY WEAPON PAYLOAD AND NOTHING ELSE IS THE TREK
           NACELLE (user ruling 2026-08-29 - see $hasJumpRecharge). Its 4th constructor argument is
           an IMPULSE RATING, not a jump delay, so getVortexRechargeLoad would answer with a charge
           derived from a number that is not a recharge time at all; markLegacy(false) set
           loadingtime/turnsloaded to 1/1 and this keeps them there.

           ⚠️ THIS USED TO TEST $legacyJump, AND THAT WAS THE BUG. Stage 9 gave every legacy drive a
           way to phase IN, so a Phasing Drive / Hyperdrive / FTL Drive spends and recovers its
           charge exactly as a B5 Jump Engine does - and short-circuiting here left all of them
           drawing a flat 1/1 while the real state moved underneath. The vortex counter block below
           is right for them too: a phase-in doorway is a vortex, invisible or not, and its holder's
           icon should count it. */
        if (!$this->hasJumpRecharge) return $strippedSystem;

        $turn = (int)TacGamedata::$currentTurn;   //(int): mysqli hands the turn back as a STRING

        $strippedSystem->turnsloaded = $this->getVortexRechargeLoad($turn);
        /* getVortexRechargeTime() rather than the raw max(1, (int)$this->delay) this used to send:
           the method IS that expression on a ship engine, so a ship's payload is byte-identical, and
           on a FIXED GATE it adds the reactor-damage term. Sending the undamaged number instead
           would leave the client's weaponManager.isLoaded test disagreeing with the server's own
           charge test in Firing::getGateSignalBlock - the Signal button would be offered and the
           claim then rejected, which is the worst of both. */
        $strippedSystem->loadingtime = $this->getVortexRechargeTime();

        $age = $this->getVortexAge($turn);
        if ($age !== null){
            $strippedSystem->vortexTurnsOpen = $age;
            /* ⭐ JUMP GATES (PHASE 2): a gate's jump point runs for the duration PROGRAMMED when it
               was signalled, not for the four turns a ship's may be maintained to - so the counter
               the system icon draws has to be out of the HOLD. $vortexHoldTurns is null on a
               ship-opened vortex, which is what keeps this byte-identical for Phase 1.
               ⚠️ Sent per instance from live state, never mirrored onto the system as a flag - two
               gates in one game must not read each other's hold (plan trap 9). */
            $strippedSystem->vortexMaxTurns  = ($this->vortexHoldTurns !== null)
                ? (int)$this->vortexHoldTurns
                : self::MAX_VORTEX_TURNS;
        }

        return $strippedSystem;
    }
}


class Structure extends ShipSystem{
    public $name = "structure";
    public $displayName = "Structure";
	private $isIndestructible = false;

	public $orbitalBump = 0; //portion of maxhealth contributed by docked Kirishiac Orbitals this load - excluded from combat value so a docked orbital's boxes aren't counted twice

	//Structure is last to be repaired, except purely cosmetic systems like Hanngars
	public $repairPriority = 2;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired

    function __construct($armour, $maxhealth, $isIndestructible = false){
        parent::__construct($armour, $maxhealth, 0, 0);
		$this->isIndestructible = $isIndestructible;
    }

	//Stage S (S-d): the client reads maxhealth from the STATIC blueprint, not the
	//per-instance JSON — so a server-side maxhealth reduction (integrated-fighter
	//structure-box loss, applied in Hangar::onIndividualNotesLoaded) would desync
	//the client's remaining-health/destruction math. Send the LIVE maxhealth so the
	//client matches whenever boxes have been permanently lost. (Harmless for every
	//other ship: it just re-sends the unchanged blueprint value.)
	//
	//CANONICAL sender of live Structure maxhealth — unconditional and enhancement-
	//agnostic, so it stays correct even if a future effect (not just SHAD_FTRL) lowers
	//structure maxhealth. The old SHAD_FTRL case in Enhancements::addSystemEnhancementsForJSON
	//that also sent it is now a no-op; don't re-add a maxhealth send there.
	public function stripForJson(){
		$strippedSystem = parent::stripForJson();
		$strippedSystem->maxhealth = (int)$this->maxhealth;
		return $strippedSystem;
	}

	//creates pre-tagged Outer Structure, with appropriate arc
	//warning: has trouble working if Structure isn't directly called earlier! so be sure to create PRIMARY Structure before trying to go for any Outer ones :)
	public static function createAsOuter($armour, $maxhealth, $startArc, $endArc, $isIndestructible = false){
		$createdStruct = new Structure($armour, $maxhealth, $isIndestructible);
		$createdStruct->startArc = $startArc;
		$createdStruct->endArc = $endArc;
		$createdStruct->addTag('Outer Structure');
		return $createdStruct;
	}
		
	//Vree need Structure that doesn't fall off even if it's destroyed - here it is!
	//it will get destroyed all right (possibly multiple times in a battle), but will still be there afterwards
	//Will be destroyed if all such Structures are reduced to 0 (and then all of them will get destroyed !)
	//upon destruction - delete destruction marker
	public function criticalPhaseEffects($ship, $gamedata)
    { 
    
		parent::criticalPhaseEffects($ship, $gamedata);//Call parent to apply effects like Limpet Bore.	    
		
		if($this->isIndestructible){
			foreach ($this->damage as $damage ) if(($damage->turn == $gamedata->turn) && ($damage->destroyed)){ 
				/* update 19.12.2024 - re-read the rules - such structures NEVER fall off!
				//check all others - if all of them are reduced to 0 - mark them destroyed as well; if not, delete destroyed marker!
				$structures = $ship->getSystemsByName('Structure', true);
				$allDestroyed = true;
				foreach($structures as $struct){
					if(($struct->isIndestructible) && ($struct->getRemainingHealth() > 0)) {
						$allDestroyed = false;
						break;
					}
				}
				if($allDestroyed){//actually do mark them so!
					foreach($structures as $struct) if( $struct->isIndestructible && (!$struct->isDestroyed())){
						$damageEntry = new DamageEntry(-1, $damage->shipid, -1, $damage->turn, $struct->id, 0, 0, 0, $damage->fireorderid, true, false, "Structure falls off", $damage->damageclass, $damage->shooterid, $damage->weaponid);
						$damageEntry->updated = true;
						$struct->damage[] = $damageEntry;
					}
				}else{*/ //unmark this one
					$damage->destroyed = false;
				/*}*/
			}
		}
    } //endof function criticalPhaseEffects	

	/* JUMP_POINTS_PLAN.md Stage 4 - the combat value a unit had when it left through a vortex.
	   Mirror of JumpEngine's preJumpValue/getCVBeforeJump pair, for units that have no jump
	   engine to hang the note on: any unit may use any open jump point, including an enemy's
	   (plan section 2.5), and CV preservation has to follow it there.
	   Movement::applyJumpOut picks the host - jump engine when the unit has one, primary
	   Structure when it does not - so exactly one of the two carries the note.

	   Private: nothing here reaches the client (BaseShip already sends the finished combatValue)
	   and a public property would be written into every Structure entry of every static
	   blueprint - plan section 8's default-value cost. */
	private $preJumpValue = 0;

	public function onIndividualNotesLoaded($gamedata){
		foreach ($this->individualNotes as $currNote){
			if ($currNote->notekey === 'jumped') $this->preJumpValue = $currNote->notevalue;
		}

		//Clear as the base implementation does - once reacted to, notes serve no further purpose.
		$this->individualNotes = array();
	}

	public function getCVBeforeJump(){
		return $this->preJumpValue;
	}

} //endof Structure	

/* Kirishiac Orbital - a weapon platform that floats above its section when DEPLOYED and
 * attaches to the hull when DOCKED (undeployed). Full rules in KIRISHIAC_ORBITALS_PLAN.md.
 * - Deployed: hittable via the section chart ('Kirishiac Orbital' rows) or a fighter-style
 *   called shot; every hit rolls the Orbital Hits sub-chart (1-6 paired beam, 7-20 orbital);
 *   beam overkill spills into the orbital, orbital overkill is LOST (flash still re-rolls);
 *   the paired beam cannot be powered down.
 * - Docked: untargetable, orbital chart rolls divert to Structure; the orbital's remaining
 *   boxes merge into the section Structure's maxhealth; the beam is stowed (no fire/intercept,
 *   may be powered down); after 5 complete docked turns orbital+beam fully regenerate
 *   (tracked by the OrbitalRepairing marker crit; aborted if the structure block dies).
 * - Dock/Deploy is ordered in the Firing Phase (client Dock/Deploy buttons -> notes) and takes
 *   effect NEXT turn; the Deployment-phase choice sets the scenario-start state immediately.
 *   No initiative or maneuvering restrictions apply in either direction. */
class KirishiacOrbital extends ShipSystem{
	public $name = "KirishiacOrbital";
//    public $displayName = "Orbital";
	public $primary = false;
	public $repairPriority = 0; //DEPLOYED default: SelfRepair cannot reach a deployed orbital; docking makes it serviceable (priority 3, beam 6 - set on notes-load)
	public $hitChartName = "Orbital"; //ship hit chart alias - displayName stays 'Orbital A'..'H' (getSystemsByNameLoc matches either)
	public $hasSystemHitChart = true; //informational: orbital resolves hits on its own sub-chart (rolled in resolveSubHitChart)
	public $systemHitChart = array(); //sub-chart bands, ship-chart convention (highest d20 roll => band): 'Weapon' = paired beam, 'Self Repair' = attached repair system (Heavy), anything else = the orbital itself

	private $pairing = null;
	protected $pairedWeapon= null;
	protected $attachedSelfRepair = null; //Heavy Orbital only: its own Self Repair system (sub-chart band + destruction coupling)

	public $targetProfile = 8; //flat defence profile ("targeted as if they were fighters"): 8 standard, 7 Light, 10 Heavy - replaces the ship's bearing profile on called shots, no called-shot penalty
	public $canRegenerate = true; //Heavy Orbitals are too large to regenerate - they carry their own Self Repair instead
	public $subChartWhileDocked = false; //Heavy Orbitals: docked hits still roll the Orbital chart (weapon/self-repair bands strike those systems); standard orbitals fold every docked roll into the Structure block

	protected $active = false; //true = DOCKED as last ORDERED (latest note, incl. an order given this firing phase); orbitals start deployed
	public $activeEffective = false; //docked state in EFFECT this turn (a firing-phase order only kicks in next turn)
	public $turnsDocked = 0; //docked-turn ordinal of the current turn (0 while deployed; regeneration completes when it reaches 5)

	private $transferReceived = false; //client sent a dock/deploy choice this request (guards POST-side note writes)
	private $appliedStructureBump = 0; //how much this orbital added to its structure block's maxhealth on this load (docked merge)

	function addOrbitalWeapon($pairedWeapon){ //Function used to assign the paired antigravity beam on the orbital
		$this->pairedWeapon = $pairedWeapon;
		$pairedWeapon->linkedOrbital = $this; //back-reference: overkill routing + stowed/power state
		$pairedWeapon->isTargetable = false; //"called shots may not be made on orbitals or weapons attached to them"
		$pairedWeapon->repairPriority = 0; //DEPLOYED default - SelfRepair may service the beam only while docked (priority 6, set on notes-load)
		if ($this->structureHomeLocation !== null) $pairedWeapon->structureHomeLocation = $this->structureHomeLocation; //beam docks to the same block
	}

	/*declutter support: the orbital (and its beam) may be DISPLAYED on the left/right section
	while still docking to the front/aft structure block - destruction coupling, docked merge,
	regeneration and SelfRepair all follow the home block, not the display section*/
	public function setStructureHome($location){
		parent::setStructureHome($location);
		if ($this->pairedWeapon !== null) $this->pairedWeapon->structureHomeLocation = $location;
	}

	public function getOrbitalWeapon(){
		return $this->pairedWeapon;
	}

	/*the orbital's associated structure block. $this->structureSystem is only assigned in
	onConstructed, which TacGamedata runs AFTER notes are loaded (DBManager::getTacGamedata:
	getTacShips -> notes -> onConstructed) - so anything running at notes-load time must
	resolve the block through the unit instead.*/
	protected function getStructureBlock(){
		if ($this->structureSystem !== null) return $this->structureSystem;
		$ship = $this->getUnit();
		return ($ship !== null) ? $ship->getStructureSystem($this->getStructureLocation()) : null;
	}

	function __construct($armour, $maxhealth, $orientation, $pairing, $profileAdjust, $systemHitChart){ //$orientation is L, R, or C - regarding graphics,
	// $profileAdjust is LEGACY and ignored: the flat defence profile is $targetProfile (class default - standard 8, Light 7, Heavy 10).
		$this->pairing = $pairing;
		$this->systemHitChart = $systemHitChart;
		$this->displayName = 'Orbital ' . $pairing . '';
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
		if ( $maxhealth == 0 ){
			$maxhealth = 18;
		}

		$this->iconPath = "KirishiacOrbital".$orientation."1.png";
		parent::__construct($armour, $maxhealth, 0, 0);
		//arcs deliberately left (0,0): addSystem stamps the HOME structure block's section arc
		//(via getStructureLocation) - an orbital can only be hit from the same directions as its
		//associated structure. NOTE: setStructureHome() must be called BEFORE addXSystem in
		//blueprints, or the DISPLAY section's arc would be stamped instead.
	}

	/*hit allocation: any hit landing on the orbital (section chart row, flash, or a called shot)
	rolls the Orbital Hits sub-chart while deployed. While DOCKED:
	- standard orbital: the whole orbital (weapon stowed inside) is part of the hull - every
	  roll is treated as a hit on the combined Structure block;
	- HEAVY orbital ($subChartWhileDocked): "any hits resolved to hitting the heavy weapon
	  orbital use the heavy weapon orbital hit location chart as normal" - the weapon and
	  self-repair bands strike those systems even while docked (their overkill then flows back
	  into the ship normally); only orbital-structure results hit the combined Structure instead.
	Consumed from BaseShip::getHitSystem.*/
	public function resolveSubHitChart(){
		$docked = $this->activeEffective;
		if ($docked && !$this->subChartWhileDocked){ //DOCKED standard orbital - treat any orbital hit as a Structure hit
			return $this->orbitalStructureResult(true);
		}
		//roll on the sub-chart (ship-chart convention: key = highest roll of the band)
		$chart = $this->systemHitChart;
		if (!is_array($chart) || count($chart) == 0) return $this->orbitalStructureResult($docked);
		ksort($chart);
		$roll = Dice::d(20);
		foreach ($chart as $maxRoll => $band){
			if ($roll <= $maxRoll){
				if ( (STRCASECMP($band,'Weapon')==0) || (STRCASECMP($band,'Antigravity Beam')==0) ){ //weapon band (legacy blueprint name accepted)
					if ($this->pairedWeapon !== null) return $this->pairedWeapon; //may be destroyed - overkill routing then folds the hit onwards (deployed: into the orbital; docked: back to the ship)
				}
				if ( (STRCASECMP($band,'Self Repair')==0) && ($this->attachedSelfRepair !== null) ){ //Heavy Orbital: its own Self Repair system
					return $this->attachedSelfRepair;
				}
				return $this->orbitalStructureResult($docked); //any other band ('Orbital'; legacy 'Structure') = the orbital's structure
			}
		}
		return $this->orbitalStructureResult($docked);
	}

	/*the 'orbital structure' chart result: the orbital itself while deployed, the combined
	Structure block while docked (the orbital's boxes are merged into it)*/
	private function orbitalStructureResult($docked){
		if (!$docked) return $this;
		$block = $this->getStructureBlock();
		return ($block !== null) ? $block : $this;
	}

	/*overkill on a destroyed DEPLOYED orbital is lost entirely (flash re-rolls before this hook
	fires); while docked the orbital cannot be hit at all, so default flow applies*/
	public function getOverkillDestination($target){
		if (!$this->activeEffective) return false; //deployed - excess dissipates into space
		return null;
	}

	//Deployed: an orbital destroyed takes its mounted weapon with it.
	//Docked: abort regeneration if the structure block is lost; complete it after 5 full turns.
	public function criticalPhaseEffects($ship, $gamedata)
	{
		parent::criticalPhaseEffects($ship, $gamedata);
		$beam = $this->pairedWeapon;

		if (!$this->activeEffective){ //DEPLOYED
			if ($this->isDestroyed() && $beam !== null && !$beam->isDestroyed()){
				$beamHealth = $beam->getRemainingHealth();
				$damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $beam->id, $beamHealth, 0, 0, -1, true, false, "Orbital destroyed - Antigravity Beam lost", "OrbitalLoss");
				$damageEntry->updated = true;
				$beam->damage[] = $damageEntry;
			}
			return;
		}

		//DOCKED
		$block = $this->getStructureBlock();
		if ($block !== null && $block->isDestroyed()){
			//associated structure block lost - orbital dies with it and can never regenerate;
			//expire the marker so a post-mortem regeneration can never fire
			$this->cancelRegenerationCrit($gamedata);
			return;
		}
		if ($this->canRegenerate && $this->turnsDocked >= 5){ //5 complete turns docked - full restoration (no-op if pristine)
			$this->performRegeneration($ship, $gamedata);
		}
	}

	/*full restoration of orbital + paired beam: all damage erased (including this turn's),
	destroyed markers lifted, lingering criticals expired. Runs in the critical phase, so
	post-firing state is what gets healed; the OrbitalRepairing marker expires via its turnend.*/
	private function performRegeneration($ship, $gamedata){
		$targets = array($this);
		if ($this->pairedWeapon !== null) $targets[] = $this->pairedWeapon;
		foreach ($targets as $sys){
			$totalDamage = $sys->getTotalDamage();
			if ( ($totalDamage > 0) || $sys->isDestroyed() ){
				$damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $sys->id, -$totalDamage, 0, 0, -1, false, true, 'Orbital Regeneration', 'OrbitalRegen');
				$damageEntry->updated = true;
				$sys->damage[] = $damageEntry;
			}
			foreach ($sys->criticals as $crit){ //clear lingering criticals, except the regen marker itself
				if ($crit->phpclass == 'OrbitalRepairing') continue;
				if ( ($crit->turnend == 0) || ($crit->turnend >= $gamedata->turn) ){
					$crit->turnend = $gamedata->turn;
					$crit->forceModify = true;
					$crit->updated = true;
				}
			}
		}
	}

	private function hasClearableCrits($sys, $turn){
		foreach ($sys->criticals as $crit){
			if ($crit->phpclass == 'OrbitalRepairing') continue;
			if ( ($crit->turnend == 0) || ($crit->turnend >= $turn) ) return true;
		}
		return false;
	}

	/*is a regeneration clock already running? (a still-live OrbitalRepairing marker) - used to
	avoid stacking a second marker when a mid-dock destruction wants to (re)start regeneration*/
	private function hasActiveRegenerationCrit($gameData){
		foreach ($this->criticals as $crit){
			if ($crit->phpclass != 'OrbitalRepairing') continue;
			if ( ($crit->turnend == 0) || ($crit->turnend >= $gameData->turn) ) return true;
		}
		return false;
	}

	/*docking just became effective: start the 5-turn regeneration clock (visible marker crit;
	skipped when there is nothing to regenerate)*/
	private function startRegeneration($ship, $gameData){
		if (!$this->canRegenerate) return; //Heavy Orbital: too large to regenerate (has its own Self Repair instead)
		if ($this->hasActiveRegenerationCrit($gameData)) return; //already regenerating - don't stack a second marker
		$beam = $this->pairedWeapon;
		$needsRepair = ($this->getTotalDamage() > 0) || $this->isDestroyed() || $this->hasClearableCrits($this, $gameData->turn);
		if (!$needsRepair && $beam !== null){
			$needsRepair = ($beam->getTotalDamage() > 0) || $beam->isDestroyed() || $this->hasClearableCrits($beam, $gameData->turn);
		}
		if (!$needsRepair) return; //pristine orbital - nothing to regenerate, no marker needed
		$crit = new OrbitalRepairing(-1, $ship->id, $this->id, "OrbitalRepairing", $gameData->turn + 1, $gameData->turn + 5);
		$crit->updated = true;
		$crit->newCrit = true;
		$this->criticals[] = $crit;
	}

	private function cancelRegenerationCrit($gamedata){
		foreach ($this->criticals as $crit){
			if ($crit->phpclass != 'OrbitalRepairing') continue;
			if ( ($crit->turnend != 0) && ($crit->turnend < $gamedata->turn) ) continue; //already over
			$crit->turnend = $gamedata->turn;
			$crit->forceModify = true;
			$crit->updated = true;
		}
	}

	/*undocking just became effective (end-of-turn advance): stop regeneration; and if the merged
	structure block carries more damage than its remaining boxes account for, the excess was
	soaked by THIS orbital's merged boxes - it leaves with the orbital (D3 ruling: block max
	while docked = base + remaining health of each docked orbital; overkill past that total is
	handled by the normal overkill workflow when the block dies)*/
	private function finishUndocking($ship, $gameData){
		$this->cancelRegenerationCrit($gameData);

		$block = $this->getStructureBlock();
		if ($block === null || $this->appliedStructureBump <= 0) return;
		//withdraw this orbital's boxes from the block's pool (recomputed from scratch next load)
		$block->maxhealth -= $this->appliedStructureBump;
		$block->orbitalBump = max(0, $block->orbitalBump - $this->appliedStructureBump);
		$this->appliedStructureBump = 0;
		if ($block->isDestroyed()) return; //block already gone - damage stays where it fell
		$overflow = $block->getTotalDamage() - $block->maxhealth; //damage beyond own boxes (+ any still-docked orbitals')
		if ($overflow <= 0) return;
		$transfer = min($overflow, max(0, $this->getRemainingHealth()));
		if ($transfer <= 0) return;
		$destroysOrbital = ($transfer >= $this->getRemainingHealth());
		$orbEntry = new DamageEntry(-1, $ship->id, -1, $gameData->turn, $this->id, $transfer, 0, 0, -1, $destroysOrbital, false, 'Damage absorbed while docked', 'OrbitalUndock');
		$orbEntry->updated = true;
		$this->damage[] = $orbEntry;
		$blockEntry = new DamageEntry(-1, $ship->id, -1, $gameData->turn, $block->id, -$transfer, 0, 0, -1, false, false, 'Damage carried away by undocking Orbital', 'OrbitalUndock');
		$blockEntry->updated = true;
		$block->damage[] = $blockEntry;
	}

	/*deploy guard: would withdrawing this orbital's merged boxes leave the structure block
	with no remaining structure? The block has been living off the orbital's docked boxes -
	deploying would collapse the section, so such an order is refused (the client hides the
	Deploy button too, but damage arriving after the order makes this server check authoritative)*/
	public function undockingWouldBreachBlock(){
		if ($this->appliedStructureBump <= 0) return false; //no merge in effect - undocking changes nothing
		$block = $this->getStructureBlock();
		if ($block === null || $block->isDestroyed()) return false; //block already gone - nothing left to protect
		return ( ($block->maxhealth - $this->appliedStructureBump - $block->getTotalDamage()) <= 0 );
	}

	public function doIndividualNotesTransfer(){
		//client sends the ordered docking state (1 = docked, 0 = deployed) with Deployment (phase -1)
		//and Firing (phase 3) submissions; absence means "no orbital input in this request" - never
		//write a note then (POST-side ships have no loaded notes; a default write would clobber state)
		if (is_array($this->individualNotesTransfer) && count($this->individualNotesTransfer) > 0){
			foreach($this->individualNotesTransfer as $docking){
				$this->active = ($docking == 1);
			}
			$this->transferReceived = true;
		}
		$this->individualNotesTransfer = array(); //empty, just in case
	}

	public function generateIndividualNotes($gameData, $dbManager){ //dbManager kept for signature compatibility
		$this->doIndividualNotesTransfer();
		$ship = $this->getUnit();

		switch($gameData->phase){
			case -1: //Deployment: scenario-start state choice, effective immediately
			case 3: //Firing (player submission, via BaseShip::generateAdditionalNotes): dock/deploy order, effective NEXT turn
				if ($this->transferReceived){
					$notekey = $this->active ? 'Docked' : 'Undocked';
					$noteHuman = $this->active ? 'Docked' : 'Deployed';
					$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,1);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue
				}
				break;
			case 4: //fire-phase advance (authoritative server gamedata, notes loaded): apply the pending order + advance the regeneration clock
				//AUTO-DOCK ON DESTRUCTION: firing + criticals are already resolved by the time this
				//runs (FireGamePhase::advance), so isDestroyed() reflects post-firing state. A destroyed
				//orbital would always be docked by the player (docking happens after firing anyway, and
				//only a docked orbital can regenerate / be reached by Self Repair) - so force it docked
				//here regardless of any deploy order this turn. A destroyed docked orbital also can never
				//be deployed, so the same forcing keeps it docked. Write a corrective Docked note that
				//sorts after the player's phase-3 order (same turn, higher phase) so every future load
				//sees Docked as the latest order.
				if ($this->isDestroyed() && !$this->active){
					$this->active = true;
					$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,'Docked','Docked (auto - orbital destroyed)',1);
				}

				$newDocked = $this->active; //latest ORDERED state, incl. this turn's firing-phase order
				$oldDocked = $this->activeEffective;
				if ($newDocked && !$oldDocked){
					$this->startRegeneration($ship, $gameData);
				} else if ($newDocked && $oldDocked && $this->isDestroyed()){
					//already docked and destroyed (this turn or earlier) - make sure a regeneration
					//clock is running (turnsDocked keeps its existing count - clock is NOT reset)
					$this->startRegeneration($ship, $gameData);
				} else if (!$newDocked && $oldDocked){
					if ($this->undockingWouldBreachBlock()){
						//deploy VETO: the block cannot spare this orbital's boxes - stay docked.
						//The corrective note sorts after the player's phase-3 order (same turn,
						//higher phase), so every future load sees Docked as the latest order.
						$newDocked = true;
						$this->active = true;
						$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,'Docked','Docked (deploy refused - structure would collapse)',1);
					} else {
						$this->finishUndocking($ship, $gameData);
					}
				}
				$previousCount = $this->turnsDocked;
				$this->turnsDocked = $newDocked ? ($previousCount + 1) : 0;
				if ($this->turnsDocked != $previousCount){ //no note spam while continuously deployed
					$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,'turnsDocked','Turns Docked',$this->turnsDocked);
				}
				break;
		}
	}

	public function onIndividualNotesLoaded($gamedata){
		$this->sortNotes(); //query already orders by turn/phase - defensive
		$ordered = false; //orbitals are deployed unless noted otherwise
		$effective = false;
		$turnsDockedNote = 0;
		foreach ($this->individualNotes as $currNote){
			switch($currNote->notekey){
				case 'Docked':
				case 'Undocked':
					$isDocked = ($currNote->notekey == 'Docked');
					$ordered = $isDocked;
					//a firing-phase order takes effect NEXT turn; older notes - and this turn's
					//Deployment-phase choice - are already in effect
					if ( ($currNote->turn < $gamedata->turn) || ($currNote->phase == -1) ){
						$effective = $isDocked;
					}
					break;
				case 'turnsDocked':
					$turnsDockedNote = (int)$currNote->notevalue;
					break;
			}
		}
		$this->active = $ordered;
		$this->activeEffective = $effective;
		$this->turnsDocked = $effective ? $turnsDockedNote : 0;
		$this->individualNotes = array(); //consumed

		//apply this turn's state effects
		$this->isTargetable = !$this->activeEffective; //docked orbital cannot be targeted at all
		//SelfRepair may service orbital + weapon while DOCKED only (rules clarification 2026-07-04);
		//deployed they are out of reach (priority 0 = not repairable)
		$this->repairPriority = $this->activeEffective ? 3 : 0;
		if ($this->pairedWeapon !== null){
			$this->pairedWeapon->stowed = $this->activeEffective; //stowed beam: cannot fire or intercept
			$this->pairedWeapon->canOffLine = $this->activeEffective; //may be powered down only while docked
			$this->pairedWeapon->repairPriority = $this->activeEffective ? 6 : 0; //standard weapon repair priority while docked
		}
		//D3 docked merge: the orbital's remaining boxes join the section structure block
		//(Structure::stripForJson always sends maxhealth, so the client sees the merged pool).
		//MUST resolve the block via getStructureBlock() - $this->structureSystem is not yet
		//assigned at notes-load time (onConstructed runs after notes).
		$block = $this->getStructureBlock();
		if ($this->activeEffective && $block !== null && !$block->isDestroyed()){
			$this->appliedStructureBump = max(0, $this->getRemainingHealth());
			$block->maxhealth += $this->appliedStructureBump;
			$block->orbitalBump += $this->appliedStructureBump; //excluded from combat value (no double-count with the orbital's own boxes)
		}
	}

	private function sortNotes() {
		usort($this->individualNotes, function($a, $b) {
			// Compare by turn first
			if ($a->turn == $b->turn) {
				// If turns are equal, compare by phase
				return ($a->phase < $b->phase) ? -1 : 1;
			}
			return ($a->turn < $b->turn) ? -1 : 1;
		});
	}

	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		//Deployed / Docked (steady) or Docking / Deploying (order pending, changes next turn);
		//the client refreshes this line live as the player toggles the order button
		if ($this->activeEffective){
			if ($this->undockingWouldBreachBlock()){ //deploy currently refused - the block depends on this orbital's boxes
				$this->data["Status"] = "Docked (cannot deploy - structure would collapse)";
			}else{
				$this->data["Status"] = ($this->active == $this->activeEffective) ? "Docked" : "Deploying";
			}
		}else{
			$this->data["Status"] = ($this->active == $this->activeEffective) ? "Deployed" : "Docking";
		}
		$this->data["Special"] = "Weapon platform that can be deployed or docked to the hull.";
		$this->data["Special"] .= "<br>Dock/Deploy is ordered in the Firing Phase and takes effect next turn.";
		$this->data["Special"] .= "<br>DEPLOYED: May be called shot using Fighter FC and has profile " . $this->targetProfile . "; Hits roll on Orbital chart (1-6 weapon, 7-20 orbital). Weapon overkill passes to the orbital; orbital overkill is lost. Its weapon cannot be deactivated.";
		$this->data["Special"] .= "<br>DOCKED: Orbital hits strike Structure instead; reinforces section Structure health; its weapon is stowed (cannot fire, may be deactivated). Self Repair may service orbital and weapon while docked.";
		$this->data["Special"] .= "<br>After 5 complete docked turns, orbital and weapon fully regenerate - unless the structure block has been destroyed.";
		//$this->data["Special"] .= "<br>Deploying is refused while the Structure block depends on the orbital's merged boxes (undocking would reduce it to 0).";
	}

	public function getTargetProfileOverride(){
		return $this->targetProfile; //flat profile replaces the ship's bearing profile on called shots (no called-shot penalty)
	}

	public function getPairing(){ //getter for pairing, allows to get attached/paired systems/weps
			return $this->pairing;
	}

	public function getFireControlIndexOverride(){
		return 0; // "targeted as if they were fighters" - use Fighter Fire Control index
	}

	public function stripForJson() {
		$strippedSystem = parent::stripForJson();
		//enemy viewers don't get the pending dock/deploy order (active) - they receive the
		//state already in effect, so the order only becomes visible once it actually happens
		//$strippedSystem->active = $this->isRevealedToCurrentViewer() ? $this->active : $this->activeEffective; //latest ORDERED state - drives the Dock/Deploy buttons + pending-order glow
		if(TacGamedata::$currentPhase == 3){
			$strippedSystem->active = $this->isRevealedToCurrentViewer() ? $this->active : false; 
		}else{
			$strippedSystem->active = $this->active;
		}		
		$strippedSystem->activeEffective = $this->activeEffective; //state in effect THIS turn - drives display & targeting
		$strippedSystem->turnsDocked = $this->turnsDocked;
		$strippedSystem->isTargetable = $this->isTargetable; //dynamic: docked orbital untargetable
		$strippedSystem->targetProfile = $this->targetProfile; //flat fighter-style profile (client hit-chance mirror)
		$strippedSystem->fireControlIndexOverride = $this->getFireControlIndexOverride(); //client hit-chance mirror (fighter FC)
		$strippedSystem->outputDisplay = $this->outputDisplay;
		$strippedSystem->repairPriority = $this->repairPriority; //dynamic: repairable while docked only (SelfRepair list gate)
		$strippedSystem->privateRepairOnly = $this->privateRepairOnly; //deployed Heavy Orbital: ship-wide SR list excludes it (only its on-board SR services it)
		if ($this->structureHomeLocation !== null) $strippedSystem->structureHomeLocation = $this->structureHomeLocation; //displayed apart from its home block
		return $strippedSystem;
	}
}

/*lighter Orbital variant (Kirishiac Conqueror) - identical rules, smaller structure (default 15
boxes) and a flat defence profile of 7 (vs the standard orbital's 8).
Client has a matching KirishiacOrbitalLight class (client factory keys off $name).*/
class KirishiacOrbitalLight extends KirishiacOrbital{
	public $name = "KirishiacOrbitalLight";
    public $displayName = "Light Orbital";
	public $targetProfile = 7;
	public $canRegenerate = false; //Light Orbitals can't regenerate when docked.	

	function __construct($armour, $maxhealth, $orientation, $pairing, $profileAdjust, $systemHitChart){
		if ( $maxhealth == 0 ) $maxhealth = 15;
		parent::__construct($armour, $maxhealth, $orientation, $pairing, $profileAdjust, $systemHitChart);
		$this->displayName = 'Light Orbital ' . $pairing . '';		
	}

	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		//Deployed / Docked (steady) or Docking / Deploying (order pending, changes next turn);
		//the client refreshes this line live as the player toggles the order button
		if ($this->activeEffective){
			if ($this->undockingWouldBreachBlock()){ //deploy currently refused - the block depends on this orbital's boxes
				$this->data["Status"] = "Docked (cannot deploy - structure would collapse)";
			}else{
				$this->data["Status"] = ($this->active == $this->activeEffective) ? "Docked" : "Deploying";
			}
		}else{
			$this->data["Status"] = ($this->active == $this->activeEffective) ? "Deployed" : "Docking";
		}
		$this->data["Special"] = "Weapon platform that can be deployed or docked to the hull.";
		$this->data["Special"] .= "<br>Dock/Deploy is ordered in the Firing Phase and takes effect next turn.";
		$this->data["Special"] .= "<br>DEPLOYED: May be called shot using Fighter FC and has profile " . $this->targetProfile . "; Hits roll on Orbital chart (1-6 weapon, 7-20 orbital). Weapon overkill passes to the orbital; orbital overkill is lost. Its weapon cannot be deactivated.";
		$this->data["Special"] .= "<br>DOCKED: Orbital hits strike Structure instead; reinforces section Structure health; its weapon is stowed (cannot fire, may be deactivated). Self Repair may service orbital and weapon while docked.";
	}

}

/*HEAVY Orbital (Kirishiac Overlord) - a large weapon platform with a mounted heavy weapon and
its OWN Self Repair system. Shares the standard orbital's dock/deploy state machine, docked
structure merge, sub-chart and overkill rules, with these differences:
- flat defence profile 10 (vs 8), targeted at MEDIUM-ship fire control (vs fighter FC);
- TOO LARGE TO REGENERATE: no 5-turn docked restoration. Its attached Self Repair may only
  service systems and structure on the orbital itself (orbital, weapon, itself); while DOCKED
  its output is DOUBLED but it may then only service the weapon and the combined Structure
  block. The main vessel's Self Repair services the orbital's systems as usual in EITHER state;
- the mounted weapon REMAINS OPERATIONAL while docked, with a reduced firing arc (the weapon's
  stowed arcs, set via setStowedArcs in the blueprint; swapped in on notes-load);
- docked hits on the orbital STILL roll the Orbital chart ($subChartWhileDocked): the weapon and
  self-repair bands strike those systems (overkill from them flows back into the ship normally);
  only orbital-structure results divert to the combined Structure block.*/
class KirishiacHeavyOrbital extends KirishiacOrbital{
	public $name = "KirishiacHeavyOrbital";
	public $hitChartName = "Heavy Orbital"; //ship hit chart alias (displayName is 'Heavy Orbital C' etc.)
	public $targetProfile = 10;
	public $canRegenerate = false;
	public $subChartWhileDocked = true; //"any hits resolved to hitting the heavy weapon orbital use the heavy weapon orbital hit location chart as normal" - even docked; only structure results divert to the combined Structure
	public $isPrimaryTargetable = true;

	function __construct($armour, $maxhealth, $orientation, $pairing, $profileAdjust, $systemHitChart){
		if ( $maxhealth == 0 ) $maxhealth = 42;
		parent::__construct($armour, $maxhealth, $orientation, $pairing, $profileAdjust, $systemHitChart);
		$this->displayName = 'Heavy Orbital ' . $pairing . '';
	}

	public function getFireControlIndexOverride(){
		return 1; //Heavy Orbitals are targeted at MEDIUM ship Fire Control, not fighter FC
	}

	/*blueprint wiring for the orbital's own Self Repair system (like addOrbitalWeapon)*/
	public function addOrbitalSystem($selfRepair){
		$this->attachedSelfRepair = $selfRepair;
		$selfRepair->linkedOrbital = $this; //overkill routing back into the orbital + data window
		$selfRepair->displayName = 'Orbital Self Repair ' . $this->getPairing(); //carries its orbital's letter, distinct from the ship's own Self Repair
		$selfRepair->isTargetable = false; //"called shots may not be made on orbitals or weapons attached to them"
		if ($this->structureHomeLocation !== null) $selfRepair->structureHomeLocation = $this->structureHomeLocation;
	}

	public function setStructureHome($location){
		parent::setStructureHome($location);
		if ($this->attachedSelfRepair !== null) $this->attachedSelfRepair->structureHomeLocation = $location;
	}

	public function onIndividualNotesLoaded($gamedata){
		parent::onIndividualNotesLoaded($gamedata);
		//Reachability model (rules 2026-07-09):
		//  DEPLOYED - the orbital, its weapon and its own attached Self Repair are OUT of the mother
		//             ship's reach. They are still repairable, but only by the orbital's on-board SR:
		//             flag them privateRepairOnly and keep them at a repairable priority (the parent
		//             sets the orbital to priority 0 while deployed, which would also block the
		//             on-board SR - override back to 3/6 so the attached SR can service them).
		//  DOCKED   - the orbital rejoins the hull; the mother ship's SR services it "as usual"
		//             (parent's docked priorities 3/6 stand, privateRepairOnly cleared).
		$deployed = !$this->activeEffective;
		$this->privateRepairOnly = $deployed;
		if ($deployed) $this->repairPriority = 3; //repairable by the on-board SR (parent zeroed it while deployed)
		if ($this->pairedWeapon !== null){
			$this->pairedWeapon->privateRepairOnly = $deployed;
			if ($deployed) $this->pairedWeapon->repairPriority = 6; //ditto - reachable by the on-board SR only
			//the heavy weapon remains operational while docked - swap its live arcs to the
			//stowed (reduced) set; the client mirrors this via the weapon's stripForJson
			$this->pairedWeapon->applyStowedArcs();
		}
		//attached Self Repair: restricted to systems ON the orbital. Docked it works at DOUBLE
		//rate, but only on the weapon and the combined Structure block (the orbital's own boxes
		//are merged into it); deployed it services orbital, weapon and itself.
		$selfRepair = $this->attachedSelfRepair;
		if ($selfRepair !== null){
			$selfRepair->outputDoubled = $this->activeEffective;
			$selfRepair->privateRepairOnly = $deployed; //deployed: the mother ship's SR may not revive it either
			$allowed = array();
			if ($this->activeEffective){
				if ($this->pairedWeapon !== null) $allowed[] = $this->pairedWeapon->id;
				$block = $this->getStructureBlock();
				if ($block !== null) $allowed[] = $block->id;
			}else{
				$allowed[] = $this->id;
				$allowed[] = $selfRepair->id;
				if ($this->pairedWeapon !== null) $allowed[] = $this->pairedWeapon->id;
			}
			$selfRepair->repairRestrictedTo = $allowed;
		}
	}

	//a destroyed deployed orbital takes its attached Self Repair down with it
	//(the parent already handles the mounted weapon and the docked/regeneration branches)
	public function criticalPhaseEffects($ship, $gamedata)
	{
		parent::criticalPhaseEffects($ship, $gamedata);
		$selfRepair = $this->attachedSelfRepair;
		if (!$this->activeEffective && $this->isDestroyed() && $selfRepair !== null && !$selfRepair->isDestroyed()){
			$srHealth = $selfRepair->getRemainingHealth();
			$damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $selfRepair->id, $srHealth, 0, 0, -1, true, false, "Orbital destroyed - Self Repair lost", "OrbitalLoss");
			$damageEntry->updated = true;
			$selfRepair->damage[] = $damageEntry;
		}
	}

	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn); //Status line etc.
		//replace the standard orbital rules text with the Heavy variant
		$this->data["Special"] = "Heavy weapon platform which is deployed or docked to hull.";
		$this->data["Special"] .= "<br>Dock/Deploy is ordered in the Firing Phase and takes effect next turn.";
		$this->data["Special"] .= "<br>DEPLOYED: May be Called Shot using Medium ship FC and " . $this->targetProfile . " profile; Hit rolls on Orbital chart (weapon / self repair / orbital). Weapon cannot be deactivated.";
		$this->data["Special"] .= "<br>DOCKED: Cannot be targeted; its health reinforces its section Structure. Hits on the orbital still roll the Orbital chart.  Weapon remains operational with a reduced arc, and may be deactivated.";
		$this->data["Special"] .= "<br>Too large to regenerate. Carries its own Self Repair system restricted to the orbital's systems - DOUBLED while docked. Ship's Self Repair may service the orbital as usual in docked state.";
		$this->data["Special"] .= "<br>Deploying is refused while the Structure block depends on the orbital's merged boxes (undocking would reduce it to 0).";
	}
}

/*custon system for Nexus LCVs*/
class NexusLCVController extends ShipSystem {

    public static $controllerList = array();
    public $name = "NexusLCVController";
    public $displayName = "LCV Controller";
    public $iconPath = "hkControlNode.png";
    public $boostable = true;
    public $maxBoostLevel = 2;
	
    public static function addControllerNexus($controller){
	    NexusLCVController::$controllerList[] = $controller; //add controller to list
    }
	

    function __construct($armour, $maxhealth, $powerReq, $output ){
        parent::__construct($armour, $maxhealth, $powerReq, $output );
        $this->boostEfficiency = $powerReq;
	    NexusLCVController::addControllerNexus($this);
    }    
	
    public static function getIniBonus($unit){ //get current Initiative bonus; current = actual as of last turn
	    $iniBonus = 0;
	    $turn = TacGamedata::$currentTurn-1;
	    $turn = max(1,$turn);
	    //strongest system applies
	    foreach(NexusLCVController::$controllerList as $controller){
		$controllerShip = $controller->getUnit();
		if($unit->userid == $controllerShip->userid){ //only for the same player...
			if ( ($controller->isDestroyed($turn))
			     || ($controller->isOfflineOnTurn($turn))
			    ){ continue; }//if controller system is destroyed or offline, no effect
	    		$iniBonus = max($controller->getOutputOnTurn($turn),$iniBonus); 
		}
	    }
	    $iniBonus = $iniBonus * 5; //d20->d100
	    $iniBonus = max(0,$iniBonus); 
	    return $iniBonus;
    }
	
    public function getOutputOnTurn($turn){
        $output = parent::getOutput();
        foreach ($this->power as $power){
            if ($power->turn == $turn && $power->type == 2){
                $output += $power->amount;
            }    
        }
        return $output;
    }
	
    public function setSystemDataWindow($turn){
        parent::setSystemDataWindow($turn);     
        $this->data["Special"] = "Gives indicated Initiative bonus to all friendly Loress-class LCVs.";	     
        $this->data["Special"] .= "<br>Only strongest bonus applies.";	     	     
        $this->data["Special"] .= "<br>Any changes are effective on NEXT TURN.";	
    }

} //end of NexusLCVController


class NexusPolarenLCVController extends ShipSystem {

    public static $controllerList = array();
    public $name = "NexusPolarenLCVController";
    public $displayName = "Polaren LCV Controller";
    public $iconPath = "hkControlNode.png";
    public $boostable = true;
    public $maxBoostLevel = 2;
	
    public static function addControllerNexus($controller){
	    NexusPolarenLCVController::$controllerList[] = $controller; //add controller to list
    }
	

    function __construct($armour, $maxhealth, $powerReq, $output ){
        parent::__construct($armour, $maxhealth, $powerReq, $output );
        $this->boostEfficiency = $powerReq;
	    NexusPolarenLCVController::addControllerNexus($this);
    }    
	
    public static function getIniBonus($unit){ //get current Initiative bonus; current = actual as of last turn
	    $iniBonus = 0;
	    $turn = TacGamedata::$currentTurn-1;
	    $turn = max(1,$turn);
	    //strongest system applies
	    foreach(NexusPolarenLCVController::$controllerList as $controller){
		$controllerShip = $controller->getUnit();
		if($unit->userid == $controllerShip->userid){ //only for the same player...
			if ( ($controller->isDestroyed($turn))
			     || ($controller->isOfflineOnTurn($turn))
			    ){ continue; }//if controller system is destroyed or offline, no effect
	    		$iniBonus = max($controller->getOutputOnTurn($turn),$iniBonus); 
		}
	    }
	    $iniBonus = $iniBonus * 5; //d20->d100
	    $iniBonus = max(0,$iniBonus); 
	    return $iniBonus;
    }
	
    public function getOutputOnTurn($turn){
        $output = parent::getOutput();
        foreach ($this->power as $power){
            if ($power->turn == $turn && $power->type == 2){
                $output += $power->amount;
            }    
        }
        return $output;
    }
	
    public function setSystemDataWindow($turn){
        parent::setSystemDataWindow($turn);     
        $this->data["Special"] = "Gives indicated Initiative bonus to all friendly Polaren LCVs.";	     
        $this->data["Special"] .= "<br>Only strongest bonus applies.";	     	     
        $this->data["Special"] .= "<br>Any changes are effective on NEXT TURN.";	
    }

} //end of NexusPolarenLCVController


	
/*custom system - Drakh Raider Controller*/
class DrakhRaiderController extends ShipSystem {
    public static $controllerList = array();
    public $name = "drakhRaiderController";
    public $displayName = "Raider Controller";
    public $iconPath = "hkControlNode.png";
    public $boostable = true;
    public $maxBoostLevel = 2;
	public $primary = true;
	
    public static function addController($controller){
	    DrakhRaiderController::$controllerList[] = $controller; //add controller to list
    }
	

    function __construct($armour, $maxhealth, $powerReq, $output ){
        parent::__construct($armour, $maxhealth, $powerReq, $output );
        $this->boostEfficiency = $powerReq;
	    DrakhRaiderController::addController($this);
    }    
	
	
    public static function getIniBonus($unit){ //get current Initiative bonus; current = actual as of last turn
	    $iniBonus = 0;
	    $turn = TacGamedata::$currentTurn-1;
	    $turn = max(1,$turn);
	    //strongest system applies
	    foreach(DrakhRaiderController::$controllerList as $controller){
		$controllerShip = $controller->getUnit();
		if($unit->userid == $controllerShip->userid){ //only for the same player...
			if ( ($controller->isDestroyed($turn))
			     || ($controller->isOfflineOnTurn($turn))
			    ){ continue; }//if controller system is destroyed or offline, no effect
	    		$iniBonus = max($controller->getOutputOnTurn($turn),$iniBonus); 
		}
	    }
	    $iniBonus = $iniBonus * 5; //d20->d100
	    $iniBonus = max(0,$iniBonus); 
	    return $iniBonus;
    }
	
    public function getOutputOnTurn($turn){
        $output = parent::getOutput();
        foreach ($this->power as $power){
            if ($power->turn == $turn && $power->type == 2){
                $output += $power->amount;
            }    
        }
        return $output;
    }

	
    public function setSystemDataWindow($turn){
        parent::setSystemDataWindow($turn);     
        $this->data["Special"] = "Gives indicated Initiative bonus to all friendly Raiders and Heavy Raiders.";	     
        $this->data["Special"] .= "<br>Only strongest bonus applies.";	     	     
        $this->data["Special"] .= "<br>Any changes are effective on NEXT TURN.";	
    }
} //end of DrakhRaiderController
	

/*Orieni Hunter-Killer Control Node
every 1 point of output of such systems allows for controlling 1 flight (here: 6 HKs)
if not enough nodes are active, HKs suffer many penalties
here penalties will be proportional (instead of, say, one flight controlled and one not, there will be 2 hal-controlled flights)
Also, instead of multitude of different penalties, there will be just Initiative penalty - but a big one.
Also, by rules HK link is vulnerable to ElInt activities - which is not modelled here.
*/
class HkControlNode extends ShipSystem{
    public $name = "hkControlNode";
    public $displayName = "HK Control Node";
    public $primary = true;
    private static $fullIniPenalty = -50; //-10, times 5 d20->d100
	
    public static $alreadyCleared = false;	
	public static $nodeList = array(); //array of nodes in game
	public static $hkList = array(); // array of HK flights in game
    
    protected $possibleCriticals = array( //simplified from B5Wars!
        15=>"OutputReduced1",
        21=>"OutputReduced2",
    );	

    function __construct($armour, $maxhealth, $powerReq, $output){
        parent::__construct($armour, $maxhealth, $powerReq, $output ); 
	    HkControlNode::$nodeList[] = $this;
    }
	
	
	
	/*to be called by every HK flight after creation*/
    public static function addHKFlight($HKflight){
	    HkControlNode::$hkList[] = $HKflight;
    }
	
	//inactive entries (from other gamedata) might have slipped by... clear them out!
	public static function clearLists($gamedata){
		HkControlNode::$alreadyCleared = true;
		$tmpArray = array();
		foreach(HkControlNode::$nodeList as $curr){
			$shp = $curr->getUnit();
			//is this unit defined in current gamedata? (particular instance!)
			$belongs = $gamedata->shipBelongs($shp);
			if ($belongs){
				$tmpArray[] = $curr;
			}			
		}
		HkControlNode::$nodeList = $tmpArray;
		$tmpArray = array();
		foreach(HkControlNode::$hkList as $curr){
			//is this unit defined in current gamedata? (particular instance!)
			$belongs = $gamedata->shipBelongs($curr);
			if ($belongs){
				$tmpArray[] = $curr;
			}			
		}
		HkControlNode::$hkList = $tmpArray;
	}//endof function clearLists
	
	/*how big percentage of uncontrolled penalty will be assigned (multiplier)*/
	public static function getUncontrolledMod($playerID,$gamedata){
		$turn = TacGamedata::$currentTurn-1; //Ini based on Controllers from PREVIOUS turn!
		$turn = max(1,$turn);	
		$totalNodeOutput = 0; //output of all active HK control nodes!
		$totalHKs = 0; //number of all Hunter-Killer craft in operation!		
		
		if(!HkControlNode::$alreadyCleared) HkControlNode::clearLists($gamedata); //in case some inactive entries slipped in
		
		foreach(HkControlNode::$nodeList as $currNode){
			if ( ($currNode->isDestroyed($turn))
			     || ($currNode->isOfflineOnTurn($turn))
			    ){ continue; }//if controller system is destroyed or offline, no effect (or rather - was last turn)			
			$shp = $currNode->getUnit();
			if ($shp->userid == $playerID) $totalNodeOutput +=  $currNode->getOutput();			
		}
		$totalNodeOutput = $totalNodeOutput*6;//translate to number of controled craft - 6 per standard-sized flight
		
		foreach(HkControlNode::$hkList as $hkFlight){
			if ($hkFlight->userid == $playerID) {
				$totalHKs += $hkFlight->countActiveCraft($turn);
			}
		}
		
		$howPartial = 1;
		if ($totalHKs > 0){ //should be! but just in case
			$howPartial = 1-($totalNodeOutput / $totalHKs); //coverage of 100% means no penalty, no covewrage means 100% penalty
			$howPartial = max(0, $howPartial); //can't exercise more than 100% control ;)
		}
		
		return $howPartial;
	}
	
	
	/*Initiative modifier for hunter-killers (penalty for being uncontrolled)
		originally -3, but other penalties were there too (and 1-strong flight was still a flight) - so I increase full penalty significantly!
	*/
	public static function getIniMod($playerID,$gamedata){
		$howPartial = HkControlNode::getUncontrolledMod($playerID,$gamedata);
		$iniModifier = HkControlNode::$fullIniPenalty*$howPartial;
		    

		if($gamedata->turn < 2){ //HKs should start in hangars; if they don't, apply additional Ini penalty on turn 1
			$iniModifier+=HkControlNode::$fullIniPenalty;
		}		
		

		$iniModifier = floor($iniModifier);
		return $iniModifier;
	}//endof function getIniMod
		
     public function setSystemDataWindow($turn){
	parent::setSystemDataWindow($turn);     
	$this->data["Special"] = "Controls up to 6 Hunter-Killer craft per point of output.";	     
	$this->data["Special"] .= "<br>If there are not enough nodes to control all deployed Hunter-Killers,<br>their Initiative will be reduced by up to " . HkControlNode::$fullIniPenalty . " due to (semi-)autonomous operation.";	     	     
	$this->data["Special"] .= "<br>On turns 1 and 2, there will be additional Ini penalty on top of that, as HKs orient themselves.";	  	     
	$this->data["Special"] .= "<br>Any Initiative changes are effective on NEXT TURN.";
    }	    
		    
} //endof class HkControlNode


/* Orieni HK Control Node.
 *
 * Same role as the base HkControlNode (commands remote-controlled Hunter-Killer flights),
 * but a DIFFERENT shortfall mechanic. Where the base class (used by NexusMakar) applies a
 * graduated, fleet-wide Initiative penalty via getIniMod(), the Orieni node instead makes
 * the EXCESS flights fully UNCONTROLLED for one turn - the same critical the ELINT JAM
 * disruption applies (-15 ini, read by BaseShip::getCommonIniModifiers). This unifies the
 * node-shortfall path with the new HK Jamming "Uncontrolled" effect.
 *
 * Keeps its OWN static node/flight lists so Orieni registration is fully separate from the
 * base NexusMakar lists - the two factions' shortfall logic never mix.
 *
 * Extends ShipSystem directly (NOT HkControlNode): the two share only a few display
 * properties while all behaviour differs, so subclassing bought nothing but coupling (it
 * forced an array_pop of the base node list in the ctor to undo the parent's registration,
 * and dragged in the unused proportional getIniMod path). $name is kept identical to the base
 * ("hkControlNode") so the client renders it with the existing icon/handling - no new client
 * subclass needed.
 *
 * Capacity is counted in WHOLE FLIGHTS: one point of output controls one standard flight up to 6 craft.
 */
class HkControlNodeOrieni extends ShipSystem{
	//Display properties mirror HkControlNode so it looks/behaves identically to the player.
	public $name = "hkControlNode";
	public $displayName = "HK Control Node";
	public $primary = true;

	protected $possibleCriticals = array( //simplified from B5Wars! (same table as HkControlNode)
		15=>"OutputReduced1",
		21=>"OutputReduced2",
	);

	public static $alreadyCleared = false;
	public static $nodeList = array(); //Orieni control nodes in game
	public static $hkList = array();   //Orieni HK flights in game

	/* One-shot per advance: guards resolveNodeShortfall against running more than once
	 * within a single setCriticals (which re-runs on replay). */
	public static $shortfallResolved = false;

	/* Separate one-shot guard for the deployment-time resolver (different phase/request
	 * from the fire-phase resolver; kept independent so neither can suppress the other). */
	public static $deploymentShortfallResolved = false;

	function __construct($armour, $maxhealth, $powerReq, $output){
		parent::__construct($armour, $maxhealth, $powerReq, $output);
		HkControlNodeOrieni::$nodeList[] = $this;
	}

	/*to be called by every Orieni HK flight after creation*/
	public static function addHKFlight($HKflight){
		HkControlNodeOrieni::$hkList[] = $HKflight;
	}

	//inactive entries (from other gamedata) might have slipped by... clear them out!
	public static function clearLists($gamedata){
		HkControlNodeOrieni::$alreadyCleared = true;
		$tmpArray = array();
		foreach(HkControlNodeOrieni::$nodeList as $curr){
			$shp = $curr->getUnit();
			if ($gamedata->shipBelongs($shp)) $tmpArray[] = $curr;
		}
		HkControlNodeOrieni::$nodeList = $tmpArray;
		$tmpArray = array();
		foreach(HkControlNodeOrieni::$hkList as $curr){
			if ($gamedata->shipBelongs($curr)) $tmpArray[] = $curr;
		}
		HkControlNodeOrieni::$hkList = $tmpArray;
	}//endof function clearLists

	/* How many WHOLE HK flights this player can control, based on active control-node
	 * output. One point of output controls one standard flight. */
	private static function getControllableFlights($playerID, $turn){
		$capacity = 0;
		foreach(HkControlNodeOrieni::$nodeList as $currNode){
			if ( ($currNode->isDestroyed($turn))
			     || ($currNode->isOfflineOnTurn($turn))
			    ){ continue; }//if controller system is destroyed or offline, no effect
			$shp = $currNode->getUnit();
			if ($shp->userid == $playerID) $capacity += $currNode->getOutput();
		}
		return $capacity; //in WHOLE flights (1 output = 1 flight controlled)
	}

	/* HK Control Node shortfall -> Uncontrolled.
	 *
	 * When a player has more active Orieni HK flights than their control nodes can command,
	 * the excess flights go Uncontrolled for ONE turn - the same critical the ELINT JAM
	 * disruption applies. The crit is placed on each affected flight's sample fighter during
	 * the crit phase, taking effect next turn (oneturn semantics -> -15 ini read by
	 * BaseShip::getCommonIniModifiers, exactly when the old proportional penalty used to land).
	 *
	 * Called from the tail of Criticals::setCriticals, AFTER HkJamming::resolveJamming.
	 *
	 * The penalty is RECOMPUTED from scratch every turn over the whole on-map HK roster and
	 * re-stamped on the tail-excess: a node shortfall is a standing condition, so the flight
	 * must stay Uncontrolled for as long as it persists, not just the turn it first appears.
	 * This is the key reason we do NOT skip a flight merely because it is Uncontrolled THIS
	 * turn - that earlier exclusion (intended only to drop Jamming victims from the count) was
	 * also dropping the resolver's OWN node-shortfall victims, so the crit applied once (or by
	 * the deployment resolver on the deploy turn) and then silently lapsed the following turn.
	 * Counting an already-Uncontrolled flight is harmless: a Jamming crit is keyed to turn T
	 * (effect T only) while the node crit we stamp here is keyed to T (effect T+1), so they
	 * never collide on the same turn, and a jammed flight that is still node-short next turn
	 * SHOULD remain Uncontrolled regardless of whether the jamming wears off.
	 *
	 * Deterministic (no dice) so no replay roll-FIFO is needed, but the added crits must
	 * persist via getUpdatedCriticals (updated/newCrit), and the resolver is guarded to run
	 * once per advance so re-entry / replay does not stack duplicate Uncontrolled crits.
	 */
	public static function resolveNodeShortfall($gamedata){
		if (HkControlNodeOrieni::$shortfallResolved) return;
		HkControlNodeOrieni::$shortfallResolved = true;

		if(!HkControlNodeOrieni::$alreadyCleared) HkControlNodeOrieni::clearLists($gamedata); //drop any inactive entries

		$turn = $gamedata->turn; //control is exercised THIS turn; crit takes effect (oneturn) NEXT turn

		//Bucket EVERY on-map HK flight that demands control, by owner.
		//Excluded:
		// - DOCKED flights (Hangar Ops: removed = true): in a hangar, not on the map, so
		//   they need no Control Node. This also covers a flight that DOCKED this turn
		//   (removed set this turn) - it is in the bay next turn, so it shouldn't count.
		//   Checked explicitly (not via isDestroyed) so the intent survives any future
		//   change to the removed -> isDestroyed coupling.
		// - DESTROYED flights / flights with no live craft left.
		//NOT excluded: a flight already Uncontrolled this turn (e.g. from Jamming, or from the
		//deployment-turn resolver). It is deliberately still counted and eligible for re-crit so
		//the node-shortfall penalty RENEWS every turn the shortfall stands - see the method
		//header for why this is safe (the two Uncontrolled crits never key to the same turn).
		//INCLUDED: a flight LAUNCHED this turn. Hangar launch (HangarOps::process*Launches,
		//run in Pass 2 of setCriticals) spawns it as a fresh, non-removed FighterFlight whose
		//constructor re-registers it on $hkList, and resolveNodeShortfall runs at the tail of
		//setCriticals - after that - so a just-launched HK is on the map next turn and correctly
		//demands a Control Node now.
		$flightsByPlayer = array();
		foreach(HkControlNodeOrieni::$hkList as $hkFlight){
			if (!empty($hkFlight->removed)) continue; //docked (incl. docked this turn) - in hangar, no node needed
			if ($hkFlight->isDestroyed()) continue;
			if ($hkFlight->countActiveCraft($turn) < 1) continue;
			$flightsByPlayer[$hkFlight->userid][] = $hkFlight;
		}

		foreach($flightsByPlayer as $playerID => $flights){
			$capacity = HkControlNodeOrieni::getControllableFlights($playerID, $turn);
			$shortfall = count($flights) - $capacity;
			if ($shortfall <= 0) continue; //all flights covered

			//Mark flights Uncontrolled from the TAIL of the list until the shortfall is met.
			//Effect lands NEXT turn (control was exercised this turn).
			for ($i = count($flights) - 1; $i >= 0 && $shortfall > 0; $i--){
				HkControlNodeOrieni::markUncontrolled($flights[$i], $gamedata, $gamedata->turn + 1);
				$shortfall--;
			}
		}
	}//endof function resolveNodeShortfall

	/* Deployment-time shortfall -> Uncontrolled IN EFFECT THIS TURN.
	 *
	 * The fire-phase resolveNodeShortfall only ever applies the penalty prospectively
	 * (resolved end-of-turn, in effect next turn) - which is correct for hangar-launched
	 * HKs. But an HK that DEPLOYS ONTO THE MAP (no hangar space - the rules expect HKs to
	 * start docked) is active and uncontrolled on its FIRST turn, and that turn has no
	 * preceding crit phase, so the prospective penalty would miss it. This closes that gap:
	 * for every Orieni HK flight whose deployment turn IS the current turn and which is on
	 * the map (not docked), if the player's nodes can't command the whole on-map HK roster,
	 * the excess (tail-first) gets an Uncontrolled crit in effect THIS turn.
	 *
	 * Generalised to any first-on-map turn, not just turn 1 (covers late-deploying slots).
	 *
	 * Called once from DeploymentGamePhase::advance; persisted via submitCriticals there.
	 * Guarded by the same once-per-advance flag as the fire-phase resolver.
	 */
	public static function resolveDeploymentShortfall($gamedata){
		if (HkControlNodeOrieni::$deploymentShortfallResolved) return;
		HkControlNodeOrieni::$deploymentShortfallResolved = true;

		if(!HkControlNodeOrieni::$alreadyCleared) HkControlNodeOrieni::clearLists($gamedata);

		$turn = $gamedata->turn;

		//All on-map HK flights demand control. Split into "deployed this turn" (eligible for
		//the this-turn crit) and "already on the map from a prior turn" (already handled by the
		//previous fire phase's resolver - counted for capacity but NOT re-crit here).
		$onMapByPlayer  = array(); //all on-map flights (for capacity accounting)
		$newByPlayer    = array(); //subset deployed THIS turn, on the map (eligible for this-turn crit)
		foreach(HkControlNodeOrieni::$hkList as $hkFlight){
			if (empty($hkFlight->remoteControl)) continue; //only remote-controlled HKs are affected
			if (!empty($hkFlight->removed)) continue;       //docked - in a hangar, needs no node
			if ($hkFlight->isDestroyed()) continue;
			if ($hkFlight->countActiveCraft($turn) < 1) continue;
			$sample = $hkFlight->getSampleFighter();
			if ($sample && $sample->hasCritical("Uncontrolled", $turn)) continue; //already uncontrolled this turn

			$onMapByPlayer[$hkFlight->userid][] = $hkFlight;
			if ($hkFlight->getTurnDeployed($gamedata) == $turn){
				$newByPlayer[$hkFlight->userid][] = $hkFlight;
			}
		}

		foreach($newByPlayer as $playerID => $newFlights){
			$capacity   = HkControlNodeOrieni::getControllableFlights($playerID, $turn);
			$onMap      = $onMapByPlayer[$playerID];
			$shortfall  = count($onMap) - $capacity;
			if ($shortfall <= 0) continue; //whole on-map roster is covered

			//Assign the shortfall to the newly-deployed flights tail-first. We only crit the
			//new arrivals (prior-turn flights already got their this-turn crit last fire phase);
			//cap the count at the number of new flights so we never over-apply.
			$toCrit = min($shortfall, count($newFlights));
			for ($i = count($newFlights) - 1; $i >= 0 && $toCrit > 0; $i--){
				HkControlNodeOrieni::markUncontrolled($newFlights[$i], $gamedata, $turn); //in effect THIS turn
				$toCrit--;
			}
		}
	}//endof function resolveDeploymentShortfall

	/* Apply an Uncontrolled crit to a flight's sample fighter (flights have no CnC;
	 * getCommonIniModifiers reads flight ini crits from the sample fighter). Mirrors
	 * HkJamming::addCrit so both paths converge on the same crit + persistence convention.
	 *
	 * Uncontrolled is a ONETURN crit: hasCritical("Uncontrolled", $T) is true when
	 * crit->turn + 1 == $T. So to take effect on $effectTurn the crit is keyed to
	 * $effectTurn - 1. The fire-phase resolver passes effectTurn = turn+1 (penalty lands
	 * next turn); the deployment resolver passes effectTurn = turn (a freshly map-deployed
	 * HK that never saw a crit phase is uncontrolled on its FIRST turn). */
	private static function markUncontrolled($flight, $gamedata, $effectTurn){
		$sample = $flight->getSampleFighter();
		if (!$sample) return;

		$critTurn = $effectTurn - 1; //oneturn crit takes effect on critTurn + 1 = $effectTurn
		$crit = new Uncontrolled(-1, $flight->id, $sample->id, "Uncontrolled", $critTurn);
		$crit->updated = true;
		$crit->newCrit = true; //force save even though it takes effect a turn after its key turn
		$sample->criticals[] = $crit;

		//Surface it in the combat log via the flight's RammingAttack system - the
		//self-targeted, zero-damage convention used by HkJamming / marine missions.
		$rammingSystem = $flight->getSystemByName("RammingAttack");
		if (!$rammingSystem) return;

		$when = ($effectTurn > $gamedata->turn) ? "next turn" : "this turn";
		$fireOrder = new FireOrder(
			-1, "normal", $flight->id, $flight->id,
			$rammingSystem->id, -1, $gamedata->turn, 1,
			0, 0, 0, 0, 0,
			0, 0, 'HkControl', 10000
		);
		$fireOrder->pubnotes = "<br>HK CONTROL: Insufficient Control Nodes - flight is UNCONTROLLED (-15 Initiative) $when.";
		$fireOrder->addToDB = true;
		$rammingSystem->fireOrders[] = $fireOrder;
	}//endof function markUncontrolled

	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		//override the base (proportional-penalty) text with the Orieni Uncontrolled behaviour
		$this->data["Special"] = "Controls one Hunter-Killer flight per point of output.";
		$this->data["Special"] .= "<br>If there are not enough Control Nodes to command all deployed Hunter-Killer flights,<br>the excess flights become UNCONTROLLED (-15 Initiative) on the following turn.";
		$this->data["Special"] .= "<br>A flight already made Uncontrolled (e.g. by ELINT Jamming) does not require a Control Node while uncontrolled.";
		$this->data["Special"] .= "<br>Any Initiative changes are effective on NEXT TURN.";
	}

	public static function getIniMod($playerID,$gamedata, $ship){
	    $iniModifier = 0;

		$depTurn = $ship->getTurnDeployed($gamedata);

		if($depTurn == $gamedata->turn){ //HKs should start in hangars; if they don't, apply additional Ini penalty on deploy turn
			$iniModifier -=50;
		}		

		return $iniModifier;
	}//endof function getIniMod	

} //endof class HkControlNodeOrieni


class HyachComputer extends ShipSystem implements SpecialAbility{
    public $name = "hyachComputer";
    public $displayName = "Computer";
    public $primary = true; 
	public $isPrimaryTargetable = false; //Check if inherited and remove?
	public $isTargetable = true; //Check if inherited and remove?
    public $iconPath = "Computer.png";
	protected $doCountForCombatValue = true; //Check if inherited and remove?
		
    public $specialAbilities = array("HyachComputer"); //Front end looks for this.	
	public $specialAbilityValue = true; //so it is actually recognized as special ability!    		
	
	public $BFCPtotal_used = 0;
	public $BFCPpertype = 2;//No category can be more than 2!
	public $currClass = '';//for front end.
	
	public $allocatedBFCP = array('Fighter' => 0, 'MCV' => 0, 'Capital' => 0); //BFCP points allocated for given FC type
    
    protected $possibleCriticals = array(); //no available criticals - however damage to the computer will reduce BFCP available.
    
    function __construct($armour, $maxhealth, $powerReq, $output){ 
        parent::__construct($armour, $maxhealth, $powerReq, $output ); //$armour, $maxhealth, $powerReq, $output    	
    }  

	public function getSpecialAbilityValue($args)
    {
		return $this->specialAbilityValue;
	}
	/* this method generates additional non-standard information in the form of individual system notes
	in this case: 
	 - Initial phase: check setting changes made by user, convert to notes.
	*/
    public function generateIndividualNotes($gameData, $dbManager){ //dbManager is necessary for Initial phase only
		$ship = $this->getUnit();
		switch($gameData->phase){
						
				case 1: //Initial phase
					//data returned as allocatedBFCP table, with one value passed per BFCP point in each FCType e.g. 'Fighter' mean +1 in allocatedBFCP['Fighter']
					if($ship->userid == $gameData->forPlayer){ //only own ships, otherwise bad things may happen!
						//load existing data first - at this point ship is rudimentary, without data from database!
						$listNotes = $dbManager->getIndividualNotesForShip($gameData, $gameData->turn, $ship->id);	
						foreach ($listNotes as $currNote){
							if($currNote->systemid==$this->id){//note is intended for this system!
								$this->addIndividualNote($currNote);	 								
							}
						}
						$this->onIndividualNotesLoaded($gameData);		

							
						$keys = array_keys($this->allocatedBFCP); //Extract keys Fighter, MCV, Capital.
						$values = array_values($this->allocatedBFCP);//Extract values for those keys.																	
						foreach ($keys as $FCType) { //Will always be three keys.
						    // Find the FC Type of the current key in $keys array
						    $index = array_search($FCType, $keys);

						    // Use the FC Type to access the corresponding value in $values array
						    $ptsSet = $values[$index];	
												
							$notekey = $FCType;
							$noteHuman = 'Bonus Fire Control Point set';
							$notevalue = $ptsSet;
							$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$notevalue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue         
						}			
		}								
			break;				
		}
	} //endof function generateIndividualNotes
	

public function onIndividualNotesLoaded($gamedata)
{
	switch($gamedata->phase){
			//What were BFCP set as last turn, load them up at start of Initial Orders.			
			case 1: //Initial phase		
			    foreach ($this->individualNotes as $currNote) {
			  		if($currNote->turn == $gamedata->turn-1) {  				    	
			        $FCClass = $currNote->notevalue;

			        // Check if the key exists in $this->allocatedBFCP
			        if (array_key_exists($FCClass, $this->allocatedBFCP)) {
			            // Increment the value associated with the appropirate key e.g. Fighter, MCV, Capital.
			            $this->allocatedBFCP[$FCClass]++;
			        		}
						}
					}				
							
			break;
			//Otherwise, what were the points set this turn at end of Initial Orders.
			default:					
			    foreach ($this->individualNotes as $currNote) {
			  		if($currNote->turn == $gamedata->turn) {  			    	
			        $FCClass = $currNote->notevalue;

			        // Check if the key exists in $this->allocatedBFCP
			        if (array_key_exists($FCClass, $this->allocatedBFCP)) {
			            // Increment the value associated with the appropirate key e.g. Fighter, MCV, Capital.
			            $this->allocatedBFCP[$FCClass]++;
			        		}
						}
					}
			break;		
			}		
		
        //and immediately delete notes themselves, they're no longer needed (this will not touch the database, just memory!)
        $this->individualNotes = array();
             
        //calculate $this->BFCPtotal_used,
        $this->BFCPtotal_used = 0;
 		//$this->BFCPtotal_used = array_sum($this->allocatedBFCP); //Amended during PHP8 update - DK 25.6.25
         foreach( $this->allocatedBFCP as $alloc){
             if ( (isset($alloc)) && (is_numeric($alloc)))    $this->BFCPtotal_used += $alloc;
        }		  
 		  
 }//endof onIndividualNotesLoaded
 
	
    public function setSystemDataWindow($turn){
        parent::setSystemDataWindow($turn);
		$damageTaken = $this->maxhealth - ($this->getRemainingHealth()); //Check for damge taken.
		$lostBFCP = floor($damageTaken/5);	//1 BFCP lost per 5 damage.        
        $this->output = $this->output - $lostBFCP; //Adjust output based on damage taken, -1 point per 5 damage.
         
		$this->data["Bonus Fire Control Points (BFCP)"] = $this->BFCPtotal_used . '/' . $this->output;
		$this->data["Max Per Category"] =  $this->BFCPpertype;
		foreach($this->allocatedBFCP as $FCType=>$BFCPallocated){
			$this->data[' - '.$FCType] =  $BFCPallocated . '/' . $this->BFCPpertype;
		}
        $this->data["Special"] = "This system is responsible for Bonus Fire Control Points (BFCP) management.";	   
        $this->data["Special"] .= "<br>Each turn you may assign available BFCP points in Initial phase to one of the three Fire Control categories.";
        $this->data["Special"] .= "<br>Each category can be assigned up to two BFCP points.";
        $this->data["Special"] .= "<br>The Computer will lose 1 BFCP per 5 points of damage taken, BFCP may then need to be reduced.";        
    }
	
	//always redefine $this->data for Hyach Computer! A lot of variable information goes there...
	public function stripForJson(){
        $strippedSystem = parent::stripForJson();
        $strippedSystem->data = $this->data;
        if (isset($this->allocatedBFCP) && !empty($this->allocatedBFCP)) {
            $strippedSystem->allocatedBFCP = $this->allocatedBFCP;
        }  			
        //$strippedSystem->allocatedBFCP = $this->allocatedBFCP;
        if (isset($this->BFCPtotal_used) && !empty($this->BFCPtotal_used)) {
            $strippedSystem->BFCPtotal_used = $this->BFCPtotal_used;
        }  		
        //$strippedSystem->BFCPtotal_used = $this->BFCPtotal_used;
		
        return $strippedSystem;
    }
	
	public function doIndividualNotesTransfer(){
		//data received in variable individualNotesTransfer, further functions will look for it in allocatedBFCP
		if(is_array($this->individualNotesTransfer))	$this->allocatedBFCP = $this->individualNotesTransfer; //else there's nothing relevant there
		$this->individualNotesTransfer = array(); //empty, just in case
	}		
	
	//returns FC bonus for allocated for a given ship classes / FC index
	public function getFCBonus($FCIndex, $turn){
	    if($this->isDestroyed($turn)) return 0;
	    $FCvalue = 0;	    	
	    $FCvalueArray = array_values($this->allocatedBFCP);        
	    if (isset($FCvalueArray[$FCIndex])) {
	        $FCvalue = $FCvalueArray[$FCIndex]; 
	    }
	    return $FCvalue;
	}

							
} //endof HyachComputer


//this system contains entirety of Specialists management
class HyachSpecialists extends ShipSystem implements SpecialAbility{
    public $name = "hyachSpecialists";
    public $displayName = "Specialists";
    public $primary = true; 
	public $isPrimaryTargetable = false;
	public $isTargetable = false; //cannot be targeted ever!
    public $iconPath = "Specialists.png";
	protected $doCountForCombatValue = false; //don't count when estimating remaining combat value
    public $specialAbilities = array("HyachSpecialists"); //Front end looks for this.
	public $specialAbilityValue = true; //so it is actually recognized as special ability!    			
	public $specTotal = 0; //How many Specialists does this ship have.
	public $specTotalSelected = 0;	//How many Specialists have been selected.
	public $specTotal_used = 0; //How many Specialists have been used.
	public $specPertype = 1; //How may per type are allowed (should always be 1).
	public $specCurrClass = '';//for front end, to display Specialist types in tooltips.
	
	public $allSpec = array('Computer' => 0, 'Defence' => 0, 'Engine' => 0, 'Maneuvering' => 0, 'Power'=> 0, 'Repair' => 0, 'Sensor' => 0, 'Targeting' => 0, 'Thruster' => 0, 'Weapon' => 0); //Lists all Specialists for selection on Turn 1.
	public $availableSpec = array(); //Counts Specialists that have been selected by player from $allSpec on Turn 1.  Numeric.
	public $currSelectedSpec = array(); //Used in front end so that it knows to transfer data on Specialists selected. Value are empty or 'selected'.

	public $currchangedSpec = array(); //Creates backend notes on Specialists that have been used this turn.	
	public $allocatedSpec = array(); //Counts Specialists that have been used by player during game.
	public $specAllocatedCount = array(); //Counter used for showing what Specialists were used in Current Turn (if any).		
	public $currAllocatedSpec = array();//Used in front end so that it knows to transfer data on Specialists used. Value are empty or 'allocated'.
	
	public $specDecreased = array(); //Front End counter for updating system tooltip on which Specialists were used this turn. 	
	public $specIncreased = array(); //Front End counter for updating system tooltip on which Specialists were used this turn. 	 
	
    
    protected $possibleCriticals = array(); //no available criticals - in fact, this system is a technicality and should never be hit
	//public $deploymentTurn = 1;
    

    function __construct( $specTotal  ){ //technical object, does not need typical system attributes (armor, structure...)
        parent::__construct( 0, 1, 0, $specTotal ); //$armour, $maxhealth, $powerReq, $output
		$this->specTotal = $specTotal;
    }

	public function getSpecialAbilityValue($args)
    {
		return $this->specialAbilityValue;
	}

    public static function sortCriticalsByRepairPriority($a, $b){ //For Repair Specialists
		//priority, then cost, then ID!
		if($a->repairPriority!==$b->repairPriority){ 
            return $b->repairPriority - $a->repairPriority; //higher priority first!
        }else if($a->repairCost!==$b->repairCost){ ///costlier first!
            return $b->repairCost - $a->repairCost; //costlier first!
        }else return $a->id - $b->id;
    } //endof function sortSystemsByRepairPriority


	public function doIndividualNotesTransfer(){   
	    // Example array from Front End:
	    //     "Defence" => array(1, 2),
	    //     "Engine" => array(1, 0)  );

	    // Data received in variable individualNotesTransfer, further functions will look for it in currchangedSpec
	    if (is_array($this->individualNotesTransfer)) {
	        foreach ($this->individualNotesTransfer as $specType => $specValues) {
	            foreach ($specValues as $specAction) {
	                if ($specAction === 1) { // Specialist has been selected.
	                    // Add $specType key to $this->currSelectedSpec
	                    $this->currSelectedSpec[] = $specType; // Append $specType to $this->currSelectedSpec array
	                } elseif ($specAction === 2) { // Specialist has been used.
	                    // Add $specType key to $this->currchangedSpec
	                    $this->currchangedSpec[] = $specType; // Append $specType to $this->currchangedSpec array
	                }
	            }
	        }
	    }                                	   
	    $this->individualNotesTransfer = array(); // Empty, just in case
	}


//	 this method generates additional non-standard informaction in the form of individual system notes in this case: 
//	 - Initial phase: check setting changes made by user, convert to notes	
    public function generateIndividualNotes($gameData, $dbManager){ //dbManager is necessary for Initial phase only
		$this->doIndividualNotesTransfer();
		$ship = $this->getUnit();	
		
		switch($gameData->phase){
			
			case -1:
				if (!empty($this->currSelectedSpec)) {
					foreach ($this->currSelectedSpec as $specialistType) {//Take Front end data on deployment turn and generate available Specs.
						$notekey = 'available;' . $specialistType; //Make those Specialist Types available for rest of game.
						$noteHuman = 'Specialist available';
						$noteValue = 1; //Max Specialists is always 1, value not actually used for this type of note.
						$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$noteValue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue
					}
				}	
			break;

			case 1: //Initial phase

				if($ship->userid == $gameData->forPlayer){ //only own ships, otherwise bad things may happen!
					//load existing data first - at this point ship is rudimentary, without data from database!
					$listNotes = $dbManager->getIndividualNotesForShip($gameData, $gameData->turn, $ship->id);	
					foreach ($listNotes as $currNote){
						if($currNote->systemid==$this->id){//note is intended for this system!
							$this->addIndividualNote($currNote);
						}
					}
					$this->onIndividualNotesLoaded($gameData);
					
					if (!empty($this->currchangedSpec)) {																				
						foreach($this->currchangedSpec as $specialistType){//Take Front end data and generate used Specs.
							$notekey = 'allocated;' . $specialistType;
							$noteHuman = 'Specialist Used';
							$noteValue = 1; //Max Specialists is always 1, value not actually used for this type of note.
							$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$noteValue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue
						}
					}
	
				}
			break;							
						
			//case 2: //Movement
			//case 5: //Pre-Firing
			//case 3: //Firing	
			default:									 

				if($ship->userid == $gameData->forPlayer){ //only own ships, otherwise bad things may happen!
					if (!empty($this->currchangedSpec)) {																				
						foreach($this->currchangedSpec as $specialistType){//Take Front end data and generate used Specs.
							$notekey = 'allocated;' . $specialistType;
							$noteHuman = 'Specialist Used';
							$noteValue = 1; //Max Specialists is always 1, value not actually used for this type of note.
							$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$noteValue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue
						}
					}
				}		
			break;
		}
			
	} //endof function generateIndividualNotes
	
//	act on notes just loaded - to be redefined by systems as necessary - fill $allocation table
	public function onIndividualNotesLoaded($gamedata){
		foreach ($this->individualNotes as $currNote){ //assume ASCENDING sorting - so enact all changes as is
			$explodedKey = explode ( ';' , $currNote->notekey ) ;//split into array: [area;value] where area denotes action, value - damage type (typically) 
			

            if ( !array_key_exists($explodedKey[1], $this->availableSpec) ){ //if this specialist is not yet set - do set it
                    $this->availableSpec[$explodedKey[1]] = 1;
                    $this->allocatedSpec[$explodedKey[1]] = 0;
            }
					
			if (($explodedKey[0] == 'allocated') && ($currNote->turn == $gamedata->turn)){ //Mark when a Specialist has been used on a given turn.
				$ship = $this->getUnit();
			
				if ($explodedKey[1] == 'Computer'){ //Computer BFCP increased by 2.
				 	$strongestSystem = null;
					$strongestValue = -1;
						foreach ($ship->systems as $system) {
							if ($system->isDestroyed($gamedata->turn)) continue;//don't need to do anything on destroyed systems.								
						    if ($system instanceof HyachComputer) {
						        if ($system->output > $strongestValue) {
						            $strongestValue = $system->output;
						            $strongestSystem = $system;

						            if ($strongestValue > 0) { // Computer actually exists to be enhanced!
						                $strongestSystem->output += 2;
										$strongestSystem->BFCPpertype += 1;
						            }	
								} 		
							}
						}
					$this->specAllocatedCount[$explodedKey[1]] = 1;//To show it has been used this turn in system info tooltip.
						
				}else if ($explodedKey[1] == 'Defence'){ //Ship profiles reduced by 5, intercept ratings +10 ,
					$ship->forwardDefense -= 2;
					$ship->sideDefense -= 2;
					
					foreach ($ship->systems as $system){
						if ($system instanceof Weapon){
							
							if ($system->intercept > 0){
							$system->intercept += 2;
							}
						}
					}
					
					$this->specAllocatedCount[$explodedKey[1]] = 1; //To show it has been used this turn in system info tooltip.	
					
				}else if ($explodedKey[1] == 'Engine'){ //+25% thrust, remove an Engine crit.
				 	$strongestSystem = null;
					$strongestValue = -1;
						foreach ($ship->systems as $system) {
							if ($system->isDestroyed($gamedata->turn)) continue;//don't need to do anything on destroyed systems.								
						    if ($system instanceof Engine) {
						        if ($system->output > $strongestValue) {
						            $strongestValue = $system->output;
						            $strongestSystem = $system;

						            if ($strongestValue > 0) { // Engine actually exists to be enhanced!
						                $specialistBoost = floor($strongestSystem->output * 0.25);
						                $strongestSystem->output += $specialistBoost;
						            }	
								} 
								/*
								$critList = array();							
								foreach($system->criticals as $critDmg) {
											if($critDmg->repairPriority<1) continue;//if critical cannot be repaired
											if($critDmg->turn >= $gamedata->turn) continue;//don't repair criticals caused in current (or future!) turn.  Shouldn't happen...
											if ($critDmg->oneturn || ($critDmg->turnend > 0)) continue;//temporary criticals (or those already repaired) also cannot be repaired
											$critList[] = $critDmg;				
											}	
								
									$noOfCrits = count($critList);							
									$critRepairs = 1;							
									if($noOfCrits>0){
										foreach ($critList as $critDmg){ //repairable criticals of current system
											if ($critRepairs > 0){//Can still repair!
												$critDmg->turnend = $gamedata->turn-1;//actual repair. Use previous turn so it disappears after Intitial Orders (but would effect then, time to repair etc.
												$critDmg->forceModify = true; //actually save the repair...
												$critDmg->updated = true; //actually save the repair cd!...
												$critRepairs -= 1;
												
									        	if ($critRepairs <= 0) {
									            break; // No need to continue looping if all repairs are done							
												}
											}
										}
									}
									*/																		
							}
						}
					$this->specAllocatedCount[$explodedKey[1]] = 1;//To show it has been used this turn in system info tooltip.
						
				}else if ($explodedKey[1] == 'Maneuvering'){ //Reduce Turn Cost and Turn Delay by one step.
				 	$strongestSystem = null;
					$strongestValue = -1;
						foreach ($ship->systems as $system) {
							if ($system->isDestroyed($gamedata->turn)) continue;//don't need to do anything on destroyed systems.								
						    if ($system instanceof Engine) {
						        if ($system->output > $strongestValue) {
						            $strongestValue = $system->output;
						            $strongestSystem = $system;

						            if ($strongestValue > 0) { // Engine actually exists to be enhanced!
						                $specialistBoost = floor($strongestSystem->output * 0.10);
						                $strongestSystem->output += $specialistBoost;
						            }	
								} 
							}
						}			
			            if ($ship->turncost == 0) $ship->turncost = 0;
			            if ($ship->turncost == 0.5) $ship->turncost = 0.25;
			            if ($ship->turncost == 0.66) $ship->turncost = 0.33;
			            if ($ship->turncost == 1) $ship->turncost = 0.5;
			            if ($ship->turncost == 1.5) $ship->turncost = 0.75;

			            if ($ship->turndelaycost == 0) $ship->turndelaycost = 0;        
			            if ($ship->turndelaycost == 0.5) $ship->turndelaycost = 0.25;
			            if ($ship->turndelaycost == 0.66) $ship->turndelaycost = 0.33;
			            if ($ship->turndelaycost == 1) $ship->turndelaycost = 0.5;
			 			if ($ship->turndelaycost == 1.5) $ship->turndelaycost = 0.75;
		 								
					$this->specAllocatedCount[$explodedKey[1]] = 1;	
													
				}else if ($explodedKey[1] == 'Power'){ //Extra power in Initial Orders. Remove a reactor crit.
				 	$strongestSystem = null;
					$strongestValue = -1;
						foreach ($ship->systems as $system) {
							if ($system->isDestroyed($gamedata->turn)) continue;//don't need to do anything on destroyed systems.								
						    if ($system instanceof Reactor) {
						        if ($system->output > $strongestValue) {
						            $strongestValue = $system->output;
						            $strongestSystem = $system;

						            if ($strongestValue > 0) { // Reactor actually exists to be enhanced!
						            	if ($ship->shipSizeClass >= 3) $powerBoost = 12;
						            	if ($ship->shipSizeClass == 2) $powerBoost = 10;						            		
						            	if ($ship->shipSizeClass < 2) $powerBoost = 8;						            	

						                $strongestSystem->output += $powerBoost;
										}						            
						            }	
								
								 
								$critList = array();							
								foreach($system->criticals as $critDmg) {
											if($critDmg->repairPriority<1) continue;//if critical cannot be repaired
											if($critDmg->turn >= $gamedata->turn) continue;//don't repair criticals caused in current (or future!) turn.  Shouldn't happen...
											if ($critDmg->oneturn || ($critDmg->turnend > 0)) continue;//temporary criticals (or those already repaired) also cannot be repaired
											$critList[] = $critDmg;				
											}	
								
									$noOfCrits = count($critList);							
									$critRepairs = 1;							
									if($noOfCrits>0){
										usort($critList, [self::class, 'sortCriticalsByRepairPriority']);			
										foreach ($critList as $critDmg){ //repairable criticals of current system
											if ($critRepairs > 0){//Can still repair!

												$critDmg->turnend = $gamedata->turn-1;//actual repair. Use previous turn so it disappears after Intitial Orders (but would effect then, time to repair etc.
												$critDmg->forceModify = true; //actually save the repair...
												$critDmg->updated = true; //actually save the repair cd!...
												$critRepairs -= 1;
												
									        	if ($critRepairs <= 0) {
									            break; // No need to continue looping if all repairs are done							
												}
											}
										}
									}														
							}
						}
					$this->specAllocatedCount[$explodedKey[1]] = 1;//To show it has been used this turn in system info tooltip.
						
				}else if ($explodedKey[1] == 'Repair'){ //Repair two critical effects automatically.
				
					//repair criticals (on non-destroyed systems only; also, skip criticals generated this turn!)
					$critList = array();
					foreach ($ship->systems as $systemToRepair){//crit fixing may be necessary even on technically undamaged systems	
						if ($systemToRepair->repairPriority<1) continue;//skip systems that cannot be repaired
						if ($systemToRepair->isDestroyed($gamedata->turn)) continue;//don't repair criticals on destroyed system...

						foreach($systemToRepair->criticals as $critDmg) {
							if($critDmg->repairPriority<1) continue;//if critical cannot be repaired
							if($critDmg->turn >= $gamedata->turn) continue;//don't repair criticals caused in current (or future!) turn.  Shouldn't happen...
							if ($critDmg->oneturn || ($critDmg->turnend > 0)) continue;//temporary criticals (or those already repaired) also cannot be repaired
							if($critDmg->repairPriority<10) $critDmg->repairPriority += $systemToRepair->repairPriority; //modify priority by priority of system critical is on! 
							$critList[] = $critDmg;				
						}		
					}	
					$noOfCrits = count($critList);
					$critRepairs = 2;
					if($noOfCrits>0){
						usort($critList, [self::class, 'sortCriticalsByRepairPriority']);
		
						foreach ($critList as $critDmg){ //repairable criticals of current system
							if ($critRepairs > 0){//Can still repair!
								$critDmg->turnend = $gamedata->turn-1;//actual repair. Use previous turn so it disappears after Intitial Orders (but would effect then, time to repair etc.
								$critDmg->forceModify = true; //actually save the repair...
								$critDmg->updated = true; //actually save the repair cd!...
								$critRepairs -= 1;
								
					        	if ($critRepairs <= 0) {
					            break; // No need to continue looping if all repairs are done							
								}
							}
						}
					}
					$this->specAllocatedCount[$explodedKey[1]] = 1;//To show it has been used this turn in system info tooltip.
						
				}else if ($explodedKey[1] == 'Sensor'){ //+1 EW, repairs a Scanner crit.
				 	$strongestSystem = null;
					$strongestValue = -1;
						foreach ($ship->systems as $system) {
							if ($system->isDestroyed($gamedata->turn)) continue;//don't need to do anything on destroyed systems.								
							    if ($system instanceof Scanner) {
						    	
							        if ($system->output > $strongestValue) {
							            $strongestValue = $system->output;
							            $strongestSystem = $system;

							            if ($strongestValue > 0) { // Scanner actually exists to be enhanced!
							                $strongestSystem->output += 1;
							            }	
									} 
								
								$critList = array();							
								foreach($system->criticals as $critDmg) {
											if($critDmg->repairPriority<1) continue;//if critical cannot be repaired
											if($critDmg->turn >= $gamedata->turn) continue;//don't repair criticals caused in current (or future!) turn.  Shouldn't happen...
											if ($critDmg->oneturn || ($critDmg->turnend > 0)) continue;//temporary criticals (or those already repaired) also cannot be repaired
											$critList[] = $critDmg;				
											}	
								
									$noOfCrits = count($critList);							
									$critRepairs = 1;							
									if($noOfCrits>0){
										usort($critList, [self::class, 'sortCriticalsByRepairPriority']);		
										foreach ($critList as $critDmg){ //repairable criticals of current system
											if ($critRepairs > 0){//Can still repair!
												$critDmg->turnend = $gamedata->turn-1;//actual repair. Use previous turn so it disappears after Intitial Orders (but would effect then, time to repair etc.
												$critDmg->forceModify = true; //actually save the repair...
												$critDmg->updated = true; //actually save the repair cd!...
												$critRepairs -= 1;
												
									        	if ($critRepairs <= 0) {
									            break; // No need to continue looping if all repairs are done							
												}
											}
										}
									}
								}
						}			

					$this->specAllocatedCount[$explodedKey[1]] = 1;//To show it has been used this turn in system info tooltip.
						
				}else if ($explodedKey[1] == 'Targeting'){ //+3% to hit on ALL weapons this turn
					$ship->toHitBonus += 0.6;	
					$this->specAllocatedCount[$explodedKey[1]] = 1;
										
				}else if ($explodedKey[1] == 'Thruster'){ //Remove limits on Thruster rating, improve Engine efficiency.
				 	$strongestSystem = null;
					$strongestValue = -1;
						foreach ($ship->systems as $system) {
							if ($system->isDestroyed($gamedata->turn)) continue;//don't need to do anything on destroyed systems.							
						    if ($system instanceof Engine) {
						        if ($system->output > $strongestValue) {
						            $strongestValue = $system->output;
						            $strongestSystem = $system;

						            if ($strongestValue > 0) { // Engine actually exists to be enhanced!
						                $strongestSystem->boostEfficiency -= 1;
						            }	
								} 		
							}
						}
						foreach ($ship->systems as $system){
							if ($system instanceof Thruster){
								$system->output = 99;	
							}
						}		
					$this->specAllocatedCount[$explodedKey[1]] = 1;//To show it has been used this turn.
						
				}else if ($explodedKey[1] == 'Weapon'){ //All weapon damage +3, actual damage increase done in weapon.php

					$this->specAllocatedCount[$explodedKey[1]] = 1; //To show it has been used this turn in system info tooltip.	
					
				}else{}
							

			}
			if ($explodedKey[0] == 'allocated'){ //Update variables to show Specialist used and not available anymore.
				 $this->allocatedSpec[$explodedKey[1]] = 1;			
				 $this->availableSpec[$explodedKey[1]] = 0;
			}	 	
		}
		//and immediately delete notes themselves, they're no longer needed (this will not touch the database, just memory!)
		$this->individualNotes = array();
		
		//calculate $this->specTotal_used and specTotalSelected too!
		$this->specTotalSelected = 0;		
		$this->specTotal_used = 0;
 		$this->specTotalSelected = array_sum($this->availableSpec);	
 		$this->specTotal_used = array_sum($this->allocatedSpec);	  
	} //endof function onIndividualNotesLoaded

	
    public function setSystemDataWindow($turn){
        parent::setSystemDataWindow($turn);            
		$this->data["Specialists"] =  $this->specTotal - $this->specTotal_used; 		
		foreach($this->availableSpec as $specialistType=>$specValue){
			//$specUsed = $this->allocatedSpec[$specialistType];
			$this->data[' - '.$specialistType] =  $specValue;
		}
		if (TacGamedata::$currentPhase != -1 && !empty($this->specAllocatedCount)) {
			$used = [];

			foreach ($this->specAllocatedCount as $specialistType => $specValue) {
				$used[] = $specialistType;
			}

			$this->data["Specialists Used This Turn"] = implode(', ', $used);
		}
	if 	($turn == 1 && TacGamedata::$currentPhase == -1){	//Show all Specialist info on Turn 1 Initial Orders.
	        $this->data["Special"] = "Technical system for Specialist management.";
	        $this->data["Special"] .= "<br>On the Turn this ship deploys, select which Specialists this ship will have available.";        	   
	        $this->data["Special"] .= "<br>Activate Specialist(s) by clicking their '+' button during Initial Orders."; 
	        $this->data["Special"] .= "<br>Each Specialist can be used once, with these effects on the turn they are used:";
			$this->data["Special"] .= "<br>  - Computer: +2 BFCP, +1 BFCP per type."; 
			$this->data["Special"] .= "<br>  - Defence: Profiles lowered by 10%, all intercept ratings +10%."; 
			$this->data["Special"] .= "<br>  - Engine: +25% Thrust."; 
			$this->data["Special"] .= "<br>  - Maneuvering: +10% thrust, Halves Turn Cost / Delay.";
			$this->data["Special"] .= "<br>  - Sensor: +1 EW, remove a Scanner critical.";
			$this->data["Special"] .= "<br>  - Power: +8 to 12 power, remove a Reactor critical.";
			$this->data["Special"] .= "<br>  - Repair: Remove two critical effects.";						 			
			$this->data["Special"] .= "<br>  - Targeting: All weapons +3% to hit.";
			$this->data["Special"] .= "<br>  - Thruster: No thruster limits and Engine Efficiency improved.";
			$this->data["Special"] .= "<br>  - Weapon: All weapons +3 damage this turn.";								 
	    }else{ //After Deployment on Turn 1, reduce data so that it just shows relevant info on Specialists selected.
	        $this->data["Special"] = "Technical system used for Specialist management.";       	   
	        $this->data["Special"] .= "<br>Activate Specialist(s) by clicking their '+' button during Initial Orders."; 
	        $this->data["Special"] .= "<br>Each Specialists can be used once, with these effects on the turn they are used:";
				foreach($this->allocatedSpec as $specialistType => $specValue) {
					if ($specialistType == 'Computer') $this->data["Special"] .= '<br>  -  '.$specialistType . ': +2 BFCP, +1 BFCP per type.';
					if ($specialistType == 'Defence') $this->data["Special"] .= '<br>  -  '.$specialistType . ': Profiles lowered by 10%, intercept ratings +10%.';
					if ($specialistType == 'Engine') $this->data["Special"] .= '<br>  -  '.$specialistType . ': +25% Thrust.';
					if ($specialistType == 'Maneuvering') $this->data["Special"] .= '<br>  -  '.$specialistType . ': +10% thrust, Halves Turn Cost / Delay.';
					if ($specialistType == 'Repair') $this->data["Special"] .= '<br>  -  '.$specialistType . ' :Remove two critical effects.';
					if ($specialistType == 'Sensor') $this->data["Special"] .= '<br>  -  '.$specialistType . ' :+1 EW, removes Scanner critical.';
					if ($specialistType == 'Power') $this->data["Special"] .= '<br>  -  '.$specialistType . ' :+8 to 12 power, removes Reactor critical.';
					if ($specialistType == 'Targeting') $this->data["Special"] .= '<br>  -  '.$specialistType . ': All weapons +3% to hit.';
					if ($specialistType == 'Thruster') $this->data["Special"] .= '<br>  -  '.$specialistType . ': No thruster limits and engine efficiency improved.';
					if ($specialistType == 'Weapon') $this->data["Special"] .= '<br>  -  '.$specialistType . ': All weapons +3 damage this turn.';						
				}        
		}         	 	
    }
	
	//always redefine $this->data for Specialists! Can trim down to essentials later.
	public function stripForJson(){
        $strippedSystem = parent::stripForJson();
        $strippedSystem->data = $this->data;
        if (isset($this->allocatedSpec) && !empty($this->allocatedSpec)) {
            $strippedSystem->allocatedSpec = $this->allocatedSpec;
        } 		
        //$strippedSystem->allocatedSpec = $this->allocatedSpec;
        if (isset($this->availableSpec) && !empty($this->availableSpec)) {
            $strippedSystem->availableSpec = $this->availableSpec;
        } 		
        //$strippedSystem->availableSpec = $this->availableSpec;  
        if (isset($this->currSelectedSpec) && !empty($this->currSelectedSpec)) {
            $strippedSystem->currSelectedSpec = $this->currSelectedSpec;
        } 			    
      	//$strippedSystem->currSelectedSpec = $this->currSelectedSpec;
        if (isset($this->currAllocatedSpec) && !empty($this->currAllocatedSpec)) {
            $strippedSystem->currAllocatedSpec = $this->currAllocatedSpec;
        } 				        
      	//$strippedSystem->currAllocatedSpec = $this->currAllocatedSpec;  
        if (isset($this->specTotal_used) && !empty($this->specTotal_used)) {
            $strippedSystem->specTotal_used = $this->specTotal_used;
        } 		      
        //$strippedSystem->specTotal_used = $this->specTotal_used;
        if (isset($this->specAllocatedCount) && !empty($this->specAllocatedCount)) {
            $strippedSystem->specAllocatedCount = $this->specAllocatedCount;
        } 			       
        //$strippedSystem->specAllocatedCount = $this->specAllocatedCount;
        if (isset($this->specDecreased) && !empty($this->specDecreased)) {
            $strippedSystem->specDecreased = $this->specDecreased;
        } 			      
        //$strippedSystem->specDecreased = $this->specDecreased;
        if (isset($this->specIncreased) && !empty($this->specIncreased)) {
            $strippedSystem->specIncreased = $this->specIncreased;
        } 			
        //$strippedSystem->specIncreased = $this->specIncreased;                             		
        return $strippedSystem;
    }
	
									
} //endof HyachSpecialists


/* Connection Strut, as present on units too large for their designers tech level
	in FV damage is reflected on Structure in Critical phase (not immediately), which means:
	 - incoming fire will affect less damaged Structure (rather than potentially spill over to PRIMARY Structure)
	 - Strut damage will be reflected on PRIMARY if appropriate structure is gone
	 - Strut should have the same armor as section itself (so reflection is accurate)
	 - damage will be attributed to ORIGINAL fire order - potentially creating strange order of events in log
	 - damage in the log will include damage on Connection Strut itself (so effectively a third of it will be non-damage ;) )
	 - any effect that trigger on hitting Structure will NOT work on Strut (like Burst Beam's power drain)
	 - any armor-affecting effects (Plasma Stream...) will work separately on Struct and Structure itself, leading to further discrepancies
*/
class ConnectionStrut extends ShipSystem{
    public $name = "connectionStrut";
    public $displayName = "Connection Strut";
    public $iconPath = "connectionStrut.png";
    
	protected $doCountForCombatValue = false; //false means this system is skipped when evaluating ships' combat value!
    
	//Connection Strut cannot be repaired!
	public $repairPriority = 0;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
    
    function __construct($armour){
        parent::__construct($armour, 999, 0, 0);    
    }
	
    public function setSystemDataWindow($turn){
        $this->data["Special"] = "This is not a system - rather a weak point in ships' Structure.";
        $this->data["Special"] .= " It has no Structure of its own (in FV - has infinite structure ;) ).";
        $this->data["Special"] .= "<br>Any damage scored on Connection Strut will be scored DOUBLE on appropriate Structure.";
        $this->data["Special"] .= "<br>It will appear in log as coming from appropriate hit, despite actually being marked in Critical phase (which may lead to strange order of events).";
		parent::setSystemDataWindow($turn);    
	}		


    public function testCritical($ship, $gamedata, $crits, $add = 0){ //reflect any damage taken this turn on appropriate Structure!
        foreach ($this->damage as $damage){
            if ($damage->turn == $gamedata->turn || $damage->turn == -1){
                if ($damage->damage > $damage->armour){
                    $dmgTaken = $damage->damage - $damage->armour;
					$dmgTaken = $dmgTaken *2;//double damage DONE, not raw damage coming!
					//reflect on appropriate Structure, and failing that on PRIMARY
					$trgtStructure = $this->structureSystem;
					$healthRem = $trgtStructure->getRemainingHealth();
					$toDeal = min($dmgTaken, $healthRem);
					$destroyed = false;
					if ($toDeal == $healthRem){
						$destroyed = true;
					}
					if ($toDeal > 0){						
						$damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $trgtStructure->id, $toDeal, 0, 0, $damage->fireorderid, $destroyed, false, "Connection Strut!", $damage->damageclass);
						$damageEntry->updated = true;
						$trgtStructure->damage[] = $damageEntry;
					}
					$toDeal = $dmgTaken - $healthRem;
					if ($toDeal > 0){ //any remaining damage - score on PRIMARY Structure
						$primary = $ship->getStructureSystem(0);
						$healthRem = $primary->getRemainingHealth();
						$toDeal = min($toDeal, $healthRem);
						$destroyed= false;
						if ($toDeal == $healthRem){
							$destroyed = true;
						}
						if ($toDeal > 0){
							$damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $primary->id, $toDeal, 0, 0, $damage->fireorderid, $destroyed, false, "Connection Strut!", $damage->damageclass);
							$damageEntry->updated = true;
							$primary->damage[] = $damageEntry;
						}
					}
		
				}
            }
        }
        
        return $crits; //unmodified - this system suffers no criticals
    } //endof function testCritical	
	
}//endof class ConnectionStrut


/*this system contains entirety of Adaptive Armor management*/
class AdaptiveArmorController extends ShipSystem{
    public $name = "adaptiveArmorController";
    public $displayName = "Adaptive Armor Controller";
    public $primary = true; 
	public $isPrimaryTargetable = false;
	public $isTargetable = false; //cannot be targeted ever!
    public $iconPath = "adaptiveArmorController.png";
	protected $doCountForCombatValue = false; //don't count when estimating remaining combat value
	
	public $AAtotal = 0;
	public $AAtotal_used = 0;
	public $AApertype = 0;
	public $AApreallocated = 0;
	public $AApreallocated_used = 0;
	public $currClass = '';//for front end
	
	public $allocatedAA = array(); //AA points allocated for given damage type
	public $availableAA = array(); //AA points available for allocation for given damage type
	public $currchangedAA = array(); //AA points allocated in front end

	public $pressignedReset = false; //New variable for Front End to reset pre-assigned to 0, so that they can be set on deployment turn - DK - May 2025
	
	
    
    protected $possibleCriticals = array(); //no available criticals - in fact, this system is a technicality and should never be hit
    
	/*as this is a technical system, armor/health/power are always pre-set
		settings defined by ship creator are: maxiumum total AA points, maximum AA points per weapon type, AA points pre-allocated		
		NOTE: this system should be asigned to adaptiveArmorController attribute (in addition to regular placement) to work properly!
	*/
    function __construct( $AAtotal, $AApertype, $AApreallocated  ){ //technical object, does not need typical system attributes (armor, structure...)
        parent::__construct( 0, 1, 0, $AAtotal ); //$armour, $maxhealth, $powerReq, $output
		$this->AAtotal = $AAtotal;
		$this->AApertype = $AApertype;
		$this->AApreallocated = $AApreallocated;
		if (TacGamedata::$currentTurn > 1){
			$this->AApreallocated_used = $AApreallocated; //for further turns player cannot allocate "pre-battle" points any more
		}
		
    }

	//marks damage class(-es) of weapon as existing in $allocatedAA and $availableAA tables
	private function markWpnDmgClass(Weapon $weapon){
		$weaponClassArray = array();
		foreach($weapon->weaponClassArray as $weaponClass){
			$weaponClassArray[] = $weaponClass;
		}
		$weaponClassArray[] = $weapon->weaponClass;
		foreach($weaponClassArray as $weaponClass){
			if($weaponClass == "Boarding") continue; //Exclude unnecessary types of weapon.
			//check if already defined, if not - add to both tables
			if (!isset($this->allocatedAA[$weaponClass])){
				$this->availableAA[$weaponClass] = 0;
				$this->allocatedAA[$weaponClass] = 0;
			}
		}
	}

	/* this method generates additional non-standard informaction in the form of individual system notes
	in this case: 
	 - Deployment phase: check existing (enemy!) weapons and weapon types, note them for allocation
	 - Initial phase: check setting changes made by user, convert to notes
	 - Firing phase: check damage suffered by ship, convert to notes if it increases allowance of particular weapon type
	*/
    public function generateIndividualNotes($gameData, $dbManager){ //dbManager is necessary for Initial phase only
		$ship = $this->getUnit();
		switch($gameData->phase){
				case -1: //deployment phase 
					if($ship->userid == $gameData->forPlayer){ //only own ships, otherwise two copies of initial data are written
						foreach($gameData->ships as $enemyUnit) if($enemyUnit->team != $ship->team){
							foreach($enemyUnit->systems as $sys){
								if($sys instanceOf Fighter){
										foreach($sys->systems as $wpn) if($wpn instanceOf Weapon){
											$this->markWpnDmgClass($wpn);
										}
								}else if($sys instanceOf Weapon){
										$this->markWpnDmgClass($sys);
								}							
							}				
						}
						//AND PREPARE APPROPRIATE NOTES!						
						//	'available;dmgType' public $availableAA = array(); //AA points available for allocation for given damage type
						//	'set;dmgType' public $allocatedAA = array(); //AA points allocated for given damage type
						foreach($this->availableAA as $weaponClass=>$ptsAvailable){
							//	'available;dmgType' public $availableAA = array(); //AA points available for allocation for given damage type
							//	'set;dmgType' public $allocatedAA = array(); //AA points allocated for given damage type
							if (isset($this->allocatedAA[$weaponClass])){
								$ptsSet = $this->allocatedAA[$weaponClass];
							} else $ptsSet = 0;
							$notekey = 'available;' . $weaponClass;
							$noteHuman = 'Adaptive Armor available';
							$noteValue = $ptsAvailable;
							$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$noteValue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue
							$notekey = 'set;' . $weaponClass;
							$noteHuman = 'Adaptive Armor set';
							$noteValue = $ptsSet;
							$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$noteValue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue
						}
					}
				break;
								
				case 1: //Initial phase
					//data returned as currchangedAA table, with ONLY information about what was changed )one entry means +1)
					//in turn 1 increase availability as well (this goes from pre-allocated pool), in further turns do not!
					if($ship->userid == $gameData->forPlayer){ //only own ships, otherwise bad things may happen!
						//load existing data first - at this point ship is rudimentary, without data from database!
						$listNotes = $dbManager->getIndividualNotesForShip($gameData, $gameData->turn, $ship->id);	
						foreach ($listNotes as $currNote){
							if($currNote->systemid==$this->id){//note is intended for this system!
								$this->addIndividualNote($currNote);
							}
						}
						$this->onIndividualNotesLoaded($gameData);		
					
						foreach($this->currchangedAA as $weaponClass){
							$this->allocatedAA[$weaponClass]++;
							if ($gameData->turn==1) $this->availableAA[$weaponClass]++;
						}

						//SERVER-AUTHORITATIVE CLAMP - DK May 2026
						//The client (canIncrease) already enforces these limits, but a malformed or
						//inflated currchangedAA payload must never be persisted over the system's caps.
						//Enforce the same hard invariants here so the database can never hold an illegal value.
						foreach($this->allocatedAA as $weaponClass=>$allocated){
							//never assign more than the per-type maximum
							if ($allocated > $this->AApertype) $this->allocatedAA[$weaponClass] = $this->AApertype;
							//never assign more than is available for that type
							if (isset($this->availableAA[$weaponClass]) && $this->allocatedAA[$weaponClass] > $this->availableAA[$weaponClass]){
								$this->allocatedAA[$weaponClass] = $this->availableAA[$weaponClass];
							}
						}
						//availability itself can never exceed the per-type maximum
						foreach($this->availableAA as $weaponClass=>$available){
							if ($available > $this->AApertype) $this->availableAA[$weaponClass] = $this->AApertype;
						}
						//enforce the total pool: trim allocations (deterministic order) until within AAtotal
						$this->AAtotal_used = 0;
						foreach($this->allocatedAA as $weaponClass=>$allocated){
							$this->AAtotal_used += $allocated;
						}
						if ($this->AAtotal_used > $this->AAtotal){
							foreach($this->allocatedAA as $weaponClass=>$allocated){
								while ($this->AAtotal_used > $this->AAtotal && $this->allocatedAA[$weaponClass] > 0){
									$this->allocatedAA[$weaponClass]--;
									$this->AAtotal_used--;
								}
							}
						}
						//endof clamp

						foreach($this->availableAA as $weaponClass=>$ptsAvailable){
							if (isset($this->allocatedAA[$weaponClass])){
								$ptsSet = $this->allocatedAA[$weaponClass];
							} else $ptsSet = 0;
							if ($gameData->turn==1){ //availability is changed here in turn 1 only
								$notekey = 'available;' . $weaponClass;
								$noteHuman = 'Adaptive Armor available';
								$noteValue = $ptsAvailable;
								$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$noteValue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue
							}
							$notekey = 'set;' . $weaponClass;
							$noteHuman = 'Adaptive Armor set';
							$noteValue = $ptsSet;
							$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$noteValue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue
						}
					}
					
				break;
				
				case 4: //firing phase
					//after re-reading ship control sheets: AA should be at flight level unless fighter in question is SuperHeavy
					if((!($ship instanceOf FighterFlight)) || (!$ship->superheavy)){ 
						foreach($ship->systems as $system) foreach($system->damage as $dmg) if($dmg->turn==$gameData->turn){//damage suffered this turn
							if($dmg->damage > $dmg->armour){ //actual damage was caused!
								$weaponClass = $dmg->damageclass;
								if(!isset($this->availableAA[$weaponClass])){ //this type of damage wasn't encountered yet!
									$this->availableAA[$weaponClass] = 0;
									$this->allocatedAA[$weaponClass] = 0;
								}
								if($this->availableAA[$weaponClass] < $this->AApertype){ //maximum not yet unlocked!
									$this->availableAA[$weaponClass]+=1;
								}
							}
						}
					}else{ //for SUPERHEAVY fighter flight - only damage of a particular fighter counts!
						$relevantFtr = $ship->getFighterBySystem($this->id);
						foreach($relevantFtr->damage as $dmg) if($dmg->turn==$gameData->turn){//damage suffered this turn
							if($dmg->damage > $dmg->armour){ //actual damage was caused!
								$weaponClass = $dmg->damageclass;
								if(!isset($this->availableAA[$weaponClass])){ //this type of damage wasn't encountered yet!
									$this->availableAA[$weaponClass] = 0;
									$this->allocatedAA[$weaponClass] = 0;
								}
								if($this->availableAA[$weaponClass] < $this->AApertype){ //maximum not yet unlocked!
									$this->availableAA[$weaponClass]+=1;
								}
							}
						}
					}
					//AND PREPARE APPROPRIATE NOTES!
					//	'preallocatedUsed' public $AApreallocated_used = 0;	
					//$notekey = 'preallocatedUsed';
					//$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,'Adaptive Armor: preallocated points used',$this->AApreallocated_used);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue
					foreach($this->availableAA as $weaponClass=>$ptsAvailable){
						//	'available;dmgType' public $availableAA = array(); //AA points available for allocation for given damage type
						//	'set;dmgType' public $allocatedAA = array(); //AA points allocated for given damage type
						if (isset($this->allocatedAA[$weaponClass])){
							$ptsSet = $this->allocatedAA[$weaponClass];
						} else $ptsSet = 0;
						$notekey = 'available;' . $weaponClass;
						$noteHuman = 'Adaptive Armor available';
						$noteValue = $ptsAvailable;
						$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$noteValue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue
						$notekey = 'set;' . $weaponClass;
						$noteHuman = 'Adaptive Armor set';
						$noteValue = $ptsSet;
						$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$noteValue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue
					}
					break;
		}
	} //endof function generateIndividualNotes
	
	/*act on notes just loaded - to be redefined by systems as necessary
	 - fill $allocation table
	*/
	public function onIndividualNotesLoaded($gamedata){
		foreach ($this->individualNotes as $currNote){ //assume ASCENDING sorting - so enact all changes as is
			$explodedKey = explode ( ';' , $currNote->notekey ) ;//split into array: [area;value] where area denotes action, value - damage type (typically) 
			switch($explodedKey[0]){
				/* always 0 for starters and max for further turns - no need to save or read this information
				case 'preallocatedUsed': //total number of preallocatable points that were actually preallocated
					$this->AApreallocated_used = $currNote->notevalue;
					break;
				*/
				case 'available': //total number of points available for assignment for a given damage type
					$this->availableAA[$explodedKey[1]] = $currNote->notevalue;
					break;
				case 'set': //total number of points assigned for a given damage type
					$this->allocatedAA[$explodedKey[1]] = $currNote->notevalue;
					break;				
			}
		}
		//and immediately delete notes themselves, they're no longer needed (this will not touch the database, just memory!)
		$this->individualNotes = array();
		//calculate $this->AAtotal_used, too!
		$this->AAtotal_used = 0;
		foreach($this->allocatedAA as $dmgType=>$countAllocated){
			$this->AAtotal_used += $countAllocated;
		}
	} //endof function onIndividualNotesLoaded
	
	
    public function setSystemDataWindow($turn){
        parent::setSystemDataWindow($turn); 
		$this->data["Total AA Assigned"] =  $this->AAtotal_used . '/' . $this->AAtotal;
		$this->data[" - Maximum per weapon type"] =  $this->AApertype;
		//$this->data[" - Pre-assigned Amount"] =  $this->AApreallocated_used . ' out of ' . $this->AApreallocated;
		$this->data[" - Pre-assigned remaining"] =  $this->AApreallocated - $this->AApreallocated_used;		
		foreach($this->allocatedAA as $dmgType=>$AAallocated){
			$AAavailable = $this->availableAA[$dmgType];
			//$this->data[' - '.$dmgType] =  $AAallocated . '/' . $AAavailable;
			$this->data[' - '.$dmgType] =  $AAallocated;
		}
        $this->data["Special"] = "This system is responsible for Adaptive Armor settings management.";	   
        $this->data["Special"] .= "<br>You may assign AA points in Initial Orders phase.";
        $this->data["Special"] .= "<br>Pre-assigned AA points may only be used on the turn this ship deploys.";
        $this->data["Special"] .= "<br>AA points set in previous turns cannot be unassigned.";
        $this->data["Special"] .= "<br>AA points are unlocked individually down to superheavy fighters - lighter craft unlock AA points as whole flights. Assignment is always individual.";
    }
	
	/*always redefine $this->data for AA controller! A lot of variable information goes there...*/
	public function stripForJson(){
        $strippedSystem = parent::stripForJson();
        $strippedSystem->data = $this->data;
        if (isset($this->allocatedAA) && !empty($this->allocatedAA)) {
            $strippedSystem->allocatedAA = $this->allocatedAA;
        }        		
        //$strippedSystem->allocatedAA = $this->allocatedAA;
        if (isset($this->availableAA) && !empty($this->availableAA)) {
            $strippedSystem->availableAA = $this->availableAA;
        }  		
        //$strippedSystem->availableAA = $this->availableAA;
        if (isset($this->currchangedAA) && !empty($this->currchangedAA)) {
            $strippedSystem->currchangedAA = $this->currchangedAA;
        }  			
        //$strippedSystem->currchangedAA = $this->currchangedAA;
        if (isset($this->AAtotal_used) && !empty($this->AAtotal_used)) {
            $strippedSystem->AAtotal_used = $this->AAtotal_used;
        }  		
        //$strippedSystem->AAtotal_used = $this->AAtotal_used;
        if (isset($this->AApreallocated_used) && !empty($this->AApreallocated_used)) {
            $strippedSystem->AApreallocated_used = $this->AApreallocated_used;
        }  				
        //$strippedSystem->AApreallocated_used = $this->AApreallocated_used;
		
        return $strippedSystem;
    }
	
	public function doIndividualNotesTransfer(){
		//data received in variable individualNotesTransfer, further functions will look for it in currchangedAA
		if(is_array($this->individualNotesTransfer))	$this->currchangedAA = $this->individualNotesTransfer; //else there's nothing relevant there
		$this->individualNotesTransfer = array(); //empty, just in case
	}		
	
	//returns protection allocated for a given dmg class
	public function getArmourValue($weaponClass){
		$armour = 0;
		if (isset($this->allocatedAA[$weaponClass])) $armour = $this->allocatedAA[$weaponClass];
		return $armour;
	}
							
} //endof AdaptiveArmorController



class DiffuserTendril extends ShipSystem{
    public $name = "DiffuserTendril";
    public $displayName = "Diffuser Tendril";
    public $primary = true;
	public $isPrimaryTargetable = false; //shouldn't be targetable at all, in fact!
	public $isTargetable = false; //cannot be targeted ever!
    public $iconPath = "EnergyDiffuserTendril.png";
	protected $doCountForCombatValue = false; //don't count when estimating remaining combat value - in this case it's not a technical system, but still one that will regenerate by itself during combat
    
	//Diffuser Tendrils cannot be repaired at all!
	public $repairPriority = 0;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired

    
    function __construct($maxhealth, $side = 'R'){ //everything is done in the diffuser, Tendrils basically just are! - L/R suggests whether to use left or right graphics
		$this->iconPath = 'EnergyDiffuserTendril' . $side;
		if($maxhealth < 15){ //small
			$this->iconPath .= "1";
		}
		else if($maxhealth < 30){//medium
			$this->iconPath .= "2";
		} else{//large!
			$this->iconPath .= "3";
		}
		$this->iconPath .= ".png";
		parent::__construct(0, $maxhealth, 0, 0);
		
		$this->output=$maxhealth;//output is displayed anyway, make it show something useful...
	}
	

	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);   
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
		$this->data["Special"] .= "Used to store absorbed energy from hits.<br>It is here for visual (and technical) purposes only. It's part of Energy Diffuser system.";
		
		$freeCapacity = $this->maxhealth - $this->getUsedCapacity();
		$this->outputDisplay = $freeCapacity . '/' . $this->maxhealth;//override on-icon display default
	}	
	
	public function getRemainingCapacity(){
		return $this->getRemainingHealth();
	}
	
	public function getUsedCapacity(){
		return $this->getTotalDamage();
	}
	
	public function absorbDamage($ship,$gamedata,$value, $fireOrderid = -1){ //or dissipate, with negative value
		//$fireOrderid ties what the tendril soaked to the shot that caused it. The combat log finds a
		//shot's damage by matching entries on fireorderid (weaponManager.getDamagesCausedBy), so
		//without it a fully absorbed shot read as "damaged for 0" and looked like nothing happened.
		//Dissipation and the docking-reabsorption in HangarOps pass no id and stay unlinked (-1), so
		//they are never reported as damage from a shot.
		$damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $this->id, $value, 0, 0, $fireOrderid, false, false, "Absorb/Dissipate!", "Tendril");
		$damageEntry->updated = true;
		$this->damage[] = $damageEntry;
	}
	
		public function stripForJson(){
			$strippedSystem = parent::stripForJson();
			$strippedSystem->outputDisplay = $this->outputDisplay; //make sure that actual output is actually sent to front end...
			//A tendril bought as an Extra Tendrils enhancement was not on the hull when the static
			//blueprint was built, so the client has nothing to merge this onto - send the blueprint
			//fields (capacity, artwork, section, arcs) with it. See ShipSystem::$addedByEnhancement.
			if ($this->addedByEnhancement) $strippedSystem = $this->addBlueprintFieldsForJson($strippedSystem);
			return $strippedSystem;
		}
}//endof class DiffuserTendril


//fighter systems don't get damaged - so fighter tendrils need to store damage by way of notes
class DiffuserTendrilFtr extends DiffuserTendril{
    public $name = "DiffuserTendrilFtr";
    public $displayName = "Diffuser Tendril";
	
	private $usedCapacityTotal=0;
	private $thisTurnEntries=array();
    
	//Diffuser Tendrils cannot be repaired at all!
	public $repairPriority = 0;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired

    
	public function setSystemDataWindow($turn){
		//add information about damage stored - ships do have visual reminder about it, but fighters do not!
		parent::setSystemDataWindow($turn); 
		$freeCapacity = $this->maxhealth - $this->getUsedCapacity();
		//$this->data["Capacity"] = $this->getUsedCapacity() . '/' . $this->maxhealth;
		$this->data["Capacity available/max"] = $freeCapacity . '/' . $this->maxhealth;
		
		$this->outputDisplay = $freeCapacity . '/' . $this->maxhealth;//override on-icon display default
	}	
	
	/*always redefine $this->data due to current capacity info*/
	public function stripForJson(){
        $strippedSystem = parent::stripForJson();
        $strippedSystem->data = $this->data;		
        return $strippedSystem;
    }
	
	
	public function getRemainingCapacity(){
		return $this->maxhealth - $this->usedCapacityTotal;
	}
	
	public function getUsedCapacity(){
		return $this->usedCapacityTotal;
	}
	
	//$fireOrderid is accepted only to keep the signature compatible with the parent - a fighter
	//tendril stores its absorption as individual notes, not as damage entries, so there is nothing
	//for the combat log to link to. Fighter diffuser absorption stays invisible in the log.
	public function absorbDamage($ship,$gamedata,$value, $fireOrderid = -1){ //or dissipate, with negative value
		$this->usedCapacityTotal += $value; //running count
		$this->thisTurnEntries[] = $value; //mark for database
	}
	
	
	/* this method generates additional non-standard informaction in the form of individual system notes
	in this case: 
	 - Firing phase: add information on stored/dissipated energy (every entry separately)
	*/
    public function generateIndividualNotes($gameData, $dbManager){ //dbManager is necessary for Initial phase only
		$ship = $this->getUnit();
		switch($gameData->phase){
				case 4: //firing phase
					foreach($this->thisTurnEntries as $tte){					
						$notekey = 'absorb';
						$noteHuman = 'Tendril absorbed or dissipated';
						$noteValue = $tte;
						$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$noteValue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue
					}
					break;
		}
	} //endof function generateIndividualNotes
	
	/*act on notes just loaded - to be redefined by systems as necessary
	here:
	 - fill $usedCapacityTotal value
	*/
	public function onIndividualNotesLoaded($gamedata){
		$this->usedCapacityTotal = 0;
		foreach ($this->individualNotes as $currNote){ //assume ASCENDING sorting 
			$explodedKey = explode ( ';' , $currNote->notekey ) ;//split into array: [area;value] where area denotes action, value - damage type (typically) 
			switch($currNote->notekey){
				case 'absorb': //absorbtion or dissipation of energy
					$this->usedCapacityTotal += $currNote->notevalue;
					break;		
			}
		}
		//and immediately delete notes themselves, they're no longer needed (this will not touch the database, just memory!)
		$this->individualNotes = array();
	} //endof function onIndividualNotesLoaded

}//endof class DiffuserTendrilFtr



/*Shadow damage absorbtion system; remember to add Tendrils - largest first!*/
class EnergyDiffuser extends ShipSystem{
    public $name = "EnergyDiffuser";
    public $displayName = "Energy Diffuser";
    public $iconPath = "EnergyDiffuser.png";
    public $primary = true;
	public $isPrimaryTargetable = false; //like other core systems
	public $tendrils = array();//tendrils belonging to this Diffuser
	
    
	//EnergyDiffuser has very high repair priority, being the core defensive system!
	public $repairPriority = 9;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
    
    protected $possibleCriticals = array(
		11=>"TendrilDestroyed",
		16=>array("TendrilDestroyed", "OutputReduced1"),
		20=>array("TendrilDestroyed", "OutputReduced2", "TendrilCapacityReduced"),
		25=>array("TendrilDestroyed", "TendrilDestroyed", "OutputReduced3", "TendrilCapacityReduced", "TendrilCapacityReduced")
	);
/* below list of critical effects:	
11-15: No effect to the diffuser. However, one of the
attached segments is destroyed (player’s choice). Mark
an X in its box to indicate this. The pilot suffers “pain” (see
10.18.10) on the next turn equal to the segment’s absorption
capacity (treated as damage, even though no damage points
are actually marked off anywhere in the ship).
16-19: Lose a segment as described under 11-15, and
reduce the diffuser’s discharge rate by 1.
20-24: Lose a segment, reduce the discharge rating by
2 and lower the absorption ratings of all remaining segments
by 2.
25+: Lose two segments, reduce the discharge rating by
3 and lower the absorption ratings of all remaining segments
by 4.
*/
	
	
	function __construct($armour, $maxhealth, $dissipation, $startArc, $endArc){
        // dissipation is handled as output.
        parent::__construct($armour, $maxhealth, 0, $dissipation);
        
        $this->startArc = (int)$startArc;
        $this->endArc = (int)$endArc;
		
		if ($this->getUnit() instanceOf FighterFlight){ //for fighters - no criticals of course ;)
			$this->possibleCriticals = array();
		}
    }
	
	function addTendril($tendril){
		if($tendril) $this->tendrils[] = $tendril;
	}

	/*Mount a tendril keeping the list in capacity order, largest first - for tendrils added after
	  the hull was built (Extra Tendrils; every constructor-mounted one simply lists them in order
	  and uses addTendril). The ORDER of this list is read, it is not decoration: criticalPhaseEffects
	  destroys the LAST surviving tendril on a TendrilDestroyed critical - i.e. the smallest - and
	  dissipates from the front. Appending blindly would make a fresh 15-capacity tendril the first
	  thing a critical strips off the ship, and leave the biggest one waiting on dissipation.*/
	function addTendrilSorted($tendril){
		if(!$tendril) return;
		foreach($this->tendrils as $index => $existing){
			if($tendril->maxhealth > $existing->maxhealth){
				array_splice($this->tendrils, $index, 0, array($tendril));
				return;
			}
		}
		$this->tendrils[] = $tendril; //smallest (or the only one) - goes last
	}

	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);     
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
		$this->data["Special"] .= "Absorbs energy from hits as long as there is storage capacity available (Diffuser Tendrils).";
		$this->data["Special"] .= "<br>Tries not to absorb if protected system would have been destroyed anyway without overkilling (eg. very strong Piercing or Matter fire hitting small systems).";
		$this->data["Special"] .= "<br>Dissipates energy from Tendrils in Critical phase.";
	}	
	
	//effects that happen in Critical phase (after criticals are rolled) - dissipation and actual loss of tendrils due to critical received
	public function criticalPhaseEffects($ship, $gamedata){
		
		parent::criticalPhaseEffects($ship, $gamedata);//Call parent to apply effects like Limpet Bore.			
		
		if($this->isDestroyed()) return; //destroyed system does not work... but other critical phase effects may work even if destroyed!
		
		$ship = $this->getUnit();
		$pilot = $ship->getSystemByName("CnC");
				
		//1. if THIS TURN TendrilDestroyed critical was added - mark last tendril destroyed
		foreach ($this->criticals as $crit) if(($crit instanceof TendrilDestroyed) && ($crit->turn==$gamedata->turn)) {
			$lastTendril = null;
			foreach($this->tendrils as $tendril) if(!$tendril->isDestroyed()){
				$lastTendril = $tendril;
			}
			if($lastTendril){ ///no need to redefine for fighter - there criticals just won't happen
				$damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $lastTendril->id, 0, 0, 0, -1, true, false, "DestroyTendril!", "Destruction");
				$damageEntry->updated = true;
				$lastTendril->damage[] = $damageEntry;
				
				//add pain to pilot, too!				
				if($pilot && ($pilot instanceOf ShadowPilot) ){//check whether it's actually a Pilot - Young races use ships equipped with Shadowtech, but without Pilots (so can't feel pain)
					$onePainPer = 10; //1 point of pain per how many damage points?
					if ($ship->factionAge > 3) { //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
						$onePainPer = 20;//slow-grown Primordial ships are more resistant to pain		
					}
					$painSuffered = ceil( $lastTendril->maxhealth/$onePainPer ); //let's round that up...
					for($i=1;$i<=$painSuffered;$i++){
						$crit = new ShadowPilotPain(-1, $ship->id, $pilot->id, 'ShadowPilotPain', $gamedata->turn+1, $gamedata->turn+1);
						$crit->updated = true;
						$pilot->criticals[] =  $crit;
					}
				}
				
			}
		}
		
		//2. dissipate stored energy (in order of tendrils - largest are first, and usually freeing up largest ones is best)
		$dissipationAvailable = $this->getOutput();
		foreach($this->tendrils as $tendril) if(!$tendril->isDestroyed()){				
			$toDissipate = min($dissipationAvailable, $tendril->getUsedCapacity());
			if($toDissipate > 0){
				$dissipationAvailable -= $toDissipate;
				//dissipation == undamage
				$tendril->absorbDamage($ship,$gamedata,-$toDissipate);
			}
		}		
		
	} //endof function criticalPhaseEffects
	
	
	//function estimating how good this Diffuser is at stopping damage;
	//in case of diffuser, its effectiveness equals largest shot it can stop, with tiebreaker equal to remaining total capacity
	//this is for recognizing it as system capable of affecting damage resolution and choosing best one if multiple Diffusers can protect
	public function doesProtectFromDamage($expectedDmg, $systemProtected = null, $damageWasDealt = false, $inflictingShots = 1, $isUnderShield = false) {
		$remainingCapacity = 0;
		$totalCapacity = 0;
		$largestCapacity = 0;
				
		//check capacity reduction due to criticals...
		$reduction = $this->hasCritical('TendrilCapacityReduced')*2;

		foreach($this->tendrils as $tendril) if(!$tendril->isDestroyed()){
			$totalCapacity += $tendril->maxhealth;
			$tendrilCapacity = $tendril->getRemainingCapacity()-$reduction;
			if($tendrilCapacity>0){
				$remainingCapacity += $tendrilCapacity;
				$largestCapacity = max($tendrilCapacity,$largestCapacity);
			}
		}

		//If this is a multi-shot volley, we need to calculate the average protection.
		if ($inflictingShots > 1) {
			$simulatedTendrils = array();
			foreach($this->tendrils as $tendril) if(!$tendril->isDestroyed()){
				$simulatedTendrils[$tendril->id] = max(0, $tendril->getRemainingCapacity()-$reduction);
			}

			$totalProtection = 0;
			for($i=0; $i<$inflictingShots; $i++){
				$bestCapacity = 0;
				$bestTendrilId = -1;
				foreach($simulatedTendrils as $id=>$cap){
					if($cap > $bestCapacity){
						$bestCapacity = $cap;
						$bestTendrilId = $id;
					}
				}
				if($bestCapacity > 0){
					$absorbed = min($expectedDmg, $bestCapacity);
					$totalProtection += $absorbed;
					$simulatedTendrils[$bestTendrilId] -= $absorbed;
				}
			}
			return $totalProtection / $inflictingShots;
		}
		
		//tiebreaker: less filled (proportionally), to try and split load if possible
		$protectionValue = $largestCapacity;
		$protectionValue = min($largestCapacity,$expectedDmg);//being able to protect over expected damage is irrelevant - while ratio of being filled is!
		if($totalCapacity > 0) $protectionValue += $remainingCapacity/$totalCapacity;
		return $protectionValue;
	}
	//actual protection
	public function doProtect($gamedata, $fireOrder, $target, $shooter, $weapon, $systemProtected, $effectiveDamage,$effectiveArmor){ //hook for actual effect of protection - return modified values of damage and armor that should be used in further calculations
		$returnValues=array('dmg'=>$effectiveDamage, 'armor'=>$effectiveArmor);
		$damageToAbsorb=$effectiveDamage-$effectiveArmor;
		$damageAbsorbed=0;
		
		if($damageToAbsorb<=0) return $returnValues; //nothing to absorb
		
		$mostSuitableAbsorbtion=0;
		$mostSuitableTendril=null;		
		//check capacity reduction due to criticals...
		$reduction = $this->hasCritical('TendrilCapacityReduced')*2;

		foreach($this->tendrils as $tendril) if(!$tendril->isDestroyed()){
			$tendrilCapacity = $tendril->getRemainingCapacity() - $reduction;
			if($tendrilCapacity>0){ //else it's useless ATM
				if ($mostSuitableAbsorbtion < $damageToAbsorb){ //not found a tendril able to accept entire damage yet, looking for something larger
					if ($tendrilCapacity >= $mostSuitableAbsorbtion) { //new one is more suitable (all other things equal later tendril is more suitable)
						$mostSuitableAbsorbtion = $tendrilCapacity;
						$mostSuitableTendril = $tendril;
					}
				}else{ //appropriate tendril already found, looking for something better suited - eg. smaller but still able to accept entire damage
					if (($tendrilCapacity <= $mostSuitableAbsorbtion) && ($tendrilCapacity >= $damageToAbsorb)) { //new one is more suitable (all other things equal later tendril is more suitable)
						$mostSuitableAbsorbtion = $tendrilCapacity;
						$mostSuitableTendril = $tendril;
					}
				}
			}
		}	
		
		$noOverkill = (!$weapon->doOverkill) && ($weapon->noOverkill || ($weapon->damageType == 'Piercing'));
		if($noOverkill){//shot is incapable of overkilling - reducing it would not matter if it doesn't prevent destruction of system hit
			$remainingHealth = $systemProtected->getRemainingHealth();
			if ($remainingHealth+$mostSuitableAbsorbtion <= $damageToAbsorb) return $returnValues; //any absorbtion would be futile and just fill tendril
		}
		
		if($mostSuitableAbsorbtion>0){ //appropriate tendril found!
			$damageAbsorbed=min($damageToAbsorb,$mostSuitableAbsorbtion);
			$returnValues['dmg']=$effectiveDamage-$damageAbsorbed;
			$mostSuitableTendril->absorbDamage($target,$gamedata,$damageAbsorbed, $fireOrder->id);//link to the shot so the combat log can report it
		}
		
		return $returnValues;
	}
} //endof EnergyDiffuser






//self repair system - implemented as weapon for simplicity; does repair damage caused in current turn, too (but only earlier crits)
//othrwise it's close to the original
class SelfRepair extends ShipSystem{
	public $name = "SelfRepair";
	public $displayName = "Self Repair";
	public $iconPath = "SelfRepair.png";
    public $primary = true;
	
		
	public $output = 0;
	public $maxRepairPoints=0;//maximum points that can be repaired during battle
	public $usedRepairPoints=0;//repair points already used
	public $usedThisTurn=0;
	public $priorityChanges = array();//priority overrides - in format systemID->priority; 0 don't repair, 20 priority repair, -1 cancel override :)
	public $currentlyDisplayedSystem = -1; //for front end only

	/*Kirishiac Heavy Orbital support: a Self Repair mounted ON an orbital may only service the
	orbital's own systems (list of system ids recomputed on notes-load by the orbital; docked =
	weapon + combined Structure block, deployed = orbital + weapon + itself) and works at DOUBLE
	rate while the orbital is docked. null/false = standard whole-ship Self Repair.*/
	public $repairRestrictedTo = null; //array of system ids this system may service; null = no restriction
	public $outputDoubled = false; //docked Heavy Orbital: internal self repair works at double rate
	public $linkedOrbital = null; //set by KirishiacHeavyOrbital::addOrbitalSystem - null on standard mounts
      
	
	//SelfRepair itself is most important to be repaired - as it's the condition of further repairs being effected!
	public $repairPriority = 10;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
	
    public $boostable = false;
    public $maxBoostLevel = 0;
    public $boostEfficiency = 0; 
    
	
 	protected $possibleCriticals = array( 
            19=>"OutputHalved"
	);
			
	function __construct($armour, $maxhealth, $output)
	{
		//power requirement is 0, health is always defined by constructor, as is output - but they cannot be <1!
		if ( $maxhealth <1 ) $maxhealth = 1;
		if ( $output <1 ) $output = 1; //base output cannot be <1
		parent::__construct($armour, $maxhealth, 0, 0, 0);
		$this->output = $output; //after parent - weapon has no output and passes 0 to system creation
		$this->maxRepairPoints = $maxhealth*10;
	}

	/* Current battle repair-point ceiling. maxRepairPoints (= maxhealth*10) is the UNDAMAGED
	ceiling; a damaged Self Repair loses capacity proportionally, so the live ceiling scales
	with remaining health (getRemainingHealth()*10). If usedRepairPoints already exceeds this
	reduced ceiling that is fine - the system simply can't spend any more this game. A destroyed
	SR has 0 remaining health, hence 0 ceiling. Mirrored client-side for the display. */
	public function getCurrentMaxRepairPoints(){
		return $this->getRemainingHealth() * 10;
	}


	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);  
		//some effects should originally work for current turn, but it won't work with FV handling of ballistics. Moving everything to next turn.
		//it's Ion (not EM) weapon with no special remarks regarding advanced races and system - so works normally on AdvArmor/Ancients etc
		$this->data["Repair points (used/max)"] = $this->usedRepairPoints . "/" . $this->getCurrentMaxRepairPoints(); //max shrinks with damage to this SR
		$this->data["Special"] = "At end of turn phase automatically repairs damage to vessel. Cannot repair destroyed structure blocks or Self Repair systems.";
		$this->data["Special"] .= "<br>Player may set repair priorities using the 'Manage Repair Queue' menu during Initial Orders.";		
		$this->data["Special"] .= "<br>Default Priority: Fix criticals, revive destroyed systems, finally heal damaged systems.";
//		$this->data["Special"] .= "<br>Core (and other particularly important) systems are repaired first, then weapons, then other systems.";
		$this->data["Special"] .= "<br>Will not fix criticals and damage caused in current turn.";
		if ($this->linkedOrbital !== null){ //mounted on a Kirishiac Heavy Orbital
			$this->data["Special"] .= "<br>Mounted on " . $this->linkedOrbital->displayName . ": only repairs systems on the orbital itself.";
			$this->data["Special"] .= "<br>While the orbital is docked output is DOUBLED.";
		}
	}

	
		/* Repair-point cost of clearing a critical. B5W: all crits cost 1 self-repair point
		except C&C criticals, which cost 4. We scope "C&C" to the CnC class hierarchy
		(CnC + OSATCnC/ProtectedCnC/ThirdspaceCnC/PakmaraCnC/FlagBridge/ShadowPilot);
		SecondaryCnC is a damage-soak proxy, not the command system, so it is excluded.
		Mirrored client-side in SelfRepairList.getEffectiveCriticalRepairCost. */
	public static function getEffectiveCriticalRepairCost($critDmg, $system){
		if ($system instanceof CnC) return 4;
		return $critDmg->repairCost;
	}

	    /* sorts generated repair queue */
    public static function sortUnifiedRepairQueue($a, $b){
		if($a['priority'] !== $b['priority']){
            return $b['priority'] - $a['priority']; //higher priority first!
        }

        //Tie-break 1: an EXPLICIT player override beats an implicit/auto priority (e.g. the
        //+10 destroyed bump). A player who deliberately sets a value to N means it to sit above
        //systems that merely landed on N automatically - without this, a destroyed low-priority
        //system could tie a promoted Structure and win purely on the id tiebreak below.
        $aOv = !empty($a['overridden']);
        $bOv = !empty($b['overridden']);
        if($aOv !== $bOv){
            return $aOv ? -1 : 1; //overridden entry first
        }

        //Deterministic Sort: System ID then SubID
        if($a['id'] !== $b['id']){
            return $a['id'] - $b['id'];
        }

        return $a['subId'] - $b['subId'];
    }
	
	private function getBoostLevel($turn){
            $boostLevel = 0;
            foreach ($this->power as $i){
                    if ($i->turn != $turn)
                            continue;
                    if ($i->type == 2){
                            $boostLevel += $i->amount;
                    }
            }
            return $boostLevel;
        }	
        
	public function getEffectiveOutput($ship){
		$turn = TacGamedata::$currentTurn;
      	$boost = $this->getBoostLevel($turn);
 	    $output = $this->getOutput();
		$bonus = 0;
		if($ship->faction == "Vorlon Empire"){
			$capacitor = $ship->getSystemByName("PowerCapacitor");			
			$doubled = $capacitor->isDoubled();			
			if($capacitor && $doubled == true) $bonus = $output; //Double output

		}				
      	$effectiveoutput = $output + $boost + $bonus;
		if ($this->outputDoubled) $effectiveoutput += $output; //docked Kirishiac Heavy Orbital - doubled base output

      	return $effectiveoutput;
		}		
	
	public function criticalPhaseEffects($ship, $gamedata)
    { 
    
		parent::criticalPhaseEffects($ship, $gamedata);//Call parent to apply effects like Limpet Bore.	  
    
		if($this->isDestroyed()) return; //destroyed system does not work... but other critical phase effects may work even if destroyed!
		
		//how many points are available? Ceiling scales with damage to THIS Self Repair
		//(getCurrentMaxRepairPoints = remaining health * 10); if already overspent vs the
		//reduced ceiling, availableRepairPoints goes <=0 and nothing is repaired.
		$availableRepairPoints = $this->getCurrentMaxRepairPoints() - $this->usedRepairPoints;
		$availableRepairPoints = min($availableRepairPoints,$this->getEffectiveOutput($ship)); //no more than remaining points, no more than actual system repair capability
		
        $repairQueue = array();
        $ship=$this->getUnit();

        // 1. Gather Systems
		foreach($ship->systems as $system){
			if ( $system->maxhealth <= $system->getRemainingHealth() ) continue; //skip undamaged systems...
			if ( $system instanceof SelfRepair ) continue; //Self Repair cannot repair Self Repair - not itself, not another SR on the ship
			if ( $system->repairPriority < 1 ) continue; //base priority 0 = cannot be repaired, even with a player override
			if ( $system->privateRepairOnly && ($this->repairRestrictedTo === null) ) continue; //deployed Heavy Orbital's systems: out of the mother ship's reach - only its own (restricted) on-board SR may service them
			if ( ($this->repairRestrictedTo !== null) && (!in_array($system->id, $this->repairRestrictedTo)) ) continue; //restricted Self Repair (Kirishiac Heavy Orbital) - may only service its orbital's systems
			//(overrides can only legitimately exist on repairable systems; guards systems whose priority is
			//DYNAMIC - e.g. a Kirishiac orbital that was overridden while docked and has since redeployed)
			//priority overrides...
            $prio = $system->repairPriority;
			$isOverridden = false; //explicit player override wins ties vs auto/bumped priorities
			if(array_key_exists($system->id, $this->priorityChanges) && ($this->priorityChanges[$system->id]>=0)){
				$prio = $this->priorityChanges[$system->id];
				$isOverridden = true;
			}

            //skip systems attached to destroyed structure blocks...
			if($prio<1) continue;//skip systems that cannot be repaired
			if(!($system instanceOf Structure)){ //non-Structure system - cannot repair if attached to destroyed Structure block
				$strBlock = $ship->getStructureSystem($system->getStructureLocation()); //home block may differ from display section (Kirishiac orbitals)
				if($strBlock->isDestroyed($gamedata->turn)) continue;
			}else{ //Structure block - cannot repair if destroyed
				if($system->isDestroyed($gamedata->turn)) continue; //cannot repair destroyed Structure
			}
			
			//destroyed systems get first priority
			if( ($prio <=10) //only systems whose priority wasn't modified yet
				&& ($system->isDestroyed($gamedata->turn))
				&& (!array_key_exists($system->id, $this->priorityChanges)) //AND was NOT manually modified by player!
			){
				$prio += 10;
			}
			
            $currentDamage = $system->maxhealth - $system->getRemainingHealth();
            $causedThisTurn = $system->damageReceivedOnTurn($gamedata->turn);
            $toBeRepaired = $currentDamage - $causedThisTurn;
            
            if ($toBeRepaired > 0) {
			    $repairQueue[] = array(
                    'type' => 'system',
                    'obj' => $system,
                    'priority' => $prio,
                    'overridden' => $isOverridden, // explicit override wins ties (see sortUnifiedRepairQueue)
                    'cost' => $toBeRepaired, // Needed for unified repair logic
                    'maxhealth' => $system->maxhealth, // For sorting
                    'id' => $system->id, // For sorting
                    'subId' => 0 // SubID for sorting
                );
            }		
		}

        // 2. Gather Criticals
        foreach ($ship->systems as $systemToRepair){
            // Filtering similar to systems logic but applicable to parent system of critical
            //$availableRepairPoints check moved to execution loop

            if ($systemToRepair instanceof SelfRepair) continue; //Self Repair cannot repair Self Repair (incl. clearing its criticals)
            if ($systemToRepair->repairPriority<1) continue;//skip systems that cannot be repaired
            if ( $systemToRepair->privateRepairOnly && ($this->repairRestrictedTo === null) ) continue; //deployed Heavy Orbital's systems: mother ship's SR may not clear their criticals - only its own on-board SR
            if ( ($this->repairRestrictedTo !== null) && (!in_array($systemToRepair->id, $this->repairRestrictedTo)) ) continue; //restricted Self Repair (Kirishiac Heavy Orbital)
            if ($systemToRepair->isDestroyed($gamedata->turn)) continue;//don't repair criticals on destroyed system...

             // CALCULATE BASE PRIORITY FOR SYSTEM (Needed for Crit Default)
            $sysPrio = $systemToRepair->repairPriority;
            // Override removed to decouple system/critical priorities
			// if(array_key_exists($systemToRepair->id, $this->priorityChanges) && ($this->priorityChanges[$systemToRepair->id]>=0)){
			// 	$sysPrio = $this->priorityChanges[$systemToRepair->id];
			// }	

            foreach($systemToRepair->criticals as $critDmg) {
                if($critDmg->repairPriority<1) continue;//if critical cannot be repaired
                if($critDmg->turn >= $gamedata->turn) continue;//don't repair criticals caused in current (or future!) turn
                if ($critDmg->oneturn || ($critDmg->turnend > 0)) continue;//temporary criticals (or those already repaired) also cannot be repaired
                
                $critPrio = $critDmg->repairPriority;

                //priority override?
                $critOverridden = false; //explicit player override wins ties vs auto priorities
                $compKey = $systemToRepair->id . '-' . $critDmg->id;
                if(array_key_exists($compKey, $this->priorityChanges) && ($this->priorityChanges[$compKey]>=0)){
                     $critPrio = $this->priorityChanges[$compKey];
                     $critOverridden = true;
                }else{
                    if($critPrio<10) $critPrio += $sysPrio; //modify priority by priority of system critical is on!
                }

                $repairQueue[] = array(
                    'type' => 'critical',
                    'obj' => $critDmg,
                    'sys' => $systemToRepair, // We need the system object to execute repair
                    'priority' => $critPrio,
                    'overridden' => $critOverridden, // explicit override wins ties (see sortUnifiedRepairQueue)
                    'cost' => self::getEffectiveCriticalRepairCost($critDmg, $systemToRepair), //C&C crits cost 4 (B5W), everything else its own repairCost
                    'id' => $systemToRepair->id, // Fallback ID for sorting
                    'subId' => $critDmg->id // SubID for sorting
                );
            }
        }
		
        // 3. Sort
		usort($repairQueue, [self::class, 'sortUnifiedRepairQueue']);
		
        // 4. Execute Repairs
        foreach($repairQueue as $job) {
             if ($availableRepairPoints < 1) break;//cannot repair anything any longer
             
             if ($job['type'] === 'critical') {
                 $critDmg = $job['obj'];
                 $critCost = $job['cost']; //effective cost (C&C crits = 4, see getEffectiveCriticalRepairCost)
                 // Additional check just in case costs changed logic? No, static data mostly.
                 if ($critCost <= $availableRepairPoints){
                    $system = $job['sys'];
                    $system->repairCritical($critDmg, $gamedata->turn); // Call our new function in shipSystem class
                    $availableRepairPoints -= $critCost;
                    $this->usedThisTurn += $critCost;
                 }
             } else {
                 // System Repair
                 $systemToRepair = $job['obj'];
                 
			    $currentDamage = $systemToRepair->maxhealth - $systemToRepair->getRemainingHealth( );
			    $causedThisTurn = $systemToRepair->damageReceivedOnTurn($gamedata->turn);
			    $toBeRepaired = $currentDamage-$causedThisTurn;
                
                // Re-validate toBeRepaired just in case
			    if($toBeRepaired > 0){ //do repair!
				    $toBeFixed = min($toBeRepaired, $availableRepairPoints);
				    $undestroy = false;
				    if ($toBeFixed>=$currentDamage){ //full health restored!
					    $undestroy=true;
				    }
				    //actual healing entry
				    $damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $systemToRepair->id, -$toBeFixed, 0, 0, -1, false, $undestroy, 'SelfRepair', 'SelfRepair');
				    $damageEntry->updated = true;
				    $systemToRepair->damage[] = $damageEntry;
				    //mark repair points used
				    $availableRepairPoints -= $toBeFixed;
				    $this->usedThisTurn += $toBeFixed;

				    //Check if fully repaired, and if so remove from priority list!
				    if ($systemToRepair->getRemainingHealth() >= $systemToRepair->maxhealth){
					    if(array_key_exists($systemToRepair->id, $this->priorityChanges)){
						    unset($this->priorityChanges[$systemToRepair->id]);
						    //and create note to remove it from DB/Client
						    $notekey = 'override';
						    $noteHuman = 'Repair priority override removed';
						    $noteValue = $systemToRepair->id . ';-1';
						    $this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gamedata->turn,$gamedata->phase,$ship->id,$this->id,$notekey,$noteHuman,$noteValue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue
					    }
				    }
			    }
             }
        }
			
    } //endof function criticalPhaseEffects
	
	

	/* this method generates additional non-standard informaction in the form of individual system notes
	in this case: 
	 - Firing phase: add repair points used to notes (current entry, not total)
	*/
    public function generateIndividualNotes($gameData, $dbManager){ //dbManager is necessary for Initial phase only
		$ship = $this->getUnit();
		switch($gameData->phase){
				case 1: //Initial phase - set new priority overrides! (and ONLY new, don't bother with preexisting ones)
					if($ship->userid == $gameData->forPlayer){ //only own ships, otherwise bad things may happen!
						/*set new overrides, just received from front end - so no need to load old ones at this point*/
						foreach($this->priorityChanges as $systemID=>$priorityValue){							
							$notekey = 'override';
							$noteHuman = 'Repair priority override';
							$noteValue = $systemID . ';' . $priorityValue;
							$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$noteValue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue	
						}
					}
				
				case 4: //firing phase
					if($this->usedThisTurn>0){ //self-repair was actually used this turn!
						$notekey = 'used';
						$noteHuman = 'Self-repair used';
						$noteValue = $this->usedThisTurn;
						$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$noteValue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue
					}
					break;
		}
	} //endof function generateIndividualNotes
	
	/*act on notes just loaded - to be redefined by systems as necessary
	here:
	 - fill $usedRepairPoints value
	*/
	public function onIndividualNotesLoaded($gamedata){
		foreach ($this->individualNotes as $currNote){ //assume ASCENDING sorting 
			$explodedKey = explode ( ';' , $currNote->notekey ) ;//split into array: [area;value] where area denotes action, value - damage type (typically) 
			switch($currNote->notekey){
				case 'used': //self-repair points used in a given turn
					$this->usedRepairPoints += $currNote->notevalue;
					break;		
				case 'override': //priority override for systems
					$explodedOverride = explode ( ';' , $currNote->notevalue ) ;//split into array: [systemID;overriding Priority] 
					if ($explodedOverride[1] >= 0){
						$this->priorityChanges[$explodedOverride[0]] = $explodedOverride[1];
					}else{
						unset($this->priorityChanges[$explodedOverride[0]]);
					}
					break;
			}
		}
		//and immediately delete notes themselves, they're no longer needed (this will not touch the database, just memory!)
		$this->individualNotes = array();
	} //endof function onIndividualNotesLoaded

	public function stripForJson(){
        $strippedSystem = parent::stripForJson();
        $strippedSystem->data = $this->data;
		//$strippedSystem->output = $this->getOutput();	//actual output is constant, and outputMod is correctly shown in front end!
        if (isset($this->priorityChanges) && !empty($this->priorityChanges)) {
            $strippedSystem->priorityChanges = $this->priorityChanges;
        }
        //$strippedSystem->priorityChanges = $this->priorityChanges;
		if ($this->linkedOrbital !== null){ //mounted on a Kirishiac Heavy Orbital - dynamic per-load state
			$strippedSystem->repairRestrictedTo = ($this->repairRestrictedTo !== null) ? array_values($this->repairRestrictedTo) : null; //Manage Repair Queue filter
			$strippedSystem->outputDoubled = $this->outputDoubled;
			$strippedSystem->privateRepairOnly = $this->privateRepairOnly; //deployed: mother ship's SR list excludes it (only its own restricted list may service it)
			$strippedSystem->dockedWithOrbital = (bool)$this->linkedOrbital->activeEffective; //client docked visual (faded icon + cyan healthbar, like the orbital)
		}
        return $strippedSystem;
    }

	/*mounted on a Kirishiac Heavy Orbital: while deployed, overkill passes into the orbital's
	structure (or is lost if the orbital is already gone). While DOCKED this system is still
	hittable (docked sub-chart) but the orbital is part of the hull, so overkill follows the
	normal ship flow (section = combined Structure). Standard mounts behave normally.*/
	public function getOverkillDestination($target){
		if ($this->linkedOrbital === null) return null; //standard mount - normal flow
		if ($this->linkedOrbital->activeEffective) return null; //docked - overkill returns to the ship (combined Structure)
		if ($this->linkedOrbital->isDestroyed() || ($this->linkedOrbital->getRemainingHealth() == 0)) return false; //orbital gone - overkill lost
		return $this->linkedOrbital;
	}
	
	/* data transferred from front end, if any - priority overrides!*/	
	public function doIndividualNotesTransfer(){
		//data received in variable individualNotesTransfer, further functions will look for it in currchangedAA
		if(is_array($this->individualNotesTransfer)){
			foreach($this->individualNotesTransfer as $noteReceived){
				$explodedOverride = explode ( ';' , $noteReceived ) ;//split into array: [systemID;overriding Priority] 
				$this->priorityChanges[$explodedOverride[0]] = $explodedOverride[1]; //here do add ALL values, -1 including - it's necessary so it's later saved to database
			}
		}
		$this->individualNotesTransfer = array(); //empty, just in case
	}		

}//endof class SelfRepair


class ThirdspaceSelfRepair extends SelfRepair{

    public $boostable = true;
    public $maxBoostLevel = 3;
    public $boostEfficiency = 0;
    
    protected $ewBoosted = true;   

	function __construct($armour, $maxhealth, $output, $maxBoost = 0)
	{
		//power requirement is 0, health is always defined by constructor, as is output - but they cannot be <1!
		if ( $maxhealth <1 ) $maxhealth = 1;
		if ( $output <1 ) $output = 1; //base output cannot be <1
		parent::__construct($armour, $maxhealth, 0, 0, 0);
		$this->output = $output; //after parent - weapon has no output and passes 0 to system creation
		$this->maxRepairPoints = $maxhealth*10;
		$this->maxBoostLevel = $maxBoost;		
	}
	
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);  
		$this->data["Special"] .= "<br> Output can be boosted up to " . $this->maxBoostLevel . " times at 1 EW per extra point of self repair.";	
		}	

	public function stripForJson(){
		$strippedSystem = parent::stripForJson();
		$strippedSystem->ewBoosted = $this->ewBoosted;													
		return $strippedSystem;
	} 

}	

//BioThruster - it's NOT seen as thruster by game; used to calculate output of BioDrive engine 
class BioThruster extends ShipSystem{
	public $iconPath = "thrusterOmni.png";
    public $name = "BioThruster";
    public $displayName = "BioThruster";
    public $isPrimaryTargetable = true; //can this system be targeted by called shot if it's on PRIMARY?
	//BioThrusters are fairly important!
	public $repairPriority = 5;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
    	    
    protected $possibleCriticals = array(15=>"OutputReduced1", 24=>array("OutputReduced1","OutputReduced1"));//different than original
    
    function __construct($armour, $maxhealth, $output ){
        parent::__construct($armour, $maxhealth, 0, $output );
		//always omnidirectional, but this need to be set AFTER default constructor
		$this->startArc = 0;
		$this->endArc = 360; 
    }
	
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);  
		$this->data["Special"] = "BioThruster - basically an omnidirectional thruster.";      
		$this->data["Special"] .= "<br>For technical reasons in FV BioThrusters output is summed up in BioDrive and then channeled by regular (invulnerable) thrusters.";  
	}	
} //endof class BioThruster



//BioDrive - basically an engine with rating calculated from ships' BioThrusters
//technical system, should never get hit.
//remember to plug BioThrusters to the BioDrive at design stage!
class BioDrive extends Engine{
	public $iconPath = "engineTechnical.png";
    public $name = "engine";
    public $displayName = "Engine";
    public $primary = true;
    public $isPrimaryTargetable = false;
    public $boostable = false;//cannot boost BioDrive!
    public $outputType = "thrust";
	
	private $bioThrusters = array();
	
    
    protected $possibleCriticals = array( ); //technical system, should never get damaged
    
    function __construct(){
        parent::__construct(0, 1, 0, 0, 0 ); //($armour, $maxhealth, $powerReq, $output, $boostEfficiency
    }
    
	function addThruster($thruster){
		if($thruster) $this->bioThrusters[] = $thruster;
	}
	
	
	public function setSystemDataWindow($turn){
		$this->output = $this->getOutput();	
		parent::setSystemDataWindow($turn); 	
		$this->output = $this->getOutput();	
		$this->data["Efficiency"] = $this->boostEfficiency;
		$this->data["Special"] = "BioDrive - basically an Engine with basic output calculated from BioThruster outputs.";      
		$this->data["Special"] .= "<br>Will never be damaged.";  
		$this->data["Special"] .= "<br>Cannot buy extra thrust."; //rules say BioThrusters CAN buy extra thrust, with rating provided on SCS... But rating on SCS is N/A...
	}
	
	
    public function getOutput(){
        $output = 0;
		//count thrust from BioThrusters
		foreach($this->bioThrusters as $thruster){
			$output += $thruster->getOutput();
		}
		if ($output === 0) return 0; //cannot buy extra thrust if there are no working thrusters!
	
		//reduce by pain
		$ship=$this->getUnit();
		if($ship){
			$pilot = $ship->getSystemByName("CnC");
			if($pilot){
				$painLevel = $pilot->hasCritical('ShadowPilotPain',TacGamedata::$currentTurn);
				$output -= $painLevel;
			}
		}
		
		//add boost, if any
        foreach ($this->power as $power){
            if ($power->turn == TacGamedata::$currentTurn && $power->type == 2){
                $output += $power->amount;
            }        
        }        
		
        return $output;        
    } //endof function getOutput
	
	
	public function stripForJson(){
		//$this->output = $this->getOutput();	
        $strippedSystem = parent::stripForJson();
        $strippedSystem->output = $this->getOutput();	
		$strippedSystem->data = $this->data;	
        return $strippedSystem;
    }
	
}//endof class BioDrive


/*Shadow Pilot - replaces C&C
 - irrepairable!
 - no regular criticals
 - feels pain due to damage suffered by ship (temporary) and own wounds (permanent)
*/
class ShadowPilot extends CnC{
    public $name = "cnC";
    public $displayName = "C&C";
    public $primary = true;
	public $iconPath = "ShadowPilot.png";
	
	//irrepairable!
	public $repairPriority = 0;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
    
    
    protected $possibleCriticals = array(
    );
        
    function __construct($armour, $maxhealth, $powerReq, $output ){
        parent::__construct($armour, $maxhealth, $powerReq, $output );
    }
	
	
	
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
/*deliberately skip inherited description		
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
*/		
		$this->data["Special"] = 'Ship pilot. Cannot be repaired. Damage to ship results in temporary pain, damage to pilot results in permanent pain.';
		$this->data["Special"] .= '<br>Pain reduces Initiative, Thrust and accuracy of fire.';
	}
	
	
	public function criticalPhaseEffects($ship, $gamedata)
    { 
    
    	parent::criticalPhaseEffects($ship, $gamedata);//Call parent to apply effects like Limpet Bore (altho this would never effect AA ships, but other effects added later might....
    
		if($this->isDestroyed()) return; //destroyed system does not work... but other critical phase effects may work even if destroyed!
		
		$damageSufferedThisTurn = 0;
		$damageToSelfThisTurn = 0;
		$onePainPer = 10; //1 point of pain per how many damage points?
		if ($ship->factionAge > 3) { //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
			$onePainPer = 20;//slow-grown Primordial ships are more resistant to pain		
		}
		
		//get all damage suffered THIS TURN - except tendrils. Ignore healing, damage dealing is painful even if it's mended afterwards!
		//ignore tendrils, that's not true damage!
		foreach ($ship->systems as $system) if (!($system instanceOf DiffuserTendril)){
			foreach ($system->damage as $dmg) if ( ($dmg->turn == $gamedata->turn) && ($dmg->damage > $dmg->armour)){
				$damageSufferedThisTurn += $dmg->damage - $dmg->armour;
				if($system->id == $this->id){
					$damageToSelfThisTurn += $dmg->damage - $dmg->armour;
				}
			}
		}
		
		//let's start pain in NEXT turn; criticals in FV usually start in CURRENT turn formally, but that causes readability to suffer during replays (originally there were no replays ;) )
		//pain can be caused by tendrils being broken - this is handled in EnergyDiffuser class, and rounded up (but doesn't sum with actual damage below)
		
		//temporary pain
		$painSuffered = round( $damageSufferedThisTurn/$onePainPer );
		for($i=1;$i<=$painSuffered;$i++){
			$crit = new ShadowPilotPain(-1, $ship->id, $this->id, 'ShadowPilotPain', $gamedata->turn+1, $gamedata->turn+1);
			$crit->updated = true;
			$this->criticals[] =  $crit;
		}
		
		//permanent pain
		for($i=1;$i<=$damageToSelfThisTurn;$i++){
			$crit = new ShadowPilotPain(-1, $ship->id, $this->id, 'ShadowPilotPain', $gamedata->turn+1);
			$crit->updated = true;
			$this->criticals[] =  $crit;
		}
		
    } //endof function criticalPhaseEffects	
		
} //endof class ShadowPilot


/*Phasing Drive - essentially a jump engine that destroys ship if damaged while half-phasing*/
class PhasingDrive extends JumpEngine{
    public $displayName = "Phasing Drive";

	//JumpEngine enables half phasing, so I'm torn about priority... I'll increase to 2 over Jump Engine's 1
	public $repairPriority = 2;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired

    /* A SHADOW SHIP DOES NOT OPEN A B5 VORTEX - IT SIMPLY FADES OUT, and the replay has to say so
     * (user ruling 2026-08-25). The only client-side effect of this flag is that
     * ReplayAnimationStrategy hands ShipJumpAnimation a fade with no ShipJumpPoint attached; the
     * ship still pans into view, still fades and still gets its log entry. It makes no SOUND
     * either: ShipJumpAudio is a vortex tearing open, so a ship that never opens one stays quiet
     * (user ruling 2026-08-25) - the animation builds no Audio object at all in that case.
     *
     * ⭐ PUBLIC, and declared HERE rather than on JumpEngine, which is the whole reason it is cheap.
     * json_encode takes public properties only and the static generator encodes the constructed
     * ship, so this key rides the blueprint of the 23 hulls that mount a Phasing Drive and does not
     * exist on the other 753 jump engines in the tree (plan section 8: a public default on the base
     * class would cost all 776 for nothing). ShipCompactor needs no entry for it either - it is
     * never false, so there is no default to strip - and the client's ShipSystem constructor copies
     * every key it is given, so it arrives without any per-class plumbing.
     *
     * ⚠️ NOT keyed off isLegacyJump() on the client side, deliberately. The Trek Nacelle, the BSG
     * FTL Drive and the Star Wars Hyperdrive are legacy too and keep the existing jump-point
     * animation; this is a SHADOW rule, not a legacy-jump rule, and the two are only aligned by
     * coincidence today. */
    public $noJumpPointAnimation = true;

    /* SHADOW ASSOCIATION HULLS JUMP THE OLD WAY (user ruling 2026-08-25) - boost the drive in
     * Initial Orders, vanish at the end of the turn, leave nothing behind. See
     * JumpEngine::markLegacy() for exactly what that flips and why it is a flag rather than a
     * subclass; the "or from a subclass constructor" case that comment describes is this one.
     *
     * Done here rather than in the 23 ship files because EVERY Phasing Drive in the tree is a
     * Shadow hull's and always will be - the drive is the faction's defining system, and the four
     * Shadow hulls filed under "Custom Ships" want the identical rule. A per-file call would be 23
     * chances to forget one on the next hull somebody adds.
     *
     * ⚠️ AFTER parent::__construct, never before: markLegacy() prunes the per-firing-mode arrays
     * that Weapon::__construct builds, so running it first would leave the seven vortex modes in
     * place. Same ordering the ship-file form relies on.
     *
     * ⚠️ FOUR ARGUMENTS, NOT FIVE. JumpEngine's 5th is the vortex PROJECTION range, and a drive
     * that cannot project one has no use for it - markLegacy() zeroes the range either way. Left
     * off rather than forwarded so the signature does not advertise a range this class can honour;
     * PHP ignores a surplus argument silently, so a ship file that passes one gets no error and no
     * effect, which is the same answer forwarding it would have given. */
    function __construct($armour, $maxhealth, $powerReq, $delay){
        parent::__construct($armour, $maxhealth, $powerReq, $delay);
        $this->markLegacy();
    }

    public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
		$this->data["Special"] .= 'If damaged while half-phasing, or on the turn it jumps to hyperspace - entire ship is destroyed.';
    }

	/* ⭐ A SHADOW HULL PHASES OUT - AND ONE HIT WHILE IT IS DOING SO TEARS IT APART (user ruling
	 * 2026-08-30). The drive that carries the ship into hyperspace is the same drive that
	 * half-phases it, and the half-phase rule has always been absolute: a single point of damage
	 * taken while the hull is between states destroys it outright (criticalPhaseEffects below).
	 * Jumping out at the end of the turn is that same manoeuvre taken all the way, so it carries
	 * the same absolute penalty in place of JumpEngine's missing-health percentage roll.
	 *
	 * ⚠️ THIS TURN'S DAMAGE ONLY, which is the whole point. A Phasing Drive damaged on an EARLIER
	 * turn and left unrepaired still jumps on the ordinary percentage roll - what kills the ship is
	 * being hit DURING the phase-out, exactly as with half-phasing. isDamagedOnTurn is deliberately
	 * the same measure the half-phase self-destruct uses two methods down, so the two halves of one
	 * rule cannot drift apart.
	 *
	 * ⚠️ TIMING IS WHY THIS WORKS AT ALL. The boost-to-jump sweep runs at the very END of
	 * Firing::fireWeapons (firing.php), so every damage entry from this turn's fire is already on
	 * the drive when the question is asked. It also runs BEFORE Criticals::setCriticals, whose
	 * $activeShips snapshot then excludes the ship this has just destroyed - which is what stops
	 * criticalPhaseEffects below from writing a SECOND destruction for a hull that both half-phased
	 * and jumped in the same turn. */
	protected function getCertainJumpFailureNote($ship, $gamedata){
		if (!$this->isDamagedOnTurn($gamedata->turn)) return null;

		return " is torn apart as it phases out - damage to the Phasing Drive destroys the ship.";
	}

	//destroy ship if damaged while half-phaseing, or while jumping out on a wrecked drive
	public function criticalPhaseEffects($ship, $gamedata)
    {

		parent::criticalPhaseEffects($ship, $gamedata);//Call parent to apply effects like Limpet Bore.

		//JUMP_POINTS_PLAN.md Stage 5: the parent call above is now also the vortex jump-failure
		//roll, which can destroy this very ship. Do not write a second destruction entry and a
		//second log line on top of it. Deliberately asks about THIS turn's roll only, rather than
		//about the ship being destroyed at all, so no pre-existing behaviour changes.
		if ($this->hasAppliedVortexFailure()) return;

		/* ⭐ THE OTHER HALF OF getCertainJumpFailureNote, AND THE ONLY WAY TO REACH THE CASE IT
		 * CANNOT (user ruling 2026-08-30). A Phasing Drive DESTROYED by this turn's fire never
		 * reaches doHyperspaceJump at all: the boost-to-jump sweep at the end of
		 * Firing::fireWeapons skips every destroyed engine, and doHyperspaceJump re-checks
		 * getRemainingHealth() <= 0 on top of that. So without this clause the gradient ran
		 * backwards - one point of damage on the drive destroyed the ship, while enough damage to
		 * wreck the drive outright left it sitting on the board, alive, its jump silently
		 * discarded.
		 *
		 * ⭐ WHY HERE RATHER THAN IN THE SWEEP. criticalPhaseEffects is called on EVERY system in
		 * Criticals::setCriticals Pass 2, destroyed or not - which is exactly why the half-phase
		 * rule below has always covered a wrecked drive and the jump rule did not. Widening the
		 * gate to "half-phased OR jumping out" makes one drive answer one way, instead of teaching
		 * a generic jump-engine sweep about a Shadow special case.
		 *
		 * ⚠️ IT CANNOT DOUBLE-FIRE with getCertainJumpFailureNote. A drive that was merely DAMAGED
		 * and boosted has already had the ship destroyed by doHyperspaceJump, which runs before
		 * setCriticals - so that ship is absent from the $activeShips snapshot and Pass 2 never
		 * reaches it. This clause only ever catches the jump that was skipped.
		 *
		 * isOverloading() reads power type 2, which despite the name is the BOOST record - that is
		 * what the client's "Jump to Hyperspace" control writes, and what the sweep tests. */
		$halfPhased = Movement::isHalfPhased($ship, $gamedata->turn);
		$jumpingOut = $this->isOverloading($gamedata->turn);

		if (!$halfPhased && !$jumpingOut) return;
		if (!$this->isDamagedOnTurn($gamedata->turn)) return;

		/* 'JumpFailure' for the jump case so it reads and queries identically to the damaged-drive
		   failure doHyperspaceJump writes - one damageclass covers both ways a Shadow jump can kill
		   its own ship. Both classes are already short-log types (weaponManager.doShortLogText).
		   ⚠️ The leading space is not decoration: combatLog.js prints the ship name and then the
		   pubnotes with nothing between them, which is why the half-phase line below has always
		   rendered as "SHIPNAMEPhasing drive damaged...". Fixed in passing. */
		$damageClass = $halfPhased ? 'HalfPhase' : 'JumpFailure';
		$pubNotes = $halfPhased
			? " - phasing drive damaged during half-phasing, ship destroyed."
			: " is torn apart as it phases out - the Phasing Drive was destroyed before the jump could complete.";

		//try to make actual attack to show in log - use Ramming Attack system!
		$rammingSystem = $ship->getSystemByName("RammingAttack");
		if($rammingSystem){ //actually exists! - it should on every ship!
			$newFireOrder = new FireOrder(
				-1, "normal", $ship->id, $ship->id,
				$rammingSystem->id, -1, $gamedata->turn, 1,
				100, 100, 1, 1, 0,
				0,0,$damageClass,10000
			);
			$newFireOrder->pubnotes = $pubNotes;
			$newFireOrder->addToDB = true;
			$rammingSystem->fireOrders[] = $newFireOrder;
		}else{
			$newFireOrder=null;
		}

		//destroy primary structure
		$primaryStruct = $ship->getStructureSystem(0);
		if($primaryStruct){			
            $remaining = $primaryStruct->getRemainingHealth();
            $damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $primaryStruct->id, $remaining, 0, 0, -1, true, false, "", $damageClass);
            $damageEntry->updated = true;
            $primaryStruct->damage[] = $damageEntry;			
			if($rammingSystem){ //add extra data to damage entry - so firing order can be identified!
					$damageEntry->shooterid = $ship->id; //additional field
					$damageEntry->weaponid = $rammingSystem->id; //additional field
			}
        }	
    } //endof function criticalPhaseEffects	
}//endof class PhasingDrive







/*Gaim damage absorbtion system
Cannot be hit directly in any way, except when absorbing damage. May protect any system on the same section (plus structure it's fitted on, even if it's primary structure - important for MCVs) 
as Bulkhead's activation is automatic, it will kick in when:
 - would prevent system destruction
 - system is Structure and would be destroyed without Bulkhead (even if it doesn't prevent destruction)
 - related Structure is under 34% (it's "use it or lose it" time)
*/
class Bulkhead extends ShipSystem{
    public $name = "Bulkhead";
    public $displayName = "Bulkhead";
    public $iconPath = "bulkhead.png";
	public $isTargetable = false; //cannot be targeted by called shots
	
	public $repairPriority = 1;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
    
    protected $possibleCriticals = array( ); //no critical effect applicable	
	
    function __construct($armour, $maxhealth){
        parent::__construct($armour, $maxhealth, 0, 0);
    }

	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);     
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
		$this->data["Special"] .= "Absorbs damage from hits on the same section - activation is automatic.";
		$this->data["Special"] .= "<br>Will kick in when it can prevent system destruction or when sections' structural integrity falls too low.";
	}	
	
     public function getOutput(){ //output = remaining health - just for visual purposes
        $output = $this->getRemainingHealth();     
        return $output;        
    }    
	
	
	//function estimating how good this Bulkhead is at stopping damage;
	public function doesProtectFromDamage($expectedDmg, $systemProtected = null, $damageWasDealt = false, $inflictingShots = 1, $isUnderShield = false) {
		//first do check whether this system can be protected! (same location or appropriate structure location)
		if ($systemProtected) {
			//is it on the same section?
			if ( ($this->location != $systemProtected->location) //different location...
			    && ($this->structureSystem !== $systemProtected ) //and this isn't appropriate structure either!
			 ) return 0;
		}else { //no particular system indicated = cannot protect
			return 0;	
		}
		//now check whether it _should_ protect...
		$targetHealth = 1;
		if($systemProtected){
			$targetHealth = $systemProtected->getRemainingHealth();
		}
		$ownHealth = $this->getRemainingHealth();
		$structureHealthFraction = $this->structureSystem->getRemainingHealth() / $this->structureSystem->maxhealth;
		$protectionValue = 0;
		if ( ($targetHealth <= $expectedDmg) && ($targetHealth + $ownHealth > $expectedDmg) ){
			$protectionValue = $targetHealth + $ownHealth - $expectedDmg; //I cannot prioritize smaller Bulkhead if it'd do the job, but at least I can avoid prioritizing larger one
		} else if ( (($targetHealth - $expectedDmg) <= 12) && ($this->structureSystem == $systemProtected)) { //Structure is hit - and is expected to fall to or below 12 points after hit, do protect
			$protectionValue = $ownHealth;
		} else if ($structureHealthFraction < 0.34) { //structure health is low, do protect for fear of not using the bulkhead at all 
			$protectionValue = $ownHealth;
		} else if ( ($systemProtected->repairPriority > 5) && ($targetHealth + $ownHealth > $expectedDmg)){ //for very important systems - protect even if result would be just damage reduction, as reduced crit on them is important
			$protectionValue = $ownHealth;
		}
		
		return $protectionValue;
	}
	//actual protection
	public function doProtect($gamedata, $fireOrder, $target, $shooter, $weapon, $systemProtected, $effectiveDamage,$effectiveArmor){ //hook for actual effect of protection - return modified values of damage and armor that should be used in further calculations
		$returnValues=array('dmg'=>$effectiveDamage, 'armor'=>$effectiveArmor);
		$damageToAbsorb=$effectiveDamage-$effectiveArmor;
		$damageAbsorbed=0;
		
		if($damageToAbsorb<=0) return $returnValues; //nothing to absorb
		
		$ownHealth = $this->getRemainingHealth();
		$damageAbsorbed = min($damageToAbsorb,$ownHealth); 
				
		
		$noOverkill = (!$weapon->doOverkill) && ($weapon->noOverkill || ($weapon->damageType == 'Piercing'));
		if($noOverkill){//shot is incapable of overkilling - reducing it would not matter if it doesn't prevent destruction of system hit
			$remainingHealth = $systemProtected->getRemainingHealth();
			if ($remainingHealth+$damageAbsorbed <= $damageToAbsorb) return $returnValues; //any absorbtion would be futile and just destroy the bulkhead uselessly
		}
		
		if($damageAbsorbed>0){ //can absorb something!
			$returnValues['dmg']=$effectiveDamage-$damageAbsorbed;
			$bulkheadDestroyed = false;
			if ($damageAbsorbed >=$ownHealth) $bulkheadDestroyed = true;
			//mark damage (possibly destruction) on bulkhead itself
			$damageEntry = new DamageEntry(-1, $target->id, -1, $gamedata->turn, $this->id, $damageAbsorbed, 0, 0, -1, $bulkheadDestroyed, false, "Absorb!", "Bulkhead");
			$damageEntry->updated = true;
			$this->damage[] = $damageEntry;
		}
		
		return $returnValues;
	}
	
	
	public function stripForJson(){
		//$this->output = $this->getOutput();	
        $strippedSystem = parent::stripForJson();
        $strippedSystem->output = $this->getOutput();
        return $strippedSystem;
    }
} //endof Bulkhead








/*Vorlon energy generating/storing system
it should replace Reactor, in FV I think it would be better when Reactor just coordinates with Capacitor!
actual power shenanigans are almost entirely in front end!
*/
class PowerCapacitor extends ShipSystem{ 
    public $name = "powerCapacitor";
    public $displayName = "Power Capacitor";
    public $primary = true; 
	public $isPrimaryTargetable = false;
    public $iconPath = "PowerCapacitor.png";
	
	public $repairPriority = 10;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
    
	//power held
	public $powerCurr = 0;
	private $powerMax = 0;
	public $capacityBonus = 0; //additional capacity - potentially set by enhancements
	public $powerReceivedFromFrontEnd = 0; //communication variable	
	public $powerReceivedFromBackEnd = 0; //communication variable
	public $nominalOutput = 0;//output to be used in front end display!
	
	//petals opening - done as boost of Capacitor!
    public $boostable = false; //changed to True if a given ship has Petals! 
    public $maxBoostLevel = 1;
    public $boostEfficiency = 0;
	protected $active = false; //To track in Front End whether system was ever activate this turn during Deployment, since boost can be toggled during Firing Phase.
	private $doubled = false; //Passed from Front End, to generate note to double Self Repair output at end of turn.		
	
/*
	1-17: No effect.
18-22: -1 to recharge rate.
23-27: -2 to recharge rate and the
capacitor loses one half (drop fractions) of
the energy it is currently holding.
28+: -4 to recharge rate and the
capacitor is completely emptied.
*/    
    protected $possibleCriticals = array(
		18=>"OutputReduced1",
		23=>array("OutputReduced2","ChargeHalve"), //multiple instances of OutputReduced - should scale fine with self-repair, rather than higher repair cost
		28=>array("OutputReduced2", "OutputReduced2","ChargeEmpty")//multiple instances of OutputReduced - should scale fine with self-repair, rather than higher repair cost
	); 
    

    function __construct( $armour, $maxhealth, $powerReq, $output, $hasPetals = true  ){ //technical object, does not need typical system attributes (armor, structure...)
        parent::__construct( $armour, $maxhealth, $powerReq, $output ); //$armour, $maxhealth, $powerReq, $output	
		$this->boostable = $hasPetals;
    }
	
	public function getMaxCapacity(){ //maximum capacity = health remaining + bonus (bonus only if there is no damage!)
		$capacity = $this->getRemainingHealth();
		$capacity += $this->capacityBonus ;
		return $capacity;
	}
	
	
	public function setPowerHeld($newValue){ //cut off by maximum capacity
		//$this->powerCurr = min($newValue, $this->getMaxCapacity() ); //cutting off at this point interacts badly with enhancements... moving to FRONT END!
		$this->powerCurr = $newValue;
	}
	
	public function isDoubled(){
		return $this->doubled;
	}


	/* this method generates additional non-standard informaction in the form of individual system notes
	in this case: 
	 - Deployment phase: fill to full
	 - Initial phase: may be changed in front end (boosting Capacitor and/or systems)
	 - Firing phase: may be changed in FRONT END (firing costs power!) - actually belay that, only BACK END will know whether firing actually happened!
	 - Firing phase: may be changed in BACK END as well (intercepting costs power! - intercept-capable weapons will have appropriate checks in place to see they don't overextax the capacitor)
	 Save always current stored power, not the changes that led to this value.
	 
	 CHANGES COMPARED TO OFFICIAL VERSION:
	  - recharge occurs in Initial phase (official - just before movement, which makes power not usable in Initial phase)
	  - opening petals reduces armor of all systems by 2 (official - armor is reduced on PRIMARY only, but all profiles are increased by 1)
	  - cannot icrease recharge rate in any other way (official - can shut down everything (weapons, shields) to increase by 100%)
	*/
    public function generateIndividualNotes($gameData, $dbManager){ //dbManager is necessary for Initial phase only
		$ship = $this->getUnit();
		switch($gameData->phase){
				case -1: //deployment phase 
					if($ship->userid == $gameData->forPlayer){ //only own ships, otherwise two copies of initial data are written
						$this->setPowerHeld($this->getMaxCapacity());
						//AND PREPARE APPROPRIATE NOTES!		
						$notekey = 'powerStored';
						$noteHuman = 'Power Capacitor - stored power';
						$noteValue = $this->powerCurr;
						$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$noteValue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue
					}
					break;
								
				case 1: //Initial phase
					if($ship->userid == $gameData->forPlayer){ //only own ships, otherwise bad things may happen!
						//load existing data first - at this point ship is rudimentary, without data from database!
						/*in this case - result from front end completely replaces current value, so it's NOT necessary to read old notes!
						$listNotes = $dbManager->getIndividualNotesForShip($gameData, $gameData->turn, $ship->id);	
						foreach ($listNotes as $currNote){
							if($currNote->systemid==$this->id){//note is intended for this system!
								$this->addIndividualNote($currNote);
							}
						}
						$this->onIndividualNotesLoaded($gameData);
						*/
						$this->setPowerHeld($this->powerReceivedFromFrontEnd); 
						//AND PREPARE APPROPRIATE NOTES!		
						$notekey = 'powerStored';
						$noteHuman = 'Power Capacitor - stored power';
						$noteValue = $this->powerCurr;
						$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$noteValue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue
				
						if($this->doubled == true){ //To generate note to double Self Repair output at end of turn.
							$notekey = 'doubled';
							$noteHuman = 'Power Capacitor - Doubled';
							$noteValue = 1;
							$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$noteValue);
						}
					}
					break;
				
				case 4: //firing phase
					//take what front end reports, and add what back end calculated (basically weapons fire cost)
					//$this->setPowerHeld($this->powerReceivedFromFrontEnd + $this->powerReceivedFromBackEnd); 
					//or perhaps disregard what front end says - in this phase it's cost of firing... and this is better calculated by back end (firing _declaration_ doesn't equal actual firing, especially for Ligntning Cannons!
					$this->setPowerHeld($this->powerCurr - $this->powerReceivedFromBackEnd); 
					//apply critical eefects: halve charge/empty charge
					if($this->hasCritical("ChargeEmpty")){
						$this->setPowerHeld(0); 
					}else if ($this->hasCritical("ChargeHalve")){
						$this->setPowerHeld(floor($this->powerCurr/2)); 
					}
					//AND PREPARE APPROPRIATE NOTES!		
					$notekey = 'powerStored';
					$noteHuman = 'Power Capacitor - stored power';
					$noteValue = $this->powerCurr ;
					$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$noteValue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue
					break;
		}
	} //endof function generateIndividualNotes
	
	public function canDrawPower($powerNeeded){
		if(($this->powerCurr - $this->powerReceivedFromBackEnd) >= $powerNeeded){
			return true; //drawing such power is possible
		}else{
			return false; //cannot draw so much power!
		}
	}
	
	//it should not happen, but technically it's possible to actually draw more power than Capacitor holds...
	public function doDrawPower($powerDrawn){
		$this->powerReceivedFromBackEnd += $powerDrawn;
	}
	
	/*act on notes just loaded - to be redefined by systems as necessary
	 - set power held
	*/
	public function onIndividualNotesLoaded($gamedata){
		foreach ($this->individualNotes as $currNote){ //assume ASCENDING sorting - so enact all changes as is
			if($currNote->turn == $gamedata->turn && $currNote->notekey == 'doubled') $this->doubled = true;	//Value check to see if Self Repair doubled.		
			switch($currNote->notekey){
				case 'powerStored': //power that should be stored at this moment
					$this->setPowerHeld($currNote->notevalue);
					break;								
			}
		}

		//We can apply petal effects here so they are visible for player (note, criticals don't seem to get saved to database here, prolly because $dbManager->submitCriticals isn't called)			
		if($gamedata->phase == 2 || $gamedata->phase == 5 || $gamedata->phase == 3){
	
			$boostlevel = $this->getBoostLevel($gamedata->turn);
			if ($boostlevel <1) return; //not boosted - no crit!
			$ship = $this->unit;
			foreach($ship->systems as $system){
				if($system->location == 0 && $system->isTargetable){	//Only targetable primary systems get reduced armor
					$crit = new ArmorReduced(-1, $ship->id, $system->id, "ArmorReduced", $gamedata->turn, $gamedata->turn);
					$crit->updated = true;
					//$crit->inEffect = true;
					$system->criticals[] =  $crit;
					$crit = new ArmorReduced(-1, $ship->id, $system->id, "ArmorReduced", $gamedata->turn, $gamedata->turn);
					$crit->updated = true;
					//$crit->inEffect = true;
					$system->criticals[] =  $crit;
				}
			}
			$cnc = $ship->getSystemByName("CnC"); //Now find CnC and increase profile by 5% using a spearate crit
			if($cnc){				 		
				$crit = new ProfileIncreased(-1, $ship->id, $cnc->id, "ProfileIncreased", $gamedata->turn, $gamedata->turn);
				$crit->updated = true;
				//$crit->inEffect = true;
				$cnc->criticals[] =  $crit;
			}									
		}	

	} //endof function onIndividualNotesLoaded
	
	
    public function setSystemDataWindow($turn){
        parent::setSystemDataWindow($turn); 
		$this->powerMax = $this->getMaxCapacity(); //do cut off overflow here as well!
		$this->powerCurr =min($this->powerCurr, $this->powerMax);
		$this->data["Power Stored / Max"] =  $this->powerCurr . '/' . $this->powerMax;
		$this->data["Power regeneration"] =  'Initial phase only';
        $this->data["Special"] = "This system is responsible for generating and storing power (Reactor is nearby for technical purposes).";
        $this->data["Special"] = "You can double power recharge (and Self-Repair), at the cost of deactivating all weapons and shields this turn.";	  			   
		if ($this->boostable){
			$this->data["Special"] .= "<br>In addition, you may open ship petals increasing generation by 50% on the following turn - however all primary systems lose 2 Armour and Defence Profiles increase 5% for the current turn.";
		}
		$this->data["Special"] .= "<br>You cannot generate more power than the Capacitor Max value, any excess is lost.";		
		$this->data["Special"] .= "<br>Destroying Capacitor disables (but does not destroy) the ship.";
    }
	
	public function beforeFiringOrderResolution($gamedata){ //actually mark armor reduced temporary critical if Petals are open
		/* //Moved to onIndividualNotesLoaded() to apply full TT effects.
		$boostlevel = $this->getBoostLevel($gamedata->turn);
		if ($boostlevel <1) return; //not boosted - no crit!
		$ship = $this->unit;
		foreach($ship->systems as $system){		
			$crit = new ArmorReduced(-1, $ship->id, $system->id, "ArmorReduced", $gamedata->turn, $gamedata->turn);
			$crit->updated = true;
			$crit->inEffect = true;
			$system->criticals[] =  $crit;
			$crit = new ArmorReduced(-1, $ship->id, $system->id, "ArmorReduced", $gamedata->turn, $gamedata->turn);
			$crit->updated = true;
			$crit->inEffect = true;
			$system->criticals[] =  $crit;
		}
		*/
			//Actually make sure petal effects are applied.	
			$boostlevel = $this->getBoostLevel($gamedata->turn);
			if ($boostlevel <1) return; //not boosted - no crit!
			$ship = $this->unit;
			foreach($ship->systems as $system){
				if($system->location == 0 && $system->isTargetable){	//Only targetable primary systems get reduced armor
					$crit = new ArmorReduced(-1, $ship->id, $system->id, "ArmorReduced", $gamedata->turn, $gamedata->turn);
					$crit->updated = true;
					$crit->inEffect = true;
					$system->criticals[] =  $crit;
					$crit = new ArmorReduced(-1, $ship->id, $system->id, "ArmorReduced", $gamedata->turn, $gamedata->turn);
					$crit->updated = true;
					$crit->inEffect = true;
					$system->criticals[] =  $crit;
				}
			}
			$cnc = $ship->getSystemByName("CnC"); //Now find CnC and increase profile by 5% using a spearate crit
			if($cnc){				 		
				$crit = new ProfileIncreased(-1, $ship->id, $cnc->id, "ProfileIncreased", $gamedata->turn, $gamedata->turn);
				$crit->updated = true;
				$crit->inEffect = true;
				$cnc->criticals[] =  $crit;
			}			
		


	}	
	
        private function getBoostLevel($turn){
            $boostLevel = 0;
            foreach ($this->power as $i){
                    if ($i->turn != $turn)
                            continue;
                    if ($i->type == 2){
                            $boostLevel += $i->amount;
                    }
            }
            return $boostLevel;
        }		
	
    public function stripForJson(){
        $strippedSystem = parent::stripForJson();
        $strippedSystem->data = $this->data;
        $strippedSystem->powerCurr = max($this->powerCurr,0); //power less than 0 would block the game in case of this system
	    $strippedSystem->powerMax = $this->getMaxCapacity();
		$strippedSystem->nominalOutput = $this->output;
		//$strippedSystem->powerReceivedFromFrontEnd = $this->powerReceivedFromFrontEnd;
        if ($this->individualNotesTransfer !== '' && $this->individualNotesTransfer !== null) {
            $strippedSystem->individualNotesTransfer = $this->individualNotesTransfer;
        }		
		//$strippedSystem->individualNotesTransfer = $this->individualNotesTransfer;
		$strippedSystem->active = $this->active;
		$strippedSystem->doubled = $this->doubled;					
        return $strippedSystem;
    }

	/*
	public function doIndividualNotesTransfer(){
		//data received in variable individualNotesTransfer, further functions will look for it in powerReceivedFromFrontEnd
		//in this case it should be just one entry, power remaining
		if(is_array($this->individualNotesTransfer)) foreach($this->individualNotesTransfer as $powerLeft => $doubled){
			$this->powerReceivedFromFrontEnd = $powerLeft;
		}  
		$this->individualNotesTransfer = array(); //empty, just in case
	}		
	*/
	
	public function doIndividualNotesTransfer() {
		//data received in variable individualNotesTransfer, further functions will look for it in powerReceivedFromFrontEnd
        $notes = (array)$this->individualNotesTransfer;
        if (isset($notes['powerRemaining'])) $this->powerReceivedFromFrontEnd = $notes['powerRemaining'];
        if (isset($notes['doubled'])) $this->doubled = $notes['doubled'];
        
		$this->individualNotesTransfer = array(); //empty, just in case
	}

	//upon destruction (ship should be completely disabled) go for:
	// - add Power reduction critical to Reactor (so ship goes out of control) 
	// - add SelfRepair output reduction critical (so the damage isn't just repaired in a few turns ;) ).
	public function criticalPhaseEffects($ship, $gamedata)
    { 
    
		parent::criticalPhaseEffects($ship, $gamedata);//Call parent to apply effects like Limpet Bore.	    
    
		if (!$this->isDamagedOnTurn($gamedata->turn)) return; 
		if (!$this->isDestroyed()) return;		
		
		$reactor = $ship->getSystemByName("Reactor"); //by class name
		if($reactor){
			$reactor->addCritical($ship->id, "OutputReduced4", $gamedata);
			$reactor->addCritical($ship->id, "OutputReduced4", $gamedata);
			$reactor->addCritical($ship->id, "OutputReduced4", $gamedata);
		}
		
		$selfRepairList = $ship->getSystemsByName("Self Repair", true);//by readable name
		foreach($selfRepairList as $selfRepair){
			$selfRepair->addCritical($ship->id, "OutputReduced4", $gamedata);
			$selfRepair->addCritical($ship->id, "OutputReduced4", $gamedata);
			$selfRepair->addCritical($ship->id, "OutputReduced4", $gamedata);
			$selfRepair->addCritical($ship->id, "OutputReduced4", $gamedata);
		}
    } //endof function criticalPhaseEffects	
							
} //endof PowerCapacitor


class FtrPetals extends ShipSystem implements SpecialAbility{    
		public $name = "FtrPetals";
		public $displayName = "Vorlon Petals";
		public $iconPath = "PowerCapacitor.png";
		public $specialAbilities = array("Petals");
		public $specialAbilityValue = 1;		
		public $primary = true;
		public $detected = true;
		//defensive system
		public $rangePenalty = 0;
		protected $active = false; //To track in Front End whether system was ever activate this turn during Deployment, since boost can be toggled during Firing Phase.
		public static $petalsDone = array();	
		protected $initializeOnLoad	= true; //Runs initialisationUpdate() immediately on page loading, useful for updating tooltips immediately.  Needs passed in strpForJson().
		
		function __construct($armour, $maxhealth, $powerReq, $output){
			parent::__construct($armour, $maxhealth, $powerReq, $output);
			
		}

		protected $possibleCriticals = array(
			26=>array("OutputReduced1")
		);

		public function isActive(){
			return $this->active;
		}

		public function setSystemDataWindow($turn){
			$this->data["Special"] = "Can be toggled open each turn during Initial Orders.";
			$this->data["Special"] .= "<br>Whislt open Fighters gain +2 Thrust, however Defence Profiles are increase by 5% and their Side Armour is reduced by 2.";													
		}	

		public function getSpecialAbilityValue($args){
			return $this->specialAbilityValue;
		}

	public function doIndividualNotesTransfer(){
		//data received in variable individualNotesTransfer, further functions will look for it in currchangedAA
		if(is_array($this->individualNotesTransfer)){			
			foreach($this->individualNotesTransfer as $petalChange){			
				if($petalChange == 1){
					$this->active = true;
				}else{
					$this->active = false; //May start Deployment phase as true via notes
				}									
			}
		} 
		$this->individualNotesTransfer = array(); //empty, just in case
	}			

    public function generateIndividualNotes($gameData, $dbManager){ //dbManager is necessary for Initial phase only
		$this->doIndividualNotesTransfer();
		$ship = $this->getUnit();	
		
		switch($gameData->phase){
			
			case 1:
				if ($this->active) {
						$notekey = 'Open';
						$noteHuman = 'Petals opened';
						$noteValue = 1;
						$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$noteValue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue
				}
			break;
			
		}	
	}			

	public function onIndividualNotesLoaded($gamedata){
		//Sort notes by turn, and then phase so latest detection note is always last.
		foreach ($this->individualNotes as $currNote){ //Search all notes, they should be process in order so the latest event applies.
			if($currNote->turn == $gamedata->turn){
				$this->active = true;
			}
		}

		//and immediately delete notes themselves, they're no longer needed (this will not touch the database, just memory!)
		$this->individualNotes = array();		
	} //endof function onIndividualNotesLoaded

	public function stripForJson(){
		$strippedSystem = parent::stripForJson();
		$strippedSystem->active = $this->active;
		$strippedSystem->initializeOnLoad = $this->initializeOnLoad;							        
		return $strippedSystem;
	}

	} //endof FtrPetals


class StructureTechnical extends ShipSystem{
    public $name = "StructureTechnical";
    public $displayName = "Structure Technical";
    public $iconPath = "StructureTechnical.png";    
    
	//Cannot be repaired
	public $repairPriority = 0;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
 
     public function getArmourInvulnerable($target, $shooter, $dmgClass, $pos=null){ //this thruster should be invulnerable to anything...
		$activeAA = 99;
		return $activeAA;
    }
    
    public function setSystemDataWindow($turn){
	parent::setSystemDataWindow($turn);     
	$this->data["Special"] = "This system is here for technical purposes only. Cannot be damaged in any way.";
	}  
	
	public $isPrimaryTargetable = false; //can this system be targeted by called shot if it's on PRIMARY?	
	public $isTargetable = false; //cannot be targeted ever!
	
   function __construct($armour, $maxhealth, $powerReq, $output){
	    parent::__construct(0, 1, 0, 0); //$armour, $maxhealth, $powerReq, $output
		}
      
}//endof VreeStructurePlaceholder	


class BSGHybrid extends ShipSystem {
    public $name = "BSGHybrid";
    public $displayName = "Cylon Hybrid";
	public $iconPath = "ShadowPilot.png";

    protected $possibleCriticals = array(
		1=>"SensorLoss"
    );

    function __construct($armour, $maxhealth, $powerReq, $output ){
        parent::__construct($armour, $maxhealth, $powerReq, $output );
    }
}


class PlasmaBattery extends ShipSystem{ 
 	public $name = "PlasmaBattery";
    public $displayName = "Plasma Battery";
//    public $primary = true; 
	public $isPrimaryTargetable = true;
    public $iconPath = "plasmabattery.png";

	public $powerCurr = 0;
//	public $capacityBonus = 0; //additional capacity - potentially set by enhancements
	public $powerReceivedFromFrontEnd = 0; //communication variable	
	public $powerReceivedFromBackEnd = 0; //communication variable
//	public $nominalOutput = 0;//output to be used in front end display!
	
//    public $boostable = true;
//    public $maxBoostLevel = 4;
//    public $boostEfficiency = 1; 
	public $powerStoredFront = 0;    
	
	public $powerDrawnAtFiring = 0;
 
    
/*
	1-12: No effect.
	13+: The battery is completely emptied.
*/        
    protected $possibleCriticals = array(
		13=>"ChargeEmpty",
	); 

    function __construct($armour, $maxhealth, $powerReq, $output ){  	
        parent::__construct($armour, $maxhealth, $powerReq, $output );
	}
 
 public function getOutput(){
        $output = min($this->powerCurr, $this->getRemainingHealth()); //output cannot be higher than remaining health
        return $output;
    }

 	public function getMaxCapacity(){ //maximum capacity = health remaining
		$capacity = $this->getRemainingHealth();
//		$capacity += $this->capacityBonus ;
		return $capacity;
	}

 public function stripForJson(){
        $strippedSystem = parent::stripForJson();
		$strippedSystem->data = $this->data; 
		$strippedSystem->output = $this->getOutput();
        return $strippedSystem;
    } 
	
 public function generateIndividualNotes($gameData, $dbManager){ //dbManager is necessary for Initial phase only
        $ship = $this->getUnit();
        switch($gameData->phase){
                case -1: //deployment phase 
                    if($ship->userid == $gameData->forPlayer){ //only own ships, otherwise two copies of initial data are written
                        $this->powerCurr = $this->maxhealth;
                        //AND PREPARE APPROPRIATE NOTES!
                        $notekey = 'powerStored';
                        $noteHuman = 'Plasma Battery - stored power';
                        $noteValue = $this->powerCurr;
                        $this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$noteValue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue
                    }
                    break;

                case 1: //Initial phase
                    if($ship->userid == $gameData->forPlayer){ //only own ships, otherwise bad things may happen!
//...no need to load earlier notes, as new value overrides the old one!
                        $this->powerCurr = $this->powerReceivedFromFrontEnd; 
                        //AND PREPARE APPROPRIATE NOTES!
                        $notekey = 'powerStored';
                        $noteHuman = 'Plasma Battery - stored power';
                        $noteValue = $this->powerCurr;
                        $this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$noteValue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue
                    }
                    break;

				case 4: //firing phase
					//reduce charge by power used by weapons in firing phase (Plasma Webs, basically)
					if($this->powerDrawnAtFiring > 0){
						$this->powerCurr -= $this->powerDrawnAtFiring;
						//AND PREPARE APPROPRIATE NOTES!		
						$notekey = 'powerStored';
						$noteHuman = 'Plasma Battery - stored power';
						$noteValue = $this->powerCurr;
						$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$noteValue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue
					}
					break;
					
        }
    } //endof function generateIndividualNotes	
 	
 	/*act on notes just loaded - to be redefined by systems as necessary
	 - set power held
	*/
	public function onIndividualNotesLoaded($gamedata){
		foreach ($this->individualNotes as $currNote){ //assume ASCENDING sorting - so enact all changes as is
			switch($currNote->notekey){
				case 'powerStored': //power that should be stored at this moment
				$this->powerCurr = $currNote->notevalue;
					break;			
			}
		}
	} //endof function onIndividualNotesLoaded
 
    public function doIndividualNotesTransfer(){
        //data received in variable individualNotesTransfer, further functions will look for it in powerReceivedFromFrontEnd
        //in this case it should be just one entry, power remaining
        if(is_array($this->individualNotesTransfer)) foreach($this->individualNotesTransfer as $powerLeft)  $this->powerReceivedFromFrontEnd = $powerLeft;
        $this->individualNotesTransfer = array(); //empty, just in case
    }
 
   
      public function setSystemDataWindow($turn){
		$this->output =  $this->getOutput();
        $this->powerCurr =$this->output;
        parent::setSystemDataWindow($turn); 
        $this->data["Power stored/max"] =  $this->powerCurr . '/' . $this->getMaxCapacity();
		$this->data["Special"] = "This system is only responsible for STORING extra power.  It does not GENERATE new power each turn.";
        $this->data["Special"] .= "<br>Power stored is shown for in Reactor output during Initial Orders. Surplus AFTER Initial phase is moved back into Batteries and may therefore show as a negative value in Reactor during Move/Fire phases.";
        $this->data["Special"] .= "<br>Stored power is necessary to use offensive mode of Plasma Web in Firing Phase.";
    }
	
	//draw power, return information if the action was successful
	public function doDrawPower(){
		if($this->isDestroyed()) return false;
		if($this->powerCurr > $this->powerDrawnAtFiring) { //battery still stores power reserves
			$this->powerDrawnAtFiring++;
			return true;
		} else { //cannot draw power from this battery
			return false;
		}
	}
	
	public static function shipDrawPower($ship){
		foreach ($ship->systems as $battery) if ($battery instanceOf PlasmaBattery) {
			if($battery->doDrawPower()){
				return; //power successfully drawn - do not look further
			}
		}
	}
							
} //endof PlasmaBattery.php


class ThirdspaceShieldGenerator extends ShipSystem{
    public $name = "ThirdspaceShieldGenerator";
    public $displayName = "Shield Generator";
    public $primary = true; //Check if inherited and remove?
	public $isPrimaryTargetable = false; //Check if inherited and remove?
	public $isTargetable = false; //Check if inherited and remove?
    public $iconPath = "ThirdspaceShieldGen.png";
	protected $doCountForCombatValue = false; //Check if inherited and remove?
	
    public $boostable = true;	
    public $boostEfficiency = 0; //Advanced Sensors are rarely lower than 13, so flat 14 boost cost is advantageous to output+1!
    public $maxBoostLevel = 0; //Unlike Shadows/Vorlons Thirdspace ships have alot of spare power, so limit their max sensor boost for balance. 	

	public $totalBaseRating = 0;// Maximum shield amount for ALL shields.	
	public $storedCapacity = 0;
	public $shieldPresets = array('Equalise', 'Forward', 'Starboard', 'Aft', 'Port');	
	public $presetCurrClass = '';//for front end, to display Preset types in tooltips.
	
	private $shieldCount = 0;

	public $repairPriority = 9;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
    
    
    function __construct($armour, $maxhealth, $powerReq, $output, $maxBoost = 0, $boostEfficiency = 0){ 
    	$this->maxBoostLevel = $maxBoost;
    	$this->boostEfficiency = $boostEfficiency;    	
        parent::__construct($armour, $maxhealth, $powerReq, $output ); //$armour, $maxhealth, $powerReq, $output    		    
    }  	    

    public function onConstructed($ship, $turn, $phase){
        parent::onConstructed($ship, $turn, $phase);	
		
		$totalShieldsRating = 0;
						
		foreach($ship->systems as $system){
			if($system instanceof ThirdspaceShield){
				//getEffectiveBaseRating: the generator is constructed BEFORE the shields, so their own
				//onConstructed hasn't refreshed ->baseRating yet; read the crit-derived value directly so
				//the "Maximum Shield Power" display reflects any phasing reduction.
				$totalShieldsRating += $system->getEffectiveBaseRating($turn);
				$this->shieldCount++;
			}
		}
		$this->totalBaseRating = $totalShieldsRating;
    }
		
	protected $possibleCriticals = array(
	            18=>"OutputReduced1",
	            20=>"OutputReduced2",
	            26=>"OutputReduced4" );

	private function getRegenforNotes($turn){
		$regen = 0;
		$regen = $this->getOutput() + ($this->getBoostLevel($turn) * $this->shieldCount);
		return $regen; 
	}

		
    public function setSystemDataWindow($turn){
        parent::setSystemDataWindow($turn);
		$this->data["Special"] = "Regenerates " . $this->getRegenforNotes($turn) . " health split equally amongst all Thirdspace Shields at the end of each turn.";
		$this->data["Special"] .= "<br>Shields will not regenerate above their Base Rating, instead any excess will be allocate to another shield where possible.";
		$this->data["Special"] .= "<br>Current Shield Power CANNOT be regenerated above Maximum Shield Power.";		       
        $this->data["Special"] .= "<br>Regeneration can be boosted " . $this->maxBoostLevel  . " times at " . $this->boostEfficiency ." power for " . $this->boostEfficiency ." extra output.";  
        $this->data["Special"] .= "<br>During Initial Orders this system can also be used to transfer shield power from one shield arc to another e.g. front to aft etc.";	   
        $this->data["Special"] .= "<br>You cannot commit your Intial Orders if there is an excess or deficit of shield energy in this system.";       
 		$this->outputDisplay = $this->storedCapacity;
 		$this->data["Current Output "] = $this->getOutput();
 		$this->data["Boosted by "] = $this->getBoostLevel($turn) * $this->shieldCount; 		
 		$this->data["Maximum Shield Power "] = $this->totalBaseRating;
 		$this->data["Current Shield Power "] = $this->totalBaseRating; //Will be updated in Front End anyway. 		  		               
    }

	private function getBoostLevel($turn){
            $boostLevel = 0;
            foreach ($this->power as $i){
                    if ($i->turn != $turn)
                            continue;
                    if ($i->type == 2){
                            $boostLevel += $i->amount;
                    }
            }
            return $boostLevel;
        }

	//effects that happen in Critical phase (after criticals are rolled) - replenishment from active Generator 
	public function criticalPhaseEffects($ship, $gamedata){
			
		parent::criticalPhaseEffects($ship, $gamedata);//Call parent to apply effects like Limpet Bore.
				
		if ($this->isDestroyed()) return; // Exit if generator is destroyed						
				
		$allShields = array(); //Create array of all shields on ship.
		$totalShieldRating = 0;//initialise
		$currentShieldHealth = 0;//initialise	
					
		foreach($ship->systems as $system){//Loop through systems to find Shields
			if($system instanceof ThirdspaceShield){
				$allShields[] = $system; //Add to list of shields.
				//getEffectiveBaseRating (not ->baseRating) so a Phased Gravitic Torpedo that hit THIS turn
				//already lowers the regen ceiling now - the crit is on the shield by the fire phase, but
				//->baseRating is only refreshed at load (before firing). Same reason as ThoughtShield.
				$totalShieldRating += $system->getEffectiveBaseRating($gamedata->turn);
				$currentShieldHealth += $system->getRemainingCapacity();
			}
		}

		if($currentShieldHealth >= $totalShieldRating) return; //If for some reason total shield health is equal/greater than baseRatings combined, don't regen at all!

		$noOfShields = count($allShields);
		$generatorOutput = $this->getOutput(); // e.g  60
		$boostLevel = $this->getBoostLevel($gamedata->turn); // e.g. 2				
		$amountPerShield = ($generatorOutput / $noOfShields) + $boostLevel; //e.g 15 + boost

		$canRechargeTotal = $totalShieldRating - $currentShieldHealth;
		$spareEnergy = 0; //Counter for shield energy not used in next part.	
					
		foreach ($allShields as $shield) {
			$maxRegenThisTurn = $shield->getEffectiveBaseRating($gamedata->turn) - $shield->getRemainingCapacity(); //Amount between health and (phasing-reduced) baseRating.
			$maxRegenThisTurn = max(0, $maxRegenThisTurn);
			
			if($maxRegenThisTurn >= $canRechargeTotal) $maxRegenThisTurn = $canRechargeTotal;//Final loop might need adjusted to no overcharge!

 			//Check if Generator can fully charge shields and shield is below baseRating, if not add excess to $spareEnergy csounter.
			if($maxRegenThisTurn >= $amountPerShield){ //Can be regenerated by full Generator amount
				$shield->absorbDamage($ship, $gamedata, -$amountPerShield); // Apply full regeneration.
				$canRechargeTotal -= $amountPerShield;						
			}else{ //Can only be regenerated by partial Generator amount, or not at all e.g. equal and greater than baseRating.
				$regenAmount = min($amountPerShield, $maxRegenThisTurn);								
				$shield->absorbDamage($ship, $gamedata, -$regenAmount); // Apply regeneration (negative to heal).										
				$canRechargeTotal -= $regenAmount;	//Deduct what we did regen from total possible to regen.
				$spareEnergy += $amountPerShield - $regenAmount;	//Add any unused energy to pool.				
			}																
		}						
					
		// When there is spare energy, loop through shields, checking if it can be allocated to any other shields.
		while ($spareEnergy > 0 && $canRechargeTotal > 0) { //
			$energyAllocated = false; // Track if any energy is allocated in this pass.
					
			foreach ($allShields as $shield) {
			    $remainingCapacity = $shield->getEffectiveBaseRating($gamedata->turn) - $shield->getRemainingCapacity(); // Calculate remaining capacity (phasing-reduced rating).

				if($spareEnergy >= $canRechargeTotal) $spareEnergy = $canRechargeTotal;//Final loop might need adjusted to no overcharge!

			    if ($remainingCapacity > 0) { // Check if there is space for regeneration.
			        $regenAmount = min($remainingCapacity, $spareEnergy); // Determine the amount of energy to regenerate.
			            
			        $shield->absorbDamage($ship, $gamedata, -$regenAmount); // Apply regeneration (negative to heal).
					$canRechargeTotal -= $regenAmount;	//Deduct what we did regen from total possible to regen.			        
			        $spareEnergy -= $regenAmount; // Deduct used energy.
			            
			        $energyAllocated = true; // Energy was allocated in this iteration.
			    }			    			    
			}

			// Break loop if no energy was allocated to avoid an infinite loop.
			if (!$energyAllocated) {
			    break;
			}
		}							
	} //endof function criticalPhaseEffects

	
	//always redefine $this->data, variable information goes there...
	public function stripForJson(){
        $strippedSystem = parent::stripForJson();
        $strippedSystem->data = $this->data;
        //$strippedSystem->shieldPresets = $this->shieldPresets;
        if ($this->storedCapacity !== 0) $strippedSystem->storedCapacity = $this->storedCapacity;       
        if ($this->presetCurrClass !== '') $strippedSystem->presetCurrClass = $this->presetCurrClass;  
		
        return $strippedSystem;
    }
							
} //endof ThirdspaceShieldGenerator


class ThoughtShieldGenerator extends ShipSystem{
    public $name = "ThoughtShieldGenerator";
    public $displayName = "Thought Shield";
    public $primary = true; //Check if inherited and remove?
	public $isPrimaryTargetable = false; //Check if inherited and remove?
	public $isTargetable = false; //Check if inherited and remove?
    public $iconPath = "ThirdspaceShieldGen.png";
	protected $doCountForCombatValue = false; //Check if inherited and remove?

    public $canOffLine = true;	
	public $storedCapacity = 0; 
	public $shieldPresets = array('Equalise');	
	public $presetCurrClass = '';//for front end, to display Preset types in tooltips.

	public $repairPriority = 4;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
    
    
    function __construct($armour, $maxhealth, $powerReq, $output){ 
        parent::__construct($armour, $maxhealth, $powerReq, $output ); //$armour, $maxhealth, $powerReq, $output    		    
    }  	    

    public function onConstructed($ship, $turn, $phase){
        parent::onConstructed($ship, $turn, $phase);
        
		$this->shieldPresets = array();	//Empty, in case.	
		$this->shieldPresets[] = 'Equalise';//Always start with default equalise setting.	
		
		foreach($ship->systems as $system){
			if($system instanceof ThoughtShield){
				if($system->side == 'F' ) 	$this->shieldPresets[] = 'Forward';
				if($system->side == 'A' ) 	$this->shieldPresets[] = 'Aft'; 
				if($system->side == 'FP' ) 	$this->shieldPresets[] = 'ForwardPort'; 
				if($system->side == 'FS' ) 	$this->shieldPresets[] = 'ForwardStarboard'; 
				if($system->side == 'AP' ) 	$this->shieldPresets[] = 'AftPort'; 
				if($system->side == 'AS' ) 	$this->shieldPresets[] = 'AftStarboard'; 
			}			
		}
    }
		
    public function setSystemDataWindow($turn){
        parent::setSystemDataWindow($turn);
		$this->data["Special"] = "Resets all Thoughtshields to " .$this->getOutput(). " at the beginning of each turn.";
        $this->data["Special"] .= "<br>If one CnC on ship is destroyed only regenerates shields by 50%, if both are destroyed no shields are regenerated.";	  		
        $this->data["Special"] .= "<br>During Initial Orders this system can be used to transfer shield power from one shield system to another e.g. front to aft etc.";
        $this->data["Special"] .= "<br>By selecting Shield Presets you will triple the power of any given shield by drawing energy from others, or Equalise all shields.";	        	   
        $this->data["Special"] .= "<br>You cannot commit your Intial Orders if there is an excess or deficit of shield energy in this system.";
 		$this->outputDisplay = $this->storedCapacity;
 		$this->data["Output"] = $this->getOutput();               
    }

	
	//always redefine $this->data, variable information goes there...
	public function stripForJson(){
        $strippedSystem = parent::stripForJson();
        $strippedSystem->data = $this->data;
        //$strippedSystem->shieldPresets = $this->shieldPresets;
        if ($this->storedCapacity !== 0) $strippedSystem->storedCapacity = $this->storedCapacity;       
        if ($this->presetCurrClass !== '') $strippedSystem->presetCurrClass = $this->presetCurrClass;  
		
        return $strippedSystem;
    }
							
} //endof ThoughtShieldGenerator

//Mindrider Hangar operates as way to keep track of how many Thought Projections Mindriders can have in play.
class MindriderHangar extends ShipSystem{
    public $name = "MindriderHangar";
    public $displayName = "Hangar";
    public $primary = true;
    public $iconPath = "Mindriderhangar.png";
    
	public $isPrimaryTargetable = true; //true if hangar has capacity
	public $isTargetable = true; //true if hangar has capacity
	protected $doCountForCombatValue = true; //true if hangar has capacity  
	
	public static $alreadyCleared = array();    	
	public static $hangarList = array(); //array of Mindrider Hangars in game
	public static $projectionList = array(); // array of Thought Projection flights in game
	public $output = 0;
    
    protected $possibleCriticals = array(
    );	

    function __construct($armour, $maxhealth, $powerReq, $output){
        parent::__construct($armour, $maxhealth, $powerReq, $output ); 
	    MindriderHangar::$hangarList[] = $this;
	    $this->output = $output;
	    
	    if($output == 0){
			$this->iconPath = "hangarTechnical.png";	    	
			$this->isPrimaryTargetable = false; //change to false if if hangar has no capacity
			$this->isTargetable = false; //change to false if if hangar has no capacity
			$this->doCountForCombatValue = false; //change to false if if hangar has no capacity	    	
	    }	    
    }
	
	
	//to be called by every Thought Projection flight after creation
    public static function addProjections($projectionflight){
	    MindriderHangar::$projectionList[] = $projectionflight;
    }
	
	//inactive entries (from other gamedata) might have slipped by... clear them out!
	public static function clearLists($gamedata, $ship){
	    // Mark $alreadyCleared so it only happens once per turn.
	    
		MindriderHangar::$alreadyCleared[] = $ship->team;
		$tmpArray = array();
		foreach(MindriderHangar::$hangarList as $curr){
			$ship = $curr->getUnit();
			//is this unit defined in current gamedata? (particular instance!)
			$belongs = $gamedata->shipBelongs($ship);
			if ($belongs){
				$tmpArray[] = $curr;
			}			
		}
		MindriderHangar::$hangarList = $tmpArray;
		$tmpArray = array();
		foreach(MindriderHangar::$projectionList as $curr){
			//is this unit defined in current gamedata? (particular instance!)
			$belongs = $gamedata->shipBelongs($curr);
			if ($belongs){
				$tmpArray[] = $curr;
			}			
		}
		MindriderHangar::$projectionList = $tmpArray;
	}//endof function clearLists

	//effects that happen in Critical phase (after criticals are rolled) - replenishment from active Generator 
	public function criticalPhaseEffects($ship, $gamedata) {
	    $thisShip = $this->getUnit();
	    
	    foreach(MindriderHangar::$alreadyCleared as $team){
	    	if($team == $thisShip->team)	return; // Already checked for this team, no further action neeed for other Hangars.
		}
		
		$this->clearLists($gamedata, $thisShip);	
	    $hangarCapacity = 0;

	    foreach (MindriderHangar::$hangarList as $hangar) {
	        $hangarShip = $hangar->getUnit();
	        if ($hangarShip->userid != $thisShip->userid) continue; // Not interested in non-friendly ships.
	        if ($hangarShip->isDestroyed()) continue; // Ignore destroyed ships - destroyed Hangars are actually fine.

	        $hangarCapacity += $hangar->output; // Add output of Hangar (e.g. how many Thought Projections ship can sustain).
	    }

	    $activeThoughts = 0;

	    foreach (MindriderHangar::$projectionList as $projection) {
	        if ($projection->userid != $thisShip->userid) continue; // Not interested in non-friendly ships.
	        if ($projection->isDestroyed()) continue; // Ignore destroyed flights.

	        foreach ($projection->systems as $ftr) {
	            if ($ftr->isDestroyed()) continue; // Do not count destroyed/disengaged fighters.
	            $activeThoughts += 1;
	        }
	    }

	    while ($activeThoughts > $hangarCapacity) {//Too many Projection active!
	    	
	    	$noToDisengage = $activeThoughts - $hangarCapacity;
	        $disengaged = $this->dropoutFighters($thisShip, $gamedata, $noToDisengage);
	        $activeThoughts -= $disengaged;
	    }
	    
	    parent::criticalPhaseEffects($ship, $gamedata); // Call parent to apply effects like Limpet Bore.	    
	} // end of function criticalPhaseEffects



	public function dropoutFighters($thisShip, $gamedata, $noToDisengage = 0) {
	    $fighterCount = 0;
	    $toDisengage = $noToDisengage;

		if($toDisengage > 0){

		    if (empty(MindriderHangar::$projectionList)) {
		        return $fighterCount;
		    }

		    $randomPick = array_rand(MindriderHangar::$projectionList);
		    $projectionFlight = MindriderHangar::$projectionList[$randomPick];

		    if ($projectionFlight->team != $thisShip->team || $projectionFlight->isDestroyed()) {
		        return $fighterCount; // Return if invalid flight
		    }

		    $fighters = $projectionFlight->systems;

		    // Create an array of valid indices
		    $validIndices = [];
		    foreach ($fighters as $index => $fighter) {
		        if ($fighter !== null && !$fighter->isDestroyed()) {
		            $validIndices[] = $index;
		        }
		    }

		    if (empty($validIndices)) {
		        return $fighterCount; // No valid fighters in this flight
		    }

		    // Sort the valid indices in descending order
		    rsort($validIndices);

		    $rammingSystem = $thisShip->getSystemByName("RammingAttack");
		    $newFireOrder = null;

		    if ($rammingSystem) { // Actually exists! - it should on every ship!
		        $shotsHit = 1;

		        $newFireOrder = new FireOrder(
		            -1, "normal", $thisShip->id, $thisShip->id,
		            $rammingSystem->id, -1, $gamedata->turn, 1,
		            100, 100, 1, $shotsHit, 0,
		            0, 0, 'NoHangar', 10000
		        );

		        $newFireOrder->addToDB = true;
		        $rammingSystem->fireOrders[] = $newFireOrder;
		    }

		    // Iterate over the valid indices
		    foreach ($validIndices as $i) {
		        $fighter = $fighters[$i];

		        // Check if the fighter is null or destroyed
		        if ($fighter == null || $fighter->isDestroyed()) {
		            continue;
		        }

		        // Create Dropout crit
		        $crit = new DisengagedFighter(-1, $projectionFlight->id, $fighter->id, "DisengagedFighter", $gamedata->turn);
		        $crit->updated = true;
		        $crit->inEffect = true;
		        $fighter->criticals[] = $crit;
		        if ($newFireOrder) {
		            $newFireOrder->pubnotes .= "<br>The Mindriders have lost control of a Thought Projection! ";
		        }
		        $fighterCount += 1;
		        $toDisengage -= 1;
		        if($toDisengage < 1) break;
		        
		        //At least make the FireOrder target Projections.		        
		        $newFireOrder->targetid = $projectionFlight->id;        		        
		    }
		}
	    return $fighterCount;
	    
	} // end of dropoutFighters

		
    public function setSystemDataWindow($turn){
	parent::setSystemDataWindow($turn);
		if($this->output == 0){	     
			$this->data["Special"] = "Technical system only.";
		}else{		       	     
			$this->data["Special"] = "Hangar capacity equals number of Thought Projections this ship can control.";
			$this->data["Special"] .= "Cannot dock or launch fighters.";			
		}    
    }	    	

}//endof MindriderHangar



	//Torvalus Shading Field - Can let them stealth but also works as a Jammer and EM Shield!
	class ShadingField extends ShipSystem implements SpecialAbility, DefensiveSystem{    
		public $name = "ShadingField";
		public $displayName = "Shading Field";
		public $specialAbilities = array("Jammer", "Stealth");
		public $primary = true;
		public $detected = true;
		public $detectedNew = array(); // New multi-team array logic
		//defensive system
		public $defensiveSystem = true;
		public $tohitPenalty = 0;
		public $damagePenalty = 0;
		public $rangePenalty = 0;
		public $range = 5;
		protected $active = false; //To track in Front End whether system was ever activate this turn during Deployment/PreOrders.				
		
		function __construct($armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc){
			// shieldfactor is handled as output.
			parent::__construct($armour, $maxhealth, $powerReq, $shieldFactor);
			
			$this->startArc = (int)$startArc;
			$this->endArc = (int)$endArc;
		}
		

		public function onConstructed($ship, $turn, $phase){
			parent::onConstructed($ship, $turn, $phase);
			$this->tohitPenalty = $this->getOutput();
			$this->damagePenalty = $this->getOutput();
		}

		protected $possibleCriticals = array(
			26=>array("OutputReduced1")
		);

		public function getDefensiveType()
		{
			return "Shield";
		}

		public function isActivated(){
			return $this->active;			
		}
		
		public function getDefensiveHitChangeMod($target, $shooter, $pos, $turn, $weapon){
			if($this->isDestroyed($turn-1) || $this->isOfflineOnTurn($turn))
				return 0;
			$output = $this->output;			 
			$output += $this->outputMod; //outputMod itself is negative!

			if($target instanceof FighterFlight){
				if(!$this->active){
					return 0; //Fighters and not shaded, no defence mod.	
				}else{					
					return $output; //Shaded, hit mod applies!
				} 			
			}else{ //Is a ship!
				if ($this->active) $output = $output *2; //If in Shading Mode, double hit mod.			
				return $output;				
			}       
		}
		
		public function getDefensiveDamageMod($target, $shooter, $pos, $turn, $weapon){
			if($this->isDestroyed($turn-1) || $this->isOfflineOnTurn() || $target instanceof FighterFlight)
				return 0;		
			
			$output = $this->output;
			$output += $this->outputMod; //outputMod itself is negative!
			return $output;
		}

		public function setSystemDataWindow($turn){
				$unit = $this->getUnit();
				if($unit instanceof FighterFlight){
					//$this->data["Special"] = "Jammer ability, even against Ancients.";
					$this->data["Special"] = "<br>Can use 'Shading Mode' by activating this system during Deployment/Pre-Turn Phase.";						
					$this->data["Special"] .= "<br>When Shading is activated, defense ratings are reduced by 15, and cannot be detected if over 15 hexes at the start or end of movement.";
					$this->data["Special"] .= "<br>HOWEVER, the flight cannot fire any weapons on a turn when Shading is active.";
					$this->data["Special"] .= "<br>This system also incorporates a small Jump Drive, with a 20 turn recharge.";									
				}else{
					$this->data["Special"] = "Jammer ability, even against Ancients.";
					$this->data["Special"] .= "<br>Provides EM Shield.";
					$this->data["Special"] .= "<br>Can use 'Shading Mode' by activating this system during Deployment/Pre-Turn Phase.";														
					$this->data["Special"] .= "<br>When Shading is active, ship cannot be detected if over 15 hexes away from all enemy units at the start or end of movement.";
					$this->data["Special"] .= "<br>EM Shield ratings are also doubled for hit chance modifier when Shaded.";									
					$this->data["Special"] .= "<br>HOWEVER, ship cannot fire any weapons on a turn when Shading is active.";
				}	
		}	
		
		//args for Jammer ability are array("shooter", "target")
		public function getSpecialAbilityValue($args)
		{
			$ship = $this->getUnit();
			if($ship instanceof FighterFlight){
				return 0; //Torvalus fighters don't get the Jammer effect.
			}

			if (!isset($args["shooter"]) || !isset($args["target"]))
				throw new InvalidArgumentException("Missing arguments for Jammer getSpecialAbilityValue");
			
			$shooter = $args["shooter"];
			$target = $args["target"];
			
			if (! ($shooter instanceof BaseShip) || ! ($target instanceof BaseShip)) 
				throw new InvalidArgumentException("Wrong argument type for Jammer getSpecialAbilityValue");
					
			if(!$this->isDestroyed() && !$this->isOfflineOnTurn()){
				$jammerValue = 1;
			} else {
				$jammerValue = 0; //never negative
			}
				
			return $jammerValue;
		}


	public function doIndividualNotesTransfer(){
		//data received in variable individualNotesTransfer, further functions will look for it in currchangedAA
		if(is_array($this->individualNotesTransfer)){			
			foreach($this->individualNotesTransfer as $shadingChange){			
				if($shadingChange == 1){
					$this->active = true;
				}else{
					$this->active = false; //May start Deployment phase as true via notes
				}									
			}
		} 
		$this->individualNotesTransfer = array(); //empty, just in case
	}			

    public function generateIndividualNotes($gameData, $dbManager){ //dbManager is necessary for Initial phase only
		$this->doIndividualNotesTransfer();
		$ship = $this->getUnit();	
		
		switch($gameData->phase){
			
			case -1:
				if ($this->active) {
						$notekey = 'Shaded';
						$noteHuman = 'Shaded this turn';
						$noteValue = 1;
						$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$noteValue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue
				}else{
						$notekey = 'Unshaded';
						$noteHuman = 'Not Shaded this turn';
						$noteValue = 1;
						$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$noteValue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue
				}
			break;

			/* Opening a jump point BREAKS the shading and reveals the ship (JUMP_POINTS_PLAN.md
			   section 2.1, user ruling 2026-08-21). Both halves are needed: the reveal alone would
			   not survive, because checkStealthNextPhase re-runs at the end of Movement and, with
			   $active still true, would write 'undetected' again for every team out of range. */
			case 1:
				$this->individualNotes = array_merge(
					$this->individualNotes,
					JumpEngine::vortexRevealNotes($ship, $this->id, $gameData, 'Unshaded')
				);
			break;

		}
	}

		public function onIndividualNotesLoaded($gamedata){
			//Sort notes by turn, and then phase so latest detection note is always last.
			$this->sortNotes();
			if (!is_array($this->detectedNew)) $this->detectedNew = array();

			foreach ($this->individualNotes as $currNote){ //Search all notes, they should be process in order so the latest event applies.
				switch($currNote->notekey){
					case 'detected': 
						$this->detected = true;
						if (strpos($currNote->notevalue, 'Team:') === 0) {
							$teamId = (int) substr($currNote->notevalue, 5);
							if (!in_array($teamId, $this->detectedNew)) {
								$this->detectedNew[] = $teamId;
							}
						}
					break;
					case 'undetected': 
						$this->detected = false;						
						if (strpos($currNote->notevalue, 'Team:') === 0) {
							$teamId = (int) substr($currNote->notevalue, 5);
							$this->detectedNew = array_values(array_diff($this->detectedNew, [$teamId]));
						}
					break;
					case 'Shaded': 
						if($currNote->turn == $gamedata->turn || $gamedata->phase == -1 && $currNote->turn == $gamedata->turn-1){					
							$this->active = true;
						}								
					break;	
					case 'Unshaded': 
						if($currNote->turn == $gamedata->turn || $gamedata->phase == -1 && $currNote->turn == $gamedata->turn-1){					
							$this->active = false;
						}								
					break;																				
				}
			}

			//and immediately delete notes themselves, they're no longer needed (this will not touch the database, just memory!)
			$this->individualNotes = array();		
		} //endof function onIndividualNotesLoaded


		private function sortNotes() {
			usort($this->individualNotes, function($a, $b) {
				// Compare by turn first
				if ($a->turn == $b->turn) {
					// If turns are equal, compare by phase
					return ($a->phase < $b->phase) ? -1 : 1;
				}
				return ($a->turn < $b->turn) ? -1 : 1;
			});
		}

		//Called in Deployment->advance() and Movement->advance()				
		public function checkStealthNextPhase($gamedata, $range = 15){				
			$ship = $this->getUnit();
			if($gamedata->phase == 1){ 
				$noteHuman1 = 'D-detectedActive';
				$noteHuman2 = 'D-undetectedActive';						
				$noteHuman3 = 'D-NotActive';						
			}else{
				$noteHuman1 = '2-detectedActive';
				$noteHuman2 = '2-undetectedActive';						
				$noteHuman3 = '2-NotActive';						
			}

			// Get all enemy teams in the game
			$enemyTeams = array();
			foreach ($gamedata->slots as $slot) {
				$teamId = (int)$slot->team;
				if ($teamId != $ship->team && !in_array($teamId, $enemyTeams)) {
					$enemyTeams[] = $teamId;
				}
			}

			// If we're checking during DeploymentGamePhase->Advance (actually Phase 1 at this point).					
			if ($this->active) {
				$detectingTeams = $this->isDetected($ship, $gamedata, $range);

				foreach ($enemyTeams as $teamId) {
					if (in_array($teamId, $detectingTeams)) {
						$notekey   = 'detected';
						$noteHuman = $noteHuman1;
						$noteValue = "Team:" . $teamId;							
					} else {
						$notekey   = 'undetected';
						$noteHuman = $noteHuman2;
						$noteValue = "Team:" . $teamId;							
					}

					$note = new IndividualNote(
							-1,
							$gamedata->id,
							$gamedata->turn,
							$gamedata->phase,
							$ship->id,
							$this->id,
							$notekey,
							$noteHuman,
							$noteValue
					);
					Manager::insertIndividualNote($note);	
				}
			} else {
				foreach ($enemyTeams as $teamId) {
					$notekey   = 'detected';
					$noteHuman = $noteHuman3; //Not shaded yet or was shaded and then turned off.
					$noteValue = "Team:" . $teamId;						

					$note = new IndividualNote(
							-1,
							$gamedata->id,
							$gamedata->turn,
							$gamedata->phase,
							$ship->id,
							$this->id,
							$notekey,
							$noteHuman,
							$noteValue
					);
					Manager::insertIndividualNote($note);	
				}
			}
		}


		private function isDetected($ship, $gameData, $range) {
	
			//$blockedHexes = $gameData->getBlockedHexes(); //Just do this once outside loop
			$blockedHexes = $gameData->blockedHexes; //Just do this once outside loop				
			$pos = $ship->getHexPos(); //Just do this once outside loop	
			
			$detectedTeams = array();

			foreach ($gameData->ships as $otherShip) {
				// Skip friendly ships
				$teamId = (int)$otherShip->team;
				if($teamId == $ship->team) continue;
				if($otherShip->isTerrain()) continue; //Ignore Terrain
				if($otherShip->isDestroyed()) continue; //Ignore destroyed enemy ships.
				if(in_array($teamId, $detectedTeams)) continue;

				// If within detection range, and LoS not blocked the ship is detected
				// Get distance to the stealth ship and check line of sight
				$distance = mathlib::getDistanceHex($ship, $otherShip);
				$otherPos = $otherShip->getHexPos(); //Just deployed ships aren't counting for this.          
				$noLoS = !empty($blockedHexes) && Mathlib::isLoSBlocked($pos, $otherPos, $blockedHexes);

				// If within detection range, and LoS not blocked the ship is detected
				if($distance <= $range && !$noLoS){
					$detectedTeams[] = $teamId;
				}		
			}

			return $detectedTeams;			
		}	

		public function stripForJson(){
			$strippedSystem = parent::stripForJson();
			$strippedSystem->detected = $this->detected;
			if (isset($this->detectedNew) && !empty($this->detectedNew)) {
				$strippedSystem->detectedNew = $this->detectedNew;
			}
			//$strippedSystem->detectedNew = is_array($this->detectedNew) ? $this->detectedNew : array();
			//enemy viewers never see the shading state directly - they find out through the
			//detection rules and resolved fire (their hit preview shows the unshaded mod)
			if(TacGamedata::$currentPhase == -1){
				$strippedSystem->active = $this->isRevealedToCurrentViewer() ? $this->active : false; 
			}else{
				$strippedSystem->active = $this->active;
			}
			return $strippedSystem;
		}

	} //endof ShadingField

//Early version of the Torvalus Shading Field, actually just a glorified EM Shield, with Jammer effect against Younger Races.
class AlphaShadingField extends EMShield implements SpecialAbility, DefensiveSystem{
    public $name = "AlphaShadingField";
    public $displayName = "Alpha Shading Field";
    public $iconPath = "ShadingField.png";
    public $canOffLine = true;
	public $specialAbilities = array("Jammer");			

    function __construct($armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc){
        // shieldfactor is handled as output.
        parent::__construct($armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc);
    }

	//args for Jammer ability are array("shooter", "target")
    public function getSpecialAbilityValue($args)
    {
        if (!isset($args["shooter"]) || !isset($args["target"]))
            throw new InvalidArgumentException("Missing arguments for Jammer getSpecialAbilityValue");
        
        $shooter = $args["shooter"];
        $target = $args["target"];
        
        //if ($shooter->faction === $target->faction) return 0; //same-faction units ignore Jammer
		
        if (! ($shooter instanceof BaseShip) || ! ($target instanceof BaseShip)) 
            throw new InvalidArgumentException("Wrong argument type for Jammer getSpecialAbilityValue");
        		
		$jammerValue = 1;
		
		if ($jammerValue > 0){ //else no point
			//Advanced Sensors negate Jammer, Improved Sensors halve Jammer
			if ($shooter->hasSpecialAbility("AdvancedSensors")) {
				$jammerValue = 0; //negated
			}
		} else {
			$jammerValue = 0; //never negative
		}
			
        return $jammerValue;
    }

}	

class MineControllerDEW extends ShipSystem{
    public $name = "MineControllerDEW";
    public $displayName = "Mine Controller";
	public $iconPath = "Computer.png";
    public $outputType = "settings";	    
    public $isTargetable = false; //cannot be targeted ever!
	public $isPrimaryTargetable = false;        
    public $canOffline = true;
	public $currClass = '';//for front end.       
    public $allocatedRanges = array('Capitals-HCVs' => null, 'LCVs-MCVs' => null, 'Fighters' => null); //Ranges allocated for given ship type. When multiTargetEnabled, becomes nested {weaponId: {shipType: range}}.
    public $setRanges = array(); //Ranges allocated for given ship type
    public $mineSet = false; //For front end, to confirm mine ranges have been manually set.
	public $rangeSetting = 0;
	private $accuracy = 0;
	public $ballisticWeapon = false; //To mark if mine has ballistic weapons
    public $validTargets = null; // null means all targets are valid
    public $multiTargetEnabled = false; //Mirrors $mine->multiSettings (Flexible Targeting); switches allocatedRanges to per-weapon nested storage.
    public $mineWeapons = array(); //Precomputed list of weapons {id, name, displayName, indexInGroup, label} for client UI; populated in stripForJson.

    function __construct($armour, $maxhealth, $powerReq, $range, $accuracy, $ballistic = false, $validTargets = null){
	//maxhealth and power reqirement are fixed; left option to override with hand-written values
        if ( $maxhealth == 0 ) $maxhealth = 1;
        if ( $powerReq == 0 ) $powerReq = 0;  
        $this->rangeSetting = $range;
		$this->accuracy = $accuracy;
		$this->outputDisplay = '-';
		$this->ballisticWeapon = $ballistic;
        $this->validTargets = $validTargets;

        parent::__construct($armour, $maxhealth, $powerReq, $range);
    }				

    public function setSystemDataWindow($turn){
            $mine = $this->getUnit();

            //Wipe stale per-shipType / per-weapon range keys so a mode flip never leaves both kinds in the dict.
            foreach (array_keys($this->data) as $k) {
                if (strncmp($k, ' - ', 3) === 0) unset($this->data[$k]);
            }

            $this->data["Max Range"] = $this->rangeSetting;
            $this->data["Accuracy"] = $this->accuracy;
            if ($mine->multiSettings) {
                $weaponLabels = $this->buildWeaponLabelMap();
                foreach ($this->allocatedRanges as $weaponId => $perType) {
                    if (!is_array($perType)) continue;
                    $label = isset($weaponLabels[$weaponId]) ? $weaponLabels[$weaponId] : ('weapon '.$weaponId);
                    foreach ($perType as $shipType => $range) {
                        $this->data[' - '.$label.' '.$shipType.' range'] = $range;
                    }
                }
            } else {
                foreach($this->allocatedRanges as $shipType=>$range){
                    if (is_array($range)) continue; //defensive: ignore any nested entries left from a prior multi state
                    $this->data[' - '.$shipType.' range'] =  $range;
                }
            }
			$this->data["Special"] = "<br>Used to set ranges for DEW Mine's weapon against different types of enemy. ";
			$this->data["Special"] .= "<br>Ranges are set on turn that the Mine deploys, and these cannot then be changed.";
			$this->data["Special"] .= "<br>All attacks by DEW mines assume an EW lock, except against Jammer-equipped ships.";
			if ($mine->multiSettings) {
				$this->data["Special"] .= "<br>Multiple Targets enhancement: each weapon has its own per-target-type range.";
			}
	}

	private function buildWeaponLabelMap(){
		$mine = $this->getUnit();
		if (!$mine) return array();
		$labels = array();
		$displayCounts = array();
		$displayTotals = array();
		foreach ($mine->systems as $sys) {
			if ($sys instanceof Weapon && $sys->name !== 'RammingAttack') {
				$d = $sys->displayName ?: $sys->name;
				$displayTotals[$d] = (isset($displayTotals[$d]) ? $displayTotals[$d] : 0) + 1;
			}
		}
		foreach ($mine->systems as $sys) {
			if ($sys instanceof Weapon && $sys->name !== 'RammingAttack') {
				$d = $sys->displayName ?: $sys->name;
				$displayCounts[$d] = (isset($displayCounts[$d]) ? $displayCounts[$d] : 0) + 1;
				$labels[$sys->id] = ($displayTotals[$d] > 1) ? ($d.' '.$displayCounts[$d]) : $d;
			}
		}
		return $labels;
	}


	public function doIndividualNotesTransfer(){
	    // Data received in variable individualNotesTransfer, further functions will look for it in setRanges.
	    // Flat keys like 'Capitals-HCVs' mean single shared settings.
	    // Compound keys like '<weaponId>;<shipType>' (sent when MINE_MULTI is active) populate nested setRanges.
	    if (is_array($this->individualNotesTransfer)) {
	        $this->setRanges = array();
	        foreach ($this->individualNotesTransfer as $key => $rangeValue) {
	            if (strpos((string)$key, ';') !== false) {
	                list($weaponId, $shipType) = explode(';', $key, 2);
	                $weaponId = (int)$weaponId;
	                if (!isset($this->setRanges[$weaponId]) || !is_array($this->setRanges[$weaponId])) {
	                    $this->setRanges[$weaponId] = array();
	                }
	                $this->setRanges[$weaponId][$shipType] = $rangeValue;
	            } else {
	                $this->setRanges[$key] = $rangeValue;
	            }
	        }
	    }
	    $this->individualNotesTransfer = array(); // Empty, just in case
	}

    public function generateIndividualNotes($gameData, $dbManager){ //dbManager is necessary for Initial phase only
		$mine = $this->getUnit();           
		switch($gameData->phase){
						
				case -1: //Deployment/Pre-Turn phase
					//data returned as allocatedBFCP table, with one value passed per BFCP point in each FCType e.g. 'Fighter' mean +1 in allocatedBFCP['Fighter']
					if($mine->userid == $gameData->forPlayer){ //only own mines, otherwise bad things may happen!
						foreach ($this->setRanges as $key => $value) {
							$noteHuman = 'Mine Range set';
							if (is_array($value)) {
								//Nested: $key is weaponId, $value is {shipType => range}. Emit compound notekey "<weaponId>;<shipType>".
								foreach ($value as $shipType => $rangeValue) {
									$notekey = $key.';'.$shipType;
									$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$mine->id,$this->id,$notekey,$noteHuman,$rangeValue);
								}
							} else {
								$notekey = $key;
								$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$mine->id,$this->id,$notekey,$noteHuman,$value);
							}
						}
					}
				break;
				
				case 1: //Need to add some method for ballistic DEW mines to fire here if they have been activated (e.g. already fired once).
	
					if($this->ballisticWeapon && $mine->activated){
						$this->checkAndCreateMineAttacks($gameData, $mine);					
					}
				break;
		}
	} //endof function generateIndividualNotes
	

    public function onIndividualNotesLoaded($gamedata)
    {
			$mine = $this->getUnit();
			// Detect MINE_MULTI before parsing notes so we know which storage shape to populate.
			$this->multiTargetEnabled = false;
			foreach ($mine->enhancementOptions as $enhancement) {
				if ($enhancement[0] == 'MINE_MULTI' && $enhancement[2] > 0) {
					$this->multiTargetEnabled = true;
					break;
				}
			}		
			if($mine->multiSettings) $this->multiTargetEnabled = true; //Some mines have this pre-set

			if ($this->multiTargetEnabled) {
				//Reset to nested shape; per-weapon ranges populated from compound notekeys "<weaponId>;<shipType>".
				$this->allocatedRanges = array();
				foreach ($this->individualNotes as $currNote) {
					$key = $currNote->notekey;
					$rangeValue = $currNote->notevalue;
					if (strpos($key, ';') !== false) {
						list($weaponId, $shipType) = explode(';', $key, 2);
						$weaponId = (int)$weaponId;
						if (!isset($this->allocatedRanges[$weaponId]) || !is_array($this->allocatedRanges[$weaponId])) {
							$this->allocatedRanges[$weaponId] = array('Capitals-HCVs' => null, 'LCVs-MCVs' => null, 'Fighters' => null);
						}
						$this->allocatedRanges[$weaponId][$shipType] = $rangeValue;
					}
				}
			} else {
				//Flat shape: notekey is shipType.
				foreach ($this->individualNotes as $currNote) {
					$shipType = $currNote->notekey;
					$rangeValue = $currNote->notevalue;
					$this->allocatedRanges[$shipType] = $rangeValue;
				}
			}

			// Determine total IMPR_RANG enhancement count and apply to rangeSetting ONCE
			$rangeEnhancement = 0;
			foreach ($mine->enhancementOptions as $enhancement) {
				if ($enhancement[0] == 'MINE_RANG') {
					$rangeEnhancement += $enhancement[2];
				}
			}
			$this->rangeSetting += $rangeEnhancement;

			foreach($mine->systems as $weapon){
				if($weapon instanceof Weapon  && $weapon->name !== "RammingAttack"){
					if($weapon->fireControl[0] !== null) $weapon->fireControl[0] = $weapon->fireControl[0] + $this->accuracy;
					if($weapon->fireControl[1] !== null) $weapon->fireControl[1] = $weapon->fireControl[1] + $this->accuracy;
					if($weapon->fireControl[2] !== null) $weapon->fireControl[2] = $weapon->fireControl[2] + $this->accuracy;

					if (!empty($weapon->fireControlArray)) {
						foreach ($weapon->fireControlArray as $mode => $fcArray) {
							if ($weapon->fireControlArray[$mode][0] !== null) $weapon->fireControlArray[$mode][0] = $weapon->fireControlArray[$mode][0] + $this->accuracy;
							if ($weapon->fireControlArray[$mode][1] !== null) $weapon->fireControlArray[$mode][1] = $weapon->fireControlArray[$mode][1] + $this->accuracy;
							if ($weapon->fireControlArray[$mode][2] !== null) $weapon->fireControlArray[$mode][2] = $weapon->fireControlArray[$mode][2] + $this->accuracy;
						}
					}

					if ($this->multiTargetEnabled && isset($this->allocatedRanges[$weapon->id]) && is_array($this->allocatedRanges[$weapon->id])) {
						//Per-weapon max-of-three (nulls treated as 0); clamp to global rangeSetting ceiling.
						$weaponMax = 0;
						foreach ($this->allocatedRanges[$weapon->id] as $val) {
							if ($val !== null && $val > $weaponMax) $weaponMax = (int)$val;
						}
						$weaponMax = min($weaponMax, $this->rangeSetting);
						$weapon->range = $weaponMax;
						foreach($weapon->rangeArray as $mode => $val) {
							$weapon->rangeArray[$mode] = $weaponMax;
						}
					} else {
						$weapon->range = $this->rangeSetting;
						foreach($weapon->rangeArray as $mode => $val) {
							$weapon->rangeArray[$mode] = $this->rangeSetting;
						}
					}
					$weapon->isTargetable = false;
					$weapon->boostable = false;

					if(!$mine->getCommandControl()){
						$weapon->autoFireOnly = true;
						$weapon->canOffLine = false;
					}
				}
			}

            //and immediately delete notes themselves, they're no longer needed (this will not touch the database, just memory!)
            $this->individualNotes = array();


    }//endof onIndividualNotesLoaded


    public function beforeFiringOrderResolution($gamedata){
        $mine = $this->getUnit();		
		if($this->ballisticWeapon && $mine->activated)	return; //Ballistic weapon mines fire in Initial Orders after their first activation	
		$this->checkAndCreateMineAttacks($gamedata, $mine);			
	}


    public function checkAndCreateMineAttacks($gamedata, $mine){
        
        if($this->isOfflineOnTurn()) return; //Mine weapon deactivated.

        //$mine = $this->getUnit();
        if($mine->isDestroyed()) return; //Mine is destroyed.
		$deployTurn = $mine->getTurnDeployed($gamedata);
		if($deployTurn > $gamedata->turn) return;  //Ship not deployed yet, don't fire weapon!

		// Mines are stationary — their position is always their deploy-move position.
		// We deliberately avoid getHexPos() because mines have no subsequent movement
		// records and getHexPos() would crash on a null movement.
		$minePosition = null;
		foreach ($mine->movement as $move) {
			if ($move->type === 'deploy') {
				$minePosition = $move->position;
				break;
			}
		}
		if ($minePosition === null) return; // Mine has no deploy move yet, can't be detected	

        $IFFSystem = $mine->getIFFSystem();
		
    	if($this->isDestroyed($gamedata->turn)) return;//Pulsar Mine is destroyed
		if($this->isOfflineOnTurn($gamedata->turn)) return; //Pulsar Mine is offline

    	$allShips = $gamedata->ships;  
    	$relevantUnits = array();

		//Make a list of relevant ships e.g. this ship and enemy fighters in the game.
		foreach($allShips as $ship){
            if ($ship instanceof Terrain) continue;
            if ($ship instanceof Mine) continue;  
            if ($ship->base || $ship instanceof OSAT) continue; //They are movement activated.          
			if ($ship->isDestroyed()) continue;		
			if ($ship->id == $mine->id) continue; // Mine should never target itself!
			if ($ship->team == $mine->team && $IFFSystem) continue;	//Ignore friendly units if IFF purchased.	
			if ($ship->getTurnDeployed($gamedata) > $gamedata->turn) continue;  //Ignore units that are not deployed yet!			
			$relevantUnits[] = $ship;			
		}
	
        $mineTarget = null;
		//Now check if any enemy units entered range and attack first one
		$mineTarget = $this->checkForValidTargets($relevantUnits, $mine, $minePosition, $gamedata);	        	

		if ($mineTarget !== null) { // Check if we found a valid target

			//Per-weapon range gate (MINE_MULTI): compute target shipType and the closest distance it reached this turn.
			$targetShipType = 'Capitals-HCVs';
			$targetMinDistance = PHP_INT_MAX;
			if ($this->multiTargetEnabled) {
				$tFCIndex = $mineTarget->getFireControlIndex();
				if ($tFCIndex == 0) $targetShipType = 'Fighters';
				else if ($tFCIndex == 1) $targetShipType = 'LCVs-MCVs';

				$startMove = $mineTarget->getLastTurnMovement($gamedata->turn);
				if ($startMove != null) {
					$d = mathlib::getDistanceHex($minePosition, $startMove->position);
					if ($d < $targetMinDistance) $targetMinDistance = $d;
				}
				if ($gamedata->phase != 1) {
					foreach ($mineTarget->movement as $tMove) {
						if ($tMove->turn == $gamedata->turn && ($tMove->type == 'move' || $tMove->type == 'slipleft' || $tMove->type == 'slipright')) {
							$d = mathlib::getDistanceHex($minePosition, $tMove->position);
							if ($d < $targetMinDistance) $targetMinDistance = $d;
						}
					}
				}
				$jammerValue = $mineTarget->getSpecialAbilityValue("Jammer", array("shooter" => $mine, "target" => $mineTarget));
			}

			//Loop through mine's weapon and create fire orders against target.
			foreach($mine->systems as $weapon){
				if($weapon instanceof Weapon && $weapon->name !== "RammingAttack"){
					if($weapon->isDestroyed($gamedata->turn)) return;//Is destroyed (shouldn't happen)

					if ($this->multiTargetEnabled) {
						if (!isset($this->allocatedRanges[$weapon->id]) || !is_array($this->allocatedRanges[$weapon->id])) continue;
						$weaponRange = isset($this->allocatedRanges[$weapon->id][$targetShipType]) ? $this->allocatedRanges[$weapon->id][$targetShipType] : null;
						if ($weaponRange === null || $weaponRange <= 0) continue; //Player chose this weapon doesn't engage this target type.
						$effectiveWeaponRange = (isset($jammerValue) && $jammerValue > 0) ? floor($weaponRange / 2) : $weaponRange;
						if ($targetMinDistance > $effectiveWeaponRange) continue; //Target never within this weapon's range.
					}

					if($weapon->getTurnsloaded() >= $weapon->getNormalLoad() && !$weapon->firedOnTurn($gamedata->turn)){ //is Loaded.  Accelerator weapons should only fire when fully loaded too.

						if($mine->getCommandControl()){  
							if($weapon->isOfflineOnTurn($gamedata->turn)) return; //Is been turned offline							          
							$firingOrders = $weapon->getFireOrders($gamedata->turn);
							
							$hasFireOrder = null;
									foreach ($firingOrders as $fireOrder) { 
										if ($fireOrder->type == 'normal' || $fireOrder->type == 'ballistic') { 
										$hasFireOrder = $fireOrder;
										break; //no need to search further
										}
									}    			
									
							if($hasFireOrder !== null) return; //Has a manual fire order, end of work
						}   

						if($weapon instanceof AmmoMissileRackS){
							$magazine =  $mine->getSystemByName("AmmoMagazine");
							$modeName = $weapon->firingModes[$weapon->firingMode];
									
							if($magazine){ //else something is wrong - weapon is put on a ship without Ammo Magazine!
								if($magazine->ammoCountArray[$modeName] > 0){ //Has ammunition available for mode.
									$magazine->doDrawAmmo($gamedata, $modeName);
								}else{
									return;
								}	
							}
						}	

						$guns = $weapon->guns; //Some weapons can fire more than once, like Twin arrays.


						if($gamedata->phase == 1){
							$type = "ballistic";
						}else{
							$type = "normal";
						} 
						while ($guns > 0){
						//Now create fireorder(s)
							$newFireOrder = new FireOrder(
								-1, $type, $mine->id, $mineTarget->id,
								$weapon->id, -1, $gamedata->turn, $weapon->firingMode, 
								0, 0, 1, 0, 0, //needed, rolled, shots, shotshit, intercepted
								0,0,$weapon->weaponClass,-1 //X, Y, damageclass, resolutionorder
							);		

							$newFireOrder->addToDB = true;
							$weapon->fireOrders[] = $newFireOrder;
							$guns--;
						}	
					}
				}
			}        
		}			

	} //endof beforePreFiringOrderResolution


	private function checkForValidTargets($relevantUnits, $mine, $minePosition, $gamedata){

        // Sort units by ascending initiative (lower value = moved first this turn).
        usort($relevantUnits, function($a, $b) {
            $iniA = ($a->iniative ?? 0) + ($a->iniativeadded ?? 0);
            $iniB = ($b->iniative ?? 0) + ($b->iniativeadded ?? 0);
            return $iniA <=> $iniB;
        });

		foreach($relevantUnits as $unit){//Now look through relevant ships and take appropriate action.				
										    
			$unitStartLoc = $unit->getLastTurnMovement($gamedata->turn);
            if($unitStartLoc == null) continue;
								
			//Check if unit can be attacked in its starting position	
			if($this->checkTargetConditions( $minePosition, $unitStartLoc->position, $gamedata, $mine, $unit)){
                return $unit;                   
			}

			if($gamedata->phase != 1){ //Don't need to do this check for ballistic weapons in Phase 1 (Initial Orders).
				//Now check other movements in turn	
				foreach($unit->movement as $unitMove){
					if($unitMove->turn == $gamedata->turn){
						// Only interested in moves where unit enters a NEW hex!
						if ($unitMove->type == "move" || $unitMove->type == "slipleft" || $unitMove->type == "slipright") {

							if($this->checkTargetConditions($minePosition, $unitMove->position, $gamedata,  $mine, $unit)) {
							//get bearing / location and return that too		    			
								return $unit;
						}else{
								continue;
							}
						} else {

						}    		 		 		
					}
				}					
			}
		}				

	    return null; 		
		
	}//end of checkForValidTargets    


	private function checkTargetConditions($minePosition, $targetPostion, $gamedata, $mine, $target){

		$distance =	mathlib::getDistanceHex($minePosition, $targetPostion);//Compare starting positions.
        $effectiveRange = $this->rangeSetting; //Start with max range.

        $shipType = 'Capitals-HCVs'; //Default as Captials.
        $FCIndex = $target->getFireControlIndex(); //Get FC array index of potential target.
        if($FCIndex == 0){ //Fighters
            $shipType = 'Fighters';
        }else if($FCIndex == 1){ //LCV or MCV
            $shipType = 'LCVs-MCVs';
        }

        if ($this->multiTargetEnabled) {
            //Mine wakes up if ANY weapon can reach this shipType. Use max across all weapons.
            $maxRange = 0;
            foreach ($this->allocatedRanges as $weaponId => $perType) {
                if (!is_array($perType)) continue;
                if (!isset($perType[$shipType]) || $perType[$shipType] === null) continue;
                if ($perType[$shipType] > $maxRange) $maxRange = (int)$perType[$shipType];
            }
            if ($maxRange <= 0) return false; //No weapon assigned to this target type.
            $effectiveRange = $maxRange;
        } else {
            if($this->allocatedRanges[$shipType] !== null){
                $effectiveRange = $this->allocatedRanges[$shipType]; //Find and set appropriate range for this type of target.
            }else{
                return false; //Null mean weapon can't target this ship type.
            }
        }

        //take into account jammer effects.
		$jammerValue = $target->getSpecialAbilityValue("Jammer", array("shooter" => $mine, "target" => $target));
		if($jammerValue > 0) $effectiveRange = floor($effectiveRange / 2);

	    if ($distance > $effectiveRange) return false; //Not within range, skip LoS check and return false.

        $loSBlocked = false;
        if (!empty($gamedata->blockedHexes)) {            
            $loSBlocked = Mathlib::isLoSBlocked($minePosition, $targetPostion, $gamedata->blockedHexes);
        }		
		if($loSBlocked) return false; //LoS Blocked

		return true;
	}	

    public function stripForJson() {
        $strippedSystem = parent::stripForJson();
        //Defensive re-detect (covers serialization paths where setSystemDataWindow hasn't run yet).
        $mine = $this->getUnit();
        $this->multiTargetEnabled = ($mine && !empty($mine->multiSettings));
        $strippedSystem->allocatedRanges = $this->allocatedRanges;
        $strippedSystem->rangeSetting = $this->rangeSetting;
        $strippedSystem->validTargets = $this->validTargets;
        $strippedSystem->multiTargetEnabled = $this->multiTargetEnabled;
        $strippedSystem->data = $this->data;
        return $strippedSystem;
    }
	    

} //endof class MineControllerDEW



/* Ammunition magazine
technical system, storing information about available (and used) consumable weapons (primarily ballistic ones)
*/
class AmmoMagazine extends ShipSystem {
    public $name = "ammoMagazine";
    public $displayName = "Ammunition Magazine";
    public $iconPath = "AmmunitionMagazineTechnical.png";
    public $primary = true;
	public $isPrimaryTargetable = false; //shouldn't be targetable at all, in fact!
	public $isTargetable = false; //cannot be targeted ever!
	protected $doCountForCombatValue = false; //don't count when estimating remaining combat value
	
	public $capacity = 0;
	public $remainingAmmo = 0;
	private $ammoUsedTotal = array(); //ammo marked as used by notes
	
	private $ammoArray = array();	
	private $ammoJustUsed = array(); //temporary array - ammo usage information received from front end, to be saved to database
	private $weaponsServed = array(); //list of weapons served by this weapon - used to notify them of ammo availability updates
	
	public $ammoCountArray = array();
	public $ammoSizeArray = array();
	public $ammoUseArray = array(); //to be used in front end to track actual ammo usage
	public $output = 0;
	
	private $interceptorUsed = 0;//Communication variable.	
	private $ammoAlreadyUsed = array();
	public $startingAmmo = null;
		
    
    function __construct($capacity){ //magazine capacity
        parent::__construct(0, 1, 0, 1); //technical system, armor and structure don't really matter
		$this->capacity = $capacity;
    }
    
    public function setSystemDataWindow($turn){
		//count remaining ammo total
		foreach($this->ammoArray as $currAmmo){
			$count = $this->ammoUsedTotal[$currAmmo->modeName];
			$size = $currAmmo->size;
			$this->remainingAmmo -= $count * $size;			
		}
		
		$this->output = $this->remainingAmmo; //just to always show on a glance how many rounds total remain!
	    $this->data["Special"] = "Technical system, keeping track of consumable ammo."; 
	    //add information about currently stored ammo!
	    $this->data["Special"] .= "<br>Total rounds: " . $this->remainingAmmo . "/" . $this->capacity; 
	    foreach($this->ammoArray as $currAmmo){
	    	$this->data["Special"] .= "<br> - " . $currAmmo->displayName . ": ". $this->ammoCountArray[$currAmmo->modeName];
			if($currAmmo->size != 1){ //non-standard ordnance size: inform player
				$this->data["Special"] .= " (size: " . $currAmmo->size . ")";
			}		
	    }
	}
    
	
 	public function stripForJson(){
		$strippedSystem = parent::stripForJson();
		$strippedSystem->data = $this->data;
		//$strippedSystem->data['Special'] = $this->data['Special'];
		$strippedSystem->output = $this->output;
		$strippedSystem->capacity = $this->capacity;           //printed SCS value - public
		$strippedSystem->remainingAmmo = $this->remainingAmmo; //round TOTAL - public, see below
		/*The at-a-glance on-icon number. Sent EXPLICITLY because the base stripForJson does not
		  transmit outputDisplay (it is a blueprint field) and the static blueprint is built from an
		  EMPTY magazine - the rounds come from lobby enhancements - so the blueprint's copy is
		  worthless here. SystemFactory.createSystemFromJson merges Object.assign(blueprint,
		  liveJson), so this one wins. A STRING deliberately: SystemIcon's getText() lets a NUMERIC
		  0 fall through to the generic display (`0 != ''` is false in JS) but returns a string "0",
		  which is what makes an emptied magazine read 0 instead of going blank.*/
		$strippedSystem->outputDisplay = (string)$this->remainingAmmo;

		/*The ordnance TYPES are own-team-only; the round TOTAL is not. An opponent may see how much
		  a magazine still holds - and its capacity, a printed SCS value - but not WHAT it holds:
		  whether those rounds are Interceptors or Antifighter is what decides whether they commit
		  missiles. So ammoCountArray/ammoSizeArray go (the sizes name the ordnance aboard even
		  where a count has reached zero) while remainingAmmo/output/outputDisplay stay.
		  EXEMPT - FIGHTERS: a fighter's missiles ride EXTERNAL HARDPOINTS, so the load is there to
		  be seen and counted (B5W). A flight's magazine keeps reporting its types in full.
		  Both client consumers of the type arrays are own-ship-scoped - the commit-time ammo check
		  (gamedata.js, iterates myShips) through doVerifyAmmoUsage, and getMagazineFireableAmmo
		  through checkOutOfAmmo - so an enemy viewer loses nothing it could act on.
		  data['Special'] is REBUILT rather than dropped: AmmoMagazine is one of the few systems
		  that transmits data[] live, and its prose repeats the same per-type breakdown. The two
		  surviving lines mirror setSystemDataWindow's wording so the public half reads identically
		  - keep them in step if that text changes. PHP arrays are value types, so writing to the
		  stripped copy cannot reach $this->data.*/
		$unit = $this->getUnit();
		$isFighterMagazine = ($unit !== null && !empty($unit->flight));
		if (!$isFighterMagazine && !$this->isDisclosedToCurrentViewer()){
			$strippedSystem->data['Special'] = "Technical system, keeping track of consumable ammo."
				. "<br>Total rounds: " . $this->remainingAmmo . "/" . $this->capacity
				. "<br>Ordnance types are known only to its own fleet.";
			return $strippedSystem;
		}

		$strippedSystem->ammoCountArray = $this->ammoCountArray;
		$strippedSystem->ammoSizeArray = $this->ammoSizeArray;
		return $strippedSystem;
	}
	
    //add new kind of ordnance: ammo to be used (CLASS INSTANCE!), number of rounds to add (number)
	//to be called only AFTER AmmoMagazine itself is fitted to unit!
    public function addAmmoEntry($ammoClass, $ammoCount, $notify = false){		
		$ammoEntryExists = false;
		foreach($this->ammoArray as $existingAmmo){
			if($existingAmmo->modeName ==$ammoClass->modeName) $ammoEntryExists = true;
		}		
		if (!$ammoEntryExists) //for some reason - apparently sometimes entry here does not exist despite ammoCountArray entry existing...
			$this->ammoArray[] = $ammoClass;
			
		$keyExists = array_key_exists($ammoClass->modeName,$this->ammoCountArray);
		if (!$keyExists){
			$this->ammoCountArray[$ammoClass->modeName] = $ammoCount;
		}else{
			$this->ammoCountArray[$ammoClass->modeName] += $ammoCount;			
		}
	    $this->ammoSizeArray[$ammoClass->modeName] = $ammoClass->size;		

        $addedSize = $ammoCount * $ammoClass->size;
        $excess = ($this->remainingAmmo + $addedSize) - $this->capacity;
        
        if ($excess > 0 && !empty($this->ammoArray)) {
            if (!isset($this->startingAmmo)) {
                $this->startingAmmo = $this->ammoCountArray;
            }
			
            // 1. Deduct from Interceptor first if applicable
            $isAntiFighterOrChaff = ($ammoClass->modeName === 'Antifighter' || $ammoClass->modeName === 'Chaff');
            if ($isAntiFighterOrChaff && isset($this->ammoCountArray['Interceptor']) && isset($this->startingAmmo['Interceptor'])) {
                $interceptorStart = $this->startingAmmo['Interceptor'];
                $interceptorCurrent = $this->ammoCountArray['Interceptor'];
                $interceptorMin = floor($interceptorStart / 4); //Deduct from this pool until 25% remain.
                
                $availableDeduct = $interceptorCurrent - $interceptorMin;
                if ($availableDeduct > 0) {
                    $intSize = $this->ammoSizeArray['Interceptor'];
                    $neededDeductCount = ceil($excess / $intSize);
                    $deductCount = min($neededDeductCount, $availableDeduct);
                    
                    $this->ammoCountArray['Interceptor'] -= $deductCount;
                    $this->remainingAmmo -= $deductCount * $intSize;
                    $excess = ($this->remainingAmmo + $addedSize) - $this->capacity;
                }
            }
			
            // 2. Deduct from Basic if there's still excess
            if ($excess > 0 && $this->ammoArray[0]->modeName !== $ammoClass->modeName) {
                $basicMode = $this->ammoArray[0]->modeName;
                $basicSize = $this->ammoSizeArray[$basicMode];
                if ($basicSize > 0) {
                    $deductCount = ceil($excess / $basicSize);
                    $deductCount = min($deductCount, $this->ammoCountArray[$basicMode]);
                    $this->ammoCountArray[$basicMode] -= $deductCount;
                    $this->remainingAmmo -= $deductCount * $basicSize;
                    
                    // Recalculate excess after deduction
                    $excess = ($this->remainingAmmo + $addedSize) - $this->capacity;
                }
            }
			
            // 3. Deduct any remainder from Interceptor (if basic ran out)
            if ($excess > 0 && isset($this->ammoCountArray['Interceptor']) && $ammoClass->modeName !== 'Interceptor') {
                $intCurrent = $this->ammoCountArray['Interceptor'];
                if ($intCurrent > 0) {
                    $intSize = isset($this->ammoSizeArray['Interceptor']) ? $this->ammoSizeArray['Interceptor'] : 1;
                    if ($intSize > 0) {
                        $deductCount = ceil($excess / $intSize);
                        $deductCount = min($deductCount, $intCurrent);
                        
                        $this->ammoCountArray['Interceptor'] -= $deductCount;
                        $this->remainingAmmo -= $deductCount * $intSize;
                        
                        // Recalculate excess after deduction
                        $excess = ($this->remainingAmmo + $addedSize) - $this->capacity;
                    }
                }
            }
        }
        
        if ($excess > 0) {
            $fitCount = floor(($this->capacity - $this->remainingAmmo) / $ammoClass->size);
            if ($fitCount < 0) $fitCount = 0;
            $ammoCount = $fitCount;
            // Also adjust the array count since we had already added the un-capped $ammoCount
            // We need to decrease the array count by the difference
            $diff = ($addedSize / $ammoClass->size) - $ammoCount;
            $this->ammoCountArray[$ammoClass->modeName] -= $diff;
        }

	    $this->remainingAmmo += $ammoCount * $ammoClass->size;
	    $this->remainingAmmo = min($this->remainingAmmo, $this->capacity);
		if(!$keyExists) $this->ammoUsedTotal[$ammoClass->modeName] = 0;
	    if($notify) $this->notifyWeapons(); //weapons need to update their stats - if this is a new entry after creation
    }
	
	private function notifyWeapons() { //notify weapons that something changed and they need to update themselves
		foreach($this->weaponsServed as $weapon){
			$weapon->recompileFiringModes();	
		}
	}
	public function subscribe($weapon){
		$this->weaponsServed[] = $weapon;
	}
	
	public function getAmmoPresence($modeName){
		$toReturn = false;
		foreach($this->ammoArray as $currAmmo){
			if($currAmmo->modeName == $modeName){
				$toReturn = true;
				break; //foreach
			}
		}
		return $toReturn;
	}
	public function getAmmo($modeName){
		$toReturn = null;
		foreach($this->ammoArray as $currAmmo){
			if($currAmmo->modeName == $modeName){
				$toReturn = $currAmmo;
				break; //foreach
			}
		}		
		return $toReturn;
	}

	//Called when Interceptor missile is attempting to intercept, to check missiles are available.
	public function canDrawInterceptor($modeName){
		
	    // Check if ammo count has a value of 1 or more after ammo used this turn deducted
	    	if(($this->remainingAmmo > 0) && (($this->ammoCountArray[$modeName] - $this->interceptorUsed) >= 1)){    	
	        return true; // drawing ammo is possible
	    } else {
	        return false; // cannot draw ammo!
	    }
	}
	
	//Reduce number of Interceptor missile when one is ordered to intercept but can be used for other modes.
	public function doDrawAmmo($gameData, $modeName){
        $ship = $this->getUnit();		
		//PREPARE APPROPRIATE NOTES FOR AMMO USED TO INTERCEPT	
        $notekey = 'AmmoUsed';
        $noteHuman = 'Ammunition Magazine - a round is drawn';
        $noteValue = $modeName;
        $this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$noteValue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue
			
		if  ($noteValue == 'Interceptor'){//doDrawAmmo() maybe used for other direct fire weapons, make this specific?          
			$this->interceptorUsed += 1;//Interceptor just used!
		}    

        $this->ammoAlreadyUsed = array(); 
	}	
		
 public function generateIndividualNotes($gameData, $dbManager){ //dbManager is necessary for Initial phase only
        $ship = $this->getUnit();
        switch($gameData->phase){
			//both Initial and Firing phase will behave the same - save to database data about current usage, received from front end (to enable ammo counting for both ballistic and direct fire weapons)
                case 1: //Initial phase - ballistic weapons
				case 3: //firing DECLARATION phase - direct fire weapons
                    if($ship->userid == $gameData->forPlayer){ //only own ships, otherwise bad things may happen!
						foreach($this->ammoJustUsed as $modeName){
							//AND PREPARE APPROPRIATE NOTES!
							$notekey = 'AmmoUsed';
							$noteHuman = 'Ammunition Magazine - a round is drawn';
							$noteValue = $modeName;
							$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$noteValue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue
						}
						$this->ammoAlreadyUsed = array(); 
                    }
                    break;					
        }
    } //endof function generateIndividualNotes	
 	
 	/*act on notes just loaded - to be redefined by systems as necessary
	 - mark rounds spent (it is possible to load more rounds than magazine capacity, but spending will be limited by it - in effect getting extra flexibility (but not magazine capacity) for very high price
	*/
public function onIndividualNotesLoaded($gamedata) {
    foreach ($this->individualNotes as $currNote) { // Assume ASCENDING sorting - so enact all changes as is
        switch ($currNote->notekey) {
            case 'AmmoReplenished': // Stage 15: a round was restocked while this flight sat docked
                // Mirror of AmmoUsed but reversed. Values may go positive temporarily relative
                // to setEnhancementsFighter's addAmmoEntry which runs AFTER this hook at game
                // load (TacGamedata::onConstructed) — addAmmoEntry then adds the base load on
                // top, giving the correct "starting - used + restocked" final count.
                if (!array_key_exists($currNote->notevalue, $this->ammoCountArray)) {
                    $this->ammoCountArray[$currNote->notevalue] = 0;
                }
                if (!array_key_exists($currNote->notevalue, $this->ammoUsedTotal)) {
                    $this->ammoUsedTotal[$currNote->notevalue] = 0;
                }
                $this->ammoCountArray[$currNote->notevalue] += 1;
                $this->ammoUsedTotal[$currNote->notevalue]  -= 1;
                break;

            case 'AmmoUsed': // Mode name for ammunition type that was expended
                // Entry may not exist yet! Due to when enhancements and notes are loaded - in this case, initialize them - values will get negative for a moment, but it's not a problem
                if (!array_key_exists($currNote->notevalue, $this->ammoCountArray)) {
                    $this->ammoCountArray[$currNote->notevalue] = 0;
                }
                if (!array_key_exists($currNote->notevalue, $this->ammoUsedTotal)) {
                    $this->ammoUsedTotal[$currNote->notevalue] = 0;
                }


                    $this->ammoCountArray[$currNote->notevalue] -= 1;
                    $this->ammoUsedTotal[$currNote->notevalue] += 1;


                /*
                $ammoSize = $this->ammoSizeArray[$currNote->notevalue];
                $this->remainingAmmo -= $ammoSize;
                */
                break;
        }
    }
	     
} // End of function onIndividualNotesLoaded
	
	
    public function doIndividualNotesTransfer(){
        //data received in variable individualNotesTransfer, further functions will look for it in powerReceivedFromFrontEnd
        //in this case - one entry for every ammo round used (name of firing mode) - to be marked for actual note creation later!
        if(is_array($this->individualNotesTransfer)) foreach($this->individualNotesTransfer as $modeName)  $this->ammoJustUsed[] = $modeName;
        $this->individualNotesTransfer = array(); //empty, just in case
    }

	/* Stage 15: rearm at most one missile per turn while this fighter sits docked.
	 * Driven by HangarOps::serviceDockedFlights, same per-turn tick that drives
	 * Weapon::whileDocked for matter-weapon ammo. Eligible missile types are
	 * derived from the docked FLIGHT's AMMO_F* enhancements (purchased load); the
	 * starting load per fighter equals the enhancement count, so a missile is
	 * "missing" iff ammoCountArray[mode] < enhancementCount. Most-expensive
	 * missing type goes first; the carrier's reload pool (HANG_ORD) pays the
	 * missile's enhancementPrice via HangarOps::drawReload. If the most expensive
	 * candidate can't be afforded, fall through to the next.
	 *
	 * Persistence: writes an AmmoReplenished IndividualNote attached to THIS
	 * magazine. The hangarOrdReserve note on the carrier's primary hangar is
	 * persisted by the standard Hangar::generateIndividualNotes change-detection.
	 * The launch-replay timeline shows neither (the rearm is silent on the carrier
	 * side); the next turn-load reconstructs the magazine's state from the notes. */
	public function whileDocked($flight, $carrier, $hangar, $gamedata){
		if (!($flight instanceof FighterFlight)) return;
		if (!isset($flight->enhancementOptions) || !is_array($flight->enhancementOptions)) return;
		if ($this->isDestroyed($gamedata->turn)) return;
		//Walk the flight's AMMO_F* enhancements and build a price-sorted list of
		//restock candidates. Each AMMO_F* enhancement maps 1:1 to an AmmoMissileF*
		//class via its modeName + enhancementPrice — same set setEnhancementsFighter
		//instantiates when applying the initial load.
		static $ammoMap = array(
			'AMMO_FB'  => 'AmmoMissileFB',
			'AMMO_FL'  => 'AmmoMissileFL',
			'AMMO_FH'  => 'AmmoMissileFH',
			'AMMO_FY'  => 'AmmoMissileFY',
			'AMMO_FD'  => 'AmmoMissileFD',
			'AMMO_DUM' => 'AmmoMissileFDum',
		);
		$candidates = array();
		foreach ($flight->enhancementOptions as $opt){
			$enhID    = (string)($opt[0] ?? '');
			$enhCount = (int)($opt[2] ?? 0);
			if ($enhCount <= 0)                continue;
			if (!isset($ammoMap[$enhID]))      continue;
			$className = $ammoMap[$enhID];
			if (!class_exists($className))     continue;
			$ammoClass = new $className();
			$modeName  = $ammoClass->modeName;
			//Only restock types this magazine actually carries.
			if (!array_key_exists($modeName, $this->ammoCountArray)) continue;
			//"Missing" relative to the starting per-fighter load (enhCount).
			$current = (int)$this->ammoCountArray[$modeName];
			if ($current >= $enhCount) continue;
			$candidates[] = array(
				'modeName' => $modeName,
				'price'    => (int)$ammoClass->getPrice($flight),
			);
		}
		if (empty($candidates)) return;
		//Most expensive first.
		usort($candidates, function($a, $b){ return $b['price'] - $a['price']; });
		//Try each in order until one fits the remaining pool. Cheap-type
		//fallback lets a partial reload happen even when the priciest type
		//is out of reach — better than failing silently.
		foreach ($candidates as $cand){
			if (!HangarOps::drawReload($carrier, $cand['price'])) continue;
			$this->ammoCountArray[$cand['modeName']] += 1;
			if (!array_key_exists($cand['modeName'], $this->ammoUsedTotal)) $this->ammoUsedTotal[$cand['modeName']] = 0;
			$this->ammoUsedTotal[$cand['modeName']] -= 1;
			//Persist the restock. Note attaches to flight + magazine ids so
			//reload reconstructs it via onIndividualNotesLoaded above. Use
			//$flight->id explicitly (not $this->getUnit()) — the magazine is a
			//subsystem of a Fighter which is itself a subsystem of the flight,
			//and getUnit()'s exact resolution path varies; the caller already
			//hands us the authoritative flight.
			$note = new IndividualNote(
				-1,
				$gamedata->id,
				$gamedata->turn,
				$gamedata->phase,
				$flight->id,
				$this->id,
				'AmmoReplenished',
				'Ammunition Magazine - a round restocked',
				$cand['modeName']
			);
			Manager::insertIndividualNote($note);
			return;   //1 missile per fighter per turn
		}
	}

} //endof AmmoMagazine



//ammunition for AmmoMagazine - template; using template assures that all variables are filled even if a particular missile does not need them
class AmmoMissileTemplate{	
	public $name = 'AmmoMissileTemplate';
	public $displayName = 'SOMEONE DID NOT OVERLOAD TEMPLATE FULLY!'; //should never be shown ;)
	public $modeName = 'Template';
	public $size = 1; //how many store slots are required for a single round
	public $enhancementName = 'AMMO_TTTT'; //enhancement name to be enabled
	public $enhancementDescription = '(ammo) TEMPLATE'; //enhancement description
	public $enhancementPrice = 1;//price per missile
	
	public $rangeMod = 0; //MODIFIER for launch range
	public $distanceRangeMod = 0; //MODIFIER for distance range
	public $fireControlMod = array(3, 3, 3); //MODIFIER for weapon fire control!
	public $minDamage = 0;
	public $maxDamage = 0;	
	public $damageType = 'Standard';//mode of dealing damage
	public $weaponClass = 'Ballistic';//weapon class
	public $priority = 1;
	public $priorityAF = 1;
	public $noOverkill = false;
	public $useOEW = false;
	//Variable for Stealth Missile		
	public $hidetarget = false;
    //Adding Pulse variables for Starburst missiles	
	public $maxpulses = 0;
	public $rof = 0;
	public $useDie = 0; //die used for base number of hits
	public $fixedBonusPulses = 0;//for weapons doing dX+Y pulse	
	//Variables for Multiwarhead Missile.  Normal called shot modifier is -8.	
    public $calledShotMod = -8; 
	//Variables for Interceptor Missile.
	public $intercept = 0;
	public $ballisticIntercept = false;       
	//Variables for Jammer Missile.    
    public $hextarget = false; 
    public $animation = "trail";
    public $animationExplosionScale = 0; //0 means it will be set automatically by standard constructor, based on average damage yield
	public $uninterceptable = false; 
	public $doNotIntercept = false;            
	//Variables for KK Missile
	public $specialRangeCalculation = false;
	public $rangePenalty = 0;
	public $noLockPenalty = false;		
	//Variable for HARM Missile	
	public $specialHitChanceCalculation = false;		
	//Variable for Ballistic Mines
	public $mineRange = 0;
	
    function __construct(){}
	
    public function getDamage($fireOrder) //actual function to be called, as with weapon!
    {
        return 0;
    }		
	
	function getPrice($unit) //some missiles might have different price depending on unit being fitted!
	{
		return $this->enhancementPrice;
	}
	
	/*missiles with special effects affecting system hit will redefine this*/
    public function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder)
    {
        return;
    }//endof function onDamagedSystem
    
    //Adding Pulse functions for Starburst missiles
    protected function getPulses($turn)
        {
            return 0;
        }//endof function getPulses
	
    protected function getExtraPulses($needed, $rolled)
        {
            return 0;
        }//endof function getExtraPulses
	
	public function rollPulses($turn, $needed, $rolled)
		{
		return 0;
		}//endof function rollPulses
	
	public function beforeFiringOrderResolution($gamedata, $weapon, $originalFireOrder) //For mulitwarhead missile
    {
    	return;
    }//endof function beforeFiringOrderResolution	
    
    public function getCalledShotMod() //For mulitwarhead missile
    {
        return $this->calledShotMod;
    }//end of getCalledShotMod     				

	public function fire($gamedata, $fireOrder) //For mulitwarhead missile
    {
    	return;
    }//end of function fire	
    
    public function calculateRangePenalty($distance)
    {
        return 0;
    }  //endof function calculateRangePenalty	 

	public function calculateHitBase($gamedata, $fireOrder)
	{
		return;
	}
		    
} //endof class AmmoMissileTemplate


//ammunition for AmmoMagazine - Class B Missile (for official Missile Racks)
class AmmoMissileB extends AmmoMissileTemplate{	
	public $name = 'ammoMissileB';
	public $displayName = 'Basic Missile';
	public $modeName = 'Basic';
	public $size = 1; //how many store slots are required for a single round
	public $enhancementName = 'AMMO_B'; //enhancement name to be enabled
	public $enhancementDescription = '(AMMO) Basic Missile'; //enhancement description
	public $enhancementPrice = 1;//officially 0, but if it was 0 then there would be no reason not to load it
	
	public $rangeMod = 0; //MODIFIER for launch range
	public $distanceRangeMod = 0; //MODIFIER for distance range
	public $fireControlMod = array(3, 3, 3); //MODIFIER for weapon fire control!
	public $minDamage = 20;
	public $maxDamage = 20;	
	public $damageType = 'Standard';//mode of dealing damage
	public $weaponClass = 'Ballistic';//weapon class
	public $priority = 6;
	public $priorityAF = 5;
	public $noOverkill = false;
	public $useOEW = false;
	public $hidetarget = false;
	
    function __construct(){}
	
    public function getDamage($fireOrder) //actual function to be called, as with weapon!
    {
        return 20;
    }		
	
} //endof class AmmoMissileB


//ammunition for AmmoMagazine - Class L Missile (for official Missile Racks)
class AmmoMissileL extends AmmoMissileTemplate{	
	public $name = 'ammoMissileL';
	public $displayName = 'Long Range Missile';
	public $modeName = 'LongRange';
	public $size = 1; //how many store slots are required for a single round
	public $enhancementName = 'AMMO_L'; //enhancement name to be enabled
	public $enhancementDescription = '(AMMO) Long Range Missile'; //enhancement description
	public $enhancementPrice = 6;
	
	public $rangeMod = 10; //MODIFIER for launch range
	public $distanceRangeMod = 30; //MODIFIER for distance range
	public $fireControlMod = array(3, 3, 3); //MODIFIER for weapon fire control!
	public $minDamage = 15;
	public $maxDamage = 15;	
	public $damageType = 'Standard';//mode of dealing damage
	public $weaponClass = 'Ballistic';//weapon class
	public $priority = 6;
	public $priorityAF = 6;
	public $noOverkill = false;
    public $useOEW = false;
	public $hidetarget = false;

	
    public function getDamage($fireOrder) //actual function to be called, as with weapon!
    {
        return 15;
    }
	
} //endof class AmmoMissileL


//ammunition for AmmoMagazine - Class H Missile (for official Missile Racks)
class AmmoMissileH extends AmmoMissileTemplate{	
	public $name = 'ammoMissileH';
	public $displayName = 'Heavy Missile';
	public $modeName = 'Heavy';
	public $size = 1; //how many store slots are required for a single round
	public $enhancementName = 'AMMO_H'; //enhancement name to be enabled
	public $enhancementDescription = '(AMMO) Heavy Missile'; //enhancement description
	public $enhancementPrice = 4;
	
	public $rangeMod = -10; //MODIFIER for launch range
	public $distanceRangeMod = -30; //MODIFIER for distance range
	public $fireControlMod = array(0, 3, 3); //MODIFIER for weapon fire control!
	public $minDamage = 30;
	public $maxDamage = 30;	
	public $damageType = 'Standard';//mode of dealing damage
	public $weaponClass = 'Ballistic';//weapon class
	public $priority = 6;
	public $priorityAF = 5;
	public $noOverkill = false;
    public $useOEW = false;
	public $hidetarget = false;
		
    public function getDamage($fireOrder) //actual function to be called, as with weapon!
    {
        return 30;
    }		
	
} //endof class AmmoMissileH


//ammunition for AmmoMagazine - Class F Missile (for official Missile Racks)
class AmmoMissileF extends AmmoMissileTemplate{	
	public $name = 'ammoMissileF';
	public $displayName = 'Flash Missile';
	public $modeName = 'Flash';
	public $size = 1; //how many store slots are required for a single round
	public $enhancementName = 'AMMO_F'; //enhancement name to be enabled
	public $enhancementDescription = '(AMMO) Flash Missile'; //enhancement description
	public $enhancementPrice = 6;
	
	public $rangeMod = 0; //MODIFIER for launch range
	public $distanceRangeMod = 0; //MODIFIER for distance range
	public $fireControlMod = array(3, 3, 3); //MODIFIER for weapon fire control!
	public $minDamage = 20;
	public $maxDamage = 20;	
	public $damageType = 'Flash';//mode of dealing damage
	public $weaponClass = 'Ballistic';//weapon class
	public $priority = 6;
	public $priorityAF = 5;
	public $noOverkill = false;
    public $useOEW = false;
	public $hidetarget = false;
		
    public function getDamage($fireOrder) //actual function to be called, as with weapon!
    {
        return 20;
    }		
	
} //endof class AmmoMissileF



//ammunition for AmmoMagazine - Class A Missile (for official Missile Racks)
class AmmoMissileA extends AmmoMissileTemplate{	
	public $name = 'ammoMissileA';
	public $displayName = 'Antifighter Missile';
	public $modeName = 'Antifighter';
	public $size = 1; //how many store slots are required for a single round
	public $enhancementName = 'AMMO_A'; //enhancement name to be enabled
	public $enhancementDescription = '(AMMO) Antifighter Missile'; //enhancement description
	public $enhancementPrice = 4;
	
	public $rangeMod = -5; //MODIFIER for launch range
	public $distanceRangeMod = -15; //MODIFIER for distance range
	public $fireControlMod = array(6, 3, 3); //MODIFIER for weapon fire control!
	public $minDamage = 15;
	public $maxDamage = 15;	
	public $damageType = 'Standard';//mode of dealing damage
	public $weaponClass = 'Ballistic';//weapon class
	public $priority = 6;
	public $priorityAF = 5;
	public $noOverkill = false;
    public $useOEW = false;
	public $hidetarget = false;
		
    public function getDamage($fireOrder) //actual function to be called, as with weapon!
    {
        return 15;
    }		
	
} //endof class AmmoMissileA


//ammunition for AmmoMagazine - Class P Missile (for official Missile Racks)
class AmmoMissileP extends AmmoMissileTemplate{	
	public $name = 'ammoMissileP';
	public $displayName = 'Piercing Missile';
	public $modeName = 'Piercing';
	public $size = 1; //how many store slots are required for a single round
	public $enhancementName = 'AMMO_P'; //enhancement name to be enabled
	public $enhancementDescription = '(AMMO) Piercing Missile'; //enhancement description
	public $enhancementPrice = 16;
	
	public $rangeMod = 0; //MODIFIER for launch range
	public $distanceRangeMod = 0; //MODIFIER for distance range
	public $fireControlMod = array(null, 3, 3); //MODIFIER for weapon fire control!
	public $minDamage = 30;
	public $maxDamage = 30;	
	public $damageType = 'Piercing';//mode of dealing damage
	public $weaponClass = 'Ballistic';//weapon class
	public $priority = 2;
	public $priorityAF = 2;//moot, as this missile cannot be fired at fighters
	public $noOverkill = true;
    public $useOEW = false;
	public $hidetarget = false;
		
    public function getDamage($fireOrder) //actual function to be called, as with weapon!
    {
        return 30;
    }		
	
} //endof class AmmoMissileP



//ammunition for AmmoMagazine - Class D Missile (for official Missile Racks)
class AmmoMissileD extends AmmoMissileTemplate{	
	public $name = 'ammoMissileD';
	public $displayName = 'Light Missile';
	public $modeName = 'D-Light';
	public $size = 1; //how many store slots are required for a single round
	public $enhancementName = 'AMMO_D'; //enhancement name to be enabled
	public $enhancementDescription = '(AMMO) Light Missile'; //enhancement description
	public $enhancementPrice = 1; //nominally 0 - included in ship price
	
	public $rangeMod = -5; //MODIFIER for launch range
	public $distanceRangeMod = -15; //MODIFIER for distance range
	public $fireControlMod = array(3, 3, 3); //MODIFIER for weapon fire control!
	public $minDamage = 12;
	public $maxDamage = 12;	
	public $damageType = 'Standard';//mode of dealing damage
	public $weaponClass = 'Ballistic';//weapon class
	public $priority = 6;
	public $priorityAF = 5;
	public $noOverkill = false;
	public $useOEW = false;
	public $hidetarget = false;
		
    public function getDamage($fireOrder) //actual function to be called, as with weapon!
    {
        return 12;
    }	
} //endof class AmmoMissileD

//ammunition for AmmoMagazine - Class C Missile (for official Missile Racks)
class AmmoMissileC extends AmmoMissileTemplate{	
	public $name = 'ammoMissileC';
	public $displayName = 'Chaff Missile';
	public $modeName = 'Chaff';
	public $size = 1; //how many store slots are required for a single round
	public $enhancementName = 'AMMO_C'; //enhancement name to be enabled
	public $enhancementDescription = '(AMMO) Chaff Missile'; //enhancement description
	public $enhancementPrice = 4;
	
	public $rangeMod = 0; //MODIFIER for launch range
	public $distanceRangeMod = 0; //MODIFIER for distance range
	public $fireControlMod = array(3, 3, 3); //MODIFIER for weapon fire control!
	public $minDamage = 0;
	public $maxDamage = 0;	
	public $damageType = 'Standard';//mode of dealing damage
	public $weaponClass = 'Ballistic';//weapon class
	public $priority = 1;
	public $priorityAF = 1;
	public $noOverkill = false;
	public $useOEW = false;
	public $hidetarget = false;
	private static $alreadyEngaged = array();	
	
    function __construct(){}
	
    public function getDamage($fireOrder) //actual function to be called, as with weapon!
    {
        return 0;
    }		
    
 	public function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){
//		if($ship->hasSpecialAbility("AdvancedSensors")) return;		 
		if (isset(AmmoMissileC::$alreadyEngaged[$ship->id])) return; //target already engaged by a previous Chaff Missile
			$effectHit = 3; 
			$effectHit5 = $effectHit * 5;
			$fireOrder->pubnotes .= "<br> All non-ballistic weapon fire by target reduced by $effectHit5 percent.";

			$allFire = $ship->getAllFireOrders($gamedata->turn);
			foreach($allFire as $currFireOrder) {
				if ($currFireOrder->type == 'normal') {
					if ($currFireOrder->rolled > 0) {
					}else{
						$currFireOrder->needed -= 3 *5; //$needed works on d100
						AmmoMissileC::$alreadyEngaged[$ship->id] = true;
					}
				}
			}

			if ($ship instanceof FighterFlight){  //place effect on first fighter, even if it's already destroyed!
				$firstFighter = $ship->getSampleFighter();
				AmmoMissileC::$alreadyEngaged[$ship->id] = true;//mark engaged        
				if($firstFighter){
					for($i=1; $i<=$effectHit;$i++){
						$crit = new tmphitreduction(-1, $ship->id, $firstFighter->id, 'tmphitreduction', $gamedata->turn, $gamedata->turn); 
						$crit->updated = true;
							$firstFighter->criticals[] =  $crit;
					}
				}
			}else{ //ship - place effcet on C&C!   */
				$CnC = $ship->getSystemByName("CnC");
				AmmoMissileC::$alreadyEngaged[$ship->id] = true;//mark engaged        
				if($CnC){
					for($i=0; $i<=$effectHit;$i++){
						$crit = new tmphitreduction(-1, $ship->id, $CnC->id, 'tmphitreduction', $gamedata->turn, $gamedata->turn); 
						$crit->updated = true;
							$CnC->criticals[] =  $crit;
					}
				}
		}
	} //endof function onDamagedSystem
	

} //endof class AmmoMissileC


//ammunition for AmmoMagazine - Class S Missile (for official Missile Racks, Kor-Lyan only)
class AmmoMissileS extends AmmoMissileTemplate{	
	public $name = 'ammoMissileS';
	public $displayName = 'Stealth Missile';
	public $modeName = 'Stealth';
	public $size = 1; //how many store slots are required for a single round
	public $enhancementName = 'AMMO_S'; //enhancement name to be enabled
	public $enhancementDescription = '(AMMO) Stealth Missile'; //enhancement description
	public $enhancementPrice = 5;
	
	public $rangeMod = 0; //MODIFIER for launch range
	public $distanceRangeMod = 0; //MODIFIER for distance range
	public $fireControlMod = array(3, 3, 3); //MODIFIER for weapon fire control!
	public $minDamage = 20;
	public $maxDamage = 20;	
	public $damageType = 'Standard';//mode of dealing damage
	public $weaponClass = 'Ballistic';//weapon class
	public $priority = 6;
	public $priorityAF = 5;
	public $noOverkill = false;
	public $useOEW = false;
	public $hidetarget = true;
 
    public function getDamage($fireOrder) //actual function to be called, as with weapon!
    {
        return 20;
    }	
} //endof class AmmoMissileS


//ammunition for AmmoMagazine - Class I Missile (for official Missile Racks)
class AmmoMissileI extends AmmoMissileTemplate{	
	public $name = 'ammoMissileI';
	public $displayName = 'Interceptor Missile';
	public $modeName = 'Interceptor';
	public $size = 1; //how many store slots are required for a single round
	public $enhancementName = 'AMMO_I'; //enhancement name to be enabled
	public $enhancementDescription = '(AMMO) Interceptor Missile'; //enhancement description
	public $enhancementPrice = 2; //PV per missile; originally it's 0 for Kor-Lyan and 2 for everyone else
	
	public $fireControlMod = array(null, null, null); //MODIFIER for weapon fire control!
	public $minDamage = 0;
	public $maxDamage = 0;	
	public $damageType = 'Standard';//mode of dealing damage
	public $weaponClass = 'Ballistic';//weapon class
	public $priority = 5;
	public $priorityAF = 5;
	public $noOverkill = false;
	public $hidetarget = false;
	public $intercept = 6;
	public $ballisticIntercept = true; 

		
    public function getDamage($fireOrder) //actual function to be called, as with weapon!
    {
        return 0;
    }		
    
    function getPrice($unit) //some missiles might have different price depending on unit being fitted!
    {
        //if($unit->faction == 'Kor-Lyan') return 0;		
		if (stristr($unit->faction,'Kor-Lyan')) return 0;
        return $this->enhancementPrice;
    }	
} //endof class AmmoMissileI

//ammunition for AmmoMagazine - Class J Missile (for official Missile Racks)
class AmmoMissileJ extends AmmoMissileTemplate{	
	public $name = 'ammoMissileJ';
	public $displayName = 'Jammer Missile';
	public $modeName = 'Jammer';
	public $size = 1; //how many store slots are required for a single round
	public $enhancementName = 'AMMO_J'; //enhancement name to be enabled
	public $enhancementDescription = '(AMMO) Jammer Missile';
	public $enhancementPrice = 8; //PV per missile;
	
	public $rangeMod = -5; //MODIFIER for launch range
	public $distanceRangeMod = -5; //MODIFIER for distance range
	public $fireControlMod = array(null, null, null); //MODIFIER for weapon fire control! Hex targetted!
	public $minDamage = 0;
	public $maxDamage = 0;	
	public $damageType = 'Standard';//mode of dealing damage
	public $weaponClass = 'Ballistic';//weapon class
	public $priority = 1;
	public $priorityAF = 1;
	public $noOverkill = false;
    public $useOEW = false;
	public $hidetarget = true;
    
    public $hextarget = true;
    public $animation = "ball";
    public $animationExplosionScale = 5;   

	public $uninterceptable = true; 
	public $doNotIntercept = true;
	public $noLockPenalty = false;	               	
    
	public static $alreadyJammed = array();     	

    public function getDamage($fireOrder) //actual function to be called, as with weapon!
    {
        return 0;
    }	

		public function beforeFiringOrderResolution($gamedata, $weapon, $originalFireOrder)
		{
        			// Shouldn't happen with null FC, but just in case.
						if ($originalFireOrder->targetid != -1) {// Sometimes player might target ship after all...
	                        $targetship = $gamedata->getShipById($originalFireOrder->targetid);
	                        $movement = $targetship->getLastTurnMovement($originalFireOrder->turn);
	                        $originalFireOrder->x = $movement->position->q;
	                        $originalFireOrder->y = $movement->position->r;
	                        $originalFireOrder->targetid = -1; // Correct the error
	                    }	

	                $target = new OffsetCoordinate($originalFireOrder->x, $originalFireOrder->y);//Traget hex from Fire Order.
					$affectedUnits = $gamedata->getShipsInDistance($target, 5);	//Find all ships within 5 hexes.
				
					foreach ($affectedUnits as $targetShip) { //Apply Jammer marker to those ships.
						if (isset(AmmoMissileJ::$alreadyJammed[$targetShip->id])) return; //But not if jammed already.									                    		
						AmmoMissileJ::$alreadyJammed[$targetShip->id] = true;//mark jammed already.
					}
	}//endof function beforeFiringOrderResolution 
	
	
	public function calculateHitBase($gamedata, $fireOrder)
		{
			$fireOrder->needed = 100; //always true
			$fireOrder->updated = true;			
		}              

    public function fire($gamedata, $fireOrder)
    {
		    $shooter = $gamedata->getShipById($fireOrder->shooterid);        
	        $rolled = Dice::d(100);
	        $fireOrder->rolled = $rolled; 
			$fireOrder->pubnotes .= " All ships within 5 hexes receive two points of Blanket DEW.";	
			if($rolled <= $fireOrder->needed){//HIT!
				$fireOrder->shotshit++;		
			}else{ //MISS!  Should never happen.
				$fireOrder->pubnotes .= " MISSED! ";
			}
	}
	
} //endof class AmmoMissileJ


//ammunition for AmmoMagazine - Class K Missile (for official Missile Racks)
class AmmoMissileK extends AmmoMissileTemplate{	
	public $name = 'ammoMissileK';
	public $displayName = 'Starburst Missile';
	public $modeName = 'K - Starburst';
	public $size = 2; //how many store slots are required for a single round
	public $enhancementName = 'AMMO_K'; //enhancement name to be enabled
	public $enhancementDescription = '(AMMO) Starburst Missile'; //2260 for Kor-Lyan, 2264 for everyone else 
	public $enhancementPrice = 30; //PV per missile; originally it's 20 for Kor-Lyan and 30 for everyone else
	
	public $rangeMod = -5; //MODIFIER for launch range
	public $distanceRangeMod = 0; //MODIFIER for distance range
	public $fireControlMod = array(3, 3, 3); //MODIFIER for weapon fire control!
	public $minDamage = 10;
	public $maxDamage = 10;	
	public $damageType = 'Pulse';//mode of dealing damage
	public $weaponClass = 'Ballistic';//weapon class
	public $priority = 4;
	public $priorityAF = 4;
	public $noOverkill = false;
	public $useOEW = false;
	public $hidetarget = false;
	public $maxpulses = 6;
	public $rof = 2;
	public $useDie = 3; //die used for base number of hits
	public $fixedBonusPulses=3;//for weapons doing dX+Y pulse

        protected function getPulses($turn)
        {
            return Dice::d($this->useDie) + $this->fixedBonusPulses;
        }
	
        protected function getExtraPulses($needed, $rolled)
        {
            return 0;
        }
	
	public function rollPulses($turn, $needed, $rolled){
		$pulses = $this->getPulses($turn);
		$pulses=min($pulses,$this->maxpulses);
		return $pulses;
	}
	
    public function getDamage($fireOrder) //actual function to be called, as with weapon!
    {
        return 10;
    }		

	function getPrice($unit) //some missiles might have different price depending on unit being fitted!
	{
		//if($unit->faction == 'Kor-Lyan') return 20;
		if (stristr($unit->faction,'Kor-Lyan')) return 20;
		return $this->enhancementPrice;
	}
	
} //endof class AmmoMissileK

//ammunition for AmmoMagazine - Class MK Missile (for official Missile Racks)
class AmmoMissileM extends AmmoMissileTemplate{	
	public $name = 'ammoMissileM';
	public $displayName = 'Multiwarhead Missile';
	public $modeName = 'Multiwarhead';
	public $size = 2; //how many store slots are required for a single round
	public $enhancementName = 'AMMO_M'; //enhancement name to be enabled
	public $enhancementDescription = '(AMMO) Multiwarhead Missile';
	public $enhancementPrice = 24; //PV per missile;
	
	public $rangeMod = 0; //MODIFIER for launch range
	public $distanceRangeMod = 0; //MODIFIER for distance range
	public $fireControlMod = array(3, null, null); //MODIFIER for weapon fire control! Should be 3
	public $minDamage = 10;
	public $maxDamage = 10;	
	public $damageType = 'Standard';//mode of dealing damage
	public $weaponClass = 'Ballistic';//weapon class
	public $priority = 5;
	public $priorityAF = 5;
	public $noOverkill = false;
    public $useOEW = false;
	public $hidetarget = false;
    public $calledShotMod = 0;   	
	
    public $ballistic = true;
    public $hextarget = false;    
    
	protected $engagedFighters = array();  //Required to avoid mulitple M-Missiles creating fire orders for destroyed fighters and therefore reverting to a normal shot. 	

    public function getDamage($fireOrder) //actual function to be called, as with weapon!
    {
        return 10;
    }	


	public function beforeFiringOrderResolution($gamedata, $weapon, $originalFireOrder){
		    // To create 6 missiles instead of just one.
		    $missilesTotal = 6;
		    $currentShotNumber = 1;

		    // Fetch target and shooter IDs from the current fire order
		    $target = $gamedata->getShipById($originalFireOrder->targetid);
		    if ($target instanceof FighterFlight) { // one attack against every fighter!

		        $fighterList = array(); // Corrected the "new" keyword

		        foreach ($target->systems as $fighter) { // Can only target fighters.
		            if ($fighter->isDestroyed()) { // Ignore destroyed fighters.
		                continue;
		            }
		            array_unshift($fighterList, $fighter); // array_unshift adds element at the beginning of array rather than at the end
		        }

		        foreach ($fighterList as $fighter) {
		            if ($currentShotNumber == 1) {
		                $originalFireOrder->calledid = $fighter->id;
		            } else {	
							$newFireOrder = new FireOrder(
                                    -1, "ballistic", $originalFireOrder->shooterid, $originalFireOrder->targetid,
                                    $weapon->id, $fighter->id, $gamedata->turn, $originalFireOrder->firingMode, 
                                    0, 0, 1, 0, 0, //needed, rolled, shots, shotshit, intercepted
                                    0,0,$weapon->weaponClass,-1 //X, Y, damageclass, resolutionorder
                                );
                            $newFireOrder->addToDB = true;
                            $weapon->fireOrders[] = $newFireOrder;
                         
					}
						
					$currentShotNumber++;
					if($currentShotNumber > $missilesTotal) break; //will get out of foreach loop once we're out of submissiles, even if there are still fighters unassigned	
					
				}
			}
	}//endof function beforeFiringOrderResolution   

     public function fire($gamedata, $fireOrder) 	//For Multiwarhead missiles
    {

	$validTarget = true; //unless we find a reason it's not
	$target = $gamedata->getShipById($fireOrder->targetid);
	$fighter = $target->getSystemById($fireOrder->calledid);
	
	if($fighter->isDestroyed()) $validTarget = false; //shot called at destroyed fighter would have been reassigned
	if(in_array($fireOrder->calledid,  $this->engagedFighters)) $validTarget = false; //if it was already engaged by this weapon, it cannot be engaged again
		
	if (!$validTarget) {//target is not valid, find another one
		 $targetFighter = null;
		foreach ($target->systems as $fighter) { // Can only target fighters.
		
		     if ($fighter->isDestroyed()) { // Ignore destroyed fighters.
		         continue;
		     }
		                    	                    
			if(in_array($fighter->id,  $this->engagedFighters)) continue; //ignore already engaged fighters
			 	
			$targetFighter = $fighter; //found appropriate target! 
			
	//		break; //Removed so that retargetted munitions strike last fighter in flight.
		}                
			                
			if($targetFighter!=null){
				$fireOrder->calledid = $targetFighter->id; //this redirection will be correctly handled by standard routines
				$validTarget = true;
			}
	}	

		if (!$validTarget) { //target not valid and no replacement found - make the shot miss!
			$fireOrder->needed = 0; //set hit chance as 0
			$fireOrder->pubnotes .= '<br>No viable target - an excess submunition is lost';//inform player of situation
		}else{ //valid target, will be engaged, note for further shots!
				$this->engagedFighters[]= $fireOrder->calledid;
		}	
	}//end of function fire
            
	
} //endof class AmmoMissileM


//ammunition for AmmoMagazine - Class KK Missile (for official Missile Racks). Used by Orieni only
class AmmoMissileKK extends AmmoMissileTemplate{	
	public $name = 'ammoMissileKK';
	public $displayName = 'Kinetic Missile';
	public $modeName = 'Kinetic'; //Technically means both Starburst and Kinetic will show as 'K' in mode selection, but Orieni don't have access to Starburst missiles.
	public $size = 1; //how many store slots are required for a single round
	public $enhancementName = 'AMMO_KK'; //enhancement name to be enabled
	public $enhancementDescription = '(AMMO) Kinetic Missile'; //enhancement description
	public $enhancementPrice = 8; //PV per missile;
	
	public $rangeMod = 40; //MODIFIER for launch range.  In theory it can travel up to 60 hexes as it's max distance (but the hit chances would be terrible!)
	public $distanceRangeMod = 0; //MODIFIER for distance range
	public $fireControlMod = array(3, 3, 3); //MODIFIER for weapon fire control!
	public $minDamage = 18;
	public $maxDamage = 18;	
	public $damageType = 'Standard';//mode of dealing damage
	public $weaponClass = 'Matter';//weapon class
	public $priority = 6;
	public $priorityAF = 5;
	public $noOverkill = true;
	public $useOEW = false;
	public $hidetarget = false;

	public $specialRangeCalculation = true;
	public $rangePenalty = 1;	//but only after 15 hexes
	public $noLockPenalty = false;		
	
    public function getDamage($fireOrder) //actual function to be called, as with weapon!
    {
        return 18;
    }		

		public function calculateRangePenalty($distance){
			$rangePenalty = 0;//base penalty
			$rangePenalty += $this->rangePenalty * max(0,$distance-15); //everything above 15 hexes receives range penalty
			return $rangePenalty;
		}

} //endof class AmmoMissileKK

//ammunition for AmmoMagazine - Class X Missile (for official Missile Racks).
class AmmoMissileX extends AmmoMissileTemplate{	
	public $name = 'AmmoMissileX';
	public $displayName = 'HARM Missile';
	public $modeName = 'X - HARM'; //Use X in modeName so that it's not confused with Heavy.
	public $size = 1; //how many store slots are required for a single round
	public $enhancementName = 'AMMO_X'; //enhancement name to be enabled
	public $enhancementDescription = '(AMMO) HARM Missile'; //enhancement description
	public $enhancementPrice = 10; //PV per missile;
	
	public $rangeMod = 0; //MODIFIER for launch range.  In theory it can travel up to 60 hexes as it's max distance (but the hit chances would be terrible!)
	public $distanceRangeMod = 0; //MODIFIER for distance range
	public $fireControlMod = array(null, 3, 3); //MODIFIER for weapon fire control!
	public $minDamage = 0;
	public $maxDamage = 0;	
	public $damageType = 'Standard';//mode of dealing damage
	public $weaponClass = 'Ballistic';//weapon class
	public $priority = 6;
	public $priorityAF = 5;
	public $noOverkill = false;
	public $useOEW = false;
	public $hidetarget = false;
    public $hextarget = false;  	

	public $specialHitChanceCalculation = true;
	
    public function getDamage($fireOrder) //actual function to be called, as with weapon!
    {
        return 0;
    }		


	public function calculateHitBase($gamedata, $fireOrder)
	{	
		
		parent::calculateHitBase($gamedata, $fireOrder);
		
	    $target = $gamedata->getShipById($fireOrder->targetid); 
	    $targetEW = $target->getAllOffensiveEW($gamedata->turn);
	    $hitChanceBonus = $targetEW * 5;
	    
		$fireOrder->needed +=  $hitChanceBonus;	    
	}// end of function calculateHitBase  


 	public function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ //Reduces Sensors by 1D6 next turn.
			if ($system->advancedArmor){
				$fireOrder->pubnotes .= "<br> No effect on ships with Advanced Armor.";				
				return; //no effect on Advanced Armor
			}
/*			if ($system->hardAdvancedArmor){  // GTS Hardened Advanced Armor
				$fireOrder->pubnotes .= "<br> No effect on ships with Hardened Advanced Armor.";				
				return; //no effect on Hardened Advanced Armor
			}
*/				
			$effectSensors = Dice::d(6,1);//Strength of effect: 1d6
			$fireOrder->pubnotes .= "<br> Sensors reduced by $effectSensors next turn.";

			$CnC = $ship->getSystemByName("CnC");
			if($CnC){
				for($i=1; $i<=$effectSensors;$i++){
					$crit = new tmpsensordown(-1, $ship->id, $CnC->id, 'tmpsensordown', $gamedata->turn); 
					$crit->updated = true;
			        	$CnC->criticals[] =  $crit;
				}
		}	
	} //endof function onDamagedSystem	

} //endof class AmmoMissileX


//ammunition for AmmoMagazine - Class Z Antimine (for official Missile Racks)
class AmmoMissileZ extends AmmoMissileTemplate{	
	public $name = 'ammoMissileZ';
	public $displayName = 'Antimine Missile';
	public $modeName = 'Z - Antimine';
	public $size = 1; //how many store slots are required for a single round
	public $enhancementName = 'AMMO_Z'; //enhancement name to be enabled
	public $enhancementDescription = '(AMMO) Antimine Missile';
	public $enhancementPrice = 8; //PV per missile;
	
	public $rangeMod = 0; //MODIFIER for launch range
	public $distanceRangeMod = 0; //MODIFIER for distance range
	public $fireControlMod = array(null, 3, null); //MODIFIER for weapon fire control! Should be 3
	public $minDamage = 15;
	public $maxDamage = 15;	
	public $damageType = 'Standard';//mode of dealing damage
	public $weaponClass = 'Ballistic';//weapon class
	public $priority = 5;
	public $priorityAF = 5;
	public $noOverkill = false;
    public $useOEW = false;
	public $hidetarget = false;
    public $calledShotMod = 0;   	
	
    public $ballistic = true;
    public $hextarget = true;    
    
    public function getDamage($fireOrder) //actual function to be called, as with weapon!
    {
        return 15;
    }	


	public function beforeFiringOrderResolution($gamedata, $weapon, $originalFireOrder){

		$targetHex = new OffsetCoordinate($originalFireOrder->x, $originalFireOrder->y);
		$fired = false;
		$ship = $weapon->getUnit();
		
		$ships0 = $gamedata->getShipsInDistance($targetHex);
		foreach ($ships0 as $targetShip) {
			if(!$targetShip instanceof Mine) continue;
			if($targetShip->team == $ship->team) continue;
	        if ($targetShip->isDestroyed()) continue;   			
				
			$newFireOrder = new FireOrder(
			-1, "ballistic", $originalFireOrder->shooterid, $targetShip->id,
				$weapon->id, -1, $gamedata->turn, $originalFireOrder->firingMode, 
				0, 0, 1, 0, 0, //needed, rolled, shots, shotshit, intercepted
				0,0,"Hex",-1 //X, Y, damageclass, resolutionorder
			);
			$newFireOrder->addToDB = true;
			$weapon->fireOrders[] = $newFireOrder;
			$fired = true;	
			break;			
		}	
		
		if(!$fired){			
			$shooter = $gamedata->getShipById($originalFireOrder->shooterid);		
			$targetMine = $gamedata->getClosestEnemyMine($shooter, $targetHex, 3);
			if($targetMine !== null){
				$newFireOrder = new FireOrder(
				-1, "ballistic", $originalFireOrder->shooterid, $targetMine->id,
					$weapon->id, -1, $gamedata->turn, $originalFireOrder->firingMode, 
					0, 0, 1, 0, 0, //needed, rolled, shots, shotshit, intercepted
					0,0,"Scanned",-1 //X, Y, damageclass, resolutionorder
				);
				$newFireOrder->addToDB = true;
				$weapon->fireOrders[] = $newFireOrder;
				$fired = true;	
			}				
		}		

		if(!$fired){
			$originalFireOrder->pubnotes = "<br>No mines detected.";	
		}
	

	}//endof function beforeFiringOrderResolution   


	public function calculateHitBase($gamedata, $fireOrder)
	{	
		if($fireOrder->targetid == -1){
			$fireOrder->needed = 100; //always true
			$fireOrder->updated = true;	
		}else{
			//parent::calculateHitBase($gamedata, $fireOrder);
			if($fireOrder->damageclass == "Scanned") $fireOrder->needed -= 15;	//-15% to hit is not in orignal hex.
		}	
			    
	}// end of function calculateHitBase  


     public function fire($gamedata, $fireOrder) 	//For Multiwarhead missiles
    {

		if($fireOrder->targetid == -1){
	        //$rolled = Dice::d(100);
	        //$fireOrder->rolled = $rolled;
			// Do NOT set shotshit++ here - this hex fire order is just the launch marker.
			// The actual hit/damage is handled by the secondary mine-targeting fire order via parent::fire().
			//$fireOrder->pubnotes .= " Antimine missile fired.";	
			return;
		}else{

			if($fireOrder->targetid !== -1){ //Not hex shot,
				$targetMine = $gamedata->getShipById($fireOrder->targetid);	
				$mineStealthSystem = $targetMine->getSystemByName("MineStealth");

				$mineStealthSystem->markDetected($gamedata, $targetMine); //Mine was fired at, detect it for this team.
			}
		}	

	}//end of function fire
            
    public function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder)
    {
		$mineStealthSystem = $ship->getSystemByName("MineStealth");		
		$mineStealthSystem->markRevealed($gamedata, $ship); //Mine was hit, so reveal its info too.

        parent::onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder);
    }//endof function onDamagedSystem

	
} //endof class AmmoMissilez


//ammunition for AmmoMagazine - Class FB Missile (Fighter Basic Missile)
class AmmoMissileFB extends AmmoMissileTemplate{	
	public $name = 'ammoMissileFB';
	//public $displayName = 'Fighter Basic Missile';
	public $displayName = 'Basic Missile'; //as we're in fighter context, adding 'Fighter' to name is unnecessary clutter
	public $modeName = 'Basic';
	public $size = 1; //how many store slots are required for a single round
	public $enhancementName = 'AMMO_FB'; //enhancement name to be enabled
	public $enhancementDescription = '(AMMO) Fighter Basic Missile'; //enhancement description
	public $enhancementPrice = 8; //PV per missile
	
	public $rangeMod = 0; //MODIFIER for launch range
	public $distanceRangeMod = 0; //MODIFIER for distance range
	public $fireControlMod = array(3, 3, 3); 
	public $minDamage = 10;
	public $maxDamage = 10;	
	public $damageType = 'Standard';//mode of dealing damage
	public $weaponClass = 'Ballistic';//weapon class
	public $priority = 5;
	public $priorityAF = 6;
		
    public function getDamage($fireOrder) //actual function to be called, as with weapon!
    {
        return 10;
    }		
} //endof class AmmoMissileFB


//ammunition for AmmoMagazine - Class FL Missile (Fighter Long Range)
class AmmoMissileFL extends AmmoMissileTemplate{	
	public $name = 'ammoMissileFL';
	//public $displayName = 'Fighter Long Range Missile';
	public $displayName = 'Long Range Missile'; //as we're in fighter context, adding 'Fighter' to name is unnecessary clutter
	public $modeName = 'LongRange';
	public $size = 1; //how many store slots are required for a single round
	public $enhancementName = 'AMMO_FL'; //enhancement name to be enabled
	public $enhancementDescription = '(AMMO) Fighter Long Range Missile'; //2226 for Kor-Lyan, 2245 for everyone else 
	public $enhancementPrice = 12; //PV per missile; originally it's 10 for Kor-Lyan and 12 for everyone else
	
	public $rangeMod = 5; //MODIFIER for launch range
	public $distanceRangeMod = 5; //MODIFIER for distance range
	public $fireControlMod = array(3, 3, 3); //for fighter missiles putting everything into weapon FC would be incorrect - as FC is not used if out of arc... 
	public $minDamage = 8;
	public $maxDamage = 8;	
	public $damageType = 'Standard';//mode of dealing damage
	public $weaponClass = 'Ballistic';//weapon class
	public $priority = 4;
	public $priorityAF = 7;
		
    public function getDamage($fireOrder) //actual function to be called, as with weapon!
    {
        return 8;
    }		
	
	function getPrice($unit) //some missiles might have different price depending on unit being fitted!
	{
		//if($unit->faction == 'Kor-Lyan') return 10;
		if (stristr($unit->faction,'Kor-Lyan')) return 10;
		return $this->enhancementPrice;
	}
} //endof class AmmoMissileFL


//ammunition for AmmoMagazine - Class FH Missile (Fighter Heavy)
//NOTE: up to 1 per fighter (2 for SHFs)
class AmmoMissileFH extends AmmoMissileTemplate{	
	public $name = 'ammoMissileFH';
	//public $displayName = 'Fighter Heavy Missile';
	public $displayName = 'Heavy Missile'; //as we're in fighter context, adding 'Fighter' to name is unnecessary clutter
	public $modeName = 'Heavy';
	public $size = 1; //how many store slots are required for a single round
	public $enhancementName = 'AMMO_FH'; //enhancement name to be enabled
	public $enhancementDescription = '(AMMO) Fighter Heavy Missile'; //2226 for Kor-Lyan, 2245 for everyone else 
	public $enhancementPrice = 12; //PV per missile; originally it's 10 for Kor-Lyan and 12 for everyone else
	
	public $rangeMod = -5; //MODIFIER for launch range
	public $distanceRangeMod = -5; //MODIFIER for distance range
	public $fireControlMod = array(1, 3, 3); //for fighter missiles putting everything into weapon FC would be incorrect - as FC is not used if out of arc... 
	public $minDamage = 15;
	public $maxDamage = 15;	
	public $damageType = 'Standard';//mode of dealing damage
	public $weaponClass = 'Ballistic';//weapon class
	public $priority = 6;
	public $priorityAF = 5;
		
    public function getDamage($fireOrder) //actual function to be called, as with weapon!
    {
        return 15;
    }		
	
	function getPrice($unit) //some missiles might have different price depending on unit being fitted!
	{
		//if($unit->faction == 'Kor-Lyan') return 10;
		if (stristr($unit->faction,'Kor-Lyan')) return 10;
		return $this->enhancementPrice;
	}
} //endof class AmmoMissileFH


//ammunition for AmmoMagazine - Class FY Missile (Dogfight Missile)
//NOTE: in tabletop it has snap fire option, which is not available in FV
class AmmoMissileFY extends AmmoMissileTemplate{	
	public $name = 'ammoMissileFY';
	public $displayName = 'Dogfight Missile';
	public $modeName = 'Dogfight';
	public $size = 1; //how many store slots are required for a single round
	public $enhancementName = 'AMMO_FY'; //enhancement name to be enabled
	public $enhancementDescription = '(AMMO) Dogfight Missile'; 
	public $enhancementPrice = 2; //PV per missile
	
	public $rangeMod = -2; //MODIFIER for launch range
	public $distanceRangeMod = -2; //MODIFIER for distance range
	public $fireControlMod = array(3, 3, 3); //for fighter missiles putting everything into weapon FC would be incorrect - as FC is not used if out of arc... 
	public $minDamage = 6;
	public $maxDamage = 6;	
	public $damageType = 'Standard';//mode of dealing damage
	public $weaponClass = 'Ballistic';//weapon class
	public $priority = 4;
	public $priorityAF = 8;
		
    public function getDamage($fireOrder) //actual function to be called, as with weapon!
    {
        return 6;
    }
} //endof class AmmoMissileFY


//ammunition for AmmoMagazine - Class FD Missile (Dropout Missile)
//NOTE: in tabletop it has snap fire option, which is not available in FV
class AmmoMissileFD extends AmmoMissileTemplate{	
	public $name = 'ammoMissileFD';
	public $displayName = 'Dropout Missile';
	public $modeName = 'RDropout'; //R to differentiate from D - on mode change first letter is displayed!
	public $size = 1; //how many store slots are required for a single round
	public $enhancementName = 'AMMO_FD'; //enhancement name to be enabled
	public $enhancementDescription = '(AMMO) Dropout Missile'; 
	public $enhancementPrice = 10; //PV per missile; originally it's 8 for Kor-Lyan and 10 for everyone else
	
	public $fireControlMod = array(3, 1, 1); //for fighter missiles putting everything into weapon FC would be incorrect - as FC is not used if out of arc... 
	public $minDamage = 6;
	public $maxDamage = 6;	
	public $damageType = 'Standard';//mode of dealing damage
	public $weaponClass = 'Ballistic';//weapon class
	public $priority = 4;
	public $priorityAF = 10; //at the very end of queue - to drop out fighters that actually survived other impacts
		
    public function getDamage($fireOrder) //actual function to be called, as with weapon!
    {
        return 6;
    }
	
	function getPrice($unit) //some missiles might have different price depending on unit being fitted!
	{
		//if($unit->faction == 'Kor-Lyan') return 8;
		if (stristr($unit->faction,'Kor-Lyan')) return 8;
		return $this->enhancementPrice;
	}	
	
	/*dropout missile effect on hit: +3 dropout penalty for SHFs, +6 for other small craft*/
    public function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder)
    {	
		if(!($ship instanceOf FighterFlight)) return;
		if($system->advancedArmor) return;
//		if($system->hardAdvancedArmor) return;  // GTS Hardened Advanced Armor
		if($ship->superheavy){
			$system->critRollMod+=3;
		}else{
			$system->critRollMod+=6;
		}
    }
} //endof class AmmoMissileFD

class AmmoMissileFDum extends AmmoMissileTemplate{	
	public $name = 'AmmoMissileFDum';
	public $displayName = 'Dummy Missile';
	public $modeName = 'XDummy'; //R to differentiate from D - on mode change first letter is displayed!
	public $size = 1; //how many store slots are required for a single round
	public $enhancementName = 'AMMO_DUM'; //enhancement name to be enabled
	public $enhancementDescription = '(AMMO) Dummy Missile'; 
	public $enhancementPrice = 1; //PV per missile; originally it's 8 for Kor-Lyan and 10 for everyone else
	
	public $fireControlMod = array(null, null, null); //Should never fire.
	public $minDamage = 0;
	public $maxDamage = 0;	
	public $damageType = 'Standard';//mode of dealing damage
	public $weaponClass = 'Ballistic';//weapon class
	public $priority = 10;
	public $priorityAF = 10; //at the very end of queue - to drop out fighters that actually survived other impacts
		
    public function getDamage($fireOrder) //actual function to be called, as with weapon!
    {
        return 0;
    }
	
	function getPrice($unit) //some missiles might have different price depending on unit being fitted!
	{
		return $this->enhancementPrice;
	}	

} //endof class AmmoMissileDum


//ammunition for AmmoMagazine - Basic Mine for BallisticMineLauncher
class AmmoBLMineB extends AmmoMissileTemplate{	
	public $name = 'AmmoBLMineB';
	public $displayName = 'Basic Mine';
	public $modeName = 'Basic Mine';
	public $size = 1; //how many store slots are required for a single round
	public $enhancementName = 'MINE_BLB'; //enhancement name to be enabled
	public $enhancementDescription = '(AMMO) Basic Mine'; //enhancement description
	public $enhancementPrice = 6;
	
	public $rangeMod = 0; //MODIFIER for launch range
	public $distanceRangeMod = 0; //MODIFIER for distance range
	public $fireControlMod = array(8, 8, 8); //MODIFIER for weapon fire control!
	public $minDamage = 17;
	public $maxDamage = 26;	
	public $damageType = 'Standard';//mode of dealing damage
	public $weaponClass = 'Ballistic';//weapon class
	public $priority = 6;
	public $priorityAF = 5;

	public $hidetarget = true;

    public $hextarget = true; 
	public $mineRange = 3;
	public $animationExplosionScale = 0.25; //single hex explosion	

    public function getDamage($fireOrder){        return Dice::d(10, 1)+16;   } 
		
} //endof class AmmoBLMineB


//ammunition for AmmoMagazine - Heavy Mine for BallisticMineLauncher
class AmmoBLMineH extends AmmoMissileTemplate{	
	public $name = 'AmmoBLMineH';
	public $displayName = 'Heavy Mine';
	public $modeName = 'Heavy Mine';
	public $size = 1; //how many store slots are required for a single round
	public $enhancementName = 'MINE_BLH'; //enhancement name to be enabled
	public $enhancementDescription = '(AMMO) Heavy Mine'; //enhancement description
	public $enhancementPrice = 6;
	
	public $rangeMod = 0; //MODIFIER for launch range
	public $distanceRangeMod = 0; //MODIFIER for distance range
	public $fireControlMod = array(5, 5, 5); //MODIFIER for weapon fire control!
	public $minDamage = 25;
	public $maxDamage = 34;	
	public $damageType = 'Standard';//mode of dealing damage
	public $weaponClass = 'Ballistic';//weapon class
	public $priority = 6;
	public $priorityAF = 5;
	public $animationExplosionScale = 0.4;

	public $hidetarget = true;

    public $hextarget = true; 
	public $mineRange = 2;

    public function getDamage($fireOrder){        return Dice::d(10, 1)+24;   } 
	
} //endof class AmmoBLMineH

//ammunition for AmmoMagazine - Basic Mine for BallisticMineLauncher
class AmmoBLMineW extends AmmoMissileTemplate{	
	public $name = 'AmmoBLMineW';
	public $displayName = 'Wide-Range Mine';
	public $modeName = 'Wide Mine';
	public $size = 1; //how many store slots are required for a single round
	public $enhancementName = 'MINE_BLW'; //enhancement name to be enabled
	public $enhancementDescription = '(AMMO) Wide-Range Mine'; //enhancement description
	public $enhancementPrice = 6;
	
	public $rangeMod = 0; //MODIFIER for launch range
	public $distanceRangeMod = 0; //MODIFIER for distance range
	public $fireControlMod = array(6, 6, 6); //MODIFIER for weapon fire control!
	public $minDamage = 13;
	public $maxDamage = 22;	
	public $damageType = 'Standard';//mode of dealing damage
	public $weaponClass = 'Ballistic';//weapon class
	public $priority = 6;
	public $priorityAF = 5;
	
	public $hidetarget = true;

    public $hextarget = true; 
	public $mineRange = 5;	
		public $animationExplosionScale = 0.25; //single hex explosion

    public function getDamage($fireOrder){        return Dice::d(10, 1)+12;   } 

} //endof class AmmoBLMineW

//ammunition for AmmoMagazine - Basic Mine for AbbaiMineLauncher
class AmmoBistifA extends AmmoMissileTemplate{	
	public $name = 'AmmoBistifA';
	public $displayName = 'Basic Mine';
	public $modeName = 'Basic Mine';
	public $size = 1; //how many store slots are required for a single round
	public $enhancementName = 'MINE_MLB'; //enhancement name to be enabled
	public $enhancementDescription = '(AMMO) Basic Mine'; //enhancement description
	public $enhancementPrice = 8;
	
	public $rangeMod = 0; //MODIFIER for launch range
	public $distanceRangeMod = 0; //MODIFIER for distance range
	public $fireControlMod = array(2, 2, 2); //MODIFIER for weapon fire control!
	public $minDamage = 12;
	public $maxDamage = 12;	
	public $damageType = 'Standard';//mode of dealing damage
	public $weaponClass = 'Ballistic';//weapon class
	public $priority = 6;
	public $priorityAF = 5;
	
	public $hidetarget = true;

    public $hextarget = true; 
	public $mineRange = 4;	

    public function getDamage($fireOrder){        return 12;   } 

} //endof class AmmoBistifA

//ammunition for AmmoMagazine - Wide-Ranged Mine for AbbaiMineLauncher
class AmmoBistifB extends AmmoMissileTemplate{	
	public $name = 'AmmoBistifB';
	public $displayName = 'Wide-Ranged Mine';
	public $modeName = 'Wide Mine';
	public $size = 1; //how many store slots are required for a single round
	public $enhancementName = 'MINE_MLW'; //enhancement name to be enabled
	public $enhancementDescription = '(AMMO) Wide-Range Mine'; //enhancement description
	public $enhancementPrice = 12;
	
	public $rangeMod = 0; //MODIFIER for launch range
	public $distanceRangeMod = 0; //MODIFIER for distance range
	public $fireControlMod = array(2, 2, 2); //MODIFIER for weapon fire control!
	public $minDamage = 12;
	public $maxDamage = 12;	
	public $damageType = 'Standard';//mode of dealing damage
	public $weaponClass = 'Ballistic';//weapon class
	public $priority = 6;
	public $priorityAF = 5;
	public $animationExplosionScale = 0.25;
	
	public $hidetarget = true;

    public $hextarget = true; 
	public $mineRange = 7;	

    public function getDamage($fireOrder){        return 12;   } 

} //endof class AmmoBistifB



//ammunition for AmmoMagazine - Vedas-A Mine for ChoukaMineLauncher
class AmmoVedasA extends AmmoMissileTemplate{	
	public $name = 'AmmoVedasA';
	public $displayName = 'Vedas-A';
	public $modeName = 'A-Vedas';
	public $size = 1; //how many store slots are required for a single round
	public $enhancementName = 'MINE_AML'; //enhancement name to be enabled
	public $enhancementDescription = '(AMMO) Vedas-A'; //enhancement description
	public $enhancementPrice = 6;
	
	public $rangeMod = 0; //MODIFIER for launch range
	public $distanceRangeMod = 0; //MODIFIER for distance range
	public $fireControlMod = array(3, 3, 3); //MODIFIER for weapon fire control!
	public $minDamage = 12;
	public $maxDamage = 12;	
	public $damageType = 'Standard';//mode of dealing damage
	public $weaponClass = 'Ballistic';//weapon class
	public $priority = 6;
	public $priorityAF = 5;
	
	public $hidetarget = true;

    public $hextarget = true; 
	public $mineRange = 4;	

    public function getDamage($fireOrder){        return 12;   } 

} //endof class AmmoVedasA

//ammunition for AmmoMagazine - Vedas-B Mine for ChoukaMineLauncher
class AmmoVedasB extends AmmoMissileTemplate{	
	public $name = 'AmmoVedasB';
	public $displayName = 'Vedas-B';
	public $modeName = 'B-Vedas';
	public $size = 1; //how many store slots are required for a single round
	public $enhancementName = 'MINE_BML'; //enhancement name to be enabled
	public $enhancementDescription = '(AMMO) Vedas-B'; //enhancement description
	public $enhancementPrice = 6;
	
	public $rangeMod = 0; //MODIFIER for launch range
	public $distanceRangeMod = 0; //MODIFIER for distance range
	public $fireControlMod = array(4, 4, 4); //MODIFIER for weapon fire control!
	public $minDamage = 15;
	public $maxDamage = 15;	
	public $damageType = 'Standard';//mode of dealing damage
	public $weaponClass = 'Ballistic';//weapon class
	public $priority = 6;
	public $priorityAF = 5;
	
	public $hidetarget = true;

    public $hextarget = true; 
	public $mineRange = 5;	

    public function getDamage($fireOrder){        return 15;   } 

} //endof class AmmoVedasB

//ammunition for AmmoMagazine - Vedas-C Mine for ChoukaMineLauncher
class AmmoVedasC extends AmmoMissileTemplate{	
	public $name = 'AmmoVedasC';
	public $displayName = 'Vedas-C';
	public $modeName = 'C-Vedas';
	public $size = 1; //how many store slots are required for a single round
	public $enhancementName = 'MINE_CML'; //enhancement name to be enabled
	public $enhancementDescription = '(AMMO) Vedas-C'; //enhancement description
	public $enhancementPrice = 6;
	
	public $rangeMod = 0; //MODIFIER for launch range
	public $distanceRangeMod = 0; //MODIFIER for distance range
	public $fireControlMod = array(6, 6, 6); //MODIFIER for weapon fire control!
	public $minDamage = 18;
	public $maxDamage = 18;	
	public $damageType = 'Standard';//mode of dealing damage
	public $weaponClass = 'Ballistic';//weapon class
	public $priority = 6;
	public $priorityAF = 5;
	
	public $hidetarget = true;

    public $hextarget = true; 
	public $mineRange = 6;	

    public function getDamage($fireOrder){        return 18;   } 

} //endof class AmmoVedasA


// GTS_Triad
class StructureSelfRepair extends ShipSystem {
    public $name = "StructureSelfRepair";
    public $displayName = "Structure Self Repair";
    public $iconPath = "StructureSelfRepair.png";
    public $primary = true;

    public $output = 0;
    public $maxRepairPoints = 0;   // ceiling = maxhealth * 10 (undamaged)
    public $usedRepairPoints = 0;  // accumulated across all turns
    public $usedThisTurn = 0;

    /* Player-specified repair order: array of structure system IDs, highest priority first.
       Empty = use default (destroyed first, then highest damage). Not persisted across turns. */
    public $repairOrder = array();

    public $repairPriority = 10;

    public $boostable = false;
    public $maxBoostLevel = 0;
    public $boostEfficiency = 0;

    protected $possibleCriticals = array(
        19 => "OutputHalved"
    );

    function __construct($armour, $maxhealth, $output)
    {
        if ($maxhealth < 1) $maxhealth = 1;
        if ($output < 1)    $output = 1;
        parent::__construct($armour, $maxhealth, 0, 0, 0);
        $this->output = $output;
        $this->maxRepairPoints = $maxhealth * 10;
    }

    /* Capacity ceiling, scales with remaining health */
    public function getCurrentMaxRepairPoints()
    {
        return $this->getRemainingHealth() * 10;
    }

    public function setSystemDataWindow($turn)
    {
        parent::setSystemDataWindow($turn);
        $this->data["Repair points (used/max)"] = $this->usedRepairPoints . "/" . $this->getCurrentMaxRepairPoints();
        $this->data["Special"]  = "Repairs damaged and destroyed structure blocks each turn, including damage caused this turn.";
        $this->data["Special"] .= "<br>Will not engage if the primary structure block is destroyed (unit is destroyed).";
        $this->data["Special"] .= "<br>Restoring a destroyed structure block reattaches all systems on that block; "
                                 . "those systems retain their individual damage and destroyed status.";
        $this->data["Special"] .= "<br>Default priority: destroyed blocks first, then most-damaged blocks.";
        $this->data["Special"] .= "<br>Player may set a custom repair order using the 'Manage Structure Repair' menu during Initial Orders.";
    }

    private function getBoostLevel($turn)
    {
        $boostLevel = 0;
        foreach ($this->power as $i) {
            if ($i->turn != $turn) continue;
            if ($i->type == 2)     $boostLevel += $i->amount;
        }
        return $boostLevel;
    }

    public function getEffectiveOutput($ship)
    {
        $turn   = TacGamedata::$currentTurn;
        $boost  = $this->getBoostLevel($turn);
        $output = $this->getOutput();
        return $output + $boost;
    }

    public function criticalPhaseEffects($ship, $gamedata)
    {
        parent::criticalPhaseEffects($ship, $gamedata);

        // Guard: primary structure destroyed means unit is destroyed — do not engage
        foreach ($ship->systems as $system) {
            if (!($system instanceof Structure)) continue;
            if ($system->location == 0) {
                if ($system->isDestroyed($gamedata->turn)) return;
                break;
            }
        }

        // Available points this turn
        $availableRepairPoints = $this->getCurrentMaxRepairPoints() - $this->usedRepairPoints;
        $availableRepairPoints = min($availableRepairPoints, $this->getEffectiveOutput($ship));
        if ($availableRepairPoints < 1) return;

        // Build repair queue
        $repairQueue = array();

        if (!empty($this->repairOrder)) {
            // Player-specified order: walk repairOrder, pick up damaged blocks in that sequence,
            // then append any remaining damaged blocks not mentioned in the order (default sort).
            $byId = array();
            foreach ($ship->systems as $system) {
                if (!($system instanceof Structure)) continue;
                $currentDamage = $system->maxhealth - $system->getRemainingHealth();
                if ($currentDamage < 1) continue;
                $byId[$system->id] = array(
                    'obj'          => $system,
                    'currentDamage'=> $currentDamage,
                    'destroyed'    => $system->isDestroyed($gamedata->turn) ? 1 : 0,
                );
            }

            // First pass: player-ordered blocks
            $seen = array();
            foreach ($this->repairOrder as $id) {
                if (isset($byId[$id])) {
                    $repairQueue[] = $byId[$id];
                    $seen[$id] = true;
                }
            }

            // Second pass: any damaged blocks not in the player order, default sort
            $remainder = array();
            foreach ($byId as $id => $entry) {
                if (!isset($seen[$id])) $remainder[] = $entry;
            }
            usort($remainder, function($a, $b) {
                if ($a['destroyed'] !== $b['destroyed']) return $b['destroyed'] - $a['destroyed'];
                return $b['currentDamage'] - $a['currentDamage'];
            });
            $repairQueue = array_merge($repairQueue, $remainder);

        } else {
            // Default: destroyed first, then highest damage
            foreach ($ship->systems as $system) {
                if (!($system instanceof Structure)) continue;
                $currentDamage = $system->maxhealth - $system->getRemainingHealth();
                if ($currentDamage < 1) continue;
                $repairQueue[] = array(
                    'obj'          => $system,
                    'currentDamage'=> $currentDamage,
                    'destroyed'    => $system->isDestroyed($gamedata->turn) ? 1 : 0,
                );
            }
            usort($repairQueue, function($a, $b) {
                if ($a['destroyed'] !== $b['destroyed']) return $b['destroyed'] - $a['destroyed'];
                return $b['currentDamage'] - $a['currentDamage'];
            });
        }

        if (empty($repairQueue)) return;

        // Execute repairs
        foreach ($repairQueue as $job) {
            if ($availableRepairPoints < 1) break;

            $structureBlock = $job['obj'];
            $currentDamage  = $job['currentDamage'];

            $toBeFixed = min($currentDamage, $availableRepairPoints);
            $undestroy = ($toBeFixed >= $currentDamage);

            $damageEntry = new DamageEntry(
                -1,
                $ship->id,
                -1,
                $gamedata->turn,
                $structureBlock->id,
                -$toBeFixed,
                0,
                0,
                -1,
                false,
                $undestroy,
                'StructureSelfRepair',
                'StructureSelfRepair'
            );
            $damageEntry->updated = true;
            $structureBlock->damage[] = $damageEntry;

            $availableRepairPoints  -= $toBeFixed;
            $this->usedRepairPoints += $toBeFixed;
            $this->usedThisTurn     += $toBeFixed;
        }
    }

    /* Receive repair order from client (Initial Orders phase).
       Format: one entry per structure block ID, semicolon-separated, in priority order. */
	public function doIndividualNotesTransfer()
	{
		if (is_array($this->individualNotesTransfer) && count($this->individualNotesTransfer) > 0) {
			$this->repairOrder = array();
			foreach ($this->individualNotesTransfer as $noteReceived) {
				$parts = explode(';', $noteReceived);
				if ($parts[0] === 'order' && isset($parts[1])) {
					$id = intval($parts[1]);
					if ($id > 0) $this->repairOrder[] = $id;
				}
			}
		}
    $this->individualNotesTransfer = array();
	}

    /* Save usedThisTurn to DB. repairOrder is not persisted (resets each turn). */
	public function generateIndividualNotes($gameData, $dbManager)
	{
		$ship = $this->getUnit();
		switch ($gameData->phase) {
			case 1: // Initial phase — save repair order
				if ($ship->userid == $gameData->forPlayer) {
					foreach ($this->repairOrder as $position => $systemId) {
						$this->individualNotes[] = new IndividualNote(
							-1, TacGamedata::$currentGameID, $gameData->turn, $gameData->phase,
							$ship->id, $this->id, 'order', 'Structure repair order', $position . ';' . $systemId
						);
					}
				}
			case 4: // Firing phase — save points used
				if ($this->usedThisTurn > 0) {
					$this->individualNotes[] = new IndividualNote(
						-1, TacGamedata::$currentGameID, $gameData->turn, $gameData->phase,
						$ship->id, $this->id, 'used', 'Structure self-repair used', $this->usedThisTurn
					);
				}
				break;
		}
	}

    /* Reconstruct usedRepairPoints from saved per-turn totals */
	public function onIndividualNotesLoaded($gamedata)
	{
		$orderEntries = array();
		foreach ($this->individualNotes as $currNote) {
			switch ($currNote->notekey) {
				case 'used':
					$this->usedRepairPoints += $currNote->notevalue;
					break;
				case 'order':
					$parts = explode(';', $currNote->notevalue);
					if (count($parts) === 2) {
						$orderEntries[(int)$parts[0]] = (int)$parts[1];
					}
					break;
			}
		}
		if (!empty($orderEntries)) {
			ksort($orderEntries);
			$this->repairOrder = array_values($orderEntries);
		}
		$this->individualNotes = array();
	}

    public function stripForJson()
    {
        $strippedSystem = parent::stripForJson();
        $strippedSystem->data = $this->data;

        // Send the full structure block list so the client can populate the repair-order UI.
        // Each entry: { id, displayName, location, section }
        // section is a human-readable label derived from location for display in the popup.
        $ship = $this->getUnit();
        $structureBlocks = array();
        $sectionLabels = array(
            0  => 'Primary Structure',
            1  => 'Forward Structure',
            2  => 'Aft Structure',
            3  => 'Port Structure',
            4  => 'Starboard Structure',
            5  => 'Upper Structure',
            6  => 'Lower Structure',
        );
        foreach ($ship->systems as $system) {
            if (!($system instanceof Structure)) continue;
            $loc = $system->location;
            $label = isset($sectionLabels[$loc]) ? $sectionLabels[$loc] : ($system->displayName . ' (section ' . $loc . ')');
            $structureBlocks[] = array(
                'id'          => $system->id,
                'displayName' => $label,
                'location'    => $loc,
            );
        }
        $strippedSystem->structureBlocks = $structureBlocks;

        // Also send the current repairOrder so the client reflects any order set earlier this turn
        if (!empty($this->repairOrder)) {
            $strippedSystem->repairOrder = array_values($this->repairOrder);
        }

        return $strippedSystem;
    }

    public function hasMaxBoost()
    {
        return ($this->maxBoostLevel > 0);
    }

} // endof class StructureSelfRepair







class CoopStructureSelfRepair extends StructureSelfRepair {
    public $name = "CoopStructureSelfRepair";
    public $displayName = "Cooperative Structure Self Repair";
    public $iconPath = "StructureSelfRepair.png";

    /* Range within which friendly units are eligible for cooperative repair */
    const COOP_RANGE = 5;

    public function setSystemDataWindow($turn)
    {
        parent::setSystemDataWindow($turn);
        $this->data["Special"] .= "<br>Cooperative: after repairing own structure, remaining points are used to repair "
                                 . "damaged structure blocks on friendly units within " . self::COOP_RANGE . " hexes.";
        $this->data["Special"] .= "<br>Prioritizes friendly units without any structure repair capability, then those with repair capability.";
        $this->data["Special"] .= "<br>Within each tier, destroyed blocks are repaired first, then most-damaged blocks.";
        $this->data["Special"] .= "<br>Cannot not assist units whose primary structure is destroyed.";
    }

    /* Returns true if a system is a functioning structure repair system (StructureSelfRepair or CoopStructureSelfRepair) */
    private function hasWorkingStructureRepair($targetShip)
    {
        foreach ($targetShip->systems as $sys) {
            if (($sys instanceof StructureSelfRepair) && !$sys->isDestroyed()) {
                return true;
            }
        }
        return false;
    }

    /* Returns true if the target ship's primary structure is destroyed */
	private function isPrimaryDestroyed($targetShip, $gamedata)
	{
		if ($targetShip instanceof FighterFlight) return false; // handled by isFlightViable
		foreach ($targetShip->systems as $sys) {
			if (!($sys instanceof Structure)) continue;
			if ($sys->location == 0) {
				return $sys->isDestroyed($gamedata->turn);
			}
		}
		return false;
	}

    /* Returns true if a fighter flight is still viable (has surviving fighters after dropout) */
    private function isFlightViable($targetShip, $gamedata)
    {
        if (!($targetShip instanceof FighterFlight)) return true; // Not a fighter — always viable
        // Check if flight has any surviving fighters
        foreach ($targetShip->systems as $fighter) {
            if (!$fighter->isDestroyed($gamedata->turn)) return true;
        }
        return false;
    }

    /* Build a flat list of damaged structure blocks on a target ship, sorted destroyed first then highest damage */
	private function getDamagedStructureBlocks($targetShip, $gamedata)
	{
		$blocks = array();

		if ($targetShip instanceof FighterFlight) {
			// For fighter flights, each Fighter system IS the structure
			foreach ($targetShip->systems as $fighter) {
				if ($fighter->isDestroyed($gamedata->turn)) continue; // destroyed fighters can't be repaired
				$currentDamage = $fighter->maxhealth - $fighter->getRemainingHealth();
				if ($currentDamage < 1) continue;
				$blocks[] = array(
					'obj'          => $fighter,
					'currentDamage'=> $currentDamage,
					'destroyed'    => 0, // surviving fighters are not destroyed by definition
				);
			}
		} else {
			// For ships, look for Structure instances
			foreach ($targetShip->systems as $sys) {
				if (!($sys instanceof Structure)) continue;
				$currentDamage = $sys->maxhealth - $sys->getRemainingHealth();
				if ($currentDamage < 1) continue;
				$blocks[] = array(
					'obj'          => $sys,
					'currentDamage'=> $currentDamage,
					'destroyed'    => $sys->isDestroyed($gamedata->turn) ? 1 : 0,
				);
			}
		}

		usort($blocks, function($a, $b) {
			if ($a['destroyed'] !== $b['destroyed']) return $b['destroyed'] - $a['destroyed'];
			return $b['currentDamage'] - $a['currentDamage'];
		});
		return $blocks;
	}

    /* Apply repair points to a list of structure blocks. Returns points remaining. */
    private function repairBlocks($blocks, $availableRepairPoints, $ship, $gamedata)
    {
        foreach ($blocks as $job) {
            if ($availableRepairPoints < 1) break;

            $structureBlock = $job['obj'];
            $currentDamage  = $structureBlock->maxhealth - $structureBlock->getRemainingHealth();
            if ($currentDamage < 1) continue; // Already repaired (by this ship's own SR, etc.)

            $toBeFixed = min($currentDamage, $availableRepairPoints);
            $undestroy = ($toBeFixed >= $currentDamage);

            $damageEntry = new DamageEntry(
                -1,
                $ship->id,
                -1,
                $gamedata->turn,
                $structureBlock->id,
                -$toBeFixed,
                0,
                0,
                -1,
                false,
                $undestroy,
                'CoopStructureSelfRepair',
                'CoopStructureSelfRepair'
            );
            $damageEntry->updated = true;
            $structureBlock->damage[] = $damageEntry;

            $availableRepairPoints  -= $toBeFixed;
            $this->usedRepairPoints += $toBeFixed;
            $this->usedThisTurn     += $toBeFixed;
        }
        return $availableRepairPoints;
    }

    public function criticalPhaseEffects($ship, $gamedata)
    {
        // Step 1: Run inherited self-repair pass (repairs own structure, respects player order)
        parent::criticalPhaseEffects($ship, $gamedata);

        if ($this->isDestroyed()) return;
//error_log("CoopRepair DEBUG: getCurrentMaxRepairPoints=" . $this->getCurrentMaxRepairPoints() 
//    . " usedRepairPoints=" . $this->usedRepairPoints 
//    . " getEffectiveOutput=" . $this->getEffectiveOutput($ship));
        // Step 2: Check remaining points after self-repair
//        $availableRepairPoints = $this->getCurrentMaxRepairPoints() - $this->usedRepairPoints;
//        $availableRepairPoints = min($availableRepairPoints, $this->getEffectiveOutput($ship));
		$availableRepairPoints = $this->getEffectiveOutput($ship) - $this->usedThisTurn;
        if ($availableRepairPoints < 1) return;

        // Step 3: Build eligible friendly target list
        $tier1 = array(); // Friendlies without working structure repair
        $tier2 = array(); // Friendlies with working structure repair

        foreach ($gamedata->ships as $targetShip) {
            // Must be friendly (same userid), not self
            if ($targetShip->id === $ship->id) continue;
            if ($targetShip->userid !== $ship->userid) continue;

            // Primary structure must not be destroyed
            if ($this->isPrimaryDestroyed($targetShip, $gamedata)) continue;

            // Fighter flights must still be viable
            if (!$this->isFlightViable($targetShip, $gamedata)) continue;

            // Must be within range
            if (Mathlib::getDistanceHex($targetShip, $ship) > self::COOP_RANGE) continue;

            // Must have at least one damaged structure block
            $blocks = $this->getDamagedStructureBlocks($targetShip, $gamedata);
            if (empty($blocks)) continue;

            $entry = array(
                'ship'   => $targetShip,
                'blocks' => $blocks,
                // Sort key: destroyed block count descending, then total damage descending
                'hasDestroyed' => ($blocks[0]['destroyed'] ? 1 : 0),
                'totalDamage'  => array_sum(array_column($blocks, 'currentDamage')),
            );

            if ($this->hasWorkingStructureRepair($targetShip)) {
                $tier2[] = $entry;
            } else {
                $tier1[] = $entry;
            }
        }

        // Sort each tier: destroyed blocks first, then most total damage
        $sortFn = function($a, $b) {
            if ($a['hasDestroyed'] !== $b['hasDestroyed']) return $b['hasDestroyed'] - $a['hasDestroyed'];
            return $b['totalDamage'] - $a['totalDamage'];
        };
        usort($tier1, $sortFn);
        usort($tier2, $sortFn);

        $targets = array_merge($tier1, $tier2);

        // Step 4: Distribute remaining points across targets, block by block
        foreach ($targets as $target) {
            if ($availableRepairPoints < 1) break;
            $availableRepairPoints = $this->repairBlocks(
                $target['blocks'],
                $availableRepairPoints,
                $target['ship'],
                $gamedata
            );
        }
    }

} // endof class CoopStructureSelfRepair



?>
