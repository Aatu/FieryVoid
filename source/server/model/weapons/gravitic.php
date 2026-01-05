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
	    public $animationArray = array(1=>'laser', 2=>'laser', 3=>'laser');
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
        public $extraoverloadshotsArray = array(1=>2, 2=>0, 3=>0);       
        public $overloadturns = 4;
        public $overloadshots = 2;

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
			case 3:
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
			case 3:
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
			case 3:
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


class GraviticShifter extends Weapon implements SpecialAbility{
    public $name = "GraviticShifter";
    public $displayName = "Gravitic Shifter";
    public $animation = "bolt";
    public $animationColor = array(99, 255, 00);	
    public $animationExplosionScale = 0.3; //single hex explosion
    public $priority = 1;    
	public $specialAbilities = array("PreFiring");
	public $specialAbilityValue = true; //so it is actually recognized as special ability!  		
	public $damageType = "Standard"; //irrelevant, really
	public $weaponClass = "Gravitic";
	public $uninterceptable = true; //although I don't think a weapon exists that could intercept it...
	public $doNotIntercept = true; //although I don't think a weapon exists that could intercept it...
	public $loadingtime = 3;
    public $rangePenalty = 1;
    protected $canTargetAll = true; //Allows weapon to target allies AND enemies, pass to Front End in strpForJson()
	public $firingModes = array(
		1 => "Clockwise",
        2 => "Anti-Clockwise"
	);
    public $fireControl = array(-3, 3, 5); // fighters, <mediums, <capitals 
	public $preFires = true;
    public $specialHitChanceCalculation = true;			
	public $repairPriority = 6;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
	    
    protected $possibleCriticals = array(14 => "ReducedRange");
	private static $alreadyShifted = array();	

    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){

		//maxhealth and power reqirement are fixed; left option to override with hand-written values
		if ( $maxhealth == 0 ){
			$maxhealth = 6;
		}
		if ( $powerReq == 0 ){
			$powerReq = 5;
		}
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
    }
	

	public function getSpecialAbilityValue($args)
    {
		return $this->specialAbilityValue;
	}

     public function setSystemDataWindow($turn){
        $this->data["Special"] = "Used to change the facing of a target ship during Pre-Firing orders."; 
        $this->data["Special"] .= "<br>Select firing mode based on direction you wish to move ship and target a unit.";
        $this->data["Special"] .= "<br>Only ONE Gravitic Shifter can be used on a ship per turn, any other attempts will automatically miss.";           
        $this->data["Special"] .= "<br>Has -15% chance to hit Ancient enemy units, or those with Gravitic drives.";        
        $this->data["Special"] .= "<br>Can target allies.";
        $this->data["Special"] .= "<br>No effect on Enormous units.";	        	        		
		parent::setSystemDataWindow($turn);     
    }

	public function calculateHitBase($gamedata, $fireOrder)
	{       
		if (isset(GraviticShifter::$alreadyShifted[$fireOrder->targetid])){
            $fireOrder->needed = 0;
            $fireOrder->updated = true; 
            $fireOrder->pubnotes = "<br>Ship has already been affected by a Gravitic Shifter.";                         
            return; //target already engaged by a previous Gravitic Shifter
        }
            
        parent::calculateHitBase($gamedata, $fireOrder);
        
        GraviticShifter::$alreadyShifted[$fireOrder->targetid] = true; //Mark that a shot has been attempted against ship.

		$target = $gamedata->getShipById($fireOrder->targetid);
        $shooter = $this->getUnit();
        if($shooter->team !== $target->team){ //Let's make penalty only for enemy units
            if($target->gravitic || $target->factionAge >= 3){//Gravitic or Ancient
                $fireOrder->needed -= 15; //+15% chance to miss.
            }
        }    
	}    
        
    public function fire($gamedata, $fireOrder){                   
        parent::fire($gamedata, $fireOrder); 
		
        if($fireOrder->firingMode == 1){
            $direction = "clockwise";
        }else{
            $direction = "anti-clockwise";      
        }

        if($fireOrder->shotshit > 0){
            $fireOrder->pubnotes = "<br>Ship has been forced to turn 60 degrees " . $direction . " by a Gravitic Shifter.";                       
        }    
    }

    protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){
        if($ship->Enormous) return; //No effect on Enormous units

		$lastMove = $ship->getLastMovement();
        $newFacing = $lastMove->facing; //Initialise as current facing.
        $newHeading = $lastMove->heading; //Initialise as current heading. 

        if($fireOrder->firingMode == 1){
            $newFacing = MathLib::addToHexFacing($lastMove->facing , 1);
            $newHeading = MathLib::addToHexFacing($lastMove->heading , 1);
            //$type = "turnRight";
        }else{
            $newFacing = MathLib::addToHexFacing($lastMove->facing , -1);
            $newHeading = MathLib::addToHexFacing($lastMove->heading , -1);
            //$type = "turnLeft";                        
        }
		
		//Create new movement order for target.
        $shift = new MovementOrder(null, "prefire", new OffsetCoordinate($lastMove->position->q, $lastMove->position->r), 0, 0, $lastMove->speed, $newHeading, $newFacing, false, $gamedata->turn, $fireOrder->id, 0);

		//Add shifted movement order to database
		Manager::insertSingleMovement($gamedata->id, $ship->id, $shift);	
    }    

	public function getDamage($fireOrder){       return 0;   } //no actual damage
	public function setMinDamage(){     $this->minDamage = 0 ;      }
	public function setMaxDamage(){     $this->maxDamage = 0 ;      }

    public function stripForJson() {
        $strippedSystem = parent::stripForJson();    
        $strippedSystem->canTargetAll = $this->canTargetAll;	        											                                        
        return $strippedSystem;
	}	

    } //endof GraviticShifter    

