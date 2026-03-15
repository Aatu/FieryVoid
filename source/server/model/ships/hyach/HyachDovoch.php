<?php
class HyachDovoch extends FighterFlight
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 70 *6;
		$this->faction = "Hyach Gerontocracy";
		$this->phpclass = "HyachDovoch";
		$this->shipClass = "Dartha Medium Fighters (w/Dovoch)";
			$this->variantOf = "Dartha Medium Fighters";
			$this->occurence = "common";		
		$this->imagePath = "img/ships/HyachDartha.png";
				
		$this->isd = 2222;

		$this->forwardDefense = 7;
		$this->sideDefense = 7;
		$this->freethrust = 10;
		$this->offensivebonus = 7;
		$this->jinkinglimit = 8;
		$this->turncost = 0.33;
        $this->gravitic = true;
		$this->notes = 'Contains a Dovoch fighter with Interdictor';       

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
			$fighter->imagePath = "img/ships/HyachDartha.png";
			$fighter->iconPath = "img/ships/HyachDartha_Large.png";

			if ($i == 1) {
				$frontGun2 = new FtrInterdictor(330, 30);
				$fighter->addFrontSystem($frontGun2);
//			$fighter->displayName = "Dovoch";	//FV appears to take one sample fighter name and apply to all, so this didn't work unfortunately.			
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