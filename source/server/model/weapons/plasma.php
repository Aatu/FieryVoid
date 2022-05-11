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
	/*
	public $animation = "trail";
	public $animationColor = array(75, 250, 90);
	public $trailColor = array(75, 250, 90);
	public $projectilespeed = 12;
	public $animationWidth = 2;
	public $trailLength = 10;
	public $animationExplosionScale = 0.1;
*/
	public $intercept = 2;
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
		$this->intercept = $nrOfShots;            
		
		
		if($nrOfShots === 1){
			$this->iconPath = "pairedPlasmaBlaster1.png";
		}
		if($nrOfShots >2){//no special icon for more than 3 linked weapons
			$this->iconPath = "pairedPlasmaBlaster3.png";
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
	/*
        public $animation = "trail";
        public $animationColor = array(75, 250, 90);
        public $trailColor = array(75, 250, 90);
        public $projectilespeed = 11;
        public $animationWidth = 4;
        public $trailLength = 12;
        public $animationExplosionScale = 0.25;
        */
	public $rangeDamagePenalty = 1;
                
        public $loadingtime = 2;
        public $exlusive = true;
	public $priority = 5;
            
        public $rangePenalty = 0.66;
        public $fireControl = array(-6, 4, 4); // fighters, <=mediums, <=capitals 


        function __construct($startArc, $endArc, $shots = 1){
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
    public $animationColor = array(75, 250, 90); //...it's not inheriting from Plasma, so needs to have proper color declared
	/*
        public $animation = "trail";
        public $animationColor = array(75, 250, 90);
        public $trailColor = array(75, 250, 90);
        public $projectilespeed = 11;
        public $animationWidth = 4;
        public $trailLength = 12;
        public $animationExplosionScale = 0.25;
*/
	
        public $intercept = 0; //no interception for this weapon!
        public $loadingtime = 1;
        public $shots = 2;
        public $defaultShots = 2;
        public $rangePenalty = 2;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals
        public $rangeDamagePenalty = 1;
		public $damageBonus = 5;
		public $priority = 5;

    	public $damageType = "Standard"; 
    	public $weaponClass = "Plasma"; 

	function __construct($startArc, $endArc, $damageBonus=5, $nrOfShots = 2){
            $this->shots = $nrOfShots;
            $this->defaultShots = $nrOfShots;
	    $this->damageBonus = $damageBonus;
	    
        if($nrOfShots === 1){
			$this->iconPath = "lightPlasma.png";
		}
		if($nrOfShots >= 2){
			$this->iconPath = "lightPlasmalinked.png";
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
	/*
        public $animation = "trail";
        public $animationColor = array(75, 250, 90);
        public $trailColor = array(75, 250, 90);
        public $projectilespeed = 10;
        public $animationWidth = 4;
        public $trailLength = 13;
        public $animationExplosionScale = 0.23;
*/

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
	/*
		public $animation = "trail";
		public $trailColor = array(75, 250, 90);
		public $animationColor = array(75, 250, 90);
		public $projectilespeed = 12;
		public $animationExplosionScalearray = array(1=>0.10, 2=>0.20);
		public $animationWidtharray = array(1=>2, 2=>3);
		public $trailLengtharray = array(1=>10, 2=>15);
        */
	
        public $intercept = 2; //this weapon can intercept, surprisingly...
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
	/*
	public $animation = "trail";
	public $animationColor = array(75, 250, 90);
	public $animationExplosionScale = 0.5;
	public $projectilespeed = 12;
	public $animationWidth = 4;
	public $trailLength = 4;
	*/
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
	/*
	public $animation = "trail";
	public $animationColor = array(75, 250, 90);
	public $animationExplosionScale = 0.4;
	public $projectilespeed = 14;
	public $animationWidth = 2;
	public $trailLength = 2;
	*/
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
	/*
	public $animation = "trail";
	public $animationColor = array(75, 250, 90);
	public $animationExplosionScale = 0.3;
	public $projectilespeed = 16;
	public $animationWidth = 1;
	public $trailLength = 1;
	*/
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
/*
    public $animation = "trail";
    public $animationColor = array(75, 250, 90);
    public $trailColor = array(75, 250, 90);
    public $projectilespeed = 11;
    public $animationWidth = 4;
    public $trailLength = 12;
    public $animationExplosionScale = 0.25;
*/	
    public $intercept = 0; //no interception for this weapon!
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
	
	/*
	public $animationArray = array(1=>'trail', 2=>'trail');
        public $animationColor = array(75, 250, 90);
        public $animationWidthArray = array(1=>6, 2=>4);
        public $animationExplosionScale = 0.30;
    	public $projectilespeed = 14;	
    	public $trailColor = array(75, 250, 90);
    	public $trailLength = 18;
   	  */
	
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
	/*
        public $animation = "trail";
        public $animationColor = array(75, 250, 90);
    	public $trailColor = array(75, 250, 90);
    	public $projectilespeed = 17;
        public $animationWidth = 6;
    	public $animationExplosionScale = 0.35;
    	public $trailLength = 24;
    	    	*/
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
	/*
	public $animationColor = array(75, 250, 90);
    public $animationWidth = 4;
    public $animationWidth2 = 0.2;
    */
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
        //public $explosionColor = array(235,235,235);
        //public $projectilespeed = 12;
        //public $animationWidth = 10;
        //public $trailLength = 10;

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
/*  
	  public $animation = "trail";
    public $animationColor = array(255, 105, 0);
	public $trailColor = array(255, 140, 60);
	public $projectilespeed = 15;
    public $animationWidth = 6;
	public $animationExplosionScale = 0.80;
	public $trailLength = 30;
  */
    public $priority = 2;
    public $rangeDamagePenalty = 1;
		        
    public $loadingtime = 5;			
    public $rangePenalty = 0.33;
    public $fireControl = array(null, 3, 5); // fighters, <=mediums, <=capitals 

	public $damageType = "Flash"; 
	public $weaponClass = "Plasma"; 
	public $firingModes = array( 1 => "Flash"); 

	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
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
    /*
    public $animation = "trail";
    public $animationColor = array(255, 105, 0);
	public $trailColor = array(255, 140, 60);
	public $projectilespeed = 15;
    public $animationWidth = 6;
	public $animationExplosionScale = 0.70;
	public $trailLength = 30;
    */
    public $priority = 2;
    public $rangeDamagePenalty = 0.25;
		        
    public $loadingtime = 5;			
    public $rangePenalty = 0.25;
    public $fireControl = array(null, 3, 5);

	public $damageType = "Flash"; 
	public $weaponClass = "Plasma"; 
	public $firingModes = array( 1 => "Flash"); 

	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
		}
	
		
	public function getDamage($fireOrder){        return Dice::d(10,6)+12;   }
        public function setMinDamage(){     $this->minDamage = 18;      }
        public function setMaxDamage(){     $this->maxDamage = 72;      }

}//endof class RangedFuser


class DualPlasmaStream extends Raking{
	public $name = "DualPlasmaStream";
	public $displayName = "Dual Plasma Stream";
	public $iconPath = "DualPlasmaStream.png"; 	
	/*
	public $animation = "beam";
	public $animationColor = array(75, 250, 90);
	public $trailColor = array(75, 250, 90);
	public $projectilespeed = 20;
	public $animationWidth = 4;
	public $animationExplosionScale = 0.30;
	public $trailLength = 400;
	*/
	public $priority = 2;
		        
	public $raking = 5;
	public $loadingtime = 2;
	public $rangeDamagePenalty = 2;	
	public $rangePenalty = 1;
	public $fireControl = array(-4, 2, 2);
	
	public $damageType = "Raking"; 
	public $weaponClass = "Plasma";

	public $firingModes = array(1 => "Raking");
	
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
	    $this->data["Special"] .= "Damage reduced by 2 points per hex.";
	    $this->data["Special"] .= "<br>Reduces armor of systems hit.";	
	    $this->data["Special"] .= "<br>Ignores half of armor.";
	}
		 
	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){
		parent::onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder);
		if (!$system->advancedArmor){//advanced armor prevents effect 
			$crit = new ArmorReduced(-1, $ship->id, $system->id, "ArmorReduced", $gamedata->turn);
			$crit->updated = true;
			    $crit->inEffect = false;
			    $system->criticals[] =  $crit;
		}
	}
		
	public function getDamage($fireOrder){        return Dice::d(10,6)+8;   }
	public function setMinDamage(){     $this->minDamage = 14;     }
	public function setMaxDamage(){     $this->maxDamage = 68;      }
	
}//endof class DualPlasmaStream