class GravityNet extends Weapon implements SpecialAbility{
    public $name = "GravityNet";
    public $displayName = "Gravity Net";
    public $animation = "laser";
    public $animationColor = array(102, 255, 00);	
    public $animationExplosionScale = 1.0; //single hex explosion
    public $noProjectile = true;
    public $priority = 1; 
	public $hextarget = false; //Toggle to switch between hexTarget and normalTarget modes
	public $hidetarget = true;   
	public $specialAbilities = array("PreFiring");
	public $specialAbilityValue = true; //so it is actually recognized as special ability!  		
	public $damageType = "Standard"; //irrelevant, really
	public $weaponClass = "Gravitic";
	public $uninterceptable = true; //although I don't think a weapon exists that could intercept it...
	public $doNotIntercept = true; //although I don't think a weapon exists that could intercept it...
	public $loadingtime = 2;
    public $rangePenalty = 1;
    protected $canTargetAll = true; //Allows weapon to target allies AND enemies, pass to Front End in strpForJson()	
    public $fireControl = array(1, 2, 3); // fighters, <mediums, <capitals 
	public $preFires = true;
    public $canSplitShots = true;
    public $moveDistance = "";
    public $showHexagonArc = true; 
    public $specialHitChanceCalculation = true;			
	public $repairPriority = 6;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired

    public $firingModes = array(
		1 => "Standard",
        2 => "Priorty"
	);
	    
