<?php
class gaimTackra extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 350;
		$this->faction = "Gaim";
		$this->phpclass = "gaimTackra";
		$this->imagePath = "img/ships/GaimTackra.png";
		$this->shipClass = "Tackra Escort Cutter";
		$this->canvasSize = 100;
        $this->isd = 2246;

		$this->forwardDefense = 13;
		$this->sideDefense = 13;

		$this->turncost = 0.33;
		$this->turndelaycost = 0.5;
		$this->accelcost = 2;
		$this->rollcost = 1;
		$this->pivotcost = 1;
		$this->iniativebonus = 60;

		$this->addPrimarySystem(new Reactor(4, 13, 0, 0));
		$this->addPrimarySystem(new CnC(4, 9, 0, 0));
		$this->addPrimarySystem(new Scanner(4, 12, 3, 6));
		$this->addPrimarySystem(new Engine(4, 11, 0, 8, 2));
		$this->addPrimarySystem(new Hangar(4, 2));
		$this->addPrimarySystem(new Thruster(3, 13, 0, 4, 3));
		$this->addPrimarySystem(new Thruster(3, 13, 0, 4, 4));

		$this->addFrontSystem(new Thruster(3, 7, 0, 3, 1));
		$this->addFrontSystem(new Thruster(3, 7, 0, 3, 1));
		$this->addFrontSystem(new ScatterGun(3, 8, 3, 240, 360));
		$this->addFrontSystem(new TwinArray(2, 6, 2, 270, 90));
		$this->addFrontSystem(new TwinArray(2, 6, 2, 270, 90));
		$this->addFrontSystem(new TwinArray(2, 6, 2, 270, 90));
		$this->addFrontSystem(new ScatterGun(3, 8, 3, 0, 120));
		$this->addFrontSystem(new Bulkhead(0, 2));

		$this->addAftSystem(new Thruster(4, 8, 0, 4, 2));
		$this->addAftSystem(new Thruster(4, 8, 0, 4, 2));
		$this->addAftSystem(new TwinArray(2, 6, 2, 180, 360));
        $this->addAftSystem(new TwinArray(2, 6, 2, 0, 180));
		$this->addAftSystem(new Bulkhead(0, 2));
	
        $this->addPrimarySystem(new Structure( 4, 38));
		
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
                        8 => "Scattergun",
                        11 => "Twin Array",
                        17 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        8 => "Thruster",
                        10 => "Twin Array",
                        17 => "Structure",
                        20 => "Primary",
                ),
        );
    }
}



?>
