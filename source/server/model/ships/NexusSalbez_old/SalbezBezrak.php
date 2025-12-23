<?php
class SalbezBezrak extends SmallStarBaseFourSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 650;
		$this->base = true;
		$this->smallBase = true;
		$this->faction = "Nexus Sal-bez Coalition (early)";
		$this->phpclass = "SalbezBezrak";
		$this->shipClass = "Bez'rak Armed Station";
		$this->imagePath = "img/ships/Nexus/salbez_combatBase3.png";
		$this->canvasSize = 140; 
		$this->unofficial = true;
		$this->isd = 2081;

		$this->shipSizeClass = 3; 
		$this->Enormous = false;
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->fighters = array("normal"=>12);

		$this->forwardDefense = 17;
		$this->sideDefense = 17;

		$this->addPrimarySystem(new Reactor(4, 23, 0, 0));
		$this->addPrimarySystem(new CnC(4, 20, 0, 0));
		$this->addPrimarySystem(new Scanner(4, 18, 6, 6));
		$this->addPrimarySystem(new Hangar(4, 6));
		$this->addPrimarySystem(new CargoBay(4, 10));
		$this->addPrimarySystem(new LightParticleBeamShip(4, 2, 1, 0, 360));
		$this->addPrimarySystem(new LightParticleBeamShip(4, 2, 1, 0, 360));
		
		$this->addFrontSystem(new LaserCutter(3, 6, 4, 270, 90));
		$this->addFrontSystem(new LaserCutter(3, 6, 4, 270, 90));
		$this->addFrontSystem(new LightParticleBeamShip(3, 2, 1, 270, 90));
		$this->addFrontSystem(new LightParticleBeamShip(3, 2, 1, 270, 90));
		$this->addFrontSystem(new NexusRangedBoltTorpedo(3, 5, 2, 270, 90));
		//$this->addFrontSystem(new Hangar(3, 3));
		//$this->addFrontSystem(new CargoBay(3, 10));

			$cargoBay = new CargoBay(4, 10);
			$cargoBay->startArc = 270;
			$cargoBay->endArc = 90;
			$this->addFrontSystem($cargoBay);
			$hangar = new Hangar(3, 3);
			$hangar->startArc = 270;
			$hangar->endArc = 90;
			$this->addFrontSystem($hangar);
					
		$this->addAftSystem(new LaserCutter(3, 6, 4, 90, 270));
		$this->addAftSystem(new LaserCutter(3, 6, 4, 90, 270));
		$this->addAftSystem(new LightParticleBeamShip(3, 2, 1, 90, 270));
		$this->addAftSystem(new LightParticleBeamShip(3, 2, 1, 90, 270));
		$this->addAftSystem(new NexusRangedBoltTorpedo(3, 5, 2, 90, 270));
		//$this->addAftSystem(new Hangar(3, 3));
		//$this->addAftSystem(new CargoBay(3, 10));

			$cargoBay = new CargoBay(4, 10);
			$cargoBay->startArc = 90;
			$cargoBay->endArc = 270;
			$this->addAftSystem($cargoBay);
			$hangar = new Hangar(3, 3);
			$hangar->startArc = 90;
			$hangar->endArc = 270;
			$this->addAftSystem($hangar);
								
		$this->addLeftSystem(new LaserCutter(3, 6, 4, 180, 360));
		$this->addLeftSystem(new LaserCutter(3, 6, 4, 180, 360));
		$this->addLeftSystem(new LightParticleBeamShip(3, 2, 1, 180, 360));
		$this->addLeftSystem(new LightParticleBeamShip(3, 2, 1, 180, 360));
		$this->addLeftSystem(new NexusRangedBoltTorpedo(3, 5, 2, 180, 360));
		//$this->addLeftSystem(new Hangar(3, 3));
		//$this->addLeftSystem(new CargoBay(3, 10));

			$cargoBay = new CargoBay(4, 10);
			$cargoBay->startArc = 180;
			$cargoBay->endArc = 360;
			$this->addLeftSystem($cargoBay);
			$hangar = new Hangar(3, 3);
			$hangar->startArc = 180;
			$hangar->endArc = 360;
			$this->addLeftSystem($hangar);
		
		$this->addRightSystem(new LaserCutter(3, 6, 4, 0, 180));
		$this->addRightSystem(new LaserCutter(3, 6, 4, 0, 180));
		$this->addRightSystem(new LightParticleBeamShip(3, 2, 1, 0, 180));
		$this->addRightSystem(new LightParticleBeamShip(3, 2, 1, 0, 180));
		$this->addRightSystem(new NexusRangedBoltTorpedo(3, 5, 2, 0, 180));
		//$this->addRightSystem(new Hangar(3, 3));
		//$this->addRightSystem(new CargoBay(3, 10));

			$cargoBay = new CargoBay(4, 10);
			$cargoBay->startArc = 0;
			$cargoBay->endArc = 180;
			$this->addRightSystem($cargoBay);
			$hangar = new Hangar(3, 3);
			$hangar->startArc = 0;
			$hangar->endArc = 180;
			$this->addRightSystem($hangar);
		
		/*replaced by TAGed versions!		
		$this->addFrontSystem(new Structure( 3, 60));
		$this->addAftSystem(new Structure( 3, 60));
		$this->addLeftSystem(new Structure( 3, 60));
		$this->addRightSystem(new Structure( 3, 60));
		$this->addPrimarySystem(new Structure( 4, 98));
		*/
		$this->addPrimarySystem(new Structure( 4, 98));//needs to be called first for some reason - static call apparently fails for the first time...
		$this->addFrontSystem(Structure::createAsOuter(3, 60, 270,90));
		$this->addAftSystem(Structure::createAsOuter(3, 60, 90, 270));
		$this->addLeftSystem(Structure::createAsOuter(3, 60, 180, 360));
		$this->addRightSystem(Structure::createAsOuter(3, 60, 0, 180));	
				
		$this->hitChart = array(			
			0=> array(
				10 => "Structure",
				11 => "Cargo Bay",
				12 => "Light Particle Beam",
				14 => "Hangar",
				16 => "Scanner",
				18 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				2 => "TAG:Laser Cutter",
				4 => "TAG:Light Particle Beam",
				5 => "TAG:Ranged Bolt Torpedo",
				7 => "TAG:Cargo Bay",
				9 => "TAG:Hangar",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				2 => "TAG:Laser Cutter",
				4 => "TAG:Light Particle Beam",
				5 => "TAG:Ranged Bolt Torpedo",
				7 => "TAG:Cargo Bay",
				9 => "TAG:Hangar",
				18 => "Structure",
				20 => "Primary",
			),	
			3=> array(
				2 => "TAG:Laser Cutter",
				4 => "TAG:Light Particle Beam",
				5 => "TAG:Ranged Bolt Torpedo",
				7 => "TAG:Cargo Bay",
				9 => "TAG:Hangar",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				2 => "TAG:Laser Cutter",
				4 => "TAG:Light Particle Beam",
				5 => "TAG:Ranged Bolt Torpedo",
				7 => "TAG:Cargo Bay",
				9 => "TAG:Hangar",
				18 => "Structure",
				20 => "Primary",
			),
		);

    	}
   	}
?>