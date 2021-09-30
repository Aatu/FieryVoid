<?php
class Xeecra extends VreeCapital{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 475;
		$this->faction = "Vree";
		$this->phpclass = "Xeecra";
		$this->shipClass = "Xeecra Trading Post";
		$this->isd = 2262;
		$this->locations = array(41, 42, 2, 32, 31, 1);	
        $this->fighters = array("normal"=>12);			

		$this->shipSizeClass = 3;
		$this->iniativebonus = 0;
		
        $this->turncost = 2;
        $this->turndelaycost = 2;
        $this->accelcost = 6;
        $this->rollcost = 999;
        $this->pivotcost = 0;	
        $this->gravitic = true;        	

		$this->forwardDefense = 17;
		$this->sideDefense = 17;

		$this->imagePath = "img/ships/VreeXeecra.png";
		$this->canvasSize = 200;

		$this->addPrimarySystem(new Reactor(5, 15, 0, 0));
		$this->addPrimarySystem(new Hangar(5, 6));
		$this->addPrimarySystem(new Hangar(5, 6));		
		$this->addPrimarySystem(new CnC(5, 16, 0, 0));
		$this->addPrimarySystem(new Scanner(5, 12, 6, 6));
        $this->addPrimarySystem(new Engine(5, 11, 0, 6, 3));
		$this->addPrimarySystem(new CargoBay(4, 25));
		$this->addPrimarySystem(new VreePortThruster(4, 14, 0, 6, 3));
		$this->addPrimarySystem(new VreeStarboardThruster(4, 14	, 0, 6, 4));			        			


        $this->addFrontSystem(new AntiprotonGun(3, 0, 0, 300, 60));
		$this->addFrontSystem(new Thruster(4, 14, 0, 6, 1));   
		$this->addFrontSystem(new CargoBay(3, 15));        
     
		
        $this->addAftSystem(new AntiprotonGun(3, 0, 0, 120, 240));
		$this->addAftSystem(new Thruster(4, 14, 0, 6, 1)); 
		$this->addAftSystem(new CargoBay(3, 15));        
     
        
		$this->addLeftFrontSystem(new AntiprotonGun(3, 0, 0, 240, 360));
		$this->addLeftFrontSystem(new CargoBay(3, 15));		

				
		$this->addLeftAftSystem(new AntiprotonGun(3, 0, 0, 180, 300));
		$this->addLeftAftSystem(new CargoBay(3, 15));		

		
		$this->addRightFrontSystem(new AntiprotonGun(3, 0, 0, 0, 120));
		$this->addRightFrontSystem(new CargoBay(3, 15));		

				
		$this->addRightAftSystem(new AntiprotonGun(3, 0, 0, 60, 180));
		$this->addRightAftSystem(new CargoBay(3, 15));		
	
		
       
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 45));
        $this->addAftSystem(new Structure( 4, 45));
        $this->addLeftFrontSystem(new Structure( 4, 45));
        $this->addLeftAftSystem(new Structure( 4, 45));
        $this->addRightFrontSystem(new Structure( 4, 45));
        $this->addRightAftSystem(new Structure( 4, 45));      
        $this->addPrimarySystem(new Structure( 5, 63));
	    
	//d20 hit chart
        $this->hitChart = array(

            0=> array(
                    7 => "Structure",
                    9 => "Cargo Bay",
                    11 => "Scanner",
                    14 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
           		 ),
            1=> array(
                    4 => "Thruster",
                    6 => "Cargo Bay",
                    7 => "Antiproton Gun",                    
                    17 => "Structure",
                    20 => "Primary",
           		 ),
            2=> array(
                    4 => "Thruster",
                    6 => "Cargo Bay",
                    7 => "Antiproton Gun",                    
                    17 => "Structure",
                    20 => "Primary",
           		 ),
            31=> array(
                    1 => "1:Thruster",
                    4 => "0:Port Thruster",
                    6 => "Cargo Bay",
                    7 => "Antiproton Gun",                    
                    17 => "Structure",
                    20 => "Primary",
           		 ),
            32=> array(
                    1 => "2:Thruster",
                    4 => "0:Port Thruster",
                    6 => "Cargo Bay",
                    7 => "Antiproton Gun",                    
                    17 => "Structure",
                    20 => "Primary",
           		 ),
            41=> array(
                    1 => "1:Thruster",
                    4 => "0:Starboard Thruster",
                    6 => "Cargo Bay",
                    7 => "Antiproton Gun",                    
                    17 => "Structure",
                    20 => "Primary",
           		 ),
       		42=> array(
                    1 => "2:Thruster",
                    4 => "0:Starboard Thruster",
                    6 => "Cargo Bay",
                    7 => "Antiproton Gun",                    
                    17 => "Structure",
                    20 => "Primary",
           		 ),
           	);
       		
		}
	}
		
?>		
