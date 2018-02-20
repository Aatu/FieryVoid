<?php
class Vengeful extends MediumShip{
	/*Orieni Vengeful Laser Frigate, variant ISD 2007 (WoCR)*/
	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);
		$this->pointCost = 415;
		$this->faction = "Orieni";
		$this->phpclass = "Vengeful";
		$this->imagePath = "img/ships/vengeful.png"; 
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

		$this->addFrontSystem(new Thruster(1, 6, 0, 2, 1));
		$this->addFrontSystem(new Thruster(2, 6, 0, 2, 1));
		$this->addFrontSystem(new Thruster(1, 6, 0, 2, 1));
		$this->addFrontSystem(new HeavyLaserLance(5, 6, 4, 300, 60));
		$this->addFrontSystem(new RapidGatling(2, 4, 1, 240, 360));
		$this->addFrontSystem(new RapidGatling(2, 4, 1, 0, 120));

		$this->addAftSystem(new Thruster(2, 6, 0, 3, 2));
		$this->addAftSystem(new Thruster(2, 6, 0, 4, 2));
		$this->addAftSystem(new Thruster(2, 6, 0, 3, 2));
		$this->addAftSystem(new RapidGatling(2, 4, 1, 120, 360));
		$this->addAftSystem(new RapidGatling(2, 4, 1, 0, 240));
		 

		$this->addPrimarySystem(new Structure(4, 44));



	//d20 hit chart
	$this->hitChart = array(
		
		0=> array( //PRIMARY
			8 => "Thruster",
			11 => "Scanner",
			15 => "Engine",
			17 => "Hangar",
			19 => "Reactor",
			20 => "C&C",
		),
		1=> array( //Fwd
			6 => "Thruster",
			8 => "Heavy Laser Lance",
			10 => "Rapid Gatling Railgun",
			17 => "Structure",
			20 => "Primary",
		),
		2=> array( //Aft
			6 => "Thruster",
			8 => "Rapid Gatling Railgun",
			17 => "Structure",
			20 => "Primary",
		),

	); //end of d20 hit chart


	}
}
?>
