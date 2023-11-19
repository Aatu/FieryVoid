<?php
class CraytanOlipan extends SmallStarBaseFourSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 450;
		$this->base = true;
		$this->smallBase = true;
		$this->faction = "ZNexus Craytan Union";
		$this->phpclass = "CraytanOlipan";
		$this->shipClass = "Olipan Supply Post";
		$this->imagePath = "img/ships/Nexus/CraytanOlipan.png";
		$this->canvasSize = 175; 
		$this->unofficial = true;
		$this->isd = 1999;

		$this->shipSizeClass = 3; 
		$this->Enormous = false;
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

        $this->fighters = array("assault shuttles"=>12);

		$this->forwardDefense = 19;
		$this->sideDefense = 19;

		$this->addPrimarySystem(new Reactor(4, 20, 0, 0));
		$this->addPrimarySystem(new CnC(4, 16, 0, 0));
		$this->addPrimarySystem(new Scanner(4, 16, 5, 5));
		$this->addPrimarySystem(new Hangar(4, 6));
		$this->addPrimarySystem(new CargoBay(4, 42));
		$this->addPrimarySystem(new Magazine(4, 24));
		
		$this->addFrontSystem(new NexusCIDS(4, 4, 2, 270, 90));
		$this->addFrontSystem(new NexusCIDS(4, 4, 2, 270, 90));
		$this->addFrontSystem(new NexusMedSentryGun(4, 6, 2, 270, 90));
		$this->addFrontSystem(new NexusMedSentryGun(4, 6, 2, 270, 90));
		$this->addFrontSystem(new Hangar(4, 3));
		$this->addFrontSystem(new CargoBay(4, 36));

		$this->addAftSystem(new NexusCIDS(4, 4, 2, 90, 270));
		$this->addAftSystem(new NexusCIDS(4, 4, 2, 90, 270));
		$this->addAftSystem(new NexusMedSentryGun(4, 6, 2, 90, 270));
		$this->addAftSystem(new NexusMedSentryGun(4, 6, 2, 90, 270));
		$this->addAftSystem(new Hangar(4, 3));
		$this->addAftSystem(new CargoBay(4, 36));
			
		$this->addLeftSystem(new NexusCIDS(4, 4, 2, 180, 360));
		$this->addLeftSystem(new NexusCIDS(4, 4, 2, 180, 360));
		$this->addLeftSystem(new NexusMedSentryGun(4, 6, 2, 180, 360));
		$this->addLeftSystem(new NexusMedSentryGun(4, 6, 2, 180, 360));
		$this->addLeftSystem(new Hangar(4, 3));
		$this->addLeftSystem(new CargoBay(4, 36));

		$this->addRightSystem(new NexusCIDS(4, 4, 2, 0, 180));
		$this->addRightSystem(new NexusCIDS(4, 4, 2, 0, 180));
		$this->addRightSystem(new NexusMedSentryGun(4, 6, 2, 0, 180));
		$this->addRightSystem(new NexusMedSentryGun(4, 6, 2, 0, 180));
		$this->addRightSystem(new Hangar(4, 3));
		$this->addRightSystem(new CargoBay(4, 36));

		$this->addFrontSystem(new Structure( 4, 64));
		$this->addAftSystem(new Structure( 4, 64));
		$this->addLeftSystem(new Structure( 4, 64));
		$this->addRightSystem(new Structure( 4, 64));
		$this->addPrimarySystem(new Structure( 4, 80));
		
		$this->hitChart = array(			
			0=> array(
				7 => "Structure",
				9 => "Magazine",
				13 => "Cargo Bay",
				14 => "Hangar",
				16 => "Scanner",
				18 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				4 => "Cargo Bay",
				6 => "Close-In Defense System",
				8 => "Medium Sentry Gun",
				10 => "Hangar",
				18 => "Structure",
				20 => "Primary",
				
			),
			2=> array(
				4 => "Cargo Bay",
				6 => "Close-In Defense System",
				8 => "Medium Sentry Gun",
				10 => "Hangar",
				18 => "Structure",
				20 => "Primary",
			),	
			3=> array(
				4 => "Cargo Bay",
				6 => "Close-In Defense System",
				8 => "Medium Sentry Gun",
				10 => "Hangar",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				4 => "Cargo Bay",
				6 => "Close-In Defense System",
				8 => "Medium Sentry Gun",
				10 => "Hangar",
				18 => "Structure",
				20 => "Primary",
			),
		);

    	}
   	}
?>