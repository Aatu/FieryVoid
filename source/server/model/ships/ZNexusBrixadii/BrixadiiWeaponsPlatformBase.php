<?php
class BrixadiiWeaponsPlatformBase extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 90;
		$this->faction = 'ZNexus Brixadii';
        $this->phpclass = "BrixadiiWeaponsPlatformBase";
        $this->imagePath = "img/ships/Nexus/BrixadiiWeaponsPlatform.png";
        $this->shipClass = "Brixadii Weapons Platform";
			$this->canvasSize = 175;
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


        $this->addPrimarySystem(new ParticleHammer(1, 12, 6, 300, 60));
        $this->addPrimarySystem(new NexusChaffLauncher(1, 2, 1, 0, 360));
        $this->addPrimarySystem(new Reactor(4, 9, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 6, 2, 3));   
        $this->addPrimarySystem(new Thruster(3, 6, 0, 0, 2));
                
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(4, 20));
		
		$this->hitChart = array(
			0=> array(
				9 => "Structure",
				11 => "Thruster",
				15 => "Particle Hammer",
				16 => "Chaff Launcher",
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
