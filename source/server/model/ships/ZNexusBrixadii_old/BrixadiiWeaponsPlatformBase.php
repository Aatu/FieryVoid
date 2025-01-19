<?php
class BrixadiiWeaponsPlatformBase extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 120;
		$this->faction = 'ZNexus Brixadii Clans (early)';
        $this->phpclass = "BrixadiiWeaponsPlatformBase";
        $this->imagePath = "img/ships/Nexus/brixadii_osat_single_mount.png";
        $this->shipClass = "Brixadii Weapons Platform";
			$this->canvasSize = 90;
	    $this->limited = 10;
		$this->unofficial = true;
		$this->isd = 2059;

        
        $this->forwardDefense = 8;
        $this->sideDefense = 8;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;

        $this->addPrimarySystem(new OSATCnC(0, 1, 0, 0));
        $this->addFrontSystem(new ParticleHammer(1, 12, 6, 300, 60));
        $this->addFrontSystem(new NexusChaffLauncher(1, 2, 1, 0, 360));
        $this->addFrontSystem(new LightParticleBeamShip(1, 2, 1, 0, 360));
        $this->addPrimarySystem(new Reactor(4, 9, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 6, 3, 5));   
        $this->addAftSystem(new Thruster(3, 6, 0, 0, 2));
                
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(4, 20));
		
		$this->hitChart = array(
			0=> array(
				7 => "Structure",
				9 => "2:Thruster",
				13 => "1:Particle Hammer",
				14 => "1:Chaff Launcher",
				16 => "1:Light Particle Beam",
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
