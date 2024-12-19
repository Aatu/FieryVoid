<?php
class MakarSraeloonRefit extends SmallStarBaseFourSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 400;
		$this->base = true;
		$this->smallBase = true;
		$this->faction = "ZNexus Makar Federation";
		$this->phpclass = "MakarSraeloonRefit";
		$this->shipClass = "Sraeloon Station (1963)";
			$this->variantOf = "Sraeloon Station";
			$this->occurence = "common";
		$this->imagePath = "img/ships/Nexus/makar_base.png";
		$this->canvasSize = 140; 
		$this->unofficial = true;
		$this->isd = 1963;

		$this->shipSizeClass = 3; 
		$this->Enormous = false;
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

//		$this->fighters = array("superheavy"=>4);

		$this->forwardDefense = 18;
		$this->sideDefense = 18;

		$this->addPrimarySystem(new Reactor(4, 35, 0, 0));
		$this->addPrimarySystem(new CnC(4, 24, 0, 0));
		$this->addPrimarySystem(new Scanner(4, 14, 5, 7));
		$this->addPrimarySystem(new Hangar(4, 6));
		$this->addPrimarySystem(new CargoBay(4, 15));
		$this->addPrimarySystem(new CargoBay(4, 30));
		$this->addPrimarySystem(new NexusWaterCaster(4, 4, 1, 0, 360));
		$this->addPrimarySystem(new NexusWaterCaster(4, 4, 1, 0, 360));
		$this->addPrimarySystem(new NexusLightChargeCannon(4, 4, 1, 0, 360));
		$this->addPrimarySystem(new NexusLightChargeCannon(4, 4, 1, 0, 360));
		
		$this->addFrontSystem(new NexusDefenseGun(4, 4, 1, 270, 90));
		$this->addFrontSystem(new EWRangedHeavyRocketLauncher(4, 6, 2, 270, 90));
		$this->addFrontSystem(new Hangar(4, 1));
		$this->addFrontSystem(new CargoBay(4, 36));

		$this->addAftSystem(new NexusDefenseGun(4, 4, 1, 90, 270));
		$this->addAftSystem(new EWRangedHeavyRocketLauncher(4, 6, 2, 90, 270));
		$this->addAftSystem(new Hangar(4, 1));
		$this->addAftSystem(new CargoBay(4, 36));

		$this->addLeftSystem(new NexusDefenseGun(4, 4, 1, 180, 360));
		$this->addLeftSystem(new EWRangedHeavyRocketLauncher(4, 6, 2, 180, 360));
		$this->addLeftSystem(new Hangar(4, 1));
		$this->addLeftSystem(new CargoBay(4, 36));

		$this->addRightSystem(new NexusDefenseGun(4, 4, 1, 0, 180));
		$this->addRightSystem(new EWRangedHeavyRocketLauncher(4, 6, 2, 0, 180));
		$this->addRightSystem(new Hangar(4, 1));
		$this->addRightSystem(new CargoBay(4, 36));

		$this->addFrontSystem(new Structure( 4, 64));
		$this->addAftSystem(new Structure( 4, 64));
		$this->addLeftSystem(new Structure( 4, 64));
		$this->addRightSystem(new Structure( 4, 64));
		$this->addPrimarySystem(new Structure( 4, 98));
		
		$this->hitChart = array(			
			0=> array(
				8 => "Structure",
				9 => "Light Charge Cannon",
				10 => "Water Caster",
				14 => "Cargo Bay",
				15 => "Hangar",
				17 => "Scanner",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				2 => "Ranged Heavy Rocket Launcher",
				4 => "Defense Gun",
				5 => "Hangar",
				9 => "Cargo Bay",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				2 => "Ranged Heavy Rocket Launcher",
				4 => "Defense Gun",
				5 => "Hangar",
				9 => "Cargo Bay",
				18 => "Structure",
				20 => "Primary",
			),	
			3=> array(
				2 => "Ranged Heavy Rocket Launcher",
				4 => "Defense Gun",
				5 => "Hangar",
				9 => "Cargo Bay",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				2 => "Ranged Heavy Rocket Launcher",
				4 => "Defense Gun",
				5 => "Hangar",
				9 => "Cargo Bay",
				18 => "Structure",
				20 => "Primary",
			),
		);

    	}
   	}
?>