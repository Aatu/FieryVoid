<?php
class Tyllz extends StarBase
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 3500;
		$this->base = true;
		$this->faction = "Vree";
		$this->phpclass = "Tyllz";
		$this->shipClass = "Tyllz Sector Trading Post";
		$this->imagePath = "img/ships/VreeTyllz.png";
		$this->canvasSize = 310;
		$this->fighters = array("normal" => 24);
		$this->isd = 2252;

		$this->shipSizeClass = 3;
		$this->Enormous = true;
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->forwardDefense = 21;
		$this->sideDefense = 21;

		$this->addPrimarySystem(new Reactor(5, 25, 0, 0));
		$this->addPrimarySystem(new Hangar(5, 30));
		$this->addPrimarySystem(new ProtectedCnC(6, 32, 0, 0));
		$this->addPrimarySystem(new Scanner(5, 18, 5, 9));
		$this->addPrimarySystem(new Scanner(5, 18, 5, 9));
		$this->addPrimarySystem(new AntimatterShredder(4, 0, 0, 0, 360));	         				
		$this->addPrimarySystem(new AntimatterShredder(4, 0, 0, 0, 360));
		$this->addPrimarySystem(new AntimatterShredder(4, 0, 0, 0, 360));	         				
		$this->addPrimarySystem(new AntimatterShredder(4, 0, 0, 0, 360));
		$this->addPrimarySystem(new AntimatterShredder(4, 0, 0, 0, 360));	         				
		$this->addPrimarySystem(new AntimatterShredder(4, 0, 0, 0, 360));     							

		$this->addFrontSystem(new CargoBay(4, 48));
		$this->addFrontSystem(new SubReactorUniversal(4, 23, 0, 0));
		$this->addFrontSystem(new AntimatterTorpedo(4, 0, 0, 300, 60)); 
		$this->addFrontSystem(new AntimatterTorpedo(4, 0, 0, 300, 60)); 				  
        $this->addFrontSystem(new AntiprotonGun(4, 0, 0, 300, 60));
        $this->addFrontSystem(new AntiprotonGun(4, 0, 0, 300, 60));  	

	
		$this->addAftSystem(new CargoBay(4, 48));
		$this->addAftSystem(new SubReactorUniversal(4, 23, 0, 0));
		$this->addAftSystem(new AntimatterTorpedo(4, 0, 0, 120, 240)); 
		$this->addAftSystem(new AntimatterTorpedo(4, 0, 0, 120, 240)); 				  
        $this->addAftSystem(new AntiprotonGun(4, 0, 0, 120, 240));
        $this->addAftSystem(new AntiprotonGun(4, 0, 0, 120, 240));  		   
        
		$this->addLeftFrontSystem(new CargoBay(4, 48));
		$this->addLeftFrontSystem(new SubReactorUniversal(4, 23, 0, 0));
		$this->addLeftFrontSystem(new AntimatterTorpedo(4, 0, 0, 240, 360)); 
		$this->addLeftFrontSystem(new AntimatterTorpedo(4, 0, 0, 240, 360)); 				  
        $this->addLeftFrontSystem(new AntiprotonGun(4, 0, 0, 240, 360));
        $this->addLeftFrontSystem(new AntiprotonGun(4, 0, 0, 240, 360)); 				

		$this->addLeftAftSystem(new CargoBay(4, 48));
		$this->addLeftAftSystem(new SubReactorUniversal(4, 23, 0, 0));
		$this->addLeftAftSystem(new AntimatterTorpedo(4, 0, 0, 180, 300)); 
		$this->addLeftAftSystem(new AntimatterTorpedo(4, 0, 0, 180, 300)); 				  
        $this->addLeftAftSystem(new AntiprotonGun(4, 0, 0, 180, 300));
        $this->addLeftAftSystem(new AntiprotonGun(4, 0, 0, 180, 300)); 			
		
		$this->addRightFrontSystem(new CargoBay(4, 48));
		$this->addRightFrontSystem(new SubReactorUniversal(4, 23, 0, 0));
		$this->addRightFrontSystem(new AntimatterTorpedo(4, 0, 0, 0, 120)); 
		$this->addRightFrontSystem(new AntimatterTorpedo(4, 0, 0, 0, 120)); 				  
        $this->addRightFrontSystem(new AntiprotonGun(4, 0, 0, 0, 120));
        $this->addRightFrontSystem(new AntiprotonGun(4, 0, 0, 0, 120)); 			

		$this->addRightAftSystem(new CargoBay(4, 48));
		$this->addRightAftSystem(new SubReactorUniversal(4, 23, 0, 0));
		$this->addRightAftSystem(new AntimatterTorpedo(4, 0, 0, 60, 180)); 
		$this->addRightAftSystem(new AntimatterTorpedo(4, 0, 0, 60, 180)); 				  
        $this->addRightAftSystem(new AntiprotonGun(4, 0, 0, 60, 180));
        $this->addRightAftSystem(new AntiprotonGun(4, 0, 0, 60, 180)); 		
		
       
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 100));
        $this->addAftSystem(new Structure( 4, 100));
        $this->addLeftFrontSystem(new Structure( 4, 100));
        $this->addLeftAftSystem(new Structure( 4, 100));
        $this->addRightFrontSystem(new Structure( 4, 100));
        $this->addRightAftSystem(new Structure( 4, 100));      
        $this->addPrimarySystem(new Structure( 5, 90));
	    
	//d20 hit chart
        $this->hitChart = array(

            0=> array(
                    9 => "Structure",
                    11 => "Antimatter Shredder",
                    14 => "Scanner",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
           		 ),
            1=> array(
                    3 => "Antiproton Gun",
                    6 => "Antiproton Torpedo",                    
                    7 => "0:Antimatter Shredder",                    
                    9 => "Cargo Bay",
                    10 => "Sub Reactor",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            2=> array(
                    3 => "Antiproton Gun",
                    6 => "Antiproton Torpedo",                    
                    7 => "0:Antimatter Shredder",                    
                    9 => "Cargo Bay",
                    10 => "Sub Reactor",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            31=> array(
                    3 => "Antiproton Gun",
                    6 => "Antiproton Torpedo",                    
                    7 => "0:Antimatter Shredder",                    
                    9 => "Cargo Bay",
                    10 => "Sub Reactor",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            32=> array(
                    3 => "Antiproton Gun",
                    6 => "Antiproton Torpedo",                    
                    7 => "0:Antimatter Shredder",                    
                    9 => "Cargo Bay",
                    10 => "Sub Reactor",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            41=> array(
                    3 => "Antiproton Gun",
                    6 => "Antiproton Torpedo",                    
                    7 => "0:Antimatter Shredder",                    
                    9 => "Cargo Bay",
                    10 => "Sub Reactor",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
       		42=> array(
                    3 => "Antiproton Gun",
                    6 => "Antiproton Torpedo",                    
                    7 => "0:Antimatter Shredder",                    
                    9 => "Cargo Bay",
                    10 => "Sub Reactor",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
           	);
       		
		}
	}


?>