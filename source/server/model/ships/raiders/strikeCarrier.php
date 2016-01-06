<?php
class StrikeCarrier extends BaseShip{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 400;
		$this->faction = "Raiders";
		$this->phpclass = "StrikeCarrier";
		$this->imagePath = "img/ships/battlewagon.png"; //need to change
		$this->shipClass = "Strike Carrier";
		$this->shipSizeClass = 3;
		$this->fighters = array("light"=>24);

		$this->forwardDefense = 14;
		$this->sideDefense = 11;

		$this->turncost = 0.5;
		$this->turndelaycost = 0.66;
		$this->accelcost = 3;
		$this->rollcost = 1;
		$this->pivotcost = 3;
		 
		$this->addPrimarySystem(new Reactor(4, 15, 0, 0));
		$this->addPrimarySystem(new CnC(5, 12, 0, 0));
		$this->addPrimarySystem(new Scanner(5, 20, 5, 6));
		$this->addPrimarySystem(new Engine(4, 15, 0, 12, 3));
		$this->addPrimarySystem(new Hangar(5, 2));
		$this->addPrimarySystem(new CargoBay(5, 30));

		$this->addFrontSystem(new Thruster(4, 13, 0, 5, 1));
		$this->addFrontSystem(new Thruster(4, 13, 0, 5, 1));
		$this->addFrontSystem(new MediumPulse(4, 6, 3, 300, 60));
		$this->addFrontSystem(new StdParticleBeam(2, 4, 1, 300, 60));
		$this->addFrontSystem(new MediumPulse(4, 6, 3, 300, 60));

		$this->addAftSystem(new JumpEngine(4, 11, 4, 16));
		$this->addAftSystem(new Thruster(4, 15, 0, 6, 2));
		$this->addAftSystem(new Thruster(4, 15, 0, 6, 2));
		$this->addAftSystem(new StdParticleBeam(2, 4, 1, 120, 240));
		
		$this->addLeftSystem(new Thruster(4, 15, 0, 5, 3));
		$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 360));
		$this->addLeftSystem(new MediumPulse(4, 6, 3, 180, 360));
		
		$this->addRightSystem(new Thruster(4, 15, 0, 5, 4));
		$this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
		$this->addRightSystem(new MediumPulse(4, 6, 3, 0, 180));

		//0:primary, 1:front, 2:rear, 3:left, 4:right;
		$this->addFrontSystem(new Structure( 5, 78));
		$this->addAftSystem(new Structure( 5, 48));
		$this->addLeftSystem(new Structure( 4, 36));
		$this->addRightSystem(new Structure( 4, 36));
		$this->addPrimarySystem(new Structure( 5, 50));

		$this->hitChart = array(
				0=> array(
						1 => "structure",
						2 => "structure",
						3 => "structure",
						4 => "structure",
						5 => "structure",
						6 => "structure",
						7 => "structure",
						8 => "structure",
						9 => "cargo Bay",
						10 => "cargo Bay",
						11 => "cargo Bay",
						12 => "cargo Bay",
						13 => "scanner",
						14 => "scanner",
						15 => "hangar",
						16 => "reactor",
						17 => "reactor",
						18 => "engine",
						19 => "engine",
						20 => "C&C",
				),
				1=> array(
						1 => "thruster",
						2 => "thruster",
						3 => "thruster",
						4 => "thruster",
						5 => "thruster",
						6 => "medium Pulse",
						7 => "medium Pulse",
						8 => "Standard Particle Beam",
						9 => "structure",
						10 => "structure",
						11 => "structure",
						12 => "structure",
						13 => "structure",
						14 => "structure",
						15 => "structure",
						16 => "structure",
						17 => "structure",
						18 => "structure",
						19 => "primary",
						20 => "primary",
				),
				2=> array(
						1 => "thruster",
						2 => "thruster",
						3 => "thruster",
						4 => "thruster",
						5 => "thruster",
						6 => "thruster",
						7 => "Standard Particle Beam",
						8 => "jump Engine",
						9 => "jump Engine",
						10 => "jump Engine",
						11 => "jump Engine",
						12 => "structure",
						13 => "structure",
						14 => "structure",
						15 => "structure",
						16 => "structure",
						17 => "structure",
						18 => "structure",
						19 => "primary",
						20 => "primary",
				),
				3=> array(
						1 => "thruster",
						2 => "thruster",
						3 => "thruster",
						4 => "thruster",
						5 => "medium Pulse",
						6 => "medium Pulse",
						7 => "Standard Particle Beam",
						8 => "structure",
						9 => "structure",
						10 => "structure",
						11 => "structure",
						12 => "structure",
						13 => "structure",
						14 => "structure",
						15 => "structure",
						16 => "structure",
						17 => "structure",
						18 => "structure",
						19 => "primary",
						20 => "primary",
				),
				4=> array(
						1 => "thruster",
						2 => "thruster",
						3 => "thruster",
						4 => "thruster",
						5 => "medium Pulse",
						6 => "medium Pulse",
						7 => "Standard Particle Beam",
						8 => "structure",
						9 => "structure",
						10 => "structure",
						11 => "structure",
						12 => "structure",
						13 => "structure",
						14 => "structure",
						15 => "structure",
						16 => "structure",
						17 => "structure",
						18 => "structure",
						19 => "primary",
						20 => "primary",
				),
		);
	}
}