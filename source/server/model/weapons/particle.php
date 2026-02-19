<?php
    class Particle extends Weapon{
        public $damageType = "Standard"; 
        public $weaponClass = "Particle"; 
	    
        public $animation = "bolt";
        public $animationColor = array(255, 102, 0); 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
        }

        public $priority = 6;

    }



    class StdParticleBeam extends Particle{ 
        public $name = "stdParticleBeam";
        public $displayName = "Standard Particle Beam";
        public $animation = "bolt";
	    /*
        public $animationColor = array(255, 102, 0);
        public $trailColor = array(255, 102, 0);
        public $animationExplosionScale = 0.15;
        public $projectilespeed = 12;
        public $animationWidth = 3;
        public $trailLength = 10;
*/
        public $intercept = 2;
        public $loadingtime = 1;

        public $rangePenalty = 1;
        public $fireControl = array(4, 4, 4); // fighters, <mediums, <capitals
        public $priority = 5; //it's medium weapon already

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
	    if ( $maxhealth == 0 ) $maxhealth = 4;
            if ( $powerReq == 0 ) $powerReq = 1;
            
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10)+6;   }
        public function setMinDamage(){     $this->minDamage = 7 ;      }
        public function setMaxDamage(){     $this->maxDamage = 16 ;      }
    }


    class QuadParticleBeam extends StdParticleBeam {
        public $name = "quadParticleBeam";
        public $displayName = "Quad Particle Beam";
	   public  $iconPath = "quadParticleBeam.png";
        public $guns = 4;

        public $firingModes = array( 1 => "Normal", 2=> "Split");
        public $canSplitShots = false; //Allows Firing Mode 2 to split shots.
        public $canSplitShotsArray = array(1=>false, 2=>true );         
        
        public function setSystemDataWindow($turn){			
            parent::setSystemDataWindow($turn);   
            $this->data["Special"] = "Can use 'Split' Firing Mode to target different enemy units.";
        }

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
	    if ( $maxhealth == 0 ) $maxhealth = 8;
            if ( $powerReq == 0 ) $powerReq = 4;            
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
    }


    class ParticleBlaster extends Particle{

        public $name = "particleBlaster";
        public $displayName = "Particle Blaster";
        public $animation = "bolt";
	    /*
        public $animationColor = array(255, 102, 0);
        public $trailColor = array(255, 102, 0);
        public $animationExplosionScale = 0.25;
        public $projectilespeed = 15;
        public $animationWidth = 5;
        public $trailLength = 10;
	*/
        public $priority = 6;

        public $loadingtime = 2;


        public $rangePenalty = 0.5;
        public $fireControl = array(0, 4, 4); // fighters, <mediums, <capitals


        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10)+12;   }
        public function setMinDamage(){     $this->minDamage = 13 ;      }
        public function setMaxDamage(){     $this->maxDamage = 22 ;      }

    }
    

/*fighter-mounted variant*/
    class ParticleBlasterFtr extends Particle{

        public $name = "particleBlasterFtr";
        public $displayName = "Particle Blaster";
        public $iconPath = "particleBlaster.png";
        public $animation = "bolt";
	    /*
        public $animationColor = array(255, 102, 0);
        public $trailColor = array(255, 102, 0);
        public $animationExplosionScale = 0.25;
        public $projectilespeed = 12;
        public $animationWidth = 5;
        public $trailLength = 10;
	*/
        public $priority = 6;

        public $loadingtime = 3;

        public $rangePenalty = 1;
        public $fireControl = array(-4, 0, 0); // fighters, <mediums, <capitals

	function __construct($startArc, $endArc, $nrOfShots = 1){
            $this->defaultShots = $nrOfShots;
            $this->shots = $nrOfShots;
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10)+12;   }
        public function setMinDamage(){     $this->minDamage = 13 ;      }
        public function setMaxDamage(){     $this->maxDamage = 22 ;      }

    }


    class AdvParticleBeam extends Particle{
        public $name = "advParticleBeam";
        public $displayName = "Advanced Particle Beam";
        public $animation = "beam";
        public $iconPath = "advParticleBeam.png";        
	    /*
        public $animationColor = array(255, 102, 0);
        public $trailColor = array(255, 102, 0);
        public $animationExplosionScale = 0.20;
        public $projectilespeed = 14;
        public $animationWidth = 5;
        public $trailLength = 10;
        public $iconPath = "stdParticleBeam.png";
	*/
        public $priority = 5;

        public $intercept = 2;
        public $loadingtime = 1;
        public $rangePenalty = 0.66;
        public $fireControl = array(5, 5, 5); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10)+8;   }
        public function setMinDamage(){     $this->minDamage = 9 ;      }
        public function setMaxDamage(){     $this->maxDamage = 18 ;      }
    }



    class TwinArray extends Particle{
        public $name = "twinArray";
        public $displayName = "Twin Array";
        public $animation = "bolt";
	    /*
        public $animationColor = array(255, 163, 26);
        public $trailColor = array(255, 163, 26);
        public $animationExplosionScale = 0.15;
        public $projectilespeed = 12;
        public $animationWidth = 3;
        public $trailLength = 10;
*/
        public $intercept = 2;

        public $loadingtime = 1;
        public $guns = 2;
        public $priority = 4;

        public $rangePenalty = 2;
        public $fireControl = array(6, 5, 4); // fighters, <mediums, <capitals

        public $firingModes = array( 1 => "Normal", 2=> "Split");
        public $canSplitShots = false; //Allows Firing Mode 2 to split shots.
        public $canSplitShotsArray = array(1=>false, 2=>true );          
        public $startArcArray = array(); 
        public $endArcArray = array();        
        protected $splitArcs = false; //Used to tell Front End that weapon has 2 or more separate arcs, passed manually via stripForJson()

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $startArc2 = null, $endArc2 = null){
            if ( $maxhealth == 0 ) $maxhealth = 6;
            if ( $powerReq == 0 ) $powerReq = 4;

            if($startArc2 !== null || $endArc2 !== null){      
                $this->startArcArray[0] = $startArc; //Set rear arcs manually
                $this->endArcArray[0] = $endArc; 
                $this->startArcArray[1] = $startArc2;
                $this->endArcArray[1] = $endArc2; 
                $this->splitArcs = true;                          
            }                                   
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){			
            parent::setSystemDataWindow($turn);   
            $this->data["Special"] = "Can use 'Split' Firing Mode to target different enemy units.";
        }

        public function getDamage($fireOrder){        return Dice::d(10)+4;   }
        public function setMinDamage(){     $this->minDamage = 5 ;      }
        public function setMaxDamage(){     $this->maxDamage = 14 ;      }

    public function stripForJson() {
        $strippedSystem = parent::stripForJson();    
        $strippedSystem->splitArcs = $this->splitArcs;        						                                        
        return $strippedSystem;
	}	            

    }

    class HeavyArray extends Particle{
        public $name = "heavyArray";
        public $displayName = "Heavy Array";
	   
        public $animation = "bolt";
	    /*
        public $animationColor = array(255, 163, 26);
        public $trailColor = array(255, 163, 26);
        public $animationExplosionScale = 0.25;
        public $projectilespeed = 20;
        public $animationWidth = 4;
        public $trailLength = 15;
*/
        public $intercept = 2;

        public $loadingtime = 1;
        public $guns = 2;
        public $priority = 6;

        public $rangePenalty = 1;
        public $fireControl = array(2, 3, 4); // fighters, <mediums, <capitals

        public $firingModes = array( 1 => "Normal", 2=> "Split");
        public $canSplitShots = false; //Allows Firing Mode 2 to split shots.
        public $canSplitShotsArray = array(1=>false, 2=>true );           


        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		if ( $maxhealth == 0 ) $maxhealth = 8;
		if ( $powerReq == 0 ) $powerReq = 4;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){			
            parent::setSystemDataWindow($turn);   
            $this->data["Special"] = "Can use 'Split' Firing Mode to target different enemy units.";
        }

        public function getDamage($fireOrder){        return Dice::d(10, 2)+6;   }
        public function setMinDamage(){     $this->minDamage = 8 ;      }
        public function setMaxDamage(){     $this->maxDamage = 26 ;      }

    }



