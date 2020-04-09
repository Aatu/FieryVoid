<?php
class TorataColotnarBase2220 extends SmallStarBaseFourSections
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 2000;
		$this->base = true;
		$this->smallBase = true;
		$this->faction = "Torata";
		$this->phpclass = "TorataColotnarBase2220";
		$this->shipClass = "Colotnar Defense Base (2220)";
		$this->variantOf = "Colotnar Defense Base";
		$this->imagePath = "img/ships/TorataColotnar.png";
		$this->canvasSize = 200;
		$this->fighters = array("heavy"=>48);
		$this->isd = 2220;

		$this->shipSizeClass = 3;
		$this->Enormous = true;
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->forwardDefense = 19;
		$this->sideDefense = 19;



		$this->addPrimarySystem(new Reactor(5, 20, 0, 0));
		$this->addPrimarySystem(new CnC(6, 32, 0, 0)); //originally 2 systems with structure 16, armor 5 each
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
		$this->addFrontSystem(new SubReactor(4, 25, 0, 0));
		$this->addFrontSystem(new ParticleAccelerator(4, 8, 8, 270, 90));
		$this->addFrontSystem(new ParticleAccelerator(4, 8, 8, 270, 90));
		$this->addFrontSystem(new ParticleAccelerator(4, 8, 8, 270, 90));
		$this->addFrontSystem(new ParticleAccelerator(4, 8, 8, 270, 90));
		$this->addFrontSystem(new PlasmaAccelerator(4, 10, 5, 270, 90));
		$this->addFrontSystem(new PlasmaAccelerator(4, 10, 5, 270, 90));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 270, 90));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 270, 90));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 270, 90));

		$this->addAftSystem(new Hangar(4, 12));
		$this->addAftSystem(new CargoBay(4, 30));
		$this->addAftSystem(new SubReactor(4, 25, 0, 0));
		$this->addAftSystem(new ParticleAccelerator(4, 8, 8, 90, 270));
		$this->addAftSystem(new ParticleAccelerator(4, 8, 8, 90, 270));
		$this->addAftSystem(new ParticleAccelerator(4, 8, 8, 90, 270));
		$this->addAftSystem(new ParticleAccelerator(4, 8, 8, 90, 270));
		$this->addAftSystem(new PlasmaAccelerator(4, 10, 5, 90, 270));
		$this->addAftSystem(new PlasmaAccelerator(4, 10, 5, 90, 270));
		$this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 90, 270));
		$this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 90, 270));
		$this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 90, 270));

		$this->addLeftSystem(new Hangar(4, 12));
		$this->addLeftSystem(new CargoBay(4, 30));
		$this->addLeftSystem(new SubReactor(4, 25, 0, 0));
		$this->addLeftSystem(new ParticleAccelerator(4, 8, 8, 180, 0));
		$this->addLeftSystem(new ParticleAccelerator(4, 8, 8, 180, 0));
		$this->addLeftSystem(new ParticleAccelerator(4, 8, 8, 180, 0));
		$this->addLeftSystem(new ParticleAccelerator(4, 8, 8, 180, 0));
		$this->addLeftSystem(new PlasmaAccelerator(4, 10, 5, 180, 0));
		$this->addLeftSystem(new PlasmaAccelerator(4, 10, 5, 180, 0));
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 0));
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 0));
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 0));
				
		$this->addRightSystem(new Hangar(4, 12));
		$this->addRightSystem(new CargoBay(4, 30));
		$this->addRightSystem(new SubReactor(4, 25, 0, 0));
		$this->addRightSystem(new ParticleAccelerator(4, 8, 8, 0, 180));
		$this->addRightSystem(new ParticleAccelerator(4, 8, 8, 0, 180));
		$this->addRightSystem(new ParticleAccelerator(4, 8, 8, 0, 180));
		$this->addRightSystem(new ParticleAccelerator(4, 8, 8, 0, 180));
		$this->addRightSystem(new PlasmaAccelerator(4, 10, 5, 0, 180));
		$this->addRightSystem(new PlasmaAccelerator(4, 10, 5, 0, 180));
		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
				
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
				1 => "Light Particle Beam",
				3 => "Plasma Accelerator",
				7 => "Particle Accelerator",
				8 => "Hangar",
				10 => "Cargo Bay",
				11 => "Reactor",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				1 => "Light Particle Beam",
				3 => "Plasma Accelerator",
				7 => "Particle Accelerator",
				8 => "Hangar",
				10 => "Cargo Bay",
				11 => "Reactor",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				1 => "Light Particle Beam",
				3 => "Plasma Accelerator",
				7 => "Particle Accelerator",
				8 => "Hangar",
				10 => "Cargo Bay",
				11 => "Reactor",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				1 => "Light Particle Beam",
				3 => "Plasma Accelerator",
				7 => "Particle Accelerator",
				8 => "Hangar",
				10 => "Cargo Bay",
				11 => "Reactor",
				18 => "Structure",
				20 => "Primary",
			),
		);
		
		
	}
}


?>