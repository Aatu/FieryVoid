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
		protected $adaptiveArmorController = null; //Adaptive Armor Controller object (if present) - would be individual one for every fighter
		
		public $possibleCriticals = array();
		
			
		public $criticals = array();
		
		
		function __construct($name, $armour, $maxhealth, $flight){
			parent::__construct($armour, $maxhealth, 0, 0 );
			
                    $this->name = $name;
                    $this->flightid = $flight;
			
			
		}

		public function stripForJson() {
			$strippedSystem = parent::stripForJson();

			$strippedSystem->fighter = true;
			$strippedSystem->location = $this->location;
			$strippedSystem->flightid = $this->flightid;
			$strippedSystem->systems = $this->systems;

			return $strippedSystem;
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
        
		
		public function getAdaptiveArmorController(){
			/*this doesn't work for some reason; need to look for AA controller among systems... OTOH a particular fighter doesn't have all that many systems!
			return $this->adaptiveArmorController;*/
			foreach ($this->systems as $fs) {
                if ($fs instanceof AdaptiveArmorController) return $fs;
            }
			return null;
		}
		public function createAdaptiveArmorController($AAtotal, $AApertype, $AApreallocated){ //$AAtotal, $AApertype, $AApreallocated
			$this->adaptiveArmorController = new AdaptiveArmorController($AAtotal, $AApertype, $AApreallocated); 
			return $this->getAdaptiveArmorController();
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
		
		$bonusCrit = $this->critRollMod + $ship->critRollMod;	//one-time penalty to dropout roll
		$crits = array_values($crits); //in case some criticals were deleted!
		
		$dropOutBonus = $gamedata->getShipById($this->flightid)->getDropOutBonus();
		if (($d + $dropOutBonus + $bonusCrit) > $this->getRemainingHealth()){
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
		

    public  function getArmourBase($target, $shooter, $dmgClass, $pos=null){ //gets armor value - from indicated direction if necessary direction 
		//$pos is to be included if launch position is different than firing unit position
		if($pos==null){
			$loc = $target->doGetHitSection($shooter); //finds array with relevant data!
		}else{ //firing position indicated!
			$loc = $target->doGetHitSectionPos($pos); //finds array with relevant data!
		}
		return $loc["armour"];
    }
	
	public function getArmourAdaptive($target, $shooter, $dmgClass, $pos=null){ //gets adaptive part of armour
		$armour = 0;
		$AAController = $this->getAdaptiveArmorController(); //AA subsystem of fighter, not of fighter flight
		if($AAController){
			$armour = $AAController->getArmourValue($dmgClass);		
/*$ss = $AAController->getArmourValue($dmgClass);
throw new Exception("getArmourAdaptive (fighter)! $dmgClass $armour");		
*/
		}
		return $armour;
	}
	    
		

    public function onAdvancingGamedata($ship, $gamedata)
    {
        foreach ($this->systems as $system)
        {
            $system->onAdvancingGamedata($ship, $gamedata);
        }
    }


    public function setInitialSystemData($ship)
    {
        foreach ($this->systems as $system)
        {
            $system->setInitialSystemData($ship);
        }
    }

    public function getRammingFactor()
    {
        $dmg = 0;
        $dmg += $this->maxhealth;
        foreach ($this->armour as $armorvalue) {
            $dmg += $armorvalue;
        }
        return $dmg;
    } //endof function getRammingFactor


}

?>
