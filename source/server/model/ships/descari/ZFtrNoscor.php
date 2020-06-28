<?php
class ZFtrNoscor extends FighterFlight
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 38 *6;
		$this->faction = "Descari";
		$this->phpclass = "ZFtrNoscor";
		$this->shipClass = "Noscor Medium Fighters";
		$this->imagePath = "img/ships/DescariNoscor.png";
		
		$this->isd = 2217;

		$this->forwardDefense = 7;
		$this->sideDefense = 9;
		$this->freethrust = 9;
		$this->offensivebonus = 3;
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

			$armour = array(1, 1, 2, 2);
			$fighter = new Fighter("ZFtrNoscor", $armour, 10, $this->id);
			$fighter->displayName = "Noscor";
			$fighter->imagePath = "img/ships/DescariNoscor.png";
			$fighter->iconPath = "img/ships/DescariNoscor_large.png";


			$frontGun = new RogolonLtPlasmaGun(330, 30);
			$fighter->addFrontSystem($frontGun);
			
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			
			$this->addSystem($fighter);

		}
	}
}


?>