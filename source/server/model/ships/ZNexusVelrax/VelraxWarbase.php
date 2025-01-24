<?php
class VelraxWarbase extends SmallStarBaseFourSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 750;
		$this->base = true;
		$this->smallBase = true;
		$this->faction = "ZNexus Velrax Republic";
		$this->phpclass = "VelraxWarbase";
		$this->shipClass = "Warbase";
		$this->imagePath = "img/ships/Nexus/velraxBase.png";
		$this->canvasSize = 140; 
		$this->unofficial = true;
		$this->isd = 2112;

		$this->shipSizeClass = 3; 
		$this->Enormous = false;
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->fighters = array("normal"=>24);

		$this->forwardDefense = 18;
		$this->sideDefense = 18;

		$this->addPrimarySystem(new Reactor(5, 35, 0, 0));
		$this->addPrimarySystem(new CnC(5, 24, 0, 0));
		$this->addPrimarySystem(new Scanner(5, 16, 6, 6));
		$this->addPrimarySystem(new Hangar(5, 6));
		$this->addPrimarySystem(new CargoBay(5, 21));
		$this->addPrimarySystem(new NexusStreakInterceptor(5, 4, 1, 0, 360));
		$this->addPrimarySystem(new NexusStreakInterceptor(5, 4, 1, 0, 360));
		$this->addPrimarySystem(new NexusStreakInterceptor(5, 4, 1, 0, 360));
		
		$this->addFrontSystem(new LaserLance(4, 6, 4, 270, 90));
		$this->addFrontSystem(new NexusRangedPlasmaWave(4, 7, 4, 270, 90));
		$this->addFrontSystem(new DualIonBolter(4, 4, 4, 270, 90));
		$this->addFrontSystem(new DualIonBolter(4, 4, 4, 270, 90));
		$this->addFrontSystem(new Hangar(4, 9));
		$this->addFrontSystem(new CargoBay(4, 24));

		$this->addAftSystem(new LaserLance(4, 6, 4, 90, 270));
		$this->addAftSystem(new NexusRangedPlasmaWave(4, 7, 4, 90, 270));
		$this->addAftSystem(new DualIonBolter(4, 4, 4, 90, 270));
		$this->addAftSystem(new DualIonBolter(4, 4, 4, 90, 270));
		$this->addAftSystem(new Hangar(4, 6));
		$this->addAftSystem(new CargoBay(4, 24));
			
		$this->addLeftSystem(new LaserLance(4, 6, 4, 180, 360));
		$this->addLeftSystem(new NexusRangedPlasmaWave(4, 7, 4, 180, 360));
		$this->addLeftSystem(new DualIonBolter(4, 4, 4, 180, 360));
		$this->addLeftSystem(new DualIonBolter(4, 4, 4, 180, 360));
		$this->addLeftSystem(new Hangar(4, 6));
		$this->addLeftSystem(new CargoBay(4, 24));

		$this->addRightSystem(new LaserLance(4, 6, 4, 0, 180));
		$this->addRightSystem(new NexusRangedPlasmaWave(4, 7, 4, 0, 180));
		$this->addRightSystem(new DualIonBolter(4, 4, 4, 0, 180));
		$this->addRightSystem(new DualIonBolter(4, 4, 4, 0, 180));
		$this->addRightSystem(new Hangar(4, 6));
		$this->addRightSystem(new CargoBay(4, 24));

		$this->addFrontSystem(new Structure( 4, 70));
		$this->addAftSystem(new Structure( 4, 70));
		$this->addLeftSystem(new Structure( 4, 70));
		$this->addRightSystem(new Structure( 4, 70));
		$this->addPrimarySystem(new Structure( 5, 86));
		
		$this->hitChart = array(			
			0=> array(
				9 => "Structure",
				11 => "Streak Interceptor",
				12 => "Hangar",
				15 => "Cargo Bay",
				17 => "Scanner",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				2 => "Laser Lance",
				4 => "Dual Ion Bolter",
				5 => "Hangar",
				9 => "Cargo Bay",
				11 => "Ranged Plasma Wave",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				2 => "Laser Lance",
				4 => "Dual Ion Bolter",
				5 => "Hangar",
				9 => "Cargo Bay",
				11 => "Ranged Plasma Wave",
				18 => "Structure",
				20 => "Primary",
			),	
			3=> array(
				2 => "Laser Lance",
				4 => "Dual Ion Bolter",
				5 => "Hangar",
				9 => "Cargo Bay",
				11 => "Ranged Plasma Wave",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				2 => "Laser Lance",
				4 => "Dual Ion Bolter",
				5 => "Hangar",
				9 => "Cargo Bay",
				11 => "Ranged Plasma Wave",
				18 => "Structure",
				20 => "Primary",
			),
		);

    	}
   	}
?>