//half a Heavy Array ;)
    class HeavyParticleBeam extends Particle{
        public $name = "HeavyParticleBeam";
        public $displayName = "Heavy Particle Beam";
        public $animation = "bolt";
        public $iconPath = "HeavyParticleBeam.png";        

        public $guns = 1; //main difference - single mount

        public $intercept = 2;

        public $loadingtime = 1;
        public $priority = 6;

        public $rangePenalty = 1;
        public $fireControl = array(2, 3, 4); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
			if ( $maxhealth == 0 ) $maxhealth = 4;
			if ( $powerReq == 0 ) $powerReq = 2;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10, 2)+6;   }
        public function setMinDamage(){     $this->minDamage = 8 ;      }
        public function setMaxDamage(){     $this->maxDamage = 26 ;      }

    }



    class ParticleCannon extends Raking{
        public $name = "particleCannon";
        public $displayName = "Particle Cannon";
	    
	public $animation = "laser";
        public $animationColor = array(255, 163, 26);
	    /*
        public $trailColor = array(255, 163, 26);
        public $animationExplosionScale = 0.25;
        public $animationWidth = 4;
        public $animationWidth2 = 0.3;
*/
        public $intercept = 1;
        public $loadingtime = 2;
        public $priority = 8;

        public $rangePenalty = 0.5;
        public $fireControl = array(2, 4, 5); // fighters, <mediums, <capitals

        public $damageType = "Raking"; 
        public $weaponClass = "Particle";
        public $firingModes = array( 1 => "Raking");
        
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
        }

        public function getDamage($fireOrder){ return Dice::d(10, 2)+15;   }
        public function setMinDamage(){     $this->minDamage = 17 ;      }
        public function setMaxDamage(){     $this->maxDamage = 35 ;      }
    }



    class LightParticleCannon extends Raking{

        public $name = "lightParticleCannon";
        public $displayName = "Light Particle Cannon";
	public $animation = "laser";
        public $animationColor = array(255, 163, 26);
	    /*
        public $trailColor = array(255, 163, 26);
        public $animationExplosionScale = 0.2;
        public $animationWidth = 3;
        public $animationWidth2 = 0.3;
*/
        public $intercept = 2;
        public $loadingtime = 2;
        public $priority = 8;

        public $rangePenalty = 1;
        public $fireControl = array(0, 2, 4); // fighters, <mediums, <capitals

        public $damageType = "Raking"; 
        public $weaponClass = "Particle";
        public $firingModes = array( 1 => "Raking");
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
        }

        public function getDamage($fireOrder){ return Dice::d(10, 2)+8;   }
        public function setMinDamage(){     $this->minDamage = 10 ;      }
        public function setMaxDamage(){     $this->maxDamage = 28 ;      }
    }
    


    class HvyParticleCannon extends Raking{

        public $name = "hvyParticleCannon";
        public $displayName = "Heavy Particle Cannon";
        public $animation = "laser";
        public $animationColor = array(255, 163, 26);
	    /*
        public $animationColor2 = array(255, 163, 26);
        public $trailColor = array(255, 163, 26);
        public $animationExplosionScale = 0.45;
        public $animationWidth = 7;
	*/
        public $priority = 7;

        public $loadingtime = 6;

        public $rangePenalty = 0.33;
        public $fireControl = array(0, 4, 6); // fighters, <mediums, <capitals

        public $damageType = "Raking"; 
        public $weaponClass = "Particle";
        public $firingModes = array( 1 => "Raking");
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
        }

        public function getDamage($fireOrder){ return Dice::d(10, 6)+60;   }
        public function setMinDamage(){     $this->minDamage = 66 ;      }
        public function setMaxDamage(){     $this->maxDamage = 120 ;      }
    }



    class ParticleCutter extends Raking{
        public $name = "particleCutter";
        public $displayName = "Particle Cutter";
	    
	public $animation = "laser";
        public $animationColor = array(255, 153, 102);
	    /*
        public $trailColor = array(255, 153, 102);
        public $animationExplosionScale = 0.45;
        public $animationWidth = 3;
        public $animationWidth2 = 0.3;
	    */
        public $firingModes = array( 1 => "Sustained");
        
        public $damageType = "Raking"; 
        public $weaponClass = "Particle";
        
        // Set to make the weapon start already overloaded.
        public $alwaysoverloading = true;
        public $overloadturns = 2;
        public $extraoverloadshots = 2;
        public $overloadshots = 2;
        public $loadingtime = 2;
        public $priority = 8;

        public $rangePenalty = 0.5;
        public $fireControl = array(2, 3, 4); // fighters, <mediums, <capitals

        private $sustainedTarget = array(); //To track for next turn which ship was fired at in Sustained Mode and whether it was hit.
        private $sustainedSystemsHit = array(); //For tracking systems that were hit and how much armour they should be reduced by following turn if hit again. 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){			
            parent::setSystemDataWindow($turn);        
			if (!isset($this->data["Special"])) {
				$this->data["Special"] = '';
			}else{
				$this->data["Special"] .= '<br>';
			}
            $this->data["Special"] .= 'This weapon is always in sustained mode.';
            $this->data["Special"] .= '<br>As a Sustained weapon, if the first shot hits then the next turns shot will hit automatically.';
            $this->data["Special"] .= '<br>Subsequent Sustained shots ignore any armour/shields that have applied to previous shots.';                                                               
		}                            


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

        public function isOverloadingOnTurn($turn = null){
            return true;
        }  

        public function getDamage($fireOrder){ return Dice::d(10, 2)+12;   }
        public function setMinDamage(){     $this->minDamage = 14 ;      }
        public function setMaxDamage(){     $this->maxDamage = 32 ;      }

    }//endof ParticleCutter



    class ParticleRepeater extends Particle{
        public $name = "particleRepeater";
        public $displayName = "Particle Repeater";
	    
        public $animation = "bolt";
        public $animationColor = array(255, 163, 26);

        public $loadingtime = 1;
        public $boostable = true;
        public $boostEfficiency = 1;
        public $priority = 5;
	    public $intercept = 1;
        public $rangePenalty = 1;
        public $fireControl = array(4, 2, 2); // fighters, <mediums, <capitals
        public $canSplitShots = false; //Defaults false without Gunsights
        public $specialHitChanceCalculation = false;        
        private $hitChanceMod = 0;
        private $shotsFiredSoFar = 0;
        //private $previousHit = true;       
       
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            $this->data["Special"] = "Standard power: 1 shot, intercept -5.";
            $this->data["Special"] .= "<br>Each additional +1 Power adds -5 intercept or 1 shot in offensive mode providing you have a OEW lock.";
            $this->data["Special"] .= "<br>Each pair of shots above 2 forces a turn of cooldown (rounded up).";
            $this->data["Special"] .= "<br>Shots can target different units with a cumulative to hit penalty. However, once a shot misses, all further ones miss automatically.";
            $this->data["Special"] .= "<br>If equipped with gunsights, this weapon may split it's shots to different tagets in a 1 hex radius.";
            parent::setSystemDataWindow($turn);
        } 
                   
	protected function applyCooldown($gamedata){
		$currBoostlevel = $this->getBoostLevel($gamedata->turn);
		//if boosted, cooldown (1 per 2 extra shots above first 2)
		$turnToAdd = 0;
		 $cooldownLength = ceil(($currBoostlevel-1)/2);//actual numbers of turns of cooldown
		 while($cooldownLength > 0){ 		     
			$crit = new ForcedOfflineOneTurn(-1, $this->unit->id, $this->id, "ForcedOfflineOneTurn", $gamedata->turn+$turnToAdd);
			$crit->updated = true;
			$crit->newCrit = true; //force save even if crit is not for current turn
			$this->criticals[] =  $crit;
			$turnToAdd++;    
			$cooldownLength--;
		 }
	}
        
        public function beforeFiringOrderResolution($gamedata){
            if(!$this->canSplitShots) return; //Only relevant for split shots.
        
            //Gather all valid fire orders for this turn
            $validOrders = array();
            foreach($this->fireOrders as $fireOrder){
                if($fireOrder->turn != $gamedata->turn) continue;
                if($fireOrder->type == 'intercept' || $fireOrder->type == 'selfIntercept') continue;
                $validOrders[] = $fireOrder;
            }
            
            $count = count($validOrders);
            if($count == 0) return;
            
            $maxShots = $this->getMaxShots($gamedata->turn);
            
            //Distribute shots: 1 per valid order, remainder to last order.
            //We reset shots here, so fire() doesn't need to guess.
            $shotsUsed = 0;
            for($i=0; $i<$count; $i++){
                $order = $validOrders[$i];
                $order->shots = 1; //Base 1 shot per target
                $shotsUsed++;
                
                if($i == ($count-1)){ //Last order gets the rest
                    $remaining = $maxShots - $shotsUsed;
                    if($remaining > 0){
                        $order->shots += $remaining;
                    }
                }
            }
        }

        public function fire($gamedata, $fireOrder){ 
            // If this order was already handled (e.g. by a previous call in the same turn resolving the whole volley), skip it.
            if ($fireOrder->rolled > 0) return;

            $currBoostlevel = $this->getBoostLevel($gamedata->turn);


            // If we can't split shots, then this is a fresh volley every time we enter fire (standard behavior).
            if (!$this->canSplitShots) {
                $this->shotsFiredSoFar = 0;
                $this->hitChanceMod = 0;
				$fireOrder->shots = 1 + $currBoostlevel; 
                
                $shooter = $this->getUnit();
                $target = $gamedata->getShipById($fireOrder->targetid);
                $oew = $shooter->getOEW($target, $gamedata->turn);                                 
                if ($oew < 1) $fireOrder->shots = 1; //Only 1 shot if no OEW lock.
                
                parent::fire($gamedata, $fireOrder);
                
                $this->shotsFiredSoFar += $fireOrder->shots;
                $this->applyCooldown($gamedata);
            } else {
                // Split shots / Gunsight logic
                // We resolve ALL shots for this weapon/turn NOW, to ensure correct sequence.

                // 1. Gather all valid fire orders for this weapon, this turn.
                $allOrders = array();
                foreach($this->fireOrders as $fo){
                    if($fo->turn == $gamedata->turn && $fo->type != 'intercept' && $fo->type != 'selfIntercept'){
                         $allOrders[] = $fo;
                    }
                }
                
                // 2. Sort by ID to ensure creation order (user click order).
                usort($allOrders, function($a, $b){
                    return $a->id - $b->id;
                });

                // 3. Reset state
                $this->shotsFiredSoFar = 0;
                $this->hitChanceMod = 0;

                // 4. Iterate and fire
                foreach($allOrders as $currOrder){
                     // Skip if somehow already rolled
                     if($currOrder->rolled > 0) continue;

                     // Calculate mod logic same as before but inside loop
                     // If splitting shots, calculate modifier for this specific shot and apply it to fire order
				     $mod = $this->getPrevShotHitChanceMod($this->shotsFiredSoFar);
				     $currOrder->needed -= $mod;

                     // Check broken chain
                     if($this->hitChanceMod >= 10000){
                         $currOrder->needed = 0;
                         $currOrder->totalIntercept = 0; //Prevent weird log entries
                         $currOrder->numInterceptors = 0;
                         $currOrder->pubnotes .= "<br>Automatic miss due to previous miss in chain.";
                     }

                     // Execute
                     parent::fire($gamedata, $currOrder);

                     // Check if miss occurred to break chain
                     if ($currOrder->shotshit < $currOrder->shots){
                        $this->hitChanceMod = 10000;
                     }

                     // Update accumulator
                     $this->shotsFiredSoFar += $currOrder->shots;
                }
                
                // 5. Apply cooldown once for the volley
                $this->applyCooldown($gamedata);
            }
        }
	    
    //applying cooldown when firing defensively, too
    public function fireDefensively($gamedata, $interceptedWeapon)
    {
		if ($this->firedDefensivelyAlready==0){ //in case of multiple interceptions during one turn - suffer backlash only once
			$this->applyCooldown($gamedata);	
		}
		parent::fireDefensively($gamedata, $interceptedWeapon);
    }
        
        //if previous shot missed, next one misses automatically
        /*so if current mod is not equal to one of previous shot, then it's clearly a miss - return suitably high mod*/
        public function getShotHitChanceMod($shotInSequence){ 
            $effectiveShotIndex = $this->shotsFiredSoFar + $shotInSequence;
            $prevExpectedChance = $this->getPrevShotHitChanceMod($effectiveShotIndex-1);
            
            if($prevExpectedChance != $this->hitChanceMod){ //something missed in between
                $this->hitChanceMod = 10000; //clear miss!!!
            }else{
                $this->hitChanceMod = $this->getPrevShotHitChanceMod($effectiveShotIndex);
            }
            
            //Subtract the modifier that was already applied to the FireOrder in fire()
            //This avoids double-counting the penalty for the first shot(s) in the sequence
            $baseMod = 0;
            if($this->canSplitShots){
                 $baseMod = $this->getPrevShotHitChanceMod($this->shotsFiredSoFar);
            }
            
            return $this->hitChanceMod - $baseMod;
        }
        
        public function getPrevShotHitChanceMod($shotInSequence){ //just finds hit chance for a given shot - what it should be
            if($shotInSequence <=0) return 0;
            // Formula: No mod (0) on 1st shot (index 0). -1 (5) on 2nd (index 1). -2 (10) more on each subsequent.
            // Index 0: 0
            // Index 1: 5
            // Index 2: 5 + 10 = 15
            // Index 3: 5 + 20 = 25
            
            $mod = 5 + 10 * ($shotInSequence - 1);
            return $mod;
        }                
        
        protected function getWeaponHitChanceMod($turn){
            return $this->hitChanceMod;
        }
        
        protected function doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata, $damageWasDealt, $location = null){ 
            //if target is fighter flight, ensure that the same fighter is hit every time if no Gunsights!
            if($target instanceof FighterFlight && !$this->canSplitShots){
                $fireOrder->linkedHit = $system;
            }            
            parent::doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata, $damageWasDealt, $location);
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
        protected function getMaxShots($turn){
            return 1 + $this->getBoostLevel($turn);
        }
        public function getInterceptRating($turn){
            return 1 + $this->getBoostLevel($turn);            
        }
        public function getDamage($fireOrder){ return Dice::d(10, 2);   }
        public function setMinDamage(){     $this->minDamage = 2 ;      }
        public function setMaxDamage(){     $this->maxDamage = 20 ;      }
    } //endof class ParticleRepeater
    
	
    class RepeaterGun extends Particle{
        public $name = "repeaterGun";
        public $displayName = "Repeater Gun";
	    
        public $animation = "bolt";
        public $animationColor = array(255, 163, 26);
	    
        public $loadingtime = 1;
        public $boostable = true;
        public $boostEfficiency = 2;
        public $priority = 4;
	public $intercept = 1;
        public $rangePenalty = 0.5; //-1/2 hexes
        public $fireControl = array(2, 2, 2); // fighters, <mediums, <capitals
        
        private $hitChanceMod = 0;
        private $previousHit = true;
        
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
	    $this->data["Special"] = "Standard power: 1 shot, intercept -5.";
	    $this->data["Special"] .= "<br>Each additional +2 Power adds -5 intercept or 1 shot in offensive mode.";
	    $this->data["Special"] .= "<br>Each additional shot forces a turn of cooldown.";
	    $this->data["Special"] .= "<br>All shots hit the same target. If a shot misses, further ones miss automatically. Otherwise they have cumulative to hit penalty.";
            //$this->defaultShots = 1+$this->getBoostLevel(TacGamedata::$currentTurn); //default shots is 1, so interception is correct!
            
            parent::setSystemDataWindow($turn);
        } 
        
	protected function applyCooldown($gamedata){
		$currBoostlevel = $this->getBoostLevel($gamedata->turn);
		//if boosted, cooldown (1 per extra shot)
		$turnToAdd = 0;
		 $cooldownLength = $currBoostlevel;//actual numbers of turns of cooldown
		 while($cooldownLength > 0){ 		     
			$crit = new ForcedOfflineOneTurn(-1, $this->unit->id, $this->id, "ForcedOfflineOneTurn", $gamedata->turn+$turnToAdd);
			$crit->updated = true;
			$crit->newCrit = true; //force save even if crit is not for current turn
			$this->criticals[] =  $crit;
			$turnToAdd++;    
			$cooldownLength--;
		 }
	}
	    
        public function fire($gamedata, $fireOrder){ 
	    $currBoostlevel = $this->getBoostLevel($gamedata->turn);
            $this->hitChanceMod = 0;
            $fireOrder->shots = 1 + $currBoostlevel;
            parent::fire($gamedata, $fireOrder);
		
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
	    
        /*if previous shot missed, next one misses automatically*/
        /*so if current mod is not equal to one of previous shot, then it's clearly a miss - return suitably high mod*/
        public function getShotHitChanceMod($shotInSequence){ 
            $prevExpectedChance = $this->getPrevShotHitChanceMod($shotInSequence-1);
            if($prevExpectedChance != $this->hitChanceMod){ //something missed in between
                $this->hitChanceMod = 10000; //clear miss!!!
            }else{
                $this->hitChanceMod = $this->getPrevShotHitChanceMod($shotInSequence);
            }
            return $this->hitChanceMod;
        }
        
        public function getPrevShotHitChanceMod($shotInSequence){ //just finds hit chance for a given shot - what it should be
            if($shotInSequence <=0) return 0;
            if($shotInSequence ==1) return 5;
            $mod= 5+10*($shotInSequence-2);
            return $mod;
        }                
        
        protected function getWeaponHitChanceMod($turn){
            return $this->hitChanceMod;
        }
        
        protected function doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata, $damageWasDealt, $location = null){ 
            //if target is fighter flight, ensure that the same fighter is hit every time!
            if($target instanceof FighterFlight){
                $fireOrder->linkedHit = $system;
            }            
            parent::doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata, $damageWasDealt, $location);
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
        protected function getMaxShots($turn){
            return 1 + $this->getBoostLevel($turn);
        }
        public function getInterceptRating($turn){
            return 1 + $this->getBoostLevel($turn);            
        }
        public function getDamage($fireOrder){ return Dice::d(10, 1)+3;   }
        public function setMinDamage(){     $this->minDamage = 4 ;      }
        public function setMaxDamage(){     $this->maxDamage = 13 ;      }
    } //endof class RepeaterGun


    class PairedParticleGun extends LinkedWeapon{
        public $name = "pairedParticleGun";
        public $displayName = "Particle Gun"; //it's not 'paired' in any way, except being usually mounted twin linked - like most fighter weapons...
        public $animation = "bolt";
        public $animationColor = array(255, 163, 26);
	    /*
        public $trailColor = array(255, 163, 26);
        public $animationExplosionScale = 0.10;
        public $projectilespeed = 12;
        public $animationWidth = 2;
        public $trailLength = 10;
	*/
        public $intercept = 2;

        public $loadingtime = 1;
        public $shots = 2;
        public $defaultShots = 2;
	public $priority = 3; //correct for d6+2 and lighter

        public $rangePenalty = 2;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals
        private $damagebonus = 0;
        
        public $damageType = "Standard"; 
        public $weaponClass = "Particle";         

        function __construct($startArc, $endArc, $damagebonus, $nrOfShots = 2){
            $this->damagebonus = $damagebonus;
            $this->defaultShots = $nrOfShots;
            $this->shots = $nrOfShots;
            $this->intercept = $nrOfShots;		

            if ($damagebonus >= 3) $this->priority++; //heavier varieties fire later in the queue
            if ($damagebonus >= 5) $this->priority++;
            if ($damagebonus >= 7) $this->priority++;

            if($nrOfShots === 1){
                $this->iconPath = "particleGun.png";
            }
            if($nrOfShots >2){//no special icon for more than 3 linked weapons
                $this->iconPath = "pairedParticleGun3.png";
            }

            parent::__construct(0, 1, 0, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
        }

        public function getDamage($fireOrder){        return Dice::d(6)+$this->damagebonus;   }
        public function setMinDamage(){     $this->minDamage = 1+$this->damagebonus ;      }
        public function setMaxDamage(){     $this->maxDamage = 6+$this->damagebonus ;      }
    } //endof PairedParticleGun



    class SolarCannon extends Particle{
        public $name = "solarCannon";
        public $displayName = "Solar Cannon";
	    
        public $animation = "bolt";
        public $animationColor = array(204, 204, 0);
	    public $animationExplosionScale = 0.5;//re-scaled automatically in constructor!
	    /*
        public $animationExplosionScale = 0.45;
        public $projectilespeed = 15;
        public $animationWidth = 8;
        public $trailLength = 14;
	*/
        public $priority = 6;

        public $loadingtime = 3;

        public $rangePenalty = 0.5;
        public $fireControl = array(0, 3, 5); // fighters, <mediums, <capitals

        public $damageType = "Standard"; 
        public $weaponClass = "Particle"; 
        public $noOverkill = true; // The damage of a solar cannon does not overkill.
		public $firingModes = array( 1 => "Melt"); 
        private $damageToRepeat = 0;
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);   	    
			if (!isset($this->data["Special"])) {
				$this->data["Special"] = '';
			}else{
				$this->data["Special"] .= '<br>';
			}
			$this->data["Special"] .= "No overkill.<br>Reduce armor by 2 (on ships only)."; //tabletop: facing armor on a fighter as well!
			$this->data["Special"] .= "<br>Damage scored is repeated on appropriate Structure.";
		
		$this->animationExplosionScale = $this->dynamicScale(0,2);//scale weapon using double damage output - as base damage output is low-ish, but it's repeated on Structure for overall impressive total
        }
		
		/*actually repeating damage scored on appropriate Structure*/
		private function doRepeatDamageOnStructure($fireOrder,$target,$systemHit, $damage, $gamedata){
            //Debug::log(json_encode($damageToRepeat));
            $damageToRepeat = $this->damageToRepeat;

			if(!$target instanceof FighterFlight){
				$struct = null;
				if($systemHit instanceof Structure){
					$struct = $systemHit;
				}else{
					$struct = $target->getStructureSystem($systemHit->location);
				}
                if($struct && (!$struct->isDestroyed())){
                    $shooter = $this->getUnit();
                    
                    //Determine if the shot successfully penetrated (dealt damage) to trigger internal effects
                    $isUnderShield = ($damage > 0);

                    //CALL SYSTEMS PROTECTING FROM DAMAGE HERE! 
                    //Pass true for isUnderShield to indicate this is internal/under-shield damage
                    $systemProtectingDmg = $target->getSystemProtectingFromDamage($shooter, null, $gamedata->turn, $this, $struct, $damageToRepeat, false, $isUnderShield);
                    if($systemProtectingDmg){
                        $effectOfProtection = $systemProtectingDmg->doProtect($gamedata, $fireOrder, $target, $shooter,$this,$struct,$damageToRepeat, 0);
                        $damageToRepeat = $effectOfProtection['dmg'];
                        //$effectiveArmor = $effectOfProtection['armor'];
                    }
                    //Debug::log(json_encode($damageToRepeat));

					$destroyed = false;
					$remHealth = $struct->getRemainingHealth();	
					if($damageToRepeat >= $remHealth) $destroyed = true;
					$damageToMark = min($damageToRepeat, $remHealth);
					$damageEntry = new DamageEntry(-1, $target->id, -1, $fireOrder->turn, $struct->id, $damageToMark, 0, 0, $fireOrder->id, $destroyed, false, "", $this->weaponClass, $fireOrder->shooterid, $this->id);
					$damageEntry->updated = true;
					$struct->damage[] = $damageEntry;
				}
			}
		}//endof function doRepeatDamageOnStructure
		
		/*repeat damage on structure (ignoring armor); 
              system hit will have its armor reduced by 2
              for non-fighter targets
        */

        protected function beforeDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){
            //Debug::log(json_encode($damage));
            $this->damageToRepeat = $damage-$armour; //Saved damage amount before any protection from Diffusers/shields etc.
            return $damage;
        }

		protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder)
		{
			$target = $ship;
			if(!$target instanceof FighterFlight){
				//reduce armor of system hit
				if (!$system->advancedArmor) { //Advanced Armor prevents armor reduction
					$crit = new ArmorReduced(-1, $target->id, $system->id, "ArmorReduced", $gamedata->turn);
					$crit->updated = true;
					$crit->inEffect = false; //in effect only on next turn
					$system->criticals[] = $crit;
					$crit = new ArmorReduced(-1, $target->id, $system->id, "ArmorReduced", $gamedata->turn);
					$crit->updated = true;
					$crit->inEffect = false; //in effect only on next turn
					$system->criticals[] = $crit;
				}               
				$this->doRepeatDamageOnStructure($fireOrder,$target,$system,$damage, $gamedata);
			}
		}//endof onDamagedSystem
		
		//overkill should return damaged system itself, even if it is destroyed! - necessary for redefined doDamage to work properly
		protected function getOverkillSystem($target, $shooter, $system, $fireOrder, $gamedata, $damageWasDealt, $location = null)
		{
			if($damageWasDealt){
				if(!$target instanceof FighterFlight){
					return $system;
				}else{
					return null;
				}
			}else{ //if damage was NOT dealt yet - regular looking for system hit should happen
				return parent::getOverkillSystem($target, $shooter, $system, $fireOrder, $gamedata, $damageWasDealt, $location );
			}
		}//endof function getOverkillSystem
		
		//if damage was already dealt - proceed immediately to melting effect (of overkill) - else regular behavior
		protected function doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata, $damageWasDealt, $location = null)
		{
			if($damageWasDealt){
				$this->doRepeatDamageOnStructure($fireOrder,$target,$system,$damage, $gamedata);
			}else{
				parent::doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata, $damageWasDealt, $location);
			}			
		}//endof function doDamage
		
		
        
        public function getDamage($fireOrder){        return Dice::d(5)+12;   }
        public function setMinDamage(){     $this->minDamage = 13 ;      }
        public function setMaxDamage(){     $this->maxDamage = 17 ;      }

    }//endof class SolarCannon



    class LightParticleBlaster extends LinkedWeapon{
        public $name = "lightParticleBlaster";
        public $displayName = "Light Particle Blaster";
        public $animation = "bolt";
        public $animationColor = array(230, 115, 0);
	    /*
        public $trailColor = array(230, 115, 0);
        public $animationExplosionScale = 0.10;
        public $projectilespeed = 12;
        public $animationWidth = 2;
        public $trailLength = 10;
*/
        public $intercept = 2;

        public $loadingtime = 1;
        public $shots = 2;
        public $defaultShots = 2;
		public $priority = 3;

        public $rangePenalty = 2;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals
        private $damagebonus = 0;
        
        public $damageType = "Standard"; 
        public $weaponClass = "Particle"; 
        

        function __construct($startArc, $endArc, $damagebonus, $nrOfShots = 2){
            $this->damagebonus = $damagebonus;
            $this->defaultShots = $nrOfShots;
            $this->shots = $nrOfShots;
            $this->intercept = $nrOfShots;		


            if ($damagebonus >= 4) $this->priority++; //heavier varieties fire later in the queue
            if ($damagebonus >= 6) $this->priority++;
            if ($damagebonus >= 8) $this->priority++;

            if($nrOfShots === 3){
                $this->iconPath = "pairedParticleGun3.png";
            }

            parent::__construct(0, 1, 0, $startArc, $endArc);

        }

        public function setSystemDataWindow($turn){

            //$this->data["Weapon type"] = "Particle";
            //$this->data["Damage type"] = "Standard";

            parent::setSystemDataWindow($turn);
        }

        public function getDamage($fireOrder){        return Dice::d(3)+$this->damagebonus;   }
        public function setMinDamage(){     $this->minDamage = 1+$this->damagebonus ;      }
        public function setMaxDamage(){     $this->maxDamage = 3+$this->damagebonus ;      }

    }



    class LightParticleBeam extends LinkedWeapon{
        public $name = "lightParticleBeam";
        public $iconPath = "lightParticleBeam.png";
        public $displayName = "Light Particle Beam";
        public $animation = "bolt";
        public $animationColor = array(230, 115, 0);
	    /*
        public $trailColor = array(230, 115, 0);
        public $animationExplosionScale = 0.10;
        public $projectilespeed = 12;
        public $animationWidth = 2;
        public $trailLength = 10;
	*/
	    
        public $priority = 3;
        public $intercept = 2;

        public $loadingtime = 1;
        public $shots = 2;
        public $defaultShots = 2;

        public $rangePenalty = 2;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals
        private $damagebonus = 0;

        public $damageType = "Standard"; 
        public $weaponClass = "Particle"; 
        
        function __construct($startArc, $endArc, $damagebonus, $nrOfShots = 2){
            $this->damagebonus = $damagebonus;
            $this->defaultShots = $nrOfShots;
            $this->shots = $nrOfShots;
            $this->intercept = $nrOfShots;

            if ($damagebonus >= 3) $this->priority++; //heavier varieties fire later in the queue
            if ($damagebonus >= 5) $this->priority++;
            if ($damagebonus >= 7) $this->priority++;
			
            if($nrOfShots === 1){
                $this->iconPath = "lightParticleBeam1.png";
            }
            if($nrOfShots >2){//no special icon for more than 3 linked weapons
                $this->iconPath = "lightParticleBeam3.png";
            }
			
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
        }

        public function getDamage($fireOrder){        return Dice::d(6)+$this->damagebonus;   }
        public function setMinDamage(){     $this->minDamage = 1+$this->damagebonus ;      }
        public function setMaxDamage(){     $this->maxDamage = 6+$this->damagebonus ;      }

    }



    class HeavyBolter extends Particle{
        public $name = "heavyBolter";
        public $displayName = "Heavy Bolter";
        public $animation = "bolt";
        public $animationColor = array(204, 122, 0);
	    /*
        public $animationExplosionScale = 0.5;
        public $projectilespeed = 12;
        public $animationWidth = 6;
        public $trailLength = 6;
	*/
	    
        public $priority = 6;
        public $loadingtime = 3;

        public $rangePenalty = 0.33;
        public $fireControl = array(-1, 2, 3); // fighters, <mediums, <capitals


        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){  
			return 24;   		
		}
        public function setMinDamage(){     $this->minDamage = 24 ;      }
        public function setMaxDamage(){     $this->maxDamage = 24 ;      }
    }
    


    class MediumBolter extends Particle{
        public $name = "mediumBolter";
        public $displayName = "Medium Bolter";
        public $animation = "bolt";
        public $animationColor = array(204, 122, 0);
	    /*
        public $animationExplosionScale = 0.4;
        public $projectilespeed = 14;
        public $animationWidth = 4;
        public $trailLength = 4;
	*/

	    public $priority = 6;
        public $loadingtime = 2;

        public $intercept = 1;

        public $rangePenalty = 0.5;
        public $fireControl = array(1, 2, 3); // fighters, <mediums, <capitals


        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return 18;   }
        public function setMinDamage(){     $this->minDamage = 18 ;      }
        public function setMaxDamage(){     $this->maxDamage = 18 ;      }
    }
    
    class LightBolter extends Particle{
        public $name = "lightBolter";
        public $displayName = "Light Bolter";
        public $animation = "bolt";
        public $animationColor = array(204, 122, 0);
	    /*
        public $animationExplosionScale = 0.3;
        public $projectilespeed = 16;
        public $animationWidth = 3;
        public $trailLength = 3;
	*/
	    
        public $priority = 5;  
        public $loadingtime = 1;
        public $intercept = 1;

        public $rangePenalty = 1;
        public $fireControl = array(3, 2, 2); // fighters, <mediums, <capitals


        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return 12;   }
        public function setMinDamage(){     $this->minDamage = 12 ;      }
        public function setMaxDamage(){     $this->maxDamage = 12 ;      }
    }
    

    class LightParticleBeamShip extends StdParticleBeam{
        public $name = "lightParticleBeamShip";
        public $displayName = "Light Particle Beam";
        public $iconPath = "lightParticleBeamShip.png";
	    /*inherited
        public $animation = "beam";
        public $animationColor = array(255, 153, 51);
        public $trailColor = array(255, 153, 51);
        public $animationExplosionScale = 0.12;
        public $projectilespeed = 12;
        public $animationWidth = 3;
        public $trailLength = 8;

        public $intercept = 2;
        public $loadingtime = 1;
	*/
        public $priority = 4;

        public $rangePenalty = 2;
        public $fireControl = array(3, 3, 3); // fighters, <mediums, <capitals

	    /*
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
	*/

        public function getDamage($fireOrder){        return Dice::d(10)+4;   }
        public function setMinDamage(){     $this->minDamage = 5 ;      }
        public function setMaxDamage(){     $this->maxDamage = 14 ;      }
    }


        /*Belt Alliance version of Mk I Interceptor - identical to EA one, but without EWeb*/
    class BAInterceptorMkI extends Particle{
        public $name = "BAInterceptorMkI";
        public $displayName = "BA Interceptor I";
        public $iconPath = "interceptor.png";
        
        public $animation = "bolt";
        public $animationColor = array(255, 163, 26);
	    /*
        public $trailColor = array(255, 163, 26);
        public $animationExplosionScale = 0.15;
        public $animationWidth = 1;
            */
	    
        public $priority = 4;	    
        public $intercept = 3;
        public $loadingtime = 1;
  
        public $rangePenalty = 2;
        public $fireControl = array(6, null, null); // fighters, <mediums, <capitals 
        
        public $damageType = "Standard"; 
        public $weaponClass = "Particle";
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 4;
            }
            if ( $powerReq == 0 ){
                $powerReq = 1;
            }	
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return Dice::d(10)+5;   }
        public function setMinDamage(){     $this->minDamage = 6 ;      }
        public function setMaxDamage(){     $this->maxDamage = 15 ;      }
        
    }



        /*Belt Alliance version of Interceptor Prototype - identical to EA one, but without EWeb*/
    class BAInterceptorPrototype extends Particle{
        public $name = "BAInterceptorPrototype";
        public $displayName = "BA Interceptor Prototype";
        public $iconPath = "interceptor.png";
        
        public $animation = "bolt";
        public $animationColor = array(255, 163, 26);
	    /*
        public $trailColor = array(255, 163, 26);
        public $animationExplosionScale = 0.15;
        public $animationWidth = 1;
            */
	    
        public $priority = 3;	    
        public $intercept = 2;
        public $loadingtime = 1;
  
        public $rangePenalty = 2;
        public $fireControl = array(4, null, null); // fighters, <mediums, <capitals 
        
        public $damageType = "Standard"; 
        public $weaponClass = "Particle";
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 4;
            }
            if ( $powerReq == 0 ){
                $powerReq = 1;
            }	
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return Dice::d(10)+3;   }
        public function setMinDamage(){     $this->minDamage = 4 ;      }
        public function setMaxDamage(){     $this->maxDamage = 13 ;      }
        
    }



        /*Abbai weapon - Twin Array on steroids and with overheating problems*/
    class QuadArray extends Particle{
        public $name = "quadArray";
        public $displayName = "Quad Array";
        public $iconPath = "quadArray.png";
        public $animation = "bolt";

        public $intercept = 2;
        public $priority = 4;

        public $loadingtime = 1;
        public $guns = 4;
        public $rangePenalty = 2;
        public $fireControl = array(6, 5, 4); // fighters, <mediums, <capitals

        public $firingModes = array(1=>'4Quad', 2=>'3Triple', 3=>'2Dual', 4=>'1Single', 5=>'Split');

        public $canSplitShots = false; //Allows Firing Mode 2 to split shots.
        public $canSplitShotsArray = array(1=>false, 2=>false, 3=>false, 4=>false, 5=>true  );          
        public $gunsArray = array(1=>4,2=>3,3=>2,4=>1,5=>4);
	    
	    private $firedThisTurn = false; //to avoid re-rolling criticals!

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 8;
            }
            if ( $powerReq == 0 ){
                $powerReq = 4;
            }            
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            $this->data["Special"] = "Can use 'Split' Firing Mode to target different enemy units."; 
            $this->data["Special"] .= '<br>If three or four shots are fired, gains May Overheat critical(s) next turn.';    
            $this->data["Special"] .= "<br>When weapon May Overheat, firing offensively causes a critical roll."; 
            $this->data["Special"] .= "<br>This roll has +2 modifier for evey shot fired in current turn, and single -2 if only one 'May Overheat' critical effect.";  
            $this->data["Special"] .= "<br>Dual or Single shots do not cause overheating the following turn, nor will defensive fire unless 'Split' firing mode was used.";                               
        }

	//Fire function for Quad Array
	public function fire($gamedata, $fireOrder){ 
	// If fires 3 or 4 shots, Quad Array might overheat next turn. Make a crit roll taking into account number of shots fired this turn and last.
	 
		parent::fire($gamedata, $fireOrder);

		 	if ($this->firedThisTurn) return; //Crits already accounted for (if necessary)
			$this->firedThisTurn = true; //To avoid rolling and adding crits for every shot fired, just once.


			$shotsThisTurn = count($this->getFireOrders($gamedata->turn)); //How many shots were fired this turn?, if so roll critical as before:
			$overheatLevel = $this->hasCritical("MayOverheat", $gamedata->turn);//Check for criticals from last turn firing. 		
			
			if(($shotsThisTurn > 0) && ($overheatLevel > 0)){//Check that weapon fired (it should have) and there's at least one 'May Overheat' critical.
			 $this->critRollMod += $shotsThisTurn * 2; // +2 for every shot fired this turn.
			 if ($overheatLevel == 1) $this->critRollMod -= 2; // -2 to crit roll if 3 or fewer shots were fired last turn. 
			 	
			 // Now roll for possible crits this turn. 
 		     $shooter = $gamedata->getShipById($fireOrder->shooterid);
			 $crits = array(); 
			 $crits = $this->testCritical($shooter, $gamedata, $crits);//Added $shooter for ship variable.
			}

		}//end of function Fire

    public function criticalPhaseEffects($ship, $gamedata) { 
            parent::criticalPhaseEffects($ship, $gamedata);//Some critical effects like Limpet Bore might destroy weapon in this phase        

            if($this->isDestroyed()) return;//Quad Array is destroyed, no further action.

			$shotsThisTurn = count($this->getFireOrders($gamedata->turn)); //How many offensive & defensive shots were fired this turn?

            if($shotsThisTurn == 0) return;//No shots, no further action.            

            $interceptsThisTurn = $this->firedDefensivelyAlready;  //Defensive shots this turn.
            if($this->firingMode != 5) $shotsThisTurn -= $interceptsThisTurn;  //If split mode wasn't used, don't add automated intercepts.                   
		
        //Add any new crits for next turn. 
		if($shotsThisTurn == 4){//Quad 
            $crit1 = new MayOverheat(-1, $ship->id, $this->id, "MayOverheat", $gamedata->turn, $gamedata->turn+1);
            $crit1->updated = true;
            $crit1->newCrit = true; //force save even if crit is not for current turn
            $this->criticals[] =  $crit1;
            //Add second critical when 4 shots are fired.
            $crit2 = clone $crit1;
            $this->criticals[] =  $crit2;
        }else if ($shotsThisTurn == 3){//Triple 
            $crit = new MayOverheat(-1, $ship->id, $this->id, "MayOverheat", $gamedata->turn, $gamedata->turn+1);
            $crit->updated = true;
            $crit->newCrit = true; //force save even if crit is not for current turn
            $this->criticals[] =  $crit;
        }  
    }    
                    
        public function getDamage($fireOrder){        return Dice::d(10)+4;   }
        public function setMinDamage(){     $this->minDamage = 5 ;      }
        public function setMaxDamage(){     $this->maxDamage = 14 ;      }

    } //endof class QuadArray
	

    class ParticleHammer extends Particle{     
        
        public $name = "particleHammer";
        public $displayName = "Particle Hammer";
        public $iconPath = "ParticleHammer.png";

        public $animation = "bolt";
        public $animationColor = array(255, 163, 26);   
	    /*
        public $trailColor = array(255, 163, 26);
        public $animationExplosionScale = 0.5;
        public $projectilespeed = 12;
        public $animationWidth = 10;
        */
        public $loadingtime = 4;
	    
        public $damageType = "Standard"; 
        public $weaponClass = "Particle"; 
        
        public $rangePenalty = 0.33;
        public $fireControl = array(-2, 1, 3); // fighters, <mediums, <capitals
        public $priority = 6;
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            if ( $maxhealth == 0 ) $maxhealth = 12;
            if ( $powerReq == 0 ) $powerReq = 5;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return Dice::d(10, 2)+15;   }
        public function setMinDamage(){     $this->minDamage = 17 ;      }
        public function setMaxDamage(){     $this->maxDamage = 35 ;      }
        
    }//End of Particle Hammer
	

