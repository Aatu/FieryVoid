<?php

class PlasmaStream extends Raking{
	public $name = "plasmaStream";
	public $displayName = "Plasma Stream";
	public $animation = "beam";
	public $animationColor = array(75, 250, 90);
	public $trailColor = array(75, 250, 90);
	public $projectilespeed = 20;
	public $animationWidth = 3;
	public $animationExplosionScale = 0.25;
	public $trailLength = 400;
	public $priority = 1;
		        
	public $raking = 5;
	public $loadingtime = 2;
	public $rangeDamagePenalty = 1;	
	public $rangePenalty = 1;
	public $fireControl = array(-4, 2, 2); // fighters, <=mediums, <=capitals 
	
	public $damageType = "Raking"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	public $weaponClass = "Plasma"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!

		public $firingModes = array(
			1 => "Raking"
		);
	
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
	}
        
	
	public function setSystemDataWindow($turn){
	    $this->data["Special"] = "Reduces armor of hit systems.";
	    $this->data["Special"] .= "5-point rakes.";
	    $this->data["Special"] .= "Damage reduced by 1 point per hex.";			
		parent::setSystemDataWindow($turn);
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
	
	
	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){
		parent::onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder);
		if (!$system->advancedArmor){
			$crit = new ArmorReduced(-1, $ship->id, $system->id, "ArmorReduced", $gamedata->turn);
			$crit->updated = true;
			    $crit->inEffect = false;
			    $system->criticals[] =  $crit;
		}
	}
		
		
	public function getDamage($fireOrder){        return Dice::d(10,3)+4;   }
	public function setMinDamage(){     $this->minDamage = 7 ;/*- $this->dp;*/      }
	public function setMaxDamage(){     $this->maxDamage = 34 /*- $this->dp*/;      }
}//endof class PlasmaStream




class ShockCannon extends Weapon{
        public $name = "shockCannon";
        public $displayName = "Shock Cannon";
        public $animation = "laser";
        public $animationColor = array(175, 225, 175);
        public $trailColor = array(175, 225, 175);
        public $projectilespeed = 15;
        public $animationWidth = 2;
        public $animationWidth2 = 0.2;
        public $animationExplosionScale = 0.15;
        public $trailLength = 30;
        public $priority = 3; //dropout effect on fighters

        public $loadingtime = 2;

        public $rangePenalty = 1;
        public $fireControl = array(3, 3, 3); // fighters, <=mediums, <=capitals

		public $damageType = "Standard"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	    public $weaponClass = "Electromagnetic"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!
	
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
        }

        // Shock Cannons ignore armor.
        protected function getSystemArmour($system, $gamedata, $fireOrder, $pos=null){
            return 0;
	}

        protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){
		if (!$system->advancedArmor){
		    $crit = null;

		    if ($system instanceof Fighter && !($ship instanceof SuperHeavyFighter)){
			$crit = new DisengagedFighter(-1, $ship->id, $system->id, "DisengagedFighter", $gamedata->turn);
			$crit->updated = true;
			$crit->inEffect = true;
			$system->criticals[] =  $crit;
			$fireOrder->pubnotes .= " DROPOUT! ";
		    }else if ($system instanceof Structure){
			$reactor = $ship->getSystemByName("Reactor");
			$outputMod = -round($damage/4);
			$crit = new OutputReduced(-1, $ship->id, $reactor->id, "OutputReduced", $gamedata->turn, $outputMod);
			$crit->updated = true;
			$reactor->criticals[] =  $crit;
		    }
		}

            parent::onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder);
	}//endof function onDamagedSystem

        public function getDamage($fireOrder){        return Dice::d(10)+4;   }
        public function setMinDamage(){     $this->minDamage = 5 /*- $this->dp*/;      }
        public function setMaxDamage(){     $this->maxDamage = 14 /*- $this->dp*/;      }
}//endof class ShockCannon



class BurstBeam extends Weapon{
	public $name = "burstBeam";
        public $displayName = "Burst Beam";
        public $animation = "laser";
        public $animationColor = array(158, 240, 255);
	public $trailColor = array(158, 240, 255);
	public $projectilespeed = 15;
        public $animationWidth = 2;
        public $animationWidth2 = 0.2;
        public $animationExplosionScale = 0.10;
	public $trailLength = 30;
		        
	public $loadingtime = 1;
        public $priority = 9; //as antiship weapon; as antifighter should go first...
        
			
        public $rangePenalty = 2;
        public $fireControl = array(4, 2, 2); // fighters, <=mediums, <=capitals 

	public $damageType = "Standard"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	public $weaponClass = "Electromagnetic"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!

	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
       
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		$this->data["Special"] = 'Forces dropout on fighters, turns off powered systems, causes extra criticals, can cause power shortages. ';
	}
		
	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){
		$crit = null;
		
		if (!$system->advancedArmor){
			if ($system instanceof Fighter && !($ship instanceof SuperHeavyFighter)){
				$crit = new DisengagedFighter(-1, $ship->id, $system->id, "DisengagedFighter", $gamedata->turn);
				$crit->updated = true;
				$crit->inEffect = true;
				$system->criticals[] =  $crit;
				$fireOrder->pubnotes .= " DROPOUT! ";
			}else if ($system instanceof Structure){
				$reactor = $ship->getSystemByName("Reactor");
				$crit = new OutputReduced1(-1, $ship->id, $reactor->id, "OutputReduced1", $gamedata->turn);
				$crit->updated = true;
				$reactor->criticals[] =  $crit;
			}else if ($system->powerReq > 0 || $system->canOffLine ){
				$system->addCritical($ship->id, "ForcedOfflineOneTurn", $gamedata);
			} else { //force critical roll at +4
				$system->forceCriticalRoll = true;
				$system->critRollMod += 4;
			    }
		}
	}		
		
		public function getDamage($fireOrder){        return 0;   }
		public function setMinDamage(){     $this->minDamage = 0;      }
		public function setMaxDamage(){     $this->maxDamage = 0;      }
}//endof class BurstBeam


	class DualBurstBeam extends BurstBeam{
		public $name = "dualBurstBeam";
		public $displayName = "Dual Burst Beam";
		public $guns = 2;
	}


class BurstPulseCannon extends Pulse {
	public $name = "burstPulseCannon";
        public $displayName = "Burst Pulse Cannon";

        public $animationColor = array(158, 240, 255);
		public $trailColor = array(158, 240, 255);

        public $animation = "trail";
        public $trailLength = 2;
        public $animationWidth = 4;
        public $projectilespeed = 17;
        public $animationExplosionScale = 0.05;
        public $rof = 5;
        public $grouping = 25;
        public $maxpulses = 6;
		        
	    public $loadingtime = 1;
        public $priority = 9;
        
    public $damageType = "Pulse"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    public $weaponClass = "Electromagnetic"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!	
			
        public $rangePenalty = 0.5;
        public $fireControl = array(2, 3, 4); // fighters, <=mediums, <=capitals 


	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
       
