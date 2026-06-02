<?php
class BloodSwordYacht extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 0;
        $this->faction = "Escalation Wars Blood Sword Raiders";
        $this->phpclass = "BloodSwordYacht";
        $this->shipClass = "Ghaira's Yacht";
        $this->imagePath = "img/ships/EscalationWars/BloodSwordYacht.png";
//        $this->customFtrName = "Yacht";
//        $this->occurence = "unique"; 
		$this->unofficial = true;
		
        $this->isd = 1943;
        $this->notes = 'Only used by the Lady of Dark Souls.';
		$this->hangarRequired = 'Yacht'; //for fleet check
        
        $this->forwardDefense = 9;
        $this->sideDefense = 12;
        $this->freethrust = 3;
        $this->offensivebonus = 3;
        $this->jinkinglimit = 0;
        $this->turncost = 0.33;

        $this->maxFlightSize = 2;//this is an unusual type of 'fighter', limit flight size.      
        
        $this->iniativebonus = 50;
        $this->populate();       

    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){            
            $armour = array(3, 3, 3, 3);
            $fighter = new Fighter("BloodSwordYacht", $armour, 12, $this->id);
            $fighter->displayName = "Yacht";
            $fighter->imagePath = "img/ships/EscalationWars/BloodSwordYacht.png";
            $fighter->iconPath = "img/ships/EscalationWars/BloodSwordYacht_large.png";

			$gun = new EWLaserBoltFtr(330, 30, 1, 1);
			$gun->displayName = "Ultralight Laser Bolt";
			$fighter->addFrontSystem($gun);
		
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack	
            
            $this->addSystem($fighter);
        }
    }
}
?>
