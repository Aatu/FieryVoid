<?php
class SixTester extends SixSidedShip{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 1500;
		$this->faction = "Custom Ships";
		$this->phpclass = "SixTester";
		$this->shipClass = "SixTester";
		$this->fighters = array("heavy"=>36); 
		$this->isd = 2202;
		$this->locations = array(41, 42, 2, 32, 31, 1);		

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


		//$bioDrive = new BioDrive(); //BioDrive just is, all parameters needed are calculated automatically
		
		//$bioThruster = new BioThruster(7,30,20);
		//$bioDrive->addThruster($bioThruster);
		//$this->addPrimarySystem($bioThruster);	
		
				
		$this->addPrimarySystem(new Reactor(7, 35, 0, 0));
		$this->addPrimarySystem(new Hangar(7, 14, 12));
		$this->addPrimarySystem(new Hangar(7, 14, 12));
		$this->addPrimarySystem(new Hangar(7, 14, 12));
		$this->addPrimarySystem(new CnC(8, 60, 0, 0));
		$this->addPrimarySystem(new Scanner(7, 32, 5, 10));
		$this->addPrimarySystem(new JumpEngine(6, 25, 3, 16));		

		$this->addFrontSystem(new BattleLaser(3, 6, 2, 300, 60));
        $this->addFrontSystem(new BattleLaser(3, 6, 2, 300, 60));
        $this->addFrontSystem(new TwinArray(3, 6, 2, 240, 120));
		$this->addFrontSystem(new TwinArray(3, 6, 2, 240, 120));
        $this->addFrontSystem(new Thruster(6, 10, 0, 5, 1));
        $this->addFrontSystem(new Thruster(6, 10, 0, 5, 1));
		
        $this->addAftSystem(new BattleLaser(5, 6, 6, 120, 40));
		$this->addAftSystem(new BattleLaser(5, 6, 6, 120, 40));
		$this->addAftSystem(new TwinArray(3, 6, 2, 120, 240));
		$this->addAftSystem(new TwinArray(3, 6, 2, 120, 240));
		$this->addAftSystem(new Thruster(5, 8, 0, 5, 2));
        $this->addAftSystem(new Thruster(5, 8, 0, 5, 2));
        
		$this->addLeftFrontSystem(new BattleLaser(5, 6, 6, 240, 0));
		$this->addLeftFrontSystem(new BattleLaser(5, 6, 6, 240, 0));
		$this->addLeftFrontSystem(new TwinArray(3, 6, 2, 240, 0));
		$this->addLeftFrontSystem(new TwinArray(3, 6, 2, 240, 0));
		$this->addLeftFrontSystem(new Thruster(5, 15, 0, 5, 4));
				
		$this->addLeftAftSystem(new BattleLaser(5, 6, 6, 180, 300));
		$this->addLeftAftSystem(new BattleLaser(5, 6, 6, 180, 300));
		$this->addLeftAftSystem(new TwinArray(3, 6, 2, 180, 300));
		$this->addLeftAftSystem(new TwinArray(3, 6, 2, 180, 300));
		$this->addLeftAftSystem(new Thruster(5, 15, 0, 5, 4));
		
		$this->addRightFrontSystem(new BattleLaser(5, 6, 6, 0, 120));
		$this->addRightFrontSystem(new BattleLaser(5, 6, 6, 0, 120));
		$this->addRightFrontSystem(new TwinArray(3, 6, 2, 0, 120));
		$this->addRightFrontSystem(new TwinArray(3, 6, 2, 0, 120));
		$this->addRightFrontSystem(new Thruster(5, 15, 0, 5, 3));
				
		$this->addRightAftSystem(new BattleLaser(5, 6, 6, 60, 180));
		$this->addRightAftSystem(new BattleLaser(5, 6, 6, 60, 1800));
		$this->addRightAftSystem(new TwinArray(3, 6, 2, 60, 180));
		$this->addRightAftSystem(new TwinArray(3, 6, 2, 60, 180));
		$this->addRightAftSystem(new Thruster(5, 15, 0, 5, 3));		
		
       
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 60));
        $this->addAftSystem(new Structure( 5, 60));
        $this->addLeftSystem(new Structure( 5, 60));
        $this->addRightSystem(new Structure( 5, 60));
        $this->addLeftSystem(new Structure( 5, 60));
        $this->addRightSystem(new Structure( 5, 60));        
        $this->addPrimarySystem(new Structure( 6, 80));
	    
	//d20 hit chart
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
            1=> array( //Forward
                    3 => "Thruster",
                    7 => "Battle Laser",
                    11 => "Twin Array",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            2=> array( //Aft
                    3 => "Thruster",
                    7 => "Battle Laser",
                    11 => "Twin Array",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            31=> array( //Port
                    3 => "Thruster",
                    7 => "Battle Laser",
                    11 => "Twin Array",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            32=> array( //Port
                     3 => "Thruster",
                    7 => "Battle Laser",
                    11 => "Twin Array",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            41=> array( //Starboard
                    3 => "Thruster",
                    7 => "Battle Laser",
                    11 => "Twin Array",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
       		42=> array( //Starboard
                    3 => "Thruster",
                    7 => "Battle Laser",
                    11 => "Twin Array",
                    18 => "Structure",
                    20 => "Primary",
           		
                    
            	),
           	);
       		
		}
	}
		
?>		