/*AoG did only Particle Projector. Nexus custom ships use other weights of this line as well.
EDIT: other weapons in the line do indeed exist, on Usuuth ships.
Nonetheless two copies of Particle Projector lines now exist in FV, in customNexus and particle files. They should be have the same properties.
*/
    class ParticleProjector extends Particle{
        public $name = "particleProjector";
        public $displayName = "Particle Projector";
        public $animation = "beam";
        public $animationColor = array(255, 163, 26);
	    /*
        public $trailColor = array(255, 163, 26);
        public $animationExplosionScale = 0.15;
        public $projectilespeed = 12;
        public $animationWidth = 4;
        public $trailLength = 10;
*/
        public $intercept = 2;
        public $loadingtime = 2;
        public $priority = 4;

        public $rangePenalty = 1;
        public $fireControl = array(1, 2, 2); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            if ( $maxhealth == 0 ) $maxhealth = 6;
            if ( $powerReq == 0 ) $powerReq = 1;		
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(10, 1)+4;   }
        public function setMinDamage(){     $this->minDamage = 5 ;      }
        public function setMaxDamage(){     $this->maxDamage = 14 ;      }
    }

    class HvyParticleProjector extends Particle{        
        public $name = "hvyParticleProjector";
        public $displayName = "Heavy Particle Projector";
        public $iconPath = "HeavyParticleProjector.png";
        public $animation = "beam";
        public $animationColor = array(255, 163, 26);    
	    /*
        public $trailColor = array(255, 163, 26);
        public $animationExplosionScale = 0.4;
        public $projectilespeed = 15;
        public $animationWidth = 4;
        */
        public $intercept = 1;
        
        public $loadingtime = 3;
        
        public $rangePenalty = 0.5;
        public $fireControl = array(-1, 2, 3); // fighters, <mediums, <capitals
        public $priority = 6;
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            if ( $maxhealth == 0 ) $maxhealth = 8;
            if ( $powerReq == 0 ) $powerReq = 3;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return Dice::d(10, 2)+8;   }
        public function setMinDamage(){     $this->minDamage = 10 ;      }
        public function setMaxDamage(){     $this->maxDamage = 28 ;      }
        
    }//End of Heavy Particle Projector
    
	
    class LightParticleProjector extends Particle{        
        public $name = "lightParticleProjector";
        public $displayName = "Light Particle Projector";
        public $iconPath = "LightParticleProjector.png";       
        public $animation = "trail";
        public $animationColor = array(255, 163, 26);  
	    /*
        public $trailColor = array(255, 163, 26);
        public $animationExplosionScale = 0.15;
        public $projectilespeed = 12;
        public $animationWidth = 1;
        public $trailLength = 5;
        */
        public $loadingtime = 1;
        public $intercept = 2;
        
        public $rangePenalty = 2;
        public $fireControl = array(3, 2, 2); // fighters, <mediums, <capitals
        public $priority = 4;
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            if ( $maxhealth == 0 ) $maxhealth = 3;
            if ( $powerReq == 0 ) $powerReq = 1;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return Dice::d(6)+4;   }
        public function setMinDamage(){     $this->minDamage = 5 ;      }
        public function setMaxDamage(){     $this->maxDamage = 10 ;      }
        
    }//End of Light Particle Projector

    
 //New version that switches to Pulse damage against fighters - DK Dec 25  
