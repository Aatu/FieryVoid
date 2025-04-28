<?php

    class Gravitic extends Weapon{
    	public $damageType = "Raking"; 
    	public $weaponClass = "Gravitic"; 

	    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
        }

    } //endof class Gravitic


    
/*Marcin Sawicki: as longer recharge time was highly troublesome, I have thrown in cooldown periods instead (but +1 turn)*/
/*30.09.2020: I have looked at original wording of Pulsar, and it says cooldown. So NO +1 turn! */
    class GravitonPulsar extends Pulse
    {
        public $name = "gravitonPulsar";
        public $displayName = "Graviton Pulsar";
        public $animation = "bolt";
        public $animationColor = array(99, 255, 00);
	    /*
        public $trailColor = array(99, 255, 00);
        public $projectilespeed = 12;
        public $animationWidth = 3;
        public $animationExplosionScale = 0.15;
	*/
	    
        public $boostable = true;
        public $boostEfficiency = 2;
        public $maxBoostLevel = 2;
        public $loadingtime = 1;
        public $maxpulses = 3;
        public $priority = 4;
		
        public $rangePenalty = 1;
        public $fireControl = array(4, 2, 2); // fighters, <mediums, <capitals 
        public $intercept = 1;
	    
        public $grouping = 20;

	    //private $useDie = 3; //die used for base number of hits - here it will not be used!
	public $damageType = 'Pulse'; 
    	public $weaponClass = "Gravitic"; 
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
	    $this->data["Special"] = "Standard power: D2 pulses, +1/20%, max 3; intercept -5; 1/turn";
	    $this->data["Special"] .= "<br>Double power: D3+1 pulses, +1/20%, max 4; intercept -10; cooldown 1 turns";
	    $this->data["Special"] .= "<br>Triple power: D3+2 pulses, +1/20%, max 5; intercept -15; cooldown 2 turns and forced critical";
            $this->defaultShots = $this->getMaxPulses($turn);
            $this->normalload = $this->loadingtime;

            //$this->shots = $this->defaultShots;
            $this->setTimes();
            
            switch($this->getBoostLevel($turn)){
                case 0:
                    $this->maxpulses = 3;
                    break;
                case 1:
                    $this->maxpulses = 4;
                    break;
                case 2:
                    $this->maxpulses = 5;
                    break;
            }            
        }
        
        public function getLoadingTime(){
			return $this->loadingtime;
        }

        public function getTurnsloaded(){
			return $this->turnsloaded;
        }
        
        public function setTimes(){
			$this->loadingtime = 1;
			$this->turnsloaded = 1;
			$this->normalload = 1;
        }
        
        protected function getPulses($turn)
        {
            switch($this->getBoostLevel($turn)){
                case 0:
                    return Dice::d(2);
                    break;
                case 1:
                    return (Dice::d(3)+1);
                    break;
                case 2:
                    return (Dice::d(3)+2);
                    break;
            }            
        }

	protected function applyCooldown($gamedata){
		$currBoostlevel = $this->getBoostLevel($gamedata->turn);
		//if boosted, cooldown (1 or 2 tuns)
		if($currBoostlevel > 0){
			$cooldownLength = $currBoostlevel ;
			$finalTurn = $gamedata->turn + $cooldownLength;
			$crit = new ForcedOfflineForTurns(-1, $this->unit->id, $this->id, "ForcedOfflineForTurns", $gamedata->turn, $finalTurn);
			$crit->updated = true;
			$this->criticals[] =  $crit;
		} 
	}
    
	public function fire($gamedata, $fireOrder){
	    $currBoostlevel = $this->getBoostLevel($gamedata->turn);
		$this->maxpulses = $this->getMaxPulses($gamedata->turn);
		$this->setTimes();
					
		parent::fire($gamedata, $fireOrder);
		// If fully boosted: test for possible crit.
		if($currBoostlevel === $this->maxBoostLevel){
			$crits = array();
			$shooter = $gamedata->getShipById($fireOrder->shooterid);
			$crits = $this->testCritical($shooter, $gamedata, $crits);
		}
	    $this->applyCooldown($gamedata);
	}
	    
	/* applying cooldown when firing defensively, too
	*/
	public function fireDefensively($gamedata, $interceptedWeapon)
	{
		if ($this->firedDefensivelyAlready==0){ //in case of multiple interceptions during one turn - suffer backlash only once
			$this->applyCooldown($gamedata);	
		}
		parent::fireDefensively($gamedata, $interceptedWeapon);
	}

	public function getNormalLoad(){
		return $this->loadingtime + $this->maxBoostLevel;
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

	private function getMaxPulses($turn){
		return 3 + $this->getBoostLevel($turn);
	}

	public function getInterceptRating($turn){
		return 1 + $this->getBoostLevel($turn);            
	}
	
	public function getDamage($fireOrder){        return 10;   }
	public function setMinDamage(){     $this->minDamage = 10 ;      }
	public function setMaxDamage(){     $this->maxDamage = 10 ;      }
} //endof class GravitonPulsar


