<?php
class Nial extends FighterFlight{

    function __construct($id, $userid, $name,  $movement){
        parent::__construct($id, $userid, $name,  $movement);

		$this->pointCost = 636;
		$this->faction = "Minbari";
		$this->phpclass = "Nial";
		$this->shipClass = "Nial flight";
			// need picture
		$this->imagePath = "ships/frazi.png";

        $this->forwardDefense = 6;
        $this->sideDefense = 8;
        $this->freethrust = 14;
        $this->offensivebonus = 6;
        $this->jinkinglimit = 6;
        $this->turncost = 0.33;
        $this->iniativebonus = 85;
        $this->gravitic = true;

        for ($i = 0; $i<6; $i++){
            $armour = array(4, 4, 4, 4);
            $nial = new Fighter($armour, 14, $this->id);
            $nial->displayName = "Nial Heavy Fighter";
        // need picture
            $nial->imagePath = "ships/frazi.png";
        // need picture
            $nial->iconPath = "ships/frazi_large.png";
            $nial->addFrontSystem(new LightFusionCannon3(300, 60, 4));
            $nial->addFrontSystem(new Jammer(0, 1, 0));
            $this->addSystem($nial);
	}
    }
}



?>
