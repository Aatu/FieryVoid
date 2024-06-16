<?php
class breachingpoddescari extends FighterFlight
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 38 *6;
		$this->faction = "Descari Committees";
		$this->phpclass = "breachingpoddescari";
		$this->shipClass = "Noscun Breaching Pods";
		$this->imagePath = "img/ships/DescariNoscho.png";
		
		$this->isd = 2218;

		$this->forwardDefense = 9;
		$this->sideDefense = 9;
		$this->freethrust = 9;
		$this->offensivebonus = 0;
		$this->jinkinglimit = 0;
		$this->turncost = 0.33;

        $this->maxFlightSize = 6;//this is an unusual type of 'fighter', limit flight size.      
		$this->hangarRequired = 'assault shuttles'; //for fleet check
		$this->unitSize = 1; 

		$this->iniativebonus = 9 *5;
		$this->populate();
	}

	public function populate()
	{
		$current = count($this->systems);
		$new = $this->flightSize;
		$toAdd = $new - $current;

		for ($i = 0; $i < $toAdd; $i++) {

			$armour = array(2, 2, 2, 2);
			$fighter = new Fighter("breachingpoddescari", $armour, 15, $this->id);
			$fighter->displayName = "Noscun";
			$fighter->imagePath = "img/ships/DescariNoscho.png";
			$fighter->iconPath = "img/ships/DescariNoscho_large.png";


			$fighter->addFrontSystem(new Marines(330, 30, 0, false)); //startarc, endarc, damagebonus, elite.
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack		
			
			$this->addSystem($fighter);

		}
	}
}


?>