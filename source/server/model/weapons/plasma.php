<?php

class Plasma extends Weapon{
	public $priority = 6;
	public $damageType = "Standard"; 
	public $weaponClass = "Plasma"; 

	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
	    parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
		
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
			//$this->data["Weapon type"] = "Plasma";
            		//$this->data["Damage type"] = "Standard";
			parent::setSystemDataWindow($turn);
            		$this->data["<font color='red'>Remark</font>"] = "Does less damage over distance (".$this->rangeDamagePenalty." per hex)";
			$this->data["<font color='red'>Remark</font>"] .= "<br>Ignores half of armor.";
		}
		
		public function setSystemData($data, $subsystem){
			parent::setSystemData($data, $subsystem);
			$this->setMinDamage();
			$this->setMaxDamage();
		}
		
} //endof class Plasma



    class PlasmaAccelerator extends Plasma{

		public $name = "plasmaAccelerator";
        public $displayName = "Plasma Accelerator";
        public $animation = "trail";
        public $animationColor = array(75, 250, 90);
		public $trailColor = array(75, 250, 90);
		public $projectilespeed = 15;
        public $animationWidth = 4;
		public $animationExplosionScale = 0.20;
		public $trailLength = 30;
		public $rangeDamagePenalty = 1;
        
        public $loadingtime = 1;
		public $normalload = 3;
		
        public $rangePenalty = 1;
        public $fireControl = array(-4, 1, 3); // fighters, <=mediums, <=capitals 


		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
		

	public function getDamage($fireOrder){
            switch($this->turnsloaded){
                case 0: 
                case 1:
                    return Dice::d(10)+4;
                case 2:
                    return Dice::d(10, 2)+8;
                case 3:
                default:
                    return Dice::d(10,4)+12;
            }
	}
        
        public function setMinDamage(){
            switch($this->turnsloaded){
                case 0:
                case 1:
                    $this->minDamage = 5 /* - $this->dp*/;
                    $this->animationExplosionScale = 0.15;
                    break;
                case 2:
                    $this->animationExplosionScale = 0.25;
                    $this->minDamage = 10 /*- $this->dp*/;  
                    break;
                case 3:
                default:
                    $this->animationExplosionScale = 0.35;
                    $this->minDamage = 16 /*- $this->dp*/;  
                    break;
            }
	}
                
        public function setMaxDamage(){
            switch($this->turnsloaded){
                case 0:
                case 1:
                    $this->maxDamage = 14 /*- $this->dp*/;
                    break;
                case 2:
                    $this->maxDamage = 28 /*- $this->dp*/;  
                    break;
                case 3:
                default:
                    $this->maxDamage = 52 /*- $this->dp*/;  
                    break;
            }
	}

}//endof class PlasmaAccelerator


	
class MagGun extends Plasma{

	public $name = "magGun";
        public $displayName = "Mag Gun";
        public $animation = "trail";
        public $animationColor = array(255, 105, 0);
	public $trailColor = array(255, 140, 60);
	public $projectilespeed = 15;
        public $animationWidth = 6;
	public $animationExplosionScale = 0.70;
	public $trailLength = 30;
        public $priority = 2;
		        
        public $loadingtime = 3;
			
        public $rangePenalty = 1;
        public $fireControl = array(null, 2, 6); // fighters, <=mediums, <=capitals 

	public $damageType = "Flash"; 
	public $weaponClass = "Plasma"; 
	public $firingModes = array( 1 => "Flash"); 

	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
		public function setSystemDataWindow($turn){
			//$this->data["Weapon type"] = "Plasma";
            		//$this->data["Damage type"] = "Standard";
			parent::setSystemDataWindow($turn);
			//$this->data["<font color='red'>Remark</font>"] = "<br>Ignores half of armor.";
		}
	
		
	public function getDamage($fireOrder){        return Dice::d(10,6)+20;   }
        public function setMinDamage(){     $this->minDamage = 26 /*- $this->dp*/;      }
        public function setMaxDamage(){     $this->maxDamage = 80 /*- $this->dp*/;      }
}
	
	
	
class HeavyPlasma extends Plasma{
    	public $name = "heavyPlasma";
        public $displayName = "Heavy Plasma Cannon";
        public $animation = "trail";
        public $animationColor = array(75, 250, 90);
    	public $trailColor = array(75, 250, 90);
    	public $projectilespeed = 15;
        public $animationWidth = 5;
    	public $animationExplosionScale = 0.30;
    	public $trailLength = 20;
    	public $rangeDamagePenalty = 0.5;
    		        
        public $loadingtime = 3;
			
