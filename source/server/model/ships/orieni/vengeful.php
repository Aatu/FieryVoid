<?php
class Vengeful extends MediumShip{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 415;
		$this->faction = "Orieni";
		$this->phpclass = "Vengeful";
		$this->imagePath = "img/ships/vengeful.png"; //image still needs to be uploaded
		$this->shipClass = "Vengeful Laser Frigate";
		$this->agile = true;
		$this->canvasSize = 100;

		$this->forwardDefense = 12;
		$this->sideDefense = 13;

		$this->turncost = 0.5;
		$this->turndelaycost = 0.5;
		$this->accelcost = 2;
		$this->rollcost = 2;
		$this->pivotcost = 2;
		$this->iniativebonus = 60;

		 
		$this->addPrimarySystem(new Reactor(4, 15, 0, 0));
		$this->addPrimarySystem(new CnC(4, 14, 0, 0));
		$this->addPrimarySystem(new Scanner(3, 12, 3, 6));
		$this->addPrimarySystem(new Engine(3, 15, 0, 10, 2));
		$this->addPrimarySystem(new Hangar(1, 1));
		$this->addPrimarySystem(new Thruster(2, 10, 0, 5, 3));
		$this->addPrimarySystem(new Thruster(2, 10, 0, 5, 4));

		$this->addFrontSystem(new Thruster(2, 6, 0, 2, 1));
		$this->addFrontSystem(new Thruster(2, 6, 0, 2, 1));
		$this->addFrontSystem(new Thruster(2, 6, 0, 2, 1));
		$this->addFrontSystem(new HeavyLaserLance(5, 6, 4, 300, 60));
		$this->addFrontSystem(new RapidGatling(2, 4, 1, 240, 360));
		$this->addFrontSystem(new RapidGatling(2, 4, 1, 0, 120));

		$this->addAftSystem(new Thruster(2, 6, 0, 3, 2));
		$this->addAftSystem(new Thruster(2, 6, 0, 4, 2));
		$this->addAftSystem(new Thruster(2, 6, 0, 3, 2));
		$this->addAftSystem(new RapidGatling(2, 4, 1, 120, 360));
		$this->addAftSystem(new RapidGatling(2, 4, 1, 0, 240));
		 
		$this->addPrimarySystem(new Structure(4, 44));

	}
}
?>

        