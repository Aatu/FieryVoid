<?php
class CircasianMukantaSmallBase extends SmallStarBaseFourSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 400;
		$this->base = true;
		$this->smallBase = true;
		$this->faction = "ZEscalation Circasian Empire";
		$this->phpclass = "CircasianMukantaSmallBase";
		$this->shipClass = "Mukanta Small Base";
		$this->imagePath = "img/ships/EscalationWars/CircasianMukanta.png";
		$this->fighters = array("light"=>6); 


		$this->shipSizeClass = 3; 
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->forwardDefense = 18;
		$this->sideDefense = 18;


		$this->canvasSize = 200; 
		$this->unofficial = true;
		

	$this->isd = 1927;

		$this->addPrimarySystem(new Reactor(3, 15, 0, 0));
		$this->addPrimarySystem(new CnC(3, 15, 0, 0)); 
		$this->addPrimarySystem(new Scanner(3, 12, 3, 5));
		//$this->addPrimarySystem(new Hangar(3, 6));
		//$this->addPrimarySystem(new CargoBay(3, 20));

		$this->addFrontSystem(new EWRangedDualRocketLauncher(2, 6, 2, 270, 90));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 270, 90));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 270, 90));
		//$this->addFrontSystem(new Hangar(2, 1));
		//$this->addFrontSystem(new CargoBay(2, 30));
		
			$cargoBay = new CargoBay(2, 30);
			$cargoBay->startArc = 270;
			$cargoBay->endArc = 90;
			$this->addFrontSystem($cargoBay);
			$hangar = new Hangar(2, 1);
			$hangar->startArc = 270;
			$hangar->endArc = 90;
			$this->addFrontSystem($hangar);
						
		$this->addAftSystem(new EWRangedDualRocketLauncher(2, 6, 2, 90, 270));
		$this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 90, 270));
		$this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 90, 270));
		//$this->addAftSystem(new Hangar(2, 1));
		//$this->addAftSystem(new CargoBay(2, 30));
		
			$cargoBay = new CargoBay(2, 30);
			$cargoBay->startArc = 90;
			$cargoBay->endArc = 270;
			$this->addAftSystem($cargoBay);
			$hangar = new Hangar(2, 1);
			$hangar->startArc = 90;
			$hangar->endArc = 270;
			$this->addAftSystem($hangar);
						
		$this->addLeftSystem(new EWRangedDualRocketLauncher(2, 6, 2, 180, 360));
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
		//$this->addLeftSystem(new Hangar(2, 1));
		//$this->addLeftSystem(new CargoBay(2, 30));
		
			$cargoBay = new CargoBay(2, 30);
			$cargoBay->startArc = 180;
			$cargoBay->endArc = 360;
			$this->addLeftSystem($cargoBay);
			$hangar = new Hangar(2, 1);
			$hangar->startArc = 180;
			$hangar->endArc = 360;
			$this->addLeftSystem($hangar);
				
		$this->addRightSystem(new EWRangedDualRocketLauncher(2, 6, 2, 0, 180));
		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
		//$this->addRightSystem(new Hangar(2, 1));
		//$this->addRightSystem(new CargoBay(2, 30));
		
			$cargoBay = new CargoBay(2, 30);
			$cargoBay->startArc = 0;
			$cargoBay->endArc = 180;
			$this->addRightSystem($cargoBay);
			$hangar = new Hangar(2, 1);
			$hangar->startArc = 0;
			$hangar->endArc = 180;
			$this->addRightSystem($hangar);
		
		/*replaced by TAGed versions!
		$this->addFrontSystem(new Structure( 2, 50));
		$this->addAftSystem(new Structure( 2, 50));
		$this->addLeftSystem(new Structure( 2, 50));
		$this->addRightSystem(new Structure( 2, 50));
		$this->addPrimarySystem(new Structure( 3, 64));
		*/
		$this->addPrimarySystem(new Structure( 3, 64));//needs to be called first for some reason - static call apparently fails for the first time...
		$this->addFrontSystem(Structure::createAsOuter(2, 50, 270,90));
		$this->addAftSystem(Structure::createAsOuter(2, 50, 90, 270));
		$this->addLeftSystem(Structure::createAsOuter(2, 50, 180, 360));
		$this->addRightSystem(Structure::createAsOuter(2, 50, 0, 180));

		$this->hitChart = array(			
			0=> array(
				10 => "Structure",
				13 => "Cargo Bay",
				14 => "Scanner",
				16 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				1 => "TAG:Light Particle Beam",
				2 => "TAG:Ranged Dual Rocket Launcher",
				7 => "TAG:Cargo Bay",
				8 => "TAG:Hangar",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				1 => "TAG:Light Particle Beam",
				2 => "TAG:Ranged Dual Rocket Launcher",
				7 => "TAG:Cargo Bay",
				8 => "TAG:Hangar",
				18 => "Structure",
				20 => "Primary",
			),	
			3=> array(
				1 => "TAG:Light Particle Beam",
				2 => "TAG:Ranged Dual Rocket Launcher",
				7 => "TAG:Cargo Bay",
				8 => "TAG:Hangar",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				1 => "TAG:Light Particle Beam",
				2 => "TAG:Ranged Dual Rocket Launcher",
				7 => "TAG:Cargo Bay",
				8 => "TAG:Hangar",
				18 => "Structure",
				20 => "Primary",
			),
		);



		
		}
    }
?>
