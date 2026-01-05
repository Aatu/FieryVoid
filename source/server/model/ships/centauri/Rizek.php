<?php

class Rizek extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 120;
	$this->faction = "Centauri Republic";
    $this->phpclass = "Rizek";
    $this->shipClass = "Rizek Defense Fighters";
        $this->variantOf = "Razik Light Fighters";
		$this->occurence = "common";
	$this->imagePath = "img/ships/Rizek.png";
	$this->unofficial = true;

    $this->notes = 'Only available to House Valheru.';
	    
	$this->isd = 2200;
        
        $this->forwardDefense = 6;
        $this->sideDefense = 5;
        $this->freethrust = 8;
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
            $armour = array(1, 1, 1, 1);
            $fighter = new Fighter("Rizek", $armour, 8, $this->id);
            $fighter->displayName = "Rizek";
            $fighter->imagePath = "img/ships/Rizek.png";
            $fighter->iconPath = "img/ships/Rizek_large.png";

	        $wpn = new FtrDefenseGun(330, 30, 1); //$startArc, $endArc, $nrOfShots
	        $fighter->addFrontSystem($wpn);
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
            $this->addSystem($fighter);
        }
    }
}

?>
