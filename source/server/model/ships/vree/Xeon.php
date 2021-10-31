<?php
class Xeon extends VreeCapital{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 340;
		$this->faction = "Vree";
		$this->phpclass = "Xeon";
		$this->shipClass = "Xeon Assault Saucer";
		$this->isd = 2225;
		$this->locations = array(41, 42, 2, 32, 31, 1);
        $this->fighters = array("assault shuttles"=>12);
  		$this->occurence = "uncommon";
    	$this->variantOf = 'Xeel War Carrier';	        				

		$this->shipSizeClass = 3; 
		$this->iniativebonus = 0;
		
        $this->turncost = 0.66;
        $this->turndelaycost = 0.5;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 0;	
        $this->gravitic = true;        	

		$this->forwardDefense = 14;
		$this->sideDefense = 14;

		$this->imagePath = "img/ships/VreeXeel.png";
		$this->canvasSize = 200;

		$this->addPrimarySystem(new Reactor(5, 13, 0, 0));
		$this->addPrimarySystem(new Hangar(5, 14));
		$this->addPrimarySystem(new CnC(5, 12, 0, 0));
		$this->addPrimarySystem(new Scanner(5, 10, 7, 7));
        $this->addPrimarySystem(new Engine(5, 11, 0, 7, 2));

        $this->addFrontSystem(new Thruster(4, 12, 0, 7, 1));
        $this->addFrontSystem(new AntiprotonDefender(3, 0, 0, 300, 60));
		
        $this->addAftSystem(new Thruster(4, 12, 0, 7, 2));
        $this->addAftSystem(new AntiprotonDefender(3, 0, 0, 120, 240));
        
		$this->addLeftFrontSystem(new AntiprotonDefender(3, 0, 0, 240, 360));
				
		$this->addLeftAftSystem(new Thruster(4, 12, 0, 7, 3));
		$this->addLeftAftSystem(new AntiprotonDefender(3, 0, 0, 180, 300));
		
		$this->addRightFrontSystem(new AntiprotonDefender(3, 0, 0, 0, 120));			

		$this->addRightAftSystem(new Thruster(4, 12, 0, 7, 4));  
		$this->addRightAftSystem(new AntiprotonDefender(3, 0, 0, 60, 180));	
       
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 21, true));
        $this->addAftSystem(new Structure( 4, 21, true));
        $this->addLeftFrontSystem(new Structure( 4, 21, true));
        $this->addLeftAftSystem(new Structure( 4, 21, true));
        $this->addRightFrontSystem(new Structure( 4, 21, true));
        $this->addRightAftSystem(new Structure( 4, 21, true));      
        $this->addPrimarySystem(new Structure( 5, 36));
	    
	//d20 hit chart
        $this->hitChart = array(
            0=> array(
                    9 => "Structure",
                    11 => "Scanner",
                    14 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
           		 ),
            1=> array(
                    4 => "Thruster",
                    6 => "Antiproton Defender",
                    17 => "Structure",
                    20 => "Primary",
           		 ),
            2=> array(
                    4 => "Thruster",
                    6 => "Antiproton Defender",
                    17 => "Structure",
                    20 => "Primary",
           		 ),
            31=> array(
                    4 => "32:Thruster", 
                    6 => "Antiproton Defender",
                    17 => "Structure",
                    20 => "Primary",
           		 ),
            32=> array(
                    4 => "Thruster", 
                    6 => "Antiproton Defender",
                    17 => "Structure",
                    20 => "Primary",
           		 ),
            41=> array(
                    4 => "42:Thruster",
                    6 => "Antiproton Defender",
                    17 => "Structure",
                    20 => "Primary",
           		 ),
       		42=> array(
                    4 => "Thruster",
                    6 => "Antiproton Defender",
                    17 => "Structure",
                    20 => "Primary",
           		 ),
           	);
       		
		}
	}
		
?>		
