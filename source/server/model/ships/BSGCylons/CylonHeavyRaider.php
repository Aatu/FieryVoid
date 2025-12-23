<?php
class CylonHeavyRaider extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 450;
        $this->faction = "BSG Cylons";
        $this->phpclass = "CylonHeavyRaider";
        $this->shipClass = "Heavy Raider";
        $this->imagePath = "img/ships/BSG/CylonHeavyRaider.png";
		$this->unofficial = true;
	    $this->isd = 2041;
        $this->canvasSize = 100;

        $this->forwardDefense = 8;
        $this->sideDefense = 10;
        $this->freethrust = 8;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 4;
        $this->turncost = 0.33;
        $this->turndelaycost = 0.25;
		
		$this->hangarRequired = 'superheavy'; //for fleet check
        $this->iniativebonus = 70;
    	$this->superheavy = true;
        $this->maxFlightSize = 3;//this is a superheavy fighter originally intended as single unit, limit flight size to 3
	
		$this->populate();
	
	}

    public function populate(){        

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
		
		for ($i = 0; $i < $toAdd; $i++) {
			$armour = array(3, 1, 2, 2);
			$fighter = new Fighter("CylonHeavyRaider ", $armour, 24, $this->id);
			$fighter->displayName = "Heavy Raider";
			$fighter->imagePath = "img/ships/BSG/CylonHeavyRaider.png.png";
			$fighter->iconPath = "img/ships/BSG/CylonHeavyRaider_Large.png";

			$leftgun = new NexusShatterGunFtr(180, 360, 1);
			$fighter->addFrontSystem($leftgun);
	        $lightGasGun = new NexusLightGasGunFtr(300, 60, 1); //$startArc, $endArc, $nrOfShots
	        $fighter->addFrontSystem($lightGasGun);
			$rightgun = new NexusShatterGunFtr(0, 180, 1);
			$fighter->addFrontSystem($rightgun);
        
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			$this->addSystem($fighter);
		}
    }

//    public function populate(){
//        return;
//    }
    

}

?>
