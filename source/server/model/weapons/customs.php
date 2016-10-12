<?php
class CustomLightMatterCannon extends Matter {
    /*Light Matter Cannon, as used on Ch'Lonas ships*/
        public $name = "customLightMatterCannon";
        public $displayName = "Light Matter Cannon";
        public $animation = "trail";
        public $animationColor = array(250, 250, 190);
        public $projectilespeed = 25;
        public $animationWidth = 3;
        public $animationExplosionScale = 0.15;
        public $priority = 6;

        public $loadingtime = 2;
        
        public $rangePenalty = 1;
        public $fireControl = array(-1, 3, 2); // fighters, <mediums, <capitals 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc) {
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 5;
            if ( $powerReq == 0 ) $powerReq = 2;
                parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10, 1)+4;   }
        public function setMinDamage(){     $this->minDamage = 5 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 14 - $this->dp;      }
} //customLightMatterCannon



class CustomLightMatterCannonF extends Matter {
    /*fighter version of Light Matter Cannon, as used on Ch'Lonas fighters*/
    /*NOT done as linked weapon!*/
        public $name = "customLightMatterCannonF";
        public $displayName = "Light Matter Cannon";
        public $animation = "trail";
        public $animationColor = array(250, 250, 190);
        public $projectilespeed = 18;
        public $animationWidth = 2;
        public $animationExplosionScale = 0.10;
        public $priority = 8;
        
        public $loadingtime = 3;
        public $exclusive = false; //this is not an exclusive weapon!
        
        public $rangePenalty = 1;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals 
 
        function __construct($startArc, $endArc){
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }
        
        public function setSystemDataWindow($turn){
            $this->data["Weapon type"] = "Matter";
            $this->data["Damage type"] = "Standard";
            
            parent::setSystemDataWindow($turn);
        }
        
        public function getDamage($fireOrder){        return Dice::d(10, 1)+4;   }
        public function setMinDamage(){   return  $this->minDamage = 5 - $this->dp;      }
        public function setMaxDamage(){   return  $this->maxDamage = 14 - $this->dp;      }
}//CustomLightMatterCannonF


class CustomHeavyMatterCannon extends Matter{
    /*Heavy Matter Cannon, as used on Ch'Lonas ships*/
        public $name = "customHeavyMatterCannon";
        public $displayName = "Heavy Matter Cannon";
        public $animation = "trail";
        public $animationColor = array(250, 250, 190);
        public $projectilespeed = 25;
        public $animationWidth = 4;
        public $animationExplosionScale = 0.25;
        public $priority = 9;
      

        public $loadingtime = 3;
        
        public $rangePenalty = 0.33;
        public $fireControl = array(-3, 3, 4); // fighters, <mediums, <capitals 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
        //maxhealth and power reqirement are fixed; left option to override with hand-written values
        if ( $maxhealth == 0 ){
            $maxhealth = 10;
        }
        if ( $powerReq == 0 ){
            $powerReq = 6;
        }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10, 3)+5;   }
        public function setMinDamage(){     $this->minDamage = 8 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 35 - $this->dp;      }
} //CustomHeavyMatterCannon




class CustomPulsarLaser extends Pulse{
    /*Pulsar Laser, as used on Ch'Lonas ships*/
        public $name = "customPulsarLaser";
        public $displayName = "Pulsar Laser";
        public $animation = "laser";
        public $animationColor = array(130, 50, 200);
        public $animationWidth = 3;
        public $animationWidth2 = 0.5;
        public $uninterceptable = true;
        public $priority = 5;

        public $rof = 2;

        public $grouping = 25;
        public $maxpulses = 4;
        public $loadingtime = 3;
        
        public $rangePenalty = 0.33;
        public $fireControl = array(-1, 3, 3); // fighters, <mediums, <capitals 
        
    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
        //maxhealth and power reqirement are fixed; left option to override with hand-written values
        if ( $maxhealth == 0 ){
            $maxhealth = 8;
        }
        if ( $powerReq == 0 ){
            $powerReq = 6;
        }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
            
            parent::setSystemDataWindow($turn);
            $this->data["Pulses"] = 'D 3';
        $this->data["Weapon type"] = "Laser";
        }
        
        protected function getPulses($turn)
        {
            return Dice::d(3);
        }
        
        public function getDamage($fireOrder){        return 12;   }
        public function setMinDamage(){     $this->minDamage = 12 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 12 - $this->dp;      }
} //customPulsarLaser



class CustomStrikeLaser extends Weapon{
    /*Srike Laser, as used on Ch'Lonas ships*/
        public $name = "customStrikeLaser";
        public $displayName = "Strike Laser";
        public $animation = "laser";
        public $animationColor = array(130, 25, 200);
        public $animationWidth = 4;
        public $animationWidth2 = 0.5;
        public $uninterceptable = true;
        public $loadingtime = 3;
        public $rangePenalty = 0.5;
        public $fireControl = array(0, 2, 2); // fighters, <mediums, <capitals
        public $priority = 6;

        public function setSystemDataWindow($turn){
            $this->data["Weapon type"] = "Laser";
            $this->data["Damage type"] = "Standard";
            parent::setSystemDataWindow($turn);
        }



        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
        //maxhealth and power reqirement are fixed; left option to override with hand-written values
        if ( $maxhealth == 0 ){
            $maxhealth = 5;
        }
        if ( $powerReq == 0 ){
            $powerReq = 4;
        }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(10, 2)+8; }
        public function setMinDamage(){ $this->minDamage = 10 - $this->dp; }
        public function setMaxDamage(){ $this->maxDamage = 28 - $this->dp; }
}//CustomStrikeLaser

?>
