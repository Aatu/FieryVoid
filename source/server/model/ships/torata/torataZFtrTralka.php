<?php
class TorataZFtrTralka extends FighterFlight
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 55 *6;
		$this->faction = "Torata";
		$this->phpclass = "TorataZFtrTralka";
		$this->shipClass = "Tralka Medium Fighters";
		$this->imagePath = "img/ships/TorataTralka.png";
		$this->variantOf = "Tuka Medium Fighters";
		$this->occurence = "rare";
		
		$this->isd = 2245;

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
			$fighter = new Fighter("TorataZFtrTralka", $armour, 9, $this->id);
			$fighter->displayName = "Tralka";
			$fighter->imagePath = "img/ships/TorataTralka.png";
			$fighter->iconPath = "img/ships/TorataTralka_large.png";


			$frontGun = new LightPlasmaAccelerator(330, 30);
			$fighter->addFrontSystem($frontGun);
			
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			
			$this->addSystem($fighter);

		}
	}
}


?>