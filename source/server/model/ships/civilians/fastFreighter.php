<?php
class FastFreighter extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 150;
		$this->faction = "Civilians";
        $this->phpclass = "FastFreighter";
        $this->imagePath = "img/ships/fastFreighter.png";
        $this->shipClass = "Civilian Fast Freighter";
        $this->canvasSize = 100;
	    $this->isCombatUnit = false; //not a combat unit, it will never be present in a regular battlegroup
		$this->isd = 2188;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 15;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 999;
        $this->pivotcost = 999;
		$this->iniativebonus = -15;
		
		$cA = new CargoBay(2, 20);
		$cB = new CargoBay(2, 20);
		$cC = new CargoBay(2, 20);
		$cD = new CargoBay(2, 20);
		
		$cA->displayName = "Cargo Bay A";
		$cB->displayName = "Cargo Bay B";
		$cC->displayName = "Cargo Bay C";
		$cD->displayName = "Cargo Bay D";		
		
        $this->addPrimarySystem(new Reactor(3, 3, 0, 0));
        $this->addPrimarySystem(new CnC(3, 5, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 10, 2, 2));
        $this->addPrimarySystem(new Engine(3, 18, 0, 12, 3));
		$this->addPrimarySystem(new Hangar(3, 2));
		$this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 0, 360));
		$this->addPrimarySystem(new Thruster(2, 13, 0, 4, 3));
		$this->addPrimarySystem(new Thruster(2, 13, 0, 4, 4));
		
        $this->addFrontSystem(new Thruster(2, 8, 0, 4, 1));
        $this->addFrontSystem(new Thruster(2, 8, 0, 4, 1));
        $this->addFrontSystem($cA);
        $this->addFrontSystem($cB);
        
        $this->addAftSystem(new Thruster(2, 12, 0, 4, 2));
        $this->addAftSystem(new Thruster(2, 12, 0, 4, 2));
        $this->addAftSystem(new Thruster(2, 12, 0, 4, 2));
        $this->addAftSystem($cC);
        $this->addAftSystem($cD);
       
        $this->addPrimarySystem(new Structure( 3, 56));
        
        $this->hitChart = array(
        		0=> array(
        				7 => "Thruster",
        				9 => "Standard Particle Beam",
        				12 => "Scanner",
        				15 => "Engine",
        				17 => "Hangar",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				5 => "Thruster",
        				8 => "Cargo Bay A",
        				11 => "Cargo Bay B",
        				17 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				5 => "Thruster",
        				8 => "Cargo Bay C",
        				11 => "Cargo Bay D",
        				17 => "Structure",
        				20 => "Primary",
        		),
        );
    }
}
?>
