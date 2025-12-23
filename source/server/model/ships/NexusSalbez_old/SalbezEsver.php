<?php
class SalbezEsver extends LCV{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 175;
        $this->faction = "Nexus Sal-bez Coalition (early)";
        $this->phpclass = "SalbezEsver";
        $this->imagePath = "img/ships/Nexus/salbez_evsk3.png";
			$this->canvasSize = 70; //img has 200px per side
        $this->shipClass = "Es-ver Combat Cutter";
			$this->variantOf = "Ev'sk Mining Cutter";
			$this->occurence = "common";
		$this->unofficial = true;
			$this->isd = 2103;

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
		$this->addPrimarySystem(new CnC(99, 1, 0, 0)); //C&C should be unhittable anyway
//        $this->addPrimarySystem(new AntiquatedScanner(3, 12, 2, 4));
    	$sensors = new Scanner(3, 12, 2, 4);
			$sensors->markLCV();
			$this->addPrimarySystem($sensors);
		$this->addPrimarySystem(new Engine(3, 12, 0, 6, 3));

		$this->addFrontSystem(new LightParticleBeamShip(1, 2, 1, 150, 30));
		$this->addFrontSystem(new LaserCutter(1, 6, 4, 300, 60));
		$this->addFrontSystem(new LightParticleBeamShip(1, 2, 1, 330, 210));
	    
        $this->addPrimarySystem(new Structure(3, 30));
	    
        $this->hitChart = array(
        		0=> array( 
        				11 => "Structure",
        				13 => "1:Laser Cutter",
        				15 => "1:Light Particle Beam",
						17 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		1=> array( //redirect to PRIMARY
        				11 => "Structure",
        				13 => "1:Laser Cutter",
        				15 => "1:Light Particle Beam",
						17 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		2=> array( //redirect to PRIMARY
        				11 => "Structure",
        				13 => "1:Laser Cutter",
        				15 => "1:Light Particle Beam",
						17 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),        		
        ); //end of hit chart
    }
}
?>