class PakmaraPlasmaWeb extends Weapon implements DefensiveSystem{        
        public $name = "PakmaraPlasmaWeb";
        public $displayName = "Plasma Web";
		public $iconPath = "PlasmaWeb.png";
 	    public $animation = "bolt";
   //     public $animationArray = array(1=>"bolt", 2=>"laser", 3=>"laser");
        //public $projectilespeed = 7;
        //public $animationWidth = 3;
        //public $trailLength = 10;       
        //public $animationColor = array(0, 184, 230);
        //public $animationWidth2 = 0.2;
  //      public $animationExplosionScale = 0.2; 
  //      public $animationExplosionType = "AoE";               
		
  //      public $ballistic = false;
        public $hextarget = true;
        public $hidetarget = false;
        public $priority = 1; //to show effect quickly
                
        public $uninterceptable = true; //just so nothing tries to actually intercept this weapon
        public $doNotIntercept = true; //do not intercept this weapon, period
		public $canInterceptUninterceptable = true; //able to intercept shots that are normally uninterceptable, eg. Lasers
        public $useOEW = false; //not important, really
        		
		public $priorityArray = array(1=>1, 2=>1); //Both modes should be fired very early???
        public $loadingtime = 1;
        public $intercept = 0; 

	    public $tohitPenalty = 0;
	    public $damagePenalty = 0;        
         		
