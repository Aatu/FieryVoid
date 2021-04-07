<?php

	//common  functionality of Antimatter weapons
	class AntimatterWeapon extends Weapon{   
        public $animation = "beam";     
		public $animationColor = array(0, 184, 230);
        public $projectilespeed = 10;
        public $animationWidth = 1;
        public $animationExplosionScale = 0.20;
        public $trailLength = 5;
		
		public $doubleRangeIfNoLock = true; //if no lock-on is achieved - double the range instead of range penalty
		public $specialRangeCalculation = true; //to inform front end that it should use weapon-specific range penalty calculation - such a method should be present in .js!
		
		public $damageType = "Standard";
    	public $weaponClass = "Antimatter"; 
		public $firingMode = "Standard";
		
		public $rngNoPenalty = 1; //maximum range at which weapon suffers no penalty
		public $rngNormalPenalty = 2;//maximum range at which weapon suffers regular penalty
		public $maxX = 10; //maximum value of X
		public $maxXArray = array(); //maximum value of X
		public $dmgEquation = '2X+5'; //to be able to automatically incorporate this into weapon description
		public $dmgEquationArray = array(); //For AntimatterShredder		
		//effect: 
		// - for range up to $rngNoPenalty weapon suffers no penalty
		// - for ranges higher than $rngNoPenalty up to $rngNormalPenalty weapon suffers regular range penalty
		// - for ranges above $rngNormalPenalty weapon suffers double range penalty
		
		
		
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            $this->data["Special"] = "Damage is dependent on how good a hit is - it's not randomized. Quality of hit is called X, and equals difference between actual and needed to-hit roll divided by 5.";
			$this->data["Special"] .= "<br>This weapon does " . $this->dmgEquation .' damage, with maximum X being ' . $this->maxX . '.';
			$this->data["Special"] .= '<br>This weapon suffers no range penalty up to ' . $this->rngNoPenalty . ' hexes, regular penalty up to ' . $this->rngNormalPenalty . ' hexes, and double penalty for remaining distance.';
			$this->data["Special"] .= "<br>In case of no lock-on the range itself is doubled (for the calculation above), not calculated penalty.";
        }
		
		public function getX($fireOrder){
			$X = floor(($fireOrder->needed - $fireOrder->rolled)/5);
			$X = max($this->maxX, $X);
			return $X;
		}
		
		public function calculateRangePenalty($distance){
			$rangePenalty = 0;//base penalty	
			$rangePenalty += $this->rangePenalty * max(0,$distance-$this->rngNoPenalty); //regular range penalty
			$rangePenalty += $this->rangePenalty * max(0,$distance-$this->rngNormalPenalty); //regular range penalty again (for effective double penalty)
			return $rangePenalty;
		}		
		
	}


    class AntimatterConverter extends Weapon{ //deliberately NOT extending AntimatterWeapon class, AMConverter mostly uses regular calculations        
        public $name = "antimatterConverter";
        public $displayName = "Antimatter Converter";
        public $animation = "beam";
        public $animationColor = array(175, 225, 175);
        public $projectilespeed = 10;
        public $animationWidth = 4;
        public $animationExplosionScale = 0.90;
        public $trailLength = 20;
        public $priority = 2;
        public $loadingtime = 3;
        public $rangePenalty = 1;
        public $fireControl = array(-6, 4, 4); // fighters, <=mediums, <=capitals 


	    public $damageType = 'Flash'; 
    	public $weaponClass = "Antimatter"; 
	    
	public $firingModes = array( 
		1 => "Flash"
	);	    
        
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            $this->data["Special"] = "Damage is dependent on how good a hit is - it's not randomized (actual damage done is 4X+2).<br>There is no actual maximum, with exceptional hit chance damage may be exceptional as well.";
        }
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
       	public function getDamage($fireOrder){
                return $damage = 2 + 4*floor(($fireOrder->needed - $fireOrder->rolled)/5);
            }

        public function setMinDamage(){     $this->minDamage = 2;      }
        public function setMaxDamage(){     $this->maxDamage = 82;      }
    }