    protected $possibleCriticals = array(14 => "ReducedRange");

    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){

		//maxhealth and power reqirement are fixed; left option to override with hand-written values
		if ( $maxhealth == 0 ){
			$maxhealth = 8;
		}
		if ( $powerReq == 0 ){
			$powerReq = 5;
		}
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        GravityNetHandler::addGravityNet($this);//so all Targeting Array are accessible together.
    }

	public function getSpecialAbilityValue($args)
    {
		return $this->specialAbilityValue;
	}

     public function setSystemDataWindow($turn){
        $this->data["Special"] = "Select"; 
        //$this->data["Special"] .= "<br>Each Gravity Net has a max movement range (Move Distance) determined by a D6 roll made for each weapon at start of each pre-fire phase.";
        //$this->data["Special"] .= "<br>Only ONE Gravity Net may affect a ship per turn, but any number may be fired at a target.;           
        //$this->data["Special"] .= "<br>Default "Standard (S)" mode: The Gravity Net that hits a given target with the largest "Move Distance" will take priority";
        //$this->data["Special"] .= "<br>"Priorty (P)" mode: The Priorty set Gravity Net that hits a given target with the largest "Move Distance" will take priority over all other Gravity Nets.;
        //$this->data["Special"] .= "<br>If no priority Gravity Nets hit than largest "Standard" mode Gravity Net will take priority";
        //$this->data["Special"] .= "<br>Has -15% chance to hit Ancient enemy units, or those with Gravitic drives (Does NOT include allies).";        
        //$this->data["Special"] .= "<br>Can target allies.";
        //$this->data["Special"] .= "<br>No effect on units smaller then fireing ship.";	        	        		
		parent::setSystemDataWindow($turn);     

	public function calculateHitBase($gamedata, $fireOrder){            
        if($fireOrder->damageclass == 'gravNetMoveHex'){
				$fireOrder->needed = 100; //always true
				$fireOrder->updated = true;
        }else{
            parent::calculateHitBase($gamedata, $fireOrder);

            if($fireOrder->targetid != -1){
                    $target = $gamedata->getShipById($fireOrder->targetid);
                $shooter = $this->getUnit();
                if($shooter->team !== $target->team){ //Let's make penalty only for enemy units
                    if($target->gravitic || $target->factionAge >= 3){//Gravitic or Ancient
                        $fireOrder->needed -= 15; //+15% chance to miss.
                    }
                }    
            }
        }
	}    

    public function fire($gamedata, $fireOrder){                   
        parent::fire($gamedata, $fireOrder);      
    }
    
    protected function beforeDamage($target, $shooter, $fireOrder, $pos, $gamedata){   
        if($fireOrder->damageclass == 'gravitic'){ //should only process the grav net's first fireOrder (pertains to actual shot) and then get gravNetPos from 2nd hexTarget order         
            $targetSize = null;
            $shooterSize = null;

            if(!$target->Enormous){
                $targetSize = $target->shipSizeClass; //only get ship size if not enormous, logic below accounts for enormous.
            }
            
            if(!$shooter->Enormous){
                $shooterSize = $shooter->shipSizeClass; //only get ship size if not enormous, logic below accounts for enormous.
            }

            if($shooter->Enormous || (!$target->Enormous && $shooterSize >= $targetSize)){ //Make sure target is equal to or smaller then shooter.      
                $primaryGravityNet = GravityNetHandler::getPrimaryGravNetPerTarget($target);
                //Debug::log(json_encode($primaryGravityNet, true));
                if($this == $primaryGravityNet){//check if THIS gravity net is the primary gravity net(Primary is defined as gravity net that hits a given target with largest move distance)
                    $fireOrder->pubnotes = "<br>Ship has been forced to move by a Gravity Net.";
                    $this->doGravityNetMove($target, $fireOrder->id, $gamedata);
                }
            }else{
                $fireOrder->pubnotes = "<br>Target ship is larger then fireing ship and cannot be moved by Gravity Net.";
            }              
        }
    }  

    private function doGravityNetMove($target, $graviticOrderID, $gamedata){
        $allFireOrders = $this->getFireOrders($gamedata->turn);
        $gravNetMovePosOrder = null; //var to hold grav net move position order, ie the hexTarget order.
        $gravNetMovePos = null; //var to hold grav net move target hex.

        foreach($allFireOrders as $fireOrderCheck){ //find the gravNetMoveHex order and then process. If it does not exist do not process.
            if ($fireOrderCheck->damageclass == 'gravNetMoveHex'){
                $gravNetMovePosOrder = $fireOrderCheck;	
                $xpos = $gravNetMovePosOrder->x; //get x coord for movement
                $ypos = $gravNetMovePosOrder->y; //get y coord for movement
                $orderID = $gravNetMovePosOrder->id;
                //Debug::log(json_encode($xpos, true));

                $lastMove = $target->getLastMovement(); //get target ship's last movement to ensure heading, speed and facing are conserved. 
                //Create new movement order to target ship to grav net target hex.
                //$id, $type, OffsetCoordinate $position, $xOffset, $yOffset, $speed, $heading, $facing, $pre, $turn, $value, $at_initiative)
                $gravNetMove = new MovementOrder(null, "prefire", new OffsetCoordinate($xpos, $ypos), 0, 0, $lastMove->speed, $lastMove->heading, $lastMove->facing, false, $gamedata->turn, $graviticOrderID, 0);

                //Add shifted movement order to database
                Manager::insertSingleMovement($gamedata->id, $target->id, $gravNetMove);				
                break;
            }
        }         
    }    
    
    public function generateIndividualNotes($gameData, $dbManager){ //dbManager is necessary for Initial phase only
		$ship = $this->getUnit();

        switch($gameData->phase){
					
				case 2: //Movement phase
                    if($ship->userid == $gameData->forPlayer){ //only own ships, otherwise bad things may happen!
						//load existing data first - at this point ship is rudimentary, without data from database!
						$listNotes = $dbManager->getIndividualNotesForShip($gameData, $gameData->turn, $ship->id);	
						//roll a D6 to determine how far THIS gravity net can move a ship	                    				
                        $currentResult = Dice::d(6, 1); //dice size, number of rolls, determined max move for gravity net
                        $notekey = 'gravityNetMove';
                        $noteHuman = 'Gravity Net Max Move: '.strval($currentResult);
                        $notevalue = $currentResult;
                        $this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$notevalue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue
                    }  
		break;	       
        }
    }

    public function onIndividualNotesLoaded($gameData){     
        switch($gameData->phase){
            case 1:
                $this->moveDistance = "TBD: PreFirePhase"; //set moveDistance back to TBD: PreFirePhase until next prefire phase, then display newly rolled value.

            case 5: //get the current turns gravity net max movement value in PreFile phase   
            foreach ($this->individualNotes as $currNote) {
                    if ($currNote->turn == $gameData->turn) {               
                        if ($currNote->notekey == 'gravityNetMove') {
                            $this->moveDistance = $currNote->notevalue; 
                        }
                    }
                }                
        break;
        }
    }

	public function getDamage($fireOrder){       return 0;   } //no actual damage
	public function setMinDamage(){     $this->minDamage = 0 ;      }
	public function setMaxDamage(){     $this->maxDamage = 0 ;      }

    public function stripForJson() {
        $strippedSystem = parent::stripForJson(); 
        $strippedSystem->showHexagonArc = $this->showHexagonArc;  
        $strippedSystem->canTargetAll = $this->canTargetAll;
        $strippedSystem->canTargetAll = $this->canTargetAll;
        $strippedSystem->moveDistance = $this->moveDistance;                                        
        return $strippedSystem;
	}	
     //endof GravityNet
}

