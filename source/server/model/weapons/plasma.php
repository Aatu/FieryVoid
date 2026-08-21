<?php

class Plasma extends Weapon{
	public $priority = 6;
	public $damageType = "Standard"; 
	public $weaponClass = "Plasma"; 
        public $animation = "bolt";
        public $animationColor = array(75, 250, 90);

	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
	    parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
	}
	
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		$this->data["Special"] = "Does less damage over distance (".$this->rangeDamagePenalty." per hex).";
		$this->data["Special"] .= "<br>Ignores half of armor.";
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
	    /*
        public $animation = "trail";
        public $animationColor = array(75, 250, 90);
		public $trailColor = array(75, 250, 90);
		public $projectilespeed = 15;
        public $animationWidth = 4;
		public $animationExplosionScale = 0.40;
		public $trailLength = 30;
		*/
		public $rangeDamagePenalty = 1;
        
        public $loadingtime = 1;
		public $normalload = 3;
		
        public $rangePenalty = 1;
        public $fireControl = array(-4, 1, 3); // fighters, <=mediums, <=capitals 


	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
	    
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);   
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}	    		
		$this->data["Special"] .= "Can fire accelerated for less damage";  
		$this->data["Special"] .= "<br> - 1 turn: 1d10+4"; 
		$this->data["Special"] .= "<br> - 2 turns: 2d10+8"; 
		$this->data["Special"] .= "<br> - 3 turns (full): 4d10+12"; 
	}

	public function getDamage($fireOrder){
            switch($this->turnsloaded){
                case 0:
                case 1:
                    return Dice::d(10)+4;
			    	break;
                case 2:
                    return Dice::d(10, 2)+8;
			    	break;
                default:
                    return Dice::d(10,4)+12;
			    	break;
            }
	}
        
        public function setMinDamage(){
            switch($this->turnsloaded){
                case 1:
                    $this->minDamage = 5 ;
                    break;
                case 2:
                    $this->minDamage = 10 ;  
                    break;
                default:
                    $this->minDamage = 16 ;  
                    break;
            }
		}
             
        public function setMaxDamage(){
            switch($this->turnsloaded){
                case 1:
                    $this->maxDamage = 14 ;
                    break;
                case 2:
                    $this->maxDamage = 28 ;  
                    break;
                default:
                    $this->maxDamage = 52 ;  
                    break;
            }
		}

		public function stripForJson(){
			$strippedSystem = parent::stripForJson();
			$strippedSystem->data = $this->data;
			$strippedSystem->minDamage = $this->minDamage;
			$strippedSystem->minDamageArray = $this->minDamageArray;
			$strippedSystem->maxDamage = $this->maxDamage;
			$strippedSystem->maxDamageArray = $this->maxDamageArray;				
			return $strippedSystem;
		}

}//endof class PlasmaAccelerator

	
class MagGun extends Plasma{
	public $name = "magGun";
	public $displayName = "Mag Gun";
	public $animation = "bolt";
	public $animationColor = array(255, 105, 0);
	/*
	public $trailColor = array(255, 140, 60);
	public $projectilespeed = 10;
	public $animationWidth = 6;
	public $animationExplosionScale = 0.90;
	public $trailLength = 30;
	*/
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
		parent::setSystemDataWindow($turn);
	}
		
	public function getDamage($fireOrder){   
		return Dice::d(10,8)+10;   
	}
	public function setMinDamage(){     $this->minDamage = 18;      }
	public function setMaxDamage(){     $this->maxDamage = 90;      }
}//endof class MagGun
	
	
	
class HeavyPlasma extends Plasma{
    	public $name = "heavyPlasma";
        public $displayName = "Heavy Plasma Cannon";
	/*
        public $animation = "trail";
        public $animationColor = array(75, 250, 90);
    	public $trailColor = array(75, 250, 90);
    	public $projectilespeed = 15;
        public $animationWidth = 5;
    	public $animationExplosionScale = 0.30;
    	public $trailLength = 20;
	*/
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
	/*
        public $animation = "trail";
        public $animationColor = array(75, 250, 90);
    	public $trailColor = array(75, 250, 90);
    	public $projectilespeed = 13;
        public $animationWidth = 4;
    	public $animationExplosionScale = 0.25;
    	public $trailLength = 16;
	*/
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
	/*
        public $animation = "trail";
        public $animationColor = array(75, 250, 90);
    	public $trailColor = array(75, 250, 90);
    	public $projectilespeed = 11;
        public $animationWidth = 3;
    	public $trailLength = 12;
        public $animationExplosionScale = 0.20;
    	*/
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
    	public $iconPath = "plasmaTorch.png";
	public $name = "PlasmaTorch";
        public $displayName = "Plasma Torch";
	/*
        public $animation = "trail";
        public $animationColor = array(75, 250, 90);
    	public $trailColor = array(75, 250, 90);
    	public $projectilespeed = 15;
        public $animationWidth = 5;
    	public $animationExplosionScale = 0.4;
    	public $trailLength = 10;
    	*/
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
			parent::setSystemDataWindow($turn);
			if (!isset($this->data["Special"])) {
				$this->data["Special"] = '';
			}else{
				$this->data["Special"] .= '<br>';
			}			
			$this->data["Special"] .= "If fired, Plasma Torch may overheat.";
		}
	
        public function fire($gamedata, $fireOrder){
            // If fired, a Plasma Torch might overheat and go in shutdown for 2 turns.
            // Make a crit roll taking into account any damage already sustained.
            parent::fire($gamedata, $fireOrder);
		
            $roll = Dice::d(20) + $this->getTotalDamage();
            
            if($roll >= 16){ // It has overheated.
				$finalTurn = $gamedata->turn + 2;
				$crit = new ForcedOfflineForTurns(-1, $this->unit->id, $this->id, "ForcedOfflineForTurns", $gamedata->turn, $finalTurn);
				$crit->updated = true;
				$this->criticals[] =  $crit;
			/*remake!
                $crit = new ForcedOfflineOneTurn(-1, $fireOrder->shooterid, $this->id, "ForcedOfflineOneTurn", $gamedata->turn);
                $crit->updated = true;
                $this->criticals[] =  $crit;
                $crit = new ForcedOfflineOneTurn(-1, $fireOrder->shooterid, $this->id, "ForcedOfflineOneTurn", $gamedata->turn+1);
                $crit->updated = true;
		$crit->newCrit = true; //force save even if crit is not for current turn
                $this->criticals[] =  $crit;
				*/
            }
		
        }
	
    } //end of PlasmaTorch



class PairedPlasmaBlaster extends LinkedWeapon{
	public $name = "pairedPlasmaBlaster";
	public $displayName = "Plasma Blaster"; //it's not 'paired' in any way, except being usually mounted twin linked - like most fighter weapons...
    public $animationColor = array(75, 250, 90); //...it's not inheriting from Plasma, so needs to have proper color declared

	public $intercept = 2; //Default intercept for 2 paired fighter weapons
	public $priority = 4; //eqivalent of d6+3, on account of armor piercing properties of Plasma

	public $loadingtime = 1;
	public $shots = 2;
	public $defaultShots = 2;

	public $rangePenalty = 2;
	public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals
	public $rangeDamagePenalty = 1;

	public $damageType = "Standard"; 
	public $weaponClass = "Plasma"; 

	
	function __construct($startArc, $endArc, $nrOfShots = 2){ 
		$this->shots = $nrOfShots;
		$this->defaultShots = $nrOfShots;
//		$this->intercept = $nrOfShots; //Changed June 2024 nothing in TT rules to say this can intercept. DK
		
		if($nrOfShots === 1){
			$this->iconPath = "pairedPlasmaBlaster1.png";
			$this->intercept = 1;
		}
		if($nrOfShots >2){//no special icon for more than 3 linked weapons
			$this->iconPath = "pairedPlasmaBlaster3.png";
			$this->intercept = $nrOfShots;
		}
		
		parent::__construct(0, 1, 0, $startArc, $endArc);
	}
	
	public function setSystemDataWindow($turn){    
		parent::setSystemDataWindow($turn);
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
		$this->data["Special"] .= "Does less damage over distance (".$this->rangeDamagePenalty." per hex)";
		$this->data["Special"] .= "<br>Ignores half of armor."; //handled by standard routines for Plasma weapons now
	}

	public function getDamage($fireOrder){        return Dice::d(3)+2;   }
	public function setMinDamage(){     $this->minDamage = 3 ;      }
	public function setMaxDamage(){     $this->maxDamage = 5 ;      }
}


class PlasmaGun extends Plasma{
    public $name = "plasmaGun";
    public $displayName = "Plasma Gun";
	public $rangeDamagePenalty = 1;
    public $loadingtime = 2;
    public $exlusive = true;
	public $priority = 5;
    public $rangePenalty = 1.5; //-3/2 hexes
    public $fireControl = array(-6, 4, 4); // fighters, <=mediums, <=capitals 


        function __construct($startArc, $endArc, $shots = 1){
            $this->shots = $shots;
            $this->defaultShots = $shots;
            
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }   
        
        public function getDamage($fireOrder){        return Dice::d(3)+6;   }
        public function setMinDamage(){     $this->minDamage = 7 ;      }
        public function setMaxDamage(){     $this->maxDamage = 9 ;      }

    }



class RogolonLtPlasmaGun extends LinkedWeapon{
	/*weapon of Rogolon fighters - very nasty!*/
    public $name = "RogolonLtPlasmaGun";
    public $displayName = "Light Plasma Gun";
    public $animationColor = array(75, 250, 90); //...it's not inheriting from Plasma, so needs to have proper color declared	
    public $loadingtime = 1;
    public $shots = 2;
    public $defaultShots = 2;
    public $rangePenalty = 2;
    public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals
    public $rangeDamagePenalty = 1;
	public $damageBonus = 5;
	public $priority = 5;
	public $intercept = 2; //Default intercept for 2 paired fighter weapons	

    	public $damageType = "Standard"; 
    	public $weaponClass = "Plasma"; 

