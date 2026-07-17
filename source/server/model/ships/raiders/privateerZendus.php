<?php
class PrivateerZendus extends BaseShip{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 450;
		$this->faction = "Raiders";
		$this->phpclass = "PrivateerZendus";
		$this->shipClass = "Torata Privateer Zendus Cruiser";
		$this->variantOf = "Raider Zendus Cruiser";
		$this->occurence = "common";
		$this->imagePath = "img/ships/TorataPrivateerZendusNoPods.png";
		$this->canvasSize = 200;
		$this->shipSizeClass = 3;
		$this->isd = 2240;
		$this->fighters = array("cargo shuttles"=>2); 		
		$this->limited = 10; 
		
		$this->notes = "Used only by Torata privateers.";
		$this->notes .= "<br> ";
		$this->notes .= "<br>More detailed deployment restrictions are in the Faction List document.";
		$this->notes .= "<br> ";

		
		$this->unofficial = true;
		
		$this->forwardDefense = 17;
		$this->sideDefense = 18;

		$this->turncost = 1.33;
		$this->turndelaycost = 1;
		$this->accelcost = 3;
		$this->rollcost = 4;
		$this->pivotcost = 4;
		$this->iniativebonus = 0;

		$this->addPrimarySystem(new Reactor(4, 17, 0, 0));
		$this->addPrimarySystem(new CnC(4, 16, 0, 0));
		$this->addPrimarySystem(new Scanner(4, 16, 5, 6));
		$this->addPrimarySystem(new Engine(4, 18, 0, 10, 3));
		$this->addPrimarySystem(new JumpEngine(4, 12, 4, 30));
		$this->addPrimarySystem(new Hangar(2, 2, 1));
		
		$this->addFrontSystem(new PlasmaAccelerator(4, 10, 5, 300, 60));
		$this->addFrontSystem(new MediumPlasma(3, 5, 3, 240, 60));
		$this->addFrontSystem(new MediumPlasma(3, 5, 3, 300, 120));
		$this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
		$this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
		
		$this->addAftSystem(new MediumPlasma(3, 5, 3, 120, 300));
		$this->addAftSystem(new MediumPlasma(3, 5, 3, 60, 240));
		$this->addAftSystem(new Thruster(4, 12, 0, 5, 2));
		$this->addAftSystem(new Thruster(4, 12, 0, 5, 2));

		$this->addLeftSystem(new Thruster(4, 15, 0, 4, 3));

		$this->addRightSystem(new Thruster(4, 15, 0, 4, 4));

		//0:primary, 1:front, 2:rear, 3:left, 4:right;
		$this->addFrontSystem(new Structure(4, 3));
		$this->addAftSystem(new Structure(4, 32));
		$this->addLeftSystem(new Structure(4, 40));
		$this->addRightSystem(new Structure(4, 40));
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
				8 => "Medium Laser Cannon",
				11 => "Medium Plasma Cannon",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				6 => "Thruster",
				8 => "Medium Plasma Cannon",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				5 => "Thruster",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				5 => "Thruster",
				18 => "Structure",
				20 => "Primary",
			),
		);
	}
}



?>