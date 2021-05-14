<?php
class TorataHeltakaCLogPods extends BaseShip{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 600;
		$this->faction = "Torata";
		$this->phpclass = "TorataHeltakaCLogPods";
		$this->imagePath = "img/ships/TorataHeltakaNOPODS.png";
		$this->shipClass = "Heltaka Logistics Cruiser (w/Cargo Pods)";
		$this->variantOf = "Heltaka Logistics Cruiser";
		$this->canvasSize = 200;
		$this->shipSizeClass = 3;
		$this->isd = 2256;

		
		$this->notes = "Logistics ship, but Torata do use it in combat fleets.";
		
		$this->forwardDefense = 17;
		$this->sideDefense = 18;

		$this->turncost = 1.33;
		$this->turndelaycost = 1;
		$this->accelcost = 4;
		$this->rollcost = 4;
		$this->pivotcost = 4;
		$this->iniativebonus = 0;

		$this->addPrimarySystem(new Reactor(4, 17, 0, 0));
		$this->addPrimarySystem(new CnC(4, 16, 0, 0));
		$this->addPrimarySystem(new Scanner(4, 16, 5, 7));
		$this->addPrimarySystem(new Engine(4, 20, 0, 12, 3));
		$this->addPrimarySystem(new JumpEngine(4, 16, 4, 27));
		$this->addPrimarySystem(new Hangar(4, 4, 0, 0));
		
		$this->addFrontSystem(new PlasmaAccelerator(4, 10, 5, 300, 60));
		$this->addFrontSystem(new PentagonArray(3, 8, 5, 240, 60));
		$this->addFrontSystem(new PentagonArray(3, 8, 5, 300, 120));
		$this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
		$this->addFrontSystem(new Thruster(4, 7, 0, 2, 1));
		$this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
		
		$this->addAftSystem(new PentagonArray(3, 8, 5, 120, 300));
		$this->addAftSystem(new PentagonArray(3, 8, 5, 60, 240));
		$this->addAftSystem(new Thruster(4, 14, 0, 6, 2));
		$this->addAftSystem(new Thruster(4, 14, 0, 6, 2));

		$pod = new CargoBay(2, 40);
		$pod->displayName = "Cargo Pod";
		$this->addLeftSystem($pod);
		$this->addLeftSystem(new Thruster(4, 15, 0, 6, 3));

		$pod = new CargoBay(2, 40);
		$pod->displayName = "Cargo Pod";
		$this->addRightSystem($pod);
		$this->addRightSystem(new Thruster(4, 15, 0, 6, 4));

		//0:primary, 1:front, 2:rear, 3:left, 4:right;
		$this->addFrontSystem(new Structure(4, 40));
		$this->addAftSystem(new Structure(4, 40));
		$this->addLeftSystem(new Structure(4, 48));
		$this->addRightSystem(new Structure(4, 48));
		$this->addPrimarySystem(new Structure(4, 56));

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
				8 => "Plasma Accelerator",
				11 => "Pentagon Array",
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
				5 => "Thruster",
				10 => "Cargo Pod",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				5 => "Thruster",
				10 => "Cargo Pod",
				18 => "Structure",
				20 => "Primary",
			),
		);
	}
}



?>