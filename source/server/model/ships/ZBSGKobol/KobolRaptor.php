<?php
class KobolRaptor extends SuperHeavyFighter{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 360;
        $this->faction = "ZPlaytest 12 Colonies of Kobol (Tier 1)";
        $this->phpclass = "KobolRaptor";
        $this->shipClass = "Raptor (Beta prototype)";
        $this->imagePath = "img/ships/BSG/raptor.png";
//	    $this->isd = ;
//        $this->canvasSize = 60;
		$this->unofficial = true;

	    $this->notes = 'Atmospheric.';
		$this->notes .= '<br>Provides +10 Initiative to all Vipers and Raptor variants within 5 hexes.';
		
        $this->forwardDefense = 8;
        $this->sideDefense = 9;
        $this->freethrust = 10;
        $this->offensivebonus = 6;
        $this->jinkinglimit = 4;
        $this->turncost = 0.33;
//        $this->turndelaycost = 0.10;

		$this->hangarRequired = 'superheavy'; //for fleet check
        $this->iniativebonus = 70;
        $this->hasNavigator = true;
        $this->maxFlightSize = 3;//this is a superheavy fighter originally intended as single unit, limit flight size to 3

		$this->populate();
		
	}
        
    public function populate(){        

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
		
		for ($i = 0; $i < $toAdd; $i++) {
			$armour = array(2, 1, 2, 2);
			$fighter = new Fighter("raptor", $armour, 20, $this->id);
			$fighter->displayName = "Raptor";
			$fighter->imagePath = "img/ships/BSG/raptor.png";
			$fighter->iconPath = "img/ships/BSG/raptor_large.png";

//            $frontGun = new PairedParticleGun(330, 30, 3);
            $frontGun = new LightScattergun(330, 30); //always a single mount for this weapon
            $frontGun->displayName = "Heavy MEC Cannon";
//			$ewGun = new SensorSpearFtr(240, 120, 0);

            $fighter->addFrontSystem($frontGun);
//			$fighter->addFrontSystem($ewGun);
        
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			$this->addSystem($fighter);
		}
    }

}