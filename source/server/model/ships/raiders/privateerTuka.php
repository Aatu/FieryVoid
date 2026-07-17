<?php
class PrivateerTuka extends FighterFlight
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 50 *6;
		$this->faction = "Raiders";
		$this->phpclass = "PrivateerTuka";
		$this->shipClass = "Torata Privateer Tuka Medium Fighters";
		$this->imagePath = "img/ships/TorataPrivateerTuka.png";
		
		$this->occurence = "rare";
		$this->variantOf = "Raider Tuka Medium Fighters";
		
		$this->isd = 2230;
		$this->notes = "Used only by Torata privateers.";
		$this->notes .= "<br> ";
		$this->notes .= "<br>More detailed deployment restrictions are in the Faction List document.";
		$this->notes .= "<br> ";
		
		$this->unofficial = true;
		
		$this->forwardDefense = 7;
		$this->sideDefense = 7;
		$this->freethrust = 8;
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

			$armour = array(2, 2, 2, 2);
			$fighter = new Fighter("PrivateerTuka", $armour, 9, $this->id);
			$fighter->displayName = "Tuka";
			$fighter->imagePath = "img/ships/TorataPrivateerTuka.png";
			$fighter->iconPath = "img/ships/TorataPrivateerTuka_large.png";


			$frontGun = new LightParticleAccelerator(330, 30);
			$fighter->addFrontSystem($frontGun);
			
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			
			$this->addSystem($fighter);

		}
	}
}


?>