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

    class PsionicTorpedo extends Torpedo{ //Powerful Thirdspace weapon that detonates as AoE and reduces armour of targets.  
        public $name = "PsionicTorpedo";
        public $displayName = "Psionic Torpedo";
        public $iconPath = "PsionicTorpedo.png";        
        public $range = 60;
        public $loadingtime = 2;
        
        public $fireControl = array(-4, 4, 5); // fighters, <mediums, <capitals 
        
        public $animation = "torpedo";
        public $animationColor = array(153, 0, 0);
		/*
        public $trailColor = array(141, 240, 255);
        public $animationExplosionScale = 0.25;
        public $projectilespeed = 12;
        public $animationWidth = 10;
        public $trailLength = 10;
		*/
		public $firingModes = array(
		1 => "AoE"
	);
 	   	public $damageType = "Flash";
        public $priority = 1; 	   

		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn); 
			if (!isset($this->data["Special"])) {
				$this->data["Special"] = '';
			}else{
				$this->data["Special"] .= '<br>';
			}	    
			$dmgDirect = $this->maxDamage;
			$dmgNear = $this->minDamage; 
			$this->data["Special"] .= "<br>Hits target and all other units on hex for $dmgDirect damage, all units on nearby hexes are hit for $dmgNear. In case of flight level units every craft is damaged separately.";  
			$this->data["Special"] .= "<br>Causes power disruptions on enemy ships.";  
	        }		

    public function fire($gamedata, $fireOrder)
    {
        $shooter = $gamedata->getShipById($fireOrder->shooterid);
        $target = $gamedata->getShipById($fireOrder->targetid);

        $fireOrder->needed -= $fireOrder->totalIntercept;
        $notes = "Interception: " . $fireOrder->totalIntercept . " sources:" . $fireOrder->numInterceptors . ", final to hit: " . $fireOrder->needed;
        $fireOrder->notes .= $notes;

        $pos = null; //functions will properly calculate from firing unit, which is important at range 0
        //$pos = $shooter->getCoPos();
        if ($this->ballistic) {
            $movement = $shooter->getLastTurnMovement($fireOrder->turn);
            $pos = $movement->position;
        }

        $shotsFired = $fireOrder->shots; //number of actual shots fired
 /*       if ($this->damageType == 'Pulse') {//Pulse mode always fires one shot of weapon - while 	$fireOrder->shots marks number of pulses for display purposes
            $shotsFired = 1;
			$fireOrder->shots = $this->maxpulses;
        } 			*/
        for ($i = 0; $i < $shotsFired; $i++) {
            $needed = $fireOrder->needed;
/*            if ($this->damageType != 'Pulse') {//non-Pulse weapons may use $grouping, too!
                $needed = $fireOrder->needed - $this->getShotHitChanceMod($i);
            } 		*/
            
/*
            //for linked shot: further shots will do the same as first!
            if ($i == 0) { //clear variables that may be relevant for further shots in line
                $fireOrder->linkedHit = null;
            }
            $rolled = Dice::d(100);
            if ($this->isLinked && $i > 0) { //linked shot - number rolled (and effect) for furthr shots will be just the same as for first
                $rolled = $fireOrder->rolled;
            } 		*/

            //interception?
            if ($rolled > $needed && $rolled <= $needed + ($fireOrder->totalIntercept * 5)) { //$fireOrder->pubnotes .= "Shot intercepted. ";
                if ($this->damageType == 'Pulse') {
                    $fireOrder->intercepted += $this->maxpulses;
                } else {
                    $fireOrder->intercepted += 1;
                }
            }

            $fireOrder->notes .= " FIRING SHOT " . ($i + 1) . ": rolled: $rolled, needed: $needed\n";
            $fireOrder->rolled = $rolled; //might be useful for weapon itself, too - like counting damage for Anti-Matter

            //hit?
            if ($rolled <= $needed) {
                $hitsRemaining = 1;

                if ($this->damageType == 'Pulse') { //possibly more than 1 hit from a shot
                    $hitsRemaining = $this->rollPulses($gamedata->turn, $needed, $rolled); //this takes care of all details
                }

                while ($hitsRemaining > 0) {
                    $hitsRemaining--;
                    $fireOrder->shotshit++;
                    $this->beforeDamage($target, $shooter, $fireOrder, $pos, $gamedata);
                }
            }
        }  
	                //do damage to ships in range...
            $ships1 = $gamedata->getShipsInDistance($target);
            $ships2 = $gamedata->getShipsInDistance($target, 1);
            foreach ($ships2 as $targetShip) {
                if (isset($ships1[$targetShip->id])) { //ship on target hex!
                    $sourceHex = $posLaunch;
                    $damage = $this->maxDamage;
                } else { //ship at range 1!
                    $sourceHex = $target;
                    $damage = $this->minDamage;
                }
                $this->AOEdamage($targetShip, $shooter, $fireOrder, $sourceHex, $damage, $gamedata);
            }
      
               
/*		//for last segment of Sustained shot - force shutdown!
		$newExtraShots = $this->overloadshots - 1; 	
		if( $newExtraShots == 0 ) {
			$crit = new ForcedOfflineOneTurn(-1, $this->unit->id, $this->id, "ForcedOfflineOneTurn", $gamedata->turn);
			$crit->updated = true;
			$crit->newCrit = true; //force save even if crit is not for current turn
			$this->criticals[] =  $crit;
		} */

        $fireOrder->rolled = max(1, $fireOrder->rolled);//Marks that fire order has been handled, just in case it wasn't marked yet!
		TacGamedata::$lastFiringResolutionNo++;    //note for further shots
		$fireOrder->resolutionOrder = TacGamedata::$lastFiringResolutionNo;//mark order in which firing was handled!
	    
    } //endof fire



    public function AOEdamage($target, $shooter, $fireOrder, $sourceHex, $damage, $gamedata)
    {
        if ($target->isDestroyed()) return; //no point allocating
        $damage = $this->getDamageMod($damage, $shooter, $target, $sourceHex, $gamedata);
        $damage -= $target->getDamageMod($shooter, $sourceHex, $gamedata->turn, $this);
        if ($target instanceof FighterFlight) {
            foreach ($target->systems as $fighter) {
                if ($fighter == null || $fighter->isDestroyed()) {
                    continue;
                }
                $this->doDamage($target, $shooter, $fighter, $damage, $fireOrder, $sourceHex, $gamedata, false);
            }
        } else {
            $tmpLocation = $target->getHitSectionPos(Mathlib::hexCoToPixel($sourceHex), $fireOrder->turn);
            $system = $target->getHitSystem($shooter, $fireOrder, $this, $gamedata, $tmpLocation);
            $this->doDamage($target, $shooter, $system, $damage, $fireOrder, null, $gamedata, false, $tmpLocation);
        }
    }  

	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ //really no matter what exactly was hit!	
		$effectArmor = Dice::d(3,1);//strength of effect: 1d6
		$fireOrder->pubnotes .= "<br> Armor reduced by $effectArmor.";
		
		if (WeaponEM::isTargetEMResistant($ship,$system)){
			$effectArmor = ceil($effectArmor/2);  	//Ancients are somewhat resistant to attacks from Thirdspace Aliens.	
			return $effectArmor;	
		}
		
	//	if (WeaponEM::isTargetEMResistant($ship,$system)){
	//		$effectPower = 0; //Let's say Ancients unaffected by power drain, to prevent Shadows etc from having to power down only weapon etc
	//		return $effectPower;
	//	}
		if ($ship instanceof FighterFlight){  //place effect on first fighter, even if it's already destroyed!			
			$firstFighter = $ship->getSampleFighter();
			if($firstFighter){
				for($i=1; $i<=$effectArmor;$i++){
					$crit = new ArmorReduced(-1, $ship->id, $firstFighter->id, 'ArmorReduced', $gamedata->turn); 
					$crit->updated = true;
			        $firstFighter->criticals[] =  $crit;
				}
			}
		}else{ //ship - place effect on C&C!
            foreach ($target->systems as $system){
   //             if ($system->advancedArmor) return;
                if ($target->shipSizeClass<=1 || $system->location === $location){ //MCVs and smaller ships are one huge section technically
                for($i=1; $i<=$effectArmor;$i++){	
                    $crit = new ArmorReduced(-1, $target->id, $system->id, "ArmorReduced", $gamedata->turn);
                    $crit->updated = true;
                    $crit->inEffect = false;
                    $system->criticals[] = $crit;
          	      }
        	    }
			} 
		}
	} //end of function onDamagedSystem
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 12;
            }
            if ( $powerReq == 0 ){
                $powerReq = 6;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return 20;   }
        public function setMinDamage(){     $this->minDamage = 10; /*- $this->dp;*/      }
        public function setMaxDamage(){     $this->maxDamage = 20 ;/*- $this->dp;*/      }
    
    }//endof class PsionicTorpedo
?>
