<?php
class VelraxHeavyTransportRefit extends HeavyCombatVesselLeftRight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 160;
	$this->faction = "Nexus Support Units";
        $this->phpclass = "VelraxHeavyTransportRefit";
        $this->imagePath = "img/ships/Nexus/velraxVersythe.png";
        $this->shipClass = "Velrax Heavy Transport (2108)";
			$this->variantOf = "Velrax Heavy Transport";
			$this->occurence = "common";
	    $this->isd = 2108;
        $this->canvasSize = 125;
		$this->unofficial = true;

        $this->forwardDefense = 15;
        $this->sideDefense = 15;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 30;

        $this->addPrimarySystem(new Reactor(3, 12, 0, 0));
        $this->addPrimarySystem(new CnC(3, 6, 0, 0));
        $this->addPrimarySystem(new Scanner(2, 8, 3, 3));
        $this->addPrimarySystem(new Engine(3, 15, 0, 6, 4));
        $this->addPrimarySystem(new Hangar(2, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 4, 1));
        $this->addAftSystem(new Thruster(3, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 3, 2));

        $this->addLeftSystem(new DualIonBolter(2, 4, 4, 180, 60));
        $this->addLeftSystem(new DualIonBolter(2, 4, 4, 120, 360));
		$this->addLeftSystem(new Hangar(1, 2));
        $this->addLeftSystem(new Thruster(3, 12, 0, 3, 3));
		$this->addLeftSystem(new CargoBay(1, 30)); 
		$this->addLeftSystem(new CargoBay(1, 30)); 

        $this->addRightSystem(new DualIonBolter(2, 4, 4, 300, 180));
        $this->addRightSystem(new DualIonBolter(2, 4, 4, 0, 240));
		$this->addRightSystem(new Hangar(1, 2));
        $this->addRightSystem(new Thruster(3, 12, 0, 3, 4));
		$this->addRightSystem(new CargoBay(1, 30)); 
		$this->addRightSystem(new CargoBay(1, 30)); 

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(3, 36));
        $this->addLeftSystem(new Structure(3, 28));
        $this->addRightSystem(new Structure(3, 28));
    
            $this->hitChart = array(
        		0=> array(
        				9 => "Structure",
        				12 => "2:Thruster",
        				14 => "Scanner",
        				16 => "Engine",
						17 => "Hangar",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		3=> array(
        				4 => "Thruster",
						9 => "Cargo Bay",
						10 => "Hangar",
        				12 => "Dual Ion Bolter",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				4 => "Thruster",
						9 => "Cargo Bay",
						10 => "Hangar",
        				12 => "Dual Ion Bolter",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
    
    }
}
?>
