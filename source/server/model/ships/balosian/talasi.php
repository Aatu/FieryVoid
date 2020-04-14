<?php
class Talasi extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 400;
		$this->faction = "Balosian";
        $this->phpclass = "Talasi";
        $this->imagePath = "img/ships/Talasi.png";
        $this->shipClass = "Talasi Assault Ship";
        $this->shipSizeClass = 3;
        $this->fighters = array("assault shuttles"=>12);
	    $this->isd = 2224;
		
        $this->forwardDefense = 14;
        $this->sideDefense = 15;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 4;
        $this->pivotcost = 4;

        
        $this->addPrimarySystem(new Reactor(5, 13, 0, 0));
        $this->addPrimarySystem(new CnC(5, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 10, 3, 6));
        $this->addPrimarySystem(new Engine(5, 20, 0, 8, 4));
        $this->addPrimarySystem(new Hangar(5, 16));
        
        $this->addFrontSystem(new IonCannon(3, 6, 4, 240, 0));
		$this->addFrontSystem(new IonCannon(3, 6, 4, 0, 120));
        $this->addFrontSystem(new StdParticleBeam(3, 4, 1, 180, 60));
        $this->addFrontSystem(new StdParticleBeam(3, 4, 1, 300, 180));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));    
		
        $this->addAftSystem(new StdParticleBeam(3, 4, 1, 120, 0));
        $this->addAftSystem(new StdParticleBeam(3, 4, 1, 0, 240));
        $this->addAftSystem(new Thruster(4, 13, 0, 5, 2));
        $this->addAftSystem(new Thruster(4, 13, 0, 5, 2));
		
        $this->addLeftSystem(new CargoBay(4, 40));
        $this->addLeftSystem(new Thruster(4, 15, 0, 4, 3));
		
        $this->addRightSystem(new CargoBay(4, 40));
        $this->addRightSystem(new Thruster(4, 15, 0, 4, 4));
		
		
        $this->addFrontSystem(new Structure(5, 44));
        $this->addAftSystem(new Structure(5, 44));
        $this->addLeftSystem(new Structure(5, 48));
        $this->addRightSystem(new Structure(5, 48));
        $this->addPrimarySystem(new Structure(5, 44));
		
		
		$this->hitChart = array(
			0=> array(
				9 => "Structure",
				12 => "Scanner",
				15 => "Engine",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				3 => "Thruster",
				5 => "Standard Particle Beam",
				9 => "Ion Cannon",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				8 => "Thruster",
				10 => "Standard Particle Beam",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				3 => "Thruster",
				9 => "Cargo Bay",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				3 => "Thruster",
				9 => "Cargo Bay",
				18 => "Structure",
				20 => "Primary",
			),
		);
    }
}



?>