<?php
class SixTester extends SixSidedShip{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 5000;
		$this->faction = "customs";
		$this->phpclass = "SixTester";
		$this->shipClass = "SixTester";
		$this->fighters = array("heavy"=>36); 
		$this->isd = 2202;

		$this->shipSizeClass = 3; //Enormous is not implemented
		$this->iniativebonus = 0; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;
		
        $this->turncost = 1.50;
        $this->turndelaycost = 1.50;
        $this->accelcost = 4;
        $this->rollcost = 999;
        $this->pivotcost = 0;		

		$this->forwardDefense = 24;
		$this->sideDefense = 24;

		$this->imagePath = "img/ships/kraken.png";
		$this->canvasSize = 280; //Enormous Starbase


		$bioDrive = new BioDrive(); //BioDrive just is, all parameters needed are calculated automatically
		
		$bioThruster = new BioThruster(7,30,20);
		$bioDrive->addThruster($bioThruster);
		$this->addPrimarySystem($bioThruster);	
		
				
		$this->addPrimarySystem(new Reactor(7, 35, 0, 0));
		$this->addPrimarySystem(new Hangar(7, 14, 12));
		$this->addPrimarySystem(new Hangar(7, 14, 12));
		$this->addPrimarySystem(new Hangar(7, 14, 12));
		$this->addPrimarySystem(new CnC(8, 60, 0, 0));
		$this->addPrimarySystem(new Scanner(7, 32, 5, 10));

		$this->addFrontSystem(new TwinArray(3, 6, 2, 240, 120));
        $this->addFrontSystem(new TwinArray(3, 6, 2, 240, 120));
        $this->addFrontSystem(new TwinArray(3, 6, 2, 240, 120));
		$this->addFrontSystem(new TwinArray(3, 6, 2, 240, 120));
        $this->addFrontSystem(new Thruster(6, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(6, 10, 0, 3, 1));
		
		$this->addAftSystem(new JumpEngine(6, 25, 3, 16));
        $this->addAftSystem(new Thruster(5, 8, 0, 2, 2));
        $this->addAftSystem(new Thruster(5, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(5, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(5, 8, 0, 2, 2));
        
		$this->addLeftSystem(new BattleLaser(5, 6, 6, 240, 0));
		$this->addLeftSystem(new BattleLaser(5, 6, 6, 240, 0));
		$this->addLeftSystem(new TwinArray(3, 6, 2, 180, 60));
		$this->addLeftSystem(new TwinArray(3, 6, 2, 180, 60));
		$this->addLeftSystem(new Thruster(5, 15, 0, 5, 3));
				
		$this->addRightSystem(new BattleLaser(5, 6, 6, 0, 120));
		$this->addRightSystem(new BattleLaser(5, 6, 6, 0, 120));
		$this->addRightSystem(new TwinArray(3, 6, 2, 300, 180));
		$this->addRightSystem(new TwinArray(3, 6, 2, 300, 180));
		$this->addRightSystem(new Thruster(5, 15, 0, 5, 4));
		
        
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 42));
        $this->addAftSystem(new Structure( 5, 40));
        $this->addLeftSystem(new Structure( 5, 56));
        $this->addRightSystem(new Structure( 5, 56));
        $this->addPrimarySystem(new Structure( 7, 40));
	    
	//d20 hit chart
        $this->hitChart = array(
            0=> array( //PRIMARY
                    9 => "Structure",
                    12 => "Scanner",
                    15 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array( //Forward
                    3 => "Thruster",
                    7 => "Twin Array",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array( //Aft
                    7 => "Thruster",
                    12 => "Jump Engine",
                    18 => "Structure",
                    20 => "Primary",
            ),
            3=> array( //Port
                    3 => "Thruster",
                    7 => "Battle Laser",
                    11 => "Twin Array",
                    18 => "Structure",
                    20 => "Primary",
            ),
            4=> array( //Starboard
                    3 => "Thruster",
                    7 => "Battle Laser",
                    11 => "Twin Array",
                    18 => "Structure",
                    20 => "Primary",
                    
           		)
       		 );
		}
	}
		
?>		