/*Marcin Sawicki: as longer recharge time was highly troublesome, I have thrown in cooldown periods instead (but +1 turn)*/
/*30.09.2020: I have looked at original wording of Pulsar, and it says cooldown. So NO +1 turn! */
class GraviticBolt extends Gravitic
    {
        public $name = "graviticBolt";
        public $displayName = "Gravitic Bolt";
        public $animation = "bolt";
        public $animationColor = array(99, 255, 00);
	/*
        public $trailColor = array(99, 255, 00);
        public $projectilespeed = 12;
        public $animationWidth = 3;
        public $animationExplosionScale = 0.20;
	*/
        public $boostable = true;
        public $boostEfficiency = 2;
        public $maxBoostLevel = 2;
        public $loadingtime = 1;
        public $curDamage = 9;
        public $priority = 4;
		
        public $rangePenalty = 1;
        public $fireControl = array(4, 2, 2); // fighters, <mediums, <capitals 
        public $intercept = 1;
        
	public $damageType = 'Standard'; 
    	public $weaponClass = "Gravitic"; 
	    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
	    $this->data["Special"] = "Standard power: 9 damage, intercept -5,  no cooldown";
	    $this->data["Special"] .= "<br>Double power: 12 damage, intercept -10, cooldown 1 turns";
	    $this->data["Special"] .= "<br>Triple power: 15 damage, intercept -15, cooldown 2 turns and forced critical";

        
            switch($this->getBoostLevel($turn)){
                case 0:
                    $this->data["Damage"] = '9';
                    break;
                case 1:
                    $this->data["Damage"] = '12';
                    break;
                case 2:
                    $this->data["Damage"] = '15';
                    break;
                default:
                    $this->data["Damage"] = '9';
                    break;
            }            
            
            $this->curDamage = $this->getCurDamage($turn);
            
            parent::setSystemDataWindow($turn);
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
        
        private function getCurDamage($turn){
            $dam = 9;
            
            switch($this->getBoostLevel($turn)){
                case 1:
                    $dam = 12;
                    break;
                case 2:
                    $dam = 15;
                    break;
                default:
                    break;
            }            
            
            return $dam;
        }

        public function getInterceptRating($turn){
            return 1 + $this->getBoostLevel($turn);            
        }
        
        public function getNormalLoad(){
            return $this->loadingtime + $this->maxBoostLevel;
        }
        
        public function getLoadingTime(){
			return $this->loadingtime;
        }

        public function getTurnsloaded(){
			return $this->turnsloaded;
        }        

	protected function applyCooldown($gamedata){
		$currBoostlevel = $this->getBoostLevel($gamedata->turn);
		
		if($currBoostlevel > 0){
			$cooldownLength = $currBoostlevel ;
			$finalTurn = $gamedata->turn + $cooldownLength;
			$crit = new ForcedOfflineForTurns(-1, $this->unit->id, $this->id, "ForcedOfflineForTurns", $gamedata->turn, $finalTurn);
			$crit->updated = true;
			$this->criticals[] =  $crit;
		}
	}	
	    
        public function fire($gamedata, $fireOrder){
		$currBoostlevel = $this->getBoostLevel($gamedata->turn);
            $this->setTimes();
                        
            parent::fire($gamedata, $fireOrder);
		
            // If fully boosted: test for possible crit.
            if($currBoostlevel === $this->maxBoostLevel){
            	$this->forceCriticalRoll = true;
            }
		
		$this->applyCooldown($gamedata);
        }
	    
	    /* applying cooldown when firing defensively, too
	    */
	    public function fireDefensively($gamedata, $interceptedWeapon)
	    {
		if ($this->firedDefensivelyAlready==0){ //in case of multiple interceptions during one turn - suffer backlash only once
			$this->applyCooldown($gamedata);	
		}
		parent::fireDefensively($gamedata, $interceptedWeapon);
	    }

        public function setTimes(){		
                $this->loadingtime = 1;
                $this->turnsloaded = 1;
                $this->normalload = 1;
        }
        
        public function getDamage($fireOrder){        return $this->getCurDamage($fireOrder->turn);   }
        public function setMinDamage(){  $this->minDamage = $this->curDamage ;      }
        public function setMaxDamage(){  $this->maxDamage = $this->curDamage ;      }
    }//endof GraviticBolt
    


    class GravitonBeam extends Raking{
        public $name = "gravitonBeam";
        public $displayName = "Graviton Beam";
        public $animation = "laser";
        public $animationColor = array(99, 255, 00);
        //public $animationWidth = 4;
        //public $animationWidth2 = 0.4;
        public $priority = 7;
        
        public $loadingtime = 4;
        public $raking = 10;
        
        public $rangePenalty = 0.25;
        public $fireControl = array(-5, 2, 3); // fighters, <mediums, <capitals 
	    
	public $damageType = 'Raking'; 
    	public $weaponClass = "Gravitic"; 	    
    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
        }
        
        public function getDamage($fireOrder){   return Dice::d(10, 5)+12;   }
        public function setMinDamage(){   return  $this->minDamage = 17 ;      }
        public function setMaxDamage(){   return  $this->maxDamage = 62 ;      }
    }



    class GraviticCannon extends Gravitic
    {
        public $name = "graviticCannon";
        public $displayName = "Gravitic Cannon";
        public $animation = "trail";
        public $animationColor = array(99, 255, 00);
	    /*
        public $trailColor = array(99, 255, 00);
        public $projectilespeed = 15;
        public $animationWidth = 2;
        public $animationExplosionScale = 0.15;
	*/
        public $loadingtime = 1;
        public $priority = 5;

	public $damageType = 'Standard'; 
    	public $weaponClass = "Gravitic"; 
		
        public $rangePenalty = 0.33;
        public $fireControl = array(-1, 2, 2); // fighters, <mediums, <capitals 
        public $intercept = 1;
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return Dice::d(10)+6;;   }
        public function setMinDamage(){  $this->minDamage = 7 ;      }
        public function setMaxDamage(){  $this->maxDamage = 16 ;      }
    }



