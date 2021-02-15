<?php
class Kaedashkada extends UnevenBaseFourSections 
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 2250;
		$this->base = true;
		$this->smallBase = true; //small = four sections
		$this->faction = "Llort";
		$this->phpclass = "Kaedashkada";
		$this->shipClass = "Kaedashkada Starbase";
		$this->imagePath = "img/ships/LlortKaedashkada.png";
		$this->canvasSize = 300;
		$this->fighters = array("normal"=>36);
		$this->isd = 2228;

		$this->shipSizeClass = 3;
		$this->Enormous = true;
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->forwardDefense = 23;
		$this->sideDefense = 25;



		$this->addPrimarySystem(new Reactor(5, 20, 0, 0));
		$this->addPrimarySystem(new CnC(5, 30, 0, 0));
		$this->addPrimarySystem(new Scanner(5, 16, 5, 7));
		$this->addPrimarySystem(new Scanner(5, 16, 5, 7));
		$this->addPrimarySystem(new Hangar(5, 6));
        $this->addPrimarySystem(new ReloadRack(5, 6));	
        $this->addPrimarySystem(new CargoBay(5, 40));	
		$this->addPrimarySystem(new LMissileRack(5, 6, 0, 0, 360, true));
		$this->addPrimarySystem(new LMissileRack(5, 6, 0, 0, 360, true));
		$this->addPrimarySystem(new StdParticleBeam(5, 4, 1, 0, 360));
		$this->addPrimarySystem(new StdParticleBeam(5, 4, 1, 0, 360));


		$this->addFrontSystem(new Hangar(3, 6));
		$this->addFrontSystem(new CargoBay(4, 25));
		$this->addFrontSystem(new SubReactorUniversal(4, 24, 0, 0));
		$this->addFrontSystem(new LMissileRack(4, 6, 0, 300, 60, true));
		$this->addFrontSystem(new ParticleCannon(4, 8, 7, 300, 60));
		$this->addFrontSystem(new ScatterGun(3, 8, 3, 240, 60));
		$this->addFrontSystem(new ScatterGun(3, 8, 3, 240, 60));
		$this->addFrontSystem(new ScatterGun(3, 8, 3, 240, 60));


		$this->addAftSystem(new Hangar(4, 6));
		$this->addAftSystem(new Hangar(4, 6));		
		$this->addAftSystem(new CargoBay(4, 25));
		$this->addAftSystem(new SubReactorUniversal(4, 31, 0, 0));
		$this->addAftSystem(new LMissileRack(4, 6, 0, 120, 240, true));
		$this->addAftSystem(new ParticleCannon(4, 8, 7, 120, 240));
		$this->addAftSystem(new StdParticleBeam(4, 4, 1, 120, 240));
		$this->addAftSystem(new StdParticleBeam(4, 4, 1, 120, 240));
		$this->addAftSystem(new TwinArray(4, 6, 2, 120, 240));
		$this->addAftSystem(new ScatterGun(3, 8, 3, 60, 240));
		$this->addAftSystem(new ScatterGun(3, 8, 3, 60, 240));
		$this->addAftSystem(new ScatterGun(3, 8, 3, 60, 240));


		$this->addLeftSystem(new Hangar(4, 6));
		$this->addLeftSystem(new CargoBay(4, 25));
		$this->addLeftSystem(new SubReactorUniversal(4, 23, 0, 0));
		$this->addLeftSystem(new LMissileRack(4, 6, 0, 180, 300, true));
		$this->addLeftSystem(new ParticleCannon(4, 8, 7, 180, 360));
		$this->addLeftSystem(new StdParticleBeam(4, 4, 1, 180, 300));
		$this->addLeftSystem(new StdParticleBeam(4, 4, 1, 180, 300));
		$this->addLeftSystem(new TwinArray(4, 6, 2, 180, 360));
		
				
		$this->addRightSystem(new Hangar(4, 6));
		$this->addRightSystem(new Hangar(4, 6));		
		$this->addRightSystem(new CargoBay(4, 25));
		$this->addRightSystem(new SubReactorUniversal(4, 23, 0, 0));
		$this->addRightSystem(new LMissileRack(4, 6, 0, 0, 180, true));
		$this->addRightSystem(new ParticleCannon(4, 8, 7, 0, 120));
		$this->addRightSystem(new StdParticleBeam(4, 4, 1, 0, 180));
		$this->addRightSystem(new StdParticleBeam(4, 4, 1, 0, 180));
		$this->addRightSystem(new TwinArray(4, 6, 2, 0, 120));
				
		$this->addFrontSystem(new Structure( 4, 200));
		$this->addAftSystem(new Structure( 4, 230));
		$this->addLeftSystem(new Structure( 4, 210));
		$this->addRightSystem(new Structure( 4, 210));
		$this->addPrimarySystem(new Structure( 5, 180));		
		
		
		$this->hitChart = array(
			0=> array(
				9 => "Structure",
				10 => "Class-L Missile Rack",
				11 => "Standard Particle Beam",
				14 => "Scanner",
				16 => "Reload Rack",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				3 => "Scattergun",
				5 => "Class-L Missile Rack",
				7 => "Particle Cannon",
				9 => "Hangar",
				11 => "Cargo Bay",
				13 => "Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				3 => "Scattergun",
				4 => "Class-L Missile Rack",
				5 => "Particle Cannon",
				6 => "Twin Array",
				7 => "Standard Particle Beam",
				9 => "Hangar",
				11 => "Cargo Bay",
				13 => "Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				1 => "Twin Array",
				3 => "Class-L Missile Rack",
				5 => "Particle Cannon",
				7 => "Standard Particle Beam",
				9 => "Hangar",
				11 => "Cargo Bay",
				13 => "Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				1 => "Twin Array",
				3 => "Class-L Missile Rack",
				5 => "Particle Cannon",
				7 => "Standard Particle Beam",
				9 => "Hangar",
				11 => "Cargo Bay",
				13 => "Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
		);
		
		
	}
}


?>