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
		/*
        public $animationColor = array(227, 148, 55);
        public $animationExplosionScale = 0.25;
        public $projectilespeed = 12;
        public $animationWidth = 4;
        public $trailLength = 40;
		*/
        public $priority = 5;
        
        public $grouping = 20;
        
        public $canChangeShots = true;
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
            
        }
        
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
        public function setMinDamage(){     $this->minDamage = 2; /*- $this->dp;*/      }
        public function setMaxDamage(){     $this->maxDamage = 20; /*- $this->dp;*/      }
    
    } //endof class BallisticTorpedo



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
		//also, target is hidden by the tabletop - but this won't be implemented in FV
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
//        public $animationExplosionType = "AoE"; 
//        public $animationExplosionScale = 1;               
		/*
        public $trailColor = array(141, 240, 255);
        public $animationExplosionScale = 0.25;
        public $projectilespeed = 12;
        public $animationWidth = 10;
        public $trailLength = 10;
		*/
//		public $firingModes = array(	1 => "AoE");

        public $range = 60;
        public $loadingtime = 2;
        
        public $weaponClass = "Electromagnetic"; //deals Plasma, not Ballistic, damage. Should be Ballistic(Plasma), but I had to choose ;)
        public $damageType = "Flash"; 
        private $alreadyFlayed = false; //to avoid doing this multiple times      
                
        public $fireControl = array(-4, 3, 4); // fighters, <mediums, <capitals 
        public $priority = 1; //Flash! should strike first 
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 12;
            }
            if ( $powerReq == 0 ){
                $powerReq = 6;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

/*
	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ //really no matter what exactly was hit!	
		if ($system->advancedArmor) return;
		if ($ship instanceof FighterFlight) return;
        if($this->alreadyFlayed) return;
        $this->alreadyFlayed = true; //avoid doing that multiple times						

		$effectArmor = Dice::d(3,1);//strength of effect: 1d6
		$fireOrder->pubnotes .= "<br> Armor reduced by $effectArmor.";
		
			
        foreach ($target->systems as $system){
                if ($target->shipSizeClass<=1 || $system->location === $location){ //MCVs and smaller ships are one huge section technically
	             	for($i=1; $i<=$effectArmor;$i++){
	                    $crit = new ArmorReduced(-1, $target->id, $system->id, "ArmorReduced", $gamedata->turn);
	                    $crit->updated = true;
	                    $crit->inEffect = false;
	                    $system->criticals[] = $crit;
	                }
	            }
			} 
	} //end of function onDamagedSystem   */
	

   protected function doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata, $damageWasDealt, $location = null)
    {
        /*$pos ONLY relevant for FIGHTER armor if damage source position is different than one from weapon itself*/
        /*otherwise best leave null BUT fill $location!*/
        /*damageWasDealt indicates whether this hit already caused damage - important for overkill for some damage modes*/
        //if (!$system->isDestroyed()) { //else system was already destroyed, proceed to overkill
		if ($system->getRemainingHealth() > 0) { //Vree Structure systems are considered not there despite not being formally destroyed
            $damage = floor($damage);//make sure damage is a whole number, without fractions!
            $armour = $this->getSystemArmourComplete($target, $system, $gamedata, $fireOrder, $pos); //handles standard and Adaptive armor, as well as Advanced armor and weapon class modifiers
			// ...if armor-related modifications are needed, they should extend appropriate method (Complete or Base, as Adaptive should not be affected)
			// ...and doDamage should always call Complete


            //armor may be ignored for some reason... usually because of Raking mode :)
            $armourIgnored = 0;
            if (isset($fireOrder->armorIgnored[$system->id])) {
                $armourIgnored = $fireOrder->armorIgnored[$system->id];
                $armour = $armour - $armourIgnored;
            } 
            $armour = max($armour, 0); 

			//returned array: dmgDealt, dmgRemaining, armorPierced	
			$damage = $this->beforeDamagedSystem($target, $system, $damage, $armour, $gamedata, $fireOrder);
			$effects = $system->assignDamageReturnOverkill($target, $shooter, $this, $gamedata, $fireOrder, $damage, $armour, $pos, $damageWasDealt);
			$this->onDamagedSystem($target, $system, $effects["dmgDealt"], $effects["armorPierced"], $gamedata, $fireOrder);//weapons that do effects on hitting something
			$damage = $effects["dmgRemaining"];
			if ($this->damageType == 'Raking'){ //note armor already pierced so further rakes have it easier
				$armourIgnored = $armourIgnored + $effects["armorPierced"];
				$fireOrder->armorIgnored[$system->id] = $armourIgnored;
			}			
			
            $damageWasDealt = true; //actual damage was done! might be relevant for overkill allocation
        }

        if (($damage > 0) || (!$damageWasDealt)) {//overkilling!
            $overkillSystem = $this->getOverkillSystem($target, $shooter, $system, $fireOrder, $gamedata, $damageWasDealt, $location);
            if ($overkillSystem != null)
                $this->doDamage($target, $shooter, $overkillSystem, $damage, $fireOrder, $pos, $gamedata, $damageWasDealt, $location);
        }     
                  
            //$location is guaranteed to be filled in this case!     
            if($this->alreadyFlayed) return;
            $this->alreadyFlayed = true; //avoid doing that multiple times
            
	        $effectArmor = Dice::d(4,1);//strength of effect: 1d6
			$fireOrder->pubnotes .= "<br> Armor reduced by $effectArmor unless Advanced Armor.";
		
            foreach ($target->systems as $system){
                if ($system->advancedArmor) return;
    //            if ($this instanceof FighterFlight) return;	//Tried to ignore fighters, but didn't work.  I can live with it affecting them too.
                if ($target->shipSizeClass<=1 || $system->location === $location){ //MCVs and smaller ships are one huge section technically
	             	for($i=1; $i<=$effectArmor;$i++){
	                    $crit = new ArmorReduced(-1, $target->id, $system->id, "ArmorReduced", $gamedata->turn);
	                    $crit->updated = true;
	                    $crit->inEffect = false;
	                    $system->criticals[] = $crit;
	                }
	            }
			} 
        } //endof function doDamage	 

    	
		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			if (!isset($this->data["Special"])) {
				$this->data["Special"] = '';
			}else{
				$this->data["Special"] .= '<br>';
			}
			$this->data["Special"] .= "Reduces armor of facing section (structure and all systems).";
			$this->data["Special"] .= "<br>Ballistic weapon that can use offensive EW.";
		}
        
        
        public function getDamage($fireOrder){         return 20;   }
        public function setMinDamage(){     $this->minDamage = 20;      }
        public function setMaxDamage(){     $this->maxDamage = 20;      }
    
    }//endof class PsionicTorpedo
?>