	function __construct($startArc, $endArc, $damageBonus=5, $nrOfShots = 2){
            $this->shots = $nrOfShots;
            $this->defaultShots = $nrOfShots;
	    	$this->damageBonus = $damageBonus;
            //$this->intercept = $nrOfShots;	//Not for this weapon.
	    
        if($nrOfShots === 1){
			$this->iconPath = "lightPlasma.png";
			$this->intercept = 1;
		}
		if($nrOfShots >= 2){
			$this->iconPath = "lightPlasmalinked.png";
			$this->intercept = $nrOfShots;
		}    
           
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }      

        public function setSystemDataWindow($turn){    
            parent::setSystemDataWindow($turn);
			if (!isset($this->data["Special"])) {
				$this->data["Special"] = '';
			}else{
				$this->data["Special"] .= '<br>';
			}
			$this->data["Special"] .= "Does less damage over distance (".$this->rangeDamagePenalty." per hex)";
			$this->data["Special"] .= "<br>Ignores half of armor."; //handled by standard routines for Plasma weapons now
        }


        public function getDamage($fireOrder){        return Dice::d(3) +$this->damageBonus;   }
        public function setMinDamage(){     $this->minDamage = 1 +$this->damageBonus ;      }
        public function setMaxDamage(){     $this->maxDamage = 3 +$this->damageBonus ;      }
    }


class RogolonLtPlasmaCannon extends LinkedWeapon{
	/*dedicated anti-ship weapon of advanced Rogolon fighters - very nasty! (and custom, thankfully)*/
    public $name = "RogolonLtPlasmaCannon";
    public $displayName = "Light Plasma Cannon";
	public $iconPath = "mediumPlasma.png";
    public $animationColor = array(75, 250, 90); //...it's not inheriting from Plasma, so needs to have proper color declared

    public $intercept = 0; //no interception for this weapon!
    public $loadingtime = 2;
    public $shots = 1;
    public $defaultShots = 1;
    public $rangePenalty = 1;
    public $fireControl = array(-5, 0, 0); // fighters, <mediums, <capitals
    public $rangeDamagePenalty = 0.5; //-1/2 hexes!
	public $priority = 6;

    public $damageType = "Standard"; 
    public $weaponClass = "Plasma"; 

    function __construct($startArc, $endArc, $shots = 1){
            $this->shots = $shots;
            $this->defaultShots = $shots;
            
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){    
            parent::setSystemDataWindow($turn);
			if (!isset($this->data["Special"])) {
				$this->data["Special"] = '';
			}else{
				$this->data["Special"] .= '<br>';
			}
			$this->data["Special"] .= "Does less damage over distance (".$this->rangeDamagePenalty." per hex)";
			$this->data["Special"] .= "<br>Ignores half of armor."; //handled by standard routines for Plasma weapons now
        }


        public function getDamage($fireOrder){        return Dice::d(10,2)+2;   }
        public function setMinDamage(){     $this->minDamage = 4 ;      }
        public function setMaxDamage(){     $this->maxDamage = 22 ;      }
    }



class LightPlasmaAccelerator extends LinkedWeapon{		
		public $name = "LightPlasmaAccelerator";
		public $displayName = "Light Plasma Accelerator";
		public $iconPath = "LightPlasmaAccelerator.png";
    public $animationColor = array(75, 250, 90); //...it's not inheriting from Plasma, so needs to have proper color declared
	
        public $intercept = 2;
		public $priority = 5;
        public $priorityArray = array(1=>5, 2=>6); //even standard shot can deal high damage due to being Plasma; charged shot is devastating
        
        public $loadingtime = 1;
		public $normalload = 2;
        public $loadingtimeArray = array(1=>1, 2=>2);
		public $shots = 2;
		public $defaultShots = 2;
		
		public $firingModes = array(1 =>'Standard', 2=>'Charged');
		public $rangePenalty = 2; //-2/hex in both modes
        public $fireControlArray = array(1=>array(0, 0, 0), 2=>array(null, 0, 0) );
		public $rangeDamagePenalty = 1; //-1/hex damage penalty
		        
		public $damageType = "Standard";
		public $weaponClass = "Plasma";  

		function __construct($startArc, $endArc, $nrOfShots = 2){
			$this->defaultShots = $nrOfShots;
			$this->shots = $nrOfShots;
			$this->intercept = $nrOfShots;
			parent::__construct(0, 1, 0, $startArc, $endArc);
		}	


		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);   
			$this->data["Special"] = "If not fired for one turn, can fire a charged shot:";  
			$this->data["Special"] .= "<br> - Standard: 1d6+3"; 
			$this->data["Special"] .= "<br> - Charged (alternate mode!): 2d6+6, cannot target fighters"; 
			$this->data["Special"] .= "<br>Damage penalty: -1/hex.";  
			$this->data["Special"] .= "<br>REMINDER: as an Accelerator weapon, it will not be used for interception unless specifically ordered to do so!"; 
		}
		
	
		public function getDamage($fireOrder){
        	switch($this->firingMode){ 
            	case 1:
                	return Dice::d(6)+3;
			    			break;
            	case 2:
            	   	return Dice::d(6,2)+6;
			    			break;
        	}
		}

		public function setMinDamage(){
				switch($this->firingMode){
						case 1:
								$this->minDamage = 4;
								break;
						case 2:
								$this->minDamage = 8;
								break;
				}
				$this->minDamageArray[$this->firingMode] = $this->minDamage;
		}							
		
		public function setMaxDamage(){
				switch($this->firingMode){
						case 1:
								$this->maxDamage = 9;
								break;
						case 2:
								$this->maxDamage = 18;
								break;
				}
				$this->maxDamageArray[$this->firingMode] = $this->maxDamage;
		}
		
		public function stripForJson(){
			$strippedSystem = parent::stripForJson();
			$strippedSystem->data = $this->data;
			$strippedSystem->minDamage = $this->minDamage;
			$strippedSystem->minDamageArray = $this->minDamageArray;
			$strippedSystem->maxDamage = $this->maxDamage;
			$strippedSystem->maxDamageArray = $this->maxDamageArray;				
			return $strippedSystem;
		}	
		
}//end of class LightPlasmaAccelerator



class HeavyPlasmaBolter extends Plasma{
	public $name = "heavyPlasmaBolter";
	public $displayName = "Heavy Plasma Bolter";
	public $iconPath = "HeavyPlasmaBolter.png";

	public $priority = 6;
	
	public $rangeDamagePenalty = 0;
	public $rangeDamagePenaltyPBolter = 0.33;
	public $loadingtime = 3;
	public $rangePenalty = 0.33;
	public $fireControl = array(-4, 2, 3);


		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
				if ( $maxhealth == 0 ) $maxhealth = 10;
				if ( $powerReq == 0 ) $powerReq = 5;
				parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
		}

	protected function getDamageMod($damage, $shooter, $target, $pos, $gamedata)
	{
		parent::getDamageMod($damage, $shooter, $target, $pos, $gamedata);
					if ($pos != null) {
					$sourcePos = $pos;
					} 
					else {
					$sourcePos = $shooter->getHexPos();
					}
			$dis = mathlib::getDistanceHex($sourcePos, $target);				
			if ($dis <= 15) {
				$damage -= 0;
				}
			else {
				$damage -= round(($dis - 15) * $this->rangeDamagePenaltyPBolter);
			}	
		        $damage = max(0, $damage); //at least 0	    
        		$damage = floor($damage); //drop fractions, if any were generated
      			 return $damage;
	}		
	
		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			$this->data["Special"] = "No range damage penalty up to a distance of 15 hexes.";
			$this->data["Special"] .= "<br>After 15 hexes, damage reduced by 1 point per 3 hexes.";
			$this->data["Special"] .= "<br>Ignores half of armor.";
	}
			
        public function getDamage($fireOrder){        return 22;   }
        public function setMinDamage(){     $this->minDamage = 22 ;      }
        public function setMaxDamage(){     $this->maxDamage = 22 ;      }
    

}// End of class HeavyPlasmaBolter	


class MediumPlasmaBolter extends Plasma{
	public $name = "mediumPlasmaBolter";
	public $displayName = "Medium Plasma Bolter";
	public $iconPath = "MediumPlasmaBolter.png";

	public $priority = 6;
	
	public $rangeDamagePenalty = 0;
	public $rangeDamagePenaltyPBolter = 0.5;
	public $loadingtime = 2;
	public $rangePenalty = 0.5;
	public $fireControl = array(-3, 2, 3);


		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
				if ( $maxhealth == 0 ) $maxhealth = 8;
				if ( $powerReq == 0 ) $powerReq = 5;
				parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
		}

	protected function getDamageMod($damage, $shooter, $target, $pos, $gamedata)
	{
		parent::getDamageMod($damage, $shooter, $target, $pos, $gamedata);
					if ($pos != null) {
					$sourcePos = $pos;
					} 
					else {
					$sourcePos = $shooter->getHexPos();
					}
			$dis = mathlib::getDistanceHex($sourcePos, $target);				
			if ($dis <= 10) {
				$damage -= 0;
				}
			else {
				$damage -= round(($dis - 10) * $this->rangeDamagePenaltyPBolter);
			}	
		        $damage = max(0, $damage); //at least 0	    
        		$damage = floor($damage); //drop fractions, if any were generated
      			 return $damage;
	}		
	
		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			$this->data["Special"] = "No range damage penalty up to a distance of 10 hexes.";
			$this->data["Special"] .= "<br>After 10 hexes, damage reduced by 1 point per 2 hexes.";
			$this->data["Special"] .= "<br>Ignores half of armor.";
	}
			
        public function getDamage($fireOrder){        return 16;   }
        public function setMinDamage(){     $this->minDamage = 16 ;      }
        public function setMaxDamage(){     $this->maxDamage = 16 ;      }
}// End of class MediumPlasmaBolter


class LightPlasmaBolter extends Plasma{
	public $name = "lightPlasmaBolter";
	public $displayName = "Light Plasma Bolter";
	public $iconPath = "LightPlasmaBolter.png";

