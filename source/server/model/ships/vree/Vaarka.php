<?php
class Vaarka extends HeavyCombatVessel{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 675;
		$this->faction = "Vree";
		$this->phpclass = "Vaarka";
		$this->shipClass = "Vaarka Escort Saucer";
		$this->isd = 2257;
  		$this->occurence = "rare";
    	$this->variantOf = 'Vaarl Scout Saucer';		


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
		$this->addPrimarySystem(new Thruster(3, 12, 0, 7, 3));
		$this->addPrimarySystem(new Thruster(3, 12, 0, 7, 4));		

        $this->addFrontSystem(new Thruster(3, 12, 0, 7, 1));
		$this->addFrontSystem(new AntiprotonDefender(2, 0, 0, 240, 0));
		$this->addFrontSystem(new AntiprotonDefender(2, 0, 0, 300, 60)); 
		$this->addFrontSystem(new AntiprotonDefender(2, 0, 0, 300, 60)); 
		$this->addFrontSystem(new AntiprotonDefender(2, 0, 0, 0, 120)); 						        
		
        $this->addAftSystem(new Thruster(3, 12, 0, 7, 2));
		$this->addAftSystem(new AntiprotonDefender(2, 0, 0, 180, 300));
		$this->addAftSystem(new AntiprotonDefender(2, 0, 0, 120, 240)); 
		$this->addAftSystem(new AntiprotonDefender(2, 0, 0, 120, 240)); 
		$this->addAftSystem(new AntiprotonDefender(2, 0, 0, 60, 180));         
        
	
       
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
                    2 => "Thruster",
                    4 => "0:Thruster",
                    5 => "0:Antimatter Cannon",
                    9 => "Antiproton Defender",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            2=> array(
                    2 => "Thruster",
                    4 => "0:Thruster",
                    5 => "0:Antimatter Cannon",
                    9 => "Antiproton Defender",
                    18 => "Structure",
                    20 => "Primary",
           		 ),

           	);
       		
		}
	}
		
?>		
