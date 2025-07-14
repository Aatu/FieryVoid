<?php
class MindsEye extends SixSidedShip{
	public $mindrider = true;
	     
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 8300;
		$this->faction = "Mindriders";
        $this->phpclass = "MindsEye";
        $this->imagePath = "img/ships/MindriderMindsEye0.png";
        $this->shipClass = "Mind's Eye";
        $this->shipSizeClass = 3;
	    $this->isd = 'Ancient';
		$this->factionAge = 4;
		$this->Enormous = true;
		$this->canvasSize = 280;
		$this->mindrider = true;							    

		$this->notes = 'Special Hull Arrangement';
		$this->notes .= '<br>Ignores Manoeuvre Hit Modifiers';	
	    
		$this->fighters = array("normal" => 24);	
		
        $this->gravitic = true;
		$this->advancedArmor = true; 		    
		
        $this->forwardDefense = 20;
        $this->sideDefense = 20;
        
        $this->turncost = 1.33;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 4;
        $this->pivotcost = 0;
		$this->ignoreManoeuvreMods = true;        
       
		$this->iniativebonus = 0;   
		
		/*Use their own enhancement set */		
		Enhancements::nonstandardEnhancementSet($this, 'MindriderShip');				     
        
        $this->addPrimarySystem(new Reactor(7, 50, 0, -24));
		$cnc = new CnC(8, 24, 0, 0);
		$cnc->startArc = 0;
		$cnc->endArc = 360;
        $this->addPrimarySystem($cnc);
		$cnc = new SecondaryCnC(8, 24, 0, 0);
		$cnc->startArc = 0;
		$cnc->endArc = 360;
        $this->addPrimarySystem($cnc);        
               
		$scanner = new ElintScanner(7, 24, 0, 14);//Ancient Scanners do not need power - base systems are included in zero hull running costs
		$scanner->markAdvanced();
		$this->addPrimarySystem($scanner);	        
        $this->addPrimarySystem(new MindriderEngine(7, 28, 0, 16, 4));
        $this->addPrimarySystem(new MindriderHangar(5, 24, 0, 24));        
		$this->addPrimarySystem(new ThoughtShieldGenerator(6, 25, 12, 25)); //$armor, $maxhealth, $power used, output				
        $this->addPrimarySystem(new UltraPulseCannon(7, 0, 0, 0, 360));
        $this->addPrimarySystem(new UltraPulseCannon(7, 0, 0, 0, 360));        
        $this->addPrimarySystem(new SecondSight(7, 0, 0, 0, 360));       
		$this->addPrimarySystem(new ThoughtWave(7, 0, 0, 0, 360, 15));     
		$this->addPrimarySystem(new JumpEngine(7, 25, 8, 8));   		  		
      
		$this->addPrimarySystem(new ThoughtShield(0, 50, 25, 210, 270, 'AP'));
		$this->addPrimarySystem(new ThoughtShield(0, 50, 25, 330, 30, 'F'));			
		$this->addPrimarySystem(new ThoughtShield(0, 50, 25, 30, 90, 'FS'));
		$this->addPrimarySystem(new ThoughtShield(0, 50, 25, 270, 330, 'FP'));
		$this->addPrimarySystem(new ThoughtShield(0, 50, 25, 150, 210, 'A'));	
		$this->addPrimarySystem(new ThoughtShield(0, 50, 25, 90, 150, 'AS'));
		
				
		$this->addFrontSystem(new SelfRepair(6, 6, 3)); //armor, structure, output 
		$tPulsar = new TriopticPulsar(6, 0, 0, 270, 90);
		$tPulsar->addTag("Trioptic Pulsar");
		$this->addFrontSystem($tPulsar);
		$tCutter = new TelekineticCutter(6, 0, 0, 270, 90);
		$tCutter->addTag("Telekinetic Cutter");
		$this->addFrontSystem($tCutter);  		           
        $this->addFrontSystem(new MindriderThruster(6, 35, 0, 12, 1));           
	

		$this->addAftSystem(new SelfRepair(6, 6, 3)); //armor, structure, output 
		$tPulsar = new TriopticPulsar(6, 0, 0, 90, 270);
		$tPulsar->addTag("Trioptic Pulsar");
		$this->addAftSystem($tPulsar);
		$tCutter = new TelekineticCutter(6, 0, 0, 90, 270);
		$tCutter->addTag("Telekinetic Cutter");
		$this->addAftSystem($tCutter);  		           
        $this->addAftSystem(new MindriderThruster(6, 35, 0, 12, 2)); 


