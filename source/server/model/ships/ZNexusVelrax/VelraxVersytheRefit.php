<?php
class VelraxVersytheRefit extends HeavyCombatVesselLeftRight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 430;
	$this->faction = "ZNexus Velrax Republic";
        $this->phpclass = "VelraxVersytheRefit";
        $this->imagePath = "img/ships/Nexus/VelraxVersythe.png";
        $this->shipClass = "Versythe Explorer (2070 refit)";
			$this->variantOf = "Versythe Explorer";
			$this->occurence = "common";
        $this->limited = 10;
	    $this->isd = 2061;
        $this->canvasSize = 105;
		$this->unofficial = true;

        $this->fighters = array("normal"=>6);

        $this->forwardDefense = 15;
        $this->sideDefense = 15;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 30;

        $this->addPrimarySystem(new Reactor(3, 18, 0, 0));
        $this->addPrimarySystem(new CnC(3, 12, 0, 0));
        $this->addPrimarySystem(new ELINTScanner(3, 16, 6, 6));
        $this->addPrimarySystem(new Engine(3, 20, 0, 8, 4));
        $this->addPrimarySystem(new Hangar(2, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 4, 1));
        $this->addAftSystem(new Thruster(3, 12, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 4, 2));
		$this->addFrontSystem(new JumpEngine(3, 20, 5, 35));

        $this->addLeftSystem(new NexusLaserSpear(3, 5, 3, 300, 60));
        $this->addLeftSystem(new NexusTwinIonGun(2, 4, 4, 180, 60));
        $this->addLeftSystem(new NexusTwinIonGun(2, 4, 4, 120, 360));
		$this->addLeftSystem(new Hangar(3, 3));
        $this->addLeftSystem(new Thruster(3, 12, 0, 4, 3));
		$this->addLeftSystem(new ELINTScanner(3, 9, 2, 1));
		$this->addLeftSystem(new CargoBay(2, 30)); 

        $this->addRightSystem(new NexusLaserSpear(3, 5, 3, 300, 60));
        $this->addRightSystem(new NexusTwinIonGun(2, 4, 4, 300, 180));
        $this->addRightSystem(new NexusTwinIonGun(2, 4, 4, 0, 240));
		$this->addRightSystem(new Hangar(3, 6));
        $this->addRightSystem(new Thruster(3, 12, 0, 4, 4));
		$this->addRightSystem(new ELINTScanner(3, 9, 2, 1));
		$this->addRightSystem(new Quarters(2, 20)); 

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(4, 36));
        $this->addLeftSystem(new Structure(3, 28));
        $this->addRightSystem(new Structure(3, 28));
    
            $this->hitChart = array(
        		0=> array(
        				7 => "Structure",
        				10 => "2:Thruster",
						12 => "1:Jump Engine",
        				14 => "ELINT Scanner",
        				16 => "Engine",
						17 => "Hangar",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		3=> array(
        				3 => "Thruster",
						5 => "Cargo Bay",
						7 => "Laser Spear",
        				9 => "Twin Ion Gun",
						11 => "ELINT Scanner",
						12 => "Hangar",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				3 => "Thruster",
						5 => "Quarters",
						7 => "Laser Spear",
        				9 => "Twin Ion Gun",
						11 => "ELINT Scanner",
						12 => "Hangar",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
    
    }
}
?>
