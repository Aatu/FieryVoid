<?php
class gaimGrastRefit extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 400;
		$this->faction = "Gaim";
        $this->phpclass = "gaimGrastRefit";
        $this->imagePath = "img/ships/GaimGrast.png";
        $this->shipClass = "Grast Support Frigate (Refit)";
			$this->variantOf = "Grast Support Frigate";
			$this->occurence = "common";
        $this->canvasSize = 100;
			$this->limited = 33;

        $this->isd = 2255;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 14;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 999;
        $this->pivotcost = 999;
		$this->iniativebonus = 0;
		
        $this->addPrimarySystem(new Reactor(5, 17, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 14, 5, 6));
		$this->addPrimarySystem(new CnC(4, 6, 0, 0));
        $this->addPrimarySystem(new Hangar(2, 1));
		$this->addPrimarySystem(new Thruster(3, 10, 0, 4, 3));
		$this->addPrimarySystem(new Thruster(3, 10, 0, 4, 4));
		
        $this->addFrontSystem(new Thruster(3, 6, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 6, 0, 3, 1));
        $this->addFrontSystem(new StdParticleBeam(3, 4, 1, 240, 120));
		$this->addFrontSystem(new ParticleConcentrator(3, 9, 7, 300, 60));
		$this->addFrontSystem(new ParticleConcentrator(3, 9, 7, 300, 60));
		$this->addFrontSystem(new Bulkhead(0, 4));
		$this->addFrontSystem(new Bulkhead(0, 4));
		
        $this->addAftSystem(new Thruster(2, 8, 0, 4, 2));
        $this->addAftSystem(new Thruster(2, 8, 0, 4, 2));
        $this->addAftSystem(new Engine(5, 15, 0, 8, 3));
        $this->addAftSystem(new StdParticleBeam(3, 4, 1, 60, 300));
		$this->addAftSystem(new CargoBay(3, 24));
		$this->addAftSystem(new CargoBay(3, 24));
		$this->addAftSystem(new Bulkhead(0, 4));
       
        $this->addPrimarySystem(new Structure( 4, 52));
        
        $this->hitChart = array(
        		0=> array(
        				11 => "Thruster",
						12 => "Hangar",
						15 => "Reactor",
        				18 => "Scanner",
        				20 => "C&C",
        		),
        		1=> array(
        				5 => "Thruster",
        				9 => "Particle Concentrator",
        				11 => "Standard Particle Beam",
        				17 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				4 => "Thruster",
        				9 => "Cargo Bay",
        				11 => "Standard Particle Beam",
        				12 => "Engine",
        				17 => "Structure",
        				20 => "Primary",
        		),
        );
    }
}
?>
