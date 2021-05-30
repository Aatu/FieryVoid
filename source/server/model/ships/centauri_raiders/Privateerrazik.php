<?php

class Privateerrazik extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 180;
	$this->faction = "Raiders";
        $this->phpclass = "Privateerrazik";
        $this->shipClass = "Centauri Privateer Razik Light Fighters";
	$this->imagePath = "img/ships/razik.png";
        
		$this->notes = "Used only by Centauri Privateers";
		$this->notes .= "<br>Junkyard Dogs may field a Razik flight";

        $this->forwardDefense = 6;
        $this->sideDefense = 5;
        $this->freethrust = 14;
        $this->offensivebonus = 5;
        $this->jinkinglimit = 10;
        $this->turncost = 0.33;
        	    
	$this->isd = 2105;
	$this->limited = 10;
        
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
            $fighter->displayName = "Razik";
            $fighter->imagePath = "img/ships/razik.png";
            $fighter->iconPath = "img/ships/razik_large.png";

            $fighter->addFrontSystem(new PairedParticleGun(330, 30, 1));
	    $fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack

            $this->addSystem($fighter);
        }
    }
}

?>
