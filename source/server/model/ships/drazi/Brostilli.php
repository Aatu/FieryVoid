<?php
class Brostilli extends SmallStarBaseFourSections
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 3000;
		$this->base = true;
		$this->smallBase = true; //"small" as in 4 outer sections
		$this->faction = "Drazi";
		$this->phpclass = "Brostilli";
		$this->shipClass = "Brostilli Warbase";
		$this->imagePath = "img/ships/Brostilli.png";
		$this->canvasSize = 300;
		$this->fighters = array("light" => 24, "superheavy" => 16);
		$this->isd = 2234;

		$this->shipSizeClass = 3;
		$this->Enormous = true;
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->forwardDefense = 21;
		$this->sideDefense = 21;



		$this->addPrimarySystem(new Reactor(6, 26, 0, 4));
		$this->addPrimarySystem(new ProtectedCnC(7, 40, 0, 0)); //originally 2 systems with structure 20, armor 6 each
		$this->addPrimarySystem(new Scanner(6, 24, 5, 8));
		$this->addPrimarySystem(new Scanner(6, 24, 5, 8));
		$this->addPrimarySystem(new TwinArray(6, 6, 2, 0, 360));
		$this->addPrimarySystem(new TwinArray(6, 6, 2, 0, 360));
		$this->addPrimarySystem(new TwinArray(6, 6, 2, 0, 360));
		$this->addPrimarySystem(new TwinArray(6, 6, 2, 0, 360));
		$this->addPrimarySystem(new ParticleRepeater(6, 6, 4, 0, 360));
		$this->addPrimarySystem(new ParticleRepeater(6, 6, 4, 0, 360));

		$this->addFrontSystem(new Hangar(5, 8));
        $this->addFrontSystem(new Catapult(5, 6));		
		$this->addFrontSystem(new CargoBay(5, 20));
		$this->addFrontSystem(new SubReactorUniversal(5, 38, 0, 0));
		$this->addFrontSystem(new ParticleCannon(5, 8, 7, 300, 60));
		$this->addFrontSystem(new ParticleBlaster(5, 8, 5, 300, 60));
		$this->addFrontSystem(new ParticleBlaster(5, 8, 5, 300, 60));
		$this->addFrontSystem(new HvyParticleCannon(5, 12, 9, 0, 90));
		$this->addFrontSystem(new StdParticleBeam(5, 4, 1, 0, 90));
		$this->addFrontSystem(new StdParticleBeam(5, 4, 1, 0, 90));

		$this->addAftSystem(new Hangar(5, 8));
        $this->addAftSystem(new Catapult(5, 6));		
		$this->addAftSystem(new CargoBay(5, 20));
		$this->addAftSystem(new SubReactorUniversal(5, 38, 0, 0));
		$this->addAftSystem(new ParticleCannon(5, 8, 7, 120, 240));
		$this->addAftSystem(new ParticleBlaster(5, 8, 5, 120, 240));
		$this->addAftSystem(new ParticleBlaster(5, 8, 5, 120, 240));
		$this->addAftSystem(new HvyParticleCannon(5, 12, 9, 0, 90));
		$this->addAftSystem(new StdParticleBeam(5, 4, 1, 180, 270));
		$this->addAftSystem(new StdParticleBeam(5, 4, 1, 180, 270));

		$this->addLeftSystem(new Hangar(5, 8));
        $this->addLeftSystem(new Catapult(5, 6));		
		$this->addLeftSystem(new CargoBay(5, 20));
		$this->addLeftSystem(new SubReactorUniversal(5, 38, 0, 0));
		$this->addLeftSystem(new ParticleCannon(5, 8, 7, 210, 330));
		$this->addLeftSystem(new ParticleBlaster(5, 8, 5, 210, 330));
		$this->addLeftSystem(new ParticleBlaster(5, 8, 5, 210, 330));
		$this->addLeftSystem(new HvyParticleCannon(5, 12, 9, 270, 360));
		$this->addLeftSystem(new StdParticleBeam(5, 4, 1, 270, 360));
		$this->addLeftSystem(new StdParticleBeam(5, 4, 1, 270, 360));
				
		$this->addRightSystem(new Hangar(5, 8));
        $this->addRightSystem(new Catapult(5, 6));		
		$this->addRightSystem(new CargoBay(5, 20));
		$this->addRightSystem(new SubReactorUniversal(5, 38, 0, 0));
		$this->addRightSystem(new ParticleCannon(5, 8, 7, 30, 150));
		$this->addRightSystem(new ParticleBlaster(5, 8, 5, 30, 150));
		$this->addRightSystem(new ParticleBlaster(5, 8, 5, 30, 150));
		$this->addRightSystem(new HvyParticleCannon(5, 12, 9, 90, 180));
		$this->addRightSystem(new StdParticleBeam(5, 4, 1, 90, 180));
		$this->addRightSystem(new StdParticleBeam(5, 4, 1, 90, 180));
				
		$this->addFrontSystem(new Structure( 5, 120));
		$this->addAftSystem(new Structure( 5, 120));
		$this->addLeftSystem(new Structure( 5, 120));
		$this->addRightSystem(new Structure( 5, 120));
		$this->addPrimarySystem(new Structure( 6, 120));		
		
		
		$this->hitChart = array(
			0=> array(
				10 => "Structure",
				12 => "Particle Repeater",
				14 => "Twin Array",
				16 => "Scanner",
				18 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				1 => "Heavy Particle Cannon",
				3 => "Particle Blaster",
				4 => "Particle Cannon",
				6 => "Standard Particle Beam",
				8 => "Cargo Bay",
				9 => "Sub Reactor",
				10 => "Hangar",
				11 => "Catapult",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				1 => "Heavy Particle Cannon",
				3 => "Particle Blaster",
				4 => "Particle Cannon",
				6 => "Standard Particle Beam",
				8 => "Cargo Bay",
				9 => "Sub Reactor",
				10 => "Hangar",
				11 => "Catapult",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				1 => "Heavy Particle Cannon",
				3 => "Particle Blaster",
				4 => "Particle Cannon",
				6 => "Standard Particle Beam",
				8 => "Cargo Bay",
				9 => "Sub Reactor",
				10 => "Hangar",
				11 => "Catapult",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				1 => "Heavy Particle Cannon",
				3 => "Particle Blaster",
				4 => "Particle Cannon",
				6 => "Standard Particle Beam",
				8 => "Cargo Bay",
				9 => "Sub Reactor",
				10 => "Hangar",
				11 => "Catapult",
				18 => "Structure",
				20 => "Primary",
			),
		);
		
		
	}
}


?>