class PentagonArray extends Raking{
	public $name = "PentagonArray";
	public $displayName = "Pentagon Array";
	public $iconPath = "PentagonArray.png";
	public $animation = "bolt";
	//public $animation = "laser"; //by the fluff it's five LPBs firing a hail of bolts, but damage is rolled and resolved as a single raking attack - so beam animation seems more appropriate
	public $animationColor = array(255, 153, 51);
    public $animationExplosionScale = 0.2; //The potential damage makes this a bit too big, set manually

	public $rakes = array();
	public $priority = 6; //light Raking weapon, effectively
	public $loadingtime = 1;
	public $rangePenalty = 1; //-1 per hex
	public $fireControl = array(3, 3, 3); // fighters, <mediums, capitals
	public $intercept = 5; //interception of -5
		
	public $firingModes = array(1=>'Raking'); //Actually switch to Pulse against fighters!

	public $damageType = "Raking"; //Actually switch to Pulse against fighters!
	public $weaponClass = "Particle";

    public $grouping = 0;
    public $maxpulses = 5;
    public $rof = 1;
    private $targetedFighters = false; //tracks if fighters were targetd for damage roll.
    private $fighterArmourIgnored = array(); //Tracks armour already dealt with when doing pulse damage to fighters

	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		if ( $maxhealth == 0 ) $maxhealth = 8;
		if ( $powerReq == 0 ) $powerReq = 5;
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
	}

	public function getRakeSize(){
			$rakesize = array_shift($this->rakes);
			$rakesize = max(1,$rakesize);//return 1 if no rakes remain
			return $rakesize;
	}

	public function rollPulses($turn, $needed, $rolled){
		return 5; //Always 5 pulses/rakes against fighters
	}

    public function fire($gamedata, $fireOrder){
        $target = $gamedata->getShipById($fireOrder->targetid);        
        if($target instanceof FighterFlight){
            $this->damageType = 'Pulse';
            $this->targetedFighters = true;                        
        }

        parent::fire($gamedata, $fireOrder);
    }    

	public function getDamage($fireOrder){
        $damage = 0;
        if($this->targetedFighters){
            $damage = Dice::d(10);
        }else{
            $this->rakes = array();
            $damage = 0;
            $rake = Dice::d(10);
            $damage+=$rake;
            $this->rakes[] = $rake;
            $rake = Dice::d(10);
            $damage+=$rake;
            $this->rakes[] = $rake;
            $rake = Dice::d(10);
            $damage+=$rake;
            $this->rakes[] = $rake;
            $rake = Dice::d(10);
            $damage+=$rake;
            $this->rakes[] = $rake;
            $rake = Dice::d(10);
            $damage+=$rake;
            $this->rakes[] = $rake;
        }    
		return $damage;
	}			

	protected function doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata, $damageWasDealt, $location = null)
    {
		if ($system->getRemainingHealth() > 0) { //Vree Structure systems are considered not there despite not being formally destroyed
            $damage = floor($damage);//make sure damage is a whole number, without fractions!
            $armour = $this->getSystemArmourComplete($target, $system, $gamedata, $fireOrder, $pos); //handles standard and Adaptive armor, as well as Advanced armor and weapon class modifiers
			// ...if armor-related modifications are needed, they should extend appropriate method (Complete or Base, as Adaptive should not be affected)
			// ...and doDamage should always call Complete


            //armor may be ignored for some reason... usually because of Raking mode :)
            $armourIgnored = 0;
            $fighterArmourIgnored = 0;
            if (isset($fireOrder->armorIgnored[$system->id])) {
                $armourIgnored = $fireOrder->armorIgnored[$system->id];
                $armour = $armour - $armourIgnored;
            }

            //Against fighters we have to keep a separate tracker since it hits in Pulse mode. 	
            if ($target instanceof FighterFlight) {		
                if (isset($this->fighterArmourIgnored[$system->id])) {		
                    $fighterArmourIgnored = $this->fighterArmourIgnored[$system->id];
                    $armour = $armour - $fighterArmourIgnored;          
                }        
            }     

            $armour = max($armour, 0);

			//returned array: dmgDealt, dmgRemaining, armorPierced	
			$damage = $this->beforeDamagedSystem($target, $system, $damage, $armour, $gamedata, $fireOrder);
			$effects = $system->assignDamageReturnOverkill($target, $shooter, $this, $gamedata, $fireOrder, $damage, $armour, $pos, $damageWasDealt); //Also applies armor pierced for sustained shots  - DK 02.25
			$this->onDamagedSystem($target, $system, $effects["dmgDealt"], $effects["armorPierced"], $gamedata, $fireOrder);//weapons that do effects on hitting something
			$damage = $effects["dmgRemaining"];
			if ($this->damageType == 'Raking'){ //note armor already pierced so further rakes have it easier. All Sustained weapons are currently raking, so they can use standard method here - DK
				$armourIgnored = $armourIgnored + $effects["armorPierced"]; 
				$fireOrder->armorIgnored[$system->id] = $armourIgnored;
			}			

            if ($target instanceof FighterFlight) {	 //note armor for the separate fighter tracker - DK
				$fighterArmourIgnored = $fighterArmourIgnored + $effects["armorPierced"]; 
				$this->fighterArmourIgnored[$system->id] = $fighterArmourIgnored;
			}	

            $damageWasDealt = true; //actual damage was done! might be relevant for overkill allocation
        }

        if (($damage > 0) || (!$damageWasDealt)) {//overkilling!
            $overkillSystem = $this->getOverkillSystem($target, $shooter, $system, $fireOrder, $gamedata, $damageWasDealt, $location);
            if ($overkillSystem != null)
                $this->doDamage($target, $shooter, $overkillSystem, $damage, $fireOrder, $pos, $gamedata, $damageWasDealt, $location);
        }

    } //function doDamage

		
	public function setMinDamage(){
		$this->minDamage = 5;
	}

	public function setMaxDamage(){
		$this->maxDamage = 50;
	}

	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		$this->data["Special"] = "Causes always 5 rakes, each dealing 1d10 damage.";
		$this->data["Special"] .= "<br>Against fighters weapon deals damage in Pulse mode, with 5 pulses causing 1d10 damage each."; 
	}    

} //end of class PentagonArray