class AntiprotonGun extends AntimatterWeapon{        
        public $name = "AntiprotonGun";
        public $displayName = "Antiproton Gun";
		public $iconPath = "AntiprotonGun.png";
		/* already included in AntimatterWeapon class; if a given weapon should be different then override, but if You want to change general AM weapons animation properties - go to AntimatterWeapon class instead		
        public $animation = "beam";
        public $animationColor = array(0, 184, 230);
        public $projectilespeed = 10;
        public $animationWidth = 1;
        public $animationExplosionScale = 0.20;
        public $trailLength = 5;
		*/
        public $priority = 5;

        public $intercept = 2;
        public $loadingtime = 1;
		
		public $rangePenalty = 1; //-1/hex base penalty

		public $rngNoPenalty = 5; //maximum range at which weapon suffers no penalty
		public $rngNormalPenalty = 10;//maximum range at which weapon suffers regular penalty
		public $maxX = 10; //maximum value of X
		public $dmgEquation = 'X+12'; //to be able to automatically incorporate this into weapon description

        public $fireControl = array(2, 3, 3); // fighters, <mediums, <capitals 
		
		
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
			/*
			if (!isset($this->data["Special"])) {
				$this->data["Special"] = '';
			}else{
				$this->data["Special"] .= '<br>';
			}
			//...and NOW $this->data["Special"] may be extended by further text, if still needed
			*/
        }
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
			if ( $maxhealth == 0 ) $maxhealth = 8;
            if ( $powerReq == 0 ) $powerReq = 4;            
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
       	public function getDamage($fireOrder){
                $X = $this->getX($fireOrder);
				$damage = $X + 12;
				return $damage ;
            }

        public function setMinDamage(){     $this->minDamage = 12;      }
        public function setMaxDamage(){     $this->maxDamage = 22;      }
	
} //end of AntiprotonGun

	
class AntimatterCannon extends AntimatterWeapon{        
        public $name = "AntimatterCannon";
        public $displayName = "Antimatter Cannon";
		public $iconPath = "AntimatterCannon.png";
        public $animation = "laser";
        public $animationColor = array(0, 184, 230);
        public $animationWidth = 4;
        public $animationWidth2 = 0.2;
		
        public $priority = 6; //that's Standard Heavy hit!
        public $raking = 10;
        public $loadingtime = 3;
		public $rangePenalty = 1; //-1/hex base penalty
        public $intercept = 1;
        
        public $firingModes = array(
            1 => "Raking",
            2 => "Piercing"
        );
        
        public $damageTypeArray = array(1=>'Raking', 2=>'Piercing');

		public $rngNoPenalty = 10; //maximum range at which weapon suffers no penalty
		public $rngNormalPenalty = 20;//maximum range at which weapon suffers regular penalty
		public $maxX = 20; //maximum value of X
		public $dmgEquation = '2X+16'; //to be able to automatically incorporate this into weapon description

        public $fireControlArray = array( 1=>array(-2, 3, 5), 2=>array(null,-1, 1) ); // fighters, <mediums, <capitals 
		
		
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
			/*
			if (!isset($this->data["Special"])) {
				$this->data["Special"] = '';
			}else{
				$this->data["Special"] .= '<br>';
			}
			//...and NOW $this->data["Special"] may be extended by further text, if still needed
			*/
        }
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
			if ( $maxhealth == 0 ) $maxhealth = 9;
            if ( $powerReq == 0 ) $powerReq = 8;            
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
       	public function getDamage($fireOrder){
                $X = $this->getX($fireOrder);
				$damage = $X + 16;
				return $damage ;
            }

        public function setMinDamage(){     $this->minDamage = 16;      }
        public function setMaxDamage(){     $this->maxDamage = 56;      }
	
} //end of class AntimatterCannon
	
	
class AntiprotonDefender extends AntimatterWeapon{        
        public $name = "AntiprotonDefender";
        public $displayName = "Antiproton Defender";
		public $iconPath = "AntiprotonDefender.png";
		/* already included in AntimatterWeapon class; if a given weapon should be different then override, but if You want to change general AM weapons animation properties - go to AntimatterWeapon class instead		
        public $animation = "beam";
        public $animationColor = array(0, 184, 230);
        public $projectilespeed = 10;
        public $animationWidth = 1;
        public $animationExplosionScale = 0.20;
        public $trailLength = 5;
		*/
        public $priority = 4; //that's Standard Heavy hit!