	public $priority = 5;
	
	public $rangeDamagePenalty = 0;
	public $rangeDamagePenaltyPBolter = 1;
	public $loadingtime = 1;
	public $rangePenalty = 1;
	public $fireControl = array(-2, 2, 3);


		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
				if ( $maxhealth == 0 ) $maxhealth = 6;
				if ( $powerReq == 0 ) $powerReq = 2;
				parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
		}

	protected function getDamageMod($damage, $shooter, $target, $pos, $gamedata)
	{
		parent::getDamageMod($damage, $shooter, $target, $pos, $gamedata);
					if ($pos != null) {
					$sourcePos = $pos;
					} 
					else {
					$sourcePos = $shooter->getHexPos();
					}
			$dis = mathlib::getDistanceHex($sourcePos, $target);				
			if ($dis <= 5) {
				$damage -= 0;
				}
			else {
				$damage -= round(($dis - 5) * $this->rangeDamagePenaltyPBolter);
			}	
		        $damage = max(0, $damage); //at least 0	    
        		$damage = floor($damage); //drop fractions, if any were generated
      			 return $damage;
	}		
	
		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			$this->data["Special"] = "No range damage penalty up to a distance of 5 hexes.";
			$this->data["Special"] .= "<br>After 5 hexes, damage reduced by 1 point per hex.";
			$this->data["Special"] .= "<br>Ignores half of armor.";
	}
			
        public function getDamage($fireOrder){        return 10;   }
        public function setMinDamage(){     $this->minDamage = 10 ;      }
        public function setMaxDamage(){     $this->maxDamage = 10 ;      }
}// End of class LightPlasmaBolter


class LightPlasmaBolterFighter extends LinkedWeapon{
	public $name = "LightPlasmaBolterFighter";
	public $displayName = "Light Plasma Bolter";
	public $iconPath = "LightPlasmaBolterFighter.png";
    public $animationColor = array(75, 250, 90); //needed as this doesn't inherit from Plasma...

  	public $intercept = 0; //actually this weapon probably SHOULD have interception after all! I don't think so, no other plasma fighter weapon does!
	public $loadingtime = 1;
	public $shots = 2;
    public $defaultShots = 2;
    public $rangePenalty = 2;
    public $fireControl = array(0, 0, 0);
    
    public $rangeDamagePenalty = 0;
	public $rangeDamagePenaltyPBolter = 1;    
    public $damageBonus = 0;
	
   	public $damageType = "Standard"; 
   	public $weaponClass = "Plasma"; 	


		function __construct($startArc, $endArc, $nrOfShots = 2){
			$this->defaultShots = $nrOfShots;
			$this->shots = $nrOfShots;
            $this->intercept = $nrOfShots;

			parent::__construct(0, 1, 0, $startArc, $endArc);
		}	


	protected function getDamageMod($damage, $shooter, $target, $pos, $gamedata)
	{
		parent::getDamageMod($damage, $shooter, $target, $pos, $gamedata);
					if ($pos != null) {
					$sourcePos = $pos;
					} 
					else {
					$sourcePos = $shooter->getHexPos();
					}
			$dis = mathlib::getDistanceHex($sourcePos, $target);				
			if ($dis <= 3) {
				$damage -= 0;
				}
			else {
				$damage -= round(($dis - 3) * $this->rangeDamagePenaltyPBolter);
			}	
		        $damage = max(0, $damage); //at least 0	    
        		$damage = floor($damage); //drop fractions, if any were generated
      			 return $damage;
	}		

		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			$this->data["Special"] = "No range damage penalty up to a distance of 3 hexes.";
			$this->data["Special"] .= "<br>After 3 hexes, damage reduced by 1 point per hex.";
			$this->data["Special"] .= "<br>Ignores half of armor.";
	}
			
        public function getDamage($fireOrder){        return 7;   }
        public function setMinDamage(){     $this->minDamage = 7 ;      }
        public function setMaxDamage(){     $this->maxDamage = 7 ;      }

}// End of class LightPlasmaBolterFighter	


class DualPlasmaCannon extends Plasma{
        public $name = "DualPlasmaCannon";
        public $displayName = "Dual Plasma Cannon";
	    public $iconPath = "dualplasmacannon.png";
	

	//actual weapons data
    	public $rangeDamagePenalty = 0.5;
   		public $priorityArray = array(1=>7, 2=>5);
    	public $gunsArray = array(1=>1, 2=>2); //one Lance, but two Beam shots!
	
        public $loadingtimeArray = array(1=>3, 2=>3); //mode 1 should be the one with longest loading time
        public $rangePenaltyArray = array(1=>0.5, 2=>1);
        public $fireControlArray = array( 1=>array(-5, 1, 3), 2=>array(-5, 1, 3) ); 
	
		public $firingModes = array(1=>'Dual', 2=>'Medium Plasmas');
		public $damageTypeArray = array(1=>'Plasma', 2=>'Plasma'); 

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
	
        public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			$this->data["Special"] = 'Can fire as either a Dual Plasma Cannon or two Medium Plasma Cannons.';
			$this->data["Special"] .= "<br>Does less damage over distance (".$this->rangeDamagePenalty." per hex).";
			$this->data["Special"] .= "<br>Ignores half of armor.";
        }
	
        public function getDamage($fireOrder){ 
		switch($this->firingMode){
			case 1:
				return Dice::d(10, 5)+8; //DualPlasma
				break;
			case 2:
				return Dice::d(10, 3)+4; //MediumPlasma
				break;	
		}
	}
        public function setMinDamage(){ 
		switch($this->firingMode){
			case 1:
				$this->minDamage = 13; //Lance
				break;
			case 2:
				$this->minDamage = 7; //Beam
				break;	
		}
		$this->minDamageArray[$this->firingMode] = $this->minDamage;
	}
        public function setMaxDamage(){
		switch($this->firingMode){
			case 1:
				$this->maxDamage = 58; //Lance (...DualPlasma)
				break;
			case 2:
				$this->maxDamage = 34; //Medium Plasma
				break;	
		}
		$this->maxDamageArray[$this->firingMode] = $this->maxDamage;
	}	
	
} //end of DualPlasmaCannon


class MegaPlasma extends Plasma{
    	public $name = "MegaPlasma";
        public $displayName = "Mega Plasma Cannon";
		public $iconPath = "MegaPlasma.png";

    	public $rangeDamagePenalty = 0.5;
	        
        public $loadingtime = 4;
			
        public $rangePenalty = 0.50;
        public $fireControl = array(-5, 1, 3); // fighters, <=mediums, <=capitals 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 10;
            if ( $powerReq == 0 ) $powerReq = 8;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
    	public function getDamage($fireOrder){        return Dice::d(10,6)+12;   }
        public function setMinDamage(){     $this->minDamage = 18 /*- $this->dp*/;      }
        public function setMaxDamage(){     $this->maxDamage = 72 /*- $this->dp*/;      }

} //end of class MegaPlasma


class PlasmaProjector extends Raking{
	public $name = "PlasmaProjector";
	public $displayName = "Plasma Projector";
	public $iconPath = "PlasmaProjector.png";
	public $animation = "laser";
    public $animationColor = array(75, 250, 90); //needed as this doesn't inherit from Plasma...

	public $priority = 6;

	public $rangeDamagePenalty = 0.25;
	public $loadingtime = 3;
	public $raking = 8;
	public $rangePenalty = 0.5;
	public $fireControl = array(null, 2, 3);

	public $damageType = "Raking";
	public $weaponClass = "Plasma";	
	public $firingModes = array(1 => "Raking");

	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
			if ( $maxhealth == 0 ) $maxhealth = 8;
			if ( $powerReq == 0 ) $powerReq = 5;
			parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
	}

	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
			$this->data["Special"] = "Damage reduced by 1 points per 4 hexes.";
			$this->data["Special"] .= "<br>Does damage in raking mode (8)";
			$this->data["Special"] .= "<br>Ignores half of armor.";
	}
			
    public function getDamage($fireOrder){        return Dice::d(10,4)+5;   }
	public function setMinDamage(){     $this->minDamage = 9;      }
	public function setMaxDamage(){     $this->maxDamage = 45;      }

}// End of class PlasmaProjector


class PlasmaBlast extends Weapon{
        public $name = "PlasmaBlast";
        public $displayName = "Plasma Blast";
		public $iconPath = "PlasmaWeb.png";
		
        public $animation = "ball";
    public $animationColor = array(75, 250, 90); //needed as this doesn't inherit from Plasma... //make it greenish, as it's Plasma ;)
        //public $animationColor = array(192,192,192);
        //public $trailColor = array(192,192,192);
        public $animationExplosionScale = 0.5;
        public $animationExplosionType = "AoE";

        public $ballistic = false;
        public $hextarget = false; //for technical reasons this proved hard to do
        public $hidetarget = false;
        public $priority = 1; //to show effect quickly
        public $uninterceptable = true; //just so nothing tries to actually intercept this weapon
        public $doNotIntercept = true; //do not intercept this weapon, period
		public $canInterceptUninterceptable = true; //able to intercept shots that are normally uninterceptable, eg. Lasers
	
        public $useOEW = false; //not important, really	    
        
		public $range = 3;
        public $loadingtime = 1; 
        public $rangePenalty = 0;
        public $fireControl = array(100, null, null); // fighters, <mediums, <capitals; just so the weapon is targetable
//		public $intercept = 2; //intercept rating -2	    
	    
