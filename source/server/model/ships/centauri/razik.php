<?php

class Razik extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 180;
	$this->faction = "Centauri";
        $this->phpclass = "Razik";
        $this->shipClass = "Razik Light flight";
	$this->imagePath = "img/ships/razik.png";
        
        $this->forwardDefense = 6;
        $this->sideDefense = 5;
        $this->freethrust = 14;
        $this->offensivebonus = 5;
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
            $armour = array(1, 1, 2, 2);
            $fighter = new Fighter("razik", $armour, 8, $this->id);
            $fighter->displayName = "Razik Light Fighter";
            $fighter->imagePath = "img/ships/razik.png";
            $fighter->iconPath = "img/ships/razik_large.png";

            $fighter->addFrontSystem(new PairedParticleGun(330, 30, 1));

            $this->addSystem($fighter);
        }
    }
}

?>
