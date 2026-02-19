<?php

    class Raking extends Weapon{
        public $raking = 10; //rake size
        public $priority = 8;
        public $damageType = "Raking"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
        public $weaponClass = "Laser"; //MANDATORY (first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!
        //public $animationExplosionScale = 0.5; //appropriate for heavy Raking weapons
        
        public $firingModes = array( 1 => "Raking");
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			if ($this->raking != 10) {//inform about non-standard rake size
				$this->data["Special"] = "Does ".$this->raking." damage per rake.";
			}
		}	    
        
        
    } //endof class Raking
    

    class Laser extends Raking{
        public $uninterceptable = true;
				
		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
            if (!isset($this->data["Special"])) {
                $this->data["Special"] = '';
            }else{
                $this->data["Special"] .= '<br>';
            }	    
            $this->data["Special"] .= "Uninterceptable.";
            if($this->overloadable){
            	$this->data["Special"] .= "<br>Can be overcharged during Initial Orders to fire in Sustained mode AFTER a full recharge cycle.";
                $this->data["Special"] .= '<br>When firing in Sustained mode, if the first shot hits, the next turns shot will hit the same target automatically.';
                $this->data["Special"] .= '<br>Subsequent Sustained shots ignore any armour/shields that have applied to first shot.';                             
            } 
		}
    }


    class HeavyLaser extends Laser{
        public $name = "heavyLaser";
        public $displayName = "Heavy Laser";
        public $animation = "laser";
        public $animationColor = array(230, 0, 0);
        //public $animationExplosionScale = 0.5;
        //public $animationWidth = 4;
        //public $animationWidth2 = 0.2;

        public $loadingtime = 4;
        public $overloadable = true;
        public $extraoverloadshots = 2;

        public $raking = 10;
        public $priority = 7;
        
        public $rangePenalty = 0.33;
        public $fireControl = array(-4, 2, 3); // fighters, <mediums, <capitals 

        private $sustainedTarget = array(); //To track for next turn which ship was fired at in Sustained Mode and whether it was hit.
        private $sustainedSystemsHit = array(); //For tracking systems that were hit and how much armour they should be reduced by following turn if hit again.         
    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
/*
        public function setSystemDataWindow($turn){			
            parent::setSystemDataWindow($turn);        
			if (!isset($this->data["Special"])) {
				$this->data["Special"] = '';
			}else{
				$this->data["Special"] .= '<br>';
			}
*/        
                
        public function calculateHitBase(TacGamedata $gamedata, FireOrder $fireOrder) {
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

        public function getDamage($fireOrder){        return Dice::d(10, 4)+20;   }
        public function setMinDamage(){     $this->minDamage = 24 ;      }
        public function setMaxDamage(){     $this->maxDamage = 60 ;      }       
        
    }


    
    class MediumLaser extends Laser{        
        public $name = "mediumLaser";
        public $displayName = "Medium Laser";
        public $animation = "laser";
        //public $animationColor = array(255, 79, 15);
        public $animationColor = array(179, 45, 0); //same as Heavy Laser
        //public $animationExplosionScale = 0.4;
        //public $animationWidth = 3;
        //public $animationWidth2 = 0.3;
        public $priority = 8;
        
        public $loadingtime = 3;
        
        public $raking = 10;
        
        public $rangePenalty = 0.5;
        public $fireControl = array(-3, 2, 3); // fighters, <mediums, <capitals 
    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return Dice::d(10, 3)+12;   }
        public function setMinDamage(){     $this->minDamage = 15 ;      }
        public function setMaxDamage(){     $this->maxDamage = 42 ;      }    
    }

    
    class LightLaser extends Laser{
        public $name = "lightLaser";
        public $displayName = "Light Laser";
        public $animation = "laser";
        //public $animationColor = array(255, 79, 15);
        public $animationColor = array(179, 45, 0);  //same as heavy laser
        //public $animationExplosionScale = 0.3;
        //public $animationWidth = 2;
        //public $animationWidth2 = 0.2;
        
        public $loadingtime = 2;
        public $priority = 8;
        
        public $raking = 10;

        public $rangePenalty = 1;
        public $fireControl = array(-2, 1, 2); // fighters, <mediums, <capitals 
    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return Dice::d(10, 2)+7;   }
        public function setMinDamage(){     $this->minDamage = 9 ;      }
        public function setMaxDamage(){     $this->maxDamage = 27 ;      }
    }
    

    class BattleLaser extends Laser{
        public $name = "battleLaser";
        public $displayName = "Battle Laser";
        public $animation = "laser";
        public $animationColor = array(255, 58, 31);
		
        //public $animationExplosionScale = 0.45;
        //public $animationWidth = 4;
        //public $animationWidth2 = 0.2;
        
        public $loadingtime = 3;

        public $raking = 10;
        public $priority = 7;
        public $priorityArray = array(1=>7, 2=>2); //Piercing shots go early, to do damage while sections aren't detroyed yet!
        
        public $firingModes = array(
            1 => "Raking",
            2 => "Piercing"
        );
        
        public $damageTypeArray=array(1=>'Raking', 2=>'Piercing');
        //public $damageType = $this->damageTypeArray[1]; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
        public $weaponClass = "Laser"; //MANDATORY (first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!
        
        
        
                
        public $rangePenalty = 0.25; //-1/4 hexes
		public $fireControl = array(-3, 3, 4);
        public $fireControlArray = array( 1=>array(-3, 3, 4), 2=>array(null,-1,0) ); //Raking and Piercing mode
    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            if ( $maxhealth == 0 ) $maxhealth = 6;
            if ( $powerReq == 0 ) $powerReq = 6;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

		public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
			$this->data["Special"] = 'Uninterceptable.';
			$this->data["Special"] .= '<br>Can also fire in Piercing Mode.';			
		}
        
        public function getDamage($fireOrder){        return Dice::d(10, 4)+12;   }
        public function setMinDamage(){     $this->minDamage = 16 ;      }
        public function setMaxDamage(){     $this->maxDamage = 52 ;      }
        
    } //endof class BattleLaser


    
    class AssaultLaser extends Laser{
        public $name = "assaultLaser";
        public $displayName = "Assault Laser";
        public $animation = "laser";
        public $animationColor = array(255, 58, 31);//same as Battle Laser
        //public $animationExplosionScale = 0.3;
        //public $animationWidth = 3;
        //public $animationWidth2 = 0.35;
        public $priority = 8;
        
        public $loadingtime = 2;
                
        public $rangePenalty = 0.33;
        public $fireControl = array(-4, 3, 3); // fighters, <mediums, <capitals 
    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return Dice::d(10, 3)+4;   }
        public function setMinDamage(){     $this->minDamage = 7 ;      }
        public function setMaxDamage(){     $this->maxDamage = 34 ;      }
    }

