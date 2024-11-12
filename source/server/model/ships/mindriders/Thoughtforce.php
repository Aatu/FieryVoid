<?php
class Thoughtforce extends MindriderCapital{
     
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 3750;
		$this->faction = "Mindriders";
        $this->phpclass = "Thoughtforce";
        $this->imagePath = "img/ships/MindriderThoughtForce.png";
        $this->shipClass = "Thoughtforce";
        $this->shipSizeClass = 3;
	    $this->isd = 'Ancient';
		$this->factionAge = 3;
		$this->canvasSize = 250;							    

		$this->notes = 'Special Hull Arrangement';
		$this->notes .= '<br>Ignores Manoeuvre Hit Modifiers';			
	    
		$this->fighters = array("normal" => 12);	
		
        $this->gravitic = true;
		$this->advancedArmor = true; 		    
		
        $this->forwardDefense = 14;
        $this->sideDefense = 14;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 4;
        $this->pivotcost = 0;
		$this->ignoreManoeuvreMods = true;         
       
		$this->iniativebonus = 2 *5;   
		
		/*Use their own enhancement set */		
		Enhancements::nonstandardEnhancementSet($this, 'MindriderShip');				     
        
        $this->addPrimarySystem(new Reactor(7, 35, 0, 0));
		$cnc = new CnC(8, 16, 0, 0);
		$cnc->startArc = 0;
		$cnc->endArc = 360;
        $this->addPrimarySystem($cnc);
		$cnc = new SecondaryCnC(8, 16, 0, 0);
		$cnc->startArc = 0;
		$cnc->endArc = 360;
        $this->addPrimarySystem($cnc);     

//        $this->addPrimarySystem(new CnC(8, 16, 0, 0));
//        $this->addPrimarySystem(new SecondaryCnC(8, 16, 0, 0));        
        $scanner = new ElintScanner(7, 22, 0, 12);
		$scanner->markMindrider();
		$this->addPrimarySystem($scanner);	        
        $this->addPrimarySystem(new Engine(7, 23, 0, 12, 4));
        $this->addPrimarySystem(new MindriderHangar(6, 12, 0, 12));        
		$this->addPrimarySystem(new ThoughtShieldGenerator(7, 24, 8, 25)); //$armor, $maxhealth, $power used, output				
        $this->addPrimarySystem(new UltraPulseCannon(7, 0, 0, 0, 360));
        $this->addPrimarySystem(new SecondSight(7, 0, 0, 0, 360));
		$this->addPrimarySystem(new JumpEngine(7, 25, 8, 8));   		  		
      
	
		$this->addPrimarySystem(new ThoughtShield(0, 100, 25, 270, 360, 'FP'));
		$this->addFrontSystem(new SelfRepair(6, 6, 3)); //armor, structure, output 
		$tPulsar = new TriopticPulsar(6, 0, 0, 270, 90);
		$tPulsar->addTag("Trioptic Pulsar");
		$this->addFrontSystem($tPulsar);           
        $this->addFrontSystem(new MindriderThruster(6, 35, 0, 12, 1));           
			
	
		$this->addPrimarySystem(new ThoughtShield(0, 100, 25, 180, 270, 'AP')); 	
		$this->addAftSystem(new SelfRepair(6, 6, 3)); //armor, structure, output 
		$tPulsar = new TriopticPulsar(6, 0, 0, 90, 270);
		$tPulsar->addTag("Trioptic Pulsar");
		$this->addAftSystem($tPulsar);         
        $this->addAftSystem(new MindriderThruster(6, 35, 0, 12, 2));        
       
        	
		$this->addPrimarySystem(new ThoughtShield(0, 100, 25, 90, 180, 'AS'));	              
		$this->addLeftSystem(new SelfRepair(6, 6, 3)); //armor, structure, output 
		$tPulsar = new TriopticPulsar(6, 0, 0, 180, 360);
		$tPulsar->addTag("Trioptic Pulsar");
		$this->addLeftSystem($tPulsar);
        $this->addLeftSystem(new MindriderThruster(6, 35, 0, 12, 3));                                                               
                 	
		$this->addPrimarySystem(new ThoughtShield(0, 100, 25, 0, 90, 'FS'));	
		$this->addRightSystem(new SelfRepair(6, 6, 3)); //armor, structure, output 
		$tPulsar = new TriopticPulsar(6, 0, 0, 0, 180);
		$tPulsar->addTag("Trioptic Pulsar");
		$this->addRightSystem($tPulsar);	         
        $this->addRightSystem(new MindriderThruster(6, 35, 0, 12, 4));    			                  		                      
              			          
		//structures
        $this->addFrontSystem(new Structure(7, 88));
        $this->addAftSystem(new Structure(7, 88));
        $this->addLeftSystem(new Structure(7, 88));
        $this->addRightSystem(new Structure(7, 88));
        $this->addPrimarySystem(new Structure(8, 90));
		
		
		$this->hitChart = array(
			0=> array( //PRIMARY
				8 => "Structure",
				10 => "Ultra Pulse Cannon",
				12 => "Thought Shield",
				13 => "Second Sight",
				14 => "Scanner",
				15 => "Hangar",				
				16 => "Engine",
				17 => "Jump Engine",
				19 => "Reactor",
				20 => "TAG:C&C",
			),
			1=> array( //Fwd
				4 => "TAG:Thruster",
				6 => "TAG:Trioptic Pulsar", 
				8 => "Self Repair",
				16 => "Structure",
				20 => "Primary",
			),
			2=> array( //Aft
				4 => "TAG:Thruster",
				6 => "TAG:Trioptic Pulsar", 
				8 => "Self Repair",
				16 => "Structure",
				20 => "Primary",
			),
			3=> array(
				4 => "TAG:Thruster",
				6 => "TAG:Trioptic Pulsar", 
				8 => "Self Repair",
				16 => "Structure",
				20 => "Primary",
			),
			4=> array(
				4 => "TAG:Thruster",
				6 => "TAG:Trioptic Pulsar", 
				8 => "Self Repair",
				16 => "Structure",
				20 => "Primary",
			),

		);
		
    }
}


?>