        public $rangePenalty = 0.66;
        public $fireControl = array(-5, 1, 3); // fighters, <=mediums, <=capitals 


    	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
		
    	public function getDamage($fireOrder){        return Dice::d(10,4)+8;   }
        public function setMinDamage(){     $this->minDamage = 12 /*- $this->dp*/;      }
        public function setMaxDamage(){     $this->maxDamage = 48 /*- $this->dp*/;      }

}
    
    
    
class MediumPlasma extends Plasma{
    	public $name = "mediumPlasma";
        public $displayName = "Medium Plasma Cannon";
        public $animation = "trail";
        public $animationColor = array(75, 250, 90);
    	public $trailColor = array(75, 250, 90);
    	public $projectilespeed = 13;
        public $animationWidth = 4;
    	public $animationExplosionScale = 0.25;
    	public $trailLength = 16;
    	public $rangeDamagePenalty = 0.5;
		        
        public $loadingtime = 3;
			
        public $rangePenalty = 1;
        public $fireControl = array(-5, 1, 3); // fighters, <=mediums, <=capitals 


    	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
		
    	public function getDamage($fireOrder){        return Dice::d(10,3)+4;   }
        public function setMinDamage(){     $this->minDamage = 7 /*- $this->dp*/;      }
        public function setMaxDamage(){     $this->maxDamage = 34 /*- $this->dp*/;      }

}



class LightPlasma extends Plasma{

    	public $name = "lightPlasma";
        public $displayName = "Light Plasma Cannon";
        public $animation = "trail";
        public $animationColor = array(75, 250, 90);
    	public $trailColor = array(75, 250, 90);
    	public $projectilespeed = 11;
        public $animationWidth = 3;
    	public $trailLength = 12;
        public $animationExplosionScale = 0.20;
    	public $rangeDamagePenalty = 0.5;
		        
        public $loadingtime = 2;
			
        public $rangePenalty = 1;
        public $fireControl = array(-5, 1, 3); // fighters, <=mediums, <=capitals 


    	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
		
    	public function getDamage($fireOrder){        return Dice::d(10,2)+2;   }
        public function setMinDamage(){     $this->minDamage = 4 /*- $this->dp*/;      }
        public function setMaxDamage(){     $this->maxDamage = 22 /*- $this->dp*/;      }

}



class PlasmaTorch extends Plasma{

    	public $name = "plasmaTorch";
        public $displayName = "Plasma Torch";
        public $animation = "trail";
        public $animationColor = array(75, 250, 90);
    	public $trailColor = array(75, 250, 90);
    	public $projectilespeed = 15;
        public $animationWidth = 5;
    	public $animationExplosionScale = 0.4;
    	public $trailLength = 10;
    	public $rangeDamagePenalty = 1;
		        
        public $loadingtime = 1;
			
        public $rangePenalty = 2;
        public $fireControl = array(null, 0, 2); // fighters, <=mediums, <=capitals 


    	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
		
    	public function getDamage($fireOrder){        return Dice::d(10,2)+10;   }
        public function setMinDamage(){     $this->minDamage = 12 ;      }
        public function setMaxDamage(){     $this->maxDamage = 30 ;      }

		public function setSystemDataWindow($turn){
			//$this->data["Weapon type"] = "Plasma";
            		//$this->data["Damage type"] = "Standard";
			parent::setSystemDataWindow($turn);
			$this->data["<font color='red'>Remark</font>"] .= "<br>If fired, Plasma Torch may overheat.";
		}
	
        public function fire($gamedata, $fireOrder){
            // If fired, a Plasma Torch might overheat and go in shutdown for 2 turns.
            // Make a crit roll taking into account any damage already sustained.
            parent::fire($gamedata, $fireOrder);
		
            $roll = Dice::d(20) + $this->getTotalDamage();
            
            if($roll >= 16){
                // It has overheated.
                $crit = new ForcedOfflineOneTurn(-1, $fireOrder->shooterid, $this->id, "ForcedOfflineOneTurn", $gamedata->turn);
                $crit->updated = true;
                $this->criticals[] =  $crit;
                $crit = new ForcedOfflineOneTurn(-1, $fireOrder->shooterid, $this->id, "ForcedOfflineOneTurn", $gamedata->turn+1);
                $crit->updated = true;
                $this->criticals[] =  $crit;
            }
		
        }
    }



class PairedPlasmaBlaster extends LinkedWeapon{
        public $name = "pairedPlasmaBlaster";
        public $displayName = "Paired Plasma Blaster";
        public $animation = "trail";
        public $animationColor = array(75, 250, 90);
        public $trailColor = array(75, 250, 90);
        public $projectilespeed = 12;
        public $animationWidth = 2;
        public $trailLength = 10;
        public $animationExplosionScale = 0.1;

