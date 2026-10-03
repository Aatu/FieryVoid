<?php
class DalithornCustomsVessel extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 50*6;
        $this->faction = "Nexus Dalithorn Commonwealth (early)";
        $this->phpclass = "DalithornCustomsVessel";
        $this->shipClass = "Krethim Customs Vessel";
			$this->variantOf = "Melivorn Cutter";
			$this->occurence = "common";
        $this->imagePath = "img/ships/Nexus/Dalithorn_Cutter2.png";
		$this->unofficial = true;
	    $this->isd = 1895;
        $this->canvasSize = 120;

        $this->forwardDefense = 8;
        $this->sideDefense = 10;
        $this->freethrust = 6;
        $this->offensivebonus = 3;
        $this->jinkinglimit = 4;
        $this->turncost = 0.33;
        $this->turndelaycost = 0.25;
		
		$this->hangarRequired = 'superheavy'; //for fleet check
        $this->iniativebonus = 70;
		$this->notes = "Bonus to attaching to enemy ships.";	
    	$this->superheavy = true;
        $this->maxFlightSize = 3;//this is a superheavy fighter originally intended as single unit, limit flight size to 3
	
		$this->populate();
	
	}

    public function populate(){        

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
		
		for ($i = 0; $i < $toAdd; $i++) {
			$armour = array(1, 0, 1, 1);
			$fighter = new Fighter("DalithornCustomsVessel", $armour, 24, $this->id);
			$fighter->displayName = "Krethim";
			$fighter->imagePath = "img/ships/Nexus/Dalithorn_Cutter2.png.png";
			$fighter->iconPath = "img/ships/Nexus/Dalithorn_Cutter_Large2.png";

			$shattergun = new NexusShatterGunFtr(0, 360, 1);
			$fighter->addFrontSystem($shattergun);

			$fighter->addFrontSystem(new Marines(0, 360, 0, false)); //startarc, endarc, damagebonus, elite.

			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			$this->addSystem($fighter);
		}
    }

}

?>
