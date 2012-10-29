<?php

    class Pulse extends Weapon{
    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        
        public $trailColor = array(190, 75, 20);
        public $animationColor = array(190, 75, 20);
        public $grouping = 20;
        public $rof = 1;
        public $maxpulses = 6;
        

        public function setSystemDataWindow($turn){

            $this->data["Weapon type"] = "Pulse";
            $this->data["Damage type"] = "Standard";
            $this->data["Grouping range"] = $this->grouping + "%";
            $this->data["Max pulses"] = $this->maxpulses;
            $this->data["Pulses"] = '1-5';
            
            parent::setSystemDataWindow($turn);
        }
        
        protected function getPulses()
        {
            return Dice::d(5);
        }
        

        public function fire($gamedata, $fireOrder){

            $shooter = $gamedata->getShipById($fireOrder->shooterid);
            $target = $gamedata->getShipById($fireOrder->targetid);
            $this->firingMode = $fireOrder->firingMode;

            $pos = $shooter->getCoPos();

            $this->calculateHit($gamedata, $fireOrder);
            $intercept = $this->getIntercept($gamedata, $fireOrder);
            $pulses = $this->getPulses(); 

            $fireOrder->notes .= " pulses: $pulses";
            $fireOrder->shots = $this->maxpulses;
            $needed = $fireOrder->needed;
            $rolled = Dice::d(100);
            if ($rolled > $needed && $rolled <= $needed+($intercept*5)){
                //$fireOrder->pubnotes .= "Shot intercepted. ";
                $fireOrder->intercepted += $pulses;
            }

            if ($rolled <= $needed){
                $extra = $this->getExtraPulses($needed, $rolled);
                $fireOrder->notes .= " extra pulses: $extra";
                $pulses += $extra;
                if ($pulses > $this->maxpulses)
                    $pulses = $this->maxpulses;

                $fireOrder->shotshit = $pulses;
                for ($i=0;$i<$pulses;$i++){
                    $this->beforeDamage($target, $shooter, $fireOrder, $pos, $gamedata);
                }
            }

            $fireOrder->rolled = 1;//Marks that fire order has been handled
        }
    
        protected function getExtraPulses($needed, $rolled)
        {
            return floor(($needed - $rolled) / ($this->grouping));
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
        public $maxpulses = 7;
        public $grouping = 15;

        public $loadingtime = 1;
		public $normalload = 2;

        public $rangePenalty = 1;
        public $fireControl = array(2, 3, 4); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function setSystemData($data, $subsystem){
			parent::setSystemData($data, $subsystem);
            if ($this->turnsloaded == 1)
            {
                $this->maxpulses = 3;
            }
            else
            {
                $this->maxpulses = 7;
            }
		}
        
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            if ($this->turnsloaded == 1)
            {
                $this->data["Pulses"] = '1-3';
                $this->data["Grouping range"] = 'NONE';
            }
            else
            {
                $this->data["Pulses"] = '1-5';
            }
        }
        
        protected function getExtraPulses($needed, $rolled)
        {
            if ($this->turnsloaded == 1)
            {
                return 0;
            }
            else
            {
                return parent::getExtraPulses($needed, $rolled);
            }
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
        
        protected function getPulses()
        {
            if ($this->turnsloaded == 1)
            {
                return Dice::d(3);
            }
            else
            {
                return Dice::d(5);
            }
            
        }
    }
?>
