<?php
class Thorun extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 300;
	$this->faction = "Dilgar";
        $this->phpclass = "Thorun";
        $this->shipClass = "Thorun Heavy Dartfighters";
	$this->imagePath = "img/ships/thorun.png";
        
        $this->forwardDefense = 8;
        $this->sideDefense = 7;
        $this->freethrust = 12;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 6;
        $this->turncost = 0.33;
        
	$this->iniativebonus = 80;
        
        for ($i = 0; $i<6; $i++){

			$armour = array(2, 1, 2, 2);
			$fighter = new Fighter("thorun", $armour, 13, $this->id);
			$fighter->displayName = "Thorun Heavy Dartfighter";
			$fighter->imagePath = "img/ships/thorun.png";
			$fighter->iconPath = "img/ships/thorun_large.png";


			$fighter->addFrontSystem(new PairedLightBoltCannon(330, 30, 4));


			$this->addSystem($fighter);

		}


    }

}

?>