/* //OLD VERSION - Keep until I'm sure new version works ok!	
class PentagonArray extends Raking{
	public $name = "PentagonArray";
	public $displayName = "Pentagon Array";
	public $iconPath = "PentagonArray.png";
	//public $animation = "bolt";
	public $animation = "laser"; //by the fluff it's five LPBs firing a hail of bolts, but damage is rolled and resolved as a single raking attack - so beam animation seems more appropriate
	public $animationColor = array(255, 153, 51);

	public $rakes = array();
	public $priority = 8; //light Raking weapon, effectively
	public $loadingtime = 1;
	public $rangePenalty = 1; //-1 per hex
	public $fireControl = array(3, 3, 3); // fighters, <mediums, <capitals
	public $intercept = 5; //interception of -5
		
	public $firingModes = array(1=>'Raking');

	public $damageType = "Raking"; 
	public $weaponClass = "Particle";

	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		if ( $maxhealth == 0 ) $maxhealth = 8;
		if ( $powerReq == 0 ) $powerReq = 5;
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
	}

	public function getRakeSize(){
			$rakesize = array_shift($this->rakes);
			$rakesize = max(1,$rakesize);//return 1 if no rakes remain
			return $rakesize;
	}

	public function getDamage($fireOrder){
		$this->rakes = array();
		$damage = 0;
		$rake = Dice::d(10);
		$damage+=$rake;
		$this->rakes[] = $rake;
		$rake = Dice::d(10);
		$damage+=$rake;
		$this->rakes[] = $rake;
		$rake = Dice::d(10);
		$damage+=$rake;
		$this->rakes[] = $rake;
		$rake = Dice::d(10);
		$damage+=$rake;
		$this->rakes[] = $rake;
		$rake = Dice::d(10);
		$damage+=$rake;
		$this->rakes[] = $rake;

		return $damage;
	}			
		
	public function setMinDamage(){
		$this->minDamage = 5;
	}

	public function setMaxDamage(){
		$this->maxDamage = 50;
	}

	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		$this->data["Special"] = "Causes always 5 rakes, each dealing 1d10 damage.";
		$this->data["Special"] .= "<br>Can sweep multiple fighters, but single rake will not overkill into another fighter."; //simplification, every rake should be assigned separately - but FV wouldn't actually recognize benefit of spreading damage among multiple fighters unless toughness+armor>=10 (eg. Tzymm and larger)
	}

	//if target is fighter and damage was already dealt (eg. it's overkill):
	// - account for rake size (single rake cannot overkill)
	// - account for shields (again - separate fighter is NOT same shield pierced)
	protected function doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata, $damageWasDealt, $location = null)
    {
		if ($damageWasDealt && ($target instanceof FighterFlight)) {
			//account for proper rake size
			$acceptedDamage = 0;
			$tryingDamage = 0;
			for($i=4;$i>=0;$i--){//starting with last possible rake and going backwards 
				if (isset($this->rakes[$i])){
					$tryingDamage += $this->rakes[$i];
					if ($tryingDamage <= $damage){ //damage fits remaining rakes
						$acceptedDamage = $tryingDamage;
					} else {//too much!
						$tryingDamage = 1000; //just in case
						break; //don't check further rakes, obviously
					}
				}
			}			
			$damage = $acceptedDamage; //this much damage fits remaining rake sizes! any more was part of rake already allocated to previous fighter
			//account for target fighter's shields (and similar damage-reducing mechanisms that affect entire shot)
			$damage -= $target->getDamageMod($shooter, $pos, $gamedata->turn, $this);
			$damage = max(0,$damage);
		}
		//then standard :)
		parent::doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata, $damageWasDealt, $location );
    } //function doDamage

	//this weapon CAN overkill into another fighter in flight!
	protected function getOverkillSystem($target, $shooter, $system, $fireOrder, $gamedata, $damageWasDealt, $location = null){
		if ($target instanceof FighterFlight){//if target is fighter flight (usually no overkill), overkill into another fighter instead
			$newTarget = $target->getHitSystem($shooter, $fireOrder, $this, $gamedata, $location);
			return $newTarget;
		}else{ //standard
			return parent::getOverkillSystem($target, $shooter, $system, $fireOrder, $gamedata, $damageWasDealt, $location);
		}
	}

} //end of class PentagonArray
*/
	
	
	
