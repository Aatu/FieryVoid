<?php
class DalithornPinnace extends SuperHeavyFighter{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 690;
        $this->faction = "ZNexus Dalithorn";
        $this->phpclass = "DalithornPinnace";
        $this->shipClass = "Pinnace";
        $this->imagePath = "img/ships/Nexus/DalithornPinnacev2.png";
		$this->unofficial = true;
	    $this->isd = 2113;
        $this->canvasSize = 150;

        $this->forwardDefense = 8;
        $this->sideDefense = 10;
        $this->freethrust = 10;
        $this->offensivebonus = 5;
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
			$armour = array(4, 3, 3, 3);
			$fighter = new Fighter("DalithornCutter", $armour, 30, $this->id);
			$fighter->displayName = "Cutter";
			$fighter->imagePath = "img/ships/Nexus/DalithornPinnacev2.png";
			$fighter->iconPath = "img/ships/Nexus/DalithornPinnace_Large.png";

			$light1 = new NexusMinigunFtr(180, 360, 1);
			$fighter->addFrontSystem($light1);
	        $autocannon = new NexusAutocannonFtr(300, 60, 1); //$startArc, $endArc, $nrOfShots
	        $fighter->addFrontSystem($autocannon);
			$light2 = new NexusMinigunFtr(0, 180, 1);
			$fighter->addFrontSystem($light2);
        
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			$this->addSystem($fighter);
		}
    }

//    public function populate(){
//        return;
//    }
    

}

?>
