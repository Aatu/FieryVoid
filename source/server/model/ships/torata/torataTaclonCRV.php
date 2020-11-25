<?php
class TorataTaclonCRV extends MediumShip
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 400;
		$this->faction = "Torata";
		$this->phpclass = "TorataTaclonCRV";
		$this->imagePath = "img/ships/TorataAtlac.png";
		$this->canvasSize = 100;
		$this->shipClass = "Taclon Escort Corvette";
		$this->variantOf = "Atlac Corvette";
		$this->occurence = "uncommon";
		$this->isd = 2257;

		$this->forwardDefense = 13;
		$this->sideDefense = 12;

		$this->turncost = 0.66;
		$this->turndelaycost = 0.33;
		$this->accelcost = 2;
		$this->rollcost = 2;
		$this->pivotcost = 2;
		$this->iniativebonus = 12 *5;

		$this->addPrimarySystem(new Reactor(4, 13, 0, 6));
		$this->addPrimarySystem(new CnC(4, 12, 0, 0));
		$this->addPrimarySystem(new Scanner(4, 12, 4, 6));
		$this->addPrimarySystem(new Engine(4, 13, 0, 8, 2));
		$this->addPrimarySystem(new Hangar(4, 1));
		$this->addPrimarySystem(new Thruster(3, 13, 0, 5, 3));
		$this->addPrimarySystem(new Thruster(3, 13, 0, 5, 4));
		$this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 180, 60));
		$this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 240, 120));
		$this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 240, 120));
		$this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 300, 180));

		$this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
		$this->addFrontSystem(new PentagonArray(3, 8, 5, 240, 60));
		$this->addFrontSystem(new PentagonArray(3, 8, 5, 300, 120));
		
		$this->addAftSystem(new Thruster(3, 16, 0, 8, 2));

		$this->addPrimarySystem(new Structure(4, 40));


			$this->hitChart = array(
					0=> array(
						8 => "Thruster",
						11 => "Scanner",
						14 => "Engine",
						16 => "Hangar",
						19 => "Reactor",
						20 => "C&C",
					),
					1=> array(
						4 => "Thruster",
						6 => "Pentagon Array",
						10 => "0:Light Particle Beam",
						16 => "Structure",
						20 => "Primary",
					),
					2=> array(
						6 => "Thruster",
						8 => "0:Light Particle Beam",
						16 => "Structure",
						20 => "Primary",
					),
			);
	}
}



?>
