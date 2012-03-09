<?php

	class Pulse extends Weapon{
	
		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
		
		public $trailColor = array(190, 75, 20);
		public $animationColor = array(190, 75, 20);
		public $grouping = 5;
		public $shots = 6;
		public $defaultShots = 6;
		public $rof = 1;
		
		protected function getPulses(){}
		
		
		/*
		public function damage($target, $shooter, $fireOrder){
		
            $extra = ($fireOrder->needed - $fireOrder->rolled) % ($this->grouping*5);
			$pulses = $this->getPulses() + $extra;
			if ($pulses > $this->maxpulses)
				$pulses = $this->maxpulses;
								
			for ($i=0;$i<$pulses;$i++){
			
				if ($target->isDestroyed())
					return;
			
               $system = $target->getHitSystem($shooter, $fireOrder->turn);
				
                if ($system == null)
					return;
					
				$this->doDamage($target, $shooter, $system, $this->getFinalDamage($shooter, $target), $fireOrder);
			
			}
            
                  
        }
		*/
		public function setSystemDataWindow($turn){

			$this->data["Weapon type"] = "Pulse";
			$this->data["Damage type"] = "Standard";
			$this->data["Grouping range"] = $this->grouping + "%";
			
			parent::setSystemDataWindow($turn);
		}
	
	
	}

	class LightPulse extends Pulse{

		public $name = "lightPulse";
        public $displayName = "Light Pulse Cannon";
        public $animation = "trail";
        public $animationWidth = 3;
		public $projectilespeed = 10;
		public $animationExplosionScale = 0.15;
		public $rof = 2;
		public $trailLength = 10;
        
        public $loadingtime = 1;
        
        public $rangePenalty = 2;
        public $fireControl = array(4, 3, 3); // fighters, <mediums, <capitals 
		
		public $grouping = 5;
		
		public $intercept = 2;
		
		
		
		

		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
		protected function getPulses(){ return Dice::d(5); }

		public function getDamage(){        return 8;   }
        public function setMinDamage(){     $this->minDamage = 8 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 8 - $this->dp;      }

	}
	
	class MediumPulse extends Pulse{

		public $name = "mediumPulse";
        public $displayName = "Medium Pulse Cannon";
        public $animation = "trail";
        public $trailLength = 15;
        public $animationWidth = 4;
		public $projectilespeed = 15;
		public $animationExplosionScale = 0.17;
		public $rof = 2;
        
        public $loadingtime = 3;
        
        public $rangePenalty = 1;
        public $fireControl = array(1, 3, 4); // fighters, <mediums, <capitals 
		
		public $grouping = 5;
		
		
		
		

		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
		protected function getPulses(){ return Dice::d(5); }

		public function getDamage(){        return 10;   }
        public function setMinDamage(){     $this->minDamage = 10 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 10 - $this->dp;      }

	}
	
	class HeavyPulse extends Pulse{

		public $name = "heavyPulse";
        public $displayName = "Heavy Pulse Cannon";
        public $animation = "trail";
        public $trailLength = 20;
        public $animationWidth = 5;
		public $projectilespeed = 20;
		public $animationExplosionScale = 0.20;
		public $rof = 2;
        
        public $loadingtime = 3;
        
        public $rangePenalty = 0.5;
        public $fireControl = array(-1, 3, 4); // fighters, <mediums, <capitals 
		
		public $grouping = 5;
		
		
		
		

		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
		protected function getPulses(){ return Dice::d(5); }

		public function getDamage(){        return 15;   }
        public function setMinDamage(){     $this->minDamage = 15 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 15 - $this->dp;      }

	}



?>
