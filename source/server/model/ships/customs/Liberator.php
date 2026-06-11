<?php
class Liberator extends Liberatorcapitalship{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 3250;
		$this->faction = "Custom Ships";
        $this->phpclass = "Liberator";
        $this->shipClass = "Heavy Cruiser";
       
        $this->imagePath = "img/ships/Liberator.png";
        $this->canvasSize = 250;
	    $this->isd = 'Ancient';
        $this->shipSizeClass = 3; 
		$this->factionAge = 3; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
				
        $this->gravitic = true;
		$this->advancedArmor = true;   
        
        $this->forwardDefense = 13;
        $this->sideDefense = 16;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 3;
        $this->rollcost = 1;
        $this->pivotcost = 2;
		$this->iniativebonus = 3 *5;
		
		
		
         
 
        $this->addPrimarySystem(new CnC(6, 15, 0, 0));
		$scanner = new Scanner(6, 20, 4, 12);
		$scanner->markAdvanced();
		$this->addPrimarySystem($scanner);			
		$this->addPrimarySystem(new Hangar(6, 6, 6));
		$this->addPrimarySystem(new Engine(6, 20, 0, 16, 3));
		$this->addPrimarySystem(new ThoughtShieldGenerator(6, 15, 6, 15)); //$armor, $maxhealth, $power used, output
		$this->addPrimarySystem(new ShieldReinforcement(6, 20, 10, 0, 360, 0));//Armour, Health, Power, sArc, eArc, Output. 
        $this->addPrimarySystem(new SelfRepair(6, 12, 6)); //armor, structure, output
		$AAC = $this->createAdaptiveArmorController(6, 3, 3); //$AAtotal, $AApertype, $AApreallocated
		$this->addPrimarySystem( $AAC );
		
		
 
        $this->addFrontSystem(new GraviticThruster(5, 15, 0, 5, 1));
        $this->addFrontSystem(new GraviticThruster(5, 15, 0, 5, 1));
		$this->addFrontSystem(new HypergravitonBlaster(7, 30, 15, 300, 60));
		$this->addFrontSystem(new HypergravitonBlaster(7, 30, 15, 300, 60));
		$this->addFrontSystem(new ThoughtShield(0, 30, 15, 120, 240, 'F'))


		$this->addAftSystem(new JumpEngine(5, 20, 0, 8))
		$this->addAftSystem(new ThoughtShield(0, 30, 15, 120, 240, 'A'));  	                
		$this->addAftSystem(new GraviticThruster(5, 15, 0, 4, 2));
		$this->addAftSystem(new HypergravitonBlaster(7, 30, 15, 120, 240));
		$this->addAftSystem(new GraviticThruster(5, 15, 0, 4, 2));
		$this->addAftSystem(new GraviticThruster(5, 15, 0, 4, 2));
		$this->addAftSystem(new GraviticThruster(5, 15, 0, 4, 2));
		

        $this->addLeftSystem(new GraviticThruster(5, 25, 0, 7, 3));
		$this->addLeftSystem(new ThoughtShield(0, 30, 15, 240, 360, 'FP'));	
		$this->addLeftSystem(new HypergravitonBlaster(7, 30, 15, 180, 300));	
		
      
        $this->addRightSystem(new GraviticThruster(5, 25, 0, 7, 4));
		$this->addRightSystem(new ThoughtShield(0, 30, 15, 0, 120, 'FS')); 		
		$this->addRightSystem(new HypergravitonBlaster(7, 30, 15, 0, 120));
		

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 6, 60));
        $this->addAftSystem(new Structure( 6, 65));
        $this->addLeftSystem(new Structure( 6, 55));
        $this->addRightSystem(new Structure( 6, 55));
        $this->addPrimarySystem(new Structure( 6, 66 ));
		
		
	
		$this->hitChart = array(
			0=> array( //PRIMARY
				8 => "Structure",
                                10 => "Thought Shield",
				12 => "Self Repair",
				14 => "Scanner",
				16 => "Engine",
			        18 => "Shield Reinforcement",
			
				20 => "C&C",
			),
			1=> array( //Fwd
				4 => "Thruster",
				8 => "Thought Shield",
                                10 => "Hypergraviton Blaster",
				
				
				18 => "Structure",
				20 => "Primary",
			),
			2=> array( //Aft
				6 => "Thruster",
				8 => "Jump Engine",
				10 => "Thought Shield",
                                12 => "Hypergraviton Blaster",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array( //left
				6 => "Thruster",
				10 => "Thought Shield",
                                12 => "Hypergraviton Blaster",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array( //right
				6 => "Thruster",
				10 => "Thought Shield",
                                12 => "Hypergraviton Blaster",
				18 => "Structure",
				20 => "Primary",
			
			),
		);
		
    }
}



?>
