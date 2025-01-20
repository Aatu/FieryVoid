<?php
class TrekNausicaanBP extends FighterFlight
{
	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 34 *6;
		$this->faction = "ZTrek Playtest Other Factions";
		$this->phpclass = "TrekNausicaanBP";
		$this->shipClass = "Nausicaan Breaching Pods"; //generic breaching pods, not (yet) very modified for in-universe use
		$this->imagePath = "img/ships/StarTrek/NausicaanShefalitayal.png";
		
		$this->isd = 2160;

		$this->forwardDefense = 9;
		$this->sideDefense = 9;
		$this->freethrust = 7;
		$this->offensivebonus = 0;
		$this->jinkinglimit = 0;
		$this->turncost = 0.33;

        $this->maxFlightSize = 3;//this is an unusual type of 'fighter', limit flight size.      
		$this->hangarRequired = 'Shuttlecraft'; //for fleet check
        $this->customFtrName = "Nausicaan assault craft"; //requires hangar space on Nausicaan ships - and requires it to be dedicated to assault craft
		$this->unitSize = 1; 

		$this->iniativebonus = 9 *5;
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

			$armour = array(2, 2, 2, 2);
			$fighter = new Fighter("TrekNausicaanBP", $armour, 15, $this->id);
			$fighter->displayName = "NausicaanBP";
			$fighter->imagePath = "img/ships/StarTrek/NausicaanShefalitayal.png";
			$fighter->iconPath = "img/ships/StarTrek/NausicaanShefalitayal_Large.png";


			$fighter->addFrontSystem(new Marines(0, 360, 0, false)); //startarc, endarc, damagebonus, elite.
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack		
			
			$this->addSystem($fighter);

		}
	}
}


?>