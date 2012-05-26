<?php
class Sitara extends FighterFlight{
    
    function __construct($id, $userid, $name,  $movement){
        parent::__construct($id, $userid, $name,  $movement);
        
		$this->pointCost = 270;
		$this->faction = "Centauri";
        $this->phpclass = "Sitara";
        $this->shipClass = "Sitara flight";
		$this->imagePath = "ships/sentri.png";
        
        $this->forwardDefense = 7;
        $this->sideDefense = 5;
        $this->freethrust = 10;
        $this->offensivebonus = 7;
        $this->jinkinglimit = 8;
        $this->turncost = 0.33;
        
		$this->iniativebonus = 90;
        
        for ($i = 0; $i<6; $i++){

			$armour = array(3, 2, 3, 3);
			$fighter = new Fighter($armour, 10, $this->id);
			$fighter->displayName = "Sitara Medium Fighter";
			$fighter->imagePath = "ships/sentri.png";
			$fighter->iconPath = "ships/sentri_large.png";


			$fighter->addFrontSystem(new IonBolt(330, 30));


			$this->addSystem($fighter);

		}


    }

}
