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

		$this->imagePath = "img/ships/VreeVaarl.png";
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

		$this->addFrontSystem(new Thruster(4, 12, 0, 7, 1));
		
        $this->addAftSystem(new Thruster(4, 12, 0, 7, 2));
        
        $this->addLeftFrontSystem(new Thruster(3, 6, 0, 3, 3));
		$this->addLeftFrontSystem(new AntimatterTorpedo(3, 0, 0, 240, 0));
		$this->addLeftFrontSystem(new AntimatterTorpedo(3, 0, 0, 300, 60)); 
		
		$this->addRightFrontSystem(new Thruster(3, 6, 0, 3, 4));
		$this->addRightFrontSystem(new AntimatterTorpedo(3, 0, 0, 300, 60)); 
		$this->addRightFrontSystem(new AntimatterTorpedo(3, 0, 0, 0, 120)); 						        
		
		$this->addLeftAftSystem(new Thruster(3, 6, 0, 4, 3));
		$this->addLeftAftSystem(new AntimatterTorpedo(3, 0, 0, 180, 300));
		$this->addLeftAftSystem(new AntimatterTorpedo(3, 0, 0, 120, 240));
		 
		$this->addRightAftSystem(new Thruster(3, 6, 0, 4, 4));
		$this->addRightAftSystem(new AntimatterTorpedo(3, 0, 0, 60, 180));  
		$this->addRightAftSystem(new AntimatterTorpedo(3, 0, 0, 120, 240)); 
       
	//	$this->addFrontSystem(new VreeStructurePlaceholder(0, 0, 0, 0)); 
        
	//	$this->addAftSystem(new VreeStructurePlaceholder(0, 0, 0, 0)); 
		     
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 3, 50));
        $this->addAftSystem(new Structure( 4, 50));
        $this->addPrimarySystem(new Structure( 4, 45));
	    
	//d20 hit chart
        $this->hitChart = array(
            0=> array(
                    12 => "Structure",
                    14 => "Scanner",
                    16 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
           		 ),
            1=> array(
                    2 => "Thruster",
                    4 => "0:Thruster",
                    5 => "0:Antiproton Defender",
                    6 => "0:Antiproton Gun",
                    9 => "Antimatter Torpedo",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            2=> array(
                    2 => "Thruster",
                    4 => "0:Thruster",
                    5 => "0:Antiproton Defender",
                    6 => "0:Antiproton Gun",
                    8 => "Antimatter Torpedo",
                    18 => "Structure",
                    20 => "Primary",
           		 ),

           	);
       		
		}
	}
		
?>		