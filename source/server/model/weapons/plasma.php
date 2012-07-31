<?php

	class Plasma extends Weapon{
	
		
	
		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
		protected function getSystemArmour($system, $gamedata, $fireOrder){
			$armor = parent::getSystemArmour($system, $gamedata, $fireOrder);
			return round($armor / 2);
		}
	
		public function setSystemDataWindow($turn){

			$this->data["Weapon type"] = "Plasma";
			$this->data["Damage type"] = "Standard";
			
			parent::setSystemDataWindow($turn);
		}
		
		public function setSystemData($data, $subsystem){
			parent::setSystemData($data, $subsystem);
			$this->setMinDamage();
			$this->setMaxDamage();
			
			
		}
		
	}

		class PlasmaAccelerator extends Plasma{

		public $name = "plasmaAccelerator";
        public $displayName = "Plasma Accelerator";
        public $animation = "trail";
        public $animationColor = array(75, 250, 90);
		public $trailColor = array(75, 250, 90);
		public $projectilespeed = 15;
        public $animationWidth = 4;
		public $animationExplosionScale = 0.20;
		public $trailLength = 30;
		public $rangeDamagePenalty = 1;
        
        public $loadingtime = 1;
		public $normalload = 3;
		
        public $rangePenalty = 1;
        public $fireControl = array(-4, 1, 3); // fighters, <=mediums, <=capitals 


		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
		

		public function getDamage($fireOrder){
		
			if ($this->turnsloaded+1 == 1){
				//print("Plasma: turnsloaded 1 \n");
				return Dice::d(10)+4;   
			}else if ($this->turnsloaded+1 == 2){
				//print("Plasma: turnsloaded 2 \n");
				return Dice::d(10, 2)+8;
			}else{
				//print("Plasma: turnsloaded 3 real: ".$this->turnsloaded." \n");
				return Dice::d(10,3)+17;
			}
			
		}
        public function setMinDamage(){
			if ($this->turnsloaded == 1){
				$this->minDamage = 5 - $this->dp;
				$this->animationExplosionScale = 0.15;
			}else if ($this->turnsloaded == 2){
				$this->animationExplosionScale = 0.25;
				$this->minDamage = 10 - $this->dp;  
			}else if ($this->turnsloaded == 3){
				$this->animationExplosionScale = 0.35;
				$this->minDamage = 20 - $this->dp;  
			}else{
				$this->minDamage = 5 - $this->dp;   
			}
		}
        public function setMaxDamage(){
			if ($this->turnsloaded == 1)
				$this->maxDamage = 14 - $this->dp ;  
			else if ($this->turnsloaded == 2)
				$this->maxDamage = 28 - $this->dp; 
			else if ($this->turnsloaded == 3)
				$this->maxDamage = 47 - $this->dp; 		    
			else
				$this->maxDamage = 47 - $this->dp;
		}

	}
	
	class MagGun extends Plasma{

		public $name = "magGun";
        public $displayName = "Mag Gun";
        public $animation = "trail";
        public $animationColor = array(255, 105, 0);
		public $trailColor = array(255, 140, 60);
		public $projectilespeed = 15;
        public $animationWidth = 6;
		public $animationExplosionScale = 0.50;
		public $trailLength = 30;
        public $flashDamage = true;
		        
        public $loadingtime = 3;
			
        public $rangePenalty = 1;
        public $fireControl = array(-20, 2, 6); // fighters, <=mediums, <=capitals 


		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
		
		public function getDamage($fireOrder){        return Dice::d(10,6)+20;   }
        public function setMinDamage(){     $this->minDamage = 26 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 80 - $this->dp;      }

	}
	
	class HeavyPlasma extends Plasma{

		public $name = "heavyPlasma";
        public $displayName = "Heavy Plasma";
        public $animation = "trail";
        public $animationColor = array(75, 250, 90);
		public $trailColor = array(75, 250, 90);
		public $projectilespeed = 15;
        public $animationWidth = 5;
		public $animationExplosionScale = 0.30;
		public $trailLength = 20;
		public $rangeDamagePenalty = 0.5;
		        
        public $loadingtime = 3;
			
        public $rangePenalty = 0.66;
        public $fireControl = array(-5, 1, 3); // fighters, <=mediums, <=capitals 


		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
		
		public function getDamage($fireOrder){        return Dice::d(10,3)+13;   }
        public function setMinDamage(){     $this->minDamage = 16 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 43 - $this->dp;      }

	}
    
    class MediumPlasma extends Plasma{

		public $name = "mediumPlasma";
        public $displayName = "Medium Plasma";
        public $animation = "trail";
        public $animationColor = array(75, 250, 90);
		public $trailColor = array(75, 250, 90);
		public $projectilespeed = 12;
        public $animationWidth = 4;
		public $animationExplosionScale = 0.20;
		public $trailLength = 15;
		public $rangeDamagePenalty = 0.5;
		        
        public $loadingtime = 3;
			
        public $rangePenalty = 1;
        public $fireControl = array(-5, 1, 3); // fighters, <=mediums, <=capitals 


		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
		
		public function getDamage($fireOrder){        return Dice::d(10,3)+4;   }
        public function setMinDamage(){     $this->minDamage = 7 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 34 - $this->dp;      }

	}

?>