class GravityNetHandler{ 
	public $name = "GravityNetHandler";
	private static $gravityNets = array();	
	
	//should be called by every Targeting Array on creation!
	public static function addGravityNet($weapon){
		self::$gravityNets[] = $weapon;		
	}

    public static function getPrimaryGravNetPerTarget($target){//returns the primary gravity net, highest priorty, highest movement and hitting grav net, per target
        $hittingGravNetsOnTarget = self::getHittingGravNetsOnTarget($target); //get all hitting gravity nets on the same target.
        $priortyGravNets = self::getPriortyGravNets($hittingGravNetsOnTarget); //see if any priority mode grav nets exist

        if(count($priortyGravNets) > 0){
            $primaryGravityNet = self::getLargestMovement($priortyGravNets); //find largestMove Priority grav net   
        }else{
            $primaryGravityNet = self::getLargestMovement($hittingGravNetsOnTarget); //find largestMove out of all hitting nets (no priority grav net/s designated)
        }
        return $primaryGravityNet;
    }

    private static function getHittingGravNetsOnTarget($target){//get all gravity nets that target same target and hit.
        $hittingGravNetsOnTarget = array();
        foreach(self::$gravityNets as $gravityNet){            
            foreach($gravityNet->fireOrders as $fireOrder){                
                $fireOrderTargetid = $fireOrder->targetid;//current fireorder targetid
                $currTargetid = $target->id; //current gravity net's targetid
                if($fireOrderTargetid == $currTargetid){//make sure target of processing gravity net matches current gravity net
                    $shotsHit = $fireOrder->shotshit;
                        if($shotsHit == 1){ //make sure the current grav net hit             
                            $hittingGravNetsOnTarget[] = $gravityNet; //add hitting gravity net with same target to array (as processing gravity net).
                        }
                }
            }          
        }
        return $hittingGravNetsOnTarget;
    }

