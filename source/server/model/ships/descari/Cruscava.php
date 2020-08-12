<?php
class Cruscava extends MediumShip
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 375;
		$this->faction = "Descari";
		$this->phpclass = "Cruscava";
		$this->imagePath = "img/ships/DescariCruscava.png";
		$this->shipClass = "Cruscava Escort Frigate";
		$this->canvasSize = 200;
		$this->isd = 2243;
		

		$this->forwardDefense = 12;
		$this->sideDefense = 13;

		$this->turncost = 0.33;
		$this->turndelaycost = 0.5;
		$this->accelcost = 2;
		$this->rollcost = 1;
		$this->pivotcost = 1;
		$this->iniativebonus = 12 *5;

		$this->addPrimarySystem(new Reactor(4, 13, 0, 0));
		$this->addPrimarySystem(new CnC(4, 9, 0, 0));
		$this->addPrimarySystem(new Scanner(4, 12, 3, 5));
		$this->addPrimarySystem(new Engine(4, 11, 0, 8, 2));
		$this->addPrimarySystem(new Hangar(4, 2));	
		$this->addPrimarySystem(new Thruster(3, 13, 0, 4, 3));
		$this->addPrimarySystem(new Thruster(3, 13, 0, 4, 4));
		$this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
		$this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
		$this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
		$this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
		$this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
		$this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 0, 180));


		$this->addFrontSystem(new Thruster(3, 7, 0, 3, 1));
		$this->addFrontSystem(new Thruster(3, 7, 0, 3, 1));
		$this->addFrontSystem(new LightPlasmaBolter(2, 0, 0, 300, 60));
		$this->addFrontSystem(new MediumPlasmaBolter(2, 0, 0, 300, 60));		
		$this->addFrontSystem(new LightPlasmaBolter(2, 0, 0, 300, 60));
		
		$this->addAftSystem(new Thruster(4, 8, 0, 4, 2));
		$this->addAftSystem(new Thruster(4, 8, 0, 4, 2));
	

		$this->addPrimarySystem(new Structure(4, 38));


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
						6 => "Thruster",
						8 => "Light Plasma Bolter",
						10 => "Medium Plasma Bolter",
						12 => "0:Light Particle Beam",
						17 => "Structure",
						20 => "Primary",
					),
					2=> array(
						8 => "Thruster",
						11 => "0:Light Particle Beam",
						17 => "Structure",
						20 => "Primary",
					),
			);
	}
}



?>