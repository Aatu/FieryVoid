<?php
class DalithornWarStation extends SmallStarBaseFourSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 950;
		$this->base = true;
		$this->smallBase = true;
		$this->faction = "Nexus Dalithorn Commonwealth";
		$this->phpclass = "DalithornWarStation";
		$this->shipClass = "War Station";
		$this->imagePath = "img/ships/Nexus/Dalithorn_Station2.png";
		$this->canvasSize = 140; 
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
		//$this->addFrontSystem(new Catapult(4, 6));
		//$this->addFrontSystem(new CargoBay(4, 12));

			$cargoBay = new CargoBay(4, 12);
			$cargoBay->startArc = 270;
			$cargoBay->endArc = 90;
			$this->addFrontSystem($cargoBay);
			$catapult = new Catapult(4, 6);
			$catapult->startArc = 270;
			$catapult->endArc = 90;
			$this->addFrontSystem($catapult);
					
		$this->addAftSystem(new NexusHeavyCoilgun(4, 12, 5, 120, 240));
		$this->addAftSystem(new NexusGasGun(4, 7, 2, 90, 270));
		$this->addAftSystem(new NexusHeavyLaserMissile(4, 6, 3, 90, 270));
		$this->addAftSystem(new NexusAutocannon(4, 4, 1, 90, 270));
		$this->addAftSystem(new NexusAutocannon(4, 4, 1, 90, 270));
		$this->addAftSystem(new NexusMinigun(4, 4, 1, 90, 270));
		//$this->addAftSystem(new Catapult(4, 6));
		//$this->addAftSystem(new CargoBay(4, 12));

			$cargoBay = new CargoBay(4, 12);
			$cargoBay->startArc = 90;
			$cargoBay->endArc = 270;
			$this->addAftSystem($cargoBay);
			$catapult = new Catapult(4, 6);
			$catapult->startArc = 90;
			$catapult->endArc = 270;
			$this->addAftSystem($catapult);
					
		$this->addLeftSystem(new NexusHeavyCoilgun(4, 12, 5, 210, 330));
		$this->addLeftSystem(new NexusGasGun(4, 7, 2, 180, 360));
		$this->addLeftSystem(new NexusHeavyLaserMissile(4, 6, 3, 180, 360));
		$this->addLeftSystem(new NexusAutocannon(4, 4, 1, 180, 360));
		$this->addLeftSystem(new NexusAutocannon(4, 4, 1, 180, 360));
		$this->addLeftSystem(new NexusMinigun(4, 4, 1, 180, 360));
		//$this->addLeftSystem(new Catapult(4, 6));
		//$this->addLeftSystem(new CargoBay(4, 12));

			$cargoBay = new CargoBay(4, 12);
			$cargoBay->startArc = 180;
			$cargoBay->endArc = 360;
			$this->addLeftSystem($cargoBay);
			$catapult = new Catapult(4, 6);
			$catapult->startArc = 180;
			$catapult->endArc = 360;
			$this->addLeftSystem($catapult);
		
		$this->addRightSystem(new NexusHeavyCoilgun(4, 12, 5, 30, 150));
		$this->addRightSystem(new NexusGasGun(4, 7, 2, 0, 180));
		$this->addRightSystem(new NexusHeavyLaserMissile(4, 6, 3, 0, 180));
		$this->addRightSystem(new NexusAutocannon(4, 4, 1, 0, 180));
		$this->addRightSystem(new NexusAutocannon(4, 4, 1, 0, 180));
		$this->addRightSystem(new NexusMinigun(4, 4, 1, 0, 180));
		//$this->addRightSystem(new Catapult(4, 6));
		//$this->addRightSystem(new CargoBay(4, 12));

			$cargoBay = new CargoBay(4, 12);
			$cargoBay->startArc = 0;
			$cargoBay->endArc = 180;
			$this->addRightSystem($cargoBay);
			$catapult = new Catapult(4, 6);
			$catapult->startArc = 0;
			$catapult->endArc = 180;
			$this->addRightSystem($catapult);
		
		/*replaced by TAGed versions!
		$this->addFrontSystem(new Structure( 4, 64));
		$this->addAftSystem(new Structure( 4, 64));
		$this->addLeftSystem(new Structure( 4, 64));
		$this->addRightSystem(new Structure( 4, 64));
		$this->addPrimarySystem(new Structure( 4, 80));
		*/
		$this->addPrimarySystem(new Structure( 4, 80));//needs to be called first for some reason - static call apparently fails for the first time...
		$this->addFrontSystem(Structure::createAsOuter(4, 64, 270,90));
		$this->addAftSystem(Structure::createAsOuter(4, 64, 90, 270));
		$this->addLeftSystem(Structure::createAsOuter(4, 64, 180, 360));
		$this->addRightSystem(Structure::createAsOuter(4, 64, 0, 180));	
		
		$this->hitChart = array(			
			0=> array(
				8 => "Structure",
				10 => "Magazine",
				13 => "Protector",
				15 => "Hangar",
				17 => "Scanner",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				2 => "TAG:Heavy Coilgun",
				4 => "TAG:Autocannon",
				5 => "TAG:Catapult",
				7 => "TAG:Gas Gun",
				9 => "TAG:Heavy Laser Missile",
				10 => "TAG:Minigun",
				12 => "TAG:Cargo Bay",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				2 => "TAG:Heavy Coilgun",
				4 => "TAG:Autocannon",
				5 => "TAG:Catapult",
				7 => "TAG:Gas Gun",
				9 => "TAG:Heavy Laser Missile",
				10 => "TAG:Minigun",
				12 => "TAG:Cargo Bay",
				18 => "Structure",
				20 => "Primary",
			),	
			3=> array(
				2 => "TAG:Heavy Coilgun",
				4 => "TAG:Autocannon",
				5 => "TAG:Catapult",
				7 => "TAG:Gas Gun",
				9 => "TAG:Heavy Laser Missile",
				10 => "TAG:Minigun",
				12 => "TAG:Cargo Bay",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				2 => "TAG:Heavy Coilgun",
				4 => "TAG:Autocannon",
				5 => "TAG:Catapult",
				7 => "TAG:Gas Gun",
				9 => "TAG:Heavy Laser Missile",
				10 => "TAG:Minigun",
				12 => "TAG:Cargo Bay",
				18 => "Structure",
				20 => "Primary",
			),
		);

    	}
   	}
?>