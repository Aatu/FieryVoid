<?php

class GammaStarfuryAM extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 366;
        $this->faction = "Earth Alliance";
        $this->phpclass = "GammaStarfuryAM";
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

            //ammo magazine itself (AND its missile options)
            $ammoMagazine = new AmmoMagazine(4); //pass magazine capacity - actual number of rounds, NOT number of salvoes
            $fighter->addAftSystem($ammoMagazine); //fit to ship immediately
            $ammoMagazine->addAmmoEntry(new AmmoMissileFY(), 0); //add Dogfight missile as an option - but do NOT load any actual missiles at this moment - so weapon data is actually filled with _something_!
            $this->enhancementOptionsEnabled[] = 'AMMO_FY';//add enhancement options for missiles - Class-FY

            $frontGun = new PairedParticleGun(330, 30, 4);
            $frontGun->displayName = "Uni-Pulse Cannon";
            $fighter->addFrontSystem($frontGun);
            $fighter->addFrontSystem(new AmmoFighterRack(330, 30, $ammoMagazine, false)); //$startArc, $endArc, $magazine, $base            
		    $fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack	
            $this->addSystem($fighter);
	   }
    }
}

?>
