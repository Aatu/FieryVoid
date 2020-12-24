<?php
class gaimRassa extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 350;
		$this->faction = "Gaim";
		$this->phpclass = "gaimRassa";
		$this->imagePath = "img/ships/GaimRassa.png";
		$this->shipClass = "Rassa Patrol Frigate";
		$this->canvasSize = 100;

		$this->notes = 'Atmospheric Capable';
		$this->isd = 2251;
		
		$this->forwardDefense = 11;
		$this->sideDefense = 13;

		$this->turncost = 0.5;
		$this->turndelaycost = 0.5;
		$this->accelcost = 3;
		$this->rollcost = 2;
		$this->pivotcost = 2;
		$this->iniativebonus = 60;

		$this->addPrimarySystem(new Reactor(4, 12, 0, 1));
		$this->addPrimarySystem(new CnC(6, 8, 0, 0));
		$this->addPrimarySystem(new Scanner(4, 9, 5, 7));
		$this->addPrimarySystem(new Engine(4, 9, 0, 10, 3));
		$this->addPrimarySystem(new Thruster(4, 10, 0, 5, 3));
		$this->addPrimarySystem(new Thruster(4, 10, 0, 5, 4));

		$this->addFrontSystem(new PacketTorpedo(4, 6, 5, 240, 360));
		$this->addFrontSystem(new PacketTorpedo(4, 6, 5, 0, 120));
		$this->addFrontSystem(new Thruster(4, 8, 0, 2, 1));
		$this->addFrontSystem(new Thruster(4, 8, 0, 2, 1));
		$this->addFrontSystem(new TwinArray(4, 6, 2, 180, 360));
		$this->addFrontSystem(new TwinArray(4, 6, 2, 0, 180));
		$this->addFrontSystem(new Bulkhead(0, 2));

		$this->addAftSystem(new Thruster(4, 10, 0, 5, 2));
		$this->addAftSystem(new Thruster(4, 10, 0, 5, 2));
		$this->addAftSystem(new Hangar(3, 1));
		$this->addAftSystem(new Bulkhead(0, 1));

		$this->addPrimarySystem(new Structure( 4, 50));
		
		$this->hitChart = array(
			0=> array(
					9 => "Thruster",
					12 => "Scanner",
					15 => "Engine",
					18 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					8 => "Packet Torpedo",
					10 => "Twin Array",
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
