<?php
class ThirdspaceBattleScout extends BaseShip{
     
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 6400;
		$this->faction = "Thirdspace";
        $this->phpclass = "ThirdspaceBattleScout";
        $this->imagePath = "img/ships/ThirdspaceBattleship.png";
        $this->shipClass = "Oculus Battle Scout";
        $this->shipSizeClass = 3;
	    $this->isd = 'Ancient';
		$this->factionAge = 4;
		$this->unofficial = true;
		$this->canvasSize = 650;
			$this->variantOf = "Eldritch Battleship";
			$this->occurence = "rare";											    
	    
		$this->fighters = array("LCVs" => 8);	
		
        $this->gravitic = true;
		$this->advancedArmor = true; 		    
		
        $this->forwardDefense = 18;
        $this->sideDefense = 20;
        
        $this->turncost = 1.5;
        $this->turndelaycost = 1.5;
        $this->accelcost = 6;
        $this->rollcost = 6;
        $this->pivotcost = 4;
       
		$this->iniativebonus = 2 *5;    
		
		/*Thirdspace use their own enhancement set */		
		Enhancements::nonstandardEnhancementSet($this, 'ThirdspaceShip');			    
        
        $this->addPrimarySystem(new AdvancedSingularityDrive(8, 50, 0, 140+12+4));
        $this->addPrimarySystem(new ThirdspaceCnC(8, 36, 0, 0));
        $scanner = new ElintScanner(7, 36, 12, 16);
		$scanner->markThirdspace();
		$this->addPrimarySystem($scanner);	        
        $this->addPrimarySystem(new Engine(7, 36, 0, 20, 4));
		$this->addPrimarySystem(new PsychicField(6, 0, 0, 0, 360));		
        $this->addPrimarySystem(new ThirdspaceSelfRepair(7, 24, 10)); //armor, structure, output 
		$this->addPrimarySystem(new JumpEngine(6, 24, 4, 5));//Presumably have access to hyperspace, or possess some other form of FTL travel that this system represents.          		  		
      
		$projection = new ThirdspaceShieldProjection(3, 140, 140, 330, 30, 'F');//: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R
		$projector = new ThirdspaceShieldProjector(6, 24, 4, 3, 330, 30, 'F'); //: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R 
		$projection->addProjector($projector);
		$this->addFrontSystem($projector);		
		$this->addFrontSystem($projection);
        $this->addFrontSystem(new PsionicConcentrator(4, 0, 0, 240, 60));
        $this->addFrontSystem(new PsionicConcentrator(4, 0, 0, 240, 60));
        $this->addFrontSystem(new PsionicConcentrator(4, 0, 0, 300, 120));
        $this->addFrontSystem(new PsionicConcentrator(4, 0, 0, 300, 120));        
        $this->addFrontSystem(new PsionicConcentrator(4, 0, 0, 240, 60)); 
        $this->addFrontSystem(new PsionicConcentrator(4, 0, 0, 240, 60)); 
        $this->addFrontSystem(new PsionicConcentrator(4, 0, 0, 300, 120));        
        $this->addFrontSystem(new PsionicConcentrator(4, 0, 0, 300, 120));                           	          	
        $this->addFrontSystem(new GraviticThruster(6, 20, 0, 8, 1));
        $this->addFrontSystem(new GraviticThruster(6, 20, 0, 8, 1));           
			
		
		$projection = new ThirdspaceShieldProjection(3, 110, 110, 0, 360, 'C');//: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R
		$projector = new ThirdspaceShieldProjector(6, 24, 4, 3, 0, 360, 'C'); //: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R 
		$projection->addProjector($projector);
		$this->addAftSystem($projector);
		$this->addAftSystem($projection);	
        $this->addAftSystem(new PsionicConcentrator(4, 0, 0, 120, 300)); 
        $this->addAftSystem(new PsionicConcentrator(4, 0, 0, 120, 300));
        $this->addAftSystem(new PsionicConcentrator(4, 0, 0, 60, 240));         
        $this->addAftSystem(new PsionicConcentrator(4, 0, 0, 60, 240)); 
        $this->addAftSystem(new PsionicConcentrator(4, 0, 0, 120, 300)); 
        $this->addAftSystem(new PsionicConcentrator(4, 0, 0, 120, 300));
        $this->addAftSystem(new PsionicConcentrator(4, 0, 0, 60, 240));         
        $this->addAftSystem(new PsionicConcentrator(4, 0, 0, 60, 240));                        
        $this->addAftSystem(new GraviticThruster(5, 24, 0, 8, 2));
        $this->addAftSystem(new GraviticThruster(5, 24, 0, 8, 2));
        $this->addAftSystem(new GraviticThruster(5, 24, 0, 8, 2));
        $this->addAftSystem(new GraviticThruster(5, 24, 0, 8, 2));       
       
       
        $projection = new ThirdspaceShieldProjection(3, 140, 140, 210, 330, 'L');//: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R
		$projector = new ThirdspaceShieldProjector(6, 24, 4, 3, 210, 330, 'L'); //: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R 
		$projection->addProjector($projector);
		$this->addLeftSystem($projector);		
		$this->addLeftSystem($projection);

