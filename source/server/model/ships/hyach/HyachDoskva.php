<?php
class HyachDoskva extends FighterFlight
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 75 *6;
		$this->faction = "Hyach Gerontocracy";
		$this->phpclass = "HyachDoskva";
		$this->shipClass = "Doskva Stealth Fighters";
			$this->variantOf = "Dartha Medium Fighters";
			$this->occurence = "rare";
		$this->imagePath = "img/ships/HyachDartha.png";
				
		$this->isd = 2254;

		$this->forwardDefense = 6;
		$this->sideDefense = 6;
		$this->freethrust = 9;
		$this->offensivebonus = 6;
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
			$fighter = new Fighter("HyachDoskva", $armour, 10, $this->id);
			$fighter->displayName = "Doskva";
			$fighter->imagePath = "img/ships/HyachDartha.png";
			$fighter->iconPath = "img/ships/HyachDartha_Large.png";

			$frontGun = new LtBlastLaser(330, 30);
			$fighter->addFrontSystem($frontGun);
			
			$fighter->addAftSystem(new Stealth(1,1,0));
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			$this->addSystem($fighter);

		}
	}
}


?>