		public $firingMode = 'AoE'; //firing mode - just a name essentially
		public $damageType = "Flash"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    	public $weaponClass = "Plasma"; //not important really
	 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		        //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 2;
            if ( $powerReq == 0 ) $powerReq = 1;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
		public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            $this->data["Special"] = "Fired at hex (although You technically have to pick an unit). Will apply interception to all fire from target hex to Chaff-protected ship.";
            $this->data["Special"] .= "<br>Will affect uninterceptable weapons.";
        }
        
	//hit chance always 100 - so it always hits and is correctly animated
	public function calculateHitBase($gamedata, $fireOrder)
	{
		$fireOrder->needed = 100; //auto hit!
		$fireOrder->updated = true;
		
		//while we're at it - we may add appropriate interception orders!		
		$targetShip = $gamedata->getShipById($fireOrder->targetid);
		
		$shipsInRange = $gamedata->getShipsInDistance($targetShip); //all units on target hex
		
		//retarget at hex - this will affect how the weapon is animated/displayed in firing log!
		    //insert correct target coordinates: CURRENT target position
		    $pos = $targetShip->getHexPos();
		    $fireOrder->x = $pos->q;
		    $fireOrder->y = $pos->r;
		    $fireOrder->targetid = -1; //correct the error

	}//endof function calculateHitBase
	   
	
	public function fire($gamedata, $fireOrder)
	{ //sadly here it really has to be completely redefined... or at least I see no option to avoid this
		$this->changeFiringMode($fireOrder->firingMode);//changing firing mode may cause other changes, too!
		$shooter = $gamedata->getShipById($fireOrder->shooterid);
		/** @var MovementOrder $movement */
		$movement = $shooter->getLastTurnMovement($fireOrder->turn);
		$posLaunch = $movement->position;//at moment of launch!!!		
		//$this->calculateHit($gamedata, $fireOrder); //already calculated!
		$rolled = Dice::d(100);
		$fireOrder->rolled = $rolled; ///and auto-hit ;)
		$fireOrder->shotshit++;
		$fireOrder->pubnotes = "Interception applied to all weapons at target hex that are firing at Chaff-launching ship. "; //just information for player, actual applying was done in calculateHitBase method

		//deal damage!
        $target = new OffsetCoordinate($fireOrder->x, $fireOrder->y);
        $ships1 = $gamedata->getShipsInDistance($target); //all ships on target hex
        foreach ($ships1 as $targetShip) if ($targetShip instanceOf FighterFlight) {

            $this->AOEdamage($targetShip, $shooter, $fireOrder, $gamedata);
//		$fireOrder->pubnotes .= "<br>Hit a fighter."; //just information for player, actual applying was done in calculateHitBase method

        }

		$fireOrder->rolled = max(1, $fireOrder->rolled);//Marks that fire order has been handled, just in case it wasn't marked yet!
		TacGamedata::$lastFiringResolutionNo++;    //note for further shots
		$fireOrder->resolutionOrder = TacGamedata::$lastFiringResolutionNo;//mark order in which firing was handled!
	} //endof function fire

//and now actual damage dealing - and we already know fighter is hit (fire()) doesn't pass anything else)
//source hex will be taken from firing unit, damage will be individually rolled for each fighter hit
 public function AOEdamage($target, $shooter, $fireOrder, $gamedata)
    {
        if ($target->isDestroyed()) return; //no point allocating
            foreach ($target->systems as $fighter) {
                if ($fighter == null || $fighter->isDestroyed()) {
                    continue;
                }
         //roll (and modify as appropriate) damage for this particular fighter:
        $damage = $this->getDamage($fireOrder);
        $damage = $this->getDamageMod($damage, $shooter, $target, null, $gamedata);
        $damage -= $target->getDamageMod($shooter, null, $gamedata->turn, $this);

                $this->doDamage($target, $shooter, $fighter, $damage, $fireOrder, null, $gamedata, false);

		}
	}


	
        public function getDamage($fireOrder){
//            return Dice::d(6, 1)+2; 
            return 5; 
        }
        public function setMinDamage(){     $this->minDamage = 5;      }
        public function setMaxDamage(){     $this->maxDamage = 5;      }

}//endof PlasmaBlast



class Fuser extends Plasma{	
	public $name = "Fuser";
    public $displayName = "Fuser";
	public $iconPath = "Fuser.png";    

    public $priority = 2;
    public $rangeDamagePenalty = 1;
		        
    public $loadingtime = 5;			
    public $rangePenalty = 0.33;
    public $fireControl = array(null, 3, 5); // fighters, <=mediums, <=capitals 

	public $damageType = "Flash"; 
	public $weaponClass = "Plasma"; 
	public $firingModes = array( 1 => "Flash"); 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 18;
            if ( $powerReq == 0 ) $powerReq = 12;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
	public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
		}
	
		
	public function getDamage($fireOrder){        return Dice::d(10,9)+20;   }
        public function setMinDamage(){     $this->minDamage = 29;      }
        public function setMaxDamage(){     $this->maxDamage = 110;      }

}//end of class Fuser


class RangedFuser extends Plasma{	
	public $name = "RangedFuser";
    public $displayName = "Ranged Fuser";
	public $iconPath = "RangedFuser.png";     

    public $priority = 2;
    public $rangeDamagePenalty = 0.25;
		        
    public $loadingtime = 5;			
    public $rangePenalty = 0.25;
    public $fireControl = array(null, 3, 5);

	public $damageType = "Flash"; 
	public $weaponClass = "Plasma"; 
	public $firingModes = array( 1 => "Flash"); 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 12;
            if ( $powerReq == 0 ) $powerReq = 12;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
	public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
		}
	
		
	public function getDamage($fireOrder){        return Dice::d(10,6)+12;   }
        public function setMinDamage(){     $this->minDamage = 18;      }
        public function setMaxDamage(){     $this->maxDamage = 72;      }

}//endof class RangedFuser



class PakmaraPlasmaWeb extends Weapon implements DefensiveSystem{        
    public $name = "PakmaraPlasmaWeb";
    public $displayName = "Plasma Web";
	public $iconPath = "PlasmaWeb.png";

    public $animation = "ball";
    public $animationColor = array(75, 250, 90);    	
    public $animationExplosionScale = 0.5;
	public $noProjectile = true; //Marker for front end to make projectile invisible for weapons that shouldn't have one.  

    public $ballistic = false;
    public $hextarget = true;
    public $hidetarget = false;

	/* The cloud damages every flight that moved through its hex, and an attached pod
	   mirrors its host's movement so it is in the hex whenever the host is. Without this
	   the automatic "hit the host too" spill turned a hex-targeted anti-fighter weapon
	   into a full-damage automatic hit on a capital ship. Protected, not public - see the
	   declaration in Weapon for why. */
	protected $skipsAttachedHostHit = true;

    public $priority = 1; //to show effect quickly
 	public $priorityArray = array(1=>1, 2=>2);        
                
    public $uninterceptable = true; //just so nothing tries to actually intercept this weapon
    public $doNotIntercept = true; //do not intercept this weapon, period
	public $canInterceptUninterceptable = true; //able to intercept shots that are normally uninterceptable, eg. Lasers
    public $useOEW = false; //not important, really
        		
    public $loadingtime = 1;
    public $intercept = 0; 

	public $tohitPenalty = 0;
	public $damagePenalty = 0;        
         		
	public $range = 100;
    public $rangeArray = array(1=>100, 2=>3); //range is essentially unlimited for Defensive, but limited for Offensive.
	public $rangePenalty= 0;
	public $rangePenaltyArray = array(1=>0, 2=>0); //no range penalty in either mode                  
        
    public $boostable = true;
    public $boostEfficiency = 1;
    public $maxBoostLevel = 1;     
		
	public $weaponClassArray = array(1=>'Plasma', 2=>'Plasma');
	public $damageTypeArray = array(1=>'Plasma', 2=>'Plasma'); //indicates that this weapon does Plasma damage in Offensive mode    	
    public $firingMode = "Defensive";
        
    public $firingModes = array(
            1 => "Defensive Plasma Web",
			2 => "Anti-Fighter Plasma Web",			
    );
    
    public $fireControlArray = array( 1=>array(0,0,0), 2=>array(0, null, null)); // fighters, <mediums, <capitals 
		
	private static $alreadyEngagedDirect = array(); //units that were already engaged by a Plasma Web this turn (multiple Webs do not stack).
	private static $alreadyEngagedClouded = array();
	protected $autoHit = true;//To show 100% hit chance in front end.				

