<?php
class SalbezEvsk extends LCV{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 110;
        $this->faction = "ZNexus Playtest Sal-bez";
        $this->phpclass = "SalbezEvsk";
        $this->imagePath = "img/ships/Nexus/salbez_evsk.png";
			$this->canvasSize = 55; //img has 200px per side
        $this->shipClass = "Ev'sk Mining Cutter";
		$this->unofficial = true;
			$this->isd = 2003;

        $this->hangarRequired = ''; //Nexus LCVs are more independent than their B5 counterparts
	    $this->notes = 'May deploy independently.';
	    $this->notes .= '<br>Antiquated Sensors.';
        
        $this->forwardDefense = 9;
        $this->sideDefense = 11;
        
		$this->agile = true;
        $this->turncost = 0.33;
        $this->turndelaycost = 0.5;
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
        $this->addPrimarySystem(new AntiquatedScanner(3, 12, 2, 3));
		$this->addPrimarySystem(new Engine(3, 12, 0, 6, 4));
		$this->addPrimarySystem(new NexusIndustrialLaser(1, 6, 2, 300, 360));
		$this->addPrimarySystem(new NexusParticleGrid(1, 3, 1, 180, 360));
		$this->addPrimarySystem(new NexusParticleGrid(1, 3, 1, 0, 180));
		$this->addPrimarySystem(new NexusIndustrialLaser(1, 6, 2, 0, 60));
	    
	    
        $this->addPrimarySystem(new Structure(2, 30));
	    
        $this->hitChart = array(
        		0=> array( 
        				10 => "Structure",
        				13 => "Industrial Laser",
        				15 => "Particle Grid",
						17 => "Engine",
        				19 => "Reactor",
        				20 => "Scanner",
        		),
        		1=> array( //redirect to PRIMARY
        				10 => "0:Structure",
        				13 => "0:Industrial Laser",
        				15 => "0:Particle Grid",
						17 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		2=> array( //redirect to PRIMARY
        				10 => "0:Structure",
        				13 => "0:Industrial Laser",
        				15 => "0:Particle Grid",
						17 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),        		
        ); //end of hit chart
    }
}
?>
