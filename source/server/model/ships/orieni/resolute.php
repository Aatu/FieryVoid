<?php
class resolute extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 175;
		$this->faction = "Orieni";
        $this->phpclass = "Resolute";
        $this->imagePath = "img/ships/fastFreighter.png";
        $this->shipClass = "Resolute Military Freighter";
        $this->canvasSize = 100;
	    $this->isd = 1802;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 13;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 999;
        $this->pivotcost = 999;
		
		$cA = new CargoBay(2, 30);
		$cB = new CargoBay(2, 30);
		$cC = new CargoBay(2, 30);
		$cD = new CargoBay(2, 30);
		
		$cA->displayName = "Cargo Bay A";
		$cB->displayName = "Cargo Bay B";
		$cC->displayName = "Cargo Bay C";
		$cD->displayName = "Cargo Bay D";		
		
        $this->addPrimarySystem(new Reactor(4, 11, 0, 0));
        $this->addPrimarySystem(new CnC(4, 9, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 11, 2, 4));
        $this->addPrimarySystem(new Engine(3, 16, 0, 8, 3));
		$this->addPrimarySystem(new Hangar(1, 4));
		$this->addPrimarySystem(new Thruster(2, 13, 0, 4, 3));
		$this->addPrimarySystem(new Thruster(2, 13, 0, 4, 4));
		
        $this->addFrontSystem(new Thruster(2, 12, 0, 6, 1));
        $this->addFrontSystem(new OrieniGatlingRG(1, 4, 1, 240, 60));
        $this->addFrontSystem(new OrieniGatlingRG(1, 4, 1, 300, 120));
        $this->addFrontSystem($cA);
        $this->addFrontSystem($cB);
        
        $this->addAftSystem(new Thruster(2, 15, 0, 8, 2));
        $this->addAftSystem(new OrieniGatlingRG(1, 4, 1, 120, 300));
        $this->addAftSystem(new OrieniGatlingRG(1, 4, 1, 60, 240));
        $this->addAftSystem($cC);
        $this->addAftSystem($cD);
       
        $this->addPrimarySystem(new Structure(4, 44));
        
        $this->hitChart = array(
        		0=> array(
        				9 => "Thruster",
           				12 => "Scanner",
        				15 => "Engine",
        				17 => "Hangar",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				5 => "Thruster",
                        7 => "Gatling Railgun",
        				9 => "Cargo Bay A",
        				11 => "Cargo Bay B",
        				17 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				5 => "Thruster",
                        7 => "Gatling Railgun",
        				9 => "Cargo Bay C",
        				11 => "Cargo Bay D",
        				17 => "Structure",
        				20 => "Primary",
        		),
        );
    }
}
?>