<?php
class baradaBaroghMediumFighters extends FighterFlight
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 38 *6;
		$this->faction = "Barada Imperium";
		$this->phpclass = "baradaBaroghMediumFighters";
		$this->shipClass = "Barogh Medium Fighters";
		$this->imagePath = "img/ships/baradaBaroghMediumFighter.png";
		
		$this->isd = 2205;

		$this->forwardDefense = 6;
		$this->sideDefense = 6;
		$this->freethrust = 11;
		$this->offensivebonus = 4;
		$this->jinkinglimit = 8;
		$this->turncost = 0.33;
		$this->unofficial = true;

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
			$fighter = new Fighter("baradaBaroghMediumFighters", $armour, 9, $this->id);
			$fighter->displayName = "Barogh";
			$fighter->imagePath = "img/ships/baradaBaroghMediumFighter.png";
			$fighter->iconPath = "img/ships/baradaBaroghMediumFighter_large.png";

			$gun = new LightParticleBeam(330, 30, 2,2);
			$gun->displayName = "Light Particle Gun";
			$fighter->addFrontSystem($gun);
            
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			
			$this->addSystem($fighter);

		}
	}
}


?>