    protected $possibleCriticals = array(
            17=>array("OutputReduced1", "ReducedRangeValue"));  //Provding Outputreduced1 works then replace reduced range from TT with reduced damage for Offensive mode

    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		if ( $maxhealth == 0 ) $maxhealth = 4;
        if ( $powerReq == 0 ) $powerReq = 2;            
        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
    }

	//Defensive system functions
	public function getDefensiveType()
    {
        return "Shield";
    }
    
	public function getDefensiveHitChangeMod($target, $shooter, $pos, $turn, $weapon) {
	    // Variable initialization
	    $output = 0;
	    $targetpos = null;

	    // Checking whether the weapon is actually ordered to intercept
	    foreach ($this->fireOrders as $fire) {
	        if ($fire->firingMode == "1" && $fire->turn == $turn) {
	            $targetpos = new OffsetCoordinate($fire->x, $fire->y);
	            $output = 2;

	            // Determine position the shot is coming from
	            if (!$weapon->ballistic) { // Direct fire
	                $pos = $shooter->getHexPos();
	            } else { // Ballistic
	                $movement = $shooter->getLastTurnMovement($fire->turn); 
	                $pos = $movement->position;
	            }

	            // If Web is ordered to intercept somewhere else - cannot intercept this shot
	            if ($pos != $targetpos) {
	                $output = 0;
	                break; // Exit the loop early if interception is not possible
	            }
	        }
	    }

	    // Return the actual value
	    return $output;
	} // End of getDefensiveHitChangeMod


	public function getDefensiveDamageMod($target, $shooter, $pos, $turn, $weapon) {

	    // Variable initialization
	    $output = 0;
	    $targetpos = null;

	    // Plasma Web damage reduction only works against three types of weapons.
	    if ($weapon->weaponClass != 'Laser' && $weapon->weaponClass != 'Antimatter' && $weapon->weaponClass != 'Particle' && $weapon->name != 'AntimatterTorpedo') {
	        return 0;
	    }
	    
	    // Checking whether the weapon is actually ordered to intercept
	    foreach ($this->fireOrders as $fire) {
	        if ($fire->firingMode == "1" && $fire->turn == $turn) {
	            $targetpos = new OffsetCoordinate($fire->x, $fire->y);
	            $output = 2;

	            // Determine the position the shot is coming from
	            if (!$weapon->ballistic) { // Direct fire
	                $pos = $shooter->getHexPos();
	            } else { // Ballistic
	                $movement = $shooter->getLastTurnMovement($fire->turn);
	                $pos = $movement->position;
	            }

	            // If Web is ordered to intercept somewhere else - cannot intercept this shot
	            if ($pos != $targetpos) {
	                $output = 0;
	                break; // Exit the loop early if interception is not possible
	            }
	        }
	    }

	    // Return the actual value
	    return $output;
	} // End of getDefensiveDamageMod
			

    public function beforePreFiringOrderResolution($gamedata){
    	
		//Start by checking for Ballistic fireOrder, if there isn't one there's no need to do anything!
		$firingOrders = $this->getFireOrders($gamedata->turn);
						    	
		$cloudFireOrder = null;
			foreach ($firingOrders as $fireOrder) { 
				if ($fireOrder->type == 'ballistic') { 
					$cloudFireOrder = $fireOrder;
					break; //no need to search further
				}
			}    			
								
		if($cloudFireOrder==null) return; //no appropriate fire order, end of work.  

		// Store the original coordinates
		$originalX = $cloudFireOrder->x;
		$originalY = $cloudFireOrder->y;

		//Check through fireOrders, only interested in Persistent Effect orders created in Initial Orders Phase
		foreach ($firingOrders as $cloudFireOrder) { 		
			if (($cloudFireOrder->type == 'ballistic') &&  ($cloudFireOrder->damageclass == 'PersistentEffectPlasma')) { 	//Double-check.	

				//fireOrder found, proceed to check whether any fighters passed through it.   	
		    	$thisShip = $this->getUnit();		    	  
		    	$allShips = $gamedata->ships;
		    	$relevantShips = array();

				//Make a list of relevant ships e.g. this ship and enemy fighters in the game.
				foreach($allShips as $ship){
					if (!$ship instanceof FighterFlight) continue; //Ignore ships.
					if($ship->isDestroyed()) continue;//Ignore destroyed flights.
					if($ship->getTurnDeployed($gamedata) > $gamedata->turn) continue; //Ship not deployed yet.												
					$relevantShips[] = $ship;			
				}

				//Now check if any enemy fighters got in arc and range during their movement.
				$targetFighters = $this->checkForValidTargets($relevantShips, $cloudFireOrder, $gamedata);	
				
				if(!empty($targetFighters))	$this->createFireOrders($targetFighters, $thisShip, $gamedata, $cloudFireOrder);
			}
		}

		// Restore the original coordinates
		$cloudFireOrder->notes = 'PersistentEffect';
		$cloudFireOrder->x = $originalX;
		$cloudFireOrder->y = $originalY;
    	
	}//endof beforeFiringOrderResolution


	private function convertOffsetCoordinateToArray($offsetCoordinate) {
	    return array((string)$offsetCoordinate->q, (string)$offsetCoordinate->r);
	}


	/* Movement rows to test a flight's hex transits against.

	   An attached breaching pod is carried through every hex its host enters, but the rows
	   MovementGamePhase writes for it are ALL type 'attached' (it copies the host's plot
	   wholesale under that one type), so the "entered a NEW hex" test below - which only
	   accepts move/slipleft/slipright - can never match one. Reading the HOST's own rows
	   instead gives the same hexes with the real move types intact, so a pod riding a ship
	   through a plasma cloud is caught exactly when the ship itself would have been.

	   Firing::fire's attached-pod host spill is what keeps this from also damaging the host:
	   this weapon opts out via $skipsAttachedHostHit, so the cloud burns the pod only. */
	private function getHexTransitMovement($flight, $gamedata) {
	    if (empty($flight->attached)) return $flight->movement;

	    $hostIds = array_keys($flight->attached);
	    $host = $gamedata->getShipById(reset($hostIds));

	    return ($host === null) ? $flight->movement : $host->movement;
	}


	private function checkForValidTargets($relevantShips, $cloudFireOrder, $gamedata) {
	    $targetFighters = array(); // Initialize array for fighters to be fired at.
	    $plasmaPosition = array((string)$cloudFireOrder->x, (string)$cloudFireOrder->y); // Convert plasma cloud position to a string array.

	    foreach ($relevantShips as $flight) { // Look through relevant ships and take appropriate action.
	        // Check starting position first.

	        // Now check other movements in the turn.
	        foreach ($this->getHexTransitMovement($flight, $gamedata) as $fighterMove) {
	            if ($fighterMove->turn == $gamedata->turn) {

	                // Only interested in moves where flight enters a NEW hex!
	                if ($fighterMove->type == "move" || $fighterMove->type == "slipleft" || $fighterMove->type == "slipright") {
	                    // Convert OffsetCoordinate to array format for comparison
	                    $fighterPositionArray = $this->convertOffsetCoordinateToArray($fighterMove->position);

	                    // Check if the position matches the plasma cloud
	                    if ($fighterPositionArray === $plasmaPosition) {
	                        $targetFighters[] = $flight; // Add to array to be targeted.
	                    }
	                }
	            }
	        }
	    }			

	    return $targetFighters; 
	} // end of checkForValidTargets


	private function createFireOrders($targetFighters, $thisShip, $gamedata, $cloudFireOrder){

		foreach ($targetFighters as $target) {

		    // Create a key from the coordinates of the fire order
		    $coordinatesKey = $cloudFireOrder->x . ',' . $cloudFireOrder->y; // This will be the key for the array
		 
			// Check if the target has been engaged by a Plasma Cloud at these coordinates
			if (isset(PakmaraPlasmaWeb::$alreadyEngagedClouded[$target->id][$coordinatesKey])) {
				if (PakmaraPlasmaWeb::$alreadyEngagedClouded[$target->id][$coordinatesKey]['engaged'] === true) {
				    // Target was already engaged at these coordinates.
				    // CONTINUE, not return - the guard is per TARGET, and returning abandoned every
				    // remaining flight in the list. A second Web clouding the same hex, or a flight
				    // that enters the hex twice in one plot, silently spared everything after it.
				    continue; // unit already engaged by a Plasma Web at these coordinates, don't create new fireOrder
				}
			}
	
		    // Create a new FireOrder
		    $newDamageFireOrder = new FireOrder(
		        -1, "prefiring", $thisShip->id, $target->id,
		        $this->id, -1, $gamedata->turn, 2, 
		        100, 0, 1, 1, 0, // needed, rolled, shots, shotshit, intercepted
		        $cloudFireOrder->x, $cloudFireOrder->y, 'PersistentEffectPlasma', -1 // X, Y, damageclass, resolutionorder
		    ); 
		    $newDamageFireOrder->notes = 'Attack on fighters passing through';
	        // Store the engagement with coordinates as the key
	        PakmaraPlasmaWeb::$alreadyEngagedClouded[$target->id][$coordinatesKey] = [
	            'engaged' => true, // marking engagement
	            'coordinates' => ['x' => $newDamageFireOrder->x, 'y' => $newDamageFireOrder->y] // store the x and y coordinates while they are still the cloud coordinate.
	        ];		 	                

			//Then change coordinates so AoE Damage and Replay are function correctly
			$movement = null;
		    foreach ($target->movement as $move){
		        $movement = $move;
		    }
			
/*
			$targetPositionArray = $this->convertOffsetCoordinateToArray($movement->position);

			$newDamageFireOrder->x = $targetPositionArray[0];
			$newDamageFireOrder->y = $targetPositionArray[1];	
*/
				
		    $newDamageFireOrder->addToDB = false;//Will be manually entered immediately below!
		    $this->fireOrders[] = $newDamageFireOrder;
		        
		    //Save the fire order to database
            Manager::insertSingleFiringOrder($gamedata, $newDamageFireOrder);
            // Retrieve and handle fire orders from the DB
			$dbFireOrders = Manager::retrieveFiringOrdersForWeapon($gamedata, $thisShip->id, $this->id);


				if($dbFireOrders != null){
					foreach($dbFireOrders as $dbFire){
						$newDamageFireOrder->id = $dbFire->id;//Change id of newFireOrder to id assigned by database.
						break; //Don't check the others, retrieved in Descending order so first one is the one we want!
					}
				}
        }

	}//End of createFireOrders()	


	public function calculateHitBase($gamedata, $fireOrder)
	{
		if($fireOrder->damageclass == 'PersistentEffectPlasma') return; //Don't resolve hit resolution for cloud markers or fighter hits.
			
		$this->changeFiringMode($fireOrder->firingMode);
			
		$fireOrder->needed = 100;				
		$fireOrder->updated = true;
		$fireOrder->shots = 1;					
		$fireOrder->notes .= 'Plasma Web direct shot.';

//		if($fireOrder->type == "ballistic" && $fireOrder->damageclass != 'PersistentEffectPlasma') $fireOrder->notes = 'Attack on fighters passing through';
				
		if ($fireOrder->targetid != -1 && $fireOrder->type == 'normal') {//Correct any direct fireOrders that targeted a ship.
			$targetship = $gamedata->getShipById($fireOrder->targetid);
			//insert correct target coordinates: last turns' target position
			$targetShip = $gamedata->getShipById($fireOrder->targetid);			
 			$targetpos = $targetShip->getHexPos();
			$fireOrder->x = $targetpos->q;
			$fireOrder->y = $targetpos->r;
			$fireOrder->targetid = -1; //correct the error
			$fireOrder->calledid = -1; //just in case
		} 		
	}		
		
		
	public function fire($gamedata, $fireOrder){
		if($fireOrder->firingMode == 1) return; //Don't animate Defensive fire, it clogs up Replay

		if($fireOrder->type == 'ballistic' && $fireOrder->damageclass == 'PersistentEffectPlasma') {
			$fireOrder->notes = 'PersistentEffect';

	        $fireOrder->updated = true;				
			return; //Don't resolve ballistic 'cloud' fireOrders.
		}
		
		$shooter = $gamedata->getShipById($fireOrder->shooterid);

		$this->changeFiringMode($fireOrder->firingMode);		
									
		switch($this->firingMode){
			case 1:	
				$rolled = Dice::d(100);
				$fireOrder->rolled = $rolled; ///and auto-hit
				$fireOrder->shotshit++;
											
				$fireOrder->pubnotes .= "Damage and Hit Chance reduction effects applied to weapons fired from target hex."; //just information for player				
								
				break;
									
			case 2:		
								
				$rolled = Dice::d(100);
				$fireOrder->rolled = $rolled; ///and auto-hit ;)
				$fireOrder->shotshit = 1;
				
				if($fireOrder->type == 'prefiring'){ //Plasma cloud attack, shouldn't draw power.
					$fireOrder->pubnotes .= "<br>Plasma cloud(s) damages fighters that pass through.";//just information for player.				
		
					//deal damage!  Will be to a particular flight, not genuinely AoE!
					$targetFlight = $gamedata->getShipById($fireOrder->targetid);						
					$this->AOEdamage($targetFlight, $shooter, $fireOrder, $gamedata);	
					
					$fireOrder->targetid = -1; //Do not need targetid anymore, change to -1			
				
				}else{//Anti-Fighter mode FIRED this turn, draw power if not boosted.
					if ($this->getBoostLevel(TacGamedata::$currentTurn) <=0 ) { //not boosted...
						PlasmaBattery::shipDrawPower($this->unit);
					}
									
					$fireOrder->pubnotes .= "Plasma cloud created on hex, remains in place until next Firing Phase!"; //just information for player.
					
					//deal damage!
					$target = new OffsetCoordinate($fireOrder->x, $fireOrder->y);
					$ships1 = $gamedata->getShipsInDistance($target); //all ships on target hex
								
					foreach ($ships1 as $targetShip) if ($targetShip instanceOf FighterFlight) {
						$this->AOEdamage($targetShip, $shooter, $fireOrder, $gamedata);
					}											

				}
			
				break;

		}	

		TacGamedata::$lastFiringResolutionNo++;    //note for further shots
		$fireOrder->resolutionOrder = TacGamedata::$lastFiringResolutionNo;//mark order in which firing was handled!			
		$fireOrder->rolled = max(1, $fireOrder->rolled);//Marks that fire order has been handled, just in case it wasn't marked yet!
	    $fireOrder->updated = true;		
						
	} //endof function fire		
	
	
	//and now actual damage dealing for Offensive Mode - and we already know fighter is hit (fire()) doesn't pass anything else)
	//source hex will be taken from firing unit, damage will be individually rolled for each fighter hit
	public function AOEdamage($target, $shooter, $fireOrder, $gamedata){

		$coordinatesKey = $fireOrder->x . ',' . $fireOrder->y; // This will be the key for the array
	
		if($fireOrder->type == 'normal'){
	        if (isset(PakmaraPlasmaWeb::$alreadyEngagedDirect[$target->id])){
	        	$fireOrder->pubnotes .= "<br> No effect on fighters already damaged by a Plasma Web on this hex." ; //just information for player.
	        	return; //unit already engaged by a previous direct Plasma Web at this hex.
			}	
			if (isset(PakmaraPlasmaWeb::$alreadyEngagedClouded[$target->id][$coordinatesKey])) {
				if (PakmaraPlasmaWeb::$alreadyEngagedClouded[$target->id][$coordinatesKey]['engaged'] === true) {
				    // Target was already engaged at these coordinates
	        		$fireOrder->pubnotes .= "<br> No effect on fighters already damaged by a Plasma Web on this hex." ; //just information for player.				    
				    return; // unit already engaged by a Plasma Web cloud at these coordinates, don't cause further damage.
				}
			}					
		}	

        if ($target->isDestroyed()) return; //no point allocating
			
        foreach ($target->systems as $fighter) {
            if ($fighter == null || $fighter->isDestroyed()) {
                    continue;
        	}
                
		    //roll (and modify as appropriate) damage for this particular fighter:
		    $damage = $this->getDamage($fireOrder);
		    $damage = $this->getDamageMod($damage, $shooter, $target, null, $gamedata);
		    $damage -= $target->getDamageMod($shooter, null, $gamedata->turn, $this);

		    $this->doDamage($target, $shooter, $fighter, $damage, $fireOrder, null, $gamedata, false);
		}			                

		if($fireOrder->type == 'normal'){		    		    
			PakmaraPlasmaWeb::$alreadyEngagedDirect[$target->id] = true;//mark engaged
		}	
	
	}//endof AOEdamage()
	
		
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
			if (!isset($this->data["Special"])) {
				$this->data["Special"] = '';
			}else{
				$this->data["Special"] .= '<br>';
			}
			$this->data["Special"] .= 'DEFENSIVE - Fires at hex during the Firing Phase.';
			$this->data["Special"] .= '<br>Applies -10 Intercept against all enemy fire from hex, and -2 damage against Antimatter, Laser and Particle attacks.';
			$this->data["Special"] .= '<br>For Ballistic weapons, target hex from where shot was launched.';			
			$this->data["Special"] .= '<br>ANTI-FIGHTER - Range 3.  Creates plasma cloud in a hex dealing D6+2 Plasma damage to fighters.';
			$this->data["Special"] .= '<br>Requires 1 extra power, by boosting in Initial Orders or from Batteries.';
			$this->data["Special"] .= '<br>Cloud remains during next Movement Phase, damaging fighters moving through it.';			
			$this->data["Special"] .= '<br>Plasma Webs are NOT cumulative, each effect applies only once.';		 
	 }


	private function getBoostLevel($turn){
		$boostLevel = 0;
		foreach ($this->power as $i){
				if ($i->turn != $turn) continue;
				if ($i->type == 2){
						$boostLevel += $i->amount;
				}
		}
		return $boostLevel;
	}
	

   public function generateIndividualNotes($gameData, $dbManager){ //dbManager is necessary for Initial phase only
		$ship = $this->getUnit();
		
		switch($gameData->phase){
					
				case 4: //Firing phase
					
				      $firingOrders = $this->getFireOrders($gameData->turn);
			    	
				      $originalFireOrder = null;
					      
				        foreach ($firingOrders as $fireOrder) { 				              		
				            if ($fireOrder->type == 'normal' && $fireOrder->firingMode == 2) { //Not the ballistic cloud effect, and not generated fireOrders passing through cloud.
				                $originalFireOrder = $fireOrder;
				                break; //no need to search further
				                }
							}    			
						
				    	if($originalFireOrder==null) return; //no appropriate fire order, end of work						
							
						$notekey = 'xCoordinate';
						$noteHuman = 'Create Plasma Cloud fireOrder x';
						$notevalue = $originalFireOrder->x;
						$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$notevalue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue   
					
						$notekey = 'yCoordinate';
						$noteHuman = 'Create Plasma Cloud fireOrder y';
						$notevalue = $originalFireOrder->y;
						$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$notevalue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue 
						
				break;				
		}
	} //endof function generateIndividualNotes
	

	public function onIndividualNotesLoaded($gamedata)
	{
	    $ship = $this->getUnit();
	    $xCoordinate = null;
	    $yCoordinate = null;

		//Look at last turn's Firing Phase notes and create ballistic 'cloud' firing orders where appropriate.  Purely visual effect so players can see where cloud is, actual damage will come from beforeFiringOrderResolution() and associated routines
		if($gamedata->phase == 1){
	    
		    // Ensure individualNotes is initialized properly
		    if (is_array($this->individualNotes)) {
		        foreach ($this->individualNotes as $currNote) {	
		            if ($currNote->turn == $gamedata->turn-1) {	    	            	
		            	 
		                // Check for x and y coordinates
		                switch ($currNote->notekey) {
		                    case 'xCoordinate':
		                        $xCoordinate = $currNote->notevalue;
		                        break;
		                    case 'yCoordinate':
		                        $yCoordinate = $currNote->notevalue;
		                        break;
		                }
		                
		                // Only proceed if both coordinates are set
		                if ($xCoordinate !== null && $yCoordinate !== null) {

		                    // Create a new FireOrder
		                    $newFireOrder = new FireOrder(
		                        -1, "ballistic", $ship->id, -1,
		                        $this->id, -1, $gamedata->turn, 2, 
		                        1, 0, 1, 0, 0, // needed, rolled, shots, shotshit, intercepted
		                        $xCoordinate, $yCoordinate, 'PersistentEffectPlasma', -1 // X, Y, damageclass, resolutionorder
		                    ); 
		                    
							$newFireOrder->notes = "PersistentEffect";
		                    $newFireOrder->addToDB = true;	                    
		                    $this->fireOrders[] = $newFireOrder;
		                    
		                    // Clear coordinates once used
		                    $xCoordinate = $yCoordinate = null;
		                }
		            }
		        }
		    }
		}
		
	    // Clear individual notes, they're no longer needed
	    $this->individualNotes = array();	

		//Need to refresh ballistic fireOrder notes in Movement Phase for some reason.  These are used in Front End to display hex sprites!  
		$firingOrders = $this->getFireOrders($gamedata->turn);				    	
		$ballisticFireOrder = null;
			foreach ($firingOrders as $fireOrder) { 
				if ($fireOrder->type == 'ballistic') { 
					$ballisticFireOrder = $fireOrder;
					break; //no need to search further
					}
				}    			
								
		if($ballisticFireOrder==null) return; //no appropriate fire order, end of work    

		if($ballisticFireOrder->notes == "") $ballisticFireOrder->notes .= "PersistentEffect";			
		    	
	}//endof individualNotesLoaded()


		public function getDamage($fireOrder){
        	switch($this->firingMode){ 
            	case 1:
                	return 0; 
			    			break;
            	case 2:
            	   	return Dice::d(6,1)+2;
			    			break;		    			
        	}
		}

		public function setMinDamage(){
				switch($this->firingMode){
						case 1:
								$this->minDamage = 0;
								break;
						case 2:
								$this->minDamage = 3;
								break;							
				}
				$this->minDamageArray[$this->firingMode] = $this->minDamage;
		}							
		
		public function setMaxDamage(){
				switch($this->firingMode){
						case 1:
								$this->maxDamage = 0;
								break;
						case 2:
								$this->maxDamage = 8;
								break;								
				}
				$this->maxDamageArray[$this->firingMode] = $this->maxDamage;
		}


    public function stripForJson() {
            $strippedSystem = parent::stripForJson();    
            $strippedSystem->autoHit = $this->autoHit;
			$strippedSystem->noProjectile = $this->noProjectile;                                                   
            return $strippedSystem;
	}

} //end of class PakmaraPlasmaWeb

	
	
