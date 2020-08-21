<?php
class BrixadiiCombatBase2112 extends SmallStarBaseFourSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 725;
		$this->base = true;
		$this->smallBase = true;
		$this->faction = "ZNexus Brixadii";
		$this->phpclass = "BrixadiiCombatBase2112";
		$this->shipClass = "Brixadii Combat Base (2112)";
		$this->variantOf = "Brixadii Combat Base";
		$this->occurence = "common";
		$this->imagePath = "img/ships/Nexus/brixadii_support_base.png";
			$this->canvasSize = 200; //img has 200px per side
		$this->unofficial = true;
		$this->isd = 2112;
		
		$this->shipSizeClass = 3; 
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->forwardDefense = 18;
		$this->sideDefense = 18;

		$this->addPrimarySystem(new Reactor(5, 32, 0, 0));
		$this->addPrimarySystem(new CnC(5, 20, 0, 0)); 
		$this->addPrimarySystem(new Scanner(5, 20, 6, 6));
		$this->addPrimarySystem(new Hangar(5, 6));
		$this->addPrimarySystem(new ParticleHammer(5, 12, 6, 0, 360));
		$this->addPrimarySystem(new ParticleHammer(5, 12, 6, 0, 360));
		$this->addPrimarySystem(new NexusLightProjectorArray(5, 5, 2, 0, 360));
		$this->addPrimarySystem(new NexusLightProjectorArray(5, 5, 2, 0, 360));
		$this->addPrimarySystem(new NexusLightProjectorArray(5, 5, 2, 0, 360));
		$this->addPrimarySystem(new NexusLightProjectorArray(5, 5, 2, 0, 360));

		$this->addFrontSystem(new NexusProjectorArray(4, 6, 1, 270, 90));
		$this->addFrontSystem(new NexusProjectorArray(4, 6, 1, 270, 90));
		$this->addFrontSystem(new NexusChaffLauncher(4, 2, 1, 270, 90));
		$this->addFrontSystem(new CargoBay(4, 20));
		$this->addFrontSystem(new HvyParticleProjector(4, 8, 4, 270, 90));
		
		$this->addLeftSystem(new NexusProjectorArray(4, 6, 1, 180, 360));
		$this->addLeftSystem(new NexusProjectorArray(4, 6, 1, 180, 360));
		$this->addLeftSystem(new NexusChaffLauncher(4, 2, 1, 180, 360));
		$this->addLeftSystem(new CargoBay(4, 20));
		$this->addLeftSystem(new HvyParticleProjector(4, 8, 4, 180, 360));

		$this->addRightSystem(new NexusProjectorArray(4, 6, 1, 0, 180));
		$this->addRightSystem(new NexusProjectorArray(4, 6, 1, 0, 180));
		$this->addRightSystem(new NexusChaffLauncher(4, 2, 1, 0, 180));
		$this->addRightSystem(new CargoBay(4, 20));
		$this->addRightSystem(new HvyParticleProjector(4, 8, 4, 0, 180));

		$this->addAftSystem(new NexusProjectorArray(4, 6, 1, 90, 270));
		$this->addAftSystem(new NexusProjectorArray(4, 6, 1, 90, 270));
		$this->addAftSystem(new NexusChaffLauncher(4, 2, 1, 90, 270));
		$this->addAftSystem(new CargoBay(4, 20));
		$this->addAftSystem(new HvyParticleProjector(4, 8, 4, 90, 270));

		$this->addFrontSystem(new Structure( 4, 60));
		$this->addAftSystem(new Structure( 4, 60));
		$this->addLeftSystem(new Structure( 4, 60));
		$this->addRightSystem(new Structure( 4, 60));
		$this->addPrimarySystem(new Structure( 5, 88));
		
		$this->hitChart = array(			
			0=> array(
				6 => "Structure",
				8 => "Light Projector Array",
				13 => "Particle Hammer",
				14 => "Hangar",
				16 => "Scanner",
				18 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				1 => "Chaff Launcher",
				3 => "Projector Array",
				6 => "Heavy Particle Projector",
				10 => "Cargo Bay",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				1 => "Chaff Launcher",
				3 => "Projector Array",
				6 => "Heavy Particle Projector",
				10 => "Cargo Bay",
				18 => "Structure",
				20 => "Primary",
			),	
			3=> array(
				1 => "Chaff Launcher",
				3 => "Projector Array",
				6 => "Heavy Particle Projector",
				10 => "Cargo Bay",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				1 => "Chaff Launcher",
				3 => "Projector Array",
				6 => "Heavy Particle Projector",
				10 => "Cargo Bay",
				18 => "Structure",
				20 => "Primary",
			),
		);


		}
    }
?>
