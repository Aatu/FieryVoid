<?php
class TorataTumalOSAT extends OSAT
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 200;
		$this->faction = "Torata";
		$this->phpclass = "TorataTumalOSAT";
		$this->shipClass = "Tumal Orbital Satellite";
		$this->imagePath = "img/ships/TorataTumalOSAT.png";
		$this->canvasSize = 200;
		$this->isd = 2240;

		$this->forwardDefense = 10;
		$this->sideDefense = 10;
		
		$this->turncost = 0;
		$this->turndelaycost = 0;
		$this->accelcost = 0;
		$this->rollcost = 0;
		$this->pivotcost = 0;
		$this->iniativebonus = 12 *5;
		
		$this->addPrimarySystem(new Reactor(4, 8, 0, 0));
		$this->addPrimarySystem(new Scanner(4, 7, 2, 5));
		$this->addPrimarySystem(new Thruster(4, 6, 0, 0, 2));
		$this->addPrimarySystem(new LaserAccelerator(4, 7, 6, 270, 90));
		$this->addPrimarySystem(new LaserAccelerator(4, 7, 6, 270, 90));
		$this->addPrimarySystem(new ParticleAccelerator(4, 8, 8, 180, 0));
		$this->addPrimarySystem(new ParticleAccelerator(4, 8, 8, 0, 180));
		$this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 0, 360));

		//0:primary, 1:front, 2:rear, 3:left, 4:right;

		$this->addPrimarySystem(new Structure(4, 30));
	}
}

?>