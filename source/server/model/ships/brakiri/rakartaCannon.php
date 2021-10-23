<?php
class RakartaCannon extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 380;
		$this->faction = "Brakiri";
		$this->phpclass = "RakartaCannon";
		$this->imagePath = "img/ships/rakarta.png";
		$this->shipClass = "Rakarta Frigate (non-Kam-Lassit)";
		$this->canvasSize = 100;
		$this->variantOf = "Rakarta Frigate";

		$this->notes = 'Pri-Wakat Concepts & Solutions (common for other Corporations without separate variant)';//Corporation producing the design
		$this->isd = 2250;
		
		$this->forwardDefense = 11;
		$this->sideDefense = 13;

		$this->turncost = 0.5;
		$this->turndelaycost = 0.5;
		$this->accelcost = 3;
		$this->rollcost = 2;
		$this->pivotcost = 2;
		$this->iniativebonus = 60;

		$this->gravitic = true;

		$this->addPrimarySystem(new Reactor(4, 12, 0, 1));
		$this->addPrimarySystem(new CnC(5, 8, 0, 0));
		$this->addPrimarySystem(new Scanner(4, 9, 5, 7));
		$this->addPrimarySystem(new Engine(4, 9, 0, 10, 3));
		$this->addPrimarySystem(new GraviticThruster(4, 10, 0, 5, 3));
		$this->addPrimarySystem(new GraviticThruster(4, 10, 0, 5, 4));

		$this->addFrontSystem(new GraviticCannon(4, 6, 5, 240, 0));
		$this->addFrontSystem(new GravitonPulsar(4, 5, 2, 180, 0));
		$this->addFrontSystem(new GraviticThruster(4, 8, 0, 2, 1));
		$this->addFrontSystem(new GraviticThruster(4, 8, 0, 2, 1));
		$this->addFrontSystem(new GravitonPulsar(4, 5, 2, 0, 180));
		$this->addFrontSystem(new GraviticCannon(4, 6, 5, 0, 120));

		$this->addAftSystem(new GraviticThruster(4, 10, 0, 5, 2));
		$this->addAftSystem(new GraviticThruster(4, 10, 0, 5, 2));
		$this->addAftSystem(new Hangar(3, 1));

		$this->addPrimarySystem(new Structure( 4, 50));

		$this->hitChart = array(
			0=> array(	
					8 => "Thruster",
					11 => "Scanner",
					14 => "Engine",
					18 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					8 => "Gravitic Cannon",
					10 => "Graviton Pulsar",
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
