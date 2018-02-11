<?php

    class Matter extends Weapon
    {	    
        public $noOverkill = true;//Matter weapons do not overkill
    	public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    	public $weaponClass = "Matter"; //MANDATORY (first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!  
	    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        protected function getSystemArmourStandard($target, $system, $gamedata, $fireOrder, $pos=null){
            return 0; //Matter ignores armor!
        }

	/*no need due to noOverkill trait
        protected function getOverkillSystem($target, $shooter, $system, $pos, $fireOrder, $gamedata)
        {
            return null;
        }
	*/

        public function setSystemDataWindow($turn)
        {
            //$this->data["Weapon type"] = "Matter";
            //$this->data["Damage type"] = "Standard";
            parent::setSystemDataWindow($turn);
	    $this->data["Special"] = "Ignores armor.";
        }

        public $priority = 9;
    } //endof class Matter




    class MatterCannon extends Matter
    {
        public $name = "matterCannon";
        public $displayName = "Matter Cannon";
        public $animation = "trail";
        public $animationColor = array(250, 250, 190);
        public $projectilespeed = 25;
        public $animationWidth = 4;
        public $animationExplosionScale = 0.20;
        
        public $loadingtime = 2;
		
        public $rangePenalty = 0.5;
        public $fireControl = array(-2, 3, 3); // fighters, <mediums, <capitals 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10, 2)+2;   }
        public function setMinDamage(){     $this->minDamage = 4 ;      }
        public function setMaxDamage(){     $this->maxDamage = 22 ;      }
    }
    

    class HeavyRailGun extends Matter
    {
        public $name = "heavyRailGun";
        public $displayName = "Heavy Railgun";
        public $animation = "trail";
        public $iconPath = "matterCannon.png";
        public $animationColor = array(250, 250, 190);
        public $projectilespeed = 20;
        public $animationWidth = 6;
        public $animationExplosionScale = 0.30;

        public $loadingtime = 4;

        public $rangePenalty = 0.33;
        public $fireControl = array(-3, 2, 2); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10, 5)+7;   }
        public function setMinDamage(){     $this->minDamage = 12 ;      }
        public function setMaxDamage(){     $this->maxDamage = 57 ;      }    
    }
    

    class RailGun extends Matter
    {
        public $name = "railGun";
        public $displayName = "Railgun";
        public $animation = "trail";
        public $iconPath = "matterCannon.png";
        public $animationColor = array(250, 250, 190);
        public $projectilespeed = 25;
        public $animationWidth = 5;
        public $animationExplosionScale = 0.25;
        
        public $loadingtime = 3;
		
        public $rangePenalty = 0.5;
        public $fireControl = array(-3, 2, 2); // fighters, <mediums, <capitals
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){  return Dice::d(10, 3)+3;   }
        public function setMinDamage(){     $this->minDamage = 6 ;      }
        public function setMaxDamage(){     $this->maxDamage = 33 ;      }    
    }
    

    class LightRailGun extends Matter
    {
        public $name = "lightRailGun";
        public $displayName = "Light Railgun";
        public $animation = "trail";
        public $iconPath = "matterCannon.png";
        public $animationColor = array(250, 250, 190);
        public $projectilespeed = 30;
        public $animationWidth = 4;
        public $animationExplosionScale = 0.20;
        public $priority = 6;
        
        public $loadingtime = 2;
		
        public $rangePenalty = 1.0;
        public $fireControl = array(3, 2, 0); // fighters, <mediums, <capitals
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
    	public function getDamage($fireOrder){        return Dice::d(10, 1)+5;   }
        public function setMinDamage(){     $this->minDamage = 6 ;      }
        public function setMaxDamage(){     $this->maxDamage = 15 ;      }    
    }
    


    class MassDriver extends Matter
    {
        public $name = "massDriver";
        public $displayName = "Mass Driver";
        public $animation = "trail";
        public $animationColor = array(250, 250, 190);
        public $projectilespeed = 10;
        public $animationWidth = 6;
        public $animationExplosionScale = 0.90;
	public $noInterceptDegradation = true;
        //public $targetImmobile = true;
        
        public $loadingtime = 4;
		
        public $rangePenalty = 0.17;
        public $fireControl = array(null, null, 2); // fighters, <mediums, <capitals 

	    public function setSystemDataWindow($turn){
	      $this->data["<font color='red'>Remark</font>"] = "Weapon misses automatically except vs speed 0 Enormous units. "     
	       ."Weapon misses automatically if launching unit speed is > 0. "  
	       ."Weapon always hits Structure. ";   
	      parent::setSystemDataWindow($turn);
	    }	    

	    
	public function calculateHitBase($gamedata, $fireOrder){ //auto-miss if restrictions not met
		$canHit = true;
		$pubnotes = '';
		
		$shooter = $gamedata->getShipById($fireOrder->shooterid);
		$target = $gamedata->getShipById($fireOrder->targetid);
		
		if(!$target->Enormous){	$canHit=false; $pubnotes.= ' Target is not Enormous. '; }
		if($target->getSpeed()>0){ $canHit=false; $pubnotes.= ' Target speed >0. '; }
		if($shooter->getSpeed()>0){ $canHit=false; $pubnotes.= ' Shooter speed >0. '; }
			
		if($canHit){
			parent::calculateHitBase($gamedata, $fireOrder);
		}else{ //accurate targeting with this weapon not possible!
			$fireOrder->needed = 0;
        		$fireOrder->notes = 'ACCURATE FIRING CRITERIA NOT MET';
			$fireOrder->pubnotes .= $pubnotes;   
        		$fireOrder->updated = true;
		}
	}
	    
	    
	public function damage($target, $shooter, $fireOrder, $gamedata, $damage){ //always hit Structure...
		if ($target->isDestroyed()) return;
		$tmpLocation = $fireOrder->chosenLocation;	

		$system = $target->getStructureSystem($tmpLocation);
		if ($system == null || $system->isDestroyed()) $system = $target->getStructureSystem(0);//facing Structure nonexistent, go to PRIMARY
		if ($system == null || $system->isDestroyed()) return; //PRIMARY Structure nonexistent also
		$this->doDamage($target, $shooter, $system, $damage, $fireOrder, null, $gamedata, $tmpLocation);
	}	    
	    
	    /* Marcin Sawicki, October 2017 - fire() and calculateHit() functions changed (new versions above)
	public function damage($target, $shooter, $fireOrder, $gamedata, $damage){ //always hit Structure...
		if ($target->isDestroyed()) return;
		$tmpLocation = $target->getHitSection($shooter, $fireOrder->turn);

		$system = $target->getStructureSystem($tmpLocation);
		if ($system == null || $system->isDestroyed()) $system = $target->getStructureSystem(0);//facing Structure nonexistent, go to PRIMARY
		if ($system == null || $system->isDestroyed()) return; //PRIMARY Structure nonexistent also
		$this->doDamage($target, $shooter, $system, $damage, $fireOrder, null, $gamedata, $tmpLocation);
	}
	    
	public function calculateHit($gamedata, $fireOrder){ //auto-miss if restrictions not met
		$canHit = true;
		$pubnotes = '';
		
		$shooter = $gamedata->getShipById($fireOrder->shooterid);
		$target = $gamedata->getShipById($fireOrder->targetid);
		
		if(!$target->Enormous){	$canHit=false; $pubnotes.= ' Target is not Enormous. '; }
		if($target->getSpeed()>0){ $canHit=false; $pubnotes.= ' Target speed >0. '; }
		if($shooter->getSpeed()>0){ $canHit=false; $pubnotes.= ' Shooter speed >0. '; }
			
		if($canHit){
			parent::calculateHit($gamedata, $fireOrder);
		}else{ //accurate targeting with this weapon not possible!
			$fireOrder->needed = 0;
        		$fireOrder->notes = 'ACCURATE FIRING CRITERIA NOT MET';
			$fireOrder->pubnotes .= $pubnotes;   
        		$fireOrder->updated = true;
		}
	}
	    */
	    
	    
	    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10, 8)+ 60;   }
        public function setMinDamage(){     $this->minDamage = 68 ;      }
        public function setMaxDamage(){     $this->maxDamage = 140 ;      }
    } //end of Mass Driver


    class GaussCannon extends MatterCannon
    {
        public $name = "gaussCannon";
        public $displayName = "Gauss Cannon";
        public $animation = "trail";
        public $animationColor = array(250, 250, 190);
        public $projectilespeed = 28;
        public $animationWidth = 4;
        public $animationExplosionScale = 0.20;
        public $trailLength = 8;
        
        public $loadingtime = 2;
        
        public $rangePenalty = 1;
        public $fireControl = array(-3, 1, 2); // fighters, <mediums, <capitals 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10, 1)+10;   }
        public function setMinDamage(){     $this->minDamage = 11 ;      }
        public function setMaxDamage(){     $this->maxDamage = 20 ;      }
    }


    class HeavyGaussCannon extends GaussCannon{

        public $name = "heavyGaussCannon";
        public $displayName = "Heavy Gauss Cannon";
        public $animation = "trail";
        public $animationColor = array(250, 250, 190);
        public $projectilespeed = 25;
        public $animationWidth = 5;
        public $animationExplosionScale = 0.30;
        public $trailLength = 12;
        
        public $loadingtime = 3;
        
        public $rangePenalty = 0.66;
        public $fireControl = array(-2, 2, 3); // fighters, <mediums, <capitals 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10, 3)+10;   }
        public function setMinDamage(){     $this->minDamage = 13;      }
        public function setMaxDamage(){     $this->maxDamage = 40 ;      }

        }


    class RapidGatling extends Matter{
        public $name = "rapidGatling";
        public $displayName = "Rapid Gatling Railgun";
        public $animation = "trail";
        public $trailColor = array(225, 255, 150);
        public $animationColor = array(225, 225, 150);
        public $projectilespeed = 16;
        public $animationWidth = 2;
        public $trailLength = 40;
        public $animationExplosionScale = 0.15;
        public $guns = 2;
        public $intercept = 1;
        public $loadingtime = 1;
        public $ballisticIntercept = true;
        public $priority = 6;
        
        public $rangePenalty = 2;
        public $fireControl = array(4, 2, 0); // fighters, <mediums, <capitals 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(6, 2);   }
        public function setMinDamage(){     $this->minDamage = 2 ;      }
        public function setMaxDamage(){     $this->maxDamage = 12 ;      }
    }
    

    class OrieniGatlingRG extends RapidGatling{
    	/*RapidGatling's predecessor*/ 
        public $displayName = "Gatling Railgun";
    	public $guns = 1;
    	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        public function getDamage($fireOrder){        return Dice::d(6, 2);   }
        public function setMinDamage(){     $this->minDamage = 2 ;      }
        public function setMaxDamage(){     $this->maxDamage = 12 ;      }
    }

    
    class PairedGatlingGun extends LinkedWeapon{
        public $name = "pairedGatlingGun";
        public $displayName = "Paired Gatling Guns";
        public $animation = "trail";
        public $animationColor = array(250, 250, 190);
        public $projectilespeed = 24;
        public $animationWidth = 2;
        public $trailLength = 12;
        public $animationExplosionScale = 0.15;
        public $shots = 2;
        public $defaultShots = 2;
        public $ammunition = 6;
	    
        
        public $loadingtime = 1;

        public $intercept = 2;
        public $ballisticIntercept = true;

        public $rangePenalty = 2;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals
        private $damagebonus = 0;
	    
	public $noOverkill = true;
	    
	public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    	public $weaponClass = "Matter"; //MANDATORY (first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!  
	 

        function __construct($startArc, $endArc){
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }
        
	    
        public function setSystemDataWindow($turn){
            //$this->data["Weapon type"] = "Matter";
            //$this->data["Damage type"] = "Standard";
            parent::setSystemDataWindow($turn);
            $this->data["Special"] = "Ignores armor.";
            $this->data["Ammunition"] = $this->ammunition;
        }


        protected function getSystemArmourStandard($target, $system, $gamedata, $fireOrder, $pos=null){
            return 0; //Matter ignores armor!
        }


        public function getDamage($fireOrder){
            $dmg = Dice::d(6, 1);
            return $dmg;
       }

        public function setAmmo($firingMode, $amount){
            $this->ammunition = $amount;
        }


       public function fire($gamedata, $fireOrder){ //note ammo usage
        	//debug::log("fire function");
            parent::fire($gamedata, $fireOrder);
            $this->ammunition--;
            Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, $gamedata->id, $this->firingMode, $this->ammunition, $gamedata->turn);
        }
    
        public function setMinDamage(){     $this->minDamage = 1;      }
        public function setMaxDamage(){     $this->maxDamage = 6;      }

    }


   class MatterGun extends PairedGatlingGun{
	   /*Belt Alliance fighter weapon, with limited ammo - poorer cousin of Orieni fighter weapon*/
        public $name = "MatterGun";
        public $displayName = "Matter Gun";  
	    public $iconPath = 'pairedGatlingGun.png';
	  
        public function getDamage($fireOrder){ //d6-1, minimum 1
            $dmg = Dice::d(6, 1) -1;
	    $dmg = max(1,$dmg);
            return $dmg;
       }
	public function setMinDamage(){     $this->minDamage = 1;      }
        public function setMaxDamage(){     $this->maxDamage = 5;      }
    }



?>