        public $intercept = 2;

        public $loadingtime = 1;
        public $shots = 2;
        public $defaultShots = 2;

        public $rangePenalty = 2;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals
        public $rangeDamagePenalty = 1;

    	public $damageType = "Standard"; 
    	public $weaponClass = "Plasma"; 

        function __construct($startArc, $endArc, $damagebonus, $shots = 2){
            $this->shots = $shots;
            $this->defaultShots = $shots;
            
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }


        protected function getSystemArmourStandard($target, $system, $gamedata, $fireOrder, $pos=null){
            $armor = parent::getSystemArmourStandard($target, $system, $gamedata, $fireOrder, $pos);

            if (is_numeric($armor)){
                $toIgnore = ceil($armor /2);
                $new = $armor - $toIgnore;
                return $new;
            }
            else {
                return 0;
            }
        }
        
    
        public function setSystemDataWindow($turn){
/*
            $this->data["Weapon type"] = "Plasma";
            $this->data["Damage type"] = "Standard";
            $this->data["<font color='red'>Remark</font>"] = "Does less damage over distance (-1 per hex)";
  */          
            parent::setSystemDataWindow($turn);
            		$this->data["<font color='red'>Remark</font>"] = "Does less damage over distance (".$this->rangeDamagePenalty." per hex)";
			$this->data["<font color='red'>Remark</font>"] .= "<br>Ignores half of armor.";
        }


        public function getDamage($fireOrder){        return Dice::d(3)+2;   }
        public function setMinDamage(){     $this->minDamage = 3 ;      }
        public function setMaxDamage(){     $this->maxDamage = 5 ;      }

    }


class PlasmaGun extends Plasma{
        public $name = "plasmaGun";
        public $displayName = "Plasma Gun";
        public $animation = "trail";
        public $animationColor = array(75, 250, 90);
        public $trailColor = array(75, 250, 90);
        public $projectilespeed = 11;
        public $animationWidth = 4;
        public $trailLength = 12;
        public $animationExplosionScale = 0.25;
        public $rangeDamagePenalty = 1;
                
        public $loadingtime = 2;
        public $exlusive = true;
            
        public $rangePenalty = 0.66;
        public $fireControl = array(-6, 4, 4); // fighters, <=mediums, <=capitals 


        function __construct($startArc, $endArc, $damagebonus, $shots = 1){
            $this->shots = $shots;
            $this->defaultShots = $shots;
            
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }   

        
        public function getDamage($fireOrder){        return Dice::d(3)+6;   }
        public function setMinDamage(){     $this->minDamage = 4 ;      }
        public function setMaxDamage(){     $this->maxDamage = 9 ;      }

    }



class RogolonLtPlasmaGun extends LinkedWeapon{
	/*weapon of Rogolon fighters - very nasty!*/
        public $name = "RogolonLtPlasmaGun";
        public $displayName = "Light Plasma Gun";
	public $iconPath = "plasmaGun.png";
	
        public $animation = "trail";
        public $animationColor = array(75, 250, 90);
        public $trailColor = array(75, 250, 90);
        public $projectilespeed = 11;
        public $animationWidth = 4;
        public $trailLength = 12;
        public $animationExplosionScale = 0.25;


        public $intercept = 0; //no interception for this weapon!
        public $loadingtime = 1;
        public $shots = 2;
        public $defaultShots = 2;
        public $rangePenalty = 2;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals
        public $rangeDamagePenalty = 1;

    	public $damageType = "Standard"; 
    	public $weaponClass = "Plasma"; 

        function __construct($startArc, $endArc, $shots = 2){
            $this->shots = $shots;
            $this->defaultShots = $shots;
            
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }

	/*Plasma - armor ignoring*/
        protected function getSystemArmourStandard($target, $system, $gamedata, $fireOrder, $pos=null){
            $armor = parent::getSystemArmourStandard($target, $system, $gamedata, $fireOrder, $pos);
            if (is_numeric($armor)){
                $toIgnore = ceil($armor /2);
                $new = $armor - $toIgnore;
                return $new;
            }
            else {
                return 0;
            }
        }
        
    
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            		$this->data["Remark"] = "Does less damage over distance (".$this->rangeDamagePenalty." per hex)";
			$this->data["Remark"] .= "<br>Ignores half of armor.";
        }


        public function getDamage($fireOrder){        return Dice::d(3)+5;   }
        public function setMinDamage(){     $this->minDamage = 6 ;      }
        public function setMaxDamage(){     $this->maxDamage = 8 ;      }

    }



?>
