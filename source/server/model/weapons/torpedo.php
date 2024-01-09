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
		$this->data["Special"] .= "<br>Also, targeting information is hidden for opponent - weapon will be marged as fired, but target will not be highlighted, and weapon will not be shown as incoming.";
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
//   		public $animationExplosionScale = 0.6;         

        public $range = 550;
        public $loadingtime = 2;
        
        public $weaponClass = "Electromagnetic"; //deals Plasma, not Ballistic, damage. Should be Ballistic(Plasma), but I had to choose ;)
        public $damageType = "Flash"; 
//        private $alreadyFlayed = false; //to avoid doing this multiple times      
                
        public $fireControl = array(null, 4, 5); // fighters, <mediums, <capitals 
        public $priority = 1; //Flash! should strike first 
        
		private static $alreadyAffected = array();         
        
        public $boostable = true;
        public $boostEfficiency = 1;
        public $maxBoostLevel = 2;  
        
		public $repairPriority = 5;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired                 
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 9;
            }
            if ( $powerReq == 0 ){
                $powerReq = 6;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }	
		
		//Ignores armor, nasty for flash damage on fighters.
		public function getSystemArmourBase($target, $system, $gamedata, $fireOrder, $pos=null){
		if ($system->advancedArmor){ //Negates Advanced armor's +2 bonus against ballistics and reduces by a further 2.
            $armour = parent::getSystemArmourBase($target, $system, $gamedata, $fireOrder, $pos);
            $armour = $armour-4;
            return $armour;
		}else{					
			return 0;
        }
	}    	

	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ //really no matter what exactly was hit!
		parent::onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder);			
		//Weapon has hit, so apply+1 to crit roll, +2 to dropout roll to the damge elements
		$mod = 1;
//		if ($ship instanceof FighterFlight) $mod++;
		$system->critRollMod += $mod; 
		
		if (isset(PsionicTorpedo::$alreadyAffected[$ship->id])) return; //But not if affected already.											
			
		$effectEW = Dice::d(3,1);//strength of effect: -1 to -3 EW.				
		$effectIni = Dice::d(3,1);//strength of effect: -5 to -15 initiative.		
		$effectPower = Dice::d(4,1)+1;//strength of effect: -2 to -4 to power.
			
		$fireOrder->pubnotes .= "<br> Target has Initiative reduced, and suffers power and scanner fluctations.";				
			
		if ($system->advancedArmor){		
			$effectEW = ceil($effectEW/2);//Other Ancients are somewhat resistant to pyschic attack from Thirdspace Aliens, 50% effect.		
			$effectIni = ceil($effectIni/2);  	
			$effectPower = ceil($effectPower/2);			
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
			for($i=1; $i<=$effectPower;$i++){
				if ($reactor){//Some ships might not have a traditional reactor.						
					$crit = new OutputReduced1(-1, $ship->id, $reactor->id, "OutputReduced1", $gamedata->turn, $gamedata->turn+1);
					$crit->updated = true;
					$reactor->criticals[] =  $crit;
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
			$this->data["Special"] .= "<br>Deals Flash damage that ignores armor (Advanced Armor is treated as 2 points less).";	
			$this->data["Special"] .= "<br>If this weapon hits, it uses psychic energy to disrupt its target next turn in the following ways:";
			$this->data["Special"] .= "<br> - 1d3 penalty to EW,";
			$this->data["Special"] .= "<br> - 1d3 penatly to Initiative,";
			$this->data["Special"] .= "<br> - 1d4+1 penalty to available power.";													
			$this->data["Special"] .= "<br>Ballistic weapon that can use offensive EW.";
			$this->data["Special"] .= "<br>Multiple Psionic Torpedoes do not stack effects (but do stack with Psychic Field).";			
			$this->data["Special"] .= "<br>Can be boosted twice, for +2 damage per boost level.";			
		    $this->data["Special"] .= "<br>Has +1 modifier to critical hits, and +1 to fighter dropout rolls.";			
		}

        private function getExtraDamagebyBoostlevel($turn){
            $add = 0;
            switch($this->getBoostLevel($turn)){
                case 1:
                    $add = 2;
                    break;
                case 2:
                    $add = 4;
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
            $dmg = Dice::d(8,1) + 13 + $add ;
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
            $this->minDamage = 14 + ($boost * 2);
        }   

        public function setMaxDamage(){
            $turn = TacGamedata::$currentTurn;
            $boost = $this->getBoostLevel($turn);
            $this->maxDamage = 21 + ($boost * 2); 
		}

/* //OLD CODE RELATING TO WHEN PSIONIC TORPEDO FLAYED ARMOUR FROM TARGET
   protected function doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata, $damageWasDealt, $location = null)
    {
    //$pos ONLY relevant for FIGHTER armor if damage source position is different than one from weapon itself
        //otherwise best leave null BUT fill $location!
    //    damageWasDealt indicates whether this hit already caused damage - important for overkill for some damage modes
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
             
			if (isset($this->alreadyFlayed[$target->id])) return;         	
			$this->alreadyFlayed[$target->id] = true;//mark engaged 
			
 			if ($target instanceof FighterFlight) return;	//Fighrers armour isn't flayed'			
			
            if ($system->advancedArmor) return; //Neither is Advanced Armour
      
                  	 			        	                         
	        $effectArmor = Dice::d(3,1);//strength of effect: 1d3
	
            foreach ($target->systems as $system){
  //              if ($system->advancedArmor) return;              					
                if ($target->shipSizeClass<=1 || $system->location === $location){ //MCVs and smaller ships are one huge section technically
	             	for($i=1; $i<=$effectArmor;$i++){
	                    $crit = new ArmorReduced(-1, $target->id, $system->id, "ArmorReduced", $gamedata->turn);
	                    $crit->updated = true;
	                    $crit->inEffect = false;
	                    $system->criticals[] = $crit;                 
		                }     
		            }	            
				}
		     $fireOrder->pubnotes .= "<br> Armor reduced on entire ship section."; 				 
        } //endof function doDamage	 
*/
		    
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

		protected $overrideCallingRestrictions = true;
		protected $canOnlyCalledShot = true;		
//		public $canTargetOtherSections = true; //NOT IMPLEMENTED. When set to true, weapon can called shot systems on external sections of target not facing firing ship.
			 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		        //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 5;
            if ( $powerReq == 0 ) $powerReq = 3;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }     
	    
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            $this->data["Special"] = "Ballistic weapon used ONLY for Called Shots on systems located on exterior sections (e.g. weapons/thrusters).";
            $this->data["Special"] .= "<br>Once it hits a system, it will try to damage it by adding a critical effect.";
            $this->data["Special"] .= "<br>This critical effect will remain until target system is destroyed, or after five failed attempts by the Limpet Bore.";            
            $this->data["Special"] .= "<br>Has no effect on targets equipped with Advanced Armor.";
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
		$fireOrder->pubnotes .= "<br> Limpet Bore cannot attached to advanced armor.";				
		return; 	
		}

		if ($system->id != $fireOrder->calledid) {//In case it ends up in general hit chart somehow.
		$fireOrder->pubnotes .= "<br> Limpet Bore was not targeted at a system.";				
		return; 	
		}	
				
		if($system){
			$fireOrder->pubnotes .= "<br> Limpet Bore attaches to system.";				
			$crit = new LimpetBore(-1, $ship->id, $system->id, 'LimpetBore', $gamedata->turn); 
			$crit->updated = true;
			$system->criticals[] =  $crit;
			}
		}	
		
		
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
 //           $strippedSystem->canTargetOtherSections = $this->canTargetOtherSections;         
                     
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

    
?>
