<?php

class GammaStarfury extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 366;
        $this->faction = "EA";
        $this->phpclass = "GammaStarfury";
        $this->shipClass = "Starfury: Aurora Gamma Heavy flight";
        $this->imagePath = "img/ships/auroraStarfury.png";
	$this->variantOf = "Starfury: Aurora Heavy flight";	    
	$this->isd = 2259;
	$this->notes = 'Non-atmospheric.';
        
        $this->forwardDefense = 8;
        $this->sideDefense = 6;
        $this->freethrust = 12;
        $this->offensivebonus = 5;
        $this->jinkinglimit = 6;
        $this->turncost = 0.33;
        
    	$this->iniativebonus = 80;
        $this->populate();        
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){        
            $armour = array(3, 2, 2, 2);
            $fighter = new Fighter("gammaStarfury", $armour, 13, $this->id);
            $fighter->displayName = "Aurora";
            $fighter->imagePath = "img/ships/auroraStarfury.png";
            $fighter->iconPath = "img/ships/auroraStarfury_largei.png";

            $missileRack = new FighterMissileRack(4, 330, 30);
            $missileRack->firingModes = array(
                1 => "FY"
            );

            $missileRack->missileArray = array(
                1 => new MissileFY(330, 30)
            );

            $frontGun = new PairedParticleGun(330, 30, 4);
            $frontGun->displayName = "Uni-Pulse Cannon";
            $fighter->addFrontSystem($missileRack);
            $fighter->addFrontSystem($frontGun);
            $this->addSystem($fighter);
	   }
    }
}

?>
