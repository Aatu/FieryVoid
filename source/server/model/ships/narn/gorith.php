<?php
class Gorith extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 240;
		$this->faction = "Narn";
        $this->phpclass = "Gorith";
        $this->shipClass = "Gorith flight";
		$this->imagePath = "img/ships/gorith.png";
        
        $this->forwardDefense = 7;
        $this->sideDefense = 9;
        $this->freethrust = 12;
        $this->offensivebonus = 5;
        $this->jinkinglimit = 8;
        $this->turncost = 0.33;
        
		$this->iniativebonus = 90;
        
        for ($i = 0; $i<6; $i++){
			
			$armour = array(2, 2, 2, 2);
			$fighter = new Fighter($armour, 10, $this->id);
			$fighter->displayName = "Gorith Medium Fighter";
			$fighter->imagePath = "img/ships/gorith.png";
			$fighter->iconPath = "img/ships/gorith_large.png";
			
			
			$fighter->addFrontSystem(new PairedParticleGun(330, 30, 3));
			
			
			$this->addSystem($fighter);
			
		}
		
		
    }

}



?>