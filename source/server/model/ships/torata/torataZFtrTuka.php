<?php
class TorataZFtrTuka extends FighterFlight
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 50 *6;
		$this->faction = "Torata";
		$this->phpclass = "TorataZFtrTuka";
		$this->shipClass = "Tuka Medium Fighters";
		$this->imagePath = "img/ships/TorataTuka.png";
		
		$this->isd = 2216;

		$this->forwardDefense = 7;
		$this->sideDefense = 7;
		$this->freethrust = 11;
		$this->offensivebonus = 6;
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

			$armour = array(2, 2, 2, 2);
			$fighter = new Fighter("TorataZFtrTuka", $armour, 9, $this->id);
			$fighter->displayName = "Tuka";
			$fighter->imagePath = "img/ships/TorataTuka.png";
			$fighter->iconPath = "img/ships/TorataTuka_large.png";


			$frontGun = new LightParticleAccelerator(330, 30);
			$fighter->addFrontSystem($frontGun);
			
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $this->getRammingFactor(), 0)); //ramming attack
			
			
			$this->addSystem($fighter);

		}
	}
}


?>