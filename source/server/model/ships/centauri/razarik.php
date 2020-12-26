<?php

class Razarik extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 210;
	$this->faction = "Centauri";
        $this->phpclass = "Razarik";
        $this->shipClass = "Razarik Torpedo Fighters";
        $this->variantOf = "Razik Light Fighters";
	$this->occurence = "rare";
	$this->imagePath = "img/ships/razik.png";
	    
	$this->isd = 2105;
        
        $this->forwardDefense = 6;
        $this->sideDefense = 5;
        $this->freethrust = 12;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 10;
        $this->turncost = 0.33;
        $this->occurence = "rare";
        
	$this->iniativebonus = 100;
        $this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(1, 1, 2, 2);
            $fighter = new Fighter("razarik", $armour, 8, $this->id);
            $fighter->displayName = "Razarik";
            $fighter->imagePath = "img/ships/razik.png";
            $fighter->iconPath = "img/ships/razik_large.png";

            $fighter->addFrontSystem(new FighterTorpedoLauncher(2, 330, 30));
            $fighter->addFrontSystem(new PairedParticleGun(330, 30, 1));
            $fighter->addFrontSystem(new FighterTorpedoLauncher(2, 330, 30));

			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
            $this->addSystem($fighter);
        }
    }
}

?>