class ParticleAccelerator extends Raking{
		public $name = "ParticleAccelerator";
        public $displayName = "Particle Accelerator";
        public $iconPath = "ParticleAccelerator.png";
	
        public $animation = "laser";
        public $animationColor = array(255, 163, 26);
	/*
		public $animationExplosionScale = 0.25;
		public $animationWidth = 4;
		public $animationWidth2 = 0.3;
        */
        public $intercept = 2;
		public $priority = 8; //light Raking		
		
        public $loadingtime = 1;
		public $normalload = 2;
		
        public $rangePenalty = 0.5;
        public $fireControl = array(2, 4, 4);

        public $damageType = "Raking";
		public $weaponClass = "Particle";
		public $firingModes = array( 1 => "Raking");

	 	public function getInterceptRating($turn){
			if ($this->turnsloaded == 1)
			{
				return 2;
			}
			else
			{
				return 1;
			}
		}

		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){ //maxhealth and power reqirement are fixed; left option to override with hand-written values
			if ( $maxhealth == 0 ) $maxhealth = 8;
			if ( $powerReq == 0 ) $powerReq = 8;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);   
			$this->data["Special"] = "Can fire accelerated ROF for less damage:";  
			$this->data["Special"] .= "<br> - 1 turn: 1d10+6, intercept -10"; 
			$this->data["Special"] .= "<br> - 2 turns: 2d10+14, intercept -5"; 
		}
	
		public function getDamage($fireOrder){
        	switch($this->turnsloaded){
            	case 0:
            	case 1:
                	return Dice::d(10)+6;
			    	break;
            	default:
                	return Dice::d(10,2)+14;
			    	break;
        	}
		}

 		public function setMinDamage(){
            switch($this->turnsloaded){
                case 1:
                    $this->minDamage = 7 ;
                    break;
                default:
                    $this->minDamage = 16 ;  
                    break;
            }
		}
             
        public function setMaxDamage(){
            switch($this->turnsloaded){
                case 1:
                    $this->maxDamage = 16 ;
                    break;
                default:
                    $this->maxDamage = 34 ;  
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

}//end of class Particle Accelerator
	

//Custome weapon for Abraxas Gaim Campaign
class BoltAccelerator extends Raking{
		public $name = "BoltAccelerator";
        public $displayName = "Bolt Accelerator";
        public $iconPath = "BoltAccelerator.png";
	
        public $animation = "bolt";
        public $animationColor = array(255, 163, 26);
	/*
		public $animationExplosionScale = 0.25;
		public $animationWidth = 4;
		public $animationWidth2 = 0.3;
        */
        public $intercept = 3;
		public $priority = 8; //light Raking		
		
        public $loadingtime = 1;
		public $normalload = 3;
		
        public $rangePenalty = 0.33;
        public $fireControl = array(1, 3, 5);

        public $damageType = "Standard";
		public $weaponClass = "Particle";
		public $firingModes = array( 1 => "Standard");

	 	public function getInterceptRating($turn){
			if ($this->turnsloaded == 1)
			{
				return 3;
			}
			else if ($this->turnsloaded == 2)
			{
				return 2;
			} 
			else{
				return 1;
			}
		}

    public function calculateRangePenalty($distance)
    {
			if ($this->turnsloaded == 1)
			{
				return 1;
			}
			else if ($this->turnsloaded == 2)
			{
				return 0.5;
			} 
			else{
				return 0.33;
			}
    }

		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){ //maxhealth and power reqirement are fixed; left option to override with hand-written values
			if ( $maxhealth == 0 ) $maxhealth = 9;
			if ( $powerReq == 0 ) $powerReq = 7;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);   
			$this->data["Special"] = "Can fire accelerated ROF for less damage:";  
			$this->data["Special"] .= "<br> - 1 turn: 11 damage, intercept -5"; 
			$this->data["Special"] .= "<br> - 2 turns: 17 damage, intercept -10";
			$this->data["Special"] .= "<br> - 3 turns: 23 damage, intercept -15"; 			 
		}
	
		public function getDamage($fireOrder){
        	switch($this->turnsloaded){
            	case 0:
            	case 1:
                	return 11;
			    	break;
            	case 2:
                	return 17;
			    	break;			    	
            	default:
                	return 23;
			    	break;
        	}
		}

 		public function setMinDamage(){
            switch($this->turnsloaded){
                case 1:
                    $this->minDamage = 11 ;
                    break;
                case 2:
                    $this->minDamage = 17 ;
                    break;                    
                default:
                    $this->minDamage = 24 ;  
                    break;
            }
		}
             
        public function setMaxDamage(){
            switch($this->turnsloaded){
                case 1:
                    $this->minDamage = 11 ;
                    break;
                case 2:
                    $this->minDamage = 17 ;
                    break;                    
                default:
                    $this->minDamage = 24 ;  
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

}//end of class Heavy Bolt Accelerator	
	
//Torata fighter weapon - with OPTIONAL ability of a powerful antiship shot if armed for two turns.	
class LightParticleAccelerator extends LinkedWeapon{		
		public $name = "LightParticleAccelerator";
		public $displayName = "Light Particle Accelerator";
		public $iconPath = "LightParticleAccelerator.png";
		public $animation = "trail";
		public $trailColor = array(255, 163, 26);
	/*
		public $animationColor = array(255, 163, 26);
		public $animationExplosionScaleArray = array(1=>0.10, 2=>0.15);
		public $animationWidthArray = array(1=>2, 2=>3);
		public $trailLengthArray = array(1=>10, 2=>15);
        */
		
        public $loadingtime = 1;
		public $normalload = 2;
		
        public $loadingtimeArray = array(1=>1, 2=>2);
		public $shots = 2;
		public $defaultShots = 2;
		public $intercept = 2;
		public $firingMode = 1;
		public $firingModes = array(1 =>'Standard', 2=>'Charged');
		
		public $priorityArray = array(1=>3, 2=>6); //standard shot is very light, but charged almost equals Gatling Pulse Cannon (and LPA is twin-linked...)
	    public $rangePenalty = 2;
        public $fireControlArray = array(1=>array(0, 0, 0), 2=>array(-4, 0, 2) );
		
		public $damageType = "Standard";
		public $weaponClass = "Particle";  

		function __construct($startArc, $endArc, $nrOfShots = 2){
			$this->defaultShots = $nrOfShots;
			$this->shots = $nrOfShots;
			$this->intercept = $nrOfShots;
			parent::__construct(0, 1, 0, $startArc, $endArc);
		}	

		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);   
			$this->data["Special"] = "If not fired for one turn, can fire a charged shot:";  
			$this->data["Special"] .= "<br> - Standard: 1d6+2"; 
			$this->data["Special"] .= "<br> - Charged (alternate mode!): 2d6+4, with -20/0/10 fire control (i.e. optimised for ships)"; 
			$this->data["Special"] .= "<br>REMINDER: as an Accelerator weapon, it will not be used for interception unless specifically ordered to do so!"; 
		}
		
	
		public function getDamage($fireOrder){
        	switch($this->firingMode){ 
            	case 1:
                	return Dice::d(6)+2;
			    			break;
            	case 2:
            	   	return Dice::d(6,2)+4;
			    			break;
        	}
		}

		public function setMinDamage(){
				switch($this->firingMode){
						case 1:
								$this->minDamage = 3;
								break;
						case 2:
								$this->minDamage = 6;
								break;
				}
				$this->minDamageArray[$this->firingMode] = $this->minDamage;
		}							
		
		public function setMaxDamage(){
				switch($this->firingMode){
						case 1:
								$this->maxDamage = 8;
								break;
						case 2:
								$this->maxDamage = 16;
								break;
				}
				$this->maxDamageArray[$this->firingMode] = $this->maxDamage;
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
}//end of class Light Particle Accelerator	


    class LightParticleBolt extends Particle{
        public $name = "LightParticleBolt";
        public $displayName = "Light Particle Bolt";
   		public $iconPath = "LightParticleBolt.png";
        public $animation = "trail";
        public $animationColor = array(255, 163, 26);
	    /*
        public $animationExplosionScale = 0.2;
        public $projectilespeed = 16;
        public $animationWidth = 2;
        public $trailLength = 2;
	*/
        public $priority = 5;

        public $loadingtime = 1;

        public $intercept = 1;

        public $rangePenalty = 2;
        public $fireControl = array(2, 2, 1); // fighters, <mediums, <capitals


        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
        if ( $maxhealth == 0 ) $maxhealth = 3;
		if ( $powerReq == 0 ) $powerReq = 1;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10)+2;   }
        public function setMinDamage(){     $this->minDamage = 3 ;      }
        public function setMaxDamage(){     $this->maxDamage = 12 ;      }
    }	
	

