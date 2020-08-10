<?php
class Croscotu extends MediumShip
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 225;
		$this->faction = "Descari";
		$this->phpclass = "Croscotu";
		$this->imagePath = "img/ships/DescariCroscotu.png";
		$this->shipClass = "Croscotu Frigate";
		$this->canvasSize = 200;
		$this->isd = 2215;

		$this->forwardDefense = 11;
		$this->sideDefense = 12;

		$this->turncost = 0.5;
		$this->turndelaycost = 0.5;
		$this->accelcost = 2;
		$this->rollcost = 1;
		$this->pivotcost = 1;
		$this->iniativebonus = 12 *5;

		$this->addPrimarySystem(new Reactor(3, 8, 0, 0));
		$this->addPrimarySystem(new CnC(3, 8, 0, 0));
		$this->addPrimarySystem(new Scanner(3, 8, 4, 4));
		$this->addPrimarySystem(new Engine(3, 7, 0, 4, 2));
		$this->addPrimarySystem(new Thruster(3, 6, 0, 2, 3));
		$this->addPrimarySystem(new Thruster(3, 6, 0, 2, 4));
		$this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 0, 360));
		$this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 0, 360));
		$this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 0, 360));

		$this->addFrontSystem(new Thruster(3, 6, 0, 2, 1));
		$this->addFrontSystem(new Thruster(3, 6, 0, 2, 1));
		$this->addFrontSystem(new PlasmaTorch(2, 4, 2, 240, 60));
		$this->addFrontSystem(new MediumPlasma(3, 5, 3, 300, 60));		
		$this->addFrontSystem(new PlasmaTorch(2, 4, 2, 300, 120));
		
		$this->addAftSystem(new Thruster(3, 6, 0, 2, 2));
		$this->addAftSystem(new Thruster(3, 6, 0, 2, 2));
		$this->addAftSystem(new Hangar(2, 1));		

		$this->addPrimarySystem(new Structure(3, 42));


			$this->hitChart = array(
					0=> array(
						9 => "Thruster",
						12 => "Light Particle Beam",
						15 => "Scanner",
						17 => "Engine",
						19 => "Reactor",
						20 => "C&C",
					),
					1=> array(
						5 => "Thruster",
						8 => "Medium Plasma Cannon",
						10 => "Plasma Torch",
						17 => "Structure",
						20 => "Primary",
					),
					2=> array(
						6 => "Thruster",
						8 => "Hangar",
						17 => "Structure",
						20 => "Primary",
					),
			);
	}
}



?>