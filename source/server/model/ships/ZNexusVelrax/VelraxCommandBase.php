<?php
class VelraxCommandBase extends SmallStarBaseFourSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 660;
		$this->base = true;
		$this->smallBase = true;
		$this->faction = "ZNexus Velrax";
		$this->phpclass = "VelraxCommandBase";
		$this->shipClass = "Command Base";
		$this->imagePath = "img/ships/Nexus/VelraxBase.png";
		$this->canvasSize = 175; 
		$this->unofficial = true;
		$this->isd = 2061;

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
		$this->addPrimarySystem(new Scanner(4, 16, 6, 6));
		$this->addPrimarySystem(new Hangar(4, 6));
		$this->addPrimarySystem(new CargoBay(4, 21));
		$this->addPrimarySystem(new NexusDartInterceptor(4, 4, 1, 0, 360));
		$this->addPrimarySystem(new NexusDartInterceptor(4, 4, 1, 0, 360));
		$this->addPrimarySystem(new NexusDartInterceptor(4, 4, 1, 0, 360));
		
		$this->addFrontSystem(new NexusHeavyLaserSpear(3, 6, 4, 270, 90));
		$this->addFrontSystem(new NexusRangedPlasmaWave(3, 7, 4, 270, 90));
		$this->addFrontSystem(new NexusTwinIonGun(3, 4, 4, 270, 90));
		$this->addFrontSystem(new NexusTwinIonGun(3, 4, 4, 270, 90));
		$this->addFrontSystem(new Hangar(3, 9));
		$this->addFrontSystem(new CargoBay(3, 24));

		$this->addAftSystem(new NexusHeavyLaserSpear(3, 6, 4, 90, 270));
		$this->addAftSystem(new NexusRangedPlasmaWave(3, 7, 4, 90, 270));
		$this->addAftSystem(new NexusTwinIonGun(3, 4, 4, 90, 270));
		$this->addAftSystem(new NexusTwinIonGun(3, 4, 4, 90, 270));
		$this->addAftSystem(new Hangar(3, 6));
		$this->addAftSystem(new CargoBay(3, 24));
			
		$this->addLeftSystem(new NexusHeavyLaserSpear(3, 6, 4, 180, 360));
		$this->addLeftSystem(new NexusRangedPlasmaWave(3, 7, 4, 180, 360));
		$this->addLeftSystem(new NexusTwinIonGun(3, 4, 4, 180, 360));
		$this->addLeftSystem(new NexusTwinIonGun(3, 4, 4, 180, 360));
		$this->addLeftSystem(new Hangar(3, 6));
		$this->addLeftSystem(new CargoBay(3, 24));

		$this->addRightSystem(new NexusHeavyLaserSpear(3, 6, 4, 0, 180));
		$this->addRightSystem(new NexusRangedPlasmaWave(3, 7, 4, 0, 180));
		$this->addRightSystem(new NexusTwinIonGun(3, 4, 4, 0, 180));
		$this->addRightSystem(new NexusTwinIonGun(3, 4, 4, 0, 180));
		$this->addRightSystem(new Hangar(3, 6));
		$this->addRightSystem(new CargoBay(3, 24));

		$this->addFrontSystem(new Structure( 3, 70));
		$this->addAftSystem(new Structure( 3, 70));
		$this->addLeftSystem(new Structure( 3, 70));
		$this->addRightSystem(new Structure( 3, 70));
		$this->addPrimarySystem(new Structure( 4, 86));
		
		$this->hitChart = array(			
			0=> array(
				9 => "Structure",
				11 => "Dart Interceptor",
				12 => "Hangar",
				15 => "Cargo Bay",
				17 => "Scanner",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				2 => "Heavy Laser Spear",
				4 => "Twin Ion Gun",
				5 => "Hangar",
				9 => "Cargo Bay",
				11 => "Ranged Plasma Wave",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				2 => "Heavy Laser Spear",
				4 => "Twin Ion Gun",
				5 => "Hangar",
				9 => "Cargo Bay",
				11 => "Ranged Plasma Wave",
				18 => "Structure",
				20 => "Primary",
			),	
			3=> array(
				2 => "Heavy Laser Spear",
				4 => "Twin Ion Gun",
				5 => "Hangar",
				9 => "Cargo Bay",
				11 => "Ranged Plasma Wave",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				2 => "Heavy Laser Spear",
				4 => "Twin Ion Gun",
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