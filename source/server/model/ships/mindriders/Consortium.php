<?php
class Consortium extends MindriderHCV{
     
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 2125;
		$this->faction = "Mindriders";
        $this->phpclass = "Consortium";
        $this->imagePath = "img/ships/MindriderConsortium.png";
        $this->shipClass = "Consortium";
        $this->shipSizeClass = 2;
	    $this->isd = 'Ancient';
		$this->factionAge = 3;
		$this->canvasSize = 200;							    

		$this->notes = 'Special Hull Arrangement';
		$this->notes .= '<br>Ignores Manoeuvre Hit Modifiers';		
		
        $this->gravitic = true;
		$this->advancedArmor = true; 		    
		
        $this->forwardDefense = 12;
        $this->sideDefense = 12;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 0;
		$this->ignoreManoeuvreMods = true;         
       
		$this->iniativebonus = 8 *5;   
		
		/*Use their own enhancement set */		
		Enhancements::nonstandardEnhancementSet($this, 'MindriderShip');				     

		      
        $this->addPrimarySystem(new Reactor(6, 20, 0, 0));
		$cnc = new CnC(7, 16, 0, 0);
		$cnc->startArc = 0;
		$cnc->endArc = 360;
        $this->addPrimarySystem($cnc);
		$cnc = new SecondaryCnC(7, 16, 0, 0);
		$cnc->startArc = 0;
		$cnc->endArc = 360;
        $this->addPrimarySystem($cnc); 

//        $this->addPrimarySystem(new CnC(7, 16, 0, 0));
//        $this->addPrimarySystem(new SecondaryCnC(7, 16, 0, 0));        
        $scanner = new ElintScanner(6, 24, 0, 10);
		$scanner->markMindrider();
		$this->addPrimarySystem($scanner);	        
        $this->addPrimarySystem(new Engine(7, 15, 0, 10, 4));
		$this->addPrimarySystem(new JumpEngine(7, 25, 8, 8)); 
        $this->addPrimarySystem(new MindriderHangar(0, 1, 0, 0)); 	//technical system only		                	
		$this->addPrimarySystem(new ThoughtShieldGenerator(6, 15, 6, 15)); //$armor, $maxhealth, $power used, output
 		$this->addPrimarySystem(new ThoughtShield(0, 60, 15, 120, 240, 'A'));            
        $this->addPrimarySystem(new TelekineticCutter(6, 0, 0, 0, 360));	   	       
        $this->addPrimarySystem(new TelekineticCutter(6, 0, 0, 0, 360));         
        $this->addPrimarySystem(new TelekineticCutter(6, 0, 0, 0, 360)); 
        $this->addPrimarySystem(new ShieldReinforcement(6, 20, 10, 0, 360, 0));//Armour, Health, Power, sArc, eArc, Output.     
        
        $this->addFrontSystem(new MindriderThruster(6, 30, 0, 10, 1));       
        
		$this->addLeftFrontSystem(new ThoughtShield(0, 60, 15, 240, 360, 'FP'));	
		$this->addRightFrontSystem(new ThoughtShield(0, 60, 15, 0, 120, 'FS')); 
							
		$this->addLeftSystem(new SelfRepair(6, 6, 3)); //armor, structure, output 
		$tPulsar = new TriopticPulsar(6, 0, 0, 240, 60);
		$tPulsar->addTag("Trioptic Pulsar");
		$this->addLeftSystem($tPulsar);		
		$tPulsar = new TriopticPulsar( 6, 0, 0, 240, 60);
		$tPulsar->addTag("Trioptic Pulsar");
		$this->addLeftSystem($tPulsar);		
        $this->addLeftSystem(new MindriderThruster(6, 30, 0, 10, 3));         			
	
	
		$this->addAftSystem(new SelfRepair(6, 6, 3)); //armor, structure, output 
		$tPulsar = new TriopticPulsar(6, 0, 0, 90, 270);
		$tPulsar->addTag("Trioptic Pulsar");
		$this->addAftSystem($tPulsar);		
		$tPulsar = new TriopticPulsar( 6, 0, 0, 90, 270);
		$tPulsar->addTag("Trioptic Pulsar"); 
		$this->addAftSystem($tPulsar);		             
        $this->addAftSystem(new MindriderThruster(6, 30, 0, 10, 2));                                                                       
               	
		$this->addRightSystem(new SelfRepair(6, 6, 3)); //armor, structure, output 
		$tPulsar = new TriopticPulsar(6, 0, 0, 300, 120);
		$tPulsar->addTag("Trioptic Pulsar");
		$this->addRightSystem($tPulsar);		
		$tPulsar = new TriopticPulsar(6, 0, 0, 300, 120);
		$tPulsar->addTag("Trioptic Pulsar");
		$this->addRightSystem($tPulsar);	         
        $this->addRightSystem(new MindriderThruster(6, 30, 0, 10, 4));    			                  		                    

              			          
		//structures
        $this->addAftSystem(new Structure(7, 88));
        $this->addLeftSystem(new Structure(7, 88));
        $this->addRightSystem(new Structure(7, 88));
        $this->addPrimarySystem(new Structure(7, 60));
		

		
		$this->hitChart = array(
			0=> array( //PRIMARY
				8 => "Structure",
				10 => "Shield Reinforcement",
				11 => "Telekinetic Cutter",
				12 => "Thought Shield",
				14 => "Scanner",			
				16 => "Engine",
				17 => "Jump Engine",
				19 => "Reactor",
				20 => "TAG:C&C",
			),
			2=> array( //Aft
				6 => "TAG:Thruster",
				8 => "TAG:Trioptic Pulsar",  
				10 => "Self Repair",
				17 => "Structure",
				20 => "Primary",
			),
			31=> array(
				4 => "TAG:Thruster",
				6 => "TAG:Trioptic Pulsar", 
				8 => "32:Self Repair",
				17 => "32:Structure",
				20 => "Primary",
			),			
			32=> array(
				4 => "TAG:Thruster",
				6 => "TAG:Trioptic Pulsar", 
				8 => "Self Repair",
				17 => "Structure",
				20 => "Primary",
			),
			41=> array(
				4 => "TAG:Thruster",
				6 => "TAG:Trioptic Pulsar", 
				8 => "42:Self Repair",
				17 => "42:Structure",
				20 => "Primary",
			),			
			42=> array(
				4 => "TAG:Thruster",
				6 => "TAG:Trioptic Pulsar", 
				8 => "Self Repair",
				17 => "Structure",
				20 => "Primary",
			),

		);
		
    }
}


?>