		$this->addLeftSystem(new PsionicTorpedo(5, 0, 0, 180, 300));
		$this->addLeftSystem(new PsionicTorpedo(5, 0, 0, 240, 360));   		            
        $this->addLeftSystem(new PsionicTorpedo(5, 0, 0, 240, 360));
        $this->addLeftSystem(new PsionicConcentrator(4, 0, 0, 180, 300));
        $this->addLeftSystem(new PsionicConcentrator(4, 0, 0, 180, 300));
        $this->addLeftSystem(new PsionicConcentrator(4, 0, 0, 180, 300));          
        $this->addLeftSystem(new PsionicConcentrator(4, 0, 0, 240, 360));              
        $this->addLeftSystem(new PsionicConcentrator(4, 0, 0, 240, 360)); 
        $this->addLeftSystem(new PsionicConcentrator(4, 0, 0, 240, 360));
        $this->addLeftSystem(new PsionicTorpedo(5, 0, 0, 240, 60));                                                       
        $this->addLeftSystem(new GraviticThruster(6, 30, 0, 10, 3)); 
        $LCVRail = new Catapult(3, 16, 3);
        $LCVRail->displayName = "LCV Rail";        
        $this->addLeftSystem($LCVRail);                      
            
            
        $projection = new ThirdspaceShieldProjection(3, 140, 140, 30, 150, 'R');//: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R
		$projector = new ThirdspaceShieldProjector(6, 24, 4, 3, 30, 150, 'R'); //: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R 
		$projection->addProjector($projector);
		$this->addRightSystem($projector);		
		$this->addRightSystem($projection);
 		$this->addRightSystem(new PsionicTorpedo(5, 0, 0, 60, 180));
 		$this->addRightSystem(new PsionicTorpedo(5, 0, 0, 0, 120)); 		
        $this->addRightSystem(new PsionicTorpedo(5, 0, 0, 0, 120));
        $this->addRightSystem(new PsionicConcentrator(4, 0, 0, 60, 180));                  
        $this->addRightSystem(new PsionicConcentrator(4, 0, 0, 60, 180)); 
        $this->addRightSystem(new PsionicConcentrator(4, 0, 0, 60, 180));          
        $this->addRightSystem(new PsionicConcentrator(4, 0, 0, 0, 120));
        $this->addRightSystem(new PsionicConcentrator(4, 0, 0, 0, 120));
        $this->addRightSystem(new PsionicConcentrator(4, 0, 0, 0, 120));
                        
		$this->addRightSystem(new PsionicTorpedo(5, 0, 0, 300, 120));                  
        $this->addRightSystem(new GraviticThruster(6, 30, 0, 10, 4)); 
        $LCVRail = new Catapult(3, 16, 3);		
        $LCVRail->displayName = "LCV Rail";							  	
        $this->addRightSystem($LCVRail); 			                      
              			          
		//structures
        $this->addFrontSystem(new Structure(6, 92));
        $this->addAftSystem(new Structure(6, 108));
        $this->addLeftSystem(new Structure(6, 112));
        $this->addRightSystem(new Structure(6, 112));
        $this->addPrimarySystem(new Structure(6, 86));
		
		
		$this->hitChart = array(
			0=> array( //PRIMARY
				9 => "Structure",
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
				7 => "Shield Projector",
				9 => "Psionic Torpedo",
				12 => "Psionic Concentrator",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array( //Aft
				7 => "Thruster",
				9 => "Shield Projector",
				12 => "Psionic Concentrator",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				3 => "Thruster",
				5 => "Psionic Lance",
				7 => "Psionic Torpedo",
				9 => "Psionic Concentrator",
				11 => "Shield Projector",
				12 => "LCV Rail",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				3 => "Thruster",
				5 => "Psionic Lance",
				7 => "Psionic Torpedo",
				9 => "Psionic Concentrator",
				11 => "Shield Projector",
				12 => "LCV Rail",
				18 => "Structure",
				20 => "Primary",
			),

		);
		
    }
}


?>
