<?php
class BrixadiiPoliceFrigateBase extends LCV{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 100;
        $this->faction = "ZNexus Brixadii Clans (early)";
        $this->phpclass = "BrixadiiPoliceFrigateBase";
        $this->imagePath = "img/ships/Nexus/BrixadiiPoliceFrigate.png";
			$this->canvasSize = 70; //img has 200px per side
        $this->shipClass = "Police Frigate";
		//$this->variantOf = "Police Frigate";
		$this->unofficial = true;
			$this->isd = 1842;

        $this->hangarRequired = ''; //Nexus LCVs are more independent than their B5 counterparts
	    $this->notes = 'May deploy independently.';
        
        $this->forwardDefense = 9;
        $this->sideDefense = 11;
        
		$this->agile = true;
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus = 15*5;
 
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance
  
		$this->addPrimarySystem(new Reactor(3, 7, 0, 0));
		$this->addPrimarySystem(new CnC(99, 1, 0, 0)); //C&C should be unhittable anyway
//        $this->addPrimarySystem(new AntiquatedScanner(4, 9, 2, 3));
    	$sensors = new Scanner(3, 9, 2, 3);
			$sensors->markLCV();
			$this->addPrimarySystem($sensors);
		$this->addPrimarySystem(new Engine(3, 10, 0, 6, 3));

		$this->addFrontSystem(new LightParticleProjector(2, 3, 1, 240, 60));
		$this->addFrontSystem(new ParticleProjector(2, 6, 1, 300, 60));
		$this->addFrontSystem(new ParticleProjector(2, 6, 1, 300, 60));
		$this->addFrontSystem(new LightParticleProjector(2, 3, 1, 300, 120));
	    
        $this->addPrimarySystem(new Structure(3, 27));
	    
        $this->hitChart = array(
        		0=> array( 
        				11 => "Structure",
						13 => "1:Light Particle Projector",
        				15 => "1:Particle Projector",
        				18 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		1=> array( //redirect to PRIMARY
        				11 => "Structure",
						13 => "1:Light Particle Projector",
        				15 => "1:Particle Projector",
        				18 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		2=> array( //redirect to PRIMARY
        				11 => "Structure",
						13 => "1:Light Particle Projector",
        				15 => "1:Particle Projector",
        				18 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),        		
        ); //end of hit chart
    }
}
?>