		public $range = 100;
        public $rangeArray = array(1=>100, 2=>3); //range is essentially unlimited for Defensive, but limited for Offensive.
		public $rangePenaltyArray = array(1=>0, 2=>0); //no range penalty in either mode                  
        
        public $boostable = true;
        public $boostEfficiency = 1;
        public $maxBoostLevel = 1;     
		
    	public $weaponClass = "Plasma";
		public $damageTypeArray = array(1=>'Standard', 2=>'Plasma'); //indicates that this weapon does Plasma damage in Offensive mode    	
    	public $firingMode = "Defensive";
        public $firingModes = array(
            1 => "Defensive",
			2 => "Offensive",			
        );
    
        public $fireControlArray = array( 1=>array(50,50,50), 2=>array(50, null, null)); // fighters, <mediums, <capitals 
		
		private static $alreadyEngaged = array(); //units that were already engaged by a Plasma Web this turn (multiple Webs do not stack).

 //   public $possibleCriticals = array(
 //           17=>array("ReducedRange", "DamageReductionRemoved"));  /Need to create two unique critical effects for Web, reduce intercept rating by 1, and reduced range of offensive mode by 2.

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
    
    public function getDefensiveHitChangeMod($target, $shooter, $pos, $turn, $weapon){ //no defensive hit chance change
			switch($this->firingMode){
				case 1:					
						$output = 2;
				        return $output;
			break;
				case 2:	
						$output = 0;
				        return $output;	        
   				 }
			}

    public function getDefensiveDamageMod($target, $shooter, $pos, $turn, $weapon){
			switch($this->firingMode){
				case 1:					
						$output = 2;
				        return $output;
			break;
				case 2:	
						$output = 0;
				        return $output;	        
   				 }
			}
//end Defensive system functions

