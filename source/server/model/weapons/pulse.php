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

        public function getDamage($fireOrder){        return 8;   }
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
        
        public $loadingtime = 2;
        
        public $rangePenalty = 1;
        public $fireControl = array(1, 3, 4); // fighters, <mediums, <capitals 
        
        public $grouping = 5;
        
        public $intercept = 2;
        
        

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        protected function getPulses(){ return Dice::d(5); }

        public function getDamage($fireOrder){        return 10;   }
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

        public function getDamage($fireOrder){        return 15;   }
        public function setMinDamage(){     $this->minDamage = 15 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 15 - $this->dp;      }

    }

    class MolecularPulsar extends Pulse
    {
        public $name = "molecularPulsar";
        public $displayName = "Molecular Pulsar";
        public $animation = "trail";
        public $trailLength = 15;
        public $animationWidth = 5;
        public $projectilespeed = 25;
        public $animationExplosionScale = 0.25;
        public $animationColor = array(175, 225, 175);
        public $rof = 2;
        public $shots = 6;

        public $loadingtime = 1;

        public $rangePenalty = 1;
        public $fireControl = array(2, 3, 4); // fighters, <mediums, <capitals

        public $grouping = 5;

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function fire($gamedata, $fireOrder)
        {
            setPulsarShots();

            parent::fire($gamedata, $fireOrder);
        }

        protected function getPulses()
        {
            return Dice::d(5);
        }

        public function getDamage($fireOrder){ return 10; }

        public function setMinDamage()
        {
            setPulsarShots();
            $this->minDamage = 10 - $this->dp;
        }

        public function setMaxDamage()
        {
            setPulsarShots();
            $this->maxDamage = 10 - $this->dp;
        }

        private function setPulsarShots()
        {
            // Molecular pulsars can shoot after 1 turn with reduced effect.
            if ($this->turnsloaded == 0)
            {
                $shots = 3;
                $defaultShots = 3;
            }
            else
            {
                $shots = 7;
                $defaultShots = 6;
            }
        }
    }



?>
