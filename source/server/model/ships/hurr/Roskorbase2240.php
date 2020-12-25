<?php
class Roskorbase2240 extends SmallStarBaseFourSections
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 2000;
		$this->base = true;
		$this->smallBase = true; //"small" as in 4 outer sections
		$this->faction = "Hurr";
		$this->phpclass = "Roskorbase2240";
		$this->shipClass = "Roskor Command Post (2240)";
		$this->imagePath = "img/ships/HurrRoskor.png";
		$this->canvasSize = 200;
		$this->fighters = array("normal"=>48);
		$this->isd = 2240;

		$this->shipSizeClass = 3;
		$this->Enormous = true;
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->forwardDefense = 19;
		$this->sideDefense = 19;



		$this->addPrimarySystem(new Reactor(5, 20, 0, 0));
		$this->addPrimarySystem(new ProtectedCnC(6, 36, 0, 0)); //3x 5/12
		$this->addPrimarySystem(new Scanner(5, 16, 4, 5));
		$this->addPrimarySystem(new Scanner(5, 16, 4, 5));
		$this->addPrimarySystem(new Hangar(5, 2));
		$this->addPrimarySystem(new ReloadRack(5, 9));		
		$this->addPrimarySystem(new HeavyPlasma(5, 8, 5, 0, 360));
		$this->addPrimarySystem(new HeavyPlasma(5, 8, 5, 0, 360));
		$this->addPrimarySystem(new HeavyPlasma(5, 8, 5, 0, 360));
		$this->addPrimarySystem(new HeavyPlasma(5, 8, 5, 0, 360));

		$this->addFrontSystem(new Hangar(4, 12));
		$this->addFrontSystem(new CargoBay(4, 24));
		$this->addFrontSystem(new SubReactorUniversal(4, 20, 0, 0));
		$this->addFrontSystem(new SMissileRack(4, 6, 0, 270, 90, true));
		$this->addFrontSystem(new SMissileRack(4, 6, 0, 270, 90, true));
		$this->addFrontSystem(new SMissileRack(4, 6, 0, 270, 90, true));
		$this->addFrontSystem(new SMissileRack(4, 6, 0, 270, 90, true));
		$this->addFrontSystem(new StdParticleBeam(4, 4, 1, 270, 90));
		$this->addFrontSystem(new StdParticleBeam(4, 4, 1, 270, 90));
		$this->addFrontSystem(new StdParticleBeam(4, 4, 1, 270, 90));

		$this->addAftSystem(new Hangar(4, 12));
		$this->addAftSystem(new CargoBay(4, 24));
		$this->addAftSystem(new SubReactorUniversal(4, 20, 0, 0));
		$this->addAftSystem(new SMissileRack(4, 6, 0, 90, 270, true));
		$this->addAftSystem(new SMissileRack(4, 6, 0, 90, 270, true));
		$this->addAftSystem(new SMissileRack(4, 6, 0, 90, 270, true));
		$this->addAftSystem(new SMissileRack(4, 6, 0, 90, 270, true));
		$this->addAftSystem(new StdParticleBeam(4, 4, 1, 90, 270));
		$this->addAftSystem(new StdParticleBeam(4, 4, 1, 90, 270));
		$this->addAftSystem(new StdParticleBeam(4, 4, 1, 90, 270));

		$this->addLeftSystem(new Hangar(4, 12));
		$this->addLeftSystem(new CargoBay(4, 24));
		$this->addLeftSystem(new SubReactorUniversal(4, 20, 0, 0));
		$this->addLeftSystem(new SMissileRack(4, 6, 0, 180, 360, true));
		$this->addLeftSystem(new SMissileRack(4, 6, 0, 180, 360, true));
		$this->addLeftSystem(new SMissileRack(4, 6, 0, 180, 360, true));
		$this->addLeftSystem(new SMissileRack(4, 6, 0, 180, 360, true));
		$this->addLeftSystem(new StdParticleBeam(4, 4, 1, 180, 360));
		$this->addLeftSystem(new StdParticleBeam(4, 4, 1, 180, 360));
		$this->addLeftSystem(new StdParticleBeam(4, 4, 1, 180, 360));
				
		$this->addRightSystem(new Hangar(4, 12));
		$this->addRightSystem(new CargoBay(4, 24));
		$this->addRightSystem(new SubReactorUniversal(4, 20, 0, 0));
		$this->addRightSystem(new SMissileRack(4, 6, 0, 0, 180, true));
		$this->addRightSystem(new SMissileRack(4, 6, 0, 0, 180, true));
		$this->addRightSystem(new SMissileRack(4, 6, 0, 0, 180, true));
		$this->addRightSystem(new SMissileRack(4, 6, 0, 0, 180, true));
		$this->addRightSystem(new StdParticleBeam(4, 4, 1, 0, 180));
		$this->addRightSystem(new StdParticleBeam(4, 4, 1, 0, 180));
		$this->addRightSystem(new StdParticleBeam(4, 4, 1, 0, 180));
				
		$this->addFrontSystem(new Structure( 4, 100));
		$this->addAftSystem(new Structure( 4, 100));
		$this->addLeftSystem(new Structure( 4, 100));
		$this->addRightSystem(new Structure( 4, 100));
		$this->addPrimarySystem(new Structure( 5, 180));		
		
		
		$this->hitChart = array(
			0=> array(
				9 => "Structure",
				13 => "Heavy Plasma Cannon",
				15 => "Scanner",
				16 => "Hangar",
				18 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				3 => "Standard Particle Beam",
				7 => "Class-S Missile Rack",
				8 => "Hangar",
				10 => "Cargo Bay",
				11 => "Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				3 => "Standard Particle Beam",
				7 => "Class-S Missile Rack",
				8 => "Hangar",
				10 => "Cargo Bay",
				11 => "Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				3 => "Standard Particle Beam",
				7 => "Class-S Missile Rack",
				8 => "Hangar",
				10 => "Cargo Bay",
				11 => "Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				3 => "Standard Particle Beam",
				7 => "Class-S Missile Rack",
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