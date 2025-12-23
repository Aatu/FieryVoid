<?php
class VelraxVersissRefit extends HeavyCombatVesselLeftRight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 470;
	$this->faction = "Nexus Velrax Republic";
        $this->phpclass = "VelraxVersissRefit";
        $this->imagePath = "img/ships/Nexus/velraxVersiss.png";
        $this->shipClass = "Versiss Combat Scout (2108)";
			$this->variantOf = "Versythe Explorer (2108)";
			$this->occurence = "rare";
	    $this->isd = 2108;
        $this->limited = 10;
        $this->canvasSize = 125;
		$this->unofficial = true;

        $this->forwardDefense = 15;
        $this->sideDefense = 15;
        
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

        $this->addLeftSystem(new LaserLance(3, 6, 4, 300, 60));
        $this->addLeftSystem(new DualIonBolter(2, 4, 4, 180, 60));
        $this->addLeftSystem(new DualIonBolter(2, 4, 4, 120, 360));
        $this->addLeftSystem(new Thruster(3, 12, 0, 5, 3));
		$this->addLeftSystem(new ELINTScanner(3, 9, 2, 2));

        $this->addRightSystem(new LaserLance(3, 6, 4, 300, 60));
        $this->addRightSystem(new DualIonBolter(2, 4, 4, 300, 180));
        $this->addRightSystem(new DualIonBolter(2, 4, 4, 0, 240));
        $this->addRightSystem(new Thruster(3, 12, 0, 5, 4));
		$this->addRightSystem(new ELINTScanner(3, 9, 2, 2));

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
        				9 => "Dual Ion Bolter",
						11 => "ELINT Scanner",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				5 => "Thruster",
						7 => "Laser Spear",
        				9 => "Dual Ion Bolter",
						11 => "ELINT Scanner",
        				18 => "Structure",
        				20 => "Primary",        		),
        );
    
    }
}
?>