//CUSTOM weapon
    class HvyAssaultLaser extends Laser{        
        public $name = "HvyAssaultLaser";
        public $displayName = "Heavy Assault Laser";
        public $animation = "laser";
        public $animationColor = array(255, 58, 31);//same as Assault Laser
        public $animationExplosionScale = 0.35;
        
        public $loadingtime = 3;
        
        public $rangePenalty = 0.33;
        public $fireControl = array(-4, 3, 3); // fighters, <mediums, <capitals 
    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
	    //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 8;
            }
            if ( $powerReq == 0 ){
                $powerReq = 62;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
		}

        public function getDamage($fireOrder){        return Dice::d(10, 5)+4;   }
        public function setMinDamage(){     $this->minDamage = 9 ;      }
        public function setMaxDamage(){     $this->maxDamage = 54 ;      }

    }    

//CUSTOM weapon
    class AdvancedAssaultLaser extends Laser{        
        public $name = "advancedAssaultLaser";
        public $displayName = "Advanced Assault Laser";
        public $animation = "laser";
        public $animationColor = array(255, 58, 31);//same as Assault Laser
        //public $animationColor = array(255, 11, 115);
        //public $animationWidth = 4;
        //public $animationWidth2 = 0.4;
        public $animationExplosionScale = 0.35;
        
        public $loadingtime = 2;
        
        public $rangePenalty = 0.33;
        public $fireControl = array(-3, 4, 4); // fighters, <mediums, <capitals 
    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return Dice::d(10, 3)+10;   }
        public function setMinDamage(){     $this->minDamage = 13 ;      }
        public function setMaxDamage(){     $this->maxDamage = 40 ;      }
    }
    

    class NeutronLaser extends Laser{
            public $name = "neutronLaser";
            public $displayName = "Neutron Laser";
            public $animation = "laser";
            public $animationColor = array(175, 225, 175);
            //public $animationWidth = 4;
            //public $animationWidth2 = 0.4;
            //public $animationExplosionScale = 0.5;

            public $loadingtime = 3;
            public $overloadable = true;

            public $priority = 7;
            public $priorityArray = array(1=>7, 2=>2); //Piercing shots go early, to do damage while sections aren't detroyed yet!

            public $firingModes = array(
                1 => "Raking",
                2 => "Piercing"
            );
        
            public $damageTypeArray=array(1=>'Raking', 2=>'Piercing');
            public $fireControlArray = array( 1=>array(1, 4, 4), 2=>array(null,0,0) ); //Raking and Piercing mode
        
            public $extraoverloadshots = 2;        
            public $extraoverloadshotsArray = array(1=>2, 2=>0); //extra shots from overload are relevant only for Raking mode!

            public $rangePenalty = 0.25;

            private $sustainedTarget = array(); //To track for next turn which ship was fired at in Sustained Mode and whether it was hit.
            private $sustainedSystemsHit = array(); //For tracking systems that were hit and how much armour they should be reduced by following turn if hit again.             

            function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
                parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
            }

            public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
                $this->data["Special"] = 'Uninterceptable.';
                $this->data["Special"] .= '<br>Can also fire in Piercing Mode.';
                $this->data["Special"] .= "<br>Can be overcharged during Initial Orders to fire in Sustained mode AFTER a full recharge cycle.";
                $this->data["Special"] .= '<br>When firing in Sustained mode, if the first shot hits, the next turns shot will hit the same target automatically.';
                $this->data["Special"] .= '<br>Subsequent Sustained shots ignore any armour/shields that have applied to first shot.';   			
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


            public function getDamage($fireOrder){ return Dice::d(10, 4)+15; }
            public function setMinDamage(){ $this->minDamage = 19 ; }
            public function setMaxDamage(){ $this->maxDamage = 55 ; }
    }



    class ImprovedNeutronLaser extends Laser{
        public $name = "improvedNeutronLaser";
        public $displayName = "Improved Neutron Laser";
        public $iconPath = "neutronLaser.png";
        public $animation = "laser";
        public $animationColor = array(175, 225, 175);
        //public $animationWidth = 5;
        //public $animationWidth2 = 0.5;
        //public $animationExplosionScale = 0.5;

        public $loadingtime = 3;
        public $overloadable = true;
        public $priority = 7;
        public $priorityArray = array(1=>7, 2=>2); //Piercing shots go early, to do damage while sections aren't detroyed yet!

        private $sustainedTarget = array(); //To track for next turn which ship was fired at in Sustained Mode and whether it was hit.
        private $sustainedSystemsHit = array(); //For tracking systems that were hit and how much armour they should be reduced by following turn if hit again. 

            public $firingModes = array(
                1 => "Raking",
                2 => "Piercing"
            );
        
        public $damageTypeArray=array(1=>'Raking', 2=>'Piercing');
        public $fireControlArray = array( 1=>array(1, 4, 5), 2=>array(null,0,1) ); //Raking and Piercing mode
        
        public $extraoverloadshots = 3; //3 turns firing in sustained mode.       
        public $extraoverloadshotsArray = array(1=>3, 2=>0); //extra shots from overload are relevant only for Raking mode!

        public $rangePenalty = 0.25;

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

		public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
			$this->data["Special"] = 'Uninterceptable.';
			$this->data["Special"] .= '<br>Can also fire in Piercing Mode.';
            $this->data["Special"] .= "<br>Can be overcharged during Initial Orders to fire in Sustained mode AFTER a full recharge cycle.";
            $this->data["Special"] .= '<br>When firing in Sustained mode, if the first shot hits, the next turns shot will hit the same target automatically.';
            $this->data["Special"] .= '<br>Subsequent Sustained shots ignore any armour/shields that have applied to first shot(s).';   		
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
                if ($currNote->turn == $gamedata->turn - 1) { // Get target, if hit with latest shot and systems hit from last turn.               
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
                if ($currNote->turn == $gamedata->turn - 2) { // Can Sustain for 3 turns, look at last two turn’s notes for systems hit. 
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

        public function getDamage($fireOrder){ return Dice::d(10, 4)+18; }
        public function setMinDamage(){ $this->minDamage = 22 ; }
        public function setMaxDamage(){ $this->maxDamage = 58; }
    }

   
    class PowerLaser extends NeutronLaser{
            public $name = "PowerLaser";
            public $displayName = "Power Laser";
            public $animation = "laser";
            public $animationColor = array(255, 255, 0);

            public $loadingtime = 2;
            public $raking = 15;

            public $priority = 7;
            public $priorityArray = array(1=>7, 2=>2); //Piercing shots go early, to do damage while sections aren't detroyed yet!

            public $firingModes = array(
                1 => "Raking",
                2 => "Piercing"
            );
        
            public $damageTypeArray=array(1=>'Raking', 2=>'Piercing');
            public $fireControlArray = array( 1=>array(4, 5, 6), 2=>array(null,1,2) ); //Raking and Piercing mode
        
            public $rangePenalty = 0.25;

            public $overloadable = true;
        
            public $extraoverloadshots = 2;        
            public $extraoverloadshotsArray = array(1=>2, 2=>0); //extra shots from overload are relevant only for Raking mode!

            private $sustainedTarget = array(); //To track for next turn which ship was fired at in Sustained Mode and whether it was hit.
            private $sustainedSystemsHit = array(); //For tracking systems that were hit and how much armour they should be reduced by following turn if hit again.             
           

            function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
                if ( $maxhealth == 0 ){
                    $maxhealth = 15;
                }
                if ( $powerReq == 0 ){
                    $powerReq = 7; 
                }                   
                parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
            }


            public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
                $this->data["Special"] = 'Uninterceptable.';
				$this->data["Special"] .= "Does ".$this->raking." damage per rake.";                
                $this->data["Special"] .= '<br>Can also fire in Piercing Mode.';
                $this->data["Special"] .= "<br>Can be overcharged during Initial Orders to fire in Sustained mode AFTER a full recharge cycle.";
                $this->data["Special"] .= '<br>When firing in Sustained mode, if the first shot hits, the next turns shot will hit the same target automatically.';
                $this->data["Special"] .= '<br>Subsequent Sustained shots ignore any armour/shields that have applied to first shot.';   			
            }

            public function getDamage($fireOrder){ return Dice::d(10, 8)+18; }
            public function setMinDamage(){ $this->minDamage = 26 ; }
            public function setMaxDamage(){ $this->maxDamage = 98 ; }
    }    


    class MedPowerLaser extends NeutronLaser{
            public $name = "MedPowerLaser";
            public $displayName = "Medium Power Laser";
            public $animation = "laser";
            public $animationColor = array(255, 255, 0);

            public $loadingtime = 2;
            public $raking = 15;
            public $priority = 5;
            public $priorityArray = array(1=>5, 2=>2); //Piercing shots go early, to do damage while sections aren't detroyed yet!

            public $firingModes = array(
                1 => "Raking",
                2 => "Piercing"
            );
        
            public $damageTypeArray=array(1=>'Raking', 2=>'Piercing');
            public $fireControlArray = array( 1=>array(3, 4, 5), 2=>array(null,0,1) ); //Raking and Piercing mode
        
            public $rangePenalty = 0.25;
           

            function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
                if ( $maxhealth == 0 ){
                    $maxhealth = 9;
                }
                if ( $powerReq == 0 ){
                    $powerReq = 5; 
                }                   
                parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
            }


            public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
                $this->data["Special"] = 'Uninterceptable.';
				$this->data["Special"] .= "Does ".$this->raking." damage per rake.";                   
                $this->data["Special"] .= '<br>Can also fire in Piercing Mode.';
                $this->data["Special"] .= "<br>Can be overcharged during Initial Orders to fire in Sustained mode AFTER a full recharge cycle.";
                $this->data["Special"] .= '<br>When firing in Sustained mode, if the first shot hits, the next turns shot will hit the same target automatically.';
                $this->data["Special"] .= '<br>Subsequent Sustained shots ignore any armour/shields that have applied to first shot.';   			
            }

            public function getDamage($fireOrder){ return Dice::d(10, 4)+10; }
            public function setMinDamage(){ $this->minDamage = 14 ; }
            public function setMaxDamage(){ $this->maxDamage = 50 ; }
    }    


    class LaserLance extends HeavyLaser{

        public $name = "laserLance";
        public $displayName = "Laser Lance";
        public $animation = "laser";
        public $animationColor = array(220, 100, 11);
        //public $animationWidth = 3;
        // public $animationWidth2 = 0.3;
        //public $animationExplosionScale = 0.35;
        public $priority = 8;
        public $priorityArray = array(1=>8, 2=>2); //Piercing shots go early, to do damage while sections aren't detroyed yet!

        public $loadingtime = 3;
        public $overloadable = false;

        public $raking = 10;

            public $firingModes = array(
                1 => "Raking",
                2 => "Piercing"
            );
        
            public $damageTypeArray=array(1=>'Raking', 2=>'Piercing');
            public $fireControlArray = array( 1=>array(-5, 3, 3), 2=>array(null,-1,-1) ); //Raking and Piercing mode

        public $rangePenalty = 0.5;
        //public $fireControl = array(-5, 3, 3); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(10, 3)+6; }
        public function setMinDamage(){ $this->minDamage = 9 ; }
        public function setMaxDamage(){ $this->maxDamage = 36 ; }
        }


    class HeavyLaserLance extends LaserLance{
        public $name = "heavyLaserLance";
        public $displayName = "Heavy Laser Lance";
        public $animationColor = array(220, 100, 11);
        //public $animationWidth = 4;
        //public $animationWidth2 = 0.6;
        //public $animationExplosionScale = 0.45;

        public $loadingtime = 4;

        public $priority = 7;
        public $priorityArray = array(1=>7, 2=>2); //Piercing shots go early, to do damage while sections aren't detroyed yet!

            public $firingModes = array(
                1 => "Raking",
                2 => "Piercing"
            );
        
            public $damageTypeArray=array(1=>'Raking', 2=>'Piercing');
            public $fireControlArray = array( 1=>array(-5, 3, 3), 2=>array(null,-1,-1) ); //Raking and Piercing mode

        public $rangePenalty = 0.5;
        //public $fireControl = array(-5, 3, 3); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(10, 4)+10; }
        public function setMinDamage(){ $this->minDamage = 14 ; }
        public function setMaxDamage(){ $this->maxDamage = 50 ; }
    }



    class TacLaser extends Laser{
        public $name = "tacLaser";
        public $displayName = "Tactical Laser";
        public $animation = "laser";
        //public $animationColor = array(220, 60, 120);
        public $animationColor = array(235, 30, 30); //let's make it similar to Battle Laser but less intense
        //public $animationWidth = 3;
        //public $animationWidth2 = 0.2;
        //public $animationExplosionScale = 0.25;
        public $priority = 8;

        public $loadingtime = 2;

        public $raking = 10;

        public $rangePenalty = 0.5;
        public $fireControl = array(-5, 1, 2); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(10, 2)+8; }
        public function setMinDamage(){ $this->minDamage = 10 ; }
        public function setMaxDamage(){ $this->maxDamage = 28 ; }
    }


