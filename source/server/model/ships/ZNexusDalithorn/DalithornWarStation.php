<?php
class DalithornWarStation extends SmallStarBaseFourSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 800;
		$this->base = true;
		$this->smallBase = true;
		$this->faction = "ZNexus Dalithorn Commonwealth";
		$this->phpclass = "DalithornWarStation";
		$this->shipClass = "War Station";
		$this->imagePath = "img/ships/Nexus/DalithornStation.png";
		$this->canvasSize = 175; 
		$this->unofficial = true;
		$this->isd = 2106;

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
		$this->addPrimarySystem(new Scanner(4, 14, 5, 7));
		$this->addPrimarySystem(new Hangar(4, 6));
		$this->addPrimarySystem(new Magazine(4, 24));
		$this->addPrimarySystem(new NexusProtector(4, 4, 1, 0, 360));
		$this->addPrimarySystem(new NexusProtector(4, 4, 1, 0, 360));
		$this->addPrimarySystem(new NexusProtector(4, 4, 1, 0, 360));
		$this->addPrimarySystem(new NexusProtector(4, 4, 1, 0, 360));
		
		$this->addFrontSystem(new NexusHeavyCoilgun(4, 12, 5, 300, 60));
		$this->addFrontSystem(new NexusGasGun(4, 7, 2, 270, 90));
		$this->addFrontSystem(new NexusHeavyLaserMissile(4, 6, 3, 270, 90));
		$this->addFrontSystem(new NexusAutocannon(4, 4, 1, 270, 90));
		$this->addFrontSystem(new NexusAutocannon(4, 4, 1, 270, 90));
		$this->addFrontSystem(new NexusMinigun(4, 4, 1, 270, 90));
		$this->addFrontSystem(new Catapult(4, 6));
		$this->addFrontSystem(new CargoBay(4, 12));

		$this->addAftSystem(new NexusHeavyCoilgun(4, 12, 5, 120, 240));
		$this->addAftSystem(new NexusGasGun(4, 7, 2, 90, 270));
		$this->addAftSystem(new NexusHeavyLaserMissile(4, 6, 3, 90, 270));
		$this->addAftSystem(new NexusAutocannon(4, 4, 1, 90, 270));
		$this->addAftSystem(new NexusAutocannon(4, 4, 1, 90, 270));
		$this->addAftSystem(new NexusMinigun(4, 4, 1, 90, 270));
		$this->addAftSystem(new Catapult(4, 6));
		$this->addAftSystem(new CargoBay(4, 12));
			
		$this->addLeftSystem(new NexusHeavyCoilgun(4, 12, 5, 210, 330));
		$this->addLeftSystem(new NexusGasGun(4, 7, 2, 180, 360));
		$this->addLeftSystem(new NexusHeavyLaserMissile(4, 6, 3, 180, 360));
		$this->addLeftSystem(new NexusAutocannon(4, 4, 1, 180, 360));
		$this->addLeftSystem(new NexusAutocannon(4, 4, 1, 180, 360));
		$this->addLeftSystem(new NexusMinigun(4, 4, 1, 180, 360));
		$this->addLeftSystem(new Catapult(4, 6));
		$this->addLeftSystem(new CargoBay(4, 12));

		$this->addRightSystem(new NexusHeavyCoilgun(4, 12, 5, 30, 150));
		$this->addRightSystem(new NexusGasGun(4, 7, 2, 0, 180));
		$this->addRightSystem(new NexusHeavyLaserMissile(4, 6, 3, 0, 180));
		$this->addRightSystem(new NexusAutocannon(4, 4, 1, 0, 180));
		$this->addRightSystem(new NexusAutocannon(4, 4, 1, 0, 180));
		$this->addRightSystem(new NexusMinigun(4, 4, 1, 0, 180));
		$this->addRightSystem(new Catapult(4, 6));
		$this->addRightSystem(new CargoBay(4, 12));

		$this->addFrontSystem(new Structure( 4, 64));
		$this->addAftSystem(new Structure( 4, 64));
		$this->addLeftSystem(new Structure( 4, 64));
		$this->addRightSystem(new Structure( 4, 64));
		$this->addPrimarySystem(new Structure( 4, 80));
		
		$this->hitChart = array(			
			0=> array(
				8 => "Structure",
				10 => "Magazine",
				12 => "Protector",
				14 => "Hangar",
				16 => "Scanner",
				18 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				3 => "Heavy Coilgun",
				5 => "Autocannon",
				7 => "Catapult",
				9 => "Gas Gun",
				11 => "Heavy Laser Missile",
				13 => "Minigun",
				15 => "Cargo Bay",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				3 => "Heavy Coilgun",
				5 => "Autocannon",
				7 => "Catapult",
				9 => "Gas Gun",
				11 => "Heavy Laser Missile",
				13 => "Minigun",
				15 => "Cargo Bay",
				18 => "Structure",
				20 => "Primary",
			),	
			3=> array(
				3 => "Heavy Coilgun",
				5 => "Autocannon",
				7 => "Catapult",
				9 => "Gas Gun",
				11 => "Heavy Laser Missile",
				13 => "Minigun",
				15 => "Cargo Bay",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				3 => "Heavy Coilgun",
				5 => "Autocannon",
				7 => "Catapult",
				9 => "Gas Gun",
				11 => "Heavy Laser Missile",
				13 => "Minigun",
				15 => "Cargo Bay",
				18 => "Structure",
				20 => "Primary",
			),
		);

    	}
   	}
?>