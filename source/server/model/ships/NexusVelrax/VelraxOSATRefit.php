<?php
class VelraxOSATRefit extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 200;
		$this->faction = 'Nexus Velrax Republic';
        $this->phpclass = "VelraxOSATRefit";
        $this->imagePath = "img/ships/Nexus/velraxOSAT.png";
			$this->canvasSize = 90; //img has 100px per side
        $this->shipClass = "Standard OSAT (2112)";
		$this->unofficial = true;
		$this->isd = 2112;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 10;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;

        $this->addPrimarySystem(new OSATCnC(0, 1, 0, 0));
        $this->addPrimarySystem(new NexusRangedPlasmaWave(3, 7, 4, 270, 90));
        $this->addPrimarySystem(new DualIonBolter(2, 4, 4, 180, 360));
        $this->addPrimarySystem(new LaserLance(3, 6, 4, 300, 60));
        $this->addPrimarySystem(new DualIonBolter(2, 4, 4, 0, 180));
        $this->addPrimarySystem(new NexusRangedPlasmaWave(3, 7, 4, 270, 90));
        $this->addPrimarySystem(new Reactor(3, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 10, 4, 6));
        $this->addPrimarySystem(new Thruster(3, 8, 0, 0, 2));
                
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(3, 30));
		
		$this->hitChart = array(
			0=> array(
				8 => "Structure",
				10 => "Laser Lance",
				12 => "Ranged Plasma Wave",
				14 => "Dual Ion Bolter",
				16 => "Thruster",
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
