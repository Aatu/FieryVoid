<?php
class WheelofThought extends MindriderMCV{
     
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 850;
		$this->faction = "Mindriders";
        $this->phpclass = "WheelofThought";
        $this->imagePath = "img/ships/MindriderWoT.png";
        $this->shipClass = "Wheel of Thought";
        $this->shipSizeClass = 1;
	    $this->isd = 'Ancient';
		$this->factionAge = 4;
		$this->canvasSize = 100;							    

		$this->notes = 'Special Hull Arrangement';
		$this->notes .= '<br>Ignores manoeuvre hit modifiers';		

        $this->agile = true;		
        $this->gravitic = true;
		$this->advancedArmor = true; 		    
		
        $this->forwardDefense = 10;
        $this->sideDefense = 10;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 3;
        $this->rollcost = 1;
        $this->pivotcost = 0;
		$this->ignoreManoeuvreMods = true;         
       
		$this->iniativebonus = 14 *5;   
		
		/*Use their own enhancement set */		
		Enhancements::nonstandardEnhancementSet($this, 'MindriderShip');				     

		      
        $this->addPrimarySystem(new Reactor(7, 20, 0, 12));
        $this->addPrimarySystem(new CnC(8, 12, 0, 0));
        $this->addPrimarySystem(new SecondaryCnC(8, 12, 0, 0));        
        $scanner = new ElintScanner(7, 12, 0, 10);
		$scanner->markMindrider();
		$this->addPrimarySystem($scanner);	        
        $this->addPrimarySystem(new Engine(7, 16, 0, 8, 3));
		$this->addPrimarySystem(new JumpEngine(7, 11, 6, 10)); 
        $this->addPrimarySystem(new MindriderHangar(0, 1, 0, 0)); 	//technical system only		                	
		$this->addPrimarySystem(new ThoughtShieldGenerator(6, 15, 4, 15)); //$armor, $maxhealth, $power used, output           
        $this->addPrimarySystem(new TelekineticCutter(6, 0, 0, 0, 360));
        $this->addPrimarySystem(new GraviticThruster(6, 15, 0, 8, 3));         
        $this->addPrimarySystem(new GraviticThruster(6, 15, 0, 8, 4));         	   	       
        
                   
		$this->addFrontSystem(new ThoughtShield(0, 60, 15, 270, 90, 'F'));	
		$this->addFrontSystem(new TriopticPulsar(6, 0, 0, 270, 90));		
		$this->addFrontSystem(new SelfRepair(6, 6, 3)); //armor, structure, output 
        $this->addFrontSystem(new GraviticThruster(6, 15, 0, 8, 1)); 		 
		
		
		$this->addAftSystem(new ThoughtShield(0, 60, 15, 90, 270, 'A')); 
		$this->addAftSystem(new TriopticPulsar(6, 0, 0, 90, 270));		
		$this->addAftSystem(new SelfRepair(6, 6, 3)); //armor, structure, output     
		$this->addAftSystem(new GraviticThruster(6, 15, 0, 8, 2)); 		  			                  		                    
              			          
		//structures
        $this->addPrimarySystem(new Structure(8, 80));	

		
		$this->hitChart = array(
			0=> array( //PRIMARY
				8 => "Structure",
				10 => "Thruster",
				11 => "Telekinetic Cutter",
				12 => "Thought Shield",
				14 => "Scanner",			
				16 => "Engine",
				17 => "Jump Engine",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				4 => "Thruster",
				6 => "Trioptic Pulsar", 
				8 => "Self Repair",
				17 => "Structure",
				20 => "Primary",
			),
			2=> array(
				4 => "Thruster",
				6 => "Trioptic Pulsar", 
				8 => "Self Repair",
				17 => "Structure",
				20 => "Primary",
			),
		);
		
    }
}


?>
