<?php
class TorataColotnarBase extends SmallStarBaseFourSections
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 2250;
		$this->base = true;
		$this->smallBase = true; //"small" as in 4 outer sections
		$this->faction = "Torata";
		$this->phpclass = "TorataColotnarBase";
		$this->shipClass = "Colotnar Defense Base";
		$this->imagePath = "img/ships/TorataColotnar.png";
		$this->canvasSize = 200;
		$this->fighters = array("heavy"=>48);
		$this->isd = 2256;

		$this->shipSizeClass = 3;
		$this->Enormous = true;
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->forwardDefense = 19;
		$this->sideDefense = 19;



		$this->addPrimarySystem(new Reactor(5, 20, 0, 0));
		$this->addPrimarySystem(new ProtectedCnC(6, 32, 0, 0)); //originally 2 systems with structure 16, armor 5 each
		$this->addPrimarySystem(new Scanner(5, 20, 5, 7));
		$this->addPrimarySystem(new Scanner(5, 20, 5, 7));
		$this->addPrimarySystem(new Hangar(5, 4));
		$this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 0, 360));
		$this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 0, 360));
		$this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 0, 360));
		$this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 0, 360));
		$this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 0, 360));
		$this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 0, 360));

		$this->addFrontSystem(new Hangar(4, 12));
		$this->addFrontSystem(new CargoBay(4, 30));
		$this->addFrontSystem(new SubReactorUniversal(4, 25, 0, 0));
		$this->addFrontSystem(new ParticleAccelerator(4, 8, 8, 270, 90));
		$this->addFrontSystem(new ParticleAccelerator(4, 8, 8, 270, 90));
		$this->addFrontSystem(new LaserAccelerator(4, 7, 6, 270, 90));
		$this->addFrontSystem(new LaserAccelerator(4, 7, 6, 270, 90));
		$this->addFrontSystem(new PlasmaAccelerator(4, 10, 5, 270, 90));
		$this->addFrontSystem(new PulseAccelerator(4, 9, 4, 270, 90));
		$this->addFrontSystem(new PentagonArray(4, 8, 5, 270, 90));

		$this->addAftSystem(new Hangar(4, 12));
		$this->addAftSystem(new CargoBay(4, 30));
		$this->addAftSystem(new SubReactorUniversal(4, 25, 0, 0));
		$this->addAftSystem(new ParticleAccelerator(4, 8, 8, 90, 270));
		$this->addAftSystem(new ParticleAccelerator(4, 8, 8, 90, 270));
		$this->addAftSystem(new LaserAccelerator(4, 7, 6, 90, 270));
		$this->addAftSystem(new LaserAccelerator(4, 7, 6, 90, 270));
		$this->addAftSystem(new PlasmaAccelerator(4, 10, 5, 90, 270));
		$this->addAftSystem(new PulseAccelerator(4, 9, 4, 90, 270));
		$this->addAftSystem(new PentagonArray(4, 8, 5, 90, 270));

		$this->addLeftSystem(new Hangar(4, 12));
		$this->addLeftSystem(new CargoBay(4, 30));
		$this->addLeftSystem(new SubReactorUniversal(4, 25, 0, 0));
		$this->addLeftSystem(new ParticleAccelerator(4, 8, 8, 180, 0));
		$this->addLeftSystem(new ParticleAccelerator(4, 8, 8, 180, 0));
		$this->addLeftSystem(new LaserAccelerator(4, 7, 6, 180, 0));
		$this->addLeftSystem(new LaserAccelerator(4, 7, 6, 180, 0));
		$this->addLeftSystem(new PlasmaAccelerator(4, 10, 5, 180, 0));
		$this->addLeftSystem(new PulseAccelerator(4, 9, 4, 180, 0));
		$this->addLeftSystem(new PentagonArray(4, 8, 5, 180, 0));
				
		$this->addRightSystem(new Hangar(4, 12));
		$this->addRightSystem(new CargoBay(4, 30));
		$this->addRightSystem(new SubReactorUniversal(4, 25, 0, 0));
		$this->addRightSystem(new ParticleAccelerator(4, 8, 8, 0, 180));
		$this->addRightSystem(new ParticleAccelerator(4, 8, 8, 0, 180));
		$this->addRightSystem(new LaserAccelerator(4, 7, 6, 0, 180));
		$this->addRightSystem(new LaserAccelerator(4, 7, 6, 0, 180));
		$this->addRightSystem(new PlasmaAccelerator(4, 10, 5, 0, 180));
		$this->addRightSystem(new PulseAccelerator(4, 9, 4, 0, 180));
		$this->addRightSystem(new PentagonArray(4, 8, 5, 0, 180));
				
		$this->addFrontSystem(new Structure( 4, 120));
		$this->addAftSystem(new Structure( 4, 120));
		$this->addLeftSystem(new Structure( 4, 120));
		$this->addRightSystem(new Structure( 4, 120));
		$this->addPrimarySystem(new Structure( 5, 160));		
		
		
		$this->hitChart = array(
			0=> array(
				10 => "Structure",
				12 => "Light Particle Beam",
				14 => "Scanner",
				16 => "Hangar",
				18 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				1 => "Pentagon Array",
				2 => "Plasma Accelerator",
				4 => "Laser Accelerator",
				6 => "Particle Accelerator",
				7 => "Pulse Accelerator",
				8 => "Hangar",
				10 => "Cargo Bay",
				11 => "Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				1 => "Pentagon Array",
				2 => "Plasma Accelerator",
				4 => "Laser Accelerator",
				6 => "Particle Accelerator",
				7 => "Pulse Accelerator",
				8 => "Hangar",
				10 => "Cargo Bay",
				11 => "Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				1 => "Pentagon Array",
				2 => "Plasma Accelerator",
				4 => "Laser Accelerator",
				6 => "Particle Accelerator",
				7 => "Pulse Accelerator",
				8 => "Hangar",
				10 => "Cargo Bay",
				11 => "Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				1 => "Pentagon Array",
				2 => "Plasma Accelerator",
				4 => "Laser Accelerator",
				6 => "Particle Accelerator",
				7 => "Pulse Accelerator",
				8 => "Hangar",
				10 => "Cargo Bay",
				11 => "Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
		);
		
		
	}
}


?>