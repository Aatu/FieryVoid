<?php

    class Torpedo extends Weapon{
    
        public $ballistic = true;
        public $damageType = "Standard"; 
        public $weaponClass = "Ballistic"; 
		
		
        public $animation = "torpedo";
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
       
        /*moving to Weapon class - this is generally useful!
        public function isInDistanceRange($shooter, $target, $fireOrder)
        {
			if(!$this->ballistic) return true; //non-ballistic weapons don't risk target moving out of range
            $movement = $shooter->getLastTurnMovement($fireOrder->turn);
            $distanceRange = max($this->range, $this->distanceRange); //just in case distanceRange is not filled! Then it's assumed to be the same as launch range
            if($distanceRange <=0 ) return true; //0 means unlimited range

            if(mathlib::getDistanceHex($movement->position,  $target) > $distanceRange )
            {
                $fireOrder->pubnotes .= " FIRING SHOT: Target moved out of distance range.";
                return false;
            }

            return true;
        }
		*/
		
       
        
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
        }

    } //endof class Torpedo


    
    class IonTorpedo extends Torpedo{
    
        public $name = "ionTorpedo";
        public $displayName = "Ion Torpedo";
        public $range = 50;
        public $loadingtime = 2;
        
        public $fireControl = array(-4, 1, 3); // fighters, <mediums, <capitals 
        
        public $animation = "torpedo";
        public $animationColor = array(30, 170, 255);
		/*
        public $trailColor = array(141, 240, 255);
        public $animationExplosionScale = 0.25;
        public $projectilespeed = 12;
        public $animationWidth = 10;
        public $trailLength = 10;
		*/
		
		
        public $priority = 6;
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return 15;   }
        public function setMinDamage(){     $this->minDamage = 15; /*- $this->dp;*/      }
        public function setMaxDamage(){     $this->maxDamage = 15 ;/*- $this->dp;*/      }
    
    }//endof class IonTorpedo


    
    class BallisticTorpedo extends Torpedo{
    
        public $name = "ballisticTorpedo";
        public $displayName = "Ballistic Torpedo";
        public $range = 25;
        public $loadingtime = 1;
        public $shots = 6;
        public $defaultShots = 1;
        public $normalload = 6;
        
        public $fireControl = array(0, 3, 4); // fighters, <mediums, <capitals 
        
        public $trailColor = array(141, 240, 255);
        public $animation = "trail";

        public $priority = 5;
        
        public $grouping = 20;
		
		public $canSplitShots = true;  
    	public $calledShotMod = 0; //Cannot make called shots against ships anyway, but shouldn't get modifier when we specificy specific fighters		      
//        public $canChangeShots = true; //No longer needed.

        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
            
        }

      
        public function firedOnTurn($turn){  
        	$totalShots = 0; //Counter for shots this turns          
            foreach ($this->fireOrders as $fire){
                if ($fire->weaponid == $this->id && $fire->turn == $turn){
                    $totalShots += $fire->shots;
                }
            }
            return $totalShots;
        }
        
        public function calculateLoading(TacGamedata $gamedata)
        {
            $loading = new WeaponLoading($this->turnsloaded, 0, 0, 0, $this->getLoadingTime(), $this->firingMode);
            $shotsfired = $this->firedOnTurn(TacGamedata::$currentTurn);
            if (TacGamedata::$currentPhase == 2)
            {
                if  
                ( $this->isOfflineOnTurn(TacGamedata::$currentTurn) )
                {
                    $loading = new WeaponLoading(0, 0, 0, 0, $this->getLoadingTime(), $this->firingMode);
                }
                else if ($shotsfired)
                {
                    $newloading = $this->turnsloaded-$shotsfired;
                    if ($newloading < 0)
                        $newloading = 0;

                $loading = new WeaponLoading($newloading, 0, 0, 0, $this->getLoadingTime(), $this->firingMode);
                }
            }
            else if (TacGamedata::$currentPhase == 1)
            {
                $newloading = $this->turnsloaded+1;
                if ($newloading > $this->getNormalLoad())
                    $newloading = $this->getNormalLoad();

                $loading = new WeaponLoading($newloading, 0, 0, 0, $this->getLoadingTime(), $this->firingMode);
            }

            return $loading;
        }//endof calculateLoading()


		public function beforeFiringOrderResolution($gamedata) {
		    $turn = $gamedata->turn; // Assume $turn is accessible from $gamedata
		    $shipOrders = [];
		    $fighterOrders = [];

		    // Group fire orders by targetid and type of target
		    foreach ($this->fireOrders as $fire) {
		        if ($fire->weaponid == $this->id && $fire->turn == $turn) {
		            $targetId = $fire->targetid;
		            $targetShip = $gamedata->getShipById($targetId);
		            
		            if ($targetShip instanceof FighterFlight) {                    
		                if (!isset($fighterOrders[$targetId])) {
		                    $fighterOrders[$targetId] = [$fire]; // Initialize as an array for multiple orders
		                } else {
		                    $fighterOrders[$targetId][] = $fire; // Add to the list of orders for this target
		                }
		            } else {            
		                if (!isset($shipOrders[$targetId])) {
		                    $shipOrders[$targetId] = $fire;
		                } else {
		                    $shipOrders[$targetId]->shots += $fire->shots;
		                }
		            }
		        }
		    }

		    // Process fighterOrders to ensure unique calledid for each fire order
		    foreach ($fighterOrders as $targetId => $fires) {
		        $fighterFlight = $gamedata->getShipById($targetId);

		        // Get a list of undestroyed fighters in the flight
		        $fighterList = [];
		        foreach ($fighterFlight->systems as $fighter) {
		            if (!$fighter->isDestroyed()) {
		                $fighterList[] = $fighter; // Add to the list of valid fighters
		            }
		            array_unshift($fighterList, $fighter); // array_unshift adds element at the beginning of array rather than at the end		    
		        }

		        // Assign unique calledid to fire orders
		        $fighterIndex = 0; // Keep track of the current fighter being targeted
		        foreach ($fires as $fire) {
		            if (!isset($fighterList[$fighterIndex])) {
		                break; // If there are no more fighters to assign, stop
		            }
		            $fire->calledid = $fighterList[$fighterIndex]->id; // Assign a unique fighter
		            $fighterIndex++; // Move to the next fighter
		        }
		    }

		    // Combine shipOrders and fighterOrders into this->fireOrders
		    $combinedOrders = [];

		    // Merge shipOrders
		    if (!empty($shipOrders)) {
		        $combinedOrders = array_merge($combinedOrders, array_values($shipOrders));
		    }

		    // Merge fighterOrders
		    if (!empty($fighterOrders)) {
		        foreach ($fighterOrders as $orders) {
		            $combinedOrders = array_merge($combinedOrders, $orders); // Flatten nested arrays
		        }
		    }

		    $this->fireOrders = $combinedOrders;
		    
		}//endof beforeFiringOrderResolution()
		

		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			if (!isset($this->data["Special"])) {
				$this->data["Special"] = '';
			}else{
				$this->data["Special"] .= '<br>';
			}

			$this->data["Special"] .= "Can fire at multiple targets, providing it has enough shots available.";
			$this->data["Special"] .= "<br>To do so, select weapon and target as many enemies as you wish following the normal rules for targeting, then deselect weapon.";
			$this->data["Special"] .= "<br>Multiple torpedoes from the same launcher against the same ship will be combined into a single Pulse attack with a 20% grouping bonus (Saturation Mode).";	
			$this->data["Special"] .= "<br>Against enemy fighters flights, each torpedo will always attack a different fighter, providing there is an appropirate target available.";
			$this->data["Special"] .= "<br>If there are more torpedoes than fighters, excess munitions are lost.";		
		}
	
	        
        public function onConstructed($ship, $turn, $phase){
            parent::onConstructed($ship, $turn, $phase);
            $this->shots = $this->turnsloaded;
        }        
        
        public function getDamage($fireOrder){        return Dice::d(10,2);   }
        public function setMinDamage(){     $this->minDamage = 2;   }
        public function setMaxDamage(){     $this->maxDamage = 20;     }
    
		public function stripForJson()
		{
			$strippedSystem = parent::stripForJson();
			$strippedSystem->maxVariableShots = $this->turnsloaded;
			return $strippedSystem;
		}     
    
    
    } //endof class BallisticTorpedo


