<?php
class XillShredderTest extends VreeCapital{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 700;
		$this->faction = "Vree";
		$this->phpclass = "XillShredderTest";
		$this->shipClass = "Xill SHREDDER TESTBED";
		$this->isd = 2258;
		$this->locations = array(41, 42, 2, 32, 31, 1);
	      $this->unofficial = true;
	//          	$this->variantOf = 'NOT YET EXISTING';					

		$this->shipSizeClass = 3;
		$this->iniativebonus = 0;
		
        $this->turncost = 1;
        $this->turndelaycost = 0.66;
        $this->accelcost = 4;
        $this->rollcost = 5;
        $this->pivotcost = 0;	
        $this->gravitic = true;        	

		$this->forwardDefense = 15;
		$this->sideDefense = 15;

		$this->imagePath = "img/ships/VreeXill.png";
		$this->canvasSize = 200;

		$this->addPrimarySystem(new Reactor(6, 18, 0, 0));
		$this->addPrimarySystem(new Hangar(6, 1));
		$this->addPrimarySystem(new CnC(6, 12, 0, 0));
		$this->addPrimarySystem(new Scanner(6, 14, 9, 10));
        $this->addPrimarySystem(new Engine(6, 16, 0, 9, 2));
		$this->addPrimarySystem(new JumpEngine(6, 10, 5, 24));         			
		$this->addPrimarySystem(new AntimatterShredder(3, 0, 0, 0, 360));
		$this->addPrimarySystem(new AntimatterShredder(3, 0, 0, 0, 360));
		$this->addPrimarySystem(new Thruster(5, 16, 0, 9, 3));
		$this->addPrimarySystem(new Thruster(5, 16, 0, 9, 4));					


        $this->addFrontSystem(new AntiprotonGun(3, 0, 0, 300, 60));
        $this->addFrontSystem(new Thruster(5, 16, 0, 9, 1));        
		
        $this->addAftSystem(new AntiprotonGun(3, 0, 0, 120, 240));
        $this->addAftSystem(new Thruster(5, 16, 0, 9, 2));       
        
		$this->addLeftFrontSystem(new AntiprotonGun(3, 0, 0, 240, 360));
	//	$this->addLeftFrontSystem(new VreeStructureTechnical(0, 0, 0, 0));		
				
		$this->addLeftAftSystem(new AntiprotonGun(3, 0, 0, 180, 300));
	//	$this->addLeftAftSystem(new VreeStructureTechnical(0, 0, 0, 0));		
		

		$this->addRightFrontSystem(new AntiprotonGun(3, 0, 0, 0, 120));
	//	$this->addRightFrontSystem(new VreeStructureTechnical(0, 0, 0, 0));	
			

		$this->addRightAftSystem(new AntiprotonGun(3, 0, 0, 60, 180));
	//	$this->addRightAftSystem(new VreeStructureTechnical(0, 0, 0, 0));		
		
       
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 30));
        $this->addAftSystem(new Structure( 5, 30));
        $this->addLeftFrontSystem(new Structure( 5, 30));
        $this->addLeftAftSystem(new Structure( 5, 30));
        $this->addRightFrontSystem(new Structure( 5, 30));
        $this->addRightAftSystem(new Structure( 5, 30));      
        $this->addPrimarySystem(new Structure( 6, 44));
	    
	//d20 hit chart
        $this->hitChart = array(

            0=> array(
                    9 => "Structure",
                    10 => "Jump Engine",
                    12 => "Scanner",
                    15 => "Engine",
                    16 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
           		 ),
            1=> array(
                    4 => "Thruster",
                    6 => "Antiproton Gun",
                    8 => "0:Antimatter Cannon",                    
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            2=> array(
                    4 => "Thruster",
                    6 => "Antiproton Gun",
                    8 => "0:Antimatter Cannon",                    
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            31=> array(
                    1 => "1:Thruster",
                    4 => "0:Thruster", 
                    6 => "Antiproton Gun",
                    8 => "0:Antimatter Cannon",                    
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            32=> array(
                    1 => "2:Thruster",
                    4 => "0:Thruster", 
                    8 => "0:Antimatter Cannon",                    
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            41=> array(
                    1 => "1:Thruster",
                    4 => "0:Thruster", 
                    6 => "Antiproton Gun",
                    8 => "0:Antimatter Cannon",                    
                    18 => "Structure",
                    20 => "Primary",
           		 ),
       		42=> array(
                    1 => "2:Thruster",
                    4 => "0:Thruster", 
                    6 => "Antiproton Gun",
                    8 => "0:Antimatter Cannon",                    
                    18 => "Structure",
                    20 => "Primary",
           		 ),
           	);
       		
		}
	}
		
?>		
