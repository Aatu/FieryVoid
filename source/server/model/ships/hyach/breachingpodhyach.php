<?php
class breachingpodhyach extends FighterFlight
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 40 *6;
		$this->faction = "Hyach Gerontocracy";
		$this->phpclass = "breachingpodhyach";
		$this->shipClass = "Achilat Breaching Pods";
		$this->imagePath = "img/ships/HyachLaricha.png";
				
		$this->isd = 2244;

		$this->forwardDefense = 8;
		$this->sideDefense = 9;
		$this->freethrust = 7;
		$this->offensivebonus = 0;
		$this->jinkinglimit = 0;
        $this->pivotcost = 2; //shuttles have pivot cost higher        
		$this->turncost = 0.33;
        $this->gravitic = true;

        $this->maxFlightSize = 6;//this is an unusual type of 'fighter', limit flight size.      
		$this->hangarRequired = 'assault shuttles'; //for fleet check
		$this->unitSize = 1; 

		$this->iniativebonus = 9*5;
		$this->populate();
	
		$this->enhancementOptionsEnabled[] = 'ELT_MAR'; //To enable Elite Marines enhancement
		$this->enhancementOptionsEnabled[] = 'EXT_MAR'; //To enable extra Marines enhancement
		
	}

	public function populate()
	{
		$current = count($this->systems);
		$new = $this->flightSize;
		$toAdd = $new - $current;

		for ($i = 0; $i < $toAdd; $i++) {

			$armour = array(3, 3, 3, 3);
			$fighter = new Fighter("breachingpodhyach", $armour, 18, $this->id);
			$fighter->displayName = "Achilat";
			$fighter->imagePath = "img/ships/HyachLaricha.png";
			$fighter->iconPath = "img/ships/HyachLaricha_Large.png";

			$fighter->addFrontSystem(new Marines(330, 30, 0, false)); //startarc, endarc, damagebonus, elite.
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			$this->addSystem($fighter);

		}
	}
}


?>