// GTS_Triad
class HyperplasmaCutter extends Weapon{
	public $name = "HyperplasmaCutter";
	public $displayName = "Hyperplasma Cutter";
	public $iconPath = "HyperplasmaCutter.png";

	public $factionAge = 3;

	public $animation = "laser";
	public $animationColor = array(200, 60, 220);

	public $loadingtime = 1;
	public $normalload = 2; //makes loadingTimeActual=2 so weaponManager allows manual intercept assignment
	public $raking = 15;
	public $rangeDamagePenalty = 0.33;
	public $rangePenalty = 0.33;
	public $fireControl = array(6, 6, 6);

	public $firingMode = 1;
	public $firingModes = array(
		1 => "Normal",
		2 => "Sustained",
	);

	//canSplitShots routes ALL modes through doMultipleFireOrders in the JS.
	//Normal mode uses the Lightning Cannon combine pattern (server-side isCombined).
	//Sustained mode uses the Heavy Laser overload pattern.
	public $canSplitShots = true;
	public $canSplitShotsArray = array(1=>true, 2=>true);

	public $damageType = "Raking";
	public $weaponClass = "Plasma";

	//Custom critical chart: roll is d20 + damaged boxes (standard engine roll).
	//24+ loses 1d10, 30+ loses 2d10. No standard weapon criticals apply.
	protected $possibleCriticals = array(24 => "DiceLost", 30 => array("DiceLost", "DiceLost"));

