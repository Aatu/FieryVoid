<?php
class lightAuxCarrier extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 120;
		$this->faction = "Civilians";
        $this->phpclass = "lightAuxCarrier";
        $this->imagePath = "img/ships/civilianFreighter.png";
        $this->shipClass = "Light Auxiliary Carrier";
			$this->variantOf = "Commercial Freighter";	    
			$this->occurence = "common";
        $this->canvasSize = 100;

        $this->fighters = array("normal"=>6);
        
        $this->forwardDefense = 13;
        $this->sideDefense = 15;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 999;
        $this->pivotcost = 999;
		$this->iniativebonus = -20;
		
		$cA = new CargoBay(2, 20);
		$cB = new CargoBay(2, 20);
		$cC = new CargoBay(2, 20);
		$cD = new CargoBay(2, 20);
		$cE = new CargoBay(2, 20);
		$cF = new CargoBay(2, 20);
		$cG = new CargoBay(2, 20);
		$cH = new CargoBay(2, 20);
		
		$cA->displayName = "Cargo Bay A";
		$cB->displayName = "Cargo Bay B";
		$cC->displayName = "Cargo Bay C";
		$cD->displayName = "Cargo Bay D";
		$cE->displayName = "Cargo Bay E";
		$cF->displayName = "Cargo Bay F";
		$cG->displayName = "Cargo Bay G";
		$cH->displayName = "Cargo Bay H";
         
        $this->addPrimarySystem(new Reactor(3, 3, 0, 0));
        $this->addPrimarySystem(new CnC(3, 5, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 10, 2, 2));
        $this->addPrimarySystem(new Engine(3, 6, 0, 4, 3));
		$this->addPrimarySystem(new Hangar(3, 4));
		$this->addPrimarySystem(new StdParticleBeam(2, 4, 2, 0, 360));
		$this->addPrimarySystem(new Thruster(2, 10, 0, 2, 3));
		$this->addPrimarySystem(new Thruster(2, 10, 0, 2, 4));
		
        $this->addFrontSystem(new Thruster(2, 8, 0, 4, 1));
        $this->addFrontSystem($cA);
        $this->addFrontSystem($cB);
        $this->addFrontSystem($cC);
        $this->addFrontSystem($cD);
		
        $this->addAftSystem(new Thruster(2, 12, 0, 4, 2));
        $this->addAftSystem($cE);
        $this->addAftSystem($cF);
        $this->addAftSystem($cG);
        $this->addAftSystem($cH);
       
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
        				3 => "Thruster",
        				5 => "Cargo Bay A",
        				7 => "Cargo Bay B",
        				9 => "Cargo Bay C",
        				11 => "Cargo Bay D",
        				17 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				3 => "Thruster",
        				5 => "Cargo Bay E",
        				7 => "Cargo Bay F",
        				9 => "Cargo Bay G",
        				11 => "Cargo Bay H",
        				17 => "Structure",
        				20 => "Primary",
        		),
        );
    }
}
?>
