<?php
class VelraxLightCarrierRefit extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 445;
	$this->faction = "Nexus Velrax Republic";
        $this->phpclass = "VelraxLightCarrierRefit";
        $this->imagePath = "img/ships/Nexus/VelraxResskar2.png";
        $this->shipClass = "Resskar Light Carrier (2105)";
        $this->shipSizeClass = 3;
		$this->canvasSize = 170; //img has 200px per side
		$this->limited = 33;
		$this->unofficial = true;

        $this->fighters = array("light"=>6, "heavy"=>6);

		$this->isd = 2105;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 14;
        
        $this->turncost = 1.0;
        $this->turndelaycost = 1.0;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(4, 18, 0, 0));
        $this->addPrimarySystem(new CnC(5, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 4, 6));
        $this->addPrimarySystem(new Engine(4, 15, 0, 8, 4));
		$this->addPrimarySystem(new Hangar(2, 4, 2));
   
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
		$this->addFrontSystem(new DualIonBolter(2, 4, 4, 240, 60));
        $this->addFrontSystem(new NexusStreakInterceptor(3, 4, 1, 240, 120));
        $this->addFrontSystem(new DualIonBolter(2, 4, 4, 300, 120));

        $this->addAftSystem(new Thruster(3, 12, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 4, 2));
		$this->addAftSystem(new JumpEngine(4, 20, 6, 35));

		$this->addLeftSystem(new PlasmaWaveTorpedo(3, 7, 4, 300, 60));
        $this->addLeftSystem(new DualIonBolter(2, 4, 4, 180, 360));
        $this->addLeftSystem(new Thruster(3, 12, 0, 4, 3));
		$this->addLeftSystem(new Hangar(2, 7, 6, 3));

		$this->addRightSystem(new PlasmaWaveTorpedo(3, 7, 4, 300, 60));
        $this->addRightSystem(new DualIonBolter(2, 4, 4, 300, 120));
        $this->addRightSystem(new Thruster(3, 12, 0, 4, 4));
		$this->addRightSystem(new Hangar(2, 7, 6, 3));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 38));
        $this->addAftSystem(new Structure(3, 30));
        $this->addLeftSystem(new Structure(3, 30));
        $this->addRightSystem(new Structure(3, 30));
        $this->addPrimarySystem(new Structure(4, 40));
		
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
					8 => "Dual Ion Bolter",
					10 => "Streak Interceptor",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					10 => "Jump Engine",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					5 => "Thruster",
					7 => "Dual Ion Bolter",
					9 => "Plasma Wave",
					12 => "Hangar",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					5 => "Thruster",
					7 => "Dual Ion Bolter",
					9 => "Plasma Wave",
					12 => "Hangar",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