		public function setSystemDataWindow($turn){
			//$this->data["Weapon type"] = "Electromagnetic";
			parent::setSystemDataWindow($turn);
		}

		
		protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){
			$crit = null;
			
			if ($system->advancedArmor) return;
			
		    if ($system instanceof Fighter && !($ship instanceof SuperHeavyFighter)){
					$crit = new DisengagedFighter(-1, $ship->id, $system->id, "DisengagedFighter", $gamedata->turn);
					$crit->updated = true;
					$crit->inEffect = true;
					$system->criticals[] =  $crit;
					$fireOrder->pubnotes .= " DROPOUT! ";
		    }else if ($system instanceof Structure){
					$reactor = $ship->getSystemByName("Reactor");
					$crit = new OutputReduced1(-1, $ship->id, $reactor->id, "OutputReduced1", $gamedata->turn);
					$crit->updated = true;
					$reactor->criticals[] =  $crit;
				}else if ($system->powerReq > 0 || $system->canOffLine ){
				$crit = new ForcedOfflineOneTurn (-1, $ship->id, $system->id, "ForcedOfflineOneTurn", $gamedata->turn);
				$crit->updated = true;
				$system->criticals[] = $crit;
					}
			    else {//force critical roll at +4
					$system->forceCriticalRoll = true;
					$system->critRollMod += 4;
			}
		}
		
		
		public function getDamage($fireOrder){        return 0;   }
		public function setMinDamage(){     $this->minDamage = 0;      }
		public function setMaxDamage(){     $this->maxDamage = 0;      }
	}



    class MediumBurstBeam extends BurstBeam{
        public $name = "mediumBurstBeam";
        public $displayName = "Medium Burst Beam";

        public $animationColor = array(158, 240, 255);
		public $trailColor = array(158, 240, 255);
		public $projectilespeed = 12;
        public $animationWidth = 3;
        public $animationWidth2 = 0.4;
        public $animationExplosionScale = 0.20;
		public $trailLength = 40;

        public $loadingtime = 2;
        public $priority = 9;

        public $rangePenalty = 0.5;
        public $fireControl = array(0, 3, 4); // fighters, <=mediums, <=capitals 

	    public $damageType = "Standard"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	    public $weaponClass = "Electromagnetic"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set! 
	    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){
            $crit = null;
		
		if ($system->advancedArmor) return;

            if ($system instanceof Fighter){
                if (!$ship instanceof SuperHeavyFighter){
                    $crit = new DisengagedFighter(-1, $ship->id, $system->id, "DisengagedFighter", $gamedata->turn);
                    $crit->updated = true;
                    $crit->inEffect = true;
                    $system->criticals[] =  $crit;
			$fireOrder->pubnotes .= " DROPOUT! ";
                }
                else {
                    $roll = Dice::d(6);
                    if ($roll < 2){
                        $crit = new DisengagedFighter(-1, $ship->id, $system->id, "DisengagedFighter", $gamedata->turn);
                        $crit->updated = true;
                        $crit->inEffect = true;
                        $system->criticals[] =  $crit;
			$fireOrder->pubnotes .= " DROPOUT! ";
                    }
                }
            }
            else if ($system instanceof Structure){
                $reactor = $ship->getSystemByName("Reactor");
                $crit = new OutputReduced1(-1, $ship->id, $reactor->id, "OutputReduced2", $gamedata->turn);
                $crit->updated = true;
                $reactor->criticals[] =  $crit;
            }
            else if ($system->powerReq > 0 || $system->canOffLine ){
                $crit = new ForcedOfflineForTurns (-1, $ship->id, $system->id, "ForcedOfflineForTurns", $gamedata->turn, 2);
                $crit->updated = true;
                $system->criticals[] = $crit;
            }
            else {//force critical roll at +6
		$system->forceCriticalRoll = true;
		$system->critRollMod += 6;
            }
        }    
    }



    class HeavyBurstBeam extends BurstBeam{
        public $name = "heavyBurstBeam";
        public $displayName = "Heavy Burst Beam";

        public $animationColor = array(158, 240, 255);
		public $trailColor = array(158, 240, 255);
		public $projectilespeed = 10;
        public $animationWidth = 4;
        public $animationWidth2 = 0.5;
        public $animationExplosionScale = 0.30;
		public $trailLength = 50;

        public $loadingtime = 3;
        public $priority = 9;

        public $rangePenalty = 0.33;
        public $fireControl = array(2, 4, 5); // fighters, <=mediums, <=capitals 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){
            $crit = null;
            if ($system->advancedArmor) return;
            if ($system instanceof Fighter){
                if (!$ship instanceof SuperHeavyFighter){
                    $crit = new DisengagedFighter(-1, $ship->id, $system->id, "DisengagedFighter", $gamedata->turn);
                    $crit->updated = true;
                    $crit->inEffect = true;
                    $system->criticals[] =  $crit;
			$fireOrder->pubnotes .= " DROPOUT! ";
                }
                else {
                    $roll = Dice::d(6);
                    if ($roll < 3){
                        $crit = new DisengagedFighter(-1, $ship->id, $system->id, "DisengagedFighter", $gamedata->turn);
                        $crit->updated = true;
                        $crit->inEffect = true;
                        $system->criticals[] =  $crit;
			$fireOrder->pubnotes .= " DROPOUT! ";
                    }
                }
            }
            else if ($system instanceof Structure){
                $reactor = $ship->getSystemByName("Reactor");
                $crit = new OutputReduced1(-1, $ship->id, $reactor->id, "OutputReduced4", $gamedata->turn);
                $crit->updated = true;
                $reactor->criticals[] =  $crit;
            }
            else if ($system->powerReq > 0 || $system->canOffLine ){
                $crit = new ForcedOfflineForTurns (-1, $ship->id, $system->id, "ForcedOfflineForTurns", $gamedata->turn, 3);
                $crit->updated = true;
            	$system->criticals[] = $crit;
            }
            else {//force critical roll at +6
			$system->forceCriticalRoll = true;
			$system->critRollMod += 6;
            }
        }    
    }

    
    class TractorBeam extends ShipSystem{
        public $name = "tractorBeam";
        public $displayName = "Tractor Beam";
      
        function __construct($armour, $maxhealth, $powerReq, $output ){
            parent::__construct($armour, $maxhealth, $powerReq, $output );
        }
    }


    class ElectroPulseGun extends Weapon{
        public $name = "electroPulseGun";
        public $displayName = "Electro-Pulse Gun";
        public $animation = "laser";
        public $animationColor = array(158, 240, 255);
        public $trailColor = array(158, 240, 255);
        public $projectilespeed = 15;
        public $animationWidth = 2;
        public $animationWidth2 = 0.2;
        public $animationExplosionScale = 0.10;
        public $trailLength = 30;
        public $priority = 1;

        public $loadingtime = 2;
        public $rangePenalty = 3;
        public $fireControl = array(3, null, null); // fighters, <=mediums, <=capitals
	    public $calledShotMod = 0; //can call shot at no penalty! (eg. pick off undamaged fighter)

	    public $damageType = "Standard"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	    public $weaponClass = "Electromagnetic"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
            //$this->data["Weapon type"] = "Electromagnetic";

            parent::setSystemDataWindow($turn);
		$this->data["Special"] = 'Forces dropout on fighters. ';
        }

        protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){
            // On a hit, make fighters drop out, but if this weapon had
            // a ReducedDamage crit, roll a d6 and substract 2 for each
            // ReducedDamage crit. If the result is less than 1, the hit
            // has no effect on the fighter.
		if (!$system->advancedArmor){
		    $crit = null;
		    $affect = Dice::d(6);

		    foreach ($this->criticals as $crit){
			if ($crit instanceof ReducedDamage){
			    $affect = $affect - 2;
			}
		    }

		    if ( ($system instanceof Fighter) && (!($ship instanceof SuperHeavyFighter)) && ($affect > 0)){
			$crit = new DisengagedFighter(-1, $ship->id, $system->id, "DisengagedFighter", $gamedata->turn);
			$crit->updated = true;
			$crit->inEffect = true;
			$system->criticals[] =  $crit;
			$fireOrder->pubnotes .= " DROPOUT! ";
		    }
		}
            
            parent::onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder);
        }


        public function getDamage($fireOrder){        return 0;   }
        public function setMinDamage(){     $this->minDamage = 0;      }
        public function setMaxDamage(){     $this->maxDamage = 0;      }
    }



class StunBeam extends Weapon{
	public $name = "StunBeam";
        public $displayName = "Stun Beam";
	   public  $iconPath = "stunBeam.png";
        public $animation = "laser";
        public $animationColor = array(158, 240, 255);
	public $trailColor = array(158, 240, 255);
	public $projectilespeed = 15;
        public $animationWidth = 2;
        public $animationWidth2 = 0.2;
        public $animationExplosionScale = 0.10;
	public $trailLength = 30;
		        
	public $loadingtime = 2;
        public $priority = 9; //as antiship weapon; as antifighter should go first...
        
			
        public $rangePenalty = 1;
        public $fireControl = array(0, 2, 4); // fighters, <=mediums, <=capitals 
		    
	public $damageType = "Standard"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	public $weaponClass = "Electromagnetic"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!

	
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
	    //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 6;
            }
            if ( $powerReq == 0 ){
                $powerReq = 5;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
       
		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			$this->data["Special"] = 'Forces dropout on fighters, turns off powered systems. ';
		}
		
		protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){
			$crit = null;
            		if ($system->advancedArmor) return;
			if ($system instanceof Fighter && !($ship instanceof SuperHeavyFighter)){
				$crit = new DisengagedFighter(-1, $ship->id, $system->id, "DisengagedFighter", $gamedata->turn);
				$crit->updated = true;
                		$crit->inEffect = true;
				$system->criticals[] =  $crit;
				$fireOrder->pubnotes .= " DROPOUT! ";
            		}else if ($system->powerReq > 0 || $system->canOffLine ){
				$system->addCritical($ship->id, "ForcedOfflineOneTurn", $gamedata);
			}
		}
		
		
		public function getDamage($fireOrder){        return 0;   }
		public function setMinDamage(){     $this->minDamage = 0;      }
		public function setMaxDamage(){     $this->maxDamage = 0;      }
}//endof class StunBeam




