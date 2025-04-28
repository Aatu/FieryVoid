<?php
class CivilianTanker extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 130;
		$this->faction = "Civilians";
        $this->phpclass = "CivilianTanker";
        $this->imagePath = "img/ships/civilianTanker.png";
        $this->shipClass = "Civilian Tanker";
        $this->canvasSize = 100;
	    $this->isCombatUnit = false; //not a combat unit, it will never be present in a regular battlegroup
		$this->isd = 2162;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 14;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 999;
        $this->pivotcost = 999;
		$this->iniativebonus = -30;
		
		$cA = new CargoBay(2, 20);
		$cB = new CargoBay(2, 20);
		$cC = new CargoBay(2, 20);
		$cD = new CargoBay(2, 20);
        $cE = new CargoBay(2, 20);
        $cF = new CargoBay(2, 20);
		
		$cA->displayName = "Cargo Bay A";
		$cB->displayName = "Cargo Bay B";
		$cC->displayName = "Cargo Bay C";
		$cD->displayName = "Cargo Bay D";
		$cE->displayName = "Cargo Bay E";
		$cF->displayName = "Cargo Bay F";
         
        $this->addPrimarySystem(new Reactor(3, 3, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 8, 2, 2));
		$this->addPrimarySystem(new Thruster(2, 10, 0, 2, 3));
		$this->addPrimarySystem(new Thruster(2, 10, 0, 2, 4));
		
        $this->addFrontSystem(new Thruster(2, 6, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 6, 0, 2, 1));
        $this->addFrontSystem(new CnC(3, 5, 0, 0));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 2, 240, 120));
        $this->addFrontSystem($cA);
        $this->addFrontSystem($cB);
        $this->addFrontSystem($cC);
		
        $this->addAftSystem(new Thruster(2, 8, 0, 2, 2));
        $this->addAftSystem(new Thruster(2, 8, 0, 2, 2));
        $this->addAftSystem(new Hangar(3, 4));
        $this->addAftSystem(new Engine(3, 6, 0, 4, 4));
        $this->addAftSystem(new StdParticleBeam(2, 4, 2, 60, 300));
        $this->addAftSystem($cD);
        $this->addAftSystem($cE);
        $this->addAftSystem($cF);
       
        $this->addPrimarySystem(new Structure( 3, 52));
        
        $this->hitChart = array(
        		0=> array(
        				14 => "Thruster",
        				17 => "Scanner",
        				20 => "Reactor",
        		),
        		1=> array(
        				4 => "Thruster",
        				6 => "Cargo Bay A",
        				8 => "Cargo Bay B",
        				10 => "Cargo Bay C",
        				11 => "Standard Particle Beam",
        				12 => "C&C",
        				17 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				4 => "Thruster",
        				6 => "Cargo Bay D",
        				8 => "Cargo Bay E",
        				10 => "Cargo Bay F",
        				11 => "Standard Particle Beam",
        				12 => "Engine",
        				13 => "Hangar",
        				17 => "Structure",
        				20 => "Primary",
        		),
        );
    }
}
?>
