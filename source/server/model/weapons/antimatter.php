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
		public $dmgEquation = '2X+5'; //to be able to automatically incorporate this into weaon description
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
		
?>
