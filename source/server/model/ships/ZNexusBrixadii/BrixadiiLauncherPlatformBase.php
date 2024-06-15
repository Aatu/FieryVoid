<?php
class BrixadiiLauncherPlatformBase extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 120;
		$this->faction = 'ZNexus Brixadii Clans';
        $this->phpclass = "BrixadiiLauncherPlatformBase";
        $this->imagePath = "img/ships/Nexus/BrixadiiSentinelPlatform.png";
        $this->shipClass = "Brixadii Launcher Platform";
			$this->canvasSize = 175;
		$this->unofficial = true;
		$this->isd = 2060;
        
        $this->forwardDefense = 8;
        $this->sideDefense = 8;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;

        $this->addPrimarySystem(new OSATCnC(0, 1, 0, 0));
        $this->addPrimarySystem(new NexusRangedKineticBoxLauncher(3, 10, 0, 270, 90));
        $this->addPrimarySystem(new NexusRangedKineticBoxLauncher(3, 10, 0, 270, 90));
        $this->addPrimarySystem(new NexusChaffLauncher(1, 2, 1, 0, 360));
        $this->addPrimarySystem(new LightParticleBeamShip(1, 2, 1, 0, 360));
        $this->addPrimarySystem(new Reactor(4, 9, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 6, 3, 5));   
        $this->addPrimarySystem(new Thruster(3, 6, 0, 0, 2));
                
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(4, 20));
		
		$this->hitChart = array(
			0=> array(
				8 => "Structure",
				10 => "Thruster",
				12 => "Ranged Kinetic Box Launcher",
				14 => "Chaff Launcher",
				16 => "Light Particle Beam",
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