    private static function getPriortyGravNets($gravityNets){//find all grav nets firing in Priority mode
        $priortyGravNets = array();
        foreach($gravityNets as $gravityNet){ 
            foreach($gravityNet->fireOrders as $fireOrder){                
                if($fireOrder->firingMode == 2){ //find priorty grav nets
                    $priortyGravNets[] = $gravityNet;
                }                
            }
        }   
        return $priortyGravNets; 
    }

	private static function getLargestMovement($gravityNets){ //get Gravity Net with largest movement potential
        $largestMovementValue = 0; //set inital movement range to 0;
        $largestGravityNet = null;        
        foreach($gravityNets as $gravityNet){ 
            foreach($gravityNet->fireOrders as $fireOrder){                
                if($gravityNet->moveDistance > $largestMovementValue){
                    $largestMovementValue = $gravityNet->moveDistance;
                    $largestGravityNet = $gravityNet;
                }                
            }
        }
        return $largestGravityNet;
    }	  
}

class HypergravitonBlaster extends Weapon {
        public $name = "HypergravitonBlaster";
        public $displayName = "Hypergraviton Blaster";
        public $iconPath = "HypergravitonBlaster.png";         

        public $animation = "laser";
        public $animationColor = array(250, 251, 196);

        public $factionAge = 3;//Ancient weapon, which sometimes has consequences!

        public $boostable = true;
        public $boostEfficiency = 0;//Weapon is boosted by Thrust, not power.  Handled in Front-End in getRemainingEngineThrust
        public $loadingtime = 1;
        public $normalload = 2;
        public $priority = 7;
		
        public $rangePenalty = 0.25;
        public $fireControl = array(6, 6, 6); // fighters, <mediums, <capitals 
        
		public $raking = 20;
		public $damageType = 'Raking'; 
    	public $weaponClass = "Gravitic";
		public $fireingModes = array(1=>"Raking");
    	
		protected $thrustBoosted = true;//Variable FRont End looks for to use thrust as boost. 
	    protected $thrustPerBoost = 6; //Variable showing how much thrust is used per boost.

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 30;
            }
            if ( $powerReq == 0 ){
                $powerReq = 15;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
			$this->data["Special"] = "20-point rakes.";
			$this->data["Special"] .= "<br>May apply thrust to boost damage output.";
			$this->data["Special"] .= "<br>Every 6 thrust applied adds 10 damage.";
			$this->data["Special"] .= "<br>Can fire accelerated ROF for less damage.";
			$this->data["Special"] .= "<br> - 1 turn: 5d10+40";
			$this->data["Special"] .= "<br> - 2 turns: 10d10+80";
            
            parent::setSystemDataWindow($turn);
        } 

        protected function getBoostLevel($turn){
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
            $add = $this->getBoostLevel($fireOrder->turn);
			switch($this->turnsloaded){
				case 0:
				case 1:
					$dmg = Dice::d(10, 5) + 40 + ($add * 10);
					return $dmg;
					break;
				default:
					$dmg = Dice::d(10, 10) + 80 + ($add * 10);
					return $dmg;
					break;
			}
        }

        public function getAvgDamage(){
            $this->setMinDamage();
            $this->setMaxDamage();

            $min = $this->minDamage;
            $max = $this->maxDamage;
            $avg = round(($min+$max)/2);
            return $avg;
        }

        public function setMinDamage(){
            $turn = TacGamedata::$currentTurn;
            $boost = $this->getBoostLevel($turn);
			switch($this->turnsloaded){
				case 1:
					$this->minDamage = 45 + ($boost * 10);
					break;
				default:
					$this->minDamage = 90 + ($boost *10);
					break;
			}
        }   

        public function setMaxDamage(){
            $turn = TacGamedata::$currentTurn;
            $boost = $this->getBoostLevel($turn);
			switch($this->turnsloaded){
				case 1:
					$this->maxDamage = 90 + ($boost * 10);
					break;
				default:
					$this->maxDamage = 180 + ($boost * 10);
					break;
			}
				
        }  

		public function stripForJson(){
			$strippedSystem = parent::stripForJson();
			$strippedSystem->thrustBoosted = $this->thrustBoosted;
			$strippedSystem->thrustPerBoost = $this->thrustPerBoost;													
			return $strippedSystem;
		}

    } //endof HypergravitonBlaster



