<?php
class SalbezBevram extends SmallStarBaseFourSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 300;
		$this->base = true;
		$this->smallBase = true;
		$this->faction = "Nexus Sal-bez Coalition (early)";
		$this->phpclass = "SalbezBevram";
		$this->shipClass = "Bev'ram Mining Center";
		$this->imagePath = "img/ships/Nexus/salbez_civilianBase3.png";
		$this->canvasSize = 140; 
		$this->unofficial = true;
		$this->isd = 2021;

		$this->shipSizeClass = 3; 
		$this->Enormous = false;
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->fighters = array("normal"=>6);

		$this->forwardDefense = 17;
		$this->sideDefense = 17;

   		$this->enhancementOptionsEnabled[] = 'ELT_MRN'; //To enable Elite Marines enhancement
		$this->enhancementOptionsEnabled[] = 'EXT_MRN'; //To enable extra Marines enhancement

		$this->addPrimarySystem(new Reactor(3, 17, 0, 0));
		$this->addPrimarySystem(new CnC(3, 10, 0, 0));
		$this->addPrimarySystem(new Scanner(3, 10, 5, 5));
		$this->addPrimarySystem(new Hangar(3, 12));
		$this->addPrimarySystem(new CargoBay(3, 40));
		
		$this->addFrontSystem(new NexusIndustrialLaser(3, 6, 3, 270, 90));
		$this->addFrontSystem(new NexusIndustrialLaser(3, 6, 3, 270, 90));
		$this->addFrontSystem(new NexusParticleGrid(3, 3, 1, 270, 90));
		$this->addFrontSystem(new NexusParticleGrid(3, 3, 1, 270, 90));
		$this->addFrontSystem(new GrapplingClaw(3, 0, 0, 270, 90, 8, false));
		//$this->addFrontSystem(new CargoBay(3, 30));

			$cargoBay = new CargoBay(4, 30);
			$cargoBay->startArc = 270;
			$cargoBay->endArc = 90;
			$this->addFrontSystem($cargoBay);

		$this->addAftSystem(new NexusIndustrialLaser(3, 6, 3, 90, 270));
		$this->addAftSystem(new NexusIndustrialLaser(3, 6, 3, 90, 270));
		$this->addAftSystem(new NexusParticleGrid(3, 3, 1, 90, 270));
		$this->addAftSystem(new NexusParticleGrid(3, 3, 1, 90, 270));
		$this->addAftSystem(new GrapplingClaw(3, 0, 0, 90, 270, 8, false));
		//$this->addAftSystem(new CargoBay(3, 30));

			$cargoBay = new CargoBay(4, 30);
			$cargoBay->startArc = 90;
			$cargoBay->endArc = 270;
			$this->addAftSystem($cargoBay);

		$this->addLeftSystem(new NexusIndustrialLaser(3, 6, 3, 180, 360));
		$this->addLeftSystem(new NexusIndustrialLaser(3, 6, 3, 180, 360));
		$this->addLeftSystem(new NexusParticleGrid(3, 3, 1, 180, 360));
		$this->addLeftSystem(new NexusParticleGrid(3, 3, 1, 180, 360));
		$this->addLeftSystem(new GrapplingClaw(3, 0, 0, 180, 360, 8, false));
		//$this->addLeftSystem(new CargoBay(3, 30));

			$cargoBay = new CargoBay(4, 30);
			$cargoBay->startArc = 180;
			$cargoBay->endArc = 360;
			$this->addLeftSystem($cargoBay);

		$this->addRightSystem(new NexusIndustrialLaser(3, 6, 3, 0, 180));
		$this->addRightSystem(new NexusIndustrialLaser(3, 6, 3, 0, 180));
		$this->addRightSystem(new NexusParticleGrid(3, 3, 1, 0, 180));
		$this->addRightSystem(new NexusParticleGrid(3, 3, 1, 0, 180));
		$this->addRightSystem(new GrapplingClaw(3, 0, 0, 0, 180, 8, false));
		//$this->addRightSystem(new CargoBay(3, 30));

			$cargoBay = new CargoBay(4, 30);
			$cargoBay->startArc = 0;
			$cargoBay->endArc = 180;
			$this->addRightSystem($cargoBay);
		
		/*replaced by TAGed versions!		
		$this->addFrontSystem(new Structure( 3, 60));
		$this->addAftSystem(new Structure( 3, 60));
		$this->addLeftSystem(new Structure( 3, 60));
		$this->addRightSystem(new Structure( 3, 60));
		$this->addPrimarySystem(new Structure( 3, 98));
		*/
		$this->addPrimarySystem(new Structure( 3, 98));//needs to be called first for some reason - static call apparently fails for the first time...
		$this->addFrontSystem(Structure::createAsOuter(3, 60, 270,90));
		$this->addAftSystem(Structure::createAsOuter(3, 60, 90, 270));
		$this->addLeftSystem(Structure::createAsOuter(3, 60, 180, 360));
		$this->addRightSystem(Structure::createAsOuter(3, 60, 0, 180));	
						
		$this->hitChart = array(			
			0=> array(
				10 => "Structure",
				12 => "Cargo Bay",
				14 => "Hangar",
				16 => "Scanner",
				18 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				3 => "TAG:Industrial Laser",
				5 => "TAG:Particle Grid",
				7 => "TAG:Grappling Claw",
				10 => "TAG:Cargo Bay",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				3 => "TAG:Industrial Laser",
				5 => "TAG:Particle Grid",
				7 => "TAG:Grappling Claw",
				10 => "TAG:Cargo Bay",
				18 => "Structure",
				20 => "Primary",
			),	
			3=> array(
				3 => "TAG:Industrial Laser",
				5 => "TAG:Particle Grid",
				7 => "TAG:Grappling Claw",
				10 => "TAG:Cargo Bay",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				3 => "TAG:Industrial Laser",
				5 => "TAG:Particle Grid",
				7 => "TAG:Grappling Claw",
				10 => "TAG:Cargo Bay",
				18 => "Structure",
				20 => "Primary",
			),
		);

    	}
   	}
?>