/*fighter weapon*/
    class LightGraviticBolt extends LinkedWeapon{
        public $name = "lightGraviticBolt";
        public $displayName = "Light Gravitic Bolt";
        public $iconPath = "lightGraviticBolt.png";
        public $animation = "trail";
        public $animationColor = array(99, 255, 00);
	    /*
        public $trailColor = array(99, 255, 00);
        public $animationExplosionScale = 0.10;
        public $projectilespeed = 12;
        public $animationWidth = 2;
        public $trailLength = 10;
*/

        public $intercept = 2;
        public $loadingtime = 1;
        public $shots = 2;
        public $defaultShots = 2;

        public $rangePenalty = 2;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals
        private $damagebonus = 0;
        
        public $priority = 4; //fixed 7 damage is more or less equivalent of standard d6+4

	public $damageType = 'Standard'; 
    	public $weaponClass = "Gravitic"; 

        function __construct($startArc, $endArc, $damagebonus, $shots = 2){
            $this->shots = $shots;
            $this->defaultShots = $shots;
		$this->intercept = $shots;
            
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }
        

        public function getDamage($fireOrder){        return 7;   }
        public function setMinDamage(){     $this->minDamage = 7 ;      }
        public function setMaxDamage(){     $this->maxDamage = 7 ;      }

    }



    class UltraLightGraviticBolt extends LinkedWeapon{
        public $name = "ultraLightGraviticBolt";
        public $displayName = "Ultra Light Gravitic Bolt";
        public $iconPath = "lightGraviticBolt.png";
        public $animation = "trail";
        public $animationColor = array(99, 255, 00);
	    /*
        public $trailColor = array(99, 255, 00);
        public $animationExplosionScale = 0.10;
        public $projectilespeed = 12;
        public $animationWidth = 2;
        public $trailLength = 10;
*/
	    
        public $intercept = 2;
        public $loadingtime = 1;
        public $shots = 2;
        public $defaultShots = 2;

        public $rangePenalty = 2;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals
        private $damagebonus = 0;
        
        public $priority = 3; //definitely light!

		public $damageType = 'Standard'; 
    	public $weaponClass = "Gravitic"; 

        function __construct($startArc, $endArc, $damagebonus, $shots = 2){
            $this->shots = $shots;
            $this->defaultShots = $shots;
			$this->intercept = $shots;
            
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }        

        public function getDamage($fireOrder){        return 5;   }
        public function setMinDamage(){     $this->minDamage = 5 ;      }
        public function setMaxDamage(){     $this->maxDamage = 5 ;      }

    }


