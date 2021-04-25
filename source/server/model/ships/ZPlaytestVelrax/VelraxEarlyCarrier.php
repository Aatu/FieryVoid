<?php
class VelraxEarlyCarrier extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 385;
	$this->faction = "ZPlaytest Velrax";
        $this->phpclass = "VelraxEarlyCarrier";
        $this->imagePath = "img/ships/Nexus/VelraxDraskar.png";
        $this->shipClass = "Draskar Early Carrier";
        $this->shipSizeClass = 3;
		$this->canvasSize = 150; //img has 200px per side
		$this->limited = 33;
		$this->unofficial = true;

        $this->fighters = array("light"=>12, "heavy"=>6);

		$this->isd = 2032;
        
        $this->forwardDefense = 16;
        $this->sideDefense = 16;
        
        $this->turncost = 0.75;
        $this->turndelaycost = 0.75;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(4, 18, 0, 0));
        $this->addPrimarySystem(new CnC(4, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 16, 4, 5));
        $this->addPrimarySystem(new Engine(4, 18, 0, 6, 4));
		$this->addPrimarySystem(new Hangar(2, 8));
   
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new NexusTwinIonGun(2, 4, 4, 240, 60));
        $this->addFrontSystem(new NexusEarlyPlasmaWave(2, 7, 4, 300, 60));
        $this->addFrontSystem(new NexusEarlyPlasmaWave(2, 7, 4, 300, 60));
        $this->addFrontSystem(new NexusTwinIonGun(2, 4, 4, 300, 120));

        $this->addAftSystem(new Thruster(3, 14, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 14, 0, 3, 2));
        $this->addAftSystem(new NexusTwinIonGun(2, 4, 4, 90, 270));
		$this->addAftSystem(new NexusLaserSpear(3, 5, 3, 120, 240));
		$this->addAftSystem(new NexusLaserSpear(3, 5, 3, 120, 240));
        $this->addAftSystem(new NexusTwinIonGun(2, 4, 4, 90, 270));

		$this->addLeftSystem(new NexusLaserSpear(3, 5, 3, 300, 60));
        $this->addLeftSystem(new NexusDartInterceptor(2, 4, 1, 240, 60));
        $this->addLeftSystem(new NexusTwinIonGun(2, 4, 4, 180, 360));
        $this->addLeftSystem(new Thruster(3, 12, 0, 4, 3));
		$this->addLeftSystem(new Hangar(2, 6));

		$this->addRightSystem(new NexusLaserSpear(3, 5, 3, 300, 60));
        $this->addRightSystem(new NexusDartInterceptor(2, 4, 1, 300, 120));
        $this->addRightSystem(new NexusTwinIonGun(2, 4, 4, 0, 180));
        $this->addRightSystem(new Thruster(3, 12, 0, 4, 4));
		$this->addRightSystem(new Hangar(2, 6));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(3, 42));
        $this->addAftSystem(new Structure(3, 40));
        $this->addLeftSystem(new Structure(3, 40));
        $this->addRightSystem(new Structure(3, 40));
        $this->addPrimarySystem(new Structure(3, 40));
		
		$this->hitChart = array(
			0=> array(
					9 => "Structure",
					12 => "Scanner",
					15 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					6 => "Thruster",
					8 => "Twin Ion Gun",
					10 => "Early Plasma Wave",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					7 => "Thruster",
					9 => "Laser Spear",
					11 => "Twin Ion Gun",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					5 => "Thruster",
					7 => "Laser Spear",
					8 => "Dart Interceptor",
					9 => "Twin Ion Gun",
					11 => "Hangar",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					5 => "Thruster",
					7 => "Laser Spear",
					8 => "Dart Interceptor",
					9 => "Twin Ion Gun",
					11 => "Hangar",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