class UnreliableTwinArray extends TwinArray{

    public $name = "UnreliableTwinArray";
    public $displayName = "Unreliable Twin Array";
    public $iconPath = "twinArray.png";

	protected $misfire3;

    public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		$this->data["Special"] = "This Twin Array is prone to misfires."; 
		$this->data["Special"] .= "<br>10% chance of misfire and doing no damage."; 
	}
	
	public function getDamage($fireOrder){
		$misfire3 = Dice::d(10,1);
		if ($misfire3 == 1) {
			$fireOrder->pubnotes .= "<br> Weapon misfire! No damage.";
			return (Dice::d(10)+4) * 0;
		}else{
			return Dice::d(10)+4;
		}
	}
	public function setMinDamage(){		$this->minDamage = 5;	}
	public function setMaxDamage(){		$this->maxDamage = 14;	}
	
} //endof class UnreliableTwinArray
	

class TelekineticCutter extends Raking{
    public $name = "TelekineticCutter";
    public $displayName = "Telekinetic Cutter";
    public $iconPath = "TelekineticCutter.png";
        	    
	public $animation = "laser";
    public $animationColor = array(204, 102, 0);

    public $intercept = 4;
    public $loadingtime = 1;
    public $priority = 6;
    public $guns = 2;
    
    public $rangePenalty = 0.33;
    public $fireControl = array(4, 4, 4); // fighters, <mediums, <capitals

    public $damageType = "Raking"; 
    public $weaponClass = "Particle";
    public $firingModes = array( 1 => "Normal", 2=> "Split");
    public $canSplitShots = false; //Allows Firing Mode 2 to split shots.
    public $canSplitShotsArray = array(1=>false, 2=>true );        
        
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		if ( $maxhealth == 0 ) $maxhealth = 12;
		if ( $powerReq == 0 ) $powerReq = 4;
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
	}

    public function setSystemDataWindow($turn){			
        parent::setSystemDataWindow($turn);   
        $this->data["Special"] = "Can use 'Split' Firing Mode to target different enemy units.";
    }

    public function getDamage($fireOrder){ return Dice::d(10, 4);   }
    public function setMinDamage(){     $this->minDamage = 4 ;      }
    public function setMaxDamage(){     $this->maxDamage = 40 ;      }

}//endof TelekineticCutter	


