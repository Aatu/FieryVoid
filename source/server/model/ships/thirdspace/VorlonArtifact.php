<?php
class VorlonArtifact extends UnevenBaseFourSections{
     
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 1000;
		$this->faction = "Thirdspace";
        $this->phpclass = "VorlonArtifact";
        $this->imagePath = "img/ships/ThirdspaceArtifact.png";
        $this->shipClass = "Vorlon Artifact";
        $this->shipSizeClass = 3;
	    $this->isd = 'Ancient';
		$this->factionAge = 4;
		$this->unofficial = true;
		$this->canvasSize = 512;							    
		
        $this->gravitic = true;
		$this->advancedArmor = true; 		    
		$this->iniativebonus = -200; //no voluntary movement anyway

        $this->forwardDefense = 20;
        $this->sideDefense = 18;
        
		$this->turncost = 0;
		$this->turndelaycost = 0;  
		$this->nonRotating = true; //some bases do not rotate - this attribute is used in combination with $base or $smallBase
		$this->Enormous = true;				
		
		/*Thirdspace use their own enhancement set */		
		Enhancements::nonstandardEnhancementSet($this, 'ThirdspaceShip');				     
		$this->enhancementOptionsEnabled[] = 'IMPR_PSY'; //Ship has Psychic Field, add enhancement.

        $this->addPrimarySystem(new MagGravReactorTechnical(99, 99, 0, 0));        
		$this->addPrimarySystem(new PowerCapacitor( 7, 40, 0, 10, false));//armor, structure, power req, output, has petals 
        $this->addPrimarySystem(new CnC(8, 36, 0, 0));
        $scanner = new Scanner(7, 20, 10, 3);
		$scanner->markAdvanced();
		$this->addPrimarySystem($scanner);	        
		$this->addPrimarySystem(new PsychicField(6, 0, 0, 0, 360));
		$this->addPrimarySystem(new ThirdspaceShieldGenerator(7, 48, 0, 80, 5, 4)); //$armor, $maxhealth, $power used, output, maxBoost, boostEfficiency
        $this->addPrimarySystem(new SelfRepair(7, 16, 8)); //armor, structure, output
		$AAC = $this->createAdaptiveArmorController(8, 3, 3); //$AAtotal, $AApertype, $AApreallocated
		$this->addPrimarySystem( $AAC );      		  		
      
		$this->addPrimarySystem(new ThirdspaceShield(2, 1500, 500, 210, 330, 'L'));		

		$this->addPrimarySystem(new ThirdspaceShield(2, 1500, 250, 330, 30, 'F'));		
       	
		$this->addPrimarySystem(new ThirdspaceShield(2, 1500, 250, 150, 210, 'A'));                        
            	
		$this->addPrimarySystem(new ThirdspaceShield(2, 1500, 500, 30, 150, 'R'));					                
	                      
              			          
		//structures
        $this->addFrontSystem(new Structure(6, 180));
        $this->addAftSystem(new Structure(6, 180));
        $this->addLeftSystem(new Structure(6, 180));
        $this->addRightSystem(new Structure(6, 180));
        $this->addPrimarySystem(new Structure(7, 120));
		
		
		$this->hitChart = array(
			0=> array( //PRIMARY
				10 => "Structure",
				12 => "Psychic Field",
				14 => "Shield Generator",
				15 => "Self Repair",
				17 => "Scanner",
				19 => "Power Capacitor",
				20 => "C&C",
			),
			1=> array( //Fwd
				18 => "Structure",
				20 => "Primary",
			),
			2=> array( //Aft
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				18 => "Structure",
				20 => "Primary",
			),

		);
		
    }
}


?>
