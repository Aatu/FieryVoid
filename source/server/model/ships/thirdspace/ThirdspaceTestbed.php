<?php
class ThirdspaceTestbed extends BaseShip{
     
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 6700;
//		$this->faction = "Thirdspace";
        $this->phpclass = "ThirdspaceTestbed";
        $this->imagePath = "img/ships/ThirdspaceBattleship.png";
        $this->shipClass = "Thirdspace Testbed";
        $this->shipSizeClass = 3;
	    $this->isd = 'Ancient';
		$this->factionAge = 4;
		$this->unofficial = true;
		$this->canvasSize = 350;							    
	    
		$this->fighters = array("LCVs" => 6);	
		
        $this->gravitic = true;
		$this->advancedArmor = true; 		    
		
        $this->forwardDefense = 19;
        $this->sideDefense = 20;
        
        $this->turncost = 1.5;
        $this->turndelaycost = 1.5;
        $this->accelcost = 6;
        $this->rollcost = 6;
        $this->pivotcost = 4;
       
		$this->iniativebonus = 2 *5;      
		
		/*Thirdspace use their own enhancement set */		
		Enhancements::nonstandardEnhancementSet($this, 'ThirdspaceShip');			  
        
        $this->addPrimarySystem(new AdvancedSingularityDrive(8, 50, 0, 180+8+4));
        $this->addPrimarySystem(new ThirdspaceCnC(8, 36, 0, 0));
        $scanner = new Scanner(7, 24, 8, 15);
		$scanner->markThirdspace();
		$this->addPrimarySystem($scanner);	        
        $this->addPrimarySystem(new Engine(7, 36, 0, 20, 3));
		$this->addPrimarySystem(new PsychicField(6, 0, 0, 0, 360));		
        $this->addPrimarySystem(new ThirdspaceSelfRepair(7, 24, 10)); //armor, structure, output 
		$this->addPrimarySystem(new JumpEngine(6, 24, 4, 5));//Presumably have access to hyperspace, or possess some other form of FTL travel that this system represents.          		  		
                  	          	
        $this->addFrontSystem(new GraviticThruster(6, 20, 0, 8, 1));
        $this->addFrontSystem(new GraviticThruster(6, 20, 0, 8, 1));           
			
		
              
        $this->addAftSystem(new GraviticThruster(5, 24, 0, 8, 2));
        $this->addAftSystem(new GraviticThruster(5, 24, 0, 8, 2));
        $this->addAftSystem(new GraviticThruster(5, 24, 0, 8, 2));
        $this->addAftSystem(new GraviticThruster(5, 24, 0, 8, 2));       
       
       
                                                    
        $this->addLeftSystem(new GraviticThruster(6, 30, 0, 10, 3)); 
                   
            
                
        $this->addRightSystem(new GraviticThruster(6, 30, 0, 10, 4)); 
			                      
              			          
		//structures
        $this->addFrontSystem(new Structure(6, 96));
        $this->addAftSystem(new Structure(6, 110));
        $this->addLeftSystem(new Structure(6, 120));
        $this->addRightSystem(new Structure(6, 120));
        $this->addPrimarySystem(new Structure(7, 88));
		
		
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
				4 => "Thruster",
				6 => "Shield Projector",
				8 => "Heavy Psionic Lance", 
				10 => "Psionic Lance",
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