class MinorThoughtPulsar extends LinkedWeapon{
    public $name = "MinorThoughtPulsar";
    public $displayName = "Minor Thought Pulsar";
    public $iconPath = "MinorThoughtPulsar.png";

    public $damageType = "Standard"; 
    public $weaponClass = "Particle"; 
	    
    public $animation = "bolt";
    public $animationColor = array(204, 102, 0);
        
	public $factionAge = 3;//Ancient weapon, which sometimes has consequences!

    public $intercept = 1; //weapon can (..probably...) intercept at default rules... probably intended as a single shot of -2/-3 - but in FV these shots are separate, so that's a lot of separate -1s
    public $loadingtime = 1;
	public $guns = 2;

    public $rangePenalty = 2; //-2/hex
    public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals
    public $priority = 5; 

	public $firingMode = 1;	
    public $firingModes = array( 1 => "RoF", 2 => "Damage", 3 => "Hitchance", 4 => "1Combo1",  5 => "2Combo2");

	public $bonusDamageShots = 0;//Effectively a counter for shots.
	public $specialHitChanceCalculation = true;    

    function __construct($startArc, $endArc, $shots=1){
        $this->shots = $shots;
        $this->defaultShots = $shots;
        parent::__construct(0, 1, 0, $startArc, $endArc);
    }

	private function getSpareThrust($ship, $gamedata){
		$thrustUsed = 0;
		
		foreach($ship->movement as $shipMove){ //Update position and facing. Assume handled in order.			
			if($shipMove->turn != $gamedata->turn) continue; //Only interested in this turn.

            foreach ($shipMove->assignedThrust as $i=>$value){
				if (empty($value))
					continue;
					
				if ($ship instanceof FighterFlight){ //Check if fighter, just in case weapon ends up on a ship.
					$thrustUsed += $value;
				}					
			}
		}
			
		return $thrustUsed;
	}//endof getSpareThrust


    public function beforeFiringOrderResolution($gamedata){

      $firingOrders = $this->getFireOrders($gamedata->turn);
    	
      $originalFireOrder = null;
              foreach ($firingOrders as $fireOrder) { 
              	   if ($fireOrder->type == 'normal') { 
                    $originalFireOrder = $fireOrder;
                    break; //no need to search further
                    }
				}    			
				
        if($originalFireOrder==null) return; //no appropriate fire order, end of work
	
		$ship = $this->getUnit();
		$target = $gamedata->getShipById($originalFireOrder->targetid);
		
		$freeThrust = $ship->freethrust; //Should be 16.	
		$thrustUsed = $this->getSpareThrust($ship, $gamedata);	//16 - used	
		$thrustForBoost = $freeThrust - $thrustUsed;	
		if($thrustForBoost < 3) return; //Not enough spare thrust to do anything, no boosts.		
		$noOfBoosts = floor($thrustForBoost/3);	//Divide remaining thurst by 3, rounded down.
	
		$this->changeFiringMode($originalFireOrder->firingMode);//Change Firing Mode, just in case.
			
			switch($this->firingMode){
				
				case 1: //Prioritise RoF then hitchance
				
					if($noOfBoosts < 3){
						$newShots = $noOfBoosts;
						for($i = 0; $i < $newShots; $i++){
							$this->createNewFireOrder($ship, $target, $gamedata, $originalFireOrder);
						}																	
					}else{	
						$newShots = 2;//Create extra two shots to max 4.
						for($i = 0; $i < $newShots; $i++){
							$this->createNewFireOrder($ship, $target, $gamedata, $originalFireOrder);
						}								
													
						$OBBoost = max(0, $noOfBoosts-$newShots) * 2;//Dump any remaining into hitchance, ensure not a minus.					
						$this->fireControl[0] += $OBBoost;
						$this->fireControl[1] += $OBBoost;
						$this->fireControl[2] += $OBBoost;	
					}
					break;
						
						
				case 2: //Priortise Damage then hitchance
				
					if($noOfBoosts < 3){
						$this->bonusDamageShots += $noOfBoosts; //First two boosts go into Damage.						
					}else{
						$this->bonusDamageShots += 2; //First two boosts go into Damage.							
						$OBBoost = max(0, $noOfBoosts-2) * 2;//Dump any remaining into hitchance, ensure not a minus.
							
						$this->fireControl[0] += $OBBoost;
						$this->fireControl[1] += $OBBoost;
						$this->fireControl[2] += $OBBoost;	
					}
					break;
				
				
				case 3: //Prioritise only Hitchance
				
					$OBBoost = $noOfBoosts * 2;//Dump all into hitchance.
						
						$this->fireControl[0] += $OBBoost;
						$this->fireControl[1] += $OBBoost;
						$this->fireControl[2] += $OBBoost;	
						
					break;


				case 4://Prioritise Damage, Shots then Hitchance

					if($noOfBoosts <= 1){//1 boost
						$this->bonusDamageShots += 1; //Boost damage of first shot.						
					}else if($noOfBoosts == 2){//2 boosts
						$this->bonusDamageShots += 1; //Split first two boosts go into damage and an extra shot.							
						$this->createNewFireOrder($ship, $target, $gamedata, $originalFireOrder);//Then add extra shot
					}else if($noOfBoosts == 3){//3 boosts
						$this->bonusDamageShots += 2; //First two boosts go into damage.							
						$this->createNewFireOrder($ship, $target, $gamedata, $originalFireOrder);//Then add extra shot(3)
					}else if($noOfBoosts == 4){//4 boosts
						$this->createNewFireOrder($ship, $target, $gamedata, $originalFireOrder);//Add extra shot(3)
						$this->bonusDamageShots += 3; //Then three boosts into damage.							
					}else{//5 or more Boosts
						$this->createNewFireOrder($ship, $target, $gamedata, $originalFireOrder);//Add extra shot
						$this->bonusDamageShots += 3; //Then three boosts into damage.
						$OBBoost = 1*2;//Dump remaining into hitchance, ensure not a minus.						
								
						$this->fireControl[0] += $OBBoost;
						$this->fireControl[1] += $OBBoost;
						$this->fireControl[2] += $OBBoost;	
					}
					break;
					
					
				case 5://Prioritise Shots, and Damage equally.
				
					if($noOfBoosts <= 1){//1 boost - extra shot(3)
						$this->createNewFireOrder($ship, $target, $gamedata, $originalFireOrder);			
					}else if($noOfBoosts == 2){//2 boosts
						$this->bonusDamageShots += 1; //Split first two boosts into damage and an extra shot.							
						$this->createNewFireOrder($ship, $target, $gamedata, $originalFireOrder);
					}else if($noOfBoosts == 3){//3 boosts
						$this->bonusDamageShots += 1; //First boosts goes into damage.							
						$newShots = 2; //Then create two extra shots
						for($i = 0; $i < $newShots; $i++){
							$this->createNewFireOrder($ship, $target, $gamedata, $originalFireOrder);
						}
					}else if($noOfBoosts == 4){//4 boosts
						$this->bonusDamageShots += 2; //Two boosts go into damage.							
						$newShots = 2;//Then create two extra shots
						for($i = 0; $i < $newShots; $i++){
							$this->createNewFireOrder($ship, $target, $gamedata, $originalFireOrder);
						}						
					}else{//5 of more Boosts
						$this->bonusDamageShots += 3; //Three boosts go into damage.							
						$newShots = 2;//Then create two extra shots
						for($i = 0; $i < $newShots; $i++){
							$this->createNewFireOrder($ship, $target, $gamedata, $originalFireOrder);
						}	
					}
					break;

				
				default://Same as Case 1.
					if($noOfBoosts < 3){
						// Create a new FireOrders
						$newShots = $noOfBoosts;
						for($i = 0; $i < $newShots; $i++){
							$this->createNewFireOrder($ship, $target, $gamedata, $originalFireOrder);
						}																	
					}else{	
						$newShots = 2;
						for($i = 0; $i < $newShots; $i++){
							$this->createNewFireOrder($ship, $target, $gamedata, $originalFireOrder);
						}								
													
						$OBBoost = max(0, $noOfBoosts-$newShots) * 2;//Dump any remaining into hitchance, ensure not a minus.					
						$this->fireControl[0] += $OBBoost;
						$this->fireControl[1] += $OBBoost;
						$this->fireControl[2] += $OBBoost;	
					}
					break;
													
			}    	
	
	}//endof beforeFiringOrderResolution	

	public function createNewFireOrder($ship, $target, $gamedata, $originalFireOrder){
		
		$newFireOrder = new FireOrder(
		    -1, "normal", $ship->id, $target->id,
		    $this->id, -1, $gamedata->turn, $originalFireOrder->firingMode, 
		    0, 0, 1, 0, 0, // needed, rolled, shots, shotshit, intercepted
		    0, 0, $this->weaponClass, -1 // X, Y, damageclass, resolutionorder
		);        

		$newFireOrder->addToDB = true;
		$this->fireOrders[] = $newFireOrder;			
	}

        
    public function setSystemDataWindow($turn){          
            parent::setSystemDataWindow($turn);
			$this->data["Special"] = "Can be improved for each 3 points of remaining Thrust after Movement.";  
			$this->data["Special"] .= "<br>Select type of bonus using Firing Modes as follow:";
			$this->data["Special"] .= "<br> - Rate of Fire - Prioritises extra shots this turn (+1 per 3 Thrust).";
			$this->data["Special"] .= "<br> - Damage - Prioritises extra Damage this turn (+5 per shot per 3 Thrust).";
			$this->data["Special"] .= "<br> - Hit Chance - Uses all thrust to improve hit chance (+10% per 3 Thrust).";				
			$this->data["Special"] .= "<br> - Combo 1 - Prioritises Damage then shots, then Hitchance.";	
			$this->data["Special"] .= "<br> - Combo 2 - Prioritises Shots and Damage equally.";				
    }

    public function getDamage($fireOrder){         
		$damage = Dice::d(6,1)+ 5;
		  	    
	    if($this->bonusDamageShots > 0){    	
	    	$damage +=5;
	    	$this->bonusDamageShots -= 1;	
	    }   
	    
	    return $damage;    
    }
    
    public function setMinDamage(){     $this->minDamage = 6 ;      }
    public function setMaxDamage(){     $this->maxDamage = 11 ;      }

	public function stripForJson(){
		$strippedSystem = parent::stripForJson();
		$strippedSystem->data = $this->data;		
		$strippedSystem->guns = $this->guns;		
		return $strippedSystem;
	}

}//endof MinorThoughtPulsar	
	
	
?>
