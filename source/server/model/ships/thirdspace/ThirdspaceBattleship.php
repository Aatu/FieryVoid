<?php
class ThirdspaceBattleship extends BaseShip{
     
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 7000;
		$this->faction = "Thirdspace";
        $this->phpclass = "ThirdspaceBattleship";
        $this->imagePath = "img/ships/ThirdspaceBattleship.png";
        $this->shipClass = "Eldritch Battleship";
        $this->shipSizeClass = 3;
	    $this->isd = 'Ancient';
		$this->factionAge = 4;
		$this->unofficial = true;
		$this->canvasSize = 550;							    
	    
		$this->fighters = array("LCVs" => 6);	
		
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
		$this->enhancementOptionsEnabled[] = 'IMPR_PSY'; //Ship has Psychic Field, add enhancement.
        
        $this->addPrimarySystem(new AdvancedSingularityDrive(8, 50, 0, 86+10+4));
        $this->addPrimarySystem(new ThirdspaceCnC(8, 36, 0, 0));
        $scanner = new Scanner(7, 24, 10, 15);
		$scanner->markThirdspace();
		$this->addPrimarySystem($scanner);	        
        $this->addPrimarySystem(new Engine(7, 36, 0, 20, 4));
		$this->addPrimarySystem(new PsychicField(6, 0, 0, 0, 360));
		$this->addPrimarySystem(new ThirdspaceShieldGenerator(7, 24, 0, 60, 5, 4)); //$armor, $maxhealth, $power used, output, maxBoost, boostEfficiency
        $this->addPrimarySystem(new ThirdspaceSelfRepair(7, 24, 12, 6)); //armor, structure, output, maxBoost 
		$this->addPrimarySystem(new JumpEngine(6, 24, 4, 7));//Presumably have access to hyperspace, or possess some other form of FTL travel that this system represents.          		  		
      
		$this->addPrimarySystem(new ThirdspaceShield(1, 600, 200, 210, 330, 'L'));		

        $this->addFrontSystem(new HeavyPsionicLance(6, 0, 0, 330, 30));
        $this->addFrontSystem(new PsionicLance(5, 0, 0, 300, 60));
        $this->addFrontSystem(new PsionicLance(5, 0, 0, 300, 60));
        $this->addFrontSystem(new PsionicConcentrator(5, 0, 0, 240, 60));
        $this->addFrontSystem(new PsionicConcentrator(5, 0, 0, 300, 120));               	          	
        $this->addFrontSystem(new GraviticThruster(6, 20, 0, 8, 1));
        $this->addFrontSystem(new GraviticThruster(6, 20, 0, 8, 1));           
			
		$this->addPrimarySystem(new ThirdspaceShield(1, 300, 100, 330, 30, 'F'));		

        $this->addAftSystem(new PsionicConcentrator(5, 0, 0, 120, 300)); 
        $this->addAftSystem(new PsionicConcentrator(5, 0, 0, 60, 240));                 
        $this->addAftSystem(new GraviticThruster(5, 24, 0, 8, 2));
        $this->addAftSystem(new GraviticThruster(5, 24, 0, 8, 2));
        $this->addAftSystem(new GraviticThruster(5, 24, 0, 8, 2));
        $this->addAftSystem(new GraviticThruster(5, 24, 0, 8, 2));       
       
       
       	
		$this->addPrimarySystem(new ThirdspaceShield(1, 300, 100, 150, 210, 'A'));	              
        $this->addLeftSystem(new PsionicConcentrator(5, 0, 0, 180, 300));         
        $this->addLeftSystem(new PsionicConcentrator(5, 0, 0, 240, 360));
        $this->addLeftSystem(new PsionicLance(5, 0, 0, 180, 300));
		$this->addLeftSystem(new PsionicLance(5, 0, 0, 210, 330));            
        $this->addLeftSystem(new PsionicLance(5, 0, 0, 240, 360));                                                               
        $this->addLeftSystem(new GraviticThruster(6, 30, 0, 10, 3)); 
        $LCVRail = new Catapult(3, 12, 3);
        $LCVRail->displayName = "LCV Rail";        
        $this->addLeftSystem($LCVRail);                      
            
            	
		$this->addPrimarySystem(new ThirdspaceShield(1, 600, 200, 30, 150, 'R'));
        $this->addRightSystem(new PsionicConcentrator(5, 0, 0, 60, 180));                           
        $this->addRightSystem(new PsionicConcentrator(5, 0, 0, 0, 120));
		$this->addRightSystem(new PsionicLance(5, 0, 0, 60, 180));                      
		$this->addRightSystem(new PsionicLance(5, 0, 0, 30, 150));
        $this->addRightSystem(new PsionicLance(5, 0, 0, 0, 120)); 				                  
        $this->addRightSystem(new GraviticThruster(6, 30, 0, 10, 4)); 
        $LCVRail = new Catapult(3, 12, 3);		
        $LCVRail->displayName = "LCV Rail";							  	
        $this->addRightSystem($LCVRail); 			                      
              			          
		//structures
        $this->addFrontSystem(new Structure(6, 120));
        $this->addAftSystem(new Structure(6, 136));
        $this->addLeftSystem(new Structure(6, 136));
        $this->addRightSystem(new Structure(6, 136));
        $this->addPrimarySystem(new Structure(7, 92));
		
		
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
				5 => "Thruster",
				7 => "Heavy Psionic Lance", 
				9 => "Psionic Lance",
				11 => "Psionic Concentrator",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array( //Aft
				8 => "Thruster",
				10 => "Psionic Concentrator",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				3 => "Thruster",
				6 => "Psionic Lance",
				8 => "Psionic Concentrator",
				10 => "LCV Rail",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				3 => "Thruster",
				6 => "Psionic Lance",
				8 => "Psionic Concentrator",
				10 => "LCV Rail",
				18 => "Structure",
				20 => "Primary",
			),

		);
		
    }
}


?>