		$this->addLeftFrontSystem(new SelfRepair(6, 6, 3)); //armor, structure, output 
		$tPulsar = new TriopticPulsar(6, 0, 0, 240, 60);
		$tPulsar->addTag("Trioptic Pulsar");
		$this->addLeftFrontSystem($tPulsar);
		$tCutter = new TelekineticCutter(6, 0, 0, 240, 60);
		$tCutter->addTag("Telekinetic Cutter");
		$this->addLeftFrontSystem($tCutter);  		           
        $this->addLeftFrontSystem(new MindriderThruster(6, 35, 0, 12, 3)); 
					 	

		$this->addRightFrontSystem(new SelfRepair(6, 6, 3)); //armor, structure, output 
		$tPulsar = new TriopticPulsar(6, 0, 0, 300, 120);
		$tPulsar->addTag("Trioptic Pulsar");
		$this->addRightFrontSystem($tPulsar);
		$tCutter = new TelekineticCutter(6, 0, 0, 300, 120);
		$tCutter->addTag("Telekinetic Cutter");
		$this->addRightFrontSystem($tCutter);  		           
        $this->addRightFrontSystem(new MindriderThruster(6, 35, 0, 12, 4));        

            
		$this->addLeftAftSystem(new SelfRepair(6, 6, 3)); //armor, structure, output 
		$tPulsar = new TriopticPulsar(6, 0, 0, 120, 300);
		$tPulsar->addTag("Trioptic Pulsar");
		$this->addLeftAftSystem($tPulsar);
		$tCutter = new TelekineticCutter(6, 0, 0, 120, 300);
		$tCutter->addTag("Telekinetic Cutter");
		$this->addLeftAftSystem($tCutter);  		                                                                       
  			                  		                      
		$this->addRightAftSystem(new SelfRepair(6, 6, 3)); //armor, structure, output 
		$tPulsar = new TriopticPulsar(6, 0, 0, 60, 240);
		$tPulsar->addTag("Trioptic Pulsar");
		$this->addRightAftSystem($tPulsar);
		$tCutter = new TelekineticCutter(6, 0, 0, 60, 240);
		$tCutter->addTag("Telekinetic Cutter");
		$this->addRightAftSystem($tCutter);  		            
        
                      			          
		//structures
        $this->addFrontSystem(new Structure(7, 88));
        $this->addAftSystem(new Structure(7, 88));
        $this->addLeftFrontSystem(new Structure(7, 88));
        $this->addRightFrontSystem(new Structure(7, 88));
        $this->addLeftAftSystem(new Structure(7, 88));
        $this->addRightAftSystem(new Structure(7, 88));        
        $this->addPrimarySystem(new Structure(8, 130));
		
		
		$this->hitChart = array(
			0=> array( //PRIMARY
				5 => "Structure",
				8 => "Ultra Pulse Cannon",
				9 => "Second Sight",
				10 => "Thought Wave",
				11 => "Thought Shield",
				12 => "Hangar",	
				14 => "Scanner",
				16 => "Engine",
				17 => "Jump Engine",
				19 => "Reactor",
				20 => "TAG:C&C",
			),
			1=> array( //Fwd
				4 => "TAG:Thruster",
				6 => "TAG:Telekinetic Cutter", 
				8 => "TAG:Trioptic Pulsar", 
				9 => "Self Repair",
				15 => "Structure",
				20 => "Primary",
			),
			2=> array( //Aft
				4 => "TAG:Thruster",
				6 => "TAG:Telekinetic Cutter", 
				8 => "TAG:Trioptic Pulsar", 
				9 => "Self Repair",
				15 => "Structure",
				20 => "Primary",
			),
			31=> array(
				4 => "TAG:Thruster",
				6 => "TAG:Telekinetic Cutter", 
				8 => "TAG:Trioptic Pulsar", 
				9 => "Self Repair",
				15 => "Structure",
				20 => "Primary",
			),
			41=> array(
				4 => "TAG:Thruster",
				6 => "TAG:Telekinetic Cutter", 
				8 => "TAG:Trioptic Pulsar", 
				9 => "Self Repair",
				15 => "Structure",
				20 => "Primary",
			),
			32=> array(
				4 => "TAG:Thruster",
				6 => "TAG:Telekinetic Cutter", 
				8 => "TAG:Trioptic Pulsar", 
				9 => "Self Repair",
				15 => "Structure",
				20 => "Primary",
			),
			42=> array(
				4 => "TAG:Thruster",
				6 => "TAG:Telekinetic Cutter", 
				8 => "TAG:Trioptic Pulsar", 
				9 => "Self Repair",
				15 => "Structure",
				20 => "Primary",
			),
		);
		
    }
}


?>
