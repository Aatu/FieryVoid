<?php
class BrixadiiPoliceFrigateBase extends LCV{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 70;
        $this->faction = "ZNexus Brixadii";
        $this->phpclass = "BrixadiiPoliceFrigateBase";
        $this->imagePath = "img/ships/Nexus/BrixadiiPoliceFrigateEarly.png";
			$this->canvasSize = 60; //img has 200px per side
        $this->shipClass = "Police Frigate";
		//$this->variantOf = "Police Frigate";
		$this->unofficial = true;
			$this->isd = 1842;

        $this->hangarRequired = ''; //Nexus LCVs are more independent than their B5 counterparts
	    $this->notes = 'May deploy independently.';
	    $this->notes .= '<br>Antiquated Sensors.';
        
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
  
		$this->addPrimarySystem(new Reactor(4, 7, 0, 0));
		$this->addPrimarySystem(new CnC(99, 99, 0, 0)); //C&C should be unhittable anyway
        $this->addPrimarySystem(new AntiquatedScanner(4, 9, 2, 3));
		$this->addPrimarySystem(new Engine(3, 10, 0, 6, 3));
		$this->addPrimarySystem(new ParticleProjector(2, 6, 1, 270, 90));
		$this->addPrimarySystem(new LightParticleProjector(2, 3, 1, 240, 120));
		$this->addPrimarySystem(new LightParticleProjector(2, 3, 1, 240, 120));
	    
        $this->addPrimarySystem(new Structure(3, 27));
	    
        $this->hitChart = array(
        		0=> array( 
        				11 => "Structure",
						13 => "Light Particle Projector",
        				15 => "Particle Projector",
        				18 => "Engine",
        				19 => "Reactor",
        				20 => "Scanner",
        		),
        		1=> array( //redirect to PRIMARY
        				11 => "0:Structure",
						13 => "0:Light Particle Projector",
        				15 => "0:Particle Projector",
        				18 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		2=> array( //redirect to PRIMARY
        				11 => "0:Structure",
						13 => "0:Light Particle Projector",
        				15 => "0:Particle Projector",
        				18 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),        		
        ); //end of hit chart
    }
}
?>
