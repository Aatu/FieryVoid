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
        

        public function setSystemDataWindow($turn){

            $this->data["Weapon type"] = "Pulse";
            $this->data["Damage type"] = "Standard";
            $this->data["Grouping range"] = $this->grouping + "%";
            
            parent::setSystemDataWindow($turn);
        }
    
        
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
        
        public function getDamage($fireOrder){        return 15;   }
        public function setMinDamage(){     $this->minDamage = 15 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 15 - $this->dp;      }

    }
    
    
    class GatlingPulseCannon extends Weapon{

        public $name = "gatlingPulseCannon";
        public $displayName = "Gatling Pulse Cannon";
        public $animation = "beam";
        public $animationWidth = 4;
        public $projectilespeed = 10;
        public $animationExplosionScale = 0.15;
        public $trailLength = 10;
        public $trailColor = array(190, 75, 20);
        public $animationColor = array(190, 75, 20);
        
        public $intercept = 2;
        
        public $loadingtime = 1;
        
        public $rangePenalty = 2;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals 


        function __construct($startArc, $endArc){
            parent::__construct(0, 1, 0, $startArc, $endArc);
           
        }
        
        public function setSystemDataWindow($turn){

            $this->data["Weapon type"] = "Pulse";
            $this->data["Damage type"] = "Standard";
            
            parent::setSystemDataWindow($turn);
        }

        public function getDamage($fireOrder){        return Dice::d(6,2)+6;   }
        public function setMinDamage(){     $this->minDamage = 8 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 18 - $this->dp;      }

    }

    class MolecularPulsar extends Pulse
    {
        public $name = "molecularPulsar";
        public $displayName = "Molecular Pulsar";
        public $iconPath = "mediumPulse.png";
        public $animation = "trail";
        public $trailLength = 15;
        public $animationWidth = 4;
        public $projectilespeed = 25;
        public $animationExplosionScale = 0.17;
        public $animationColor =  array(175, 225, 175);
        public $trailColor = array(110, 225, 110);
        public $rof = 2;
        public $shots = 7;

        public $loadingtime = 1;
		public $normalload = 2;

        public $rangePenalty = 1;
        public $fireControl = array(2, 3, 4); // fighters, <mediums, <capitals

        public $grouping = 5;

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function setSystemData($data, $subsystem){
			parent::setSystemData($data, $subsystem);
            $this->setPulsarShots();
		}

        public function getDamage($fireOrder){ return 10; }
 
        public function setMinDamage()
        {
            $this->minDamage = 10 - $this->dp;
        }

        public function setMaxDamage()
        {
            $this->maxDamage = 10 - $this->dp;
        }

        private function setPulsarShots()
        {
            // Molecular pulsars can shoot after 1 turn with reduced effect.
            if ($this->turnsloaded == 1)
            {
                $shots = 3;
                $defaultShots = 3;
            }
            else
            {
                $shots = 7;
                $defaultShots = 7;
            }
        }
    }



?>