class CommDisruptor extends Weapon{
    /*Abbai weapon - does no damage, but limits target's Initiative and Sensors next turn
    */
    public $name = "CommDisruptor";
    public $displayName = "Comm Disruptor";
	public $iconPath = "commDIsruptor.png";
	
    public $priority = 10; //let's fire last, order not all that important here!
    public $loadingtime = 3;
    public $rangePenalty = 0.5; //-1/2 hexes
    public $intercept = 0;
    public $fireControl = array(-1, 2, 3);
	
	public $damageType = "Standard"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	public $weaponClass = "Electromagnetic"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!
   
	   
	//let's animate this as a very wide beam...
	public $animation = "laser";
        public $animationColor = array(150, 150, 220);
        public $animationColor2 = array(170, 170, 250);
        public $animationExplosionScale = 0.45;
        public $animationWidth = 15;
        public $animationWidth2 = 0.5;
	
 	public $possibleCriticals = array( //no point in damage reduced crit
            14=>"ReducedRange"
	);
	
    public function setSystemDataWindow($turn){
	      $this->data["Special"] = "Does no damage, but weakens target's Initiative (-1d6) and Sensors (-1d6) rating next turn";  
	      parent::setSystemDataWindow($turn);    
    }	
    
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
	    //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 6;
            }
            if ( $powerReq == 0 ){
                $powerReq = 3;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ //really no matter what exactly was hit!
		if ($system->advancedArmor) return; //no effect on Advanced Armor
		
		$effectIni = Dice::d(6,1);//strength of effect: 1d6
		$effectSensors = Dice::d(6,1);//strength of effect: 1d6
		$effectIni5 = $effectIni * 5;
		$fireOrder->pubnotes .= "<br> Initiative reduced by $effectIni5, Sensors by $effectSensors.";
		
		if ($ship instanceof FighterFlight){  //place effect on first fighter, even if it's already destroyed!
			$firstFighter = $ship->getSampleFighter();
			if($firstFighter){
				for($i=1; $i<=$effectSensors;$i++){
					$crit = new tmpsensordown(-1, $ship->id, $firstFighter->id, 'tmpsensordown', $gamedata->turn); 
					$crit->updated = true;
			        	$firstFighter->criticals[] =  $crit;
				}
				for($i=1; $i<=$effectIni;$i++){
					$crit = new tmpinidown(-1, $ship->id, $firstFighter->id, 'tmpinidown', $gamedata->turn); 
					$crit->updated = true;
			        	$firstFighter->criticals[] =  $crit;
				}
			}
		}else{ //ship - place effcet on C&C!
			$CnC = $ship->getSystemByName("CnC");
			if($CnC){
				for($i=1; $i<=$effectSensors;$i++){
					$crit = new tmpsensordown(-1, $ship->id, $CnC->id, 'tmpsensordown', $gamedata->turn); 
					$crit->updated = true;
			        	$CnC->criticals[] =  $crit;
				}
				for($i=1; $i<=$effectIni;$i++){
					$crit = new tmpinidown(-1, $ship->id, $CnC->id, 'tmpinidown', $gamedata->turn); 
					$crit->updated = true;
			        	$CnC->criticals[] =  $crit;
				}
			}
		}
	} //endof function onDamagedSystem

	public function getDamage($fireOrder){ return  0;   }
	public function setMinDamage(){   $this->minDamage =  0 ;      }
	public function setMaxDamage(){   $this->maxDamage =  0 ;      }
} //end of class CommDisruptor



class CommJammer extends Weapon{
    /*Abbai weapon - does no damage, but limits target's Initiative  next turn
    */
    public $name = "CommJammer";
    public $displayName = "Comm Jammer";
	public $iconPath = "commJammer.png";
	
    public $priority = 10; //let's fire last, order not all that important here!
    public $loadingtime = 3;
    public $rangePenalty = 1; //-1/hex
    public $intercept = 0;
    public $fireControl = array(0, 2, 2);
	
	public $damageType = "Standard"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	public $weaponClass = "Electromagnetic"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!
   
	   
	//let's animate this as a very wide beam...
	public $animation = "laser";
        public $animationColor = array(150, 150, 220);
        public $animationColor2 = array(160, 160, 240);
        public $animationExplosionScale = 0.25;
        public $animationWidth = 10;
        public $animationWidth2 = 0.5;
	
 	public $possibleCriticals = array( //no point in damage reduced crit
            14=>"ReducedRange"
	);
	
    public function setSystemDataWindow($turn){
	      $this->data["Special"] = "Does no damage, but weakens target's Initiative (-1d6) rating next turn";  
	      parent::setSystemDataWindow($turn);    
    }	
    
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
	    //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 4;
            }
            if ( $powerReq == 0 ){
                $powerReq = 3;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ //really no matter what exactly was hit!
		if ($system->advancedArmor) return; //no effect on Advanced Armor
		
		$effectIni = Dice::d(6,1);//strength of effect: 1d6
		$effectIni5 = $effectIni * 5;
		$fireOrder->pubnotes .= "<br> Initiative reduced by $effectIni5.";
		
		if ($ship instanceof FighterFlight){  //place effect on first fighter, even if it's already destroyed!
			$firstFighter = $ship->getSampleFighter();
			if($firstFighter){
				for($i=1; $i<=$effectIni;$i++){
					$crit = new tmpinidown(-1, $ship->id, $firstFighter->id, 'tmpinidown', $gamedata->turn); 
					$crit->updated = true;
			        	$firstFighter->criticals[] =  $crit;
				}
			}
		}else{ //ship - place effcet on C&C!
			$CnC = $ship->getSystemByName("CnC");
			if($CnC){
				for($i=1; $i<=$effectIni;$i++){
					$crit = new tmpinidown(-1, $ship->id, $CnC->id, 'tmpinidown', $gamedata->turn); 
					$crit->updated = true;
			        	$CnC->criticals[] =  $crit;
				}
			}
		}
	} //endof function onDamagedSystem

	public function getDamage($fireOrder){ return  0;   }
	public function setMinDamage(){   $this->minDamage =  0 ;      }
	public function setMaxDamage(){   $this->maxDamage =  0 ;      }
} //end of class CommJammer


class ImpCommJammer extends CommJammer{
    /*Abbai weapon - does no damage, but limits target's Initiative and Sensors next turn
    */
    public $name = "ImpCommJammer";
    public $displayName = "Improved Comm Jammer";
	public $iconPath = "commJammer.png";
	
    public $rangePenalty = 0.5; //-1/2 hexes
} //end of class ImpCommJammer




class SensorSpear extends Weapon{
    /*Abbai weapon - does no damage, but limits target's Sensors next turn
    */
    public $name = "SensorSpear";
    public $displayName = "Sensor Spear";
	public $iconPath = "sensorSpike.png";
	
    public $priority = 10; //let's fire last, order not all that important here!
    public $loadingtime = 2;
    public $rangePenalty = 0.5; //-1/2 hexes
    public $intercept = 0;
    public $fireControl = array(-1, 1, 1);
	
	public $damageType = "Standard"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	public $weaponClass = "Electromagnetic"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!
   
	   
	//let's animate this as a very wide beam...
	public $animation = "laser";
        public $animationColor = array(150, 150, 220);
        public $animationColor2 = array(160, 160, 240);
        public $animationExplosionScale = 0.25;
        public $animationWidth = 10;
        public $animationWidth2 = 0.5;
	
 	public $possibleCriticals = array( //no point in damage reduced crit
            14=>"ReducedRange"
	);
	
