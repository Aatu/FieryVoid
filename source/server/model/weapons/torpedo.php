<?php

    class Torpedo extends Weapon{
    
        public $ballistic = true;
        public $damageType = "Standard"; 
        public $weaponClass = "Ballistic"; 
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        
        
        public function isInDistanceRange($shooter, $target, $fireOrder)
        {
            $movement = $shooter->getLastTurnMovement($fireOrder->turn);
            $distanceRange = max($this->range, $this->distanceRange); //just in case distanceRange is not filled!

            if(mathlib::getDistanceHex($movement->position,  $target) > $distanceRange )
            {
                $fireOrder->pubnotes .= " FIRING SHOT: Target moved out of distance range.";
                return false;
            }

            return true;
        }
       
        
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
        }

    } //endof class Torpedo


    
    class IonTorpedo extends Torpedo{
    
        public $name = "ionTorpedo";
        public $displayName = "Ion Torpedo";
        public $range = 50;
        public $loadingtime = 2;
        
        public $fireControl = array(-4, 1, 3); // fighters, <mediums, <capitals 
        
        public $trailColor = array(141, 240, 255);
        public $animation = "torpedo";
        public $animationColor = array(30, 170, 255);
        public $animationExplosionScale = 0.25;
        public $projectilespeed = 12;
        public $animationWidth = 10;
        public $trailLength = 10;
        public $priority = 6;
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return 15;   }
        public function setMinDamage(){     $this->minDamage = 15; /*- $this->dp;*/      }
        public function setMaxDamage(){     $this->maxDamage = 15 ;/*- $this->dp;*/      }
    
    }//endof class IonTorpedo


    
    class BallisticTorpedo extends Torpedo{
    
        public $name = "ballisticTorpedo";
        public $displayName = "Ballistic Torpedo";
        public $range = 25;
        public $loadingtime = 1;
        public $shots = 6;
        public $defaultShots = 1;
        public $normalload = 6;
        
        public $fireControl = array(0, 3, 4); // fighters, <mediums, <capitals 
        
        public $trailColor = array(141, 240, 255);
        public $animation = "trail";
        public $animationColor = array(227, 148, 55);
        public $animationExplosionScale = 0.25;
        public $projectilespeed = 12;
        public $animationWidth = 4;
        public $trailLength = 40;
        public $priority = 5;
        
        public $grouping = 20;
        
        public $canChangeShots = true;
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
            
        }
        
        public function firedOnTurn($turn){            
            foreach ($this->fireOrders as $fire){
                if ($fire->weaponid == $this->id && $fire->turn == $turn){
                    return  $fire->shots;
                }
            }
            return 0;
        }
        
        public function calculateLoading(TacGamedata $gamedata)
        {
            $loading = new WeaponLoading($this->turnsloaded, 0, 0, 0, $this->getLoadingTime(), $this->firingMode);
            $shotsfired = $this->firedOnTurn(TacGamedata::$currentTurn);
            if (TacGamedata::$currentPhase == 2)
            {
                if  
                ( $this->isOfflineOnTurn(TacGamedata::$currentTurn) )
                {
                    $loading = new WeaponLoading(0, 0, 0, 0, $this->getLoadingTime(), $this->firingMode);
                }
                else if ($shotsfired)
                {
                    $newloading = $this->turnsloaded-$shotsfired;
                    if ($newloading < 0)
                        $newloading = 0;

                $loading = new WeaponLoading($newloading, 0, 0, 0, $this->getLoadingTime(), $this->firingMode);
                }
            }
            else if (TacGamedata::$currentPhase == 1)
            {
                $newloading = $this->turnsloaded+1;
                if ($newloading > $this->getNormalLoad())
                    $newloading = $this->getNormalLoad();

                $loading = new WeaponLoading($newloading, 0, 0, 0, $this->getLoadingTime(), $this->firingMode);
            }

            return $loading;
        }
        
        public function onConstructed($ship, $turn, $phase){
            parent::onConstructed($ship, $turn, $phase);
            $this->shots = $this->turnsloaded;
        }
        
        public function getDamage($fireOrder){        return Dice::d(10,2);   }
        public function setMinDamage(){     $this->minDamage = 2; /*- $this->dp;*/      }
        public function setMaxDamage(){     $this->maxDamage = 20; /*- $this->dp;*/      }
    
    } //endof class BallisticTorpedo



    class PlasmaWaveTorpedo extends Torpedo{
        public $name = "PlasmaWaveTorpedo";
        public $displayName = "Plasma Wave";
        public $iconPath = "plasmaWaveTorpedo.png";
        public $range = 30;
        public $loadingtime = 3;
        
        public $weaponClass = "Plasma"; //deals Plasma, not Ballistic, damage. Should be Ballistic(Plasma), but I had to choose ;)
        public $damageType = "Flash"; 
        
        
        public $fireControl = array(null, 0, 2); // fighters, <mediums, <capitals 
        
        public $trailColor = array(75, 230, 90);
        public $animation = "torpedo";
        public $animationColor = array(75, 230, 90);
        public $animationExplosionScale = 0.3;
        public $projectilespeed = 11;
        public $animationWidth = 10;
        public $trailLength = 10;
        public $priority = 1; //Flash! should strike first (?)
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 7;
            }
            if ( $powerReq == 0 ){
                $powerReq = 4;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
	    //ignores half armor (as a Plasma weapon should!)
	protected function getSystemArmourStandard($target, $system, $gamedata, $fireOrder, $pos=null){
		$armour = parent::getSystemArmourStandard($target, $system, $gamedata, $fireOrder, $pos);
		if (is_numeric($armour)){
		$toIgnore = ceil($armour /2);
		$new = $armour - $toIgnore;
		return $new;
		}
		else {
		return 0;
		}
	    }
    	
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		$this->data["<font color='red'>Remark</font>"] = "<br>Ignores half of armor.";
	}
        
        
        public function getDamage($fireOrder){        return Dice::d(10, 3);   }
        public function setMinDamage(){     $this->minDamage = 3;      }
        public function setMaxDamage(){     $this->maxDamage = 30;      }
    
    }//endof class PlasmaWaveTorpedo


?>
