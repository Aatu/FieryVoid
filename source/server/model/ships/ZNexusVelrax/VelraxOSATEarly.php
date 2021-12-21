<?php
class VelraxOSATEarly extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 150;
		$this->faction = 'ZNexus Velrax';
        $this->phpclass = "VelraxOSATEarly";
        $this->imagePath = "img/ships/Nexus/VelraxOSAT.png";
			$this->canvasSize = 80; //img has 100px per side
        $this->shipClass = "Early OSAT";
			$this->variantOf = "Standard OSAT";
			$this->occurence = "common";
		$this->unofficial = true;
		$this->isd = 2019;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 10;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;

        $this->addPrimarySystem(new NexusRangedEarlyPlasmaWave(3, 7, 4, 270, 90));
        $this->addPrimarySystem(new NexusTwinIonGun(2, 4, 4, 180, 360));
        $this->addPrimarySystem(new NexusLaserSpear(3, 5, 3, 300, 60));
        $this->addPrimarySystem(new NexusTwinIonGun(2, 4, 4, 0, 180));
        $this->addPrimarySystem(new NexusRangedEarlyPlasmaWave(3, 7, 4, 270, 90));
        $this->addPrimarySystem(new Reactor(3, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 10, 4, 5));
        $this->addPrimarySystem(new Thruster(3, 8, 0, 0, 2));
                
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(3, 30));
		
		$this->hitChart = array(
			0=> array(
				8 => "Structure",
				10 => "Laser Spear",
				12 => "Ranged Early Plasma Wave",
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