    public function setSystemDataWindow($turn){
	      $this->data["Special"] = "Does no damage, but weakens target's Sensors (-1d3) rating next turn";  
	      parent::setSystemDataWindow($turn);    
    }	
    
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
	    //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 6;
            }
            if ( $powerReq == 0 ){
                $powerReq = 3;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ //really no matter what exactly was hit!
		if ($system->advancedArmor) return; //no effect on Advanced Armor

		$effectSensors = Dice::d(3,1);//strength of effect: 1d3
		$fireOrder->pubnotes .= "<br> Sensors reduced by $effectSensors.";
		
		if ($ship instanceof FighterFlight){  //place effect on first fighter, even if it's already destroyed!
			$firstFighter = $ship->getSampleFighter();
			if($firstFighter){
				for($i=1; $i<=$effectSensors;$i++){
					$crit = new tmpsensordown(-1, $ship->id, $firstFighter->id, 'tmpsensordown', $gamedata->turn); 
					$crit->updated = true;
			        	$firstFighter->criticals[] =  $crit;
				}
			}
		}else{ //ship - place effcet on C&C!
			$CnC = $ship->getSystemByName("CnC");
			if($CnC){
				for($i=1; $i<=$effectSensors;$i++){
					$crit = new tmpsensordown(-1, $ship->id, $CnC->id, 'tmpsensordown', $gamedata->turn); 
					$crit->updated = true;
			        	$CnC->criticals[] =  $crit;
				}
			}
		}
	} //endof function onDamagedSystem

	public function getDamage($fireOrder){ return  0;   }
	public function setMinDamage(){   $this->minDamage =  0 ;      }
	public function setMaxDamage(){   $this->maxDamage =  0 ;      }
} //end of class SensorSpear


class SensorSpike extends SensorSpear{
    /*Abbai weapon - does no damage, but limits target's Sensors next turn
    */
    public $name = "SensorSpike";
    public $displayName = "Sensor Spike";
	public $iconPath = "sensorSpike.png";
	

    public $fireControl = array(-1, 1, 2);
	
	
    public function setSystemDataWindow($turn){
	      parent::setSystemDataWindow($turn);  
	      $this->data["Special"] = "Does no damage, but weakens target's Sensors (-1d6) rating next turn";  
    }	
    

	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ //really no matter what exactly was hit!
		if ($system->advancedArmor) return; //no effect on Advanced Armor

		$effectSensors = Dice::d(6,1);//strength of effect: 1d6
		$fireOrder->pubnotes .= "<br> Sensors reduced by $effectSensors.";
		
		if ($ship instanceof FighterFlight){  //place effect on first fighter, even if it's already destroyed!
			$firstFighter = $ship->getSampleFighter();
			if($firstFighter){
				for($i=1; $i<=$effectSensors;$i++){
					$crit = new tmpsensordown(-1, $ship->id, $firstFighter->id, 'tmpsensordown', $gamedata->turn); 
					$crit->updated = true;
			        	$firstFighter->criticals[] =  $crit;
				}
			}
		}else{ //ship - place effcet on C&C!
			$CnC = $ship->getSystemByName("CnC");
			if($CnC){
				for($i=1; $i<=$effectSensors;$i++){
					$crit = new tmpsensordown(-1, $ship->id, $CnC->id, 'tmpsensordown', $gamedata->turn); 
					$crit->updated = true;
			        	$CnC->criticals[] =  $crit;
				}
			}
		}
	} //endof function onDamagedSystem

	public function getDamage($fireOrder){ return  0;   }
	public function setMinDamage(){   $this->minDamage =  0 ;      }
	public function setMaxDamage(){   $this->maxDamage =  0 ;      }
} //end of class SensorSpike




class EmBolter extends Weapon{
    /*EM Bolter - Ipsha weapon*/
        public $name = "EmBolter";
        public $displayName = "EM Bolter";
	public $iconPath = "EMBolter.png";
	
        public $animation = "trail";
        public $animationColor = array(100, 100, 250);
        public $projectilespeed = 14;
        public $animationWidth = 5;
        public $animationExplosionScale = 0.45;
        public $priority = 6;
      
        public $loadingtime = 1;
        
        public $rangePenalty = 0.33; //-1/3 hexes
        public $fireControl = array(0, 3, 3); // fighters, <mediums, <capitals 
	
	
	    public $damageType = "Standard"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	    public $weaponClass = "Electromagnetic"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!

	private $cooldown = 2;
	
	private $alreadyResolved = false;
	
	    public function setSystemDataWindow($turn){
		      parent::setSystemDataWindow($turn);  
		      $this->data["Special"] = "Cooldown period: 2 turns.";  
		      $this->data["Special"] .= "<br>+1 to all critical rolls made by target this turn.";  
	    }	
	
	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ //really no matter what exactly was hit!
		parent::onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder);
		
		if ($system->advancedArmor) return; //no effect on Advanced Armor
		if ($this->alreadyResolved) return; //effect already applied this turn
		
		$this->alreadyResolved = true;
		if (!($ship instanceof FighterFlight)){
			$ship->critRollMod++; //+1 to all critical rolls made by target this turn 
		}

	} //endof function onDamagedSystem
	
	
        public function fire($gamedata, $fireOrder){
            // If fired, this weapon needs 2 turns cooldown period (=forced shutdown)
            parent::fire($gamedata, $fireOrder);
		for($i = 1; $i<=$this->cooldown;$i++){		
			$trgtTurn = $gamedata->turn+$i-1;//start on current turn rather than next!
			$crit = new ForcedOfflineOneTurn(-1, $fireOrder->shooterid, $this->id, "ForcedOfflineOneTurn", $trgtTurn);
			$crit->updated = true;
			$crit->newCrit = true; //force save even if crit is not for current turn
			$this->criticals[] =  $crit;
		}	
        } //endof function fire
	
	
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 10;
            }
            if ( $powerReq == 0 ){
                $powerReq = 9;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
	
        public function getDamage($fireOrder){        return 21;   }
        public function setMinDamage(){     $this->minDamage = 21 ;      }
        public function setMaxDamage(){     $this->maxDamage = 21 ;      }
} //endof class EmBolter



/*handles creation of firing orders for Spark Fields*/
class SparkFieldHandler{
	public $name = "sparkFieldHandler";
	private static $sparkFields = array();
	private static $firingDeclared = false;
	
	
	//should be called by every SparkField on creation!
	public static function addSparkField($weapon){
		SparkFieldHandler::$sparkFields[] = $weapon;		
	}
	
	//compares boost levels of fields
	//	lowest boost first (will potentially do more damage)
	//	owner irrelevant, as weapon will damage everything in range except firing unit itself
	public static function sortByBoost($fieldA, $fieldB){	    
		if ($fieldA->boostlevel < $fieldB->boostlevel){ //low boost level first
		    return -1;
		}else if ($fieldA->boostlevel > $fieldB->boostlevel){
		    return 1;
		}else{
		    return 0;
		}   
	} //endof function sortByBoost
	
	
	public static function createFiringOrders($gamedata){
		if (SparkFieldHandler::$firingDeclared) return; //already done!
		SparkFieldHandler::$firingDeclared = true;
		
		//apparently ships may be loaded multiple times... make sure fields in array belong to current gamedata!
		$tmpFields = array();
		foreach(SparkFieldHandler::$sparkFields as $field){
			$shooter = $field->getUnit();
			if($field->isDestroyed($gamedata->turn-1)) continue; //destroyed weapons can be safely left out
			//is this unit defined in current gamedata? (particular instance!)
			$belongs = $gamedata->shipBelongs($shooter);
			if ($belongs){
				$tmpFields[] = $field;
			}			
		}
		SparkFieldHandler::$sparkFields = $tmpFields;
		
		
		//make sure boost level for all weapons is calculated
		foreach(SparkFieldHandler::$sparkFields as $field){
			$field->calculateBoostLevel($gamedata->turn);
		}
		
		//sort all fields by boost
		usort(SparkFieldHandler::$sparkFields, "self::sortByBoost");	
	
		//table of units that are already targeted
		$alreadyTargeted = array();
		//create firing order for each weapon (target self)
		//for each weapon find possible targets and add them to weapons' target list
		//strongest weapons fire first, and only 1 field affects particular ship	
		foreach(SparkFieldHandler::$sparkFields as $field){			
			if ($field->isDestroyed($gamedata->turn-1)) continue; //destroyed field does not attack
			if ($field->isOfflineOnTurn($gamedata->turn)) continue; //disabled field does not attack
			$shooter = $field->getUnit();      
			$targetPos = $shooter->getCoPos();
			$movementThisTurn = $shooter->getLastTurnMovement($gamedata->turn+1);
			$fire = new FireOrder(-1, 'normal', $shooter->id, -1, $field->id, -1, $gamedata->turn, 
				1, 0, 0, 1, 0, 0, $movementThisTurn->position->q,  $movementThisTurn->position->r, $field->weaponClass
			);
			$fire->addToDB = true;
			$field->fireOrders[] = $fire;			
			$aoe = $field->getAoE($gamedata->turn);			
			$inAoE = $gamedata->getShipsInDistance($shooter, $aoe);
			foreach($inAoE as $targetID=>$target){		
				if ($shooter->id == $target->id) continue;//does not threaten self!
				if ($target->isDestroyed()) continue; //no point allocating				
				if (in_array($target->id,$alreadyTargeted,true)) continue;//each target only once 
				//add to target list
				$alreadyTargeted[] = $target->id; //add to list of already targeted units
				$field->addTarget($target);
			}
		} //endof foreach SparkField
	}//endof function createFiringOrders
	
}//endof class SparkFieldHandler



