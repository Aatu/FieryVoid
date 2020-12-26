<?php
class ZFtrTilliniDoC extends FighterFlight
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 52 *6;
		$this->faction = "Corillani";
		$this->phpclass = "ZFtrTilliniDoC";
		$this->shipClass = "Tillini Medium Fighters (DoC)";
		$this->imagePath = "img/ships/CorillaniTilliniDoC.png";
		$this->notes = 'The Defenders of Corillan (DoC) version of the Tillini fighter';
				
		$this->isd = 2230;

		$this->forwardDefense = 7;
		$this->sideDefense = 8;
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

			$armour = array(2, 1, 2, 2);
			$fighter = new Fighter("ZFtrTilliniDoC", $armour, 10, $this->id);
			$fighter->displayName = "Tillini";
			$fighter->imagePath = "img/ships/CorillaniTilliniDoC.png";
			$fighter->iconPath = "img/ships/CorillaniTilliniDoC_large.png";


			$frontGun = new PairedParticleGun(330, 30, 5);
			$fighter->addFrontSystem($frontGun);
			
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			
			$this->addSystem($fighter);

		}
	}
}


?>