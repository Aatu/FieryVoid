<?php
class raiderTuka extends FighterFlight
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 42 *6;
		$this->faction = "Raiders";
		$this->phpclass = "raiderTuka";
		$this->shipClass = "Raider Tuka Medium Fighters";
		$this->imagePath = "img/ships/RaiderTuka.png";
		
		$this->isd = 2228;
		$this->notes = "Generic raider unit.";
		$this->notes .= "<br> ";	

		$this->forwardDefense = 7;
		$this->sideDefense = 7;
		$this->freethrust = 11;
		$this->offensivebonus = 6;
		$this->jinkinglimit = 8;
		$this->turncost = 0.33;
		$this->unofficial = true;
		$this->iniativebonus = 18 *5;
		$this->populate();
	}

	public function populate()
	{
		$current = count($this->systems);
		$new = $this->flightSize;
		$toAdd = $new - $current;

		for ($i = 0; $i < $toAdd; $i++) {

			$armour = array(2, 2, 2, 2);
			$fighter = new Fighter("raiderTuka", $armour, 9, $this->id);
			$fighter->displayName = "Tuka";
			$fighter->imagePath = "img/ships/RaiderTuka.png";
			$fighter->iconPath = "img/ships/RaiderTuka_large.png";


			$fighter->addFrontSystem(new PairedParticleGun(330, 30, 3));
			
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			
			$this->addSystem($fighter);

		}
	}
}


?>