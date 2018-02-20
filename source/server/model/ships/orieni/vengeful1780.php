<?php
class Vengeful1780 extends MediumShip{
	/*Orieni Vengeful Laser Frigate, variant ISD 1780 (WoCR)*/
	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);
		$this->pointCost = 365;
		$this->faction = "Orieni";
		$this->phpclass = "Vengeful1780";
		$this->imagePath = "img/ships/vengeful.png"; 
		$this->shipClass = "Vengeful Laser Frigate (early)";
		$this->variantOf = "Vengeful Laser Frigate";
		$this->isd = 1780;
		
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
		$this->addPrimarySystem(new Scanner(3, 12, 3, 5));
		$this->addPrimarySystem(new Engine(3, 15, 0, 10, 2));
		$this->addPrimarySystem(new Hangar(1, 1));
		$this->addPrimarySystem(new Thruster(2, 10, 0, 5, 3));
		$this->addPrimarySystem(new Thruster(2, 10, 0, 5, 4));

		$this->addFrontSystem(new Thruster(1, 6, 0, 2, 1));
		$this->addFrontSystem(new Thruster(1, 6, 0, 2, 1));
		$this->addFrontSystem(new Thruster(1, 6, 0, 2, 1));
		$this->addFrontSystem(new LaserLance(3, 6, 4, 300, 60));
		$this->addFrontSystem(new LightLaser(0, 4, 3, 240, 360));
		$this->addFrontSystem(new LightLaser(0, 4, 3, 0, 120));

		$this->addAftSystem(new Thruster(1, 6, 0, 3, 2));
		$this->addAftSystem(new Thruster(1, 6, 0, 4, 2));
		$this->addAftSystem(new Thruster(1, 6, 0, 3, 2));
		$this->addAftSystem(new OrieniGatlingRG(1, 4, 1, 120, 360));
		$this->addAftSystem(new OrieniGatlingRG(1, 4, 1, 0, 240));
		 

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
			8 => "Laser Lance",
			10 => "Light Laser",
			17 => "Structure",
			20 => "Primary",
		),
		2=> array( //Aft
			6 => "Thruster",
			8 => "Gatling Railgun",
			17 => "Structure",
			20 => "Primary",
		),

	); //end of d20 hit chart


	}
}
?>
