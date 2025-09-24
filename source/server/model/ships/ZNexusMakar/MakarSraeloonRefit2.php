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
		$this->addPrimarySystem(new HKControlNode(4, 15, 3, 4));
		$this->addPrimarySystem(new NexusPlasmaCharge(4, 7, 4, 0, 360));
		$this->addPrimarySystem(new NexusWaterCaster(4, 4, 1, 0, 360));
		$this->addPrimarySystem(new NexusWaterCaster(4, 4, 1, 0, 360));
		$this->addPrimarySystem(new NexusLightChargeCannon(4, 4, 1, 0, 360));
		$this->addPrimarySystem(new NexusLightChargeCannon(4, 4, 1, 0, 360));
		
		$this->addFrontSystem(new NexusLightXRayLaser(4, 3, 1, 270, 90));
		$this->addFrontSystem(new EWRangedDualHeavyRocketLauncher(4, 8, 4, 270, 90));
		//$this->addFrontSystem(new Hangar(4, 1));
		//$this->addFrontSystem(new CargoBay(4, 26));

			$cargoBay = new CargoBay(4, 26);
			$cargoBay->startArc = 270;
			$cargoBay->endArc = 90;
			$this->addFrontSystem($cargoBay);
			$hangar = new Hangar(4, 1);
			$hangar->startArc = 270;
			$hangar->endArc = 90;
			$this->addFrontSystem($hangar);
					
		$this->addAftSystem(new NexusLightXRayLaser(4, 3, 1, 90, 270));
		$this->addAftSystem(new EWRangedDualHeavyRocketLauncher(4, 8, 4, 90, 270));
		//$this->addAftSystem(new Hangar(4, 1));
		//$this->addAftSystem(new CargoBay(4, 26));

			$cargoBay = new CargoBay(4, 26);
			$cargoBay->startArc = 90;
			$cargoBay->endArc = 270;
			$this->addAftSystem($cargoBay);
			$hangar = new Hangar(4, 1);
			$hangar->startArc = 90;
			$hangar->endArc = 270;
			$this->addAftSystem($hangar);
					
		$this->addLeftSystem(new NexusLightXRayLaser(4, 3, 1, 180, 360));
		$this->addLeftSystem(new EWRangedDualHeavyRocketLauncher(4, 8, 4, 180, 360));
		//$this->addLeftSystem(new Hangar(4, 1));
		//$this->addLeftSystem(new CargoBay(4, 26));

			$cargoBay = new CargoBay(4, 26);
			$cargoBay->startArc = 180;
			$cargoBay->endArc = 360;
			$this->addLeftSystem($cargoBay);
			$hangar = new Hangar(4, 1);
			$hangar->startArc = 180;
			$hangar->endArc = 360;
			$this->addLeftSystem($hangar);
		
		$this->addRightSystem(new NexusLightXRayLaser(4, 3, 1, 0, 180));
		$this->addRightSystem(new EWRangedDualHeavyRocketLauncher(4, 8, 4, 0, 180));
		//$this->addRightSystem(new Hangar(4, 1));
		//$this->addRightSystem(new CargoBay(4, 26));

			$cargoBay = new CargoBay(4, 26);
			$cargoBay->startArc = 0;
			$cargoBay->endArc = 180;
			$this->addRightSystem($cargoBay);
			$hangar = new Hangar(4, 1);
			$hangar->startArc = 0;
			$hangar->endArc = 180;
			$this->addRightSystem($hangar);
		
		/*replaced by TAGed versions!		
		$this->addFrontSystem(new Structure( 4, 70));
		$this->addAftSystem(new Structure( 4, 70));
		$this->addLeftSystem(new Structure( 4, 70));
		$this->addRightSystem(new Structure( 4, 70));
		$this->addPrimarySystem(new Structure( 4, 98));
		*/
		$this->addPrimarySystem(new Structure( 4, 98));//needs to be called first for some reason - static call apparently fails for the first time...
		$this->addFrontSystem(Structure::createAsOuter(4, 70, 270,90));
		$this->addAftSystem(Structure::createAsOuter(4, 70, 90, 270));
		$this->addLeftSystem(Structure::createAsOuter(4, 70, 180, 360));
		$this->addRightSystem(Structure::createAsOuter(4, 70, 0, 180));	
		
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
				2 => "TAG:Ranged Dual Heavy Rocket Launcher",
				4 => "TAG:Light X-Ray Laser",
				5 => "TAG:Hangar",
				9 => "TAG:Cargo Bay",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				2 => "TAG:Ranged Dual Heavy Rocket Launcher",
				4 => "TAG:Light X-Ray Laser",
				5 => "TAG:Hangar",
				9 => "TAG:Cargo Bay",
				18 => "Structure",
				20 => "Primary",
			),	
			3=> array(
				2 => "TAG:Ranged Dual Heavy Rocket Launcher",
				4 => "TAG:Light X-Ray Laser",
				5 => "TAG:Hangar",
				9 => "TAG:Cargo Bay",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				2 => "TAG:Ranged Dual Heavy Rocket Launcher",
				4 => "TAG:Light X-Ray Laser",
				5 => "TAG:Hangar",
				9 => "TAG:Cargo Bay",
				18 => "Structure",
				20 => "Primary",
			),
		);

    	}
   	}
?>