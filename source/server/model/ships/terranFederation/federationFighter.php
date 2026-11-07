<?php
class federationFighter extends FighterFlight
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 41 *6;
		$this->faction = "Terran Federation";
		$this->phpclass = "federationFighter";
		$this->shipClass = "Medium Fighters";
		$this->imagePath = "img/ships/federationFighter.png";
		
		$this->forwardDefense = 7;
		$this->sideDefense = 9;
		$this->freethrust = 11;
		$this->offensivebonus = 4;
		$this->jinkinglimit = 8;
		$this->turncost = 0.33;

		$this->iniativebonus = 18 *5;
		$this->populate();
	}

	public function populate()
	{
		$current = count($this->systems);
		$new = $this->flightSize;
		$toAdd = $new - $current;

		for ($i = 0; $i < $toAdd; $i++) {

			$armour = array(3, 1, 2, 2);
			$fighter = new Fighter("federationFighter", $armour, 10, $this->id);
			$fighter->displayName = "Fighter";
			$fighter->imagePath = "img/ships/federationFighter.png";
			$fighter->iconPath = "img/ships/federationFighter_large.png";

			$frontGun = new RogolonLtPlasmaGun(330, 30);
			$fighter->addFrontSystem($frontGun);
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			$this->addSystem($fighter);

		}
	}
}


?>