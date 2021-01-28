<?php
class CircasianMukantaSmallBase extends SmallStarBaseFourSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 400;
		$this->base = true;
		$this->smallBase = true;
		$this->faction = "ZEscalation Circasian";
		$this->phpclass = "CircasianMukantaSmallBase";
		$this->shipClass = "Mukanta Small Base";
		$this->imagePath = "img/ships/EscalationWars/CircasianMukantaSmallBase.png";
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
		$this->addPrimarySystem(new Hangar(3, 6));
		$this->addPrimarySystem(new CargoBay(3, 20));

		$this->addFrontSystem(new EWRangedDualRocketLauncher(2, 6, 2, 270, 90));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 270, 90));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 270, 90));
		$this->addFrontSystem(new Hangar(2, 1));
		$this->addFrontSystem(new CargoBay(2, 30));

		$this->addAftSystem(new EWRangedDualRocketLauncher(2, 6, 2, 90, 270));
		$this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 90, 270));
		$this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 90, 270));
		$this->addAftSystem(new Hangar(2, 1));
		$this->addAftSystem(new CargoBay(2, 30));
		
		$this->addLeftSystem(new EWRangedDualRocketLauncher(2, 6, 2, 180, 360));
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
		$this->addLeftSystem(new Hangar(2, 1));
		$this->addLeftSystem(new CargoBay(2, 30));
		
		$this->addRightSystem(new EWRangedDualRocketLauncher(2, 6, 2, 0, 180));
		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
		$this->addRightSystem(new Hangar(2, 1));
		$this->addRightSystem(new CargoBay(2, 30));


		$this->addFrontSystem(new Structure( 2, 50));
		$this->addAftSystem(new Structure( 2, 50));
		$this->addLeftSystem(new Structure( 2, 50));
		$this->addRightSystem(new Structure( 2, 50));
		$this->addPrimarySystem(new Structure( 3, 64));
		
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
				1 => "Light Particle Beam",
				2 => "Ranged Dual Rocket Launcher",
				7 => "Cargo Bay",
				8 => "Hangar",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				1 => "Light Particle Beam",
				2 => "Ranged Dual Rocket Launcher",
				7 => "Cargo Bay",
				8 => "Hangar",
				18 => "Structure",
				20 => "Primary",
			),	
			3=> array(
				1 => "Light Particle Beam",
				2 => "Ranged Dual Rocket Launcher",
				7 => "Cargo Bay",
				8 => "Hangar",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				1 => "Light Particle Beam",
				2 => "Ranged Dual Rocket Launcher",
				7 => "Cargo Bay",
				8 => "Hangar",
				18 => "Structure",
				20 => "Primary",
			),
		);



		
		}
    }
?>
