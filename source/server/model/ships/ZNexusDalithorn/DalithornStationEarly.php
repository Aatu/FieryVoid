<?php
class DalithornStationEarly extends SmallStarBaseFourSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 650;
		$this->base = true;
		$this->smallBase = true;
		$this->faction = "ZNexus Dalithorn Commonwealth";
		$this->phpclass = "DalithornStationEarly";
		$this->shipClass = "Early Station";
			$this->variantOf = "Station";
			$this->occurence = "common";
		$this->imagePath = "img/ships/Nexus/Dalithorn_Station2.png";
		$this->canvasSize = 175; 
		$this->unofficial = true;
		$this->isd = 1945;

		$this->shipSizeClass = 3; 
		$this->Enormous = false;
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->fighters = array("superheavy"=>4);

		$this->forwardDefense = 17;
		$this->sideDefense = 17;

		$this->addPrimarySystem(new Reactor(4, 25, 0, 0));
		$this->addPrimarySystem(new CnC(4, 25, 0, 0));
		$this->addPrimarySystem(new Scanner(4, 14, 5, 6));
		$this->addPrimarySystem(new Hangar(4, 6));
		$this->addPrimarySystem(new Magazine(4, 24));
		$this->addPrimarySystem(new NexusShatterGun(4, 2, 1, 0, 360));
		$this->addPrimarySystem(new NexusShatterGun(4, 2, 1, 0, 360));
		$this->addPrimarySystem(new NexusShatterGun(4, 2, 1, 0, 360));
		$this->addPrimarySystem(new NexusShatterGun(4, 2, 1, 0, 360));
		
		$this->addFrontSystem(new NexusCoilgun(4, 10, 4, 300, 60));
		$this->addFrontSystem(new NexusGasGun(4, 7, 2, 270, 90));
		$this->addFrontSystem(new NexusLightGasGun(4, 5, 1, 270, 90));
		$this->addFrontSystem(new Catapult(4, 6));
		$this->addFrontSystem(new CargoBay(4, 36));

		$this->addAftSystem(new NexusCoilgun(4, 10, 4, 120, 240));
		$this->addAftSystem(new NexusGasGun(4, 7, 2, 90, 270));
		$this->addAftSystem(new NexusLightGasGun(4, 5, 1, 90, 270));
		$this->addAftSystem(new Catapult(4, 6));
		$this->addAftSystem(new CargoBay(4, 36));
			
		$this->addLeftSystem(new NexusCoilgun(4, 10, 4, 210, 330));
		$this->addLeftSystem(new NexusGasGun(4, 7, 2, 180, 360));
		$this->addLeftSystem(new NexusLightGasGun(4, 5, 1, 180, 360));
		$this->addLeftSystem(new Catapult(4, 6));
		$this->addLeftSystem(new CargoBay(4, 36));

		$this->addRightSystem(new NexusCoilgun(4, 10, 4, 30, 150));
		$this->addRightSystem(new NexusGasGun(4, 7, 2, 0, 180));
		$this->addRightSystem(new NexusLightGasGun(4, 5, 1, 0, 180));
		$this->addRightSystem(new Catapult(4, 6));
		$this->addRightSystem(new CargoBay(4, 36));

		$this->addFrontSystem(new Structure( 4, 64));
		$this->addAftSystem(new Structure( 4, 64));
		$this->addLeftSystem(new Structure( 4, 64));
		$this->addRightSystem(new Structure( 4, 64));
		$this->addPrimarySystem(new Structure( 4, 80));
		
		$this->hitChart = array(			
			0=> array(
				8 => "Structure",
				10 => "Magazine",
				13 => "Shatter Gun",
				15 => "Hangar",
				17 => "Scanner",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				2 => "Coilgun",
				3 => "Light Gas Gun",
				4 => "Catapult",
				5 => "Gas Gun",
				10 => "Cargo Bay",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				2 => "Coilgun",
				3 => "Light Gas Gun",
				4 => "Catapult",
				5 => "Gas Gun",
				10 => "Cargo Bay",
				18 => "Structure",
				20 => "Primary",
			),	
			3=> array(
				2 => "Coilgun",
				3 => "Light Gas Gun",
				4 => "Catapult",
				5 => "Gas Gun",
				10 => "Cargo Bay",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				2 => "Coilgun",
				3 => "Light Gas Gun",
				4 => "Catapult",
				5 => "Gas Gun",
				10 => "Cargo Bay",
				18 => "Structure",
				20 => "Primary",
			),
		);

    	}
   	}
?>