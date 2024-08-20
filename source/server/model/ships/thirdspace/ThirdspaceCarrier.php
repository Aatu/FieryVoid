<?php
class ThirdspaceCarrier extends BaseShip{
     
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 4000;
		$this->faction = "Thirdspace";
        $this->phpclass = "ThirdspaceCarrier";
        $this->imagePath = "img/ships/ThirdspaceBattleship.png";
        $this->shipClass = "Harbinger Battle Carrier";
        $this->shipSizeClass = 3;
	    $this->isd = 'Ancient';
		$this->factionAge = 4;
		$this->unofficial = true;
		$this->canvasSize = 650;
			$this->variantOf = "Eldritch Battleship";
			$this->occurence = "uncommon";											    
	    
		$this->fighters = array("LCVs" => 12);	
		
        $this->gravitic = true;
		$this->advancedArmor = true; 		    
		
        $this->forwardDefense = 18;
        $this->sideDefense = 20;
        
        $this->turncost = 1.5;
        $this->turndelaycost = 1;
        $this->accelcost = 6;
        $this->rollcost = 6;
        $this->pivotcost = 4;
       
		$this->iniativebonus = 2 *5; 
		
		/*Thirdspace use their own enhancement set */		
		Enhancements::nonstandardEnhancementSet($this, 'ThirdspaceShip');			       
        
        $this->addPrimarySystem(new AdvancedSingularityDrive(8, 40, 0, 40+10+4));
        $this->addPrimarySystem(new ThirdspaceCnC(8, 32, 0, 0));
        $scanner = new Scanner(7, 28, 10, 14);
		$scanner->markThirdspace();
		$this->addPrimarySystem($scanner);	        
        $this->addPrimarySystem(new Engine(7, 30, 0, 18, 4));
		$this->addPrimarySystem(new PsychicField(6, 0, 0, 0, 360));	
		$this->addPrimarySystem(new ThirdspaceShieldGenerator(6, 20, 0, 10)); //$armor, $maxhealth, $power used, output				
        $this->addPrimarySystem(new ThirdspaceSelfRepair(7, 16, 8)); //armor, structure, output 
		$this->addPrimarySystem(new JumpEngine(6, 24, 4, 7));//Presumably have access to hyperspace, or possess some other form of FTL travel that this system represents.          		  		
      
		$this->addPrimarySystem(new ThirdspaceShield(2, 280, 140, 210, 330, 'L'));		

        $this->addFrontSystem(new PsionicConcentrator(4, 0, 0, 240, 60));
        $this->addFrontSystem(new PsionicConcentrator(4, 0, 0, 240, 60));
        $this->addFrontSystem(new PsionicConcentrator(4, 0, 0, 300, 120));
        $this->addFrontSystem(new PsionicConcentrator(4, 0, 0, 240, 60));        
        $this->addFrontSystem(new PsionicConcentrator(4, 0, 0, 300, 120)); 
        $this->addFrontSystem(new PsionicConcentrator(4, 0, 0, 300, 120));                         	          	
        $this->addFrontSystem(new GraviticThruster(6, 20, 0, 8, 1));
        $this->addFrontSystem(new GraviticThruster(6, 20, 0, 8, 1));           
			
		$this->addPrimarySystem(new ThirdspaceShield(2, 140, 70, 330, 30, 'F'));		
	
