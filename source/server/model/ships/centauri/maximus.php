<?php
class Maximus extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 475;
		$this->faction = "Centauri";
        $this->phpclass = "Maximus";
        $this->imagePath = "img/ships/maximus.png";
        $this->shipClass = "Maximus Defense Frigate";
        $this->agile = true;
        $this->canvasSize = 100;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 13;
        
        $this->turncost = 0.50;
        $this->turndelaycost = 0.66;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
		$this->iniativebonus = 60;
        
         
        $this->addPrimarySystem(new Reactor(5, 12, 0, 0));
        $this->addPrimarySystem(new CnC(4, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 12, 4, 8));
        $this->addPrimarySystem(new Engine(4, 11, 0, 10, 2));
		$this->addPrimarySystem(new Hangar(4, 1));
		$this->addPrimarySystem(new Thruster(3, 10, 0, 4, 3));
		$this->addPrimarySystem(new Thruster(3, 10, 0, 4, 4));
		
		$this->addPrimarySystem(new GuardianArray(0, 4, 2, 0, 0));
		$this->addPrimarySystem(new GuardianArray(0, 4, 2, 0, 0));
		$this->addPrimarySystem(new GuardianArray(0, 4, 2, 0, 0));
		
        
		
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new TwinArray(3, 6, 2, 240, 120));
        $this->addFrontSystem(new TwinArray(3, 6, 2, 240, 120));
				
		
        $this->addAftSystem(new Thruster(3, 12, 0, 5, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 5, 2));
		$this->addAftSystem(new TwinArray(3, 6, 2, 60, 300));
        $this->addAftSystem(new TwinArray(3, 6, 2, 60, 300));
		
    
        $this->addPrimarySystem(new Structure( 5, 45));

	$this->hitChart = array(
                0=> array(
                        7 => "Thruster",
                        10 => "Guardian Array",
                        13 => "Scanner",
			16 => "Engine",
                        17 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        6 => "Thruster",
                        11 => "Twin Array",
                        17 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        5 => "Thruster",
                        9 => "Twin Array",
                        17 => "Structure",
                        20 => "Primary",
                ),
        );    
		
    }

}



?>
