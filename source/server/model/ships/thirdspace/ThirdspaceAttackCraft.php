<?php
class ThirdspaceAttackCraft extends LCV{ //Actually an LCV.

	/*no EW restrictions*/
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 500;
    $this->faction = "Thirdspace";
	$this->factionAge = 4; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
	$this->phpclass = "ThirdspaceAttackCraft";
	$this->shipClass = "Attack Craft";
	$this->imagePath = "img/ships/ThirdspaceAttackCraft.png";
	$this->canvasSize = 80;

	$this->unofficial = true;
    $this->gravitic = true;
	$this->advancedArmor = true; 
	  
	$this->agile = true;
	$this->forwardDefense = 10;
	$this->sideDefense = 12;
	$this->isd = 'Ancient';
	    
	$this->turncost = 0.25;
	$this->turndelaycost = 0.25;
	$this->accelcost = 1;
	$this->rollcost = 1;
	$this->pivotcost = 1;
	$this->iniativebonus = 15 *5;
		
	$this->hangarRequired = "LCVs";
	
	/*Thirdspace use their own enhancement set */		
	Enhancements::nonstandardEnhancementSet($this, 'ThirdspaceShip');		
	    
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhittable and with unlimited thrust allowance   
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhittable and with unlimited thrust allowance
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhittable and with unlimited thrust allowance
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhittable and with unlimited thrust allowance

	$this->addPrimarySystem(new ThirdspaceCnC(99, 1, 0, 0)); //C&C should be unhittable anyway	    
    $this->addPrimarySystem(new AdvancedSingularityDrive(5, 15, 0, 8+4+3));
	$sensors = new Scanner(5, 12, 4, 9);
		$sensors->markThirdspace();
		$this->addPrimarySystem($sensors);
	$this->addPrimarySystem(new JumpEngine(5, 6, 3, 8));//Added a small jump drive, how they travel is unknown but if White Star can house a jump drive it's not unfeasible Thirdspace aliens would have a FTL drive on their smaller craft.
	$this->addPrimarySystem(new Engine(5, 12, 0, 10, 2));
	$this->addPrimarySystem(new ThirdspaceShieldGenerator(5, 6, 0, 10, 4, 2)); //$armor, $maxhealth, $power used, output, maxBoost, boostEfficiency	
    $this->addPrimarySystem(new ThirdspaceSelfRepair(5, 6, 3, 3)); //armor, structure, output, maxBoost  	

	$this->addFrontSystem(new PsionicConcentrator(5, 0, 0, 240, 120));
//	$this->addFrontSystem(new PsionicConcentrator(5, 0, 0, 210, 60));
//	$this->addFrontSystem(new PsionicConcentrator(5, 0, 0, 210, 60));
//	$this->addFrontSystem(new PsionicConcentrator(5, 0, 0, 300, 150));
//	$this->addFrontSystem(new PsionicConcentrator(5, 0, 0, 300, 150));
			
	$this->addPrimarySystem(new ThirdspaceShield(0, 60, 60, 0, 360, 'C'));
				
	$this->addPrimarySystem(new Structure( 5, 36));
	    
        $this->hitChart = array(
        		0=> array( //should never happen (but it will!)
        				9 => "Structure",
        				11 => "1:Psionic Concentrator",
						12 => "Shield Generator",   
						13 => "Self Repair",        				
        				14 => "Jump Engine",
        				16 => "Engine",
        				18 => "Reactor",
        				20 => "Scanner",
        		),
        		1=> array( //PRIMARY hit table, effectively
        				9 => "Structure",
        				11 => "Psionic Concentrator",
						12 => "0:Shield Generator",          				
						13 => "0:Self Repair",        				
        				14 => "0:Jump Engine",
        				16 => "0:Engine",
        				18 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		2=> array( //PRIMARY hit table, effectively
        				9 => "Structure",
        				11 => "1:Psionic Concentrator",
						12 => "0:Shield Generator",          				
						13 => "0:Self Repair",        				
        				14 => "0:Jump Engine",
        				16 => "0:Engine",
        				18 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		
        ); //end of hit chart
    }
	
}
?>