        public $intercept = 3;
        public $loadingtime = 1;
		
		public $rangePenalty = 1; //-1/hex base penalty

		public $rngNoPenalty = 3; //maximum range at which weapon suffers no penalty
		public $rngNormalPenalty = 6;//maximum range at which weapon suffers regular penalty
		public $maxX = 10; //maximum value of X
		public $dmgEquation = 'X+8'; //to be able to automatically incorporate this into weapon description

        public $fireControl = array(4, 2, 2); // fighters, <mediums, <capitals 
		
		
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
			/*
			if (!isset($this->data["Special"])) {
				$this->data["Special"] = '';
			}else{
				$this->data["Special"] .= '<br>';
			}
			//...and NOW $this->data["Special"] may be extended by further text, if still needed
			*/
        }
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
			if ( $maxhealth == 0 ) $maxhealth = 4;
            if ( $powerReq == 0 ) $powerReq = 3;            
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
       	public function getDamage($fireOrder){
                $X = $this->getX($fireOrder);
				$damage = $X + 8;
				return $damage ;
            }

        public function setMinDamage(){     $this->minDamage = 8;      }
        public function setMaxDamage(){     $this->maxDamage = 18;      }
	
} //end of class AntiprotonDefender


class AntimatterTorpedo extends AntimatterWeapon{        
        public $name = "AntimatterTorpedo";
        public $displayName = "Antimatter Torpedo";
		public $iconPath = "AntimatterTorpedo.png";
        public $trailColor = array(0, 184, 230);
        public $animation = "torpedo";
        public $animationColor = array(30, 170, 255);
        public $animationExplosionScale = 0.25;
        public $projectilespeed = 12;
        public $animationWidth = 10;
        public $trailLength = 10;
        public $priority = 6;
        
        public $ballistic = true;
        public $weaponClass = "Ballistic";         

        public $loadingtime = 2;
		
		public $rangePenalty = 1; //-1/hex base penalty

		public $rngNoPenalty = 25; //maximum range at which weapon suffers no penalty
		public $rngNormalPenalty = 50;//maximum range at which weapon suffers regular penalty
		public $maxX = 12; //maximum value of X
		public $dmgEquation = 'X+8'; //to be able to automatically incorporate this into weapon description

        public $fireControl = array(-2, 2, 4); // fighters, <mediums, <capitals 
		
		
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
			/*
			if (!isset($this->data["Special"])) {
				$this->data["Special"] = '';
			}else{
				$this->data["Special"] .= '<br>';
			}
			//...and NOW $this->data["Special"] may be extended by further text, if still needed
			*/
        }
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
			if ( $maxhealth == 0 ) $maxhealth = 6;
            if ( $powerReq == 0 ) $powerReq = 7;            
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
       	public function getDamage($fireOrder){
                $X = $this->getX($fireOrder);
				$damage = $X + 8;
				return $damage ;
            }

        public function setMinDamage(){     $this->minDamage = 8;      }
        public function setMaxDamage(){     $this->maxDamage = 20;      }
	
} //end of class AntimatterTorpedo

class LightAntiprotonGun extends LinkedWeapon{  //deliberately NOT extending AntimatterWeapon class uses regular calculations 
	public $name = "LightAntiprotonGun";
	public $displayName = "Light Antiproton Gun";
    public $animation = "trail";
    public $animationColor = array(0, 184, 230);
    public $animationExplosionScale = 0.10;
    public $projectilespeed = 12;
    public $animationWidth = 2;
    public $trailLength = 10;

	public $priority = 3;

	public $loadingtime = 1;
	public $shots = 2;
	public $defaultShots = 2;

	public $rangePenalty = 2;
	public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals
	public $rangeDamagePenalty = 1;

	public $damageType = "Standard"; 
	public $weaponClass = "Antimatter"; 

	function __construct($startArc, $endArc, $nrOfShots = 2){ 
		$this->shots = $nrOfShots;
		$this->defaultShots = $nrOfShots;        
	
		if($nrOfShots === 1){
			$this->iconPath = "LightAntiprotonGun.png";
		}
		if($nrOfShots === 2){
			$this->iconPath = "LightAntiprotonGun2.png";
		}
	
		parent::__construct(0, 1, 0, $startArc, $endArc);
	}

