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
	}

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
        public $fireControl = array(0, 3, 40); // fighters, <=mediums, <=capitals 

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
        public $animationColor = array(120, 50, 250);
        public $projectilespeed = 18;
        public $animationWidth = 4;
        public $animationExplosionScale = 0.35;
        public $priority = 6;
      
        public $loadingtime = 1;
        
        public $rangePenalty = 0.33; //-1/3 hexes
        public $fireControl = array(0, 3, 3); // fighters, <mediums, <capitals 
	
	
	    public $damageType = "Standard"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	    public $weaponClass = "Electromagnetic"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!

	
	
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
			$this->critRollMod++; //+1 to all critical rolls made by target this turn 
		}

	} //endof function onDamagedSystem
	
	
        public function fire($gamedata, $fireOrder){
            // If fired, this weapon needs 2 turns cooldown period (=forced shutdown)
            parent::fire($gamedata, $fireOrder);
		
		$trgtTurn = $gamedata->turn;
                $crit = new ForcedOfflineOneTurn(-1, $fireOrder->shooterid, $this->id, "ForcedOfflineOneTurn", $trgtTurn);
                $crit->updated = true;
                $this->criticals[] =  $crit;
		$trgtTurn = $gamedata->turn +1;
                $crit = new ForcedOfflineOneTurn(-1, $fireOrder->shooterid, $this->id, "ForcedOfflineOneTurn", $trgtTurn);
                $crit->updated = true;
                $this->criticals[] =  $crit;		
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
		
		//make sure boost level for all weapons is calculated
		foreach(SparkFieldHandler::$sparkFields as $field){
			$field->calculateBoostLevel($gamedata->turn);
		}
		
		//sort all fields by boost
		usort(SparkFieldHandler::$sparkFields, "self::sortByBoost");	
	
		//table of units that are already targeted
		$alreadyTargeted = array();
throw new Exception("DEBUG: specialWeapons line 977: before foreach Y");	
		//now for each weapon find possible targets and create firing orders (unless they are already fired at)
		//strongest weapons fire first, and only 1 field affects particular ship		
		foreach(SparkFieldHandler::$sparkFields as $field){
			$fieldActive = true;
			if ($this->isDestroyed($gamedata->turn-1)) $fieldActive = false; //destroyed field does not attack
			if ($this->isOfflineOnTurn($gamedata->turn)) $fieldActive = false; //disabled field does not attack
			if ($fieldActive){
				$shooter = $field->getUnit();
				$aoe = $field->getAoE($gamedata->turn);
				$inAoE = $gamedata->getShipsInDistanceHex($shooter, $aoe);
				foreach($inAoE as $targetID=>$target){
/*
					$validTarget = true;
					if ($shooter->id == $target->id) $validTarget = false;//does not threaten self!
					if ($target->isDestroyed()) $validTarget = false; //no point allocating
					if (in_array($target->id,$alreadyTargeted)) $validTarget = false; //each target only once

					if ($validTarget) {
						$alreadyTargeted[] = $target->id; //add to list of already targeted units
						//create appropriate firing order
						$fire = new FireOrder(-1, 'normal', $shooter->id, $target->id, $field->id, -1, $gamedata->turn, 1, 0, 0, 1, 0, 0, $aoe, null);
						$fire->addToDB = true;
						$field->fireOrders[] = $fire;
					}
*/
				}
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
        public $animationColor = array(1, 1, 1);
        public $animationExplosionScale = 1;
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
	
	
	
 	public $possibleCriticals = array( //no point in range reduced crit; but reduced damage is really nasty for this weapon!
            14=>"ReducedDamage"
	);

	
	    public function setSystemDataWindow($turn){
		    $boostlevel = $this->getBoostLevel($turn);
		    $this->minDamage = 2-$boostlevel;
		    $this->maxDamage = 7-$boostlevel;
		    $this->minDamage = max(0,$this->minDamage);
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
		$fireOrder->needed = 100; //this weapon simply causes damage, hit is automatic
		$fireOrder->updated = true;
	}
	
	public function calculateBoostLevel($turn){
		$this->boostlevel = 	$this->getBoostLevel($turn);
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

	public function damage($target, $shooter, $fireOrder, $gamedata, $damage){
		if (!($target instanceof FighterFlight)){ //ship - as usual
			parent::damage($target, $shooter, $fireOrder, $gamedata, $damage);
		}else{ //fighter - damage every fighter in flight! (separate damage roll for each)
			foreach ($target->systems as $fighter){
				if ($fighter == null || $fighter->isDestroyed()){
				    continue;
				}
				$damageAmount = $this->getDamage($fireOrder);
				$this->doDamage($target, $shooter, $fighter, $damageAmount, $fireOrder, null, $gamedata, false);
                    	}
		}
	}//endof function damage
	
	
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




?>
