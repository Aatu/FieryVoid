<?php
class VelraxHisshrimCarrier extends HeavyCombatVesselLeftRight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 315;
	$this->faction = "ZNexus Velrax Republic (early)";
        $this->phpclass = "VelraxHisshrimCarrier";
        $this->imagePath = "img/ships/Nexus/velraxHisshrim.png";
        $this->shipClass = "Hisshrim Patrol Carrier";
	    $this->isd = 2023;
        $this->canvasSize = 125;
		$this->unofficial = true;

        $this->fighters = array("light"=>6, "heavy"=>6);

        $this->forwardDefense = 12;
        $this->sideDefense = 14;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 30;

        $this->addPrimarySystem(new Reactor(4, 14, 0, 0));
        $this->addPrimarySystem(new CnC(4, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 16, 5, 5));
        $this->addPrimarySystem(new Engine(4, 15, 0, 8, 4));
        $this->addFrontSystem(new PlasmaWaveTorpedo(3, 7, 4, 300, 60));
        $this->addAftSystem(new Thruster(3, 15, 0, 4, 1));
        $this->addAftSystem(new Thruster(4, 20, 0, 8, 2));

        $this->addLeftSystem(new NexusLaserSpear(3, 5, 3, 240, 360));
        $this->addLeftSystem(new NexusDartInterceptor(2, 4, 1, 180, 60));
        $this->addLeftSystem(new NexusTwinIonGun(2, 4, 4, 240, 60));
        $this->addLeftSystem(new NexusTwinIonGun(2, 4, 4, 120, 300));
		$this->addLeftSystem(new Hangar(3, 8));
        $this->addLeftSystem(new Thruster(3, 12, 0, 4, 3));

        $this->addRightSystem(new NexusLaserSpear(3, 5, 3, 0, 120));
        $this->addRightSystem(new NexusDartInterceptor(2, 4, 1, 300, 180));
        $this->addRightSystem(new NexusTwinIonGun(2, 4, 4, 300, 120));
        $this->addRightSystem(new NexusTwinIonGun(2, 4, 4, 60, 240));
		$this->addRightSystem(new Hangar(3, 8));
        $this->addRightSystem(new Thruster(3, 12, 0, 4, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(3, 50));
        $this->addLeftSystem(new Structure(3, 50));
        $this->addRightSystem(new Structure(3, 50));
    
            $this->hitChart = array(
        		0=> array(
        				7 => "Structure",
        				12 => "2:Thruster",
						13 => "1:Plasma Wave",
        				15 => "Scanner",
        				17 => "Engine",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		3=> array(
        				4 => "Thruster",
						5 => "1:Plasma Wave",
        				7 => "Twin Ion Gun",
						9 => "Dart Interceptor",
						11 => "Laser Spear",
						12 => "Hangar",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				4 => "Thruster",
						5 => "1:Plasma Wave",
        				7 => "Twin Ion Gun",
						9 => "Dart Interceptor",
						11 => "Laser Spear",
						12 => "Hangar",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
    
    }
}
?>
