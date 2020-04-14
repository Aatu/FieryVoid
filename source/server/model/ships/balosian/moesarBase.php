<?php
class MoesarBase extends SmallStarBaseFourSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 625;
		$this->base = true;
		$this->smallBase = true;
		$this->faction = "Balosian";
		$this->phpclass = "MoesarBase";
		$this->shipClass = "Moesar Outpost";
		$this->imagePath = "img/ships/Moesar.png";
		$this->canvasSize = 200; 
		$this->fighters = array("heavy"=>18); 
		$this->isd = 2253;

		$this->shipSizeClass = 3; 
		$this->Enormous = true;
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->forwardDefense = 16;
		$this->sideDefense = 16;


		$this->addPrimarySystem(new Reactor(5, 30, 0, 0));
		$this->addPrimarySystem(new CnC(5, 24, 0, 0));
		$this->addPrimarySystem(new Scanner(5, 16, 6, 8));
		$this->addPrimarySystem(new Hangar(5, 22));
		$this->addPrimarySystem(new StdParticleBeam(5, 4, 1, 0, 360));
		$this->addPrimarySystem(new StdParticleBeam(5, 4, 1, 0, 360));
		
		$this->addFrontSystem(new IonCannon(4, 6, 4, 300, 60));
		$this->addFrontSystem(new IonCannon(4, 6, 4, 300, 60));
		$this->addFrontSystem(new IonCannon(4, 6, 4, 240, 0));
		$this->addFrontSystem(new StdParticleBeam(4, 4, 1, 300, 60));
		$this->addFrontSystem(new StdParticleBeam(4, 4, 1, 300, 60));

		$this->addAftSystem(new IonCannon(4, 6, 4, 120, 240));
		$this->addAftSystem(new IonCannon(4, 6, 4, 120, 240));
		$this->addAftSystem(new IonCannon(4, 6, 4, 60, 180));
		$this->addAftSystem(new StdParticleBeam(4, 4, 1, 120, 240));
		$this->addAftSystem(new StdParticleBeam(4, 4, 1, 120, 240));
			
		$this->addLeftSystem(new IonCannon(4, 6, 4, 210, 330));
		$this->addLeftSystem(new IonCannon(4, 6, 4, 210, 330));
		$this->addLeftSystem(new IonCannon(4, 6, 4, 180, 300));
		$this->addLeftSystem(new StdParticleBeam(4, 4, 1, 210, 330));
		$this->addLeftSystem(new StdParticleBeam(4, 4, 1, 210, 330));

		$this->addRightSystem(new IonCannon(4, 6, 4, 30, 150));
		$this->addRightSystem(new IonCannon(4, 6, 4, 30, 150));
		$this->addRightSystem(new IonCannon(4, 6, 4, 0, 120));
		$this->addRightSystem(new StdParticleBeam(4, 4, 1, 30, 150));
		$this->addRightSystem(new StdParticleBeam(4, 4, 1, 30, 150));


		$this->addFrontSystem(new Structure( 4, 60));
		$this->addAftSystem(new Structure( 4, 60));
		$this->addLeftSystem(new Structure( 4, 60));
		$this->addRightSystem(new Structure( 4, 60));
		$this->addPrimarySystem(new Structure( 5, 60));
		
		$this->hitChart = array(			
			0=> array(
				9 => "Structure",
				11 => "Standard Particle Beam",
				13 => "Scanner",
				16 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				6 => "Ion Cannon",
				9 => "Standard Particle Beam",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				6 => "Ion Cannon",
				9 => "Standard Particle Beam",
				18 => "Structure",
				20 => "Primary",
			),	
			3=> array(
				6 => "Ion Cannon",
				9 => "Standard Particle Beam",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				6 => "Ion Cannon",
				9 => "Standard Particle Beam",
				18 => "Structure",
				20 => "Primary",
			),
		);

    	}
   	}
?>