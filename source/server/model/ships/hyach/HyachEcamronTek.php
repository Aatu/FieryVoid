<?php
class HyachEcamronTek extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 170;
		$this->faction = "Civilians";
		$this->phpclass = "HyachEcamronTek";
		$this->imagePath = "img/ships/HyachEcamromTek.png";
		$this->shipClass = "Ecamron Tek Freighter";
		$this->canvasSize = 100;
        $this->gravitic = true;
        $this->isd = 2213;

		$this->forwardDefense = 14;
		$this->sideDefense = 15;

		$this->turncost = 1;
		$this->turndelaycost = 1;
		$this->accelcost = 3;
		$this->rollcost = 2;
		$this->pivotcost = 2;
		$this->iniativebonus = 0;

		$this->addPrimarySystem(new Reactor(4, 10, 0, 0));
		$this->addPrimarySystem(new CnC(4, 5, 0, 0));
		$sensors = new Scanner(4, 8, 3, 4);
			$sensors->markHyach();
			$this->addPrimarySystem($sensors); 
		$this->addPrimarySystem(new Engine(4, 6, 0, 6, 4));
		$this->addPrimarySystem(new Hangar(3, 2));
		$this->addPrimarySystem(new Interdictor(2, 4, 1, 0, 360));
		$this->addPrimarySystem(new GraviticThruster(3, 8, 0, 3, 3));
		$this->addPrimarySystem(new GraviticThruster(3, 8, 0, 3, 4));

		$this->addFrontSystem(new GraviticThruster(3, 10, 0, 3, 1));
		$this->addFrontSystem(new Maser(2, 6, 3, 180, 360));
		$this->addFrontSystem(new CargoBay(3, 24));
		$this->addFrontSystem(new CargoBay(3, 24));
		$this->addFrontSystem(new Maser(2, 6, 3, 0, 180));

		$this->addAftSystem(new GraviticThruster(3, 14, 0, 6, 2));
		$this->addAftSystem(new CargoBay(3, 26));
		$this->addAftSystem(new CargoBay(3, 26));
	
        $this->addPrimarySystem(new Structure( 4, 52));
		
				$this->hitChart = array(
                0=> array(
                        9 => "Thruster",
						11 => "Interdictor",
                        13 => "Scanner",
                        16 => "Engine",
                        17 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        5 => "Thruster",
                        8 => "Maser",
                        11 => "Cargo Bay",
                        17 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        6 => "Thruster",
                        10 => "Cargo Bay",
                        17 => "Structure",
                        20 => "Primary",
                ),
        );
    }
}



?>
