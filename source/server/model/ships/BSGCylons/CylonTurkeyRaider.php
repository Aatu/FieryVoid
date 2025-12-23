<?php
class CylonTurkeyRaider extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 600;
        $this->faction = "BSG Cylons";
        $this->phpclass = "CylonTurkeyRaider";
        $this->shipClass = "Turkey Assault Raider";
		$this->occurence = "common";
			
        $this->imagePath = "img/ships/BSG/CylonHeavyRaider.png";
//	    $this->isd = ;
        $this->canvasSize = 80;
		$this->unofficial = true;

	    $this->notes = 'Atmospheric.';
	
		
        $this->forwardDefense = 8;
        $this->sideDefense = 10;
        $this->freethrust = 11;
        $this->offensivebonus = 6;
        $this->jinkinglimit = 4;
        $this->turncost = 0.33;
        $this->turndelaycost = 0.10;

		$this->hangarRequired = 'superheavy'; //for fleet check
        $this->iniativebonus = 60;
        $this->hasNavigator = true;
    	$this->superheavy = true;
        $this->maxFlightSize = 3;//this is a superheavy fighter originally intended as single unit, limit flight size to 3

		$this->populate();
		
	}
        
    public function populate(){        

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
		
		for ($i = 0; $i < $toAdd; $i++) {
			$armour = array(5, 4, 4, 3);
			$fighter = new Fighter("CylonTurkeyRaider", $armour, 20, $this->id);
			$fighter->displayName = "Assault Raider";
			$fighter->imagePath = "img/ships/BSG/CylonHeavyRaider.png";
			$fighter->iconPath = "img/ships/BSG/CylonHeavyRaider_Large.png";

//			$frontGun = new BSGKineticEnergyWeapon(330, 30, 2, 4);
//			$frontGun->displayName = "Kinetic Energy Cannon";

         
			$hvyGun = new BSGHvyKineticEnergyWeapon(330, 30, 1); //$startArc, $endArc, $nrOfShots
			$hvyGun->displayName = "Heavy Kinetic Energy Cannon";
			$missile1 = new FighterMissileRack(3, 300, 60);
			$missile1->displayName = "Missile Pod";
			$missile2 = new FighterMissileRack(3, 300, 60);
			$missile2->displayName = "Missile Pod";
			$centurion = new BSGCenturions(300, 60);

//			
			$fighter->addFrontSystem($hvyGun);
			$fighter->addFrontSystem($centurion);

			$fighter->addAftSystem($missile1);
			$fighter->addAftSystem($missile2);
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			$this->addSystem($fighter);
		}
    }
    
    
	
}
