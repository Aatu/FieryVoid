<?php
class AuroraStarfury extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 342;
		$this->faction = "EA";
        $this->phpclass = "AuroraStarfury";
        $this->shipClass = "Aurora Starfury flight";
		$this->imagePath = "img/ships/auroraStarfury.png";
        
        $this->forwardDefense = 8;
        $this->sideDefense = 6;
        $this->freethrust = 12;
        $this->offensivebonus = 5;
        $this->jinkinglimit = 6;
        $this->turncost = 0.33;
        
		$this->iniativebonus = 80;
        
        for ($i = 0; $i<6; $i++){

			$armour = array(3, 2, 2, 2);
			$fighter = new Fighter($armour, 13, $this->id);
			$fighter->displayName = "Starfury Medium Fighter";
			$fighter->imagePath = "img/ships/auroraStarfury.png";
			$fighter->iconPath = "img/ships/auroraStarfury_largei.png";


			$fighter->addFrontSystem(new PairedParticleGun(330, 30, 4));


			$this->addSystem($fighter);

		}


    }

}



?>