class CustomStrikeLaser extends Weapon{
    /*actually StrikeLaser is official weapon, but as Custom version was already in wider use - class name was retained*/
        public $name = "customStrikeLaser";
        public $displayName = "Strike Laser";
	    public $iconPath = "customStrikeLaser.png";
		
        //public $animation = "laser";
		public $animation = "bolt";//a bolt, not a beam
        public $animationColor = array(255, 30, 30);
        //public $animationExplosionScale = 0.4;
		
        public $fireControl = array(0, 2, 2); // fighters, <mediums, <capitals
        public $priority = 6; //heavy Standard weapon    
        public $loadingtime = 3;
        public $rangePenalty = 0.5; //-1/2 hexes
		
        public $uninterceptable = true;
	    public $damageType = 'Standard'; 
    	public $weaponClass = "Laser"; 


        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
			//maxhealth and power reqirement are fixed; left option to override with hand-written values
			if ( $maxhealth == 0 ){
				$maxhealth = 5;
			}
			if ( $powerReq == 0 ){
				$powerReq = 4;
			}
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
        public function setSystemDataWindow($turn)
        {
            parent::setSystemDataWindow($turn);
			if (!isset($this->data["Special"])) {
				$this->data["Special"] = '';
			}else{
				$this->data["Special"] .= '<br>';
			}
			$this->data["Special"] .= "Uninterceptable.";      
		}

