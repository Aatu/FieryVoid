<?php
class Rolotha extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 180;
		$this->faction = "Minbari";
        $this->phpclass = "Rolotha";
        $this->imagePath = "img/ships/rogata.png";
        $this->shipClass = "Rolotha Freighter";
        $this->gravitic = true;
        $this->forwardDefense = 13;
        $this->sideDefense = 14;
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
		$this->iniativebonus = 0;
		$this->canvasSize = 115; 
		$this->isd = 1990;

        $this->addPrimarySystem(new Reactor(5, 17, 0, 4));
        $this->addPrimarySystem(new CnC(3, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 14, 4, 5));
        $this->addPrimarySystem(new Engine(3, 11, 0, 8, 3));
		$this->addPrimarySystem(new Hangar(4, 2));
		$this->addPrimarySystem(new GraviticThruster(4, 12, 0, 4, 3));
		$this->addPrimarySystem(new GraviticThruster(4, 12, 0, 4, 4));
		$this->addPrimarySystem(new TractorBeam(4, 10, 0, 0));
		$this->addPrimarySystem(new CargoBay(2, 60));
		$this->addPrimarySystem(new CargoBay(2, 60));
		$this->addPrimarySystem(new FusionCannon(3, 8, 1, 180, 0));
		$this->addPrimarySystem(new FusionCannon(3, 8, 1, 0, 180));
		
        $this->addFrontSystem(new GraviticThruster(4, 14, 0, 6, 1));

        $this->addAftSystem(new GraviticThruster(4, 22, 0, 8, 2));

        $this->addPrimarySystem(new Structure( 5, 45));
		
		$this->hitChart = array(
            0=> array(
					8 => "Thruster",
                    11 => "Cargo Bay",
					13 => "Tractor Beam",
            		15 => "Scanner",
                    16 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    6 => "Thruster",
					8 => "0:Fusion Cannon",
					11 => "0:Cargo Bay",
                    17 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    6 => "Thruster",
                    8 => "0:Fusion Cannon",
            		11 => "0:Cargo Bay",
                    17 => "Structure",
                    20 => "Primary",
            ),
        );
    }

}
?>
