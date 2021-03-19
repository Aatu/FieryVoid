<?php
class SixTester2 extends SixSidedShip{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 1500;
		$this->faction = "Custom Ships";
		$this->phpclass = "SixTester2";
		$this->shipClass = "SixTester2";
		$this->fighters = array("heavy"=>36); 
		$this->isd = 2202;

		$this->shipSizeClass = 3; //Enormous is not implemented
		$this->iniativebonus = 0; //no voluntary movement anyway
		
        $this->turncost = 1.50;
        $this->turndelaycost = 1.50;
        $this->accelcost = 4;
        $this->rollcost = 999;
        $this->pivotcost = 0;		

		$this->forwardDefense = 20;
		$this->sideDefense = 20;

		$this->imagePath = "img/ships/CylonBasestar.png";
		$this->canvasSize = 300;
		$this->locations = array(41, 42, 2, 32, 31, 1);	
			
        $this->hitChart = array(
            0=> array( //PRIMARY
                    3 => 'Thruster',
                    8 => "Structure",
                    10 => "Jump Engine",
                    12 => "Scanner",
                    15 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
			),
		);
		
		$this->addPrimarySystem(new Reactor(6, 30, 0, 0));
		$this->addPrimarySystem(new Hangar(6, 36));
		$this->addPrimarySystem(new CnC(6, 30, 0, 0));
		$this->addPrimarySystem(new Scanner(6, 24, 5, 8));
		$this->addPrimarySystem(new JumpEngine(6, 30, 3, 16));
        $this->addPrimarySystem(new Engine(6, 25, 0, 15, 3));			
		$this->addPrimarySystem(new Thruster(6, 20, 0, 8, 1));
		$this->addPrimarySystem(new Thruster(6, 20, 0, 8, 2));	
		$this->addPrimarySystem(new Thruster(6, 20, 0, 8, 3));	
		$this->addPrimarySystem(new Thruster(6, 20, 0, 8, 4));									
		
		$this->addPrimarySystem(new Structure( 6, 80));		

		for ($i = 0; $i < sizeof($this->locations); $i++){
			$min = 0 + ($i*60);
			$max = 120 + ($i*60);
			$systems = array(
				new LHMissileRack(4, 8, 0, $min, $max),
				new LHMissileRack(4, 8, 0, $min, $max),
				new StdParticleBeam(2, 4, 1, $min, $max),
				new StdParticleBeam(2, 4, 1, $min, $max),
				new Structure(5, 60)
			);
			$loc = $this->locations[$i];
			$this->hitChart[$loc] = array(
                    7 => "Class-LH Missile Rack",
                    11 => "Standard Particle Beam",
                    18 => "Structure",
                    20 => "Primary",
			);
			foreach ($systems as $system){
				$this->addSystem($system, $loc);
			}
		}
		
		
      		
		}
	}
		
?>		
