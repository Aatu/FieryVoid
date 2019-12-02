<?php
class BrixadiiPursuitFrigate2108 extends LCV{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 320;
        $this->faction = "ZNexus Brixadii";
        $this->phpclass = "BrixadiiPursuitFrigate2108";
        $this->imagePath = "img/ships/Nexus/BrixadiiPursuitFrigate.png";
			$this->canvasSize = 200; //img has 200px per side
        $this->shipClass = "Pursuit Frigate (2108)";
		$this->variantOf = "Pursuit Frigate";
		$this->occurence = "common";
		$this->unofficial = true;
       		$this->isd = 2108;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 12;
        
	$this->agile = true;
        $this->turncost = 0.33;
        $this->turndelaycost = 0.50;
        $this->accelcost = 1;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus = 12*5;
        
         
        $this->addPrimarySystem(new Reactor(4, 9, 0, 0));
        $this->addPrimarySystem(new CnC(4, 8, 0, 0));
	    	$sensors = new Scanner(3, 10, 4, 5);
		$sensors->markLCV();
		$this->addPrimarySystem($sensors);
        $this->addPrimarySystem(new Engine(4, 9, 0, 9, 4));
        $this->addPrimarySystem(new Hangar(1, 2));
        $this->addPrimarySystem(new Thruster(2, 14, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(2, 14, 0, 4, 4));
      
        $this->addFrontSystem(new Thruster(3, 14, 0, 6, 1));
	$this->addFrontSystem(new ParticleProjector(2, 6, 1, 180, 60));
	$this->addFrontSystem(new ParticleProjector(2, 6, 1, 270, 90));
	$this->addFrontSystem(new ParticleProjector(2, 6, 1, 270, 90));
	$this->addFrontSystem(new ParticleProjector(2, 6, 1, 300, 180));
	$this->addFrontSystem(new NexusKineticBoxLauncher(2, 0, 0, 300, 60));
                
        $this->addAftSystem(new Thruster(3, 10, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 4, 2));
        $this->addAftSystem(new NexusParticleProjectorLight(1, 0, 0, 120, 0));
        $this->addAftSystem(new NexusParticleProjectorLight(1, 0, 0, 0, 240));
	$this->addAftSystem(new NexusChaffLauncher(2, 0, 0, 0, 360));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure( 3, 40));
        $this->hitChart = array(
            0=> array(
                    9 => "Thruster",
		    10 => "Hangar",
                    13 => "Scanner",
                    17 => "Engine",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    5 => "Thruster",
		    6 => "Kinetic Box Launcher",
                    10 => "Particle Projector",
		    17 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    7 => "Thruster",
		    8 => "Chaff Launcher",
                    10 => "Light Particle Projector",
                    17 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}

?>
