<?php
class TankerTug extends MediumShip{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 102;
		$this->faction = "Civilians";
		$this->phpclass = "TankerTug";
		$this->imagePath = "img/ships/civilianFreighter.png"; //need to change
		$this->shipClass = "Tanker Tug";
		$this->canvasSize = 100;
	    $this->isCombatUnit = false; //not a combat unit, it will never be present in a regular battlegroup
		$this->isd = 2195;

		$this->forwardDefense = 12;
		$this->sideDefense = 12;

		$this->turncost = 1;
		$this->turndelaycost = 1;
		$this->accelcost = 4;
		$this->rollcost = 999;
		$this->pivotcost = 999;
		$this->iniativebonus = -20;
		 
		$this->addPrimarySystem(new Reactor(2, 3, 0, 0));
		$this->addPrimarySystem(new Scanner(2, 6, 1, 1));
		$this->addPrimarySystem(new Engine(2, 18, 0, 12, 3));
		$this->addPrimarySystem(new Hangar(2, 1));
		$this->addPrimarySystem(new Thruster(2, 15, 0, 6, 3));
		$this->addPrimarySystem(new Thruster(2, 15, 0, 6, 4));

		$this->addFrontSystem(new Thruster(2, 10, 0, 4, 1));
		$this->addFrontSystem(new Thruster(2, 10, 0, 4, 1));
		$this->addFrontSystem(new CnC(3, 5, 0, 0));
		$this->addFrontSystem(new StdParticleBeam(1, 4, 1, 0, 360));
		$this->addFrontSystem(new CargoBay(2, 20));

		$this->addAftSystem(new Thruster(2, 12, 0, 4, 2));
		$this->addAftSystem(new Thruster(2, 12, 0, 4, 2));
		$this->addAftSystem(new Thruster(2, 12, 0, 4, 2));
		$this->addAftSystem(new CargoBay(2, 24));
		$this->addAftSystem(new CargoBay(2, 24));

		$this->addPrimarySystem(new Structure(2, 40));

		$this->hitChart = array(
				0=> array(
						8 => "Thruster",
						11 => "Scanner",
						15 => "Engine",
						17 => "Hangar",
						20 => "Reactor",
				),
				1=> array(
						5 => "Thruster",
						8 => "Standard Particle Beam",
						9 => "C&C",
						11 => "Cargo Bay",
						17 => "Structure",
						20 => "Primary",
				),
				2=> array(
						8 => "Thruster",
						12 => "Cargo Bay",
						17 => "Structure",
						20 => "Primary",
				),
		);
	}
}
?>
