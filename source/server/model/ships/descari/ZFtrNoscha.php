<?php
class ZFtrNoscha extends FighterFlight
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 55 *6;
		$this->faction = "Descari";
		$this->phpclass = "ZFtrNoscha";
		$this->shipClass = "Noscha Heavy Fighters";
		$this->imagePath = "img/ships/Descarinoscha.png";
		
		$this->isd = 2249;

		$this->forwardDefense = 7;
		$this->sideDefense = 8;
		$this->freethrust = 10;
		$this->offensivebonus = 4;
		$this->jinkinglimit = 6;
		$this->turncost = 0.33;

		$this->iniativebonus = 16 *5;
		$this->populate();
	}

	public function populate()
	{
		$current = count($this->systems);
		$new = $this->flightSize;
		$toAdd = $new - $current;

		for ($i = 0; $i < $toAdd; $i++) {

			$armour = array(2, 2, 2, 2);
			$fighter = new Fighter("ZFtrNoscha", $armour, 12, $this->id);
			$fighter->displayName = "Noscha";
			$fighter->imagePath = "img/ships/Descarinoscha.png";
			$fighter->iconPath = "img/ships/Descarinoscha_large.png";


			$frontGun = new LightPlasmaBolterFighter(330, 30);
			$fighter->addFrontSystem($frontGun);
			
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			
			$this->addSystem($fighter);

		}
	}
}


?>