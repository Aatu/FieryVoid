<?php

	class Plasma extends Weapon{

        public $priority = 8;

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
		protected function getSystemArmour($system, $gamedata, $fireOrder){
			$armor = parent::getSystemArmour($system, $gamedata, $fireOrder);


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

			$this->data["Weapon type"] = "Plasma";
            $this->data["Damage type"] = "Standard";
            $this->data["<font color='red'>Remark</font>"] = "Does less damage over distance (.5 to 1 per hex)";
			
			parent::setSystemDataWindow($turn);
		}
		
		public function setSystemData($data, $subsystem){
			parent::setSystemData($data, $subsystem);
			$this->setMinDamage();
			$this->setMaxDamage();
		}
		
	}

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
                    return Dice::d(10,3)+17;
            }
	}
        
        public function setMinDamage(){
            switch($this->turnsloaded){
                case 0:
                case 1:
                    $this->minDamage = 5 - $this->dp;
                    $this->animationExplosionScale = 0.15;
                    break;
                case 2:
                    $this->animationExplosionScale = 0.25;
                    $this->minDamage = 10 - $this->dp;  
                    break;
                case 3:
                default:
                    $this->animationExplosionScale = 0.35;
                    $this->minDamage = 20 - $this->dp;  
                    break;
            }

/*            if ($this->turnsloaded == 1){
				$this->minDamage = 5 - $this->dp;
				$this->animationExplosionScale = 0.15;
			}else if ($this->turnsloaded == 2){
				$this->animationExplosionScale = 0.25;
				$this->minDamage = 10 - $this->dp;  
			}else if ($this->turnsloaded == 3){
				$this->animationExplosionScale = 0.35;
				$this->minDamage = 20 - $this->dp;  
			}else{
				$this->minDamage = 5 - $this->dp;   
			}*/
		}
                
        public function setMaxDamage(){
            switch($this->turnsloaded){
                case 0:
                case 1:
                    $this->maxDamage = 14 - $this->dp;
                    break;
                case 2:
                    $this->maxDamage = 28 - $this->dp;  
                    break;
                case 3:
                default:
                    $this->maxDamage = 47 - $this->dp;  
                    break;
            }
/*			if ($this->turnsloaded == 1)
				$this->maxDamage = 14 - $this->dp ;  
			else if ($this->turnsloaded == 2)
				$this->maxDamage = 28 - $this->dp; 
			else if ($this->turnsloaded == 3)
				$this->maxDamage = 47 - $this->dp; 		    
			else
				$this->maxDamage = 47 - $this->dp;
*/		}

	}
	
	class MagGun extends Plasma{

		public $name = "magGun";
        public $displayName = "Mag Gun";
        public $animation = "trail";
        public $animationColor = array(255, 105, 0);
		public $trailColor = array(255, 140, 60);
		public $projectilespeed = 15;
        public $animationWidth = 6;
		public $animationExplosionScale = 0.50;
		public $trailLength = 30;
        public $flashDamage = true;
        public $priority = 1;
		        
        public $loadingtime = 3;
			
        public $rangePenalty = 1;
        public $fireControl = array(null, 2, 6); // fighters, <=mediums, <=capitals 


		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
		
		public function getDamage($fireOrder){        return Dice::d(10,6)+20;   }
        public function setMinDamage(){     $this->minDamage = 26 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 80 - $this->dp;      }

	}
	
	class HeavyPlasma extends Plasma{

    	public $name = "heavyPlasma";
        public $displayName = "Heavy Plasma";
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
		
		
    	public function getDamage($fireOrder){        return Dice::d(10,3)+13;   }
        public function setMinDamage(){     $this->minDamage = 16 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 43 - $this->dp;      }

	}
    
    class MediumPlasma extends Plasma{

    	public $name = "mediumPlasma";
        public $displayName = "Medium Plasma";
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
        public function setMinDamage(){     $this->minDamage = 7 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 34 - $this->dp;      }

	}

        class LightPlasma extends Plasma{

    	public $name = "lightPlasma";
        public $displayName = "Light Plasma";
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
        public function setMinDamage(){     $this->minDamage = 4 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 22 - $this->dp;      }

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
        public function setMinDamage(){     $this->minDamage = 12 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 30 - $this->dp;      }

        public function fire($gamedata, $fireOrder){
            // If fired, a Plasma Torch might overheat and go in shutdown for 2 turns.
            // Make a crit roll taking into account any damage already sustained.
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
            
            parent::fire($gamedata, $fireOrder);
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



        function __construct($startArc, $endArc, $damagebonus, $shots = 2){
            $this->shots = $shots;
            $this->defaultShots = $shots;
            
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }


        protected function getSystemArmour($system, $gamedata, $fireOrder){
            $armor = parent::getSystemArmour($system, $gamedata, $fireOrder);
            if (is_numeric($armor))
                return round($armor / 2);
            
            return 0;
        }
    
        public function setSystemDataWindow($turn){

            $this->data["Weapon type"] = "Plasma";
            $this->data["Damage type"] = "Standard";
            $this->data["<font color='red'>Remark</font>"] = "Does less damage over distance (-1 per hex)";
            
            parent::setSystemDataWindow($turn);
        }


        public function getDamage($fireOrder){        return Dice::d(3)+2;   }
        public function setMinDamage(){     $this->minDamage = 3 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 5 - $this->dp;      }

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

        public function setSystemDataWindow($turn){

            $this->data["Weapon type"] = "Plasma";
            $this->data["Damage type"] = "Standard";
            $this->data["<font color='red'>Remark</font>"] = "Does less damage over distance (-1 per hex)";
            
            parent::setSystemDataWindow($turn);
        }
        

        protected function getSystemArmour($system, $gamedata, $fireOrder){
            $armor = parent::getSystemArmour($system, $gamedata, $fireOrder);
            if (is_numeric($armor))
                return round($armor / 2);
            
            return 0;
        }
        
        
        public function getDamage($fireOrder){        return Dice::d(3)+6;   }
        public function setMinDamage(){     $this->minDamage = 4 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 9 - $this->dp;      }

    }



?>
