<?php

class HighTemplar extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 240;
        $this->faction = "Orieni";
        $this->phpclass = "HighTemplar";
        $this->shipClass = "High Templar Light flight";
        $this->imagePath = "img/ships/highTemplar.png";

        $this->forwardDefense = 7;
        $this->sideDefense = 6;
        $this->freethrust = 12;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 10;
        $this->turncost = 0.33;
        
    	$this->iniativebonus = 100;
        $this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(2, 1, 1, 1);
            $fighter = new Fighter("templar", $armour, 9, $this->id);
            $fighter->displayName = "High Templar Light Fighter";
            $fighter->imagePath = "img/ships/highTemplar.png";
            $fighter->iconPath = "img/ships/highTemplar_large.png";

            $fighter->addFrontSystem(new PairedGatlingGun(330, 30));
            $fighter->addFrontSystem(new FighterMissileRack(2, 330, 30));

            $this->addSystem($fighter);
        }
    }
}

?>
