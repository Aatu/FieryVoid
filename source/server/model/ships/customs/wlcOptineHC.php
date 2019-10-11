<?php
class wlcOptineHC extends BaseShip{
    
	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 785;
		$this->faction = "Centauri (WotCR)";
		$this->phpclass = "wlcOptineHC";
		$this->imagePath = "img/ships/optine.png";
		$this->shipClass = "Optine House Cruiser";
		$this->shipSizeClass = 3;
		$this->forwardDefense = 16;
		$this->sideDefense = 18;

		$this->variantOf = 'Optine Battlecruiser';
		$this->unofficial = true;
		$this->fighters = array("light"=>12);        

		$this->turncost = 1;
		$this->turndelaycost = 1;
		$this->accelcost = 4;
		$this->rollcost = 3;
		$this->pivotcost = 4;

		$this->addPrimarySystem(new Reactor(6, 25, 0, 0));
		$this->addPrimarySystem(new CnC(6, 16, 0, 0));
		$this->addPrimarySystem(new Scanner(6, 23, 4, 8));
		$this->addPrimarySystem(new Engine(5, 20, 0, 10, 3));
		$this->addPrimarySystem(new Hangar(4, 2));

		$this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));
		$this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));
		$this->addFrontSystem(new Hangar(4, 6));
		$this->addFrontSystem(new AssaultLaser(3, 6, 4, 300, 60));
		$this->addFrontSystem(new ImperialLaser(3, 8, 5, 300, 60));
		$this->addFrontSystem(new ImperialLaser(3, 8, 5, 300, 60));
		$this->addFrontSystem(new TwinArray(3, 6, 2, 240, 60));
		$this->addFrontSystem(new TwinArray(3, 6, 2, 300, 120));

		$this->addAftSystem(new Thruster(4, 10, 0, 3, 2));
		$this->addAftSystem(new Thruster(4, 12, 0, 4, 2));
		$this->addAftSystem(new Thruster(4, 10, 0, 3, 2));
		$this->addAftSystem(new JumpEngine(5, 20, 3, 20));
		$this->addAftSystem(new AssaultLaser(3, 6, 4, 180, 300));
		$this->addAftSystem(new AssaultLaser(3, 6, 4, 60, 180));
		$this->addAftSystem(new TwinArray(3, 6, 2, 120, 0));
		$this->addAftSystem(new TwinArray(3, 6, 2, 0, 240));

		$this->addLeftSystem(new Thruster(4, 14, 0, 5, 3));
		$this->addLeftSystem(new ImperialLaser(3, 8, 5, 300, 0));
		$this->addLeftSystem(new TwinArray(3, 6, 2, 180, 0));

		$this->addRightSystem(new Thruster(4, 14, 0, 5, 4));
		$this->addRightSystem(new ImperialLaser(3, 8, 5, 0, 60));
		$this->addRightSystem(new TwinArray(3, 6, 2, 0, 180));

		//0:primary, 1:front, 2:rear, 3:left, 4:right;
		$this->addFrontSystem(new Structure( 5, 38));
		$this->addAftSystem(new Structure( 4, 35));
		$this->addLeftSystem(new Structure( 4, 51));
		$this->addRightSystem(new Structure( 4, 51));
		$this->addPrimarySystem(new Structure( 6, 55));


		   
		//d20 hit chart
		$this->hitChart = array(
			
			0=> array(
				10 => "Structure",
				13 => "Scanner",
				16 => "Engine",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),

			1=> array(
				4 => "Thruster",
				6 => "Imperial Laser",
				7 => "Assault Laser",
				9 => "Twin Array",
				11 => "Hangar",
				18 => "Structure",
				20 => "Primary",
			),

			2=> array(
				5 => "Thruster",
				8 => "Jump Engine",
				10 => "Assault Laser",
				12 => "Twin Array",
				18 => "Structure",
				20 => "Primary",
			),

			3=> array(
				4 => "Thruster",
				6 => "Imperial Laser",
				8 => "Twin Array",
				18 => "Structure",
				20 => "Primary",
			),

			4=> array(
				4 => "Thruster",
				6 => "Imperial Laser",
				8 => "Twin Array",
				18 => "Structure",
				20 => "Primary",
			),
		);
    }
}
