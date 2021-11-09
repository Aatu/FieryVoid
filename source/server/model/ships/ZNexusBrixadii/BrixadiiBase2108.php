<?php
class BrixadiiBase2108 extends SmallStarBaseFourSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 825;
		$this->base = true;
		$this->smallBase = true;
		$this->faction = "ZNexus Brixadii";
		$this->phpclass = "BrixadiiBase2108";
		$this->shipClass = "Combat Base";
		$this->imagePath = "img/ships/Nexus/BrixadiiBase.png";
		$this->canvasSize = 160; 
		$this->unofficial = true;
		$this->isd = 2108;

		$this->shipSizeClass = 3; 
		$this->Enormous = false;
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->forwardDefense = 16;
		$this->sideDefense = 16;

		$this->addPrimarySystem(new Reactor(5, 32, 0, 0));
		$this->addPrimarySystem(new CnC(5, 20, 0, 0));
		$this->addPrimarySystem(new Scanner(5, 20, 6, 7));
		$this->addPrimarySystem(new Hangar(5, 6));
		$this->addPrimarySystem(new ScatterPulsar(5, 4, 2, 0, 360));
		$this->addPrimarySystem(new ScatterPulsar(5, 4, 2, 0, 360));
		$this->addPrimarySystem(new ScatterPulsar(5, 4, 2, 0, 360));
		$this->addPrimarySystem(new ScatterPulsar(5, 4, 2, 0, 360));
		$this->addPrimarySystem(new ParticleHammer(5, 12, 6, 0, 360));
		$this->addPrimarySystem(new ParticleHammer(5, 12, 6, 0, 360));
		
		$this->addFrontSystem(new HvyParticleProjector(4, 8, 4, 270, 90));
		$this->addFrontSystem(new NexusChaffLauncher(4, 2, 1, 270, 90));
		$this->addFrontSystem(new EnergyPulsar(4, 6, 3, 270, 90));
		$this->addFrontSystem(new EnergyPulsar(4, 6, 3, 270, 90));
		$this->addFrontSystem(new NexusRangedKineticBoxLauncher(4, 10, 0, 270, 90));
		$this->addFrontSystem(new Quarters(4, 12));
		$this->addFrontSystem(new CargoBay(4, 10));

		$this->addAftSystem(new HvyParticleProjector(4, 8, 4, 90, 270));
		$this->addAftSystem(new NexusChaffLauncher(4, 2, 1, 90, 270));
		$this->addAftSystem(new EnergyPulsar(4, 6, 3, 90, 270));
		$this->addAftSystem(new EnergyPulsar(4, 6, 3, 90, 270));
		$this->addAftSystem(new NexusRangedKineticBoxLauncher(4, 10, 0, 90, 270));
		$this->addAftSystem(new Quarters(4, 12));
		$this->addAftSystem(new CargoBay(4, 10));
			
		$this->addLeftSystem(new HvyParticleProjector(4, 8, 4, 180, 360));
		$this->addLeftSystem(new NexusChaffLauncher(4, 2, 1, 180, 360));
		$this->addLeftSystem(new EnergyPulsar(4, 6, 3, 180, 360));
		$this->addLeftSystem(new EnergyPulsar(4, 6, 3, 180, 360));
		$this->addLeftSystem(new NexusRangedKineticBoxLauncher(4, 10, 0, 180, 360));
		$this->addLeftSystem(new Quarters(4, 12));
		$this->addLeftSystem(new CargoBay(4, 10));

		$this->addRightSystem(new HvyParticleProjector(4, 8, 4, 0, 180));
		$this->addRightSystem(new NexusChaffLauncher(4, 2, 1, 0, 180));
		$this->addRightSystem(new EnergyPulsar(4, 6, 3, 0, 180));
		$this->addRightSystem(new EnergyPulsar(4, 6, 3, 0, 180));
		$this->addRightSystem(new NexusRangedKineticBoxLauncher(4, 10, 0, 0, 180));
		$this->addRightSystem(new Quarters(4, 12));
		$this->addRightSystem(new CargoBay(4, 10));

		$this->addFrontSystem(new Structure( 4, 60));
		$this->addAftSystem(new Structure( 4, 60));
		$this->addLeftSystem(new Structure( 4, 60));
		$this->addRightSystem(new Structure( 4, 60));
		$this->addPrimarySystem(new Structure( 5, 88));
		
		$this->hitChart = array(			
			0=> array(
				6 => "Structure",
				8 => "Scatter Pulsar",
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
				8 => "Energy Pulsar",
				9 => "Ranged Kinetic Box Launcher",
				11 => "Quarters",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				1 => "Chaff Launcher",
				3 => "Heavy Particle Projector",
				6 => "Cargo Bay",
				8 => "Energy Pulsar",
				9 => "Ranged Kinetic Box Launcher",
				11 => "Quarters",
				18 => "Structure",
				20 => "Primary",
			),	
			3=> array(
				1 => "Chaff Launcher",
				3 => "Heavy Particle Projector",
				6 => "Cargo Bay",
				8 => "Energy Pulsar",
				9 => "Ranged Kinetic Box Launcher",
				11 => "Quarters",
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