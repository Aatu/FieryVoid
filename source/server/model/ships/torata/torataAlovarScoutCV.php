<?php
class TorataAlovarScoutCV extends BaseShip{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 675;
		$this->faction = "Torata";
		$this->phpclass = "TorataAlovarScoutCV";
		$this->imagePath = "img/ships/TorataClovant.png";
		$this->shipClass = "Alovar Scout Carrier";
		$this->limited = 33;
		$this->canvasSize = 200;
		$this->shipSizeClass = 3;
		$this->variantOf = "Clovant Medium Scout";
		$this->occurence = "common";
		
		$this->fighters = array("heavy"=>12);
		$this->isd = 2255;
		
		

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
		$this->addPrimarySystem(new Hangar(5, 14, 0, 0));
		
		$this->addFrontSystem(new PentagonArray(4, 0, 0, 270, 90));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 240, 60));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 240, 60));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 300, 120));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 300, 120));
		$this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
		$this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
		
		$this->addAftSystem(new PentagonArray(3, 0, 0, 90, 270));
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
				7 => "Pentagon Array",
				12 => "Light Particle Beam",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				6 => "Thruster",
				8 => "Pentagon Array",
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