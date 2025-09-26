<?php
class VelraxLogisticsBase extends SmallStarBaseFourSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 525;
		$this->base = true;
		$this->smallBase = true;
		$this->faction = "ZNexus Velrax Republic (early)";
		$this->phpclass = "VelraxLogisticsBase";
		$this->shipClass = "Logistics Base";
		$this->imagePath = "img/ships/Nexus/velraxBase.png";
		$this->canvasSize = 140; 
		$this->unofficial = true;
		$this->isd = 2033;

		$this->shipSizeClass = 3; 
		$this->Enormous = false;
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->fighters = array("normal"=>12);

		$this->forwardDefense = 18;
		$this->sideDefense = 18;

		$this->addPrimarySystem(new Reactor(3, 35, 0, 0));
		$this->addPrimarySystem(new CnC(3, 16, 0, 0));
		$this->addPrimarySystem(new Scanner(3, 16, 5, 5));
		$this->addPrimarySystem(new Hangar(3, 6));
		$this->addPrimarySystem(new CargoBay(3, 39));
		$this->addPrimarySystem(new NexusDartInterceptor(3, 4, 1, 0, 360));
		$this->addPrimarySystem(new NexusDartInterceptor(3, 4, 1, 0, 360));
		$this->addPrimarySystem(new NexusDartInterceptor(3, 4, 1, 0, 360));
		
		$this->addFrontSystem(new NexusLaserSpear(3, 5, 3, 270, 90));
		$this->addFrontSystem(new NexusRangedPlasmaWave(3, 7, 4, 270, 90));
		$this->addFrontSystem(new NexusTwinIonGun(3, 4, 4, 270, 90));
		$this->addFrontSystem(new NexusTwinIonGun(3, 4, 4, 270, 90));
		//$this->addFrontSystem(new Hangar(3, 6));
		//$this->addFrontSystem(new CargoBay(3, 36));

			$cargoBay = new CargoBay(3, 36);
			$cargoBay->startArc = 270;
			$cargoBay->endArc = 90;
			$this->addFrontSystem($cargoBay);
			$hangar = new Hangar(3, 6);
			$hangar->startArc = 270;
			$hangar->endArc = 90;
			$this->addFrontSystem($hangar);

		$this->addAftSystem(new NexusLaserSpear(3, 5, 3, 90, 270));
		$this->addAftSystem(new NexusRangedPlasmaWave(3, 7, 4, 90, 270));
		$this->addAftSystem(new NexusTwinIonGun(3, 4, 4, 90, 270));
		$this->addAftSystem(new NexusTwinIonGun(3, 4, 4, 90, 270));
		//$this->addAftSystem(new Hangar(3, 6));
		//$this->addAftSystem(new CargoBay(3, 36));
					
			$cargoBay = new CargoBay(3, 36);
			$cargoBay->startArc = 90;
			$cargoBay->endArc = 270;
			$this->addAftSystem($cargoBay);
			$hangar = new Hangar(3, 6);
			$hangar->startArc = 90;
			$hangar->endArc = 270;
			$this->addAftSystem($hangar);
			
		$this->addLeftSystem(new NexusLaserSpear(3, 5, 3, 180, 360));
		$this->addLeftSystem(new NexusRangedPlasmaWave(3, 7, 4, 180, 360));
		$this->addLeftSystem(new NexusTwinIonGun(3, 4, 4, 180, 360));
		$this->addLeftSystem(new NexusTwinIonGun(3, 4, 4, 180, 360));
		//$this->addLeftSystem(new Hangar(3, 6));
		//$this->addLeftSystem(new CargoBay(3, 36));
						
			$cargoBay = new CargoBay(3, 36);
			$cargoBay->startArc = 180;
			$cargoBay->endArc = 360;
			$this->addLeftSystem($cargoBay);
			$hangar = new Hangar(3, 6);
			$hangar->startArc = 180;
			$hangar->endArc = 360;
			$this->addLeftSystem($hangar);
					
		$this->addRightSystem(new NexusLaserSpear(3, 5, 3, 0, 180));
		$this->addRightSystem(new NexusRangedPlasmaWave(3, 7, 4, 0, 180));
		$this->addRightSystem(new NexusTwinIonGun(3, 4, 4, 0, 180));
		$this->addRightSystem(new NexusTwinIonGun(3, 4, 4, 0, 180));
		//$this->addRightSystem(new Hangar(3, 6));
		//$this->addRightSystem(new CargoBay(3, 36));

			$cargoBay = new CargoBay(3, 36);
			$cargoBay->startArc = 0;
			$cargoBay->endArc = 180;
			$this->addRightSystem($cargoBay);
			$hangar = new Hangar(3, 6);
			$hangar->startArc = 0;
			$hangar->endArc = 180;
			$this->addRightSystem($hangar);
				
		/*replaced by TAGed versions!		
		$this->addFrontSystem(new Structure( 3, 70));
		$this->addAftSystem(new Structure( 3, 70));
		$this->addLeftSystem(new Structure( 3, 70));
		$this->addRightSystem(new Structure( 3, 70));
		$this->addPrimarySystem(new Structure( 3, 86));
		*/
		$this->addPrimarySystem(new Structure( 3, 86));//needs to be called first for some reason - static call apparently fails for the first time...
		$this->addFrontSystem(Structure::createAsOuter(3, 70, 270,90));
		$this->addAftSystem(Structure::createAsOuter(3, 70, 90, 270));
		$this->addLeftSystem(Structure::createAsOuter(3, 70, 180, 360));
		$this->addRightSystem(Structure::createAsOuter(3, 70, 0, 180));	
											
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
				2 => "TAG:Laser Spear",
				4 => "TAG:Twin Ion Gun",
				5 => "TAG:Hangar",
				9 => "TAG:Cargo Bay",
				11 => "TAG:Ranged Plasma Wave",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				2 => "TAG:Laser Spear",
				4 => "TAG:Twin Ion Gun",
				5 => "TAG:Hangar",
				9 => "TAG:Cargo Bay",
				11 => "TAG:Ranged Plasma Wave",
				18 => "Structure",
				20 => "Primary",
			),	
			3=> array(
				2 => "TAG:Laser Spear",
				4 => "TAG:Twin Ion Gun",
				5 => "TAG:Hangar",
				9 => "TAG:Cargo Bay",
				11 => "TAG:Ranged Plasma Wave",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				2 => "TAG:Laser Spear",
				4 => "TAG:Twin Ion Gun",
				5 => "TAG:Hangar",
				9 => "TAG:Cargo Bay",
				11 => "TAG:Ranged Plasma Wave",
				18 => "Structure",
				20 => "Primary",
			),
		);

    	}
   	}
?>