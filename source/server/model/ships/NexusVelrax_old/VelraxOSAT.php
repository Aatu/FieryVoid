<?php
class VelraxOSAT extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 170;
		$this->faction = 'Nexus Velrax Republic (early)';
        $this->phpclass = "VelraxOSAT";
        $this->imagePath = "img/ships/Nexus/velraxOSAT.png";
			$this->canvasSize = 90; //img has 100px per side
        $this->shipClass = "Standard OSAT";
//	    $this->variantOf = "Brixadii Weapons Platform";
//		$this->limited = 33;
		$this->unofficial = true;
		$this->isd = 2057;
        
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
        $this->addPrimarySystem(new NexusTwinIonGun(2, 4, 4, 180, 360));
        $this->addPrimarySystem(new NexusHeavyLaserSpear(3, 6, 4, 300, 60));
        $this->addPrimarySystem(new NexusTwinIonGun(2, 4, 4, 0, 180));
        $this->addPrimarySystem(new NexusRangedPlasmaWave(3, 7, 4, 270, 90));
        $this->addPrimarySystem(new Reactor(3, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 10, 4, 5));
        $this->addPrimarySystem(new Thruster(3, 8, 0, 0, 2));
                
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(3, 30));
		
		$this->hitChart = array(
			0=> array(
				8 => "Structure",
				10 => "Heavy Laser Spear",
				12 => "Ranged Plasma Wave",
				14 => "Twin Ion Gun",
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
