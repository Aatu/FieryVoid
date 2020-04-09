<?php

class Thosalsi extends BaseShip{
    
    function __construct($id, $userid, $name,  $movement){
        parent::__construct($id, $userid, $name,  $movement);
        
		$this->pointCost = 550;
		$this->faction = "Balosian";
        $this->phpclass = "Thosalsi";
        $this->imagePath = "img/ships/thosalsi.png";
        $this->shipClass = "Thosalsi Heavy Carrier";
        $this->shipSizeClass = 3;
        $this->limited = 10;
        $this->fighters = array("medium"=>36);
		$this->isd = 2223;
        
        $this->forwardDefense = 16;
        $this->sideDefense = 16;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 3;

        $this->addPrimarySystem(new Reactor(7, 15, 0, 0));
        $this->addPrimarySystem(new CnC(7, 20, 0, 0));
        $this->addPrimarySystem(new Scanner(7, 12, 4, 6));
        $this->addPrimarySystem(new Engine(6, 20, 0, 10, 3));
		$this->addPrimarySystem(new Hangar(7, 14));
        
		$this->addFrontSystem(new StdParticleBeam(3, 4, 1, 180, 60));
		$this->addFrontSystem(new StdParticleBeam(3, 4, 1, 300, 180));
        $this->addFrontSystem(new Thruster(6, 8, 0, 2, 1));
       
        $this->addAftSystem(new Thruster(5, 15, 0, 5, 2));
        $this->addAftSystem(new Thruster(5, 15, 0, 5, 2));
        $this->addAftSystem(new StdParticleBeam(3, 4, 1, 90, 270));
	
		$this->addLeftSystem(new Thruster(5, 15, 0, 5, 3));
		$this->addLeftSystem(new Thruster(5, 8, 0, 3, 1));
		$this->addLeftSystem(new StdParticleBeam(3, 4, 1, 180, 0));
		$this->addLeftSystem(new IonCannon(3, 6, 4, 180, 0));
        $this->addLeftSystem(new Hangar(5, 12));
		
		$this->addRightSystem(new Thruster(5, 15, 0, 5, 4));
		$this->addRightSystem(new Thruster(5, 8, 0, 3, 1));
		$this->addRightSystem(new StdParticleBeam(3, 4, 1, 0, 180));
		$this->addRightSystem(new IonCannon(3, 6, 4, 0, 180));
        $this->addRightSystem(new Hangar(5, 12));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 6, 40));
        $this->addAftSystem(new Structure( 5, 40));
        $this->addLeftSystem(new Structure( 5, 60));
        $this->addRightSystem(new Structure( 5, 60));
        $this->addPrimarySystem(new Structure( 7, 39));
		
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
                    2 => "Thruster",
                    8 => "Standard Particle Beam",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    7 => "Thruster",
		    9 => "Standard Particle Beam",
                    18 => "Structure",
                    20 => "Primary",
            ),
            3=> array(
                    4 => "Thruster",
		    6 => "Standard Particle Beam",
		    8 => "Ion Cannon",
		    11 => "Hangar",
                    18 => "Structure",
                    20 => "Primary",
            ),
            4=> array(
                    4 => "Thruster",
		    6 => "Standard Particle Beam",
		    8 => "Ion Cannon",
		    11 => "Hangar",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
