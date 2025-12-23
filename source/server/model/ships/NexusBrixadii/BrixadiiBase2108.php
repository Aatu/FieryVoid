<?php
class BrixadiiBase2108 extends SmallStarBaseFourSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 750;
		$this->base = true;
		$this->smallBase = true;
		$this->faction = "Nexus Brixadii Clans";
		$this->phpclass = "BrixadiiBase2108";
		$this->shipClass = "Combat Base";
		$this->imagePath = "img/ships/Nexus/brixadii_combat_station.png";
		$this->canvasSize = 140; 
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
		//$this->addFrontSystem(new Quarters(4, 12));
		//$this->addFrontSystem(new CargoBay(4, 10));

			$cargoBay = new CargoBay(4, 10);
			$cargoBay->startArc = 270;
			$cargoBay->endArc = 90;
			$this->addFrontSystem($cargoBay);
			$quarters = new Quarters(4, 12);
			$quarters->startArc = 270;
			$quarters->endArc = 90;
			$this->addFrontSystem($quarters);
							
		$this->addAftSystem(new HvyParticleProjector(4, 8, 4, 90, 270));
		$this->addAftSystem(new NexusChaffLauncher(4, 2, 1, 90, 270));
		$this->addAftSystem(new EnergyPulsar(4, 6, 3, 90, 270));
		$this->addAftSystem(new EnergyPulsar(4, 6, 3, 90, 270));
		$this->addAftSystem(new NexusRangedKineticBoxLauncher(4, 10, 0, 90, 270));
		//$this->addAftSystem(new Quarters(4, 12));
		//$this->addAftSystem(new CargoBay(4, 10));

			$cargoBay = new CargoBay(4, 10);
			$cargoBay->startArc = 90;
			$cargoBay->endArc = 270;
			$this->addAftSystem($cargoBay);
			$quarters = new Quarters(4, 12);
			$quarters->startArc = 90;
			$quarters->endArc = 270;
			$this->addAftSystem($quarters);
							
		$this->addLeftSystem(new HvyParticleProjector(4, 8, 4, 180, 360));
		$this->addLeftSystem(new NexusChaffLauncher(4, 2, 1, 180, 360));
		$this->addLeftSystem(new EnergyPulsar(4, 6, 3, 180, 360));
		$this->addLeftSystem(new EnergyPulsar(4, 6, 3, 180, 360));
		$this->addLeftSystem(new NexusRangedKineticBoxLauncher(4, 10, 0, 180, 360));
		//$this->addLeftSystem(new Quarters(4, 12));
		//$this->addLeftSystem(new CargoBay(4, 10));

			$cargoBay = new CargoBay(4, 10);
			$cargoBay->startArc = 180;
			$cargoBay->endArc = 360;
			$this->addLeftSystem($cargoBay);
			$quarters = new Quarters(4, 12);
			$quarters->startArc = 180;
			$quarters->endArc = 360;
			$this->addLeftSystem($quarters);
				
		$this->addRightSystem(new HvyParticleProjector(4, 8, 4, 0, 180));
		$this->addRightSystem(new NexusChaffLauncher(4, 2, 1, 0, 180));
		$this->addRightSystem(new EnergyPulsar(4, 6, 3, 0, 180));
		$this->addRightSystem(new EnergyPulsar(4, 6, 3, 0, 180));
		$this->addRightSystem(new NexusRangedKineticBoxLauncher(4, 10, 0, 0, 180));
		//$this->addRightSystem(new Quarters(4, 12));
		//$this->addRightSystem(new CargoBay(4, 10));

			$cargoBay = new CargoBay(4, 10);
			$cargoBay->startArc = 0;
			$cargoBay->endArc = 180;
			$this->addRightSystem($cargoBay);
			$quarters = new Quarters(4, 12);
			$quarters->startArc = 0;
			$quarters->endArc = 180;
			$this->addRightSystem($quarters);
				
		/*replaced by TAGed versions!
		$this->addFrontSystem(new Structure( 4, 60));
		$this->addAftSystem(new Structure( 4, 60));
		$this->addLeftSystem(new Structure( 4, 60));
		$this->addRightSystem(new Structure( 4, 60));
		$this->addPrimarySystem(new Structure( 5, 88));
		*/
		$this->addPrimarySystem(new Structure( 5, 88));//needs to be called first for some reason - static call apparently fails for the first time...
		$this->addFrontSystem(Structure::createAsOuter(4, 60, 270,90));
		$this->addAftSystem(Structure::createAsOuter(4, 60, 90, 270));
		$this->addLeftSystem(Structure::createAsOuter(4, 60, 180, 360));
		$this->addRightSystem(Structure::createAsOuter(4, 60, 0, 180));		
				
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
				1 => "TAG:Chaff Launcher",
				3 => "TAG:Heavy Particle Projector",
				6 => "TAG:Cargo Bay",
				8 => "TAG:Energy Pulsar",
				10 => "TAG:Ranged Kinetic Box Launcher",
				12 => "TAG:Quarters",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				1 => "TAG:Chaff Launcher",
				3 => "TAG:Heavy Particle Projector",
				6 => "TAG:Cargo Bay",
				8 => "TAG:Energy Pulsar",
				10 => "TAG:Ranged Kinetic Box Launcher",
				12 => "TAG:Quarters",
				18 => "Structure",
				20 => "Primary",
			),	
			3=> array(
				1 => "TAG:Chaff Launcher",
				3 => "TAG:Heavy Particle Projector",
				6 => "TAG:Cargo Bay",
				8 => "TAG:Energy Pulsar",
				10 => "TAG:Ranged Kinetic Box Launcher",
				12 => "TAG:Quarters",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				1 => "TAG:Chaff Launcher",
				3 => "TAG:Heavy Particle Projector",
				6 => "TAG:Cargo Bay",
				8 => "TAG:Energy Pulsar",
				10 => "TAG:Ranged Kinetic Box Launcher",
				12 => "TAG:Quarters",
				18 => "Structure",
				20 => "Primary",
			),
		);

    	}
   	}
?>