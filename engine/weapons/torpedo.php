<?php

	class Torpedo extends Weapon{
	
		public $ballistic = true;

		function __construct($armour, $maxhealth, $location, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $location, $powerReq, $startArc, $endArc);
        }

	}
	
	class IonTorpedo extends Torpedo{
	
		public $name = "ionTorpedo";
        public $displayName = "Ion Torpedo";
		public $range = 50;
		public $loadingtime = 2;
		
		public $fireControl = array(-4, 1, 3); // fighters, <mediums, <capitals 
		
		public $trailColor = array(30, 170, 255);
        public $animation = "trail";
        public $animationColor = array(30, 170, 255);
		public $animationExplosionScale = 0.25;
		public $projectilespeed = 12;
        public $animationWidth = 3;
		public $trailLength = 10;
		
		function __construct($armour, $maxhealth, $location, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $location, $powerReq, $startArc, $endArc);
        }
		
		public function getDamage(){        return 15;   }
        public function setMinDamage(){     $this->minDamage = 15 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 15 - $this->dp;      }
	
	}
	
	class BallisticTorpedo extends Torpedo{
	
		public $name = "ballisticTorpedo";
        public $displayName = "Ballistic Torpedo";
		public $range = 25;
		public $loadingtime = 1;
		public $shots = 6;
		public $defaultShots = 1;
		public $normalload = 6;
		
		function __construct($armour, $maxhealth, $location, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $location, $powerReq, $startArc, $endArc);
			
        }
		
		public function beforeTurn($ship, $turn, $phase){
			$this->shots = $this->turnsloaded;
			parent::beforeTurn($ship, $turn, $phase);
		}
		
		public function getDamage(){        return Dice::d(10,2);   }
        public function setMinDamage(){     $this->minDamage = 2 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 20 - $this->dp;      }
	
	}



?>
