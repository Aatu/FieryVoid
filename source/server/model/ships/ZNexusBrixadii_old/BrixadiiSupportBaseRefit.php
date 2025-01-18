<?php
class BrixadiiSupportBaseRefit extends SmallStarBaseFourSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 650;
		$this->base = true;
		$this->smallBase = true;
		$this->faction = "ZNexus Brixadii Clans (early)";
		$this->phpclass = "BrixadiiSupportBaseRefit";
		$this->shipClass = "Support Base (2048)";
			$this->variantOf = "Support Base";
			$this->occurence = "common";
		$this->imagePath = "img/ships/Nexus/BrixadiiBase.png";
		$this->canvasSize = 160; 
		$this->unofficial = true;
		$this->isd = 2048;

		$this->shipSizeClass = 3; 
		$this->Enormous = false;
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->forwardDefense = 16;
		$this->sideDefense = 16;

		$this->addPrimarySystem(new Reactor(4, 26, 0, 0));
		$this->addPrimarySystem(new CnC(4, 20, 0, 0));
		$this->addPrimarySystem(new Scanner(4, 20, 6, 6));
		$this->addPrimarySystem(new Hangar(4, 6));
		$this->addPrimarySystem(new LightParticleBeamShip(4, 2, 1, 0, 360));
		$this->addPrimarySystem(new LightParticleBeamShip(4, 2, 1, 0, 360));
		$this->addPrimarySystem(new LightParticleBeamShip(4, 2, 1, 0, 360));
		$this->addPrimarySystem(new LightParticleBeamShip(4, 2, 1, 0, 360));
		$this->addPrimarySystem(new ParticleHammer(4, 12, 6, 0, 360));
		$this->addPrimarySystem(new ParticleHammer(4, 12, 6, 0, 360));
		
		$this->addFrontSystem(new HvyParticleProjector(3, 8, 4, 270, 90));
		$this->addFrontSystem(new NexusChaffLauncher(4, 2, 1, 270, 90));
		$this->addFrontSystem(new NexusParticleBolter(3, 6, 2, 270, 90));
		$this->addFrontSystem(new NexusParticleBolter(3, 6, 2, 270, 90));
		$this->addFrontSystem(new NexusRangedKineticBoxLauncher(3, 10, 0, 270, 90));
		$this->addFrontSystem(new Quarters(3, 12));
		$this->addFrontSystem(new CargoBay(3, 10));

		$this->addAftSystem(new HvyParticleProjector(3, 8, 4, 90, 270));
		$this->addAftSystem(new NexusChaffLauncher(4, 2, 1, 90, 270));
		$this->addAftSystem(new NexusParticleBolter(3, 6, 2, 90, 270));
		$this->addAftSystem(new NexusParticleBolter(3, 6, 2, 90, 270));
		$this->addAftSystem(new NexusRangedKineticBoxLauncher(3, 10, 0, 90, 270));
		$this->addAftSystem(new Quarters(3, 12));
		$this->addAftSystem(new CargoBay(3, 10));
			
		$this->addLeftSystem(new HvyParticleProjector(3, 8, 4, 180, 360));
		$this->addLeftSystem(new NexusChaffLauncher(4, 2, 1, 180, 360));
		$this->addLeftSystem(new NexusParticleBolter(3, 6, 2, 180, 360));
		$this->addLeftSystem(new NexusParticleBolter(3, 6, 2, 180, 360));
		$this->addLeftSystem(new NexusRangedKineticBoxLauncher(4, 10, 0, 180, 360));
		$this->addLeftSystem(new Quarters(3, 12));
		$this->addLeftSystem(new CargoBay(3, 10));

		$this->addRightSystem(new HvyParticleProjector(3, 8, 4, 0, 180));
		$this->addRightSystem(new NexusChaffLauncher(4, 2, 1, 0, 180));
		$this->addRightSystem(new NexusParticleBolter(3, 6, 2, 0, 180));
		$this->addRightSystem(new NexusParticleBolter(3, 6, 2, 0, 180));
		$this->addRightSystem(new NexusRangedKineticBoxLauncher(3, 10, 0, 0, 180));
		$this->addRightSystem(new Quarters(3, 12));
		$this->addRightSystem(new CargoBay(3, 10));

		$this->addFrontSystem(new Structure( 3, 60));
		$this->addAftSystem(new Structure( 3, 60));
		$this->addLeftSystem(new Structure( 3, 60));
		$this->addRightSystem(new Structure( 3, 60));
		$this->addPrimarySystem(new Structure( 4, 88));
		
		$this->hitChart = array(			
			0=> array(
				6 => "Structure",
				8 => "Light Particle Beam",
				13 => "Particle Hammer",
				14 => "Hangar",
				16 => "Scanner",
				18 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				1 => "Chaff Launcher",
				3 => "Heavy Particle Projector",
				6 => "Cargo Bay",
				8 => "Particle Bolter",
				10 => "Ranged Kinetic Box Launcher",
				12 => "Quarters",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				1 => "Chaff Launcher",
				3 => "Heavy Particle Projector",
				6 => "Cargo Bay",
				8 => "Particle Bolter",
				10 => "Ranged Kinetic Box Launcher",
				12 => "Quarters",
				18 => "Structure",
				20 => "Primary",
			),	
			3=> array(
				1 => "Chaff Launcher",
				3 => "Heavy Particle Projector",
				6 => "Cargo Bay",
				8 => "Particle Bolter",
				10 => "Ranged Kinetic Box Launcher",
				12 => "Quarters",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				1 => "Chaff Launcher",
				3 => "Heavy Particle Projector",
				6 => "Cargo Bay",
				8 => "Particle Bolter",
				10 => "Ranged Kinetic Box Launcher",
				12 => "Quarters",
				18 => "Structure",
				20 => "Primary",
			),
		);

    	}
   	}
?>