        public function getDamage($fireOrder){ return Dice::d(10, 2)+8; }
        public function setMinDamage(){ $this->minDamage = 10 ; }
        public function setMaxDamage(){ $this->maxDamage = 28 ; }
}//CustomStrikeLaser




    class ImperialLaser extends Laser{
        public $name = "imperialLaser";
        public $displayName = "Imperial Laser";
        public $animation = "laser";
        //public $animationColor = array(172, 0, 230);		
        public $animationColor = array(235, 30, 30); //let's make it similar to Battle Laser but less intense
        //public $animationExplosionScale = 0.45;
        //public $animationWidth = 5;
        //public $animationWidth2 = 0.5;
		
        public $priority = 7;

        public $loadingtime = 4;

        public $raking = 10;

        public $rangePenalty = 0.33;
        public $fireControl = array(-5, 2, 3); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(10, 4)+8; }
        public function setMinDamage(){ $this->minDamage = 12 ; }
        public function setMaxDamage(){ $this->maxDamage = 48 ; }
    }




    class BlastLaser extends Weapon{
        public $name = "BlastLaser";
        public $displayName = "Blast Laser";
        public $iconPath = "improvedBlastLaser.png"; //can have the same icon all right
		
        public $animationColor = array(255, 50, 50);
        public $animation = "bolt"; //a bolt, not beam
        //public $animationExplosionScale = 0.5;
        //public $projectilespeed = 17;
        //public $animationWidth = 25;
        //public $trailLength = 25;
        public $priority = 6; //heavy Standard weapons
		
		public $noPrimaryHits = true;//cannot penetrate to PRIMARY on outer hits
        public $loadingtime = 3;

        public $rangePenalty = 0.33; //-1/3 hexes
        public $fireControl = array(0, 2, 4); // fighters, <mediums, <capitals

        public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
        public $weaponClass = "Laser";		
		
        public $uninterceptable = true;
        
        

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            if ( $maxhealth == 0 ) $maxhealth = 10;
            if ( $powerReq == 0 ) $powerReq = 5;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
		public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
			$this->data["Special"] = 'Uninterceptable.'; 
			$this->data["Special"] .= '<br>Cannot penetrate to PRIMARY when hitting outer section.';
		}

        public function getDamage($fireOrder){ return Dice::d(10, 2)+14; }
        public function setMinDamage(){ $this->minDamage = 16 ; }
        public function setMaxDamage(){ $this->maxDamage = 34 ; }

    } //endof class BlastLaser


    class ImprovedBlastLaser extends Weapon{
        public $name = "improvedBlastLaser";
        public $displayName = "Improved Blast Laser";
        public $iconPath = "improvedBlastLaser.png";
		
        public $animationColor = array(255, 50, 50);
        public $animation = "beam"; //a bolt, not beam
        //public $animationExplosionScale = 0.6;
        //public $projectilespeed = 17;
        //public $animationWidth = 30;
        //public $trailLength = 30;
		
        public $priority = 6; //heavy Standard weapons
		
		public $noPrimaryHits = true;//cannot penetrate to PRIMARY on outer hits
        public $loadingtime = 3;


        public $rangePenalty = 0.33;
        public $fireControl = array(-1, 3, 5); // fighters, <mediums, <capitals

        public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
        public $weaponClass = "Laser";
        
        public $uninterceptable = true;
        

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            if ( $maxhealth == 0 ) $maxhealth = 10;
            if ( $powerReq == 0 ) $powerReq = 8;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
		public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
			$this->data["Special"] = 'Uninterceptable.';
			$this->data["Special"] .= '<br>Cannot penetrate to PRIMARY when hitting outer section.';
		}

        public function getDamage($fireOrder){ return Dice::d(10, 3)+14; }
        public function setMinDamage(){ $this->minDamage = 17 ; }
        public function setMaxDamage(){ $this->maxDamage = 44 ; }

    } //endof class ImprovedBlastLaser




    class CombatLaser extends Laser{
        /*Abbai variant of Battle Laser - always piercing*/
        public $name = "CombatLaser";
        public $displayName = "Combat Laser";        
	    public $iconPath = "battleLaser.png";
		
        public $animation = "laser";
        public $animationColor = array(255, 70, 45);//same as Battle Laser, but a touch more intense
        //public $animationExplosionScale = 0.45;
        //public $animationWidth = 3;
        //public $animationWidth2 = 0.2;
        
        public $loadingtime = 3;

        public $raking = 10;
        public $priority = 2; //Piercing shots go early, to do damage while sections aren't detroyed yet!
        
        public $firingModes = array(
            1 => "Piercing"
        );
        public $damageType = 'Piercing';
        public $weaponClass = "Laser"; //MANDATORY (first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!
        
                
        public $rangePenalty = 0.33; //-1/3 hexes
        public $fireControl = array(-2, 3, 3); // fighters, <mediums, <capitals 
    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 7;
            }
            if ( $powerReq == 0 ){
                $powerReq = 7;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){
			return Dice::d(10, 3)+20;   
		}
        public function setMinDamage(){     $this->minDamage = 23 ;      }
        public function setMaxDamage(){     $this->maxDamage = 50 ;      }
        
    } //endof class CombatLaser





    class LaserCutter extends Laser{
        /*Abbai weapon*/
        public $name = "LaserCutter";
        public $displayName = "Laser Cutter";  
	    public $iconPath = "graviticCutter.png";
	    
        public $animation = "laser";
		//public $animationColor = array(255, 91, 91);
        public $animationColor = array(200, 45, 0); //let's make it similar to regular Laser line, but less intense
        //public $animationExplosionScale = 0.35;
        //public $animationWidth = 3;
        //public $animationWidth2 = 0.3;
		
        public $priority = 8;
        
        public $loadingtime = 3;
        
        public $raking = 6;
        
        public $rangePenalty = 0.5; //-1/2 hexes
        public $fireControl = array(-2, 1, 2); // fighters, <mediums, <capitals 
    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
	    //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 6;
            }
            if ( $powerReq == 0 ){
                $powerReq = 4;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return Dice::d(10, 4)+2;   }
        public function setMinDamage(){     $this->minDamage = 6 ;      }
        public function setMaxDamage(){     $this->maxDamage = 42 ;      }
    } //endof class LaserCutter


