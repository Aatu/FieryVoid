<?php
class Xaarix extends VreeCapital{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 475;
		$this->faction = "Vree Conglomerate";
		$this->phpclass = "Xaarix";
		$this->shipClass = "Xaarix Mobile Fighter Garrison";
		$this->isd = 2211;
		$this->locations = array(41, 42, 2, 32, 31, 1);	
        $this->fighters = array("normal"=>12, "ultralight"=>36);	
        
  		$this->occurence = "rare";
    	$this->variantOf = 'Xeecra Trading Post';        	
 		$this->unofficial = 'S'; //design released after AoG demise

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
		$this->addPrimarySystem(new Hangar(5, 2));					
		$this->addPrimarySystem(new CnC(5, 16, 0, 0));
		$this->addPrimarySystem(new Scanner(5, 12, 6, 6));
        $this->addPrimarySystem(new Engine(5, 11, 0, 6, 3));
		$this->addPrimarySystem(new Quarters(4, 12));

        $this->addFrontSystem(new AntiprotonGun(3, 0, 0, 300, 60));
		$this->addFrontSystem(new GraviticThruster(4, 14, 0, 6, 1));   
		$this->addFrontSystem(new Hangar(3, 3));        
     	
        $this->addAftSystem(new AntiprotonGun(3, 0, 0, 120, 240));
		$this->addAftSystem(new GraviticThruster(4, 14, 0, 6, 2)); 
		$this->addAftSystem(new Hangar(3, 3));       
     
		$this->addLeftFrontSystem(new AntiprotonGun(3, 0, 0, 240, 360));
		$this->addLeftFrontSystem(new Hangar(3, 3));	
				
		$this->addLeftAftSystem(new GraviticThruster(4, 14, 0, 6, 3));
		$this->addLeftAftSystem(new AntiprotonGun(3, 0, 0, 180, 300));
		$this->addLeftAftSystem(new Hangar(3, 3));		
		
		$this->addRightFrontSystem(new AntiprotonGun(3, 0, 0, 0, 120));
		$this->addRightFrontSystem(new Hangar(3, 3));					
	
		$this->addRightAftSystem(new GraviticThruster(4, 14, 0, 6, 4));	
		$this->addRightAftSystem(new AntiprotonGun(3, 0, 0, 60, 180));
		$this->addRightAftSystem(new Hangar(3, 3));
       
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
		/*remade for Tags!
        $this->addFrontSystem(new Structure( 4, 45, true));
        $this->addAftSystem(new Structure( 4, 45, true));
        $this->addLeftFrontSystem(new Structure( 4, 45, true));
        $this->addLeftAftSystem(new Structure( 4, 45, true));
        $this->addRightFrontSystem(new Structure( 4, 45, true));
        $this->addRightAftSystem(new Structure( 4, 45, true));    
*/		
		$structArmor = 4;
		$structHP = 45;
		
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
		
        $this->addPrimarySystem(new Structure( 5, 63));
	    
	//d20 hit chart
        $this->hitChart = array(
            0=> array(
                    7 => "Structure",
                    9 => "Quarters",
                    11 => "Scanner",
                    14 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
           		 ),
            1=> array(
                    4 => "TAG:Thruster",
                    6 => "Hangar",
                    7 => "TAG:Weapon",                    
                    17 => "Structure",
                    20 => "Primary",
           		 ),
            2=> array(
                    4 => "TAG:Thruster",
                    6 => "Hangar",
                    7 => "TAG:Weapon",                    
                    17 => "Structure",
                    20 => "Primary",
           		 ),
            31=> array(
                    4 => "TAG:Thruster",
                    6 => "Hangar",
                    7 => "TAG:Weapon",                    
                    17 => "Structure",
                    20 => "Primary",
           		 ),
            32=> array(
                    4 => "TAG:Thruster",
                    6 => "Hangar",
                    7 => "TAG:Weapon",                    
                    17 => "Structure",
                    20 => "Primary",
           		 ),
            41=> array(
                    4 => "TAG:Thruster",
                    6 => "Hangar",
                    7 => "TAG:Weapon",                    
                    17 => "Structure",
                    20 => "Primary",
           		 ),
       		42=> array(
                    4 => "TAG:Thruster",
                    6 => "Hangar",
                    7 => "TAG:Weapon",                    
                    17 => "Structure",
                    20 => "Primary",
           		 ),
           	);
	
	
		/*remade for Tags!
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
                    4 => "32:Thruster",
                    6 => "Cargo Bay",
                    7 => "Antiproton Gun",                    
                    17 => "Structure",
                    20 => "Primary",
           		 ),
            32=> array(
                    4 => "Thruster",
                    6 => "Cargo Bay",
                    7 => "Antiproton Gun",                    
                    17 => "Structure",
                    20 => "Primary",
           		 ),
            41=> array(
                    4 => "42:Thruster",
                    6 => "Cargo Bay",
                    7 => "Antiproton Gun",                    
                    17 => "Structure",
                    20 => "Primary",
           		 ),
       		42=> array(
                    4 => "Thruster",
                    6 => "Cargo Bay",
                    7 => "Antiproton Gun",                    
                    17 => "Structure",
                    20 => "Primary",
           		 ),
           	);
			*/
       		
		}
	}
		
?>		