/*
   //Old Version of Ballistic Torpedo without split shots
    class BallisticTorpedo extends Torpedo{
    
        public $name = "ballisticTorpedo";
        public $displayName = "Ballistic Torpedo";
        public $range = 25;
        public $loadingtime = 1;
        public $shots = 6;
        public $defaultShots = 1;
        public $normalload = 6;
        
        public $fireControl = array(0, 3, 4); // fighters, <mediums, <capitals 
        
        public $trailColor = array(141, 240, 255);
        public $animation = "trail";
		/*
        public $animationColor = array(227, 148, 55);
        public $animationExplosionScale = 0.25;
        public $projectilespeed = 12;
        public $animationWidth = 4;
        public $trailLength = 40;
		*//*
        public $priority = 5;
        
        public $grouping = 20;
        
        public $canChangeShots = true;

        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
            
        }

		//Need to overwrite to with a counter for each fireOrder and return counter instead.        
        public function firedOnTurn($turn){            
            foreach ($this->fireOrders as $fire){
                if ($fire->weaponid == $this->id && $fire->turn == $turn){
                    return  $fire->shots;
                }
            }
            return 0;
        }
        
        public function calculateLoading(TacGamedata $gamedata)
        {
            $loading = new WeaponLoading($this->turnsloaded, 0, 0, 0, $this->getLoadingTime(), $this->firingMode);
            $shotsfired = $this->firedOnTurn(TacGamedata::$currentTurn);
            if (TacGamedata::$currentPhase == 2)
            {
                if  
                ( $this->isOfflineOnTurn(TacGamedata::$currentTurn) )
                {
                    $loading = new WeaponLoading(0, 0, 0, 0, $this->getLoadingTime(), $this->firingMode);
                }
                else if ($shotsfired)
                {
                    $newloading = $this->turnsloaded-$shotsfired;
                    if ($newloading < 0)
                        $newloading = 0;

                $loading = new WeaponLoading($newloading, 0, 0, 0, $this->getLoadingTime(), $this->firingMode);
                }
            }
            else if (TacGamedata::$currentPhase == 1)
            {
                $newloading = $this->turnsloaded+1;
                if ($newloading > $this->getNormalLoad())
                    $newloading = $this->getNormalLoad();

                $loading = new WeaponLoading($newloading, 0, 0, 0, $this->getLoadingTime(), $this->firingMode);
            }

            return $loading;
        }
        
        public function onConstructed($ship, $turn, $phase){
            parent::onConstructed($ship, $turn, $phase);
            $this->shots = $this->turnsloaded;
        }        
        
        public function getDamage($fireOrder){        return Dice::d(10,2);   }
        public function setMinDamage(){     $this->minDamage = 2;       }
        public function setMaxDamage(){     $this->maxDamage = 20;      }
    
		public function stripForJson()
		{
			$strippedSystem = parent::stripForJson();
			$strippedSystem->maxVariableShots = $this->turnsloaded;
			return $strippedSystem;
		}     
    
    
    } //endof class BallisticTorpedo
    
*/    


    class PlasmaWaveTorpedo extends Torpedo{
        public $name = "PlasmaWaveTorpedo";
        public $displayName = "Plasma Wave";
        public $iconPath = "plasmaWaveTorpedo.png";
        public $range = 30;
        public $loadingtime = 3;
        
        public $weaponClass = "Plasma"; //deals Plasma, not Ballistic, damage. Should be Ballistic(Plasma), but I had to choose ;)
        public $damageType = "Flash"; 
        
        
        public $fireControl = array(null, 0, 2); // fighters, <mediums, <capitals 
        
        public $animation = "torpedo";
        public $animationColor = array(75, 230, 90);
		/*
        public $trailColor = array(75, 230, 90);
        public $animationExplosionScale = 0.3;
        public $projectilespeed = 11;
        public $animationWidth = 10;
        public $trailLength = 10;
		*/
        public $priority = 1; //Flash! should strike first 
         
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 7;
            }
            if ( $powerReq == 0 ){
                $powerReq = 4;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
	    //ignores half armor (as a Plasma weapon should!) - now handled by standard routines
    	
		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			if (!isset($this->data["Special"])) {
				$this->data["Special"] = '';
			}else{
				$this->data["Special"] .= '<br>';
			}
			$this->data["Special"] .= "Ignores half of armor.";
			$this->data["Special"] .= "<br>Ballistic weapon that can use offensive EW.";
		}
        
        
        public function getDamage($fireOrder){        return Dice::d(10, 3);   }
        public function setMinDamage(){     $this->minDamage = 3;      }
        public function setMaxDamage(){     $this->maxDamage = 30;      } 
    
    }//endof class PlasmaWaveTorpedo



    class PacketTorpedo extends Torpedo{
        public $name = "PacketTorpedo";
        public $displayName = "Packet Torpedo";
        public $iconPath = "packetTorpedo.png";
        public $range = 50;  
        public $loadingtime = 2;
		public $specialRangeCalculation = true; //to inform front end that it should use weapon-specific range penalty calculation - such a method should be present in .js!
        
        public $weaponClass = "Ballistic"; 
        public $damageType = "Standard"; 
        
		public $hidetarget = true;
        
        public $fireControl = array(-6, 3, 3); // fighters, <mediums, <capitals 
		public $rangePenalty = 0.5; //-1/2 hexes - BUT ONLY AFTER 10 HEXES
        
        public $animation = "torpedo";
        public $animationColor = array(130, 170, 255);
		/*
        public $trailColor = array(191, 200, 215);
        public $animationExplosionScale = 0.25;
        public $projectilespeed = 14;
        public $animationWidth = 11;
        public $trailLength = 16;
		*/
        public $priority = 6; //heavy Standard
        
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
            	
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
		$this->data["Special"] .= "This weapon suffers range penalty (like direct fire weapons do), but only after first 10 hexes of distance.";
		$this->data["Special"] .= "<br>Also, targeting information is hidden for opponent - weapon will be marked as fired, but target will not be highlighted, and weapon will not be shown as incoming.";
	}
        
	    //override standard to skip first 10 hexes when calculating range penalty
		/*
	    public function calculateRangePenalty(OffsetCoordinate $pos, BaseShip $target)
	    {
			$targetPos = $target->getHexPos();
			$dis = mathlib::getDistanceHex($pos, $targetPos);
			$dis = max(0,$dis-10);//first 10 hexes are "free"

			$rangePenalty = ($this->rangePenalty * $dis);
			$notes = "shooter: " . $pos->q . "," . $pos->r . " target: " . $targetPos->q . "," . $targetPos->r . " dis: $dis, rangePenalty: $rangePenalty";
			return Array("rp" => $rangePenalty, "notes" => $notes);
	    }*/
		public function calculateRangePenalty($distance){
			$rangePenalty = 0;//base penalty
			$rangePenalty += $this->rangePenalty * max(0,$distance-10); //everything above 10 hexes receives range penalty
			return $rangePenalty;
		}
        
        public function getDamage($fireOrder){        return Dice::d(10, 2)+10;    }
        public function setMinDamage(){     $this->minDamage = 12;      }
        public function setMaxDamage(){     $this->maxDamage = 30;      }
    
    }//endof class PacketTorpedo



