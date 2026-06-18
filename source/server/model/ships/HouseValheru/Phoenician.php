<?php
class Phoenician extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 37*6;
        $this->faction = "House Valheru";
        $this->phpclass = "Phoenician";
        $this->shipClass = "Phoenician Assault Fighters";
		$this->imagePath = "img/ships/phalan.png";
		$this->isd = 2200;
        
        $this->forwardDefense = 9;
        $this->sideDefense = 8;
        $this->freethrust = 9;
        $this->offensivebonus = 3;
        $this->jinkinglimit = 6;
        $this->turncost = 0.33;
        
		$this->iniativebonus = 80;
        $this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
			
			$armour = array(2, 1, 1, 1);
			$fighter = new Fighter("Phoenician", $armour, 12, $this->id);
			$fighter->displayName = "Phoenician";
			$fighter->imagePath = "img/ships/Phoenician.png";
			$fighter->iconPath = "img/ships/Phoenician_large.png";
			
				$fighter->addFrontSystem(new PairedParticleGun(330, 30, 2));
		        $largeGun = new CustomLightMatterCannonF(330, 30); 
            		$largeGun->exclusive = true; 
            		$fighter->addFrontSystem($largeGun);			
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			$this->addSystem($fighter);
			
		}	
		
    }

}



?>
