<?php
	class Fighter extends ShipSystem{
		
		
		public $flightid;
		public $location = 0;
		public $id, $armour, $maxhealth, $powerReq, $output, $name, $displayName;
		public $damage = array();
		public $outputMod = 0;
		public $boostable = false;
		public $power = array();
		public $data = array();
		public $critData = array();
		public $fighter = true;
		public $systems = array();
		
		public $possibleCriticals = array();
		
			
		public $criticals = array();
		
		
		function __construct($name, $armour, $maxhealth, $flight){
			parent::__construct($armour, $maxhealth, 0, 0 );
			
                    $this->name = $name;
                    $this->flightid = $flight;
			
			
		}
        
        public function getSpecialAbilityList($list)
        {
            if ($this->isDestroyed())
                return $list;
            
            foreach ($this->systems as $system)
            {
                if ($system instanceof SpecialAbility)
                {
                    foreach ($system->specialAbilities as $effect)
                    {
                        if (!isset($list[$effect]))
                        {
                            $list[$effect] = $system->id;
                        }
                    }
                }
            }
            return $list;
        }
        
        public function getSystemById($id){
            foreach ($this->systems as $system){
                if ($system->id == $id){
                    return $system;
                }
            }
            return null;
        }
        
        public function isDisengaged($turn){
            if ($this->hasCritical("DisengagedFighter", $turn))
				return true;
        }
			
        public function isDestroyed($turn = false){
            if ($this->isDisengaged($turn))
                return true;
            
            return parent::isDestroyed();
        }
		
		public function addFrontSystem($system){
            
			$this->addSystem($system, 1);
            
        
        }
        
        public function addAftSystem($system){
            
			$this->addSystem($system, 2);
            
        
        }
		
		
		protected function addSystem($system, $loc){
            $system->location = $loc;
            $this->systems[] = $system;
        }
			
		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);			
			foreach ($this->systems as $system){
				$system->setSystemDataWindow($turn);	
			}
		}
		
		public function onConstructed($ship, $turn, $phase){
			parent::onConstructed($ship, $turn, $phase);	
			foreach ($this->systems as $system){
				$system->onConstructed($ship, $turn, $phase);
			}
     
		}
		
		public function testCritical($ship, $gamedata, $crits, $add = 0){
			$d = Dice::d(10);
			
                        $dropOutBonus = $gamedata->getShipById($this->flightid)->getDropOutBonus();
                        
			if (($d + $dropOutBonus) > $this->getRemainingHealth()){
				$crit = new DisengagedFighter(-1, $ship->id, $this->id, "DisengagedFighter", $gamedata->turn);
				$crit->updated = true;
                $this->criticals[] =  $crit;
                $crits[] = $crit;
			}
						
			return $crits;
			 
		}
		
		
		public function isOfflineOnTurn($turn = null){
		
			return false;
		
		}
		
		public function isOverloadingOnTurn($turn = null){
			
			return false;
		
		}
		
		 public function getArmourPos($gamedata, $pos){
			$target = $gamedata->getShipById($this->flightid); 
			$tf = $target->getFacingAngle();
			$shooterCompassHeading = mathlib::getCompassHeadingOfPos($target, $pos);
			
			return $this->doGetArmour($tf,  $shooterCompassHeading);
        }
        
        public function getArmour($target, $shooter){
            $tf = $target->getFacingAngle();
            $shooterCompassHeading = mathlib::getCompassHeadingOfShip($target, $shooter);
          
            return $this->doGetArmour($tf,  $shooterCompassHeading);
            
        }
        
        
        public function doGetArmour($tf, $shooterCompassHeading){
            if (mathlib::isInArc($shooterCompassHeading, Mathlib::addToDirection(330,$tf), Mathlib::addToDirection(30,$tf) )){
               return $this->armour[0];
            }else if (mathlib::isInArc($shooterCompassHeading, Mathlib::addToDirection(150,$tf), Mathlib::addToDirection(210,$tf) )){
                return $this->armour[1];
            }else if (mathlib::isInArc($shooterCompassHeading, Mathlib::addToDirection(210,$tf), Mathlib::addToDirection(330,$tf) )){
                return $this->armour[2];
            }  else if (mathlib::isInArc($shooterCompassHeading, Mathlib::addToDirection(30,$tf), Mathlib::addToDirection(150,$tf) )){
                return $this->armour[3];
            } 
                
            return $this->armour[0];
        }
        
        public function onAdvancingGamedata($ship)
        {
            foreach ($this->systems as $system)
            {
                $system->onAdvancingGamedata($ship);
            }
        }


        public function setInitialSystemData($ship)
        {
            foreach ($this->systems as $system)
            {
                $system->setInitialSystemData($ship);
            }
        }
        

	}

?>
