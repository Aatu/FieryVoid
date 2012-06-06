<?php

    class AntimatterConverter extends Weapon{
        
        public $name = "antimatterConverter";
        public $displayName = "Antimatter Converter";
        public $animation = "beam";
        public $animationColor = array(175, 225, 175);
        public $projectilespeed = 20;
        public $animationWidth = 4;
	public $animationExplosionScale = 0.20;
	public $trailLength = 20;
		        
        public $loadingtime = 3;
        public $rangePenalty = 1;
        public $fireControl = array(-6, 4, 4); // fighters, <=mediums, <=capitals 
        public $flashDamage = true;
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function damage( $target, $shooter, $fireOrder, $pos, $gamedata, $damage, $location = null){
            parent::damage( $target, $shooter, $fireOrder, $pos, $gamedata, $damage, $location = null);
        }
        
       	public function getDamage(){        return 0;   }
        public function setMinDamage(){     $this->minDamage = 0;      }
        public function setMaxDamage(){     $this->maxDamage = 0;      }
    }

?>