class SparkField extends Weapon{
    /*Spark Field - Ipsha weapon*/
        public $name = "SparkField";
        public $displayName = "Spark Field";
	public $iconPath = "SparkField.png";
	
	//let's make animation more or less invisible, and effect very large
	public $trailColor = array(141, 240, 255);
        public $animation = "ball";
        public $animationColor = array(1, 1, 255);
        public $animationExplosionScale = 2;
        public $animationExplosionType = "AoE";
        public $explosionColor = array(165, 165, 255);
        public $projectilespeed = 20;
        public $animationWidth = 1;
        public $trailLength = 1;
	
	public $boostable = true;
        public $boostEfficiency = 2;
        public $maxBoostLevel = 4;
	
      
        public $priority = 2; //should attack very early
	
        public $loadingtime = 1;
	public $autoFireOnly = true; //this weapon cannot be fired by player
	public $doNotIntercept = true; //this weapon is a field, "attacks" are just for technical reason
        
        public $rangePenalty = 0; //no range penalty, but range itself is limited
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals ; not relevant really!
	
	public $boostlevel = 0;
		
	public $damageType = "Standard"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	public $weaponClass = "Electromagnetic"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!
    	public $firingModes = array( 1 => "Field"); //just a convenient name for firing mode
	public $hextarget = true;
	
	protected $targetList = array(); //weapon will hit units on this list rather than target from firing order; filled by SparkFieldHandler!
	
	
 	public $possibleCriticals = array( //no point in range reduced crit; but reduced damage is really nasty for this weapon!
            14=>"ReducedDamage"
	);
	
	
	
	public function addTarget($newTarget){
		$this->targetList[] = $newTarget;
	}

	
	    public function setSystemDataWindow($turn){
		    $boostlevel = $this->getBoostLevel($turn);
		    $this->minDamage = 2-$boostlevel;
		    $this->maxDamage = 7-$boostlevel;
		    $this->minDamage = max(0,$this->minDamage);
		    $this->animationExplosionScale = $this->getAoE($turn);
		    $this->range = $this->getAoE($turn);
		      parent::setSystemDataWindow($turn);  
		      //$this->data["AoE"] = $this->getAoE($turn);
		      $this->data["Special"] = "This weapons automatically affects all units (friend or foe) in area of effect.";  
		      $this->data["Special"] .= "<br>It should not be fired manually."; 
		      $this->data["Special"] .= "<br>Ignores armor, but cannot damage ship structure.";  
		      $this->data["Special"] .= "<br>Base damage is 1d6+1, range 2 hexes.";  
		      $this->data["Special"] .= "<br>Can be boosted, for +2 AoE and -1 damage per level."; 
		      $this->data["Special"] .= "<br>Multiple overlapping Spark Fields will only cause 1 (strongest) attack on a particular target."; 
	    }	//endof function setSystemDataWindow
	
	
	
	public function getAoE($turn){
		$boostlevel = $this->getBoostLevel($turn);
		$aoe = 2+(2*$boostlevel);
		return $aoe;
	}
	
	
	public function calculateHitBase($gamedata, $fireOrder){
		//parent::calculateHitBase($gamedata, $fireOrder);
	        $fireOrder->updated = true;
		$fireOrder->chosenLocation = 0;//so it's recalculated later every time! - as location chosen here is completely incorrect for target 
		$fireOrder->needed = 100; //this weapon simply causes damage, hit is automatic
	}
	
	public function fire($gamedata, $fireOrder){
		//parent::fire($gamedata, $fireOrder);
		//actually fire at units from target list - and fill fire order data appropriately
		$shooter = $gamedata->getShipById($fireOrder->shooterid);
		$fireOrder->rolled = 1; //just to mark that there was a roll!
		$fireOrder->shotshit = 1; //always hit, technically
		
		//actual damage dealing...
		foreach($this->targetList as $target){
			$this->beforeDamage($target, $shooter, $fireOrder, null, $gamedata);			
		}
        	$notes = "this weapon simply causes damage, hit is automatic"; //replace usual note
		$fireOrder->notes = $notes;
	}
	
	public function calculateBoostLevel($turn){
		$this->boostlevel = $this->getBoostLevel($turn);
	}
	
        private function getBoostLevel($turn){
            $boostLevel = 0;
            foreach ($this->power as $i){
                    if ($i->turn != $turn)
                            continue;
                    if ($i->type == 2){
                            $boostLevel += $i->amount;
                    }
            }
            return $boostLevel;
        }	
	
	
	//find units in range (other than self), create attacks vs them
	public function beforeFiringOrderResolution($gamedata){
		SparkFieldHandler::createFiringOrders($gamedata);		
	}
	
	
	protected function beforeDamage($target, $shooter, $fireOrder, $pos, $gamedata){
		if (!($target instanceof FighterFlight)){ //ship - as usual
			$damage = $this->getFinalDamage($shooter, $target, $pos, $gamedata, $fireOrder);
			$this->damage($target, $shooter, $fireOrder,  $gamedata, $damage);
		}else{//fighter flight - separate hit on each fighter!
			foreach ($target->systems as $fighter){
				if ($fighter == null || $fighter->isDestroyed()){
				    continue;
				}
				$damage = $this->getFinalDamage($shooter, $target, $pos, $gamedata, $fireOrder);
				$this->doDamage($target, $shooter, $fighter, $damage, $fireOrder, null, $gamedata, false);
                    	}
		}
	}	

	
	protected function doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata, $damageWasDealt, $location = null){ 
		if ($system instanceof Structure) $damage = 0; //will not harm Structure!
		parent::doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata, $damageWasDealt, $location);
	}	

	
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 8;
            }
            if ( $powerReq == 0 ){
                $powerReq = 2;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
	    SparkFieldHandler::addSparkField($this);//so all Spark Fields are accessible together, and firing orders can be uniformly created
        }
	
        protected function getSystemArmourStandard($target, $system, $gamedata, $fireOrder, $pos=null){
            return 0; //ignores armor!
        }
	
        public function getDamage($fireOrder){        
		$damageRolled = Dice::d(6, 1)+1;
		$boostlevel = $this->getBoostLevel($fireOrder->turn);
		$damageRolled -= $boostlevel; //-1 per level of boost
		$damageRolled = max(0,$damageRolled); //cannot do less than 0	
		return $damageRolled;   
	}
        public function setMinDamage(){    
		$this->minDamage = 2 ;	      		
	}
        public function setMaxDamage(){   
		$this->maxDamage = 7 ;	    
	}
} //endof class SparkField 







class SurgeCannon extends Raking{
    /*Surge Cannon - Ipsha weapon*/
        public $name = "SurgeCannon";
        public $displayName = "Surge Cannon";
	public $iconPath = "SurgeCannon.png";
	
	public $animation = "laser";
	public $animationColor = array(165, 165, 255);
	public $animationWidth = 2;
	public $animationWidthArray = array(1=>2, 2=>3, 3=>4, 4=>5, 5=>6);
	public $animationWidth2 = 0.4;
	public $animationExplosionScaleArray = array(1=>0.1, 2=>0.2, 3=>0.3, 4=>0.5, 5=>0.6);

      
        public $loadingtime = 1;
	public $intercept = 2; //intercept rating -2
        
	
	
