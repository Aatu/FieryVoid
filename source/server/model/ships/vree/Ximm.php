<?php
class Ximm extends VreeCapital{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 550;
		$this->faction = "Vree Conglomerate";
		$this->phpclass = "Ximm";
		$this->shipClass = "Ximm Close Escort";
		$this->isd = 2219;
		$this->locations = array(41, 42, 2, 32, 31, 1);
  		$this->occurence = "uncommon";
    	$this->variantOf = 'Xorr War Saucer';					

		$this->shipSizeClass = 3; 
		$this->iniativebonus = 0; 
		
        $this->turncost = 0.66;
        $this->turndelaycost = 0.5;
        $this->accelcost = 3;
        $this->rollcost = 4;
        $this->pivotcost = 0;	
        $this->gravitic = true;        	

		$this->forwardDefense = 14;
		$this->sideDefense = 14;

		$this->imagePath = "img/ships/VreeXorr.png";
		$this->canvasSize = 200;

		$this->addPrimarySystem(new Reactor(5, 17, 0, 0));
		$this->addPrimarySystem(new Hangar(5, 1));
		$this->addPrimarySystem(new CnC(5, 12, 0, 0));
		$this->addPrimarySystem(new Scanner(5, 12, 9, 8));
        $this->addPrimarySystem(new Engine(5, 12, 0, 8, 2));			
		$this->addPrimarySystem(new AntimatterCannon(3, 0, 0, 0, 360));
		$this->addPrimarySystem(new AntiprotonDefender(3, 0, 0, 0, 360));
		$this->addPrimarySystem(new AntiprotonDefender(3, 0, 0, 0, 360));

        $this->addFrontSystem(new GraviticThruster(4, 14, 0, 8, 1));
	
		$this->addAftSystem(new GraviticThruster(4, 14, 0, 8, 2));
    
    	$this->addLeftFrontSystem(new AntiprotonDefender(3, 0, 0, 240, 0));
		$this->addLeftFrontSystem(new AntiprotonDefender(3, 0, 0, 240, 0)); 
		$this->addLeftFrontSystem(new StructureTechnical(0, 0, 0, 0));
		
		$this->addRightFrontSystem(new AntiprotonDefender(3, 0, 0, 0, 120)); 
		$this->addRightFrontSystem(new AntiprotonDefender(3, 0, 0, 0, 120)); 						        
		$this->addRightFrontSystem(new StructureTechnical(0, 0, 0, 0));
		
		$this->addLeftAftSystem(new GraviticThruster(4, 14, 0, 8, 3));
		$this->addLeftAftSystem(new AntiprotonDefender(3, 0, 0, 180, 300));
		$this->addLeftAftSystem(new AntiprotonDefender(3, 0, 0, 180, 300));
		 
		$this->addRightAftSystem(new GraviticThruster(4, 14, 0, 8, 4));
		$this->addRightAftSystem(new AntiprotonDefender(3, 0, 0, 60, 180));  
		$this->addRightAftSystem(new AntiprotonDefender(3, 0, 0, 60, 180)); 		
       
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
		/*remade for Tags!
        $this->addFrontSystem(new Structure( 4, 24, true));
        $this->addAftSystem(new Structure( 4, 24, true));
        $this->addLeftFrontSystem(new Structure( 4, 24, true));
        $this->addLeftAftSystem(new Structure( 4, 24, true));
        $this->addRightFrontSystem(new Structure( 4, 24, true));
        $this->addRightAftSystem(new Structure( 4, 24, true));   
*/		
		$structArmor = 4;
		$structHP = 24;
		
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
		
        $this->addPrimarySystem(new Structure( 5, 40));
	    
	//d20 hit chart
        $this->hitChart = array(
            0=> array(
                    10 => "Structure",
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
                    10 => "Structure",
                    12 => "Scanner",
                    15 => "Engine",
                    16 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
           		 ),
            1=> array(
                    4 => "Thruster",
                    5 => "31:Antiproton Defender",
                    6 => "41:Antiproton Defender",  
                    7 => "0:Antimatter Cannon",
					8 => "0:Antiproton Defender",                    
                    17 => "Structure",
                    20 => "Primary",
           		 ),
            2=> array(
                    4 => "Thruster",
                    5 => "32:Antiproton Defender",
                    6 => "42:Antiproton Defender",  
                    7 => "0:Antimatter Cannon",
					8 => "0:Antiproton Defender",                    
                    17 => "Structure",
                    20 => "Primary",
           		 ),                  
            31=> array(
                    4 => "32:Thruster", 
                    5 => "31:Antiproton Defender",
                    6 => "32:Antiproton Defender",  
                    7 => "0:Antimatter Cannon",
					8 => "0:Antiproton Defender",                    
                    17 => "Structure",
                    20 => "Primary",
           		 ),     
            32=> array(
                    4 => "Thruster", 
                    5 => "31:Antiproton Defender",
                    6 => "32:Antiproton Defender",  
                    7 => "0:Antimatter Cannon",
					8 => "0:Antiproton Defender",                    
                    17 => "Structure",
                    20 => "Primary",
           		 ),   
            41=> array(
                    4 => "42:Thruster", 
                    5 => "41:Antiproton Defender",
                    6 => "42:Antiproton Defender",  
                    7 => "0:Antimatter Cannon",
					8 => "0:Antiproton Defender",                    
                    17 => "Structure",
                    20 => "Primary",
           		 ),   
       		42=> array(
                    4 => "Thruster", 
                    5 => "41:Antiproton Defender",
                    6 => "42:Antiproton Defender",  
                    7 => "0:Antimatter Cannon",
					8 => "0:Antiproton Defender",                    
                    17 => "Structure",
                    20 => "Primary",
           		 ),   
           	);
			*/
       		
		}
	}
		
?>		
