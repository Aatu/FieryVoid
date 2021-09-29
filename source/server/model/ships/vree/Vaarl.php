<?php
class Vaarl extends VreeHCV{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 625;
		$this->faction = "Vree";
		$this->phpclass = "Vaarl";
		$this->shipClass = "Vaarl Scout Saucer";
		$this->isd = 2210;
		$this->locations = array(41, 42, 2, 32, 31, 1);					


		$this->shipSizeClass = 2; //Enormous is not implemented
		$this->iniativebonus = 6; //no voluntary movement anyway
		
        $this->turncost = 0.66;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 3;
        $this->pivotcost = 0;	
        $this->gravitic = true;        	

		$this->forwardDefense = 13;
		$this->sideDefense = 13;

		$this->imagePath = "img/ships/VreeVaarl.png";
		$this->canvasSize = 200;

		$this->addPrimarySystem(new Reactor(4, 15, 0, 0));
		$this->addPrimarySystem(new Hangar(4, 1));
		$this->addPrimarySystem(new CnC(4, 12, 0, 0));
		$this->addPrimarySystem(new ElintScanner(4, 20, 9, 12));
        $this->addPrimarySystem(new Engine(4, 11, 0, 7, 2));
		$this->addPrimarySystem(new JumpEngine(6, 10, 4, 24));        			
		$this->addPrimarySystem(new AntimatterCannon(3, 0, 0, 0, 360));		
		
		$this->addFrontSystem(new Thruster(3, 12, 0, 7, 1));
		
        $this->addAftSystem(new Thruster(3, 12, 0, 7, 2));
        
        $this->addLeftFrontSystem(new Thruster(3, 6, 0, 3, 3));
		$this->addLeftFrontSystem(new AntiprotonDefender(2, 0, 0, 240, 0));
		$this->addLeftFrontSystem(new AntiprotonDefender(2, 0, 0, 300, 60)); 
		
		$this->addRightFrontSystem(new Thruster(3, 6, 0, 3, 4));
		$this->addRightFrontSystem(new AntiprotonDefender(2, 0, 0, 300, 60)); 
		$this->addRightFrontSystem(new AntiprotonDefender(2, 0, 0, 0, 120)); 						        
		
		$this->addLeftAftSystem(new Thruster(3, 6, 0, 4, 3));
		$this->addLeftAftSystem(new AntiprotonDefender(2, 0, 0, 180, 300));
		$this->addLeftAftSystem(new AntiprotonDefender(2, 0, 0, 120, 240));
		 
		$this->addRightAftSystem(new Thruster(3, 6, 0, 4, 4));
		$this->addRightAftSystem(new AntiprotonDefender(2, 0, 0, 60, 180));  
		$this->addRightAftSystem(new AntiprotonDefender(2, 0, 0, 120, 240)); 
       
	//	$this->addFrontSystem(new VreeStructurePlaceholder(0, 0, 0, 0)); 
        
	//	$this->addAftSystem(new VreeStructurePlaceholder(0, 0, 0, 0)); 
       
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 50));
        $this->addAftSystem(new Structure( 4, 50));
        $this->addPrimarySystem(new Structure( 4, 50));
	    
	//d20 hit chart
        $this->hitChart = array(
            0=> array(
                    10 => "Structure",
                    12 => "Jump Engine",
                    14 => "Scanner",
                    16 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
           		 ),
            1=> array(
                    4 => "Thruster",   
                    6 => "31:Antiproton Defender",
                    7 => "41:Antiproton Defender",
                    8 => "0:Antimatter Cannon",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            2=> array(
                    4 => "Thruster",   
                    6 => "32:Antiproton Defender",
                    7 => "42:Antiproton Defender",
                    8 => "0:Antimatter Cannon",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            31=> array(
                    2 => "1:Thruster",
                    3 => "Thruster",
                    4 => "32:Thruster",                        
                    6 => "Antiproton Defender",
                    7 => "41:Antiproton Defender",
                    8 => "0:Antimatter Cannon",
                    18 => "1:Structure",
                    20 => "Primary",
           		 ),
            32=> array(
                    2 => "2:Thruster",
                    3 => "Thruster",
                    4 => "31:Thruster",                        
                    6 => "Antiproton Defender",
                    7 => "42:Antiproton Defender",
                    8 => "0:Antimatter Cannon",
                    18 => "2:Structure",
                    20 => "Primary",
           		 ),
            41=> array(
                    2 => "1:Thruster",
                    3 => "Thruster",
                    4 => "42:Thruster",                        
                    6 => "Antiproton Defender",
                    7 => "31:Antiproton Defender",
                    8 => "0:Antimatter Cannon",
                    18 => "1:Structure",
                    20 => "Primary",
           		 ),
       		42=> array(
                    2 => "2:Thruster",
                    3 => "Thruster",
                    4 => "41:Thruster",                        
                    6 => "Antiproton Defender",
                    7 => "32:Antiproton Defender",
                    8 => "0:Antimatter Cannon",
                    18 => "2:Structure",
                    20 => "Primary",
           		 ),
           	);
       		
		}
	}
		
?>		
