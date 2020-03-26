<?php
class TorataDartocCA extends BaseShip{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 650;
		$this->faction = "Torata";
		$this->phpclass = "TorataDartocCA";
		$this->imagePath = "img/ships/TorataDartoc.png";
		$this->canvasSize = 200;
		$this->shipClass = "Dartoc Strike Cruiser";
		$this->shipSizeClass = 3;
		$this->variantOf = "Golthar Fast Cruiser";
		$this->occurence = "uncommon";
		$this->fighters = array("heavy"=>12);
		$this->isd = 2260;

		$this->forwardDefense = 17;
		$this->sideDefense = 15;

		$this->turncost = 1;
		$this->turndelaycost = 0.66;
		$this->accelcost = 2;
		$this->rollcost = 3;
		$this->pivotcost = 3;
		$this->iniativebonus = 0;

		$this->addPrimarySystem(new Reactor(5, 18, 0, 0));
		$this->addPrimarySystem(new CnC(5, 16, 0, 0));
		$this->addPrimarySystem(new Scanner(5, 18, 6, 8));
		$this->addPrimarySystem(new Engine(5, 20, 0, 12, 3));
		$this->addPrimarySystem(new JumpEngine(5, 16, 4, 27));
		$this->addPrimarySystem(new Hangar(5, 2, 0, 0));
		
		$this->addFrontSystem(new LaserAccelerator(4, 7, 6, 300, 60));
		$this->addFrontSystem(new PentagonArray(3, 8, 5, 270, 90));
		$this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
		$this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
		$this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
		
		$this->addAftSystem(new PentagonArray(3, 8, 5, 90, 270));
		$this->addAftSystem(new Thruster(4, 10, 0, 3, 2));
		$this->addAftSystem(new Thruster(4, 10, 0, 3, 2));
		$this->addAftSystem(new Thruster(4, 10, 0, 3, 2));
		$this->addAftSystem(new Thruster(4, 10, 0, 3, 2));

		$this->addLeftSystem(new LaserAccelerator(4, 7, 6, 270, 60));
		$this->addLeftSystem(new Hangar(4, 6, 0, 0)); //for a flight of fighters
		$this->addLeftSystem(new Thruster(3, 15, 0, 6, 3));

		$this->addRightSystem(new LaserAccelerator(4, 7, 6, 300, 90));
		$this->addRightSystem(new Hangar(4, 6, 0, 0));//for a flight of fighters
		$this->addRightSystem(new Thruster(3, 15, 0, 6, 4));

		//0:primary, 1:front, 2:rear, 3:left, 4:right;
		$this->addFrontSystem(new Structure(4, 48));
		$this->addAftSystem(new Structure(4, 48));
		$this->addLeftSystem(new Structure(5, 48));
		$this->addRightSystem(new Structure(5, 48));
		$this->addPrimarySystem(new Structure(5, 50));

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
				6 => "Thruster",
				8 => "Laser Accelerator",
				10 => "PentagonArray",
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
				4 => "Thruster",
				6 => "Laser Accelerator",
				8 => "Hangar",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				4 => "Thruster",
				6 => "Laser Accelerator",
				8 => "Hangar",
				18 => "Structure",
				20 => "Primary",
			),
		);
	}
}



?>