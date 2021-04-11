<?php

	//common  functionality of Antimatter weapons
	class AntimatterWeapon extends Weapon{   
        public $animation = "beam";     
		public $animationColor = array(26, 240, 112);
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
        public $animationColor = array(26, 240, 112);
        public $projectilespeed = 10;
        public $animationWidth = 1;
        public $animationExplosionScale = 0.20;
        public $trailLength = 5;
		*/
        public $priority = 6; //that's Standard Heavy hit!

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
	} //endof AntiprotonGun
	
	
?>