class HypergravitonBeam extends Weapon {
        public $name = "HypergravitonBeam";
        public $displayName = "Hypergraviton Beam";
        public $iconPath = "HypergravitonBeam.png";         

        public $animation = "laser";
        public $animationColor = array(250, 251, 196);

        public $factionAge = 3;//Ancient weapon, which sometimes has consequences!

        public $boostable = true;
        public $boostEfficiency = 0;//Weapon is boosted by Thrust, not power.  Handled in Front-End in getRemainingEngineThrust
        public $loadingtime = 1;
        public $priority = 6;
		
        public $rangePenalty = 0.33;
        public $fireControl = array(3, 4, 5); // fighters, <mediums, <capitals 
        
		public $raking = 15;
		public $damageType = 'Raking'; 
    	public $weaponClass = "Gravitic";
    	
		protected $thrustBoosted = true;//Variable FRont End looks for to use thrust as boost. 
	    protected $thrustPerBoost = 4; //Variable showing how much thrust is used per boost.

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 20;
            }
            if ( $powerReq == 0 ){
                $powerReq = 12;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
			$this->data["Special"] = "15-point rakes.";
			$this->data["Special"] .= "<br>May apply thrust to boost damage output.";
			$this->data["Special"] .= "<br>Every 4 thrust applied adds 5 damage.";
            
            parent::setSystemDataWindow($turn);
        } 

        protected function getBoostLevel($turn){
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
            $add = $this->getBoostLevel($fireOrder->turn);
			$dmg = Dice::d(10, 4) + 20 + ($add * 5);
            return $dmg;
        }

        public function getAvgDamage(){
            $this->setMinDamage();
            $this->setMaxDamage();

            $min = $this->minDamage;
            $max = $this->maxDamage;
            $avg = round(($min+$max)/2);
            return $avg;
        }

        public function setMinDamage(){
            $turn = TacGamedata::$currentTurn;
            $boost = $this->getBoostLevel($turn);
            $this->minDamage = 24 + ($boost * 5);
        }   

        public function setMaxDamage(){
            $turn = TacGamedata::$currentTurn;
            $boost = $this->getBoostLevel($turn);
            $this->maxDamage = 60 + ($boost * 5);
        }  

		public function stripForJson(){
			$strippedSystem = parent::stripForJson();
			$strippedSystem->thrustBoosted = $this->thrustBoosted;
			$strippedSystem->thrustPerBoost = $this->thrustPerBoost;													
			return $strippedSystem;
		}

    } //endof HypergravitonBeam



class MedAntigravityBeam extends Gravitic{
		public $name = "MedAntigravityBeam";
        public $displayName = "Medium Antigravity Beam";
        public $iconPath = "MedAntigravityBeam.png";
        public $animation = "laser";
        public $animationColor = array(250, 251, 196);

        public $factionAge = 4;//Primordial weapon, which sometimes has consequences!

        public $intercept = 2;
		public $priority = 5; 		
    	public $gunsArray = array(1=>1, 2=>2); //one mount, but two Beam shots!
		
        public $loadingtime = 1;
		
        public $rangePenaltyArray = array(1=>0.5, 2=>0.5);
        public $fireControlArray = array( 1=>array(4, 3, 1), 2=>array(4, 3, 1) ); 

