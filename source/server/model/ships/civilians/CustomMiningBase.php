<?php
class CustomMiningBase extends SmallStarBaseFourSections{
	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);
		$this->pointCost = 250;
		$this->faction = "Civilians";
		$this->phpclass = "CustomMiningBase";
		$this->shipClass = "Mining Base";
		$this->imagePath = "img/ships/miningBase.png";
		$this->canvasSize = 280; 
		
		$this->base = true;
		$this->smallBase = true;
		$this->nonRotating = true;  //completely immobile, doesn't even rotate
		
		//$this->fighters = array("heavy"=>6); 
		$this->shipSizeClass = 3; 
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;
		$this->forwardDefense = 18;
		$this->sideDefense = 18;
		$this->unofficial = true;
		$this->isd = 2000;
		
		
		/*replaced by TAGed versions!
		$this->addFrontSystem(new Structure( 2, 58));
		$this->addAftSystem(new Structure( 2, 58));
		$this->addLeftSystem(new Structure( 2, 58));
		$this->addRightSystem(new Structure( 2, 58));
		*/
		$this->addPrimarySystem(new Structure( 4, 44));
		$this->addFrontSystem(Structure::createAsOuter(2, 58, 270,90));
		$this->addAftSystem(Structure::createAsOuter(2, 58, 90, 270));
		$this->addLeftSystem(Structure::createAsOuter(2, 58, 180, 360));
		$this->addRightSystem(Structure::createAsOuter(2, 58, 0, 180));
		
		$this->addPrimarySystem(new Reactor(4, 16, 0, 0));
		$this->addPrimarySystem(new CnC(4, 15, 0, 0)); 
		$this->addPrimarySystem(new Scanner(4, 12, 3, 4));
		$this->addPrimarySystem(new Hangar(4, 6));
		$this->addPrimarySystem(new CargoBay(4, 42));
		
		$this->addFrontSystem(new CustomMiningCutter(2, 0, 0, 270, 90));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 270, 90));
		$this->addFrontSystem(new CustomIndustrialGrappler(2, 0, 0, 270, 90));
		$this->addFrontSystem(new CustomIndustrialGrappler(2, 0, 0, 270, 90));
		$this->addFrontSystem(new CustomIndustrialGrappler(2, 0, 0, 270, 90));
		/*
		$this->addFrontSystem(new Hangar(2, 6));
		$this->addFrontSystem(new CargoBay(2, 18));
		*/
			$hangar = new Hangar(2, 6);
			$hangar->startArc = 270;
			$hangar->endArc = 90;
			$this->addFrontSystem($hangar);
			$cargoBay = new CargoBay(2, 25);
			$cargoBay->startArc = 270;
			$cargoBay->endArc = 90;
			$this->addFrontSystem($cargoBay);
		
		
		
		$this->addAftSystem(new CustomMiningCutter(2, 0, 0, 90,270));
		$this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 90,270));
		$this->addAftSystem(new CustomIndustrialGrappler(2, 0, 0, 90,270));
		$this->addAftSystem(new CustomIndustrialGrappler(2, 0, 0, 90,270));
		$this->addAftSystem(new CustomIndustrialGrappler(2, 0, 0, 90,270));
		/*
		$this->addAftSystem(new Hangar(2, 6));
		$this->addAftSystem(new CargoBay(2, 18));		
		*/
			$hangar = new Hangar(2, 6);
			$hangar->startArc = 90;
			$hangar->endArc = 270;
			$this->addAftSystem($hangar);
			$cargoBay = new CargoBay(2, 25);
			$cargoBay->startArc = 90;
			$cargoBay->endArc = 270;
			$this->addAftSystem($cargoBay);
		
		$this->addRightSystem(new CustomMiningCutter(2, 0, 0, 0, 180));
		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
		$this->addRightSystem(new CustomIndustrialGrappler(2, 0, 0, 0, 180));
		$this->addRightSystem(new CustomIndustrialGrappler(2, 0, 0, 0, 180));
		$this->addRightSystem(new CustomIndustrialGrappler(2, 0, 0, 0, 180));
		/*
		$this->addRightSystem(new Hangar(2, 6));
		$this->addRightSystem(new CargoBay(2, 18));
		*/
			$hangar = new Hangar(2, 6);
			$hangar->startArc = 0;
			$hangar->endArc = 180;
			$this->addRightSystem($hangar);
			$cargoBay = new CargoBay(2, 25);
			$cargoBay->startArc = 0;
			$cargoBay->endArc = 180;
			$this->addRightSystem($cargoBay);

		$this->addLeftSystem(new CustomMiningCutter(2, 0, 0, 180, 0));
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 0));
		$this->addLeftSystem(new CustomIndustrialGrappler(2, 0, 0, 180, 0));
		$this->addLeftSystem(new CustomIndustrialGrappler(2, 0, 0, 180, 0));
		$this->addLeftSystem(new CustomIndustrialGrappler(2, 0, 0, 180, 0));
		/*
		$this->addLeftSystem(new Hangar(2, 6));
		$this->addLeftSystem(new CargoBay(2, 18));
		*/
			$hangar = new Hangar(2, 6);
			$hangar->startArc = 180;
			$hangar->endArc = 0;
			$this->addLeftSystem($hangar);
			$cargoBay = new CargoBay(2, 25);
			$cargoBay->startArc = 180;
			$cargoBay->endArc = 0;
			$this->addLeftSystem($cargoBay);
				
		
		$this->hitChart = array(			
			0=> array(
				9 => "Structure",
				12 => "Cargo Bay",
				14 => "Hangar",
				16 => "Scanner",
				18 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				1 => "TAG:Light Particle Beam",
				3 => "TAG:Mining Cutter",
				6 => "TAG:Industrial Grappler",
				8 => "TAG:Cargo Bay",
				10 => "TAG:Hangar",
				18 => "Outer Structure",
				20 => "Primary",
			),
			2=> array(
				1 => "TAG:Light Particle Beam",
				3 => "TAG:Mining Cutter",
				6 => "TAG:Industrial Grappler",
				8 => "TAG:Cargo Bay",
				10 => "TAG:Hangar",
				18 => "Outer Structure",
				20 => "Primary",
			),	
			3=> array(
				1 => "TAG:Light Particle Beam",
				3 => "TAG:Mining Cutter",
				6 => "TAG:Industrial Grappler",
				8 => "TAG:Cargo Bay",
				10 => "TAG:Hangar",
				18 => "Outer Structure",
				20 => "Primary",
			),
			4=> array(
				1 => "TAG:Light Particle Beam",
				3 => "TAG:Mining Cutter",
				6 => "TAG:Industrial Grappler",
				8 => "TAG:Cargo Bay",
				10 => "TAG:Hangar",
				18 => "Outer Structure",
				20 => "Primary",
			),
		);
		
	}
    }
?>