class PsionicTorpedo extends Torpedo{ //Powerful Thirdspace weapon that detonates and reduces armour of targets.  
        public $name = "PsionicTorpedo";
        public $displayName = "Psionic Torpedo";
        public $iconPath = "PsionicTorpedo.png";                
  	  	public $animation = "torpedo";
        public $animationColor = array(128, 0, 0);        

        public $range = 50;
        public $loadingtime = 2;
        
        public $weaponClass = "Psychic";
        public $damageType = "Standard";     
                
        public $fireControl = array(null, 4, 5); // fighters, <mediums, <capitals 
        public $priority = 1; //Flash! should strike first 
        
		private static $alreadyAffected = array();         
        
        public $boostable = true;
        public $boostEfficiency = 0;
        public $maxBoostLevel = 2; 
        
    protected $ewBoosted = true;         
        
		public $repairPriority = 5;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired                 
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 9;
            }
            if ( $powerReq == 0 ){
                $powerReq = 3;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }	
		
		//Ignores armor, nasty for flash damage on fighters.
		public function getSystemArmourBase($target, $system, $gamedata, $fireOrder, $pos=null){
		if (($system->advancedArmor) && (!$system->hardAdvancedArmor)){ //Negates Advanced armor's +2 bonus against ballistics and reduces by a further 2. No impact on hardened advanced armor
            $armour = parent::getSystemArmourBase($target, $system, $gamedata, $fireOrder, $pos);
            $armour = $armour-4;
            return $armour;
		}elseif ($system->hardAdvancedArmor){
			return (($armor/2)+2);
		}else{					
			return 0;
        }
	}    	

	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ //really no matter what exactly was hit!
		parent::onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder);			
		//Weapon has hit, so apply+1 to crit roll, +1 to dropout roll to the damge elements
		$mod = 2;
