<?php
class Xixx extends VreeHCV{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 650;
		$this->faction = "Vree";
		$this->phpclass = "Xixx";
		$this->shipClass = "Xixx Torpedo Saucer";
		$this->isd = 2251;
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

		$this->imagePath = "img/ships/VreeXixx.png";
		$this->canvasSize = 200;

		$this->addPrimarySystem(new Reactor(4, 20, 0, 0));
		$this->addPrimarySystem(new Hangar(4, 1));
		$this->addPrimarySystem(new CnC(4, 12, 0, 0));
		$this->addPrimarySystem(new Scanner(4, 14, 9, 7));
        $this->addPrimarySystem(new Engine(4, 11, 0, 7, 2));
		$this->addPrimarySystem(new AntiprotonDefender(3, 0, 0, 0, 360));
		$this->addPrimarySystem(new AntiprotonDefender(3, 0, 0, 0, 360));
		$this->addPrimarySystem(new AntiprotonDefender(3, 0, 0, 0, 360));				
		$this->addPrimarySystem(new AntiprotonGun(3, 0, 0, 0, 360));
		$this->addPrimarySystem(new Thruster(3, 12, 0, 7, 3));
		$this->addPrimarySystem(new Thruster(3, 12, 0, 7, 4));					

		$this->addFrontSystem(new Thruster(4, 12, 0, 7, 1));
		
        $this->addAftSystem(new Thruster(4, 12, 0, 7, 2));
        
		$this->addLeftFrontSystem(new AntimatterTorpedo(3, 0, 0, 240, 0));
		$this->addLeftFrontSystem(new AntimatterTorpedo(3, 0, 0, 300, 60)); 
		$this->addLeftFrontSystem(new StructureTechnical(0, 0, 0, 0));			
		
		$this->addLeftAftSystem(new AntimatterTorpedo(3, 0, 0, 180, 300));
		$this->addLeftAftSystem(new AntimatterTorpedo(3, 0, 0, 120, 240));
		$this->addLeftAftSystem(new StructureTechnical(0, 0, 0, 0));	
		
		$this->addRightFrontSystem(new AntimatterTorpedo(3, 0, 0, 300, 60)); 
		$this->addRightFrontSystem(new AntimatterTorpedo(3, 0, 0, 0, 120));
		$this->addRightFrontSystem(new StructureTechnical(0, 0, 0, 0));			 						        
	
		$this->addRightAftSystem(new AntimatterTorpedo(3, 0, 0, 60, 180));  
		$this->addRightAftSystem(new AntimatterTorpedo(3, 0, 0, 120, 240)); 
		$this->addRightAftSystem(new StructureTechnical(0, 0, 0, 0));				
       
	//	$this->addFrontSystem(new StructurePlaceholder(0, 0, 0, 0)); 
        
	//	$this->addAftSystem(new StructurePlaceholder(0, 0, 0, 0)); 
		     
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 3, 50));
        $this->addAftSystem(new Structure( 4, 50));
        $this->addPrimarySystem(new Structure( 4, 45));
	    
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
                    5 => "31:Antiproton Torpedo",
                    6 => "41:Antiproton Torpedo",
                    7 => "0:Antiproton Gun",
                    8 => "0: Antiproton Defender",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            2=> array(
                    4 => "Thruster",   
                    5 => "32:Antiproton Torpedo",
                    6 => "42:Antiproton Torpedo",
                    7 => "0:Antiproton Gun",
                    8 => "0: Antiproton Defender",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            31=> array(
                    2 => "1:Thruster",
                    4 => "0:Thruster",                         
                    6 => "31:Antiproton Torpedo",
                    7 => "41:Antiproton Torpedo",
                    8 => "0:Antiproton Gun",
                    9 => "0: Antiproton Defender",
                    18 => "1:Structure",
                    20 => "Primary",
           		 ),
            32=> array(
                    2 => "2:Thruster",
                    4 => "0:Thruster",                        
                    6 => "32:Antiproton Torpedo",
                    7 => "42:Antiproton Torpedo",
                    8 => "0:Antiproton Gun",
                    9 => "0: Antiproton Defender",
                    18 => "2:Structure",
                    20 => "Primary",
           		 ),
            41=> array(
                    2 => "1:Thruster",
                    4 => "0:Thruster",                         
                    6 => "41:Antiproton Torpedo",
                    7 => "31:Antiproton Torpedo",
                    8 => "0:Antiproton Gun",
                    9 => "0: Antiproton Defender",
                    18 => "1:Structure",
                    20 => "Primary",
           		 ),
       		42=> array(
                    2 => "2:Thruster",
                    4 => "0:Thruster",                        
                    6 => "42:Antiproton Torpedo",
                    7 => "32:Antiproton Torpedo",
                    8 => "0:Antiproton Gun",
                    9 => "0: Antiproton Defender",
                    18 => "2:Structure",
                    20 => "Primary",
           		 ),
           	);
       		
		}
	}
		
?>		