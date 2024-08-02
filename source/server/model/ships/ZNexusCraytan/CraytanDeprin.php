<?php
class CraytanDeprin extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 600;
		$this->faction = "ZNexus Craytan Union";
        $this->phpclass = "CraytanDeprin";
        $this->imagePath = "img/ships/Nexus/CraytanDeprin.png";
        $this->shipClass = "Deprin Orbital Defenses";
		$this->unofficial = true;
        $this->isd = 1984;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 12;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;

        $this->addPrimarySystem(new OSATCnC(0, 1, 0, 0));
        $this->addPrimarySystem(new Reactor(4, 24, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 16, 3, 5));   
		$this->addPrimarySystem(new Magazine(4, 18));
		$this->addPrimarySystem(new NexusCIDS(2, 4, 2, 180, 360));
		$this->addPrimarySystem(new NexusCIDS(2, 4, 2, 180, 360));
		$this->addPrimarySystem(new NexusHeavySentryGun(3, 7, 3, 300, 60));
		$this->addPrimarySystem(new NexusAssaultCannonBattery(4, 16, 10, 0, 360));
		$this->addPrimarySystem(new NexusAssaultCannonBattery(4, 16, 10, 0, 360));
		$this->addPrimarySystem(new NexusHeavySentryGun(3, 7, 3, 300, 60));
		$this->addPrimarySystem(new NexusCIDS(2, 4, 2, 0, 180));
		$this->addPrimarySystem(new NexusCIDS(2, 4, 2, 0, 180));
        $this->addPrimarySystem(new Thruster(4, 20, 0, 0, 2));
                
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(4, 80));
		
		$this->hitChart = array(
			0=> array(
				6 => "Structure",
				8 => "Thruster",
				10 => "Heavy Sentry Gun",
				13 => "Assault Cannon Battery",
				15 => "Close-In Defense System",
				17 => "Scanner",
				19 => "Reactor",
				20 => "Magazine",
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
