<?php
class torataGoracFF extends MediumShip
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 300;
		$this->faction = "Torata Regency";
		$this->phpclass = "torataGoracFF";
		$this->imagePath = "img/ships/torataGorac.png";
		$this->shipClass = "Gorac Fast Frigate";
		$this->canvasSize = 125;
		$this->isd = 2219;
		$this->unofficial = true;

		$this->forwardDefense = 12;
		$this->sideDefense = 12;

		$this->turncost = 0.50;
		$this->turndelaycost = 0.33;
		$this->accelcost = 2;
		$this->rollcost = 2;
		$this->pivotcost = 1;
		$this->iniativebonus = 12 *5;

		$this->addPrimarySystem(new Reactor(4, 12, 0, 0));
		$this->addPrimarySystem(new CnC(4, 9, 0, 0));
		$this->addPrimarySystem(new Scanner(4, 12, 3, 5));
		$this->addPrimarySystem(new Engine(4, 12, 0, 12, 2));
		$this->addPrimarySystem(new Hangar(1, 1, 1));
		$this->addPrimarySystem(new Thruster(4, 13, 0, 5, 3));
		$this->addPrimarySystem(new Thruster(4, 13, 0, 5, 4));
		$this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 120, 300));
		$this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 60, 240));
		
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 240, 60));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 300, 120));
		$this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
		$this->addFrontSystem(new ParticleAccelerator(3, 8, 8, 300, 60));
		
		$this->addAftSystem(new Thruster(4, 12, 0, 6, 2));
		$this->addAftSystem(new Thruster(4, 12, 0, 6, 2));


		$this->addPrimarySystem(new Structure(4, 36));


			$this->hitChart = array(
					0=> array(
						9 => "Thruster",
						12 => "Scanner",
						15 => "Engine",
						16 => "Hangar",
						19 => "Reactor",
						20 => "C&C",
					),
					1=> array(
						5 => "Thruster",
						7 => "Particle Accelerator",
						10 => "Light Particle Beam",
						16 => "Structure",
						20 => "Primary",
					),
					2=> array(
						6 => "Thruster",
						9 => "Light Particle Beam",
						17 => "Structure",
						20 => "Primary",
					),
			);
	}
}



?>