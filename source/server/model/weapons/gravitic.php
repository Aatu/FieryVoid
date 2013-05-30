<?php

    class Gravitic extends Weapon{
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){

            $this->data["Weapon type"] = "Molecular";
            $this->data["Damage type"] = "Standard";

            parent::setSystemDataWindow($turn);
        }
    }
    
    class GravitonPulsar extends Pulse
    {
        public $name = "gravitonPulsar";
        public $displayName = "Graviton Pulsar";
        public $animation = "trail";
        public $trailColor = array(99, 255, 00);
        public $animationColor = array(99, 255, 00);
        public $projectilespeed = 12;
        public $animationWidth = 3;
        public $animationExplosionScale = 0.15;
        public $boostable = true;
        public $boostEfficiency = 2;
        public $maxBoostLevel = 2;
        public $loadingtime = 1;
        public $maxpulses = 3;
		
        public $rangePenalty = 1;
        public $fireControl = array(4, 2, 2); // fighters, <mediums, <capitals 
        public $intercept = 1;
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
            // Keep this consistent with the gravitic.js implementation.
            // Yeah, I know: dirty.
            $this->data["Weapon type"] = "Pulse";
            $this->data["Damage type"] = "Standard";
            $this->data["Grouping range"] = $this->grouping + "%";
            $this->data["Max pulses"] = $this->getMaxPulses($turn);
            $this->data["REMARK"] = "Max. power might cause<br> crits on this system";
            $this->defaultShots = $this->getMaxPulses($turn);
        
            switch($this->getBoostLevel($turn)){
                case 0:
                    $this->data["Pulses"] = '1-2';
                case 1:
                    $this->data["Pulses"] = '1-3';
                case 2:
                    $this->data["Pulses"] = '1-3';
            }            
            
            parent::setSystemDataWindow($turn);
        }
        
        protected function getPulses($turn)
        {
            switch($this->getBoostLevel($turn)){
                case 0:
                    return Dice::d(2);
                case 1:
                    return (Dice::d(3)+1);
                case 2:
                    return (Dice::d(3)+2);
            }            
        }

        public function getIntercept($gamedata, $fireOrder){
            $this->intercept = $this->getInterceptRating($gamedata->turn);
            
            parent::getIntercept($gamedata, $fireOrder);
        }
        
        public function fire($gamedata, $fireOrder){
            $this->maxpulses = $this->getMaxPulses($gamedata->turn);
            $this->loadingtime = $this->getLoadingTime();
            
            $crits = array();
            
            /* If fully boosted: test for possible crit. */
            if($this->getBoostLevel($gamedata->turn) === $this->maxBoostLevel){
                Debug::log("Graviton Pulsar: Fully boosted");
                $shooter = $gamedata->getShipById($fireOrder->shooterid);
                $crits = $this->testCritical($shooter, $gamedata->turn, $crits);
                $this->setCriticals($crits, $gamedata->turn);
            }
            
            parent::fire($gamedata, $fireOrder);
        }

        public function getNormalLoad(){
            return $this->loadingtime + $this->maxBoostLevel;
        }
        
        private function getMaxPulses($turn){
            return 3 + $this->getBoostLevel($turn);            
        }

        private function getInterceptRating($turn){
            return 1 + $this->getBoostLevel($turn);            
        }
        
        private function getBoostLevel($turn){
            $boostLevel = 0;
            
            foreach($this->power as $i){
                if($i->turn === $turn){
                    $boostLevel += $i->amount;
                }
            }
            
            return $boostLevel;
        }
        
        public function getDamage($fireOrder){        return 10;   }
        public function setMinDamage(){     $this->minDamage = 10 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 10 - $this->dp;      }
    }
    
class GraviticBolt extends Gravitic
    {
        public $name = "graviticBolt";
        public $displayName = "Gravitic Bolt";
        public $animation = "trail";
        public $trailColor = array(99, 255, 00);
        public $animationColor = array(99, 255, 00);
        public $projectilespeed = 12;
        public $animationWidth = 3;
        public $animationExplosionScale = 0.20;
        public $boostable = true;
        public $boostEfficiency = 2;
        public $maxBoostLevel = 2;
        public $loadingtime = 1;
		
        public $rangePenalty = 1;
        public $fireControl = array(4, 2, 2); // fighters, <mediums, <capitals 
        public $intercept = 1;
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
            // Keep this consistent with the gravitic.js implementation.
            // Yeah, I know: dirty.
            $this->data["Weapon type"] = "Gravitic";
            $this->data["Damage type"] = "Standard";
        
            switch($this->getBoostLevel($turn)){
                case 0:
                    $this->damage = 9;
                    $this->data["Damage"] = '9';
                case 1:
                    $this->damage = 12;
                    $this->data["Damage"] = '12';
                case 2:
                    $this->damage = 15;
                    $this->data["Damage"] = '15';
            }            
            
            parent::setSystemDataWindow($turn);
        }
        
        private function getBoostLevel($turn){
            $boostLevel = 0;
            
            foreach($this->power as $i){
                if($i->turn === $turn){
                    $boostLevel += $i->amount;
                }
            }
            
            return $boostLevel;
        }
        
/*        private function getBoostedDamage(){
           switch($this->getBoostLevel($turn)){
                case 0:
                    return 9;
                case 1:
                    return 12;
                case 2:
                    return 15;
            }                        
        }
  */      
        public function getDamage($fireOrder){        return $this->damage;   }
        public function setMinDamage(){     $this->minDamage = $this->damage; - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = $this->damage; - $this->dp;      }
    }
    
    class GravitonBeam extends Raking{
        public $name = "gravitonBeam";
        public $displayName = "Graviton Beam";
        public $animation = "laser";
        public $animationColor = array(99, 255, 00);
        public $animationWidth = 4;
        
        public $loadingtime = 4;
        public $damageType = "raking";
        public $raking = 10;
        
        public $rangePenalty = 0.25;
        public $fireControl = array(-5, 2, 3); // fighters, <mediums, <capitals 
    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){

            $this->data["Weapon type"] = "Gravitic";
            $this->data["Damage type"] = "Raking";
            
            parent::setSystemDataWindow($turn);
        }
        
        public function getDamage($fireOrder){        return Dice::d(10, 5)+12;   }
        public function setMinDamage(){     $this->minDamage = 17 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 62 - $this->dp;      }
    }

?>
