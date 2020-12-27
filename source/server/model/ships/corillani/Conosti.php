<?php
class Conosti extends MediumShip
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 300;
		$this->faction = "Corillani";
		$this->phpclass = "Conosti";
		$this->imagePath = "img/ships/CorillaniConosti.png";
		$this->shipClass = "Conosti Patrol Frigate (CPN)";
		$this->canvasSize = 100;
		$this->isd = 2230;
		

		$this->forwardDefense = 11;
		$this->sideDefense = 13;

		$this->turncost = 0.66;
		$this->turndelaycost = 0.66;
		$this->accelcost = 2;
		$this->rollcost = 2;
		$this->pivotcost = 1;
		$this->iniativebonus = 12 *5;

		$this->addPrimarySystem(new Reactor(3, 8, 0, 0));
		$this->addPrimarySystem(new CnC(4, 9, 0, 0));
		$this->addPrimarySystem(new Scanner(3, 8, 3, 5));
		$this->addPrimarySystem(new Engine(3, 10, 0, 8, 2));
		$this->addPrimarySystem(new Hangar(2, 1));	
		$this->addPrimarySystem(new Thruster(3, 3, 0, 2, 3));
		$this->addPrimarySystem(new Thruster(3, 3, 0, 2, 3));
		$this->addPrimarySystem(new Thruster(3, 3, 0, 2, 4));
		$this->addPrimarySystem(new Thruster(3, 3, 0, 2, 4));						


		$this->addFrontSystem(new LightParticleCannon(3, 6, 5, 240, 60));
		$this->addFrontSystem(new MediumPlasma(3, 5, 3, 270, 90));		
		$this->addFrontSystem(new LightParticleCannon(3, 6, 5, 300, 120));
		$this->addFrontSystem(new TwinArray(2, 6, 2, 240, 120));
		$this->addFrontSystem(new Thruster(3, 5, 0, 3, 1));
		$this->addFrontSystem(new Thruster(3, 5, 0, 3, 1));

		
		$this->addAftSystem(new Thruster(3, 8, 0, 4, 2));
		$this->addAftSystem(new Thruster(3, 8, 0, 4, 2));
	

		$this->addPrimarySystem(new Structure(4, 48));


			$this->hitChart = array(
					0=> array(
						8 => "Thruster",
						11 => "Scanner",
						14 => "Engine",
						15 => "Hangar",
						18 => "Reactor",
						20 => "C&C",
					),
					1=> array(
						6 => "Thruster",
						8 => "Light Particle Cannon",
						9 => "Medium Plasma Cannon",
						10 => "Twin Array",
						17 => "Structure",
						20 => "Primary",
					),
					2=> array(
						8 => "Thruster",
						17 => "Structure",
						20 => "Primary",
					),
			);
	}
}



?>