/*heavy fighter weapon*/
    class LightGravitonBeam extends Weapon{
        public $name = "lightGravitonBeam";
        public $displayName = "Light Graviton Beam";
        public $animation = "laser";
        public $animationColor = array(99, 255, 00);
        //public $animationWidth = 1;
        //public $animationWidth2 = 0;
        
        public $loadingtime = 3;
        public $raking = 10;
        public $exclusive = true;
        public $priority = 8; //Raking weapon
        
        public $rangePenalty = 1;
        public $fireControl = array(-5, 0, 0); // fighters, <mediums, <capitals 
 
		public $damageType = 'Raking'; 
		public $weaponClass = "Gravitic"; 
	    
	    
        function __construct($startArc, $endArc, $damagebonus){
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }
        
        
        public function getDamage($fireOrder){        return Dice::d(6, 5);   }
        public function setMinDamage(){   return  $this->minDamage = 5 ;      }
        public function setMaxDamage(){   return  $this->maxDamage = 30 ;      }
    }
    

/*new approach to Gravitic Lance, using new mode mechanism...*/
class GraviticLance extends Raking{
        public $name = "GraviticLance";
        public $displayName = "Gravitic Lance";
	    public $iconPath = "graviticLance.png";
	
	//visual display - will it be enough to ensure correct animations?...
	    public $animationArray = array(1=>'laser', 2=>'laser');
        public $animationColor = array(99, 255, 00);
	/*
        public $animationWidthArray = array(1=>6, 2=>4);
        public $animationWidth2 = 0.5;
        public $animationExplosionScale = 0.35;
	*/
	
	//actual weapons data
	    public $raking = 10; 
        public $priorityArray = array(1=>7, 2=>7, 3=>7);
        public $gunsArray = array(1=>1, 2=>2, 3=>2); //one Lance, but two Beam shots!
	    public $uninterceptableArray = array(1=>false, 2=>false, 3=>false);
	
