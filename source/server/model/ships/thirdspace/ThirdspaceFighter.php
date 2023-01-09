<?php
class ThirdspaceFighter extends LCV{
	/*Drakh Heavy Raider LCV*/
	/*no EW restrictions*/
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 600;
        $this->faction = "Thirdspace";
	$this->factionAge = 4; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
	$this->phpclass = "ThirdspaceFighter";
	$this->shipClass = "Thirdspace Fighter";
	$this->imagePath = "img/ships/ThirdspaceLCV.png";
	$this->canvasSize = 100;

	$this->unofficial = true;
    $this->gravitic = true;
	$this->advancedArmor = true; 
	  
	$this->agile = true;
	$this->forwardDefense = 11;
	$this->sideDefense = 13;
	$this->isd = 'Ancient';
	    
	$this->turncost = 0.25;
	$this->turndelaycost = 0.25;
	$this->accelcost = 2;
	$this->rollcost = 1;
	$this->pivotcost = 1;
	$this->iniativebonus = 14 *5;
		
	$this->hangarRequired = "LCVs";
	    
	$this->addFrontSystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance   
	$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
	$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance
	$this->addPrimarySystem(new ThirdspaceCnC(99, 99, 0, 0)); //C&C should be unhittable anyway
		    
    $this->addPrimarySystem(new AdvancedSingularityDrive(6, 15, 0, 20+4+5));
	$sensors = new Scanner(6, 12, 4, 10);
		$sensors->markAdvanced();
		$this->addPrimarySystem($sensors);
	$this->addPrimarySystem(new JumpEngine(5, 10, 5, 8));//Added a small jump drive, how they travel is unknown but if White Star can house a jump drive it's not unfeasible Thirdspace aliens would have  FTL drive on their smaller craft.
	$this->addPrimarySystem(new Engine(6, 18, 0, 14, 2));
    $this->addPrimarySystem(new SelfRepair(5, 10, 2)); //armor, structure, output 	

	$this->addFrontSystem(new PsionicConcentrator(4, 0, 0, 300, 180));
	$this->addFrontSystem(new PsionicConcentrator(4, 0, 0, 300, 180));
	$this->addFrontSystem(new PsionicConcentrator(4, 0, 0, 180, 60));
	$this->addFrontSystem(new PsionicConcentrator(4, 0, 0, 180, 60));			

		$projection = new ThirdspaceShieldProjection(0, 50, 50, 0, 360, 'F');//: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R
		$projector = new ThirdspaceShieldProjector(5, 10, 4, 4, 0, 360, 'F'); //: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R 
		$projection->addProjector($projector);
		$this->addFrontSystem($projector);
		$this->addFrontSystem($projection);
				
	$this->addPrimarySystem(new Structure( 6, 30));
	    
        $this->hitChart = array(
        		0=> array( //should never happen (but it will!)
        				7 => "Structure",
        				10 => "1:Psionic Concentrator",
        				12 => "1:Shield Projector",
						13 => "Self Repair",        				
        				14 => "Jump Engine",
        				16 => "Engine",
        				18 => "Reactor",
        				20 => "Scanner",
        		),
        		1=> array( //PRIMARY hit table, effectively
        				7 => "Structure",
        				10 => "1:Psionic Concentrator",
        				12 => "1:Shield Projector",
						13 => "0:Self Repair",        				
        				14 => "0:Jump Engine",
        				16 => "0:Engine",
        				18 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		2=> array( //PRIMARY hit table, effectively
        				7 => "Structure",
        				10 => "1:Psionic Concentrator",
        				12 => "1:Shield Projector",
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
