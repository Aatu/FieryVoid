<?php
class MakarSraeloonRefit2 extends SmallStarBaseFourSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 550;
		$this->base = true;
		$this->smallBase = true;
		$this->faction = "ZNexus Makar Federation";
		$this->phpclass = "MakarSraeloonRefit2";
		$this->shipClass = "Sraeloon Station (2108)";
		$this->imagePath = "img/ships/Nexus/makar_base.png";
		$this->canvasSize = 140; 
		$this->unofficial = true;
		$this->isd = 2108;

		$this->shipSizeClass = 3; 
		$this->Enormous = false;
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->fighters = array("normal"=>24);

		$this->forwardDefense = 18;
		$this->sideDefense = 18;

		$this->addPrimarySystem(new Reactor(4, 35, 0, 0));
		$this->addPrimarySystem(new CnC(4, 24, 0, 0));
		$this->addPrimarySystem(new Scanner(4, 14, 5, 8));
		$this->addPrimarySystem(new Hangar(4, 6));
		$this->addPrimarySystem(new HKControlNode(4, 10, 3, 4));
		$this->addPrimarySystem(new NexusPlasmaCharge(4, 7, 4, 0, 360));
		$this->addPrimarySystem(new NexusWaterCaster(4, 4, 1, 0, 360));
		$this->addPrimarySystem(new NexusWaterCaster(4, 4, 1, 0, 360));
		$this->addPrimarySystem(new NexusLightChargeCannon(4, 4, 1, 0, 360));
		$this->addPrimarySystem(new NexusLightChargeCannon(4, 4, 1, 0, 360));
		
		$this->addFrontSystem(new NexusLightXRayLaser(4, 3, 1, 270, 90));
		$this->addFrontSystem(new EWRangedDualHeavyRocketLauncher(4, 8, 4, 270, 90));
		$this->addFrontSystem(new Hangar(4, 1));
		$this->addFrontSystem(new CargoBay(4, 26));

		$this->addAftSystem(new NexusLightXRayLaser(4, 3, 1, 90, 270));
		$this->addAftSystem(new EWRangedDualHeavyRocketLauncher(4, 8, 4, 90, 270));
		$this->addAftSystem(new Hangar(4, 1));
		$this->addAftSystem(new CargoBay(4, 26));

		$this->addLeftSystem(new NexusLightXRayLaser(4, 3, 1, 180, 360));
		$this->addLeftSystem(new EWRangedDualHeavyRocketLauncher(4, 8, 4, 180, 360));
		$this->addLeftSystem(new Hangar(4, 1));
		$this->addLeftSystem(new CargoBay(4, 26));

		$this->addRightSystem(new NexusLightXRayLaser(4, 3, 1, 0, 180));
		$this->addRightSystem(new EWRangedDualHeavyRocketLauncher(4, 8, 4, 0, 180));
		$this->addRightSystem(new Hangar(4, 1));
		$this->addRightSystem(new CargoBay(4, 26));

		$this->addFrontSystem(new Structure( 4, 70));
		$this->addAftSystem(new Structure( 4, 70));
		$this->addLeftSystem(new Structure( 4, 70));
		$this->addRightSystem(new Structure( 4, 70));
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
				2 => "Ranged Dual Heavy Rocket Launcher",
				4 => "Light X-Ray Laser",
				5 => "Hangar",
				9 => "Cargo Bay",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				2 => "Ranged Dual Heavy Rocket Launcher",
				4 => "Light X-Ray Laser",
				5 => "Hangar",
				9 => "Cargo Bay",
				18 => "Structure",
				20 => "Primary",
			),	
			3=> array(
				2 => "Ranged Dual Heavy Rocket Launcher",
				4 => "Light X-Ray Laser",
				5 => "Hangar",
				9 => "Cargo Bay",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				2 => "Ranged Dual Heavy Rocket Launcher",
				4 => "Light X-Ray Laser",
				5 => "Hangar",
				9 => "Cargo Bay",
				18 => "Structure",
				20 => "Primary",
			),
		);

    	}
   	}
?>