/*Torata weapon*/
class LaserAccelerator extends Laser{
		public $name = "LaserAccelerator";
        public $displayName = "Laser Accelerator";
        public $iconPath = "LaserAccelerator.png";
        public $animation = "laser";
        //public $animationColor = array(230, 0, 0);
        public $animationColor = array(179, 45, 0); //same as heavy laser
        //public $animationExplosionScale = 0.5;
		//public $animationWidth = 4;
		//public $animationWidth2 = 0.2;
        
        public $loadingtime = 2;
		public $normalload = 4;
		public $raking = 10;
		public $priority = 7; //heavy Raking	
		
        public $rangePenalty = 0.33; //-1 per 3 hexes
        public $fireControl = array(0, 2, 2);

		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){ //maxhealth and power reqirement are fixed; left option to override with hand-written values
			if ( $maxhealth == 0 ) $maxhealth = 7;
			if ( $powerReq == 0 ) $powerReq = 6;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
	    
		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn); 
			if (!isset($this->data["Special"])) {
				$this->data["Special"] = '';
			}else{
				$this->data["Special"] .= '<br>';
			}	    		  
			$this->data["Special"] .= "Can fire at accelerated RoF for less damage:";  
			$this->data["Special"] .= "<br> - 1 per 2 turns: 2d10+6"; 
			$this->data["Special"] .= "<br> - 1 per 3 turns: 3d10+10"; 
			$this->data["Special"] .= "<br> - 1 per 4 turns: 4d10+16"; 
		}
	
		public function getDamage($fireOrder){
        	switch($this->turnsloaded){
            	case 0:
            	case 1: 
            	case 2:
                	return Dice::d(10,2)+6;
					break;
            	case 3:
            	   	return Dice::d(10,3)+10;
					break;
			    default:
			    	return Dice::d(10,4)+16;
					break;			
        	}
		}

        public function setMinDamage(){
            switch($this->turnsloaded){
            	case 1:
            	case 2:
                    $this->minDamage = 8 ;
                    break;
                case 3:
                    $this->minDamage = 13 ;  
                    break;
                default:
                    $this->minDamage = 20 ;  
                    break;
            }
		}
             
        public function setMaxDamage(){
            switch($this->turnsloaded){
                case 1:
                case 2:
                    $this->maxDamage = 26 ;
                    break;
                case 3:
                    $this->maxDamage = 40 ;  
                    break;
                default:
                    $this->maxDamage = 56 ;  
                    break;
            }
		}

		public function stripForJson(){
			$strippedSystem = parent::stripForJson();
			$strippedSystem->data = $this->data;
			$strippedSystem->minDamage = $this->minDamage;
			$strippedSystem->minDamageArray = $this->minDamageArray;
			$strippedSystem->maxDamage = $this->maxDamage;
			$strippedSystem->maxDamageArray = $this->maxDamageArray;				
			return $strippedSystem;
		}

} // End of class Laser Accelerator



    class Maser extends Laser{
        public $trailColor = array(140, 210, 255);

        public $name = "Maser";
        public $displayName = "Maser";
		public $iconPath = "Maser.png";
		
        public $animation = "laser";
        //public $animationColor = array(240, 90, 90);
        public $animationColor = array(100, 30, 15);
        //public $animationExplosionScale = 0.2;
        //public $animationWidth = 3;
        //public $animationWidth2 = 0.3;
		
        public $priority = 4; //double accounting for armor puts this weapon in light Standard rather than medium Standard 

        public $loadingtime = 1;

        public $rangePenalty = 1;
        public $fireControl = array(2, 3, 3); // fighters, <mediums, <capitals

        public $damageType = "Standard"; 
        public $weaponClass = "Laser";
        public $firingModes = array( 1 => "Standard");

        public $noOverkill = true;		

        public function getSystemArmourBase($target, $system, $gamedata, $fireOrder, $pos=null){ //Maser treats armor as doubled
            $armour = parent::getSystemArmourBase($target, $system, $gamedata, $fireOrder, $pos);
            $armour = $armour * 2;
            $armour = max(0,$armour);
            return $armour;
        }

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 6;
            }
            if ( $powerReq == 0 ){
                $powerReq = 3;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
            //$this->data["Special"] = '<br>Armor counts as double.';
            $this->data["Special"] = "<br>Armor is doubled, and damage from turn of firing doubled for criticals/dropout rolls.";
			$this->data["Special"] .= "<br>Forces critical on any system or fighter hit, even if Maser does not penetrate armor.";
			$this->data["Special"] .= "<br>No overkill damage.";
            parent::setSystemDataWindow($turn);
        }

		protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ //really no matter what exactly was hit!
			parent::onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder);
			if (WeaponEM::isTargetEMResistant($ship,$system)) return; //no effect on Advanced Armor
			$system->critRollMod+=max(0, ($damage-$armour)); //+twice damage to all critical/dropout rolls on system hit this turn
			$system->forceCriticalRoll = true;
		} //endof function onDamagedSystem

        public function getDamage($fireOrder){ return Dice::d(10, 2)+2;   }
        public function setMinDamage(){     $this->minDamage = 4 ;      }
        public function setMaxDamage(){     $this->maxDamage = 22 ;      }  
		
    }  //endof Maser


    class SpinalLaser extends Laser{
        public $name = "SpinalLaser";
        public $displayName = "Spinal Laser";
        public $animation = "laser";
        public $animationColor = array(255, 79, 15);
        //public $animationWidth = 5;
        //public $animationWidth2 = 0.3;
        //public $animationExplosionScale = 0.75;

        public $loadingtime = 5;
        public $overloadable = true;
        public $extraoverloadshots = 2;

        public $raking = 10;
        public $priority = 9;
		public $repairPriority = 6;//Make slightly higher than normal weapons :)        
        
        public $rangePenalty = 0.2;
        public $fireControl = array(null, 2, 4); // fighters, <mediums, <capitals 

        private $sustainedTarget = array(); //To track for next turn which ship was fired at in Sustained Mode and whether it was hit.
        private $sustainedSystemsHit = array(); //For tracking systems that were hit and how much armour they should be reduced by following turn if hit again.          
    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 12;
            }
            if ( $powerReq == 0 ){
                $powerReq = 12;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
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

        public function getDamage($fireOrder){        return Dice::d(10, 6)+40;   }
        public function setMinDamage(){     $this->minDamage = 46 ;      }
        public function setMaxDamage(){     $this->maxDamage = 100 ;      }
        
        
    } // endof Spinal Laser


	//Fighter-sized blast laser
    class LtBlastLaser extends Weapon{  
        public $name = "LtBlastLaser";
        public $displayName = "Light Blast Laser";
        public $iconPath = "improvedBlastLaser.png";

        public $animationColor = array(255, 30, 30);
        public $animation = "bolt"; //a bolt, not beam
        public $uninterceptable = true;
		
		public $noPrimaryHits = true;//cannot penetrate to PRIMARY on outer hits
//	    public $rof = 1;
        
        public $loadingtime = 1;
		public $priority = 6;//VERY large fighter weapon
        
        public $rangePenalty = 2;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals 
        public $damageType = "Standard";
        public $weaponClass = "Laser"; 

		public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
			$this->data["Special"] = 'Uninterceptable.'; 
			$this->data["Special"] .= '<br>Cannot penetrate to PRIMARY when hitting outer section.';
		}

        function __construct($startArc, $endArc){
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return Dice::d(6,2)+5;   }
        public function setMinDamage(){     $this->minDamage = 7 /*- $this->dp*/;      }
        public function setMaxDamage(){     $this->maxDamage = 17 /*- $this->dp*/;      }

    }

   //Very deceptive name, this is Torvalus Fighter weapon so actually pretty heavy laser ;)
    class UltralightLaser extends Laser{  
        public $name = "UltralightLaser";
        public $displayName = "Ultralight Laser";
        public $iconPath = "PowerLaser.png";

        public $animationColor = array(255, 255, 0);
        public $animation = "laser";
        public $animationExplosionScale = 0.2;        
        public $uninterceptable = true;
		
		public $noPrimaryHits = true;//cannot penetrate to PRIMARY on outer hits
        public $loadingtime = 2;
		public $priority = 6;//VERY large fighter weapon
        
        public $rangePenalty = 1;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals 


		public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
			$this->data["Special"] = 'Uninterceptable.'; 
		}

        function __construct($startArc, $endArc){
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return Dice::d(6,2)+5;   }
        public function setMinDamage(){     $this->minDamage = 10 /*- $this->dp*/;      }
        public function setMaxDamage(){     $this->maxDamage = 28 /*- $this->dp*/;      }

    }


class UnreliableBattleLaser extends BattleLaser{

    public $name = "UnreliableBattleLaser";
    public $displayName = "Unreliable Battle Laser";
    public $iconPath = "battleLaser.png";

	protected $misfire2;

    public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		$this->data["Special"] .= "<br>This Battle Laser is prone to misfires."; 
		$this->data["Special"] .= "<br>10% chance of misfire and doing no damage."; 
	}
	
	public function getDamage($fireOrder){
		$misfire2 = Dice::d(10,1);
		if ($misfire2 == 1) {
			$fireOrder->pubnotes .= "<br> Weapon misfire! No damage.";
			return (Dice::d(10, 4)+12)*0;
		}else{
			return Dice::d(10, 4)+12;
		}
	}
	public function setMinDamage(){		$this->minDamage = 16;	}
	public function setMaxDamage(){		$this->maxDamage = 52;	}
	
} //endof class UnreliableBattleLaser


?>