	public function getDamage($fireOrder){        return Dice::d(6, 2) - 1;   }
	public function setMinDamage(){     $this->minDamage = 1 ;      }
	public function setMaxDamage(){     $this->maxDamage = 11 ;      }

}// end of class LightAntiprotonGun


class LtAntimatterCannon extends Weapon{  //deliberately NOT extending AntimatterWeapon class uses regular calculations 
		public $iconPath = "LightAntimatterCannon.png";
        public $name = "LtAntimatterCannon";
        public $displayName = "Light Antimatter Cannon";
        public $animation = "trail";
        public $animationColor = array(0, 184, 230);
        public $trailColor = array(0, 184, 230);
        public $projectilespeed = 11;
        public $animationWidth = 4;
        public $trailLength = 12;
        public $animationExplosionScale = 0.25;
                
        public $loadingtime = 2;
		public $priority = 5;
		public $shots = 1;		
            
        public $rangePenalty = 2;
        public $fireControl = array(0, 0, 0); // fighters, <=mediums, <=capitals 

		public $damageType = "Standard"; 
		public $weaponClass = "Antimatter"; 

        function __construct($startArc, $endArc, $shots = 1){
            $this->shots = $shots;
            $this->defaultShots = $shots;
            
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }   

        
        public function getDamage($fireOrder){        return Dice::d(10, 1) + 4 * 2;   }
        public function setMinDamage(){     $this->minDamage = 10 ;      }
        public function setMaxDamage(){     $this->maxDamage = 28 ;      }

}//end of class LtAntimatterCannon


class AntimatterShredder extends AntimatterWeapon{        
        public $name = "AntimatterShredder";
        public $displayName = "Antimatter Shredder";
		public $iconPath = "AntimatterShredder.png";
		public $animationArray = array(1=>'ball', 2=>'laser', 3=>'laser');
        public $animationColor = array(0, 184, 230);
        public $explosionColor = array(0, 184, 230);
        public $trailColor = array(0, 184, 230);                
   		public $animationWidthArray = array(1=>10, 2=>4, 3=>4);
        public $animationWidth2 = 0.2; 	
        public $projectilespeed = 12;
        public $trailLength = 10;
        public $animationExplosionScale = 1;                        	
   		public $animationExplosionTypeArray = array(1=>'AoE', 2=>'normal', 3=>'normal');        

        public $priority = 6; //that's Standard Heavy hit!
        public $raking = 10;
        public $loadingtime = 3;
  		public $rangePenaltyArray = array(1=>0, 2=>1, 3=>1); //-1/hex base penalty
        public $intercept = 1;
 		public $rangeArray = array(1=>10, 2=>0, 3=>0);
		public $dmgEquationArray = array(1=>'2X+6', 2=>'2X+16', 3=>'2X+16');  		        

 	    public $hextargetArray = array(1=>true, 2=>false, 3=>false); //I have added this as a new marker to weapon.php
		public $hidetarget = false;
		public $ballistic = false;
		//public $uninterceptable = true; Need $uninterceptableArray added to Weapon.php
		//public $doNotIntercept = true; Need $doNotInterceptArray added to Weapon.php	    
    
        public $firingModes = array(
            1 => "AoE",
            2 => "Raking",
            3 => "Piercing"
        );
        
        public $damageTypeArray = array(1=>'Antimatter', 2=>'Raking', 3=>'Piercing');

		public $rngNoPenalty = 10; //maximum range at which weapon suffers no penalty.  Do not need arrays as Shredder has max range 10 anyway?
		public $rngNormalPenalty = 20;//maximum range at which weapon suffers regular penalty
		public $maxXArray = array(1=>10, 2=>20, 3=>20); //maximum value of X
		public $dmgEquationArray = array(1=>'2X+6', 2=>'2X+16', 3=>'2X+16');  //to be able to automatically incorporate this into weapon description

