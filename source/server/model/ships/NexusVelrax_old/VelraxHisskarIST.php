<?php
class VelraxHisskarIST extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 35*6;
        $this->faction = "Nexus Support Units";
        $this->phpclass = "VelraxHisskarIST";
        $this->shipClass = "Velrax Hisskar Intra-System Transport";
        $this->imagePath = "img/ships/Nexus/velraxHisskar.png";
		$this->unofficial = true;
	    $this->isd = 1999;
        $this->canvasSize = 120;

//        $this->notes = 'Needs updated hangars to handle.';
//        $this->notes .= '<br>Each counts as 2 heavy fighters.';

        $this->forwardDefense = 9;
        $this->sideDefense = 11;
        $this->freethrust = 4;
        $this->offensivebonus = 2;
        $this->jinkinglimit = 0;
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
		$this->unitSize = 1/6;
		
		$this->hangarRequired = "superheavy"; //Velrax Hisskar are housed in regular hangars, as heavy fighters
//		$this->unitSize = 0.5; //one craft requires 2 hangar slots
        $this->iniativebonus = 50;
    	$this->superheavy = true;
        $this->maxFlightSize = 3;//this is a superheavy fighter originally intended as single unit, limit flight size to 3
	
		$this->populate();

	}

    public function populate(){        

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
		
		for ($i = 0; $i < $toAdd; $i++) {
			$armour = array(2, 2, 1, 1);
			$fighter = new Fighter("VelraxHisskarIST", $armour, 34, $this->id);
			$fighter->displayName = "Hisskar";
			$fighter->imagePath = "img/ships/Nexus/velraxHisskar.png.png";
			$fighter->iconPath = "img/ships/Nexus/velraxHisskar_large.png";

	        $light = new NexusLightIonGun(0, 360, 0, 1); //$startArc, $endArc, $nrOfShots
	        $fighter->addFrontSystem($light);
        
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			$this->addSystem($fighter);
		}
    }

}

?>