//		if ($ship instanceof FighterFlight) $mod++;
		$system->critRollMod += $mod; 
		
		if (isset(PsionicTorpedo::$alreadyAffected[$ship->id])) return; //But not if affected already.											
		$boostlevel = $this->getBoostLevel($fireOrder->turn);			
		$effectEW = Dice::d(2,1)+$boostlevel;//strength of effect: -1 to -4 EW.				
		$effectIni = Dice::d(3,1)+$boostlevel;//strength of effect: -5 to -25 initiative.		
		$effectPower = Dice::d(4,1)+$boostlevel;//strength of effect: -1 to -6 to power.
//		$effectThrust = Dice::d(3,1); //Potentially could add additional crit effect.		
			
		$fireOrder->pubnotes .= "<br> Target has Initiative reduced, and suffers from power and scanner fluctations.";				
			
		if ($system->advancedArmor){		
			$effectEW = ceil($effectEW/2);//Other Ancients are somewhat resistant to pyschic attack from Thirdspace Aliens, 50% effect.		
			$effectIni = ceil($effectIni/2);  	
			$effectPower = 0; //Reducing power in Vorlons can make it impossible for them to commit Initial Orders.		
		}

		if ($ship instanceof FighterFlight){  //No additional effect on fighters beyond flash damage.			
			PsionicTorpedo::$alreadyAffected[$ship->id] = true;//mark affected already.	
			return;
		}else{				
			$CnC = $ship->getSystemByName("CnC");
			$reactor = $ship->getSystemByName("Reactor");
			$scanner = $ship->getSystemByName("Scanner");			
			PsionicTorpedo::$alreadyAffected[$ship->id] = true;//mark affected already.					
			for($i=1; $i<=$effectIni;$i++){				
				$crit = new tmpinidown(-1, $ship->id, $CnC->id, 'tmpinidown', $gamedata->turn); 
				$crit->updated = true;
		        $CnC->criticals[] =  $crit;
			}							
            if ($effectPower > 0) {
                for($i=1; $i<=$effectPower;$i++){
                    if ($reactor){
                        $crit = new OutputReduced1(-1, $ship->id, $reactor->id, "OutputReduced1", $gamedata->turn, $gamedata->turn+1);
                        $crit->updated = true;
                        $reactor->criticals[] =  $crit;
                    }     
                }
            }
			for($i=1; $i<=$effectEW;$i++){
				if ($scanner){//Some ships might not have a traditional scanner.						
					$crit = new OutputReduced1(-1, $ship->id, $scanner->id, "OutputReduced1", $gamedata->turn, $gamedata->turn+1);
					$crit->updated = true;
					$scanner->criticals[] =  $crit;
				}     
			}								
		}
			
	} //endof function onDamagedSystem	
    	
		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			if (!isset($this->data["Special"])) {
				$this->data["Special"] = '';
			}else{
				$this->data["Special"] .= '<br>';
			}
			$this->data["Special"] .= "<br>Ignores armor (Advanced Armor is treated as 2 points less, Hardened Avanced Armor is treated as half +2 for ballistics).";	
			$this->data["Special"] .= "<br>Uses psychic energy to disrupt target next turn in the following ways:";
			$this->data["Special"] .= "<br> - 1d2 penalty to EW,";
			$this->data["Special"] .= "<br> - 1d3 penatly to Initiative,";
			$this->data["Special"] .= "<br> - 1d4 penalty to available power.";													
			$this->data["Special"] .= "<br>Ballistic weapon that can use offensive EW.";
			$this->data["Special"] .= "<br>Multiple Psionic Torpedoes do not stack effects (but do stack with Psychic Field).";			
			$this->data["Special"] .= "<br>Can be boosted twice using EW, for +4 damage per boost, and +1 to each disruptive effect above.";			
		    $this->data["Special"] .= "<br>Has +2 modifier to critical hits.";			
		}

        private function getExtraDamagebyBoostlevel($turn){
            $add = 0;
            switch($this->getBoostLevel($turn)){
                case 1:
                    $add = 4;
                    break;
                case 2:
                    $add = 8;
                    break;
                                        
                      
                default:
                    break;
            }
            return $add;
        }

         private function getBoostLevel($turn){
            $boostLevel = 0;
            foreach ($this->power as $i){
                if ($i->turn != $turn){
                   continue;
                }
                if ($i->type == 2){
                    $boostLevel += $i->amount;
                }
            }
            return $boostLevel;
        }
        
        public function getDamage($fireOrder){
            $add = $this->getExtraDamagebyBoostlevel($fireOrder->turn);
            $dmg = Dice::d(10,1) + 12 + $add ;
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
            $this->minDamage = 13 + ($boost * 4);
        }   

        public function setMaxDamage(){
            $turn = TacGamedata::$currentTurn;
            $boost = $this->getBoostLevel($turn);
            $this->maxDamage = 22 + ($boost * 4); 
		}

	public function stripForJson(){
		$strippedSystem = parent::stripForJson();
		$strippedSystem->ewBoosted = $this->ewBoosted;													
		return $strippedSystem;
	} 
		    
    }//endof class PsionicTorpedo


