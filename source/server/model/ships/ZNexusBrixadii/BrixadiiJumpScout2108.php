<?php
class BrixadiiJumpScout2108 extends BaseShipNoAft{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 510;
		$this->faction = "ZNexus Brixadii";
        $this->phpclass = "BrixadiiJumpScout2108";
        $this->imagePath = "img/ships/Nexus/BrixadiiWarship.png";
			$this->canvasSize = 145; //img has 200px per side
        $this->shipClass = "Jump Scout (2108)";
			$this->variantOf = "Jump Scout";
			$this->occurence = "common";		
			$this->limited = 10;
		$this->unofficial = true;
		$this->isd = 2108;

	    $this->notes .= '<br>Crude Jump Drive';
		
        $this->forwardDefense = 14;
        $this->sideDefense = 14;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
		$this->iniativebonus = 10;
        
        $this->addPrimarySystem(new Reactor(5, 20, 0, 0));
        $this->addPrimarySystem(new CnC(5, 20, 0, 0));
        $this->addPrimarySystem(new ELINTScanner(4, 16, 5, 8));
        $this->addPrimarySystem(new Engine(4, 18, 0, 10, 4));
		$this->addPrimarySystem(new Hangar(2, 4));
		$this->addPrimarySystem(new JumpEngine(4, 12, 5, 40));
		$this->addPrimarySystem(new Thruster(4, 15, 0, 5, 2));
		$this->addPrimarySystem(new Thruster(4, 15, 0, 5, 2));

        $this->addFrontSystem(new Thruster(3, 14, 0, 5, 1));
        $this->addFrontSystem(new Thruster(3, 14, 0, 5, 1));
		$this->addFrontSystem(new EnergyPulsar(3, 6, 3, 240, 60));
		$this->addFrontSystem(new EnergyPulsar(3, 6, 3, 300, 120));
		$this->addFrontSystem(new CargoBay(2, 25));
		$this->addFrontSystem(new CargoBay(2, 25));
        
		$this->addLeftSystem(new Thruster(3, 14, 0, 8, 3));
		$this->addLeftSystem(new EnergyPulsar(3, 6, 3, 180, 360));
		$this->addLeftSystem(new ScatterPulsar(2, 4, 2, 180, 360));
		$this->addLeftSystem(new NexusChaffLauncher(1, 2, 1, 180, 360));

		$this->addRightSystem(new Thruster(3, 14, 0, 8, 4));
		$this->addRightSystem(new EnergyPulsar(3, 6, 3, 0, 180));
		$this->addRightSystem(new ScatterPulsar(2, 4, 2, 0, 180));
		$this->addRightSystem(new NexusChaffLauncher(1, 2, 1, 0, 180));
        
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
                    6 => "Thruster",
					10 => "Cargo Bay",
					12 => "Energy Pulsar",
					18 => "Structure",
                    20 => "Primary",
            ),
            3=> array(
                    7 => "Thruster",
					8 => "Chaff Launcher",
                    9 => "Scatter Pulsar",
					11 => "Energy Pulsar",
                    18 => "Structure",
                    20 => "Primary",
			),
            4=> array(
                    7 => "Thruster",
					8 => "Chaff Launcher",
                    9 => "Scatter Pulsar",
					11 => "Energy Pulsar",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );

		
    }
}
?>