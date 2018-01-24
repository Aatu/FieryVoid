<?php

class ShiningLight extends HKFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 192;
        $this->faction = "Orieni";
        $this->phpclass = "ShiningLight";
        $this->shipClass = "Shining Light Flight";
        $this->imagePath = "img/ships/templar.png";

        $this->forwardDefense = 8;
        $this->sideDefense = 8;
        $this->freethrust = 9;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 4;
        $this->turncost = 0.33;
        $this->ramDamage = 60;
        
    	$this->iniativebonus = 300;
        
        for ($i = 0; $i<6; $i++){
            $armour = array(0, 0, 0, 0);
            $fighter = new Fighter("shininglight", $armour, 9, $this->id);
            $fighter->ranDamage = $this->ramDamage;
            $fighter->displayName = "Shining Light Hunter Killer";
            $fighter->imagePath = "img/ships/templar.png";
            $fighter->iconPath = "img/ships/templar_large.png";

            $fighter->addFrontSystem(new PairedGatlingGun(330, 30));

            $this->addSystem($fighter);
        }
    }
}

?>
