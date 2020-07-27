<?php
class Felucca extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 225;
		$this->faction = "Raiders";
        $this->phpclass = "Felucca";
        $this->imagePath = "img/ships/fastFreighter.png"; 
        $this->shipClass = "Felucca";
        $this->canvasSize = 100;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 15;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 999;
        $this->pivotcost = 999;
    	$this->iniativebonus = 40;
         
        $this->addPrimarySystem(new Reactor(4, 7, 0, 0));
        $this->addPrimarySystem(new CnC(4, 5, 0, 0));
        $this->addPrimarySystem(new Engine(4, 20, 0, 15, 3));
        $this->addPrimarySystem(new Scanner(4, 12, 3, 5));
        $this->addPrimarySystem(new Hangar(3, 2));
    	$this->addPrimarySystem(new Thruster(4, 15, 0, 7, 3));
    	$this->addPrimarySystem(new Thruster(4, 15, 0, 7, 4));
    	$this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 0, 360));
		
		$temp1 = new CargoBay(3, 20);
			$temp1->displayName = "Cargo Bay A";
		$temp2 = new CargoBay(3, 20);
			$temp2->displayName = "Cargo Bay B";
		$temp3 = new CargoBay(3, 20);
			$temp3->displayName = "Cargo Bay C";
		$temp4 = new CargoBay(3, 20);
			$temp4->displayName = "Cargo Bay D";
		   	
    	$this->addFrontSystem(new Thruster(4, 8, 0, 5, 1));
		$this->addFrontSystem(new Thruster(4, 8, 0, 5, 1));
		$this->addFrontSystem(new StdParticleBeam(2, 4, 1, 180, 360));
		$this->addFrontSystem(new StdParticleBeam(2, 4, 1, 0, 180));
		$this->addFrontSystem($temp1);
		$this->addFrontSystem($temp2);
		
		$this->addAftSystem(new Thruster(4, 12, 0, 5, 2));
		$this->addAftSystem(new Thruster(4, 12, 0, 5, 2));
		$this->addAftSystem(new Thruster(4, 12, 0, 5, 2));
		$this->addAftSystem($temp3);
		$this->addAftSystem($temp4);
		
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
        				7 => "Standard Particle Beam",
        				9 => "Cargo Bay A",
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
