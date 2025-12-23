<?php
class VelraxVersissScout extends HeavyCombatVesselLeftRight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 420;
	$this->faction = "Nexus Velrax Republic (early)";
        $this->phpclass = "VelraxVersissScout";
        $this->imagePath = "img/ships/Nexus/velraxVersiss.png";
        $this->shipClass = "Versiss Combat Scout";
			$this->variantOf = "Versythe Explorer";
			$this->occurence = "rare";
	    $this->isd = 2059;
        $this->limited = 10;
        $this->canvasSize = 125;
		$this->unofficial = true;

        $this->forwardDefense = 14;
        $this->sideDefense = 14;
        
        $this->turncost = 0.75;
        $this->turndelaycost = 0.75;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 30;

        $this->addPrimarySystem(new Reactor(4, 18, 0, 0));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new ELINTScanner(4, 16, 6, 6));
        $this->addPrimarySystem(new Engine(4, 20, 0, 8, 4));
        $this->addPrimarySystem(new Hangar(2, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 5, 1));
        $this->addAftSystem(new Thruster(3, 12, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 4, 2));

        $this->addLeftSystem(new NexusLaserSpear(3, 5, 3, 300, 60));
        $this->addLeftSystem(new NexusTwinIonGun(2, 4, 4, 180, 60));
        $this->addLeftSystem(new NexusTwinIonGun(2, 4, 4, 120, 360));
        $this->addLeftSystem(new Thruster(3, 12, 0, 5, 3));
		$this->addLeftSystem(new ELINTScanner(3, 9, 2, 1));

        $this->addRightSystem(new NexusLaserSpear(3, 5, 3, 300, 60));
        $this->addRightSystem(new NexusTwinIonGun(2, 4, 4, 300, 180));
        $this->addRightSystem(new NexusTwinIonGun(2, 4, 4, 0, 240));
        $this->addRightSystem(new Thruster(3, 12, 0, 5, 4));
		$this->addRightSystem(new ELINTScanner(3, 9, 2, 1));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(4, 36));
        $this->addLeftSystem(new Structure(4, 25));
        $this->addRightSystem(new Structure(4, 25));
    
            $this->hitChart = array(
        		0=> array(
        				9 => "Structure",
        				12 => "2:Thruster",
        				14 => "ELINT Scanner",
        				16 => "Engine",
						17 => "Hangar",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		3=> array(
        				5 => "Thruster",
						7 => "Laser Spear",
        				9 => "Twin Ion Gun",
						11 => "ELINT Scanner",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				5 => "Thruster",
						7 => "Laser Spear",
        				9 => "Twin Ion Gun",
						11 => "ELINT Scanner",
        				18 => "Structure",
        				20 => "Primary",        		),
        );
    
    }
}
?>
