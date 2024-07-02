<?php
class breachingpodraiders extends FighterFlight{
				
		function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 40*6;
		$this->faction = "Raiders";
		$this->phpclass = "breachingpodraiders";
		$this->shipClass = "Mako Breaching Pods";
		$this->imagePath = "img/ships/doubleV.png";
		$this->isd = 2236;
		
		$this->forwardDefense = 7;
		$this->sideDefense = 8;
		$this->freethrust = 8;
		$this->offensivebonus = 0;
		$this->jinkinglimit = 4;
		$this->turncost = 0.33;	

        $this->maxFlightSize = 6;//this is an unusual type of 'fighter', limit flight size.      
		$this->hangarRequired = 'assault shuttles'; //for fleet check
		$this->unitSize = 1; 

		$this->iniativebonus = 9*10;
		$this->notes = "Cannot attach to enemy ships while jinking.";		
		
        $this->populate();
    
    	$this->enhancementOptionsEnabled[] = 'ELT_MAR'; //To enable Elite Marines enhancement
		$this->enhancementOptionsEnabled[] = 'EXT_MAR'; //To enable extra Marines enhancement
		
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
		$armour = array(1, 1, 1, 1);
		$fighter = new Fighter("breachingpodraiders", $armour, 16, $this->id);
		$fighter->displayName = "Mako";
		$fighter->imagePath = "img/ships/doubleV.png";
		$fighter->iconPath = "img/ships/doubleV_large.png";
			
		$fighter->addFrontSystem(new Marines(330, 30, 0, false)); //startarc, endarc, damagebonus, elite.
			
		$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
		$this->addSystem($fighter);
	}
				}
}

?>