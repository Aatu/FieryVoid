<?php
class TorataClovantScout extends BaseShip{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 650;
		$this->faction = "Torata";
		$this->phpclass = "TorataClovantScout";
		$this->imagePath = "img/ships/TorataClovant.png";
		$this->canvasSize = 200;
		$this->shipClass = "Clovant Medium Scout";
		$this->limited = 33; //Limited Deployment
		
		$this->shipSizeClass = 3;
		$this->fighters = array("heavy"=>6);
		$this->isd = 2224;
		
		
		$this->notes = "Rare after 2256 (converted to Alovar).";

		$this->forwardDefense = 15;
		$this->sideDefense = 16;

		$this->turncost = 1;
		$this->turndelaycost = 1;
		$this->accelcost = 3;
		$this->rollcost = 3;
		$this->pivotcost = 3;
		$this->iniativebonus = 0;

		$this->addPrimarySystem(new Reactor(5, 17, 0, 0));
		$this->addPrimarySystem(new CnC(5, 16, 0, 0));
		$this->addPrimarySystem(new ElintScanner(5, 24, 6, 10));
		$this->addPrimarySystem(new Engine(5, 20, 0, 9, 3));
		$this->addPrimarySystem(new JumpEngine(5, 16, 4, 27));
		$this->addPrimarySystem(new Hangar(5, 8, 0, 0));
		
		$this->addFrontSystem(new ParticleAccelerator(4, 0, 0, 300, 60));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 240, 60));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 240, 60));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 300, 120));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 300, 120));
		$this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
		$this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
		
		$this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 90, 270));
		$this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 90, 270));
		$this->addAftSystem(new Thruster(4, 10, 0, 3, 2));
		$this->addAftSystem(new Thruster(4, 10, 0, 3, 2));
		$this->addAftSystem(new Thruster(4, 10, 0, 3, 2));

		$this->addLeftSystem(new Thruster(4, 15, 0, 5, 3));

		$this->addRightSystem(new Thruster(4, 15, 0, 5, 4));

		//0:primary, 1:front, 2:rear, 3:left, 4:right;
		$this->addFrontSystem(new Structure(4, 48));
		$this->addAftSystem(new Structure(4, 44));
		$this->addLeftSystem(new Structure(5, 44));
		$this->addRightSystem(new Structure(5, 44));
		$this->addPrimarySystem(new Structure(5, 44));

		$this->hitChart = array(
			0=> array(
				10 => "Structure",
				12 => "Jump Engine",
				14 => "Scanner",
				16 => "Engine",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				5 => "Thruster",
				7 => "Particle Accelerator",
				12 => "Light Particle Beam",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				6 => "Thruster",
				8 => "Light Particle Beam",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				6 => "Thruster",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				6 => "Thruster",
				18 => "Structure",
				20 => "Primary",
			),
		);
	}
}



?>