        public $priority = 8;
        public $priorityArray = array(1=>9, 2=>8, 3=>8, 4=>7, 5=>7); //weakest mode should go late, more powerful modes early (for Raking weapon)
	public $firingMode = 1;	
            public $firingModes = array(
                1 => "Single",
                2 => "2combined",
                3 => "3combined",
                4 => "4combined",
                5 => "5combined"
            );
        public $rangePenalty = 2; //-2 hex in single mode
            public $rangePenaltyArray = array( 1=>2, 2=>1, 3=>0.5, 4=>0.33, 5=>0.25 ); //Raking and Piercing mode
        public $fireControl = array(2, 2, 2); // fighters, <mediums, <capitals 
            public $fireControlArray = array( 1=>array(2, 2, 2), 2=>array(1,3,3), 3=>array(0,4,4), 4=>array(-2,4,4), 5=>array(-4,4,4) ); //Raking and Piercing mode
	
	
	
	    public $damageType = "Raking"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	    public $weaponClass = "Electromagnetic"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!

	
	public $isCombined = false; //is being combined with other weapon
	public $alreadyConsidered = false; //already considered - either being fired or combined
	
	
	    public function setSystemDataWindow($turn){
		      parent::setSystemDataWindow($turn);  
		      $this->data["Special"] = "Can combine multiple Surge Cannons into a single shot with increased range and damage (and cooldown):";  
		      $this->data["Special"] .= "<br> - 2 SC: 5-23 dmg, -5/hex"; 
		      $this->data["Special"] .= "<br> - 3 SC: 9-36 dmg, -2.5/hex"; 
		      $this->data["Special"] .= "<br> - 4 SC: 14-50 dmg, -1.65/hex"; 
		      $this->data["Special"] .= "<br> - 5 SC: 20-65 dmg, -1.25/hex"; 
		      $this->data["Special"] .= "<br>If You allocate multiple Surge Cannons in higher mode of fire at the same target, they will be combined."; 
		      $this->data["Special"] .= "<br>If not enough weapons are allocated to be combined, weapons will be fired in single mode instead.";  
		      $this->data["Special"] .= "<br>Cooldown period: 1 less than number of weapons combining.";  
		      $this->data["Special"] .= "<br>+2 per rake to critical/dropout rolls on system(s) hit this turn.";  //original rule is more fancy
	    }	
	
	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ 
		parent::onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder);
		//each rake causes +2 mod on critical roll for hit system! 
		if ($system->advancedArmor) return; //no effect on Advanced Armor
		$system->critRollMod+=2; 
	} //endof function onDamagedSystem
	
	
        public function fire($gamedata, $fireOrder){
            // If fired, this weapon needs 2 turns cooldown period (=forced shutdown)
	    if ($this->isCombined) $fireOrder->shots = 0; //no actual shots from weapon that's firing as part of combined shot!
            parent::fire($gamedata, $fireOrder);
	    for($i = 1; $i<$this->firingMode;$i++){
		$trgtTurn = $gamedata->turn+$i-1;
                $crit = new ForcedOfflineOneTurn(-1, $fireOrder->shooterid, $this->id, "ForcedOfflineOneTurn", $trgtTurn);
                $crit->updated = true;
		$crit->newCrit = true; //force save even if crit is not for current turn
                $this->criticals[] =  $crit;
	    }
        } //endof function fire
	
	
	
	//if fired in higher mode - combine with other weapons that are so fired!
	//if already combining - do not fire at all (eg. set hit chance at 0, make self completely uninterceptable and number of shots at 0)
	public function calculateHitBase($gamedata, $fireOrder){
		$this->alreadyConsidered = true;
		if ($this->isCombined){  //this weapon is being used as subordinate combination weapon! 
			$notes = "technical fire order - weapon combined into another shot";
			$fireOrder->chosenLocation = 0; //tylko techniczne i tak
			$fireOrder->needed = 0;
			$fireOrder->notes = $notes;
			$fireOrder->updated = true;
			$this->changeFiringMode($fireOrder->firingMode);
			return;
		}
		if ($fireOrder->firingMode > 1){ //for single fire there's nothing special
			$firingShip = $gamedata->getShipById($fireOrder->shooterid);
			$subordinateOrders = array();
			$subordinateOrdersNo = 0;
			//look for firing orders from same ship at same target (and same called id as well) in same mode - and make sure it's same type of weapon
			$allOrders = $firingShip->getAllFireOrders($gamedata->turn);
			foreach($allOrders as $subOrder) {
				if (($subOrder->type == 'normal') && ($subOrder->targetid == $fireOrder->targetid) && ($subOrder->calledid == $fireOrder->calledid) && ($subOrder->firingMode == $fireOrder->firingMode) ){ 
					//order data fits - is weapon another Surge Cannon?...
					$subWeapon = $firingShip->getSystemById($subOrder->weaponid);
					if ($subWeapon instanceof SurgeCannon){
						if (!$subWeapon->alreadyConsidered){ //ok, can be combined then!
							$subordinateOrdersNo++;
							$subordinateOrders[] = $subOrder;
						}
					}
				}
				if ($subordinateOrdersNo>=($fireOrder->firingMode-1)) break;//enough subordinate weapons found! - exit loop
			}						
			if ($subordinateOrdersNo == ($fireOrder->firingMode-1)){ //combining - set other combining weapons/fire orders to technical status!
				foreach($subordinateOrders as $subOrder){
					$subWeapon = $firingShip->getSystemById($subOrder->weaponid);
					$subWeapon->isCombined = true;
					$subWeapon->alreadyConsidered = true;
					$subWeapon->doNotIntercept = true;
				}
			}else{//not enough weapons to combine in this mode - set self to single fire
				$fireOrder->firingMode = 1;
			}
		}
		parent::calculateHitBase($gamedata, $fireOrder);
	}//endof function calculateHitBase
	
	
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 6;
            }
            if ( $powerReq == 0 ){
                $powerReq = 3;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
	
	
        public function getDamage($fireOrder){
		switch($this->firingMode){
			case 1:
				return Dice::d(10, 1)+1; //1 SC
				break;
			case 2:
				return Dice::d(10, 2)+3; //2 SC
				break;
			case 3:
				return Dice::d(10, 3)+6; //3 SC
				break;
			case 4:
				return Dice::d(10, 4)+10; //4 SC
				break;
			case 5:
				return Dice::d(10, 5)+15; //5 SC
				break;
		}
	}
        public function setMinDamage(){    
		switch($this->firingMode){
			case 1:
				$this->minDamage = 2;
				break;
			case 2:
				$this->minDamage = 5;
				break;	
			case 3:
				$this->minDamage = 9;
				break;	
			case 4:
				$this->minDamage = 14;
				break;	
			case 5:
				$this->minDamage = 20;
				break;	
		}
		$this->minDamageArray[$this->firingMode] = $this->minDamage;
	}
        public function setMaxDamage(){
		switch($this->firingMode){
			case 1:
				$this->maxDamage = 11;
				break;
			case 2:
				$this->maxDamage = 23;
				break;	
			case 3:
				$this->maxDamage = 36;
				break;	
			case 4:
				$this->maxDamage = 50;
				break;	
			case 5:
				$this->maxDamage = 65;
				break;	
		}
		$this->maxDamageArray[$this->firingMode] = $this->maxDamage;  
	}
} //endof class SurgeCannon


   class LtSurgeBlaster extends LinkedWeapon{
	   /*Ipsha fighter weapon*/
        public $trailColor = array(50, 50, 200);
        public $name = "LtSurgeBlaster";
        public $displayName = "Light Surge Blaster";
	   public  $iconPath = "lightParticleBeam.png";
        public $animation = "trail";
        public $animationColor =  array(145, 145, 245);
        public $animationExplosionScale = 0.10;
        public $projectilespeed = 10;
        public $animationWidth = 2;
        public $trailLength = 10;
        public $intercept = 2;
        public $loadingtime = 1;
        public $shots = 2;
        public $defaultShots = 2;
        public $rangePenalty = 2;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals
	    public $priority = 4; 
        
        public $damageType = "Standard"; 
        public $weaponClass = "Electromagnetic"; 
	    
        
     function __construct($startArc, $endArc, $nrOfShots = 2){
            $this->defaultShots = $nrOfShots;
            $this->shots = $nrOfShots;
            $this->intercept = $nrOfShots;
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }
	
	
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            $this->data["Special"] = "+1 to Crit/Dropout rolls per hit.";
        }
	   
	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ 
		parent::onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder);
		//each hit causes +1 mod on critical roll for hit system! 
		if ($system->advancedArmor) return; //no effect on Advanced Armor
		$system->critRollMod+=1; 
	} //endof function onDamagedSystem
	    
        public function getDamage($fireOrder){        return Dice::d(6,2)-1;   }
        public function setMinDamage(){     $this->minDamage = 1 ;      }
        public function setMaxDamage(){     $this->maxDamage = 11 ;      }
    } //endof class LtSurgeBlaster



 class EmPulsar extends Pulse{
	 /*Ipsha weapon*/
        public $name = "EmPulsar";
        public $displayName = "EM Pulsar";
	public $iconPath = "EmPulsar.png";
        public $animationColor = array(100, 100, 255);
        public $animation = "trail";
        public $animationWidth = 3;
        public $projectilespeed = 10;
        public $animationExplosionScale = 0.15;
        public $trailLength = 10;
        
        public $loadingtime = 1;
        public $priority = 3;
        
	public $intercept = 2;
        public $rangePenalty = 1; //-1/hex
        public $grouping = 25; //+1 pulse hit per 1 below target number on d20
        public $fireControl = array(1, 2, 3); // fighters, <mediums, <capitals 
	 
	 public $weaponClass = "Electromagnetic"; 
        
	 
	 
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 6;
            }
            if ( $powerReq == 0 ){
                $powerReq = 3;
            }		
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
	 
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
	    $this->data["Special"] .= "<br>-1 per hit to crit rolls, -2 on dropout rolls.";
            $this->data["Special"] .= "<br>Cooldown period: 1 turn.";  
        }
	 
	 
	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ //really no matter what exactly was hit!
		parent::onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder);		
		if ($system->advancedArmor) return; //no effect on Advanced Armor
		
		//+1 to crit roll, +2 to dropout roll
		$mod = 1;
		if ($ship instanceof FighterFlight) $mod++;		
		$system->critRollMod += $mod; 
	} //endof function onDamagedSystem
	
	
        public function fire($gamedata, $fireOrder){
            // If fired, this weapon needs 1 turn cooldown period (=forced shutdown)
            parent::fire($gamedata, $fireOrder);		
		$trgtTurn = $gamedata->turn;
                $crit = new ForcedOfflineOneTurn(-1, $fireOrder->shooterid, $this->id, "ForcedOfflineOneTurn", $trgtTurn);
                $crit->updated = true;
		$crit->newCrit = true; //force save even if crit is not for current turn
                $this->criticals[] =  $crit;	
        } //endof function fire
	 
        
        public function getDamage($fireOrder){        return 9;   }
    }//endof class EmPulsar



