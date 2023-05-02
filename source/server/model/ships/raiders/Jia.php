<?php
class Jia extends VreeCapital{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 425;
		$this->faction = "Raiders";
		$this->phpclass = "Jia";
		$this->shipClass = "Jia Recycling Transport";
		$this->isd = 2175;
		$this->locations = array(41, 42, 2, 32, 31, 1);	
        $this->fighters = array("normal"=>12);
		$this->notes = 'Used only by the Vree Salvage Guild';
   	    $this->notes .= '<br>Sluggish';
 	    $this->notes .= '<br>Vulnerable to Criticals';					
        $this->limited = 33;
        
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
		
        $this->iniativebonus = -7;
        $this->critRollMod += 1;
       	$this->enhancementOptionsDisabled[] = 'SLUGGISH';
		$this->enhancementOptionsDisabled[] = 'VULN_CRIT';
		$this->enhancementOptionsDisabled[] = 'IMPR_ENG';				

		$this->imagePath = "img/ships/RaiderVSGJia.png";
		$this->canvasSize = 200;

		$this->addPrimarySystem(new Reactor(5, 15, 0, 0));
		$this->addPrimarySystem(new Hangar(5, 6));
		$this->addPrimarySystem(new Hangar(5, 6));
		$this->addPrimarySystem(new Hangar(5, 2));				
		$this->addPrimarySystem(new CnC(5, 16, 0, 0));
		$this->addPrimarySystem(new Scanner(5, 10, 6, 5));
		$engine = new Engine(5, 11, 0, 6, 6);
			$engine->markEngineFlux();
			$this->addPrimarySystem($engine);
		$this->addPrimarySystem(new CargoBay(4, 25));
        $this->addPrimarySystem(new JumpEngine(4, 10, 5, 36));		
		$this->addPrimarySystem(new TractorBeam(4, 4, 0, 0));
		$this->addPrimarySystem(new TractorBeam(4, 4, 0, 0));		

        $this->addFrontSystem(new AntiprotonDefender(3, 0, 0, 300, 60));
		$this->addFrontSystem(new GraviticThruster(4, 14, 0, 6, 1));   
		$this->addFrontSystem(new CargoBay(3, 15));        
     	
        $this->addAftSystem(new AntiprotonDefender(3, 0, 0, 120, 240));
		$this->addAftSystem(new GraviticThruster(4, 14, 0, 6, 2)); 
		$this->addAftSystem(new CargoBay(3, 15));        
     
		$this->addLeftFrontSystem(new AntiprotonDefender(3, 0, 0, 240, 360));
		$this->addLeftFrontSystem(new CargoBay(3, 15));		
				
		$this->addLeftAftSystem(new GraviticThruster(4, 14, 0, 6, 3));
		$this->addLeftAftSystem(new AntiprotonDefender(3, 0, 0, 180, 300));
		$this->addLeftAftSystem(new CargoBay(3, 15));		
		
		$this->addRightFrontSystem(new AntiprotonDefender(3, 0, 0, 0, 120));
		$this->addRightFrontSystem(new CargoBay(3, 15));					
	
		$this->addRightAftSystem(new GraviticThruster(4, 14, 0, 6, 4));	
		$this->addRightAftSystem(new AntiprotonDefender(3, 0, 0, 60, 180));
		$this->addRightAftSystem(new CargoBay(3, 15));
       
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 45, true));
        $this->addAftSystem(new Structure( 4, 45, true));
        $this->addLeftFrontSystem(new Structure( 4, 45, true));
        $this->addLeftAftSystem(new Structure( 4, 45, true));
        $this->addRightFrontSystem(new Structure( 4, 45, true));
        $this->addRightAftSystem(new Structure( 4, 45, true));      
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
                    7 => "Antiproton Defender",                    
                    17 => "Structure",
                    20 => "Primary",
           		 ),
            2=> array(
                    4 => "Thruster",
                    6 => "Cargo Bay",
                    7 => "Antiproton Defender",                    
                    17 => "Structure",
                    20 => "Primary",
           		 ),
            31=> array(
                    4 => "32:Thruster",
                    6 => "Cargo Bay",
                    7 => "Antiproton Defender",                    
                    17 => "Structure",
                    20 => "Primary",
           		 ),
            32=> array(
                    4 => "Thruster",
                    6 => "Cargo Bay",
                    7 => "Antiproton Defender",                    
                    17 => "Structure",
                    20 => "Primary",
           		 ),
            41=> array(
                    4 => "42:Thruster",
                    6 => "Cargo Bay",
                    7 => "Antiproton Defender",                    
                    17 => "Structure",
                    20 => "Primary",
           		 ),
       		42=> array(
                    4 => "Thruster",
                    6 => "Cargo Bay",
                    7 => "Antiproton Defender",                    
                    17 => "Structure",
                    20 => "Primary",
           		 ),
           	);
       		
		}
	}
		
?>		
