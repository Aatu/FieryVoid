<?php
class torataAgnarLightCruiser extends BaseShip{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 500;
		$this->faction = "Torata Regency";
		$this->shipClass = "Agnar Light Cruiser";
		$this->phpclass = "torataAgnarLightCruiser";
		$this->imagePath = "img/ships/torataAgnar.png";
		$this->canvasSize = 200;
		$this->shipSizeClass = 3;
		$this->isd = 2219;
		$this->unofficial = true;


		$this->forwardDefense = 16;
		$this->sideDefense = 15;

		$this->turncost = 0.66;
		$this->turndelaycost = 0.66;
		$this->accelcost = 2;
		$this->rollcost = 2;
		$this->pivotcost = 2;
		$this->iniativebonus = 0 *5;

		$this->addPrimarySystem(new Reactor(5, 17, 0, 0));
		$this->addPrimarySystem(new CnC(5, 12, 0, 0));
		$this->addPrimarySystem(new Scanner(5, 16, 4, 7));
		$this->addPrimarySystem(new Engine(5, 17, 0, 12, 3));
		$this->addPrimarySystem(new Hangar(5, 2, 1));
		
		$this->addFrontSystem(new PlasmaAccelerator(4, 10, 5, 300, 60));
		$this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
		$this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 240, 60));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 300, 120));
		
		$this->addAftSystem(new LightParticleBeamShip(2, 4, 1, 90, 270));
        $this->addAftSystem(new LightParticleBeamShip(2, 4, 1, 90, 270));
		$this->addAftSystem(new Thruster(4, 12, 0, 4, 2));
		$this->addAftSystem(new Thruster(4, 12, 0, 4, 2));
		$this->addAftSystem(new Thruster(4, 12, 0, 4, 2));


		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 240, 60));
		$this->addLeftSystem(new ParticleAccelerator(3, 8, 8, 270, 60));
		$this->addLeftSystem(new Thruster(3, 13, 0, 5, 3));



		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 300, 120));
		$this->addRightSystem(new ParticleAccelerator(3, 8, 8, 300, 90));
		$this->addRightSystem(new Thruster(3, 13, 0, 5, 4));


		//0:primary, 1:front, 2:rear, 3:left, 4:right;
		$this->addFrontSystem(new Structure(4, 40));
		$this->addAftSystem(new Structure(4, 36));
		$this->addLeftSystem(new Structure(5, 50));
		$this->addRightSystem(new Structure(4, 48));
		$this->addPrimarySystem(new Structure(5, 40));

		$this->hitChart = array(
			0=> array(
				10 => "Structure",
				14 => "Scanner",
				16 => "Engine",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				6 => "Thruster",
				8 => "Plasma Accelerator",
				10 => "Light Particle Beam",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				6 => "Thruster",
				8 => "Light Particle Beam",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				4 => "Thruster",
				6 => "Particle Accelerator",
				9 => "Light Particle Beam",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				4 => "Thruster",
				6 => "Particle Accelerator",
				9 => "Light Particle Beam",
				18 => "Structure",
				20 => "Primary",
			),
		);
	}
}



?>
