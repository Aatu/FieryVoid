<?php
class CraytanRollun extends LCV{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 175;
        $this->faction = "Nexus Craytan Union";
        $this->phpclass = "CraytanRollun";
        $this->imagePath = "img/ships/Nexus/craytan_calen.png";
			$this->canvasSize = 70; //img has 200px per side
        $this->shipClass = "Rollun Escort";
			$this->variantOf = "Calen Gunboat";
			$this->occurence = "common";
		$this->unofficial = true;
		$this->isd = 2139;

        $this->hangarRequired = ''; //Nexus LCVs are more independent than their B5 counterparts
	    $this->notes = 'May deploy independently.';
        
        $this->forwardDefense = 10;
        $this->sideDefense = 11;
        
		$this->agile = true;
        $this->turncost = 0.25;
        $this->turndelaycost = 0.33;
        $this->accelcost = 1;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus = 14*5;
    
		$this->addFrontSystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance
  
		$this->addPrimarySystem(new Reactor(4, 9, 0, 0));
		$this->addPrimarySystem(new CnC(99, 1, 0, 0)); //C&C should be unhittable anyway
//        $this->addPrimarySystem(new AntiquatedScanner(3, 12, 2, 4));
    	$sensors = new Scanner(4, 10, 4, 5);
			$sensors->markLCV();
			$this->addPrimarySystem($sensors);
		$this->addPrimarySystem(new Engine(4, 10, 0, 6, 2));

		$this->addFrontSystem(new NexusCIDS(3, 4, 2, 180, 60));
		$this->addFrontSystem(new NexusACIDS(3, 6, 2, 270, 90));
		$this->addFrontSystem(new NexusCIDS(3, 4, 2, 300, 180));
	    
        $this->addPrimarySystem(new Structure(4, 30));
	    
        $this->hitChart = array(
        		0=> array( 
        				10 => "Structure",
        				12 => "1:Advanced Close-In Defense System",
        				15 => "1:Close-In Defense System",
						17 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		1=> array( //redirect to PRIMARY
        				10 => "Structure",
        				12 => "1:Advanced Close-In Defense System",
        				15 => "1:Close-In Defense System",
						17 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		2=> array( //redirect to PRIMARY
        				10 => "Structure",
        				12 => "1:Advanced Close-In Defense System",
        				15 => "1:Close-In Defense System",
						17 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),        		
        ); //end of hit chart
    }
}
?>
