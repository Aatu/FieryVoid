<?php

    class AntimatterConverter extends Weapon{
        
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
                return $damage = 2 + 4*round(($fireOrder->needed - $fireOrder->rolled)/5);
            }

        public function setMinDamage(){     $this->minDamage = 2;      }
        public function setMaxDamage(){     $this->maxDamage = 82;      }
    }

class AntiprotonGun extends Weapon{
        
        public $name = "AntiprotonGun";
        public $displayName = "Antiproton Gun";
		public $iconPath = "AntiprotonGun.png";
        public $animation = "beam";
//        public $animationColor = array(175, 225, 175);
        public $animationColor = array(26, 240, 112);
        public $projectilespeed = 10;
        public $animationWidth = 1;
        public $animationExplosionScale = 0.20;
        public $trailLength = 5;
        public $priority = 3;
		public $specialRangeCalculation = true; //to inform front end that it should use weapon-specific range penalty calculation - such a method should be present in .js!

        public $intercept = 2;
        public $loadingtime = 1;
		public $rangePenalty = 1;        

		public $damageType = "Standard";
    	public $weaponClass = "Antimatter"; 
		public $firingMode = "Standard";

        public $fireControl = array(2, 3, 3); // fighters, <mediums, <capitals 


	    //override standard to skip first 5 hexes when calculating range penalty
	    public function calculateRangePenalty(OffsetCoordinate $pos, BaseShip $target)
	    {
			$targetPos = $target->getHexPos();
			$dis = mathlib::getDistanceHex($pos, $targetPos);
			$dis = max(0,$dis-5);//first 5 hexes are "free"

			if ($dis >= 1 && $dis < 6) {
				$rangePenalty = ($this->rangePenalty * $dis);
			$notes = "shooter: " . $pos->q . "," . $pos->r . " target: " . $targetPos->q . "," . $targetPos->r . " dis: $dis, rangePenalty: $rangePenalty";
			return Array("rp" => $rangePenalty, "notes" => $notes);
		}
			if ($dis >= 6 ) {
				$rangePenalty = (($this->rangePenalty * $dis) + (($this->rangePenalty * $dis) - 5));
			$notes = "shooter: " . $pos->q . "," . $pos->r . " target: " . $targetPos->q . "," . $targetPos->r . " dis: $dis, rangePenalty: $rangePenalty";
			return Array("rp" => $rangePenalty, "notes" => $notes);
	   			 }	    
	}
		
		
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            $this->data["Special"] = "Damage is dependent on how good a hit is - it's not randomized (actual damage done is 1X+12).<br>The maximum X is 10 for 22 damage.";
			$this->data["Special"] .= "This weapon suffers the following range penalties:"; 
			$this->data["Special"] .= "0 from 0-5 hexes"; 
			$this->data["Special"] .= "-1 per hex from 6-10 hexes";
			$this->data["Special"] .= "-2 per hex at 11+ hexes.";
        }
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
			if ( $maxhealth == 0 ) $maxhealth = 4;
            if ( $powerReq == 0 ) $powerReq = 4;            
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
       	public function getDamage($fireOrder){
                $X = round(($fireOrder->needed - $fireOrder->rolled)/5);
				if ($X > 10) $X = 10;
				return $damage = ($X) + 12;
            }

        public function setMinDamage(){     $this->minDamage = 12;      }
        public function setMaxDamage(){     $this->maxDamage = 22;      }
}
?>
