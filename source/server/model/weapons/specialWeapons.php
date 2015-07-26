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


		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        protected function getSystemArmour($system, $gamedata, $fireOrder){
            $armor = $system->armour;
            if (is_numeric($armor))
                return round($armor / 2);
            
            return 0;
		}
	
		public function setSystemDataWindow($turn){

            $this->data["Damage type"] = "Raking (5)";
            $this->data["Weapon type"] = "Plasma";
            $this->data["Special"] = "Reduces armor of hit systems.";
						
			parent::setSystemDataWindow($turn);
		}
		
		protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){
				
			$crit = new ArmorReduced(-1, $ship->id, $system->id, "ArmorReduced", $gamedata->turn);
			$crit->updated = true;
            $crit->inEffect = false;
            $system->criticals[] =  $crit;
			parent::onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder);
		}
		
		
		public function getDamage($fireOrder){        return Dice::d(10,3)+4;   }
        public function setMinDamage(){     $this->minDamage = 7 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 34 - $this->dp;      }

	}


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
        public $priority = 5;

        public $loadingtime = 2;

        public $rangePenalty = 1;
        public $fireControl = array(3, 3, 3); // fighters, <=mediums, <=capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
            $this->data["Weapon type"] = "Electromagnetic";
            parent::setSystemDataWindow($turn);
        }

        // Shock Cannons ignore armor.
        protected function getSystemArmour($system, $gamedata, $fireOrder){
            return 0;
	}

        protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){
            $crit = null;

            if ($system instanceof Fighter && !($system instanceof SuperHeavyFighter)){
                $crit = new DisengagedFighter(-1, $ship->id, $system->id, "DisengagedFighter", $gamedata->turn);
                $crit->updated = true;
                $crit->inEffect = true;
                $system->criticals[] =  $crit;
            }else if ($system instanceof Structure){
                $reactor = $ship->getSystemByName("Reactor");
                $outputMod = -round($damage/4);
                $crit = new OutputReduced(-1, $ship->id, $reactor->id, "OutputReduced", $gamedata->turn, $outputMod);
                $crit->updated = true;
                $reactor->criticals[] =  $crit;
            }

            parent::onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder);
	}

        public function getDamage($fireOrder){        return Dice::d(10)+4;   }
        public function setMinDamage(){     $this->minDamage = 5 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 14 - $this->dp;      }
    }

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
        public $priority = 10;
        
			
        public $rangePenalty = 2;
        public $fireControl = array(4, 2, 2); // fighters, <=mediums, <=capitals 


		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
       
		public function setSystemDataWindow($turn){

			$this->data["Weapon type"] = "Electromagnetic";
						
			parent::setSystemDataWindow($turn);
		}
		
		protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){
			$crit = null;
			
            debug::log($system->displayName);
			if ($system instanceof Fighter && !($system instanceof SuperHeavyFighter)){
				$crit = new DisengagedFighter(-1, $ship->id, $system->id, "DisengagedFighter", $gamedata->turn);
				$crit->updated = true;
                $crit->inEffect = true;
				$system->criticals[] =  $crit;
            }else if ($system instanceof Structure){
				$reactor = $ship->getSystemByName("Reactor");
				$crit = new OutputReduced1(-1, $ship->id, $reactor->id, "OutputReduced1", $gamedata->turn);
				$crit->updated = true;
				$reactor->criticals[] =  $crit;
			}else if ($system->powerReq > 0 || $system->canOffLine ){
				$system->addCritical($ship->id, "ForcedOfflineOneTurn", $gamedata);
			}
            else {
                $crits = array();
                $crits = $system->testCritical($ship, $gamedata, $crits, $add = 4);
            }
		}
		
		
		public function getDamage($fireOrder){        return 0;   }
        public function setMinDamage(){     $this->minDamage = 0;      }
        public function setMaxDamage(){     $this->maxDamage = 0;      }

	}

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
        public $projectilespeed = 12;
        public $animationExplosionScale = 0.05;
        public $rof = 5;
        public $grouping = 25;
        public $maxpulses = 6;
		        
	    public $loadingtime = 1;
        public $priority = 10;
        
			
        public $rangePenalty = 0.5;
        public $fireControl = array(2, 3, 4); // fighters, <=mediums, <=capitals 


		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
       
		public function setSystemDataWindow($turn){

			$this->data["Weapon type"] = "Electromagnetic";
						
			parent::setSystemDataWindow($turn);
		}

		
		protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){
			$crit = null;
			
            if ($system instanceof Fighter && !($system instanceof SuperHeavyFighter)){
				$crit = new DisengagedFighter(-1, $ship->id, $system->id, "DisengagedFighter", $gamedata->turn);
				$crit->updated = true;
                $crit->inEffect = true;
				$system->criticals[] =  $crit;
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
            else {
                $crits = array();
                $crits = $system->testCritical($ship, $gamedata, $crits, $add = 4);
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
        public $priority = 10;

        public $rangePenalty = 0.5;
        public $fireControl = array(0, 3, 40); // fighters, <=mediums, <=capitals 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){
            $crit = null;

            if ($system instanceof Fighter){
                if (!$system instanceof SuperHeavyFighter){
                    $crit = new DisengagedFighter(-1, $ship->id, $system->id, "DisengagedFighter", $gamedata->turn);
                    $crit->updated = true;
                    $crit->inEffect = true;
                    $system->criticals[] =  $crit;
                }
                else {
                    $roll = Dice::d(6);
                    if ($roll < 2){
                        $crit = new DisengagedFighter(-1, $ship->id, $system->id, "DisengagedFighter", $gamedata->turn);
                        $crit->updated = true;
                        $crit->inEffect = true;
                        $system->criticals[] =  $crit;
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
            else {
                $crits = array();
                $crits = $system->testCritical($ship, $gamedata, $crits, $add = 6);
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
        public $priority = 10;

        public $rangePenalty = 0.33;
        public $fireControl = array(2, 4, 5); // fighters, <=mediums, <=capitals 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){
            $crit = null;
            
            debug::log($system->displayName);
            if ($system instanceof Fighter){
                if (!$system instanceof SuperHeavyFighter){
                    $crit = new DisengagedFighter(-1, $ship->id, $system->id, "DisengagedFighter", $gamedata->turn);
                    $crit->updated = true;
                    $crit->inEffect = true;
                    $system->criticals[] =  $crit;
                }
                else {
                    $roll = Dice::d(6);
                    if ($roll < 3){
                        $crit = new DisengagedFighter(-1, $ship->id, $system->id, "DisengagedFighter", $gamedata->turn);
                        $crit->updated = true;
                        $crit->inEffect = true;
                        $system->criticals[] =  $crit;
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
            else {
                $crits = array();
                $crits = $system->testCritical($ship, $gamedata, $crits, $add = 6);
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
        // You have to take a look at this.
        public $animation = "laser";
        // You have to take a look at this.
        public $animationColor = array(158, 240, 255);
        // You have to take a look at this.
        public $trailColor = array(158, 240, 255);
        // You have to take a look at this.
        public $projectilespeed = 15;
        // You have to take a look at this.
        public $animationWidth = 2;
        public $animationWidth2 = 0.2;
        // You have to take a look at this.
        public $animationExplosionScale = 0.10;
        // You have to take a look at this.
        public $trailLength = 30;
        public $priority = 1;

        public $loadingtime = 2;
        public $rangePenalty = 3;
        public $fireControl = array(3, null, null); // fighters, <=mediums, <=capitals


        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
            $this->data["Weapon type"] = "Electromagnetic";

            parent::setSystemDataWindow($turn);
        }

        protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){

            // On a hit, make fighters drop out, but if this weapon had
            // a ReducedDamage crit, roll a d6 and substract 2 for each
            // ReducedDamage crit. If the result is less than 1, the hit
            // has no effect on the fighter.
            $crit = null;
            $affect = Dice::d(6);

            foreach ($this->criticals as $crit){
                if ($crit instanceof ReducedDamage){
                    $affect = $affect - 2;
                }
            }

            if ($system instanceof Fighter && !($system instanceof SuperHeavyFighter) && $affect > 0){
                $crit = new DisengagedFighter(-1, $ship->id, $system->id, "DisengagedFighter", $gamedata->turn);
				$crit->updated = true;
                $crit->inEffect = true;
				$system->criticals[] =  $crit;
            }
            
            parent::onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder);
        }


        public function getDamage($fireOrder){        return 0;   }
        public function setMinDamage(){     $this->minDamage = 0;      }
        public function setMaxDamage(){     $this->maxDamage = 0;      }
    }
?>
