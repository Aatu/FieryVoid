<?php
class DalithornTransportCutter extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 25*6;
        $this->faction = "Nexus Support Units";
        $this->phpclass = "DalithornTransportCutter";
        $this->shipClass = "Dalithorn Transport Cutter";
        $this->imagePath = "img/ships/Nexus/Dalithorn_Cutter2.png";
		$this->unofficial = true;
	    $this->isd = 1893;
        $this->canvasSize = 120;

	    $this->notes = '<br>Carries 5 boxes of cargo';

        $this->forwardDefense = 8;
        $this->sideDefense = 10;
        $this->freethrust = 6;
        $this->offensivebonus = 2;
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
			$armour = array(2, 1, 2, 2);
			$fighter = new Fighter("DalithornCutter", $armour, 24, $this->id);
			$fighter->displayName = "Transport Cutter";
			$fighter->imagePath = "img/ships/Nexus/Dalithorn_Cutter2.png.png";
			$fighter->iconPath = "img/ships/Nexus/Dalithorn_Cutter_Large2.png";

			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			$this->addSystem($fighter);
		}
    }

//    public function populate(){
//        return;
//    }
    

}

?>
