<?php
class Kromala extends SmallStarBaseFourSections
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 1250;
		$this->base = true;
		$this->smallBase = true; //"small" as in 4 outer sections
		$this->faction = "Drazi (WotCR)";
		$this->phpclass = "Kromala";
		$this->shipClass = "Kromala Defense Base";
		$this->imagePath = "img/ships/drazi/DraziKromala.png";
		$this->canvasSize = 280;
		$this->fighters = array("light" => 24);
		$this->isd = 2000;

		$this->shipSizeClass = 3;
		$this->Enormous = true;
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->forwardDefense = 20;
		$this->sideDefense = 20;

		$this->addPrimarySystem(new Reactor(5, 26, 0, 0));
		$this->addPrimarySystem(new ProtectedCnC(5, 40, 0, 0)); //originally 2 systems with structure 20, armor 5 each
		$this->addPrimarySystem(new Scanner(5, 24, 5, 7));
		$this->addPrimarySystem(new Scanner(5, 24, 5, 7));
		$this->addPrimarySystem(new Hangar(5, 28));
		$this->addPrimarySystem(new ParticleCannon(5, 8, 7, 0, 360));
		$this->addPrimarySystem(new ParticleCannon(5, 8, 7, 0, 360));
		$this->addPrimarySystem(new RepeaterGun(5, 6, 4, 0, 360));
		$this->addPrimarySystem(new RepeaterGun(5, 6, 4, 0, 360));

		$this->addFrontSystem(new CargoBay(4, 20));
		$this->addFrontSystem(new SubReactorUniversal(4, 25, 0, 0));
		$this->addFrontSystem(new ParticleCannon(4, 8, 7, 270, 90));
		$this->addFrontSystem(new HeavyPlasma(4, 8, 5, 270, 90));
		$this->addFrontSystem(new ParticleCannon(4, 8, 7, 270, 90));
		$this->addFrontSystem(new StdParticleBeam(4, 4, 1, 270, 90));
		$this->addFrontSystem(new StdParticleBeam(4, 4, 1, 270, 90));

		$this->addAftSystem(new CargoBay(4, 20));
		$this->addAftSystem(new SubReactorUniversal(4, 25, 0, 0));
		$this->addAftSystem(new ParticleCannon(4, 8, 7, 90, 270));
		$this->addAftSystem(new HeavyPlasma(4, 8, 5, 90, 270));
		$this->addAftSystem(new ParticleCannon(4, 8, 7, 90, 270));
		$this->addAftSystem(new StdParticleBeam(4, 4, 1, 90, 270));
		$this->addAftSystem(new StdParticleBeam(4, 4, 1, 90, 270));

		$this->addLeftSystem(new CargoBay(4, 20));
		$this->addLeftSystem(new SubReactorUniversal(4, 25, 0, 0));
		$this->addLeftSystem(new ParticleCannon(4, 8, 7, 180, 360));
		$this->addLeftSystem(new HeavyPlasma(4, 8, 5, 180, 360));
		$this->addLeftSystem(new ParticleCannon(4, 8, 7, 180, 360));
		$this->addLeftSystem(new StdParticleBeam(4, 4, 1, 180, 360));
		$this->addLeftSystem(new StdParticleBeam(4, 4, 1, 180, 360));

		$this->addRightSystem(new CargoBay(4, 20));
		$this->addRightSystem(new SubReactorUniversal(4, 25, 0, 0));
		$this->addRightSystem(new ParticleCannon(4, 8, 7, 0, 180));
		$this->addRightSystem(new HeavyPlasma(4, 8, 5, 0, 180));
		$this->addRightSystem(new ParticleCannon(4, 8, 7, 0, 180));
		$this->addRightSystem(new StdParticleBeam(4, 4, 1, 0, 180));
		$this->addRightSystem(new StdParticleBeam(4, 4, 1, 0, 180));
				
		$this->addFrontSystem(new Structure(4, 100));
		$this->addAftSystem(new Structure(4, 100));
		$this->addLeftSystem(new Structure(4, 100));
		$this->addRightSystem(new Structure(4, 100));
		$this->addPrimarySystem(new Structure(5, 120));		
		
		
		$this->hitChart = array(
			0=> array(
				8 => "Structure",
				10 => "Particle Cannon",
				12 => "Repeater Gun",
				14 => "Scanner",
				16 => "Hangar",
				18 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				2 => "Heavy Plasma Cannon",
				5 => "Particle Cannon",
				7 => "Standard Particle Beam",
				9 => "Cargo Bay",
				11 => "Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				2 => "Heavy Plasma Cannon",
				5 => "Particle Cannon",
				7 => "Standard Particle Beam",
				9 => "Cargo Bay",
				11 => "Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				2 => "Heavy Plasma Cannon",
				5 => "Particle Cannon",
				7 => "Standard Particle Beam",
				9 => "Cargo Bay",
				11 => "Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				2 => "Heavy Plasma Cannon",
				5 => "Particle Cannon",
				7 => "Standard Particle Beam",
				9 => "Cargo Bay",
				11 => "Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
		);
		
		
	}
}


?>