	//Track original max dice so we can calculate losses from accumulated DiceLost crits.
	protected $originalMaxDice = 10;
	public $maxDice = 10; //current max dice pool, reduced by DiceLost crits

	//Apply DiceLost crits: count accumulated crits and reduce maxDice accordingly.
	//Mirrors Twin Array's effectCriticals pattern for GunLost.
	public function effectCriticals(){
		parent::effectCriticals();
		$diceLost = $this->hasCritical("DiceLost", false);
		$this->maxDice = max(0, $this->originalMaxDice - $diceLost);
	}

	//If all dice are lost the weapon is effectively useless but not destroyed.
	//Each DiceLost crit is individually repairable (engine handles this automatically
	//since each crit is stored as a separate entry).
	public function criticalPhaseEffects($ship, $gamedata){
		parent::criticalPhaseEffects($ship, $gamedata);
		$diceLost = $this->hasCritical("DiceLost", false);
		$this->maxDice = max(0, $this->originalMaxDice - $diceLost);
	}
	//$overloadable=true enables the pipeline. $extraoverloadshots=2 means 3 turns total
	//(1 initial + 2 continuation). No power doubling needed - HyperplasmaCutter is exempt.
	//$loadingtime=1 so Normal mode fires every turn without any arming delay.
	public $intercept = 1; //1 intercept point per d10 allocated = 5% reduction per die
	public $overloadturns = 0;
	public $overloadshots = 0;
	public $extraoverloadshots = 0; //set to 2 only in beforeFiringOrderResolution when sustained is actually fired

	public $damageDice = array();
	private $sustainedTarget = array();
	private $sustainedSystemsHit = array();
	private $uniqueIntercepts = array();

