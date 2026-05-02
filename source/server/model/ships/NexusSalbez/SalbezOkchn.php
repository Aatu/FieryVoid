<?php
class SalbezOkchn extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 600;
        $this->faction = "Nexus Sal-bez Coalition";
        $this->phpclass = "SalbezOkchn";
        $this->shipClass = "Ok-chn Patrol Ship";
        $this->customFtrName = "Ok-chn";
        $this->imagePath = "img/ships/Nexus/salbez_okchn.png";
		$this->unofficial = true;
	    $this->isd = 2136;
//        $this->canvasSize = 150;

        $this->notes = 'Needs updated hangars to handle.';
        $this->notes .= '<br>Each counts as 2 fighters.';

        $this->forwardDefense = 10;
        $this->sideDefense = 10;
        $this->freethrust = 9;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 4;
        $this->turncost = 0.33;
        $this->turndelaycost = 0.25;
		
		$this->hangarRequired = "heavy"; //Sal-bez Ok-chn are housed in regular hangars, as heavy fighters
		$this->unitSize = 0.5; //one craft requires 2 hangar slots
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
			$armour = array(3, 3, 3, 3);
			$fighter = new Fighter("SalbezOkchn", $armour, 32, $this->id);
			$fighter->displayName = "Ok-chn";
			$fighter->imagePath = "img/ships/Nexus/salbez_okchn.png";
			$fighter->iconPath = "img/ships/Nexus/salbez_okchn_large.png";

	        $light = new HvyParticleGunFtr(300, 60, 1); //$startArc, $endArc, $nrOfShots
	        $fighter->addFrontSystem($light);

			$plasma = new LtPlasmaCannonFtr(330, 30, 1); //$startArc, $endArc, $nrOfShots
			$fighter->addFrontSystem($plasma);

			$aft = new LightParticleBeam(150, 210, 2,2);
			$aft->displayName = "Light Particle Gun";
			$fighter->addAftSystem($aft);
        
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			$this->addSystem($fighter);
		}
    }

}

?>
