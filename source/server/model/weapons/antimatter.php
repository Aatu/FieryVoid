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
		public $dmgEquation = '2X+5'; //to be able to automatically incorporate this into weapon description
	
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
	      
	 	    public $hextargetArray = array(1=>true, 2=>false, 3=>false); //I have added $hextargetArray as a new marker to weapon.php
			public $hidetarget = false;
			public $ballistic = false;
			public $uninterceptableArray = array(1=>true, 2=>false, 3=>false);
			public $doNotInterceptArray = array(1=>true, 2=>false, 3=>false); //Added $doNotInterceptArray added to Weapon.php	    
	    
	        public $firingModes = array(
	            1 => "AoE",
	            2 => "Raking",
	            3 => "Piercing"
	        );    
	        public $damageTypeArray = array(1=>'Standard', 2=>'Raking', 3=>'Piercing');

			public $rngNoPenalty = 10; //maximum range at which weapon suffers no penalty.  MARCIN - Do not need arrays as Shredder has max range 10 anyway?
			public $rngNormalPenalty = 20;//maximum range at which weapon suffers regular penalty
			public $maxXArray = array(1=>10, 2=>20, 3=>20);  //I have added $maxXArray as a new marker to weapon.php
			public $dmgEquationArray = array(1=>'2X+6', 2=>'2X+16', 3=>'2X+16');  //Added $dmgEquationArray added to Weapon.php	 
			public $fireControlArray = array(1=>array(0, 0, 0), 2=>array(-2, 3, 5), 3=>array(null,-1, 1) ); // fighters, <mediums, <capitals 

			//temporary private variables
			private $multiplied = false;
			private static $alreadyFiredAt = array(); //Shredders are not cumulative...		



	public function beforeFiringOrderResolution($gamedata){
			if($this->firingMode ==1){
			
			if($this->multiplied==true); return;//shots of this weapon are already multiplied
			$this->multiplied = true;//shots WILL be multiplied in a moment, mark this
			//just basic precaution in case this function is called multiple times for the same weapon ;) . Of course You have to actually _declare_ appropriate variable first, look a few lines higher:

			$fire = null;

			//let's modify this adding more precautions - make sure it's current turn firing order, and DO check mode as well:
			foreach($this->fireOrders as $chosenFire) if( ($chosenFire->turn == $gamedata->turn) && ($chosenFire->firingMode == 1) ) {
				$fire = $chosenFire; //fire order to multiply has been found!
			}
			if(!$fire) return; //nothing to multiply

			//...and now if You are inside THAT, You know You have firing order that, during current turn, tries to actually use Shredder. HERE You need to try to do something about that. Let's get some basic variables we'll probably need:
			$shooter = $this->getUnit();
			$targetHex = new OffsetCoordinate($fire->x, $fire->y);

			//...and let's go search for targets, taking page from eMine and modifying it as appropriate:
			$potentialTargets = $gamedata->getShipsInDistance($targetHex, 1); //all units within 1 hex ofpoint of targeting
			foreach($potentialTargets as $targetUnit){
			//here we'll need to consider whether unit found is actually an appropriate target.
			//we know it's within danger zone, but to be actually fired at, it needs to also be:
			// - in firing arc OR on source hex (Shredders are fitted on all-around mounts, but technically this doesn't need to be the case - You don't want to be surprised ;) )
			// - in firing range
			// - not the firing ship itself
			if($targetUnit->id == $shooter->id) continue;//ignore firing ship itself
			if(isset(AntimatterShredder::$alreadyFiredAt[$targetUnit->id])) continue;//already fired at by another Shredder
			$distanceToTarget = mathlib::getDistance($shooter, $targetUnit);
		//	if($distance>10) continue;//too far
			if($distance > 0){//if ==0 it's automatically eligible, even if it technically isn't in arc; but between 1 and 10 we need to check whether target is in arc
				$relativeBearing = $shooter->getBearingOnUnit($targetUnit);
			        if (!(mathlib::isInArc($relativeBearing, $this->startArc, $this->endArc))) continue; //not in arc
				} 
			}        
			//if we got here then target is eligible to be actually fired at - make appropriate declaration(s)! again ScatterGun helps :)
			AntimatterShredder::$alreadyFiredAt[$targetUnit->id] = true;//first mark target as already fired at
			//we need to make it different for fighters, those will be called shots effectively (remember to make weapon called shot penalty 0 instead of default 8!)
			if($targetUnit instanceOf FighterFlight){ //one shot at every fighter in flight
				foreach ($targetUnit->systems as $fighter){
							if ($fighter == null || $fighter->isDestroyed()){
							    continue;
							}
							//actually we're adding a fire order now!
							$multipliedFireOrder = new FireOrder( -1, $fire->type, $fire->shooterid, $targetUnit->id,
								$fire->weaponid, $fighter->id, $fire->turn, $fire->firingMode,
								0, 0, 1, 0, 0, null, null
							); //I THINK all variables are set properly, I don't remember what that last line of number means :)
							$multipliedFireOrder->addToDB = true;
							$this->fireOrders[] = $multipliedFireOrder;
			                    	}
					} 					
				else{//appropriate number of shots
					$noOfAttacks = 0;
					if($targetUnit->shipSizeClass < 3){ //MCV/HCV: d3 attacks
						$noOfAttacks = Dice::d(3, 1);
					}else{//capital : d6 attacks
						$noOfAttacks = Dice::d(6, 1);
						if($targetUnit->Enormous) $noOfAttacks+=3;//3 more if target is Enormous
					} 
					for($i=0;$i<$noOfAttacks;$i++){ //declare appropriate number of attacks
						//actually we're adding a fire order now!
								$multipliedFireOrder = new FireOrder( -1, $fire->type, $fire->shooterid, $targetUnit->id,
									$fire->weaponid, -1, $fire->turn, $fire->firingMode,
									0, 0, 1, 0, 0, null, null
								); //I THINK all variables are set properly, I don't remember what that last line of number means :)
								$multipliedFireOrder->addToDB = true;
								$this->fireOrders[] = $multipliedFireOrder;
					}
				}
			}
			else{
			parent::beforeFiringOrderResolution();//call standard 
					}
	} //endof function beforeFiringOrderResolution


		
    public function calculateHitBase($gamedata, $fireOrder) {   //ONLY FOR FIRING MODE 1
    
		if($this->firingMode ==1){    	

	//insert shredder calculateHitBase code
	
		//$fireOrder->needed = ($this->profile - $this->rangepenalty); //Range penalty usually 0 for Shredder, but could be raised by crits and rules say it does apply.
		//$fireOrder->updated = true;
		
		} 
		else {	
		parent::calculateHitBase();//call standard
    		}
	}//endof function calculateHitBase	
	
	
	// - targeting direct fire weapons at hex rather than unit
    public function fire($gamedata, $fireOrder) {
	    if($this->firingMode ==1){
	   	 
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
	            $this->AOEdamage($targetShip, $shooter, $fireOrder, $sourceHex, $damage, $gamedata);
	            }
			}
			else{
			parent::fire();//call standard 
			} 
		} //endof function fire  
	        

    public function AOEdamage($target, $shooter, $fireOrder, $sourceHex, $damage, $gamedata){ //MARCIN - do I need to put a firing mode switch for this?
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

	        } else { 
	            $tmpLocation = $target->getHitSectionPos(Mathlib::hexCoToPixel($sourceHex), $fireOrder->turn);
	          $system = $target->getHitSystem($shooter, $fireOrder, $this, $gamedata, $tmpLocation);
	           $this->doDamage($target, $shooter, $system, $damage, $fireOrder, null, $gamedata, false, $tmpLocation);
	        }
	    }
    