class ResonanceGenerator extends Weapon{
    /*Resonance Generator - Ipsha weapon*/
        public $name = "ResonanceGenerator";
        public $displayName = "Resonance Generator";
	public $iconPath = "ResonanceGenerator.png";
	
	public $animation = "laser"; //described as beam in nature, standard damage is resonance effect and not direct
	public $animationColor = array(125, 125, 230);
	public $animationWidth = 10;
	public $animationWidth2 = 0.4;
	public $animationExplosionScaleArray = array(1=>0.25);
      
        public $loadingtime = 1;
        
        public $rangePenalty = 1; //-1/hex
        public $fireControl = array(null, 2, 2); // fighters, <mediums, <capitals 
	
	public $intercept = 0;
	public $priority = 1;// as it attacks every section, should go first!
	
	public $noPrimaryHits = true; //outer section hit will NOT be able to roll PRIMARY result!
	
	private $cooldown = 2;
	
	    public $damageType = "Standard"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	    public $weaponClass = "Electromagnetic"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!

	
	
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
		      $this->data["Special"] = "Cooldown period: 2 turns.";  
		      $this->data["Special"] .= "<br>Attacks all sections (so a capital ship will sufer 5 attacks, while MCV only 1).";  //MCV should suffer 2, but for technical reasons I opted for going for Section = Structure block		    
		      $this->data["Special"] .= "<br>Ignores all standard and half advanced armor."; //should take full Adaptive armor, but it's easier to treat Adaptive as Advanced here
	    }	
	
	
	
        public function fire($gamedata, $fireOrder){
            // If fired, this weapon needs 2 turns cooldown period (=forced shutdown)
            parent::fire($gamedata, $fireOrder);
		for($i = 1; $i<=$this->cooldown;$i++){		
			$trgtTurn = $gamedata->turn+$i-1;//start on current turn rather than next!
			$crit = new ForcedOfflineOneTurn(-1, $fireOrder->shooterid, $this->id, "ForcedOfflineOneTurn", $trgtTurn);
			$crit->updated = true;
			$crit->newCrit = true; //force save even if crit is not for current turn
			$this->criticals[] =  $crit;
		}
        } //endof function fire
	
	
	protected function getSystemArmourStandard($target, $system, $gamedata, $fireOrder, $pos=null){
		return 0; //standard armor is ignored
        }
	
	protected function getSystemArmourInvulnerable($target, $system, $gamedata, $fireOrder, $pos=null){
		//half of advanced armor is ignored
		$armour = parent::getSystemArmourInvulnerable($target, $system, $gamedata, $fireOrder, $pos);
		    if (is_numeric($armour)){
			$toIgnore = ceil($armour /2);
			$new = $armour - $toIgnore;
			return $new;
		    }
		    else {
			return 0;
		    }
        }//endof function getSystemArmourInvulnerable
	
	
	public function isTargetAmbiguous($gamedata, $fireOrder){//targat always ambiguous - just so enveloping weapon is not used to decide target section!
		return true;
	}
	
	
	/*attacks every not destroyed (as of NOW!) ship section*/
	protected function beforeDamage($target, $shooter, $fireOrder, $pos, $gamedata){
		//fighters are untargetable, so we know it's a ship
		if ($target->isDestroyed()) return; //no point allocating
		$activeStructures = $target->getSystemsByName("Structure",false);//list of non-destroyed Structure blocks
		foreach($activeStructures as $struct){
			$fireOrder->chosenLocation = $struct->location;			
			$damage = $this->getFinalDamage($shooter, $target, $pos, $gamedata, $fireOrder);
			$this->damage($target, $shooter, $fireOrder,  $gamedata, $damage, true);//force PRIMARY location!
		}
	}//endof function beforeDamage
	
		
        public function getDamage($fireOrder){       return Dice::d(10,1);   }
        public function setMinDamage(){     $this->minDamage = 1 ;      }
        public function setMaxDamage(){     $this->maxDamage = 10 ;      }
} //endof class ResonanceGenerator





class SurgeBlaster extends Weapon{
    /*Surge Blaster - Ipsha weapon*/
        public $name = "SurgeBlaster";
        public $displayName = "Surge Blaster";
	public $iconPath = "SurgeBlaster.png";
	
        public $animation = "trail";
        public $animationColor =  array(165, 165, 255);
        public $projectilespeed = 14;
        public $animationWidth = 4;
        public $animationExplosionScale = 0.4;
        public $priority = 6;
      
        public $loadingtime = 1;
	public $intercept = 1;
        
        public $rangePenalty = 0.5; //-1/2 hexes
        public $fireControl = array(1, 2, 3); // fighters, <mediums, <capitals 
	
	
	    public $damageType = "Standard"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	    public $weaponClass = "Electromagnetic"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!

	
	private $cooldown = 1;
	
	
	
	    public function setSystemDataWindow($turn){
		      parent::setSystemDataWindow($turn);  
		      $this->data["Special"] = "Cooldown period: " . $this->cooldown . " turns.";  
		      $this->data["Special"] .= "<br>+4 to all critical/dropout rolls made by system hit this turn.";  
	    }	
	
	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ //really no matter what exactly was hit!
		parent::onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder);
		if ($system->advancedArmor) return; //no effect on Advanced Armor

		$system->critRollMod+=4; //+4 to all critical/dropout rolls on system hit this turn

	} //endof function onDamagedSystem
	
	
        public function fire($gamedata, $fireOrder){
            // If fired, this weapon needs 2 turns cooldown period (=forced shutdown)
            parent::fire($gamedata, $fireOrder);
		for($i = 1; $i<=$this->cooldown;$i++){		
			$trgtTurn = $gamedata->turn+$i-1;//start on current turn rather than next!
			$crit = new ForcedOfflineOneTurn(-1, $fireOrder->shooterid, $this->id, "ForcedOfflineOneTurn", $trgtTurn);
			$crit->updated = true;
			$crit->newCrit = true; //force save even if crit is not for current turn
			$this->criticals[] =  $crit;
		}
        } //endof function fire
	
	
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 6;
            }
            if ( $powerReq == 0 ){
                $powerReq = 6;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
	
        public function getDamage($fireOrder){        return Dice::d(10,4);   }
        public function setMinDamage(){     $this->minDamage = 4 ;      }
        public function setMaxDamage(){     $this->maxDamage = 40 ;      }
} //endof class SurgeBlaster


