<?php
class TechnicalTestbedFtr extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 150*6;
		$this->faction = "Custom Ships";
		$this->phpclass = "TechnicalTestbedFtr";
		$this->shipClass = "Testbed Medium Fighters";
		$this->imagePath = "img/ships/ShadowFighter.png";
	    
		$this->isd = 2202;
		$this->factionAge = 1; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
        
		
		$this->advancedArmor = true;
		$this->hardAdvancedArmor = true;
		
		$this->forwardDefense = 70;
		$this->sideDefense = 50;
		$this->freethrust = 18;
		$this->offensivebonus = 7;
		$this->jinkinglimit = 8;
		$this->turncost = 0.33;
        
		
        $this->gravitic = true;
		$this->critRollMod = -100; 
		
		$this->iniativebonus = 90;
		$this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
			
			$armour = array(4, 4, 4, 4);
			$fighter = new Fighter("TechnicalTestbedFtr", $armour, 20, $this->id);
			$fighter->displayName = "Testbed Fighter";
			$fighter->imagePath = "img/ships/ShadowFighter.png";
			$fighter->iconPath = "img/ships/ShadowFighter_LARGE.png";
			
			
			//ramming attack - no room to show it cleanly on Aft, Diffuser and Tendrils take a lot of room...			
			$fighter->addFrontSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			$fighter->addFrontSystem(new ftrPolarityCannon(330, 30, 1));//arcfrom, arcto, numberofguns		
			//$fighter->addFrontSystem(new LightPlasmaAccelerator(330, 30, 1));//arcfrom, arcto, numberofguns		
			
			//Trek-style shielding
			//$fighter->addAftSystem(new TrekShieldFtr(2, 20, 4, 1) ); //armor, health, rating, recharge
					
			
			
			$this->addSystem($fighter);
			
		}
		
		
    }


}



?>
