<?php
class CraytanHelisRefit extends LCV{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 180;
        $this->faction = "ZNexus Playtest Craytan";
        $this->phpclass = "CraytanHelisRefit";
        $this->imagePath = "img/ships/Nexus/CraytanHelis.png";
			$this->canvasSize = 55; //img has 200px per side
        $this->shipClass = "Helis Interdictor (2097 refit)";
			$this->variantOf = "Helis Interdictor";
			$this->occurence = "common";
		$this->unofficial = true;
		$this->isd = 2097;

        $this->hangarRequired = ''; //Nexus LCVs are more independent than their B5 counterparts
	    $this->notes = 'May deploy independently.';
        
        $this->forwardDefense = 9;
        $this->sideDefense = 11;
        
		$this->agile = true;
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 1;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus = 14*5;
    
		$this->addFrontSystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance
  
		$this->addPrimarySystem(new Reactor(3, 7, 0, 0));
		$this->addPrimarySystem(new CnC(99, 99, 0, 0)); //C&C should be unhittable anyway
//        $this->addPrimarySystem(new AntiquatedScanner(3, 12, 2, 4));
    	$sensors = new Scanner(3, 12, 2, 4);
			$sensors->markLCV();
			$this->addPrimarySystem($sensors);
		$this->addPrimarySystem(new Engine(3, 12, 0, 6, 2));

		$this->addFrontSystem(new NexusCIDS(2, 4, 2, 240, 60));
		$this->addFrontSystem(new MediumPlasma(2, 5, 3, 270, 90));
		$this->addFrontSystem(new NexusCIDS(2, 4, 2, 300, 120));
	    
        $this->addPrimarySystem(new Structure(3, 32));
	    
        $this->hitChart = array(
        		0=> array( 
        				11 => "Structure",
        				13 => "1:Medium Plasma Cannon",
        				16 => "1:Close-In Defense System",
						18 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		1=> array( //redirect to PRIMARY
        				11 => "Structure",
        				13 => "1:Medium Plasma Cannon",
        				16 => "1:Close-In Defense System",
						18 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		2=> array( //redirect to PRIMARY
        				11 => "Structure",
        				13 => "1:Medium Plasma Cannon",
        				16 => "1:Close-In Defense System",
						18 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),        		
        ); //end of hit chart
    }
}
?>