//only one Shredder can affect a given unit. The two Ipsha systems Marcin recommended looking at to address this are the Ipsha Spark Field and Surge Cannon	
	//public static function createFiringOrders($gamedata){ //MARCIN - This feels like the right place to stop units being hit more than one and prevent damage to the shooter, I'm just not sure how to apply it.  Does it need a firing mode switch also?
		//if (SparkFieldHandler::$firingDeclared) return; //already done!
		//SparkFieldHandler::$firingDeclared = true;
		
		//apparently ships may be loaded multiple times... make sure fields in array belong to current gamedata!
		//$tmpFields = array();
		//foreach(SparkFieldHandler::$sparkFields as $field){
		//	$shooter = $field->getUnit();
			//if($field->isDestroyed($gamedata->turn-1)) continue; //destroyed weapons can be safely left out
		//	if($field->isDestroyed($gamedata->turn)) continue; //actually at this stage - CURRENT turn should be indicated!
			//is this unit defined in current gamedata? (particular instance!)
		//	$belongs = $gamedata->shipBelongs($shooter);
		//	if ($belongs){
		//		$tmpFields[] = $field;
		//	}			
		//}
	//	SparkFieldHandler::$sparkFields = $tmpFields;
		
		
		//make sure boost level for all weapons is calculated
		//foreach(SparkFieldHandler::$sparkFields as $field){
	//		$field->calculateBoostLevel($gamedata->turn);
	//	}
		
		//sort all fields by boost
	//	usort(SparkFieldHandler::$sparkFields, "self::sortByBoost");	
	
		//table of units that are already targeted
		//$alreadyTargeted = array();
		//create firing order for each weapon (target self)
		//for each weapon find possible targets and add them to weapons' target list
		//strongest weapons fire first, and only 1 field affects particular ship	
		//foreach(SparkFieldHandler::$sparkFields as $field){			
		//	if ($field->isDestroyed($gamedata->turn-1)) continue; //destroyed field does not attack
		//	if ($field->isOfflineOnTurn($gamedata->turn)) continue; //disabled field does not attack
			//$shooter = $field->getUnit();      
			//$targetPos = $shooter->getCoPos();
			//$movementThisTurn = $shooter->getLastTurnMovement($gamedata->turn+1);
			//$fire = new FireOrder(-1, 'normal', $shooter->id, -1, $field->id, -1, $gamedata->turn, 
			//	1, 0, 0, 1, 0, 0, $movementThisTurn->position->q,  $movementThisTurn->position->r, $field->weaponClass
		//	);
		//	$fire->addToDB = true;
		//	$field->fireOrders[] = $fire;			
		//	$aoe = $field->getAoE($gamedata->turn);			
		//	$inAoE = $gamedata->getShipsInDistance($shooter, $aoe);
		//	foreach($inAoE as $targetID=>$target){		
		//		if ($shooter->id == $target->id) continue;//does not threaten self!
		//		if ($target->isDestroyed()) continue; //no point allocating				
		//		if (in_array($target->id,$alreadyTargeted,true)) continue;//each target only once 
				//add to target list
		//		$alreadyTargeted[] = $target->id; //add to list of already targeted units
		//		$field->addTarget($target);
		//	}
	//	} //endof foreach AM Shredder
	
	
	public function setSystemDataWindow($turn){ //this is done I think, but can it be tidied so Case 2 and 3 are combined?
            parent::setSystemDataWindow($turn);

            $this->data["Special"] = "The Shredder targets a hex and adjacent hexes, rolling to hit any capital ships within 1d6 times, HCVs and medium ships 1d3 times, fighters once, and enormous units 1d6+3 times";
			$this->data["Special"] .= "<br>Once the number of potential hits is determined, it rolls to hits as normal but ignores DEW and jinking.";
			$this->data["Special"] .= "<br>Damage is dependent on how good a hit is - it is not randomized. Quality of hit is called X, and equals difference between actual and needed to-hit roll divided by 5.";
			$this->data["Special"] .= "<br>This weapon does ' . $this->dmgEquation .' damage, with maximum X being ' . $this->maxX . '.";			

			$this->data["Special"] .= "<br>This weapon may also switch modes and fire as an Antimatter Cannon in Raking or Piercing mode";
					}
	  
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){ //done
			if ( $maxhealth == 0 ) $maxhealth = 10;
            if ( $powerReq == 0 ) $powerReq = 8;            
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
 
    public function getDamage($fireOrder){ 
			if($this->firingMode ==1){
	                $X = $this->getX($fireOrder);
					$damage = $X + 6; //Shredder
					return $damage ;
			}
			else{
	                $X = $this->getX($fireOrder);
					$damage = $X + 16;
					return $damage ; //AMCannon	
			}
		}
    public function setMinDamage(){ 
			if($this->firingMode ==1){
					$this->minDamage = 6; //Shredder
			}
			else{
					$this->minDamage = 16; //AMCannon
			}
			$this->minDamageArray[$this->firingMode] = $this->minDamage;
		}
		
    public function setMaxDamage(){
			if($this->firingMode ==1){
					$this->maxDamage = 26; //Shredder
			}
			else{
					$this->maxDamage = 56; //AMCannonRaking
			}
			$this->maxDamageArray[$this->firingMode] = $this->maxDamage;
		}
	
} //end of class AntimatterShredder
		
?>
