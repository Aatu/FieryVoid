<?php
class CylonBasestar extends SixSidedShip{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 1500;
		$this->faction = "zBSGCylons";
		$this->phpclass = "SixTester";
		$this->shipClass = "SixTester";
		$this->fighters = array("heavy"=>36); 
		$this->isd = 2202;
		$this->locations = array(41, 42, 2, 32, 31, 1);		

		$this->shipSizeClass = 3; //Enormous is not implemented
		$this->iniativebonus = 0; //no voluntary movement anyway
		
        $this->turncost = 1.25;
        $this->turndelaycost = 1.25;
        $this->accelcost = 4;
        $this->rollcost = 999;
        $this->pivotcost = 2;	
        $this->gravitic = true;        	

		$this->forwardDefense = 19;
		$this->sideDefense = 19;

		$this->imagePath = "img/ships/CylonBasestar.png";
		$this->canvasSize = 300;

		$this->addPrimarySystem(new Reactor(6, 35, 0, 0));
		$this->addPrimarySystem(new Hangar(6, 36));
		$this->addPrimarySystem(new CnC(6, 30, 0, 0));
		$this->addPrimarySystem(new Scanner(6, 24, 5, 8));
        $this->addPrimarySystem(new Engine(6, 25, 0, 15, 3));			
		$this->addPrimarySystem(new JumpEngine(6, 25, 3, 16));		

		$this->addFrontSystem(new LHMissileRack(4, 8, 0, 300, 60));
        $this->addFrontSystem(new LHMissileRack(4, 8, 0,  300, 60));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 120, 240));
		$this->addFrontSystem(new StdParticleBeam(2, 4, 1, 120, 240));
        $this->addFrontSystem(new Thruster(6, 10, 0, 5, 1));
        $this->addFrontSystem(new Thruster(6, 10, 0, 5, 1));
		
        $this->addAftSystem(new LHMissileRack(4, 8, 0, 120, 240));
		$this->addAftSystem(new LHMissileRack(4, 8, 0, 120, 240));
		$this->addAftSystem(new StdParticleBeam(2, 4, 1, 120, 240));
		$this->addAftSystem(new StdParticleBeam(2, 4, 1, 120, 240));
		$this->addAftSystem(new Thruster(5, 8, 0, 5, 2));
        $this->addAftSystem(new Thruster(5, 8, 0, 5, 2));
        
		$this->addLeftFrontSystem(new LHMissileRack(4, 8, 0, 240, 0));
		$this->addLeftFrontSystem(new LHMissileRack(4, 8, 0, 240, 0));
		$this->addLeftFrontSystem(new StdParticleBeam(2, 4, 1, 240, 0));
		$this->addLeftFrontSystem(new StdParticleBeam(2, 4, 1, 240, 0));
		$this->addLeftFrontSystem(new Thruster(5, 15, 0, 5, 3));
				
		$this->addLeftAftSystem(new LHMissileRack(4, 8, 0, 180, 300));
		$this->addLeftAftSystem(new LHMissileRack(4, 8, 0, 180, 300));
		$this->addLeftAftSystem(new StdParticleBeam(2, 4, 1, 180, 300));
		$this->addLeftAftSystem(new StdParticleBeam(2, 4, 1, 180, 300));
		$this->addLeftAftSystem(new Thruster(5, 15, 0, 5, 3));
		
		$this->addRightFrontSystem(new LHMissileRack(4, 8, 0, 0, 120));
		$this->addRightFrontSystem(new LHMissileRack(4, 8, 0, 0, 120));
		$this->addRightFrontSystem(new StdParticleBeam(2, 4, 1, 0, 120));
		$this->addRightFrontSystem(new StdParticleBeam(2, 4, 1, 0, 120));
		$this->addRightFrontSystem(new Thruster(5, 15, 0, 5, 4));
				
		$this->addRightAftSystem(new LHMissileRack(4, 8, 0, 60, 180));
		$this->addRightAftSystem(new LHMissileRack(4, 8, 0, 60, 180));
		$this->addRightAftSystem(new StdParticleBeam(2, 4, 1, 60, 180));
		$this->addRightAftSystem(new StdParticleBeam(2, 4, 1, 60, 180));
		$this->addRightAftSystem(new Thruster(5, 15, 0, 5, 4));		
		
       
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 60));
        $this->addAftSystem(new Structure( 5, 60));
        $this->addLeftFrontSystem(new Structure( 5, 60));
        $this->addLeftAftSystem(new Structure( 5, 60));
        $this->addRightFrontSystem(new Structure( 5, 60));
        $this->addRightAftSystem(new Structure( 5, 60));        
        $this->addPrimarySystem(new Structure( 6, 80));
	    
	//d20 hit chart
        $this->hitChart = array(
            0=> array(
                    8 => "Structure",
                    10 => "Jump Engine",
                    12 => "Scanner",
                    15 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
           		 ),
            1=> array(
                    4 => "Thruster",
                    7 => "Class-LH Missile Rack",
                    11 => "Standard Particle Beam",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            2=> array(
                    4 => "Thruster",
                    7 => "Class-LH Missile Rack",
                    11 => "Standard Particle Beam",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            31=> array(
                    4 => "Thruster",
                    7 => "Class-LH Missile Rack",
                    11 => "Standard Particle Beam",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            32=> array(
                    4 => "Thruster",
                    7 => "Class-LH Missile Rack",
                    11 => "Standard Particle Beam",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            41=> array(
                    4 => "Thruster",
                    7 => "Class-LH Missile Rack",
                    11 => "Standard Particle Beam",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
       		42=> array(
                    4 => "Thruster",
                    7 => "Class-LH Missile Rack",
                    11 => "Standard Particle Beam",
                    18 => "Structure",
                    20 => "Primary",   
            	),
           	);
       		
		}
	}
		
?>		