        public $loadingtimeArray = array(1=>4, 2=>4, 3=>4); //mode 1 should be the one with longest loading time
        public $rangePenaltyArray = array(1=>0.2, 2=>0.25, 3=>0.25); //Lance -1/5 hexes, Beams -1/4 hexes
        public $fireControlArray = array( 1=>array(-5, 2, 3), 2=>array(-5, 2, 3), 3=>array(-5, 2, 3) ); // fighters, <mediums, <capitals 
	
	    public $firingModes = array(1=>'Lance (Sustained)', 2=>'Beams', 3=>'Split Beams');
	    public $damageTypeArray = array(1=>'Raking', 2=>'Raking', 3=>'Raking'); //indicates that this weapon does damage in Pulse mode
    	public $weaponClassArray = array(1=>'Gravitic', 2=>'Gravitic', 3=>'Gravitic'); //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!	
	
	    public $intercept = 0; 
	
        public $overloadable = false; //Not actually required and messes with stop overload button.
        public $alwaysoverloading = true;
        public $extraoverloadshots = 2;
        public $overloadturns = 4;

        public $canSplitShots = false; //Allows Firing Mode 2 to split shots.
		public $canSplitShotsArray = array(1=>false, 2=>false, 3=>true );

        private $sustainedTarget = array(); //To track for next turn which ship was fired at in Sustained Mode and whether it was hit.
        private $sustainedSystemsHit = array(); //For tracking systems that were hit and how much armour they should be reduced by following turn if hit again.         
		
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
			//maxhealth and power reqirement are fixed; left option to override with hand-written values
			if ( $maxhealth == 0 ){
				$maxhealth = 12;
			}
			if ( $powerReq == 0 ){
				$powerReq = 16;
			}
			parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
	
