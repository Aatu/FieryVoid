<?php

class Templar extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 180;
        $this->faction = "Orieni";
        $this->phpclass = "Templar";
        $this->shipClass = "Templar Light flight";
        $this->imagePath = "img/ships/templar.png";

        $this->forwardDefense = 7;
        $this->sideDefense = 6;
        $this->freethrust = 12;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 10;
        $this->turncost = 0.33;
        
    	$this->iniativebonus = 100;
        
        for ($i = 0; $i<6; $i++){
            $armour = array(1, 1, 1, 1);
            $fighter = new Fighter("templar", $armour, 9, $this->id);
            $fighter->displayName = "Templar Light Fighter";
            $fighter->imagePath = "img/ships/templar.png";
            $fighter->iconPath = "img/ships/templar_large.png";

            $fighter->addFrontSystem(new PairedGatlingGun(330, 30));

            $this->addSystem($fighter);
        }
    }
}

?>
