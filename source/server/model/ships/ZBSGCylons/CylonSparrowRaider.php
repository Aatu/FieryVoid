<?php
class CylonSparrowRaider extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 40*6;
        $this->faction = "ZPlaytest BSG Cylons";
        $this->phpclass = "CylonSparrowRaider";
        $this->shipClass = "Sparrow Raider Medium Flight";
        $this->imagePath = "img/ships/BSG/CylonSparrowRaider.png";
		$this->unofficial = true;

        $this->isd = 1948;

	    $this->notes = 'Atmospheric.';
        
        $this->forwardDefense = 5;
        $this->sideDefense = 6;
        $this->freethrust = 13;
        $this->offensivebonus = 3;
        $this->jinkinglimit = 10;
        $this->turncost = 0.33;
		$this->turndelay = 0;
        
        $this->iniativebonus = 100;
        $this->hasNavigator = false;
        $this->populate();       

    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){            
            $armour = array(3, 2, 2, 2);
            $fighter = new Fighter("CylonSparrowRaider", $armour, 9, $this->id);
            $fighter->displayName = "Sparrow Raider";
            $fighter->imagePath = "img/ships/BSG/CylonSparrowRaider.png";
            $fighter->iconPath = "img/ships/BSG/CylonSparrowRaider_large.png";

            $frontGun = new BSGKineticEnergyWeapon(330, 30, 2, 4);
            $frontGun->displayName = "Kinetic Energy Cannon";

            $fighter->addFrontSystem(new FighterMissileRack(2, 330, 30));
            $fighter->addFrontSystem($frontGun);
            $fighter->addFrontSystem(new FighterMissileRack(2, 330, 30));

			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            
            $this->addSystem($fighter);
        }
    }

	
}
?>