        public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			$this->data["Special"] = 'Can fire as either a Gravitic Lance (Sustained) or two Graviton Beams. ';
            $this->data["Special"] .= '<br>When firing in Sustained mode, if the first shot hits, the shot next turn hits automatically.';
            $this->data["Special"] .= '<br>Subsequent Sustained shots ignore any armour/shields that have applied to first shot.';
            $this->data["Special"] .= "<br>Can use 'Split Beams' Firing Mode to target different enemy units with Graviton Beams.";                                          
        }
	
        public function calculateHitBase(TacGamedata $gamedata, FireOrder $fireOrder) {
            //Check if this is a Sustained weapon firing, and therefore possible automatic hit. 
            // We only care if the overloaded weapon fired last turn and therefore has a targetid stored in sustainedTarget variable.
            if (
                $this->isOverloadingOnTurn($gamedata->turn) &&
                isset($this->sustainedTarget[$fireOrder->targetid]) &&
                $this->sustainedTarget[$fireOrder->targetid] == 1
            ) {
                $fireOrder->needed = 100; // Auto-hit!
                $fireOrder->updated = true;
                $this->uninterceptable = true;
                $this->doNotIntercept = true;
                $fireOrder->pubnotes .= " Sustained shot automatically hits.";

                return;
            }

            parent::calculateHitBase($gamedata, $fireOrder); // Default routine if not an auto-hit.
        }   
 

        public function generateIndividualNotes($gameData, $dbManager) {
            switch($gameData->phase) {
                case 4: // Post-Firing phase
                    $firingOrders = $this->getFireOrders($gameData->turn); // Get fire orders for this turn
                    if (!$firingOrders) {
                        break; // No fire orders, nothing to process
                    }

                    $ship = $this->getUnit(); // Ensure ship is defined before use

                    if($this->isDestroyed() || $ship->isDestroyed()) break;                    
        
                    foreach ($firingOrders as $firingOrder) { //Should only be 1.
                        $didShotHit = $firingOrder->shotshit; //1 or 0 depending on hit or miss.
                        $targetid = $firingOrder->targetid;

                        // Check for sustained mode condition
                        if ($this->isOverloadingOnTurn($gameData->turn) && $this->loadingtime <= $this->overloadturns) {
                            if (($this->overloadshots - 1) > 0) { // Ensure not the last sustained shot
                                $notekey = 'targetinfo';
                                $noteHuman = 'ID of Target fired at';
                                $notevalue = $targetid . ';' . $didShotHit;
                                $this->individualNotes[] = new IndividualNote(
                                    -1, TacGamedata::$currentGameID, $gameData->turn, $gameData->phase,
                                    $ship->id, $this->id, $notekey, $noteHuman, $notevalue
                                );
                            }
                        }
         
                        if ($didShotHit == 0) {
                            continue; // Shot missed, no need to track damage
                        }
      
                        // Process damage to target systems
                        $target = $gameData->getShipById($targetid);
                        if (!$target || !is_array($target->systems) || empty($target->systems)) {
                            continue; // Ensure valid target and systems exist
                        }

                        foreach ($target->systems as $system) {
                            $systemDamageThisTurn = 0;
                            $notes = 0; // Tracks how much armor should be ignored next turn
       
                            foreach ($system->damage as $damage) {
                               
                                if ($damage->turn == $gameData->turn){  // Only consider this turn’s damage
                                 
                                    if ($damage->shooterid == $ship->id && $damage->weaponid == $this->id) {

                                        $systemDamageThisTurn += $damage->damage; // Accumulate total damage dealt this turn
                                    }
                                }
                            }
            
                            if ($systemDamageThisTurn > 0) { // Ensure damage was dealt
                                if ($systemDamageThisTurn >= $system->armour) {
                                    $notes = $system->armour; // All armor used up
                                } else {
                                    $notes = $systemDamageThisTurn; // Partial armor penetration
                                }
         
                                // Create note(s) for armor ignored next turn
                                while ($notes > 0) {
                                    $notekey = 'systeminfo';
                                    $noteHuman = 'ID of System fired at';
                                    $notevalue = $system->id;
                                    $this->individualNotes[] = new IndividualNote(
                                        -1, TacGamedata::$currentGameID, $gameData->turn, $gameData->phase,
                                        $ship->id, $this->id, $notekey, $noteHuman, $notevalue
                                    );
                                    $notes--;
                                }
                            }
                        }
                    }
                    break;
            }
        } // end of function generateIndividualNotes


        public function onIndividualNotesLoaded($gamedata)
        {
            // Process rearrangements made by player					
            foreach ($this->individualNotes as $currNote) {
                if ($currNote->turn == $gamedata->turn - 1) { // Only interested in last turn’s notes               
                    if ($currNote->notekey == 'targetinfo') {
                        if (strpos($currNote->notevalue, ';') === false) {
                            continue; // Skip notes with invalid format
                        }
        
                        $explodedValue = explode(';', $currNote->notevalue);
                        if (count($explodedValue) === 2) { // Ensure correct format
                            $targetId = $explodedValue[0];
                            $didHit = $explodedValue[1];
        
                            $this->sustainedTarget[$targetId] = $didHit; // Store target ID and hit status
                        }
                    }
            
                    // Process armor reductions
                    if ($currNote->notekey == 'systeminfo') {
                        $this->sustainedSystemsHit[] = $currNote->notevalue; // Store system ID
                    }    
                }
            }				

            //and immediately delete notes themselves, they're no longer needed (this will not touch the database, just memory!)
            $this->individualNotes = array();
                   
        }//endof onIndividualNotesLoaded               

        //Called from core firing routines to check if any armour should be bypassed by a sustained shot.
        public function getsustainedSystemsHit()
        {
            if(!empty($this->sustainedSystemsHit) && is_array($this->sustainedSystemsHit)){
                return $this->sustainedSystemsHit; 
            } else{
                return null;
            }
        }    

        // Sustained shots only apply shield damage reduction once.
        public function shieldInteractionDamage($target, $shooter, $pos, $turn, $shield, $mod) {
            $toReturn = max(0, $mod);
            
            // Ensure sustainedTarget is set and not an empty array before checking its keys
            if (!empty($this->sustainedTarget) && is_array($this->sustainedTarget) && array_key_exists($target->id, $this->sustainedTarget)) {
                $toReturn = 0;
            }
            
            return $toReturn;
        }

        public function stripForJson(){
			$strippedSystem = parent::stripForJson();
			$strippedSystem->sustainedTarget = $this->sustainedTarget;	//Needed for front end hit calculation                      			
			return $strippedSystem;
		}  

        
        public function isOverloadingOnTurn($turn = null){
            return true;
        }
	
        public function getDamage($fireOrder){ 
		switch($this->firingMode){
			case 1:
				return Dice::d(10, 6)+24; //Lance
				break;
			case 2:
				return Dice::d(10, 5)+12; //Beam
				break;	
		}
	}
        public function setMinDamage(){ 
		switch($this->firingMode){
			case 1:
				$this->minDamage = 30; //Lance
				break;
			case 2:
				$this->minDamage = 17; //Beam
				break;	
		}
		$this->minDamageArray[$this->firingMode] = $this->minDamage;
	}
        public function setMaxDamage(){
		switch($this->firingMode){
			case 1:
				$this->maxDamage = 84; //Gravitic Lance
				break;
			case 2:
				$this->maxDamage = 62; //Graviton Beam
				break;	
		}
		$this->maxDamageArray[$this->firingMode] = $this->maxDamage;
	}	
	
} //endof GraviticLance


