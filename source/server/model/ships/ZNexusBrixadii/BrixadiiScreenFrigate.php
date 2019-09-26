<?php
class BrixadiiScreenFrigate extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 70;
        $this->faction = "ZNexus Brixadii";
        $this->phpclass = "BrixadiiScreenFrigate";
        $this->imagePath = "img/ships/Nexus/BrixadiiPoliceFrigate.png";
			$this->canvasSize = 200; //img has 200px per side
        $this->shipClass = "Screen Frigate";
		$this->variantOf = "Police Frigate";
        $this->occurence = "uncommon";
		$this->unofficial = true;
			$this->isd = 2107;
        
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
		$this->addPrimarySystem(new Scanner(4, 9, 2, 4));
		$this->addPrimarySystem(new Engine(3, 10, 0, 5, 3));
		$this->addPrimarySystem(new NexusParticleProjectorLight(2, 0, 0, 240, 120));
		$this->addPrimarySystem(new NexusParticleProjectorLight(2, 0, 0, 180, 360));
		$this->addPrimarySystem(new NexusParticleProjectorLight(2, 0, 0, 0, 180));
	    
	    
        $this->addPrimarySystem(new Structure(4, 21));
	    
        $this->hitChart = array(
        		0=> array( 
        				11 => "Structure",
        				15 => "Light Particle Projector",
        				18 => "Engine",
        				19 => "Reactor",
        				20 => "Scanner",
        		),
        		1=> array( //redirect to PRIMARY
						20 => "Primary",
        		),
        		2=> array( //redirect to PRIMARY
						20 => "Primary",
        		),     
        ); //end of hit chart
    }
}
?>