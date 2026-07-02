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
            if (isset($this->sustainedTarget) && !empty($this->sustainedTarget)) {
                $strippedSystem->sustainedTarget = $this->sustainedTarget;
            }                        			
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
	public $specialAbilities = array("PreFiring", "alliedEW");
	public $specialAbilityValue = true; //so it is actually recognized as special ability!  		
	public $damageType = "Standard"; //irrelevant, really
	public $weaponClass = "Gravitic";
	public $uninterceptable = true; //although I don't think a weapon exists that could intercept it...
	public $doNotIntercept = true; //although I don't think a weapon exists that could intercept it...
	public $loadingtime = 3;
    public $rangePenalty = 1;
    public $canTargetAll = true; //Allows weapon to target allies AND enemies, pass to Front End in strpForJson()
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
        $this->data["Special"] .= "<br>Can target allies without suffering double range penalty for no EW Lock.";
        $this->data["Special"] .= "<br>No effect on Enormous units or Mines.";	        	        		
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
        //$shooter = $this->getUnit();

        if($target->gravitic || $target->factionAge >= 3){ 
            $fireOrder->needed -= 15; //-15% to hit gravitic and/or Ancient targets. 
        }      
	}    
        
    public function fire($gamedata, $fireOrder){                   
        parent::fire($gamedata, $fireOrder); 
    }

    protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){
        if ($ship->Enormous) return; //No effect on Enormous units
        if ($ship instanceof Mine){
            $fireOrder->pubnotes = "<br>Mines are not affected by Gravitic Shifters.";
            return; //No point.                         
        } 

		$lastMove = $ship->getLastMovement();
        $newFacing = $lastMove->facing; //Initialise as current facing.
        $newHeading = $lastMove->heading; //Initialise as current heading. 

        if($fireOrder->firingMode == 1){
            $newFacing = MathLib::addToHexFacing($lastMove->facing , 1);
            $newHeading = MathLib::addToHexFacing($lastMove->heading , 1);
        }else{
            $newFacing = MathLib::addToHexFacing($lastMove->facing , -1);
            $newHeading = MathLib::addToHexFacing($lastMove->heading , -1);                      
        }
		
		//Create new movement order for target.
        $shift = new MovementOrder(null, "prefire", new OffsetCoordinate($lastMove->position->q, $lastMove->position->r), 0, 0, $lastMove->speed, $newHeading, $newFacing, false, $gamedata->turn, $fireOrder->id, 0);

        if($fireOrder->firingMode == 1){
            $direction = "clockwise";
        }else{
            $direction = "anti-clockwise";      
        }        

        if($fireOrder->shotshit > 0){
            $fireOrder->pubnotes = "<br>Ship has been forced to turn 60 degrees " . $direction . " by a Gravitic Shifter.";                       
        }   
		//Add shifted movement order to database and in-memory movement array
		//so subsequent prefire weapons see the updated heading/facing.
		Manager::insertSingleMovement($gamedata->id, $ship->id, $shift);
		$ship->setMovement($shift);	
    }    

	public function getDamage($fireOrder){       return 0;   } //no actual damage
	public function setMinDamage(){     $this->minDamage = 0 ;      }
	public function setMaxDamage(){     $this->maxDamage = 0 ;      }

    public function stripForJson() {
        $strippedSystem = parent::stripForJson();    
        //$strippedSystem->canTargetAll = $this->canTargetAll;	        											                                        
        return $strippedSystem;
	}	

    } //endof GraviticShifter    

class GravityNet extends Weapon implements SpecialAbility{
    public $name = "GravityNet";
    public $displayName = "Gravity Net";
    public $animation = "laser";
    public $animationColor = array(102, 255, 00);	
    public $animationExplosionScale = 1.0; //single hex explosion
    public $priority = 1; 
	public $hextarget = false; //Toggle to switch between hexTarget and normalTarget modes
	public $hidetarget = true;   
	public $specialAbilities = array("PreFiring", "alliedEW");
	public $specialAbilityValue = true; //so it is actually recognized as special ability!  		
	public $damageType = "Standard"; //irrelevant, really
	public $weaponClass = "Gravitic";
	public $uninterceptable = true; //although I don't think a weapon exists that could intercept it...
	public $doNotIntercept = true; //although I don't think a weapon exists that could intercept it...
	public $loadingtime = 2;
    public $rangePenalty = 1;
    public $canTargetAll = true; //Allows weapon to target allies AND enemies, pass to Front End in strpForJson()	
    public $fireControl = array(1, 2, 3); // fighters, <mediums, <capitals 
	public $preFires = true;
    public $canSplitShots = true;
    public $moveDistance = "";
    public $showHexagonArc = true; 
    public $specialHitChanceCalculation = true;			
	public $repairPriority = 6;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired

    public $firingModes = array(
		1 => "Standard - GN",
        2 => "Priority - GN"
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
        $this->data["Special"] = "<br>Each Gravity Net has a max movement range (Move Distance) determined by a D6 roll made for each weapon at start of pre-fire phase.";
        $this->data["Special"] .= "<br>Move Distance is reported on mouse over and can be visualized when targeting with overlay.";
        $this->data["Special"] .= "<br>Only ONE Gravity Net may affect a ship per turn, more may be fired at target.";           
        $this->data["Special"] .= "<br>Standard (S) mode: Gravity Net hitting target with the largest Move Distance will take priority";
        $this->data["Special"] .= "<br>Priorty (P) mode: Gravity Net hitting target will take priority over all other Gravity Nets.";
        $this->data["Special"] .= "<br>Has -15% chance to hit Ancient enemy units, or those with Gravitic drives (Does NOT include allies).";        
        $this->data["Special"] .= "<br>Can target allies.";
        $this->data["Special"] .= "<br>No effect on units bigger then firing ship, or Mines.";	        	        		
		parent::setSystemDataWindow($turn);  
    }   

	public function calculateHitBase($gamedata, $fireOrder){            
        if($fireOrder->damageclass == 'gravNetMoveHex'){
				$fireOrder->needed = 100; //always true
				$fireOrder->updated = true;
        }else{
            parent::calculateHitBase($gamedata, $fireOrder);

            if($fireOrder->targetid != -1){
                $target = $gamedata->getShipById($fireOrder->targetid);
                $shooter = $this->getUnit();

                if($target->gravitic || $target->factionAge >= 3){ 
                    $fireOrder->needed -= 15; //-15% to hit gravitic and/or Ancient targets. 
                }
            }
        }
	}    

