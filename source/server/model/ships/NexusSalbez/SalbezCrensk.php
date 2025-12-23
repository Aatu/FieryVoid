<?php
class SalbezCrensk extends LCV{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 200;
        $this->faction = "Nexus Sal-bez Coalition";
        $this->phpclass = "SalbezCrensk";
        $this->imagePath = "img/ships/Nexus/salbez_crensk3.png";
			$this->canvasSize = 70; //img has 200px per side
        $this->shipClass = "Cre-nsk Combat Cutter";
		$this->unofficial = true;
			$this->isd = 2127;

        $this->hangarRequired = ''; //Nexus LCVs are more independent than their B5 counterparts
	    $this->notes = 'May deploy independently.';
        
        $this->forwardDefense = 8;
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
  
		$this->addPrimarySystem(new Reactor(4, 10, 0, 0));
		$this->addPrimarySystem(new CnC(99, 1, 0, 0)); //C&C should be unhittable anyway
//        $this->addPrimarySystem(new AntiquatedScanner(4, 12, 4, 4));
    	$sensors = new Scanner(4, 12, 4, 4);
			$sensors->markLCV();
			$this->addPrimarySystem($sensors);
		$this->addPrimarySystem(new Engine(4, 12, 0, 8, 3));

		$this->addFrontSystem(new NexusImprovedParticleBeam(2, 3, 1, 150, 30));
		$this->addFrontSystem(new MediumLaser(3, 6, 5, 300, 60));
		$this->addFrontSystem(new NexusImprovedParticleBeam(2, 3, 1, 330, 210));
    
        $this->addPrimarySystem(new Structure(4, 36));
	    
        $this->hitChart = array(
        		0=> array( 
        				11 => "Structure",
        				13 => "1:Medium Laser",
        				15 => "1:Improved Particle Beam",
						17 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		1=> array( //redirect to PRIMARY
        				11 => "Structure",
        				13 => "1:Medium Laser",
        				15 => "1:Improved Particle Beam",
						17 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		2=> array( //redirect to PRIMARY
        				11 => "Structure",
        				13 => "1:Medium Laser",
        				15 => "1:Improved Particle Beam",
						17 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),        		
        ); //end of hit chart
    }
}
?>
