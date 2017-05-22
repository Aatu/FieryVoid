<?php

class Drofta extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 246;
        $this->faction = "Markab";
        $this->phpclass = "Drofta";
        $this->shipClass = "Dofta Medium Fighters";
        $this->imagePath = "img/ships/auroraStarfury.png";
		$this->isd = 1925;
        
        $this->forwardDefense = 6;
        $this->sideDefense = 8;
        $this->freethrust = 7;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 8;
        $this->turncost = 0.33;
        
    	$this->iniativebonus = 80;
        $this->populate();        
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){        
            $armour = array(3, 1, 2, 2);
            $fighter = new Fighter("Drofta", $armour, 10, $this->id);
            $fighter->displayName = "Drofta Medium Fighter";
            $fighter->imagePath = "img/ships/auroraStarfury.png";
            $fighter->iconPath = "img/ships/auroraStarfury_largei.png";

            $frontGun = new LightParticleBeam(330, 30, 1);
            $frontGun->displayName = "Ultralight Particle Beam";
            $fighter->addFrontSystem($frontGun);
            $this->addSystem($fighter);
	   }
    }
}

?>
