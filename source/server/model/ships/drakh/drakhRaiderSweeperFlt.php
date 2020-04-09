<?php
class DrakhRaiderSweeperFlt extends FighterFlight{
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 150*6;
        $this->faction = "Drakh";
	$this->factionAge = 2; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
        $this->phpclass = "DrakhRaiderSweeperFlt";
        $this->shipClass = "Raider Sweeper Interceptors";
        $this->variantOf = "Raider Assault Fighters";
	        $this->occurence = 'rare';
        $this->imagePath = "img/ships/DrakhRaider.png";
        
        $this->isd = 2255;
        $this->unofficial = true;
        $this->gravitic = true;
        
        $this->forwardDefense = 9;
        $this->sideDefense = 10;
        $this->freethrust = 10;
        $this->offensivebonus = 7;
        $this->jinkinglimit = 4; //deliberately low jinking limit and Init, they're SHFs grouped for convenience!
        $this->turncost = 0.33;
        
		$this->hangarRequired = 'Raiders'; //for fleet check
    	$this->iniativebonus = 14 *5; 
	    $this->advancedArmor = true; 
    	$this->superheavy = true;
        $this->maxFlightSize = 3;//this is a superheavy fighter originally intended as single unit, limit flight size to 3
		
		
        $this->populate();
		
    }
    
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(4, 3, 4, 4);
            $fighter = new Fighter("DrakhRaiderSweeperFlt", $armour, 25, $this->id);
            $fighter->displayName = "Raider Sweeper";
            $fighter->imagePath = "img/ships/DrakhRaider.png";
            $fighter->iconPath = "img/ships/DrakhRaider_Large.png"; 
		$sweeper = new customPhaseSweeper(330, 30);
		$fighter->addFrontSystem($sweeper);
		            
       		//Absorbtion Shield, 1 points
        	$fighter->addAftSystem(new AbsorbtionShield(0, 1, 0, 1, 0, 360));
			
			//Improved Sensors
            $fighter->addAftSystem(new Fighterimprsensors(0, 1, 0));
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
            
        	$this->addSystem($fighter);
       }
    }
    
        public function getInitiativebonus($gamedata){
	    $iniBonus = parent::getInitiativebonus($gamedata);
            //may be boosted by  Raider Controller...
	    $iniBonus += DrakhRaiderController::getIniBonus($this);
            return $iniBonus;
        }	
    
}
?>