class GraviticCutter extends Raking
    {
        public $name = "graviticCutter";
        public $displayName = "Gravitic Cutter";
        public $animation = "laser";
        public $animationColor = array(99, 255, 00);	
        //public $animationWidth = 2;
        //public $animationWidth2 = 0.2;
        public $priority = 8;
        
        public $raking = 6;

        public $boostable = true;
        public $boostEfficiency = 5;
        public $maxBoostLevel = 1;
        public $loadingtime = 2;
		
        public $rangePenalty = 0.5;
        public $fireControl = array(-4, 2, 4); // fighters, <mediums, <capitals 
        
	public $damageType = 'Raking'; 
    	public $weaponClass = "Gravitic"; 
	    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){            
            if($this->getBoostLevel($turn)==0){
                $this->raking = 6;
            }
            else{
                $this->raking = 8;
            }            
            parent::setSystemDataWindow($turn);
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
		$this->data["Special"] .= 'Can be boosted for increased raw damage (13-40) and rake size (8). Requires a turn of cooldown if fired boosted.';		
        }
        
        
        public function fire($gamedata, $fireOrder){
	    $currBoostlevel = $this->getBoostLevel($gamedata->turn);
            if($this->getBoostLevel($fireOrder->turn)==0){
                $this->raking = 6;
            }
            else{
                $this->raking = 8;
            }		
                        
            parent::fire($gamedata, $fireOrder);
	    //if boosted, cooldown (1 turn)
	     if($currBoostlevel > 0){ //1 turn forced shutdown
		$crit = new ForcedOfflineOneTurn(-1, $fireOrder->shooterid, $this->id, "ForcedOfflineOneTurn", $gamedata->turn);
                $crit->updated = true;
                $this->criticals[] =  $crit;
	     }
        }

        public function getNormalLoad(){
            return $this->loadingtime + $this->maxBoostLevel;
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

        public function getDamage($fireOrder){
            if($this->getBoostLevel($fireOrder->turn)==0){
                $this->raking = 6;
                return (Dice::d(10, 2)+8);
            }
            else{
                $this->raking = 8;
                return (Dice::d(10, 3)+10);
            }
        }
        
        public function setMinDamage(){
            if($this->getBoostLevel(TacGameData::$currentTurn)==0){
                $this->minDamage = 10 ;
            }
            else{
                $this->minDamage = 13 ;
            }
        }
        
        public function setMaxDamage(){
            if($this->getBoostLevel(TacGameData::$currentTurn)==0){
                $this->maxDamage = 28 ;
            }
            else{
                $this->maxDamage = 40 ;
            }
        }
    } //endof GraviticCutter



?>
