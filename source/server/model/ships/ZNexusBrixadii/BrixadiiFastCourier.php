<?php
class BrixadiiFastCourier extends BaseShipNoAft{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 390;
		$this->faction = "ZNexus Brixadii";
        $this->phpclass = "BrixadiiFastCourier";
        $this->imagePath = "img/ships/Nexus/BrixadiiWarship.png";
			$this->canvasSize = 145; //img has 200px per side
        $this->shipClass = "Fast Courier";
		$this->unofficial = true;
		$this->isd = 2114;
         
	    $this->notes = 'Crude Jump Drive';
		
        $this->forwardDefense = 14;
        $this->sideDefense = 14;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
		$this->iniativebonus = 0;
         
        $this->addPrimarySystem(new Reactor(5, 18, 0, 0));
        $this->addPrimarySystem(new CnC(5, 20, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 16, 5, 6));
        $this->addPrimarySystem(new Engine(4, 18, 0, 10, 4));
		$this->addPrimarySystem(new Hangar(2, 4));
		$this->addPrimarySystem(new JumpEngine(4, 12, 5, 40));
		$this->addPrimarySystem(new Thruster(4, 15, 0, 5, 2));
		$this->addPrimarySystem(new Thruster(4, 15, 0, 5, 2));

        $this->addFrontSystem(new Thruster(3, 14, 0, 5, 1));
        $this->addFrontSystem(new Thruster(3, 14, 0, 5, 1));
		$this->addFrontSystem(new NexusProjectorArray(3, 6, 1, 240, 60));
		$this->addFrontSystem(new NexusProjectorArray(3, 6, 1, 300, 120));
		$this->addFrontSystem(new CargoBay(2, 16));
		$this->addFrontSystem(new CargoBay(2, 16));
        
		$this->addLeftSystem(new Thruster(3, 14, 0, 8, 3));
		$this->addLeftSystem(new ScatterPulsar(2, 4, 2, 180, 360));
		$this->addLeftSystem(new ScatterPulsar(2, 4, 2, 180, 360));
		$this->addLeftSystem(new NexusChaffLauncher(1, 2, 1, 180, 360));
		$this->addLeftSystem(new CargoBay(2, 16));

		$this->addRightSystem(new Thruster(3, 14, 0, 8, 4));
		$this->addRightSystem(new ScatterPulsar(2, 4, 2, 0, 180));
		$this->addRightSystem(new ScatterPulsar(2, 4, 2, 0, 180));
		$this->addRightSystem(new NexusChaffLauncher(1, 2, 1, 0, 180));
		$this->addRightSystem(new CargoBay(2, 16));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 40));
        $this->addLeftSystem(new Structure( 4, 40));
        $this->addRightSystem(new Structure( 4, 40));
        $this->addPrimarySystem(new Structure( 5, 44));
		
        $this->hitChart = array(
            0=> array(
                    8 => "Structure",
                    10 => "JumpEngine",
                    12 => "Thruster",
					14 => "Scanner",
                    16 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    5 => "Thruster",
					10 => "Cargo Bay",
					12 => "Projector Array",
					18 => "Structure",
                    20 => "Primary",
            ),
            3=> array(
                    5 => "Thruster",
					9 => "Cargo Bay",
					10 => "Chaff Launcher",
					12 => "Scatter Pulsar",
                    18 => "Structure",
                    20 => "Primary",
			),
            4=> array(
                    5 => "Thruster",
					9 => "Cargo Bay",
					10 => "Chaff Launcher",
					12 => "Scatter Pulsar",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );

		
    }
}
?>