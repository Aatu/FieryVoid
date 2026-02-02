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
	public $detected = false;
    
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

        switch($gameData->phase){
			case 1: //Initial Orders - Check for any ballistic launches
				if(!$this->detected){ //Only check if ship has not been already detected at some point.
					//Did stealth ship launch any ballistics?
					$ballisticOrEW = $this->isDetectedInitial($ship, $gameData);

					if($ballisticOrEW){ //There was a ballistic launch this turn.  Create note for ship to be marked detected.
						//Prepare note for database!		
						$notekey = 'detected';
						$noteHuman = 'Ship detected';
						$noteValue = 1;
						$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$noteValue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue							
					}
				}	
			break;

			case 5: //Pre-Firing phase Advance(), always called even if phase not needed in game.
				if(!$this->detected){ //Ship has not already been detected, check if it is detected now.
					if($this->isDetectedFire($ship, $gameData)){ //Now check if ship just been detected this turn?		
						$notekey = 'detected';
						$noteHuman = 'Ship detected';
						$noteValue = 1;
						$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$noteValue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue
					}
				}else{ //Ship is already detected, but can it now become undetected again by breaking all line of sight? 
					if($this->isUndetected($ship, $gameData)){ //Line of Sight test
						//Prepare note for database!		
						$notekey = 'undetected';
						$noteHuman = 'Ship undetected';
						$noteValue = 1;
						$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$noteValue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue
					}

				}
			break;			
					
        }
    } //endof function generateIndividualNotes	
 	

	public function onIndividualNotesLoaded($gamedata){
		//Sort notes by turn, and then phase so latest detection note is always last.
		$this->sortNotes();

		foreach ($this->individualNotes as $currNote){ //Search all notes, they should be process in order so the latest event applies.
			switch($currNote->notekey){
				case 'detected': 
					if($currNote->notevalue == 1) $this->detected = true;
				break;
				case 'undetected': 
					if($currNote->notevalue == 1) $this->detected = false;
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
		$revealed = false;

		foreach($ship->systems as $weapon){ //Check for weapon fire.
			if($weapon instanceof Weapon){
				if($weapon->firedOffensivelyOnTurn($gameData->turn)) {
					$revealed = true;
					return $revealed;
				}	
			}
		}

		// If the ship used offensive or ELINT EW, it is revealed
		$usedEW = $ship->getAllEWExceptDEW($gameData->turn); // Has used any EW except DEW?
		if($usedEW > 0) $revealed = true;

		return $revealed;
	}	


	private function isDetectedFire($ship, $gameData) {
		// If the ship has fired this turn, it is revealed
		foreach($ship->systems as $weapon){ //Check for weapon fire.
			if($weapon instanceof Weapon){
				$firingOrders = $weapon->getFireOrders($gameData->turn);
				foreach ($firingOrders as $fireOrder) { 
					if($fireOrder->type == "normal"){ //Ballistics already handled in Phase 1.
						return true; //Just return, fired in Firing Phase revealing itself again even without LoS. Although who know at what without LoS...
					}	
				}	
			}
		}

		// Check all enemy ships to see if any can detect this ship at end of turn
		$blockedHexes = $gameData->getBlockedHexes(); //Just do this once outside loop
		$pos = $ship->getHexPos(); //Just do this once outside loop		

		foreach ($gameData->ships as $otherShip) {
			// Skip friendly ships
			if($otherShip->team === $ship->team) continue;
			if($otherShip->isTerrain()) continue; //Ignore Terrain
			if($otherShip->isDestroyed()) continue; //Ignore destroyed enemy ships.
	
			$totalDetection = 0;
	
			if (!$otherShip instanceof FighterFlight) {
				if($otherShip->isDisabled()) continue;
				// Not a fighter — use scanner systems
				foreach($otherShip->systems as $system){
					if($system instanceof Scanner){
						if(!$system->isDestroyed() && !$system->isOfflineOnTurn()) $totalDetection += $system->output;
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
				return true; //Just return, if one ship can see the stealthed ship then all can.
			}
		}

		return false; //No other conditions were true, not detected.

	}


	private function isUndetected($ship, $gameData) {		
		$blockedHexes = $gameData->getBlockedHexes(); //Save outside loop as this won't change.
		$shipPosition = $ship->getHexPos(); //Save outside loop as this won't change.
		$canStealth = true; //Default to being able to stealth again, then prove if it can't below.

		//Check all enemy ships for line of sight, if none then ship can stealth again.
		if (!empty($blockedHexes)) {  //No point checking if there are no blocked hexes.
			foreach($gameData->ships as $enemyShip){
				if($enemyShip->team == $ship->team) continue; //ignore ships in same team.
				if($enemyShip->isTerrain()) continue; //Ignore Terrain
				if($enemyShip->isDestroyed()) continue; //Ignore destroyed enemy ships.

				$enemyPosition = $enemyShip->getHexPos();
				$noLoS = false; //False means LoS not blocked
			
				$noLoS = Mathlib::isLoSBlocked($shipPosition, $enemyPosition, $blockedHexes);				

				if(!$noLoS){ //The enemy unit can see this ship LoS not blocked.
					$canStealth = false;
					return $canStealth; //Just return, only need one enemy ship to have LoS to fail check.
				}
			}
		}else{//Just return false, nothing to block LoS.			
			$canStealth = false;
			return $canStealth; 
		}	

		//Check for weapon fire.
		foreach($ship->systems as $weapon){ 
			if($weapon instanceof Weapon){
				$firingOrders = $weapon->getFireOrders($gameData->turn);
				foreach ($firingOrders as $fireOrder) { 
					if($fireOrder->type == "normal"){ //Ballistics already handled in Phase 1, and shouldn't prevent re-stealth at end of turn.						
						$canStealth = false;
						return $canStealth; //Just return, fired in Firing Phase revealing itself again even without LoS. Although who know at what without LoS...
					}	
				}	
			}
		}
 
		return $canStealth;
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
        return $strippedSystem;
    }

} //endof Stealth


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
        
        $output = $this->output;
        $output += $this->outputMod; //outputMod itself is negative!
        return $output;
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
			$newFireOrder->pubnotes .= "Reactor destroyed - entire ship is immolated.";
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
	public function setSystemDataWindow($turn){
		$this->data["Output"] = $this->output;
		parent::setSystemDataWindow($turn);     
		$this->data["Special"] = "This system is here for technical purposes only. Cannot be damaged in any way.";
		$ship = $this->getUnit();
		//I'm reusing this system in Asteroid unit, but don't want this text here - DK
		if($ship->factionAge > 2){
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
			$newFireOrder->pubnotes = "Sub Reactor destroyed - section is immolated.";
			$newFireOrder->addToDB = true;
			$rammingSystem->fireOrders[] = $newFireOrder;
		}else{
			$newFireOrder=null;
		}

		//destroy primary structure
		$ownStruct = $ship->getStructureSystem($this->location);
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
        $this->data["Special"] .= "Allows additional Sensor operations:";
        $this->data["Special"] .= "<br> - SOEW: gives friendly ships half of ELINT ships' OEW bonus against targets. Targets must be within 30 hexes of ELINT at the moment of firing, friendly ships at both declaration and firing.  Requires line of sight to target and friendly ship at firing.";		     
        $this->data["Special"] .= "<br> - SDEW: boosts target's DEW (by 1 for 2 points allocated). Range 30 hexes (at both declaration and firing).";		     
        $this->data["Special"] .= "<br> - Blanket Protection: all friendly units within 20 hexes (incl. fighters) get +1 DEW per 4 points allocated. Cannot combine with other ElInt activities.";		     
        $this->data["Special"] .= "<br> - Disruption: Reduces target enemy ships' OEW and CCEW by 1 per 3 points allocated (split evenly between targets, CCEW being counted as one target; cannot bring OEW on a target below 0). Range 30 hexes (at both declaration and firing).";	
		$this->data["Special"] .= "<br> - Detect Stealth: Increases stealth detection range of this ship by +2 per point of EW used.";	    
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
			
} //endof class CnC


class OSATCnC extends CnC{	//Special technical OSAT CnC system, so criticals effects can be applied to these units etc
    public $iconPath = "cnCtechnical.png";
    public $isPrimaryTargetable = false;
	public $isTargetable = false;   
	public $doCountForCombatValue = false;   

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

class DockingCollar extends ShipSystem{
    public $name = "DockingCollar";
    public $displayName = "Docking Collar";
    
	//Quarters is not important at all!
	public $repairPriority = 1;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
    
    function __construct($armour, $maxhealth, $output = 1){
        parent::__construct($armour, $maxhealth, 0, $output);
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
    
    function __construct($armour, $maxhealth, $output = null){
		if($output === null){ //if output is not explicitly indicated, assume it to be 6 per every full 6 boxes! (that's the usual combat craft capacity)
			$output = floor($maxhealth/6)*6;
		}
        parent::__construct($armour, $maxhealth, 0, $output ); 
    }
}


class Catapult extends ShipSystem{
    public $name = "catapult";
    public $displayName = "Catapult";
    public $squadrons = Array();
    public $primary = false; //changed from true on 21.11 - let's not consider it a core system after all!
    
	//Catapult is not impotant at all!
	public $repairPriority = 1;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
    
    function __construct($armour, $maxhealth, $output = 1){
        parent::__construct($armour, $maxhealth, 0, $output );
 
    }
}


class JumpEngine extends ShipSystem{
    public $name = "jumpEngine";
    public $displayName = "Jump Engine";
    public $delay = 0;
    public $primary = true;
    
    //Make boostable to do 'Jumping Out' effect
    public $boostable = true; //for reactor overload feature!
    public $maxBoostLevel = 1;
    public $boostEfficiency = 0;    
    
	//JumpEngine tactically  is not important at all!
	public $repairPriority = 6;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired

	private $preJumpValue = 0; //Will be used to store ship's Combat Value at the moment it jumped.
    
    function __construct($armour, $maxhealth, $powerReq, $delay){
        parent::__construct($armour, $maxhealth, $powerReq, 0);
    
        $this->delay = $delay;
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

		$currHealth = $this->getRemainingHealth();
		$maxhealth = $this->maxhealth;
		$healthDiff = $maxhealth - $currHealth;
	
		// Calculate the percentage of health missing
		$missingHealthPercentage = round(($healthDiff / $maxhealth) * 100);

		// Roll a D100
		$d100Roll = Dice::d(100);
	
		// Determine if the jump fails
		$jumpFailure = $missingHealthPercentage > 0 && $d100Roll <= $missingHealthPercentage;

		// Try to create the fire order for logs
		$rammingSystem = $ship->getSystemByName("RammingAttack");
		$fireOrderType = $jumpFailure ? 'JumpFailure' : 'HyperspaceJump';
		$pubNotes = $jumpFailure
			? " attempts to jump to hyperspace, but damage to the Jump Drive causes the ship to be destroyed (" . $missingHealthPercentage . "% chance of failure)."
			: " successfully jumps to hyperspace (" . $missingHealthPercentage . "% chance of failure).";
	
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

	public function onIndividualNotesLoaded($gamedata){
		foreach ($this->individualNotes as $currNote) {			    	
			//Insert the noteValue (e.g. combatValue when ship jumped) in appropriate variable
			$this->preJumpValue = $currNote->notevalue;
		}				
	}//endof onIndividualNotesLoaded


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
        $this->data["Special"] = "<br>Boost in Initial Orders to jump to hyperspace at end of turn.";	
        $this->data["Special"] .= "<br>WARNING - Jumping to hyperspace REMOVES ship from rest of the battle.";
        $this->data["Special"] .= "<br>If Jump Engine is damaged, ship has a % chance of being destroyed opening jump point.";
        $this->data["Special"] .= "SHOULD NOT be shut down for power (unless damaged >50% or if Desperate rules apply).";									
		parent::setSystemDataWindow($turn);     
    }
}


class Structure extends ShipSystem{
    public $name = "structure";
    public $displayName = "Structure";
	private $isIndestructible = false;
    
	//Structure is last to be repaired, except purely cosmetic systems like Hanngars  
	public $repairPriority = 2;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
    
    function __construct($armour, $maxhealth, $isIndestructible = false){
        parent::__construct($armour, $maxhealth, 0, 0);
		$this->isIndestructible = $isIndestructible;
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
	
} //endof Structure	

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
		    
		if($gamedata->turn<=2){ //HKs should start in hangars; instead, they will get additional Ini penalty on turn 1 and 2
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
        $strippedSystem->allocatedBFCP = $this->allocatedBFCP;
        $strippedSystem->BFCPtotal_used = $this->BFCPtotal_used;
		
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
        $strippedSystem->allocatedSpec = $this->allocatedSpec;
        $strippedSystem->availableSpec = $this->availableSpec;      
      	$strippedSystem->currSelectedSpec = $this->currSelectedSpec;		        
      	$strippedSystem->currAllocatedSpec = $this->currAllocatedSpec;        
        $strippedSystem->specTotal_used = $this->specTotal_used;       
        $strippedSystem->specAllocatedCount = $this->specAllocatedCount;      
        $strippedSystem->specDecreased = $this->specDecreased;
        $strippedSystem->specIncreased = $this->specIncreased;                             		
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
        $strippedSystem->allocatedAA = $this->allocatedAA;
        $strippedSystem->availableAA = $this->availableAA;
        $strippedSystem->currchangedAA = $this->currchangedAA;
        $strippedSystem->AAtotal_used = $this->AAtotal_used;
        $strippedSystem->AApreallocated_used = $this->AApreallocated_used;
		
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
	
	public function absorbDamage($ship,$gamedata,$value){ //or dissipate, with negative value
		$damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $this->id, $value, 0, 0, -1, false, false, "Absorb/Dissipate!", "Tendril");
		$damageEntry->updated = true;
		$this->damage[] = $damageEntry;
	}
	
		public function stripForJson(){
			$strippedSystem = parent::stripForJson();
			$strippedSystem->outputDisplay = $this->outputDisplay; //make sure that actual output is actually sent to front end...				
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
	
	public function absorbDamage($ship,$gamedata,$value){ //or dissipate, with negative value
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
			$mostSuitableTendril->absorbDamage($target,$gamedata,$damageAbsorbed);
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
	
	
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);  
		//some effects should originally work for current turn, but it won't work with FV handling of ballistics. Moving everything to next turn.
		//it's Ion (not EM) weapon with no special remarks regarding advanced races and system - so works normally on AdvArmor/Ancients etc
		$this->data["Repair points (used/max)"] = $this->usedRepairPoints . "/" . $this->maxRepairPoints;
		$this->data["Special"] = "At end of turn phase automatically repairs damage to vessel. Cannot repair destroyed structure blocks.";      
		//$this->data["Special"] .= "<br>Priority: first fix criticals, then damaged systems, finally restore destroyed systems.";  
		$this->data["Special"] .= "<br>Priority: first fix criticals, then revive destroyed systems, finally restore boxes to damaged systems.";  
		$this->data["Special"] .= "<br>Core (and other particularly important) systems are repaired first, then weapons, then other systems.";
		$this->data["Special"] .= "<br>Will not fix criticals and damage caused in current turn.";
		$this->data["Special"] .= "<br>Player may modify repair priorities - click self repair system in Initial phase and cycle through damaged systems.";
	}

	
	/* sorts system for repair priority*/
    public static function sortSystemsByRepairPriority($a, $b){
		/* being destroyed modifies priority instead
		$aDestroyed = $a->isDestroyed();
		$bDestroyed = $b->isDestroyed();
		//destroyed systems are fixed first!
		if(($aDestroyed == true) && ($bDestroyed == false)){
			return -1;
		}else if(($aDestroyed == false) && ($bDestroyed == true)){
			return 1;
		}
		*/
		//priority, then size (smaller first, as easier to repair), then ID!
		if($a->repairPriority!==$b->repairPriority){ 
            return $b->repairPriority - $a->repairPriority; //higher priority first!
        }else if($a->maxhealth!==$b->maxhealth){ 
            return $a->maxhealth - $b->maxhealth; //smaller first!
        }else return $a->id - $b->id;
    } //endof function sortSystemsByRepairPriority
	/* sorts critical hits for repair priority*/
    public static function sortCriticalsByRepairPriority($a, $b){
		//priority, then size (smaller first, as easier to repair), then ID!
		if($a->repairPriority!==$b->repairPriority){ 
            return $b->repairPriority - $a->repairPriority; //higher priority first!
        }else if($a->repairCost!==$b->repairCost){ ///costlier first!
            return $b->repairCost - $a->repairCost; //costlier first!
        }else return $a->id - $b->id;
    } //endof function sortSystemsByRepairPriority
	
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
      	
      	return $effectiveoutput; 
		}		
	
	public function criticalPhaseEffects($ship, $gamedata)
    { 
    
		parent::criticalPhaseEffects($ship, $gamedata);//Call parent to apply effects like Limpet Bore.	  
    
		if($this->isDestroyed()) return; //destroyed system does not work... but other critical phase effects may work even if destroyed!
		
		//how many points are available?
		$availableRepairPoints = $this->maxRepairPoints - $this->usedRepairPoints;
		$availableRepairPoints = min($availableRepairPoints,$this->getEffectiveOutput($ship)); //no more than remaining points, no more than actual system repair capability	
		
		//sort all systems by priority
		$ship=$this->getUnit();
		$systemList = array();
		foreach($ship->systems as $system){			
			if ( $system->maxhealth <= $system->getRemainingHealth() ) continue; //skip undamaged systems...
			//priority overrides...
			if(array_key_exists($system->id, $this->priorityChanges) && ($this->priorityChanges[$system->id]>=0)){
				$system->repairPriority = $this->priorityChanges[$system->id];
			}			
			//skip systems attached to destroyed structure blocks...
			if($system->repairPriority<1) continue;//skip systems that cannot be repaired
			if(!($system instanceOf Structure)){ //non-Structure system - cannot repair if attached to destroyed Structure block
				$strBlock = $ship->getStructureSystem($system->location);
				if($strBlock->isDestroyed($gamedata->turn)) continue;
			}else{ //Structure block - cannot repair if destroyed
				if($system->isDestroyed($gamedata->turn)) continue; //cannot repair destroyed Structure
			}
			
			//destroyed systems get first priority
			if( ($system->repairPriority <=10) //only systems whose priority wasn't modified yet
				&& ($system->isDestroyed($gamedata->turn))
			){
				$system->repairPriority += 10;
			}
			
			$systemList[] = $system;			
		}
		usort($systemList, [self::class, 'sortSystemsByRepairPriority']);
		

// Add GTS		
		//repair criticals (on non-destroyed systems only; also, skip criticals generated this turn!)
        $critList = array();
        foreach ($ship->systems as $systemToRepair){//crit fixing may be necessary even on technically undamaged systems
            if ($availableRepairPoints<1) break;//cannot repair anything
            if ($systemToRepair->repairPriority<1) continue;//skip systems that cannot be repaired
            if ($systemToRepair->isDestroyed($gamedata->turn)) continue;//don't repair criticals on destroyed system...

            foreach($systemToRepair->criticals as $critDmg) {
                if($critDmg->repairPriority<1) continue;//if critical cannot be repaired
                if($critDmg->turn >= $gamedata->turn) continue;//don't repair criticals caused in current (or future!) turn
                if ($critDmg->oneturn || ($critDmg->turnend > 0)) continue;//temporary criticals (or those already repaired) also cannot be repaired
                if($critDmg->repairPriority<10) $critDmg->repairPriority += $systemToRepair->repairPriority; //modify priority by priority of system critical is on! 
                $critList[] = $critDmg;
            }
        }
		$noOfCrits = count($critList);
        if($noOfCrits>0){
			usort($critList, [self::class, 'sortCriticalsByRepairPriority']);
			
            foreach ($critList as $critDmg){ //repairable criticals of current system, already sorted
                if ($critDmg->repairCost <= $availableRepairPoints){//execute repair!
                    $system = $ship->getSystemById($critDmg->systemid); //We already have the ship object passed to criticalPhaseEffects(), use it to get the system the foreach loop is considering at this point'
                    $system->repairCritical($critDmg, $gamedata->turn); // Call our new function in shipSystem class, passing details of the particular critical we're considering, plus $gamedata->turn as it's also needed.
                    $availableRepairPoints -= $critDmg->repairCost; //Keep these two lines  here, as they amend variable in THIS function (see above)!
                    $this->usedThisTurn += $critDmg->repairCost; 
                    //End of work, move onto next critical if there is one.
                } 
            }
        }
// End add GTS
		
		
		//repair damaged/destroyed systems, possibly undestroying them in the process (cannot repair destroyed Structure and systems attached to it - but this is taken care at the stage of preparing list of repairable systems)
		foreach ($systemList as $systemToRepair){
			if ($availableRepairPoints<1) continue;//cannot repair anything any longer
			$currentDamage = $systemToRepair->maxhealth - $systemToRepair->getRemainingHealth( );
			$causedThisTurn = $systemToRepair->damageReceivedOnTurn($gamedata->turn);
			$toBeRepaired = $currentDamage-$causedThisTurn;
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
			}
		}	
			
			
		/*rehearsed above
		//repair criticals (on non-destroyed systems only; also, skip criticals generated this turn!)
		//foreach ($systemList as $systemToRepair){
		foreach ($ship->systems as $systemToRepair){//crit fixing may be necessary even on technically undamaged systems	
			if ($availableRepairPoints<1) break;//cannot repair anything
			if ($systemToRepair->repairPriority<1) continue;//skip systems that cannot be repaired
			if ($systemToRepair->isDestroyed($gamedata->turn)) continue;//don't repair criticals on destroyed system...
			$critList = array();
			foreach($systemToRepair->criticals as $critDmg) {
				if($critDmg->repairPriority<1) continue;//if critical cannot be repaired
				if($critDmg->turn >= $gamedata->turn) continue;//don't repair criticals caused in current (or future!) turn
				if ($critDmg->oneturn || ($critDmg->turnend > 0)) continue;//temporary criticals (or those already repaired) also cannot be repaired
				$critList[] = $critDmg;				
			}			
			$noOfCrits = count($critList);
			if($noOfCrits>0){
				usort($critList, "self::sortCriticalsByRepairPriority");
				foreach ($critList as $critDmg){ //repairable criticals of current system
					if ($critDmg->repairCost <= $availableRepairPoints){//execute repair!
						$critDmg->turnend = $gamedata->turn;//actual repair ;)
						$critDmg->forceModify = true; //actually save the repair...
						$critDmg->updated = true; //actually save the repair cd!...
						$availableRepairPoints -= $critDmg->repairCost;
						$this->usedThisTurn += $critDmg->repairCost;
					}
				}
			}
		}	
		*/	
			
		/*old version - separate loops for destroyed and damaged systems
		//repair destroyed systems, possibly undestroying them in the process (cannot repair destroyed Structure)
		foreach ($systemList as $systemToRepair){
			if ($availableRepairPoints<1) continue;//cannot repair anything any longer
			if ($systemToRepair instanceOf Structure) continue; //cannot repair destroyed Structure
			$currentDamage = $systemToRepair->maxhealth - $systemToRepair->getRemainingHealth( );
			if($currentDamage > 0){ //do repair!
				$toBeFixed = min($currentDamage, $availableRepairPoints);
				$undestroy = false;
				if ($toBeFixed==$currentDamage){ //full health restored!
					$undestroy=true;
				}
				//actual healing entry
				$damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $systemToRepair->id, -$toBeFixed, 0, 0, -1, false, $undestroy, 'SelfRepair', 'SelfRepair');
				$damageEntry->updated = true;
				$systemToRepair->damage[] = $damageEntry;
				//meark repair points used
				$availableRepairPoints -= $toBeFixed;
				$this->usedThisTurn += $toBeFixed;
			}
		}	
		
		
		//repair damaged systems
		foreach ($systemList as $systemToRepair){
			if ($availableRepairPoints<1) continue;//cannot repair anything any longer
			//structure is okay - at apprpriate priority - as revivable systems were revived already
			//if ($systemToRepair instanceOf Structure) continue; //let's repair destroyed systems first, then go for damaged Structure
			if ($systemToRepair->isDestroyed($gamedata->turn)) continue;//don't repair damage on destroyed system... yet!
			$currentDamage = $systemToRepair->maxhealth - $systemToRepair->getRemainingHealth( );
			if($currentDamage > 0){ //do repair!
				$toBeFixed = min($currentDamage, $availableRepairPoints);
				//actual healing entry
				$damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $systemToRepair->id, -$toBeFixed, 0, 0, -1, false, false, 'SelfRepair', 'SelfRepair');
				$damageEntry->updated = true;
				$systemToRepair->damage[] = $damageEntry;
				//meark repair points used
				$availableRepairPoints -= $toBeFixed;
				$this->usedThisTurn += $toBeFixed;
			}
		}	
		*/

		/* separate block not necessary when repair happens AFTER reviving
		//repair damaged Structure
		foreach ($systemList as $systemToRepair){
			if ($availableRepairPoints<1) continue;//cannot repair anything any longer
			if (!($systemToRepair instanceOf Structure)) continue; //now it's Structure exclusively
			if ($systemToRepair->isDestroyed($gamedata->turn)) continue;//cannot repair destroyed Structure
			$currentDamage = $systemToRepair->maxhealth - $systemToRepair->getRemainingHealth( );
			if($currentDamage > 0){ //do repair!
				$toBeFixed = min($currentDamage, $availableRepairPoints);
				//actual healing entry
				$damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $systemToRepair->id, -$toBeFixed, 0, 0, -1, false, false, 'SelfRepair', 'SelfRepair');
				$damageEntry->updated = true;
				$systemToRepair->damage[] = $damageEntry;
				//meark repair points used
				$availableRepairPoints -= $toBeFixed;
				$this->usedThisTurn += $toBeFixed;
			}
		}	
		*/
		
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
        $strippedSystem->priorityChanges = $this->priorityChanges;	
        return $strippedSystem;
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
    	
    public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
		$this->data["Special"] .= 'If damaged while half-phasing - entire ship is destroyed.';
    }
	
	//destroy ship if damaged while half-phaseing
	public function criticalPhaseEffects($ship, $gamedata)
    { 
    
		parent::criticalPhaseEffects($ship, $gamedata);//Call parent to apply effects like Limpet Bore.	    
    
		if (!Movement::isHalfPhased($ship, $gamedata->turn)) return;
		if (!$this->isDamagedOnTurn($gamedata->turn)) return; 
		
	
		//try to make actual attack to show in log - use Ramming Attack system!				
		$rammingSystem = $ship->getSystemByName("RammingAttack");
		if($rammingSystem){ //actually exists! - it should on every ship!				
			$newFireOrder = new FireOrder(
				-1, "normal", $ship->id, $ship->id,
				$rammingSystem->id, -1, $gamedata->turn, 1, 
				100, 100, 1, 1, 0,
				0,0,'HalfPhase',10000
			);
			$newFireOrder->pubnotes = "Phasing drive damaged during half-phasing - ship destroyed.";
			$newFireOrder->addToDB = true;
			$rammingSystem->fireOrders[] = $newFireOrder;
		}else{
			$newFireOrder=null;
		}

		//destroy primary structure
		$primaryStruct = $ship->getStructureSystem(0);
		if($primaryStruct){			
            $remaining = $primaryStruct->getRemainingHealth();
            $damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $primaryStruct->id, $remaining, 0, 0, -1, true, false, "", "HalfPhase");
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
        $this->data["Special"] = "You can double power generation (and self-repair) by clicking 'Select', at the cost of deactivating all weapons and shields this turn.";	  			   
		if ($this->boostable){
			$this->data["Special"] .= "<br>In addition, you may open ship petals by boosting this system with '+', increasing generation by 50% on the following turn - however all primary systems lose 2 Armour and Defence Profiles increase 5% for the current turn.";
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
		$strippedSystem->individualNotesTransfer = $this->individualNotesTransfer;
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
				$totalShieldsRating += $system->baseRating;
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
		$this->data["Special"] = "Regenerates " . $this->getRegenforNotes($turn) . " health split eqaully amongst all Thirdspace Shields at the end of each turn.";
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
				$totalShieldRating += $system->baseRating;
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
			$maxRegenThisTurn = $shield->baseRating - $shield->getRemainingCapacity(); //Amount between health and baseRating.
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
			    $remainingCapacity = $shield->baseRating - $shield->getRemainingCapacity(); // Calculate remaining capacity.				

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
        $strippedSystem->shieldPresets = $this->shieldPresets;
        $strippedSystem->storedCapacity = $this->storedCapacity;       
        $strippedSystem->presetCurrClass = $this->presetCurrClass;  
		
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
        $strippedSystem->shieldPresets = $this->shieldPresets;
        $strippedSystem->storedCapacity = $this->storedCapacity;       
        $strippedSystem->presetCurrClass = $this->presetCurrClass;  
		
        return $strippedSystem;
    }
							
} //endof ThoughtShieldGenerator

//Mindrider Hangar operates as way to keep track of how many Thought Projections Mindriders can have in play.
class MindriderHangar extends ShipSystem{
    public $name = "MindriderHangar";
    public $displayName = "Hangar";
    public $primary = true;
    public $iconPath = "hangar.png";
    
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
					$this->data["Special"] = "Jammer ability, even against Ancients.";
					$this->data["Special"] .= "<br>Can use 'Shading Mode' by activating this system during Deployment/Pre-Turn Phase.";						
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
			
		}	
	}			

		public function onIndividualNotesLoaded($gamedata){
			//Sort notes by turn, and then phase so latest detection note is always last.
			$this->sortNotes();
			foreach ($this->individualNotes as $currNote){ //Search all notes, they should be process in order so the latest event applies.
				switch($currNote->notekey){
					case 'detected': 
						$this->detected = true;
					break;
					case 'undetected': 
						$this->detected = false;						
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

				//If we're checking during DeploymentGamePhase->Advance (actually Phase 1 at this point).					
				if ($this->active) {
					if ($this->isDetected($ship, $gamedata, $range)) {
						$notekey   = 'detected';
						$noteHuman = $noteHuman1;
						$noteValue = 1;							
					} else {
						$notekey   = 'undetected';
						$noteHuman = $noteHuman2;
						$noteValue = 1;							
					}
				} else {
					$notekey   = 'detected';
					$noteHuman = $noteHuman3; //Not shaded yet or was shaded and then turned off.
					$noteValue = 0;						
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


		private function isDetected($ship, $gameData, $range) {
	
			$blockedHexes = $gameData->getBlockedHexes(); //Just do this once outside loop
			$pos = $ship->getHexPos(); //Just do this once outside loop	

			foreach ($gameData->ships as $otherShip) {
				// Skip friendly ships
				if($otherShip->team === $ship->team) continue;
				if($otherShip->isTerrain()) continue; //Ignore Terrain
				if($otherShip->isDestroyed()) continue; //Ignore destroyed enemy ships.

				// If within detection range, and LoS not blocked the ship is detected
				// Get distance to the stealth ship and check line of sight
				$distance = mathlib::getDistanceHex($ship, $otherShip);
				$otherPos = $otherShip->getHexPos(); //Just deployed ships aren't counting for this.          
				$noLoS = !empty($blockedHexes) && Mathlib::isLoSBlocked($pos, $otherPos, $blockedHexes);

				// If within detection range, and LoS not blocked the ship is detected
				if($distance <= $range && !$noLoS){
					return true;
				}		
			}

			return false;			
		}	

		public function stripForJson(){
			$strippedSystem = parent::stripForJson();
			$strippedSystem->detected = $this->detected;
			$strippedSystem->active = $this->active;				        
			return $strippedSystem;
		}

	} //endof ShadingField



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
		$strippedSystem->remainingAmmo = $this->remainingAmmo;
		$strippedSystem->ammoCountArray = $this->ammoCountArray;
		$strippedSystem->ammoSizeArray = $this->ammoSizeArray;
		$strippedSystem->output = $this->output;	
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
	public function canDrawAmmo($modeName){
		
	    // Check if ammo count has a value of 1 or more after ammo used this turn deducted
	    	if(($this->remainingAmmo > 0) && (($this->ammoCountArray[$modeName] - $this->interceptorUsed) >= 1)){    	
	        return true; // drawing ammo is possible
	    } else {
	        return false; // cannot draw ammo!
	    }
	}
	
	//Reduce number of Interceptor missile when one is ordered to intercept.
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
	public $distanceRangeMod = 10; //MODIFIER for distance range
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
	public $distanceRangeMod = -10; //MODIFIER for distance range
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
	public $modeName = 'D - Light';
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


?>