        public $fireControlArray = array(1=>array(0, 0, 0), 2=>array(-2, 3, 5), 3=>array(null,-1, 1) ); // fighters, <mediums, <capitals 
		
// - multiplying one declared attack into actual multiple attacks on nearby units (finding nearby units - EMine, multiplying attacks - ScatterGun)ONLY FOR FIRING MODE 1
	public function beforeFiringOrderResolution($gamedata){ //from scattergun
		if($this->multiplied==true) return;//shots of this weapon are already multiplied
		$this->multiplied = true;//shots WILL be multiplied in a moment, mark this
		//is offensive fire declared?...
		$offensiveShot = null;
		$noOfShots = Dice::d(6,1); //actual number of shots for this turn

		foreach($this->fireOrders as $fire){
			if(($fire->type =='normal') && ($fire->turn == $gamedata->turn)) $offensiveShot = $fire;
		}
		if($offensiveShot!==null){ //offensive fire declared, multiply!
			while($noOfShots > 1){ //first shot is already declared!
				$multipliedFireOrder = new FireOrder( -1, $offensiveShot->type, $offensiveShot->shooterid, $offensiveShot->targetid,
					$offensiveShot->weaponid, $offensiveShot->calledid, $offensiveShot->turn, $offensiveShot->firingMode,
					0, 0, 1, 0, 0, null, null
				);
				$multipliedFireOrder->addToDB = true;
				$this->fireOrders[] = $multipliedFireOrder;
				$noOfShots--;	      
			}
		//}else{   DEFENSIVE
			//$this->guns = $noOfShots; DEFENSIVE
		}
	} //endof function beforeFiringOrderResolution
	

// - targeting direct fire weapons at hex rather than unit (Vortex Disruptor does that) ONLY FOR FIRING MODE 1
    public function fire($gamedata, $fireOrder) {
    switch($this->firingMode){
        	
       case 1:    	 
        $this->changeFiringMode($fireOrder->firingMode);//changing firing mode may cause other changes, too!
        $shooter = $gamedata->getShipById($fireOrder->shooterid);

        /** @var MovementOrder $movement */
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
		$fireOrder->rolled = $rolled;

        $target = new OffsetCoordinate($fireOrder->x, $fireOrder->y);
        
            $ships1 = $gamedata->getShipsInDistance($target);
            $ships2 = $gamedata->getShipsInDistance($target, 1);
            foreach ($ships2 as $targetShip) {
            //    if (isset($ships1[$targetShip->id])) { //ship on target hex!
           //         $sourceHex = $posLaunch;
           //         $damage = $this->maxDamage;
              //  } else { //ship at range 1!
              //      $sourceHex = $target;
              //      $damage = $this->minDamage;
              //  }
                $this->AOEdamage($targetShip, $shooter, $fireOrder, $sourceHex, $damage, $gamedata);
            }
          //    $fireOrder->rolled = max(1, $fireOrder->rolled);//Marks that fire order has been handled, just in case it wasn't marked yet!
		}
	} //endof function fire  
        


