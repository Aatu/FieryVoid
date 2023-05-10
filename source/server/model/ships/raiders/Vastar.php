<?php
class Vastar extends VreeHCV{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 460;
		$this->faction = "Raiders";
		$this->phpclass = "Vastar";
		$this->shipClass = "Vastar Saucer";
		$this->isd = 2180;		
		$this->notes = 'Used only by the Vree Salvage Guild';
		
		$this->iniativebonus = 6 *5; 
		
        $this->turncost = 0.66;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 3;
        $this->pivotcost = 0;	
        $this->gravitic = true;        	

		$this->forwardDefense = 13;
		$this->sideDefense = 13;

		$this->imagePath = "img/ships/RaiderVSGVastar.png";
		$this->canvasSize = 200;

		$this->addPrimarySystem(new Reactor(4, 14, 0, 0));
		$this->addPrimarySystem(new Hangar(4, 2));
		$this->addPrimarySystem(new CnC(4, 12, 0, 0));
		$this->addPrimarySystem(new Scanner(4, 12, 5, 6));
        $this->addPrimarySystem(new Engine(4, 11, 0, 7, 4));
		$this->addPrimarySystem(new CargoBay(3, 20));            			
		$this->addPrimarySystem(new HeavyPlasma(2, 8, 5, 0, 360));
		$this->addPrimarySystem(new HeavyPlasma(2, 8, 5, 0, 360));				
		$this->addPrimarySystem(new GraviticThruster(3, 12, 0, 7, 3));
		$this->addPrimarySystem(new GraviticThruster(3, 12, 0, 7, 4));
		$this->addPrimarySystem(new GraviticThruster(3, 12, 0, 7, 1));		
        $this->addPrimarySystem(new GraviticThruster(3, 12, 0, 7, 2));						

		$this->addFrontSystem(new AntiprotonDefender(2, 0, 0, 240, 0));
		$this->addFrontSystem(new AntiprotonDefender(2, 0, 0, 300, 60)); 
		$this->addFrontSystem(new AntiprotonDefender(2, 0, 0, 300, 60)); 
		$this->addFrontSystem(new AntiprotonDefender(2, 0, 0, 0, 120)); 

		$this->addAftSystem(new AntiprotonDefender(2, 0, 0, 180, 300));  
		$this->addAftSystem(new AntiprotonDefender(2, 0, 0, 120, 240)); 		
 		$this->addAftSystem(new AntiprotonDefender(2, 0, 0, 120, 240));
		$this->addAftSystem(new AntiprotonDefender(2, 0, 0, 60, 180));	      
       
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 50, true));
        $this->addAftSystem(new Structure( 4, 50, true));
        $this->addPrimarySystem(new Structure( 4, 50));
	    
	//d20 hit chart
        $this->hitChart = array(
            0=> array(
                    10 => "Structure",
                    12 => "Cargo Bay",
                    14 => "Scanner",
                    16 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
           		 ),
            1=> array(
                    4 => "0:Thruster",                        
                    7 => "Antiproton Defender",
                    8 => "0:Heavy Plasma Cannon",
                    17 => "Structure",
                    20 => "Primary",
           		 ),
            2=> array(
                    4 => "0:Thruster",   
                    7 => "Antiproton Defender",
                    8 => "0:Heavy Plasma Cannon",
                    17 => "Structure",
                    20 => "Primary",
           		 ),
           	);
       		
		}
	}
		
?>		
