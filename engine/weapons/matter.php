<?php

	class Matter extends Weapon{
	
		
	
		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
		protected function getSystemArmour($system, $gamedata, $fireOrder){
			return 0;
		}
		
		protected function getOverkillSystem($target, $shooter, $system){
			return null;
		}
		
		public function setSystemDataWindow($turn){

			$this->data["Weapon type"] = "Matter";
			$this->data["Damage type"] = "Standard";
			
			parent::setSystemDataWindow($turn);
		}
    
	
	
	}

		class MatterCannon extends Matter{

		public $name = "matterCannon";
        public $displayName = "Matter Cannon";
        public $animation = "trail";
        public $animationColor = array(250, 250, 190);
		public $projectilespeed = 25;
        public $animationWidth = 4;
		public $animationExplosionScale = 0.20;
        
        public $loadingtime = 2;

		
        public $rangePenalty = 0.5;
        public $fireControl = array(-2, 3, 3); // fighters, <mediums, <capitals 


		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

		public function getDamage(){        return Dice::d(10, 2)+2;   }
        public function setMinDamage(){     $this->minDamage = 4 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 22 - $this->dp;      }

	}

?>
