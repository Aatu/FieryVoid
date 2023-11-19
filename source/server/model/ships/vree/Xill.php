<?php
class Xill extends VreeCapital{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 700;
		$this->faction = "Vree";
		$this->phpclass = "Xill";
		$this->shipClass = "Xill Battle Saucer";
		$this->isd = 2258;
		  

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
		$this->addPrimarySystem(new AntimatterShredder(4, 0, 0, 0, 360));  			
		$this->addPrimarySystem(new AntimatterShredder(4, 0, 0, 0, 360));
		

        $this->addFrontSystem(new GraviticThruster(5, 16, 0, 9, 1)); 
        $this->addFrontSystem(new AntiprotonGun(3, 0, 0, 300, 60));

        $this->addAftSystem(new GraviticThruster(5, 16, 0, 9, 2));  		
        $this->addAftSystem(new AntiprotonGun(3, 0, 0, 120, 240));
        
		$this->addLeftFrontSystem(new AntiprotonGun(3, 0, 0, 240, 360));

		$this->addLeftAftSystem(new GraviticThruster(5, 16, 0, 9, 3));				
		$this->addLeftAftSystem(new AntiprotonGun(3, 0, 0, 180, 300));
		
		$this->addRightFrontSystem(new AntiprotonGun(3, 0, 0, 0, 120));
			
		$this->addRightAftSystem(new GraviticThruster(5, 16, 0, 9, 4));
		$this->addRightAftSystem(new AntiprotonGun(3, 0, 0, 60, 180));
	
		
       
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
		/*remade for Tags!
        $this->addFrontSystem(new Structure( 5, 30, true));
        $this->addAftSystem(new Structure( 5, 30, true));
        $this->addLeftFrontSystem(new Structure( 5, 30, true));
        $this->addLeftAftSystem(new Structure( 5, 30, true));
        $this->addRightFrontSystem(new Structure( 5, 30, true));
        $this->addRightAftSystem(new Structure( 5, 30, true));  
*/
		$structArmor = 5;
		$structHP = 30;
		
		$struct = new Structure( $structArmor, $structHP, true);
		$struct->addTag("Outer Structure");
		$struct->startArc = 300;
		$struct->endArc = 60;
        $this->addFrontSystem($struct);
		
		$struct = new Structure( $structArmor, $structHP, true);
		$struct->addTag("Outer Structure");
		$struct->startArc = 120;
		$struct->endArc = 240;
        $this->addAftSystem($struct);
		
		$struct = new Structure( $structArmor, $structHP, true);
		$struct->addTag("Outer Structure");
		$struct->startArc = 240;
		$struct->endArc = 0;
        $this->addLeftFrontSystem($struct);
		
		$struct = new Structure( $structArmor, $structHP, true);
		$struct->addTag("Outer Structure");
		$struct->startArc = 180;
		$struct->endArc = 300;
        $this->addLeftAftSystem($struct);
		
		$struct = new Structure( $structArmor, $structHP, true);
		$struct->addTag("Outer Structure");
		$struct->startArc = 0;
		$struct->endArc = 120;
        $this->addRightFrontSystem($struct);
		
		$struct = new Structure( $structArmor, $structHP, true);
		$struct->addTag("Outer Structure");
		$struct->startArc = 60;
		$struct->endArc = 180;
        $this->addRightAftSystem($struct);  
		
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
                    4 => "TAG:Thruster", 
                    8 => "TAG:Weapon",                    
                    17 => "TAG:Outer Structure",
                    20 => "Primary",
           		 ),
            2=> array(
                    4 => "TAG:Thruster", 
                    8 => "TAG:Weapon",                    
                    17 => "TAG:Outer Structure",
                    20 => "Primary",
           		 ),
            31=> array(
                    4 => "TAG:Thruster", 
                    8 => "TAG:Weapon",                    
                    17 => "TAG:Outer Structure",
                    20 => "Primary",
           		 ),
            32=> array(
                    4 => "TAG:Thruster", 
                    8 => "TAG:Weapon",                    
                    17 => "TAG:Outer Structure",
                    20 => "Primary",
           		 ),
            41=> array(
                    4 => "TAG:Thruster", 
                    8 => "TAG:Weapon",                    
                    17 => "TAG:Outer Structure",
                    20 => "Primary",
           		 ),
       		42=> array(
                    4 => "TAG:Thruster", 
                    8 => "TAG:Weapon",                    
                    17 => "TAG:Outer Structure",
                    20 => "Primary",
           		 ),
           	);
	
	
	
		/*remade for Tags!
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
                    8 => "0:Antimatter Shredder",                    
                    17 => "Structure",
                    20 => "Primary",
           		 ),
            2=> array(
                    4 => "Thruster", 
                    6 => "Antiproton Gun",
                    8 => "0:Antimatter Shredder",                    
                    17 => "Structure",
                    20 => "Primary",
           		 ),
            31=> array(
                    4 => "32:Thruster", 
                    6 => "Antiproton Gun",
                    8 => "0:Antimatter Shredder",                    
                    17 => "Structure",
                    20 => "Primary",
           		 ),
            32=> array(
                    4 => "Thruster", 
                    6 => "Antiproton Gun",
                    8 => "0:Antimatter Shredder",                    
                    17 => "Structure",
                    20 => "Primary",
           		 ),
            41=> array(
                    4 => "42:Thruster", 
                    6 => "Antiproton Gun",
                    8 => "0:Antimatter Shredder",                    
                    17 => "Structure",
                    20 => "Primary",
           		 ),
       		42=> array(
                    4 => "Thruster", 
                    6 => "Antiproton Gun",
                    8 => "0:Antimatter Shredder",                    
                    17 => "Structure",
                    20 => "Primary",
           		 ),
           	);
			*/
			
       		
		}
	}
		
?>		