    public function AOEdamage($target, $shooter, $fireOrder, $sourceHex, $damage, $gamedata) //do I need to put a firing mode switch for this?
    {
        if ($target->isDestroyed()) return; //no point allocating
        $damage = $this->getDamageMod($damage, $shooter, $target, $sourceHex, $gamedata);
        $damage -= $target->getDamageMod($shooter, $sourceHex, $gamedata->turn, $this);
        if ($target instanceof FighterFlight) {
            foreach ($target->systems as $fighter) {
                if ($fighter == null || $fighter->isDestroyed()) {
                    continue;
                }
				$damage = $this->getDamage($fireOrder);
                $this->doDamage($target, $shooter, $fighter, $damage, $fireOrder, $sourceHex, $gamedata, false);
            }
        //add more if functions???
        
        } else {   //Commented out as this targets ships and not needed for the plasma web
            $tmpLocation = $target->getHitSectionPos(Mathlib::hexCoToPixel($sourceHex), $fireOrder->turn);
          $system = $target->getHitSystem($shooter, $fireOrder, $this, $gamedata, $tmpLocation);
           $this->doDamage($target, $shooter, $system, $damage, $fireOrder, null, $gamedata, false, $tmpLocation);
        }
    }
    
//only one Shredder can affect a given unit. The two Ipsha systems Marcin recommended looking at to address this are the Ipsha Spark Field and Surge Cannon	
	
	
    public function calculateHitBase($gamedata, $fireOrder) {   ONLY FOR FIRING MODE 1
    switch($this->firingMode){
        	
       case 1:	
		$fireOrder->needed = 100; //update to be against defense profile only + range penalty which is usually 0.  Cannot hit firing unit too.
		$fireOrder->updated = true;
       case 2:	//how do i make Case 2 and 3 act normal for AM Cannon modes?
       case 3:	    
		}
    }


		
    public function setSystemDataWindow($turn){ //this is done I think, but can it be tidied so Case 2 and 3 are combined?
            parent::setSystemDataWindow($turn);
        
		switch($this->firingMode){
        	
       case 1:	
            $this->data["Special"] = "Damage is dependent on how good a hit is - it's not randomized. Quality of hit is called X, and equals difference between actual and needed to-hit roll divided by 5.";
			$this->data["Special"] .= "<br>This weapon does " . $this->dmgEquation .' damage, with maximum X being ' . $this->maxX . '.';
			$this->data["Special"] .= '<br>This weapon targets a hex within a maximum range of 10 hexes and rolls to hit each unit/fighter in that and adjacent hexes';
			$this->data["Special"] .= '<br>The Shredder can potentially hit capital ships 1d6 times, HCVs and medium ships 1d3 times, fighters once, and enormous units 1d6+3 times';
			$this->data["Special"] .= "<br>Once the number of hits is determined it rolls to hits as normal but ignores DEW and jinking.";			
       case 2:	   
            $this->data["Special"] = "Damage is dependent on how good a hit is - it's not randomized. Quality of hit is called X, and equals difference between actual and needed to-hit roll divided by 5.";
			$this->data["Special"] .= "<br>This weapon does " . $this->dmgEquation .' damage, with maximum X being ' . $this->maxX . '.';
			$this->data["Special"] .= '<br>This weapon suffers no range penalty up to ' . $this->rngNoPenalty . ' hexes, regular penalty up to ' . $this->rngNormalPenalty . ' hexes, and double penalty for remaining distance.';
			$this->data["Special"] .= "<br>In case of no lock-on the range itself is doubled (for the calculation above), not calculated penalty.";
      case 3:	   
            $this->data["Special"] = "Damage is dependent on how good a hit is - it's not randomized. Quality of hit is called X, and equals difference between actual and needed to-hit roll divided by 5.";
			$this->data["Special"] .= "<br>This weapon does " . $this->dmgEquation .' damage, with maximum X being ' . $this->maxX . '.';
			$this->data["Special"] .= '<br>This weapon suffers no range penalty up to ' . $this->rngNoPenalty . ' hexes, regular penalty up to ' . $this->rngNormalPenalty . ' hexes, and double penalty for remaining distance.';
			$this->data["Special"] .= "<br>In case of no lock-on the range itself is doubled (for the calculation above), not calculated penalty.";
			}
		}  
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){ //done
			if ( $maxhealth == 0 ) $maxhealth = 10;
            if ( $powerReq == 0 ) $powerReq = 8;            
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
 
//used MLPA text as a basis
        public function getDamage($fireOrder){ 
		switch($this->firingMode){
			case 1:
                $X = $this->getX($fireOrder);
				$damage = $X + 6; //Shredder
				return $damage ;
			case 2:
                $X = $this->getX($fireOrder);
				$damage = $X + 16;
				return $damage ; //AMCannonRaking
				break;
			case 3:
                $X = $this->getX($fireOrder);
				$damage = $X + 16;
				return $damage ; //AMCannonPiercing
				break;			
		}
	}
        public function setMinDamage(){ 
		switch($this->firingMode){
			case 1:
				$this->minDamage = 6; //Shredder
				break;
			case 2:
				$this->minDamage = 16; //AMCannonRaking
				break;	
			case 3:
				$this->minDamage = 16; //AMCannonPiercing
				break;		
		}
		$this->minDamageArray[$this->firingMode] = $this->minDamage;
	}
        public function setMaxDamage(){
		switch($this->firingMode){
			case 1:
				$this->maxDamage = 26; //Shredder
				break;
			case 2:
				$this->maxDamage = 56; //AMCannonRaking
				break;	
			case 3:
				$this->maxDamage = 56; //AMCannonPiercing
				break;	
		}
		$this->maxDamageArray[$this->firingMode] = $this->maxDamage;
	}
	
} //end of class AntimatterShredder
		
?>
