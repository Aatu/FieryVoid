<?php

class DroftaE extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 40*6;
        $this->faction = "Markab";
        $this->phpclass = "DroftaE";
        $this->shipClass = "Drofta Medium Fighters (early)";
	    $this->variantOf = "Drofta Medium Fighters";
        $this->imagePath = "img/ships/MarkabDrofta.png";
		$this->isd = 1925;
        
        $this->forwardDefense = 6;
        $this->sideDefense = 8;
        $this->freethrust = 7;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 8;
        $this->turncost = 0.33;
        
    	$this->iniativebonus = 18 *5;
        $this->populate();        
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){        
            $armour = array(3, 1, 2, 2);
            $fighter = new Fighter("DroftaE", $armour, 10, $this->id);
            $fighter->displayName = "Drofta";
            $fighter->imagePath = "img/ships/MarkabDrofta.png";
            $fighter->iconPath = "img/ships/MarkabDroftaLARGE.png";

            $frontGun = new LightParticleBeam(330, 30, 1);
            $frontGun->displayName = "Ultralight Particle Beam";
            $fighter->addFrontSystem($frontGun);
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
            $this->addSystem($fighter);
	   }
    }
}

?>