    public function fire($gamedata, $fireOrder){   
		if($fireOrder->damageclass == 'gravNetMoveHex'){		
			return; //Don't roll targeting shots, to remove them from animations.
		}else{	                        
            parent::fire($gamedata, $fireOrder);  
        }        
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

            if ($target instanceof Mine){
                $fireOrder->pubnotes = "<br>Mines cannot be moved by Gravity Net.";
            } else if($shooter->Enormous || (!$target->Enormous && $shooterSize >= $targetSize)){ //Make sure target is equal to or smaller then shooter.      
                $primaryGravityNet = GravityNetHandler::getPrimaryGravNetPerTarget($target);
                if($this == $primaryGravityNet){//check if THIS gravity net is the primary gravity net(Primary is defined as gravity net that hits a given target with largest move distance)
                    $fireOrder->pubnotes = "<br>Ship has been forced to move by a Gravity Net.";
                    $this->doGravityNetMove($target, $fireOrder->id, $gamedata);
                }
            }else{
                $fireOrder->pubnotes = "<br>Target ship is larger then firing ship and cannot be moved by Gravity Net.";
            }              
        }
    }  

    private function doGravityNetMove($target, $graviticOrderID, $gamedata){
        $allFireOrders = $this->getFireOrders($gamedata->turn);
        $gravNetMovePosOrder = null; //var to hold grav net move position order, ie the hexTarget order.
        //$gravNetMovePos = null; //var to hold grav net move target hex.

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

                //Add shifted movement order to database and in-memory movement array
                //so subsequent prefire weapons see the updated position.
                Manager::insertSingleMovement($gamedata->id, $target->id, $gravNetMove);
                $target->setMovement($gravNetMove);				
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
        //$strippedSystem->canTargetAll = $this->canTargetAll;
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
		public $firingModes = array(1=>"Raking");
    	
		protected $thrustBoosted = true;//Variable Front End looks for to use thrust as boost. 
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
			$this->data["Special"] .= "<br>May apply 6 thrust to boost damage by 10 during Intitial Orders.";
			$this->data["Special"] .= "<br>Can fire accelerated for less damage.";
			$this->data["Special"] .= "<br> - 1 turn: 5d10 + 40";
			$this->data["Special"] .= "<br> - 2 turns: 10d10 + 80";
			$this->data["Special"] .= "<br>Can target multiple enemy units on a single turn, use Target Transfer List menu to build a chain of potential targets.";            
			$this->data["Special"] .= "<br>On a miss or transfer, weapon subtracts 20 damage and re-rolls to hit until it hits or runs out of damage.";

            parent::setSystemDataWindow($turn);
        }

        /* STAGE 1 - Miss re-roll.
         * The Hypergraviton Blaster determines its full volley damage UP FRONT (per
         * rules), then rolls to hit. On a miss it subtracts one 20-point rake from the
         * remaining damage and rolls again, repeating until it hits or has less than a
         * full rake left. Whatever damage survives that process is what actually rakes
         * the target.
         *
         * We override fire() instead of letting the base single-roll routine run, because
         * the base fire() rolls once and (on hit) calls beforeDamage()->getDamage() which
         * re-rolls the damage dice. Here we must roll the damage ONCE and decrement it as
         * rakes are consumed by misses, so we drive our own roll loop and call damage()
         * directly with the pre-rolled, decremented amount.
         *
         * $this->firstShotMissed is recorded for later stages (a missed FIRST shot bars
         * any target transfer for the rest of the volley).
         */
        public $firstShotMissed = false; //set true if the opening to-hit roll missed - blocks transfer in later stages

        /* STAGE 2 - transfer-target carrier.
         * The client encodes the player's ordered transfer-target list into the fire
         * order's notes field as:  HBT|<shipid>:<transferOnStructure 0|1>|<shipid>:<flag>|...
         * The leading HBT marks our format. transferOnStructure=1 means "also transfer
         * when this target merely loses a structure block" (HCV+); 0 means "only transfer
         * once this target is wholly destroyed".
         *
         * beforeFiringOrderResolution (called in firing.php before fireWeapons) parses that
         * string into $this->transferQueue, then CLEARS notes so the stored fire order ends
         * up holding only the human-readable firing log, not the encoded payload.
         */
        public $transferQueue = array(); //ordered list of ['shipid'=>int,'transferOnStructure'=>bool] for the current shot

        //One Blaster per ship may transfer damage per turn (the ship can't optimise more than
        //one beam at once). Keyed "shipid|turn" -> weaponid that claimed the transfer. Static so
        //all Blasters on the same ship see the same claim during a single firing resolution.
        protected static $transferClaimedByShipTurn = array();

        /* Returns true if THIS Blaster may transfer this turn: true if no Blaster on the ship has
         * claimed yet, or if this same weapon already holds the claim (so a multi-hop chain by the
         * same weapon keeps working). False if a DIFFERENT Blaster on the ship already transferred. */
        protected function claimTransferForTurn($shooter, $gamedata)
        {
            $key = $shooter->id . '|' . $gamedata->turn;
            if (!isset(self::$transferClaimedByShipTurn[$key])) {
                self::$transferClaimedByShipTurn[$key] = $this->id;
                return true;
            }
            return self::$transferClaimedByShipTurn[$key] === $this->id;
        }

        public function beforeFiringOrderResolution($gamedata)
        {
            $this->transferQueue = array();

            foreach ($this->getFireOrders($gamedata->turn) as $order) {
                if ($order->turn != $gamedata->turn) continue;
                if (empty($order->notes)) continue;
                if (strpos($order->notes, 'HBT|') !== 0) continue; //not our encoded format

                $segments = explode('|', $order->notes);
                array_shift($segments); //drop the leading 'HBT'

                foreach ($segments as $seg) {
                    if ($seg === '') continue;
                    $parts = explode(':', $seg);
                    $shipid = (int)$parts[0];
                    $flag   = isset($parts[1]) ? ((int)$parts[1] === 1) : false;
                    if ($shipid <= 0) continue;
                    $this->transferQueue[] = array(
                        'shipid' => $shipid,
                        'transferOnStructure' => $flag,
                    );
                }

                //Clear the encoded payload so it never reaches the firing log / DB as gibberish.
                //(Note: calculateHitBase later OVERWRITES notes with its hit-calc breakdown,
                // so anything written here is for in-flight use only and won't persist.)
                $order->notes = '';
            }
        }

        public function fire($gamedata, $fireOrder)
        {
            $shooter = $gamedata->getShipById($fireOrder->shooterid);
            $target  = $gamedata->getShipById($fireOrder->targetid);

            //No valid target (e.g. a hex-targeted order leaking through): fall back to base handling.
            if ($target == null) {
                parent::fire($gamedata, $fireOrder);
                return;
            }

            //Mirror base fire(): apply interception to the needed number first.
            $fireOrder->needed -= $fireOrder->totalIntercept;
            $fireOrder->notes .= "Interception: " . $fireOrder->totalIntercept
                . " sources:" . $fireOrder->numInterceptors
                . ", final to hit: " . $fireOrder->needed . "\n";

            $pos = null; //let damage routines work out the source position (correct at range 0)
            if ($this->ballistic) {
                $pos = $this->getFiringHex($gamedata, $fireOrder);
            }

            //Determine the full volley damage ONCE, before rolling to hit.
            $damageRemaining = $this->getFinalDamage($shooter, $target, $pos, $gamedata, $fireOrder);
            $rakeSize        = $this->getRakeSize();

            $this->firstShotMissed = false;
            $attempts = 0; //number of to-hit rolls made (filled by reference) - used for the X/Y combat-log display

            //If the original target is ALREADY destroyed (killed by other fire that resolved
            //before this Blaster) and we hold a transfer queue, don't roll to hit a corpse:
            //skip the opening shot entirely and go straight to the transfer flow, which will
            //hop to the next legal queued target. There's no opening shot to "miss", so the
            //first-shot-miss transfer bar does not apply here.
            $targetAlreadyDead = $target->isDestroyed();

            //A fighter-flight target is handled by the per-fighter rake routine, which gives
            //each fighter its own to-hit roll and tallies $fireOrder->shots/shotshit itself
            //(so the combat log reads e.g. 3/3 when three fighters die, not 1/1). It also drives
            //firstShotMissed off its very first roll, then transfers if a queue is present and the
            //flight is wiped out. We must NOT also run the single opening roll / shotshit++ below,
            //or the count would double up.
            $isFlightTarget = ($target instanceof FighterFlight);

            if ($targetAlreadyDead && !empty($this->transferQueue)) {
                $fireOrder->shots = 1;
                $this->resolveRakingWithTransfer($shooter, $target, $fireOrder, $gamedata, $damageRemaining, $rakeSize);
            } else if ($isFlightTarget && !$targetAlreadyDead) {
                //rakeFighterFlight owns the to-hit rolls AND the shots/shotshit tally for the flight.
                //Zero the constructor defaults (shots=1, shotshit=0) first so its per-fighter
                //increments produce an accurate count (e.g. 3/3) rather than an off-by-one (4/3).
                $fireOrder->shots = 0;
                $fireOrder->shotshit = 0;
                $stop = $this->rakeFighterFlight($shooter, $target, $fireOrder, $gamedata, $damageRemaining, $rakeSize);
                //If the whole flight was destroyed and a transfer queue is present (and the opening
                //fighter wasn't a first-shot miss), carry the surviving damage on down the list.
                if ($stop === 'destroyed' && !empty($this->transferQueue) && !$this->firstShotMissed) {
                    $this->resolveRakingWithTransfer($shooter, $target, $fireOrder, $gamedata, $damageRemaining, $rakeSize);
                }
            } else {
                $hit = $this->rollToHitWithRakeReroll($gamedata, $fireOrder, $target, $damageRemaining, $rakeSize, true, $attempts);

                if ($hit) {
                    //Apply whatever damage survived the re-roll process. If a transfer queue was
                    //supplied (and the first shot didn't miss), use the transfer-aware raking loop;
                    //otherwise fall back to the base raking behaviour exactly.
                    $fireOrder->shotshit++;
                    $target->clearVreeHitSectionChoice($shooter->id, $fireOrder);

                    if (!empty($this->transferQueue) && !$this->firstShotMissed) {
                        $this->resolveRakingWithTransfer($shooter, $target, $fireOrder, $gamedata, $damageRemaining, $rakeSize);
                    } else {
                        $this->damage($target, $shooter, $fireOrder, $gamedata, $damageRemaining);
                    }
                }

                //Report each to-hit attempt as a "shot" so the combat log shows hits/attempts
                //(e.g. 1/3 for a hit on the third roll) instead of a misleading 1/1. (Flights set
                //their own shots inside rakeFighterFlight, so this only covers the ship path.)
                $fireOrder->shots = max(1, $attempts);
            }

            //Bookkeeping identical to base fire() so combat log / downstream code stay happy.
            $fireOrder->rolled = max(1, $fireOrder->rolled);
            TacGamedata::$lastFiringResolutionNo++;
            $fireOrder->resolutionOrder = TacGamedata::$lastFiringResolutionNo;
        } //endof fire

        /* STAGE 3 - transfer-aware raking.
         * Rakes $damageRemaining into $target one 20-pt rake at a time. After each rake it
         * checks whether the beam should jump to the next queued target:
         *   - the target is wholly destroyed, OR
         *   - the section's structure block was just destroyed AND that target's
         *     transferOnStructure flag is set AND the target is HCV+ (shipSizeClass>=2).
         * On a transfer: subtract one rake (the transfer cost), pull the next queue entry,
         * roll to hit it with the standard re-roll-on-miss procedure, and (on a hit) continue
         * raking the new victim. A failed transfer keeps re-rolling the SAME new target until
         * it hits or the volley runs dry (per design decision 2026-06-27). Each victim after
         * the first gets its own synthetic addToDB FireOrder so the combat log reads as a
         * sequence of distinct shots.
         */
        protected function resolveRakingWithTransfer($shooter, $target, $fireOrder, $gamedata, $damageRemaining, $rakeSize)
        {
            $queueIndex    = 0;
            $currentTarget = $target;
            $currentOrder  = $fireOrder; //the live fire order whose damage entries we attribute rakes to

            while ($damageRemaining > 0 && $currentTarget != null) {

                if ($currentTarget->isDestroyed()) {
                    //The current victim is ALREADY destroyed - either it was killed by other fire
                    //before this Blaster resolved (initial target), or a prior rake/transfer left it
                    //dead. Don't waste rakes on a corpse: treat it as a 'destroyed' stop and try to
                    //transfer straight on to the next queued target.
                    $stop = 'destroyed';
                } else {
                    //Rake the current victim until it's destroyed, a structure-block transfer is
                    //triggered, or we run out of damage. Returns the stop reason; $damageRemaining
                    //is updated by reference.
                    $stop = $this->rakeOneVictim($shooter, $currentTarget, $currentOrder, $gamedata, $damageRemaining, $rakeSize);
                }

                if ($stop === 'outOfDamage') {
                    break;
                }
                //stop is 'destroyed' or 'structure' - either way a transfer MAY be attempted,
                //provided we can pay the transfer cost AND there's a legal next target.

                //Can we transfer? It costs one FULL rake (20) to make the jump, and we must have
                //at least 1 point left over to actually deliver - i.e. STRICTLY MORE than 20 must
                //remain. There must also be a legal next target, and this ship's one transfer slot
                //must be free (one Blaster per ship per turn).
                //
                //Order matters: confirm affordability AND a legal target FIRST, and only THEN take
                //the one-per-ship claim. Claiming before we know a transfer can happen would burn
                //the ship's single transfer slot on a Blaster that didn't actually transfer, wrongly
                //blocking another Blaster on the same ship that could.
                $canAfford = $damageRemaining > $rakeSize;
                $nextTarget = $canAfford
                    ? $this->getNextTransferTarget($gamedata, $queueIndex, $shooter, $currentTarget, $target->id)
                    : null;
                $claimOk = ($nextTarget != null) && $this->claimTransferForTurn($shooter, $gamedata);

                if ($nextTarget == null || !$claimOk) {
                    //Can't transfer (too little damage left, no legal target, or the slot is taken).
                    //Per the rule, the final leftover rake is simply applied to the CURRENT target
                    //(wasted if it's already destroyed - raking a corpse is a no-op). noTransfer=true
                    //so it just dumps the remainder without re-triggering a structure transfer.
                    if (!$currentTarget->isDestroyed() && $damageRemaining > 0) {
                        $this->rakeOneVictim($shooter, $currentTarget, $currentOrder, $gamedata, $damageRemaining, $rakeSize, true);
                    }
                    break;
                }

                //Affordable + a legal target: pay the 20-pt cost. Whatever remains (a full or
                //partial rake) is carried over and dealt to the next target.
                $damageRemaining -= $rakeSize;

                //Build a synthetic fire order for the new victim (combat-log clarity).
                $transferOrder = $this->buildTransferFireOrder($shooter, $nextTarget, $fireOrder, $gamedata);
                $transferOrder->needed -= $transferOrder->totalIntercept;
                $transferOrder->pubnotes .= "<br>Hypergraviton Blaster beam loses 20 damage and transfers to " . $nextTarget->name . ".";

                if ($nextTarget instanceof FighterFlight) {
                    //A flight hop is resolved per-fighter by rakeOneVictim/rakeFighterFlight on the
                    //next loop pass, which owns the to-hit rolls and the shots/shotshit tally. Don't
                    //do a unit-level to-hit roll here, or the first fighter would be counted twice.
                    //Zero the constructor defaults so rakeFighterFlight's count is accurate.
                    $transferOrder->shots = 0;
                    $transferOrder->shotshit = 0;
                } else {
                    //Ship hop: one unit-level to-hit roll (with re-roll), counted as one shot.
                    $attempts = 0;
                    $transferHit = $this->rollToHitWithRakeReroll($gamedata, $transferOrder, $nextTarget, $damageRemaining, $rakeSize, false, $attempts);
                    $transferOrder->shots = max(1, $attempts);

                    if (!$transferHit) {
                        break; //missed and out of damage
                    }

                    $transferOrder->shotshit++;
                    $nextTarget->clearVreeHitSectionChoice($shooter->id, $transferOrder);
                }

                //Continue on the new victim, attributing damage to its own order.
                $currentTarget = $nextTarget;
                $currentOrder  = $transferOrder;
            }
        }

        /* Rakes a single victim one 20-pt rake at a time and stops, returning WHY it stopped:
         *   'destroyed'   - the whole unit was destroyed by a rake (transfer may follow)
         *   'structure'   - a structure block was destroyed and that target opted in to
         *                   structure-transfer (HCV+) - a voluntary transfer may follow
         *   'outOfDamage' - the volley ran out before either of the above
         * $damageRemaining is decremented by reference.
         *
         * Fighter flights are handled specially (rakeFighterFlight): each fighter gets its own
         * to-hit roll, with the standard re-roll-on-miss, until the whole flight is destroyed.
         *
         * $noTransfer=true forces it to simply rake until destroyed/out-of-damage WITHOUT
         * reporting a structure-transfer opportunity (used to dump leftover damage when no
         * transfer is possible).
         */
        protected function rakeOneVictim($shooter, $victim, $order, $gamedata, &$damageRemaining, $rakeSize, $noTransfer = false)
        {
            if ($victim instanceof FighterFlight) {
                //A flight reached as a transfer hop is never the volley's opening shot.
                return $this->rakeFighterFlight($shooter, $victim, $order, $gamedata, $damageRemaining, $rakeSize, false);
            }

            $launchPos = null; //non-ballistic Blaster

            while ($damageRemaining > 0 && !$victim->isDestroyed()) {
                $tmpLocation = $order->chosenLocation;
                if (!($tmpLocation > 0)) {
                    $tmpLocation = $victim->getHitSection($shooter, $order->turn);
                }

                $sectionStructure  = $victim->getStructureSystem($tmpLocation);
                $structureWasAlive = $sectionStructure != null && !$sectionStructure->isDestroyed();

                $rake   = min($damageRemaining, $rakeSize);
                $system = $victim->getHitSystem($shooter, $order, $this, $gamedata, $tmpLocation);
                $this->doDamage($victim, $shooter, $system, $rake, $order, $launchPos, $gamedata, false, $tmpLocation);
                $damageRemaining -= $rake;

                if ($victim->isDestroyed()) {
                    return 'destroyed';
                }

                if (!$noTransfer) {
                    $structureDestroyed = $structureWasAlive
                        && $sectionStructure != null && $sectionStructure->isDestroyed();
                    if ($structureDestroyed
                        && $this->getTransferFlagForShip($victim->id)
                        && $victim->shipSizeClass >= 2) {
                        return 'structure';
                    }
                }
            }

            return 'outOfDamage';
        }

        /* Rakes a fighter flight, giving EACH fighter its own to-hit roll (with the standard
         * re-roll-on-miss, -20 per miss) until the entire flight is destroyed or the volley
         * runs dry. Returns 'destroyed' (whole flight gone) or 'outOfDamage'.
         *
         * This routine OWNS the combat-log tally for a flight target: every to-hit attempt adds
         * to $order->shots and every fighter killed adds to $order->shotshit, so the log reads
         * e.g. 3/3 when three fighters die (rather than a misleading 1/1 from a single increment
         * in fire()). $isOpeningShot=true means this routine's very first roll is the volley's
         * opening shot, so a first-roll miss must set firstShotMissed (which bars transfer). */
        protected function rakeFighterFlight($shooter, $flight, $order, $gamedata, &$damageRemaining, $rakeSize, $isOpeningShot = true)
        {
            $launchPos = null;
            $firstRoll = $isOpeningShot;

            while ($damageRemaining > 0 && !$flight->isDestroyed()) {
                //Pick the next living fighter (getHitSystem returns a non-destroyed one).
                $fighter = $flight->getHitSystem($shooter, $order, $this, $gamedata, null);
                if ($fighter == null || $fighter->isDestroyed()) {
                    break; //nothing left to shoot at
                }

                //Fresh roll for this fighter, re-rolling on misses until it hits or damage runs dry.
                //Each fighter's whole re-roll sequence is reported as ONE shot in the combat log
                //(consistent with the ship path, where a hit-on-the-3rd-reroll is still 1 shot).
                $attempts = 0;
                $hit = $this->rollToHitWithRakeReroll($gamedata, $order, $flight, $damageRemaining, $rakeSize, $firstRoll, $attempts);
                $order->shots += max(1, $attempts);
                $firstRoll = false;

                if (!$hit) {
                    return 'outOfDamage';
                }

                //One rake destroys the fighter (fighters are tiny); apply it and count the hit.
                $rake = min($damageRemaining, $rakeSize);
                $flight->clearVreeHitSectionChoice($shooter->id, $order);
                $this->doDamage($flight, $shooter, $fighter, $rake, $order, $launchPos, $gamedata, false, null);
                $damageRemaining -= $rake;
                $order->shotshit++;
            }

            return $flight->isDestroyed() ? 'destroyed' : 'outOfDamage';
        }

        /* Returns the transferOnStructure flag for a given ship id from the parsed queue
         * (false if the ship isn't in the queue). */
        protected function getTransferFlagForShip($shipid)
        {
            foreach ($this->transferQueue as $entry) {
                if ($entry['shipid'] == $shipid) {
                    return !empty($entry['transferOnStructure']);
                }
            }
            return false;
        }

        /* Pulls the next still-valid transfer target from the queue, advancing $queueIndex
         * (by reference) past skipped/invalid entries. Returns the Ship or null.
         *
         * The beam may only transfer to a unit within ONE HEX of the PREVIOUS recipient
         * ($fromVictim) and IN ARC of the weapon (evaluated from the shooter, "at the time of
         * firing"). A candidate failing either gate is skipped (a later queue entry may still be
         * a legal hop), so the player's ordered list is honoured as a preference within the
         * geometric constraints.
         *
         * $initialTargetId is the fire order's own target. Since Stage-4 the queue's FIRST
         * entry is the initial target (carried only so getTransferFlagForShip can read its
         * structure-loss flag) — it is NOT a transfer hop, so skip it here. */
        protected function getNextTransferTarget($gamedata, &$queueIndex, $shooter, $fromVictim, $initialTargetId = null)
        {
            while ($queueIndex < count($this->transferQueue)) {
                $entry = $this->transferQueue[$queueIndex];
                $queueIndex++;
                if ($initialTargetId !== null && (int)$entry['shipid'] === (int)$initialTargetId) continue; //never hop to the initial target
                $candidate = $gamedata->getShipById($entry['shipid']);
                if ($candidate == null) continue;
                if ($candidate->isDestroyed()) continue;
                if (!$this->isLegalTransferTarget($shooter, $fromVictim, $candidate)) continue;
                return $candidate;
            }
            return null;
        }

        /* A transfer hop is legal only if the candidate is within 1 hex of the previous
         * recipient AND in the weapon's firing arc (relative to the shooter). */
        protected function isLegalTransferTarget($shooter, $fromVictim, $candidate)
        {
            //Within one hex of the previous recipient.
            if (mathlib::getDistanceHex($fromVictim, $candidate) > 1) {
                return false;
            }

            //In arc of the weapon (measured from the shooter, as at time of firing).
            $relativeBearing = $shooter->getBearingOnUnit($candidate);
            if (!mathlib::isInArc($relativeBearing, $this->startArc, $this->endArc)) {
                return false;
            }

            return true;
        }

        /* Creates and registers a synthetic addToDB fire order for a transfer victim, so the
         * transfer shot appears as its own line in the combat log. Hit chance is computed via
         * the standard calculateHitBase against the new target. */
        protected function buildTransferFireOrder($shooter, $newTarget, $originalOrder, $gamedata)
        {
            $transferOrder = new FireOrder(
                -1, "normal", $shooter->id, $newTarget->id, $this->id, -1,
                $gamedata->turn, $this->firingMode, 0, 0, 1, 0, 0, 0, 0,
                $originalOrder->damageclass
            );
            $transferOrder->addToDB = true;
            //$transferOrder->pubnotes = " Transferred Hypergraviton Blaster shot.";
            $this->fireOrders[] = $transferOrder;

            //Compute hit chance against the new victim (sets $transferOrder->needed).
            $this->calculateHitBase($gamedata, $transferOrder);

            return $transferOrder;
        }

        /* Rolls to hit, applying the miss-re-roll procedure.
         * A to-hit roll is attempted whenever ANY damage remains (even a final partial rake
         * of fewer than 20 points). On a miss, one full rake (20) is removed and we re-roll
         * if anything is still left; a partial rake that misses simply ends the attempt.
         * On a hit, whatever $damageRemaining holds (full or partial) is what gets dealt.
         * Returns true on a hit, false if the volley ran out of damage without hitting.
         * $isFirstShot only matters for setting $this->firstShotMissed (later stages).
         * $attempts is filled (by reference) with the number of to-hit rolls actually made.
         */
        protected function rollToHitWithRakeReroll($gamedata, $fireOrder, $target, &$damageRemaining, $rakeSize, $isFirstShot = false, &$attempts = 0)
        {
            $needed   = $fireOrder->needed;
            $attempts = 0;

            while ($damageRemaining > 0) {
                $attempts++;
                $rolled = Dice::d(100);
                $fireOrder->rolled = $rolled; //last roll wins, as in base fire()

                $fireOrder->notes .= " FIRING SHOT (attempt $attempts): rolled: $rolled, needed: $needed, damage left: $damageRemaining\n";

                //Mirror base fire(): a roll landing in the interception band counts as intercepted.
                if ($rolled > $needed && $rolled <= $needed + $fireOrder->totalIntercept) {
                    $fireOrder->intercepted += 1;
                }

                if ($rolled <= $needed) {
                    return true; //hit - $damageRemaining is what gets dealt (may be a partial rake)
                }

                //Miss: record first-shot miss, drop a full rake, and re-roll if anything remains.
                if ($isFirstShot && $attempts == 1) {
                    $this->firstShotMissed = true;
                }

                $damageRemaining -= $rakeSize;
                if ($damageRemaining <= 0) {
                    $damageRemaining = 0;
                    $fireOrder->notes .= " Missed and no damage remains to re-roll.\n";
                    $fireOrder->pubnotes .= "<br>Missed; out of damage to re-roll.";
                    break;
                }
                $fireOrder->pubnotes .= "<br>Missed; -20 damage and re-rolling.";
            }

            return false;
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



class GraviticMine extends Weapon{
	public $name = "GraviticMine";
	public $displayName = "Gravitic Mine";
	public $iconPath = "graviticMine.png";
	
	public $damageType = "Flash"; //But no collateral damage
    protected $noCollateral = true;
    //public $noPrimaryHits = true; //To replicate flash with no collateral 
	public $weaponClass = "Gravitic";
	public $hextarget = true;
	public $hidetarget = true;
	public $revealAfterPreFire = true; //Mine detonates at end of phase 5; reveal target to opponents from phase 3 onward.
	public $ballistic = true;
	public $uninterceptable = true;
	public $doNotIntercept = true; //just in case
	public $priority = 1;
	public $range = 40;
	public $loadingtime = 2;
	public $animation = "ball";
    public $animationColor = array(250, 251, 196);
	public $animationExplosionScale = 1;
	public $firingModes = array(
		1 => "Gravitic Mine"
	);
		
	protected static $alreadyGravMined = array(); //list of IDs of units already affected in this firing phase - to avoid multiplying effects on overlap
	
		
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);  
		$this->data["Special"] = "Ballistic weapon that targets a hex.";      
		$this->data["Special"] .= "<br>In Prefiring Phase, units within 5 hexes are pulled one hex toward the closest mine.";
		$this->data["Special"] .= "<br>If caught between overlapping mines, the units are instead sheared for damage scaled by ship size and proximity, ignoring armour.";
		$this->data["Special"] .= "<br>Terrain, fixed Enormous bases, and other mines are unaffected.";
	}	
	
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
	{
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
		if ( $maxhealth == 0 ) $maxhealth = 6;
		if ( $powerReq == 0 ) $powerReq = 6;
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
		GraviticMineHandler::register($this);
	}

	public function calculateHitBase($gamedata, $fireOrder)
	{
		//Debug::log("GraviticMine calculateHitBase called: id={$fireOrder->id} type={$fireOrder->type} target={$fireOrder->targetid} turn={$fireOrder->turn} dc=" . ($fireOrder->damageclass ?? 'null'));
		$fireOrder->needed = 100; //always true
		$fireOrder->updated = true;
	}

    public function fire($gamedata, $fireOrder)
    {
        //Debug::log("GraviticMine fire called: id={$fireOrder->id} type={$fireOrder->type} target={$fireOrder->targetid} turn={$fireOrder->turn} dc=" . ($fireOrder->damageclass ?? 'null'));
        if ($fireOrder->damageclass === 'graviticShear') {
            //Debug::log("GraviticMine fire: shear branch, chosenLoc=" . ($fireOrder->chosenLocation ?? 'null') . " x={$fireOrder->x} y={$fireOrder->y}");
            parent::fire($gamedata, $fireOrder);
            //Debug::log("GraviticMine fire: after parent::fire shots={$fireOrder->shots} shotshit={$fireOrder->shotshit} rolled={$fireOrder->rolled}");
            return;
        }
        // Per-target pull marker: created by applyMovement(), already persisted with rolled=1.
        // Animation/log only — no damage rolls, no parent::fire().
        if ($fireOrder->damageclass === 'graviticPull') {
            return;
        }

        $shooter = $gamedata->getShipById($fireOrder->shooterid);

        $movement = $shooter->getLastTurnMovement($fireOrder->turn);
        $posLaunch = $movement->position;//at moment of launch!!!

        //sometimes player does manage to target ship after all..
        if ($fireOrder->targetid != -1) {
            $targetship = $gamedata->getShipById($fireOrder->targetid);
            //insert correct target coordinates: last turns' target position
            $movement = $targetship->getLastTurnMovement($fireOrder->turn);
            $fireOrder->x = $movement->position->q;
            $fireOrder->y = $movement->position->r;
            $fireOrder->targetid = -1; //correct the error
        }

        $rolled = Dice::d(100);
        $fireOrder->rolled = $rolled; //...and hit, regardless of value rolled
		//$fireOrder->pubnotes .= "Gravitic Mine distorts space around its position. ";
		$fireOrder->shotshit++;

        $fireOrder->rolled = max(1, $fireOrder->rolled);//Marks that fire order has been handled, just in case it wasn't marked yet!
    } //endof function fire


    public function beforePreFiringOrderResolution($gamedata){
        if ($this->isDestroyed($gamedata->turn)) return;
        if ($this->isOfflineOnTurn($gamedata->turn)) return;

        $thisMine = $this->getUnit();
        if ($thisMine === null) return;
        if ($thisMine->getTurnDeployed($gamedata) > $gamedata->turn) return;

        // Promote this weapon's current-turn ballistic launch orders to "prefiring"
        // so the mine detonates in the same phase as shear damage rather than
        // waiting for the regular Firing phase. Rationale:
        //   - Mines aren't in-flight projectiles, so there is no intercept window
        //     to preserve by deferring them to Firing.
        //   - Shearing is resolved here in PreFiring; keeping mine detonations in
        //     Firing desynchronises the two effects in replay/logs.
        // We only rewrite the fire-order type. The weapon's $ballistic flag is
        // left alone so fire-order creation, hex-target deploy, and UI continue
        // to treat GraviticMine as ballistic up to the moment of resolution.
        // This runs before the $resolvedTurn guard below so every GraviticMine
        // instance promotes its own orders, not just the first one.
        foreach ($this->fireOrders as $fo) {
            if ((int)$fo->turn !== (int)$gamedata->turn) continue;
            if ($fo->type === "ballistic") {
                $fo->type = "prefiring";
                $fo->updated = true;
            }
        }

        // Cross-mine resolution runs exactly once per turn, on the first mine that gets here.
        if (GraviticMineHandler::$resolvedTurn === $gamedata->turn) return;
        GraviticMineHandler::$resolvedTurn = $gamedata->turn;

        // Build the active-mine list once.
        $activeMines = array();
        $seenFireOrderIds = array();
        foreach (GraviticMineHandler::getAllMines() as $mineWeapon) {
            $mineUnit = $mineWeapon->getUnit();
            if ($mineUnit === null) continue;
            if ($mineUnit->isDestroyed()) continue;
            if ($mineUnit->getTurnDeployed($gamedata) > $gamedata->turn) continue;
            if ($mineWeapon->isDestroyed($gamedata->turn)) continue;
            if ($mineWeapon->isOfflineOnTurn($gamedata->turn)) continue;

            foreach ($mineWeapon->fireOrders as $fo) {
                if (isset($fo->damageclass) && $fo->damageclass === 'graviticShear') continue;
                if ((int)$fo->turn !== (int)$gamedata->turn) continue; // only this turn's shots count
                if (!isset($fo->x) || !isset($fo->y)) continue;

                // The same fire order can appear on multiple weapon instances; use its DB id
                // as the dedup key so two weapons firing at the same hex both count (different ids),
                // but the same shot visible through two weapons does not (same id).
                if (isset($seenFireOrderIds[$fo->id])) continue;
                $seenFireOrderIds[$fo->id] = true;

                $minePos = new OffsetCoordinate((int)$fo->x, (int)$fo->y);
                //Debug::log("  active mine: carrier={$mineUnit->id} orderId={$fo->id} firePos=({$minePos->q},{$minePos->r})");

                $activeMines[] = array(
                    'weapon' => $mineWeapon,
                    'unit'   => $mineUnit,
                    'pos'    => $minePos,
                );
            }
        }
        if (empty($activeMines)) {
            //Debug::log("GraviticMine beforePreFiring: no active mines found, skipping");
            return;
        }
        //Debug::log("GraviticMine beforePreFiring: " . count($activeMines) . " active mine(s)");

        foreach ($gamedata->ships as $unit) {
            if ($this->isImmuneTarget($unit, $gamedata)) continue;

            $unitPos = $unit->getHexPos();
            if (!($unitPos instanceof OffsetCoordinate)) continue;
            //Debug::log("GraviticMine: checking unit id={$unit->id} name={$unit->name} pos=({$unitPos->q},{$unitPos->r})");

            // Find this unit's mines within 5 hexes.
            $minesInRange = array();
            foreach ($activeMines as $entry) {
                $dist = $entry['pos']->distanceTo($unitPos);
                //Debug::log("  vs mine id={$entry['unit']->id} pos=({$entry['pos']->q},{$entry['pos']->r}) dist={$dist}");
                if ($dist <= 5) {
                    $entry['distance'] = $dist;
                    $minesInRange[] = $entry;
                }
            }
            if (empty($minesInRange)) {
                //Debug::log("  no mines in range, skipping");
                continue;
            }

            // Sort ascending by distance so [0] is the closest.
            usort($minesInRange, function($a, $b) { return $a['distance'] <=> $b['distance']; });

            // Shearing eligibility: 2+ mines AND target lies in their zone.
            if (count($minesInRange) >= 2) {
                $minePositions = array();
                foreach ($minesInRange as $m) $minePositions[] = $m['pos'];
                $inZone = $this->isUnitInShearingZone($unitPos, $minePositions);
                //Debug::log("  shearing check: {$unit->name} with " . count($minesInRange) . " mines in range -> inZone=" . ($inZone ? 'YES' : 'NO'));
                if ($inZone) {
                    if (!isset(GraviticMineHandler::$alreadyShearedTargetIds[$unit->id])) {
                        //Debug::log("  -> applying shearing");
                        $this->applyShearing($unit, $unitPos, $minesInRange, $gamedata);
                    } else {
                        //Debug::log("  -> already sheared this turn, skipping");
                    }
                    continue;
                }
            }

            // Otherwise: pull toward closest mine (1 mine OR 2+ that don't form a zone).
            if (isset(GraviticMineHandler::$alreadyMovedTargetIds[$unit->id])) continue;

            $closestDist = $minesInRange[0]['distance'];
            $tied = array();
            foreach ($minesInRange as $m) {
                if ($m['distance'] === $closestDist) $tied[] = $m;
            }
            $chosenMine = $tied[array_rand($tied)];
            //Debug::log("  pulling toward mine id={$chosenMine['unit']->id} pos=({$chosenMine['pos']->q},{$chosenMine['pos']->r}) dist={$chosenMine['distance']}");

            $newHex = $this->pickMoveHex($unitPos, $chosenMine['pos'], $gamedata);
            if ($newHex === null) {
                //Debug::log("  pickMoveHex returned null (no valid candidates), no movement");
                continue;
            }
            //Debug::log("  moving unit to ({$newHex->q},{$newHex->r})");

            $this->applyMovement($unit, $newHex, $gamedata);
        }
    }


    private function isImmuneTarget($unit, $gamedata){
        if ($unit instanceof Terrain) return true;
        if ($unit instanceof Mine) return true;        
        if ($unit->base && $unit->Enormous) return true; // fixed bases throw off the field     
        if ($unit->isDestroyed()) return true;
        if ($unit->getTurnDeployed($gamedata) > $gamedata->turn) return true;
        if (isset($unit->shipSizeClass) && (int)$unit->shipSizeClass === 5) return true; // Terrain catch-all
        return false;
    }

    private function isImmovableHex(OffsetCoordinate $hex, $gamedata){
        $here = $gamedata->getShipsInDistance($hex, 0);
        foreach ($here as $ship) {
            if ($ship instanceof Terrain) return true;
            if (!empty($ship->Enormous)) return true;
        }
        return false;
    }

    private function getShearingFactor($unit){
        if ($unit instanceof Mine) return 0;
        if ($unit instanceof FighterFlight) return 1;
        if ($unit instanceof OSAT) return 1;
        if ($unit instanceof LCV) return 2;        
        if ($unit->Enormous) return 6;
        if (!isset($unit->shipSizeClass)) return 0;
        switch ((int)$unit->shipSizeClass) {
            case 0: return 2; // Light combat vessels
            case 1: return 3; // Medium ships
            case 2: return 4; // Heavy combat vessels
            case 3: return 5; // Capital ships
            default: return 0;
        }
    }

    private function pickMoveHex(OffsetCoordinate $unitPos, OffsetCoordinate $minePos, $gamedata){
        $currDist = $unitPos->distanceTo($minePos);
        //Debug::log("  pickMoveHex: unit=({$unitPos->q},{$unitPos->r}) mine=({$minePos->q},{$minePos->r}) currDist={$currDist}");
        if ($currDist <= 0) {
            //Debug::log("  pickMoveHex: unit is on the mine hex, no movement");
            return null;
        }

        $candidates = array();
        foreach (Mathlib::getNeighbouringHexes($unitPos, 1) as $n) {
            $hex = new OffsetCoordinate((int)$n['q'], (int)$n['r']);
            $newDist = $hex->distanceTo($minePos);
            $blocked = $this->isImmovableHex($hex, $gamedata);
            //Debug::log("    neighbour ({$hex->q},{$hex->r}) distToMine={$newDist} blocked=" . ($blocked ? 'yes' : 'no'));
            if ($newDist >= $currDist) continue;
            if ($blocked) continue;
            $candidates[] = $hex;
        }
        if (empty($candidates)) return null;
        return $candidates[array_rand($candidates)];
    }

    private function applyMovement($unit, OffsetCoordinate $newHex, $gamedata){
        $lastMove = $unit->getLastMovement();
        if ($lastMove === null) return;

        // Create a per-target "graviticPull" fire order so the moved unit appears in
        // its own incomingFire stream (replay animation matches movement.value to a
        // fire order targeting the moved ship; the original hex-target order has
        // targetid=-1 and would never match). Persist it now to grab a real DB id;
        // addToDB=false afterwards prevents PreFiringGamePhase from inserting a duplicate.
        $shooterShipId = $this->getUnit()->id;
        $pullOrder = new FireOrder(
            -1, "prefiring", $shooterShipId, $unit->id,
            $this->id, -1, $gamedata->turn, 1,
            100, 1, 1, 1, 0,
            $newHex->q, $newHex->r, 'graviticPull', 1001
        );
        $pullOrder->pubnotes = "<br>{$unit->name} is moved towards a nearby Gravitic Mine.";
        $pullOrder->addToDB = true;
        $newId = Manager::insertSingleFiringOrder($gamedata, $pullOrder);
        if ($newId) {
            $pullOrder->id = (int)$newId;
        }
        $pullOrder->addToDB = false;
        $this->fireOrders[] = $pullOrder;

        // Preserve heading/facing/speed; mark as a forced prefire shift, mirroring GravityNet.
        // The 11th arg ($value) carries the pull fire order's id so ReplayAnimationStrategy
        // can pair this movement with that order and animate the pull after the mine fires.
        $forced = new MovementOrder(null, "prefire", $newHex, 0, 0, $lastMove->speed, $lastMove->heading, $lastMove->facing, false, $gamedata->turn, $pullOrder->id, 0);
        Manager::insertSingleMovement($gamedata->id, $unit->id, $forced);
        $unit->setMovement($forced);
        GraviticMineHandler::$alreadyMovedTargetIds[$unit->id] = true;
    }

    private function applyShearing($unit, OffsetCoordinate $unitPos, array $minesInRange, $gamedata){
        $factor = $this->getShearingFactor($unit);
        //Debug::log("  applyShearing: unit={$unit->name} shearingFactor={$factor}");
        if ($factor <= 0) {
            //Debug::log("  applyShearing: factor=0, no damage (immune/unknown class)");
            // Tagged anyway so the once-per-turn guard holds even for mines/zero-factor units.
            GraviticMineHandler::$alreadyShearedTargetIds[$unit->id] = true;
            return;
        }

        $nearestDist = $minesInRange[0]['distance'];
        $damage = ((int)$nearestDist + 1) * $factor;
        //Debug::log("  applyShearing: nearestDist={$nearestDist} factor={$factor} damage={$damage}");

        // Furthest mine within 5 hexes determines the impact side; randomise on tie.
        $furthestDist = $minesInRange[count($minesInRange) - 1]['distance'];
        $tiedFurthest = array();
        foreach ($minesInRange as $m) {
            if ($m['distance'] === $furthestDist) $tiedFurthest[] = $m;
        }
        $furthestMine = $tiedFurthest[array_rand($tiedFurthest)];
        $sourceHex = $furthestMine['pos'];

        $lastMove = $unit->getLastMovement();
        $facing = ($lastMove !== null) ? $lastMove->getFacingAngle() : 0;
        $relBearing = $this->computeRelativeBearing($unitPos, $sourceHex, $unit, $facing);
        $hitLocation = $this->getHitLocation($relBearing, $unit);

        // Stash precomputed damage/source for fire() / getDamage() to read.
        GraviticMineHandler::$shearDamageByTargetId[$unit->id]    = $damage;
        GraviticMineHandler::$shearSourceHexByTargetId[$unit->id] = $sourceHex;

        // Append to $this->fireOrders (mirroring AoE proximity mine pattern) so
        // preparePreFiring's second loop sees the order on the weapon it is
        // currently iterating — avoids any cross-instance visibility issues.
        $shooterShipId = $this->getUnit()->id;
        $shearOrder = new FireOrder(
            -1, "prefiring", $shooterShipId, $unit->id,
            $this->id, -1, $gamedata->turn, 1,
            100, 0, 1, 0, 0,
            $sourceHex->q, $sourceHex->r, 'graviticShear', 1000
        );
        $shearOrder->chosenLocation = $hitLocation;
        $shearOrder->pubnotes = " Sheared by conflicting Gravitic Mines.";
        $shearOrder->addToDB = true;
        $this->fireOrders[] = $shearOrder;

        GraviticMineHandler::$alreadyShearedTargetIds[$unit->id] = true;
    }

    private function computeRelativeBearing(OffsetCoordinate $unitPos, OffsetCoordinate $sourceHex, $unit, $facing){
        $oPos = Mathlib::hexCoToPixel($unitPos);
        $tPos = Mathlib::hexCoToPixel($sourceHex);
        $compassHeading = Mathlib::getCompassHeadingOfPoint($oPos, $tPos);
        $relativeBearing = Mathlib::addToDirection($compassHeading, -$facing);
        if (Movement::isRolled($unit)) {
            if ($relativeBearing != 0) $relativeBearing = 360 - $relativeBearing;
        }
        return round($relativeBearing);
    }

    private function getHitLocation($relativeBearing, $target){
        if (!method_exists($target, 'getLocations')) return 0;
        foreach ($target->getLocations() as $location) {
            $min = $location["min"];
            $max = $location["max"];
            if ($min < $max && $relativeBearing >= $min && $relativeBearing < $max) return $location["loc"];
            if ($min > $max && ($relativeBearing >= $min || $relativeBearing < $max)) return $location["loc"];
        }
        return 0;
    }

    private function isUnitInShearingZone(OffsetCoordinate $unitPos, array $minePositions){
        $count = count($minePositions);
        if ($count < 2) return false;

        $unitPx = Mathlib::hexCoToPixel($unitPos);
        $minePx = array();
        foreach ($minePositions as $p) $minePx[] = Mathlib::hexCoToPixel($p);

        //Debug::log("    isUnitInShearingZone: unitHex=({$unitPos->q},{$unitPos->r}) unitPx=({$unitPx['x']},{$unitPx['y']})");
        //foreach ($minePositions as $i => $p) {
        //    Debug::log("      mine[$i] hex=({$p->q},{$p->r}) px=({$minePx[$i]['x']},{$minePx[$i]['y']})");
        //}

        if ($count === 2) {
            // Line touches the unit's hex iff perpendicular distance ≤ ½ hex-width
            // (Mathlib::hexCoToPixel uses unit hex-width = sqrt(3), so half-width = sqrt(3)/2).
            $tolerance = sqrt(3) / 2;
            $dist = $this->pointToSegmentDistance($unitPx, $minePx[0], $minePx[1]);
            //Debug::log("    2-mine check: segDist={$dist} tolerance={$tolerance} inZone=" . ($dist <= $tolerance ? 'YES' : 'NO'));
            return $dist <= $tolerance;
        }

        // 3+ mines: convex hull point-in-polygon.
        $hull = $this->convexHull($minePx);
        $inside = $this->pointInPolygon($unitPx, $hull);
        //Debug::log("    3+-mine check: hullSize=" . count($hull) . " inside=" . ($inside ? 'YES' : 'NO'));
        return $inside;
    }

    private function pointToSegmentDistance($p, $a, $b){
        $abx = $b['x'] - $a['x'];
        $aby = $b['y'] - $a['y'];
        $lenSq = $abx * $abx + $aby * $aby;
        if ($lenSq <= 1e-9) {
            return sqrt(($p['x'] - $a['x']) ** 2 + ($p['y'] - $a['y']) ** 2);
        }
        $t = (($p['x'] - $a['x']) * $abx + ($p['y'] - $a['y']) * $aby) / $lenSq;
        $t = max(0, min(1, $t));
        $cx = $a['x'] + $t * $abx;
        $cy = $a['y'] + $t * $aby;
        return sqrt(($p['x'] - $cx) ** 2 + ($p['y'] - $cy) ** 2);
    }

    private function convexHull(array $points){
        $n = count($points);
        if ($n < 3) return $points;
        usort($points, function($a, $b) {
            if ($a['x'] !== $b['x']) return $a['x'] <=> $b['x'];
            return $a['y'] <=> $b['y'];
        });
        $cross = function($O, $A, $B) {
            return ($A['x'] - $O['x']) * ($B['y'] - $O['y']) - ($A['y'] - $O['y']) * ($B['x'] - $O['x']);
        };
        $lower = array();
        foreach ($points as $p) {
            while (count($lower) >= 2 && $cross($lower[count($lower) - 2], $lower[count($lower) - 1], $p) <= 0) {
                array_pop($lower);
            }
            $lower[] = $p;
        }
        $upper = array();
        foreach (array_reverse($points) as $p) {
            while (count($upper) >= 2 && $cross($upper[count($upper) - 2], $upper[count($upper) - 1], $p) <= 0) {
                array_pop($upper);
            }
            $upper[] = $p;
        }
        array_pop($lower);
        array_pop($upper);
        return array_merge($lower, $upper);
    }

    private function pointInPolygon($p, array $polygon){
        $n = count($polygon);
        if ($n < 3) return false;
        $inside = false;
        for ($i = 0, $j = $n - 1; $i < $n; $j = $i++) {
            $xi = $polygon[$i]['x']; $yi = $polygon[$i]['y'];
            $xj = $polygon[$j]['x']; $yj = $polygon[$j]['y'];
            $denom = ($yj - $yi);
            if ($denom == 0) $denom = 1e-9;
            $intersect = (($yi > $p['y']) !== ($yj > $p['y'])) &&
                         ($p['x'] < ($xj - $xi) * ($p['y'] - $yi) / $denom + $xi);
            if ($intersect) $inside = !$inside;
        }
        return $inside;
    }

    public function getSystemArmourBase($target, $system, $gamedata, $fireOrder, $pos = null){
        if ($fireOrder->damageclass === 'graviticShear') return 0; // Shearing ignores armor (Molecular Slicer pattern).
        return parent::getSystemArmourBase($target, $system, $gamedata, $fireOrder, $pos);
    }


        public function getDamage($fireOrder){
            if ($fireOrder->damageclass === 'graviticShear') {
                $dmg = GraviticMineHandler::$shearDamageByTargetId[$fireOrder->targetid] ?? 0;
                //Debug::log("GraviticMine getDamage: shear order target={$fireOrder->targetid} returning dmg={$dmg} (registry keys: " . implode(',', array_keys(GraviticMineHandler::$shearDamageByTargetId)) . ")");
                return $dmg;
            }
            return 0; /*no actual damage, just various effects*/
        }
        public function setMinDamage(){     $this->minDamage = 0 ;      }
        public function setMaxDamage(){     $this->maxDamage = 0 ;      }

}//endof class GraviticMine


class GraviticMineHandler {
    private static $mines = array();

    // Per-turn guards / data carried across the beforePreFiringOrderResolution → fire pipeline.
    public static $resolvedTurn = -1;
    public static $alreadyMovedTargetIds = array();
    public static $alreadyShearedTargetIds = array();
    public static $shearDamageByTargetId = array();
    public static $shearSourceHexByTargetId = array();

    public static function register($weapon){
        self::$mines[] = $weapon;
    }

    public static function getAllMines(){
        return self::$mines;
    }
}

?>
