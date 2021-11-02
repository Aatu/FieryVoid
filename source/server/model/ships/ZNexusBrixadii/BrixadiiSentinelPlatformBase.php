<?php
class BrixadiiSentinelPlatformBase extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 120;
		$this->faction = 'ZNexus Brixadii';
        $this->phpclass = "BrixadiiSentinelPlatformBase";
        $this->imagePath = "img/ships/Nexus/BrixadiiSentinelPlatform.png";
        $this->shipClass = "Brixadii Sentinel Platform";
			$this->canvasSize = 175;
		$this->unofficial = true;
		$this->isd = 1960;
        
        $this->forwardDefense = 8;
        $this->sideDefense = 8;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;


        $this->addPrimarySystem(new HvyParticleProjector(3, 8, 4, 300, 60));
        $this->addPrimarySystem(new HvyParticleProjector(3, 8, 4, 300, 60));
        $this->addPrimarySystem(new NexusChaffLauncher(1, 2, 1, 0, 360));
        $this->addPrimarySystem(new NexusDefensePulsar(1, 4, 2, 0, 360));
        $this->addPrimarySystem(new Reactor(4, 9, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 6, 2, 3));   
        $this->addPrimarySystem(new Thruster(3, 6, 0, 0, 2));
                
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(4, 20));
		
		$this->hitChart = array(
			0=> array(
				8 => "Structure",
				10 => "Thruster",
				14 => "Heavy Particle Projector",
				15 => "Chaff Launcher",
				16 => "Defense Pulsar",
				18 => "Scanner",
				20 => "Reactor",
			),
			1=> array(
				20 => "Primary",
			),
			2=> array(
				20 => "Primary",
			),
        );
    }
}

?>