class RammingAttack extends Weapon{
    /*option to ram target will be implemented as an actual weapon*/
        public $name = "RammingAttack";
        public $displayName = "Ramming Attack";
	public $iconPath = "RammingAttack.png";
	
	//animation irrelevant really (range 0), but needs to be fast!
        public $animation = "trail";
        public $animationColor =  array(1, 1, 1);
        public $projectilespeed = 24;
        public $animationWidth = 1;
        public $animationExplosionScale = 0.4;
        public $priority = 1;
	
	public $doNotIntercept = true; //unit hurls itself at the enemy - this cannot be intercepted!
      
        public $loadingtime = 1;
	public $intercept = 0;
        
        public $rangePenalty = 0; //no range penalty
	public $range = 0; //attacks units on same hex only
	
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals 
	public $raking = 10; //size of rake
	
	
            public $firingModes = array(
                1 => "Ramming"
            );	
	    public $damageType = "Raking"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	    public $weaponClass = "Ramming"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!


	public $isRammingAttack = true;	
	private $designedToRam = false;
	private $selfDestroy = 0; //will successful attack destroy the ramming ship? Additional damage modifier
	private $designDamage = 0;
	private $damageModRolled = 0;
	
	
	private $gamedata = null; //gamedata is needed in places normally unavailable - this variable will be filled before any calculations happen!
	
	 public $possibleCriticals = array(); //shouldn't be hit ever, but if it is, should not suffer any criticals
	
	
	//fill gamedata variable, which might otherwise be left out!
	public function beforeFiringOrderResolution($gamedata){
		$this->gamedata = $gamedata;
	}
	
	    public function setSystemDataWindow($turn){
			$this->setMinDamage(); //just in case it's not set correctly in the beginning!
        		$this->setMaxDamage();
		      parent::setSystemDataWindow($turn);  
		      $this->data["Special"] = "Ramming attack - if cucccessful, ramming unit itself will take damage too (determined by targets' ramming factor).";  
		      if($this->designedToRam) {
			      $this->data["Special"] .= "<br>This unit is specifically designed for ramming and may do so in any scenario.";
		      }else{
			      $this->data["Special"] .= "<br>ALLOWED ONLY IN SPECIAL CIRCUMSTANCES, LIKE HOMEWORLD DEFENSE!";
		      }
		      $this->data["Special"] .= "<br>Profiles and EW do not matter for hit chance - but unit size and target speed does.";  
		      $this->data["Special"] .= "<br>	(it's generally easier to ram slow targets and targets larger than ramming units itself)";  
		      $this->data["Special"] .= "<br>Ramming damage is also influenced by conditions - moving head on with initiative slightly increases chance of high damage.";
		      $this->data["Special"] .= "<br>Ramming attacks will be done in ship firing phase (even attacks by fighters) and cannot be intercepted.";
	    }	
	
	
	public function fire($gamedata, $fireOrder){
		// If hit, firing unit itself suffers damage, too (based on raming factor of target)!
		$this->gamedata = $gamedata;
		parent::fire($gamedata, $fireOrder);
		if($fireOrder->shotshit > 0){
			$pos = null;
			$shooter = $gamedata->getShipById($fireOrder->targetid);
			$target = $this->unit;
			$fireOrder->chosenLocation = 0;//to be redetermined!
			$damage = $this->getReturnDamage($fireOrder);
        		$damage = $this->getDamageMod($damage, $shooter, $target, $pos, $gamedata);
        		$damage -= $target->getDamageMod($shooter, $pos, $gamedata->turn, $this);
			if($target instanceof FighterFlight){ //allocate exactly to firing fighter!
				$ftr = $target->getFighterBySystem($this->id);
				if ($ftr->isDestroyed()) return; //do not allocate to already destroyed fighter!!! it would cause the game to randomly choose another one, which would be incorrect
				$fireOrder->calledid = $ftr->id;
			}
			$this->damage($target, $shooter, $fireOrder,  $gamedata, $damage);
			$fireOrder->calledid = -1; //just in case!
		}
        } //endof function fire

	
        function __construct($armour, $startArc, $endArc, $designDamage = 0, $fcbonus = 0, $designedToRam = false, $selfDestroy = 0){
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            $maxhealth = 1;
            $powerReq = 0;
		if ($fcbonus != 0){
			$this->fireControl = array($fcbonus, $fcbonus, $fcbonus);
		}
		if ($designDamage > 0){ //most units calculate ramming factor on the fly, but some are specifically designed to ram and carry explosives to do so effectively - they have fixed ramming factor
			$this->designDamage = 	$designDamage;
		}
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
		$this->designedToRam = $designedToRam;
		$this->selfDestroy = $selfDestroy;
        }
	

	
	private function getRammingFactor(){
		$dmg = 0;
		if ($this->designDamage > 0){
			$dmg = 	$this->designDamage;
		}else{
			$rammingShip = $this->unit;
			if (isset($rammingShip)) {
				$dmg = $rammingShip->getRammingFactor();
			}
		}
		return $dmg;
	}
        public function getDamage($fireOrder){  	
		//modifier: +1 if greater Ini than target, +1 if head on, +1 if target is head on also
		$modifier = 0;
		$shooter = $this->unit;
		$gd = $this->gamedata;
		$target = $gd->getShipById($fireOrder->targetid);
		if ($shooter->iniative > $target->iniative) $modifier++;
		$bearing = abs($shooter->getBearingOnUnit($target));
		if ($bearing < 10) $modifier++;//should be 0, but at rage 0 there may be a few degrees off...
		$bearing = abs($target->getBearingOnUnit($shooter));
		if ($bearing < 10) $modifier++;//should be 0, but at rage 0 there may be a few degrees off...
		
		//roll and consult table
		$rfactor = $this->getRammingFactor();
		$roll = Dice::d(20,1)+$modifier;
		$this->damageModRolled = 0.25; //baseline: 25% damage
		if ($roll >= 17){
			$this->damageModRolled = 1; //100%, perfect hit!
		}else if ($roll >= 13){
			$this->damageModRolled = 0.75;
		}else if ($roll >= 7){
			$this->damageModRolled = 0.5;
		}//if lower, stays 0.25
		$damage = ceil($this->damageModRolled * $rfactor);
		if ($fireOrder->notes != '') $fireOrder->notes .= "; ";
		$fireOrder->notes .= "; mod = " . $this->damageModRolled . " rammingfactor: $rfactor" ;
		if ((!($shooter instanceof FighterFlight)) && ($target instanceof FighterFlight)) $damage += 1000;  //fighter colliding with ship will always be destroyed
		$fireOrder->notes .= "mod = " . $this->damageModRolled . " rammingfactor: $rfactor damage: $damage" ;		
		return $damage;			     
	}//endof function getDamage
        public function getReturnDamage($fireOrder){    //damage that ramming unit suffers itself - using same modifier as actual attack! (already set)   
		$gd = $this->gamedata;
		$target = $gd->getShipById($fireOrder->targetid);
		$shooter = $this->unit;
		$rfactor =  $target->getRammingFactor();
		$damage = ceil($this->damageModRolled * $rfactor);			
		if ((!($target instanceof FighterFlight)) && ($shooter instanceof FighterFlight)) $damage = 1000;  //fighter colliding with ship will always be destroyed
		$damage += $this->selfDestroy;//unit will suffer additional damage on a successful attack
		$fireOrder->notes .= "; return rammingfactor: $rfactor damage: $damage" ;
		return $damage;					     
	}
	
        public function setMinDamage(){     
		$this->minDamage = ceil($this->getRammingFactor()/4);				      
	}
        public function setMaxDamage(){     
		$this->maxDamage = $this->getRammingFactor();				      
	}
} //endof class RammingAttack

	

?>