class LimpetBoreTorpedo extends Torpedo{
        public $name = "LimpetBoreTorpedo";
        public $displayName = "Limpet Bore Torpedo";
		    public $iconPath = "LimpetBoreTorpedo.png";
        public $animation = "trail";
        public $trailColor = array(141, 240, 255);
        public $animationColor = array(50, 50, 50);
        public $animationExplosionScale = 0.2;
        public $projectilespeed = 10;
        public $animationWidth = 4;
        public $trailLength = 100;    

        public $useOEW = true; 
        public $ballistic = true;
        public $range = 30;
        public $distanceRange = 30;
        public $ammunition = 5; //limited number of shots

		public $noPrimaryHits = true; //cannot hit PRIMARY from outer table

        public $calledShotMod = 0; //instead of usual -8
        
        public $loadingtime = 2; // 1/2 turns
        public $rangePenalty = 0;
        public $fireControl = array(null, 2, 4);
	    
		public $noOverkill = true; //Matter weapon
		public $priority = 9; //Matter weapon
		    

		public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	    public $weaponClass = "Matter"; //should be Ballistic and Matter, but FV does not allow that. Instead decrease advanced armor encountered by 2 points (if any) (usually system does that, but it will account for Ballistic and not Matter)

		protected $overrideCallingRestrictions = true;//Front End: To allow this as a ballistic weapon to make called shots.
		protected $canTargetAllExtSections = true; //Front End: Allow this weapon to target any system on external sections of target ship.  Keep separate from above in case useful at a later point.
		protected $canOnlyCalledShot = true;//Front End: To block it from targeting ships, only systems.		

			 
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		        //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 5;
            if ( $powerReq == 0 ) $powerReq = 3;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }     
	    
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            $this->data["Special"] = "Ballistic weapon used ONLY for Called Shots.";
            $this->data["Special"] .= "<br>Can target any system on target, even those not facing the firing vessel.";            
            $this->data["Special"] .= "<br>Once it hits a ship, it will attach to target system and try to damage it the following turn by adding a critical effect.";
            $this->data["Special"] .= "<br>If the target system is not on the section that the torpedo hits, it will take an additional turn(s) to travel to it.";            
            $this->data["Special"] .= "<br>Once attached, this critical effect will remain until target system is destroyed, or after five failed attempts.";            
            $this->data["Special"] .= "<br>Has no effect on OSATs or targets equipped with Advanced Armor.";
            $this->data["Special"] .= "<br>No Called Shot penalty.";
            $this->data["Special"] .= "<br>Ignores Armor. No Overkill.";                     
            $this->data["Ammunition"] = $this->ammunition;
        }
        

        public function setAmmo($firingMode, $amount){
            $this->ammunition = $amount;
        }
        
        
       public function fire($gamedata, $fireOrder){ //note ammo usage
            parent::fire($gamedata, $fireOrder);
            $this->ammunition--;
            Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, $gamedata->id, $this->firingMode, $this->ammunition, $gamedata->turn);
        }


	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ //really no matter what exactly was hit!
		
		if ($system->advancedArmor) {//no effect on Advanced Armor	
			$fireOrder->pubnotes .= "<br> Limpet Bore cannot attach to advanced armor.";				
			return; 	
		}

		if ($system->id != $fireOrder->calledid) {//In case it ends up in general hit chart somehow.
			$fireOrder->pubnotes .= "<br> Limpet Bore was not targeted at a system.";				
			return; 	
		}
		
		if ($ship instanceof OSAT) {//In case it ends up in general hit chart somehow.
			$fireOrder->pubnotes .= "<br> Limpet Bore cannot attach to OSAT units.";				
			return; 	
		}			
			
		if($system){
		
			$locationPairsTwoTurn = [//Create list of non-adjacent sections, that Limpet Bore will take TWO turns to reach.
			    [1, 2],
			    [2, 1],
			    [3, 4],
			    [4, 3],
			    [1, 42],
			    [42, 1],
			    [1, 32],
			    [32, 1],			    
			    [2, 31],
			    [31, 2],
			    [2, 41],
			    [41, 2]				    			    			    			    
			];						
			$locationPairsThreeTurn = [//Create list of OPPOSITE sections in six sided units, that Limpet Bore will take THREE turns to reach.
			    [32, 41],
			    [41, 32],
			    [31, 42],
			    [42, 31]			    			    			    			    
			];		

			$turnActivates = $gamedata->turn+1;//Usually Limpet will attack the turn AFTER it hits.
						    			
				if($system->location == $fireOrder->chosenLocation || $ship instanceof LCV){//Hits same side as target system or an LCV.  Attaches (current turn +1)
					
					$fireOrder->pubnotes .= "<br> Limpet Bore attaches to ship, it will attack target system next turn.";				
					$crit = new LimpetBore(-1, $ship->id, $system->id, 'LimpetBore', $gamedata->turn+1); //The crit takes effect the FOLLOWING turn.
					$crit->updated = true;
					$system->criticals[] =  $crit;
								
				}elseif(in_array([$system->location, $fireOrder->chosenLocation], $locationPairsTwoTurn)){//Non-Adjacent locations, takes longer to travel there.
					$turnActivates += 2;//Add two turn of travel time before attaching (current turn +3).
					
					if($ship instanceof MediumShip) $turnActivates = $gamedata->turn+2;//MCVs and HCVs can only have a maximum of 1 turn travel time.
					if($ship instanceof VreeCapital) $turnActivates -= 1; //Easier to traverse Vree ships due to wider structure arcs.						
					if((($system->location == 1 && $fireOrder->chosenLocation == 2) || ($system->location == 2 && $fireOrder->chosenLocation == 1)) && $ship instanceof StarBaseSixSections) $turnActivates += 1; //Starbases 1 and 2 locs are actually OPPOSITE!
	
																					
						//Add crit to show Limpet Bore is on its way.
						$fireOrder->pubnotes .= "<br>Limpet Bore attaches to ship and will traverse the hull to attack target system.";				
						$crit = new LimpetBoreTravelling(-1, $ship->id, $system->id, 'LimpetBoreTravelling', $gamedata->turn+1, $turnActivates-1); //The crit takes effect the turn after next.
						$crit->updated = true;
						$system->criticals[] =  $crit;
					
						//Now add crit to actually show Limpet attached and will attack.			
						$crit2 = new LimpetBore(-1, $ship->id, $system->id, 'LimpetBore', $turnActivates); //The crit takes effect the FOLLOWING turn.
						$crit2->updated = true;
						$system->criticals[] =  $crit2;	
																	
				}elseif(in_array([$system->location, $fireOrder->chosenLocation], $locationPairsThreeTurn)){//Opposite locations, takes longer to travel there.
					$turnActivates += 3;//Add three turns of travel time before attaching (current turn +4).
					
					if($ship instanceof MediumShip) $turnActivates = $gamedata->turn+2;//MCVs can only have a maximum of 1 turn travel time.
					if($ship instanceof VreeCapital) $turnActivates -= 1; //Easier to traverse Vree ships due to wider structure arcs.
																	
						//Add crit to show Limpet Bore is on its way.
						$fireOrder->pubnotes .= "<br>Limpet Bore attaches to ship and will traverse the hull to attack target system.";				
						$crit = new LimpetBoreTravelling(-1, $ship->id, $system->id, 'LimpetBoreTravelling', $gamedata->turn+1, $turnActivates-1); //The crit takes effect the turn after next.
						$crit->updated = true;
						$system->criticals[] =  $crit;
						
						//Now add crit to actually show Limpet attached and will attack.			
						$crit2 = new LimpetBore(-1, $ship->id, $system->id, 'LimpetBore', $turnActivates); //The crit takes effect the FOLLOWING turn.
						$crit2->updated = true;
						$system->criticals[] =  $crit2;	
																	
				}else{//Adjacent sections, Primary sections or anything else.
					$turnActivates += 1;//Add extra turn of travel time, attaches current turn +2.
					
						//Add crit to show Limpet Bore is on its way.
						$fireOrder->pubnotes .= "<br>Limpet Bore attaches to ship and will traverse the hull to attack target system.";					
						$crit = new LimpetBoreTravelling(-1, $ship->id, $system->id, 'LimpetBoreTravelling', $gamedata->turn+1, $gamedata->turn+1); //The crit takes effect the turn after next.
						$crit->updated = true;
						$system->criticals[] =  $crit;
						
						//Now add crit to actually show Limpet attached and will attack.			
						$crit2 = new LimpetBore(-1, $ship->id, $system->id, 'LimpetBore', $turnActivates); //The crit takes effect the FOLLOWING turn.
						$crit2->updated = true;
						$system->criticals[] =  $crit2;										
				}		
				
			}
			
	}//endof onDamagedSystem() 	
		
		
    public function getDamage($fireOrder){ //Damage is handled in criticalPhaseEffects() once Limpet Bore attaches.
            return 0;
    }
    
    
    public function setMinDamage(){     $this->minDamage = 12;      } //However, keep these values for intercept calculations.
    public function setMaxDamage(){     $this->maxDamage = 30;      }

    public function stripForJson() {
            $strippedSystem = parent::stripForJson();    
            $strippedSystem->ammunition = $this->ammunition;
            $strippedSystem->overrideCallingRestrictions = $this->overrideCallingRestrictions;
            $strippedSystem->canOnlyCalledShot = $this->canOnlyCalledShot;   
            $strippedSystem->canTargetAllExtSections = $this->canTargetAllExtSections;                            
            return $strippedSystem;
    }
		
}//endof LimpetBoreTorp



