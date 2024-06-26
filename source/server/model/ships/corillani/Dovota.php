<?php
class Dovota extends MediumShip
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 385;
		$this->faction = "Corillani Theocracy";
		$this->phpclass = "Dovota";
		$this->imagePath = "img/ships/CorillaniNollita.png";
		$this->shipClass = "Dovota Minesweeper";
		$this->canvasSize = 100;
		$this->isd = 2237;
		$this->notes = 'Defenders of Corrilan (DoC)';
        $this->occurence = "uncommon";
		$this->variantOf = "Nollita Tactical Frigate";
        $this->agile = true;	
        
        $this->minesweeperbonus = 2;        					    
 		$this->unofficial = 'S'; //design released after AoG demise		

		$this->forwardDefense = 12;
		$this->sideDefense = 13;

		$this->turncost = 0.5;
		$this->turndelaycost = 0.5;
		$this->accelcost = 2;
		$this->rollcost = 1;
		$this->pivotcost = 1;
		$this->iniativebonus = 13 *5;

		$this->addPrimarySystem(new Reactor(3, 10, 0, 1));
		$this->addPrimarySystem(new CnC(4, 9, 0, 0));
		$this->addPrimarySystem(new Scanner(3, 8, 3, 6));
		$this->addPrimarySystem(new Engine(3, 11, 0, 8, 2));
		$this->addPrimarySystem(new Hangar(1, 1));	
		$this->addPrimarySystem(new Thruster(3, 4, 0, 2, 3));
		$this->addPrimarySystem(new Thruster(3, 4, 0, 2, 3));
		$this->addPrimarySystem(new Thruster(3, 4, 0, 2, 4));
		$this->addPrimarySystem(new Thruster(3, 4, 0, 2, 4));						
		$this->addFrontSystem(new ParticleCannon(3, 8, 7, 240, 60));
		$this->addFrontSystem(new TwinArray(2, 6, 2, 270, 90));		
		$this->addFrontSystem(new ParticleCannon(3, 8, 7, 300, 120));
		$this->addFrontSystem(new Thruster(3, 5, 0, 3, 1));
		$this->addFrontSystem(new Thruster(3, 5, 0, 3, 1));

		$this->addAftSystem(new TwinArray(2, 6, 2, 180, 360));
		$this->addAftSystem(new TwinArray(2, 6, 2, 90, 270));
		$this->addAftSystem(new TwinArray(2, 6, 2, 0, 180));
		$this->addAftSystem(new Thruster(3, 8, 0, 4, 2));
		$this->addAftSystem(new Thruster(3, 8, 0, 4, 2));
	

		$this->addPrimarySystem(new Structure(4, 60));


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
						8 => "Particle Cannon",
						10 => "Twin Array",
						17 => "Structure",
						20 => "Primary",
					),
					2=> array(
						6 => "Thruster",
						10 => "Twin Array",
						17 => "Structure",
						20 => "Primary",
					),
			);
	}
}



?>