<?php
class HyachDartha extends FighterFlight
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 70 *6;
		$this->faction = "Hyach Gerontocracy";
		$this->phpclass = "HyachDartha";
		$this->shipClass = "Dartha Medium Fighters";
		$this->imagePath = "img/ships/CorillaniTilliniCPN.png";
				
		$this->isd = 2222;

		$this->forwardDefense = 7;
		$this->sideDefense = 7;
		$this->freethrust = 10;
		$this->offensivebonus = 7;
		$this->jinkinglimit = 8;
		$this->turncost = 0.33;
        $this->gravitic = true;

		$this->iniativebonus = 18 *5;
		$this->populate();
	}

	public function populate()
	{
		$current = count($this->systems);
		$new = $this->flightSize;
		$toAdd = $new - $current;

		for ($i = 0; $i < $toAdd; $i++) {

			$armour = array(2, 2, 1, 1);
			$fighter = new Fighter("HyachDartha", $armour, 10, $this->id);
			$fighter->displayName = "Dartha";
			$fighter->imagePath = "img/ships/CorillaniTilliniCPN.png";
			$fighter->iconPath = "img/ships/CorillaniTilliniCPN_large.png";

			if ($i == 1) {
				$frontGun2 = new FtrInterdictor(330, 30);
				$fighter->addFrontSystem($frontGun2);
			} else {
				$frontGun = new LtBlastLaser(330, 30);
				$fighter->addFrontSystem($frontGun);
			}
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			$this->addSystem($fighter);

		}
	}
}


?>