<?php
class ZFtrNoscho extends FighterFlight
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 26 *6;
		$this->faction = "Descari Committees";
		$this->phpclass = "ZFtrNoscho";
		$this->shipClass = "Noscho Assault Shuttles";
		$this->imagePath = "img/ships/DescariNoscho.png";
		
		$this->isd = 2218;

		$this->forwardDefense = 9;
		$this->sideDefense = 9;
		$this->freethrust = 6;
		$this->offensivebonus = 2;
		$this->jinkinglimit = 0;
		$this->turncost = 0.33;

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
			$fighter = new Fighter("ZFtrNoscho", $armour, 15, $this->id);
			$fighter->displayName = "Noscho";
			$fighter->imagePath = "img/ships/DescariNoscho.png";
			$fighter->iconPath = "img/ships/DescariNoscho_large.png";


			$frontGun = new RogolonLtPlasmaGun(330, 30, 1);//one gun
			$fighter->addFrontSystem($frontGun);
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			
			$this->addSystem($fighter);

		}
	}
}


?>