		public function calculateHitBase($gamedata, $fireOrder)
		{
			$this->changeFiringMode($fireOrder->firingMode);
	//		if($this->firingMode ==1) { //Shredder
				//against hex - it shouldn't even be resolved
					if ($fireOrder->targetid != -1) {
						$targetship = $gamedata->getShipById($fireOrder->targetid);
						//insert correct target coordinates: last turns' target position
						$targetPos = $targetship->getHexPos();
						$fireOrder->x = $targetPos->q;
						$fireOrder->y = $targetPos->r;
						$fireOrder->targetid = -1; //correct the error
						$fireOrder->calledid = -1; //just in case
					
									
			//	if ($fireOrder->targetid == -1) {				
					$fireOrder->needed = 100;			
					$fireOrder->updated = true;
					$fireOrder->notes .= 'Plasma Web aiming shot, not resolved.';
					return;
		//		} 
			} 

		}		
		
		
	public function fire($gamedata, $fireOrder){
//			$this->changeFiringMode($fireOrder->firingMode);  //Already called in calculateHitBase
			
			switch($this->firingMode){
				case 1:	
			$shooter = $gamedata->getShipById($fireOrder->shooterid);
       		$target = $gamedata->getShipById($fireOrder->targetid);
			$pos = null;
//You define firing location as that from beginning of turn, like for ballistics (so incorrectly here). Using current ship location would be appropriate for direct fire.
//			$movement = $shooter->getLastTurnMovement($fireOrder->turn);
//			$posLaunch = $movement->position;//at moment of launch!!!
					
			//$this->calculateHit($gamedata, $fireOrder); //already calculated!
			$rolled = Dice::d(100);
			$fireOrder->rolled = $rolled; ///and auto-hit ;)
			$fireOrder->shotshit++;
			$fireOrder->pubnotes .= "Interception and damage reduction applied to all weapons at target hex that are firing at Plasma Web-launching ship. "; //just information for player, actual applying was done in calculateHitBase method

			$fireOrder->rolled = max(1, $fireOrder->rolled);//Marks that fire order has been handled, just in case it wasn't marked yet!
			TacGamedata::$lastFiringResolutionNo++;    //note for further shots
			$fireOrder->resolutionOrder = TacGamedata::$lastFiringResolutionNo;//mark order in which firing was handled!					
				
					break;
				case 2:		
		$shooter = $gamedata->getShipById($fireOrder->shooterid);

		
//You define firing location as that from beginning of turn, like for ballistics (so incorrectly here). Using current ship location would be appropriate for direct fire.		
		$movement = $shooter->getLastTurnMovement($fireOrder->turn);
		$posLaunch = $movement->position;//at moment of launch!!!	
		
			
		//$this->calculateHit($gamedata, $fireOrder); //already calculated!
		$rolled = Dice::d(100);
		$fireOrder->rolled = $rolled; ///and auto-hit ;)
		$fireOrder->shotshit++;
		$fireOrder->pubnotes .= "All fighters in target hex take damage"; //just information for player, actual applying was done in calculateHitBase method		

		//deal damage!
        $target = new OffsetCoordinate($fireOrder->x, $fireOrder->y);
        $ships1 = $gamedata->getShipsInDistance($target); //all ships on target hex
        foreach ($ships1 as $targetShip) if ($targetShip instanceOf FighterFlight) {
            $this->AOEdamage($targetShip, $shooter, $fireOrder, $gamedata);


        }

		$fireOrder->rolled = max(1, $fireOrder->rolled);//Marks that fire order has been handled, just in case it wasn't marked yet!
		TacGamedata::$lastFiringResolutionNo++;    //note for further shots
		$fireOrder->resolutionOrder = TacGamedata::$lastFiringResolutionNo;//mark order in which firing was handled!
			}
	} //endof function fire		


//and now actual damage dealing for Offensive Mode - and we already know fighter is hit (fire()) doesn't pass anything else)
//source hex will be taken from firing unit, damage will be individually rolled for each fighter hit
 public function AOEdamage($target, $shooter, $fireOrder, $gamedata)    {
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
		
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
			if (!isset($this->data["Special"])) {
				$this->data["Special"] = '';
			}else{
				$this->data["Special"] .= '<br>';
			}
			$this->data["Special"] .= 'Defensive mode automatically hits an enemy unit, it then applies -10 intercept rating and 2 damage reduction against ALL invoming enemy fire from that hex.';
			$this->data["Special"] .= '<br>Offensive Mode targets a hex within 3 hexes of firing unit and deals D6+2 damage to all fighters in that hex.';
			$this->data["Special"] .= '<br>Offensive Mode requires 1 additional power either from boosting in Initial Orders phase or from space capacity stored in plasma batteries in Firing Phase.';
			$this->data["Special"] .= '<br>Multiple Plasma Webs do not apply their effects cumulatively.'; //uninterceptability is due to technical reasons - with no fire order ID, interception will not be applied properly
	 }
               
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

	} //end of class PakmaraPlasmaWeb

	
?>
