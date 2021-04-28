<?php
class DalithornCutter extends SuperHeavyFighter{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 360;
        $this->faction = "ZNexus Dalithorn";
        $this->phpclass = "DalithornCutter";
        $this->shipClass = "Cutter";
        $this->imagePath = "img/ships/Nexus/DalithornCutter.png";
		$this->unofficial = true;
	    $this->isd = 1879;
        $this->canvasSize = 100;

        $this->forwardDefense = 8;
        $this->sideDefense = 10;
        $this->freethrust = 6;
        $this->offensivebonus = 3;
        $this->jinkinglimit = 4;
        $this->turncost = 0.33;
        $this->turndelaycost = 0.25;
		
		$this->hangarRequired = 'superheavy'; //for fleet check
        $this->iniativebonus = 70;
        $this->maxFlightSize = 3;//this is a superheavy fighter originally intended as single unit, limit flight size to 3
	
		$this->populate();
	
	}

    public function populate(){        

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
		
		for ($i = 0; $i < $toAdd; $i++) {
			$armour = array(2, 1, 2, 2);
			$fighter = new Fighter("DalithornCutter", $armour, 24, $this->id);
			$fighter->displayName = "Cutter";
			$fighter->imagePath = "img/ships/Nexus/DalithornCutter.png.png";
			$fighter->iconPath = "img/ships/Nexus/DalithornCutter_Large.png";

	        $lightGasGun = new NexusLightGasGunFtr(300, 60, 1); //$startArc, $endArc, $nrOfShots
	        $fighter->addFrontSystem($lightGasGun);
			$shattergun = new NexusShatterGunFtr(0, 360, 1);
			$fighter->addFrontSystem($shattergun);
        
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			$this->addSystem($fighter);
		}
    }

//    public function populate(){
//        return;
//    }
    

}

?>
