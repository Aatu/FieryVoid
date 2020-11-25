<?php
class TorataToglatMonitor extends BaseShip{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 800;
		$this->faction = "Torata";
		$this->shipClass = "Toglat System Monitor";
		$this->phpclass = "TorataToglatMonitor";
		$this->imagePath = "img/ships/TorataToglat.png";
		$this->canvasSize = 200;
		$this->shipSizeClass = 3;
		$this->isd = 2242;

		$this->forwardDefense = 18;
		$this->sideDefense = 17;

		$this->turncost = 1.5;
		$this->turndelaycost = 1.5;
		$this->accelcost = 6;
		$this->rollcost = 4;
		$this->pivotcost = 4;
		$this->iniativebonus = -2 *5;

		$this->addPrimarySystem(new Reactor(5, 20, 0, 0));
		$this->addPrimarySystem(new CnC(5, 16, 0, 0));
		$this->addPrimarySystem(new Scanner(5, 18, 6, 8));
		$this->addPrimarySystem(new Engine(5, 20, 0, 8, 3));
		$this->addPrimarySystem(new Hangar(5, 4, 0, 0));
		
		$this->addFrontSystem(new LaserAccelerator(4, 7, 6, 300, 60));
		$this->addFrontSystem(new LaserAccelerator(4, 7, 6, 300, 60));
		$this->addFrontSystem(new LaserAccelerator(4, 7, 6, 210, 120));
		$this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
		$this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
			
		$this->addAftSystem(new LaserAccelerator(4, 7, 6, 60, 300));
		$this->addAftSystem(new Thruster(4, 16, 0, 4, 2));
		$this->addAftSystem(new Thruster(4, 16, 0, 4, 2));

		$this->addLeftSystem(new LaserAccelerator(4, 7, 6, 240, 0));
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 0));
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 0));
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 0));
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 0));
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 0));
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 0));
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 0));
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 0));
		$this->addLeftSystem(new Thruster(4, 15, 0, 6, 3));

		$this->addRightSystem(new LaserAccelerator(4, 7, 6, 0, 120));
		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
		$this->addRightSystem(new Thruster(4, 15, 0, 6, 4));

		//0:primary, 1:front, 2:rear, 3:left, 4:right;
		$this->addFrontSystem(new Structure(5, 56));
		$this->addAftSystem(new Structure(5, 54));
		$this->addLeftSystem(new Structure(5, 56));
		$this->addRightSystem(new Structure(5, 56));
		$this->addPrimarySystem(new Structure(5, 60));

		$this->hitChart = array(
			0=> array(
				12 => "Structure",
				14 => "Scanner",
				16 => "Engine",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				5 => "Thruster",
				9 => "Laser Accelerator",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				6 => "Thruster",
				8 => "Laser Accelerator",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				4 => "Thruster",
				5 => "Laser Accelerator",
				13 => "Light Particle Beam",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				4 => "Thruster",
				5 => "Laser Accelerator",
				13 => "Light Particle Beam",
				18 => "Structure",
				20 => "Primary",
			),
		);
	}
}



?>