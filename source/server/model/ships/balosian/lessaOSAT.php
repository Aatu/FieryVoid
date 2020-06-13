<?php
class LessaOSAT extends OSAT
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 150;
		$this->faction = "Balosian";
		$this->phpclass = "LessaOSAT";
		$this->shipClass = "Lessa Orbital Satellite";
		$this->imagePath = "img/ships/Lessa.png";
		$this->canvasSize = 200;
		$this->isd = 2237;

		$this->forwardDefense = 10;
		$this->sideDefense = 10;
		
		$this->turncost = 0;
		$this->turndelaycost = 0;
		$this->accelcost = 0;
		$this->rollcost = 0;
		$this->pivotcost = 0;
		$this->iniativebonus = 12 *5;
		
		$this->addPrimarySystem(new Reactor(4, 6, 0, 0));
		$this->addPrimarySystem(new Scanner(4, 6, 3, 5));
		$this->addPrimarySystem(new Thruster(4, 6, 0, 0, 2));
		$this->addPrimarySystem(new IonCannon(4, 6, 4, 270, 90));
		$this->addPrimarySystem(new IonCannon(4, 6, 4, 270, 90));
		$this->addPrimarySystem(new StdParticleBeam(3, 4, 1, 240, 120));
		$this->addPrimarySystem(new StdParticleBeam(3, 4, 1, 240, 120));

		$this->addPrimarySystem(new Structure(4, 36));
	}
}

?>