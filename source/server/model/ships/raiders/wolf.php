<?php
class Wolf extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
    	$this->pointCost = 450;
    	$this->faction = "Raiders";
        $this->phpclass = "Wolf";
        $this->imagePath = "img/ships/civilianFreighter.png"; //need to change this
        $this->shipClass = "Wolf Raider";
        $this->canvasSize = 100;
        
		$this->notes = "Generic raider unit.";
		$this->notes .= "<br> ";
		$this->notes .= "<br>More detailed deployment restrictions are in the Faction List document.";
		$this->notes .= "<br> ";

		$this->isd = 2200;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 15;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 999;
        $this->pivotcost = 999;
    	$this->iniativebonus = 0;
    	$this->fighters = array("light"=>6);
    	 
        $this->addPrimarySystem(new Reactor(4, 9, 0, 0));
        $this->addPrimarySystem(new Engine(4, 12, 0, 8, 3));
        $this->addPrimarySystem(new CnC(4, 9, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 4, 6));
        $this->addPrimarySystem(new Hangar(4, 4));
    	$this->addPrimarySystem(new Thruster(2, 10, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(2, 10, 0, 4, 4));
        $this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 0, 360));
		
        $this->addFrontSystem(new Thruster(2, 8, 0, 6, 1));
		$this->addFrontSystem(new ParticleCannon(3, 8, 7, 240, 60));
		$this->addFrontSystem(new ParticleCannon(3, 8, 7, 300, 120));
		$this->addFrontSystem(new StdParticleBeam(2, 4, 1, 180, 60));
		$this->addFrontSystem(new StdParticleBeam(2, 4, 1, 300, 180));
		$this->addFrontSystem(new CargoBay(3, 10));
		$this->addFrontSystem(new CargoBay(3, 10));
		$this->addFrontSystem(new Hangar(3, 2));
		$this->addFrontSystem(new Hangar(3, 2));
				
        $this->addAftSystem(new Thruster(2, 12, 0, 8, 2));
        $this->addAftSystem(new ParticleCannon(3, 8, 7, 120, 300));
        $this->addAftSystem(new ParticleCannon(3, 8, 7, 60, 240));
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 120, 360));
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 0, 240));
        $this->addAftSystem(new CargoBay(3, 20));
        $this->addAftSystem(new CargoBay(3, 20));
        
        $this->addPrimarySystem(new Structure( 4, 56));
        
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
        				5 => "Particle Cannon",
        				7 => "Standard Particle Beam",
        				9 => "Cargo Bay",
        				11 => "Hangar",
        				17 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				3 => "Thruster",
        				5 => "Particle Cannon",
        				7 => "Standard Particle Beam",
        				11 => "Cargo Bay",
        				17 => "Structure",
        				20 => "Primary",
        		),
        );
        
    }

}



?>
