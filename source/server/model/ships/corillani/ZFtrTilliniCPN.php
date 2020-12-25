<?php
class ZFtrTilliniCPN extends FighterFlight
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 52 *6;
		$this->faction = "Corillani";
		$this->phpclass = "ZFtrTilliniCPN";
		$this->shipClass = "Tillini Medium Fighters (CPN)";
		$this->imagePath = "img/ships/CorillaniTilliniCPN.png";
		$this->notes = 'The Corillani Peoples Navy (CPN) version of the Tillini fighter';
				
		$this->isd = 2230;

		$this->forwardDefense = 7;
		$this->sideDefense = 8;
		$this->freethrust = 9;
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

			$armour = array(3, 2, 3, 3);
			$fighter = new Fighter("ZFtrTilliniCPN", $armour, 10, $this->id);
			$fighter->displayName = "Tillini";
			$fighter->imagePath = "img/ships/CorillaniTilliniCPN.png";
			$fighter->iconPath = "img/ships/CorillaniTilliniCPN_large.png";


			$frontGun = new PairedParticleGun(330, 30, 5);
			$fighter->addFrontSystem($frontGun);
			
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			
			$this->addSystem($fighter);

		}
	}
}


?>