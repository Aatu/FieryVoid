<?php
	class PlasmaStream extends Raking{

		public $name = "plasmaStream";
        public $displayName = "Plasma Stream";
        public $animation = "trail";
        public $animationColor = array(75, 250, 90);
		public $trailColor = array(75, 250, 90);
		public $projectilespeed = 15;
        public $animationWidth = 6;
		public $animationExplosionScale = 0.25;
		public $trailLength = 30;
		        
		public $raking = 5;
        public $loadingtime = 2;
        
		public $rangeDamagePenalty = 1;	
        public $rangePenalty = 1;
        public $fireControl = array(-4, 2, 2); // fighters, <=mediums, <=capitals 


		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        protected function getSystemArmour($system){
			return round($system->armour / 2);
		}
	
		public function setSystemDataWindow($turn){

			$this->data["Weapon type"] = "Plasma";
						
			parent::setSystemDataWindow($turn);
		}
		
		protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata){
			$crit = new ArmorReduced(-1, $ship->id, $system->id, "ArmorReduced", $gamedata->turn);
			$crit->updated = true;
            $system->criticals[] =  $crit;
			parent::onDamagedSystem($ship, $system, $damage, $armour, $gamedata);
		}
		
		
		public function getDamage(){        return Dice::d(10,3)+4;   }
        public function setMinDamage(){     $this->minDamage = 7 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 34 - $this->dp;      }

	}
	
	class BurstBeam extends Weapon{

		public $name = "burstBeam";
        public $displayName = "Burst Beam";
        public $animation = "laser";
        public $animationColor = array(158, 240, 255);
		public $trailColor = array(158, 240, 255);
		public $projectilespeed = 15;
        public $animationWidth = 2;
		public $animationExplosionScale = 0.10;
		public $trailLength = 30;
		        
	    public $loadingtime = 1;
        
			
        public $rangePenalty = 2;
        public $fireControl = array(2, 2, 4); // fighters, <=mediums, <=capitals 


		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
       
		public function setSystemDataWindow($turn){

			$this->data["Weapon type"] = "Electromagnetic";
						
			parent::setSystemDataWindow($turn);
		}
		
		protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata){
			$crit = null;
			
			if ($system instanceof Structure){
				$reactor = $ship->getSystemByName("Reactor");
				$crit = new OutputReduced1(-1, $ship->id, $reactor->id, "OutputReduced1", $gamedata->turn);
				$crit->updated = true;
				$reactor->criticals[] =  $crit;
			}else if ($system->powerReq > 0){
				$crit = new ForcedOfflineOneTurn(-1, $ship->id, $system->id, "ForcedOfflineOneTurn", $gamedata->turn);
				$crit->updated = true;
				$system->criticals[] =  $crit;
			
			}
			
			parent::onDamagedSystem($ship, $system, $damage, $armour, $gamedata);
		}
		
		
		public function getDamage(){        return 0;   }
        public function setMinDamage(){     $this->minDamage = 0;      }
        public function setMaxDamage(){     $this->maxDamage = 0;      }

	}
?>
