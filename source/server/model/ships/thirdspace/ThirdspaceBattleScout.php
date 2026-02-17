<?php
class ThirdspaceBattleScout extends BaseShip{
     
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 5500;
		$this->faction = "Thirdspace";
        $this->phpclass = "ThirdspaceBattleScout";
        $this->imagePath = "img/ships/ThirdspaceBattleship.png";
        $this->shipClass = "Oculus Battle Scout";
        $this->shipSizeClass = 3;
	    $this->isd = 'Ancient';
		$this->factionAge = 4;
		$this->unofficial = true;
		$this->canvasSize = 550;
			$this->variantOf = "Eldritch Battleship";
			$this->occurence = "rare";											    
	    
		$this->fighters = array("LCVs" => 9);	
		
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
        
        $this->addPrimarySystem(new AdvancedSingularityDrive(8, 44, 0, 66+12+4));
        $this->addPrimarySystem(new ThirdspaceCnC(8, 32, 0, 0));
        $scanner = new ElintScanner(7, 36, 12, 16);
		$scanner->markThirdspace();
		$this->addPrimarySystem($scanner);	        
        $this->addPrimarySystem(new Engine(7, 36, 0, 18, 4));
		$this->addPrimarySystem(new PsychicField(6, 0, 0, 0, 360));		
		$this->addPrimarySystem(new ThirdspaceShieldGenerator(7, 24, 0, 48, 4, 4)); //$armor, $maxhealth, $power used, output, maxBoost, boostEfficiency
		$this->addPrimarySystem(new ThirdspaceSelfRepair(7, 20, 10, 4)); //armor, structure, output, maxBoost  
		$this->addPrimarySystem(new JumpEngine(6, 24, 4, 7));//Presumably have access to hyperspace, or possess some other form of FTL travel that this system represents.          		  		
      
		$this->addPrimarySystem(new ThirdspaceShield(0, 480, 160, 210, 330, 'L'));	

        $this->addFrontSystem(new PsionicTorpedo(5, 0, 0, 240, 0));
        $this->addFrontSystem(new PsionicTorpedo(5, 0, 0, 240, 0));        
        $this->addFrontSystem(new PsionicTorpedo(5, 0, 0, 0, 120));
        $this->addFrontSystem(new PsionicTorpedo(5, 0, 0, 0, 120));        
        $this->addFrontSystem(new PsionicConcentrator(5, 0, 0, 240, 120));               	          	
        $this->addFrontSystem(new GraviticThruster(6, 20, 0, 8, 1));
        $this->addFrontSystem(new GraviticThruster(6, 20, 0, 8, 1));             
			
		$this->addPrimarySystem(new ThirdspaceShield(0, 240, 80, 330, 30, 'F'));		

        $this->addAftSystem(new PsionicTorpedo(5, 0, 0, 180, 300));
        $this->addAftSystem(new PsionicTorpedo(5, 0, 0, 60, 180));			
        $this->addAftSystem(new PsionicConcentrator(5, 0, 0, 90, 270));                      
        $this->addAftSystem(new GraviticThruster(5, 24, 0, 8, 2));
        $this->addAftSystem(new GraviticThruster(5, 24, 0, 8, 2));
        $this->addAftSystem(new GraviticThruster(5, 24, 0, 8, 2));
        $this->addAftSystem(new GraviticThruster(5, 24, 0, 8, 2)); 
        $LCVRail1 = new Catapult(3, 12, 3);
        $LCVRail1->displayName = "LCV Rail";        
        $this->addAftSystem($LCVRail1);                
       
       		
		$this->addPrimarySystem(new ThirdspaceShield(0, 240, 80, 150, 210, 'A'));
		$this->addLeftSystem(new PsionicLance(5, 0, 0, 180, 300));   
		$this->addLeftSystem(new PsionicLance(5, 0, 0, 240, 360)); 		                  
        $this->addLeftSystem(new PsionicConcentrator(5, 0, 0, 180, 300));  
        $this->addLeftSystem(new PsionicConcentrator(5, 0, 0, 240, 360));		                 
        $this->addLeftSystem(new GraviticThruster(6, 30, 0, 10, 3)); 
        $LCVRail2 = new Catapult(3, 12, 3);
        $LCVRail2->displayName = "LCV Rail";        
        $this->addLeftSystem($LCVRail2);                      
            
            	
		$this->addPrimarySystem(new ThirdspaceShield(0, 480, 160, 30, 150, 'R'));
 		$this->addRightSystem(new PsionicLance(5, 0, 0, 60, 180));
		$this->addRightSystem(new PsionicLance(5, 0, 0, 0, 120)); 		
		$this->addRightSystem(new PsionicConcentrator(5, 0, 0, 60, 180));
        $this->addRightSystem(new PsionicConcentrator(5, 0, 0, 0, 120));  		                                                             
        $this->addRightSystem(new GraviticThruster(6, 30, 0, 10, 4)); 
        $LCVRail3 = new Catapult(3, 12, 3);		
        $LCVRail3->displayName = "LCV Rail";							  	
        $this->addRightSystem($LCVRail3); 			                      
              			          
		//structures
        $this->addFrontSystem(new Structure(6, 110));
        $this->addAftSystem(new Structure(6, 120));
        $this->addLeftSystem(new Structure(6, 120));
        $this->addRightSystem(new Structure(6, 120));
        $this->addPrimarySystem(new Structure(6, 86));
		
		
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
				8 => "Psionic Torpedo",
				10 => "Psionic Concentrator",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array( //Aft
				7 => "Thruster",
				9 => "Psionic Torpedo",				
				11 => "Psionic Concentrator",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				4 => "Thruster",
				6 => "Psionic Lance",
				8 => "Psionic Concentrator",
				10 => "LCV Rail",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				4 => "Thruster",
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
