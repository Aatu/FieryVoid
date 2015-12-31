<?php
class customLightMatterCannon extends Matter{
	/*Light Matter Cannon, as used on Ch'Lonas ships*/
        public $name = "customLightMatterCannon";
        public $displayName = "Light Matter Cannon";
        public $animation = "trail";
        public $animationColor = array(250, 250, 190);
        public $projectilespeed = 25;
        public $animationWidth = 3;
        public $animationExplosionScale = 0.15;
        
        public $loadingtime = 2;
		
        public $rangePenalty = 1;
        public $fireControl = array(-1, 3, 2); // fighters, <mediums, <capitals 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $override = false)
        {
		//override maxhealth and power reqirement, they are fixed; left option to override with hand-written values
		if ( $override == false ){
			$maxhealth = 5;
			$powerReq = 2;
		}
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10, 1)+4;   }
        public function setMinDamage(){     $this->minDamage = 5 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 14 - $this->dp;      }
    }
    



class customLightMatterCannonF extends Matter{
	/*fighter version of Light Matter Cannon, as used on Ch'Lonas fighters*/
	/*NOT done as linked weapon!*/

        public $name = "customLightMatterCannonF";
        public $displayName = "Light Matter Cannon";
        public $animation = "trail";

        public $animationColor = array(250, 250, 190);
        public $projectilespeed = 18;
        public $animationWidth = 2;
        public $animationExplosionScale = 0.10;
        
        public $loadingtime = 3;
        public $exclusive = false; //this is not an exclusive weapon!
        
        public $rangePenalty = 1;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals 
 


        function __construct($startArc, $endArc){
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }
        
        public function setSystemDataWindow($turn){
            $this->data["Weapon type"] = "Matter";
            $this->data["Damage type"] = "Standard";
            
            parent::setSystemDataWindow($turn);
        }
        
        public function getDamage($fireOrder){        return Dice::d(10, 1)+4;   }
        public function setMinDamage(){   return  $this->minDamage = 5 - $this->dp;      }
        public function setMaxDamage(){   return  $this->maxDamage = 14 - $this->dp;      }


    }

?>
