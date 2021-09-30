<?php
class Ximm extends VreeCapital{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 550;
		$this->faction = "Vree";
		$this->phpclass = "Ximm";
		$this->shipClass = "Ximm Close Escort";
		$this->isd = 2219;
		$this->locations = array(41, 42, 2, 32, 31, 1);
  		$this->occurence = "uncommon";
    	$this->variantOf = 'Xorr War Saucer';					

		$this->shipSizeClass = 3; //Enormous is not implemented
		$this->iniativebonus = 0; //no voluntary movement anyway
		
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
		$this->addPrimarySystem(new Thruster(4, 14, 0, 8, 3));
		$this->addPrimarySystem(new Thruster(4, 14, 0, 8, 4));			

        $this->addFrontSystem(new Thruster(4, 14, 0, 8, 1));
		
        $this->addAftSystem(new Thruster(4, 14, 0, 8, 2));
        
		$this->addLeftFrontSystem(new AntiprotonDefender(3, 0, 0, 240, 360));
		$this->addLeftFrontSystem(new AntiprotonDefender(3, 0, 0, 240, 360));		
				
		$this->addLeftAftSystem(new AntiprotonDefender(3, 0, 0, 180, 300));
		$this->addLeftAftSystem(new AntiprotonDefender(3, 0, 0, 180, 300));		
		
		$this->addRightFrontSystem(new AntiprotonDefender(3, 0, 0, 0, 120));
		$this->addRightFrontSystem(new AntiprotonDefender(3, 0, 0, 0, 120));		
				
		$this->addRightAftSystem(new AntiprotonDefender(3, 0, 0, 60, 180));
		$this->addRightAftSystem(new AntiprotonDefender(3, 0, 0, 60, 180));		
		
       
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 24));
        $this->addAftSystem(new Structure( 4, 24));
        $this->addLeftFrontSystem(new Structure( 4, 24));
        $this->addLeftAftSystem(new Structure( 4, 24));
        $this->addRightFrontSystem(new Structure( 4, 24));
        $this->addRightAftSystem(new Structure( 4, 24));      
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
                    4 => "Thruster",
                    5 => "0:Antimatter Cannon",
					6 => "0:Antiproton Defender",                    
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            2=> array(
                    4 => "Thruster",
                    5 => "0:Antimatter Cannon",
					6 => "0:Antiproton Defender",                    
                    18 => "Structure",
                    20 => "Primary",
           		 ),                    
            31=> array(
                    1 => "1:Thruster",
                    4 => "0:Thruster", 
                    7 => "Antiproton Defender",
					8 => "0:Antiproton Defender",
					9 => "0:Antimatter Cannon",                    
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            32=> array(
                    1 => "2:Thruster",
                    4 => "0:Thruster", 
                    7 => "Antiproton Defender",
					8 => "0:Antiproton Defender",
					9 => "0:Antimatter Cannon",                    
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            41=> array(
                    1 => "1:Thruster",
                    4 => "0:Thruster", 
                    7 => "Antiproton Defender",
					8 => "0:Antiproton Defender",
					9 => "0:Antimatter Cannon",                    
                    18 => "Structure",
                    20 => "Primary",
           		 ),
       		42=> array(
                    1 => "2:Thruster",
                    4 => "0:Thruster", 
                    7 => "Antiproton Defender",
					8 => "0:Antiproton Defender",
					9 => "0:Antimatter Cannon",                    
                    18 => "Structure",
                    20 => "Primary",
           		 ),
           	);
       		
		}
	}
		
?>		
