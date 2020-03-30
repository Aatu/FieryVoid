<?php

class Shadras extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 300;
        $this->faction = "Balosian";
        $this->phpclass = "shadras";
        $this->shipClass = "Shadras Heavy Fighters";
        $this->imagePath = "img/ships/shadras.png";
		$this->isd = 2250;

        $this->forwardDefense = 8;
        $this->sideDefense = 7;
        $this->freethrust = 10;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 6;
        $this->turncost = 0.33;

        $this->iniativebonus = 16*5;
        $this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
			
            $armour = array(3, 1, 2, 2);
            $fighter = new Fighter("shadras", $armour, 12, $this->id);
            $fighter->displayName = "Shadras";
            $fighter->imagePath = "img/ships/shadras.png";
            $fighter->iconPath = "img/ships/shadras_large.png";

			$gun = new LightParticleBeam(330, 30, 2,3);
			$gun->displayName = "Light Particle Gun";
			$fighter->addFrontSystem($gun);
            
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
            
            $this->addSystem($fighter);
	}
    }
}
?>