	//Each cutter represents one intercept group applied to exactly ONE incoming shot.
	//Uses a simple flag rather than checking intercept orders, because intercept orders
	//are only created AFTER getInterceptionMod returns - so they are never present
	//when the next call comes in, allowing the same cutter to incorrectly intercept
	//multiple shots.
	public function getInterceptionMod($gamedata, $intercepted){
		$selfInterceptCount = 0;
		$existingInterceptOrders = 0;
		foreach ($this->fireOrders as $order){
			if ($order->type == "selfIntercept" && $order->shots > 0){
				$selfInterceptCount++;
			}
			if ($order->type == "intercept"){
				$existingInterceptOrders++;
			}
		}

		if ($selfInterceptCount == 0) return 0;

		//If this cutter already has an intercept order, it has already resolved one shot.
		//intercept orders are created by addToInterceptionTotal AFTER getInterceptionMod
		//returns, so they are present on the NEXT call - use them as the "already fired" signal.
		if ($existingInterceptOrders > 0) return 0;

		return $selfInterceptCount * $this->intercept * 5;
	}

	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
	{
		if ( $maxhealth == 0 ) $maxhealth = 16;
		if ( $powerReq == 0 ) $powerReq = 9;
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
	}

	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		} else {
			$this->data["Special"] .= '<br>';
		}
		$ship = $this->getUnit();
		$count = $ship ? $this->countShipHyperplasmaCutters($ship) : 1;
		$this->data["Special"] .= "Base pool: " . $this->maxDice . "d10 per cutter, freely allocated across any number of targets with no accuracy penalty. May hold dice back as defensive intercept.";
		$this->data["Special"] .= "<br>This ship has {$count} cutter(s), giving a maximum pool of " . (10*$count) . "d10.";
		$this->data["Special"] .= "<br>Sustained mode (Mode 2) requires ALL cutters to commit ALL dice to one target. Sustains for up to 3 turns (auto-hit turns 2-3). Cooldown of 1 turn applies after a 2 or 3 turn sustain.";
		$this->data["Special"] .= "<br>If any cutter fires in Normal mode, Sustained mode cannot be initiated that turn.";
		if (!empty($this->sustainedTarget)){
			$targetId = key($this->sustainedTarget);
			$this->data["Current Target"] = "Sustaining vs ship #" . $targetId;
		} else {
			unset($this->data["Current Target"]);
		}
	}

	public function countShipHyperplasmaCutters($ship){
		$count = 0;
		foreach ($ship->systems as $sys){
			if ($sys instanceof HyperplasmaCutter && !$sys->isDestroyed()) $count++;
		}
		return $count;
	}

	//Only return true when actually in an active sustain sequence (overloadshots > 0).
	//Without this override, $overloadable=true would cause the engine to show an S2
	//arming cycle during the initial phase even when no sustain is in progress.
	public function isOverloadingOnTurn($turn = null){
		return ($this->overloadshots > 0);
	}

	//Override calculateLoading so that when the turn advances after a sustained shot,
	//the engine saves overloadshots=2 rather than 0. Without this, calculateLoadingFromLastTurn
	//reads $this->overloadshots which was loaded from the database BEFORE our
	//beforeFiringOrderResolution write - so it always sees 0 and never continues the sequence.
	public function calculateLoading(TacGamedata $gamedata){
		//Bootstrap the sustain sequence on the first turn advance after a sustained shot.
		//We check: fired last turn AND overloadshots==0 AND has a normal fire order with
		//the HPC-Sustained note. The note check is critical - without it, an intercept-only
		//turn also satisfies firedOnTurn==true and would incorrectly bootstrap the sequence.
		if ($gamedata->phase == -1
			&& $this->overloadshots == 0){
			$hasSustainedOrder = false;
			foreach ($this->fireOrders as $order){
				if ($order->type == 'normal'
					&& $order->turn == $gamedata->turn - 1
					&& strpos($order->notes ?? '', 'HPC-Sustained') !== false){
					$hasSustainedOrder = true;
					break;
				}
			}
			if ($hasSustainedOrder){
				return new WeaponLoading(
					$this->getTurnsloaded(),
					2, //extrashots=2: bootstrap the 2-continuation-turn sequence
					0,
					$this->overloadturns,
					$this->getLoadingTime(),
					$this->firingMode
				);
			}
		}
		return parent::calculateLoading($gamedata);
	}

	//Capture dice count from each fire order before the engine resets shots to 1.
	//For Sustained mode (Mode 2), arm the overload sequence exactly once.
	public function beforeFiringOrderResolution($gamedata){
		//Detect a sustained fire order via the HPC-Sustained note and arm the overload
		//sequence. We also set $this->firingMode = 2 here because firingMode IS one of
		//the six fields WeaponLoading persists to the database (as field 6). This gives
		//us a reliable cross-turn signal that a sustain is in progress - next turn,
		//isOverloadingOnTurn() checks $overloadshots > 0 which the engine maintains via
		//the extrashots field (field 2 of WeaponLoading).
		foreach ($this->fireOrders as $order){
			if ($order->type == 'normal' && strpos($order->notes, 'HPC-Sustained') !== false && $this->overloadshots == 0){
				$this->extraoverloadshots = 2;
				$this->overloadshots = 2;
				$this->firingMode = 2;
				//Persist the overload state immediately. calculateLoading skips phase 4,
				//so without this the overloadshots value is lost when the turn advances.
				$ship = $this->getUnit();
				if ($ship){
					$loading = new WeaponLoading(
						$this->getTurnsloaded(),
						$this->overloadshots,  //extrashots=2: 2 continuation turns
						0,
						$this->overloadturns,
						$this->getLoadingTime(),
						$this->firingMode
					);
					SystemData::addDataForSystem($this->id, 0, $ship->id, $loading->toJSON());
				}
				break;
			}
		}

		//PASS 1: Capture dice counts from fire orders before the engine resets shots to 1.
		//This must happen for ALL cutters before combining can occur.
		$this->guns = 0;
		foreach ($this->fireOrders as $order){
			//Skip 0-shot auto-assigned selfIntercept orders from setSelfIntercept -
			//they exist to satisfy the engine but carry no dice and should not count as guns.
			if ($order->type == 'selfIntercept' && $order->shots == 0) continue;
			$this->guns++;
			if ($order->type == "normal"){
				$this->damageDice[$order->id] = $order->shots;
				$order->shots = 1;
			}
			//selfIntercept orders each represent one die = one intercept shot allowed.
			//guns is incremented above for each order so the engine allows all intercepts.
		}

		//PASS 2: Combining. Only run on the HIGHEST-ID cutter, which is called last by the
		//engine's beforeFiringOrderResolution loop. By the time the highest-ID cutter runs,
		//ALL other cutters have already populated their $damageDice in Pass 1 above.
		//This means we can safely read sibling damageDice values and combine them.
		$firingShip = $gamedata->getShipById($this->unit->id);
		if (!$firingShip) return;

		//Check if this is the highest-ID cutter on the ship.
		$isHighestId = true;
		foreach ($firingShip->systems as $sys){
			if ($sys instanceof HyperplasmaCutter && !$sys->isDestroyed() && (int)$sys->id > (int)$this->id){
				$isHighestId = false;
				break;
			}
		}
		if (!$isHighestId) return; //let the highest-ID cutter do the combining

		//We are the highest-ID cutter. Iterate all cutters and combine orders sharing
		//the same targetid+calledid into the lowest-ID cutter's damageDice entry.
		$allCutters = array();
		foreach ($firingShip->systems as $sys){
			if ($sys instanceof HyperplasmaCutter && !$sys->isDestroyed()) $allCutters[] = $sys;
		}
		//Sort by ID ascending so lowest-ID is first (primary)
		usort($allCutters, function($a, $b){ return (int)$a->id - (int)$b->id; });

		//Build a map of targetid+calledid -> primary order (lowest-ID cutter's order)
		$primaryOrders = array(); // key => ['weapon' => $weapon, 'order' => $order]
		foreach ($allCutters as $cutter){
			foreach ($cutter->fireOrders as $order){
				if ($order->type != 'normal') continue;
				//For fighter flights, each shot targets independently - don't combine.
				//For ships, combine all shots against the same target into one pooled shot.
				$orderTarget = $gamedata->getShipById($order->targetid);
				if ($orderTarget instanceof FighterFlight) continue;
				$key = $order->targetid . '_' . $order->calledid;
				if (!isset($primaryOrders[$key])){
					//First (lowest-ID) cutter with this target+calledid becomes primary
					$primaryOrders[$key] = array('weapon' => $cutter, 'order' => $order);
				} else {
					//Higher-ID cutter: add its dice to primary and stamp as subordinate
					$primary = $primaryOrders[$key];
					$myDice = isset($cutter->damageDice[$order->id]) ? $cutter->damageDice[$order->id] : 0;
					$primary['weapon']->damageDice[$primary['order']->id] += $myDice;
					$order->notes .= 'combined-subordinate';
					$cutter->guns--;
				}
			}
		}
	}

	public function getDamage($fireOrder){
		$dice = isset($this->damageDice[$fireOrder->id]) ? $this->damageDice[$fireOrder->id] : 1;
		return Dice::d(10, $dice);
	}

	public function setMinDamage(){ $this->minDamage = 1; }

	public function setMaxDamage(){
		$ship = $this->getUnit();
		$count = $ship ? $this->countShipHyperplasmaCutters($ship) : 1;
		$this->maxDamage = 10 * (10 * $count);
	}

	public function calculateHitBase($gamedata, $fireOrder){
		$this->changeFiringMode($fireOrder->firingMode);

		//This order was stamped as subordinate in beforeFiringOrderResolution because a
		//lower-ID cutter has an order against the same target+calledid. Its dice were
		//already added to that cutter's damageDice total. Nullify and return.
		if (strpos($fireOrder->notes, 'combined-subordinate') !== false){
			$fireOrder->chosenLocation = 0;
			$fireOrder->needed = 0;
			$fireOrder->shots = 0;
			$fireOrder->notes = "technical fire order - dice combined into primary cutter shot";
			$fireOrder->updated = true;
			$this->doNotIntercept = true;
			return;
		}

		//SUSTAINED MODE: auto-hit on continuation turns (Improved Neutron Laser pattern).
		//isOverloadingOnTurn() + loadingtime <= overloadturns confirms we're in an active
		//sustain sequence, not just in the overload pipeline for another reason.
		if ($this->isOverloadingOnTurn($gamedata->turn) &&
			$this->loadingtime <= $this->overloadturns &&
			isset($this->sustainedTarget[$fireOrder->targetid]) &&
			$this->sustainedTarget[$fireOrder->targetid] == 1){
			$fireOrder->needed = 100;
			$fireOrder->updated = true;
			$this->uninterceptable = true;
			$this->doNotIntercept = true;
			$fireOrder->pubnotes .= " Sustained shot automatically hits.";
			return;
		}

		parent::calculateHitBase($gamedata, $fireOrder);

		//Stamp the mode label into pubnotes since firingMode=1 in the fire order
		//causes the combat log to show "Normal" instead of "Sustained".
		if ($this->isOverloadingOnTurn($gamedata->turn)){
			$fireOrder->pubnotes .= " [Sustained]";
		}
	}

	//Improved Neutron Laser pattern: save target and hit result for auto-hit next turn.
	//Also forces sibling cutters offline when the sustain sequence ends, so all three
	//cutters cool down together via the engine's standard forced-offline mechanic.
	public function generateIndividualNotes($gameData, $dbManager){
		switch($gameData->phase){
			case 4:
				$ship = $this->getUnit();
				if ($this->isDestroyed() || $ship->isDestroyed()) break;

				$firingOrders = $this->getFireOrders($gameData->turn);

				//If this cutter is in an active sustain but did NOT fire this turn,
				//the player chose to end the sustain early. Force all cutters offline
				//for the cooldown turn.
				if ($this->isOverloadingOnTurn($gameData->turn) && !$firingOrders){
					foreach ($ship->systems as $sys){
						if ($sys instanceof HyperplasmaCutter && !$sys->isDestroyed()){
							$crit = new ForcedOfflineOneTurn(-1, $ship->id, $sys->id, "ForcedOfflineOneTurn", $gameData->turn);
							$crit->updated = true;
							$crit->newCrit = true;
							$sys->criticals[] = $crit;
						}
					}
					break;
				}

				if (!$firingOrders) break;

				if ($this->isOverloadingOnTurn($gameData->turn) && ($this->overloadshots - 1) > 0){
					foreach ($firingOrders as $firingOrder){
						$didShotHit = $firingOrder->shotshit;
						$targetid = $firingOrder->targetid;

						$this->individualNotes[] = new IndividualNote(
							-1, TacGamedata::$currentGameID, $gameData->turn, $gameData->phase,
							$ship->id, $this->id, 'targetinfo', 'ID of Target fired at',
							$targetid . ';' . $didShotHit
						);

						if ($didShotHit == 0) continue;

						$target = $gameData->getShipById($targetid);
						if (!$target) continue;
						foreach ($target->systems as $system){
							$dmgThisTurn = 0;
							foreach ($system->damage as $damage){
								if ($damage->turn == $gameData->turn
									&& $damage->shooterid == $ship->id
									&& $damage->weaponid == $this->id){
									$dmgThisTurn += $damage->damage;
								}
							}
							if ($dmgThisTurn > 0){
								$notes = min($dmgThisTurn, $system->armour);
								while ($notes > 0){
									$this->individualNotes[] = new IndividualNote(
										-1, TacGamedata::$currentGameID, $gameData->turn, $gameData->phase,
										$ship->id, $this->id, 'systeminfo', 'ID of System fired at', $system->id
									);
									$notes--;
								}
							}
						}
					}
				}

				//When the sustain sequence ends (last overloadshot), force siblings offline.
				if ($this->isOverloadingOnTurn($gameData->turn) && $this->overloadshots <= 1){
					foreach ($ship->systems as $sys){
						if ($sys instanceof HyperplasmaCutter && !$sys->isDestroyed() && $sys->id != $this->id){
							$crit = new ForcedOfflineOneTurn(-1, $ship->id, $sys->id, "ForcedOfflineOneTurn", $gameData->turn);
							$crit->updated = true;
							$crit->newCrit = true;
							$sys->criticals[] = $crit;
						}
					}
				}
				break;
		}
	}

	//Improved Neutron Laser pattern: restore target and armor data.
	//Looks back 2 turns for systeminfo notes since sustain can last 3 turns.
	public function onIndividualNotesLoaded($gamedata){
		foreach ($this->individualNotes as $currNote){
			if ($currNote->turn == $gamedata->turn - 1){
				if ($currNote->notekey == 'targetinfo'){
					if (strpos($currNote->notevalue, ';') === false) continue;
					$parts = explode(';', $currNote->notevalue);
					if (count($parts) === 2){
						$this->sustainedTarget[$parts[0]] = $parts[1];
					}
				}
				if ($currNote->notekey == 'systeminfo'){
					$this->sustainedSystemsHit[] = $currNote->notevalue;
				}
			}
			//Look back 2 turns for armor bypass - sustain can last 3 turns total
			if ($currNote->turn == $gamedata->turn - 2){
				if ($currNote->notekey == 'systeminfo'){
					$this->sustainedSystemsHit[] = $currNote->notevalue;
				}
			}
		}
		$this->individualNotes = array();
	}

	public function getsustainedSystemsHit(){
		if (!empty($this->sustainedSystemsHit)) return $this->sustainedSystemsHit;
		return null;
	}

	public function shieldInteractionDamage($target, $shooter, $pos, $turn, $shield, $mod){
		$toReturn = max(0, $mod);
		if (!empty($this->sustainedTarget) && array_key_exists($target->id, $this->sustainedTarget)){
			$toReturn = 0;
		}
		return $toReturn;
	}

	public function stripForJson(){
		$strippedSystem = parent::stripForJson();
		if (!empty($this->sustainedTarget)){
			$strippedSystem->sustainedTarget = $this->sustainedTarget;
		}
		$strippedSystem->maxDice = $this->maxDice;
		return $strippedSystem;
	}

}//endof class HyperplasmaCutter
	
	
?>