        $this->addAftSystem(new PsionicConcentrator(4, 0, 0, 120, 300)); 
        $this->addAftSystem(new PsionicConcentrator(4, 0, 0, 120, 300));
        $this->addAftSystem(new PsionicConcentrator(4, 0, 0, 60, 240));         
        $this->addAftSystem(new PsionicConcentrator(4, 0, 0, 120, 300)); 
        $this->addAftSystem(new PsionicConcentrator(4, 0, 0, 60, 240));        
        $this->addAftSystem(new PsionicConcentrator(4, 0, 0, 60, 240));                         
        $this->addAftSystem(new GraviticThruster(5, 24, 0, 8, 2));
        $this->addAftSystem(new GraviticThruster(5, 24, 0, 8, 2));
        $this->addAftSystem(new GraviticThruster(5, 24, 0, 8, 2));
        $this->addAftSystem(new GraviticThruster(5, 24, 0, 8, 2));       
       
       	
		$this->addPrimarySystem(new ThirdspaceShield(2, 140, 70, 150, 210, 'A'));
		$this->addLeftSystem(new PsionicLance(5, 0, 0, 240, 360));
		$this->addLeftSystem(new PsionicLance(5, 0, 0, 180, 300));   		                  
        $this->addLeftSystem(new PsionicConcentrator(4, 0, 0, 180, 300));
        $this->addLeftSystem(new PsionicConcentrator(4, 0, 0, 180, 300));
        $this->addLeftSystem(new PsionicConcentrator(4, 0, 0, 180, 300));          
        $this->addLeftSystem(new PsionicConcentrator(4, 0, 0, 240, 360));              
        $this->addLeftSystem(new PsionicConcentrator(4, 0, 0, 240, 360)); 
        $this->addLeftSystem(new PsionicConcentrator(4, 0, 0, 240, 360));  
        $this->addLeftSystem(new GraviticThruster(6, 30, 0, 10, 3)); 
        $LCVRail = new Catapult(3, 12, 3);
        $LCVRail->displayName = "LCV Rail";        
        $this->addLeftSystem($LCVRail); 
        $LCVRail = new Catapult(3, 12, 3);
        $LCVRail->displayName = "LCV Rail";        
        $this->addLeftSystem($LCVRail);                              
            
            	
		$this->addPrimarySystem(new ThirdspaceShield(2, 280, 140, 30, 150, 'R'));
 		$this->addRightSystem(new PsionicLance(5, 0, 0, 0, 120));
 		$this->addRightSystem(new PsionicLance(5, 0, 0, 60, 180)); 		
        $this->addRightSystem(new PsionicConcentrator(4, 0, 0, 60, 180));                  
        $this->addRightSystem(new PsionicConcentrator(4, 0, 0, 60, 180)); 
        $this->addRightSystem(new PsionicConcentrator(4, 0, 0, 60, 180));          
        $this->addRightSystem(new PsionicConcentrator(4, 0, 0, 0, 120));
        $this->addRightSystem(new PsionicConcentrator(4, 0, 0, 0, 120));
        $this->addRightSystem(new PsionicConcentrator(4, 0, 0, 0, 120));                                      
        $this->addRightSystem(new GraviticThruster(6, 30, 0, 10, 4)); 
        $LCVRail = new Catapult(3, 12, 3);		
        $LCVRail->displayName = "LCV Rail";							  	
        $this->addRightSystem($LCVRail);
        $LCVRail = new Catapult(3, 12, 3);		
        $LCVRail->displayName = "LCV Rail";							  	
        $this->addRightSystem($LCVRail);          			                      
              			          
		//structures
        $this->addFrontSystem(new Structure(5, 92));
        $this->addAftSystem(new Structure(5, 108));
        $this->addLeftSystem(new Structure(5, 112));
        $this->addRightSystem(new Structure(5, 112));
        $this->addPrimarySystem(new Structure(5, 86));
		
		
		$this->hitChart = array(
			0=> array( //PRIMARY
				9 => "Structure",
				10 => "Shield Generator",				
				11 => "Psychic Field",
				12 => "Self Repair",
				14 => "Scanner",
				16 => "Engine",
				17 => "Jump Engine",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array( //Fwd
				6 => "Thruster",
				9 => "Psionic Concentrator",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array( //Aft
				7 => "Thruster",
				10 => "Psionic Concentrator",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				4 => "Thruster",
				7 => "Psionic Concentrator",
				11 => "LCV Rail",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				4 => "Thruster",
				7 => "Psionic Concentrator",
				11 => "LCV Rail",
				18 => "Structure",
				20 => "Primary",
			),

		);
		
    }
}


?>
