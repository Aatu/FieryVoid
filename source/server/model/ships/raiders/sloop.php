<?php
class Sloop extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 380;
	$this->faction = "Raiders";
        $this->phpclass = "Sloop";
        $this->imagePath = "img/ships/sloop.png";
        $this->shipClass = "Sloop";
        $this->canvasSize = 100;
        
        $this->forwardDefense = 11;
        $this->sideDefense = 11;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 1;
        $this->rollcost = 1;
        $this->pivotcost = 2;
    	$this->iniativebonus = 60;
    	$this->fighters = array("light"=>6);
         
        $this->addPrimarySystem(new Reactor(3, 15, 0, 0));
        $this->addPrimarySystem(new CnC(4, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 12, 4, 5));
        $this->addPrimarySystem(new CargoBay(3, 18));
        $this->addPrimarySystem(new Hangar(3, 2));
    	$this->addPrimarySystem(new Thruster(3, 10, 0, 3, 3));
    	$this->addPrimarySystem(new Thruster(3, 10, 0, 3, 4));
		
        $this->addFrontSystem(new Thruster(3, 6, 0, 2, 1));
        $this->addFrontSystem(new Thruster(3, 6, 0, 2, 1));
        $this->addFrontSystem(new MediumPulse(3, 6, 3, 300, 60));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 240, 60));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 300, 120));
        $this->addFrontSystem(new MediumPulse(3, 6, 3, 300, 60));		

        $this->addAftSystem(new Engine(3, 11, 0, 6, 2));
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 180, 360));
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 0, 180));
        $this->addAftSystem(new Thruster(3, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 8, 0, 3, 2));
	
        $this->addPrimarySystem(new Structure( 4, 46));
        
        $this->hitChart = array(
        		0=> array(
        				1 => "thruster",
        				2 => "thruster",
        				3 => "thruster",
        				4 => "thruster",
        				5 => "thruster",
        				6 => "thruster",
        				7 => "thruster",
        				8 => "thruster",
        				9 => "thruster",
        				10 => "thruster",
        				11 => "cargo Bay",
        				12 => "cargo Bay",
        				13 => "cargo Bay",
        				14 => "scanner",
        				15 => "scanner",
        				16 => "scanner",
        				17 => "hangar",
        				18 => "reactor",
        				19 => "reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				1 => "thruster",
        				2 => "thruster",
        				3 => "thruster",
        				4 => "thruster",
        				5 => "thruster",
        				6 => "thruster",
        				7 => "medium Pulse",
        				8 => "medium Pulse",
        				9 => "Standard Particle Beam",
        				10 => "Standard Particle Beam",
        				11 => "structure",
        				12 => "structure",
        				13 => "structure",
        				14 => "structure",
        				15 => "structure",
        				16 => "structure",
        				17 => "structure",
        				18 => "primary",
        				19 => "primary",
        				20 => "primary",
        		),
        		2=> array(
        				1 => "thruster",
        				2 => "thruster",
        				3 => "thruster",
        				4 => "thruster",
        				5 => "thruster",
        				6 => "thruster",
        				7 => "Standard Particle Beam",
        				8 => "Standard Particle Beam",
        				9 => "engine",
        				10 => "structure",
        				11 => "structure",
        				12 => "structure",
        				13 => "structure",
        				14 => "structure",
        				15 => "structure",
        				16 => "structure",
        				17 => "structure",
        				18 => "primary",
        				19 => "primary",
        				20 => "primary",
        		),
        );
    }

}



?>
