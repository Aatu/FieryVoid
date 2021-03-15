<?php
class VelraxFleetCarrierRefit extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 615;
	$this->faction = "ZPlaytest Velrax";
        $this->phpclass = "VelraxFleetCarrierRefit";
        $this->imagePath = "img/ships/Nexus/VelraxNaresh.png";
        $this->shipClass = "Naresh Fleet Carrier (2107 Refit)";
			$this->variantOf = "Naresh Fleet Carrier";
			$this->occurence = "common";
        $this->shipSizeClass = 3;
		$this->canvasSize = 150; //img has 200px per side
		$this->limited = 33;
		$this->unofficial = true;

        $this->fighters = array("light"=>36, "heavy"=>12);

		$this->isd = 2107;
        
        $this->forwardDefense = 17;
        $this->sideDefense = 17;
        
        $this->turncost = 1.0;
        $this->turndelaycost = 1.0;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(5, 25, 0, 0));
        $this->addPrimarySystem(new CnC(5, 24, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 16, 4, 6));
        $this->addPrimarySystem(new Engine(4, 18, 0, 8, 4));
		$this->addPrimarySystem(new Hangar(2, 12));
   
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new NexusStreakInterceptor(3, 4, 1, 180, 60));
        $this->addFrontSystem(new DualIonBolter(2, 4, 4, 270, 90));
		$this->addFrontSystem(new PlasmaWaveTorpedo(3, 7, 4, 300, 60));
        $this->addFrontSystem(new DualIonBolter(2, 4, 4, 270, 90));
        $this->addFrontSystem(new NexusStreakInterceptor(3, 4, 1, 300, 180));

        $this->addAftSystem(new Thruster(3, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 9, 0, 2, 2));
		$this->addAftSystem(new JumpEngine(4, 20, 6, 35));
        $this->addAftSystem(new DualIonBolter(2, 4, 4, 120, 300));
        $this->addAftSystem(new LaserLance(3, 6, 4, 180, 240));
        $this->addAftSystem(new LaserLance(3, 6, 4, 120, 180));
        $this->addAftSystem(new DualIonBolter(2, 4, 4, 60, 240));

		$this->addLeftSystem(new PlasmaWaveTorpedo(3, 7, 4, 240, 360));
		$this->addLeftSystem(new LaserLance(3, 6, 4, 180, 360));
		$this->addLeftSystem(new LaserLance(3, 6, 4, 180, 360));
        $this->addLeftSystem(new DualIonBolter(2, 4, 4, 240, 60));
        $this->addLeftSystem(new Thruster(3, 12, 0, 4, 3));
		$this->addLeftSystem(new Hangar(2, 7));
		$this->addLeftSystem(new Hangar(2, 7));
		$this->addLeftSystem(new Hangar(2, 7));

		$this->addRightSystem(new PlasmaWaveTorpedo(3, 7, 4, 0, 120));
		$this->addRightSystem(new LaserLance(3, 6, 4, 0, 180));
		$this->addRightSystem(new LaserLance(3, 6, 4, 0, 180));
        $this->addRightSystem(new DualIonBolter(2, 4, 4, 300, 120));
        $this->addRightSystem(new Thruster(3, 12, 0, 4, 4));
		$this->addRightSystem(new Hangar(2, 7));
		$this->addRightSystem(new Hangar(2, 7));
		$this->addRightSystem(new Hangar(2, 7));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 50));
        $this->addAftSystem(new Structure(3, 40));
        $this->addLeftSystem(new Structure(3, 45));
        $this->addRightSystem(new Structure(3, 45));
        $this->addPrimarySystem(new Structure(4, 50));
		
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
					8 => "Plasma Wave",
					10 => "Streak Interceptor",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					8 => "Laser Lance",
					9 => "Dual Ion Bolter",
					12 => "Jump Engine",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					6 => "Thruster",
					7 => "Dual Ion Bolter",
					9 => "Laser Lance",
					12 => "Hangar",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					6 => "Thruster",
					7 => "Dual Ion Bolter",
					9 => "Laser Lance",
					12 => "Hangar",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
