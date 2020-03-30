<?php

class Resha extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 325;
		$this->faction = "Balosian";
        $this->phpclass = "Resha";
        $this->imagePath = "img/ships/resha.png";
        $this->shipClass = "Resha Patrol Frigate";
        $this->agile = true;
        $this->canvasSize = 100;
		$this->isd = 2245;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 13;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
		$this->iniativebonus = 60;
        
        $this->addPrimarySystem(new Reactor(4, 10, 0, 0));
        $this->addPrimarySystem(new CnC(4, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(4,10, 3, 5));
        $this->addPrimarySystem(new Engine(4, 10, 0, 8, 2));
		$this->addPrimarySystem(new Hangar(4, 1));
		$this->addPrimarySystem(new Thruster(3, 11, 0, 4, 3));
		$this->addPrimarySystem(new Thruster(3, 11, 0, 4, 4));

        $this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));
        $this->addFrontSystem(new StdParticleBeam(3, 4, 1, 180, 0));
        $this->addFrontSystem(new StdParticleBeam(3, 4, 1, 180, 0));
		$this->addFrontSystem(new IonCannon(4, 6, 4, 240, 0));
		$this->addFrontSystem(new IonCannon(4, 6, 4, 0, 120));
        $this->addFrontSystem(new StdParticleBeam(3, 4, 1, 0, 180));
        $this->addFrontSystem(new StdParticleBeam(3, 4, 1, 0, 180));
		
        $this->addAftSystem(new Thruster(4, 14, 0, 8, 2));
       
        $this->addPrimarySystem(new Structure( 4, 36));
		
		$this->hitChart = array(
            0=> array(
					9 => "Thruster",
					12 => "Scanner",
                    15 => "Engine",
                    16 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    3 => "Thruster",
                    7 => "Ion Cannon",
                    10 => "Standard Particle Beam",
                    17 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    8 => "Thruster",
                    17 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