        public $damageType = "Standard";
		public $damageTypeArray = array(1=>'Standard', 2=>'Standard'); 
		public $weaponClass = "Gravitic";
		public $weaponClassArray = array(1=>'Gravitic', 2=>'Gravitic');
		public $firingModes = array( 1 => "Single", 2=> "Dual Beams");
		public $firingMode = 1;
        public $canSplitShots = false; //Allows Firing Mode 2 to split shots.
        public $canSplitShotsArray = array(1=>false, 2=>true );          

		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){ //maxhealth and power reqirement are fixed; left option to override with hand-written values
			if ( $maxhealth == 0 ) $maxhealth = 6;
			if ( $powerReq == 0 ) $powerReq = 2;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);   
			if (!isset($this->data["Special"])) {
				$this->data["Special"] = '';
			}else{
				$this->data["Special"] .= '<br>';
			}
			$this->data["Special"] .= "Can fire as either:";  
			$this->data["Special"] .= "<br> - A single medium antigravity beam (2d10+4)."; 
			$this->data["Special"] .= "<br> - Split into two beams (1d10+2, each)."; 
		}
	
		public function getDamage($fireOrder){
			switch($this->firingMode){
				case 1:
					return Dice::d(10, 2)+4; //Medium Antigravity Beam
					break;
				case 2:
					return Dice::d(10, 1)+2; //Split Beam
					break;	
			}
		}

 		public function setMinDamage(){
			switch($this->firingMode){
				case 1:
					$this->minDamage = 6; //Medium Antigravity Beam
					break;
				case 2:
					$this->minDamage = 3; //Split Beam
					break;	
			}
		}
             
        public function setMaxDamage(){
			switch($this->firingMode){
				case 1:
					$this->maxDamage = 24; //Medium Antigravity Beam
					break;
				case 2:
					$this->maxDamage = 12; //Split Beam
					break;	
			}
		}
		
}//end of class MedAntigravityBeam



class AntigravityBeam extends Gravitic{
		public $name = "AntigravityBeam";
        public $displayName = "Antigravity Beam";
        public $iconPath = "AntigravityBeam.png";
        public $animation = "laser";
        public $animationColor = array(250, 251, 196);

        public $factionAge = 3;//Primordial weapon, which sometimes has consequences!

        public $intercept = 3;
		public $priority = 5; 		
    	public $gunsArray = array(1=>1, 2=>3); //one mount, but three Beam shots!
		
        public $loadingtime = 1;
		
        public $rangePenaltyArray = array(1=>0.5, 2=>0.5);
        public $fireControlArray = array( 1=>array(5, 3, 1), 2=>array(5, 3, 1) ); 

        public $damageType = "Standard";
		public $damageTypeArray = array(1=>'Standard', 2=>'Standard'); 
		public $weaponClass = "Gravitic";
		public $weaponClassArray = array(1=>'Gravitic', 2=>'Gravitic');
		public $firingModes = array( 1 => "Single", 2=> "Triple Beams");
		public $firingMode = 1;
        public $canSplitShots = false; //Allows Firing Mode 2 to split shots.
        public $canSplitShotsArray = array(1=>false, 2=>true );          

		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){ //maxhealth and power reqirement are fixed; left option to override with hand-written values
			if ( $maxhealth == 0 ) $maxhealth = 6;
			if ( $powerReq == 0 ) $powerReq = 3;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);   
			if (!isset($this->data["Special"])) {
				$this->data["Special"] = '';
			}else{
				$this->data["Special"] .= '<br>';
			}
			$this->data["Special"] .= "Can fire as either:";  
			$this->data["Special"] .= "<br> - A single antigravity beam (3d10+6)."; 
			$this->data["Special"] .= "<br> - Split into three beams (1d10+2, each)."; 
		}
	
		public function getDamage($fireOrder){
			switch($this->firingMode){
				case 1:
					return Dice::d(10, 3)+6; //Antigravity Beam
					break;
				case 2:
					return Dice::d(10, 1)+2; //Split Beam
					break;	
			}
		}

 		public function setMinDamage(){
			switch($this->firingMode){
				case 1:
					$this->minDamage = 9; //Antigravity Beam
					break;
				case 2:
					$this->minDamage = 3; //Split Beam
					break;	
			}
		}
             
        public function setMaxDamage(){
			switch($this->firingMode){
				case 1:
					$this->maxDamage = 36; //Antigravity Beam
					break;
				case 2:
					$this->maxDamage = 12; //Split Beam
					break;	
			}
		}
		
}//end of class AntigravityBeam


?>
