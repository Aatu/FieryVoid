<?php
class Xonn extends VreeCapital{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 1200;
		$this->faction = "Vree";
		$this->phpclass = "Xonn";
		$this->shipClass = "Xonn Dreadnought";
		$this->isd = 2260;
		$this->locations = array(41, 42, 2, 32, 31, 1);	
	      $this->unofficial = true;			

		$this->shipSizeClass = 3;
		$this->iniativebonus = 0;
		
        $this->turncost = 1.5;
        $this->turndelaycost = 1;
        $this->accelcost = 5;
        $this->rollcost = 6;
        $this->pivotcost = 0;	
        $this->gravitic = true;        	

		$this->forwardDefense = 16;
		$this->sideDefense = 16;

		$this->imagePath = "img/ships/VreeXonn.png";
		$this->canvasSize = 200;

		$this->addPrimarySystem(new Reactor(6, 25, 0, 0));
		$this->addPrimarySystem(new Hangar(6, 3));
		$this->addPrimarySystem(new CnC(7, 16, 0, 0));
		$this->addPrimarySystem(new Scanner(6, 18, 9, 10));
        $this->addPrimarySystem(new Engine(6, 18, 0, 10, 3));
		$this->addPrimarySystem(new JumpEngine(7, 16, 6, 24));
		$this->addPrimarySystem(new AntimatterCannon(4, 0, 0, 0, 360));		         			
		$this->addPrimarySystem(new AntimatterCannon(4, 0, 0, 0, 360));
		$this->addPrimarySystem(new AntimatterCannon(4, 0, 0, 0, 360));		
		$this->addPrimarySystem(new AntimatterCannon(4, 0, 0, 0, 360));
		$this->addPrimarySystem(new AntimatterCannon(4, 0, 0, 0, 360));
		$this->addPrimarySystem(new AntimatterCannon(4, 0, 0, 0, 360));
		$this->addPrimarySystem(new AntimatterCannon(4, 0, 0, 0, 360));
		$this->addPrimarySystem(new Thruster(5, 20, 0, 10, 3));
		$this->addPrimarySystem(new Thruster(5, 20, 0, 10, 4));									


        $this->addFrontSystem(new AntiprotonGun(3, 0, 0, 300, 60));
        $this->addFrontSystem(new AntiprotonGun(3, 0, 0, 300, 60));        
        $this->addFrontSystem(new Thruster(5, 20, 0, 10, 1));        
		
        $this->addAftSystem(new AntiprotonGun(3, 0, 0, 120, 240));
        $this->addAftSystem(new AntiprotonGun(3, 0, 0, 120, 240));        
        $this->addAftSystem(new Thruster(5, 20, 0, 10, 2));       
        
		$this->addLeftFrontSystem(new AntiprotonGun(3, 0, 0, 240, 360));
		$this->addLeftFrontSystem(new AntiprotonGun(3, 0, 0, 240, 360));
		$this->addLeftFrontSystem(new VreeStructureTechnical(0, 0, 0, 0));				
				
		$this->addLeftAftSystem(new AntiprotonGun(3, 0, 0, 180, 300));
		$this->addLeftAftSystem(new AntiprotonGun(3, 0, 0, 180, 300));
		$this->addLeftAftSystem(new VreeStructureTechnical(0, 0, 0, 0));				
		
		$this->addRightFrontSystem(new AntiprotonGun(3, 0, 0, 0, 120));
		$this->addRightFrontSystem(new AntiprotonGun(3, 0, 0, 0, 120));	
		$this->addRightFrontSystem(new VreeStructureTechnical(0, 0, 0, 0));			
				
		$this->addRightAftSystem(new AntiprotonGun(3, 0, 0, 60, 180));
		$this->addRightAftSystem(new AntiprotonGun(3, 0, 0, 60, 180));	
		$this->addRightAftSystem(new VreeStructureTechnical(0, 0, 0, 0));			
		
       
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 36));
        $this->addAftSystem(new Structure( 5, 36));
        $this->addLeftFrontSystem(new Structure( 5, 36));
        $this->addLeftAftSystem(new Structure( 5, 36));
        $this->addRightFrontSystem(new Structure( 5, 36));
        $this->addRightAftSystem(new Structure( 5, 36));      
        $this->addPrimarySystem(new Structure( 6, 60));
	    
	//d20 hit chart
        $this->hitChart = array(

            0=> array(
                    9 => "Structure",
                    10 => "Jump Engine",
                    13 => "Scanner",
                    15 => "Engine",
                    16 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
           		 ),
            1=> array(
                    4 => "Thruster",
                    7 => "Antiproton Gun",
                    8 => "0:Antimatter Cannon",                    
                    9 => "0:Antimatter Cannon",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            2=> array(
                    4 => "Thruster",
                    7 => "Antiproton Gun",
                    8 => "0:Antimatter Cannon",                    
                    9 => "0:Antimatter Cannon",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            31=> array(
                    1 => "1:Thruster",
                    4 => "0:Thruster", 
                    7 => "Antiproton Gun",
                    8 => "0:Antimatter Cannon",                    
                    9 => "0:Antimatter Cannon",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            32=> array(
                    1 => "2:Thruster",
                    4 => "0:Thruster", 
                    7 => "Antiproton Gun",
                    8 => "0:Antimatter Cannon",                    
                    9 => "0:Antimatter Cannon",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            41=> array(
                    1 => "1:Thruster",
                    4 => "0:Thruster", 
                    7 => "Antiproton Gun",
                    8 => "0:Antimatter Cannon",                    
                    9 => "0:Antimatter Cannon",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
       		42=> array(
                    1 => "2:Thruster",
                    4 => "0:Thruster", 
                    7 => "Antiproton Gun",
                    8 => "0:Antimatter Cannon",                    
                    9 => "0:Antimatter Cannon",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
           	);
       		
		}
	}
		
?>		