class LimpetBoreTorpedoBase extends LimpetBoreTorpedo{
        public $name = "LimpetBoreTorpedoBase";
        public $displayName = "Limpet Bore Torpedo";
 
        public $range = 60;
        public $distanceRange = 60;
        public $ammunition = 10; //limited number of shots.  Should be 15 with two reload phases every 5 shots, so we'll settle for just 10
        
        public $loadingtime = 1; 
	    		
}//endof LimpetBoreBase


	//Custom Updagrade Version of Packet Torpedo for Abraxas' campaign.
   	class FlexPacketTorpedo extends Torpedo{
        public $name = "FlexPacketTorpedo";
        public $displayName = "Flexible Packet Torpedo";
        public $iconPath = "flexPacketTorpedo.png";
        public $range = 50;  
		public $specialRangeCalculation = true; //to inform front end that it should use weapon-specific range penalty calculation
        
        public $weaponClass = "Ballistic"; 
        public $damageType = "Standard"; 

		public $loadingtime = 1;
		public $normalload = 2;        
		public $hidetarget = true;
		
		public $firingMode = 1;	
		public $firingModes = array(
			1 => "Torpedo"
		);		
        
        public $fireControl = array(-6, 3, 3); // fighters, <mediums, <capitals 
		public $rangePenalty = 0.5; //-1/2 hexes - BUT ONLY AFTER 10 HEXES
        
        public $animation = "torpedo";
        public $animationColor = array(130, 170, 255);

		private $firedInRapidMode = false; //was this weapon fired in rapid mode (this turn)?
        public $priority = 6; //heavy Standard
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 9;
            }
            if ( $powerReq == 0 ){
                $powerReq = 9;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
            	
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
		$this->data["Special"] .= "Weapon suffers range penalty (like direct fire weapons do), but only after first 20 hexes of distance.";		
		$this->data["Special"] .= "<br>Can fire after only recharging for one turn, but range penalty is applied after first 5 hexes of distance.";					
		$this->data["Special"] .= "<br>Targeting information is hidden for opponent - weapon will be marked as fired, but target will not be highlighted, and weapon will not be shown as incoming.";
	}
        
		
	private function nullFireControl() {//Extra function needed to null Fire Control values across ALL ammo types in recalculateFireControl.
			$this->fireControl = array(null,null,null);//I need this method if launched has NO ammo modes.
			$this->fireControlArray = array();		
	}		

	
	// This method generates additional non-standard information in the form of individual system notes
	 public function generateIndividualNotes($gameData, $dbManager){ //dbManager is necessary for Initial phase only
			$ship = $this->getUnit();
			switch($gameData->phase){								
				case 1: //Initial phase 
					//if weapon is marked as firing in Rapid mode, make a note of it!
					if($ship->userid == $gameData->forPlayer){ //only own ships, otherwise bad things may happen!
						if($this->firedInRapidMode){
							$notekey = 'RapidFire';
							$noteHuman = 'fired in Rapid mode';
							$noteValue = 'R';
							$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$noteValue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue
						}		
					}							
                break;
			}					

	} //endof function generateIndividualNotes
	
	//act on notes just loaded - to be redefined by systems as necessary
	public function onIndividualNotesLoaded($gamedata){
		foreach ($this->individualNotes as $currNote) 
			if($currNote->turn == $gamedata->turn) if ($currNote->notevalue == 'R'){ //only current round matters!
			$this->firedInRapidMode = true;			
		}	
		//and immediately delete notes themselves, they're no longer needed (this will not touch the database, just memory!)		
		$this->individualNotes = array();
	} //endof function onIndividualNotesLoaded
	
	
	public function doIndividualNotesTransfer(){
		//data received in variable individualNotesTransfer, further functions will look for variable firedInRapidMode
		if(is_array($this->individualNotesTransfer)) foreach($this->individualNotesTransfer as $entry) {
			if ($entry == 'R') $this->firedInRapidMode = true;
			if ($entry == 'L') $this->firedInLongRangeMode = true;				
		}
		
		$this->individualNotesTransfer = array(); //empty, just in case
	}			
		
		public function calculateRangePenalty($distance){

			if($this->firedInRapidMode){
				$rangePenalty = 0;//base penalty
				$rangePenalty += $this->rangePenalty * max(0,$distance - 5); //Normal range penalty in rapid mode
				return $rangePenalty;				
			}else{
				$rangePenalty = 0;//base penalty
				$rangePenalty += $this->rangePenalty * max(0,$distance-20); //everything above 10 hexes receives range penalty
				return $rangePenalty;
			}
		}
			
	public function stripForJson(){
		$strippedSystem = parent::stripForJson();	
		$strippedSystem->firedInRapidMode = $this->firedInRapidMode;			
		return $strippedSystem;
	}
        
        public function getDamage($fireOrder){        return Dice::d(10, 2)+10;    }
        public function setMinDamage(){     $this->minDamage = 12;      }
        public function setMaxDamage(){     $this->maxDamage = 30;      }
    
    }//endof class FlexPacketTorpedo
    
    
    
?>
