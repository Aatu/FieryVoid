<?php
class SixTester1 extends SixSidedShip{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 1500;
		$this->faction = "Custom Ships";
		$this->phpclass = "SixTester1";
		$this->shipClass = "SixTester1";
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
		$this->canvasSize = 280;
		$this->locations = array(41, 42, 2, 32, 31, 1);	
			
        $this->hitChart = array(
            0=> array( //PRIMARY
                    8 => "Structure",
                    10 => "Jump Engine",
                    12 => "Scanner",
                    15 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
           		 ),

		//$bioDrive = new BioDrive(); //BioDrive just is, all parameters needed are calculated automatically
		
		//$bioThruster = new BioThruster(7,30,20);
		//$bioDrive->addThruster($bioThruster);
		//$this->addPrimarySystem($bioThruster);	
		
				
		$this->addPrimarySystem(new Reactor(6, 30, 0, 0));
		$this->addPrimarySystem(new Hangar(6, 36));
		$this->addPrimarySystem(new CnC(6, 30, 0, 0));
		$this->addPrimarySystem(new Scanner(6, 24, 5, 8));
		$this->addPrimarySystem(new JumpEngine(6, 30, 3, 16));
		
		$this->addPrimarySystem(new Structure( 6, 80));		

		for ($i = 0; $i < sizeof($this->locations); $i++){
			$min = 0 + ($i*60);
			$max = 120 + ($i*60);
			$systems = array(
				new BattleLaser(5, 6, 6, $min, $max),
				new BattleLaser(5, 6, 6, $min, $max),
				new TwinArray(3, 6, 2, $min, $max),
				new TwinArray(3, 6, 2, $min, $max),
				new Thruster(5, 15, 0, $min, $max),
				new Structure(5, 60)
			);
			$loc = $this->locations[$i];
			$this->hitChart[$loc] = array(
                    3 => "Thruster",
                    7 => "Battle Laser",
                    11 => "Twin Array",
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
