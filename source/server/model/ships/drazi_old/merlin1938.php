<?php
class Merlin1938 extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
    	$this->pointCost = 300;
        $this->faction = "Drazi Freehold (WotCR)";
        $this->phpclass = "Merlin1938";
        $this->imagePath = "img/ships/merlin.png";
        $this->shipClass = "Merlin Frigate (Early)";
			$this->occurence = 'common'; 
			$this->variantOf = "Merlin Frigate";
	    $this->isd = 1938;
        $this->agile = true;
        $this->canvasSize = 100;
        
        $this->forwardDefense = 11;
        $this->sideDefense = 11;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
    	$this->iniativebonus = 70;
        
        $this->addPrimarySystem(new Reactor(4, 11, 0, 0));
        $this->addPrimarySystem(new CnC(4, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 10, 3, 4));
        $this->addPrimarySystem(new Engine(3, 10, 0, 6, 2));
    	$this->addPrimarySystem(new Hangar(3, 1));
    	$this->addPrimarySystem(new Thruster(3, 10, 0, 4, 3));
    	$this->addPrimarySystem(new Thruster(3, 10, 0, 4, 4));
		
        $this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 240, 60));
    	$this->addFrontSystem(new MediumPlasma(3, 5, 3, 240, 360));
    	$this->addFrontSystem(new MediumPlasma(3, 5, 3, 0, 120));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 300, 120));
		
        $this->addAftSystem(new Thruster(4, 12, 0, 6, 2));
		
        $this->addPrimarySystem(new Structure( 4, 33));
              
        $this->hitChart = array(
        		0=> array(
        				8=> "Thruster",
        				11=> "Scanner",
        				14=> "Engine",
        				16=> "Hangar",
        				18=> "Reactor",
        				20=> "C&C",
        		),
        		1=> array(
        				5=> "Thruster",
        				7=> "Medium Plasma Cannon",
        				10=> "Standard Particle Beam",
        				17=> "Structure",
        				20=> "Primary",
        		),
        		2=> array(
        				8=> "Thruster",
        				17=> "Structure",
        				20=> "Primary",
        		),
        );
	    
    }
}
?>