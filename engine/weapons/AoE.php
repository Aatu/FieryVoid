<?php

	class AoE extends Weapon{

		function __construct($armour, $maxhealth, $location, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $location, $powerReq, $startArc, $endArc);
        }

	}
	
	class EnergyMine extends AoE{
	
		public $name = "energyMine";
        public $displayName = "Energy mine";
		public $range = 50;
		public $loadingtime = 2;
		
		function __construct($armour, $maxhealth, $location, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $location, $powerReq, $startArc, $endArc);
        }
		
		public function getDamage(){        return 10;   }
        public function setMinDamage(){     $this->minDamage = 10;      }
        public function setMaxDamage(){     $this->maxDamage = 30;      }
	
	}



?>