<?php
class Hakara extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 125;
		$this->faction = "Civilians";
		$this->phpclass = "Hakara";
		$this->imagePath = "img/ships/BrakiriCourier.png";
		$this->shipClass = "Brakiri Hakara Courier";
		$this->canvasSize = 100;
        $this->gravitic = true;
        $this->isd = 2207;
		$this->fighters = array("cargo shuttles"=>2); 		
	    $this->isCombatUnit = false; //not a combat unit, it will never be present in a regular battlegroup

		$this->forwardDefense = 14;
		$this->sideDefense = 15;

		$this->turncost = 1;
		$this->turndelaycost = 0.66;
		$this->accelcost = 4;
		$this->rollcost = 999;
		$this->pivotcost = 999;
		$this->iniativebonus = -20;

		$this->addPrimarySystem(new Reactor(4, 10, 0, 0));
		$this->addPrimarySystem(new CnC(3, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 9, 3, 4));
		$this->addPrimarySystem(new Engine(3, 10, 0, 12, 3));
		$this->addPrimarySystem(new Hangar(2, 2, 1));
		$this->addPrimarySystem(new GraviticThruster(4, 10, 0, 5, 3));
		$this->addPrimarySystem(new GraviticThruster(4, 10, 0, 5, 4));
		$this->addPrimarySystem(new GraviticBolt(3, 5, 2, 0, 360));

		$this->addFrontSystem(new GraviticThruster(4, 8, 0, 2, 1));
		$this->addFrontSystem(new GraviticThruster(4, 8, 0, 2, 1));
		$this->addFrontSystem(new CargoBay(1, 20));
		$this->addFrontSystem(new CargoBay(1, 20));

		$this->addAftSystem(new GraviticThruster(3, 10, 0, 6, 2));
		$this->addAftSystem(new GraviticThruster(3, 10, 0, 6, 2));
		$this->addAftSystem(new CargoBay(1, 20));
		$this->addAftSystem(new CargoBay(1, 20));
	
        $this->addPrimarySystem(new Structure( 4, 44));
		
				$this->hitChart = array(
                0=> array(
                        8 => "Thruster",
                        9 => "Gravitic Bolt",
                        11 => "Scanner",
                        14 => "Engine",
                        15 => "Hangar",
                        18 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        4 => "Thruster",
                        10 => "Cargo Bay",
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
